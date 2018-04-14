<?php

class Template
{
	private $filePath;
	private $param;
	private $loader;
	private $twig;

	//constructor
	public function __construct($filePath = null, $param = null)
	{
		$this->filePath = $filePath;
		$this->param = $param;
		$this->loadTwig();
	}
	
	//getter
	public function getAttr($name) 
	{
		if (property_exists( __CLASS__, $name)) { 
		  return $this->$name;
		} 
		$emess = __CLASS__ . ": unknown member $name (getAttr)";
		throw new Exception($emess, 45);
	}
	
	//setter
	public function setAttr($name, $value)
	{
		if (property_exists( __CLASS__, $name)) {
		  $this->$name = $value; 
		  return $this->$name;
		} 
		$emess = __CLASS__ . ": unknown member $name (setAttr)";
		throw new Exception($emess, 45);
	}

	public function loadTwig()
	{
		require_once "/vendor/autoload.php";

		$this->loader = new Twig_Loader_Filesystem(__DIR__, "app/ressources/templates/");
		$this->twig = new Twig_Environment($this->loader, [
			"cache" => false,
			"debug" => true
		]);
		$this->twig->addExtension(new Twig_Extensions_Extension_Text());
		$this->twig->addExtension(new Twig_Extension_Debug());
	}

	public function render()
	{
		if(!file_exists("C:\\Users\\Plank\\Documents\\OpenClassrooms\\Projet_4\\app\\ressources\\templates\\".$this->filePath)
			&& !file_exists("C:\\Users\\Plank\\Documents\\OpenClassrooms\\Projet_4\\app\\ressources\\templates\\admin\\".$this->filePath)) {
			$this->filePath = "error-404.html";
			$this->param = array(
				"pageName" => "error_404",
				"pageTitle" => "404 - Page not found"
			);
			header("HTTP/1.0 404 Not Found");
		}
		echo $this->twig->render($this->filePath, $this->param);
	}

}