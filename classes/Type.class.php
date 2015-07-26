<?php
include_once 'DB.class.php';
class Type {

	private $id;
	private $name;
	
	private static function loadByRow($row) {
		if (!is_null($row)) {
			$type = new Type();
			$type->id		= $row['TY_ID'];
			$type->name 	= $row['TY_NAME'];
			return $type;
		}
		return false;
	}
		
	public static function loadAll() {
	
		$sql = "SELECT * FROM type_t";
		$return = array();
		$result = DB::execute($sql);
		
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$return[] = self::loadByRow($row);
			}
		}			
		return $return;
	}
	
	public static function loadById($id) {
	
		$sql = "SELECT * FROM type_t WHERE TY_ID = ".$id;
		$result = DB::execute($sql);
		
		if ($result) {
			$row = mysqli_fetch_assoc($result);
			$return = self::loadByRow($row);
			return $return;
		}			
		return false;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
}
?>