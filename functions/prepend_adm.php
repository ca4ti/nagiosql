<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2006 by Martin Willisegger / nagiosql_v2@wizonet.ch
//
// Projekt:	NagiosQL Applikation
// Author :	Martin Willisegger
// Datum:	12.03.2007
// Zweck:	Allgemeine Funtionen für jede Seite des Administrationsbereiches
// Datei:	prepend_adm.php
// Version: 2.00.00 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
//
// Einbinden der externen Funktions- und Definitionsdateien
// ========================================================
include($SETS['path']['physical']."functions/nag_class.php");
include($SETS['path']['physical']."functions/data_class.php");
include($SETS['path']['physical']."functions/config_class.php");
include($SETS['path']['physical']."functions/mysql_class.php");
require_once($SETS['path']['IT']);
if (!class_exists("HTML_Template_IT")) {
   echo "Required PEAR module HTML_Template_IT not found - read documentation!";
   exit;
}
//
// Versionsverwaltung
// ==================
$setMainVersion  = "2";
$setSubVersion   = "00";
$setPatchLevel   = "00";
$setFileVersion  = $setMainVersion.".".$setSubVersion."-P".$setPatchLevel;
$setTitleVersion = $setMainVersion.".".$setSubVersion;
// 
// Session starten
// ===============
session_start();
$_SESSION['SETS'] = $SETS;
if (!isset($_SESSION['username']))  $_SESSION['username'] = "";
if (!isset($_SESSION['startsite'])) $_SESSION['startsite'] = "";
if (isset($_GET['menu']) && ($_GET['menu'] == "visible"))   $_SESSION['menu'] = "visible";
if (isset($_GET['menu']) && ($_GET['menu'] == "invisible")) $_SESSION['menu'] = "invisible";
if (isset($chkLogout) && ($chkLogout == "yes")) session_destroy();
//
// Klassen initialisieren
// ======================
$myDBClass     = new mysqldb;
$myVisClass    = new nagvisual;
$myDataClass   = new nagdata;
$myConfigClass = new nagconfig;
//
// Klassen gegeseitig propagieren
// ===============================
$myVisClass->myDBClass		=& $myDBClass;
$myVisClass->myDataClass	=& $myDataClass;
$myDataClass->myDBClass		=& $myDBClass;
$myDataClass->myVisClass	=& $myVisClass;
$myConfigClass->myDBClass	=& $myDBClass;
$myConfigClass->myVisClass	=& $myVisClass;
$myConfigClass->myDataClass	=& $myDataClass;
//
// Sprachdatei einbinden
// =====================
$intReturn = $myVisClass->getLanguage($SETS['data']['lang'],$LANG);
if ($intReturn != 0) {$strLoginMessage = "Could not load language definition from database!<br>Please check your site configuration and/or database connection!";}
$_SESSION['LANG']           = $LANG;
$myVisClass->arrLanguage    = $LANG;	
$myDataClass->arrLanguage   = $LANG;
$myConfigClass->arrLanguage = $LANG;
//
// Login verarbeiten
// =================
if (isset($preUsername)) {
	$strSQL    = "SELECT * FROM tbl_user WHERE username='$preUsername' AND password=MD5('$prePassword')";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataUser,$intDataCount);
	if ($booReturn == false) {
		if (!isset($strMessage)) $strMessage = "";
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
	} else if ($intDataCount == 1) {	
		// Session Variabeln setzen
		$_SESSION['username']  = $arrDataUser[0]['username'];
		$_SESSION['startsite'] = $SETS['path']['root']."admin.php";
		$_SESSION['keystring'] = $arrDataUser[0]['access_rights'];
		$_SESSION['timestamp'] = mktime();
		// Letzte Loginzeit aufdatieren
		$strSQLUpdate = "UPDATE tbl_user SET last_login=NOW() WHERE username='$preUsername'";
		$booReturn    = $myDBClass->insertData($strSQLUpdate);		
		$myDataClass->writeLog($LANG['logbook']['successlogin']);
	} else {
		$strLoginMessage = $LANG['user']['loginfail'];
		$myDataClass->writeLog($LANG['logbook']['faillogin']." - Username: ".$preUsername);
	}
}
//
// Login überprüfen und aktualisieren
// ===================================
if ($_SESSION['username'] != "") {
	$strSQL    = "SELECT * FROM tbl_user WHERE username='".$_SESSION['username']."'";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataUser,$intDataCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
	} else if ($intDataCount == 1) {
		// Zeit abgelaufen?
		if (mktime() - $_SESSION['timestamp'] > $SETS['security']['logofftime']) {
			// Neues Login erzwingen
			$myDataClass->writeLog($LANG['logbook']['timeout']." ".(mktime() - $_SESSION['timestamp']." - User: ".$_SESSION['username']));
			$_SESSION['username'] = "";
			header("Location: ".$SETS['path']['protocol']."://".$_SERVER['HTTP_HOST'].$SETS['path']['root']."index.php");
		} else {
			// Rechte kontrollieren
			if (isset($preAccess) && ($preAccess == 1) && ($intSub != 0)) {
				$strKey    = $myDBClass->getFieldData("SELECT access_rights FROM tbl_submenu WHERE id=$intSub");
				$intResult = $myVisClass->checkKey($_SESSION['keystring'],$strKey);
				// Falls keine Rechte - Fehlerseite anzeigen
				if ($intResult != 0) {
					$myDataClass->writeLog($LANG['logbook']['errorsite']." ".$_SERVER['PHP_SELF']);
					header("Location: ".$SETS['path']['protocol']."://".$_SERVER['HTTP_HOST'].$SETS['path']['root']."errorsite.php");
				}
			}
			// Zeit aktualisieren
			$_SESSION['timestamp'] = mktime();
		}
	} else {
		// Neues Login erzwingen
		$myDataClass->writeLog($LANG['logbook']['userfail']);
		$_SESSION['username'] = "";
		header("Location: ".$SETS['path']['protocol']."://".$_SERVER['HTTP_HOST'].$SETS['path']['root']."index.php");
	}
} else if (!isset($preNoLogin)) {
	// Neues Login erzwingen
	$_SESSION['username'] = "";
	header("Location: ".$SETS['path']['protocol']."://".$_SERVER['HTTP_HOST'].$SETS['path']['root']."index.php");
}
//
// Haupttemplate einbinden
// =======================
$arrTplOptions = array('use_preg' => false);
$maintp = new HTML_Template_IT($SETS['path']['physical']."/templates/"); 
$maintp->loadTemplatefile("main.tpl.htm", true, false);
$maintp->setOptions($arrTplOptions);
$maintp->setVariable("META_DESCRIPTION","NagiosQL System Monitoring Administration Tool");
$maintp->setVariable("REVISIT","4 Weeks");
$maintp->setVariable("AUTHOR","Martin Willisegger");
$maintp->setVariable("LANGUAGE","de");
$maintp->setVariable("PUBLISHER","Martin Willisegger");
$maintp->setVariable("GENERATOR","Macromedia Dreamweaver MX");
$maintp->setVariable("ADMIN","<a href=\"".$SETS['path']['root']."admin.php\"><img class=\"imagelink\" src=\"".$SETS['path']['root']."images/admin.png\" width=\"94\" height=\"37\" alt=\"".$LANG['position']['admin']."\"></a>");;
$maintp->setVariable("BASE_PATH",$SETS['path']['root']);
$maintp->setVariable("ROBOTS","noindex,nofollow");
$maintp->setVariable("PAGETITLE","NagiosQL - Version ".$setTitleVersion);
if ($_SESSION['username'] != "") $maintp->setVariable("LOGIN_INFO",$LANG['user']['loggedin']." ".$_SESSION['username']);
$maintp->setVariable("IMAGEDIR",$SETS['path']['root']."images/");
//
// Content und Master Template einbinden
// ======================================
if (isset($preContent) && ($preContent != "")) {
	$conttp = new HTML_Template_IT($SETS['path']['physical']."/templates/admin/");
	$conttp->loadTemplatefile($preContent, true, true);
	$conttp->setOptions($arrTplOptions);
	$mastertp = new HTML_Template_IT($SETS['path']['physical']."/templates/admin/");
	$mastertp->loadTemplatefile("admin_master.tpl.htm", true, true);
	$mastertp->setOptions($arrTplOptions);
}
//
// Standardbergabeparameter verarbeiten
// =====================================
$chkModus   	= isset($_GET['modus'])  		? $_GET['modus']  		: "display";
$chkModus   	= isset($_POST['modus']) 		? $_POST['modus'] 		: "display";
$chkLimit   	= isset($_POST['hidLimit']) 	? $_POST['hidLimit'] 	: 0;
$chkHidModify  	= isset($_POST['hidModify']) 	? $_POST['hidModify']	: "";
$chkSelModify	= isset($_POST['selModify'])	? $_POST['selModify']	: "";
$chkListId	  	= isset($_POST['hidListId']) 	? $_POST['hidListId']	: 0;
$chkDataId		= isset($_POST['hidId'])		? $_POST['hidId']		: 0;
$chkActive		= isset($_POST['chbActive'])	? $_POST['chbActive']	: 0;
$hidActive		= isset($_POST['hidActive'])	? $_POST['hidActive']	: 0;
if ($chkModus == "add")    $chkSelModify = "";
if ($chkHidModify != "")   $chkSelModify = $chkHidModify;
if (isset($_GET['limit'])) $chkLimit = $_GET['limit'];
?>