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
// Zweck:	Konfigurationsklassen
// Datei:	functions/config_class.php
// Version: 2.00.00 (Internal)
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Klasse: Konfigurationsklasse
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Enthält sämtliche Funktionen, zum Erstellen der Nagioskonfiguration nötig sind
//
// Version 2.00.00 (Internal)
// Datum   12.03.2007 wim
//
// Name: nagconfig
//
// Klassenvariabeln:
// -----------------
// $arrSettings:	Mehrdimensionales Array mit den globalen Konfigurationseinstellungen
// $arrLanguage:	Mehrdimensionales Array mit den globalen Sprachstrings
// $myDBClass:		Datenbank Klassenobjekt
// $myVisClass		NagiosQL Visionalisierungsklasse
// $myDataClass		NagiosQL Datenmaipulationsklasse
// $strDBMessage	Mitteilungen des Datenbankservers
//
// Externe Funktionen
// ------------------
// 
// 	
///////////////////////////////////////////////////////////////////////////////////////////////
class nagconfig {
    // Klassenvariabeln deklarieren
    var $arrSettings;				// Wird im Klassenkonstruktor gefüllt
	var $arrLanguage;				// Wird in der Datei prepend_adm.php gefüllt
	var $myDBClass;					// Wird in der Datei prepend_adm.php definiert
	var $myVisClass;				// Wird in der Datei prepend_adm.php definiert
	var $myDataClass;				// Wird in der Datei prepend_adm.php definiert
	var $strDBMessage	= "";		// Wird Klassenintern gefüllt
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Klassenkonstruktor
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Ttigkeiten bei Klasseninitialisierung
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function nagconfig() {
		// Globale Einstellungen einlesen
		$this->arrSettings = $_SESSION['SETS'];
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Letzte Datentabellenänderung und letzte Konfigurationsdateiänderung
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Ermittelt die Zeitpunkte der letzten Datentabellenänderung sowie der letzten Änderung an
	//  der Konfigurationsdatei
	//
	//  Übergabeparameter:	$strTableName		Datentabellenname
	//	------------------
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//
	//	Rückgabewerte:		$strTimeTable		Datum der letzten Datentabellenänderung
	//						$strTimeFile		Datum der letzten Konfigurationsdateiänderung
	//						$strCheckConfig		Informationsstring, falls Datei älter als Tabelle		
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function lastModified($strTableName,&$strTimeTable,&$strTimeFile,&$strCheckConfig) {
		// Konfigurationsdateinamen entsprechend dem Tabellennamen festlegen
		switch($strTableName) {
			case "tbl_timeperiod":			$strFile = "timeperiods.cfg"; break;
			case "tbl_misccommand":			$strFile = "misccommands.cfg"; break;
			case "tbl_checkcommand":		$strFile = "checkcommands.cfg"; break;
			case "tbl_contact":				$strFile = "contacts.cfg"; break;
			case "tbl_contactgroup":		$strFile = "contactgroups.cfg"; break;
			case "tbl_hostgroup":			$strFile = "hostgroups.cfg"; break;
			case "tbl_servicegroup":		$strFile = "servicegroups.cfg"; break;
			case "tbl_servicedependency":	$strFile = "servicedependencies.cfg"; break;
			case "tbl_hostdependency":		$strFile = "hostdependencies.cfg"; break;
			case "tbl_serviceescalation":	$strFile = "serviceescalations.cfg"; break;
			case "tbl_hostescalation":		$strFile = "hostescalations.cfg"; break;
			case "tbl_hostextinfo":			$strFile = "hostextinfo.cfg"; break;
			case "tbl_serviceextinfo":		$strFile = "serviceextinfo.cfg"; break;
		}
		// Variabeln definieren
		$strCheckConfig = "";
		$strTimeTable   = "unknown";
		$strTimeFile	= "unknown";
		// Statuscache lschen
		clearstatcache();
		// Letzte Änderung an der Datentabelle auslesen
		$booReturn = $this->myDBClass->getSingleDataset("SHOW TABLE STATUS LIKE '$strTableName'",$arrDataset);
		if ($booReturn == true) {
			$strTimeTable = $arrDataset['Update_time'];
			// Letzte Änderung an der Konfigurationsdatei auslesen
			if (file_exists($this->arrSettings['nagios']['config'].$strFile)) {
				$intFileStamp = filemtime($this->arrSettings['nagios']['config'].$strFile);
				$strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
				// Falls Datei älter, den entsprechenden String zurückgeben
				if (strtotime($strTimeTable) > $intFileStamp) $strCheckConfig = $this->arrLanguage['common']['older'];
				return(0);
			}
		}
		return(1);	
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Letzte Datensatzänderung und letzte Konfigurationsdateiänderung
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Ermittelt die Zeitpunkte der letzten Datensatzänderung sowie der letzten Änderung an
	//  der Konfigurationsdatei
	//
	//  Übergabeparameter:	$strConfigname	Name der Konfiguration
	//	------------------	$strId			Datensatz ID			
	//						$strType		Datentyp ("host" oder "service")
	//		
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//	Rückgabewerte:		$strTime		Datum der letzten Datensatzänderung
	//						$strTimeFile	Datum der letzten Konfigurationsdateiänderung
	//						$intOlder 		0, falls Datei älter - 1, falls aktuell			
	//								
	///////////////////////////////////////////////////////////////////////////////////////////
	function lastModifiedDir($strConfigname,$strId,$strType,&$strTime,&$strTimeFile,&$intOlder) {
		// Filename zusammenstellen
		$strFilename = $strConfigname.".cfg";
		// Variabeln definieren
		$intCheck    	= 0;
		// Statuscache löschen
		clearstatcache();
		// Letzte Änderung an der Datentabelle auslesen
		if ($strType == "host") {
			$strTime = $this->myDBClass->getFieldData("SELECT DATE_FORMAT(last_modified,'%Y-%m-%d %H:%i:%s') 
													   FROM tbl_host WHERE id=".$strId);
			$strPath = $this->arrSettings['nagios']['confighosts'];
			if ($strTime != false) $intCheck++;
		} else if ($strType == "service") {
			$strTime = $this->myDBClass->getFieldData("SELECT DATE_FORMAT(last_modified,'%Y-%m-%d %H:%i:%s') 
													   FROM tbl_service WHERE id=".$strId);
			$strPath = $this->arrSettings['nagios']['configservices'];
			if ($strTime != false) $intCheck++;
		} else {
			$strTime      = "undefined";
			$intOlder     = 1;
		}	
		// Letzte Änderung an der Konfigurationsdatei auslesen
		if (file_exists($strPath.$strFilename)) {
			$intFileStamp = filemtime($strPath.$strFilename);
			$strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
			$intCheck++;
		} else {
			$strTimeFile = "undefined";
			$intOlder    = 1;
		}
		// Falls beide Werte gültig, vergleichen
		if ($intCheck == 2) {
			if (strtotime($strTime) > $intFileStamp) {$intOlder = 1;} else {$intOlder = 0;}
			return(0);
		}
		return(1);
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Konfigurationsdatei schreiben
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Schreibt ein einzelnes Konfigurationsfile mit allen Datensätzen einer Tabelle oder
	//  liefert die Ausgabe als Textdatei zum Download aus.
	//
	//  Übergabeparameter:	$strTableName	Tabellenname
	//	------------------	$intMode		0 = Datei schreiben, 1 = Ausgabe für Download
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//
	//  Rckgabewert:		Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function createConfig($strTableName,$intMode=0) {
		// Variabeln entsprechend dem Tabellennamen definieren
		switch($strTableName) {
			case "tbl_timeperiod":			$strFileString 	= "timeperiods";
											$strOrderField 	= "timeperiod_name";
											$strAddFill 	= "";
											break;
			case "tbl_misccommand":			$strFileString 	= "misccommands";
											$strOrderField 	= "command_name";
											$strAddFill 	= "";
											break;
			case "tbl_checkcommand":		$strFileString 	= "checkcommands";
											$strOrderField 	= "command_name";
											$strAddFill 	= "";
											break;
			case "tbl_contact":				$strFileString 	= "contacts";
											$strOrderField 	= "contact_name";
											$strAddFill 	= "\t\t";
											break;
			case "tbl_contactgroup":		$strFileString 	= "contactgroups";
											$strOrderField 	= "contactgroup_name";
											$strAddFill 	= "\t\t";
											break;
			case "tbl_hostgroup":			$strFileString 	= "hostgroups";
											$strOrderField 	= "hostgroup_name";
											$strAddFill 	= "\t";
											break;
			case "tbl_servicegroup":		$strFileString 	= "servicegroups";
											$strOrderField 	= "servicegroup_name";
											$strAddFill 	= "\t\t";
											break;
			case "tbl_servicedependency":	$strFileString 	= "servicedependencies";
											$strOrderField 	= "dependent_host_name";
											$strAddFill 	= "\t\t";
											break;
			case "tbl_hostdependency":		$strFileString 	= "hostdependencies";
											$strOrderField 	= "dependent_host_name";
											$strAddFill 	= "\t\t";
											break;
			case "tbl_serviceescalation":	$strFileString 	= "serviceescalations";
											$strOrderField 	= "host_name,service_description";
											$strAddFill 	= "\t\t";
											break;
			case "tbl_hostescalation":		$strFileString 	= "hostescalations";
											$strOrderField 	= "host_name,hostgroup_name";
											$strAddFill 	= "\t\t";
											break;
			case "tbl_hostextinfo":			$strFileString 	= "hostextinfo";
											$strOrderField 	= "host_name";
											$strAddFill 	= "\t\t";
											break;
			case "tbl_serviceextinfo":		$strFileString 	= "serviceextinfo";
											$strOrderField 	= "host_name";
											$strAddFill 	= "\t\t";
											break;
			default:						return(1);
		}
		// SQL Abfrage festlegen und Dateinamen definieren
		$strSQL 		= "SELECT * FROM $strTableName WHERE active='1' ORDER BY $strOrderField";
		$strFile 	 	= $strFileString.".cfg";
		$setTemplate 	= $strFileString.".tpl.dat";
		// Relationen holen
		$this->myDataClass->tableRelations($strTableName,$arrRelations);
		// Konfiguration schreiben?
		if ($intMode == 0) {
			// Alte Konfigurationsdatei sichern
			if (file_exists($this->arrSettings['nagios']['config'].$strFile) && (is_writable($this->arrSettings['nagios']['config']))) {
				$strOldDate = date("YmdHis",mktime());
				copy($this->arrSettings['nagios']['config'].$strFile,$this->arrSettings['nagios']['backup'].$strFile."_old_".$strOldDate);
			} else if (!(is_writable($this->arrSettings['nagios']['config']))) {
				$this->strDBMessage = $this->arrLanguage['file']['failed'];
				return(1);
			}
			// Konfigurationsdatei öffnen
			if (is_writable($this->arrSettings['nagios']['config'].$strFile) || (!file_exists($this->arrSettings['nagios']['config'].$strFile))) {	
				$CONFIGFILE = fopen($this->arrSettings['nagios']['config'].$strFile,"w");	
			} else {
			    $this->myDataClass->writeLog($this->arrLanguage['logbook']['configfail']." ".$strFile);
				$this->strDBMessage = $this->arrLanguage['file']['failed'];
				return(1);		
			}
			
		}	
		// Konfigurationsvorlage laden
		$arrTplOptions = array('use_preg' => false);
		$configtp = new HTML_Template_IT($this->arrSettings['path']['physical']."/templates/files/");
		$configtp->loadTemplatefile($setTemplate, true, true);
		$configtp->setOptions($arrTplOptions);
		$configtp->setVariable("CREATE_DATE",date("Y-m-d H:i:s",mktime()));
		// Datenbank abfragen und Resultat verarbeiten
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
		if ($booReturn == false) {
			$this->strDBMessage = $this->arrLanguage['db']['dberror']."<br>".$this->myDBClass->strDBError."<br>";		
		} else if ($intDataCount != 0) {
			// Jeden Datensatz verarbeiten
			for ($i=0;$i<$intDataCount;$i++) {
				foreach($arrData[$i] AS $key => $value) {
					$intSkip = 0;
					// Spezialdatenfelder überspringen
					if (($value == "") || ($key == "id") || ($key == "config_name") || ($key == "active") || 
						($key == "last_modified") || ($key == "access_rights ") || ($key == "config_id") ||
						($key == "template")) {
						continue;
					}
					// Ist das Datenfeld über eine Relation mit einem anderen Datenfeld verbunden?
					if (is_array($arrRelations)) {
						foreach($arrRelations AS $elem) {
							if ($elem['fieldName'] == $key) {
								// Handelt es sich um eine normale 1:n Relation?
								if (($value == 1) && ($elem['type'] == 2)) {
									$strSQLRel = "SELECT ".$elem['tableName'].".".$elem['target']." FROM tbl_relation
											      LEFT JOIN ".$elem['tableName']." ON tbl_relation.tbl_B_ID = ".$elem['tableName'].".id
											      WHERE tbl_A=".$this->myDataClass->tableId($strTableName)." 
												  	AND tbl_B=".$this->myDataClass->tableId($elem['tableName'])." 
													AND tbl_A_field='$key' AND tbl_A_id=".$arrData[$i]['id']." 
													AND ".$elem['tableName'].".".$elem['target']." IS NOT NULL
											      ORDER BY ".$elem['tableName'].".".$elem['target'];
									$booReturn = $this->myDBClass->getDataArray($strSQLRel,$arrDataRel,$intDataCountRel);
									// Wurden Datensätze gefunden?
									if ($intDataCountRel != 0) {
										// Datenfeldwerte der gefundenen Datensätze eintragen
										$value = "";
										foreach ($arrDataRel AS $data) {
											$value .= $data[$elem['target']].",";
										}
										$value = substr($value,0,-1);
									} else {
										$intSkip = 1;
									}
								// Handelt es sich um den Ausnahmewert "*"?
								} else if (($value == 2) && ($elem['type'] == 2)) {
									$value = "*";
								// Handelt es sich um eine normale 1:1 Relation?
								} else if ($elem['type'] == 1) {
									$strSQLRel = "SELECT ".$elem['target']." FROM ".$elem['tableName']."
											      WHERE id=".$arrData[$i][$elem['fieldName']];
									$booReturn = $this->myDBClass->getDataArray($strSQLRel,$arrDataRel,$intDataCountRel);
									// Wurden Datensätze gefunden?
									if ($booReturn && ($intDataCountRel != 0)) {
										// Datenfeldwert des gefundenen Datensatzes eintragen
										$value = $arrDataRel[0][$elem['target']];
									} else {
										$intSkip = 1;
									}
								} else {
									$intSkip = 1;
								}
							}
						}
					}
					// Spezialbehandlung für Konfiguration der Servicegruppen im Feld "members"
					if (($strTableName == "tbl_servicegroup") && ($key == "members")) {
						$strSQLRel = "SELECT tbl_host.host_name, service_description, tbl_B1_id, tbl_B2_id
									  FROM tbl_relation_special
									  LEFT JOIN tbl_host ON tbl_relation_special.tbl_B1_id = tbl_host.id
									  LEFT JOIN tbl_service ON tbl_relation_special.tbl_B2_id = tbl_service.id
									  WHERE tbl_A =14 AND tbl_B1 =4 AND tbl_B2 =10 AND tbl_A_field = 'members' 
									  	AND tbl_A_id=".$arrData[$i]['id']."
									  ORDER BY tbl_host.host_name, service_description";
						$booReturn = $this->myDBClass->getDataArray($strSQLRel,$arrDataRel,$intDataCountRel);
						// Wurden Datensätze gefunden?
						if ($booReturn && ($intDataCountRel != 0)) {
							// Datenfeldwerte der gefundenen Datensätze eintragen
							$value = "";
							foreach ($arrDataRel AS $data) {
								if ($data['tbl_B1_id'] == 0) $data['host_name'] = "*";
								if ($data['tbl_B2_id'] == 0) $data['service_description'] = "*";
								$value .= $data['host_name'].",".$data['service_description'].",";
							}
							$value = substr($value,0,-1);
							$intSkip = 0;
						} else {
							$intSkip = 1;
						}    
					}
					// Falls das Datenfeld nicht übersprungen werden soll
					if ($intSkip != 1) {
						// Bei längeren Keys zuszliche Tabulatoren einfgen
						if (strlen($key) < 8) {$strFill = "\t";} else {$strFill = "";}
						if ((strlen($key) < 16) && isset($strAddFill))   $strFill .= $strAddFill;
						if ((strlen($key) < 23) && (strlen($key) >= 17)) $strFill .= "\t";
						// Schlüssel und Wert in Template schreiben und nächste Zeile aufrufen
						$configtp->setVariable("ITEM_TITLE",$key.$strFill);
						$configtp->setVariable("ITEM_VALUE",stripslashes($value));
						$configtp->parse("configline");
					}
				}
				//Konfigurationssatz schreiben
				$configtp->parse("configset");
			}
		}		
		$configtp->parse();
		// Entsprechend dem Modus die Ausgabe in die Konfigurationsdatei schreiben oder direkt ausgeben
		if ($intMode == 0) {
		   
			fwrite($CONFIGFILE,$configtp->get());
			fclose($CONFIGFILE);
			$this->myDataClass->writeLog($this->arrLanguage['logbook']['config']." ".$strFile);
			$this->strDBMessage = $this->arrLanguage['file']['success'];	
			return(0);
		   
		} else if ($intMode == 1) {
			$configtp->show();
			return(0);
		}
		return(1);
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Konfigurationsdatei für einzelnen Datensatz schreiben
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Schreibt ein einzelnes Konfigurationsfile mit einem einzelnen Datensatz einer Tabelle oder
	//  liefert die Ausgabe als Textdatei zum Download aus.
	//
	//  Übergabeparameter:	$strTableName	Tabellenname
	//	------------------	$intDbId		Datensatz ID
	//						$intMode		0 = Datei schreiben, 1 = Ausgabe für Download
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//  Rckgabewert:		Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function createConfigSingle($strTableName,$intDbId = 0,$intMode = 0) {
		// Entsprechend der übergebenen ID die WHERE Bedingung setzen
		//if ($intDbId != 0) {$strWHERE = "WHERE id=$intDbId";} else {$strWHERE = "";}
		$strWHERE = "";
		// Alle Datensatz ID der Tabelle holen
		$booReturn = $this->myDBClass->getDataArray("SELECT id FROM $strTableName $strWHERE ORDER BY id",$arrData,$intDataCount);
		if (($booReturn != false) && ($intDataCount != 0)) {
			for ($i=0;$i<$intDataCount;$i++) {
				// Formularübergabeparameter zusammenstellen
				$strChbName = "chbId_".$arrData[$i]['id'];
				// Falls ein Parameter mit diesem Namen übergeben oder eine Datensatz ID angegeben wurde
				if (isset($_POST[$strChbName]) || (($intDbId != 0) && ($intDbId == $arrData[$i]['id']))) {	
					$this->myDBClass->strDBError = "";
					// Variabeln entsprechend dem Tabellennamen definieren
					switch($strTableName) {
						case "tbl_host":
							$strConfigName = $this->myDBClass->getFieldData("SELECT host_name FROM $strTableName WHERE id=".$arrData[$i]['id']);
							$setTemplate   = "hosts.tpl.dat";
							$strDirectory  = $this->arrSettings['nagios']['confighosts'];
							$strBackupdir  = $this->arrSettings['nagios']['backuphosts'];
							$strSQLData    = "SELECT * FROM $strTableName WHERE host_name='$strConfigName'";
							break;
						case "tbl_service":
							$strConfigName = $this->myDBClass->getFieldData("SELECT config_name FROM $strTableName WHERE id=".$arrData[$i]['id']);
							$setTemplate   = "services.tpl.dat";
							$strDirectory  = $this->arrSettings['nagios']['configservices'];
							$strBackupdir  = $this->arrSettings['nagios']['backupservices'];
							$strSQLData    = "SELECT * FROM $strTableName WHERE config_name='$strConfigName' ORDER BY service_description";
							break;
					}
					$strFilename   = $strConfigName.".cfg";
					// Falls ein Datenbankfehler aufgetreten ist, hier abbrechen
					if ($this->myDBClass->strDBError != "") {
						$this->strDBMessage = $this->arrLanguage['file']['failed'];
						return(1);
					}
					// Relationen holen
					$this->myDataClass->tableRelations($strTableName,$arrRelations);
					
					// Konfigurationsdatei sichern
					if ($intMode == 0) {
						
						if (file_exists($strDirectory.$strFilename) && is_writable($strDirectory.$strFilename)) {
							$strOldDate = date("YmdHis",mktime());
							copy($strDirectory.$strFilename,$strBackupdir.$strFilename."_old_".$strOldDate);
						} else if (file_exists($strDirectory.$strFilename)){
							$this->strDBMessage = $this->arrLanguage['file']['failed'];
							return(1);			
						}
						// Konfigurationsdatei ffnen
						if (is_writable($strDirectory.$strFilename) || (!file_exists($strDirectory.$strFilename))) {
							$CONFIGFILE = fopen($strDirectory.$strFilename,"w");
						} else {
							$this->strDBMessage = $this->arrLanguage['file']['failed'];
							return(1);
						}
					}
					// Alle passenden Datensätze holen
					$booReturn = $this->myDBClass->getDataArray($strSQLData,$arrDataConfig,$intDataCountConfig);
					// Konfigurationsvorlage laden
					$arrTplOptions = array('use_preg' => false);
					$configtp = new HTML_Template_IT($this->arrSettings['path']['physical']."/templates/files/");
					$configtp->loadTemplatefile($setTemplate, true, true);
					$configtp->setOptions($arrTplOptions);
					$configtp->setVariable("CREATE_DATE",date("Y-m-d H:i:s",mktime()));
					// Falls der Datensatz nicht gefunden wurde
					if ($booReturn == false) {
						$this->strDBMessage = $this->arrLanguage['db']['dberror']."<br>".$this->myDBClass->strDBError."<br>";		
					// Falls der Datensatz gefunden wurde
					} else if ($intDataCountConfig != 0) {
						// Jeden Datensatz verarbeiten
						for ($y=0;$y<$intDataCountConfig;$y++) {
							// Inaktive Datensätze überspringen
							if ($arrDataConfig[$y]['active'] == "0") continue;
							foreach($arrDataConfig[$y] AS $key => $value) {
								$intSkip = 0;
								// Spezialdatenfelder berspringen
								if (($value == "") || ($key == "id") || ($key == "config_name") || ($key == "active") || 
									($key == "last_modified") || ($key == "access_rights ") || ($key == "config_id") ||
									($key == "template")) {
									continue;
								}	
								// Ist das Datenfeld über eine Relation mit einem anderen Datenfeld verbunden?
								if (is_array($arrRelations)) {
									foreach($arrRelations AS $elem) {
										if ($elem['fieldName'] == $key) {
											// Handelt es sich um eine normale 1:n Relation?
											if (($value == 1) && ($elem['type'] == 2)) {
												$strSQLRel = "SELECT ".$elem['tableName'].".".$elem['target']." FROM tbl_relation
															  LEFT JOIN ".$elem['tableName']." ON tbl_relation.tbl_B_ID = ".$elem['tableName'].".id
															  WHERE tbl_A=".$this->myDataClass->tableId($strTableName)." 
															  	AND tbl_B=".$this->myDataClass->tableId($elem['tableName'])." 
																AND tbl_A_field='$key' AND tbl_A_id=".$arrDataConfig[$y]['id']." 
																AND ".$elem['tableName'].".".$elem['target']." IS NOT NULL
															  ORDER BY ".$elem['tableName'].".".$elem['target'];
															  
												$booReturn = $this->myDBClass->getDataArray($strSQLRel,$arrDataRel,$intDataCountRel);
												// Wurden Datensätze gefunden?
												if ($intDataCountRel != 0) {
													// Datenfeldwerte der gefundenen Datensätze eintragen
													$value = "";
													foreach ($arrDataRel AS $data) {
														$value .= $data[$elem['target']].",";
													}
													$value = substr($value,0,-1);
												} else {
													$intSkip = 1;
												}
											// Handelt es sich um den Ausnahmewert "*"?
											} else if (($value == 2) && ($elem['type'] == 2)) {
												$value = "*";
											// Handelt es sich um eine normale 1:1 Relation?
											} else if ($elem['type'] == 1) {
												if ($elem['tableName'] == "tbl_checkcommand") {
													$arrField   = explode("!",$arrDataConfig[$y][$elem['fieldName']]);
													$strCommand = strchr($arrDataConfig[$y][$elem['fieldName']],"!");
													$strSQLRel = "SELECT ".$elem['target']." FROM ".$elem['tableName']."
																  WHERE id=".$arrField[0];
												} else {
													$strSQLRel = "SELECT ".$elem['target']." FROM ".$elem['tableName']."
																  WHERE id=".$arrDataConfig[$y][$elem['fieldName']];
												}
												$booReturn = $this->myDBClass->getDataArray($strSQLRel,$arrDataRel,$intDataCountRel);
												// Wurden Datensätze gefunden?
												if ($booReturn && ($intDataCountRel != 0)) {
													// Datenfeldwert des gefundenen Datensatzes eintragen
													if ($elem['tableName'] == "tbl_checkcommand") {
														$value = $arrDataRel[0][$elem['target']].$strCommand;
													} else {
														$value = $arrDataRel[0][$elem['target']];
													}
												} else {
													$intSkip = 1;
												}
											} else {
												$intSkip = 1;
											}
										}
									}
								}
								// Falls das Datenfeld nicht übersprungen werden soll
								if ($intSkip != 1) {
									// Bei längeren Keys zusäzliche Tabulatoren einfgen
									if (strlen($key) <= 8) {$strFill  = "\t";} else {$strFill = "";}
									if (strlen($key) < 16)  $strFill .= "\t\t";
									if ((strlen($key) < 23) && (strlen($key) >= 16)) $strFill .= "\t";
									// Schlüssel und Wert in Template schreiben und nächste Zeile aufrufen
									$configtp->setVariable("ITEM_TITLE",$key.$strFill);
									$configtp->setVariable("ITEM_VALUE",stripslashes($value));
									$configtp->parse("configline");
								} 
							}
							// Ist die Konfiguration aktiv?
							$configtp->setVariable("ITEM_TITLE","register\t".$strFill);
							$configtp->setVariable("ITEM_VALUE",$arrDataConfig[$y]['active']);			
							$configtp->parse("configline");
							$configtp->parse("configset");
						}
					}		
					$configtp->parse();
					// Entsprechend dem Modus die Ausgabe in die Konfigurationsdatei schreiben oder direkt ausgeben
					if ($intMode == 0) {
						
						fwrite($CONFIGFILE,$configtp->get());
						fclose($CONFIGFILE);
						$this->myDataClass->writeLog($this->arrLanguage['logbook']['config']." ".$strFilename);
						$this->strDBMessage = $this->arrLanguage['file']['success'];	
						//return(0);
						
					}
					if ($intMode == 1) $configtp->show();
					//return(0);
				}
			}
		} else {
			$this->writeLog($this->arrLanguage['logbook']['configfaildb']);
			$this->strDBMessage = $this->arrLanguage['file']['failed'];	
			return(1);
		}
	}
}
?>