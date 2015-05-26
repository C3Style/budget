<?php
	session_start();
	include_once 'classes/Transaction.class.php';
	
	if (isset($_GET['id'])) {
		$transaction = Transaction::loadById($_GET['id']);	
		$date = $_GET['date'];
		$transaction->setPaid(substr($date, 4, 2), substr($date, 0, 4));
	}
?>