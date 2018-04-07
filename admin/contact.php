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
// Zweck:	Übersicht Kontakte
// Datei:	admin/contact.php
// Version:	1.02
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Menuvariabeln für diese Seite
// =============================
$intMain 		= 3;
$intSub  		= 0;
$intMenu 		= 2;
$preContent 	= "contact.tpl.htm";
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
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm3']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['contact']);
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("DESC",$LANG['admincontent']['contacttext']);
$conttp->setVariable("STATISTICS",$LANG['admincontent']['statistic']);
$conttp->setVariable("TYPE",$LANG['admincontent']['group']);
$conttp->setVariable("ACTIVE",$LANG['admintable']['active']);
$conttp->setVariable("INACTIVE",$LANG['admincontent']['inactive']);
$conttp->setVariable("CONTACTS",$LANG['menu']['item_adm3']);
$conttp->setVariable("CONTGROUPS",$LANG['menu']['item_admsub6']);
$conttp->setVariable("TIMEPERIODS",$LANG['menu']['item_admsub2']);
// Statistische Daten zusammenstellen
$conttp->setVariable("ACT_CONT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_contact WHERE active='1'"));
$conttp->setVariable("INACT_CONT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_contact WHERE active='0'"));
$conttp->setVariable("ACT_CGROUP",$myDBClass->getFieldData("SELECT count(*) FROM tbl_contactgroup WHERE active='1'"));
$conttp->setVariable("INACT_CGROUP",$myDBClass->getFieldData("SELECT count(*) FROM tbl_contactgroup WHERE active='0'"));
$conttp->setVariable("ACT_TIMEP",$myDBClass->getFieldData("SELECT count(*) FROM tbl_timeperiod WHERE active='1'"));
$conttp->setVariable("INACT_TIMEP",$myDBClass->getFieldData("SELECT count(*) FROM tbl_timeperiod WHERE active='0'"));
$conttp->setVariable("CONSISTENCY",$LANG['admincontent']['consistency']);
$strContMessage = $myVisClass->checkConsistContacts();
$conttp->setVariable("CONSUSAGE_CONTACTS",$strContMessage);
if ($strContMessage == $LANG['admincontent']['contactsok']) {
	$conttp->setVariable("CON_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("CON_MSGCLASS","dbmessage");
}
if ($myVisClass->strTempValue1 != "") $conttp->setVariable("CONT_FREEDATA",$myVisClass->strTempValue1);
$strContGroupMessage = $myVisClass->checkConsistContactgroups();
$conttp->setVariable("CONSUSAGE_CGROUPS",$strContGroupMessage);
if ($strContGroupMessage == $LANG['admincontent']['cgroupssok']) {
	$conttp->setVariable("CGROUP_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("CGROUP_MSGCLASS","dbmessage");
}
if ($myVisClass->strTempValue1 != "") $conttp->setVariable("CGROUP_FREEDATA",$myVisClass->strTempValue1);
$strTimeGroupMessage = $myVisClass->checkConsistTimeperiods();
$conttp->setVariable("CONSUSAGE_TIMEP",$strTimeGroupMessage);
if ($strTimeGroupMessage == $LANG['admincontent']['timeperiodsok']) {
	$conttp->setVariable("TIMEP_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("TIMEP_MSGCLASS","dbmessage");
}
if ($myVisClass->strTempValue1 != "") $conttp->setVariable("TIME_FREEDATA",$myVisClass->strTempValue1);
$conttp->parse("main");
$conttp->show("main");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>