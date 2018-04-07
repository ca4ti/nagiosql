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
// Component : Admin file deletion
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
$intSub     = 32;
$intMenu    = 2;
$preContent = "admin/delbackup.tpl.htm";
$intModus   = 0;
$strMessage = "";
$errMessage = "";
//
// Process post parameters
// =======================
$chkSelFilename = isset($_POST['selImportFile'])  ? $_POST['selImportFile'] 									: array("");
$chkSearch      = isset($_POST['txtSearch'])      ? htmlspecialchars($_POST['txtSearch'], ENT_QUOTES, 'utf-8')  : "";
$chkStatus      = isset($_POST['hidStatus'])      ? $_POST['hidStatus']+0   									: 0;
//
// Include preprocessing file
// ==========================
$preAccess    = 1;
$preFieldvars = 1;
require("../functions/prepend_adm.php");
$myConfigClass->getConfigData("method",$intMethod);
$myConfigClass->getConfigData("basedir",$strBaseDir);
$myConfigClass->getConfigData("hostconfig",$strHostDir);
$myConfigClass->getConfigData("serviceconfig",$strServiceDir);
//
// 3rd party function to add files of a given directory to an array
// ======================================================
function DirToArray($sPath, $include, $exclude, &$output,&$errMessage) {
	while (substr($sPath,-1) == "/" OR substr($sPath,-1) == "\\") {
		$sPath=substr($sPath, 0, -1);
	}
	$handle = @opendir($sPath);
	if( $handle === false ) {
		$errMessage = translate('Could not open directory')." ".$sPath;
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
// Process form inputs
// ===================
if (($chkSelFilename[0] != "") && ($chkStatus == 1)) {
	$intModus = 1;
	foreach($chkSelFilename AS $elem) {
		$intCheck = $myConfigClass->removeFile(trim($elem));
		$strFile = str_replace($strServiceDir,"",$elem);
		$strFile = str_replace($strHostDir,"",$strFile);
		$strFile = str_replace($strBaseDir,"",$strFile);
    	if ($intCheck == 0) {
      		$myDataClass->writeLog(translate("File deleted").": ".trim($strFile));
      		$myVisClass->processMessage($strFile." ".translate("successfully deleted")."!",$strMessage);
    	} else {
      		$myVisClass->processMessage($strFile." ".translate("could not be deleted (check the permissions)")."!",$strMessage);
      		$strMessage .= $myDataClass->strDBMessage;
    	}
  	}
}
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Include content
// ===============
$conttp->setVariable("TITLE",translate("Delete config files"));
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("LANG_SEARCH_STRING",translate('Filter string'));
$conttp->setVariable("LANG_SEARCH",translate('Search'));
$conttp->setVariable("LANG_DELETE",translate('Delete'));
$conttp->setVariable("LANG_DELETE_SEARCH",translate("Reset filter"));
$conttp->setVariable("DAT_SEARCH",$chkSearch);
$conttp->setVariable("BACKUPFILE",translate("Backup file"));
$conttp->setVariable("MUST_DATA","* ".translate('required'));
$conttp->setVariable("MAKE",translate("Delete"));
$conttp->setVariable("ABORT",translate("Abort"));
$conttp->setVariable("CTRL_INFO",translate("Hold CTRL to select<br>more than one entry"));
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
// Build a local file list
$output = array();
$temp=DirToArray($strBaseDir, "\.cfg", "",$output,$errMessage);
if ($intMethod == 1) {
  	if (is_array($output) && (count($output) != 0)) {
    	foreach ($output AS $elem2) {
      		if (($chkSearch == "") || (substr_count($elem2,$chkSearch) != 0)) {
				$conttp->setVariable("DAT_BACKUPFILE",$elem2);
				$conttp->parse("filelist");
      		}
    	}
  	}
} else if ($intMethod == 2) {
	// Open ftp connection
	$intConnection = 1;
	if (!isset($myConfigClass->resConnectId) || !is_resource($myConfigClass->resConnectId)) {
		$booReturn = $myConfigClass->getFTPConnection();
		if ($booReturn == 1) {
			$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
			$intConnection = 0;
		}
	}
	if ($intConnection == 1) {
		$arrFiles  = array();
		$arrFiles1 = ftp_nlist($myConfigClass->resConnectId,$strBaseDir);
		if (is_array($arrFiles1)) $arrFiles = array_merge($arrFiles,$arrFiles1);
		$arrFiles2 = ftp_nlist($myConfigClass->resConnectId,$strHostDir);
		if (is_array($arrFiles2)) $arrFiles = array_merge($arrFiles,$arrFiles2);
		$arrFiles3 = ftp_nlist($myConfigClass->resConnectId,$strServiceDir);
		if (is_array($arrFiles3)) $arrFiles = array_merge($arrFiles,$arrFiles3);
		if (is_array($arrFiles) && (count($arrFiles) != 0)) {
		  	foreach ($arrFiles AS $elem) {
				if (!substr_count($elem,"cfg")) continue;
				if (($chkSearch == "") || (substr_count($elem,$chkSearch) != 0)) {
			  		$conttp->setVariable("DAT_BACKUPFILE",str_replace("//","/",$elem));
			  		$conttp->parse("filelist");
				}
		  	}
		}
		ftp_close($myConfigClass->resConnectId);
  	}
} else if ($intMethod == 3) {
	// Open ssh connection
	$intConnection = 1;
	if (!isset($myConfigClass->resConnectId) || !is_resource($myConfigClass->resConnectId)) {
		$booReturn = $myConfigClass->getSSHConnection();
		if ($booReturn == 1) {
			$myVisClass->processMessage($myConfigClass->strDBMessage,$strMessage);
			$intConnection = 0;
		}
	}
	if ($intConnection == 1) {
		$arrFiles1 = $myConfigClass->sendSSHCommand('ls '.$strBaseDir);
		if (is_array($arrFiles1) && (count($arrFiles1) != 0)) {
		  	foreach ($arrFiles1 AS $elem) {
				if (!substr_count($elem,"cfg")) continue;
				if (substr_count($elem,"cgi.cfg") != 0) continue;
				if (substr_count($elem,"nagios.cfg") != 0) continue;
				if (($chkSearch == "") || (substr_count($elem,$chkSearch) != 0)) {
			  		$conttp->setVariable("DAT_BACKUPFILE",str_replace("//","/",$elem));
					$conttp->setVariable("DAT_BACKUPFILE_FULL",str_replace("//","/",$strBaseDir."/".$elem));
			  		$conttp->parse("filelist");
				}
		  	}
		}
		$arrFiles2 = $myConfigClass->sendSSHCommand('ls '.$strHostDir);
		if (is_array($arrFiles2) && (count($arrFiles2) != 0)) {
		  	foreach ($arrFiles2 AS $elem) {
				if (!substr_count($elem,"cfg")) continue;
				if (($chkSearch == "") || (substr_count($elem,$chkSearch) != 0)) {
			  		$conttp->setVariable("DAT_BACKUPFILE",str_replace("//","/","hosts/".$elem));
					$conttp->setVariable("DAT_BACKUPFILE_FULL",str_replace("//","/",$strHostDir."/".$elem));
			  		$conttp->parse("filelist");
				}
		  	}
		}
		$arrFiles3 = $myConfigClass->sendSSHCommand('ls '.$strServiceDir);
		if (is_array($arrFiles3) && (count($arrFiles3) != 0)) {
		  	foreach ($arrFiles3 AS $elem) {
				if (!substr_count($elem,"cfg")) continue;
				if (($chkSearch == "") || (substr_count($elem,$chkSearch) != 0)) {
			  		$conttp->setVariable("DAT_BACKUPFILE",str_replace("//","/","services/".$elem));
					$conttp->setVariable("DAT_BACKUPFILE_FULL",str_replace("//","/",$strServiceDir."/".$elem));
			  		$conttp->parse("filelist");
				}
		  	}
		}
  	}
}
if (isset($errMessage)) {
    $conttp->setVariable("ERRORMESSAGE",$errMessage);
} else {
    $conttp->setVariable("ERRORMESSAGE","&nbsp;");
}
if ($intModus == 1) {
  if ($intCheck == 0) $conttp->setVariable("SUCCESS",$strMessage);
  if ($intCheck == 1) $conttp->setVariable("FAILED",$strMessage);
}
$conttp->parse("main");
$conttp->show("main");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>