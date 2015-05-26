<?php
	session_start();
	include_once 'classes/Transaction.class.php';
	
	if (isset($_GET['id'])) {
		$transaction = Transaction::loadById($_GET['id']);	
		$transaction->delete();
	}
?>