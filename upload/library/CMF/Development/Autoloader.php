<?php

/**
 * Class CMF_Development_Autoloader
 * CMF Devel Autoloader class
 *
 * @package CMF_Development
 * @author Yoskaldyr <yoskaldyr@gmail.com>
 *
 * @static CMF_Development_Autoloader getProxy()
 */
class CMF_Development_Autoloader extends CMF_Core_Autoloader
{
	protected static $_config = array(
		'addon' => array(
			'dir' => '',
		    'map' => array()
		),
		//path to custom styles
		'style' => array(
			'dir' => '',
			'map' => array()
		),
		//path to custom languages
		'language' => array(
			'dir' => '',
			'map' => array()
		),
		//empty for default 'file_output'
	    'fileOutput' => '',
	    //create mirror files when importing or upgrading
	    'createOnImport' => false,
	    //auto create mirror directories
	    'autoCreateDirs' => false,
	);

	public function getConfig()
	{
		return self::$_config;
	}

	/**
	 * Configure development paths
	 *
	 * @param array $config Configuration array
	 *                      Example:
	 *                      array(
	 *                          //addon path configuration
	 *                          'addon' => array(
	 *                              'dir' => 'addon_repos',
	 *                              'map' => array(
	 *                                  'Diff' => 'tms',
	 *                                  'SomeAddon Id' => 'some_addon'
	 *                              )
	 *                          ),
	 *                          //custom styles configuration
	 *                          'style' => array(
	 *                              'dir' => 'style_repos',
	 *                              'map' => array(
	 *                                  1 => 'my_custom_style' //style integer_id => path
	 *                                  '1-SomeAddonId' => 'addonId_my_custom_style' // style_id-addon_id => path
	 *                              )
	 *                          ),
	 *                          //custom languages configuraton
	 *                          'language' => array(
	 *                              'dir' => '',
	 *                              'map' => array(
	 *                                  2 => 'custom_language_Russian' //language integer_id => path
	 *                                  '2-SomeAddonId' => 'addonId_language_Russian' // language_id-addon_id => path
	 *                              )
	 *                          ),
	 *                          //configure override default XenForo 'file_output'
	 *                          'fileOutput' => 'development', //set empty string to use XenForo default
	 *                          //create mirror files when importing or upgrading. Default false
	 *                          'createOnImport' => true,
	 *                          //auto create mirror directories. Default false
	 *                          'autoCreateDirs' => true
	 *                      )
	 *
	 * @return $this
	 */
	public function configure($config)
	{
		if ($config && is_array($config))
		{
			foreach (self::$_config as $type => $typeConfig)
			{
				if (isset($config[$type]))
				{
					if (is_array($typeConfig))
					{
						if (isset($config[$type]['dir']))
						{
							$dir = $config[$type]['dir'];
							self::$_config[$type]['dir'] = ($dir && ($dir = rtrim(trim((string)$dir), '/')) && @is_readable($dir) && @is_dir($dir)) ? $dir : '';
							if (self::$_config[$type]['dir'] && isset($config[$type]['map']) && $config[$type]['map'] && is_array($config[$type]['map']))
							{
								self::$_config[$type]['map'] = $config[$type]['map'] + $typeConfig['map'];
							}
						}
					}
					else if (is_scalar($typeConfig) && is_scalar($config[$type]))
					{
						self::$_config[$type] = $config[$type];
					}
				}
			}
		}
		return $this;
	}

	/**
	 * Manually reset the new autoloader instance. Use this to inject a modified version.
	 *
	 * @param XenForo_Autoloader|null|string
	 * @return $this|CMF_Core_Autoloader
	 */
	public static function getProxy($newInstance = null)
	{
		if (!$newInstance)
		{
			$newInstance = 'CMF_Development_Autoloader';
		}
		return parent::getProxy($newInstance);
	}

	/**
	 * Resolves a class name to an autoload path.
	 *
	 * @param string $class Name of class to autoload
	 *
	 * @return string|boolean False if the class contains invalid characters.
	 */
	public function autoloaderClassToFile($class)
	{
		if (preg_match('#[^a-zA-Z0-9_\\\\]#', $class))
		{
			return false;
		}

		if (self::$_config['addon']['dir'])
		{
			
			$chunks = explode('_', $class);
			$count = sizeof($chunks);
			if ($count > 1)
			{
				$classPrefixes = array();
				for ($i = min(3, $count - 1); $i > 0; $i--)
				{
					$classPrefixes[] = implode('_', array_slice($chunks, 0, $i));
				}
				$dir = '';
				//Checks for addon map
				foreach ($classPrefixes as $classPrefix)
				{
					if (!empty(self::$_config['addon']['map'][$classPrefix]))
					{
						$dirLong = self::$_config['addon']['dir'] . '/' . self::$_config['addon']['map'][$classPrefix];
						if (file_exists($dirLong) && is_dir($dirLong))
						{
							$dir = $dirLong;
							break;
						}
					}
				}
				if (!$dir)
				{
					//Checks for all addon prefixes: providerName_addonGroup_addonName, providerName_addonName, addonName
					foreach ($classPrefixes as $classPrefix)
					{
						$dirLong = self::$_config['addon']['dir'] . '/' . strtolower($classPrefix);
						if (file_exists($dirLong) && is_dir($dirLong))
						{
							$dir = $dirLong;
							break;
						}
					}
				}
				if ($dir)
				{
					// Long convention with full path (upload/library)
					$file = $dir . '/upload/library/' . implode('/', $chunks) . '.php';
					if (file_exists($file))
					{
						return $file;
					}

					// Short convention with _Extra dir
					$file = $dir . '/' . implode('/', array_slice($chunks, 2)) . '.php';
					if (file_exists($file))
					{
						return $file;
					}
				}
			}
		}

		return $this->_rootDir . '/' . str_replace(array('_', '\\'), '/', $class) . '.php';
		//return parent::autoloaderClassToFile($class);
	}
}