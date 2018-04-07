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
// Component : Service dependencies definition
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
$intSub       	= 10;
$intMenu      	= 2;
$preContent   	= "admin/servicedependencies.tpl.htm";
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
$chkTfSearch    		= isset($_POST['txtSearch'])		? $_POST['txtSearch']			: "";
$chkSelHostDepend   	= isset($_POST['selHostDepend'])    ? $_POST['selHostDepend']       : array("");
$chkSelHostgroupDep   	= isset($_POST['selHostgroupDep'])  ? $_POST['selHostgroupDep']     : array("");
$chkSelServiceDepend  	= isset($_POST['selServiceDepend'])	? $_POST['selServiceDepend']    : array("");
$chkSelHost       		= isset($_POST['selHost'])        	? $_POST['selHost']           	: array("");
$chkSelHostgroup    	= isset($_POST['selHostgroup'])     ? $_POST['selHostgroup']        : array("");
$chkSelService      	= isset($_POST['selService'])       ? $_POST['selService']          : array("");
$chkTfConfigName    	= isset($_POST['tfConfigName'])     ? $_POST['tfConfigName']        : "";
$chkEOo         		= isset($_POST['chbEOo'])       	? $_POST['chbEOo'].","          : "";
$chkEOw         		= isset($_POST['chbEOw'])       	? $_POST['chbEOw'].","          : "";
$chkEOu         		= isset($_POST['chbEOu'])       	? $_POST['chbEOu'].","          : "";
$chkEOc         		= isset($_POST['chbEOc'])       	? $_POST['chbEOc'].","          : "";
$chkEOp         		= isset($_POST['chbEOp'])       	? $_POST['chbEOp'].","          : "";
$chkEOn         		= isset($_POST['chbEOn'])       	? $_POST['chbEOn'].","          : "";
$chkNOo         		= isset($_POST['chbNOo'])       	? $_POST['chbNOo'].","          : "";
$chkNOw         		= isset($_POST['chbNOw'])       	? $_POST['chbNOw'].","          : "";
$chkNOu         		= isset($_POST['chbNOu'])       	? $_POST['chbNOu'].","          : "";
$chkNOc         		= isset($_POST['chbNOc'])       	? $_POST['chbNOc'].","          : "";
$chkNOp         		= isset($_POST['chbNOp'])       	? $_POST['chbNOp'].","          : "";
$chkNOn         		= isset($_POST['chbNOn'])       	? $_POST['chbNOn'].","          : "";
$chkSelDependPeriod 	= isset($_POST['selDependPeriod'])  ? $_POST['selDependPeriod']+0   : 0;
$chkInherit       		= isset($_POST['chbInherit'])     	? $_POST['chbInherit']          : 0;
$chkSelAccessGroup		= isset($_POST['selAccessGroup'])	? $_POST['selAccessGroup']+0	: 0;
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
if (!isset($_SESSION['search']) || !isset($_SESSION['search']['servicedependencies'])) $_SESSION['search']['servicedependencies'] = "";
if (($chkModus == "checkform") || ($chkModus == "filter")) {
  	$_SESSION['search']['servicedependencies'] = $chkTfSearch;
}
//
// Data processing
// ===============
$strEO = substr($chkEOo.$chkEOw.$chkEOu.$chkEOc.$chkEOp.$chkEOn,0,-1);
$strNO = substr($chkNOo.$chkNOw.$chkNOu.$chkNOc.$chkNOp.$chkNOn,0,-1);
if (($chkSelHostDepend[0]      	== "") 	|| ($chkSelHostDepend[0]    == "0")) {$intSelHostDepend    	= 0;} else {$intSelHostDepend    = 1;}
if ($chkSelHostDepend[0]       	== "*") $intSelHostDepend 	 = 2;
if (($chkSelHostgroupDep[0]    	== "") 	|| ($chkSelHostgroupDep[0]  == "0")) {$intSelHostgroupDep   = 0;} else {$intSelHostgroupDep  = 1;}
if ($chkSelHostgroupDep[0]     	== "*") $intSelHostgroupDep  = 2;
if (($chkSelServiceDepend[0]	== "") 	|| ($chkSelServiceDepend[0] == "0")) {$intSelServiceDepend	= 0;} else {$intSelServiceDepend = 1;}
if ($chkSelServiceDepend[0]    	== "*") $intSelServiceDepend = 2;
if (($chkSelHost[0]        		== "") 	|| ($chkSelHost[0]      	== "0")) {$intSelHost        	= 0;} else {$intSelHost      	 = 1;}
if ($chkSelHost[0]        		== "*") $intSelHost 		 = 2;
if (($chkSelHostgroup[0]     	== "") 	|| ($chkSelHostgroup[0]     == "0")) {$intSelHostgroup     	= 0;} else {$intSelHostgroup     = 1;}
if ($chkSelHostgroup[0]        	== "*") $intSelHostgroup 	 = 2;
if (($chkSelService[0]       	== "") 	|| ($chkSelService[0]     	== "0")) {$intSelService     	= 0;} else {$intSelService     	 = 1;}
if ($chkSelService[0]        	== "*") $intSelService 		 = 2;
// 
// Add or modify data
// ==================
if (($chkModus == "insert") || ($chkModus == "modify")) {
	if ($hidActive   == 1) $chkActive = 1;
	if ($chkGroupAdm == 1) {$strGroupSQL = "`access_group`=$chkSelAccessGroup, ";} else {$strGroupSQL = "";}
  	$strSQLx = "`tbl_servicedependency` SET `dependent_host_name`=$intSelHostDepend, `dependent_hostgroup_name`=$intSelHostgroupDep,
        		`dependent_service_description`=$intSelServiceDepend, `host_name`=$intSelHost, `hostgroup_name`=$intSelHostgroup,
        		`service_description`=$intSelService, `config_name`='$chkTfConfigName', `inherits_parent`='$chkInherit',
        		`execution_failure_criteria`='$strEO', `notification_failure_criteria`='$strNO', `dependency_period`=$chkSelDependPeriod,
        		$strGroupSQL `active`='$chkActive', `config_id`=$chkDomainId, `last_modified`=NOW()";
  	if ($chkModus == "insert") {
    	$strSQL = "INSERT INTO ".$strSQLx;
  	} else {
    	$strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
  	}
	
  	if ((($intSelHost != 0) || ($intSelHostgroup != 0)) && (($intSelHostDepend != 0) || ($intSelHostgroupDep != 0)) &&
    	($intSelService != 0) && ($intSelServiceDepend != 0)) {
    	$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
		$myDataClass->updateStatusTable("tbl_servicedependency");
    	if ($chkModus == "insert") $chkDataId = $intInsertId;
    	if ($intInsert == 1) {
      		$intReturn = 1;
    	} else {
      		if ($chkModus == "insert") $myDataClass->writeLog(translate('New service dependency inserted:')." ".$chkTfConfigName);
      		if ($chkModus == "modify") $myDataClass->writeLog(translate('Service dependency modified:')." ".$chkTfConfigName);
      		//
      		// Insert/update relations
      		// =======================
      		if ($chkModus == "insert") {
        		if ($intSelHostDepend	 != 0) $myDataClass->dataInsertRelation("tbl_lnkServicedependencyToHost_DH",$chkDataId,$chkSelHostDepend);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHostgroupDep  != 0) $myDataClass->dataInsertRelation("tbl_lnkServicedependencyToHostgroup_DH",$chkDataId,$chkSelHostgroupDep);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelServiceDepend != 0) $myDataClass->dataInsertRelation("tbl_lnkServicedependencyToService_DS",$chkDataId,$chkSelServiceDepend);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHost       	 != 0) $myDataClass->dataInsertRelation("tbl_lnkServicedependencyToHost_H",$chkDataId,$chkSelHost);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHostgroup     != 0) $myDataClass->dataInsertRelation("tbl_lnkServicedependencyToHostgroup_H",$chkDataId,$chkSelHostgroup);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelService       != 0) $myDataClass->dataInsertRelation("tbl_lnkServicedependencyToService_S",$chkDataId,$chkSelService);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		} else if ($chkModus == "modify") {
        		if ($intSelHostDepend != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkServicedependencyToHost_DH",$chkDataId,$chkSelHostDepend);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkServicedependencyToHost_DH",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHostgroupDep != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkServicedependencyToHostgroup_DH",$chkDataId,$chkSelHostgroupDep);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkServicedependencyToHostgroup_DH",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelServiceDepend != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkServicedependencyToService_DS",$chkDataId,$chkSelServiceDepend);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkServicedependencyToService_DS",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHost != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkServicedependencyToHost_H",$chkDataId,$chkSelHost);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkServicedependencyToHost_H",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHostgroup != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkServicedependencyToHostgroup_H",$chkDataId,$chkSelHostgroup);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkServicedependencyToHostgroup_H",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelService != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkServicedependencyToService_S",$chkDataId,$chkSelService);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkServicedependencyToService_S",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		}
			//
			// Update Import HASH
			// ==================
			$booReturn = $myDataClass->updateHash('tbl_servicedependency',$chkDataId);
			$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		$intReturn = 0;
    	}
  	} else {
    	$myVisClass->processMessage(translate('Database entry failed! Not all necessary data filled in!'),$strMessage);
  	}
  	$chkModus = "display";
} else if ($chkModus == "make") {
	// Write configuration file
  	$intReturn   = $myConfigClass->createConfig("tbl_servicedependency",0);
	$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "info")) {
	// Display additional relation information
  	$myDataClass->infoRelation("tbl_servicedependency",$chkListId,"config_name");
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$intReturn   = 0;
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Delete selected datasets
  	$intReturn   = $myDataClass->dataDeleteFull("tbl_servicedependency",$chkListId);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Copy selected datasets
  	$intReturn   = $myDataClass->dataCopyEasy("tbl_servicedependency","config_name",$chkListId,$chkSelTargetDomain);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "activate")) {
	// Activate selected datasets
	$intReturn   = $myDataClass->dataActivate("tbl_servicedependency",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "deactivate")) {
	// Deactivate selected datasets
	$intReturn   = $myDataClass->dataDeactivate("tbl_servicedependency",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus    = "display"; 
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Open a dataset to modify
	$booReturn   = $myDBClass->getSingleDataset("SELECT * FROM `tbl_servicedependency` WHERE `id`=".$chkListId,$arrModifyData);
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
} else if (($chkModus != "add") && ($chkModus != "refresh")) {
  $chkModus    = "display"; 
}
// Get status messages from database
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $strMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$strMessage."</span>";
//
// Get date/time of last database and config file manipulation
// ===========================================================
$myConfigClass->lastModified("tbl_servicedependency",$strLastModified,$strFileDate,$strOld);
$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Start content
// =============
$conttp->setVariable("TITLE",translate('Define service dependencies (servicedependencies.cfg)'));
$conttp->parse("header");
$conttp->show("header");
//
// Singe data form
// ===============
if (($chkModus == "add") || ($chkModus == "refresh")) {
	if ($chkModus == "refresh") {
    	$_SESSION['refresh']['sd_dependent_host'] 		= $chkSelHostDepend;
		$_SESSION['refresh']['sd_host']           		= $chkSelHost;
		$_SESSION['refresh']['sd_dependent_hostgroup'] 	= $chkSelHostgroupDep;
    	$_SESSION['refresh']['sd_hostgroup']       		= $chkSelHostgroup;
    	$_SESSION['refresh']['sd_dependent_service'] 	= $chkSelServiceDepend;
		$_SESSION['refresh']['sd_service']           	= $chkSelService;
  	} else {
    	$_SESSION['refresh']['sd_dependent_service'] 	= $chkSelServiceDepend;
		$_SESSION['refresh']['sd_service']           	= $chkSelService;
		$_SESSION['refresh']['sd_dependent_host'] 		= $chkSelHostDepend;
		$_SESSION['refresh']['sd_host']           		= $chkSelHost;
		$_SESSION['refresh']['sd_dependent_hostgroup'] 	= $chkSelHostgroupDep;
    	$_SESSION['refresh']['sd_hostgroup']       		= $chkSelHostgroup;
		if (isset($arrModifyData['dependent_host_name']) && ($arrModifyData['dependent_host_name'] > 0 )) {
			$strSQL   	= "SELECT `idSlave`, `exclude`  FROM `tbl_lnkServicedependencyToHost_DH` WHERE `idMaster` = ".$arrModifyData['id'];
			$booReturn  = $myDBClass->getDataArray($strSQL,$arrData,$intDC);
			$myVisClass->processMessage($myDBClass->strDBError,$strMessage);
			if ($booReturn && ($intDC != 0)) {
				$arrTemp = "";	
				foreach ($arrData AS $elem) {
					if ($elem['exclude'] == 1) {
						$arrTemp[] = "e".$elem['idSlave'];
					} else {
						$arrTemp[] = $elem['idSlave'];
					}
        		}
				if ($arrModifyData['dependent_host_name'] == 2) $arrTemp[] = '*';
				$_SESSION['refresh']['sd_dependent_host'] = $arrTemp;
			}
		}
		if (isset($arrModifyData['host_name']) && ($arrModifyData['host_name'] > 0 )){
		  	$strSQL   	= "SELECT `idSlave`, `exclude` FROM `tbl_lnkServicedependencyToHost_H` WHERE `idMaster` = ".$arrModifyData['id'];
		  	$booReturn  = $myDBClass->getDataArray($strSQL,$arrData,$intDC);
			$myVisClass->processMessage($myDBClass->strDBError,$strMessage);
		  	if ($intDC != 0) {
				$arrTemp = "";
				foreach ($arrData AS $elem) {
					if ($elem['exclude'] == 1) {
						$arrTemp[] = "e".$elem['idSlave'];
					} else {
						$arrTemp[] = $elem['idSlave'];
					}
        		}
				if ($arrModifyData['host_name'] == 2) $arrTemp[] = '*';
				$_SESSION['refresh']['sd_host'] = $arrTemp;
		  	}
		}
		if (isset($arrModifyData['dependent_hostgroup_name']) && ($arrModifyData['dependent_hostgroup_name'] > 0 )){
		  	$strSQL   	= "SELECT `idSlave`, `exclude`  FROM `tbl_lnkServicedependencyToHostgroup_DH` WHERE `idMaster` = ".$arrModifyData['id'];
		  	$booReturn  = $myDBClass->getDataArray($strSQL,$arrData,$intDC);
			$myVisClass->processMessage($myDBClass->strDBError,$strMessage);
		  	if ($intDC != 0) {
				$arrTemp = "";
				foreach ($arrData AS $elem) {
					if ($elem['exclude'] == 1) {
						$arrTemp[] = "e".$elem['idSlave'];
					} else {
						$arrTemp[] = $elem['idSlave'];
					}
        		}
				if ($arrModifyData['dependent_hostgroup_name'] == 2) $arrTemp[] = '*';
				$_SESSION['refresh']['sd_dependent_hostgroup']  = $arrTemp;
		  	}
		}
		if (isset($arrModifyData['hostgroup_name']) && ($arrModifyData['hostgroup_name'] > 0 )){
		  	$strSQL   = "SELECT `idSlave`, `exclude`  FROM `tbl_lnkServicedependencyToHostgroup_H` WHERE `idMaster` = ".$arrModifyData['id'];
		  	$booReturn  = $myDBClass->getDataArray($strSQL,$arrData,$intDC);
			$myVisClass->processMessage($myDBClass->strDBError,$strMessage);
		  	if ($intDC != 0) {
				$arrTemp = "";
				foreach ($arrData AS $elem) {
					if ($elem['exclude'] == 1) {
						$arrTemp[] = "e".$elem['idSlave'];
					} else {
						$arrTemp[] = $elem['idSlave'];
					}
        		}
				if ($arrModifyData['hostgroup_name'] == 2) $arrTemp[] = '*';
				$_SESSION['refresh']['sd_hostgroup']  = $arrTemp;
		  	}
		}
  	}
	// Process host selection field
  	$intReturn1 = 0;
	if (isset($arrModifyData['dependent_host_name'])) {$intFieldId = $arrModifyData['dependent_host_name'];} else {$intFieldId = 0;}
	if (($chkModus == "refresh") && (count($chkSelHostDepend) != 0)) {$strRefresh = 'sd_dependent_host';} else {$strRefresh = '';}
	$intReturn1 = $myVisClass->parseSelectMulti('tbl_host','host_name','dependent_host','tbl_lnkServicedependencyToHost_DH',2,$intFieldId,-9,$strRefresh);
	if (isset($arrModifyData['host_name'])) {$intFieldId = $arrModifyData['host_name'];} else {$intFieldId = 0;}
	if (($chkModus == "refresh") && (count($chkSelHost) != 0)) {$strRefresh = 'sd_host';} else {$strRefresh = '';}
	$intReturn1 = $myVisClass->parseSelectMulti('tbl_host','host_name','host','tbl_lnkServicedependencyToHost_H',2,$intFieldId,-9,$strRefresh);
	// Process time period selection field
  	if (isset($arrModifyData['dependency_period'])) {$intFieldId = $arrModifyData['dependency_period'];} else {$intFieldId = 0;}
	if ($chkModus == "refresh") {$intFieldId = $chkSelDependPeriod;}
  	$intReturn = $myVisClass->parseSelectSimple('tbl_timeperiod','timeperiod_name','timeperiod',1,$intFieldId);
	// Process host group selection field
  	$intReturn2 = 0;
	if (isset($arrModifyData['dependent_hostgroup_name'])) {$intFieldId = $arrModifyData['dependent_hostgroup_name'];} else {$intFieldId = 0;}
	if (($chkModus == "refresh") && (count($chkSelHostgroupDep) != 0)) {$strRefresh = 'sd_dependent_hostgroup';} else {$strRefresh = '';}
	$intReturn2 = $myVisClass->parseSelectMulti('tbl_hostgroup','hostgroup_name','dependent_hostgroup','tbl_lnkServicedependencyToHostgroup_DH',2,$intFieldId,-9,$strRefresh);
	if (isset($arrModifyData['hostgroup_name'])) {$intFieldId = $arrModifyData['hostgroup_name'];} else {$intFieldId = 0;}
	if (($chkModus == "refresh") && (count($chkSelHostgroup) != 0)) {$strRefresh = 'sd_hostgroup';} else {$strRefresh = '';}
	$intReturn2 = $myVisClass->parseSelectMulti('tbl_hostgroup','hostgroup_name','hostgroup','tbl_lnkServicedependencyToHostgroup_H',2,$intFieldId,-9,$strRefresh);
  	if (($intReturn1 != 0) && ($intReturn2 != 0)) $strDBWarning .= translate('Attention, no hosts and hostgroups defined!')."<br>";
  	// Process services selection field
	if (isset($arrModifyData['dependent_service_description'])) {$intFieldId = $arrModifyData['dependent_service_description'];} else {$intFieldId = 0;}
	if (($chkModus == "refresh") && (count($chkSelHostgroup) != 0)) {$strRefresh = 'sd_dependent_service';} else {$strRefresh = '';}
	$intReturn = $myVisClass->parseSelectMulti('tbl_service','service_description','dependent_service','tbl_lnkServicedependencyToService_DS',2,$intFieldId,-9,$strRefresh);
	if (isset($arrModifyData['service_description'])) {$intFieldId = $arrModifyData['service_description'];} else {$intFieldId = 0;}
	if (($chkModus == "refresh") && (count($chkSelHostgroup) != 0)) {$strRefresh = 'sd_service';} else {$strRefresh = '';}
	$intReturn = $myVisClass->parseSelectMulti('tbl_service','service_description','service','tbl_lnkServicedependencyToService_S',2,$intFieldId,-9,$strRefresh);
  	// Process access group selection field
  	if (isset($arrModifyData['access_group'])) {$intFieldId = $arrModifyData['access_group'];} else {$intFieldId = 0;}
	if ($chkModus == "refresh") {$intFieldId = $chkSelAccessGroup;}
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
  	if ($chkModus == "refresh") {
    	if ($chkTfConfigName != "") $conttp->setVariable("DAT_CONFIG_NAME",$chkTfConfigName);
    	foreach(explode(",",$strEO) AS $elem) {
      		$conttp->setVariable("DAT_EO".strtoupper($elem)."_CHECKED","checked");
    	}
    	foreach(explode(",",$strNO) AS $elem) {
      		$conttp->setVariable("DAT_NO".strtoupper($elem)."_CHECKED","checked");
    	}
    	if ($chkActive != 1)  $conttp->setVariable("ACT_CHECKED","");
    	if ($chkInherit == 1) $conttp->setVariable("ACT_INHERIT","checked");
    	if ($chkDataId != 0) {
      		$conttp->setVariable("DAT_ID",$chkDataId);
			$conttp->setVariable("MODUS",$chkModus);
    	}
  	// Insert data from database in "modify" mode
  	} else if (isset($arrModifyData) && ($chkSelModify == "modify")) {
    	foreach($arrModifyData AS $key => $value) {
      		if (($key == "active") || ($key == "last_modified")) continue;
      		$conttp->setVariable("DAT_".strtoupper($key),htmlentities($value,ENT_QUOTES,'UTF-8'));
    	}
		// Process option fields
    	foreach(explode(",",$arrModifyData['execution_failure_criteria']) AS $elem) {
      		$conttp->setVariable("DAT_EO".strtoupper($elem)."_CHECKED","checked");
    	}
    	foreach(explode(",",$arrModifyData['notification_failure_criteria']) AS $elem) {
      		$conttp->setVariable("DAT_NO".strtoupper($elem)."_CHECKED","checked");
    	}
    	if ($arrModifyData['inherits_parent'] == 1) $conttp->setVariable("ACT_INHERIT","checked");
    	if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
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
  	$mastertp->setVariable("FIELD_2",translate('Dependent services'));
  	$mastertp->setVariable("LIMIT",$chkLimit);
  	$mastertp->setVariable("ACTION_MODIFY",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
  	$mastertp->setVariable("TABLE_NAME","tbl_servicedependency");
  	$mastertp->setVariable("DAT_SEARCH",$_SESSION['search']['servicedependencies']);
  	// Get Group id's with READ
  	$strAccess = $myVisClass->getAccGroupRead($_SESSION['userid']);
	// Include domain list
	$myVisClass->insertDomainList($mastertp);
  	// Process filter string
  	$strSearchWhere = "";
 	if ($_SESSION['search']['servicedependencies'] != "") {
  		$strSearchTxt   = $_SESSION['search']['servicedependencies'];
  		$strSearchWhere = "AND (`config_name` LIKE '%".$strSearchTxt."%')";
  	}
  	// Count datasets
  	$strSQL    = "SELECT count(*) AS `number` FROM `tbl_servicedependency` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
  	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
  	if ($booReturn == false) {
    	$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
  	} else {
    	$intCount = (int)$arrDataLinesCount['number'];
  	}
  	// Get datasets
  	$strSQL    = "SELECT `id`, `config_name`, `dependent_service_description`, `active`, `config_id`  FROM `tbl_servicedependency`WHERE $strDomainWhere 
          		  $strSearchWhere AND `access_group` IN ($strAccess) ORDER BY `config_id`, `config_name` LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
      		$mastertp->setVariable("DATA_FIELD_1",htmlspecialchars(stripslashes($arrDataLines[$i]['config_name']),ENT_COMPAT,'UTF-8'));
      		$strDataline = "";
      		if ($arrDataLines[$i]['dependent_service_description'] != 0) {
        		$strSQLService 	= "SELECT `strSlave` FROM `tbl_lnkServicedependencyToService_DS` WHERE `idMaster`=".$arrDataLines[$i]['id'];
        		$booReturn 		= $myDBClass->getDataArray($strSQLService,$arrDataService,$intDCService);
        		if ($intDCService != 0) {
          			foreach($arrDataService AS $elem) {
            			$strDataline .= $elem['strSlave'].",";
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