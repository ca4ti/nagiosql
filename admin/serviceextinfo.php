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
// Component : Service extended information definition
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
$intSub       	= 15;
$intMenu      	= 2;
$preContent   	= "admin/serviceextinfo.tpl.htm";
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
$chkSelHost        	= isset($_POST['selHost'])        	? $_POST['selHost']         	: 0;
$chkSelService     	= isset($_POST['selService'])     	? $_POST['selService']      	: 0;
$chkTfNotes        	= isset($_POST['tfNotes'])        	? $_POST['tfNotes']         	: "";
$chkTfNotesURL     	= isset($_POST['tfNotesURL'])     	? $_POST['tfNotesURL']      	: "";
$chkTfActionURL    	= isset($_POST['tfActionURL'])    	? $_POST['tfActionURL']     	: "";
$chkTfIconImage    	= isset($_POST['tfIconImage'])    	? $_POST['tfIconImage']     	: "";
$chkTfIconImageAlt 	= isset($_POST['tfIconImageAlt']) 	? $_POST['tfIconImageAlt']  	: "";
$chkSelAccessGroup	= isset($_POST['selAccessGroup'])	? $_POST['selAccessGroup']+0	: 0;
//
// Quote special characters
// ==========================
if (get_magic_quotes_gpc() == 0) {
  	$chkTfSearch		= addslashes($chkTfSearch);
 	$chkTfNotes         = addslashes($chkTfNotes);
 	$chkTfNotesURL      = addslashes($chkTfNotesURL);
  	$chkTfActionURL     = addslashes($chkTfActionURL);
  	$chkTfIconImage     = addslashes($chkTfIconImage);
  	$chkTfIconImageAlt  = addslashes($chkTfIconImageAlt);
}
//
// Search/Filter - Session data
// ============================
if (!isset($_SESSION['search']) || !isset($_SESSION['search']['serviceextinfo'])) $_SESSION['search']['serviceextinfo'] = "";
if (($chkModus == "checkform") || ($chkModus == "filter")) {
  	$_SESSION['search']['serviceextinfo'] = $chkTfSearch;
}
// 
// Add or modify data
// ==================
if (($chkModus == "insert") || ($chkModus == "modify")) {
	if ($hidActive   == 1) $chkActive = 1;
	if ($chkGroupAdm == 1) {$strGroupSQL = "`access_group`=$chkSelAccessGroup, ";} else {$strGroupSQL = "";}
  	$strSQLx = "`tbl_serviceextinfo` SET `host_name`='$chkSelHost', `service_description`='$chkSelService', `notes`='$chkTfNotes',
        		`notes_url`='$chkTfNotesURL', `action_url`='$chkTfActionURL', `icon_image`='$chkTfIconImage',
        		`icon_image_alt`='$chkTfIconImageAlt', $strGroupSQL `active`='$chkActive', `config_id`=$chkDomainId, `last_modified`=NOW()";
  	if ($chkModus == "insert") {
    	$strSQL = "INSERT INTO ".$strSQLx;
 	 } else {
    	$strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
  	}
  	if ($chkSelHost != 0) {
    	$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
		$myDataClass->updateStatusTable("tbl_serviceextinfo");
		if ($chkModus == "insert")  $chkDataId = $intInsertId;
    	if ($intInsert == 1) {
      		$intReturn = 1;
    	} else {
      		if ($chkModus == "insert") $myDataClass->writeLog(translate('New service extended information inserted:')." ".$chkSelHost."::".$chkSelService);
      		if ($chkModus == "modify") $myDataClass->writeLog(translate('Service extended information modified:')." ".$chkSelHost."::".$chkSelService);
			//
			// Update Import HASH
			// ==================
			$booReturn = $myDataClass->updateHash('tbl_serviceextinfo',$chkDataId);
			$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
      		$intReturn = 0;
    	}
		
  	} else {
    	$myVisClass->processMessage(translate('Database entry failed! Not all necessary data filled in!'),$strMessage);
  	}
  	$chkModus    = "display";
} else if ($chkModus == "make") {
	// Write configuration file
  	$intReturn   = $myConfigClass->createConfig("tbl_serviceextinfo",0);
	$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "info")) {
	// Display additional relation information
  	$myDataClass->infoRelation("tbl_serviceextinfo",$chkListId,"host_name");
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$intReturn   = 0;
 	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Delete selected datasets
  	$intReturn   = $myDataClass->dataDeleteEasy("tbl_serviceextinfo","id",$chkListId);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Copy selected datasets
  	$intReturn   = $myDataClass->dataCopyEasy("tbl_serviceextinfo","notes",$chkListId,$chkSelTargetDomain);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "activate")) {
	// Activate selected datasets
	$intReturn   = $myDataClass->dataActivate("tbl_serviceextinfo",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus    = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "deactivate")) {
	// Deactivate selected datasets
	$intReturn   = $myDataClass->dataDeactivate("tbl_serviceextinfo",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	$chkModus    = "display"; 
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Open a dataset to modify
	$booReturn   = $myDBClass->getSingleDataset("SELECT * FROM `tbl_serviceextinfo` WHERE `id`=".$chkListId,$arrModifyData);
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
			if ($arrModifyData['host_name'] == 0){
	  			$chkModus 	  = "refresh";	
			} else {
	  			$chkModus 	  = "add";	
			}
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
$myConfigClass->lastModified("tbl_serviceextinfo",$strLastModified,$strFileDate,$strOld);
$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Start content
// =============
$conttp->setVariable("TITLE",translate('Define service extended information (serviceextinfo.cfg)'));
$conttp->parse("header");
$conttp->show("header");
//
// Singe data form
// ===============
if (($chkModus == "add") || ($chkModus == "refresh")) {
	if ($chkModus == "refresh") {
		$_SESSION['refresh']['se_host']			= $chkSelHost;
  	} else {
		$_SESSION['refresh']['se_host']			= $chkSelHost;
    	if (isset($arrModifyData['host_name']) && ($arrModifyData['host_name'] != 0 )){
      		$strSQL     = "SELECT `host_name` FROM `tbl_serviceextinfo` WHERE `id` = ".$arrModifyData['id'];
      		$booReturn  = $myDBClass->getDataArray($strSQL,$arrData,$intDC);
      		if ($intDC != 0) {
        		$_SESSION['refresh']['se_host'] = $arrData[0]['host_name'];
      		}
    	} else {
      		$strSQL   = "SELECT `id` FROM `tbl_host` WHERE `active`='1' AND `config_id`=$chkDomainId ORDER BY `host_name`";
      		$booReturn  = $myDBClass->getDataArray($strSQL,$arrData,$intDC);
      		if ($intDC != 0) {
        		$_SESSION['refresh']['se_host'] = $arrData[0]['id'];
      		}
		}
  	}
	// Process host selection field
  	$intReturn1 = 0;
  	if (isset($arrModifyData['host_name'])) {$intFieldId = $arrModifyData['host_name'];} else {$intFieldId = 0;}
	if (($chkModus == "refresh") && (count($chkSelHost) != 0)) $intFieldId = $chkSelHost;
  	$intReturn1 = $myVisClass->parseSelectSimple('tbl_host','host_name','host',0,$intFieldId);
  	if ($intReturn1 != 0) $strDBWarning .= translate('Attention, no hosts defined!')."<br>";
  	// Process service selection field
	if (isset($arrModifyData['service_description'])) {$intFieldId = $arrModifyData['service_description'];} else {$intFieldId = 0;}
	$intReturn1 = $myVisClass->parseSelectSimple('tbl_service','service_description','service_extinfo',0,$intFieldId);
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
  	if ($chkModus == "refresh") {
    	$conttp->setVariable("DAT_NOTES",$chkTfNotes);
   	 	$conttp->setVariable("DAT_NOTES_URL",$chkTfNotesURL);
    	$conttp->setVariable("DAT_ACTION_URL",$chkTfActionURL);
    	$conttp->setVariable("DAT_ICON_IMAGE",$chkTfIconImage);
    	$conttp->setVariable("DAT_ICON_IMAGE_ALT",$chkTfIconImageAlt);
    	if ($chkActive != 1) $conttp->setVariable("ACT_CHECKED","");
    	if ($chkDataId != 0) {
      		$conttp->setVariable("MODUS","modify");
      		$conttp->setVariable("DAT_ID",$chkDataId);
    	}
  	// Insert data from database in "modify" mode
  	} else if (isset($arrModifyData) && ($chkSelModify == "modify")) {
    	foreach($arrModifyData AS $key => $value) {
      		if (($key == "active") || ($key == "last_modified") || ($key == "access_rights")) continue;
      		$conttp->setVariable("DAT_".strtoupper($key),htmlentities($value,ENT_QUOTES,'UTF-8'));
    	}
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
  	$mastertp->setVariable("FIELD_1",translate('Hostname'));
  	$mastertp->setVariable("FIELD_2",translate('Service'));
  	$mastertp->setVariable("LIMIT",$chkLimit);
  	$mastertp->setVariable("ACTION_MODIFY",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
  	$mastertp->setVariable("TABLE_NAME","tbl_serviceextinfo");
  	// Get Group id's with READ
  	$strAccess = $myVisClass->getAccGroupRead($_SESSION['userid']);
	// Include domain list
	$myVisClass->insertDomainList($mastertp);
  	// Process filter string
  	$strSearchWhere = "";
 	if ($_SESSION['search']['serviceextinfo'] != "") {
  		$strSearchTxt   = $_SESSION['search']['serviceextinfo'];
  		$strSearchWhere = "AND (`tbl_host`.`host_name` LIKE '%".$strSearchTxt."%' OR `tbl_serviceextinfo`.`notes` LIKE '%".$strSearchTxt."%' 
						   OR `tbl_serviceextinfo`.`notes_url` LIKE '%".$strSearchTxt."%')";
  	}
  	// Count datasets
	$myConfigClass->getConfigData("enable_common",$setEnableCommon);
	if ($setEnableCommon != 0) {
		$strDomainWhere = " (`tbl_serviceextinfo`.`config_id`=$chkDomainId OR `tbl_serviceextinfo`.`config_id`=0) ";	
	} else {
		$strDomainWhere = " (`tbl_serviceextinfo`.`config_id`=$chkDomainId) ";
	}
	$strSQL    = "SELECT count(*) AS `number` FROM `tbl_serviceextinfo`
          		  LEFT JOIN `tbl_host` ON `tbl_serviceextinfo`.`host_name` = `tbl_host`.`id`
				  LEFT JOIN `tbl_service` ON `tbl_serviceextinfo`.`service_description` = `tbl_service`.`id`
          		  WHERE $strDomainWhere $strSearchWhere AND `tbl_serviceextinfo`.`access_group` IN ($strAccess)";
  	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
  	if ($booReturn == false) {
    	$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
  	} else {
    	$intCount = (int)$arrDataLinesCount['number'];
  	}
  	// Get datasets
  	$strSQL    = "SELECT `tbl_serviceextinfo`.`id`, `tbl_host`.`host_name`, `tbl_service`.`service_description`, `tbl_serviceextinfo`.`notes`,
				  `tbl_serviceextinfo`.`active`, `tbl_serviceextinfo`.`config_id` FROM `tbl_serviceextinfo`
          		  LEFT JOIN `tbl_host` ON `tbl_serviceextinfo`.`host_name` = `tbl_host`.`id`
				  LEFT JOIN `tbl_service` ON `tbl_serviceextinfo`.`service_description` = `tbl_service`.`id`
          		  WHERE $strDomainWhere $strSearchWhere AND `tbl_serviceextinfo`.`access_group` IN ($strAccess)
          		  ORDER BY `tbl_serviceextinfo`.`config_id`, `host_name`,`tbl_service`.`service_description` LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
      		if ($arrDataLines[$i]['host_name'] != "") {
        		$mastertp->setVariable("DATA_FIELD_1",htmlspecialchars($arrDataLines[$i]['host_name'],ENT_COMPAT,'UTF-8'));
      		} else {
        		$mastertp->setVariable("DATA_FIELD_1","NOT DEFINED - ".$arrDataLines[$i]['id']);
      		}
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