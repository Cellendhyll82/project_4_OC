<?php
include_once("Base.php");

class Contact
{
	public $title;
	public $content;
	public $contactForm; //0 or 1
	public $email;
	public $menuLabel;

	//constructor
	public function __construct()
	{
		
	}
	
	//getter
	public function getAttr($name) 
	{
		if (property_exists( __CLASS__, $name)) { 
		  return $this->$name;
		} 
		$emess = __CLASS__ . ": unknown member $name (getAttr)";
		throw new Exception($emess, 45);
	}
	
	//setter
	public function setAttr($name, $value)
	{
		if (property_exists( __CLASS__, $name)) {
		  $this->$name = $value; 
		  return $this->$name;
		} 
		$emess = __CLASS__ . ": unknown member $name (setAttr)";
		throw new Exception($emess, 45);
	}
	
	public function cloneContact() {
		$contact = new Contact();
		$attrs = $contact->get_vars();
		foreach ($attrs as $key => $attr) {
			if(null !== $this->getAttr($attr)) {
				$contact->$attr = $this->$attr;
			}
		}
		return $contact;
	}
	
	//---------------------------------------- ACCESS TO DATA BASE ------------------------------------------//
	//create, update & delete
	public function save()
	{
		//creat and fill Media object
		$contact = $this->cloneContact();
		
		$pdo = Base::getConnection();
		$pdo->exec('TRUNCATE TABLE contact');

		Base::insert($contact, "contact");
	}

	public function get_vars()
	{
		$toReturn = array();
		$table = get_object_vars($this);
		foreach($table as $key => $val){
			$toReturn[] = $key;
		}
		return $toReturn;
	}
	
	public function get_object_vars()
	{
		return get_object_vars($this);
	}

	public static function getContact() 
	{
		$query = "SELECT * FROM contact";

		$db = Base::getConnection();
		try{
			$stmt = $db->query($query);
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			if(empty($result)) {
				return null;
			}
			else {
				$contact = new Contact();
				foreach($result as $key => $val){
					$contact->setAttr($key, $val);
				}
				
				return $contact;
			}
		} catch(PDOException $e){
			return null;
		}
	}
}