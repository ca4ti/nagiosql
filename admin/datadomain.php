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
// Component : Admin domain administration
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-12-01 15:20:17 +0100 (Do, 01. Dez 2011) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1137 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$prePageId			= 35;
$preContent   		= "admin/datadomain.tpl.htm";
$preTableName		= 'tbl_datadomain';
$preKeyField		= 'domain';
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
	if ($chkTfValue1 == 'common') $chkSelValue1 = 0;
	$strSQLx = "`$preTableName` SET `$preKeyField`='$chkTfValue1', `alias`='$chkTfValue2', `targets`=$chkSelValue1, `version`=$chkSelValue2, 
				`access_group`=$chkSelAccGr, `enable_common`=$chkSelValue3, `active`='$chkActive',
				`last_modified`=NOW()"; 
	if ($chkModus == "insert") {
		$strSQL 		= "INSERT INTO ".$strSQLx;
  	} else {
    	$strSQL			= "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
  	}
	if ($intWriteAccessId == 0) {
		if (($chkTfValue1 != "") && ($chkTfValue2 != "") && (($chkTfValue1 == 'common') || ($chkSelValue1 != 0))) {
			$intReturn = $myDataClass->dataInsert($strSQL,$intInsertId);
			if ($chkModus == "insert")  $chkDataId = $intInsertId;
			if ($intReturn == 1) {
				$myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
			} else {
				$myVisClass->processMessage($myDataClass->strInfoMessage,$strInfoMessage);
				if ($chkModus == "insert") $myDataClass->writeLog(translate('New Domain inserted:')." ".$chkTfValue1);
				if ($chkModus == "modify") $myDataClass->writeLog(translate('Domain modified:')." ".$chkTfValue1);
			}
		} else {
			$myVisClass->processMessage(translate('Database entry failed! Not all necessary data filled in!'),$strErrorMessage);
		}
	} else {
		$myVisClass->processMessage(translate('Database entry failed! No write access!'),$strErrorMessage);
	}
	$chkModus = "display";
}
if ($chkModus != "add") $chkModus = "display"; 
//
// Start content
// =============
$conttp->setVariable("TITLE",translate('Data domain administration'));
$conttp->parse("header");
$conttp->show("header");
//
// Single view
// ===========
if ($chkModus == "add") {
	// Process configuration target selection fields
  	$intReturn = 0;
  	if (isset($arrModifyData['targets'])) {$intFieldId = $arrModifyData['targets'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectSimple('tbl_configtarget','target','target',0,$intFieldId);
  	if ($intReturn != 0) {
		$myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
		$myVisClass->processMessage(translate('Attention, no configuration targets defined!'),$strDBWarning);
		$intDataWarning = 1;
	}
  	// Process acces group selection field
  	if (isset($arrModifyData['access_group'])) {$intFieldId = $arrModifyData['access_group'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectSimple('tbl_group','groupname','acc_group',0,$intFieldId);
	if ($intReturn != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
	// Initial add/modify form definitions
	$myContentClass->addFormInit($conttp);
	if ($intDataWarning == 1) $conttp->setVariable("WARNING",$strDBWarning."<br>".translate('Saving not possible!'));
	$conttp->setVariable("FILL_ALLFIELDS",translate('Please fill in all fields marked with an *'));
	$conttp->setVariable("FILL_ILLEGALCHARS",translate('The following field contains not permitted characters:'));
	$conttp->setVariable("ENABLE",translate('Enable'));
	$conttp->setVariable("DISABLE",translate('Disable'));
	// Insert data from database in "modify" mode
	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		// Process data
		$myContentClass->addInsertData($conttp,$arrModifyData,0,'');
    	// Nagios version
		if ($arrModifyData['version'] == 1) $conttp->setVariable("VER_SELECTED_1","selected");
		if ($arrModifyData['version'] == 2) $conttp->setVariable("VER_SELECTED_2","selected");
		if ($arrModifyData['version'] == 3) $conttp->setVariable("VER_SELECTED_3","selected");
		// Enable common domain
		if ($arrModifyData['enable_common'] == 0) $conttp->setVariable("ENA_COMMON_SELECTED_0","selected");
		if ($arrModifyData['enable_common'] == 1) $conttp->setVariable("ENA_COMMON_SELECTED_1","selected");
		// Domain localhost cant' be renamed
    	if ($arrModifyData['domain'] == "localhost") {
      		$conttp->setVariable("DOMAIN_DISABLE","readonly");
      		$conttp->setVariable("LOCKCLASS","inputlock");
		} else if ($arrModifyData['domain'] == "common") {
      		$conttp->setVariable("DOMAIN_DISABLE","readonly");
			$conttp->setVariable("COMMON_INVISIBLE","class=\"elementHide\"");
      		$conttp->setVariable("LOCKCLASS","inputlock");
    	} else {
			$conttp->setVariable("CHECK_TARGETS",",selValue1");
    	}
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
	$mastertp->setVariable("FIELD_1",translate('Data domain'));
	$mastertp->setVariable("FIELD_2",translate('Description'));
	// Row sorting
	$strOrderString = "ORDER BY `domain` $hidSortDir";
	if ($hidSortBy == 2) $strOrderString = "ORDER BY `alias` $hidSortDir";
  	// Count datasets
  	$strSQL    = "SELECT count(*) AS `number` FROM `$preTableName` WHERE `access_group` IN ($strAccess)";
  	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
  	if ($booReturn == false) {
    	$myVisClass->processMessage(translate('Error while selecting data from database:'),$strErrorMessage);
		$myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
  	} else {
    	$intLineCount = (int)$arrDataLinesCount['number'];
		if ($intLineCount < $chkLimit) $chkLimit = 0;
  	}
  	// Get datasets
  	$strSQL    = "SELECT `id`, `domain`, `alias`, `active`, `nodelete`, `access_group`  FROM `$preTableName` WHERE `access_group` IN ($strAccess)
				  $strOrderString LIMIT $chkLimit,".$SETS['common']['pagelines'];
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	if ($booReturn == false) {
    	$myVisClass->processMessage(translate('Error while selecting data from database:'),$strErrorMessage);
		$myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
  	}
	// Process data
	$myContentClass->listData($mastertp,$arrDataLines,$intDataCount,$intLineCount,$preKeyField,'alias');
}
// Show messages
$myContentClass->showMessages($mastertp,$strErrorMessage,$strInfoMessage,$strConsistMessage,'','',1);
//
// Process footer
// ==============
$myContentClass->showFooter($maintp,$setFileVersion);
?>