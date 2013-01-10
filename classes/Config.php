<?php
/**
 * Konfigurationsklasse
 * 
 * @author Tom Thaler
 *
 */
final class Config {
	
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
	//const MODEL_FILE_1 = "P:/Projekte/RefModMiner/HudsonBuilds/data/becker_survey.epml";
	//const MODEL_FILE_2 = "P:/Projekte/RefModMiner/HudsonBuilds/data/becker_survey.epml";
	
	// SAP Referenzmodell
	const MODEL_FILE_1 = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell.epml";
	const MODEL_FILE_2 = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell.epml";
	
	// Model Analyzer
	//const MODEL_ANALYSIS_FILE = "C:/Users/t.thaler/Documents/IWi/Eigene Paper/in Arbeit/Modellähnlichkeit/Modelle/SAP.epml";
	//const MODEL_ANALYSIS_FILE = "P:/Projekte/RefModMiner/HudsonBuilds/data/refmodmine.epml";
	//const MODEL_ANALYSIS_FILE = "P:/Projekte/RefModMiner/Data/Kontrollierte Modellierung/Kontrollierte Modellierung.epml";
	//const MODEL_ANALYSIS_FILE = "P:/Projekte/RefModMiner/Data/Kontrollierte Modellierung/Klausuren/EPML/WInfo II WS1011.epml";
	//const MODEL_ANALYSIS_FILE = "P:/Projekte/RefModMiner/Data/Kontrollierte Modellierung/Klausuren/EPML/Winfo II SS11.epml";
	//const MODEL_ANALYSIS_FILE = "P:/Projekte/RefModMiner/Data/Kontrollierte Modellierung/Klausuren/EPML/Winfo II WS1112.epml";
	//const MODEL_ANALYSIS_FILE = "C:/Users/t.thaler/Dropbox/_arbeitsverzeichnis_nils/data/RMK_erfassung/Handels-H/Handels-H-Model_base.epml";
	//const MODEL_ANALYSIS_FILE = "C:/Users/t.thaler/Dropbox/_arbeitsverzeichnis_nils/data/RMK_erfassung/Y-CIM/Y-CIM_complete/Y-CIM_complete.epml";
	//const MODEL_ANALYSIS_FILE = "C:/Users/t.thaler/Dropbox/_arbeitsverzeichnis_nils/data/RMK_erfassung/SAP R3/SAP R3 prozessorientiert anwenden/SAP_R3_prozesorientiert_anwenden_complete/SAP_R3_prozesorientiert_anwenden_complete .epml";
	const MODEL_ANALYSIS_FILE = "C:/xampp/htdocs/refmodmine/input/SAP_Referenzmodell.epml";
	
	// Einstellen einer Fixpunktarithmetik: false => Ausgeschaltet, [0-x] => Fixpunkt
	const FIX_POINT_ARITHMETIC = false;
	
	// Input-Datei für die Berechnung der Empirischen Korreklation zwischen den Ähnlichkeitsmaßen
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/corr_kontrollierte_mod.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_varianten.csv";
	const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_varianten_var1.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_varianten_var2.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_varianten_var3.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_varianten_var4.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_varianten_var5.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_becker_replizierung.csv";
	//const CORR_INPUT_FILE = "C:/xampp/htdocs/refmodmine/input/korrelation_input_test.csv";
}
?>