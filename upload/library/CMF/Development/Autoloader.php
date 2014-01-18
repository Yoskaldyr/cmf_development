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
	protected $_config = array(
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
		return $this->_config;
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
	 *                              )
	 *                          ),
	 *                          //custom languages configuraton
	 *                          'language' => array(
	 *                              'dir' => '',
	 *                              'map' => array(
	 *                                  2 => 'custom_language_Russian' //language integer_id => path
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
			foreach ($this->_config as $type => $typeConfig)
			{
				if (isset($config[$type]))
				{
					if (is_array($typeConfig))
					{
						if (isset($config[$type]['dir']))
						{
							$dir = $config[$type]['dir'];
							$this->_config[$type]['dir'] = ($dir && ($dir = rtrim(trim((string)$dir), '/')) && @is_readable($dir) && @is_dir($dir)) ? $dir : '';
							if ($typeConfig['dir'] && isset($config[$type]['map']) && $config[$type]['map'] && is_array($config[$type]['map']))
							{
								$this->_config[$type]['map'] = $config[$type]['map'] + $typeConfig['map'];
							}
						}
					}
					else if (is_scalar($typeConfig) && is_scalar($config[$type]))
					{
						$this->_config[$type] = $config[$type];
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

		if ($this->_config['addon']['dir'])
		{
			
			$chunks = explode('_', $class);
			if (sizeof($chunks) > 1)
			{
				//shot addon prefix (only addonName)
				$classPrefix = $chunks[0];
				$dir = $this->_config['addon']['dir'] . '/' . (!empty($this->_config['addon']['map'][$classPrefix]) ? $this->_config['addon']['map'][$classPrefix] : strtolower($classPrefix));
				if (sizeof($chunks)>2)
				{
					//long addon prefix (providerName_addonName)
					$classPrefix = $chunks[0] . '_' . $chunks[1];
					$dirLong = $this->_config['addon']['dir'] . '/' . (!empty($this->_config['addon']['map'][$classPrefix]) ? $this->_config['addon']['map'][$classPrefix] : strtolower($classPrefix));
					if (file_exists($dirLong))
					{
						$dir = $dirLong;
					}
				}
				// Short convention with _Extra dir
				$fileShort = $dir . '/' . implode('/', array_slice($chunks, 2)) . '.php';
				// Long convention with full path (upload/library)
				$fileLong = $dir . '/upload/library/' . implode('/', $chunks) . '.php';

				if (file_exists($fileLong))
				{
					return $fileLong;
				}
				else if (file_exists($fileShort))
				{
					return $fileShort;
				}
			}
		}

		return $this->_rootDir . '/' . str_replace(array('_', '\\'), '/', $class) . '.php';
		//return parent::autoloaderClassToFile($class);
	}
}