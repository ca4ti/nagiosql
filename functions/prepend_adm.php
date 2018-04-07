<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
///////////////////////////////////////////////////////////////////////////////
//
// Project   : NagiosQL
// Component : Preprocessing script
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-04-11 08:06:25 +0200 (Mo, 11. Apr 2011) $
// Author    : $LastChangedBy: martin $
// Version   : 3.1.1
// Revision  : $LastChangedRevision: 1072 $
//
///////////////////////////////////////////////////////////////////////////////
//error_reporting(E_ALL);
error_reporting(E_ERROR);
//
// Security Protection
// ===================
if (isset($_GET['SETS']) || isset($_POST['SETS'])) {
	$SETS = "";
}
//
// Timezone settings (>=PHP5.1)
// ============================
if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get")) {
	@date_default_timezone_set(@date_default_timezone_get());
}
//
// Define common variables
// =======================
$strMessage     = "";
$tplHeaderVar   = "";
$chkDomainId 		= 0;
$chkGroupAdm 		= 0;
$intError 			= 0;
$setDBVersion 	= "unknown";
$setFileVersion	= "3.1.1";
//
// Start PHP session
// =================
session_start();
//
// Path constants
// ==============
define('BASE_PATH', filter_var(str_replace("functions","",dirname(__FILE__))), FILTER_SANITIZE_STRING);
if (!isset($_SESSION['BASE_URL'])) { 
	$urlpath=dirname(filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
	if (substr($urlpath,-1) != "/") {
		$_SESSION['BASE_URL'] = $urlpath."/";
	} else {
		$_SESSION['BASE_URL'] = $urlpath;
	}
}
define('BASE_URL', $_SESSION['BASE_URL']);
//
// Read settings file
// ==================
function parseIniFile($iIniFile) {
	$aResult  =
  	$aMatches = array();
  	$a = &$aResult;
  	$s = '\s*([[:alnum:]_\- \*]+?)\s*'; 
	preg_match_all('#^\s*((\['.$s.'\])|(("?)'.$s.'\\5\s*=\s*("?)(.*?)\\7))\s*(;[^\n]*?)?$#ms', @file_get_contents($iIniFile), $aMatches, PREG_SET_ORDER);
  	foreach ($aMatches as $aMatch) {
    	if (empty($aMatch[2])) {
        	$a [$aMatch[6]] = $aMatch[8];
      	} else {  
			$a = &$aResult [$aMatch[3]];
		}
  	}
  	return $aResult;
}
// Check if we need to launch the installer
$inifile=BASE_PATH .'config/settings.php';
if (! file_exists($inifile) OR ! is_readable($inifile)) {
	header("Location: ". BASE_URL ."install/index.php");
}
$SETS = parseIniFile($inifile);
if (!isset($SETS['path']['physical']) or !isset($SETS['path']['root'])) {
	$SETS['path']['physical']	= BASE_PATH;
	$SETS['path']['root'] 		= BASE_URL;
}
// Store the settings to the session
$_SESSION['SETS'] = $SETS;
//
// Include PEAR
//
require_once(BASE_PATH .'libraries/pear/PEAR.php');
//
// Include external function/class files - part 1
// ==============================================
include("mysql_class.php");
require("translator.php");
//
// Initialize classes - part 1
// ===========================
$myDBClass = new mysqldb;
if ($myDBClass->error == true) {
  	$strMessage .= translate('Error while connecting to database:')."<br>".$myDBClass->strDBError."<br>";
  	$intError 	 = 1;
}
//
// Get additional configuration from the table tbl_settings
// ========================================================
if ($intError == 0) {
	$strSQL    = "SELECT `category`,`name`,`value` FROM `tbl_settings`";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	if ($booReturn == false) {
  		$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
		$intError 	 = 1;
	} else if ($intDataCount != 0) {
  		for ($i=0;$i<$intDataCount;$i++) {
    		$SETS[$arrDataLines[$i]['category']][$arrDataLines[$i]['name']] = $arrDataLines[$i]['value'];
  		}
	}
}
//
// Enable PHP gettext functionality
// ================================
if ($intError == 0) {
	$arrLocale = explode(".",$SETS['data']['locale']);
	$strDomain = $arrLocale[0];
	$loc = setlocale(LC_ALL, $SETS['data']['locale'], $SETS['data']['locale'].".utf-8", $SETS['data']['locale'].".utf-8", $SETS['data']['locale'].".utf8", "en_GB", "en_GB.utf-8", "en_GB.utf8");
	if (!isset($loc)) {
		$strMessage .= translate("Error in setting the correct locale, please report this error with the associated output of  'locale -a' to bugs@nagiosql.org")."<br>";
		 $intError 	 = 1;
	}
	putenv("LC_ALL=".$SETS['data']['locale'].".utf-8");
	putenv("LANG=".$SETS['data']['locale'].".utf-8");
	bindtextdomain($strDomain, BASE_PATH ."config/locale");
	bind_textdomain_codeset($strDomain, $SETS['data']['encoding']);
	textdomain($strDomain);
}
//
// Include external function/class files
// =====================================
include("nag_class.php");
include("data_class.php");
include("config_class.php");
require_once(BASE_PATH .'libraries/pear/HTML/Template/IT.php');
if (isset($preFieldvars) && ($preFieldvars == 1)) {
  	require(BASE_PATH .'config/fieldvars.php');
}
//
// Add data to the session
// =======================
$_SESSION['SETS'] 					= $SETS;
$_SESSION['strLoginMessage'] 		= "";
$_SESSION['startsite'] 				= BASE_URL ."admin.php";
if (!isset($_SESSION['logged_in'])) $_SESSION['logged_in'] = 0;
if (isset($chkLogout) && ($chkLogout == "yes")) {
  	$_SESSION = array();
	$_SESSION['SETS'] 				= $SETS;
  	$_SESSION['logged_in'] 			= 0;
	$_SESSION['userid']    			= 0;
	$_SESSION['groupadm']  			= 0;
	$_SESSION['strLoginMessage'] 	= "";
}
if (isset($_GET['menu']) && (htmlspecialchars($_GET['menu'], ENT_QUOTES, 'utf-8') == "visible"))   $_SESSION['menu'] = "visible";
if (isset($_GET['menu']) && (htmlspecialchars($_GET['menu'], ENT_QUOTES, 'utf-8') == "invisible")) $_SESSION['menu'] = "invisible";
//
// Initialize classes
// ==================
$myVisClass    = new nagvisual;
$myDataClass   = new nagdata;
$myConfigClass = new nagconfig;
//
// Propagating the classes themselves
// ==================================
$myVisClass->myDBClass    	=& $myDBClass;
$myVisClass->myDataClass  	=& $myDataClass;
$myVisClass->myConfigClass  =& $myConfigClass;
$myDataClass->myDBClass   	=& $myDBClass;
$myDataClass->myVisClass  	=& $myVisClass;
$myDataClass->myConfigClass =& $myConfigClass;
$myConfigClass->myDBClass 	=& $myDBClass;
$myConfigClass->myVisClass  =& $myVisClass;
$myConfigClass->myDataClass =& $myDataClass;
//
// Version management
// ==================
if ($intError == 0) {
	$strSQL    		= "SELECT `value` FROM `tbl_settings` WHERE `name`='version'";
	$setDBVersion = $myDBClass->getFieldData($strSQL);
}
//
// Version check
//
if (version_compare($setFileVersion,$setDBVersion,'>') AND (file_exists(BASE_PATH."install") && is_readable(BASE_PATH."install"))) {
	header("Location: ". BASE_URL ."install/index.php");
}
//
// Login process
// ==============
if (isset($_SERVER['REMOTE_USER']) && ($_SERVER['REMOTE_USER'] != "") && ($_SESSION['logged_in'] == 0) && 
    ($chkLogout != "yes") && ($chkInsName == "")) {
  	$strSQL    = "SELECT * FROM `tbl_user` WHERE `username`='".$_SERVER['REMOTE_USER']."' AND `wsauth`='1' AND `active`='1'";
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataUser,$intDataCount);
  	if ($booReturn && ($intDataCount == 1)) {
		// Set session variables
		$_SESSION['username']  = $arrDataUser[0]['username'];
		$_SESSION['userid']    = $arrDataUser[0]['id'];
		$_SESSION['groupadm']  = $arrDataUser[0]['admin_enable'];
		$_SESSION['startsite'] = $SETS['path']['root'] ."admin.php";
		$_SESSION['timestamp'] = mktime();
		$_SESSION['logged_in'] = 1;
		$_SESSION['domain']    = 1;
		// Update last login time
		$strSQLUpdate = "UPDATE `tbl_user` SET `last_login`=NOW() WHERE `username`='".mysql_real_escape_string($chkInsName)."'";
		$booReturn    = $myDBClass->insertData($strSQLUpdate);
		$myDataClass->writeLog(translate('Webserver login successfull'));
		$_SESSION['strLoginMessage'] = ""; 
		// Redirect to start page
		header("Location: ".$_SESSION['SETS']['path']['protocol']."://".$_SERVER['HTTP_HOST'].$_SESSION['startsite']);
  	}
}
if (($_SESSION['logged_in'] == 0) && isset($chkInsName) && ($chkInsName != "") && ($intError == 0)) {
  	$strSQL    = "SELECT * FROM `tbl_user` WHERE `username`='".mysql_real_escape_string($chkInsName)."' 
				  AND `password`=MD5('$chkInsPasswd') AND `active`='1'";
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataUser,$intDataCount);
  	if ($booReturn == false) {
		$myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError,$strMessage);
    	$_SESSION['strLoginMessage'] = $strMessage;
  	} else if ($intDataCount == 1) {
		// Set session variables
		$_SESSION['username']  = $arrDataUser[0]['username'];
		$_SESSION['userid']    = $arrDataUser[0]['id'];
		$_SESSION['groupadm']  = $arrDataUser[0]['admin_enable'];
		$_SESSION['startsite'] = BASE_URL ."admin.php";
		$_SESSION['timestamp'] = mktime();
		$_SESSION['logged_in'] = 1;
		$_SESSION['domain']    = 1;
    	// Update last login time
    	$strSQLUpdate = "UPDATE `tbl_user` SET `last_login`=NOW() WHERE `username`='".mysql_real_escape_string($chkInsName)."'";
    	$booReturn    = $myDBClass->insertData($strSQLUpdate);
    	$myDataClass->writeLog(translate('Login successfull'));
    	$_SESSION['strLoginMessage'] = "";
		// Redirect to start page
		header("Location: ".$_SESSION['SETS']['path']['protocol']."://".$_SERVER['HTTP_HOST'].$_SESSION['startsite']);
  	} else {
    	$_SESSION['strLoginMessage'] = translate('Login failed!');
    	$myDataClass->writeLog(translate('Login failed!')." - Username: ".$chkInsName);
    	$preNoMain = 0;
  	}
} 
if (($_SESSION['logged_in'] == 0) && !isset($chkInsName)) {
	header("Location: ".$_SESSION['SETS']['path']['protocol']."://".$_SERVER['HTTP_HOST'].$_SESSION['SETS']['path']['root']."index.php");
}
//
// Review and update login
// =======================
if (($_SESSION['logged_in'] == 1) && ($intError == 0)) {
  	$strSQL  = "SELECT * FROM `tbl_user` WHERE `username`='".mysql_real_escape_string($_SESSION['username'])."'";
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataUser,$intDataCount);
  	if ($booReturn == false) {
		$myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError,$strMessage);
  	} else if ($intDataCount == 1) {
    	// Time expired?
    	if (mktime() - $_SESSION['timestamp'] > $_SESSION['SETS']['security']['logofftime']) {
      		// Force new login
      		$myDataClass->writeLog(translate('Session timeout reached - Seconds:')." ".(mktime() - $_SESSION['timestamp']." - User: ".$_SESSION['username']));
      		$_SESSION['logged_in'] = 0;
      		header("Location: ".$_SESSION['SETS']['path']['protocol']."://".$_SERVER['HTTP_HOST'].$_SESSION['SETS']['path']['root']."index.php");
    	} else {
      		// Check rights
      		if (isset($preAccess) && ($preAccess == 1) && ($intSub != 0)) {
        		$strKey    = $myDBClass->getFieldData("SELECT `access_group` FROM `tbl_submenu` WHERE `id`=$intSub");
        		$intResult = $myVisClass->checkAccGroup($_SESSION['userid'],$strKey);
        		// If no rights - redirect to index page
        		if ($intResult != 0) {
          			$myDataClass->writeLog(translate('Restricted site accessed:')." ".filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
					header("Location: ".$_SESSION['SETS']['path']['protocol']."://".$_SERVER['HTTP_HOST'].$_SESSION['SETS']['path']['root']."index.php");
        		}
      		}
      		// Update login time
      		$_SESSION['timestamp'] = mktime();
	  		if (isset($preContent) && ($preContent == "index.tpl.htm")) {
		  		header("Location: ".$_SESSION['SETS']['path']['protocol']."://".$_SERVER['HTTP_HOST'].$_SESSION['startsite']);
	  		}
    	}
  	} else {
    	// Force new login
    	$myDataClass->writeLog(translate('User not found in database'));
		$_SESSION['logged_in'] = 0;
    	header("Location: ".$_SESSION['SETS']['path']['protocol']."://".$_SERVER['HTTP_HOST'].$_SESSION['SETS']['path']['root']."index.php");
  	}
}
//
// Insert main template
// ====================
if (isset($preContent) && ($preContent != "") && (!isset($preNoMain) || ($preNoMain != 1))) {
	$arrTplOptions = array('use_preg' => false);
	$maintp = new HTML_Template_IT(BASE_PATH ."templates/");
	$maintp->loadTemplatefile("main.tpl.htm", true, true);
	$maintp->setOptions($arrTplOptions);
	$maintp->setVariable("META_DESCRIPTION","NagiosQL System Monitoring Administration Tool");
	$maintp->setVariable("AUTHOR","NagiosQL Team");
	$maintp->setVariable("LANGUAGE","de");
	$maintp->setVariable("PUBLISHER","www.nagiosql.org");
	if ($_SESSION['logged_in'] == 1) {
		$maintp->setVariable("ADMIN","<a href=\"". $_SESSION['SETS']['path']['root'] ."admin.php\" class=\"top-link\">".translate('Administration')."</a>");
		//$maintp->setVariable("PLUGINS","<a href=\"".BASE_URL."/plugin.php\" class=\"top-link\">".translate('Plugins')."</a>");
	}
	$maintp->setVariable("BASE_PATH",$_SESSION['SETS']['path']['root']);
	$maintp->setVariable("ROBOTS","noindex,nofollow");
	$maintp->setVariable("PAGETITLE","NagiosQL - Version ".$setDBVersion);
	$maintp->setVariable("IMAGEDIR",$_SESSION['SETS']['path']['root'] ."images/");
	if (isset($intMain) && (isset($intMenu) && ($intMenu != 1)) && ($intError == 0)) $maintp->setVariable("POSITION",$myVisClass->getPosition($intMain,$intSub,translate('Admin')));
	$maintp->parse("header");
	$tplHeaderVar = $maintp->get("header");
	//
	// Read domain list
	// ================
  	if (($_SESSION['logged_in'] == 1) && ($intError == 0)) {
    	$intDomain = isset($_POST['selDomain']) ? $_POST['selDomain'] : -1;
    	if ($intDomain != -1) {
			$_SESSION['domain'] 		= $intDomain;
			$myVisClass->intDomainId 	= $intDomain;
			$myDataClass->intDomainId 	= $intDomain;
			$myConfigClass->intDomainId = $intDomain;
		}
    	$strSQL    = "SELECT * FROM `tbl_domain` WHERE `active` <> '0' ORDER BY `domain`";
    	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataDomain,$intDataCount);
    	if ($booReturn == false) {
			$myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError,$strMessage);
    	} else {
      		$intDomain = 0;
      		foreach($arrDataDomain AS $elem) {
				// Check acces rights
				if ($myVisClass->checkAccGroup($_SESSION['userid'],$elem['access_group']) == 0) {
					$maintp->setVariable("DOMAIN_VALUE",$elem['id']);
					$maintp->setVariable("DOMAIN_TEXT",$elem['domain']);
					if (isset($_SESSION['domain']) && ($_SESSION['domain'] == $elem['id'])) {
						$maintp->setVariable("DOMAIN_SELECTED","selected");
						$intDomain = $elem['id'];
					}
					if ($intDomain == -1) $intDomain = $elem['id'];
					$maintp->parse("domainsel");
				}
			}
			if ($intDataCount > 0) {
				$maintp->setVariable("DOMAIN_INFO",translate("Domain").":");
				$maintp->parse("dselect");
				$tplHeaderVar .= $maintp->get("dselect");
				$_SESSION['domain'] = $intDomain;
			}
		}
	}
	//
	// Show login information
	// ======================
  	if ($_SESSION['logged_in'] == 1) {
    	$maintp->setVariable("LOGIN_INFO",translate('Logged in:')." ".$_SESSION['username']);
    	$maintp->setVariable("LOGOUT_INFO","<a href=\"".$_SESSION['SETS']['path']['root']."index.php?logout=yes\">".translate('Logout')."</a>");
  	} else {
    	$maintp->setVariable("LOGOUT_INFO","&nbsp;");
  	}
  	$maintp->parse("header2");
  	$tplHeaderVar .= $maintp->get("header2");
  	if (!isset($preShowHeader) || $preShowHeader == 1) {
    	echo $tplHeaderVar;
  	}
}
//
// Insert content and master template
// ======================================
if (isset($preContent) && ($preContent != "")) {
	$arrTplOptions = array('use_preg' => false);
	if (!file_exists(BASE_PATH ."templates/".$preContent) || !is_readable(BASE_PATH ."templates/".$preContent)) {
		echo "<span style=\"color:#F00\">".translate('Warning - template file not found or not readable, please check your file permissions! - File: ');
		echo str_replace("//","/",BASE_PATH ."templates/".$preContent)."</span><br>";
		exit;
	}
	$conttp = new HTML_Template_IT(BASE_PATH ."templates/");
	$conttp->loadTemplatefile($preContent, true, true);
	$conttp->setOptions($arrTplOptions);
	$strRootPath = $_SESSION['SETS']['path']['root'];
	if (substr($strRootPath,-1) != "/") {
		$conttp->setVariable("BASE_PATH",$strRootPath);
		$conttp->setVariable("IMAGE_PATH",$strRootPath."images/");
	} else {
		$conttp->setVariable("BASE_PATH",$strRootPath);
		$conttp->setVariable("IMAGE_PATH",$strRootPath."images/");
	}
	$mastertp = new HTML_Template_IT(BASE_PATH ."templates/");
	$mastertp->loadTemplatefile("admin/admin_master.tpl.htm", true, true);
	$mastertp->setOptions($arrTplOptions);
} elseif (isset($pluginTemplate) && ($pluginTemplate != "")) {
//
// Insert Plugin Template
// ======================
	$arrTplOptions = array('use_preg' => false);
	$conttp = new HTML_Template_IT(BASE_PATH ."plugins/".$pluginType."/".$pluginName."/templates/default/");
	$conttp->loadTemplatefile($pluginTemplate, true, true);
	$conttp->setOptions($arrTplOptions);
	$strRootPath = $_SESSION['SETS']['path']['root'];
	if (substr($strRootPath,-1) != "/") {
		$conttp->setVariable("BASE_PATH",$strRootPath."/plugins/".$pluginType."/".$pluginName."/");
		$conttp->setVariable("IMAGE_PATH",$strRootPath."/plugins/".$pluginType."/".$pluginName."/images/");
	} else {
		$conttp->setVariable("BASE_PATH",$strRootPath."/plugins/".$pluginType."/".$pluginName."/");
		$conttp->setVariable("IMAGE_PATH",$strRootPath."/plugins/".$pluginType."/".$pluginName."/images/");
	}
	$mastertp = new HTML_Template_IT(BASE_PATH ."templates/");
	$mastertp->loadTemplatefile("admin/admin_master.tpl.htm", true, true);
	$mastertp->setOptions($arrTplOptions);
}
//
// Process standard get/post parameters
// ====================================
$chkModus     		= isset($_GET['modus'])     			? htmlspecialchars($_GET['modus'], ENT_QUOTES, 'utf-8')	               : "display";
$chkModus     		= isset($_POST['modus'])    			? htmlspecialchars($_POST['modus'], ENT_QUOTES, 'utf-8')               : "display";
$chkLimit     		= isset($_POST['hidLimit'])   		? htmlspecialchars($_POST['hidLimit'], ENT_QUOTES, 'utf-8')            : 0;
$chkHidModify   	= isset($_POST['hidModify'])  		? htmlspecialchars($_POST['hidModify'], ENT_QUOTES, 'utf-8')		       : "";
$chkSelModify 		= isset($_POST['selModify'])  		? htmlspecialchars($_POST['selModify'], ENT_QUOTES, 'utf-8') 		       : "";
$chkSelTargetDomain	= isset($_POST['selTargetDomain'])	? htmlspecialchars($_POST['selTargetDomain'], ENT_QUOTES, 'utf-8') : 0;
$chkListId      	= isset($_POST['hidListId'])  		? htmlspecialchars($_POST['hidListId'], ENT_QUOTES, 'utf-8')           : 0;
$chkDataId    		= isset($_POST['hidId'])    			? htmlspecialchars($_POST['hidId'], ENT_QUOTES, 'utf-8')               : 0;
$chkActive    		= isset($_POST['chbActive'])  		? htmlspecialchars($_POST['chbActive'], ENT_QUOTES, 'utf-8')           : 0;
$hidActive    		= isset($_POST['hidActive'])  		? htmlspecialchars($_POST['hidActive'], ENT_QUOTES, 'utf-8')           : 0;
//
// Setting some variables
// ======================
if ($chkModus == "add")       				$chkSelModify 				  = "";
if ($chkHidModify != "")      				$chkSelModify 				  = $chkHidModify;
if (isset($_GET['limit']))    				$chkLimit 					  	= htmlspecialchars($_GET['limit'], ENT_QUOTES, 'utf-8');
if (isset($_SESSION['domain'])) 			$chkDomainId 				  	= $_SESSION['domain'];
if (isset($_SESSION['groupadm'])) 			$chkGroupAdm 			  	= $_SESSION['groupadm'];
if (isset($_SESSION['strLoginMessage'])) 	$_SESSION['strLoginMessage'] .= $strMessage;
$myConfigClass->getConfigData("version",$intVersion);
$myConfigClass->getConfigData("enable_common",$setEnableCommon);
if ($setEnableCommon != 0) {
	$strDomainWhere = " (`config_id`=$chkDomainId OR `config_id`=0) ";	
} else {
	$strDomainWhere = " (`config_id`=$chkDomainId) ";
}
//
// Set class variables
// ===================
if (isset($preContent) && ($preContent != "")) {
	$myVisClass->myContentTpl 	= $conttp;
	$myVisClass->dataId 		= $chkListId;
}
?>