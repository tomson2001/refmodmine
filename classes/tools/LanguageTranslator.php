<?php
/**
 * Uses https://tech.yandex.com/translate/doc/dg/reference/translate-docpage/
 * Example HTTP Request: https://translate.yandex.net/api/v1.5/tr/translate?key=APIkey&lang=en-de&text=To+be,+or+not+to+be%3F&text=That+is+the+question
 * 
 * @author thaler
 *
 */
class LanguageTranslator {
	
	/**
	 * Possible language Combination can be found under the link above. 
	 * However, important for here is de-en and en-de
	 * 
	 * @param array $inputArray An array with text stringt which should be translated
	 * @return array $result the translated inputArray (same order)
	 */
	public static function translate($langCombination, $inputArray) {
		$uri = "https://translate.yandex.net/api/v1.5/tr/translate?key=".Config::YANDEX_API_KEY."&lang=".$langCombination."&text=";
		$uri .= implode("%3F&text=", array_values($inputArray));
		$serviceReturnXML = self::curl_get_contents($uri);
		try {
			$xml = new SimpleXMLElement($serviceReturnXML);
		} catch (Exception $e) {
			print("\nCatched XML-Read Error caused by uri: ".$uri."\n\n");
			return null;
		}
		$outputArray = array();
		$translation = $xml->xpath("//Translation");
		$translation = (array) $translation[0]->text;
		foreach ( $translation as $text ) {
			$text = ( substr($text, -1) == "?" ) ? substr($text, 0, -1) : $text;
			array_push($outputArray, $text);
		}
		$outputArrayWithOriginalIndizes = array();
		$index = 0;
		foreach ( $inputArray as $key => $value ) {
			$outputArrayWithOriginalIndizes[$key] = $outputArray[$index]; 
			$index++;
		}
		return $outputArrayWithOriginalIndizes;
	}
	
	public static function curl_get_contents($url)	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		$data = curl_exec($curl);
		curl_close($curl);
		return $data;
	}
	
	
}
?>