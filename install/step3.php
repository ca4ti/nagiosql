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
// Component : Installer script - step 2
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2012-02-23 11:44:55 +0100 (Thu, 23 Feb 2012) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1239 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Prevent this file from direct access
// ====================================
if(preg_match('#' . basename(__FILE__) . '#', htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'utf-8'))) {
  exit;
}
//
// Define common variables
// =======================
$preIncludeContent	= "templates/step3.tpl.htm";
$intError 			= 0;

if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get")) {
 @date_default_timezone_set(@date_default_timezone_get());
}
//
// Build content
// =============
$arrTemplate['STEP1_BOX'] 		= translate('Requirements');
$arrTemplate['STEP2_BOX']		= translate($_SESSION['install']['mode']);
$arrTemplate['STEP3_BOX'] 		= translate('Finish');
$arrTemplate['STEP3_TITLE'] 	= "NagiosQL ".translate($_SESSION['install']['mode']).": ".translate("Finishing Setup");
$arrTemplate['INST_VISIBLE'] 	= "showfield";
$arrTemplate['STEP4_SUB_TITLE']	= translate("Deploy NagiosQL settings");
$arrTemplate['STEP3_TEXT_01']	= translate("Database server connection (privileged user)");
$arrTemplate['STEP3_TEXT_03']	= translate("Database server version");
$arrTemplate['STEP3_TEXT_05']	= translate("Database server support");
$arrTemplate['STEP3_TEXT_07']	= translate("Delete existing NagiosQL database");
$arrTemplate['STEP3_TEXT_09']	= translate("Creating new database");
$arrTemplate['STEP3_TEXT_11']	= translate("Create NagiosQL database user");
$arrTemplate['STEP3_TEXT_13']	= translate("Installing NagiosQL database tables");
$arrTemplate['STEP3_TEXT_15']	= translate("Set initial NagiosQL Administrator");
$arrTemplate['STEP3_TEXT_17']	= translate("Database server connection (NagiosQL user)");
$arrTemplate['STEP4_TEXT_01']	= translate("Writing global settings to database");
$arrTemplate['STEP4_TEXT_03']	= translate("Writing database configuration to settings.php");
$arrTemplate['STEP4_TEXT_05']	= translate("Import Nagios sample data");
$arrTemplate['STEP4_TEXT_07']	= translate("Create and/or store NagiosQL path settings");

$arrTemplate['STEP4_VISIBLE'] 	   = "hidefield";
$arrTemplate['STEP3_TEXT_03_SHOW'] = "hidefield";
$arrTemplate['STEP3_TEXT_05_SHOW'] = "hidefield";
$arrTemplate['STEP3_TEXT_07_SHOW'] = "hidefield";
$arrTemplate['STEP3_TEXT_09_SHOW'] = "hidefield";
$arrTemplate['STEP3_TEXT_11_SHOW'] = "hidefield";
$arrTemplate['STEP3_TEXT_13_SHOW'] = "hidefield";
$arrTemplate['STEP3_TEXT_15_SHOW'] = "hidefield";
$arrTemplate['STEP3_TEXT_17_SHOW'] = "hidefield";
$arrTemplate['STEP4_TEXT_03_SHOW'] = "hidefield";
$arrTemplate['STEP4_TEXT_05_SHOW'] = "hidefield";
$arrTemplate['STEP4_TEXT_07_SHOW'] = "hidefield";
//
// Doing installation/upgrade
// ==========================
if ($_SESSION['install']['mode'] == "Update") {
	$arrTemplate['STEP3_SUB_TITLE']	= translate("Updating existing NagiosQL database");
	if ($_SESSION['install']['dbtype'] == "mysql") {
		// Check database connection
		if ($intError == 0)	$intError = $myInstClass->openAdmDBSrv($arrTemplate['STEP3_TEXT_02'],$strErrorMessage);
		if ($intError == 0)	$intError = $myInstClass->openDatabase($arrTemplate['STEP3_TEXT_02'],$strErrorMessage);
		// Check NagiosQL version
		if ($intError == 0)	{
			$arrTemplate['STEP3_TEXT_03'] 		= translate("Installed NagiosQL version");
			$arrTemplate['STEP3_TEXT_03_SHOW'] 	= "showfield";
			$intError = $myInstClass->checkQLVersion($arrTemplate['STEP3_TEXT_04'],$strErrorMessage,$arrUpdate,$setQLVersion);
		}
		// Upgrade NagiosQL DB
		if ($intError == 0) {
			$arrTemplate['STEP3_TEXT_05'] 		= translate("Upgrading from version")." ".$setQLVersion." ".translate("to")." ".$preNagiosQL_ver;
			$arrTemplate['STEP3_TEXT_05_SHOW'] 	= "showfield";
			$intError = $myInstClass->updateQLDB($arrTemplate['STEP3_TEXT_06'],$strErrorMessage,$arrUpdate);
		}
		// Converting database to UTF8
		if ($intError == 0) {
			$arrTemplate['STEP3_TEXT_07'] 		= translate("Converting database to utf8 character set");
			$arrTemplate['STEP3_TEXT_07_SHOW'] 	= "showfield";
			$intError = $myInstClass->convQLDB($arrTemplate['STEP3_TEXT_08'],$strErrorMessage);
		}
		// Converting database tables to UTF8
		if ($intError == 0) {
			$arrTemplate['STEP3_TEXT_09'] 		= translate("Converting database tables to utf8 character set");
			$arrTemplate['STEP3_TEXT_09_SHOW'] 	= "showfield";
			$intError = $myInstClass->convQLDBTables($arrTemplate['STEP3_TEXT_10'],$strErrorMessage);
		}
		// Converting database fields to UTF8
		if ($intError == 0) {
			$arrTemplate['STEP3_TEXT_11'] 		= translate("Converting database fields to utf8 character set");
			$arrTemplate['STEP3_TEXT_11_SHOW'] 	= "showfield";
			$intError = $myInstClass->convQLDBFields($arrTemplate['STEP3_TEXT_12'],$strErrorMessage);
		}
		// Reconnect Database with new user
		if ($intError == 0) {
			$arrTemplate['STEP3_TEXT_17_SHOW'] = "showfield";
			$intError = $myInstClass->openAdmDBSrv($arrTemplate['STEP3_TEXT_18'],$strErrorMessage,1);
			$intError = $myInstClass->openDatabase($arrTemplate['STEP3_TEXT_18'],$strErrorMessage,1);
		}
		// Deploy NagiosQL database settings
		if ($intError == 0) {
			$arrTemplate['STEP4_VISIBLE'] = "showfield";
			$intError = $myInstClass->updateSettingsDB($arrTemplate['STEP4_TEXT_02'],$strErrorMessage);
		}	
		// Write database settings to file
		if ($intError == 0) {
			$arrTemplate['STEP4_TEXT_03_SHOW'] = "showfield";
			$intError = $myInstClass->updateSettingsFile($arrTemplate['STEP4_TEXT_04'],$strErrorMessage);
		}
	}
} else {
	$arrTemplate['STEP3_SUB_TITLE']	= translate("Create new NagiosQL database");
	// Check database connection
	$intOldDBStatus = 0;
	if ($intError == 0)	$intError = $myInstClass->openAdmDBSrv($arrTemplate['STEP3_TEXT_02'],$strErrorMessage);
	if ($intError == 0)	{
		$intOldDBStatus = $myInstClass->openDatabase($arrTemplate['STEP3_TEXT_02'],$strErrorMessage);
		if (($intOldDBStatus == 0) && ($_SESSION['install']['dbdrop'] == 0)) {
				$strErrorMessage .=	translate("Database already exists and drop database was not selected, please correct or manage manually")."<br>";
				$arrTemplate['STEP3_TEXT_02'] = "<span class=\"red\">".translate("failed")."</span>";
				$intError = 1;				
		} else {
			$arrTemplate['STEP3_TEXT_02'] = "<span class=\"green\">".translate("passed")."</span>";
		}
	}
	// Check database version
	if ($intError == 0) {
		$arrTemplate['STEP3_TEXT_03_SHOW'] = "showfield";	
		$arrTemplate['STEP3_TEXT_05_SHOW'] = "showfield";
		$intError = $myInstClass->checkDBVersion($arrTemplate['STEP3_TEXT_06'],$strErrorMessage,$strVersion);
		if ($strVersion == "unknown") {
			$arrTemplate['STEP3_TEXT_04'] = "<span class=\"red\">".translate("unknown")."</span>";
		} else {
			$arrTemplate['STEP3_TEXT_04'] = "<span class=\"green\">".$strVersion."</span>";
		}
	}
	// Drop existing database
	if (($intError == 0) && ($_SESSION['install']['dbdrop'] == 1) && ($intOldDBStatus == 0)) {
		$arrTemplate['STEP3_TEXT_07_SHOW'] = "showfield";
		$intError = $myInstClass->dropDB($arrTemplate['STEP3_TEXT_08'],$strErrorMessage);
	}
	// Create new database
	if ($intError == 0) {
		$arrTemplate['STEP3_TEXT_09_SHOW'] = "showfield";
		$intError = $myInstClass->createDB($arrTemplate['STEP3_TEXT_10'],$strErrorMessage);
	}
	// Grant NagiosQL database user
	if ($intError == 0) {
		$arrTemplate['STEP3_TEXT_11_SHOW'] = "showfield";
		$intError = $myInstClass->grantDBUser($arrTemplate['STEP3_TEXT_12'],$strErrorMessage);
	}
	// Write initial SQL data to database
	if ($intError == 0) $intError = $myInstClass->openDatabase($arrTemplate['STEP3_TEXT_02'],$strErrorMessage);
	if ($intError == 0) {
		$arrTemplate['STEP3_TEXT_13_SHOW'] = "showfield";
		$arrInsert[] = $preSqlNewInstall;
		$intError = $myInstClass->updateQLDB($arrTemplate['STEP3_TEXT_14'],$strErrorMessage,$arrInsert);
	}
	// Create NagiosQL admin user
	if ($intError == 0) {
		$arrTemplate['STEP3_TEXT_15_SHOW'] = "showfield";
		$intError = $myInstClass->createNQLAdmin($arrTemplate['STEP3_TEXT_16'],$strErrorMessage);
	}
	// Reconnect Database with new user
	if ($intError == 0) {
		$arrTemplate['STEP3_TEXT_17_SHOW'] = "showfield";
		if ($intError == 0)	$intError = $myInstClass->openAdmDBSrv($arrTemplate['STEP3_TEXT_18'],$strErrorMessage,1);
		if ($intError == 0)	$intError = $myInstClass->openDatabase($arrTemplate['STEP3_TEXT_18'],$strErrorMessage,1);
	}
	// Deploy NagiosQL settings
	if ($intError == 0) {
		$arrTemplate['STEP4_VISIBLE'] = "showfield";
		$intError = $myInstClass->updateSettingsDB($arrTemplate['STEP4_TEXT_02'],$strErrorMessage);
	}	
	// Write database settings to file
	if ($intError == 0) {
		$arrTemplate['STEP4_TEXT_03_SHOW'] = "showfield";
		$intError = $myInstClass->updateSettingsFile($arrTemplate['STEP4_TEXT_04'],$strErrorMessage);
	}
	// Write sample data to database
	if (($intError == 0) && ($_SESSION['install']['sample'] == 1)) {
		$arrTemplate['STEP4_TEXT_05_SHOW'] = "showfield";
		$arrSample[] = "sql/import_nagios_sample.sql";
		$intError = $myInstClass->updateQLDB($arrTemplate['STEP4_TEXT_06'],$strErrorMessage,$arrSample);
	}
	// Create NagiosQL path and write path settings to the database
	if ($intError == 0) {
		$arrTemplate['STEP4_TEXT_07_SHOW'] = "showfield";
		$intError = $myInstClass->updateQLpath($arrTemplate['STEP4_TEXT_08'],$strErrorMessage);
	}
}
if ($intError != 0) {
	$arrTemplate['ERRORMESSAGE']  = "<p style=\"color:#F00;margin-top:0px;font-weight:bold;\">".$strErrorMessage."</p>\n";
	$arrTemplate['INFO_TEXT']	  = "";
	$arrTemplate['BUTTON']	  	  = "<div id=\"install-back\">\n";
	$arrTemplate['BUTTON']	  	 .= "<input type='hidden' name='hidStep' id='hidStep' value='2' />\n";
	$arrTemplate['BUTTON']	  	 .= "<input type='image' src='images/previous.png' value='Submit' alt='Submit' /><br />".translate("Back")."\n";
	$arrTemplate['BUTTON']	  	 .= "</div>\n";
} else {
	$arrTemplate['ERRORMESSAGE']  = "";
	$arrTemplate['INST_VISIBLE']  = "showfield";
	$arrTemplate['INFO_TEXT']	  = translate("Please delete the install directory to continue!");
	$arrTemplate['BUTTON']	  	  = "<div id=\"install-next\">\n";
	$arrTemplate['BUTTON']	  	 .= "<a href='../index.php'><img src='images/next.png' alt='finish' title='finish' border='0' /></a><br />".translate("Finish")."\n";
	$arrTemplate['BUTTON']	  	 .= "</div>\n";
}
//
// Write content
// =============
$strContent = $myInstClass->parseTemplate($arrTemplate,$preIncludeContent);
echo $strContent;
?>