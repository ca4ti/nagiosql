<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project   : NagiosQL
// Component : Admin main site
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2010-10-25 15:45:55 +0200 (Mo, 25 Okt 2010) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.0.4
// Revision  : $LastChangedRevision: 827 $
//
///////////////////////////////////////////////////////////////////////////////
//error_reporting(E_ALL);
error_reporting(E_ERROR);
//
// Menuvariabeln für diese Seite
// =============================
$intMain      = 1;
$intSub       = 0;
$intMenu      = 2;
$preContent   = "admin/mainpages.tpl.htm";
$prePosition  = "admin";
//
// Vorgabedatei einbinden
// ======================
require("functions/prepend_adm.php");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",gettext('NagiosQL Administration'));
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("DESC",gettext('Admin description'));
$conttp->parse("main");
$conttp->show("main");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>