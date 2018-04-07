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
// Zweck:	Übersicht Befehlsdaten
// Datei:	admin/commands.php
// Version: 2.00.00 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Menuvariabeln für diese Seite
// =============================
$intMain 		= 4;
$intSub  		= 0;
$intMenu 		= 2;
$preContent 	= "mainpages.tpl.htm";
//
// Vorgabedatei einbinden
// ======================
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
//
// Statistische Daten zusammenstellen
// ==================================
$conttp->setVariable("NAME",$LANG['menu']['item_admsub4']);
$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_checkcommand WHERE active='1'"));
$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_checkcommand WHERE active='0'"));
$conttp->parse("statisticrow");
$conttp->setVariable("NAME",$LANG['menu']['item_admsub3']);
$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_misccommand WHERE active='1'"));
$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_misccommand WHERE active='0'"));
$conttp->parse("statisticrow");
$conttp->parse("statistics");
$conttp->parse("main");
$conttp->show("main");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>