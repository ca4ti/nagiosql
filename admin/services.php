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
// Datum:	25.09.2007
// Zweck:	Services definieren
// Datei:	admin/services.php
// Version: 2.01.00 (Internal)
// SV:		$Id: services.php 65 2008-03-31 13:41:15Z rouven $
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 2;
$intSub  		= 7;
$intMenu 		= 2;
$preContent 	= "services.tpl.htm";
$strDBWarning	= "";
$intCount		= 0;
$strMessage		= "";
//
// Vorgabedatei einbinden
// ======================
$preAccess	= 1;
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkHostGiven			= isset($_POST['hostGiven'])			? $_POST['hostGiven']						: 0;
$chkOldConfig			= isset($_POST['hidConfigname'])		? addslashes($_POST['hidConfigname'])		: "";
$chkTfConfigName		= isset($_POST['tfConfigName']) 		? addslashes($_POST['tfConfigName'])		: "";
$chkTfService 			= isset($_POST['tfService']) 			? addslashes($_POST['tfService'])			: "";
$chkTfArg1				= isset($_POST['tfArg1']) 				? addslashes($_POST['tfArg1'])				: "";
$chkTfArg2				= isset($_POST['tfArg2']) 				? addslashes($_POST['tfArg2']) 				: "";
$chkTfArg3				= isset($_POST['tfArg3']) 				? addslashes($_POST['tfArg3']) 				: "";
$chkTfArg4				= isset($_POST['tfArg4']) 				? addslashes($_POST['tfArg4']) 				: "";
$chkTfArg5				= isset($_POST['tfArg5']) 				? addslashes($_POST['tfArg5']) 				: "";
$chkTfArg6				= isset($_POST['tfArg6']) 				? addslashes($_POST['tfArg6']) 				: "";
$chkTfArg7				= isset($_POST['tfArg7']) 				? addslashes($_POST['tfArg7']) 				: "";
$chkTfArg8				= isset($_POST['tfArg8']) 				? addslashes($_POST['tfArg8']) 				: "";
$chkSelHostGroups 		= isset($_POST['selHostGroups'])		? $_POST['selHostGroups']					: array("");
$chkSelServiceGroups 	= isset($_POST['selServiceGroups'])		? $_POST['selServiceGroups']				: array("");
$chkSelContactGroups 	= isset($_POST['selContactGroups']) 	? $_POST['selContactGroups'] 				: array("");
$chkSelHosts			= isset($_POST['selHosts']) 			? $_POST['selHosts'] 						: array("");
$chkSelCheckPeriod 		= isset($_POST['selCheckPeriod']) 		? $_POST['selCheckPeriod'] 					: "";
$chkSelEventHandler 	= isset($_POST['selEventHandler']) 		? $_POST['selEventHandler']					: "";
$chkSelNotifPeriod 		= isset($_POST['selNotifPeriod']) 		? $_POST['selNotifPeriod'] 					: "";
$chkSelServiceCommand	= isset($_POST['selServiceCommand']) 	? $_POST['selServiceCommand'] 				: "";
$chkSelOrderBy			= isset($_POST['selOrderBy']) 			? $_POST['selOrderBy'] 						: "";
$chkTfMaxCheckAttempts	= (isset($_POST['tfMaxCheckAttempts']) 	&& ($_POST['tfMaxCheckAttempts'] != ""))	? $_POST['tfMaxCheckAttempts']	: "NULL";
$chkTfNormCheckInt 		= (isset($_POST['tfNormCheckInt'])		&& ($_POST['tfNormCheckInt'] != ""))		? $_POST['tfNormCheckInt']		: "NULL";
$chkTfRetryCheckInt		= (isset($_POST['tfRetryCheckInt'])		&& ($_POST['tfRetryCheckInt'] != ""))		? $_POST['tfRetryCheckInt']		: "NULL";
$chkTfLowFlat			= (isset($_POST['tfLowFlat'])			&& ($_POST['tfLowFlat'] != ""))				? $_POST['tfLowFlat']			: "NULL";
$chkTfHighFlat			= (isset($_POST['tfHighFlat'])			&& ($_POST['tfHighFlat'] != ""))			? $_POST['tfHighFlat']			: "NULL";
$chkTfFreshTreshold		= (isset($_POST['tfFreshTreshold'])		&& ($_POST['tfFreshTreshold'] != ""))		? $_POST['tfFreshTreshold']		: "NULL";
$chkNotifIntervall		= (isset($_POST['tfNotifIntervall'])	&& ($_POST['tfNotifIntervall'] != "")) 		? $_POST['tfNotifIntervall']	: "NULL";
$chkNOw					= isset($_POST['chbNOw'])				? $_POST['chbNOw'].","						: "";
$chkNOu					= isset($_POST['chbNOu'])				? $_POST['chbNOu'].","						: "";
$chkNOc					= isset($_POST['chbNOc'])				? $_POST['chbNOc'].","						: "";
$chkNOr					= isset($_POST['chbNOr'])				? $_POST['chbNOr'].","						: "";
$chkNOf					= isset($_POST['chbNOf'])				? $_POST['chbNOf'].","						: "";
$chkSOo					= isset($_POST['chbSOo'])				? $_POST['chbSOo'].","						: "";
$chkSOw					= isset($_POST['chbSOw'])				? $_POST['chbSOw'].","						: "";
$chkSOu					= isset($_POST['chbSOu'])				? $_POST['chbSOu'].","						: "";
$chkSOc					= isset($_POST['chbSOc'])				? $_POST['chbSOc'].","						: "";
$chkActiveChecks		= isset($_POST['chbActiveChecks'])		? $_POST['chbActiveChecks']					: 0;
$chkPassiveChecks		= isset($_POST['chbPassiveChecks'])		? $_POST['chbPassiveChecks']				: 0;
$chkIsVolatile			= isset($_POST['chbIsVolatile'])		? $_POST['chbIsVolatile']					: 0;
$chkParallelize			= isset($_POST['chbParallelize'])		? $_POST['chbParallelize']					: 0;
$chkEventEnable			= isset($_POST['chbEventEnable'])		? $_POST['chbEventEnable']					: 0;
$chkFreshness			= isset($_POST['chbFreshness'])			? $_POST['chbFreshness']					: 0;
$chkObsess				= isset($_POST['chbObsess'])			? $_POST['chbObsess']						: 0;
$chkPerfData			= isset($_POST['chbPerfData'])			? $_POST['chbPerfData']						: 0;
$chkFlapEnable			= isset($_POST['chbFlapEnable'])		? $_POST['chbFlapEnable']					: 0;
$chkStatusInfos			= isset($_POST['chbStatusInfos'])		? $_POST['chbStatusInfos']					: 0;
$chkNonStatusInfos		= isset($_POST['chbNonStatusInfos'])	? $_POST['chbNonStatusInfos']				: 0;
$chkNotifEnabled		= isset($_POST['chbNotifEnabled'])		? $_POST['chbNotifEnabled']					: 0;
$chkSelOrderByGet		= isset($_GET['orderby'])				? rawurldecode($_GET['orderby'])			: "";
//
// Daten verarbeiten
// =================
$strFilter = "";
$strNO 	   = substr($chkNOw.$chkNOu.$chkNOc.$chkNOr.$chkNOf,0,-1);
$strSO 	   = substr($chkSOo.$chkSOw.$chkSOu.$chkSOc,0,-1);
if ($chkModus == "add") $chkSelModify = "";
// Filter definieren
if ($chkSelOrderByGet != "") $chkSelOrderBy=$chkSelOrderByGet;
if (($chkSelOrderBy != "") && ($chkSelOrderBy != $LANG['admintable']['allconfigs'])){
	$strFilter    = "WHERE config_name='".$chkSelOrderBy."' ";
} 
// Strings zusammenstellen
if (($chkSelHosts[0]         == "") || ($chkSelHosts[0]         == "0")) {$intSelHosts = 0;}  	 	 else {$intSelHosts = 1;}
if (($chkSelHostGroups[0]    == "") || ($chkSelHostGroups[0]    == "0")) {$intSelHostGroups = 0;}    else {$intSelHostGroups = 1;}
if (($chkSelServiceGroups[0] == "") || ($chkSelServiceGroups[0] == "0")) {$intServiceGroups = 0;}    else {$intServiceGroups = 1;}
if (($chkSelContactGroups[0] == "") || ($chkSelContactGroups[0] == "0")) {$intSelContactGroups = 0;} else {$intSelContactGroups = 1;}
if ($chkSelHosts[0]          == "*") $intSelHosts = 2;
if ($chkSelHostGroups[0]     == "*") $intSelHostGroups = 2;
// Checkcommand zusammenstellen
$strCheckCommand = $chkSelServiceCommand;
if ($chkSelServiceCommand != "") {
	for ($i=1;$i<=8;$i++) {
		if (${"chkTfArg$i"} != "") $strCheckCommand .= "!".${"chkTfArg$i"};
	}
}
// Leerzeichen aus dem Konfigurationsnamen entfernen
$chkTfConfigName = str_replace(" ","_",$chkTfConfigName);
// Daten Einfügen oder Aktualisieren
if (($chkModus == "insert") || ($chkModus == "modify")) {
	if ($hidActive == 1) $chkActive = 1;
	$strSQLx = "tbl_service SET host_name=$intSelHosts, hostgroup_name=$intSelHostGroups, service_description='$chkTfService', 
				config_name='$chkTfConfigName', servicegroups=$intServiceGroups, is_volatile='$chkIsVolatile', check_command='$strCheckCommand', 
				max_check_attempts=$chkTfMaxCheckAttempts, normal_check_interval=$chkTfNormCheckInt, retry_check_interval=$chkTfRetryCheckInt, 
				active_checks_enabled='$chkActiveChecks', passive_checks_enabled='$chkPassiveChecks', check_period='$chkSelCheckPeriod', 
				parallelize_check='$chkParallelize', obsess_over_service='$chkObsess', check_freshness='$chkFreshness', 
				freshness_threshold=$chkTfFreshTreshold, event_handler='$chkSelEventHandler', event_handler_enabled='$chkEventEnable', 
				low_flap_threshold=$chkTfLowFlat, high_flap_threshold=$chkTfHighFlat, flap_detection_enabled='$chkFlapEnable', 
				process_perf_data='$chkPerfData', retain_status_information='$chkStatusInfos', retain_nonstatus_information='$chkNonStatusInfos', 
				contact_groups=$intSelContactGroups, notification_interval=$chkNotifIntervall, notification_period='$chkSelNotifPeriod', 
				notification_options='$strNO', notifications_enabled='$chkNotifEnabled', stalking_options='$strSO', 
				active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL = "INSERT INTO ".$strSQLx; 
	} else {
		$strSQL = "UPDATE ".$strSQLx." WHERE id=$chkDataId";   
	}	
	if ((($intSelHosts != 0) || ($intSelHostGroups != 0)) && ($chkTfService != "") && ($chkSelCheckPeriod != "") && ($chkTfMaxCheckAttempts != "NULL") &&
	    ($chkTfNormCheckInt  != "NULL") && ($chkTfRetryCheckInt  != "NULL") && ($chkSelNotifPeriod != "") && 
		($chkSelServiceCommand != "") && ($chkNotifIntervall != "NULL") && ($strNO != "") && ($intSelContactGroups != 0)) {
		$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		if ($intInsert == 1) {
			$intReturn = 1;
		} else {
			if ($chkModus  == "insert") 	$myDataClass->writeLog($LANG['logbook']['newservice']." ".$chkTfConfigName);
			if ($chkModus  == "modify") 	$myDataClass->writeLog($LANG['logbook']['modifyservice']." ".$chkTfConfigName);
			//
			// Relationen eintragen/updaten
			// ============================
			$intTableA = $myDataClass->tableID("tbl_service");
			if ($chkModus == "insert") {
				if ($intSelHosts         == 1)	$myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_host"),$intInsertId,'host_name',$chkSelHosts);
				if ($intSelHostGroups    == 1) 	$myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$intInsertId,'hostgroup_name',$chkSelHostGroups);
				if ($intServiceGroups    == 1) 	$myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_servicegroup"),$intInsertId,'servicegroups',$chkSelServiceGroups);
				if ($intSelContactGroups == 1) 	$myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_contactgroup"),$intInsertId,'contact_groups',$chkSelContactGroups);
			} else if ($chkModus == "modify") {		
				if ($intSelHosts == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'host_name',$chkSelHosts);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'host_name');
				}
				if ($intSelHostGroups == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$chkDataId,'hostgroup_name',$chkSelHostGroups);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$chkDataId,'hostgroup_name');			
				}
				if ($intServiceGroups == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_servicegroup"),$chkDataId,'servicegroups',$chkSelServiceGroups);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_servicegroup"),$chkDataId,'servicegroups');			
				}			
				if ($intSelContactGroups == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_contactgroup"),$chkDataId,'contact_groups',$chkSelContactGroups);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_contactgroup"),$chkDataId,'contact_groups');			
				}
			}		
			$intReturn = 0;
			// Falls Konfigurationsname geändert wurde und kein weiterer Service mit diesem Konfigurationsnamen besteht, 
			// alte Konfigurationsdatei löschen		
			if (($chkModus == "modify") && ($chkOldConfig != $chkTfConfigName)) {
				$intServiceCount = $myDBClass->countRows("SELECT * FROM tbl_service WHERE BINARY config_name='$chkOldConfig'");
				if ($intServiceCount == 0) {
					$strOldDate    = date("YmdHis",mktime());
					$strFilename   = $SETS['nagios']['configservices'].$chkOldConfig.".cfg";
					$strBackupfile = $SETS['nagios']['backupservices'].$chkOldConfig.".cfg_old_".$strOldDate;
					if (file_exists($strFilename) && (is_writable($strFilename)) && (is_writable($SETS['nagios']['backupservices']))) {
						copy($strFilename,$strBackupfile);
						unlink($strFilename);
						$myDataClass->strDBMessage .= "<br>".$LANG['file']['success_del'];
						$myDataClass->writeLog($LANG['logbook']['delservice']." ".$strFilename);
					} else if (file_exists($strFilename)) {
						$myDataClass->strDBMessage .= "<br>".$LANG['file']['failed_del'];
						$intReturn = 1;
					}
				}
			}
		}
	} else {
		$strMessage .= $LANG['db']['datamissing'];
	}
	$chkModus = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$intReturn = $myDataClass->dataDeleteSimple("tbl_service",$chkListId);
	$intResult = $myDBClass->getFieldData("SELECT count(*) FROM tbl_service WHERE config_name='$chkSelOrderBy'");
	if ($intResult == 0) $strFilter = "";
	$chkModus  = "display";	
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$intReturn = $myDataClass->dataCopySimple("tbl_service",$chkListId);
	$chkModus  = "display";
} else if ($chkModus == "make") {
	// Servicekonfigurationen schreiben
	$strSQL   = "SELECT id, config_name FROM tbl_service WHERE active='1' GROUP BY config_name";
	$myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
	$intError = 0;
	if ($intDataCount != 0) {
		foreach ($arrData AS $data) {
			$myConfigClass->createConfigSingle("tbl_service",$data['id']);
			if ($myConfigClass->strDBMessage != $LANG['file']['success']) $intError++;
		}
	}
	if ($intError == 0) {
		$myDataClass->strDBMessage .= $LANG['file']['success']."<br>";
	} else {
		$myDataClass->strDBMessage .= $LANG['file']['failed']."<br>";
	}
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_service WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) {
		$myDataClass->strDBMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";	
		$intReturn = 1;
	}	
	$chkModus      = "add";
} else if (($chkModus == "checkform") && ($chkSelModify == "config")) {
	// Konfiguration schreiben
	$intDSId    = (int)substr(array_search("on",$_POST),6);
	if (isset($chkListId) && ($chkListId != 0)) $intDSId = $chkListId;
	// Prüfen ob es noch aktive Konfigurationen gibt
	$booReturn = $myDBClass->getSingleDataset("SELECT config_name, active FROM tbl_service WHERE id=".$intDSId,$arrModifyData);
	$myDBClass->getSingleDataset("SELECT count(*) AS counter FROM tbl_service WHERE config_name='".$arrModifyData['config_name']."' AND active='1'",$arrActiveData);
	// Falls es keine aktiven Konfigurationen mehr gibt, das Konfigurationsfile löschen
	if ($arrActiveData['counter'] == 0) {
		$strOldDate    = date("YmdHis",mktime());
		$strFilename   = $SETS['nagios']['configservices'].$arrModifyData['config_name'].".cfg";
		$strBackupfile = $SETS['nagios']['backupservices'].$arrModifyData['config_name'].".cfg_old_".$strOldDate;
		if (file_exists($strFilename) && (is_writable($strFilename)) && (is_writable($SETS['nagios']['backupservices']))) {
			copy($strFilename,$strBackupfile);
			unlink($strFilename);
			$myDataClass->strDBMessage .= "<br>".$LANG['file']['success_del'];
			$myDataClass->writeLog($LANG['logbook']['delservice']." ".$strFilename);
		} else if (file_exists($strFilename)) {
			$myDataClass->strDBMessage .= "<br>".$LANG['file']['failed_del'];
			$intReturn = 1;
		}
	// Falls es aktive Konfiguration mehr gibt, das Konfigurationsfile löschen
	} else {
		$intReturn = $myConfigClass->createConfigSingle("tbl_service",$intDSId);
		$myDataClass->strDBMessage = $myConfigClass->strDBMessage;
	}
	$chkModus   = "display";
}  else if ($chkModus == "filter") {
	// Filtereinstellungen definieren
	if (($chkSelOrderBy != "") && ($chkSelOrderBy != $LANG['admintable']['allconfigs'])){
		$strFilter    = "WHERE config_name='".$chkSelOrderBy."' ";
	} else if ($chkSelOrderBy == $LANG['admintable']['allconfigs']) {
		$chkSelOrderBy = "";
	}
	$chkModus   = "display";
}
// Statusmitteilungen setzen
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $myDataClass->strDBMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm2']." -> ".$LANG['menu']['item_admsub7']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['service']);
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if ($chkModus == "add") {
	// Klassenvariabeln definieren
	$myVisClass->resTemplate     =& $conttp;
	$myVisClass->strTempValue1   = $chkSelModify;
	$myVisClass->strTempValue2   = $chkModus;
	$myVisClass->intTabA   	     = $myDataClass->tableID("tbl_service");
	if (isset($arrModifyData)) {
		$myVisClass->arrWorkdata = $arrModifyData;
		$myVisClass->intTabA_id  = $arrModifyData['id'];
	} else {
		$myVisClass->intTabA_id  = 0;
	}
	// Hostauswahlfelder füllen
	$intReturn = 0;
	$intReturn = $myVisClass->parseSelectNew('tbl_host','host_name','DAT_HOST','hostname','host_name',2,2);
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_host']."<br>";	
    // Hostgruppenauswahlfelder füllen
	$intReturn2 = 0;
	$intReturn2 = $myVisClass->parseSelectNew('tbl_hostgroup','hostgroup_name','DAT_HOSTGROUPS','hostgroups','hostgroup_name',2,2);
	if (($intReturn != 0) && ($intReturn2 != 0)) $strDBWarning .= $LANG['admintable']['warn_host_groups']."<br>";
	// Servicegruppenauswahlfelder füllen
	$intReturn = $myVisClass->parseSelectNew('tbl_servicegroup','servicegroup_name','DAT_SERVICEGROUPITEM','servicegroups','servicegroups',2,1);
	// Servicecommandfelder füllen
	$intReturn = 0;
	$strFirstServiceCommand = "";
	$intReturn = $myVisClass->parseSelectNew('tbl_checkcommand','command_name','DAT_SERVICE_COMMAND','servicecommand','check_command',1,0);
	$strFirstServiceCommand = $myVisClass->strTempValue2;
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_command']."<br>";
	// Teiperiodenauswahlfelder füllen
	$intReturn = 0;
	$intReturn = $myVisClass->parseSelectNew('tbl_timeperiod','timeperiod_name','DAT_CHECK_PERIODS','checkperiod','check_period',1,0);
	$intReturn = $myVisClass->parseSelectNew('tbl_timeperiod','timeperiod_name','DAT_NOTIF_PERIOD','notifperiod','notification_period',1,0);
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_timeperiod']."<br>";
	// Eventhandlerauswahlfelder füllen
	$intReturn = $myVisClass->parseSelectNew('tbl_misccommand','command_name','DAT_EVENT_HANDLERITEM','eventhandler','event_handler',1,1);
	// Contactgruppenauswahlfelder füllen
	$intReturn = 0;
	$intReturn = $myVisClass->parseSelectNew('tbl_contactgroup','contactgroup_name','DAT_CONTACTGROUPS','contactgroups','contact_groups',2,0);
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_contgroups']."<br>";		
	// Feldbeschriftungen setzen
	foreach($LANG['admintable'] AS $key => $value) {
		$conttp->setVariable("LANG_".strtoupper($key),$value);
	}
	foreach($LANG['formchecks'] AS $key => $value) {
		$conttp->setVariable(strtoupper($key),$value);
	}
	$conttp->setVariable("ORDER_BY",$chkSelOrderBy);
	$conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
	$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	$conttp->setVariable("DOCUMENT_ROOT",$SETS['path']['root']);
	$conttp->setVariable("IFRAME_SRC",$SETS['path']['root']."admin/commandline.php");
	$conttp->setVariable("LIMIT",$chkLimit);
	if ($strDBWarning != "") $conttp->setVariable("WARNING",$strDBWarning.$LANG['admintable']['warn_save']);
	$conttp->setVariable("IFRAME_SRC",$SETS['path']['root']."admin/commandline.php?cname=".$strFirstServiceCommand);
	$conttp->setVariable("ACT_CHECKED","checked");
	$conttp->setVariable("ACTIVE_CHECKS_CHECKED","checked");
	$conttp->setVariable("PASSIVE_CHECKS_CHECKED","checked");
	$conttp->setVariable("NOTIF_CHECKED","checked");
	$conttp->setVariable("OBESS_CHECKED","checked");
	$conttp->setVariable("PARALLELIZE_CHECKED","checked");
	$conttp->setVariable("EVENTHANDLER_CHECKED","checked");
	$conttp->setVariable("FLAP_CHECKED","checked");
	$conttp->setVariable("STATUS_CHECKED","checked");
	$conttp->setVariable("NONSTATUS_CHECKED","checked");
	$conttp->setVariable("MODUS","insert");
	// Im Modus "Modifizieren" die Datenfelder setzen
	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		foreach($arrModifyData AS $key => $value) {
			if (($key == "active") || ($key == "last_modified") || ($key == "access_rights")) continue;
			$conttp->setVariable("DAT_".strtoupper($key),htmlspecialchars(stripslashes($value)));
		}
		if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
		if ($arrModifyData['active_checks_enabled'] != 1) $conttp->setVariable("ACTIVE_CHECKS_CHECKED","");
		if ($arrModifyData['passive_checks_enabled'] != 1) $conttp->setVariable("PASSIVE_CHECKS_CHECKED","");
		if ($arrModifyData['obsess_over_service'] != 1) $conttp->setVariable("OBESS_CHECKED","");
		if ($arrModifyData['check_freshness'] == 1) $conttp->setVariable("FRESHNESS_CHECKED","checked");
		if ($arrModifyData['parallelize_check'] != 1) $conttp->setVariable("PARALLELIZE_CHECKED","");
		if ($arrModifyData['is_volatile'] == 1) $conttp->setVariable("VOLATILE_CHECKED","checked");
		if ($arrModifyData['event_handler_enabled'] != 1) $conttp->setVariable("EVENTHANDLER_CHECKED","");
		if ($arrModifyData['flap_detection_enabled'] != 1) $conttp->setVariable("FLAP_CHECKED","");
		if ($arrModifyData['process_perf_data'] == 1) $conttp->setVariable("PERF_CHECKED","checked");
		if ($arrModifyData['retain_status_information'] != 1) $conttp->setVariable("STATUS_CHECKED","");
		if ($arrModifyData['retain_nonstatus_information'] != 1) $conttp->setVariable("NONSTATUS_CHECKED","");		 
		if ($arrModifyData['notifications_enabled'] != 1) $conttp->setVariable("NOTIF_CHECKED","");		
		if ($arrModifyData['check_command'] != "") {
			$arrArgument = explode("!",stripslashes($arrModifyData['check_command']));
			foreach ($arrArgument AS $key => $value) {
				if ($key == 0) {
					$conttp->setVariable("IFRAME_SRC",$SETS['path']['root']."admin/commandline.php?cname=".$value);
				} else {
					$conttp->setVariable("DAT_ARG".$key,htmlspecialchars($value));
				}
			}
		}
		// Prüfen, ob dieser Eintrag in einer anderen Konfiguration verwendet wird
		if ($myDataClass->checkMustdata("tbl_service",$arrModifyData['id'],$arrInfo) != 0) {
			$conttp->setVariable("ACT_DISABLED","disabled");
			$conttp->setVariable("ACTIVE","1");
			$conttp->setVariable("CHECK_MUST_DATA","<span class=\"dbmessage\">".$LANG['admintable']['noactivate']."</span>");
			$conttp->setVariable("CHECK_MUST_DATA2","<span class=\"dbmessage\">".$LANG['admintable']['warnmusthost']."</span>");
		} 
		// Optionskästchen verarbeiten		
		foreach(explode(",",$arrModifyData['notification_options']) AS $elem) {
			$conttp->setVariable("DAT_NO".strtoupper($elem)."_CHECKED","checked");
		}
		foreach(explode(",",$arrModifyData['stalking_options']) AS $elem) {
			$conttp->setVariable("DAT_SO".strtoupper($elem)."_CHECKED","checked");
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
	foreach($LANG['admintable'] AS $key => $value) {
		$mastertp->setVariable("LANG_".strtoupper($key),$value);
	}  
	$mastertp->setVariable("FIELD_1",$LANG['admintable']['configname']);
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['service']);	
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);
	$mastertp->setVariable("WRITE_CONFIG",$LANG['admintable']['write_conf']);
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_service");
	$mastertp->setVariable("MAX_ID","0");
	$mastertp->setVariable("MIN_ID","0");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_service ".$strFilter;
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
	// Datensätze holen
	$strSQL    = "SELECT id, config_name, service_description, active, last_modified FROM tbl_service 
				 ".$strFilter."ORDER BY config_name,service_description LIMIT $chkLimit,".$SETS['common']['pagelines'];
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
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
			if ($arrDataLines[$i]['active'] == 0) {$strActive = $LANG['common']['no_nak'];} else {$strActive = $LANG['common']['yes_ok'];}	
			// Dateidatum holen
			$myConfigClass->lastModifiedDir($arrDataLines[$i]['config_name'],$arrDataLines[$i]['id'],"service",$strTimeEntry,$strTimeFile,$intOlder);
			// Datenfelder setzen
			foreach($LANG['admintable'] AS $key => $value) {
				$mastertp->setVariable("LANG_".strtoupper($key),$value);
			} 
			$mastertp->setVariable("DATA_FIELD_1",stripslashes($arrDataLines[$i]['config_name']));
			$mastertp->setVariable("DATA_FIELD_2",stripslashes($arrDataLines[$i]['service_description']));
			$mastertp->setVariable("DATA_ACTIVE",$strActive);
			$mastertp->setVariable("DATA_FILE","<span class=\"dbmessage\">".$LANG['admintable']['file_old']."</span>");
			$mastertp->setVariable("LINE_ID",$arrDataLines[$i]['id']);
			$mastertp->setVariable("CELLCLASS_L",$strClassL);
			$mastertp->setVariable("CELLCLASS_M",$strClassM);
			$mastertp->setVariable("CHB_CLASS",$strChbClass);
			$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
			if ($intOlder == 0) $mastertp->setVariable("DATA_FILE",$LANG['admintable']['file_io']);
			if ($chkModus != "display") $mastertp->setVariable("DISABLED","disabled");		
			$mastertp->parse("datarowservice");		
		}
	} else {
		$mastertp->setVariable("DATA_FIELD_1",$LANG['admintable']['nodata']);
		$mastertp->setVariable("DATA_FIELD_2","&nbsp;");
		$mastertp->setVariable("DATA_ACTIVE","&nbsp;");
		$mastertp->setVariable("DATA_FILE","&nbsp;");
		$mastertp->setVariable("CELLCLASS_L","tdlb");
		$mastertp->setVariable("CELLCLASS_M","tdmb");
		$mastertp->setVariable("CHB_CLASS","checkbox");
		$mastertp->setVariable("DISABLED","disabled");
	}
	// Configauswahl
	$mastertp->setVariable("DAT_CONFIGNAME",$LANG['admintable']['allconfigs']);
	if ($chkHostGiven == 0) $mastertp->setVariable("DAT_CONFIGNAME_SEL","selected");
	$mastertp->parse("configlist");
	$strSQL    = "SELECT DISTINCT config_name FROM tbl_service ORDER BY config_name";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataConfig,$intDataCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
	} else if ($intDataCount != 0) {	
		for ($i=0;$i<$intDataCount;$i++) {
			$mastertp->setVariable("DAT_CONFIGNAME",$arrDataConfig[$i]['config_name']);
			if ($chkSelOrderBy == $arrDataConfig[$i]['config_name']) $mastertp->setVariable("DAT_CONFIGNAME_SEL","selected");
			$mastertp->parse("configlist");
		}
	}
	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	if (isset($intCount)) $mastertp->setVariable("PAGES",$myVisClass->buildPageLinks($_SERVER['PHP_SELF'],$intCount,$chkLimit,$chkSelOrderBy));
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
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org'>NagiosQL</a> - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>