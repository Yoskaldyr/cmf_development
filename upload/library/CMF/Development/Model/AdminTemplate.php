<?php

/**
 * Model for admin templates.
 *
 * @package CMF_Development
 * @author  Yoskaldyr <yoskaldyr@gmail.com>
 */
class CMF_Development_Model_AdminTemplate extends XFCP_CMF_Development_Model_AdminTemplate
{
	/**
	 * Returns the path to the admin template development directory, if it has been configured and exists
	 *
	 * @return string Path to admin template directory
	 */
	public function getAdminTemplateDevelopmentDirectory()
	{
		$path = parent::getAdminTemplateDevelopmentDirectory();
		if ($path && ($devel = XenForo_Application::get('cmfDevelopment')) && $devel['fileOutput'])
		{
			$path = preg_replace('#/file_output/admin_templates$#', '/' . $devel['fileOutput'] . '/admin_templates', $path);
		}
		return $path;
	}

	/**
	 * Checks that the admin templates directory has been configured and exists
	 *
	 * @return boolean
	 */
	public function canImportAdminTemplatesFromDevelopment()
	{
		$dir = $this->getAdminTemplateDevelopmentDirectory();
		return ($dir && is_dir($dir));
	}

	/**
	 * Deletes the admin templates that belong to the specified add-on.
	 *
	 * @param string $addOnId
	 */
	public function deleteAdminTemplatesForAddOn($addOnId)
	{
		$templateTitles = $this->getAdminTemplateTitlesByAddOn($addOnId);
		$templateIds = array_keys($templateTitles);

		if ($templateTitles)
		{
			$db = $this->_getDb();
			$quotedIds = $db->quote($templateIds);

			$db->delete('xf_admin_template', "template_id IN ($quotedIds)");
			$db->delete('xf_admin_template_compiled', 'title IN (' . $db->quote($templateTitles) . ')');
			$db->delete('xf_admin_template_include', "source_id IN ($quotedIds)");
			$db->delete('xf_admin_template_phrase', "template_id IN ($quotedIds)");
			$db->delete('xf_admin_template_modification_log', "template_id IN ($quotedIds)");
		}

		XenForo_Template_Compiler_Admin::resetTemplateCache();
	}

	/**
	 * Imports all admin templates from the admin templates directory into the database
	 */
	public function importAdminTemplatesFromDevelopment()
	{
		$db = $this->_getDb();

		$templateDir = $this->getAdminTemplateDevelopmentDirectory();
		if (!$templateDir && !is_dir($templateDir))
		{
			throw new XenForo_Exception("Admin template development directory not enabled or doesn't exist");
		}

		$files = glob("$templateDir/*.html");
		if (!$files)
		{
			throw new XenForo_Exception("Admin template development directory does not have any templates");
		}

		XenForo_Db::beginTransaction($db);
		$this->deleteAdminTemplatesForAddOn('XenForo');

		$titles = array();
		foreach ($files AS $templateFile)
		{
			$filename = basename($templateFile);
			if (preg_match('/^(.+)\.html$/', $filename, $match))
			{
				$titles[] = $match[1];
			}
		}

		$existingTemplates = $this->getAdminTemplatesByTitles($titles);

		foreach ($files AS $templateFile)
		{
			if (!is_readable($templateFile))
			{
				throw new XenForo_Exception("Template file '$templateFile' not readable");
			}

			$filename = basename($templateFile);
			if (preg_match('/^(.+)\.html$/', $filename, $match))
			{
				$templateName = $match[1];
				$data = file_get_contents($templateFile);

				$dw = XenForo_DataWriter::create('XenForo_DataWriter_AdminTemplate');
				if (isset($existingTemplates[$templateName]))
				{
					$dw->setExistingData($existingTemplates[$templateName], true);
				}
				$dw->setOption(XenForo_DataWriter_AdminTemplate::OPTION_DEV_OUTPUT_DIR, '');
				$dw->setOption(XenForo_DataWriter_AdminTemplate::OPTION_FULL_COMPILE, false);
				$dw->setOption(XenForo_DataWriter_AdminTemplate::OPTION_TEST_COMPILE, false);
				$dw->set('title', $templateName);
				$dw->set('template', $data);
				$dw->set('addon_id', 'XenForo');

				try
				{
					$dw->save();
				}
				catch (Exception $e)
				{
					throw new XenForo_Exception("Template '$templateName' not imported." . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
				}
			}
		}

		$this->compileAllParsedAdminTemplates();

		XenForo_Db::commit($db);
	}

	/**
	 * Imports the add-on admin templates XML.
	 *
	 * @param SimpleXMLElement $xml XML element pointing to the root of the data
	 * @param string $addOnId Add-on to import for
	 * @param integer $maxExecution Maximum run time in seconds
	 * @param integer $offset Number of elements to skip
	 *
	 * @return boolean|integer True on completion; false if the XML isn't correct; integer otherwise with new offset value
	 */
	public function importAdminTemplatesAddOnXml(SimpleXMLElement $xml, $addOnId, $maxExecution = 0, $offset = 0)
	{
		$db = $this->_getDb();

		XenForo_Db::beginTransaction($db);

		$startTime = microtime(true);

		if ($offset == 0)
		{
			$this->deleteAdminTemplatesForAddOn($addOnId);
		}

		$templates = XenForo_Helper_DevelopmentXml::fixPhpBug50670($xml->template);

		$titles = array();
		$current = 0;
		foreach ($templates AS $template)
		{
			$current++;
			if ($current <= $offset)
			{
				continue;
			}
			$titles[] = (string)$template['title'];
		}

		$existingTemplates = $this->getAdminTemplatesByTitles($titles);

		$current = 0;
		$restartOffset = false;
		foreach ($templates AS $template)
		{
			$current++;
			if ($current <= $offset)
			{
				continue;
			}

			$templateName = (string)$template['title'];

			$dw = XenForo_DataWriter::create('XenForo_DataWriter_AdminTemplate');
			if (isset($existingTemplates[$templateName]))
			{
				$dw->setExistingData($existingTemplates[$templateName], true);
			}
			$dw->setOption(XenForo_DataWriter_AdminTemplate::OPTION_DEV_OUTPUT_DIR, '');
			$dw->setOption(XenForo_DataWriter_AdminTemplate::OPTION_FULL_COMPILE, false);
			$dw->setOption(XenForo_DataWriter_AdminTemplate::OPTION_TEST_COMPILE, false);
			$dw->set('title', $templateName);
			$dw->set('template', XenForo_Helper_DevelopmentXml::processSimpleXmlCdata($template));
			$dw->set('addon_id', $addOnId);
			$dw->save();

			if ($maxExecution && (microtime(true) - $startTime) > $maxExecution)
			{
				$restartOffset = $current;
				break;
			}
		}

		XenForo_Db::commit($db);

		return ($restartOffset ? $restartOffset : true);
	}

	/**
	 * Gets the admin templates development XML.
	 *
	 * @return DOMDocument
	 */
	public function getAdminTemplatesDevelopmentXml()
	{
		$document = new DOMDocument('1.0', 'utf-8');
		$document->formatOutput = true;
		$rootNode = $document->createElement('admin_templates');
		$document->appendChild($rootNode);

		$this->appendAdminTemplatesAddOnXml($rootNode, 'XenForo');

		return $document;
	}
}