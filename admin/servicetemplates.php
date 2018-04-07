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
// Component : Service template definition
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
$intMain      	= 2;
$intSub       	= 27;
$intMenu      	= 2;
$preContent   	= "admin/servicetemplates.tpl.htm";
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
$chkTfSearch      			= isset($_POST['txtSearch'])      			? $_POST['txtSearch']             		: "";
$chkTfName					= isset($_POST['tfName'])               	? $_POST['tfName']                 		: "";
$chkSelHosts             	= isset($_POST['selHosts'])             	? $_POST['selHosts']               		: array("");
$chkRadHosts             	= isset($_POST['radHosts'])             	? $_POST['radHosts']+0             		: 2;
$chkSelHostGroups        	= isset($_POST['selHostGroups'])        	? $_POST['selHostGroups']          		: array("");
$chkRadHostGroups        	= isset($_POST['radHostGroups'])        	? $_POST['radHostGroups']+0        		: 2;
$chkTfServiceDescription 	= isset($_POST['tfServiceDescription'])		? $_POST['tfServiceDescription']   		: "";
$chkTfDisplayName        	= isset($_POST['tfDisplayName'])        	? $_POST['tfDisplayName']          		: "";
$chkSelServiceGroups     	= isset($_POST['selServiceGroups'])     	? $_POST['selServiceGroups']       		: array("");
$chkRadServiceGroups     	= isset($_POST['radServiceGroups'])     	? $_POST['radServiceGroups']+0     		: 2;
$chkServiceCommand       	= isset($_POST['selServiceCommand'])    	? $_POST['selServiceCommand']+0    		: 0;
$chkTfArg1               	= isset($_POST['tfArg1'])               	? $_POST['tfArg1']                 		: "";
$chkTfArg2               	= isset($_POST['tfArg2'])               	? $_POST['tfArg2']                 		: "";
$chkTfArg3               	= isset($_POST['tfArg3'])               	? $_POST['tfArg3']                 		: "";
$chkTfArg4               	= isset($_POST['tfArg4'])               	? $_POST['tfArg4']                 		: "";
$chkTfArg5               	= isset($_POST['tfArg5'])               	? $_POST['tfArg5']                 		: "";
$chkTfArg6               	= isset($_POST['tfArg6'])               	? $_POST['tfArg6']                 		: "";
$chkTfArg7               	= isset($_POST['tfArg7'])               	? $_POST['tfArg7']                 		: "";
$chkTfArg8               	= isset($_POST['tfArg8'])               	? $_POST['tfArg8']                 		: "";
$chkRadTemplates         	= isset($_POST['radTemplate'])          	? $_POST['radTemplate']+0          		: 2;
$chkISo                  	= isset($_POST['chbISo'])               	? $_POST['chbISo'].","             		: "";
$chkISw                  	= isset($_POST['chbISw'])               	? $_POST['chbISw'].","             		: "";
$chkISu                  	= isset($_POST['chbISu'])               	? $_POST['chbISu'].","             		: "";
$chkISc                  	= isset($_POST['chbISc'])               	? $_POST['chbISc'].","             		: "";
$chkActiveChecks         	= isset($_POST['radActiveChecksEnabled'])   ? $_POST['radActiveChecksEnabled']+0    : 2;
$chkPassiveChecks        	= isset($_POST['radPassiveChecksEnabled'])  ? $_POST['radPassiveChecksEnabled']+0   : 2;
$chkParallelizeChecks    	= isset($_POST['radParallelizeChecks'])     ? $_POST['radParallelizeChecks']+0      : 2;
$chkSelCheckPeriod       	= isset($_POST['selCheckPeriod'])           ? $_POST['selCheckPeriod']+0            : 0;
$chkFreshness            	= isset($_POST['radFreshness'])         	? $_POST['radFreshness']+0          	: 2;
$chkObsess               	= isset($_POST['radObsess'])            	? $_POST['radObsess']+0             	: 2;
$chkSelEventHandler      	= isset($_POST['selEventHandler'])      	? $_POST['selEventHandler']+0       	: 0;
$chkEventEnable          	= isset($_POST['radEventEnable'])       	? $_POST['radEventEnable']+0        	: 2;
$chkFlapEnable           	= isset($_POST['radFlapEnable'])        	? $_POST['radFlapEnable']+0         	: 2;
$chkFLo                  	= isset($_POST['chbFLo'])               	? $_POST['chbFLo'].","              	: "";
$chkFLw                  	= isset($_POST['chbFLw'])               	? $_POST['chbFLw'].","              	: "";
$chkFLu                  	= isset($_POST['chbFLu'])               	? $_POST['chbFLu'].","              	: "";
$chkFLc                  	= isset($_POST['chbFLc'])               	? $_POST['chbFLc'].","              	: "";
$chkStatusInfos          	= isset($_POST['radStatusInfos'])       	? $_POST['radStatusInfos']+0        	: 2;
$chkNonStatusInfos       	= isset($_POST['radNoStatusInfos'])     	? $_POST['radNoStatusInfos']+0      	: 2;
$chkPerfData             	= isset($_POST['radPerfData'])          	? $_POST['radPerfData']+0           	: 2;
$chkIsVolatile           	= isset($_POST['radIsVolatile'])        	? $_POST['radIsVolatile']+0         	: 2;
$chkSelContacts          	= isset($_POST['selContacts'])          	? $_POST['selContacts']             	: array("");
$chkRadContacts          	= isset($_POST['radContacts'])          	? $_POST['radContacts']+0           	: 2;
$chkSelContactGroups    	= isset($_POST['selContactGroups'])     	? $_POST['selContactGroups']        	: array("");
$chkRadContactGroups     	= isset($_POST['radContactGroups'])     	? $_POST['radContactGroups']+0      	: 2;
$chkSelNotifPeriod       	= isset($_POST['selNotifPeriod'])       	? $_POST['selNotifPeriod']+0        	: 0;
$chkNOw                  	= isset($_POST['chbNOw'])               	? $_POST['chbNOw'].","              	: "";
$chkNOu                  	= isset($_POST['chbNOu'])               	? $_POST['chbNOu'].","              	: "";
$chkNOc                  	= isset($_POST['chbNOr'])               	? $_POST['chbNOr'].","              	: "";
$chkNOr                  	= isset($_POST['chbNOc'])               	? $_POST['chbNOc'].","              	: "";
$chkNOf                  	= isset($_POST['chbNOf'])               	? $_POST['chbNOf'].","              	: "";
$chkNOs                  	= isset($_POST['chbNOs'])               	? $_POST['chbNOs'].","              	: "";
$chkNotifEnabled         	= isset($_POST['radNotifEnabled'])      	? $_POST['radNotifEnabled']+0       	: 0;
$chkSTo                  	= isset($_POST['chbSTo'])               	? $_POST['chbSTo'].","              	: "";
$chkSTw                  	= isset($_POST['chbSTw'])               	? $_POST['chbSTw'].","              	: "";
$chkSTu                  	= isset($_POST['chbSTu'])               	? $_POST['chbSTu'].","              	: "";
$chkSTc                  	= isset($_POST['chbSTc'])               	? $_POST['chbSTc'].","              	: "";
$chkTfNotes              	= isset($_POST['tfNotes'])              	? $_POST['tfNotes']                 	: "";
$chkTfNotesURL           	= isset($_POST['tfNotesURL'])           	? $_POST['tfNotesURL']              	: "";
$chkTfActionURL          	= isset($_POST['tfActionURL'])          	? $_POST['tfActionURL']             	: "";
$chkTfIconImage          	= isset($_POST['tfIconImage'])          	? $_POST['tfIconImage']             	: "";
$chkTfIconImageAlt       	= isset($_POST['tfIconImageAlt'])       	? $_POST['tfIconImageAlt']          	: "";
$chkSelAccessGroup			= isset($_POST['selAccessGroup'])			? $_POST['selAccessGroup']+0			: 0;
//
$chkTfRetryInterval      	= (isset($_POST['tfRetryInterval'])     && ($_POST['tfRetryInterval'] 	 != "")) ? $myVisClass->checkNull($_POST['tfRetryInterval'])+0    : "NULL";
$chkTfMaxCheckAttempts   	= (isset($_POST['tfMaxCheckAttempts'])  && ($_POST['tfMaxCheckAttempts'] != "")) ? $myVisClass->checkNull($_POST['tfMaxCheckAttempts'])+0 : "NULL";
$chkTfCheckInterval      	= (isset($_POST['tfCheckInterval'])     && ($_POST['tfCheckInterval'] 	 != "")) ? $myVisClass->checkNull($_POST['tfCheckInterval'])+0    : "NULL";
$chkTfFreshTreshold      	= (isset($_POST['tfFreshTreshold'])     && ($_POST['tfFreshTreshold'] 	 != "")) ? $myVisClass->checkNull($_POST['tfFreshTreshold'])+0    : "NULL";
$chkTfLowFlat            	= (isset($_POST['tfLowFlat'])           && ($_POST['tfLowFlat'] 		 != "")) ? $myVisClass->checkNull($_POST['tfLowFlat'])+0          : "NULL";
$chkTfHighFlat           	= (isset($_POST['tfHighFlat'])          && ($_POST['tfHighFlat'] 		 != "")) ? $myVisClass->checkNull($_POST['tfHighFlat'])+0         : "NULL";
$chkNotifInterval        	= (isset($_POST['tfNotifInterval'])     && ($_POST['tfNotifInterval'] 	 != "")) ? $myVisClass->checkNull($_POST['tfNotifInterval'])+0    : "NULL";
$chkNotifDelay           	= (isset($_POST['tfFirstNotifDelay'])   && ($_POST['tfFirstNotifDelay']  != "")) ? $myVisClass->checkNull($_POST['tfFirstNotifDelay'])+0  : "NULL";
//
// Quote special characters
// ==========================
if (get_magic_quotes_gpc() == 0) {
	$chkTfSearch        		= addslashes($chkTfSearch);
	$chkTfName                	= addslashes($chkTfName);
	$chkTfServiceDescription  	= addslashes($chkTfServiceDescription);
	$chkTfDisplayName         	= addslashes($chkTfDisplayName);
	$chkTfArg1                	= addslashes($chkTfArg1);
	$chkTfArg2                	= addslashes($chkTfArg2);
	$chkTfArg3                	= addslashes($chkTfArg3);
	$chkTfArg4                	= addslashes($chkTfArg4);
	$chkTfArg5                	= addslashes($chkTfArg5);
	$chkTfArg6                	= addslashes($chkTfArg6);
	$chkTfArg7                	= addslashes($chkTfArg7);
	$chkTfArg8                	= addslashes($chkTfArg8);
	$chkTfNotes               	= addslashes($chkTfNotes);
	$chkTfNotesURL            	= addslashes($chkTfNotesURL);
	$chkTfActionURL           	= addslashes($chkTfActionURL);
	$chkTfIconImage           	= addslashes($chkTfIconImage);
	$chkTfIconImageAlt        	= addslashes($chkTfIconImageAlt);
}
//
// Search/Filter - Session data
// ============================
if (!isset($_SESSION['search']) || !isset($_SESSION['search']['servicetemplate'])) $_SESSION['search']['servicetemplate'] = "";
if (($chkModus == "checkform") || ($chkModus == "filter")) {
  	$_SESSION['search']['servicetemplate'] = $chkTfSearch;
}
//
// Process additional templates
// ============================
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
$strIS = substr($chkISo.$chkISw.$chkISu.$chkISc,0,-1);
$strFL = substr($chkFLo.$chkFLw.$chkFLu.$chkFLc,0,-1);
$strNO = substr($chkNOw.$chkNOu.$chkNOc.$chkNOr.$chkNOf.$chkNOs,0,-1);
$strST = substr($chkSTo.$chkSTw.$chkSTu.$chkSTc,0,-1);
if (($chkSelHosts[0] 		 == "")  || ($chkSelHosts[0] 		 == "0")) {$intSelHosts 		= 0;} else {$intSelHosts 		 = 1;}
if ($chkSelHosts[0] 		 == "*") $intSelHosts = 2;
if (($chkSelHostGroups[0] 	 == "")  || ($chkSelHostGroups[0] 	 == "0")) {$intSelHostGroups 	= 0;} else {$intSelHostGroups 	 = 1;}
if ($chkSelHostGroups[0] 	 == "*") $intSelHostGroups = 2;
if (($chkSelServiceGroups[0] == "")  || ($chkSelServiceGroups[0] == "0")) {$intSelServiceGroups = 0;} else {$intSelServiceGroups = 1;}
if (($chkSelContacts[0] 	 == "")  || ($chkSelContacts[0] 	 == "0")) {$intSelContacts 		= 0;} else {$intSelContacts 	 = 1;}
if ($chkSelContacts[0] 		 == "*") $intSelContacts = 2;
if (($chkSelContactGroups[0] == "")  || ($chkSelContactGroups[0] == "0")) {$intSelContactGroups = 0;} else {$intSelContactGroups = 1;}
if ($chkSelContactGroups[0]  == "*") $intSelContactGroups = 2;
$strCheckCommand = $chkServiceCommand;
if ($chkServiceCommand != "") {
  for ($i=1;$i<=8;$i++) {
    if (${"chkTfArg$i"} != "") $strCheckCommand .= "!".${"chkTfArg$i"};
  }
}
// 
// Add or modify data
// ==================
if (($chkModus == "insert") || ($chkModus == "modify")) {
  	if ($hidActive == 1) $chkActive = 1;
  	if ($chkGroupAdm == 1) {$strGroupSQL = "`access_group`=$chkSelAccessGroup, ";} else {$strGroupSQL = "";}
  	$strSQLx = "`tbl_servicetemplate` SET `template_name`='$chkTfName', `host_name`=$intSelHosts, `host_name_tploptions`=$chkRadHosts,
        		`hostgroup_name`=$intSelHostGroups, `hostgroup_name_tploptions`=$chkRadHostGroups, `service_description`='$chkTfServiceDescription',
        		`display_name`='$chkTfDisplayName', `servicegroups`=$intSelServiceGroups, `servicegroups_tploptions`=$chkRadServiceGroups,
        		`check_command`='$strCheckCommand', `use_template`=$intTemplates, `use_template_tploptions`=$chkRadTemplates,
        		`is_volatile`=$chkIsVolatile, `initial_state`='$strIS', `max_check_attempts`=$chkTfMaxCheckAttempts, `check_interval`=$chkTfCheckInterval,
        		`retry_interval`=$chkTfRetryInterval, `active_checks_enabled`=$chkActiveChecks, `passive_checks_enabled`=$chkPassiveChecks,
        		`check_period`=$chkSelCheckPeriod, `parallelize_check`=$chkParallelizeChecks, `obsess_over_service`=$chkObsess,
        		`check_freshness`=$chkFreshness, `freshness_threshold`=$chkTfFreshTreshold, `event_handler`=$chkSelEventHandler,
        		`event_handler_enabled`=$chkEventEnable, `low_flap_threshold`=$chkTfLowFlat, `high_flap_threshold`=$chkTfHighFlat,
        		`flap_detection_enabled`=$chkFlapEnable, `flap_detection_options`='$strFL', `process_perf_data`=$chkPerfData,
        		`retain_status_information`=$chkStatusInfos, `retain_nonstatus_information`=$chkNonStatusInfos, `contacts`=$intSelContacts,
        		`contacts_tploptions`=$chkRadContacts, `contact_groups`=$intSelContactGroups, `contact_groups_tploptions`=$chkRadContactGroups,
        		`notification_interval`=$chkNotifInterval, `notification_period`=$chkSelNotifPeriod,
        		`first_notification_delay`=$chkNotifDelay, `notification_options`='$strNO', `notifications_enabled`=$chkNotifEnabled,
        		`stalking_options`='$strST', `notes`='$chkTfNotes', `notes_url`='$chkTfNotesURL', `action_url`='$chkTfActionURL',
        		`icon_image`='$chkTfIconImage', `icon_image_alt`='$chkTfIconImageAlt', `active`='$chkActive',
        		$strGroupSQL `use_variables`=$intVariables, `config_id`=$chkDomainId, `last_modified`=NOW()";
  	if ($chkModus == "insert") {
    	$strSQL = "INSERT INTO ".$strSQLx;
  	} else {
    	$strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
  	}
  	if ($chkTfName != "") {
    	$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
		$myDataClass->updateStatusTable("tbl_servicetemplate");
    	if ($chkModus == "insert") $chkDataId = $intInsertId;
    	if ($intInsert == 1) {
      		$intReturn = 1;
    	} else {
      		if ($chkModus == "insert") $myDataClass->writeLog(translate('New service template inserted:')." ".$chkTfName);
      		if ($chkModus == "modify") $myDataClass->writeLog(translate('Service template modified:')." ".$chkTfName);
      		//
      		// Insert/update relations
      		// =======================
      		if ($chkModus == "insert") {
        		if ($intSelHosts         != 0)  $myDataClass->dataInsertRelation("tbl_lnkServicetemplateToHost",$chkDataId,$chkSelHosts);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHostGroups    != 0)  $myDataClass->dataInsertRelation("tbl_lnkServicetemplateToHostgroup",$chkDataId,$chkSelHostGroups);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelServiceGroups != 0)  $myDataClass->dataInsertRelation("tbl_lnkServicetemplateToServicegroup",$chkDataId,$chkSelServiceGroups);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelContacts    	 != 0)  $myDataClass->dataInsertRelation("tbl_lnkServicetemplateToContact",$chkDataId,$chkSelContacts);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelContactGroups != 0)  $myDataClass->dataInsertRelation("tbl_lnkServicetemplateToContactgroup",$chkDataId,$chkSelContactGroups);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		} else if ($chkModus == "modify") {
        		if ($intSelHosts != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkServicetemplateToHost",$chkDataId,$chkSelHosts);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkServicetemplateToHost",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHostGroups != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkServicetemplateToHostgroup",$chkDataId,$chkSelHostGroups);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkServicetemplateToHostgroup",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelServiceGroups != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkServicetemplateToServicegroup",$chkDataId,$chkSelServiceGroups);
        		} else {
         			$myDataClass->dataDeleteRelation("tbl_lnkServicetemplateToServicegroup",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelContacts != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkServicetemplateToContact",$chkDataId,$chkSelContacts);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkServicetemplateToContact",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelContactGroups != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkServicetemplateToContactgroup",$chkDataId,$chkSelContactGroups);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkServicetemplateToContactgroup",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		}
      		//
      		// Insert/update session data for templates
			// ========================================
      		if ($chkModus == "modify") {
        		$strSQL   	= "DELETE FROM `tbl_lnkServicetemplateToServicetemplate` WHERE `idMaster`=$chkDataId";
        		$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		}
      		if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition']) && (count($_SESSION['templatedefinition']) != 0)) {
        		$intSortId = 1;
        		foreach($_SESSION['templatedefinition'] AS $elem) {
          			if ($elem['status'] == 0) {
            			$strSQL 	= "INSERT INTO `tbl_lnkServicetemplateToServicetemplate` (`idMaster`,`idSlave`,`idTable`,`idSort`)
                   				   	   VALUES ($chkDataId,".$elem['idSlave'].",".$elem['idTable'].",".$intSortId.")";
            			$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
						$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
          			}
          			$intSortId++;
        		}
      		}
      		//
      		// Insert/update session data for free variables
      		// =============================================
      		if ($chkModus == "modify") {
        		$strSQL   	= "SELECT * FROM `tbl_lnkServicetemplateToVariabledefinition` WHERE `idMaster`=$chkDataId";
        		$booReturn  = $myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
        		if ($intDataCount != 0) {
          			foreach ($arrData AS $elem) {
            			$strSQL   	= "DELETE FROM `tbl_variabledefinition` WHERE `id`=".$elem['idSlave'];
            			$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
						$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
          			}
        		}
        		$strSQL   	= "DELETE FROM `tbl_lnkServicetemplateToVariabledefinition` WHERE `idMaster`=$chkDataId";
        		$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
      		}
      		if (isset($_SESSION['variabledefinition']) && is_array($_SESSION['variabledefinition']) && (count($_SESSION['variabledefinition']) != 0)) {
        		foreach($_SESSION['variabledefinition'] AS $elem) {
          			if ($elem['status'] == 0) {
            			$strSQL 	= "INSERT INTO `tbl_variabledefinition` (`name`,`value`,`last_modified`)
                   				   	   VALUES ('".$elem['definition']."','".$elem['range']."',now())";
            			$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
						$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
            			$strSQL 	= "INSERT INTO `tbl_lnkServicetemplateToVariabledefinition` (`idMaster`,`idSlave`)
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
} else if ($chkModus == "make") {
	// Write configuration files
  	$intReturn   = $myConfigClass->createConfig("tbl_servicetemplate",0);
	$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "info")) {
	// Display additional relation information
	$myDataClass->infoRelation("tbl_servicetemplate",$chkListId,"template_name");
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$intReturn   = 0;
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Delete selected datasets
  	$intReturn   = $myDataClass->dataDeleteFull("tbl_servicetemplate",$chkListId);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Copy selected datasets
  	$intReturn   = $myDataClass->dataCopyEasy("tbl_servicetemplate","template_name",$chkListId,$chkSelTargetDomain);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "activate")) {
	// Activate selected datasets
	$intReturn   = $myDataClass->dataActivate("tbl_servicetemplate",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "deactivate")) {
	// Deactivate selected datasets
	$intReturn   = $myDataClass->dataDeactivate("tbl_servicetemplate",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus    = "display"; 
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Open a dataset to modify
  	$booReturn   = $myDBClass->getSingleDataset("SELECT * FROM `tbl_servicetemplate` WHERE `id`=".$chkListId,$arrModifyData);
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
  $chkModus = "display"; 
}
// Get status messages from database
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $strMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$strMessage."</span>";
//
// Get date/time of last database and config file manipulation
// ===========================================================
$myConfigClass->lastModified("tbl_servicetemplate",$strLastModified,$strFileDate,$strOld);
$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Start content
// =============
$conttp->setVariable("TITLE",translate('Define service templates (servicetemplates.cfg)'));
$conttp->parse("header");
$conttp->show("header");
//
// Singe data form
// ===============

if ($chkModus == "add") {
	// Process template fields
  	$strWhere = "";
  	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
    	$strWhere = "AND `id` <> ".$arrModifyData['id'];
  	}
 	$strSQL   	= "SELECT `id`, `template_name` FROM `tbl_servicetemplate` WHERE `config_id` = $chkDomainId $strWhere ORDER BY `template_name`";
  	$booReturn  = $myDBClass->getDataArray($strSQL,$arrDataTpl,$intDataCountTpl);
  	if ($intDataCountTpl != 0) {
    	foreach ($arrDataTpl AS $elem) {
      		$conttp->setVariable("DAT_TEMPLATE",$elem['template_name']);
      		$conttp->setVariable("DAT_TEMPLATE_ID",$elem['id']."::1");
      		$conttp->parse("template");
    	}
  	}
  	$strSQL   	= "SELECT `id`, `name` FROM `tbl_service` WHERE `config_id` = $chkDomainId AND `name` <> '' ORDER BY `name`";
  	$booReturn  = $myDBClass->getDataArray($strSQL,$arrDataHpl,$intDataCount);
  	if ($arrDataHpl != 0) {
    	foreach ($arrDataHpl AS $elem) {
      		$conttp->setVariable("DAT_TEMPLATE",$elem['name']);
      		$conttp->setVariable("DAT_TEMPLATE_ID",$elem['id']."::2");
      		$conttp->parse("template");
    	}
  	}
	// Process host selection field
  	if (isset($arrModifyData['host_name'])) {$intFieldId = $arrModifyData['host_name'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectMulti('tbl_host','host_name','hosts','tbl_lnkServicetemplateToHost',2,$intFieldId);
  	if (isset($arrModifyData['hostgroup_name'])) {$intFieldId = $arrModifyData['hostgroup_name'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectMulti('tbl_hostgroup','hostgroup_name','hostgroup','tbl_lnkServicetemplateToHostgroup',2,$intFieldId);
	// Process service groups selection field
  	if (isset($arrModifyData['servicegroups'])) {$intFieldId = $arrModifyData['servicegroups'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectMulti('tbl_servicegroup','servicegroup_name','servicegroup','tbl_lnkServicetemplateToServicegroup',0,$intFieldId);
	// Process check command selection field
  	if (isset($arrModifyData['check_command']) && ($arrModifyData['check_command'] != "")) {
    	$arrCommand = explode("!",$arrModifyData['check_command']);
    	$intFieldId = $arrCommand[0];
   	} else {
    	$intFieldId = 0;
   	}
	$intReturn = $myVisClass->parseSelectSimple('tbl_command','command_name','servicecommand',2,$intFieldId);
  	// Process check period selection field
  	if (isset($arrModifyData['check_period'])) {$intFieldId = $arrModifyData['check_period'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectSimple('tbl_timeperiod','timeperiod_name','checkperiod',1,$intFieldId);
  	if (isset($arrModifyData['notification_period'])) {$intFieldId = $arrModifyData['notification_period'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectSimple('tbl_timeperiod','timeperiod_name','notifyperiod',1,$intFieldId);
  	// Process event handler selection field
  	if (isset($arrModifyData['event_handler'])) {$intFieldId = $arrModifyData['event_handler'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectSimple('tbl_command','command_name','eventhandler',1,$intFieldId);
  	// Process contact and contact group selection field
  	if (isset($arrModifyData['contacts'])) {$intFieldId = $arrModifyData['contacts'];} else {$intFieldId = 0;}
	$intReturn1 = $myVisClass->parseSelectMulti('tbl_contact','contact_name','service_contacts','tbl_lnkServicetemplateToContact',2,$intFieldId);
  	if (isset($arrModifyData['contact_groups'])) {$intFieldId = $arrModifyData['contact_groups'];} else {$intFieldId = 0;}
	$intReturn2 = $myVisClass->parseSelectMulti('tbl_contactgroup','contactgroup_name','service_contactgroups','tbl_lnkServicetemplateToContactgroup',2,$intFieldId);
  	if (($intReturn != 0) && ($intReturn2 != 0)) $strDBWarning .= translate('Attention, no contact groups defined!')."<br>";
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
	$conttp->setVariable("DOCUMENT_ROOT",$SETS['path']['root']);
  	$conttp->setVariable("IFRAME_SRC",$SETS['path']['root']."admin/commandline.php");
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
  	}
	// Process status fields
  	$strStatusfelder = "ACE,PCE,PAC,FRE,OBS,EVH,FLE,STI,NSI,PED,ISV,NOE,HOS,HOG,SEG,COT,COG,TPL";
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
    	$strStatusfelder = "ACE,PCE,PCE,FRE,OBS,EVH,FLE,STI,NSI,PED,ISV,NOE,HOS,HOG,SEG,COT,COG,TPL";
    	foreach (explode(",",$strStatusfelder) AS $elem) {
      		$conttp->setVariable("DAT_".$elem."0_CHECKED","");
      		$conttp->setVariable("DAT_".$elem."1_CHECKED","");
      		$conttp->setVariable("DAT_".$elem."2_CHECKED","");
    	}
    	$conttp->setVariable("DAT_ACE".$arrModifyData['active_checks_enabled']."_CHECKED","checked");
    	$conttp->setVariable("DAT_PCE".$arrModifyData['passive_checks_enabled']."_CHECKED","checked");
    	$conttp->setVariable("DAT_PAC".$arrModifyData['parallelize_check']."_CHECKED","checked");
    	$conttp->setVariable("DAT_FRE".$arrModifyData['check_freshness']."_CHECKED","checked");
    	$conttp->setVariable("DAT_OBS".$arrModifyData['obsess_over_service']."_CHECKED","checked");
    	$conttp->setVariable("DAT_EVH".$arrModifyData['event_handler_enabled']."_CHECKED","checked");
    	$conttp->setVariable("DAT_FLE".$arrModifyData['flap_detection_enabled']."_CHECKED","checked");
    	$conttp->setVariable("DAT_STI".$arrModifyData['retain_status_information']."_CHECKED","checked");
    	$conttp->setVariable("DAT_NSI".$arrModifyData['retain_nonstatus_information']."_CHECKED","checked");
    	$conttp->setVariable("DAT_PED".$arrModifyData['process_perf_data']."_CHECKED","checked");
    	$conttp->setVariable("DAT_ISV".$arrModifyData['is_volatile']."_CHECKED","checked");
    	$conttp->setVariable("DAT_NOE".$arrModifyData['notifications_enabled']."_CHECKED","checked");
    	$conttp->setVariable("DAT_HOS".$arrModifyData['host_name_tploptions']."_CHECKED","checked");
    	$conttp->setVariable("DAT_HOG".$arrModifyData['hostgroup_name_tploptions']."_CHECKED","checked");
    	$conttp->setVariable("DAT_SEG".$arrModifyData['servicegroups_tploptions']."_CHECKED","checked");
    	$conttp->setVariable("DAT_COT".$arrModifyData['contacts_tploptions']."_CHECKED","checked");
    	$conttp->setVariable("DAT_COG".$arrModifyData['contact_groups_tploptions']."_CHECKED","checked");
    	$conttp->setVariable("DAT_TPL".$arrModifyData['use_template_tploptions']."_CHECKED","checked");
    	// Special processing for -1 values - write 'null' to integer fields
    	$strIntegerfelder = "max_check_attempts,check_interval,retry_interval,freshness_threshold,low_flap_threshold,high_flap_threshold,notification_interval,first_notification_delay";
    	foreach(explode(",",$strIntegerfelder) AS $elem) {
      		if ($arrModifyData[$elem] == -1) {
        		$conttp->setVariable("DAT_".strtoupper($elem),"null");
      		}
    	}
    	if ($arrModifyData['check_command'] != "") {
      		$arrArgument = explode("!",$arrModifyData['check_command']);
      		foreach ($arrArgument AS $key => $value) {
        		if ($key == 0) {
          			$conttp->setVariable("IFRAME_SRC",$SETS['path']['root']."admin/commandline.php?cname=".$value);
        		} else {
          			$conttp->setVariable("DAT_ARG".$key,htmlentities($value,ENT_QUOTES,'UTF-8'));
        		}
      		}
    	}
		// Check relation information to find out locked configuration datasets
    	if ($myDataClass->infoRelation("tbl_servicetemplate",$arrModifyData['id'],"template_name") != 0) {
     		$conttp->setVariable("ACT_DISABLED","disabled");
      		$conttp->setVariable("ACT_CHECKED","checked");
     		$conttp->setVariable("ACTIVE","1");
      		$strInfo = "<br><span class=\"dbmessage\">".translate('Entry cannot be activated because it is used by another configuration').":</span><br><span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
      		$conttp->setVariable("CHECK_MUST_DATA",$strInfo);
    	}
    	// Process option fields
    	foreach(explode(",",$arrModifyData['initial_state']) AS $elem) {
      		$conttp->setVariable("DAT_IS".strtoupper($elem)."_CHECKED","checked");
    	}
    	foreach(explode(",",$arrModifyData['flap_detection_options']) AS $elem) {
      		$conttp->setVariable("DAT_FL".strtoupper($elem)."_CHECKED","checked");
    	}
    	foreach(explode(",",$arrModifyData['notification_options']) AS $elem) {
      		$conttp->setVariable("DAT_NO".strtoupper($elem)."_CHECKED","checked");
    	}
    	foreach(explode(",",$arrModifyData['stalking_options']) AS $elem) {
      		$conttp->setVariable("DAT_ST".strtoupper($elem)."_CHECKED","checked");
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
  	$mastertp->setVariable("FIELD_1",translate('Template name'));
  	$mastertp->setVariable("FIELD_2",translate('Service name'));
  	$mastertp->setVariable("LIMIT",$chkLimit);
  	$mastertp->setVariable("ACTION_MODIFY",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
  	$mastertp->setVariable("TABLE_NAME","tbl_servicetemplate");
	$mastertp->setVariable("DAT_SEARCH",$_SESSION['search']['servicetemplate']);
  	// Get Group id's with READ
  	$strAccess = $myVisClass->getAccGroupRead($_SESSION['userid']);
	// Include domain list
	$myVisClass->insertDomainList($mastertp);
  	// Process filter string
  	$strSearchWhere = "";
  	if ($_SESSION['search']['servicetemplate'] != "") {
		$strSearchTxt   = $_SESSION['search']['servicetemplate'];
    	$strSearchWhere = "AND (`template_name` LIKE '%".$strSearchTxt."%' OR `service_description` LIKE '%".$strSearchTxt."%' OR
                		   `display_name` LIKE '%".$strSearchTxt."%')";
  	}
  	// Count datasets
  	$strSQL    = "SELECT count(*) AS `number` FROM `tbl_servicetemplate` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
  	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
  	if ($booReturn == false) {
    	$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
  	} else {
    	$intCount = (int)$arrDataLinesCount['number'];
  	}
  	// Get datasets
  	$strSQL    = "SELECT `id`, `template_name`, `service_description`, `active`, `last_modified`, `config_id` 
				  FROM `tbl_servicetemplate` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess) 
				  ORDER BY `config_id`, `template_name`, `service_description` LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
      		$mastertp->setVariable("DATA_FIELD_1",htmlspecialchars($arrDataLines[$i]['template_name'],ENT_COMPAT,'UTF-8'));
      		$mastertp->setVariable("DATA_FIELD_2",htmlspecialchars($arrDataLines[$i]['service_description'],ENT_COMPAT,'UTF-8'));
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