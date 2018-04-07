<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project   : NagiosQL
// Component : Import Class
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2010-10-25 15:45:55 +0200 (Mo, 25 Okt 2010) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.0.4
// Revision  : $LastChangedRevision: 827 $
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
// Name: nagimport
//
// Klassenvariabeln:
// -----------------
// $arrSettings:  Mehrdimensionales Array mit den globalen Konfigurationseinstellungen
// $myDBClass:    Datenbank Klassenobjekt
// $myDataClass:  Standard Klassenobjekt
// $strDBMessage  Mitteilungen des Datenbankservers
// $strMessage    Mitteilungen der Klassenfunktion
//
// Externe Funktionen
// ------------------
//
//
///////////////////////////////////////////////////////////////////////////////////////////////
class nagimport {
    // Klassenvariabeln deklarieren
  var $arrSettings;       // Wird im Klassenkonstruktor gefüllt
  var $intDomainId = 0;     // Wird im Klassenkonstruktor gefüllt
  var $myDBClass;         // Wird in der Datei prepend_adm.php definiert
  var $myDataClass;       // Wird in der Datei prepend_adm.php definiert
  var $myConfigClass;       // Wird in der Datei prepend_adm.php definiert
  var $strDBMessage    = "";    // Wird Klassenintern gefüllt
  var $strMessage    = "";    // Wird Klassenintern gefüllt
  var $strList1    = "";    // Werteliste 1
  var $strList2    = "";    // Werteliste 2

    ///////////////////////////////////////////////////////////////////////////////////////////
  //  Klassenkonstruktor
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Tätigkeiten bei Klasseninitialisierung
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function nagimport() {
    // Globale Einstellungen einlesen
    $this->arrSettings = $_SESSION['SETS'];
    if (isset($_SESSION['domain'])) $this->intDomainId = $_SESSION['domain'];
  }

    ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Datenimport
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Importiert eine Konfigurationsdatei und schreibt deren Daten in die entsprechende
  //  Datentabelle
  //
  //  Übergabeparameter:  $strFileName    Importdateiname
  //  ------------------  $intOverwrite   0 = Daten nicht überschreiben
  //                      1 = Daten überschreiben
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //            Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function fileImport($strFileName,$intOverwrite) {
    // Variabeln deklarieren
    $intBlock     = 0;
    $intCheck     = 0;
    $intRemoveTmp   = 0;
    $strFileName    = trim($strFileName);
    $booReturn      = $this->myConfigClass->getConfigData("method",$intMethod);
    // Sind die Dateien lesbar?
    if ($intMethod == 1) {
      if (!is_readable($strFileName)) {
        $this->strDBMessage .= gettext('Cannot open the data file (check the permissions)!')." ".$strFileName."<br>";
        return(1);
      }
    } else if ($intMethod == 2) {
      // Set up basic connection
      $booReturn    = $this->myConfigClass->getConfigData("server",$strServer);
      $conn_id    = ftp_connect($strServer);
      // Login with username and password
      $booReturn    = $this->myConfigClass->getConfigData("user",$strUser);
      $booReturn    = $this->myConfigClass->getConfigData("password",$strPasswd);
      $login_result   = ftp_login($conn_id, $strUser, $strPasswd);
      // Check connection
      if ((!$conn_id) || (!$login_result)) {
        return(1);
      } else {
        if (!ftp_get($conn_id,$this->arrSettings['path']['tempdir']."/nagiosql_import_temp.dat",$strFileName,FTP_ASCII)) {
          $this->strDBMessage = gettext('Cannot open the configuration file (FTP connection failed)!');
          ftp_close($conn_id);
          return(1);
        }
        $intRemoveTmp   = 1;
        $strFileName  = $this->arrSettings['path']['tempdir']."/nagiosql_import_temp.dat";
      }
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
        $arrData = "";
        continue;
      }
      // Blockdaten in ein Array speichern
      if (($intBlock == 1) && ($arrLine[0] != "}")) {
        $strExclude = "template_name,alias,name,use,register";
		if (($strBlockKey == "timeperiod") && (!in_array($arrLine[0],explode(",",$strExclude)))) {
          $arrNewLine = explode(" ",$strNewLine);
          $arrData[$arrLine[0]] = array("key" => str_replace(" ".$arrNewLine[count($arrNewLine)-1],"",$strNewLine), "value" => $arrNewLine[count($arrNewLine)-1]);
        } else {
		  $key   = $arrLine[0];
		  $value = str_replace($arrLine[0]." ","",$strNewLine);
		  // Sonderfall retry_check_interval, normal_check_interval
		  if ($key == "retry_check_interval")  $key = "retry_interval";
		  if ($key == "normal_check_interval") $key = "check_interval";
          $arrData[$arrLine[0]] = array("key" => $key, "value" => $value);
        }
      }
      // Bei Blockende Daten verarbeiten
      if ((substr_count($strConfLine,"}") == 1) && (is_array($arrData)))  {
        $intBlock = 0;
        $intReturn = $this->importTable($strBlockKey,$arrData,$intOverwrite,$strFileName);
      }
    }
    if ($intRemoveTmp == 1) {
      unlink($strFileName);
    }
    return($intCheck);
  }

  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Hilfsfunktion: Tabelle importieren
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Importiert eine Konfigurationsdatei in die passende Datentabelle.
  //
  //  Übergabeparameter:  $strBlockKey      Konfigurationsschlüssel (define)
  //              $arrImportData      Eingelesene Daten eines Blockes
  //            $intOverwrite     Daten in Tabelle überschreiben 1=Ja, 0=Nein
  //            $strFileName      Name der Konfigurationsdatei
  //
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg / 2 Eintrag existiert schon
  //            Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function importTable($strBlockKey,$arrImportData,$intOverwrite,$strFileName) {
    // Variabeln deklarieren
    $intExists      = 0;
    $intInsertRelations = 0;
    $intInsertVariables = 0;
    $intIsTemplate    = 0;
    $strVCValues    = "";
    $strRLValues    = "";
    $strVWValues    = "";
    $strVIValues     = "";
    $intWriteConfig   = 0;
    $strWhere     = "";
    $this->strList1   = "";
    $this->strList2   = "";
    // Template oder Konfiguration
    if (array_key_exists("name",$arrImportData) && (isset($arrImportData['register']) && ($arrImportData['register']['value'] == 0))) {
      $intIsTemplate = 1;
    }
    // Tabellenname festlegen
    if ($intIsTemplate == 0) {
      switch($strBlockKey) {
        case "command":       $strTable = "tbl_command";       $strKeyField = "command_name";     break;
        case "contactgroup":    $strTable = "tbl_contactgroup";    $strKeyField = "contactgroup_name";  break;
        case "contact":       $strTable = "tbl_contact";       $strKeyField = "contact_name";     break;
        case "timeperiod":      $strTable = "tbl_timeperiod";      $strKeyField = "timeperiod_name";    break;
        case "host":        $strTable = "tbl_host";        $strKeyField = "host_name";      break;
        case "service":       $strTable = "tbl_service";       $strKeyField = "";           break;
        case "hostgroup":     $strTable = "tbl_hostgroup";     $strKeyField = "hostgroup_name";     break;
        case "servicegroup":    $strTable = "tbl_servicegroup";    $strKeyField = "servicegroup_name";  break;
        case "hostescalation":    $strTable = "tbl_hostescalation";    $strKeyField = "";           break;
        case "serviceescalation": $strTable = "tbl_serviceescalation"; $strKeyField = "";           break;
        case "hostdependency":    $strTable = "tbl_hostdependency";    $strKeyField = "";           break;
        case "servicedependency": $strTable = "tbl_servicedependency"; $strKeyField = "";           break;
        case "hostdependency":    $strTable = "tbl_hostdependency";    $strKeyField = "";           break;
        case "servicedependency": $strTable = "tbl_servicedependency"; $strKeyField = "";           break;
        case "hostextinfo":     $strTable = "tbl_hostextinfo";       $strKeyField = "host_name";      break;
        case "serviceextinfo":    $strTable = "tbl_serviceextinfo";    $strKeyField = "";           break;
        default:
          $this->strDBMessage = gettext('Table for import definition').$strBlockKey.gettext('is not available!');
          return(1);
      }
    } else {
      switch($strBlockKey) {
        case "contact":       $strTable = "tbl_contacttemplate";   $strKeyField = "name";     break;
        case "host":        $strTable = "tbl_hosttemplate";    $strKeyField = "name";     break;
        case "service":       $strTable = "tbl_servicetemplate";   $strKeyField = "name";     break;
        default:
          $this->strDBMessage = gettext('Table for import definition').$strBlockKey.gettext('is not available!');
          return(1);
      }
    }
    // Bei Konfigurationen ohne Schlüsselfeld ein solches erzeugen
    if ($strTable == "tbl_service") {
      $arrTemp1  = explode(".",strrev(basename($strFileName)),2);

      if (isset($arrImportData['host_name']))     $arrTemp2  = explode(",",$arrImportData['host_name']['value']);
      if (isset($arrImportData['hostgroup_name']))  $arrTemp3  = explode(",",$arrImportData['hostgroup_name']['value']);
      $strTemp   = "";
//      if (isset($arrTemp2) && count($arrTemp2) != 0) {
//        if (isset($arrTemp2[0])) $strTemp = $arrTemp2[0];
//      } else {
//        if (isset($arrTemp3[0])) $strTemp = $arrTemp3[0];
//      }
//      if ($strTemp == "") 
	  $strTemp = strrev($arrTemp1[1]);
      $strTemp = str_replace("+","",$strTemp);
      $strKeyField = "config_name";
      $arrImportData['config_name']['key']  = "config_name";
      $arrImportData['config_name']['value']  = $strTemp;
      $strWhere = " AND `service_description` LIKE '%".$arrImportData['service_description']['value']."%' ";
    }
    if (($strTable == "tbl_hostdependency") || ($strTable == "tbl_servicedependency")) {
      $arrTemp1  = explode(".",strrev(basename($strFileName)),2);
      if (isset($arrImportData['dependent_host_name']))     $arrTemp2  = explode(",",$arrImportData['dependent_host_name']['value']);
      if (isset($arrImportData['dependent_hostgroup_name']))  $arrTemp3  = explode(",",$arrImportData['dependent_hostgroup_name']['value']);
      $strTemp   = "";
      if (isset($arrTemp2) && count($arrTemp2) != 0) {
        if (isset($arrTemp2[0])) $strTemp = $arrTemp2[0];
      } else {
        if (isset($arrTemp3[0])) $strTemp = $arrTemp3[0];
      }
      if ($strTemp == "") $strTemp   = strrev($arrTemp1[1]);
      $strTemp = str_replace("+","",$strTemp);
      $strKeyField = "config_name";
      $arrImportData['config_name']['key']  = "config_name";
      $arrImportData['config_name']['value']  = $strTemp;
    }
    if (($strTable == "tbl_hostescalation") || ($strTable == "tbl_serviceescalation")) {
      $arrTemp1  = explode(".",strrev(basename($strFileName)),2);
      if (isset($arrImportData['host_name']))     $arrTemp2  = explode(",",$arrImportData['host_name']['value']);
      if (isset($arrImportData['hostgroup_name']))  $arrTemp3  = explode(",",$arrImportData['hostgroup_name']['value']);
      $strTemp   = "";
      if (isset($arrTemp2) && count($arrTemp2) != 0) {
        if (isset($arrTemp2[0])) $strTemp = $arrTemp2[0];
      } else {
        if (isset($arrTemp3[0])) $strTemp = $arrTemp3[0];
      }
      if ($strTemp == "") $strTemp   = strrev($arrTemp1[1]);
      $strTemp = str_replace("+","",$strTemp);
      $strKeyField = "config_name";
      $arrImportData['config_name']['key']  = "config_name";
      $arrImportData['config_name']['value']  = $strTemp;
    }
    if ($strTable == "tbl_serviceextinfo") {
      $arrTemp1  = explode(".",strrev(basename($strFileName)),2);
      if (isset($arrImportData['host_name']))       $arrTemp2  = explode(",",$arrImportData['host_name']['value']);
      if (isset($arrImportData['service_description']))   $arrTemp3  = explode(",",$arrImportData['service_description']['value']);
      if (isset($arrTemp2[0])) $strTemp  = $arrTemp2[0];
      if (isset($arrTemp3[0])) $strTemp .= " - ".$arrTemp3[0];
      if ($strTemp == "") $strTemp   = strrev($arrTemp1[1]);
      $strTemp = str_replace("+","",$strTemp);
      $strKeyField = "config_name";
      $arrImportData['config_name']['key']  = "config_name";
      $arrImportData['config_name']['value']  = $strTemp;
    }
    // Relationen dieser Tabelle einlesen
    $intRelation = $this->myDataClass->tableRelations($strTable,$arrRelations);
	
    // Existiert der Eintrag schon?
    if ($intIsTemplate == 0) {
      if (($strKeyField != "") && isset($arrImportData[$strKeyField])) {
        $intExists = $this->myDBClass->getFieldData("SELECT `id` FROM `".$strTable."` WHERE `config_id`=".$this->intDomainId." AND `".$strKeyField."`='".$arrImportData[$strKeyField]['value']."' $strWhere");
        if ($intExists == false) $intExists = 0;
      }
    } else {
      if (($strKeyField != "") && isset($arrImportData['name'])) {
        $intExists = $this->myDBClass->getFieldData("SELECT `id` FROM `".$strTable."` WHERE `config_id`=".$this->intDomainId." AND `template_name`='".$arrImportData['name']['value']."' $strWhere");
        if ($intExists == false) $intExists = 0;
      }
    }
	
	// Bei Services zweite Prüfung auf Hosts
	if (($strTable == "tbl_service") && ($intExists != 0)) {
		$intExists = 0;
		$strSQLService = "SELECT `id`,`host_name`,`hostgroup_name` FROM `tbl_service` WHERE `config_id`=".$this->intDomainId." AND `".$strKeyField."`='".$arrImportData[$strKeyField]['value']."' $strWhere";
		$booReturn     = $this->myDBClass->getDataArray($strSQLService,$arrDataService,$intDCService);
		if ($booReturn && ($intDCService != 0)) {
			$arrHc  = array();
			$arrHgc = array();
			foreach($arrDataService AS $servElem) {
				// Hosts holen
				$strSQLHC   = "SELECT host_name FROM tbl_host LEFT JOIN tbl_lnkServiceToHost ON id = idSlave WHERE idMaster = ".$servElem['id']." ORDER BY host_name";
				$booReturn  = $this->myDBClass->getDataArray($strSQLHC,$arrDataHC,$intDCHC);
				if ($servElem['host_name'] == '2') {
					$strHostline = "*";
				} else {
					$strHostline = "";
				}
				if ($booReturn && ($intDCHC != 0)) {
					foreach ($arrDataHC AS $elemHC) {
						$strHostline .= $elemHC['host_name'];
					}
				}
				$arrHc[$servElem['id']] = $strHostline;
				// Hostgruppen holen
				$strSQLHGC  = "SELECT hostgroup_name FROM tbl_hostgroup LEFT JOIN tbl_lnkServiceToHostgroup ON id = idSlave WHERE idMaster = ".$servElem['id']." ORDER BY hostgroup_name";
				$booReturn  = $this->myDBClass->getDataArray($strSQLHGC,$arrDataHGC,$intDCHGC);
				if ($servElem['hostgroup_name'] == '2') {
					$strHostgroupline = "*";
				} else {
					$strHostgroupline = "";
				}				
				if ($booReturn && ($intDCHGC != 0)) {
					foreach ($arrDataHGC AS $elemHGC) {
						$strHostgroupline .= $elemHGC['hostgroup_name'];
					}	
				}
				$arrHgc[$servElem['id']] =  $strHostgroupline;
			}
		}
		// Vergleich
		if (isset($arrImportData['host_name']['value']) && !isset($arrImportData['hostgroup_name']['value'])) {
			$arrTemp1         = explode(",",$arrImportData['host_name']['value']);
			asort($arrTemp1);
			$strHostline      = implode("::",$arrTemp1);		
			foreach ($arrHc AS $key => $chkElem1) {
				if (($chkElem1 == $strHostline) && (!isset($arrHgc[$key]) || ($arrHgc[$key] == ""))) {
				   $intExists = $key;
				}
			}
		}
		if (!isset($arrImportData['host_name']['value']) && isset($arrImportData['hostgroup_name']['value'])) {
			$arrTemp2         = explode(",",$arrImportData['hostgroup_name']['value']);
			asort($arrTemp2);
			$strHostgroupline = implode("::",$arrTemp2);		
			foreach($arrHgc AS $key => $chkElem2) {
				if (($chkElem2 == $strHostgroupline) && (!isset($arrHc[$key]) || ($arrHc[$key] == ""))) {
				   $intExists = $key;
				}
			}
		}
		if (isset($arrImportData['host_name']['value']) && isset($arrImportData['hostgroup_name']['value'])) {
		    $arrTemp1         = explode(",",$arrImportData['host_name']['value']);
			asort($arrTemp1);
			$strHostline      = implode("::",$arrTemp1);
			$arrTemp2         = explode(",",$arrImportData['hostgroup_name']['value']);
			asort($arrTemp2);
			$strHostgroupline = implode("::",$arrTemp2);
			foreach ($arrHc AS $key => $chkElem1) {
				if ($chkElem1 == $strHostline) {
					if ($strHostgroupline != "") {
						// Hostgruppen stimmen auch?
						foreach($arrHgc AS $key => $chkElem2) {
							if ($chkElem2 == $strHostgroupline) {
							   $intExists = $key;
							}
						}
					} else {
					   $intExists = $chkElem['id'];
					}
				}
			}
		}
	}

    // Existiert der Eintrag, darf aber nicht überschrieben werden?
    if (($intExists != 0) && ($intOverwrite == 0)) {
      $this->strMessage .= gettext('Entry')." ".$strKeyField."::".$arrImportData[$strKeyField]['value'].gettext('inside')." ".gettext('exists and were not overwritten')."<br>";
      return(2);
    }

    // * Werte nicht schreiben
    if ($arrImportData[$strKeyField] == "*") {
      $this->strMessage .= gettext('Entry')." ".$strKeyField."::".$arrImportData[$strKeyField]['value'].gettext('inside')." ".gettext('were not written')."<br>";
      return(2);
    }

    // Eintrag aktiv ?
    if (isset($arrImportData['register']) && ($arrImportData['register']['value'] == 0) && ($intIsTemplate != 1)) {
      $intActive = 0;
    } else {
      $intActive = 1;
    }

    // SQL Definieren - Teil 1
    if ($intExists != 0) {
      // DB Eintrag updaten
      $strSQL1 = "UPDATE `".$strTable."` SET ";
      $strSQL2 = "  `config_id`=".$this->intDomainId.", `active`='$intActive', `last_modified`=NOW() WHERE `id`=$intExists";
      // Variabeln löschen - diese werden neu angelegt
      if ($intRelation != 0) {
        foreach ($arrRelations AS $relVar) {
          if ($relVar['type'] == 4) {
            $strSQL   = "SELECT * FROM `".$relVar['linktable']."` WHERE `idMaster`=$intExists";
            $booReturn  = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
            if ($intDataCount != 0) {
              foreach ($arrData AS $elem) {
                $strSQL   = "DELETE FROM `tbl_variabledefinition` WHERE `id`=".$elem['idSlave'];
                $booReturn  = $this->myDataClass->dataInsert($strSQL,$intInsertId);
              }
            }
            $strSQL   = "DELETE FROM `".$relVar['linktable']."` WHERE `idMaster`=$intExists";
            $booReturn  = $this->myDataClass->dataInsert($strSQL,$intInsertId);
          }
        }
      }
    } else {
      // DB Eintrag einfügen
      $strSQL1 = "INSERT INTO `".$strTable."` SET ";
      $strSQL2 = "  `config_id`=".$this->intDomainId.", `active`='$intActive', `last_modified`=NOW()";
    }

    // Erklärung zu den Werten
    // -----------------------
    // $strVCValues = Reine Textwerte, werden in der Tabelle als Varchar abgespeichert null = 'null' als Textwert leer = ''
    // $strRLValues = Relationen - Werte mit Verknüpfungen zu anderen Tabellen
    // $strVWValues = Integerwerte - werden in der Tabelle ans INT gespeichert null = -1, leere Werte als NULL
    // $strVIValues = Entscheidungswerte 0 = nein, 1 = ja, 2 = Auslassen, 3 = null

    //
    // Command Konfigurationen einlesen
    // ================================
    if ($strKeyField == "command_name") {
      $strVCValues = "command_name,command_line";

      // Commandtyp herausfinden
      if ((substr_count($arrImportData['command_line']['value'],"ARG1") != 0) ||
          (substr_count($arrImportData['command_line']['value'],"USER1") != 0)) {
       $strSQL1 .= "`command_type` = 1,";
      } else {
        $strSQL1 .= "`command_type` = 2,";
      }
      $intWriteConfig = 1;
    }
    //
    // Contact Konfigurationen einlesen
    // ================================
     else if ($strKeyField == "contact_name") {
      $strVCValues  = "contact_name,alias,host_notification_options,service_notification_options,email,";
      $strVCValues .= "pager,address1,address2,address3,address4,address5,address6,name";

      $strVIValues  = "host_notifications_enabled,service_notifications_enabled,can_submit_commands,retain_status_information,";
      $strVIValues  = "retain_nonstatus_information";

      $strRLValues  = "contactgroups,host_notification_period,service_notification_period,host_notification_commands,";
      $strRLValues .= "service_notification_commands,use";
      $intWriteConfig = 1;
    }
    //
    // Contactgroup Konfigurationen einlesen
    // =====================================
     else if ($strKeyField == "contactgroup_name") {
      $strVCValues  = "contactgroup_name,alias";

      $strRLValues  = "members,contactgroup_members";
      $intWriteConfig = 1;
    }
    //
    // Timeperiod Konfigurationen einlesen
    // ===================================
     else if ($strKeyField == "timeperiod_name") {
      $strVCValues  = "timeperiod_name,alias,name";

      $strRLValues  = "exclude";
      $intWriteConfig = 1;
    }
    //
    // Contacttemplate Konfigurationen einlesen
    // ========================================
     else if (($strKeyField == "name") && ($strTable == "tbl_contacttemplate")) {
      $strVCValues  = "contact_name,alias,host_notification_options,service_notification_options,email,";
      $strVCValues .= "pager,address1,address2,address3,address4,address5,address6,name";

      $strVIValues  = "host_notifications_enabled,service_notifications_enabled,can_submit_commands,retain_status_information,";
      $strVIValues  = "retain_nonstatus_information";

      $strRLValues  = "contactgroups,host_notification_period,service_notification_period,host_notification_commands,";
      $strRLValues .= "service_notification_commands,use";
      $intWriteConfig = 1;
    }
    //
    // Host Konfigurationen einlesen
    // =============================
     else if ($strTable == "tbl_host") {
      $strVCValues  = "host_name,alias,display_name,address,initial_state,flap_detection_options,notification_options,";
      $strVCValues .= "stalking_options,notes,notes_url,action_url,icon_image,icon_image_alt,vrml_image,statusmap_image,";
      $strVCValues .= "2d_coords,3d_coords,name";

      $strVWValues  = "max_check_attempts,retry_interval,check_interval,freshness_threshold,low_flap_threshold,";
      $strVWValues .= "high_flap_threshold,notification_interval,first_notification_delay,";

      $strVIValues  = "active_checks_enabled,passive_checks_enabled,check_freshness,obsess_over_host,event_handler_enabled,";
      $strVIValues .= "flap_detection_enabled,process_perf_data,retain_status_information,retain_nonstatus_information,";
      $strVIValues .= "notifications_enabled";

      $strRLValues  = "parents,hostgroups,check_command,use,check_period,event_handler,contacts,contact_groups,";
      $strRLValues .= "notification_period";
      $intWriteConfig = 1;
    }
    //
    // Hosttemplate Konfigurationen einlesen
    // =====================================
     else if (($strKeyField == "name") && ($strTable == "tbl_hosttemplate")) {
      $strVCValues  = "template_name,alias,initial_state,flap_detection_options,notification_options,";
      $strVCValues .= "stalking_options,notes,notes_url,action_url,icon_image,icon_image_alt,vrml_image,statusmap_image,";
      $strVCValues .= "2d_coords,3d_coords,name";

      $strVWValues  = "max_check_attempts,retry_interval,check_interval,freshness_threshold,low_flap_threshold,";
      $strVWValues .= "high_flap_threshold,notification_interval,first_notification_delay,";

      $strVIValues  = "active_checks_enabled,passive_checks_enabled,check_freshness,obsess_over_host,event_handler_enabled,";
      $strVIValues .= "flap_detection_enabled,process_perf_data,retain_status_information,retain_nonstatus_information,";
      $strVIValues .= "notifications_enabled";

      $strRLValues  = "parents,hostgroups,check_command,use,check_period,event_handler,contacts,contact_groups,";
      $strRLValues .= "notification_period";
      $intWriteConfig = 1;
    }
    //
    // Hostgroup Konfigurationen einlesen
    // ==================================
     else if ($strKeyField == "hostgroup_name") {
      $strVCValues  = "hostgroup_name,alias,notes,notes_url,action_url";

      $strRLValues  = "members,hostgroup_members";
      $intWriteConfig = 1;
    }
    //
    // Service Konfigurationen einlesen
    // ================================
     else if ($strTable == "tbl_service") {
      $strVCValues  = "service_description,display_name,initial_state,flap_detection_options,stalking_options,notes,notes_url,";
      $strVCValues .= "action_url,icon_image,icon_image_alt,name,config_name,notification_options";

      $strVWValues  = "max_check_attempts,check_interval,retry_interval,freshness_threshold,low_flap_threshold,";
      $strVWValues .= "high_flap_threshold,notification_interval,first_notification_delay";

      $strVIValues  = "is_volatile,active_checks_enabled,passive_checks_enabled,parallelize_check,obsess_over_service,";
      $strVIValues .= "check_freshness,event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,";
      $strVIValues .= "retain_nonstatus_information,notifications_enabled";

      $strRLValues  = "host_name,hostgroup_name,servicegroups,use,check_command,check_period,event_handler,notification_period,contacts,contact_groups";
      $intWriteConfig = 1;
    }
    //
    // Servicetemplate Konfigurationen einlesen
    // ========================================
     else if (($strKeyField == "name") && ($strTable == "tbl_servicetemplate")) {
      $strVCValues  = "template_name,service_description,display_name,initial_state,flap_detection_options,stalking_options,notes,notes_url,";
      $strVCValues .= "action_url,icon_image,icon_image_alt,name,notification_options";

      $strVWValues  = "max_check_attempts,check_interval,retry_interval,freshness_threshold,low_flap_threshold,";
      $strVWValues .= "high_flap_threshold,notification_interval,first_notification_delay";

      $strVIValues  = "is_volatile,active_checks_enabled,passive_checks_enabled,parallelize_check,obsess_over_service,";
      $strVIValues .= "check_freshness,event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,";
      $strVIValues .= "retain_nonstatus_information,notifications_enabled";

      $strRLValues  = "host_name,hostgroup_name,servicegroups,use,check_command,check_period,event_handler,notification_period,contacts,contact_groups";
      $intWriteConfig = 1;
    }
    //
    // Servicegroup Konfigurationen einlesen
    // ==================================
     else if ($strKeyField == "servicegroup_name") {
      $strVCValues  = "servicegroup_name,alias,notes,notes_url,action_url";

      $strRLValues  = "members,servicegroup_members";
      $intWriteConfig = 1;
    }
    //
    // Hostdependency Konfigurationen einlesen
    // =======================================
     else if ($strTable == "tbl_hostdependency") {
      $strVCValues  = "config_name,execution_failure_criteria,notification_failure_criteria";

      $strVIValues  = "inherits_parent";

      $strRLValues  = "dependent_host_name,dependent_hostgroup_name,host_name,hostgroup_name,dependency_period";
      $intWriteConfig = 1;
    }
    //
    // Hostescalation Konfigurationen einlesen
    // =======================================
     else if ($strTable == "tbl_hostescalation") {
      $strVCValues  = "config_name,escalation_options";

      $strVWValues  = "first_notification,last_notification,notification_interval";

      $strRLValues  = "host_name,hostgroup_name,contacts,contact_groups,escalation_period";
      $intWriteConfig = 1;
    }
    //
    // Hostextinfo Konfigurationen einlesen
    // ====================================
     else if ($strTable == "tbl_hostextinfo") {
      $strVCValues  = "notes,notes_url,action_url,icon_image,icon_image_alt,vrml_image,statusmap_image,2d_coords,3d_coords";

      $strRLValues  = "host_name";
      $intWriteConfig = 1;
    }
    //
    // Hostdependency Konfigurationen einlesen
    // =======================================
     else if ($strTable == "tbl_servicedependency") {
      $strVCValues  = "config_name,execution_failure_criteria,notification_failure_criteria";

      $strVIValues  = "inherits_parent";

      $strRLValues  = "dependent_host_name,dependent_hostgroup_name,dependent_service_description,host_name,";
      $strRLValues .= "hostgroup_name,dependency_period,service_description";
      $intWriteConfig = 1;
    }
    //
    // Serviceescalation Konfigurationen einlesen
    // ==========================================
     else if ($strTable == "tbl_serviceescalation") {
      $strVCValues  = "config_name,escalation_options";

      $strVWValues  = "first_notification,last_notification,notification_interval";

      $strRLValues  = "host_name,hostgroup_name,contacts,contact_groups,service_description,escalation_period";
      $intWriteConfig = 1;
    }
    //
    // Serviceextinfo Konfigurationen einlesen
    // =======================================
     else if ($strTable == "tbl_serviceextinfo") {
      $strVCValues  = "notes,notes_url,action_url,icon_image,icon_image_alt";

      $strRLValues  = "host_name,service_description";
      $intWriteConfig = 1;
    }
    foreach ($arrImportData AS $elem) {
      // Command Feld zerlegen
      if ($elem['key'] == "check_command") {
        $arrValues = explode("!",$elem['value']);
      }

      $intCheck = 0;
      // Textwerte schreiben
      if (in_array($elem['key'],explode(",",$strVCValues))) {
        if (strtolower(trim($elem['value'])) == "null") {
          $strSQL1 .= "`".$elem['key']."` = 'null',";
        } else {
          $elem['value']  = addslashes($elem['value']);
          if ($intIsTemplate == 1) {
            if ($elem['key'] == "name") {
              $strSQL1 .= "template_name = '".$elem['value']."',";
            } else {
              $strSQL1 .= "`".$elem['key']."` = '".$elem['value']."',";
            }
          } else {
            $strSQL1 .= "`".$elem['key']."` = '".$elem['value']."',";
          }
        }
        $intCheck = 1;
      }
      // Statuswerte schreiben
      if (in_array($elem['key'],explode(",",$strVIValues))) {
        if (strtolower(trim($elem['value'])) == "null") {
          $strSQL1 .= "`".$elem['key']."` = 3,";
        } else {
          $strSQL1 .= "`".$elem['key']."` = '".$elem['value']."',";
        }
        $intCheck = 1;
      }
      // Integerwerte schreiben
      if (in_array($elem['key'],explode(",",$strVWValues))) {
        if (strtolower(trim($elem['value'])) == "null") {
          $strSQL1 .= "`".$elem['key']."` = -1,";
        } else {
          $strSQL1 .= "`".$elem['key']."` = '".$elem['value']."',";
        }
        $intCheck = 1;
      }
      // Relationen schreiben
      if (($intCheck == 0) && (in_array($elem['key'],explode(",",$strRLValues)))) {
        if ($elem['key'] == "use") $elem['key'] = "use_template";
        $arrTemp        = "";
        $arrTemp['key']     = $elem['key'];
        $arrTemp['value']     = $elem['value'];
        $arrImportRelations[]   = $arrTemp;
        $intInsertRelations   = 1;
        $intCheck         = 1;
      }
      // Freie Variabeln schreiben
      if ($intCheck == 0) {
        $strSkip = "register";
        if (!in_array($elem['key'],explode(",",$strSkip))) {
          $arrTemp      = "";
          $arrTemp['key']   = $elem['key'];
          $arrTemp['value']   = $elem['value'];
          $arrFreeVariables[] = $arrTemp;
          $intInsertVariables = 1;
        }
      }
    }
    $strTemp1 = "";
    $strTemp2 = "";
    // Datenbank updaten
    if ($intWriteConfig == 1) {
      $booResult = $this->myDBClass->insertData($strSQL1.$strSQL2);
    } else {
      $booResult = false;
    }
    if ($strKeyField == "") {$strKey = $strConfig;} else {$strKey = $strKeyField;}
    if ($booResult != true) {
      $this->strDBMessage = $this->myDBClass->strDBError;
      if ($strKeyField == "")
      if ($strKeyField != "") $this->strMessage .= gettext('Entry')." ".$strKey."::".$arrImportData[$strKeyField]['value']." ".gettext('inside')." ".$strTable." ".gettext('could not be inserted:')." ".mysql_error()."<br>";
      if ($strKeyField == "") $this->strMessage .= gettext('Entry')." ".$strTemp1."::".$strTemp2.gettext('inside')." ".$strTable." ".$strTable." ".gettext('could not be inserted:')." ".mysql_error()."<br>";
      return(1);
    } else {
      if ($strKeyField != "") $this->strMessage .= "<span class=\"greenmessage\">".gettext('Entry')." ".$strKey."::".$arrImportData[$strKeyField]['value']." ".gettext('inside')." ".$strTable." ".gettext('successfully inserted')."</span><br>";
      if ($strKeyField == "") $this->strMessage .= "<span class=\"greenmessage\">".gettext('Entry')." ".$strTemp1."::".$strTemp2." ".gettext('inside')." ".$strTable." ".gettext('successfully inserted')."</span><br>";
      // Datensatz ID festlegen
      if ($intExists != 0) {
        $intDatasetId = $intExists;
      } else {
        $intDatasetId = $this->myDBClass->intLastId;
      }
      // Müssen noch Relationen eingetragen werden?
      if ($intInsertRelations == 1) {
        foreach ($arrImportRelations AS $elem) {
          foreach ($arrRelations AS $reldata) {
            if ($reldata['fieldName'] == $elem['key']) {
              if ($elem['key'] == "check_command") {
                $this->writeRelation_5($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata);
              } else if ($reldata['type'] == 1) {
                $this->writeRelation_1($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata);
              } else if ($reldata['type'] == 2) {
                $this->writeRelation_2($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata);
              } else if ($reldata['type'] == 3) {
                $this->writeRelation_3($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata);
              } else if ($reldata['type'] == 4) {
                $this->writeRelation_4($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata);
              } else if ($reldata['type'] == 5) {
                $this->writeRelation_6($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata);
              }
            }
          }
        }
      }
      // Müssen noch freie Variabeln eingetragen werden?
      if ($intInsertVariables == 1) {
        if ($strTable == "tbl_timeperiod") {
          // Alte Einträge löschen
          $strSQL   = "DELETE FROM `tbl_timedefinition` WHERE `tipId` = $intDatasetId";
          $booResult  = $this->myDBClass->insertData($strSQL);
          foreach ($arrFreeVariables AS $elem) {
            $strSQL = "INSERT INTO `tbl_timedefinition` SET `tipId` = $intDatasetId,
                   `definition` = '".addslashes($elem['key'])."', `range` = '".addslashes($elem['value'])."'";
            $booResult  = $this->myDBClass->insertData($strSQL);
          }
        } else {
          foreach ($arrFreeVariables AS $elem) {
            $this->writeRelation_4($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata);
          }
        }
      }
      return(0);
    }
  }
  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Datenrelation eintragen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Fügt eine Datenverknüpfung vom Typ 1 ein (1:1)
  //
  //  Übergabeparameter:  $strKey     Datenfeld
  //                      $strValue   Datenwert
  //            $intDataId    Daten ID
  //            $strDataTable Datentabelle (Master)
  //            $arrRelData   Verknüfungsdaten
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function writeRelation_1($strKey,$strValue,$intDataId,$strDataTable,$arrRelData) {
    // Variabeln definieren
    $intSlaveId = 0;
    if (strtolower(trim($strValue)) == "null") {
      // Felddaten in Haupttabelle updaten
      $strSQL   = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = -1 WHERE `id` = ".$intDataId;
      $booResult  = $this->myDBClass->insertData($strSQL);
    } else {
      // Datenwert zerlegen
      $arrValues = explode(",",$strValue);
      // Datenwerte abarbeiten
      foreach ($arrValues AS $elem) {
        $strWhere = "";
        $strLink  = "";
        if (($strDataTable == "tbl_serviceextinfo") && (substr_count($strKey,"service") != 0)) {
          $strLink  = "LEFT JOIN `tbl_lnkServiceToHost` on `id`=`idMaster`";
          $strWhere = "AND `idSlave` IN (".$this->strList1.")";
        }
        // Feststellen, ob der Eintrag bereits existiert
        $strSQL = "SELECT `id` FROM `".$arrRelData['tableName']."` $strLink WHERE `".$arrRelData['target']."` = '".$elem."' $strWhere AND `config_id`=".$this->intDomainId;
        $strId  = $this->myDBClass->getFieldData($strSQL);
        //echo $strSQL."<br>";
        if ($strId != "") {
          $intSlaveId = $strId+0;
        }
        if ($intSlaveId == 0) {
          // Temporärer Eintrag in die Zieltabelle vornehmen
          $strSQL = "INSERT INTO `".$arrRelData['tableName']."` SET `".$arrRelData['target']."` = '".$elem."',
                 `config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
          $booResult  = $this->myDBClass->insertData($strSQL);
          $intSlaveId = $this->myDBClass->intLastId;
        }
        // Felddaten in Haupttabelle updaten
        $strSQL   = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = ".$intSlaveId." WHERE `id` = ".$intDataId;
        $booResult  = $this->myDBClass->insertData($strSQL);
        if ($strDataTable == "tbl_serviceextinfo") {
          $this->strList1 = $intSlaveId;
        }
      }
    }
  }

  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Datenrelation eintragen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Fügt eine Datenverknüpfung vom Typ 2 ein (1:n)
  //
  //  Übergabeparameter:  $strKey     Datenfeld
  //                      $strValue   Datenwert
  //            $intDataId    Daten ID
  //            $strDataTable Datentabelle (Master)
  //            $arrRelData   Verknüfungsdaten
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function writeRelation_2($strKey,$strValue,$intDataId,$strDataTable,$arrRelData) {
    // Feststellen ob ein tploptions Feld existiert
    $strSQL   = "SELECT * FROM `".$strDataTable."` WHERE `id` = ".$intDataId;
    $booResult  = $this->myDBClass->getSingleDataset($strSQL,$arrDataset);
    if (isset($arrDataset[$arrRelData['fieldName']."_tploptions"])) {
      $intTplOption = 1;
    } else {
      $intTplOption = 0;
    }
    // Linktabelle löschen
    $strSQL   = "DELETE FROM `".$arrRelData['linktable']."` WHERE `idMaster` = ".$intDataId;
    $booResult  = $this->myDBClass->insertData($strSQL);
    // Variabeln definieren
    $intSlaveId = 0;
    if (strtolower(trim($strValue)) == "null") {
      // Felddaten in Haupttabelle updaten
      if ($intTplOption == 1) {
        $strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 0,
              `".$arrRelData['fieldName']."_tploptions` = 1  WHERE `id` = ".$intDataId;
      } else {
        $strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 0 WHERE `id` = ".$intDataId;
      }
      $booResult  = $this->myDBClass->insertData($strSQL);
    } else {
      if (substr(trim($strValue),0,1) == "+") {
        $intOption = 0;
        $strValue = str_replace("+","",$strValue);
      } else {
        $intOption = 2;
      }
      // Datenwert zerlegen
      $arrValues = explode(",",$strValue);
      // Datenwerte abarbeiten;
      foreach ($arrValues AS $elem) {
        $strWhere = "";
        $strLink  = "";
        if ((($strDataTable == "tbl_servicedependency") || ($strDataTable == "tbl_serviceescalation")) &&
           (substr_count($strKey,"service") != 0)) {
          if (substr_count($strKey,"depend") != 0) {
            $strLink  = "LEFT JOIN `tbl_lnkServiceToHost` on `id`=`idMaster`";
            $strWhere = "AND `idSlave` IN (".substr($this->strList1,0,-1).")";
          } else {
            $strLink  = "LEFT JOIN `tbl_lnkServiceToHost` on `id`=`idMaster`";
            $strWhere = "AND `idSlave` IN (".substr($this->strList2,0,-1).")";
          }
        }
        // Feststellen, ob der Eintrag bereits existiert
        $strSQL = "SELECT `id` FROM `".$arrRelData['tableName']."` $strLink WHERE `".$arrRelData['target']."` = '".$elem."'
               $strWhere AND `config_id`=".$this->intDomainId;
        $strId  = $this->myDBClass->getFieldData($strSQL);
        if ($strId != "") {
          $intSlaveId = $strId+0;
        } else {
		  $intSlaveId = 0;
		}
        if (($intSlaveId == 0) && ($elem != "*")) {
          // Temporärer Eintrag in die Zieltabelle vornehmen
          $strSQL = "INSERT INTO `".$arrRelData['tableName']."` SET `".$arrRelData['target']."` = '".$elem."',
                 `config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
          $booResult  = $this->myDBClass->insertData($strSQL);
          $intSlaveId = $this->myDBClass->intLastId;
        }
        // Verknüpfung Eintragen
        $strSQL   = "INSERT INTO `".$arrRelData['linktable']."` SET `idMaster` = ".$intDataId.", `idSlave` = ".$intSlaveId;
		$booResult  = $this->myDBClass->insertData($strSQL);
        // Werte in Werteliste zwischenspeichern
        if (($strDataTable == "tbl_servicedependency") || ($strDataTable == "tbl_serviceescalation")) {
          $strTemp = "";
          if (($strKey == "dependent_host_name") || ($strKey == "host_name")) {
            $strTemp .= $intSlaveId.",";
          } else if (($strKey == "dependent_hostgroup_name") || ($strKey == "hostgroup_name")) {
            $strSQL = "SELECT DISTINCT `id` FROM `tbl_host`
                   LEFT JOIN `tbl_lnkHostToHostgroup` ON `id` = `tbl_lnkHostToHostgroup`.`idMaster`
                   LEFT JOIN `tbl_lnkHostgroupToHost` ON `id` = `tbl_lnkHostgroupToHost`.`idSlave`
                   WHERE (`tbl_lnkHostgroupToHost`.`idMaster` = $intSlaveId
                    OR `tbl_lnkHostToHostgroup`.`idSlave` = $intSlaveId)
                   AND `active`='1'
                   AND `config_id`=".$this->intDomainId;
            $booReturn = $this->myDBClass->getDataArray($strSQL,$arrDataHostgroups,$intDCHostgroups);
            $arrDataHg2 = "";
            foreach ($arrDataHostgroups AS $elem) {
              $strTemp .= $elem['id'].",";
            }
          }
          if (substr_count($strKey,"dependent") != 0) {
            $this->strList1 .= $strTemp;
          } else {
            $this->strList2 .= $strTemp;
          }
        }
        // Felddaten in Haupttabelle updaten
        if ($strValue == "*") {
          $intRelValue = 2;
        } else {
          $intRelValue = 1;
        }
        if ($intTplOption == 1) {
          $strSQL   = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = $intRelValue,
                  `".$arrRelData['fieldName']."_tploptions` = ".$intOption." WHERE `id` = ".$intDataId;
        } else {
          $strSQL   = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = $intRelValue WHERE `id` = ".$intDataId;
        }
        $booResult  = $this->myDBClass->insertData($strSQL);
      }
    }
  }

  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Datenrelation eintragen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Fügt eine Datenverknüpfung vom Typ 3 ein Templates
  //
  //  Übergabeparameter:  $strKey     Datenfeld
  //                      $strValue   Datenwert
  //            $intDataId    Daten ID
  //            $strDataTable Datentabelle (Master)
  //            $arrRelData   Verknüfungsdaten
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function writeRelation_3($strKey,$strValue,$intDataId,$strDataTable,$arrRelData) {
    // Variabeln definieren
    $intSlaveId = 0;
    $intTable   = 0;
    $intSortNr  = 1;
    if (strtolower(trim($strValue)) == "null") {
      // Felddaten in Haupttabelle updaten
      $strSQL   = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 0,
              `".$arrRelData['fieldName']."_tploptions` = 1  WHERE `id` = ".$intDataId;
      $booResult  = $this->myDBClass->insertData($strSQL);
    } else {
      if (substr(trim($strValue),0,1) == "+") {
        $intOption = 0;
        $strValue = str_replace("+","",$strValue);
      } else {
        $intOption = 2;
      }
      // Datenwert zerlegen
      $arrValues = explode(",",$strValue);
	  // Remove old relations
      $strSQL   	= "DELETE FROM `".$arrRelData['linktable']."` WHERE `idMaster` = ".$intDataId;
      $booResult  = $this->myDBClass->insertData($strSQL);
      // Datenwerte abarbeiten
      foreach ($arrValues AS $elem) {
        // Feststellen, ob das Template bereits existiert (Tabelle 1)
        $strSQL = "SELECT `id` FROM `".$arrRelData['tableName1']."` WHERE `".$arrRelData['target1']."` = '".$elem."' AND `config_id`=".$this->intDomainId;
        $strId  = $this->myDBClass->getFieldData($strSQL);
        if ($strId != "") {
          $intSlaveId = $strId+0;
          $intTable = 1;
        }
        if ($intSlaveId == 0) {
          // Feststellen, ob das Template bereits existiert (Tabelle 2)
          $strSQL = "SELECT `id` FROM `".$arrRelData['tableName2']."` WHERE `".$arrRelData['target2']."` = '".$elem."' AND `config_id`=".$this->intDomainId;
          $strId  = $this->myDBClass->getFieldData($strSQL);
          if ($strId != "") {
            $intSlaveId = $strId+0;
            $intTable   = 2;
          }
        }
        if ($intSlaveId == 0) {
          // Temporärer Eintrag in die Templatetabelle vornehmen
          $strSQL = "INSERT INTO `".$arrRelData['tableName1']."` SET `".$arrRelData['target1']."` = '".$elem."',
                 `config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
          $booResult  = $this->myDBClass->insertData($strSQL);
          $intSlaveId = $this->myDBClass->intLastId;
          $intTable   = 1;
        }
        // Verknüpfung Eintragen
        $strSQL   = "INSERT INTO `".$arrRelData['linktable']."` SET `idMaster` = ".$intDataId.", `idSlave` = ".$intSlaveId.",
                 `idSort` = ".$intSortNr.", `idTable` = ".$intTable;
        $booResult  = $this->myDBClass->insertData($strSQL);
        $intSortNr++;
        // Felddaten in Haupttabelle updaten
        $strSQL   = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 1,
                `".$arrRelData['fieldName']."_tploptions` = ".$intOption." WHERE `id` = ".$intDataId;
        $booResult  = $this->myDBClass->insertData($strSQL);
      }
    }
  }

  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Datenrelation eintragen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Fügt eine Datenverknüpfung vom Typ 4 ein (freie Variabeln)
  //
  //  Übergabeparameter:  $strKey     Datenfeld
  //                      $strValue   Datenwert
  //            $intDataId    Daten ID
  //            $strDataTable Datentabelle (Master)
  //            $arrRelData   Verknüfungsdaten
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function writeRelation_4($strKey,$strValue,$intDataId,$strDataTable,$arrRelData) {
    // Werte in die Variabeltabelle eintragen
    $strSQL   = "INSERT INTO `tbl_variabledefinition` SET `name` = '$strKey', `value` = '$strValue', `last_modified`=now()";
    $booResult  = $this->myDBClass->insertData($strSQL);
    $intSlaveId = $this->myDBClass->intLastId;
    // Werte in die Verknüpfungstabelle eintragen
    $strSQL   = "INSERT INTO `".$arrRelData['linktable']."` SET `idMaster` = ".$intDataId.", `idSlave` = ".$intSlaveId;
    $booResult  = $this->myDBClass->insertData($strSQL);
    // Felddaten in Haupttabelle updaten
    $strSQL   = "UPDATE `".$strDataTable."` SET `use_variables` = 1 WHERE `id` = ".$intDataId;
    $booResult  = $this->myDBClass->insertData($strSQL);
  }
  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Datenrelation eintragen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Fügt eine Datenverknüpfung vom Typ 5 ein (1:1) check_command
  //
  //  Übergabeparameter:  $strKey     Datenfeld
  //                      $strValue   Datenwert
  //            $intDataId    Daten ID
  //            $strDataTable Datentabelle (Master)
  //            $arrRelData   Verknüfungsdaten
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function writeRelation_5($strKey,$strValue,$intDataId,$strDataTable,$arrRelData) {
    // Datenwerte extrahieren
    $arrCommand = explode("!",$strValue);
    $strValue   = $arrCommand[0];
    // Variabeln definieren
    $intSlaveId = 0;
    if (strtolower(trim($strValue)) == "null") {
      // Felddaten in Haupttabelle updaten
      $strSQL   = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = -1 WHERE `id` = ".$intDataId;
      $booResult  = $this->myDBClass->insertData($strSQL);
    } else {
      // Datenwert zerlegen
      $arrValues = explode(",",$strValue);
      // Datenwerte abarbeiten
      foreach ($arrValues AS $elem) {
        // Feststellen, ob der Eintrag bereits existiert
        $strSQL = "SELECT `id` FROM `".$arrRelData['tableName']."` WHERE `".$arrRelData['target']."` = '".$elem."' AND `config_id`=".$this->intDomainId;
        $strId  = $this->myDBClass->getFieldData($strSQL);
        if ($strId != "") {
          $intSlaveId = $strId+0;
        }
        if ($intSlaveId == 0) {
          // Temporärer Eintrag in die Zieltabelle vornehmen
          $strSQL = "INSERT INTO `".$arrRelData['tableName']."` SET `".$arrRelData['target']."` = '".$elem."',
                 `config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
          $booResult  = $this->myDBClass->insertData($strSQL);
          $intSlaveId = $this->myDBClass->intLastId;
        }
        // Felddaten in Haupttabelle updaten
        $arrCommand[0] = $intSlaveId;
        $strValue     = implode("!",$arrCommand);
        $strSQL   = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = '".mysql_real_escape_string($strValue)."' WHERE `id` = ".$intDataId;
        $booResult  = $this->myDBClass->insertData($strSQL);
      }
    }
  }
  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Datenrelation eintragen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Fügt eine Datenverknüpfung vom Typ 5 ein (1:n:n) (Servicegruppen)
  //
  //  Übergabeparameter:  $strKey     Datenfeld
  //                      $strValue   Datenwert
  //            $intDataId    Daten ID
  //            $strDataTable Datentabelle (Master)
  //            $arrRelData   Verknüfungsdaten
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function writeRelation_6($strKey,$strValue,$intDataId,$strDataTable,$arrRelData) {
    // Variabeln definieren
    $intSlaveId  = 0;
    $intSlaveIdS = 0;
    $intSlaveIdH = 0;
    // Datenwert zerlegen
    $arrValues = explode(",",$strValue);
    // Linktabelle löschen
    $strSQL   = "DELETE FROM `".$arrRelData['linktable']."` WHERE `idMaster` = ".$intDataId;
    $booResult  = $this->myDBClass->insertData($strSQL);
    // Prüfen, ob die Anzahl Elemente richtig ist
    if (count($arrValues) % 2 != 0) {
      $this->strMessage .= gettext("Error: wrong number of arguments - cannot import service group members")."<br>";
    } else {
      // Datenwerte abarbeiten
      $intCounter = 1;
      foreach ($arrValues AS $elem) {
        if ($intCounter % 2 == 0) {
          // Feststellen, ob der Hosteintrag bereits existiert
          $strSQL = "SELECT `id` FROM `".$arrRelData['tableName1']."` WHERE `".$arrRelData['target1']."` = '".$strValue."' AND `config_id`=".$this->intDomainId;
          $strId  = $this->myDBClass->getFieldData($strSQL);
          if ($strId != "") {
            $intSlaveIdH = $strId+0;
          }
          if ($intSlaveIdH == 0) {
            // Temporärer Eintrag in die Zieltabelle vornehmen
            $strSQL = "INSERT INTO `".$arrRelData['tableName1']."` SET `".$arrRelData['target1']."` = '".$strValue."',
                   `config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
            $booResult   = $this->myDBClass->insertData($strSQL);
            $intSlaveIdH = $this->myDBClass->intLastId;
          }
          // Feststellen, ob der Serviceeintrag bereits existiert
          $strSQL = "SELECT `id` FROM `".$arrRelData['tableName2']."`
                 LEFT JOIN `tbl_lnkServiceToHost` ON `id` = `idMaster`
                 WHERE `".$arrRelData['target2']."` = '".$elem."' AND `idSlave` = ".$intSlaveIdH." AND `config_id`=".$this->intDomainId;
          $strId  = $this->myDBClass->getFieldData($strSQL);
          if ($strId != "") {
            $intSlaveIdS = $strId+0;
          }
          if ($intSlaveIdS == 0) {
            // Temporärer Eintrag in die Zieltabelle vornehmen
            $strSQL = "INSERT INTO `".$arrRelData['tableName2']."` SET `".$arrRelData['target2']."` = '".$strValue."',
                   `config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
            $booResult   = $this->myDBClass->insertData($strSQL);
            $intSlaveIdS = $this->myDBClass->intLastId;
          }
          // Verknüpfung Eintragen
          $strSQL   = "INSERT INTO `".$arrRelData['linktable']."` SET `idMaster` = ".$intDataId.", `idSlaveH` = ".$intSlaveIdH.", `idSlaveS` = ".$intSlaveIdS;
          $booResult  = $this->myDBClass->insertData($strSQL);
          // Felddaten in Haupttabelle updaten
          $strSQL   = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 1 WHERE `id` = ".$intDataId;
          $booResult  = $this->myDBClass->insertData($strSQL);
        } else {
          $strValue = $elem;
        }
        $intCounter++;
      }
    }
  }
  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Template integrieren
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Version:  2.00.00 (Internal)
  //  Datum:    12.03.2007
  //
  //  Integriert die Daten eines bestimmten Templates in das Importdatenarrays
  //
  //  Übergabeparameter:  $strFileName  Importdateiname
  //                      $strTemplate  Name des Templates
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //  Rückgabewert:   Datenarray mit hinzugefügten Templatevariabeln
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function insertTemplate($strFileName,$strTemplate,&$arrData) {
    // Variabeln deklarieren
    $intBlock    = 0;
    $intCheck    = 0;
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