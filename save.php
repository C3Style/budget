<?php
	session_start();
	include_once 'classes/Transaction.class.php';
		
	// save data
	if (isset($_POST['type'])) {
		
		$transaction = new Transaction();
		
		$transaction->setTypeId($_POST['type']);
		$transaction->setAccountId(str_replace("'", "", $_POST['acutalAccount']));
		$transaction->setAmount(str_replace("'", "", $_POST['amount']));
		$transaction->setRemark($_POST['remark']);
		
		$recurrence = array();
		for ($i = 1; $i < 13; $i++)
			if (isset($_POST['rec'.$i]))
				$recurrence[] = $i;
		$transaction->setRecurrence($recurrence);
		
		switch($_POST['type'])  {
			case "1"  : // Débit
				$transaction->setOperationId($_POST['operation']);
				$transaction->setDate(substr($_POST['date'], 6, 4) . "-" . substr($_POST['date'], 3, 2) . "-" . substr($_POST['date'], 0, 2));
				$returnDate = $transaction->getDate();
				$returnPage = 1;
				break;
			case "2"  : // Crédit
				$transaction->setDate(substr($_POST['date'], 6, 4) . "-" . substr($_POST['date'], 3, 2) . "-" . substr($_POST['date'], 0, 2));
				$returnDate = $transaction->getDate();
				$returnPage = 1;
				break;
			case "3" : // Budget
				$transaction->setOperationId($_POST['operation']);
				$transaction->setDate($_POST['year'].'-01-01');
				
				if (empty($recurrence)) {
					$returnDate = $transaction->getDate();
					$returnPage = 2;
				}
				else {
					$month = (($recurrence[0]<10)?"0":"").$recurrence[0];
					$returnDate = $_POST['year'].$month.'01';
					$returnPage = 1;
				}
				
				break;
		}
		
		// It's an update
		if ($_POST['id'] != '') {
			$transaction->setId($_POST['id']);
			$return = $transaction->update();
		}
		else
			$return = $transaction->save();
		
		$returnDate = str_replace('-', '', $returnDate);
		
		if ($return)
			header('Location: index.php?page='.$returnPage.'&date='.$returnDate.'&account='.$_POST['acutalAccount'].'&action=1');
		else
			header('Location: error.php');
	}
?>