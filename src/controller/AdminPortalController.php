<?php

class AdminPortalController extends Controller {

	function login() 
	{
		$template = new Template();
		$template->setAttr("filePath", "admin/admin_portal.html");

		$username = "admin";
		$hashPassword = '$2a$10$Jm8GO.JmsO/kY/c0B4ZDsOWVSwEyLphcEF5YsKTLK6lBrnMvj1sfa';

		if(!isset($_POST["username"]) && !isset($_POST["password"])) {
			$template->setAttr("param", []);
			$template->render();
		}
		else if(!isset($_POST["username"])) {
			$template->setAttr("param", array("errorUsername" => "", "password" => isset($_POST["password"]) ? $_POST["password"] : ""));
			$template->render();
		}
		else if(!isset($_POST["password"])) {
			$template->setAttr("param", array("errorPassword" => "", "username" => isset($_POST["username"]) ? $_POST["username"] : ""));
			$template->render();
		}
		else if(isset($_POST["username"]) && $_POST["username"] !== $username) {
			$template->setAttr("param", array("errorUsername" => "", "username" => $_POST["username"], "password" => $_POST["password"]));
			$template->render();
		}
		else if (isset($_POST["password"]) && !password_verify($_POST["password"], $hashPassword)) {
			$template->setAttr("param", array("errorPassword" => "", "username" => $_POST["username"], "password" => $_POST["password"]));
			$template->render();
		}
		else {
			$_SESSION["accessRights"] = "82";
			header("Location:?c=Episode&p=admin_episodes");
		}
	}

	function logout()
	{
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("Location:/Projet_4/");
	}
}