<?php
include_once("Base.php");

class Image
{
	public $id;
	public $filename;
	public $insertionDate;

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

	public function cloneImage() {
		$image = new Image();
		$attrs = $image->get_vars();
		foreach ($attrs as $key => $attr) {
			if(null !== $this->getAttr($attr)) {
				$image->$attr = $this->$attr;
			}
		}
		return $image;
	}
	
	//---------------------------------------- ACCESS TO DATA BASE ------------------------------------------//
	//create, update & delete
	public function create()
	{
		//creat and fill Media object
		$image = $this->cloneImage();
		
		//insert Media in database
		$param = Base::insert($image, "images");

		//retreive, set and return id
		$this -> setAttr("id", $param -> LastInsertId("images"));
	}

	public function edit()
	{
		Base::update($this, "images");
	}

	public function delete()
	{
		Base::delete($this, "images");
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

	// -------------------------------------- DATABASE ACCESS ------------------------------------- //
	public static function findAll(){
		$query = "SELECT * FROM images";

		$db = Base::getConnection();
		try{
			$stmt = $db->query($query);
			
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if(empty($result)) {
				return null;
			}
			else {
				$tab = array();
				foreach($result as $obj=>$line){
					$image = new Image();
					foreach($line as $key => $val){
						$image->setAttr($key, $val);
					}
					$tab[] = $image;
				}
				
				return $tab;
			}
		 }catch(PDOException $e){
			return null;
		}
	}

	public static function find($attrs = null, $optional = null){
		$query = "SELECT * FROM images";

		if($attrs !== null) {
			$query .= " WHERE ";
			foreach ($attrs as $attrName => $attrValue) {
				$query .= $attrName." = :".$attrName." AND ";
			}
			$query = substr($query, 0, -5);
		}

		if($optional !== null) {
			if(isset($optional["order_by"])) {
				$query .= " ORDER BY ".$optional["order_by"];
			}
			if(isset($optional["direction"])) {
				$query .= " ".$optional["direction"];
			}
			if(isset($optional["start"])) {
				$query .= " LIMIT ".$optional["start"];
			}
			if(isset($optional["length"])) {
				$query .= ", ".$optional["length"];
			}
		}

		$db = Base::getConnection();
		try{
			if($attrs) {
				$stmt = $db->prepare($query);
				$stmt->execute($attrs);
			} else {
				$stmt = $db->query($query);
			}

			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if(empty($result)) {
				return null;
			}
			else if(sizeof($result) > 1) {
				$tab = array();
				foreach($result as $obj=>$line){
					$image = new Image();
					foreach($line as $key => $val){
						$image->setAttr($key, $val);
					}
					$tab[] = $image;
				}
				
				return $tab;
			} 
			else {
				$image = new Image();
				foreach($result["0"] as $key => $val){
					$image->setAttr($key, $val);
				}
				return $image;
			}
		 }catch(PDOException $e){
			return null;
		}
	}
}