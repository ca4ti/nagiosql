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
// Component : Installer script - check page
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
$preContent = $preBasePath.'install/templates/install.htm.tpl';
$preEncode  = 'utf-8';
$preLocale  = $preBasePath.'config/locale';
$chkModus   = 'none';
//
// Include preprocessing file
// ==========================
require $preBasePath.'install/functions/prepend_install.php';
//
// Actual database files
// =====================
$preSqlNewInstall = $preBasePath.'install/sql/nagiosQL_v341_db_mysql.sql';
$preSqlUpdateLast = $preBasePath.'install/sql/update_340_341.sql';
$preNagiosQL_ver  = '3.4.1';
//
// Process initial value
// =====================
if (!isset($_SESSION['init_settings'])) {
    header('Location: index.php');
    exit;
}
$strInitDBtype   = isset($_SESSION['SETS']['db']['type'])     ? $_SESSION['SETS']['db']['type']
    : $_SESSION['init_settings']['db']['type'];
$strInitDBserver = isset($_SESSION['SETS']['db']['server'])   ? $_SESSION['SETS']['db']['server']
    : $_SESSION['init_settings']['db']['server'];
$strInitDBname   = isset($_SESSION['SETS']['db']['database']) ? $_SESSION['SETS']['db']['database']
    : $_SESSION['init_settings']['db']['database'];
$strInitDBuser   = isset($_SESSION['SETS']['db']['username']) ? $_SESSION['SETS']['db']['username']
    : $_SESSION['init_settings']['db']['username'];
$strInitDBpass   = isset($_SESSION['SETS']['db']['password']) ? $_SESSION['SETS']['db']['password']
    : $_SESSION['init_settings']['db']['password'];
$strInitDBport   = isset($_SESSION['SETS']['db']['port'])     ? $_SESSION['SETS']['db']['port']
    : $_SESSION['init_settings']['db']['port'];
//
// Init session parameters
// =======================
if (!isset($_SESSION['install']['jscript'])) {
    $_SESSION['install']['jscript']    = 'no';
}
if (!isset($_SESSION['install']['locale'])) {
    $_SESSION['install']['locale']     = 'en_GB';
}
if (!isset($_SESSION['install']['dbtype'])) {
    $_SESSION['install']['dbtype']     = $strInitDBtype;
}
if (!isset($_SESSION['install']['dbserver'])) {
    $_SESSION['install']['dbserver']   = $strInitDBserver;
}
if (!isset($_SESSION['install']['localsrv'])) {
    $_SESSION['install']['localsrv']   = '';
}
if (!isset($_SESSION['install']['dbname'])) {
    $_SESSION['install']['dbname']     = $strInitDBname;
}
if (!isset($_SESSION['install']['dbuser'])) {
    $_SESSION['install']['dbuser']     = $strInitDBuser;
}
if (!isset($_SESSION['install']['dbpass'])) {
    $_SESSION['install']['dbpass']     = $strInitDBpass;
}
if (!isset($_SESSION['install']['admuser'])) {
    $_SESSION['install']['admuser']    = 'root';
}
if (!isset($_SESSION['install']['admpass'])) {
    $_SESSION['install']['admpass']    = '';
}
if (!isset($_SESSION['install']['qluser'])) {
    $_SESSION['install']['qluser']     = 'admin';
}
if (!isset($_SESSION['install']['qlpass'])) {
    $_SESSION['install']['qlpass']     = '';
}
if (!isset($_SESSION['install']['dbport'])) {
    $_SESSION['install']['dbport']     = $strInitDBport;
}
if (!isset($_SESSION['install']['dbdrop'])) {
    $_SESSION['install']['dbdrop']     = 0;
}
if (!isset($_SESSION['install']['sample'])) {
    $_SESSION['install']['sample']     = 0;
}
if (!isset($_SESSION['install']['version'])) {
    $_SESSION['install']['version']    = $preNagiosQL_ver;
}
if (!isset($_SESSION['install']['createpath'])) {
    $_SESSION['install']['createpath'] = 0;
}
if (!isset($_SESSION['install']['qlpath'])) {
    $_SESSION['install']['qlpath']     = '/etc/nagiosql';
}
if (!isset($_SESSION['install']['nagpath'])) {
    $_SESSION['install']['nagpath']    = '/etc/nagios';
}
//
// POST parameters
// ===============
$arrStep = array(1,2,3);
$chkStep = filter_input(INPUT_POST, 'hidStep', FILTER_VALIDATE_INT);
if (!in_array($chkStep, $arrStep, true)) {
    $chkStep = 1;
}
$chkStepG = filter_input(INPUT_GET, 'step', FILTER_VALIDATE_INT);
if (($chkStepG != null) && in_array($chkStepG, $arrStep, true)) {
    $chkStep = $chkStepG;
}
//
// Set session values
// ==================
$_SESSION['install']['locale']     = (filter_input(INPUT_POST, 'hidLocale') != null) ?
    filter_input(INPUT_POST, 'hidLocale', FILTER_SANITIZE_STRING)    : $_SESSION['install']['locale'];
$_SESSION['install']['jscript']    = (filter_input(INPUT_POST, 'hidJScript') != null) ?
    filter_input(INPUT_POST, 'hidJScript', FILTER_SANITIZE_STRING)   : $_SESSION['install']['jscript'];
$_SESSION['install']['dbtype']     = (filter_input(INPUT_POST, 'selDBtype') != null) ?
    filter_input(INPUT_POST, 'selDBtype', FILTER_SANITIZE_STRING)    : $_SESSION['install']['dbtype'];
$_SESSION['install']['dbserver']   = (filter_input(INPUT_POST, 'tfDBserver') != null) ?
    filter_input(INPUT_POST, 'tfDBserver', FILTER_SANITIZE_STRING)   : $_SESSION['install']['dbserver'];
$_SESSION['install']['localsrv']   = (filter_input(INPUT_POST, 'tfLocalSrv') != null) ?
    filter_input(INPUT_POST, 'tfLocalSrv', FILTER_SANITIZE_STRING)   : $_SESSION['install']['localsrv'];
$_SESSION['install']['dbname']     = (filter_input(INPUT_POST, 'tfDBname') != null) ?
    filter_input(INPUT_POST, 'tfDBname', FILTER_SANITIZE_STRING)     : $_SESSION['install']['dbname'];
$_SESSION['install']['dbuser']     = (filter_input(INPUT_POST, 'tfDBuser') != null) ?
    filter_input(INPUT_POST, 'tfDBuser', FILTER_SANITIZE_STRING)     : $_SESSION['install']['dbuser'];
$_SESSION['install']['dbpass']     = (filter_input(INPUT_POST, 'tfDBpass') != null) ?
    filter_input(INPUT_POST, 'tfDBpass', FILTER_SANITIZE_STRING)     : $_SESSION['install']['dbpass'];
$_SESSION['install']['admuser']    = (filter_input(INPUT_POST, 'tfDBprivUser') != null) ?
    filter_input(INPUT_POST, 'tfDBprivUser', FILTER_SANITIZE_STRING) : $_SESSION['install']['admuser'];
$_SESSION['install']['admpass']    = (filter_input(INPUT_POST, 'tfDBprivPass') != null) ?
    filter_input(INPUT_POST, 'tfDBprivPass', FILTER_SANITIZE_STRING) : $_SESSION['install']['admpass'];
$_SESSION['install']['qluser']     = (filter_input(INPUT_POST, 'tfQLuser') != null) ?
    filter_input(INPUT_POST, 'tfQLuser', FILTER_SANITIZE_STRING)     : $_SESSION['install']['qluser'];
$_SESSION['install']['qlpass']     = (filter_input(INPUT_POST, 'tfQLpass') != null) ?
    filter_input(INPUT_POST, 'tfQLpass', FILTER_SANITIZE_STRING)     : $_SESSION['install']['qlpass'];
$_SESSION['install']['qlpath']     = (filter_input(INPUT_POST, 'tfQLpath') != null) ?
    filter_input(INPUT_POST, 'tfQLpath', FILTER_SANITIZE_STRING)     : $_SESSION['install']['qlpath'];
$_SESSION['install']['nagpath']     = (filter_input(INPUT_POST, 'tfNagiosPath') != null) ?
    filter_input(INPUT_POST, 'tfNagiosPath', FILTER_SANITIZE_STRING) : $_SESSION['install']['nagpath'];
$_SESSION['install']['dbdrop']     = filter_input(
    INPUT_POST,
    'chbDrop',
    FILTER_VALIDATE_INT,
    array('options' => array('default' => 0))
);
$_SESSION['install']['sample'] = filter_input(
    INPUT_POST,
    'chbSample',
    FILTER_VALIDATE_INT,
    array('options' => array('default' => 0))
);
$_SESSION['install']['createpath'] = filter_input(
    INPUT_POST,
    'chbPath',
    FILTER_VALIDATE_INT,
    array('options' => array('default' => 0))
);
$strSqlFile =  str_replace('DBTYPE', $_SESSION['install']['dbtype'], $preSqlNewInstall);
if (filter_input(INPUT_POST, 'butNewInstall') != null) {
    $chkModus = 'Installation';
}
if (filter_input(INPUT_POST, 'butUpgrade') != null) {
    $chkModus = 'Update';
}
if (!isset($_SESSION['install']['mode'])) {
    $_SESSION['install']['mode'] = $chkModus;
}
//
// Language settings
// =================
if (extension_loaded('gettext')) {
    putenv('LC_ALL=' .$_SESSION['install']['locale'].$preEncode);
    putenv('LANG=' .$_SESSION['install']['locale'].$preEncode);
    // GETTEXT domain
    setlocale(LC_ALL, $_SESSION['install']['locale']. '.' .$preEncode);
    bindtextdomain($_SESSION['install']['locale'], $preLocale);
    bind_textdomain_codeset($_SESSION['install']['locale'], $preEncode);
    textdomain($_SESSION['install']['locale']);
}
$myInstClass->arrSession = $_SESSION;
//
// Content in buffer laden
// =======================
ob_start();
include 'step' .$chkStep. '.php';
$strContentRaw = ob_get_contents();
ob_end_clean();
//
// Build content
// =============
$arrTemplate['PAGETITLE']  = '[NagiosQL] Installation Wizard';
$arrTemplate['MAIN_TITLE'] = $myInstClass->translate('Welcome to the NagiosQL Installation Wizard');
$arrTemplate['CONTENT']    = $strContentRaw;
//
// Write content
// =============
$myInstClass->filTemplate = $preContent;
$strContent = $myInstClass->parseTemplate($arrTemplate, $preContent);
echo $strContent;
