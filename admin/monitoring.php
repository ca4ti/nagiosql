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
// Component : Admin specials overview
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
$prePageId  = 2;
$preContent = 'admin/mainpages.htm.tpl';
//
// Include preprocessing file
// ==========================
require $preBasePath.'functions/prepend_adm.php';
require $preBasePath.'functions/prepend_content.php';
//
// Include content
// ===============
$conttp->setVariable('TITLE', translate('Monitoring'));
$conttp->parse('header');
$conttp->show('header');
$conttp->setVariable('DESC', translate('Define host and service supervisions as well as host and service groups.'));
$conttp->setVariable('STATISTICS', translate('Statistical datas'));
$conttp->setVariable('TYPE', translate('Group'));
$conttp->setVariable('ACTIVE', translate('Active'));
$conttp->setVariable('INACTIVE', translate('Inactive'));
//
// Include statistical data
// ========================
// Get read access groups
$strAccess      = $myVisClass->getAccessGroups('read');
$intAccessGrp8  = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=8');
$intAccessGrp9  = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=9');
$intAccessGrp10 = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=10');
$intAccessGrp11 = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=11');
$intAccessGrp12 = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=12');
$intAccessGrp13 = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=13');
if ($myVisClass->checkAccountGroup($intAccessGrp8, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Hosts'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_host` '
            . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_host` '
            . "WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->parse('statisticrow');
}
if ($myVisClass->checkAccountGroup($intAccessGrp9, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Services'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_service` '
            . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_service` '
            . "WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->parse('statisticrow');
}
if ($myVisClass->checkAccountGroup($intAccessGrp10, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Host groups'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_hostgroup` '
            . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_hostgroup` '
            . "WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->parse('statisticrow');
}
if ($myVisClass->checkAccountGroup($intAccessGrp11, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Service groups'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_servicegroup` '
            . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_servicegroup` '
            . "WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->parse('statisticrow');
}
if ($myVisClass->checkAccountGroup($intAccessGrp12, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Host templates'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_hosttemplate` '
            . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_hosttemplate` '
            . "WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->parse('statisticrow');
}
if ($myVisClass->checkAccountGroup($intAccessGrp13, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Service templates'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_servicetemplate` '
            . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_servicetemplate` '
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
