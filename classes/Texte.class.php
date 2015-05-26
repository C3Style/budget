<?php
include_once 'DB.class.php';
class Texte {

	private $id;
	private $langue;
	private $code;
	private $texte;
	
	private static function loadByRow($row) {
		if (!is_null($row)) {
			$texte = new Texte();
			$texte->id		= $row['TE_ID'];
			$texte->langue	= $row['TE_LANGUE'];
			$texte->code	= $row['TE_CODE'];
			$texte->texte	= $row['TE_TEXTE'];
			return $texte;
		}
		return false;
	}
	
	public static function getText($lang, $code) {
		
		$sql = "SELECT * FROM texte_t WHERE TE_LANGUE = '".$lang."' AND TE_CODE = '".$code."';";
		$result = DB::execute($sql);

		if ($result) {
			$row = mysql_fetch_assoc($result);
			$return = self::loadByRow($row);
			return $return->texte;
		}			
		return false;
	}
}
?>