<?php

/**
 * Model for phrases.
 *
 * @package CMF_Development
 * @author  Yoskaldyr <yoskaldyr@gmail.com>
 */
class CMF_Development_Model_Phrase extends XFCP_CMF_Development_Model_Phrase
{
	/**
	 * Returns the path to the admin template development directory, if it has been configured and exists
	 *
	 * @return string Path to admin template directory
	 */
	public function getPhraseDevelopmentDirectory()
	{
		$path = parent::getPhraseDevelopmentDirectory();
		if ($path && ($devel = XenForo_Application::get('cmfDevelopment')) && $devel['fileOutput'])
		{
			$path = preg_replace('#/file_output/phrases$#', '/' . $devel['fileOutput'] . '/phrases', $path);
		}
		return $path;
	}
}