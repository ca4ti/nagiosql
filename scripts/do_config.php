#!/usr/bin/php5
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
// Component : Configuration scripting interface
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-03-13 14:00:26 +0100 (So, 13. Mär 2011) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.1.1
// Revision  : $LastChangedRevision: 1058 $
//
///////////////////////////////////////////////////////////////////////////////
//
// To enable scripting functionality - comment out the line below
// ==============================================================
exit;
//
// Include preprocessing file
// ==========================
$preAccess    	= 0;
$preNoMain  	= 1;
require(str_replace("scripts","",dirname(__FILE__)) ."functions/prepend_scripting.php");
$myConfigClass->getConfigData("method",$intMethod);
//
// Process post parameters
// =======================
$argFunction	= isset($argv[1])	? htmlspecialchars($argv[1], ENT_QUOTES, 'utf-8') : "none";
$argDomain		= isset($argv[2])	? htmlspecialchars($argv[2], ENT_QUOTES, 'utf-8') : "none";
$argObject		= isset($argv[3])	? htmlspecialchars($argv[3], ENT_QUOTES, 'utf-8') : "none";
if ((($argFunction == "none") || ($argDomain == "none")) || (($argFunction == "write") && ($argObject == "none")) ||
	(($argFunction != "write") && ($argFunction != "check") && ($argFunction != "restart"))){
	echo "Usage: ".htmlspecialchars($argv[0], ENT_QUOTES, 'utf-8')." function domain [object]\n";
	echo "function = write/check/restart\n";
	echo "domain   = domain name like 'localhost'\n";
	echo "object   = object name like 'contact' - see table name without 'tbl_' (only in 'write' function)\n";
	exit(1);
}
//
// Get domain ID
// =============
$strSQL 	= "SELECT `id` FROM `tbl_domain` WHERE `domain`='$argDomain'";
$intDomain 	= $myDBClass->getFieldData($strSQL);
if ($intDomain == "") {
	echo "Domain '".$argDomain."' doesn not exist\n";
	exit(1);
} else if ($intDomain == "0") {
	echo "Domain '".$argDomain."' cannot be used\n";
	exit(1);
} else {
	$myDataClass->intDomainId 	= $intDomain;
	$myConfigClass->intDomainId = $intDomain;
}
//
// Process form variables
// ======================
if ($argFunction == "check") {
	$myConfigClass->getConfigData("binaryfile",$strBinary);
  	$myConfigClass->getConfigData("basedir",$strBaseDir);
  	$myConfigClass->getConfigData("nagiosbasedir",$strNagiosBaseDir);
  	$myConfigClass->getConfigData("conffile",$strConffile);
  	if ($intMethod == 1) {
    	if (file_exists($strBinary) && is_executable($strBinary)) {
      		$resFile = popen($strBinary." -v ".$strConffile,"r");
    	} else {
      		echo "Cannot find the Nagios binary or no rights for execution!\n";
			exit(1);
    	}
	} else if ($intMethod == 2) {
		$booReturn = 0;
		if (!isset($myConfigClass->resConnectId) || !is_resource($myConfigClass->resConnectId)) {
			$booReturn = $myConfigClass->getFTPConnection();
		}
		if ($booReturn == 1) {
      		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
		} else {
      		if (!($resFile = ftp_exec($myConfigClass->resConnectId,$strBinary.' -v '.$strConffile))) {
        		echo "Remote execution (FTP SITE EXEC) is not supported on your system!\n";
				exit(1);
      		}
      		ftp_close($conn_id);		
		}
  	} else if ($intMethod == 3) {
		$booReturn = 0;
		if (!isset($myConfigClass->resConnectId) || !is_resource($myConfigClass->resConnectId)) {
			$booReturn = $myConfigClass->getSSHConnection();
		}
		if ($booReturn == 1) {
      		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
		} else {
			if ((is_array($myConfigClass->sendSSHCommand('ls '.$strBinary))) && 
				(is_array($myConfigClass->sendSSHCommand('ls '.$strConffile)))) {
				$arrResult = $myConfigClass->sendSSHCommand($strBinary.' -v '.$strConffile);
				if (!is_array($arrResult) || ($arrResult == false)) {
					echo "Remote execution of nagios verify command failed (remote SSH)!\n";
					exit(1);
				}
			} else {
				echo "Nagios binary or configuration file not found (remote SSH)!\n";
				exit(1);
			}
		}
	}
}
if ($argFunction == "restart") {
  	// Read config file
  	$myConfigClass->getConfigData("commandfile",$strCommandfile);
  	$myConfigClass->getConfigData("pidfile",$strPidfile);
  	// Check state nagios demon
  	clearstatcache();
  	if ($intMethod == 1) {
    	if (file_exists($strPidfile)) {
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
          			echo "Restart command successfully send to Nagios\n";
					exit(0);
        		}
      		}
			echo "Restart failed - Nagios command file not found or no rights to execute\n";
			exit(1);
    	} else {
      		echo "Nagios daemon is not running, cannot send restart command!\n";
			exit(1);
    	}
  	} else if ($intMethod == 2) {
      	echo "Nagios restart is not possible via FTP remote connection!\n";
		exit(1);
  	} else if ($intMethod == 3) {
		$booReturn = 0;
		if (!isset($myConfigClass->resConnectId) || !is_resource($myConfigClass->resConnectId)) {
			$booReturn = $myConfigClass->getSSHConnection();
		}
		if ($booReturn == 1) {
      		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
		} else {
			if (is_array($myConfigClass->sendSSHCommand('ls '.$strCommandfile))) {
				$strCommandString = "[".mktime()."] RESTART_PROGRAM;".mktime();
				$arrResult = $myConfigClass->sendSSHCommand('echo "'.$strCommandString.'" >> '.$strCommandfile);
				if ($arrResult == false) {
					echo "Restart failed - Nagios command file not found or no rights to execute (remote SSH)!\n";
					exit(1);
				}
          		echo "Nagios daemon successfully restarted (remote SSH)\n";
				exit(0);
			} else {
				echo "Nagios command file not found (remote SSH)!\n";
				exit(1);
			}
		}
	}
}
if ($argFunction == "write") {
	if ($argObject == "host") {
  		// Write host configuration
  		$strInfo = "Write host configurations  ...\n";
  		$strSQL  = "SELECT `id` FROM `tbl_host` WHERE `config_id` = $intDomain AND `active`='1'";
  		$myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
  		$intError = 0;
  		if ($intDataCount != 0) {
    		foreach ($arrData AS $data) {
      			$intReturn = $myConfigClass->createConfigSingle("tbl_host",$data['id']);
      			if ($intReturn == 1) $intError++;
    		}
  		}
  		if ($intError == 0) {
    		$strInfo .= "Host configuration files successfully written!\n";
  		} else {
    		$strInfo .= "Cannot open/overwrite the configuration file (check the permissions)!\n";
  		}
	} else if ($argObject == "service") {
  		// Write service configuration
  		$strInfo  = "Write service configurations ...\n";
  		$strSQL   = "SELECT `id`, `config_name` FROM `tbl_service` WHERE `config_id` = $intDomain AND `active`='1' GROUP BY `config_name`";
  		$myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
  		$intError = 0;
  		if ($intDataCount != 0) {
    		foreach ($arrData AS $data) {
      			$myConfigClass->createConfigSingle("tbl_service",$data['id']);
      			if ($intReturn == 1) $intError++;
    		}
  		}
  		if ($intError == 0) {
    		$strInfo .= "Service configuration file successfully written!\n";
  		} else {
    		$strInfo .= "Cannot open/overwrite the configuration file (check the permissions)!\n";
  		}
	} else {
		$strInfo   = "Write ".$argObject.".cfg ...\n";
		$booReturn = $myConfigClass->createConfig("tbl_".$argObject);
  		if ($booReturn == 0) {
    		$strInfo .= "Configuration file ".$argObject.".cfg successfully written!\n";
  		} else {
    		$strInfo .= "Cannot open/overwrite the configuration file ".$argObject.".cfg (check the permissions or probably tbl_".$argObject." does not exists)!\n";
  		}
	}
	echo $strInfo;
}

//
// Output processing
// =================
if (isset($resFile) && ($resFile != false)){
	$intError   = 0;
	$intWarning = 0;
	$strOutput  = "";
  	while(!feof($resFile)) {
    	$strLine = fgets($resFile,1024);
    	if (substr_count($strLine,"Error:") != 0) {
      		$intError++;
    	}
    	if (substr_count($strLine,"Warning:") != 0) {
      		$intWarning++;
    	}
    	$strOutput .= $strLine;
  	}
  	pclose($resFile);
	echo $strOutput."\n";
  	if (($intError == 0) && ($intWarning == 0)) {
		echo "Written configuration files are valid, Nagios can be restarted!\n\n";
  	}
} else if (isset($arrResult) && is_array($arrResult)) {
	$intError   = 0;
	$intWarning = 0;
	$strOutput  = "";
  	foreach ($arrResult AS $elem) {
    	if (substr_count($strLine,"Error:") != 0) {
      		$intError++;
    	}
    	if (substr_count($strLine,"Warning:") != 0) {
      		$intWarning++;
    	}
    	$strOutput .= $strLine;
  	}
	echo $strOutput."\n";
  	if (($intError == 0) && ($intWarning == 0)) {
		echo "Written configuration files are valid, Nagios can be restarted!\n\n";
  	}
}
?>
