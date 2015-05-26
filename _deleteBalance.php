<?php
	session_start();
	include_once 'classes/Balance.class.php';
	
	if (isset($_GET['id']) && isset($_GET['account'])) {
		$balance = Balance::loadByYearAndAccount($_GET['id'], $_GET['account']);
		$balance->delete();
	}
?>