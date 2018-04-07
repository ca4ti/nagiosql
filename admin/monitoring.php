<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2017 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Admin specials overview
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2017-06-22 09:29:35 +0200 (Thu, 22 Jun 2017) $
// Author    : $LastChangedBy: martin $
// Version   : 3.3.0
// Revision  : $LastChangedRevision: 2 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$prePageId		= 2;
$preContent 	= "admin/mainpages.tpl.htm";
//
// Include preprocessing file
// ==========================
require("../functions/prepend_adm.php");
require("../functions/prepend_content.php");
//
// Include content
// ===============
$conttp->setVariable("TITLE",translate('Monitoring'));
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("DESC",translate('To define host and service supervisions as well as host and service groups.'));
$conttp->setVariable("STATISTICS",translate('Statistical datas'));
$conttp->setVariable("TYPE",translate('Group'));
$conttp->setVariable("ACTIVE",translate('Active'));
$conttp->setVariable("INACTIVE",translate('Inactive'));
//
// Include statistical data
// ========================
// Get read access groups
$strAccess = $myVisClass->getAccGroups('read');
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=8")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Hosts'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_host` WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_host` WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->parse("statisticrow");
}
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=9")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Services'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_service` WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_service` WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->parse("statisticrow");
}
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=10")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Host groups'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_hostgroup` WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_hostgroup` WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->parse("statisticrow");
}
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=11")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Service groups'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_servicegroup` WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_servicegroup` WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->parse("statisticrow");
}
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=12")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Host templates'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_hosttemplate` WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_hosttemplate` WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->parse("statisticrow");
}
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=13")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Service templates'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_servicetemplate` WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_servicetemplate` WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
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