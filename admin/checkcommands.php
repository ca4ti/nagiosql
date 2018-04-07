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
// Component : Command definitions
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
$intMain 		= 4;
$intSub  		= 4;
$intMenu        = 2;
$preContent     = "admin/checkcommands.tpl.htm";
$strDBWarning 	= "";
$intCount		= 0;
//
// Include preprocessing file
// ==========================
$preAccess    	= 1;
$preFieldvars	= 1;
require("../functions/prepend_adm.php");
//
// Process post parameters
// =======================
$chkTfSearch    	= isset($_POST['txtSearch'])		? htmlspecialchars($_POST['txtSearch'], ENT_QUOTES, 'utf-8')			: "";
$chkInsName 		= isset($_POST['tfName']) 			? $_POST['tfName'] 				: "";
$chkInsCommand 		= isset($_POST['tfCommand']) 		? $_POST['tfCommand'] 			: "";
$chkInsType 		= isset($_POST['selCommandType']) 	? $_POST['selCommandType'] 		: 0;
$chkSelAccessGroup	= isset($_POST['selAccessGroup'])	? $_POST['selAccessGroup']+0	: 0;
//
// Search/Filter - Session data
// ============================
if (!isset($_SESSION['search']) || !isset($_SESSION['search']['checkcommand'])) $_SESSION['search']['checkcommand'] = "";
if (($chkModus == "checkform") || ($chkModus == "filter")) {
  $_SESSION['search']['checkcommand'] = $chkTfSearch;
}
//
// Quote special characters
// ========================
if (get_magic_quotes_gpc() == 0) {
  	$chkTfSearch	= addslashes($chkTfSearch);
	$chkInsName 	= addslashes($chkInsName);
	$chkInsCommand  = addslashes($chkInsCommand);
}
// 
// Add or modify data
// ==================
if (($chkModus == "insert") || ($chkModus == "modify")) {
	if ($hidActive   == 1) $chkActive = 1;
	if ($chkGroupAdm == 1) 		{$strGroupSQL 	= "`access_group`=$chkSelAccessGroup, ";} 	else {$strGroupSQL 	= "";}
	if ($chkModus == "insert") 	{$strDomain 	= "`config_id`=$chkDomainId, ";} 			else {$strDomain 	= "";}
	$strSQLx = "`tbl_command` SET `command_name`='$chkInsName', `command_line`='$chkInsCommand', `command_type`=$chkInsType,
				$strGroupSQL `active`='$chkActive', `config_id`=$chkDomainId, `last_modified`=NOW()";
	if ($chkModus == "insert") {
		$strSQL = "INSERT INTO ".$strSQLx; 
	} else {
		$strSQL = "UPDATE ".$strSQLx." WHERE id=$chkDataId";   
	}	
	if (($chkInsName != "") && ($chkInsCommand != "")) {
		$intInsert 	 = $myDataClass->dataInsert($strSQL,$intInsertId);
		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
		$myDataClass->updateStatusTable("tbl_command");
		if ($chkModus == "insert") $chkDataId = $intInsertId;
		if ($intInsert == 1) {  
			$intReturn = 1;
		} else {
			if ($chkModus  == "insert") $myDataClass->writeLog(translate('New command inserted:')." ".$chkInsName);
			if ($chkModus  == "modify") $myDataClass->writeLog(translate('Command modified:')." ".$chkInsName);
			$intReturn = 0;
		}
	} else {
		$myVisClass->processMessage(translate('Database entry failed! Not all necessary data filled in!'),$strMessage);
	}
	$chkModus = "display";
} else if ($chkModus == "make") {
	// Write configuration file
	$intReturn   = $myConfigClass->createConfig("tbl_command",0);
	$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "info")) {
	// Display additional relation information
	$myDataClass->infoRelation("tbl_command",$chkListId,"command_name");
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$intReturn   = 0;
	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Delete selected datasets
	$intReturn   = $myDataClass->dataDeleteFull("tbl_command",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);	
	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Copy selected datasets
	$intReturn 	 = $myDataClass->dataCopyEasy("tbl_command","command_name",$chkListId,$chkSelTargetDomain);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus  	 = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "activate")) {
	// Activate selected datasets
	$intReturn   = $myDataClass->dataActivate("tbl_command",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "deactivate")) {
	// Deactivate selected datasets
	$intReturn   = $myDataClass->dataDeactivate("tbl_command",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus    = "display"; 
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Open a dataset to modify
	$booReturn   = $myDBClass->getSingleDataset("SELECT * FROM tbl_command WHERE id=".$chkListId,$arrModifyData);
	$myVisClass->processMessage($myDBClass->strDBError,$strMessage);
	if ($booReturn == false) {
		$myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError,$strMessage);
		$chkModus    = "add";
	} else {
		// Check access permission
		$intAccess = $myVisClass->checkAccGroup($_SESSION['userid'],$arrModifyData['access_group']);  
		if ($intAccess == 1) {
			$myVisClass->processMessage(translate('No permission to open configuration!'),$strMessage);
	  		$arrModifyData  = "";
	 		$chkModus       = "display";
		} else {
	  		$chkModus 	  	= "add";	
		}
	}
} else if ($chkModus != "add") {
  $chkModus    = "display"; 
}
// Get status messages from database
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $strMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$strMessage."</span>";
//
// Get date/time of last database and config file manipulation
// ===========================================================
$myConfigClass->lastModified("tbl_command",$strLastModified,$strFileDate,$strOld);
$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage); 
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
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
  	// Process access group selection field
  	if (isset($arrModifyData['access_group'])) {$intFieldId = $arrModifyData['access_group'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectSimple('tbl_group','groupname','acc_group',0,$intFieldId);
	// Process template text raplacements
	foreach($arrDescription AS $elem) {
		$conttp->setVariable($elem['name'],str_replace("</","<\/",$elem['string']));
	}
	$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
	$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	$conttp->setVariable("LIMIT",$chkLimit);
	if ($strDBWarning != "") $conttp->setVariable("WARNING",$strDBWarning.translate('Saving not possible!'));
	$conttp->setVariable("ACT_CHECKED","checked");
	$conttp->setVariable("MODUS","insert");
	$conttp->setVariable("VERSION",$intVersion);
	$conttp->setVariable("RELATION_CLASS","elementHide");
	$conttp->setVariable("NO_TYPE",translate('unclassified'));
	$conttp->setVariable("CHECK_TYPE",translate('check command'));
	$conttp->setVariable("MISC_TYPE",translate('misc command'));
	if ($chkGroupAdm == 0) $conttp->setVariable("RESTRICT_GROUP_ADMIN","class=\"elementHide\"");
  	// Insert data from database in "modify" mode
	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		foreach($arrModifyData AS $key => $value) {
			if (($key == "active") || ($key == "last_modified") || ($key == "access_rights")) continue;
			$conttp->setVariable("DAT_".strtoupper($key),htmlentities($value,ENT_QUOTES,'UTF-8'));
		}
		if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
    	// Check relation information to find out locked configuration datasets
		if ($myDataClass->infoRelation("tbl_command",$arrModifyData['id'],"command_name") != 0) {
			$conttp->setVariable("ACT_DISABLED","disabled");
			$conttp->setVariable("ACT_CHECKED","checked");
			$conttp->setVariable("ACTIVE","1");
      		$strInfo = "<br><span class=\"dbmessage\">".translate('Entry cannot be activated because it is used by another configuration').":</span><br><span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
			$conttp->setVariable("CHECK_MUST_DATA",$strInfo);
			$conttp->setVariable("RELATION_CLASS","elementShow");
		} 
		// Insert command type
		if ($arrModifyData['command_type'] == 1) {$conttp->setVariable("CHECK_TYPE_SELECTED","selected");}
		if ($arrModifyData['command_type'] == 2) {$conttp->setVariable("MISC_TYPE_SELECTED","selected");}
		$conttp->setVariable("MODUS","modify");
	}
	$conttp->parse("datainsert");
	$conttp->show("datainsert");
}
//
// List view
// ==========
if ($chkModus == "display") {
  	// Process template text raplacements
  	foreach($arrDescription AS $elem) {
    	$mastertp->setVariable($elem['name'],$elem['string']);
  	} 
	$mastertp->setVariable("FIELD_1",translate('Command name'));
	$mastertp->setVariable("FIELD_2",translate('Command line'));	
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("ACTION_MODIFY",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
	$mastertp->setVariable("TABLE_NAME","tbl_command");
  	$mastertp->setVariable("DAT_SEARCH",$_SESSION['search']['checkcommand']);
  	// Get Group id's with READ
  	$strAccess = $myVisClass->getAccGroupRead($_SESSION['userid']);
	// Include domain list
	$myVisClass->insertDomainList($mastertp);
  	// Process filter string
  	$strSearchWhere = "";
 	if ($_SESSION['search']['checkcommand'] != "") {
  		$strSearchTxt   = $_SESSION['search']['checkcommand'];
  		$strSearchWhere = "AND (`command_name` LIKE '%".$strSearchTxt."%' OR `command_line` LIKE '%".$strSearchTxt."%')";
  	}
  	// Count datasets
	$strSQL    = "SELECT count(*) AS `number` FROM `tbl_command` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {
		$myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError,$strMessage);
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
  	// Get datasets
	$strSQL    = "SELECT `id`, `command_name`, `command_line`, `active`, `config_id` FROM `tbl_command` WHERE $strDomainWhere $strSearchWhere
          		  AND `access_group` IN ($strAccess) ORDER BY `config_id`, `command_name` LIMIT $chkLimit,".$SETS['common']['pagelines'];
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	$mastertp->setVariable("CELLCLASS_L","tdlb");
	$mastertp->setVariable("CELLCLASS_M","tdmb");	
	$mastertp->setVariable("DISABLED","disabled");
	$mastertp->setVariable("DATA_FIELD_1",translate('No data'));
	$mastertp->setVariable("DATA_FIELD_2","&nbsp;");
	$mastertp->setVariable("DATA_ACTIVE","&nbsp;");
	$mastertp->setVariable("CHB_CLASS","checkbox");
	$mastertp->setVariable("PICTURE_CLASS","elementHide");
	if ($booReturn == false) {
		$myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError,$strMessage);
	} else if ($intDataCount != 0) {
		for ($i=0;$i<$intDataCount;$i++) {
      		// Line colours
			$strClassL = "tdld"; $strClassM = "tdmd"; $strChbClass = "checkboxline";
			if ($i%2 == 1) {$strClassL = "tdlb"; $strClassM = "tdmb"; $strChbClass = "checkbox";}
			if ($arrDataLines[$i]['active'] == 0) {$strActive = translate('No');} else {$strActive = translate('Yes');}	
      		// Set datafields
			foreach($arrDescription AS $elem) {
				$mastertp->setVariable($elem['name'],$elem['string']);
			}		
			if (strlen($arrDataLines[$i]['command_line']) > 70) {$strAdd = " .....";} else {$strAdd = "";}
			$mastertp->setVariable("DATA_FIELD_1",htmlspecialchars($arrDataLines[$i]['command_name'],ENT_COMPAT,'UTF-8'));
			$mastertp->setVariable("DATA_FIELD_2",htmlspecialchars(substr($arrDataLines[$i]['command_line'],0,70),ENT_COMPAT,'UTF-8').$strAdd);
			$mastertp->setVariable("DATA_ACTIVE",$strActive);
			$mastertp->setVariable("LINE_ID",$arrDataLines[$i]['id']);
			$mastertp->setVariable("CELLCLASS_L",$strClassL);
			$mastertp->setVariable("CELLCLASS_M",$strClassM);
			$mastertp->setVariable("CHB_CLASS",$strChbClass);
			$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
			$mastertp->setVariable("PICTURE_CLASS","elementShow");
			$mastertp->setVariable("DISABLED","");
			if ($chkModus != "display") $conttp->setVariable("DISABLED","disabled");
			// Disable common domain objects
			if ($arrDataLines[$i]['config_id'] != $chkDomainId) {
				$mastertp->setVariable("DISABLED","disabled");
				$mastertp->setVariable("PICTURE_CLASS","elementHide");
				$mastertp->setVariable("DOMAIN_SPECIAL"," [common]");
			}
			$mastertp->parse("datarow");
		}
	} else {
		// Disable common domain objects
		if ($chkDomainId == 0) {
			$mastertp->setVariable("DISABLED","disabled");
			$mastertp->setVariable("DOMAIN_SPECIAL","&nbsp;");
		}
		$mastertp->parse("datarow");
	}
	$mastertp->setVariable("BUTTON_CLASS","elementShow");
	if ($chkDomainId == 0) $mastertp->setVariable("BUTTON_CLASS","elementHide");
	// Show page numbers
	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	if (isset($intCount)) $mastertp->setVariable("PAGES",$myVisClass->buildPageLinks(filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING),$intCount,$chkLimit));
	$mastertp->parse("datatable");
	$mastertp->show("datatable");
}
// Show messages
$mastertp->setVariable("DBMESSAGE",$strMessage);
if ($chkDomainId != 0) {
	if ($strOld != "") $mastertp->setVariable("FILEISOLD","<br><span class=\"dbmessage\">".$strOld."</span><br>");	
	$mastertp->setVariable("LAST_MODIFIED",translate('Last database update:')." <b>".$strLastModified."</b>");
	$mastertp->setVariable("FILEDATE",translate('Last change of the configuration file:')." <b>".$strFileDate."</b>");
}
$mastertp->parse("msgfooter");
$mastertp->show("msgfooter");
//
// Process footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>