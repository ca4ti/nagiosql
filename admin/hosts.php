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
// Zweck:	Hosts definieren
// Datei:	admin/hosts.php
// Version: 2.00.00 (Internal)
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
$chkOldHost				= isset($_POST['hidHostname'])			? addslashes($_POST['hidHostname'])			: "";
$chkTfName 				= isset($_POST['tfName']) 				? addslashes($_POST['tfName']) 				: "";
$chkTfFriendly 			= isset($_POST['tfFriendly']) 			? addslashes($_POST['tfFriendly']) 			: "";
$chkTfAddress 			= isset($_POST['tfAddress']) 			? addslashes($_POST['tfAddress']) 			: "";
$chkTfArg1				= isset($_POST['tfArg1']) 				? addslashes($_POST['tfArg1']) 				: "";
$chkTfArg2				= isset($_POST['tfArg2']) 				? addslashes($_POST['tfArg2']) 				: "";
$chkTfArg3				= isset($_POST['tfArg3']) 				? addslashes($_POST['tfArg3']) 				: "";
$chkTfArg4				= isset($_POST['tfArg4']) 				? addslashes($_POST['tfArg4']) 				: "";
$chkTfArg5				= isset($_POST['tfArg5']) 				? addslashes($_POST['tfArg5']) 				: "";
$chkTfArg6				= isset($_POST['tfArg6']) 				? addslashes($_POST['tfArg6']) 				: "";
$chkTfArg7				= isset($_POST['tfArg7']) 				? addslashes($_POST['tfArg7']) 				: "";
$chkTfArg8				= isset($_POST['tfArg8']) 				? addslashes($_POST['tfArg8']) 				: "";
$chkSelParents 			= isset($_POST['selParents']) 			? $_POST['selParents'] 						: array("");
$chkSelHostGroups 		= isset($_POST['selHostGroups']) 		? $_POST['selHostGroups'] 					: array("");
$chkSelContactGroups 	= isset($_POST['selContactGroups']) 	? $_POST['selContactGroups'] 				: array("");
$chkSelHostCommand 		= isset($_POST['selHostCommand']) 		? $_POST['selHostCommand'] 					: "";
$chkSelCheckPeriod 		= isset($_POST['selCheckPeriod']) 		? $_POST['selCheckPeriod'] 					: "";
$chkSelEventHandler 	= isset($_POST['selEventHandler']) 		? $_POST['selEventHandler']					: "";
$chkSelNotifPeriod 		= isset($_POST['selNotifPeriod']) 		? $_POST['selNotifPeriod']	 				: "";
$chkTfMaxCheckAttempts	= (isset($_POST['tfMaxCheckAttempts']) 	&& ($_POST['tfMaxCheckAttempts'] != ""))	? $_POST['tfMaxCheckAttempts']	: "NULL";
$chkTfCheckIntervall	= (isset($_POST['tfCheckIntervall'])	&& ($_POST['tfCheckIntervall'] != ""))		? $_POST['tfCheckIntervall']	: "NULL";
$chkTfLowFlat			= (isset($_POST['tfLowFlat'])			&& ($_POST['tfLowFlat'] != ""))				? $_POST['tfLowFlat']			: "NULL";
$chkTfHighFlat			= (isset($_POST['tfHighFlat'])			&& ($_POST['tfHighFlat'] != ""))			? $_POST['tfHighFlat']			: "NULL";
$chkTfFreshTreshold		= (isset($_POST['tfFreshTreshold'])		&& ($_POST['tfFreshTreshold'] != ""))		? $_POST['tfFreshTreshold']		: "NULL";
$chkNotifIntervall		= (isset($_POST['tfNotifIntervall'])	&& ($_POST['tfNotifIntervall'] != "")) 		? $_POST['tfNotifIntervall']	: "NULL";
$chkActiveChecks		= isset($_POST['chbActiveChecks'])		? $_POST['chbActiveChecks']					: 0;
$chkPassiveChecks		= isset($_POST['chbPassiveChecks'])		? $_POST['chbPassiveChecks']				: 0;
$chkEventEnable			= isset($_POST['chbEventEnable'])		? $_POST['chbEventEnable']					: 0;
$chkFreshness			= isset($_POST['chbFreshness'])			? $_POST['chbFreshness']					: 0;
$chkObsess				= isset($_POST['chbObsess'])			? $_POST['chbObsess']						: 0;
$chkPerfData			= isset($_POST['chbPerfData'])			? $_POST['chbPerfData']						: 0;
$chkFlapEnable			= isset($_POST['chbFlapEnable'])		? $_POST['chbFlapEnable']					: 0;
$chkStatusInfos			= isset($_POST['chbStatusInfos'])		? $_POST['chbStatusInfos']					: 0;
$chkNonStatusInfos		= isset($_POST['chbNonStatusInfos'])	? $_POST['chbNonStatusInfos']				: 0;
$chkNotifEnabled		= isset($_POST['chbNotifEnabled'])		? $_POST['chbNotifEnabled']					: 0;
$chkSTo					= isset($_POST['chbSTo'])				? $_POST['chbSTo'].","						: "";
$chkSTd					= isset($_POST['chbSTd'])				? $_POST['chbSTd'].","						: "";
$chkSTu					= isset($_POST['chbSTu'])				? $_POST['chbSTu'].","						: "";
$chkNOd					= isset($_POST['chbNOd'])				? $_POST['chbNOd'].","						: "";
$chkNOu					= isset($_POST['chbNOu'])				? $_POST['chbNOu'].","						: "";
$chkNOr					= isset($_POST['chbNOr'])				? $_POST['chbNOr'].","						: "";
$chkNOf					= isset($_POST['chbNOf'])				? $_POST['chbNOf'].","						: "";
//
// Daten verarbeiten
// =================
$strNO 	  = substr($chkNOd.$chkNOu.$chkNOr.$chkNOf,0,-1);
$strST 	  = substr($chkSTo.$chkSTd.$chkSTu,0,-1);
if (($chkSelParents[0] == "")   	|| ($chkSelParents[0] == "0"))   	 {$intSelParents = 0;}  	 else {$intSelParents = 1;}
if (($chkSelHostGroups[0] == "")    || ($chkSelHostGroups[0] == "0"))    {$intSelHostGroups = 0;}    else {$intSelHostGroups = 1;}
if (($chkSelContactGroups[0] == "") || ($chkSelContactGroups[0] == "0")) {$intSelContactGroups = 0;} else {$intSelContactGroups = 1;}
// Checkcommand zusammenstellen
$strCheckCommand = $chkSelHostCommand;
if ($chkSelHostCommand != "") {
	for ($i=1;$i<=8;$i++) {
		if (${"chkTfArg$i"} != "") $strCheckCommand .= "!".${"chkTfArg$i"};
	}
}
// Datein einfügen oder modifizieren
if (($chkModus == "insert") || ($chkModus == "modify")) {
	if ($hidActive == 1) $chkActive = 1;
	$strSQLx = "tbl_host SET host_name='$chkTfName', alias='$chkTfFriendly', address='$chkTfAddress', parents=$intSelParents, 
				hostgroups=$intSelHostGroups, check_command='$strCheckCommand', max_check_attempts=$chkTfMaxCheckAttempts, 
				check_interval=$chkTfCheckIntervall, active_checks_enabled='$chkActiveChecks', passive_checks_enabled='$chkPassiveChecks', 
				check_period='$chkSelCheckPeriod', obsess_over_host='$chkObsess', check_freshness='$chkFreshness', 
				freshness_threshold=$chkTfFreshTreshold, event_handler='$chkSelEventHandler', event_handler_enabled='$chkEventEnable', 
				low_flap_threshold=$chkTfLowFlat, high_flap_threshold=$chkTfHighFlat, flap_detection_enabled='$chkFlapEnable', 
				process_perf_data='$chkPerfData', retain_status_information='$chkStatusInfos', retain_nonstatus_information='$chkNonStatusInfos', 
				contact_groups=$intSelContactGroups, notification_interval=$chkNotifIntervall, notification_period='$chkSelNotifPeriod', 
				notification_options='$strNO', notifications_enabled='$chkNotifEnabled', stalking_options='$strST', 
				active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL = "INSERT INTO ".$strSQLx; 
	} else {
		$strSQL = "UPDATE ".$strSQLx." WHERE id=$chkDataId";   
	}	
	if (($chkTfName != "") && ($chkTfFriendly != "") && ($chkTfAddress != "") && ($chkSelCheckPeriod != "") && 
	    ($chkTfMaxCheckAttempts != "NULL") && ($chkSelNotifPeriod != "") && ($chkNotifIntervall != "NULL") && 
		($strNO != "") && ($intSelContactGroups != 0)) {
		$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		if ($intInsert == 1) {
			$intReturn = 1;
		} else {
			if ($chkModus  == "insert") 	$myDataClass->writeLog($LANG['logbook']['newhost']." ".$chkTfName);
			if ($chkModus  == "modify") 	$myDataClass->writeLog($LANG['logbook']['modifyhost']." ".$chkTfName);
			//
			// Relationen eintragen/updaten
			// ============================
			$intTableA = $myDataClass->tableID("tbl_host");
			if ($chkModus == "insert") {
				if ($intSelParents       == 1)	$myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_host"),$intInsertId,'parents',$chkSelParents);
				if ($intSelHostGroups    == 1)	$myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$intInsertId,'hostgroups',$chkSelHostGroups);
				if ($intSelContactGroups == 1)	$myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_contactgroup"),$intInsertId,'contact_groups',$chkSelContactGroups);
			} else if ($chkModus == "modify") {		
				if ($intSelParents == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'parents',$chkSelParents);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'parents');
				}
				if ($intSelHostGroups == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$chkDataId,'hostgroups',$chkSelHostGroups);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$chkDataId,'hostgroups');			
				}
				if ($intSelContactGroups == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_contactgroup"),$chkDataId,'contact_groups',$chkSelContactGroups);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_contactgroup"),$chkDataId,'contact_groups');			
				}
			}	
			$intReturn = 0;
			// Falls Hostname geändert wurde, alte Konfigurationsdatei umkopieren/löschen
			if (($chkModus == "modify") && ($chkOldHost != $chkTfName)) {
				$strOldDate    = date("YmdHis",mktime());
				$strFilename   = $SETS['nagios']['confighosts'].$chkOldHost.".cfg";
				$strBackupfile = $SETS['nagios']['backuphosts'].$chkOldHost.".cfg_old_".$strOldDate;
				if (file_exists($strFilename) && (is_writable($strFilename)) && (is_writable($SETS['nagios']['backuphosts']))) {
					copy($strFilename,$strBackupfile);
					unlink($strFilename);
					$myDataClass->strDBMessage .= "<br>".$LANG['file']['success_del'];
					$myDataClass->writeLog($LANG['logbook']['delhost']." ".$strFilename);
				} else if (file_exists($strFilename)) {
					$myDataClass->strDBMessage .= "<br>".$LANG['file']['failed_del'];
					$intReturn = 1;
				}
			}
		}
	} else {
		$strMessage .= $LANG['db']['datamissing'];
	}
	$chkModus = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$intReturn = $myDataClass->dataDeleteSimple("tbl_host",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$intReturn = $myDataClass->dataCopySimple("tbl_host",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_host WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) {
		$myDataClass->strDBMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
		$intReturn = 1;
	}	
	$chkModus      = "add";
} else if (($chkModus == "checkform") && ($chkSelModify == "config")) {
	// Konfiguration schreiben
	$intDSId    = (int)substr(array_search("on",$_POST),6);
	if ($chkListId != 0) $intDSId = $chkListId;
	// Prüfen ob die Konfiguration aktiv ist
	$booReturn = $myDBClass->getSingleDataset("SELECT active FROM tbl_host WHERE id=".$intDSId,$arrModifyData);
	if (isset($arrModifyData['active']) && ($arrModifyData['active'] == 1)) {
		$intReturn = $myConfigClass->createConfigSingle("tbl_host",$intDSId);
		$myDataClass->strDBMessage = $myConfigClass->strDBMessage;
	} else {
		$myDataClass->strDBMessage = $LANG['db']['noactive_host'];
		$intReturn = 1;
	}
	$chkModus   = "display";
}
// Statusmitteilungen setzen
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $myDataClass->strDBMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
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
	// Klassenvariabeln definieren
	$myVisClass->resTemplate     =& $conttp;
	$myVisClass->strTempValue1   = $chkSelModify;
	$myVisClass->intTabA   	     = $myDataClass->tableID("tbl_host");
	if (!isset($arrModifyData['id'])) $chkDataId == 0;
	if (isset($arrModifyData)) {
		$myVisClass->arrWorkdata = $arrModifyData;
		$myVisClass->intTabA_id  = $arrModifyData['id'];
	} else {
		$myVisClass->intTabA_id  = 0;
	}
	// Hostfelder füllen
	$myVisClass->parseSelectNew('tbl_host','host_name','DAT_PARENTITEM','parents','parents',2,1,$myVisClass->intTabA_id);
	// Hostgruppenfelder füllen
	$myVisClass->parseSelectNew('tbl_hostgroup','hostgroup_name','DAT_HOSTGROUPITEM','hostgroups','hostgroups',2,1);
	// Prüfbefehlfelder füllen
	$myVisClass->parseSelectNew('tbl_checkcommand','command_name','DAT_HOST_COMMAND','hostcommand','check_command',1,1);
	$strFirstHostCommand = $myVisClass->strTempValue2; 	
	// Prüfperiodenfelder füllen
	$intReturn = 0;
	$intReturn = $myVisClass->parseSelectNew('tbl_timeperiod','timeperiod_name','DAT_CHECK_PERIODS','checkperiod','check_period',1,1);
	$intReturn = $myVisClass->parseSelectNew('tbl_timeperiod','timeperiod_name','DAT_NOTIF_PERIOD','notifperiod','notification_period',1,1);
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_timeperiod']."<br>";
	// Eventhandlerfelder füllen
	$myVisClass->parseSelectNew('tbl_misccommand','command_name','DAT_EVENTHANDLER','eventhandlerrow','event_handler',1,1);
	// Kontaktgruppenfelder füllen
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
	$conttp->setVariable("FRESHNESS_CHECKED","checked");
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
		if ($arrModifyData['obsess_over_host'] != 1) $conttp->setVariable("OBESS_CHECKED","");
		if ($arrModifyData['check_freshness'] != 1) $conttp->setVariable("FRESHNESS_CHECKED","");
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
		} else {
			$conttp->setVariable("IFRAME_SRC",$SETS['path']['root']."admin/commandline.php?cname=".$strFirstHostCommand);
		}
		// Prüfen, ob dieser Eintrag in einer anderen Konfiguration verwendet wird
		if ($myDataClass->checkMustdata("tbl_host",$arrModifyData['id'],$arrInfo) != 0) {
			$conttp->setVariable("ACT_DISABLED","disabled");
			$conttp->setVariable("ACTIVE","1");
			$conttp->setVariable("CHECK_MUST_DATA","<span class=\"dbmessage\">".$LANG['admintable']['noactivate']."</span>");
		} 
		// Optionskästchen verarbeiten
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
	$mastertp->setVariable("MAX_ID","0");
	$mastertp->setVariable("MIN_ID","0");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_host";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
	// Datensätze holen
	$strSQL    = "SELECT id, host_name, alias, active, last_modified FROM tbl_host 
				  ORDER BY host_name LIMIT $chkLimit,".$SETS['common']['pagelines'];
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
	} else if ($intDataCount != 0) {
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
			$myConfigClass->lastModifiedDir($arrDataLines[$i]['host_name'],$arrDataLines[$i]['id'],"host",$strTimeEntry,$strTimeFile,$intOlder);
			// Datenfelder setzen
			foreach($LANG['admintable'] AS $key => $value) {
				$mastertp->setVariable("LANG_".strtoupper($key),$value);
			} 
			if (strlen($arrDataLines[$i]['host_name']) > 50) {$strAdd = ".....";} else {$strAdd = "";}
			$mastertp->setVariable("DATA_FIELD_1",substr(stripslashes($arrDataLines[$i]['host_name']),0,50).$strAdd);
			$mastertp->setVariable("DATA_FIELD_2",stripslashes($arrDataLines[$i]['alias']));
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