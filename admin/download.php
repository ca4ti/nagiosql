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
// Zweck:	Konfiguration herunterladen
// Datei:	admin/download.php
// Version: 2.0.2 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
//
// Versionskontrolle:
session_cache_limiter('private_no_expire');
//
// Vorgabedatei einbinden
// ======================
$preRights 	= "admin1";
$SETS		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Übergabeparameter überprüfen
// ============================
$chkTable  	= isset($_GET['table'])		? $_GET['table'] 	: "";
$chkConfig 	= isset($_GET['config']) 	? $_GET['config'] 	: "";
$chkLine 	= isset($_GET['line'])		? $_GET['line']		: 0;
//
// Header ausgeben
// ===============
switch($chkTable) {
	case "tbl_timeperiod":			$strFile = "timeperiods.cfg"; break;
	case "tbl_misccommand":			$strFile = "misccommands.cfg"; break;
	case "tbl_checkcommand":		$strFile = "checkcommands.cfg"; break;
	case "tbl_contact":				$strFile = "contacts.cfg"; break;
	case "tbl_contactgroup":		$strFile = "contactgroups.cfg"; break;
	case "tbl_hostgroup":			$strFile = "hostgroups.cfg"; break;
	case "tbl_servicegroup":		$strFile = "servicegroups.cfg"; break;
	case "tbl_servicedependency":	$strFile = "servicedependencies.cfg"; break;
	case "tbl_hostdependency":		$strFile = "hostdependencies.cfg"; break;
	case "tbl_serviceescalation":	$strFile = "serviceescalations.cfg"; break;
	case "tbl_hostescalation":		$strFile = "hostescalations.cfg"; break;
	case "tbl_hostextinfo":			$strFile = "hostextinfo.cfg"; break;
	case "tbl_serviceextinfo":		$strFile = "serviceextinfo.cfg"; break;
	default:						$strFile = $chkConfig.".cfg";
}
if ($strFile == ".cfg") exit;
header("Content-Disposition: attachment; filename=".$strFile);
header("Content-Type: text/plain");
//
// Daten abrufen und ausgeben
// ==========================
if ($chkLine == 0) {
	$myConfigClass->createConfig($chkTable,1);
} else {
	$myConfigClass->createConfigSingle($chkTable,$chkLine,1);
}
$myDataClass->writeLog($LANG['logbook']['download']." ".$strFile);
?>