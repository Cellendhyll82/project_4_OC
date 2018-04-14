<?php
class Base{
	
	private static $dblink;
	
	private static function connect()
	{
		include('DataBase.php');
		$dsn=$prefix.':host='.$host.';dbname='.$dbname;
		$dblink=new PDO($dsn, $user, $pass, array(PDO::ERRMODE_EXCEPTION=>true, PDO::ATTR_PERSISTENT=>true));
		return $dblink;

		/*$hostname = 'db732247767.db.1and1.com';
		$database = 'db732247767';
		$username = 'dbo732247767';
		$password = 'Ma-Cha82';

		$dblink = null;
		try {
		  $dblink = new PDO("mysql:host=$hostname; dbname=$database;", $username, $password);
		} catch (PDOException $e) {
		  echo "Erreur!: " . $e->getMessage() . "<br/>";
		  die();
		}
		return $dblink;*/
	}

	public static function getConnection()
	{
		if(isset(self::$dblink)){
			return self::$dblink;
		}else{
			self::$dblink=self::connect();
			return self::$dblink;
		}
	}
	
	public static function update($object, $tableName){
		
		if (null == $object -> getAttr('id')) {
		  throw new Exception(__CLASS__ . ": Primary Key undefined : cannot update");
		} 
		if(!isset($tableName)) {
			throw new Exception(__CLASS__ . ": Table name undefined : cannot update");
		}
		
		$table = self::get_vars($object);
		$last_var = end($table);
		$querry = "UPDATE ".$tableName." SET ";
		$execute = array();
		foreach($table as $key => $val) {
			if($val !== 'id' AND $val !== 'postEngId'){
				$querry .= $val." = :".$val;
				$execute[$val]=$object->getAttr($val);
				if($val !== $last_var){
					$querry .= ", ";
				} 
			}
		}
		$querry .= " WHERE id = :id";
		$execute['id']=$object->getAttr('id');
		
		
		$pdo = self::getConnection(); //$pdo=Base::getConnection(); ?
		$req = $pdo->prepare($querry);
		$req -> execute($execute) or die(print_r($req->errorInfo()));
		
		return $pdo;
	}
	
	public static function insert($object, $tableName){
		
		if(!isset($tableName)) {
			throw new Exception(__CLASS__ . ": Table name undefined : cannot insert");
		}
		
		$table = self::get_vars($object);
		$last_var = end($table);
		$querry = "INSERT INTO ".$tableName." (";
		$execute = array();
		foreach($table as $key=>$val) {
			if($val !== 'id'){
				$querry .= $val;
				if($val == $last_var){
					$querry .= ") ";
				}
				else {
					$querry .= ", ";
				}
			}
		}
		$querry .= "VALUES (";
		foreach($table as $key=>$val) {
			if($val !== 'id'){
				$querry .= ":$val";
				$execute[$val]=$object->getAttr($val);
				if($val == $last_var){
					$querry .= ") ";
				}
				else {
					$querry .= ", ";
				}
			}
		}
		
		$pdo = self::getConnection(); //$pdo=Base::getConnection(); ?
		$req = $pdo->prepare($querry);
		$req->execute($execute) or die(print_r($req->errorInfo()));
		
		return $pdo;
	}
	
	public static function delete($object, $tableName){
		
		if(null ==$object->getAttr('id')) {
			throw new Exception(__CLASS__ . ": Primary Key undefined : cannot delete");
		}
		if(!isset($tableName)) {
			throw new Exception(__CLASS__ . ": Table name undefined : cannot update");
		}
		
		$query="DELETE FROM ".$tableName." WHERE id=:id";
		$execute =array('id' => $object -> getAttr('id'));
		$pdo = self::getConnection();
		$req = $pdo->prepare($query);
		$req->execute($execute);
		$req->closeCursor();
	}
	
	public static function get_vars($object)
	{
		$john = array();
		$table = $object->get_object_vars();
		foreach($table as $key=>$val){
			$john[] = $key;
		}
		return $john;
	}
	
}
?>