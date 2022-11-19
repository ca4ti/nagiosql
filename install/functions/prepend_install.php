<?php
/* ----------------------------------------------------------------------------
 NagiosQL
-------------------------------------------------------------------------------
 (c) 2005-2022 by Martin Willisegger

 Project   : NagiosQL
 Component : Installer preprocessing script
 Website   : https://sourceforge.net/projects/nagiosql/
 Version   : 3.5.0
 GIT Repo  : https://gitlab.com/wizonet/NagiosQL
-----------------------------------------------------------------------------*/
error_reporting(E_ALL);
/**
 * Class and variable includes
 * @var string $preBasePath from index.php
 */
/*
Define common variables
*/
$strErrorMessage = '';  /* All error messages (red) */
$strInfoMessage  = '';  /* All information messages (green) */
/*
// Start PHP session
*/
session_start([ 'name' => 'nagiosql_install']);
/*
Include external function/class files
*/
require $preBasePath.'functions/Autoloader.php';
functions\Autoloader::register($preBasePath);
/*
Initialize class
*/
$myInstClass = new install\functions\NagInstallClass($_SESSION);