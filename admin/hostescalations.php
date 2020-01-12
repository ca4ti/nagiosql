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
// Component : Host escalation definition
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
$prePageId        = 20;
$preContent       = 'admin/hostescalations.htm.tpl';
$preListTpl       = 'admin/datalist.htm.tpl';
$preSearchSession = 'hostescalation';
$preTableName     = 'tbl_hostescalation';
$preKeyField      = 'config_name';
$preAccess        = 1;
$preFieldvars     = 1;
//
// Include preprocessing files
// ===========================
require $preBasePath.'functions/prepend_adm.php';
require $preBasePath.'functions/prepend_content.php';
//
// Data processing
// ===============
$strEO = substr($chkChbGr1a.$chkChbGr1b.$chkChbGr1c, 0, -1);
//
// Add or modify data
// ==================
if ((($chkModus == 'insert') || ($chkModus == 'modify')) && ($intGlobalWriteAccess == 0)) {
    $strSQLx = "`$preTableName` SET `$preKeyField`='$chkTfValue1', `host_name`=$intMselValue3, "
        . "`hostgroup_name`=$intMselValue4, `contacts`=$intMselValue1, `contact_groups`=$intMselValue2, "
        . "`first_notification`=$chkTfNullVal1, `last_notification`=$chkTfNullVal2, "
        . "`notification_interval`=$chkTfNullVal3, `escalation_period`='$chkSelValue1', "
        . "`escalation_options`='$strEO', $preSQLCommon1";
    if ($chkModus == 'insert') {
        $strSQL = 'INSERT INTO ' .$strSQLx;
    } else {
        $strSQL = 'UPDATE ' .$strSQLx. ' WHERE `id`=' .$chkDataId;
    }
    if ($intWriteAccessId == 0) {
        if ((($intMselValue3 != 0) || ($chkMselValue4 != 0)) && (($intMselValue1 != 0) || ($intMselValue2 != 0)) &&
            ($chkTfNullVal1 != 'NULL') && ($chkTfNullVal2 != 'NULL') && ($chkTfNullVal3 != 'NULL')) {
            $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
            if ($chkModus == 'insert') {
                $chkDataId = $intInsertId;
            }
            if ($intReturn == 1) {
                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
            } else {
                $myVisClass->processMessage($myDataClass->strInfoMessage, $strInfoMessage);
                $myDataClass->updateStatusTable($preTableName);
                if ($chkModus == 'insert') {
                    $myDataClass->writeLog(translate('New host escalation inserted:'). ' ' .$chkTfValue1);
                }
                if ($chkModus == 'modify') {
                    $myDataClass->writeLog(translate('Host escalation modified:'). ' ' .$chkTfValue1);
                }
                //
                // Insert/update relations
                // =======================
                if ($chkModus == 'insert') {
                    if ($intMselValue1 != 0) {
                        $intRet1 = $myDataClass->dataInsertRelation(
                            'tbl_lnkHostescalationToContact',
                            $chkDataId,
                            $chkMselValue1
                        );
                    }
                    if (isset($intRet1) && ($intRet1 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue2 != 0) {
                        $intRet2 = $myDataClass->dataInsertRelation(
                            'tbl_lnkHostescalationToContactgroup',
                            $chkDataId,
                            $chkMselValue2
                        );
                    }
                    if (isset($intRet2) && ($intRet2 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue3 != 0) {
                        $intRet3 = $myDataClass->dataInsertRelation(
                            'tbl_lnkHostescalationToHost',
                            $chkDataId,
                            $chkMselValue3
                        );
                    }
                    if (isset($intRet3) && ($intRet3 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue4 != 0) {
                        $intRet4 = $myDataClass->dataInsertRelation(
                            'tbl_lnkHostescalationToHostgroup',
                            $chkDataId,
                            $chkMselValue4
                        );
                    }
                    if (isset($intRet4) && ($intRet4 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                } elseif ($chkModus == 'modify') {
                    if ($intMselValue1 != 0) {
                        $intRet1 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkHostescalationToContact',
                            $chkDataId,
                            $chkMselValue1
                        );
                    } else {
                        $intRet1 = $myDataClass->dataDeleteRelation('tbl_lnkHostescalationToContact', $chkDataId);
                    }
                    if ($intRet1 != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue2 != 0) {
                        $intRet2 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkHostescalationToContactgroup',
                            $chkDataId,
                            $chkMselValue2
                        );
                    } else {
                        $intRet2 = $myDataClass->dataDeleteRelation('tbl_lnkHostescalationToContactgroup', $chkDataId);
                    }
                    if ($intRet2 != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue3 != 0) {
                        $intRet3 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkHostescalationToHost',
                            $chkDataId,
                            $chkMselValue3
                        );
                    } else {
                        $intRet3 = $myDataClass->dataDeleteRelation('tbl_lnkHostescalationToHost', $chkDataId);
                    }
                    if ($intRet3 != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue4 != 0) {
                        $intRet4 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkHostescalationToHostgroup',
                            $chkDataId,
                            $chkMselValue4
                        );
                    } else {
                        $intRet4 = $myDataClass->dataDeleteRelation('tbl_lnkHostescalationToHostgroup', $chkDataId);
                    }
                    if ($intRet4 != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                }
                if (($intRet1 + $intRet2 + $intRet3 + $intRet4) != 0) {
                    $strInfoMessage = '';
                }
                //
                // Update Import HASH
                // ==================
                $booReturn = $myDataClass->updateHash($preTableName, $chkDataId);
                if ($booReturn != 0) {
                    $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
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
    $conttp->setVariable('TITLE', translate('Define host escalation (hostescalations.cfg)'));
    // Do not show modified time list
    $intNoTime = 1;
    // Process host and host group selection field
    if (isset($arrModifyData['host_name'])) {
        $intFieldId = $arrModifyData['host_name'];
    } else {
        $intFieldId = 0;
    }
    $intReturn1 = $myVisClass->parseSelectMulti(
        'tbl_host',
        'host_name',
        'host',
        'tbl_lnkHostescalationToHost',
        2,
        $intFieldId
    );
    if ($intReturn1 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (isset($arrModifyData['hostgroup_name'])) {
        $intFieldId = $arrModifyData['hostgroup_name'];
    } else {
        $intFieldId = 0;
    }
    $intReturn2 = $myVisClass->parseSelectMulti(
        'tbl_hostgroup',
        'hostgroup_name',
        'hostgroup',
        'tbl_lnkHostescalationToHostgroup',
        2,
        $intFieldId
    );
    if ($intReturn2 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (($intReturn1 != 0) && ($intReturn2 != 0)) {
        $myVisClass->processMessage(translate('Attention, no hosts and hostgroups defined!'), $strDBWarning);
        $intDataWarning = 1;
    }
    // Process timeperiod selection field
    if (isset($arrModifyData['escalation_period'])) {
        $intFieldId = $arrModifyData['escalation_period'];
    } else {
        $intFieldId = 0;
    }
    $intReturn = $myVisClass->parseSelectSimple('tbl_timeperiod', 'timeperiod_name', 'escperiod', 1, $intFieldId);
    if ($intReturn != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Process contact and contact group selection field
    if (isset($arrModifyData['contacts'])) {
        $intFieldId = $arrModifyData['contacts'];
    } else {
        $intFieldId = 0;
    }
    $intReturn1 = $myVisClass->parseSelectMulti(
        'tbl_contact',
        'contact_name',
        'contact',
        'tbl_lnkHostescalationToContact',
        2,
        $intFieldId
    );
    if ($intReturn1 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (isset($arrModifyData['contact_groups'])) {
        $intFieldId = $arrModifyData['contact_groups'];
    } else {
        $intFieldId = 0;
    }
    $intReturn2 = $myVisClass->parseSelectMulti(
        'tbl_contactgroup',
        'contactgroup_name',
        'contactgroup',
        'tbl_lnkHostescalationToContactgroup',
        2,
        $intFieldId
    );
    if ($intReturn2 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (($intReturn1 != 0) && ($intReturn2 != 0)) {
        $myVisClass->processMessage(translate('Attention, no contacts and contactgroups defined!'), $strDBWarning);
        $intDataWarning = 1;
    }
    // Process access group selection field
    if (isset($arrModifyData['access_group'])) {
        $intFieldId = $arrModifyData['access_group'];
    } else {
        $intFieldId = 0;
    }
    $intReturn = $myVisClass->parseSelectSimple('tbl_group', 'groupname', 'acc_group', 0, $intFieldId);
    if ($intReturn != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Initial add/modify form definitions
    $myContentClass->addFormInit($conttp);
    if ($intDataWarning == 1) {
        $conttp->setVariable('WARNING', $strDBWarning. '<br>' .translate('Saving not possible!'));
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
        foreach (explode(',', $arrModifyData['escalation_options']) as $elem) {
            $conttp->setVariable('DAT_EO' .strtoupper($elem). '_CHECKED', 'checked');
        }
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
    $mastertp->setVariable('TITLE', translate('Define host escalation (hostescalations.cfg)'));
    $mastertp->setVariable('FIELD_1', translate('Config name'));
    $mastertp->setVariable('FIELD_2', translate('Hosts'). ' / ' .translate('Host groups'));
    $mastertp->setVariable('FILTER_VISIBLE', 'visibility: hidden');
    // Process search string
    if ($_SESSION['search'][$preSearchSession] != '') {
        $strSearchTxt   = $_SESSION['search'][$preSearchSession];
        $strSearchWhere = "AND (`$preKeyField` LIKE '%".$strSearchTxt."%')";
    }
    // Row sorting
    $strOrderString = "ORDER BY `config_id`, `$preKeyField` $hidSortDir";
    if ($hidSortBy == 2) {
        $strOrderString = "ORDER BY `config_id`, `$preKeyField` $hidSortDir";
    }
    $mastertp->setVariable('DISABLE_SORT_2', 'disable');
    // Count datasets
    $strSQL    = "SELECT count(*) AS `number` FROM `$preTableName` "
        . "WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
    $booReturn = $myDBClass->hasSingleDataset($strSQL, $arrDataLinesCount);
    if ($booReturn == false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    } else {
        $intLineCount = (int)$arrDataLinesCount['number'];
        if ($intLineCount < $chkLimit) {
            $chkLimit = 0;
        }
    }
    // Get datasets
    $strSQL    = "SELECT `id`, `$preKeyField`, `host_name`, `hostgroup_name`, `register`, `active`, `config_id`, "
        . "`access_group`  FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere AND `access_group` "
        . "IN ($strAccess) $strOrderString LIMIT $chkLimit,".$SETS['common']['pagelines'];
    $booReturn = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
    if ($booReturn == false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    }
    // Process data
    $myContentClass->listData(
        $mastertp,
        $arrDataLines,
        $intDataCount,
        $intLineCount,
        $preKeyField,
        'process_field',
        40
    );
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
