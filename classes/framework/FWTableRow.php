<?php
/**
 * FWTableRow
 * 
 * Diese Klasse repräsentiert eine Zeile einer Datenbanktabelle. Für den im Konstruktur Ã¼bergebenen Tabellennamen
 * wird automatisch die Tabellenstruktur geladen (also alle enthaltenen Felder, sowie AutoIncrement und Primär-
 * schlüssel). Auf die Tabellenfelder kann dann direkt mit $object->fieldName zugegriffen werden. 
 * 
 * Die Klasse dient als Elternklasse für alle Tabellenobjekte, kann aber auch direkt instanziiert werden.
 * 
 * @author Tom Thaler
 */
class FWTableRow {
	
	private $tableName;     		  // Name der Datenkbanktabelle
	private $idFields;     		 	  // Primärschlüssel
	private $autoIncrement; 		  // Boolscher Wert, der aussagt, ob der Primärschlüssel auf AutoIncrement steht
	protected $isPositioned = false;  // Boolscher Wert, der aussagt, ob der Datensatz bereits in der Tabelle vorhanden ist
	
	private $data;
	
	/**
	 * Konstruktor
	 * 
	 * @param String $tableName Name der Datenbanktabelle
	 */
	public function __construct($tableName) {
		$this->tableName = $tableName;
		$tableStructure = FWDatabase::getTableStructure($tableName);
		foreach ( $tableStructure['columns'] as $columnName ) {
			$this->data[$columnName] = null;
		}
		$this->idFields = $tableStructure['idFields'];
		$this->autoIncrement = FWDatabase::isAutoIncrement($tableName);
	}
	
	/**
	 * @Override Magic Function __get
	 * Gibt die Werte der Tabellenspalten bzw. die Objektfelder zurÃ¼ck.
	 * 
	 * @param String $key SchlÃ¼sselwert (Feldname)
	 * 
	 * @return mixed
	 */
	public function __get($key) {
		if ( array_key_exists($key, $this->data) ) {
			return $this->data[$key];
		} else {
			return $this->$key;
		}
	}
	
	/**
	 * @Override Magic Function __set
	 * Setzt die Werte der Tabellenspalten bzw. der Objektfelder
	 * 
	 * @param String $key   Key
	 * @param mixed  $value Wert
	 * 
	 * @return no return
	 */
	public function __set($key, $value) {
		if ( array_key_exists($key, $this->data) ) {
			$this->data[$key] = $value;
		} else {
			$this->$key = $value;
		}
	}
	
	/**
	 * Speichert das aktuelle Objekt als neue Zeile in der Datenbanktabelle
	 * 
	 * @return bool
	 */
	public function insert() {
		if ( $this->autoIncrement ) {
			$insertFields = array_diff(array_keys($this->data), $this->idFields);
		} else {
			$insertFields = array_keys($this->data);
		}
		$insertValues = array();
		foreach ( $insertFields as $key ) {
			array_push($insertValues, $this->getSQLValue($key));
		}
		$sql = "INSERT INTO ".$this->tableName." (".implode(", ", $insertFields).") VALUES (".implode(", ", $insertValues).")";
		$insertId = $this->autoIncrement ? FWDatabase::getNextAutoIncrementValue($this->tableName) : null;
		$affected =& FWDatabase::exec($sql);
		if ( PEAR::isError($affected) ) {
			Logger::log($_SESSION['email'], 'Executing SQL-Insert failed: '.$sql." - Message: ".$affected->getMessage(), "ERROR");
			return false;
		} else {
			if ( $this->autoIncrement ) {
				$this->data[$this->idFields[0]] = $insertId;
			}
			$this->isPositioned = true;
			return true;
		}		
	} 
	
	/**
	 * Aktualisiert die Datenbanktabelle mit den Objektdaten
	 * 
	 * @return no return
	 */
	public function update() {
		$insertFields = array_diff(array_keys($this->data), $this->idFields);
		$sql = "UPDATE ".$this->tableName." SET ";
		$isFirst = true;
		foreach ( $insertFields as $key ) {
			if ( !$isFirst ) {
				$sql .= ", ";
			}
			$isFirst = false;
			$sql .= $key." = ".$this->getSQLValue($key); 
		}
		$sql .= " WHERE ";
		$isFirst = true;
		foreach ( $this->idFields as $key ) {
			if ( !$isFirst ) {
				$sql .= "AND ";
			}
			$isFirst = false;
			$sql .= $key." = ".$this->getSQLValue($key);
		}
		$affected =& FWDatabase::exec($sql);
		if ( PEAR::isError($affected) ) {
			Logger::log($_SESSION['email'], 'Executing SQL-Update failed: '.$sql." - Message: ".$affected->getMessage(), "ERROR");
			return false;
		} else {
			return true;
		}	
	}
	
	/**
	 * Gibt den Wert eines Datenbankfeldes so zurÃ¼ck, dass es
	 * im SQL-Statement verwendet werden kann.
	 * 
	 * @param String $key Key
	 * 
	 * @return String
	 */
	private function getSQLValue($key) {
		if ( array_key_exists($key, $this->data) ) {
			return is_null($this->data[$key]) ? 'null' : "'".$this->data[$key]."'";
		} else {
			throw new Exception("Field '".$key."' in table '".$this->tableName."' not exists.");
		}
	}
	
	/**
	 * Speichert das aktuelle Objekt in der Datenbank
	 * 
	 * @TODO Was wenn das Objekt kopiert wird und neu eingefÃ¼gt werden soll ... isPositioned?
	 * @TODO Was wenn das Object kopiert wird und ein anderes Ã¼berschreiben soll (id = andere id)
	 * 
	 * @return bool
	 */
	public function save() {
		$this->updateIsPositioned();
		return $this->isPositioned ? $this->update() : $this->insert();	
	}

	/**
	 * Aktualisiert $isPositioned. PrÃ¼ft also, ob ein Datensatz mit den
	 * hier angegebenen SchlÃ¼sseldaten in der Datenbank existiert oder nicht.
	 * 
	 * @return no return
	 */
	private function updateIsPositioned() {
		$sql = "SELECT COUNT(*) FROM ".$this->tableName." WHERE ";
		foreach ( $this->idFields as $index => $key ) {
			if ( is_null($this->data[$key]) ) {
				$this->isPositioned = false;
				return;
			}
			if ( $index > 0 ) {
				$sql .= " AND ";
			}
			$sql .= $key." = '".$this->data[$key]."'";
		}
		$res = FWDatabase::query($sql);
		if ( PEAR::isError($res) ) {
			Logger::log($_SESSION['email'], 'Executing SQL-Query failed: '.$sql." - Message: ".$res->getMessage(), "ERROR");
		} else {
			$row = $res->fetchRow();
			if ( $row[0] > 0 ) {
				$this->isPositioned = true;
			} else {
				$this->isPositioned = false;
			}
		}
	}
	
	/**
	 * LÃ¶scht das aktuelle Objekt aus der Datenbank
	 * 
	 * @return bool
	 */
	public function delete() {
		if ( !$this->isPositioned ) {
			return false;
		}
		$sql = "DELETE FROM ".$this->tableName." WHERE ";
		$isFirst = true;
		foreach ( $this->idFields as $key ) {
			if ( !$isFirst ) {
				$sql .= "AND ";
			}
			$isFirst = false;
			$sql .= $key." = ".$this->getSQLValue($key);
		}
		$affected =& FWDatabase::exec($sql);
		if ( PEAR::isError($affected) ) {
			Logger::log($_SESSION['email'], 'Executing SQL-Delete failed: '.$sql." - Message: ".$affected->getMessage(), "ERROR");
			return false;
		} else {
			$this->isPositioned = false;
			return true;
		}	
	}
	
	/**
	 * LÃ¤dt den Datensatz mit dem gegebenen PrimÃ¤rschlÃ¼ssel
	 * 
	 * WICHTIG: Der Funktion kÃ¶nnen beliebig viele Parameter Ã¼bergeben werden.
	 * Handelt es sich also beispielsweise um eine Tabelle mit einem
	 * PrimÃ¤rschlÃ¼ssel bestehend aus id1, id2 und id3, dann wÃ¼rde der
	 * Funktionsaufruf beispielsweise wie folgt aussehen: load(1,2,3);
	 * 
	 * @return bool
	 */
	public function load() {
		$sql = "SELECT * FROM ".$this->tableName." WHERE ";
		$countArguments = func_num_args();
		$arguments = func_get_args();
		if ( $countArguments < 1 ) return false;
		for ($i=0; $i<$countArguments; $i++) {
			if ( $i > 0 ) {
				$sql .= " AND ";
			}
			$sql .= $this->idFields[$i]." = '".$arguments[$i]."'";
		}
		$res = FWDatabase::query($sql);
		if ( PEAR::isError($res) ) {
			Logger::log($_SESSION['email'], 'Loading TableRow of Table '.$this->tableName.' failed: '.$sql." - Message: ".$res->getMessage(), "ERROR");
			return false;
		} else {
			if ( $row = $res->fetchRow(MDB2_FETCHMODE_ASSOC) ) {
				foreach ( array_keys($this->data) as $key ) {
					$this->__set($key, $row[$key]);
				}
				$this->isPositioned = true;
				return true;
			} else {
				//Logger::log($_SESSION['email'], 'Data of Table '.$this->tableName.' not found: '.$sql, "ERROR");
				return false;
			}
		}
	}
	
	/**
	 * Erstellt ein TableRow Object anhand dem Tabellennamen
	 * und des PrimÃ¤rschlÃ¼ssels. Hier kÃ¶nnen mehrere Parameter
	 * Ã¼bergeben werden, siehe dazu den Funktionskommentar
	 * von load()
	 * 
	 * @param String $tableName Name der Tabelle
	 * 
	 * @return FWTableRow
	 */
	public static function find($tableName) {
		$tableRow = new FWTableRow($tableName);
		$arguments = func_get_args();
		unset($arguments[0]);
		$evalString = "\$tableRow->load(".implode(',', $arguments).");";
		eval($evalString);
		return $tableRow;
	}
	
	/**
	 * Erstellt ein Object einer anhand des Klassennamen
	 * und des PrimÃ¤rschlÃ¼ssels. Hier kÃ¶nnen mehrere Parameter
	 * Ã¼bergeben werden, siehe dazu den Funktionskommentar
	 * von load(). Diese Funktion ist fÃ¼r ableitende Klassen 
	 * von FWTableRow gedacht. Die entsprechenden Klassen sind
	 * im Ordner /logic/data/tables/ zu finden.
	 * 
	 * @param String $className Klassenname
	 * 
	 * @return Objekt der Klasse $className, null im Fehlerfall
	 */
	public static function findObject($className) {
		$createObject = "\$object = new ".$className."();";
		eval($createObject);
		$arguments = func_get_args();
		unset($arguments[0]);
		$evalString = "\$object->load(".implode(',', $arguments).");";
		eval($evalString);
		return $object;
	}
	
}
?>