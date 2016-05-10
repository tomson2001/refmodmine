<?php
/**
 * Checks labels for its labeling style based on Leopold, S. 53ff
 * 
 * @author thaler
 *
 */
class NLPLableStyleVerifierEN extends ANLPLabelStyleVerifier {
	
	// Pattern of HighLevelTags => Label Style Key (see NLPHighLevelTransformator)
	protected $lableStyles = array(				
		"{A}{N}" 		=> 	array(						// Activity Labeling Style: Verb-Object VO				e.g. Create invoice
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0), // the index of the relevant high level tag, here {A}
								"object"	=> array(1),
								"toNoun"	=> null
							),
		"{A}{N}{N}" 		=> 	array(						// Activity Labeling Style: Verb-Object VO				e.g. process acknoledgement certificate
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0), // the index of the relevant high level tag, here {A}
								"object"	=> array(1),
								"toNoun"	=> null
							),
		"{A}{IN}{D}" 		=> 	array(						// Activity Labeling Style: Verb-Object VO				e.g. checking if complete
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0), // the index of the relevant high level tag, here {A}
								"object"	=> array(2),
								"toNoun"	=> null
							),
		"{A}{N}{D}" 		=> 	array(						// Activity Labeling Style: Verb-Object VO				e.g. check application complete
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0), // the index of the relevant high level tag, here {A}
								"object"	=> array(1),
								"toNoun"	=> null
							),
							
							
		"{A}{TO}{DT}{N}{N}" 		=> 	array(						// Activity Labeling Style: Verb-Object VO				e.g. invite to an aptitude test
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0), // the index of the relevant high level tag, here {A}
								"object"	=> array(3,4),
								"toNoun"	=> null
							),
		"{A}{DT}{A}{N}" 		=> 	array(						// Activity Labeling Style: Verb-Object VO				e.g. Providing the missing documents
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0), // the index of the relevant high level tag, here {A}
								"object"	=> array(2,3),
								"toNoun"	=> null
							),
		"{A}{DT}{AP}{N}" 		=> 	array(						// Activity Labeling Style: Verb-Object VO				e.g. Receiving the written applications
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0), // the index of the relevant high level tag, here {A}
								"object"	=> array(2,3),
								"toNoun"	=> null
							),
		"{A}{N}{IN}{N}{IN}{CD}{TO}{CD}" 		=> 	array(						// Activity Labeling Style: Verb-Object VO				e.g. Rank application on scale from 1 to 10 
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0), // the index of the relevant high level tag, here {A}
								"object"	=> array(1),
								"toNoun"	=> null
							),
		"{A}{N}{IN}{TO}{A}{N}" 		=> 	array(						// Activity Labeling Style: Verb-Object VO				e.g. Hand application over to examining board 
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0), // the index of the relevant high level tag, here {A}
								"object"	=> array(1),
								"toNoun"	=> array(4,5)
							),
		"{A}{D}{DT}{N}{N}{N}{N}" 		=> 	array(						// Activity Labeling Style: Verb-Object VO				e.g. Print out the online application form (GER)
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0), // the index of the relevant high level tag, here {A}
								"object"	=> array(3,4,5),
								"toNoun"	=> null
							),
		"{A}{DT}{DT}{N}{TO}{DT}{D}{N}{IN}{N}" 		=> 	array(						// Activity Labeling Style: Verb-Object VO				e.g. Send all the requirements to the secretarial office for students
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0), // the index of the relevant high level tag, here {A}
								"object"	=> array(3),
								"toNoun"	=> array(6,7)
							),
		"{A}{N}{IN}" 		=> 	array(						// Activity Labeling Style: Verb-Object VO				e.g. do it without
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0), // the index of the relevant high level tag, here {A}
								"object"	=> array(1),
								"toNoun"	=> null
							),
		
		"{A}{AP}{N}" 		=> 	array(						// Activity Labeling Style: Verb-Object VO				e.g. process acknowledged certificate
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0), // the index of the relevant high level tag, here {A}
								"object"	=> array(2),
								"toNoun"	=> null
							),
		"{A}{N}{IN}{N}" => array(						// "													e.g. send letter of congratulation		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1,2,3),
								"toNoun"	=> null
							),
		"{A}{TO}{N}{IN}{N}" => array(						// "													e.g. redirect to office of professor		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2,3,4),
								"toNoun"	=> null
							),
		"{A}{N}{N}{TO}{N}" => array(						// "													e.g. send birth certificate to office		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1,2),
								"toNoun"	=> array(4)
							),
		"{A}{N}{TO}{N}{N}" => array(						// "													e.g. send documents to selection committee		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1),
								"toNoun"	=> array(3,4)
							),
		"{A}{N}{D}{D}{N}" => array(						// "													e.g. Register baby as russian citizen		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1),
								"toNoun"	=> null
							),
		"{A}{DT}{N}{N}{IN}{N}{N}" => array(						// "													e.g. Getting a citizenship stamp on birth certificate		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2,3),
								"toNoun"	=> null
							),
		"{A}{TO}{DT}{N}" => array(						// "													e.g. go to the school		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(3),
								"toNoun"	=> null
							),
		"{A}{DT}{N}{D}{N}{N}" => array(						// "													e.g. Send the request about money benefit		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(3),
								"toNoun"	=> null
							),
		"{A}{TO}{N}" => array(						// "													e.g. go to school		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2),
								"toNoun"	=> null
							),
		"{A}{DT}{N}" => array(						// "													e.g. do the thing		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2),
								"toNoun"	=> null
							),
		"{A}{IN}{N}{N}" => array(						// "													e.g. keep in applicatio pool		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2,3),
								"toNoun"	=> null
							),
		"{A}{IN}{N}{N}{N}" => array(						// "													e.g. fill in birth registration form		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0,1),
								"object"	=> array(2,3,4),
								"toNoun"	=> null
							),
		"{A}{D}{N}{N}" => array(						// "													e.g. fill out registration form		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2,3),
								"toNoun"	=> null
							),
		"{A}{IN}{AP}{N}{IN}{N}{IN}{D}{N}" => array(						// "													e.g. Fill in printed form of application for international students 		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2,3,4,5),
								"toNoun"	=> null
							),
		"{A}{IN}{N}{N}{IN}{N}" => array(						// "													e.g. Fill in online form of application 		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2,3,4,5),
								"toNoun"	=> null
							),
		"{A}{N}{IN}{D}{N}" => array(						// "													e.g. Send letter of provisional acceptance 		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1,2,3,4),
								"toNoun"	=> null
							),
		"{A}{N}{IN}{N}{CD}" => array(						// "													e.g. register employee as outlier 1		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1),
								"toNoun"	=> null
							),
		"{A}{N}{D}{N}{CD}" => array(						// "													e.g. register employee as outlier 1		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1),
								"toNoun"	=> null
							),
		"{A}{IN}{D}{N}{CD}" => array(						// "													e.g. decide on first name 1		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2,3),
								"toNoun"	=> null
							),
		"{A}{D}{IN}{N}{N}" => array(						// "													e.g. decide together on first name		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(3,4),
								"toNoun"	=> null
							),
		"{A}{D}{IN}{N}" => array(						// "													e.g. decide together on name		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(3),
								"toNoun"	=> null
							),
		"{A}{D}{D}{N}" => array(						// "													e.g. request new first name		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2,3),
								"toNoun"	=> null
							),
		"{D}{A}" => array(						// "													e.g. open proceeding		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(1),
								"object"	=> null,
								"toNoun"	=> null
							),
		"{A}{IN}{CD}{N}" => array(						// "													e.g. Registration in one's place		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(3),
								"toNoun"	=> null
							),
		"{A}{D}{N}{D}" => array(						// "													e.g. Apply decent right (single)		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2),
								"toNoun"	=> null
							),
		"{A}{D}{N}" => array(						// "													e.g. Apply decent rig		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2),
								"toNoun"	=> null
							),
		"{A}{IN}{N}{N}" => array(						// "													e.g. Registration in children's home		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(3),
								"toNoun"	=> null
							),
		"{A}{IN}{N}{IN}{N}{N}" => array(						// "													e.g. Trial in court of International Affairs		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2,3,4,5),
								"toNoun"	=> null
							),
		"{A}{N}{IN}{N}{IN}{N}" => array(						// "													e.g. Receive rejection for course of studies		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1,2,3,4,5),
								"toNoun"	=> null
							),
		"{A}{N}{TO}{N}{IN}{D}{N}" => array(						// "													e.g. Send information to Bürgeramt via internal IT		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1),
								"toNoun"	=> array(3)
							),
		"{A}{N}{N}{TO}{D}{D}{D}{N}" => array(						// "													e.g. Send birth certificate to respective local public office		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1,2),
								"toNoun"	=> array(7)
							),
		"{A}{IN}{N}{N}{IN}{N}{A}{D}{N}{N}" => array(						// "													e.g. decide on family name if parents have different family names		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2,3),
								"toNoun"	=> null
							),
		"{A}{N}{N}{AP}{IN}{N}{IN}{N}{IN}{N}" => array(						// "													e.g.  Decide family name based on law of nationality of child		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1,2),
								"toNoun"	=> null
							),
		"{A}{N}{IN}{D}{AP}{N}" => array(						// "													e.g. check information of new born child		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1),
								"toNoun"	=> null
							),
		"{A}{N}{IN}{N}{N}{N}" => array(						// "													e.g. check application in time EU countries		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1,2,3,4,5),
								"toNoun"	=> null
							),
		"{A}{N}{IN}{N}{N}" => array(						// "													e.g. Add certificate of bachelor degree 		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1,2,3,4),
								"toNoun"	=> null
							),
		"{A}{N}{IN}{D}{N}{N}" => array(						// "													e.g. Add certificates of further language skills 		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1,2,3,4,5),
								"toNoun"	=> null
							),
		"{A}{N}{IN}{N}{D}{N}" => array(						// "													e.g. check application in time non-EU countries		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1,2,3,4,5),
								"toNoun"	=> null
							),
		"{A}{D}{N}{AP}{IN}{D}{N}" => array(						// "													e.g. decide first name based on internation law		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1,2),
								"toNoun"	=> null
							),
		"{A}{IN}{D}{N}{AP}{IN}{D}{N}" => array(						// "													e.g. decide first name based on internation law		NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2,3),
								"toNoun"	=> null
							),
		"{A}{N}{N}" 	=> array(						// "													e.g. take aptitude test				NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1,2),
								"toNoun"	=> null
							),
		"{A}{N}{CD}" 	=> array(						// "													e.g. check test 1				NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1),
								"toNoun"	=> null
							),
		"{A}{D}{N}" 	=> array(						// "													e.g. decide				NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1,2),
								"toNoun"	=> null
							),
		"{A}{IN}{N}" 	=> array(						// "													e.g. wait for results				NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2),
								"toNoun"	=> null
							),
		"{A}{IN}{DT}{N}" 	=> array(						// "													e.g. wait for the results				NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(3),
								"toNoun"	=> null
							),
		"{A}{DT}{N}{N}" 	=> array(						// "													e.g. formulize the annual report				NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(2,3),
								"toNoun"	=> null
							),
		"{A}{AP}{N}{IN}{N}" 	=> array(						// "													e.g. give married name of parents				NEW
								"style" 	=> "A_VO",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> array(1,2),
								"toNoun"	=> null
							),
		"{A}"			=> array(															// Activity Labeling Style: Action-Noun AN				e.g. Evaluate 						NEW
								"style" 	=> "A_AN",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> null,
								"toNoun"	=> null
							),
		"{N}{A}{N}" 	=> array(						// "													e.g. Clerk creates invoice				NEW
								"style" 	=> "A_DDES",
								"subject"	=> array(0),
								"verb"		=> array(1),
								"object"	=> array(2),
								"toNoun"	=> null
							),
		"{N}{A}{N}{N}" 	=> array(						// "													e.g. mother chooses family name				NEW
								"style" 	=> "A_DDES",
								"subject"	=> array(0),
								"verb"		=> array(1),
								"object"	=> array(2,3),
								"toNoun"	=> null
							),
		"{N}{A}{DT}{N}" 	=> array(						// "													e.g. mother chooses family name				NEW
								"style" 	=> "A_DDES",
								"subject"	=> array(0),
								"verb"		=> array(1),
								"object"	=> array(2,3),
								"toNoun"	=> null
							),
		""		 		=> "A_AN-np", 					// Activity Labeling Style: Action-Noun AN(np)			e.g. Invoice creation
		""		 		=> "A_AN-of",					// Activity Labeling Style: Action-Noun AN(of)			e.g. Creation of incoice
		""	 			=> "A_AN-gerund", 				// Activity Labeling Style: Action-Noun AN(gerund)		e.g. Creating incoice
		"" 				=> "A_AN-irregular",			// Activity Labeling Style: Action-Noun AN(irregular)	e.g. LIFO: Valuation: Pool level
		""				=> "A_DES",						// Activity Labeling Style: Descriptive DES				e.g. Clerk creates invoice
		""				=> "A_NA",						// Activity Labeling Style: No-Action NA				e.g. Error
		
		"{N}{AP}"		=> array(						// Event Labeling Style: Participle PS					e.g. Invoice created
								"style" 	=> "E_PS",
								"subject"	=> null,
								"verb"		=> array(1),
								"object"	=> array(0)
							),
		""				=> "E_MS",						// Event Labeling Style: Modal MS						e.g. Invoice must be created
		""				=> "E_AS",						// Event Labeling Style: Adjective AS					e.g. Invoice correct
		""				=> "E_CS",						// Event Labeling Style: Categorization CS				e.g. Customer is member
		""				=> "E_Irregular",				// Event Labeling Style: Irregular						e.g. Inquiry
		"{AP}"			=> array(						// Event Labeling Style: Participle P					e.g. created
								"style" 	=> "E_P",
								"subject"	=> null,
								"verb"		=> array(0),
								"object"	=> null
							),
				
		""				=> "G_PQ",						// Gateway Labeling Style: Participle-Question PQ		e.g. Invoice created?
		""				=> "G_IFQ",						// Gateway Labeling Style: Infinitive-Question IFQ		e.g. Approve contract?
		""				=> "G_AQ",						// Gateway Labeling Style: Adjective-Question AQ		e.g. Parts available?
		""				=> "G_EQ",						// Gateway Labeling Style: Equation-Question EQ			e.g. Amount is greater than �200?
		""	 			=> "G_Irregular"				// Gateway Labeling Style: Irregular					e.g. Result?
	);
	
}
?>