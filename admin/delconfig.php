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
$prePageId    = 27;
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
$myConfigClass->getConfigValues($intConfigId, 'basedir', $strBaseDir);
$myConfigClass->getConfigValues($intConfigId, 'hostconfig', $strHostDir);
$myConfigClass->getConfigValues($intConfigId, 'serviceconfig', $strServiceDir);
//
// Process form inputs
// ===================
/** @var array $chkMselValue1 */
if (($chkMselValue1[0] != '') && ($chkStatus == 1)) {
    foreach ($chkMselValue1 as $elem) {
        $intCheck = $myConfigClass->removeFile(trim($elem), $intConfigId);
        $strFileTmp1 = str_replace($strServiceDir, '', $elem);
        $strFileTmp2 = str_replace($strHostDir, '', $strFileTmp1);
        $strFile     = str_replace($strBaseDir, '', $strFileTmp2);
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
$conttp->setVariable('TITLE', translate('Delete config files'));
$conttp->parse('header');
$conttp->show('header');
$conttp->setVariable('LANG_SEARCH_STRING', translate('Filter string'));
$conttp->setVariable('LANG_SEARCH', translate('Search'));
$conttp->setVariable('LANG_DELETE', translate('Delete'));
$conttp->setVariable('LANG_DELETE_SEARCH', translate('Reset filter'));
$conttp->setVariable('DAT_SEARCH', $chkTfSearch);
$conttp->setVariable('BACKUPFILE', translate('Configuration file'));
$conttp->setVariable('LANG_REQUIRED', translate('required'));
$conttp->setVariable('MAKE', translate('Delete'));
$conttp->setVariable('ABORT', translate('Abort'));
$conttp->setVariable('CTRL_INFO', translate('Hold CTRL to select<br>more than one entry'));
$conttp->setVariable('IMAGE_PATH', $_SESSION['SETS']['path']['base_url']. 'images/');
$conttp->setVariable('ACTION_INSERT', filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING));
// Build a local file list
if ($intMethod == 1) {
    $output = array();
    $myConfigClass->storeDirToArray($strBaseDir, "\.cfg", '\.cfg_old', $output, $strErrorMessage);
    if (is_array($output) && (count($output) != 0)) {
        foreach ($output as $elem2) {
            if (($chkTfSearch == '') || (substr_count($elem2, $chkTfSearch) != 0)) {
                $conttp->setVariable('DAT_BACKUPFILE', $elem2);
                $conttp->parse('filelist');
            }
        }
    }
} elseif ($intMethod == 2) {
    // Open ftp connection
    if ($myConfigClass->getFTPConnection($intConfigId) == '0') {
        $arrFiles  = array();
        $arrFiles1 = ftp_nlist($myConfigClass->resConnectId, $strBaseDir);
        if (is_array($arrFiles1)) {
            $arrFiles = array_merge($arrFiles, $arrFiles1);
        }
        $arrFiles2 = ftp_nlist($myConfigClass->resConnectId, $strHostDir);
        if (is_array($arrFiles2)) {
            $arrFiles = array_merge($arrFiles, $arrFiles2);
        }
        $arrFiles3 = ftp_nlist($myConfigClass->resConnectId, $strServiceDir);
        if (is_array($arrFiles3)) {
            $arrFiles = array_merge($arrFiles, $arrFiles3);
        }
        if (is_array($arrFiles) && (count($arrFiles) != 0)) {
            foreach ($arrFiles as $elem) {
                if (!substr_count($elem, 'cfg')) {
                    continue;
                }
                if (($chkTfSearch == '') || (substr_count($elem, $chkTfSearch) != 0)) {
                    $conttp->setVariable('DAT_BACKUPFILE', str_replace('//', '/', $elem));
                    $conttp->parse('filelist');
                }
            }
        }
        ftp_close($myConfigClass->resConnectId);
    } else {
        $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
    }
} elseif ($intMethod == 3) {
    // Open ssh connection
    if ($myConfigClass->getSSHConnection($intConfigId) == '0') {
        $intReturn = $myConfigClass->sendSSHCommand('ls '.$strBaseDir, $arrFiles1);
        if (($intReturn == 0) && is_array($arrFiles1) && (count($arrFiles1) != 0)) {
            foreach ($arrFiles1 as $elem) {
                if (!substr_count($elem, 'cfg')) {
                    continue;
                }
                if (substr_count($elem, 'cgi.cfg') != 0) {
                    continue;
                }
                if (substr_count($elem, 'nagios.cfg') != 0) {
                    continue;
                }
                if (($chkTfSearch == '') || (substr_count($elem, $chkTfSearch) != 0)) {
                    $conttp->setVariable('DAT_BACKUPFILE', str_replace('//', '/', $strBaseDir. '/' .$elem));
                    $conttp->setVariable('DAT_BACKUPFILE_FULL', str_replace('//', '/', $strBaseDir. '/' .$elem));
                    $conttp->parse('filelist');
                }
            }
        }
        $intReturn = $myConfigClass->sendSSHCommand('ls '.$strHostDir, $arrFiles2);
        if (($intReturn == 0) && is_array($arrFiles2) && (count($arrFiles2) != 0)) {
            foreach ($arrFiles2 as $elem) {
                if (!substr_count($elem, 'cfg')) {
                    continue;
                }
                if (($chkTfSearch == '') || (substr_count($elem, $chkTfSearch) != 0)) {
                    $conttp->setVariable('DAT_BACKUPFILE', str_replace('//', '/', $strHostDir. '/' .$elem));
                    $conttp->setVariable('DAT_BACKUPFILE_FULL', str_replace('//', '/', $strHostDir. '/' .$elem));
                    $conttp->parse('filelist');
                }
            }
        }
        $intReturn = $myConfigClass->sendSSHCommand('ls '.$strServiceDir, $arrFiles3);
        if (($intReturn == 0) && is_array($arrFiles3) && (count($arrFiles3) != 0)) {
            foreach ($arrFiles3 as $elem) {
                if (!substr_count($elem, 'cfg')) {
                    continue;
                }
                if (($chkTfSearch == '') || (substr_count($elem, $chkTfSearch) != 0)) {
                    $conttp->setVariable('DAT_BACKUPFILE', str_replace('//', '/', $strServiceDir. '/' .$elem));
                    $conttp->setVariable('DAT_BACKUPFILE_FULL', str_replace('//', '/', $strServiceDir. '/' .$elem));
                    $conttp->parse('filelist');
                }
            }
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
