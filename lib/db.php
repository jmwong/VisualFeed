<?php
class Database {
	public function __construct($host,$usr,$pwd,$dbname){
		$link = mysql_pconnect($host,$usr, $pwd) or die('Could not connect: ' . mysql_error());
		mysql_select_db($dbname) or die('Could not select database');
	}
	public function query($q){
		$result = mysql_query($q) or die('Query failed: ' . mysql_error());
		return $result;
	}
	
	public function is_record_exist($q){
		return mysql_num_rows(self::query($q));
	}
	
	public function delete($table,$column,$value){
		self::query ("DELETE FROM $table WHERE $column=$value");
	}
	
}
?>