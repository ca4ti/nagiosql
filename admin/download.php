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
// Component : Download config file
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-03-13 14:00:26 +0100 (So, 13. Mär 2011) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.1.1
// Revision  : $LastChangedRevision: 1058 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Version control
// ===============
session_cache_limiter('private_no_expire');
//
// Include preprocessing file
// ==========================
$preNoMain    = 1;
$preNoLogin   = 1;
require("../functions/prepend_adm.php");
//
// Process post parameters
// =======================
$chkTable   = isset($_GET['table'])   ? htmlspecialchars($_GET['table'], ENT_QUOTES, 'utf-8')  : "";
$chkConfig  = isset($_GET['config'])  ? htmlspecialchars($_GET['config'], ENT_QUOTES, 'utf-8') : "";
$chkLine    = isset($_GET['line'])    ? htmlspecialchars($_GET['line'], ENT_QUOTES, 'utf-8')   : 0;
//
// Header output
// ===============
switch($chkTable) {
	case "tbl_timeperiod":			$strFile = "timeperiods.cfg"; break;
	case "tbl_command":				$strFile = "commands.cfg"; break;
	case "tbl_contact":				$strFile = "contacts.cfg"; break;
	case "tbl_contacttemplate":		$strFile = "contacttemplates.cfg"; break;
	case "tbl_contactgroup":		$strFile = "contactgroups.cfg"; break;
	case "tbl_hosttemplate":		$strFile = "hosttemplates.cfg"; break;
	case "tbl_servicetemplate":		$strFile = "servicetemplates.cfg"; break;
	case "tbl_hostgroup":     		$strFile = "hostgroups.cfg"; break;
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
// Get data
// ========
if ($chkLine == 0) {
	$myConfigClass->createConfig($chkTable,1);
} else {
	$myConfigClass->createConfigSingle($chkTable,$chkLine,1);
}
$myDataClass->writeLog(translate('Download')." ".$strFile);
?>