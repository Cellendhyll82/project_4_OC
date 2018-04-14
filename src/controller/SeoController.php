<?php

class SeoController extends Controller{

	function save()
	{
		$seo = new Seo();
		$seo->setAttr('title', $_POST['seo-title']);
		$seo->setAttr('imageId', isset($_POST['seo-imageId']) ? $_POST['seo-imageId'] : null);
		$seo->setAttr('keywords', $_POST['seo-keywords']);
		$seo->setAttr('description', $_POST['seo-description']);

		$seo->save();

		$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
		$_SESSION['tmpMessage'] = 'Votre SEO a bien été enregistré';

		header('Location:?c=Seo&p=admin_seo&tmpMessage=82');
	}

	function getData($page)
	{
		unset($_SESSION['dataInserted']);
		
		$toReturn = [];
		
		//tmpMessage
		if(isset($_GET['tmpMessage'])) {
			$toReturn['tmpMessage'] = $_SESSION['tmpMessage'];
			$toReturn['tmpClass'] = $_SESSION['tmpClass'];
		}

		$seo = Seo::getSeo();
		$toReturn['seo'] = $seo;
		if($seoImage = Image::find(['id' => $seo->getAttr('imageId')])) {
			$toReturn['seoImage'] = $seoImage;
		}

		return array_merge($toReturn, array(
			'currentPage' => 'seo',
			'jsFilename' => 'seo',
			'images' => Image::find(null, ['order_by' => 'insertionDate', 'direction' => 'desc'])
		));
	}
}