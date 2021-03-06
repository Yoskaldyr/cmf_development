Автозагрузчик WHM_Development_Autoloader
========================================
Общие положения
---------------
Ядро для работы и реализации функционала использует собственный автозагрузчик классов и собственный аналог реестра очень похож на реестр XenForo_Application, но со своими особенностями.

Для включения полного функционала ядра, кроме установки дополнения через админку, надо включить подмену стандартного автозагрузчика XenForo.

Самый ранний вариант без правки оригинальных файлов - это добавление инициализации автозагрузчика в `config.php`, которое никак не повлияет на обновление форума или на установку каких либо сторонних хаков и инициализируется приложением достаточно рано чтобы была возможность перехватить практически любой класс после его загрузки.

Режим разработки. Отдельная папка для каждого аддона.
-----------------------------------------------------
При разработке удобно когда каждый аддон лежит в полностью своей папке, что практически не осуществимо в текущей структуре папок XenForo (php, js и стили - все в разных папках).
Для этого у автолоадера есть режим поиска файлов в папке по альтернативному пути, причем внутри файлы хака могут располагаться исходя из нескольких вариантов соглашений (связано с тем что разработчики как только не называют свои классы при создании расширения).

Настройка автозагрузчика делается через метод `configure`.

#### [пример config-а с комментариями.](config.php.md)
------

Если автозагрузчик не находит класс по альтернативному пути, то он ищет его по первоначальному пути, т.е. в `/library/`.

### Соглашения по структуре и именованию папок дополнений
Разработчики при наименовании своих дополнений обычно используют 2 варианта наименования классов короткий и длинный:

1. Короткий - `AddOnName_SubClass` (соответсвенно хранится в `/library/AddOnName/SubClass.php`)
2. Длинный - `Author_AddOnName_SubClass` (соответсвенно хранится в `/library/Author/AddOnName/SubClass.php`)

Соотвественно id дополнения на основе названия классов можно считать `addonname` и `author_addonname` (для удобства http редиректов будут в нижнем регистре).
После чего простая последовательная проверка на наличие папок `author_addonname` и `addonname` позволяет точно сказать по какому соглашению проименованы классы в аддоне.

Учитывая уже сложившуюся структуру SVN репозиториев для CMF, когда файлы классов лежат в 1 папке `/library/CMF/AddonName/` (т.е. длинное наименование), а все дополнительные файлы (js/xml/style) лежат в `/library/CMF/AddonName/_Extras/`, то автозагрузчик обрабатывает и этот вариант хранения готового дополнения.

Т.е. дополнения с длинным наименованиями можно хранить так:

+ **Расположение по умолчанию:**
	+ xml файлов с аддоном и языками может вообще не быть в папках

~~~
/library/CMF/SomeAddon/Model/Forum.php
/library/CMF/SomeAddon/Model/Thread.php
/library/CMF/SomeAddon/Listener.php
/js/cmf/someaddon/thread.js
/styles/cmf/someaddon/image.jpg
~~~

+  **CMF-соглашение:**
	+ папка дополнения первые 2 части класса через подчеркивание в нижнем регистре
	+ остальная часть пути класса как в library
	+ все остальное лежит в _Extras
	+ xml лежит в _Extras

~~~
/addons/cmf_someaddon/Model/Forum.php
/addons/cmf_someaddon/Model/Thread.php
/addons/cmf_someaddon/Listener.php
/addons/cmf_someaddon/_Extras/js/cmf/someaddon/thread.js
/addons/cmf_someaddon/_Extras/styles/cmf/someaddon/image.jpg
/addons/cmf_someaddon/_Extras/xml/language.xml
~~~

+  **FullPath-соглашение:**
	+ папка дополнения первые 2 части класса через подчеркивание в нижнем регистре
	+ все кроме xml лежит в upload по полному пути
	+

~~~
/addons/cmf_someaddon/upload/library/CMF/SomeAddon/Model/Forum.php
/addons/cmf_someaddon/upload/library/CMF/SomeAddon/Model/Thread.php
/addons/cmf_someaddon/upload/library/CMF/SomeAddon/Listener.php
/addons/cmf_someaddon/upload/js/cmf/someaddon/thread.js
/addons/cmf_someaddon/upload/styles/cmf/someaddon/image.jpg
/addons/cmf_someaddon/xml/language.xml
~~~

Для дополнений с **коротким** стилем наименования используется только **FullPath-соглашение** только в качестве имени папки используется первая часть класса.

Во всех соглашениях за счет нижнего регистра названия аддона и присутствия частей названия аддона в путях к статическим файлам, легко сделать редирект с `/(js|styles)/` на соответствующую папку аддона.

### Привязка классов к определенному дополнению
Если дополнение использует сторонние классы, с другим префиксом/неймспейсом (типичный пример дополнение `TMS` использует сторонние классы `Diff_*`), то может понадобиться принудительно указать в какой папке искать класс с заданным префиксом.