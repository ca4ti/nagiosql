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
// Zweck:	Vollständiges Kommando ausgeben innerhalb des IFRAME
// Datei:	admin/commandline.php
// Version: 2.0.2 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
//
// Vorgabedatei einbinden
// ======================
$SETS = parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
$strCommandLine = "&nbsp;";
//
// Datenbank abfragen
// ===================
if (isset($_GET['cname']) && ($_GET['cname'] != "")) {
	$strResult = $myDBClass->getFieldData("SELECT command_line FROM tbl_checkcommand WHERE id='".$_GET['cname']."'");
	if ($strResult != false) {
		$strCommandLine = htmlspecialchars(stripslashes($strResult));
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <style type="text/css">
    <!--
    body {
	  font-family: Verdana, Arial, Helvetica, sans-serif;
	  font-size: 12px;
	  color: #000000;
	  background-color: #FFFFFF;
	  margin: 2px;
	  border: none;
    }
    -->
    </style>
  </head>
<body>
  <?php echo $strCommandLine; ?>
</body>
</html>