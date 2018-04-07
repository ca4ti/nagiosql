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
// Datum:	29.03.2005
// Zweck:	Prüfbefehle definieren
// Datei:	admin/checkcommands.php
// Version:	1.02
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 4;
$intSub  		= 4;
$intMenu        = 2;
$preContent     = "commands.tpl.htm";
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
$chkInsName 	= isset($_POST['tfName']) 		? $_POST['tfName'] 		: "";
$chkInsCommand 	= isset($_POST['tfCommand']) 	? $_POST['tfCommand'] 	: "";
//
// Daten verarbeiten
// =================
if (($chkModus == "insert") || ($chkModus == "modify")) {
	// Daten Einfügen oder Aktualisieren
	$strSQL2 = "tbl_checkcommand SET command_name='$chkInsName', command_line='$chkInsCommand', active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL1 = "INSERT INTO "; 
		$strSQL3 = "";
	} else {
		$strSQL1 = "UPDATE ";      
		$strSQL3 = " WHERE id=$chkDataId";	
	}	
	$strSQL = $strSQL1.$strSQL2.$strSQL3;
	if (($chkInsName != "") && ($chkInsCommand != "")) {
		$myVisClass->dataInsert($strSQL);
		$strMessage .= $myVisClass->strDBMessage;
		if ($chkModus == "insert") $myVisClass->writeLog($LANG['logbook']['newchkcmd']." ".$chkInsName);
		if ($chkModus == "modify") $myVisClass->writeLog($LANG['logbook']['modifychkcmd']." ".$chkInsName);
	} else {
		$strMessage  .= $LANG['db']['datamissing'];
	}
	$chkModus = "display";
}  else if ($chkModus == "make") {
	// Konfigurationsdatei schreiben
	$myVisClass->createConfig("tbl_checkcommand");
	$strMessage .= $myVisClass->strDBMessage;
	$chkModus    = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$myVisClass->dataDelete("tbl_checkcommand",$chkListId);
	$strMessage .= $myVisClass->strDBMessage;
	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$myVisClass->dataCopy("tbl_checkcommand",$chkListId);
	$strMessage .= $myVisClass->strDBMessage;
	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_checkcommand WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	$chkModus      = "add";
}
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myVisClass->lastModified("tbl_checkcommand",$strLastModified,$strFileDate,$strOld);
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm3']." -> ".$LANG['menu']['item_admsub4']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['checkcommands']);
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if ($chkModus == "add") {
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
	$mastertp->setVariable("FIELD_1",$LANG['admintable']['checkcmd']);
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['commandline']);	
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);	
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_checkcommand");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_checkcommand";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";} else {$intCount = (int)$arrDataLinesCount['number'];}
	// Datensätze holen
	$strSQL    = "SELECT id, command_name, command_line, active FROM tbl_checkcommand ORDER BY command_name LIMIT $chkLimit,15";
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
			if (strlen($arrDataLines[$i]['command_line']) > 50) {$strAdd = ".....";} else {$strAdd = "";}
			$mastertp->setVariable("DATA_FIELD_1",$arrDataLines[$i]['command_name']);
			$mastertp->setVariable("DATA_FIELD_2",substr($arrDataLines[$i]['command_line'],0,50).$strAdd);
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
if ($strMessage != "") $mastertp->setVariable("DBMESSAGE",$strMessage);
$mastertp->setVariable("LAST_MODIFIED",$LANG['db']['last_modified']."<b>".$strLastModified."</b>");
$mastertp->setVariable("FILEDATE",$LANG['common']['filedate']."<b>".$strFileDate."</b>");
$mastertp->setVariable("FILEISOLD","<br><span class=\"dbmessage\">".$strOld."</span>");
$strContMessage = $myVisClass->checkConsistCheckcommands();
$mastertp->setVariable("CONSISTUSAGE",$strContMessage);
if ($strContMessage == $LANG['admincontent']['checkcommandsok']) {
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