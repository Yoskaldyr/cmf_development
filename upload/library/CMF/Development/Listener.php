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
					'XenForo_Model_EmailTemplate' => 'CMF_Development_Model_EmailTemplate',
					'XenForo_Model_Template' => 'CMF_Development_Model_Template',
					'XenForo_Model_Phrase' => 'CMF_Development_Model_Phrase',
					//datawriter'
					'XenForo_DataWriter_AdminTemplate' => 'CMF_Development_DataWriter_AdminTemplate',
					'XenForo_DataWriter_EmailTemplate' => 'CMF_Development_DataWriter_EmailTemplate',
					'XenForo_DataWriter_Template' => 'CMF_Development_DataWriter_Template',
					'XenForo_DataWriter_Phrase' => 'CMF_Development_DataWriter_Phrase',
				)
			);

			$devel = $loader->getConfig();
		}
		XenForo_Application::set('cmfDevelopment', $devel);
	}
}