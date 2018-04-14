<?php

class AProposController extends Controller{

	function save()
	{
		$aPropos = new APropos();
		$aPropos->setAttr('title', $_POST['aPropos-title']);
		$aPropos->setAttr('content', $_POST['aPropos-content']);
		$aPropos->setAttr('imageId', isset($_POST['aPropos-imageId']) ? $_POST['aPropos-imageId'] : null);
		$aPropos->setAttr('menuLabel', $_POST['aPropos-menuLabel']);

		$aPropos->save();

		$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
		$_SESSION['tmpMessage'] = 'Votre section "A propos" a bien été enregistré';

		header('Location:?c=APropos&p=admin_aPropos&tmpMessage=82');
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

		$aPropos = APropos::getAPropos();
		$toReturn['aPropos'] = $aPropos;
		if($aProposImage = Image::find(['id' => $aPropos->getAttr('imageId')])) {
			$toReturn['aProposImage'] = $aProposImage;
		}

		return array_merge($toReturn, array(
			'currentPage' => 'sections',
			'jsFilename' => 'aPropos',
			'images' => Image::find(null, ['order_by' => 'insertionDate', 'direction' => 'desc'])
		));
	}
}