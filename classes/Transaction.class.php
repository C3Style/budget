<?php
include_once 'DB.class.php';
include_once 'Recurrence.class.php';
include_once 'Balance.class.php';
class Transaction {

	private $id;
	private $type_id		= '0';
	private $operation_id	= '0';
	private $account_id		= '0';
	private $recurrence     = array();
	private $date;
	private $formatedDate;
	private $month;
	private $actualRecForYear; // initialize only in the method loadDebiByYearAndOperation and search
	private $amount;
	private $remark;

	private static function loadByRow($row) {
		if (!is_null($row)) {
			$transaction = new Transaction();
			$transaction->id			= $row['TR_ID'];
			$transaction->type_id 		= $row['TR_TY_ID'];
			$transaction->operation_id 	= $row['TR_OP_ID'];
			$transaction->account_id 	= $row['TR_AC_ID'];
			$transaction->recurrence	= Recurrence::loadAllByTransactionIdByYear($row['TR_ID'], substr($row['TR_DATE'], 0, 4));
			$transaction->date		 	= $row['TR_DATE'];
			if (isset($row['RE_MONTH'])) {
				$transaction->formatedDate  = substr($row['TR_DATE'], 8, 2).'/'.($row['RE_MONTH']<10?'0':'').$row['RE_MONTH'].'/'.substr($row['TR_DATE'], 0, 4);
				$transaction->month		 	= $row['RE_MONTH'];
			}
			$transaction->amount 		= $row['TR_AMOUNT'];
			$transaction->remark	 	= $row['TR_REMARK'];
			return $transaction;
		}
		return false;
	}
		
		/*
	public static function loadAll() {
	
		$sql  = "SELECT * FROM transaction_t ";
		$sql .= "WHERE TR_LOGIN = '".$_SESSION['user']."' ";
		$return = array();
		$result = DB::execute($sql);
		
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$return[] = self::loadByRow($row);
			}
		}			
		return $return;
	}
	*/
	
	public static function loadById($id) {
	
		$sql  = "SELECT * FROM transaction_t WHERE TR_ID = ".$id." ";
		//$sql .= "AND TR_LOGIN = '".$_SESSION['user']."' ";
		$result = DB::execute($sql);
		
		if ($result) {
			$row = mysqli_fetch_assoc($result);
			$return = self::loadByRow($row);
			return $return;
		}			
		return false;
	}
	
	public static function loadBudgetByMonth($date, $account) {
		
		$sql  = "SELECT * FROM transaction_t, recurrence_t, operation_t WHERE TR_ID = RE_TR_ID AND TR_TY_ID = 3 ";
		$sql .= "AND RE_MONTH = ".((int)substr($date, 4, 2))." AND RE_YEAR = ".((int)substr($date, 0, 4))." ";
		//$sql .= "AND TR_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "AND TR_AC_ID = '".$account."' ";
		$sql .= "AND TR_OP_ID = OP_ID ";
		$sql .= "ORDER BY OP_NAME ";
		$return = array();
		$result = DB::execute($sql);
		
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$return[] = self::loadByRow($row);
			}
			return $return;
		}			
		return false;
	}
	
	public static function loadYearBudget($date, $account) {
		
		$sql  = "SELECT * FROM transaction_t WHERE TR_TY_ID = 3 ";
		$sql .= "AND YEAR(TR_DATE) = ".substr($date, 0, 4)." ";
		$sql .= "AND TR_ID NOT IN (SELECT RE_TR_ID FROM recurrence_t)";
		//$sql .= "AND TR_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "AND TR_AC_ID = '".$account."' ";
		$return = array();
		$result = DB::execute($sql);
				
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$return[] = self::loadByRow($row);
			}
			return $return;
		}			
		return false;
	}
	
	public static function loadNonBudgetByMonth($date, $account) {
		
		$sql  = "SELECT * FROM transaction_t, recurrence_t WHERE TR_ID = RE_TR_ID AND TR_TY_ID = 1 ";
		$sql .= "AND (RE_MONTH = ".((int)substr($date, 4, 2)).") ";
		$sql .= "AND (RE_YEAR = ".((int)substr($date, 0, 4)).") ";
		$sql .= "AND TR_OP_ID NOT IN ( SELECT TR_OP_ID FROM transaction_t, recurrence_t ";
		$sql .= "					   WHERE TR_ID = RE_TR_ID AND TR_TY_ID = 3 ";
		$sql .= "					   AND RE_MONTH = ".((int)substr($date, 4, 2))." ";
		$sql .=	"					   AND RE_YEAR = ".((int)substr($date, 0, 4))." ";
		//$sql .= "					   AND TR_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "					   AND TR_AC_ID = '".$account."' )";
		//$sql .= "AND TR_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "AND TR_AC_ID = '".$account."' ";
		$sql .= "ORDER BY DAY(TR_DATE) ";
		$return = array();
		$result = DB::execute($sql);
				
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$return[] = self::loadByRow($row);
			}
			return $return;
		}			
		return false;
	}
	
	public static function loadCreditByMonth($date, $account) {
		
		$sql  = "SELECT * FROM transaction_t, recurrence_t WHERE TR_ID = RE_TR_ID AND TR_TY_ID = 2 ";
		$sql .= "AND (RE_MONTH = ".((int)substr($date, 4, 2)).") ";
		$sql .= "AND (RE_YEAR = ".((int)substr($date, 0, 4)).") ";
		//$sql .= "AND TR_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "AND TR_AC_ID = '".$account."' ";
		$sql .= "ORDER BY DAY(TR_DATE) ";
		$return = array();
		$result = DB::execute($sql);
				
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				$return[] = self::loadByRow($row);
			}
			return $return;
		}			
		return false;
	}
	
	public static function loadDebiByMonthAndOperation($date, $operationId, $account, $isNotInReport = true, $transactionType = 1) {
		
		$sql  = "SELECT * FROM transaction_t, recurrence_t WHERE TR_ID = RE_TR_ID AND TR_OP_ID = ".$operationId." AND TR_TY_ID = ".$transactionType." ";
		
		// For the report page, the where clause for the month does not exist
		if ($isNotInReport)
			$sql .= "AND (RE_MONTH = ".((int)substr($date, 4, 2)).") ";
			
		$sql .= "AND (RE_YEAR = ".((int)substr($date, 0, 4)).") ";
		//$sql .= "AND TR_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "AND TR_AC_ID = '".$account."' ";
		$sql .= "ORDER BY MONTH(TR_DATE), DAY(TR_DATE) ";
		$return = array();
		$result = DB::execute($sql);
		
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
			
				$t = self::loadByRow($row);
				
				// in the report, we have to know which year and month from the recurrence_t it is
				if (!$isNotInReport)
					$t->actualRecForYear = $row['RE_YEAR'].($row['RE_MONTH']<10?'0':'').$row['RE_MONTH'].'01';
					
				$return[] = $t;
			}
			return $return;
		}			
		return false;
	}
	
	public static function loadUnPaidCreditOrDebit($date, $account, $isCredit) {
		
		If ($isCredit) $typeId = 2; else $typeId = 1;
		
		$sql  = "SELECT * FROM transaction_t, recurrence_t WHERE TR_ID = RE_TR_ID AND TR_TY_ID = " . $typeId . " ";
		$sql .= "AND (RE_MONTH < ".((int)substr($date, 4, 2)).") ";
		$sql .= "AND (RE_YEAR = ".((int)substr($date, 0, 4)).") ";
		$sql .= "AND TR_AC_ID = '".$account."' ";
		$sql .= "AND RE_PAID = 0 ";
		$sql .= "ORDER BY RE_MONTH, DAY(TR_DATE), TR_OP_ID ";
		$return = array();
		$result = DB::execute($sql);

		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {
				
				// all oldest transaction are loading. Date must be updated with the oldest date to know wich date it is
				if (!is_null($row)) {
					$row['TR_DATE'] = $row['RE_YEAR'].'-'.($row['RE_MONTH']<10?'0':'').$row['RE_MONTH'].'-'.substr($row['TR_DATE'], 8, 2);
				}
			
				$return[] = self::loadByRow($row);
			}
			return $return;
		}			
		return false;
	}
	
	public static function getTotalGlobal ($date, $operationId, $account, $isNotInReport = true, $onlyPaid = true) {
		
		$sql  = "SELECT SUM(TR_AMOUNT) as S FROM transaction_t, recurrence_t WHERE TR_ID = RE_TR_ID ";
		$sql .= "AND TR_OP_ID = ".$operationId." AND TR_TY_ID = 1 ";
		
		// 2012.01.16 : add parameter to take all row
		if ($onlyPaid)
			$sql .= "AND RE_PAID = 1 ";
		
		// For the report page, the where clause for the month does not exist
		if ($isNotInReport)
			$sql .= "AND (RE_MONTH = ".((int)substr($date, 4, 2)).") ";
			
		$sql .= "AND (RE_YEAR = ".((int)substr($date, 0, 4)).") ";
		//$sql .= "AND TR_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "AND TR_AC_ID = '".$account."' ";
		$sql .= "GROUP BY TR_OP_ID ";
		$return = array();
		$result = DB::execute($sql);
				
		if ($result) {
			$row = mysqli_fetch_assoc($result);
			return $row['S'];
		}			
		return false;
	}
	
	public static function getTotalBudgetGlobal ($date, $operationId, $account, $isNotInReport = true) {
		
		$sql  = "SELECT SUM(TR_AMOUNT) as S FROM transaction_t, recurrence_t WHERE TR_ID = RE_TR_ID ";
		$sql .= "AND TR_OP_ID = ".$operationId." AND TR_TY_ID = 3 ";
		
		// For the report page, the where clause for the month does not exist
		if ($isNotInReport)
			$sql .= "AND (RE_MONTH = ".((int)substr($date, 4, 2)).") ";
			
		$sql .= "AND (RE_YEAR = ".((int)substr($date, 0, 4)).") ";
		//$sql .= "AND TR_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "AND TR_AC_ID = '".$account."' ";
		$sql .= "GROUP BY TR_OP_ID ";
		$return = array();
		$result = DB::execute($sql);
				
		if ($result) {
			$row = mysqli_fetch_assoc($result);
			return $row['S'];
		}			
		return false;
	}
	
	public static function getSolde ($date, $account) {
		
		$totalDebit = 0;
		$totalCredit = 0;
		
		$balance = Balance::loadByYearAndAccount(substr($date, 0, 4), $account);
		
		$sql  = "SELECT SUM(TR_AMOUNT) as S FROM transaction_t, recurrence_t WHERE TR_ID = RE_TR_ID ";
		$sql .= "AND TR_TY_ID = 1 AND RE_PAID = 1 ";
		$sql .= "AND (RE_MONTH < ".((int)substr($date, 4, 2)).") ";
		$sql .= "AND (RE_YEAR = ".((int)substr($date, 0, 4)).") ";
		//$sql .= "AND TR_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "AND TR_AC_ID = '".$account."' ";
		$sql .= "GROUP BY TR_TY_ID ";
		$return = array();
		$result = DB::execute($sql);
				
		if ($result) {
			$row = mysqli_fetch_assoc($result);
			$totalDebit = $row['S'];
		}

		$totalCredit = self::getCredit($date, 1, $account);
		
		$amount = $balance != null ? $balance->getAmount() : 0;
		return $amount + $totalCredit - $totalDebit;
	}
	
	// paid : We want the credit with only received credit
	public static function getCredit($date, $paid, $account) {
		
		$sql  = "SELECT SUM(TR_AMOUNT) as S FROM transaction_t, recurrence_t WHERE TR_TY_ID = 2 ";
		$sql .= "AND TR_ID = RE_TR_ID ";
		$sql .= ($paid?"AND RE_PAID = 1 ":"");
		$sql .= "AND (RE_MONTH < ".((int)substr($date, 4, 2)).") ";
		$sql .= "AND (RE_YEAR = ".substr($date, 0, 4).") ";
		//$sql .= "AND TR_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "AND TR_AC_ID = '".$account."' ";
		$sql .= "GROUP BY TR_TY_ID ";
		$return = array();
		$result = DB::execute($sql);
				
		if ($result) {
			$row = mysqli_fetch_assoc($result);
			return $row['S'];
		}
		return false;
	}
	
	public static function getEstimatedSolde($date, $account, $typeId = 3) {
		
		$sql  = "SELECT SUM(TR_AMOUNT) as S FROM transaction_t, recurrence_t WHERE TR_ID = RE_TR_ID ";
		$sql .= "AND TR_TY_ID = " . $typeId . " ";
		$sql .= "AND (RE_MONTH < ".((int)substr($date, 4, 2)).") ";
		$sql .= "AND (RE_YEAR = ".((int)substr($date, 0, 4)).") ";
		//$sql .= "AND TR_LOGIN = '".$_SESSION['user']."' ";
		$sql .= "AND TR_AC_ID = '".$account."' ";
		$sql .= "GROUP BY TR_TY_ID ";
		$return = array();
		$result = DB::execute($sql);
				
		if ($result) {
			$row = mysqli_fetch_assoc($result);
			return $row['S'];
		}			
		return false;
	}
	
	public static function search($typeId, $operationId, $remark, $beginDate, $endDate, $account) {
		
		$formatCorrectDate = "CONCAT(YEAR(TR_DATE), CONCAT('-', CONCAT(IF(RE_MONTH<10,'0',''), CONCAT(RE_MONTH, CONCAT('-', CONCAT(IF(DAY(TR_DATE)<10,'0',''), DAY(TR_DATE)))))))";
		
		// Load annual budget
		$sql  = "SELECT TR_ID, TR_TY_ID, TR_OP_ID, TR_AMOUNT, TR_REMARK, TR_LOGIN, TR_AC_ID, YEAR(TR_DATE) AS RE_YEAR, MONTH(TR_DATE) AS RE_MONTH, TR_DATE, 1 AS IS_ANNUAL ";
		$sql .= "FROM transaction_t WHERE ";
		$sql .= "TR_ID NOT IN (SELECT RE_TR_ID FROM recurrence_t) ";
		
		if ($typeId > 0)
			$sql .= "AND TR_TY_ID = ".$typeId." ";
		if ($operationId > 0)
			$sql .= "AND TR_OP_ID = ".$operationId." ";
		if ($beginDate != null)
			$sql .= "AND TR_DATE >= '".$beginDate."' ";
		if ($endDate != null)
			$sql .= "AND TR_DATE <= '".$endDate."' ";
		if ($remark != null)
			$sql .= "AND TR_REMARK LIKE '%".$remark."%' ";
		
		$sql .= "AND TR_AC_ID = '".$account."' ";
		$sql .= "UNION ALL ";
		
		// Load others
		$sql .= "SELECT TR_ID, TR_TY_ID, TR_OP_ID, TR_AMOUNT, TR_REMARK, TR_LOGIN, TR_AC_ID, RE_YEAR, RE_MONTH, ";
		$sql .= ($formatCorrectDate . " AS TR_DATE, 0 AS IS_ANNUAL ");
		$sql .= "FROM transaction_t, recurrence_t WHERE TR_ID = RE_TR_ID ";

		if ($typeId > 0)
			$sql .= "AND TR_TY_ID = ".$typeId." ";
		if ($operationId > 0)
			$sql .= "AND TR_OP_ID = ".$operationId." ";
		if ($beginDate != null)
			$sql .= "AND " . $formatCorrectDate . " >= '".$beginDate."' ";
		if ($endDate != null)
			$sql .= "AND " . $formatCorrectDate . " <= '".$endDate."' ";
		if ($remark != null)
			$sql .= "AND TR_REMARK LIKE '%".$remark."%' ";
			
		$sql .= "AND TR_AC_ID = '".$account."' ";
		$sql .= "ORDER BY TR_DATE ";

		$return = array();
		$result = DB::execute($sql);
				
		if ($result) {
			while ($row = mysqli_fetch_assoc($result)) {		
			
				$t = self::loadByRow($row);
				if ($row['IS_ANNUAL'])
					$t->actualRecForYear = $row['RE_YEAR'];
				else
					$t->actualRecForYear = $row['RE_YEAR'].($row['RE_MONTH']<10?'0':'').$row['RE_MONTH'].'01';
			
				$return[] = $t;
			}
			return $return;
		}			
		return false;
	}
	
	public function save() {
	
		$sql =  "INSERT INTO `transaction_t` (`TR_ID`, `TR_TY_ID`, `TR_OP_ID`, `TR_DATE`, `TR_AMOUNT`, `TR_REMARK`, `TR_LOGIN`, `TR_AC_ID`) ";
		$sql .= "VALUES ('', ".$this->type_id.", ".$this->operation_id.", '".$this->date."', ".$this->amount.", '".addslashes($this->remark)."', '".$_SESSION['user']."', '".$this->account_id."')";
		$id = DB::execute($sql, true);
		
		$result = true;
		
		foreach ($this->recurrence as $month) {
			$recurrence = new Recurrence();
			$recurrence->setTransactionId($id);
			$recurrence->setMonth($month);
			$date = str_replace('-', '', $this->date);
			$recurrence->setYear(substr($date, 0, 4));
			$result = $recurrence->save();
		}
		
		if ($id && $result)
			return $id;
		return false;
	}
	
	public function update() {

		$sql =  "UPDATE `transaction_t` SET `TR_TY_ID` = ".$this->type_id.", `TR_OP_ID` = ".$this->operation_id.", `TR_DATE` = '".$this->date."', ";
		$sql .= "`TR_AMOUNT` = ".$this->amount.", `TR_REMARK` = '".addslashes($this->remark)."' ";
		$sql .= "WHERE `TR_ID` = ".$this->id;		
		$result = DB::execute($sql);
		
		$date = str_replace('-', '', $this->date);
		
		// -----------------------------
		// Keep a trace of the paid value
		$paid = Recurrence::loadAllByTransactionIdByYear($this->id, substr($date, 0, 4));
		$paidArray = array();
		foreach ($paid as $recurrence) 
			$paidArray[$recurrence->getMonth()] = $recurrence->getPaid();
		// -----------------------------
		
		$recurrence = new Recurrence();
		$recurrence->setTransactionId($this->id);
		$result = $result && $recurrence->delete();
		
		foreach ($this->recurrence as $month) {
			$recurrence = new Recurrence();
			$recurrence->setTransactionId($this->id);

			// -----------------------------
			if (isset($paidArray[$month]))
				$recurrence->setPaid($paidArray[$month]);
			// -----------------------------
			
			$recurrence->setMonth($month);
			$recurrence->setYear(substr($date, 0, 4));
			$result = $recurrence->save();
		}
		
		if ($result) 
			return $result;
		return false;
	}
	
	public function delete() {
		
		$recurrence = new Recurrence();
		$recurrence->setTransactionId($this->id);
		$result = $recurrence->delete();
		
		$sql = "DELETE FROM `transaction_t` WHERE `TR_ID` = ".$this->id;
		$result = $result && DB::execute($sql);
		
		if ($result) 
			return $result;
		return false;
	}
			
	public function monthExist($month, $year) {
		foreach ($this->recurrence as $r)
			if ($r->getMonth() == $month && $r->getYear() == $year)
				return true;
				
		return false;
	}
	
	public static function operationExist($operation_id) {
		
		$sql  = "SELECT count(*) as COUNT FROM transaction_t WHERE TR_OP_ID = ".$operation_id;
		// Normaly, it the operation is for every one 
		// $sql .= "AND TR_LOGIN = '".$_SESSION['user']."' ";
		$return = array();
		$result = DB::execute($sql);
				
		if ($result) {
			$row = mysqli_fetch_assoc($result);
			return ($row['COUNT'] > 0);
		}		

		// it's an error, the opreation can't be deleted
		return true;
	}
	
	public function isPaid($month, $year) {
		foreach ($this->recurrence as $r) 
			if ($r->getMonth() == $month && $r->getYear() == $year) 
				return $r->getPaid();
				
		return false;
	}
	
	public function setPaid($month, $year) {
		foreach ($this->recurrence as $r) 
			if ($r->getMonth() == $month && $r->getYear() == $year) 
				return $r->paidOrUnPaid();
				
		return false;
	}
			
	public function setId($id) {
		$this->id = $id;
	}
			
	public function setTypeId($type_id) {
		$this->type_id = $type_id;
	}
	
	public function setOperationId($operation_id) {
		$this->operation_id = $operation_id;
	}
	
	public function setAccountId($account_id) {
		$this->account_id = $account_id;
	}
	
	public function setRecurrence($recurrence) {
		$this->recurrence = $recurrence;
	}
	
	public function setDate($date) {
		$this->date = $date;
	}
	
	public function setAmount($amount) {
		$this->amount = $amount;
	}
	
	public function setStatusId($status_id) {
		$this->status_id = $status_id;
	}
	
	public function setRemark($remark) {
		$this->remark = $remark;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getTypeId() {
		return $this->type_id;
	}
	
	public function getOperationId() {
		return $this->operation_id;
	}
	
	public function getAccountId() {
		return $this->account_id;
	}
	
	public function getRecurrence() {
		return $this->recurrence;
	}
	
	public function getDate($isFR = false) {
		if ($isFR && $this->date != '')
			return substr($this->date, 8, 2) . "/" . substr($this->date, 5, 2) . "/" . substr($this->date, 0, 4);
		else
			return $this->date;
	}
	
	public function getFormatedDate() {
		return $this->formatedDate;
	}
	
	public function getMonth() {
		return $this->month;
	}
	
	public function getActualRecForYear() {
		return $this->actualRecForYear;
	}
	
	public function getAmount() {
		return $this->amount;
	}
	
	public function getStatusId() {
		return $this->status_id;
	}
	
	public function getRemark() {
		return $this->remark;
	}
}
?>