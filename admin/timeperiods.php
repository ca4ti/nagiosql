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
// Component : Timeperiod definitions
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
$intMain      	= 3;
$intSub       	= 2;
$intMenu      	= 2;
$preContent   	= "admin/timeperiods.tpl.htm";
$strDBWarning 	= "";
$intCount     	= 0;
//
// Include preprocessing file
// ==========================
$preAccess    	= 1;
$preFieldvars 	= 1;
require("../functions/prepend_adm.php");
//
// Process post parameters
// =======================
$chkTfSearch    	= isset($_POST['txtSearch'])    	? $_POST['txtSearch']   		: "";
$chkInsName     	= isset($_POST['tfName'])       	? $_POST['tfName']      		: "";
$chkInsAlias    	= isset($_POST['tfFriendly'])   	? $_POST['tfFriendly']  		: "";
$chkInsTplName  	= isset($_POST['tfTplName'])    	? $_POST['tfTplName']   		: "";
$chkSelExclude  	= isset($_POST['selExclude'])   	? $_POST['selExclude']  		: array("");
$chkSelInclude  	= isset($_POST['selInclude'])   	? $_POST['selInclude']  		: array("");
$chkSelAccessGroup	= isset($_POST['selAccessGroup'])	? $_POST['selAccessGroup']+0	: 0;
//
// Search/Filter - Session data
// ============================
if (!isset($_SESSION['search']) || !isset($_SESSION['search']['timeperiod'])) $_SESSION['search']['timeperiod'] = "";
if (($chkModus == "checkform") || ($chkModus == "filter")) {
	$_SESSION['search']['timeperiod'] = $chkTfSearch;
}
//
// Quote special characters
// ==========================
if (get_magic_quotes_gpc() == 0) {
	$chkTfSearch	= addslashes($chkTfSearch);
	$chkInsName     = addslashes($chkInsName);
	$chkInsAlias    = addslashes($chkInsAlias);
	$chkInsTplName  = addslashes($chkInsTplName);
}
//
// Data processing
// ===============
if (($chkSelExclude[0] == "") || ($chkSelExclude[0] == "0")) {$intSelExclude = 0;}  else {$intSelExclude = 1;}
if (($chkSelInclude[0] == "") || ($chkSelInclude[0] == "0")) {$intSelInclude = 0;}  else {$intSelInclude = 1;}
// 
// Add or modify data
// ==================
if (($chkModus == "insert") || ($chkModus == "modify")) {
	if ($hidActive   == 1) $chkActive = 1;
	if ($chkModus == "insert") 	{$strDomain   = "`config_id`=$chkDomainId, ";} 			else {$strDomain   = "";}
	if ($chkGroupAdm == 1) 		{$strGroupSQL = "`access_group`=$chkSelAccessGroup, ";} else {$strGroupSQL = "";}
	$strSQLx = "`tbl_timeperiod` SET `timeperiod_name`='$chkInsName', `alias`='$chkInsAlias', `exclude`=$intSelExclude, `use_template`=$intSelInclude,
              	`name`='$chkInsTplName', `active`='$chkActive', $strDomain $strGroupSQL `last_modified`=NOW()";
	if ($chkModus == "insert") {
		$strSQL = "INSERT INTO ".$strSQLx;
	} else {
		$strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
	}
	if (($chkInsName != "") && ($chkInsAlias != "")) {
		$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
		$myDataClass->updateStatusTable("tbl_timeperiod");
		if ($chkModus == "insert") $chkDataId = $intInsertId;
		if ($intInsert == 1) {
			$intReturn = 1;
		} else {
			if ($chkModus  == "insert")   $myDataClass->writeLog(translate('New time period inserted:')." ".$chkInsName);
			if ($chkModus  == "modify")   $myDataClass->writeLog(translate('Time period modified:')." ".$chkInsName);
			//
			// Insert/update relations
			// =======================
			if ($chkModus == "insert") {
				if ($intSelExclude != 0)  $myDataClass->dataInsertRelation("tbl_lnkTimeperiodToTimeperiod",$chkDataId,$chkSelExclude);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
				if ($intSelInclude != 0)  $myDataClass->dataInsertRelation("tbl_lnkTimeperiodToTimeperiodUse",$chkDataId,$chkSelInclude);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
			} else if ($chkModus == "modify") {
				if ($intSelExclude != 0) {
					$myDataClass->dataUpdateRelation("tbl_lnkTimeperiodToTimeperiod",$chkDataId,$chkSelExclude);
				} else {
					$myDataClass->dataDeleteRelation("tbl_lnkTimeperiodToTimeperiod",$chkDataId);
				}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
				if ($intSelInclude != 0) {
					$myDataClass->dataUpdateRelation("tbl_lnkTimeperiodToTimeperiodUse",$chkDataId,$chkSelInclude);
				} else {
					$myDataClass->dataDeleteRelation("tbl_lnkTimeperiodToTimeperiodUse",$chkDataId);
				}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
			}
			$intReturn = 0;
			//
			// Insert/update time defintions
			// =============================
			if ($chkModus == "modify") {
				$strSQL   	= "DELETE FROM `tbl_timedefinition` WHERE `tipId`=$chkDataId";
				$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
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
						$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
					}
				}
			}
			$intReturn = 0;
    	}
  	} else {
	  	$myVisClass->processMessage(translate('Database entry failed! Not all necessary data filled in!'),$strMessage);
  	}
  	$chkModus    = "display";
} else if ($chkModus == "make") {
  	// Write configuration file
  	$intReturn   = $myConfigClass->createConfig("tbl_timeperiod",0);
  	$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "info")) {
  	// Display additional relation information
  	$intReturn   = $myDataClass->infoRelation("tbl_timeperiod",$chkListId,"timeperiod_name");
 	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$intReturn   = 0;
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
  	// Delete selected datasets
  	$intReturn   = $myDataClass->dataDeleteFull("tbl_timeperiod",$chkListId);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
  	// Copy selected datasets
  	$intReturn   = $myDataClass->dataCopyEasy("tbl_timeperiod","timeperiod_name",$chkListId,$chkSelTargetDomain);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "activate")) {
  	// Activate selected datasets
  	$intReturn   = $myDataClass->dataActivate("tbl_timeperiod",$chkListId);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "deactivate")) {
  	// Deactivate selected datasets
  	$intReturn   = $myDataClass->dataDeactivate("tbl_timeperiod",$chkListId);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display"; 
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
  	// Open a dataset to modify
  	$booReturn   = $myDBClass->getSingleDataset("SELECT * FROM `tbl_timeperiod` WHERE `id`=".$chkListId,$arrModifyData);
  	$myVisClass->processMessage($myDBClass->strDBError,$strMessage);
  	if ($booReturn == false) {
		$myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError,$strMessage);
		$intReturn   = 1;
		$chkModus    = "add";
  	} else {
  		// Check access permission
    	$intReturn = $myVisClass->checkAccGroup($_SESSION['userid'],$arrModifyData['access_group']);  
		if ($intReturn == 1) {
			$myVisClass->processMessage(translate('No permission to open configuration!'),$strMessage);
	  		$arrModifyData  = "";
      		$chkModus       = "display";
		} else {
	  		$chkModus		= "add";	
		}
  	}	
} else if ($chkModus != "add") {
  	$chkModus   = "display";
}
// Get status messages from database
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $strMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$strMessage."</span>";
//
// Get date/time of last database and config file manipulation
// ===========================================================
$myConfigClass->lastModified("tbl_timeperiod",$strLastModified,$strFileDate,$strOld);
$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage); 
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
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
  	// Process exclude selection fields
  	if (isset($arrModifyData['exclude'])) {$intFieldId = $arrModifyData['exclude'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectMulti('tbl_timeperiod','name','excludes','tbl_lnkTimeperiodToTimeperiod',0,$intFieldId,$chkListId);
  	// Process include selection fields
  	if (isset($arrModifyData['use_template'])) {$intFieldId = $arrModifyData['use_template'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectMulti('tbl_timeperiod','name','uses','tbl_lnkTimeperiodToTimeperiodUse',0,$intFieldId,$chkListId);
  	// Process acces group selection field
  	if (isset($arrModifyData['access_group'])) {$intFieldId = $arrModifyData['access_group'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectSimple('tbl_group','groupname','acc_group',0,$intFieldId);
  	// Process template text raplacements
  	foreach($arrDescription AS $elem) {
    	$conttp->setVariable($elem['name'],str_replace("</","<\/",$elem['string']));
  	}
  	$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
  	$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
  	$conttp->setVariable("LIMIT",$chkLimit);
  	$conttp->setVariable("MENU_ID",$intSub);
  	$conttp->setVariable("DOCUMENT_ROOT",$SETS['path']['root']);
  	if ($strDBWarning != "") $conttp->setVariable("WARNING",$strDBWarning.translate('Saving not possible!'));
  	$conttp->setVariable("ACT_CHECKED","checked");
  	$conttp->setVariable("MODUS","insert");
  	$conttp->setVariable("VERSION",$intVersion);
  	$conttp->setVariable("LANG_INSERT_ALL_TIMERANGE",translate('Please insert a time definition and a time range'));
	$conttp->setVariable("RELATION_CLASS","elementHide");
	$conttp->setVariable("SELECT_FIELD_DISABLED","disabled");
	if ($SETS['common']['seldisable'] == 0)$conttp->setVariable("SELECT_FIELD_DISABLED","enabled");
  	if ($chkGroupAdm == 0) $conttp->setVariable("RESTRICT_GROUP_ADMIN","class=\"elementHide\"");
  	// Process additional fields based on nagios version
  	if ($intVersion == 3) {
    	$conttp->setVariable("CLASS_NAME_20","elementHide");
    	$conttp->setVariable("CLASS_NAME_30","elementShow");
    	$conttp->setVariable("VERSION","3");
  	} else {
    	$conttp->setVariable("CLASS_NAME_20","elementShow");
    	$conttp->setVariable("CLASS_NAME_30","elementHide");
    	$conttp->setVariable("VERSION","2");
  	}
  	// Insert data from database in "modify" mode
  	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
    	foreach($arrModifyData AS $key => $value) {
      		if (($key == "active") || ($key == "last_modified") || ($key == "access_rights")) continue;
      		$conttp->setVariable("DAT_".strtoupper($key),htmlentities($value,ENT_QUOTES,'UTF-8'));
    	}
    	if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
    	// Check relation information to find out locked configuration datasets
    	if ($myDataClass->infoRelation("tbl_timeperiod",$arrModifyData['id'],"timeperiod_name") != 0) {
      		$conttp->setVariable("ACT_DISABLED","disabled");
      		$conttp->setVariable("ACT_CHECKED","checked");
      		$conttp->setVariable("ACTIVE","1");
      		$strInfo = "<br><span class=\"dbmessage\">".translate('Entry cannot be activated because it is used by another configuration').":</span><br><span class=\"greenmessage\">".$myDataClass->strDBMessage."&nbsp;</span>";
      		$conttp->setVariable("CHECK_MUST_DATA",$strInfo);
			$conttp->setVariable("RELATION_CLASS","elementShow");
    	}
    	$conttp->setVariable("MODUS","modify");
    	$conttp->setVariable("TIP_ID",$arrModifyData['id']);
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
  	$mastertp->setVariable("FIELD_1",translate('Time period'));
  	$mastertp->setVariable("FIELD_2",translate('Description'));
  	$mastertp->setVariable("LIMIT",$chkLimit);
  	$mastertp->setVariable("ACTION_MODIFY",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
  	$mastertp->setVariable("TABLE_NAME","tbl_timeperiod");
  	$mastertp->setVariable("DAT_SEARCH",$_SESSION['search']['timeperiod']);
  	// Get Group id's with READ
  	$strAccess = $myVisClass->getAccGroupRead($_SESSION['userid']);
	// Include domain list
	$myVisClass->insertDomainList($mastertp);
  	// Process filter string
  	$strSearchWhere = "";
  	if ($_SESSION['search']['timeperiod'] != "") {
  	$strSearchTxt   = $_SESSION['search']['timeperiod'];
  	$strSearchWhere = "AND (`timeperiod_name` LIKE '%".$strSearchTxt."%' OR `alias` LIKE '%".$strSearchTxt."%' OR
                      `name` LIKE '%".$strSearchTxt."%')";
  	}
  	// Count datasets
  	$strSQL    = "SELECT count(*) AS `number` FROM `tbl_timeperiod` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
  	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
  	if ($booReturn == false) {
		$myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError,$strMessage);
  	} else {
    	$intCount = (int)$arrDataLinesCount['number'];
  	}
  	// Get datasets
  	$strSQL    = "SELECT `id`, `timeperiod_name`, `alias`, `active`, `config_id` FROM `tbl_timeperiod` WHERE $strDomainWhere $strSearchWhere 
				  AND `access_group` IN ($strAccess) ORDER BY `config_id`, `timeperiod_name` LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
			$mastertp->setVariable("DATA_FIELD_1",htmlspecialchars($arrDataLines[$i]['timeperiod_name'],ENT_COMPAT,'UTF-8'));
			$mastertp->setVariable("DATA_FIELD_2",htmlspecialchars($arrDataLines[$i]['alias'],ENT_COMPAT,'UTF-8'));
			$mastertp->setVariable("DATA_ACTIVE",$strActive);
			$mastertp->setVariable("LINE_ID",$arrDataLines[$i]['id']);
			$mastertp->setVariable("CELLCLASS_L",$strClassL);
			$mastertp->setVariable("CELLCLASS_M",$strClassM);
			$mastertp->setVariable("CHB_CLASS",$strChbClass);
			$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
			$mastertp->setVariable("PICTURE_CLASS","elementShow");
			$mastertp->setVariable("DISABLED","");
			if ($chkModus != "display") $mastertp->setVariable("DISABLED","disabled");
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
// Include footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>