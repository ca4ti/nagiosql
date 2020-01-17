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
// Component : Settings configuration
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
$prePageId    = 38;
$preContent   = 'admin/settings.htm.tpl';
$preAccess    = 1;
$preFieldvars = 1;
$arrSQL       = array();
//
// Include preprocessing files
// ===========================
require $preBasePath.'functions/prepend_adm.php';
require $preBasePath.'functions/prepend_content.php';
//
// Process initial values
// ======================
if (filter_input(INPUT_POST, 'tfValue1') == null) {
    $chkTfValue1  = $SETS['path']['tempdir'];
}
if (filter_input(INPUT_POST, 'tfValue2') == null) {
    $chkTfValue2  = $SETS['data']['encoding'];
}
if (filter_input(INPUT_POST, 'tfValue3') == null) {
    $chkTfValue3  = $SETS['db']['server'];
}
if (filter_input(INPUT_POST, 'tfValue4') == null) {
    $chkTfValue4  = $SETS['db']['port'];
}
if (filter_input(INPUT_POST, 'tfValue5') == null) {
    $chkTfValue5  = $SETS['db']['database'];
}
if (filter_input(INPUT_POST, 'tfValue6') == null) {
    $chkTfValue6  = $SETS['db']['username'];
}
if (filter_input(INPUT_POST, 'tfValue7') == null) {
    $chkTfValue7  = $SETS['db']['password'];
}
if (filter_input(INPUT_POST, 'tfValue8') == null) {
    $chkTfValue8  = $SETS['security']['logofftime'];
}
if (filter_input(INPUT_POST, 'tfValue9') == null) {
    $chkTfValue9  = $SETS['common']['pagelines'];
}
if (filter_input(INPUT_POST, 'tfValue10') == null) {
    $chkTfValue10 = $SETS['network']['proxyserver'];
}
if (filter_input(INPUT_POST, 'tfValue11') == null) {
    $chkTfValue11 = $SETS['network']['proxyuser'];
}
if (filter_input(INPUT_POST, 'tfValue12') == null) {
    $chkTfValue12 = $SETS['network']['proxypasswd'];
}
if (filter_input(INPUT_POST, 'selValue3') == null) {
    $chkSelValue3 = $SETS['security']['wsauth'];
}
if (filter_input(INPUT_POST, 'selValue4') == null) {
    $chkSelValue4 = $SETS['common']['seldisable'];
}
if (filter_input(INPUT_POST, 'radValue1') == null) {
    $chkRadValue1 = $SETS['common']['tplcheck'];
}
if (filter_input(INPUT_POST, 'radValue2') == null) {
    $chkRadValue2 = $SETS['common']['updcheck'];
}
if (filter_input(INPUT_POST, 'radValue3') == null) {
    $chkRadValue3 = $SETS['network']['proxy'];
}
if (filter_input(INPUT_POST, 'radValue4') == null) {
    $chkRadValue4 = $SETS['performance']['parents'];
}
//
// Save changes
// ============
if (filter_input(INPUT_POST, 'selValue1')) {
    //
    // Write settings to database
    // ==========================
    if ($chkSelValue1 == 2) {
        $strProtocol = 'https';
    } else {
        $strProtocol = 'http';
    }
    $strLocale = $myDBClass->getFieldData("SELECT `locale` FROM `tbl_language` WHERE `id`='".$chkSelValue2."'");
    if ($strLocale == '') {
        $strLocale = 'en_GB';
    }
    $SETS['path']['protocol'] = $strProtocol;
    $SETS['data']['locale']   = $strLocale;
    // Check Proxy via curl
    if (!function_exists('curl_init')) {
        $myVisClass->processMessage(translate('Curl module not loaded, Proxy will be deactivated!'), $strErrorMessage);
        $chkRadValue3 = 0;
    }
    // Check base paths
    $strSQLBase = "UPDATE `tbl_settings` SET `value`='%s' WHERE `category`='%s' AND `name`='%s'";
    $arrSQL[]   = sprintf($strSQLBase, $strProtocol, 'path', 'protocol');
    $arrSQL[]   = sprintf($strSQLBase, $chkTfValue1, 'path', 'tempdir');
    $arrSQL[]   = sprintf($strSQLBase, $preRelPath, 'path', 'base_url');
    $arrSQL[]   = sprintf($strSQLBase, $preBasePath, 'path', 'base_path');
    $arrSQL[]   = sprintf($strSQLBase, $strLocale, 'data', 'locale');
    $arrSQL[]   = sprintf($strSQLBase, $chkTfValue2, 'data', 'encoding');
    $arrSQL[]   = sprintf($strSQLBase, $chkTfValue8, 'security', 'logofftime');
    $arrSQL[]   = sprintf($strSQLBase, $chkSelValue3, 'security', 'wsauth');
    $arrSQL[]   = sprintf($strSQLBase, $chkTfValue9, 'common', 'pagelines');
    $arrSQL[]   = sprintf($strSQLBase, $chkSelValue4, 'common', 'seldisable');
    $arrSQL[]   = sprintf($strSQLBase, $chkRadValue1, 'common', 'tplcheck');
    $arrSQL[]   = sprintf($strSQLBase, $chkRadValue2, 'common', 'updcheck');
    $arrSQL[]   = sprintf($strSQLBase, $chkRadValue3, 'network', 'proxy');
    $arrSQL[]   = sprintf($strSQLBase, $chkTfValue10, 'network', 'proxyserver');
    $arrSQL[]   = sprintf($strSQLBase, $chkTfValue11, 'network', 'proxyuser');
    $arrSQL[]   = sprintf($strSQLBase, $chkTfValue12, 'network', 'proxypasswd');
    $arrSQL[]   = sprintf($strSQLBase, $chkRadValue4, 'performance', 'parents');
    foreach ($arrSQL as $elem) {
        $booReturn = $myDBClass->insertData($elem);
        if ($booReturn == false) {
            $myVisClass->processMessage(
                translate('An error occured while writing settings to database:'),
                $strErrorMessage
            );
            $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
        }
    }
    // Write db settings to file
    if (is_writable($preBasePath. 'config/settings.php')) {
        $filSettings = fopen($preBasePath. 'config/settings.php', 'wb');
        if ($filSettings) {
            fwrite($filSettings, "<?php\n");
            fwrite($filSettings, "exit;\n");
            fwrite($filSettings, "?>\n");
            fwrite($filSettings, ";///////////////////////////////////////////////////////////////////////////////\n");
            fwrite($filSettings, ";\n");
            fwrite($filSettings, "; NagiosQL\n");
            fwrite($filSettings, ";\n");
            fwrite($filSettings, ";///////////////////////////////////////////////////////////////////////////////\n");
            fwrite($filSettings, ";\n");
            fwrite($filSettings, "; Project  : NagiosQL\n");
            fwrite($filSettings, "; Component: Database Configuration\n");
            fwrite($filSettings, "; Website  : https://sourceforge.net/projects/nagiosql/\n");
            fwrite($filSettings, '; Date     : ' .date('F j, Y, g:i a')."\n");
            fwrite($filSettings, '; Version  : ' .$setFileVersion."\n");
            fwrite($filSettings, ";\n");
            fwrite($filSettings, ";///////////////////////////////////////////////////////////////////////////////\n");
            fwrite($filSettings, "[db]\n");
            fwrite($filSettings, "type         = 'mysqli'\n");
            fwrite($filSettings, 'server       = \'' .$chkTfValue3. "'\n");
            fwrite($filSettings, 'port         = \'' .$chkTfValue4. "'\n");
            fwrite($filSettings, 'database     = \'' .$chkTfValue5. "'\n");
            fwrite($filSettings, 'username     = \'' .$chkTfValue6. "'\n");
            fwrite($filSettings, 'password     = \'' .$chkTfValue7. "'\n");
            fwrite($filSettings, "[path]\n");
            fwrite($filSettings, 'base_url     = \'' .$preRelPath. "'\n");
            fwrite($filSettings, 'base_path    = \'' .$preBasePath. "'\n");
            fclose($filSettings);
            // Activate new language settings
            $arrLocale = explode('.', $strLocale);
            $strDomain = $arrLocale[0];
            $loc = setlocale(
                LC_ALL,
                $strLocale,
                $strLocale. '.utf-8',
                $strLocale. '.utf-8',
                $strLocale. '.utf8',
                'en_GB',
                'en_GB.utf-8',
                'en_GB.utf8'
            );
            if (!isset($loc)) {
                $myVisClass->processMessage(translate('Error setting the correct locale. Please report this error '
                        . "with the associated output of 'locale -a'"), $strErrorMessage);
            }
            putenv('LC_ALL=' .$strLocale. '.utf-8');
            putenv('LANG=' .$strLocale. '.utf-8');
            bindtextdomain($strLocale, $preBasePath. 'config/locale');
            bind_textdomain_codeset($strLocale, $chkTfValue2);
            textdomain($strLocale);
            $myVisClass->processMessage(translate('Settings were changed'), $strInfoMessage);
        } else {
            $myVisClass->processMessage(translate('An error occured while writing settings.php. Please '
                    . 'check permissions!'), $strErrorMessage);
        }
    } else {
        $myVisClass->processMessage($preBasePath. 'config/settings.php ' .translate('is not writeable, please '
                . 'check permissions!'), $strErrorMessage);
    }
}
//
// Start content
// =============
$conttp->setVariable('TITLE', translate('Configure Settings'));
foreach ($arrDescription as $elem) {
    $conttp->setVariable($elem['name'], $elem['string']);
}
$conttp->setVariable('ACTION_INSERT', filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING));
$conttp->setVariable('LANG_DESCRIPTION', translate('Change your current NagiosQL settings (e.g. Database user, '
        . 'Language).'));
//
// Path settings
// =============
$conttp->setVariable('PATH', translate('Path'));
$conttp->setVariable('TEMPDIR_NAME', translate('Temporary Directory'));
$conttp->setVariable('TEMPDIR_VALUE', htmlspecialchars($chkTfValue1, ENT_QUOTES, 'utf-8'));
$conttp->setVariable('PROTOCOL_NAME', translate('Server protocol'));
$conttp->setVariable(strtoupper($SETS['path']['protocol']). '_SELECTED', 'selected');
//
// Data settings
// =============
$conttp->setVariable('DATA', translate('Language'));
$conttp->setVariable('LOCALE', translate('Language'));
// Process language selection field
$strSQL    = "SELECT * FROM `tbl_language` WHERE `active`='1' ORDER BY `id`";
$booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
if ($booReturn && ($intDataCount != 0)) {
    foreach ($arrData as $elem) {
        $conttp->setVariable('LANGUAGE_ID', $elem['id']);
        $conttp->setVariable('LANGUAGE_NAME', translate($elem['language']));
        if ($elem['locale'] == $SETS['data']['locale']) {
            $conttp->setVariable('LANGUAGE_SELECTED', 'selected');
        }
        $conttp->parse('language');
    }
} else {
    $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
    $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
}
$conttp->setVariable('ENCODING_NAME', translate('Encoding'));
$conttp->setVariable('ENCODING_VALUE', htmlspecialchars($chkTfValue2, ENT_QUOTES, 'utf-8'));
//
// Database settings
// =================
$conttp->setVariable('DB', translate('Database'));
$conttp->setVariable('SERVER_NAME', translate('MySQL Server'));
$conttp->setVariable('SERVER_VALUE', htmlspecialchars($chkTfValue3, ENT_QUOTES, 'utf-8'));
$conttp->setVariable('SERVER_PORT', translate('MySQL Server Port'));
$conttp->setVariable('PORT_VALUE', htmlspecialchars($chkTfValue4, ENT_QUOTES, 'utf-8'));
$conttp->setVariable('DATABASE_NAME', translate('Database name'));
$conttp->setVariable('DATABASE_VALUE', htmlspecialchars($chkTfValue5, ENT_QUOTES, 'utf-8'));
$conttp->setVariable('USERNAME_NAME', translate('Database user'));
$conttp->setVariable('USERNAME_VALUE', htmlspecialchars($chkTfValue6, ENT_QUOTES, 'utf-8'));
$conttp->setVariable('PASSWORD_NAME', translate('Database password'));
$conttp->setVariable('PASSWORD_VALUE', htmlspecialchars($chkTfValue7, ENT_QUOTES, 'utf-8'));
//
// Security settings
// =================
$conttp->setVariable('SECURITY', translate('Security'));
$conttp->setVariable('LOGOFFTIME_NAME', translate('Session auto logoff time'));
$conttp->setVariable('LOGOFFTIME_VALUE', htmlspecialchars($chkTfValue8, ENT_QUOTES, 'utf-8'));
$conttp->setVariable('WSAUTH_NAME', translate('Authentication type'));
$conttp->setVariable('WSAUTH_' .$chkSelValue3. '_SELECTED', 'selected');
//
// Common settings
// ===============
$conttp->setVariable('COMMON', translate('Common'));
$conttp->setVariable('PAGELINES_NAME', translate('Data lines per page'));
$conttp->setVariable('PAGELINES_VALUE', htmlspecialchars($chkTfValue9, ENT_QUOTES, 'utf-8'));
$conttp->setVariable('SELDISABLE_NAME', translate('Selection method'));
$conttp->setVariable('SELDISABLE_' .$chkSelValue4. '_SELECTED', 'selected');
//
// Template Check
// ==============
$conttp->setVariable('TEMPLATE_CHECK', translate('Template warn message'));
$conttp->setVariable('LANG_ENABLE', translate('Enable'));
$conttp->setVariable('LANG_DISABLE', translate('Disable'));
$conttp->setVariable('TPL_CHECK_' .$chkRadValue1. '_CHECKED', 'checked');
//
// Online version check
// ====================
$conttp->setVariable('CLASS_NAME_1', 'elementHide');
$conttp->setVariable('CLASS_NAME_2', 'elementHide');
$conttp->setVariable('UPDATE_CHECK', translate('Online version check'));
$conttp->setVariable('UPD_CHECK_' .$chkRadValue2. '_CHECKED', 'checked');
if ($chkRadValue2 == 1) {
    $conttp->setVariable('CLASS_NAME_1', 'elementShow');
}
//
// Online update proxy settings
// ============================
$conttp->setVariable('UPD_PROXY_CHECK', translate('Proxyserver'));
$conttp->setVariable('UPD_PROXY_' .$chkRadValue3. '_CHECKED', 'checked');
if (($chkRadValue3 == 1) && ($chkRadValue2 == 1)) {
    $conttp->setVariable('CLASS_NAME_2', 'elementShow');
}
$conttp->setVariable('UPD_PROXY_SERVER', translate('Proxy Address'));
$conttp->setVariable('UPD_PROXY_SERVER_VALUE', htmlspecialchars($chkTfValue10, ENT_QUOTES, 'utf-8'));
$conttp->setVariable('UPD_PROXY_USERNAME', translate('Proxy Username (optional)'));
$conttp->setVariable('UPD_PROXY_USERNAME_VALUE', htmlspecialchars($chkTfValue11, ENT_QUOTES, 'utf-8'));
$conttp->setVariable('UPD_PROXY_PASSWORD', translate('Proxy Password (optional)'));
$conttp->setVariable('UPD_PROXY_PASSWORD_VALUE', htmlspecialchars($chkTfValue12, ENT_QUOTES, 'utf-8'));
//
// Performance options
// ===================
$conttp->setVariable('PERFORMANCE', translate('Performance options'));
$conttp->setVariable('SHOW_PARENTS', translate('Show object parents'));
$conttp->setVariable('PAR_CHECK_' .$chkRadValue4. '_CHECKED', 'checked');
//
// Requirements of form
// ====================
$conttp->setVariable('LANG_SAVE', translate('Save'));
$conttp->setVariable('LANG_ABORT', translate('Abort'));
$conttp->setVariable('LANG_REQUIRED', translate('required'));
$conttp->setVariable('ERRORMESSAGE', $strErrorMessage);
$conttp->setVariable('INFOMESSAGE', $strInfoMessage);
//
// Check access rights for adding new objects
// ==========================================
if ($myVisClass->checkAccountGroup($prePageKey, 'write') != 0) {
    $conttp->setVariable('ADD_CONTROL', 'disabled="disabled"');
}
$conttp->parse('settingssite');
$conttp->show('settingssite');
//
// Footer ausgeben
// ===============
$maintp->setVariable('VERSION_INFO', "<a href='https://sourceforge.net/projects/nagiosql/' "
        . "target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse('footer');
$maintp->show('footer');
