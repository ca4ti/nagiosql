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
// Zweck:	Löschen der Backupdateien
// Datei:	admin/delback.php
// Version: 2.00.00 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Menuvariabeln für diese Seite
// =============================
$intMain 		= 6;
$intSub  		= 17;
$intMenu 		= 2;
$preContent 	= "delback.tpl.htm";
$intModus		= 0;
//
// Übergabeparameter
// =================
$chkSelFilename	= isset($_POST['selImportFile'])	? $_POST['selImportFile']	: array("");
//
// Vorgabedatei einbinden
// ======================
$preAccess	= 1;
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Formulareingaben verarbeiten
// ============================
if ($chkSelFilename[0] != "") {
	$intModus = 1;
	$strMessage = "";
	foreach($chkSelFilename AS $elem) {
		if (is_writeable(trim($elem))) {
			unlink(trim($elem));
			$myDataClass->writeLog($LANG['logbook']['delfile']." ".trim($elem));
			$strMessage .= $elem.$LANG['file']['deleteok']."<br>";
		} else {
			$strMessage .= $elem.$LANG['file']['deletefail']."<br>";
		}
	}
}
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm6']." -> ".$LANG['menu']['item_admsub17']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['delbackup']);
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("BACKUPFILE",$LANG['file']['backupfile']);
$conttp->setVariable("MAKE",$LANG['file']['delete']);
$conttp->setVariable("ABORT",$LANG['admintable']['abort']);
$conttp->setVariable("MUST_DATA",$LANG['admintable']['mustdata']);
$conttp->setVariable("CTRL_INFO",$LANG['admintable']['ctrlinfo']);
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
// Dateien zusammensuchen
$strCommand = "ls -1 ".$SETS['nagios']['backup']."*.cfg_*;".
              "ls -1 ".$SETS['nagios']['backuphosts']."*.cfg_*;".
			  "ls -1 ".$SETS['nagios']['backupservices']."*.cfg_*;";
$resList = popen($strCommand,"r");
while (!feof($resList)) {
	$strFile = fgets($resList,200);
	if ($strFile != "") {
		$conttp->setVariable("DAT_BACKUPFILE",$strFile);
		$conttp->parse("filelist");
	}
}
pclose($resList);
if ($intModus == 1) $conttp->setVariable("SUCCESS",$strMessage);
$conttp->parse("main");
$conttp->show("main");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>