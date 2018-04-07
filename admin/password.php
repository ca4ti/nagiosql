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
// Zweck:	Passwort wechseln
// Datei:	admin/password.php
// Version:	1.02
//
///////////////////////////////////////////////////////////////////////////////
//error_reporting(E_ALL);
// 
// Menuvariabeln für diese Seite
// =============================
$intMain 		= 7;
$intSub  		= 20;
$intMenu 		= 2;
$preContent 	= "admin_master.tpl.htm";
$setFileVersion = "1.02";
$strMessage		= "";
//
// Vorgabedatei einbinden
// ======================
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkInsPasswdOld 	= isset($_POST['tfPasswordOld']) 	? $_POST['tfPasswordOld'] 	: "";
$chkInsPasswdNew1 	= isset($_POST['tfPasswordNew1']) 	? $_POST['tfPasswordNew1'] 	: "";
$chkInsPasswdNew2 	= isset($_POST['tfPasswordNew2']) 	? $_POST['tfPasswordNew2'] 	: "";
//
// Passwort wechseln
// =================
if (($chkInsPasswdOld != "") && ($chkInsPasswdNew1 != "")) {
	// Passwort prüfen
	$strSQL    = "SELECT * FROM tbl_user WHERE username='".$_SESSION['username']."' AND password=MD5('$chkInsPasswdOld')";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
	} else if ($intDataCount == 1) {
		if (($chkInsPasswdNew1 === $chkInsPasswdNew2) && (strlen($chkInsPasswdNew1) >=5)) {
			// Letzte DB Eintrag aktualisieren
			$strSQLUpdate = "UPDATE tbl_user SET password=MD5('$chkInsPasswdNew1'), last_login=NOW() WHERE username='".$_SESSION['username']."'";
			$booReturn = $myDBClass->insertData($strSQLUpdate);
			if ($booReturn == true) {
				$myVisClass->writeLog($LANG['logbook']['pwdchanged']);
				// Neues Login erzwingen
				$_SESSION['username'] = "";
				header("Location: http://".$_SERVER['HTTP_HOST'].$SETS['path']['root']."index.php");
			} else {
				$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
			}

		} else {
			// Neues Passwort ungültig
			$strMessage .= $LANG['user']['passwordwrong'];
		}
	} else {
		// Altes Passwort falsch
		$strMessage .= $LANG['user']['oldpwfailed'];
	}	
	// Passwort falsch
	$strMessage .= $LANG['db']['datamissing'];
}
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['user']['pwchange']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
foreach($LANG['user'] AS $key => $value) {
	$conttp->setVariable("LANG_".strtoupper($key),$value);
}
$conttp->setVariable("LANG_SAVE",$LANG['admintable']['save']);
$conttp->setVariable("LANG_ABORT",$LANG['admintable']['abort']);
foreach($LANG['formchecks'] AS $key => $value) {
	$conttp->setVariable(strtoupper($key),$value);
}
if ($strMessage != "") $conttp->setVariable("MESSAGE",$strMessage);
$conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->parse("passwordsite");
$conttp->show("passwordsite");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>