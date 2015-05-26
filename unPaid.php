<?php
	session_start();
	
	header('Content-Type: text/html; charset=utf-8');
	
	include_once 'classes/Transaction.class.php';
	include_once 'classes/Operation.class.php';
	include_once 'classes/Account.class.php';
	include_once 'classes/Type.class.php';
	include_once 'classes/Texte.class.php';
	
	setlocale (LC_TIME, 'fr_FR');
	
	$accounts = Account::loadAll();	
	$types = Type::loadAll();		
			
	$date = "";
	if (isset($_GET['date']))
		$date = $_GET['date'];
		
	$account = "";
	if (isset($_GET['account']))
		$account = $_GET['account'];
	$actualAccount = Account::loadById($account);
	
	if (is_object($actualAccount)) {
		$operations = Operation::loadAllByAccount($actualAccount->getAccountId());
	} else {
		$operations = array();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Budget</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script language="javascript" src="scripts/lib.js"></script>
<script language="javascript" src="scripts/unPaid.js"></script>
</head>
<body>
	<h2><?php echo Texte::getText($_SESSION['lang'], 'list_unpaid'); ?> <span style="font-size: 12px;"><?php if($actualAccount) { echo '(' . $actualAccount->getAccountId() . ' - ' . $actualAccount->getAccountName() . ')'; } ?></span></h2>
	<h4><?php echo Texte::getText($_SESSION['lang'], 'credit'); ?></h4>
	<div style="height:150px; overflow:auto;">
		<table width="100%" border="0">	
			<?php

				$credits = Transaction::loadUnPaidCreditOrDebit($date, $account, true);
				
				if (count($credits) != 0) {
					echo '<tr align="center">';
					echo '<th width="72%">'.Texte::getText($_SESSION['lang'], 'operation').'</th>';
					echo '<th width="20%">'.Texte::getText($_SESSION['lang'], 'credit').'</th>';
					echo '<th width="8%">&nbsp;</th>';
					echo '<tr>';
				}
				else
					echo '<tr align="center"><td>'.Texte::getText($_SESSION['lang'], 'aucun_credit_unpaid').'</td></tr>'; 
			
				// **********************************************************************************************************
				// Credit
				// **********************************************************************************************************
				foreach ($credits as $c) {
				
					$billPaid = ($c->isPaid((int)substr($c->getDate(), 5, 2), (int)substr($c->getDate(), 0, 4)))?'':'style="background-color: #FFE88B;"';
					$alt   				= ($billPaid=='')?Texte::getText($_SESSION['lang'], 'unpaid_credit'):Texte::getText($_SESSION['lang'], 'paid_credit');
					$src   				= ($billPaid=='')?'btnUnpaid':'btnPaid';
					$class 				= ($billPaid=='')?'credit':'creditNoPaid';
					$classNoPaid 		= ($billPaid=='')?'':'unpaid';
					$dateWithoutScore 	= str_replace('-', '', $c->getDate());
				
					echo '<tr id="' . $c->getId() . str_replace('-', '', $c->getDate()) . '" class="' . $classNoPaid . '" align="center" '.$billPaid.'>';
					echo '<td align="left">';
					echo '	<a class="link" href="index.php?page=1&date=' . $dateWithoutScore . '&account=' . $account . '">'.strftime('%d/%m/%Y', strtotime($c->getDate())).' : '.(($c->getRemark()!='')?$c->getRemark():'-').'</a>';
					echo '</td>';
					echo '<td class="amount '.$class.'" align="right">'.$c->getAmount().'</td>';
					echo '<td align="center" class="'.$src.'"><a class="tooltip" href="javascript:payOlderTransaction('.$c->getId().', '.$dateWithoutScore.', \''.$account.'\');"><span class="custom info left">'.$alt.'</span></a></td>';
					echo '<tr>';
				}
			?>
		</table>
	</div>
	<h4><?php echo Texte::getText($_SESSION['lang'], 'debit'); ?></h4>
	<div style="height:200px; overflow:auto;">
		<table width="100%" border="0">	
			<?php
					
				$debits = Transaction::loadUnPaidCreditOrDebit($date, $account, false);
				
				if (count($debits) != 0) {
					echo '<tr align="center">';
					echo '<th width="72%">'.Texte::getText($_SESSION['lang'], 'operation').'</th>';
					echo '<th width="20%">'.Texte::getText($_SESSION['lang'], 'debit').'</th>';
					echo '<th width="8%">&nbsp;</th>';
					echo '<tr>';
				}
				else
					echo '<tr align="center"><td>'.Texte::getText($_SESSION['lang'], 'aucun_debit_unpaid').'</td></tr>'; 
			
				// **********************************************************************************************************
				// Debit
				// **********************************************************************************************************
				foreach ($debits as $d) {
				
					$billPaid = ($d->isPaid((int)substr($d->getDate(), 5, 2), (int)substr($d->getDate(), 0, 4)))?'':'style="background-color: #FFE88B;"';
					$alt   				= ($billPaid=='')?Texte::getText($_SESSION['lang'], 'unpaid_debit'):Texte::getText($_SESSION['lang'], 'paid_debit');
					$src   				= ($billPaid=='')?'btnUnpaid':'btnPaid';
					$class 				= ($billPaid=='')?'credit':'creditNoPaid';
					$classNoPaid 		= ($billPaid=='')?'':'unpaid';
					$dateWithoutScore 	= str_replace('-', '', $d->getDate());
					$op 				= Operation::loadById($d->getOperationId());
				
					echo '<tr id="' . $d->getId() . str_replace('-', '', $d->getDate()) . '" class="' . $classNoPaid . '" align="center" '.$billPaid.'>';
					echo '<td align="left">';
						echo '	<a class="link" href="index.php?page=1&date=' . $dateWithoutScore . '&account=' . $account . '">'.strftime('%d/%m/%Y', strtotime($d->getDate())).' : '.$op->getName().($d->getRemark()!=''?' ('.$d->getRemark().')':'').'</a>';
					echo '</td>';
					echo '<td class="amount '.$class.'" align="right">'.$d->getAmount().'</td>';
					echo '<td align="center" class="'.$src.'"><a class="tooltip" href="javascript:payOlderTransaction('.$d->getId().', '.$dateWithoutScore.', \''.$account.'\');"><span class="custom info left">'.$alt.'</span></a></td>';
					echo '<tr>';
				}
			?>
		</table>
	</div>
</body>
</html>