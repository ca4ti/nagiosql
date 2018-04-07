<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL 2005
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005 by Martin Willisegger / nagios.ql2005@wizonet.ch
//
// Projekt:	NagiosQL Applikation
// Author :	Martin Willisegger
// Datum:	11.03.2005
// Zweck:	Allgemeine Funtionen für jede Seite des Administrationsbereiches
// Datei:	prepend_adm.php
// Version:	1.00
//
///////////////////////////////////////////////////////////////////////////////
//
// Einbinden der externen Funktions- und Definitionsdateien
// ========================================================
include($SETS['path']['physical']."functions/nag_class.php");
include($SETS['path']['physical']."functions/mysql_class.php");
require_once($SETS['path']['IT']);
$LANG = parse_ini_file($SETS['path']['physical']."config/".$SETS['data']['lang'],TRUE);
// 
// Session starten
// ===============
session_start();
$_SESSION['SETS'] = $SETS;
$_SESSION['LANG'] = $LANG;
if (!isset($_SESSION['username']))  $_SESSION['username'] = "";
if (!isset($_SESSION['startsite'])) $_SESSION['startsite'] = "";
if (isset($_GET['menu']) && ($_GET['menu'] == "visible"))   $_SESSION['menu'] = "visible";
if (isset($_GET['menu']) && ($_GET['menu'] == "invisible")) $_SESSION['menu'] = "invisible";
if (isset($chkLogout) && ($chkLogout == "yes")) $_SESSION['username'] = "";
//
// Klassen initialisieren
// ======================
$myVisClass = new nagvisual;
$myDBClass  = new mysqldb;
//
// Login verarbeiten
// =================
if (isset($preUsername)) {
	$strSQL    = "SELECT * FROM tbl_user WHERE username='$preUsername' AND password=MD5('$prePassword')";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataUser,$intDataCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
	} else if ($intDataCount == 1) {	
		// Session Variabeln setzen
		$_SESSION['username']  = $arrDataUser[0]['username'];
		$_SESSION['startsite'] = $SETS['path']['root']."admin.php";
		$arrRights['admin1']   = $arrDataUser[0]['admin1'];
		$arrRights['admin2']   = $arrDataUser[0]['admin2'];
		$arrRights['admin3']   = $arrDataUser[0]['admin3'];
		if (($arrDataUser[0]['admin1'] == 1) || ($arrDataUser[0]['admin2'] == 1) || ($arrDataUser[0]['admin3'] == 1)) $arrRights['admin_all'] = 1;
		$_SESSION['rights']	   = $arrRights;
		$_SESSION['timestamp'] = mktime();
		// Letzte Loginzeit aufdatieren
		$strSQLUpdate = "UPDATE tbl_user SET last_login=NOW() WHERE username='$preUsername'";
		$booReturn    = $myDBClass->insertData($strSQLUpdate);		
		$myVisClass->writeLog($LANG['logbook']['successlogin']);
	} else {
		$strLoginMessage = $LANG['user']['loginfail'];
		$myVisClass->writeLog($LANG['logbook']['faillogin']." User: ".$preUsername);
	}
}
//
// Login überprüfen und aktualisieren
// ==================================
if ($_SESSION['username'] != "") {
	$strSQL    = "SELECT * FROM tbl_user WHERE username='".$_SESSION['username']."'";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataUser,$intDataCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
	} else if ($intDataCount == 1) {
		// Zeit abgelaufen?
		if (mktime() - $_SESSION['timestamp'] > $SETS['security']['logofftime']) {
			// -> Neues Login erzwingen
			$myVisClass->writeLog($LANG['logbook']['timeout']." ".(mktime() - $_SESSION['timestamp']." ".$_SESSION['username']));
			$_SESSION['username'] = "";
			header("Location: ".$SETS['path']['protocol']."://".$_SERVER['HTTP_HOST'].$SETS['path']['root']."index.php");
		} else {
			// Rechte kontrollieren
			if (isset($preRights)) {
			    $intRightsFailed = 1;
				if (($preRights == "admin_all") && (($arrDataUser[0]['admin1'] == 1) || ($arrDataUser[0]['admin2'] == 1) || ($arrDataUser[0]['admin3'] == 1))) $intRightsFailed = 0;
				if (($preRights == "admin1") && ($arrDataUser[0]['admin1'] == 1)) $intRightsFailed = 0;
				if (($preRights == "admin2") && ($arrDataUser[0]['admin2'] == 1)) $intRightsFailed = 0;
				if (($preRights == "admin3") && ($arrDataUser[0]['admin3'] == 1)) $intRightsFailed = 0;
				// Falls keine Rechte - Fehlerseite anzeigen
				if ($intRightsFailed == 1) {
					$myVisClass->writeLog($LANG['logbook']['errorsite']." ".$_SERVER['PHP_SELF']);
					header("Location: ".$SETS['path']['protocol']."://".$_SERVER['HTTP_HOST'].$SETS['path']['root']."errorsite.php");
				}
			}
			// Zeit aktualisieren
			$_SESSION['timestamp'] = mktime();
		}
	} else {
		// -> Neues Login erzwingen
		$myVisClass->writeLog($LANG['logbook']['userfail']);
		$_SESSION['username'] = "";
		header("Location: ".$SETS['path']['protocol']."://".$_SERVER['HTTP_HOST'].$SETS['path']['root']."index.php");
	}
} else if (!isset($preNoLogin)) {
	// -> Neues Login erzwingen
	$_SESSION['username'] = "";
	header("Location: ".$SETS['path']['protocol']."://".$_SERVER['HTTP_HOST'].$SETS['path']['root']."index.php");
}
//
// Haupttemplate einbinden
// =======================
$maintp = new HTML_Template_IT($SETS['path']['physical']."/templates/"); 
$maintp->loadTemplatefile("main.tpl.htm", true, false);
$maintp->setVariable("META_DESCRIPTION","NagiosQL 2005 System Monitoring Tool");
$maintp->setVariable("REVISIT","4 Weeks");
$maintp->setVariable("AUTHOR","Martin Willisegger");
$maintp->setVariable("LANGUAGE","de");
$maintp->setVariable("PUBLISHER","Martin Willisegger");
$maintp->setVariable("GENERATOR","Macromedia Dreamweaver MX");
$maintp->setVariable("ADMIN","<a href=\"".$SETS['path']['root']."admin.php\"><img class=\"imagelink\" src=\"".$SETS['path']['root']."images/admin.png\" width=\"94\" height=\"37\" alt=\"".$LANG['position']['admin']."\"></a>");;
//$maintp->setVariable("MONITOR","<img src=\"".$SETS['path']['root']."images/monitor.png\" width=\"94\" height=\"37\" alt=\"\">");
$maintp->setVariable("BASE_PATH",$SETS['path']['root']);
$maintp->setVariable("ROBOTS","noindex,nofollow");
$maintp->setVariable("PAGETITLE","NagiosQL 2005 - Version 1.00-RC2");
if ($_SESSION['username'] != "") $maintp->setVariable("LOGIN_INFO",$LANG['user']['loggedin'].$_SESSION['username']);
$maintp->setVariable("IMAGEDIR",$SETS['path']['root']."images/");
//
// Content und Master Template einbinden
// ======================================
if (isset($preContent) && ($preContent != "")) {
	$conttp = new HTML_Template_IT($SETS['path']['physical']."/templates/admin/");
	$conttp->loadTemplatefile($preContent, true, true);
	$mastertp = new HTML_Template_IT($SETS['path']['physical']."/templates/admin/");
	$mastertp->loadTemplatefile("admin_master.tpl.htm", true, true);
}
//
// Standardübergabeparameter verarbeiten
// =====================================
$chkModus   	= isset($_GET['modus'])  		? $_GET['modus']  		: "display";
$chkModus   	= isset($_POST['modus']) 		? $_POST['modus'] 		: "display";
$chkLimit   	= isset($_POST['hidLimit']) 	? $_POST['hidLimit'] 	: 0;
$chkHidModify  	= isset($_POST['hidModify']) 	? $_POST['hidModify']	: "";
$chkSelModify	= isset($_POST['selModify'])	? $_POST['selModify']	: "";
$chkListId	  	= isset($_POST['hidListId']) 	? $_POST['hidListId']	: 0;
$chkDataId		= isset($_POST['hidId'])		? $_POST['hidId']		: 0;
$chkActive		= isset($_POST['chbActive'])	? $_POST['chbActive']	: 0;
if ($chkModus == "add")    $chkSelModify = "";
if ($chkHidModify != "")   $chkSelModify = $chkHidModify;
if (isset($_GET['limit'])) $chkLimit = $_GET['limit'];
?>