<?php

class EpisodeSectionController extends Controller{

	function save()
	{
		$episodeSection = new EpisodeSection();
		$episodeSection->setAttr('title', $_POST['episodeSection-title']);
		$episodeSection->setAttr('menuLabel', $_POST['episodeSection-menuLabel']);

		$episodeSection->save();

		$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
		$_SESSION['tmpMessage'] = 'Votre section "Episodes" a bien été enregistré';

		header('Location:?c=EpisodeSection&p=admin_episodeSection&tmpMessage=82');
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
			'currentPage' => 'sections',
			'episodeSection' => EpisodeSection::getEpisodeSection()
		));
	}
}