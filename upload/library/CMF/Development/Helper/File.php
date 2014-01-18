<?php

/**
 * Helper for file-related functions.
 *
 * @package CMF_Development
 * @author Yoskaldyr <yoskaldyr@gmail.com>
 */
abstract class CMF_Development_Helper_File
{
	private static $_pathCache = array();

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
			if (isset(self::$_pathCache['addon'][$addOnId]))
			{
				return self::$_pathCache['addon'][$addOnId];
			}
			$path = $devel['addon']['dir'] . '/' . (isset($devel['addon']['map'][$addOnId]) ? $devel['addon']['map'][$addOnId] : utf8_strtolower(preg_replace(array('/\s+/iu', '/[^a-z0-9_-]/iu'), array('_', ''), $addOnId)));
			if (@is_readable($path) && @is_dir($path))
			{
				self::$_pathCache['addon'][$addOnId] = $path;
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
			$suffix = $addOnPath . '/' . ($devel['fileOutput'] ? $devel['fileOutput'] : 'file_output');
			$path = $suffix . '/' . $subPath;
			if (@is_writable($path) && @is_dir($path))
			{
				return $path;
			}
			else if ($devel['autoCreateDirs'] && @is_writable($suffix) && @is_dir($suffix))
			{

				return (XenForo_Helper_File::createDirectory($path) && XenForo_Helper_File::makeWritableByFtpUser($path)) ? $path : '';
			}
		}
		return '';
	}

	/**
	 * Gets a path to custom styles
	 *
	 * @param integer $styleId Style id
	 * @return string
	 */
	public static function getStylePathByStyleId($styleId)
	{
		return self::_getTypePathById('style', $styleId);
	}

	/**
	 * Gets a path to custom languages
	 *
	 * @param integer $styleId Style id
	 * @return string
	 */
	public static function getLanguagePathByLanguageId($styleId)
	{
		return self::_getTypePathById('language', $styleId);
	}

	protected static function _getTypePathById($type, $id)
	{
		$devel = XenForo_Application::isRegistered('cmfDevelopment') ? XenForo_Application::get('cmfDevelopment') : false;
		$type = (string) $type;
		if ($id && $type && $devel && isset($devel[$type]['dir']) && $devel[$type]['dir'] && isset($devel[$type]['map'][$id]))
		{
			if (isset(self::$_pathCache[$type][$id]))
			{
				return self::$_pathCache[$type][$id];
			}
			$path = $devel[$type]['dir'] . '/' . $devel[$type]['map'][$id];
			if (@is_readable($path) && @is_dir($path))
			{
				self::$_pathCache[$type][$id] = $path;
				return $path;
			}
		}
		return '';
	}

	/**
	 * Gets a path to custom styles by addon_id
	 *
	 * @param integer $styleId Style id
	 * @param string  $addOnId Add-On id
	 * @return string
	 */
	public static function getAddOnStylePathByStyleId($styleId, $addOnId)
	{
		return self::_getAddOnTypePathById('style', $styleId, $addOnId);
	}

	/**
	 * Gets a path to custom styles by addon_id
	 *
	 * @param integer $languageId Style id
	 * @param string  $addOnId Add-On id
	 * @return string
	 */
	public static function getAddOnLanguagePathByLanguageId($languageId, $addOnId)
	{
		return self::_getAddOnTypePathById('language', $languageId, $addOnId);
	}

	protected static function _getAddOnTypePathById($type, $id, $addOnId)
	{
		$addOnId = preg_replace(array('/\s+/iu', '/[^a-z0-9_-]/iu'), array('_', ''), trim((string)$addOnId));
		if ($addOnId)
		{
			$type = (string)$type;
			$devel = XenForo_Application::get('cmfDevelopment');
			$path = self::_getTypePathById($type, $id . '-' . $addOnId);
			if (!$path && ($path = self::_getTypePathById($type, $id)))
			{
				$path .= '/' . $addOnId;
			}
			if ($path)
			{
				if (@is_writable($path) && @is_dir($path))
				{
					return $path;
				}
				else if ($devel['autoCreateDirs'])
				{
					return (XenForo_Helper_File::createDirectory($path) && XenForo_Helper_File::makeWritableByFtpUser($path)) ? $path : '';
				}
			}
		}
		return '';
	}
}