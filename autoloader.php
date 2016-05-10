<?php
// Registrierung der Klassenverzeichnisse
set_include_path(
    get_include_path() . 
    PATH_SEPARATOR . "classes" .
    PATH_SEPARATOR . "classes/extensions" .
    PATH_SEPARATOR . "classes/framework" .
    PATH_SEPARATOR . "classes/logging" .
    PATH_SEPARATOR . "classes/transformators" .
    PATH_SEPARATOR . "classes/miscellaneous" .
    PATH_SEPARATOR . "classes/nlp" .
    PATH_SEPARATOR . "classes/process-model" .
    PATH_SEPARATOR . "classes/process-model-behavior" .
    PATH_SEPARATOR . "classes/process-model-mapping" .
    PATH_SEPARATOR . "classes/process-model-similarity" .
    PATH_SEPARATOR . "classes/process-model-tools" .
    PATH_SEPARATOR . "classes/process-trace" .
    PATH_SEPARATOR . "classes/tools" .
    PATH_SEPARATOR . "classes/workspace" .
    PATH_SEPARATOR . "gui/classes" .
    PATH_SEPARATOR . "lib/AutomaticKeywordGenerator" .
    PATH_SEPARATOR . "lib/PHP-OpenCalais" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Analysis" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Classifiers" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Clustering" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Clustering/CentroidFactories" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Clustering/MergeStrategies" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Documents" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Exceptions" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/FeatureFactories" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Models" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Optimizers" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Random" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Random/Distributions" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Random/Generators" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Similarity" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Stemmers" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Tokenizers" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Utils" .
    PATH_SEPARATOR . "lib/PHP-NLP-Tools/src/NlpTools/Utils/Normalizers"
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
			$possibleIncludes[] = $path.str_replace('\\', '/', $classname).".php";	// Namespaces
			//print_r($possibleIncludes);
			$possibleIncludes[] = $path.$classname.".class.php";	// Konvention bei Smarty
			foreach ( $possibleIncludes as $possibleInclude ) {
				if ( file_exists($possibleInclude) ) {
					$includeFile = $possibleInclude;
					break;
				}
			}
			//print_r($possibleIncludes);
		}
		if ( $includeFile === false ) {

// 			if ( substr_count($classname, "\\") > 0 ) {
// 				$pos = strrpos($classname, "\\");
// 				$newClassname = substr($classname, $pos+1);
// 			//	print("try ".$newClassname."\n");
// 				self::complexLoad($newClassname);
// 			} else {
				exit('Class '.$classname.' could not be loaded. No such file or directory. See autoloader.php for more information.');
// 			}
		}
		include $includeFile;
	}

}

// Registrierung des Autoloaders
spl_autoload_register('Autoloader::complexLoad');
?>