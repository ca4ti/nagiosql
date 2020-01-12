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
// Component : Contact template definitions
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
$prePageId        = 17;
$preContent       = 'admin/contacttemplates.htm.tpl';
$preListTpl       = 'admin/datalist.htm.tpl';
$preSearchSession = 'contacttemplate';
$preTableName     = 'tbl_contacttemplate';
$preKeyField      = 'template_name';
$preAccess        = 1;
$preFieldvars     = 1;
//
// Include preprocessing files
// ===========================
require $preBasePath.'functions/prepend_adm.php';
require $preBasePath.'functions/prepend_content.php';
//
// Checkbox data processing
// ========================
if (($intVersion == 3) || ($intVersion == 4)) {
    $strHO = substr($chkChbGr1a.$chkChbGr1b.$chkChbGr1c.$chkChbGr1d.$chkChbGr1e.$chkChbGr1f, 0, -1);
    $strSO = substr($chkChbGr2a.$chkChbGr2b.$chkChbGr2c.$chkChbGr2d.$chkChbGr2e.$chkChbGr2f.$chkChbGr2g, 0, -1);
} else {
    $strHO = substr($chkChbGr1a.$chkChbGr1b.$chkChbGr1c.$chkChbGr1d.$chkChbGr1f, 0, -1);
    $strSO = substr($chkChbGr2a.$chkChbGr2b.$chkChbGr2c.$chkChbGr2d.$chkChbGr2e.$chkChbGr2g, 0, -1);
}
//
// Add or modify data
// ==================
if ((($chkModus == 'insert') || ($chkModus == 'modify')) && ($intGlobalWriteAccess == 0)) {
    $strSQLx = "`$preTableName` SET `$preKeyField`='$chkTfValue1', `alias`='$chkTfValue2', "
             . "`contactgroups`=$intMselValue1, `contactgroups_tploptions`=$chkRadValue1, "
             . "`minimum_importance`=$chkTfNullVal1, "
             . "`host_notifications_enabled`='$chkRadValue2', `service_notifications_enabled`='$chkRadValue3', "
             . "`host_notification_period`='$chkSelValue1', `service_notification_period`='$chkSelValue2', "
             . "`host_notification_options`='$strHO', `host_notification_commands_tploptions`=$chkRadValue4, "
             . "`service_notification_options`='$strSO', `host_notification_commands`=$intMselValue2, "
             . "`service_notification_commands`=$intMselValue3, "
             . "`service_notification_commands_tploptions`=$chkRadValue5, `can_submit_commands`='$chkRadValue8', "
             . "`retain_status_information`='$chkRadValue6', `retain_nonstatus_information`='$chkRadValue7', "
             . "`email`='$chkTfValue3', `pager`='$chkTfValue4', `address1`='$chkTfValue5', `address2`='$chkTfValue6', "
             . "`address3`='$chkTfValue7', `address4`='$chkTfValue8', `address5`='$chkTfValue9', "
             . "`address6`='$chkTfValue10', `use_variables`='$intVariables', `use_template`=$intTemplates, "
             . $preSQLCommon2;
    if ($chkModus == 'insert') {
        $strSQL = 'INSERT INTO ' .$strSQLx;
    } else {
        $strSQL = 'UPDATE ' .$strSQLx. ' WHERE `id`=' .$chkDataId;
    }
    if ($intWriteAccessId == 0) {
        if ($chkTfValue1 != '') {
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
                    $myDataClass->writeLog(translate('New contact template inserted:'). ' ' .$chkTfValue1);
                }
                if ($chkModus  == 'modify') {
                    $myDataClass->writeLog(translate('Contact template modified:'). ' ' .$chkTfValue1);
                }
                //
                // Insert/update relations
                // =======================
                if ($chkModus == 'insert') {
                    if ($intMselValue1 != 0) {
                        $intRet1 = $myDataClass->dataInsertRelation(
                            'tbl_lnkContacttemplateToContactgroup',
                            $chkDataId,
                            $chkMselValue1
                        );
                    }
                    if (isset($intRet1) && ($intRet1 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue2 != 0) {
                        $intRet2 = $myDataClass->dataInsertRelation(
                            'tbl_lnkContacttemplateToCommandHost',
                            $chkDataId,
                            $chkMselValue2
                        );
                    }
                    if (isset($intRet2) && ($intRet2 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue3 != 0) {
                        $intRet3 = $myDataClass->dataInsertRelation(
                            'tbl_lnkContacttemplateToCommandService',
                            $chkDataId,
                            $chkMselValue3
                        );
                    }
                    if (isset($intRet3) && ($intRet3 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                } elseif ($chkModus == 'modify') {
                    if ($intMselValue1 != 0) {
                        $intRet1 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkContacttemplateToContactgroup',
                            $chkDataId,
                            $chkMselValue1
                        );
                    } else {
                        $intRet1 = $myDataClass->dataDeleteRelation('tbl_lnkContacttemplateToContactgroup', $chkDataId);
                    }
                    if ($intRet1 != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue2 != 0) {
                        $intRet2 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkContacttemplateToCommandHost',
                            $chkDataId,
                            $chkMselValue2
                        );
                    } else {
                        $intRet2 = $myDataClass->dataDeleteRelation('tbl_lnkContacttemplateToCommandHost', $chkDataId);
                    }
                    if ($intRet2 != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue3 != 0) {
                        $intRet3 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkContacttemplateToCommandService',
                            $chkDataId,
                            $chkMselValue3
                        );
                    } else {
                        $intRet3 = $myDataClass->dataDeleteRelation(
                            'tbl_lnkContacttemplateToCommandService',
                            $chkDataId
                        );
                    }
                    if ($intRet3 != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                }
                //if (($intRet1 + $intRet2 + $intRet3) != 0) {
                    //$strInfoMessage = "";
                //}
                //
                // Insert/update templates from session data
                // =========================================
                if ($chkModus == 'modify') {
                    $strSQL    = 'DELETE FROM `tbl_lnkContacttemplateToContacttemplate` WHERE `idMaster`=' .$chkDataId;
                    $booReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                    if ($booReturn == false) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                }
                if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition']) &&
                    (count($_SESSION['templatedefinition']) != 0)) {
                    $intSortId = 1;
                    /** @noinspection ForeachSourceInspection */
                    foreach ($_SESSION['templatedefinition'] as $elem) {
                        if ($elem['status'] == 0) {
                            $strSQL    = 'INSERT INTO `tbl_lnkContacttemplateToContacttemplate` (`idMaster`, '
                                       . "`idSlave`,`idTable`,`idSort`) VALUES ($chkDataId,".$elem['idSlave']. ', '
                                       . $elem['idTable']. ',' .$intSortId. ')';
                            $booReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                            if ($booReturn == false) {
                                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                            }
                        }
                        $intSortId++;
                    }
                }
                //
                // Insert/update variables from session data
                // =========================================
                if ($chkModus == 'modify') {
                    $strSQL    = 'SELECT * '
                               . 'FROM `tbl_lnkContacttemplateToVariabledefinition` WHERE `idMaster`=' .$chkDataId;
                    $booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
                    if ($booReturn == false) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intDataCount != 0) {
                        foreach ($arrData as $elem) {
                            $strSQL    = 'DELETE FROM `tbl_variabledefinition` WHERE `id`=' .$elem['idSlave'];
                            $booReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                            if ($booReturn == false) {
                                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                            }
                        }
                    }
                    $strSQL    = 'DELETE FROM `tbl_lnkContacttemplateToVariabledefinition` '
                               . 'WHERE `idMaster`=' .$chkDataId;
                    $booReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                    if ($booReturn == false) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                }
                if (isset($_SESSION['variabledefinition']) && is_array($_SESSION['variabledefinition']) &&
                    (count($_SESSION['variabledefinition']) != 0)) {
                    foreach ($_SESSION['variabledefinition'] as $elem) {
                        if ($elem['status'] == 0) {
                            $strSQL    = 'INSERT INTO `tbl_variabledefinition` (`name`,`value`,`last_modified`) '
                                       . "VALUES ('".$elem['definition']."','".$elem['range']."',now())";
                            $booReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                            if ($booReturn == false) {
                                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                            }
                            $strSQL    = 'INSERT INTO `tbl_lnkContacttemplateToVariabledefinition` (`idMaster`, '
                                       . "`idSlave`) VALUES ($chkDataId,$intInsertId)";
                            $booReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                            if ($booReturn == false) {
                                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                            }
                        }
                    }
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
    $chkModus = 'display';
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
    $conttp->setVariable('TITLE', translate('Define contact templates (contacttemplates.cfg)'));
    // Do not show modified time list
    $intNoTime = 1;
    // Process template selection fields (Spezial)
    $strWhere = '';
    if (isset($arrModifyData) && ($chkSelModify == 'modify')) {
        $strWhere = 'AND `id` <> ' .$arrModifyData['id'];
    }
    $strSQL1    = "SELECT `id`,`$preKeyField`, `active` FROM `$preTableName` "
                . "WHERE $strDomainWhere $strWhere ORDER BY `$preKeyField`";
    $booReturn1 = $myDBClass->hasDataArray($strSQL1, $arrDataTpl, $intDataCountTpl);
    if ($booReturn1 == false) {
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    }
    if ($intDataCountTpl != 0) {
        /** @var array $arrDataTpl */
        foreach ($arrDataTpl as $elem) {
            if ($elem['active'] == 0) {
                $strActive = ' [inactive]';
                $conttp->setVariable('SPECIAL_STYLE', 'inactive_option');
            } else {
                $strActive = '';
                $conttp->setVariable('SPECIAL_STYLE', '');
            }
            $conttp->setVariable('DAT_TEMPLATE', htmlspecialchars($elem[$preKeyField], ENT_QUOTES, 'UTF-8').$strActive);
            $conttp->setVariable('DAT_TEMPLATE_ID', $elem['id']. '::1');
            $conttp->parse('template');
        }
    }
    $strSQL2    = 'SELECT `id`, `name`, `active` FROM `tbl_contact` '
                . "WHERE `name` <> '' AND $strDomainWhere2 ORDER BY name";
    $booReturn2 = $myDBClass->hasDataArray($strSQL2, $arrDataHpl, $intDataCountHpl);
    if ($booReturn2 == false) {
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    }
    if ($intDataCountHpl != 0) {
        /** @var array $arrDataHpl */
        foreach ($arrDataHpl as $elem) {
            if ($elem['active'] == 0) {
                $strActive = ' [inactive]';
                $conttp->setVariable('SPECIAL_STYLE', 'inactive_option');
            } else {
                $strActive = '';
                $conttp->setVariable('SPECIAL_STYLE', '');
            }
            $conttp->setVariable('DAT_TEMPLATE', htmlspecialchars($elem['name'], ENT_QUOTES, 'UTF-8').$strActive);
            $conttp->setVariable('DAT_TEMPLATE_ID', $elem['id']. '::2');
            $conttp->parse('template');
        }
    }
    // Process timeperiod selection fields
    if (isset($arrModifyData['host_notification_period'])) {
        $intFieldId = $arrModifyData['host_notification_period'];
    } else {
        $intFieldId = 0;
    }
    $intReturn1 = $myVisClass->parseSelectSimple('tbl_timeperiod', 'timeperiod_name', 'host_time', 1, $intFieldId);
    if ($intReturn1 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (isset($arrModifyData['service_notification_period'])) {
        $intFieldId = $arrModifyData['service_notification_period'];
    } else {
        $intFieldId = 0;
    }
    $intReturn2 = $myVisClass->parseSelectSimple('tbl_timeperiod', 'timeperiod_name', 'service_time', 1, $intFieldId);
    if ($intReturn2 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Process command selection fields
    if (isset($arrModifyData['host_notification_commands'])) {
        $intFieldId = $arrModifyData['host_notification_commands'];
    } else {
        $intFieldId = 0;
    }
    $intReturn3 = $myVisClass->parseSelectMulti(
        'tbl_command',
        'command_name',
        'host_command',
        'tbl_lnkContacttemplateToCommandHost',
        0,
        $intFieldId
    );
    if ($intReturn3 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (isset($arrModifyData['service_notification_commands'])) {
        $intFieldId = $arrModifyData['service_notification_commands'];
    } else {
        $intFieldId = 0;
    }
    $intReturn4 = $myVisClass->parseSelectMulti(
        'tbl_command',
        'command_name',
        'service_command',
        'tbl_lnkContacttemplateToCommandService',
        0,
        $intFieldId
    );
    if ($intReturn4 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Process contactgroup selection field
    if (isset($arrModifyData['contactgroups'])) {
        $intFieldId = $arrModifyData['contactgroups'];
    } else {
        $intFieldId = 0;
    }
    $intReturn5 = $myVisClass->parseSelectMulti(
        'tbl_contactgroup',
        'contactgroup_name',
        'contactgroup',
        'tbl_lnkContacttemplateToContactgroup',
        2,
        $intFieldId
    );
    if ($intReturn5 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Process acces group selection field
    if (isset($arrModifyData['access_group'])) {
        $intFieldId = $arrModifyData['access_group'];
    } else {
        $intFieldId = 0;
    }
    $intReturn6 = $myVisClass->parseSelectSimple('tbl_group', 'groupname', 'acc_group', 0, $intFieldId);
    if ($intReturn6 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Initial add/modify form definitions
    $strChbFields = 'HNE,SNE,RSI,CSC,RNS,TPL,SEC,HOC,COG';
    $myContentClass->addFormInit($conttp, $strChbFields);
    if ($intDataWarning == 1) {
        $conttp->setVariable('WARNING', $strDBWarning. '<br>' .translate('Saving not possible!'));
    }
    if ($intVersion == 4) {
        $conttp->setVariable('HOST_OPTION_FIELDS', 'chbGr1a,chbGr1b,chbGr1c,chbGr1d,chbGr1e,chbGr1f');
        $conttp->setVariable('SERVICE_OPTION_FIELDS', 'chbGr2a,chbGr2b,chbGr2c,chbGr2d,chbGr2e,chbGr2f,chbGr2g');
    }
    if ($intVersion == 3) {
        $conttp->setVariable('HOST_OPTION_FIELDS', 'chbGr1a,chbGr1b,chbGr1c,chbGr1d,chbGr1e,chbGr1f');
        $conttp->setVariable('SERVICE_OPTION_FIELDS', 'chbGr2a,chbGr2b,chbGr2c,chbGr2d,chbGr2e,chbGr2f,chbGr2g');
    }
    if ($intVersion < 3) {
        $conttp->setVariable('HOST_OPTION_FIELDS', 'chbGr1a,chbGr1b,chbGr1c,chbGr1d,chbGr1f');
        $conttp->setVariable('SERVICE_OPTION_FIELDS', 'chbGr2a,chbGr2b,chbGr2c,chbGr2d,chbGr2e,chbGr2g');
        $conttp->setVariable('VERSION_20_VALUE_MUST', ',tfValue2');
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
        $myContentClass->addInsertData($conttp, $arrModifyData, $intLocked, $strInfo, $strChbFields);
        // Process radio fields
        $conttp->setVariable('DAT_HNE' .$arrModifyData['host_notifications_enabled']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_SNE' .$arrModifyData['service_notifications_enabled']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_RSI' .$arrModifyData['retain_status_information']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_CSC' .$arrModifyData['can_submit_commands']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_RNS' .$arrModifyData['retain_nonstatus_information']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_TPL' .$arrModifyData['use_template_tploptions']. '_CHECKED', 'checked');
        $conttp->setVariable(
            'DAT_SEC' .$arrModifyData['service_notification_commands_tploptions']. '_CHECKED',
            'checked'
        );
        $conttp->setVariable('DAT_HOC' .$arrModifyData['host_notification_commands_tploptions']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_COG' .$arrModifyData['contactgroups_tploptions']. '_CHECKED', 'checked');
        // Process option fields
        foreach (explode(',', $arrModifyData['host_notification_options']) as $elem) {
            $conttp->setVariable('DAT_HO' .strtoupper($elem). '_CHECKED', 'checked');
        }
        foreach (explode(',', $arrModifyData['service_notification_options']) as $elem) {
            $conttp->setVariable('DAT_SO' .strtoupper($elem). '_CHECKED', 'checked');
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
    $mastertp->setVariable('TITLE', translate('Define contact templates (contacttemplates.cfg)'));
    $mastertp->setVariable('FIELD_1', translate('Contact name'));
    $mastertp->setVariable('FIELD_2', translate('Description'));
    $mastertp->setVariable('FILTER_VISIBLE', 'visibility: hidden');
    // Process filter string
    if ($_SESSION['search'][$preSearchSession] != '') {
        $strSearchTxt   = $_SESSION['search'][$preSearchSession];
        $strSearchWhere = "AND (`$preKeyField` LIKE '%".$strSearchTxt."%' OR `alias` LIKE '%".$strSearchTxt."%' OR "
                        . "`email` LIKE '%".$strSearchTxt."%' OR `pager` LIKE '%".$strSearchTxt."%' OR "
                        . "`address1` LIKE '%".$strSearchTxt."%' OR `address2` LIKE '%".$strSearchTxt."%' OR "
                        . "`address3` LIKE '%".$strSearchTxt."%' OR `address4` LIKE '%".$strSearchTxt."%' OR "
                        . "`address5` LIKE '%".$strSearchTxt."%' OR `address6` LIKE '%".$strSearchTxt."%')";
    }
    // Row sorting
    $strOrderString = "ORDER BY `config_id`, `$preKeyField` $hidSortDir";
    if ($hidSortBy == 2) {
        $strOrderString = "ORDER BY `config_id`, `alias` $hidSortDir";
    }
    // Count datasets
    $strSQL     = 'SELECT count(*) AS `number` '
                . "FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
    $booReturn1 = $myDBClass->hasSingleDataset($strSQL, $arrDataLinesCount);
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
    $strSQL     = "SELECT `id`, `$preKeyField`, `alias`, `active`, `register`, `config_id`, `access_group` "
                . "FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess) "
                . "$strOrderString LIMIT $chkLimit,".$SETS['common']['pagelines'];
    $booReturn2 = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
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
