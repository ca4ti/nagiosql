<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2007 by Martin Willisegger / nagiosql_v2@wizonet.ch
//
// Projekt:	NagiosQL Applikation
// Author :	Martin Willisegger
// Datum:	25.09.2007
// Zweck:	Auswahl PopUp
// Datei:	admin/dataselect.php
// Version: 2.01.00 (Internal)
// SV:		$Id$
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$preContent 	= "dataselect_js.tpl.htm";
//
// Vorgabedatei einbinden
// ======================
$preAccess		= 1;
$intSub  		= 0;
$SETS 			= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkTable	= isset($_GET['table'])		? 	$_GET['table']	: "";
$chkField	= isset($_GET['field'])		? 	$_GET['field']	: "";
$chkId		= isset($_GET['id'])		? 	$_GET['id']		: 0;
$chkObject	= isset($_GET['object'])	? 	$_GET['object']	: "";
$chkModus	= isset($_GET['mode'])		? 	$_GET['mode']	: "";
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['dataselect']);
$conttp->setVariable("PAGETITLE","NagiosQL - Version ".$setTitleVersion);
$conttp->setVariable("BASE_PATH",$SETS['path']['root']);
$conttp->setVariable("OPENER_FIELD",$chkObject);
$conttp->parse("header");
$conttp->show("header");
//
// Formular
// ========
// Feldbeschriftungen setzen
switch($chkField) {
	case "host":			$strTitle = $LANG['admintable']['hostname'];		break;
	case "hostgroup":		$strTitle = $LANG['admintable']['hostgroups'];		break;
	case "servicegroup":	$strTitle = $LANG['admintable']['servicegroups'];	break;
	case "contactgroup":	$strTitle = $LANG['admintable']['contactgroups'];	break;
	case "parents":			$strTitle = $LANG['admintable']['parents'];			break;
	case "members":			$strTitle = $LANG['admintable']['members'];			break;
	case "commands":		$strTitle = $LANG['admintable']['command'];			break;
	default:				$strTitle = "Not defined!";
}
$conttp->setVariable("SELECTION_TITLE",$strTitle);
foreach($LANG['admintable'] AS $key => $value) {
	$conttp->setVariable("LANG_".strtoupper($key),$value);
}
foreach($LANG['formchecks'] AS $key => $value) {
	$conttp->setVariable(strtoupper($key),$value); 
}
$conttp->setVariable("OPENER_FIELD",$chkObject);
$conttp->setVariable("MODE_VALUE",$chkModus);
$conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->parse("datainsert");
$conttp->show("datainsert");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org'>NagiosQL</a> - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>