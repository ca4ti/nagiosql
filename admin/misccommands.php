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
// Zweck:	Allgemeine Befehle definieren
// Datei:	admin/misccommands.php
// Version: 2.0.2 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 4;
$intSub  		= 3;
$intMenu 		= 2;
$preContent     = "misc_and_checkcommands.tpl.htm";
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
$chkInsName 	= isset($_POST['tfName']) 		? addslashes($_POST['tfName']) 		: "";
$chkInsCommand 	= isset($_POST['tfCommand']) 	? addslashes($_POST['tfCommand']) 	: "";
//
// Daten verarbeiten
// =================
if (($chkModus == "insert") || ($chkModus == "modify")) {
	// Daten Einfügen oder Aktualisieren
	if ($hidActive == 1) $chkActive = 1;
	$strSQLx = "tbl_misccommand SET command_name='$chkInsName', command_line='$chkInsCommand', active='$chkActive', 
				last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL = "INSERT INTO ".$strSQLx; 
	} else {
		$strSQL = "UPDATE ".$strSQLx." WHERE id=$chkDataId";   
	}	
	if (($chkInsName != "") && ($chkInsCommand != "")) {
		$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		if ($intInsert == 1) {
			$intReturn = 1;
		} else {
			if ($chkModus  == "insert") 	$myDataClass->writeLog($LANG['logbook']['newmisccmd']." ".$chkInsName);
			if ($chkModus  == "modify") 	$myDataClass->writeLog($LANG['logbook']['modifymisccmd']." ".$chkInsName);
			$intReturn = 0;
		}
	} else {
		$strMessage    .= $LANG['db']['datamissing'];
	}
	$chkModus = "display";
}  else if ($chkModus == "make") {
	// Konfigurationsdatei schreiben
	$intReturn = $myConfigClass->createConfig("tbl_misccommand",0);
	$chkModus  = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$intReturn = $myDataClass->dataDeleteSimple("tbl_misccommand",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$intReturn = $myDataClass->dataCopySimple("tbl_misccommand",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_misccommand WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";	
	$chkModus      = "add";
}
// Statusmitteilungen setzen
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $myDataClass->strDBMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myConfigClass->lastModified("tbl_misccommand",$strLastModified,$strFileDate,$strOld);
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm3']." -> ".$LANG['menu']['item_admsub3']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['misccommands']);
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
	$conttp->setVariable("ACT_CHECKED","checked");
	$conttp->setVariable("MODUS","insert");
	// Im Modus "Modifizieren" die Datenfelder setzen
	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		foreach($arrModifyData AS $key => $value) {
			if (($key == "active") || ($key == "last_modified") || ($key == "access_rights")) continue;
			$conttp->setVariable("DAT_".strtoupper($key),htmlspecialchars(stripslashes($value)));
		}
		if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
		// Prüfen, ob dieser Eintrag in einer anderen Konfiguration verwendet wird
		if ($myDataClass->checkMustdata("tbl_misccommand",$arrModifyData['id'],$arrInfo) != 0) {
			$conttp->setVariable("ACT_DISABLED","disabled");
			$conttp->setVariable("ACTIVE","1");
			$conttp->setVariable("CHECK_MUST_DATA","<span class=\"dbmessage\">".$LANG['admintable']['noactivate']."</span>");
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
	$mastertp->setVariable("FIELD_1",$LANG['admintable']['command']);
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['commandline']);	
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);	
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_misccommand");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_misccommand";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
	// Datensätze holen
	$strSQL    = "SELECT id, command_name, command_line, active FROM tbl_misccommand 
				  ORDER BY command_name LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
			$mastertp->setVariable("DATA_FIELD_2",substr(stripslashes($arrDataLines[$i]['command_line']),0,50));
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
	// Seiten anzeigen
	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	if (isset($intCount)) $mastertp->setVariable("PAGES",$myVisClass->buildPageLinks($_SERVER['PHP_SELF'],$intCount,$chkLimit));
	$mastertp->parse("datatable");
	$mastertp->show("datatable");
}
// Mitteilungen ausgeben
if (isset($strMessage) && ($strMessage != "")) $mastertp->setVariable("DBMESSAGE",$strMessage);
$mastertp->setVariable("LAST_MODIFIED",$LANG['db']['last_modified']."<b>".$strLastModified."</b>");
$mastertp->setVariable("FILEDATE",$LANG['common']['filedate']."<b>".$strFileDate."</b>");
if ($strOld != "") $mastertp->setVariable("FILEISOLD","<br><span class=\"dbmessage\">".$strOld."</span><br>");
$mastertp->parse("msgfooter");
$mastertp->show("msgfooter");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org'>NagiosQL</a> - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>