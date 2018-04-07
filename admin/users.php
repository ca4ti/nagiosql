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
// Datum:	07.03.2005
// Zweck:	Benutzeradministration
// Datei:	admin/users.php
// Version: 1.02
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 7;
$intSub  		= 18;
$intMenu 		= 2;
$preContent 	= "users.tpl.htm";
$setFileVersion = "1.02";
$intCount		= 0;
$strMessage		= "";
//
// Vorgabedatei einbinden
// ======================
$preRights 	= "admin3";
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkInsName 	= isset($_POST['tfName']) 		? $_POST['tfName'] 		: "";
$chkInsAlias    = isset($_POST['tfAlias']) 		? $_POST['tfAlias'] 	: "";
$chkInsPwd1 	= isset($_POST['tfPassword1']) 	? $_POST['tfPassword1'] : "";
$chkInsPwd2		= isset($_POST['tfPassword2']) 	? $_POST['tfPassword2'] : "";
$chkInsAdmin1	= isset($_POST['chbAdmin1']) 	? $_POST['chbAdmin1'] 	: 0;
$chkInsAdmin2	= isset($_POST['chbAdmin2']) 	? $_POST['chbAdmin2'] 	: 0;
$chkInsAdmin3	= isset($_POST['chbAdmin3']) 	? $_POST['chbAdmin3'] 	: 0;
//
// Daten verarbeiten
// =================
if (($chkModus == "insert") || ($chkModus == "modify")) {
	// Passwort prüfen
	if ((($chkInsPwd1 === $chkInsPwd2) && (strlen($chkInsPwd1) > 5)) || (($chkModus == "modify") && ($chkInsPwd1 == ""))) {
		if ($chkInsPwd1 == "") {$strPasswd = "";} else {$strPasswd = "password=MD5('$chkInsPwd1'),";}
		// Daten Einfügen oder Aktualisieren
		$strSQL2 = "tbl_user SET username='$chkInsName', alias='$chkInsAlias', $strPasswd admin1='$chkInsAdmin1', 
					admin2='$chkInsAdmin2', admin3='$chkInsAdmin3', active='$chkActive'";
		if ($chkModus == "insert") {
			$strSQL1 = "INSERT INTO "; 
			$strSQL3 = "";
		} else {
			$strSQL1 = "UPDATE ";      
			$strSQL3 = " WHERE id=$chkDataId";	
		}	
		$strSQL = $strSQL1.$strSQL2.$strSQL3;
		if (($chkInsName != "") && ($chkInsAlias != "")) {
			$myVisClass->dataInsert($strSQL);
			$strMessage = $myVisClass->strDBMessage;
			if ($chkModus == "insert") $myVisClass->writeLog($LANG['logbook']['newuser']." ".$chkInsName);
			if ($chkModus == "modify") $myVisClass->writeLog($LANG['logbook']['modifyuser']." ".$chkInsName);
		} else {
			$strMessage  = $LANG['db']['datamissing'];
		}
	} else {
		$strMessage  = $LANG['user']['passwordwrong'];
	}
	$chkModus = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$myVisClass->dataDelete("tbl_user",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$myVisClass->dataCopy("tbl_user",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_user WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	$chkModus      = "add";
}
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm6']." -> ".$LANG['menu']['item_admsub18']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['useradmin']);
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if ($chkModus == "add") {
	// Feldbeschriftungen setzen
	foreach($LANG['user'] AS $key => $value) {
		$conttp->setVariable("LANG_".strtoupper($key),$value);
	}
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
	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		// Im Modus "Modifizieren" die Datenfelder setzen
		foreach($arrModifyData AS $key => $value) {
			if (($key == "active") || ($key == "last_modified")) continue;
			$conttp->setVariable("DAT_".strtoupper($key),htmlspecialchars($value));
		}
		if ($arrModifyData['admin1'] != 0) $conttp->setVariable("ADMIN1_CHECKED","checked");
		if ($arrModifyData['admin2'] != 0) $conttp->setVariable("ADMIN2_CHECKED","checked");
		if ($arrModifyData['admin3'] != 0) $conttp->setVariable("ADMIN3_CHECKED","checked");
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
	$mastertp->setVariable("FIELD_1",$LANG['user']['username']);
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['friendly']);	
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);	
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_timeperiod");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_user";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";} else {$intCount = (int)$arrDataLinesCount['number'];}
	// Datensätze holen
	$strSQL    = "SELECT id, username, alias, active FROM tbl_user ORDER BY username LIMIT $chkLimit,15";
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
			$mastertp->setVariable("DATA_FIELD_1",$arrDataLines[$i]['username']);
			$mastertp->setVariable("DATA_FIELD_2",$arrDataLines[$i]['alias']);
			$mastertp->setVariable("DATA_ACTIVE",$strActive);
			$mastertp->setVariable("LINE_ID",$arrDataLines[$i]['id']);
			$mastertp->setVariable("CELLCLASS_L",$strClassL);
			$mastertp->setVariable("CELLCLASS_M",$strClassM);
			$mastertp->setVariable("CHB_CLASS",$strChbClass);
			$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
			if ($chkModus != "display") $conttp->setVariable("DISABLED","disabled");		
			$mastertp->parse("datarowuser");
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
	$mastertp->parse("datatableuser");
	$mastertp->show("datatableuser");
}
// Mitteilungen ausgeben
if (isset($strMessage)) {$mastertp->setVariable("DBMESSAGE",$strMessage);} else {$mastertp->setVariable("DBMESSAGE","&nbsp;");}
$mastertp->parse("msgfooter");
$mastertp->show("msgfooter");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>