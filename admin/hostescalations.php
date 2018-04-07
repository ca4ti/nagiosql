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
// Component : Host escalation definition
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
$intMain      	= 5;
$intSub       	= 13;
$intMenu      	= 2;
$preContent   	= "admin/hostescalations.tpl.htm";
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
$chkTfSearch    	= isset($_POST['txtSearch'])		? $_POST['txtSearch']			: "";
$chkSelContact      = isset($_POST['selContact'])     	? $_POST['selContact']          : array("");
$chkSelContactGroup	= isset($_POST['selContactGroup'])	? $_POST['selContactGroup']     : array("");
$chkSelHostGroup    = isset($_POST['selHostGroup'])   	? $_POST['selHostGroup']        : array("");
$chkSelHost       	= isset($_POST['selHost'])      	? $_POST['selHost']           	: array("");
$chkSelEscPeriod    = isset($_POST['selEscPeriod'])   	? $_POST['selEscPeriod']+0      : 0;
$chkTfConfigName    = isset($_POST['tfConfigName'])   	? $_POST['tfConfigName']        : "";
$chkEOd         	= isset($_POST['chbEOd'])     		? $_POST['chbEOd'].","          : "";
$chkEOu         	= isset($_POST['chbEOu'])     		? $_POST['chbEOu'].","          : "";
$chkEOr         	= isset($_POST['chbEOr'])     		? $_POST['chbEOr'].","          : "";
$chkSelAccessGroup	= isset($_POST['selAccessGroup'])	? $_POST['selAccessGroup']+0	: 0;
//
$chkTfFirstNotif    = (isset($_POST['tfFirstNotif'])  	&& ($_POST['tfFirstNotif'] 	  != "")) ? $myVisClass->checkNull($_POST['tfFirstNotif'])    : "NULL";
$chkTfLastNotif     = (isset($_POST['tfLastNotif'])   	&& ($_POST['tfLastNotif'] 	  != "")) ? $myVisClass->checkNull($_POST['tfLastNotif'])     : "NULL";
$chkTfNotifInterval = (isset($_POST['tfNotifInterval']) && ($_POST['tfNotifInterval'] != "")) ? $myVisClass->checkNull($_POST['tfNotifInterval']) : "NULL";
//
// Quote special characters
// ==========================
if (get_magic_quotes_gpc() == 0) {
  	$chkTfSearch		= addslashes($chkTfSearch);
  	$chkTfConfigName 	= addslashes($chkTfConfigName);
}
//
// Search/Filter - Session data
// ============================
if (!isset($_SESSION['search']) || !isset($_SESSION['search']['hostescalation'])) $_SESSION['search']['hostescalation'] = "";
if (($chkModus == "checkform") || ($chkModus == "filter")) {
  	$_SESSION['search']['hostescalation'] = $chkTfSearch;
}
//
// Data processing
// ===============
$strEO = substr($chkEOd.$chkEOu.$chkEOr,0,-1);
if (($chkSelHost[0]     	== "")  || ($chkSelHost[0]         == "0")) {$intSelHost     	 = 0;}  else {$intSelHost         = 1;}
if (($chkSelHostGroup[0]  	== "")  || ($chkSelHostGroup[0]    == "0")) {$intSelHostGroup    = 0;}  else {$intSelHostGroup    = 1;}
if (($chkSelContact[0]    	== "")  || ($chkSelContact[0]      == "0")) {$intSelContact      = 0;}  else {$intSelContact      = 1;}
if (($chkSelContactGroup[0] == "")  || ($chkSelContactGroup[0] == "0")) {$intSelContactGroup = 0;}  else {$intSelContactGroup = 1;}
if ($chkSelHost[0]          == "*") $intSelHost 		= 2;
if ($chkSelHostGroup[0]     == "*") $intSelHostGroup 	= 2;
if ($chkSelContact[0]       == "*") $intSelContact 		= 2;
if ($chkSelContactGroup[0]  == "*") $intSelContactGroup = 2;
// 
// Add or modify data
// ==================
if (($chkModus == "insert") || ($chkModus == "modify")) {
  	if ($hidActive 	 == 1) $chkActive = 1;
  	if ($chkGroupAdm == 1) {$strGroupSQL = "`access_group`=$chkSelAccessGroup, ";} else {$strGroupSQL = "";}
  	$strSQLx = "`tbl_hostescalation` SET `config_name`='$chkTfConfigName', `host_name`=$intSelHost, `hostgroup_name`=$intSelHostGroup,
        		`contacts`=$intSelContact, `contact_groups`=$intSelContactGroup, `first_notification`=$chkTfFirstNotif,
        		`last_notification`=$chkTfLastNotif, `notification_interval`=$chkTfNotifInterval, `escalation_period`='$chkSelEscPeriod',
        		$strGroupSQL `escalation_options`='$strEO', `active`='$chkActive', `config_id`=$chkDomainId, `last_modified`=NOW()";
  	if ($chkModus == "insert") {
    	$strSQL = "INSERT INTO ".$strSQLx;
  	} else {
    	$strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
  	}
  	if ((($intSelHost != 0) || ($chkSelHostGroup != 0)) && (($intSelContact != 0) || ($intSelContactGroup != 0)) &&
    	($chkTfFirstNotif != "NULL") && ($chkTfLastNotif != "NULL") && ($chkTfNotifInterval != "NULL")) {
    	$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
		$myDataClass->updateStatusTable("tbl_hostescalation");
    	if ($chkModus == "insert") $chkDataId = $intInsertId;
    	if ($intInsert == 1) {
      		$intReturn = 1;
    	} else {
      		if ($chkModus == "insert") $myDataClass->writeLog(translate('New host escalation inserted:')." ".$chkTfConfigName);
      		if ($chkModus == "modify") $myDataClass->writeLog(translate('Host escalation modified:')." ".$chkTfConfigName);
      		//
      		// Insert/update relations
      		// =======================
      		if ($chkModus == "insert") {
        		if ($intSelHost     	!= 0) $myDataClass->dataInsertRelation("tbl_lnkHostescalationToHost",$chkDataId,$chkSelHost);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHostGroup  	!= 0) $myDataClass->dataInsertRelation("tbl_lnkHostescalationToHostgroup",$chkDataId,$chkSelHostGroup);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelContact    	!= 0) $myDataClass->dataInsertRelation("tbl_lnkHostescalationToContact",$chkDataId,$chkSelContact);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelContactGroup != 0) $myDataClass->dataInsertRelation("tbl_lnkHostescalationToContactgroup",$chkDataId,$chkSelContactGroup);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
     		 } else if ($chkModus == "modify") {
        		if ($intSelHost != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkHostescalationToHost",$chkDataId,$chkSelHost);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkHostescalationToHost",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHostGroup != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkHostescalationToHostgroup",$chkDataId,$chkSelHostGroup);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkHostescalationToHostgroup",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelContact != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkHostescalationToContact",$chkDataId,$chkSelContact);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkHostescalationToContact",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelContactGroup != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkHostescalationToContactgroup",$chkDataId,$chkSelContactGroup);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkHostescalationToContactgroup",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		}
			//
			// Update Import HASH
			// ==================
			$booReturn = $myDataClass->updateHash('tbl_hostescalation',$chkDataId);
			$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		$intReturn = 0;
    	}
  	} else {
    	$myVisClass->processMessage(translate('Database entry failed! Not all necessary data filled in!'),$strMessage);
  	}
  	$chkModus = "display";
} else if ($chkModus == "make") {
	// Write configuration file
  	$intReturn   = $myConfigClass->createConfig("tbl_hostescalation",0);
  	$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "info")) {
	// Display additional relation information
  	$myDataClass->infoRelation("tbl_hostescalation",$chkListId,"config_name");
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$intReturn   = 0;
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Delete selected datasets
  	$intReturn 	 = $myDataClass->dataDeleteFull("tbl_hostescalation",$chkListId);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Copy selected datasets
  	$intReturn   = $myDataClass->dataCopyEasy("tbl_hostescalation","config_name",$chkListId,$chkSelTargetDomain);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "activate")) {
	// Activate selected datasets
	$intReturn   = $myDataClass->dataActivate("tbl_hostescalation",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "deactivate")) {
	// Deactivate selected datasets
	$intReturn   = $myDataClass->dataDeactivate("tbl_hostescalation",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus    = "display"; 
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Open a dataset to modify
	$booReturn   = $myDBClass->getSingleDataset("SELECT * FROM `tbl_hostescalation` WHERE `id`=".$chkListId,$arrModifyData);
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
	  		$chkModus 	  = "add";	
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
$myConfigClass->lastModified("tbl_hostescalation",$strLastModified,$strFileDate,$strOld);
$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Start content
// =============
$conttp->setVariable("TITLE",translate('Define host escalation (hostescalations.cfg)'));
$conttp->parse("header");
$conttp->show("header");
//
// Singe data form
// ===============
if ($chkModus == "add") {
	// Process host and host group selection field
  	$intReturn1 = 0;
	$intReturn2 = 0;
	if (isset($arrModifyData['host_name'])) {$intFieldId = $arrModifyData['host_name'];} else {$intFieldId = 0;}
	$intReturn1 = $myVisClass->parseSelectMulti('tbl_host','host_name','host','tbl_lnkHostescalationToHost',2,$intFieldId);
	if (isset($arrModifyData['hostgroup_name'])) {$intFieldId = $arrModifyData['hostgroup_name'];} else {$intFieldId = 0;}
	$intReturn2 = $myVisClass->parseSelectMulti('tbl_hostgroup','hostgroup_name','hostgroup','tbl_lnkHostescalationToHostgroup',2,$intFieldId);
	if (($intReturn1 != 0) && ($intReturn2 != 0)) $strDBWarning .= translate('Attention, no hosts and hostgroups defined!')."<br>";
  	// Process timeperiod selection field
  	if (isset($arrModifyData['escalation_period'])) {$intFieldId = $arrModifyData['escalation_period'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectSimple('tbl_timeperiod','timeperiod_name','escperiod',1,$intFieldId);
	// Process contact and contact group selection field
  	$intReturn1 = 0;
	$intReturn2 = 0;
	if (isset($arrModifyData['contacts'])) {$intFieldId = $arrModifyData['contacts'];} else {$intFieldId = 0;}
	$intReturn1 = $myVisClass->parseSelectMulti('tbl_contact','contact_name','contact','tbl_lnkHostescalationToContact',2,$intFieldId);
	if (isset($arrModifyData['contact_groups'])) {$intFieldId = $arrModifyData['contact_groups'];} else {$intFieldId = 0;}
	$intReturn2 = $myVisClass->parseSelectMulti('tbl_contactgroup','contactgroup_name','contactgroup','tbl_lnkHostescalationToContactgroup',2,$intFieldId);
  	if (($intReturn1 != 0) && ($intReturn2 != 0)) $strDBWarning .= translate('Attention, no contacts and contactgroups defined!')."<br>";
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
	$conttp->setVariable("MENU_ID",$intSub);
	if ($strDBWarning != "") $conttp->setVariable("WARNING",$strDBWarning.translate('Saving not possible!'));
	$conttp->setVariable("ACT_CHECKED","checked");
	$conttp->setVariable("MODUS","insert");
	$conttp->setVariable("VERSION",$intVersion);
	$conttp->setVariable("SELECT_FIELD_DISABLED","disabled");
	if ($SETS['common']['seldisable'] == 0)$conttp->setVariable("SELECT_FIELD_DISABLED","enabled");
	if ($chkGroupAdm == 0) $conttp->setVariable("RESTRICT_GROUP_ADMIN","class=\"elementHide\"");
	// Process additional fields based on nagios version
  	if ($intVersion == 3) {
    	$conttp->setVariable("CLASS_NAME_20","elementHide");
    	$conttp->setVariable("CLASS_NAME_30","elementShow");
 	 } else {
    	$conttp->setVariable("CLASS_NAME_20","elementShow");
    	$conttp->setVariable("CLASS_NAME_30","elementHide");
    	$conttp->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
    	$conttp->setVariable("MUST_20_STAR","*");
    	$conttp->setVariable("MEMBER_20_MUST","selMembers,");
  	}
  	// Insert data from database in "modify" mode
  	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
    	foreach($arrModifyData AS $key => $value) {
      		if (($key == "active") || ($key == "last_modified") || ($key == "access_rights")) continue;
      		$conttp->setVariable("DAT_".strtoupper($key),htmlentities($value,ENT_QUOTES,'UTF-8'));
    	}
    	if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
    	$conttp->setVariable("MODUS","modify");
    	// Set option fields
    	foreach(explode(",",$arrModifyData['escalation_options']) AS $elem) {
      		$conttp->setVariable("DAT_EO".strtoupper($elem)."_CHECKED","checked");
    	}
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
  	$mastertp->setVariable("FIELD_1",translate('Config name'));
  	$mastertp->setVariable("FIELD_2",translate('Hosts')." / ".translate('Host groups'));
  	$mastertp->setVariable("LIMIT",$chkLimit);
  	$mastertp->setVariable("ACTION_MODIFY",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
  	$mastertp->setVariable("TABLE_NAME","tbl_hostescalation");
  	$mastertp->setVariable("DAT_SEARCH",$_SESSION['search']['hostescalation']);
  	// Get Group id's with READ
  	$strAccess = $myVisClass->getAccGroupRead($_SESSION['userid']);
	// Include domain list
	$myVisClass->insertDomainList($mastertp);
  	// Process filter string
  	$strSearchWhere = "";
 	if ($_SESSION['search']['hostescalation'] != "") {
  		$strSearchTxt   = $_SESSION['search']['hostescalation'];
  		$strSearchWhere = "AND (`config_name` LIKE '%".$strSearchTxt."%')";
  	}
  	// Count datasets
  	$strSQL    = "SELECT count(*) AS `number` FROM `tbl_hostescalation` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
  	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
  	if ($booReturn == false) {
    	$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
  	} else {
    	$intCount = (int)$arrDataLinesCount['number'];
  	}
  	// Get datasets
  	$strSQL    = "SELECT `id`, `config_name`, `host_name`, `hostgroup_name`, `active`, `config_id`  FROM `tbl_hostescalation` WHERE $strDomainWhere 
          		  $strSearchWhere AND `access_group` IN ($strAccess)ORDER BY `config_id`, `config_name` LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
      		$mastertp->setVariable("DATA_FIELD_1",htmlspecialchars($arrDataLines[$i]['config_name'],ENT_COMPAT,'UTF-8'));
      		$strDataline = "";
      		if ($arrDataLines[$i]['host_name'] != 0) {
        		$strSQLHost = "SELECT `host_name` FROM `tbl_host` LEFT JOIN `tbl_lnkHostescalationToHost` ON `id`=`idSlave`
                 			   WHERE `idMaster`=".$arrDataLines[$i]['id'];
        		$booReturn 	= $myDBClass->getDataArray($strSQLHost,$arrDataHosts,$intDCHost);
        		if ($intDCHost != 0) {
          			foreach($arrDataHosts AS $elem) {
            			$strDataline .= $elem['host_name'].",";
          			}
        		}
      		} else {
        		$strSQLHost = "SELECT `hostgroup_name` FROM `tbl_hostgroup` LEFT JOIN `tbl_lnkHostescalationToHostgroup` ON `id`=`idSlave`
                 			   WHERE `idMaster`=".$arrDataLines[$i]['id'];
        		$booReturn  = $myDBClass->getDataArray($strSQLHost,$arrDataHostgroups,$intDCHostgroup);
        		if ($intDCHostgroup != 0) {
          			foreach($arrDataHostgroups AS $elem) {
            			$strDataline .= $elem['hostgroup_name'].",";
          			}
        		}
      		}
      		if (strlen(substr($strDataline,0,-1)) > 50) {$strAdd = "...";} else {$strAdd = "";}
      		$mastertp->setVariable("DATA_FIELD_2",htmlspecialchars(substr(substr($strDataline,0,-1),0,50).$strAdd,ENT_COMPAT,'UTF-8'));
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
	if ($strOld != "") $mastertp->setVariable("FILEISOLD","<br><span class=\"dbmessage\">".$strOld."&nbsp;</span><br>");
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