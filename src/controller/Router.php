<?php

class Router {
	
	function analyse($param)
	{
		session_start();

		$redirected = false;

		if(isset($param['c']) && class_exists($param['c']."Controller")) {
			if(isset($_SESSION["accessRights"]) && $_SESSION["accessRights"] == "82") {
				$className = $param['c']."Controller";
				$controller = new $className();
			} else {
				$controller = new AdminPortalController();
			}
		} else {
			$controller = new WebsiteController();
		}
		
		$action = (isset($param['action']) ? $action = $param['action'] : $action = "showPage");

		if(method_exists($controller, $action)) {
			$controller->$action($param);	
		}
		else {
			header("Location:?p=error-404");
		}
		
	}

}