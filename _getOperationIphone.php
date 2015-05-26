<?php
	session_start();
	header('Content-type: text/html; charset=utf-8');
	include_once 'classes/Operation.class.php';
	
	if (isset($_GET['account'])) {
		$operations = Operation::loadAllByAccountForIphone($_GET['account']);
		echo json_encode($operations);
	}
?>