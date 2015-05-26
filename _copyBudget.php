<?php 

	session_start();

	include_once 'classes/Operation.class.php';
	include_once 'classes/Transaction.class.php';
	include_once 'classes/Balance.class.php';
	include_once 'classes/Account.class.php';
	include_once 'classes/Texte.class.php';
	setlocale (LC_TIME, 'fr_FR');

	$account = $_GET['account'];
	$year = $_GET['year'];
	$firstDateFirstMonth = '0101';

	$operations = Operation::loadAllByAccount($account);
	$yearBudget = Transaction::loadYearBudget($year.$firstDateFirstMonth, $account);
	
	echo '<ul>';
	
	foreach($operations as $op) {
		$budgets = Transaction::loadDebiByMonthAndOperation($year.$firstDateFirstMonth, $op->getId(), $account, false, 3);
		echo '<li id="_op'.$op->getId().'" class="folder">'.$op->getName();
		echo '<ul>';
		if (count($budgets) > 0) {
		
			echo '<li id="_bm'.$op->getId().'" class="folder">' . Texte::getText($_SESSION['lang'], 'budget_mensuel');
			echo '<ul>';
			foreach ($budgets as $b) {
				$iformat = (($b->getMonth() < 10)?'0':'').$b->getMonth();
				echo '<li id="'.$b->getId().'_'.$b->getMonth().'" class="">'.htmlentities(strftime('%B', strtotime($year.$iformat .'01')));
			}
			echo '</ul>';
			
			$debits = Transaction::loadDebiByMonthAndOperation($year.$firstDateFirstMonth, $op->getId(), $account, false);
			if (count($debits) > 0) {
				echo '<li id="_de'.$op->getId().'" class="folder">' . Texte::getText($_SESSION['lang'], 'debit');
				echo '<ul>';
				foreach ($debits as $d) {
					echo '<li id="'.$d->getId().'_'.$d->getMonth().'" class="">'.$d->getFormatedDate().' : '.(($d->getRemark()!="")?$d->getRemark():'-');
				}
				echo '</ul>';
			}
		}
		
		$tId = operationIsAnual($op->getId(), $yearBudget);
		
		if ($tId > 0) {
			echo '<li id="'.$tId.'_0" class="">' . Texte::getText($_SESSION['lang'], 'budget_annuel');
			
			$nonBudget = Transaction::loadDebiByMonthAndOperation($year.$firstDateFirstMonth, $op->getId(), $account, false);
			if (count($nonBudget) > 0) {
				echo '<li id="_de'.$op->getId().'" class="folder">' . Texte::getText($_SESSION['lang'], 'debit');
				echo '<ul>';
				foreach ($nonBudget as $nb) {
					echo '<li id="'.$nb->getId().'_'.$nb->getMonth().'" class="">'.$nb->getFormatedDate().' : '.(($nb->getRemark()!="")?$nb->getRemark():'-');
				}
				echo '</ul>';
			}
		}
		echo '</ul>';
	}
	
	echo '</ul>';
	
	function operationIsAnual($operationId, $transactionArray) {
		foreach ($transactionArray As $transaction)
			if ($transaction->getOperationId() == $operationId)
				return $transaction->getId();
		return 0;
	}
?>