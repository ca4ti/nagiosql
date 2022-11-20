<?php
/* ----------------------------------------------------------------------------
 NagiosQL
-------------------------------------------------------------------------------
 (c) 2005-2022 by Martin Willisegger

 Project   : NagiosQL
 Component : Check command definitions
 Website   : https://sourceforge.net/projects/nagiosql/
 Version   : 3.5.0
 GIT Repo  : https://gitlab.com/wizonet/NagiosQL
-----------------------------------------------------------------------------*/

use functions\MysqliDbClass;
use functions\NagConfigClass;
use functions\NagContentClass;
use functions\NagDataClass;
use functions\NagVisualClass;

/**
 * Class and variable includes
 * @var HTML_Template_IT $conttp
 * @var HTML_Template_IT $maintp
 * @var HTML_Template_IT $mastertp
 * @var NagVisualClass $myVisClass
 * @var MysqliDbClass $myDBClass
 * @var NagDataClass $myDataClass
 * @var NagConfigClass $myConfigClass
 * @var NagContentClass $myContentClass
 * @var string $setFileVersion from prepend_adm.php
 * @var string $chkModus from prepend_adm.php
 * @var int $chkDataId from prepend_adm.php
 * @var int $intGlobalWriteAccess from prepend_content.php
 * @var int $intWriteAccessId from prepend_content.php
 * @var string $preSQLCommon1 from prepend_content.php
 * @var string $chkTfValue1 from prepend_content.php
 * @var string $chkTfSpValue1 from prepend_content.php
 * @var int $chkSelValue1 from prepend_content.php
 * @var string $chkTaArg1Info from prepend_content.php
 * @var string $chkTaArg2Info from prepend_content.php
 * @var string $chkTaArg3Info from prepend_content.php
 * @var string $chkTaArg4Info from prepend_content.php
 * @var string $chkTaArg5Info from prepend_content.php
 * @var string $chkTaArg6Info from prepend_content.php
 * @var string $chkTaArg7Info from prepend_content.php
 * @var string $chkTaArg8Info from prepend_content.php
 * @var int $intDataWarning from prepend_content.php
 * @var string $strDBWarning from ? TODO
 * @var int $intVersion from prepend_adm.php
 * @var string $chkSelModify from prepend_adm.php
 * @var string $hidSortDir from prepend_adm.php
 * @var int $hidSortBy from prepend_adm.php
 * @var string $strDomainWhere from prepend_adm.php
 * @var string $strAccess from prepend_content.php
 * @var int $chkLimit from prepend_adm.php
 * @var array $SETS from prepend_adm.php
 * @var string $strConsistMessage from prepend_adm.php
 */
/*
Path settings
*/
$strPattern = '(admin/[^/]*.php)';
$preRelPath = preg_replace($strPattern, '', filter_input(INPUT_SERVER, 'PHP_SELF'));
$preBasePath = preg_replace($strPattern, '', filter_input(INPUT_SERVER, 'SCRIPT_FILENAME'));
/*
Define common variables
*/
$prePageId = 18;
$preContent = 'admin/checkcommands.htm.tpl';
$preListTpl = 'admin/datalist.htm.tpl';
$preSearchSession = 'checkcommand';
$preTableName = 'tbl_command';
$preKeyField = 'command_name';
$preAccess = 1;
$preFieldvars = 1;
$strErrorMessage = '';
$strInfoMessage = '';
/*
Include preprocessing file
*/
require $preBasePath . 'functions/prepend_adm.php';
require $preBasePath . 'functions/prepend_content.php';
/*
Add or modify data
*/
if ((($chkModus === 'insert') || ($chkModus === 'modify')) && ($intGlobalWriteAccess === 0)) {
    $strSQLx = "`$preTableName` SET `$preKeyField`='$chkTfValue1', `command_line`='$chkTfSpValue1', "
        . "`command_type`=$chkSelValue1, $preSQLCommon1, `arg1_info`='$chkTaArg1Info', "
        . "`arg2_info`='$chkTaArg2Info', `arg3_info`='$chkTaArg3Info', `arg4_info`='$chkTaArg4Info', "
        . "`arg5_info`='$chkTaArg5Info', `arg6_info`='$chkTaArg6Info', `arg7_info`='$chkTaArg7Info', "
        . "`arg8_info`='$chkTaArg8Info'";
    if ($chkModus === 'insert') {
        $strSQL = 'INSERT INTO ' . $strSQLx;
    } else {
        $strSQL = 'UPDATE ' . $strSQLx . ' WHERE `id`=' . $chkDataId;
    }
    if ($intWriteAccessId === 0) {
        if (($chkTfValue1 !== '') && ($chkTfSpValue1 !== '')) {
            $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
            if ($chkModus === 'insert') {
                $chkDataId = $intInsertId;
            }
            if ($intReturn === 1) {
                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
            } else {
                $myVisClass->processMessage($myDataClass->strInfoMessage, $strInfoMessage);
                $myDataClass->updateStatusTable($preTableName);
                if ($chkModus === 'insert') {
                    $myDataClass->writeLog(translate('New command inserted:') . ' ' . $chkTfValue1);
                }
                if ($chkModus === 'modify') {
                    $myDataClass->writeLog(translate('Command modified:') . ' ' . $chkTfValue1);
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
if ($chkModus !== 'add') {
    $chkModus = 'display';
}
/*
Get date/time of last database and config file manipulation
*/
$intReturn = $myConfigClass->lastModifiedFile($preTableName, $arrTimeData, $strTimeInfoString);
if ($intReturn !== 0) {
    $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
}
/*
Singe data form
*/
$intNoTime = 0;
if ($chkModus === 'add') {
    /* Do not show modified time list */
    $intNoTime = 1;
    /* Process access group selection field */
    $intFieldId = $arrModifyData['access_group'] ?? 0;
    $intReturn = $myVisClass->parseSelectSimple('tbl_group', 'groupname', 'acc_group', 0, $intFieldId);
    if ($intReturn !== 0) {
        $myVisClass->processMessage($myVisClass->strErrorMessage, $strErrorMessage);
    }
    /* Initial add/modify form definitions */
    $myContentClass->addFormInit($conttp);
    if ($intDataWarning === 1) {
        $conttp->setVariable('WARNING', $strDBWarning . '<br>' . translate('Saving not possible!'));
    }
    if ($intVersion < 3) {
        $conttp->setVariable('VERSION_20_VALUE_MUST', 'mselValue1,');
    }
    $conttp->setVariable('TITLE', translate('Command definitions'));
    $conttp->setVariable('NO_TYPE', translate('unclassified'));
    $conttp->setVariable('CHECK_TYPE', translate('check command'));
    $conttp->setVariable('MISC_TYPE', translate('misc command'));
    /* Insert data from database in "modify" mode */
    if (isset($arrModifyData) && ($chkSelModify === 'modify')) {
        /* Check relation information to find out locked configuration datasets */
        $intLocked = $myDataClass->infoRelation($preTableName, $arrModifyData['id'], $preKeyField);
        $myVisClass->processMessage($myDataClass->strInfoMessage, $strRelMessage);
        $strInfo = '<br><span class="redmessage">' . translate('Entry cannot be activated because it is used by '
                . 'another configuration') . ':</span>';
        $strInfo .= '<br><span class="greenmessage">' . $strRelMessage . '</span>';
        /* Process data */
        $myContentClass->addInsertData($conttp, $arrModifyData, $intLocked, $strInfo);
        /* Insert command type */
        if ((int)$arrModifyData['command_type'] === 1) {
            $conttp->setVariable('CHECK_TYPE_SELECTED', 'selected');
        }
        if ((int)$arrModifyData['command_type'] === 2) {
            $conttp->setVariable('MISC_TYPE_SELECTED', 'selected');
        }
    }
    $conttp->parse('datainsert');
    $conttp->show('datainsert');
}
/*
List view
*/
if ($chkModus === 'display') {
    /* Initial list view definitions */
    $myContentClass->listViewInit($mastertp);
    $mastertp->setVariable('TITLE', translate('Command definitions'));
    $mastertp->setVariable('FIELD_1', translate('Command name'));
    $mastertp->setVariable('FIELD_2', translate('Command line'));
    $mastertp->setVariable('FILTER_VISIBLE', 'visibility: hidden');
    /* Process search string */
    $strSearchWhere = '';
    if ($_SESSION['search'][$preSearchSession] !== '') {
        $strSearchTxt = $_SESSION['search'][$preSearchSession];
        $strSearchWhere = "AND (`$preKeyField` LIKE '%" . $strSearchTxt . "%' "
            . "OR `command_line` LIKE '%" . $strSearchTxt . "%')";
    }
    /* Row sorting */
    $strOrderString = "ORDER BY `config_id`, `$preKeyField` $hidSortDir";
    if ($hidSortBy === 2) {
        $strOrderString = "ORDER BY `config_id`, `command_line` $hidSortDir";
    }
    /* Count datasets */
    $intLineCount = 0;
    /** @noinspection SqlResolve */
    $strSQL = "SELECT count(*) AS `number` FROM `$preTableName` "
        . "WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
    $booReturn1 = $myDBClass->hasSingleDataset($strSQL, $arrDataLinesCount);
    if ($booReturn1 === false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    } else {
        $intLineCount = (int)$arrDataLinesCount['number'];
        if ($intLineCount < $chkLimit) {
            $chkLimit = 0;
        }
    }
    /* Get datasets */
    $strSQL     = "SELECT `id`, `$preKeyField`, `command_line`, `register`, `active`, `config_id`, `access_group` "
                . "FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere "
                . "AND `access_group` IN ($strAccess) $strOrderString LIMIT $chkLimit,".$SETS['common']['pagelines'];
    $booReturn2 = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
    if ($booReturn2 === false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    }
    /* Process data */
    $myContentClass->listData($mastertp, $arrDataLines, $intDataCount, $intLineCount, $preKeyField, 'command_line', 40);
}
/* Show messages */
$myContentClass->showMessages(
    $mastertp,
    $strErrorMessage,
    $strInfoMessage,
    $strConsistMessage,
    $arrTimeData,
    $strTimeInfoString,
    $intNoTime
);
/*
Process footer
 */
$myContentClass->showFooter($maintp, $setFileVersion);