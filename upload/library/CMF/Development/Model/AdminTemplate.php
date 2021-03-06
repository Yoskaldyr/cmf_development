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
}