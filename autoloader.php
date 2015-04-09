<?php
// Registrierung der Klassenverzeichnisse
set_include_path(
    get_include_path() . 
    PATH_SEPARATOR . "classes" .  
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

}

// Registrierung des Autoloaders
spl_autoload_register('Autoloader::load');
?>