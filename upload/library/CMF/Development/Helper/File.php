<?php

/**
 * Helper for file-related functions.
 *
 * @package CMF_Development
 * @author Yoskaldyr <yoskaldyr@gmail.com>
 */
abstract class CMF_Development_Helper_File
{
	private static $_addOnPathCache = array();

	/**
	 * Gets a path to addon by addon_id
	 *
	 * @param string $addOnId
	 *
	 * @return string
	 */
	public static function getAddonPathByAddOnId($addOnId)
	{
		$devel = XenForo_Application::isRegistered('cmfDevelopment') ? XenForo_Application::get('cmfDevelopment') : false;
		$addOnId = trim((string) $addOnId);
		if ($addOnId && $devel && $devel['addon']['dir'])
		{
			if (isset(self::$_addOnPathCache[$addOnId]))
			{
				return self::$_addOnPathCache[$addOnId];
			}
			$path = $devel['addon']['dir'] . '/' . (isset($devel['addon']['map'][$addOnId]) ? $devel['addon']['map'][$addOnId] : utf8_strtolower(preg_replace(array('/\s+/iu', '/[^a-z0-9_-]/iu'), array('_', ''), $addOnId)));
			if (@is_readable($path) && @is_dir($path))
			{
				self::$_addOnPathCache[$addOnId] = $path;
				return $path;
			}

		}
		return '';
	}

	/**
	 * Gets a path to addon by addon_id
	 *
	 * @param string $addOnId  Add-On id
	 * @param string $subPath  part of path (templates, admin_templates etc.)
	 * @return string
	 */
	public static function getSubPathByAddOnId($addOnId, $subPath)
	{
		$subPath = trim((string)$subPath);
		if ($subPath && ($addOnPath = self::getAddonPathByAddOnId($addOnId)))
		{
			$devel = XenForo_Application::get('cmfDevelopment');
			$suffix = $devel['fileOutput'] ? $devel['fileOutput'] : 'file_output';
			$path = $addOnPath . '/' . $suffix . '/' . $subPath;
			if (@is_writable($path) && @is_dir($path))
			{
				return $path;
			}
			else if ($devel['autoCreateDirs'])
			{
				return (XenForo_Helper_File::createDirectory($path) && XenForo_Helper_File::makeWritableByFtpUser($path)) ? $path : '';
			}
		}
		return '';
	}
}