<?php
class DB {

	private static function connect() {
		
		try {

			if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') {
				$db = mysql_connect('localhost', 'root', '');
				mysql_set_charset('utf8', $db);
				mysql_select_db('budget',$db);
			}
			if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == '127.0.0.1') {
				$db = mysql_connect('127.0.0.1', 'root', '');
				mysql_set_charset('utf8', $db);
				mysql_select_db('budget',$db);
			}
			else {
				$db = mysql_connect('mysql.c3style.ch', 'budget', 'san11jc3');
				mysql_set_charset('utf8', $db);
				mysql_select_db('c3stylech1',$db);
			}
		}
		catch(Exception $e) {
			print_r($e);
			return false;
		}	
		return true;
	}
	
	private static function disconnect() {
		mysql_close();
	}
	
	public static function execute($sql, $insert = false) {
		
		if (self::connect()) {
		
			mysql_query("SET NAMES 'utf8';");
			mysql_query("SET CHARACTER SET 'utf8';");
		
			try {
				$result = mysql_query($sql);
				
				if ($insert)
					$result = mysql_insert_id();
			}
			catch(Exception $e) {
				print_r($e);
				$result = false;
			}
			
			self::disconnect();
			return $result;
		}
		else
			return false;
	}
}
?>