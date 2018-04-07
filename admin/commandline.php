<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL 2005
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005 by Martin Willisegger / nagios.ql2005@wizonet.ch
//
// Projekt:	NagiosQL Applikation
// Author :	Martin Willisegger
// Datum:	30.03.2005
// Zweck:	Vollständiges Kommando ausgeben innerhalb des IFRAME
// Datei:	admin/commandline.php
// Version:	1.02
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
	$strResult = $myDBClass->getFieldData("SELECT command_line FROM tbl_checkcommand WHERE command_name='".$_GET['cname']."'");
	if ($strResult != false) {
		$strCommandLine = htmlspecialchars($strResult);
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