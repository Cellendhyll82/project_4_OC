<?php

class WebsiteController extends Controller{
	
	function showEpisode()
	{
		if(isset($_GET['slug']) && $episode = Episode::find(['slug' => $_GET['slug']])) {
			if($episode->getAttr('status') === 'published') {
				$template = new Template();

				$filePath = 'episode.html';
				$param = $this->getData('episode', $episode);

				$template->setAttr('filePath', $filePath);
				$template->setAttr('param', $param);
				$template->render();
			} else {
				$template = new Template('error-404.html', $this->getData('error-404'));
				$template->render();
			}
		} else {
			$template = new Template('error-404.html', $this->getData('error-404'));
			$template->render();
		}
	}

	function addComment()
	{
		if($episode = Episode::find(['id' => $_GET['id']])) {
			$comment = new Comment();
			$comment->setAttr('username', strip_tags($_POST['username']));
			$comment->setAttr('creationDate', date('Y-m-d H:i:s'));
			$comment->setAttr('content', strip_tags($_POST['content']), '<br><p><a>');
			$comment->setAttr('episodeId', $_GET['id']);
			$comment->setAttr('report', 0);

			$comment->create();

			header('Location:episode/'.$episode->getAttr('slug').'#comments-wrapper');
		} else {
			header('Location:?p=error');
		}
	}

	function reportComment()
	{
		if($comment = Comment::find(['id' => $_GET['id']])) {
			$comment->setAttr('report', (int)$comment->getAttr('report') + 1);
			$comment->edit();

			$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
			$_SESSION['tmpMessage'] = 'Le commentaire de '.$comment->getAttr('username').' a été signalé.<br/>Merci de votre aide.';
			header('Location:episode/'.$_GET['slug'].'#comments-wrapper');
		} else {
			header('Location:?p=error');
		}
	}

	function sendMail()
	{
		$to = 'soufiane.kharroubi@gmail.com';
		$subject = strip_tags($_POST['contactForm-subject'], '<br><p><a>');
		$txt = "Email envoyé depuis \"Billet simple pour l'Alaska\"\n"
		.'Envoyeur: '.strip_tags($_POST['contactForm-name'], '<br><p><a>')."\n"
		.'Email: '.strip_tags($_POST['contactForm-email'], '<br><p><a>')."\n"
		.'Sujet: '.$subject."\n"
		.'Message: '.strip_tags($_POST['contactForm-message'], '<br><p><a>');
		$headers = 'From: '.strip_tags($_POST['contactForm-email'], '<br><p><a>');

		if(mail($to,$subject,$txt,$headers)) {
			$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
			$_SESSION['tmpMessage'] = 'Votre email a bien été envoyé.<br/>Merci de votre intérêt.';
			header('Location:?#contact-anchor');
		} else {
			foreach ($_POST as $key => $postVar) {
				$_SESSION['dataInserted'][$key] = $postVar;
			}
			$email = Contact::getContact()->getAttr('email');
			$_SESSION['tmpClass'] = 'tmpMessage tmpRed';
			$_SESSION['tmpMessage'] = 'Il semble y avoir un problème avec le formulaire que vous avez complété. Veuillez réessayer.<br />Si le problème persiste, veuillez envoyer votre message directement à <a href="mailto:'.$email.'">'.$email.'</a>';
			header('Location:?#contact-anchor');
		}
	}

	function getData($page, $episode = null)
	{
		$toReturn = [];

		//seo
		$seo = Seo::getSeo();
		$toReturn['seo'] = $seo;
		if($seoImage = Image::find(['id' => $seo->getAttr('imageId')])) {
			$toReturn['seoImage'] = $seoImage->getAttr('filename');
		}

		//dataInserted
		if(isset($_SESSION['dataInserted'])) {
			foreach($_SESSION['dataInserted'] as $key => $value) {
				$toReturn[$key] = $_SESSION['dataInserted'][$key];
			}
		}
		unset($_SESSION['dataInserted']);

		//tmpMessage
		if(isset($_SESSION['tmpMessage'])) {
			$toReturn['tmpMessage'] = $_SESSION['tmpMessage'];
			$toReturn['tmpClass'] = $_SESSION['tmpClass'];
		}
		unset($_SESSION['tmpMessage']);
		unset($_SESSION['tmpClass']);

		if(!$episode) {
			$accueil = Accueil::getAccueil();
			$toReturn['accueil'] = $accueil;
			if($accueilImage = Image::find(['id' => $accueil->getAttr('imageId')])) {
				$toReturn['accueilImage'] = $accueilImage->getAttr('filename');
			}

			$aPropos = APropos::getAPropos();
			if($aProposImage = Image::find(['id' => $aPropos->getAttr('imageId')])) {
				$toReturn['aProposImage'] = $aProposImage->getAttr('filename');
			}
			$toReturn['aPropos'] = $aPropos;

			$toReturn['episodeSection'] = EpisodeSection::getEpisodeSection();
			$toReturn['contact'] = Contact::getContact();
			$toReturn['footer'] = Footer::getFooter();
			
			$episodes = Episode::find(['status' => 'published'], ['order_by' => 'publicationDate', 'direction' => 'desc']);
			if($episodes) {
				if(!is_array($episodes)) {
					$episodes = [$episodes];
				}

				$toReturn['episodes'] = $episodes;
				$toReturn['nbEpisodes'] = count($episodes);
			}
		} else {
			if($episodeImage = Image::find(['id' => $episode->getAttr('imageId')])) {
				$toReturn['episodeImage'] = $episodeImage->getAttr('filename');
			}

			$toReturn['episode'] = $episode;

			$comments = Comment::find(['episodeId' => $episode->getAttr('id')], ['order_by' => 'creationDate', 'direction' => 'desc']);
			if($comments) {
				if(!is_array($comments)) {
					$comments = [$comments];
				}
				$toReturn['comments'] = $comments;
			}
			
			$toReturn['accueil'] = Accueil::getAccueil();
			$toReturn['aPropos'] = APropos::getAPropos();
			$toReturn['episodeSection'] = EpisodeSection::getEpisodeSection();
			$toReturn['contact'] = Contact::getContact();
			$toReturn['footer'] = Footer::getFooter();
		}

		return $toReturn;
	}

}