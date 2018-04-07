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
// Component : Menu access administration
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
$intMain    	= 7;
$intSub     	= 24;
$intMenu    	= 2;
$preContent 	= "admin/admin_master.tpl.htm";
$strMessage 	= "";
$intError   	= 0;
$intFieldId 	= 0;
//
// Include preprocessing file
// ==========================
$preAccess    	= 1;
$preFieldvars 	= 1;
require("../functions/prepend_adm.php");
//
// Process post parameters
// =======================
$chkSubMenu   	= isset($_POST['selSubMenu'])  ? $_POST['selSubMenu']+0  : 0;
$chkAccGroup  	= isset($_POST['selAccGroup']) ? $_POST['selAccGroup']+0 : 0;
//
// Process data
// ============
if (isset($_POST['subSave']) && ($chkSubMenu != 0)) {
  	$strSQL = "UPDATE `tbl_submenu` SET `access_group`='$chkAccGroup'  WHERE `id`=$chkSubMenu";
  	$booReturn  = $myDBClass->insertData($strSQL);
  	if ($booReturn == false) {
    	$strMessage .= translate('Error while inserting the data to the data base:')."<br>".$myDBClass->strDBError."<br>";
    	$intError = 1;
  	} else {
    	$strMessage .= translate('Data were successfully inserted to the data base!');
    	$myDataClass->writeLog(translate('Access group set for menu item:')." ".$myDBClass->getFieldData("SELECT `item` FROM `tbl_submenu` WHERE `id`=$chkSubMenu"));
  	}
}
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Include content
// ===============
$conttp->setVariable("TITLE",translate('Define Menu Accessrights'));
foreach($arrDescription AS $elem) {
  	$conttp->setVariable($elem['name'],$elem['string']);
}
$conttp->setVariable("LANG_ACCESSDESCRIPTION",translate('In order for a user to get access, he needs to be member of the group selected here.'));
//
// Auswahlfeld einlesen
// ====================
$strSQL 	= "SELECT `tbl_submenu`.`id`,`tbl_submenu`.`item` AS `subitem`,`tbl_mainmenu`.`item` AS `mainitem`,`tbl_submenu`.`access_group`
       		   FROM `tbl_submenu`
       		   LEFT JOIN `tbl_mainmenu` ON `tbl_submenu`.`id_main`=`tbl_mainmenu`.`id`
       		   ORDER BY `tbl_submenu`.`id_main`,`tbl_submenu`.`order_id`";
$booReturn  = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
if ($booReturn == false) {
  	$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
  	$intError = 1;
} else {
  	$conttp->setVariable("SUBMENU_VALUE","0");
  	$conttp->setVariable("SUBMENU_NAME","&nbsp;");
  	$conttp->parse("submenu");
  	foreach($arrDataLines AS $elem) {
    	$conttp->setVariable("SUBMENU_VALUE",$elem['id']);
    	$conttp->setVariable("SUBMENU_NAME",translate($elem['mainitem'])." - ".translate($elem['subitem']));
    	if ($chkSubMenu == $elem['id']) {
      		$conttp->setVariable("SUBMENU_SELECTED","selected");
	  		$intFieldId = $elem['access_group'];
    	}
    	$conttp->parse("submenu");
  	}
  	// Process access group selection field
  	$intReturn = $myVisClass->parseSelectSimple('tbl_group','groupname','acc_group',0,$intFieldId);
}
if ($strMessage != "") {
  	if ($intError == 1) {
    	$conttp->setVariable("LOGDBMESSAGE",$strMessage);
  	} else {
    	$conttp->setVariable("OKDATA",$strMessage);
  	}
}
$conttp->parse("menuaccesssite");
$conttp->show("menuaccesssite");
//
// Process footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>