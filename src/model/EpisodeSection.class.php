<?php
include_once("Base.php");

class EpisodeSection
{
	public $title;
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
	
	public function cloneEpisodeSection() {
		$episodeSection = new EpisodeSection();
		$attrs = $episodeSection->get_vars();
		foreach ($attrs as $key => $attr) {
			if(null !== $this->getAttr($attr)) {
				$episodeSection->$attr = $this->$attr;
			}
		}
		return $episodeSection;
	}
	
	//---------------------------------------- ACCESS TO DATA BASE ------------------------------------------//
	//create, update & delete
	public function save()
	{
		//creat and fill Media object
		$episodeSection = $this->cloneEpisodeSection();
		
		$pdo = Base::getConnection();
		$pdo->exec('TRUNCATE TABLE episodeSection');

		Base::insert($episodeSection, "episode_section");
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

	public static function getEpisodeSection() 
	{
		$query = "SELECT * FROM episode_section";

		$db = Base::getConnection();
		try{
			$stmt = $db->query($query);
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			if(empty($result)) {
				return null;
			}
			else {
				$episodeSection = new EpisodeSection();
				foreach($result as $key => $val){
					$episodeSection->setAttr($key, $val);
				}
				
				return $episodeSection;
			}
		} catch(PDOException $e){
			return null;
		}
	}
}