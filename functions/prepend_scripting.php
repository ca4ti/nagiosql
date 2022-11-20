<?php
/* ----------------------------------------------------------------------------
 NagiosQL
-------------------------------------------------------------------------------
 (c) 2005-2022 by Martin Willisegger

 Project   : NagiosQL
 Component : Preprocessing script for scripting files
 Website   : https://sourceforge.net/projects/nagiosql/
 Version   : 3.5.0
 GIT Repo  : https://gitlab.com/wizonet/NagiosQL
-----------------------------------------------------------------------------*/

/*
Security Protection
*/
if (isset($_GET['SETS']) || isset($_POST['SETS'])) {
    $SETS = '';
}

/*
Timezone settings
*/
if (function_exists('date_default_timezone_set') and function_exists('date_default_timezone_get')) {
    @date_default_timezone_set(@date_default_timezone_get());
}

/*
Define common variables
*/
$chkDomainId = 0;
$intError = 0;

/*
Read settings file
*/
$preBasePath = str_replace('functions', '', __DIR__);
$preIniFile = $preBasePath . 'config/settings.php';

/*
Read file settings
*/
$SETS = parse_ini_file($preIniFile, true);

/*
Include external function/class files - part 1
*/
require_once $preBasePath . 'libraries/pear/HTML/Template/IT.php';
require $preBasePath . 'functions/Autoloader.php';
functions\Autoloader::register($preBasePath);

/*
Initialize classes - part 1
*/
$myDBClass = new functions\MysqliDbClass();
$myDBClass->arrParams = $SETS['db'];
$myDBClass->hasDBConnection();
if ($myDBClass->error === true) {
    $strDBMessage = $myDBClass->strErrorMessage;
    $booError = $myDBClass->error;
}

/*
Get additional configuration from the table tbl_settings
*/
if ($intError === 0) {
    $strSQL = 'SELECT `category`,`name`,`value` FROM `tbl_settings`';
    $booReturn = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
    if ($booReturn === false) {
        echo str_replace('::', "\n", 'Error while selecting data from database: ' . $myDBClass->strErrorMessage);
        $intError = 1;
    } elseif ($intDataCount !== 0) {
        for ($i = 0; $i < $intDataCount; $i++) {
            $SETS[$arrDataLines[$i]['category']][$arrDataLines[$i]['name']] = $arrDataLines[$i]['value'];
        }
    }
} else {
    echo "Could not load configuration settings from database - abort\n";
    exit;
}

/*
Include translator strings
*/
include 'translator.php';

/*
Initialize classes
*/
$arrSession = array();
$arrSession['SETS'] = $SETS;
$myDataClass = new functions\NagDataClass($arrSession);
$myConfigClass = new functions\NagConfigClass($arrSession);
/** @noinspection PhpObjectFieldsAreOnlyWrittenInspection */
$myImportClass = new functions\NagImportClass($arrSession);

/*
Propagating the classes themselves
*/
$myDataClass->myDBClass =& $myDBClass;
$myDataClass->myConfigClass =& $myConfigClass;
$myConfigClass->myDBClass =& $myDBClass;
$myConfigClass->myDataClass =& $myDataClass;
$myImportClass->myDataClass =& $myDataClass;
$myImportClass->myDBClass =& $myDBClass;
$myImportClass->myConfigClass =& $myConfigClass;