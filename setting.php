<?php 
	include_once 'classes/Operation.class.php';
	include_once 'classes/Transaction.class.php';
	include_once 'classes/Balance.class.php';
	include_once 'classes/Account.class.php';
	setlocale (LC_TIME, 'fr_FR');
?>
<link href="scripts/dynatree/skin/ui.dynatree.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="styles/tab.css">
<script src="scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="scripts/dynatree/jquery.dynatree.js" type="text/javascript"></script>
<script language="javascript" src="scripts/setting.js"></script>
<input type='hidden' id='tabGetValue' value='<?php echo isset($_GET['tab'])?$_GET['tab']:'1'; ?>'>
<h2 align="center"><?php echo Texte::getText($_SESSION['lang'], 'parametres'); ?></h2>
<script language="javascript">
	var copy = 0; 
	$(function(){
		$('select#account').selectmenu({style:'dropdown', maxHeight: 300});
		
		$("#tree").dynatree({
			persist: true,
			checkbox: true,
			selectMode: 3,
			onSelect: function(select, node) {
				// Get a list of all selected nodes, and convert to a key array:
				var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
					if (node.data.key.substr(0,1) != '_')
						return node.data.key;
				});
				$("#treeInput").val(selKeys.join(","));
			},
			onPostInit: function(isReloading, isError) {
			   logMsg("onPostInit(%o, %o)", isReloading, isError);
			   // Re-fire onActivate, so the text is update
			   this.reactivate();
			},
			onActivate: function(node) {
				$("#echoActive").text(node.data.title);
			},
			onDeactivate: function(node) {
				$("#echoActive").text("-");
			},
			onDblClick: function(node, event) {
				logMsg("onDblClick(%o, %o)", node, event);
				node.toggleExpand();
			}
		});
		
	});
</script>
<table width='100%'><tr><td>
	<ul class="tabs">
		<li id='liTab1'><a href="#tab1"><?php echo Texte::getText($_SESSION['lang'], 'gestion_operation'); ?></a></li>
		<li id='liTab2'><a href="#tab2"><?php echo Texte::getText($_SESSION['lang'], 'gestion_solde'); ?></a></li>
		<li id='liTab3'><a href="#tab3"><?php echo Texte::getText($_SESSION['lang'], 'copie_transac'); ?></a></li>
		<li id='liTab4'><a href="#tab4"><?php echo Texte::getText($_SESSION['lang'], 'update_pwd'); ?></a></li>
		<div align='right' style="margin-top: -2px;">
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
		</div>
	</ul>
	<div class="tab_container">
		<div id="tab1" class="tab_content">
			<table border="0">
				<tr>
					<td width="140px"><?php echo Texte::getText($_SESSION['lang'], 'ajouter_une_operation'); ?></td>
					<td width="235px"><input type="textbox" size="35" id="operation"/></td>
					<td class="btnAdd"><a class="tooltip" href="javascript:addOperation();"><span class="custom info right"><?php echo Texte::getText($_SESSION['lang'], 'ajouter_l_operation'); ?></span></a></td>
				</tr>
			</table>
			<br/>
			<i><?php echo Texte::getText($_SESSION['lang'], 'list_operation'); ?> :</i>
			<table border="0" style="margin-top: 3px;">
				<?php 
					$operations = Operation::loadAllByAccount($account);
					
					if (empty($operations)) {
						echo '<tr><td>'.Texte::getText($_SESSION['lang'], 'aucune_operation').'</td></tr>';
					}
					else {
					
						foreach ($operations as $o) {
							echo '<tr>';
							echo '<td width="250px" align="left">'.$o->getName().'</td>';
							if (Transaction::operationExist($o->getId()))
								echo '<td width="50px" align="center">-</td>';
							else
								echo '<td width="50px" align="center" class="btnDelete">';
								echo '<a class="tooltip" href="javascript:deleteOperation('.$o->getId().', \''.$o->getAccountId().'\');"><span class="custom info right">'.Texte::getText($_SESSION['lang'], 'delete_operation').'</span></a></td>';
							echo '</tr>';
						}
					}
				?>
			</table>
		</div>
		<div id="tab2" class="tab_content">
			<table border="0">
				<tr>
					<td width="130px"><?php echo Texte::getText($_SESSION['lang'], 'ajouter_un_solde'); ?></td>
					<td width="145px" colspan="2"><input id="balance" type="textbox"/></td>
					<td width="85px"><?php echo Texte::getText($_SESSION['lang'], 'pour_annee'); ?></td>
					<td width="65px">
						<?php $year = date('Y'); ?>
						<select id="year">
							<option value='<?php echo ($year - 1); ?>'>		<?php echo ($year - 1); ?></option>';
							<option value='<?php echo $year; ?>' selected>	<?php echo $year; ?></option>';
							<option value='<?php echo ($year + 1); ?>'>		<?php echo ($year + 1); ?></option>';
						</select>
					</td>
					<td class="btnAdd"><a class="tooltip" href="javascript:addBalance();"><span class="custom info right"><?php echo Texte::getText($_SESSION['lang'], 'ajouter_le_solde'); ?></span></a></td>
				</tr>
			</table>
			<br/>
			<i><?php echo Texte::getText($_SESSION['lang'], 'list_solde'); ?> :</i>
			<table border="0" style="margin-top: 3px;">
				<?php 
					$balance = Balance::loadAllByAccount($account);
					
					if (empty($balance)) {
						echo '<tr><td>'.Texte::getText($_SESSION['lang'], 'aucun_solde').'</td></tr>';
					}
					else {
					
						foreach ($balance as $s) {
							echo '<tr>';
							echo '<td width="60px" align="left">'.Texte::getText($_SESSION['lang'], 'annee').' : </td>';
							echo '<td width="100px" align="left">'.$s->getYear().'</td>';
							echo '<td width="70px" align="left">'.Texte::getText($_SESSION['lang'], 'account').' : </td>';
							echo '<td width="120px" align="left">'.$s->getAccount().'</td>';
							echo '<td width="55px" align="left">'.Texte::getText($_SESSION['lang'], 'solde').' : </td>';
							echo '<td width="100px" align="left" class="amount">'.$s->getAmount().'</td>';
							echo '<td width="50px" align="center" class="btnDelete">';
							echo '<a class="tooltip" href="javascript:deleteBalance('.$s->getYear().', \''.$s->getAccount().'\');"><span class="custom info right">'.Texte::getText($_SESSION['lang'], 'delete_solde').'</span></a></td>';
							echo '</tr>';
						}
					}
				?>
			</table>
		</div>
		<div id="tab3" class="tab_content">
			<form method="post" id="copyBudget" action="saveCopy.php" enctype="x-www-form-urlencoded">
				<table border="0">
					<tr>
						<td width="240px"><?php echo Texte::getText($_SESSION['lang'], 'choix_annee_source'); ?></td>
						<td width="100px">
							<select name="yearBudgetSrc" id="yearBudgetSrc">
								<option value='0' selected><?php echo Texte::getText($_SESSION['lang'], 'choisir'); ?></option>';
								<option value='<?php echo ($year - 1); ?>'>		<?php echo ($year - 1); ?></option>';
								<option value='<?php echo $year; ?>'>	<?php echo $year; ?></option>';
								<option value='<?php echo ($year + 1); ?>'>		<?php echo ($year + 1); ?></option>';
							</select>
						</td>
						<td rowspan="2" align="left">
							<img id='loading' src='images/loading.gif' style='display:none;'>
						</td>
					</tr>
					<tr>
						<td width="240px"><?php echo Texte::getText($_SESSION['lang'], 'choix_annee_dest'); ?></td>
						<td width="100px">
							<select name="yearBudgetDest" id="yearBudgetDest">
								<option value='0' selected><?php echo Texte::getText($_SESSION['lang'], 'choisir'); ?></option>';
								<option value='<?php echo ($year - 1); ?>'>		<?php echo ($year - 1); ?></option>';
								<option value='<?php echo $year; ?>'>	<?php echo $year; ?></option>';
								<option value='<?php echo ($year + 1); ?>'>		<?php echo ($year + 1); ?></option>';
							</select>
						</td>
					</tr>
				</table>
				<br/>
				<div id="tree" style="display:none;"></div>
				<input type="hidden" name="treeInput" id="treeInput">
			</form>
			<br/>
			<table>
				<tr>
					<td class="btnSave"><a class="tooltip" href="javascript:saveCopy();"><span class="custom info right"><?php echo Texte::getText($_SESSION['lang'], 'copier_transac'); ?></span></a></td>
					<?php if (isset($_GET['copy']) && $_GET['copy'] == 1) { ?>
						<td><h3 style="color:green;">&nbsp;&nbsp;&nbsp;<?php echo Texte::getText($_SESSION['lang'], 'copie_transac_ok'); ?></h3></td>
					<?php } elseif (isset($_GET['copy']) && $_GET['copy'] == 0) { ?>
						<td><h3 style="color:red;">&nbsp;&nbsp;&nbsp;<?php echo Texte::getText($_SESSION['lang'], 'copie_transac_ko'); ?></h3></td>
					<?php } ?>
				</tr>
			</table>
		</div>
		<div id="tab4" class="tab_content">
			<form method="post" id="updatePassword" action="updatePassword.php" enctype="x-www-form-urlencoded">
				<table border="0">
					<tr>
						<td width="250px"><?php echo Texte::getText($_SESSION['lang'], 'actuel_pwd'); ?></td>
						<td width="200px"><input id="password" name="password" type="password"/></td>
					</tr>
					<tr>
						<td><?php echo Texte::getText($_SESSION['lang'], 'nouveau_pwd'); ?></td>
						<td><input id="newPassword" name="newPassword" type="password"/></td>
					</tr>
					<tr>
						<td><?php echo Texte::getText($_SESSION['lang'], 'confirm_pwd'); ?></td>
						<td><input id="confirmPassword" name="confirmPassword" type="password"/></td>
					</tr>
				</table>
				<input id="account" name="account" type="hidden" value="<?php echo $account; ?>"/>
				<table style="padding-top: 10px;">
					<tr>
						<td class="btnSave"><a class="tooltip" href="javascript:updatePassword();"><span class="custom info right"><?php echo Texte::getText($_SESSION['lang'], 'update_pwd'); ?></span></a></td>
							<?php if (isset($_GET['status']) && $_GET['status'] == 1) { ?>
								<td><span style="color:green;">&nbsp;&nbsp;&nbsp;<?php echo Texte::getText($_SESSION['lang'], 'update_pwd_ok'); ?></h3></td>
							<?php } elseif (isset($_GET['status']) && $_GET['status'] == 0) { ?>
								<td><span style="color:red;">&nbsp;&nbsp;&nbsp;<?php echo Texte::getText($_SESSION['lang'], 'update_pwd_ko'); ?></h3></td>
							<?php } ?>
					</tr>
				</table>
			</form>
			<br/><br/>
		</div>
	</div>
</td></tr></table>
<br/>
<div id="errorOperation" class="add" title="Erreur"></div>
<div id="dialogOperation" class="add" title="Confirmation de suppression"></div>
<div id="copyBudgetOperation" class="add" title="Confirmation avant copie"></div>
<br/>