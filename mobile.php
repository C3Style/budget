<?php
	session_start();
	
	header('Content-Type: text/html; charset=utf-8');
	
	include_once 'classes/Transaction.class.php';
	include_once 'classes/Operation.class.php';
	include_once 'classes/Account.class.php';
	include_once 'classes/Texte.class.php';
	
	if (!isset($_SESSION['lang']))
		$_SESSION['lang'] = 'FR';
	
	setlocale (LC_TIME, 'fr_FR');
	
	if (!isset($_GET['user']) && !isset($_SESSION['user'])) {
		header('Location: index.php');
	} elseif (isset($_GET['user'])) {
		$_SESSION['user'] = $_GET['user'];
	} 
	
	$accounts = Account::loadAll();	
	$date = date('Ymd');
	
	// Save data
	if (isset($_POST['operation']) && isset($_POST['amount']) && isset($_POST['account'])) {
		
		$transaction = new Transaction();
		$transaction->setTypeId(1);
		$transaction->setOperationId($_POST['operation']);
		$transaction->setAccountId($_POST['account']);
		$transaction->setDate(date('Ymd'));
		$transaction->setAmount(str_replace("'", "", $_POST['amount']));
		$transaction->setRemark($_POST['remark']);
		
		$recurrence = array();
		$recurrence[] = (int)date('m');
		$transaction->setRecurrence($recurrence);

		$return = $transaction->save();
		
		if (isset($_POST['paid']) && $_POST['paid'] == 'oui' && $return > 0) {
			$transaction = Transaction::loadById($return);
			$transaction->setPaid(date('m'), date('Y'));
		}
		
		if ($return > 0) {
			?>
			<script type="text/javascript">
				alert("<?php echo Texte::getText($_SESSION['lang'], 'save_transac_ok'); ?>");
			</script>
			<?php
		} else {
			?>
			<script type="text/javascript">
				alert("<?php echo Texte::getText($_SESSION['lang'], 'save_transac_ko'); ?>");
			</script>
			<?php
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html> 
	<head> 
	<title>Budget</title> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="stylesheet" href="styles/jquery.mobile-1.0b2.min.css" />
	<script language="javascript" src="scripts/jquery-1.6.2.min.js"></script>
	<script language="javascript" src="scripts/jquery-ui-1.8.1.custom.min.js"></script>
	<script language="javascript" src="scripts/jquery.mobile-1.0b2.min.js"></script>
	<script language="javascript" src="scripts/mobile.js"></script>
</head> 
<body> 
	<div data-role="page">
		<div data-role="header" data-theme="e">
			<h1><?php echo Texte::getText($_SESSION['lang'], 'ajouter').' '.Texte::getText($_SESSION['lang'], 'une_transaction'); ?></h1>			
			<a style='font-size: 10px;' href="login.php" rel="external" data-role="button" data-theme="e" data-pos="right" class="ui-btn-right"><?php echo Texte::getText($_SESSION['lang'], 'retour'); ?></a> 
		</div><!-- /header -->

		<div data-role="content">	
			
			<form method="post" id="add" action="mobile.php" data-ajax="false" enctype="x-www-form-urlencoded">
			
				<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
					<label class="ui-input-text" for="name"><?php echo Texte::getText($_SESSION['lang'], 'account'); ?> :</label>
					<select name="account" id="account">
						<option value='-1'>-</option>';
						<?php 
						foreach ($accounts as $a)				
							echo '<option value="'.$a->getAccountId().'">'.$a->getAccountName().'</option>'; 
						?>
					</select>
				</div>	
			
				<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
					<label class="ui-input-text" for="name"><?php echo Texte::getText($_SESSION['lang'], 'operation'); ?> :</label>
					<select name="operation" id="operation">
						<option value='-1'>-</option>';
					</select>
				</div>	
			
				<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
					<label class="ui-input-text" for="name"><?php echo Texte::getText($_SESSION['lang'], 'montant'); ?> :</label>
					<input class="ui-input-text ui-body-null ui-corner-all ui-shadow-inset ui-body-c" size="4" type="text" name="amount" id="amount"/>
				</div>	
				
				<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
					<label class="ui-input-text" for="name"><?php echo Texte::getText($_SESSION['lang'], 'remarque'); ?> :</label>
					<textarea name="remark" cols="25" rows="2"></textarea>
				</div>	
				
				<div class="ui-field-contain ui-body ui-br" data-role="fieldcontain">
					<label class="ui-input-text" for="name"><?php echo Texte::getText($_SESSION['lang'], 'paye'); ?> :</label>
					<select name="paid" id="paid" data-role="slider">
						<option value="oui" selected><?php echo Texte::getText($_SESSION['lang'], 'oui'); ?></option>
						<option value="non"><?php echo Texte::getText($_SESSION['lang'], 'non'); ?></option>
					</select> 
				</div>	
				
				<div align="center"><input type="submit" value="Enregistrer" data-theme="b">
				
			</form>
			
		</div><!-- /content -->
	
		<!--
		<div data-role="footer">
			<h4></h4>
		</div><!-- /footer -->
	</div><!-- /page -->
</body>
</html>