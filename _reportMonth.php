<?php

	session_start();
	header('Content-type: text/html; charset=utf-8');
	include_once 'classes/Transaction.class.php';
	
	$year = $_GET['year'];
	$account = $_GET['account'];
	$operation = $_GET['operation'];
	
	$return = array();
	$return['budget'] = array();
	$return['debit'] = array();
	
	for ($i = 1; $i < 13; $i++) {

		$iformat = (($i < 10)?'0':'').$i;
		$iformat2 = ((($i+1) < 10)?'0':'').($i+1);
				
		if ($operation <> 0) {
			
			$debit = (double)Transaction::getTotalGlobal($year.$iformat.'01', $operation, $account);
			$budget = (double)Transaction::getTotalBudgetGlobal($year.$iformat.'01', $operation, $account);
			
			if ($debit <> null) {
				$return['debit'][] = array($iformat . '-01-' . $year, $debit);
			}
			
			if ($budget <> null) {
				$return['budget'][] = array($iformat . '-01-' . $year, $budget);
			}
			
		} else {
			$return['debit'][] = array($iformat . '-01-' . $year, Transaction::getSolde($year.$iformat2.'01', $account));
		}
	}
	
	echo json_encode($return);
?>