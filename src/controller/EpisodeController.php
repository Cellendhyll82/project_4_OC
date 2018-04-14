<?php

class EpisodeController extends Controller{

	function save()
	{
		isset($_GET['id']) ? $this->edit() : $this->add();
	}

	function add()
	{
		unset($_SESSION['dataInserted']);

		//check for errors
		if($error = ErrorDetector::checkError('Episode', 'add')) {			
			$_SESSION['tmpClass'] = 'tmpMessage tmpRed';
			if($error == 'existingTitle') {
				$_SESSION['tmpMessage'] = 'Un épisode avec ce titre existe déjà';
			} else if($error == 'existingSlug') {
				$_SESSION['tmpMessage'] = 'Un épisode avec ce slug existe déjà';
			} else if($error == 'invalidSlug') {
				$_SESSION['tmpMessage'] = 'Le slug contient des caractères non autorisés. Veuillez n\'insérer que les caractères suivants: lettres (sans accents), chiffres, "-" et "_"';
			} else if($error == 'statusAndPublicationDate1') {
				$_SESSION['tmpMessage'] = 'Le statut de l\'épisode est "publié" et la date de publication est après aujourd\'hui';
			} else if($error == 'statusAndPublicationDate2') {
				$_SESSION['tmpMessage'] = 'Le statut de l\'épisode est "publié" et la date de publication est manquante';
			}

			foreach ($_POST as $key => $postVar) {
				$_SESSION['dataInserted'][$key] = $postVar;
			}

			header('Location:?c=Episode&p=admin_edit_episode&tmpMessage=82&error='.$error);
		}
		else {
			$episode = new Episode();
			$episode->setAttr('title', $_POST['episode-title']);
			$episode->setAttr('slug', $_POST['episode-slug']);
			$episode->setAttr('status', $_POST['episode-status']);
			$episode->setAttr('creationDate', date('Y-m-d H:i:s'));
			$episode->setAttr('modificationDate', date('Y-m-d H:i:s'));
			$episode->setAttr('publicationDate', $_POST['episode-publicationDate'] != '' ? $_POST['episode-publicationDate'] : null);
			$episode->setAttr('imageId', isset($_POST['episode-imageId']) ? $_POST['episode-imageId'] : null);
			$episode->setAttr('content', $_POST['episode-content']);
			$episode->setAttr('description', $_POST['episode-description']);
			$episode->setAttr('author', $_POST['episode-author']);
			$episode->setAttr('seoKeywords', $_POST['episode-seoKeywords']);
			$episode->setAttr('seoDescription', $_POST['episode-seoDescription']);

			$episode->create();

			$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
			$_SESSION['tmpMessage'] = $episode->getAttr('title').' a été ajouté à la base de données';
			
			header('Location:?c=Episode&p=admin_episodes&tmpMessage=82');
		}
	}

	function edit()
	{
		unset($_SESSION['dataInserted']);

		//check for errors
		if($error = ErrorDetector::checkError('Episode', 'edit')) {			
			$_SESSION['tmpClass'] = 'tmpMessage tmpRed';
			if($error == 'existingTitle') {
				$_SESSION['tmpMessage'] = 'Un épisode avec ce titre existe déjà';
			} else if($error == 'existingSlug') {
				$_SESSION['tmpMessage'] = 'Un épisode avec ce slug existe déjà';
			} else if($error == 'invalidSlug') {
				$_SESSION['tmpMessage'] = 'Le slug contient des caractères non autorisés. Veuillez n\'insérer que les caractères suivants: lettres (sans accents), chiffres, "-" et "_"';
			} else if($error == 'statusAndPublicationDate1') {
				$_SESSION['tmpMessage'] = 'Le statut de l\'épisode est "publié" et la date de publication est après aujourd\'hui';
			} else if($error == 'statusAndPublicationDate2') {
				$_SESSION['tmpMessage'] = 'Le statut de l\'épisode est "publié" et la date de publication est manquante';
			}

			foreach ($_POST as $key => $postVar) {
				$_SESSION['dataInserted'][$key] = $postVar;
			}

			header('Location:?c=Episode&p=admin_edit_episode&id='.$_GET['id'].'&tmpMessage=82&error='.$error);
		}
		else {
			$originalEpisode = Episode::find(['id' => $_GET['id']]);

			$episode = new Episode();
			$episode->setAttr('id', $_GET['id']);
			$episode->setAttr('title', $_POST['episode-title']);
			$episode->setAttr('slug', $_POST['episode-slug']);
			$episode->setAttr('status', $_POST['episode-status']);
			$episode->setAttr('creationDate', $originalEpisode->getAttr('creationDate'));
			$episode->setAttr('modificationDate', date('Y-m-d H:i:s'));
			$episode->setAttr('publicationDate', $_POST['episode-publicationDate'] != '' ? $_POST['episode-publicationDate'] : null);
			$episode->setAttr('imageId', isset($_POST['episode-imageId']) ? $_POST['episode-imageId'] : null);
			$episode->setAttr('content', $_POST['episode-content']);
			$episode->setAttr('description', $_POST['episode-description']);
			$episode->setAttr('author', $_POST['episode-author']);
			$episode->setAttr('seoKeywords', $_POST['episode-seoKeywords']);
			$episode->setAttr('seoDescription', $_POST['episode-seoDescription']);

			$episode->edit();

			$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
			$_SESSION['tmpMessage'] = 'Les modifications apportées à '.$episode->getAttr('title').' ont été enregistrées';
			
			header('Location:?c=Episode&p=admin_episodes&tmpMessage=82');
		}
	}

	function delete()
	{
		$episode = Episode::find(['id' => $_GET['id']]);
		$episode->delete();

		//delete comments related to episode
		$comments = Comment::find(['episodeId' => $_GET['id']]);
		if($comments) {
			if(!is_array($comments)) {
				$comments = [$comments];
			}

			foreach ($comments as $key => $comment) {
				$comment->delete();
			}
		}

		$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
		$_SESSION['tmpMessage'] = $episode->getAttr('title').' a été supprimé';
		
		header('Location:?c=Episode&p=admin_episodes&tmpMessage=82');
	}

	function getData($page)
	{
		switch ($page)
		{
			case 'admin_episodes':
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

				//episodes
				$episodes = Episode::find(null, ['order_by' => 'creationDate', 'direction' => 'desc']);
				if($episodes && !is_array($episodes)) {
					$episodes = [$episodes];
				}
				$toReturn['episodes'] = $episodes;

				return array_merge($toReturn, array(
					'currentPage' => 'episodes',
					'episodeImages' => Episode::getEpisodeImages(),
					'episodesNbComments' => Episode::getNbCommentsAll()
				));
				break;

			case 'admin_edit_episode':
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

				//dataInserted
				if(isset($_SESSION['dataInserted'])) {
					foreach($_SESSION['dataInserted'] as $key => $value) {
						$toReturn[$key] = $_SESSION['dataInserted'][$key];
					}
				}

				//error messages
				$toReturn['errorExistingTitle'] = isset($_GET['error']) && $_GET['error'] == 'existingTitle' ? 'error' : '';
				$toReturn['errorExistingSlug'] = isset($_GET['error']) && $_GET['error'] == 'existingSlug' ? 'error' : '';
				$toReturn['errorInvalidSlug'] = isset($_GET['error']) && $_GET['error'] == 'invalidSlug' ? 'error' : '';
				$toReturn['errorStatusAndPublicationDate1'] = isset($_GET['error']) && $_GET['error'] == 'statusAndPublicationDate1' ? 'error' : '';
				$toReturn['errorStatusAndPublicationDate2'] = isset($_GET['error']) && $_GET['error'] == 'statusAndPublicationDate2' ? 'error' : '';

				//data to edit existing episode
				if(isset($_GET['id'])) {
					$episode = Episode::find(['id' => $_GET['id']]);
					$toReturn['episode'] = $episode;
					if($episodeImage = Image::find(['id' => $episode->getAttr('imageId')])) {
						$toReturn['episodeImage'] = $episodeImage;
					}

					$toReturn['comments'] = $episode->getComments();
					$toReturn['nbComments'] = $episode->getNbComments();
				}

				return array_merge($toReturn, array(
					'currentPage' => 'episodes',
					'jsFilename' => 'episode',
					'images' => Image::find(null, ['order_by' => 'insertionDate', 'direction' => 'desc'])
				));
				break;

			default:
				return [];
		}
	}
}