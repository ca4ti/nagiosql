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
// Component : Installer main script
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Path settings
// ===================
$strPattern = '(install/[^/]*.php)';
$preRelPath  = preg_replace($strPattern, '', filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING));
$preBasePath = preg_replace($strPattern, '', filter_input(INPUT_SERVER, 'SCRIPT_FILENAME', FILTER_SANITIZE_STRING));
//
// Define common variables
// =======================
$preContent = $preBasePath.'install/templates/index.htm.tpl';
$preEncode  = 'utf-8';
$preLocale  = $preBasePath.'config/locale';
$filConfig  = $preBasePath.'config/settings.php';
$strLangOpt = '';
$strVersion = '3.4.1';
$intUpdate  = 0;
$intError   = 0;
//
// Include preprocessing file
// ==========================
require $preBasePath.'install/functions/prepend_install.php';
//
// Restart session
// ===============
session_destroy();
session_start([ 'name' => 'nagiosql_install']);
//
// POST parameters
// ===============
$arrLocale = array('zh_CN', 'de_DE', 'da_DK', 'en_GB', 'fr_FR', 'it_IT', 'ja_JP', 'nl_NL', 'pl_PL', 'pt_BR', 'ru_RU',
    'es_ES');
$chkLocale = filter_input(INPUT_POST, 'selLanguage', FILTER_SANITIZE_STRING);
if (!in_array($chkLocale, $arrLocale, true)) {
    $chkLocale = 'no';
}
//
// Language settings
// =================
if (extension_loaded('gettext')) {
    if ($chkLocale == 'no') {
        if (0 === strpos(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING), 'de')) {
            $chkLocale = 'de_DE';
        } else {
            $chkLocale = 'en_GB';
        }
    }
    putenv('LC_ALL=' .$chkLocale. '.' .$preEncode);
    putenv('LANG=' .$chkLocale. '.' .$preEncode);
    setlocale(LC_ALL, $chkLocale. '.' .$preEncode);
    bindtextdomain($chkLocale, $preLocale);
    bind_textdomain_codeset($chkLocale, $preEncode);
    textdomain($chkLocale);
    $arrTemplate['NAGIOS_FAQ'] = $myInstClass->translate('Online Documentation');
    // Language selection field
    $arrTemplate['LANGUAGE']   = $myInstClass->translate('Language');
    foreach ($myInstClass->getLangData() as $key => $elem) {
        $strLangOpt .= "<option value='".$key."' {sel}>".$myInstClass->getLangNameFromCode($key, false)."</option>\n";
        if ($key != $chkLocale) {
            $strLangOpt = str_replace(' {sel}', '', $strLangOpt);
        } else {
            $strLangOpt = str_replace(' {sel}', ' selected', $strLangOpt);
        }
    }
    $arrTemplate['LANG_OPTION'] = $strLangOpt;
} else {
    $intError         = 1;
    $strErrorMessage .= 'Installation cannot continue, please make sure you have the php-gettext extension loaded!';
}
//
// Checking current installation
// =============================
// Does the settings file exist?
if (file_exists($filConfig) && is_readable($filConfig)) {
    $preSettings = parse_ini_file($filConfig, true);
    // Are there any database connection data?
    if (isset($preSettings['db']) && isset($preSettings['db']['server']) && isset($preSettings['db']['port']) &&
        isset($preSettings['db']['database']) && isset($preSettings['db']['username']) &&
        isset($preSettings['db']['password'])) {
        // Old mysql db module is no longer supported
        if (!isset($preSettings['db']['type']) || ($preSettings['db']['type'] == 'mysql') ||
            ($preSettings['db']['type'] == '')) {
            $preSettings['db']['type'] = 'mysqli';
        }
        // Copy settings to session
        $_SESSION['SETS'] = $preSettings;
        // Select database
        $intDBFallback = 0;
        if (($preSettings['db']['type'] == 'mysqli') && extension_loaded('mysqli')) {
            // Initialize mysqli class
            $myDBClass = new functions\MysqliDbClass;
            // Set DB parameters
            $myDBClass->arrParams['server']   = $preSettings['db']['server'];
            $myDBClass->arrParams['port']     = $preSettings['db']['port'];
            $myDBClass->arrParams['username'] = $preSettings['db']['username'];
            $myDBClass->arrParams['password'] = $preSettings['db']['password'];
            $myDBClass->arrParams['database'] = $preSettings['db']['database'];
            $myDBClass->hasDBConnection();
            if ($myDBClass->error == true) {
                $strErrorMessage .= $myInstClass->translate('Database connection failed. Upgrade not available!') .
                    '<br>';
                $strErrorMessage .= str_replace('::', '<br>', $myDBClass->strErrorMessage) . '<br>';
            } else {
                $strSQL    = 'SELECT `category`, `name`, `value` FROM `tbl_settings`';
                $booReturn = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
                if ($booReturn == false) {
                    $strErrorMessage .= $myInstClass->translate('Settings table not available or wrong. '
                            . 'Upgrade not available!'). '<br>';
                    $strErrorMessage .= str_replace('::', '<br>', $myDBClass->strErrorMessage) . '<br>';
                } elseif ($intDataCount != 0) {
                    foreach ($arrDataLines as $elem) {
                        $preSettings[$elem['category']][$elem['name']] = $elem['value'];
                    }
                    $intUpdate = 1;
                }
            }
        } else {
            $strErrorMessage .= $myInstClass->translate('Invalid database type in settings file '
                . '(config/settings.php). Upgrade not available!');
        }
    } else {
        $strErrorMessage .= $myInstClass->translate('Database values in settings file are missing '
            . '(config/settings.php). Upgrade not available!');
    }
} else {
    $strErrorMessage .= $myInstClass->translate('Settings file not found or not readable (config/settings.php). '
        . 'Upgrade not available!');
}
//
// Initial settings (new installation)
// ===================================
$filInit = 'functions/initial_settings.php';
if (file_exists($filInit) && is_readable($filInit)) {
    $preInit = parse_ini_file($filInit, true);
    $_SESSION['init_settings'] = $preInit;
} else {
    $strErrorMessage .= $myInstClass->translate('Default values file is not available or not readable '
        . '(install/functions/initial_settings.php). Installation possible, '
        . 'but without predefined data!');
}
//
// Build content
// =============
$arrTemplate['PAGETITLE']       = '[NagiosQL] ' .$myInstClass->translate('Installation wizard');
$arrTemplate['MAIN_TITLE']      = $myInstClass->translate('Welcome to the NagiosQL installation wizard');
$arrTemplate['TEXT_PART_1']     = $myInstClass->translate('This wizard will help you to install and configure '
    . 'NagiosQL.');
$arrTemplate['TEXT_PART_2']     = $myInstClass->translate('For questions please visit'). ': ';
$arrTemplate['TEXT_PART_3']     = $myInstClass->translate("First let's check your local environment and find out if "
    . 'everything NagiosQL needs is available.');
$arrTemplate['TEXT_PART_4']     = $myInstClass->translate('The basic requirements are:');
$arrTemplate['TEXT_PART_5']     = $myInstClass->translate('PHP 5.5.0 or greater including:');
$arrTemplate['TEXT_PHP_REQ_1']  = $myInstClass->translate('PHP database module:'). ' ' .
    $myInstClass->translate('supported types are'). ' <b>mysqli</b>';
$arrTemplate['TEXT_PHP_REQ_2']  = $myInstClass->translate('PHP module:'). ' <b>session</b>';
$arrTemplate['TEXT_PHP_REQ_3']  = $myInstClass->translate('PHP module:'). ' <b>gettext</b>';
$arrTemplate['TEXT_PHP_REQ_6']  = $myInstClass->translate('PHP module:'). ' <b>filter</b>';
$arrTemplate['TEXT_PHP_REQ_8']  = $myInstClass->translate('PHP module:'). ' <b>FTP</b> ' .
    $myInstClass->translate('(optional)');
$arrTemplate['TEXT_PHP_REQ_10'] = $myInstClass->translate('PECL extension:'). ' <b>SSH</b> ' .
    $myInstClass->translate('(optional)');
$arrTemplate['TEXT_PART_6']     = $myInstClass->translate('php.ini options'). ':';
$arrTemplate['TEXT_INI_REQ_1']  = $myInstClass->translate('file_uploads on (for upload features)');
$arrTemplate['TEXT_INI_REQ_2']  = $myInstClass->translate('session.auto_start needs to be off');
$arrTemplate['TEXT_PART_7']     = $myInstClass->translate('A database server');
$arrTemplate['TEXT_PART_8']     = $myInstClass->translate('Nagios 2.x/3.x/4.x');
$arrTemplate['TEXT_PART_9']     = $myInstClass->translate('NagiosQL version'). ' ' .$strVersion;
$arrTemplate['LOCALE']          = $chkLocale;
$arrTemplate['ONLINE_DOC']      = $myInstClass->translate('Online documentation');
//
// New installation or upgrade
// ===========================
$arrTemplate['NEW_INSTALLATION'] = $myInstClass->translate('START INSTALLATION');
$arrTemplate['UPDATE']           = $myInstClass->translate('START UPDATE');
$arrTemplate['DISABLE_NEW']      = '';
$arrTemplate['UPDATE_ERROR']     = '<div style="color:red;">' .$strErrorMessage. '</div>';
if ($intUpdate == 1) {
    $arrTemplate['DISABLE_UPDATE'] = '';
} else {
    $arrTemplate['DISABLE_UPDATE'] = "disabled=\disabled\"";
}
if ($intError == 1) {
    $arrTemplate['DISABLE_NEW']    = "disabled=\disabled\"";
    $arrTemplate['DISABLE_UPDATE'] = "disabled=\disabled\"";
}
//
// Write content
// =============
$strContent = $myInstClass->parseTemplate($arrTemplate, $preContent);
echo $strContent;
