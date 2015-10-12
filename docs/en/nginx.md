Example nginx locations config
==============================

All .repos stored in folder `/.repos/`

Location config for add-on's static files (js/css/images):

~~~
location ~ ^/.repos/ {
	internal;
}

location ~ ^/(js|styles)/([a-z]+)/([a-z]+)/([a-z]+)/(.+)$ {
	try_files 
		/.repos/$2_$3_$4/upload/$1/$2/$3/$4/$5 
		/.repos/$2_$3_$4/_Extras/$1/$2/$3/$4/$5 
		/.repos/$2_$3/upload/$1/$2/$3/$4/$5 
		/.repos/$2_$3/_Extras/$1/$2/$3/$4/$5 
		/.repos/$2/upload/$1/$2/$3/$4/$5 
		/.repos/$2/_Extras/$1/$2/$3/$4/$5 
		$uri =404;
}
location ~ ^/(js|styles)/([a-z]+)/([a-z]+)/(.+)$ {
	try_files 
		/.repos/$2_$3/upload/$1/$2/$3/$4 
		/.repos/$2_$3/_Extras/$1/$2/$3/$4 
		/.repos/$2/upload/$1/$2/$3/$4 
		/.repos/$2/_Extras/$1/$2/$3/$4 
		$uri =404;
}
location ~ ^/(js|styles)/([a-z]+)/(.+)$ {
	try_files 
		/.repos/$2/upload/$1/$2/$3 
		/.repos/$2/_Extras/$1/$2/$3 
		$uri =404;
}
~~~
