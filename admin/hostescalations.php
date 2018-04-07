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
// Zweck:	Host Eskalationen definieren
// Datei:	admin/hostescalations.php
// Version:	1.02
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 5;
$intSub  		= 13;
$intMenu 		= 2;
$preContent 	= "hostescalations.tpl.htm";
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
$chkSelContactGroup 	= isset($_POST['selContactGroup']) 	? $_POST['selContactGroup'] 	: array("");
$chkSelHostGroup 		= isset($_POST['selHostGroup']) 	? $_POST['selHostGroup'] 		: array("");
$chkSelHost 			= isset($_POST['selHost']) 			? $_POST['selHost'] 			: array("");
$chkSelService		 	= isset($_POST['selService']) 		? $_POST['selService'] 			: "";
$chkSelEscPeriod		= isset($_POST['selEscPeriod']) 	? $_POST['selEscPeriod'] 		: "";
$chkTfConfigName 		= isset($_POST['tfConfigName']) 	? $_POST['tfConfigName'] 		: "";
$chkTfFirstNotif 		= isset($_POST['tfFirstNotif']) 	? $_POST['tfFirstNotif'] 		: "NULL";
$chkTfLastNotif 		= isset($_POST['tfLastNotif']) 		? $_POST['tfLastNotif'] 		: "NULL";
$chkTfNotifInterval 	= isset($_POST['tfNotifInterval']) 	? $_POST['tfNotifInterval'] 	: "NULL";
$chkEOd					= isset($_POST['chbEOd'])			? $_POST['chbEOd'].","			: "";
$chkEOu					= isset($_POST['chbEOu'])			? $_POST['chbEOu'].","			: "";
$chkEOr					= isset($_POST['chbEOr'])			? $_POST['chbEOr'].","			: "";
//
// Daten verarbeiten
// =================
$strEO 	  = substr($chkEOd.$chkEOu.$chkEOr,0,-1);
// Strings zusammenstellen
$strHost		  = $myVisClass->makeCommaString($chkSelHost);
$strHostGroups    = $myVisClass->makeCommaString($chkSelHostGroup);
$strContactGroups = $myVisClass->makeCommaString($chkSelContactGroup);
if (($chkModus == "insert") || ($chkModus == "modify")) {
	// Daten Einfügen oder Aktualisieren
	$strSQL2 = "tbl_hostescalation SET config_name='$chkTfConfigName', host_name='$strHost', hostgroup_name='$strHostGroups', 
				contact_groups='$strContactGroups', first_notification=$chkTfFirstNotif, last_notification=$chkTfLastNotif, 
				notification_interval=$chkTfNotifInterval, escalation_period='$chkSelEscPeriod', escalation_options='$strEO',
				active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL1 = "INSERT INTO ";
		$strSQL3 = "";
	} else {
		$strSQL1 = "UPDATE ";
		$strSQL3 = " WHERE id=$chkDataId";	
	}	
	$strSQL = $strSQL1.$strSQL2.$strSQL3;
	if (($chkSelHost != "") && ($strContactGroups != "") && ( $chkTfFirstNotif != "NULL") && 
	    ($chkTfLastNotif != "NULL") && ($chkTfNotifInterval != "NULL")) {
		$myVisClass->dataInsert($strSQL);
		$strMessage = $myVisClass->strDBMessage;
		if ($chkModus == "insert") $myVisClass->writeLog($LANG['logbook']['newhostesc']." ".$chkTfConfigName);
		if ($chkModus == "modify") $myVisClass->writeLog($LANG['logbook']['modifyhostesc']." ".$chkTfConfigName);
	} else {
		$strMessage  = $LANG['db']['datamissing'];
	}
	$chkModus = "display";
}  else if ($chkModus == "make") {
	// Konfigurationsdatei schreiben
	$myVisClass->createConfig("tbl_hostescalation");
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$myVisClass->dataDelete("tbl_hostescalation",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$myVisClass->dataCopy("tbl_hostescalation",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_hostescalation WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	$chkModus      = "add";
}
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myVisClass->lastModified("tbl_hostescalation",$strLastModified,$strFileDate,$strOld);
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm5']." -> ".$LANG['menu']['info13']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['hostescal']);
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
	// Hostfelder füllem
	$intReturn = 0;
	$strSQL    = "SELECT host_name FROM tbl_host WHERE active='1' ORDER BY host_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_HOST","host_name","host_name","host",2);
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_host']."<br>";
	// Hostgruppenfelder füllem
	$strSQL    = "SELECT hostgroup_name FROM tbl_hostgroup WHERE active='1' ORDER BY hostgroup_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_HOSTGROUP","hostgroup_name","hostgroup_name","hostgroup",2);
	// Eskalationsfelder füllem
	$strSQL    = "SELECT timeperiod_name FROM tbl_timeperiod WHERE active='1' ORDER BY timeperiod_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_ESCPERIOD","timeperiod_name","escalation_period","escperiod",1);
	// Kontaktgruppenfelder füllem
	$intReturn = 0;
	$strSQL    = "SELECT contactgroup_name FROM tbl_contactgroup WHERE active='1' ORDER BY contactgroup_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_CONTACTGROUP","contactgroup_name","contact_groups","contactgroup");
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
	$conttp->setVariable("LIMIT",$chkLimit);
	if ($strDBWarning != "") $conttp->setVariable("WARNING",$strDBWarning.$LANG['admintable']['warn_save']);
	$conttp->setVariable("ACT_CHECKED","checked");
	$conttp->setVariable("MODUS","insert");
	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		// Im Modus "Modifizieren" die Datenfelder setzen
		foreach($arrModifyData AS $key => $value) {
			if (($key == "active") || ($key == "last_modified")) continue;
			$conttp->setVariable("DAT_".strtoupper($key),htmlspecialchars($value));
		}
		if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
		$conttp->setVariable("MODUS","modify");
		foreach(explode(",",$arrModifyData['escalation_options']) AS $elem) {
			$conttp->setVariable("DAT_EO".strtoupper($elem)."_CHECKED","checked");
		}	
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
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['hostnames']." / ".$LANG['admintable']['hostgroups']);	
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);	
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_hostescalation");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_hostescalation";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";} else {$intCount = (int)$arrDataLinesCount['number'];}
	// Datensätze holen
	$strSQL    = "SELECT id, config_name, host_name, hostgroup_name, active 
				  FROM tbl_hostescalation ORDER BY config_name LIMIT $chkLimit,15";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
	} else if ($intDataCount != 0) {
		for ($i=0;$i<$intDataCount;$i++) {	
			// Jede zweite Zeile einfärben (Klassen setzen)
			$strClassL = "tdld"; $strClassM = "tdmd"; $strChbClass = "checkboxline";
			if ($i%2 == 1) {$strClassL = "tdlb"; $strClassM = "tdmb"; $strChbClass = "checkbox";}
			if ($arrDataLines[$i]['active'] == 0) {$strActive = $LANG['common']['no_nak'];} else {$strActive = $LANG['common']['yes_ok'];}	
			// Datenfelder setzen
			foreach($LANG['admintable'] AS $key => $value) {
				$mastertp->setVariable("LANG_".strtoupper($key),$value);
			} 
			$mastertp->setVariable("DATA_FIELD_1",$arrDataLines[$i]['config_name']);
			if ($arrDataLines[$i]['host_name'] != "") {
				if (strlen($arrDataLines[$i]['host_name']) > 50) {$strAdd = ".....";} else {$strAdd = "";}
				$mastertp->setVariable("DATA_FIELD_2",substr($arrDataLines[$i]['host_name'],0,50).$strAdd);
			} else {
				if (strlen($arrDataLines[$i]['hostgroup_name']) > 50) {$strAdd = ".....";} else {$strAdd = "";}
				$mastertp->setVariable("DATA_FIELD_2",substr($arrDataLines[$i]['hostgroup_name'],0,50).$strAdd);
			}
			$mastertp->setVariable("DATA_ACTIVE",$strActive);
			$mastertp->setVariable("LINE_ID",$arrDataLines[$i]['id']);
			$mastertp->setVariable("CELLCLASS_L",$strClassL);
			$mastertp->setVariable("CELLCLASS_M",$strClassM);
			$mastertp->setVariable("CHB_CLASS",$strChbClass);
			$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
			if ($chkModus != "display") $conttp->setVariable("DISABLED","disabled");		
			$mastertp->parse("datarow");
		}		
	} else {
		$mastertp->setVariable("DATA_FIELD_1",$LANG['admintable']['nodata']);
		$mastertp->setVariable("DATA_FIELD_2","&nbsp;");
		$mastertp->setVariable("DATA_ACTIVE","&nbsp;");
		$mastertp->setVariable("CELLCLASS_L","tdlb");
		$mastertp->setVariable("CELLCLASS_M","tdmb");
		$mastertp->setVariable("CHB_CLASS","checkbox");
		$mastertp->setVariable("DISABLED","disabled");
	}
	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	if (isset($intCount)) $mastertp->setVariable("PAGES",$myVisClass->buildPageLinks($_SERVER['PHP_SELF'],$intCount,$chkLimit));
	$mastertp->parse("datatable");
	$mastertp->show("datatable");
}
// Mitteilungen ausgeben
if (isset($strMessage)) $mastertp->setVariable("DBMESSAGE",$strMessage);
$mastertp->setVariable("LAST_MODIFIED",$LANG['db']['last_modified']."<b>".$strLastModified."</b>");
$mastertp->setVariable("FILEDATE",$LANG['common']['filedate']."<b>".$strFileDate."</b>");
$mastertp->setVariable("FILEISOLD","<br><span class=\"dbmessage\">".$strOld."</span>");
$mastertp->parse("msgfooter");
$mastertp->show("msgfooter");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>