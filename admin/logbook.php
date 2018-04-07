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
// Component : Admin logbook
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-03-13 14:00:26 +0100 (So, 13. Mär 2011) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.1.1
// Revision  : $LastChangedRevision: 1058 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$intMain    	= 7;
$intSub     	= 21;
$intMenu    	= 2;
$preContent 	= "admin/admin_master.tpl.htm";
$intError   	= 0;
$strMessage 	= "";
//
// Include preprocessing file
// ==========================
$preAccess    	= 1;
$preFieldvars 	= 1;
require("../functions/prepend_adm.php");
//
// Process post parameters
// =======================
$chkFromLine  	= isset($_GET['from_line'])   ? filter_var($_GET['from_line'], FILTER_SANITIZE_NUMBER_INT)  : 0;
$chkDelFrom   	= isset($_POST['txtFrom'])    ? htmlspecialchars($_POST['txtFrom'], ENT_QUOTES, 'utf-8')    : "";
$chkDelTo     	= isset($_POST['txtTo'])      ? htmlspecialchars($_POST['txtTo'], ENT_QUOTES, 'utf-8')  	: "";
$chkSearch    	= isset($_POST['txtSearch'])  ? htmlspecialchars($_POST['txtSearch'], ENT_QUOTES, 'utf-8') 	: "";
//
// Delete log entries
// ==================
if (isset($_POST['txtFrom']) && (($chkDelFrom != "") || ($chkDelTo != ""))) {
  	$strWhere = "";
  	if ($chkDelFrom != "") {
    	$strWhere 	.= "AND `time` > '$chkDelFrom 00:00:00'";
  	}
  	if ($chkDelTo != "") {
    	$strWhere .= "AND `time` < '$chkDelTo 23:59:59'";
  	}
  	$strSQL  	= "DELETE FROM `tbl_logbook` WHERE 1=1 $strWhere";
  	$booReturn  = $myDBClass->insertData($strSQL);
  	if ($booReturn == false) {
    	$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
    	$intError = 1;
  	} else {
    	$strMessage .= translate('Dataset successfully deleted. Affected rows:')." ".$myDBClass->intAffectedRows;
  	}
}
//
// Search data
// ===========
if ($chkSearch != "") {
  	$strWhere = "WHERE `user` LIKE '%$chkSearch%' OR `ipadress` LIKE '%$chkSearch%' OR `domain` LIKE '%$chkSearch%' OR `entry` LIKE '%$chkSearch%'";
} else {
  	$strWhere = "";
}
//
// Get data
// ========
$intNumRows = $myDBClass->getFieldData("SELECT count(*) FROM `tbl_logbook` $strWhere");
$strSQL     = "SELECT DATE_FORMAT(time,'%Y-%m-%d %H:%i:%s') AS `time`, `user`, `ipadress`, `domain`, `entry`
         	   FROM `tbl_logbook` $strWhere ORDER BY `time` DESC LIMIT $chkFromLine,".$SETS['common']['pagelines'];
$booReturn  = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
if ($booReturn == false) {
  	$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
  	$intError 	 = 1;
}
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Start content
// =============
$conttp->setVariable("TITLE",translate('View logbook'));
foreach($arrDescription AS $elem) {
  	$conttp->setVariable($elem['name'],$elem['string']);
}
$conttp->setVariable("LANG_ENTRIES_BEFORE",translate('Delete logentries between:'));
$conttp->setVariable("LOCALE",$SETS['data']['locale']);
$conttp->setVariable("LANG_SELECT_DATE",translate('Please at least fill in a start or a stop time'));
$conttp->setVariable("LANG_DELETELOG",translate('Do you really want to delete all log entries between the selected dates?'));
$conttp->setVariable("DAT_SEARCH",$chkSearch);
// Legende einblenden
if ($chkFromLine > 1) {
  	$intPrevNumber = $chkFromLine - 20;
  	$conttp->setVariable("LANG_PREVIOUS", "<a href=\"".filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING)."?from_line=".$intPrevNumber."\"><< ".translate('previous 20 entries')."</a>");
} else {
  	$conttp->setVariable("LANG_PREVIOUS", "");
}
if ($chkFromLine < $intNumRows-20) {
  	$intNextNumber = $chkFromLine + 20;
  	$conttp->setVariable("LANG_NEXT", "<a href=\"".filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING)."?from_line=".$intNextNumber."\">".translate('next 20 entries')." >></a>");
} else {
  	$conttp->setVariable("LANG_NEXT", "");
}
// 
// Output log data
// ===============
if ($intDataCount != 0) {
  	for ($i=0;$i<$intDataCount;$i++) {
		// Set default values
		if ($arrDataLines[$i]['ipadress'] == "") $arrDataLines[$i]['ipadress'] = "&nbsp;";
		// Insert data values
		$conttp->setVariable("DAT_TIME", $arrDataLines[$i]['time']);
		$conttp->setVariable("DAT_ACCOUNT", $arrDataLines[$i]['user']);
		$conttp->setVariable("DAT_ACTION", $arrDataLines[$i]['entry']);
		$conttp->setVariable("DAT_IPADRESS", $arrDataLines[$i]['ipadress']);
		$conttp->setVariable("DAT_DOMAIN", $arrDataLines[$i]['domain']);
		$conttp->parse("logdatacell");
  	}
}
if ($strMessage != "") {
  	if ($intError == 1) {
    	$conttp->setVariable("LOGDBMESSAGE",$strMessage);
  	} else {
    	$conttp->setVariable("OKDATA",$strMessage);
  	}
}
$conttp->parse("logbooksite");
$conttp->show("logbooksite");
//
// Process footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>