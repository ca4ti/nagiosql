<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2012 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Commands overview
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2012-02-08 15:13:31 +0100 (Wed, 08 Feb 2012) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1189 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$prePageId		= 4;
$preContent 	= "admin/mainpages.tpl.htm";
//
// Include preprocessing file
// ==========================
require("../functions/prepend_adm.php");
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
// Get read access groups
$strAccess = $myVisClass->getAccGroups('read');
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=18")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Check commands'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_command WHERE active='1' AND config_id=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_command WHERE active='0' AND config_id=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->parse("statisticrow");
}
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