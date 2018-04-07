<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project  : NagiosQL
// Component: Installer
// Website  : http://www.nagiosql.org
// Date     : $LastChangedDate: 2011-03-13 14:00:26 +0100 (So, 13. Mär 2011) $
// Author   : $LastChangedBy: rouven $
// Version  : 3.1.1
// Revision : $LastChangedRevision: 1058 $
//
///////////////////////////////////////////////////////////////////////////////
error_reporting(E_ALL);
session_start();
// Set Defaults
$step = isset($_SESSION['SETS']['install']['step']) ? $step = $_SESSION['SETS']['install']['step'] : "1";
// Security
if (isset($_GET['step']) AND is_numeric(htmlspecialchars($_GET['step'], ENT_QUOTES, 'utf-8'))) {
  $step = htmlspecialchars($_GET['step'], ENT_QUOTES, 'utf-8');
}
if (isset($_POST['step']) AND is_numeric(htmlspecialchars($_POST['step'], ENT_QUOTES, 'utf-8'))) {
  $step = htmlspecialchars($_POST['step'], ENT_QUOTES, 'utf-8');
}
if (isset($_POST['step']) AND htmlspecialchars($_POST['step'], ENT_QUOTES, 'utf-8') > 3) {
  die('Trying to cheat?');
}
require_once("functions/func_installer.php");
// Interpret forms
if (isset($_POST['step']) AND htmlspecialchars($_POST['step'], ENT_QUOTES, 'utf-8') == 1) {
  $_SESSION['SETS']['data']['locale']     		= htmlspecialchars($_POST['locale'], ENT_QUOTES, 'utf-8') != ""             ? htmlspecialchars($_POST['locale'], ENT_QUOTES, 'utf-8')             : "en_EN";
  $_SESSION['SETS']['install']['InstallType'] 	= htmlspecialchars($_POST['butInstallType'], ENT_QUOTES, 'utf-8') != ""     ? htmlspecialchars($_POST['butInstallType'], ENT_QUOTES, 'utf-8')     : "Installation";
  $_SESSION['SETS']['install']['javascript']	= htmlspecialchars($_POST['js'], ENT_QUOTES, 'utf-8') != ""                 ? htmlspecialchars($_POST['js'], ENT_QUOTES, 'utf-8')                 : "";
}
if (isset($_POST['step']) AND htmlspecialchars($_POST['step'], ENT_QUOTES, 'utf-8') == 3) {
  $_SESSION['SETS']['db']['server']       		= isset($_POST['txtDBserver'])       ? $_POST['txtDBserver']    : $_SESSION['SETS']['db']['server'];
  $_SESSION['SETS']['db']['port']         		= isset($_POST['txtDBport'])         ? $_POST['txtDBport']+0    : $_SESSION['SETS']['db']['port'];
  $_SESSION['SETS']['db']['database']     		= isset($_POST['txtDBname'])         ? $_POST['txtDBname']      : $_SESSION['SETS']['db']['database'];
  $_SESSION['SETS']['db']['username']			= isset($_POST['txtDBuser'])         ? $_POST['txtDBuser']      : $_SESSION['SETS']['db']['username'];
  $_SESSION['SETS']['db']['password']			= isset($_POST['txtDBpass'])         ? $_POST['txtDBpass']      : $_SESSION['SETS']['db']['password'];
  $_SESSION['SETS']['install']['db_privuser']	= isset($_POST['txtDBprivUser'])     ? $_POST['txtDBprivUser']  : "root";
  $_SESSION['SETS']['install']['db_privpwd']    = isset($_POST['txtDBprivPass'])     ? $_POST['txtDBprivPass']  : "";
  $_SESSION['SETS']['install']['db_drop']		= isset($_POST['chkDrop'])           ? $_POST['chkDrop']+0      : 0;
  $_SESSION['SETS']['install']['sampleData']	= isset($_POST['chkSample'])         ? $_POST['chkSample']+0	: 0;
  $_SESSION['SETS']['install']['ql_user']       = isset($_POST['txtQLuser'])         ? $_POST['txtQLuser']		: "admin";
  $_SESSION['SETS']['install']['ql_pass']       = isset($_POST['txtQLpass'])         ? $_POST['txtQLpass']      : "admin";
}
// Language Definition
if (extension_loaded('gettext')) {
	putenv("LC_ALL=".$_SESSION['SETS']['data']['locale'].".".$_SESSION['SETS']['data']['encoding']);
	putenv("LANG=".$_SESSION['SETS']['data']['locale'].".".$_SESSION['SETS']['data']['encoding']);
	$localPath = BASE_PATH."/../config/locale";
	setlocale(LC_ALL, $_SESSION['SETS']['data']['locale'].".".$_SESSION['SETS']['data']['encoding']); // defines language
	bindtextdomain($_SESSION['SETS']['data']['locale'], $localPath); // location of language files
	bind_textdomain_codeset($_SESSION['SETS']['data']['locale'], $_SESSION['SETS']['data']['encoding']); // define encoding and domain
	textdomain($_SESSION['SETS']['data']['locale']); // use the domain
	require_once("../functions/translator.php");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>[NagiosQL] Installation Wizard</title>
<link rel="stylesheet" type="text/css" href="css/install.css" />
<link rel="stylesheet" type="text/css" media="screen" href="css/screen.css" />
<link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/cmxform.js"></script>
<link rel="stylesheet" type="text/css" href="../functions/yui/build/container/assets/container.css" />
<script type="text/javascript" src="../functions/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="../functions/yui/build/container/container-min.js"></script>
<script type="text/javascript" src="js/yh_tooltip.js"></script>
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/validation.js"></script>
</head>
<body>
  <div id="page_margins">
    <div id="page">
      <div id="header">
        <div id="header-logo">
          <a href="index.php"><img src="images/nagiosql.png" border="0" alt="NagiosQL Logo" title="NagiosQL Logo"></a>
        </div>
        <div id="documentation">
          <a href="http://www.nagiosql.org/faq.html" target="_blank"><?php echo translate("Online Documentation"); ?></a>   
        </div>
      </div>
      <div id="main">
        <?php include "step".$step.".php"; ?>
      </div>
      <div id="footer">
        <a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> <?php echo BASE_VERSION; ?>
      </div>
    </div>
  </div>
</body>
</html>