<?php 

header("Content-Type: text/xml"); 
header("Content-Disposition: attachment; filename=bilanAnnuel.xml");

include_once 'classes/_YearReport.class.php';
include_once 'classes/Type.class.php';
include_once 'classes/Operation.class.php';

setlocale (LC_TIME, 'fr_FR');

$year = $_GET['year'];
$account = $_GET['account'];
$data = _YearReport::loadByYearAndAccount($year, $account);

/*******************************************************************************
									CONTENT
*******************************************************************************/
$content = <<<EXCEL
   <Row ss:AutoFitHeight="0" ss:Height="3"/>
   <Row ss:Height="15">
    <Cell ss:StyleID="s18"><Data ss:Type="String">Opérations</Data></Cell>
    <Cell ss:StyleID="s19"/>
    <Cell ss:StyleID="s20"/>
EXCEL;

for ($i = 1; $i <= 12; $i++) {
	$content .= '<Cell ss:StyleID="s21"/>';
	$content .= '<Cell ss:StyleID="s22"><Data ss:Type="String">' . utf8_encode(strftime('%B', strtotime($year.'-'.$i.'-01'))) . '</Data></Cell>';
}
	
$content .= <<<EXCEL
    <Cell ss:StyleID="s24"/>
   </Row>
   <Row ss:AutoFitHeight="0" ss:Height="2.25">
   </Row>
EXCEL;

$formulaTotalBudget = ""; 
$formulaTotalDebit = ""; 
$countNbOperationForTotal = 0;

foreach ($data as $operationId => $types) {
	if ($operationId != 0) {
	
		/*********** Calculate the total ************/
		if ($countNbOperationForTotal != 0) {
			$formulaTotalBudget .= "+";
			$formulaTotalDebit .= "+";
		}
		
		$formulaTotalBudget .= 'R[-' . (3 + 2 * $countNbOperationForTotal) . ']C';
		$formulaTotalDebit .= 'R[-' . (4 + 2 * $countNbOperationForTotal) . ']C';
		$countNbOperationForTotal++;
		
		/******* Each type (budget + debit) *******/
		for ($t = 3; $t >= 1; $t-=2) {
			
			$opObj = Operation::loadById($operationId);
			$typeObj = Type::loadById($t);
				
			$content .= '<Row>';
			
			$debit = 'Débit';
			
			if ($t == 3) { // first row budget
				$content .= '<Cell ss:MergeDown="1" ss:StyleID="m47353300"><Data ss:Type="String">' . utf8_encode($opObj->getName()) . '</Data></Cell>';
				$content .= '<Cell ss:StyleID="s23"/>';
				$style = 's26';
			} else {
				$content .= '<Cell ss:Index="2" ss:StyleID="s23"/>';
				$style = 's28';
			}
			
			$content .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="String">' . utf8_encode($typeObj->getName()) . '</Data></Cell>';
			
			for ($i = 1; $i <= 12; $i++) { // each month
			
				$value = 0;
				if (isset($data[$operationId][$t][$i]))
					$value = $data[$operationId][$t][$i];
					
				$content .= '<Cell ss:StyleID="s21"/>';
				$content .= '<Cell ss:StyleID="' . $style . '"><Data ss:Type="Number">' . $value . '</Data></Cell>';
			}
			$content .= '</Row>';
			
		}
	}
}

$content .= <<<EXCEL
   <Row ss:AutoFitHeight="0" ss:Height="2.25" ss:StyleID="s29">
    <Cell ss:StyleID="s23"/>
    <Cell ss:StyleID="s23"/>
    <Cell ss:Index="5" ss:StyleID="s30"/>
    <Cell ss:Index="7" ss:StyleID="s30"/>
    <Cell ss:Index="9" ss:StyleID="s30"/>
    <Cell ss:Index="11" ss:StyleID="s30"/>
    <Cell ss:Index="13" ss:StyleID="s30"/>
    <Cell ss:Index="15" ss:StyleID="s30"/>
    <Cell ss:Index="17" ss:StyleID="s30"/>
    <Cell ss:Index="19" ss:StyleID="s30"/>
    <Cell ss:Index="21" ss:StyleID="s30"/>
    <Cell ss:Index="23" ss:StyleID="s30"/>
    <Cell ss:Index="25" ss:StyleID="s30"/>
    <Cell ss:Index="27" ss:StyleID="s30"/>
    <Cell ss:StyleID="s30"/>
   </Row>
   <Row ss:AutoFitHeight="0" ss:Height="21" ss:StyleID="s29">
    <Cell ss:StyleID="s31"><Data ss:Type="String">Total Budget</Data></Cell>
    <Cell ss:StyleID="s23"/>
	<Cell ss:StyleID="s21"/>
EXCEL;

for ($i = 1; $i <= 12; $i++) {
	$content .= '<Cell ss:StyleID="s21"/>';
	$content .= '<Cell ss:StyleID="s32" ss:Formula="=' . $formulaTotalBudget . '"><Data ss:Type="Number">0</Data></Cell>';
}

$content .= <<<EXCEL
  <Cell ss:StyleID="s33"/>
   </Row>
   <Row ss:AutoFitHeight="0" ss:Height="2.25">
    <Cell ss:StyleID="s23"/>
    <Cell ss:StyleID="s23"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s29"/>
   </Row>
   <Row ss:AutoFitHeight="0" ss:Height="21" ss:StyleID="s29">
    <Cell ss:StyleID="s34"><Data ss:Type="String">Total Débit</Data></Cell>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s35"/>
EXCEL;

for ($i = 1; $i <= 12; $i++) {
	$content .= '<Cell ss:StyleID="s35"/>';
	$content .= '<Cell ss:StyleID="s32" ss:Formula="=' . $formulaTotalDebit . '"><Data ss:Type="Number">0</Data></Cell>';
}
	
$content .= <<<EXCEL
    <Cell ss:StyleID="s33"/>
   </Row>
   <Row ss:AutoFitHeight="0" ss:Height="2.25" ss:StyleID="s29">
    <Cell ss:Index="5" ss:StyleID="s33"/>
    <Cell ss:Index="7" ss:StyleID="s33"/>
    <Cell ss:Index="9" ss:StyleID="s33"/>
    <Cell ss:Index="11" ss:StyleID="s33"/>
    <Cell ss:Index="13" ss:StyleID="s33"/>
    <Cell ss:Index="15" ss:StyleID="s33"/>
    <Cell ss:Index="17" ss:StyleID="s33"/>
    <Cell ss:Index="19" ss:StyleID="s33"/>
    <Cell ss:Index="21" ss:StyleID="s33"/>
    <Cell ss:Index="23" ss:StyleID="s33"/>
    <Cell ss:Index="25" ss:StyleID="s33"/>
    <Cell ss:Index="27" ss:StyleID="s33"/>
    <Cell ss:StyleID="s33"/>
   </Row>
   <Row ss:AutoFitHeight="0" ss:Height="21" ss:StyleID="s29">
    <Cell ss:StyleID="s34"><Data ss:Type="String">Total Crédit</Data></Cell>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s35"/>
EXCEL;
	
for ($i = 1; $i <= 12; $i++) { // each month

	$value = 0;
	if (isset($data[0][2][$i]))
		$value = $data[0][2][$i];
		
	$content .= '<Cell ss:StyleID="s35"/>';
	$content .= '<Cell ss:StyleID="s32"><Data ss:Type="Number">' . $value . '</Data></Cell>';
}
	
$content .= <<<EXCEL
    <Cell ss:StyleID="s33"/>
   </Row>
   <Row ss:AutoFitHeight="0" ss:Height="2.25">
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s35"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s33"/>
    <Cell ss:StyleID="s29"/>
   </Row>
   <Row ss:AutoFitHeight="0" ss:Height="21">
    <Cell ss:StyleID="s39"><Data ss:Type="String">Solde</Data></Cell>
    <Cell ss:StyleID="s36"/>
    <Cell ss:StyleID="s37"/>
EXCEL;

for ($i = 1; $i <= 12; $i++) {
	$content .= '<Cell ss:StyleID="s37"/>';
	$content .= '<Cell ss:StyleID="s40" ss:Formula="=R[-2]C-R[-4]C"><Data ss:Type="Number">0</Data></Cell>';
}
	
$content .= <<<EXCEL
    <Cell ss:StyleID="s38"/>
    <Cell ss:StyleID="s29"/>
   </Row>
   <Row>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s30"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s30"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s30"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s30"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s30"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s30"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s30"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s30"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s30"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s30"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s30"/>
    <Cell ss:StyleID="s29"/>
    <Cell ss:StyleID="s30"/>
    <Cell ss:StyleID="s30"/>
    <Cell ss:StyleID="s29"/>
   </Row>
   <Row ss:Index="2161">
    <Cell><Data ss:Type="String"> </Data></Cell>
   </Row>
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
   </PageSetup>
   <Print>
    <ValidPrinterInfo/>
    <PaperSizeIndex>9</PaperSizeIndex>
    <HorizontalResolution>600</HorizontalResolution>
    <VerticalResolution>600</VerticalResolution>
   </Print>
   <Zoom>85</Zoom>
   <Selected/>
   <FreezePanes/>
   <FrozenNoSplit/>
   <SplitHorizontal>7</SplitHorizontal>
   <TopRowBottomPane>7</TopRowBottomPane>
   <SplitVertical>3</SplitVertical>
   <LeftColumnRightPane>3</LeftColumnRightPane>
   <ActivePane>0</ActivePane>
   <Panes>
    <Pane>
     <Number>3</Number>
    </Pane>
    <Pane>
     <Number>1</Number>
    </Pane>
    <Pane>
     <Number>2</Number>
     <ActiveRow>4</ActiveRow>
    </Pane>
    <Pane>
     <Number>0</Number>
     <ActiveRow>24</ActiveRow>
     <ActiveCol>12</ActiveCol>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
 <Worksheet ss:Name="Feuil2">
  <Table ss:ExpandedColumnCount="1" ss:ExpandedRowCount="1" x:FullColumns="1"
   x:FullRows="1" ss:DefaultColumnWidth="60" ss:DefaultRowHeight="15">
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
   </PageSetup>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
 <Worksheet ss:Name="Feuil3">
  <Table ss:ExpandedColumnCount="1" ss:ExpandedRowCount="1" x:FullColumns="1"
   x:FullRows="1" ss:DefaultColumnWidth="60" ss:DefaultRowHeight="15">
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
   </PageSetup>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
</Workbook>
EXCEL;

$header = <<<EXCEL
<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>Christophe</Author>
  <LastAuthor>Christophe</LastAuthor>
  <Created>2010-11-06T13:17:01Z</Created>
  <LastSaved>2010-11-07T20:33:58Z</LastSaved>
  <Version>12.00</Version>
 </DocumentProperties>
 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
  <WindowHeight>7995</WindowHeight>
  <WindowWidth>10515</WindowWidth>
  <WindowTopX>120</WindowTopX>
  <WindowTopY>75</WindowTopY>
  <ProtectStructure>False</ProtectStructure>
  <ProtectWindows>False</ProtectWindows>
 </ExcelWorkbook>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="m47353280">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="m47353300">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s16">
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s17">
   <Alignment ss:Horizontal="Right" ss:Vertical="Bottom"/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0.00_ ;[Red]\-#,##0.00\ "/>
  </Style>
  <Style ss:ID="s18">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s19">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
    ss:Italic="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s20">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s21">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s22">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0.00_ ;[Red]\-#,##0.00\ "/>
  </Style>
  <Style ss:ID="s23">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s24">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0.00_ ;[Red]\-#,##0.00\ "/>
  </Style>
  <Style ss:ID="s25">
   <Borders>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s26">
   <Alignment ss:Horizontal="Right" ss:Vertical="Bottom"/>
   <Borders>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0.00_ ;[Red]\-#,##0.00\ "/>
  </Style>
  <Style ss:ID="s27">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s28">
   <Alignment ss:Horizontal="Right" ss:Vertical="Bottom"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0.00_ ;[Red]\-#,##0.00\ "/>
  </Style>
  <Style ss:ID="s29">
   <Borders/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s30">
   <Alignment ss:Horizontal="Right" ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0.00_ ;[Red]\-#,##0.00\ "/>
  </Style>
  <Style ss:ID="s31">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="12" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s32">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="12" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0.00_ ;[Red]\-#,##0.00\ "/>
  </Style>
  <Style ss:ID="s33">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="12" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0.00_ ;[Red]\-#,##0.00\ "/>
  </Style>
  <Style ss:ID="s34">
   <Alignment ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="12" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s35">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s36">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="12" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s37">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="14" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s38">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="14" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0.00_ ;[Red]\-#,##0.00\ "/>
  </Style>
  <Style ss:ID="s39">
   <Alignment ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="14" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s40">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="2"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="14" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0.00_ ;[Red]\-#,##0.00\ "/>
  </Style>
  <Style ss:ID="s41">
   <Alignment ss:Vertical="Bottom"/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s42">
   <Alignment ss:Vertical="Bottom"/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="16" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s43">
   <Alignment ss:Vertical="Bottom"/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="12" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s44">
   <Alignment ss:Horizontal="Left" ss:Vertical="Bottom"/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s45">
   <Alignment ss:Vertical="Bottom"/>
   <Font ss:FontName="Arial" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
    ss:Underline="Single"/>
   <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Feuil1">
  <Table ss:ExpandedColumnCount="29" ss:ExpandedRowCount="2161" x:FullColumns="1"
   x:FullRows="1" ss:StyleID="s16" ss:DefaultColumnWidth="60"
   ss:DefaultRowHeight="14.25">
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="161.25"/>
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="2.25"/>
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="42.75"/>
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="2.25"/>
   <Column ss:StyleID="s17" ss:AutoFitWidth="0" ss:Width="78"/>
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="2.25"/>
   <Column ss:StyleID="s17" ss:AutoFitWidth="0" ss:Width="77.25"/>
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="2.25"/>
   <Column ss:StyleID="s17" ss:AutoFitWidth="0" ss:Width="77.25"/>
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="2.25"/>
   <Column ss:StyleID="s17" ss:AutoFitWidth="0" ss:Width="77.25"/>
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="2.25"/>
   <Column ss:StyleID="s17" ss:AutoFitWidth="0" ss:Width="77.25"/>
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="2.25"/>
   <Column ss:StyleID="s17" ss:AutoFitWidth="0" ss:Width="77.25"/>
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="2.25"/>
   <Column ss:StyleID="s17" ss:AutoFitWidth="0" ss:Width="77.25"/>
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="2.25"/>
   <Column ss:StyleID="s17" ss:AutoFitWidth="0" ss:Width="77.25"/>
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="2.25"/>
   <Column ss:StyleID="s17" ss:AutoFitWidth="0" ss:Width="77.25"/>
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="2.25"/>
   <Column ss:StyleID="s17" ss:AutoFitWidth="0" ss:Width="77.25"/>
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="2.25"/>
   <Column ss:StyleID="s17" ss:AutoFitWidth="0" ss:Width="77.25"/>
   <Column ss:StyleID="s16" ss:AutoFitWidth="0" ss:Width="2.25"/>
   <Column ss:StyleID="s17" ss:AutoFitWidth="0" ss:Width="77.25"/>
   <Column ss:StyleID="s17" ss:AutoFitWidth="0" ss:Width="87.75"/>
   <Row ss:AutoFitHeight="0" ss:Height="20.25">
    <Cell ss:StyleID="s42"><Data ss:Type="String">BUDGET - Rapport annuel</Data></Cell>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
   </Row>
   <Row ss:AutoFitHeight="0" ss:Height="2.25">
    <Cell ss:StyleID="s42"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
   </Row>
   <Row ss:AutoFitHeight="0" ss:Height="15">
    <Cell ss:StyleID="s43"><Data ss:Type="String">Année : $year</Data></Cell>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s45"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s44"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
   </Row>
   <Row ss:AutoFitHeight="0" ss:Height="15">
    <Cell ss:StyleID="s43"><Data ss:Type="String">Compte : $account</Data></Cell>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s45"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
    <Cell ss:StyleID="s41"/>
   </Row>
EXCEL;

echo $header.$content;
?>