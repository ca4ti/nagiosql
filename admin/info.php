<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2012 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Admin information  dialog
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2012-02-21 14:10:41 +0100 (Tue, 21 Feb 2012) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1229 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Include preprocessing file
// ==========================
$preNoMain  = 1;
require("../functions/prepend_adm.php");
//
// Process get parameters
// ======================
$chkKey1        = isset($_GET['key1'])    ? htmlspecialchars($_GET['key1'], ENT_QUOTES, 'utf-8')       : "";
$chkKey2        = isset($_GET['key2'])    ? htmlspecialchars($_GET['key2'], ENT_QUOTES, 'utf-8')       : "";
$chkVersion     = isset($_GET['version']) ? htmlspecialchars($_GET['version'], ENT_QUOTES, 'utf-8')    : "";
//
// Get information data
// ===================================================
if ($chkKey1 == "admin" and isset($_SESSION['updInfo'])) {
	// Exception for version check at admin.php
  	$strContentDB = $_SESSION['updInfo'];
} elseif ($chkKey1 == "settings") {
	// Exception for settings page to have gettext translated text 
  	$arrTrans = array (
		"txtRootPath"   => translate("This is relative path of your NagiosQL Installation"),
		"txtBasePath"   => translate("This is the absolut path to your NagiosQL Installation"),
		"selProtocol"   => translate("If you need a secure connection, select HTTPS instead of HTTP"),
		"txtTempdir"    => translate("Please choose a temporary directory with write permissions. The default is the temp directory provided by your OS"),
		"selLanguage"   => translate("Please choose your application language"),
		"txtEncoding"   => translate("Encoding should be set to nothing else than utf-8. Any changes at your own risk"),
		"txtDBserver"   => translate("IP-Address or hostname of the database server<br>e.g. localhost"),
		"txtDBport"     => translate("MySQL Server Port, default is 3306"),
		"txtDBname"     => translate("Name of the NagiosQL database<br>e.g. db_nagiosql_v3"),
		"txtDBuser"     => translate("User with sufficient permission for the NagiosQL database<br>At least this user should have SELECT, INSERT, UPDATE, DELETE permissions"),
		"txtDBpass"     => translate("Password for the above mentioned user"),
		"txtLogoff"     => translate("After the defined amount of seconds the session will terminate for security reasons"),
		"selWSAuth"     => translate("Decide between authentication based on your Webserver<br>e.g. Apache configuration (config file or htaccess) or NagiosQL"),
		"txtLines"      => translate("How many entries per side should be visibile (e.g. services or hosts)"),
		"selSeldisable" => translate("Selection of multiple entries by using the new dialog or by holding CTRL + left click like in NagiosQL2"),
		"templatecheck" => translate("Enable or disable the warning if a required field contains no data in objects with templates"),
		"updatecheck" 	=> translate("Enable or disable the automatic online version check."),
		"chkUpdProxy" 	=> translate("If you require a Proxyserver to connect to the Internet (Port 80), please define one."),
		"txtProxyServer"=> translate("Address of your Proxyserver e.g. proxy.yourdomain.com:3128"),
		"txtProxyUser" 	=> translate("Username to connect through your proxy (optional)"),
		"txtProxyPasswd"=> translate("Password to connect through your proxy (optional)"),		
 	);
  	$strContentDB = $arrTrans[$chkKey2];
} else {
	// Get information from tbl_info
	$strSQL     	= "SELECT `infotext` FROM `tbl_info`
					   WHERE `key1` = '$chkKey1' AND `key2` = '$chkKey2' AND `version` = '$chkVersion' AND `language` = 'private'";
	$strContentDB 	= $myDBClass->getFieldData($strSQL);
	if ($strContentDB == "") {
		$strSQL     	= "SELECT `infotext` FROM `tbl_info`
						   WHERE `key1` = '$chkKey1' AND `key2` = '$chkKey2' AND `version` = '$chkVersion' AND `language` = 'default'";
		$strContentDB	= $myDBClass->getFieldData($strSQL);
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo translate("Information PopUp");?></title>
	<style>
		.infobody {
  			font-family:"Courier New", Courier, monospace;
  			font-size:12px;
		}
	</style>
</head>
<body class="infobody">
<?php
if ($strContentDB != "") {
  	echo $strContentDB;
} else {
	echo translate("No information available");
}
?>
</body>
</html>