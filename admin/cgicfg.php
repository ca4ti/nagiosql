<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2011 by Martin Willisegger
//
// Project   : NagiosQL
// Component : File editor cgi.cfg
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
$intMain 		= 6;
$intSub  		= 23;
$intMenu 		= 2;
$preContent 	= "admin/nagioscfg.tpl.htm";
$strConfig		= "";
$strMessage		= "";
$intRemoveTmp 	= 0;
//
// Include preprocessing file
// ==========================
$preAccess		= 1;
$preFieldvars 	= 1;
require("../functions/prepend_adm.php");
$myConfigClass->getConfigData("method",$intMethod);
//
// Process post parameters
// =======================
$chkNagiosConf 	= isset($_POST['taNagiosCfg']) 	? $_POST['taNagiosCfg'] : "";
//
// Quote special characters
// ==========================
if (get_magic_quotes_gpc() == 0) {
  	$chkNagiosConf    = addslashes($chkNagiosConf);
}
//
// Define paths
// ============
$myConfigClass->getConfigData("nagiosbasedir",$strBaseDir);
$strOldDate    	= date("YmdHis",mktime());
$strConfigfile 	= str_replace("//","/",$strBaseDir."/cgi.cfg");
$strLocalBackup	= str_replace("//","/",$strBaseDir."/cgi.cfg_old_".$strOldDate);
//
// Process data
// ============
if ($chkNagiosConf != "") {
	if ($intMethod == 1) {
    	if (file_exists($strBaseDir) && (is_writable($strBaseDir) && (is_writable($strConfigfile)))) {
			// Backup config file
			$myConfigClass->moveFile("nagiosbasic","cgi.cfg");
			// Write configuration
			$resFile = fopen($strConfigfile,"w");
			$chkNagiosConf = stripslashes($chkNagiosConf);
			fputs($resFile,$chkNagiosConf);
			fclose($resFile);
			$myVisClass->processMessage("<span style=\"color:green\">".translate('Configuration file successfully written!')."</span>",$strMessage);
			$myDataClass->writeLog(translate('Configuration successfully written:')." ".$strConfigfile);
		} else {
			$myVisClass->processMessage(translate('Cannot open/overwrite the configuration file (check the permissions)!'),$strMessage);
			$myDataClass->writeLog(translate('Configuration write failed:')." ".$strConfigfile);	
		}
	} else if (($intMethod == 2) || ($intMethod == 3)) {
		// Backup config file
		$myConfigClass->moveFile("nagiosbasic","cgi.cfg");
		// Write file to temporary
		$strFileName = tempnam(sys_get_temp_dir(), 'nagiosql_cgi');	
		$resFile = fopen($strFileName,"w");
		$chkNagiosConf = stripslashes($chkNagiosConf);
		fputs($resFile,$chkNagiosConf);
		fclose($resFile);
		// Copy configuration to remoty system
		$intReturn = $myConfigClass->configCopy($strConfigfile,$strFileName,1);
		if ($intReturn == 0) {
			$myVisClass->processMessage("<span style=\"color:green\">".translate('Configuration file successfully written!')."</span>",$strMessage);
			$myDataClass->writeLog(translate('Configuration successfully written:')." ".$strConfigfile);
			unlink($strFileName);			
		} else {
			$myVisClass->processMessage(translate('Cannot open/overwrite the configuration file (check the permissions on remote system)!'),$strMessage);
			$myDataClass->writeLog(translate('Configuration write failed (remote):')." ".$strConfigfile);	
			unlink($strFileName);
		}
	}
}
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Include content
// ===============
$conttp->setVariable("TITLE",translate('CGI configuration file'));
$conttp->parse("header");
$conttp->show("header");
//
// Include input form
// ===================
$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
$conttp->setVariable("MAINSITE",$SETS['path']['root']."admin.php");
foreach($arrDescription AS $elem) {
	$conttp->setVariable($elem['name'],$elem['string']);
} 
//
// Open configuration
// ==================
if ($intMethod == 1) {
	if (file_exists($strConfigfile) && is_readable($strConfigfile)) {
		$resFile = fopen($strConfigfile,"r");
		if ($resFile) {
			while(!feof($resFile)) {
				$strConfig .= fgets($resFile,1024);
			}
		}
	} else {
		$myVisClass->processMessage(translate('Cannot open the data file (check the permissions)!'),$strMessage);
	}
} else if (($intMethod == 2) || ($intMethod == 3)) {
	// Write file to temporary
	$strFileName = tempnam(sys_get_temp_dir(), 'nagiosql_cgi');	
	// Copy configuration from remoty system
	$intReturn = $myConfigClass->configCopy($strConfigfile,$strFileName,0);
	if ($intReturn == 0) {
		$resFile = fopen($strFileName,"r");
		if (is_resource($resFile)) {
			while(!feof($resFile)) {
				$strConfig .= fgets($resFile,1024);
			}
			unlink($strFileName);
		} else {
			$myVisClass->processMessage(translate('Cannot open the temporary file'),$strMessage);
		}
	} else {
		$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
		$myDataClass->writeLog(translate('Configuration read failed (remote):')." ".$strConfigfile);	
		unlink($strFileName);
	}
}
if ($strMessage != "") $conttp->setVariable("MESSAGE",$strMessage);
$conttp->setVariable("DAT_NAGIOS_CONFIG",$strConfig);
$conttp->parse("naginsert");
$conttp->show("naginsert");
//
// Process footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>