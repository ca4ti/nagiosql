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
// Component : Installer main script
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
$preContent	= "templates/index.tpl.htm";
$preEncode	= 'utf-8';
$preLocale 	= "../config/locale";
$filConfig  = "../config/settings.php";
$preDBType  = "mysql";
$strLangOpt = "";
$strVersion = "3.3.0";
$intUpdate  = 0;
$intError   = 0;
//
// Include preprocessing file
// ==========================
require("functions/prepend_install.php");
//
// Restart session
// ===============
session_destroy();
session_start([ 'name' => 'nagiosql_install']);
//
// POST parameters
// ===============
$arrLocale	= array("zh_CN","de_DE","da_DK","en_GB","fr_FR","it_IT","ja_JP","nl_NL","pl_PL","pt_BR","ru_RU","es_ES");
$chkLocale 	= (isset($_POST['selLanguage']) && in_array($_POST['selLanguage'],$arrLocale)) ? $_POST['selLanguage'] : "no";
//
// Language settings
// =================
if (extension_loaded('gettext')) {
	if ($chkLocale == "no") {
		if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2) == "de") {
			$chkLocale = 'de_DE';
		} else {
			$chkLocale = 'en_GB';
		}
	}
	putenv("LC_ALL=".$chkLocale.".".$preEncode);
	putenv("LANG=".$chkLocale.".".$preEncode);
	// GETTEXT domain
	setlocale(LC_ALL, $chkLocale.".".$preEncode);
	bindtextdomain($chkLocale, $preLocale);
	bind_textdomain_codeset($chkLocale, $preEncode);
	textdomain($chkLocale);
	$arrTemplate['NAGIOS_FAQ'] = $myInstClass->translate("Online Documentation");
	// Language selection field
	$arrTemplate['LANGUAGE']   = $myInstClass->translate("Language");
	foreach($myInstClass->getLangData() AS $key => $elem) {
		$strLangOpt .= "<option value='".$key."' {sel}>".$myInstClass->getLangNameFromCode($key,false)."</option>\n";
		if ($key != $chkLocale) { $strLangOpt = str_replace(" {sel}","",$strLangOpt); } else { $strLangOpt = str_replace(" {sel}"," selected",$strLangOpt); }
	}
	$arrTemplate['LANG_OPTION'] = $strLangOpt;
} else {
	$intError 			 = 1;
	$strErrorMessage 	.= "Installation cannot continue, please make sure you have the php-gettext extension loaded!";
}
//
// Checking current installation
// =============================
// Does the settings file exist?
if (file_exists($filConfig) && is_readable($filConfig)) {
	$preSettings = parse_ini_file($filConfig,true);
	// Are there any connection data?
	if (isset($preSettings['db']) && isset($preSettings['db']['server']) && isset($preSettings['db']['port']) &&
	    isset($preSettings['db']['database']) && isset($preSettings['db']['username']) && isset($preSettings['db']['password'])) {
		// Copy settings to session
		$_SESSION['SETS'] = $preSettings;
		// Existing postgres database?
		if (isset($preSettings['db']['dbtype']) && ($preSettings['db']['dbtype'] == "postgres")) {
			$preDBType = "pgsql";
			$_SESSION['install']['dbtype'] = $preDBType;
		}
		// Select database
		$intDBFallback = 0;
		if (isset($preSettings['db']['type'])) {
			if ($preSettings['db']['type'] == "mysqli") {
				if (extension_loaded('mysqli')) {
					// Include mysqli class
					include("../functions/mysqli_class.php");
					// Initialize mysqli class
					$myDBClass = new mysqlidb;
				} else {
					$intDBFallback = 1;
				}
				$_SESSION['install']['dbtype'] = 'mysqli';
			} else {
				$intDBFallback = 1;
			}
		} else {
			if (extension_loaded('mysqli')) {
				// Include mysql class
				include("../functions/mysqli_class.php");
				// Initialize mysql class
				$myDBClass = new mysqlidb;
			} else {
				$intDBFallback = 1;
			}
			$_SESSION['install']['dbtype'] = 'mysqli';
			$preSettings['db']['type']     = 'mysqli';
		}
		// Set DB parameters
		$myDBClass->arrParams['server']		= $preSettings['db']['server'];
		$myDBClass->arrParams['port']		= $preSettings['db']['port'];
		$myDBClass->arrParams['username']	= $preSettings['db']['username'];
		$myDBClass->arrParams['password']	= $preSettings['db']['password'];
		$myDBClass->arrParams['database']	= $preSettings['db']['database'];
		$myDBClass->getdatabase();
		// DB failure
		if ($intDBFallback == 1) {
			$_SESSION['install']['dbtype'] = 'mysqli';
			$preSettings['db']['type']   = 'mysqli';
			$intUpdate 					   = 0;
		} else {
			if ($myDBClass->error == true) {
				$strErrorMessage .= $myInstClass->translate("Database connection failed. Upgrade not available!")."<br>";
				$strErrorMessage .= str_replace("::","<br>",$myDBClass->strErrorMessage)."<br>";
			} else {
				$strSQL    = "SELECT category,name,value FROM tbl_settings";
				$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
				if ($booReturn == false) {
					$strErrorMessage .= $myInstClass->translate("Settings table not available or wrong. Upgrade not available!")."<br>";
					$strErrorMessage .= str_replace("::","<br>",$myDBClass->strErrorMessage)."<br>";
																												  
				} else if ($intDataCount != 0) {
					foreach ($arrDataLines AS $elem) {
						$preSettings[$elem['category']][$elem['name']] = $elem['value'];
					}
					$intUpdate = 1;
				}
			}
		}
	} else {
		$strErrorMessage .= $myInstClass->translate("Database values in settings file are missing (config/settings.php). Upgrade not available!");
	}
} else {
	$strErrorMessage .= $myInstClass->translate("Settings file not found or not readable (config/settings.php). Upgrade not available!");
}
//
// Initial settings (new installation)
// ===================================
$filInit = "functions/initial_settings.php";
if (file_exists($filInit) && is_readable($filInit)) {
	$preInit = parse_ini_file($filInit,true);
	$_SESSION['init_settings'] = $preInit;
} else {
	$strErrorMessage .= $myInstClass->translate("Default values file is not available or not readable (install/functions/initial_settings.php). Installation possible, but without predefined data!");
}
//
// Build content
// =============
$arrTemplate['PAGETITLE'] 		= "[NagiosQL] ".$myInstClass->translate("Installation wizard");
$arrTemplate['MAIN_TITLE']  	= $myInstClass->translate("Welcome to the NagiosQL installation wizard");
$arrTemplate['TEXT_PART_1'] 	= $myInstClass->translate("This wizard will help you to install and configure NagiosQL.");
$arrTemplate['TEXT_PART_2'] 	= $myInstClass->translate("For questions please visit").": ";
$arrTemplate['TEXT_PART_3']		= $myInstClass->translate("First let's check your local environment and find out if everything NagiosQL needs is available.");
$arrTemplate['TEXT_PART_4']		= $myInstClass->translate("The basic requirements are:");
$arrTemplate['TEXT_PART_5']		= $myInstClass->translate("PHP 5.2.0 or greater including:");
$arrTemplate['TEXT_PHP_REQ_1']	= $myInstClass->translate("PHP database module:")." ".
								  $myInstClass->translate("supported types are")." <b>mysqli</b>";
$arrTemplate['TEXT_PHP_REQ_2']	= $myInstClass->translate("PHP module:")." <b>session</b>";
$arrTemplate['TEXT_PHP_REQ_3']	= $myInstClass->translate("PHP module:")." <b>gettext</b>";
$arrTemplate['TEXT_PHP_REQ_6']	= $myInstClass->translate("PHP module:")." <b>filter</b>";
$arrTemplate['TEXT_PHP_REQ_8']	= $myInstClass->translate("PHP module:")." <b>FTP</b> ".$myInstClass->translate("(optional)");
$arrTemplate['TEXT_PHP_REQ_10']	= $myInstClass->translate("PECL extension:")." <b>SSH</b> ".$myInstClass->translate("(optional)");
$arrTemplate['TEXT_PART_6']		= $myInstClass->translate("php.ini options").":";
$arrTemplate['TEXT_INI_REQ_1']	= $myInstClass->translate("file_uploads on (for upload features)");
$arrTemplate['TEXT_INI_REQ_2']	= $myInstClass->translate("session.auto_start needs to be off");
$arrTemplate['TEXT_PART_7']		= $myInstClass->translate("A database server");
$arrTemplate['TEXT_PART_8']		= $myInstClass->translate("Nagios 2.x/3.x/4.x");
$arrTemplate['TEXT_PART_9']		= $myInstClass->translate("NagiosQL version")." ".$strVersion;
$arrTemplate['LOCALE']			= $chkLocale;
$arrTemplate['ONLINE_DOC'] 		= $myInstClass->translate("Online documentation");
//
// New installation or upgrade
// ===========================
$arrTemplate['NEW_INSTALLATION'] = $myInstClass->translate("START INSTALLATION");
$arrTemplate['UPDATE'] 			 = $myInstClass->translate("START UPDATE");
$arrTemplate['DISABLE_NEW'] 	 = "";
$arrTemplate['UPDATE_ERROR']   	 = "<font style=\"color:red;\">".$strErrorMessage."</font>";
if ($intUpdate == 1) {
	$arrTemplate['DISABLE_UPDATE'] 	= "";
} else {
	$arrTemplate['DISABLE_UPDATE'] 	= "disabled=\disabled\"";
}
if ($intError == 1) {
	$arrTemplate['DISABLE_NEW'] 	= "disabled=\disabled\"";
	$arrTemplate['DISABLE_UPDATE'] 	= "disabled=\disabled\"";
}
//
// Write content
// =============
$strContent = $myInstClass->parseTemplate($arrTemplate,$preContent);
echo $strContent;
?>