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
$preIncludeContent = $preBasePath.'install/templates/step3.htm.tpl';
$intError          = 0;
$setQLVersion      = '';
$arrUpdate         = array();

if (function_exists('date_default_timezone_set') and function_exists('date_default_timezone_get')) {
    date_default_timezone_set(date_default_timezone_get());
}
//
// Build content
// =============
$arrTemplate['STEP1_BOX']       = $myInstClass->translate('Requirements');
$arrTemplate['STEP2_BOX']       = $myInstClass->translate('Installation');
$arrTemplate['STEP3_BOX']       = $myInstClass->translate('Finish');
$arrTemplate['STEP3_TITLE']     = 'NagiosQL ' .$myInstClass->translate('Installation'). ': ' .
    $myInstClass->translate('Finishing Setup');
$arrTemplate['INST_VISIBLE']    = 'showfield';
$arrTemplate['STEP4_SUB_TITLE'] = $myInstClass->translate('Deploy NagiosQL settings');
$arrTemplate['STEP3_TEXT_01']   = $myInstClass->translate('Database server connection (privileged user)');
$arrTemplate['STEP3_TEXT_03']   = $myInstClass->translate('Database server version');
$arrTemplate['STEP3_TEXT_05']   = $myInstClass->translate('Database server support');
$arrTemplate['STEP3_TEXT_07']   = $myInstClass->translate('Delete existing NagiosQL database');
$arrTemplate['STEP3_TEXT_09']   = $myInstClass->translate('Creating new database');
$arrTemplate['STEP3_TEXT_11']   = $myInstClass->translate('Installing NagiosQL database tables');
$arrTemplate['STEP3_TEXT_13']   = $myInstClass->translate('Create NagiosQL database user');
$arrTemplate['STEP3_TEXT_15']   = $myInstClass->translate('Set initial NagiosQL Administrator');
$arrTemplate['STEP3_TEXT_17']   = $myInstClass->translate('Database server connection (NagiosQL user)');
$arrTemplate['STEP4_TEXT_01']   = $myInstClass->translate('Writing global settings to database');
$arrTemplate['STEP4_TEXT_03']   = $myInstClass->translate('Writing database configuration to settings.php');
$arrTemplate['STEP4_TEXT_05']   = $myInstClass->translate('Import Nagios sample data');
$arrTemplate['STEP4_TEXT_07']   = $myInstClass->translate('Create and/or store NagiosQL path settings');

$arrTemplate['STEP4_VISIBLE']      = 'hidefield';
$arrTemplate['STEP3_TEXT_02_SHOW'] = 'hidefield';
$arrTemplate['STEP3_TEXT_03_SHOW'] = 'hidefield';
$arrTemplate['STEP3_TEXT_05_SHOW'] = 'hidefield';
$arrTemplate['STEP3_TEXT_07_SHOW'] = 'hidefield';
$arrTemplate['STEP3_TEXT_09_SHOW'] = 'hidefield';
$arrTemplate['STEP3_TEXT_11_SHOW'] = 'hidefield';
$arrTemplate['STEP3_TEXT_13_SHOW'] = 'hidefield';
$arrTemplate['STEP3_TEXT_15_SHOW'] = 'hidefield';
$arrTemplate['STEP3_TEXT_17_SHOW'] = 'hidefield';
$arrTemplate['STEP4_TEXT_03_SHOW'] = 'hidefield';
$arrTemplate['STEP4_TEXT_05_SHOW'] = 'hidefield';
$arrTemplate['STEP4_TEXT_07_SHOW'] = 'hidefield';
//
// Check any data before installation
// ==================================
$intInstError = 0;
//
// Doing installation/upgrade
// ==========================
if ($_SESSION['install']['mode'] == 'Update') {
    $arrTemplate['STEP3_SUB_TITLE'] = $myInstClass->translate('Updating existing NagiosQL database');
    //
    // Include database class
    // ======================
    if ($_SESSION['install']['dbtype'] == 'mysqli') {
        // Initialize mysqli class
        $myDBClass = new functions\MysqliDbClass;
    } else {
        $strErrorMessage .= $myInstClass->translate('Database type not defined!'). ' (' .
            $_SESSION['install']['dbtype'].")<br>\n";
        $strStatusMessage = '<span class="red">' .$myInstClass->translate('failed'). '</span>';
        $intError         = 1;
    }
    // Set DB parameters
    $myDBClass->arrParams['server']   = $_SESSION['install']['dbserver'];
    $myDBClass->arrParams['port']     = $_SESSION['install']['dbport'];
    $myDBClass->arrParams['username'] = $_SESSION['install']['admuser'];
    $myDBClass->arrParams['password'] = $_SESSION['install']['admpass'];
    $myDBClass->arrParams['database'] = $_SESSION['install']['dbname'];
    // Include classes
    if ($intError == 0) {
        $myInstClass->myDBClass =& $myDBClass;
    }
    // Check database connection
    if ($intError == 0) {
        $intError = $myInstClass->openAdmDBSrv($arrTemplate['STEP3_TEXT_02'], $strErrorMessage);
    }
    if ($intError == 0) {
        $intError = $myInstClass->openDatabase($arrTemplate['STEP3_TEXT_02'], $strErrorMessage);
    }
    $arrTemplate['STEP3_TEXT_02_SHOW']    = 'showfield';
    // Check NagiosQL version
    if ($intError == 0) {
        $arrTemplate['STEP3_TEXT_03']      = $myInstClass->translate('Installed NagiosQL version');
        $arrTemplate['STEP3_TEXT_03_SHOW'] = 'showfield';
        $intError = $myInstClass->checkQLVersion(
            $arrTemplate['STEP3_TEXT_04'],
            $strErrorMessage,
            $arrUpdate,
            $setQLVersion
        );
    }
    // Upgrade NagiosQL DB
    if ($intError == 0) {
        $arrTemplate['STEP3_TEXT_05'] = $myInstClass->translate('Upgrading from version'). ' ' .$setQLVersion
            . ' ' . $myInstClass->translate('to'). ' ' .$preNagiosQL_ver;
        $arrTemplate['STEP3_TEXT_05_SHOW'] = 'showfield';
        $intError = $myInstClass->updateQLDB($arrTemplate['STEP3_TEXT_06'], $strErrorMessage, $arrUpdate);
    }
    if (($_SESSION['install']['dbtype'] == 'mysql') && (version_compare($setQLVersion, '3.4.1') == -1)) {
        // Converting database to UTF8
        if ($intError == 0) {
            $arrTemplate['STEP3_TEXT_07']      = $myInstClass->translate('Converting database to utf8 character set');
            $arrTemplate['STEP3_TEXT_07_SHOW'] = 'showfield';
            $intError = $myInstClass->convQLDB($arrTemplate['STEP3_TEXT_08'], $strErrorMessage);
        }
        // Converting database tables to UTF8
        if ($intError == 0) {
            $arrTemplate['STEP3_TEXT_09']      = $myInstClass->translate('Converting database tables to utf8 character '
                . 'set');
            $arrTemplate['STEP3_TEXT_09_SHOW'] = 'showfield';
            $intError = $myInstClass->convQLDBTables($arrTemplate['STEP3_TEXT_10'], $strErrorMessage);
        }
        // Converting database fields to UTF8
        if ($intError == 0) {
            $arrTemplate['STEP3_TEXT_11']      = $myInstClass->translate('Converting database fields to utf8 '
                . 'character set');
            $arrTemplate['STEP3_TEXT_11_SHOW'] = 'showfield';
            $intError = $myInstClass->convQLDBFields($arrTemplate['STEP3_TEXT_12'], $strErrorMessage);
        }
    }
    // Reconnect Database with new user
    if ($intError == 0) {
        $arrTemplate['STEP3_TEXT_17_SHOW'] = 'showfield';
        $intError1 = $myInstClass->openAdmDBSrv($arrTemplate['STEP3_TEXT_18'], $strErrorMessage, 1);
        $intError2 = $myInstClass->openDatabase($arrTemplate['STEP3_TEXT_18'], $strErrorMessage, 1);
        $intError  = $intError1 + $intError2;
    }
    // Deploy NagiosQL database settings
    if ($intError == 0) {
        $arrTemplate['STEP4_VISIBLE'] = 'showfield';
        $intError = $myInstClass->updateSettingsDB($arrTemplate['STEP4_TEXT_02'], $strErrorMessage);
    }
    // Write database settings to file
    if ($intError == 0) {
        $arrTemplate['STEP4_TEXT_03_SHOW'] = 'showfield';
        $intError = $myInstClass->updateSettingsFile($arrTemplate['STEP4_TEXT_04'], $strErrorMessage);
    }
} else {
    $arrTemplate['STEP3_SUB_TITLE']    = $myInstClass->translate('Create new NagiosQL database');
    //
    // Include database class
    // ======================
    if ($_SESSION['install']['dbtype'] == 'mysqli') {
        // Initialize mysqli class
        $myDBClass = new functions\MysqliDbClass;
    } else {
        $strErrorMessage .= $myInstClass->translate('Database type not defined!'). ' (' .
            $_SESSION['install']['dbtype'].")<br>\n";
        $strStatusMessage = '<span class="red">' .$myInstClass->translate('failed'). '</span>';
        $intError         = 1;
    }
    // Set DB parameters
    $myDBClass->arrParams['server']   = $_SESSION['install']['dbserver'];
    $myDBClass->arrParams['port']     = $_SESSION['install']['dbport'];
    $myDBClass->arrParams['username'] = $_SESSION['install']['admuser'];
    $myDBClass->arrParams['password'] = $_SESSION['install']['admpass'];
    $myDBClass->arrParams['database'] = $_SESSION['install']['dbname'];
    // Include classes
    if ($intError == 0) {
        $myInstClass->myDBClass =& $myDBClass;
    }
    // Check database connection
    $intOldDBStatus = 0;
    if ($intError == 0) {
        $intError = $myInstClass->openAdmDBSrv($arrTemplate['STEP3_TEXT_02'], $strErrorMessage);
    }
    // Does the database already exist?
    if ($intError == 0) {
        $intOldDBStatus = $myInstClass->openDatabase($strTmpMessage, $strTmpError);
        $myDBClass->strErrorMessage = '';
        if (($intOldDBStatus == 0) && ($_SESSION['install']['dbdrop'] == 0)) {
            $strErrorMessage .= $myInstClass->translate('Database already exists and drop database was not '
                    . 'selected, please correct or manage manually'). '<br>';
            $arrTemplate['STEP3_TEXT_02'] = '<span class="red">' .$myInstClass->translate('failed'). '</span>';
            $intError = 1;
        } else {
            $myInstClass->openAdmDBSrv($arrTemplate['STEP3_TEXT_02'], $strErrorMessage);
        }
    }
    $arrTemplate['STEP3_TEXT_02_SHOW'] = 'showfield';
    $arrTemplate['STEP3_TEXT_02']     .= ' (' .$_SESSION['install']['dbtype']. ')';
    // Check database version
    if ($intError == 0) {
        $arrTemplate['STEP3_TEXT_03_SHOW'] = 'showfield';
        $arrTemplate['STEP3_TEXT_05_SHOW'] = 'showfield';
        $intError = $myInstClass->checkDBVersion($arrTemplate['STEP3_TEXT_06'], $strErrorMessage, $strVersion);
        if ($strVersion == 'unknown') {
            $arrTemplate['STEP3_TEXT_04'] = '<span class="red">' .$myInstClass->translate('unknown'). '</span>';
        } else {
            $arrTemplate['STEP3_TEXT_04'] = '<span class="green">' .$strVersion. '</span>';
        }
    }
    // Drop existing database
    if (($intError == 0) && ($_SESSION['install']['dbdrop'] == 1) && ($intOldDBStatus == 0)) {
        $arrTemplate['STEP3_TEXT_07_SHOW'] = 'showfield';
        $intError = $myInstClass->dropDB($arrTemplate['STEP3_TEXT_08'], $strErrorMessage);
    }
    // Create new database
    if ($intError == 0) {
        $arrTemplate['STEP3_TEXT_09_SHOW'] = 'showfield';
        $intError = $myInstClass->createDB($arrTemplate['STEP3_TEXT_10'], $strErrorMessage);
    }
    // Write initial SQL data to database
    if ($intError == 0) {
        $intError = $myInstClass->openDatabase($strTmp, $strErrorMessage);
    }
    if ($intError == 0) {
        $arrTemplate['STEP3_TEXT_11_SHOW'] = 'showfield';
        $arrInsert[] = $preSqlNewInstall;
        $intError = $myInstClass->updateQLDB($arrTemplate['STEP3_TEXT_12'], $strErrorMessage, $arrInsert);
    }
    // Grant NagiosQL database user
    if ($intError == 0) {
        $arrTemplate['STEP3_TEXT_13_SHOW'] = 'showfield';
        $intError = $myInstClass->grantDBUser($arrTemplate['STEP3_TEXT_14'], $strErrorMessage);
    }
    // Create NagiosQL admin user
    if ($intError == 0) {
        $arrTemplate['STEP3_TEXT_15_SHOW'] = 'showfield';
        $intError = $myInstClass->createNQLAdmin($arrTemplate['STEP3_TEXT_16'], $strErrorMessage);
    }
    // Reconnect Database with new user
    if ($intError == 0) {
        $myDBClass->arrParams['username']  = $_SESSION['install']['dbuser'];
        $myDBClass->arrParams['password']  = $_SESSION['install']['dbpass'];
        $arrTemplate['STEP3_TEXT_17_SHOW'] = 'showfield';
        if ($intError == 0) {
            $intError = $myInstClass->openAdmDBSrv($arrTemplate['STEP3_TEXT_18'], $strErrorMessage, 1);
        }
        if ($intError == 0) {
            $intError = $myInstClass->openDatabase($arrTemplate['STEP3_TEXT_18'], $strErrorMessage, 1);
        }
    }
    // Deploy NagiosQL settings
    if ($intError == 0) {
        $arrTemplate['STEP4_VISIBLE'] = 'showfield';
        $intError = $myInstClass->updateSettingsDB($arrTemplate['STEP4_TEXT_02'], $strErrorMessage);
    }
    // Write database settings to file
    if ($intError == 0) {
        $arrTemplate['STEP4_TEXT_03_SHOW'] = 'showfield';
        $intError = $myInstClass->updateSettingsFile($arrTemplate['STEP4_TEXT_04'], $strErrorMessage);
    }
    // Write sample data to database
    if (($intError == 0) && ($_SESSION['install']['sample'] == 1)) {
        $arrTemplate['STEP4_TEXT_05_SHOW'] = 'showfield';
        $arrSample[] = 'sql/import_nagios_sample.sql';
        $intError = $myInstClass->updateQLDB($arrTemplate['STEP4_TEXT_06'], $strErrorMessage, $arrSample);
    }
    // Create NagiosQL path and write path settings to the database
    if ($intError == 0) {
        $arrTemplate['STEP4_TEXT_07_SHOW'] = 'showfield';
        $intError = $myInstClass->updateQLpath($arrTemplate['STEP4_TEXT_08'], $strErrorMessage);
    }
}
if ($intError != 0) {
    $arrTemplate['ERRORMESSAGE'] = '<p style="color:#F00;margin-top:0;font-weight:bold;">' .
        $strErrorMessage."</p>\n";
    $arrTemplate['INFO_TEXT']    = '';
    $arrTemplate['BUTTON']       = "<div id=\"install-back\">\n";
    $arrTemplate['BUTTON']      .= "<input type='hidden' name='hidStep' id='hidStep' value='2' />\n";
    $arrTemplate['BUTTON']      .= "<input type='image' src='images/previous.png' value='Submit' alt='Submit' />"
        . '<br />' .$myInstClass->translate('Back')."\n";
    $arrTemplate['BUTTON']      .= "</div>\n";
} else {
    $arrTemplate['ERRORMESSAGE'] = '';
    $arrTemplate['INST_VISIBLE'] = 'showfield';
    $arrTemplate['INFO_TEXT']    = $myInstClass->translate('Please delete the install directory to continue!');
    $arrTemplate['BUTTON']       = "<div id=\"install-next\">\n";
    $arrTemplate['BUTTON']      .= "<a href='../index.php'><img src='images/next.png' alt='finish' title='finish' "
        . "border='0' /></a><br />".$myInstClass->translate('Finish')."\n";
    $arrTemplate['BUTTON']      .= "</div>\n";
}
//
// Write content
// =============
$strContent = $myInstClass->parseTemplate($arrTemplate, $preIncludeContent);
echo $strContent;
