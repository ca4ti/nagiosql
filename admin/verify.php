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
// Component : Configuration verification
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-03-14 11:04:07 +0100 (Mo, 14. Mär 2011) $
// Author    : $LastChangedBy: martin $
// Version   : 3.1.1
// Revision  : $LastChangedRevision: 1061 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$intMain    	= 6;
$intSub     	= 19;
$intMenu    	= 2;
$preContent 	= "admin/verify.tpl.htm";
$strMessage 	= "";
$strInfo    	= "";
//
// Include preprocessing file
// ==========================
$preAccess    	= 1;
$preFieldvars 	= 1;
require("../functions/prepend_adm.php");
$myConfigClass->getConfigData("method",$intMethod);
//
// Process post parameters
// =======================
$chkCheck    	= isset($_POST['checkConfig'])     ? $_POST['checkConfig']     : "";
$chkReboot   	= isset($_POST['restartNagios'])   ? $_POST['restartNagios'] : "";
$chkWriteMon 	= isset($_POST['writeMonitoring']) ? $_POST['writeMonitoring'] : "";
$chkWriteAdd 	= isset($_POST['writeAdditional']) ? $_POST['writeAdditional'] : "";
//
// Process form variables
// ======================
if ($chkCheck != "") {
  	$myConfigClass->getConfigData("binaryfile",$strBinary);
  	$myConfigClass->getConfigData("basedir",$strBaseDir);
  	$myConfigClass->getConfigData("nagiosbasedir",$strNagiosBaseDir);
  	$myConfigClass->getConfigData("conffile",$strConffile);
  	if ($intMethod == 1) {
    	if (file_exists($strBinary) && is_executable($strBinary)) {
      		$resFile = popen($strBinary." -v ".$strConffile,"r");
    	} else {
      		$strMessage = translate('Cannot find the Nagios binary or no rights for execution!');
    	}
	} else if ($intMethod == 2) {
		$booReturn = 0;
		if (!isset($myConfigClass->resConnectId) || !is_resource($myConfigClass->resConnectId)) {
			$booReturn = $myConfigClass->getFTPConnection();
		}
		if ($booReturn == 1) {
      		$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
		} else {
      		if (!($resFile = ftp_exec($myConfigClass->resConnectId,$strBinary.' -v '.$strConffile))) {
        		$strMessage = translate('Remote execution (FTP SITE EXEC) is not supported on your system!');
      		}
      		ftp_close($conn_id);		
		}
  	} else if ($intMethod == 3) {
		$booReturn = 0;
		if (!isset($myConfigClass->resConnectId) || !is_resource($myConfigClass->resConnectId)) {
			$booReturn = $myConfigClass->getSSHConnection();
		}
		if ($booReturn == 1) {
      		$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
		} else {
			if (($strBinary != "") && ($strConffile != "") && (is_array($myConfigClass->sendSSHCommand('ls '.$strBinary))) && 
				(is_array($myConfigClass->sendSSHCommand('ls '.$strConffile)))) {
				$arrResult = $myConfigClass->sendSSHCommand($strBinary.' -v '.$strConffile,15000);
				if (!is_array($arrResult) || ($arrResult == false)) {
					$myVisClass->processMessage(translate('Remote execution of nagios verify command failed (remote SSH)!'),$strMessage);
				}
			} else {
				$myVisClass->processMessage(translate('Nagios binary or configuration file not found (remote SSH)!'),$strMessage);	
			}
		}
	}
}
if ($chkReboot != "") {
  	// Read config file
  	$myConfigClass->getConfigData("commandfile",$strCommandfile);
	$myConfigClass->getConfigData("binaryfile",$strBinary);
  	$myConfigClass->getConfigData("pidfile",$strPidfile);
  	// Check state nagios demon
  	clearstatcache();
  	if ($intMethod == 1) {
	    if (substr_count(PHP_OS,"Linux") != 0) {
    		exec('ps -ef | grep '.basename($strBinary).' | grep -v grep',$arrExec);
		} else {
			$arrExec[0] = 1;
		}
		if (file_exists($strPidfile) && isset($arrExec[0])) {
      		if (file_exists($strCommandfile) && is_writable($strCommandfile)) {
        		$strCommandString = "[".mktime()."] RESTART_PROGRAM;".mktime();
        		$timeout = 3;
        		$old = ini_set('default_socket_timeout', $timeout);
        		$resCmdFile = fopen($strCommandfile,"w");
        		ini_set('default_socket_timeout', $old);
        		stream_set_timeout($resCmdFile, $timeout);
        		stream_set_blocking($resCmdFile, 0);
        		if ($resCmdFile) {
          			fputs($resCmdFile,$strCommandString);
          			fclose($resCmdFile);
          			$myDataClass->writeLog("<span class=\"verify-ok\">".translate('Nagios daemon successfully restarted')."</span><br><br>");
          			$strInfo = "<span class=\"verify-ok\">".translate('Restart command successfully send to Nagios')."</span><br><br>";
        		} else {
          			$myDataClass->writeLog("<span class=\"verify-critical\">".translate('Restart failed - Nagios command file not found or no rights to execute')."</span><br><br>");
          			$strMessage = "<span class=\"verify-critical\">".translate('Nagios command file not found or no rights to write!')."</span><br><br>";
        		}
      		} else {
        		$myDataClass->writeLog("<span class=\"verify-critical\">".translate('Restart failed - Nagios command file not found or no rights to execute')."</span><br><br>");
        		$strMessage = "<span class=\"verify-critical\">".translate('Restart failed - Nagios command file not found or no rights to execute')."</span><br><br>";
      		}
    	} else {
      		$myDataClass->writeLog(translate('Restart failed - Nagios daemon was not running'));
      		$strMessage = "<span class=\"verify-critical\">".translate('Nagios daemon is not running, cannot send restart command!')."</span><br><br>";
    	}
  	} else if ($intMethod == 2) {
      	$myDataClass->writeLog(translate('Restart failed - FTP restrictions'));
      	$strMessage = "<span class=\"verify-critical\">".translate('Nagios restart is not possible via FTP remote connection!')."</span><br><br>";
  	} else if ($intMethod == 3) {
		$booReturn = 0;
		if (!isset($myConfigClass->resConnectId) || !is_resource($myConfigClass->resConnectId)) {
			$booReturn = $myConfigClass->getSSHConnection();
		}
		if ($booReturn == 1) {
      		$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
		} else {
			if (is_array($myConfigClass->sendSSHCommand('ls '.$strCommandfile))) {
				$strCommandString = "[".mktime()."] RESTART_PROGRAM;".mktime();
				$arrInfo = ssh2_sftp_stat($myConfigClass->resSFTP, $strCommandfile);
				$intFileStamp1 = $arrInfo['mtime'];
				$arrResult = $myConfigClass->sendSSHCommand('echo "'.$strCommandString.'" >> '.$strCommandfile);
				$arrInfo = ssh2_sftp_stat($myConfigClass->resSFTP, $strCommandfile);
				$intFileStamp2 = $arrInfo['mtime'];
				if ($intFileStamp2 <= $intFileStamp1) {
					$myVisClass->processMessage(translate('Restart failed - Nagios command file not found or no rights to execute (remote SSH)!'),$strMessage);
				} else {
					$myDataClass->writeLog("<span class=\"verify-ok\">".translate('Nagios daemon successfully restarted (remote SSH)')."</span><br><br>");
          			$strInfo = "<span class=\"verify-ok\">".translate('Restart command successfully send to Nagios (remote SSH)')."</span><br><br>";
				}
			} else {
				$myVisClass->processMessage(translate('Nagios command file not found (remote SSH)!'),$strMessage);	
			}
		}
	}
}
if ($chkWriteMon != "") {
  	// Write host configuration
  	$strInfo = translate("Write host configurations")." ...<br>";
  	$strSQL  = "SELECT `id` FROM `tbl_host` WHERE `config_id` = $chkDomainId AND `active`='1'";
  	$myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
  	$intError = 0;
  	if ($intDataCount != 0) {
    	foreach ($arrData AS $data) {
      		$myConfigClass->createConfigSingle("tbl_host",$data['id']);
      		if ($myConfigClass->strDBMessage != translate("Configuration file successfully written!")) $intError++;
    	}
  	}
  	if (($intError == 0) && ($intDataCount != 0)) {
    	$strInfo .= "<span class=\"verify-ok\">".translate("Configuration file successfully written!")."</span><br><br>";
  	} else if ($intDataCount != 0) {
    	$strInfo .= "<span class=\"verify-critical\">".translate("Cannot open/overwrite the configuration file (check the permissions)!")."</span><br>";
  	} else {
    	$strInfo .= "<span class=\"verify-critical\">".translate("No configuration items defined!")."</span><br><br>";
	}
  	// Write service configuration
  	$strInfo .= translate("Write service configurations")." ...<br>";
  	$strSQL   = "SELECT `id`, `config_name` FROM `tbl_service` WHERE `config_id` = $chkDomainId AND `active`='1' GROUP BY `config_name`";
  	$myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
  	$intError = 0;
  	if ($intDataCount != 0) {
    	foreach ($arrData AS $data) {
      		$myConfigClass->createConfigSingle("tbl_service",$data['id']);
      		if ($myConfigClass->strDBMessage != translate("Configuration file successfully written!")) $intError++;
    	}
  	}
  	if (($intError == 0) && ($intDataCount != 0)) {
    	$strInfo .= "<span class=\"verify-ok\">".translate("Configuration file successfully written!")."</span><br><br>";
  	} else if ($intDataCount != 0) {
    	$strInfo .= "<span class=\"verify-critical\">".translate("Cannot open/overwrite the configuration file (check the permissions)!")."</span><br>";
  	} else {
    	$strInfo .= "<span class=\"verify-critical\">".translate("No configuration items defined!")."</span><br><br>";
	}
	$strInfo .= translate("Write")." hostgroups.cfg ...<br>";
	$myConfigClass->createConfig("tbl_hostgroup");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= translate("Write")." servicegroups.cfg ...<br>";
	$myConfigClass->createConfig("tbl_servicegroup");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= translate("Write")." hosttemplates.cfg ...<br>";
	$myConfigClass->createConfig("tbl_hosttemplate");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= translate("Write")." servicetemplates.cfg ...<br>";
	$myConfigClass->createConfig("tbl_servicetemplate");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
}
if ($chkWriteAdd != "") {
	$strInfo = translate("Write")." timeperiods.cfg ...<br>";
	$myConfigClass->createConfig("tbl_timeperiod");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= translate("Write")." commands.cfg ...<br>";
	$myConfigClass->createConfig("tbl_command");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= translate("Write")." contacts.cfg ...<br>";
	$myConfigClass->createConfig("tbl_contact");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= translate("Write")." contactgroups.cfg ...<br>";
	$myConfigClass->createConfig("tbl_contactgroup");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= translate("Write")." contacttemplates.cfg ...<br>";
	$myConfigClass->createConfig("tbl_contacttemplate");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= translate("Write")." servicedependencies.cfg ...<br>";
	$myConfigClass->createConfig("tbl_servicedependency");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= translate("Write")." hostdependencies.cfg ...<br>";
	$myConfigClass->createConfig("tbl_hostdependency");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= translate("Write")." serviceescalations.cfg ...<br>";
	$myConfigClass->createConfig("tbl_serviceescalation");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= translate("Write")." hostescalations.cfg ...<br>";
	$myConfigClass->createConfig("tbl_hostescalation");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= translate("Write")." serviceextinfo.cfg ...<br>";
	$myConfigClass->createConfig("tbl_serviceextinfo");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= translate("Write")." hostextinfo.cfg ...<br>";
	$myConfigClass->createConfig("tbl_hostextinfo");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
}
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// include content
// ===============
$conttp->setVariable("TITLE",translate('Check written configuration files'));
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("CHECK_CONFIG",translate('Check configuration files:'));
$conttp->setVariable("RESTART_NAGIOS",translate('Restart Nagios:'));
$conttp->setVariable("WRITE_MONITORING_DATA",translate('Write monitoring data'));
$conttp->setVariable("WRITE_ADDITIONAL_DATA",translate('Write additional data'));
if (($chkCheck == "") && ($chkReboot == "")) $conttp->setVariable("WARNING",translate('Warning, always check the configuration files before restart Nagios!'));
$conttp->setVariable("MAKE",translate('Do it'));
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
$strOutput = "<br>";
if ($strMessage != "") {
	$conttp->setVariable("VERIFY_CLASS","dbmessage");
	$conttp->setVariable("VERIFY_LINE",$strMessage);
} else if (isset($resFile) && ($resFile != false)){
	$intError   = 0;
	$intWarning = 0;
  	while(!feof($resFile)) {
    	$strLine = fgets($resFile,1024);
    	if ((substr_count($strLine,"Error:") != 0) || (substr_count($strLine,"Total Errors:") != 0)) {
      		$conttp->setVariable("VERIFY_CLASS","errormessage");
      		$conttp->setVariable("VERIFY_LINE",$strLine);
      		$conttp->parse("verifyline");
      		$intError++;
			if (substr_count($strLine,"Total Errors:") != 0) $intError--;
    	}
    	if ((substr_count($strLine,"Warning:") != 0) || (substr_count($strLine,"Total Warnings:") != 0)) {
      		$conttp->setVariable("VERIFY_CLASS","warnmessage");
      		$conttp->setVariable("VERIFY_LINE",$strLine);
      		$conttp->parse("verifyline");
      		$intWarning++;
			if (substr_count($strLine,"Total Warnings:") != 0) $intWarning--;
    	}
    	$strOutput .= $strLine."<br>";
  	}
  	$myDataClass->writeLog(translate('Written Nagios configuration checked - Warnings/Errors:')." ".$intWarning."/".$intError);
  	pclose($resFile);
  	if (($intError == 0) && ($intWarning == 0)) {
    	$conttp->setVariable("VERIFY_CLASS","greenmessage");
    	$conttp->setVariable("VERIFY_LINE","<br><b>".translate('Written configuration files are valid, Nagios can be restarted!')."</b>");
    	$conttp->parse("verifyline");
  	}	
  	$conttp->setVariable("DATA",$strOutput);
  	$conttp->parse("verifyline");
} else if (isset($arrResult) && is_array($arrResult)) {
	$intError   = 0;
	$intWarning = 0;
  	foreach ($arrResult AS $elem) {
    	if ((substr_count($elem,"Error:") != 0) || (substr_count($elem,"Total Errors:") != 0)) {
      		$conttp->setVariable("VERIFY_CLASS","errormessage");
      		$conttp->setVariable("VERIFY_LINE",$elem);
      		$conttp->parse("verifyline");
      		$intError++;
      		if (substr_count($elem,"Total Errors:") != 0) $intError--;
    	}
    	if ((substr_count($elem,"Warning:") != 0) || (substr_count($elem,"Total Warnings:") != 0)) {
      		$conttp->setVariable("VERIFY_CLASS","warnmessage");
      		$conttp->setVariable("VERIFY_LINE",$elem);
      		$conttp->parse("verifyline");
      		$intWarning++;
      		if (substr_count($elem,"Total Warnings:") != 0) $intWarning--;
    	}
    	$strOutput .= $elem."<br>";
  	}
  	$myDataClass->writeLog(translate('Written Nagios configuration checked - Warnings/Errors:')." ".$intWarning."/".$intError);
  	if (($intError == 0) && ($intWarning == 0)) {
    	$conttp->setVariable("VERIFY_CLASS","greenmessage");
    	$conttp->setVariable("VERIFY_LINE","<br><b>".translate('Written configuration files are valid, Nagios can be restarted!')."</b>");
    	$conttp->parse("verifyline");
  	}	
  	$conttp->setVariable("DATA",$strOutput);
  	$conttp->parse("verifyline");

}
if ($strInfo != "") {
  	$conttp->setVariable("VERIFY_CLASS","okmessage");
  	$conttp->setVariable("VERIFY_LINE","<br>".$strInfo);
  	$conttp->parse("verifyline");
}
$conttp->parse("main");
$conttp->show("main");
//
// Insert footer
// =============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>