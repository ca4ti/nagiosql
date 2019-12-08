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
// Component : Help text editor
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
$prePageId     = 39;
$preContent    = 'admin/helpedit.htm.tpl';
$preAccess     = 1;
$preFieldvars  = 1;
$setSaveLangId = 'private';
//
// Include preprocessing files
// ===========================
require $preBasePath.'functions/prepend_adm.php';
require $preBasePath.'functions/prepend_content.php';
//
// Process post parameters
// =======================
$chkHidVersion = filter_input(INPUT_POST, 'hidVersion', 513, array('options' => array('default' => 'all')));
$chkKey1       = filter_input(INPUT_POST, 'selInfoKey1', FILTER_SANITIZE_STRING);
$chkKey2       = filter_input(INPUT_POST, 'selInfoKey2', FILTER_SANITIZE_STRING);
$chkVersion    = filter_input(INPUT_POST, 'selInfoVersion', FILTER_SANITIZE_STRING);
//
// Quote special characters
// ==========================
if (get_magic_quotes_gpc() == 0) {
    $chkHidVersion = addslashes($chkHidVersion);
    $chkKey1       = addslashes($chkKey1);
    $chkKey2       = addslashes($chkKey2);
    $chkVersion    = addslashes($chkVersion);
}
//
// Security function for text fields
// =================================
$chkHidVersion = $myVisClass->tfSecure($chkHidVersion);
$chkKey1       = $myVisClass->tfSecure($chkKey1);
$chkKey2       = $myVisClass->tfSecure($chkKey2);
$chkVersion    = $myVisClass->tfSecure($chkVersion);
//
// Add or modify data
// ==================
if (($chkTaFileTextRaw != '') && ($chkTfValue3 == '1')) {
    $strSQL    = "SELECT `infotext` FROM `tbl_info` WHERE `key1`='$chkTfValue1' AND `key2`='$chkTfValue2' "
               . "AND `version`='$chkHidVersion' AND `language`='$setSaveLangId'";
    $booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
    if ($intDataCount == 0) {
        $strSQL = 'INSERT INTO `tbl_info` (`key1`,`key2`,`version`,`language`,`infotext`) '
                . "VALUES ('$chkTfValue1','$chkTfValue2','$chkHidVersion','$setSaveLangId','$chkTaFileTextRaw')";
    } else {
        $strSQL = "UPDATE `tbl_info` SET `infotext` = '$chkTaFileTextRaw' WHERE `key1` = '$chkTfValue1' "
                . "AND `key2` = '$chkTfValue2' AND `version` = '$chkHidVersion' AND `language` = '$setSaveLangId'";
    }
    $intReturn = $myDataClass->dataInsert($strSQL, $intInsertId);
    if ($intReturn != 0) {
        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
    } else {
        $myVisClass->processMessage($myDataClass->strInfoMessage, $strInfoMessage);
    }
}
//
// Singe data form
// ===============
$conttp->setVariable('TITLE', translate('Help text editor'));
$conttp->setVariable('ACTION_INSERT', filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING));
$conttp->setVariable('MAINSITE', $_SESSION['SETS']['path']['base_url']. 'admin.php');
foreach ($arrDescription as $elem) {
    $conttp->setVariable($elem['name'], $elem['string']);
}
$conttp->setVariable('INFOKEY_1', translate('Main key'));
$conttp->setVariable('INFOKEY_2', translate('Sub key'));
$conttp->setVariable('INFO_LANG', translate('Language'));
$conttp->setVariable('INFO_VERSION', translate('Nagios version'));
$conttp->setVariable('LOAD_DEFAULT', translate('Load default text'));
if ($chkChbValue1 == '1') {
    $conttp->setVariable('DEFAULT_CHECKED', 'checked');
}
//
// Get Key
// =======
$arrData   = array();
$strSQL    = 'SELECT DISTINCT `key1` FROM `tbl_info` ORDER BY `key1`';
$booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
if ($intDataCount != 0) {
    foreach ($arrData as $elem) {
        $conttp->setVariable('INFOKEY_1_VAL', $elem['key1']);
        if ($chkKey1 == $elem['key1']) {
            $conttp->setVariable('INFOKEY_1_SEL', 'selected');
            $conttp->setVariable('INFOKEY_1_SEL_VAL', $elem['key1']);
        }
        $conttp->parse('infokey1');
    }
}
if ($chkKey1 != '') {
    $strSQL    = "SELECT DISTINCT `key2` FROM `tbl_info` WHERE `key1` = '$chkKey1' ORDER BY `key1`";
    $booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
    if ($intDataCount != 0) {
        foreach ($arrData as $elem) {
            $conttp->setVariable('INFOKEY_2_VAL', $elem['key2']);
            if ($chkKey2 == $elem['key2']) {
                $conttp->setVariable('INFOKEY_2_SEL', 'selected');
                $conttp->setVariable('INFOKEY_2_SEL_VAL', $elem['key2']);
            }
            $conttp->parse('infokey2');
        }
    }
}
if (($chkKey1 != '') && ($chkKey2 != '')) {
    $strSQL    = 'SELECT DISTINCT `version` FROM `tbl_info` '
               . "WHERE `key1` = '$chkKey1' AND `key2` = '$chkKey2' ORDER BY `version`";
    $booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
    if ($intDataCount != 0) {
        if (($intDataCount == 1) && ($chkVersion == '')) {
            $chkVersion = $arrData[0]['version'];
        }
        foreach ($arrData as $elem) {
            $conttp->setVariable('INFOVERSION_2_VAL', $elem['version']);
            if ($chkVersion == $elem['version']) {
                $conttp->setVariable('INFOVERSION_2_SEL', 'selected');
                $conttp->setVariable('INFOVERSION_2_SEL_VAL', $elem['version']);
            }
            $conttp->parse('infoversion');
        }
    }
}
//
// Insert content
// ==============
if (($chkKey1 != '') && ($chkKey2 != '') && ($chkVersion != '')) {
    $strSQL       = "SELECT `infotext` FROM `tbl_info` WHERE `key1`='$chkKey1' AND `key2`='$chkKey2' "
                  . "AND `version`='$chkVersion' AND `language`='$setSaveLangId'";
    $strContentDB = $myDBClass->getFieldData($strSQL);
    if (($chkChbValue1 == 1) || ($strContentDB == '')) {
        $strSQL       = "SELECT `infotext` FROM `tbl_info` WHERE `key1`='$chkKey1' AND `key2`='$chkKey2' "
                      . "AND `version`='$chkVersion' AND `language`='default'";
        $strContentDB = $myDBClass->getFieldData($strSQL);
    }
    $conttp->setVariable('DAT_HELPTEXT', $strContentDB);
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
$conttp->parse('helpedit');
$conttp->show('helpedit');
//
// Process footer
// ==============
$maintp->setVariable('VERSION_INFO', "<a href='https://sourceforge.net/projects/nagiosql/' "
        . "target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse('footer');
$maintp->show('footer');
