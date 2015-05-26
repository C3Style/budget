<?php
	session_start();
	
	include_once 'classes/Transaction.class.php';
	include_once 'classes/Type.class.php';
	include_once 'classes/Operation.class.php';
	include_once 'classes/Texte.class.php';
	
	$beginDate = '';
	$endDate = '';
	$account = $_GET['accountSearch'];
	
	if ($_GET['beginDateSearch'] != '')
		$beginDate = substr($_GET['beginDateSearch'], 6, 4) . "-" . substr($_GET['beginDateSearch'], 3, 2) . "-" . substr($_GET['beginDateSearch'], 0, 2);
		
	if ($_GET['endDateSearch'] != '')
		$endDate = substr($_GET['endDateSearch'], 6, 4) . "-" . substr($_GET['endDateSearch'], 3, 2) . "-" . substr($_GET['endDateSearch'], 0, 2);
	
	$transactions = Transaction::search($_GET['typeSearch'], $_GET['operationSearch'], $_GET['remarkSearch'], $beginDate, $endDate, $account);		
?>
	<table class="detail" width="100%">
<?php	
	if (count($transactions) > 0)  {
?>
		<tr style="font-style:normal; text-align:left;">
			<th width="10%"><?php echo Texte::getText($_SESSION['lang'], 'type'); ?></th>
			<th width="20%"><?php echo Texte::getText($_SESSION['lang'], 'operation'); ?></th>
			<th width="60%"><?php echo Texte::getText($_SESSION['lang'], 'date'); ?> : <?php echo Texte::getText($_SESSION['lang'], 'remarque'); ?></th>
			<th width="10%" style="text-align:right;"><?php echo Texte::getText($_SESSION['lang'], 'montant'); ?></th>
		</tr>
<?php
	} else {
?>
		<tr><td align="center"><?php echo Texte::getText($_SESSION['lang'], 'aucune_transaction_recherche'); ?> </td></tr>
<?php
	}

	foreach ($transactions as $transaction) {
		$type = Type::loadById($transaction->getTypeId());
		$operation = Operation::loadById($transaction->getOperationId());
		
		if(strlen($transaction->getActualRecForYear()) == 4) {
			$dateLink = $transaction->getActualRecForYear() . '0101';
			$date = Texte::getText($_SESSION['lang'], 'annee') . ' ' . $transaction->getActualRecForYear();
			$page = 2;
		} else {
			$dateLink = $transaction->getActualRecForYear();
			$date = $transaction->getDate(true);
			$page = 1;
		}	
?>
		<tr>
			<td><?php echo $type->getName(); ?></td>
			<td><?php echo ($operation->getId() == 0)?'-':$operation->getName(); ?></td>
			<td><a class="link tooltip" href="index.php?page=<?php echo $page; ?>&date=<?php echo $dateLink . '&account=' . $account; ?>"><?php echo $date.' : '.(($transaction->getRemark()!="")?$transaction->getRemark():'-'); ?>
				<span class="custom info rigth"><?php echo Texte::getText($_SESSION['lang'], 'access_mois'); ?></span></a></td>
			<td align="right"><?php echo $transaction->getAmount(); ?></td>
		<tr/>
<?php
	}
?>
	</table>