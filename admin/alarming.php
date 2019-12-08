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
// Component : Alarming overview
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
$prePageId  = 3;
$preContent = 'admin/mainpages.htm.tpl';
//
// Include preprocessing file
// ==========================
require $preBasePath.'functions/prepend_adm.php';
//
// Include content
// ===============
$conttp->setVariable('TITLE', translate('Alarming'));
$conttp->setVariable('DESC', translate('To define contact data, contact templates and contact groups and time '
        . 'periods.'));
$conttp->setVariable('STATISTICS', translate('Statistical datas'));
$conttp->setVariable('TYPE', translate('Group'));
$conttp->setVariable('ACTIVE', translate('Active'));
$conttp->setVariable('INACTIVE', translate('Inactive'));
//
// Include statistical data
// ========================
// Get read access groups
$strAccess    = $myVisClass->getAccessGroups('read');
$intGroupId14 = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=14');
$intGroupId15 = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=15');
$intGroupId16 = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=16');
$intGroupId17 = (int)$myDBClass->getFieldData('SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=17');
if ($myVisClass->checkAccountGroup($intGroupId14, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Contact data'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_contact` '
            . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_contact` '
            . "WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->parse('statisticrow');
}
if ($myVisClass->checkAccountGroup($intGroupId15, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Contact groups'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_contactgroup` '
            . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_contactgroup` '
            . "WHERE active='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->parse('statisticrow');
}
if ($myVisClass->checkAccountGroup($intGroupId16, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Time periods'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_timeperiod` '
            . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_timeperiod` '
            . "WHERE `active`='0' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->parse('statisticrow');
}
if ($myVisClass->checkAccountGroup($intGroupId17, 'read') == 0) {
    $conttp->setVariable('NAME', translate('Contact templates'));
    $conttp->setVariable('ACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_contacttemplate` '
            . "WHERE `active`='1' AND `config_id`=$chkDomainId AND `access_group` IN ($strAccess)"));
    $conttp->setVariable('INACT_COUNT', $myDBClass->getFieldData('SELECT count(*) FROM `tbl_contacttemplate` '
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
