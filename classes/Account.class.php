<?php
include_once 'DB.class.php';
class Account {

	private $login;
	private $accountId;
	private $accountName;
	private $defaultAcc;
	
	private static function loadByRow($row) {
		if (!is_null($row)) {
			$account = new Account();
			$account->login			= $row['AC_LOGIN'];
			$account->accountId		= $row['AC_ID'];
			$account->accountName	= $row['AC_NAME'];
			$account->defaultAcc		= $row['AC_DEFAULT'];
			return $account;
		}
		return false;
	}
	
	public static function loadAll() {
		$sql  = "SELECT * FROM account_t ";
		$sql .= "WHERE AC_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "ORDER BY AC_ID ";
		$return = array();
		$result = DB::execute($sql);
		
		if ($result) {
			while ($row = mysql_fetch_assoc($result)) {
				$return[] = self::loadByRow($row);
			}
		}			
		return $return;
	}	
	
	public static function loadById($id) {
		$sql  = "SELECT * FROM account_t ";
		$sql .= "WHERE AC_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "AND AC_ID = '".$id."' ";
		$sql .= "ORDER BY AC_ID ";
		$result = DB::execute($sql);
		$row = mysql_fetch_assoc($result);
		
		if ($row != '')
			return self::loadByRow($row);
		else
			return false;
	}	
	
	public static function loadDefaultAccount() {
		$sql  = "SELECT * FROM account_t ";
		$sql .= "WHERE AC_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "AND AC_DEFAULT = 1 ";
		$sql .= "ORDER BY AC_ID ";
		$result = DB::execute($sql);
		$row = mysql_fetch_assoc($result);
		
		if ($row != '')
			return self::loadByRow($row);
		else
			return false;
	}
	
	public function getLogin() {
		return $this->login;
	}
	
	public function getAccountId() {
		return $this->accountId;
	}
	
	public function getAccountName() {
		return $this->accountName;
	}
	
	public function isDefault() {
		return $this->defaultAcc;
	}
}
?>