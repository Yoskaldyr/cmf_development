<?php

class CMF_Development_Listener
{
	/**
	 * init_listeners event
	 */
	public static function initListeners(CMF_Core_Listener $events)
	{
		/** @var CMF_Development_Autoloader $loader */
		$loader = XenForo_Autoloader::getInstance();
		$config = XenForo_Application::get('config');
		$devel = array();
		if ($config->debug && ($loader instanceof CMF_Development_Autoloader))
		{
			$events->addExtenders(
				array(
					//model
					'XenForo_Model_AdminTemplate' => 'CMF_Development_Model_AdminTemplate',
					'XenForo_Model_Template' => 'CMF_Development_Model_Template',
					//'XenForo_Model_Thread' => 'CMF_Thread_Model_Thread',
					//'XenForo_Model_Post' => 'CMF_Thread_Model_Post',
					//route prefix
					//'XenForo_Route_Prefix_Threads' => 'CMF_Thread_Route_Prefix_Threads',
					//view
					//'XenForo_ViewPublic_Thread_View' => 'CMF_Thread_ViewPublic_Thread_View',
					//'XenForo_ViewPublic_Thread_Reply' => 'CMF_Thread_ViewPublic_Thread_Reply',
					//datawriter'
					'XenForo_DataWriter_AdminTemplate' => 'CMF_Development_DataWriter_AdminTemplate',
					//controller
					//'XenForo_ControllerPublic_Thread' => 'CMF_Thread_ControllerPublic_Thread',
					//'XenForo_ControllerPublic_Post' => 'CMF_Thread_ControllerPublic_Post'
				)
			);

			$devel = $loader->getConfig();
		}
		XenForo_Application::set('cmfDevelopment', $devel);
	}
}