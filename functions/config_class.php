<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project   : NagiosQL
// Component : Configuration Class
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
// Klasse: Konfigurationsklasse
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Enthält sämtliche Funktionen, zum Erstellen der Nagioskonfiguration nötig sind
//
// Name: nagconfig
//
// Klassenvariabeln:
// -----------------
// $arrSettings:  Mehrdimensionales Array mit den globalen Konfigurationseinstellungen
//
// Externe Funktionen
// ------------------
//
//
///////////////////////////////////////////////////////////////////////////////////////////////
class nagconfig {
  // Klassenvariabeln deklarieren
  var $arrSettings;       // Wird im Klassenkonstruktor gefüllt
  var $intDomainId = 0;     // Wird im Klassenkonstruktor gefüllt
  var $strDBMessage = "";     // Wird intern verwendet


    ///////////////////////////////////////////////////////////////////////////////////////////
  //  Klassenkonstruktor
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Ttigkeiten bei Klasseninitialisierung
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function nagconfig() {
    // Globale Einstellungen einlesen
    $this->arrSettings = $_SESSION['SETS'];
    if (isset($_SESSION['domain'])) $this->intDomainId = $_SESSION['domain'];
  }
  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Letzte Datentabellenänderung und letzte Konfigurationsdateiänderung
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Ermittelt die Zeitpunkte der letzten Datentabellenänderung sowie der letzten Änderung an
  //  der Konfigurationsdatei
  //
  //  Übergabeparameter:  $strTableName   Datentabellenname
  //  ------------------
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //
  //  Rückgabewerte:    $strTimeTable   Datum der letzten Datentabellenänderung
  //            $strTimeFile    Datum der letzten Konfigurationsdateiänderung
  //            $strCheckConfig   Informationsstring, falls Datei älter als Tabelle
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function lastModified($strTableName,&$strTimeTable,&$strTimeFile,&$strCheckConfig) {
    // Konfigurationsdateinamen entsprechend dem Tabellennamen festlegen
    switch($strTableName) {
      case "tbl_timeperiod":      $strFile = "timeperiods.cfg"; break;
      case "tbl_command":       $strFile = "commands.cfg"; break;
      case "tbl_contact":       $strFile = "contacts.cfg"; break;
      case "tbl_contacttemplate":   $strFile = "contacttemplates.cfg"; break;
      case "tbl_contactgroup":    $strFile = "contactgroups.cfg"; break;
      case "tbl_hosttemplate":    $strFile = "hosttemplates.cfg"; break;
      case "tbl_servicetemplate":   $strFile = "servicetemplates.cfg"; break;
      case "tbl_hostgroup":     $strFile = "hostgroups.cfg"; break;
      case "tbl_servicegroup":    $strFile = "servicegroups.cfg"; break;
      case "tbl_servicedependency": $strFile = "servicedependencies.cfg"; break;
      case "tbl_hostdependency":    $strFile = "hostdependencies.cfg"; break;
      case "tbl_serviceescalation": $strFile = "serviceescalations.cfg"; break;
      case "tbl_hostescalation":    $strFile = "hostescalations.cfg"; break;
      case "tbl_hostextinfo":     $strFile = "hostextinfo.cfg"; break;
      case "tbl_serviceextinfo":    $strFile = "serviceextinfo.cfg"; break;
    }
    // Variabeln definieren
    $strCheckConfig = "";
    $strTimeTable   = "unknown";
    $strTimeFile  = "unknown";
    // Statuscache loeschen und Domänen-Id neu einlesen
    clearstatcache();
    if (isset($_SESSION['domain'])) $this->intDomainId = $_SESSION['domain'];
    // Letzte Änderung an der Datentabelle auslesen
    $strSQL = "SELECT `last_modified` FROM `".$strTableName."` WHERE `config_id`=".$this->intDomainId." ORDER BY `last_modified` DESC LIMIT 1";
    $booReturn = $this->myDBClass->getSingleDataset($strSQL,$arrDataset);
    if (($booReturn == true) && isset($arrDataset['last_modified'])) {
      $strTimeTable = $arrDataset['last_modified'];
      // Konfigurationsdaten holen
      $booReturn = $this->getConfigData("basedir",$strBaseDir);
      $booReturn = $this->getConfigData("method",$strMethod);
      // Letzte Änderung an der Konfigurationsdatei auslesen
      if (($strMethod == 1) && (file_exists($strBaseDir."/".$strFile))) {
        $intFileStamp = filemtime($strBaseDir."/".$strFile);
        $strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
        // Falls Datei älter, den entsprechenden String zurückgeben
        if (strtotime($strTimeTable) > $intFileStamp) $strCheckConfig = gettext('Warning: configuration file is out of date!');
        return(0);
      } else if ($strMethod == 2) {
        // Set up basic connection
        $booReturn    = $this->getConfigData("server",$strServer);
        $conn_id    = ftp_connect($strServer);
        // Login with username and password
        $booReturn    = $this->getConfigData("user",$strUser);
        $booReturn    = $this->getConfigData("password",$strPasswd);
        $login_result   = ftp_login($conn_id, $strUser, $strPasswd);
        // Check connection
        if ((!$conn_id) || (!$login_result)) {
          return(1);
        } else {
          $intFileStamp = ftp_mdtm($conn_id, $strBaseDir."/".$strFile);
          if ($intFileStamp != -1) $strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
          ftp_close($conn_id);
          if ((strtotime($strTimeTable) > $intFileStamp) && ($intFileStamp != -1)) $strCheckConfig = gettext('Warning: configuration file is out of date!');
          return(0);
          }
      }
    }
    return(1);
  }
    ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Letzte Datensatzänderung und letzte Konfigurationsdateiänderung
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Ermittelt die Zeitpunkte der letzten Datensatzänderung sowie der letzten Änderung an
  //  der Konfigurationsdatei
  //
  //  Übergabeparameter:  $strConfigname  Name der Konfiguration
  //  ------------------  $strId      Datensatz ID
  //            $strType    Datentyp ("host" oder "service")
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //  Rückgabewerte:    $strTime    Datum der letzten Datensatzänderung
  //            $strTimeFile  Datum der letzten Konfigurationsdateiänderung
  //            $intOlder     0, falls Datei älter - 1, falls aktuell
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function lastModifiedDir($strConfigname,$strId,$strType,&$strTime,&$strTimeFile,&$intOlder) {
    // Filename zusammenstellen
    $strFile = $strConfigname.".cfg";
    // Variabeln definieren
    $intCheck     = 0;
    // Statuscache löschen
    clearstatcache();
    // Letzte Änderung an der Datentabelle auslesen
    if ($strType == "host") {
      $strTime = $this->myDBClass->getFieldData("SELECT DATE_FORMAT(`last_modified`,'%Y-%m-%d %H:%i:%s')
                             FROM `tbl_host` WHERE `id`=".$strId);
      $booReturn = $this->getConfigData("hostconfig",$strBaseDir);
      if ($strTime != false) $intCheck++;
    } else if ($strType == "service") {
      $strTime = $this->myDBClass->getFieldData("SELECT DATE_FORMAT(`last_modified`,'%Y-%m-%d %H:%i:%s')
                             FROM `tbl_service` WHERE `id`=".$strId);
      $booReturn = $this->getConfigData("serviceconfig",$strBaseDir);
      if ($strTime != false) $intCheck++;
    } else {
      $strTime      = "undefined";
      $intOlder     = 1;
    }

    // Letzte Änderung an der Konfigurationsdatei auslesen
    $booReturn = $this->getConfigData("method",$strMethod);
    // Letzte Änderung an der Konfigurationsdatei auslesen
    if (($strMethod == 1) && (file_exists($strBaseDir."/".$strFile))) {
      $intFileStamp = filemtime($strBaseDir."/".$strFile);
      $strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
      $intCheck++;
    } else if ($strMethod == 2) {
      // Set up basic connection
      $booReturn    = $this->getConfigData("server",$strServer);
      $conn_id    = ftp_connect($strServer);
      // Login with username and password
      $booReturn    = $this->getConfigData("user",$strUser);
      $booReturn    = $this->getConfigData("password",$strPasswd);
      $login_result   = ftp_login($conn_id, $strUser, $strPasswd);
      // Check connection
      if ((!$conn_id) || (!$login_result)) {
        return(1);
      } else {
        $intFileStamp = ftp_mdtm($conn_id, $strBaseDir."/".$strFile);
        if ($intFileStamp != -1) $strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
        ftp_close($conn_id);
        $intCheck++;
      }
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
  //  Funktion: Konfigurationsdaten holen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Ermittelt die Konfigurationseinstellungen anhand der Domäne und den Einstellungen
  //  die seit Version 3.0 in der Datenbank gespeichert sind.
  //
  //  Übergabeparameter:  $strConfigItem    KonfigurationsItem (DB Spaltenname)
  //  ------------------
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //
  //  Rückgabewerte:    $strValue     Konfigurationswert
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function getConfigData($strConfigItem,&$strValue) {
    $strSQL   = "SELECT `".$strConfigItem."` FROM `tbl_domain` WHERE `id` = ".$_SESSION['domain'];
    $strValue = $this->myDBClass->getFieldData($strSQL);
    if ($strValue != "" ) return(0);
    return(1);

  }
  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Überprüft spezielle Templateeinstellungen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Ermittelt aufgrund der Templateoptionen notwendige Zusatzeinstellungen zum
  //  Konfigurationswert
  //
  //  Übergabeparameter:  $strValue   Unveränderter Konfigurationswert
  //  ------------------  $strKeyField  Schlüsselfeldname, der die Optionen enthält
  //            $strTable   Tabellenname
  //            $intId      Datensatz ID
  //
  //  Rückgabewert    $intSkip    Skipwert
  //
  //  Returnwert:     Veränderter Konfigurationswert
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function checkTpl($strValue,$strKeyField,$strTable,$intId,&$intSkip) {
    $strSQL   = "SELECT `".$strKeyField."` FROM `".$strTable."` WHERE `id` = $intId";
    $intValue = $this->myDBClass->getFieldData($strSQL);
    if ($intValue == 0) return("+".$strValue);
    if ($intValue == 1) {
      $intSkip = 0;
      return("null");
    }
    return($strValue);

  }
  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Verschieben einer Konfigurationsdatei in das backup Verzeichnis
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Verschiebt eine existierende Konfigurationsdatei in das Backupverzeichnis und löscht
  //  die Originaldatei
  //
  //  Übergabeparameter:  $strType    Typ der Konfigurationsdatei
  //  ------------------  $strName    Name der Konfigurationsdatei
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function moveFile($strType,$strName) {
    // Verzeichnisse ermitteln
    switch ($strType) {
      case "host":    $this->getConfigData("hostconfig",$strConfigDir);
                $this->getConfigData("hostbackup",$strBackupDir);
                break;
      case "service":   $this->getConfigData("serviceconfig",$strConfigDir);
                $this->getConfigData("servicebackup",$strBackupDir);
                break;
      case "basic":   $this->getConfigData("basedir",$strConfigDir);
                $this->getConfigData("backupdir",$strBackupDir);
                break;
      case "nagiosbasic": $this->getConfigData("nagiosbasedir",$strConfigDir);
                $this->getConfigData("backupdir",$strBackupDir);
                break;
      default:      return(1);
    }
    // Methode ermitteln
    $this->getConfigData("method",$strMethod);
    if ($strMethod == 1) {
      // Konfigurationsdatei sichern
      if (file_exists($strConfigDir."/".$strName) && is_writable($strBackupDir) && is_writable($strConfigDir)) {
        $strOldDate = date("YmdHis",mktime());
        copy($strConfigDir."/".$strName,$strBackupDir."/".$strName."_old_".$strOldDate);
        unlink($strConfigDir."/".$strName);
      } else if (!is_writable($strBackupDir)) {
        $this->strDBMessage = gettext('Cannot backup and delete the old configuration file (check the permissions)!');
        return(1);
      }
    } else if ($strMethod == 2) {
      // Set up basic connection
      $booReturn    = $this->getConfigData("server",$strServer);
      $conn_id    = ftp_connect($strServer);
      // Login with username and password
      $booReturn    = $this->getConfigData("user",$strUser);
      $booReturn    = $this->getConfigData("password",$strPasswd);
      $login_result   = ftp_login($conn_id, $strUser, $strPasswd);
      // Check connection
      if ((!$conn_id) || (!$login_result)) {
        $this->myDataClass->writeLog(gettext('Configuration backup failed (FTP connection failed):')." ".$strFile);
        $this->strDBMessage = gettext('Cannot backup and delete the old configuration file (FTP connection failed)!');
        return(1);
      } else {
        // Alte Konfigurationsdatei sichern
        $intFileStamp = ftp_mdtm($conn_id, $strConfigDir."/".$strName);
        if ($intFileStamp > -1) {
          $strOldDate = date("YmdHis",mktime());
          $intReturn  = ftp_rename($conn_id,$strConfigDir."/".$strName,$strBackupDir."/".$strName."_old_".$strOldDate);
          if (!$intReturn) {
            $this->strDBMessage = gettext('Cannot backup the old configuration file because the permissions are wrong (remote FTP)!');
          }
        }
      }
    }
    return(0);
  }
  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Löscht eine Konfigurationsdatei aus dem backup Verzeichnis
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Verschiebt eine existierende Konfigurationsdatei in das Backupverzeichnis und löscht
  //  die Originaldatei
  //
  //  Übergabeparameter:  $strType    Typ der Konfigurationsdatei
  //  ------------------  $strName    Name der Konfigurationsdatei
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function removeFile($strName) {
    // Methode ermitteln
    $this->getConfigData("method",$strMethod);
    if ($strMethod == 1) {
      // Konfigurationsdatei sichern
      if (file_exists($strName)) {
        unlink($strName);
      } else {
        $this->strDBMessage = gettext('Cannot delete the file (check the permissions)!');
        return(1);
      }
    } else if ($strMethod == 2) {
      // Set up basic connection
      $booReturn    = $this->getConfigData("server",$strServer);
      $conn_id    = ftp_connect($strServer);
      // Login with username and password
      $booReturn    = $this->getConfigData("user",$strUser);
      $booReturn    = $this->getConfigData("password",$strPasswd);
      $login_result   = ftp_login($conn_id, $strUser, $strPasswd);
      // Check connection
      if ((!$conn_id) || (!$login_result)) {
        $this->myDataClass->writeLog(gettext('File deletion failed (FTP connection failed):')." ".$strFile);
        $this->strDBMessage = gettext('Cannot delete a file (FTP connection failed)!');
        return(1);
      } else {
        // Alte Konfigurationsdatei sichern
        $intFileStamp = ftp_mdtm($conn_id, $strName);
        if ($intFileStamp > -1) {
          $intReturn  = ftp_delete($conn_id,$strName);
          if (!$intReturn) {
            $this->strDBMessage = gettext('Cannot delete file because the permissions are wrong (remote FTP)!');
          }
        }
      }
    }
    return(0);
  }

  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Kopiert eine Datei
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Kopiert eine Datei
  //
  //
  //  Übergabeparameter:  $strFileRemote  Name der Datei remote
  //  ------------------  $strFileLokal Name der Datei lokal
  //            $strType    Typ der Datei (basic/host/Service)
  //            $intType    0 = vom Remotesystem holen,
  //                    1 = zum Remotesystem kopieren
  //            $intBackup    1 = Backup der Remotedatei vornehmen
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function configCopy($strFileRemote,$strFileLokal,$strType,$intType,$intBackup=0) {
    // Verzeichnisse ermitteln
    switch ($strType) {
      case "host":  $this->getConfigData("hostconfig",$strConfigDir);
              break;
      case "service": $this->getConfigData("serviceconfig",$strConfigDir);
              break;
      case "basic": $this->getConfigData("basedir",$strConfigDir);
              break;
      default:    return(1);
    }
    // Methode ermitteln
    $this->getConfigData("method",$strMethod);
    if ($strMethod == 2) {
      if ($intBackup == 1) $this->moveFile($strType,$strFileRemote);
      // Set up basic connection
      $booReturn    = $this->getConfigData("server",$strServer);
      $conn_id    = ftp_connect($strServer);
      // Login with username and password
      $booReturn    = $this->getConfigData("user",$strUser);
      $booReturn    = $this->getConfigData("password",$strPasswd);
      $login_result   = ftp_login($conn_id, $strUser, $strPasswd);
      // Check connection
      if ((!$conn_id) || (!$login_result)) {
        $this->myDataClass->writeLog(gettext('Reading remote configuration failed (FTP connection failed):')." ".$strFileRemote);
        $this->strDBMessage = gettext('Cannot read the remote configuration file (FTP connection failed)!');
        return(1);
      } else {
        if ($intType == 0) {
          if (!ftp_get($conn_id,$this->arrSettings['path']['tempdir']."/".$strFileLokal,$strConfigDir."/".$strFileRemote,FTP_ASCII)) {
            $this->strDBMessage = gettext('Cannot get the configuration file (FTP connection failed)!');
            ftp_close($conn_id);
          }
        } else {
          if (!ftp_put($conn_id,$strConfigDir."/".$strFileRemote,$this->arrSettings['path']['tempdir']."/".$strFileLokal,FTP_ASCII)) {
            $this->strDBMessage = gettext('Cannot write the configuration file (FTP connection failed)!');
            ftp_close($conn_id);
          }
        }
      }
    }
    return(0);
  }
    ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Komplette Konfigurationsdatei schreiben
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Schreibt ein einzelnes Konfigurationsfile mit allen Datensätzen einer Tabelle oder
  //  liefert die Ausgabe als Textdatei zum Download aus.
  //
  //  Übergabeparameter:  $strTableName Tabellenname
  //  ------------------  $intMode    0 = Datei schreiben, 1 = Ausgabe für Download
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //
  //  Rckgabewert:    Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function createConfig($strTableName,$intMode=0) {
    // Variabeln entsprechend dem Tabellennamen definieren
    switch($strTableName) {
      case "tbl_timeperiod":      $strFileString  = "timeperiods";
                      $strOrderField  = "timeperiod_name";
                      break;
      case "tbl_command":       $strFileString  = "commands";
                      $strOrderField  = "command_name";
                      break;
      case "tbl_contact":       $strFileString  = "contacts";
                      $strOrderField  = "contact_name";
                      break;
      case "tbl_contacttemplate":   $strFileString  = "contacttemplates";
                      $strOrderField  = "template_name";
                      break;
      case "tbl_contactgroup":    $strFileString  = "contactgroups";
                      $strOrderField  = "contactgroup_name";
                      break;
      case "tbl_hosttemplate":    $strFileString  = "hosttemplates";
                      $strOrderField  = "template_name";
                      break;
      case "tbl_hostgroup":     $strFileString  = "hostgroups";
                      $strOrderField  = "hostgroup_name";
                      break;
      case "tbl_servicetemplate":   $strFileString  = "servicetemplates";
                      $strOrderField  = "template_name";
                      break;
      case "tbl_servicegroup":    $strFileString  = "servicegroups";
                      $strOrderField  = "servicegroup_name";
                      break;
      case "tbl_hostdependency":    $strFileString  = "hostdependencies";
                      $strOrderField  = "dependent_host_name";
                      break;
      case "tbl_hostescalation":    $strFileString  = "hostescalations";
                      $strOrderField  = "host_name`,`hostgroup_name";
                      break;
      case "tbl_hostextinfo":     $strFileString  = "hostextinfo";
                      $strOrderField  = "host_name";
                      break;
      case "tbl_servicedependency": $strFileString  = "servicedependencies";
                      $strOrderField  = "dependent_host_name";
                      break;
      case "tbl_serviceescalation": $strFileString  = "serviceescalations";
                      $strOrderField  = "host_name`,`service_description";
                      break;
      case "tbl_serviceextinfo":    $strFileString  = "serviceextinfo";
                      $strOrderField  = "host_name";
                      break;
      default:            return(1);
    }
    // SQL Abfrage festlegen und Dateinamen definieren
    $strSQL     = "SELECT * FROM `".$strTableName."`
               WHERE `active`='1' AND `config_id`=".$this->intDomainId." ORDER BY `".$strOrderField."`";
    $strFile    = $strFileString.".cfg";
    $setTemplate  = $strFileString.".tpl.dat";
    // Relationen holen
    $this->myDataClass->tableRelations($strTableName,$arrRelations);
    // Konfiguration schreiben?
    if ($intMode == 0) {
      // Konfigurationsdaten holen
      $booReturn = $this->getConfigData("basedir",$strBaseDir);
      $booReturn = $this->getConfigData("backupdir",$strBackupDir);
      $booReturn = $this->getConfigData("method",$strMethod);
      if ($strMethod == 1) {
        // Alte Konfigurationsdatei sichern
        if (file_exists($strBaseDir."/".$strFile) && is_writable($strBaseDir)) {
          $strOldDate = date("YmdHis",mktime());
          copy($strBaseDir."/".$strFile,$strBackupDir."/".$strFile."_old_".$strOldDate);
        } else if (!(is_writable($strBaseDir))) {
          $this->strDBMessage = "<span class=\"verify-critical\">".gettext('Cannot open/overwrite the configuration file (check the permissions)!')."</span>";
          return(1);
        }
        // Konfigurationsdatei öffnen
        if (is_writable($strBaseDir."/".$strFile) || (!file_exists($strBaseDir."/".$strFile))) {
          $CONFIGFILE = fopen($strBaseDir."/".$strFile,"w");
          chmod($strBaseDir."/".$strFile, 0644);
        } else {
          $this->myDataClass->writeLog("<span class=\"verify-critical\">".gettext('Configuration write failed:')."</span>"." ".$strFile);
          $this->strDBMessage = "<span class=\"verify-critical\">".gettext('Cannot open/overwrite the configuration file (check the permissions)!')."</span>";
          return(1);
        }
      } else if ($strMethod == 2) {
        // Set up basic connection
        $booReturn    = $this->getConfigData("server",$strServer);
        $conn_id    = ftp_connect($strServer);
        // Login with username and password
        $booReturn    = $this->getConfigData("user",$strUser);
        $booReturn    = $this->getConfigData("password",$strPasswd);
        $login_result   = ftp_login($conn_id, $strUser, $strPasswd);
        // Check connection
        if ((!$conn_id) || (!$login_result)) {
          $this->myDataClass->writeLog("<span class=\"verify-critical\">".gettext('Configuration write failed (FTP connection failed):')."</span>"." ".$strFile);
          $this->strDBMessage = "<span class=\"verify-critical\">".gettext('Cannot open/overwrite the configuration file (FTP connection failed)!')."</span>";
          return(1);
        } else {
          // Alte Konfigurationsdatei sichern
          $intFileStamp = ftp_mdtm($conn_id, $strBaseDir."/".$strFile);
          if ($intFileStamp > -1) {
            $strOldDate = date("YmdHis",mktime());
            $intReturn  = ftp_rename($conn_id,$strBaseDir."/".$strFile,$strBackupDir."/".$strFile."_old_".$strOldDate);
            if (!$intReturn) {
              $this->strDBMessage = "<span class=\"verify-critical\">".gettext('Cannot backup the configuration file because the permissions are wrong (remote FTP)!')."</span>";
            }
          }
          // Konfigurationsdatei öffnen
          if (is_writable($this->arrSettings['path']['tempdir']."/".$strFile) || (!file_exists($this->arrSettings['path']['tempdir']."/".$strFile))) {
            $CONFIGFILE = fopen($this->arrSettings['path']['tempdir']."/".$strFile,"w");
            chmod($this->arrSettings['path']['tempdir']."/".$strFile, 0644);
          } else {
            $this->myDataClass->writeLog("<span class=\"verify-critical\">".gettext('Configuration write failed:')."</span>"." ".$strFile);
            $this->strDBMessage = "<span class=\"verify-critical\">".gettext('Cannot open/overwrite the configuration file - check the permissions of the temp directory:')."</span>"." ".$this->arrSettings['path']['tempdir'];
            ftp_close($conn_id);
            return(1);
          }

          }
      }
    }
    // Konfigurationsvorlage laden
    $arrTplOptions = array('use_preg' => false);
    $configtp = new HTML_Template_IT($this->arrSettings['path']['physical']."/templates/files/");
    $configtp->loadTemplatefile($setTemplate, true, true);
    $configtp->setOptions($arrTplOptions);
    $configtp->setVariable("CREATE_DATE",date("Y-m-d H:i:s",mktime()));
    $this->getConfigData("version",$strVersionValue);
    if ($strVersionValue == 3) $strVersion = "Nagios 3.x config file";
    if ($strVersionValue == 2) $strVersion = "Nagios 2.9 config file";
    if ($strVersionValue == 1) $strVersion = "Nagios 2.x config file";
    $configtp->setVariable("VERSION",$strVersion);
    // Datenbank abfragen und Resultat verarbeiten
    $booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
    if ($booReturn == false) {
      $this->strDBMessage = "<span class=\"verify-critical\">".gettext('Error while selecting data from database:')."</span>"."<br>".$this->myDBClass->strDBError."<br>";
    } else if ($intDataCount != 0) {
      // Jeden Datensatz verarbeiten
      for ($i=0;$i<$intDataCount;$i++) {
        foreach($arrData[$i] AS $key => $value) {
          $intSkip = 0;
          if ($key == "id") $intDataId = $value;
          // Spezialdatenfelder überspringen
          $strSpecial = "id,config_name,active,last_modified,access_rights,config_id,template,nodelete,command_type";
          if ($strTableName == "tbl_hosttemplate") $strSpecial .= ",parents_tploptions,hostgroups_tploptions,contacts_tploptions,contact_groups_tploptions,use_template_tploptions";
          if ($strTableName == "tbl_servicetemplate") $strSpecial .= ",host_name_tploptions,hostgroup_name_tploptions,servicegroups_tploptions,contacts_tploptions,contact_groups_tploptions,use_template_tploptions";
          if ($strTableName == "tbl_contact") $strSpecial .= ",use_template_tploptions,contactgroups_tploptions,host_notification_commands_tploptions,service_notification_commands_tploptions";
          if ($strTableName == "tbl_contacttemplate") $strSpecial .= ",use_template_tploptions,contactgroups_tploptions,host_notification_commands_tploptions,service_notification_commands_tploptions";

          // Je nach Version weitere Felder überspringen
          if ($strVersionValue != 3) {
            // Timeperiod
            if ($strTableName == "tbl_timeperiod") $strSpecial .= ",exclude,name";
            // Contact
            if ($strTableName == "tbl_contact") $strSpecial .= ",host_notifications_enabled,service_notifications_enabled,can_submit_commands,retain_status_information,retain_nonstatus_information";
            // Contacttemplate
            if ($strTableName == "tbl_contacttemplate") $strSpecial .= ",host_notifications_enabled,service_notifications_enabled,can_submit_commands,retain_status_information,retain_nonstatus_information";
            // Contactgroup
            if ($strTableName == "tbl_contactgroup") $strSpecial .= ",contactgroup_members";
            // Hostgroup
            if ($strTableName == "tbl_hostgroup") $strSpecial .= ",hostgroup_members,notes,notes_url,action_url";
            // Servicegroup
            if ($strTableName == "tbl_sevicegroup") $strSpecial .= ",servicegroup_members,notes,notes_url,action_url";
            // Hostdependencies
            if ($strTableName == "tbl_hostdependency") $strSpecial .= ",dependent_hostgroup_name,hostgroup_name,dependency_period";
          }
          if ($strVersionValue == 3) {
            // Servicetemplate
            if ($strTableName == "tbl_servicetemplate") $strSpecial .= ",parallelize_check ";
          }
          if ($strVersionValue == 1) {
            $strSpecial .= "";
          }
          $arrSpecial = explode(",",$strSpecial);
          if (($value == "") || (in_array($key,$arrSpecial))) {
            continue;
          }
          // Nicht alle Konfigurationsdaten schreiben
          $strNoTwo  = "active_checks_enabled,passive_checks_enabled,obsess_over_host,check_freshness,event_handler_enabled,flap_detection_enabled,";
          $strNoTwo .= "process_perf_data,retain_status_information,retain_nonstatus_information,notifications_enabled,parallelize_check,is_volatile,";
          $strNoTwo .= "host_notifications_enabled,service_notifications_enabled,can_submit_commands,obsess_over_service";
          $booTest = 0;
          foreach(explode(",",$strNoTwo) AS $elem){
            if (($key == $elem) && ($value == "2")) $booTest = 1;
          }
          if ($booTest == 1) continue;
          // Ist das Datenfeld über eine Relation mit einem anderen Datenfeld verbunden? // TODO Realtionen
          if (is_array($arrRelations)) {
            foreach($arrRelations AS $elem) {
			  if ($elem['fieldName'] == $key) {
                // Handelt es sich um eine normale 1:n Relation?
                if (($elem['type'] == 2) && ($value == 1)) {
                  $strSQLRel = "SELECT `".$elem['tableName']."`.`".$elem['target']."` FROM `".$elem['linktable']."`
                            LEFT JOIN `".$elem['tableName']."` ON `".$elem['linktable']."`.`idSlave` = `".$elem['tableName']."`.`id`
                            WHERE `idMaster`=".$arrData[$i]['id']." AND `active`='1'
                            ORDER BY `".$elem['tableName']."`.`".$elem['target']."`";
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
                // Handelt es sich um eine normale 1:1 Relation?
                } else if ($elem['type'] == 1) {
                  if ($elem['tableName'] == "tbl_command") {
                    $arrField   = explode("!",$arrData[$i][$elem['fieldName']]);
                    $strCommand = strchr($arrData[$i][$elem['fieldName']],"!");
                    $strSQLRel  = "SELECT `".$elem['target']."` FROM `".$elem['tableName']."`
                             WHERE `id`=".$arrField[0]." AND `active`='1'";
                  } else {
                    $strSQLRel  = "SELECT `".$elem['target']."` FROM `".$elem['tableName']."`
                                 WHERE `id`=".$arrData[$i][$elem['fieldName']]." AND `active`='1'";
                  }
                  $booReturn = $this->myDBClass->getDataArray($strSQLRel,$arrDataRel,$intDataCountRel);
                  // Wurden Datensätze gefunden?
                  if ($booReturn && ($intDataCountRel != 0)) {
                    // Datenfeldwert des gefundenen Datensatzes eintragen
                    if ($elem['tableName'] == "tbl_command") {
                      $value = $arrDataRel[0][$elem['target']].$strCommand;
                    } else {
                      $value = $arrDataRel[0][$elem['target']];
                    }
                  } else {
                    $intSkip = 1;
                  }
                // Handelt es sich um eine normale 1:n Relation mit Spezialtabelle?
                } else if (($elem['type'] == 3) && ($value == 1)) {
                  $strSQLMaster   = "SELECT * FROM `".$elem['linktable']."` WHERE `idMaster` = ".$arrData[$i]['id']." ORDER BY `idSort`";
                  $booReturn    = $this->myDBClass->getDataArray($strSQLMaster,$arrDataMaster,$intDataCountMaster);
                  // Wurden Datensätze gefunden?
                  if ($intDataCountMaster != 0) {
                    // Datenfeldwerte der gefundenen Datensätze eintragen
                    $value = "";
                    foreach ($arrDataMaster AS $data) {
                      if ($data['idTable'] == 1) {
                        $strSQLName = "SELECT `".$elem['target1']."` FROM `".$elem['tableName1']."` WHERE `id` = ".$data['idSlave']." AND `active`='1'";
                      } else {
                        $strSQLName = "SELECT `".$elem['target2']."` FROM `".$elem['tableName2']."` WHERE `id` = ".$data['idSlave']." AND `active`='1'";
                      }
                      $value .= $this->myDBClass->getFieldData($strSQLName).",";
                    }
                    $value = substr($value,0,-1);
                  } else {
                    $intSkip = 1;
                  }
                // Handelt es sich um eine Spezialrrelation für freie Variabeln?
                } else if (($elem['type'] == 4) && ($value == 1)) {
                  $strSQLVar = "SELECT * FROM `tbl_variabledefinition` LEFT JOIN `".$elem['linktable']."` ON `id` = `idSlave`
                          WHERE `idMaster`=".$arrData[$i]['id']." ORDER BY `name`";
                  $booReturn = $this->myDBClass->getDataArray($strSQLVar,$arrDSVar,$intDCVar);
                  if ($intDCVar != 0) {
                    foreach ($arrDSVar AS $vardata) {
                      // Bei längeren Keys zuszliche Tabulatoren einfgen
                      $intLen  = strlen($vardata['name']);
                      $strFill = "                            ";
                      if ($intLen < 30) {
                        $strFill = substr($strFill,-(30-$intLen));
                      } else {
                        $strFill = "\t";
                      }
                      $configtp->setVariable("ITEM_TITLE",$vardata['name'].$strFill."\t");
                      $configtp->setVariable("ITEM_VALUE",$vardata['value']);
                      $configtp->parse("configline");
                    }
                  }
                  $intSkip = 1;
                // Handelt es sich um eine Spezialrelation für Servicegruppen?
                } else if (($elem['type'] == 5) && ($value == 1)) {
                  $strSQLMaster   = "SELECT * FROM `".$elem['linktable']."` WHERE `idMaster` = ".$arrData[$i]['id'];
                  $booReturn    = $this->myDBClass->getDataArray($strSQLMaster,$arrDataMaster,$intDataCountMaster);
                  // Wurden Datensätze gefunden?
                  if ($intDataCountMaster != 0) {
                    // Datenfeldwerte der gefundenen Datensätze eintragen
                    $value = "";
                    foreach ($arrDataMaster AS $data) {
                      if ($data['idSlaveHG'] != 0) {
                        $strService = $this->myDBClass->getFieldData("SELECT `".$elem['target2']."` FROM `".$elem['tableName2']."` WHERE `id` = ".$data['idSlaveS']." AND `active`='1'");
                        $strSQLHG1  = "SELECT `host_name` FROM `tbl_host` LEFT JOIN `tbl_lnkHostgroupToHost` ON `id`=`idSlave` WHERE `idMaster`=".$data['idSlaveHG']." AND `active`='1'";;
                        $booReturn  = $this->myDBClass->getDataArray($strSQLHG1,$arrHG1,$intHG1);
                        if ($intHG1 != 0) {
                          foreach ($arrHG1 AS $elemHG1) {
                            if (substr_count($value,$elemHG1['host_name'].",".$strService) == 0) {
                              $value .= $elemHG1['host_name'].",".$strService.",";
                            }
                          }
                        }
                        $strSQLHG2  = "SELECT `host_name` FROM `tbl_host` LEFT JOIN `tbl_lnkHostToHostgroup` ON `id`=`idMaster` WHERE `idSlave`=".$data['idSlaveHG']." AND `active`='1'";;
                        $booReturn  = $this->myDBClass->getDataArray($strSQLHG2,$arrHG2,$intHG2);
                        if ($intHG2 != 0) {
                          foreach ($arrHG2 AS $elemHG2) {
                            if (substr_count($value,$elemHG2['host_name'].",".$strService) == 0) {
                              $value .= $elemHG2['host_name'].",".$strService.",";
                            }
                          }
                        }
                      } else {
                        $strHost   = $this->myDBClass->getFieldData("SELECT `".$elem['target1']."` FROM `".$elem['tableName1']."` WHERE `id` = ".$data['idSlaveH']." AND `active`='1'");
                        $strService  = $this->myDBClass->getFieldData("SELECT `".$elem['target2']."` FROM `".$elem['tableName2']."` WHERE `id` = ".$data['idSlaveS']." AND `active`='1'");
                        if (($strHost != "") && ($strService != "")) {
                          if (substr_count($value,$strHost.",".$strService) == 0) {
                            $value .= $strHost.",".$strService.",";
                          }
                        }
                      }
                    }
                    $value = substr($value,0,-1);
                  } else {
                    $intSkip = 1;
                  }
                // Handelt es sich um den Ausnahmewert "*"?
                } else if ($value == 2) {
                  $value = "*";
                } else {
                  $intSkip = 1;
                }
              }
            }
          }
          // Felder umbenennen
          if ($strTableName == "tbl_hosttemplate") {
            if ($key == "template_name")  $key = "name";
            if ($key == "use_template")   $key = "use";
            $strVIValues  = "active_checks_enabled,passive_checks_enabled,check_freshness,obsess_over_host,event_handler_enabled,";
            $strVIValues .= "flap_detection_enabled,process_perf_data,retain_status_information,retain_nonstatus_information,";
            $strVIValues .= "notifications_enabled";
            if (in_array($key,explode(",",$strVIValues))) {
              if ($value == -1)         $value = "null";
              if ($value == 3)        $value = "null";
            }
            if ($key == "parents")      $value = $this->checkTpl($value,"parents_tploptions","tbl_hosttemplate",$intDataId,$intSkip);
            if ($key == "hostgroups")   $value = $this->checkTpl($value,"hostgroups_tploptions","tbl_hosttemplate",$intDataId,$intSkip);
            if ($key == "contacts")     $value = $this->checkTpl($value,"contacts_tploptions","tbl_hosttemplate",$intDataId,$intSkip);
            if ($key == "contact_groups") $value = $this->checkTpl($value,"contact_groups_tploptions","tbl_hosttemplate",$intDataId,$intSkip);
            if ($key == "use")        $value = $this->checkTpl($value,"use_template_tploptions","tbl_hosttemplate",$intDataId,$intSkip);
          }
          if ($strTableName == "tbl_servicetemplate") {
            if ($key == "template_name")  $key = "name";
            if ($key == "use_template")   $key = "use";
		    if (($strVersionValue != 3) && ($strVersionValue != 2)) {
			  if ($key == "check_interval")   $key = "normal_check_interval";
			  if ($key == "retry_interval")   $key = "retry_check_interval";
		    }
            $strVIValues  = "is_volatile,active_checks_enabled,passive_checks_enabled,parallelize_check,obsess_over_service,";
            $strVIValues .= "check_freshness,event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,";
            $strVIValues .= "retain_nonstatus_information,notifications_enabled";
            if (in_array($key,explode(",",$strVIValues))) {
              if ($value == -1)         $value = "null";
              if ($value == 3)        $value = "null";
            }
            if ($key == "host_name")    $value = $this->checkTpl($value,"host_name_tploptions","tbl_servicetemplate",$intDataId,$intSkip);
            if ($key == "hostgroup_name") $value = $this->checkTpl($value,"hostgroup_name_tploptions","tbl_servicetemplate",$intDataId,$intSkip);
            if ($key == "servicegroups")  $value = $this->checkTpl($value,"servicegroups_tploptions","tbl_servicetemplate",$intDataId,$intSkip);
            if ($key == "contacts")     $value = $this->checkTpl($value,"contacts_tploptions","tbl_servicetemplate",$intDataId,$intSkip);
            if ($key == "contact_groups") $value = $this->checkTpl($value,"contact_groups_tploptions","tbl_servicetemplate",$intDataId,$intSkip);
            if ($key == "use")        $value = $this->checkTpl($value,"use_template_tploptions","tbl_servicetemplate",$intDataId,$intSkip);
          }
          if ($strTableName == "tbl_contact") {
            if ($key == "use_template")   $key = "use";
            $strVIValues  = "host_notifications_enabled,service_notifications_enabled,can_submit_commands,retain_status_information,";
            $strVIValues  = "retain_nonstatus_information";             if (in_array($key,explode(",",$strVIValues))) {
              if ($value == -1)         $value = "null";
              if ($value == 3)        $value = "null";
            }
            if ($key == "contactgroups")  $value = $this->checkTpl($value,"contactgroups_tploptions","tbl_contact",$intDataId,$intSkip);
            if ($key == "host_notification_commands")   $value = $this->checkTpl($value,"host_notification_commands_tploptions","tbl_contact",$intDataId,$intSkip);
            if ($key == "service_notification_commands")  $value = $this->checkTpl($value,"service_notification_commands_tploptions","tbl_contact",$intDataId,$intSkip);
            if ($key == "use")        $value = $this->checkTpl($value,"use_template_tploptions","tbl_contact",$intDataId,$intSkip);
          }
          if ($strTableName == "tbl_contacttemplate") {
            if ($key == "template_name")  $key = "name";
            if ($key == "use_template")   $key = "use";
            $strVIValues  = "host_notifications_enabled,service_notifications_enabled,can_submit_commands,retain_status_information,";
            $strVIValues  = "retain_nonstatus_information";
            if (in_array($key,explode(",",$strVIValues))) {
              if ($value == -1)         $value = "null";
              if ($value == 3)        $value = "null";
            }
            if ($key == "contactgroups")  $value = $this->checkTpl($value,"contactgroups_tploptions","tbl_contacttemplate",$intDataId,$intSkip);
            if ($key == "host_notification_commands")   $value = $this->checkTpl($value,"host_notification_commands_tploptions","tbl_contacttemplate",$intDataId,$intSkip);
            if ($key == "service_notification_commands")  $value = $this->checkTpl($value,"service_notification_commands_tploptions","tbl_contacttemplate",$intDataId,$intSkip);
            if ($key == "use")        $value = $this->checkTpl($value,"use_template_tploptions","tbl_contacttemplate",$intDataId,$intSkip);
          }

          // Spezialbehandlung für Konfiguration der Servicegruppen im Feld "members" // TODO - 3.0 Check
//          if (($strTableName == "tbl_servicegroup") && ($key == "members")) {
//            $strSQLRel = "SELECT tbl_host.host_name, service_description, tbl_B1_id, tbl_B2_id
//                    FROM tbl_relation_special
//                    LEFT JOIN tbl_host ON tbl_relation_special.tbl_B1_id = tbl_host.id
//                    LEFT JOIN tbl_service ON tbl_relation_special.tbl_B2_id = tbl_service.id
//                    WHERE tbl_A =14 AND tbl_B1 =4 AND tbl_B2 =10 AND tbl_A_field = 'members'
//                      AND tbl_A_id=".$arrData[$i]['id']."
//                    ORDER BY tbl_host.host_name, service_description";
//            $booReturn = $this->myDBClass->getDataArray($strSQLRel,$arrDataRel,$intDataCountRel);
//            // Wurden Datensätze gefunden?
//            if ($booReturn && ($intDataCountRel != 0)) {
//              // Datenfeldwerte der gefundenen Datensätze eintragen
//              $value = "";
//              foreach ($arrDataRel AS $data) {
//                if ($data['tbl_B1_id'] == 0) $data['host_name'] = "*";
//                if ($data['tbl_B2_id'] == 0) $data['service_description'] = "*";
//                $value .= $data['host_name'].",".$data['service_description'].",";
//              }
//              $value = substr($value,0,-1);
//              $intSkip = 0;
//            } else {
//              $intSkip = 1;
//            }
//          }
          // Falls das Datenfeld nicht übersprungen werden soll
          if ($intSkip != 1) {
            // Bei längeren Keys zuszliche Tabulatoren einfgen
            $intLen  = strlen($key);
            $strFill = "                            ";
            if ($intLen < 30) {
              $strFill = substr($strFill,-(30-$intLen));
            } else {
              $strFill = "\t";
            }
            // Schlüssel und Wert in Template schreiben und nächste Zeile aufrufen
            $configtp->setVariable("ITEM_TITLE",$key.$strFill."\t");
            $configtp->setVariable("ITEM_VALUE",$value);
            $configtp->parse("configline");
          }
        }
        // Sonderregel für Zeitperioden
        if ($strTableName == "tbl_timeperiod") {
          $strSQLTime = "SELECT `definition`, `range` FROM `tbl_timedefinition` WHERE `tipId` = ".$arrData[$i]['id'];
          $booReturn  = $this->myDBClass->getDataArray($strSQLTime,$arrDataTime,$intDataCountTime);
          // Wurden Datensätze gefunden?
          if ($intDataCountTime != 0) {
            // Datenfeldwerte der gefundenen Datensätze eintragen
            foreach ($arrDataTime AS $data) {
              // Bei längeren Keys zuszliche Tabulatoren einfgen
              $intLen  = strlen(stripslashes($data['definition']));
              $strFill = "                            ";
              if ($intLen < 30) {
                $strFill = substr($strFill,-(30-$intLen));
              } else {
                $strFill = "\t";
              }
              // Schlüssel und Wert in Template schreiben und nächste Zeile aufrufen
              $configtp->setVariable("ITEM_TITLE",stripslashes($data['definition']).$strFill."\t");
              $configtp->setVariable("ITEM_VALUE",stripslashes($data['range']));
              $configtp->parse("configline");
            }
          }
        }
        if (($strTableName == "tbl_hosttemplate") || ($strTableName == "tbl_servicetemplate") || ($strTableName == "tbl_contacttemplate")) {
              $configtp->setVariable("ITEM_TITLE","register                    \t");
              $configtp->setVariable("ITEM_VALUE","0");
              $configtp->parse("configline");
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
      if ($strMethod == 2) {
        if (!ftp_put($conn_id,$strBaseDir."/".$strFile,$this->arrSettings['path']['tempdir']."/".$strFile,FTP_ASCII)) {
          $this->strDBMessage = gettext('Cannot open/overwrite the configuration file (FTP connection failed)!');
          ftp_close($conn_id);
          return(1);
        }
        ftp_close($conn_id);
        // Temp File löschen
        unlink($this->arrSettings['path']['tempdir']."/".$strFile);
      }
      $this->myDataClass->writeLog(gettext('Configuration successfully written:')." ".$strFile);
      $this->strDBMessage = gettext('Configuration file successfully written!');
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
  //  Schreibt ein einzelnes Konfigurationsfile mit einem einzelnen Datensatz einer Tabelle oder
  //  liefert die Ausgabe als Textdatei zum Download aus.
  //
  //  Übergabeparameter:  $strTableName Tabellenname
  //  ------------------  $intDbId    Datensatz ID
  //            $intMode    0 = Datei schreiben, 1 = Ausgabe für Download
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //  Rckgabewert:    Erfolg-/Fehlermeldung via Klassenvariable strDBMessage
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function createConfigSingle($strTableName,$intDbId = 0,$intMode = 0) {
    $return = 0;
	// Alle Datensatz ID der Tabelle holen
    $strSQL = "SELECT `id` FROM `".$strTableName."` WHERE `config_id`=".$this->intDomainId." ORDER BY `id`";
    $booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
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
              $strConfigName = $this->myDBClass->getFieldData("SELECT `host_name` FROM `".$strTableName."` WHERE `id`=".$arrData[$i]['id']);
              $setTemplate   = "hosts.tpl.dat";
              $this->getConfigData("hostconfig",$strBaseDir);
              $this->getConfigData("hostbackup",$strBackupDir);
              $strSQLData    = "SELECT * FROM `".$strTableName."` WHERE `host_name`='$strConfigName' AND `config_id`=".$this->intDomainId;
              break;
            case "tbl_service":
              $strConfigName = $this->myDBClass->getFieldData("SELECT `config_name` FROM `".$strTableName."` WHERE `id`=".$arrData[$i]['id']);
              $setTemplate   = "services.tpl.dat";
              $this->getConfigData("serviceconfig",$strBaseDir);
              $this->getConfigData("servicebackup",$strBackupDir);
              $strSQLData    = "SELECT * FROM `".$strTableName."` WHERE `config_name`='$strConfigName' AND `config_id`=".$this->intDomainId." ORDER BY `service_description`";
              break;
          }
          $strFile = $strConfigName.".cfg";
          // Falls ein Datenbankfehler aufgetreten ist, hier abbrechen
          if ($this->myDBClass->strDBError != "") {
            $this->strDBMessage = gettext('Cannot open/overwrite the configuration file (check the permissions)!');
            return(1);
          }
          // Relationen holen
          $this->myDataClass->tableRelations($strTableName,$arrRelations);
          // Konfigurationsdatei sichern
          if ($intMode == 0) {
            // Konfigurationsdaten holen
            $booReturn = $this->getConfigData("method",$strMethod);
            if ($strMethod == 1) {
              // Alte Konfigurationsdatei sichern
              if (file_exists($strBaseDir."/".$strFile) && is_writable($strBackupDir)) {
                $strOldDate = date("YmdHis",mktime());
                copy($strBaseDir."/".$strFile,$strBackupDir."/".$strFile."_old_".$strOldDate);
              } else if (!is_writable($strBackupDir)) {
                $this->strDBMessage = gettext('Cannot backup the configuration file (check the permissions)!');
                return(1);
              }
              // Konfigurationsdatei öffnen
              if (is_writable($strBaseDir."/".$strFile) || (!file_exists($strBaseDir."/".$strFile))) {
                $CONFIGFILE = fopen($strBaseDir."/".$strFile,"w");
                chmod($strBaseDir."/".$strFile, 0644);
              } else {
                $this->myDataClass->writeLog(gettext('Configuration write failed:')." ".$strFile);
                $this->strDBMessage = gettext('Cannot open/overwrite the configuration file (check the permissions)!');
                return(1);
              }
            } else if ($strMethod == 2) {
              // Set up basic connection
              $booReturn    = $this->getConfigData("server",$strServer);
              $conn_id    = ftp_connect($strServer);
              // Login with username and password
              $booReturn    = $this->getConfigData("user",$strUser);
              $booReturn    = $this->getConfigData("password",$strPasswd);
              $login_result   = ftp_login($conn_id, $strUser, $strPasswd);
              // Check connection
              if ((!$conn_id) || (!$login_result)) {
                $this->myDataClass->writeLog(gettext('Configuration write failed (FTP connection failed):')." ".$strFile);
                $this->strDBMessage = gettext('Cannot open/overwrite the configuration file (FTP connection failed)!');
                return(1);
              } else {
                // Alte Konfigurationsdatei sichern
                $intFileStamp = ftp_mdtm($conn_id, $strBaseDir."/".$strFile);
                if ($intFileStamp > -1) {
                  $strOldDate = date("YmdHis",mktime());
                  $intReturn  = ftp_rename($conn_id,$strBaseDir."/".$strFile,$strBackupDir."/".$strFile."_old_".$strOldDate);
                  if (!$intReturn) {
                    $this->strDBMessage = gettext('Cannot backup the configuration file because the permissions are wrong (remote FTP)!');
                  }
                }
                // Konfigurationsdatei öffnen
                if (is_writable($this->arrSettings['path']['tempdir']."/".$strFile) || (!file_exists($this->arrSettings['path']['tempdir']."/".$strFile))) {
                  $CONFIGFILE = fopen($this->arrSettings['path']['tempdir']."/".$strFile,"w");
                } else {
                  $this->myDataClass->writeLog(gettext('Configuration write failed:')." ".$strFile);
                  $this->strDBMessage = gettext('Cannot open/overwrite the configuration file - check the permissions of the temp directory:')." ".$this->arrSettings['path']['tempdir'];
                  ftp_close($conn_id);
                  return(1);
                }

              }
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
          $this->getConfigData("version",$strVersionValue);
          if ($strVersionValue == 3) $strVersion = "Nagios 3.x config file";
          if ($strVersionValue == 2) $strVersion = "Nagios 2.9 config file";
          if ($strVersionValue == 1) $strVersion = "Nagios 2.x config file";
          $configtp->setVariable("VERSION",$strVersion);
          // Falls der Datensatz nicht gefunden wurde
          if ($booReturn == false) {
            $this->strDBMessage = gettext('Error while selecting data from database:')."<br>".$this->myDBClass->strDBError."<br>";
          // Falls der Datensatz gefunden wurde
          } else if ($intDataCountConfig != 0) {
            // Jeden Datensatz verarbeiten
            for ($y=0;$y<$intDataCountConfig;$y++) {
              // Inaktive Datensätze überspringen
              if ($arrDataConfig[$y]['active'] == "0") continue;
              foreach($arrDataConfig[$y] AS $key => $value) {
                $intSkip = 0;
                if ($key == "id") $intDataId = $value;
                // Spezialdatenfelder überspringen
                $strSpecial = "id,config_name,active,last_modified,access_rights,config_id,template,nodelete,command_type";
                if ($strTableName == "tbl_host") $strSpecial .= ",parents_tploptions,hostgroups_tploptions,contacts_tploptions,contact_groups_tploptions,use_template_tploptions";
                if ($strTableName == "tbl_service") $strSpecial .= ",host_name_tploptions,hostgroup_name_tploptions,servicegroups_tploptions,contacts_tploptions,contact_groups_tploptions,use_template_tploptions";
                // Je nach Version weitere Felder überspringen
                if ($strVersionValue == 3) {
                  if ($strTableName == "tbl_service") $strSpecial .= ",parallelize_check";
                }
                if ($strVersionValue == 1) {
                  $strSpecial .= "";
                }
                $arrSpecial = explode(",",$strSpecial);
                if (($value == "") || (in_array($key,$arrSpecial))) {
                  continue;
                }
                // Nicht alle Konfigurationsdaten schreiben
                $strNoTwo = "active_checks_enabled,passive_checks_enabled,obsess_over_host,check_freshness,event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,retain_nonstatus_information,notifications_enabled,is_volatile,parallelize_check,obsess_over_service";
                $booTest = 0;
                foreach(explode(",",$strNoTwo) AS $elem){
                  if (($key == $elem) && ($value == "2")) $booTest = 1;
                }
                if ($booTest == 1) continue;
                // Ist das Datenfeld über eine Relation mit einem anderen Datenfeld verbunden?
                if (is_array($arrRelations)) {
                  foreach($arrRelations AS $elem) {
                    if ($elem['fieldName'] == $key) {
                      // Handelt es sich um eine normale 1:n Relation?
                      if (($elem['type'] == 2) && ($value == 1)) {
                        $strSQLRel = "SELECT `".$elem['tableName']."`.`".$elem['target']."` FROM `".$elem['linktable']."`
                                LEFT JOIN `".$elem['tableName']."` ON `".$elem['linktable']."`.`idSlave` = `".$elem['tableName']."`.`id`
                                WHERE `idMaster`=".$arrDataConfig[$y]['id']."
                                ORDER BY `".$elem['tableName']."`.`".$elem['target']."`";
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
                      // Handelt es sich um eine normale 1:1 Relation?
                      } else if ($elem['type'] == 1) {
                        if ($elem['tableName'] == "tbl_command") {
                          $arrField   = explode("!",$arrDataConfig[$y][$elem['fieldName']]);
                          $strCommand = strchr($arrDataConfig[$y][$elem['fieldName']],"!");
                          $strSQLRel  = "SELECT `".$elem['target']."` FROM `".$elem['tableName']."`
                                   WHERE `id`=".$arrField[0];
                        } else {
                          $strSQLRel  = "SELECT `".$elem['target']."` FROM `".$elem['tableName']."`
                                   WHERE `id`=".$arrDataConfig[$y][$elem['fieldName']];
                        }
                        $booReturn = $this->myDBClass->getDataArray($strSQLRel,$arrDataRel,$intDataCountRel);
                        // Wurden Datensätze gefunden?
                        if ($booReturn && ($intDataCountRel != 0)) {
                          // Datenfeldwert des gefundenen Datensatzes eintragen
                          if ($elem['tableName'] == "tbl_command") {
                            $value = $arrDataRel[0][$elem['target']].$strCommand;
                          } else {
                            $value = $arrDataRel[0][$elem['target']];
                          }
                        } else {
                          $intSkip = 1;
                        }
                      // Handelt es sich um eine normale 1:n Relation mit Spezialtabelle?
                      } else if (($elem['type'] == 3) && ($value == 1)) {
                        $strSQLMaster   = "SELECT * FROM `".$elem['linktable']."` WHERE `idMaster` = ".$arrDataConfig[$y]['id']." ORDER BY `idSort`";
                        $booReturn    = $this->myDBClass->getDataArray($strSQLMaster,$arrDataMaster,$intDataCountMaster);
                        // Wurden Datensätze gefunden?
                        if ($intDataCountMaster != 0) {
                          // Datenfeldwerte der gefundenen Datensätze eintragen
                          $value = "";
                          foreach ($arrDataMaster AS $data) {
                            if ($data['idTable'] == 1) {
                              $strSQLName = "SELECT `".$elem['target1']."` FROM `".$elem['tableName1']."` WHERE `id` = ".$data['idSlave'];
                            } else {
                              $strSQLName = "SELECT `".$elem['target2']."` FROM `".$elem['tableName2']."` WHERE `id` = ".$data['idSlave'];
                            }
                            $value .= $this->myDBClass->getFieldData($strSQLName).",";
                          }
                          $value = substr($value,0,-1);
                        } else {
                          $intSkip = 1;
                        }
                      // Handelt es sich um eine Spezialrrelation für freie Variabeln?
                      } else if (($elem['type'] == 4) && ($value == 1)) {
                        $strSQLVar = "SELECT * FROM `tbl_variabledefinition` LEFT JOIN `".$elem['linktable']."` ON `id` = `idSlave`
                                WHERE `idMaster`=".$arrDataConfig[$y]['id']." ORDER BY `name`";
                        $booReturn = $this->myDBClass->getDataArray($strSQLVar,$arrDSVar,$intDCVar);
                        if ($intDCVar != 0) {
                          foreach ($arrDSVar AS $vardata) {
                            $intLen = strlen($vardata['name']);
                            if ($intLen < 8) $strFiller = "\t\t\t";
                            if (($intLen >= 8) && ($intLen < 16)) $strFiller = "\t\t";
                            if ($intLen >= 16) $strFiller = "\t";
                            $configtp->setVariable("ITEM_TITLE",$vardata['name'].$strFiller);
                            $configtp->setVariable("ITEM_VALUE",$vardata['value']);
                            $configtp->parse("configline");
                          }
                        }
                        $intSkip = 1;
                      // Handelt es sich um den Ausnahmewert "*"?
                      } else if ($value == 2) {
                        $value = "*";
                      } else {
                        $intSkip = 1;
                      }
                    }
                  }
                }
                // Felder umbenennen
                if ($strTableName == "tbl_host") {
                  if ($key == "use_template")   $key = "use";
                  $strVIValues  = "active_checks_enabled,passive_checks_enabled,check_freshness,obsess_over_host,event_handler_enabled,";
                  $strVIValues .= "flap_detection_enabled,process_perf_data,retain_status_information,retain_nonstatus_information,";
                  $strVIValues .= "notifications_enabled";
                  if (in_array($key,explode(",",$strVIValues))) {
                    if ($value == -1)         $value = "null";
                    if ($value == 3)        $value = "null";
                  }
                  if ($key == "parents")      $value = $this->checkTpl($value,"parents_tploptions","tbl_host",$intDataId,$intSkip);
                  if ($key == "hostgroups")   $value = $this->checkTpl($value,"hostgroups_tploptions","tbl_host",$intDataId,$intSkip);
                  if ($key == "contacts")     $value = $this->checkTpl($value,"contacts_tploptions","tbl_host",$intDataId,$intSkip);
                  if ($key == "contact_groups") $value = $this->checkTpl($value,"contact_groups_tploptions","tbl_host",$intDataId,$intSkip);
                  if ($key == "use")        $value = $this->checkTpl($value,"use_template_tploptions","tbl_host",$intDataId,$intSkip);
                }
                // Felder umbenennen
                if ($strTableName == "tbl_service") {
                  if ($key == "use_template")   $key = "use";
				  if (($strVersionValue != 3) && ($strVersionValue != 2)) {
				  	if ($key == "check_interval")   $key = "normal_check_interval";
					if ($key == "retry_interval")   $key = "retry_check_interval";
				  }
                  $strVIValues  = "is_volatile,active_checks_enabled,passive_checks_enabled,parallelize_check,obsess_over_service,";
                  $strVIValues .= "check_freshness,event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,";
                  $strVIValues .= "retain_nonstatus_information,notifications_enabled";
                  if (in_array($key,explode(",",$strVIValues))) {
                    if ($value == -1)         $value = "null";
                    if ($value == 3)        $value = "null";
                  }
                  if ($key == "host_name")    $value = $this->checkTpl($value,"host_name_tploptions","tbl_service",$intDataId,$intSkip);
                  if ($key == "hostgroup_name") $value = $this->checkTpl($value,"hostgroup_name_tploptions","tbl_service",$intDataId,$intSkip);
                  if ($key == "servicegroups")  $value = $this->checkTpl($value,"servicegroups_tploptions","tbl_service",$intDataId,$intSkip);
                  if ($key == "contacts")     $value = $this->checkTpl($value,"contacts_tploptions","tbl_service",$intDataId,$intSkip);
                  if ($key == "contact_groups") $value = $this->checkTpl($value,"contact_groups_tploptions","tbl_service",$intDataId,$intSkip);
                  if ($key == "use")        $value = $this->checkTpl($value,"use_template_tploptions","tbl_service",$intDataId,$intSkip);
                }
                // Falls das Datenfeld nicht übersprungen werden soll
                if ($intSkip != 1) {
                  // Bei längeren Keys zuszliche Tabulatoren einfgen
                  if (strlen($key) < 8) {$strFill  = "\t";} else {$strFill = "";}
                  if (strlen($key) < 16)  $strFill .= "\t\t";
                  if ((strlen($key) < 23) && (strlen($key) >= 16)) $strFill .= "\t";
                  // Schlüssel und Wert in Template schreiben und nächste Zeile aufrufen
                  $configtp->setVariable("ITEM_TITLE",$key.$strFill);
                  $configtp->setVariable("ITEM_VALUE",$value);
                  $configtp->parse("configline");
                }
              }
              // Ist die Konfiguration aktiv?
              $configtp->setVariable("ITEM_TITLE","register\t\t");
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
            if ($strMethod == 2) {
              if (!ftp_put($conn_id,$strBaseDir."/".$strFile,$this->arrSettings['path']['tempdir']."/".$strFile,FTP_ASCII)) {
                $this->strDBMessage = gettext('Cannot open/overwrite the configuration file (FTP connection failed)!');
                ftp_close($conn_id);
                return(1);
              }
              ftp_close($conn_id);
              // Temp File löschen
              unlink($this->arrSettings['path']['tempdir']."/".$strFile);
            }
            $this->myDataClass->writeLog(gettext('Configuration successfully written:')." ".$strFile);
            $this->strDBMessage = gettext('Configuration file successfully written!');
            $return = 0;
			//return(0);
          } else if ($intMode == 1) {
            $configtp->show();
            $return = 0;
			//return(0);
          }
        }
      }
    } else {
      $this->myDataClass->writeLog(gettext('Configuration write failed - Dataset not found'));
      $this->strDBMessage = gettext('Cannot open/overwrite the configuration file (check the permissions)!');
      return(1);
    }
	return($return);
  }
}
?>