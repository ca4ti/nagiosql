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
// Component : Specials overview
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
$prePageId		= 5;
$preContent 	= "admin/mainpages.htm.tpl";
//
// Include preprocessing file
// ==========================
require("../functions/prepend_adm.php");
//
// Include content
// ===============
$conttp->setVariable("TITLE",translate('Misc commands'));
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("DESC",translate('To define host and service dependencies, host and service escalations as well as host and service additional data.'));
$conttp->setVariable("STATISTICS",translate('Statistical datas'));
$conttp->setVariable("TYPE",translate('Group'));
$conttp->setVariable("ACTIVE",translate('Active'));
$conttp->setVariable("INACTIVE",translate('Inactive'));
//
// Include statistical data
// ========================
// Get read access groups
$strAccess = $myVisClass->getAccGroups('read');
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=19")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Host dependencies'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_hostdependency` WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_hostdependency` WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->parse("statisticrow");
}
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=20")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Host escalations'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_hostescalation` WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_hostescalation` WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->parse("statisticrow");
}
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=21")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Host ext. info'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_hostextinfo` WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_hostextinfo` WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->parse("statisticrow");
}
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=22")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Service dependencies'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_servicedependency` WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_servicedependency` WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->parse("statisticrow");
}
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=23")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Service escalations'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_serviceescalation` WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_serviceescalation` WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->parse("statisticrow");
}
if ($myVisClass->checkAccGroup($myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=24")+0,'read') == 0) {
	$conttp->setVariable("NAME",translate('Service ext. info'));
	$conttp->setVariable("ACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_serviceextinfo` WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
	$conttp->setVariable("INACT_COUNT",$myDBClass->getFieldData("SELECT count(*) FROM `tbl_serviceextinfo` WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
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