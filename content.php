<?php 
	include_once 'classes/Transaction.class.php';
	include_once 'classes/Operation.class.php';
	include_once 'classes/Account.class.php';
	setlocale (LC_TIME, 'fr_FR');
?>
<script language="javascript" src="scripts/content.js"></script>
<h2 align="center"><?php echo Texte::getText($_SESSION['lang'], 'transac_mensuelle'); ?></h2>
<script language="javascript">
	$(function(){
		$('select#account').selectmenu({style:'dropdown', maxHeight: 300});
	});
</script>	
<table width="100%" border="0" align="center">
	<tr>
		<td width="35%">
			<table><tr>
			<?php 
			$credits = Transaction::loadUnPaidCreditOrDebit($date, $account, true);
			$debits = Transaction::loadUnPaidCreditOrDebit($date, $account, false);
			
			$month = (int)strftime('%m', strtotime($date));
			$year = (int)strftime('%Y', strtotime($date));
			
			if (count($credits) <> 0 || count($debits) <> 0) { ?>
				<td class="btnEye">
				<a class="tooltip" href="javascript:previewUnpaid('<?php echo $date; ?>', '<?php echo $account; ?>');">
					<span class="custom info right"><?php echo Texte::getText($_SESSION['lang'], 'voir_unpaid'); ?></span>
				</a>
				</td>
				&nbsp;&nbsp;
			<?php } ?>
			<td class="btnPrint">
			<a class="tooltip" href="printPDF.php?date=<?php echo date("Ymd", mktime(0,0,0, $month, 1, $year)).'&account='.$account; ?>" target="_blank">
				<span class="custom info right"><?php echo Texte::getText($_SESSION['lang'], 'print_decompte'); ?></span>
			</a>
			</td></tr></table>
		</td>
		<td class="previous" align="left">
			<?php 
				echo '<a class="tooltip" href="index.php?page=1&date='.date("Ymd", mktime(0,0,0, $month - 1, 1, $year)).'&account='.$account.'">';
				echo '<span class="custom info right">'.Texte::getText($_SESSION['lang'], 'access_to_mois').' '.htmlentities(strftime('%B %Y', mktime(0,0,0, $month - 1, 1, $year))).'.</span></a>'; 
			?>
		</td>
		<td align="center">
			<input name="startDate" id="startDate" class="date-picker" style='border: 0px;' readonly='true' value='<?php echo htmlentities(strftime('%B %Y', strtotime($date))); ?>'/>
		</td>
		<td class="next" align="right">
			<?php echo '<a class="tooltip" href="index.php?page=1&date='.date("Ymd", mktime(0,0,0, $month + 1, 1, $year)).'&account='.$account.'">';
				  echo '<span class="custom info right">'.Texte::getText($_SESSION['lang'], 'access_to_mois').' '.htmlentities(strftime('%B %Y', mktime(0,0,0, $month + 1, 1, $year))).'.</span></a>'; ?>
		</td>
		<td width="35%" align="right">
			<script>var date = <?php echo $date; ?></script>
			<table border="0"><tr>
				<td><?php echo Texte::getText($_SESSION['lang'], 'account'); ?> :</td>
				<td>
					<select style="display: none; width: 220px;" name="account" id="account">
						<?php 
							foreach (Account::loadAll() as $acc) {
								$selected = ($acc->getAccountId() == $account)?'selected':'';
								echo '<option value="' . $acc->getAccountId() . '" ' . $selected . '>' . $acc->getAccountId() . ' - ' . $acc->getAccountName() . '</option>';
							}
						?>
					</select>
				</td>
			</tr></table>
		</td>
	</tr>
</table>
<table width="100%" border="0">
	</tr>
		<td width="65%" align="left"><h4><?php echo Texte::getText($_SESSION['lang'], 'credit'); ?></h4></td>
		<td width="25%" align="right" class="solde"><?php echo Texte::getText($_SESSION['lang'], 'solde_mois_passe'); ?> :</td>
		<td width="10%" id="solde" align="right" class="amount solde"><?php echo Transaction::getSolde($date, $account); ?></td>
	</tr>
</table>
<table width="100%" border="0">	
	<?php

		$credits = Transaction::loadCreditByMonth($date, $account);
		
		if (count($credits) != 0) {
			echo '<tr align="center">';
			echo '<th width="3%">&nbsp;</th>';
			echo '<th width="43%">'.Texte::getText($_SESSION['lang'], 'operation').'</th>';
			echo '<th width="14%">&nbsp;</th>';
			echo '<th width="14%">'.Texte::getText($_SESSION['lang'], 'credit').'</th>';
			echo '<th width="14%">&nbsp;</th>';
			echo '<th width="4%">&nbsp;</th>';
			echo '<th width="4%">&nbsp;</th>';
			echo '<th width="4%">&nbsp;</th>';
			echo '<tr>';
		}
		else
			echo '<tr align="center"><td>'.Texte::getText($_SESSION['lang'], 'aucun_credit_mois').'</td></tr>'; 
	
		// **********************************************************************************************************
		// Credit
		// **********************************************************************************************************
		foreach ($credits as $c) {
		
			$billPaid = ($c->isPaid((int)substr($date, 4, 2), (int)substr($date, 0, 4)))?'':'style="background-color: #FFE88B;"';
			$alt   = ($billPaid=='')?Texte::getText($_SESSION['lang'], 'unpaid_credit'):Texte::getText($_SESSION['lang'], 'paid_credit');
			$src   = ($billPaid=='')?'btnUnpaid':'btnPaid';
			$class = ($billPaid=='')?'credit':'creditNoPaid';
		
			echo '<tr align="center" '.$billPaid.'>';
			echo '<td></td>';
			$calcDate = substr($c->getDate(),0 ,4).'-'.substr($date, 4, 2).'-'.substr($c->getDate(), 8, 2);
			echo '<td align="left">'.strftime('%d/%m/%Y', strtotime($calcDate)).' : '.(($c->getRemark()!='')?$c->getRemark():'-').'</td>';
			echo '<td align="right">&nbsp;</td>';
			echo '<td class="amount '.$class.'" align="right">'.$c->getAmount().'</td>';
			echo '<td align="right">&nbsp;</td>';
			echo '<td align="center" class="btnEdit"><a class="tooltip" href="javascript:addTransaction('.$c->getId().', '.$date.', \''.$account.'\');"><span class="custom info left">'.Texte::getText($_SESSION['lang'], 'edit_credit').'</span></a></td>';
			echo '<td align="center" class="btnDelete"><a class="tooltip" href="javascript:deleteTransaction('.$c->getId().', '.$date.', 1, \''.$account.'\');"><span class="custom info left">'.Texte::getText($_SESSION['lang'], 'delete_credit').'</span></a></td>';
			echo '<td align="center" class="'.$src.'"><a class="tooltip" href="javascript:paid('.$c->getId().', '.$date.', \''.$account.'\');"><span class="custom info left">'.$alt.'</span></a></td>';
			echo '<tr>';
		}
		
		// **********************************************************************************************************
		// Total of credit
		// **********************************************************************************************************
		if (count($credits) != 0) {
		
			echo '<tr align="center">';
			echo '<th>&nbsp;</th>';
			echo '<th align="right">'.Texte::getText($_SESSION['lang'], 'total_credit').' : &nbsp;</th>';
			echo '<th align="right">&nbsp;</th>';
			echo '<th id="totalCredit" class="amount" align="right"></th>';
			echo '<th align="right">&nbsp;</th>';
			echo '<th align="right">&nbsp;</th>';
			echo '<th align="right">&nbsp;</th>';
			echo '<th align="right">&nbsp;</th>';
			echo '<tr>';
		}
	?>
</table>
<h4><?php echo Texte::getText($_SESSION['lang'], 'budget_debit'); ?></h4>
<table width="100%" border="0">	
	<?php
			
		$transactions 	 = Transaction::loadBudgetByMonth($date, $account);
		$nonTransactions = Transaction::loadNonBudgetByMonth($date, $account);
	
		if (count($transactions) != 0 || count($nonTransactions) != 0) {
			echo '<tr align="center">';
			echo '<th width="3%"><a id="plusMinusAll" href="javascript:hideShowRowAll();"><img alt="Cacher" src="images/minus.png"/></th>';
			echo '<th width="43%">'.Texte::getText($_SESSION['lang'], 'operation').'</th>';
			echo '<th width="14%">'.Texte::getText($_SESSION['lang'], 'budget').'</th>';
			echo '<th width="14%">'.Texte::getText($_SESSION['lang'], 'debit').'</th>';
			echo '<th width="14%">'.Texte::getText($_SESSION['lang'], 'disposition').'</th>';
			echo '<th width="4%">&nbsp;</th>';
			echo '<th width="4%">&nbsp;</th>';
			echo '<th width="4%">&nbsp;</th>';
			echo '<tr>';
		}
		else
			echo '<tr align="center"><td>'.Texte::getText($_SESSION['lang'], 'aucun_budget_debit_mois').'</td></tr>'; 
	
		// **********************************************************************************************************
		// Budget
		// **********************************************************************************************************
		foreach ($transactions as $t) {
			
			$op = Operation::loadById($t->getOperationId());
	
			echo '<tr align="center">';
			echo '<td><a id="plusMinus'.$t->getOperationId().'" href="javascript:hideShowRow('.$t->getOperationId().');"><img alt="Cacher" src="images/minus.png"/></td>';
			echo '<td align="left">&nbsp;'.$op->getName().'</td>';
			echo '<td class="amount budget" align="right">'.$t->getAmount().'</td>';
			echo '<td class="amount debit" align="right">'.Transaction::getTotalGlobal($date, $t->getOperationId(), $account).'</td>';
			// 2012.01.16 : add parameter to take all row
			echo '<td class="amount calcDif" align="right">'.($t->getAmount() - Transaction::getTotalGlobal($date, $t->getOperationId(), $account, true, false)).'</td>';
			echo '<td align="center" class="btnEdit"><a class="tooltip" href="javascript:addTransaction('.$t->getId().', '.$date.', \''.$account.'\');"><span class="custom info left">'.Texte::getText($_SESSION['lang'], 'edit_budget_menusel').'</span></a></td>';
			echo '<td align="center" class="btnDelete"><a class="tooltip" href="javascript:deleteTransaction('.$t->getId().', '.$date.', 1, \''.$account.'\');"><span class="custom info left">'.Texte::getText($_SESSION['lang'], 'delete_budget_mensuel').'</span></a></td>';
			echo '<tr>';
			
			$debits = Transaction::loadDebiByMonthAndOperation($date, $t->getOperationId(), $account);
			
			// name with row XX for hidden all rows with +/- at the table header 
			echo '<tr id="row'.$t->getOperationId().'" name="row '.$t->getOperationId().'" class=""><td></td><td colspan="7">';
			echo '<table class="detail" width="100%" border="0">';
			
			// **********************************************************************************************************
			// Debit transaction
			// **********************************************************************************************************
			if (count($debits) == 0)
				echo '<tr align="center"><td>'.Texte::getText($_SESSION['lang'], 'aucun_debit_mois').'</td></tr>'; 
			
			foreach ($debits as $d) {
							
				$billPaid = ($d->isPaid(substr($date, 4, 2), substr($date, 0, 4)))?'':'style="background-color: #FFE88B;"';
				$alt = ($billPaid=='')?Texte::getText($_SESSION['lang'], 'unpaid_debit'):Texte::getText($_SESSION['lang'], 'paid_debit');
				$src = ($billPaid=='')?'btnUnpaid':'btnPaid';
			
				$dateToDisplay = substr($d->getDate(), 0, 4) . '-' . $month . '-'. substr($d->getDate(), 8, 2);
			
				echo '<tr align="center" '.$billPaid.'>';
				echo '<td width="43%" align="left">'.strftime('%d/%m/%Y', strtotime($dateToDisplay)).' : '.(($d->getRemark()!="")?$d->getRemark():'-').'</td>';
				echo '<td width="14%">&nbsp;</td>';
				echo '<td width="14%" class="amount" align="right">'.$d->getAmount().'</td>';
				echo '<td width="14%">&nbsp;</td>';
				echo '<td width="4%" align="center" class="btnEdit"><a class="tooltip" href="javascript:addTransaction('.$d->getId().', '.$date.', \''.$account.'\');"><span class="custom info left">'.Texte::getText($_SESSION['lang'], 'edit_debit').'</span></a></td>';
				echo '<td width="4%" align="center" class="btnDelete"><a class="tooltip" href="javascript:deleteTransaction('.$d->getId().', '.$date.', 1, \''.$account.'\');"><span class="custom info left">'.Texte::getText($_SESSION['lang'], 'delete_debit').'</span></a></td>';
				echo '<td width="4%" align="center" class="'.$src.'"><a class="tooltip" href="javascript:paid('.$d->getId().', '.$date.', \''.$account.'\');"><span class="custom info left">'.$alt.'</span></a></td>';
			}
			echo '</table></td></tr>';
		}
		
		// **********************************************************************************************************
		// Non budget transaction
		// **********************************************************************************************************
		foreach ($nonTransactions as $t) {
			
			$op = Operation::loadById($t->getOperationId());
			 
			$billPaid = ($t->isPaid((int)substr($date, 4, 2), (int)substr($date, 0, 4)))?'':'style="background-color: #FFE88B;"';
			$alt   = ($billPaid=='')?Texte::getText($_SESSION['lang'], 'unpaid_debit'):Texte::getText($_SESSION['lang'], 'paid_debit');
			$src   = ($billPaid=='')?'btnUnpaid':'btnPaid';
			$class = ($billPaid=='')?'debit':'';
			
			echo '<tr align="center" '.$billPaid.'>';
			echo '<td>&nbsp;</td>';
			echo '<td align="left">&nbsp;'.strftime('%d/%m/%Y', strtotime($t->getDate())).' : '.$op->getName().'</td>';
			echo '<td align="right">-</td>';
			echo '<td class="amount '.$class.'" align="right">'.$t->getAmount().'</td>';
			echo '<td align="right">-</td>';
			echo '<td align="center" class="btnEdit"><a class="tooltip" href="javascript:addTransaction('.$t->getId().', '.$date.', \''.$account.'\');"><span class="custom info left">'.Texte::getText($_SESSION['lang'], 'edit_debit').'</span></a></td>';
			echo '<td align="center" class="btnDelete"><a class="tooltip" href="javascript:deleteTransaction('.$t->getId().', '.$date.', 1, \''.$account.'\');"><span class="custom info left">'.Texte::getText($_SESSION['lang'], 'delete_debit').'</span></a></td>';
			echo '<td align="center" class="'.$src.'"><a class="tooltip" href="javascript:paid('.$t->getId().', '.$date.', \''.$account.'\');"><span class="custom info left">'.$alt.'</span></a></td>';
			echo '<tr>';
		}
		
		// **********************************************************************************************************
		// Total of transaction
		// **********************************************************************************************************
		if (count($transactions) != 0 || count($nonTransactions) != 0) {
		
			echo '<tr align="center">';
			echo '<th>&nbsp;</th>';
			echo '<th align="right">'.Texte::getText($_SESSION['lang'], 'total').' : &nbsp;</th>';
			echo '<th id="totalBudget" class="amount" align="right"></th>';
			echo '<th id="totalDebit" class="amount" align="right"></th>';
			echo '<th id="totalDiff" class="amount" align="right">&nbsp;</th>';
			echo '<th align="right">&nbsp;</th>';
			echo '<th align="right">&nbsp;</th>';
			echo '<th align="right">&nbsp;</th>';
			echo '<tr>';
		}
	?>
</table>
<br/>
<table width="100%" border="0" align="center">
	<tr>
		<td width="90%" align="right" class="solde"><?php echo Texte::getText($_SESSION['lang'], 'solde_mois'); ?> : </td>
		<td width="10%" id="totalGlobal" align="right" class="amount solde"></td>
	</tr>
	<tr>
		<td align="right"><?php echo Texte::getText($_SESSION['lang'], 'solde_estime_mois'); ?> : </td>
		<td align="right" class="amount">
			<?php 
				$month = (int)substr($date, 4, 2);
				$month = (($month + 1 < 10)?'0':'').($month + 1);
			
				echo (Transaction::getSolde(substr($date, 0, 4).'0101', $account) +
					  Transaction::getCredit(substr($date, 0, 4).$month.'01', 0, $account) -
					  Transaction::getEstimatedSolde(substr($date, 0, 4).$month.'01', $account, 1));
			?>
		</td>
	</tr>
</table>
<br/>

<!-- For calendar (add.php) -->
<!-- For calendar (add.php) -->
<table class="ds_box" cellpadding="0" cellspacing="0" id="ds_conclass" style="display: none;">
<tr><td id="ds_calclass">
</td></tr>
</table>