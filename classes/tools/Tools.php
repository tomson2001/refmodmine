<?php
class Tools {
	
	public static function getMaxValueHorizontal($matrix) {
		$maxValue = 0;
		foreach ( $matrix as $id2 => $value ) {
			if ( $value > $maxValue ) {
				$maxValue = $value;
			}
		}
		return $maxValue;
	}
	
	public static function getMaxValueVertical($matrix, $nodeID2) {
		$maxValue = 0;
		foreach ( $matrix as $id1 => $arr ) {
			foreach ( $arr as $id2 => $value ) {
				if ( $id2 == $nodeID2 && $value > $maxValue ) {
					$maxValue = $value;
				}
			}
		}
		return $maxValue;
	}
	
	public static function isNodePreciseMappableFromAtoB($matrix, $maxValue) {
		$countMatchingNodes = 0;
		foreach ( $matrix as $id2 => $value ) {
			if ( $value == $maxValue ) {
				$countMatchingNodes++;
			}
		}
		return $countMatchingNodes > 1 ? false : true;
	}
	
	public static function array_value_union() {
		$numArgs = func_num_args();
		$unionArr = array();
		for ( $i=0; $i<$numArgs; $i++ ) {
			$arr = func_get_arg($i);
			if ( !is_array($arr) ) return false;
			foreach ( $arr as $value ) {
				if ( !in_array($value, $unionArr) ) {
					array_push($unionArr, $value);
				}
			}
		}
		return $unionArr;
	}
	
	public static function array_unique_complex($array) {
		$newArray = array();
		foreach ( $array as $entry ) {
			array_push($newArray, serialize($entry));
		}
		$newArray = array_unique($newArray);
		
		$resultArray = array();
		foreach ( $newArray as $entry ) {
			array_push($resultArray, unserialize($entry));
		}
		return $resultArray;
	}
	
	public static function removeInvalidFunctions($allFuncs, $invalidFuncs) {
		foreach ( $allFuncs as $index => $funcDef ) {
			foreach ( $invalidFuncs as $node ) {
				if ( $funcDef["id"] == $node->id && $funcDef["EPC_InternalID"] == $node->epc->internalID ) unset($allFuncs[$index]);
			}
		}
		return $allFuncs;
	}
	
	public static function startsWith($haystack, $needle) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}
	
	public static function endsWith($haystack, $needle) {
		// search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
	}
	
	public static function replaceUnsupportedChars($text) {
		$text = str_replace("?", "", $text);
		$text = str_replace("!", "", $text);
		return $text;
	}
	
	public static function printMatrixToCLI($matrix) {
		print("\n\n");
		$columnLengths = array();
		$indexLength = 1;
		$overallLength = 0;
		foreach ( $matrix as $index => $values ) {
			if ( strlen($index) > $indexLength ) $indexLength = strlen($index)+1;
			$i = 0;
			foreach ( $values as $value ) {
				if ( !isset($columnLengths[$i]) ) $columnLengths[$i] = 2;
				if ( strlen($value) > $columnLengths[$i] ) $columnLengths[$i] = strlen($value)+2;
				$i++;
			}
		}
		$overallLength = $indexLength;
		foreach ( $columnLengths as $len ) {
			$overallLength += $len + 2;
		}
		
		foreach ( $matrix as $index => $values ) {
			print(" ".$index);
			$counter = strlen($index)+1;
			while ( $counter <= $indexLength ) {
				print(" ");
				$counter++;
			}
			print("| ");
			$i = 0;
			foreach ( $values as $value ) {
				print($value);
				$counter = strlen($value)+1;
				while ( $counter <= $columnLengths[$i] ) {
					print(" ");
					$counter++;
				}
				print("| ");
				$i++;
			}
			print("\n ");
			
			$counter = 0;
			while ( $counter <= $overallLength ) {
				print("-");
				$counter++;
			}
			print("\n");
		}
	}
	
	private static function getNumDendogramLeaves($arr) {
		$leaves = 0;
		foreach ( $arr as $element ) {
			if ( is_array($element) ) {
				$leaves += self::getNumDendogramLeaves($element);
			} else {
				$leaves++;
			}
		}
		return $leaves;
	}
	
	private static function getLongestDendogramLeaveLabelLength($arr) {
		$maxLen = 0;
		foreach ( $arr as $element ) {
			if ( is_array($element) ) {
				$maxSubLen = self::getLongestDendogramLeaveLabelLength($element);
				if ( $maxSubLen > $maxLen ) $maxLen = $maxSubLen;
			} else {
				if ( strlen($element) > $maxLen ) $maxLen = strlen($element); 
			}
		}
		return $maxLen;
	}
	
	/**
     * Return a gd handle with a visualization of the given dendrogram or null
     * if gd is not present.
     * 
     * @param filename path to the png
     * 
     * Retrieved from the test base of the NLP-Tools
     */
    public static function drawDendrogram($filename, $dendrogram, $w=300, $h=200)
    {
        $elements = self::getNumDendogramLeaves($dendrogram);
        $maxLeaveLabelLen = self::getLongestDendogramLeaveLabelLength($dendrogram);
    	
    	if (!function_exists('imagecreate'))
            return false;

        $im = imagecreatetruecolor($w,$h);
        $white = imagecolorallocate($im, 255,255,255);
        $black = imagecolorallocate($im, 0,0,0);
        $blue = imagecolorallocate($im, 0,0,255);
        imagefill($im, 0,0, $white);

        // padding 5%
        $padding = round(0.05*$w);
        // equally distribute
        $d = ($w-2*$padding)/$elements;
        $count_depth = function ($a) use (&$depth, &$count_depth) {
            if (is_array($a)) {
                return max(
                    array_map(
                        $count_depth,
                        $a
                    )
                ) + 1;
            } else {
                return 1;
            }
        };
        $depth = $count_depth($dendrogram)-1;
        $d_v = ($h-2*$padding)/$depth;

        // offset from bottom
        $y = $h-$padding;
        $left = $padding;

        $draw_subcluster = function ($dendrogram, &$left, $maxLeaveLabelLen) use (&$im, $d, $y, $d_v, $black, &$draw_subcluster,$blue) {
            if (!is_array($dendrogram)) {
                imagestringup($im, 1, $left-(0.5*strlen($dendrogram)), $y, $dendrogram, $black);
                $left += $d;

                return array($left - $d,$y-(5.5*$maxLeaveLabelLen));
            }
            list($l,$yl) = $draw_subcluster($dendrogram[0],$left, $maxLeaveLabelLen);
            list($r,$yr) = $draw_subcluster($dendrogram[1],$left, $maxLeaveLabelLen);
            $ym = min($yl,$yr)-$d_v;
            imageline($im, $l, $yl, $l, $ym, $blue);
            imageline($im, $r, $yr, $r, $ym, $blue);
            imageline($im, $l, $ym, $r, $ym, $blue);

            return array($l+($r-$l)/2,$ym);
        };

        if (count($dendrogram)==1)
            $draw_subcluster($dendrogram[0],$left, $maxLeaveLabelLen);
        else
            $draw_subcluster($dendrogram,$left, $maxLeaveLabelLen);
        
        imagepng($im, $filename);

        return $im;
    }
    
    public static function replaceDendogramLeaveLabels($dendogram, $replacements) {
    	foreach ( $dendogram as $index => $element ) {
    		if ( is_array($element) ) {
    			$dendogram[$index] = self::replaceDendogramLeaveLabels($element, $replacements);
    		} else {
    			$dendogram[$index] = $replacements[$element];
    		}
    	}
    	return $dendogram;
    }
	
}
?>