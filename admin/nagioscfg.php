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
// Zweck:	Nagios Hauptkonfiguration
// Datei:	admin/nagioscfg.php
// Version: 2.00.00 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 6;
$intSub  		= 22;
$intMenu 		= 2;
$preContent 	= "nagioscfg.tpl.htm";
$strConfig		= "";
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
$chkNagiosConf 	= isset($_POST['taNagiosCfg']) 	? $_POST['taNagiosCfg'] : "";
//
// Daten verarbeiten
// =================
if ($chkNagiosConf != "") {
	// Konfiguration schreiben
	$strOldDate    = date("YmdHis",mktime());
	$strFilename   = $SETS['nagios']['config']."nagios.cfg";
	$strBackupfile = $SETS['nagios']['backup']."nagios.cfg_old_".$strOldDate;
	if (file_exists($strFilename) && (is_writable($strFilename))) {
		// Alte Konfiguration sichern
		$strOldDate = date("YmdHis",mktime());
		copy($strFilename,$strBackupfile);
		// Neue Konfiguration sschreiben
		$resFile = fopen($strFilename,"w");
		$chkNagiosConf = stripslashes($chkNagiosConf);
		fputs($resFile,$chkNagiosConf);
		fclose($resFile);
		$strMessage = $LANG['file']['success'];
		$myDataClass->writeLog($LANG['logbook']['config']." ".$strFilename);
	} else {
		$strMessage = $LANG['file']['failed'];
		$myDataClass->writeLog($LANG['logbook']['configfail']." ".$strFilename);	
	}
}
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm1']." -> ".$LANG['menu']['item_admsub22']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['nagiosconfig']);
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
if (file_exists($SETS['nagios']['config']."nagios.cfg") && is_readable($SETS['nagios']['config']."nagios.cfg")) {
	$resFile = fopen($SETS['nagios']['config']."nagios.cfg","r");
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
$maintp->setVariable("VERSION_INFO","NagiosQL - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>