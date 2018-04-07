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
// Component : Timeperiod definitions
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2012-03-09 07:43:00 +0100 (Fri, 09 Mar 2012) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1282 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$prePageId			= 16;
$preContent   		= "admin/timeperiods.tpl.htm";
$preSearchSession	= 'timeperiod';
$preTableName		= 'tbl_timeperiod';
$preKeyField		= 'timeperiod_name';
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
	$strSQLx = "`$preTableName` SET `$preKeyField`='$chkTfValue1', `alias`='$chkTfValue2', `exclude`=$intMselValue1, `use_template`=$intMselValue2,
              	`name`='$chkTfValue3', $preSQLCommon1";
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
				if ($chkModus  == "insert") $myDataClass->writeLog(translate('New time period inserted:')." ".$chkTfValue1);
				if ($chkModus  == "modify") $myDataClass->writeLog(translate('Time period modified:')." ".$chkTfValue1);
				//
				// Insert/update relations
				// =======================
				if ($chkModus == "insert") {
					if ($intMselValue1 != 0) $intRet1 = $myDataClass->dataInsertRelation("tbl_lnkTimeperiodToTimeperiod",$chkDataId,$chkMselValue1);
					if (isset($intRet1) && ($intRet1 != 0)) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
					if ($intMselValue2 != 0) $intRet2 = $myDataClass->dataInsertRelation("tbl_lnkTimeperiodToTimeperiodUse",$chkDataId,$chkMselValue2);
					if (isset($intRet2) && ($intRet2 != 0)) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
				} else if ($chkModus == "modify") {
					if ($intMselValue1 != 0) {
						$intRet1 = $myDataClass->dataUpdateRelation("tbl_lnkTimeperiodToTimeperiod",$chkDataId,$chkMselValue1);
					} else {
						$intRet1 = $myDataClass->dataDeleteRelation("tbl_lnkTimeperiodToTimeperiod",$chkDataId);
					}
					if ($intRet1 != 0) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
					if ($intMselValue2 != 0) {
						$intRet2 = $myDataClass->dataUpdateRelation("tbl_lnkTimeperiodToTimeperiodUse",$chkDataId,$chkMselValue2);
					} else {
						$intRet2 = $myDataClass->dataDeleteRelation("tbl_lnkTimeperiodToTimeperiodUse",$chkDataId);
					}
					if ($intRet2 != 0) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
				}
				if (($intRet1 + $intRet2) != 0) $strInfoMessage = "";
				//
				// Insert/update time defintions
				// =============================
				if ($chkModus == "modify") {
					$strSQL   	= "DELETE FROM `tbl_timedefinition` WHERE `tipId`=$chkDataId";
					$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
					if ($booReturn == false) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
				}
				if (isset($_SESSION['timedefinition']) && is_array($_SESSION['timedefinition']) && (count($_SESSION['timedefinition']) != 0)) {
					foreach($_SESSION['timedefinition'] AS $elem) {
						if ($elem['status'] == 0) {
							if ($elem['definition'] != "use") {
								$elem['range'] = str_replace(" ","",$elem['range']);
							}
							$strSQL 	= "INSERT INTO `tbl_timedefinition` (`tipId`,`definition`,`range`,`last_modified`)
										   VALUES ($chkDataId,'".$elem['definition']."','".$elem['range']."',now())";
							$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
							if ($booReturn == false) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
						}
					}
				}
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
$conttp->setVariable("TITLE",translate('Timeperiod definitions'));
$conttp->parse("header");
$conttp->show("header");
//
// Singe data form
// ===============
if ($chkModus == "add") {
	// Do not show modified time list
	$intNoTime = 1;
  	// Process exclude selection fields
  	if (isset($arrModifyData['exclude'])) {$intFieldId = $arrModifyData['exclude'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectMulti($preTableName,'name','excludes','tbl_lnkTimeperiodToTimeperiod',0,$intFieldId,$chkListId);
	if  ($intReturn != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
  	// Process include selection fields
  	if (isset($arrModifyData['use_template'])) {$intFieldId = $arrModifyData['use_template'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectMulti($preTableName,'name','uses','tbl_lnkTimeperiodToTimeperiodUse',0,$intFieldId,$chkListId);
	if  ($intReturn != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
  	// Process acces group selection field
  	if (isset($arrModifyData['access_group'])) {$intFieldId = $arrModifyData['access_group'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectSimple('tbl_group','groupname','acc_group',0,$intFieldId);
	if  ($intReturn != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
	// Initial add/modify form definitions
	$myContentClass->addFormInit($conttp);
	if ($intDataWarning == 1) 	$conttp->setVariable("WARNING",$strDBWarning."<br>".translate('Saving not possible!'));
	if ($intVersion != 3) 		$conttp->setVariable("VERSION_20_VALUE_MUST","mselValue1,");
	$conttp->setVariable("LANG_INSERT_ALL_TIMERANGE",translate('Please insert a time definition and a time range'));
	// Insert data from database in "modify" mode
	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		// Check relation information to find out locked configuration datasets
		$intLocked = $myDataClass->infoRelation($preTableName,$arrModifyData['id'],$preKeyField);
		$myVisClass->processMessage($myDataClass->strInfoMessage,$strRelMessage);
		$strInfo  = "<br><span class=\"redmessage\">".translate('Entry cannot be activated because it is used by another configuration').":</span>";
		$strInfo .= "<br><span class=\"greenmessage\">".$strRelMessage."</span>";
		// Process data
		$myContentClass->addInsertData($conttp,$arrModifyData,$intLocked,$strInfo);
		$conttp->setVariable("TIP_ID",$arrModifyData['id']);
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
	$mastertp->setVariable("FIELD_1",translate('Time period'));
	$mastertp->setVariable("FIELD_2",translate('Description'));
  	// Process filter string
  	if ($_SESSION['search'][$preSearchSession] != "") {
  		$strSearchTxt   = $_SESSION['search'][$preSearchSession];
  		$strSearchWhere = "AND (`$preKeyField` LIKE '%".$strSearchTxt."%' OR `alias` LIKE '%".$strSearchTxt."%' OR `name` LIKE '%".$strSearchTxt."%')";
  	}
	// Row sorting
	$strOrderString = "ORDER BY `config_id`, `$preKeyField` $hidSortDir";
	if ($hidSortBy == 2) $strOrderString = "ORDER BY `config_id`, `alias` $hidSortDir";
  	// Count datasets
  	$strSQL    = "SELECT count(*) AS `number` FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
  	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
  	if ($booReturn == false) {
    	$myVisClass->processMessage(translate('Error while selecting data from database:'),$strErrorMessage);
		$myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
  	} else {
    	$intLineCount = (int)$arrDataLinesCount['number'];
		if ($intLineCount < $chkLimit) $chkLimit = 0;
  	}
  	// Get datasets
  	$strSQL    = "SELECT `id`, `$preKeyField`, `alias`, `register`, `active`, `config_id`, `access_group` FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere 
				  AND `access_group` IN ($strAccess) $strOrderString LIMIT $chkLimit,".$SETS['common']['pagelines'];
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	if ($booReturn == false) {
    	$myVisClass->processMessage(translate('Error while selecting data from database:'),$strErrorMessage);
		$myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
  	}
	// Process data
	$myContentClass->listData($mastertp,$arrDataLines,$intDataCount,$intLineCount,$preKeyField,'alias');
}
// Show messages
$myContentClass->showMessages($mastertp,$strErrorMessage,$strInfoMessage,$strConsistMessage,$arrTimeData,$strTimeInfoString,$intNoTime);
//
// Process footer
// ==============
$myContentClass->showFooter($maintp,$setFileVersion);
?>