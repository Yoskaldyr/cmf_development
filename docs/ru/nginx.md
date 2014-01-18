Пример настройки locations для nginx
====================================

Пусть все аддоны нахолдятся в папке `/.repos/`

Тогда для поддержки всех вариантов соглашений в конфиг надо добавить:

~~~
location ~ ^/.repos/ {
	internal;
}

location ~ ^/(js|styles)/([a-z_]+)/([^/]+)$ {
	try_files /.repos/$2/upload/$1/$2/$3 /.repos/$2/_Extras/$1/$2/$3 $uri =404;
}
location ~ ^/(js|styles)/([a-z_]+)/([a-z_]+)/([^/]+)$ {
	try_files /.repos/$2_$3/upload/$1/$2/$3/$4 /.repos/$2_$3/_Extras/$1/$2/$3/$4 $uri =404;
}
~~~
