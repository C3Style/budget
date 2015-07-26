<?php
include_once 'DB.class.php';
class Recurrence {

	private $transaction_id;
	private $paid = 0;
	private $month;
	private $year;
	
	private static function loadByRow($row) {
		if (!is_null($row)) {
			$recurrence = new Recurrence();
			$recurrence->transaction_id	= $row['RE_TR_ID'];
			$recurrence->paid			= $row['RE_PAID'];
			$recurrence->month		 	= $row['RE_MONTH'];
			$recurrence->year		 	= $row['RE_YEAR'];
			return $recurrence;
		}
		return false;
	}
		
	public static function loadAllByTransactionIdByYear($transactionId, $year) {
	
		$sql = "SELECT * FROM recurrence_t WHERE RE_TR_ID = ".$transactionId." AND RE_YEAR = ".$year;
		$return = array();
		$result = DB::execute($sql);
		
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$return[] = self::loadByRow($row);
			}
		}			
		return $return;
	}
	
	public function save() {
	
		$sql =  "INSERT INTO `recurrence_t` (`RE_TR_ID`, `RE_PAID`, `RE_MONTH`, `RE_YEAR`) ";
		$sql .= "VALUES (".$this->transaction_id.", ".$this->paid.", ".$this->month.", ".$this->year.");";
		return DB::execute($sql);
	}
	
	public function delete() {
	
		$sql =  "DELETE FROM `recurrence_t` WHERE `RE_TR_ID` = ".$this->transaction_id;
		$result = DB::execute($sql);
		
		if ($result) 
			return $result;
		return false;
	}
	
	public function paidOrUnPaid() {
	
		$sql =  "UPDATE `recurrence_t` SET `RE_PAID` = NOT (`RE_PAID`) ";
		$sql .= "WHERE `RE_TR_ID` = ".$this->transaction_id." AND RE_MONTH = ".$this->month." AND RE_YEAR = ".$this->year;		
		$result = DB::execute($sql);
		
		if ($result) 
			return $result;
		return false;
	}
			
	public function setTransactionId($id) {
		$this->transaction_id = $id;
	}
	
	public function setPaid($paid) {
		$this->paid = $paid;
	}
			
	public function setMonth($month) {
		$this->month = $month;
	}
	
	public function setYear($year) {
		$this->year = $year;
	}
	
	public function getTransactionId() {
		return $this->transaction_id;
	}
	
	public function getPaid() {
		return $this->paid;
	}
	
	public function getMonth() {
		return $this->month;
	}
	
	public function getYear() {
		return $this->year;
	}
}
?>