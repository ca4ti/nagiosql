<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2007 by Martin Willisegger / nagiosql_v2@wizonet.ch
//
// Projekt:	NagiosQL Applikation
// Author :	Martin Willisegger
// Datum:	12.03.2007
// Zweck:	Indexseite
// Datei:	index.php
// Version: 2.00.00 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Menuvariabeln für diese Seite
// =============================
$intMain 		= 1;
$intSub  		= 0;
$intMenu 		= 1;
$preContent 	= "index.tpl.htm";
//
// Übergabeparameter
// =================
$chkInsName 	= isset($_POST['tfUsername']) 	? $_POST['tfUsername']	: "";
$chkInsPasswd 	= isset($_POST['tfPassword']) 	? $_POST['tfPassword'] 	: "";
$chkLogout		= isset($_GET['logout'])		? $_GET['logout']		: "rr";
if ($chkInsName != "") {
	$preUsername = $chkInsName;
	$prePassword = $chkInsPasswd;
}
//
// Vorgabedatei einbinden
// ======================
$preNoLogin = true;
$SETS 		= parse_ini_file("config/settings.ini",TRUE);
if (!file_exists($SETS['path']['physical']."functions/prepend_adm.php")) {
   echo "Please check your site configuration in config/settings.ini!";
   exit;
}
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Seite umleiten, wenn Login erfolgreich
// ======================================
if (($_SESSION['startsite'] != "") && ($_SESSION['username'] != "")) {
	header("Location: ".$SETS['path']['protocol']."://".$_SERVER['HTTP_HOST'].$_SESSION['startsite']);
}
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['user']['login']);
$maintp->parse("header");
$maintp->show("header");
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['login']); 
$conttp->setVariable("USERNAME",$LANG['user']['username']);
$conttp->setVariable("PASSWORD",$LANG['user']['password']);
$conttp->setVariable("LOGIN",$LANG['user']['login']);
if (isset($strLoginMessage) && ($strLoginMessage != "")) $conttp->setVariable("MESSAGE",$strLoginMessage);
$conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->parse("main");
$conttp->show("main");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>