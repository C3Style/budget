<?php
include_once 'DB.class.php';
class _YearReport {

	// return array [operation_id][type_id][month]
	public static function loadByYearAndAccount($year, $account) {
	
		$sql  = "SELECT * FROM _year_report_v ";
		$sql .= "WHERE RE_YEAR = " . $year . " ";
		$sql .= "AND AC_ID = '" . $account . "' ";
		$return = array();
		$result = DB::execute($sql);
		if ($result) {
			while ($row = mysql_fetch_assoc($result)) {
				$return[$row['OP_ID']][$row['TY_ID']][$row['RE_MONTH']] = $row['TR_AMOUNT'];
			}
		}			
		return $return;
	}
}
?>