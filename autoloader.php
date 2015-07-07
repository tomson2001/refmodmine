<?php
// Registrierung der Klassenverzeichnisse
set_include_path(
    get_include_path() . 
    PATH_SEPARATOR . "classes" .
    PATH_SEPARATOR . "classes/framework" .
    PATH_SEPARATOR . "classes/transformators" .
    PATH_SEPARATOR . "gui/classes"
);

/**
 * Autoloader
 * 
 * Laedt notwendige Klassen zur Laufzeit
 * 
 * @author Tom Thaler
 */
class Autoloader {
	
	/**
	 * Laedt eine Klasse
	 *
	 * @param String $classname Name der Klasse
	 */
	public static function load($classname) {
		include $classname.".php";
	}
	
	
	/**
	 * Laedt eine Klasse
	 *
	 * Hier k�nnen unterschiedliche Konventionen ber�cksichtigt werden
	 * Aktuell wird die Funktion load verwendet und nicht diese hier
	 *
	 * @param String $classname Name der Klasse
	 */
	public static function complexLoad($classname) {
		$includedPaths = explode(PATH_SEPARATOR, get_include_path());
		$includedPaths[] = "";
		$includeFile = false;
		foreach ( $includedPaths as $path ) {
			$path = empty($path) ? $path : $path."/";
			$possibleIncludes[] = $path.$classname.".php";	// Standard
			$possibleIncludes[] = $path.str_replace('_', '/', $classname).".php";	// Konvention bei einigen Pear-Paketen
			$possibleIncludes[] = $path.$classname.".class.php";	// Konvention bei Smarty
			foreach ( $possibleIncludes as $possibleInclude ) {
				if ( file_exists($possibleInclude) ) {
					$includeFile = $possibleInclude;
					break;
				}
			}
			if ( $includeFile ) {
				include $includeFile;
				break;
			}
		}
		if ( $includeFile === false ) exit('Class '.$classname.' could not be loaded. No such file or directory. See /setup/autoloader.php for more information.');
	}

}

// Registrierung des Autoloaders
spl_autoload_register('Autoloader::complexLoad');
?>