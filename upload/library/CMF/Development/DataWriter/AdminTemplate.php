<?php

/**
 * Data writer for admin templates.
 *
 * @package CMF_Development
 * @author  Yoskaldyr <yoskaldyr@gmail.com>
 */
class CMF_Development_DataWriter_AdminTemplate extends XFCP_CMF_Development_DataWriter_AdminTemplate
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
		if (!$devel || !$addOnId)
		{
			return parent::_getDevOutputDir();
		}
		else if ($addOnId == 'XenForo')
		{
			return $devel['createOnImport'] ? $this->_getAdminTemplateModel()->getAdminTemplateDevelopmentDirectory() : parent::_getDevOutputDir();
		}

		return ($templatePath = CMF_Development_Helper_File::getSubPathByAddOnId($addOnId, 'admin_templates')) ? $templatePath : '';
	}
}