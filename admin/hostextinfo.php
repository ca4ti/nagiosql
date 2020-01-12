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
// Component : Host extended information definition
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
$prePageId        = 21;
$preContent       = 'admin/hostextinfo.htm.tpl';
$preListTpl       = 'admin/datalist.htm.tpl';
$preSearchSession = 'hostextinfo';
$preTableName     = 'tbl_hostextinfo';
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
    $strSQLx = "`$preTableName` SET `$preKeyField`=$chkSelValue1, `notes`='$chkTfValue1', `notes_url`='$chkTfValue2', "
        . "`action_url`='$chkTfValue3', `icon_image`='$chkTfValue4', `icon_image_alt`='$chkTfValue5', "
        . "`vrml_image`='$chkTfValue6', `statusmap_image`='$chkTfValue7', `2d_coords`='$chkTfValue8', "
        . "`3d_coords`='$chkTfValue9', $preSQLCommon1";
    if ($chkModus == 'insert') {
        $strSQL = 'INSERT INTO ' .$strSQLx;
    } else {
        $strSQL = 'UPDATE ' .$strSQLx. ' WHERE `id`=' .$chkDataId;
    }
    if ($intWriteAccessId == 0) {
        if ($chkSelValue1 != 0) {
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
                    $myDataClass->writeLog(translate('New host extended information inserted:'). ' ' .$chkSelValue1);
                }
                if ($chkModus == 'modify') {
                    $myDataClass->writeLog(translate('Host extended information modified:'). ' ' .$chkSelValue1);
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
    $conttp->setVariable('TITLE', translate('Define host extended information (hostextinfo.cfg)'));
    // Do not show modified time list
    $intNoTime = 1;
    if (isset($arrModifyData[$preKeyField])) {
        $intFieldId = $arrModifyData[$preKeyField];
    } else {
        $intFieldId = 0;
    }
    $intReturn1 = $myVisClass->parseSelectSimple('tbl_host', $preKeyField, 'host', 0, $intFieldId);
    if ($intReturn1 != 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
        $myVisClass->processMessage(translate('Attention, no hosts defined!'), $strDBWarning);
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
    $mastertp->setVariable('TITLE', translate('Define host extended information (hostextinfo.cfg)'));
    $mastertp->setVariable('FIELD_1', translate('Host name'));
    $mastertp->setVariable('FIELD_2', translate('Notes'));
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
        $strOrderString = "ORDER BY `$preTableName`.`config_id`, `notes` $hidSortDir";
    }
    // Count datasets
    $strSQL    = "SELECT count(*) AS `number` FROM `$preTableName` "
        . "LEFT JOIN `tbl_host` ON `$preTableName`.`$preKeyField`=`tbl_host`.`id` "
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
    $strSQL    = "SELECT `$preTableName`.`id`, `tbl_host`.`$preKeyField`, `$preTableName`.`notes`, "
        . "`$preTableName`.`register`, `$preTableName`.`active`, `$preTableName`.`config_id`, "
        . "`$preTableName`.`access_group` FROM `$preTableName` "
        . "LEFT JOIN `tbl_host` ON `$preTableName`.`$preKeyField` = `tbl_host`.`id` "
        . "WHERE $strDomainWhere $strSearchWhere AND `$preTableName`.`access_group` IN ($strAccess) $strOrderString "
        . "LIMIT $chkLimit,".$SETS['common']['pagelines'];
    $booReturn = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
    if ($booReturn == false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    }
    $myContentClass->listData($mastertp, $arrDataLines, $intDataCount, $intLineCount, $preKeyField, 'notes');
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
