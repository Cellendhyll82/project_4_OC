<?php

class FooterController extends Controller{

	function save()
	{
		$footer = new Footer();
		$footer->setAttr('title', $_POST['footer-title']);
		$footer->setAttr('content', $_POST['footer-content']);

		$footer->save();

		$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
		$_SESSION['tmpMessage'] = 'Votre footer a bien été enregistré';

		header('Location:?c=Footer&p=admin_footer&tmpMessage=82');
	}

	function getData($page)
	{
		unset($_SESSION['dataInserted']);
		
		$toReturn = [];

		//favicon
		$seo = Seo::getSeo();
		if($seoImage = Image::find(['id' => $seo->getAttr('imageId')])) {
			$toReturn['seoImage'] = $seoImage;
		}
		
		//tmpMessage
		if(isset($_GET['tmpMessage'])) {
			$toReturn['tmpMessage'] = $_SESSION['tmpMessage'];
			$toReturn['tmpClass'] = $_SESSION['tmpClass'];
		}

		return array_merge($toReturn, array(
			'currentPage' => 'footer',
			'footer' => Footer::getFooter()
		));
	}
}