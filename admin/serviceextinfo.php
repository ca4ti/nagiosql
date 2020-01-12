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
// Component : Service extended information definition
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
$prePageId        = 24;
$preContent       = 'admin/serviceextinfo.htm.tpl';
$preListTpl       = 'admin/datalist.htm.tpl';
$preSearchSession = 'serviceextinfo';
$preTableName     = 'tbl_serviceextinfo';
$preKeyField      = 'host_name';
$preAccess        = 1;
$preFieldvars     = 1;
//
// Include preprocessing files
// ===========================
require $preBasePath.'functions/prepend_adm.php';
require $preBasePath.'functions/prepend_content.php';
//
// Add or modify data
// ==================
if ((($chkModus == 'insert') || ($chkModus == 'modify')) && ($intGlobalWriteAccess == 0)) {
    $strSQLx = "`$preTableName` SET `$preKeyField`='$chkSelValue1', `service_description`='$chkSelValue2', "
        . "`notes`='$chkTfValue1', `notes_url`='$chkTfValue2', `action_url`='$chkTfValue3', "
        . "`icon_image`='$chkTfValue4', `icon_image_alt`='$chkTfValue5', $preSQLCommon1";
    if ($chkModus == 'insert') {
        $strSQL = 'INSERT INTO ' .$strSQLx;
    } else {
        $strSQL = 'UPDATE ' .$strSQLx. ' WHERE `id`=' .$chkDataId;
    }
    if ($intWriteAccessId == 0) {
        if (($chkSelValue1 != 0) && ($chkSelValue2 != 0)) {
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
                    $myDataClass->writeLog(translate('New service extended information inserted:'). ' ' .$chkSelValue1.
                        '::' .$chkSelValue2);
                }
                if ($chkModus == 'modify') {
                    $myDataClass->writeLog(translate('Service extended information modified:'). ' ' .$chkSelValue1.
                        '::' .$chkSelValue2);
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
    $conttp->setVariable('TITLE', translate('Define service extended information (serviceextinfo.cfg)'));
    // Do not show modified time list
    $intNoTime = 1;
    // Refresh mode
    if ($chkModus == 'refresh') {
        $_SESSION['refresh']['se_host'] = $chkSelValue1;
        $myVisClass->arrSession = $_SESSION;
    } else {
        $_SESSION['refresh']['se_host'] = $chkSelValue1;
        if (isset($arrModifyData[$preKeyField]) && ($arrModifyData[$preKeyField] != 0)) {
            $strSQL     = "SELECT `$preKeyField` FROM `$preTableName` WHERE `id` = ".$arrModifyData['id'];
            $booReturn  = $myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($intDC != 0) {
                $_SESSION['refresh']['se_host'] = $arrData[0][$preKeyField];
            }
        } else {
            $strSQL    = 'SELECT `id` FROM `tbl_host` '
                . "WHERE `active`='1' AND `config_id`=$chkDomainId ORDER BY `$preKeyField`";
            $booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($intDC != 0) {
                $_SESSION['refresh']['se_host'] = $arrData[0]['id'];
            }
        }
        $myVisClass->arrSession = $_SESSION;
    }
    // Process host selection field
    if (isset($arrModifyData[$preKeyField])) {
        $intFieldId = $arrModifyData[$preKeyField];
    } else {
        $intFieldId = 0;
    }
    if (($chkModus == 'refresh') && ($chkSelValue1 != 0)) {
        $intFieldId = $chkSelValue1;
    }
    $intReturn1 = $myVisClass->parseSelectSimple('tbl_host', $preKeyField, 'host', 0, $intFieldId);
    if ($intReturn1 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
        $myVisClass->processMessage(translate('Attention, no hosts defined!'), $strDBWarning);
        $intDataWarning = 1;
    }
    // Process service selection field
    if (isset($arrModifyData['service_description'])) {
        $intFieldId = $arrModifyData['service_description'];
    } else {
        $intFieldId = 0;
    }
    $intReturn1 = $myVisClass->parseSelectSimple(
        'tbl_service',
        'service_description',
        'service_extinfo',
        0,
        $intFieldId
    );
    if ($intReturn1 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
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
    if ($intVersion < 3) {
        $conttp->setVariable('VERSION_20_VALUE_MUST', 'mselValue1,');
    }
    if ($chkModus == 'refresh') {
        $conttp->setVariable('DAT_NOTES', $chkTfValue1);
        $conttp->setVariable('DAT_NOTES_URL', $chkTfValue2);
        $conttp->setVariable('DAT_ACTION_URL', $chkTfValue3);
        $conttp->setVariable('DAT_ICON_IMAGE', $chkTfValue4);
        $conttp->setVariable('DAT_ICON_IMAGE_ALT', $chkTfValue5);
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
    $mastertp->setVariable('TITLE', translate('Define service extended information (serviceextinfo.cfg)'));
    $mastertp->setVariable('FIELD_1', translate('Hostname'));
    $mastertp->setVariable('FIELD_2', translate('Service'));
    $mastertp->setVariable('FILTER_VISIBLE', 'visibility: hidden');
    // Process search string
    if ($_SESSION['search'][$preSearchSession] != '') {
        $strSearchTxt   = $_SESSION['search'][$preSearchSession];
        $strSearchWhere = "AND (`tbl_host`.`$preKeyField` LIKE '%".$strSearchTxt."%' OR `$preTableName`.`notes` "
            . "LIKE '%".$strSearchTxt."%' OR `$preTableName`.`notes_url` LIKE '%".$strSearchTxt."%')";
    }
    // Row sorting
    $strOrderString = "ORDER BY `$preTableName`.`config_id`, `$preKeyField` $hidSortDir";
    if ($hidSortBy == 2) {
        $strOrderString = "ORDER BY `$preTableName`.`config_id`, `tbl_service`.`service_description` $hidSortDir";
    }
    // Count datasets
    $strSQL    = "SELECT count(*) AS `number` FROM `$preTableName` "
        . "LEFT JOIN `tbl_host` ON `$preTableName`.`$preKeyField` = `tbl_host`.`id` "
        . "LEFT JOIN `tbl_service` ON `$preTableName`.`service_description` = `tbl_service`.`id` "
        . "WHERE $strDomainWhere $strSearchWhere AND `$preTableName`.`access_group` IN ($strAccess)";
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
    $strSQL    = "SELECT `$preTableName`.`id`, `tbl_host`.`$preKeyField`, `tbl_service`.`service_description`, "
        . "`$preTableName`.`notes`, `$preTableName`.`register`, `$preTableName`.`active`, `$preTableName`.`config_id`, "
        . "`$preTableName`.`access_group` FROM `$preTableName` "
        . "LEFT JOIN `tbl_host` ON `$preTableName`.`$preKeyField` = `tbl_host`.`id` "
        . "LEFT JOIN `tbl_service` ON `$preTableName`.`service_description` = `tbl_service`.`id` "
        . "WHERE $strDomainWhere $strSearchWhere AND `$preTableName`.`access_group` IN ($strAccess) $strOrderString "
        . "LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
        'service_description'
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
