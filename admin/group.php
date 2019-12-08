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
// Component : Group administration
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
$prePageId        = 33;
$preContent       = 'admin/group.htm.tpl';
$preListTpl       = 'admin/datalist_common.htm.tpl';
$preSearchSession = 'group';
$preTableName     = 'tbl_group';
$preKeyField      = 'groupname';
$preAccess        = 1;
$preFieldvars     = 1;
$preNoAccessGrp   = 1;
$arrDataLines     = array();
//
// Include preprocessing files
// ===========================
require $preBasePath.'functions/prepend_adm.php';
require $preBasePath.'functions/prepend_content.php';
//
// Add or modify data
// ==================
if (($chkModus == 'insert') || ($chkModus == 'modify')) {
    $strSQLx = "`$preTableName` SET `groupname`='$chkTfValue1', `description`='$chkTfValue2', `active`='$chkActive', "
             . '`last_modified`=NOW()';
    if ($chkModus == 'insert') {
        $strSQL = 'INSERT INTO ' .$strSQLx;
    } else {
        $strSQL = 'UPDATE ' .$strSQLx. ' WHERE `id`=' .$chkDataId;
    }
    if ($intWriteAccessId == 0) {
        if (($chkTfValue1 != '') && ($chkTfValue2 != '')) {
            $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
            if ($chkModus == 'insert') {
                $chkDataId = $intInsertId;
            }
            if ($intReturn == 1) {
                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
            } else {
                $myVisClass->processMessage($myDataClass->strInfoMessage, $strInfoMessage);
                if ($chkModus == 'insert') {
                    $myDataClass->writeLog(translate('A new group added:'). ' ' .$chkTfValue1);
                }
                if ($chkModus == 'modify') {
                    $myDataClass->writeLog(translate('User modified:'). ' ' .$chkTfValue1);
                }
                //
                // Insert/update user/group data from session data
                // ===============================================
                if ($chkModus == 'modify') {
                    $strSQL    = "DELETE FROM `tbl_lnkGroupToUser` WHERE `idMaster`=$chkDataId";
                    $intReturn  = $myDataClass->dataInsert($strSQL, $intInsertId);
                    if ($intReturn != 0) {
                        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
                    }
                }
                if (isset($_SESSION['groupuser']) && is_array($_SESSION['groupuser']) &&
                    (count($_SESSION['groupuser']) != 0)) {
                    foreach ($_SESSION['groupuser'] as $elem) {
                        if ($elem['status'] == 0) {
                            $intRead  = 0;
                            $intWrite = 0;
                            $intLink  = 0;
                            if (substr_count($elem['rights'], 'READ')  != 0) {
                                $intRead  = 1;
                            }
                            if (substr_count($elem['rights'], 'WRITE') != 0) {
                                $intWrite = 1;
                            }
                            if (substr_count($elem['rights'], 'LINK')  != 0) {
                                $intLink  = 1;
                            }
                            if ($intWrite == 1) {
                                $intRead = 1;
                                $intLink = 1;
                            }
                            if ($intRead  == 1) {
                                $intLink = 1;
                            }
                            // if ($intLink  == 1) $intRead = 1;
                            $strSQL    = 'INSERT INTO `tbl_lnkGroupToUser` (`idMaster`,`idSlave`,`read`,`write`,'
                                       . "`link`) VALUES ($chkDataId,".$elem['user'].",'$intRead','$intWrite',"
                                       . "'$intLink')";
                            $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
                            if ($intReturn != 0) {
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
    $chkModus  = 'display';
}
//
// Singe data form
// ===============
if ($chkModus == 'add') {
    // Process data fields
    $strSQL    = 'SELECT * FROM `tbl_user` WHERE `id`<>1 ORDER BY `username`';
    $booReturn = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
    $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    if ($booReturn && ($intDataCount != 0)) {
        foreach ($arrDataLines as $elem) {
            $conttp->setVariable('DAT_USER_ID', $elem['id']);
            $conttp->setVariable('DAT_USER', $elem['username']);
            $conttp->parse('users');
        }
    }
    // Initial add/modify form definitions
    $myContentClass->addFormInit($conttp);
    $conttp->setVariable('TITLE', translate('Group administration'));
    $conttp->setVariable('LANG_READ', translate('Read'));
    $conttp->setVariable('LANG_WRITE', translate('Write'));
    $conttp->setVariable('LANG_LINK', translate('Link'));
    $conttp->setVariable('DAT_ID', $chkListId);
    $conttp->setVariable('FILL_ALLFIELDS', translate('Please fill in all fields marked with an *'));
    $conttp->setVariable('FILL_ILLEGALCHARS', translate('The following field contains illegal characters:'));
    // Insert data from database in "modify" mode
    if (isset($arrModifyData) && ($chkSelModify == 'modify')) {
        // Process data
        $myContentClass->addInsertData($conttp, $arrModifyData, 0, '');
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
    $mastertp->setVariable('TITLE', translate('Group administration'));
    $mastertp->setVariable('FIELD_1', translate('Groupname'));
    $mastertp->setVariable('FIELD_2', translate('Description'));
    // Row sorting
    $strOrderString = "ORDER BY `groupname` $hidSortDir";
    if ($hidSortBy == 2) {
        $strOrderString = "ORDER BY `description` $hidSortDir";
    }
    // Count datasets
    $strSQL     = "SELECT count(*) AS `number` FROM `$preTableName`";
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
    $strSQL     = 'SELECT `id`, `groupname`, `description`, `active` '
                . "FROM `$preTableName` $strOrderString LIMIT $chkLimit,".$SETS['common']['pagelines'];
    $booReturn2 = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
    if ($booReturn2 == false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    }
    // Process data
    $myContentClass->listData($mastertp, $arrDataLines, $intDataCount, $intLineCount, $preKeyField, 'description');
}
// Show messages
$myContentClass->showMessages($mastertp, $strErrorMessage, $strInfoMessage, $strConsistMessage, array(), '', 1);
//
// Process footer
// ==============
$myContentClass->showFooter($maintp, $setFileVersion);
