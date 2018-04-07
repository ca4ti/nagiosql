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
// Zweck:	Passwort wechseln
// Datei:	admin/password.php
// Version: 2.00.00 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Menuvariabeln f�r diese Seite
// =============================
$intMain 		= 7;
$intSub  		= 20;
$intMenu 		= 2;
$preContent 	= "admin_master.tpl.htm";
$strMessage		= "";
//
// Vorgabedatei einbinden
// ======================
$preAccess	= 1;
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// �bergabeparameter
// =================
$chkInsPasswdOld 	= isset($_POST['tfPasswordOld']) 	? $_POST['tfPasswordOld'] 	: "";
$chkInsPasswdNew1 	= isset($_POST['tfPasswordNew1']) 	? $_POST['tfPasswordNew1'] 	: "";
$chkInsPasswdNew2 	= isset($_POST['tfPasswordNew2']) 	? $_POST['tfPasswordNew2'] 	: "";
//
// Passwort wechseln
// =================
if (($chkInsPasswdOld != "") && ($chkInsPasswdNew1 != "")) {
	// Passwort pr�fen
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
				$myDataClass->writeLog($LANG['logbook']['pwdchanged']);
				// Neues Login erzwingen
				$_SESSION['username'] = "";
				header("Location: http://".$_SERVER['HTTP_HOST'].$SETS['path']['root']."index.php");
			} else {
				$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
			}

		} else {
			// Neues Passwort ung�ltig
			$strMessage .= $LANG['user']['passwordwrong'];
		}
	} else {
		// Altes Passwort falsch
		$strMessage .= $LANG['user']['oldpwfailed'];
	}	
} else if (isset($_POST['submit'])) {
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
$conttp->setVariable("LANG_MUSTDATA",$LANG['admintable']['mustdata']);
foreach($LANG['formchecks'] AS $key => $value) {
	$conttp->setVariable(strtoupper($key),$value);
}
if ($strMessage != "") $conttp->setVariable("PW_MESSAGE",$strMessage);
$conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->parse("passwordsite");
$conttp->show("passwordsite");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>