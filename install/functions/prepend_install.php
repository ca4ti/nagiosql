<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2012 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Installer preprocessing script
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2012-01-04 15:40:03 +0100 (Mi, 04. Jan 2012) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1154 $
//
///////////////////////////////////////////////////////////////////////////////
error_reporting(E_ALL);
//
// Define common variables
// =======================
$strErrorMessage	= "";  // All error messages (red)
$strInfoMessage		= "";  // All information messages (green)
//
// Start PHP session
// =================
session_start('nagiosql_install');
//
// Include external function/class files
// =====================================
include("functions/install_class.php");
//
// Initialize class
// ================
$myInstClass = new naginstall;
?>