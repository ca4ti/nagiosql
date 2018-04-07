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
// Datum:	29.03.2005
// Zweck:	Übersicht Kommandos
// Datei:	admin/command.php
// Version:	1.02
//
///////////////////////////////////////////////////////////////////////////////
//error_reporting(E_ALL);
// 
// Menuvariabeln für diese Seite
// =============================
$intMain 		= 4;
$intSub  		= 0;
$intMenu 		= 2;
$preContent 	= "command.tpl.htm";
$setFileVersion = "1.02";
//
// Vorgabedatei einbinden
// ======================
$preRights 	= "admin1";
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm4']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['command']);
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("DESC",$LANG['admincontent']['commandtext']);
$conttp->setVariable("STATISTICS",$LANG['admincontent']['statistic']);
$conttp->setVariable("TYPE",$LANG['admincontent']['group']);
$conttp->setVariable("ACTIVE",$LANG['admintable']['active']);
$conttp->setVariable("INACTIVE",$LANG['admincontent']['inactive']);
$conttp->setVariable("CHECK_COMMANDS",$LANG['menu']['item_admsub4']);
$conttp->setVariable("MISC_COMMANDS",$LANG['menu']['item_admsub3']);
// Statistische Daten zusammenstellen
$conttp->setVariable("ACT_CHECK",$myDBClass->getFieldData("SELECT count(*) FROM tbl_checkcommand WHERE active='1'"));
$conttp->setVariable("INACT_CHECK",$myDBClass->getFieldData("SELECT count(*) FROM tbl_checkcommand WHERE active='0'"));
$conttp->setVariable("ACT_MISC",$myDBClass->getFieldData("SELECT count(*) FROM tbl_misccommand WHERE active='1'"));
$conttp->setVariable("INACT_MISC",$myDBClass->getFieldData("SELECT count(*) FROM tbl_misccommand WHERE active='0'"));
$conttp->setVariable("CONSISTENCY",$LANG['admincontent']['consistency']);
$strCheckMessage = $myVisClass->checkConsistCheckcommands();
$conttp->setVariable("CONSUSAGE_CHECK",$strCheckMessage);
if ($strCheckMessage == $LANG['admincontent']['checkcommandsok']) {
	$conttp->setVariable("CHECK_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("CHECK_MSGCLASS","dbmessage");
}
if ($myVisClass->strTempValue1 != "") $conttp->setVariable("CHECK_FREEDATA",$myVisClass->strTempValue1);
$strMiscMessage = $myVisClass->checkConsistMisccommands();
$conttp->setVariable("CONSUSAGE_MISC",$strMiscMessage);
if ($strMiscMessage == $LANG['admincontent']['misccommandsok']) {
	$conttp->setVariable("MISC_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("MISC_MSGCLASS","dbmessage");
}
if ($myVisClass->strTempValue1 != "") $conttp->setVariable("MISC_FREEDATA",$myVisClass->strTempValue1);
$conttp->parse("main");
$conttp->show("main");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>