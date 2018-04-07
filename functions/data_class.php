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
// Zweck:	Datenmanipulationsklassen
// Datei:	functions/data_class.php
// Version: 2.0.2 (Internal)
// SV:      $Id: data_class.php 72 2008-04-03 07:01:46Z rouven $
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Klasse: Datenmanipulationsfunktionen
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Behandelt sämtliche Funktionen, die zur Manipulation der Konfigurationsdaten innerhalb der 
// Datenbank notwendig sind
//
// Version 2.0.2 (Internal)
// Datum   12.03.2007 wim
//
// Name: nagdata
//
// Klassenvariabeln:
// -----------------
// $arrSettings:	Mehrdimensionales Array mit den globalen Konfigurationseinstellungen
// $arrLanguage:	Mehrdimensionales Array mit den globalen Sprachstrings
// $myDBClass:		Datenbank Klassenobjekt
// $myVisClass:		NagiosQL Visualisierungsklasse
// $strDBMessage	Mitteilungen des Datenbankservers
//
// Externe Funktionen
// ------------------
// keine
// 	
///////////////////////////////////////////////////////////////////////////////////////////////
class nagdata {
    // Klassenvariabeln deklarieren
    var $arrSettings;					// Wird im Klassenkonstruktor gefüllt
	var $arrLanguage;					// Wird in der Datei prepend_adm.php gefüllt
	var $myDBClass;						// Wird in der Datei prepend_adm.php definiert
	var $myVisClass;					// Wird in der Datei prepend_adm.php definiert
	var $strDBMessage    = "";			// Wird Klassenintern verwendet
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Klassenkonstruktor
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Tätigkeiten bei Klasseninitialisierung
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function nagdata() {
		// Globale Einstellungen einlesen
		$this->arrSettings = $_SESSION['SETS'];
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Auswahlfeld in Kommastring berfhren
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Schreibt die per Array übergebenen Einzelwerte in einen String hintereinander 
	//  mit Komma getrennt.
	//
	//  ÜÜbergabeparameter:	$arrData	Datenarray
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
	//  Funktion: Daten in die Datenbank schreiben
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Sendet einen übergebenen SQL String an den Datenbankserver und wertet die Rückgabe
	//  des Servers aus.
	//
	//  ÜÜbergabeparameter:	$strSQL					SQL Befehl
	//
	//  Rückgabewert:		$intDataID				ID des letzten, eingefgten Datensatzes
	//						$this->strDBMessage		Erfolg-/Fehlermeldung
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dataInsert($strSQL,&$intDataID) {
		// Daten an Datenbankserver senden
		$booReturn = $this->myDBClass->insertData($strSQL);
		$intDataID = $this->myDBClass->intLastId;
		// Konnte der Datensatz erfolgreich eingefgt werden?
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
	//  Funktion: Relationen in die Datenbank schreiben
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Trägt die notwendigen Relationen für eine 1:n (Optional 1:n:n) Beziehung in die 
	//  Relationstabelle ein
	//
	//  ÜÜbergabeparameter:	$intTabA		Tabellen-ID der Tabelle A
	//						$intTabB		Tabellen-ID der Tabelle B (Optional: B1)
	//						$intTabA_id		Datensatz-ID der Tabelle A
	//						$strTabA_field	Name des Tabellenfeldes der Tabelle A
	//						$arrTabB_id		Array aller Datensatz-IDs der Tabelle B
	//						$intTabB2		Optional: Tabellen-ID der Tabelle B2
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//						Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dataInsertRelation($intTabA,$intTabB,$intTabA_id,$strTabA_field,$arrTabB_id,$intTabB2=0) {
		// Für jede Arrayposition einen Eintrag in die Relationstabelle vornehmen
		foreach($arrTabB_id AS $elem) {
			// Leere Werte ausblenden
			if (($elem == 0) && ($intTabB2 == 0)) continue;
			// Normale oder spezielle Relation?
			if ($intTabB2 == 0) {
				// SQL Statement definieren
				$strSQL = "INSERT INTO tbl_relation SET tbl_A=$intTabA, tbl_B=$intTabB, 
						   tbl_A_id=$intTabA_id, tbl_A_field='$strTabA_field', tbl_B_id=$elem";
			} else {
				// Bei der speziellen Relation werden die IDs in der Form B1_ID.B2_ID bergeben
				$arrValues = explode(".",$elem);
				// Falls Übergabewert ein "*" ist, eine 0 als B1_ID eintragen
				if ($arrValues[0] == "*") $arrValues[0] = 0;
				// SQL Statement definieren
				$strSQL = "INSERT INTO tbl_relation_special SET tbl_A=$intTabA, tbl_B1=$intTabB, tbl_B2=$intTabB2, 
						   tbl_A_id=$intTabA_id, tbl_A_field='$strTabA_field', tbl_B1_id=".$arrValues[0].", tbl_B2_id=".$arrValues[1];
			}
			// Daten an Datenbankserver senden
			$intReturn = $this->dataInsert($strSQL,$intDataID);
			if ($intReturn != 0) return(1);
		}
		return(0);
	}

    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Relationen in der Datenbank aktualisieren
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Ändert die Relationen für eine 1:n (Optonal 1:n:n) Beziehung innerhalb der Relations-
	//  tabelle
	//
	//  ÜÜbergabeparameter:	$intTabA		Tabellen-ID der Tabelle A
	//						$intTabB		Tabellen-ID der Tabelle B (Optional: B1)
	//						$intTabA_id		Datensatz-ID der Tabelle A
	//						$strTabA_field	Name des Tabellenfeldes der Tabelle A
	//						$arrTabB_id		Array aller Datensatz-IDs der Tabelle B
	//						$intTabB2		Optional: Tabellen-ID der Tabelle B2
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//						Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dataUpdateRelation($intTabA,$intTabB,$intTabA_id,$strTabA_field,$arrTabB_id,$intTabB2=0) {
		// Alte Relationen löschen
		$intReturn1 = $this->dataDeleteRelation($intTabA,$intTabB,$intTabA_id,$strTabA_field,$intTabB2);
		if ($intReturn1 != 0) return(1);
		// Neue Relationen eintragen
		$intReturn2 = $this->dataInsertRelation($intTabA,$intTabB,$intTabA_id,$strTabA_field,$arrTabB_id,$intTabB2);
		if ($intReturn2 != 0) return(1);
		return(0);
	}

    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Relationen in der Datenbank lschen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Löscht eine Relation aus der Relationstabelle
	//
	//  ÜÜbergabeparameter:	$intTabA		Tabellen-ID der Tabelle A
	//						$intTabB		Tabellen-ID der Tabelle B (Optional: B1)
	//						$intTabA_id		Datensatz-ID der Tabelle A
	//						$strTabA_field	Name des Tabellenfeldes der Tabelle A
	//						$intTabB2		Optional: Tabellen-ID der Tabelle B2
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//  				 	Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dataDeleteRelation($intTabA,$intTabB,$intTabA_id,$strTabA_field,$intTabB2=0) {		
		// Normale oder spezielle Relation?
		if ($intTabB2 == 0) {
			// SQL Statement definieren
			$strSQL = "DELETE FROM tbl_relation WHERE tbl_A=$intTabA AND tbl_B=$intTabB
					   AND tbl_A_id=$intTabA_id AND tbl_A_field='$strTabA_field'";
		} else {
			// SQL Statement definieren
			$strSQL = "DELETE FROM tbl_relation_special WHERE tbl_A=$intTabA AND tbl_B1=$intTabB
					   AND tbl_B2=$intTabB2 AND tbl_A_id=$intTabA_id AND tbl_A_field='$strTabA_field'";		
		}
		// Daten an Datenbankserver senden
		$intReturn = $this->dataInsert($strSQL,$intDataID);
		if ($intReturn != 0) return(1);
		return(0);
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Relationen in der Datenbank finden
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Sucht eine Relation in der Relationstabelle
	//
	//  ÜÜbergabeparameter:	$intTabA		Tabellen-ID der Tabelle A
	//						$intTabB		Tabellen-ID der Tabelle B (Optional: B1)
	//						$intTabA_id		Datensatz-ID der Tabelle A
	//						$strTabA_field	Name des Tabellenfeldes der Tabelle A
	//						$arrTabB_id		Array aller Datensatz-IDs der Tabelle B
	//
	//  Returnwert:			0 falls keine Relation / Anzahl falls Relationen gefunden
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function findRelation($intTabA,$intTabB,$intTabA_id,$strTabA_field,$intTabB_id) {
		// SQL Statement definieren
		$strSQL = "SELECT * FROM tbl_relation WHERE tbl_A=$intTabA AND tbl_B=$intTabB AND 
					   tbl_A_id=$intTabA_id AND tbl_A_field='$strTabA_field' AND tbl_B_id=$intTabB_id";
		// Daten an Datenbankserver senden
		$intCount = $this->myDBClass->countRows($strSQL);
		return($intCount);
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Assoziierte Hosts zu einer, mehreren oder allen Hostgruppen finden
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Sucht alle assoziierten Hosts zu den mitgelieferten Hostgruppen
	//
	//  ÜÜbergabeparameter:	$arrHostgroupId	Array mit allen gewnschten Hostgruppen
	//						$arrResult		Resultatearray
	//
	//  Returnwert:			0 bei Erfolg / 1 falls Fehler aufgetreten
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function findHostsByHostgroup($arrHostgroupId,&$arrResult) {
		// (Es ist nicht möglich, einer Hostgruppe via "*" alle Hosts zuzuordnen, es können 
		// aber alle Hostgruppen via "*" ausgewählt werden)
		if (in_array("*",$arrHostgroupId)) {
			// SQL Statement definieren
			$strSQL = "SELECT DISTINCT tbl_host.id, tbl_host.host_name 
					   FROM tbl_hostgroup
					   LEFT JOIN tbl_relation ON tbl_hostgroup.id = tbl_relation.tbl_A_id
					   LEFT JOIN tbl_host ON tbl_relation.tbl_B_id = tbl_host.id 
					   WHERE tbl_relation.tbl_A = ".$this->tableID("tbl_hostgroup")." AND
					         tbl_relation.tbl_B = ".$this->tableID("tbl_host")." AND
							 tbl_relation.tbl_A_field = 'members'
					   ORDER BY tbl_host.host_name";	
			$booReturn = $this->myDBClass->getDataArray($strSQL,$arrResult,$intDataCount);
			if ($booReturn == true) {return(0);} else {return(1);}
		} else {
			// IN Statement der WHERE Bedingung zusammenstellen
			$strWhere = "";
			foreach($arrHostgroupId AS $elem) {
				$strWhere .= "'".$elem['id']."',";
			}
			$strWhere = substr($strWhere,0,-1);
			// SQL Statement definieren
			$strSQL = "SELECT DISTINCT tbl_host.id, tbl_host.host_name 
					   FROM tbl_hostgroup
					   LEFT JOIN tbl_relation ON tbl_hostgroup.id = tbl_relation.tbl_A_id
					   LEFT JOIN tbl_host ON tbl_relation.tbl_B_id = tbl_host.id 
					   WHERE tbl_relation.tbl_A = ".$this->tableID("tbl_hostgroup")." AND
					         tbl_relation.tbl_B = ".$this->tableID("tbl_host")." AND
							 tbl_relation.tbl_A_field = 'members' AND tbl_hostgroup.id IN ($strWhere)
					   ORDER BY tbl_host.host_name";	
			$booReturn = $this->myDBClass->getDataArray($strSQL,$arrResult,$intDataCount);
			if ($booReturn == true) {return(0);} else {return(1);}		
		}
	}

    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Daten aus Datenbank lschen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Löscht einen Datensatz oder mehrere Datensätze aus einer Datentabelle. Wahlweise kann 
	//  eine einzelne Datensatz ID angegeben werden oder die Werte der mittels $_POST['chbId_n'] 
	//	übergebenen Parameter ausgewertet werden, wobei "n" der Datensatz ID entsprechen muss.
	//
	//  ÜÜbergabeparameter:	$strTableName	Tabellenname
	//						$_POST[]		Formularausgabe (Checkboxen "chbId_n" n=DBId)
	//						$intDataId		Einzelne Datensatz ID, welche zu lschen ist
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//  					Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dataDeleteSimple($strTableName,$intDataId = 0) {
		// Variabeln deklarieren
		$intError=0; $intNumber=0; $intMustData=0; $intFileDel=0;
		$this->strDBMessage = "";
		// Alle Datensätze der angegebenen Tabelle holen
		$booReturn = $this->myDBClass->getDataArray("SELECT id FROM ".$strTableName,$arrData,$intDataCount);
		if ($booReturn == false) {
			// Datenbankabfrage fehlgeschlagen
			$this->strDBMessage = $this->arrLanguage['db']['dberror']."<br>".$this->myDBClass->strDBError."<br>";	
			return(1);
		} else if ($intDataCount == 0) {
			// Keine Datensätze zurückgeliefert
			$this->strDBMessage = $this->arrLanguage['db']['nodata_del']."<br>";	
			return(0);
		} else {
			// Datensätze zurückgeliefert
			for ($i=0;$i<$intDataCount;$i++) {		
				// Formularübergabeparameter zusammenstellen
				$strChbName = "chbId_".$arrData[$i]['id'];
				// Falls ein $_POST Parameter mit diesem Namen oder explizit diese Id übergeben wurde
				if ((isset($_POST[$strChbName]) && ($intDataId == 0)) || ($intDataId == $arrData[$i]['id'])) {
					// Prüfen, ob dieser Datenbankeintrag noch in einer anderen Tabelle als MUSS Feld verwendet wird
					$intReturn = $this->checkMustdata($strTableName,$arrData[$i]['id'],$arrInfo);
					if ($intReturn == 1) {
						// Mustdaten in einem Array zwischenspeichern
						foreach ($arrInfo AS $elem) $arrMustData[] = $elem;
						$intMustData = 1;
					} else {
						// Ausnahmeregel für Usertabelle und Adminaccount
						if ($strTableName == "tbl_user") {
							$intAdminId = $this->myDBClass->getFieldData("SELECT id FROM tbl_user WHERE username='Admin'");
							if (($intAdminId != false) && ($intAdminId == $arrData[$i]['id'])) {
								$this->strDBMessage .= "<span class=\"dbmessage\">".$this->arrLanguage['db']['admindelete']."<br></span>";
								continue;
							}
						}
						// Konfigurationsname/Hostname ermitteln
						if ($strTableName == "tbl_service") {
							$strConfigName = $this->myDBClass->getFieldData("SELECT config_name FROM $strTableName WHERE id=".$arrData[$i]['id']);
						} else if ($strTableName == "tbl_host") {
							$strHostName   = $this->myDBClass->getFieldData("SELECT host_name FROM $strTableName WHERE id=".$arrData[$i]['id']);
						}
						// Datenbankeintrag löschen
						$intCheck  = 0;
						$booReturn = $this->myDBClass->insertData("DELETE FROM $strTableName WHERE id=".$arrData[$i]['id']);
						if ($booReturn == false) $intCheck++;
						// Eventuell vorhandene Relationen löschen
						if (($this->tableRelations($strTableName,$arrRelations) != 0) && ($intCheck == 0)){
							$intTabA    = $this->tableID($strTableName);
							$intTabA_id = $arrData[$i]['id'];
							// Alle normalen Relationen aus der DB löschen (A-Seite)
							$strSQL = "DELETE FROM tbl_relation WHERE tbl_A=$intTabA AND tbl_A_id=$intTabA_id";
							$booReturn = $this->myDBClass->insertData($strSQL);
							if ($booReturn == false) $intCheck++;
							// Alle normalen Relationen aus der DB löschen (B-Seite)
							$strSQL = "DELETE FROM tbl_relation WHERE tbl_B=$intTabA AND tbl_B_id=$intTabA_id";
							$booReturn = $this->myDBClass->insertData($strSQL);
							if ($booReturn == false) $intCheck++;
							// Alle speziellen Relationen aus der DB löschen (A-Seite)
							$strSQL = "DELETE FROM tbl_relation_special WHERE tbl_A=$intTabA AND tbl_A_id=$intTabA_id";
							$booReturn = $this->myDBClass->insertData($strSQL);
							if ($booReturn == false) $intCheck++;
							// Alle speziellen Relationen aus der DB löschen (B-Seite)
							$strSQL = "DELETE FROM tbl_relation_special WHERE (tbl_B1=$intTabA AND tbl_B1_id=$intTabA_id) OR (tbl_B2=$intTabA AND tbl_B2_id=$intTabA_id)";
							$booReturn = $this->myDBClass->insertData($strSQL);
							if ($booReturn == false) $intCheck++;
						}			
						// Fehlerbehandlung
						if ($intCheck != 0) {
							// Misserfolg
							$intError++; 
							$this->writeLog($this->arrLanguage['logbook']['deletedatafail']." ".$strTableName." [".$arrData[$i]['id']."]");
						} else {
							// Erfolg
							$this->writeLog($this->arrLanguage['logbook']['deletedata']." ".$strTableName." [".$arrData[$i]['id']."]");
						}
						// Falls Service betroffen - evtl. Konfigurationsdatei löschen
						if (isset($strConfigName) && ($strConfigName != "") && ($intCheck == 0)) {
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
						} else if (isset($strHostName) && ($strHostName != "") && ($intCheck == 0)) {
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
						$intNumber++;
					}
				}
			}
		}
		// Fehlerbehandlung
		if ($intNumber > 0) {
			// Kein Fehler -> Löschen war erfolgreich
			if ($intError == 0) {
				// Kein Mussdaten -> alles Ok
				if ($intMustData == 0) {
					$this->strDBMessage .= $this->arrLanguage['db']['success_del'];
					if ($intFileDel != 0) $this->strDBMessage .= "<br>".$this->arrLanguage['file']['success_del'];
					return(0);
				} else {
					$this->strDBMessage .= $this->arrLanguage['db']['mustdata_del'];
					$intCount = 0;
					// Mussdaten angeben
					foreach ($arrMustData AS $elem) {
						if ($intCount < 10) {
							$this->strDBMessage .= "<br>".$this->arrLanguage['db']['entry']." \"".$elem['entry']."\" ".
														  $this->arrLanguage['db']['used_in_table']." \"".$elem['target_table']."\" ".
														  $this->arrLanguage['db']['in_entry']." \"".$elem['target_name']."\"";
						}
						$intCount++;
					}
					if ($intCount >= 10) {
						$intCount = $intCount-10;
						$this->strDBMessage .= "<br>".$this->arrLanguage['db']['entry']." \"".$elem['entry']."\" ".
													 $this->arrLanguage['db']['usedin']." ".$intCount." ".
													 $this->arrLanguage['db']['othertables'];
					}
					return(1);
				}
			// Fehler ist aufgetreten -> Löschen war nicht erfolgreich
			} else {
				$this->strDBMessage .= $this->arrLanguage['db']['failed_del'];
				return(1);
			}
		}
		// Falls keine Daten gelöscht werden konnten
		if ($intMustData != 0) {
			$this->strDBMessage .= $this->arrLanguage['db']['mustdata_del'];
			$intCount = 0;
			// Mussdaten angeben
			foreach ($arrMustData AS $elem) {
				if ($intCount < 10) {
					$this->strDBMessage .= "<br>".$this->arrLanguage['db']['entry']." \"".$elem['entry']."\" ".
												  $this->arrLanguage['db']['used_in_table']." \"".$elem['target_table']."\" ".
												  $this->arrLanguage['db']['in_entry']." \"".$elem['target_name']."\"";
				}
				$intCount++;
			}
			if ($intCount >= 10) {
				$intCount = $intCount-10;
				$this->strDBMessage .= "<br>".$this->arrLanguage['db']['entry']." \"".$elem['entry']."\" ".
											  $this->arrLanguage['db']['usedin']." ".$intCount." ".
											  $this->arrLanguage['db']['othertables'];
			}	
			return(1);
		}	
		return(0);	
	}

    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Datensätze kopieren
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Kopiert einen oder mehrere Datensätze in einer Datentabelle. Wahlweise kann eine 
	//  einzelne Datensatz ID angegeben werden oder die Werte der mittels $_POST['chbId_n'] 
	//	übergebenen Parameter ausgewertet werden, wobei "n" der Datensatz ID entsprechen muss.
	//
	//  Übergabeparameter:	$strTableName	Tabellenname
	//	 					$_POST[]		Formularausgabe (Checkboxen "chbId_n" n=DBId)
	//						$intDataId		Einzelne Datensatz ID, welche zu lschen ist
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//  					Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dataCopySimple($strTableName,$intDataId = 0) {
		// Schlüsselfeld entsprechend dem Tabellennamen festlegen
		switch($strTableName) {
			case "tbl_timeperiod":			$strKeyField = "timeperiod_name"; 		break;
			case "tbl_misccommand":			$strKeyField = "command_name"; 			break;
			case "tbl_checkcommand":		$strKeyField = "command_name"; 			break;
			case "tbl_contact":				$strKeyField = "contact_name"; 			break;
			case "tbl_contactgroup":		$strKeyField = "contactgroup_name"; 	break;
			case "tbl_hostgroup":			$strKeyField = "hostgroup_name"; 		break;
			case "tbl_servicegroup":		$strKeyField = "servicegroup_name"; 	break;
			case "tbl_host":				$strKeyField = "host_name"; 			break;
			case "tbl_service":				$strKeyField = "service_description"; 	break;
			case "tbl_servicedependency":	$strKeyField = "config_name"; 			break;
			case "tbl_hostdependency":		$strKeyField = "config_name"; 			break;
			case "tbl_serviceescalation":	$strKeyField = "config_name"; 			break;							
			case "tbl_hostescalation":		$strKeyField = "config_name"; 			break;
			case "tbl_hostextinfo":			$strKeyField = "notes"; 				break;
			case "tbl_serviceextinfo":		$strKeyField = "service_description"; 	break;
			case "tbl_user":				$strKeyField = "username"; 				break;							
		}
		// Variabeln deklarieren
		$intError=0; $intNumber=0;
		// Alle Datensatz-IDs der Zieltabelle abfragen
		$booReturn = $this->myDBClass->getDataArray("SELECT id FROM $strTableName ORDER BY id",$arrData,$intDataCount);
		if ($booReturn == false) {
			$this->strDBMessage = $this->arrLanguage['db']['dberror']."<br>".$this->myDBClass->strDBError."<br>";
			return(1);	
		} else if ($intDataCount != 0) {
			// Datensätze zurückgeliefert
			for ($i=0;$i<$intDataCount;$i++) {
				// Formularübergabeparameter zusammenstellen
				$strChbName = "chbId_".$arrData[$i]['id'];
				// Falls ein $_POST Parameter mit diesem Namen oder explizit diese Id bergeben wurde
				if ((isset($_POST[$strChbName]) && ($intDataId == 0)) || ($intDataId == $arrData[$i]['id'])) {
					// Daten des entsprechenden Eintrages holen
					$this->myDBClass->getSingleDataset("SELECT * FROM $strTableName WHERE id=".$arrData[$i]['id'],$arrData[$i]);
					// Namenszusatz erstellen
					for ($y=1;$y<=$intDataCount;$y++) {
						$strNewName = $arrData[$i][$strKeyField]." ($y)";
						$booReturn = $this->myDBClass->getFieldData("SELECT id FROM $strTableName WHERE $strKeyField='$strNewName'");
						// Falls den neue Name einmalig ist, abbrechen
						if ($booReturn == false) break;
					}
					// Entsprechend dem Tabellennamen den Datenbank-Insertbefehl zusammenstellen
					$strSQLInsert = "INSERT INTO $strTableName SET $strKeyField='$strNewName',";
					foreach($arrData[$i] AS $key => $value) {
						if (($key != $strKeyField) && ($key != "active") && ($key != "last_modified") && ($key != "id")) {
							// NULL Werte nach Datenfeld setzen
							if (($key == "normal_check_interval") 	&& ($value == "")) 	$value="NULL";
							if (($key == "retry_check_interval") 	&& ($value == "")) 	$value="NULL";
							if (($key == "max_check_attempts") 		&& ($value == ""))	$value="NULL";
							if (($key == "low_flap_threshold") 		&& ($value == ""))	$value="NULL";							
							if (($key == "high_flap_threshold") 	&& ($value == ""))	$value="NULL";
							if (($key == "freshness_threshold") 	&& ($value == "")) 	$value="NULL";
							if (($key == "notification_interval") 	&& ($value == "")) 	$value="NULL";
							if (($key == "check_interval") 			&& ($value == "")) 	$value="NULL";
							if (($key == "access_rights") 			&& ($value == "")) 	$value="NULL";
							// NULL Werte nach Tabellenname setzen
							if (($strTableName == "tbl_hostextinfo") && ($key == "host_name")) 		$value="NULL";
							if (($strTableName == "tbl_serviceextinfo") && ($key == "host_name")) 	$value="NULL";
							// Passwort für kopierten Benutzer nicht bernehmen
							if (($strTableName == "tbl_user") && ($key == "password"))  			$value="xxxxxxx";
							// Sofern der Datenwert nicht "NULL" ist, den Datenwert in Hochkommas einschliessen
							if ($value != "NULL") {							
								$strSQLInsert .= $key."='".addslashes($value)."',";
							} else {
								$strSQLInsert .= $key."=".$value.",";							
							}
						}
					}
					$strSQLInsert .= "active='0', last_modified=NOW()";
					// Kopie in die Datenbank eintragen
					$intCheck   = 0;
					$booReturn  = $this->myDBClass->insertData($strSQLInsert);
					$intTabA_id = $this->myDBClass->intLastId;
					if ($booReturn == false) $intCheck++;
					// Eventuell vorhandene Relationen kopieren
					if (($this->tableRelations($strTableName,$arrRelations) != 0) && ($intCheck == 0)){
						$intTabA    = $this->tableID($strTableName);
						foreach ($arrRelations AS $elem) {
							// Ist Feld nicht auf "None" oder "*" gesetzt?
							if ($arrData[$i][$elem['fieldName']] == 1) {
								if ($elem['type'] != 3) {
									// Alle normalen Relationen aus der DB herausholen
									$strSQL = "SELECT tbl_B_id FROM tbl_relation WHERE tbl_A=$intTabA AND tbl_B=".$this->tableID($elem['tableName'])." 
											   AND tbl_A_id=".$arrData[$i]['id']." AND tbl_A_field='".$elem['fieldName']."'";
									$booReturn = $this->myDBClass->getDataArray($strSQL,$arrRelData,$intRelDataCount);
									if ($intRelDataCount != 0) {
										$arrDataInsert = "";
										for ($y=0;$y<$intRelDataCount;$y++) {
											$arrDataInsert[] = $arrRelData[$y]['tbl_B_id'];
										}
										$this->dataInsertRelation($intTabA,$this->tableID($elem['tableName']),$intTabA_id,$elem['fieldName'],$arrDataInsert);
									}
								} else {
									// Alle speziellen Relationen aus der DB herausholen
									$strSQL = "SELECT tbl_B1_id, tbl_B2_id FROM tbl_relation_special WHERE tbl_A=$intTabA AND 
											   tbl_B1=".$this->tableID($elem['tableName1'])." AND tbl_B2=".$this->tableID($elem['tableName2'])."
											   AND tbl_A_id=".$arrData[$i]['id']." AND tbl_A_field='".$elem['fieldName']."'";
									$booReturn = $this->myDBClass->getDataArray($strSQL,$arrRelData,$intRelDataCount);
									if ($intRelDataCount != 0) {
										$arrDataInsert = "";
										for ($y=0;$y<$intRelDataCount;$y++) {
											$arrDataInsert[] = $arrRelData[$y]['tbl_B1_id'].".".$arrRelData[$y]['tbl_B2_id'];
										}
										$this->dataInsertRelation($intTabA,$this->tableID($elem['tableName1']),$intTabA_id,$elem['fieldName'],$arrDataInsert,$this->tableID($elem['tableName2']));
									}
								}
							}
						}
					}					
					// Logfile schreiben
					if ($intCheck != 0) {
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
				return(0);
			} else {
				// Misserfolg
				$this->strDBMessage = $this->arrLanguage['db']['failed']."<br>".$this->myDBClass->strDBError;
				return(1);
			}
		}
	}

    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Tabellen ID ermitteln
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim	
	//  
	//  Gibt die Tabellen ID der angegebenen Tabelle zurück
	//
	//  Übergabeparameter:	$strTable		Tabellenname
	//
	//  Returnwert:			Tabellen ID
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function tableID($strTable) {
		switch ($strTable) {
			case "tbl_checkcommand":		return(1);
			case "tbl_contact":				return(2);
			case "tbl_contactgroup":		return(3);
			case "tbl_host":				return(4);
			case "tbl_hostdependency":		return(5);
			case "tbl_hostescalation":		return(6);
			case "tbl_hostextinfo":			return(7);
			case "tbl_hostgroup":			return(8);
			case "tbl_misccommand":			return(9);
			case "tbl_service":				return(10);
			case "tbl_servicedependency":	return(11);
			case "tbl_serviceescalation":	return(12);
			case "tbl_serviceextinfo":		return(13);
			case "tbl_servicegroup":		return(14);
			case "tbl_timeperiod":			return(15);
			default:				 		return(0);
		}
	}	

    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Relationen einer Datentabelle zurückliefern
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   31.08.2007 wim
	//  
	//  Gibt eine Liste aus mit allen Datenfeldern einer Tabelle, die eine 1:1 oder 1:n 
	//  Beziehung zu einer anderen Tabelle haben.
	//
	//  Übergabeparameter:	$strTable		Tabellenname
	//
	//  Rückgabewert:		$arrRelations	Array mit den betroffenen Datenfeldern
	//
	//  Returnwert:			0 bei keinem Feld mit Relation
	//						1 bei mindestens einem Feld mit Relation 
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function tableRelations($strTable,&$arrRelations) {
		$arrRelations = "";
		switch ($strTable) {
			case "tbl_checkcommand":		return(0);
			case "tbl_contact":				$arrRelations[] = array('tableName' => "tbl_misccommand",
																	'fieldName' => "host_notification_commands",
																	'target' 	=> "command_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_misccommand",
																	'fieldName' => "service_notification_commands",
																	'target' 	=> "command_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_contactgroup",
																	'fieldName' => "contactgroups",
																	'target' 	=> "contactgroup_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_timeperiod",
																	'fieldName' => "host_notification_period",
																	'target' 	=> "timeperiod_name",
																	'type'		=> 1);
											$arrRelations[] = array('tableName' => "tbl_timeperiod",
																	'fieldName' => "service_notification_period",
																	'target' 	=> "timeperiod_name",
																	'type'		=> 1);																
											return(1);
			case "tbl_contactgroup":		$arrRelations[] = array('tableName' => "tbl_contact", 
																	'fieldName' => "members",
																	'target' 	=> "contact_name",
																	'type'		=> 2);
											return(1);
			case "tbl_host":				$arrRelations[] = array('tableName' => "tbl_host", 
																	'fieldName' => "parents",
																	'target' 	=> "host_name",
																	'type'		=> 2);			
											$arrRelations[] = array('tableName' => "tbl_hostgroup", 
																	'fieldName' => "hostgroups",
																	'target' 	=> "hostgroup_name",
																	'type'		=> 2);											
											$arrRelations[] = array('tableName' => "tbl_contactgroup", 
																	'fieldName' => "contact_groups",
																	'target' 	=> "contactgroup_name",
																	'type'		=> 2);											
											$arrRelations[] = array('tableName' => "tbl_timeperiod", 
																	'fieldName' => "check_period",
																	'target' 	=> "timeperiod_name",
																	'type'		=> 1);			
											$arrRelations[] = array('tableName' => "tbl_checkcommand", 
																	'fieldName' => "check_command",
																	'target' 	=> "command_name",
																	'type'		=> 1);																				
											$arrRelations[] = array('tableName' => "tbl_timeperiod", 
																	'fieldName' => "notification_period",
																	'target' 	=> "timeperiod_name",
																	'type'		=> 1);
											$arrRelations[] = array('tableName' => "tbl_misccommand", 
																	'fieldName' => "event_handler",
																	'target' 	=> "command_name",
																	'type'		=> 1);	
											return(1);
			case "tbl_hostdependency":		$arrRelations[] = array('tableName' => "tbl_host",
																	'fieldName' => "dependent_host_name",
																	'target' 	=> "host_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_host",
																	'fieldName' => "host_name",
																	'target' 	=> "host_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_hostgroup",
																	'fieldName' => "dependent_hostgroup_name",
																	'target' 	=> "hostgroup_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_hostgroup",
																	'fieldName' => "hostgroup_name",
																	'target' 	=> "hostgroup_name",
																	'type'		=> 2);		
											return(1);
			case "tbl_hostescalation":		$arrRelations[] = array('tableName' => "tbl_host",
																	'fieldName' => "host_name",
																	'target' 	=> "host_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_hostgroup",
																	'fieldName' => "hostgroup_name",
																	'target' 	=> "hostgroup_name",
																	'type'		=> 2); 
											$arrRelations[] = array('tableName' => "tbl_contactgroup",
																	'fieldName' => "contact_groups",
																	'target' 	=> "contactgroup_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_timeperiod",
																	'fieldName' => "escalation_period",
																	'target' 	=> "timeperiod_name",
																	'type'		=> 1);	
											return(1);
			case "tbl_hostextinfo":			$arrRelations[] = array('tableName' => "tbl_host",
																	'fieldName' => "host_name",
																	'target' 	=> "host_name",
																	'type'		=> 1);	
											return(1);
			case "tbl_hostgroup":			$arrRelations[] = array('tableName' => "tbl_host",
																	'fieldName' => "members",
																	'target' 	=> "host_name",
																	'type'		=> 2);	
											return(1);
			case "tbl_misccommand":			return(0);
			case "tbl_service":				$arrRelations[] = array('tableName' => "tbl_host",
																	'fieldName' => "host_name",
																	'target' 	=> "host_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_hostgroup",
																	'fieldName' => "hostgroup_name",
																	'target' 	=> "hostgroup_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_servicegroup",
																	'fieldName' => "servicegroups",
																	'target' 	=> "servicegroup_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_contactgroup",
																	'fieldName' => "contact_groups",
																	'target' 	=> "contactgroup_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_timeperiod", 
																	'fieldName' => "check_period",
																	'target' 	=> "timeperiod_name",
																	'type'		=> 1);			
											$arrRelations[] = array('tableName' => "tbl_checkcommand", 
																	'fieldName' => "check_command",
																	'target' 	=> "command_name",
																	'type'		=> 1);																				
											$arrRelations[] = array('tableName' => "tbl_timeperiod", 
																	'fieldName' => "notification_period",
																	'target' 	=> "timeperiod_name",
																	'type'		=> 1);
											$arrRelations[] = array('tableName' => "tbl_misccommand", 
																	'fieldName' => "event_handler",
																	'target' 	=> "command_name",
																	'type'		=> 1);																		
											return(1);
			case "tbl_servicedependency":	$arrRelations[] = array('tableName' => "tbl_host",
																	'fieldName' => "dependent_host_name",
																	'target' 	=> "host_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_host",
																	'fieldName' => "host_name",
																	'target' 	=> "host_name",
																	'type'		=> 2); 
											$arrRelations[] = array('tableName' => "tbl_hostgroup",
																	'fieldName' => "dependent_hostgroup_name",
																	'target' 	=> "hostgroup_name",
																	'type'		=> 2); 
											$arrRelations[] = array('tableName' => "tbl_hostgroup",
																	'fieldName' => "hostgroup_name",
																	'target' 	=> "hostgroup_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_service",
																	'fieldName' => "dependent_service_description",
																	'target' 	=> "service_description",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_service",
																	'fieldName' => "service_description",
																	'target' 	=> "service_description",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_servicegroup",
																	'fieldName' => "dependent_servicegroup_name",
																	'target' 	=> "servicegroup_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_servicegroup",
																	'fieldName' => "servicegroup_name",
																	'target' 	=> "servicegroup_name",
																	'type'		=> 2);
											return(1);
			case "tbl_serviceescalation":	$arrRelations[] = array('tableName' => "tbl_host",
																	'fieldName' => "host_name",
																	'target' 	=> "host_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_hostgroup",
																	'fieldName' => "hostgroup_name",
																	'target' 	=> "hostgroup_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_service",
																	'fieldName' => "service_description",
																	'target' 	=> "service_description",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_servicegroup",
																	'fieldName' => "servicegroup_name",
																	'target' 	=> "servicegroup_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_contactgroup",
																	'fieldName' => "contact_groups",
																	'target' 	=> "contactgroup_name",
																	'type'		=> 2);
											$arrRelations[] = array('tableName' => "tbl_timeperiod",
																	'fieldName' => "escalation_period",
																	'target' 	=> "timeperiod_name",
																	'type'		=> 1);
											return(1);
			case "tbl_serviceextinfo":		$arrRelations[] = array('tableName' => "tbl_host",
																	'fieldName' => "host_name",
																	'target' 	=> "host_name",
																	'type'		=> 1);	
											$arrRelations[] = array('tableName' => "tbl_service",
																	'fieldName' => "service_description",
																	'target' 	=> "service_description",
																	'type'		=> 1);	
											return(1);
			case "tbl_servicegroup":		$arrRelations[] = array('tableName1' => "tbl_host",
																	'tableName2' => "tbl_service",
																	'fieldName'  => "members",
																	'target1' 	 => "host_name",
																	'target2' 	 => "service_description",
																	'type'		 => 3);	
											return(1);
			case "tbl_timeperiod":			return(0);
			default:				 		return(0);
		}
	}	
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Mussdaten prüfen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Überprüft, ob mit dem mitgelieferten Datensatz in einer anderen Tabelle eine Relation
	//  besteht, die nicht gelöscht werden darf. Alle gefundenen Relationen werden als
	//  Resultatearray zurückgegeben.
	//
	//  Übergabeparameter:	$strTable		Tabellenname
	//						$intDataId		Daten ID
	//
	//  Rückgabewert:		$arrInfo		Array mit den betroffenen Datenfeldern (Tabelle, Name)
	//
	//  Returnwert:			0 wenn keine Relation gefunden wurde
	//						1 wenn mindestens eine Relation gefunden wurde 
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function checkMustdata($strTableName,$intDataId,&$arrInfo) {
		$intTableID = $this->tableID($strTableName);
		$intReturn  = 0;
		// SQL Statement nach Tabellennamen
		switch ($strTableName) {
			case "tbl_timeperiod":
				$strName = $this->myDBClass->getFieldData("SELECT timeperiod_name FROM tbl_timeperiod WHERE id=$intDataId");
				$intReturn += $this->getMustDataSingle("host_name","tbl_host","check_period","notification_period",$intDataId,$strName,"Host",$arrInfo);
				$intReturn += $this->getMustDataSingle("config_name","tbl_service","check_period","notification_period",$intDataId,$strName,"Service",$arrInfo);
				$intReturn += $this->getMustDataSingle("contact_name","tbl_contact","host_notification_period","service_notification_period",$intDataId,$strName,"Contact",$arrInfo);
				break;
			case "tbl_checkcommand":	
				$strName = $this->myDBClass->getFieldData("SELECT command_name FROM tbl_checkcommand WHERE id=$intDataId");
				$strSQL    = "SELECT DISTINCT config_name FROM tbl_service WHERE check_command='$intDataId' OR check_command LIKE '$intDataId!%'";
				$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
				if (($booReturn == true) && ($intDataCount != 0)) {
					$intReturn = 1;
					foreach ($arrData AS $elem) {
						$arrInfo[] = array("entry" => $strName, "target_table" => "Service", "target_name" => $elem['config_name']);
					}
				}
				break;
			case "tbl_contactgroup":	
				$strName = $this->myDBClass->getFieldData("SELECT contactgroup_name FROM tbl_contactgroup WHERE id=$intDataId");
				$intReturn += $this->getMustDataMultiple("host_name","tbl_host","contact_groups",$intTableID,$intDataId,$strName,"Host",$arrInfo);
				$intReturn += $this->getMustDataMultiple("config_name","tbl_service","contact_groups",$intTableID,$intDataId,$strName,"Service",$arrInfo);
				$intReturn += $this->getMustDataMultiple("config_name","tbl_hostescalation","contact_groups",$intTableID,$intDataId,$strName,"Hostescalation",$arrInfo);	
				$intReturn += $this->getMustDataMultiple("config_name","tbl_serviceescalation","contact_groups",$intTableID,$intDataId,$strName,"Serviceescalation",$arrInfo);	
				break;
			case "tbl_hostgroup":
				$strName = $this->myDBClass->getFieldData("SELECT hostgroup_name FROM tbl_hostgroup WHERE id=$intDataId");
				$intReturn += $this->getMustDataMultiple("config_name","tbl_service","hostgroup_name",$intTableID,$intDataId,$strName,"Service",$arrInfo);
				$intReturn += $this->getMustDataMultiple("config_name","tbl_hostdependency","hostgroup_name",$intTableID,$intDataId,$strName,"Hostdependencies",$arrInfo);
				$intReturn += $this->getMustDataMultiple("config_name","tbl_hostdependency","dependent_hostgroup_name",$intTableID,$intDataId,$strName,"Hostdependencies",$arrInfo);	
				$intReturn += $this->getMustDataMultiple("config_name","tbl_hostescalation","hostgroup_name",$intTableID,$intDataId,$strName,"Hostescalation",$arrInfo);	
				$intReturn += $this->getMustDataMultiple("config_name","tbl_servicedependency","hostgroup_name",$intTableID,$intDataId,$strName,"Servicedependencies",$arrInfo);
				$intReturn += $this->getMustDataMultiple("config_name","tbl_servicedependency","dependent_hostgroup_name",$intTableID,$intDataId,$strName,"Servicedependencies",$arrInfo);	
				$intReturn += $this->getMustDataMultiple("config_name","tbl_serviceescalation","hostgroup_name",$intTableID,$intDataId,$strName,"Serviceescalation",$arrInfo);					
				break;
			case "tbl_servicegroup":
				$strName = $this->myDBClass->getFieldData("SELECT servicegroup_name FROM tbl_servicegroup WHERE id=$intDataId");
				$intReturn += $this->getMustDataMultiple("config_name","tbl_servicedependency","servicegroup_name",$intTableID,$intDataId,$strName,"Servicedependencies",$arrInfo);
				$intReturn += $this->getMustDataMultiple("config_name","tbl_servicedependency","dependent_servicegroup_name",$intTableID,$intDataId,$strName,"Servicedependencies",$arrInfo);	
				$intReturn += $this->getMustDataMultiple("config_name","tbl_serviceescalation","servicegroup_name",$intTableID,$intDataId,$strName,"Serviceescalation",$arrInfo);					
				break;
			case "tbl_contact":
				$strName = $this->myDBClass->getFieldData("SELECT contact_name FROM tbl_contact WHERE id=$intDataId");
				$intReturn += $this->getMustDataMultiple("contactgroup_name","tbl_contactgroup","members",$intTableID,$intDataId,$strName,"Contactgroups",$arrInfo);
				break;
			case "tbl_host":
				$strName = $this->myDBClass->getFieldData("SELECT host_name FROM tbl_host WHERE id=$intDataId");
				$intReturn += $this->getMustDataMultiple("config_name","tbl_service","host_name",$intTableID,$intDataId,$strName,"Service",$arrInfo);
				$intReturn += $this->getMustDataMultiple("hostgroup_name","tbl_hostgroup","members",$intTableID,$intDataId,$strName,"Hostgroup",$arrInfo);
				//$intReturn += $this->getMustDataCross("servicegroup_name","tbl_servicegroup","members",$intTableID,$intDataId,$strName,"Servicegroup",$arrInfo);
				$intReturn += $this->getMustDataMultiple("config_name","tbl_hostdependency","dependent_host_name",$intTableID,$intDataId,$strName,"Hostdependencies",$arrInfo);
				$intReturn += $this->getMustDataMultiple("config_name","tbl_hostdependency","host_name",$intTableID,$intDataId,$strName,"Hostdependencies",$arrInfo);
				$intReturn += $this->getMustDataMultiple("config_name","tbl_hostescalation","host_name",$intTableID,$intDataId,$strName,"Hostescalation",$arrInfo);	
				$intReturn += $this->getMustDataSingle("host_name","tbl_hostextinfo","host_name","host_name",$intDataId,$strName,"Hostextinfo",$arrInfo);	
				$intReturn += $this->getMustDataMultiple("config_name","tbl_servicedependency","dependent_host_name",$intTableID,$intDataId,$strName,"Servicedependencies",$arrInfo);	
				$intReturn += $this->getMustDataMultiple("config_name","tbl_serviceescalation","host_name",$intTableID,$intDataId,$strName,"Serviceescalation",$arrInfo);					
				$intReturn += $this->getMustDataSingle("config_name","tbl_serviceextinfo","host_name","host_name",$intDataId,$strName,"Serviceextinfo",$arrInfo);	
				break;	
			case "tbl_service":
				$strName = $this->myDBClass->getFieldData("SELECT CONCAT(config_name,'::',service_description) FROM tbl_service WHERE id=$intDataId");
				$intReturn += $this->getMustDataCross("servicegroup_name","tbl_servicegroup","members",$intTableID,$intDataId,$strName,"Servicegroup",$arrInfo);
				$intReturn += $this->getMustDataMultiple("config_name","tbl_servicedependency","dependent_service_name",$intTableID,$intDataId,$strName,"Servicedependencies",$arrInfo);	
				$intReturn += $this->getMustDataMultiple("config_name","tbl_serviceescalation","service_name",$intTableID,$intDataId,$strName,"Serviceescalation",$arrInfo);					
				$intReturn += $this->getMustDataSingle("config_name","tbl_serviceextinfo","host_name","host_name",$intDataId,$strName,"Serviceextinfo",$arrInfo);	
				break;	
		} 
		if ($intReturn != 0) $intReturn = 1; 
		return($intReturn);
	}
	// Hilfsabfragefunktion für 1:1 Relationen
	function getMustDataSingle($strField,$strTableName,$strWhere_field1,$strWhere_field2,$intDataId,$strName,$strTable,&$arrInfo) {
		$strSQL    = "SELECT $strField FROM $strTableName WHERE $strWhere_field1=$intDataId OR $strWhere_field2=$intDataId AND active='1'";
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
		if (($booReturn == true) && ($intDataCount != 0)) {
			foreach ($arrData AS $elem) {
				if ($strTableName == "tbl_hostextinfo") {
					$arrInfo[] = array("entry" => $strName, "target_table" => $strTable, "target_name" => $strName);				
				} else {
					$arrInfo[] = array("entry" => $strName, "target_table" => $strTable, "target_name" => $elem[$strField]);
				}
			}
			return(1);
		}
		return(0);
	}
	// Hilfsabfragefunktion für 1:n Relationen
	function getMustDataMultiple($strFieldA,$strTableA,$strTableA_field,$intTableB_id,$intDataId,$strName,$strTable,&$arrInfo) {
		$intTableA_id = $this->tableID($strTableA);
		if ($strTableA == "tbl_service") {
			$strSQL    = "SELECT CONCAT( config_name, '::', service_description ) AS $strFieldA, count(*) AS counter FROM $strTableA 
						  LEFT JOIN tbl_relation ON $strTableA.id=tbl_relation.tbl_A_id AND tbl_relation.tbl_A=$intTableA_id
						  WHERE tbl_relation.tbl_A_field='$strTableA_field' AND tbl_relation.tbl_B=$intTableB_id AND tbl_relation.tbl_B_id=$intDataId AND active='1'
						  GROUP BY $strFieldA HAVING counter=1";
		} else {
			$strSQL    = "SELECT $strFieldA, count(*) AS counter FROM $strTableA 
						  LEFT JOIN tbl_relation ON $strTableA.id=tbl_relation.tbl_A_id AND tbl_relation.tbl_A=$intTableA_id
						  WHERE tbl_relation.tbl_A_field='$strTableA_field' AND tbl_relation.tbl_B=$intTableB_id AND tbl_relation.tbl_B_id=$intDataId AND active='1'
						  GROUP BY $strFieldA HAVING counter=1";
		}
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
		if (($booReturn == true) && ($intDataCount != 0)) {
			foreach ($arrData AS $elem) {
				$arrInfo[] = array("entry" => $strName, "target_table" => $strTable, "target_name" => $elem[$strFieldA]);
			}
			return(1);
		}
		return(0);
	}
	// Hilfsabfragefunktion für 1:(n::n) Relationen
	function getMustDataCross($strFieldA,$strTableA,$strTableA_field,$intTableB_id,$intDataId,$strName,$strTable,&$arrInfo) {
		$intTableA_id = $this->tableID($strTableA);
		if ($strTableA == "tbl_service") {
			$strSQL    = "SELECT CONCAT( config_name, '::', service_description ) AS $strFieldA, count(*) AS counter FROM $strTableA 
						  LEFT JOIN tbl_relation ON $strTableA.id=tbl_relation.tbl_A_id AND tbl_relation.tbl_A=$intTableA_id
						  WHERE tbl_relation.tbl_A_field='$strTableA_field' AND tbl_relation.tbl_B=$intTableB_id AND tbl_relation.tbl_B_id=$intDataId
						  GROUP BY $strFieldA HAVING counter=1";
		} else if (($strTableA == "tbl_servicegroup") && ($intTableB_id == "10")) {
			// Tabelle B = Services, Tabelle A = Servicegroup
			// Alle Hosts suchen die in Tabelle B gewählt sind
			$strSQL = "SELECT tbl_B_id FROM tbl_relation 
					   LEFT JOIN tbl_service ON tbl_relation.tbl_A_id = tbl_service.id
					   WHERE tbl_A_field='host_name' AND tbl_A=$intTableB_id AND tbl_B=4";
			$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
			if (($booReturn == true) && ($intDataCount != 0)) {
				$strHostID = "";
				foreach ($arrData AS $elem) { 
					$strHostID .= $elem['tbl_B_id'].", ";
				}
				$strHostID = substr($strHostID,0,-2);
			}
			// Keine HostID gesetzt -> Alle Hosts eintragen
			if (!isset($strHostID)) {
				$strSQL = "SELECT id FROM tbl_host WHERE active='1'";
				$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
				if (($booReturn == true) && ($intDataCount != 0)) {
					$strHostID = "";
					foreach ($arrData AS $elem) { 
						$strHostID .= $elem['id'].", ";
					}
					$strHostID = substr($strHostID,0,-2);
				}
			}
			$strSQL    = "SELECT $strFieldA, count(*) AS counter FROM $strTableA 
						  LEFT JOIN tbl_relation_special ON $strTableA.id=tbl_relation_special.tbl_A_id 
						  WHERE tbl_relation_special.tbl_A_field='$strTableA_field' 
						        AND tbl_relation_special.tbl_A=$intTableA_id AND tbl_relation_special.tbl_B1=4 AND tbl_relation_special.tbl_B2=$intTableB_id 
						        AND tbl_relation_special.tbl_B1_id IN ($strHostID) AND tbl_relation_special.tbl_B2_id = $intDataId
						  GROUP BY $strFieldA HAVING counter >= 1";
		} else if (($strTableA == "tbl_servicegroup") && ($intTableB_id == "4")) {
			// Host in Servicegroup verwendet
			// -> Folgt noch
		}
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
		if (($booReturn == true) && ($intDataCount != 0)) {
			foreach ($arrData AS $elem) {
				$arrInfo[] = array("entry" => $strName, "target_table" => $strTable, "target_name" => $elem[$strFieldA]);
			}
			return(1);
		}
		return(0);
	}

    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Services für Hosts zurückliefern
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Gibt ein Array der Servicenamen und IDs zurück welche einem Host zugeteilt sind.
	//
	//  Übergabeparameter:	$arrHostIDs		Array mit allen HostIDs
	//						$intModeId		(Reserviert für sptere Verwendung)
	//
	//  Rückgabewert:		$arrServiceIDs	Array mit allen ServiceIDs
	//						
	//  Returnwert:			0 bei Erfolg, 1 bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function getServicesByHost($arrHostIDs,&$arrServiceIDs,$intModeId=0) {
		// In welchen Hostgruppen ist dieser Host Mitglied?
		$strHostIDs = $this->makeCommaString($arrHostIDs);
		$strSQL = "SELECT DISTINCT tbl_hostgroup.id AS hostgroup_id FROM tbl_hostgroup
				   LEFT JOIN tbl_relation ON tbl_A_id = tbl_hostgroup.id
				   WHERE ((tbl_hostgroup.members = 1 AND tbl_relation.tbl_A = ".$this->tableID("tbl_hostgroup")."
						 AND tbl_relation.tbl_B = ".$this->tableID("tbl_host").") AND tbl_B_id IN ($strHostIDs))
						 OR  tbl_hostgroup.members = 2
				   ORDER BY hostgroup_id"; 
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrDataHostgroup,$intDataCount1);
		// Services nach HostID holen
		$strSQL = "SELECT DISTINCT tbl_service.id AS service_id, tbl_service.service_description AS description
				   FROM tbl_service 
				   LEFT JOIN tbl_relation ON tbl_service.id = tbl_relation.tbl_A_id
				   LEFT JOIN tbl_host ON tbl_relation.tbl_B_id = tbl_host.id
				   WHERE ((tbl_service.host_name = 1 AND tbl_relation.tbl_A = ".$this->tableID("tbl_service")."
				         AND tbl_relation.tbl_B = ".$this->tableID("tbl_host").") AND tbl_host.id IN ($strHostIDs)) 
						 OR (tbl_service.host_name = 2) 
				   ORDER BY tbl_service.service_description";		
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrDataHostServices,$intDataCount2);
		// Daten in einem Array zusammenfgen
		foreach ($arrDataHostServices AS $elem) {
			$arrServiceData[] = Array( "id" => $elem['service_id'], "description" => $elem['description']);
			$arrIdCheck[]     = $elem['service_id'];
		}
		// Services nach HostgroupID holen			
		if ($intDataCount1 != 0) { 
			$strHostgroupIDs = "";
			foreach ($arrDataHostgroup AS $elem) {
				$strHostgroupIDs .= $elem['hostgroup_id'].",";	
			}
			$strHostgroupIDs = substr($strHostgroupIDs,0,-1);
			$strSQL = "SELECT DISTINCT tbl_service.id AS service_id, tbl_service.service_description AS description
					   FROM tbl_service 
					   LEFT JOIN tbl_relation ON tbl_service.id = tbl_relation.tbl_A_id
					   LEFT JOIN tbl_hostgroup ON tbl_relation.tbl_B_id = tbl_hostgroup.id
					   WHERE ((tbl_service.hostgroup_name = 1 AND tbl_relation.tbl_A = ".$this->tableID("tbl_service")."
							 AND tbl_relation.tbl_B = ".$this->tableID("tbl_hostgroup").") AND tbl_hostgroup.id IN ($strHostgroupIDs)) 
							 OR (tbl_service.hostgroup_name = 2) 
					   ORDER BY tbl_service.service_description";		     
			$booReturn = $this->myDBClass->getDataArray($strSQL,$arrDataHostgroupServices,$intDataCount3);
			// Daten in einem Array zusammenfgen
			if ($intDataCount3 != 0) {
				foreach ($arrDataHostgroupServices AS $elem) {
					if (!in_array($elem['service_id'],$arrIdCheck)) {
						$arrServiceData[] = Array( "description" => $elem['description'], "id" => $elem['service_id']);
					} 
				}
				// Array sortieren und ausgeben
				asort($arrServiceData);
				reset($arrServiceData);	
			}
		}
		// Array ausgeben
		$arrServiceIDs = $arrServiceData;
		return(0);
	}	
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Services für Hostgruppen zurückliefern
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Gibt ein Array der Servicenamen und IDs zurück welche einer Hostgruppe zugeteilt sind.
	//
	//  Übergabeparameter:	$arrHostgroupIDs	Array mit allen HostIDs
	//						$intModeId			(Reserviert für sptere Verwendung)
	//
	//  Rückgabewert:		$arrServiceIDs		Array mit allen ServiceIDs
	//						
	//  Returnwert:			0 bei Erfolg, 1 bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function getServicesByHostgroup($arrHostgroupIDs,&$arrServiceIDs,$intModeId=0) {
		// Services nach HostgruppenID holen
		$strHostgroupIDs = $this->makeCommaString($arrHostgroupIDs);
		// Servicedaten von Hostgruppen die direkt einem Service zugeordnet wurden
		$strSQL = "SELECT DISTINCT tbl_service.id AS service_id, tbl_service.service_description AS description
				   FROM tbl_service 
				   LEFT JOIN tbl_relation ON tbl_service.id = tbl_relation.tbl_A_id
				   LEFT JOIN tbl_hostgroup ON tbl_relation.tbl_B_id = tbl_hostgroup.id
				   WHERE ((tbl_service.hostgroup_name = 1 AND tbl_relation.tbl_A = ".$this->tableID("tbl_service")."
						 AND tbl_relation.tbl_B = ".$this->tableID("tbl_hostgroup").") AND tbl_hostgroup.id IN ($strHostgroupIDs)) 
						 OR (tbl_service.hostgroup_name = 2) 
				   ORDER BY tbl_service.service_description";	
		$booReturn1 = $this->myDBClass->getDataArray($strSQL,$arrDataHostgroupServices1,$intDataCount1);
		if ($booReturn1 == false) return(1);		
		// Servicedaten von Hostgruppen die via Hosts zugeordnet sind   
		$strSQL = "SELECT DISTINCT tbl_service.id AS service_id, tbl_service.service_description AS description
				   FROM tbl_hostgroup
				   LEFT JOIN tbl_relation AS rel_1 ON tbl_hostgroup.id=rel_1.tbl_A_id
				   LEFT JOIN tbl_host ON rel_1.tbl_B_id=tbl_host.id
				   LEFT JOIN tbl_relation AS rel_2 ON tbl_host.id=rel_2.tbl_B_id
				   LEFT JOIN tbl_service ON rel_2.tbl_A_id=tbl_service.id						  
				   WHERE (rel_1.tbl_A=8 AND rel_1.tbl_B=4 AND rel_1.tbl_A_field='members' AND
						  rel_2.tbl_A=10 AND rel_2.tbl_B=4 AND rel_2.tbl_A_field='host_name' AND 
						  tbl_hostgroup.id IN ($strHostgroupIDs)) OR tbl_service.host_name=2
				   GROUP BY description
				   ORDER BY description";
		$booReturn2 = $this->myDBClass->getDataArray($strSQL,$arrDataHostgroupServices2,$intDataCount2);
		if ($booReturn2 == false) return(1);
		// Arrays zusammenfgen
		if (($intDataCount1 == 0) && ($intDataCount2 == 0)) {
			$arrServiceIDs = "";
			$arrServiceIDs[] = Array( "id" => 0, "description" => "");
			return(0);
		} else if (($intDataCount1 != 0) && ($intDataCount2 == 0)) {
			$arrDataHostgroupServices = $arrDataHostgroupServices1;
		} else if (($intDataCount1 == 0) && ($intDataCount2 != 0)) {
			$arrDataHostgroupServices = $arrDataHostgroupServices2;
		} else {
			$arrDataHostgroupServices = array_merge($arrDataHostgroupServices1,$arrDataHostgroupServices2);
		}
		// Doppelte Eintrge herausfiltern
		foreach($arrDataHostgroupServices AS $elem) {
			if (!isset($arrTemp1) || !in_array($elem['description'],$arrTemp1)) {
				$arrTemp1[] = $elem['description'];
				$arrTemp2[] = Array( "id" => $elem['service_id'], "description" => $elem['description']);
			}
		}
		$arrDataHostgroupServices = $arrTemp2;
		// Daten in das Array abfllen
		$arrServiceData = "";
		foreach ($arrDataHostgroupServices AS $elem) {
			$arrServiceData[] = Array( "id" => $elem['id'], "description" => $elem['description']);
		}
		// Array ausgeben
		$arrServiceIDs = $arrServiceData;
		return(0);
	}	
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Logbuch schreiben
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.0.2 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Speichert einen übergebenen String im Logbuch
	//
	//  Übergabeparameter:	$strMessage				Mitteilung
	//						$_SESSION['username']	Benutzername
	//
	//  Returnwert:			0 bei Erfolg, 1 bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function writeLog($strMessage) {
		// Logstring in Datenbank schreiben
		$strUserName = (isset($_SESSION['username']) && ($_SESSION['username'] != ""))	? $_SESSION['username'] : "unknown";
		$booReturn   = $this->myDBClass->insertData("INSERT INTO tbl_logbook SET user='".$strUserName."',time=NOW(), entry='$strMessage'");
		if ($booReturn == false) return(1);
		return(0);
	}
}
?>