<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2007 by Martin Willisegger / nagiosql_v2@wizonet.ch
//
// Projekt:	NagiosQL Application
// Website: http://www.nagiosql.org
// Datum:   17.08.2007
// Author :	Martin Willisegger/Rouven Homann
// Zweck:	Testscript
// Version: 2.0.2 (Internal)
// SV:      $Id: testQL.php 72 2008-04-03 07:01:46Z rouven $
//
///////////////////////////////////////////////////////////////////////////////
?>
<style type="text/css">
<!--
.normal {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: x-small;
	}
.red {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: x-small;
	color:#FF0000
	}
-->
</style>
<?php
ini_set("display_errors","On");
error_reporting(E_ALL);
// 
// Menuvariabeln für diese Seite
// =============================
$intMain 		= 1;
$intSub  		= 0;
$intMenu 		= 1;

//
// Vorgabedatei einbinden
// ======================
$preNoLogin = true;
$SETS 		= parse_ini_file("config/settings.ini",TRUE);
// Update Warnung
if (!isset($SETS['path']['physical']) || !isset($SETS['path']['protocol'])) {
	echo "<b>Please update the [path] section of your settings.ini file as described in update.txt!</b><br>";
	exit;
}
//
// Pfade prüfen
// ============
echo "<font class=\"normal\">";
echo "<h4><b>Welcome to NagiosQL Installation Test</b></h4>\n";

//
// Session und MySQL Support in PHP überprüfen
//
echo "<h5><b>PHP requirements</b></h5>\n";
$lext = get_loaded_extensions();
if(in_array("mysql",$lext)) {
	echo "PHP mysql module loaded.<br>";
	$strMySQL = "ok";
} else {
	echo "<span class=\"red\">PHP without mysql support, please install mysql module to php!</span><br>";
	$strMySQL = "nak";
}
if(in_array("session",$lext)) {
	echo "PHP session module loaded.<br>";
} else {
	echo "<span class=\"red\">PHP without session support, please install session module to php!</span><br><br>";
}

if ($strMySQL == "ok") {
	echo "<h5><b>MySQL Database requirements</b></h5>\n";
	@mysql_connect($SETS['db']['server'],$SETS['db']['username'],$SETS['db']['password']);
	if (mysql_error() == "") {
		echo "MySQL connection to server ok.<br>";
	} else {
		echo "<span class=\"red\">MySQL connection to server failed, please check settings!<br>MySQL says: ".mysql_error()."</span><br><br>";
	}
	@mysql_select_db($SETS['db']['database']);
	if (mysql_error() == "") {
		echo "MySQL connection to database ok.<br>";
	} else {
		echo "<span class=\"red\">MySQL connection to database failed, please check settings!<br>MySQL says: ".mysql_error()."</span><br><br>";
	}
	$resQuery = mysql_query("SELECT * FROM tbl_language");
	if ($resQuery && (mysql_num_rows($resQuery) != 0) && (mysql_error() == "")) {
		echo "MySQL getting data from database ok.<br>";
	} else {
		echo "<span class=\"red\">MySQL getting data from database failed, please check the NagiosQL database installation!<br>MySQL says: ".mysql_error()."</span><br><br>";
	}
}
echo "<h5><b>Webserver paths</b></h5>\n";
if (!file_exists($SETS['path']['physical']."functions/prepend_adm.php")) {
  	echo "<span class=\"red\">Parameter wrong in settings.ini: section \"[path]\", parameter \"physical\"!</span><br>";
  	$strPath = str_replace("testQL.php","",$_SERVER['SCRIPT_FILENAME']); 
  	echo "<span class=\"red\"> -> If you do not use the Apache \"Alias\" function, this parameter should be set to: \"$strPath\" - now it is set to \"".$SETS['path']['physical']."\"<br>";
} else {
  	echo "Parameter in settings.ini: section \"[path]\", parameter \"physical\" seems to be correct!<br>";
}
if (str_replace("testQL.php","",$_SERVER['REQUEST_URI']) != $SETS['path']['root']) {
  	echo "<span class=\"red\">Parameter wrong in settings.ini: section \"[path]\", parameter \"root\"!</span><br>";
  	$strPath = str_replace("testQL.php","",$_SERVER['REQUEST_URI']); 
  	echo "<span class=\"red\"> -> This parameter should be set to: \"$strPath\" - now it is set to \"".$SETS['path']['root']."\"</span><br>";
} else {
  	echo "Parameter in settings.ini: section \"[path]\", parameter \"root\" seems to be correct!";
}

echo "<br><h5><b>Templates</b></h5>\n";

if (file_exists($SETS['path']['physical']."/templates/main.tpl.htm") && is_readable($SETS['path']['physical']."/templates/main.tpl.htm")) {
	echo "Main template file found and readable<br>";
} else {
	echo "<span class=\"red\">Main template file not found or not readable: ".$SETS['path']['physical']."/templates/main.tpl.htm</span><br>";
}
if (file_exists($SETS['path']['physical']."/templates/admin/admin_master.tpl.htm") && is_readable($SETS['path']['physical']."/templates/admin/admin_master.tpl.htm")) {
	echo "Admin template file found and readable<br>";
} else {
	echo "<span class=\"red\">Admin template file not found or not readable: ".$SETS['path']['physical']."/templates/admin/admin_master.tpl.htm</span><br>";
}
//
// PEAR testen
// ===========
$incpath = strtoupper(ini_get("include_path"));
if (substr_count($incpath,"PEAR") == 0) {
	echo "<span class=\"red\">PEAR extension is probably not installed</span><br>";
} else {
	echo "PEAR extension seems to be installed<br>";
}
echo "<span class=\"red\">";
include($SETS['path']['IT']);
$testtp = new HTML_Template_IT($SETS['path']['physical']."/templates/"); 
echo "</span><br>";
if (!$testtp->loadTemplatefile("main.tpl.htm", true, false)) {
	echo "<span class=\"red\">Can not load main template</span><br>";
} else {
	echo "Main template successfully loaded<br>";
}
$testtp2 = new HTML_Template_IT($SETS['path']['physical']."/templates/"); 
if (!$testtp2->loadTemplatefile("admin/admin_master.tpl.htm", true, false)) {
	echo "<span class=\"red\">Can not load admin template</span><br>";
} else {
	echo "Admin template successfully loaded<br>";
}

$testtp->setVariable("LANGUAGE","de");
$testtp->parse("header");
$test1 = $testtp->get("header");
$testtp2->parse();
$test2 = $testtp2->get();
if ($test1 == "") {
	echo "<span class=\"red\">Can not parse main template</span><br>";
} else {
	echo "Main template successfully parsed<br>";
}
if ($test2 == "") {
	echo "<span class=\"red\">Can not parse admin template</span><br>";
} else {
	echo "Admin template successfully parsed<br><br>";
}
$testGPC = ini_get("magic_quotes_gpc");
if ($testGPC == 1) {
	echo "php.ini configuration for magic_quotes_gpc is correct<br>";
} else {
	echo "<span class=\"red\">php.ini configuration for magic_quotes_gpc is not correct -> change it to \"magic_quotes_gpc = On\"</span><br>";
}
echo "<br><h5><b>Style sheet</b></h5>\n";
if (file_exists($SETS['path']['physical']."/config/main.css") && is_readable($SETS['path']['physical']."/config/main.css")) {
	echo "CSS file found and readable<br>";
} else {
	echo "<span class=\"red\">CSS file not found or not readable: ".$SETS['path']['physical']."/config/main.css</span><br>";
}
$strURL = $SETS['path']['protocol']."://".$_SERVER['HTTP_HOST'].$SETS['path']['root']."config/main.css";
if (fopen($strURL,"r")) {
	echo "CSS file via HTTP found and readable<br>";
} else {
	echo "<span class=\"red\">CSS file via HTTP not found or not readable: ".$SETS['path']['physical']."/config/main.css</span><br>";
	echo "<span class=\"red\"><b>This is not an error if you have enabled HTTP authorization (Statuscode 401)!</b></span><br>";
}
echo "<br><h5><b>Nagios configuration directories</b></h5>\n";
if (!file_exists($SETS['nagios']['binary'])) {
  	echo "<span class=\"red\">Nagios Binary not found - parameter wrong in settings.ini: section \"[nagios]\", parameter \"binary\"!</span><br>";
} else {
	if (is_executable($SETS['nagios']['binary'])) {
  		echo "Nagios Binary found and executable.<br>";
	} else {
		echo "<span class=\"red\">Nagios Binary found but not executable, please check permissions!</span><br>";
	}
}
if (!file_exists($SETS['nagios']['cmdfile'])) {
  	echo "<span class=\"red\">Nagios Command File not found - parameter wrong in settings.ini: section \"[nagios]\", parameter \"cmdfile\"!</span><br>";
} else {
	if (is_writable($SETS['nagios']['cmdfile'])) {
  		echo "Nagios Command File found and writeable.<br>";
	} else {
		echo "<span class=\"red\">Nagios Command File found but not writeable, please check permissions!</span><br>";
	}
}
if (!file_exists($SETS['nagios']['config'])) {
  	echo "<span class=\"red\">Nagios Configuration Directory not found - parameter wrong in settings.ini: section \"[nagios]\", parameter \"config\"!</span><br>";
} else {
	if (is_writable($SETS['nagios']['config'])) {
  		echo "Nagios Configuration Directory found and writeable.<br>";
	} else {
		echo "<span class=\"red\">Nagios Configuration Directory but not writeable, please check permissions!</span><br>";
	}
}
if (!file_exists($SETS['nagios']['confighosts'])) {
  	echo "<span class=\"red\">Nagios Hosts Configuration Directory not found - parameter wrong in settings.ini: section \"[nagios]\", parameter \"confighosts\"!</span><br>";
} else {
	if (is_writable($SETS['nagios']['confighosts'])) {
  		echo "Nagios Hosts Configuration Directory found and writeable.<br>";
	} else {
		echo "<span class=\"red\">Nagios Hosts Configuration Directory but not writeable, please check permissions!</span><br>";
	}
}
if (!file_exists($SETS['nagios']['configservices'])) {
  	echo "<span class=\"red\">Nagios Services Configuration Directory not found - parameter wrong in settings.ini: section \"[nagios]\", parameter \"configservices\"!</span><br>";
} else {
	if (is_writable($SETS['nagios']['configservices'])) {
  		echo "Nagios Services Configuration Directory found and writeable.<br>";
	} else {
		echo "<span class=\"red\">Nagios Services Configuration Directory but not writeable, please check permissions!</span><br>";
	}
}
if (!file_exists($SETS['nagios']['backup'])) {
  	echo "<span class=\"red\">Nagios Backup Directory not found - parameter wrong in settings.ini: section \"[nagios]\", parameter \"backup\"!</span><br>";
} else {
	if (is_writable($SETS['nagios']['backup'])) {
  		echo "Nagios Backup Directory found and writeable.<br>";
	} else {
		echo "<span class=\"red\">Nagios Backup Directory but not writeable, please check permissions!</span><br>";
	}
}
if (!file_exists($SETS['nagios']['backuphosts'])) {
  	echo "<span class=\"red\">Nagios Hosts Backup Directory not found - parameter wrong in settings.ini: section \"[nagios]\", parameter \"backuphosts\"!</span><br>";
} else {
	if (is_writable($SETS['nagios']['backuphosts'])) {
  		echo "Nagios Hosts Backup Directory found and writeable.<br>";
	} else {
		echo "<span class=\"red\">Nagios Hosts Backup Directory but not writeable, please check permissions!</span><br>";
	}
}
if (!file_exists($SETS['nagios']['backupservices'])) {
  	echo "<span class=\"red\">Nagios Services Backup Directory not found - parameter wrong in settings.ini: section \"[nagios]\", parameter \"backupservices\"!</span><br>";
} else {
	if (is_writable($SETS['nagios']['backupservices'])) {
  		echo "Nagios Services Backup Directory found and writeable.<br>";
	} else {
		echo "<span class=\"red\">Nagios Services Backup Directory but not writeable, please check permissions!</span><br>";
	}
}
echo "<font>";
?>