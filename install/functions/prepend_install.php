<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2017 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Installer preprocessing script
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2017-06-22 09:29:35 +0200 (Thu, 22 Jun 2017) $
// Author    : $LastChangedBy: martin $
// Version   : 3.3.0
// Revision  : $LastChangedRevision: 2 $
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
session_start([ 'name' => 'nagiosql_install']);
//
// Include external function/class files
// =====================================
include("functions/install_class.php");
//
// Initialize class
// ================
$myInstClass = new naginstall;
?>