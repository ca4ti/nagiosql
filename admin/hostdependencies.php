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
// Component : Host dependencies definition
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
$intSub       	= 12;
$intMenu      	= 2;
$preContent   	= "admin/hostdependencies.tpl.htm";
$strDBWarning 	= "";
$intCount     	= 0;
//
// Include preprocessing file
// ==========================
$preAccess    	= 1;
$preFieldvars 	= 1;
require("../functions/prepend_adm.php");
$myConfigClass->getConfigData("version",$intVersion);
//
// Process post parameters
// =======================
$chkTfSearch    	= isset($_POST['txtSearch'])		? $_POST['txtSearch']			: "";
$chkSelHostDepend   = isset($_POST['selHostDepend'])  	? $_POST['selHostDepend']   	: array("");
$chkSelHost       	= isset($_POST['selHost'])      	? $_POST['selHost']         	: array("");
$chkSelHostgroupDep	= isset($_POST['selHostgroupDep'])  ? $_POST['selHostgroupDep'] 	: array("");
$chkSelHostgroup    = isset($_POST['selHostgroup'])   	? $_POST['selHostgroup']    	: array("");
$chkEOo         	= isset($_POST['chbEOo'])     		? $_POST['chbEOo'].","          : "";
$chkEOd         	= isset($_POST['chbEOd'])     		? $_POST['chbEOd'].","          : "";
$chkEOu         	= isset($_POST['chbEOu'])     		? $_POST['chbEOu'].","          : "";
$chkEOp         	= isset($_POST['chbEOp'])     		? $_POST['chbEOp'].","          : "";
$chkEOn         	= isset($_POST['chbEOn'])     		? $_POST['chbEOn'].","          : "";
$chkNOo         	= isset($_POST['chbNOo'])     		? $_POST['chbNOo'].","          : "";
$chkNOd         	= isset($_POST['chbNOd'])     		? $_POST['chbNOd'].","          : "";
$chkNOu         	= isset($_POST['chbNOu'])     		? $_POST['chbNOu'].","          : "";
$chkNOp         	= isset($_POST['chbNOp'])     		? $_POST['chbNOp'].","          : "";
$chkNOn         	= isset($_POST['chbNOn'])     		? $_POST['chbNOn'].","          : "";
$chkSelDependPeriod = isset($_POST['selDependPeriod'])	? $_POST['selDependPeriod']+0	: 0;
$chkTfConfigName    = isset($_POST['tfConfigName'])   	? $_POST['tfConfigName']        : "";
$chkInherit       	= isset($_POST['chbInherit'])   	? $_POST['chbInherit']          : 0;
$chkSelAccessGroup	= isset($_POST['selAccessGroup'])	? $_POST['selAccessGroup']+0	: 0;
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
if (!isset($_SESSION['search']) || !isset($_SESSION['search']['hostdependencies'])) $_SESSION['search']['hostdependencies'] = "";
if (($chkModus == "checkform") || ($chkModus == "filter")) {
  	$_SESSION['search']['hostdependencies'] = $chkTfSearch;
}
//
// Data processing
// ===============
$strEO = substr($chkEOo.$chkEOd.$chkEOu.$chkEOp.$chkEOn,0,-1);
$strNO = substr($chkNOo.$chkNOd.$chkNOu.$chkNOp.$chkNOn,0,-1);
if (($chkSelHostDepend[0]   == "")  || ($chkSelHostDepend[0]   == "0")) {$intSelHostDepend   = 0;}  else {$intSelHostDepend   = 1;}
if ($chkSelHostDepend[0] 	== "*")	$intSelHostDepend = 2;
if (($chkSelHost[0]         == "")  || ($chkSelHost[0]         == "0")) {$intSelHost         = 0;}  else {$intSelHost         = 1;}
if ($chkSelHost[0] 			== "*") $intSelHost = 2;
if (($chkSelHostgroupDep[0] == "")  || ($chkSelHostgroupDep[0] == "0")) {$intSelHostgroupDep = 0;}  else {$intSelHostgroupDep = 1;}
if ($chkSelHostgroupDep[0] 	== "*") $intSelHostgroupDep = 2;
if (($chkSelHostgroup[0]    == "")  || ($chkSelHostgroup[0]    == "0")) {$intSelHostgroup    = 0;}  else {$intSelHostgroup    = 1;}
if ($chkSelHostgroup[0] 	== "*") $intSelHostgroup = 2;
// 
// Add or modify data
// ==================
if (($chkModus == "insert") || ($chkModus == "modify")) {
  	if ($hidActive   == 1) $chkActive = 1;
  	if ($chkGroupAdm == 1) {$strGroupSQL = "`access_group`=$chkSelAccessGroup, ";} else {$strGroupSQL = "";}
  	$strSQLx = "`tbl_hostdependency` SET `config_name`='$chkTfConfigName', `dependent_host_name`=$intSelHostDepend, `host_name`=$intSelHost,
        		`dependent_hostgroup_name`=$intSelHostgroupDep, `hostgroup_name`=$intSelHostgroup, `inherits_parent`='$chkInherit',
        		`execution_failure_criteria`='$strEO', `notification_failure_criteria`='$strNO', `dependency_period`=$chkSelDependPeriod,
        		$strGroupSQL `active`='$chkActive', `config_id`=$chkDomainId, `last_modified`=NOW()";
  	if ($chkModus == "insert") {
    	$strSQL = "INSERT INTO ".$strSQLx;
  	} else {
    	$strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
  	}
  	if ((($intSelHostDepend != 0) && ($intSelHost != 0)) || (($intSelHostgroupDep != 0) && ($intSelHostgroup != 0)) ||
       (($intSelHostDepend != 0) && ($intSelHostgroup != 0)) || (($intSelHostgroupDep != 0) && ($intSelHost != 0))) {
    	$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
		$myDataClass->updateStatusTable("tbl_hostdependency");
    	if ($chkModus == "insert") $chkDataId = $intInsertId;
    	if ($intInsert == 1) {
      		$intReturn = 1;
    	} else {
      		if ($chkModus == "insert") $myDataClass->writeLog(translate('New host dependency inserted:')." ".$chkTfConfigName);
      		if ($chkModus == "modify") $myDataClass->writeLog(translate('Host dependency modified:')." ".$chkTfConfigName);
			//
      		// Insert/update relations
      		// =======================
      		if ($chkModus == "insert") {
        		if ($intSelHostDepend   != 0) $myDataClass->dataInsertRelation("tbl_lnkHostdependencyToHost_DH",$chkDataId,$chkSelHostDepend);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHost     	!= 0) $myDataClass->dataInsertRelation("tbl_lnkHostdependencyToHost_H",$chkDataId,$chkSelHost);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHostgroupDep != 0) $myDataClass->dataInsertRelation("tbl_lnkHostdependencyToHostgroup_DH",$chkDataId,$chkSelHostgroupDep);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHostgroup  	!= 0) $myDataClass->dataInsertRelation("tbl_lnkHostdependencyToHostgroup_H",$chkDataId,$chkSelHostgroup);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		} else if ($chkModus == "modify") {
        		if ($intSelHostDepend != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkHostdependencyToHost_DH",$chkDataId,$chkSelHostDepend);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkHostdependencyToHost_DH",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHost != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkHostdependencyToHost_H",$chkDataId,$chkSelHost);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkHostdependencyToHost_H",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHostgroupDep != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkHostdependencyToHostgroup_DH",$chkDataId,$chkSelHostgroupDep);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkHostdependencyToHostgroup_DH",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHostgroup != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkHostdependencyToHostgroup_H",$chkDataId,$chkSelHostgroup);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkHostdependencyToHostgroup_H",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		}
			//
			// Update Import HASH
			// ==================
			$booReturn = $myDataClass->updateHash('tbl_hostdependency',$chkDataId);
			$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		$intReturn = 0;
    	}
  	} else {
    	$myVisClass->processMessage(translate('Database entry failed! Not all necessary data filled in!'),$strMessage);
  	}

	$chkModus = "display";
} else if ($chkModus == "make") {
	// Write configuration file
  	$intReturn   = $myConfigClass->createConfig("tbl_hostdependency",0);
	$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "info")) {
	// Display additional relation information
  	$myDataClass->infoRelation("tbl_hostdependency",$chkListId,"config_name");
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
 	$intReturn   = 0;
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Delete selected datasets
  	$intReturn 	 = $myDataClass->dataDeleteFull("tbl_hostdependency",$chkListId);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus  	 = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Copy selected datasets
    $intReturn   = $myDataClass->dataCopyEasy("tbl_hostdependency","config_name",$chkListId,$chkSelTargetDomain);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
    $chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "activate")) {
	// Activate selected datasets
	$intReturn   = $myDataClass->dataActivate("tbl_hostdependency",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "deactivate")) {
	// Deactivate selected datasets
	$intReturn   = $myDataClass->dataDeactivate("tbl_hostdependency",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus    = "display"; 
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Open a dataset to modify
	$booReturn   = $myDBClass->getSingleDataset("SELECT * FROM `tbl_hostdependency` WHERE `id`=".$chkListId,$arrModifyData);
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
$myConfigClass->lastModified("tbl_hostdependency",$strLastModified,$strFileDate,$strOld);
$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Start content
// =============
$conttp->setVariable("TITLE",translate('Define host dependencies (hostdependencies.cfg)'));
$conttp->parse("header");
$conttp->show("header");
//
// Singe data form
// ===============
if ($chkModus == "add") {
  	// Process host selection field
  	$intReturn1 = 0;
  	$intReturn2 = 0;
	if (isset($arrModifyData['dependent_host_name'])) {$intFieldId = $arrModifyData['dependent_host_name'];} else {$intFieldId = 0;}
	$intReturn1 = $myVisClass->parseSelectMulti('tbl_host','host_name','depend_host','tbl_lnkHostdependencyToHost_DH',2,$intFieldId);
	if (isset($arrModifyData['host_name'])) {$intFieldId = $arrModifyData['host_name'];} else {$intFieldId = 0;}
	$intReturn3 = $myVisClass->parseSelectMulti('tbl_host','host_name','host','tbl_lnkHostdependencyToHost_H',2,$intFieldId);
  	// Process time period selection field
  	if (isset($arrModifyData['dependency_period'])) {$intFieldId = $arrModifyData['dependency_period'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectSimple('tbl_timeperiod','timeperiod_name','timeperiod',1,$intFieldId);
  	// Process host group selection field
  	$intReturn3 = 0;
  	$intReturn4 = 0;
	if (isset($arrModifyData['dependent_hostgroup_name'])) {$intFieldId = $arrModifyData['dependent_hostgroup_name'];} else {$intFieldId = 0;}
	$intReturn1 = $myVisClass->parseSelectMulti('tbl_hostgroup','hostgroup_name','depend_hostgroup','tbl_lnkHostdependencyToHostgroup_DH',2,$intFieldId);
	if (isset($arrModifyData['hostgroup_name'])) {$intFieldId = $arrModifyData['hostgroup_name'];} else {$intFieldId = 0;}
	$intReturn3 = $myVisClass->parseSelectMulti('tbl_hostgroup','hostgroup_name','hostgroup','tbl_lnkHostdependencyToHostgroup_H',2,$intFieldId);
	if (($intReturn2 != 0) && ($intReturn4 != 0)) $strDBWarning .= translate('Attention, no hosts and hostgroups defined!')."<br>";
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
    	if ($arrModifyData['active'] != 1) 			$conttp->setVariable("ACT_CHECKED","");
    	if ($arrModifyData['inherits_parent'] == 1) $conttp->setVariable("ACT_INHERIT","checked");
    	foreach(explode(",",$arrModifyData['execution_failure_criteria']) AS $elem) {
      		$conttp->setVariable("DAT_EO".strtoupper($elem)."_CHECKED","checked");
    	}
    	foreach(explode(",",$arrModifyData['notification_failure_criteria']) AS $elem) {
      		$conttp->setVariable("DAT_NO".strtoupper($elem)."_CHECKED","checked");
    	}
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
  	$mastertp->setVariable("FIELD_1",translate('Config name'));
  	$mastertp->setVariable("FIELD_2",translate('Dependent hosts')." / ".translate('Dependent hostgroups'));
  	$mastertp->setVariable("LIMIT",$chkLimit);
  	$mastertp->setVariable("ACTION_MODIFY",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
  	$mastertp->setVariable("TABLE_NAME","tbl_hostdependency");
  	$mastertp->setVariable("DAT_SEARCH",$_SESSION['search']['hostdependencies']);
  	// Get Group id's with READ
  	$strAccess = $myVisClass->getAccGroupRead($_SESSION['userid']);
	// Include domain list
	$myVisClass->insertDomainList($mastertp);
  	// Process filter string
  	$strSearchWhere = "";
 	if ($_SESSION['search']['hostdependencies'] != "") {
  		$strSearchTxt   = $_SESSION['search']['hostdependencies'];
  		$strSearchWhere = "AND (`config_name` LIKE '%".$strSearchTxt."%')";
  	}
  	// Count datasets
  	$strSQL    = "SELECT count(*) AS `number` FROM `tbl_hostdependency` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
  	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
  	if ($booReturn == false) {
    	$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
  	} else {
   		$intCount = (int)$arrDataLinesCount['number'];
  	}
  	// Get datasets
  	$strSQL    = "SELECT `id`, `config_name`, `dependent_host_name`, `dependent_hostgroup_name`, `active`, `config_id`  
				  FROM `tbl_hostdependency` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess) 
				  ORDER BY `config_id`, `config_name` LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
      		if ($arrDataLines[$i]['dependent_host_name'] != 0) {
        		$strSQLHost = "SELECT `host_name` FROM `tbl_host` LEFT JOIN `tbl_lnkHostdependencyToHost_DH` ON `id`=`idSlave`
                 			   WHERE `idMaster`=".$arrDataLines[$i]['id'];
        		$booReturn 	= $myDBClass->getDataArray($strSQLHost,$arrDataHosts,$intDCHost);
				if ($intDCHost != 0) {
					foreach($arrDataHosts AS $elem) {
						$strDataline .= $elem['host_name'].",";
					}
				}
      		} else {
        		$strSQLHost = "SELECT `hostgroup_name` FROM `tbl_hostgroup` LEFT JOIN `tbl_lnkHostdependencyToHostgroup_DH` ON `id`=`idSlave`
                 			   WHERE `idMaster`=".$arrDataLines[$i]['id'];
        		$booReturn 	= $myDBClass->getDataArray($strSQLHost,$arrDataHostgroups,$intDCHostgroup);
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