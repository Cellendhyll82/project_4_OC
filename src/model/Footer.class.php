<?php
include_once("Base.php");

class Footer
{
	public $title;
	public $content;

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
	
	public function cloneFooter() {
		$footer = new Footer();
		$attrs = $footer->get_vars();
		foreach ($attrs as $key => $attr) {
			if(null !== $this->getAttr($attr)) {
				$footer->$attr = $this->$attr;
			}
		}
		return $footer;
	}
	
	//---------------------------------------- ACCESS TO DATA BASE ------------------------------------------//
	//create, update & delete
	public function save()
	{
		//creat and fill Media object
		$footer = $this->cloneFooter();
		
		$pdo = Base::getConnection();
		$pdo->exec('TRUNCATE TABLE footer');

		Base::insert($footer, "footer");
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

	public static function getFooter() 
	{
		$query = "SELECT * FROM footer";

		$db = Base::getConnection();
		try{
			$stmt = $db->query($query);
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			if(empty($result)) {
				return null;
			}
			else {
				$footer = new Footer();
				foreach($result as $key => $val){
					$footer->setAttr($key, $val);
				}
				
				return $footer;
			}
		} catch(PDOException $e){
			return null;
		}
	}
}