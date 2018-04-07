<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2011 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Help text editor
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-03-13 14:00:26 +0100 (So, 13. Mär 2011) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.1.1
// Revision  : $LastChangedRevision: 1058 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$intMain    	= 7;
$intSub     	= 30;
$intMenu    	= 2;
$preContent   	= "admin/helpedit.tpl.htm";
$strConfig    	= "";
$strMessage   	= "";
$intRemoveTmp   = 0;
$setSaveLangId  = "private";
//
// Include preprocessing file
// ==========================
$preAccess    	= 1;
$preFieldvars   = 1;
require("../functions/prepend_adm.php");
//
// Process post parameters
// =======================
$chkKey1    	= isset($_POST['selInfoKey1'])    	? $_POST['selInfoKey1']		: "";
$chkKey2    	= isset($_POST['selInfoKey2'])    	? $_POST['selInfoKey2']		: "";
$chkVersion   	= isset($_POST['selInfoVersion']) 	? $_POST['selInfoVersion']  : "";
$chkDefault   	= isset($_POST['chbDefault'])     	? $_POST['chbDefault']		: "0";
$chkHidKey1   	= isset($_POST['hidKey1'])      	? $_POST['hidKey1']			: "";
$chkHidKey2   	= isset($_POST['hidKey2'])      	? $_POST['hidKey2']			: "";
$chkHidVersion  = isset($_POST['hidVersion'])     	? $_POST['hidVersion']		: "all";
$chkModus   	= isset($_POST['modus'])      		? $_POST['modus']			: "0";
$chkContent   	= isset($_POST['taContent'])    	? $_POST['taContent']		: "";
//
// Quote special characters
// ========================
if (get_magic_quotes_gpc() == 0) {
  	$chkContent = addslashes($chkContent);
}
// 
// Add or modify data
// ==================
if (($chkContent != "") && ($chkModus == "1")) {
  	$strSQL		= "SELECT `infotext` FROM `tbl_info`
            	   WHERE `key1` = '$chkHidKey1' AND `key2` = '$chkHidKey2' AND `version` = '$chkHidVersion'
              	   AND `language` = '$setSaveLangId'";
  	$booReturn	= $myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
  	if ($intDataCount == 0) {
    	$strSQL	= "INSERT INTO `tbl_info` (`key1`,`key2`,`version`,`language`,`infotext`)
           		   VALUES ('$chkHidKey1','$chkHidKey2','$chkHidVersion','$setSaveLangId','$chkContent')";
  	} else {
    	$strSQL	= "UPDATE `tbl_info` SET `infotext` = '$chkContent'
          		   WHERE `key1` = '$chkHidKey1' AND `key2` = '$chkHidKey2' AND `version` = '$chkHidVersion'
            	   AND `language` = '$setSaveLangId'";
  	}
  	$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
}
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
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
$conttp->setVariable("MAINSITE",$SETS['path']['root']."admin.php");
foreach($arrDescription AS $elem) {
  	$conttp->setVariable($elem['name'],$elem['string']);
}
$conttp->setVariable("INFOKEY_1",translate('Main key'));
$conttp->setVariable("INFOKEY_2",translate('Sub key'));
$conttp->setVariable("INFO_LANG",translate('Language'));
$conttp->setVariable("INFO_VERSION",translate('Nagios version'));
$conttp->setVariable("LOAD_DEFAULT",translate('Load default text'));
if ($chkDefault == "1") $conttp->setVariable("DEFAULT_CHECKED","checked");
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
  	if (($chkDefault == 1) || ($strContentDB == "")) {
    	$strSQL     	= "SELECT `infotext` FROM `tbl_info`
             			   WHERE `key1` = '$chkKey1' AND `key2` = '$chkKey2' AND `version` = '$chkVersion' AND `language` = 'default'";
    	$strContentDB 	= $myDBClass->getFieldData($strSQL);
  	}
  	$conttp->setVariable("DAT_HELPTEXT",$strContentDB);
}
$conttp->parse("helpedit");
$conttp->show("helpedit");
//
// Process footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>