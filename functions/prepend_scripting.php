<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2012 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Preprocessing script for scripting files
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2012-02-29 09:54:45 +0100 (Wed, 29 Feb 2012) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1262 $
//
///////////////////////////////////////////////////////////////////////////////
error_reporting(E_ALL);
//error_reporting(E_ERROR);
//
// Security Protection
// ===================
if (isset($_GET['SETS']) || isset($_POST['SETS'])) {
	$SETS = "";
}
//
// Timezone settings (>=PHP5.1)
// ============================
if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get")) {
	@date_default_timezone_set(@date_default_timezone_get());
}
//
// Define common variables
// =======================
$chkDomainId  = 0;
$intError 	  = 0;
//
// Define path constants
//
define('BASE_PATH', str_replace("functions","",dirname(__FILE__)));
//
// Read settings file
// ==================
$preBasePath = str_replace("scripts","",getcwd());
$preIniFile  = $preBasePath.'config/settings.php';
//
// Read file settings
// ==================
$SETS = parse_ini_file($preIniFile,true);
//
// Include external function/class files - part 1
// ==============================================
include("mysql_class.php");
//
// Initialize classes - part 1
// ===========================
$myDBClass = new mysqldb;
$myDBClass->arrSettings = $SETS;
$myDBClass->getDatabase($SETS['db']);
if ($myDBClass->error == true) {
  	echo str_replace("::","\n","Error while connecting to database: ".$myDBClass->strErrorMessage);
  	$intError 	 = 1;
}
//
// Get additional configuration from the table tbl_settings
// ========================================================
if ($intError == 0) {
	$strSQL    = "SELECT `category`,`name`,`value` FROM `tbl_settings`";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	if ($booReturn == false) {
  		echo str_replace("::","\n","Error while selecting data from database: ".$myDBClass->strErrorMessage);
		$intError 	 = 1;
	} else if ($intDataCount != 0) {
  		for ($i=0;$i<$intDataCount;$i++) {
    		$SETS[$arrDataLines[$i]['category']][$arrDataLines[$i]['name']] = $arrDataLines[$i]['value'];
  		}
	}
} else {
	echo "Could not load configuration settings from database - abort\n";
	exit;
}
//
// Include external function/class files
// =====================================
include("translator.php");
include("data_class.php");
include("config_class.php");
include("import_class.php");
require_once($preBasePath.'libraries/pear/HTML/Template/IT.php');
//
// Initialize classes
// ==================
$myDataClass   = new nagdata;
$myConfigClass = new nagconfig;
$myImportClass = new nagimport;
//
// Propagating the classes themselves
// ==================================
$myDataClass->myDBClass   		=& $myDBClass;
$myDataClass->myConfigClass 	=& $myConfigClass;
$myConfigClass->myDBClass 		=& $myDBClass;
$myConfigClass->myDataClass 	=& $myDataClass;
$myImportClass->myDataClass   	=& $myDataClass;
$myImportClass->myDBClass   	=& $myDBClass;
$myImportClass->myConfigClass 	=& $myConfigClass;
//
// Set class variables
// ===================
$myDataClass->arrSettings 	= $SETS;
$myConfigClass->arrSettings = $SETS;
?>