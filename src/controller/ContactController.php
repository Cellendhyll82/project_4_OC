<?php

class ContactController extends Controller{

	function save()
	{
		unset($_SESSION['dataInserted']);

		//check for errors
		if($error = ErrorDetector::checkError('Contact')) {			
			$_SESSION['tmpClass'] = 'tmpMessage tmpRed';
			if($error == 'invalidEmail') {
				$_SESSION['tmpMessage'] = 'Veuillez entrer une adresse email valid';
			} else if ($error == 'emptyEmailWithContactForm') {
				$_SESSION['tmpMessage'] = 'Veuillez entrer une adresse email ou décocher le formulaire de contact';
			}

			foreach ($_POST as $key => $postVar) {
				$_SESSION['dataInserted'][$key] = $postVar;
			}
			//special case for checkbox => $_POST not defined if unchecked
			$_SESSION['dataInserted']['contact-contactForm'] = isset($_POST['contact-contactForm']) ? '1' : '0';

			header('Location:?c=Contact&p=admin_contact&tmpMessage=82&error='.$error);
		}
		else {
			$contact = new Contact();
			$contact->setAttr('title', $_POST['contact-title']);
			$contact->setAttr('content', $_POST['contact-content']);
			$contact->setAttr('contactForm', isset($_POST['contact-contactForm']) ? 1 : 0);
			$contact->setAttr('email', $_POST['contact-email']);
			$contact->setAttr('menuLabel', $_POST['contact-menuLabel']);

			$contact->save();

			$_SESSION['tmpClass'] = 'tmpMessage tmpGreen';
			$_SESSION['tmpMessage'] = 'Votre section "Contact" a bien été enregistré';

			header('Location:?c=Contact&p=admin_contact&tmpMessage=82');
		}
	}

	function getData($page)
	{
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
		$toReturn['errorInvalidEmail'] = isset($_GET['error']) && $_GET['error'] == 'invalidEmail' ? 'error' : '';
		$toReturn['errorEmptyEmailWithContactForm'] = isset($_GET['error']) && $_GET['error'] == 'emptyEmailWithContactForm' ? 'error' : '';
		

		return array_merge($toReturn, array(
			'currentPage' => 'sections',
			'contact' => Contact::getContact()
		));
	}
}