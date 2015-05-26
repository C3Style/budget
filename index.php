<?php
	session_start();
	
	header('Content-Type: text/html; charset=utf-8');
	
	include_once 'classes/Account.class.php';
	include_once 'classes/Texte.class.php';
	
	if (!isset($_SESSION['user']))
		header('Location: login.php');

	if (!isset($_SESSION['lang']))
		$_SESSION['lang'] = 'FR';
	
	$date = date('Ym01');

	if (isset($_GET['date']))
		$date = $_GET['date'];
		
	$page = 1;

	if (isset($_GET['page']))
		$page = $_GET['page'];
	
	$accountObj = Account::loadDefaultAccount();
	
	if (isset($_GET['account']))
		$account = $_GET['account'];
	else if (is_object($accountObj))
		$account = $accountObj->getAccountId();
	else
		$account = '0';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Budget</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="styles/main.css">
<link rel="stylesheet" type="text/css" href="styles/menu.css">
<link rel="stylesheet" type="text/css" href="scripts/simplemodal/simplemodal.css">
<link rel="stylesheet" type="text/css" href="styles/dialog/jquery-ui-1.8.1.custom.css">	
<link rel="Stylesheet" type="text/css" href="styles/select-ui.css">
<script language="javascript" src="scripts/jquery-1.4.2.min.js"></script>
<script language="javascript" src="scripts/jquery-ui-1.8.1.custom.min.js"></script>
<script language="javascript" src="scripts/jquery.validate.js"></script>
<script language="javascript" src="scripts/simplemodal/jquery.simplemodal-1.2.2.min.js"></script>
<script language="javascript" src="scripts/lib.js"></script>
<script language="javascript" src="scripts/index.js"></script>
<script language="javascript" src="scripts/select-ui.js"></script>
</head>
<body>
	<script language="javascript">var account = "<?php echo $account; ?>";</script>
	<div id="goTopOfPage"></div>
	<div class="top">
		<img alt="title" src="images/gold.png">
	</div>
	<div class="frame">
		<div class="nav-container-outer">
		   <img src="images/menu/nav-bg-l.jpg" alt="" class="float-left" />
		   <img src="images/menu/nav-bg-r.jpg" alt="" class="float-right" />
		   <ul id="nav-container" class="nav-container">
			  <li style="margin-top:10px;"><img src="images/year.png"/></li>
			  <li><a class="item-primary" href="index.php?page=2&account=<?php echo $account; ?>"><?php echo Texte::getText($_SESSION['lang'], 'recap_annuel'); ?></a></li>
			  <li style="margin-top:10px;"><img src="images/month.png"/></li>
			  <li><a class="item-primary" href="index.php?page=1&account=<?php echo $account; ?>"><?php echo Texte::getText($_SESSION['lang'], 'transac_mensuelle'); ?></a></li>
			  <li style="margin-top:10px;"><img src="images/add.png"/></li>
			  <li><a class="item-primary" href="javascript:addTransaction(-1, '<?php echo $date; ?>', '<?php echo $account; ?>');"><?php echo Texte::getText($_SESSION['lang'], 'ajouter_transac'); ?></a></li>
			  <li style="margin-top:10px;"><img src="images/search.png"/></li>
			  <li><a class="item-primary" href="javascript:searchTransactions('<?php echo $date; ?>', '<?php echo $account; ?>')"><?php echo Texte::getText($_SESSION['lang'], 'rechercher_transac'); ?></a></li>
			  <li style="margin-top:10px;"><img src="images/setting.png"/></li>
			  <li><a class="item-primary" href="index.php?page=3&account=<?php echo $account; ?>"><?php echo Texte::getText($_SESSION['lang'], 'parametre'); ?></a></li>
			  <li style="margin-top:10px;"><img src="images/quit.png"/></li>
			  <li><a class="item-primary" href="login.php"><?php echo Texte::getText($_SESSION['lang'], 'deconnexion'); ?></a></li>
		   </ul>
		</div>
		<?php if ($page == 1) { ?>
			<div class="content"><?php include 'content.php'; ?></div>
		<?php }else if ($page == 2) { ?>
			<div class="content"><?php include 'report.php'; ?></div>
		<?php }else if ($page == 3) { ?>
			<div class="content"><?php include 'setting.php'; ?></div>
		<?php } ?>
	</div>
	<div id="preview" style="display: none;"></div>
	<div id="dialog" class="add" title="Confirmation de suppression">
		<p>
			<?php echo Texte::getText($_SESSION['lang'], 'msg_confirm_suppr'); ?>
		</p>
		<span class="remark"><u><?php echo Texte::getText($_SESSION['lang'], 'msg_confirm_rem'); ?></u> : <?php echo Texte::getText($_SESSION['lang'], 'msg_confirm_rem_info'); ?></span>
	</div>
	<div id="dialogAdd" class="add" title=""></div>
	<div id="dialogSearch" class="add" title=""></div>
</body>
</html>