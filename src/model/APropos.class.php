<?php
include_once("Base.php");

class APropos
{
	public $title;
	public $content;
	public $imageId;
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
	
	public function cloneAPropos() {
		$aPropos = new APropos();
		$attrs = $aPropos->get_vars();
		foreach ($attrs as $key => $attr) {
			if(null !== $this->getAttr($attr)) {
				$aPropos->$attr = $this->$attr;
			}
		}
		return $aPropos;
	}
	
	//---------------------------------------- ACCESS TO DATA BASE ------------------------------------------//
	//create, update & delete
	public function save()
	{
		//creat and fill Media object
		$aPropos = $this->cloneAPropos();
		
		$pdo = Base::getConnection();
		$pdo->exec('TRUNCATE TABLE a_propos');

		Base::insert($aPropos, "a_propos");
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

	public static function getAPropos() 
	{
		$query = "SELECT * FROM a_propos";

		$db = Base::getConnection();
		try{
			$stmt = $db->query($query);
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			if(empty($result)) {
				return null;
			}
			else {
				$aPropos = new APropos();
				foreach($result as $key => $val){
					$aPropos->setAttr($key, $val);
				}
				
				return $aPropos;
			}
		} catch(PDOException $e){
			return null;
		}
	}
}