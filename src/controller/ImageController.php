<?php

class ImageController extends Controller {

	function save()
	{
		$singleFile = (isset($_GET['singleFile']) && $_GET['singleFile'] == true) ? true : false;

		if($error = ErrorDetector::checkError('Image', 'add', $singleFile)) {
			$_SESSION['tmpClass'] = 'tmpMessage tmpRed';
			if($error == 'filename') {
				$_SESSION['tmpMessage'] = 'Please insert a file named without spaces nor accents';
			} else if($error == 'fileLoad') {
				$_SESSION['tmpMessage'] = 'Something wrong happened. Please try again';
			} else if ($error == 'fakeImage') {
				$_SESSION['tmpMessage'] = 'The selected file is not an image';
			} else if ($error == 'fileTooHeavy') {
				$_SESSION['tmpMessage'] = 'The selected file is too heavy (max 4Mo)';
			} else if ($error == 'forbidenExtension') {
				$_SESSION['tmpMessage'] = 'File type not accepted. Accepted file types are jpg, jpeg and png';
			}

			header('Location:?c=Image&p=admin_images&tmpMessage=82&error='.$error);
			
		} else if($singleFile) {
			$properFilename = Controller::getProperSlug('Image', 'add', $_FILES['new-image']['name']);
			$targetFile = 'www/images/'.$properFilename;

			//rotate image correctly
			$filename = $_FILES['new-image']['name'];
			$filePath = $_FILES['new-image']['tmp_name'];
			$uploaded = false;

			//if image has headers
			if($_FILES['new-image']['type'] === "image/jpeg") {
				$exif = exif_read_data($_FILES['new-image']['tmp_name']);
				
				//rotate image
				if (!empty($exif['Orientation'])) {
					$imageResource = imagecreatefromjpeg($filePath);

				    switch ($exif['Orientation']) {
				        case 3:
					        $image = imagerotate($imageResource, 180, 0);
					        break;
				        case 6:
					        $image = imagerotate($imageResource, -90, 0);
					        break;
				        case 8:
					        $image = imagerotate($imageResource, 90, 0);
					        break;
				        default:
				        $image = $imageResource;
				    }
				} else {
					$image = imagecreatefromjpeg($filePath);
				}

				$uploaded = imagejpeg($image, $targetFile, 100);
				imagedestroy($image);
			} else {
				if(move_uploaded_file($_FILES["new-image"]["tmp_name"], $targetFile)) {
					$uploaded = true;
				}
			}
			
			if ($uploaded) {
				$image = new Image();
				$image->setAttr('filename', $properFilename);
				$image->setAttr('insertionDate', date('Y-m-d H:i:s'));

				$image->create();

				$filename = explode('.', $properFilename)[0];
				$extension = explode('.', $properFilename)[1];
				$imageThumbnail = Controller::createThumbnail(
					"www/images/".$properFilename,
					"www/images/thumbnails/".$filename."_130x130.".$extension,
					130,
					130
				);

				$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
				$_SESSION['tmpMessage'] = $image->getAttr('filename').' has been added to the system';

				header('Location:?c=Image&p=admin_images&tmpMessage=82');
				
			}
			else {
				$_SESSION['tmpClass'] = 'tmpMessage tmpRed';
				$_SESSION['tmpMessage'] = 'Something wrong happened. Please try again';
				header('Location:?c=Image&p=admin_images&tmpMessage=82');
			}
		} else {

			//if file there are several files
			foreach($_FILES['new-image']['tmp_name'] as $key => $tmp_name) {
				$properFilename = Controller::getProperSlug('Image', 'add', $_FILES['new-image']['name'][$key]);
				$targetFile = 'www/images/'.$properFilename;

				//rotate image correctly
				$filename = $_FILES['new-image']['name'][$key];
				$filePath = $_FILES['new-image']['tmp_name'][$key];
				$uploaded = false;
				$allUploaded = true;

				//if image has headers
				if($_FILES['new-image']['type'][$key] === "image/jpeg") {
					$exif = exif_read_data($_FILES['new-image']['tmp_name'][$key]);
					
					//rotate image
					if (!empty($exif['Orientation'])) {
						$imageResource = imagecreatefromjpeg($filePath);

					    switch ($exif['Orientation']) {
					        case 3:
						        $image = imagerotate($imageResource, 180, 0);
						        break;
					        case 6:
						        $image = imagerotate($imageResource, -90, 0);
						        break;
					        case 8:
						        $image = imagerotate($imageResource, 90, 0);
						        break;
					        default:
					        $image = $imageResource;
					    }
					} else {
						$image = imagecreatefromjpeg($filePath);
					}

					$uploaded = imagejpeg($image, $targetFile, 100);
					imagedestroy($image);
				} else {
					if(move_uploaded_file($_FILES["new-image"]["tmp_name"][$key], $targetFile)) {
						$uploaded = true;
					}
				}
				
				if ($uploaded) {
					$image = new Image();
					$image->setAttr('filename', $properFilename);
					$image->setAttr('insertionDate', date('Y-m-d H:i:s'));

					$image->create();

					$filename = explode('.', $properFilename)[0];
					$extension = explode('.', $properFilename)[1];

					//generate different sizes
					Controller::getResizedPictures(
						"www/images/".$properFilename,
						"www/images/",
						$filename.".".$extension
					);

					$imageThumbnail = Controller::createThumbnail(
						"www/images/".$properFilename,
						"www/images/thumbnails/".$filename."_130x130.".$extension,
						130,
						130
					);
				}
				else {
					//if file wasn't successfully uploaded
					$allUploaded = false;
				}
			}

			if($allUploaded) {
				$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
				if(count($_FILES["new-image"]["tmp_name"]) === 1) {
					$_SESSION['tmpMessage'] = $image->getAttr('filename').' have been added to the system';
				} else {
					$_SESSION['tmpMessage'] = 'The '.$_FILES["new-image"]["tmp_name"]->length.' images have been added to the system';
				}
				header('Location:?c=Image&p=admin_images&tmpMessage=82');
			}
			else {
				$_SESSION['tmpClass'] = 'tmpMessage tmpRed';
				$_SESSION['tmpMessage'] = 'Something wrong happened. Please try again';
				header('Location:?c=Image&p=admin_images&tmpMessage=82');
			}
		}
	}

	function delete()
	{
		$image = Image::find(['id' => $_GET['id']]);
		$image->delete();
		
		//delete file
		if(file_exists("www/images/".$image->getAttr("filename"))) {
			unlink("www/images/".$image->getAttr("filename"));	
		}

		//delete thumbnail
		$filename = explode('.', $image->getAttr("filename"))[0];
		$extension = explode('.', $image->getAttr("filename"))[1];
		if(file_exists("www/images/thumbnails/".$filename."_130x130.".$extension)) {
			unlink("www/images/thumbnails/".$filename."_130x130.".$extension);	
		}

		//large
		if(file_exists("www/images/large/".$filename."_lg.".$extension)) {
			unlink("www/images/large/".$filename."_lg.".$extension);
		}

		//mediumLarge
		if(file_exists("www/images/mediumLarge/".$filename."_md_lg.".$extension)) {
			unlink("www/images/mediumLarge/".$filename."_md_lg.".$extension);
		}

		//mediumSmall
		if(file_exists("www/images/mediumSmall/".$filename."_md_sm.".$extension)) {
			unlink("www/images/mediumSmall/".$filename."_md_sm.".$extension);
		}

		//small
		if(file_exists("www/images/small/".$filename."_sm.".$extension)) {
			unlink("www/images/small/".$filename."_sm.".$extension);
		}

		//check if image is used in Seo, Accueil, APropos or in an Episode
		$seo = Seo::getSeo();
		$accueil = Accueil::getAccueil();
		$aPropos = APropos::getAPropos();

		if($seo->getAttr('imageId') == $image->getAttr('id')) {
			$seo->setAttr('imageId', null);
			$seo->save();
		}
		if($accueil->getAttr('imageId') == $image->getAttr('id')) {
			$accueil->setAttr('imageId', null);
			$accueil->save();
		}
		if($aPropos->getAttr('imageId') == $image->getAttr('id')) {
			$aPropos->setAttr('imageId', null);
			$aPropos->save();
		}

		$episodes = Episode::find();
		if($episodes) {
			if(!is_array($episodes)) {
				$episodes = [$episodes];
			}

			foreach ($episodes as $key => $episode) {
				if($episode->getAttr('imageId') == $image->getAttr('id')) {
					$episode->setAttr('imageId', null);
					$episode->edit();
				}
			}
		}

		$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
		$_SESSION['tmpMessage'] = $image->getAttr('filename').' has been deleted';

		header('Location:?c=Image&p=admin_images&tmpMessage=82');
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
			'jsFilename' => 'image',
			'images' => Image::find(null, ['order_by' => 'insertionDate', 'direction' => 'desc'])
		));
	}
}