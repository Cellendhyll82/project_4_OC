<?php

class Autoloader {
	
	static function register()
	{
		spl_autoload_register(array(__CLASS__, 'autoloadClass'));
		spl_autoload_register(array(__CLASS__, 'autoloadController'));
	}

	static function autoloadClass($className)
	{
		if(file_exists('src/model/'.$className.'.class.php')) {
			require 'src/model/'.$className.'.class.php';
		} else if(file_exists('src/model/'.$className.'.php')) {
			require 'src/model/'.$className.'.php';
		} else if(file_exists('app/ressources/templates/'.$className.'.class.php')) {
			require 'app/ressources/templates/'.$className.'.class.php';
		}
	}

	static function autoloadController($controllerName)
	{
		if(file_exists('src/controller/'.$controllerName.'.php')) {
			require 'src/controller/'.$controllerName.'.php';
		}
	}
}