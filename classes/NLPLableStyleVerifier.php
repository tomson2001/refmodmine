<?php
/**
 * Checks labels for its labeling style based on Leopold, S. 53ff
 * 
 * @author thaler
 *
 */
class NLPLableStyleVerifier {
	
	// Pattern of HighLevelTags => Label Style Key (see NLPHighLevelTransformator)
	private $lableStyles = array(
		"{A}{N}" 		=> "A_VO", 				// Activity Labeling Style: Verb-Object VO				e.g. Create invoice
		"{A}{N}{IN}{N}" => "A_VO",				// "													e.g. send letter of rejection		NEW
		"{A}{IN}{N}{N}" => "A_VO",				// "													e.g. keep in applicatio pool		NEW
		"{A}{N}{N}" 	=> "A_VO",				// "													e.g. take aptitude test				NEW
		"{A}"			=> "A_AN",				// Activity Labeling Style: Action-Noun AN				e.g. Evaluate 						NEW
		""		 		=> "A_AN-np", 			// Activity Labeling Style: Action-Noun AN(np)			e.g. Invoice creation
		""		 		=> "A_AN-of",			// Activity Labeling Style: Action-Noun AN(of)			e.g. Creation of incoice
		""	 			=> "A_AN-gerund", 		// Activity Labeling Style: Action-Noun AN(gerund)		e.g. Creating incoice
		"" 				=> "A_AN-irregular",	// Activity Labeling Style: Action-Noun AN(irregular)	e.g. LIFO: Valuation: Pool level
		""				=> "A_DES",				// Activity Labeling Style: Descriptive DES				e.g. Clerk creates invoice
		""				=> "A_NA",				// Activity Labeling Style: No-Action NA				e.g. Error
		
		"{N}{AP}"		=> "E_PS",				// Event Labeling Style: Participle PS					e.g. Invoice created
		""				=> "E_MS",				// Event Labeling Style: Modal MS						e.g. Invoice must be created
		""				=> "E_AS",				// Event Labeling Style: Adjective AS					e.g. Invoice correct
		""				=> "E_CS",				// Event Labeling Style: Categorization CS				e.g. Customer is member
		""				=> "E_Irregular",		// Event Labeling Style: Irregular						e.g. Inquiry
		"{AP}"			=> "E_P",				// Event Labeling Style: Participle P					e.g. created
		
		""				=> "G_PQ",				// Gateway Labeling Style: Participle-Question PQ		e.g. Invoice created?
		""				=> "G_IFQ",				// Gateway Labeling Style: Infinitive-Question IFQ		e.g. Approve contract?
		""				=> "G_AQ",				// Gateway Labeling Style: Adjective-Question AQ		e.g. Parts available?
		""				=> "G_EQ",				// Gateway Labeling Style: Equation-Question EQ			e.g. Amount is greater than €200?
		""	 			=> "G_Irregular"		// Gateway Labeling Style: Irregular					e.g. Result?
	);
	
	public function __construct() {
		
	}
	
	/**
	 * getLableStyle
	 * 
	 * return the labeling style based on a tag set.
	 * 
	 * @param  string $tagSetString tag-set-string
	 * @return string Label Style Key
	 */
	public function getLableStyleKey($tagSetString) {
		foreach ( $this->lableStyles as $pattern => $styleKey ) {
			if ( $pattern == $tagSetString ) return $styleKey;
		}
		return false;
	}
	
}
?>