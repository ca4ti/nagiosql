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
// Component : Service escalation definition
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
$prePageId        = 23;
$preContent       = 'admin/serviceescalations.htm.tpl';
$preListTpl       = 'admin/datalist.htm.tpl';
$preSearchSession = 'serviceescalation';
$preTableName     = 'tbl_serviceescalation';
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
$strEO = substr($chkChbGr1a.$chkChbGr1b.$chkChbGr1c.$chkChbGr1d, 0, -1);
//
// Add or modify data
// ==================
if ((($chkModus == 'insert') || ($chkModus == 'modify')) && ($intGlobalWriteAccess == 0)) {
    $strSQLx = "`$preTableName` SET `$preKeyField`='$chkTfValue1', `host_name`=$intMselValue1, "
        . "`service_description`=$intMselValue3, `hostgroup_name`=$intMselValue2, `contacts`=$intMselValue4, "
        . "`contact_groups`=$intMselValue5, `servicegroup_name`=$intMselValue6, `first_notification`=$chkTfNullVal1, "
        . "`last_notification`=$chkTfNullVal2, `notification_interval`=$chkTfNullVal3, "
        . "`escalation_period`='$chkSelValue1', `escalation_options`='$strEO', $preSQLCommon1";
    if ($chkModus == 'insert') {
        $strSQL = 'INSERT INTO ' .$strSQLx;
    } else {
        $strSQL = 'UPDATE ' .$strSQLx. ' WHERE `id`=' .$chkDataId;
    }
    if ($intWriteAccessId == 0) {
        if ((($intMselValue1 != 0) || ($intMselValue2 != 0) || ($intMselValue6 != 0)) && (($intMselValue3 != 0) ||
            ($intMselValue6 != 0)) && (($intMselValue5 != 0) || ($intMselValue4 != 0)) && ($chkTfNullVal1 != 'NULL') &&
            ($chkTfNullVal2 != 'NULL') && ($chkTfNullVal3 != 'NULL')) {
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
                    $myDataClass->writeLog(translate('New service escalation inserted:'). ' ' .$chkTfValue1);
                }
                if ($chkModus == 'modify') {
                    $myDataClass->writeLog(translate('Service escalation modified:'). ' ' .$chkTfValue1);
                }
                //
                // Insert/update relations
                // =======================
                if ($chkModus == 'insert') {
                    if ($intMselValue1 != 0) {
                        $intRet1 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServiceescalationToHost',
                            $chkDataId,
                            $chkMselValue1
                        );
                    }
                    if (isset($intRet1) && ($intRet1 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue2 != 0) {
                        $intRet2 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServiceescalationToHostgroup',
                            $chkDataId,
                            $chkMselValue2
                        );
                    }
                    if (isset($intRet2) && ($intRet2 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue3 != 0) {
                        $intRet3 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServiceescalationToService',
                            $chkDataId,
                            $chkMselValue3
                        );
                    }
                    if (isset($intRet3) && ($intRet3 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue4 != 0) {
                        $intRet4 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServiceescalationToContact',
                            $chkDataId,
                            $chkMselValue4
                        );
                    }
                    if (isset($intRet4) && ($intRet4 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue5 != 0) {
                        $intRet5 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServiceescalationToContactgroup',
                            $chkDataId,
                            $chkMselValue5
                        );
                    }
                    if (isset($intRet5) && ($intRet5 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue6 != 0) {
                        $intRet6 = $myDataClass->dataInsertRelation(
                            'tbl_lnkServiceescalationToServicegroup',
                            $chkDataId,
                            $chkMselValue6
                        );
                    }
                    if (isset($intRet6) && ($intRet6 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                } elseif ($chkModus == 'modify') {
                    if ($intMselValue1 != 0) {
                        $intRet1 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServiceescalationToHost',
                            $chkDataId,
                            $chkMselValue1
                        );
                    } else {
                        $intRet1 = $myDataClass->dataDeleteRelation('tbl_lnkServiceescalationToHost', $chkDataId);
                    }
                    if (isset($intRet1) && ($intRet1 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue2 != 0) {
                        $intRet2 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServiceescalationToHostgroup',
                            $chkDataId,
                            $chkMselValue2
                        );
                    } else {
                        $intRet2 = $myDataClass->dataDeleteRelation('tbl_lnkServiceescalationToHostgroup', $chkDataId);
                    }
                    if (isset($intRet2) && ($intRet2 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue3   != 0) {
                        $intRet3 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServiceescalationToService',
                            $chkDataId,
                            $chkMselValue3
                        );
                    } else {
                        $intRet3 = $myDataClass->dataDeleteRelation('tbl_lnkServiceescalationToService', $chkDataId);
                    }
                    if (isset($intRet3) && ($intRet3 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue4 != 0) {
                        $intRet4 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServiceescalationToContact',
                            $chkDataId,
                            $chkMselValue4
                        );
                    } else {
                        $intRet4 = $myDataClass->dataDeleteRelation('tbl_lnkServiceescalationToContact', $chkDataId);
                    }
                    if (isset($intRet4) && ($intRet4 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue5 != 0) {
                        $intRet5 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServiceescalationToContactgroup',
                            $chkDataId,
                            $chkMselValue5
                        );
                    } else {
                        $intRet5 = $myDataClass->dataDeleteRelation(
                            'tbl_lnkServiceescalationToContactgroup',
                            $chkDataId
                        );
                    }
                    if (isset($intRet5) && ($intRet5 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue6 != 0) {
                        $intRet6 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkServiceescalationToServicegroup',
                            $chkDataId,
                            $chkMselValue6
                        );
                    } else {
                        $intRet6 = $myDataClass->dataDeleteRelation(
                            'tbl_lnkServiceescalationToServicegroup',
                            $chkDataId
                        );
                    }
                    if (isset($intRet6) && ($intRet6 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                }
                if (($intRet1 + $intRet2 + $intRet3 + $intRet4 + $intRet5 + $intRet6) != 0) {
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
if (($chkModus != 'add') && ($chkModus != 'refresh')) {
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
if (($chkModus == 'add') || ($chkModus == 'refresh')) {
    $conttp->setVariable('TITLE', translate('Define service escalation (serviceescalations.cfg)'));
    // Do not show modified time list
    $intNoTime = 1;
    // Refresh mode
    if ($chkModus == 'refresh') {
        $_SESSION['refresh']['se_host']         = $chkMselValue1;
        $_SESSION['refresh']['se_hostgroup']    = $chkMselValue2;
        $_SESSION['refresh']['se_service']      = $chkMselValue3;
        $_SESSION['refresh']['se_contact']      = $chkMselValue4;
        $_SESSION['refresh']['se_contactgroup'] = $chkMselValue5;
        $_SESSION['refresh']['se_servicegroup'] = $chkMselValue6;
        $myVisClass->arrSession = $_SESSION;
    } else {
        $_SESSION['refresh']['se_host']         = $chkMselValue1;
        $_SESSION['refresh']['se_hostgroup']    = $chkMselValue2;
        $_SESSION['refresh']['se_service']      = $chkMselValue3;
        $_SESSION['refresh']['se_contact']      = $chkMselValue4;
        $_SESSION['refresh']['se_contactgroup'] = $chkMselValue5;
        $_SESSION['refresh']['se_servicegroup'] = $chkMselValue6;
        if (isset($arrModifyData['host_name']) && ($arrModifyData['host_name'] > 0)) {
            $arrTemp   = array();
            $strSQL    = 'SELECT `idSlave`, `exclude` '
                . 'FROM `tbl_lnkServiceescalationToHost` WHERE `idMaster` = ' .$arrModifyData['id'];
            $booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($booReturn == false) {
                $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
            }
            if ($intDC != 0) {
                foreach ($arrData as $elem) {
                    if ($elem['exclude'] == 1) {
                        $arrTemp[] = 'e' .$elem['idSlave'];
                    } else {
                        $arrTemp[] = $elem['idSlave'];
                    }
                }
            }
            if ($arrModifyData['host_name'] == 2) {
                $arrTemp[] = '*';
            }
            $_SESSION['refresh']['se_host'] = $arrTemp;
        }
        if (isset($arrModifyData['hostgroup_name']) && ($arrModifyData['hostgroup_name'] > 0)) {
            $arrTemp   = array();
            $strSQL    = 'SELECT `idSlave`, `exclude` '
                . 'FROM `tbl_lnkServiceescalationToHostgroup` WHERE `idMaster` = ' .$arrModifyData['id'];
            $booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($booReturn == false) {
                $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
            }
            if ($intDC != 0) {
                foreach ($arrData as $elem) {
                    if ($elem['exclude'] == 1) {
                        $arrTemp[] = 'e' .$elem['idSlave'];
                    } else {
                        $arrTemp[] = $elem['idSlave'];
                    }
                }
            }
            if ($arrModifyData['hostgroup_name'] == 2) {
                $arrTemp[] = '*';
            }
            $_SESSION['refresh']['se_hostgroup']  = $arrTemp;
        }
        $myVisClass->arrSession = $_SESSION;
    }
    // Process host selection field
    if (isset($arrModifyData['host_name'])) {
        $intFieldId = $arrModifyData['host_name'];
    } else {
        $intFieldId = 0;
    }
    if (($chkModus == 'refresh') && is_array($chkMselValue1) && (count($chkMselValue1) != 0)) {
        $strRefresh = 'se_host';
    } else {
        $strRefresh = '';
    }
    $intReturn1 = $myVisClass->parseSelectMulti(
        'tbl_host',
        'host_name',
        'host',
        'tbl_lnkServiceescalationToHost',
        2,
        $intFieldId,
        -9,
        $strRefresh
    );
    if ($intReturn1 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (isset($arrModifyData['hostgroup_name'])) {
        $intFieldId = $arrModifyData['hostgroup_name'];
    } else {
        $intFieldId = 0;
    }
    if (($chkModus == 'refresh') && is_array($chkMselValue2) && (count($chkMselValue2) != 0)) {
        $strRefresh = 'se_hostgroup';
    } else {
        $strRefresh = '';
    }
    $intReturn2 = $myVisClass->parseSelectMulti(
        'tbl_hostgroup',
        'hostgroup_name',
        'hostgroup',
        'tbl_lnkServiceescalationToHostgroup',
        2,
        $intFieldId,
        -9,
        $strRefresh
    );
    if ($intReturn2 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (($intReturn1 != 0) && ($intReturn2 != 0)) {
        $myVisClass->processMessage(translate('Attention, no hosts and hostgroups defined!'), $strDBWarning);
        $intDataWarning = 1;
    }
    // Process time period selection field
    if (isset($arrModifyData['escalation_period'])) {
        $intFieldId = $arrModifyData['escalation_period'];
    } else {
        $intFieldId = 0;
    }
    if ($chkModus == 'refresh') {
        $intFieldId = $chkSelValue1;
    }
    $intReturn = $myVisClass->parseSelectSimple('tbl_timeperiod', 'timeperiod_name', 'timeperiod', 1, $intFieldId);
    if ($intReturn != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Process contact and contact group selection field
    if (isset($arrModifyData['contacts'])) {
        $intFieldId = $arrModifyData['contacts'];
    } else {
        $intFieldId = 0;
    }
    if (($chkModus == 'refresh') && is_array($chkMselValue4) && (count($chkMselValue4) != 0)) {
        $strRefresh = 'se_contact';
    } else {
        $strRefresh = '';
    }
    $intReturn1 = $myVisClass->parseSelectMulti(
        'tbl_contact',
        'contact_name',
        'contact',
        'tbl_lnkServiceescalationToContact',
        2,
        $intFieldId,
        -9,
        $strRefresh
    );
    if ($intReturn1 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (isset($arrModifyData['contact_groups'])) {
        $intFieldId = $arrModifyData['contact_groups'];
    } else {
        $intFieldId = 0;
    }
    if (($chkModus == 'refresh') && is_array($chkMselValue5) && (count($chkMselValue5) != 0)) {
        $strRefresh = 'se_contactgroup';
    } else {
        $strRefresh = '';
    }
    $intReturn2 = $myVisClass->parseSelectMulti(
        'tbl_contactgroup',
        'contactgroup_name',
        'contactgroup',
        'tbl_lnkServiceescalationToContactgroup',
        2,
        $intFieldId,
        -9,
        $strRefresh
    );
    if ($intReturn2 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (($intReturn1 != 0) && ($intReturn2 != 0)) {
        $myVisClass->processMessage(translate('Attention, no contacts and contactgroups defined!'), $strDBWarning);
        $intDataWarning = 1;
    }
    // Process services selection field
    if (isset($arrModifyData['service_description'])) {
        $intFieldId = $arrModifyData['service_description'];
    } else {
        $intFieldId = 0;
    }
    if (($chkModus == 'refresh') && is_array($chkMselValue3) && (count($chkMselValue3) != 0)) {
        $strRefresh = 'se_service';
    } else {
        $strRefresh = '';
    }
    $intReturn = $myVisClass->parseSelectMulti(
        'tbl_service',
        'service_description',
        'service',
        'tbl_lnkServiceescalationToService',
        2,
        $intFieldId,
        -9,
        $strRefresh
    );
    if ($intReturn != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Process servicegroup selection field
    if (isset($arrModifyData['servicegroup_name'])) {
        $intFieldId = $arrModifyData['servicegroup_name'];
    } else {
        $intFieldId = 0;
    }
    if (($chkModus == 'refresh') && is_array($chkMselValue6) && (count($chkMselValue6) != 0)) {
        $strRefresh = 'se_servicegroup';
    } else {
        $strRefresh = '';
    }
    $intReturn = $myVisClass->parseSelectMulti(
        'tbl_servicegroup',
        'servicegroup_name',
        'servicegroup',
        'tbl_lnkServiceescalationToServicegroup',
        0,
        $intFieldId,
        -9,
        $strRefresh
    );
    if ($intReturn != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Process access group selection field
    if (isset($arrModifyData['access_group'])) {
        $intFieldId = $arrModifyData['access_group'];
    } else {
        $intFieldId = 0;
    }
    if ($chkModus == 'refresh') {
        $intFieldId = $chkSelAccGr;
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
    if ($intVersion < 3) {
        $conttp->setVariable('VERSION_20_VALUE_MUST', 'mselValue1,');
    }
    if ($chkModus == 'refresh') {
        if ($chkTfNullVal1    != 'NULL') {
            $conttp->setVariable('DAT_FIRST_NOTIFICATION', $chkTfNullVal1);
        }
        if ($chkTfNullVal2    != 'NULL') {
            $conttp->setVariable('DAT_LAST_NOTIFICATION', $chkTfNullVal2);
        }
        if ($chkTfNullVal3  != 'NULL') {
            $conttp->setVariable('DAT_NOTIFICATION_INTERVAL', $chkTfNullVal3);
        }
        if ($chkTfValue1    != '') {
            $conttp->setVariable('DAT_CONFIG_NAME', $chkTfValue1);
        }
        foreach (explode(',', $strEO) as $elem) {
            $conttp->setVariable('DAT_EO' .strtoupper($elem). '_CHECKED', 'checked');
        }
        if ($chkActive   != 1) {
            $conttp->setVariable('ACT_CHECKED', '');
        }
        if ($chkRegister != 1) {
            $conttp->setVariable('REG_CHECKED', '');
        }
        if ($chkDataId   != 0) {
            $conttp->setVariable('MODUS', 'modify');
            $conttp->setVariable('DAT_ID', $chkDataId);
        }
        // Insert data from database in "modify" mode
    } elseif (isset($arrModifyData) && ($chkSelModify == 'modify')) {
        // Check relation information to find out locked configuration datasets
        $intLocked = $myDataClass->infoRelation($preTableName, $arrModifyData['id'], $preKeyField);
        $myVisClass->processMessage($myDataClass->strInfoMessage, $strRelMessage);
        $strInfo  = '<br><span class="redmessage">' .translate('Entry cannot be activated because it is used by '
                .'another configuration'). ':</span>';
        $strInfo .= '<br><span class="greenmessage">' .$strRelMessage. '</span>';
        // Process data
        $myContentClass->addInsertData($conttp, $arrModifyData, $intLocked, $strInfo);
        // Process option fields
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
    $mastertp->setVariable('TITLE', translate('Define service escalation (serviceescalations.cfg)'));
    $mastertp->setVariable('FIELD_1', translate('Config name'));
    $mastertp->setVariable('FIELD_2', translate('Services'));
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
    $strSQL    = "SELECT `id`, `$preKeyField`, `service_description`, `register`, `active`, `config_id`, "
        . "`access_group` FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN "
        . "($strAccess) $strOrderString LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
