Development mode
================
All addons (with CMF_Core and CMF_Development add-on) stored in /.repos/ (path configurable).
Auto-export template and phrase files also enabled.
Add to config.php:

~~~php
//Enable undocumented XenForo option for template auto-export
$config['development']['directory'] = '.';

//Enable debug mode
$config['debug']=true;

//Manual include development autoloader
if (!class_exists('CMF_Development_Autoloader', false))
{
	include(realpath(dirname(__FILE__) . '/..') . '/.repos/cmf_core/upload/library/CMF/Core/Autoloader.php');
	include(realpath(dirname(__FILE__) . '/..') . '/.repos/cmf_development/upload/library/CMF/Development/Autoloader.php');
	CMF_Development_Autoloader::getProxy()->configure(array(

		//---------------------------------------
    	// add-on path configuration
    	//---------------------------------------

		'addon' => array(
			'dir' => '.repos',
			'map' => array( // path list for class prefixes and for add-on ids
				'Diff' => 'tms', // Search Diff_* classes in 'tms' directory (TMS add-on)
				'SomeAddon_Id' => 'some_addon' // For add-on with id 'SomeAddon_Id' search auto-export directories in 'some_addon'
			)
		),

		//---------------------------------------
    	// auto-export configuration
    	//---------------------------------------

	    //custom styles configuration
	    'style' => array(
	        'dir' => '.repos',
	        'map' => array(
	            1 => 'my_custom_style' //style integer_id => path
	            '1-SomeAddonId' => 'addonId_my_custom_style' // style_id-addon_id => path
	        )
	    ),

	    //custom languages configuraton
	    'language' => array(
	        'dir' => '.repos',
	        'map' => array(
	            2 => 'custom_language_Russian' //language integer_id => path
	            '2-SomeAddonId' => 'addonId_language_Russian' // language_id-addon_id => path
	        )
	    ),

	    //override default XenForo 'file_output'
	    'fileOutput' => 'development', //set empty string to use XenForo default

	    //create mirror files when importing or upgrading. Default false
	    'createOnImport' => true,

	    //auto create mirror directories. Default false
	    'autoCreateDirs' => true
	));

}
~~~
