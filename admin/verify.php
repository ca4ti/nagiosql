<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL 2005
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005 by Martin Willisegger / nagios.ql2005@wizonet.ch
//
// Projekt:	NagiosQL Applikation
// Author :	Martin Willisegger
// Datum:	01.04.2005
// Zweck:	Geschriebene Konfiguration prüfen
// Datei:	admin/verify.php
// Version:	1.02
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
$setFileVersion = "1.02";
$strMessage		= "";
$strInfo		= "";
//
// Vorgabedatei einbinden
// ======================
$preRights 	= "admin2";
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkCheck  = isset($_POST['checkConfig'])   ? $_POST['checkConfig']	    : "";
$chkReboot = isset($_POST['restartNagios']) ? $_POST['restartNagios']	: "";
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
	if (file_exists($SETS['nagios']['cmdfile']) && is_writable($SETS['nagios']['cmdfile'])) {
		// Prüfen, ob Nagios Daemon läuft
		$resCheck  = popen("ps -ef | grep ".$SETS['nagios']['binary']." | grep -v grep | wc -l","r");
		$strReturn = fgets($resCheck,10);
		pclose($resCheck);
		if ((int)$strReturn >= 1) {
			$strCommandString = "[".mktime()."] RESTART_PROGRAM;".mktime();
			$resCmdFile = fopen($SETS['nagios']['cmdfile'],"w");
			if ($resCmdFile) {
				fputs($resCmdFile,$strCommandString);
				fclose($resCmdFile);
				$myVisClass->writeLog($LANG['logbook']['restartok']);
				$strInfo = $LANG['file']['restartet'];
				
			} else {
				$myVisClass->writeLog($LANG['logbook']['cmdfailed']);
				$strMessage = $LANG['file']['cmdfail'];
			}
		} else {
			$myVisClass->writeLog($LANG['logbook']['nagiosdown']);
			$strMessage = $LANG['file']['nodaemon'];
		}
	} else {
		$myVisClass->writeLog($LANG['logbook']['cmdfailed']);
		$strMessage = $LANG['file']['cmdfail'];
	}
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
	$myVisClass->writeLog($LANG['logbook']['configcheck']." ".$intWarning."/".$intError);
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
// Alle Konsistenztests nochmals anzeigen
$conttp->setVariable("CONSISTENCY",$LANG['admincontent']['consistency']);
$strHostsMessage = $myVisClass->checkConsistHosts();
$conttp->setVariable("CONSUSAGE_HOSTS",$strHostsMessage);
if ($strHostsMessage == $LANG['admincontent']['hostsok']) {
	$conttp->setVariable("HOST_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("HOST_MSGCLASS","dbmessage");
}
$strServiceMessage = $myVisClass->checkConsistServices();
$conttp->setVariable("CONSUSAGE_SERVICES",$strServiceMessage);
if ($strServiceMessage == $LANG['admincontent']['servicesok']) {
	$conttp->setVariable("SERV_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("SERV_MSGCLASS","dbmessage");
}
$strHostGroupMessage = $myVisClass->checkConsistHostgroups();
$conttp->setVariable("CONSUSAGE_HOSTG",$strHostGroupMessage);
if ($strHostGroupMessage == $LANG['admincontent']['hostgroupsok']) {
	$conttp->setVariable("HOSTG_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("HOSTG_MSGCLASS","dbmessage");
}
$strServiceGroupMessage = $myVisClass->checkConsistServicegroups();
$conttp->setVariable("CONSUSAGE_SERVG",$strServiceGroupMessage);
if ($strServiceGroupMessage == $LANG['admincontent']['servicegroupsok']) {
	$conttp->setVariable("SERVG_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("SERVG_MSGCLASS","dbmessage");
}
$strCheckMessage = $myVisClass->checkConsistCheckcommands();
$conttp->setVariable("CONSUSAGE_CHECK",$strCheckMessage);
if ($strCheckMessage == $LANG['admincontent']['checkcommandsok']) {
	$conttp->setVariable("CHECK_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("CHECK_MSGCLASS","dbmessage");
}
$strMiscMessage = $myVisClass->checkConsistMisccommands();
$conttp->setVariable("CONSUSAGE_MISC",$strMiscMessage);
if ($strMiscMessage == $LANG['admincontent']['misccommandsok']) {
	$conttp->setVariable("MISC_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("MISC_MSGCLASS","dbmessage");
}
$strContMessage = $myVisClass->checkConsistContacts();
$conttp->setVariable("CONSUSAGE_CONTACTS",$strContMessage);
if ($strContMessage == $LANG['admincontent']['contactsok']) {
	$conttp->setVariable("CON_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("CON_MSGCLASS","dbmessage");
}
$strContGroupMessage = $myVisClass->checkConsistContactgroups();
$conttp->setVariable("CONSUSAGE_CGROUPS",$strContGroupMessage);
if ($strContGroupMessage == $LANG['admincontent']['cgroupssok']) {
	$conttp->setVariable("CGROUP_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("CGROUP_MSGCLASS","dbmessage");
}
$strTimeGroupMessage = $myVisClass->checkConsistTimeperiods();
$conttp->setVariable("CONSUSAGE_TIMEP",$strTimeGroupMessage);
if ($strTimeGroupMessage == $LANG['admincontent']['timeperiodsok']) {
	$conttp->setVariable("TIMEP_MSGCLASS","okmessage");
} else {
	$conttp->setVariable("TIMEP_MSGCLASS","dbmessage");
}
$conttp->parse("main");
$conttp->show("main");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>