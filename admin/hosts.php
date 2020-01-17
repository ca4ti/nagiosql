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
// Component : Host definition
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
$prePageId        = 8;
$preContent       = 'admin/hosts.htm.tpl';
$preListTpl       = 'admin/datalist_hosts.htm.tpl';
$preSearchSession = 'host';
$preTableName     = 'tbl_host';
$preKeyField      = 'host_name';
$preAccess        = 1;
$preFieldvars     = 1;
$strSqlParents    = '';
//
// Include preprocessing files
// ===========================
require $preBasePath.'functions/prepend_adm.php';
require $preBasePath.'functions/prepend_content.php';
//
// Data processing
// ===============
$strNO = substr($chkChbGr1a.$chkChbGr1b.$chkChbGr1c.$chkChbGr1d.$chkChbGr1e, 0, -1);
$strIS = substr($chkChbGr2a.$chkChbGr2b.$chkChbGr2c, 0, -1);
$strFL = substr($chkChbGr3a.$chkChbGr3b.$chkChbGr3c, 0, -1);
$strST = substr($chkChbGr4a.$chkChbGr4b.$chkChbGr4c, 0, -1);
if ($chkSelValue1 != '') {
    for ($i = 1; $i <= 8; $i++) {
        $tmpVar = 'chkTfArg'.$i;
        $$tmpVar = str_replace('!', '::bang::', $$tmpVar);
        if ($$tmpVar != '') {
            $chkSelValue1 .= '!' .$$tmpVar;
        }
    }
}
//
// Add or modify data
// ==================
if ((($chkModus == 'insert') || ($chkModus == 'modify')) && ($intGlobalWriteAccess == 0)) {
    if ($SETS['performance']['parents'] == 1) {
        $strSqlParents = "`parents`=$intMselValue1, `parents_tploptions`=$chkRadValue1,";
    }
    $strSQLx = "`$preTableName` SET `$preKeyField`='$chkTfValue1', `alias`='$chkTfValue3', "
             . "`display_name`='$chkTfValue4', `address`='$chkTfValue5', `name`='$chkTfValue6', "
             . "$strSqlParents `importance`=$chkTfNullVal9, `hostgroups`=$intMselValue2, "
             . "`hostgroups_tploptions`=$chkRadValue2, `check_command`='$chkSelValue1', `use_template`=$intTemplates, "
             . "`initial_state`='$strIS', `max_check_attempts`=$chkTfNullVal2, `check_interval`=$chkTfNullVal3, "
             . "`retry_interval`=$chkTfNullVal1, `active_checks_enabled`=$chkRadValue5, "
             . "`passive_checks_enabled`=$chkRadValue6, `check_period`=$chkSelValue2, "
             . "`obsess_over_host`=$chkRadValue8, `check_freshness`=$chkRadValue7, "
             . "`freshness_threshold`=$chkTfNullVal4, `event_handler`=$chkSelValue3, "
             . "`event_handler_enabled`=$chkRadValue9, `low_flap_threshold`=$chkTfNullVal5, "
             . "`high_flap_threshold`=$chkTfNullVal6, `flap_detection_enabled`=$chkRadValue10, "
             . "`flap_detection_options`='$strFL', `process_perf_data`=$chkRadValue13, "
             . "`retain_status_information`=$chkRadValue11, `retain_nonstatus_information`=$chkRadValue12, "
             . "`contacts`=$intMselValue3, `contacts_tploptions`=$chkRadValue3, `contact_groups`=$intMselValue4, "
             . "`contact_groups_tploptions`=$chkRadValue4, `notification_interval`=$chkTfNullVal7, "
             . "`notification_period`=$chkSelValue4, `first_notification_delay`=$chkTfNullVal8, "
             . "`notification_options`='$strNO', `notifications_enabled`=$chkRadValue14, `stalking_options`='$strST', "
             . "`notes`='$chkTfValue7', `notes_url`='$chkTfValue9', `action_url`='$chkTfValue11', "
             . "`icon_image`='$chkTfValue12', `icon_image_alt`='$chkTfValue13', `vrml_image`='$chkTfValue8', "
             . "`statusmap_image`='$chkTfValue10', `2d_coords`='$chkTfValue14', `3d_coords`='$chkTfValue15', "
             . $preSQLCommon1;
    if ($chkModus == 'insert') {
        $strSQL = 'INSERT INTO ' .$strSQLx;
    } else {
        $strSQL = 'UPDATE ' .$strSQLx. ' WHERE `id`=' .$chkDataId;
    }
    if ($intWriteAccessId == 0) {
        if (($chkTfValue1 != '') && ($chkTfValue5 != '')) {
            $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
            if ($chkModus == 'insert') {
                $chkDataId = $intInsertId;
            }
            if ($intReturn == 1) {
                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
            } else {
                $myVisClass->processMessage($myDataClass->strInfoMessage, $strInfoMessage);
                if ($chkModus == 'insert') {
                    $myDataClass->writeLog(translate('New host inserted:'). ' ' .$chkTfValue1);
                }
                if ($chkModus == 'modify') {
                    $myDataClass->writeLog(translate('Host modified:'). ' ' .$chkTfValue1);
                }
                //
                // Insert/update relations
                // =======================
                if ($chkModus == 'insert') {
                    if ($SETS['performance']['parents'] == 1) {
                        if ($intMselValue1 != 0) {
                            $intRet1 = $myDataClass->dataInsertRelation('tbl_lnkHostToHost', $chkDataId, $chkMselValue1);
                        }
                        if (isset($intRet1) && ($intRet1 != 0)) {
                            $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                        }
                    }
                    if ($intMselValue2 != 0) {
                        $intRet2 = $myDataClass->dataInsertRelation(
                            'tbl_lnkHostToHostgroup',
                            $chkDataId,
                            $chkMselValue2
                        );
                    }
                    if (isset($intRet2) && ($intRet2 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue3 != 0) {
                        $intRet3 = $myDataClass->dataInsertRelation('tbl_lnkHostToContact', $chkDataId, $chkMselValue3);
                    }
                    if (isset($intRet3) && ($intRet3 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue4 != 0) {
                        $intRet4 = $myDataClass->dataInsertRelation(
                            'tbl_lnkHostToContactgroup',
                            $chkDataId,
                            $chkMselValue4
                        );
                    }
                    if (isset($intRet4) && ($intRet4 != 0)) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                } elseif ($chkModus == 'modify') {
                    if ($SETS['performance']['parents'] == 1) {
                        if ($intMselValue1 != 0) {
                            $intRet1 = $myDataClass->dataUpdateRelation('tbl_lnkHostToHost', $chkDataId, $chkMselValue1);
                        } else {
                            $intRet1 = $myDataClass->dataDeleteRelation('tbl_lnkHostToHost', $chkDataId);
                        }
                        if ($intRet1 != 0) {
                            $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                        }
                    }
                    if ($intMselValue2 != 0) {
                        $intRet2 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkHostToHostgroup',
                            $chkDataId,
                            $chkMselValue2
                        );
                    } else {
                        $intRet2 = $myDataClass->dataDeleteRelation('tbl_lnkHostToHostgroup', $chkDataId);
                    }
                    if ($intRet2 != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue3 != 0) {
                        $intRet3 = $myDataClass->dataUpdateRelation('tbl_lnkHostToContact', $chkDataId, $chkMselValue3);
                    } else {
                        $intRet3 = $myDataClass->dataDeleteRelation('tbl_lnkHostToContact', $chkDataId);
                    }
                    if ($intRet3 != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intMselValue4 != 0) {
                        $intRet4 = $myDataClass->dataUpdateRelation(
                            'tbl_lnkHostToContactgroup',
                            $chkDataId,
                            $chkMselValue4
                        );
                    } else {
                        $intRet4 = $myDataClass->dataDeleteRelation('tbl_lnkHostToContactgroup', $chkDataId);
                    }
                    if ($intRet4 != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                }
                if (($intRet1 + $intRet2 + $intRet3 + $intRet4) != 0) {
                    $strInfoMessage = '';
                }
                //
                // Removing the config file if an entry was deleted or renamed
                // ===========================================================
                if (($chkModus == 'modify') && ($chkTfValue2 != $chkTfValue1) && ($chkDomainId != 0)) {
                    $myConfigClass->getConfigTargets($arrConfigID);
                    if (($arrConfigID != 1) && is_array($arrConfigID)) {
                        $intReturn = 0;
                        foreach ($arrConfigID as $intConfigID) {
                            $intReturn += $myConfigClass->moveFile('host', $chkTfValue2. '.cfg', $intConfigID);
                        }
                        if ($intReturn == 0) {
                            $myVisClass->processMessage(translate('The assigned, no longer used configuration files '
                                    . 'were deleted successfully!'), $strInfoMessage);
                            $myDataClass->writeLog(translate('Host file deleted:'). ' ' .$chkTfValue2. '.cfg');
                        } else {
                            if ($chkDomainId == 0) {
                                $myVisClass->processMessage(translate('Common files cannot be removed from target '
                                        . 'systems - please check manually'), $strErrorMessage);
                            } else {
                                $myVisClass->processMessage(translate('Errors while deleting the old configuration '
                                        . 'file - please check!:'), $strErrorMessage);
                                $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
                            }
                        }
                    }
                }
                //
                // Removing the config file if an entry was dectivated
                // ===================================================
                if (($chkModus == 'modify') && ($chkActive == 0)) {
                    $myConfigClass->getConfigTargets($arrConfigID);
                    if (($arrConfigID != 1) && is_array($arrConfigID)) {
                        $intReturn = 0;
                        foreach ($arrConfigID as $intConfigID) {
                            $intReturn += $myConfigClass->moveFile('host', $chkTfValue2. '.cfg', $intConfigID);
                        }
                        if ($intReturn == 0) {
                            $myVisClass->processMessage(translate('The assigned, no longer used configuration files '
                                    . 'were deleted successfully!'), $strInfoMessage);
                            $myDataClass->writeLog(translate('Host file deleted:'). ' ' .$chkTfValue1. '.cfg');
                        } else {
                            if ($chkDomainId == 0) {
                                $myVisClass->processMessage(translate('Common files cannot be removed from target '
                                        . 'systems - please check manually'), $strErrorMessage);
                            } else {
                                $myVisClass->processMessage(translate('Errors while deleting the old configuration '
                                        . 'file - please check!:'), $strErrorMessage);
                                $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
                            }
                        }
                    }
                }
                //
                // Insert/update session data for templates
                // ========================================
                if ($chkModus == 'modify') {
                    $strSQL    = 'DELETE FROM `tbl_lnkHostToHosttemplate` WHERE `idMaster`=' .$chkDataId;
                    $intReturn  = $myDataClass->dataInsert($strSQL, $intInsertId);
                    if ($intReturn != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                }
                if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition']) &&
                    (count($_SESSION['templatedefinition']) != 0)) {
                    $intSortId = 1;
                    /** @noinspection ForeachSourceInspection */
                    foreach ($_SESSION['templatedefinition'] as $elem) {
                        if ($elem['status'] == 0) {
                            $strSQL    = 'INSERT INTO `tbl_lnkHostToHosttemplate` (`idMaster`,`idSlave`,`idTable`, '
                                       . "`idSort`) VALUES ($chkDataId,".$elem['idSlave']. ',' .$elem['idTable']. ', '
                                       . $intSortId. ')';
                            $intReturn  = $myDataClass->dataInsert($strSQL, $intInsertId);
                            if ($intReturn != 0) {
                                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                            }
                        }
                        $intSortId++;
                    }
                }
                //
                // Insert/update session data for free variables
                // =============================================
                if ($chkModus == 'modify') {
                    $strSQL    = 'SELECT * FROM `tbl_lnkHostToVariabledefinition` WHERE `idMaster`=' .$chkDataId;
                    $booReturn  = $myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
                    if ($booReturn == false) {
                        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
                    }
                    if ($intDataCount != 0) {
                        foreach ($arrData as $elem) {
                            $strSQL    = 'DELETE FROM `tbl_variabledefinition` WHERE `id`=' .$elem['idSlave'];
                            $intReturn  = $myDataClass->dataInsert($strSQL, $intInsertId);
                            if ($intReturn != 0) {
                                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                            }
                        }
                    }
                    $strSQL     = 'DELETE FROM `tbl_lnkHostToVariabledefinition` WHERE `idMaster`=' .$chkDataId;
                    $intReturn1 = $myDataClass->dataInsert($strSQL, $intInsertId);
                    if ($intReturn1 != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                    $strSQL     = 'UPDATE `tbl_host` SET `use_variables`=0 WHERE `id`=' .$chkDataId;
                    $intReturn2 = $myDataClass->dataInsert($strSQL, $intInsertId);
                    if ($intReturn2 != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                }
                if (isset($_SESSION['variabledefinition']) && is_array($_SESSION['variabledefinition']) &&
                     (count($_SESSION['variabledefinition']) != 0)) {
                    $intCountVariable = 0;
                    /** @noinspection ForeachSourceInspection */
                    foreach ($_SESSION['variabledefinition'] as $elem) {
                        if ($elem['status'] == 0) {
                            $strSQL     = 'INSERT INTO `tbl_variabledefinition` (`name`,`value`,`last_modified`) '
                                        . "VALUES ('".$elem['definition']."','".$elem['range']."',now())";
                            $intReturn1 = $myDataClass->dataInsert($strSQL, $intInsertId);
                            if ($intReturn1 != 0) {
                                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                            }
                            $strSQL     = 'INSERT INTO `tbl_lnkHostToVariabledefinition` (`idMaster`,`idSlave`) '
                                        . "VALUES ($chkDataId,$intInsertId)";
                            $intReturn2 = $myDataClass->dataInsert($strSQL, $intInsertId);
                            if ($intReturn2 != 0) {
                                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                            }
                            if (($intReturn1 == 0) && ($intReturn2 == 0)) {
                                $intCountVariable++;
                            }
                        }
                    }
                    if ($intCountVariable != 0) {
                        $strSQL    = 'UPDATE `tbl_host` SET `use_variables`=1 WHERE `id`=' .$chkDataId;
                        $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                        if ($intReturn != 0) {
                            $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                        }
                    }
                }
                //
                // Insert/Update service <-> host relations
                // ========================================
                // Update service table last modified date
                $strSQL    = 'SELECT `idMaster` FROM `tbl_lnkServiceToHost` WHERE `idSlave`=' .$chkDataId;
                $booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
                if ($booReturn == false) {
                    $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
                }
                if ($intDataCount != 0) {
                    foreach ($arrData as $elem) {
                        $strSQL    = 'UPDATE `tbl_service` SET `last_modified` = NOW() WHERE `id`=' .$elem['idMaster'];
                        $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                        if ($intReturn != 0) {
                            $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                        }
                    }
                }
                // Remove any link data from host to service
                $strSQL    = 'DELETE FROM `tbl_lnkServiceToHost` WHERE `idSlave`=' .$chkDataId;
                $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                if ($intReturn != 0) {
                    $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                }
                if (is_array($chkMselValue5)) {
                    foreach ($chkMselValue5 as $elem) {
                        if ($elem != '') {
                            $intExclude = 0;
                            if (0 === strpos($elem, 'e')) {
                                $intExclude = 1;
                                $elem = substr_replace($elem, '', 0, 1);
                            }
                            $strSQL1 = 'INSERT INTO `tbl_lnkServiceToHost` (`idMaster`,`idSlave`,`exclude`) '
                                . "VALUES ($elem,$chkDataId,$intExclude)";
                            $intReturn1 = $myDataClass->dataInsert($strSQL1, $intInsertId);
                            if ($intReturn1 != 0) {
                                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                            }
                            // Update service table last modified date
                            $strSQL2 = 'UPDATE `tbl_service` SET `last_modified` = NOW() WHERE `id`=' . $elem;
                            $intReturn2 = $myDataClass->dataInsert($strSQL2, $intInsertId);
                            if ($intReturn2 != 0) {
                                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                            }
                            // Check if Service ID is not 0
                            $strSQL3 = "SELECT `$preKeyField` FROM `tbl_service` WHERE `id`=$elem";
                            $strResult3 = $myDBClass->getFieldData($strSQL3);
                            if ($strResult3 == 0) {
                                $strSQL = "UPDATE `tbl_service` SET `$preKeyField`=1 WHERE `id`=$elem";
                                $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                                if ($intReturn != 0) {
                                    $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                                }
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
// Singe data form
// ===============
if ($chkModus == 'add') {
    $conttp->setVariable('TITLE', translate('Define hosts (hosts.cfg)'));
    // Do not show modified time list
    $intNoTime = 1;
    // Process template fields
    $strWhere = '';
    if (isset($arrModifyData) && ($chkSelModify == 'modify')) {
        $strWhere = 'AND `id` <> ' .$arrModifyData['id'];
    }
    $strSQL1    = 'SELECT `id`,`template_name`, `active` ' .
                  "FROM `tbl_hosttemplate` WHERE $strDomainWhere2 ORDER BY `template_name`";
    $booReturn1 = $myDBClass->hasDataArray($strSQL1, $arrDataTpl, $intDataCountTpl);
    if ($booReturn == false) {
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
            $conttp->setVariable('DAT_TEMPLATE', htmlspecialchars($elem['template_name'], ENT_QUOTES, 'UTF-8').
                    $strActive);
            $conttp->setVariable('DAT_TEMPLATE_ID', $elem['id']. '::1');
            $conttp->parse('template');
        }
    }
    $strSQL2    = 'SELECT `id`, `name`, `active` '
                . "FROM `$preTableName` WHERE `name` <> '' $strWhere AND $strDomainWhere ORDER BY `name`";
    $booReturn2 = $myDBClass->hasDataArray($strSQL2, $arrDataHpl, $intDataCountHpl);
    if ($booReturn == false) {
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
    if ($SETS['performance']['parents'] == 1) {
        // Process host selection field
        if (isset($arrModifyData['parents'])) {
            $intFieldId = $arrModifyData['parents'];
        } else {
            $intFieldId = 0;
        }
        if (isset($arrModifyData['id'])) {
            $intKeyId = $arrModifyData['id'];
        } else {
            $intKeyId = 0;
        }
        $intReturn3 = $myVisClass->parseSelectMulti(
            $preTableName,
            $preKeyField,
            'host_parents',
            'tbl_lnkHostToHost',
            0,
            $intFieldId,
            $intKeyId
        );
        if ($intReturn3 != 0) {
            $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
        }
        $conttp->setVariable('PARENTS_VISIBLE', 'elementShow');
    } else {
        $conttp->setVariable('PARENTS_VISIBLE', 'elementHide');
    }
    // Process hostgroup selection field
    if (isset($arrModifyData['hostgroups'])) {
        $intFieldId = $arrModifyData['hostgroups'];
    } else {
        $intFieldId = 0;
    }
    $intReturn4 = $myVisClass->parseSelectMulti(
        'tbl_hostgroup',
        'hostgroup_name',
        'hostgroup',
        'tbl_lnkHostToHostgroup',
        0,
        $intFieldId
    );
    if ($intReturn4 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Process check command selection field
    if (isset($arrModifyData['check_command']) && ($arrModifyData['check_command'] != '')) {
        $arrCommand = explode('!', $arrModifyData['check_command']);
        $intFieldId = $arrCommand[0];
    } else {
        $intFieldId = 0;
    }
    $intReturn5 = $myVisClass->parseSelectSimple('tbl_command', 'command_name', 'hostcommand', 2, $intFieldId);
    if ($intReturn5 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Process check period selection field
    if (isset($arrModifyData['check_period'])) {
        $intFieldId = $arrModifyData['check_period'];
    } else {
        $intFieldId = 0;
    }
    $intReturn6 = $myVisClass->parseSelectSimple('tbl_timeperiod', 'timeperiod_name', 'checkperiod', 1, $intFieldId);
    if ($intReturn6 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (isset($arrModifyData['notification_period'])) {
        $intFieldId = $arrModifyData['notification_period'];
    } else {
        $intFieldId = 0;
    }
    $intReturn7 = $myVisClass->parseSelectSimple('tbl_timeperiod', 'timeperiod_name', 'notifyperiod', 1, $intFieldId);
    if ($intReturn7 != 0) {
        $myVisClass->processMessage(translate('Attention, no time periods defined!'), $strDBWarning);
        $intDataWarning = 1;
    }
    // Process event handler selection field
    if (isset($arrModifyData['event_handler'])) {
        $intFieldId = $arrModifyData['event_handler'];
    } else {
        $intFieldId = 0;
    }
    $intReturn8 = $myVisClass->parseSelectSimple('tbl_command', 'command_name', 'eventhandler', 1, $intFieldId);
    if ($intReturn8 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Process contact and contact group selection field
    if (isset($arrModifyData['contacts'])) {
        $intFieldId = $arrModifyData['contacts'];
    } else {
        $intFieldId = 0;
    }
    $intReturn9 = $myVisClass->parseSelectMulti(
        'tbl_contact',
        'contact_name',
        'host_contacts',
        'tbl_lnkHostToContact',
        2,
        $intFieldId
    );
    if ($intReturn9 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (isset($arrModifyData['contact_groups'])) {
        $intFieldId = $arrModifyData['contact_groups'];
    } else {
        $intFieldId = 0;
    }
    $intReturn10 = $myVisClass->parseSelectMulti(
        'tbl_contactgroup',
        'contactgroup_name',
        'host_contactgroups',
        'tbl_lnkHostToContactgroup',
        2,
        $intFieldId
    );
    if ($intReturn10 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    if (($intReturn8 != 0) && ($intReturn10 != 0)) {
        $myVisClass->processMessage(translate('Attention, no contact groups defined!'), $strDBWarning);
        $intDataWarning = 1;
    }
    // Process access group selection field
    if (isset($arrModifyData['access_group'])) {
        $intFieldId = $arrModifyData['access_group'];
    } else {
        $intFieldId = 0;
    }
    $intReturn11 = $myVisClass->parseSelectSimple('tbl_group', 'groupname', 'acc_group', 0, $intFieldId);
    if ($intReturn11 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Process service selection field
    if (isset($arrModifyData['contacts'])) {
        $intFieldId = $arrModifyData['id'];
    } else {
        $intFieldId = 0;
    }
    $intReturn12 = $myVisClass->parseSelectMulti(
        'tbl_service',
        'service_description',
        'host_services',
        'tbl_lnkServiceToHost',
        0,
        $intFieldId
    );
    if ($intReturn12 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    // Initial add/modify form definitions
    $strChbFields = 'ACE,PCE,FRE,OBS,EVH,FLE,STI,NSI,PED,NOE,PAR,HOG,COT,COG,TPL';
    $myContentClass->addFormInit($conttp, $strChbFields);
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
        $myContentClass->addInsertData($conttp, $arrModifyData, $intLocked, $strInfo, $strChbFields);
        $conttp->setVariable('DAT_ACE' .$arrModifyData['active_checks_enabled']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_PCE' .$arrModifyData['passive_checks_enabled']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_FRE' .$arrModifyData['check_freshness']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_OBS' .$arrModifyData['obsess_over_host']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_EVH' .$arrModifyData['event_handler_enabled']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_FLE' .$arrModifyData['flap_detection_enabled']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_STI' .$arrModifyData['retain_status_information']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_NSI' .$arrModifyData['retain_nonstatus_information']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_PED' .$arrModifyData['process_perf_data']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_NOE' .$arrModifyData['notifications_enabled']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_PAR' .$arrModifyData['parents_tploptions']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_HOG' .$arrModifyData['hostgroups_tploptions']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_COT' .$arrModifyData['contacts_tploptions']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_COG' .$arrModifyData['contact_groups_tploptions']. '_CHECKED', 'checked');
        $conttp->setVariable('DAT_TPL' .$arrModifyData['use_template_tploptions']. '_CHECKED', 'checked');
        // Special processing for -1 values - write 'null' to integer fields
        $strIntegerfelder  = 'max_check_attempts,check_interval,retry_interval,freshness_threshold,low_flap_threshold,'
                           . 'high_flap_threshold,';
        $strIntegerfelder .= 'notification_interval,first_notification_delay';
        foreach (explode(',', $strIntegerfelder) as $elem) {
            if ($arrModifyData[$elem] == -1) {
                $conttp->setVariable('DAT_' .strtoupper($elem), 'null');
            }
        }
        if ($arrModifyData['check_command'] != '') {
            $arrArgument = explode('!', $arrModifyData['check_command']);
            foreach ($arrArgument as $key => $value) {
                if ($key == 0) {
                    $conttp->setVariable('IFRAME_SRC', $_SESSION['SETS']['path']['base_url'].
                        'admin/commandline.php?cname=' .$value);
                } else {
                    $value1 = str_replace('::bang::', '!', $value);
                    $value2 = str_replace('::back::', "\\", $value1);
                    $conttp->setVariable('DAT_ARG' .$key, htmlentities($value2, ENT_QUOTES, 'UTF-8'));
                }
            }
        }
        // Process option fields
        foreach (explode(',', $arrModifyData['initial_state']) as $elem) {
            $conttp->setVariable('DAT_IS' .strtoupper($elem). '_CHECKED', 'checked');
        }
        foreach (explode(',', $arrModifyData['flap_detection_options']) as $elem) {
            $conttp->setVariable('DAT_FL' .strtoupper($elem). '_CHECKED', 'checked');
        }
        foreach (explode(',', $arrModifyData['notification_options']) as $elem) {
            $conttp->setVariable('DAT_NO' .strtoupper($elem). '_CHECKED', 'checked');
        }
        foreach (explode(',', $arrModifyData['stalking_options']) as $elem) {
            $conttp->setVariable('DAT_ST' .strtoupper($elem). '_CHECKED', 'checked');
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
    $mastertp->setVariable('TITLE', translate('Define hosts (hosts.cfg)'));
    $mastertp->setVariable('FIELD_1', translate('Host name'));
    $mastertp->setVariable('FIELD_2', translate('Description'));
    // Process filter string and filter
    $strSearchWhere = '';
    if ($_SESSION['search'][$preSearchSession] != '') {
        $strSearchTxt    = $_SESSION['search'][$preSearchSession];
        $strSearchWhere .= "AND (`$preKeyField` LIKE '%".$strSearchTxt."%' OR `alias` LIKE '%".$strSearchTxt."%' OR "
                        . "`display_name` LIKE '%".$strSearchTxt."%' OR `address` LIKE '%".$strSearchTxt."%') ";
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
    $strSQL     = "SELECT count(*) AS `number` FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere "
                . "AND `access_group` IN ($strAccess)";
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
    $strSQL     = "SELECT `id`, `$preKeyField`, `alias`, `register`, `active`, `last_modified`, `config_id`, "
                . "`access_group` FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere AND `access_group` "
                . "IN ($strAccess) $strOrderString LIMIT $chkLimit,".$SETS['common']['pagelines'];
    $booReturn2 = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
    if ($booReturn2 == false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    }
    // Process data
    $myContentClass->listData($mastertp, $arrDataLines, $intDataCount, $intLineCount, $preKeyField, 'alias', 0);
    if ($myContentClass->strErrorMessage != '') {
        $myVisClass->processMessage($myContentClass->strErrorMessage, $strErrorMessage);
    }
}
// Show messages
$arrTimeData       = array();
$strTimeInfoString = '';
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
