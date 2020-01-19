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
// Component : Preprocessing script
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
error_reporting(E_ALL & ~E_STRICT);
//
// Timezone settings (>=PHP5.1)
// ============================
if (function_exists('date_default_timezone_set') and function_exists('date_default_timezone_get')) {
    date_default_timezone_set(date_default_timezone_get());
}
//
// Process post/get parameters
// ===========================
$chkInsName   = filter_input(INPUT_POST, 'tfUsername', FILTER_SANITIZE_STRING);
$chkInsPasswd = filter_input(INPUT_POST, 'tfPassword', FILTER_SANITIZE_STRING);
$chkLogout    = filter_input(INPUT_GET, 'logout', FILTER_SANITIZE_STRING, array('options' => array('default' => 'rr')));
//
// Define common variables
// =======================
if ((filter_input(INPUT_GET, 'SETS') != null) || (filter_input(INPUT_POST, 'SETS') != null)) {
    $SETS = ''; // For security reason
}
$strErrorMessage   = '';  // All error messages (red)
$strInfoMessage    = '';  // All information messages (green)
$strConsistMessage = '';  // Consistency message
$tplHeaderVar      = '';
$chkDomainId       = 0;
$chkGroupAdm       = 0;
$intError          = 0;
$setDBVersion      = 'unknown';
$setFileVersion    = '3.4.1';
$setGITVersion     = '2020-01-19';
//
// Start PHP session
// =================
session_start();
//
// Check path settings
// ===================
if (substr_count(filter_input(INPUT_SERVER, 'SCRIPT_NAME', FILTER_SANITIZE_STRING), 'index.php') != 0) {
    $preBasePath = str_replace('//', '/', dirname(filter_input(
        INPUT_SERVER,
        'SCRIPT_FILENAME',
        FILTER_SANITIZE_STRING
    )). '/');
    $preBaseURL  = str_replace('//', '/', dirname(filter_input(
        INPUT_SERVER,
        'SCRIPT_NAME',
        FILTER_SANITIZE_STRING
    )). '/');
    $_SESSION['SETS']['path']['base_url']  = $preBaseURL;
    $_SESSION['SETS']['path']['base_path'] = $preBasePath;
} elseif (!isset($_SESSION['SETS']['path']['base_url']) || !isset($_SESSION['SETS']['path']['base_path'])) {
    header('Location: ../index.php');
    exit;
} else {
    $preBaseURL  = $_SESSION['SETS']['path']['base_url'];
    $preBasePath = $_SESSION['SETS']['path']['base_path'];
}
//
// Start installer
// ===============
$preIniFile = $preBasePath.'config/settings.php';
if (!file_exists($preIniFile) || !is_readable($preIniFile)) {
    header('Location: '.$preBaseURL.'install/index.php');
    exit;
}
//
// Read file settings
// ==================
$SETS = parse_ini_file($preBasePath.'config/settings.php', true);
if (!isset($_SESSION['SETS']['db'])) {
    $_SESSION['SETS']['db'] = $SETS['db'];
}
//
// Include external function/class files
// =====================================
require $preBasePath.'functions/Autoloader.php';
require $preBasePath.'functions/translator.php';
functions\Autoloader::register($preBasePath);
//
// Initialize classes - part 1
// ===========================
$myDBClass = new functions\MysqliDbClass;
$myDBClass->arrParams = $_SESSION['SETS']['db'];
$myDBClass->hasDBConnection();
if ($myDBClass->error == true) {
    $strDBMessage = $myDBClass->strErrorMessage;
    $booError     = $myDBClass->error;
    $intError     = 1;
}
//
// Get additional configuration from the table tbl_settings
// ========================================================
if ($intError == 0) {
    $strSQL    = 'SELECT `category`,`name`,`value` FROM `tbl_settings`';
    $booReturn = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
    if ($booReturn == false) {
        $strErrorMessage .= translate('Error while selecting data from database:'). '::' .$myDBClass->strErrorMessage;
        $intError     = 1;
    } elseif ($intDataCount != 0) {
        if (isset($_SESSION['SETS']['data']['locale']) && ($_SESSION['SETS']['data']['locale'] != '')) {
            $strStoreLanguage = $_SESSION['SETS']['data']['locale'];
        }
        // Save additional configuration information
        for ($i = 0; $i < $intDataCount; $i++) {
            // We use the path settings from file
            if ($arrDataLines[$i]['name'] == 'base_url') {
                continue;
            }
            if ($arrDataLines[$i]['name'] == 'base_path') {
                continue;
            }
            $SETS[$arrDataLines[$i]['category']][$arrDataLines[$i]['name']] = $arrDataLines[$i]['value'];
        }
        if (isset($strStoreLanguage) && ($strStoreLanguage != '')) {
            $SETS['data']['locale'] = $strStoreLanguage;
        }
    }
}
//
// Enable PHP gettext functionality
// ================================
if ($intError == 0) {
    $arrLocale = explode('.', $SETS['data']['locale']);
    $strDomain = $arrLocale[0];
    $strLocale = setlocale(
        LC_ALL,
        $SETS['data']['locale'],
        $SETS['data']['locale']. '.utf-8',
        $SETS['data']['locale']. '.utf-8',
        $SETS['data']['locale']. '.utf8',
        'en_GB',
        'en_GB.utf-8',
        'en_GB.utf8'
    );
    if (!isset($strLocale)) {
        $strErrorMessage .= translate('Error setting the correct locale. Please report this error with the associated '
                                    . "output of 'locale -a'"). '::';
        $intError = 1;
    }
    putenv('LC_ALL=' .$SETS['data']['locale']. '.utf-8');
    putenv('LANG=' .$SETS['data']['locale']. '.utf-8');
    bindtextdomain($strDomain, $preBasePath. 'config/locale');
    bind_textdomain_codeset($strDomain, $SETS['data']['encoding']);
    textdomain($strDomain);
}
//
// Include external function/class files
// =====================================
require_once $preBasePath.'libraries/pear/HTML/Template/IT.php';
if (isset($preFieldvars) && ($preFieldvars == 1)) {
    require $preBasePath.'config/fieldvars.php';
}
//
// Check path settings
// ===================
if (!isset($SETS['path']['base_path']) || ($preBasePath != $SETS['path']['base_path'])) {
    $SETS['path']['base_path'] = $preBasePath;
}
if (!isset($SETS['path']['base_url']) || ($preBaseURL != $SETS['path']['base_url'])) {
    $SETS['path']['base_url'] = $preBaseURL;
}
//
// Add data to the session
// =======================
$_SESSION['SETS']            = $SETS;
$_SESSION['strLoginMessage'] = '';
$_SESSION['startsite']       = $_SESSION['SETS']['path']['base_url']. 'admin.php';
if (!isset($_SESSION['logged_in'])) {
    $_SESSION['logged_in'] = 0;
}
// Reload locale after logout
if (isset($chkLogout) && ($chkLogout == 'yes')) {
    $_SESSION = array();
    $_SESSION['SETS']            = $SETS;
    $_SESSION['logged_in']       = 0;
    $_SESSION['userid']          = 0;
    $_SESSION['groupadm']        = 0;
    $_SESSION['strLoginMessage'] = '';
    $_SESSION['startsite']       = $_SESSION['SETS']['path']['base_url']. 'admin.php';
    // Get default language
    $strSQL      = "SELECT `value` FROM `tbl_settings` WHERE `category`='data' AND `name`='locale'";
    $strLocaleDB = $myDBClass->getFieldData($strSQL);
    if ($strLocaleDB != '') {
        $_SESSION['SETS']['data']['locale'] = $strLocaleDB;
        $SETS['data']['locale']             = $strLocaleDB;
    }
    $arrLocale = explode('.', $SETS['data']['locale']);
    $strDomain = $arrLocale[0];
    $strLocale = setlocale(
        LC_ALL,
        $SETS['data']['locale'],
        $SETS['data']['locale']. '.utf-8',
        $SETS['data']['locale']. '.utf-8',
        $SETS['data']['locale']. '.utf8',
        'en_GB',
        'en_GB.utf-8',
        'en_GB.utf8'
    );
    if (!isset($strLocale)) {
        $strErrorMessage .= translate('Error in setting the correct locale, please report this error with the '
                                    . "associated output of  'locale -a' to bugs@nagiosql.org"). '::';
        $intError = 1;
    }
    putenv('LC_ALL=' .$SETS['data']['locale']. '.utf-8');
    putenv('LANG=' .$SETS['data']['locale']. '.utf-8');
    bindtextdomain($strDomain, $preBasePath . 'config/locale');
    bind_textdomain_codeset($strDomain, $SETS['data']['encoding']);
    textdomain($strDomain);
}
// Hide menu
if (filter_input(INPUT_GET, 'menu') != null) {
    if (filter_input(INPUT_GET, 'menu', FILTER_SANITIZE_STRING) == 'visible') {
        $_SESSION['menu'] = 'visible';
    } elseif (filter_input(INPUT_GET, 'menu', FILTER_SANITIZE_STRING) == 'invisible') {
        $_SESSION['menu'] = 'invisible';
    }
}
//
// Initialize classes
// ==================
$myVisClass     = new functions\NagVisualClass($_SESSION);
$myDataClass    = new functions\NagDataClass($_SESSION);
$myConfigClass  = new functions\NagConfigClass($_SESSION);
$myContentClass = new functions\NagContentClass($_SESSION);
//
// Propagating the classes themselves
// ==================================
$myVisClass->myDBClass         =& $myDBClass;
$myVisClass->myConfigClass     =& $myConfigClass;
//
$myDataClass->myDBClass        =& $myDBClass;
$myDataClass->myVisClass       =& $myVisClass;
$myDataClass->myConfigClass    =& $myConfigClass;
//
$myConfigClass->myDBClass      =& $myDBClass;
$myConfigClass->myDataClass    =& $myDataClass;
//
$myContentClass->myDBClass     =& $myDBClass;
$myContentClass->myVisClass    =& $myVisClass;
$myContentClass->myConfigClass =& $myConfigClass;
if (isset($arrDescription)) {
    $myContentClass->arrDescription = $arrDescription;
}
//
// Version management
// ==================
if ($intError == 0) {
    $setDBVersion = $SETS['db']['version'];
}
//
// Version check
// =============
if (version_compare($setFileVersion, $setDBVersion, '>') && (file_exists($preBasePath. 'install') &&
    is_readable($preBasePath. 'install'))) {
    header('Location: '. $_SESSION['SETS']['path']['base_url'].'install/index.php');
    exit;
}
//
// Browser Check
// =============
$preBrowser = $myVisClass->browserCheck();
//
// Login process
// ==============
$strRemoteUser = filter_input(INPUT_SERVER, 'REMOTE_USER', FILTER_SANITIZE_STRING);
if (isset($strRemoteUser) && ($strRemoteUser != '') && ($_SESSION['logged_in'] == 0) &&
    ($chkLogout != 'yes') && ($chkInsName == '')) {
    $strSQL    = "SELECT * FROM `tbl_user` WHERE `username`='".$strRemoteUser."' AND `wsauth`='1' AND `active`='1'";
    $booReturn = $myDBClass->hasDataArray($strSQL, $arrDataUser, $intDataCount);
    if ($booReturn && ($intDataCount == 1)) {
        // Set session variables
        $_SESSION['username']  = $arrDataUser[0]['username'];
        $_SESSION['userid']    = $arrDataUser[0]['id'];
        $_SESSION['groupadm']  = $arrDataUser[0]['admin_enable'];
        $_SESSION['startsite'] = $_SESSION['SETS']['path']['base_url']. 'admin.php';
        $_SESSION['timestamp'] = time();
        $_SESSION['logged_in'] = 1;
        $_SESSION['domain']    = $arrDataUser[0]['domain'];
        // Update language settings
        $strSQL        = 'SELECT `locale` FROM `tbl_language` '
                       . "WHERE `id`='".$arrDataUser[0]['language']."' AND `active`='1'";
        $strUserLocale = $myDBClass->getFieldData($strSQL);
        if ($strUserLocale != '') {
            $_SESSION['SETS']['data']['locale'] = $strUserLocale;
            $SETS['data']['locale']             = $strUserLocale;
        }
        // Update last login time
        $strSQLUpdate = 'UPDATE `tbl_user` SET `last_login`=NOW() '
                      . "WHERE `username`='".$myDBClass->realEscape($chkInsName)."'";
        $booReturn    = $myDBClass->insertData($strSQLUpdate);
        $myDataClass->strUserName = $arrDataUser[0]['username'];
        $myDataClass->writeLog(translate('Webserver login successfull'));
        $_SESSION['strLoginMessage'] = '';
        // Redirect to start page
        header('Location: ' .$_SESSION['SETS']['path']['protocol']. '://' .
                filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING).$_SESSION['startsite']);
        exit;
    }
}
if (($_SESSION['logged_in'] == 0) && isset($chkInsName) && ($chkInsName != '') && ($intError == 0)) {
    $chkInsName   = $myDBClass->realEscape($chkInsName);
    $chkInsPasswd = $myDBClass->realEscape($chkInsPasswd);
    $strSQL    = 'SELECT * FROM `tbl_user` '
               . "WHERE `username`='".$chkInsName."' AND `password`=MD5('".$chkInsPasswd."') AND `active`='1'";
    $booReturn = $myDBClass->hasDataArray($strSQL, $arrDataUser, $intDataCount);
    if ($booReturn == false) {
        $strErrorMessage = str_replace('::', '<br>', $strErrorMessage);
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
        $_SESSION['strLoginMessage'] = $strErrorMessage;
    } elseif ($intDataCount == 1) {
        // Set session variables
        $_SESSION['username']  = $arrDataUser[0]['username'];
        $_SESSION['userid']    = $arrDataUser[0]['id'];
        $_SESSION['groupadm']  = $arrDataUser[0]['admin_enable'];
        $_SESSION['startsite'] = $_SESSION['SETS']['path']['base_url'] . 'admin.php';
        $_SESSION['timestamp'] = time();
        $_SESSION['logged_in'] = 1;
        $_SESSION['domain']    = $arrDataUser[0]['domain'];
        // Update language settings
        $strSQL = 'SELECT `locale` FROM `tbl_language` '
                . "WHERE `id`='".$arrDataUser[0]['language']."' AND `active`='1'";
        $strUserLocale = $myDBClass->getFieldData($strSQL);
        if ($strUserLocale != '') {
            $_SESSION['SETS']['data']['locale'] = $strUserLocale;
            $SETS['data']['locale']             = $strUserLocale;
        }
        // Update last login time
        $strSQLUpdate = 'UPDATE `tbl_user` SET `last_login`=NOW() '
                      . "WHERE `username`='".$myDBClass->realEscape($chkInsName)."'";
        $booReturn    = $myDBClass->insertData($strSQLUpdate);
        $myDataClass->strUserName = $arrDataUser[0]['username'];
        $myDataClass->writeLog(translate('Login successfull'));
        $_SESSION['strLoginMessage'] = '';
        // Redirect to start page
        header('Location: ' .$_SESSION['SETS']['path']['protocol']. '://' .
                filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING).$_SESSION['startsite']);
        exit;
    } else {
        $_SESSION['strLoginMessage'] = translate('Login failed!');
        $myDataClass->writeLog(translate('Login failed!'). ' - Username: ' .$chkInsName);
        $preNoMain = 0;
    }
}
if (($_SESSION['logged_in'] == 0) && (!isset($intPageID) || ($intPageID != 0)) &&
    (!isset($chkInsName) || ($chkInsName == ''))) {
    header('Location: ' .$_SESSION['SETS']['path']['protocol']. '://' .
            filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING).
            $_SESSION['SETS']['path']['base_url']. 'index.php');
    exit;
}
if (!isset($_SESSION['userid']) && ($_SESSION['logged_in'] == 1)) {
    $_SESSION['logged_in'] = 0;
    header('Location: ' .$_SESSION['SETS']['path']['protocol']. '://' .
            filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING).
            $_SESSION['SETS']['path']['base_url']. 'index.php');
    exit;
}
//
// Review and update login
// =======================
if (($_SESSION['logged_in'] == 1) && ($intError == 0)) {
    $strSQL  = "SELECT * FROM `tbl_user` WHERE `username`='".$myDBClass->realEscape($_SESSION['username'])."'";
    $booReturn = $myDBClass->hasDataArray($strSQL, $arrDataUser, $intDataCount);
    if ($booReturn == false) {
        $strErrorMessage = str_replace('::', '<br>', $strErrorMessage);
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    } elseif ($intDataCount == 1) {
        // Time expired?
        if (time() - $_SESSION['timestamp'] > $_SESSION['SETS']['security']['logofftime']) {
            // Force new login
            $myDataClass->writeLog(translate('Session timeout reached - Seconds:'). ' ' .
                                  (time() - $_SESSION['timestamp']. ' - User: ' .$_SESSION['username']));
            $_SESSION['logged_in'] = 0;
            
            header('Location: ' .$_SESSION['SETS']['path']['protocol']. '://' .
                    filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING).
                    $_SESSION['SETS']['path']['base_url']. 'index.php');
            exit;
        }
        // Check rights
        if (isset($preAccess) && ($preAccess == 1) && (isset($prePageId) && ($prePageId != 0))) {
            $strKey    = $myDBClass->getFieldData("SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=$prePageId");
            $intResult = $myVisClass->checkAccountGroup($strKey, 'read');
            // If no rights - redirect to index page
            if ($intResult != 0) {
                $myDataClass->writeLog(translate('Restricted site accessed:'). ' ' .
                        filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING));
                header('Location: ' .$_SESSION['SETS']['path']['protocol']. '://' .
                        filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING).
                        $_SESSION['SETS']['path']['base_url']. 'index.php');
                exit;
            }
        }
        // Update login time
        $_SESSION['timestamp'] = time();
        if (isset($preContent) && ($preContent == 'index.htm.tpl')) {
            header('Location: ' .$_SESSION['SETS']['path']['protocol']. '://' .
                    filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING).$_SESSION['startsite']);
            exit;
        }
    } else {
        // Force new login
        $myDataClass->writeLog(translate('User not found in database'));
        $_SESSION['logged_in'] = 0;
        header('Location: ' .$_SESSION['SETS']['path']['protocol']. '://' .
                filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING).
                $_SESSION['SETS']['path']['base_url']. 'index.php');
        exit;
    }
}
//
// Check access to current site
// ============================
if (isset($prePageId) && ($prePageId != 1)) {
    if (!isset($_SESSION['userid'])) {
        header('Location: ' .$_SESSION['SETS']['path']['protocol']. '://' .
                filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING).
                $_SESSION['SETS']['path']['base_url']. 'index.php');
        exit;
    }
    $strSQL     = "SELECT `mnuGrpId` FROM `tbl_menu` WHERE `mnuId`=$prePageId";
    $prePageKey = (int)$myDBClass->getFieldData($strSQL);
    if ($myVisClass->checkAccountGroup($prePageKey, 'read') != 0) {
        header('Location: ' .$_SESSION['SETS']['path']['protocol']. '://' .
                filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING).
                $_SESSION['startsite']);
        exit;
    }
}
//
// Insert main template
// ====================
if (isset($preContent) && ($preContent != '') && (!isset($preNoMain) || ($preNoMain != 1))) {
    $arrTplOptions = array('use_preg' => false);
    $maintp = new HTML_Template_IT($preBasePath . 'templates/');
    $maintp->loadTemplatefile('main.htm.tpl', true, true);
    $maintp->setOptions($arrTplOptions);
    $maintp->setVariable('META_DESCRIPTION', 'NagiosQL System Monitoring Administration Tool');
    $maintp->setVariable('AUTHOR', 'NagiosQL Team');
    $maintp->setVariable('LANGUAGE', 'de');
    $maintp->setVariable('PUBLISHER', 'NagiosQL @ Sourceforge');
    if ($_SESSION['logged_in'] == 1) {
        $maintp->setVariable('ADMIN', '<a href="' . $_SESSION['SETS']['path']['base_url'] . 'admin.php" '
                           . 'class="top-link">' .translate('Administration'). '</a>');
        //$maintp->setVariable("PLUGINS","<a href=\"".$_SESSION['SETS']['path']['base_url']."/plugin.php\"
        //class=\"top-link\">".translate('Plugins')."</a>");
    }
    $maintp->setVariable('BASE_PATH', $_SESSION['SETS']['path']['base_url']);
    $maintp->setVariable('ROBOTS', 'noindex,nofollow');
    $maintp->setVariable('PAGETITLE', 'NagiosQL - Version ' .$setDBVersion);
    $maintp->setVariable('IMAGEDIR', $_SESSION['SETS']['path']['base_url'] . 'images/');
    if (isset($prePageId) && ($intError == 0)) {
        $maintp->setVariable('POSITION', $myVisClass->getPosition($prePageId, translate('Administration')));
    }
    $maintp->parse('header');
    $tplHeaderVar = $maintp->get('header');
    //
    // Read domain list
    // ================
    if (($_SESSION['logged_in'] == 1) && ($intError == 0)) {
        $intDomain = filter_input(
            INPUT_POST,
            'selDomain',
            FILTER_VALIDATE_INT,
            array('options' => array('default' => -1))
        );
        if ($intDomain != -1) {
            $_SESSION['domain']          = $intDomain;
            $myVisClass->intDomainId     = $intDomain;
            $myDataClass->intDomainId    = $intDomain;
            $myConfigClass->intDomainId  = $intDomain;
            $myContentClass->intDomainId = $intDomain;
        }
        $arrDataDomain = array();
        $strSQL        = "SELECT * FROM `tbl_datadomain` WHERE `active` <> '0' ORDER BY `domain`";
        $booReturn     = $myDBClass->hasDataArray($strSQL, $arrDataDomain, $intDataCount);
        if ($booReturn == false) {
            $strErrorMessage = str_replace('::', '<br>', $strErrorMessage);
            $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
            $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
        } else {
            $intDomain = 0;
            if ($intDataCount > 0) {
                foreach ($arrDataDomain as $elem) {
                    $intIsDomain = 0;
                    // Check access rights
                    if ($myVisClass->checkAccountGroup($elem['access_group'], 'read') == 0) {
                        $maintp->setVariable('DOMAIN_VALUE', $elem['id']);
                        $maintp->setVariable('DOMAIN_TEXT', $elem['domain']);
                        if (isset($_SESSION['domain']) && ($_SESSION['domain'] == $elem['id'])) {
                            $maintp->setVariable('DOMAIN_SELECTED', 'selected');
                            $intDomain     = $elem['id'];
                            $intIsDomain = 1;
                        }
                        if ($intDomain == -1) {
                            $intDomain   = $elem['id'];
                            $intIsDomain = 1;
                        }
                        $maintp->parse('domainsel');
                    }
                    if ($intIsDomain == 0) {
                        // Select available an domain
                        $strDomAcc = $myVisClass->getAccessGroups('read');
                        $strSQL    = 'SELECT id FROM `tbl_datadomain` '
                                   . "WHERE `active` <> '0' AND `access_group` IN (".$strDomAcc. ') '
                                   . 'ORDER BY domain LIMIT 1';
                        $booReturn = $myDBClass->hasDataArray($strSQL, $arrDataDomain, $intDataCount);
                        if ($booReturn == false) {
                            $strErrorMessage = str_replace('::', '<br>', $strErrorMessage);
                            $myVisClass->processMessage(
                                translate('Error while selecting data from database:'),
                                $strErrorMessage
                            );
                            $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
                        } else {
                            if ($intDataCount != 0) {
                                $intDomain = $arrDataDomain[0]['id'];
                            }
                        }
                    }
                }
                $maintp->setVariable('DOMAIN_INFO', translate('Domain'). ':');
                $maintp->parse('dselect');
                $tplHeaderVar .= $maintp->get('dselect');
            }
        }
    }
    //
    // Show login information
    // ======================
    if ($_SESSION['logged_in'] == 1) {
        $maintp->setVariable('LOGIN_INFO', translate('Logged in:'). ' ' .$_SESSION['username']);
        $maintp->setVariable('LOGOUT_INFO', '<a href="' .$_SESSION['SETS']['path']['base_url'].
            'index.php?logout=yes">' .translate('Logout'). '</a>');
    } else {
        $maintp->setVariable('LOGOUT_INFO', '&nbsp;');
    }
    //
    // Build content menu
    // ==================
    if (isset($prePageId) && ($prePageId != 0)) {
        $maintp->setVariable('MAINMENU', $myVisClass->getMenu($prePageId));
    }
    $maintp->parse('header2');
    $tplHeaderVar .= $maintp->get('header2');
    if (!isset($preShowHeader) || $preShowHeader == 1) {
        echo $tplHeaderVar;
    }
}
//
// Insert content and master template
// ======================================
if (isset($preContent) && ($preContent != '')) {
    $arrTplOptions = array('use_preg' => false);
    if (!file_exists($preBasePath . 'templates/' .$preContent) ||
        !is_readable($preBasePath . 'templates/' .$preContent)) {
        echo '<span style="color:#F00">' .translate('Warning - template file not found or not readable, please '
                                                   . 'check your file permissions! - File: ');
        echo str_replace('//', '/', $preBasePath . 'templates/' .$preContent). '</span><br>';
        exit;
    }
    $conttp = new HTML_Template_IT($preBasePath . 'templates/');
    $conttp->loadTemplatefile($preContent, true, true);
    $conttp->setOptions($arrTplOptions);
    $strRootPath = $_SESSION['SETS']['path']['base_url'];
    if (substr($strRootPath, -1) != '/') {
        $conttp->setVariable('BASE_PATH', $strRootPath);
        $conttp->setVariable('IMAGE_PATH', $strRootPath. 'images/');
    } else {
        $conttp->setVariable('BASE_PATH', $strRootPath);
        $conttp->setVariable('IMAGE_PATH', $strRootPath. 'images/');
    }
    $mastertp = new HTML_Template_IT($preBasePath . 'templates/');
    if (isset($preListTpl) && ($preListTpl != '')) {
        $mastertp->loadTemplatefile($preListTpl, true, true);
    }
    $mastertp->setOptions($arrTplOptions);
}
//
// Process standard get/post parameters
// ====================================
$arrSortDir   = array('ASC', 'DESC');
$arrSortBy    = array(1, 2);
$chkModus     = 'display';
$chkModusGet  = filter_input(INPUT_GET, 'modus', 513, array('options' => array('default' => 'display')));
$chkOrderBy   = filter_input(INPUT_GET, 'orderby', FILTER_SANITIZE_STRING);
$chkOrderDir  = filter_input(INPUT_GET, 'orderdir', FILTER_SANITIZE_STRING);
$chkLimitGet  = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);
$chkModusPost = filter_input(INPUT_POST, 'modus', 513, array('options' => array('default' => 'display')));
$chkHidModify = filter_input(INPUT_POST, 'hidModify', FILTER_SANITIZE_STRING);
$chkSelModify = filter_input(INPUT_POST, 'selModify', FILTER_SANITIZE_STRING);
$hidSortDir   = filter_input(INPUT_POST, 'hidSortDir', FILTER_SANITIZE_STRING);
$hidSortBy    = filter_input(INPUT_POST, 'hidSortBy', FILTER_VALIDATE_INT);
$chkLimit     = filter_input(INPUT_POST, 'hidLimit', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
$chkSelTarDom = filter_input(INPUT_POST, 'selTarDom', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
$chkListId    = filter_input(INPUT_POST, 'hidListId', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
$chkDataId    = filter_input(INPUT_POST, 'hidId', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
$chkActive    = filter_input(INPUT_POST, 'chbActive', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
$chkRegister  = filter_input(INPUT_POST, 'chbRegister', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
$hidActive    = filter_input(INPUT_POST, 'hidActive', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
$hidSort      = filter_input(INPUT_POST, 'hidSort', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
$chkStatus    = filter_input(INPUT_POST, 'hidStatus', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
if ($chkModusGet != 'display') {
    $chkModus = $chkModusGet;
}
if ($chkModusPost != 'display') {
    $chkModus = $chkModusPost;
}
if (!in_array($hidSortDir, $arrSortDir, true)) {
    $hidSortDir = 'ASC';
}
if (!in_array($hidSortBy, $arrSortBy, true)) {
    $hidSortBy = 1;
}
if (in_array($chkOrderDir, $arrSortDir, true)) {
    $hidSortDir = $chkOrderDir;
}
if (in_array($chkOrderBy, $arrSortBy, true)) {
    $hidSortBy = $chkOrderBy;
}
//
// Setting some variables
// ======================
if ($chkModus == 'add') {
    $chkSelModify = '';
}
if ($chkHidModify != '') {
    $chkSelModify = $chkHidModify;
}
if (isset($chkLimitGet)) {
    $chkLimit     = $chkLimitGet;
}
if (isset($_SESSION['domain'])) {
    $chkDomainId  = $_SESSION['domain'];
}
if (isset($_SESSION['groupadm'])) {
    $chkGroupAdm  = $_SESSION['groupadm'];
}
if (isset($_SESSION['strLoginMessage'])) {
    $_SESSION['strLoginMessage'] .= str_replace('::', '<br>', $strErrorMessage);
}
$myConfigClass->getDomainData('version', $intVersion);
$myConfigClass->getDomainData('enable_common', $setEnableCommon);
if (isset($preTableName)) {
    if ($setEnableCommon != 0) {
        $strDomainWhere  = " (`$preTableName`.`config_id`=$chkDomainId OR `$preTableName`.`config_id`=0) ";
        $strDomainWhere2 = " (`config_id`=$chkDomainId OR `config_id`=0) ";
    } else {
        $strDomainWhere  = " (`$preTableName`.`config_id`=$chkDomainId) ";
        $strDomainWhere2 = " (`config_id`=$chkDomainId) ";
    }
}
// Row sort variables
if ($hidSortDir == 'ASC') {
    $setSortDir = 'DESC';
} else {
    $setSortDir = 'ASC';
}
if (isset($preContent) && ($preContent != '')) {
    if ($hidSortBy == 2) {
        $mastertp->setVariable('SORT_IMAGE_1', '');
    } else {
        $hidSortBy = 1;
        $mastertp->setVariable('SORT_IMAGE_2', '');
    }
    $setSortPicture = $_SESSION['SETS']['path']['base_url']. 'images/sort_' .strtolower($hidSortDir). '.png';
    $mastertp->setVariable('SORT_DIR_' .$hidSortBy, $setSortDir);
    $mastertp->setVariable('SORT_IMAGE_' .$hidSortBy, "<img src=\"$setSortPicture\" alt=\"$hidSortDir\" "
                         . "title=\"$hidSortDir\" width=\"15\" height=\"14\" border=\"0\">");
    $mastertp->setVariable('SORT_DIR', $hidSortDir);
    $mastertp->setVariable('SORT_BY', $hidSortBy);
}
//
// Set class variables
// ===================
if (isset($preContent) && ($preContent != '')) {
    $myVisClass->myContentTpl = $conttp;
    $myVisClass->intDataId    = $chkListId;
}
