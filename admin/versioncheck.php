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
// Component : Command line visualization
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-10-31 15:42:09 +0100 (Mo, 31. Okt 2011) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1118 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$preNoMain 	= 1;
$chkShow  	= isset($_GET['show']) ? $_GET['show']+0 : 0;
//
// Include preprocessing file
// ==========================
require("../functions/prepend_adm.php");
$strCommandLine = "&nbsp;";
$intCount		= 0;
//
// Get database values
// ===================
if ($chkShow == 1) {
	$versionfeed = "http://api.wizonet.ch/nagiosql/versioncheck.php?myversion=".urlencode($setFileVersion);
	$strError	 = "";
	if ((isset($SETS['network']['proxy']) && ($SETS['network']['proxy'] == '1')) && 
		(isset($SETS['network']['proxyserver']) && ($SETS['network']['proxyserver'] != ""))) {
		if ((isset($SETS['network']['proxyuser']) && ($SETS['network']['proxyuser'] != "")) && 
			(isset($SETS['network']['proxypasswd']) && ($SETS['network']['proxypasswd'] != ""))) {
			$strProxyAuth = base64_encode($SETS['network']['proxyuser'].":".$SETS['network']['proxypasswd']);	
			$aContext = array(
    			'http' => array(
        		'proxy' => 'tcp://'.$SETS['network']['proxyserver'],
        		'request_fulluri' => true,
        		'header' => "Proxy-Authorization: Basic $strProxyAuth",
				'timeout' => 1,
    			),
			);
		} else {
			$aContext = array(
				'http' => array(
        		'proxy' => 'tcp://'.$SETS['network']['proxyserver'],
        		'request_fulluri' => true,
				'timeout' => 1,
				),
			);
		}
		$intErrorReporting = error_reporting();
		error_reporting(0);
		$cxContext = stream_context_create($aContext);
		$arrFile   = file($versionfeed, false, $cxContext);	
		$arrError  = error_get_last();
		error_reporting($intErrorReporting);
		if ($arrError['message'] != "") $strError .= utf8_encode($arrError['message'])." (".translate("check proxy settings").")";
	} else {
		$intErrorReporting = error_reporting();
		error_reporting(0);
		$cxContext = stream_context_create(array('http' => array('timeout' => 1)));	
		$arrFile   = file($versionfeed, false, $cxContext);
		$arrError  = error_get_last();
		error_reporting($intErrorReporting);
		if ($arrError['message'] != "") $strError .= utf8_encode($arrError['message'])." (".translate("check proxy settings").")";
		
	}
	$strInstalled   = translate('Installed');
	$strAvailable   = translate('Available');
	$strInformation = translate('Information');
	$strVersion		= "";
	$strRelease		= "";
	$strRelInfo		= "";
	if (is_array($arrFile) && (count($arrFile)) != 0) {
		foreach ($arrFile AS $elem) {
			if (substr_count($elem,"version") 	   != 0) $strVersion = trim(strip_tags($elem));
			if (substr_count($elem,"release_date") != 0) $strRelease = trim(strip_tags($elem));
			if (substr_count($elem,"error") 	   != 0) $strError   = trim(strip_tags($elem));
			if (substr_count($elem,"information")  != 0) $strRelInfo = trim(strip_tags($elem));	
		}
	}
	$setFileAvailable = $strVersion;
	if (version_compare($strVersion, $setFileVersion,'==')) {
		$setFileInformation = "<span class='greenmessage'>".translate('You already have the latest version installed')."</span>";
	} else if (version_compare($strVersion, $setFileVersion,'>=')) {
		$setFileInformation = "<span class='redmessage'>".translate('You are using an old NagiosQL version. Please update to the latest stable version')."</span>: ";
		$setFileInformation .= "<a href=\"http://sourceforge.net/projects/nagiosql/files/\" target=\"_blank\">NagiosQL on Sourceforge</a>";
	} else if (version_compare($strVersion, $setFileVersion,'<=')) {
		$setFileInformation = "<span class='redmessage'>".translate('You are using a newer development version without official support')."</span>";
	}
	if (($strError != "none") && ($strError != "")) $setFileInformation = "<span class='redmessage'>".$strError."</span>";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  	<title>Version check</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="<?php echo $_SESSION['SETS']['path']['base_url']; ?>config/main.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $_SESSION['SETS']['path']['base_url']; ?>config/content.css" rel="stylesheet" type="text/css">
    <style type="text/css">
    <!--
    body {
	  font-family: Verdana, Arial, Helvetica, sans-serif;
	  font-size: 10px;
	  color: #000000;
	  background-color: #FFFFFF;
	  margin: 0px;
	  border: none;
    }
    -->
    </style>
  </head>
<body>
	<table width="100%" border="0" class="content_listtable" style="padding:0px; margin:0px; top:3px;">
        <tr>
        	<th style="text-align: center"><?php echo $strInstalled; ?></th>
			<th style="text-align: center"><?php echo $strAvailable; ?></th>
			<th style="text-align: left; padding-left: 30px;"><?php echo $strInformation; ?></th>
        </tr>
        <tr>
        	<td class="tdmb" style="width:90px;vertical-align:top;padding-top:4px;"><?php echo $setFileVersion; ?></td>
        	<td class="tdmb" style="width:90px;vertical-align:top;padding-top:4px;"><?php echo $setFileAvailable; ?></td>
        	<td class="tdlb" style="width:470px;vertical-align:top;padding-top:4px;"><?php echo $setFileInformation; ?></td>
    	</tr>
	</table>
    <script language="JavaScript">
        <!--
		parent.document.getElementById('vcheck').className 		 = 'elementHide';
        parent.document.getElementById('versioncheck').className = 'elementShow';
<?php
	if (($strError != "none") && ($strError != "")) {
		echo "        parent.document.getElementById('versioncheck').height = '65';";		
	}
?>
		//-->
    </script>
</body>
</html>
<?php
} else {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  	<title>Commandline</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
    <!--
    body {
	  font-family: Verdana, Arial, Helvetica, sans-serif;
	  font-size: 10px;
	  color: #000000;
	  background-color: #FFFFFF;
	  margin: 0px;
	  border: none;
    }
    -->
    </style>
  </head>
<body>
<p><br>Loading...</p>
</body>
</html>
<?php
}
?>