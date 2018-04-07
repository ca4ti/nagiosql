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
// Component : Start script
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
$intMain    	= 1;
$intSub     	= 0;
$intMenu    	= 1;
$preContent   = "index.tpl.htm";
//
// Process post/get parameters
// ===========================
$chkInsName    	= isset($_POST['tfUsername'])		  ? $_POST['tfUsername']  : "";
$chkInsPasswd  	= isset($_POST['tfPassword'])   	? $_POST['tfPassword']  : "";
$chkLogout     	= isset($_GET['logout'])       		? htmlspecialchars($_GET['logout'], ENT_QUOTES, 'utf-8')       : "rr";
//
// Redirect to installation wizard if PHP smaller than 5.2
//
define('MIN_PHP_VERSION', '5.2.0');
if (version_compare(PHP_VERSION, MIN_PHP_VERSION, '<')) {
	header("Location: install/index.php");
}
//
// Include preprocessing file
// ==========================
$preAccess    	= 0;
$preFieldvars 	= 0;
require("functions/prepend_adm.php");
//
// Include Content
// ===============
$conttp->setVariable("TITLE",translate('Welcome to'));
$conttp->setVariable("TITLE_LOGIN",translate('Welcome'));
$conttp->setVariable("LOGIN_TEXT",translate('Please enter your username and password to access NagiosQL.<br>If you forgot one of them, please contact your Administrator.'));
$conttp->setVariable("USERNAME",translate('Username'));
$conttp->setVariable("PASSWORD",translate('Password'));
$conttp->setVariable("LOGIN",translate('Login'));
if (isset($_SESSION['strLoginMessage']) && ($_SESSION['strLoginMessage'] != "")) {
  $conttp->setVariable("MESSAGE",$_SESSION['strLoginMessage']);
} else {
	$conttp->setVariable("MESSAGE","&nbsp;");
}
$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
$conttp->setVariable("IMAGE_PATH","images/");
$conttp->parse("main");
$conttp->show("main");
//
// Include footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>