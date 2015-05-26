<?php
	session_start();
	include_once 'classes/Operation.class.php';
	
	if (isset($_GET['name'])) {
		$operation = new Operation();
		$operation->setName($_GET['name']);
		$operation->setAccountId($_GET['account']);
		$result = $operation->save();
		
		$return = array();
		$return['error'] = !$result;
		echo json_encode($return);
	}
?>