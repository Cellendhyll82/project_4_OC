<?php

abstract class Controller{

	function showPage($param)
	{	
		$page = "";
		if(isset($param["p"])) {
			$page = $param["p"];
		}

		if(substr($page, 0, 6) == "admin_") {
			$filePath = "\\admin\\".$page.".html";
		} else {
			$filePath = $page == "" ? "main-page-content.html" : $page.".html";
		}

		$template = new Template();
		$template->setAttr("filePath", $filePath);
		$template->setAttr("param", $this->getData($page));
		$template->render();
	}

}