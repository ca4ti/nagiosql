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
// Component : Service definition
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2012-04-17 12:09:50 +0200 (Tue, 17 Apr 2012) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1321 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$prePageId			= 9;
$preContent   		= "admin/services.tpl.htm";
$preSearchSession	= 'service';
$preTableName		= 'tbl_service';
$preKeyField		= 'config_name';
$preAccess    		= 1;
$preFieldvars 		= 1;
//
// Include preprocessing files
// ===========================
require("../functions/prepend_adm.php");
require("../functions/prepend_content.php");
//
// Data processing
// ===============
$strNO = substr($chkChbGr1a.$chkChbGr1b.$chkChbGr1c.$chkChbGr1d.$chkChbGr1e.$chkChbGr1f,0,-1);
$strIS = substr($chkChbGr2a.$chkChbGr2b.$chkChbGr2c.$chkChbGr2d,0,-1);
$strFL = substr($chkChbGr3a.$chkChbGr3b.$chkChbGr3c.$chkChbGr3d,0,-1);
$strST = substr($chkChbGr4a.$chkChbGr4b.$chkChbGr4c.$chkChbGr4d,0,-1);
if ($chkSelValue1 != "") {
  	for ($i=1;$i<=8;$i++) {
		${"chkTfArg$i"} = str_replace("!","::bang::",${"chkTfArg$i"});
		if (${"chkTfArg$i"} != "") $chkSelValue1 .= "!".${"chkTfArg$i"};
  	}
}
// 
// Add or modify data
// ==================
if ((($chkModus == "insert") || ($chkModus == "modify")) && ($intGlobalWriteAccess == 0)) {
  	$strSQLx = "`$preTableName` SET `$preKeyField`='$chkTfValue1', `host_name`=$intMselValue1, `host_name_tploptions`=$chkRadValue1, `hostgroup_name`=$intMselValue2,
				`hostgroup_name_tploptions`=$chkRadValue2, `service_description`='$chkTfValue3', `display_name`='$chkTfValue4', `servicegroups`=$intMselValue3,
				`servicegroups_tploptions`=$chkRadValue3, `check_command`='$chkSelValue1', `use_template`=$intTemplates, `is_volatile`=$chkRadValue14, 
				`initial_state`='$strIS', `max_check_attempts`=$chkTfNullVal2, `check_interval`=$chkTfNullVal3, `retry_interval`=$chkTfNullVal1,
				`active_checks_enabled`=$chkRadValue4, `passive_checks_enabled`=$chkRadValue5, `check_period`=$chkSelValue2, `parallelize_check`=$chkRadValue6,
				`obsess_over_service`=$chkRadValue8, `check_freshness`=$chkRadValue7, `freshness_threshold`=$chkTfNullVal4, `event_handler`=$chkSelValue3,
        		`event_handler_enabled`=$chkRadValue9, `low_flap_threshold`=$chkTfNullVal5, `high_flap_threshold`=$chkTfNullVal6, `flap_detection_enabled`=$chkRadValue10,
				`flap_detection_options`='$strFL', `process_perf_data`=$chkRadValue13, `retain_status_information`=$chkRadValue11, `retain_nonstatus_information`=$chkRadValue12,
				`contacts`=$intMselValue4, `contacts_tploptions`=$chkRadValue15, `contact_groups`=$intMselValue5, `contact_groups_tploptions`=$chkRadValue16,
        		`notification_interval`=$chkTfNullVal7, `notification_period`=$chkSelValue4, `first_notification_delay`=$chkTfNullVal8, `notification_options`='$strNO',
				`notifications_enabled`=$chkRadValue17, `stalking_options`='$strST', `notes`='$chkTfValue5', `notes_url`='$chkTfValue6', `action_url`='$chkTfValue7',
        		`icon_image`='$chkTfValue8', `icon_image_alt`='$chkTfValue9', `name`='$chkTfValue10', $preSQLCommon1";
	if ($chkModus == "insert") {
		$strSQL 		= "INSERT INTO ".$strSQLx;
  	} else {
    	$strSQL			= "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
  	}
	if ($intWriteAccessId == 0) {
		if (($chkTfValue1 != "") && ($chkTfValue3 != "")) {
			$intReturn = $myDataClass->dataInsert($strSQL,$intInsertId);
			if ($chkModus == "insert")  $chkDataId = $intInsertId;
			if ($intReturn == 1) {
				$myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
			} else {
				$myVisClass->processMessage($myDataClass->strInfoMessage,$strInfoMessage);
				if ($chkModus == "insert") $myDataClass->writeLog(translate('New service inserted:')." ".$chkTfValue1);
				if ($chkModus == "modify") $myDataClass->writeLog(translate('Service modified:')." ".$chkTfValue1);
				//
				// Insert/update relations
				// =======================
				if ($chkModus == "insert") {
					if ($intMselValue1 != 0) $intRet1 = $myDataClass->dataInsertRelation("tbl_lnkServiceToHost",$chkDataId,$chkMselValue1);
					if (isset($intRet1) && ($intRet1 != 0)) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
					if ($intMselValue2 != 0) $intRet2 = $myDataClass->dataInsertRelation("tbl_lnkServiceToHostgroup",$chkDataId,$chkMselValue2);
					if (isset($intRet2) && ($intRet2 != 0)) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
					if ($intMselValue3 != 0) $intRet3 = $myDataClass->dataInsertRelation("tbl_lnkServiceToServicegroup",$chkDataId,$chkMselValue3);
					if (isset($intRet3) && ($intRet3 != 0)) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
					if ($intMselValue4 != 0) $intRet4 = $myDataClass->dataInsertRelation("tbl_lnkServiceToContact",$chkDataId,$chkMselValue4);
					if (isset($intRet4) && ($intRet4 != 0)) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
					if ($intMselValue5 != 0) $intRet5 = $myDataClass->dataInsertRelation("tbl_lnkServiceToContactgroup",$chkDataId,$chkMselValue5);
					if (isset($intRet5) && ($intRet5 != 0)) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
				} else if ($chkModus == "modify") {
					if ($intMselValue1 != 0) {
						$intRet1 = $myDataClass->dataUpdateRelation("tbl_lnkServiceToHost",$chkDataId,$chkMselValue1);
					} else {
						$intRet1 = $myDataClass->dataDeleteRelation("tbl_lnkServiceToHost",$chkDataId);
					}
					if (isset($intRet1) && ($intRet1 != 0)) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
					if ($intMselValue2 != 0) {
						$intRet2 = $myDataClass->dataUpdateRelation("tbl_lnkServiceToHostgroup",$chkDataId,$chkMselValue2);
					} else {
						$intRet2 = $myDataClass->dataDeleteRelation("tbl_lnkServiceToHostgroup",$chkDataId);
					}
					if (isset($intRet1) && ($intRet1 != 0)) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
					if ($intMselValue3 != 0) {
						$intRet3 = $myDataClass->dataUpdateRelation("tbl_lnkServiceToServicegroup",$chkDataId,$chkMselValue3);
					} else {
						$intRet3 = $myDataClass->dataDeleteRelation("tbl_lnkServiceToServicegroup",$chkDataId);
					}
					if (isset($intRet1) && ($intRet1 != 0)) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
					if ($intMselValue4 != 0) {
						$intRet4 = $myDataClass->dataUpdateRelation("tbl_lnkServiceToContact",$chkDataId,$chkMselValue4);
					} else {
						$intRet4 = $myDataClass->dataDeleteRelation("tbl_lnkServiceToContact",$chkDataId);
					}
					if (isset($intRet1) && ($intRet1 != 0)) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
					if ($intMselValue5 != 0) {
						$intRet5 = $myDataClass->dataUpdateRelation("tbl_lnkServiceToContactgroup",$chkDataId,$chkMselValue5);
					} else {
						$intRet5 = $myDataClass->dataDeleteRelation("tbl_lnkServiceToContactgroup",$chkDataId);
					}
					if (isset($intRet1) && ($intRet1 != 0)) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
				}
				if (($intRet1 + $intRet2 + $intRet3 + $intRet4 + $intRet5) != 0) $strInfoMessage = "";
				//
				// Removing the config file if an entry was deleted or renamed
				// ===========================================================
				if (($chkModus == "modify") && ($chkTfValue2 != $chkTfValue1) && ($chkDomainId != 0)) {
					$intServiceCount = $myDBClass->countRows("SELECT * FROM `$preTableName` 
															  WHERE BINARY `$preKeyField`='$chkTfValue2' AND `config_id`=$chkDomainId AND `active`='1'");
					if ($intServiceCount == 0) {
						$arrConfigID  = $myConfigClass->getConfigSets();
						if (($arrConfigID != 1) && is_array($arrConfigID)) {
							$intReturn = 0;
							foreach($arrConfigID AS $intConfigID) {
								$intReturn += $myConfigClass->moveFile("service",$chkTfValue2.".cfg",$intConfigID);
							}
							if ($intReturn == 0) {
								$myVisClass->processMessage(translate('The assigned, no longer used configuration files were deleted successfully!'),$strInfoMessage);
								$myDataClass->writeLog(translate('Service file deleted:')." ".$chkTfValue2.".cfg");
							} else {
								if ($chkDomainId == 0) {
									$myVisClass->processMessage(translate('Common files cannot be removed from target systems - please check manually'),$strErrorMessage);
								} else {
									$myVisClass->processMessage(translate('Errors while deleting the old configuration file - please check!:'),$strErrorMessage);
									$myVisClass->processMessage($myConfigClass->strErrorMessage,$strErrorMessage);
								}
							}
						}
					}
				}
				//
				// Removing the config file if an entry was dectivated
				// ===================================================
				if (($chkModus == "modify") && ($chkActive == 0)) {
					$intServiceCount = $myDBClass->countRows("SELECT * FROM `$preTableName` WHERE `$preKeyField`='$chkTfValue2' AND `config_id`=$chkDomainId AND `active`='1'");
					if ($intServiceCount == 0) {
						$arrConfigID  = $myConfigClass->getConfigSets();
						if (($arrConfigID != 1) && is_array($arrConfigID)) {
							$intReturn = 0;
							foreach($arrConfigID AS $intConfigID) {
								$intReturn += $myConfigClass->moveFile("service",$chkTfValue2.".cfg",$intConfigID);
							}
							if ($intReturn == 0) {
								$myVisClass->processMessage(translate('The assigned, no longer used configuration files were deleted successfully!'),$strInfoMessage);
								$myDataClass->writeLog(translate('Service file deleted:')." ".$chkTfValue2.".cfg");
							} else {
								$myVisClass->processMessage(translate('Errors while deleting the old configuration file - please check!:'),$strErrorMessage);
								$myVisClass->processMessage($myConfigClass->strErrorMessage,$strErrorMessage);
							}
						}
					}
				}
				//
				// Insert/update session data for templates
				// ========================================
				if ($chkModus == "modify") {
					$strSQL   	= "DELETE FROM `tbl_lnkServiceToServicetemplate` WHERE `idMaster`=$chkDataId";
					$intReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
					if ($intReturn != 0) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
				}
				if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition']) && (count($_SESSION['templatedefinition']) != 0)) {
					$intSortId = 1;
					foreach($_SESSION['templatedefinition'] AS $elem) {
						if ($elem['status'] == 0) {
							$strSQL 	= "INSERT INTO `tbl_lnkServiceToServicetemplate` (`idMaster`,`idSlave`,`idTable`,`idSort`)
										   VALUES ($chkDataId,".$elem['idSlave'].",".$elem['idTable'].",".$intSortId.")";
							$intReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
							if ($intReturn != 0) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
						}
						$intSortId++;
					}
				}
				//
				// Insert/update session data for free variables
				// =============================================
				if ($chkModus == "modify") {
					$strSQL   	= "SELECT * FROM `tbl_lnkServiceToVariabledefinition` WHERE `idMaster`=$chkDataId";
					$booReturn  = $myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
					if ($booReturn == false) $myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
					if ($intDataCount != 0) {
						foreach ($arrData AS $elem) {
							$strSQL   	= "DELETE FROM `tbl_variabledefinition` WHERE `id`=".$elem['idSlave'];
							$intReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
							if ($intReturn != 0) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
						}
					}
					$strSQL   	= "DELETE FROM `tbl_lnkServiceToVariabledefinition` WHERE `idMaster`=$chkDataId";
					$intReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
					if ($intReturn != 0) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
					$strSQL 	= "UPDATE `tbl_service` SET `use_variables`=0 WHERE `id`=$chkDataId";
					$intReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
					if ($intReturn != 0) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
				}
				if (isset($_SESSION['variabledefinition']) && is_array($_SESSION['variabledefinition']) && (count($_SESSION['variabledefinition']) != 0)) {
					$intCountVariable = 0;
					foreach($_SESSION['variabledefinition'] AS $elem) {
						if ($elem['status'] == 0) {
							$strSQL 	= "INSERT INTO `tbl_variabledefinition` (`name`,`value`,`last_modified`)
										   VALUES ('".$elem['definition']."','".$elem['range']."',now())";
							$intReturn1 = $myDataClass->dataInsert($strSQL,$intInsertId);
							if ($intReturn1 != 0) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
							$strSQL 	= "INSERT INTO `tbl_lnkServiceToVariabledefinition` (`idMaster`,`idSlave`)
										   VALUES ($chkDataId,$intInsertId)";
							$intReturn2 = $myDataClass->dataInsert($strSQL,$intInsertId);
							if ($intReturn2 != 0) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
							if (($intReturn1 == 0) && ($intReturn2 == 0)) $intCountVariable++;
						}
					}
					if ($intCountVariable != 0) {
						$strSQL 	= "UPDATE `tbl_service` SET `use_variables`=1 WHERE `id`=$chkDataId";
						$intReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
						if ($intReturn != 0) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
					}
				}
				//
				// Update Import HASH
				// ==================
				$intReturn = $myDataClass->updateHash($preTableName,$chkDataId);
				if ($intReturn != 0) $myVisClass->processMessage($myDataClass->strErrorMessage,$strErrorMessage);
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
// Start content
// =============
$conttp->setVariable("TITLE",translate('Define services (services.cfg)'));
$conttp->parse("header");
$conttp->show("header");
//
// Singe data form
// ===============
if ($chkModus == "add") {
	// Do not show modified time list
	$intNoTime = 1;
	// Process template fields
  	$strWhere = "";
  	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
    	$strWhere = "AND `id` <> ".$arrModifyData['id'];
  	}
  	$strSQL    = "SELECT `id`,`template_name`, `active` FROM `tbl_servicetemplate` WHERE $strDomainWhere2 ORDER BY `template_name`";
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataTpl,$intDataCountTpl);
	if ($booReturn == false) $myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
  	if ($intDataCountTpl != 0) {
    	foreach ($arrDataTpl AS $elem) {
			if ($elem['active'] == 0) { 
				$strActive = " [inactive]";
				$conttp->setVariable("SPECIAL_STYLE","inactive_option");
			} else {
				$strActive = "";
				$conttp->setVariable("SPECIAL_STYLE","");
			}
      		$conttp->setVariable("DAT_TEMPLATE",htmlspecialchars($elem['template_name'],ENT_QUOTES,'UTF-8').$strActive);
      		$conttp->setVariable("DAT_TEMPLATE_ID",$elem['id']."::1");
      		$conttp->parse("template");
    	}
  	}
  	$strSQL    = "SELECT `id`, `name`, `active` FROM `$preTableName` WHERE `name` <> '' $strWhere AND $strDomainWhere ORDER BY `name`";
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataHpl,$intDataCount);
	if ($booReturn == false) $myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
  	if ($arrDataHpl != 0) {
    	foreach ($arrDataHpl AS $elem) {
			if ($elem['active'] == 0) { 
				$strActive = " [inactive]";
				$conttp->setVariable("SPECIAL_STYLE","inactive_option");
			} else {
				$strActive = "";
				$conttp->setVariable("SPECIAL_STYLE","");
			}
      		$conttp->setVariable("DAT_TEMPLATE",htmlspecialchars($elem['name'],ENT_QUOTES,'UTF-8').$strActive);
      		$conttp->setVariable("DAT_TEMPLATE_ID",$elem['id']."::2");
      		$conttp->parse("template");
    	}
  	}
	// Process host selection field
  	if (isset($arrModifyData['host_name'])) {$intFieldId = $arrModifyData['host_name'];} else {$intFieldId = 0;}
	$intReturn1 = $myVisClass->parseSelectMulti('tbl_host','host_name','hosts','tbl_lnkServiceToHost',2,$intFieldId);
	if ($intReturn1 != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
  	if (isset($arrModifyData['hostgroup_name'])) {$intFieldId = $arrModifyData['hostgroup_name'];} else {$intFieldId = 0;}
	$intReturn2 = $myVisClass->parseSelectMulti('tbl_hostgroup','hostgroup_name','hostgroup','tbl_lnkServiceToHostgroup',2,$intFieldId);
	if ($intReturn2 != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
	if (($intReturn1 != 0) && ($intReturn2 != 0)) {
		$myVisClass->processMessage(translate('Attention, no hosts or hostgroups defined!'),$strDBWarning);
		$intDataWarning = 1;
	}
	// Process service groups selection field
  	if (isset($arrModifyData['servicegroups'])) {$intFieldId = $arrModifyData['servicegroups'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectMulti('tbl_servicegroup','servicegroup_name','servicegroup','tbl_lnkServiceToServicegroup',0,$intFieldId);
	if ($intReturn != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
	// Process check command selection field
  	if (isset($arrModifyData['check_command']) && ($arrModifyData['check_command'] != "")) {
    	$arrCommand = explode("!",$arrModifyData['check_command']);
    	$intFieldId = $arrCommand[0];
   	} else {
    	$intFieldId = 0;
   	}
	$intReturn = $myVisClass->parseSelectSimple('tbl_command','command_name','servicecommand',2,$intFieldId);
	if ($intReturn != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
	if ($intReturn != 0) {
		$myVisClass->processMessage(translate('Attention, no check commands defined!'),$strDBWarning);
		$intDataWarning = 1;
	}
  	// Process check period selection field
  	if (isset($arrModifyData['check_period'])) {$intFieldId = $arrModifyData['check_period'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectSimple('tbl_timeperiod','timeperiod_name','checkperiod',1,$intFieldId);
	if ($intReturn != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
  	if (isset($arrModifyData['notification_period'])) {$intFieldId = $arrModifyData['notification_period'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectSimple('tbl_timeperiod','timeperiod_name','notifyperiod',1,$intFieldId);
	if ($intReturn != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
	if ($intReturn != 0) {
		$myVisClass->processMessage(translate('Attention, no time periods defined!'),$strDBWarning);
		$intDataWarning = 1;
	}
  	// Process event handler selection field
  	if (isset($arrModifyData['event_handler'])) {$intFieldId = $arrModifyData['event_handler'];} else {$intFieldId = 0;}
	$intReturn = $myVisClass->parseSelectSimple('tbl_command','command_name','eventhandler',1,$intFieldId);
	if ($intReturn != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
  	// Process contact and contact group selection field
  	if (isset($arrModifyData['contacts'])) {$intFieldId = $arrModifyData['contacts'];} else {$intFieldId = 0;}
	$intReturn1 = $myVisClass->parseSelectMulti('tbl_contact','contact_name','service_contacts','tbl_lnkServiceToContact',2,$intFieldId);
	if ($intReturn1 != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
  	if (isset($arrModifyData['contact_groups'])) {$intFieldId = $arrModifyData['contact_groups'];} else {$intFieldId = 0;}
	$intReturn2 = $myVisClass->parseSelectMulti('tbl_contactgroup','contactgroup_name','service_contactgroups','tbl_lnkServiceToContactgroup',2,$intFieldId);
	if ($intReturn2 != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
  	if (($intReturn != 0) && ($intReturn2 != 0)) {
		$myVisClass->processMessage(translate('Attention, no contacts or contact groups defined!'),$strDBWarning);
		$intDataWarning = 1;
	}		
  	// Process access group selection field
  	if (isset($arrModifyData['access_group'])) {$intFieldId = $arrModifyData['access_group'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectSimple('tbl_group','groupname','acc_group',0,$intFieldId);
	if ($intReturn != 0) $myVisClass->processMessage($myVisClass->strErrorMessage,$strErrorMessage);
	// Initial add/modify form definitions
	$strChbFields = "ACE,PCE,PAC,FRE,OBS,EVH,FLE,STI,NSI,PED,ISV,NOE,HOS,HOG,SEG,COT,COG,TPL";
	$myContentClass->addFormInit($conttp,$strChbFields);
	if ($intDataWarning == 1) $conttp->setVariable("WARNING",$strDBWarning."<br>".translate('Saving not possible!'));
  	// Insert data from database in "modify" mode
  	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		// Check relation information to find out locked configuration datasets
		$intLocked = $myDataClass->infoRelation($preTableName,$arrModifyData['id'],$preKeyField);
		$myVisClass->processMessage($myDataClass->strInfoMessage,$strRelMessage);
		$strInfo  = "<br><span class=\"redmessage\">".translate('Entry cannot be activated because it is used by another configuration').":</span>";
		$strInfo .= "<br><span class=\"greenmessage\">".$strRelMessage."</span>";
		// Process data
		$myContentClass->addInsertData($conttp,$arrModifyData,$intLocked,$strInfo,$strChbFields);
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
    	$strIntegerfelder  = "max_check_attempts,check_interval,retry_interval,freshness_threshold,low_flap_threshold,high_flap_threshold,";
		$strIntegerfelder .= "notification_interval,first_notification_delay";
    	foreach(explode(",",$strIntegerfelder) AS $elem) {
      		if ($arrModifyData[$elem] == -1) {
        		$conttp->setVariable("DAT_".strtoupper($elem),"null");
      		}
    	}
    	if ($arrModifyData['check_command'] != "") {
      		$arrArgument = explode("!",$arrModifyData['check_command']);
      		foreach ($arrArgument AS $key => $value) {
        		if ($key == 0) {
          			$conttp->setVariable("IFRAME_SRC",$_SESSION['SETS']['path']['base_url']."admin/commandline.php?cname=".$value);
        		} else {
					$value = str_replace("::bang::","!",$value);
					$value = str_replace("::back::","\\",$value);
          			$conttp->setVariable("DAT_ARG".$key,htmlentities($value,ENT_QUOTES,'UTF-8'));
        		}
      		}
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
	$mastertp->setVariable("FIELD_1",translate('Config name'));
	$mastertp->setVariable("FIELD_2",translate('Service name'));
	// Configuration name selection
	$mastertp->setVariable("DAT_CONFIGNAME",translate('All configs'));
  	$mastertp->parse("configlist");
  	$strSQL    = "SELECT DISTINCT `$preKeyField` FROM `$preTableName` WHERE $strDomainWhere ORDER BY `$preKeyField`";
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataConfig,$intDataCount);
  	if ($booReturn == false) {
    	$myVisClass->processMessage(translate('Error while selecting data from database:'),$strErrorMessage);
		$myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
  	} else if ($intDataCount != 0) {
		for ($i=0;$i<$intDataCount;$i++) {
      		$mastertp->setVariable("DAT_CONFIGNAME",$arrDataConfig[$i][$preKeyField]);
      		if ($_SESSION['search']['config_selection'] == $arrDataConfig[$i][$preKeyField]) {
	  			$mastertp->setVariable("DAT_CONFIGNAME_SEL","selected");
	  		}
      		$mastertp->parse("configlist");
    	}
  	}
  	// Process filter string
  	if ($_SESSION['search'][$preSearchSession] != "") {
		$strSearchTxt   = $_SESSION['search'][$preSearchSession];
    	$strSearchWhere = "AND (`$preKeyField` LIKE '%".$strSearchTxt."%' OR `service_description` LIKE '%".$strSearchTxt."%' OR
                		   `display_name` LIKE '%".$strSearchTxt."%')";
  	}
  	if ($_SESSION['search']['config_selection'] != "") {
		$strSearchTxt2   = $_SESSION['search']['config_selection'];
		if ($strSearchTxt2 != translate('All configs')) {
    		$strSearchWhere2 = " AND `$preKeyField` = '".$strSearchTxt2."' ";
		}
  	}
	// Row sorting
	$strOrderString = "ORDER BY `config_id`,`$preKeyField` $hidSortDir";
	if ($hidSortBy == 2) $strOrderString = "ORDER BY `config_id`, `service_description` $hidSortDir";
  	// Count datasets
  	$strSQL    = "SELECT count(*) AS `number` FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere2 $strSearchWhere AND `access_group` IN ($strAccess)";
 	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
  	if ($booReturn == false) {
    	$myVisClass->processMessage(translate('Error while selecting data from database:'),$strErrorMessage);
		$myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
  	} else {
    	$intLineCount = (int)$arrDataLinesCount['number'];
		if ($intLineCount < $chkLimit) $chkLimit = 0;
  	}
  	// Datensätze holen
  	$strSQL    = "SELECT `id`, `$preKeyField`, `service_description`, `register`, `active`, `last_modified`, `config_id`, `access_group` 
				  FROM `$preTableName` WHERE $strDomainWhere $strSearchWhere2 $strSearchWhere AND `access_group` IN ($strAccess) $strOrderString 
				  LIMIT $chkLimit,".$SETS['common']['pagelines'];
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	if ($booReturn == false) {
    	$myVisClass->processMessage(translate('Error while selecting data from database:'),$strErrorMessage);
		$myVisClass->processMessage($myDBClass->strErrorMessage,$strErrorMessage);
  	}
	// Process data
	$myContentClass->listData($mastertp,$arrDataLines,$intDataCount,$intLineCount,$preKeyField,'service_description',0);
	if ($myContentClass->strErrorMessage != "") $myVisClass->processMessage($myContentClass->strErrorMessage,$strErrorMessage);
}
// Show messages
$arrTimeData 		= array();
$strTimeInfoString 	= "";
$myContentClass->showMessages($mastertp,$strErrorMessage,$strInfoMessage,$strConsistMessage,$arrTimeData,$strTimeInfoString,$intNoTime);
//
// Process footer
// ==============
$myContentClass->showFooter($maintp,$setFileVersion);
?>