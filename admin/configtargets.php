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
// Component : Admin configuration target administration
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
$prePageId      = 36;
$preContent     = 'admin/configtargets.htm.tpl';
$preListTpl     = 'admin/datalist_common.htm.tpl';
$preTableName   = 'tbl_configtarget';
$preKeyField    = 'target';
$preAccess      = 1;
$preFieldvars   = 1;
$intIsError     = 0;
$strPathMessage = '';
//
// Include preprocessing files
// ===========================
require $preBasePath.'functions/prepend_adm.php';
require $preBasePath.'functions/prepend_content.php';
//
// Process path values (add slashes)
// =================================
$chkTfValue8  = $myVisClass->addSlash($chkTfValue8);
$chkTfValue9  = $myVisClass->addSlash($chkTfValue9);
$chkTfValue10 = $myVisClass->addSlash($chkTfValue10);
$chkTfValue11 = $myVisClass->addSlash($chkTfValue11);
$chkTfValue12 = $myVisClass->addSlash($chkTfValue12);
$chkTfValue13 = $myVisClass->addSlash($chkTfValue13);
$chkTfValue14 = $myVisClass->addSlash($chkTfValue14);
$chkTfValue15 = $myVisClass->addSlash($chkTfValue15);
$chkTfValue16 = $myVisClass->addSlash($chkTfValue16);
//
// Check Port Value
// ================
/** @noinspection UnnecessaryCastingInspection */
$chkTfValue23 = (int)$chkTfValue23;
if ($chkTfValue23 == 0) {
    $chkTfValue23 = 22;
}
//
// Check if the permissions and other parameters
// =============================================
if (($chkModus == 'modify' || $chkModus == 'insert') && $chkDataId != 0) {
    if ($chkSelValue1 == 1) {
        $arrPaths = array($chkTfValue8,$chkTfValue9,$chkTfValue10,$chkTfValue11,$chkTfValue12,$chkTfValue13);
        foreach ($arrPaths as $elem) {
            if ($myConfigClass->isDirWriteable($elem) == 1) {
                $myVisClass->processMessage($elem. ' ' .translate('is not writeable'), $strPathMessage);
                $intIsError = 1;
            }
        }
        // Nagios base configuration files
        if (!is_writable($chkTfValue20)) {
            $myVisClass->processMessage(str_replace('  ', ' ', translate('Nagios config file'). ' ' .$chkTfValue20
                    . ' ' .translate('is not writeable')), $strPathMessage);
            $intIsError = 1;
        } else {
            $intCheck = 0;
            if (file_exists($chkTfValue20) && is_readable($chkTfValue20)) {
                $resFile = fopen($chkTfValue20, 'rb');
                while (!feof($resFile)) {
                    $strLine = trim(fgets($resFile));
                    if ((substr_count($strLine, 'cfg_dir') != 0) || (substr_count($strLine, 'cfg_file') != 0)) {
                        $intCheck = 1;
                    }
                }
                fclose($resFile);
            }
            if ($intCheck == 0) {
                $myVisClass->processMessage(str_replace('  ', ' ', translate('Nagios config file'). ' ' .
                        $chkTfValue20. ' ' .translate('is not a valid configuration file!')), $strPathMessage);
                $intIsError = 1;
            }
        }
        if (!is_writable($chkTfValue14)) {
            $myVisClass->processMessage(str_replace('  ', ' ', translate('Nagios base directory'). ' ' .
                $chkTfValue14. ' ' .translate('is not writeable')), $strPathMessage);
            $intIsError = 1;
        }
        if (!is_writable($chkTfValue21)) {
            $myVisClass->processMessage(str_replace('  ', ' ', translate('Nagios cgi config file'). ' ' .
                    $chkTfValue21. ' ' .translate('is not writeable')), $strPathMessage);
            $intIsError = 1;
        }
        if (!is_readable($chkTfValue22)) {
            $myVisClass->processMessage(str_replace('  ', ' ', translate('Nagios resource config file'). ' ' .
                $chkTfValue22. ' ' .translate('is not readable')), $strPathMessage);
            $intIsError = 1;
        }
    }
    // Check SSH Method
    if (($chkSelValue1  == 3) && !function_exists('ssh2_connect')) {
        $myVisClass->processMessage(translate('SSH module not loaded!'), $strPathMessage);
        $intIsError = 1;
    }
    // Check FTP Method
    if (($chkSelValue1  == 2) && !function_exists('ftp_connect')) {
        $myVisClass->processMessage(translate('FTP module not loaded!'), $strPathMessage);
        $intIsError = 1;
    }
    if ($intIsError == 1) {
        $chkModus     = 'add';
        $chkSelModify = 'errormodify';
    }
}
//
// Add or modify data
// ==================
if ((($chkModus == 'insert') || ($chkModus == 'modify')) && ($intGlobalWriteAccess == 0)) {
    $strSQLx = "`$preTableName` SET `$preKeyField`='$chkTfValue1', `alias`='$chkTfValue2', `server`='$chkTfValue4', "
             . "`port`='$chkTfValue23', `method`='$chkSelValue1', `user`='$chkTfValue5', `password`='$chkTfValue6', "
             . "`ssh_key_path`='$chkTfValue7', `ftp_secure`=$chkChbValue1, `basedir`='$chkTfValue8', "
             . "`hostconfig`='$chkTfValue9', `serviceconfig`='$chkTfValue10', `backupdir`='$chkTfValue11', "
             . "`hostbackup`='$chkTfValue12', `servicebackup`='$chkTfValue13', `nagiosbasedir`='$chkTfValue14', "
             . "`importdir`='$chkTfValue15', `picturedir`='$chkTfValue16', `commandfile`='$chkTfValue17', "
             . "`binaryfile`='$chkTfValue18', `pidfile`='$chkTfValue19', `conffile`='$chkTfValue20', "
             . "`cgifile`='$chkTfValue21', `resourcefile`='$chkTfValue22',`version`=$chkSelValue2, "
             . "`access_group`=$chkSelAccGr, `active`='$chkActive',`last_modified`=NOW()";
    if ($chkModus == 'insert') {
        $strSQL = 'INSERT INTO ' .$strSQLx;
    } else {
        $strSQL = 'UPDATE ' .$strSQLx. ' WHERE `id`=' .$chkDataId;
    }
    if ($intWriteAccessId == 0) {
        if (($chkTfValue1 != '') && ($chkTfValue2 != '') && (($chkTfValue4 != '') || ($chkDataId == 0))) {
            $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
            if ($intReturn == 1) {
                $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
            } else {
                $myVisClass->processMessage($myDataClass->strInfoMessage, $strInfoMessage);
                if ($chkModus == 'insert') {
                    $myDataClass->writeLog(translate('New Domain inserted:'). ' ' .$chkTfValue1);
                }
                if ($chkModus == 'modify') {
                    $myDataClass->writeLog(translate('Domain modified:'). ' ' .$chkTfValue1);
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
// Single view
// ===========
if ($chkModus == 'add') {
    // Process acces group selection field
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
    $conttp->setVariable('TITLE', translate('Configuration domain administration'));
    if ($intIsError == 1) {
        $conttp->setVariable('PATHMESSAGE', '<h2 style="padding-bottom:5px;">' .translate('Warning, at least one ' .
                'error occured, please check!'). '</h2>' .$strPathMessage);
    }
    $conttp->setVariable('CLASS_NAME_1', 'elementHide');
    $conttp->setVariable('CLASS_NAME_2', 'elementHide');
    $conttp->setVariable('CLASS_NAME_3', 'elementHide');
    $conttp->setVariable('FILL_ALLFIELDS', translate('Please fill in all fields marked with an *'));
    $conttp->setVariable('FILL_ILLEGALCHARS', translate('The following field contains illegal characters:'));
    // Insert data from database in "modify" mode
    if (isset($arrModifyData) && ($chkSelModify == 'modify')) {
        // Process data
        $myContentClass->addInsertData($conttp, $arrModifyData, 0, '');
        // Connection method
        if ($arrModifyData['method'] == 1) {
            $conttp->setVariable('FILE_SELECTED', 'selected');
        }
        if ($arrModifyData['method'] == 2) {
            $conttp->setVariable('FTP_SELECTED', 'selected');
            $conttp->setVariable('CLASS_NAME_1', 'elementShow');
            $conttp->setVariable('CLASS_NAME_2', 'elementHide');
            $conttp->setVariable('CLASS_NAME_3', 'elementShow');
        }
        if ($arrModifyData['method'] == 3) {
            $conttp->setVariable('SFTP_SELECTED', 'selected');
            $conttp->setVariable('CLASS_NAME_1', 'elementShow');
            $conttp->setVariable('CLASS_NAME_2', 'elementShow');
            $conttp->setVariable('CLASS_NAME_3', 'elementHide');
        }
        if ($arrModifyData['ftp_secure'] == 1) {
            $conttp->setVariable('FTPS_CHECKED', 'checked');
        }
        // Nagios version
        $conttp->setVariable('VER_SELECTED_' .$arrModifyData['version'], 'selected');
        // Domain localhost cant' be renamed
        if ($arrModifyData[$preKeyField] == 'localhost') {
            $conttp->setVariable('DOMAIN_DISABLE', 'readonly');
            $conttp->setVariable('LOCKCLASS', 'inputlock');
        } elseif ($arrModifyData[$preKeyField] == 'common') {
            $conttp->setVariable('DOMAIN_DISABLE', 'readonly');
            $conttp->setVariable('COMMON_INVISIBLE', 'class="elementHide"');
            $conttp->setVariable('LOCKCLASS', 'inputlock');
        }
    }
    if ($chkSelModify == 'errormodify') {
        $conttp->setVariable('DAT_TARGET', $chkTfValue1);
        // Domain localhost cant' be renamed
        if ($chkTfValue1 == 'localhost') {
            $conttp->setVariable('DOMAIN_DISABLE', 'readonly');
            $conttp->setVariable('LOCKCLASS', 'inputlock');
        } elseif ($chkTfValue1 == 'common') {
            $conttp->setVariable('DOMAIN_DISABLE', 'readonly');
            $conttp->setVariable('COMMON_INVISIBLE', 'class="elementHide"');
            $conttp->setVariable('LOCKCLASS', 'inputlock');
        } else {
            $conttp->setVariable('LOCKCLASS', 'inpmust');
        }
        $conttp->setVariable('DAT_ALIAS', $chkTfValue2);
        $conttp->setVariable('DAT_SERVER', $chkTfValue4);
        // Connection method
        if ($chkSelValue1 == 1) {
            $conttp->setVariable('FILE_SELECTED', 'selected');
            $conttp->setVariable('CLASS_NAME_1', 'elementHide');
            $conttp->setVariable('CLASS_NAME_2', 'elementHide');
            $conttp->setVariable('CLASS_NAME_3', 'elementHide');
        }
        if ($chkSelValue1 == 2) {
            $conttp->setVariable('FTP_SELECTED', 'selected');
            $conttp->setVariable('CLASS_NAME_1', 'elementShow');
            $conttp->setVariable('CLASS_NAME_2', 'elementHide');
            $conttp->setVariable('CLASS_NAME_3', 'elementShow');
        }
        if ($chkSelValue1 == 3) {
            $conttp->setVariable('SFTP_SELECTED', 'selected');
            $conttp->setVariable('CLASS_NAME_1', 'elementShow');
            $conttp->setVariable('CLASS_NAME_2', 'elementShow');
            $conttp->setVariable('CLASS_NAME_3', 'elementHide');
        }
        $conttp->setVariable('DAT_USER', $chkTfValue5);
        $conttp->setVariable('DAT_SSH_KEY_PATH', $chkTfValue7);
        if ($chkChbValue1== 1) {
            $conttp->setVariable('FTPS_CHECKED', 'checked');
        }
        $conttp->setVariable('DAT_BASEDIR', $chkTfValue8);
        $conttp->setVariable('DAT_HOSTCONFIG', $chkTfValue9);
        $conttp->setVariable('DAT_SERVICECONFIG', $chkTfValue10);
        $conttp->setVariable('DAT_BACKUPDIR', $chkTfValue11);
        $conttp->setVariable('DAT_HOSTBACKUP', $chkTfValue12);
        $conttp->setVariable('DAT_SERVICEBACKUP', $chkTfValue13);
        $conttp->setVariable('DAT_NAGIOSBASEDIR', $chkTfValue14);
        $conttp->setVariable('DAT_IMPORTDIR', $chkTfValue15);
        $conttp->setVariable('DAT_COMMANDFILE', $chkTfValue17);
        $conttp->setVariable('DAT_BINARYFILE', $chkTfValue18);
        $conttp->setVariable('DAT_PIDFILE', $chkTfValue19);
        $conttp->setVariable('DAT_CONFFILE', $chkTfValue20);
        $conttp->setVariable('DAT_CGIFILE', $chkTfValue21);
        $conttp->setVariable('DAT_RESOURCEFILE', $chkTfValue22);
        $conttp->setVariable('DAT_PICTUREDIR', $chkTfValue16);
        // NagiosQL version
        if ($chkSelValue2 == 1) {
            $conttp->setVariable('VER_SELECTED_1', 'selected');
        }
        if ($chkSelValue2 == 2) {
            $conttp->setVariable('VER_SELECTED_2', 'selected');
        }
        if ($chkSelValue2 == 3) {
            $conttp->setVariable('VER_SELECTED_3', 'selected');
        }
        // Hidden variables
        $conttp->setVariable('MODUS', filter_input(INPUT_POST, 'modus', FILTER_SANITIZE_STRING));
        $conttp->setVariable('DAT_ID', filter_input(INPUT_POST, 'hidId', FILTER_VALIDATE_INT));
        $conttp->setVariable('LIMIT', filter_input(INPUT_POST, 'hidLimit', FILTER_VALIDATE_INT));
        // Active
        if (filter_input(INPUT_POST, 'chbActive')) {
            $conttp->setVariable('ACT_CHECKED', 'checked');
        } else {
            $conttp->setVariable('ACT_CHECKED', '');
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
    $mastertp->setVariable('TITLE', translate('Configuration domain administration'));
    $mastertp->setVariable('FIELD_1', translate('Configuration target'));
    $mastertp->setVariable('FIELD_2', translate('Description'));
    // Row sorting
    $strOrderString = "ORDER BY `$preKeyField` $hidSortDir";
    if ($hidSortBy == 2) {
        $strOrderString = "ORDER BY `alias` $hidSortDir";
    }
    // Count datasets
    $strSQL     = "SELECT count(*) AS `number` FROM `$preTableName` WHERE `access_group` IN ($strAccess)";
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
    $strSQL     = "SELECT `id`, `$preKeyField`, `alias`, `active`, `nodelete`, `access_group` "
                . "FROM `$preTableName` WHERE `access_group` IN ($strAccess) $strOrderString "
                . "LIMIT $chkLimit,".$SETS['common']['pagelines'];
    $booReturn2 = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
    if ($booReturn2 == false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    }
    // Process data
    $myContentClass->listData($mastertp, $arrDataLines, $intDataCount, $intLineCount, $preKeyField, 'alias');
}
// Show messages
$myContentClass->showMessages($mastertp, $strErrorMessage, $strInfoMessage, $strConsistMessage, array(), '', 1);
//
// Process footer
// ==============
$myContentClass->showFooter($maintp, $setFileVersion);
