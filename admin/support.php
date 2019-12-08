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
// Component : Support page
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
$prePageId     = 40;
$preContent    = 'admin/support.htm.tpl';
$preAccess     = 1;
$preFieldvars  = 1;
$setSaveLangId = 'private';
//
// Include preprocessing files
// ===========================
require $preBasePath.'functions/prepend_adm.php';
require $preBasePath.'functions/prepend_content.php';
//
// Start content
// =============
$conttp->setVariable('TITLE', translate('NagiosQL support page'));
$conttp->parse('header');
$conttp->show('header');
//
// Single data form
// ================
$conttp->setVariable('MAINSITE', $_SESSION['SETS']['path']['base_url']. 'admin.php');
foreach ($arrDescription as $elem) {
    $conttp->setVariable($elem['name'], $elem['string']);
}
$conttp->setVariable('SUBTITLE_1', translate('Support contact information'));
$conttp->setVariable('SUPPORT_TEXT_1', translate('For questions, the online support forum or contact information '
    . 'visit our website:'));
$conttp->setVariable('WEBSITE_LINK', translate('NagiosQL on sourceforge'));
//
// Donation
// ========
$conttp->setVariable('SUBTITLE_2', translate('Donate to support NagiosQL'));
$conttp->setVariable('SUPPORT_TEXT_2', translate('If you like NagiosQL and it simplifies your daily work, then you '
    . 'may want to support the project by making a donation. This helps us to keep NagiosQL alive and to cover '
    . 'our costs. Thank you for your donation!'));
$conttp->setVariable('DONATE_LINK', translate('Donate for NagiosQL on sourceforge'));
//
// Translations
// ============
$conttp->setVariable('SUBTITLE_3', translate('Translation services'));
$conttp->setVariable('SUPPORT_TEXT_3', translate('NagiosQL was translated into various languages​​. Since some '
    . 'translators are no longer available in later versions, there may be untranslated words or phrases. If '
    . 'you want to help us complete the translation, correct them or introduce a new language​​, then sign up '
    . 'with us now! The translations are simply feasible online - we use an open translation service where '
    . 'you can register for free at any time:'));
$conttp->setVariable('TRANSLATION_LINK', translate('Transifex translation service'));
//
// GIT repository
// ==============
$conttp->setVariable('SUBTITLE_8', translate('GIT software repository'));
$conttp->setVariable('SUPPORT_TEXT_5', translate('The NagiosQL sources are available on GitLab. There you will '
    .'always find the latest bugfixes and changes as well as older branches.'));
$conttp->setVariable('GIT_LINK', translate('GitLab'));
//
// Online version check
// ====================
$conttp->setVariable('SUBTITLE_4', translate('Version check'));
if (!isset($SETS['common']['updcheck']) || ($SETS['common']['updcheck'] == '0')) {
    $conttp->setVariable('SUPPORT_TEXT_4', translate('The online version check is not enabled. You can enable it '
        . 'on the settings page.'));
} elseif (isset($SETS['common']['updcheck']) && ($SETS['common']['updcheck'] == '1')) {
    $conttp->setVariable('SUPPORT_TEXT_4', translate('The online version check connects the NagiosQL page to find '
        . 'out, if your version is still up to date.'));
    $conttp->setVariable('LOADER_IMAGE', $_SESSION['SETS']['path']['base_url']. 'images/loader.gif');
    $conttp->setVariable('VERSION_IF_SRC', $_SESSION['SETS']['path']['base_url']. 'admin/versioncheck.php?show=0');
    $conttp->parse('versioncheck_frame');
    $conttp->setVariable('VERSION_IF_SRC_RELOAD', $_SESSION['SETS']['path']['base_url'].
        'admin/versioncheck.php?show=1');
    $conttp->parse('versioncheck_js');
}
$conttp->setVariable('GIT_TITLE', translate('GIT code version'));
$conttp->setVariable('GIT_VERSION', $setFileVersion.'-'.$setGITVersion);
//
// Environment check
// =================
$conttp->setVariable('SUBTITLE_5', translate('Environment check'));
// Javascript check
$conttp->setVariable('FAILED', translate('failed'));
$conttp->setVariable('OK', translate('ok'));
// PHP version check
$conttp->setVariable('PHP_VERSION', translate('PHP version'));
if (PHP_VERSION_ID >= 50500) {
    $conttp->setVariable('PHP_CLASS', 'checkgreen');
    $conttp->setVariable('PHP_RESULT', translate('ok'). ' (' .PHP_VERSION. ')');
} else {
    $conttp->setVariable('PHP_CLASS', 'checkred');
    $conttp->setVariable('PHP_RESULT', translate('failed'). ' (' .PHP_VERSION. ' - '
        .translate('Required:'). ' 5.5.0)');
}
// PHP modules / extensions
$strExtPath = ini_get('extension_dir');
$strPrefix    = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
$conttp->setVariable('PHP_SESSION_MODULE', translate('PHP module:'). ' session');
if (extension_loaded('session')) {
    $conttp->setVariable('PHP_SESSION_CLASS', 'checkgreen');
    $conttp->setVariable('PHP_SESSION_RESULT', translate('ok'));
} else {
    $conttp->setVariable('PHP_SESSION_CLASS', 'checkred');
    $conttp->setVariable('PHP_SESSION_RESULT', translate('failed'));
}
$conttp->setVariable('PHP_GETTEXT_MODULE', translate('PHP module:'). ' gettext');
if (extension_loaded('gettext')) {
    $conttp->setVariable('PHP_GETTEXT_CLASS', 'checkgreen');
    $conttp->setVariable('PHP_GETTEXT_RESULT', translate('ok'));
} else {
    $conttp->setVariable('PHP_GETTEXT_CLASS', 'checkred');
    $conttp->setVariable('PHP_GETTEXT_RESULT', translate('failed'));
}
$conttp->setVariable('PHP_FTP_MODULE', translate('PHP module:'). ' ftp');
if (extension_loaded('ftp')) {
    $conttp->setVariable('PHP_FTP_CLASS', 'checkgreen');
    $conttp->setVariable('PHP_FTP_RESULT', translate('ok'));
    $intFTP_ok = 1;
} else {
    $conttp->setVariable('PHP_FTP_CLASS', 'checkorange');
    $conttp->setVariable('PHP_FTP_RESULT', translate('failed'));
    $intFTP_ok = 0;
}
$conttp->setVariable('PHP_SSH2_MODULE', translate('PHP module:'). ' ssh');
if (extension_loaded('ssh2')) {
    $conttp->setVariable('PHP_SSH2_CLASS', 'checkgreen');
    $conttp->setVariable('PHP_SSH2_RESULT', translate('ok'));
    $intSSH_ok = 1;
} else {
    $conttp->setVariable('PHP_SSH2_CLASS', 'checkorange');
    $conttp->setVariable('PHP_SSH2_RESULT', translate('failed'));
    $intSSH_ok = 0;
}
// Datenbankversion
if ($SETS['db']['type'] == 'mysql') {
    $conttp->setVariable('DB_VERSION', translate('MySQL version'));
    $strSQL    = "SHOW VARIABLES LIKE 'version'";
    $booReturn = $myDBClass->hasSingleDataset($strSQL, $arrDataset);
    if ($booReturn && (count($arrDataset) != 0)) {
        $strDBVersion = $arrDataset['Value'];
        if (version_compare($strDBVersion, '5.0.0', '>=')) {
            $conttp->setVariable('DB_CLASS', 'checkgreen');
            $conttp->setVariable('DB_RESULT', translate('ok'). ' (' .$strDBVersion. ')');
        } else {
            $conttp->setVariable('DB_CLASS', 'checkorange');
            $conttp->setVariable('DB_RESULT', translate('failed'). ' (' .$strDBVersion. ' - ' .translate('Required:').
                ' 5.0.0)');
        }
    }
}
// INI settings
$conttp->setVariable('INI_FILE_UPLOADS', translate('PHP ini settings:'). ' file_uploads');
$strStatus1 = ini_get('file_uploads');
if (empty($strStatus1) || ($strStatus1 == 1)) {
    $conttp->setVariable('INI_FILE_UPLOADS_CLASS', 'checkgreen');
    $conttp->setVariable('INI_FILE_UPLOADS_RESULT', translate('ok'));
} else {
    $conttp->setVariable('INI_FILE_UPLOADS_CLASS', 'checkred');
    $conttp->setVariable('INI_FILE_UPLOADS_RESULT', translate('failed'));
}
$conttp->setVariable('INI_AUTO_START', translate('PHP ini settings:'). ' session.auto_start');
$strStatus2 = ini_get('session.auto_start');
if (empty($strStatus2) || ($strStatus2 == 0)) {
    $conttp->setVariable('INI_AUTO_START_CLASS', 'checkgreen');
    $conttp->setVariable('INI_AUTO_START_RESULT', translate('ok'));
} else {
    $conttp->setVariable('INI_AUTO_START_CLASS', 'checkred');
    $conttp->setVariable('INI_AUTO_START_RESULT', translate('failed'));
}
$conttp->setVariable('INI_SUHO_SESS_ENC', translate('PHP ini settings:'). ' suhosin.session.encrypt');
$strStatus3 = ini_get('suhosin.session.encrypt');
if (empty($strStatus3) || ($strStatus3 == 0)) {
    $conttp->setVariable('INI_SUHO_SESS_ENC_CLASS', 'checkgreen');
    $conttp->setVariable('INI_SUHO_SESS_ENC_RESULT', translate('ok'));
} else {
    $conttp->setVariable('INI_SUHO_SESS_ENC_CLASS', 'checkred');
    $conttp->setVariable('INI_SUHO_SESS_ENC_RESULT', translate('failed'));
}
$conttp->setVariable('INI_DATE_TIMEZONE', translate('PHP ini settings:'). ' date.timezone');
$strStatus4 = ini_get('date.timezone');
if (!empty($strStatus4)) {
    $conttp->setVariable('INI_DATE_TIMEZONE_CLASS', 'checkgreen');
    $conttp->setVariable('INI_DATE_TIMEZONE_RESULT', translate('ok'). ' (' .$strStatus4. ')');
} else {
    $conttp->setVariable('INI_DATE_TIMEZONE_CLASS', 'checkred');
    $conttp->setVariable('INI_DATE_TIMEZONE_RESULT', translate('failed'));
}
// File access checks
$conttp->setVariable('RW_CONFIG', translate('Read/Write access:'). ' settings.php');
$strConfigFile = '../config/settings.php';
if (file_exists($strConfigFile) && is_readable($strConfigFile) && is_writable($strConfigFile)) {
    $conttp->setVariable('RW_CONFIG_CLASS', 'checkgreen');
    $conttp->setVariable('RW_CONFIG_RESULT', translate('ok'));
} else {
    $conttp->setVariable('RW_CONFIG_CLASS', 'checkred');
    $conttp->setVariable('RW_CONFIG_RESULT', translate('failed'));
}
//
// Domain checks
// =============
$myConfigClass->getConfigTargets($arrConfigSet);
$intConfigId  = $arrConfigSet[0];
$myConfigClass->getConfigValues($intConfigId, 'method', $intMethod);
if ($intConfigId != 0) {
    $conttp->setVariable('SUBTITLE_6', translate('Config domain checks'));
    $conttp->setVariable('SUPPORT_TEXT_6', translate('The checks below are based on your data domain and config '
        . 'domain settings. To change the data domain, use the pull down menu in the upper right corner. Repeat '
        . 'this check for any data domain you have configured. To change the config domain, use the data domain '
        . 'menu and select a different config domain value.'));
    $myConfigClass->getConfigValues($intConfigId, 'conffile', $strConffile);
    $myConfigClass->getConfigValues($intConfigId, 'target', $strConfName);
    $conttp->setVariable('DOMAIN_NAME', translate('Config domain name'));
    $conttp->setVariable('DOMAIN_NAME_VALUE', $strConfName);
    $conttp->setVariable('CONNECT_TYPE', translate('Connection type'));
    if ($intMethod == 1) {
        $conttp->setVariable('CONNECT_TYPE_CLASS', 'checkgreen');
        $conttp->setVariable('CONNECT_TYPE_RESULT', 'Fileaccess');
    } elseif ($intMethod == 2) {
        if ($intFTP_ok == 1) {
            $conttp->setVariable('CONNECT_TYPE_CLASS', 'checkgreen');
            $conttp->setVariable('CONNECT_TYPE_RESULT', 'FTP');
        } else {
            $conttp->setVariable('CONNECT_TYPE_CLASS', 'checkred');
            $conttp->setVariable('CONNECT_TYPE_RESULT', 'FTP (no FTP module)');
        }
    } elseif ($intMethod == 3) {
        if ($intSSH_ok == 1) {
            $conttp->setVariable('CONNECT_TYPE_CLASS', 'checkgreen');
            $conttp->setVariable('CONNECT_TYPE_RESULT', 'SSH/SFTP');
        } else {
            $conttp->setVariable('CONNECT_TYPE_CLASS', 'checkred');
            $conttp->setVariable('CONNECT_TYPE_RESULT', 'SSH/SFTP (no SSH2 module)');
        }
    }
    $conttp->setVariable('CONNECT_CHECK', translate('Connection check'));
    if ($intMethod == 1) {
        $conttp->setVariable('CONNECT_CHECK_CLASS', 'checkgreen');
        $conttp->setVariable('CONNECT_CHECK_RESULT', translate('ok'));
    } elseif ($intMethod == 2) {
        $booReturn = 0;
        if (empty($myConfigClass->resConnectId) || !is_resource($myConfigClass->resConnectId)) {
            $booReturn = $myConfigClass->getFTPConnection($intConfigId);
        }
        if ($booReturn == 1) {
            $conttp->setVariable('CONNECT_CHECK_CLASS', 'checkred');
            $conttp->setVariable('CONNECT_CHECK_RESULT', translate('failed'));
            $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
            $intConnectCheck = 0;
        } else {
            $conttp->setVariable('CONNECT_CHECK_CLASS', 'checkgreen');
            $conttp->setVariable('CONNECT_CHECK_RESULT', translate('ok'));
            $intConnectCheck = 1;
        }
    } elseif ($intMethod == 3) {
        $booReturn = 0;
        if (empty($myConfigClass->resConnectId) || !is_resource($myConfigClass->resConnectId)) {
            $booReturn = $myConfigClass->getSSHConnection($intConfigId);
        }
        if ($booReturn == 1) {
            $conttp->setVariable('CONNECT_CHECK_CLASS', 'checkred');
            $conttp->setVariable('CONNECT_CHECK_RESULT', translate('failed'));
            $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
            $intConnectCheck = 0;
        } else {
            $conttp->setVariable('CONNECT_CHECK_CLASS', 'checkgreen');
            $conttp->setVariable('CONNECT_CHECK_RESULT', translate('ok'));
            $intConnectCheck = 1;
        }
    }
    $conttp->setVariable('RW_NAG_CONF', translate('Nagios config file'));
    if ($intMethod == 1) {
        if (file_exists($strConffile) && is_readable($strConffile)) {
            if (is_writable($strConffile)) {
                $conttp->setVariable('RW_NAG_CONF_CLASS', 'checkgreen');
                $conttp->setVariable('RW_NAG_CONF_RESULT', translate('ok'));
            } else {
                $conttp->setVariable('RW_NAG_CONF_CLASS', 'checkorange');
                $conttp->setVariable('RW_NAG_CONF_RESULT', translate('ok'). ' (' .translate('readonly'). ')');
            }
        } else {
            $conttp->setVariable('RW_NAG_CONF_CLASS', 'checkred');
            $conttp->setVariable('RW_NAG_CONF_RESULT', translate('failed'));
        }
    } elseif (($intMethod == 2) || ($intMethod == 3)) {
        // Write file to temporary
        $strFileName = tempnam($SETS['path']['tempdir'], 'nagiosql_conf');
        // Copy configuration from remote system
        $intReturn = $myConfigClass->remoteFileCopy($strConffile, $intConfigId, $strFileName, 0);
        if ($intReturn == 0) {
            $intCheck = 0;
        } else {
            $intCheck = 1;
            $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
            if (file_exists($strFileName)) {
                unlink($strFileName);
            }
        }
        // Copy configuration to remote system
        if ($intCheck == 0) {
            $intReturn = $myConfigClass->remoteFileCopy($strConffile, $intConfigId, $strFileName, 1);
            if ($intReturn == 0) {
                $intCheck = 0;
                if (file_exists($strFileName)) {
                    unlink($strFileName);
                }
            } else {
                $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
                $intCheck = 2;
                if (file_exists($strFileName)) {
                    unlink($strFileName);
                }
            }
        }
        // Write Results
        if ($intCheck == 0) {
            $conttp->setVariable('RW_NAG_CONF_CLASS', 'checkgreen');
            $conttp->setVariable('RW_NAG_CONF_RESULT', translate('ok'));
        } elseif ($intCheck == 1) {
            $conttp->setVariable('RW_NAG_CONF_CLASS', 'checkred');
            $conttp->setVariable('RW_NAG_CONF_RESULT', translate('failed'));
        } elseif ($intCheck == 2) {
            $conttp->setVariable('RW_NAG_CONF_CLASS', 'checkorange');
            $conttp->setVariable('RW_NAG_CONF_RESULT', translate('ok'). ' (' .translate('readonly'). ')');
        }
    }
    $myConfigClass->getConfigValues($intConfigId, 'pidfile', $strPidfile);
    $myConfigClass->getConfigValues($intConfigId, 'binaryfile', $strBinary);
    $myConfigClass->getConfigValues($intConfigId, 'commandfile', $strCommandfile);
    $conttp->setVariable('CHECK_NAG_LOCK', translate('Nagios process file'));
    if ($intMethod == 1) {
        if (substr_count(PHP_OS, 'Linux') != 0) {
            exec('ps -ef | grep '.basename($strBinary).' | grep -v grep', $arrExec);
        } else {
            $arrExec[0] = 1;
        }
        if (isset($arrExec[0])) {
            if (file_exists($strPidfile)) {
                $conttp->setVariable('CHECK_NAG_LOCK_CLASS', 'checkgreen');
                $conttp->setVariable('CHECK_NAG_LOCK_RESULT', translate('ok'));
                $intDemonOk = 0;
            } else {
                $conttp->setVariable('CHECK_NAG_LOCK_CLASS', 'checkred');
                $conttp->setVariable('CHECK_NAG_LOCK_RESULT', translate('failed') . ' ('
                    . translate('file is missed') . ')');
                $intDemonOk = 1;
            }
        } else {
            $conttp->setVariable('CHECK_NAG_LOCK_CLASS', 'checkred');
            $conttp->setVariable('CHECK_NAG_LOCK_RESULT', translate('failed'));
            $myVisClass->processMessage(translate('Nagios daemon is not running'), $strErrorMessage);
            $intDemonOk = 1;
        }
    } elseif ($intMethod == 2) {
        $conttp->setVariable('CHECK_NAG_LOCK_CLASS', 'checkorange');
        $conttp->setVariable('CHECK_NAG_LOCK_RESULT', translate('ok'). ' (' .translate('not used with FTP'). ')');
        $intDemonOk = 1;
    } elseif ($intMethod == 3) {
        $arrResultBinary = array();
        $arrResultPid    = array();
        $intBinary       = 1;
        $intPid          = 1;
        if ($strBinary != '') {
            $intBinary = $myConfigClass->sendSSHCommand('ps -ef | grep '.basename($strBinary).' | grep '.
                basename($strConffile).' | grep -v grep', $arrResultBinary);
        }
        if ($strPidfile != '') {
            $intPid = $myConfigClass->sendSSHCommand('ls ' . $strPidfile, $arrResult2);
        }
        if (($intBinary == 0) && ($intPid == 0)) {
            $conttp->setVariable('CHECK_NAG_LOCK_CLASS', 'checkgreen');
            $conttp->setVariable('CHECK_NAG_LOCK_RESULT', translate('ok'));
            $intDemonOk = 0;
        } elseif (($intBinary == 0) && ($intPid == 1)) {
            $conttp->setVariable('CHECK_NAG_LOCK_CLASS', 'checkred');
            $conttp->setVariable('CHECK_NAG_LOCK_RESULT', translate('failed'). ' (' .translate('file is missed'). ')');
            $intDemonOk = 1;
        } else {
            $conttp->setVariable('CHECK_NAG_LOCK_CLASS', 'checkred');
            $conttp->setVariable('CHECK_NAG_LOCK_RESULT', translate('failed'). ' (' .translate('demon dead'). ')');
            $myVisClass->processMessage(translate('Nagios daemon is not running'), $strErrorMessage);
            $intDemonOk = 1;
        }
    }
    // Command file
    $conttp->setVariable('RW_NAG_COMMAND', translate('Nagios command file'));
    if ($intMethod == 1) {
        if (file_exists($strCommandfile) && is_readable($strCommandfile) && is_writable($strCommandfile)) {
            if ($intDemonOk == 0) {
                $conttp->setVariable('RW_NAG_COMMAND_CLASS', 'checkgreen');
                $conttp->setVariable('RW_NAG_COMMAND_RESULT', translate('ok'));
            } else {
                $conttp->setVariable('RW_NAG_COMMAND_CLASS', 'checkorange');
                $conttp->setVariable('RW_NAG_COMMAND_RESULT', translate('ok'). ' (' .translate('demon dead'). '?)');
            }
        }
        if (!file_exists($strCommandfile)) {
            $conttp->setVariable('RW_NAG_COMMAND_CLASS', 'checkred');
            $conttp->setVariable('RW_NAG_COMMAND_RESULT', translate('failed'). ' (' .translate('file is missed'). ')');
        } elseif (!is_writable($strCommandfile)) {
            $conttp->setVariable('RW_NAG_COMMAND_CLASS', 'checkred');
            $conttp->setVariable('RW_NAG_COMMAND_RESULT', translate('failed'). ' (' .translate('readonly'). ')');
        } elseif (!is_readable($strCommandfile)) {
            $conttp->setVariable('RW_NAG_COMMAND_CLASS', 'checkred');
            $conttp->setVariable('RW_NAG_COMMAND_RESULT', translate('failed'));
        }
    } elseif ($intMethod == 2) {
        $conttp->setVariable('RW_NAG_COMMAND_CLASS', 'checkorange');
        $conttp->setVariable('RW_NAG_COMMAND_RESULT', translate('ok'). ' (' .translate('not used with FTP'). ')');
    } elseif ($intMethod == 3) {
        if ($strCommandfile != '') {
            if ($intDemonOk == 0) {
                $myConfigClass->sendSSHCommand('echo "TEST" >>' . $strCommandfile . '; echo $?; echo ""', $arrTemp);
                if ($arrTemp[0] == 0) {
                    $conttp->setVariable('RW_NAG_COMMAND_CLASS', 'checkgreen');
                    $conttp->setVariable('RW_NAG_COMMAND_RESULT', translate('ok'));
                } else {
                    $conttp->setVariable('RW_NAG_COMMAND_CLASS', 'checkred');
                    $conttp->setVariable('RW_NAG_COMMAND_RESULT', translate('failed'). ' ('
                        .translate('readonly'). ')');
                }
            } elseif ($intDemonOk == 1) {
                $conttp->setVariable('RW_NAG_COMMAND_CLASS', 'checkred');
                $conttp->setVariable('RW_NAG_COMMAND_RESULT', translate('failed'). ' (' .translate('demon dead'). ')');
            } else {
                $conttp->setVariable('RW_NAG_COMMAND_CLASS', 'checkred');
                $conttp->setVariable('RW_NAG_COMMAND_RESULT', translate('failed'). ' ('
                    .translate('file is missed'). ')');
            }
        } else {
            $conttp->setVariable('RW_NAG_COMMAND_CLASS', 'checkred');
            $conttp->setVariable('RW_NAG_COMMAND_RESULT', translate('failed'). ' (' .translate('file is missed'). ')');
        }
    }
    // Binary file
    $conttp->setVariable('EXE_NAG_BINARY', translate('Nagios binary file'));
    if ($intMethod == 1) {
        if (file_exists($strBinary)) {
            if (is_executable($strBinary)) {
                $conttp->setVariable('EXE_NAG_BINARY_CLASS', 'checkgreen');
                $conttp->setVariable('EXE_NAG_BINARY_RESULT', translate('ok'));
            } else {
                $conttp->setVariable('EXE_NAG_BINARY_CLASS', 'checkred');
                $conttp->setVariable('EXE_NAG_BINARY_RESULT', translate('failed'). ' ('
                    .translate('not executable'). ')');
            }
        } else {
            $conttp->setVariable('EXE_NAG_BINARY_CLASS', 'checkred');
            $conttp->setVariable('EXE_NAG_BINARY_RESULT', translate('failed'). ' (' .translate('file is missed'). ')');
        }
    } elseif ($intMethod == 2) {
        $conttp->setVariable('EXE_NAG_BINARY_CLASS', 'checkorange');
        $conttp->setVariable('EXE_NAG_BINARY_RESULT', translate('ok'). ' (' .translate('not used with FTP'). ')');
    } elseif ($intMethod == 3) {
        $booReturn = 0;
        /** @noinspection IsEmptyFunctionUsageInspection */
        if (empty($myConfigClass->resConnectId) || !is_resource($myConfigClass->resConnectId)) {
            $booReturn = $myConfigClass->getSSHConnection($intConfigId);
        }
        if ($booReturn == 1) {
            $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
        } else {
            if (($strBinary != '') && ($strConffile != '') &&
                ($myConfigClass->sendSSHCommand('ls '.$strBinary, $arrTemp) == 0)) {
                $myConfigClass->sendSSHCommand($strBinary.' -V', $arrResult);
                if (!is_array($arrResult) || ($arrResult == false)) {
                    $conttp->setVariable('EXE_NAG_BINARY_CLASS', 'checkred');
                    $conttp->setVariable('EXE_NAG_BINARY_RESULT', translate('failed'). ' (' .
                        translate('not executable'). ')');
                } else {
                    $conttp->setVariable('EXE_NAG_BINARY_CLASS', 'checkgreen');
                    $conttp->setVariable('EXE_NAG_BINARY_RESULT', translate('ok'));
                }
            } else {
                $conttp->setVariable('EXE_NAG_BINARY_CLASS', 'checkred');
                $conttp->setVariable('EXE_NAG_BINARY_RESULT', translate('failed'). ' (' .
                    translate('file is missed'). ')');
            }
        }
    }
    // Check config files
    $myConfigClass->getConfigValues($intConfigId, 'basedir', $strBasedir);
    $myConfigClass->getConfigValues($intConfigId, 'hostconfig', $strHostdir);
    $myConfigClass->getConfigValues($intConfigId, 'serviceconfig', $strServicedir);
    $conttp->setVariable('SUBTITLE_7', translate('Verify configuration files and demon configuration'));
    $conttp->setVariable('CONFIGURATION_NAME', translate('Configuration name'));
    $conttp->setVariable('USED', translate('Used in data domain'));
    $conttp->setVariable('DEMON_CONFIG', translate('Included in demon configuration'). ' ('
        .basename($strConffile). ')');
    $arrConfig      = array();
    $arrConfigFiles = array(
        'Hosts'               =>    array( 'table' => 'tbl_host',              'file' => 'directory'),
        'Services'            =>    array( 'table' => 'tbl_service',           'file' => 'directory'),
        'Hostgroups'          =>    array( 'table' => 'tbl_hostgroup',         'file' => 'hostgroups.cfg'),
        'Servicegroups'       =>    array( 'table' => 'tbl_servicegroup',      'file' => 'servicegroups.cfg'),
        'Hosttemplates'       =>    array( 'table' => 'tbl_hosttemplate',      'file' => 'hosttemplates.cfg'),
        'servicetemplates'    =>    array( 'table' => 'tbl_servicetemplate',   'file' => 'servicetemplates.cfg'),
        'Contacts'            =>    array( 'table' => 'tbl_contact',           'file' => 'contacts.cfg'),
        'Contactgroups'       =>    array( 'table' => 'tbl_contactgroup',      'file' => 'contactgroups.cfg'),
        'Timeperiods'         =>    array( 'table' => 'tbl_timeperiod',        'file' => 'timeperiods.cfg'),
        'Contacttemplates'    =>    array( 'table' => 'tbl_contacttemplate',   'file' => 'contacttemplates.cfg'),
        'Commands'            =>    array( 'table' => 'tbl_command',           'file' => 'commands.cfg'),
        'Hostdependencies'    =>    array( 'table' => 'tbl_hostdependency',    'file' => 'hostdependencies.cfg'),
        'Hostescalations'     =>    array( 'table' => 'tbl_hostescalation',    'file' => 'hostescalations.cfg'),
        'Hostextinfo'         =>    array( 'table' => 'tbl_hostextinfo',       'file' => 'hostextinfo.cfg'),
        'Servicedependencies' =>    array( 'table' => 'tbl_servicedependency', 'file' => 'servicedependencies.cfg'),
        'Serviceescalations'  =>    array( 'table' => 'tbl_serviceescalation', 'file' => 'serviceescalations.cfg'),
        'Serviceextinfo'      =>    array( 'table' => 'tbl_serviceextinfo',    'file' => 'serviceextinfo.cfg'));
    if ($intMethod == 1) {
        $intCheck = 1;
        if (file_exists($strConffile) && is_readable($strConffile)) {
            $resFile = fopen($strConffile, 'rb');
            while (!feof($resFile)) {
                $strLine = trim(fgets($resFile));
                if ((0 === strpos($strLine, 'c')) && ((substr_count($strLine, 'cfg_dir') != 0) ||
                        (substr_count($strLine, 'cfg_file') != 0))) {
                    $arrConfig[] = $strLine;
                }
            }
            $intCheck = 0;
            fclose($resFile);
        }
    } elseif (($intMethod == 2) || ($intMethod == 3)) {
        $intCheck = 1;
        // Write file to temporary
        $strFileName = tempnam($SETS['path']['tempdir'], 'nagiosql_conf');
        // Copy configuration from remote system
        $intReturn = $myConfigClass->remoteFileCopy($strConffile, $intConfigId, $strFileName, 0);
        if ($intReturn == 0) {
            $intCheck = 0;
            if (file_exists($strFileName) && is_readable($strFileName)) {
                $resFile = fopen($strFileName, 'rb');
                while (!feof($resFile)) {
                    $strLine = trim(fgets($resFile));
                    if ((0 === strpos($strLine, 'c')) && ((substr_count($strLine, 'cfg_dir') != 0) ||
                            (substr_count($strLine, 'cfg_file') != 0))) {
                        $arrConfig[] = $strLine;
                    }
                }
                fclose($resFile);
                $intCheck = 0;
            }
        } else {
            $intCheck = 1;
            $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
            if (file_exists($strFileName)) {
                unlink($strFileName);
            }
        }
    }
    $i = 0;
    foreach ($arrConfigFiles as $key => $elem) {
        // Line colours
        $strClassL = 'tdlb';
        $strClassM = 'tdmb';
        if ($i%2 == 1) {
            $strClassL = 'tdld';
            $strClassM = 'tdmd';
        }
        $conttp->setVariable('CLASS_L', $strClassL);
        $conttp->setVariable('CLASS_M', $strClassM);
        // Write configuiration name
        $conttp->setVariable('CONFIG_NAME', $key);
        // Count active datasets
        $strSQL = 'SELECT * FROM `' .$elem['table']."` WHERE `active`='1' AND `config_id`=$chkDomainId";
        $booReturn = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
        if ($booReturn && ($intDataCount != 0)) {
            $conttp->setVariable('ACTIVE_CONFIG_COUNT', '<span class="checkgreen">' .translate('ok'). ' (' .
                $intDataCount. ')</span>');
        } elseif ($intDataCount == 0) {
            $conttp->setVariable('ACTIVE_CONFIG_COUNT', '<span class="checkgreen">' .translate('not used'). '</span>');
        } else {
            $conttp->setVariable('ACTIVE_CONFIG_COUNT', '<span class="checkred">' .translate('failed'). '</span>');
            $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
        }
        $conttp->setVariable('DEMON_CFG_OK', '<span class="checkred">' .translate('failed'). '</span> (' .
            translate('cfg definition missed'). ')');
        if (($intCheck == 0) && is_array($arrConfig) && (count($arrConfig) != 0)) {
            foreach ($arrConfig as $line) {
                if ($elem['file'] != 'directory') {
                    if ((substr_count($line, 'cfg_dir=' .$strBasedir) != 0) && (substr_count($line, 'cfg_dir=' .
                                substr($strHostdir, 0, -1)) == 0) && (substr_count($line, 'cfg_dir=' .
                                substr($strServicedir, 0, -1)) == 0)) {
                        $conttp->setVariable('DEMON_CFG_OK', '<span class="checkgreen">' .translate('ok').
                            '</span> (' .$line. ')');
                        break;
                    }
                    if (substr_count($line, $strBasedir.$elem['file']) != 0) {
                        $conttp->setVariable('DEMON_CFG_OK', '<span class="checkgreen">' .translate('ok').
                            '</span> (' .$line. ')');
                        break;
                    }
                    if ($intDataCount == 0) {
                        $conttp->setVariable('DEMON_CFG_OK', '<span class="checkorange">' .translate('ok').
                            '</span> (' .translate('cfg definition missed, but actually not used'). ')');
                    } elseif (substr_count($line, $elem['file']) != 0) {
                        $conttp->setVariable('DEMON_CFG_OK', '<span class="checkred">' .translate('failed').
                            '</span> (' .translate('wrong base path:'). ' ' .$line. ')');
                        break;
                    }
                } elseif ($elem['table'] == 'tbl_host') {
                    if (substr_count($line, 'cfg_dir=' .substr($strHostdir, 0, -1)) != 0) {
                        $conttp->setVariable('DEMON_CFG_OK', '<span class="checkgreen">' .translate('ok').
                            '</span> (' .$line. ')');
                        break;
                    }
                    if ($intDataCount == 0) {
                        $conttp->setVariable('DEMON_CFG_OK', '<span class="checkorange">' .translate('ok').
                            '</span> (' .translate('cfg definition missed, but actually not used'). ')');
                        break;
                    }
                } elseif ($elem['table'] == 'tbl_service') {
                    if (substr_count($line, 'cfg_dir=' .substr($strServicedir, 0, -1)) != 0) {
                        $conttp->setVariable('DEMON_CFG_OK', '<span class="checkgreen">' .translate('ok').
                            '</span> (' .$line. ')');
                        break;
                    }
                    if ($intDataCount == 0) {
                        $conttp->setVariable('DEMON_CFG_OK', '<span class="checkorange">' .translate('ok').
                            '</span> (' .translate('cfg definition missed, but actually not used'). ')');
                        break;
                    }
                }
            }
        } else {
            $conttp->setVariable('DEMON_CFG_OK', '<span class="checkred">' .translate('failed').
                '</span> (' .translate('cfg file not readable'). ')');
        }
        $conttp->parse('configfileline');
        $i++;
    }
    // Check for unused config
    if (($intCheck == 0) && is_array($arrConfig) && (count($arrConfig) != 0)) {
        foreach ($arrConfig as $line) {
            $intTest=0;
            foreach ($arrConfigFiles as $elem) {
                if (substr_count($line, $elem['file']) != 0) {
                    $intTest = 1;
                }
            }
            if ($intTest == 0) {
                if (substr_count($line, substr('cfg_dir=' .$strHostdir, 0, -1))    != 0) {
                    $intTest = 1;
                }
                if (substr_count($line, substr('cfg_dir=' .$strServicedir, 0, -1)) != 0) {
                    $intTest = 1;
                }
                //if (substr_count($line,substr("cfg_dir=".$strBasedir,0,-1))    != 0) $intTest = 1;
            }
            if ($intTest == 0) {
                // Line colours
                $strClassL = 'tdlb';
                $strClassM = 'tdmb';
                if ($i%2 == 1) {
                    $strClassL = 'tdld';
                    $strClassM = 'tdmd';
                }
                $conttp->setVariable('CLASS_L', $strClassL);
                $conttp->setVariable('CLASS_M', $strClassM);
                $conttp->setVariable('CONFIG_NAME', translate('Not used'));
                $conttp->setVariable('ACTIVE_CONFIG_COUNT', '<span class="checkred">' .translate('failed'). '</span>');
                $conttp->setVariable('DEMON_CFG_OK', '<span class="checkred">' .translate('unused - please delete!').
                    '</span> (' .$line. ')');
                $conttp->parse('configfileline');
                $i++;
            }
        }
    }
    $conttp->parse('configdomain');
}

// Messages
if ($strErrorMessage != '') {
    $conttp->setVariable('ERRORMESSAGE', $strErrorMessage);
}
if ($strInfoMessage != '') {
    $conttp->setVariable('INFOMESSAGE', $strInfoMessage);
}
// Check access rights for adding new objects
if ($myVisClass->checkAccountGroup($prePageKey, 'write') != 0) {
    $conttp->setVariable('ADD_CONTROL', 'disabled="disabled"');
}
$conttp->parse('support');
$conttp->show('support');
//
// Process footer
// ==============
$maintp->setVariable('VERSION_INFO', "<a href='https://sourceforge.net/projects/nagiosql/' "
    . "target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse('footer');
$maintp->show('footer');
