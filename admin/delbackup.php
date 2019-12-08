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
// Component : Admin file deletion
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
$prePageId    = 26;
$preContent   = 'admin/delbackup.htm.tpl';
$preAccess    = 1;
$preFieldvars = 1;
//
// Include preprocessing files
// ===========================
require $preBasePath.'functions/prepend_adm.php';
require $preBasePath.'functions/prepend_content.php';
//
// Get configuration set ID
// ========================
$myConfigClass->getConfigTargets($arrConfigSet);
$intConfigId  = $arrConfigSet[0];
$myConfigClass->getConfigValues($intConfigId, 'method', $intMethod);
$myConfigClass->getConfigValues($intConfigId, 'backupdir', $strBackupDir);
$myConfigClass->getConfigValues($intConfigId, 'hostbackup', $strHostBackupDir);
$myConfigClass->getConfigValues($intConfigId, 'servicebackup', $strServiceBackupDir);
//
// Process form inputs
// ===================
if (($chkMselValue1[0] != '') && ($chkStatus == 1)) {
    /** @var array $chkMselValue1 */
    foreach ($chkMselValue1 as $elem) {
        $intCheck = $myConfigClass->removeFile(trim($elem), $intConfigId);
        $strFileTmp1 = str_replace($strServiceBackupDir, '', $elem);
        $strFileTmp2 = str_replace($strHostBackupDir, '', $strFileTmp1);
        $strFile     = str_replace($strBackupDir, '', $strFileTmp2);
        if ($intCheck == 0) {
            $myDataClass->writeLog(translate('File deleted'). ': ' .trim($strFile));
            $myVisClass->processMessage($strFile. ' ' .translate('successfully deleted'). '!', $strInfoMessage);
        } else {
            $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
        }
    }
}
//
// Include content
// ===============
$conttp->setVariable('TITLE', translate('Delete backup files'));
$conttp->parse('header');
$conttp->show('header');
$conttp->setVariable('LANG_SEARCH_STRING', translate('Filter string'));
$conttp->setVariable('LANG_SEARCH', translate('Search'));
$conttp->setVariable('LANG_DELETE', translate('Delete'));
$conttp->setVariable('LANG_DELETE_SEARCH', translate('Reset filter'));
$conttp->setVariable('DAT_SEARCH', $chkTfSearch);
$conttp->setVariable('BACKUPFILE', translate('Backup file'));
$conttp->setVariable('LANG_REQUIRED', translate('required'));
$conttp->setVariable('MAKE', translate('Delete'));
$conttp->setVariable('ABORT', translate('Abort'));
$conttp->setVariable('CTRL_INFO', translate('Hold CTRL to select<br>more than one entry'));
$conttp->setVariable('IMAGE_PATH', $_SESSION['SETS']['path']['base_url']. 'images/');
$conttp->setVariable('ACTION_INSERT', filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING));
// Build a local file list
if ($intMethod == 1) {
    $output = array();
    $myConfigClass->storeDirToArray($strBackupDir, "\.cfg_old", '', $output, $strErrorMessage);
    if (is_array($output) && (count($output) != 0)) {
        foreach ($output as $elem) {
            if (($chkTfSearch == '') || (substr_count($elem, $chkTfSearch) != 0)) {
                $conttp->setVariable('DAT_BACKUPFILE', $elem);
                $conttp->parse('filelist');
            }
        }
    }
} elseif ($intMethod == 2) {
    // Set up basic connection
    if ($myConfigClass->getFTPConnection($intConfigId) == '0') {
        $arrFiles  = array();
        $arrFiles1 = ftp_nlist($myConfigClass->resConnectId, $strBackupDir);
        if (is_array($arrFiles1)) {
            $arrFiles = array_merge($arrFiles, $arrFiles1);
        }
        $arrFiles2 = ftp_nlist($myConfigClass->resConnectId, $strHostBackupDir);
        if (is_array($arrFiles2)) {
            $arrFiles = array_merge($arrFiles, $arrFiles2);
        }
        $arrFiles3 = ftp_nlist($myConfigClass->resConnectId, $strServiceBackupDir);
        if (is_array($arrFiles3)) {
            $arrFiles = array_merge($arrFiles, $arrFiles3);
        }
        if (is_array($arrFiles) && (count($arrFiles) != 0)) {
            foreach ($arrFiles as $elem) {
                if (!substr_count($elem, 'cfg')) {
                    continue;
                }
                if (($chkTfSearch == '') || (substr_count($elem, $chkTfSearch) != 0)) {
                    $conttp->setVariable('DAT_BACKUPFILE', $elem);
                    $conttp->parse('filelist');
                }
            }
        } else {
            $myVisClass->processMessage(
                translate('No backup files or no permission to read the backup files'),
                $strErrorMessage
            );
        }
        ftp_close($myConfigClass->resConnectId);
    } else {
        $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
    }
} elseif ($intMethod == 3) {
    // Set up basic connection
    if ($myConfigClass->getSSHConnection($intConfigId) == '0') {
        $arrFiles  = array();
        $intReturn = $myConfigClass->sendSSHCommand('ls ' .$strBackupDir. '*.cfg_old*', $arrFiles1);
        if (($intReturn == 0) && is_array($arrFiles1)) {
            $arrFiles = array_merge($arrFiles, $arrFiles1);
        }
        $intReturn = $myConfigClass->sendSSHCommand('ls ' .$strHostBackupDir. '*.cfg_old*', $arrFiles2);
        if (($intReturn == 0) && is_array($arrFiles2)) {
            $arrFiles = array_merge($arrFiles, $arrFiles2);
        }
        $intReturn = $myConfigClass->sendSSHCommand('ls ' .$strServiceBackupDir. '*.cfg_old*', $arrFiles3);
        if (($intReturn == 0) && is_array($arrFiles3)) {
            $arrFiles = array_merge($arrFiles, $arrFiles3);
        }
        if (is_array($arrFiles) && (count($arrFiles) != 0)) {
            foreach ($arrFiles as $elem) {
                if (!substr_count($elem, 'cfg_old')) {
                    continue;
                }
                if (($chkTfSearch == '') || (substr_count($elem, $chkTfSearch) != 0)) {
                    $conttp->setVariable('DAT_BACKUPFILE', str_replace('//', '/', $elem));
                    $conttp->parse('filelist');
                }
            }
        } else {
            $myVisClass->processMessage(
                translate('No backup files or no permission to read the backup files'),
                $strErrorMessage
            );
        }
    } else {
        $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
    }
}
if ($strErrorMessage != '') {
    $conttp->setVariable('ERRORMESSAGE', $strErrorMessage);
}
$conttp->setVariable('INFOMESSAGE', $strInfoMessage);
// Check access rights for adding new objects
if ($myVisClass->checkAccountGroup($prePageKey, 'write') != 0) {
    $conttp->setVariable('ADD_CONTROL', 'disabled="disabled"');
}
$conttp->parse('main');
$conttp->show('main');
//
// Footer ausgeben
// ===============
$maintp->setVariable('VERSION_INFO', "<a href='https://sourceforge.net/projects/nagiosql/' "
        . "target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse('footer');
$maintp->show('footer');
