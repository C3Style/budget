<?php
	session_start();
	include_once 'classes/Operation.class.php';
	
	if (isset($_GET['id'])) {
		$operation = Operation::loadById($_GET['id']);
		$operation->delete();
	}
?>