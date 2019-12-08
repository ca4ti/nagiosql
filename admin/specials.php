<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2020 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Specials overview
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Path settings
// ===================
$strPattern = '(admin/[^/]*.php)';
$preRelPath  = preg_replace($strPattern, '', filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING));
$preBasePath = preg_replace($strPattern, '', filter_input(INPUT_SERVER, 'SCRIPT_FILENAME', FILTER_SANITIZE_STRING));
//
// Define common variables
// =======================
$prePageId  = 5;
$preContent = 'admin/mainpages.htm.tpl';
//
// Include preprocessing file
// ==========================
require $preBasePath.'functions/prepend_adm.php';
//
// Include content
// ===============
$conttp->setVariable('TITLE', translate('Misc commands'));
$conttp->parse('header');
$conttp->show('header');
$conttp->setVariable('DESC', translate('Define host and service dependencies, host and service escalations as well '.
    'as host and service additional data.'));
$conttp->setVariable('STATISTICS', translate('Statistical datas'));
$conttp->setVariable('TYPE', translate('Group'));
$conttp->setVariable('ACTIVE', translate('Active'));
$conttp->setVariable('INACTIVE', translate('Inactive'));
//
// Include statistical data
// ========================
// Get read access groups
$strAccess = $myVisClass->getAccessGroups('read');
$intAccessGrp19 = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=19');
$intAccessGrp20 = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=20');
$intAccessGrp21 = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=21');
$intAccessGrp22 = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=22');
$intAccessGrp23 = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=23');
$intAccessGrp24 = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=24');
if ($myVisClass->checkAccountGroup($intAccessGrp19, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Host dependencies'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_hostdependency` '
        . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_hostdependency` '
        . "WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->parse('statisticrow');
}
if ($myVisClass->checkAccountGroup($intAccessGrp20, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Host escalations'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_hostescalation` '
        . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_hostescalation` '
        . "WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->parse('statisticrow');
}
if ($myVisClass->checkAccountGroup($intAccessGrp21, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Host ext. info'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_hostextinfo` '
        . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_hostextinfo` '
        . "WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->parse('statisticrow');
}
if ($myVisClass->checkAccountGroup($intAccessGrp22, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Service dependencies'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_servicedependency` '
        . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_servicedependency` '
        . "WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->parse('statisticrow');
}
if ($myVisClass->checkAccountGroup($intAccessGrp23, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Service escalations'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_serviceescalation` '
        . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_serviceescalation` '
        . "WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->parse('statisticrow');
}
if ($myVisClass->checkAccountGroup($intAccessGrp24, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Service ext. info'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_serviceextinfo` '
        . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_serviceextinfo` '
        . "WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->parse('statisticrow');
}
$conttp->parse('statistics');
$conttp->parse('main');
$conttp->show('main');
//
// Include Footer
// ==============
$maintp->setVariable('VERSION_INFO', "<a href='https://sourceforge.net/projects/nagiosql/' "
    . "target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse('footer');
$maintp->show('footer');
