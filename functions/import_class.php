<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2006 by Martin Willisegger / nagiosql_v2@wizonet.ch
//
// Projekt:	NagiosQL Applikation
// Author :	Martin Willisegger
// Datum:	12.03.2007
// Zweck:	Datenimportklassen
// Datei:	functions/import_class.php
// Version: 2.00.00 (Internal)
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Klasse: Datenimportklasse
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Enthält sämtliche Funktionen, zum Importieren bestehender Konfigurationsdateien nötig sind
//
// Version 2.00.00 (Internal)
// Datum   12.03.2007 wim
//
// Name: nagimport
//
// Klassenvariabeln:
// -----------------
// $arrSettings:	Mehrdimensionales Array mit den globalen Konfigurationseinstellungen
// $arrLanguage:	Mehrdimensionales Array mit den globalen Sprachstrings
// $myDBClass:		Datenbank Klassenobjekt
// $myDataClass:	Standard Klassenobjekt
// $strDBMessage	Mitteilungen des Datenbankservers
// $strMessage		Mitteilungen der Klassenfunktion
//
// Externe Funktionen
// ------------------
// 
// 	
///////////////////////////////////////////////////////////////////////////////////////////////
class nagimport {
    // Klassenvariabeln deklarieren
    var $arrSettings;				// Wird im Klassenkonstruktor gefüllt
	var $arrLanguage;				// Wird in der Datei prepend_adm.php gefüllt
	var $myDBClass;					// Wird in der Datei prepend_adm.php definiert
	var $myDataClass;				// Wird in der Datei prepend_adm.php definiert
	var $strDBMessage    = "";		// Wird Klassenintern gefüllt
	var $strMessage		 = "";		// Wird Klassenintern gefüllt
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Klassenkonstruktor
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Tätigkeiten bei Klasseninitialisierung
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function nagimport() {
		// Globale Einstellungen einlesen
		$this->arrSettings = $_SESSION['SETS'];
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Datenimport
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Importiert eine Konfigurationsdatei und schreibt deren Daten in die entsprechende
	//	Datentabelle
	//
	//  Übergabeparameter:	$strFileName		Importdateiname
	//	------------------	$strTemplateName	Name der Templatedatei
	//						$intOverwrite		0 = Daten nicht überschreiben 
	//											1 = Daten überschreiben
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//  					Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function fileImport($strFileName,$strTemplateName,$intOverwrite) {
		// Variabeln deklarieren
		$intBlock	 		= 0;
		$intCheck	 		= 0;
		$strFileName 		= trim($strFileName);
		$strTemplateName 	= trim($strTemplateName);
		// Sind die Dateien lesbar?
		if (!is_readable($strFileName)) {
			$this->strDBMessage .= $this->arrLanguage['file']['notreadable']." ".$strFileName."<br>";
			return(1);
		}
		if (($strTemplateName != "") && !is_readable($strTemplateName)) {
			$this->strDBMessage .= $this->arrLanguage['file']['notreadable']." ".$strTemplateName."<br>";
			return(1);
		}
		// Konfigurationsdatei öffnen und zeilenweise einlesen
		$resFile = fopen($strFileName,"r");
		while(!feof($resFile)) {
			$strConfLine = fgets($resFile,4096);
			$strConfLine = trim($strConfLine);
			// Kommentarzeilen und Leerzeilen übergehen
			if (substr($strConfLine,0,1) == "#") continue;
			if ($strConfLine == "") continue;
			if (($intBlock == 1) && ($strConfLine == "{")) continue;
			// Linie verarbeiten (Leerzeichen reduzieren und Kommentare abschneiden)
			$arrLine    = preg_split("/[\s]+/", $strConfLine);
			$arrTemp    = explode(";",implode(" ",$arrLine));
			$strNewLine = trim($arrTemp[0]);
			// Blockbeginn suchen
			if ($arrLine[0] == "define") {
				$intBlock = 1;
				$strBlockKey = str_replace("{","",$arrLine[1]);
				if (($strBlockKey == "command") && (substr_count($strFileName,"misccommand") != 0))  $strBlockKey = "misccommand";
				if (($strBlockKey == "command") && (substr_count($strFileName,"checkcommand") != 0)) $strBlockKey = "checkcommand";
				$arrData = "";
				continue;
			}
			// Blockdaten in ein Array speichern
			if (($intBlock == 1) && ($arrLine[0] != "}")) {
				if ($arrLine[0] != "use") {
					$arrData[$arrLine[0]] = array("key" => $arrLine[0], "value" => str_replace($arrLine[0]." ","",$strNewLine));
				} else {
					// Templatedaten einfügen
					if ($strTemplateName == "") $strTemplateName = $strFileName;
					$intTplReturn = $this->insertTemplate($strTemplateName,str_replace($arrLine[0]." ","",$strNewLine),$arrData);
					if ($intTplReturn == 0) {
						$this->strMessage .= $this->arrLanguage['file']['templateok'].str_replace($arrLine[0]." ","",$strNewLine)."<br>";
					} else {
						$this->strMessage .= $this->arrLanguage['file']['templatenak'].str_replace($arrLine[0]." ","",$strNewLine)."<br>";					
					} 
				}
			}
			// Bei Blockende Daten verarbeiten	
			if (substr_count($strConfLine,"}") == 1)  {
				$intBlock = 0;
				// Template oder Konfiguration
				if (!array_key_exists ("name",$arrData)) {
					// Daten in DB schreiben
					$intReturn = $this->importTable($strBlockKey,$arrData,$intOverwrite,$strFileName);
					//if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
				}
			}			
		}
		return($intCheck);			
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Hilfsfunktion: Tabelle importieren
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Importiert eine Konfigurationsdatei in die passende Datentabelle.
	//
	//  Übergabeparameter:	$strBlockKey			Konfigurationsschlüssel (define)
	//  				    $arrImportData			Eingelesene Daten eines Blockes
	//						$intOverwrite			Daten in Tabelle überschreiben 1=Ja, 0=Nein
	//						$strFileName			Name der Konfigurationsdatei					
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg / 2 Eintrag existiert schon
	//						Erfolg-/Fehlermeldung via Klassenvariable strDBMessage					
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function importTable($strBlockKey,$arrImportData,$intOverwrite,$strFileName) {
		// Variabeln deklarieren
		$intExists 			= 0;
		$intInsertRelations = 0;
		// Tabellenname festlegen
		switch($strBlockKey) {
			case "misccommand":			$strTable = "tbl_misccommand"; 	 	 $strKeyField = "command_name"; 		break;
			case "checkcommand":		$strTable = "tbl_checkcommand";	 	 $strKeyField = "command_name"; 		break;
			case "contactgroup":		$strTable = "tbl_contactgroup"; 	 $strKeyField = "contactgroup_name"; 	break;
			case "contact":				$strTable = "tbl_contact"; 			 $strKeyField = "contact_name"; 		break;
			case "timeperiod":			$strTable = "tbl_timeperiod"; 		 $strKeyField = "timeperiod_name"; 		break;
			case "host":				$strTable = "tbl_host"; 			 $strKeyField = "host_name"; 			break;
			case "service":				$strTable = "tbl_service"; 			 $strKeyField = ""; 					break;
			case "hostgroup":			$strTable = "tbl_hostgroup"; 		 $strKeyField = "hostgroup_name"; 		break;
			case "servicegroup":		$strTable = "tbl_servicegroup"; 	 $strKeyField = "servicegroup_name"; 	break;
			case "hostescalation":		$strTable = "tbl_hostescalation"; 	 $strKeyField = ""; 					break;
			case "serviceescalation":	$strTable = "tbl_serviceescalation"; $strKeyField = ""; 					break;
			case "hostdependency":		$strTable = "tbl_hostdependency"; 	 $strKeyField = "";						break;
			case "servicedependency":	$strTable = "tbl_servicedependency"; $strKeyField = ""; 					break;
			case "hostdependency":		$strTable = "tbl_hostdependency"; 	 $strKeyField = ""; 					break;
			case "servicedependency":	$strTable = "tbl_servicedependency"; $strKeyField = ""; 					break;			
			case "hostextinfo":			$strTable = "tbl_hostextinfo"; 	     $strKeyField = "host_name"; 			break;
			case "serviceextinfo":		$strTable = "tbl_serviceextinfo"; 	 $strKeyField = ""; 					break;
			default:	
				$this->strDBMessage = $this->arrLanguage['file']['tablefail1'].$strBlockKey.$this->arrLanguage['file']['tablefail2'];
				return(1);			
		}
		// Relationen dieser Tabelle einlesen
		$intRelation = $this->myDataClass->tableRelations($strTable,$arrRelations);

		// Existiert der Eintrag schon?
		if (($strKeyField != "") && isset($arrImportData[$strKeyField])) {
			$intExists = $this->myDBClass->getFieldData("SELECT id FROM $strTable WHERE $strKeyField='".$arrImportData[$strKeyField]['value']."'");	
			if ($intExists == false) $intExists = 0;
		}
		
		// Existiert der Eintrag, darf aber nicht überschrieben werden?
		if (($intExists != 0) && ($intOverwrite == 0)) {
			$this->strMessage .= $this->arrLanguage['db']['entry']." ".$strKeyField."::".$arrImportData[$strKeyField]['value'].$this->arrLanguage['db']['inside']." ".$strTable.$this->arrLanguage['db']['exists']."<br>";
			return(2);
		}
		
		// Templatedefinitionen nicht übernehmen
		if (isset($arrImportData['name'])) return(0);
		
		// Eintrag aktiv ?
		if (isset($arrImportData['register']) && ($arrImportData['register']['value'] == 0)) {
			$intActive = 0;
		} else {
			$intActive = 1;
		}

		// SQL Definieren - Teil 1
		if ($intExists != 0) {
			// DB Eintrag updaten
			$strSQL1 = "UPDATE $strTable SET ";
			$strSQL2 = " active='$intActive', last_modified=NOW() WHERE id=$intExists";
		} else {
			// DB Eintrag einfügen
			$strSQL1 = "INSERT INTO $strTable SET ";
			$strSQL2 = " active='$intActive', last_modified=NOW()";
		}
		
		// SQL definieren - Teil 2 (Array abarbeiten)
		$i = 0;
		foreach ($arrImportData AS $elem) {
			if (($elem['key'] != "register") && ($elem['key'] != "use")) {
				// Unterliegt dieses Feld einer Relation
				if ($intRelation != 0) {
					foreach ($arrRelations AS $relations) {
						if ($relations['fieldName'] == $elem['key']) {
							// Handelt es sich um eine 1:1 Relation?
							if ($relations['type'] == 1) { //---> INSERT + UPDATE gleich
								// Sonderregel für command_line
								if ($relations['target'] == "command_name") {
									$arrCommand = explode("!",$elem['value']);
									if (count($arrCommand) == 1) {
										$strArguments  = "";
									} else {
										$strArguments = str_replace($arrCommand[0],"",$elem['value']);
									}
									$elem['value'] = $arrCommand[0];
									//echo "Sonderregel Commandlinie<br>";
								}
								
								// Existiert der Zieleintrag?
								$intReturn = $this->myDBClass->getFieldData("SELECT id FROM ".$relations['tableName']." WHERE ".$relations['target']."='".$elem['value']."'");
								if (($intReturn != false) && ($intReturn != 0)) {
									//echo "existiert<br>";
									$elem['value'] = $intReturn;
									if ($relations['target'] == "command_name") {
										$elem['value'] = $intReturn.$strArguments;
									}
								} else {
								    //echo "fehlt<br>";
									$intInsert = $this->myDBClass->insertData("INSERT INTO ".$relations['tableName']." 
																			   SET ".$relations['target']."='".$elem['value']."', 
																			   active='0', last_modified=NOW()");
									$elem['value'] = $this->myDBClass->intLastId;
									if ($relations['target'] == "command_name") {
										$elem['value'] = $this->myDBClass->intLastId.$strArguments;
									}
									//echo "VERKNÜPFUNG FEHLT 1:1"."SELECT id FROM ".$relations['tableName']." WHERE ".$relations['target']."='".$elem['value']."'"."<BR>";
								}
							} else if ($relations['type'] == 2) { //---> INSERT + UPDATE verschieden
								// Im Updatefall erst alle Relationen löschen
								if ($intExists != 0) {
									$arrInsertSQL[] = "DELETE FROM tbl_relation WHERE 
												   	   tbl_A=".$this->myDataClass->tableID($strTable)." AND
												   	   tbl_A_id={INSERT_ID} AND
													   tbl_A_field='".$relations['fieldName']."'";
								}
								// Wert trennen
								$arrValue = explode(",",$elem['value']);
								// Ziel IDs bestimmen
								$intSkip = 0;
								foreach ($arrValue AS $rel2) {
									if ($intSkip == 1) continue;
									// Spezialeintrag "*" behandeln
									if ($rel2 == "*") {
										$elem['value'] = 2;
										$intSkip = 1;
										continue;
									}
									// Existiert der Zieleintrag? 
									$intReturn = $this->myDBClass->getFieldData("SELECT id FROM ".$relations['tableName']." WHERE ".$relations['target']."='".$rel2."'");
									//echo "SELECT id FROM ".$relations['tableName']." WHERE ".$relations['target']."='".$rel2."'<br>";
									if (($intReturn != false) && ($intReturn != 0)) {
										$arrInsertSQL[] =  "INSERT INTO tbl_relation SET 
															tbl_A=".$this->myDataClass->tableID($strTable).",
															tbl_A_id={INSERT_ID},
															tbl_A_field='".$relations['fieldName']."',
															tbl_B=".$this->myDataClass->tableID($relations['tableName']).",
															tbl_B_id=$intReturn";		
										$intInsertRelations = 1;
									} else {
										$arrInsertSQL[] =  "INSERT INTO ".$relations['tableName']." SET ".$relations['target']."='".$rel2."', 
															active='0', last_modified=NOW()";
										$arrInsertSQL[] =  "INSERT INTO tbl_relation SET 
															tbl_A=".$this->myDataClass->tableID($strTable).",
															tbl_A_id={INSERT_ID},
															tbl_A_field='".$relations['fieldName']."',
															tbl_B=".$this->myDataClass->tableID($relations['tableName']).",
															tbl_B_id={INSERT_ID_TARGET}";
										$intInsertRelations = 1;
									}
									$elem['value'] = 1;
								}
							}
						}
					}
				}
				$strSQL1 .= $elem['key']."='".addslashes($elem['value'])."', ";
				// Schlüsselfeld speichern
				if ($i == 0) $strTemp1 = addslashes($elem['value']);
				// Datenfeld speichern
				if ($i == 1) $strTemp2 = addslashes($elem['value']);
				$i++;
			}
		}
		// Konfigurationsname ermitteln
		if ($strTable == "tbl_service") {
			$arrTemp  = explode(".",strrev(basename($strFileName)),2);
			$strSQL1 .= "config_name='".strrev($arrTemp[1])."', ";
			$strTemp1 = strrev($arrTemp[1]);
		}		
		if (($strTable == "tbl_serviceescalation") || ($strTable == "tbl_servicedependency") || 
			($strTable == "tbl_hostescalation") || ($strTable == "tbl_hostdependency")) {
			$strName = str_replace(" ","_","import_".microtime());
			$strSQL1 .= "config_name='".$strName."', ";
			if (isset($arrTemp)) $strTemp1 = strrev($arrTemp[1]);
		}
		
		// Datenbank updaten
		//echo $strSQL1.$strSQL2."<br>"; $booResult=true;
		$booResult = $this->myDBClass->insertData($strSQL1.$strSQL2);
		if ($booResult != true) {
			$this->strDBMessage = $this->myDBClass->strDBError;
			if ($strKeyField != "") $this->strMessage .= $this->arrLanguage['db']['entry']." ".$strKeyField."::".$arrImportData[$strKeyField]['value']." ".$this->arrLanguage['db']['inside']." ".$strTable." ".$this->arrLanguage['db']['insertnak'].mysql_error()."<br>";
			if ($strKeyField == "") $this->strMessage .= $this->arrLanguage['db']['entry']." ".$strTemp1."::".$strTemp2.$this->arrLanguage['db']['inside']." ".$strTable." ".$strTable." ".$this->arrLanguage['db']['insertnak'].mysql_error()."<br>";
			return(1);
		} else {
			if ($strKeyField != "") $this->strMessage .= "<span class=\"greenmessage\">".$this->arrLanguage['db']['entry']." ".$strKeyField."::".$arrImportData[$strKeyField]['value']." in ".$strTable." ".$this->arrLanguage['db']['insertok']."</span><br>";
			if ($strKeyField == "") $this->strMessage .= "<span class=\"greenmessage\">".$this->arrLanguage['db']['entry']." ".$strTemp1."::".$strTemp2." ".$this->arrLanguage['db']['inside']." ".$strTable." ".$this->arrLanguage['db']['insertok']."</span><br>";
			// Müssen noch Relationen eingetragen werden?
			if ($intInsertRelations == 1) {
				if ($intExists != 0) {
					$intDatasetId = $intExists;
				} else {
					$intDatasetId = $this->myDBClass->intLastId;
				}
				foreach($arrInsertSQL AS $elem) {
					if (substr_count($elem,"DELETE") == 1) { 
						$elem = str_replace("{INSERT_ID}",$intExists,$elem);
						//echo "Hier - $elem<br>";
						$booResult 	= $this->myDBClass->insertData($elem);
						continue;
					}
					if (substr_count($elem,"active") == 1) {
						$booResult 	= $this->myDBClass->insertData($elem);
						$intSaveID  = $this->myDBClass->intLastId;
						//echo "Target: ".$elem."<br>";
					} else {
						if (isset($intSaveID) && ($intSaveID != 0))  {
							$elem = str_replace("{INSERT_ID}",$intDatasetId,$elem);
							$elem = str_replace("{INSERT_ID_TARGET}",$intSaveID,$elem);
							$intSaveID = 0;
						} else {
							$elem = str_replace("{INSERT_ID}",$intDatasetId,$elem);
						}	
						$booResult 	= $this->myDBClass->insertData($elem);
						//echo "Relation: ".$elem."<br>";			
					}
				}
			}
			return(0);
		}
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Template integrieren
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Integriert die Daten eines bestimmten Templates in das Importdatenarrays
	//
	//  Übergabeparameter:	$strFileName	Importdateiname
	//	                    $strTemplate	Name des Templates	
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//  Rückgabewert:		Datenarray mit hinzugefügten Templatevariabeln
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function insertTemplate($strFileName,$strTemplate,&$arrData) {
		// Variabeln deklarieren
		$intBlock	   = 0;
		$intCheck	   = 0;
		$intIsTemplate = 0;
		// Konfigurationsdatei öffnen und zeilenweise einlesen
		$resTplFile = fopen($strFileName,"r");
		while(!feof($resTplFile)) {
			$strConfLine = fgets($resTplFile,1024);
			$strConfLine = trim($strConfLine);
			// Kommentarzeilen und Leerzeilen übergehen
			if (substr($strConfLine,0,1) == "#") continue;
			if ($strConfLine == "") continue;
			if (($intBlock == 1) && ($strConfLine == "{")) continue;
			// Linie verarbeiten (Leerzeichen reduzieren und Kommentare abschneiden)
			$arrLine    = preg_split("/[\s]+/", $strConfLine);
			$arrTemp    = explode(";",implode(" ",$arrLine));
			$strNewLine = trim($arrTemp[0]);
			// Blockbeginn suchen
			if ($arrLine[0] == "define") {
				$intBlock = 1;
				$strBlockKey = str_replace("{","",$arrLine[1]);
				if (($strBlockKey == "command") && (substr_count($strFileName,"misccommand") != 0))  $strBlockKey = "misccommand";
				if (($strBlockKey == "command") && (substr_count($strFileName,"checkcommand") != 0)) $strBlockKey = "checkcommand";
				$arrDataTpl = "";
				continue;
			}
			// Blockdaten in ein Array speichern
			if (($intBlock == 1) && ($arrLine[0] != "}")) {
				if (($arrLine[0] == "name") && (str_replace($arrLine[0]." ","",$strNewLine) == $strTemplate)) $intIsTemplate = 1;
				if (($arrLine[0] != "name") && ($arrLine[0] != "register")) {
					$arrDataTpl[$arrLine[0]] = str_replace($arrLine[0]." ","",$strNewLine);
				}
			}
			// Bei Blockende Daten verarbeiten	
			if (substr_count($strConfLine,"}") == 1)  {
				$intBlock = 0;
				// Template in Datenarray einfügen
				if ($intIsTemplate) {
					foreach($arrDataTpl AS $key => $value) {
						$arrData[$key] = array("key" => $key, "value" => $value);
					}
					return(0);
				}
			}			
		}
		return(1);			
	}
}
?>