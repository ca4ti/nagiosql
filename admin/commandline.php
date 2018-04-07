<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2012 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Command line visualization
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2012-02-21 14:10:41 +0100 (Tue, 21 Feb 2012) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1229 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$preNoMain 		= 1;
//
// Include preprocessing file
// ==========================
require("../functions/prepend_adm.php");
$strCommandLine = "&nbsp;";
$intCount		= 0;
//
// Get database values
// ===================
if (isset($_GET['cname']) && ($_GET['cname'] != "")) {
	$strResult = $myDBClass->getFieldData("SELECT command_line FROM tbl_command WHERE id='".filter_var($_GET['cname'], FILTER_SANITIZE_NUMBER_INT)."'");
	if (($strResult != false) && ($strResult != "")) {
		$strCommandLine = $strResult;
		$intCount = substr_count($strCommandLine,"ARG");
		if (substr_count($strCommandLine,"ARG8") != 0) {
			$intCount = 8;
		} else if (substr_count($strCommandLine,"ARG7") != 0) {
			$intCount = 7;
		} else if (substr_count($strCommandLine,"ARG6") != 0) {
			$intCount = 6;
		} else if (substr_count($strCommandLine,"ARG5") != 0) {
			$intCount = 5;
		} else if (substr_count($strCommandLine,"ARG4") != 0) {
			$intCount = 4;
		} else if (substr_count($strCommandLine,"ARG3") != 0) {
			$intCount = 3;
		} else if (substr_count($strCommandLine,"ARG2") != 0) {
			$intCount = 2;
		} else if (substr_count($strCommandLine,"ARG1") != 0) {
			$intCount = 1;
		} else {
			$intCount = 0;
		}
		
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  	<title>Commandline</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
    <!--
    body {
	  font-family: Verdana, Arial, Helvetica, sans-serif;
	  font-size: 10px;
	  color: #000000;
	  background-color: #EDF5FF;
	  margin: 3px;
	  border: none;
    }
    -->
    </style>
  </head>
<body>
  <?php echo $strCommandLine; ?>
  <script type="text/javascript" language="javascript">
  <!--
     parent.argcount = <?php echo $intCount; ?>;
  //-->
  </script>
</body>
</html>