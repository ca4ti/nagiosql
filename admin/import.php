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
// Datum:	27.02.2005
// Zweck:	Datenimport
// Datei:	admin/import.php
// Version:	1.00
//
///////////////////////////////////////////////////////////////////////////////
error_reporting(E_ALL);
// 
// Menuvariabeln für diese Seite
// =============================
$intMain 		= 6;
$intSub  		= 16;
$intMenu 		= 2;
$preContent 	= "import.tpl.htm";
$setFileVersion = "1.00";
$intModus		= 0;
//
// Vorgabedatei einbinden
// ======================
$preRights 	= "admin2";
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkSelFilename	= isset($_POST['selImportFile'])	? $_POST['selImportFile']	: array("");
$chkOverwrite	= isset($_POST['chbOverwrite'])		? $_POST['chbOverwrite']	: 0;
//
// Formulareingaben verarbeiten
// ============================
if ($chkSelFilename[0] != "") {
	$myVisClass->strMessage = "";
	foreach($chkSelFilename AS $elem) {
		$intModus  = 1;
		$intReturn = $myVisClass->fileImport($elem,$chkOverwrite);
		$myVisClass->writeLog($LANG['logbook']['import']." ".$elem." [".$chkOverwrite."]");
		if ($intReturn == 1) $myVisClass->strMessage .= $myVisClass->strDBMessage;
	}
}
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm6']." -> ".$LANG['menu']['item_admsub16']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['import']);
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("IMPORTFILE",$LANG['admintable']['importfile']);
$conttp->setVariable("OVERWRITE",$LANG['admintable']['overwrite']);
$conttp->setVariable("MAKE",$LANG['admintable']['import']);
$conttp->setVariable("ABORT",$LANG['admintable']['abort']);
$conttp->setVariable("MUST_DATA",$LANG['admintable']['mustdata']);
$conttp->setVariable("CTRL_INFO",$LANG['admintable']['ctrlinfo']);
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
// Dateien zusammensuchen
$strCommand = "ls -1 ".$SETS['nagios']['config']."*.cfg | grep -Ev 'cgi.cfg|nagios.cfg|nrpe.cfg|nsca.cfg';".
              "ls -1 ".$SETS['nagios']['confighosts']."*.cfg;".
			  "ls -1 ".$SETS['nagios']['configservices']."*.cfg;".
			  "ls -1 ".$SETS['nagios']['backup']."*.cfg_* | grep -Ev 'cgi.cfg|nagios.cfg|nrpe.cfg|nsca.cfg';".
              "ls -1 ".$SETS['nagios']['backuphosts']."*.cfg_*;".
			  "ls -1 ".$SETS['nagios']['backupservices']."*.cfg_*;";
$resList = popen($strCommand,"r");
while (!feof($resList)) {
	$strFile = fgets($resList,200);
	if ($strFile != "") {
		$conttp->setVariable("DAT_IMPORTFILE",$strFile);
		$conttp->parse("filelist");
	}
}
pclose($resList);
if ($intModus == 1) $conttp->setVariable("SUCCESS",$myVisClass->strMessage);
$conttp->parse("main");
$conttp->show("main");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>