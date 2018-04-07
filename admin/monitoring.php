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
// Datum:	26.02.2005
// Zweck:	Überwachungsdefinitionen
// Datei:	admin/monitoring.php
// Version:	1.02
//
///////////////////////////////////////////////////////////////////////////////
error_reporting(E_ALL);
// 
// Menuvariabeln für diese Seite
// =============================
$intMain 		= 2;
$intSub  		= 0;
$intMenu 		= 2;
$preContent 	= "monitoring.tpl.htm";
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
$conttp->setVariable("HOSTS",$LANG['menu']['item_admsub1']);
$conttp->setVariable("SERVICES",$LANG['menu']['item_admsub7']);
$conttp->setVariable("HOSTGROUPS",$LANG['menu']['item_admsub8']);
$conttp->setVariable("SERVICEGROUPS",$LANG['menu']['item_admsub9']);
// Statistische Daten zusammenstellen
$conttp->setVariable("ACT_HOST",$myDBClass->getFieldData("SELECT count(*) FROM tbl_host WHERE active='1'"));
$conttp->setVariable("INACT_HOST",$myDBClass->getFieldData("SELECT count(*) FROM tbl_host WHERE active='0'"));
$conttp->setVariable("ACT_SERV",$myDBClass->getFieldData("SELECT count(*) FROM tbl_service WHERE active='1'"));
$conttp->setVariable("INACT_SERV",$myDBClass->getFieldData("SELECT count(*) FROM tbl_service WHERE active='0'"));
$conttp->setVariable("ACT_HGROUP",$myDBClass->getFieldData("SELECT count(*) FROM tbl_hostgroup WHERE active='1'"));
$conttp->setVariable("INACT_HGROUP",$myDBClass->getFieldData("SELECT count(*) FROM tbl_hostgroup WHERE active='0'"));
$conttp->setVariable("ACT_SGROUP",$myDBClass->getFieldData("SELECT count(*) FROM tbl_servicegroup WHERE active='1'"));
$conttp->setVariable("INACT_SGROUP",$myDBClass->getFieldData("SELECT count(*) FROM tbl_servicegroup WHERE active='0'"));
$conttp->setVariable("CONSISTENCY",$LANG['admincontent']['consistency']);
$strHostsMessage = $myVisClass->checkConsistHosts();
$conttp->setVariable("CONSUSAGE_HOSTS",$strHostsMessage);
if ($strHostsMessage == $LANG['admincontent']['hostsok']) {
	$conttp->setVariable("HOST_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("HOST_MSGCLASS","dbmessage");
}
if ($myVisClass->strTempValue1 != "") $conttp->setVariable("HOST_FREEDATA",$myVisClass->strTempValue1);
$strServiceMessage = $myVisClass->checkConsistServices();
$conttp->setVariable("CONSUSAGE_SERVICES",$strServiceMessage);
if ($strServiceMessage == $LANG['admincontent']['servicesok']) {
	$conttp->setVariable("SERV_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("SERV_MSGCLASS","dbmessage");
}
$strHostGroupMessage = $myVisClass->checkConsistHostgroups();
$conttp->setVariable("CONSUSAGE_HOSTG",$strHostGroupMessage);
if ($strHostGroupMessage == $LANG['admincontent']['hostgroupsok']) {
	$conttp->setVariable("HOSTG_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("HOSTG_MSGCLASS","dbmessage");
}
$strServiceGroupMessage = $myVisClass->checkConsistServicegroups();
$conttp->setVariable("CONSUSAGE_SERVG",$strServiceGroupMessage);
if ($strServiceGroupMessage == $LANG['admincontent']['servicegroupsok']) {
	$conttp->setVariable("SERVG_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("SERVG_MSGCLASS","dbmessage");
}
$conttp->parse("main");
$conttp->show("main");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>