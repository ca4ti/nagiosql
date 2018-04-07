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
// Component : Command definitions
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
$prePageId			= 18;
$preContent   		= "admin/checkcommands.tpl.htm";
$preSearchSession	= 'checkcommand';
$preTableName		= 'tbl_command';
$preKeyField		= 'command_name';
$preAccess    		= 1;
$preFieldvars 		= 1;
//
// Include preprocessing files
// ===========================
require("../functions/prepend_adm.php");
require("../functions/prepend_content.php");
// 
// Add or modify data
// ==================
if ((($chkModus == "insert") || ($chkModus == "modify")) && ($intGlobalWriteAccess == 0)) {
	$strSQLx = "`$preTableName` SET `$preKeyField`='$chkTfValue1', `command_line`='$chkTfValue2', `command_type`=$chkSelValue1, $preSQLCommon1";
	if ($chkModus == "insert") {
		$strSQL 		= "INSERT INTO ".$strSQLx;
  	} else {
    	$strSQL			= "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
  	}
	if ($intWriteAccessId == 0) {
		if (($chkTfValue1 != "") && ($chkTfValue2 != "")) {
			$intReturn = $myDataClass->dataInsert($strSQL,$intInsertId);
			if ($chkModus == "insert")  $chkDataId = $intInsertId;
			if ($intReturn == 1) {
				$myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
			} else {
				$myVisClass->processMessage($myDataClass->strInfoMessage,$strInfoMessage);
				$myDataClass->updateStatusTable($preTableName);
				if ($chkModus  == "insert") $myDataClass->writeLog(translate('New command inserted:')." ".$chkTfValue1);
				if ($chkModus  == "modify") $myDataClass->writeLog(translate('Command modified:')." ".$chkTfValue1);
			}
		} else {
			$myVisClass->processMessage(translate('Database entry failed! Not all necessary data filled in!'),$strErrorMessage);
		}
	} else {
		$myVisClass->processMessage(translate('Database entry failed! No write access!'),$strErrorMessage);
	}
  	$chkModus = "display";
}
if ($chkModus != "add") $chkModus    = "display"; 
//
// Get date/time of last database and config file manipulation
// ===========================================================
$intReturn = $myConfigClass->lastModifiedFile($preTableName,$arrTimeData,$strTimeInfoString);
if ($intReturn != 0) $myVisClass->processMessage($myConfigClass->strErrorMessage,$strErrorMessage); 
//
// Start content
// =============
$conttp->setVariable("TITLE",translate('Command definitions'));
$conttp->parse("header");
$conttp->show("header");
//
// Singe data form
// ===============
if ($chkModus == "add") {
	// Do not show modified time list
	$intNoTime = 1;
  	// Process access group selection field
  	if (isset($arrModifyData['access_group'])) {$intFieldId = $arrModifyData['access_group'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectSimple('tbl_group','groupname','acc_group',0,$intFieldId);
	if ($intReturn != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
	// Initial add/modify form definitions
	$myContentClass->addFormInit($conttp);
	if ($intDataWarning == 1) 	$conttp->setVariable("WARNING",$strDBWarning."<br>".translate('Saving not possible!'));
	if ($intVersion != 3) 		$conttp->setVariable("VERSION_20_VALUE_MUST","mselValue1,");
	$conttp->setVariable("NO_TYPE",translate('unclassified'));
	$conttp->setVariable("CHECK_TYPE",translate('check command'));
	$conttp->setVariable("MISC_TYPE",translate('misc command'));
	// Insert data from database in "modify" mode
	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		// Check relation information to find out locked configuration datasets
		$intLocked = $myDataClass->infoRelation($preTableName,$arrModifyData['id'],$preKeyField);
		$myVisClass->processMessage($myDataClass->strInfoMessage,$strRelMessage);
		$strInfo  = "<br><span class=\"redmessage\">".translate('Entry cannot be activated because it is used by another configuration').":</span>";
		$strInfo .= "<br><span class=\"greenmessage\">".$strRelMessage."</span>";
		// Process data
		$myContentClass->addInsertData($conttp,$arrModifyData,$intLocked,$strInfo);
		// Insert command type
		if ($arrModifyData['command_type'] == 1) {$conttp->setVariable("CHECK_TYPE_SELECTED","selected");}
		if ($arrModifyData['command_type'] == 2) {$conttp->setVariable("MISC_TYPE_SELECTED","selected");}		
  	}
  	$conttp->parse("datainsert");
  	$conttp->show("datainsert");
}
//
// List view
// ==========
if ($chkModus == "display") {
	// Initial list view definitions
	$myContentClass->listViewInit($mastertp);
	$mastertp->setVariable("FIELD_1",translate('Command name'));
	$mastertp->setVariable("FIELD_2",translate('Command line'));
	// Process search string
 	if ($_SESSION['search'][$preSearchSession] != "") {
  		$strSearchTxt   = $_SESSION['search'][$preSearchSession];
  		$strSearchWhere = "AND (`$preKeyField` LIKE '%".$strSearchTxt."%' OR `command_line` LIKE '%".$strSearchTxt."%')";
  	}
	// Row sorting
	$strOrderString = "ORDER BY `config_id`, `$preKeyField` $hidSortDir";
	if ($hidSortBy == 2) $strOrderString = "ORDER BY `config_id`, `command_line` $hidSortDir";
  	// Count datasets
	$strSQL    = "SELECT count(*) AS `number` FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {
		$myVisClass->processMessage(translate('Error while selecting data from database:'));
		$myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
	} else {
    	$intLineCount = (int)$arrDataLinesCount['number'];
		if ($intLineCount < $chkLimit) $chkLimit = 0;
	}
  	// Get datasets
	$strSQL    = "SELECT `id`, `$preKeyField`, `command_line`, `register`, `active`, `config_id`, `access_group` FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere
          		  AND `access_group` IN ($strAccess) $strOrderString LIMIT $chkLimit,".$SETS['common']['pagelines'];
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	if ($booReturn == false) {
		$myVisClass->processMessage(translate('Error while selecting data from database:'));
		$myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
  	}
	// Process data
	$myContentClass->listData($mastertp,$arrDataLines,$intDataCount,$intLineCount,$preKeyField,'command_line',40);
}
// Show messages
$myContentClass->showMessages($mastertp,$strErrorMessage,$strInfoMessage,$strConsistMessage,$arrTimeData,$strTimeInfoString,$intNoTime);
//
// Process footer
// ==============
$myContentClass->showFooter($maintp,$setFileVersion);
?>