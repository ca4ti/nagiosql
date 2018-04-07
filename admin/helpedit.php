<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2012 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Help text editor
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2012-02-27 13:01:17 +0100 (Mon, 27 Feb 2012) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1257 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$prePageId			= 39;
$preContent   		= "admin/helpedit.tpl.htm";
$preAccess    		= 1;
$preFieldvars 		= 1;
$setSaveLangId  	= "private";
//
// Include preprocessing files
// ===========================
require("../functions/prepend_adm.php");
require("../functions/prepend_content.php");
//
// Process post parameters
// =======================
$chkHidVersion  = isset($_POST['hidVersion'])     	? $_POST['hidVersion']		: "all";
$chkKey1    	= isset($_POST['selInfoKey1'])    	? $_POST['selInfoKey1']		: "";
$chkKey2    	= isset($_POST['selInfoKey2'])    	? $_POST['selInfoKey2']		: "";
$chkVersion   	= isset($_POST['selInfoVersion']) 	? $_POST['selInfoVersion']  : "";
// 
// Add or modify data
// ==================
if (($chkTaValue1 != "") && ($chkTfValue3 == "1")) {
  	$strSQL		= "SELECT `infotext` FROM `tbl_info`
            	   WHERE `key1` = '$chkTfValue1' AND `key2` = '$chkTfValue2' AND `version` = '$chkHidVersion'
              	   AND `language` = '$setSaveLangId'";
  	$booReturn	= $myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
  	if ($intDataCount == 0) {
    	$strSQL	= "INSERT INTO `tbl_info` (`key1`,`key2`,`version`,`language`,`infotext`)
           		   VALUES ('$chkTfValue1','$chkTfValue2','$chkHidVersion','$setSaveLangId','$chkTaValue1')";
  	} else {
    	$strSQL	= "UPDATE `tbl_info` SET `infotext` = '$chkTaValue1'
          		   WHERE `key1` = '$chkTfValue1' AND `key2` = '$chkTfValue2' AND `version` = '$chkHidVersion'
            	   AND `language` = '$setSaveLangId'";
  	}
  	$intReturn = $myDataClass->dataInsert($strSQL,$intInsertId);
  	if ($intReturn != 0) {
		$myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
	} else {
		$myVisClass->processMessage($myDataClass->strInfoMessage,$strInfoMessage);
	}
}
//
// Start content
// =============
$conttp->setVariable("TITLE",translate('Help text editor'));
$conttp->parse("header");
$conttp->show("header");
//
// Singe data form
// ===============
$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
$conttp->setVariable("MAINSITE",$_SESSION['SETS']['path']['base_url']."admin.php");
foreach($arrDescription AS $elem) {
  	$conttp->setVariable($elem['name'],$elem['string']);
}
$conttp->setVariable("INFOKEY_1",translate('Main key'));
$conttp->setVariable("INFOKEY_2",translate('Sub key'));
$conttp->setVariable("INFO_LANG",translate('Language'));
$conttp->setVariable("INFO_VERSION",translate('Nagios version'));
$conttp->setVariable("LOAD_DEFAULT",translate('Load default text'));
if ($chkChbValue1 == "1") $conttp->setVariable("DEFAULT_CHECKED","checked");
//
// Get Key
// =======
$strSQL   	= "SELECT DISTINCT `key1` FROM `tbl_info` ORDER BY `key1`";
$booReturn  = $myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
if ($intDataCount != 0) {
  	foreach ($arrData AS $elem) {
    	$conttp->setVariable("INFOKEY_1_VAL",$elem['key1']);
    	if ($chkKey1 == $elem['key1']) {
      		$conttp->setVariable("INFOKEY_1_SEL","selected");
      		$conttp->setVariable("INFOKEY_1_SEL_VAL",$elem['key1']);
    	}
   	 	$conttp->parse("infokey1");
  	}
}
if ($chkKey1 != "") {
  	$strSQL   	= "SELECT DISTINCT `key2` FROM `tbl_info` WHERE `key1` = '$chkKey1' ORDER BY `key1`";
  	$booReturn  = $myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
  	if ($intDataCount != 0) {
    	foreach ($arrData AS $elem) {
      		$conttp->setVariable("INFOKEY_2_VAL",$elem['key2']);
			if ($chkKey2 == $elem['key2']) {
				$conttp->setVariable("INFOKEY_2_SEL","selected");
				$conttp->setVariable("INFOKEY_2_SEL_VAL",$elem['key2']);
			}
			$conttp->parse("infokey2");
		}
	}
}
if (($chkKey1 != "") && ($chkKey2 != "")) {
  	$strSQL   	= "SELECT DISTINCT `version` FROM `tbl_info` WHERE `key1` = '$chkKey1' AND `key2` = '$chkKey2' ORDER BY `version`";
  	$booReturn  = $myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
  	if ($intDataCount != 0) {
	if (($intDataCount == 1) && ($chkVersion == "")) $chkVersion = $arrData[0]['version'];
		foreach ($arrData AS $elem) {
			$conttp->setVariable("INFOVERSION_2_VAL",$elem['version']);
			if ($chkVersion == $elem['version']) {
				$conttp->setVariable("INFOVERSION_2_SEL","selected");
				$conttp->setVariable("INFOVERSION_2_SEL_VAL",$elem['version']);
			}
			$conttp->parse("infoversion");
		}
	}
}
//
// Insert content
// ==============
if (($chkKey1 != "") && ($chkKey2 != "") && ($chkVersion != "")) {
  	$strSQL     	= "SELECT `infotext` FROM `tbl_info`
           			   WHERE `key1` = '$chkKey1' AND `key2` = '$chkKey2' AND `version` = '$chkVersion' AND `language` = '$setSaveLangId'";
  	$strContentDB 	= $myDBClass->getFieldData($strSQL);
  	if (($chkChbValue1 == 1) || ($strContentDB == "")) {
    	$strSQL     	= "SELECT `infotext` FROM `tbl_info`
             			   WHERE `key1` = '$chkKey1' AND `key2` = '$chkKey2' AND `version` = '$chkVersion' AND `language` = 'default'";
    	$strContentDB 	= $myDBClass->getFieldData($strSQL);
  	}
  	$conttp->setVariable("DAT_HELPTEXT",$strContentDB);
}
// Messages
if ($strErrorMessage != "") $conttp->setVariable("ERRORMESSAGE",$strErrorMessage);
if ($strInfoMessage != "")  $conttp->setVariable("INFOMESSAGE",$strInfoMessage);
// Check access rights for adding new objects
if ($myVisClass->checkAccGroup($prePageKey,'write') != 0) $conttp->setVariable("ADD_CONTROL","disabled=\"disabled\"");
$conttp->parse("helpedit");
$conttp->show("helpedit");
//
// Process footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>