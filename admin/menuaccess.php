<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2006 by Martin Willisegger / nagiosql_v2@wizonet.ch
//
// Projekt:	NagiosQL Applikation
// Author :	Martin Willisegger
// Datum:	12.03.2007
// Zweck:	Menu Access festlegen
// Datei:	admin/menuaccess.php
// Version: 2.00.00 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 7;
$intSub  		= 24;
$intMenu        = 2;
$preContent     = "admin_master.tpl.htm";
$strMessage		= "";
//
// Vorgabedatei einbinden
// ======================
$preAccess	= 1;
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkSubMenu		= isset($_POST['selSubMenu'])	 ? $_POST['selSubMenu']+0	: 0;
$chkInsKey1		= isset($_POST['chbKey1']) 		? $_POST['chbKey1'] 		: 0;
$chkInsKey2		= isset($_POST['chbKey2']) 		? $_POST['chbKey2'] 		: 0;
$chkInsKey3		= isset($_POST['chbKey3']) 		? $_POST['chbKey3'] 		: 0;
$chkInsKey4		= isset($_POST['chbKey4']) 		? $_POST['chbKey4'] 		: 0;
$chkInsKey5		= isset($_POST['chbKey5']) 		? $_POST['chbKey5'] 		: 0;
$chkInsKey6		= isset($_POST['chbKey6']) 		? $_POST['chbKey6'] 		: 0;
$chkInsKey7		= isset($_POST['chbKey7']) 		? $_POST['chbKey7'] 		: 0;
$chkInsKey8		= isset($_POST['chbKey8']) 		? $_POST['chbKey8'] 		: 0;
//
// Daten verarbeiten
// =================
$strKeys = $chkInsKey1.$chkInsKey2.$chkInsKey3.$chkInsKey4.$chkInsKey5.$chkInsKey6.$chkInsKey7.$chkInsKey8;
if (isset($_POST['subSave']) && ($chkSubMenu != 0)) {
	$strSQL = "UPDATE tbl_submenu SET access_rights='$strKeys' WHERE id=$chkSubMenu";
	$booReturn  = $myDBClass->insertData($strSQL);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['failed']."<br>".$myDBClass->strDBError."<br>";		
	} else {
		$strMessage .= $LANG['db']['success'];	
		$myDataClass->writeLog($LANG['logbook']['menuaccess']." ".$myDBClass->getFieldData("SELECT item FROM tbl_submenu WHERE id=$chkSubMenu"));
	}
}
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm7']." -> ".$LANG['menu']['item_admsub24']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['menuaccess']);
foreach($LANG['admintable'] AS $key => $value) {
	$conttp->setVariable("LANG_".strtoupper($key),$value);
}
//
// Auswahlfeld einlesen
// ====================
$strSQL = "SELECT tbl_submenu.id,tbl_submenu.item AS subitem,tbl_mainmenu.item AS mainitem,tbl_submenu.access_rights 
		   FROM tbl_submenu 
		   LEFT JOIN tbl_mainmenu ON tbl_submenu.id_main=tbl_mainmenu.id
		   ORDER BY tbl_submenu.id_main,tbl_submenu.order_id";
$booReturn  = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
if ($booReturn == false) {
	$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
} else {
	$conttp->setVariable("SUBMENU_VALUE","0");
	$conttp->setVariable("SUBMENU_NAME","");
	$conttp->parse("submenu");
	foreach($arrDataLines AS $elem) {
		$conttp->setVariable("SUBMENU_VALUE",$elem['id']);
		$conttp->setVariable("SUBMENU_NAME",$LANG['menu'][$elem['mainitem']]." - ".$LANG['menu'][$elem['subitem']]);
		if ($chkSubMenu == $elem['id']) {
			$conttp->setVariable("SUBMENU_SELECTED","selected");
			$arrKeys = $myVisClass->getKeyArray($elem['access_rights']);
			for ($i=1;$i<9;$i++) {
				if ($arrKeys[$i-1] == 1) $conttp->setVariable("KEY".$i."_CHECKED","checked");
			}
		}
		$conttp->parse("submenu");
	}
}
if ($strMessage != "") $conttp->setVariable("LOGDBMESSAGE",$strMessage);
$conttp->parse("menuaccesssite");
$conttp->show("menuaccesssite");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>