Режим разработчика
==================
Все дополнения (включая CMF_Core и CMF_Development) находятся в папке /.repos/ (путь настраиваемый).
Автоэкспорт шаблонов и фраз включен.
Добавить в config.php:

~~~php
//Включение недокументированной настройки XenForo для автоэкспорта шаблонов
$config['development']['directory'] = '.';

//Обязательное включение режима отладки
$config['debug']=true;

//Загрузка измененного автолоадера
if (!class_exists('CMF_Development_Autoloader', false))
{
	//необходим ручной include файлов
	include(realpath(dirname(__FILE__) . '/..') . '/.repos/cmf_core/upload/library/CMF/Core/Autoloader.php');
	include(realpath(dirname(__FILE__) . '/..') . '/.repos/cmf_development/upload/library/CMF/Development/Autoloader.php');
	CMF_Development_Autoloader::getProxy()->configure(array(

		//---------------------------------------
		// настройки размещения дополнений
		//---------------------------------------

		'addon' => array(
			'dir' => '.repos',
			'map' => array( //список путей как для префиксов классов, так и для id дополнений
				'Diff' => 'tms', //Искать классы Diff_* в папке tms (дополнение TMS)
				'SomeAddon_Id' => 'some_addon' //Для дополнения с id SomeAddon_Id искать папки для автоэкспорта по пути some_addon
			)
		),

		//---------------------------------------
		// настройки автоэкспорта
		//---------------------------------------

		//пользовательские стили (style_id > 0)
		'style' => array(
			'dir' => '.repos', //путь размещения пользовательских стилей
			'map' => array(
				1 => 'xenforo_default_style' //цифровой_id_стиля => путь
				'1-SomeAddonId' => 'someAddonId_default_style' //цифровой_id_стиля-id_дополнения => путь
			)
		),

		//настройки фраз пользовательских языков (language_id > 0)
		'language' => array(
			'dir' => '.repos', //путь размещения пользовательских языков
			'map' => array(
				2 => 'language_russian', //цифровой_id_языка => путь
			    '2-XenForo' => 'xenforo_language_russian/development' //цифровой_id_языка-id_дополнения => путь
			)
		),

		//путь для размещения всех мастер-данных (фразы и шаблоны)
		'fileOutput' => 'development', //если пусто используется путь file_output (по умолчанию для XenForo)

		//Создавать файлы при импорте/обновлении дополнений и XenForo (по умолчению отключено)
		'createOnImport' => true,

		//автоматически создавать папки
		'autoCreateDirs' => true
	));

}
~~~
