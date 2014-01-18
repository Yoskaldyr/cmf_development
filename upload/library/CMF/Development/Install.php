<?php

class CMF_Development_Install
{
	public static function install($existingAddOn, $addOnData)
	{
		if (XenForo_Application::$versionId < 1020470)
		{
		    throw new XenForo_Exception('This Add-On requires XenForo version 1.2.4 or higher.');
		}
		$addons = XenForo_Application::get('addOns');
		if (!isset($addons['CMF_Core']) || $addons['CMF_Core'] < 1000031)
		{
			throw new XenForo_Exception('This Add-On requires "CMF Core" add-on version 1.0.0 beta 1 or higher.');
		}
	}
}