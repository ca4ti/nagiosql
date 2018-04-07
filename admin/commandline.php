<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project   : NagiosQL
// Component : Admin command line visualization
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2010-10-25 15:45:55 +0200 (Mo, 25 Okt 2010) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.0.4
// Revision  : $LastChangedRevision: 827 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Vorgabedatei einbinden
// ======================
//$preAccess		= 1;
//$intSub			= 2;
$preNoMain 		= 1;

require("../functions/prepend_adm.php");
$strCommandLine = "&nbsp;";
$intCount		= 0;
//
// Datenbank abfragen
// ===================
if (isset($_GET['cname']) && ($_GET['cname'] != "")) {
	$strResult = $myDBClass->getFieldData("SELECT command_line FROM tbl_command WHERE id='".$_GET['cname']."'");
	if ($strResult != false) {
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
	  font-size: 12px;
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