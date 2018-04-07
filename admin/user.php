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
// Component : User administration
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
$intSub     	= 18;
$intMenu    	= 2;
$preContent 	= "admin/user.tpl.htm";
$intCount   	= 0;
$strMessage 	= "";
//
// Include preprocessing file
// ==========================
$preAccess    	= 1;
$preFieldvars 	= 1;
require("../functions/prepend_adm.php");
//
// Process post parameters
// =======================
$chkInsName   	= isset($_POST['tfName'])       	? htmlspecialchars($_POST['tfName'], ENT_QUOTES, 'utf-8')      	: "";
$chkInsAlias  	= isset($_POST['tfAlias'])      	? htmlspecialchars($_POST['tfAlias'], ENT_QUOTES, 'utf-8')     	: "";
$chkHidName   	= isset($_POST['hidName'])      	? $_POST['hidName']     	: "";
$chkInsPwd1   	= isset($_POST['tfPassword1'])  	? $_POST['tfPassword1'] 	: "";
$chkInsPwd2   	= isset($_POST['tfPassword2'])  	? $_POST['tfPassword2'] 	: "";
$chkAdminEnable = isset($_POST['chbAdminEnable'])   ? $_POST['chbAdminEnable']  : 0;
$chkWsAuth    	= isset($_POST['chbWsAuth'])    	? $_POST['chbWsAuth']   	: 0;
//
// Quote special characters
// ==========================
if (get_magic_quotes_gpc() == 0) {
	$chkInsName   = addslashes($chkInsName);
	$chkInsAlias  = addslashes($chkInsAlias);
	$chkHidName   = addslashes($chkHidName);
	$chkInsPwd1   = addslashes($chkInsPwd1);
	$chkInsPwd2   = addslashes($chkInsPwd2);
}
// 
// Add or modify data
// ==================
if (($chkModus == "insert") || ($chkModus == "modify")) {
  	// Check password
  	if ((($chkInsPwd1 === $chkInsPwd2) && (strlen($chkInsPwd1) > 5)) || (($chkModus == "modify") && ($chkInsPwd1 == ""))) {
    	if ($chkInsPwd1 == "") {$strPasswd = "";} else {$strPasswd = "`password`=MD5('$chkInsPwd1'),";}
    	// Grant admin rights
		if ($chkHidName == "Admin") { 
			$chkInsName	 	 = "Admin";
			$chkActive		 = "1";
			$chkAdminEnable  = "1";
		}
    	$strSQLx = "`tbl_user` SET `username`='$chkInsName', `alias`='$chkInsAlias', $strPasswd
          			`admin_enable`='$chkAdminEnable', `wsauth`='$chkWsAuth', `active`='$chkActive', `last_modified`=NOW()";
    	if ($chkModus == "insert") {
      		$strSQL = "INSERT INTO ".$strSQLx;
    	} else {
      		$strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
    	}
    	if (($chkInsName != "") && ($chkInsAlias != "")) {
      		$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
			$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
			if ($chkModus == "insert") $chkDataId = $intInsertId;
			if ($intInsert == 1) {
				$intReturn = 1;
			} else {
      			if ($chkModus  == "insert")   $myDataClass->writeLog(translate('A new user added:')." ".$chkInsName);
      			if ($chkModus  == "modify")   $myDataClass->writeLog(translate('User modified:')." ".$chkInsName);
				$intReturn = 0;
			}
    	} else {
      		$strMessage .= translate('Database entry failed! Not all necessary data filled in!');
    	}
  	} else {
    	$strMessage .= translate('Password too short or password fields unequally!');
  	}
  	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Delete selected datasets
  	if ($chkHidName != "Admin") {
    	$intReturn = $myDataClass->dataDeleteEasy("tbl_user","id",$chkListId);
		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	} else {
    	$myDataClass->strDBMessage = translate("Admin can't be deleted");
  	}
  	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Copy selected datasets
  	$intReturn = $myDataClass->dataCopyEasy("tbl_user","username",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
  	// Open a dataset to modify
  	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM `tbl_user` WHERE `id`=".$chkListId,$arrModifyData);
	$myVisClass->processMessage($myDBClass->strDBError,$strMessage);
  	if ($booReturn == false) $strMessage .= translate('Error while selecting data from database:')."<br>".$myDataClass->strDBError."<br>";
  	$chkModus      = "add";
}
// Get status messages from database
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $strMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$strMessage."</span>";
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Insert content
// ==============
$conttp->setVariable("TITLE",translate('User administration'));
$conttp->parse("header");
$conttp->show("header");
//
// Singe data form
// ===============
if ($chkModus == "add") {
  	// Process template text raplacements
  	foreach($arrDescription AS $elem) {
    	$conttp->setVariable($elem['name'],$elem['string']);
  	}
	$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
	$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	$conttp->setVariable("LIMIT",$chkLimit);
	$conttp->setVariable("ACT_CHECKED","checked");
	$conttp->setVariable("WSAUTH_DISABLE","disabled");
	$conttp->setVariable("MODUS","insert");
	$conttp->setVariable("FILL_ALLFIELDS",translate('Please fill in all fields marked with an *'));
	$conttp->setVariable("FILL_ILLEGALCHARS",translate('The following field contains not permitted characters:'));
	$conttp->setVariable("FILL_PASSWD_NOT_EQUAL",translate('The passwords are not equal!'));
	$conttp->setVariable("FILL_PASSWORD",translate('Please fill in the password'));
	$conttp->setVariable("FILL_PWDSHORT",translate('The password is too short - use at least 6 characters!'));
	$conttp->setVariable("LANG_WEBSERVER_AUTH",translate('Webserver authentification'));
	$conttp->setVariable("PASSWORD_MUST","class=\"inpmust\"");
	$conttp->setVariable("PASSWORD_MUST_STAR","*");
  	// If webserver authetification is enabled - show option field
  	if (isset($SETS['security']['wsauth']) && ($SETS['security']['wsauth'] == 1)) {
    	$conttp->setVariable("WSAUTH_DISABLE","");
  	}
  	// Insert data from database in "modify" mode
  	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
    	foreach($arrModifyData AS $key => $value) {
      		if (($key == "active") || ($key == "last_modified")) continue;
      		$conttp->setVariable("DAT_".strtoupper($key),$value);
    	}
		if ($arrModifyData['wsauth'] != 1) 		 	$conttp->setVariable("WSAUTH_CHECKED","");
		if ($arrModifyData['active'] != 1) 		 	$conttp->setVariable("ACT_CHECKED","");
		if ($arrModifyData['admin_enable'] != 1) 	$conttp->setVariable("ADMINENABLE_CHECKED","");
		// Object based group administration
		if ($arrModifyData['admin_enable'] == 1) 	$conttp->setVariable("ADMINENABLE_CHECKED","checked");
		// Webserver authentification
		if ($arrModifyData['wsauth'] == 1) 			$conttp->setVariable("WSAUTH_CHECKED","checked");
		// Admin rules
   		if ($arrModifyData['username'] == "Admin") {
			$conttp->setVariable("NAME_DISABLE","disabled");
			$conttp->setVariable("ACT_DISABLE","disabled");
			$conttp->setVariable("WSAUTH_DISABLE","disabled");
			$conttp->setVariable("ADMINENABLE_DISABLE","disabled");
			$conttp->setVariable("ADMINENABLE_CHECKED","checked");
    	}
		$conttp->setVariable("PASSWORD_MUST","");
		$conttp->setVariable("PASSWORD_MUST_STAR","");
    	$conttp->setVariable("MODUS","modify");
  	}
  	$conttp->parse("datainsert");
  	$conttp->show("datainsert");
}
//
// Data table
// ==========
if ($chkModus == "display") {
  	// Process template text raplacements
  	foreach($arrDescription AS $elem) {
    	$mastertp->setVariable($elem['name'],$elem['string']);
  	}
	$mastertp->setVariable("FIELD_1",translate('Username'));
	$mastertp->setVariable("FIELD_2",translate('Description'));
	$mastertp->setVariable("DELETE",translate('Delete'));
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",translate('Copy'));
	$mastertp->setVariable("ACTION_MODIFY",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
	$mastertp->setVariable("LANG_DELETESINGLE",translate('Do you really want to delete this database entry:'));
	$mastertp->setVariable("LANG_DELETEOK",translate('Do you really want to delete all marked entries?'));
  	// Count datasets
  	$strSQL    = "SELECT count(*) AS `number` FROM `tbl_user`";
  	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
  	if ($booReturn == false) {
    	$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
  	} else {
    	$intCount = (int)$arrDataLinesCount['number'];
  	}
  	// Get datasets
  	$strSQL    = "SELECT `id`, `username`, `alias`, `active`, `nodelete`
          		  FROM `tbl_user` ORDER BY `username` LIMIT $chkLimit,".$SETS['common']['pagelines'];
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	$mastertp->setVariable("CELLCLASS_L","tdlb");
	$mastertp->setVariable("CELLCLASS_M","tdmb");	;
	$mastertp->setVariable("DATA_FIELD_1",translate('No data'));
	$mastertp->setVariable("DATA_FIELD_2","&nbsp;");
	$mastertp->setVariable("DATA_ACTIVE","&nbsp;");
	$mastertp->setVariable("CHB_CLASS","checkbox");
  	if ($booReturn == false) {
    	$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
  	} else if ($intDataCount != 0) {
    	for ($i=0;$i<$intDataCount;$i++) {
      		// Line colours
      		$strClassL = "tdld"; $strClassM = "tdmd"; $strChbClass = "checkboxline";
      		if ($i%2 == 1) {$strClassL = "tdlb"; $strClassM = "tdmb"; $strChbClass = "checkbox";}
      		if ($arrDataLines[$i]['active'] == 0) {$strActive = translate('No');} else {$strActive = translate('Yes');}
      		// Set datafields
      		foreach($arrDescription AS $elem) {
        		$mastertp->setVariable($elem['name'],$elem['string']);
      		}
			$mastertp->setVariable("DATA_FIELD_1",htmlspecialchars($arrDataLines[$i]['username'],ENT_COMPAT,'UTF-8'));
			$mastertp->setVariable("DATA_FIELD_2",htmlspecialchars($arrDataLines[$i]['alias'],ENT_COMPAT,'UTF-8'));
			$mastertp->setVariable("DATA_ACTIVE",$strActive);
			$mastertp->setVariable("LINE_ID",$arrDataLines[$i]['id']);
			$mastertp->setVariable("CELLCLASS_L",$strClassL);
			$mastertp->setVariable("CELLCLASS_M",$strClassM);
			$mastertp->setVariable("CHB_CLASS",$strChbClass);
			$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
			$mastertp->setVariable("PICTURE_CLASS","elementShow");
			if ($chkModus != "display") $mastertp->setVariable("DISABLED","disabled");
			if ($arrDataLines[$i]['nodelete'] == "1") {
				$mastertp->setVariable("DEL_HIDE_START","<!--");
				$mastertp->setVariable("DEL_HIDE_STOP","-->");
				$mastertp->setVariable("DISABLED","disabled");
			}
      	$mastertp->parse("datarowcommon");
    	}
  	} else {
      	$mastertp->parse("datarowcommon");
  	}
	// Show page numbers
  	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
  	if (isset($intCount)) $mastertp->setVariable("PAGES",$myVisClass->buildPageLinks(filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING),$intCount,$chkLimit));
  	$mastertp->parse("datatablecommon");
  	$mastertp->show("datatablecommon");
}
// Show messages
if (isset($strMessage)) {$mastertp->setVariable("DBMESSAGE",$strMessage);} else {$mastertp->setVariable("DBMESSAGE","&nbsp;");}
$mastertp->parse("msgfooter");
$mastertp->show("msgfooter");
//
// Process footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>