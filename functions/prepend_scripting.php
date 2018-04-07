<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2011 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Preprocessing script for scripting files
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-03-13 14:00:26 +0100 (So, 13. Mär 2011) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.1.1
// Revision  : $LastChangedRevision: 1058 $
//
///////////////////////////////////////////////////////////////////////////////
//error_reporting(E_ALL);
error_reporting(E_ERROR);
//
// Timezone settings (>=PHP5.1)
// ============================
if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get")) {
	@date_default_timezone_set(@date_default_timezone_get());
}
//
// Define common variables
// =======================
$strMessage   = "";
$chkDomainId  = 0;
$intError 	  = 0;
//
// Define path constants
//
define('BASE_PATH', str_replace("functions","",dirname(__FILE__)));
//
// Read settings file
// ==================
function parseIniFile($iIniFile) {
	$aResult  =
  	$aMatches = array();
  	$a = &$aResult;
  	$s = '\s*([[:alnum:]_\- \*]+?)\s*'; 
	preg_match_all('#^\s*((\['.$s.'\])|(("?)'.$s.'\\5\s*=\s*("?)(.*?)\\7))\s*(;[^\n]*?)?$#ms', @file_get_contents($iIniFile), $aMatches, PREG_SET_ORDER);
  	foreach ($aMatches as $aMatch) {
    	if (empty($aMatch[2])) {
        	$a [$aMatch[6]] = $aMatch[8];
      	} else {  
			$a = &$aResult [$aMatch[3]];
		}
  	}
  	return $aResult;
}
// Read database configuration from settings.php
$SETS = parseIniFile(BASE_PATH .'config/settings.php');
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
  	echo str_replace("<br>","\n","Error while connecting to database: ".$myDBClass->strDBError);
  	$intError 	 = 1;
}
//
// Get additional configuration from the table tbl_settings
// ========================================================
if ($intError == 0) {
	$strSQL    = "SELECT `category`,`name`,`value` FROM `tbl_settings`";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	if ($booReturn == false) {
  		echo str_replace("<br>","\n","Error while selecting data from database: ".$myDBClass->strDBError);
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
if (!isset($SETS['path']['physical'])) {
	$SETS['path']['physical']	= BASE_PATH;
}
//
// Include external function/class files
// =====================================
include("data_class.php");
include("config_class.php");
require_once(BASE_PATH .'libraries/pear/HTML/Template/IT.php');
//
// Initialize classes
// ==================
$myDataClass   = new nagdata;
$myConfigClass = new nagconfig;
//
// Propagating the classes themselves
// ==================================
$myDataClass->myDBClass   	=& $myDBClass;
$myDataClass->myConfigClass =& $myConfigClass;
$myConfigClass->myDBClass 	=& $myDBClass;
$myConfigClass->myDataClass =& $myDataClass;
//
// Set class variables
// ===================
$myDataClass->arrSettings 	= $SETS;
$myConfigClass->arrSettings = $SETS;
?>