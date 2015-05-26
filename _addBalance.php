<?php
	session_start();
	include_once 'classes/Balance.class.php';
	
	if (isset($_GET['year']) && isset($_GET['balance']) && isset($_GET['account'])) {
		$balance = new Balance();
		$balance->setYear($_GET['year']);
		$balance->setAccount($_GET['account']);
		$balance->setAmount($_GET['balance']);
		$result = $balance->save();
		
		$return = array();
		$return['error'] = !$result;
		echo json_encode($return);
	}
?>