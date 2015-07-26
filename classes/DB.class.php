<?php
class DB {

	private static $db;

	private static function connect() {
		
		try {

			if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') {
				self::$db = mysqli_connect('localhost', 'root', '');
				mysqli_set_charset(self::$db, 'utf8');
				mysqli_select_db(self::$db, 'budget');
			}
			if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == '127.0.0.1') {
				self::$db = mysqli_connect('127.0.0.1', 'root', '');
				mysqli_set_charset(self::$db, 'utf8');
				mysqli_select_db(self::$db, 'budget');
			}
			else {
				self::$db = mysqli_connect('mysql.c3style.ch', 'budget', 'san11jc3');
				mysqli_set_charset(self::$db, 'utf8');
				mysqli_select_db(self::$db, 'c3stylech1');
			}
		}
		catch(Exception $e) {
			print_r($e);
			return false;
		}	
		return true;
	}
	
	private static function disconnect() {
		mysqli_close(self::$db);
	}
	
	public static function execute($sql, $insert = false) {
		
		if (self::connect()) {
		
			mysqli_query(self::$db, "SET NAMES 'utf8';");
			mysqli_query(self::$db, "SET CHARACTER SET 'utf8';");
		
			try {
				$result = mysqli_query(self::$db, $sql);
				
				if ($insert)
					$result = mysqli_insert_id(self::$db);
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