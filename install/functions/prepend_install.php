<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2020 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Installer preprocessing script
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
error_reporting(E_ALL);
//
// Define common variables
// =======================
$strErrorMessage = '';  // All error messages (red)
$strInfoMessage  = '';  // All information messages (green)
//
// Start PHP session
// =================
session_start([ 'name' => 'nagiosql_install']);
//
// Include external function/class files
// =====================================
require $preBasePath.'functions/Autoloader.php';
functions\Autoloader::register($preBasePath);
//
// Initialize class
// ================
$myInstClass = new install\functions\NagInstallClass($_SESSION);
