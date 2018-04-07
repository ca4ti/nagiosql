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
// Component : Alarming overview
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
$prePageId		= 3;
$preContent 	= "admin/mainpages.tpl.htm";
//
// Include preprocessing file
// ==========================
require("../functions/prepend_adm.php");
//
// Include content
// ===============
$conttp->setVariable("TITLE",translate('Alarming'));
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("DESC",translate('To define contact data, contact templates and contact groups and time periods.'));
$conttp->setVariable("STATISTICS",translate('Statistical datas'));
$conttp->setVariable("TYPE",translate('Group'));
$conttp->setVariable("ACTIVE",translate('Active'));
$conttp->setVariable("INACTIVE",translate('Inactive'));
//
// Include statistical data
// ========================
// Get read access groups
$strAccess = $myVisClass->getAccGroups('read');
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=14")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Contact data'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_contact WHERE active='1' AND config_id=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_contact WHERE active='0' AND config_id=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->parse("statisticrow");
}
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=15")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Contact groups'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_contactgroup WHERE active='1' AND config_id=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_contactgroup WHERE active='0' AND config_id=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->parse("statisticrow");
}
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=16")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Time periods'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_timeperiod WHERE active='1' AND config_id=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_timeperiod WHERE active='0' AND config_id=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->parse("statisticrow");
}
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=17")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Contact templates'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_contacttemplate WHERE active='1' AND config_id=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM tbl_contacttemplate WHERE active='0' AND config_id=$chkDomainId AND `access_group` IN ($strAccess)"));
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