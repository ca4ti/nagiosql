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
// Component : Admin timeperiod definitions
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
$prePageId    = 28;
$preContent   = 'admin/nagioscfg.htm.tpl';
$preAccess    = 1;
$preFieldvars = 1;
$intRemoveTmp = 0;
$strConfig    = '';
//
// Include preprocessing files
// ===========================
require $preBasePath.'functions/prepend_adm.php';
require $preBasePath.'functions/prepend_content.php';
//
// Get configuration set ID
// ========================
$myConfigClass->getConfigTargets($arrConfigSet);
$intConfigId    = $arrConfigSet[0];
$myConfigClass->getConfigValues($intConfigId, 'method', $intMethod);
$myConfigClass->getConfigValues($intConfigId, 'nagiosbasedir', $strBaseDir);
$myConfigClass->getConfigValues($intConfigId, 'conffile', $strConfigfile);
$strLocalBackup = $strConfigfile. '_old_' .date('YmdHis');
//
// Convert Windows to UNIX
// =======================
$chkTaFileText = str_replace("\r\n", "\n", $chkTaFileText);
//
// Process data
// ============
if (($chkTaFileText != '') && ($arrConfigSet[0] != 0)) {
    if ($intMethod == 1) {
        if (file_exists($strBaseDir) && (is_writable($strBaseDir) && is_writable($strConfigfile))) {
            // Backup config file
            $intReturn = $myConfigClass->moveFile('nagiosbasic', basename($strConfigfile), $intConfigId);
            if ($intReturn == 1) {
                $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
            }
            // Write configuration
            $resFile = fopen($strConfigfile, 'wb');
            fwrite($resFile, $chkTaFileText);
            fclose($resFile);
            $myVisClass->processMessage('<span style="color:green">' .translate('Configuration file successfully '
                    . 'written!'). '</span>', $strInfoMessage);
            $myDataClass->writeLog(translate('Configuration successfully written:'). ' ' .$strConfigfile);
        } else {
            $myVisClass->processMessage(translate('Cannot open/overwrite the configuration file (check the '
                    . 'permissions)!'), $strErrorMessage);
            $myDataClass->writeLog(translate('Configuration write failed:'). ' ' .$strConfigfile);
        }
    } elseif (($intMethod == 2) || ($intMethod == 3)) {
        // Backup config file
        $intReturn1 = $myConfigClass->moveFile('nagiosbasic', basename($strConfigfile), $intConfigId);
        if ($intReturn1 == 1) {
            $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
        }
        // Write file to temporary
        $strFileName = tempnam($_SESSION['SETS']['path']['tempdir'], 'nagiosql_conf');
        $resFile = fopen($strFileName, 'wb');
        fwrite($resFile, $chkTaFileText);
        fclose($resFile);
        // Copy configuration to remoty system
        $intReturn2 = $myConfigClass->remoteFileCopy($strConfigfile, $intConfigId, $strFileName, 1);
        if ($intReturn2 == 0) {
            $myVisClass->processMessage('<span style="color:green">' .translate('Configuration file successfully '
                    . 'written!'). '</span>', $strInfoMessage);
            $myDataClass->writeLog(translate('Configuration successfully written:'). ' ' .$strConfigfile);
            unlink($strFileName);
        } else {
            $myVisClass->processMessage(translate('Cannot open/overwrite the configuration file (check the permissions '
                    . 'on remote system)!'), $strErrorMessage);
            $myDataClass->writeLog(translate('Configuration write failed (remote):'). ' ' .$strConfigfile);
            unlink($strFileName);
        }
    }
} elseif ($arrConfigSet[0] == 0) {
    $myVisClass->processMessage(translate('There are no nagios configuration files in common domain, please select a '.
        'valid domain to edit this files!'), $strErrorMessage);
}
//
// Include content
// ===============
$conttp->setVariable('TITLE', translate('Nagios main configuration file'));
$conttp->setVariable('ACTION_INSERT', filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING));
$conttp->setVariable('MAINSITE', $_SESSION['SETS']['path']['base_url']. 'admin.php');
foreach ($arrDescription as $elem) {
    $conttp->setVariable($elem['name'], $elem['string']);
}
//
// Open configuration
// ==================
if ($intMethod == 1) {
    if (file_exists($strConfigfile) && is_readable($strConfigfile)) {
        $resFile   = fopen($strConfigfile, 'rb');
        if ($resFile) {
            while (!feof($resFile)) {
                $strConfig .= fgets($resFile, 1024);
            }
        }
    } else {
        $myVisClass->processMessage(translate('Cannot open the data file (check the permissions)!'), $strErrorMessage);
    }
} elseif (($intMethod == 2) || ($intMethod == 3)) {
    // Write file to temporary
    $strFileName = tempnam($_SESSION['SETS']['path']['tempdir'], 'nagiosql_conf');
    // Copy configuration from remoty system
    $intReturn = $myConfigClass->remoteFileCopy($strConfigfile, $intConfigId, $strFileName, 0);
    if ($intReturn == 0) {
        $resFile = fopen($strFileName, 'rb');
        if (is_resource($resFile)) {
            while (!feof($resFile)) {
                $strConfig .= fgets($resFile, 1024);
            }
            unlink($strFileName);
        } else {
            $myVisClass->processMessage(translate('Cannot open the temporary file'), $strErrorMessage);
        }
    } else {
        $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
        $myDataClass->writeLog(translate('Configuration read failed (remote):'). ' ' .$strErrorMessage);
        if (file_exists($strFileName)) {
            unlink($strFileName);
        }
    }
}
$conttp->setVariable('DAT_NAGIOS_CONFIG', $strConfig);
if ($strErrorMessage != '') {
    $conttp->setVariable('ERRORMESSAGE', $strErrorMessage);
}
$conttp->setVariable('INFOMESSAGE', $strInfoMessage);
// Check access rights for adding new objects
if ($myVisClass->checkAccountGroup($prePageKey, 'write') != 0) {
    $conttp->setVariable('ADD_CONTROL', 'disabled="disabled"');
}
$conttp->parse('naginsert');
$conttp->show('naginsert');
//
// Process footer
// ==============
$maintp->setVariable('VERSION_INFO', "<a href='https://sourceforge.net/projects/nagiosql/' "
        . "target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse('footer');
$maintp->show('footer');
