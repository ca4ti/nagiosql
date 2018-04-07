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
// Zweck:	Überwachungsdefinitionen
// Datei:	admin/monitoring.php
// Version: 2.0.2 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
//error_reporting(E_ALL);
// 
// Menuvariabeln für diese Seite
// =============================
$intMain 		= 2;
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
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm2']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['monitor']);
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("DESC",$LANG['admincontent']['monitortext']);
$conttp->setVariable("STATISTICS",$LANG['admincontent']['statistic']);
$conttp->setVariable("TYPE",$LANG['admincontent']['group']);
$conttp->setVariable("ACTIVE",$LANG['admintable']['active']);
$conttp->setVariable("INACTIVE",$LANG['admincontent']['inactive']);
//
// Statistische Daten zusammenstellen
// ==================================
$conttp->setVariable("NAME",$LANG['menu']['item_admsub1']);
$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_host WHERE active='1'"));
$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_host WHERE active='0'"));
$conttp->parse("statisticrow");
$conttp->setVariable("NAME",$LANG['menu']['item_admsub7']);
$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_service WHERE active='1'"));
$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_service WHERE active='0'"));
$conttp->parse("statisticrow");
$conttp->setVariable("NAME",$LANG['menu']['item_admsub8']);
$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_hostgroup WHERE active='1'"));
$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_hostgroup WHERE active='0'"));
$conttp->parse("statisticrow");
$conttp->setVariable("NAME",$LANG['menu']['item_admsub9']);
$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_servicegroup WHERE active='1'"));
$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_servicegroup WHERE active='0'"));
$conttp->parse("statisticrow");
$conttp->parse("statistics");
$conttp->parse("main");
$conttp->show("main");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org'>NagiosQL</a> - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>