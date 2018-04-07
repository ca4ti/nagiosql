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
// Datum:	26.02.2005
// Zweck:	CGI Konfiguration
// Datei:	admin/cgicfg.php
// Version: 1.00
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 6;
$intSub  		= 23;
$intMenu 		= 2;
$preContent 	= "nagioscfg.tpl.htm";
$setFileVersion = "1.00";
$strConfig		= "";
$strMessage		= "";
//
// Vorgabedatei einbinden
// ======================
$preRights 	= "admin2";
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkCgiConf 	= isset($_POST['taNagiosCfg']) 	? $_POST['taNagiosCfg'] : "";
//
// Daten verarbeiten
// =================
if ($chkCgiConf != "") {
	// Konfiguration schreiben
	$strOldDate    = date("YmdHis",mktime());
	$strFilename   = $SETS['nagios']['config']."cgi.cfg";
	$strBackupfile = $SETS['nagios']['backup']."cgi.cfg_old_".$strOldDate;
	if (file_exists($strFilename) && (is_writable($strFilename))) {
		// Alte Konfiguration sichern
		$strOldDate = date("YmdHis",mktime());
		copy($strFilename,$strBackupfile);
		// Neue Konfiguration sschreiben
		$resFile = fopen($strFilename,"w");
		$chkCgiConf = stripslashes($chkCgiConf);
		fputs($resFile,$chkCgiConf);
		fclose($resFile);
		$strMessage = $LANG['file']['success'];
		$myVisClass->writeLog($LANG['logbook']['config']." ".$strFilename);
	} else {
		$strMessage = $LANG['file']['failed'];
		$myVisClass->writeLog($LANG['logbook']['configfail']." ".$strFilename);	
	}
}
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm1']." -> ".$LANG['menu']['item_admsub23']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['cgiconfig']);
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
$conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
$conttp->setVariable("MAINSITE",$SETS['path']['root']."admin.php");
$conttp->setVariable("FILL_FIELDEMPTY",$LANG['formchecks']['fill_fieldempty']);
$conttp->setVariable("LANG_SAVE",$LANG['admintable']['save']);
$conttp->setVariable("LANG_ABORT",$LANG['admintable']['abort']);
//
// Konfigurationsdatei öffnen
// ==========================
if (file_exists($SETS['nagios']['config']."cgi.cfg") && is_readable($SETS['nagios']['config']."cgi.cfg")) {
	$resFile = fopen($SETS['nagios']['config']."cgi.cfg","r");
	if ($resFile) {
		while(!feof($resFile)) {
			$strConfig .= fgets($resFile,1024);
		}
	}
} else {
	$strMessage = $LANG['file']['notreadable'];
}
if ($strMessage != "") $conttp->setVariable("MESSAGE",$strMessage);
$conttp->setVariable("DAT_NAGIOS_CONFIG",$strConfig);
$conttp->parse("naginsert");
$conttp->show("naginsert");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>