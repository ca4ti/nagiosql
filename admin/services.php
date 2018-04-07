<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL 2005
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005 by Martin Willisegger / nagios.ql2005@wizonet.ch
//
// Projekt:	Nagios NG Applikation
// Author :	Martin Willisegger
// Datum:	30.03.2005
// Zweck:	Services definieren
// Datei:	admin/services.php
// Version:	1.02
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
$setFileVersion = "1.02";
$strDBWarning	= "";
$intCount		= 0;
$strMessage		= "";
//
// Vorgabedatei einbinden
// ======================
$preRights 	= "admin1";
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkHostGiven			= isset($_POST['hostGiven'])			? $_POST['hostGiven']			: 0;
$chkOldConfig			= isset($_POST['hidConfigname'])		? $_POST['hidConfigname']		: "";
$chkTfConfigName		= isset($_POST['tfConfigName']) 		? $_POST['tfConfigName']		: "";
$chkTfService 			= isset($_POST['tfService']) 			? $_POST['tfService'] 			: "";
$chkTfArg1				= isset($_POST['tfArg1']) 				? $_POST['tfArg1'] 				: "";
$chkTfArg2				= isset($_POST['tfArg2']) 				? $_POST['tfArg2'] 				: "";
$chkTfArg3				= isset($_POST['tfArg3']) 				? $_POST['tfArg3'] 				: "";
$chkTfArg4				= isset($_POST['tfArg4']) 				? $_POST['tfArg4'] 				: "";
$chkTfArg5				= isset($_POST['tfArg5']) 				? $_POST['tfArg5'] 				: "";
$chkTfArg6				= isset($_POST['tfArg6']) 				? $_POST['tfArg6'] 				: "";
$chkTfArg7				= isset($_POST['tfArg7']) 				? $_POST['tfArg7'] 				: "";
$chkTfArg8				= isset($_POST['tfArg8']) 				? $_POST['tfArg8'] 				: "";
$chkSelHostGroups 		= isset($_POST['selHostGroups'])		? $_POST['selHostGroups']		: array("");
$chkSelServiceGroups 	= isset($_POST['selServiceGroups'])		? $_POST['selServiceGroups']	: array("");
$chkSelContactGroups 	= isset($_POST['selContactGroups']) 	? $_POST['selContactGroups'] 	: array("");
$chkSelHosts			= isset($_POST['selHosts']) 			? $_POST['selHosts'] 			: array("");
$chkSelCheckPeriod 		= isset($_POST['selCheckPeriod']) 		? $_POST['selCheckPeriod'] 		: "";
$chkSelEventHandler 	= isset($_POST['selEventHandler']) 		? $_POST['selEventHandler']		: "";
$chkSelNotifPeriod 		= isset($_POST['selNotifPeriod']) 		? $_POST['selNotifPeriod'] 		: "";
$chkSelServiceCommand	= isset($_POST['selServiceCommand']) 	? $_POST['selServiceCommand'] 	: "";
$chkSelOrderBy			= isset($_POST['selOrderBy']) 			? $_POST['selOrderBy'] 			: "";
$chkTfMaxCheckAttempts	= (isset($_POST['tfMaxCheckAttempts']) 	&& ($_POST['tfMaxCheckAttempts'] != ""))	? $_POST['tfMaxCheckAttempts']	: "NULL";
$chkTfNormCheckInt 		= (isset($_POST['tfNormCheckInt'])		&& ($_POST['tfNormCheckInt'] != ""))		? $_POST['tfNormCheckInt']		: "NULL";
$chkTfRetryCheckInt		= (isset($_POST['tfRetryCheckInt'])		&& ($_POST['tfRetryCheckInt'] != ""))		? $_POST['tfRetryCheckInt']		: "NULL";
$chkTfLowFlat			= (isset($_POST['tfLowFlat'])			&& ($_POST['tfLowFlat'] != ""))				? $_POST['tfLowFlat']			: "NULL";
$chkTfHighFlat			= (isset($_POST['tfHighFlat'])			&& ($_POST['tfHighFlat'] != ""))			? $_POST['tfHighFlat']			: "NULL";
$chkTfFreshTreshold		= (isset($_POST['tfFreshTreshold'])		&& ($_POST['tfFreshTreshold'] != ""))		? $_POST['tfFreshTreshold']		: "NULL";
$chkNotifIntervall		= (isset($_POST['tfNotifIntervall'])	&& ($_POST['tfNotifIntervall'] != "")) 		? $_POST['tfNotifIntervall']	: "NULL";
$chkNOw					= isset($_POST['chbNOw'])				? $_POST['chbNOw'].","			: "";
$chkNOu					= isset($_POST['chbNOu'])				? $_POST['chbNOu'].","			: "";
$chkNOc					= isset($_POST['chbNOc'])				? $_POST['chbNOc'].","			: "";
$chkNOr					= isset($_POST['chbNOr'])				? $_POST['chbNOr'].","			: "";
$chkNOf					= isset($_POST['chbNOf'])				? $_POST['chbNOf'].","			: "";
$chkSOo					= isset($_POST['chbSOo'])				? $_POST['chbSOo'].","			: "";
$chkSOw					= isset($_POST['chbSOw'])				? $_POST['chbSOw'].","			: "";
$chkSOu					= isset($_POST['chbSOu'])				? $_POST['chbSOu'].","			: "";
$chkSOc					= isset($_POST['chbSOc'])				? $_POST['chbSOc'].","			: "";
$chkActiveChecks		= isset($_POST['chbActiveChecks'])		? $_POST['chbActiveChecks']		: 0;
$chkPassiveChecks		= isset($_POST['chbPassiveChecks'])		? $_POST['chbPassiveChecks']	: 0;
$chkIsVolatile			= isset($_POST['chbIsVolatile'])		? $_POST['chbIsVolatile']		: 0;
$chkParallelize			= isset($_POST['chbParallelize'])		? $_POST['chbParallelize']		: 0;
$chkEventEnable			= isset($_POST['chbEventEnable'])		? $_POST['chbEventEnable']		: 0;
$chkFreshness			= isset($_POST['chbFreshness'])			? $_POST['chbFreshness']		: 0;
$chkObsess				= isset($_POST['chbObsess'])			? $_POST['chbObsess']			: 0;
$chkPerfData			= isset($_POST['chbPerfData'])			? $_POST['chbPerfData']			: 0;
$chkFlapEnable			= isset($_POST['chbFlapEnable'])		? $_POST['chbFlapEnable']		: 0;
$chkStatusInfos			= isset($_POST['chbStatusInfos'])		? $_POST['chbStatusInfos']		: 0;
$chkNonStatusInfos		= isset($_POST['chbNonStatusInfos'])	? $_POST['chbNonStatusInfos']	: 0;
$chkNotifEnabled		= isset($_POST['chbNotifEnabled'])		? $_POST['chbNotifEnabled']		: 0;
$chkSelOrderByGet		= isset($_GET['orderby'])				? rawurldecode($_GET['orderby']): "";
//
// Daten verarbeiten
// =================
$strFilter = "";
if ($chkModus == "add") $chkSelModify = "";
// Filter definieren
if ($chkSelOrderByGet != "") $chkSelOrderBy=$chkSelOrderByGet;

if (($chkSelOrderBy != "") && ($chkSelOrderBy != $LANG['admintable']['allconfigs'])){
	$strFilter    = "WHERE config_name='".$chkSelOrderBy."' ";
} 
$strNO 	  = substr($chkNOw.$chkNOu.$chkNOc.$chkNOr.$chkNOf,0,-1);
$strSO 	  = substr($chkSOo.$chkSOw.$chkSOu.$chkSOc,0,-1);
// Strings zusammenstellen
$strSelHosts			= $myVisClass->makeCommaString($chkSelHosts);
$strSelHostGroups		= $myVisClass->makeCommaString($chkSelHostGroups);
$strSelServiceGroups	= $myVisClass->makeCommaString($chkSelServiceGroups);
$strSelContactGroups	= $myVisClass->makeCommaString($chkSelContactGroups);
// Checkcommand zusammenstellen
$strCheckCommand = $chkSelServiceCommand;
if ($chkSelServiceCommand != "") {
	for ($i=1;$i<=8;$i++) {
		if (${"chkTfArg$i"} != "") $strCheckCommand .= "!".${"chkTfArg$i"};
	}
}
// Leerzeichen aus dem Konfigurationsnamen entfernen
$chkTfConfigName = str_replace(" ","_",$chkTfConfigName);
// Modi verarbeiten
if (($chkModus == "insert") || ($chkModus == "modify")) {
	// Daten Einfügen oder Aktualisieren
	$strSQL2 = "tbl_service SET host_name='$strSelHosts', hostgroup_name='$strSelHostGroups', service_description='$chkTfService', 
				config_name='$chkTfConfigName', servicegroups='$strSelServiceGroups', is_volatile='$chkIsVolatile', check_command='$strCheckCommand', 
				max_check_attempts=$chkTfMaxCheckAttempts, normal_check_interval=$chkTfNormCheckInt, retry_check_interval=$chkTfRetryCheckInt, 
				active_checks_enabled='$chkActiveChecks', passive_checks_enabled='$chkPassiveChecks', check_period='$chkSelCheckPeriod', 
				parallelize_check='$chkParallelize', obsess_over_service='$chkObsess', check_freshness='$chkFreshness', 
				freshness_threshold=$chkTfFreshTreshold, event_handler='$chkSelEventHandler', event_handler_enabled='$chkEventEnable', 
				low_flap_threshold=$chkTfLowFlat, high_flap_threshold=$chkTfHighFlat, flap_detection_enabled='$chkFlapEnable', 
				process_perf_data='$chkPerfData', retain_status_information='$chkStatusInfos', retain_nonstatus_information='$chkNonStatusInfos', 
				contact_groups='$strSelContactGroups', notification_interval=$chkNotifIntervall, notification_period='$chkSelNotifPeriod', 
				notification_options='$strNO', notifications_enabled='$chkNotifEnabled', stalking_options='$strSO', 
				active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL1 = "INSERT INTO ";
		$strSQL3 = "";
	} else {
		$strSQL1 = "UPDATE ";
		$strSQL3 = " WHERE id=".$chkDataId;	
	}	
	$strSQL = $strSQL1.$strSQL2.$strSQL3;
	if ((($chkSelHosts != "") || ($strSelHostGroups != "")) && ($chkTfService != "") && ($chkSelCheckPeriod != "") && ($chkTfMaxCheckAttempts != "NULL") &&
	    ($chkTfNormCheckInt  != "NULL") && ($chkTfRetryCheckInt  != "NULL") && ($chkSelNotifPeriod != "") && 
		($chkSelServiceCommand != "") && ($chkNotifIntervall != "NULL") && ($strNO != "") && ($strSelContactGroups != "")) {
		$myVisClass->dataInsert($strSQL);
		if ($chkModus == "insert") $myVisClass->writeLog($LANG['logbook']['newservice']." ".$chkTfConfigName);
		if ($chkModus == "modify") $myVisClass->writeLog($LANG['logbook']['modifyservice']." ".$chkTfConfigName);
		// Falls Konfigurationsname geändert wurde und kein weiterer Service mit diesem Konfigurationsnamen besteht, 
		// alte Konfigurationsdatei löschen		
		if (($chkModus == "modify") && ($chkOldConfig != $chkTfConfigName)) {
			$intServiceCount = $myDBClass->countRows("SELECT * FROM tbl_service WHERE config_name='$chkOldConfig'");
			if ($intServiceCount == 0) {
				$strOldDate    = date("YmdHis",mktime());
				$strFilename   = $SETS['nagios']['configservices'].$chkOldConfig.".cfg";
				$strBackupfile = $SETS['nagios']['backupservices'].$chkOldConfig.".cfg_old_".$strOldDate;
				if (file_exists($strFilename) && (is_writable($strFilename)) && (is_writable($SETS['nagios']['backupservices']))) {
					copy($strFilename,$strBackupfile);
					unlink($strFilename);
					$myVisClass->strDBMessage .= "<br>".$LANG['file']['success_del'];
					$myVisClass->writeLog($LANG['logbook']['delservice']." ".$strFilename);
				} else if (file_exists($strFilename)) {
					$myVisClass->strDBMessage .= "<br>".$LANG['file']['failed_del'];
				}
			}
		}
		$strMessage = $myVisClass->strDBMessage;
	} else {
		$strMessage  = $LANG['db']['datamissing'];
	}
	$chkModus = "display";
}  else if ($chkModus == "make") {
	// Konfigurationsdateien schreiben
	$intError = 0;
	// Datensätze holen	
	$strSQL    = "SELECT id FROM tbl_service ".$strFilter." GROUP BY config_name ORDER BY config_name";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataMake,$intDataCountMake);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
	} else if ($intDataCountMake != 0) {
		for ($i=0;$i<$intDataCountMake;$i++) {
			$myVisClass->createConfigSingle("tbl_service",$arrDataMake[$i]['id']);
			if ($myVisClass->strDBMessage == $LANG['file']['failed']) {
				$intError++;
			}
		}
	}
	if ($intError == 0) {
		$strMessage = $LANG['file']['allsuccess'];
	} else {
		$strMessage = $LANG['file']['somefailed'];
	}
	$chkModus = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$myVisClass->dataDelete("tbl_service",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$intResult  = $myDBClass->getFieldData("SELECT count(*) FROM tbl_service WHERE config_name='$chkSelOrderBy'");
	if ($intResult == 0) $strFilter = "";
	$chkModus = "display";	
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$myVisClass->dataCopy("tbl_service",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_service WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";	
	$chkModus      = "add";
} else if (($chkModus == "checkform") && ($chkSelModify == "config")) {
	// Daten des gewählten Datensatzes holen
	$intDSId    = (int)substr(array_search("on",$_POST),6);
	if ($chkListId != 0) $intDSId = $chkListId;
	$myVisClass->createConfigSingle("tbl_service",$intDSId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus   = "display";
}  else if ($chkModus == "filter") {
	// Daten des gewählten Datensatzes holen
	if (($chkSelOrderBy != "") && ($chkSelOrderBy != $LANG['admintable']['allconfigs'])){
		$strFilter    = "WHERE config_name='".$chkSelOrderBy."' ";
	} else if ($chkSelOrderBy == $LANG['admintable']['allconfigs']) {
		$chkSelOrderBy = "";
	}
	$chkModus   = "display";
}
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
	// Datenbankabfragen
	$myVisClass->strTempValue1 = $chkSelModify;
	$myVisClass->strTempValue2 = $chkModus;
	$myVisClass->resTemplate   =& $conttp;
	if (isset($arrModifyData)) $myVisClass->arrWorkdata = $arrModifyData;	
	// Hostauswahlfelder füllen
	$intReturn = 0;
	$strSQL    = "SELECT host_name FROM tbl_host ORDER BY host_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_HOST","host_name","host_name","hostname",2);
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_host']."<br>";	
    // Hostgruppenauswahlfelder füllen
	$intReturn2 = 0;
	$strSQL     = "SELECT hostgroup_name FROM tbl_hostgroup ORDER BY hostgroup_name";
	$intReturn2 = $myVisClass->parseSelect($strSQL,"DAT_HOSTGROUPS","hostgroup_name","hostgroup_name","hostgroups",2);
	if (($intReturn != 0) && ($intReturn2 != 0)) $strDBWarning .= $LANG['admintable']['warn_host_groups']."<br>";
	// Servicegruppenauswahlfelder füllen
	$strSQL    = "SELECT servicegroup_name FROM tbl_servicegroup ORDER BY servicegroup_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_SERVICEGROUPITEM","servicegroup_name","servicegroups","servicegroups",1);	
	// Servicecommandfelder füllen
	$intReturn = 0;
	$strFirstServiceCommand = "";
	$strSQL    = "SELECT command_name FROM tbl_checkcommand ORDER BY command_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_SERVICE_COMMAND","command_name","check_command","servicecommand",0);
	$strFirstServiceCommand = $myVisClass->strTempValue3;
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_command']."<br>";
	// Teiperiodenauswahlfelder füllen
	$intReturn = 0;
	$strSQL    = "SELECT timeperiod_name FROM tbl_timeperiod ORDER BY timeperiod_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_CHECK_PERIOD","timeperiod_name","check_period","checkperiod");
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_NOTIF_PERIOD","timeperiod_name","notification_period","notifperiod");
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_timeperiod']."<br>";
	// Eventhaldlerauswahlfelder füllen
	$strSQL    = "SELECT command_name FROM tbl_misccommand ORDER BY command_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_EVENT_HANDLERITEM","command_name","event_handler","eventhandler",1);
	// Contactgruppenauswahlfelder füllen
	$intReturn = 0;
	$strSQL    = "SELECT contactgroup_name FROM tbl_contactgroup ORDER BY contactgroup_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_CONTACTGROUPS","contactgroup_name","contact_groups","contactgroups");
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
	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		// Im Modus "Modifizieren" die Datenfelder setzen
		foreach($arrModifyData AS $key => $value) {
			if (($key == "active") || ($key == "last_modified")) continue;
			$conttp->setVariable("DAT_".strtoupper($key),htmlspecialchars($value));
		}
		foreach(explode(",",$arrModifyData['notification_options']) AS $elem) {
			$conttp->setVariable("DAT_NO".strtoupper($elem)."_CHECKED","checked");
		}
		foreach(explode(",",$arrModifyData['stalking_options']) AS $elem) {
			$conttp->setVariable("DAT_ST".strtoupper($elem)."_CHECKED","checked");
		}
		if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
		if ($arrModifyData['active_checks_enabled'] != 1) $conttp->setVariable("ACTIVE_CHECKS_CHECKED","");
		if ($arrModifyData['passive_checks_enabled'] != 1) $conttp->setVariable("PASSIVE_CHECKS_CHECKED","");
		if ($arrModifyData['obsess_over_service'] != 1) $conttp->setVariable("OBESS_CHECKED","");
		if ($arrModifyData['check_freshness'] != 1) $conttp->setVariable("FRESHNESS_CHECKED","");
		if ($arrModifyData['parallelize_check'] != 1) $conttp->setVariable("PARALLELIZE_CHECKED","");
		if ($arrModifyData['is_volatile'] == 1) $conttp->setVariable("VOLATILE_CHECKED","checked");
		if ($arrModifyData['event_handler_enabled'] != 1) $conttp->setVariable("EVENTHANDLER_CHECKED","");
		if ($arrModifyData['flap_detection_enabled'] != 1) $conttp->setVariable("FLAP_CHECKED","");
		if ($arrModifyData['process_perf_data'] == 1) $conttp->setVariable("PERF_CHECKED","checked");
		if ($arrModifyData['retain_status_information'] != 1) $conttp->setVariable("STATUS_CHECKED","");
		if ($arrModifyData['retain_nonstatus_information'] != 1) $conttp->setVariable("NONSTATUS_CHECKED","");		 
		if ($arrModifyData['notifications_enabled'] != 1) $conttp->setVariable("NOTIF_CHECKED","");		
		if ($arrModifyData['check_command'] != "") {
			$arrArgument = explode("!",$arrModifyData['check_command']);
			foreach ($arrArgument AS $key => $value) {
				if ($key == 0) {
					$conttp->setVariable("IFRAME_SRC",$SETS['path']['root']."admin/commandline.php?cname=".$value);
				} else {
					$conttp->setVariable("DAT_ARG".$key,$value);
				}
			}
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
	if ($booReturn == false) {$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";} else {$intCount = (int)$arrDataLinesCount['number'];}
	// Datensätze holen
	$strSQL    = "SELECT id, config_name, service_description, active, last_modified
				  FROM tbl_service ".$strFilter."ORDER BY config_name,service_description LIMIT $chkLimit,15";
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
			$myVisClass->lastModifiedDir($arrDataLines[$i]['config_name'],$arrDataLines[$i]['id'],"service",$strTimeEntry,$strTimeFile,$intOlder);
			// Datenfelder setzen
			foreach($LANG['admintable'] AS $key => $value) {
				$mastertp->setVariable("LANG_".strtoupper($key),$value);
			} 
			$mastertp->setVariable("DATA_FIELD_1",$arrDataLines[$i]['config_name']);
			$mastertp->setVariable("DATA_FIELD_2",$arrDataLines[$i]['service_description']);
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
if (isset($strMessage)) $mastertp->setVariable("DBMESSAGE",$strMessage);
$strContMessage = $myVisClass->checkConsistServices();
$mastertp->setVariable("CONSISTUSAGE",$strContMessage);
if ($strContMessage == $LANG['admincontent']['servicesok']) {
	$mastertp->setVariable("CON_MSGCLASS","okmessage");
} else {
	$mastertp->setVariable("CON_MSGCLASS","dbmessage");
}
$mastertp->parse("msgfooterhost");
$mastertp->show("msgfooterhost");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>