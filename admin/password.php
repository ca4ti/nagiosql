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
// Component : Password administration
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
$intMain      	= 7;
$intSub       	= 20;
$intMenu      	= 2;
$preContent   	= "admin/admin_master.tpl.htm";
$strMessage   	= "";
//
// Include preprocessing file
// ==========================
$preAccess      = 1;
$preFieldvars   = 1;
$preShowHeader  = 0;
require("../functions/prepend_adm.php");
//
// Process post parameters
// =======================
$chkInsPasswdOld  = isset($_POST['tfPasswordOld'])    ? $_POST['tfPasswordOld']   : "";
$chkInsPasswdNew1 = isset($_POST['tfPasswordNew1'])   ? $_POST['tfPasswordNew1']  : "";
$chkInsPasswdNew2 = isset($_POST['tfPasswordNew2'])   ? $_POST['tfPasswordNew2']  : "";
//
// Change password
// =======================
if (($chkInsPasswdOld != "") && ($chkInsPasswdNew1 != "")) {
  	// Check old password
  	$strSQL    = "SELECT * FROM `tbl_user` WHERE `username`='".$_SESSION['username']."' AND `password`=MD5('$chkInsPasswdOld')";
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
  	if ($booReturn == false) {
    	$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
  	} else if ($intDataCount == 1) {
    	// Check equality and password length
		if (($chkInsPasswdNew1 === $chkInsPasswdNew2) && (strlen($chkInsPasswdNew1) >=5)) {
      		// Update database
      		$strSQLUpdate = "UPDATE `tbl_user` SET `password`=MD5('$chkInsPasswdNew1'), 
							 `last_login`=NOW() WHERE `username`='".$_SESSION['username']."'";
      		$booReturn = $myDBClass->insertData($strSQLUpdate);
      		if ($booReturn == true) {
        		$myDataClass->writeLog(translate('Password successfully modified'));
        		// Force new login
        		$_SESSION['logged_in'] = 0;
    			$_SESSION['username']  = "";
				$_SESSION['userid']    = 0;
				$_SESSION['groupadm']  = 0;
				$_SESSION['domain']    = 0;
        		header("Location: ".$SETS['path']['protocol']."://".$_SERVER['HTTP_HOST'].$SETS['path']['root']."index.php");
      		} else {
        		$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
      		}
    	} else {
      		// New password wrong 
      		$strMessage .= translate('Password too short or password fields unequally!');
    	}
  	} else {
    	// Old password wrong
    	$strMessage .= translate('Old password is wrong');
  	}
} else if (isset($_POST['submit'])) {
  	// Wrong data
 	$strMessage .= translate('Database entry failed! Not all necessary data filled in!');
}
//
// Output header variable
// ======================
echo $tplHeaderVar;
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Include content
// ===============
foreach($arrDescription AS $elem) {
  	$conttp->setVariable($elem['name'],$elem['string']);
}
$conttp->setVariable("LANG_SAVE",translate('Save'));
$conttp->setVariable("LANG_ABORT",translate('Abort'));
$conttp->setVariable("FILL_ALLFIELDS",translate('Please fill in all fields marked with an *'));
$conttp->setVariable("FILL_NEW_PASSWD_NOT_EQUAL",translate('The new passwords are not equal!'));
$conttp->setVariable("FILL_NEW_PWDSHORT",translate('The new password is too short - use at least 6 characters!'));
if ($strMessage != "") $conttp->setVariable("PW_MESSAGE",$strMessage);
$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->parse("passwordsite");
$conttp->show("passwordsite");
//
// Include footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>