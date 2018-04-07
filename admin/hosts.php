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
// Component : Host definition
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
$intSub       	= 1;
$intMenu      	= 2;
$preContent   	= "admin/hosts.tpl.htm";
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
$chkTfSearch      		= isset($_POST['txtSearch'])      			? htmlspecialchars($_POST['txtSearch'], ENT_QUOTES, 'utf-8')		: "";
$chkTfName       		= isset($_POST['tfName'])         			? $_POST['tfName']              		: "";
$chkOldHost       		= isset($_POST['hidName'])        			? $_POST['hidName']             		: "";
$chkTfFriendly      	= isset($_POST['tfFriendly'])       		? $_POST['tfFriendly']            		: "";
$chkTfDisplay     		= isset($_POST['tfDisplay'])      			? $_POST['tfDisplay']             		: "";
$chkTfAddress     		= isset($_POST['tfAddress'])      			? $_POST['tfAddress']             		: "";
$chkTfGenericName   	= isset($_POST['tfGenericName'])    		? $_POST['tfGenericName']           	: "";
$chkSelParents      	= isset($_POST['selParents'])       		? $_POST['selParents']            		: array("");
$chkRadParent     		= isset($_POST['radParent'])      			? $_POST['radParent']+0           		: 2;
$chkSelHostGroups     	= isset($_POST['selHostGroups'])    		? $_POST['selHostGroups']           	: array("");
$chkRadHostGroups   	= isset($_POST['radHostGroups'])    		? $_POST['radHostGroups']+0         	: 2;
$chkSelHostCommand   	= isset($_POST['selHostCommand'])   		? $_POST['selHostCommand']+0        	: 0;
$chkTfArg1        		= isset($_POST['tfArg1'])         			? $_POST['tfArg1']              		: "";
$chkTfArg2        		= isset($_POST['tfArg2'])         			? $_POST['tfArg2']              		: "";
$chkTfArg3        		= isset($_POST['tfArg3'])         			? $_POST['tfArg3']              		: "";
$chkTfArg4        		= isset($_POST['tfArg4'])         			? $_POST['tfArg4']              		: "";
$chkTfArg5        		= isset($_POST['tfArg5'])         			? $_POST['tfArg5']              		: "";
$chkTfArg6        		= isset($_POST['tfArg6'])         			? $_POST['tfArg6']              		: "";
$chkTfArg7        		= isset($_POST['tfArg7'])         			? $_POST['tfArg7']              		: "";
$chkTfArg8        		= isset($_POST['tfArg8'])         			? $_POST['tfArg8']              		: "";
$chkRadTemplates    	= isset($_POST['radTemplate'])      		? $_POST['radTemplate']+0         		: 2;
$chkISo         		= isset($_POST['chbISo'])       			? $_POST['chbISo'].","            		: "";
$chkISd         		= isset($_POST['chbISd'])       			? $_POST['chbISd'].","            		: "";
$chkISu         		= isset($_POST['chbISu'])       			? $_POST['chbISu'].","            		: "";
$chkISnull        		= isset($_POST['chbISnull'])      			? $_POST['chbISnull'].","         		: "";
$chkActiveChecks    	= isset($_POST['radActiveChecksEnabled']) 	? $_POST['radActiveChecksEnabled']+0  	: 2;
$chkPassiveChecks   	= isset($_POST['radPassiveChecksEnabled'])	? $_POST['radPassiveChecksEnabled']+0 	: 2;
$chkSelCheckPeriod  	= isset($_POST['selCheckPeriod'])     		? $_POST['selCheckPeriod']+0        	: 0;
$chkFreshness     		= isset($_POST['radFreshness'])     		? $_POST['radFreshness']+0          	: 2;
$chkObsess        		= isset($_POST['radObsess'])      			? $_POST['radObsess']+0           		: 2;
$chkSelEventHandler		= isset($_POST['selEventHandler'])    		? $_POST['selEventHandler']+0       	: 0;
$chkEventEnable     	= isset($_POST['radEventEnable'])   		? $_POST['radEventEnable']+0        	: 2;
$chkFlapEnable      	= isset($_POST['radFlapEnable'])    		? $_POST['radFlapEnable']+0         	: 2;
$chkFLo         		= isset($_POST['chbFLo'])       			? $_POST['chbFLo'].","            		: "";
$chkFLd        			= isset($_POST['chbFLd'])       			? $_POST['chbFLd'].","            		: "";
$chkFLu         		= isset($_POST['chbFLu'])       			? $_POST['chbFLu'].","            		: "";
$chkFLnull        		= isset($_POST['chbFLnull'])      			? $_POST['chbFLnull'].","         		: "";
$chkStatusInfos     	= isset($_POST['radStatusInfos'])   		? $_POST['radStatusInfos']+0        	: 2;
$chkNonStatusInfos    	= isset($_POST['radNoStatusInfos'])   		? $_POST['radNoStatusInfos']+0        	: 2;
$chkPerfData     		= isset($_POST['radPerfData'])      		? $_POST['radPerfData']+0         		: 2;
$chkSelContacts     	= isset($_POST['selContacts'])      		? $_POST['selContacts']           		: array("");
$chkRadContacts     	= isset($_POST['radContacts'])      		? $_POST['radContacts']+0         		: 2;
$chkSelContactGroups	= isset($_POST['selContactGroups'])   		? $_POST['selContactGroups']        	: array("");
$chkRadContactGroups  	= isset($_POST['radContactGroups'])   		? $_POST['radContactGroups']+0        	: 2;
$chkSelNotifPeriod    	= isset($_POST['selNotifPeriod'])     		? $_POST['selNotifPeriod']+0        	: 0;
$chkNOd         		= isset($_POST['chbNOd'])       			? $_POST['chbNOd'].","            		: "";
$chkNOu         		= isset($_POST['chbNOu'])       			? $_POST['chbNOu'].","            		: "";
$chkNOr         		= isset($_POST['chbNOr'])       			? $_POST['chbNOr'].","            		: "";
$chkNOf        			= isset($_POST['chbNOf'])       			? $_POST['chbNOf'].","            		: "";
$chkNOs         		= isset($_POST['chbNOs'])       			? $_POST['chbNOs'].","            		: "";
$chkNOnull        		= isset($_POST['chbNOnull'])      			? $_POST['chbNOnull'].","         		: "";
$chkSTo         		= isset($_POST['chbSTo'])       			? $_POST['chbSTo'].","            		: "";
$chkSTd         		= isset($_POST['chbSTd'])       			? $_POST['chbSTd'].","            		: "";
$chkSTu         		= isset($_POST['chbSTu'])       			? $_POST['chbSTu'].","            		: "";
$chkSTnull       		= isset($_POST['chbSTnull'])      			? $_POST['chbSTnull'].","         		: "";
$chkTfNotes       		= isset($_POST['tfNotes'])        			? $_POST['tfNotes']             		: "";
$chkTfVmrlImage     	= isset($_POST['tfVmrlImage'])      		? $_POST['tfVmrlImage']           		: "";
$chkTfNotesURL      	= isset($_POST['tfNotesURL'])       		? $_POST['tfNotesURL']            		: "";
$chkTfStatusImage   	= isset($_POST['tfStatusImage'])    		? $_POST['tfStatusImage']           	: "";
$chkTfActionURL     	= isset($_POST['tfActionURL'])      		? $_POST['tfActionURL']           		: "";
$chkTfIconImage     	= isset($_POST['tfIconImage'])      		? $_POST['tfIconImage']           		: "";
$chkTfD2Coords      	= isset($_POST['tfD2Coords'])       		? $_POST['tfD2Coords']            		: "";
$chkTfIconImageAlt    	= isset($_POST['tfIconImageAlt'])     		? $_POST['tfIconImageAlt']          	: "";
$chkTfD3Coords      	= isset($_POST['tfD3Coords'])       		? $_POST['tfD3Coords']            		: "";
$chkNotifEnabled    	= isset($_POST['radNotifEnabled'])    		? $_POST['radNotifEnabled']+0       	: 0;
$chkSelAccessGroup		= isset($_POST['selAccessGroup'])			? $_POST['selAccessGroup']+0			: 0;
$chkSelServices			= isset($_POST['selServices'])				? $_POST['selServices']					: array("");
//
$chkTfRetryInterval   	= (isset($_POST['tfRetryInterval'])    && ($_POST['tfRetryInterval'] 	!= "")) ? $myVisClass->checkNull($_POST['tfRetryInterval'])    	: "NULL";
$chkTfMaxCheckAttempts  = (isset($_POST['tfMaxCheckAttempts']) && ($_POST['tfMaxCheckAttempts'] != "")) ? $myVisClass->checkNull($_POST['tfMaxCheckAttempts']) 	: "NULL";
$chkTfCheckInterval   	= (isset($_POST['tfCheckInterval'])    && ($_POST['tfCheckInterval'] 	!= "")) ? $myVisClass->checkNull($_POST['tfCheckInterval'])    	: "NULL";
$chkTfFreshTreshold   	= (isset($_POST['tfFreshTreshold'])    && ($_POST['tfFreshTreshold'] 	!= "")) ? $myVisClass->checkNull($_POST['tfFreshTreshold'])    	: "NULL";
$chkTfLowFlat     		= (isset($_POST['tfLowFlat'])     	   && ($_POST['tfLowFlat'] 			!= "")) ? $myVisClass->checkNull($_POST['tfLowFlat'])     		: "NULL";
$chkTfHighFlat      	= (isset($_POST['tfHighFlat'])         && ($_POST['tfHighFlat'] 		!= "")) ? $myVisClass->checkNull($_POST['tfHighFlat'])      	: "NULL";
$chkNotifInterval   	= (isset($_POST['tfNotifInterval'])    && ($_POST['tfNotifInterval'] 	!= "")) ? $myVisClass->checkNull($_POST['tfNotifInterval'])   	: "NULL";
$chkNotifDelay      	= (isset($_POST['tfFirstNotifDelay'])  && ($_POST['tfFirstNotifDelay'] 	!= "")) ? $myVisClass->checkNull($_POST['tfFirstNotifDelay']) 	: "NULL";
//
// Quote special characters
// ==========================
if (get_magic_quotes_gpc() == 0) {
  $chkTfSearch			= addslashes($chkTfSearch);
  $chkTfName      		= addslashes($chkTfName);
  $chkOldHost     		= addslashes($chkOldHost);
  $chkTfFriendly    	= addslashes($chkTfFriendly);
  $chkTfDisplay     	= addslashes($chkTfDisplay);
  $chkTfAddress     	= addslashes($chkTfAddress);
  $chkTfGenericName   	= addslashes($chkTfGenericName);
  $chkTfArg1      		= addslashes($chkTfArg1);
  $chkTfArg2      		= addslashes($chkTfArg2);
  $chkTfArg3      		= addslashes($chkTfArg3);
  $chkTfArg4      		= addslashes($chkTfArg4);
  $chkTfArg5      		= addslashes($chkTfArg5);
  $chkTfArg6      		= addslashes($chkTfArg6);
  $chkTfArg7      		= addslashes($chkTfArg7);
  $chkTfArg8      		= addslashes($chkTfArg8);
  $chkTfNotes     		= addslashes($chkTfNotes);
  $chkTfVmrlImage   	= addslashes($chkTfVmrlImage);
  $chkTfNotesURL    	= addslashes($chkTfNotesURL);
  $chkTfStatusImage   	= addslashes($chkTfStatusImage);
  $chkTfActionURL   	= addslashes($chkTfActionURL);
  $chkTfIconImage   	= addslashes($chkTfIconImage);
  $chkTfD2Coords    	= addslashes($chkTfD2Coords);
  $chkTfIconImageAlt  	= addslashes($chkTfIconImageAlt);
  $chkTfD3Coords    	= addslashes($chkTfD3Coords);
}
//
// Search/Filter - Session data
// ============================
if (!isset($_SESSION['search']) || !isset($_SESSION['search']['host'])) $_SESSION['search']['host'] = "";
if (($chkModus == "checkform") || ($chkModus == "filter")) {
  	$_SESSION['search']['host'] = $chkTfSearch;
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
if ($chkISnull == "") {$strIS = substr($chkISo.$chkISd.$chkISu,0,-1);} 					else {$strIS = "null";}
if ($chkFLnull == "") {$strFL = substr($chkFLo.$chkFLd.$chkFLu,0,-1);} 					else {$strFL = "null";}
if ($chkNOnull == "") {$strNO = substr($chkNOd.$chkNOu.$chkNOr.$chkNOf.$chkNOs,0,-1);} 	else {$strNO = "null";}
if ($chkSTnull == "") {$strST = substr($chkSTo.$chkSTd.$chkSTu,0,-1);} 					else {$strST = "null";}
if (($chkSelParents[0]		 == "")  || ($chkSelParents[0] 		 == "0")) {$intSelParents 		= 0;} else {$intSelParents 		 = 1;}
if (($chkSelHostGroups[0] 	 == "")  || ($chkSelHostGroups[0] 	 == "0")) {$intSelHostGroups 	= 0;} else {$intSelHostGroups 	 = 1;}
if (($chkSelContacts[0] 	 == "")  || ($chkSelContacts[0] 	 == "0")) {$intSelContacts 		= 0;} else {$intSelContacts 	 = 1;}
if ($chkSelContacts[0] 		 == "*") $intSelContacts = 2;
if (($chkSelContactGroups[0] == "")  || ($chkSelContactGroups[0] == "0")) {$intSelContactGroups = 0;} else {$intSelContactGroups = 1;}
if ($chkSelContactGroups[0]  == "*") $intSelContactGroups = 2;
$strCheckCommand = $chkSelHostCommand;
if ($chkSelHostCommand != "") {
  for ($i=1;$i<=8;$i++) {
    if (${"chkTfArg$i"} != "") $strCheckCommand .= "!".${"chkTfArg$i"};
  }
}

// 
// Add or modify data
// ==================
if (($chkModus == "insert") || ($chkModus == "modify")) {
	if ($hidActive   == 1) $chkActive = 1;
	if ($chkGroupAdm == 1) 		{$strGroupSQL 	= "`access_group`=$chkSelAccessGroup, ";} 	else {$strGroupSQL 	= "";}
	if ($chkModus == "insert") 	{$strDomain 	= "`config_id`=$chkDomainId, ";} 			else {$strDomain 	= "";}
  	$strSQLx = "`tbl_host` SET `host_name`='$chkTfName', `alias`='$chkTfFriendly', `display_name`='$chkTfDisplay', `address`='$chkTfAddress',
        		`name`='$chkTfGenericName', `parents`=$intSelParents, `parents_tploptions`=$chkRadParent, `hostgroups`=$intSelHostGroups,
        		`hostgroups_tploptions`=$chkRadHostGroups, `check_command`='$strCheckCommand', `use_template`=$intTemplates,
        		`use_template_tploptions`=$chkRadTemplates, `initial_state`='$strIS', `max_check_attempts`=$chkTfMaxCheckAttempts,
        		`check_interval`=$chkTfCheckInterval, `retry_interval`=$chkTfRetryInterval, `active_checks_enabled`=$chkActiveChecks,
        		`passive_checks_enabled`=$chkPassiveChecks, `check_period`=$chkSelCheckPeriod, `obsess_over_host`=$chkObsess,
        		`check_freshness`=$chkFreshness, `freshness_threshold`=$chkTfFreshTreshold, `event_handler`=$chkSelEventHandler,
        		`event_handler_enabled`=$chkEventEnable, `low_flap_threshold`=$chkTfLowFlat, `high_flap_threshold`=$chkTfHighFlat,
        		`flap_detection_enabled`=$chkFlapEnable, `flap_detection_options`='$strFL', `process_perf_data`=$chkPerfData,
        		`retain_status_information`=$chkStatusInfos, `retain_nonstatus_information`=$chkNonStatusInfos, `contacts`=$intSelContacts,
        		`contacts_tploptions`=$chkRadContacts, `contact_groups`=$intSelContactGroups, `contact_groups_tploptions`=$chkRadContactGroups,
        		`notification_interval`=$chkNotifInterval, `notification_period`=$chkSelNotifPeriod,
        		`first_notification_delay`=$chkNotifDelay, `notification_options`='$strNO', `notifications_enabled`=$chkNotifEnabled,
        		`stalking_options`='$strST', `notes`='$chkTfNotes', `notes_url`='$chkTfNotesURL', `action_url`='$chkTfActionURL',
        		`icon_image`='$chkTfIconImage', `icon_image_alt`='$chkTfIconImageAlt', `vrml_image`='$chkTfVmrlImage',
        		`statusmap_image`='$chkTfStatusImage', `2d_coords`='$chkTfD2Coords', `3d_coords`='$chkTfD3Coords', `active`='$chkActive',
        		 $strGroupSQL $strDomain `use_variables`=$intVariables, `last_modified`=NOW()";
	if ($chkModus == "insert") {
    	$strSQL = "INSERT INTO ".$strSQLx;
  	} else {
    	$strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
  	}
  	if ($chkTfName != "") {
    	$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
    	if ($chkModus == "insert") $chkDataId = $intInsertId;
		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
    	if ($intInsert == 1) {
      		$strMessage = $myDataClass->strDBMessage;
     		$intReturn  = 1;
    	} else {
     		if ($chkModus == "insert") $myDataClass->writeLog(translate('New host inserted:')." ".$chkTfName);
      		if ($chkModus == "modify") $myDataClass->writeLog(translate('Host modified:')." ".$chkTfName);
      		//
      		// Insert/update relations
      		// =======================
      		if ($chkModus == "insert") {
        		if ($intSelParents       != 0)  $myDataClass->dataInsertRelation("tbl_lnkHostToHost",$chkDataId,$chkSelParents);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHostGroups    != 0)  $myDataClass->dataInsertRelation("tbl_lnkHostToHostgroup",$chkDataId,$chkSelHostGroups);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelContacts    	 != 0)  $myDataClass->dataInsertRelation("tbl_lnkHostToContact",$chkDataId,$chkSelContacts);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelContactGroups != 0)  $myDataClass->dataInsertRelation("tbl_lnkHostToContactgroup",$chkDataId,$chkSelContactGroups);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		} else if ($chkModus == "modify") {
        		if ($intSelParents != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkHostToHost",$chkDataId,$chkSelParents);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkHostToHost",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelHostGroups != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkHostToHostgroup",$chkDataId,$chkSelHostGroups);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkHostToHostgroup",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelContacts != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkHostToContact",$chkDataId,$chkSelContacts);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkHostToContact",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
        		if ($intSelContactGroups != 0) {
          			$myDataClass->dataUpdateRelation("tbl_lnkHostToContactgroup",$chkDataId,$chkSelContactGroups);
        		} else {
          			$myDataClass->dataDeleteRelation("tbl_lnkHostToContactgroup",$chkDataId);
        		}
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		}
      		//
			// Removing the config file if an entry was deleted or renamed
			// ===========================================================
      		if (($chkModus == "modify") && ($chkOldHost != $chkTfName) && ($chkDomainId != 0)) {
        		$intReturn = $myConfigClass->moveFile("host",$chkOldHost.".cfg");
        		if ($intReturn == 0) {
					$myVisClass->processMessage(translate('The assigned, no longer used configuration files were deleted successfully!'),$strMessage);
          			$myDataClass->writeLog(translate('Host file deleted:')." ".$chkOldHost.".cfg");
        		} else {
					if ($chkDomainId == 0) {
						$myVisClass->processMessage(translate('Common files cannot be removed from target systems - please check manually'),$strMessage);
					} else {
						$myVisClass->processMessage(translate('Errors while deleting the old configuration file - please check!:')."<br>".$myConfigClass->strDBMessage,$strMessage);
					}
        		}
      		}
      		//
			// Removing the config file if an entry was dectivated ???
			// ===================================================
			/*
      		if (($chkModus == "modify") && ($chkActive == 0)) {
        		$intReturn = $myConfigClass->moveFile("host",$chkTfName.".cfg");
        		if ($intReturn == 0) {
					$myVisClass->processMessage(translate('The assigned, no longer used configuration files were deleted successfully!'),$strMessage);
          			$myDataClass->writeLog(translate('Host file deleted:')." ".$chkTfName.".cfg");
        		} else {
					if ($chkDomainId == 0) {
						$myVisClass->processMessage(translate('Common files cannot be removed from target systems - please check manually'),$strMessage);
					} else {
          				$myVisClass->processMessage(translate('Errors while deleting the old configuration file - please check!:')."<br>".$myConfigClass->strDBMessage,$strMessage);
					}
        		}
      		}
			*/
      		//
      		// Insert/update session data for templates
      		// ========================================
      		if ($chkModus == "modify") {
        		$strSQL   	= "DELETE FROM `tbl_lnkHostToHosttemplate` WHERE `idMaster`=$chkDataId";
        		$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		}
      		if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition']) && (count($_SESSION['templatedefinition']) != 0)) {
        		$intSortId = 1;
        		foreach($_SESSION['templatedefinition'] AS $elem) {
          			if ($elem['status'] == 0) {
            			$strSQL 	= "INSERT INTO `tbl_lnkHostToHosttemplate` (`idMaster`,`idSlave`,`idTable`,`idSort`)
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
        		$strSQL   	= "SELECT * FROM `tbl_lnkHostToVariabledefinition` WHERE `idMaster`=$chkDataId";
        		$booReturn  = $myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
				if ($intDataCount != 0) {
					foreach ($arrData AS $elem) {
						$strSQL   	= "DELETE FROM `tbl_variabledefinition` WHERE `id`=".$elem['idSlave'];
						$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
						$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
					}
				}
				$strSQL   	= "DELETE FROM `tbl_lnkHostToVariabledefinition` WHERE `idMaster`=$chkDataId";
				$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		}
      		if (isset($_SESSION['variabledefinition']) && is_array($_SESSION['variabledefinition']) && (count($_SESSION['variabledefinition']) != 0)) {
        		foreach($_SESSION['variabledefinition'] AS $elem) {
          			if ($elem['status'] == 0) {
            			$strSQL 	= "INSERT INTO `tbl_variabledefinition` (`name`,`value`,`last_modified`)
                   				   	   VALUES ('".$elem['definition']."','".$elem['range']."',now())";
            			$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
						$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
            			$strSQL 	= "INSERT INTO `tbl_lnkHostToVariabledefinition` (`idMaster`,`idSlave`)
                   				       VALUES ($chkDataId,$intInsertId)";
            			$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
						$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
          			}
        		}
      		}
			//
			// Insert/Update service <-> host relations
			// ========================================
			$strSQL = "DELETE FROM `tbl_lnkServiceToHost` WHERE `idSlave`=$chkDataId";
			$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
			$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
			foreach ($chkSelServices AS $elem) {
				if ($elem != "") {
					$strSQL 	= "INSERT INTO `tbl_lnkServiceToHost` (`idMaster`,`idSlave`,`exclude`)
								   VALUES ($elem,$chkDataId,0)";
					$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
					$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
				}
			}
      		$intReturn = 0;
    	}
  	} else {
    	$myVisClass->processMessage(translate('Database entry failed! Not all necessary data filled in!'),$strMessage);
  	}
  	$chkModus = "display";
} else if ($chkModus == "make") {
	// Write any configuration files
	$myConfigClass->getConfigData("enable_common",$setEnableCommon);
	if ($setEnableCommon != 0) {
		$strDomainWhere = " (`config_id`=".$chkDomainId." OR `config_id`=0) ";	
	} else {
		$strDomainWhere = " (`config_id`=".$chkDomainId.") ";
	}
  	$strSQL  = "SELECT `id` FROM `tbl_host` WHERE $strDomainWhere";
  	$myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
  	$intError = 0;
  	if ($intDataCount != 0) {
    	foreach ($arrData AS $data) {
      		$booReturn = $myConfigClass->createConfigSingle("tbl_host",$data['id']);
			$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
      		if ($booReturn == 1) $intError++;
    	}
  	}
  	if ($intError == 0) {
		$myVisClass->processMessage(translate('Configuration file successfully written!'),$strMessage);
    	$intReturn   = 0;
  	} else {
   		$strMessage .= $myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
    	$intReturn   = 1;
  	}
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "info")) {
	// Display additional relation information
  	$myDataClass->infoRelation("tbl_host",$chkListId,"host_name");
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$intReturn   = 0;
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Delete selected datasets
  	$intReturn   = $myDataClass->dataDeleteFull("tbl_host",$chkListId);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Copy selected datasets
  	$intReturn   = $myDataClass->dataCopyEasy("tbl_host","host_name",$chkListId,$chkSelTargetDomain);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "activate")) {
	// Activate selected datasets
	$intReturn   = $myDataClass->dataActivate("tbl_host",$chkListId);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "deactivate")) {
	// Deactivate selected datasets
	$intReturn   = $myDataClass->dataDeactivate("tbl_host",$chkListId);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus    = "display"; 
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Open a dataset to modify
  	$booReturn   = $myDBClass->getSingleDataset("SELECT * FROM `tbl_host` WHERE `id`=".$chkListId,$arrModifyData);
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
} else if (($chkModus == "checkform") && ($chkSelModify == "config")) {
	// Write configuration file
  	$intDSId    = (int)substr(array_search("on",$_POST),6);
  	if (isset($chkListId) && ($chkListId != 0)) $intDSId = $chkListId;
   	$intReturn   = $myConfigClass->createConfigSingle("tbl_host",$intDSId);
   	$strMessage .= $myConfigClass->strDBMessage;
  	$chkModus   = "display";
} else if ($chkModus != "add") {
  	$chkModus   = "display";
}
// Get status messages from database
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $strMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$strMessage."</span>";
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Start content
// =============
$conttp->setVariable("TITLE",translate('Define hosts (hosts.cfg)'));
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
  	$strSQL   = "SELECT `id`,`template_name` FROM `tbl_hosttemplate` WHERE $strDomainWhere ORDER BY `template_name`";
  	$booReturn  = $myDBClass->getDataArray($strSQL,$arrDataTpl,$intDataCountTpl);
  	if ($intDataCountTpl != 0) {
    	foreach ($arrDataTpl AS $elem) {
      		$conttp->setVariable("DAT_TEMPLATE",$elem['template_name']);
      		$conttp->setVariable("DAT_TEMPLATE_ID",$elem['id']."::1");
      		$conttp->parse("template");
    	}
  	}
  	$strSQL   = "SELECT `id`, `name` FROM `tbl_host` WHERE `name` <> '' $strWhere AND $strDomainWhere ORDER BY `name`";
  	$booReturn  = $myDBClass->getDataArray($strSQL,$arrDataHpl,$intDataCount);
  	if ($arrDataHpl != 0) {
    	foreach ($arrDataHpl AS $elem) {
      		$conttp->setVariable("DAT_TEMPLATE",$elem['name']);
      		$conttp->setVariable("DAT_TEMPLATE_ID",$elem['id']."::2");
      		$conttp->parse("template");
    	}
  	}
	// Process host selection field
  	if (isset($arrModifyData['parents']))   {$intFieldId = $arrModifyData['parents'];}  else {$intFieldId = 0;}
  	if (isset($arrModifyData['id']))    	{$intKeyId   = $arrModifyData['id'];}     	else {$intKeyId   = 0;}
	$intReturn = $myVisClass->parseSelectMulti('tbl_host','host_name','host_parents','tbl_lnkHostToHost',0,$intFieldId,$intKeyId);
	// Process hostgroup selection field
  	if (isset($arrModifyData['hostgroups'])) {$intFieldId = $arrModifyData['hostgroups'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectMulti('tbl_hostgroup','hostgroup_name','hostgroup','tbl_lnkHostToHostgroup',0,$intFieldId);
	// Process check command selection field
  	if (isset($arrModifyData['check_command']) && ($arrModifyData['check_command'] != "")) {
    	$arrCommand = explode("!",$arrModifyData['check_command']);
    	$intFieldId = $arrCommand[0];
   	} else {
    	$intFieldId = 0;
   	}
	$intReturn = $myVisClass->parseSelectSimple('tbl_command','command_name','hostcommand',2,$intFieldId);
  	// Process check period selection field
  	$intReturn = 0;
  	if (isset($arrModifyData['check_period'])) {$intFieldId = $arrModifyData['check_period'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectSimple('tbl_timeperiod','timeperiod_name','checkperiod',1,$intFieldId);
  	if (isset($arrModifyData['notification_period'])) {$intFieldId = $arrModifyData['notification_period'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectSimple('tbl_timeperiod','timeperiod_name','notifyperiod',1,$intFieldId);
  	if ($intReturn != 0) $strDBWarning .= translate('Attention, no time periods defined!')."<br>";
  	// Process event handler selection field
  	if (isset($arrModifyData['event_handler'])) {$intFieldId = $arrModifyData['event_handler'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectSimple('tbl_command','command_name','eventhandler',1,$intFieldId);
  	// Process contact and contact group selection field
	$intReturn1 = 0;
	$intReturn2 = 0;
  	if (isset($arrModifyData['contacts'])) {$intFieldId = $arrModifyData['contacts'];} else {$intFieldId = 0;}
	$intReturn1 = $myVisClass->parseSelectMulti('tbl_contact','contact_name','host_contacts','tbl_lnkHostToContact',2,$intFieldId);
  	if (isset($arrModifyData['contact_groups'])) {$intFieldId = $arrModifyData['contact_groups'];} else {$intFieldId = 0;}
	$intReturn2 = $myVisClass->parseSelectMulti('tbl_contactgroup','contactgroup_name','host_contactgroups','tbl_lnkHostToContactgroup',2,$intFieldId);
  	if (($intReturn != 0) && ($intReturn2 != 0)) $strDBWarning .= translate('Attention, no contact groups defined!')."<br>";
  	// Process access group selection field
  	if (isset($arrModifyData['access_group'])) {$intFieldId = $arrModifyData['access_group'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectSimple('tbl_group','groupname','acc_group',0,$intFieldId);
	// Process service selection field
	$intReturn1 = 0;
  	if (isset($arrModifyData['contacts'])) {$intFieldId = $arrModifyData['id'];} else {$intFieldId = 0;}
	$intReturn1 = $myVisClass->parseSelectMulti('tbl_service','service_description','host_services','tbl_lnkServiceToHost',0,$intFieldId);
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
	$conttp->setVariable("RELATION_CLASS","elementHide");
	$conttp->setVariable("DOCUMENT_ROOT",$SETS['path']['root']);
	$conttp->setVariable("IFRAME_SRC",$SETS['path']['root']."admin/commandline.php");
	$conttp->setVariable("SELECT_FIELD_DISABLED","disabled");
	if ($SETS['common']['seldisable'] == 0)$conttp->setVariable("SELECT_FIELD_DISABLED","enabled");
	if ($SETS['common']['tplcheck'] == 0) $conttp->setVariable("CHECK_BYPASS","return true;");
	if ($chkGroupAdm == 0) $conttp->setVariable("RESTRICT_GROUP_ADMIN","class=\"elementHide\"");
	// Process additional fields based on nagios version
	if ($intVersion == 3) {
    	$conttp->setVariable("CLASS_NAME_20","elementHide");
    	$conttp->setVariable("CLASS_NAME_30","elementShow");
  	} else {
    	$conttp->setVariable("CLASS_NAME_20","elementShow");
    	$conttp->setVariable("CLASS_NAME_30","elementHide");
    	$conttp->setVariable("MUST_20_STAR","*");
  	}
	// Process status fields
  	$strStatusfelder = "ACE,PCE,FRE,OBS,EVH,FLE,STI,NSI,PED,NOE,PAR,HOG,COT,COG,TPL";
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
    	$strStatusfelder = "ACE,PCE,FRE,OBS,EVH,FLE,STI,NSI,PED,NOE,PAR,HOG,COT,COG,TPL";
    	foreach (explode(",",$strStatusfelder) AS $elem) {
      		$conttp->setVariable("DAT_".$elem."0_CHECKED","");
      		$conttp->setVariable("DAT_".$elem."1_CHECKED","");
      		$conttp->setVariable("DAT_".$elem."2_CHECKED","");
    	}
   		$conttp->setVariable("DAT_ACE".$arrModifyData['active_checks_enabled']."_CHECKED","checked");
   		$conttp->setVariable("DAT_PCE".$arrModifyData['passive_checks_enabled']."_CHECKED","checked");
   		$conttp->setVariable("DAT_FRE".$arrModifyData['check_freshness']."_CHECKED","checked");
   		$conttp->setVariable("DAT_OBS".$arrModifyData['obsess_over_host']."_CHECKED","checked");
   		$conttp->setVariable("DAT_EVH".$arrModifyData['event_handler_enabled']."_CHECKED","checked");
   		$conttp->setVariable("DAT_FLE".$arrModifyData['flap_detection_enabled']."_CHECKED","checked");
   		$conttp->setVariable("DAT_STI".$arrModifyData['retain_status_information']."_CHECKED","checked");
   		$conttp->setVariable("DAT_NSI".$arrModifyData['retain_nonstatus_information']."_CHECKED","checked");
   		$conttp->setVariable("DAT_PED".$arrModifyData['process_perf_data']."_CHECKED","checked");
   		$conttp->setVariable("DAT_NOE".$arrModifyData['notifications_enabled']."_CHECKED","checked");
   		$conttp->setVariable("DAT_PAR".$arrModifyData['parents_tploptions']."_CHECKED","checked");
   		$conttp->setVariable("DAT_HOG".$arrModifyData['hostgroups_tploptions']."_CHECKED","checked");
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
    	if ($myDataClass->infoRelation("tbl_host",$arrModifyData['id'],"host_name") != 0) {
      		$conttp->setVariable("ACT_DISABLED","disabled");
      		$conttp->setVariable("ACT_CHECKED","checked");
      		$conttp->setVariable("ACTIVE","1");
      		$strInfo = "<br><span class=\"dbmessage\">".translate('Entry cannot be activated because it is used by another configuration').":</span><br><span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
      		$conttp->setVariable("CHECK_MUST_DATA",$strInfo);
			$conttp->setVariable("RELATION_CLASS","elementShow");
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
  	$mastertp->setVariable("FIELD_1",translate('Host name'));
  	$mastertp->setVariable("FIELD_2",translate('Description'));
  	$mastertp->setVariable("LIMIT",$chkLimit);
  	$mastertp->setVariable("ACTION_MODIFY",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
  	$mastertp->setVariable("TABLE_NAME","tbl_host");
  	$mastertp->setVariable("MAX_ID","0");
  	$mastertp->setVariable("MIN_ID","0");
  	$mastertp->setVariable("DAT_SEARCH",$_SESSION['search']['host']);
  	// Get Group id's with READ
  	$strAccess = $myVisClass->getAccGroupRead($_SESSION['userid']);
	// Include domain list
	$myVisClass->insertDomainList($mastertp);
  	// Process filter string
  	$strSearchWhere = "";
  	if ($_SESSION['search']['host'] != "") {
		$strSearchTxt   = $_SESSION['search']['host'];
    	$strSearchWhere = "AND (`host_name` LIKE '%".$strSearchTxt."%' OR `alias` LIKE '%".$strSearchTxt."%' OR
                		   `display_name` LIKE '%".$strSearchTxt."%' OR `address` LIKE '%".$strSearchTxt."%')";
  	}
  	// Count datasets
  	$strSQL    = "SELECT count(*) AS `number` FROM `tbl_host` WHERE $strDomainWhere $strSearchWhere AND `access_group` IN ($strAccess)";
  	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
  	if ($booReturn == false) {
		$myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError,$strMessage);
  	} else {
    	$intCount = (int)$arrDataLinesCount['number'];
  	}
	// Get datasets
  	if ($chkLimit > $intCount) $chkLimit = 0;
  	$strSQL    = "SELECT `id`, `host_name`, `alias`, `active`, `last_modified`, `config_id` FROM `tbl_host` WHERE $strDomainWhere $strSearchWhere
          		  AND `access_group` IN ($strAccess) ORDER BY `config_id`, `host_name` LIMIT $chkLimit,".$SETS['common']['pagelines'];
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	$mastertp->setVariable("CELLCLASS_L","tdlb");
	$mastertp->setVariable("CELLCLASS_M","tdmb");	
	$mastertp->setVariable("DISABLED","disabled");
	$mastertp->setVariable("DATA_FIELD_1",translate('No data'));
	$mastertp->setVariable("DATA_FIELD_2","&nbsp;");
	$mastertp->setVariable("DATA_ACTIVE","&nbsp;");
	$mastertp->setVariable("DATA_FILE","&nbsp;");
	$mastertp->setVariable("CHB_CLASS","checkbox");
	$mastertp->setVariable("PICTURE_CLASS","elementHide");
	$mastertp->setVariable("PICTURE_CLASS_2","elementHide");
  	if ($booReturn == false) {
    	$myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError,$strMessage);
  	} else if ($intDataCount != 0) {
    	$y=0; $z=0;
    	for ($i=0;$i<$intDataCount;$i++) {
      		// Get biggest and smalest value
      		if ($i == 0) {$y = $arrDataLines[$i]['id']; $z = $arrDataLines[$i]['id'];}
      		if ($arrDataLines[$i]['id'] < $y) $y = $arrDataLines[$i]['id'];
      		if ($arrDataLines[$i]['id'] > $z) $z = $arrDataLines[$i]['id'];
      		$mastertp->setVariable("MAX_ID",$z);
      		$mastertp->setVariable("MIN_ID",$y);
      		// Line colours
      		$strClassL = "tdld"; $strClassM = "tdmd"; $strChbClass = "checkboxline";
      		if ($i%2 == 1) {$strClassL = "tdlb"; $strClassM = "tdmb"; $strChbClass = "checkbox";}
      		if ($arrDataLines[$i]['active'] == 0) {$strActive = translate('No');} else {$strActive = translate('Yes');}
      		// Get file date
     		$intDate = $myConfigClass->lastModifiedDir($arrDataLines[$i]['host_name'],$arrDataLines[$i]['id'],"host",$strTimeEntry,$strTimeFile,$intOlder);
			$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage); 
			// Set datafields
      		foreach($arrDescription AS $elem) {
        		$mastertp->setVariable($elem['name'],$elem['string']);
      		}
      		if (strlen($arrDataLines[$i]['host_name']) > 50) {$strAdd = ".....";} else {$strAdd = "";}
			$mastertp->setVariable("DATA_FIELD_1",htmlspecialchars(substr($arrDataLines[$i]['host_name'],0,50).$strAdd,ENT_COMPAT,'UTF-8'));
			$mastertp->setVariable("DATA_FIELD_2",htmlspecialchars($arrDataLines[$i]['alias'],ENT_COMPAT,'UTF-8'));
			$mastertp->setVariable("DATA_ACTIVE",$strActive);
			$mastertp->setVariable("DATA_FILE","<span class=\"dbmessage\">".translate('out-of-date')."</span>");
			$mastertp->setVariable("LINE_ID",$arrDataLines[$i]['id']);
			$mastertp->setVariable("CELLCLASS_L",$strClassL);
			$mastertp->setVariable("CELLCLASS_M",$strClassM);
			$mastertp->setVariable("CHB_CLASS",$strChbClass);
			$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
			$mastertp->setVariable("PICTURE_CLASS","elementShow");
			$mastertp->setVariable("PICTURE_CLASS_2","elementShow");
			$mastertp->setVariable("DISABLED","");
			if ($intOlder == 0) $mastertp->setVariable("DATA_FILE",translate('up-to-date'));
			if ($intDate  == 1) $mastertp->setVariable("DATA_FILE","<span class=\"dbmessage\">".translate('missed')."</span>");
			if ($chkModus != "display") $mastertp->setVariable("DISABLED","disabled");
			// Disable common domain objects
			if ($arrDataLines[$i]['config_id'] != $chkDomainId) {
				$mastertp->setVariable("DISABLED","disabled");
				$mastertp->setVariable("PICTURE_CLASS","elementHide");
				$mastertp->setVariable("PICTURE_CLASS_2","elementHide");
				$mastertp->setVariable("DOMAIN_SPECIAL"," [common]");
			}
			if ($chkDomainId == 0) {
				$mastertp->setVariable("PICTURE_CLASS_2","elementHide");
				$mastertp->setVariable("DATA_FILE","&nbsp;");
			}
			$mastertp->parse("datarowhost");
    	}
  	} else {
		// Disable common domain objects
		if ($chkDomainId == 0) {
			$mastertp->setVariable("DISABLED","disabled");
			$mastertp->setVariable("PICTURE_CLASS","elementHide");
			$mastertp->setVariable("PICTURE_CLASS_2","elementHide");
			$mastertp->setVariable("DOMAIN_SPECIAL","&nbsp;");
		}
		$mastertp->parse("datarowhost");
  	}
	// Show page numbers
  	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
  	if (isset($intCount)) $mastertp->setVariable("PAGES",$myVisClass->buildPageLinks(filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING),$intCount,$chkLimit));
  	$mastertp->parse("datatablehost");
  	$mastertp->show("datatablehost");
}
// Show messages
$mastertp->setVariable("DBMESSAGE",$strMessage);
$mastertp->parse("msgfooterhost");
$mastertp->show("msgfooterhost");
//
// Process footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>