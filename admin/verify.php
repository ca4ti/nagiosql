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
// Zweck:	Geschriebene Konfiguration prüfen
// Datei:	admin/verify.php
// Version: 2.0.2 (Internal)
// SVN:		$Id: verify.php 72 2008-04-03 07:01:46Z rouven $
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Menuvariabeln für diese Seite
// =============================
$intMain 		= 6;
$intSub  		= 19;
$intMenu 		= 2;
$preContent 	= "verify.tpl.htm";
$strMessage		= "";
$strInfo		= "";
//
// Vorgabedatei einbinden
// ======================
$preAccess	= 1;
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkCheck    = isset($_POST['checkConfig'])     ? $_POST['checkConfig']	    : "";
$chkReboot   = isset($_POST['restartNagios'])   ? $_POST['restartNagios']	: "";
$chkWriteMon = isset($_POST['writeMonitoring']) ? $_POST['writeMonitoring']	: "";
$chkWriteAdd = isset($_POST['writeAdditional']) ? $_POST['writeAdditional']	: "";
//
// Formulareingaben verarbeiten
// ============================
if ($chkCheck != "") {
	if (file_exists($SETS['nagios']['binary']) && is_executable($SETS['nagios']['binary'])) {
		$resFile = popen($SETS['nagios']['binary']." -v ".$SETS['nagios']['config']."nagios.cfg","r");
	} else {
		$strMessage = $LANG['file']['binaryfail'];
	}
}
if ($chkReboot != "") {
	// Prüfen, ob Nagios Daemon läuft
	clearstatcache();
	if (file_exists($SETS['nagios']['pidfile'])) {
		if (file_exists($SETS['nagios']['cmdfile']) && is_writable($SETS['nagios']['cmdfile'])) {
				$strCommandString = "[".mktime()."] RESTART_PROGRAM;".mktime();
				$resCmdFile = fopen($SETS['nagios']['cmdfile'],"w");
				if ($resCmdFile) {
					fputs($resCmdFile,$strCommandString);
					fclose($resCmdFile);
					$myDataClass->writeLog($LANG['logbook']['restartok']);
					$strInfo = $LANG['file']['restartet'];
				} else {
					$myDataClass->writeLog($LANG['logbook']['cmdfailed']);
					$strMessage = $LANG['file']['cmdfail'];
				}
		} else {
			$myDataClass->writeLog($LANG['logbook']['cmdfailed']);
			$strMessage = $LANG['file']['cmdfail'];
		}
	} else {
		$myDataClass->writeLog($LANG['logbook']['nagiosdown']);
		$strMessage = $LANG['file']['nodaemon'];
	}
}
if ($chkWriteMon != "") {
	// Hostkonfiguration schreiben
	$strInfo = "Write host configurations ...<br>";
	$strSQL  = "SELECT id FROM tbl_host WHERE active='1'";
	$myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
	$intError = 0;
	if ($intDataCount != 0) {
		foreach ($arrData AS $data) {
			$myConfigClass->createConfigSingle("tbl_host",$data['id']);
			if ($myConfigClass->strDBMessage != $LANG['file']['success']) $intError++;
		}
	}
	if ($intError == 0) {
		$strInfo .= $LANG['file']['success']."<br>";
	} else {
		$strInfo .= $LANG['file']['failed']."<br>";
	}
	// servicekonfiguration schreiben
	$strInfo .= "Write service configurations ...<br>";
	$strSQL   = "SELECT id, config_name FROM tbl_service WHERE active='1' GROUP BY config_name";
	$myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
	$intError = 0;
	if ($intDataCount != 0) {
		foreach ($arrData AS $data) {
			$myConfigClass->createConfigSingle("tbl_service",$data['id']);
			if ($myConfigClass->strDBMessage != $LANG['file']['success']) $intError++;
		}
	}
	if ($intError == 0) {
		$strInfo .= $LANG['file']['success']."<br>";
	} else {
		$strInfo .= $LANG['file']['failed']."<br>";
	}
	$strInfo .= "Write hostgroups.cfg ...<br>";
	$myConfigClass->createConfig("tbl_hostgroup");
	$strInfo .= $myConfigClass->strDBMessage."<br>";	
	$strInfo .= "Write servicegroups.cfg ...<br>";
	$myConfigClass->createConfig("tbl_servicegroup");
	$strInfo .= $myConfigClass->strDBMessage."<br>";	

}
if ($chkWriteAdd != "") {
	$strInfo = "Write timeperiods.cfg ... ";
	$myConfigClass->createConfig("tbl_timeperiod");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= "Write misccommands.cfg ... ";
	$myConfigClass->createConfig("tbl_misccommand");
	$strInfo .= $myConfigClass->strDBMessage."<br>";	
	$strInfo .= "Write checkcommands.cfg ... ";
	$myConfigClass->createConfig("tbl_checkcommand");
	$strInfo .= $myConfigClass->strDBMessage."<br>";	
	$strInfo .= "Write contacts.cfg ... ";
	$myConfigClass->createConfig("tbl_contact");
	$strInfo .= $myConfigClass->strDBMessage."<br>";	
	$strInfo .= "Write contactgroups.cfg ... ";
	$myConfigClass->createConfig("tbl_contactgroup");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= "Write servicedependencies.cfg ... ";
	$myConfigClass->createConfig("tbl_servicedependency");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= "Write hostdependencies.cfg ... ";
	$myConfigClass->createConfig("tbl_hostdependency");
	$strInfo .= $myConfigClass->strDBMessage."<br>";	
	$strInfo .= "Write serviceescalations.cfg ... ";
	$myConfigClass->createConfig("tbl_serviceescalation");
	$strInfo .= $myConfigClass->strDBMessage."<br>";	
	$strInfo .= "Write hostescalations.cfg ... ";
	$myConfigClass->createConfig("tbl_hostescalation");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= "Write serviceextinfo.cfg ... ";
	$myConfigClass->createConfig("tbl_serviceextinfo");
	$strInfo .= $myConfigClass->strDBMessage."<br>";
	$strInfo .= "Write hostextinfo.cfg ... ";
	$myConfigClass->createConfig("tbl_hostextinfo");
	$strInfo .= $myConfigClass->strDBMessage."<br>";	
}
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm6']." -> ".$LANG['menu']['item_admsub19']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['verifyconfig']);
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("CHECK_CONFIG",$LANG['file']['checkconfig']);
$conttp->setVariable("RESTART_NAGIOS",$LANG['file']['restart']);
$conttp->setVariable("WRITE_MONITORING_DATA",$LANG['file']['write_monitoring_data']);
$conttp->setVariable("WRITE_ADDITIONAL_DATA",$LANG['file']['write_additional_data']);
if (($chkCheck == "") && ($chkReboot == "")) $conttp->setVariable("WARNING",$LANG['file']['warning']);
$conttp->setVariable("MAKE",$LANG['file']['check']);
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
if ($strMessage != "") {
	$conttp->setVariable("VERIFY_CLASS","dbmessage");
	$conttp->setVariable("VERIFY_LINE",$strMessage);
} else if (isset($resFile)){
	$intError   = 0;
	$intWarning = 0;
	while(!feof($resFile)) {
		$strLine = fgets($resFile,1024);
		if (substr_count($strLine,"Error") != 0) {
			$conttp->setVariable("VERIFY_CLASS","dbmessage");
			$conttp->setVariable("VERIFY_LINE",$strLine);
			$conttp->parse("verifyline");
			$intError++;
			if (substr_count($strLine,"Total Errors") != 0) $intError--;
		}
		if (substr_count($strLine,"Warning") != 0) {
			$conttp->setVariable("VERIFY_CLASS","warnmessage");
			$conttp->setVariable("VERIFY_LINE",$strLine);
			$conttp->parse("verifyline");
			$intWarning++;
			if (substr_count($strLine,"Total Warnings") != 0) $intWarning--;
		}
	}
	$myDataClass->writeLog($LANG['logbook']['configcheck']." ".$intWarning."/".$intError);
	pclose($resFile);
	if (($intError == 0) && ($intWarning == 0)) {
		$conttp->setVariable("VERIFY_CLASS","okmessage");
		$conttp->setVariable("VERIFY_LINE","<br>".$LANG['file']['configok']);
		$conttp->parse("verifyline");	
	}
}
if ($strInfo != "") {
	$conttp->setVariable("VERIFY_CLASS","okmessage");
	$conttp->setVariable("VERIFY_LINE","<br>".$strInfo);
	$conttp->parse("verifyline");	
}
$conttp->parse("main");
$conttp->show("main");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org'>NagiosQL</a> - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>