<?php
/**
 * Konfigurationsklasse
 * 
 * @author Tom Thaler
 *
 */
final class Config {
	
	const WORDNET_EXE = "wordnet";
	//const WORDNET_EXE = "\"c:\\Program Files (x86)\\WordNet\\2.1\\bin\\wn.exe\"";
	const WORDNET_SYNONYM_LIMIT = 7; // Anzahl der zu beruecksichtigen Bedeutungen. Ersten Tests zufolge mindestens 7, MUSS < 10 sein!!!!
	
	const NUM_CORES_TO_WORK_ON = 1;
	
	// MOBIS SS11
	//const MODEL_FILE_1 = "P:/Projekte/RefModMiner/Data_old/data_kontrollierte_modellierung/Klausuren_MobIS_SS11/loesungen.epml";
	//const MODEL_FILE_2 = "P:/Projekte/RefModMiner/Data_old/data_kontrollierte_modellierung/Klausuren_MobIS_SS11/loesungen.epml";
	
	// Kontrollierte Modellierung das Grosse!
	//const MODEL_FILE_1 = "C:/Users/t.thaler/Dropbox/_arbeitsverzeichnis_nils/data/Kontrollierte Modellierung/Kontrollierte Modellierung.epml";
	//const MODEL_FILE_2 = "C:/Users/t.thaler/Dropbox/_arbeitsverzeichnis_nils/data/Kontrollierte Modellierung/Kontrollierte Modellierung.epml";
	
	// Winfo-Klausuren
	//const MODEL_FILE_1 = "P:/Projekte/RefModMiner/Data/Kontrollierte Modellierung/Klausuren/EPML/WInfo II WS1011.epml";
	//const MODEL_FILE_2 = "P:/Projekte/RefModMiner/Data/Kontrollierte Modellierung/Klausuren/EPML/WInfo II WS1011.epml";
	//const MODEL_FILE_1 = "P:/Projekte/RefModMiner/Data/Kontrollierte Modellierung/Klausuren/EPML/Winfo II SS11.epml";
	//const MODEL_FILE_2 = "P:/Projekte/RefModMiner/Data/Kontrollierte Modellierung/Klausuren/EPML/Winfo II SS11.epml";
	//const MODEL_FILE_1 = "P:/Projekte/RefModMiner/Data/Kontrollierte Modellierung/Klausuren/EPML/Winfo II WS1112.epml";
	//const MODEL_FILE_2 = "P:/Projekte/RefModMiner/Data/Kontrollierte Modellierung/Klausuren/EPML/Winfo II WS1112.epml";
		
	// Mapping Simulator
	//const MODEL_FILE_1 = "P:/Projekte/RefModMiner/HudsonBuilds/data/refmodmine.epml";
	//const MODEL_FILE_2 = "P:/Projekte/RefModMiner/HudsonBuilds/data/refmodmine.epml";
	
	// Modelle aus Becker-Papier
	//const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/becker_survey.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/becker_survey.epml";
	
	// Modelle des Process Matching Contest
	//const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Handels-H-Model_1996_original_noSEQ.empl";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/handels-h-model_2004_original_noSEQ.epml";

	//const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Y-CIM_de_original.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Y-CIM_3.0.epml";
	
	// IWi Process Model Corpus
 	//const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/ECO-Integral_adapted.epml";
    const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/E-Payment.epml";
    //const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/Gewerbemeldungen.epml";
    //const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/Handels-H-Model_1996_original_noSEQ.epml";
	//const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/handels-h-model_2004_original_noSEQ.epml";
// 	const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/Klausuren Winfo II SS11.epml";
// 	const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/Klausuren Winfo II WS1011.epml";
	//const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/Klausuren Winfo II WS1112.epml";
    //const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/SAP R3 prozessorientiert anwenden_adapted.epml";
	//const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/SAP_ALL_EPCs_named.epml";
// 	const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/Vogelaar.epml";
// 	const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/Y-CIM_3.0.epml";
	//const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/Y-CIM_de_adapted.epml";
// 	const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Y-CIM_en_adapted.epml";
    //const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/CustomB2B-ATW.epml";
    //const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/CustomB2B-Hype.epml";
    //const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/GK_Rewe_adapted.epml";
    //const MODEL_FILE_1 = "/opt/refmodmine/input/epml/ipmc/ITIL.epml";
    //const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/pmc_contest.epml";
	
// 	const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/ECO-Integral_adapted.epml";
 	//const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/E-Payment.epml";
 	const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/Gewerbemeldungen.epml";
    //const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/Handels-H-Model_1996_original_noSEQ.epml";
    //const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/handels-h-model_2004_original_noSEQ.epml";
// 	const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/Klausuren Winfo II SS11.epml";
	//const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/Klausuren Winfo II WS1011.epml";
// 	const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/Klausuren Winfo II WS1112.epml";
    //const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/SAP R3 prozessorientiert anwenden_adapted.epml";
	//const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/SAP_ALL_EPCs_named.epml";
	//const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/Vogelaar.epml";
// 	const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/Y-CIM_3.0.epml";
    //const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/Y-CIM_de_adapted.epml";
	//const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/Y-CIM_en_adapted.epml";
    //const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/CustomB2B-ATW.epml";
    //const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/CustomB2B-Hype.epml";
    //const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/GK_Rewe_adapted.epml";
    //const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/ITIL.epml";
    //const MODEL_FILE_2 = "/opt/refmodmine/input/epml/ipmc/pmc_contest.epml";
	
	//-------------------------------------------------------
	
	//const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate.epml";
	//const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate_p31.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate.epml";
	//const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission.epml";
	//const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission Cologne.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission.epml";
	
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission Frankfurt.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission FU Berlin.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission Hohenheim.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission IIS Erlangen.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission Muenster.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission Potsdam.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission TU Munich.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission Wuerzburg.epml";
	
	// SAP Referenzmodell
	//const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell.epml";
	
	//const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell_Part1.epml";
	//const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell_Part2.epml";
	//const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell_Part3.epml";
	//const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell_Part4.epml";
	
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell_Part1.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell_Part2.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell_Part3.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell_Part4.epml";
	
	// eGov - Gewerbeanmeldung
	//const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/Gewerbeanmeldung.epml";
	//const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/Gewerbeanmeldung.epml";
	
	// Model Analyzer
	//const MODEL_ANALYSIS_FILE = "P:/Projekte/RefModMiner/HudsonBuilds/data/becker_survey.epml";
	//const MODEL_ANALYSIS_FILE = "C:/Users/t.thaler/Documents/IWi/Eigene Paper/in Arbeit/Modell\E4hnlichkeit/Modelle/SAP.epml";
	//const MODEL_ANALYSIS_FILE = "P:/Projekte/RefModMiner/HudsonBuilds/data/refmodmine.epml";
	//const MODEL_ANALYSIS_FILE = "P:/Projekte/RefModMiner/Data/Kontrollierte Modellierung/Kontrollierte Modellierung.epml";
	//const MODEL_ANALYSIS_FILE = "P:/Projekte/RefModMiner/Data/Kontrollierte Modellierung/Klausuren/EPML/WInfo II WS1011.epml";
	//const MODEL_ANALYSIS_FILE = "P:/Projekte/RefModMiner/Data/Kontrollierte Modellierung/Klausuren/EPML/Winfo II SS11.epml";
	//const MODEL_ANALYSIS_FILE = "P:/Projekte/RefModMiner/Data/Kontrollierte Modellierung/Klausuren/EPML/Winfo II WS1112.epml";
	//const MODEL_ANALYSIS_FILE = "C:/Users/t.thaler/Dropbox/_arbeitsverzeichnis_nils/data/RMK_erfassung/Handels-H/Handels-H-Model_base.epml";
	//const MODEL_ANALYSIS_FILE = "C:/Users/t.thaler/Dropbox/_arbeitsverzeichnis_nils/data/RMK_erfassung/Y-CIM/Y-CIM_complete/Y-CIM_complete.epml";
	//const MODEL_ANALYSIS_FILE = "C:/Users/t.thaler/Dropbox/_arbeitsverzeichnis_nils/data/RMK_erfassung/SAP R3/SAP R3 prozessorientiert anwenden/SAP_R3_prozesorientiert_anwenden_complete/SAP_R3_prozesorientiert_anwenden_complete .epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/eGov.epml";
	
	// All Models
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ECO-Integral_export.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/E-Payment_20.06.12.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/Handels-H-Model_noSEQ__export.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/SAP_R3_all.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/SAP_R3_proze\DForientiert_anwenden_export.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/Winfo_II_SS11.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/WInfo_II_WS1011.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/Winfo_II_WS1112.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/Y-CIM_3.0.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/Y-CIM_eng_export.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/Y-CIM_export.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/Admission_orig.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/birhtCertificate_orig.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/Handels-H-Model_2004_adapted_withSEQ.epml";
	
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Handels-H-Model_1996_original_noSEQ.empl";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/handels-h-model_2004_original_noSEQ.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Y-CIM_de_original.epml";
	const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Y-CIM_3.0.epml";
	
	// IWi Process Catalogue Catalogue
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/ECO-Integral_adapted.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/ECO-Integral_original.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/E-Payment.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Gewerbemeldungen.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Handels-H-Model_1996_original.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Handels-H-Model_2004_adapted_noSEQ.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Handels-H-Model_2004_adapted_withSEQ.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Handels-H-Model_2004_original.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Klausuren Winfo II SS11.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Klausuren Winfo II WS1011.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Klausuren Winfo II WS1112.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/SAP R3 prozessorientiert anwenden_adapted.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/SAP_All_EPCs.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Vogelaar.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Y-CIM_3.0.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Y-CIM_de_adapted.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Y-CIM_de_original.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Y-CIM_en_adapted.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ipmc/Y-CIM_en_original.epml";
	//const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ITIL.epml";
	
	// in Sekunden, 0=keine Beschraenkung
	const MAX_TIME_PER_TRACE_EXTRAKTION = 15;
	
	// Trace Extractor
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/Gewerbeanmeldung.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/eGov_VK.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/becker_survey.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example01_simpleAND.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example02_simpleAND_with_events.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example03_nestedAND.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example04_nestedAND_XOR.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example05_loop.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example06_loop_with_and.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example07_and_with_multiple_exits.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example08_deadlock.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example09_multiple_starts_with_AND.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example10_or_split.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example11_simple_or.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example12_or_with_multiple_exits.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example13_complex_or.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example14_or_wait_for_decision.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example15_becker_c_v2.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example16_becker_c_v2_easy.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example17_multiple_starts_with_OR.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example18_multiple_starts_with_XOR.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/example19_multiple_start_nested_xor.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/state_explosion.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/state_explosion2.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/state_explosion3.epml";
	const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/all_examples.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/beispielmodelle.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell_Part1.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell_Part2.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell_Part3.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell_Part4.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/sap_1Ar_m7re.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/sap_1An_kazo.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/sap_1In_aklk.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/sap_1Ar_m8hl.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/sap_1An_kmmdl.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/sap_1An_kmy0.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/sap_1An_l8wo.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/eGov.epml";
	
	// All Models
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/ECO-Integral_export.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/E-Payment_20.06.12.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/Handels-H-Model_noSEQ__export.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/SAP_R3_all.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/SAP_R3_proze\DForientiert_anwenden_export.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/Winfo_II_SS11.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/WInfo_II_WS1011.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/Winfo_II_WS1112.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/Y-CIM_3.0.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/Y-CIM_eng_export.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/Y-CIM_export.epml";
	
	// Process Matching Contest
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission Cologne.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission Frankfurt.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission FU Berlin.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission Hohenheim.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission IIS Erlangen.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission Muenster.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission Potsdam.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission TU Munich.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission Wuerzburg.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate_p31.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate_p32.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate_p33.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate_p34.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate_p246.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate_p247.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate_p248.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate_p249.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate_p250.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/Admission.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/epml/pmc/birhtCertificate.epml";
	//const TRACE_EXTRACTOR_FILE = "C:/xampp/htdocs/refmodmine/input/_state_explosion_all.epml";
	
	// Einstellen einer Fixpunktarithmetik: false => Ausgeschaltet, [0-x] => Fixpunkt
	const FIX_POINT_ARITHMETIC = false;
	
	// Input-Datei f\FCr die Berechnung der Empirischen Korreklation zwischen den \C4hnlichkeitsma\DFen
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/corr_kontrollierte_mod.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_varianten.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_varianten_var1.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_varianten_var2.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_varianten_var3.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_varianten_var4.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_varianten_var5.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_becker_replizierung.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_test.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/Analyse_SM.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/Analyse_SM_Variante_1.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/Analyse_SM_Variante_2.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/Analyse_SM_Variante_3.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/Analyse_SM_Variante_4.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/Analyse_SM_Variante_5.csv";
	const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/empiric_values.csv";
	
	// Input-Datei f\FCr die Berechnung der Stichprobenvarianz
	const VARIANCE_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/Analyse_SM.csv";
}
?>
