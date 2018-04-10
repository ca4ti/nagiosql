<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2018 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Start script
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.0
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Path settings
// ===================
$preRelPath  = strchr(filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING), 'index.php', true);
$preBasePath = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_STRING).$preRelPath;
//
// Destroy old session data
// ========================
session_start();
session_destroy();
//
// Define common variables
// =======================
$intPageID  = 0;
$preContent = "index.htm.tpl";
//
// Redirect to installation wizard
// ===============================
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    header("Location: install/index.php");
}
//
// Include preprocessing file
// ==========================
$preAccess    = 0;
$preFieldvars = 0;
require($preBasePath.'functions/prepend_adm.php');
//
// Include Content
// ===============
$conttp->setVariable("TITLE", translate('Welcome to'));
$conttp->setVariable("TITLE_LOGIN", translate('Welcome'));
$conttp->setVariable("LOGIN_TEXT", translate('Please enter your username and password to access NagiosQL.<br>If '
    . 'you forgot one of them, please contact your Administrator.'));
$conttp->setVariable("USERNAME", translate('Username'));
$conttp->setVariable("PASSWORD", translate('Password'));
$conttp->setVariable("LOGIN", translate('Login'));
if (isset($_SESSION['strLoginMessage']) && ($_SESSION['strLoginMessage'] != "")) {
    $conttp->setVariable("MESSAGE", $_SESSION['strLoginMessage']);
} else {
    $conttp->setVariable("MESSAGE", "&nbsp;");
}
$conttp->setVariable("ACTION_INSERT", filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING));
$conttp->setVariable("IMAGE_PATH", "images/");
$conttp->parse("main");
$conttp->show("main");
//
// Include footer
// ==============
$maintp->setVariable("VERSION_INFO", "<a href='https://sourceforge.net/projects/nagiosql/' "
    . "target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
