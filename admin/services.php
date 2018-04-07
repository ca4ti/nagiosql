<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2008, 2009 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Admin servicetemplate definition
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2009-04-28 15:02:27 +0200 (Di, 28. Apr 2009) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.0.3
// Revision  : $LastChangedRevision: 708 $
// SVN-ID    : $Id: services.php 708 2009-04-28 13:02:27Z rouven $
//
///////////////////////////////////////////////////////////////////////////////
//
// Variabeln deklarieren
// =====================
$intMain      = 2;
$intSub       = 7;
$intMenu      = 2;
$preContent   = "admin/services.tpl.htm";
$strDBWarning = "";
$intCount     = 0;
$strMessage   = "";
//
// Vorgabedatei einbinden
// ======================
$preAccess    = 1;
$preFieldvars = 1;
require("../functions/prepend_adm.php");
$myConfigClass->getConfigData("version",$intVersion);
//
// Übergabeparameter
// =================
$chkTfSearch        = isset($_POST['txtSearch'])      ? $_POST['txtSearch']             : "";
$chkTfName          = isset($_POST['tfName'])         ? $_POST['tfName']              : "";
$chkOldConfig       = isset($_POST['hidName'])        ? $_POST['hidName']             : "";
$chkSelHosts        = isset($_POST['selHosts'])       ? $_POST['selHosts']            : array("");
$chkRadHosts        = isset($_POST['radHosts'])       ? $_POST['radHosts']+0            : 2;
$chkSelHostGroups     = isset($_POST['selHostGroups'])    ? $_POST['selHostGroups']           : array("");
$chkRadHostGroups     = isset($_POST['radHostGroups'])    ? $_POST['radHostGroups']+0         : 2;
$chkTfServiceDescription  = isset($_POST['tfServiceDescription']) ? $_POST['tfServiceDescription']      : "";
$chkTfDisplayName       = isset($_POST['tfDisplayName'])    ? $_POST['tfDisplayName']         : "";
$chkSelServiceGroups    = isset($_POST['selServiceGroups'])   ? $_POST['selServiceGroups']        : array("");
$chkRadServiceGroups    = isset($_POST['radServiceGroups'])   ? $_POST['radServiceGroups']+0        : 2;
$chkServiceCommand      = isset($_POST['selServiceCommand'])  ? $_POST['selServiceCommand']+0       : 0;
$chkTfArg1          = isset($_POST['tfArg1'])         ? $_POST['tfArg1']              : "";
$chkTfArg2          = isset($_POST['tfArg2'])         ? $_POST['tfArg2']              : "";
$chkTfArg3          = isset($_POST['tfArg3'])         ? $_POST['tfArg3']              : "";
$chkTfArg4          = isset($_POST['tfArg4'])         ? $_POST['tfArg4']              : "";
$chkTfArg5          = isset($_POST['tfArg5'])         ? $_POST['tfArg5']              : "";
$chkTfArg6          = isset($_POST['tfArg6'])         ? $_POST['tfArg6']              : "";
$chkTfArg7          = isset($_POST['tfArg7'])         ? $_POST['tfArg7']              : "";
$chkTfArg8          = isset($_POST['tfArg8'])         ? $_POST['tfArg8']              : "";
$chkRadTemplates      = isset($_POST['radTemplate'])      ? $_POST['radTemplate']+0         : 2;
$chkISo           = isset($_POST['chbISo'])       ? $_POST['chbISo'].","            : "";
$chkISw           = isset($_POST['chbISw'])       ? $_POST['chbISw'].","            : "";
$chkISu           = isset($_POST['chbISu'])       ? $_POST['chbISu'].","            : "";
$chkISc           = isset($_POST['chbISc'])       ? $_POST['chbISc'].","            : "";
$chkTfRetryInterval     = (isset($_POST['tfRetryInterval'])   && ($_POST['tfRetryInterval'] != ""))   ? $myVisClass->checkNull($_POST['tfRetryInterval'])+0   : "NULL";
$chkTfMaxCheckAttempts    = (isset($_POST['tfMaxCheckAttempts'])  && ($_POST['tfMaxCheckAttempts'] != ""))  ? $myVisClass->checkNull($_POST['tfMaxCheckAttempts'])+0  : "NULL";
$chkTfCheckInterval     = (isset($_POST['tfCheckInterval'])   && ($_POST['tfCheckInterval'] != ""))   ? $myVisClass->checkNull($_POST['tfCheckInterval'])+0   : "NULL";
$chkActiveChecks      = isset($_POST['radActiveChecksEnabled']) ? $_POST['radActiveChecksEnabled']+0  : 2;
$chkPassiveChecks     = isset($_POST['radPassiveChecksEnabled'])  ? $_POST['radPassiveChecksEnabled']+0 : 2;
$chkParallelizeChecks   = isset($_POST['radParallelizeChecks']) ? $_POST['radParallelizeChecks']+0      : 2;
$chkSelCheckPeriod      = isset($_POST['selCheckPeriod'])     ? $_POST['selCheckPeriod']+0        : 0;
$chkTfFreshTreshold     = (isset($_POST['tfFreshTreshold'])   && ($_POST['tfFreshTreshold'] != ""))   ? $myVisClass->checkNull($_POST['tfFreshTreshold'])+0   : "NULL";
$chkFreshness       = isset($_POST['radFreshness'])     ? $_POST['radFreshness']+0          : 2;
$chkObsess          = isset($_POST['radObsess'])      ? $_POST['radObsess']+0           : 2;
$chkSelEventHandler     = isset($_POST['selEventHandler'])    ? $_POST['selEventHandler']+0       : 0;
$chkEventEnable       = isset($_POST['radEventEnable'])   ? $_POST['radEventEnable']+0        : 2;
$chkTfLowFlat       = (isset($_POST['tfLowFlat'])     && ($_POST['tfLowFlat'] != ""))       ? $myVisClass->checkNull($_POST['tfLowFlat'])+0       : "NULL";
$chkTfHighFlat        = (isset($_POST['tfHighFlat'])      && ($_POST['tfHighFlat'] != ""))      ? $myVisClass->checkNull($_POST['tfHighFlat'])+0      : "NULL";
$chkFlapEnable        = isset($_POST['radFlapEnable'])    ? $_POST['radFlapEnable']+0         : 2;
$chkFLo           = isset($_POST['chbFLo'])       ? $_POST['chbFLo'].","            : "";
$chkFLw           = isset($_POST['chbFLw'])       ? $_POST['chbFLw'].","            : "";
$chkFLu           = isset($_POST['chbFLu'])       ? $_POST['chbFLu'].","            : "";
$chkFLc           = isset($_POST['chbFLc'])       ? $_POST['chbFLc'].","            : "";
$chkStatusInfos       = isset($_POST['radStatusInfos'])   ? $_POST['radStatusInfos']+0        : 2;
$chkNonStatusInfos      = isset($_POST['radNoStatusInfos'])   ? $_POST['radNoStatusInfos']+0        : 2;
$chkPerfData        = isset($_POST['radPerfData'])      ? $_POST['radPerfData']+0         : 2;
$chkIsVolatile        = isset($_POST['radIsVolatile'])    ? $_POST['radIsVolatile']+0         : 2;
$chkSelContacts       = isset($_POST['selContacts'])      ? $_POST['selContacts']           : array("");
$chkRadContacts       = isset($_POST['radContacts'])      ? $_POST['radContacts']+0         : 2;
$chkSelContactGroups    = isset($_POST['selContactGroups'])   ? $_POST['selContactGroups']        : array("");
$chkRadContactGroups    = isset($_POST['radContactGroups'])   ? $_POST['radContactGroups']+0        : 2;
$chkSelNotifPeriod      = isset($_POST['selNotifPeriod'])     ? $_POST['selNotifPeriod']+0        : 0;
$chkNOw           = isset($_POST['chbNOw'])       ? $_POST['chbNOw'].","            : "";
$chkNOu           = isset($_POST['chbNOu'])       ? $_POST['chbNOu'].","            : "";
$chkNOc           = isset($_POST['chbNOr'])       ? $_POST['chbNOr'].","            : "";
$chkNOr           = isset($_POST['chbNOc'])       ? $_POST['chbNOc'].","            : "";
$chkNOf           = isset($_POST['chbNOf'])       ? $_POST['chbNOf'].","            : "";
$chkNOs           = isset($_POST['chbNOs'])       ? $_POST['chbNOs'].","            : "";
$chkNOnull          = isset($_POST['chbNOnull'])      ? $_POST['chbNOnull'].","         : "";
$chkNotifInterval     = (isset($_POST['tfNotifInterval'])   && ($_POST['tfNotifInterval'] != ""))     ? $myVisClass->checkNull($_POST['tfNotifInterval'])+0   : "NULL";
$chkNotifDelay        = (isset($_POST['tfFirstNotifDelay']) && ($_POST['tfFirstNotifDelay'] != ""))   ? $myVisClass->checkNull($_POST['tfFirstNotifDelay'])+0   : "NULL";
$chkNotifEnabled      = isset($_POST['radNotifEnabled'])    ? $_POST['radNotifEnabled']+0       : 0;
$chkSTo           = isset($_POST['chbSTo'])       ? $_POST['chbSTo'].","            : "";
$chkSTw           = isset($_POST['chbSTw'])       ? $_POST['chbSTw'].","            : "";
$chkSTu           = isset($_POST['chbSTu'])       ? $_POST['chbSTu'].","            : "";
$chkSTc           = isset($_POST['chbSTc'])       ? $_POST['chbSTc'].","            : "";
$chkTfNotes         = isset($_POST['tfNotes'])        ? $_POST['tfNotes']             : "";
$chkTfNotesURL        = isset($_POST['tfNotesURL'])       ? $_POST['tfNotesURL']            : "";
$chkTfActionURL       = isset($_POST['tfActionURL'])      ? $_POST['tfActionURL']           : "";
$chkTfIconImage       = isset($_POST['tfIconImage'])      ? $_POST['tfIconImage']           : "";
$chkTfIconImageAlt      = isset($_POST['tfIconImageAlt'])     ? $_POST['tfIconImageAlt']          : "";
$chkGenericName       = isset($_POST['tfGenericName'])    ? $_POST['tfGenericName']           : "";
$chkSelOrderByGet     = isset($_GET['orderby'])       ? rawurldecode($_GET['orderby'])      : "";
$chkSelOrderBy        = isset($_POST['selOrderBy'])       ? $_POST['selOrderBy']            : "";
$chkHostGiven       = isset($_POST['hostGiven'])      ? $_POST['hostGiven']           : 0;
//
// Suchfunktion - Session schreiben
// ================================
if (!isset($_SESSION['serviceSearch'])) $_SESSION['serviceSearch']  = "";
if (!isset($_SESSION['serviceOrder']))  $_SESSION['serviceOrder']   = "";
if (($chkModus == "checkform") || ($chkModus == "filter")) {
  $_SESSION['serviceSearch'] = $chkTfSearch;
  $_SESSION['serviceOrder']  = $chkSelOrderBy;
  if ($chkSelOrderByGet != "") {
    $_SESSION['serviceOrder']  = $chkSelOrderByGet;
  }
  if ($chkSelOrderBy == gettext('All configs')) {
    $_SESSION['serviceOrder'] = "";
  }
}
//
// Datenbankeintrag vorbereiten bei Sonderzeichen
// ==============================================
if (ini_get("magic_quotes_gpc") == 0) {
  $chkTfSearch        = addslashes($chkTfSearch);
  $chkTfName          = addslashes($chkTfName);
  $chkOldConfig       = addslashes($chkOldConfig);
  $chkTfServiceDescription    = addslashes($chkTfServiceDescription);
  $chkTfDisplayName     = addslashes($chkTfDisplayName);
  $chkTfArg1          = addslashes($chkTfArg1);
  $chkTfArg2          = addslashes($chkTfArg2);
  $chkTfArg3          = addslashes($chkTfArg3);
  $chkTfArg4          = addslashes($chkTfArg4);
  $chkTfArg5          = addslashes($chkTfArg5);
  $chkTfArg6          = addslashes($chkTfArg6);
  $chkTfArg7          = addslashes($chkTfArg7);
  $chkTfArg8          = addslashes($chkTfArg8);
  $chkTfNotes         = addslashes($chkTfNotes);
  $chkTfNotesURL        = addslashes($chkTfNotesURL);
  $chkTfActionURL       = addslashes($chkTfActionURL);
  $chkTfIconImage       = addslashes($chkTfIconImage);
  $chkTfIconImageAlt      = addslashes($chkTfIconImageAlt);
  $chkGenericName       = addslashes($chkGenericName);
}
//
// Zusätzliche Templates/Variabeln verarbeiten
// ===========================================
if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition']) && (count($_SESSION['templatedefinition']) != 0)) {
  $intTemplates = 1;
} else {
  $intTemplates = 0;
}
if (isset($_SESSION['variabledefinition']) && is_array($_SESSION['variabledefinition']) && (count($_SESSION['variabledefinition']) != 0)) {
  $intVariables = 1;
} else {
  $intVariables = 0;
}
//
// Daten verarbeiten
// =================
$strFilter = "";
$strIS = substr($chkISo.$chkISw.$chkISu.$chkISc,0,-1);
$strFL = substr($chkFLo.$chkFLw.$chkFLu.$chkFLc,0,-1);
$strNO = substr($chkNOw.$chkNOu.$chkNOc.$chkNOr.$chkNOf.$chkNOs,0,-1);
$strST = substr($chkSTo.$chkSTw.$chkSTu.$chkSTc,0,-1);
if (($chkSelHosts[0] == "")       || ($chkSelHosts[0] == "0"))        {$intSelHosts = 0;}     else {$intSelHosts = 1;}
if ($chkSelHosts[0] == "*")     $intSelHosts = 2;
if (($chkSelHostGroups[0] == "")    || ($chkSelHostGroups[0] == "0"))     {$intSelHostGroups = 0;}    else {$intSelHostGroups = 1;}
if ($chkSelHostGroups[0] == "*")     $intSelHostGroups = 2;
if (($chkSelServiceGroups[0] == "") || ($chkSelServiceGroups[0] == "0"))  {$intSelServiceGroups = 0;} else {$intSelServiceGroups = 1;}
if (($chkSelContacts[0] == "")    || ($chkSelContacts[0] == "0"))     {$intSelContacts = 0;}    else {$intSelContacts = 1;}
if ($chkSelContacts[0] == "*")        $intSelContacts = 2;
if (($chkSelContactGroups[0] == "") || ($chkSelContactGroups[0] == "0"))  {$intSelContactGroups = 0;} else {$intSelContactGroups = 1;}
if ($chkSelContactGroups[0] == "*")     $intSelContactGroups = 2;
// Checkcommand zusammenstellen
$strCheckCommand = $chkServiceCommand;
if ($chkServiceCommand != "") {
  for ($i=1;$i<=8;$i++) {
    if (${"chkTfArg$i"} != "") $strCheckCommand .= "!".${"chkTfArg$i"};
  }
}
if ($chkModus == "add") $chkSelModify = "";
// Filter definieren
if ($_SESSION['serviceOrder'] != ""){
  $strFilter    = "AND `config_name`='".$_SESSION['serviceOrder']."' ";
}
// Leerzeichen aus dem Konfigurationsnamen entfernen
$chkTfName = str_replace(" ","_",$chkTfName);
// Daten Einfügen oder Aktualisieren
if (($chkModus == "insert") || ($chkModus == "modify")) {
  if ($hidActive == 1) $chkActive = 1;
  $strSQLx = "`tbl_service` SET `config_name`='$chkTfName', `host_name`=$intSelHosts, `host_name_tploptions`=$chkRadHosts,
        `hostgroup_name`=$intSelHostGroups, `hostgroup_name_tploptions`=$chkRadHostGroups, `service_description`='$chkTfServiceDescription',
        `display_name`='$chkTfDisplayName', `servicegroups`=$intSelServiceGroups, `servicegroups_tploptions`=$chkRadServiceGroups,
        `check_command`='$strCheckCommand', `use_template`=$intTemplates, `use_template_tploptions`=$chkRadTemplates,
        `is_volatile`=$chkIsVolatile, `initial_state`='$strIS', `max_check_attempts`=$chkTfMaxCheckAttempts, `check_interval`=$chkTfCheckInterval,
        `retry_interval`=$chkTfRetryInterval, `active_checks_enabled`=$chkActiveChecks, `passive_checks_enabled`=$chkPassiveChecks,
        `check_period`=$chkSelCheckPeriod, `parallelize_check`=$chkParallelizeChecks, `obsess_over_service`=$chkObsess,
        `check_freshness`=$chkFreshness, `freshness_threshold`=$chkTfFreshTreshold, `event_handler`=$chkSelEventHandler,
        `event_handler_enabled`=$chkEventEnable, `low_flap_threshold`=$chkTfLowFlat, `high_flap_threshold`=$chkTfHighFlat,
        `flap_detection_enabled`=$chkFlapEnable, `flap_detection_options`='$strFL', `process_perf_data`=$chkPerfData,
        `retain_status_information`=$chkStatusInfos, `retain_nonstatus_information`=$chkNonStatusInfos, `contacts`=$intSelContacts,
        `contacts_tploptions`=$chkRadContacts, `contact_groups`=$intSelContactGroups, `contact_groups_tploptions`=$chkRadContactGroups,
        `notification_interval`=$chkNotifInterval, `notification_period`=$chkSelNotifPeriod,
        `first_notification_delay`=$chkNotifDelay, `notification_options`='$strNO', `notifications_enabled`=$chkNotifEnabled,
        `stalking_options`='$strST', `notes`='$chkTfNotes', `notes_url`='$chkTfNotesURL', `action_url`='$chkTfActionURL',
        `icon_image`='$chkTfIconImage', `icon_image_alt`='$chkTfIconImageAlt', `name`='$chkGenericName', `active`='$chkActive',
        `use_variables`=$intVariables, `config_id`=$chkDomainId, `last_modified`=NOW()";
  if ($chkModus == "insert") {
    $strSQL = "INSERT INTO ".$strSQLx;
  } else {
    $strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
  }
//  if ((($intSelHosts != 0) || ($intSelHostGroups != 0)) && ($chkTfServiceDescription != "") && ($chkSelCheckPeriod != "") && ($chkTfMaxCheckAttempts != "NULL") &&
//      ($chkTfCheckInterval  != "NULL") && ($chkTfRetryInterval  != "NULL") && ($chkSelNotifPeriod != "") &&
//    ($strCheckCommand != "") && ($chkNotifInterval != "NULL") && ($strNO != "") && (($intSelContactGroups != 0) || ($intSelContacts != 0))) {
//    $intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
  if (($chkTfName != "") && ($chkTfServiceDescription != "")) {
    $intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
    if ($chkModus == "insert") {
      $chkDataId = $intInsertId;
    }
    if ($intInsert == 1) {
      $intReturn = 1;
    } else {
      if ($chkModus  == "insert")   $myDataClass->writeLog(gettext('New service inserted:')." ".$chkTfName);
      if ($chkModus  == "modify")   $myDataClass->writeLog(gettext('Service modified:')." ".$chkTfName);
      //
      // Relationen eintragen/updaten
      // ============================
      if ($chkModus == "insert") {
        if ($intSelHosts         == 1)  $myDataClass->dataInsertRelation("tbl_lnkServiceToHost",$chkDataId,$chkSelHosts);
        if ($intSelHostGroups    == 1)  $myDataClass->dataInsertRelation("tbl_lnkServiceToHostgroup",$chkDataId,$chkSelHostGroups);
        if ($intSelServiceGroups == 1)  $myDataClass->dataInsertRelation("tbl_lnkServiceToServicegroup",$chkDataId,$chkSelServiceGroups);
        if ($intSelContacts    == 1)  $myDataClass->dataInsertRelation("tbl_lnkServiceToContact",$chkDataId,$chkSelContacts);
        if ($intSelContactGroups == 1)  $myDataClass->dataInsertRelation("tbl_lnkServiceToContactgroup",$chkDataId,$chkSelContactGroups);
      } else if ($chkModus == "modify") {
        if ($intSelHosts == 1) {
          $myDataClass->dataUpdateRelation("tbl_lnkServiceToHost",$chkDataId,$chkSelHosts);
        } else {
          $myDataClass->dataDeleteRelation("tbl_lnkServiceToHost",$chkDataId);
        }
        if ($intSelHostGroups == 1) {
          $myDataClass->dataUpdateRelation("tbl_lnkServiceToHostgroup",$chkDataId,$chkSelHostGroups);
        } else {
          $myDataClass->dataDeleteRelation("tbl_lnkServiceToHostgroup",$chkDataId);
        }
        if ($intSelServiceGroups == 1) {
          $myDataClass->dataUpdateRelation("tbl_lnkServiceToServicegroup",$chkDataId,$chkSelServiceGroups);
        } else {
          $myDataClass->dataDeleteRelation("tbl_lnkServiceToServicegroup",$chkDataId);
        }
        if ($intSelContacts == 1) {
          $myDataClass->dataUpdateRelation("tbl_lnkServiceToContact",$chkDataId,$chkSelContacts);
        } else {
          $myDataClass->dataDeleteRelation("tbl_lnkServiceToContact",$chkDataId);
        }
        if ($intSelContactGroups == 1) {
          $myDataClass->dataUpdateRelation("tbl_lnkServiceToContactgroup",$chkDataId,$chkSelContactGroups);
        } else {
          $myDataClass->dataDeleteRelation("tbl_lnkServiceToContactgroup",$chkDataId);
        }
      }
      // Falls Servicename geändert wurde, alte Konfigurationsdatei umkopieren/löschen
      if (($chkModus == "modify") && ($chkOldConfig != $chkTfName)) {
        $intServiceCount = $myDBClass->countRows("SELECT * FROM `tbl_service` WHERE `config_name`='$chkOldConfig' AND `config_id`=$chkDomainId AND `active`='1'");
        if ($intServiceCount == 0) {
          $intReturn = $myConfigClass->moveFile("service",$chkOldConfig.".cfg");
          if ($intReturn == 0) {
            $strMessage .=  gettext('The assigned, no longer used configuration files were deleted successfully!')."<br>";
            $myDataClass->writeLog(gettext('Service file deleted:')." ".$chkOldConfig.".cfg");
          } else {
            $strMessage .=  gettext('Errors while deleting the old configuration file - please check!:')."<br>".$myConfigClass->strDBMessage."<br>";
          }
        }
      }
      // Falls Service deaktiviert wurde, alte Konfigurationsdatei umkopieren/löschen
      if (($chkModus == "modify") && ($chkActive == 0)) {
        $intServiceCount = $myDBClass->countRows("SELECT * FROM `tbl_service` WHERE `config_name`='$chkOldConfig' AND `config_id`=$chkDomainId AND `active`='1'");
        if ($intServiceCount == 0) {
          $intReturn = $myConfigClass->moveFile("service",$chkOldConfig.".cfg");
          if ($intReturn == 0) {
            $strMessage .=  gettext('The assigned, no longer used configuration files were deleted successfully!')."<br>";
            $myDataClass->writeLog(gettext('Service file deleted:')." ".$chkTfName.".cfg");
          } else {
            $strMessage .=  gettext('Errors while deleting the old configuration file - please check!:')."<br>".$myConfigClass->strDBMessage."<br>";
          }
        }
      }
      //
      // Sessiondaten Templates eintragen/updaten
      // ========================================
      if ($chkModus == "modify") {
        $strSQL   = "DELETE FROM `tbl_lnkServiceToServicetemplate` WHERE `idMaster`=$chkDataId";
        $booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
      }
      if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition']) && (count($_SESSION['templatedefinition']) != 0)) {
        $intSortId = 1;
        foreach($_SESSION['templatedefinition'] AS $elem) {
          if ($elem['status'] == 0) {
            $strSQL = "INSERT INTO `tbl_lnkServiceToServicetemplate` (`idMaster`,`idSlave`,`idTable`,`idSort`)
                   VALUES ($chkDataId,".$elem['idSlave'].",".$elem['idTable'].",".$intSortId.")";
            $booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
          }
          $intSortId++;
        }
      }
      //
      // Sessiondaten Variabeln eintragen/updaten
      // ========================================
      if ($chkModus == "modify") {
        $strSQL   = "SELECT * FROM `tbl_lnkServiceToVariabledefinition` WHERE `idMaster`=$chkDataId";
        $booReturn  = $myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
        if ($intDataCount != 0) {
          foreach ($arrData AS $elem) {
            $strSQL   = "DELETE FROM `tbl_variabledefinition` WHERE `id`=".$elem['idSlave'];
            $booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
          }
        }
        $strSQL   = "DELETE FROM `tbl_lnkServiceToVariabledefinition` WHERE `idMaster`=$chkDataId";
        $booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
      }
      if (isset($_SESSION['variabledefinition']) && is_array($_SESSION['variabledefinition']) && (count($_SESSION['variabledefinition']) != 0)) {
        foreach($_SESSION['variabledefinition'] AS $elem) {
          if ($elem['status'] == 0) {
            $strSQL = "INSERT INTO `tbl_variabledefinition` (`name`,`value`,`last_modified`)
                   VALUES ('".$elem['definition']."','".$elem['range']."',now())";
            $booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
            $strSQL = "INSERT INTO `tbl_lnkServiceToVariabledefinition` (`idMaster`,`idSlave`)
                   VALUES ($chkDataId,$intInsertId)";
            $booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
          }
        }
      }
      $intReturn = 0;
    }
  } else {
    $strMessage .= gettext('Database entry failed! Not all necessary data filled in!');
  }
  $chkModus = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
  // Gewählte Datensätze löschen
  $intReturn = $myDataClass->dataDeleteFull("tbl_service",$chkListId);
  $strMessage .= $myDataClass->strDBMessage;
  if ($intResult == 0) $strFilter = "";
  $chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
  // Gewählte Datensätze kopieren
  $intReturn = $myDataClass->dataCopyEasy("tbl_service","config_name",$chkListId);
  $chkModus  = "display";
} else if ($chkModus == "make") {
  // Servicekonfigurationen schreiben
  $strSQL  = "SELECT `id`, `config_name` FROM `tbl_service` WHERE `active`='1' AND `config_id`=$chkDomainId GROUP BY `config_name`";
  $myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
  $intError = 0;
  if ($intDataCount != 0) {
    foreach ($arrData AS $data) {
      $myConfigClass->createConfigSingle("tbl_service",$data['id']);
      if ($myConfigClass->strDBMessage != gettext('Configuration file successfully written!')) $intError++;
    }
  }
  if ($intError == 0) {
    $strMessage .= gettext('Configuration file successfully written!')."<br>";
    $intReturn = 0;
  } else {
    $strMessage .= $myConfigClass->strDBMessage."<br>";
    $intReturn = 1;
  }
  $chkModus  = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "info")) {
  // Konfigurationsdatei schreiben
  $intReturn  = $myDataClass->infoRelation("tbl_service",$chkListId,"config_name,service_description");
  $strMessage .= $myDataClass->strDBMessage;
  $intReturn  = 0;
  $chkModus   = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
  // Daten des gewählten Datensatzes holen
  $booReturn = $myDBClass->getSingleDataset("SELECT * FROM `tbl_service` WHERE `id`=".$chkListId,$arrModifyData);
  if ($booReturn == false) {
    $myDataClass->strDBMessage .= gettext('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
    $intReturn = 1;
  }
  $chkModus      = "add";
} else if (($chkModus == "checkform") && ($chkSelModify == "config")) {
  // Konfiguration schreiben
  $intDSId    = (int)substr(array_search("on",$_POST),6);
  if (isset($chkListId) && ($chkListId != 0)) $intDSId = $chkListId;
  // Prüfen ob es noch aktive Konfigurationen gibt
  $booReturn = $myDBClass->getSingleDataset("SELECT `config_name`, `active` FROM `tbl_service` WHERE `id`=".$intDSId,$arrModifyData);
  $myDBClass->getSingleDataset("SELECT count(*) AS `counter` FROM `tbl_service` WHERE `config_name`='".$arrModifyData['config_name']."' AND `active`='1' AND `config_id`=$chkDomainId",$arrActiveData);
  // Falls es keine aktiven Konfigurationen mehr gibt, das Konfigurationsfile löschen
  if ($arrActiveData['counter'] == 0) {
    $intReturn = $myConfigClass->moveFile("service",$chkOldConfig.".cfg");
    if ($intReturn == 0) {
      $strMessage .=  gettext('The assigned, no longer used configuration files were deleted successfully!');
      $myDataClass->writeLog(gettext('Service file deleted:')." ".$chkOldConfig.".cfg");
    } else {
      $strMessage .=  gettext('Errors while deleting the old configuration file - please check!:')."<br>".$myConfigClass->strDBMessage;
    }
  // Falls es aktive Konfiguration mehr gibt, das Konfigurationsfile löschen
  } else {
    $intReturn = $myConfigClass->createConfigSingle("tbl_service",$intDSId);
    $myDataClass->strDBMessage = $myConfigClass->strDBMessage;
  }
  $chkModus   = "display";
}  else if ($chkModus == "filter") {
  // Filtereinstellungen definieren
  if ($_SESSION['serviceOrder'] != ""){
    $strFilter    = "AND `config_name`='".$_SESSION['serviceOrder']."' ";
  }
  $chkModus   = "display";
} else if ($chkModus  != "add") {
  $chkModus   = "display";
}
// Statusmitteilungen setzen
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $myDataClass->strDBMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",gettext('Define services (services.cfg)'));
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if ($chkModus == "add") {
  // Templatefelder füllen (Spezial)
  $strWhere = "";
  if (isset($arrModifyData) && ($chkSelModify == "modify")) {
    $strWhere = "AND `id` <> ".$arrModifyData['id'];
  }
  $strSQL   = "SELECT `id`,`template_name` FROM `tbl_servicetemplate` WHERE `config_id`=$chkDomainId ORDER BY `template_name`";
  $booReturn  = $myDBClass->getDataArray($strSQL,$arrDataTpl,$intDataCountTpl);
  if ($intDataCountTpl != 0) {
    foreach ($arrDataTpl AS $elem) {
      $conttp->setVariable("DAT_TEMPLATE",$elem['template_name']);
      $conttp->setVariable("DAT_TEMPLATE_ID",$elem['id']."::1");
      $conttp->parse("template");
    }
  }
  $strSQL     = "SELECT `id`, `name` FROM `tbl_service` WHERE `name` <> '' AND `config_id`=$chkDomainId $strWhere ORDER BY `name`";
  $booReturn  = $myDBClass->getDataArray($strSQL,$arrDataHpl,$intDataCount);
  if ($arrDataHpl != 0) {
    foreach ($arrDataHpl AS $elem) {
      $conttp->setVariable("DAT_TEMPLATE",$elem['name']);
      $conttp->setVariable("DAT_TEMPLATE_ID",$elem['id']."::2");
      $conttp->parse("template");
    }
  }
  // Hostauswahlfelder füllen
  $intReturn = 0;
  if (isset($arrModifyData['host_name'])) {$intFieldId = $arrModifyData['host_name'];} else {$intFieldId = 0;}
  $intReturn1 = $myVisClass->parseSelect('tbl_host','host_name','DAT_HOST','hosts',$conttp,$chkListId,'tbl_lnkServiceToHost',$intFieldId,3);
  if (isset($arrModifyData['hostgroup_name'])) {$intFieldId = $arrModifyData['hostgroup_name'];} else {$intFieldId = 0;}
  $intReturn2 = $myVisClass->parseSelect('tbl_hostgroup','hostgroup_name','DAT_HOSTGROUPITEM','hostgroups',$conttp,$chkListId,'tbl_lnkServiceToHostgroup',$intFieldId,3);
  if (($intReturn1 != 0) && ($intReturn2 != 0)) $strDBWarning .= gettext('Attention, no hosts or hostgroups defined!')."<br>";
  // Servicegruppenauswahlfelder füllen
  if (isset($arrModifyData['servicegroups'])) {$intFieldId = $arrModifyData['servicegroups'];} else {$intFieldId = 0;}
  $myVisClass->parseSelect('tbl_servicegroup','servicegroup_name','DAT_SERVICEGROUPITEM','servicegroups',$conttp,$chkListId,'tbl_lnkServiceToServicegroup',$intFieldId,0);
  // Servicecommandfelder füllen
  if (isset($arrModifyData['check_command']) && ($arrModifyData['check_command'] != "")) {
    $arrCommand = explode("!",$arrModifyData['check_command']);
    $intFieldId = $arrCommand[0];
   } else {
    $intFieldId = 0;
   }
  $intReturn = $myVisClass->parseSelect('tbl_command','command_name','DAT_SERVICE_COMMAND','servicecommand',$conttp,$chkListId,'',$intFieldId,1,0,1);
  if ($intReturn != 0) $strDBWarning .= gettext('Attention, no check commands defined!')."<br>";
  // Prüfperiodenfelder füllen
  $intReturn = 0;
  if (isset($arrModifyData['check_period'])) {$intFieldId = $arrModifyData['check_period'];} else {$intFieldId = 0;}
  $intReturn = $myVisClass->parseSelect('tbl_timeperiod','timeperiod_name','DAT_CHECK_PERIODS','checkperiod',$conttp,$chkListId,'',$intFieldId,1);
  if (isset($arrModifyData['notification_period'])) {$intFieldId = $arrModifyData['notification_period'];} else {$intFieldId = 0;}
  $intReturn = $myVisClass->parseSelect('tbl_timeperiod','timeperiod_name','DAT_NOTIF_PERIOD','notifperiod',$conttp,$chkListId,'',$intFieldId,1);
  // Eventhandlerfelder füllen
  if (isset($arrModifyData['event_handler'])) {$intFieldId = $arrModifyData['event_handler'];} else {$intFieldId = 0;}
  $intReturn = $myVisClass->parseSelect('tbl_command','command_name','DAT_EVENTHANDLER','eventhandlerrow',$conttp,$chkListId,'',$intFieldId,1,0,4);
  if ($intReturn != 0) $strDBWarning .= gettext('Attention, no time periods defined!')."<br>";
  // Kontaktfelder füllen
  $intReturn1 = 0;
  $intReturn2 = 0;
  if (isset($arrModifyData['contacts'])) {$intFieldId = $arrModifyData['contacts'];} else {$intFieldId = 0;}
  $intReturn1 = $myVisClass->parseSelect('tbl_contact','contact_name','DAT_CONTACT','contacts',$conttp,$chkListId,'tbl_lnkServiceToContact',$intFieldId,0);
  if (isset($arrModifyData['contact_groups'])) {$intFieldId = $arrModifyData['contact_groups'];} else {$intFieldId = 0;}
  $intReturn2 = $myVisClass->parseSelect('tbl_contactgroup','contactgroup_name','DAT_CONTACTGROUPS','contactgroups',$conttp,$chkListId,'tbl_lnkServiceToContactgroup',$intFieldId,0);
  if (($intReturn1 != 0) && ($intReturn1 != 0)) $strDBWarning .= gettext('Attention, no contact or contact groups defined!')."<br>";
  // Feldbeschriftungen setzen
  foreach($arrDescription AS $elem) {
    $conttp->setVariable($elem['name'],str_replace("</","<\/",$elem['string']));
  }
  $conttp->setVariable("ORDER_BY",$_SESSION['serviceOrder']);
  $conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
  $conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
  $conttp->setVariable("DOCUMENT_ROOT",$SETS['path']['root']);
  $conttp->setVariable("IFRAME_SRC",$SETS['path']['root']."admin/commandline.php");
  $conttp->setVariable("LIMIT",$chkLimit);
  if ($strDBWarning != "") $conttp->setVariable("WARNING",$strDBWarning." ".gettext('Saving not possible!'));
  $conttp->setVariable("ACT_CHECKED","checked");
  if ($SETS['common']['seldisable'] == 1)$conttp->setVariable("SELECT_FIELD_DISABLED","disabled");
  if ($intVersion == 3) {
    $conttp->setVariable("CLASS_NAME_20","elementHide");
    $conttp->setVariable("CLASS_NAME_30","elementShow");
    $conttp->setVariable("VERSION","3");
  } else {
    $conttp->setVariable("CLASS_NAME_20","elementShow");
    $conttp->setVariable("CLASS_NAME_30","elementHide");
    $conttp->setVariable("MUST_20_STAR","*");
    $conttp->setVariable("VERSION","2");
  }
  // Statusfelder setzen
  $strStatusfelder = "ACE,PCE,PAC,FRE,OBS,EVH,FLE,STI,NSI,PED,ISV,NOE,HOS,HOG,SEG,COT,COG,TPL";
  foreach (explode(",",$strStatusfelder) AS $elem) {
    $conttp->setVariable("DAT_".$elem."0_CHECKED","");
    $conttp->setVariable("DAT_".$elem."1_CHECKED","");
    $conttp->setVariable("DAT_".$elem."2_CHECKED","checked");
  }
  $conttp->setVariable("MODUS","insert");
  // Im Modus "Modifizieren" die Datenfelder setzen
  if (isset($arrModifyData) && ($chkSelModify == "modify")) {
    foreach($arrModifyData AS $key => $value) {
      if (($key == "active") || ($key == "last_modified") || ($key == "access_rights")) continue;
      $conttp->setVariable("DAT_".strtoupper($key),htmlentities($value));
    }
    if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
    // Statusfelder setzen
    $strStatusfelder = "ACE,PCE,PCE,FRE,OBS,EVH,FLE,STI,NSI,PED,ISV,NOE,HOS,HOG,SEG,COT,COG,TPL";
    foreach (explode(",",$strStatusfelder) AS $elem) {
      $conttp->setVariable("DAT_".$elem."0_CHECKED","");
      $conttp->setVariable("DAT_".$elem."1_CHECKED","");
      $conttp->setVariable("DAT_".$elem."2_CHECKED","");
    }
    $conttp->setVariable("DAT_ACE".$arrModifyData['active_checks_enabled']."_CHECKED","checked");
    $conttp->setVariable("DAT_PCE".$arrModifyData['passive_checks_enabled']."_CHECKED","checked");
    $conttp->setVariable("DAT_PAC".$arrModifyData['parallelize_check']."_CHECKED","checked");
    $conttp->setVariable("DAT_FRE".$arrModifyData['check_freshness']."_CHECKED","checked");
    $conttp->setVariable("DAT_OBS".$arrModifyData['obsess_over_service']."_CHECKED","checked");
    $conttp->setVariable("DAT_EVH".$arrModifyData['event_handler_enabled']."_CHECKED","checked");
    $conttp->setVariable("DAT_FLE".$arrModifyData['flap_detection_enabled']."_CHECKED","checked");
    $conttp->setVariable("DAT_STI".$arrModifyData['retain_status_information']."_CHECKED","checked");
    $conttp->setVariable("DAT_NSI".$arrModifyData['retain_nonstatus_information']."_CHECKED","checked");
    $conttp->setVariable("DAT_PED".$arrModifyData['process_perf_data']."_CHECKED","checked");
    $conttp->setVariable("DAT_ISV".$arrModifyData['is_volatile']."_CHECKED","checked");
    $conttp->setVariable("DAT_NOE".$arrModifyData['notifications_enabled']."_CHECKED","checked");
    $conttp->setVariable("DAT_HOS".$arrModifyData['host_name_tploptions']."_CHECKED","checked");
    $conttp->setVariable("DAT_HOG".$arrModifyData['hostgroup_name_tploptions']."_CHECKED","checked");
    $conttp->setVariable("DAT_SEG".$arrModifyData['servicegroups_tploptions']."_CHECKED","checked");
    $conttp->setVariable("DAT_COT".$arrModifyData['contacts_tploptions']."_CHECKED","checked");
    $conttp->setVariable("DAT_COG".$arrModifyData['contact_groups_tploptions']."_CHECKED","checked");
    $conttp->setVariable("DAT_TPL".$arrModifyData['use_template_tploptions']."_CHECKED","checked");
    // Spezialfall -1 in Integerfeldern als "null" ausgeben
    $strIntegerfelder = "max_check_attempts,check_interval,retry_interval,freshness_threshold,low_flap_threshold,high_flap_threshold,notification_interval,first_notification_delay";
    foreach(explode(",",$strIntegerfelder) AS $elem) {
      if ($arrModifyData[$elem] == -1) {
        $conttp->setVariable("DAT_".strtoupper($elem),"null");
      }
    }
    if ($arrModifyData['check_command'] != "") {
      $arrArgument = explode("!",$arrModifyData['check_command']);
      foreach ($arrArgument AS $key => $value) {
        if ($key == 0) {
          $conttp->setVariable("IFRAME_SRC",$SETS['path']['root']."admin/commandline.php?cname=".$value);
        } else {
          $conttp->setVariable("DAT_ARG".$key,htmlentities($value));
        }
      }
    }
    // Prüfen, ob dieser Eintrag in einer anderen Konfiguration verwendet wird
    if ($myDataClass->infoRelation("tbl_service",$arrModifyData['id'],"config_name,service_description") != 0) {
      $conttp->setVariable("ACT_DISABLED","disabled");
      $conttp->setVariable("ACT_CHECKED","checked");
      $conttp->setVariable("ACTIVE","1");
      $strInfo = "<br><span class=\"dbmessage\">".gettext('Entry cannot be activated because it is used by another configuration').":</span><br><span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
      $conttp->setVariable("CHECK_MUST_DATA",$strInfo);
    }
    // Optionskästchen verarbeiten
    foreach(explode(",",$arrModifyData['initial_state']) AS $elem) {
      $conttp->setVariable("DAT_IS".strtoupper($elem)."_CHECKED","checked");
    }
    foreach(explode(",",$arrModifyData['flap_detection_options']) AS $elem) {
      $conttp->setVariable("DAT_FL".strtoupper($elem)."_CHECKED","checked");
    }
    foreach(explode(",",$arrModifyData['notification_options']) AS $elem) {
      $conttp->setVariable("DAT_NO".strtoupper($elem)."_CHECKED","checked");
    }
    foreach(explode(",",$arrModifyData['stalking_options']) AS $elem) {
      $conttp->setVariable("DAT_ST".strtoupper($elem)."_CHECKED","checked");
    }
    $conttp->setVariable("MODUS","modify");
  }
  $conttp->parse("datainsert");
  $conttp->show("datainsert");
}
//
// Datentabelle
// ============
// Titel setzen
if ($chkModus == "display") {
  // Feldbeschriftungen setzen
  foreach($arrDescription AS $elem) {
    $mastertp->setVariable($elem['name'],$elem['string']);
  }
  $mastertp->setVariable("FIELD_1",gettext('Config name'));
  $mastertp->setVariable("FIELD_2",gettext('Service name'));
  $mastertp->setVariable("LIMIT",$chkLimit);
  $mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
  $mastertp->setVariable("TABLE_NAME","tbl_service");
  $mastertp->setVariable("MAX_ID","0");
  $mastertp->setVariable("MIN_ID","0");
  $mastertp->setVariable("DAT_SEARCH",$_SESSION['serviceSearch']);
   // Configauswahl
  $mastertp->setVariable("DAT_CONFIGNAME",gettext('All configs'));
  if ($chkHostGiven == 0) $mastertp->setVariable("DAT_CONFIGNAME_SEL","selected");
  $mastertp->parse("configlist");
  $strSQL    = "SELECT DISTINCT `config_name` FROM `tbl_service` WHERE `config_id`=$chkDomainId ORDER BY `config_name`";
  $booReturn = $myDBClass->getDataArray($strSQL,$arrDataConfig,$intDataCount);
  if ($booReturn == false) {
    $strMessage .= gettext('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
  } else if ($intDataCount != 0) {
    $intRefreshConfig = 1;
	for ($i=0;$i<$intDataCount;$i++) {
      $mastertp->setVariable("DAT_CONFIGNAME",$arrDataConfig[$i]['config_name']);
      if ($_SESSION['serviceOrder'] == $arrDataConfig[$i]['config_name']) {
	  	$mastertp->setVariable("DAT_CONFIGNAME_SEL","selected");
	  	$intRefreshConfig = 0;
	  }
      $mastertp->parse("configlist");
    }
	if ($intRefreshConfig == 1) $strFilter = "";
  }
  // Anzahl Datensätze holen
  $strSearchWhere = "";
  if ($_SESSION['serviceSearch'] != "") {
    $strSearchWhere = "AND (`config_name` like '%".$_SESSION['serviceSearch']."%' OR `service_description` LIKE '%".$_SESSION['serviceSearch']."%' OR
                `display_name` LIKE '%".$_SESSION['serviceSearch']."%')";
  }
  $strSQL    = "SELECT count(*) AS `number` FROM `tbl_service` WHERE `config_id`=$chkDomainId $strFilter $strSearchWhere";
  $booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
  if ($booReturn == false) {
    $strMessage .= gettext('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
  } else {
    $intCount = (int)$arrDataLinesCount['number'];
  }
  // Datensätze holen
  if ($chkLimit > $intCount) $chkLimit =  0;
  $strSQL    = "SELECT `id`, `config_name`, `service_description`, `active`, `last_modified` FROM `tbl_service` WHERE `config_id`=$chkDomainId
         $strFilter $strSearchWhere ORDER BY `config_name`,`service_description` LIMIT $chkLimit,".$SETS['common']['pagelines'];
  $booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
  if ($booReturn == false) {
    $strMessage .= gettext('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
    $mastertp->setVariable("CELLCLASS_L","tdlb");
    $mastertp->setVariable("CELLCLASS_M","tdmb");
    $mastertp->setVariable("DISABLED","disabled");
  } else if ($intDataCount != 0) {
    $y=0; $z=0;
    for ($i=0;$i<$intDataCount;$i++) {
        // Grösste und kleinste ID heraussuchen
      if ($i == 0) {$y = $arrDataLines[$i]['id']; $z = $arrDataLines[$i]['id'];}
      if ($arrDataLines[$i]['id'] < $y) $y = $arrDataLines[$i]['id'];
      if ($arrDataLines[$i]['id'] > $z) $z = $arrDataLines[$i]['id'];
      $mastertp->setVariable("MAX_ID",$z);
      $mastertp->setVariable("MIN_ID",$y);
      // Jede zweite Zeile einfärben (Klassen setzen)
      $strClassL = "tdld"; $strClassM = "tdmd"; $strChbClass = "checkboxline";
      if ($i%2 == 1) {$strClassL = "tdlb"; $strClassM = "tdmb"; $strChbClass = "checkbox";}
      if ($arrDataLines[$i]['active'] == 0) {$strActive = gettext('No');} else {$strActive = gettext('Yes');}
      // Dateidatum holen
      $intDate = $myConfigClass->lastModifiedDir($arrDataLines[$i]['config_name'],$arrDataLines[$i]['id'],"service",$strTimeEntry,$strTimeFile,$intOlder);
      // Datenfelder setzen
      foreach($arrDescription AS $elem) {
        $mastertp->setVariable($elem['name'],$elem['string']);
      }
      $mastertp->setVariable("DATA_FIELD_1",htmlspecialchars($arrDataLines[$i]['config_name']));
	  $mastertp->setVariable("DATA_FIELD_1S",addslashes(htmlspecialchars($arrDataLines[$i]['config_name'])));
      $mastertp->setVariable("DATA_FIELD_2",htmlspecialchars($arrDataLines[$i]['service_description']));
	  $mastertp->setVariable("DATA_FIELD_2S",addslashes(htmlspecialchars($arrDataLines[$i]['service_description'])));
      $mastertp->setVariable("DATA_ACTIVE",$strActive);
      $mastertp->setVariable("DATA_FILE","<span class=\"dbmessage\">".gettext('out-of-date')."</span>");
      $mastertp->setVariable("LINE_ID",$arrDataLines[$i]['id']);
      $mastertp->setVariable("CELLCLASS_L",$strClassL);
      $mastertp->setVariable("CELLCLASS_M",$strClassM);
      $mastertp->setVariable("CHB_CLASS",$strChbClass);
      $mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
      if ($intOlder == 0) $mastertp->setVariable("DATA_FILE",gettext('up-to-date'));
      if ($intDate  == 1) $mastertp->setVariable("DATA_FILE","<span class=\"dbmessage\">".gettext('missed')."</span>");
      if ($chkModus != "display") $mastertp->setVariable("DISABLED","disabled");
      $mastertp->parse("datarowservice");
    }
  } else {
    $mastertp->setVariable("DATA_FIELD_1",gettext('No data'));
    $mastertp->setVariable("DATA_FIELD_2","&nbsp;");
    $mastertp->setVariable("DATA_ACTIVE","&nbsp;");
    $mastertp->setVariable("DATA_FILE","&nbsp;");
    $mastertp->setVariable("CELLCLASS_L","tdlb");
    $mastertp->setVariable("CELLCLASS_M","tdmb");
    $mastertp->setVariable("CHB_CLASS","checkbox");
    $mastertp->setVariable("DISABLED","disabled");
  }
  $mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
  if (isset($intCount)) $mastertp->setVariable("PAGES",$myVisClass->buildPageLinks($_SERVER['PHP_SELF'],$intCount,$chkLimit,$_SESSION['serviceOrder']));
  $mastertp->parse("datatableservice");
  $mastertp->show("datatableservice");
}
// Mitteilungen ausgeben
if (isset($strMessage) && ($strMessage != "")) $mastertp->setVariable("DBMESSAGE",$strMessage);
$mastertp->parse("msgfooterhost");
$mastertp->show("msgfooterhost");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>