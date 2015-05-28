<?php
	session_start();
	include_once 'classes/Account.class.php';
	include_once 'classes/User.class.php';
	
	if (isset($_POST['password']) && isset($_POST['newPassword']) && isset($_POST['confirmPassword'])) {
		$account = Account::loadById($_POST['account']);
		$user = User::identify($account->getLogin(), $_POST['password']);
		echo $user->getLogin();
		if (is_object($user) && $user->getLogin() != "") {
			User::updatePassword($account->getLogin(), $_POST['newPassword']);
			$status = 1;
		} else {
			$status = 0;
		}
		
		header('Location: index.php?page=3&account='.$account->getAccountId().'&tab=4&status='.$status);
	}
?>