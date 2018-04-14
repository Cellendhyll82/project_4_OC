<?php
include_once("Base.php");

class Comment
{
	public $id;
	public $username;
	public $creationDate;
	public $content; //content of the comment
	public $episodeId; //82
	public $report; //count the nb of time the comment has been reported

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

	public function cloneComment() {
		$comment = new Comment();
		$attrs = $comment->get_vars();
		foreach ($attrs as $key => $attr) {
			if(null !== $this->getAttr($attr)) {
				$comment->$attr = $this->$attr;
			}
		}
		return $comment;
	}
	
	//---------------------------------------- ACCESS TO DATA BASE ------------------------------------------//
	//create, update & delete
	public function create()
	{
		//creat and fill Comment object
		$comment = $this->cloneComment();
		
		//insert Comment in database
		$param = Base::insert($comment, "comments");

		//retreive, set and return id
		$this -> setAttr("id", $param -> LastInsertId("comments"));
	}

	public function edit()
	{
		Base::update($this, "comments");
	}

	public function delete()
	{
		//delete the Comment from the database
		Base::delete($this, "comments");
	}

	public function get_vars()
	{
		$john = array();
		$table = get_object_vars($this);
		foreach($table as $key => $val){
			$john[] = $key;
		}
		return $john;
	}
	
	public function get_object_vars()
	{
		return get_object_vars($this);
	}

	//setting 
	public static function find($attrs = null, $optional = null, $reportedFirst = false)
	{
		$query = "SELECT * FROM comments";

		if($attrs !== null) {
			$query .= " WHERE ";
			foreach ($attrs as $attrName => $attrValue) {
				$query .= $attrName." = :".$attrName." AND ";
			}
			$query = substr($query, 0, -5);
		}

		if($reportedFirst) {
			$query .= " ORDER BY report DESC";
		}

		if($optional !== null) {
			if(isset($optional["order_by"])) {
				$query .= $reportedFirst? ", ".$optional["order_by"] : " ORDER BY ".$optional["order_by"];
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
					$comment = new Comment();
					foreach($line as $key => $val){
						$comment->setAttr($key, $val);
					}
					$tab[] = $comment;
				}
				
				return $tab;
			} 
			else {
				$comment = new Comment();
				foreach($result["0"] as $key => $val){
					$comment->setAttr($key, $val);
				}
				return $comment;
			}
		 }catch(PDOException $e){
			return null;
		}
	}
}