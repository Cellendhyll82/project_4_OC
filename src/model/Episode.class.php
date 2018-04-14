<?php
include_once("Base.php");

class Episode
{
	public $id;
	public $title;
	public $slug;
	public $status; //published or draft
	public $creationDate;
	public $modificationDate;
	public $publicationDate;
	public $imageId; //Id of main picture
	public $content; //content of the episode
	public $description; //description of the episode => used on main episode
	public $author;
	public $seoKeywords;
	public $seoDescription;

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

	public function cloneEpisode() {
		$episode = new Episode();
		$attrs = $episode->get_vars();
		foreach ($attrs as $key => $attr) {
			if(null !== $this->getAttr($attr)) {
				$episode->$attr = $this->$attr;
			}
		}
		return $episode;
	}
	
	//---------------------------------------- ACCESS TO DATA BASE ------------------------------------------//
	//create, update & delete
	public function create()
	{	
		//insert Episode in database
		$param = Base::insert($this, "episodes");

		//retreive, set and return id
		$this -> setAttr("id", $param -> LastInsertId("episodes"));
	}

	public function edit()
	{
		Base::update($this, "episodes");
	}

	public function delete()
	{
		//delete associated comments
		$comments = Comment::find();

		if($comments) {
			if(!is_array($comments)) {
				$comments = [$comments];
			}

			foreach ($comments as $key => $comment) {
				if($comment->getAttr("episodeId") == $this->id) {
					$comment->delete();
				}
			}
		}
		//delete the article from the database
		Base::delete($this, "episodes");
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

	public static function isValidSlug($slug)
	{
		$query = "SELECT COUNT(id) FROM episodes WHERE id = :id";
		$db = Base::getConnection();
		try{
			$stmt = $db->prepare($query);
			$stmt->execute(array( "id" => $id));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			
			return ($row["COUNT(id)"] !== "0");
		 }catch(PDOException $e){
			return false;
		}
	}

	/*
		return array(
			episodeId1 => Image,
			episodeId2 => Image,
			episodeId3 => Image,
			...
		);
		Episodes without images are not returned by this method
	*/
	public static function getEpisodeImages()
	{
		$toReturn = [];
		$episodes = Episode::find();
		if($episodes) {
			if(!is_array($episodes)) {
				$episodes = [$episodes];
			}

			foreach ($episodes as $key => $episode) {
				if($episode->getAttr('imageId')) {
					$toReturn[$episode->getAttr('id')] = Image::find(['id' => $episode->getAttr('imageId')]);
				}
			}
		}

		return $toReturn;
	}

	public function getComments()
	{
		$comments = Comment::find(['episodeId' => $this->id], ['order_by' => 'creationDate','direction' => 'desc']);
		if($comments && !is_array($comments)) {
			$comments = [$comments];
		}

		return $comments;
	}

	public function getNbComments()
	{
		$comments = Comment::find(['episodeId' => $this->id]);

		if(!$comments) {
			return 0;
		}

		if(!is_array($comments)) {
			return 1;
		}

		return count($comments);
	}

	/* 	return array(
			episodeId1 => nbComments1,
			episodeId2 => nbComments2,
			episodeId3 => nbComments3,
			...
		);
	*/
	public static function getNbCommentsAll()
	{
		$toReturn = [];
		
		$episodes = Episode::find();
		if($episodes) {
			if(!is_array($episodes)) {
				$episodes = [$episodes];
			}

			foreach ($episodes as $key => $episode) {
				$toReturn[$episode->id] = $episode->getNbComments();
			}
		}

		return $toReturn;
	}

	public static function find($attrs = null, $optional = null){
		$query = 'SELECT * FROM episodes';

		if($attrs !== null) {
			$query .= ' WHERE ';
			foreach ($attrs as $attrName => $attrValue) {
				$query .= $attrName.' = :'.$attrName.' AND ';
			}
			$query = substr($query, 0, -5);
		}

		if($optional !== null) {
			if(isset($optional['order_by'])) {
				$query .= ' ORDER BY '.$optional['order_by'];
			}
			if(isset($optional['direction'])) {
				$query .= ' '.$optional['direction'];
			}
			if(isset($optional['start'])) {
				$query .= ' LIMIT '.$optional['start'];
			}
			if(isset($optional['length'])) {
				$query .= ', '.$optional['length'];
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
					$episode = new Episode();
					foreach($line as $key => $val){
						$episode->setAttr($key, $val);
					}
					$tab[] = $episode;
				}
				
				return $tab;
			} 
			else {
				$episode = new Episode();
				foreach($result['0'] as $key => $val){
					$episode->setAttr($key, $val);
				}
				return $episode;
			}
		 }catch(PDOException $e){
			return null;
		}
	}
	
}