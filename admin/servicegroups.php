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
// Component : Admin servicegroup definition
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
$prePageId        = 11;
$preContent       = 'admin/servicegroups.htm.tpl';
$preListTpl       = 'admin/datalist.htm.tpl';
$preSearchSession = 'servicegroup';
$preTableName     = 'tbl_servicegroup';
$preKeyField      = 'servicegroup_name';
$preAccess        = 1;
$preFieldvars     = 1;
//
// Include preprocessing file
// ==========================
require $preBasePath.'functions/prepend_adm.php';
require $preBasePath.'functions/prepend_content.php';
//
// Add or modify data
// ==================
if ((($chkModus == 'insert') || ($chkModus == 'modify')) && ($intGlobalWriteAccess == 0)) {
    $strSQLx = "`$preTableName` SET `$preKeyField`='$chkTfValue1', `alias`='$chkTfValue2', `members`=$intMselValue1, "
             . "`servicegroup_members`=$intMselValue2, `notes`='$chkTfValue3', `notes_url`='$chkTfValue4', "
             . "`action_url`='$chkTfValue5', $preSQLCommon1";
    if ($chkModus == 'insert') {
        $strSQL = 'INSERT INTO ' .$strSQLx;
    } else {
        $strSQL = 'UPDATE ' .$strSQLx. ' WHERE `id`=' .$chkDataId;
    }
    if ($intWriteAccessId == 0) {
        if (($chkTfValue1 != '') && ($chkTfValue2 != '') && (($intMselValue1 != 0) || ($intVersion >= 3))) {
            $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
            if ($chkModus == 'insert') {
                $chkDataId = $intInsertId;
            }
            if ($intReturn == 1) {
                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
            } else {
                $myVisClass->processMessage($myDataClass->strInfoMessage, $strInfoMessage);
                $myDataClass->updateStatusTable($preTableName);
                if ($chkModus  == 'insert') {
                    $myDataClass->writeLog(translate('New service group inserted:'). ' ' .$chkTfValue1);
                }
                if ($chkModus  == 'modify') {
                    $myDataClass->writeLog(translate('Service group modified:'). ' ' .$chkTfValue1);
                }
                //
                // Insert/update relations
                // =======================
                if ($chkModus == 'insert') {
                    if ($intMselValue1 != 0) {
                        $intRet1 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServicegroupToService',
                            $chkDataId,
                            $chkMselValue1,
                            1
                        );
                    }
                    if (isset($intRet1) && ($intRet1 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue2 != 0) {
                        $intRet2 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServicegroupToServicegroup',
                            $chkDataId,
                            $chkMselValue2
                        );
                    }
                    if (isset($intRet2) && ($intRet2 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                } elseif ($chkModus == 'modify') {
                    if ($intMselValue1 != 0) {
                        $intRet1 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServicegroupToService',
                            $chkDataId,
                            $chkMselValue1,
                            1
                        );
                    } else {
                        $intRet1 = $myDataClass->dataDeleteRelation('tbl_lnkServicegroupToService', $chkDataId);
                    }
                    if ($intRet1 != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue2 != 0) {
                        $intRet2 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServicegroupToServicegroup',
                            $chkDataId,
                            $chkMselValue2
                        );
                    } else {
                        $intRet2 = $myDataClass->dataDeleteRelation('tbl_lnkServicegroupToServicegroup', $chkDataId);
                    }
                    if ($intRet2 != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                }
                if (($intRet1 + $intRet2) != 0) {
                    $strInfoMessage = '';
                }
            }
        } else {
            $myVisClass->processMessage(
                translate('Database entry failed! Not all necessary data filled in!'),
                $strErrorMessage
            );
        }
    } else {
        $myVisClass->processMessage(translate('Database entry failed! No write access!'), $strErrorMessage);
    }
    $chkModus = 'display';
}
if ($chkModus != 'add') {
    $chkModus    = 'display';
}
//
// Get date/time of last database and config file manipulation
// ===========================================================
$intReturn = $myConfigClass->lastModifiedFile($preTableName, $arrTimeData, $strTimeInfoString);
if ($intReturn != 0) {
    $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
}
//
// Singe data form
// ===============
if ($chkModus == 'add') {
    $conttp->setVariable('TITLE', translate('Define service groups (servicegroups.cfg)'));
    // Do not show modified time list
    $intNoTime = 1;
    // Process service selection field
    if (isset($arrModifyData['members'])) {
        $intFieldId = $arrModifyData['members'];
    } else {
        $intFieldId = 0;
    }
    $intReturn1 = $myVisClass->parseSelectMulti(
        'tbl_service',
        'service_description',
        'service_members',
        'tbl_lnkServicegroupToService',
        0,
        $intFieldId
    );
    if ($intReturn1 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (($intReturn1 != 0) && ($intVersion < 3)) {
        $myVisClass->processMessage(translate('Attention, no services defined!'), $strDBWarning);
        $intDataWarning = 1;
    }
    // Process service group selection field
    if (isset($arrModifyData['servicegroup_members'])) {
        $intFieldId = $arrModifyData['servicegroup_members'];
    } else {
        $intFieldId = 0;
    }
    $intReturn2 = $myVisClass->parseSelectMulti(
        $preTableName,
        $preKeyField,
        'servicegroups',
        'tbl_lnkServicegroupToServicegroup',
        0,
        $intFieldId,
        $chkListId
    );
    if ($intReturn2 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Process access group selection field
    if (isset($arrModifyData['access_group'])) {
        $intFieldId = $arrModifyData['access_group'];
    } else {
        $intFieldId = 0;
    }
    $intReturn3 = $myVisClass->parseSelectSimple('tbl_group', 'groupname', 'acc_group', 0, $intFieldId);
    if ($intReturn3 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Initial add/modify form definitions
    $myContentClass->addFormInit($conttp);
    if ($intDataWarning == 1) {
        $conttp->setVariable('WARNING', $strDBWarning. '<br>' .translate('Saving not possible!'));
    }
    if ($intVersion < 3) {
        $conttp->setVariable('VERSION_20_VALUE_MUST', 'mselValue1,');
    }
    // Insert data from database in "modify" mode
    if (isset($arrModifyData) && ($chkSelModify == 'modify')) {
        // Check relation information to find out locked configuration datasets
        $intLocked = $myDataClass->infoRelation($preTableName, $arrModifyData['id'], $preKeyField);
        $myVisClass->processMessage($myDataClass->strInfoMessage, $strRelMessage);
        $strInfo  = '<br><span class="redmessage">' .translate('Entry cannot be activated because it is used by '
                  . 'another configuration'). ':</span>';
        $strInfo .= '<br><span class="greenmessage">' .$strRelMessage. '</span>';
        // Process data
        $myContentClass->addInsertData($conttp, $arrModifyData, $intLocked, $strInfo);
    }
    $conttp->parse('datainsert');
    $conttp->show('datainsert');
}
//
// List view
// ==========
if ($chkModus == 'display') {
    // Initial list view definitions
    $myContentClass->listViewInit($mastertp);
    $mastertp->setVariable('TITLE', translate('Define service groups (servicegroups.cfg)'));
    $mastertp->setVariable('FIELD_1', translate('Service group'));
    $mastertp->setVariable('FIELD_2', translate('Description'));
    // Process search string and filter
    $strSearchWhere = '';
    if ($_SESSION['search'][$preSearchSession] != '') {
        $strSearchTxt    = $_SESSION['search'][$preSearchSession];
        $strSearchWhere .= "AND (`$preKeyField` LIKE '%".$strSearchTxt."%' OR `alias` LIKE '%".$strSearchTxt."%' "
                        . "OR `notes` LIKE '%".$strSearchTxt."%') ";
    }
    if ($_SESSION['filter'][$preSearchSession]['registered'] != '') {
        $intRegistered = (int)$_SESSION['filter'][$preSearchSession]['registered'];
        if ($intRegistered == 1) {
            $strSearchWhere .= "AND `register` = '1' ";
        }
        if ($intRegistered == 2) {
            $strSearchWhere .= "AND `register` = '0' ";
        }
        $mastertp->setVariable('SEL_REGFILTER_'.$intRegistered.'_SELECTED', 'selected');
    }
    if ($_SESSION['filter'][$preSearchSession]['active'] != '') {
        $intActivated = (int)$_SESSION['filter'][$preSearchSession]['active'];
        if ($intActivated == 1) {
            $strSearchWhere .= "AND `active` = '1' ";
        }
        if ($intActivated == 2) {
            $strSearchWhere .= "AND `active` = '0' ";
        }
        $mastertp->setVariable('SEL_ACTIVEFILTER_'.$intActivated.'_SELECTED', 'selected');
    }
    // Row sorting
    $strOrderString = "ORDER BY `config_id`, `$preKeyField` $hidSortDir";
    if ($hidSortBy == 2) {
        $strOrderString = "ORDER BY `config_id`, `alias` $hidSortDir";
    }
    // Count datasets
    $strSQL1    = "SELECT count(*) AS `number` FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere "
                . "AND `access_group` IN ($strAccess)";
    $booReturn1 = $myDBClass->hasSingleDataset($strSQL1, $arrDataLinesCount);
    if ($booReturn1 == false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    } else {
        $intLineCount = (int)$arrDataLinesCount['number'];
        if ($intLineCount < $chkLimit) {
            $chkLimit = 0;
        }
    }
    // Get datasets
    $strSQL2    = "SELECT `id`, `$preKeyField`, `alias`, `register`, `active`, `config_id`, `access_group` "
                . "FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess) "
                . "$strOrderString LIMIT $chkLimit,".$SETS['common']['pagelines'];
    $booReturn2 = $myDBClass->hasDataArray($strSQL2, $arrDataLines, $intDataCount);
    if ($booReturn2 == false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    }
    // Process data
    $myContentClass->listData($mastertp, $arrDataLines, $intDataCount, $intLineCount, $preKeyField, 'alias');
}
// Show messages
$myContentClass->showMessages(
    $mastertp,
    $strErrorMessage,
    $strInfoMessage,
    $strConsistMessage,
    $arrTimeData,
    $strTimeInfoString,
    $intNoTime
);
//
// Process footer
// ==============
$myContentClass->showFooter($maintp, $setFileVersion);
