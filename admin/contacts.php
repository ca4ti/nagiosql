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
// Component : Contact definitions
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
$intSub       	= 5;
$intMenu      	= 2;
$preContent   	= "admin/contacts.tpl.htm";
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
$chkTfSearch        	= isset($_POST['txtSearch'])      			? $_POST['txtSearch']             	: "";
$chkTfName				= isset($_POST['tfName'])					? $_POST['tfName']					: "";
$chkTfFriendly			= isset($_POST['tfFriendly'])				? $_POST['tfFriendly']				: "";
$chkSelContactGroup		= isset($_POST['selContactGroup'])			? $_POST['selContactGroup']			: array("");
$chkRadContactGroup		= isset($_POST['radContactGroup'])			? $_POST['radContactGroup']+0		: 2;
$chkHostNotifEnable		= isset($_POST['radHostNotifEnable'])		? $_POST['radHostNotifEnable']+0	: 2;
$chkServiceNotifEnable	= isset($_POST['radServiceNotifEnable'])	? $_POST['radServiceNotifEnable']+0	: 2;
$chkSelHostPeriod		= isset($_POST['selHostPeriod'])			? $_POST['selHostPeriod']+0			: 0;
$chkSelServicePeriod	= isset($_POST['selServicePeriod'])			? $_POST['selServicePeriod']+0		: 0;
$chkSelHostCommand		= isset($_POST['selHostCommand'])			? $_POST['selHostCommand']			: array("");
$chkRadHostCommand		= isset($_POST['radHostCommand'])			? $_POST['radHostCommand']+0		: 2;
$chkSelServiceCommand	= isset($_POST['selServiceCommand'])		? $_POST['selServiceCommand']		: array("");
$chkRadServiceCommand	= isset($_POST['radServiceCommand'])		? $_POST['radServiceCommand']+0		: 2;
$chkRetStatInf			= isset($_POST['radRetStatInf'])			? $_POST['radRetStatInf']+0			: 2;
$chkRetNonStatInf		= isset($_POST['radRetNonStatInf'])			? $_POST['radRetNonStatInf']+0		: 2;
$chkCanSubCmds			= isset($_POST['radCanSubCmds'])			? $_POST['radCanSubCmds']+0			: 2;
$chkTfEmail				= isset($_POST['tfEmail'])					? $_POST['tfEmail']					: "";
$chkTfPager				= isset($_POST['tfPager'])					? $_POST['tfPager']					: "";
$chkTfAddress1			= isset($_POST['tfAddress1'])				? $_POST['tfAddress1']				: "";
$chkTfAddress2			= isset($_POST['tfAddress2'])				? $_POST['tfAddress2']				: "";
$chkTfAddress3			= isset($_POST['tfAddress3'])				? $_POST['tfAddress3']				: "";
$chkTfAddress4			= isset($_POST['tfAddress4'])				? $_POST['tfAddress4']				: "";
$chkTfAddress5			= isset($_POST['tfAddress5'])				? $_POST['tfAddress5']				: "";
$chkTfAddress6			= isset($_POST['tfAddress6'])				? $_POST['tfAddress6']				: "";
$chkTfGeneric			= isset($_POST['tfGenericName'])			? $_POST['tfGenericName']			: "";
$chbHOd3				= isset($_POST['chbHOd3'])					? $_POST['chbHOd3'].","				: "";
$chbHOu3				= isset($_POST['chbHOu3'])					? $_POST['chbHOu3'].","				: "";
$chbHOr3				= isset($_POST['chbHOr3'])					? $_POST['chbHOr3'].","				: "";
$chbHOf3				= isset($_POST['chbHOf3'])					? $_POST['chbHOf3'].","				: "";
$chbHOs3				= isset($_POST['chbHOs3'])					? $_POST['chbHOs3'].","				: "";
$chbHOn3				= isset($_POST['chbHOn3'])					? $_POST['chbHOn3'].","				: "";
$chbHOnull3				= isset($_POST['chbHOnull3'])				? $_POST['chbHOnull3'].","			: "";
$chbSOw3				= isset($_POST['chbSOw3'])					? $_POST['chbSOw3'].","				: "";
$chbSOu3				= isset($_POST['chbSOu3'])					? $_POST['chbSOu3'].","				: "";
$chbSOc3				= isset($_POST['chbSOc3'])					? $_POST['chbSOc3'].","				: "";
$chbSOr3				= isset($_POST['chbSOr3'])					? $_POST['chbSOr3'].","				: "";
$chbSOf3				= isset($_POST['chbSOf3'])					? $_POST['chbSOf3'].","				: "";
$chbSOs3				= isset($_POST['chbSOs3'])					? $_POST['chbSOs3'].","				: "";
$chbSOn3				= isset($_POST['chbSOn3'])					? $_POST['chbSOn3'].","				: "";
$chbSOnull3				= isset($_POST['chbSOnull3'])				? $_POST['chbSOnull3'].","			: "";
$chbHOd2				= isset($_POST['chbHOd2'])					? $_POST['chbHOd2'].","				: "";
$chbHOu2				= isset($_POST['chbHOu2'])					? $_POST['chbHOu2'].","				: "";
$chbHOr2				= isset($_POST['chbHOr2'])					? $_POST['chbHOr2'].","				: "";
$chbHOf2				= isset($_POST['chbHOf2'])					? $_POST['chbHOf2'].","				: "";
$chbHOn2				= isset($_POST['chbHOn2'])					? $_POST['chbHOn2'].","				: "";
$chbSOw2				= isset($_POST['chbSOw2'])					? $_POST['chbSOw2'].","				: "";
$chbSOu2				= isset($_POST['chbSOu2'])					? $_POST['chbSOu2'].","				: "";
$chbSOc2				= isset($_POST['chbSOc2'])					? $_POST['chbSOc2'].","				: "";
$chbSOr2				= isset($_POST['chbSOr2'])					? $_POST['chbSOr2'].","				: "";
$chbSOf2				= isset($_POST['chbSOf2'])					? $_POST['chbSOf2'].","				: "";
$chbSOn2				= isset($_POST['chbSOn2'])					? $_POST['chbSOn2'].","				: "";
$chkRadTemplates		= isset($_POST['radTemplate'])				? $_POST['radTemplate']+0			: 2;
$chkSelAccessGroup		= isset($_POST['selAccessGroup'])			? $_POST['selAccessGroup']+0		: 0;
//
// Search/Filter - Session data
// ============================
if (!isset($_SESSION['search']) || !isset($_SESSION['search']['contact'])) $_SESSION['search']['contact'] = "";
if (($chkModus == "checkform") || ($chkModus == "filter")) {
  $_SESSION['search']['contact'] = $chkTfSearch;
}
//
// Quote special characters
// ==========================
if (get_magic_quotes_gpc() == 0) {
  $chkTfSearch		= addslashes($chkTfSearch);
  $chkTfName		= addslashes($chkTfName);
  $chkTfFriendly	= addslashes($chkTfFriendly);
  $chkTfEmail		= addslashes($chkTfEmail);
  $chkTfPager		= addslashes($chkTfPager);
  $chkTfAddress1	= addslashes($chkTfAddress1);
  $chkTfAddress2	= addslashes($chkTfAddress2);
  $chkTfAddress3	= addslashes($chkTfAddress3);
  $chkTfAddress4	= addslashes($chkTfAddress4);
  $chkTfAddress5	= addslashes($chkTfAddress5);
  $chkTfAddress6	= addslashes($chkTfAddress6);
  $chkTfGeneric		= addslashes($chkTfGeneric);
}
//
// Process additional templates/variables
// ======================================
if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition']) && (count($_SESSION['templatedefinition']) != 0)) {
  $intTemplates = 1;
} else {
  $intTemplates = 0;
}
if (isset($_SESSION['variabledefinition']) && is_array($_SESSION['variabledefinition']) && (count($_SESSION['variabledefinition']) != 0)) {
  $intVariables = 1;
} else {
  $intVariables = 0;
}
//
// Data processing
// ===============
if ($intVersion == 3) {
  $strHO = substr($chbHOd3.$chbHOu3.$chbHOr3.$chbHOf3.$chbHOs3.$chbHOn3,0,-1);
  $strSO = substr($chbSOw3.$chbSOu3.$chbSOc3.$chbSOr3.$chbSOf3.$chbSOs3.$chbSOn3,0,-1);
} else {
  $strHO = substr($chbHOd2.$chbHOu2.$chbHOr2.$chbHOf2.$chbHOn2,0,-1);
  $strSO = substr($chbSOw2.$chbSOu2.$chbSOc2.$chbSOr2.$chbSOf2.$chbSOn2,0,-1);
}
if (($chkSelContactGroup[0]   == "")  || ($chkSelContactGroup[0]   == "0")) {$intContactGroups  = 0;} else {$intContactGroups  = 1;}
if ($chkSelContactGroup[0]    == "*") $intContactGroups  = 2;
if (($chkSelHostCommand[0]    == "")  || ($chkSelHostCommand[0]    == "0")) {$intHostCommand    = 0;} else {$intHostCommand    = 1;}
if ($chkSelHostCommand[0]     == "*") $intHostCommand    = 2;
if (($chkSelServiceCommand[0] == "")  || ($chkSelServiceCommand[0] == "0")) {$intServiceCommand = 0;} else {$intServiceCommand = 1;}
if ($chkSelServiceCommand[0]  == "*") $intServiceCommand = 2;
// 
// Add or modify data
// ==================
if (($chkModus == "insert") || ($chkModus == "modify")) {
  	if ($hidActive   == 1) $chkActive = 1;
	if ($chkGroupAdm == 1) 		{$strGroupSQL 	= "`access_group`=$chkSelAccessGroup, ";} 	else {$strGroupSQL 	= "";}
	if ($chkModus == "insert") 	{$strDomain 	= "`config_id`=$chkDomainId, ";} 			else {$strDomain 	= "";}
  	$strSQLx = "`tbl_contact` SET `contact_name`='$chkTfName', `alias`='$chkTfFriendly', `contactgroups`=$intContactGroups,
        	  	`contactgroups_tploptions`=$chkRadContactGroup, `host_notifications_enabled`='$chkHostNotifEnable',
        	  	`service_notifications_enabled`='$chkServiceNotifEnable', `host_notification_period`='$chkSelHostPeriod',
        	  	`service_notification_period`='$chkSelServicePeriod', `host_notification_options`='$strHO',
        	  	`host_notification_commands_tploptions`=$chkRadHostCommand, `service_notification_options`='$strSO',
        	  	`host_notification_commands`=$intHostCommand, `service_notification_commands`=$intServiceCommand,
        	  	`service_notification_commands_tploptions`=$chkRadServiceCommand, `can_submit_commands`='$chkCanSubCmds ',
        	  	`retain_status_information`='$chkRetStatInf', `retain_nonstatus_information`='$chkRetNonStatInf', `email`='$chkTfEmail',
        	  	`pager`='$chkTfPager', `address1`='$chkTfAddress1', `address2`='$chkTfAddress2', `address3`='$chkTfAddress3',
        	  	`address4`='$chkTfAddress4', `address5`='$chkTfAddress5', `address6`='$chkTfAddress6', `name`='$chkTfGeneric',
        	  	`use_variables`='$intVariables', `use_template`=$intTemplates, `use_template_tploptions`=$chkRadTemplates,
        	  	`active`='$chkActive', $strDomain $strGroupSQL `last_modified`=NOW()";
	if ($chkModus == "insert") {
    	$strSQL = "INSERT INTO ".$strSQLx;
  	} else {
    	$strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
  	}
  	if ($chkTfName != "") {
    	$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
		$myDataClass->updateStatusTable("tbl_contact");
    	if ($chkModus == "insert") $chkDataId = $intInsertId;
    	if ($intInsert == 1) {
     		$intReturn = 1;
   		} else {
      		if ($chkModus  == "insert") $myDataClass->writeLog(translate('New contact inserted:')." ".$chkTfName);
      		if ($chkModus  == "modify") $myDataClass->writeLog(translate('Contact modified:')." ".$chkTfName);
      		//
      		// Insert/update relations
      		// =======================
      		if ($chkModus == "insert") {
				if ($intContactGroups  != 0) $myDataClass->dataInsertRelation("tbl_lnkContactToContactgroup",$chkDataId,$chkSelContactGroup);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intHostCommand    != 0) $myDataClass->dataInsertRelation("tbl_lnkContactToCommandHost",$chkDataId,$chkSelHostCommand);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intServiceCommand != 0) $myDataClass->dataInsertRelation("tbl_lnkContactToCommandService",$chkDataId,$chkSelServiceCommand);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		} else if ($chkModus == "modify") {
				if ($intContactGroups != 0) {
					$myDataClass->dataUpdateRelation("tbl_lnkContactToContactgroup",$chkDataId,$chkSelContactGroup);
				} else {
					$myDataClass->dataDeleteRelation("tbl_lnkContactToContactgroup",$chkDataId);
				}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
				if ($intHostCommand != 0) {
					$myDataClass->dataUpdateRelation("tbl_lnkContactToCommandHost",$chkDataId,$chkSelHostCommand);
				} else {
					$myDataClass->dataDeleteRelation("tbl_lnkContactToCommandHost",$chkDataId);
				}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
				if ($intServiceCommand != 0) {
					$myDataClass->dataUpdateRelation("tbl_lnkContactToCommandService",$chkDataId,$chkSelServiceCommand);
				} else {
					$myDataClass->dataDeleteRelation("tbl_lnkContactToCommandService",$chkDataId);
				}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
			}
			//
			// Insert/update templates from session data
			// =========================================
			if ($chkModus == "modify") {
				$strSQL     = "DELETE FROM `tbl_lnkContactToContacttemplate` WHERE `idMaster`=$chkDataId";
				$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
		  	}
		  	if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition']) && (count($_SESSION['templatedefinition']) != 0)) {
				$intSortId = 1;
				foreach($_SESSION['templatedefinition'] AS $elem) {
			  		if ($elem['status'] == 0) {
						$strSQL     = "INSERT INTO `tbl_lnkContactToContacttemplate` (`idMaster`,`idSlave`,`idTable`,`idSort`)
						   		       VALUES ($chkDataId,".$elem['idSlave'].",".$elem['idTable'].",".$intSortId.")";
						$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
						$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
			  		}
			  		$intSortId++;
				}
		  	}
      		//
      		// Insert/update variables from session data
      		// =========================================
      		if ($chkModus == "modify") {
        		$strSQL     = "SELECT * FROM `tbl_lnkContactToVariabledefinition` WHERE `idMaster`=$chkDataId";
        		$booReturn  = $myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intDataCount != 0) {
          			foreach ($arrData AS $elem) {
            			$strSQL     = "DELETE FROM `tbl_variabledefinition` WHERE `id`=".$elem['idSlave'];
            			$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
						$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
          			}
        		}
        		$strSQL     = "DELETE FROM `tbl_lnkContactToVariabledefinition` WHERE `idMaster`=$chkDataId";
        		$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		}
      		if (isset($_SESSION['variabledefinition']) && is_array($_SESSION['variabledefinition']) && (count($_SESSION['variabledefinition']) != 0)) {
        		foreach($_SESSION['variabledefinition'] AS $elem) {
          			if ($elem['status'] == 0) {
            			$strSQL     = "INSERT INTO `tbl_variabledefinition` (`name`,`value`,`last_modified`)
                   				   	   VALUES ('".$elem['definition']."','".$elem['range']."',now())";
            			$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
						$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
            			$strSQL     = "INSERT INTO `tbl_lnkContactToVariabledefinition` (`idMaster`,`idSlave`)
                   					   VALUES ($chkDataId,$intInsertId)";
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
}  else if ($chkModus == "make") {
  	// Write configuration file
  	$intReturn   = $myConfigClass->createConfig("tbl_contact",0);
    $myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "info")) {
  	// Display additional relation information
  	$myDataClass->infoRelation("tbl_contact",$chkListId,"contact_name");
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$intReturn   = 0;
  	$chkModus    = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
  	// Delete selected datasets
  	$intReturn   = $myDataClass->dataDeleteFull("tbl_contact",$chkListId);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
  	// Copy selected datasets
  	$intReturn   = $myDataClass->dataCopyEasy("tbl_contact","contact_name",$chkListId,$chkSelTargetDomain);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "activate")) {
  	// Activate selected datasets
  	$intReturn   = $myDataClass->dataActivate("tbl_contact",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "deactivate")) {
  	// Deactivate selected datasets
  	$intReturn   = $myDataClass->dataDeactivate("tbl_contact",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display"; 
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
  	// Open a dataset to modify
  	$booReturn   = $myDBClass->getSingleDataset("SELECT * FROM `tbl_contact` WHERE `id`=".$chkListId,$arrModifyData);
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
$myConfigClass->lastModified("tbl_contact",$strLastModified,$strFileDate,$strOld);
$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage); 
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Start content
// =============
$conttp->setVariable("TITLE",translate("Define contacts (contacts.cfg)"));
$conttp->parse("header");
$conttp->show("header");
//
// Singe data form
// ===============
if ($chkModus == "add") {
  	// Process template selection fields (Spezial)
  	$strWhere = "";
  	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
    	$strWhere = "AND `id` <> ".$arrModifyData['id'];
  	}
  	$strSQL     = "SELECT `id`,`template_name` FROM `tbl_contacttemplate` WHERE $strDomainWhere ORDER BY `template_name`";
  	$booReturn  = $myDBClass->getDataArray($strSQL,$arrDataTpl,$intDataCountTpl);
  	if ($intDataCountTpl != 0) {
    	foreach ($arrDataTpl AS $elem) {
      		$conttp->setVariable("DAT_TEMPLATE",$elem['template_name']);
      		$conttp->setVariable("DAT_TEMPLATE_ID",$elem['id']."::1");
      		$conttp->parse("template");
    	}
  	}
  	$strSQL     = "SELECT `id`, `name` FROM `tbl_contact` WHERE `name` <> '' $strWhere AND $strDomainWhere ORDER BY `name`";
  	$booReturn  = $myDBClass->getDataArray($strSQL,$arrDataHpl,$intDataCount);
  	if ($arrDataHpl != 0) {
    	foreach ($arrDataHpl AS $elem) {
      		$conttp->setVariable("DAT_TEMPLATE",$elem['name']);
      		$conttp->setVariable("DAT_TEMPLATE_ID",$elem['id']."::2");
      		$conttp->parse("template");
    	}
  	}
	// Process timeperiod selection fields
	$intReturn = 0;
	if (isset($arrModifyData['host_notification_period'])) {$intFieldId = $arrModifyData['host_notification_period'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectSimple('tbl_timeperiod','timeperiod_name','host_time',1,$intFieldId);
	if (isset($arrModifyData['service_notification_period'])) {$intFieldId = $arrModifyData['service_notification_period'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectSimple('tbl_timeperiod','timeperiod_name','service_time',1,$intFieldId);
	if ($intReturn != 0) $strDBWarning .= translate('Attention, no time periods defined!')."<br>";
	// Process command selection fields
	if (isset($arrModifyData['host_notification_commands'])) {$intFieldId = $arrModifyData['host_notification_commands'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectMulti('tbl_command','command_name','host_command','tbl_lnkContactToCommandHost',0,$intFieldId);
	if (isset($arrModifyData['service_notification_commands'])) {$intFieldId = $arrModifyData['service_notification_commands'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectMulti('tbl_command','command_name','service_command','tbl_lnkContactToCommandService',0,$intFieldId);
	if ($intReturn != 0) $strDBWarning .= translate('Attention, no commands defined!')."<br>";
	// Process contactgroup selection field
	if (isset($arrModifyData['contactgroups'])) {$intFieldId = $arrModifyData['contactgroups'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectMulti('tbl_contactgroup','contactgroup_name','contactgroup','tbl_lnkContactToContactgroup',2,$intFieldId);
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
	$conttp->setVariable("RELATION_CLASS","elementHide");
	$conttp->setVariable("SELECT_FIELD_DISABLED","disabled");
	if ($SETS['common']['seldisable'] == 0)$conttp->setVariable("SELECT_FIELD_DISABLED","enabled");
	if ($SETS['common']['tplcheck'] == 0) $conttp->setVariable("CHECK_BYPASS","return true;");
	if ($chkGroupAdm == 0) $conttp->setVariable("RESTRICT_GROUP_ADMIN","class=\"elementHide\"");
	// Process additional fields based on nagios version
	if ($intVersion == 3) {
    	$conttp->setVariable("CLASS_NAME_20","elementHide");
    	$conttp->setVariable("CLASS_NAME_30","elementShow");
    	$conttp->setVariable("HOST_OPTION_FIELDS","chbHOd3,chbHOu3,chbHOr3,chbHOf3,chbHOs3,chbHOn3");
    	$conttp->setVariable("SERVICE_OPTION_FIELDS","chbSOw3,chbSOu3,chbSOc3,chbSOr3,chbSOf3,chbSOs3,chbSOn3");
		$conttp->setVariable("VERSION","3");
  	} else {
    	$conttp->setVariable("CLASS_NAME_20","elementShow");
    	$conttp->setVariable("CLASS_NAME_30","elementHide");
    	$conttp->setVariable("HOST_OPTION_FIELDS","chbHOd2,chbHOu2,chbHOr2,chbHOf2,chbHOn2");
    	$conttp->setVariable("SERVICE_OPTION_FIELDS","chbSOw2,chbSOu2,chbSOc2,chbSOr2,chbSOf2,chbSOn2");
    	$conttp->setVariable("FRIENDLY_20_MUST",",tfFriendly");
    	$conttp->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
    	$conttp->setVariable("CLASS_20_MUST_STAR","*");
		$conttp->setVariable("VERSION","2");
  	}
  	// Process status fields
  	$strStatusfelder = "HNE,SNE,RSI,CSC,RNS,TPL,SEC,HOC,COG";
  	foreach (explode(",",$strStatusfelder) AS $elem) {
    	$conttp->setVariable("DAT_".$elem."0_CHECKED","");
    	$conttp->setVariable("DAT_".$elem."1_CHECKED","");
    	$conttp->setVariable("DAT_".$elem."2_CHECKED","checked");
  	}
  	// Insert data from database in "modify" mode
  	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		foreach($arrModifyData AS $key => $value) {
      		if (($key == "active") || ($key == "last_modified") || ($key == "access_rights")) continue;
     		$conttp->setVariable("DAT_".strtoupper($key),htmlentities($value,ENT_QUOTES,'UTF-8'));
    	}
    	if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
    	// Process status fields
    	$strStatusfelder = "HNE,SNE,RSI,CSC,RNS,TPL,SEC,HOC,COG";
    	foreach (explode(",",$strStatusfelder) AS $elem) {
      		$conttp->setVariable("DAT_".$elem."0_CHECKED","");
      		$conttp->setVariable("DAT_".$elem."1_CHECKED","");
      		$conttp->setVariable("DAT_".$elem."2_CHECKED","");
    	}
    	$conttp->setVariable("DAT_HNE".$arrModifyData['host_notifications_enabled']."_CHECKED","checked");
    	$conttp->setVariable("DAT_SNE".$arrModifyData['service_notifications_enabled']."_CHECKED","checked");
    	$conttp->setVariable("DAT_RSI".$arrModifyData['retain_status_information']."_CHECKED","checked");
    	$conttp->setVariable("DAT_CSC".$arrModifyData['can_submit_commands']."_CHECKED","checked");
    	$conttp->setVariable("DAT_RNS".$arrModifyData['retain_nonstatus_information']."_CHECKED","checked");
    	$conttp->setVariable("DAT_TPL".$arrModifyData['use_template_tploptions']."_CHECKED","checked");
    	$conttp->setVariable("DAT_SEC".$arrModifyData['service_notification_commands_tploptions']."_CHECKED","checked");
    	$conttp->setVariable("DAT_HOC".$arrModifyData['host_notification_commands_tploptions']."_CHECKED","checked");
    	$conttp->setVariable("DAT_COG".$arrModifyData['contactgroups_tploptions']."_CHECKED","checked");
    	// Check relation information to find out locked configuration datasets
    	if ($myDataClass->infoRelation("tbl_contact",$arrModifyData['id'],"contact_name") != 0) {
      		$conttp->setVariable("ACT_DISABLED","disabled");
      		$conttp->setVariable("ACT_CHECKED","checked");
      		$conttp->setVariable("ACTIVE","1");
      		$strInfo = "<br><span class=\"dbmessage\">".translate('Entry cannot be deactivated because it is used by another configuration').":</span><br><span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
      		$conttp->setVariable("CHECK_MUST_DATA",$strInfo);
			$conttp->setVariable("RELATION_CLASS","elementShow");
    	}
    	// Process option fields
    	foreach(explode(",",$arrModifyData['host_notification_options']) AS $elem) {
      		$conttp->setVariable("DAT_HO".strtoupper($elem)."_CHECKED","checked");
    	}
    	foreach(explode(",",$arrModifyData['service_notification_options']) AS $elem) {
      		$conttp->setVariable("DAT_SO".strtoupper($elem)."_CHECKED","checked");
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
	$mastertp->setVariable("FIELD_1",translate('Contact name'));
	$mastertp->setVariable("FIELD_2",translate('Description'));
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("ACTION_MODIFY",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
	$mastertp->setVariable("TABLE_NAME","tbl_contact");
	$mastertp->setVariable("DAT_SEARCH",$_SESSION['search']['contact']);
	// Get Group id's with READ
	$strAccess = $myVisClass->getAccGroupRead($_SESSION['userid']);
	// Include domain list
	$myVisClass->insertDomainList($mastertp);
	// Process filter string
	$strSearchWhere = "";
	if ($_SESSION['search']['contact'] != "") {
		$strSearchTxt   = $_SESSION['search']['contact'];
		$strSearchWhere = "AND (`contact_name` LIKE '%".$strSearchTxt."%' OR `alias` LIKE '%".$strSearchTxt."%' OR
						  `email` LIKE '%".$strSearchTxt."%' OR `pager` LIKE '%".$strSearchTxt."%' OR
						  `address1` LIKE '%".$strSearchTxt."%' OR `address2` LIKE '%".$strSearchTxt."%' OR
						  `address3` LIKE '%".$strSearchTxt."%' OR `address4` LIKE '%".$strSearchTxt."%' OR
						  `address5` LIKE '%".$strSearchTxt."%' OR `address6` LIKE '%".$strSearchTxt."%' OR
						  `name` LIKE '%".$strSearchTxt."%')";
	}
	// Count datasets
	$strSQL    = "SELECT count(*) AS `number` FROM `tbl_contact` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {
		$myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError,$strMessage);
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
	// Get datasets
	$strSQL    = "SELECT `id`, `contact_name`, `alias`, `active`, `config_id` FROM `tbl_contact` WHERE $strDomainWhere $strSearchWhere 
				  AND `access_group` IN ($strAccess) ORDER BY `config_id`, `contact_name` LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
			$mastertp->setVariable("DATA_FIELD_1",htmlspecialchars($arrDataLines[$i]['contact_name'],ENT_COMPAT,'UTF-8'));
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
// Process footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>