<?php
// Registrierung der Klassenverzeichnisse
set_include_path(
    get_include_path() . 
    PATH_SEPARATOR . "classes"
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
		include "classes/".$classname.".php";
	}

}

// Registrierung des Autoloaders
spl_autoload_register('Autoloader::load');
?>