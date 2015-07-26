<?php
	session_start();
	
	header('Content-Type: text/html; charset=utf-8');
	
	include_once 'classes/Texte.class.php';
	include_once 'classes/Transaction.class.php';
	include_once 'classes/Operation.class.php';
	include_once 'classes/Account.class.php';
	include_once 'classes/Type.class.php';
	
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
	
	if (isset($_GET['id']))
		$transaction = Transaction::loadById($_GET['id']);
		
	if (is_object($actualAccount)) {
		$operations = Operation::loadAllByAccount($actualAccount->getAccountId());
	} else {
		$operations = array();
	}

	$title  = ($transaction != null && $transaction->getId() > 0)?Texte::getText($_SESSION['lang'], 'modifier'):Texte::getText($_SESSION['lang'], 'ajouter');
	$title .= (" " . Texte::getText($_SESSION['lang'], 'une_transaction'));
	$title .= (is_object($actualAccount))?(' (' . $actualAccount->getAccountId() . ' - ' . $actualAccount->getAccountName() . ')'):'';	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Budget</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script language="javascript" src="scripts/lib.js"></script>
<script language="javascript" src="scripts/add.js"></script>
<script language="javascript" src="scripts/calendar/js/jscal2.js"></script>
<script language="javascript" src="scripts/calendar/js/lang/fr.js"></script>
<link rel="stylesheet" type="text/css" href="scripts/calendar/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="scripts/calendar/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="scripts/calendar/css/gold/gold.css" />

<script type="text/javascript">
	// Source : http://www.dynarch.com/projects/calendar/doc/
	var cal = Calendar.setup({
		onSelect		: function(cal) { cal.hide(); setRecurrence(cal.selection.get()); },
		weekNumbers   	: false,
		showTime		: false
	});
	cal.manageFields("date", "date", "%d/%m/%Y");
	
	changeDialogTitle('dialogAdd', '<?php echo $title; ?>');
</script>
</head>
<body>
<div class="add">
	<form method="post" id="add" class="addForm" action="save.php" enctype="x-www-form-urlencoded">
		<table width="500px">
			<input type="hidden" name="id" value="<?php echo $transaction->getId(); ?>"/>
			<input type="hidden" id="hiddenType" value="<?php echo $transaction->getTypeId(); ?>"/>
			<input type="hidden" name="acutalAccount" value="<?php if($actualAccount) echo $actualAccount->getAccountId(); else echo'0'; ?>"/>
			<tr height="30px">
				<td width="40%" valign="top" class="marginTop"><?php echo Texte::getText($_SESSION['lang'], 'type'); ?> : </td>
				<td>
					<?php 
						foreach ($types as $t) {
							if ($t->getId() == 1 || $t->getId() == $transaction->getTypeId()) // Débit
								$checked = 'checked';
							else
								$checked = '';
								
							echo '<input type="radio" name="type" value="'.$t->getId().'"  onClick="changeType(this);" '.$checked.'>'.$t->getName().'&nbsp;';
						}
					?>
				</td>
			</tr>
			<tr height="30px" id="rowOperation" <?php if ($transaction->getTypeId() == 2) { ?>style="display: none;"<?php } ?>>
				<td valign="top" class="marginTop"><?php echo Texte::getText($_SESSION['lang'], 'operation'); ?> : </td>
				<td>
					<select name="operation" id="operation">
							<option value='-1'>-</option>
						<?php 
						if (count($operations)) {
							foreach ($operations as $o) {
							
								if ($o->getId() == $transaction->getOperationId())
									$selected = 'selected';
								else
									$selected = '';
							
								echo '<option value='.$o->getId().' '.$selected.'>'.$o->getName().'</option>';
							}
						}
						?>
					</select>
				</td>
			</tr>
			<tr height="30px" id="rowAccount" <?php if ($transaction->getTypeId() != 4) { ?>style="display: none;"<?php } ?>>
				<td valign="top" class="marginTop"><?php echo Texte::getText($_SESSION['lang'], 'account'); ?> : </td>
				<td>
					<select name="accountDest" id="accountDest">
						<option value='-1' selected>-</option>';
						<?php 
						foreach ($accounts as $acc) {
							if ($acc->getAccountId() != $account)
								echo '<option value="' . $acc->getAccountId() . '">' . $acc->getAccountId() . ' - ' . $acc->getAccountName() . '</option>';
						}
						?>
					</select>
				</td>
			</tr>
			<tr id="rowDate" <?php if ($transaction->getTypeId() == 3) { ?>style="display: none;"<?php } ?>>
				<td valign="top" class="marginTop"><?php echo Texte::getText($_SESSION['lang'], 'date'); ?> : </td>
				<td>
					<input size="29" name="date" id="date" value="<?php echo $transaction->getDate(true); ?>" readonly="readonly" style="cursor:pointer;"/>
				</td>
			</tr>
			<tr height="30px" id="rowYear" <?php if ($transaction->getTypeId() != 3) { ?>style="display: none;"<?php } ?>>
				<td valign="top" class="marginTop"><?php echo Texte::getText($_SESSION['lang'], 'annee'); ?> : </td>
				<td>
					<select name="year" id="year">
						<?php 
							if ($transaction->getDate() == "")
								$year = date("Y");
							else
								$year = substr($transaction->getDate(), 0, 4);
						?>
						<option value='<?php echo ($year - 1); ?>'>		<?php echo ($year - 1); ?></option>';
						<option value='<?php echo $year; ?>' selected>	<?php echo $year; ?></option>';
						<option value='<?php echo ($year + 1); ?>'>		<?php echo ($year + 1); ?></option>';
					</select>
				</td>
			</tr>
			<tr height="30px" id="rowRec" <?php /* if ($transaction->getId() == "" || $transaction->getTypeId() == 1) { ?>style="display: none;"<?php } */ ?>>
				<td valign="top" class="marginTop"><?php echo Texte::getText($_SESSION['lang'], 'recurrence'); ?> : </td>
				<td>
					<table width="100%">
						<tr>
							<td colspan="2">
								<?php $checked = (count($transaction->getRecurrence()) == 12)?'checked':''; ?>
								<input type="checkbox" name="rec0" id="rec0" value="0" <?php echo $checked; ?>/>
								<label id="recText" for="rec0"> 
									<?php 
										if ($checked == 'checked')
											echo Texte::getText($_SESSION['lang'], 'tout_decocher');
										else
											echo Texte::getText($_SESSION['lang'], 'tout_cocher');
									?>
								</label>
							</td>
						</tr>
					<?php
						for ($i = 1; $i < 13; $i+=2) {
							$iformat = (($i < 10)?'0':'').$i;
							$iformat2 = ((($i+1) < 10)?'0':'').($i+1);
							
							$checked = '';
							$checked2 = '';
							
							if ($transaction->monthExist($i, substr($date,0, 4)))
								$checked = 'checked';
								
							if ($transaction->monthExist($i+1, substr($date,0, 4)))
								$checked2 = 'checked';
							
							echo '<tr>';
							echo '<td width="50%"><input type="checkbox" id="rec'.$i.'" name="rec'.$i.'" value="'.$i.'" '.$checked.'/>'.htmlentities(strftime('%B', strtotime('2010'.$iformat.'01'))).'</td>';
							echo '<td width="50%"><input type="checkbox" id="rec'.($i+1).'" name="rec'.($i+1).'" value="'.($i+1).'" '.$checked2.'/>'.htmlentities(strftime('%B', strtotime('2010'.$iformat2.'01'))).'</td>';
							echo '</tr>';
						}
					?>
						<tr id="rowRemarkRec" <?php if ($transaction->getTypeId() != 3) { ?>style="display: none;"<?php } ?>>
							<td colspan="2">
								<span class="remark"><u><?php echo Texte::getText($_SESSION['lang'], 'recurrence_rem'); ?></u> : <?php echo Texte::getText($_SESSION['lang'], 'recurrence_rem_info'); ?></span>
							<td/>
						<tr>
					</table>
					<input type="hidden" name="onlyCheckRec" id="onlyCheckRec"/>
				</td>
			</tr>
			<tr height="30px">
				<td valign="top" class="marginTop"><?php echo Texte::getText($_SESSION['lang'], 'montant'); ?> : </td>
				<td>
					<input size="29" type="textbox" name="amount" id="amount" value="<?php echo (($transaction->getAmount() != "")?$transaction->getAmount():"0"); ?>"/>
				</td>
			</tr>
			<tr height="30px">
				<td valign="top"><?php echo Texte::getText($_SESSION['lang'], 'remarque'); ?> : </td>
				<td>
					<textarea name="remark" id="remark" cols="25" rows="2" onkeypress="javascript:checkDescrip(this, 253);"><?php echo $transaction->getRemark(); ?></textarea>
				</td>
			</tr>
		</table>
		<br/>
	</form>
	<?php if (is_object($actualAccount)) { ?>
		<div align="center" class="btnSave"><a class="tooltip" href="javascript:save();"><span class="custom info right"><?php echo Texte::getText($_SESSION['lang'], 'ajouter_la_transac'); ?></span></a></div>
	<?php } else { ?>
		<div align="center"><h2><?php echo Texte::getText($_SESSION['lang'], 'compte_doit_select'); ?></h2><h4>(<?php echo Texte::getText($_SESSION['lang'], 'go_transac_mensuelle'); ?>)</h4></div>
	<?php } ?>
</div>
</body>
</html>