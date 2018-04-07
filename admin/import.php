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
// Component : Admin configuration verification
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-03-13 14:00:26 +0100 (So, 13. Mär 2011) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.1.1
// Revision  : $LastChangedRevision: 1058 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$intMain    = 6;
$intSub     = 16;
$intMenu    = 2;
$preContent = "admin/import.tpl.htm";
$intModus   = 0;
$strMessage = "";
$errMessage = "";
//
// Include preprocessing file
// ==========================
$preAccess    = 1;
$preFieldvars = 1;
require("../functions/prepend_adm.php");
//
// Initialize import class
// =======================
include("../functions/import_class.php");
$myImportClass = new nagimport;
$myImportClass->myDataClass   	=& $myDataClass;
$myImportClass->myDBClass   	=& $myDBClass;
$myImportClass->myConfigClass 	=& $myConfigClass;
//
// Process post parameters
// =======================
$chkSearch      = isset($_POST['txtSearch'])        ? htmlspecialchars($_POST['txtSearch'], ENT_QUOTES, 'utf-8')       : "";
$chkSelFilename = isset($_POST['selImportFile'])    ? $_POST['selImportFile']   : array("");
$chkSelTemplate = isset($_POST['selTemplateFile'])  ? $_POST['selTemplateFile'] : "";
$chkLocalFile   = isset($_POST['datLocalImport'])  	? $_POST['datLocalImport'] 	: "";
$chkOverwrite   = isset($_POST['chbOverwrite'])     ? $_POST['chbOverwrite']    : 0;
$chkStatus      = isset($_POST['hidStatus'])      	? $_POST['hidStatus']+0   	: 0;
//
// Function to add files of a given directory to an array
// ======================================================
function DirToArray($sPath, $include, $exclude, &$output,&$errMessage) {
	while (substr($sPath,-1) == "/" OR substr($sPath,-1) == "\\") {
		$sPath=substr($sPath, 0, -1);
	}
	$handle = @opendir($sPath);
	if( $handle === false ) {
		if ($_SESSION['domain'] != 0) {
			$errMessage .= translate('Could not open directory')." ".$sPath."<br>";
		}
	} else {
		while ($arrDir[] = readdir($handle)) {}
		closedir($handle);
		sort($arrDir);
		foreach($arrDir as $file) {
			if (!preg_match("/^\.{1,2}/", $file) and strlen($file)) {
				if (is_dir($sPath."/".$file)) {
					DirToArray($sPath."/".$file, $include, $exclude, $output, $errMessage);
				} else {
					if (preg_match("/".$include."/",$file) && (($exclude == "") || !preg_match("/".$exclude."/", $file))) {
						if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
							$sPath=str_replace("/", "\\", $sPath);
							$output [] = $sPath."\\".$file;
						} else {
							$output [] = $sPath."/".$file;
						}
					}
				}
			}
		}
	}
}
//
// Process form variables
// ======================
if (isset($_FILES['datLocalImport']) && ($_FILES['datLocalImport']['name'] != "") && ($chkStatus == 1)) {
	// Upload Error
	if ($_FILES['datLocalImport']['error'] !== UPLOAD_ERR_OK) {
		$myImportClass->strMessage == translate('File upload error:')." ".$_FILES['filMedia']['error'];
	} else {
	    $intModus    = 1;
		$strFileName = tempnam(sys_get_temp_dir(), 'nagiosql_local_imp');
        move_uploaded_file($_FILES['datLocalImport']['tmp_name'], $strFileName);
		$intReturn   = $myImportClass->fileImport($strFileName,$chkOverwrite);
		$strMessage  .= $myImportClass->strMessage;
		$strMessage  .= $myImportClass->strDBMessage;
		$myDataClass->writeLog(translate('File imported - File [overwite flag]:')." ".$_FILES['datLocalImport']['name']." [".$chkOverwrite."]");
		if ($intReturn == 1) $errMessage = $myImportClass->strMessage."<br>";
	}
}
if (($chkSelFilename[0] != "") && ($chkStatus == 1)) {
	$myVisClass->strMessage = "";
	foreach($chkSelFilename AS $elem) {
		$intModus    = 1;
		$myImportClass->strMessage = "";
		$intReturn   = $myImportClass->fileImport($elem,$chkOverwrite);
		$strMessage .= $myImportClass->strMessage;
		$strMessage .= $myImportClass->strDBMessage;
		$myDataClass->writeLog(translate('File imported - File [overwite flag]:')." ".$elem." [".$chkOverwrite."]");
		if ($intReturn == 1) $errMessage .=  $myVisClass->strDBMessage."<br>";
	}
}
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Start content
// =============
$conttp->setVariable("TITLE",translate('Configuration import'));
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("LANG_SEARCH_STRING",translate('Filter string'));
$conttp->setVariable("LANG_SEARCH",translate('Search'));
$conttp->setVariable("LANG_DELETE",translate('Delete'));
$conttp->setVariable("LANG_DELETE_SEARCH",translate("Reset filter"));
$conttp->setVariable("DAT_SEARCH",$chkSearch);
$conttp->setVariable("TEMPLATE",translate('Template definition'));
$conttp->setVariable("IMPORTFILE",translate('Import file'));
$conttp->setVariable("LOCAL_FILE",translate('Local import file'));
$conttp->setVariable("OVERWRITE",translate('Overwrite database'));
$conttp->setVariable("MAKE",translate('Import'));
$conttp->setVariable("ABORT",translate('Abort'));
$conttp->setVariable("MUST_DATA","* ".translate('required'));
$conttp->setVariable("CTRL_INFO",translate('Hold CTRL to select<br>more than one'));
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
$conttp->setVariable("DAT_IMPORTFILE_1","");
$conttp->setVariable("IMPORT_INFO",translate("To prevent errors or misconfigurations, you should import your configurations in an useful order. We recommend to do it like this:<br><br><b><i>commands -> timeperiods -> contacttemplates -> contacts -> contactgroups -> hosttemplates -> hosts -> hostgroups -> servicetemplates -> services -> servicegroups</i></b><br><br><span style=\"color:#FF0000\"><b>Check your configuration after import!</b><br>In cause of an error or an uncomplete configuration, re-importing the wrong configuration can solve the problem.</span>"));
$conttp->parse("filelist1");
// Get settings
$myConfigClass->getConfigData("method",$intMethod);
$myConfigClass->getConfigData("basedir",$strBaseDir);
$myConfigClass->getConfigData("hostconfig",$strHostDir);
$myConfigClass->getConfigData("serviceconfig",$strServiceDir);
$myConfigClass->getConfigData("backupdir",$strBackupDir);
$myConfigClass->getConfigData("hostbackup",$strHostBackupDir);
$myConfigClass->getConfigData("servicebackup",$strServiceBackupDir);
$myConfigClass->getConfigData("importdir",$strImportDir);
$myConfigClass->getConfigData("nagiosbasedir",$strNagiosBaseDir);
if ($intMethod == 1) {
	// Building local file list
	$output = array();
	$temp=DirToArray($strBaseDir, "\.cfg", "cgi.cfg|nagios.cfg|nrpe.cfg|nsca.cfg",$output,$errMessage);
	if ($strNagiosBaseDir != $strBaseDir) {
		$temp=DirToArray($strNagiosBaseDir, "\.cfg", "cgi.cfg|nagios.cfg|nrpe.cfg|nsca.cfg",$output,$errMessage);
	}
	$temp=DirToArray($strHostDir, "\.cfg", "",$output,$errMessage);
	$temp=DirToArray($strServiceDir, "\.cfg", "",$output,$errMessage);
	$temp=DirToArray($strHostBackupDir, "\.cfg_", "",$output,$errMessage);
	$temp=DirToArray($strServiceBackupDir, "\.cfg_", "",$output,$errMessage);
	if (($strImportDir != "") && ($strImportDir != $strBaseDir) && ($strImportDir != $strNagiosBaseDir)) {
		$temp=DirToArray($strImportDir, "\.cfg", "",$output,$errMessage);
	}
	$output=array_unique($output);
	if (is_array($output) && (count($output) != 0)) {
		foreach ($output AS $elem) {
			if (($chkSearch == "") || (substr_count($elem,$chkSearch) != 0)) {
				$conttp->setVariable("DAT_IMPORTFILE_2",$elem);
				$conttp->parse("filelist2");
			}
		}
	}
} else if ($intMethod == 2) {
	// Set up basic connection
	if ($myConfigClass->getFTPConnection() == "0") {
		$arrFiles  = array();
		$arrFiles1 = ftp_nlist($myConfigClass->resConnectId,$strBaseDir);
		if (is_array($arrFiles1)) $arrFiles = array_merge($arrFiles,$arrFiles1);
		$arrFiles2 = ftp_nlist($myConfigClass->resConnectId,$strHostDir);
		if (is_array($arrFiles2)) $arrFiles = array_merge($arrFiles,$arrFiles2);
		$arrFiles3 = ftp_nlist($myConfigClass->resConnectId,$strServiceDir);
		if (is_array($arrFiles3)) $arrFiles = array_merge($arrFiles,$arrFiles3);
		$arrFiles4 = ftp_nlist($myConfigClass->resConnectId,$strHostBackupDir);
		if (is_array($arrFiles4)) $arrFiles = array_merge($arrFiles,$arrFiles4);
		$arrFiles5 = ftp_nlist($myConfigClass->resConnectId,$strServiceBackupDir);
		if (is_array($arrFiles5)) $arrFiles = array_merge($arrFiles,$arrFiles5);
		if ($strImportDir != "" ) {
			$arrFiles6 = ftp_nlist($myConfigClass->resConnectId,$strImportDir);
			if (is_array($arrFiles6)) $arrFiles = array_merge($arrFiles,$arrFiles6);
		}
		if (is_array($arrFiles) && (count($arrFiles) != 0)) {
			foreach ($arrFiles AS $elem) {
				if (!substr_count($elem,"cfg")) continue;
				if (substr_count($elem,"resource.cfg")) continue;
				if (substr_count($elem,"nagios.cfg")) continue;
				if (substr_count($elem,"cgi.cfg")) continue;
				if (substr_count($elem,"nrpe.cfg")) continue;
				if (substr_count($elem,"nsca.cfg")) continue;
				if (($chkSearch == "") || (substr_count($elem,$chkSearch) != 0)) {
					$conttp->setVariable("DAT_IMPORTFILE_2",str_replace("//","/",$elem));
					$conttp->parse("filelist2");
				}
			}
		}
		ftp_close($myConfigClass->resConnectId);
	} else {
		$errMessage .= $myConfigClass->strDBMessage;
	}
} else if ($intMethod == 3) {
  	// Set up basic connection
  	if ($myConfigClass->getSSHConnection() == "0") {
		$arrFiles  = array();
		$arrFiles1 = $myConfigClass->sendSSHCommand("ls ".$strBaseDir."*.cfg");
		if ($arrFiles1 && is_array($arrFiles1)) $arrFiles = array_merge($arrFiles,$arrFiles1);
		$arrFiles2 = $myConfigClass->sendSSHCommand("ls ".$strHostDir."*.cfg");
		if (is_array($arrFiles2)) $arrFiles = array_merge($arrFiles,$arrFiles2);
		$arrFiles3 = $myConfigClass->sendSSHCommand("ls ".$strServiceDir."*.cfg");
		if (is_array($arrFiles3)) $arrFiles = array_merge($arrFiles,$arrFiles3);
		$arrFiles4 = $myConfigClass->sendSSHCommand("ls ".$strHostBackupDir."*.cfg");
		if (is_array($arrFiles4)) $arrFiles = array_merge($arrFiles,$arrFiles4);
		$arrFiles5 = $myConfigClass->sendSSHCommand("ls ".$strServiceBackupDir."*.cfg");
		if (is_array($arrFiles5)) $arrFiles = array_merge($arrFiles,$arrFiles5);
		if ($strImportDir != "" ) {
			$arrFiles6 = $myConfigClass->sendSSHCommand("ls ".$strImportDir."*.cfg");
			if (is_array($arrFiles6)) $arrFiles = array_merge($arrFiles,$arrFiles6);
		}
		if (is_array($arrFiles) && (count($arrFiles) != 0)) {
			foreach ($arrFiles AS $elem) {
				if (!substr_count($elem,"cfg")) continue;
				if (substr_count($elem,"resource.cfg")) continue;
				if (substr_count($elem,"nagios.cfg")) continue;
				if (substr_count($elem,"cgi.cfg")) continue;
				if (substr_count($elem,"nrpe.cfg")) continue;
				if (substr_count($elem,"nsca.cfg")) continue;
				if (($chkSearch == "") || (substr_count($elem,$chkSearch) != 0)) {
					$conttp->setVariable("DAT_IMPORTFILE_2",str_replace("//","/",$elem));
					$conttp->parse("filelist2");
				}
			}
		}
	} else {
		$errMessage .= $myConfigClass->strDBMessage;
	}
}
if ($errMessage != "") {
	$conttp->setVariable("ERRORMESSAGE","<p class=\"dbmessage\">".$errMessage."</p>");
}
if ($intModus == 1) $conttp->setVariable("SUCCESS",$strMessage);
$conttp->parse("main");
$conttp->show("main");
//
// Process footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>