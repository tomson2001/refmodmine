<?php
class FWDatabase {

	/**
	 * Hierbei handelt es um eine abgewandelte Erweiterung der MDB2-Klasse.
	 *
	 * @param String $name      Name der Funktion
	 * @param array  $arguments Die Argumente
	 * 
	 * @return mixed
	 */
	public static function __callStatic($name, $arguments) {
		global $db;
		if ( $name == 'query' || $name == 'exec' ) {
			if ( Config::ENABLE_DB_QUERY_LOGGING ) Logger::log($_SESSION['email'], $arguments[0], "ACCESS");
		}
		switch (count($arguments)) {
			case 1: return $db->$name($arguments[0]);
			case 2: return $db->$name($arguments[0], $arguments[1]);
			case 3: return $db->$name($arguments[0], $arguments[1], $arguments[2]);
			case 4: return $db->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
			
			default: Logger::log($_SESSION['email'], "Function-Call on FWDatabase with more than 4 arguments currently not possible. See classes/framework/FWDatabase Function __callStatic", "ERROR");
		}
	}
	
	/**
	 * Ermittelt die Struktur einer Datenbanktabelle.
	 * Unter Struktur sind hier die Spaltennamen und der Primärschlüssel zu verstehen.
	 * 
	 * @param String $tableName Name der Tabelle
	 * 
	 * @return array(columns => array(String), idFields => array(String)) - String sind jeweils die Spaltennamen
	 */
	public static function getTableStructure($tableName) {
		$res = self::query('SHOW COLUMNS FROM '.$tableName);
		$columnArr = array();
		$idFieldArr = array();
		while ( $row = $res->fetchRow() ) {
			array_push($columnArr, $row[0]);
			if ( $row[3] == 'PRI' ) {
				array_push($idFieldArr, $row[0]);
			}
		}
		$returnArr = array();
		$returnArr['columns'] = empty($columnArr) ? null : $columnArr;
		$returnArr['idFields'] = empty($idFieldArr) ? null : $idFieldArr;
		return $returnArr;
	}
	
	/**
	 * Ermittelt die Spalten einer Tabelle
	 * 
	 * @param String $tableName Name der Tabelle
	 * 
	 * @return array(String)
	 */
	public static function getColumns($tableName) {
		$tableStructure = self::getTableStructure($tableName);
		return $tableStructure['columns'];
	}
	
	/**
	 * Ermittelt den Primaerschluessel einer Tabelle
	 * 
	 * @param String $tableName Name der Tabelle
	 * 
	 * @return array(String)
	 */
	public static function getIdFields($tableName) {
		$tableStructure = self::getTableStructure($tableName);
		return $tableStructure['idFields'];
	}
	
	/**
	 * Prüft, ob eine Tabelle mit AutoIncrement arbeitet
	 * 
	 * @param String $tableName Name der Tabelle
	 * 
	 * @return bool
	 */
	public static function isAutoIncrement($tableName) {
		$nextAutoIncrementValue = self::getNextAutoIncrementValue($tableName);
		return is_null($nextAutoIncrementValue) ? false : true;
	}
	
	/**
	 * Ermittelt den naechsten AutoIncrement Wert einer Datenbanktabelle
	 * 
	 * @param String $tableName Name der Tabelle
	 * 
	 * @return int (falls kein AutoIncrement existiert, dann null)
	 */
	public static function getNextAutoIncrementValue($tableName) {
		$res = self::query("SELECT Auto_increment FROM information_schema.tables WHERE table_name = '".$tableName."' AND table_schema = '".Config::DB_DATABASE."'");
		$row = $res->fetchRow();
		return $row[0];
	}
	
	/**
	 * 
	 * 
	 * @param unknown_type $className
	 * @param unknown_type $tableName
	 */
	public static function oquery($className, $tableName) {
		
	}
	
}
?>