<?php

class CommentController extends Controller{

	function save()
	{
		isset($_GET['id']) ? $this->edit() : $this->add();
	}

	function add()
	{
		unset($_SESSION['dataInserted']);

		//check for errors
		if($error = ErrorDetector::checkError('Comment')) {			
			$_SESSION['tmpClass'] = 'tmpMessage tmpRed';
			if($error == 'missingEpisode') {
				$_SESSION['tmpMessage'] = 'Veuillez choisir un épisode auquel ajouter le commentaire';
			}

			foreach ($_POST as $key => $postVar) {
				$_SESSION['dataInserted'][$key] = $postVar;
			}

			header('Location:?c=Comment&p=admin_edit_comment&tmpMessage=82&error='.$error);
		}
		else {
			$comment = new Comment();
			$comment->setAttr('username', $_POST['comment-username']);
			$comment->setAttr('creationDate', $_POST['comment-creationDate']);
			$comment->setAttr('content', $_POST['comment-content']);
			$comment->setAttr('episodeId', $_POST['comment-episodeId']);
			$comment->setAttr('report', 0);

			$comment->create();

			$episode = Episode::find(['id' => $_POST['comment-episodeId']]);
			$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
			$_SESSION['tmpMessage'] = 'Votre commentaire a été ajouté à "<em>'.$episode->getAttr('title').'</em>"';
			
			header('Location:?c=Comment&p=admin_comments&tmpMessage=82');
		}
	}

	function edit()
	{
		unset($_SESSION['dataInserted']);

		//check for errors
		if($error = ErrorDetector::checkError('Comment')) {			
			$_SESSION['tmpClass'] = 'tmpMessage tmpRed';
			if($error == 'missingEpisode') {
				$_SESSION['tmpMessage'] = 'Veuillez choisir un épisode auquel ajouter le commentaire';
			}

			foreach ($_POST as $key => $postVar) {
				$_SESSION['dataInserted'][$key] = $postVar;
			}

			header('Location:?c=Comment&p=admin_edit_comment&tmpMessage=82&error='.$error);
		}
		else {
			$comment = new Comment();
			$comment->setAttr('id', $_GET['id']);
			$comment->setAttr('username', $_POST['comment-username']);
			$comment->setAttr('creationDate', $_POST['comment-creationDate']);
			$comment->setAttr('content', $_POST['comment-content']);
			$comment->setAttr('episodeId', $_POST['comment-episodeId']);
			$comment->setAttr('report', isset($_POST['comment-report']) ? $_POST['comment-report'] : 0);

			$comment->edit();

			$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
			$_SESSION['tmpMessage'] =  'Les modifications apportées au commentaire de '.$comment->getAttr('username').' ont été enregistrées';
			
			header('Location:?c=Comment&p=admin_comments&tmpMessage=82');
		}
	}

	function delete()
	{
		$comment = Comment::find(['id' => $_GET['id']]);
		$comment->delete();

		$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
		$_SESSION['tmpMessage'] = 'Le commentaire de "'.$comment->getAttr('username').'" a été supprimé';
		
		header('Location:?c=Comment&p=admin_comments&tmpMessage=82');
	}

	function getData($page)
	{
		switch ($page)
		{
			case 'admin_comments':
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

				//true gives reported comments first
				$comments = Comment::find(null, ['order_by' => 'creationDate', 'direction' => 'desc'], true);
				if($comments) {
					if(!is_array($comments)) {
						$comments = [$comments];
					}
					$toReturn['comments'] = $comments;
				}
				
				$episodes = Episode::find();
				if($episodes) {
					if(!is_array($episodes)) {
						$episodes = [$episodes];
					}
					$toReturn['episodes'] = $episodes;
				}

				return array_merge($toReturn, array(
					'currentPage' => 'comments'
				));
				break;

			case 'admin_edit_comment':
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
				$toReturn['errorMissingEpisode'] = isset($_GET['error']) && $_GET['error'] == 'missingEpisode' ? 'error' : '';
				
				//data to edit existing comment
				if(isset($_GET['id'])) {
					$comment = Comment::find(['id' => $_GET['id']]);
					$toReturn['comment'] = $comment;
				}

				return array_merge($toReturn, array(
					'currentPage' => 'comments',
					'jsFilename' => 'comment',
					'episodes' => Episode::find(null, ['order_by' => 'publicationDate', 'direction' => 'desc']),
					'episodeImages' => Episode::getEpisodeImages()
				));
				break;

			default:
				return [];
		}
	}
}