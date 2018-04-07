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
// Zweck:	Benutzeradministration
// Datei:	admin/users.php
// Version: 2.00.00 (Internal)
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
$chkInsAlias    = isset($_POST['tfAlias']) 		? addslashes($_POST['tfAlias']) 	: "";
$chkHidName 	= isset($_POST['hidName']) 		? addslashes($_POST['hidName'])		: "";
$chkInsPwd1 	= isset($_POST['tfPassword1']) 	? $_POST['tfPassword1'] : "";
$chkInsPwd2		= isset($_POST['tfPassword2']) 	? $_POST['tfPassword2'] : "";
$chkInsKey1		= isset($_POST['chbKey1']) 		? $_POST['chbKey1'] 	: 0;
$chkInsKey2		= isset($_POST['chbKey2']) 		? $_POST['chbKey2'] 	: 0;
$chkInsKey3		= isset($_POST['chbKey3']) 		? $_POST['chbKey3'] 	: 0;
$chkInsKey4		= isset($_POST['chbKey4']) 		? $_POST['chbKey4'] 	: 0;
$chkInsKey5		= isset($_POST['chbKey5']) 		? $_POST['chbKey5'] 	: 0;
$chkInsKey6		= isset($_POST['chbKey6']) 		? $_POST['chbKey6'] 	: 0;
$chkInsKey7		= isset($_POST['chbKey7']) 		? $_POST['chbKey7'] 	: 0;
$chkInsKey8		= isset($_POST['chbKey8']) 		? $_POST['chbKey8'] 	: 0;
//
// Daten verarbeiten
// =================
$strKeys = $chkInsKey1.$chkInsKey2.$chkInsKey3.$chkInsKey4.$chkInsKey5.$chkInsKey6.$chkInsKey7.$chkInsKey8;
// Datein einfügen oder modifizieren
if (($chkModus == "insert") || ($chkModus == "modify")) {
	// Passwort prüfen
	if ((($chkInsPwd1 === $chkInsPwd2) && (strlen($chkInsPwd1) > 5)) || (($chkModus == "modify") && ($chkInsPwd1 == ""))) {
		if ($chkInsPwd1 == "") {$strPasswd = "";} else {$strPasswd = "password=MD5('$chkInsPwd1'),";}
		// Adminrechte garantieren
		if ($chkHidName == "Admin") {$chkInsName="Admin";}
		if ($chkInsName == "Admin") {$strKeys="11111111";} 
		// Daten Einfügen oder Aktualisieren
		$strSQLx = "tbl_user SET username='$chkInsName', alias='$chkInsAlias', access_rights='$strKeys', 
					$strPasswd active='$chkActive', last_modified=NOW()";
		if ($chkModus == "insert") {
			$strSQL = "INSERT INTO ".$strSQLx; 
		} else {
			$strSQL = "UPDATE ".$strSQLx." WHERE id=$chkDataId";      
		}	
		if (($chkInsName != "") && ($chkInsAlias != "")) {
			$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
			if ($intInsert == 1) 			$strMessage = $myDataClass->strDBMessage;
			if ($chkModus  == "insert") 	$myDataClass->writeLog($LANG['logbook']['newuser']." ".$chkInsName);
			if ($chkModus  == "modify") 	$myDataClass->writeLog($LANG['logbook']['modifyuser']." ".$chkInsName);
		} else {
			$strMessage .= $LANG['db']['datamissing'];
		}
	} else {
		$strMessage .= $LANG['user']['passwordwrong'];
	}
	$chkModus = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$intReturn = $myDataClass->dataDeleteSimple("tbl_user",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$intReturn = $myDataClass->dataCopySimple("tbl_user",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_user WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	$chkModus      = "add";
}
// Statusmitteilungen setzen
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $myDataClass->strDBMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
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
	// Im Modus "Modifizieren" die Datenfelder setzen
	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		foreach($arrModifyData AS $key => $value) {
			if (($key == "active") || ($key == "last_modified")) continue;
			$conttp->setVariable("DAT_".strtoupper($key),htmlspecialchars(stripslashes($value)));
		}
		if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
		// Schlüssel
		$arrKeys = $myVisClass->getKeyArray($arrModifyData['access_rights']);
		for ($i=1;$i<9;$i++) {
			if ($arrKeys[$i-1] == 1) $conttp->setVariable("KEY".$i."_CHECKED","checked");
		}
		// Adminregeln
		if ($arrModifyData['username'] == "Admin") {
			$conttp->setVariable("NAME_DISABLE","disabled");
			$conttp->setVariable("KEY_DISABLE","disabled");
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
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
	// Datensätze holen
	$strSQL    = "SELECT id, username, alias, active FROM tbl_user ORDER BY username LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
			$mastertp->setVariable("DATA_FIELD_1",stripslashes($arrDataLines[$i]['username']));
			$mastertp->setVariable("DATA_FIELD_2",stripslashes($arrDataLines[$i]['alias']));
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
$maintp->setVariable("VERSION_INFO","NagiosQL - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>