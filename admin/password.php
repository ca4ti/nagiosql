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
// Component : Password administration
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
$prePageId			= 31;
$preContent   		= "admin/admin_master.tpl.htm";
$preAccess    		= 1;
$preFieldvars 		= 1;
$preShowHeader  	= 0;
//
// Include preprocessing files
// ===========================
require("../functions/prepend_adm.php");
require("../functions/prepend_content.php");
//
// Change password
// =======================
if (($chkTfValue1 != "") && ($chkTfValue2 != "")) {
  	// Check old password
  	$strSQL    = "SELECT * FROM `tbl_user` WHERE `username`='".$_SESSION['username']."' AND `password`=MD5('$chkTfValue1')";
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
  	if ($booReturn == false) {
		$myVisClass->processMessage(translate('Error while selecting data from database:'),$strErrorMessage);
		$myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
  	} else if ($intDataCount == 1) {
    	// Check equality and password length
		if (($chkTfValue2 === $chkTfValue3) && (strlen($chkTfValue2) >=5)) {
      		// Update database
      		$strSQLUpdate = "UPDATE `tbl_user` SET `password`=MD5('$chkTfValue2'), 
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
        		header("Location: ".$SETS['path']['protocol']."://".$_SERVER['HTTP_HOST'].$_SESSION['SETS']['path']['base_url']."index.php");
      		} else {
				$myVisClass->processMessage(translate('Error while selecting data from database:'),$strErrorMessage);
				$myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
      		}
    	} else {
      		// New password wrong 
			$myVisClass->processMessage(translate('Password too short or password fields unequally!'),$strErrorMessage);
    	}
  	} else {
    	// Old password wrong
		$myVisClass->processMessage(translate('Old password is wrong'),$strErrorMessage);
  	}
} else if (isset($_POST['submit'])) {
  	// Wrong data
	$myVisClass->processMessage(translate('Database entry failed! Not all necessary data filled in!'),$strErrorMessage);
}
//
// Output header variable
// ======================
echo $tplHeaderVar;
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
if ($strErrorMessage != "") $conttp->setVariable("ERRORMESSAGE",$strErrorMessage);
$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
$conttp->setVariable("IMAGE_PATH",$_SESSION['SETS']['path']['base_url']."images/");
// Check access rights for adding new objects
if ($myVisClass->checkAccGroup($prePageKey,'write') != 0) $conttp->setVariable("ADD_CONTROL","disabled=\"disabled\"");
$conttp->parse("passwordsite");
$conttp->show("passwordsite");
//
// Include footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>