<?php

/**
 * Model for public templates.
 *
 * @package CMF_Development
 * @author  Yoskaldyr <yoskaldyr@gmail.com>
 */
class CMF_Development_Model_Template extends XFCP_CMF_Development_Model_Template
{
	/**
	 * Returns the path to the template development directory, if it has been configured and exists
	 *
	 * @return string Path to templates directory
	 */
	public function getTemplateDevelopmentDirectory()
	{
		$path = parent::getTemplateDevelopmentDirectory();
		if ($path && ($devel = XenForo_Application::get('cmfDevelopment')) && $devel['fileOutput'])
		{
			$path = preg_replace('#/file_output/templates$#', '/' . $devel['fileOutput'] . '/templates', $path);
		}
		return $path;
	}
}