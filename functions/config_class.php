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
// Component : Configuration Class
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-04-13 14:49:45 +0200 (Mi, 13. Apr 2011) $
// Author    : $LastChangedBy: martin $
// Version   : 3.1.1
// Revision  : $LastChangedRevision: 1074 $
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Class: Configuration class
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Includes all functions used for handling configuration files with NagiosQL
//
// Name: nagconfig
//
// Class variables:
// $arrSettings  		Includes all global settings ($SETS)
// $intDomainId			Domain ID
// $strDBMessage		Process messages
// $resConnectId		Remote connection ressource
// $resSFTP				Remote SFTP connection ressource
//
///////////////////////////////////////////////////////////////////////////////////////////////
class nagconfig {
  	// Define class variables
  	var $arrSettings;       // Will be filled in class constructor
  	var $intDomainId  = 0;  // Will be filled in class constructor
  	var $strDBMessage = ""; // Will be filled in functions
	var $resConnectId;		// Will be filled in connection function
	var $resSFTP;			// Will be filled in connection function
	var $arrRelData   = ""; // Will be filled in getRelation function
	var $strRelTable  = ""; // Will be filled in getRelation function


	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Class constructor
  	///////////////////////////////////////////////////////////////////////////////////////////
  	//
  	//  Activities during initialisation
  	//
  	///////////////////////////////////////////////////////////////////////////////////////////
  	function nagconfig() {
    	if (isset($_SESSION) && isset($_SESSION['SETS'])) {
    		// Read global settings
    		$this->arrSettings = $_SESSION['SETS'];
    		if (isset($_SESSION['domain'])) $this->intDomainId = $_SESSION['domain'];
		}
  	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Get last change date of table and config file
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Determines the dates of the last data table change and the last modification to the 
	//  configuration file
	//
	//  Parameter:  		$strTableName   	Name of the data table
	//   
  	//  Return values:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
	//
	//						$strTimeTable   	Date of the last data table change
	//            			$strTimeFile    	Date of the last change to the configuration file
	//            			$strCheckConfig		Information string (text message)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function lastModified($strTableName,&$strTimeTable,&$strTimeFile,&$strCheckConfig) {
    	// Get configuration filename based on table name
    	switch($strTableName) {
      		case "tbl_timeperiod":      	$strFile = "timeperiods.cfg"; 			break;
      		case "tbl_command":       		$strFile = "commands.cfg"; 				break;
      		case "tbl_contact":       		$strFile = "contacts.cfg"; 				break;
      		case "tbl_contacttemplate":   	$strFile = "contacttemplates.cfg"; 		break;
      		case "tbl_contactgroup":    	$strFile = "contactgroups.cfg"; 		break;
      		case "tbl_hosttemplate":    	$strFile = "hosttemplates.cfg"; 		break;
      		case "tbl_servicetemplate":   	$strFile = "servicetemplates.cfg"; 		break;
      		case "tbl_hostgroup":     		$strFile = "hostgroups.cfg"; 			break;
      		case "tbl_servicegroup":    	$strFile = "servicegroups.cfg"; 		break;
      		case "tbl_servicedependency": 	$strFile = "servicedependencies.cfg"; 	break;
      		case "tbl_hostdependency":    	$strFile = "hostdependencies.cfg"; 		break;
      		case "tbl_serviceescalation": 	$strFile = "serviceescalations.cfg"; 	break;
      		case "tbl_hostescalation":    	$strFile = "hostescalations.cfg"; 		break;
      		case "tbl_hostextinfo":     	$strFile = "hostextinfo.cfg"; 			break;
      		case "tbl_serviceextinfo":    	$strFile = "serviceextinfo.cfg"; 		break;
    	}
    	// Define variables
    	$strCheckConfig = "";
    	$strTimeTable   = "unknown";
    	$strTimeFile  	= "unknown";
		// Get configuration file data
		$this->getConfigData("basedir",$strBaseDir);
		$this->getConfigData("method",$strMethod);
		$this->getConfigData("enable_common",$strCommon);
    	// Clear status cache
    	clearstatcache();
    	if (isset($_SESSION['domain'])) $this->intDomainId = $_SESSION['domain'];
    	// Get last change of date table
		if ($strCommon == 1) {
			$strSQL 	= "SELECT `updateTime` FROM `tbl_tablestatus` WHERE `domainId`=".$this->intDomainId." OR `domainId`=0 AND `tableName`='".$strTableName."' ORDER BY `updateTime` DESC LIMIT 1";
		} else {
			$strSQL 	= "SELECT `updateTime` FROM `tbl_tablestatus` WHERE `domainId`=".$this->intDomainId." AND `tableName`='".$strTableName."'";
		}
		$booReturn 	= $this->myDBClass->getSingleDataset($strSQL,$arrDataset);
		if ($booReturn && isset($arrDataset['updateTime'])) {
			$strTimeTable = $arrDataset['updateTime'];
		} else {
			$strSQL = "SELECT `last_modified` FROM `".$strTableName."` WHERE `config_id`=".$this->intDomainId." ORDER BY `last_modified` DESC LIMIT 1";
			$booReturn = $this->myDBClass->getSingleDataset($strSQL,$arrDataset);
    		if (($booReturn == true) && isset($arrDataset['last_modified'])) {
      			$strTimeTable = $arrDataset['last_modified'];
			}
		}
		// Lokal file system
		if (($strMethod == 1) && (file_exists($strBaseDir."/".$strFile))) {
			$intFileStamp = filemtime($strBaseDir."/".$strFile);
			$strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
		// FTP file system
		} else if ($strMethod == 2) {
			// Check connection
			if (!isset($this->resConnectId) || !is_resource($this->resConnectId)) {
				$booReturn = $this->getFTPConnection();
				if ($booReturn == 1) return(1); 
			}
			$intFileStamp = ftp_mdtm($this->resConnectId, $strBaseDir."/".$strFile);
			if ($intFileStamp != -1) $strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
			ftp_close($this->resConnectId);
		// SSH file system
		} else if ($strMethod == 3) {
			// Check connection
			if (!isset($this->resConnectId) || !is_resource($this->resConnectId)) {
				$booReturn = $this->getSSHConnection();
				if ($booReturn == 1) return(1); 
			}
			// Check file date
			if (is_array($this->sendSSHCommand('ls '.str_replace("//","/",$strBaseDir."/".$strFile)))) {
				$arrInfo 	  = ssh2_sftp_stat($this->resSFTP,str_replace("//","/",$strBaseDir."/".$strFile));
				$intFileStamp = $arrInfo['mtime'];
				if ($intFileStamp != -1) $strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
			}
		}
		// Fill message string
		if ($strTimeFile != 'unknown') {
			if (strtotime($strTimeTable) > $intFileStamp) $strCheckConfig = translate('Warning: configuration file is out of date!');
			return(0);
		} else {
			$strCheckConfig = translate('Warning: configuration file is out of date!');
			return(0);
		}
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Get last change date of table and config file
	///////////////////////////////////////////////////////////////////////////////////////////
  	//
  	//  Determines the dates of the last data table change and the last modification to the 
	//  configuration file
  	//
	//  Parameter:  		$strConfigname   	Name of the configuration file
  	//						$strId      		Dataset ID
  	//            			$strType    		Datatype ("host" or "service")
  	//
  	//  Return values:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
	//
	//						$strTime   			Date of the last dataset change
	//            			$strTimeFile    	Date of the last change to the configuration file
	//            			$intOlder			Status field:
	//											0  if file is out-of-date
	//											-1 if file is up-to-date)
    //
    ///////////////////////////////////////////////////////////////////////////////////////////
  	function lastModifiedDir($strConfigname,$strId,$strType,&$strTime,&$strTimeFile,&$intOlder) {
		// Build file name
    	$strFile  = $strConfigname.".cfg";
    	// Define variables
    	$intCheck = 0;
    	// Clear status cache
    	clearstatcache();
    	// Get last change on dataset
    	if ($strType == "host") {
      		$strTime 	= $this->myDBClass->getFieldData("SELECT DATE_FORMAT(`last_modified`,'%Y-%m-%d %H:%i:%s')
                             						   	  FROM `tbl_host` WHERE `id`=".$strId);
      		$booReturn 	= $this->getConfigData("hostconfig",$strBaseDir);
      		if ($strTime != false) $intCheck++;
    	} else if ($strType == "service") {
      		$strTime 	= $this->myDBClass->getFieldData("SELECT DATE_FORMAT(`last_modified`,'%Y-%m-%d %H:%i:%s')
                             						   	  FROM `tbl_service` WHERE `id`=".$strId);
			$booReturn 	= $this->getConfigData("serviceconfig",$strBaseDir);
      		if ($strTime != false) $intCheck++;
    	} else {
      		$strTime	= "undefined";
      		$intOlder   = 1;
    	}
   		// Get last change of configuration file
    	$booReturn = $this->getConfigData("method",$strMethod);
    	// Local file system
		if (($strMethod == 1) && (file_exists($strBaseDir."/".$strFile))) {
      		$intFileStamp = filemtime($strBaseDir."/".$strFile);
      		$strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
      		$intCheck++;
		// Remote file via FTP
    	} else if ($strMethod == 2) {
			// Check connection
			if (!isset($this->resConnectId) || !is_resource($this->resConnectId)) {
				$booReturn = $this->getFTPConnection();
				if ($booReturn == 1) return(1); 
			}
			$intFileStamp = ftp_mdtm($this->resConnectId, $strBaseDir."/".$strFile);
			if ($intFileStamp != -1) $strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
			ftp_close($this->resConnectId);
			$intCheck++;
    	} else if ($strMethod == 3) {
			// Check connection
			if (!isset($this->resConnectId) || !is_resource($this->resConnectId)) {
				$booReturn = $this->getSSHConnection();
				if ($booReturn == 1) return(1); 
			}
			
			if (is_array($this->sendSSHCommand('ls '.str_replace("//","/",$strBaseDir.'/'.$strFile)))) {
				$arrInfo 	  = ssh2_sftp_stat($this->resSFTP,str_replace("//","/",$strBaseDir."/".$strFile));
				$intFileStamp = $arrInfo['mtime'];
				if ($intFileStamp != -1) $strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
				$intCheck++;
			} else {
				// Try again
				if (is_array($this->sendSSHCommand('ls '.str_replace("//","/",$strBaseDir.'/'.$strFile)))) {
					$arrInfo 	  = ssh2_sftp_stat($this->resSFTP,str_replace("//","/",$strBaseDir."/".$strFile));
					$intFileStamp = $arrInfo['mtime'];
					if ($intFileStamp != -1) $strTimeFile  = date("Y-m-d H:i:s",$intFileStamp);
					$intCheck++;
				} else {
					return(1);
				}
			}
    	} else {
      		$strTimeFile = "undefined";
      		$intOlder    = 1;
    	}
    	// Get differennces
    	if ($intCheck == 2) {
      		if (strtotime($strTime) > $intFileStamp) {$intOlder = 1;} else {$intOlder = 0;}
      		return(0);
    	}
    	return(1);
  	}
  	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Function: Move a config file to the backup directory
  	///////////////////////////////////////////////////////////////////////////////////////////
  	//
  	//  Moves an existing configuration file to the backup directory and removes then the
  	//  original file
  	//
  	//  Parameter:  	$strType    Type of the configuration file
  	//					$strName    Name of the configuration file
  	//
  	//  Return values:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
  	//
  	///////////////////////////////////////////////////////////////////////////////////////////
  	function moveFile($strType,$strName) {
    	// Get directories
    	switch ($strType) {
      		case "host":    		$this->getConfigData("hostconfig",$strConfigDir);
                					$this->getConfigData("hostbackup",$strBackupDir);
                					break;
      		case "service": 		$this->getConfigData("serviceconfig",$strConfigDir);
                					$this->getConfigData("servicebackup",$strBackupDir);
                					break;
      		case "basic":   		$this->getConfigData("basedir",$strConfigDir);
                					$this->getConfigData("backupdir",$strBackupDir);
                					break;
      		case "nagiosbasic": 	$this->getConfigData("nagiosbasedir",$strConfigDir);
                					$this->getConfigData("backupdir",$strBackupDir);
                					break;
      		default:      			return(1);
    	}
    	// Get tranfer method
    	$this->getConfigData("method",$strMethod);
    	// Local file system
		if ($strMethod == 1) {
      		// Save configuration file
      		if (file_exists($strConfigDir."/".$strName) && is_writable($strBackupDir) && is_writable($strConfigDir)) {
       			$strOldDate = date("YmdHis",mktime());
        		copy($strConfigDir."/".$strName,$strBackupDir."/".$strName."_old_".$strOldDate);
        		unlink($strConfigDir."/".$strName);
      		} else if (!is_writable($strBackupDir)) {
        		$this->strDBMessage = translate('Cannot backup and delete the old configuration file (check the permissions)!');
        		return(1);
      		}
		// Remote file (FTP)
    	} else if ($strMethod == 2) {
			// Check connection
			if (!isset($this->resConnectId) || !is_resource($this->resConnectId)) {
				$booReturn = $this->getFTPConnection();
				if ($booReturn == 1) return(1); 
			}
        	// Save configuration file
        	$intFileStamp = ftp_mdtm($this->resConnectId, $strConfigDir."/".$strName);
        	if ($intFileStamp > -1) {
          		$strOldDate = date("YmdHis",mktime());
          		$intReturn  = ftp_rename($this->resConnectId,$strConfigDir."/".$strName,$strBackupDir."/".$strName."_old_".$strOldDate);
          		if (!$intReturn) {
            		$this->strDBMessage = translate('Cannot backup the old configuration file because the permissions are wrong (remote FTP)!');
          		}
			}
		// Remote file (SFTP)
    	} else if ($strMethod == 3) {
			// Check connection
			if (!isset($this->resConnectId) || !is_resource($this->resConnectId)) {
				$booReturn = $this->getSSHConnection();
				if ($booReturn == 1) return(1); 
			}
        	// Save configuration file
			if (is_array($this->sendSSHCommand('ls '.str_replace("//","/",$strConfigDir."/".$strName)))) {
				$arrInfo = ssh2_sftp_stat($this->resSFTP,str_replace("//","/",$strConfigDir."/".$strName));
				$intFileStamp = $arrInfo['mtime'];
        		if ($intFileStamp > -1) {
				
          			$strOldDate = date("YmdHis",mktime());
          			$intReturn  = ssh2_sftp_rename($this->resSFTP,$strConfigDir."/".$strName,$strBackupDir."/".$strName."_old_".$strOldDate);
          			/// BUG - DELETE DOES NOT WORK ???
					if (!$intReturn) {
            			$this->strDBMessage = translate('Cannot backup the old configuration file because the permissions are wrong (remote SFTP)!');
          			}
				}
			}
		}
    	return(0);
  	}	

  	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Function: Remove a config file
  	///////////////////////////////////////////////////////////////////////////////////////////
  	//
  	//  Parameter:  		$strType    Filename including path to remove
  	//
  	//  Return values:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
  	//
  	///////////////////////////////////////////////////////////////////////////////////////////
  	function removeFile($strName) {
    	// Get access method
    	$this->getConfigData("method",$strMethod);
    	// Local file system
		if ($strMethod == 1) {
      		// Remove file if exists
      		if (file_exists($strName)) {
        		unlink($strName);
      		} else {
        		$this->strDBMessage = translate('Cannot delete the file (check the permissions)!');
        		return(1);
      		}
		// Remote file (FTP)
    	} else if ($strMethod == 2) {
			// Check connection
			if (!isset($this->resConnectId) || !is_resource($this->resConnectId)) {
				$booReturn = $this->getFTPConnection();
				if ($booReturn == 1) return(1); 
			}
        	// Remove file if exists
        	$intFileStamp = ftp_mdtm($this->resConnectId, $strName);
        	if ($intFileStamp > -1) {
          		$intReturn  = ftp_delete($this->resConnectId,$strName);
          		if (!$intReturn) {
            		$this->strDBMessage = translate('Cannot delete file because the permissions are wrong (remote FTP)!');
          		}
        	} else {
            	$this->strDBMessage = translate('Cannot delete file because it does not exists (remote FTP)!');
			}
		// Remote file (SSH)
    	} else if ($strMethod == 3) {
			// Check connection
			if (!isset($this->resConnectId) || !is_resource($this->resConnectId)) {
				$booReturn = $this->getSSHConnection();
				if ($booReturn == 1) return(1); 
			}
			// Remove file if exists
			if (is_array($this->sendSSHCommand('ls '.$strName))) {
				$intReturn = ssh2_sftp_unlink($this->resSFTP,$strName);
        		if (!$intReturn) {
            		$this->strDBMessage = translate('Cannot delete file because the permissions are wrong (remote SFTP)!');
          		}
			} else {
            	$this->strDBMessage = translate('Cannot delete file because it does not exists (remote SFTP)!');
			}
		}
    	return(0);
  	}
	
  	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Function: Copy a config file
  	///////////////////////////////////////////////////////////////////////////////////////////
  	//
  	//  Parameter:  		$strFileRemote    	Remote file name
	//						$strLocalFile		Local file name
	//						$intDirection		0 = from remote to local
	//											1 = from local to remote
  	//
  	//  Return values:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
  	//
  	///////////////////////////////////////////////////////////////////////////////////////////
	function configCopy($strFileRemote,$strFileLokal,$intDirection=0) {
		// Get method
    	$this->getConfigData("method",$strMethod);
		if ($strMethod == 2) {
			// Open ftp connection
			if (!isset($this->resConnectId) || !is_resource($this->resConnectId)) {
				$booReturn = $this->getFTPConnection();
				if ($booReturn == 1) return(1);
			}
			if ($intDirection == 0) {
				if (!ftp_get($this->resConnectId,$strFileLokal,$strFileRemote,FTP_ASCII)) {
					$this->strDBMessage = translate('Cannot get the configuration file (FTP connection failed)!');
					ftp_close($this->resConnectId);
					return(1);
				}
			}
			if ($intDirection == 1) {
				if (!ftp_put($this->resConnectId,$strFileRemote,$strFileLokal,FTP_ASCII)) {
					$this->strDBMessage = translate('Cannot write the configuration file (FTP connection failed)!');
					ftp_close($this->resConnectId);
					return(1);
				}
			}
			return(0);
		} else if ($strMethod == 3) {
			// Open ssh connection
			if (!isset($this->resConnectId) || !is_resource($this->resConnectId)) {
				$booReturn = $this->getSSHConnection();
				if ($booReturn == 1) return(1);
			}
			if ($intDirection == 0) {
				if (is_array($this->sendSSHCommand('ls '.$strFileRemote))) {		
					if (!ssh2_scp_recv($this->resConnectId,$strFileRemote,$strFileLokal)) {
						$this->strDBMessage = translate('Cannot get the configuration file (SSH connection failed)!');
						return(1);
					}
				} else {
					$this->strDBMessage = translate('Cannot get the configuration file (remote file does not exist)!');
					return(1);
				}
				return(0);
			}
			if ($intDirection == 1) {
				if (!ssh2_scp_send($this->resConnectId,$strFileLokal,$strFileRemote,0644)) {
					$this->strDBMessage = translate('Cannot write the configuration file (SSH connection failed)!');
					return(1);
				}
				return(0);
			}
		}
		return(1);
  	}

	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Function: Write a config file (full version)
  	///////////////////////////////////////////////////////////////////////////////////////////
  	//
  	//  Writes a configuration file including all datasets of a configuration table or returns
  	//  the output as a text file for download.
  	//
  	//  Parameters:			$strTableName 	Table name
  	//  -----------			$intMode    	0 = Write file to filesystem
	//										1 = Return Textfile fot download
  	//
  	//  Return value:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
  	//
  	///////////////////////////////////////////////////////////////////////////////////////////
  	function createConfig($strTableName,$intMode=0) {
    	// Do not create configs in common domain
		if ($this->intDomainId == 0) {
			$this->strDBMessage = translate('It is not possible to write config files directly from the common domain!');
			return(1);
		}
		// Get config strings
		$this->getConfigStrings($strTableName,$strFileString,$strOrderField);
		if ($strFileString == "") return 1;
		$strFile     = $strFileString.".cfg";
    	$setTemplate = $strFileString.".tpl.dat";
		// Open configuration file in "write" mode
    	if ($intMode == 0) {
			$booReturn = $this->getConfigFile($strFile,0,$resConfigFile,$strConfigFile);
			if ($booReturn == 1) return 1;
		}
		// Load config template file
    	$arrTplOptions = array('use_preg' => false);
    	$configtp = new HTML_Template_IT($this->arrSettings['path']['physical']."/templates/files/");
    	$configtp->loadTemplatefile($setTemplate, true, true);
    	$configtp->setOptions($arrTplOptions);
    	$configtp->setVariable("CREATE_DATE",date("Y-m-d H:i:s",mktime()));
    	$this->getConfigData("version",$strVersionValue);
		$configtp->setVariable("NAGIOS_QL_VERSION",$this->arrSettings['db']['version']);
    	if ($strVersionValue == 3) $strVersion = "Nagios 3.x config file";
    	if ($strVersionValue == 2) $strVersion = "Nagios 2.9 config file";
    	if ($strVersionValue == 1) $strVersion = "Nagios 2.x config file";
    	$configtp->setVariable("VERSION",$strVersion);
		// Get config data from given table and define file name
		$this->getConfigData("utf8_decode",$setUTF8Decode);
		$this->getConfigData("enable_common",$setEnableCommon);
		if ($setEnableCommon != 0) {
			$strDomainWhere = " (`config_id`=".$this->intDomainId." OR `config_id`=0) ";	
		} else {
			$strDomainWhere = " (`config_id`=".$this->intDomainId.") ";
		}
    	$strSQL      = "SELECT * FROM `".$strTableName."` WHERE $strDomainWhere ORDER BY `".$strOrderField."`";
    	$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
    	if ($booReturn == false) {
      		$this->strDBMessage = "<span class=\"verify-critical\">".translate('Error while selecting data from database:')."</span>".
								  "<br>".$this->myDBClass->strDBError."<br>";
		} else if ($intDataCount != 0) {
			// Process every data set
      		for ($i=0;$i<$intDataCount;$i++) {
				foreach($arrData[$i] AS $key => $value) {
          			$intSkip = 0;
          			if ($key == "id")     $intDataId = $value;
					if ($key == "active") $key = "register";
					
					// UTF8 decoded vaules
					if ($setUTF8Decode == 1) $value = utf8_decode($value);
					
					// Pass special fields (NagiosQL data fields not used by Nagios itselves)
					if ($this->skipEntries($strTableName,$strVersionValue,$key,$value) == 1) continue;
			
					// Get relation data
					$intSkip = $this->getRelationData($strTableName,$configtp,$arrData[$i],$key,$value);

					// Rename field names
					$this->renameFields($strTableName,$intDataId,$key,$value,$intSkip);

					// Inset data field
					if ($intSkip != 1) {
						// Insert fill spaces
						$strFillLen = (25-strlen($key));
						$strSpace = " ";
						for ($f=0;$f<$strFillLen;$f++) {
							$strSpace .= " ";
						}
						// Write key and value to template
						$configtp->setVariable("ITEM_TITLE",$key.$strSpace);
						// Short values
						if (strlen($value) < 800) {
							$configtp->setVariable("ITEM_VALUE",$value);
							$configtp->parse("configline");
						// Long values
						} else {
							$arrValueTemp = explode(",",$value);
							$strValueNew  = "";
							$intArrCount  = count($arrValueTemp);
							$intCounter   = 0;
							$strSpace = " ";
							for ($f=0;$f<25;$f++) {
								$strSpace .= " ";
							}
							foreach($arrValueTemp AS $elem) {
								if (strlen($strValueNew) < 800) {
									$strValueNew .= $elem.",";
								} else {
									if (substr($strValueNew,-1) == ",") {
										$strValueNew = substr($strValueNew,0,-1);
									}
									if ($intCounter < $intArrCount) {
										$strValueNew = $strValueNew.",\\";
										$configtp->setVariable("ITEM_VALUE",$strValueNew);
										$configtp->parse("configline");
										$configtp->setVariable("ITEM_TITLE",$strSpace);
									} else {
										$configtp->setVariable("ITEM_VALUE",$strValueNew);
										$configtp->parse("configline");
										$configtp->setVariable("ITEM_TITLE",$strSpace);
									}
									$strValueNew = $elem.",";
								}
								$intCounter++;
							}
							if ($strValueNew != "") {
								if (substr($strValueNew,-1) == ",") {
									$strValueNew = substr($strValueNew,0,-1);
								}
								$configtp->setVariable("ITEM_VALUE",$strValueNew);
								$configtp->parse("configline");
								$strValueNew = "";
							}
						}
					}
				}

				// Special processing for time periods
				if ($strTableName == "tbl_timeperiod") {
					$strSQLTime = "SELECT `definition`, `range` FROM `tbl_timedefinition` WHERE `tipId` = ".$arrData[$i]['id'];
					$booReturn  = $this->myDBClass->getDataArray($strSQLTime,$arrDataTime,$intDataCountTime);
					if ($booReturn && $intDataCountTime != 0) {
						foreach ($arrDataTime AS $data) {
							// Insert fill spaces
							$strFillLen = (25-strlen($data['definition']));
							$strSpace = " ";
							for ($f=0;$f<$strFillLen;$f++) {
								$strSpace .= " ";
							}
							// Write key and value
							$configtp->setVariable("ITEM_TITLE",stripslashes($data['definition']).$strSpace);
							$configtp->setVariable("ITEM_VALUE",stripslashes($data['range']));
							$configtp->parse("configline");
						}
					}
				}
				
				// Write configuration set
				$configtp->parse("configset");
			}
		}
    	$configtp->parse();
    	// Write configuration file
    	if ($intMode == 0) {
			$booReturn = $this->writeConfigFile($configtp->get(),$strFile,0,$resConfigFile,$strConfigFile);
			return($booReturn);
		// Return configuration file (download)
		} else if ($intMode == 1) {
			$configtp->show();
			return(0);
    	}
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Function: Write a config file (single dataset version)
  	///////////////////////////////////////////////////////////////////////////////////////////
  	//
  	//  Writes a configuration file including one single datasets of a configuration table or 
  	//  returns the output as a text file for download.
  	//
  	//  Parameters:			$strTableName 	Table name
  	//  -----------			$intDbId		Data ID
	//						$intMode    	0 = Write file to filesystem
	//										1 = Return Textfile fot download
  	//
  	//  Return value:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
  	//
  	///////////////////////////////////////////////////////////////////////////////////////////
	function createConfigSingle($strTableName,$intDbId = 0,$intMode = 0) {
		// Do not create configs in common domain
		if ($this->intDomainId == 0) {
			$this->strDBMessage = translate('It is not possible to write config files directly from the common domain!');
			return(1);
		}
    	// Get all data from table
		$this->getConfigData("utf8_decode",$setUTF8Decode);
		$this->getConfigData("enable_common",$setEnableCommon);
		if ($setEnableCommon != 0) {
			$strDomainWhere = " (`config_id`=".$this->intDomainId." OR `config_id`=0) ";	
		} else {
			$strDomainWhere = " (`config_id`=".$this->intDomainId.") ";
		}
		if ($intDbId == 0) {
    		$strSQL = "SELECT * FROM `".$strTableName."` WHERE $strDomainWhere ORDER BY `id`";
		} else {
    		$strSQL = "SELECT * FROM `".$strTableName."` WHERE $strDomainWhere AND `id`=$intDbId";
		}
    	$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
    	if (($booReturn != false) && ($intDataCount != 0)) {
      		$intError = 0;
			for ($i=0;$i<$intDataCount;$i++) {
        		// Process form POST variable
        		$strChbName = "chbId_".$arrData[$i]['id'];
        		// Check if this POST variable exists or the data ID parameter matches
        		if (isset($_POST[$strChbName]) || (($intDbId != 0) && ($intDbId == $arrData[$i]['id']))) {
					$this->myDBClass->strDBError = "";
          			// Define variable names based on table name
          			switch($strTableName) {
            			case "tbl_host":
              				$strConfigName = $arrData[$i]['host_name'];
							$intDomainId   = $arrData[$i]['config_id'];
              				$setTemplate   = "hosts.tpl.dat";
							$intType 	   = 1;
              				$strSQLData    = "SELECT * FROM `".$strTableName."` WHERE `host_name`='$strConfigName' AND `config_id`=$intDomainId";
              				break;
            			case "tbl_service":
              				$strConfigName = $arrData[$i]['config_name'];
							$intDomainId   = $arrData[$i]['config_id'];
							$setTemplate   = "services.tpl.dat";
							$intType	   = 2;
							$strSQLData    = "SELECT * FROM `".$strTableName."` WHERE `config_name`='$strConfigName' AND `config_id`=$intDomainId 
											  ORDER BY `service_description`";
							break;
          			}
          			$strFile = $strConfigName.".cfg";
					// Open configuration file in "write" mode
					if ($intMode == 0) {
						$booReturn = $this->getConfigFile($strFile,$intType,$resConfigFile,$strConfigFile);
						if ($booReturn == 1) return 1;
					}
					// Load config template file
          			$arrTplOptions = array('use_preg' => false);
          			$configtp = new HTML_Template_IT($this->arrSettings['path']['physical']."/templates/files/");
          			$configtp->loadTemplatefile($setTemplate, true, true);
          			$configtp->setOptions($arrTplOptions);
          			$configtp->setVariable("CREATE_DATE",date("Y-m-d H:i:s",mktime()));
          			$this->getConfigData("version",$strVersionValue);
					$configtp->setVariable("NAGIOS_QL_VERSION",$this->arrSettings['db']['version']);
					if ($strVersionValue == 3) $strVersion = "Nagios 3.x config file";
					if ($strVersionValue == 2) $strVersion = "Nagios 2.9 config file";
					if ($strVersionValue == 1) $strVersion = "Nagios 2.x config file";
					$configtp->setVariable("VERSION",$strVersion);
					
					// Alle passenden Datensätze holen
					$booReturn = $this->myDBClass->getDataArray($strSQLData,$arrDataConfig,$intDataCountConfig);
					if ($booReturn == false) {
						$this->strDBMessage = translate('Error while selecting data from database:')."<br>".$this->myDBClass->strDBError."<br>";
		  			} else if ($intDataCountConfig != 0) {
		  				// Process every data set
		            	for ($y=0;$y<$intDataCountConfig;$y++) {
							foreach($arrDataConfig[$y] AS $key => $value) {
								$intSkip = 0;
								if ($key == "id")     $intDataId = $value;
								if ($key == "active") $key = "register";
								
								// UTF8 decoded vaules
								if ($setUTF8Decode == 1) $value = utf8_decode($value);
								
								// Pass special fields (NagiosQL data fields not used by Nagios itselves)
								if ($this->skipEntries($strTableName,$strVersionValue,$key,$value) == 1) continue;

								// Get relation data
								$intSkip = $this->getRelationData($strTableName,$configtp,$arrDataConfig[$y],$key,$value);

								// Rename field names
								$this->renameFields($strTableName,$intDataId,$key,$value,$intSkip);

								// Inset data field
								if ($intSkip != 1) {
									// Insert fill spaces
									$strFillLen = (25-strlen($key));
									$strSpace = " ";
									for ($f=0;$f<$strFillLen;$f++) {
										$strSpace .= " ";
									}
									// Write key and value to template
									$configtp->setVariable("ITEM_TITLE",$key.$strSpace);
									// Short values
									if (strlen($value) < 800) {
										$configtp->setVariable("ITEM_VALUE",$value);
										$configtp->parse("configline");
									// Long values
									} else {
										$arrValueTemp = explode(",",$value);
										$strValueNew  = "";
										$intArrCount  = count($arrValueTemp);
										$intCounter   = 0;
										$strSpace = " ";
										for ($f=0;$f<25;$f++) {
											$strSpace .= " ";
										}
										foreach($arrValueTemp AS $elem) {
											if (strlen($strValueNew) < 800) {
												$strValueNew .= $elem.",";
											} else {
												if (substr($strValueNew,-1) == ",") {
													$strValueNew = substr($strValueNew,0,-1);
												}
												if ($intCounter < $intArrCount) {
													$strValueNew = $strValueNew.",\\";
													$configtp->setVariable("ITEM_VALUE",$strValueNew);
													$configtp->parse("configline");
													$configtp->setVariable("ITEM_TITLE",$strSpace);
												} else {
													$configtp->setVariable("ITEM_VALUE",$strValueNew);
													$configtp->parse("configline");
													$configtp->setVariable("ITEM_TITLE",$strSpace);
												}
												$strValueNew = $elem.",";
											}
											$intCounter++;
										}
										if ($strValueNew != "") {
											if (substr($strValueNew,-1) == ",") {
												$strValueNew = substr($strValueNew,0,-1);
											}
											$configtp->setVariable("ITEM_VALUE",$strValueNew);
											$configtp->parse("configline");
											$strValueNew = "";
										}
									}
								}
							}
							// Write configuration set
							$configtp->parse("configset");
		            	}
						$configtp->parse();
						// Write configuration file
						if ($intMode == 0) {
							$booReturn = $this->writeConfigFile($configtp->get(),$strFile,$intType,$resConfigFile,$strConfigFile);
							if ($booReturn == 1) $intError++;
						// Return configuration file (download)
						} else if ($intMode == 1) {
							$configtp->show();
						}
					}
        		}
      		}
    	} else {
      		$this->myDataClass->writeLog(translate('Configuration write failed - Dataset not found'));
      		$this->strDBMessage = translate('Cannot open/overwrite the configuration file (check the permissions)!');
      		return(1);
    	}
		if ($intError == 0) return(0);
		return(1);
  	}
 
    //3.1 HELP FUNCTIONS
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Help function: Get config parameters
  	///////////////////////////////////////////////////////////////////////////////////////////
	//  $strTableName		-> Table name
	//  $strFileString		-> File name string
	//  $strOrderField		-> Order field name 		(return value)
	///////////////////////////////////////////////////////////////////////////////////////////
  	function getConfigStrings($strTableName,&$strFileString,&$strOrderField) {
		switch($strTableName) {
      		case "tbl_timeperiod":      	$strFileString  = "timeperiods";
                      						$strOrderField  = "timeperiod_name";
                      						break;
      		case "tbl_command":       		$strFileString  = "commands";
                      						$strOrderField  = "command_name";
                      						break;
      		case "tbl_contact":       		$strFileString  = "contacts";
                      						$strOrderField  = "contact_name";
                      						break;
      		case "tbl_contacttemplate": 	$strFileString  = "contacttemplates";
                      						$strOrderField  = "template_name";
                      						break;
      		case "tbl_contactgroup":    	$strFileString  = "contactgroups";
                      						$strOrderField  = "contactgroup_name";
                      						break;
      		case "tbl_hosttemplate":    	$strFileString  = "hosttemplates";
                      						$strOrderField  = "template_name";
                      						break;
      		case "tbl_hostgroup":     		$strFileString  = "hostgroups";
                      						$strOrderField  = "hostgroup_name";
                      						break;
      		case "tbl_servicetemplate": 	$strFileString  = "servicetemplates";
                      						$strOrderField  = "template_name";
                      						break;
      		case "tbl_servicegroup":    	$strFileString  = "servicegroups";
                      						$strOrderField  = "servicegroup_name";
                      						break;
      		case "tbl_hostdependency":		$strFileString  = "hostdependencies";
                      						$strOrderField  = "dependent_host_name";
                      						break;
      		case "tbl_hostescalation": 	 	$strFileString  = "hostescalations";
                      						$strOrderField  = "host_name`,`hostgroup_name";
                      						break;
      		case "tbl_hostextinfo":     	$strFileString  = "hostextinfo";
                      						$strOrderField  = "host_name";
                      						break;
      		case "tbl_servicedependency": 	$strFileString  = "servicedependencies";
                      						$strOrderField  = "dependent_host_name";
                      						break;
      		case "tbl_serviceescalation": 	$strFileString  = "serviceescalations";
                      						$strOrderField  = "host_name`,`service_description";
                      						break;
      		case "tbl_serviceextinfo":    	$strFileString  = "serviceextinfo";
                      						$strOrderField  = "host_name";
                      						break;
      		default:            			$strFileString  = "";
                      						$strOrderField  = "";
    	}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Help function: Open configuration file
  	///////////////////////////////////////////////////////////////////////////////////////////
	//  $strFile			-> File name
	//  $intType			-> Type ID
	//  $resConfigFile		-> Temporary or configuration file ressource (return value)
	//  $strConfigFile		-> Configuration file name					 (return value)
	///////////////////////////////////////////////////////////////////////////////////////////
	function getConfigFile($strFile,$intType,&$resConfigFile,&$strConfigFile) {
		// Get config data
		if ($intType == 1) {
            $this->getConfigData("hostconfig",$strBaseDir);
            $this->getConfigData("hostbackup",$strBackupDir);
			$strType = 'host';
		} else if ($intType == 2) {
			$this->getConfigData("serviceconfig",$strBaseDir);
			$this->getConfigData("servicebackup",$strBackupDir);
			$strType = 'service';
		} else {
			$this->getConfigData("basedir",$strBaseDir);
			$this->getConfigData("backupdir",$strBackupDir);
			$strType = 'basic';
		}
      	$booReturn = $this->getConfigData("method",$strMethod);
		// Backup config file
		$this->moveFile($strType,$strFile);
      	// Method 1 - local file system
		if ($strMethod == 1) {
			// Open the config file
        	if (is_writable($strBaseDir."/".$strFile) || ((!file_exists($strBaseDir."/".$strFile) && (is_writable($strBaseDir))) )) {
				$strConfigFile = $strBaseDir."/".$strFile;
				$resConfigFile = fopen($strConfigFile,"w");
				chmod($strConfigFile, 0644);
        	} else {
          		$this->myDataClass->writeLog("<span class=\"verify-critical\">".translate('Configuration write failed:')."</span>"." ".$strFile);
          		$this->strDBMessage = "<span class=\"verify-critical\">".translate('Cannot open/overwrite the configuration file (check the permissions)!')."</span>";
          		return(1);
        	}
      	// Method 2 - ftp access
	  	} else if ($strMethod == 2) {
        	// Set up basic connection
        	$booReturn    		= $this->getConfigData("server",$strServer);
        	$this->resConnectId = ftp_connect($strServer);
        	// Login with username and password
        	$booReturn    = $this->getConfigData("user",$strUser);
        	$booReturn    = $this->getConfigData("password",$strPasswd);
        	$login_result = ftp_login($this->resConnectId, $strUser, $strPasswd);
        	// Check connection
        	if ((!$this->resConnectId) || (!$login_result)) {
          		$this->myDataClass->writeLog("<span class=\"verify-critical\">".translate('Configuration write failed (FTP connection failed):')."</span>"." ".$strFile);
          		$this->strDBMessage = "<span class=\"verify-critical\">".translate('Cannot open/overwrite the configuration file (FTP connection failed)!')."</span>";
          		return(1);
        	} else {
				// Open the config file
				$strConfigFile = tempnam(sys_get_temp_dir(), 'nagiosql');
				$resConfigFile = fopen($strConfigFile,"w");
			}
       	// Method 3 - ssh access
	  	} else if ($strMethod == 3) {
			// Check connection
			if (!isset($this->resConnectId) || !is_resource($this->resConnectId)) {
				$booReturn = $this->getSSHConnection();
				if ($booReturn == 1) return(1); 
			}
			// Open the config file
			$strConfigFile = tempnam(sys_get_temp_dir(), 'nagiosql');
			$resConfigFile = fopen($strConfigFile,"w");
      	}
    }
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Help function: Write configuration file
  	///////////////////////////////////////////////////////////////////////////////////////////
	//  $strData			-> Data string
	//  $strFile			-> File name
	//  $intType			-> Type ID
	//  $resConfigFile		-> Temporary or configuration file ressource
	//  $strConfigFile		-> Configuration file name
	///////////////////////////////////////////////////////////////////////////////////////////
	function writeConfigFile($strData,$strFile,$intType,$resConfigFile,$strConfigFile) {
		// Get config data
		if ($intType == 1) {
            $this->getConfigData("hostconfig",$strBaseDir);
		} else if ($intType == 2) {
			$this->getConfigData("serviceconfig",$strBaseDir);
		} else {
			$this->getConfigData("basedir",$strBaseDir);
		}
		$booReturn = $this->getConfigData("method",$strMethod);
		fwrite($resConfigFile,$strData);
		// Local filesystem
		if ($strMethod == 1) {
			fclose($resConfigFile);
		// FTP access
		} else if ($strMethod == 2) {
			// SSH Possible
			if (!function_exists('ftp_put')) {
				$this->strDBMessage = translate('FTP module not loaded!');
				return(1);
			}
			if (!ftp_put($this->resConnectId,$strBaseDir."/".$strFile,$strConfigFile,FTP_ASCII)) {
				$this->strDBMessage = translate('Cannot open/overwrite the configuration file (FTP connection failed)!');
				ftp_close($this->resConnectId);
				fclose($resConfigFile);
				unlink($strConfigFile);
				return(1);
			}
			ftp_close($this->resConnectId);
			fclose($resConfigFile);
		// SSH access	
		} else if ($strMethod == 3) {
			// SSH Possible
			if (!function_exists('ssh2_scp_send')) {
				$this->strDBMessage = translate('SSH module not loaded!');
				return(1);
			}
			if (!ssh2_scp_send($this->resConnectId,$strConfigFile,$strBaseDir."/".$strFile,0644)) {
				$this->strDBMessage = translate('Cannot open/overwrite the configuration file (remote SFTP)!');
				return(1);
			}
			fclose($resConfigFile);
			unlink($strConfigFile);
		}
		$this->myDataClass->writeLog(translate('Configuration successfully written:')." ".$strFile);
		$this->strDBMessage = translate('Configuration file successfully written!');
		return(0);
	}
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Help function: Return related value
  	///////////////////////////////////////////////////////////////////////////////////////////
	//  $strTableName		-> Table name
	//  $resTemplate		-> Template ressource
	//  $arrData			-> Dataset array
	//  $strDataKey			-> Data key
	//  $strDataValue		-> Data value	(return value)
	///////////////////////////////////////////////////////////////////////////////////////////
	function getRelationData($strTableName,$resTemplate,$arrData,$strDataKey,&$strDataValue) {
		// Pass function for tbl_command
		if ($strTableName == 'tbl_command') return(0);
		// Get relation info and store the value in a class variable (speedup export)
		if 	($this->strRelTable != $strTableName) {
			$intReturn = $this->myDataClass->tableRelations($strTableName,$arrRelations);
			$this->strRelTable = $strTableName;
			$this->arrRelData  = $arrRelations;
		} else {
			$arrRelations = $this->arrRelData;
			$intReturn = 1;
		}
		if (($intReturn == 0) || (!is_array($arrRelations))) return(1);
		// Process relations
        foreach($arrRelations AS $elem) {
			if ($elem['fieldName'] == $strDataKey) {
                // Process normal 1:n relations (1 = only data / 2 = including a * value)
                if (($elem['type'] == 2) && (($strDataValue == 1) || ($strDataValue == 2))) {
                  	$strSQLRel = "SELECT `".$elem['tableName1']."`.`".$elem['target1']."`, `".$elem['linkTable']."`.`exclude` FROM `".$elem['linkTable']."`
                            	  LEFT JOIN `".$elem['tableName1']."` ON `".$elem['linkTable']."`.`idSlave` = `".$elem['tableName1']."`.`id`
                            	  WHERE `idMaster`=".$arrData['id']." AND `".$elem['tableName1']."`.`active`='1'
                            	  ORDER BY `".$elem['tableName1']."`.`".$elem['target1']."`";
                  	$booReturn = $this->myDBClass->getDataArray($strSQLRel,$arrDataRel,$intDataCountRel);
					if ($booReturn && ($intDataCountRel != 0)) {
                    	// Rewrite $strDataValue with returned relation data
                    	if ($strDataValue == 2) {$strDataValue = "*,";} else {$strDataValue = "";}
                    	foreach ($arrDataRel AS $data) {
					  		if ($data['exclude'] == 0) {	
                      			$strDataValue .= $data[$elem['target1']].",";
					  		} else {
								$strDataValue .= "!".$data[$elem['target1']].",";   
					  		}
                    	}
                    	$strDataValue = substr($strDataValue,0,-1);
					} else {
						if ($strDataValue == 2) {$strDataValue = "*";} else {return(1);}
                	}
                // Process normal 1:1 relations
                } else if ($elem['type'] == 1) {
                  	if (($elem['tableName1'] == "tbl_command") && (substr_count($arrData[$elem['fieldName']],"!") != 0)) {
						$arrField   = explode("!",$arrData[$elem['fieldName']]);
                    	$strCommand = strchr($arrData[$elem['fieldName']],"!");
                    	$strSQLRel  = "SELECT `".$elem['target1']."` FROM `".$elem['tableName1']."`
                             		   WHERE `id`=".$arrField[0];
                  	} else {
                    	$strSQLRel  = "SELECT `".$elem['target1']."` FROM `".$elem['tableName1']."`
                                 	   WHERE `id`=".$arrData[$elem['fieldName']];
                  	}
                  	$booReturn = $this->myDBClass->getDataArray($strSQLRel,$arrDataRel,$intDataCountRel);
                  	if ($booReturn && ($intDataCountRel != 0)) {
                    	// Rewrite $strDataValue with returned relation data
                    	if (($elem['tableName1'] == "tbl_command") && (substr_count($strDataValue,"!") != 0)) {
							$strDataValue = $arrDataRel[0][$elem['target1']].$strCommand;
                    	} else {
                      		$strDataValue = $arrDataRel[0][$elem['target1']];
                    	}
                  	} else {
                    	if (($elem['tableName1'] == "tbl_command") && (substr_count($strDataValue,"!") != 0) && ($arrField[0] == -1)) {
							$strDataValue = "null";
						} else {
							return(1);
						}
                  	}
                // Process normal 1:n relations with special table
                } else if (($elem['type'] == 3) && ($strDataValue == 1)) {
                  	$strSQLMaster = "SELECT * FROM `".$elem['linkTable']."` WHERE `idMaster` = ".$arrData['id'];
                  	$booReturn    = $this->myDBClass->getDataArray($strSQLMaster,$arrDataMaster,$intDataCountMaster);
					if ($booReturn && ($intDataCountMaster != 0)) {
						// Rewrite $strDataValue with returned relation data
                    	$strDataValue = "";
                    	foreach ($arrDataMaster AS $data) {
                      		if ($data['idTable'] == 1) {
                        		$strSQLName = "SELECT `".$elem['target1']."` FROM `".$elem['tableName1']."` WHERE `id` = ".$data['idSlave']." AND `".$elem['tableName1']."`.`active`='1'";
                      		} else {
                        		$strSQLName = "SELECT `".$elem['target2']."` FROM `".$elem['tableName2']."` WHERE `id` = ".$data['idSlave']." AND `".$elem['tableName2']."`.`active`='1'";
                      		}
                      		$strDataValue .= $this->myDBClass->getFieldData($strSQLName).",";
                    	}
                    	$strDataValue = substr($strDataValue,0,-1);
                  	} else {
                    	return(1);
                  	}
                // Process special 1:n:str relations with string values (servicedependencies)
                } else if (($elem['type'] == 6) && (($strDataValue == 1) || ($strDataValue == 2))) {
                  	$strSQLRel = "SELECT `".$elem['linkTable']."`.`strSlave`, `".$elem['linkTable']."`.`exclude` 
								  FROM `".$elem['linkTable']."` WHERE `".$elem['linkTable']."`.`idMaster`=".$arrData['id']."
                            	  ORDER BY `".$elem['linkTable']."`.`strSlave`";
                  	$booReturn = $this->myDBClass->getDataArray($strSQLRel,$arrDataRel,$intDataCountRel);
					if ($booReturn && ($intDataCountRel != 0)) {
                    	// Rewrite $strDataValue with returned relation data
                    	if ($strDataValue == 2) {$strDataValue = "*,";} else {$strDataValue = "";}
                    	foreach ($arrDataRel AS $data) {
					  		if ($data['exclude'] == 0) {	
                      			$strDataValue .= $data['strSlave'].",";
					  		} else {
								$strDataValue .= "!".$data['strSlave'].",";   
					  		}
                    	}
                    	$strDataValue = substr($strDataValue,0,-1);
					} else {
						if ($strDataValue == 2) {$strDataValue = "*";} else {return(1);}
                	}
                // Process special relations for free variables
                } else if (($elem['type'] == 4) && ($strDataValue == 1)) {
                  	$strSQLVar = "SELECT * FROM `tbl_variabledefinition` LEFT JOIN `".$elem['linkTable']."` ON `id` = `idSlave`
                          		  WHERE `idMaster`=".$arrData['id']." ORDER BY `name`";
                  	$booReturn = $this->myDBClass->getDataArray($strSQLVar,$arrDSVar,$intDCVar);
                  	if ($booReturn && ($intDCVar != 0)) {
                    	foreach ($arrDSVar AS $vardata) {
                      		// Insert fill spaces
							$strFillLen = (25-strlen($vardata['name']));
							$strSpace = " ";
							for ($f=0;$f<$strFillLen;$f++) {
								$strSpace .= " ";
							}
                      		$resTemplate->setVariable("ITEM_TITLE",$vardata['name'].$strSpace);
                      		$resTemplate->setVariable("ITEM_VALUE",$vardata['value']);
                      		$resTemplate->parse("configline");
                    	}
                  	}
                  	return(1);
                // Process special relations for service groups
                } else if (($elem['type'] == 5) && ($strDataValue == 1)) {
                  	$strSQLMaster = "SELECT * FROM `".$elem['linkTable']."` WHERE `idMaster` = ".$arrData['id'];
                  	$booReturn    = $this->myDBClass->getDataArray($strSQLMaster,$arrDataMaster,$intDataCountMaster);
                  	if ($booReturn && ($intDataCountMaster != 0)) {
						// Rewrite $strDataValue with returned relation data
                    	$strDataValue = "";
                    	foreach ($arrDataMaster AS $data) {
                      		if ($data['idSlaveHG'] != 0) {
                        			$strService = $this->myDBClass->getFieldData("SELECT `".$elem['target2']."` FROM `".$elem['tableName2'].
																			     "` WHERE `id` = ".$data['idSlaveS']." AND `active`='1'");
									$strSQLHG1  = "SELECT `host_name` FROM `tbl_host` LEFT JOIN `tbl_lnkHostgroupToHost` ON `id`=`idSlave` 
											       WHERE `idMaster`=".$data['idSlaveHG'];
                        			$booReturn  = $this->myDBClass->getDataArray($strSQLHG1,$arrHG1,$intHG1);
                        			if ($booReturn && ($intHG1 != 0)) {
                          				foreach ($arrHG1 AS $elemHG1) {
                            				if (substr_count($strDataValue,$elemHG1['host_name'].",".$strService) == 0) {
                              					$strDataValue .= $elemHG1['host_name'].",".$strService.",";
                            				}
                          				}
                        			}
                        			$strSQLHG2  = "SELECT `host_name` FROM `tbl_host` LEFT JOIN `tbl_lnkHostToHostgroup` ON `id`=`idMaster` 
									  		 	   WHERE `idSlave`=".$data['idSlaveHG'];
                        			$booReturn  = $this->myDBClass->getDataArray($strSQLHG2,$arrHG2,$intHG2);
                        			if ($booReturn && ($intHG2 != 0)) {
                          				foreach ($arrHG2 AS $elemHG2) {
                            				if (substr_count($strDataValue,$elemHG2['host_name'].",".$strService) == 0) {
                              					$strDataValue .= $elemHG2['host_name'].",".$strService.",";
                            				}
                          				}
                        			}
                      			} else {
                        			$strHost   	 = $this->myDBClass->getFieldData("SELECT `".$elem['target1']."` FROM `".$elem['tableName1']."` ". 
																				  "WHERE `id` = ".$data['idSlaveH']);
                        			$strService  = $this->myDBClass->getFieldData("SELECT `".$elem['target2']."` FROM `".$elem['tableName2']."` ".
																				  "WHERE `id` = ".$data['idSlaveS']." AND `active`='1'");
                        			if (($strHost != "") && ($strService != "")) {
                          				if (substr_count($strDataValue,$strHost.",".$strService) == 0) {
                            				$strDataValue .= $strHost.",".$strService.",";
                          			}
                        		}
                      		}
                    	}
                    	$strDataValue = substr($strDataValue,0,-1);
						if ($strDataValue == "") return(1);
                  	} else {
                    	return(1);
                  	}
                // Process "*"
                } else if ($strDataValue == 2) {
                  	$strDataValue = "*";
                } else {
                  	return(1);
                }
            }
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Help function: Rename field names 
  	///////////////////////////////////////////////////////////////////////////////////////////
	//  $strTableName		-> Table name
	//  $intDataId			-> Data ID
	//  $key				-> Data key		(return value)
	//  $value				-> Data value	(return value)
	//  $intSkip			-> Skip value	(return value)
	///////////////////////////////////////////////////////////////////////////////////////////
	function renameFields($strTableName,$intDataId,&$key,&$value,&$intSkip) {
		$this->getConfigData("version",$strVersionValue);
		// Picture path
		$this->getConfigData("picturedir",$strPictureDir);
		if ($key == "icon_image") 		$value = $strPictureDir.$value;
		if ($key == "vrml_image") 		$value = $strPictureDir.$value;
		if ($key == "statusmap_image") 	$value = $strPictureDir.$value;
		// Tables
		if ($strTableName == "tbl_host") {
		  	if ($key == "use_template")   	$key = "use";
		  	$strVIValues  = "active_checks_enabled,passive_checks_enabled,check_freshness,obsess_over_host,event_handler_enabled,";
		  	$strVIValues .= "flap_detection_enabled,process_perf_data,retain_status_information,retain_nonstatus_information,";
		  	$strVIValues .= "notifications_enabled";
		  	if (in_array($key,explode(",",$strVIValues))) {
				if ($value == -1)         	$value = "null";
				if ($value == 3)        	$value = "null";
		 	}
		  	if ($key == "parents")      	$value = $this->checkTpl($value,"parents_tploptions","tbl_host",$intDataId,$intSkip);
		  	if ($key == "hostgroups")   	$value = $this->checkTpl($value,"hostgroups_tploptions","tbl_host",$intDataId,$intSkip);
		  	if ($key == "contacts")     	$value = $this->checkTpl($value,"contacts_tploptions","tbl_host",$intDataId,$intSkip);
		  	if ($key == "contact_groups") 	$value = $this->checkTpl($value,"contact_groups_tploptions","tbl_host",$intDataId,$intSkip);
		  	if ($key == "use")        		$value = $this->checkTpl($value,"use_template_tploptions","tbl_host",$intDataId,$intSkip);
		}
		if ($strTableName == "tbl_service") {
		  	if ($key == "use_template")   	$key = "use";
		  	if (($strVersionValue != 3) && ($strVersionValue != 2)) {
				if ($key == "check_interval")   $key = "normal_check_interval";
				if ($key == "retry_interval")   $key = "retry_check_interval";
		  	}
		  	$strVIValues  = "is_volatile,active_checks_enabled,passive_checks_enabled,parallelize_check,obsess_over_service,";
		  	$strVIValues .= "check_freshness,event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,";
		  	$strVIValues .= "retain_nonstatus_information,notifications_enabled";
		  	if (in_array($key,explode(",",$strVIValues))) {
				if ($value == -1)         	$value = "null";
				if ($value == 3)        	$value = "null";
		  	}
		  	if ($key == "host_name")    	$value = $this->checkTpl($value,"host_name_tploptions","tbl_service",$intDataId,$intSkip);
		  	if ($key == "hostgroup_name") 	$value = $this->checkTpl($value,"hostgroup_name_tploptions","tbl_service",$intDataId,$intSkip);
		  	if ($key == "servicegroups")  	$value = $this->checkTpl($value,"servicegroups_tploptions","tbl_service",$intDataId,$intSkip);
		  	if ($key == "contacts")     	$value = $this->checkTpl($value,"contacts_tploptions","tbl_service",$intDataId,$intSkip);
		  	if ($key == "contact_groups") 	$value = $this->checkTpl($value,"contact_groups_tploptions","tbl_service",$intDataId,$intSkip);
		  	if ($key == "use")        		$value = $this->checkTpl($value,"use_template_tploptions","tbl_service",$intDataId,$intSkip);
		}
		if ($strTableName == "tbl_hosttemplate") {
			if ($key == "template_name")  	$key = "name";
			if ($key == "use_template")   	$key = "use";
			$strVIValues  = "active_checks_enabled,passive_checks_enabled,check_freshness,obsess_over_host,event_handler_enabled,";
			$strVIValues .= "flap_detection_enabled,process_perf_data,retain_status_information,retain_nonstatus_information,";
			$strVIValues .= "notifications_enabled";
			if (in_array($key,explode(",",$strVIValues))) {
				if ($value == -1)         	$value = "null";
				if ($value == 3)        	$value = "null";
			}
			if ($key == "parents")      	$value = $this->checkTpl($value,"parents_tploptions","tbl_hosttemplate",$intDataId,$intSkip);
			if ($key == "hostgroups")   	$value = $this->checkTpl($value,"hostgroups_tploptions","tbl_hosttemplate",$intDataId,$intSkip);
			if ($key == "contacts")     	$value = $this->checkTpl($value,"contacts_tploptions","tbl_hosttemplate",$intDataId,$intSkip);
			if ($key == "contact_groups") 	$value = $this->checkTpl($value,"contact_groups_tploptions","tbl_hosttemplate",$intDataId,$intSkip);
			if ($key == "use")        		$value = $this->checkTpl($value,"use_template_tploptions","tbl_hosttemplate",$intDataId,$intSkip);
		}
		if ($strTableName == "tbl_servicetemplate") {
			if ($key == "template_name")  	$key = "name";
			if ($key == "use_template")   	$key = "use";
			if (($strVersionValue != 3) && ($strVersionValue != 2)) {
				if ($key == "check_interval")   $key = "normal_check_interval";
				if ($key == "retry_interval")   $key = "retry_check_interval";
			}
			$strVIValues  = "is_volatile,active_checks_enabled,passive_checks_enabled,parallelize_check,obsess_over_service,";
			$strVIValues .= "check_freshness,event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,";
			$strVIValues .= "retain_nonstatus_information,notifications_enabled";
			if (in_array($key,explode(",",$strVIValues))) {
				if ($value == -1)         	$value = "null";
				if ($value == 3)        	$value = "null";
			}
			if ($key == "host_name")    	$value = $this->checkTpl($value,"host_name_tploptions","tbl_servicetemplate",$intDataId,$intSkip);
			if ($key == "hostgroup_name") 	$value = $this->checkTpl($value,"hostgroup_name_tploptions","tbl_servicetemplate",$intDataId,$intSkip);
			if ($key == "servicegroups")  	$value = $this->checkTpl($value,"servicegroups_tploptions","tbl_servicetemplate",$intDataId,$intSkip);
			if ($key == "contacts")     	$value = $this->checkTpl($value,"contacts_tploptions","tbl_servicetemplate",$intDataId,$intSkip);
			if ($key == "contact_groups") 	$value = $this->checkTpl($value,"contact_groups_tploptions","tbl_servicetemplate",$intDataId,$intSkip);
			if ($key == "use")        		$value = $this->checkTpl($value,"use_template_tploptions","tbl_servicetemplate",$intDataId,$intSkip);
		}
		if ($strTableName == "tbl_contact") {
			if ($key == "use_template")   	$key = "use";
			$strVIValues  = "host_notifications_enabled,service_notifications_enabled,can_submit_commands,retain_status_information,";
			$strVIValues  = "retain_nonstatus_information";             
			if (in_array($key,explode(",",$strVIValues))) {
				if ($value == -1)         	$value = "null";
				if ($value == 3)        	$value = "null";
			}
			if ($key == "contactgroups")  	$value = $this->checkTpl($value,"contactgroups_tploptions","tbl_contact",$intDataId,$intSkip);
			if ($key == "host_notification_commands") {  	
											$value = $this->checkTpl($value,"host_notification_commands_tploptions","tbl_contact",$intDataId,$intSkip);}
			if ($key == "service_notification_commands") {  	
											$value = $this->checkTpl($value,"service_notification_commands_tploptions","tbl_contact",$intDataId,$intSkip);}
			if ($key == "use")        		$value = $this->checkTpl($value,"use_template_tploptions","tbl_contact",$intDataId,$intSkip);
		}
		if ($strTableName == "tbl_contacttemplate") {
			if ($key == "template_name")  	$key = "name";
			if ($key == "use_template")   	$key = "use";
			$strVIValues  = "host_notifications_enabled,service_notifications_enabled,can_submit_commands,retain_status_information,";
			$strVIValues  = "retain_nonstatus_information";
			if (in_array($key,explode(",",$strVIValues))) {
				if ($value == -1)         	$value = "null";
				if ($value == 3)        	$value = "null";
			}
			if ($key == "contactgroups")  	$value = $this->checkTpl($value,"contactgroups_tploptions","tbl_contacttemplate",$intDataId,$intSkip);
			if ($key == "host_notification_commands") {
											$value = $this->checkTpl($value,"host_notification_commands_tploptions","tbl_contacttemplate",$intDataId,$intSkip);}
			if ($key == "service_notification_commands") {
											$value = $this->checkTpl($value,"service_notification_commands_tploptions","tbl_contacttemplate",$intDataId,$intSkip);}
			if ($key == "use")        		$value = $this->checkTpl($value,"use_template_tploptions","tbl_contacttemplate",$intDataId,$intSkip);
		}
		if (($strTableName == "tbl_hosttemplate") || ($strTableName == "tbl_servicetemplate") || ($strTableName == "tbl_contacttemplate")) {
			if ($key == "register")  		$value = "0";
		}
		if ($strTableName == "tbl_timeperiod") {
		  	if ($key == "use_template")   	$key = "use";
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Help function: Skip database values
  	///////////////////////////////////////////////////////////////////////////////////////////
	//  $strTableName		-> Table name
	//  $strVersionValue	-> NagiosQL version value 
	//  $key				-> Data key
	//  $value				-> Data value
	///////////////////////////////////////////////////////////////////////////////////////////
	function skipEntries($strTableName,$strVersionValue,$key,$value) {
		// Common fields
		$strSpecial = "id,active,config_name,last_modified,access_rights,access_group,config_id,template,nodelete,command_type,import_hash";
		// Fields for special tables
		if ($strTableName == "tbl_hosttemplate")  		$strSpecial .= ",parents_tploptions,hostgroups_tploptions,contacts_tploptions".
																   	   ",contact_groups_tploptions,use_template_tploptions";
		if ($strTableName == "tbl_servicetemplate") 	$strSpecial .= ",host_name_tploptions,hostgroup_name_tploptions,contacts_tploptions".
																   	   ",servicegroups_tploptions,contact_groups_tploptions,use_template_tploptions";
		if ($strTableName == "tbl_contact") 			$strSpecial .= ",use_template_tploptions,contactgroups_tploptions".
																   	   ",host_notification_commands_tploptions,service_notification_commands_tploptions";
		if ($strTableName == "tbl_contacttemplate") 	$strSpecial .= ",use_template_tploptions,contactgroups_tploptions".
																   	   ",host_notification_commands_tploptions,service_notification_commands_tploptions";
        if ($strTableName == "tbl_host") 				$strSpecial .= ",parents_tploptions,hostgroups_tploptions,contacts_tploptions".
																	   ",contact_groups_tploptions,use_template_tploptions";
        if ($strTableName == "tbl_service") 			$strSpecial .= ",host_name_tploptions,hostgroup_name_tploptions,servicegroups_tploptions".
																	   ",contacts_tploptions,contact_groups_tploptions,use_template_tploptions";
																   
		// Pass special fields based on nagios version
		if ($strVersionValue != 3) {
			// Timeperiod
			if ($strTableName == "tbl_timeperiod") 		$strSpecial .= ",use,exclude,name";
			// Contact
			if ($strTableName == "tbl_contact") 		$strSpecial .= ",host_notifications_enabled,service_notifications_enabled,can_submit_commands,".
																	   "retain_status_information,retain_nonstatus_information";
			// Contacttemplate
			if ($strTableName == "tbl_contacttemplate") $strSpecial .= ",host_notifications_enabled,service_notifications_enabled,can_submit_commands,".
																	   "retain_status_information,retain_nonstatus_information";
			// Contactgroup
			if ($strTableName == "tbl_contactgroup") 	$strSpecial .= ",contactgroup_members";
			// Hostgroup
			if ($strTableName == "tbl_hostgroup") 		$strSpecial .= ",hostgroup_members,notes,notes_url,action_url";
			// Servicegroup
			if ($strTableName == "tbl_sevicegroup") 	$strSpecial .= ",servicegroup_members,notes,notes_url,action_url";
			// Hostdependencies
			if ($strTableName == "tbl_hostdependency") 	$strSpecial .= ",dependent_hostgroup_name,hostgroup_name,dependency_period";
		}
		if ($strVersionValue == 3) {
			// Servicetemplate
			if ($strTableName == "tbl_servicetemplate") $strSpecial .= ",parallelize_check ";
			// Service
			if ($strTableName == "tbl_service") 		$strSpecial .= ",parallelize_check";
		}
		if ($strVersionValue == 1) {
			$strSpecial .= "";
		}
		$arrSpecial = explode(",",$strSpecial);
		if (($value == "") || (in_array($key,$arrSpecial))) return(1);

		// Do not write config data (based on 'skip' option)
		$strNoTwo  = "active_checks_enabled,passive_checks_enabled,obsess_over_host,check_freshness,event_handler_enabled,flap_detection_enabled,";
		$strNoTwo .= "process_perf_data,retain_status_information,retain_nonstatus_information,notifications_enabled,parallelize_check,is_volatile,";
		$strNoTwo .= "host_notifications_enabled,service_notifications_enabled,can_submit_commands,obsess_over_service";
		$booTest = 0;
		foreach(explode(",",$strNoTwo) AS $elem){
			if (($key == $elem) && ($value == "2")) $booTest = 1;
		}
		if ($booTest == 1) return(1);
		return(0);
	}
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Help function: Open an SSH connection
  	///////////////////////////////////////////////////////////////////////////////////////////
	function getSSHConnection() {
		// SSH Possible
		if (!function_exists('ssh2_connect')) {
			$this->strDBMessage = translate('SSH module not loaded!');
			return(1);
		}
		// Set up basic connection
		$this->getConfigData("server",$strServer);
		$this->resConnectId = ssh2_connect($strServer);
		// Check connection
		if (!$this->resConnectId) {
			$this->myDataClass->writeLog(translate('Connection to remote system failed (SSH2 connection):')." ".$strServer);
			$this->strDBMessage = translate('Connection to remote system failed (SSH2 connection)!');
			return(1);
		}
		// Login with username and password
		$this->getConfigData("user",$strUser);
		$this->getConfigData("password",$strPasswd);
		$this->getConfigData("ssh_key_path",$strSSHKeyPath);
		if ($strSSHKeyPath != "") {
			$strPublicKey = str_replace("//","/",$strSSHKeyPath."/id_rsa.pub");
			$strPrivatKey = str_replace("//","/",$strSSHKeyPath."/id_rsa");
			// Check if ssh key file are readable
			if (!file_exists($strPublicKey) || !is_readable($strPublicKey)) {
				$this->myDataClass->writeLog(translate('SSH public key does not exist or is not readable')." ".$strPublicKey);
				$this->strDBMessage = translate('SSH public key does not exist or is not readable')." ".$strPublicKey;
				return(1);
			}
			if (!file_exists($strPrivatKey) || !is_readable($strPrivatKey)) {
				$this->myDataClass->writeLog(translate('SSH private key does not exist or is not readable')." ".$strPrivatKey);
				$this->strDBMessage = translate('SSH private key does not exist or is not readable')." ".$strPrivatKey;
				return(1);
			}
			if ($strPasswd == "") {
				$login_result = ssh2_auth_pubkey_file($this->resConnectId, $strUser, $strSSHKeyPath."/id_rsa.pub", $strSSHKeyPath."/id_rsa");
			} else {
				$login_result = ssh2_auth_pubkey_file($this->resConnectId, $strUser, $strSSHKeyPath."/id_rsa.pub", $strSSHKeyPath."/id_rsa",$strPasswd);
			}
		} else {
			$login_result = ssh2_auth_password($this->resConnectId,$strUser,$strPasswd);
		}
		// Check connection
		if ((!$this->resConnectId) || (!$login_result)) {
			$this->myDataClass->writeLog(translate('Connection to remote system failed (SSH2 connection):')." ".$strServer);
			$this->strDBMessage = translate('Connection to remote system failed (SSH2 connection)!');
			return(1);
		}
		// Etablish an SFTP connection ressource
		$this->resSFTP = ssh2_sftp($this->resConnectId);
		return(0);
	}
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Help function: Sends a command via SSH and stores the result in an array
  	///////////////////////////////////////////////////////////////////////////////////////////
	//  $strCommand			-> Command
	//  $intLines			-> Read max output lines
	//  This functions returs a result array or false in case of error
	///////////////////////////////////////////////////////////////////////////////////////////
  	function sendSSHCommand($strCommand,$intLines=100) {
		$intCount1 = 0;
		$intCount2 = 0;
		$booResult = false;
		if (is_resource($this->resConnectId)) {
			$resStream = ssh2_exec($this->resConnectId, $strCommand.'; echo "__END__";');
			if ($resStream) {
				stream_set_blocking($resStream,1);
				stream_set_timeout($resStream,2);
				do {
					$strLine = stream_get_line($resStream,1024,"\n");
					if ($strLine == "") {
						$intCount1++;
					} else if (substr_count($strLine,"__END__") != 1) {
						$arrResult[] = $strLine;
						$booResult   = true;
					}
					$intCount2++;
					$arrStatus = stream_get_meta_data($resStream);
				} while ($resStream && !(feof($resStream)) && ($intCount1 <= 10) && ($intCount2 <= $intLines) && ($arrStatus['timed_out'] != true));
				fclose($resStream);
				if ($booResult) {
					if ($arrStatus['timed_out'] == true) {
						//echo "timed_out".var_dump($arrResult)."<br>";
					}
					return $arrResult;
				} else {
					return true;
				}
			}
		}
		return false;
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Help function: Open an FTP connection
  	///////////////////////////////////////////////////////////////////////////////////////////
	function getFTPConnection() {
        // Set up basic connection
        $this->getConfigData("server",$strServer);
        $this->resConnectId	= ftp_connect($strServer);
        // Login with username and password
        $this->getConfigData("user",$strUser);
        $this->getConfigData("password",$strPasswd);
        $login_result   = ftp_login($this->resConnectId, $strUser, $strPasswd);
        // Check connection
        if ((!$this->resConnectId) || (!$login_result)) {
			$this->myDataClass->writeLog(translate('Connection to remote system failed (FTP connection):')." ".$strFile);
			$this->strDBMessage = translate('Connection to remote system failed (FTP connection)!');
          	return(1);
        }
		return(0);
	}
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Help function: Get configuration parameters
  	///////////////////////////////////////////////////////////////////////////////////////////
    //  $strConfigItem		-> Configuration key
	//  $strValue			-> Configuration value (return value)
	///////////////////////////////////////////////////////////////////////////////////////////
  	function getConfigData($strConfigItem,&$strValue) {
    	$strSQL   = "SELECT `".$strConfigItem."` FROM `tbl_domain` WHERE `id` = ".$this->intDomainId;
    	$strValue = $this->myDBClass->getFieldData($strSQL);
    	if ($strValue != "" ) return(0);
    	return(1);
  	}
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Help function: Process special settings based on template option
  	///////////////////////////////////////////////////////////////////////////////////////////
    //  $strValue			-> Original data value
	//  $strKeyField		-> Template option field name
    //  $strTable			-> Table name
	//  $intId				-> Dataset ID
	//  $intSkip			-> Skip value 	(return value)
	//  This function returns the manipulated data value
	///////////////////////////////////////////////////////////////////////////////////////////
  	function checkTpl($strValue,$strKeyField,$strTable,$intId,&$intSkip) {
    	$strSQL   = "SELECT `".$strKeyField."` FROM `".$strTable."` WHERE `id` = $intId";
    	$intValue = $this->myDBClass->getFieldData($strSQL);
    	if ($intValue == 0) return("+".$strValue);
    	if ($intValue == 1) {
      		$intSkip = 0;
      		return("null");
    	}
    	return($strValue);
  	}
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Help function: Check directory for write access
  	///////////////////////////////////////////////////////////////////////////////////////////
    //  $path				-> Physical path
	//  This function returns true if writeable or false if not
	//  This is a 3rd party function and not written by the NagiosQL developper team
	///////////////////////////////////////////////////////////////////////////////////////////
	function dir_is_writable($path) {
		if ($path == "") return false;
		//will work in despite of Windows ACLs bug
		//NOTE: use a trailing slash for folders!!!
		//see http://bugs.php.net/bug.php?id=27609
		//see http://bugs.php.net/bug.php?id=30931
		if ($path{strlen($path)-1}=='/') // recursively return a temporary file path
			return $this->dir_is_writable($path.uniqid(mt_rand()).'.tmp');
		else if (is_dir($path))
			return $this->dir_is_writable($path.'/'.uniqid(mt_rand()).'.tmp');
		// check tmp file for read/write capabilities
		$rm = file_exists($path);
		$f = @fopen($path, 'a');
		if ($f===false)
			return false;
		fclose($f);
		if (!$rm)
			unlink($path);
		return true;
	}
}
?>