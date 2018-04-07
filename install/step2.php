<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project  : NagiosQL
// Component: Installer
// Website  : http://www.nagiosql.org
// Date     : $LastChangedDate: 2011-03-13 14:00:26 +0100 (So, 13. Mär 2011) $
// Author   : $LastChangedBy: rouven $
// Version  : 3.1.1
// Revision : $LastChangedRevision: 1058 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Security
// =========
if(preg_match('#' . basename(__FILE__) . '#', filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING))) {
  die("You can't access this file directly!");
}
if (!isset($_SESSION['SETS']['install']['step'])) {
  header("Location: index.php");
} else {
  $_SESSION['SETS']['install']['step'] = 2;
}
if ((isset($_GET['SETS']) AND htmlspecialchars($_GET['SETS'], ENT_QUOTES, 'utf-8') != "") OR (isset($_GET['SETS']) AND htmlspecialchars($_POST['SETS'], ENT_QUOTES, 'utf-8') != "")) {
  $SETS = "";
}
$intError = 0;
$output="";
// Default: Remove existing database
if (isset($_SESSION['SETS']['install']['db_drop']) && ($_SESSION['SETS']['install']['db_drop'] == 1)) {
  $valDrop = "checked";
} else {
  $valDrop = "";
}
// Default: Nagios sample data
if (isset($_SESSION['SETS']['install']['sampleData']) && ($_SESSION['SETS']['install']['sampleData'] == 1)) {
  $valSample = "checked";
} else {
  $valSample = "";
}
?>
<!-- DIV Container for installer Menu -->
<div id="installmenu">
  <div id="installmenu_content">
    <?php include "status.php"; ?>
  </div>
</div>
<!-- DIV Container for installer content -->
<div id="installmain">
  <div id="installmain_content">
    <h1>NagiosQL <?php echo translate($_SESSION['SETS']['install']['InstallType']). ": ". translate("Setup"); ?></h1>
    <form action="" method="post" class="cmxform" id="setup" name="setup">
      <?php
		echo "<p class='hint'>".translate("Please complete the form below. Mandatory fields marked <em>*</em>")."</p>\n";
		if ($_SESSION['SETS']['install']['InstallType'] == "Update") echo "<p><b>".translate("Please backup your database before proceeding!")."</b></p>\n";
		echo "<fieldset>\n";
		echo "<legend>".translate("Database Configuration")."</legend>\n";
		echo "<ol>\n";
		echo "<li><label>".translate("MySQL Server")." <em>*</em></label> <input name='txtDBserver' id='txtDBserver' class='required' value='".htmlspecialchars($_SESSION['SETS']['db']['server'], ENT_QUOTES, 'utf-8')."' /></li>\n";
		echo "<li><label>".translate("MySQL Server Port")." <em>*</em></label> <input name='txtDBport' id='txtDBport' class='required validate-number' value='".htmlspecialchars($_SESSION['SETS']['db']['port'], ENT_QUOTES, 'utf-8')."' /></li>\n";
		echo "<li><label>".translate("Database name")." <em>*</em></label> <input name='txtDBname' id='txtDBname' class='required' value='".htmlspecialchars($_SESSION['SETS']['db']['database'], ENT_QUOTES, 'utf-8')."' /></li>\n";
		if ($_SESSION['SETS']['install']['InstallType'] == "Installation") {
			echo "<li><label>".translate("NagiosQL DB User")." <em>*</em></label> <input name='txtDBuser' id='txtDBuser' class='required' value='".htmlspecialchars($_SESSION['SETS']['db']['username'], ENT_QUOTES, 'utf-8')."' /></li>\n";
			echo "<li><label>".translate("NagiosQL DB Password")." <em>*</em></label> <input type='password' name='txtDBpass' id='txtDBpass' class='required' value='".htmlspecialchars($_SESSION['SETS']['db']['password'], ENT_QUOTES, 'utf-8')."' /></li>\n";
		} else {
			$output .= "<input name='txtDBuser' type='hidden' value='".htmlspecialchars($_SESSION['SETS']['db']['username'], ENT_QUOTES, 'utf-8')."' />\n";
			$output .= "<input name='txtDBpass' type='hidden' value='".htmlspecialchars($_SESSION['SETS']['db']['password'], ENT_QUOTES, 'utf-8')."' />\n";
		}
		echo "<li><label>".translate("Administrative MySQL User")." <em>*</em></label> <input id='txtDBprivUser' class='required' name='txtDBprivUser' value='root' size='15' /></li>\n";
		echo "<li><label>".translate("Administrative MySQL Password")."</label> <input type='password' name='txtDBprivPass' id='txtDBprivPass' size='15' /></li>\n";
		if ($_SESSION['SETS']['install']['InstallType'] == "Installation") { 
			echo "<li><label>".translate("Drop database if already exists?")."</label> <input type='checkbox' name='chkDrop' value='1' ".$valDrop." /></li>\n";
		} else {
			$output .= "<input type='hidden' name='chkDrop' id='chkDrop' value='1' />\n";
		}
		echo "</ol>\n";
		echo "</fieldset>\n";
		if ($output != "") echo $output;
		if ($_SESSION['SETS']['install']['InstallType'] == "Installation") {
			// New Installation
			echo "<fieldset>\n";
			echo "<legend>".translate("NagiosQL User Setup")."</legend>\n";
			echo "<ol>\n";
			echo "<li><label>".translate("Initial NagiosQL User")." <em>*</em></label> <input type='text' name='txtQLuser' id='txtQLuser' class='required' value='admin' size='15' /></li>\n";
			echo "<li><label>".translate("Initial NagiosQL Password")." <em>*</em></label> <input type='password' class='validate-equalto required' name='txtQLpass' id='txtQLpass' size='15' /></li>\n";
			echo "<li><label>".translate("Please repeat the password")." <em>*</em></label> <input type='password' class='validate-equalto required' name='txtQLpassrepeat' id='txtQLpassrepeat' size='15' /></li>\n";
			echo "</ol>\n";
			echo "</fieldset>\n";
			echo "<fieldset>\n";	
			echo "<legend>".translate("Nagios Configuration")."</legend>\n";
			echo "<ol>\n";			
			echo "<li><label>".translate("Import Nagios sample config?")." </label> <input type='checkbox' name='chkSample' id='chkSample' value='1' ".$valSample." /></li>\n";
			echo "</ol>\n";
			echo "</fieldset>\n";
		}
    echo "<div id=\"install-next\">\n";
    echo "<input type='hidden' name='step' value='3' />\n";
    echo "<input type='hidden' name='PHPSESSID' value='".session_id()."' />";
    echo "<input type='image' src='images/next.png' value='Submit' alt='Submit'><br>".translate("Next")."\n";
    echo "</div>\n";
    echo "</form>\n";
    ?>
    <script type="text/javascript">
      new Validation('setup',{stopOnFirst:true});
    </script>

  </div>
</div>
<div id="ie_clearing"> </div>
