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
// Datum:	30.03.2005
// Zweck:	Kontakte definieren
// Datei:	admin/contacts.php
// Version: 1.02
//
///////////////////////////////////////////////////////////////////////////////
//error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 3;
$intSub  		= 5;
$intMenu 		= 2;
$preContent 	= "contacts.tpl.htm";
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
$chkTfName 				= isset($_POST['tfName']) 			? $_POST['tfName'] 				: "";
$chkTfFriendly 			= isset($_POST['tfFriendly']) 		? $_POST['tfFriendly'] 			: "";
$chkSelContactGroup 	= isset($_POST['selContactGroup']) 	? $_POST['selContactGroup'] 	: array("");
$chkSelHostPeriod 		= isset($_POST['selHostPeriod']) 	? $_POST['selHostPeriod'] 		: "";
$chkSelServicePeriod 	= isset($_POST['selServicePeriod']) ? $_POST['selServicePeriod'] 	: "";
$chkSelHostCommand 		= isset($_POST['selHostCommand']) 	? $_POST['selHostCommand'] 		: "";
$chkSelServiceCommand 	= isset($_POST['selServiceCommand'])? $_POST['selServiceCommand'] 	: "";
$chkTfEmail 			= isset($_POST['tfEmail']) 			? $_POST['tfEmail'] 			: "";
$chkTfPager 			= isset($_POST['tfPager']) 			? $_POST['tfPager'] 			: "";
$chkTfAddress1 			= isset($_POST['tfAddress1']) 		? $_POST['tfAddress1'] 			: "";
$chkTfAddress2 			= isset($_POST['tfAddress2']) 		? $_POST['tfAddress2'] 			: "";
$chkTfAddress3 			= isset($_POST['tfAddress3']) 		? $_POST['tfAddress3'] 			: "";
$chkTfAddress4 			= isset($_POST['tfAddress4']) 		? $_POST['tfAddress4'] 			: "";
$chkTfAddress5 			= isset($_POST['tfAddress5']) 		? $_POST['tfAddress5'] 			: "";
$chkHOd					= isset($_POST['chbHOd'])				? $_POST['chbHOd'].","			: "";
$chkHOu					= isset($_POST['chbHOu'])				? $_POST['chbHOu'].","			: "";
$chkHOr					= isset($_POST['chbHOr'])				? $_POST['chbHOr'].","			: "";
$chkSOw					= isset($_POST['chbSOw'])				? $_POST['chbSOw'].","			: "";
$chkSOu					= isset($_POST['chbSOu'])				? $_POST['chbSOu'].","			: "";
$chkSOc					= isset($_POST['chbSOc'])				? $_POST['chbSOc'].","			: "";
$chkSOr					= isset($_POST['chbSOr'])				? $_POST['chbSOr'].","			: "";
//
// Daten verarbeiten
// =================
$strHO 	  		  = substr($chkHOd.$chkHOu.$chkHOr,0,-1);
$strSO			  = substr($chkSOw.$chkSOu.$chkSOc.$chkSOr,0,-1);
$strContactGroups = $myVisClass->makeCommaString($chkSelContactGroup);
if (($chkModus == "insert") || ($chkModus == "modify")) {
	// Daten Einfügen oder Aktualisieren
	$strSQL2 = "tbl_contact SET contact_name='$chkTfName', alias='$chkTfFriendly', contactgroups='$strContactGroups', 
				host_notification_period='$chkSelHostPeriod', service_notification_period='$chkSelServicePeriod', 
				host_notification_options='$strHO', service_notification_options='$strSO', 
				host_notification_commands='$chkSelHostCommand', service_notification_commands='$chkSelServiceCommand', 
				email='$chkTfEmail', pager='$chkTfPager', address1='$chkTfAddress1', address2='$chkTfAddress2', 
				address3='$chkTfAddress3', address4='$chkTfAddress4', address5='$chkTfAddress5',active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL1 = "INSERT INTO "; 
		$strSQL3 = "";
	} else {
		$strSQL1 = "UPDATE ";      
		$strSQL3 = " WHERE id=".$chkDataId;	
	}	
	$strSQL = $strSQL1.$strSQL2.$strSQL3;
	if (($chkTfName != "") && ($chkTfFriendly != "") && ($chkSelHostPeriod != "") && 
		($chkSelServicePeriod != "") && ($strHO != "") && ($strSO != "")) {	
		$myVisClass->dataInsert($strSQL);
		$strMessage = $myVisClass->strDBMessage;
		if ($chkModus == "insert") $myVisClass->writeLog($LANG['logbook']['newcontact']." ".$chkTfName);
		if ($chkModus == "modify") $myVisClass->writeLog($LANG['logbook']['modifycontact']." ".$chkTfName);
	} else {
		$strMessage  = $LANG['db']['datamissing'];
	}
	$chkModus = "display";
}  else if ($chkModus == "make") {
	// Konfigurationsdatei schreiben
	$myVisClass->createConfig("tbl_contact");
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$myVisClass->dataDelete("tbl_contact",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$myVisClass->dataCopy("tbl_contact",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_contact WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	$chkModus      = "add";
}
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myVisClass->lastModified("tbl_contact",$strLastModified,$strFileDate,$strOld);
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm3']." -> ".$LANG['menu']['item_admsub5']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['contacts']);
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
	// Zeitperiodenfelder füllem
	$intReturn = 0;
	$strSQL    = "SELECT timeperiod_name FROM tbl_timeperiod ORDER BY timeperiod_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_TIMEPERIOD","timeperiod_name","host_notification_period","timeperiodgroup1");
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_TIMEPERIOD","timeperiod_name","service_notification_period","timeperiodgroup2");
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_timeperiod']."<br>";
	// Kommandonamenfelder füllen
	$strSQL    = "SELECT command_name FROM tbl_misccommand ORDER BY command_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_COMMAND","command_name","host_notification_commands","commandgroup1",1);
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_COMMAND","command_name","service_notification_commands","commandgroup2",1);
	// Kontaktgruppenfeld setzen
	$strSQL    = "SELECT contactgroup_name FROM tbl_contactgroup ORDER BY contactgroup_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_CONTACTGROUP","contactgroup_name","contactgroups","contactgroup",1);		
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
		foreach(explode(",",$arrModifyData['host_notification_options']) AS $elem) {
			$conttp->setVariable("DAT_HO".strtoupper($elem)."_CHECKED","checked");
		}
		foreach(explode(",",$arrModifyData['service_notification_options']) AS $elem) {
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
	$mastertp->setVariable("FIELD_1",$LANG['admintable']['contactname']);
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['friendly']);
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);	
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_contact");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_contact";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";} else {$intCount = (int)$arrDataLinesCount['number'];}
	// Datensätze holen
	$strSQL    = "SELECT id, contact_name, alias, active FROM tbl_contact ORDER BY contact_name LIMIT $chkLimit,15";
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
			$mastertp->setVariable("DATA_FIELD_1",$arrDataLines[$i]['contact_name']);
			$mastertp->setVariable("DATA_FIELD_2",$arrDataLines[$i]['alias']);
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
$strContMessage = $myVisClass->checkConsistContacts();
$mastertp->setVariable("CONSISTUSAGE",$strContMessage);
if ($strContMessage == $LANG['admincontent']['contactsok']) {
	$mastertp->setVariable("CON_MSGCLASS","okmessage");
} else {
	$mastertp->setVariable("CON_MSGCLASS","dbmessage");
}
if ($myVisClass->strTempValue1 != "") $mastertp->setVariable("FREEDATA",$myVisClass->strTempValue1);
$mastertp->parse("msgfooter");
$mastertp->show("msgfooter");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>