<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project   : NagiosQL
// Component : Settings configuration
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
$intMain    = 7;
$intSub     = 29;
$intMenu    = 2;
$preContent = "admin/settings.tpl.htm";
$strMessage = "";
$intError   = 0;
//
// Include preprocessing file
// ==========================
$preAccess    = 1;
$preFieldvars = 1;
require("../functions/prepend_adm.php");
//
// Process post parameters
// =======================
$selProtocol    = isset($_POST['selProtocol'])    ? $_POST['selProtocol']                           : $SETS['path']['protocol'];
$txtTempdir     = isset($_POST['txtTempdir'])     ? htmlspecialchars($_POST['txtTempdir'], ENT_QUOTES, 'utf-8')          : $SETS['path']['tempdir'];
$selLanguage    = isset($_POST['selLanguage'])    ? $_POST['selLanguage']                           : $SETS['data']['locale'];
$txtEncoding    = isset($_POST['txtEncoding'])    ? htmlspecialchars($_POST['txtEncoding'], ENT_QUOTES, 'utf-8')         : $SETS['data']['encoding'];
$txtDBserver    = isset($_POST['txtDBserver'])    ? htmlspecialchars($_POST['txtDBserver'], ENT_QUOTES, 'utf-8')         : $SETS['db']['server'];
$txtDBport      = isset($_POST['txtDBport'])      ? htmlspecialchars($_POST['txtDBport'], ENT_QUOTES, 'utf-8')           : $SETS['db']['port'];
$txtDBname      = isset($_POST['txtDBname'])      ? htmlspecialchars($_POST['txtDBname'], ENT_QUOTES, 'utf-8')           : $SETS['db']['database'];
$txtDBuser      = isset($_POST['txtDBuser'])      ? $_POST['txtDBuser']										          : $SETS['db']['username'];
$txtDBpass      = isset($_POST['txtDBpass'])      ? $_POST['txtDBpass']                             : $SETS['db']['password'];
$txtLogoff      = isset($_POST['txtLogoff'])      ? $_POST['txtLogoff']+0         					: $SETS['security']['logofftime'];
$selWSAuth      = isset($_POST['selWSAuth'])      ? $_POST['selWSAuth']                             : $SETS['security']['wsauth'];
$txtLines       = isset($_POST['txtLines'])       ? $_POST['txtLines']+0          					: $SETS['common']['pagelines'];
$selSeldisable  = isset($_POST['selSeldisable'])  ? $_POST['selSeldisable']                         : $SETS['common']['seldisable'];
$selTplCheck    = isset($_POST['selTplCheck'])    ? $_POST['selTplCheck']                           : $SETS['common']['tplcheck'];
$selUpdCheck    = isset($_POST['selUpdCheck'])    ? $_POST['selUpdCheck']                           : $SETS['common']['updcheck'];
$chkProxy       = isset($_POST['chkProxy'])       ? $_POST['chkProxy']+0                            : $SETS['network']['Proxy'];
$txtProxyServer = isset($_POST['tfProxyServer'])  ? $_POST['tfProxyServer']                         : $SETS['network']['ProxyServer'];
$txtProxyUser   = isset($_POST['tfProxyUser'])    ? $_POST['tfProxyUser']           				: $SETS['network']['ProxyUser'];
$txtProxyPasswd = isset($_POST['tfProxyPassword'])? $_POST['tfProxyPassword']                       : $SETS['network']['ProxyPasswd'];
//
// Quote special characters
// ==========================
if (get_magic_quotes_gpc() == 0) {
	$txtTempdir  		= addslashes($txtTempdir);
	$txtEncoding  	    = addslashes($txtEncoding);
	$txtDBserver  	    = addslashes($txtDBserver);
	$txtDBport 			= addslashes($txtDBport);
	$txtDBname 			= addslashes($txtDBname);
	$txtDBuser 			= addslashes($txtDBuser);
	$txtDBpass 			= addslashes($txtDBpass);
    $txtProxyServer     = addslashes($txtProxyServer);
    $txtProxyUser		= addslashes($txtProxyUser);
    $txtProxyPasswd     = addslashes($txtProxyPasswd);
}
//
// Save changes
// ============
if ((isset($_POST)) AND (isset($_POST['selLanguage']))) {
	// Write global settings to database
	$strSQL = "SET @previous_value := NULL";
	$booReturn = $myDBClass->insertData($strSQL);
	$strSQL  = "INSERT INTO `tbl_settings` (`category`,`name`,`value`) VALUES";
	$strSQL .= "('path','protocol','".$selProtocol."'),";
	$strSQL .= "('path','tempdir','".str_replace("\\", "\\\\", $txtTempdir)."'),";
	$strSQL .= "('data','locale','".$selLanguage."'),";
	$strSQL .= "('data','encoding','".$txtEncoding."'),";
	$strSQL .= "('security','logofftime','".$txtLogoff."'),";
	$strSQL .= "('security','wsauth','".$selWSAuth."'),";
	// Check Proxy via curl
	if (!function_exists('curl_init')) {
		$strMessage .= "<br><font color='red'>".translate('Curl module not loaded, Proxy will be deactivated!')."</font><br>";
		$strSQL .= "('network','Proxy','0'),";
		$chkProxy=0;
	} else {
		$strSQL .= "('network','Proxy','".mysql_real_escape_string($chkProxy)."'),";
	}
	$strSQL .= "('network','ProxyServer','".mysql_real_escape_string($txtProxyServer)."'),";
	$strSQL .= "('network','ProxyUser','".mysql_real_escape_string($txtProxyUser)."'),";
	$strSQL .= "('network','ProxyPasswd','".mysql_real_escape_string($txtProxyPasswd)."'),";
	$strSQL .= "('common','pagelines','".$txtLines."'),";
	$strSQL .= "('common','seldisable','".$selSeldisable."'),";
	$strSQL .= "('common','tplcheck','".$selTplCheck."'),";
	$strSQL .= "('common','updcheck','".$selUpdCheck."') ";
	$strSQL .= "ON DUPLICATE KEY UPDATE value = IF((@previous_value := value) <> NULL IS NULL, VALUES(value), NULL);";
	$booReturn = $myDBClass->insertData($strSQL);
	if ( $booReturn == false ) $writingmsg = translate("An error occured while writing settings to database")."<br>".$myDBClass->strDBError;
	$strSQL = "SELECT @previous_note";
	$booReturn = $myDBClass->insertData($strSQL);
	// Write db settings to file
	$filSet = fopen($_SESSION['SETS']['path']['physical']."/config/settings.php","w");
	if ($filSet) {
		fwrite($filSet,"<?php\n");
		fwrite($filSet,"exit;\n");
		fwrite($filSet,"?>\n");
		fwrite($filSet,";///////////////////////////////////////////////////////////////////////////////\n");
		fwrite($filSet,";\n");
		fwrite($filSet,"; NagiosQL\n");
		fwrite($filSet,";\n");
		fwrite($filSet,";///////////////////////////////////////////////////////////////////////////////\n");
		fwrite($filSet,";\n");
		fwrite($filSet,"; Project  : NagiosQL\n");
		fwrite($filSet,"; Component: Configuration settings\n");
		fwrite($filSet,"; Website  : http://www.nagiosql.org\n");
		fwrite($filSet,"; Date     : ".date("F j, Y, g:i a")."\n");
		fwrite($filSet,"; Version  : ".$setFileVersion."\n");
		fwrite($filSet,"; \$LastChangedRevision: 1058 $\n");
		fwrite($filSet,";\n");
		fwrite($filSet,";///////////////////////////////////////////////////////////////////////////////\n");
		fwrite($filSet,"[db]\n");
		fwrite($filSet,"server       = ".$txtDBserver."\n");
		fwrite($filSet,"port         = ".$txtDBport."\n");    
		fwrite($filSet,"database     = ".$txtDBname."\n");
		fwrite($filSet,"username     = ".$txtDBuser."\n");
		fwrite($filSet,"password     = ".$txtDBpass."\n");
		fclose($filSet);
		// Activate new language settings
		$arrLocale = explode(".",$selLanguage);
		$strDomain = $arrLocale[0];
		$loc = setlocale(LC_ALL, $selLanguage, $selLanguage.".utf-8", $selLanguage.".utf-8", $selLanguage.".utf8", "en_GB", "en_GB.utf-8", "en_GB.utf8");
		if (!isset($loc)) {
			$strMessage .= translate("Error in setting the correct locale, please report this error with the associated output of 'locale -a'");
		}
		putenv("LC_ALL=".$selLanguage.".utf-8");
		putenv("LANG=".$selLanguage.".utf-8");
		bindtextdomain($selLanguage, $_SESSION['SETS']['path']['physical']."/config/locale");
		bind_textdomain_codeset($selLanguage, $txtEncoding);
		textdomain($selLanguage);
			$strMessage .= translate("Settings were changed");
  	} else {
    	$strMessage .= translate("An error occured while writing settings.php, please check permissions!");
  	}
}
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
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
$conttp->setVariable("TEMPDIR_VALUE",$txtTempdir);
$conttp->setVariable("PROTOCOL_NAME",translate('Server protocol'));
$conttp->parse("ProtocolSelection");
$conttp->setVariable("PROTOCOL_VALUE","http");
if ($selProtocol == "http") {
  	$conttp->setVariable("PROTOCOL_SELECTED","selected");
}
$conttp->parse("ProtocolSelection");
$conttp->setVariable("PROTOCOL_VALUE","https");
if ($selProtocol == "https") {
  	$conttp->setVariable("PROTOCOL_SELECTED","selected");
}
$conttp->parse("ProtocolSelection");
//
// Data settings
// =============
$conttp->setVariable("DATA",translate('Language'));
$conttp->setVariable("LOCALE",translate('Language'));
$conttp->parse("LanguageSelection");
$arrAvailableLanguages=getLanguageData();
foreach(getLanguageData() as $key=>$val) {
  	$conttp->setVariable("LANGUAGE_VALUE",$key);
  	if($key == $selLanguage) {
    	$conttp->setVariable("LANGUAGE_SELECTED","selected");
  	}
  	$conttp->setVariable("LANGUAGE_NAME",getLanguageNameFromCode($key,false));
  	$conttp->parse("LanguageSelection");
}
$conttp->setVariable("ENCODING_NAME",translate('Encoding'));
$conttp->setVariable("ENCODING_VALUE",$txtEncoding);
//
// Database settings
// =================
$conttp->setVariable("DB",translate('Database'));
$conttp->setVariable("SERVER_NAME",translate('MySQL Server'));
$conttp->setVariable("SERVER_VALUE",$txtDBserver);
$conttp->setVariable("SERVER_PORT",translate('MySQL Server Port'));
$conttp->setVariable("PORT_VALUE",$txtDBport);
$conttp->setVariable("DATABASE_NAME",translate('Database name'));
$conttp->setVariable("DATABASE_VALUE",$txtDBname);
$conttp->setVariable("USERNAME_NAME",translate('Database user'));
$conttp->setVariable("USERNAME_VALUE",htmlspecialchars($txtDBuser, ENT_QUOTES, 'utf-8'));
$conttp->setVariable("PASSWORD_NAME",translate('Database password'));
$conttp->setVariable("PASSWORD_VALUE",htmlspecialchars($txtDBpass, ENT_QUOTES, 'utf-8'));
//
// Security settings
// =================
$conttp->setVariable("SECURITY",translate('Security'));
$conttp->setVariable("LOGOFFTIME_NAME",translate('Session auto logoff time'));
$conttp->setVariable("LOGOFFTIME_VALUE",$txtLogoff);
$conttp->setVariable("WSAUTH_NAME",translate('Authentication type'));
$conttp->setVariable("WSAUTH_DESCRIPTION","NagiosQL");
$conttp->setVariable("WSAUTH_VALUE","0");
if ($selWSAuth == 0) {
  	$conttp->setVariable("WSAUTH_SELECTED","selected");
}
$conttp->parse("WSAuthSelection");
$conttp->setVariable("WSAUTH_DESCRIPTION","Apache");
$conttp->setVariable("WSAUTH_VALUE","1");
if ($selWSAuth == 1) {
  	$conttp->setVariable("WSAUTH_SELECTED","selected");
}
$conttp->parse("WSAuthSelection");
//
// Common settings
// ===============
$conttp->setVariable("COMMON",translate('Common'));
$conttp->setVariable("PAGELINES_NAME",translate('Data lines per page'));
$conttp->setVariable("PAGELINES_VALUE",$txtLines);
$conttp->parse("SeldisableSelection");
$conttp->setVariable("SELDISABLE_NAME",translate('Selection method'));
$conttp->setVariable("SELDISABLE_DESCRIPTION","NagiosQL2");
$conttp->setVariable("SELDISABLE_VALUE","0");
if ($selSeldisable == 0) {
  	$conttp->setVariable("SELDISABLE_SELECTED","selected");
}
$conttp->parse("SeldisableSelection");
$conttp->setVariable("SELDISABLE_DESCRIPTION","NagiosQL3");
$conttp->setVariable("SELDISABLE_VALUE","1");
if ($selSeldisable == 1) {
  	$conttp->setVariable("SELDISABLE_SELECTED","selected");
}
$conttp->parse("SeldisableSelection");
// Template Check
$conttp->setVariable("TEMPLATE_CHECK", translate('Template warn message'));
$conttp->setVariable("TPL_CHECK_DESCRIPTION",translate('Enable'));
$conttp->setVariable("TPL_CHECK_VALUE","1");
if ($selTplCheck == 1) {
  	$conttp->setVariable("TPL_CHECK_CHECKED","checked");
}
$conttp->parse("template_check");
$conttp->setVariable("TPL_CHECK_DESCRIPTION",translate('Disable'));
$conttp->setVariable("TPL_CHECK_VALUE","0");
if ($selTplCheck == 0) {
  	$conttp->setVariable("TPL_CHECK_CHECKED","checked");
}
$conttp->parse("template_check");
// Update Check
$conttp->setVariable("CLASS_NAME_1","elementHide");
$conttp->setVariable("CLASS_NAME_2","elementHide");
$conttp->setVariable("UPDATE_CHECK", translate('Automatically check for online updates (internet access required!)'));
$conttp->setVariable("UPD_CHECK_DESCRIPTION",translate('Enable'));
$conttp->setVariable("UPD_CHECK_VALUE","1");
$conttp->setVariable("UPD_CHECK_CHECKED","checked");
$conttp->parse("update_check");
$conttp->setVariable("UPD_CHECK_DESCRIPTION",translate('Disable'));
$conttp->setVariable("UPD_CHECK_VALUE","0");
if ($selUpdCheck == 0) {
  	$conttp->setVariable("UPD_CHECK_CHECKED","checked");
} else {
	$conttp->setVariable("CLASS_NAME_1","elementShow");	
}
$conttp->parse("update_check");
// Setup Proxy
$conttp->setVariable("UPD_PROXY_CHECK", translate('Proxyserver'));
$conttp->setVariable("UPD_PROXY_DESCRIPTION", translate('Enable'));
$conttp->setVariable("UPD_PROXY_CHECK_VALUE","1");
if ($chkProxy == 1) {
	$conttp->setVariable("UPD_PROXY_CHECKED","checked");
	if ($selUpdCheck == 1) $conttp->setVariable("CLASS_NAME_2","elementShow");
}
$conttp->setVariable("UPD_PROXY_CHECKED","checked");
$conttp->setVariable("UPD_PROXY_SERVER", translate('Proxy Address'));
$conttp->setVariable("UPD_PROXY_SERVER_VALUE", htmlspecialchars($txtProxyServer, ENT_QUOTES, 'utf-8'));
$conttp->setVariable("UPD_PROXY_USERNAME", translate('Proxy Username (optional)'));
$conttp->setVariable("UPD_PROXY_USERNAME_VALUE", htmlspecialchars($txtProxyUser, ENT_QUOTES, 'utf-8'));
$conttp->setVariable("UPD_PROXY_PASSWORD", translate('Proxy Password (optional)'));
$conttp->setVariable("UPD_PROXY_PASSWORD_VALUE", htmlspecialchars($txtProxyPasswd, ENT_QUOTES, 'utf-8'));
$conttp->parse("update_proxy_check");
$conttp->setVariable("UPD_PROXY_DESCRIPTION", translate('Disable'));
$conttp->setVariable("UPD_PROXY_CHECK_VALUE","0");
if ($chkProxy == 0) {
	$conttp->setVariable("UPD_PROXY_CHECKED","checked");
}
$conttp->parse("update_proxy_check");

// Requirements of form
$conttp->setVariable("LANG_SAVE", translate('Save'));
$conttp->setVariable("LANG_ABORT", translate('Abort'));
$conttp->setVariable("LANG_REQUIRED", translate('required'));
if (isset($strMessage)) {
 	$conttp->setVariable("WRITING_MSG",$strMessage);
}
$conttp->parse("settingssite");
$conttp->show("settingssite");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>