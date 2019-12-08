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
// Component : Installer script - step 2
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Prevent this file from direct access
// ====================================
if (preg_match('#' . basename(__FILE__) . '#', htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'utf-8'))) {
    header('Location: install.php');
    exit;
}
//
// Define common variables
// =======================
$preIncludeContent = $preBasePath.'install/templates/step2.htm.tpl';
$intError          = 0;
//
// Build content
// =============
$arrTemplate['PASSWD_MESSAGE'] = $myInstClass->translate('The NagiosQL first passwords are not equal!');
$arrTemplate['FIELDS_MESSAGE'] = $myInstClass->translate('Please fill in all fields marked with an *');
$arrTemplate['STEP1_BOX']      = $myInstClass->translate('Requirements');
$arrTemplate['STEP2_BOX']      = $myInstClass->translate('Installation');
$arrTemplate['STEP3_BOX']      = $myInstClass->translate('Finish');
$arrTemplate['STEP2_TITLE']    = 'NagiosQL ' .$myInstClass->translate('Installation'). ': ' .
                                 $myInstClass->translate('Setup');
$arrTemplate['STEP2_TEXT1_1']  = $myInstClass->translate('Please complete the form below. Mandatory fields marked '
                               . '<em>*</em>');
$arrTemplate['STEP2_TEXT2_1']  = $myInstClass->translate('Database Configuration');
$arrTemplate['STEP2_TEXT2_2']  = $myInstClass->translate('Database Type');
$strSelected = '';
if (is_array($_SESSION['install']['dbtype_available']) && (count($_SESSION['install']['dbtype_available']) != 0)) {
    $strDBType = '';
    foreach ((array)$_SESSION['install']['dbtype_available'] as $elem) {
        if (isset($_SESSION['install']['dbtype']) && ($_SESSION['install']['dbtype'] == $elem)) {
            $strSelected = 'selected="selected"';
        }
        $strDBType .= '<option value="' .$elem."\" $strSelected>".$elem."</option>\n";
    }
} else {
    $strDBType .= "<option value=\"mysql\" $strSelected>mysql</option>\n";
}
$arrTemplate['STEP2_VALUE2_2']    = $strDBType;
$arrTemplate['STEP2_TEXT2_3']    = $myInstClass->translate('Database Server');
$arrTemplate['STEP2_VALUE2_3']    = htmlspecialchars($_SESSION['install']['dbserver'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT2_4']    = $myInstClass->translate('Local hostname or IP address');
if (htmlspecialchars($_SESSION['install']['dbserver'], ENT_QUOTES, 'utf-8') == 'localhost') {
    $arrTemplate['STEP2_VALUE2_4'] = htmlspecialchars($_SESSION['install']['dbserver'], ENT_QUOTES, 'utf-8');
} else {
    $arrTemplate['STEP2_VALUE2_4'] = filter_input(INPUT_SERVER, 'SERVER_ADDR', FILTER_SANITIZE_STRING);
}
$arrTemplate['STEP2_TEXT2_5']    = $myInstClass->translate('Database Server Port');
$arrTemplate['STEP2_VALUE2_5']    = htmlspecialchars($_SESSION['install']['dbport'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT2_6']    = $myInstClass->translate('Database name');
$arrTemplate['STEP2_VALUE2_6']    = htmlspecialchars($_SESSION['install']['dbname'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT2_7']    = $myInstClass->translate('NagiosQL DB User');
$arrTemplate['STEP2_VALUE2_7']    = htmlspecialchars($_SESSION['install']['dbuser'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT2_8']    = $myInstClass->translate('NagiosQL DB Password');
$arrTemplate['STEP2_VALUE2_8']    = htmlspecialchars($_SESSION['install']['dbpass'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT2_9']    = $myInstClass->translate('Administrative Database User');
$arrTemplate['STEP2_VALUE2_9']    = htmlspecialchars($_SESSION['install']['admuser'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT2_10']    = $myInstClass->translate('Administrative Database Password');
$arrTemplate['STEP2_TEXT2_11']    = $myInstClass->translate('Drop database if already exists?');
if ($_SESSION['install']['dbdrop'] == 1) {
    $arrTemplate['STEP2_VALUE2_11'] = 'checked';
} else {
    $arrTemplate['STEP2_VALUE2_11'] = '';
}
$arrTemplate['STEP2_TEXT3_1']  = $myInstClass->translate('NagiosQL User Setup');
$arrTemplate['STEP2_TEXT3_2']  = $myInstClass->translate('Initial NagiosQL User');
$arrTemplate['STEP2_VALUE3_2'] = htmlspecialchars($_SESSION['install']['qluser'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT3_3']  = $myInstClass->translate('Initial NagiosQL Password');
$arrTemplate['STEP2_VALUE3_3'] = htmlspecialchars($_SESSION['install']['qlpass'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT3_4']  = $myInstClass->translate('Please repeat the password');
$arrTemplate['STEP2_TEXT4_1']  = $myInstClass->translate('Nagios Configuration');
$arrTemplate['STEP2_TEXT4_2']  = $myInstClass->translate('Import Nagios sample config?');
if ($_SESSION['install']['sample'] == 1) {
    $arrTemplate['STEP2_VALUE4_2'] = 'checked';
} else {
    $arrTemplate['STEP2_VALUE4_2'] = '';
}
$arrTemplate['STEP2_FORM_1']  = $myInstClass->translate('Next');
$arrTemplate['STEP2_TEXT5_1'] = $myInstClass->translate('NagiosQL path values');
$arrTemplate['STEP2_TEXT5_2'] = $myInstClass->translate('Create NagiosQL config paths?');
if ($_SESSION['install']['createpath'] == 1) {
    $arrTemplate['STEP2_VALUE5_2'] = 'checked';
} else {
    $arrTemplate['STEP2_VALUE5_2'] = '';
}
$arrTemplate['STEP2_TEXT5_3']  = $myInstClass->translate('NagiosQL config path');
$arrTemplate['STEP2_VALUE5_3'] = htmlspecialchars($_SESSION['install']['qlpath'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT5_4']  = $myInstClass->translate('Nagios config path');
$arrTemplate['STEP2_VALUE5_4'] = htmlspecialchars($_SESSION['install']['nagpath'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT5_5']  = $myInstClass->translate('Both path values were stored in your configuration target '
        . 'settings for localhost.');
$arrTemplate['STEP2_TEXT5_6']  = $myInstClass->translate('If you select the create path option, be sure that the '
        . 'NagiosQL base path exist and the webserver demon has write access to it. So the installer will create the '
        . "required subdirectories in your localhost's filesystem (hosts, services, backup etc.)");
$arrTemplate['INSTALL_FIELDS'] = '';
//
// Setting some template values to blank
// =====================================
$arrTemplate['STEP2_TEXT1_2'] = '';
//
// Conditional checks
// =======================
if ($_SESSION['install']['mode'] == 'Update') {
    $arrTemplate['STEP2_TEXT1_2']  = '<p style="color:red;"><b>' .$myInstClass->translate('Please backup your '
            . 'database before proceeding!')."</b></p>\n";
    $arrTemplate['INST_VISIBLE']   = 'hidefield';
} else {
    $arrTemplate['INSTALL_FIELDS'] = ',tfDBprivUser,tfDBprivPass,tfQLuser,tfQLpass';
    $arrTemplate['INST_VISIBLE']   = 'showfield';
}
//
// Write content
// =============
$strContent = $myInstClass->parseTemplate($arrTemplate, $preIncludeContent);
echo $strContent;
