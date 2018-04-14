<?php

abstract class Controller{

	function showPage($param)
	{	
		$page = "";
		if(isset($param["p"])) {
			$page = $param["p"];
		}

		if(substr($page, 0, 6) == "admin_") {
			$filePath = "\\admin\\".$page.".html";
		} else {
			$filePath = $page == "" ? "main-page-content.html" : $page.".html";
		}

		$template = new Template();
		$template->setAttr("filePath", $filePath);
		$template->setAttr("param", $this->getData($page));
		$template->render();
	}

	static function getProperSlug($class, $method, $slug)
	{
		if($class == 'Image') {
			if($class::find(['filename' => $slug])) {

				$filename = explode('.', $slug)[0];
				$extention = explode('.', $slug)[1];

				$toReturn = $slug;

				if(preg_match("#^[a-zA-Z0-9_]*_[0-9]*$#", $filename)) {
					$toReturn = preg_replace("#_[0-9]*$#", "", $filename).'.'.$extention;
				}
				
				if(!$class::find(['filename' => $toReturn])) {
					return $toReturn;
				}

				$i = 2;
				while ($class::find(['filename' => $filename."_".$i.'.'.$extention])) {
					$i++;
				}

				return $filename."_".$i.'.'.$extention;
			}
			
			return $slug;
		}

		switch ($method) {
			case 'add':
				if($class::find(['slug' => $slug])) {

					$toReturn = $slug;

					if(preg_match("#^[a-zA-Z0-9_-]*-[0-9]*$#", $toReturn)) {
						$toReturn = preg_replace("#-[0-9]*$#", "", $toReturn);
					}
					
					if(!$class::find(['slug' => $toReturn])) {
						return $toReturn;
					}

					$i = 2;
					while ($class::find(['slug' => $toReturn."-".$i])) {
						$i++;
					}

					return $toReturn."-".$i;
				}
				
				return $slug;
				break;
			
			case 'edit':
				if($inDB = $class::find(['slug' => $slug])) {

					$toReturn = $slug;

					if($inDB->getAttr("id") == $_GET["id"]) {
						return $toReturn;
					}

					if(preg_match("#^[a-zA-Z0-9_-]*-[0-9]*$#", $toReturn)) {
						$toReturn = preg_replace("#-[0-9]*$#", "", $toReturn);
						if($inDB = $class::find(['slug' => $toReturn])) {
							if($inDB->getAttr("id") == $_GET["id"]) {
								return $toReturn;
							}
						} else {
							return $toReturn;
						}
					} 

					$i = 2;
					while ($inDB = $class::find(['slug' => $toReturn."-".$i])) {
						if($inDB->getAttr("id") == $_GET["id"]) {
							return $toReturn."-".$i;
						}
						$i++;
					}

					return $toReturn."-".$i;
				}

				return $slug;
				break;

			default:
				return null;
				break;
		}		
	}

	/*
	*	return array of the picture in different sizes
	*	array(
	*		"large" => 2000 - 1125,
	*		"mediumLarge" =>  1500 - 850,
	*		"mediumSmall" => 1000 - 560,
	*		"small" => 500 - 282
	*	)
	*/
	protected static function getResizedPictures($path, $savePath, $saveName) {
		$name = explode(".", $saveName)[0];
		$extension = explode(".", $saveName)[1];

		$saveLarge = $savePath."large/".$name."_lg.".$extension;
		$saveMediumLarge = $savePath."mediumLarge/".$name."_md_lg.".$extension;
		$saveMediumSmall = $savePath."mediumSmall/".$name."_md_sm.".$extension;
		$saveSmall = $savePath."small/".$name."_sm.".$extension;

		return array(
			"large" => Controller::resizePicture($path, $saveLarge, 2000, 1125),
			"mediumLarge" => Controller::resizePicture($path, $saveMediumLarge, 1500, 850),
			"mediumSmall" => Controller::resizePicture($path, $saveMediumSmall, 1000, 560),
			"small" => Controller::resizePicture($path, $saveSmall, 500, 282)
		);
	}

	protected static function resizePicture($path, $save, $maxWidth, $maxHeight) {
		$info = getimagesize($path);
		$srcWidth = $info[0];
		$srcHeight = $info[1];

		if($info["mime"] == "image/png") {
			$src = imagecreatefrompng($path);
		} else if($info["mime"] == "image/jpeg") {
			$src = imagecreatefromjpeg($path);
		} else if($info["mime"] == "image/gif") {
			$src = imagecreatefromgif($path);
		} else {
			return false;
		}

		$srcAspect = $srcWidth / $srcHeight;
		$newAspect = $maxWidth / $maxHeight;

		if($srcAspect < $newAspect) {
			//narrower
			$newHeight = $maxHeight;
			$newWidth = $newHeight * $srcAspect;
		} else if ($srcAspect > $newAspect) {
			//wider
			$newWidth = $maxWidth;
			$newHeight = $newWidth / $srcAspect;
		} else {
			//same shape
			$newWidth = $maxWidth;
			$newHeight = $maxHeight;
		}

		//check if picture is not smaller than the size requested
		if($srcWidth < $maxWidth && $srcHeight < $maxHeight) {
			$newWidth = $srcWidth;
			$newHeight = $srcHeight;
		}

		$resized = imagecreatetruecolor($newWidth, $newHeight);
		imagesavealpha($resized, true);
		$transparentColour = imagecolorallocatealpha($resized, 0, 0, 0, 127);
		imagefill($resized, 0, 0, $transparentColour);

		imagecopyresampled($resized, $src, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);

		if($info["mime"] == "image/png") {
			return imagepng($resized, $save);
		} else if($info["mime"] == "image/jpeg") {
			return imagejpeg($resized, $save);
		} else if($info["mime"] == "image/gif") {
			return imagegif($resized, $save);
		}
	}

	static function createThumbnail($path, $save, $width, $height) {
		$info = getimagesize($path);
		$size = array($info[0], $info[1]);

		if($info["mime"] == "image/png") {
			$src = imagecreatefrompng($path);
		} else if($info["mime"] == "image/jpeg") {
			$src = imagecreatefromjpeg($path);
		} else if($info["mime"] == "image/gif") {
			$src = imagecreatefromgif($path);
		} else {
			return false;
		}

		//create transparent image
		$thumb = imagecreatetruecolor($width, $height);	
		imagesavealpha($thumb, true);
		$transparentColour = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
		imagefill($thumb, 0, 0, $transparentColour);

		$srcAspect = $size[0] / $size[1];
		$thumbAspect = $width / $height;

		if($srcAspect < $thumbAspect) {
			//narrower
			$newSize = array($size[0], $size[0] / $thumbAspect);
			$srcPos = array(0, ($size[1] - $newSize[1])/2);
		} else if ($srcAspect > $thumbAspect) {
			//wider
			$newSize = array($size[1] * $thumbAspect, $size[1]);
			$srcPos = array(($size[0] - $newSize[0])/2, 0);
		} else {
			//same shape
			$newSize = array($size[0], $size[1]);
			$srcPos = array(0,0);
		}

		imagecopyresampled($thumb, $src, 0, 0, $srcPos[0], $srcPos[1], $width, $height, $newSize[0], $newSize[1]);

		if($info["mime"] == "image/png") {
			return imagepng($thumb, $save);
		} else if($info["mime"] == "image/jpeg") {
			return imagejpeg($thumb, $save);
		} else if($info["mime"] == "image/gif") {
			return imagegif($thumb, $save);
		}
	}

}