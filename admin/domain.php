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
// Component : Admin domain administration
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
$intMain    	= 7;
$intSub     	= 25;
$intMenu    	= 2;
$preContent 	= "admin/domain.tpl.htm";
$intCount   	= 0;
$strErrMessage 	= "";
$intDomainError = "";
$intIsError		= 0;
//
// Include preprocessing file
// ==========================
$preAccess    	= 1;
$preFieldvars 	= 1;
require("../functions/prepend_adm.php");
//
// Process post parameters
// =======================
$chkInsDomain           = isset($_POST['tfDomain'])           ? $_POST['tfDomain']                                  			: "";
$chkInsAlias            = isset($_POST['tfAlias'])            ? htmlspecialchars($_POST['tfAlias'], ENT_QUOTES, 'utf-8')        : "";
$chkHidDomain           = isset($_POST['hidDomain'])          ? $_POST['hidDomain']                                 			: "";
$chkInsServer           = isset($_POST['tfServername'])       ? htmlspecialchars($_POST['tfServername'], ENT_QUOTES, 'utf-8')   : "";
$chkInsMethod           = isset($_POST['selMethod'])          ? $_POST['selMethod']                                 			: 0;
$chkInsUser             = isset($_POST['tfUsername'])         ? htmlspecialchars($_POST['tfUsername'], ENT_QUOTES, 'utf-8')     : "";
$chkInsPasswd           = isset($_POST['tfPassword'])         ? $_POST['tfPassword']                                			: "";
$chkInsSSHKey           = isset($_POST['tfSSHKey'])           ? htmlspecialchars($_POST['tfSSHKey'], ENT_QUOTES, 'utf-8')       : "";
$chkInsBasedir          = isset($_POST['tfBasedir'])          ? $myVisClass->addSlash(htmlspecialchars($_POST['tfBasedir'], ENT_QUOTES, 'utf-8'))          : "";
$chkInsHostconfig       = isset($_POST['tfHostconfigdir'])    ? $myVisClass->addSlash(htmlspecialchars($_POST['tfHostconfigdir'], ENT_QUOTES, 'utf-8'))    : "";
$chkInsServiceconfig    = isset($_POST['tfServiceconfigdir']) ? $myVisClass->addSlash(htmlspecialchars($_POST['tfServiceconfigdir'], ENT_QUOTES, 'utf-8')) : "";
$chkInsBackupdir        = isset($_POST['tfBackupdir'])        ? $myVisClass->addSlash(htmlspecialchars($_POST['tfBackupdir'], ENT_QUOTES, 'utf-8'))        : "";
$chkInsHostbackup       = isset($_POST['tfHostbackupdir'])    ? $myVisClass->addSlash(htmlspecialchars($_POST['tfHostbackupdir'], ENT_QUOTES, 'utf-8'))    : "";
$chkInsServicebackup    = isset($_POST['tfServicebackupdir']) ? $myVisClass->addSlash(htmlspecialchars($_POST['tfServicebackupdir'], ENT_QUOTES, 'utf-8')) : "";
$chkInsNagiosBaseDir    = isset($_POST['tfNagiosBaseDir'])    ? $myVisClass->addSlash(htmlspecialchars($_POST['tfNagiosBaseDir'], ENT_QUOTES, 'utf-8'))    : "";
$chkInsImportDir        = isset($_POST['tfImportdir'])        ? $myVisClass->addSlash(htmlspecialchars($_POST['tfImportdir'], ENT_QUOTES, 'utf-8'))        : "";
$chkInsPictureDir       = isset($_POST['tfPicturedir'])       ? $myVisClass->addSlash(htmlspecialchars($_POST['tfPicturedir'], ENT_QUOTES, 'utf-8'))       : "";
$chkInsCommandfile      = isset($_POST['tfCommandfile'])      ? htmlspecialchars($_POST['tfCommandfile'], ENT_QUOTES, 'utf-8')                             : "";
$chkInsBinary           = isset($_POST['tfBinary'])           ? htmlspecialchars($_POST['tfBinary'], ENT_QUOTES, 'utf-8')                                  : "";
$chkInsPidfile          = isset($_POST['tfPidfile'])          ? htmlspecialchars($_POST['tfPidfile'], ENT_QUOTES, 'utf-8')                                 : "";
$chkInsConffile         = isset($_POST['tfConffile'])         ? htmlspecialchars($_POST['tfConffile'], ENT_QUOTES, 'utf-8')                                : "";
$chkInsVersion          = isset($_POST['selVersion'])         ? $_POST['selVersion']                                			: 1;
$chkAccGroup  			= isset($_POST['selAccessGroup']) 	  ? $_POST['selAccessGroup']+0 										: 0;
$chkEnableCommon		= isset($_POST['selEnableCommon']) 	  ? $_POST['selEnableCommon']+0 									: 0;
$chkUTF8decode			= isset($_POST['selUTF8decode']) 	  ? $_POST['selUTF8decode']+0 										: 1;
//
// Quote special characters
// ==========================
if (get_magic_quotes_gpc() == 0) {
	$chkInsDomain         	= addslashes($chkInsDomain);
	$chkInsAlias          	= addslashes($chkInsAlias);
	$chkHidDomain         	= addslashes($chkHidDomain);
	$chkInsServer         	= addslashes($chkInsServer);
	$chkInsUser           	= addslashes($chkInsUser);
	$chkInsPasswd         	= addslashes($chkInsPasswd);
	$chkInsSSHKey         	= addslashes($chkInsSSHKey);
	$chkInsBasedir        	= addslashes($chkInsBasedir);
	$chkInsHostconfig     	= addslashes($chkInsHostconfig);
	$chkInsServiceconfig  	= addslashes($chkInsServiceconfig);
	$chkInsBackupdir      	= addslashes($chkInsBackupdir);
	$chkInsHostbackup     	= addslashes($chkInsHostbackup);
	$chkInsServicebackup  	= addslashes($chkInsServicebackup);
	$chkInsNagiosBaseDir  	= addslashes($chkInsNagiosBaseDir);
	$chkInsImportDir      	= addslashes($chkInsImportDir);
	$chkInsPictureDir      	= addslashes($chkInsPictureDir);
	$chkInsCommandfile    	= addslashes($chkInsCommandfile);
	$chkInsBinary         	= addslashes($chkInsBinary);
	$chkInsPidfile        	= addslashes($chkInsPidfile);
	$chkInsConffile			= addslashes($chkInsConffile);
}
//
// Check if the permissions and other parameters
// =============================================
if (($chkModus == "modify" || $chkModus == "insert")) {
	if ($chkDataId != 0) {
		// Base directory
		if (($chkInsMethod  == 1) && isset($chkInsBasedir) && !$myConfigClass->dir_is_writable($chkInsBasedir)) {
			$intDomainError .= $chkInsBasedir." ".translate("is not writeable")."<br>";
			$intIsError		 = 1;
		}
		// Host directory
		if (($chkInsMethod  == 1) && isset($chkInsHostconfig) 	&& !$myConfigClass->dir_is_writable($chkInsHostconfig)) {
			$intDomainError .= $chkInsHostconfig." ".translate("is not writeable")."<br>";
			$intIsError		 = 1;
		}
		// Service directory
		if (($chkInsMethod  == 1) && isset($chkInsServiceconfig) && !$myConfigClass->dir_is_writable($chkInsServiceconfig)) {
			$intDomainError .= $chkInsServiceconfig." ".translate("is not writeable")."<br>";
			$intIsError		 = 1;
		}
		// Backup base directory
		if (($chkInsMethod  == 1) && isset($chkInsBackupdir) 	&& !$myConfigClass->dir_is_writable($chkInsBackupdir)) {
			$intDomainError .= $chkInsBackupdir." ".translate("is not writeable")."<br>";
			$intIsError		 = 1;
		}
		// Backup host directory
		if (($chkInsMethod  == 1) && isset($chkInsHostbackup) 	&& !$myConfigClass->dir_is_writable($chkInsHostbackup)) {
			$intDomainError .= $chkInsHostbackup." ".translate("is not writeable")."<br>";
			$intIsError		 = 1;
		}
		// Backup service directory
		if (($chkInsMethod  == 1) && isset($chkInsServicebackup) && !$myConfigClass->dir_is_writable($chkInsServicebackup)) {
			$intDomainError .= $chkInsServicebackup." ".translate("is not writeable")."<br>";
			$intIsError		 = 1;
		}
		// Nagios base configuration files
		if (($chkInsMethod  == 1) && isset($chkInsNagiosBaseDir)) {
			if (!is_writable($chkInsConffile)) {
				if ($chkInsConffile == "") $chkInsConffile = translate("Nagios config file");
				$intDomainError .= $chkInsConffile." ".translate("is not writeable")."<br>";
				$intIsError		 = 1;
			}
			if (!is_writable($chkInsNagiosBaseDir."cgi.cfg")) {
				$intDomainError .= $chkInsNagiosBaseDir."cgi.cfg ".translate("is not writeable")."<br>";
				$intIsError		 = 1;
			}
		}
		// Check SSH Method
		if (($chkInsMethod  == 3) && !function_exists('ssh2_connect')) {
			$intDomainError .= translate('SSH module not loaded!');
			$intIsError		 = 1;
		}
		// Check FTP Method
		if (($chkInsMethod  == 2) && !function_exists('ftp_connect')) {
			$intDomainError .= translate('FTP module not loaded!');
			$intIsError		 = 1;
		}
		if ($intIsError == 1) {
			$intError = 1;
			$chkModus = "add";
			$chkSelModify 	= "errormodify";
			$strErrMessage .= "<h2>".translate("Warning, at least one error occured, please check!")."</h2>";
			$strErrMessage .= $intDomainError;
		}
	}
}
// 
// Add or modify data
// ==================
if (($intError != 1) &&(($chkModus == "insert") || ($chkModus == "modify"))) {
	$strSQLx = "`tbl_domain` SET `domain`='$chkInsDomain', `alias`='$chkInsAlias', `server`='$chkInsServer', `method`='$chkInsMethod',
        		`user`='$chkInsUser', `password`='$chkInsPasswd', `ssh_key_path`='$chkInsSSHKey', `basedir`='$chkInsBasedir', 
				`hostconfig`='$chkInsHostconfig', `serviceconfig`='$chkInsServiceconfig', `backupdir`='$chkInsBackupdir', 
				`hostbackup`='$chkInsHostbackup', `servicebackup`='$chkInsServicebackup', `nagiosbasedir`='$chkInsNagiosBaseDir', 
				`importdir`='$chkInsImportDir', `picturedir`='$chkInsPictureDir', `commandfile`='$chkInsCommandfile', 
				`binaryfile`='$chkInsBinary', `pidfile`='$chkInsPidfile', `conffile`='$chkInsConffile', `version`=$chkInsVersion, 
				`access_group`=$chkAccGroup, `enable_common`=$chkEnableCommon, `utf8_decode`=$chkUTF8decode, `active`='$chkActive',
				`last_modified`=NOW()"; 
  	if ($chkModus == "insert") {
    	$strSQL 	= "INSERT INTO ".$strSQLx;
		$chkDataId  = -1;
  	} else {
    	$strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
  	}
  	if (($chkInsDomain != "") && ($chkInsAlias != "") && (($chkInsServer != "") || ($chkDataId == 0))) {
    	$intReturn = $myDataClass->dataInsert($strSQL,$intInsertId);
		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
    	if ($intReturn == 1)      $strMessage = $myDataClass->strDBMessage;
    	if ($chkModus  == "insert")   $myDataClass->writeLog(translate('New Domain inserted:')." ".$chkInsDomain);
    	if ($chkModus  == "modify")   $myDataClass->writeLog(translate('Domain modified:')." ".$chkInsDomain);
  	} else {
    	$strMessage .= translate('Database entry failed! Not all necessary data filled in!');
  	}
  	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Delete selected datasets
	if ($chkHidDomain != "localhost") {
		$intReturn = $myDataClass->dataDeleteEasy("tbl_domain","id",$chkListId);
		$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
	} else {
		$myDataClass->strDBMessage = translate("Localhost can't be deleted");
	}
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Copy selected datasets
  	$intReturn = $myDataClass->dataCopyEasy("tbl_domain","domain",$chkListId);
	$myVisClass->processMessage($myDataClass->strDBMessage,$strMessage);
  	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Open a dataset to modify
  	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM `tbl_domain` WHERE `id`=".$chkListId,$arrModifyData);
	$myVisClass->processMessage($myDBClass->strDBError,$strMessage);
  	if ($booReturn == false) {
		$myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError,$strMessage);
  		$chkModus    = "add";
	} else {
		// Check access permission
		$intAccess = $myVisClass->checkAccGroup($_SESSION['userid'],$arrModifyData['access_group']);  
		if ($intAccess == 1) {
	  		$myVisClass->processMessage(translate('No permission to open configuration!'),$strMessage);
	  		$arrModifyData  = "";
	 		$chkModus       = "display";
		} else {
	  		$chkModus 	  = "add";	
		}
	}
}
// Get status messages from database
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $strMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$strMessage."</span>";
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Include content
// ===============
$conttp->setVariable("TITLE",translate('Domain administration'));
if (isset($strErrMessage)) {$conttp->setVariable("ERRMESSAGE",$strErrMessage."<br>");} else {$conttp-->setVariable("ERRMESSAGE","&nbsp;");}
$conttp->parse("header");
$conttp->show("header");
//
// Single view
// ===========
if ($chkModus == "add") {
  	// Process acces group selection field
  	if (isset($arrModifyData['access_group'])) {$intFieldId = $arrModifyData['access_group'];} else {$intFieldId = 0;}
  	$intReturn = $myVisClass->parseSelectSimple('tbl_group','groupname','acc_group',0,$intFieldId);
	// Process template text raplacements
  	foreach($arrDescription AS $elem) {
    	$conttp->setVariable($elem['name'],$elem['string']);
  	}
	$conttp->setVariable("LANG_ACCESSDESCRIPTION",translate('In order for a user to get access, he needs to be member of the group selected here.'));
	$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
	$conttp->setVariable("LIMIT",$chkLimit);
	$conttp->setVariable("ACT_CHECKED","checked");
	$conttp->setVariable("MODUS","insert");
	$conttp->setVariable("CLASS_NAME_1","elementHide");
	$conttp->setVariable("CLASS_NAME_2","elementHide");
	$conttp->setVariable("FILL_ALLFIELDS",translate('Please fill in all fields marked with an *'));
	$conttp->setVariable("FILL_ILLEGALCHARS",translate('The following field contains not permitted characters:'));
	$conttp->setVariable("LOCKCLASS","inpmust");
	$conttp->setVariable("ENABLE",translate('Enable'));
	$conttp->setVariable("DISABLE",translate('Disable'));
  	// Insert data from database in "modify" mode
	if (isset($arrModifyData) && ($chkSelModify == "modify") && (is_array($arrModifyData))) {
		foreach($arrModifyData AS $key => $value) {
      		if (($key == "active") || ($key == "last_modified")) continue;
      		$conttp->setVariable("DAT_".strtoupper($key),htmlspecialchars($value,ENT_COMPAT,'UTF-8'));
    	}
    	// Connection method
		if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
    	if ($arrModifyData['method'] == 1) $conttp->setVariable("FILE_SELECTED","selected");
    	if ($arrModifyData['method'] == 2) {
      		$conttp->setVariable("FTP_SELECTED","selected");
      		$conttp->setVariable("CLASS_NAME_1","elementShow");
    	}
    	if ($arrModifyData['method'] == 3) {
      		$conttp->setVariable("SFTP_SELECTED","selected");
      		$conttp->setVariable("CLASS_NAME_1","elementShow");
	  		$conttp->setVariable("CLASS_NAME_2","elementShow");
    	}
    	// Nagios version
		if ($arrModifyData['version'] == 1) $conttp->setVariable("VER_SELECTED_1","selected");
		if ($arrModifyData['version'] == 2) $conttp->setVariable("VER_SELECTED_2","selected");
		if ($arrModifyData['version'] == 3) $conttp->setVariable("VER_SELECTED_3","selected");
		// Enable common domain
		if ($arrModifyData['enable_common'] == 0) $conttp->setVariable("ENA_COMMON_SELECTED_0","selected");
		if ($arrModifyData['enable_common'] == 1) $conttp->setVariable("ENA_COMMON_SELECTED_1","selected");
		// Enable common domain
		if ($arrModifyData['utf8_decode'] == 0) $conttp->setVariable("UTF8_DECODE_SELECTED_0","selected");
		if ($arrModifyData['utf8_decode'] == 1) $conttp->setVariable("UTF8_DECODE_SELECTED_1","selected");
		// Domain localhost cant' be renamed
    	if ($arrModifyData['domain'] == "localhost") {
      		$conttp->setVariable("DOMAIN_DISABLE","readonly");
      		$conttp->setVariable("LOCKCLASS","inputlock");
		} else if ($arrModifyData['domain'] == "common") {
      		$conttp->setVariable("DOMAIN_DISABLE","readonly");
			$conttp->setVariable("COMMON_INVISIBLE","class=\"elementHide\"");
      		$conttp->setVariable("LOCKCLASS","inputlock");
    	} else {
      		$conttp->setVariable("LOCKCLASS","inpmust");
    	}
    	$conttp->setVariable("MODUS","modify");
  	}
  	if ($chkSelModify == "errormodify") {
    	$conttp->setVariable("DAT_DOMAIN",$chkInsDomain);
    	// Domain localhost cant' be renamed
    	if ($chkInsDomain == "localhost") {
      		$conttp->setVariable("DOMAIN_DISABLE","readonly");
      		$conttp->setVariable("LOCKCLASS","inputlock");
		} else if ($chkInsDomain == "common") {
      		$conttp->setVariable("DOMAIN_DISABLE","readonly");
			$conttp->setVariable("COMMON_INVISIBLE","class=\"elementHide\"");
      		$conttp->setVariable("LOCKCLASS","inputlock");
    	} else {
      		$conttp->setVariable("LOCKCLASS","inpmust");
    	}
    	$conttp->setVariable("DAT_ALIAS",$chkInsAlias);
    	$conttp->setVariable("DAT_SERVER",$chkInsServer);
    	// Connection method
    	if ($chkInsMethod == 1) $conttp->setVariable("FILE_SELECTED","selected");
    	if ($chkInsMethod == 2) {
      		$conttp->setVariable("FTP_SELECTED","selected");
      		$conttp->setVariable("CLASS_NAME_1","elementShow");
    	}
    	if ($chkInsMethod == 3) {
      		$conttp->setVariable("SFTP_SELECTED","selected");
      		$conttp->setVariable("CLASS_NAME_1","elementShow");
	  		$conttp->setVariable("CLASS_NAME_2","elementShow");
    	}
		$conttp->setVariable("DAT_USER",$chkInsUser);
		$conttp->setVariable("DAT_SSH_KEY_PATH",$chkInsSSHKey);
		$conttp->setVariable("DAT_BASEDIR",$chkInsBasedir);
		$conttp->setVariable("DAT_HOSTCONFIG",$chkInsHostconfig);
		$conttp->setVariable("DAT_SERVICECONFIG",$chkInsServiceconfig);
		$conttp->setVariable("DAT_BACKUPDIR",$chkInsBackupdir);
		$conttp->setVariable("DAT_HOSTBACKUP",$chkInsHostbackup);
		$conttp->setVariable("DAT_SERVICEBACKUP",$chkInsServicebackup);
		$conttp->setVariable("DAT_NAGIOSBASEDIR",$chkInsNagiosBaseDir);
		$conttp->setVariable("DAT_IMPORTDIR",$chkInsImportDir);
		$conttp->setVariable("DAT_COMMANDFILE",$chkInsCommandfile);
		$conttp->setVariable("DAT_BINARYFILE",$chkInsBinary);
		$conttp->setVariable("DAT_PIDFILE",$chkInsPidfile);
		$conttp->setVariable("DAT_CONFFILE",$chkInsConffile);
		$conttp->setVariable("DAT_PICTUREDIR",$chkInsPictureDir);
    	// NagiosQL version
		if ($chkInsVersion == 1) $conttp->setVariable("VER_SELECTED_1","selected");
		if ($chkInsVersion == 2) $conttp->setVariable("VER_SELECTED_2","selected");
		if ($chkInsVersion == 3) $conttp->setVariable("VER_SELECTED_3","selected");
		// Hidden variables
		$conttp->setVariable("MODUS",$_POST['modus']);
		$conttp->setVariable("DAT_ID",$_POST['hidId']);
		$conttp->setVariable("LIMIT",$_POST['hidLimit']);
		// Active
		if (isset ($_POST['chbActive'])) {
			$conttp->setVariable("ACT_CHECKED","checked");
		} else {
			$conttp->setVariable("ACT_CHECKED","");
		}
  	}
	$conttp->parse("datainsert");
	$conttp->show("datainsert");
}
//
// List view
// ==========
if ($chkModus == "display") {
  	// Process template text raplacements
  	foreach($arrDescription AS $elem) {
    	$mastertp->setVariable($elem['name'],$elem['string']);
  	}
	$mastertp->setVariable("FIELD_1",translate('Domain'));
	$mastertp->setVariable("FIELD_2",translate('Description'));
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("ACTION_MODIFY",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
  	// Get Group id's with READ
  	$strAccess = $myVisClass->getAccGroupRead($_SESSION['userid']);
  	// Count datasets
  	$strSQL    = "SELECT count(*) AS `number` FROM `tbl_domain` WHERE `access_group` IN ($strAccess)";
  	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
  	if ($booReturn == false) {
		$myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError,$strMessage);
  	} else {
    	$intCount = (int)$arrDataLinesCount['number'];
  	}
  	// Get datasets
  	$strSQL    = "SELECT `id`, `domain`, `alias`, `active`, `nodelete` FROM `tbl_domain` WHERE `access_group` IN ($strAccess)
				  ORDER BY `domain` LIMIT $chkLimit,".$SETS['common']['pagelines'];
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	$mastertp->setVariable("CELLCLASS_L","tdlb");
	$mastertp->setVariable("CELLCLASS_M","tdmb");	
	$mastertp->setVariable("DISABLED","disabled");
	$mastertp->setVariable("DATA_FIELD_1",translate('No data'));
	$mastertp->setVariable("DATA_FIELD_2","&nbsp;");
	$mastertp->setVariable("DATA_ACTIVE","&nbsp;");
	$mastertp->setVariable("CHB_CLASS","checkbox");
	$mastertp->setVariable("PICTURE_CLASS","elementHide");
  	if ($booReturn == false) {
    	$myVisClass->processMessage(translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError,$strMessage);
  	} else if ($intDataCount != 0) {
    	for ($i=0;$i<$intDataCount;$i++) {
      		// Line colours
      		$strClassL = "tdld"; $strClassM = "tdmd"; $strChbClass = "checkboxline";
      		if ($i%2 == 1) {$strClassL = "tdlb"; $strClassM = "tdmb"; $strChbClass = "checkbox";}
      		if ($arrDataLines[$i]['active'] == 0) {$strActive = translate('No');} else {$strActive = translate('Yes');}
      		foreach($arrDescription AS $elem) {
        		$mastertp->setVariable($elem['name'],$elem['string']);
      		}
			$mastertp->setVariable("DATA_FIELD_1",htmlspecialchars($arrDataLines[$i]['domain'],ENT_COMPAT,'UTF-8'));
			$mastertp->setVariable("DATA_FIELD_2",htmlspecialchars($arrDataLines[$i]['alias'],ENT_COMPAT,'UTF-8'));
			$mastertp->setVariable("DATA_ACTIVE",$strActive);
			$mastertp->setVariable("LINE_ID",$arrDataLines[$i]['id']);
			$mastertp->setVariable("CELLCLASS_L",$strClassL);
			$mastertp->setVariable("CELLCLASS_M",$strClassM);
			$mastertp->setVariable("CHB_CLASS",$strChbClass);
			$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
			$mastertp->setVariable("PICTURE_CLASS","elementShow");
			$mastertp->setVariable("DISABLED","");
			if ($chkModus != "display") $mastertp->setVariable("DISABLED","disabled");
			if ($arrDataLines[$i]['nodelete'] == "1") {
				$mastertp->setVariable("DEL_HIDE_START","<!--");
				$mastertp->setVariable("DEL_HIDE_STOP","-->");
				$mastertp->setVariable("DISABLED","disabled");
			}
			$mastertp->parse("datarowcommon");
		}
	} else {
		$mastertp->parse("datarowcommon");
	}
	// Show page numbers
	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	if (isset($intCount)) $mastertp->setVariable("PAGES",$myVisClass->buildPageLinks(filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING),$intCount,$chkLimit));
	$mastertp->parse("datatablecommon");
	$mastertp->show("datatablecommon");
}
// Show messages
if (isset($strMessage)) {$mastertp->setVariable("DBMESSAGE",$strMessage);} else {$mastertp->setVariable("DBMESSAGE","&nbsp;");}
$mastertp->parse("msgfooter");
$mastertp->show("msgfooter");
//
// Process footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>