<?php
	session_start();
	set_time_limit(0);
	include_once 'classes/Transaction.class.php';
		
	$alltransactions = explode(",", $_POST['treeInput']);
	$arrayTransactions = array();
	$account = '';
	$return = true;
		
	// Construct the array
	foreach ($alltransactions as $code) {
		$values = explode("_", $code);
		if ($values[1] <> 0)
			$arrayTransactions[$values[0]][] = $values[1];
		else
			$arrayTransactions[$values[0]] = array();
	}

	foreach ($arrayTransactions as $transactionId => $dates) {
		$transaction = Transaction::loadById($transactionId);
		$account = $transaction->getAccountId();
		$date = $transaction->getDate();
		$transaction->setDate($_POST['yearBudgetDest'].substr($date, 4));
		$transaction->setRecurrence($dates);
		$return = $return && $transaction->save();
	}
		
	if ($account <> '' && $return) 
		header('Location: index.php?page=3&account='.$account.'&tab=3&copy=1');
	else
		header('Location: index.php?page=3&account='.$account.'&tab=3&copy=0');
?>