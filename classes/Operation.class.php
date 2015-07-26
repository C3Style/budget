<?php
include_once 'DB.class.php';
class Operation {

	private $id;
	private $account_id;
	private $name;

	private static function loadByRow($row) {
		if (!is_null($row)) {
			$operation = new Operation();
			$operation->id			= $row['OP_ID'];
			$operation->account_id	= $row['OP_AC_ID'];
			$operation->name 		= $row['OP_NAME'];
			return $operation;
		}
		return false;
	}
		
	public static function loadAllByAccount($account_id) {
	
		$sql  = "SELECT * FROM operation_t ";
		$sql .= "WHERE OP_AC_ID = '".$account_id."' ";
		$sql .= "AND OP_ID > 0 ";
		$sql .= "ORDER BY OP_NAME ";
		$return = array();
		$result = DB::execute($sql);
		
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$return[] = self::loadByRow($row);
			}
		}			
		return $return;
	}
	
	public static function loadAllByAccountForIphone($account_id) {
	
		$sql  = "SELECT * FROM operation_t ";
		$sql .= "WHERE OP_AC_ID = '".$account_id."' ";
		$sql .= "AND OP_ID > 0 ";
		$sql .= "ORDER BY OP_NAME ";
		$return = array();
		$result = DB::execute($sql);
		
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				if (!is_null($row)) {
					$operation = array();
					$operation['id']			= $row['OP_ID'];
					$operation['account_id']	= $row['OP_AC_ID'];
					$operation['name'] 			= utf8_encode($row['OP_NAME']);
					$return[] = $operation;
				}
			}
		}			
		return $return;
	}
	
	public static function loadById($id) {
	
		$sql  = "SELECT * FROM operation_t WHERE OP_ID = ".$id." ";
		$result = DB::execute($sql);
		
		if ($result) {
			$row = mysqli_fetch_assoc($result);
			$return = self::loadByRow($row);
			return $return;
		}			
		return false;
	}
	
	public static function loadByNameAndAccount($name, $account_id) {
	
		$sql = "SELECT * FROM operation_t WHERE OP_NAME = '".$name."' ";
		$sql .= "AND OP_AC_ID = '".$account_id."' ";
		$result = DB::execute($sql);
	
		if ($result) {
			$row = mysqli_fetch_assoc($result);
			$return = self::loadByRow($row);
			return $return;
		}			
		return false;
	}
	
	public function save() {
	
		// The object with the name name already exist
		$op = Operation::loadByNameAndAccount($this->name, $this->account_id);
		if ($op != null && $op->getId() > 0)
			return false;
	
		$sql =  "INSERT INTO `operation_t` (`OP_ID`, `OP_AC_ID`, `OP_NAME`, `OP_LOGIN`) ";
		$sql .= "VALUES ('', '".$this->account_id."', '".$this->name."', '".$_SESSION['user']."') ";
		$result = DB::execute($sql, true);
		
		if ($result)
			return $result;
		else
			return false;
	}
	
	public function delete() {
	
		$sql =  "DELETE FROM `operation_t` WHERE `OP_ID` = ".$this->id;
		$result = DB::execute($sql);
		
		if ($result) 
			return $result;
		return false;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getAccountId() {
		return $this->account_id;
	}
	
	public function setAccountId($account_id) {
		$this->account_id = $account_id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
}
?>