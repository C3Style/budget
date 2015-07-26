<?php
include_once 'DB.class.php';
class Balance {

	private $year;
	private $account;
	private $amount;
	
	private static function loadByRow($row) {
		if (!is_null($row)) {
			$balance = new Balance();
			$balance->year		= $row['BA_YEAR'];
			$balance->account	= $row['BA_AC_ID'];
			$balance->amount	= $row['BA_AMOUNT'];
			return $balance;
		}
		return false;
	}
		
	public static function loadAllByAccount($account) {
	
		$sql  = "SELECT * FROM balance_t, account_t ";
		$sql .= "WHERE AC_ID = BA_AC_ID ";
		$sql .= "AND AC_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "AND BA_AC_ID = '".$account."' ";
		$sql .= "ORDER BY BA_YEAR, BA_AC_ID ";
		$return = array();
		$result = DB::execute($sql);
		
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$return[] = self::loadByRow($row);
			}
		}			
		return $return;
	}
	
	public static function loadByYearAndAccount($year, $account) {
	
		$sql  = "SELECT * FROM balance_t WHERE BA_YEAR = ".$year." ";
		$sql .= "AND BA_AC_ID = '".$account."' ";
		//$sql .= "AND BA_LOGIN = '".$_SESSION['user']."' ";
		$result = DB::execute($sql);
		
		if ($result) {
			$row = mysqli_fetch_assoc($result);
			$return = self::loadByRow($row);
			return $return;
		}			
		return false;
	}
	
	public function save() {
	
		$sql =  "INSERT INTO `balance_t` (`BA_YEAR`, `BA_AMOUNT`, `BA_AC_ID`, `BA_LOGIN`) ";
		$sql .= "VALUES (".$this->year.", ".$this->amount.", '".$this->account."', '".$_SESSION['user']."') ";
		$result = DB::execute($sql, false);
		return $result;
	}
	
	public function delete() {
	
		$sql  =  "DELETE FROM `balance_t` WHERE `BA_YEAR` = ".$this->year." ";
		$sql .=  "AND `BA_AC_ID` = '".$this->account."' ";
		//$sql .=  "AND `BA_LOGIN` = '".$_SESSION['user']."' ";
		$result = DB::execute($sql);
		
		if ($result) 
			return $result;
		return false;
	}
	
	public function getYear() {
		return $this->year;
	}
	
	public function getAccount() {
		return $this->account;
	}
	
	public function getAmount() {
		return $this->amount;
	}
	
	public function setYear($year) {
		$this->year = $year;
	}
	
	public function setAccount($account) {
		$this->account = $account;
	}
	
	public function setAmount($amount) {
		$this->amount = $amount;
	}
}
?>