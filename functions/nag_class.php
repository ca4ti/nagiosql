<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL 2005
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005 by Martin Willisegger / nagios.ql2005@wizonet.ch
//
// Projekt:	NagiosQL Applikation
// Author :	Martin Willisegger
// Datum:	11.03.2005
// Zweck:	Administrationsklassen
// Datei:	functions/nag_class.php
// Version:	1.00
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Klasse: Allgemeine Darstellungsfunktionen
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Behandelt sämtliche Funktionen, zur Darstellung der Applikation notwendig 
// sind
//
// Version 1.02 - 30.03.2005 wim
//
// Name: nagvisual
//
// Klassenvariabeln:
// -----------------
// $arrSettings:	Mehrdimensionales Array mit den globalen Konfigurationseinstellungen
// $arrLanguage:	Mehrdimensionales Array mit den globalen Sprachstrings
// $myDBClass:		Datenbank Klassenobjekt
// $strDBMessage	Mitteilungen des Datenbankservers
// $strSQLStatement	SQL Statement
// $strTempValue1	Temporärer Wert 1
// $strTempValue2	Temporärer Wert 2
// $strTempValue3	Temporärer Wert 3
// $strMessage		Mitteilungen der Klassenfunktion
// $intCounter		Zählvariable
// $arrDataset		Array zum temporären abspeichern einer Datensatzgruppe
// $resTemplate		Objektvariable zum abspeichern der externen Templateklasse
// $arrWorkdata		Temoräres Arbeitsarray
//
// Externe Funktionen
// ------------------
// 
// 	
///////////////////////////////////////////////////////////////////////////////////////////////
class nagvisual {
    // Klassenvariabeln deklarieren
    var $arrSettings;
	var $arrLanguage;
	var $myDBClass;
	var $arrDataset;
	var $resTemplate;
	var $arrWorkdata;	
	var $strDBMessage    = "";
	var $strSQLStatement = "";
	var $strTempValue1   = "";
	var $strTempValue2   = "";
	var $strTempValue3   = "";
	var $strMessage		 = "";
	var $intCounter		 = 0;
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Klassenkonstruktor
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.00
	//  Datum:		09.03.2005	
	//  
	//  Tätigkeiten bei Klasseninitialisierung
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function nagvisual() {
		// Datenbankklasse initialisieren
		$this->myDBClass = new mysqldb;
		// Globale Einstellungen einlesen
		$this->arrSettings = $_SESSION['SETS'];
		// Sprachendatei einlesen
		$this->arrLanguage = $_SESSION['LANG'];		
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Hauptmenu anzeigen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.02
	//  Datum:		30.03.2005	
	//  
	//  Gibt das Hauptmenu aus
	//
	//  Übergabeparameter:	$intMain	ID des ausgewählten Hauptmenueintrages
	//	------------------	$intSub		ID des ausgewählten Submenueintrages (0, wenn kein)
	//						$intMenu	ID der Menugruppe
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function getMenu($intMain,$intSub,$intMenu) {
		//
		// URL für sichtbares/unsichtbares Menu modifizieren
		// =================================================
		$strQuery = str_replace("menu=visible&","",$_SERVER['QUERY_STRING']);
		$strQuery = str_replace("menu=invisible&","",$strQuery);
		$strQuery = str_replace("menu=visible","",$strQuery);
		$strQuery = str_replace("menu=invisible","",$strQuery);
		if ($strQuery != "") {
			$strURIVisible   = str_replace("&","&amp;",$_SERVER['PHP_SELF']."?menu=visible&".$strQuery);
			$strURIInvisible = str_replace("&","&amp;",$_SERVER['PHP_SELF']."?menu=invisible&".$strQuery);
		} else {
			$strURIVisible 	 = $_SERVER['PHP_SELF']."?menu=visible";
			$strURIInvisible = $_SERVER['PHP_SELF']."?menu=invisible";	
		}
		//
		// Menupunkte aus Datenbank auslesen und in Arrays speichern
		// =========================================================
		$strSQLMain = "SELECT id, item, link, rights FROM tbl_mainmenu WHERE menu_id = $intMenu ORDER BY order_id";
		$strSQLSub  = "SELECT id, item, link, rights FROM tbl_submenu WHERE id_main = $intMain ORDER BY order_id";
		
		// Datensätze für das Hauptmenu in einem numerischen Array speichern
		$booReturn = $this->myDBClass->getDataArray($strSQLMain,$arrDataMain,$intDataCountMain);
		if (($booReturn != false) && ($intDataCountMain != 0)) {
			$y=1;
			for ($i=0;$i<$intDataCountMain;$i++) {
				// Menupunkt nur in Array übertragen, wenn der Benutzer über die nötigen Rechte verfügt
				if (isset($_SESSION['rights'][$arrDataMain[$i]['rights']]) && $_SESSION['rights'][$arrDataMain[$i]['rights']] == 1) {
					$arrMainLink[$y] = $this->arrSettings['path']['root'].$arrDataMain[$i]['link'];
					$arrMainId[$y]   = $arrDataMain[$i]['id'];
					$arrMain[$y] 	 = $this->arrLanguage['menu'][$arrDataMain[$i]['item']];
					$y++;
				}
			}
		} else {
			return(1);
		}
		// Datensätze für das Untermenu in einem numerischenArray speichern
		$booReturn = $this->myDBClass->getDataArray($strSQLSub,$arrDataSub,$intDataCountSub);
		if (($booReturn != false) && ($intDataCountSub != 0)) {
			$y=1;
			for ($i=0;$i<$intDataCountSub;$i++) {
				// Menupunkt nur in Array übertragen, wenn der Benutzer über die nötigen Rechte verfügt
				if (isset($_SESSION['rights'][$arrDataSub[$i]['rights']]) && $_SESSION['rights'][$arrDataSub[$i]['rights']] == 1) {
					$arrSubLink[$y] = $this->arrSettings['path']['root'].$arrDataSub[$i]['link'];
					$arrSubID[$y]   = $arrDataSub[$i]['id'];
					$arrSub[$y]     = $this->arrLanguage['menu'][$arrDataSub[$i]['item']];
					$y++;
				}
			}
		}
		//
		// Ausgabe der kompletten Menustruktur
		// ===================================
		if (!(isset($_SESSION['menu'])) || ($_SESSION['menu'] != "invisible")) {
			// Menu ist eingeblendet
			echo "<td width=\"150\" align=\"center\" valign=\"top\">\n"; 
			echo "<table cellspacing=\"5\" class=\"menutable\">\n";
			// Jeden Hauptmenueintrag abarbeiten
			for ($i=1;$i<=count($arrMain);$i++) {
				echo "<tr>\n";
				if ($arrMainId[$i] == $intMain) {
					echo "<td class=\"menuaktiv\"><a href=\"".$arrMainLink[$i]."\">".$arrMain[$i]."</a></td>\n</tr>\n";  
					// Falls Untermenueintrag existiert
					if (isset($arrSub)) {
						echo "<tr>\n<td class=\"menusub\">\n";
						// Jeden Untermenueintrag abarbeiten
						for ($y=1;$y<=count($arrSub);$y++) {
							if ((isset($arrSubLink[$y])) && ($arrSubLink[$y] != "")) {
								if ($arrSubID[$y] == $intSub) {
									echo "<a class=\"menulink\" href=\"".$arrSubLink[$y]."\"><b>".$arrSub[$y]."</b></a><br>\n";
								} else {
									echo "<a class=\"menulink\" href=\"".$arrSubLink[$y]."\">".$arrSub[$y]."</a><br>\n";
								}	
							} 
						}
						echo "</td>\n</tr>\n";
					}
				} else {
					echo "<td class=\"menuinaktiv\"><a href=\"".$arrMainLink[$i]."\">".$arrMain[$i]."</a></td>\n</tr>\n";  		
				}
			}  
			echo "</table>\n";
			echo "<br><a href=\"$strURIInvisible\" class=\"menulinksmall\">[".$this->arrLanguage['menu']['disable']."]</a>\n";
			echo "</td>\n";
		} else {
			// Menu ist ausgeblendet
			echo "<td valign=\"top\">\n"; 
			echo "<a href=\"$strURIVisible\"><img src=\"".$this->arrSettings['path']['root']."images/menu.gif\" alt=\"".$this->arrLanguage['menu']['enable']."\" border=\"0\"></a>\n"; 
			echo "</td>\n";
		}
		return(0);
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Letzte Datentabellenänderung und letzte Konfigurationsdateiänderung
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.02
	//  Datum:		30.03.2005	
	//  
	//  Ermittelt die Zeitpunkte der letzten Datentabellenänderung sowie der letzten Änderung an
	// der Konfigurationsdatei
	//
	//  Übergabeparameter:	$strTableName	Datentabellenname
	//	------------------
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//	Rückgabewerte:		$strTimeTable	Datum der letzten Datentabellenänderung
	//						$strTimeFile	Datum der letzten Konfigurationsdateiänderung
	//						$strCheckConfig	Informationsstring, falls Datei älter als Tabelle		
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function lastModified($strTableName,&$strTimeTable,&$strTimeFile,&$strCheckConfig) {
		// Konfigurationsdatei entsprechend dem Tabellennamen festlegen
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
		$strCheckConfig = "";
		$strTimeTable   = "unknown";
		$strTimeFile	= "unknown";
		// Statuscache löschen
		clearstatcache();
		// Letzte Änderung an der Datentabelle auslesen
		$this->myDBClass->getSingleDataset("SHOW TABLE STATUS LIKE '$strTableName'",$arrDataset);
		$strTimeTable = $arrDataset['Update_time'];
		// Letzte Änderung an der Konfigurationsdatei auslesen
		if (file_exists($this->arrSettings['nagios']['config'].$strFile)) {
			$intFileStamp = filectime($this->arrSettings['nagios']['config'].$strFile);
			$strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
			// Falls Datei älter, den entsprechenden String zurückgeben
			if (strtotime($strTimeTable) > $intFileStamp) $strCheckConfig = $this->arrLanguage['common']['older'];
			return(0);
		} else {
			return(1);
		}
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Letzte Datensatzänderung und letzte Konfigurationsdateiänderung
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.02
	//  Datum:		30.03.2005	
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
		$intCheck    = 0;
		// Statuscache löschen
		clearstatcache();
		// Letzte Änderung an der Datentabelle auslesen
		if ($strType == "host") {
			$strTime = $this->myDBClass->getFieldData("SELECT DATE_FORMAT(last_modified,'%Y-%m-%d %H:%i:%s') FROM tbl_host WHERE id=".$strId);
			$strPath = $this->arrSettings['nagios']['confighosts'];
			$intCheck++;
		} else if ($strType == "service") {
			$strTime = $this->myDBClass->getFieldData("SELECT DATE_FORMAT(last_modified,'%Y-%m-%d %H:%i:%s') FROM tbl_service WHERE id=".$strId);
			$strPath = $this->arrSettings['nagios']['configservices'];
			$intCheck++;
		} else {
			$strTime      = "undefined";
			$intOlder     = 1;
		}	
		// Letzte Änderung an der Konfigurationsdatei auslesen
		if (file_exists($strPath.$strFilename)) {
			$intFileStamp = filectime($strPath.$strFilename);
			$strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
			$intCheck++;
		} else {
			$strTimeFile = "undefined";
			$intOlder    = 1;
		}
		// Falls beide Werte gültig, vergleichen
		if ($intCheck == 2) {
			if (strtotime($strTime) > $intFileStamp) {$intOlder = 1;} else {$intOlder = 0;}
		}
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Daten in die Datenbank schreiben
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.02
	//  Datum:		30.03.2005	
	//  
	//  Sendet einen übergebenen SQL String an den Datenbankserver und werter die Rückgabe
	//  des Servers aus.
	//
	//  Übergabeparameter:	$strSQL		SQL Befehl
	//	------------------	
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//  Rückgabewert:		Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dataInsert($strSQL) {
		// Daten an Datenbankserver senden
		$booReturn = $this->myDBClass->insertData($strSQL);
		if ($booReturn == true) {
			// Erfolgreich
			$this->strDBMessage = $this->arrLanguage['db']['success'];
			return(0);
		} else {
			// Misserfolg
			$this->strDBMessage = $this->arrLanguage['db']['failed']."<br>".$this->myDBClass->strDBError;
			return(1);
		}
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Daten aus Datenbank löschen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.02
	//  Datum:		30.03.2005	
	//  
	//  Löscht eine oder mehrere Datensätze aus einer Datentabelle. Wahlweise kann eine 
	//  einzelne Datensatz ID angegeben werden oder die Werte der mittels $_POST['chbId_n'] 
	//	übergebenen Parameter ausgewertet werden, wobei "n" der Datensatz ID entsprechen muss.
	//
	//  Übergabeparameter:	$strTableName	Tabellenname
	//	------------------	$_POST[]		Formularausgabe (Checkboxen "chbId_n" n=DBId)
	//						$intDataId		Einzelne Datensatz ID, welche zu löschen ist
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//  Rückgabewert:		Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dataDelete($strTableName,$intDataId = 0) {
		//
		// Datenbankeinträge löschen
		// =========================
		$intError=0; $intNumber=0; $intFileDel=0;
		// Alle Datensätze der angegebenen Tabelle holen
		$booReturn = $this->myDBClass->getDataArray("SELECT id FROM ".$strTableName,$arrData,$intDataCount);
		if ($booReturn == false) {
			$this->strDBMessage = $this->arrLanguage['db']['dberror']."<br>".$this->myDBClass->strDBError."<br>";		
		} else if ($intDataCount != 0) {
			for ($i=0;$i<$intDataCount;$i++) {		
				// Formularübergabeparameter zusammenstellen
				$strChbName = "chbId_".$arrData[$i]['id'];
				// Falls ein Parameter mit diesem Namen übergeben wurde
				if (isset($_POST[$strChbName]) || ($intDataId == $arrData[$i]['id'])) {
				    // Konfigurationsname/Hostname ermitteln
					if ($strTableName == "tbl_service") {
						$strConfigName = $this->myDBClass->getFieldData("SELECT config_name FROM $strTableName WHERE id=".$arrData[$i]['id']);
					} else if ($strTableName == "tbl_host") {
						$strHostName   = $this->myDBClass->getFieldData("SELECT host_name FROM $strTableName WHERE id=".$arrData[$i]['id']);
					}
					// Datenbankeintrag löschen
					$booReturn = $this->myDBClass->insertData("DELETE FROM $strTableName WHERE id=".$arrData[$i]['id']);
					if ($booReturn != true) {
						// Misserfolg
						$intError++; 
						$this->writeLog($this->arrLanguage['logbook']['deletedatafail']." ".$strTableName." [".$arrData[$i]['id']."]");
					} else {
						// Erfolg
						$this->writeLog($this->arrLanguage['logbook']['deletedata']." ".$strTableName." [".$arrData[$i]['id']."]");
					}
					$intNumber++;
					// Falls Service betroffen - evtl. Konfigurationsdatei löschen
					if (isset($strConfigName) && ($strConfigName != "") && ($booReturn != false)) {
						// Falls es keine weiteren Einträge mit diesem Konfigurationsnamen gibt, die Datei löschen
						$intServiceRows = $this->myDBClass->countRows("SELECT * FROM $strTableName WHERE config_name='$strConfigName'");
						if ($intServiceRows == 0) {
							$strFilename = $strConfigName.".cfg";
							if (file_exists($this->arrSettings['nagios']['configservices'].$strFilename) && is_writeable($this->arrSettings['nagios']['configservices'].$strFilename)) {
								$strOldDate = date("YmdHis",mktime());
								copy($this->arrSettings['nagios']['configservices'].$strFilename,$this->arrSettings['nagios']['backupservices'].$strFilename."_old_".$strOldDate);					
								unlink($this->arrSettings['nagios']['configservices'].$strFilename);
								$this->writeLog($this->arrLanguage['logbook']['delservice']." ".$strFilename);
								$intFileDel++;
							}
						}						
					} else if (isset($strHostName) && ($strHostName != "") && ($booReturn != false)) {
						// Falls es keine weiteren Einträge mit diesem Hostnamen gibt, die Datei löschen
						$intHostRows = $this->myDBClass->countRows("SELECT * FROM $strTableName WHERE host_name='$strHostName'");
						if ($intHostRows == 0) {
							$strFilename = $strHostName.".cfg";
							if (file_exists($this->arrSettings['nagios']['confighosts'].$strFilename) && is_writeable($this->arrSettings['nagios']['confighosts'].$strFilename)) {
								$strOldDate = date("YmdHis",mktime());
								copy($this->arrSettings['nagios']['confighosts'].$strFilename,$this->arrSettings['nagios']['backuphosts'].$strFilename."_old_".$strOldDate);					
								unlink($this->arrSettings['nagios']['confighosts'].$strFilename);
								$this->writeLog($this->arrLanguage['logbook']['delhost']." ".$strFilename);
								$intFileDel++;
							}
						}					
					}
				}
			}
		}
		// Fehlerbehandlung
		if ($intNumber > 0) {
			if ($intError == 0) {
				// Erfolg
				$this->strDBMessage = $this->arrLanguage['db']['success_del'];
				if ($intFileDel != 0) $this->strDBMessage .= "<br>".$this->arrLanguage['file']['success_del'];
				return(0);
			} else {
				// Misserfolg
				$this->strDBMessage = $this->arrLanguage['db']['failed_del'];
				return(1);
			}
		}
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Datensätze kopieren
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.02
	//  Datum:		30.03.2005	
	//  
	//  Kopiert einen oder mehrere Datensätze in einer Datentabelle. Wahlweise kann eine 
	//  einzelne Datensatz ID angegeben werden oder die Werte der mittels $_POST['chbId_n'] 
	//	übergebenen Parameter ausgewertet werden, wobei "n" der Datensatz ID entsprechen muss.
	//
	//  Übergabeparameter:	$strTableName	Tabellenname
	//	------------------	$_POST[]		Formularausgabe (Checkboxen "chbId_n" n=DBId)
	//						$intDataId		Einzelne Datensatz ID, welche zu löschen ist
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//  Rückgabewert:		Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dataCopy($strTableName,$intDataId = 0) {
		// Keyfeld entsprechend dem Tabellennamen festlegen
		switch($strTableName) {
			case "tbl_timeperiod":			$strKeyField  = "timeperiod_name"; break;
			case "tbl_misccommand":			$strKeyField  = "command_name"; break;
			case "tbl_checkcommand":		$strKeyField  = "command_name"; break;
			case "tbl_contact":				$strKeyField  = "contact_name"; break;
			case "tbl_contactgroup":		$strKeyField  = "contactgroup_name"; break;
			case "tbl_hostgroup":			$strKeyField  = "hostgroup_name"; break;
			case "tbl_servicegroup":		$strKeyField  = "servicegroup_name"; break;
			case "tbl_host":				$strKeyField  = "host_name"; break;
			case "tbl_service":				$strKeyField  = "service_description"; break;
			case "tbl_servicedependency":	$strKeyField  = "config_name"; break;
			case "tbl_hostdependency":		$strKeyField  = "config_name"; break;
			case "tbl_serviceescalation":	$strKeyField  = "config_name"; break;							
			case "tbl_hostescalation":		$strKeyField  = "config_name"; break;
			case "tbl_hostextinfo":			$strKeyField  = "host_name"; break;
			case "tbl_serviceextinfo":		$strKeyField  = "service_description"; break;
			case "tbl_user":				$strKeyField  = "username"; break;							
		}
		$intError=0; $intNumber=0;
		$intAnzDS  = $this->myDBClass->countRows("SELECT * FROM $strTableName");
		$booReturn = $this->myDBClass->getDataArray("SELECT id FROM $strTableName ORDER BY id",$arrData,$intDataCount);
		if ($booReturn == false) {
			$this->strDBMessage = $this->arrLanguage['db']['dberror']."<br>".$this->myDBClass->strDBError."<br>";		
		} else if ($intDataCount != 0) {
			for ($i=0;$i<$intDataCount;$i++) {
				// Formularübergabeparameter zusammenstellen
				$strChbName = "chbId_".$arrData[$i]['id'];
				// Falls ein Parameter mit diesem Namen übergeben oder eine Datensatz ID angegeben wurde
				if (isset($_POST[$strChbName]) || (($intDataId != 0) && ($intDataId == $arrData[$i]['id']))) {
					// Daten des entsprechenden Eintrages holen
					$this->myDBClass->getSingleDataset("SELECT * FROM $strTableName WHERE id=".$arrData[$i]['id'],$arrData[$i]);
					// Namenszusatz erstellen
					for ($y=1;$y<=$intAnzDS;$y++) {
						$strNewName = $arrData[$i][$strKeyField]." ($y)";
						$booReturn = $this->myDBClass->getFieldData("SELECT id FROM $strTableName WHERE $strKeyField='$strNewName'");
						// Falls den neue Name einmalig ist, abbrechen
						if ($booReturn == false) break;
					}
					// Entsprechend dem Tabellennamen den Datenbank-Insertbefehl zusammenstellen
					$strSQLInsert = "INSERT INTO $strTableName SET $strKeyField='$strNewName',";
					foreach($arrData[$i] AS $key => $value) {
						if (($key != $strKeyField) && ($key != "active") && ($key != "last_modified") && ($key != "id")) {
							// NULL Werte setzen
							if (($key == "normal_check_interval") 	&& ($value == "")) 	$value="NULL";
							if (($key == "retry_check_interval") 	&& ($value == "")) 	$value="NULL";
							if (($key == "max_check_attempts") 		&& ($value == ""))	$value="NULL";
							if (($key == "low_flap_threshold") 		&& ($value == ""))	$value="NULL";							
							if (($key == "high_flap_threshold") 	&& ($value == ""))	$value="NULL";
							if (($key == "freshness_threshold") 	&& ($value == "")) 	$value="NULL";
							if (($key == "notification_interval") 	&& ($value == "")) 	$value="NULL";
							if (($key == "check_interval") && ($value == "")) 			$value="NULL";
							if ($value != "NULL") {							
								$strSQLInsert .= $key."='".addslashes($value)."',";
							} else {
								$strSQLInsert .= $key."=".$value.",";							
							}
						}
					}
					$strSQLInsert .= "active='0', last_modified=NOW()";
					// Kopie in die Datenbank eintragen
					$booReturn = $this->myDBClass->insertData($strSQLInsert);
					if ($booReturn != true) {
						// Misserfolg
						$intError++;
						$this->writeLog($this->arrLanguage['logbook']['copydatafail']." ".$strTableName." [".$strNewName."]");
					} else {
						// Erfolg
						$this->writeLog($this->arrLanguage['logbook']['copydata']." ".$strTableName." [".$strNewName."]");					
					} 
					$intNumber++;
				}				
			}
		}
		// Fehlerbehandlung
		if ($intNumber > 0) {
			if ($intError == 0) {
				// Erfolg
				$this->strDBMessage = $this->arrLanguage['db']['success'];
			} else {
				// Misserfolg
				$this->strDBMessage = $this->arrLanguage['db']['failed']."<br>".$this->myDBClass->strDBError;
			}
		}
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Konfigurationsdatei schreiben
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.02
	//  Datum:		30.03.2005	
	//  
	//  Schreibt ein einzelnes Konfigurationsfile mit allen Datensätzen einer Tabelle oder
	//  liefert die Ausgabe als Textdatei zum Download aus.
	//
	//  Übergabeparameter:	$strTableName	Tabellenname
	//	------------------	$intMode		0 = Datei schreiben, 1 = Ausgabe für Download
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//  Rückgabewert:		Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function createConfig($strTableName,$intMode = 0) {
		// Variabeln entsprechend dem Tabellennamen definieren
		switch($strTableName) {
			case "tbl_timeperiod":			$strFileString = "timeperiods"; 		$strOrderField = "timeperiod_name";					$strAddFill = "";		break;
			case "tbl_misccommand":			$strFileString = "misccommands"; 		$strOrderField = "command_name";					$strAddFill = "";		break;
			case "tbl_checkcommand":		$strFileString = "checkcommands"; 		$strOrderField = "command_name";					$strAddFill = "";		break;
			case "tbl_contact":				$strFileString = "contacts"; 			$strOrderField = "contact_name";					$strAddFill = "\t\t";	break;
			case "tbl_contactgroup":		$strFileString = "contactgroups"; 		$strOrderField = "contactgroup_name";				$strAddFill = "\t\t";	break;
			case "tbl_hostgroup":			$strFileString = "hostgroups"; 			$strOrderField = "hostgroup_name";					$strAddFill = "\t";		break;
			case "tbl_servicegroup":		$strFileString = "servicegroups"; 		$strOrderField = "servicegroup_name";				$strAddFill = "\t\t";	break;
			case "tbl_servicedependency":	$strFileString = "servicedependencies"; $strOrderField = "dependent_host_name";				$strAddFill = "\t\t";	break;
			case "tbl_hostdependency":		$strFileString = "hostdependencies"; 	$strOrderField = "dependent_host_name";				$strAddFill = "\t\t";	break;
			case "tbl_serviceescalation":	$strFileString = "serviceescalations"; 	$strOrderField = "host_name,service_description";	$strAddFill = "\t\t";	break;
			case "tbl_hostescalation":		$strFileString = "hostescalations"; 	$strOrderField = "host_name,hostgroup_name";		$strAddFill = "\t\t";	break;
			case "tbl_hostextinfo":			$strFileString = "hostextinfo"; 		$strOrderField = "host_name";						$strAddFill = "\t\t";	break;
			case "tbl_serviceextinfo":		$strFileString = "serviceextinfo"; 		$strOrderField = "host_name";						$strAddFill = "\t\t";	break;
			default:						return(1);
		}
		// SQL Abfrage festlegen und Dateinamen definieren
		$strSQL = "SELECT * FROM $strTableName WHERE active='1' ORDER BY $strOrderField";
		$strFile 	 = $strFileString.".cfg";
		$setTemplate = $strFileString.".tpl.dat";

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
			    $this->writeLog($this->arrLanguage['logbook']['configfail']." ".$strFile);
				$this->strDBMessage = $this->arrLanguage['file']['failed'];
				return(1);		
			}
		}	
		// Konfiguration in Konfigurationsdatei schreiben
		$configtp  = new HTML_Template_IT($this->arrSettings['path']['physical']."/templates/files/");
		$configtp->loadTemplatefile($setTemplate, true, true);
		$configtp->setVariable("CREATE_DATE",date("Y-m-d H:i:s",mktime()));
		// Datenbank abfragen und Resultat verarbeiten
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
		if ($booReturn == false) {
			$this->strDBMessage = $this->arrLanguage['db']['dberror']."<br>".$this->myDBClass->strDBError."<br>";		
		} else if ($intDataCount != 0) {
			for ($i=0;$i<$intDataCount;$i++) {
				// Für jeden Datensatz einen Templateabschnitt eintragen
				foreach($arrData[$i] AS $key => $value) {
					// Spezialfelder nicht übernehmen
					if (($value != "") && ($key != "id") && ($key != "config_name") && ($key != "active") && ($key != "last_modified")) {
						// Bei längeren Keys zusäzliche Tabulatoren einfügen
						if (strlen($key) < 8) {$strFill = "\t";} else {$strFill = "";}
						if ((strlen($key) < 16) && isset($strAddFill))   $strFill .= $strAddFill;
						if ((strlen($key) < 23) && (strlen($key) >= 17)) $strFill .= "\t";
						// Schlüssel und Wert in Template schreiben und nächste Zeile aufrufen
						$configtp->setVariable("ITEM_TITLE",$key.$strFill);
						$configtp->setVariable("ITEM_VALUE",$value);
						$configtp->parse("configline");
					} 
				}
				$configtp->parse("configset");
			}
		}		
		$configtp->parse();
		// Entsprechend dem Modus die Ausgabe in die Konfigurationsdatei schreiben oder direkt ausgeben
		if ($intMode == 0) {
			fwrite($CONFIGFILE,$configtp->get());
			fclose($CONFIGFILE);
			$this->writeLog($this->arrLanguage['logbook']['config']." ".$strFile);
			$this->strDBMessage = $this->arrLanguage['file']['success'];	
			return(0);
		}
		if ($intMode == 1) $configtp->show();
		return(0);
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Konfigurationsdatei für einzelnen Datensatz schreiben
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.02
	//  Datum:		30.03.2005	
	//  
	//  Schreibt ein einzelnes Konfigurationsfile mit einem einzelnen Datensatz einer Tabelle oder
	//  liefert die Ausgabe als Textdatei zum Download aus.
	//
	//  Übergabeparameter:	$strTableName	Tabellenname
	//	------------------	$intDbId		Datensatz ID
	//						$intMode		0 = Datei schreiben, 1 = Ausgabe für Download
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//  Rückgabewert:		Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function createConfigSingle($strTableName,$intDbId = 0,$intMode = 0) {
		if ($intDbId != 0) {$strWHERE = "WHERE id=$intDbId";} else {$strWHERE="";}
		// Alle Datensatz ID der Tabelle holen
		$booReturn = $this->myDBClass->getDataArray("SELECT id FROM $strTableName $strWHERE ORDER BY id",$arrData,$intDataCount);
		if (($booReturn != false) && ($intDataCount != 0)) {
			for ($i=0;$i<$intDataCount;$i++) {
				// Formularübergabeparameter zusammenstellen
				$strChbName = "chbId_".$arrData[$i]['id'];
				// Falls ein Parameter mit diesem Namen übergeben oder eine Datensatz ID angegeben wurde
				if (isset($_POST[$strChbName]) || (($intDbId != 0) && ($intDbId == $arrData[$i]['id']))) {	
					// Variabeln entsprechend dem Tabellennamen definieren
					$this->myDBClass->strDBError = "";
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
					// Konfigurationsdatei sichern
					if ($intMode == 0) {
						if (file_exists($strDirectory.$strFilename) && is_writable($strDirectory.$strFilename)) {
							$strOldDate = date("YmdHis",mktime());
							copy($strDirectory.$strFilename,$strBackupdir.$strFilename."_old_".$strOldDate);
						} else if (file_exists($strDirectory.$strFilename)){
							$this->strDBMessage = $this->arrLanguage['file']['failed'];
							return(1);			
						}
						// Konfigurationsdatei öffnen
						if (is_writable($strDirectory.$strFilename) || (!file_exists($strDirectory.$strFilename))) {
							$CONFIGFILE = fopen($strDirectory.$strFilename,"w");
						} else {
							$this->strDBMessage = $this->arrLanguage['file']['failed'];
							return(1);
						}
					}
					// Alle passenden Datensätze holen
					$booReturn = $this->myDBClass->getDataArray($strSQLData,$arrDataConfig,$intDataCountConfig);
					// Konfiguration in Konfigurationsdatei schreiben
					$configtp = new HTML_Template_IT($this->arrSettings['path']['physical']."/templates/files/");
					$configtp->loadTemplatefile($setTemplate, true, true);
					$configtp->setVariable("CREATE_DATE",date("Y-m-d H:i:s",mktime()));
					if ($booReturn == false) {
						$this->strDBMessage = $this->arrLanguage['db']['dberror']."<br>".$this->myDBClass->strDBError."<br>";		
					} else if ($intDataCountConfig != 0) {
						for ($i=0;$i<$intDataCountConfig;$i++) {
							// Für jeden Datensatz einen Templateabschnitt eintragen
							foreach($arrDataConfig[$i] AS $key => $value) {
								// Spezialfelder nicht übernehmen
								if (($value != "") && ($key != "id") && ($key != "config_name") && ($key != "active") && ($key != "last_modified")) {
									if (strlen($key) <= 8) {$strFill  = "\t";} else {$strFill = "";}
									if (strlen($key) < 16)  $strFill .= "\t\t";
									if ((strlen($key) < 23) && (strlen($key) >= 16)) $strFill .= "\t";
									// Schlüssel und Wert in Template schreiben und nächste Zeile aufrufen
									$configtp->setVariable("ITEM_TITLE",$key.$strFill);
									$configtp->setVariable("ITEM_VALUE",$value);
									$configtp->parse("configline");
								} 
							}
							// Ist die Konfiguration aktiv?
							$configtp->setVariable("ITEM_TITLE","register\t".$strFill);
							$configtp->setVariable("ITEM_VALUE",$arrDataConfig[$i]['active']);			
							$configtp->parse("configline");
							$configtp->parse("configset");
						}
					}		
					$configtp->parse();
					// Entsprechend dem Modus die Ausgabe in die Konfigurationsdatei schreiben oder direkt ausgeben
					if ($intMode == 0) {
						fwrite($CONFIGFILE,$configtp->get());
						fclose($CONFIGFILE);
						$this->writeLog($this->arrLanguage['logbook']['config']." ".$strFilename);
						$this->strDBMessage = $this->arrLanguage['file']['success'];	
						return(0);
					}
					if ($intMode == 1) $configtp->show();
					return(0);
				}
			}
		} else {
			$this->writeLog($this->arrLanguage['logbook']['configfaildb']);
			$this->strDBMessage = $this->arrLanguage['file']['failed'];	
			return(1);
		}
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Datenimport
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.01
	//  Datum:		29.03.2005	
	//  
	//  Importiert eine Konfigurationsdatei und schreibt deren Daten in die entsprechende
	//	Datentabelle
	//
	//  Übergabeparameter:	$strFileName	Importdateiname
	//	------------------	$intOverwrite	0 = Daten überschreiben, 1 = Ausgabe für Download
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//  Rückgabewert:		Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function fileImport($strFileName,$intOverwrite) {
		// Variabeln deklarieren
		$intBlock	 = 0;
		$intCheck	 = 0;
		$strFileName = trim($strFileName);
		// Ist die Datei lesbar?
		if (!is_readable($strFileName)) {
			$this->strDBMessage .= $this->arrLanguage['file']['notreadable']."<br>";
			return(1);
		}
		// Konfigurationsdatei öffnen und zeilenweise einlesen
		$resFile = fopen($strFileName,"r");
		while(!feof($resFile)) {
			$strConfLine = fgets($resFile,1024);
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
					$intTplReturn = $this->insertTemplate($strFileName,str_replace($arrLine[0]." ","",$strNewLine),$arrData);
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
					if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
				}
			}			
		}
		return($intCheck);			
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Auswahlfeld in Kommastring überführen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.00
	//  Datum:		09.03.2005	
	//  
	//  Schreibt die per Array übergebenen Einzelwerte in einen String hintereinander mit Komma
	//  getrennt.
	//
	//  Übergabeparameter:	$arrData	Datenarray
	//	------------------	
	//
	//  Returnwert:			Kommagetrennter Datenstring
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function makeCommaString($arrData) {
		$strReturn = "";
		for($i=0;$i<count($arrData);$i++) {
			if ($arrData[$i] != "") $strReturn .= $arrData[$i].",";	
		}
		return(substr($strReturn,0,-1));
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Seitenlinks zusammenstellen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.00
	//  Datum:		09.03.2005	
	//  
	//  Erstellt einen String, der die Links für die einzelnen Seiten zum anwählen enthält
	//
	//  Übergabeparameter:	$strSite		Link zur Seite
	//	------------------	$intCount		Anzahl Datensätze
	//						$chkLimit		Aktuelles Limit (Seitenlink fettschreiben)
	//						$chkSelOrderBy	OrderBy-String (für Services Seite)
	//
	//  Returnwert:			Kommagetrennter Linkstring
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function buildPageLinks($strSite,$intCount,$chkLimit,$chkSelOrderBy="") {
		$y=1;
		$strPages = $this->arrLanguage['admintable']['pages']." [ ";
		// In Schritten von 15 die Datensätze in Seiten unterteilen
		for($i=0;$i<$intCount;$i=$i+15) {
			// Aktuelle Seitennummer fett schreiben
		    if ($i == $chkLimit) {$strNumber = "<b>$y</b>";} else {$strNumber = $y;}
			if ($chkSelOrderBy == "") {
				$strPages .= "<a href=\"".$strSite."?limit=$i\">".$strNumber."</a> ";
			} else {
				$strOrderBy = rawurlencode($chkSelOrderBy);
				$strPages .= "<a href=\"".$strSite."?limit=$i&orderby=$chkSelOrderBy\">".$strNumber."</a> ";
			} 
			$y++;
		}
		$strPages .= " ] ";
		// Linkstring zurückgeben
		return($strPages);
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Auswahlfeld aufbauen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.02
	//  Datum:		30.03.2005	
	//  
	//  Erstellt einen String, der die Links für die einzelnen Seiten zum anwählen enthält
	//
	//  Übergabeparameter:	$strSQL			SQL Abfrage für Auswahlfeld
	//	------------------	$strParseVar	Templateschlüssel für Datenwert [{DAT_XXX}]
	//						$strDataField	Tabellenfeldname des auszugebenden Datenfeldes
	//						$strModifyField	Tabellenfeldname in das die Daten in der aktuellen 
	//										Tabelle eingetragen werden
	//						$strParseGroup	Templategruppe des Auswahlfeldes [$templ->parse(xxx)]
	//						$intMode		0 = nur Daten, 1 = mit Leerzeile, 2 = mit Leerzeile und "*"
	//						$strRefresh		Formularfeldname (optional, falls Formular mit Refresh)
	//						
	//	Klassenvariabeln:	$this->resTemplate		Templateobjekt
	//	-----------------	$this->strTempValue1	Modus ("modify","add" etc.)
	//						$this->strTempValue2	Modus ("refresh")
	//						$this->strTempValue3	Erster Datenbankeintrag (Rückgabewert)
	//
	//  Returnwert:			Kommagetrennter Linkstring
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function parseSelect($strSQL,$strParseVar,$strDataField,$strModifyField,$strParseGroup,$intMode = 0,$strRefresh= "") {
		// Leerzeile einfügen
		if (($intMode == 1) || ($intMode == 2)) {
			$this->resTemplate->setVariable($strParseVar,"");
			$this->resTemplate->parse($strParseGroup);
		}
		// "*" Zeile einfügen
		if ($intMode == 2) {
			$this->resTemplate->setVariable($strParseVar,"*"); 
			// Modus "modify": Wird "*" im gespeicherten Datensatz verwendet?
			if (($this->strTempValue1 == "modify") && ($this->arrWorkdata[$strModifyField] != "") && 
				(substr_count($this->arrWorkdata[$strModifyField],"*") != 0)) {
				$this->resTemplate->setVariable($strParseVar."_SEL","selected","selected");
			}	
			// Modus "refresh": Ist "*" zurzeit ausgewählt?
			if (($this->strTempValue2 == "refresh") && (substr_count($strRefresh,"*") != 0)) {
				$this->resTemplate->setVariable($strParseVar."_SEL","selected","selected");
			}
			$this->resTemplate->parse($strParseGroup);		
		}
		// Datensätze aus der Datenbank holen
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
		if (($booReturn != false) && ($intDataCount != 0)) {
			for ($i=0;$i<$intDataCount;$i++) {
				$this->resTemplate->setVariable($strParseVar,$arrData[$i][$strDataField]);
				// Erster Datensatzeintrag in Klassenvariable speichern
				if ($i == 0) $this->strTempValue3 = $arrData[$i][$strDataField];
				// Modus "modify": Wird dieser Datenwert im gespeicherten Datensatz verwendet?
				if (($this->strTempValue1 == "modify") && ($this->arrWorkdata[$strModifyField] != "")) {
					if (substr_count($this->arrWorkdata[$strModifyField],"!")) {
						$arrCheck = explode("!",$this->arrWorkdata[$strModifyField]);
						if ($arrCheck[0] == $arrData[$i][$strDataField]) {
							$this->resTemplate->setVariable($strParseVar."_SEL","selected");
						}
					} else if (substr_count($this->arrWorkdata[$strModifyField],",")) {
						$arrCheck = explode(",",$this->arrWorkdata[$strModifyField]);
						foreach ($arrCheck AS $elem) {
							if ($elem == $arrData[$i][$strDataField]) {
								$this->resTemplate->setVariable($strParseVar."_SEL","selected");
							}					
						}
					} else if ($this->arrWorkdata[$strModifyField] == $arrData[$i][$strDataField]) {
						$this->resTemplate->setVariable($strParseVar."_SEL","selected");
					}
				}
				// Modus "refresh": Ist dieser Datenwert zurzeit ausgewählt?
				if ($this->strTempValue2 == "refresh") {
					if (substr_count($strRefresh,",")) {
						$arrCheck = explode(",",$strRefresh);
						foreach ($arrCheck AS $elem) {
							if ($elem == $arrData[$i][$strDataField]) {
								$this->resTemplate->setVariable($strParseVar."_SEL","selected");
							}					
						}						
					} else if ($strRefresh == $arrData[$i][$strDataField]) {
						$this->resTemplate->setVariable($strParseVar."_SEL","selected","selected");
					}
				}
				$this->resTemplate->parse($strParseGroup);
			}
			return(0);
		} else {
			return(1);
		}
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Logbuch schreiben
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.02
	//  Datum:		30.03.2005	
	//  
	//  Speichert einen übergebenen String im Logbuch
	//
	//  Übergabeparameter:	$strMessage				Mitteilung
	//	------------------	$_SESSION['username']	Benutzername
	//
	//  Returnwert:			nicht definiert
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function writeLog($strMessage) {
		// Logstring in Datenbank schreiben
		$strUserName = (isset($_SESSION['username']) && ($_SESSION['username'] != ""))	? $_SESSION['username'] : "none";
		$this->myDBClass->insertData("INSERT INTO tbl_logbook SET user='".$_SESSION['username']."',time=NOW(), entry='$strMessage'");
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Konsistenzprüfung Kontakte
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.00
	//  Datum:		10.03.2005	
	//  
	//  Überprüft die Datenkonsistenz der Kontakte
	//
	//  Übergabeparameter:	keine
	//  ------------------
	//
	//  Returnwert:			Infostring enthaltend die gefundenen Fehler
	//	Rückgabewert:		$this->strTempValue1	Liste der nicht verwendeten, aktiven Werte	
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function checkConsistContacts() {
		$this->strTempValue1  = "";
		$this->strMessage     = "";
		$this->intCounter     = 0;
		// Alle Kontakte in ein Array schreiben
		$this->strSQLStatement = "SELECT contact_name, active FROM tbl_contact";
		$intReturn = $this->getDataset("contact_name");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		if ($this->intCounter == 1) return($this->arrLanguage['admincontent']['nocontacts']);
		//
		// 	Kontakte werden verwendet in:
		// 	Kontaktgruppen
		$this->strSQLStatement = "SELECT DISTINCT members, active FROM tbl_contactgroup WHERE members!=''";
		$intReturn = $this->checkConsistency("members", "contactgroups", "contact");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		if ($this->intCounter == 1) return($this->arrLanguage['admincontent']['contactsok']);
		// Wird ein Kontakt nicht verwendet?
		foreach($this->arrDataset AS $key => $elem) {
			if (($elem['used'] == 0) && ($elem['active'] == "1")) { 
				$this->strTempValue1 .= $this->arrLanguage['admincontent']['contact']." ".$key." ".
										$this->arrLanguage['admincontent']['notused']."<br>";
			}		
		}
		if ($this->strMessage == "") $this->strMessage = $this->arrLanguage['admincontent']['contactsok'];
		return($this->strMessage);		
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Konsistenzprüfung Kontaktgruppen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.00
	//  Datum:		10.03.2005	
	//  
	//  Überprüft die Datenkonsistenz der Kontaktgruppen
	//
	//  Übergabeparameter:	keine
	//  ------------------
	//
	//  Returnwert:			Infostring enthaltend die gefundenen Fehler
	//	Rückgabewert:		$this->strTempValue1	Liste der nicht verwendeten, aktiven Werte	
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function checkConsistContactgroups() {
		$this->strTempValue1  = "";
		$this->strMessage     = "";
		$this->intCounter     = 0;
		// Alle Kontakte in ein Array schreiben
		$this->strSQLStatement = "SELECT contactgroup_name, active FROM tbl_contactgroup";
		$intReturn = $this->getDataset("contactgroup_name");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		if ($this->intCounter == 1) return($this->arrLanguage['admincontent']['nocontactgroups']);
		//
		// Kontaktgruppen verwendet in:
		// Hosts 
		$this->strSQLStatement = "SELECT DISTINCT contact_groups , active FROM tbl_host WHERE contact_groups!=''";
		$intReturn = $this->checkConsistency("contact_groups", "hosts", "contactgroup");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Services
		$this->strSQLStatement = "SELECT DISTINCT contact_groups, active FROM tbl_service WHERE contact_groups!=''";
		$intReturn = $this->checkConsistency("contact_groups", "services", "contactgroup");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Contacts
		$this->strSQLStatement = "SELECT DISTINCT contactgroups, active FROM tbl_contact WHERE contactgroups!=''";
		$intReturn = $this->checkConsistency("contactgroups", "contacts", "contactgroup");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Service Escalations
		$this->strSQLStatement = "SELECT DISTINCT contact_groups, active FROM tbl_serviceescalation WHERE contact_groups!=''";
		$intReturn = $this->checkConsistency("contact_groups", "serviceescalations", "contactgroup");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Host Escalations
		$this->strSQLStatement = "SELECT DISTINCT contact_groups, active FROM tbl_hostescalation WHERE contact_groups!=''";
		$intReturn = $this->checkConsistency("contact_groups", "hostescalations", "contactgroup");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";				
		// Resultate auswerten
		if ($this->intCounter == 1) return($this->arrLanguage['admincontent']['cgroupssok']);	
		// Wird eine Kontaktgruppe nicht verwendet?
		foreach($this->arrDataset AS $key => $elem) {
			if (($elem['used'] == 0) && ($elem['active'] == "1")) {
				$this->strTempValue1 .= $this->arrLanguage['admincontent']['contactgroup']." ".$key." ".
										$this->arrLanguage['admincontent']['notused']."<br>";
			}		
		}
		if ($this->strMessage == "") $this->strMessage = $this->arrLanguage['admincontent']['cgroupssok'];
		return($this->strMessage);		
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Konsistenzprüfung Zeitperioden
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.00
	//  Datum:		10.03.2005	
	//  
	//  Überprüft die Datenkonsistenz der Zeitperioden
	//
	//  Übergabeparameter:	keine
	//  ------------------
	//
	//  Returnwert:			Infostring enthaltend die gefundenen Fehler
	//	Rückgabewert:		$this->strTempValue1	Liste der nicht verwendeten, aktiven Werte					
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function checkConsistTimeperiods() {
		$this->strTempValue1  = "";
		$this->strMessage     = "";
		$this->intCounter     = 0;
		// Alle Kontakte in ein Array schreiben
		$this->strSQLStatement = "SELECT timeperiod_name, active FROM tbl_timeperiod";
		$intReturn = $this->getDataset("timeperiod_name");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		if ($this->intCounter == 1) return($this->arrLanguage['admincontent']['notimeperiods']);
		// Zeitperioden verwendet in:
		// Hosts
		$this->strSQLStatement = "SELECT DISTINCT CONCAT(check_period,',',notification_period) AS timeperiods, active FROM tbl_host WHERE check_period!='' OR notification_period!=''";
		$intReturn = $this->checkConsistency("timeperiods", "hosts", "timeperiod");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Services
		$this->strSQLStatement = "SELECT DISTINCT CONCAT(check_period,',',notification_period) AS timeperiods, active FROM tbl_service WHERE check_period!='' OR notification_period!=''";
		$intReturn = $this->checkConsistency("timeperiods", "services", "timeperiod");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Contacts
		$this->strSQLStatement = "SELECT DISTINCT CONCAT(host_notification_period,',',service_notification_period) AS timeperiods, active FROM tbl_contact WHERE host_notification_period!='' OR service_notification_period!=''";
		$intReturn = $this->checkConsistency("timeperiods", "contacts", "timeperiod");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Host Escalations
		$this->strSQLStatement = "SELECT DISTINCT escalation_period, active FROM tbl_hostescalation WHERE contact_groups!=''";
		$intReturn = $this->checkConsistency("escalation_period", "hostescalations", "timeperiod");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Resultate auswerten
		if ($this->intCounter == 4) return($this->arrLanguage['admincontent']['timeperiodsok']);
		// Wird eine Zeitperiode nicht verwendet?
		foreach($this->arrDataset AS $key => $elem) {
			if (($elem['used'] == 0) && ($elem['active'] == "1")) {
				$this->strTempValue1 .= $this->arrLanguage['admincontent']['timeperiod']." ".$key." ".
										$this->arrLanguage['admincontent']['notused']."<br>";
			}		
		}
		if ($this->strMessage == "") $this->strMessage = $this->arrLanguage['admincontent']['timeperiodsok'];
		return($this->strMessage);		
	}

    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Konsistenzprüfung Spezialbefehle
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.00
	//  Datum:		10.03.2005	
	//  
	//  Überprüft die Datenkonsistenz der Spezialbefehle
	//
	//  Übergabeparameter:	keine
	//  ------------------
	//
	//  Returnwert:			Infostring enthaltend die gefundenen Fehler
	//	Rückgabewert:		$this->strTempValue1	Liste der nicht verwendeten, aktiven Werte					
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function checkConsistMisccommands() {
		$this->strTempValue1  = "";
		$this->strMessage     = "";
		$this->intCounter     = 0;
		// Alle Kontakte in ein Array schreiben
		$this->strSQLStatement = "SELECT command_name, active FROM tbl_misccommand";
		$intReturn = $this->getDataset("command_name");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		if ($this->intCounter == 1) return($this->arrLanguage['admincontent']['nomisccommands']);
		// Spezialbefehle verwendet in:
		// Hosts
		$this->strSQLStatement = "SELECT DISTINCT event_handler, active FROM tbl_host WHERE event_handler!=''";
		$intReturn = $this->checkConsistency("event_handler", "hosts", "misccommand");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Services
		$this->strSQLStatement = "SELECT DISTINCT event_handler, active FROM tbl_service WHERE event_handler!=''";
		$intReturn = $this->checkConsistency("event_handler", "services", "misccommand");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Contacts
		$this->strSQLStatement = "SELECT DISTINCT CONCAT(host_notification_commands,',',service_notification_commands) AS commands, active FROM tbl_contact WHERE host_notification_commands!='' OR service_notification_commands!=''";
		$intReturn = $this->checkConsistency("commands", "contacts", "misccommand");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Hauptkonfiguration
		$strFileName = $this->arrSettings['nagios']['config']."nagios.cfg";
		if (file_exists($strFileName) && is_readable($strFileName)) {
			// Konfigurationsdatei öffnen und zeilenweise einlesen
			$resFile = fopen($strFileName,"r");
			while(!feof($resFile)) {
				$strConfLine = fgets($resFile,1024);
				$strConfLine = trim($strConfLine);
				// Kommentarzeilen und Leerzeilen übergehen
				if (substr($strConfLine,0,1) == "#") continue;
				if ($strConfLine == "") continue;
				// Linie verarbeiten (Leerzeichen reduzieren und Kommentare abschneiden)
				$arrLine    = preg_split("/[\s]+/", $strConfLine);
				$arrTemp    = explode(";",implode(" ",$arrLine));
				$strNewLine = trim($arrTemp[0]);
				$arrConfig  = explode("=",$strNewLine);
				// Wird der Wert innerhalb der Linie gefunden?
				if (isset($this->arrDataset[$arrConfig[1]]) && ($this->arrDataset[$arrConfig[1]]['active'] == 1)) {
						$this->arrDataset[$arrConfig[1]]['used']++;
				// Verwendet, aber nicht aktiv -> Warnung ausgeben
				} else if (isset($this->arrDataset[$arrConfig[1]]) && ($this->arrDataset[$arrConfig[1]]['active'] == 0)) {
					if (substr_count($this->strMessage,$arrConfig[1]." ".$this->arrLanguage['admincontent']['usedin']." nagios.cfg") == 0) 
						$this->strMessage .= $this->arrLanguage['admincontent']['misccommand']." ".$arrConfig[1]." ".
						$this->arrLanguage['admincontent']['usedin']." nagios.cfg ".
						$this->arrLanguage['admincontent']['usednotactive']."<br>";
				}			
			}
		}
		// Resultate auswerten
		if ($this->intCounter == 3) return($this->arrLanguage['admincontent']['misccommandsok']);
		// Wird ein Spezialbefehl nicht verwendet?
		foreach($this->arrDataset AS $key => $elem) {
			if (($elem['used'] == 0) && ($elem['active'] == "1")) {
				$this->strTempValue1 .= $this->arrLanguage['admincontent']['misccommand']." ".$key." ".
										$this->arrLanguage['admincontent']['notused']."<br>";
			}		
		}
		if ($this->strMessage == "") $this->strMessage = $this->arrLanguage['admincontent']['misccommandsok'];
		return($this->strMessage);		
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Konsistenzprüfung Prüfbefehle
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.00
	//  Datum:		10.03.2005	
	//  
	//  Überprüft die Datenkonsistenz der Prüfbefehle
	//
	//  Übergabeparameter:	keine
	//  ------------------
	//
	//  Returnwert:			Infostring enthaltend die gefundenen Fehler
	//	Rückgabewert:		$this->strTempValue1	Liste der nicht verwendeten, aktiven Werte					
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function checkConsistCheckcommands() {
		$this->strTempValue1  = "";
		$this->strMessage     = "";
		$this->intCounter     = 0;
		// Alle Kontakte in ein Array schreiben
		$this->strSQLStatement = "SELECT command_name, active FROM tbl_checkcommand";
		$intReturn = $this->getDataset("command_name");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		if ($this->intCounter == 1) return($this->arrLanguage['admincontent']['nocheckcommands']);
		// Prüfbefehle verwendet in:
		// Hosts
		$this->strSQLStatement = "SELECT DISTINCT SUBSTRING_INDEX(check_command, '!', 1) AS command, active FROM tbl_host WHERE check_command!=''";
		$intReturn = $this->checkConsistency("command", "hosts", "checkcommand");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Services
		$this->strSQLStatement = "SELECT DISTINCT SUBSTRING_INDEX(check_command, '!', 1) AS command, active FROM tbl_service WHERE check_command!=''";
		$intReturn = $this->checkConsistency("command", "services", "checkcommand");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";	
		// Resultate auswerten
		if ($this->intCounter == 2) return($this->arrLanguage['admincontent']['checkcommandsok']);
		// Wird ein Prüfbefehl nicht verwendet?
		foreach($this->arrDataset AS $key => $elem) {
			if (($elem['used'] == 0) && ($elem['active'] == "1")) {
				$this->strTempValue1 .= $this->arrLanguage['admincontent']['checkcommand']." ".$key." ".
										$this->arrLanguage['admincontent']['notused']."<br>";
			}		
		}
		if ($this->strMessage == "") $this->strMessage = $this->arrLanguage['admincontent']['checkcommandsok'];
		return($this->strMessage);				
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Konsistenzprüfung Hosts
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.00
	//  Datum:		10.03.2005	
	//  
	//  Überprüft die Datenkonsistenz der Hosts
	//
	//  Übergabeparameter:	keine
	//  ------------------
	//
	//  Returnwert:			Infostring enthaltend die gefundenen Fehler
	//	Rückgabewert:		$this->strTempValue1	Liste der nicht verwendeten, aktiven Werte					
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function checkConsistHosts() {
		$this->strTempValue1  = "";
		$this->strMessage     = "";
		$this->intCounter     = 0;
		// Alle Kontakte in ein Array schreiben
		$this->strSQLStatement = "SELECT host_name, active FROM tbl_host";
		$intReturn = $this->getDataset("host_name");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		if ($this->intCounter == 1) return($this->arrLanguage['admincontent']['nohosts']);
		// Hosts verwendet in:
		// Hosts
		$this->strSQLStatement = "SELECT DISTINCT parents, active FROM tbl_host WHERE parents!=''";
		$intReturn = $this->checkConsistency("parents", "hosts (Parent Host)", "host");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Services
		$this->strSQLStatement = "SELECT DISTINCT host_name, active FROM tbl_service WHERE host_name!='' AND host_name!='*'";
		$intReturn = $this->checkConsistency("host_name", "services", "host");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Hostgroups
		$this->strSQLStatement = "SELECT DISTINCT members, active FROM tbl_hostgroup WHERE members!=''";
		$intReturn = $this->checkConsistency("members", "hostgroups", "host");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";		
		// Servicegroups
		$this->strSQLStatement = "SELECT DISTINCT members, active FROM tbl_servicegroup WHERE members!=''";
		$intReturn = $this->checkConsistency("members", "servicegroups", "host",1);
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Service dependencies
		$this->strSQLStatement = "SELECT DISTINCT CONCAT(dependent_host_name ,',',host_name ) AS hosts, active FROM tbl_hostdependency WHERE dependent_host_name !='' OR host_name !=''";
		$intReturn = $this->checkConsistency("hosts", "hostdependencies", "host");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";	
		// Service escscalations
		$this->strSQLStatement = "SELECT DISTINCT host_name, active FROM tbl_hostescalation WHERE host_name!='' AND host_name!='*'";
		$intReturn = $this->checkConsistency("host_name", "hostescalations", "host");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";	
		// Host dependencies
		$this->strSQLStatement = "SELECT DISTINCT host_name, active FROM tbl_hostextinfo WHERE host_name!=''";
		$intReturn = $this->checkConsistency("host_name", "hostextinfos", "host");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Host escalations
		$this->strSQLStatement = "SELECT DISTINCT CONCAT(dependent_host_name,',',host_name ) AS hosts, active FROM tbl_servicedependency WHERE dependent_host_name !='' OR host_name !=''";
		$intReturn = $this->checkConsistency("hosts", "servicedependencies", "host");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";		
		// Host extended info
		$this->strSQLStatement = "SELECT DISTINCT host_name, active FROM tbl_serviceescalation WHERE host_name!=''";
		$intReturn = $this->checkConsistency("host_name", "serviceescalations", "host");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Service extendet info
		$this->strSQLStatement = "SELECT DISTINCT host_name, active FROM tbl_serviceextinfo WHERE host_name!=''";
		$intReturn = $this->checkConsistency("host_name", "serviceextinfo", "host");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Resultate auswerten
		if ($this->intCounter == 10) return($this->arrLanguage['admincontent']['hostsok']);
		// Wird ein Host nicht verwendet?
		foreach($this->arrDataset AS $key => $elem) {
			if (($elem['used'] == 0) && ($elem['active'] == "1")) {
				$this->strTempValue1 .= $this->arrLanguage['admincontent']['host']." ".$key." ".
										$this->arrLanguage['admincontent']['notused']."<br>";
			}		
		}
		if ($this->strMessage == "") $this->strMessage = $this->arrLanguage['admincontent']['hostsok'];
		return($this->strMessage);		
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Konsistenzprüfung Services
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.00
	//  Datum:		10.03.2005	
	//  
	//  Überprüft die Datenkonsistenz der Services
	//
	//  Übergabeparameter:	keine
	//  ------------------
	//
	//  Returnwert:			Infostring enthaltend die gefundenen Fehler					
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function checkConsistServices() {
		$this->strMessage     = "";
		$this->intCounter     = 0;
		// Alle Kontakte in ein Array schreiben
		$this->strSQLStatement = "SELECT CONCAT(host_name,'::',service_description) AS service, active FROM tbl_service";
		$intReturn = $this->getDataset("service");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		if ($this->intCounter == 1) return($this->arrLanguage['admincontent']['noservices']);
		// Kontaktgruppen verwendet in:
		// Servicegroups
		$this->strSQLStatement = "SELECT DISTINCT members, active FROM tbl_servicegroup WHERE members!=''";
		$intReturn = $this->checkConsistency("members", "servicegroups", "service",2);
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";		
		// Service dependencies
		$this->strSQLStatement = "SELECT DISTINCT CONCAT(CONCAT(dependent_host_name,'::',dependent_service_description),'@@',CONCAT(host_name,'::',service_description)) AS services, active FROM tbl_servicedependency WHERE dependent_service_description!='' OR service_description!=''";
		$intReturn = $this->checkConsistency("services", "servicedependency", "service",3);
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";		
		// Service escalation
		$this->strSQLStatement = "SELECT DISTINCT CONCAT(host_name,'::',service_description) AS services, active FROM tbl_serviceescalation WHERE service_description!='' AND service_description!='*' AND host_name != '' AND host_name != '*'";
		$intReturn = $this->checkConsistency("services", "serviceescalation", "service");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Service extendet info
		$this->strSQLStatement = "SELECT DISTINCT CONCAT(host_name,'::',service_description) AS services, active FROM tbl_serviceextinfo WHERE service_description!=''";
		$intReturn = $this->checkConsistency("services", "serviceescalation", "service");
		if ($intReturn == 1) $this->strMessage .= $this->strDBMessage."<br>";
		// Resultate auswerten
		if ($this->intCounter == 4) return($this->arrLanguage['admincontent']['servicesok']);
		if ($this->strMessage == "") $this->strMessage = $this->arrLanguage['admincontent']['servicesok'];
		return($this->strMessage);			
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Konsistenzprüfung Hostgruppen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.00
	//  Datum:		10.03.2005	
	//  
	//  Überprüft die Datenkonsistenz der Hostgruppen
	//
	//  Übergabeparameter:	keine
	//  ------------------
	//
	//  Returnwert:			Infostring enthaltend die gefundenen Fehler					
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function checkConsistHostgroups() {
		$this->strMessage     = "";
		$this->intCounter     = 0;
		// Alle Kontakte in ein Array schreiben
		$this->strSQLStatement = "SELECT hostgroup_name, active FROM tbl_hostgroup";
		$intReturn = $this->getDataset("hostgroup_name");
		if ($intReturn == 1) echo $this->strDBMessage;
		if ($this->intCounter == 1) return($this->arrLanguage['admincontent']['nohostgroups']);
		// Kontaktgruppen verwendet in:
		// Hosts
		$this->strSQLStatement = "SELECT DISTINCT hostgroups, active FROM tbl_host WHERE hostgroups!=''";
		$intReturn = $this->checkConsistency("hostgroups", "hosts", "hostgroup");
		if ($intReturn == 1) echo $this->strDBMessage;		
		// Host escalations
		$this->strSQLStatement = "SELECT DISTINCT hostgroup_name, active FROM tbl_hostescalation WHERE hostgroup_name!=''";
		$intReturn = $this->checkConsistency("hostgroup_name", "hostescalation", "hostgroup");
		if ($intReturn == 1) echo $this->strDBMessage;		
		// Resultate auswerten
		if ($this->intCounter == 2) return($this->arrLanguage['admincontent']['hostgroupsok']);
		if ($this->strMessage == "") $this->strMessage = $this->arrLanguage['admincontent']['hostgroupsok'];
		return($this->strMessage);			
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Konsistenzprüfung Servicegruppen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.00
	//  Datum:		10.03.2005	
	//  
	//  Überprüft die Datenkonsistenz der Servicegruppen
	//
	//  Übergabeparameter:	keine
	//  ------------------
	//
	//  Returnwert:			Infostring enthaltend die gefundenen Fehler					
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function checkConsistServicegroups() {
		$this->strMessage     = "";
		$this->intCounter     = 0;
		// Alle Kontakte in ein Array schreiben
		$this->strSQLStatement = "SELECT servicegroup_name, active FROM tbl_servicegroup";
		$intReturn = $this->getDataset("servicegroup_name");
		if ($intReturn == 1) echo $this->strDBMessage;
		if ($this->intCounter == 1) return($this->arrLanguage['admincontent']['noservicegroups']);
		// Servicegruppen verwendet in:
		// Services
		$this->strSQLStatement = "SELECT DISTINCT servicegroups, active FROM tbl_service WHERE servicegroups!=''";
		$intReturn = $this->checkConsistency("servicegroups", "services", "servicegroup");
		if ($intReturn == 1) echo $this->strDBMessage;				
		// Resultate auswerten
		if ($this->intCounter == 1) return($this->arrLanguage['admincontent']['servicegroupsok']);
		if ($this->strMessage == "") $this->strMessage = $this->arrLanguage['admincontent']['servicegroupsok'];
		return($this->strMessage);			
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Private Hilfsfunktionen
	//
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Hilfsfunktion: Datenarray für die Konsistenztests zusammenstellen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.02
	//  Datum:		30.03.2005	
	//  
	//  Speichert von allen Datensätzen der Abfrage einen Datenfeldnamen, Aktivinfo sowie eine
	//  Zählvariable in einem Array und übergibt dieses an eine Klassenvariable
	//
	//  Übergabeparameter:	$strDatafield			Datenfeldname
	//  ------------------  $this->strSQLStatement	SQL Abfrage
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//	Rückgabewert:		$this->arrDataset		Formattiertes Datenarray
	//						$this->strDBMessage		Datenbankfehlermeldungen					
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function getDataset($strDatafield) {
		$this->arrDataset	= "";
		$this->strDBMessage = "";
		// Datenbankabfrage ausführen
		$booReturn = $this->myDBClass->getDataArray($this->strSQLStatement,$arrData,$intDataCount);
		if ($booReturn == false) {
			$this->strDBMessage = $this->myDBClass->strDBError;
			return(1);
		// Array abfüllen		
		} else if ($intDataCount != 0) {
			for ($i=0;$i<$intDataCount;$i++) {
			    $arrTemp[$arrData[$i][$strDatafield]] = 
					array('active' => $arrData[$i]['active'], 'used' => 0);
			}
			$this->arrDataset = $arrTemp;
			return(0);
		} else {
			$this->intCounter++;
			return(0);
		}	
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Hilfsfunktion: Konsistenztest
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.02
	//  Datum:		30.03.2005	
	//  
	//  Vergleicht die Werte eines Datenarrays mit den Werten einer Abfrage und stellt 
	// 	Differenzen fest.
	//
	//  Übergabeparameter:	$strDatafield			Datenfeldname
	//  ------------------  $strTableInfo			Tabelleninformation
	//						$strLangKey				Sprachschlüssel
	//						$intSpecial				Modusvariable
	//						$this->strSQLStatement	SQL Statement
	//						$this->arrDataset		Datensatzarray aus getDataset()					
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//	Rückgabewert:		$this->arrDataset		Formattiertes Datenarray
	//						$this->strDBMessage		Datenbankfehlermeldungen
	//						$this->strMessage		Konsistenzmeldungen					
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function checkConsistency($strDatafield, $strTableInfo, $strLangKey, $intSpecial=0) {
		// Datenbankabfrage ausführen
		$booReturn = $this->myDBClass->getDataArray($this->strSQLStatement,$arrData,$intDataCount);
		if ($booReturn == false) {
			$this->strDBMessage = $this->myDBClass->strDBError;
			return(1);
		// Array abfüllen		
		} else if ($intDataCount != 0) {
			for ($i=0;$i<$intDataCount;$i++) {
			    $arrTemp = explode(",",$arrData[$i][$strDatafield]);
				// Servicegruppenfelder haben ein spezielles Format (host,service,host,service...)
				// Fall 1 -> Hostname vergleichen
				if ($intSpecial == 1) {
					for($y=0;$y<count($arrTemp);$y++) {
						// Hostnamen in neues Array speichern
						if ($y%2 == 0) $arrTempNew[] = $arrTemp[$y];  
					}
					$arrTemp = $arrTempNew;
				// Fall 2 -> Servicenamen vergleichen
				} else if ($intSpecial == 2) {
					for($y=0;$y<count($arrTemp);$y++) {
						// Daten in der Form "host::service" in ein neues Array speichern 
						if ($y%2 == 0) {
						    if (isset($strTemp) && $strTemp != "") $arrTempNew[] = $strTemp;
							$strTemp = $arrTemp[$y]."::";
						} else {
							$strTemp .= $arrTemp[$y];
						}
					}
					if (isset($strTemp) && $strTemp != "") $arrTempNew[] = $strTemp;
					$arrTemp = $arrTempNew;				
				} else if ($intSpecial == 3) {
					$arrTemp  = explode("@@",$arrData[$i][$strDatafield]);
				}
				// Array umformatieren
				foreach($arrTemp AS $elem) {
					if (substr_count($elem,"::") != 0) {
						$arrWork = explode("::",$elem);
						if (substr_count($arrWork[1],",") != 0) {
							$arrSubWork = explode(",",$arrWork[1]);
							foreach($arrSubWork AS $subelem) {
								$arrTempNew[] = $arrWork[0]."::".$subelem;
							}
						} else {
							$arrTempNew[] = $arrWork[0]."::".$arrWork[1];
						}
					} else {
						$arrTempNew[] = $elem;
					}
				}
				$arrTemp = $arrTempNew;
				// Werte vergleichen und Resultat in Klassenvariable schreiben
				foreach ($arrTemp AS $key => $elem) {
					// Verwendet und aktiv -> Zähler erhöhen
					if (isset($this->arrDataset[$elem]) && ($arrData[$i]['active'] == 1) && ($this->arrDataset[$elem]['active'] == 1)) {
						$this->arrDataset[$elem]['used']++;
					// Verwendet, aber nicht aktiv -> Warnung ausgeben
					} else if (isset($this->arrDataset[$elem]) && ($arrData[$i]['active'] == 1) &&  ($this->arrDataset[$elem]['active'] == 0) && ($elem != "")) {
						if (substr_count($this->strMessage,$elem." ".$this->arrLanguage['admincontent']['usedin']." ".$strTableInfo) == 0) 
							$this->strMessage .= $this->arrLanguage['admincontent'][$strLangKey]." ".$elem." ".
							$this->arrLanguage['admincontent']['usedin']." ".$strTableInfo." ".
							$this->arrLanguage['admincontent']['usednotactive']."<br>";
					// Verwendet aber nicht definiert -> Warnung ausgeben
					} else if (($arrData[$i]['active'] == 1) && ($elem != "")) {
						if (substr_count($this->strMessage,$elem." ".$this->arrLanguage['admincontent']['usedin']." ".$strTableInfo) == 0) 
							$this->strMessage .= $this->arrLanguage['admincontent'][$strLangKey]." ".$elem." ".
							$this->arrLanguage['admincontent']['usedin']." ".$strTableInfo." ".
							$this->arrLanguage['admincontent']['usednotexist']."<br>";
					}
				}
			}
		} else {
			$this->intCounter++;
		}
		return(0);
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Hilfsfunktion: Tabelle importieren
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.02
	//  Datum:		30.03.2005	
	//  
	//  Importiert eine Konfigurationsdatei in die passende Datentabelle.
	//
	//  Übergabeparameter:	$strBlockKey			Konfigurationsschlüssel (define)
	//  ------------------  $arrImportData			Eingelesene Daten eines Blockes
	//						$intOverwrite			Daten in Tabelle überschreiben 1=Ja, 0=Nein
	//						$strFileName			Name der Konfigurationsdatei					
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg / 2 Eintrag existiert schon
	//	Rückgabewert:		$this->strDBMessage		Datenbankfehlermeldungen					
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function importTable($strBlockKey,$arrImportData,$intOverwrite,$strFileName) {
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
		// Existiert der Eintrag schon?
		$intExists = 0;
		if (($strKeyField != "") && isset($arrImportData[$strKeyField])) {
			$intExists = $this->myDBClass->getFieldData("SELECT id FROM $strTable WHERE $strKeyField='".$arrImportData[$strKeyField]['value']."'");	
			if ($intExists == false) $intExists = 0;
		}
		if (($intExists != 0) && ($intOverwrite == 0)) {
			$this->strMessage .= $this->arrLanguage['db']['entry'].$strKeyField."::".$arrImportData[$strKeyField]['value'].$this->arrLanguage['db']['inside'].$strTable.$this->arrLanguage['db']['exists']."<br>";
			return(2);
		}
		// Generische Einträge übergehen
		if (isset($arrImportData['name'])) return(0);
		// Eintrag aktiv
		if (isset($arrImportData['register']) && ($arrImportData['register']['value'] == 0)) {
			$intActive = 0;
		} else {
			$intActive = 1;
		}
		if ($intExists != 0)  {
			// DB Eintrag updaten
			$strSQL1 = "UPDATE $strTable SET ";
			$strSQL2 = " active='$intActive', last_modified=NOW() WHERE id=$intExists";
		} else {
			// DB Eintrag einfügen
			$strSQL1 = "INSERT INTO $strTable SET ";
			$strSQL2 = " active='$intActive', last_modified=NOW()";
		}
		$i = 0;
		foreach ($arrImportData AS $elem) {
			if (($elem['key'] != "register") && ($elem['key'] != "use")) {
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
			$arrTemp = explode(".",strrev(basename($strFileName)),2);
			$strSQL1 .= "config_name='".strrev($arrTemp[1])."', ";
		}
		if (($strTable == "tbl_serviceescalation") || ($strTable == "tbl_servicedependency") || 
			($strTable == "tbl_hostescalation") || ($strTable == "tbl_hostedependency")) {
			$strSQL1 .= "config_name='import_".microtime()."', ";
		}
		// Datenbank updaten
		$booResult = $this->myDBClass->insertData($strSQL1.$strSQL2);
		if ($booResult != true) {
			$this->strDBMessage = $this->myDBClass->strDBError;
			if ($strKeyField != "") $this->strMessage .= $this->arrLanguage['db']['entry'].$strKeyField."::".$arrImportData[$strKeyField]['value'].$this->arrLanguage['db']['inside'].$strTable.$this->arrLanguage['db']['insertnak']."<br>";
			if ($strKeyField == "") $this->strMessage .= $this->arrLanguage['db']['entry'].$strTemp1."::".$strTemp2.$this->arrLanguage['db']['inside'].$strTable.$strTable.$this->arrLanguage['db']['insertnak']."<br>";
			return(1);
		} else {
			if ($strKeyField != "") $this->strMessage .= $this->arrLanguage['db']['entry'].$strKeyField."::".$arrImportData[$strKeyField]['value']." in ".$strTable.$this->arrLanguage['db']['insertok']."<br>";
			if ($strKeyField == "") $this->strMessage .= $this->arrLanguage['db']['entry'].$strTemp1."::".$strTemp2.$this->arrLanguage['db']['inside'].$strTable.$this->arrLanguage['db']['insertok']."<br>";
			return(0);
		}
	}
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Template integrieren
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	1.00
	//  Datum:		29.03.2005	
	//  
	//  Integriert die Daten eines bestimmten Templates in das Importdatenarrays
	//
	//  Übergabeparameter:	$strFileName	Importdateiname
	//	------------------  $strTemplate	Name des Template	
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