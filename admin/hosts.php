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
// Datum:	02.04.2005
// Zweck:	Hosts definieren
// Datei:	admin/hosts.php
// Version: 1.02
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 2;
$intSub  		= 1;
$intMenu 		= 2;
$preContent 	= "hosts.tpl.htm";
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
$chkOldHost				= isset($_POST['hidHostname'])			? $_POST['hidHostname']			: "";
$chkTfName 				= isset($_POST['tfName']) 				? $_POST['tfName'] 				: "";
$chkTfFriendly 			= isset($_POST['tfFriendly']) 			? $_POST['tfFriendly'] 			: "";
$chkTfAddress 			= isset($_POST['tfAddress']) 			? $_POST['tfAddress'] 			: "";
$chkTfArg1				= isset($_POST['tfArg1']) 				? $_POST['tfArg1'] 				: "";
$chkTfArg2				= isset($_POST['tfArg2']) 				? $_POST['tfArg2'] 				: "";
$chkTfArg3				= isset($_POST['tfArg3']) 				? $_POST['tfArg3'] 				: "";
$chkTfArg4				= isset($_POST['tfArg4']) 				? $_POST['tfArg4'] 				: "";
$chkTfArg5				= isset($_POST['tfArg5']) 				? $_POST['tfArg5'] 				: "";
$chkTfArg6				= isset($_POST['tfArg6']) 				? $_POST['tfArg6'] 				: "";
$chkTfArg7				= isset($_POST['tfArg7']) 				? $_POST['tfArg7'] 				: "";
$chkTfArg8				= isset($_POST['tfArg8']) 				? $_POST['tfArg8'] 				: "";
$chkSelParents 			= isset($_POST['selParents']) 			? $_POST['selParents'] 			: array("");
$chkSelHostGroups 		= isset($_POST['selHostGroups']) 		? $_POST['selHostGroups'] 		: array("");
$chkSelContactGroups 	= isset($_POST['selContactGroups']) 	? $_POST['selContactGroups'] 	: array("");
$chkSelHostCommand 		= isset($_POST['selHostCommand']) 		? $_POST['selHostCommand'] 		: "";
$chkSelCheckPeriod 		= isset($_POST['selCheckPeriod']) 		? $_POST['selCheckPeriod'] 		: "";
$chkSelEventHandler 	= isset($_POST['selEventHandler']) 		? $_POST['selEventHandler']		: "";
$chkSelNotifPeriod 		= isset($_POST['selNotifPeriod']) 		? $_POST['selNotifPeriod'] 		: "";
$chkTfMaxCheckAttempts	= (isset($_POST['tfMaxCheckAttempts']) 	&& ($_POST['tfMaxCheckAttempts'] != ""))	? $_POST['tfMaxCheckAttempts']	: "NULL";
$chkTfCheckIntervall	= (isset($_POST['tfCheckIntervall'])	&& ($_POST['tfCheckIntervall'] != ""))		? $_POST['tfCheckIntervall']	: "NULL";
$chkTfLowFlat			= (isset($_POST['tfLowFlat'])			&& ($_POST['tfLowFlat'] != ""))				? $_POST['tfLowFlat']			: "NULL";
$chkTfHighFlat			= (isset($_POST['tfHighFlat'])			&& ($_POST['tfHighFlat'] != ""))			? $_POST['tfHighFlat']			: "NULL";
$chkTfFreshTreshold		= (isset($_POST['tfFreshTreshold'])		&& ($_POST['tfFreshTreshold'] != ""))		? $_POST['tfFreshTreshold']		: "NULL";
$chkNotifIntervall		= (isset($_POST['tfNotifIntervall'])	&& ($_POST['tfNotifIntervall'] != "")) 		? $_POST['tfNotifIntervall']	: "NULL";
$chkActiveChecks		= isset($_POST['chbActiveChecks'])		? $_POST['chbActiveChecks']		: 0;
$chkPassiveChecks		= isset($_POST['chbPassiveChecks'])		? $_POST['chbPassiveChecks']	: 0;
$chkEventEnable			= isset($_POST['chbEventEnable'])		? $_POST['chbEventEnable']		: 0;
$chkFreshness			= isset($_POST['chbFreshness'])			? $_POST['chbFreshness']		: 0;
$chkObsess				= isset($_POST['chbObsess'])			? $_POST['chbObsess']			: 0;
$chkPerfData			= isset($_POST['chbPerfData'])			? $_POST['chbPerfData']			: 0;
$chkFlapEnable			= isset($_POST['chbFlapEnable'])		? $_POST['chbFlapEnable']		: 0;
$chkStatusInfos			= isset($_POST['chbStatusInfos'])		? $_POST['chbStatusInfos']		: 0;
$chkNonStatusInfos		= isset($_POST['chbNonStatusInfos'])	? $_POST['chbNonStatusInfos']	: 0;
$chkNotifEnabled		= isset($_POST['chbNotifEnabled'])		? $_POST['chbNotifEnabled']		: 0;
$chkSTo					= isset($_POST['chbSTo'])				? $_POST['chbSTo'].","			: "";
$chkSTd					= isset($_POST['chbSTd'])				? $_POST['chbSTd'].","			: "";
$chkSTu					= isset($_POST['chbSTu'])				? $_POST['chbSTu'].","			: "";
$chkNOd					= isset($_POST['chbNOd'])				? $_POST['chbNOd'].","			: "";
$chkNOu					= isset($_POST['chbNOu'])				? $_POST['chbNOu'].","			: "";
$chkNOr					= isset($_POST['chbNOr'])				? $_POST['chbNOr'].","			: "";
$chkNOf					= isset($_POST['chbNOf'])				? $_POST['chbNOf'].","			: "";
//
// Daten verarbeiten
// =================
$strNO 	  = substr($chkNOd.$chkNOu.$chkNOr.$chkNOf,0,-1);
$strST 	  = substr($chkSTo.$chkSTd.$chkSTu,0,-1);
// Strings zusammenstellen
$strSelParents			= $myVisClass->makeCommaString($chkSelParents);
$strSelHostGroups		= $myVisClass->makeCommaString($chkSelHostGroups);
$strSelContactGroups	= $myVisClass->makeCommaString($chkSelContactGroups);
// Checkcommand zusammenstellen
$strCheckCommand = $chkSelHostCommand;
if ($chkSelHostCommand != "") {
	for ($i=1;$i<=8;$i++) {
		if (${"chkTfArg$i"} != "") $strCheckCommand .= "!".${"chkTfArg$i"};
	}
}
if (($chkModus == "insert") || ($chkModus == "modify")) {
	// Daten Einfügen oder Aktualisieren
	$strSQL2 = "tbl_host SET host_name='$chkTfName', alias='$chkTfFriendly', address='$chkTfAddress', parents='$strSelParents', 
				hostgroups='$strSelHostGroups', check_command='$strCheckCommand', max_check_attempts=$chkTfMaxCheckAttempts, 
				check_interval=$chkTfCheckIntervall, active_checks_enabled='$chkActiveChecks', passive_checks_enabled='$chkPassiveChecks', 
				check_period='$chkSelCheckPeriod', obsess_over_host='$chkObsess', check_freshness='$chkFreshness', 
				freshness_threshold=$chkTfFreshTreshold, event_handler='$chkSelEventHandler', event_handler_enabled='$chkEventEnable', 
				low_flap_threshold=$chkTfLowFlat, high_flap_threshold=$chkTfHighFlat, flap_detection_enabled='$chkFlapEnable', 
				process_perf_data='$chkPerfData', retain_status_information='$chkStatusInfos', retain_nonstatus_information='$chkNonStatusInfos', 
				contact_groups='$strSelContactGroups', notification_interval=$chkNotifIntervall, notification_period='$chkSelNotifPeriod', 
				notification_options='$strNO', notifications_enabled='$chkNotifEnabled', stalking_options='$strST', 
				active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL1 = "INSERT INTO ";
		$strSQL3 = "";
	} else {
		$strSQL1 = "UPDATE ";
		$strSQL3 = " WHERE id=".$chkDataId;	
	}	
	$strSQL = $strSQL1.$strSQL2.$strSQL3;
	if (($chkTfName != "") && ($chkTfFriendly != "") && ($chkTfAddress != "") && ($chkSelCheckPeriod != "") && 
	    ($chkTfMaxCheckAttempts != "NULL") && ($chkSelNotifPeriod != "") && ($chkNotifIntervall != "NULL") && 
		($strNO != "") && ($strSelContactGroups != "")) {
		$myVisClass->dataInsert($strSQL);
		if ($chkModus == "insert") $myVisClass->writeLog($LANG['logbook']['newhost']." ".$chkTfName);
		if ($chkModus == "modify") $myVisClass->writeLog($LANG['logbook']['modifyhost']." ".$chkTfName);
		// Falls Hostname geändert wurde, alte Konfigurationsdatei umkopieren/löschen
		if (($chkModus == "modify") && ($chkOldHost != $chkTfName)) {
			$strOldDate    = date("YmdHis",mktime());
			$strFilename   = $SETS['nagios']['confighosts'].$chkOldHost.".cfg";
			$strBackupfile = $SETS['nagios']['backuphosts'].$chkOldHost.".cfg_old_".$strOldDate;
			if (file_exists($strFilename) && (is_writable($strFilename)) && (is_writable($SETS['nagios']['backuphosts']))) {
				copy($strFilename,$strBackupfile);
				unlink($strFilename);
				$myVisClass->strDBMessage .= "<br>".$LANG['file']['success_del'];
				$myVisClass->writeLog($LANG['logbook']['delhost']." ".$strFilename);
			} else if (file_exists($strFilename)) {
				$myVisClass->strDBMessage .= "<br>".$LANG['file']['failed_del'];
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
	$strSQL    = "SELECT id FROM tbl_host ORDER BY host_name";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataMake,$intDataCountMake);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
	} else if ($intDataCountMake != 0) {
		for ($i=0;$i<$intDataCountMake;$i++) {	
			$myVisClass->createConfigSingle("tbl_host",$arrDataMake[$i]['id']);
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
	$myVisClass->dataDelete("tbl_host",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$myVisClass->dataCopy("tbl_host",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_host WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";	
	$chkModus      = "add";
} else if (($chkModus == "checkform") && ($chkSelModify == "config")) {
	// Daten des gewählten Datensatzes holen
	$intDSId    = (int)substr(array_search("on",$_POST),6);
	if ($chkListId != 0) $intDSId = $chkListId;
	$myVisClass->createConfigSingle("tbl_host",$intDSId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus   = "display";
}
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myVisClass->lastModified("tbl_contact",$strLastModified,$strFileDate,$strOld);
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm2']." -> ".$LANG['menu']['item_admsub1']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['host']);
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if ($chkModus == "add") {
	// Datenbankabfragen
	$myVisClass->strTempValue1 = $chkSelModify;
	$myVisClass->resTemplate   =& $conttp;
	if (isset($arrModifyData)) $myVisClass->arrWorkdata = $arrModifyData;
	// Hostfelder füllen
	$strSQL    = "SELECT host_name FROM tbl_host ORDER BY host_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_PARENTITEM","host_name","parents","parents",1);	
	// Hostgruppenfelder füllen
	$strSQL    = "SELECT hostgroup_name FROM tbl_hostgroup ORDER BY hostgroup_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_HOSTGROUPITEM","hostgroup_name","hostgroups","hostgroups",1);	
	// Prüfbefehlfelder füllen
	$strSQL    = "SELECT command_name FROM tbl_checkcommand ORDER BY command_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_HOST_COMMAND","command_name","check_command","hostcommand",1);
	$strFirstHostCommand = $myVisClass->strTempValue3; 	
	// Prüfperiodenfelder füllen
	$intReturn = 0;
	$strSQL    = "SELECT timeperiod_name FROM tbl_timeperiod ORDER BY timeperiod_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_CHECK_PERIOD","timeperiod_name","check_period","checkperiod");
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_NOTIF_PERIOD","timeperiod_name","notification_period","notifperiod");
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_timeperiod']."<br>";
	// Eventhandlerfelder füllen
	$strSQL    = "SELECT command_name FROM tbl_misccommand ORDER BY command_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_EVENTHANDLER","command_name","event_handler","eventhandlerrow",1);	
	// Kontaktgruppenfelder füllen
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
	$conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
	$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	$conttp->setVariable("DOCUMENT_ROOT",$SETS['path']['root']);
	$conttp->setVariable("IFRAME_SRC",$SETS['path']['root']."admin/commandline.php");
	$conttp->setVariable("LIMIT",$chkLimit);
	if ($strDBWarning != "") $conttp->setVariable("WARNING",$strDBWarning.$LANG['admintable']['warn_save']);
	$conttp->setVariable("ACT_CHECKED","checked");
	$conttp->setVariable("ACTIVE_CHECKS_CHECKED","checked");
	$conttp->setVariable("PASSIVE_CHECKS_CHECKED","checked");
	$conttp->setVariable("NOTIF_CHECKED","checked");
	$conttp->setVariable("OBESS_CHECKED","checked");
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
		if ($arrModifyData['obsess_over_host'] != 1) $conttp->setVariable("OBESS_CHECKED","");
		if ($arrModifyData['check_freshness'] != 1) $conttp->setVariable("FRESHNESS_CHECKED","");
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
		} else {
			$conttp->setVariable("IFRAME_SRC",$SETS['path']['root']."admin/commandline.php?cname=".$strFirstHostCommand);
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
	$mastertp->setVariable("FIELD_1",$LANG['admintable']['hostname']);
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['friendly']);	
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);
	$mastertp->setVariable("WRITE_CONFIG",$LANG['admintable']['write_conf']);
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_host");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_host";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";} else {$intCount = (int)$arrDataLinesCount['number'];}
	// Datensätze holen
	$strSQL    = "SELECT id, host_name, alias, active, last_modified FROM tbl_host ORDER BY host_name LIMIT $chkLimit,15";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
	} else if ($intDataCount != 0) {
		for ($i=0;$i<$intDataCount;$i++) {	
			// Jede zweite Zeile einfärben (Klassen setzen)
			$strClassL = "tdld"; $strClassM = "tdmd"; $strChbClass = "checkboxline";
			if ($i%2 == 1) {$strClassL = "tdlb"; $strClassM = "tdmb"; $strChbClass = "checkbox";}
			if ($arrDataLines[$i]['active'] == 0) {$strActive = $LANG['common']['no_nak'];} else {$strActive = $LANG['common']['yes_ok'];}	
			// Dateidatum holen
			$myVisClass->lastModifiedDir($arrDataLines[$i]['host_name'],$arrDataLines[$i]['id'],"host",$strTimeEntry,$strTimeFile,$intOlder);
			// Datenfelder setzen
			foreach($LANG['admintable'] AS $key => $value) {
				$mastertp->setVariable("LANG_".strtoupper($key),$value);
			} 
			if (strlen($arrDataLines[$i]['host_name']) > 50) {$strAdd = ".....";} else {$strAdd = "";}
			$mastertp->setVariable("DATA_FIELD_1",substr($arrDataLines[$i]['host_name'],0,50).$strAdd);
			$mastertp->setVariable("DATA_FIELD_2",$arrDataLines[$i]['alias']);
			$mastertp->setVariable("DATA_ACTIVE",$strActive);
			$mastertp->setVariable("DATA_FILE","<span class=\"dbmessage\">".$LANG['admintable']['file_old']."</span>");
			$mastertp->setVariable("LINE_ID",$arrDataLines[$i]['id']);
			$mastertp->setVariable("CELLCLASS_L",$strClassL);
			$mastertp->setVariable("CELLCLASS_M",$strClassM);
			$mastertp->setVariable("CHB_CLASS",$strChbClass);
			$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
			if ($intOlder == 0) $mastertp->setVariable("DATA_FILE",$LANG['admintable']['file_io']);
			if ($chkModus != "display") $mastertp->setVariable("DISABLED","disabled");		
			$mastertp->parse("datarowhost");		
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
	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	if (isset($intCount)) $mastertp->setVariable("PAGES",$myVisClass->buildPageLinks($_SERVER['PHP_SELF'],$intCount,$chkLimit));
	$mastertp->parse("datatablehost");
	$mastertp->show("datatablehost");
}
// Mitteilungen ausgeben
if (isset($strMessage)) $mastertp->setVariable("DBMESSAGE",$strMessage);
$strContMessage = $myVisClass->checkConsistHosts();
$mastertp->setVariable("CONSISTUSAGE",$strContMessage);
if ($strContMessage == $LANG['admincontent']['hostsok']) {
	$mastertp->setVariable("CON_MSGCLASS","okmessage");
} else {
	$mastertp->setVariable("CON_MSGCLASS","dbmessage");
}
if ($myVisClass->strTempValue1 != "") $mastertp->setVariable("FREEDATA",$myVisClass->strTempValue1);
$mastertp->parse("msgfooterhost");
$mastertp->show("msgfooterhost");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>