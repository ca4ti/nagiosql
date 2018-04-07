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
$preIncludeContent	= "templates/step2.tpl.htm";
$intError 			= 0;
//
// Build content
// =============
$arrTemplate['PASSWD_MESSAGE'] 	= translate('The NagiosQL first passwords are not equal!');
$arrTemplate['FIELDS_MESSAGE'] 	= translate('Please fill in all fields marked with an *');
$arrTemplate['STEP1_BOX'] 		= translate('Requirements');
$arrTemplate['STEP2_BOX']		= translate($_SESSION['install']['mode']);
$arrTemplate['STEP3_BOX'] 		= translate('Finish');
$arrTemplate['STEP2_TITLE'] 	= "NagiosQL ".translate($_SESSION['install']['mode']).": ".translate("Setup");
$arrTemplate['STEP2_TEXT1_1'] 	= translate("Please complete the form below. Mandatory fields marked <em>*</em>");
$arrTemplate['STEP2_TEXT2_1'] 	= translate("Database Configuration");
$arrTemplate['STEP2_TEXT2_2'] 	= translate("Database Type");
$arrTemplate['STEP2_VALUE2_2'] 	= htmlspecialchars($_SESSION['install']['dbtype'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT2_3'] 	= translate("Database Server");
$arrTemplate['STEP2_VALUE2_3'] 	= htmlspecialchars($_SESSION['install']['dbserver'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT2_4'] 	= translate("Local hostname or IP address");
if (htmlspecialchars($_SESSION['install']['dbserver'], ENT_QUOTES, 'utf-8') == "localhost") {
	$arrTemplate['STEP2_VALUE2_4'] 	= htmlspecialchars($_SESSION['install']['dbserver'], ENT_QUOTES, 'utf-8');
}else {
	$arrTemplate['STEP2_VALUE2_4'] 	= $_SERVER['SERVER_ADDR'];
}
$arrTemplate['STEP2_TEXT2_5'] 	= translate("Database Server Port");
$arrTemplate['STEP2_VALUE2_5'] 	= htmlspecialchars($_SESSION['install']['dbport'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT2_6'] 	= translate("Database name");
$arrTemplate['STEP2_VALUE2_6'] 	= htmlspecialchars($_SESSION['install']['dbname'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT2_7'] 	= translate("NagiosQL DB User");
$arrTemplate['STEP2_VALUE2_7'] 	= htmlspecialchars($_SESSION['install']['dbuser'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT2_8'] 	= translate("NagiosQL DB Password");
$arrTemplate['STEP2_VALUE2_8'] 	= htmlspecialchars($_SESSION['install']['dbpass'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT2_9'] 	= translate("Administrative Database User");
$arrTemplate['STEP2_VALUE2_9'] 	= htmlspecialchars($_SESSION['install']['admuser'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT2_10'] 	= translate("Administrative Database Password");
$arrTemplate['STEP2_TEXT2_11'] 	= translate("Drop database if already exists?");
if ($_SESSION['install']['dbdrop'] == 1) {$arrTemplate['STEP2_VALUE2_11'] = "checked";} else {$arrTemplate['STEP2_VALUE2_11'] = "";}
$arrTemplate['STEP2_TEXT3_1'] 	= translate("NagiosQL User Setup");
$arrTemplate['STEP2_TEXT3_2'] 	= translate("Initial NagiosQL User");
$arrTemplate['STEP2_VALUE3_2'] 	= htmlspecialchars($_SESSION['install']['qluser'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT3_3'] 	= translate("Initial NagiosQL Password");
$arrTemplate['STEP2_VALUE3_3'] 	= htmlspecialchars($_SESSION['install']['qlpass'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT3_4'] 	= translate("Please repeat the password");
$arrTemplate['STEP2_TEXT4_1'] 	= translate("Nagios Configuration");
$arrTemplate['STEP2_TEXT4_2'] 	= translate("Import Nagios sample config?");
if ($_SESSION['install']['sample'] == 1) {$arrTemplate['STEP2_VALUE4_2'] = "checked";} else {$arrTemplate['STEP2_VALUE4_2'] = "";}
$arrTemplate['STEP2_FORM_1'] 	= translate("Next");
$arrTemplate['STEP2_TEXT5_1'] 	= translate("NagiosQL path values");
$arrTemplate['STEP2_TEXT5_2'] 	= translate("Create NagiosQL config paths?");
if ($_SESSION['install']['createpath'] == 1) {$arrTemplate['STEP2_VALUE5_2'] = "checked";} else {$arrTemplate['STEP2_VALUE5_2'] = "";}
$arrTemplate['STEP2_TEXT5_3'] 	= translate("NagiosQL config path");
$arrTemplate['STEP2_VALUE5_3'] 	= htmlspecialchars($_SESSION['install']['qlpath'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT5_4'] 	= translate("Nagios config path");
$arrTemplate['STEP2_VALUE5_4'] 	= htmlspecialchars($_SESSION['install']['nagpath'], ENT_QUOTES, 'utf-8');
$arrTemplate['STEP2_TEXT5_5'] 	= translate("Both path values were stored in your configuration target settings for localhost.");
$arrTemplate['STEP2_TEXT5_6'] 	= translate("If you select the create path option, be sure that the NagiosQL base path exist and the webserver demon has write access to it. So the installer will create the required subdirectories in your localhost's filesystem (hosts, services, backup etc.)");
$arrTemplate['INSTALL_FIELDS'] 	= "";

//
// Setting some template values to blank
// =====================================
$arrTemplate['STEP2_TEXT1_2'] = "";

//
// Conditional checks
// =======================
if ($_SESSION['install']['mode'] == "Update") {
	$arrTemplate['STEP2_TEXT1_2'] = "<p style=\"color:red;\"><b>".translate("Please backup your database before proceeding!")."</b></p>\n";
	$arrTemplate['INST_VISIBLE'] = "hidefield";
} else {
	$arrTemplate['INSTALL_FIELDS'] 	= ",tfDBprivUser,tfDBprivPass,tfQLuser,tfQLpass";
	$arrTemplate['INST_VISIBLE'] = "showfield";
}

//
// Write content
// =============
$strContent = $myInstClass->parseTemplate($arrTemplate,$preIncludeContent);
echo $strContent;
?>