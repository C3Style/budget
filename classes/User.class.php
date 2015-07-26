<?php
include_once 'DB.class.php';
class User {

	private $login;
	private $lastName;
	private $firstName;
	
	private static function loadByRow($row) {
		if (!is_null($row)) {
			$user = new User();
			$user->login		= $row['US_LOGIN'];
			$user->lastName		= $row['US_LASTNAME'];
			$user->firstName	= $row['US_FIRSTNAME'];
			return $user;
		}
		return false;
	}
	
	public static function identify($login, $password) {
		
		$sql = "SELECT * FROM user_t WHERE US_LOGIN = ('".$login."') AND US_PASSWORD = md5('".$password."');";
		$result = DB::execute($sql);
		
		if ($result) {
			$row = mysqli_fetch_assoc($result);
			$return = self::loadByRow($row);
			return $return;
		}			
		return false;
	}
	
	public static function updatePassword($login, $newPassword) {
		$sql  = "UPDATE user_t ";
		$sql .= "SET US_PASSWORD = MD5('".$newPassword."') ";
		$sql .= "WHERE US_LOGIN = '".$login."' ";
		$result = DB::execute($sql);
	}	
	
	public function getLogin() {
		return $this->login;
	}
	
	public function getLastName() {
		return $this->lastName;
	}
	
	public function getFirstName() {
		return $this->firstName;
	}
}
?>