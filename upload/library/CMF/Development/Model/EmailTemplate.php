<?php

/**
 * Model for email templates.
 *
 * @package CMF_Development
 * @author  Yoskaldyr <yoskaldyr@gmail.com>
 */
class CMF_Development_Model_EmailTemplate extends XFCP_CMF_Development_Model_EmailTemplate
{
	/**
	 * Returns the path to the admin template development directory, if it has been configured and exists
	 *
	 * @return string Path to admin template directory
	 */
	public function getEmailTemplateDevelopmentDirectory()
	{
		$path = parent::getEmailTemplateDevelopmentDirectory();
		if ($path && ($devel = XenForo_Application::get('cmfDevelopment')) && $devel['fileOutput'])
		{
			$path = preg_replace('#/file_output/email_templates$#', '/' . $devel['fileOutput'] . '/email_templates', $path);
		}
		return $path;
	}
}