<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2017 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Start script
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2017-06-22 09:29:35 +0200 (Thu, 22 Jun 2017) $
// Author    : $LastChangedBy: martin $
// Version   : 3.3.0
// Revision  : $LastChangedRevision: 2 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Destroy old session data
// ========================
session_start();
session_destroy();
//
// Define common variables
// =======================
$intPageID		= 0;
$preContent		= "index.tpl.htm";
//
// Redirect to installation wizard
// ===============================
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