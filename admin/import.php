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
// Zweck:	Datenimport
// Datei:	admin/import.php
// Version: 2.00.00 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
//error_reporting(E_ALL);
// 
// Menuvariabeln für diese Seite
// =============================
$intMain 		= 6;
$intSub  		= 16;
$intMenu 		= 2;
$preContent 	= "import.tpl.htm";
$intModus		= 0;
//
// Vorgabedatei einbinden
// ======================
$preAccess	= 1;
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
require($SETS['path']['physical']."functions/import_class.php");
//
// Klassen initialisieren
// ======================
$myImportClass = new nagimport;
$myImportClass->myDataClass	=& $myDataClass;
$myImportClass->myDBClass	=& $myDBClass;
$myImportClass->arrLanguage = $LANG;
//
// Übergabeparameter
// =================
$chkSelFilename	= isset($_POST['selImportFile'])	? $_POST['selImportFile']	: array("");
$chkSelTemplate	= isset($_POST['selTemplateFile'])	? $_POST['selTemplateFile']	: "";
$chkOverwrite	= isset($_POST['chbOverwrite'])		? $_POST['chbOverwrite']	: 0;
//
// Formulareingaben verarbeiten
// ============================
if ($chkSelFilename[0] != "") {
	$myVisClass->strMessage = "";
	foreach($chkSelFilename AS $elem) {
		$intModus  = 1;
		$intReturn = $myImportClass->fileImport($elem,$chkSelTemplate,$chkOverwrite);
		$myDataClass->writeLog($LANG['logbook']['import']." ".$elem." [".$chkOverwrite."]");
		if ($intReturn == 1) $myImportClass->strMessage .= $myVisClass->strDBMessage;
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
$conttp->setVariable("TEMPLATE",$LANG['admintable']['template']);
$conttp->setVariable("IMPORTFILE",$LANG['admintable']['importfile']);
$conttp->setVariable("OVERWRITE",$LANG['admintable']['overwrite']);
$conttp->setVariable("MAKE",$LANG['admintable']['import']);
$conttp->setVariable("ABORT",$LANG['admintable']['abort']);
$conttp->setVariable("MUST_DATA",$LANG['admintable']['mustdata']);
$conttp->setVariable("CTRL_INFO",$LANG['admintable']['ctrlinfo']);
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
$conttp->setVariable("DAT_IMPORTFILE_1","");
$conttp->parse("filelist1");
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
		$conttp->setVariable("DAT_IMPORTFILE_1",$strFile);
		$conttp->parse("filelist1");
		$conttp->setVariable("DAT_IMPORTFILE_2",$strFile);
		$conttp->parse("filelist2");
	}
}
pclose($resList);
if ($intModus == 1) $conttp->setVariable("SUCCESS",$myImportClass->strMessage);
$conttp->parse("main");
$conttp->show("main");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>