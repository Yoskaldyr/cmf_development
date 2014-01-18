<?php

/**
 * Data writer for public templates.
 *
 * @package CMF_Development
 * @author  Yoskaldyr <yoskaldyr@gmail.com>
 */
class CMF_Development_DataWriter_Template extends XFCP_CMF_Development_DataWriter_Template
{

	/**
	 * Helper to get the developer data output directory only if it is enabled
	 * and applicable to this situation.
	 *
	 * @return string
	 */
	protected function _getDevOutputDir()
	{
		$devel = XenForo_Application::get('cmfDevelopment');
		$addOnId = $this->get('addon_id');
		$styleId = $this->get('style_id');
		if (!$devel || !$addOnId)
		{
			return parent::_getDevOutputDir();
		}
		else if ($styleId == 0) //master style
		{
			if ($addOnId == 'XenForo')
			{
				return $devel['createOnImport'] ? $this->_getTemplateModel()->getTemplateDevelopmentDirectory() : parent::_getDevOutputDir();
			}
			else
			{
				return ($templatePath = CMF_Development_Helper_File::getSubPathByAddOnId($addOnId, 'templates')) ? $templatePath : '';
			}
		}

	}

}