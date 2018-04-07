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
// Zweck:	Service Zusatzinformationen definieren
// Datei:	admin/serviceextinfo.php
// Version: 1.02
//
///////////////////////////////////////////////////////////////////////////////
//error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 5;
$intSub  		= 15;
$intMenu 		= 2;
$preContent 	= "serviceextinfo.tpl.htm";
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
$chkSelHost 		= isset($_POST['selHost']) 			? $_POST['selHost'] 		: "";
$chkSelService	 	= isset($_POST['selService']) 		? $_POST['selService']		: "";
$chkTfNotes		 	= isset($_POST['tfNotes']) 			? $_POST['tfNotes'] 		: "";
$chkTfNotesURL		= isset($_POST['tfNotesURL']) 		? $_POST['tfNotesURL'] 		: "";
$chkTfActionURL 	= isset($_POST['tfActionURL']) 		? $_POST['tfActionURL'] 	: "";
$chkTfIconImage		= isset($_POST['tfIconImage']) 		? $_POST['tfIconImage'] 	: "";
$chkTfIconImageAlt 	= isset($_POST['tfIconImageAlt']) 	? $_POST['tfIconImageAlt'] 	: "";
//
// Daten verarbeiten
// =================
if (($chkModus == "insert") || ($chkModus == "modify")) {
	// Daten Einfügen oder Aktualisieren
	$strSQL2 = "tbl_serviceextinfo SET host_name='$chkSelHost', service_description='$chkSelService', notes='$chkTfNotes', 
				notes_url='$chkTfNotesURL', action_url='$chkTfActionURL', icon_image='$chkTfIconImage', 
				icon_image_alt='$chkTfIconImageAlt', active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL1 = "INSERT INTO ";
		$strSQL3 = "";
	} else {
		$strSQL1 = "UPDATE ";
		$strSQL3 = " WHERE id=$chkDataId";	
	}	
	$strSQL = $strSQL1.$strSQL2.$strSQL3;
	if (($chkSelHost != "") && ($chkSelService != "")) {
		$myVisClass->dataInsert($strSQL);
		$strMessage = $myVisClass->strDBMessage;
		if ($chkModus == "insert") $myVisClass->writeLog($LANG['logbook']['newservext']." ".$chkSelHost."::".$chkSelService);
		if ($chkModus == "modify") $myVisClass->writeLog($LANG['logbook']['modifyservext']." ".$chkSelHost."::".$chkSelService);
	} else {
		$strMessage  = $LANG['db']['datamissing'];
	}
	$chkModus = "display";
}  else if ($chkModus == "make") {
	// Konfigurationsdatei schreiben
	$myVisClass->createConfig("tbl_serviceextinfo");
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$myVisClass->dataDelete("tbl_serviceextinfo",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$myVisClass->dataCopy("tbl_serviceextinfo",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_serviceextinfo WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";	
	$chkModus      = "add";
}
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myVisClass->lastModified("tbl_serviceextinfo",$strLastModified,$strFileDate,$strOld);
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm5']." -> ".$LANG['menu']['item_admsub15']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['serviceextinfo']);
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if (($chkModus == "add") || ($chkModus == "refresh")) {
	// Datenbankabfragen
	$chkGetHost 				= $chkSelHost;
	$myVisClass->strTempValue1 	= $chkSelModify;
	$myVisClass->strTempValue2 	= $chkModus;
	$myVisClass->resTemplate   	=& $conttp;
	if (isset($arrModifyData)) $myVisClass->arrWorkdata = $arrModifyData;
	// Hostfelder füllen
	$intReturn = 0;
	$strSQL    = "SELECT host_name FROM tbl_host ORDER BY host_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_HOST","host_name","host_name","host",0,$chkGetHost);
	if ($chkGetHost == "") 	$chkGetHost = $myVisClass->strTempValue3;
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_host']."<br>";
	// Servicefelder füllen
	$intReturn = 0;
	$strSQL    = "SELECT service_description FROM tbl_service WHERE host_name='$chkGetHost' ORDER BY service_description";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_SERVICE","service_description","service_description","service",0,$chkSelService);
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_service']."<br>";	
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
	if ($chkModus == "refresh") {
		$conttp->setVariable("DAT_NOTES",$chkTfNotes);
		$conttp->setVariable("DAT_NOTES_URL",$chkTfNotesURL);
		$conttp->setVariable("DAT_ACTION_URL",$chkTfActionURL);
		$conttp->setVariable("DAT_ICON_IMAGE",$chkTfIconImage);
		$conttp->setVariable("DAT_ICON_IMAGE_ALT",$chkTfIconImageAlt);
		if ($chkActive != 1) $conttp->setVariable("ACT_CHECKED","");
		if ($chkDataId != 0) {
			$conttp->setVariable("MODUS","modify");
			$conttp->setVariable("ID",$chkDataId);
		}
	} else if (isset($arrModifyData) && ($chkSelModify == "modify")) {
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
	$mastertp->setVariable("FIELD_1",$LANG['admintable']['hostname']);
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['service']);	
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);	
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_serviceextinfo");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_serviceextinfo";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";} else {$intCount = (int)$arrDataLinesCount['number'];}
	// Datensätze holen
	$strSQL    = "SELECT id, host_name, service_description, active
				  FROM tbl_serviceextinfo ORDER BY host_name,service_description LIMIT $chkLimit,15";
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
			$mastertp->setVariable("DATA_FIELD_1",$arrDataLines[$i]['host_name']);
			$mastertp->setVariable("DATA_FIELD_2",$arrDataLines[$i]['service_description']);
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