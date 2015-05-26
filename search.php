<?php
	session_start();
	
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
		
	if (is_object($actualAccount)) {
		$operations = Operation::loadAllByAccount($actualAccount->getAccountId());
	} else {
		$operations = array();
	}

	$title = Texte::getText($_SESSION['lang'], 'rechercher_transac');
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
<link rel="stylesheet" type="text/css" href="styles/main.css">
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
	cal.manageFields("beginDateSearch", "beginDateSearch", "%d/%m/%Y");
	cal.manageFields("endDateSearch", "endDateSearch", "%d/%m/%Y");

	changeDialogTitle('dialogSearch', '<?php echo $title; ?>');
	
	$("#search").click(function (i) {
		$('#searchResult').html("<img id='searchLoading' src='images/loading.gif' style='display:block;'>");
		$.ajax({
			type: 'GET',
			url:  '_searchTransaction.php',
			data: 'typeSearch=' + $('#typeSearch').val() + '&operationSearch=' + $('#operationSearch').val() + '&remarkSearch=' + $('#remarkSearch').val() + 
				  '&beginDateSearch=' + $('#beginDateSearch').val() + '&endDateSearch=' + $('#endDateSearch').val() + '&accountSearch=' + <?php echo "'".$account."'"; ?>,
			dataType: 'text',
			success: function (msg) {
				$('#searchResult').html(msg);
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				alert('load product search form error: '+textStatus);
			}
		});
	});
	
	$("#reset").click(function (i) {
		$('#searchResult').html('');
		$('#typeSearch').val(-1);
		$('#operationSearch').val(-1);
		$('#beginDateSearch').val('');
		$('#endDateSearch').val('');
		$('#remarkSearch').val('');
	});

</script>
</head>
<body>
<div class="add">
	<table width="100%" border="0" align="center" style="margin-top: 15px; margin-bottom: 15px;">
		<tr>
			<td width="24%"><?php echo Texte::getText($_SESSION['lang'], 'type'); ?> : </td>
			<td width="24%">
				<select style="width: 200px;" name="typeSearch" id="typeSearch">
						<option value='-1'>-</option>
					<?php 
					foreach ($types as $t) { ?>
						<option value='<?php echo $t->getId(); ?>'><?php echo $t->getName(); ?></option>
					<?php } ?>
				</select>
			</td>
			<td width="4%"></td>
			<td width="24%"><?php echo Texte::getText($_SESSION['lang'], 'operation'); ?>  :</td>
			<td width="24%">
				<select style="width:200px;" name="operationSearch" id="operationSearch">
						<option value='-1'>-</option>
					<?php 
					foreach ($operations as $o) { ?>
						<option value='<?php echo $o->getId(); ?>'><?php echo $o->getName(); ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?php echo Texte::getText($_SESSION['lang'], 'date_debut'); ?>  :</td>
			<td><input size="20" name="beginDateSearch" id="beginDateSearch" value="" readonly="readonly" style="cursor:pointer;"/></td>
			<td></td>
			<td><?php echo Texte::getText($_SESSION['lang'], 'date_fin'); ?>  :</td>
			<td><input size="20" name="endDateSearch" id="endDateSearch" value="" readonly="readonly" style="cursor:pointer;"/></td>
		</tr>
		<tr>
			<td><?php echo Texte::getText($_SESSION['lang'], 'remarque'); ?> :</td>
			<td><input size="20" name="remarkSearch" id="remarkSearch" value=""/></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="5" align="right">
				<a style="color: #ffffff;" id="reset" href="#" class="budgetButton"><?php echo Texte::getText($_SESSION['lang'], 'reset'); ?></a>
				<a style="color: #ffffff;" id="search" href="#" class="budgetButton"><?php echo Texte::getText($_SESSION['lang'], 'rechercher'); ?></a>
			</td>
		</tr>
	</table>
</div>
<br/>
<div id="searchResult" align="center"></div>