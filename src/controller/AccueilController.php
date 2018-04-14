<?php

class AccueilController extends Controller{

	function save()
	{
		$accueil = new Accueil();
		$accueil->setAttr('title', $_POST['accueil-title']);
		$accueil->setAttr('content', $_POST['accueil-content']);
		$accueil->setAttr('imageId', isset($_POST['accueil-imageId']) ? $_POST['accueil-imageId'] : null);
		$accueil->setAttr('menuLabel', $_POST['accueil-menuLabel']);

		$accueil->save();

		$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
		$_SESSION['tmpMessage'] = 'Votre section "Accueil" a bien été enregistré';

		header('Location:?c=Accueil&p=admin_accueil&tmpMessage=82');
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

		$accueil = Accueil::getAccueil();
		$toReturn['accueil'] = $accueil;
		if($accueilImage = Image::find(['id' => $accueil->getAttr('imageId')])) {
			$toReturn['accueilImage'] = $accueilImage;
		}

		return array_merge($toReturn, array(
			'currentPage' => 'sections',
			'jsFilename' => 'accueil',
			'images' => Image::find(null, ['order_by' => 'insertionDate', 'direction' => 'desc'])
		));
	}
}