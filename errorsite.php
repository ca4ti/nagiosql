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
// Datum:	11.03.2005
// Zweck:	Fehlerseite
// Datei:	errorsite.php
// Version:	1.00
//
///////////////////////////////////////////////////////////////////////////////
error_reporting(E_ALL);
// 
// Menuvariabeln für diese Seite
// =============================
$intMain 		= 1;
$intSub  		= 0;
$intMenu 		= 1;
$preContent 	= "admin_master.tpl.htm";
$setFileVersion = "1.00";
//
// Vorgabedatei einbinden
// ======================
$preNoLogin = true;
$SETS 		= parse_ini_file("config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['user']['errorsite']);
$maintp->parse("header");
$maintp->show("header");
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['user']['errorsite']); 
$conttp->setVariable("LOGINSITE",$LANG['user']['loginsite']);
$conttp->setVariable("LOGINPATH",$SETS['path']['root']."index.php");
$conttp->setVariable("MESSAGE",$LANG['user']['norights']);
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->parse("errorsite");
$conttp->show("errorsite");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>