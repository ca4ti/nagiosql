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
// Zweck:	Übersicht Spezialitäten
// Datei:	admin/specials.php
// Version: 1.02
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Menuvariabeln für diese Seite
// =============================
$intMain 		= 5;
$intSub  		= 0;
$intMenu 		= 2;
$preContent 	= "special.tpl.htm";
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
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm5']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['special']);
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("DESC",$LANG['admincontent']['specialtext']);
$conttp->setVariable("STATISTICS",$LANG['admincontent']['statistic']);
$conttp->setVariable("TYPE",$LANG['admincontent']['group']);
$conttp->setVariable("ACTIVE",$LANG['admintable']['active']);
$conttp->setVariable("INACTIVE",$LANG['admincontent']['inactive']);
$conttp->setVariable("HOST_DEP",$LANG['menu']['info12']);
$conttp->setVariable("HOST_ESC",$LANG['menu']['info13']);
$conttp->setVariable("HOST_ADDON",$LANG['menu']['item_admsub14']);
$conttp->setVariable("SERV_DEP",$LANG['menu']['info10']);
$conttp->setVariable("SERV_ESC",$LANG['menu']['info11']);
$conttp->setVariable("SERV_ADDON",$LANG['menu']['item_admsub15']);
// Statistische Daten zusammenstellen
$conttp->setVariable("ACT_HD",$myDBClass->getFieldData("SELECT count(*) FROM tbl_hostdependency WHERE active='1'"));
$conttp->setVariable("INACT_HD",$myDBClass->getFieldData("SELECT count(*) FROM tbl_hostdependency WHERE active='0'"));
$conttp->setVariable("ACT_HE",$myDBClass->getFieldData("SELECT count(*) FROM tbl_hostescalation WHERE active='1'"));
$conttp->setVariable("INACT_HE",$myDBClass->getFieldData("SELECT count(*) FROM tbl_hostescalation WHERE active='0'"));
$conttp->setVariable("ACT_HA",$myDBClass->getFieldData("SELECT count(*) FROM tbl_hostextinfo WHERE active='1'"));
$conttp->setVariable("INACT_HA",$myDBClass->getFieldData("SELECT count(*) FROM tbl_hostextinfo WHERE active='0'"));
$conttp->setVariable("ACT_SD",$myDBClass->getFieldData("SELECT count(*) FROM tbl_servicedependency WHERE active='1'"));
$conttp->setVariable("INACT_SD",$myDBClass->getFieldData("SELECT count(*) FROM tbl_servicedependency WHERE active='0'"));
$conttp->setVariable("ACT_SE",$myDBClass->getFieldData("SELECT count(*) FROM tbl_serviceescalation WHERE active='1'"));
$conttp->setVariable("INACT_SE",$myDBClass->getFieldData("SELECT count(*) FROM tbl_serviceescalation WHERE active='0'"));
$conttp->setVariable("ACT_SA",$myDBClass->getFieldData("SELECT count(*) FROM tbl_serviceextinfo WHERE active='1'"));
$conttp->setVariable("INACT_SA",$myDBClass->getFieldData("SELECT count(*) FROM tbl_serviceextinfo WHERE active='0'"));
$conttp->parse("main");
$conttp->show("main");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>