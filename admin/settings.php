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
// Component : Settings configuration
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2017-06-22 09:29:35 +0200 (Thu, 22 Jun 2017) $
// Author    : $LastChangedBy: martin $
// Version   : 3.3.0
// Revision  : $LastChangedRevision: 2 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$prePageId			= 38;
$preContent   		= "admin/settings.tpl.htm";
$preAccess    		= 1;
$preFieldvars 		= 1;
//
// Include preprocessing files
// ===========================
require("../functions/prepend_adm.php");
require("../functions/prepend_content.php");
//
// Process initial values
// ======================
if (!isset($_POST['tfValue1']))  $chkTfValue1  = $SETS['path']['tempdir'];
if (!isset($_POST['tfValue2']))  $chkTfValue2  = $SETS['data']['encoding'];
if (!isset($_POST['tfValue3']))  $chkTfValue3  = $SETS['db']['server'];
if (!isset($_POST['tfValue4']))  $chkTfValue4  = $SETS['db']['port'];
if (!isset($_POST['tfValue5']))  $chkTfValue5  = $SETS['db']['database'];
if (!isset($_POST['tfValue6']))  $chkTfValue6  = $SETS['db']['username'];
if (!isset($_POST['tfValue7']))  $chkTfValue7  = $SETS['db']['password'];
if (!isset($_POST['tfValue8']))  $chkTfValue8  = $SETS['security']['logofftime'];
if (!isset($_POST['tfValue9']))  $chkTfValue9  = $SETS['common']['pagelines'];
if (!isset($_POST['tfValue10'])) $chkTfValue10 = $SETS['network']['proxyserver'];
if (!isset($_POST['tfValue11'])) $chkTfValue11 = $SETS['network']['proxyuser'];
if (!isset($_POST['tfValue12'])) $chkTfValue12 = $SETS['network']['proxypasswd'];
if (!isset($_POST['selValue3'])) $chkSelValue3 = $SETS['security']['wsauth'];
if (!isset($_POST['selValue4'])) $chkSelValue4 = $SETS['common']['seldisable'];
if (!isset($_POST['radValue1'])) $chkRadValue1 = $SETS['common']['tplcheck'];
if (!isset($_POST['radValue2'])) $chkRadValue2 = $SETS['common']['updcheck'];
if (!isset($_POST['radValue3'])) $chkRadValue3 = $SETS['network']['proxy'];	
//
// Save changes
// ============
if (isset($_POST) && isset($_POST['selValue1'])) {
	//
	// Write settings to database
	// ==========================
	if ($chkSelValue1 == 2) {$strProtocol = "https";} else {$strProtocol = "http";}
	$strLocale = $myDBClass->getFieldData("SELECT `locale` FROM `tbl_language` WHERE `id`='".$chkSelValue2."'");
	if ($strLocale == "") $strLocale = "en_GB";
	// Check Proxy via curl
	if (!function_exists('curl_init')) {
		$myVisClass->processMessage(translate('Curl module not loaded, Proxy will be deactivated!'),$strErrorMessage);
		$chkRadValue3 = 0;
	}
	// Check base paths
	$strBaseURL  	= str_replace("admin/settings.php","",$_SERVER["PHP_SELF"]);
	$strBasePath	= substr(realpath('.'),0,-5);
	$arrSQL = "";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$strProtocol."'  WHERE `category` = 'path' 		AND `name`='protocol'";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$chkTfValue1."'  WHERE `category` = 'path' 		AND `name`='tempdir'";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$strBaseURL."'   WHERE `category` = 'path'  	AND `name`='base_url'";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$strBasePath."'  WHERE `category` = 'path'  	AND `name`='base_path'";	
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$strLocale."'    WHERE `category` = 'data' 		AND `name`='locale'";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$chkTfValue2."'  WHERE `category` = 'data' 		AND `name`='encoding'";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$chkTfValue8."'  WHERE `category` = 'security' 	AND `name`='logofftime'";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$chkSelValue3."' WHERE `category` = 'security' 	AND `name`='wsauth'";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$chkTfValue9."'  WHERE `category` = 'common' 	AND `name`='pagelines'";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$chkSelValue4."' WHERE `category` = 'common' 	AND `name`='seldisable'";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$chkRadValue1."' WHERE `category` = 'common' 	AND `name`='tplcheck'";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$chkRadValue2."' WHERE `category` = 'common' 	AND `name`='updcheck'";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$chkRadValue3."' WHERE `category` = 'network' 	AND `name`='proxy'";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$chkTfValue10."' WHERE `category` = 'network' 	AND `name`='proxyserver'";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$chkTfValue11."' WHERE `category` = 'network' 	AND `name`='proxyuser'";
	$arrSQL[] = "UPDATE `tbl_settings` SET `value` = '".$chkTfValue12."' WHERE `category` = 'network'  	AND `name`='proxypasswd'";

	foreach ($arrSQL AS $elem) {
		$booReturn = $myDBClass->insertData($elem);	
		if ($booReturn == false) {
			$myVisClass->processMessage(translate('An error occured while writing settings to database:'),$strErrorMessage);
			$myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
		}
	}
	// Write db settings to file
	if (is_writable($strBasePath."config/settings.php")) {
		$filSettings = fopen($strBasePath."config/settings.php","w");
		if ($filSettings) {
			fwrite($filSettings,"<?php\n");
			fwrite($filSettings,"exit;\n");
			fwrite($filSettings,"?>\n");
			fwrite($filSettings,";///////////////////////////////////////////////////////////////////////////////\n");
			fwrite($filSettings,";\n");
			fwrite($filSettings,"; NagiosQL\n");
			fwrite($filSettings,";\n");
			fwrite($filSettings,";///////////////////////////////////////////////////////////////////////////////\n");
			fwrite($filSettings,";\n");
			fwrite($filSettings,"; Project  : NagiosQL\n");
			fwrite($filSettings,"; Component: Database Configuration\n");
			fwrite($filSettings,"; Website  : http://www.nagiosql.org\n");
			fwrite($filSettings,"; Date     : ".date("F j, Y, g:i a")."\n");
			fwrite($filSettings,"; Version  : ".$setFileVersion."\n");
			fwrite($filSettings,";\n");
			fwrite($filSettings,";///////////////////////////////////////////////////////////////////////////////\n");
			fwrite($filSettings,"[db]\n");
			fwrite($filSettings,"server       = ".$chkTfValue3."\n");
			fwrite($filSettings,"port         = ".$chkTfValue4."\n");
			fwrite($filSettings,"database     = ".$chkTfValue5."\n");
			fwrite($filSettings,"username     = ".$chkTfValue6."\n");
			fwrite($filSettings,"password     = ".$chkTfValue7."\n");
			fwrite($filSettings,"[path]\n");
			fwrite($filSettings,"base_url     = ".$strBaseURL."\n");
			fwrite($filSettings,"base_path    = ".$strBasePath."\n");
			fclose($filSettings);	
			// Activate new language settings
			$arrLocale = explode(".",$strLocale);
			$strDomain = $arrLocale[0];
			$loc = setlocale(LC_ALL, $strLocale, $strLocale.".utf-8", $strLocale.".utf-8", $strLocale.".utf8", "en_GB", "en_GB.utf-8", "en_GB.utf8");
			if (!isset($loc)) {
				$myVisClass->processMessage(translate("Error in setting the correct locale, please report this error with the associated output of 'locale -a'"),$strErrorMessage);
			}
			putenv("LC_ALL=".$strLocale.".utf-8");
			putenv("LANG=".$strLocale.".utf-8");
			bindtextdomain($strLocale, $strBasePath."config/locale");
			bind_textdomain_codeset($strLocale, $chkTfValue2);
			textdomain($strLocale);
			$myVisClass->processMessage(translate("Settings were changed"),$strInfoMessage);
		} else {
			$myVisClass->processMessage(translate("An error occured while writing settings.php, please check permissions!"),$strErrorMessage);
		}
	} else {
		$myVisClass->processMessage($strBasePath."config/settings.php ".translate("is not writeable, please check permissions!"),$strErrorMessage);
	}
}
//
// Start content
// =============
$conttp->setVariable("TITLE",translate('Configure Settings'));
$conttp->parse("header");
$conttp->show("header");
foreach($arrDescription AS $elem) {
  	$conttp->setVariable($elem['name'],$elem['string']);
}
$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
$conttp->setVariable("LANG_DESCRIPTION",translate('Change your current NagiosQL settings (e.g. Database user, Language).'));
//
// Path settings
// =============
$conttp->setVariable("PATH",translate('Path'));
$conttp->setVariable("TEMPDIR_NAME",translate('Temporary Directory'));
$conttp->setVariable("TEMPDIR_VALUE",htmlspecialchars($chkTfValue1, ENT_QUOTES, 'utf-8'));
$conttp->setVariable("PROTOCOL_NAME",translate('Server protocol'));
$conttp->setVariable(strtoupper($SETS['path']['protocol'])."_SELECTED","selected");
//
// Data settings
// =============
$conttp->setVariable("DATA",translate('Language'));
$conttp->setVariable("LOCALE",translate('Language'));
// Process language selection field
$strSQL 	= "SELECT * FROM `tbl_language` WHERE `active`='1' ORDER BY `id`";
$booReturn 	= $myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
if ($booReturn && ($intDataCount != 0)) {
	foreach($arrData AS $elem) {
		$conttp->setVariable("LANGUAGE_ID",$elem['id']);
		$conttp->setVariable("LANGUAGE_NAME",translate($elem['language']));
		if ($elem['locale'] == $SETS['data']['locale']) $conttp->setVariable("LANGUAGE_SELECTED","selected");
		$conttp->parse("language");
	}
} else {
	$myVisClass->processMessage(translate('Error while selecting data from database:'),$strErrorMessage);
	$myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
}
$conttp->setVariable("ENCODING_NAME",translate('Encoding'));
$conttp->setVariable("ENCODING_VALUE",htmlspecialchars($chkTfValue2, ENT_QUOTES, 'utf-8'));
//
// Database settings
// =================
$conttp->setVariable("DB",translate('Database'));
$conttp->setVariable("SERVER_NAME",translate('MySQL Server'));
$conttp->setVariable("SERVER_VALUE",htmlspecialchars($chkTfValue3, ENT_QUOTES, 'utf-8'));
$conttp->setVariable("SERVER_PORT",translate('MySQL Server Port'));
$conttp->setVariable("PORT_VALUE",htmlspecialchars($chkTfValue4, ENT_QUOTES, 'utf-8'));
$conttp->setVariable("DATABASE_NAME",translate('Database name'));
$conttp->setVariable("DATABASE_VALUE",htmlspecialchars($chkTfValue5, ENT_QUOTES, 'utf-8'));
$conttp->setVariable("USERNAME_NAME",translate('Database user'));
$conttp->setVariable("USERNAME_VALUE",htmlspecialchars($chkTfValue6, ENT_QUOTES, 'utf-8'));
$conttp->setVariable("PASSWORD_NAME",translate('Database password'));
$conttp->setVariable("PASSWORD_VALUE",htmlspecialchars($chkTfValue7, ENT_QUOTES, 'utf-8'));
//
// Security settings
// =================
$conttp->setVariable("SECURITY",translate('Security'));
$conttp->setVariable("LOGOFFTIME_NAME",translate('Session auto logoff time'));
$conttp->setVariable("LOGOFFTIME_VALUE",htmlspecialchars($chkTfValue8, ENT_QUOTES, 'utf-8'));
$conttp->setVariable("WSAUTH_NAME",translate('Authentication type'));
$conttp->setVariable("WSAUTH_".$chkSelValue3."_SELECTED","selected");
//
// Common settings
// ===============
$conttp->setVariable("COMMON",translate('Common'));
$conttp->setVariable("PAGELINES_NAME",translate('Data lines per page'));
$conttp->setVariable("PAGELINES_VALUE",htmlspecialchars($chkTfValue9, ENT_QUOTES, 'utf-8'));
$conttp->setVariable("SELDISABLE_NAME",translate('Selection method'));
$conttp->setVariable("SELDISABLE_".$chkSelValue4."_SELECTED","selected");
//
// Template Check
// ==============
$conttp->setVariable("TEMPLATE_CHECK", translate('Template warn message'));
$conttp->setVariable("LANG_ENABLE", translate('Enable'));
$conttp->setVariable("LANG_DISABLE", translate('Disable'));
$conttp->setVariable("TPL_CHECK_".$chkRadValue1."_CHECKED","checked");
//
// Online version check
// ====================
$conttp->setVariable("CLASS_NAME_1","elementHide");
$conttp->setVariable("CLASS_NAME_2","elementHide");
$conttp->setVariable("UPDATE_CHECK", translate('Online version check'));
$conttp->setVariable("UPD_CHECK_".$chkRadValue2."_CHECKED","checked");
if ($chkRadValue2 == 1) $conttp->setVariable("CLASS_NAME_1","elementShow");
//
// Online update proxy settings
// ============================
$conttp->setVariable("UPD_PROXY_CHECK", translate('Proxyserver'));
$conttp->setVariable("UPD_PROXY_".$chkRadValue3."_CHECKED","checked");
if (($chkRadValue3 == 1) && ($chkRadValue2 == 1)) $conttp->setVariable("CLASS_NAME_2","elementShow");
$conttp->setVariable("UPD_PROXY_SERVER", translate('Proxy Address'));
$conttp->setVariable("UPD_PROXY_SERVER_VALUE",htmlspecialchars($chkTfValue10, ENT_QUOTES, 'utf-8'));
$conttp->setVariable("UPD_PROXY_USERNAME", translate('Proxy Username (optional)'));
$conttp->setVariable("UPD_PROXY_USERNAME_VALUE",htmlspecialchars($chkTfValue11, ENT_QUOTES, 'utf-8'));
$conttp->setVariable("UPD_PROXY_PASSWORD", translate('Proxy Password (optional)'));
$conttp->setVariable("UPD_PROXY_PASSWORD_VALUE",htmlspecialchars($chkTfValue12, ENT_QUOTES, 'utf-8'));
//
// Requirements of form
// ====================
$conttp->setVariable("LANG_SAVE", translate('Save'));
$conttp->setVariable("LANG_ABORT", translate('Abort'));
$conttp->setVariable("LANG_REQUIRED", translate('required'));
$conttp->setVariable("ERRORMESSAGE",$strErrorMessage);
$conttp->setVariable("INFOMESSAGE",$strInfoMessage);
//
// Check access rights for adding new objects
// ==========================================
if ($myVisClass->checkAccGroup($prePageKey,'write') != 0) $conttp->setVariable("ADD_CONTROL","disabled=\"disabled\"");
$conttp->parse("settingssite");
$conttp->show("settingssite");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>