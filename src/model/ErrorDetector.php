<?php

abstract class ErrorDetector {

	private static function isValidSlug($slug){
		if($slug == '') {
			return 1;
		}
		return preg_match('#^[a-zA-Z0-9_-]+$#',$slug);
	}

	private static function isValidUrl($url) {
		if($url == '') {
			return 1;
		}
		return filter_var($url, FILTER_VALIDATE_URL);
	}

	private static function isValidEmail($email) {
		if($email == '') {
			return 1;
		}
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	public static function checkError($class, $method = null, $singleFile = null)
	{
		if($class == 'Episode') {
			//check if title is unique
			if($episode = Episode::find(['title' => $_POST['episode-title']])) {
				if($method == 'add') {
					return 'existingTitle';
				} else if($method == 'edit') {
					if($episode->getAttr('id') !== $_GET['id']) {
						return 'existingTitle';
					}
				}
			} 

			if($episode = Episode::find(['slug' => $_POST['episode-slug']])) {
				if($method == 'add') {
					return 'existingSlug';
				} else if($method == 'edit') {
					if($episode->getAttr('id') !== $_GET['id']) {
						return 'existingSlug';
					}
				}
			}

			//check if slug is valid
			if(ErrorDetector::isValidSlug($_POST['episode-slug']) === 0) {
				return 'invalidSlug';
			}

			//check if status = published and publicationDate > today
			if($_POST["episode-status"] == "published" && strtotime($_POST["episode-publicationDate"]) > strtotime(date("Y-m-d"))) {
				return "statusAndPublicationDate1";
			}

			//check if status = published and publicationDate = null
			if($_POST["episode-status"] == "published" && $_POST["episode-publicationDate"] == '') {
				return "statusAndPublicationDate2";
			}
		}

		if($class == 'Comment') {
			//check that an episode has been selected
			if(!$_POST['comment-episodeId']) {
				return 'missingEpisode';
			}
		}

		if($class == 'Contact') {
			//check email
			if(ErrorDetector::isValidEmail($_POST['contact-email']) == false) {
				return 'invalidEmail';
			}

			//check if there is an email if contactForm is checked
			if($_POST['contact-contactForm'] && !$_POST['contact-email']) {
				return 'emptyEmailWithContactForm';
			}
		}

		if($class == 'Image' && !$singleFile) {
			$target_dir = 'www/images/';
			$uploadOk = 1;
			foreach($_FILES["new-image"]["tmp_name"] as $key => $tmp_name) {
				$temp = $_FILES["new-image"]["tmp_name"][$key];
			    $name = $_FILES["new-image"]["name"][$key];

				$target_file = $target_dir . pathinfo($_FILES['new-image']['name'][$key], PATHINFO_BASENAME);
				$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

				//check name of the file
				if(ErrorDetector::isValidSlug(pathinfo($_FILES['new-image']['name'][$key], PATHINFO_FILENAME)) === false) {
					return 'filename';
				}

				//check if file is recognised
				if($_FILES['new-image']['error'][$key] !== 0) {
					return 'fileLoad';
				}

				// Check if image file is a actual image or fake image
			    $check = getimagesize($_FILES['new-image']['tmp_name'][$key]);		    
			    if(!$check) {
			    	return 'fakeImage';
			    }

				// Check file size - 4Mo max
				if($_FILES['new-image']['size'][$key] > 4000000) {
				   return 'fileTooHeavy';
				}

				// Allow certain file formats
				if($imageFileType != 'jpg' && $imageFileType != 'JPG' 
				&& $imageFileType != 'png' && $imageFileType != 'PNG'
				&& $imageFileType != 'jpeg' && $imageFileType != 'JPEG') {
				    return 'forbidenExtension';
				}
			}
		}

		if($class == 'Image' && $singleFile) {
			$target_dir = 'www/images/';
			$uploadOk = 1;
			$temp = $_FILES["new-image"]["tmp_name"];
		    $name = $_FILES["new-image"]["name"];

			$target_file = $target_dir . pathinfo($_FILES['new-image']['name'], PATHINFO_BASENAME);
			$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

			//check name of the file
			if(ErrorDetector::isValidSlug(pathinfo($_FILES['new-image']['name'], PATHINFO_FILENAME)) === false) {
				return 'filename';
			}
			

			//check if file is recognised
			if($_FILES['new-image']['error'] !== 0) {
				return 'fileLoad';
			}


			// Check if image file is a actual image or fake image
		    $check = getimagesize($_FILES['new-image']['tmp_name']);		    
		    if(!$check) {
		    	return 'fakeImage';
		    }

			// Check file size - 4Mo max
			if($_FILES['new-image']['size'] > 4000000) {
			   return 'fileTooHeavy';
			}

			// Allow certain file formats
			if($imageFileType != 'jpg' && $imageFileType != 'JPG' 
			&& $imageFileType != 'png' && $imageFileType != 'PNG'
			&& $imageFileType != 'jpeg' && $imageFileType != 'JPEG') {
			    return 'forbidenExtension';
			}
		}

		return null;
	}

}