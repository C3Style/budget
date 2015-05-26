<?php 
	include_once 'classes/Transaction.class.php';
	include_once 'classes/Operation.class.php';
	include_once 'classes/Account.class.php';

	setlocale (LC_TIME, 'fr_FR');
	
	$month = (int)strftime('%m', strtotime($date));
	$year = (int)strftime('%Y', strtotime($date));
	
	$operations = Operation::loadAllByAccount($account);
?>
<link rel="stylesheet" type="text/css" href="styles/jquery.jqplot.css" />
<script language="javascript" src="scripts/report.js"></script>
<script language="javascript" src="scripts/jquery.jqplot.js"></script>
<script language="javascript" src="scripts/plugins/jqplot.highlighter.js"></script>
<script language="javascript" src="scripts/plugins/jqplot.cursor.js"></script>
<script language="javascript" src="scripts/plugins/jqplot.dateAxisRenderer.js"></script>
<script language="javascript" src="scripts/plugins/jqplot.json2.js"></script>	
<h2 align="center"><?php echo Texte::getText($_SESSION['lang'], 'recap_annuel'); ?></h2>
<script language="javascript">
	var year = <?php echo $year; ?>;
	var account = '<?php echo $account; ?>';
	$(function(){
		$('select#operation').selectmenu({style:'dropdown', maxHeight: 300});
		$('select#account').selectmenu({style:'dropdown', maxHeight: 300});
	});
</script>	
<table width="100%" border="0" align="center">
	<tr>
		<td width="35%"></td>
		<td class="previous" align="left">
			<?php 
				echo '<a class="tooltip" href="index.php?page=2&date='.date("Ymd", mktime(0,0,0, 1, 1, $year - 1)).'&account='.$account.'">';
				echo '<span class="custom info right">'.Texte::getText($_SESSION['lang'], 'recap_annuel').' '.($year - 1).'.</span></a>'; 
			?>
		</td>
		<td align="center">
			<h3><?php echo htmlentities(strftime('%Y', strtotime($date))); ?></h3>
		</td>
		<td class="next" align="right">
			<?php echo '<a class="tooltip" href="index.php?page=2&date='.date("Ymd", mktime(0,0,0, 1, 1, $year + 1)).'&account='.$account.'">';
				  echo '<span class="custom info right">'.Texte::getText($_SESSION['lang'], 'recap_annuel').' '.($year + 1).'.</span></a>'; ?>
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
<table width="100%">	
	<tr valign="middle">
		<td width="50%" valign="top">
			<h4><?php echo Texte::getText($_SESSION['lang'], 'recap_mensuel'); ?></h4>
			<table width="100%">	
				<?php
					echo '<tr align="center">';
					echo '<th width="20%">'.Texte::getText($_SESSION['lang'], 'mois').'</th>';
					echo '<th width="20%">'.Texte::getText($_SESSION['lang'], 'solde_initial').'</td>';
					echo '<th width="20%">'.Texte::getText($_SESSION['lang'], 'solde_final').'</th>';
					echo '<th width="20%">'.Texte::getText($_SESSION['lang'], 'a_disposition').'</th>';
					echo '<th width="20%">'.Texte::getText($_SESSION['lang'], 'solde_estime').'</th>';
					echo '<tr>';
				
					// 2012.01.16 : add a sum of all non budget to calculate the disposal value by year
					$nonBudget = 0;
				
					for ($i = 1; $i < 13; $i++) {
					
						$iformat = (($i < 10)?'0':'').$i;
						$iformat2 = ((($i+1) < 10)?'0':'').($i+1);
					
						// 2012.01.16 : add a sum of all non budget to calculate the disposal value by year
						$nonTransactions = Transaction::loadNonBudgetByMonth($year.$iformat.'01', $account);
						foreach ($nonTransactions as $t) {
							$nonBudget += $t->getAmount();
						}
						
						// 2012.01.16 : the month is passed
						if (($i <= (int)date('m') && $year == (int)date('Y')) || $year < (int)date('Y')) {
							echo '<tr>';
							echo '<td><a class="link tooltip" href="index.php?page=1&date='.$year.$iformat.'01&account='.$account.'" >'.htmlentities(strftime('%B', strtotime($year.$iformat.'01')));
							echo '<span class="custom info rigth">'.Texte::getText($_SESSION['lang'], 'access_to_month').' '.htmlentities(strftime('%B', strtotime($year.$iformat.'01'))).'.</span></a></td>';
							echo '<td align="right" class="amount">'.Transaction::getSolde($year.$iformat.'01', $account).'</td>';
							echo '<td align="right" class="amount">'.Transaction::getSolde($year.$iformat2.'01', $account).'</td>';
							// 2012.01.16 : add parameter to take all row
							echo '<td align="right" class="amount calcDifGlobal">'.(Transaction::getEstimatedSolde($year.$iformat2.'01', $account, 3) -
																					Transaction::getEstimatedSolde($year.$iformat2.'01', $account, 1) +
																					$nonBudget).'</td>';
							echo '<td align="right" class="amount">'.(Transaction::getSolde($year.'0101', $account) +
																	 Transaction::getCredit($year.$iformat2.'01', 0, $account) -
																	 Transaction::getEstimatedSolde($year.$iformat2.'01', $account, 1)).'</td>';
							echo '</tr>';
						}
					}
					
					$solde = Transaction::getSolde($year.'0101', $account) +
							 Transaction::getCredit($year.'1301', 0, $account) -
						     Transaction::getEstimatedSolde($year.'1301', $account, ($_SESSION['user'] == 'jelugon')?1:3);
					
					echo '<tr><td colsapn="5">&nbsp;</td></tr>';
					
					echo '<tr align="right" style="font-weight: bold;">';
					echo '<td colspan="4" width="80%" style="padding: 5px;">'.Texte::getText($_SESSION['lang'], 'solde_initial_1_jan').' ' . $year . '</td>';
					echo '<td style="padding: 5px;" class="amount" width="20%">' . Transaction::getSolde($year.'0101', $account) . '</td>';
					echo '<tr>';
					
					// 2012.01.16 : set the actual month
					$actualMonth = ($year < (int)date('Y'))?12:$month;
					$actualMonth = (($actualMonth < 10)?'0':'').$actualMonth;
					
					echo '<tr align="right" style="font-weight: bold;">';
					echo '<td colspan="4" width="80%" style="padding: 5px;">'.Texte::getText($_SESSION['lang'], 'solde_effectif_31').' ' . htmlentities(strftime('%B', strtotime($year.$actualMonth.'01'))) . ' ' . $year . '</td>';
					echo '<td style="padding: 5px;" class="amount" width="20%">' . Transaction::getSolde($year.'1301', $account) . '</td>';
					echo '<tr>';
					
					echo '<tr align="right" style="font-weight: bold; background-color: #FFE88B;">';
					echo '<td colspan="4" width="80%" style="padding: 5px;">'.Texte::getText($_SESSION['lang'], 'solde_estime_31').' ' . $year . '</td>';
					echo '<td id="estimatedSolde" style="padding: 5px;" class="amount" width="20%">' . $solde . '</td>';
					echo '<tr>';
				?>
			</table>
		</td>
		<td width="50%" valign="top" align="center">
			<table style="margin-top: 50px;"><tr>
				<td><?php echo Texte::getText($_SESSION['lang'], 'operation'); ?> :</td>
				<td>
					<select style="display: none; width: 300px;" name="operation" id="operation">
						<option value="0" selected="selected">-- <?php echo Texte::getText($_SESSION['lang'], 'toute_confondue'); ?> --</option>
						<?php foreach ($operations as $op) { ?>
							<option value="<?php echo $op->getId(); ?>"><?php echo $op->getName(); ?></option>
						<?php } ?>
					</select>
				</td>
			</tr></table>
			<div id="chart1" class="plot" style="width:500px;height:300px;"></div>
			<br/>
			<table id="recapTable">
				<tr>
					<?php for ($i = 1; $i < 13; $i++) {
						$iformat = (($i < 10)?'0':'').$i; ?>
						<td align="center" width="35px">
							<a class="link" href="index.php?page=1&date=<?php echo $year.$iformat.'01'; ?>&account=<?php echo $account; ?>">
								<?php echo htmlentities(strftime('%b', strtotime($year.$iformat.'01'))); ?></td>
							</a>
					<?php } ?>
					<td align="center" width="35px"><?php echo Texte::getText($_SESSION['lang'], 'moyenne_abrev'); ?>.</td>
					<td align="center" width="35px"><?php echo Texte::getText($_SESSION['lang'], 'total_abrev'); ?>.</td>
				<tr>
				<tr id="recapBudget" style="background-color:#FFE88B;">
					<?php for ($i = 1; $i < 13; $i++) { 
						$iformat = (($i < 10)?'0':'').$i; ?>
						<td align="right" class="<?php echo $iformat . '-01-' .$year; ?>">-</td>
					<?php } ?>
					<td align="right" class="average">-</td>
					<td align="right" class="total">-</td>
				<tr>
				<tr id="recapDebit" style="background-color:#168BD4; color:white;">
					<?php for ($i = 1; $i < 13; $i++) {
						$iformat = (($i < 10)?'0':'').$i; ?>
						<td align="right" class="<?php echo $iformat . '-01-' .$year; ?>">-</td>
					<?php } ?>
					<td align="right" class="average">-</td>
					<td align="right" class="total">-</td>
				<tr>
			</table>
		</td>
	</tr>
</table>
<br/>
<h4><?php echo Texte::getText($_SESSION['lang'], 'budget_annuel'); ?></h4>
<table width="100%">	
	<?php
		
		$yearBudget = Transaction::loadYearBudget($date, $account);
	
		if (count($yearBudget) != 0) {
			echo '<tr align="center">';
			echo '<th width="3%">&nbsp;</th>';
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
			echo '<tr align="center"><td>'.Texte::getText($_SESSION['lang'], 'aucun_budget_annee').'</td></tr>'; 
	
		// **********************************************************************************************************
		// Budget
		// **********************************************************************************************************
		foreach ($yearBudget as $t) {
			
			$op = Operation::loadById($t->getOperationId());
	
			echo '<tr align="center">';
			echo '<td><a id="plusMinus'.$t->getOperationId().'" href="javascript:hideShowRow('.$t->getOperationId().');"><img alt="Cacher" src="images/minus.png"/></td>';
			echo '<td align="left">&nbsp;'.$op->getName().'</td>';
			echo '<td class="amount budget" align="right">'.$t->getAmount().'</td>';
			echo '<td class="amount debit" align="right">'.Transaction::getTotalGlobal($date, $t->getOperationId(), $account, false).'</td>';
			// 2012.01.16 : add parameter to take all row
			echo '<td class="amount calcDif" align="right">'.($t->getAmount() - Transaction::getTotalGlobal($date, $t->getOperationId(), $account, false, false)).'</td>';
			echo '<td align="center" class="btnEdit"><a class="tooltip" href="javascript:addTransaction('.$t->getId().', '.$date.', \''.$account.'\');"><span class="custom info left">'.Texte::getText($_SESSION['lang'], 'edit_budget_annuel').'</span></a></td>';
			echo '<td align="center" class="btnDelete"><a class="tooltip" href="javascript:deleteTransaction('.$t->getId().', '.$date.', 2, \''.$account.'\');"><span class="custom info left">'.Texte::getText($_SESSION['lang'], 'delete_budget_annuel').'</span></a></td>';
			echo '<td align="center"></a></td>';
			echo '<tr>';
			
			$debits = Transaction::loadDebiByMonthAndOperation($date, $t->getOperationId(), $account, false);
			
			echo '<tr id="row'.$t->getOperationId().'" class=""><td></td><td colspan="7">';
			echo '<table class="detail" width="100%" border="0">';
			
			// **********************************************************************************************************
			// Debit transaction
			// **********************************************************************************************************
			if (count($debits) == 0)
				echo '<tr align="center"><td>'.Texte::getText($_SESSION['lang'], 'aucun_debit_budget').'</td></tr>'; 
			
			foreach ($debits as $d) {
			
				$billPaid = ($d->isPaid(substr($d->getActualRecForYear(), 4, 2), substr($d->getActualRecForYear(), 0, 4)))?'':'style="background-color: #FFE88B;"';
			
				echo '<tr align="center" '.$billPaid.'>';
				echo '<td width="43%" align="left">';
					echo '<a class="link tooltip" href="index.php?page=1&date=' . $d->getActualRecForYear() . '&account=' . $account . '">'.htmlentities(strftime('%d/%m/%Y', strtotime($d->getDate()))).' : '.(($d->getRemark()!="")?$d->getRemark():'-');
					echo '<span class="custom info rigth">'.Texte::getText($_SESSION['lang'], 'access_mois_debit').'</span></a>';
				echo '</td>';
				echo '<td width="14%">&nbsp;</td>';
				echo '<td width="14%" class="amount" align="right">'.$d->getAmount().'</td>';
				echo '<td width="14%">&nbsp;</td>';
				echo '<td width="4%" align="center"></td>';
				echo '<td width="4%" align="center"></td>';
				echo '<td width="4%" align="center"></td>';				
			}
			echo '</table></td></tr>';
		}
		
		// **********************************************************************************************************
		// Total of transaction
		// **********************************************************************************************************
		if (count($yearBudget) != 0) {
		
			echo '<tr align="center">';
			echo '<th>&nbsp;</th>';
			echo '<th align="right">'.Texte::getText($_SESSION['lang'], 'total_budget_debit').' : &nbsp;</th>';
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