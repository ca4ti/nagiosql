<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2011 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Commands overview
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-03-13 14:00:26 +0100 (So, 13. Mär 2011) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.1.1
// Revision  : $LastChangedRevision: 1058 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$intMain 		= 4;
$intSub  		= 0;
$intMenu 		= 2;
$preContent 	= "admin/mainpages.tpl.htm";
//
// Include preprocessing file
// ==========================
require("../functions/prepend_adm.php");
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Include content
// ===============
$conttp->setVariable("TITLE",translate('Check commands'));
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("DESC",translate('To define check and misc commands, notification commands and special commands.'));
$conttp->setVariable("STATISTICS",translate('Statistical datas'));
$conttp->setVariable("TYPE",translate('Group'));
$conttp->setVariable("ACTIVE",translate('Active'));
$conttp->setVariable("INACTIVE",translate('Inactive'));
//
// Include statistical data
// ========================
$conttp->setVariable("NAME",translate('Check commands'));
$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_command WHERE active='1' AND config_id=$chkDomainId"));
$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_command WHERE active='0' AND config_id=$chkDomainId"));
$conttp->parse("statisticrow");
$conttp->parse("statistics");
$conttp->parse("main");
$conttp->show("main");
//
// Include Footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>