<?php
	session_start();

	require('lib/FPDF/fpdf.php');
	include_once 'classes/Account.class.php';
	include_once 'classes/Transaction.class.php';
	include_once 'classes/Operation.class.php';
	include_once 'classes/Texte.class.php';
	
	setlocale (LC_TIME, 'fr_FR');
	
	// Set date
	$date = date('Ym01');
	if (isset($_GET['date']))
		$date = $_GET['date'];
		
	// Set account
	$accountObj = Account::loadDefaultAccount();
	if (isset($_GET['account'])) {
		$account = $_GET['account'];
		$accountObj = Account::LoadById($account);
	}
	else if (is_object($accountObj))
		$account = $accountObj->getAccountId();
	else
		$account = '0';
	
	$month = (int)substr($date, 4, 2);
	
	function formatAmount($amount) {
		return number_format($amount, 2, '.', "'");
	}
	
	function truncate($text, $length) {
      $trunc = (strlen($text)>$length)?true:false;

      if($trunc)
         return substr($text, 0, $length).'...';
      else
         return $text;
   }
	
	class PDF extends FPDF
	{
		// Header
		function Header() {
			$this->Image('images/gold.png', 35, 5, 140);
			$this->Ln(25);
		}

		// Footer
		function Footer() {
			$this->SetY(-15);
			$this->SetTextColor(0);
			$this->SetFont('Helvetica','I',8);
			$this->Cell(0,10,utf8_decode(Texte::getText($_SESSION['lang'], 'page')).' '.$this->PageNo().'/{nb}',0,0,'C');
		}
		
		function createTable($table, $date)
		{
			$sizes = array();

			foreach($table->getRows() as $row) {
				
				$rowHeight = 5;
				$this->SetFont('Helvetica','',10);
			
				// Header
				if($row->getType() == 'header') {
					$this->SetFillColor(22,139,212); // blue
					$this->SetTextColor(255,255,255);
					$this->SetDrawColor(0,0,0);
					$this->SetLineWidth(.1);
					$this->SetFont('','B');
				}
			
				// Data
				if($row->getType() == 'data') {
					$this->SetFillColor(255,255,255);
					$this->SetTextColor(0);
					$this->SetFont('');
					$rowHeight = 7;
				}
				
				// Data Blue
				if($row->getType() == 'dataBlue') {
					$this->SetFillColor(208,227,250); // light blue
					$this->SetTextColor(0);
					$this->SetFont('','I');
				}
				
				// Data Yellow
				if($row->getType() == 'dataYellow') {
					$this->SetFillColor(255,232,139); // light yellow
					$this->SetTextColor(0);
					$this->SetFont('','I');
					$rowHeight = 5;
				}
				
				// Total
				if($row->getType() == 'total') {
					$this->SetFillColor(22,139,212); // blue
					$this->SetTextColor(255,255,255);
					$this->SetFont('','B');
				}
					
				$values = $row->getValues();
				$sizes = $row->getSizeOfColumn();
				$aligns = $row->getAlign();
				
				for($i=0;$i<count($values);$i++) {
					if($values[$i] < 0) {
						$this->SetTextColor(255, 0, 0);
						if ($row->getType() == 'total')
							$this->SetFillColor(255,195,195); // red
					}
					$this->Cell($sizes[$i],$rowHeight,$values[$i],1,0,$aligns[$i],true);
				}
				$this->Ln();
			}

			// End line
			$this->Cell(array_sum($sizes),0,'','T');
		}
	}
	
	class Row {
		private $type;
		private $sizeOfColumn;
		private $align;
		private $values;
		
		function Row($type, $sizeOfColumn, $align, $values) {
			$this->type = $type;
			$this->sizeOfColumn = $sizeOfColumn;
			$this->align = $align;
			$this->values = $values;
		}
		
		function getType() { return $this->type; }
		function getSizeOfColumn() { return $this->sizeOfColumn; }
		function getAlign() { return $this->align; }
		function getValues() { return $this->values; }
	}
	
	class Table {
		private $rows;
		private $index;
		
		function Table() {
			$this->index = 0;
		}
		
		function addRow($row) {
			$this->rows[$this->index++] = $row;
		}
		
		function getRows() {
			return $this->rows;
		}
	}
		
	$marginLeft = 10;
	$breakLine = 3;
	
	// Instanciation de la classe dérivée
	$pdf = new PDF();
	$pdf->AliasNbPages();
	$pdf->AddPage();
	
	$pdf->SetFont('Helvetica','B',18);
	$pdf->SetX($marginLeft); 
	$pdf->Cell(0, 10,utf8_decode(Texte::getText($_SESSION['lang'], 'rapport_financier_titre')) . ' ' . strftime('%B %Y', strtotime($date)), 0, 1);
	
	$pdf->SetFont('Helvetica','B',12);
	$pdf->SetX($marginLeft); 
	$pdf->Cell(0, 10,utf8_decode(Texte::getText($_SESSION['lang'], 'account')) . ' : ' . $accountObj->getAccountId() . ' - ' . $accountObj->getAccountName(), 0, 1);
	$pdf->Ln($breakLine);
	
	$pdf->SetFont('Helvetica','',10);
	$pdf->SetX($marginLeft); 
	$pdf->Cell(0, 4,utf8_decode(Texte::getText($_SESSION['lang'], 'solde_mois_passe')) . ' : ' . formatAmount(Transaction::getSolde($date, $account)), 0, 1);
	$pdf->Ln($breakLine);
	$pdf->SetX($marginLeft); 
	$pdf->Cell(0, 4,utf8_decode(Texte::getText($_SESSION['lang'], 'solde_mois')) . ' : ' . formatAmount(Transaction::getSolde(substr($date, 0, 4).(($month+1)<10?'0':'').($month+1).'01', $account)), 0, 1);
	$pdf->Ln($breakLine);
	$pdf->SetX($marginLeft); 
	$pdf->Cell(0, 4,utf8_decode(Texte::getText($_SESSION['lang'], 'solde_estime_mois')) . ' : ' . formatAmount((Transaction::getSolde(substr($date, 0, 4).'0101', $account) +
																												Transaction::getCredit(substr($date, 0, 4).(($month + 1 < 10)?'0':'').($month + 1).'01', 0, $account) -
																												Transaction::getEstimatedSolde(substr($date, 0, 4).(($month + 1 < 10)?'0':'').($month + 1).'01', $account, 1))), 0, 1);
	$pdf->Ln($breakLine * 2);
	
	/**************** CREDITS **********************/
	$pdf->SetFont('Helvetica','BU',12);
	$pdf->SetTextColor(0);
	$pdf->SetX($marginLeft); 
	$pdf->Cell(0, 4, utf8_decode(Texte::getText($_SESSION['lang'], 'credit')), 0, 1);
	$pdf->Ln($breakLine);
	
	$credits = Transaction::loadCreditByMonth($date, $account);
	$total = 0;
	$table = new Table();
	$table->addRow(new Row('header', array(160, 30), array('C', 'C'), array(utf8_decode(Texte::getText($_SESSION['lang'], 'operation')), 
																			utf8_decode(Texte::getText($_SESSION['lang'], 'credit')))));
	foreach($credits as $c) {
		$type = ($c->isPaid(substr($date, 4, 2), substr($date, 0, 4)))?'data':'dataYellow';
		$values = array();
		$calcDate = substr($c->getDate(),0 ,4).'-'.substr($date, 4, 2).'-'.substr($c->getDate(), 8, 2);
		$values[0] = truncate(strftime('%d/%m/%Y', strtotime($calcDate)).' : '.((utf8_decode($c->getRemark())!='')?utf8_decode($c->getRemark()):'-'), 70);
		$values[1] = formatAmount($c->getAmount());
		$table->addRow(new Row($type, array(160, 30), array('L', 'R'), $values));
		if($type == 'data')
			$total += $c->getAmount();
	}
	$table->addRow(new Row('total', array(160, 30), array('R', 'R'), array(utf8_decode(Texte::getText($_SESSION['lang'], 'total')).' : ', formatAmount($total))));
	$pdf->createTable($table, $date);
	$pdf->Ln($breakLine * 2);
	
	/**************** DEBITS WITH BUDGET **********************/
	$pdf->SetFont('Helvetica','BU',12);
	$pdf->SetTextColor(0);
	$pdf->SetX($marginLeft); 
	$pdf->Cell(0, 4, 'Débits', 0, 1);
	$pdf->Ln($breakLine);
	
	$transactions = Transaction::loadBudgetByMonth($date, $account);
	$totalBudget = 0;
	$totalDebit = 0;
	$totalDisposition = 0;
	$table = new Table();
	$table->addRow(new Row('header', array(100, 30, 30, 30), array('C', 'C', 'C', 'C'), array(utf8_decode(Texte::getText($_SESSION['lang'], 'operation')),
																							  utf8_decode(Texte::getText($_SESSION['lang'], 'budget')),
																							  utf8_decode(Texte::getText($_SESSION['lang'], 'debit')),
																							  utf8_decode(Texte::getText($_SESSION['lang'], 'disposition')))));
	foreach($transactions as $t) {
	
		// Create budget row
		$subTotal = 0;
		$subTotalUnpaid = 0;
		$debits = Transaction::loadDebiByMonthAndOperation($date, $t->getOperationId(), $account);
		foreach ($debits as $d)
			if($d->isPaid(substr($date, 4, 2), substr($date, 0, 4)))
				$subTotal += $d->getAmount();
			else
				$subTotalUnpaid += $d->getAmount();
		
		$op = Operation::loadById($t->getOperationId());
		$values = array();
		$values[0] = utf8_decode($op->getName());
		$values[1] = formatAmount($t->getAmount());
		$values[2] = formatAmount($subTotal);
		$values[3] = formatAmount($t->getAmount() - $subTotal - $subTotalUnpaid);
		
		$table->addRow(new Row('data', array(100, 30, 30, 30), array('L', 'R', 'R', 'R'), $values));
		$totalBudget += $t->getAmount();
		$totalDebit += $subTotal;
		$totalDisposition += $t->getAmount() - $subTotal - $subTotalUnpaid;
		
		// Create debit row
		foreach ($debits as $d) {
			$type = ($d->isPaid(substr($date, 4, 2), substr($date, 0, 4)))?'dataBlue':'dataYellow';
			$values = array();
			$dateToDisplay = substr($d->getDate(), 0, 4) . '-' . $month . '-'. substr($d->getDate(), 8, 2);
			$values[0] = '     '.truncate(strftime('%d/%m/%Y', strtotime($dateToDisplay)).' : '.((utf8_decode($d->getRemark())!="")?utf8_decode($d->getRemark()):'-'), 50);
			$values[1] = '';
			$values[2] = formatAmount($d->getAmount());
			$values[3] = '';
			$table->addRow(new Row($type, array(100, 30, 30, 30), array('L', 'R', 'R', 'R'), $values));
		}
	}

	/**************** DEBITS WITHOUT BUDGET **********************/
	$nonTransactions = Transaction::loadNonBudgetByMonth($date, $account);
	foreach($nonTransactions as $t) {
	
		$op = Operation::loadById($t->getOperationId());
		
		$values = array();
		$values[0] = utf8_decode($op->getName());
		$values[1] = '';
		$values[2] = formatAmount($t->getAmount());
		$values[3] = '';
		
		$table->addRow(new Row('data', array(100, 30, 30, 30), array('L', 'R', 'R', 'R'), $values));
		$totalDebit += $t->getAmount();
	}
	
	$table->addRow(new Row('total', array(100, 30, 30, 30), array('R', 'R', 'R', 'R'), array(utf8_decode(Texte::getText($_SESSION['lang'], 'total')).' : ', 
						   formatAmount($totalBudget), formatAmount($totalDebit), formatAmount($totalDisposition))));
	$pdf->createTable($table, $date);
	$pdf->Ln($breakLine);

	$pdf->Output();
?>