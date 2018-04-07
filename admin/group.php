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
// Component : Group administration
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
$intMain      = 7;
$intSub       = 31;
$intMenu      = 2;
$preContent   = "admin/group.tpl.htm";
$intCount     = 0;
$strMessage   = "";
$strDBWarning = "";
//
// Include preprocessing file
// ==========================
$preAccess    = 1;
$preFieldvars = 1;
require("../functions/prepend_adm.php");
//
// Process post parameters
// =======================
$chkInsName   = isset($_POST['tfGroupname'])   ? $_POST['tfGroupname']      : "";
$chkInsDesc   = isset($_POST['tfDescription']) ? $_POST['tfDescription']    : "";
$chkHidName   = isset($_POST['hidGroupname'])  ? $_POST['hidGroupname']     : "";
$chkSelUsers  = isset($_POST['selUsers'])      ? $_POST['selUsers']         : array("");
//
// Quote special characters
// ========================
if (get_magic_quotes_gpc() == 0) {
  $chkInsName   = addslashes($chkInsName);
  $chkInsAlias  = addslashes($chkInsDesc);
  $chkHidName   = addslashes($chkInsDesc);
}
// 
// Add or modify data
// ==================
if (($chkSelUsers[0] == "")  || ($chkSelUsers[0] == "0"))  {$intSelUsers = 0;}  else {$intSelUsers = 1;}
if (($chkModus == "insert") || ($chkModus == "modify")) {
	$strSQLx = "`tbl_group` SET `groupname`='$chkInsName', `description`='$chkInsDesc', `users`=$intSelUsers,
	          	`active`='$chkActive', `last_modified`=NOW()";
	if ($chkModus == "insert") {
		$strSQL = "INSERT INTO ".$strSQLx;
	} else {
		$strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
	}
	if (($chkInsName != "") && ($chkInsDesc != "")) {
		$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
		if ($chkModus == "insert") $chkDataId = $intInsertId;
    	if ($intInsert == 1) {
	  		$intReturn = 1;
    	} else {
     		if ($chkModus  == "insert")   $myDataClass->writeLog(translate('A new group added:')." ".$chkInsName);
      		if ($chkModus  == "modify")   $myDataClass->writeLog(translate('User modified:')." ".$chkInsName);
			//
			// Insert/update user/group data from session data
			// ===============================================
			if ($chkModus == "modify") {
				$strSQL   	= "DELETE FROM `tbl_lnkGroupToUser` WHERE `idMaster`=$chkDataId";
				$booReturn  = $myDataClass->dataInsert($strSQL,$intInsertId);
				$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
			}
			if (isset($_SESSION['groupuser']) && is_array($_SESSION['groupuser']) && (count($_SESSION['groupuser']) != 0)) {
				foreach($_SESSION['groupuser'] AS $elem) {
					if ($elem['status'] == 0) {
						$intRead  = 0; $intWrite = 0; $intLink  = 0;
						if (substr_count($elem['rights'],"READ")  != 0) $intRead  = 1;
						if (substr_count($elem['rights'],"WRITE") != 0) $intWrite = 1;
						if (substr_count($elem['rights'],"LINK")  != 0) $intLink  = 1;
						if ($intWrite == 1) $intRead = 1;
						if ($intLink  == 1) $intRead = 1;
						$strSQL = "INSERT INTO `tbl_lnkGroupToUser` (`idMaster`,`idSlave`,`read`,`write`,`link`)
							   VALUES ($chkDataId,".$elem['user'].",'$intRead','$intWrite','$intLink')";
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
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Delete selected datasets
  	$intReturn = $myDataClass->dataDeleteEasy("tbl_group","id",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Copy selected datasets
  	$intReturn = $myDataClass->dataCopyEasy("tbl_group","groupname",$chkListId);
  	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
 	 // Daten des gewählten Datensatzes holen
  	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM `tbl_group` WHERE `id`=".$chkListId,$arrModifyData);
	$myVisClass->processMessage($myDBClass->strDBError,$strMessage);
  	if ($booReturn == false) $myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDataClass->strDBError."<br>",$strMessage);
  	$chkModus      = "add";
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
$conttp->setVariable("TITLE",translate('Group administration'));
$conttp->parse("header");
$conttp->show("header");
//
// Singe data form
// ===============
if ($chkModus == "add") {
	// Process data fields
  	$strSQL    = "SELECT * FROM tbl_user WHERE id <> 1 ORDER BY username";
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
  	if ($booReturn && ($intDataCount != 0)) {
		foreach($arrDataLines AS $elem) {
	  		$conttp->setVariable("DAT_USER_ID",$elem['id']);
	  		$conttp->setVariable("DAT_USER",$elem['username']);
	  		$conttp->parse("users");
		}
  	}
	// Process template text raplacements
  	foreach($arrDescription AS $elem) {
    	$conttp->setVariable($elem['name'],$elem['string']);
  	}
	$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
	$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	$conttp->setVariable("LIMIT",$chkLimit);
	$conttp->setVariable("ACT_CHECKED","checked");
	if ($strDBWarning != "") {
		$conttp->setVariable("WARNING",$strDBWarning);
	} else {
		$conttp->setVariable("WARNING","&nbsp;");
	}
	$conttp->setVariable("MODUS","insert");
	$conttp->setVariable("LANG_READ",translate("Read"));
	$conttp->setVariable("LANG_WRITE",translate("Write"));
	$conttp->setVariable("LANG_LINK",translate("Link"));
	$conttp->setVariable("DAT_ID",$chkListId);
	if ($SETS['common']['seldisable'] == 1)$conttp->setVariable("SELECT_FIELD_DISABLED","disabled");
	$conttp->setVariable("FILL_ALLFIELDS",translate('Please fill in all fields marked with an *'));
	$conttp->setVariable("FILL_ILLEGALCHARS",translate('The following field contains not permitted characters:'));
  	// Insert data from database in "modify" mode
	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		foreach($arrModifyData AS $key => $value) {
			if (($key == "active") || ($key == "last_modified")) continue;
			$conttp->setVariable("DAT_".strtoupper($key),$value);
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
	$mastertp->setVariable("FIELD_1",translate('Groupname'));
	$mastertp->setVariable("FIELD_2",translate('Description'));
	$mastertp->setVariable("DELETE",translate('Delete'));
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",translate('Copy'));
	$mastertp->setVariable("ACTION_MODIFY",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
	$mastertp->setVariable("LANG_DELETESINGLE",translate('Do you really want to delete this database entry:'));
	$mastertp->setVariable("LANG_DELETEOK",translate('Do you really want to delete all marked entries?'));
  	// Count datasets
	$strSQL    = "SELECT count(*) AS `number` FROM `tbl_group`";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {
		$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
  	// Get datasets
	$strSQL    = "SELECT `id`, `groupname`, `description`, `active`
				  FROM `tbl_group` ORDER BY `groupname` LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
    	$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
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
			$mastertp->setVariable("DATA_FIELD_1",htmlspecialchars($arrDataLines[$i]['groupname'],ENT_COMPAT,'UTF-8'));
			$mastertp->setVariable("DATA_FIELD_2",htmlspecialchars($arrDataLines[$i]['description'],ENT_COMPAT,'UTF-8'));
			$mastertp->setVariable("DATA_ACTIVE",$strActive);
			$mastertp->setVariable("LINE_ID",$arrDataLines[$i]['id']);
			$mastertp->setVariable("CELLCLASS_L",$strClassL);
			$mastertp->setVariable("CELLCLASS_M",$strClassM);
			$mastertp->setVariable("CHB_CLASS",$strChbClass);
			$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
			$mastertp->setVariable("PICTURE_CLASS","elementShow");
			$mastertp->setVariable("DISABLED","");
			if ($chkModus != "display") $mastertp->setVariable("DISABLED","disabled");
			$mastertp->parse("datarowcommon");
    	}
  	} else {
		$mastertp->parse("datarowcommon");
	}
	// Show page numbers
  	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
  	if (isset($intCount)) $mastertp->setVariable("PAGES",$myVisClass->buildPageLinks(filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING),$intCount,$chkLimit));
  	$mastertp->parse("datatablecommon");
  	$mastertp->show("datatablecommon");
}
// Show messages
if (isset($strMessage)) {$mastertp->setVariable("DBMESSAGE",$strMessage);} else {$mastertp->setVariable("DBMESSAGE","&nbsp;");}
$mastertp->parse("msgfooter");
$mastertp->show("msgfooter");
//
// Process footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>