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
// Component : Import Class
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-03-18 14:18:04 +0100 (Fr, 18. Mär 2011) $
// Author    : $LastChangedBy: martin $
// Version   : 3.1.1
// Revision  : $LastChangedRevision: 1066 $
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Class: Data import class
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Includes any functions to import data from config files
//
// Name: nagimport
//
// Class variables:
// $arrSettings  		Includes all global settings ($SETS)
// $intDomainId			Domain ID
// $myDBClass     		MySQL database class object
// $myDataClass  		Data manipulation class
// $myConfigClass		Configure class
// $strDBMessage		Process messages
// $strMessage    		Import messages
//
///////////////////////////////////////////////////////////////////////////////////////////////
class nagimport {
  	// Define class variables
    var $arrSettings;       // Will be filled in class constructor
  	var $intDomainId  = 0;  // Will be filled in class constructor
  	var $myDBClass;         // Will be filled in prepend_adm.php
  	var $myDataClass;       // Will be filled in prepend_adm.php
  	var $myConfigClass;		// Will be filled in prepend_adm.php
    var $strDBMessage = ""; // Will be filled in functions
  	var $strMessage   = ""; // Will be filled in functions	
  
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Class constructor
  	///////////////////////////////////////////////////////////////////////////////////////////
  	//
  	//  Activities during initialisation
  	//
  	///////////////////////////////////////////////////////////////////////////////////////////
	function nagimport() {
    	// Read global settings
		$this->arrSettings = $_SESSION['SETS'];
		if (isset($_SESSION['domain'])) $this->intDomainId = $_SESSION['domain'];
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Data import
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Import a config file and writes the values to the database
	//
  	//  Parameters:  		$strFileName    	Import file name
	//						$intOverwrite   	0 = Do not replace existing data
	//                      					1 = Replace existing data in tables
	//
  	//  Return value:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function fileImport($strFileName,$intOverwrite=0) {
  		// Define variables
    	$intBlock     	= 0;
    	$intCheck     	= 0;
    	$intRemoveTmp   = 0;
    	$strFileName    = trim($strFileName);
    	$this->myConfigClass->getConfigData("method",$intMethod);
    	// Get inport file
		// Local file system
		if ($intMethod == 1) {
			if (!is_readable($strFileName)) {
				$this->strMessage .= translate('Cannot open the data file (check the permissions)!')." ".$strFileName."<br>";
				return(1);
      		}
		// FTP access
    	} else if ($intMethod == 2) {
			// Open ftp connection
			$this->myConfigClass->getFTPConnection();
			// Transfer file from remote server to a local temp file
			$strConfigFile = tempnam(sys_get_temp_dir(), 'nagiosql_imp');	
			if (!ftp_get($this->myConfigClass->resConnectId,$strConfigFile,$strFileName,FTP_ASCII)) {
				$this->strMessage = translate('Cannot receive the configuration file (FTP connection)!');
          		ftp_close($conn_id);
          		return(1);
        	}
			$intRemoveTmp   = 1;
			$strFileName  	= $strConfigFile;
		// SSH Access
		} else if ($intMethod == 3) {
			// Open ftp connection
			$this->myConfigClass->getSSHConnection();
			// Transfer file from remote server to a local temp file
			$strConfigFile = tempnam(sys_get_temp_dir(), 'nagiosql_imp');
  			if (!ssh2_scp_recv($this->myConfigClass->resConnectId,$strFileName,$strConfigFile)) {
				$this->strMessage = translate('Cannot receive the configuration file (SSH connection)!');
				return(1);
			}
			$intRemoveTmp   = 1;
			$strFileName  	= $strConfigFile;
		}
    	// Open and read config file
		if (file_exists($strFileName) && is_readable($strFileName)) {
			$resFile     = fopen($strFileName,"r");
			$intMultiple = 0;
			while($resFile && !feof($resFile)) {
				$strConfLine = fgets($resFile);
				// Remove blank chars
		  		$strConfLine = trim($strConfLine);
				// Process multi-line configuration instructions 
				if (substr($strConfLine,-1) == '\\') {
					if ($intMultiple == 0) {
						$strConfLineTemp = str_replace("\\",",",$strConfLine);
						$intMultiple     = 1;
					} else {
						$strConfLineTemp .= str_replace("\\",",",$strConfLine); 
					}
					continue;
				}
				if ($intMultiple == 1) {
					$strConfLine = $strConfLineTemp.$strConfLine;
					$intMultiple = 0;
		  		}
		  		// Pass comments and empty lines
		  		if (substr($strConfLine,0,1) == "#") continue;
		  		if ($strConfLine == "") continue;
		  		if (($intBlock == 1) && ($strConfLine == "{")) continue;
		  		// Process line (remove blanks and cut comments)
		  		$arrLine    = preg_split("/[\s]+/", $strConfLine);
		  		$arrTemp    = explode(";",implode(" ",$arrLine));
		  		$strNewLine = trim($arrTemp[0]);
		  		// Find block begin
				if ($arrLine[0] == "define") {
					$intBlock 		= 1;
					$strBlockKey 	= str_replace("{","",$arrLine[1]);
					$arrData 		= "";
					continue;
				}
		  		// Store the block data to an array
				if (($intBlock == 1) && ($arrLine[0] != "}")) {
					$strExclude = "template_name,alias,name,use,register";
					if (($strBlockKey == "timeperiod") && (!in_array($arrLine[0],explode(",",$strExclude)))) {
  						$arrNewLine = explode(" ",$strNewLine);
						$strTPKey   = str_replace(" ".$arrNewLine[count($arrNewLine)-1],"",$strNewLine);
						$strTPValue = $arrNewLine[count($arrNewLine)-1];
  						$arrData[$strTPKey] = array("key" => $strTPKey, 
											 	    "value" => $strTPValue);
					} else {
  						$key   = $arrLine[0];
  						$value = str_replace($arrLine[0]." ","",$strNewLine);
  						// Special retry_check_interval, normal_check_interval
  						if ($key == "retry_check_interval")  $key = "retry_interval";
  						if ($key == "normal_check_interval") $key = "check_interval";
  						$arrData[$arrLine[0]] = array("key" => $key, "value" => $value);
					}
				}
				// Process data at end of block
				if ((substr_count($strConfLine,"}") == 1) && (isset($arrData)) && (is_array($arrData)))  {
					$intBlock 	= 0;
					$intReturn 	= $this->importTable($strBlockKey,$arrData,$intOverwrite,$strFileName);
				} else if (!isset($arrData)) {
					$this->strMessage = translate('No valid configuration found!');
					return(1);
				}
			}
			if ($intRemoveTmp == 1) {
		  		unlink($strFileName);
			}
		}
    	return($intCheck);
  	}

	///////////////////////////////////////////////////////////////////////////////////////////
	//  Help function: Import table
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Writes the block data to the database
	//
  	//  Parameters:  		$strBlockKey      	Config key (from define)
	//						$arrImportData		Imported block data
	//						$strFileName		Name of config file
	//						$intOverwrite   	0 = Do not replace existing data
	//                      					1 = Replace existing data in tables
	//
  	//  Return value:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function importTable($strBlockKey,$arrImportData,$intOverwrite,$strFileName) {
  		// Define variables
		$intExists      	= 0;
		$intInsertRelations = 0;
		$intInsertVariables = 0;
		$intIsTemplate    	= 0;
		$strVCValues    	= "";
		$strRLValues    	= "";
		$strVWValues    	= "";
		$strVIValues     	= "";
		$intWriteConfig   	= 0;
		$strWhere     		= "";
		$this->strList1   	= "";
		$this->strList2   	= "";
		// Block data from template or real configuration?
    	if (array_key_exists("name",$arrImportData) && (isset($arrImportData['register']) && ($arrImportData['register']['value'] == 0))) {
      		$intIsTemplate = 1;
    	}
    	// Define table name
    	if ($intIsTemplate == 0) {
      		switch($strBlockKey) {
				case "command":       		$strTable = "tbl_command";       		$strKeyField = "command_name";     	break;
				case "contactgroup":    	$strTable = "tbl_contactgroup";    		$strKeyField = "contactgroup_name"; break;
				case "contact":       		$strTable = "tbl_contact";       		$strKeyField = "contact_name";    	break;
				case "timeperiod":      	$strTable = "tbl_timeperiod";      		$strKeyField = "timeperiod_name";   break;
				case "host":        		$strTable = "tbl_host";        			$strKeyField = "host_name";      	break;
				case "service":       		$strTable = "tbl_service";       		$strKeyField = "";           		break;
				case "hostgroup":     		$strTable = "tbl_hostgroup";     		$strKeyField = "hostgroup_name";    break;
				case "servicegroup":    	$strTable = "tbl_servicegroup";    		$strKeyField = "servicegroup_name";	break;
				case "hostescalation":    	$strTable = "tbl_hostescalation";    	$strKeyField = "";           		break;
				case "serviceescalation": 	$strTable = "tbl_serviceescalation"; 	$strKeyField = "";           		break;
				case "hostdependency":    	$strTable = "tbl_hostdependency";    	$strKeyField = "";           		break;
				case "servicedependency": 	$strTable = "tbl_servicedependency"; 	$strKeyField = "";           		break;
				case "hostextinfo":     	$strTable = "tbl_hostextinfo";       	$strKeyField = "host_name";      	break;
				case "serviceextinfo":    	$strTable = "tbl_serviceextinfo";    	$strKeyField = "";           		break;
			}
		} else {
			switch($strBlockKey) {
				case "contact":       		$strTable = "tbl_contacttemplate";   	$strKeyField = "name";     			break;
				case "host":        		$strTable = "tbl_hosttemplate";    		$strKeyField = "name";     			break;
				case "service":       		$strTable = "tbl_servicetemplate";   	$strKeyField = "name";     			break;
			}
    	}
		if (!isset($strTable) || ($strTable == "")) {
			$this->strDBMessage = translate('Table for import definition').$strBlockKey.translate('is not available!');
			return(1);			
		}
    	// Create an import hash if no key field is available
		if ($strKeyField == "") { 
			$this->createHash($strTable,$arrImportData,$strHash,$strConfigName);
			$arrImportData['config_name']['key']  	= "config_name";
      		$arrImportData['config_name']['value']  = $strConfigName;
			$strKeyField = "config_name";
		} else {
			$strHash = "";
		}
		// Get relation data
		$intRelation = $this->myDataClass->tableRelations($strTable,$arrRelations);
    	// Does this entry already exist?
		if ($intIsTemplate == 0) {
			if (($strKeyField != "") && isset($arrImportData[$strKeyField])) {
				if ($strHash == "") {
					$strSQL = "SELECT `id` FROM `".$strTable."` 
							   WHERE `config_id`=".$this->intDomainId." AND `".$strKeyField."`='".$arrImportData[$strKeyField]['value']."'";
				} else {
					$strSQL = "SELECT `id` FROM `".$strTable."` 
							   WHERE `config_id`=".$this->intDomainId." AND `import_hash`='".$strHash."'";
				}
				$intExists = $this->myDBClass->getFieldData($strSQL);
				
        		if ($intExists == false) $intExists = 0;
      		}
    	} else {
			if (($strKeyField != "") && isset($arrImportData['name'])) {
				$strSQL = "SELECT `id` FROM `".$strTable."` 
						   WHERE `config_id`=".$this->intDomainId." AND `template_name`='".$arrImportData['name']['value']."'";
        		$intExists = $this->myDBClass->getFieldData($strSQL);
        		if ($intExists == false) $intExists = 0;
      		}
    	}
		// Entry exsists but should not be overwritten
    	if (($intExists != 0) && ($intOverwrite == 0)) {
			if ($strKeyField == 'config_name') {
				$strSQLConfig  = "SELECT `config_name` FROM `".$strTable."` WHERE `id`=".$intExists;
				$arrImportData[$strKeyField]['value'] = $this->myDBClass->getFieldData($strSQLConfig);
			}
      		$this->strMessage .= translate('Entry')." ".$strKeyField."::".$arrImportData[$strKeyField]['value']." ".translate('inside')." ".$strTable." ".translate('exists and were not overwritten')."<br>";
      		return(2);
    	}
		// Do not write "*" values
		if (isset($arrImportData[$strKeyField]) && ($arrImportData[$strKeyField] == "*")) {
      		$this->strMessage .= translate('Entry')." ".$strKeyField."::".$arrImportData[$strKeyField]['value']." ".translate('inside')." ".$strTable." ".translate('were not written')."<br>";
      		return(2);
    	}
		// Activate entry?
		if (isset($arrImportData['register']) && ($arrImportData['register']['value'] == 0) && ($intIsTemplate != 1)) {
		  	$intActive = 0;
		} else {
		  	$intActive = 1;
		}
		// Define SQL statement - part 1
    	if ($strHash == "") {$strHash = "";} else {$strHash = " `import_hash`='".$strHash."', ";}
		if ($intExists != 0) { 		
			// Update database
      		$strSQL1 = "UPDATE `".$strTable."` SET ";
      		$strSQL2 = "  `config_id`=".$this->intDomainId.", $strHash `active`='$intActive', `last_modified`=NOW() WHERE `id`=$intExists";
			// Keep config name while update
			if ($strKeyField == 'config_name') {
				$strSQLConfig  = "SELECT `config_name` FROM `".$strTable."` WHERE `id`=".$intExists;
				$arrImportData[$strKeyField]['value'] = $this->myDBClass->getFieldData($strSQLConfig);
			}
      		// Remove free variables
      		if ($intRelation != 0) {
        		foreach ($arrRelations AS $relVar) {
          			if ($relVar['type'] == 4) {
            			$strSQL   	= "SELECT * FROM `".$relVar['linkTable']."` WHERE `idMaster`=$intExists";
            			$booReturn  = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
						if ($booReturn && ($intDataCount != 0)) {
							foreach ($arrData AS $elem) {
								$strSQL   	= "DELETE FROM `tbl_variabledefinition` WHERE `id`=".$elem['idSlave'];
								$booReturn  = $this->myDataClass->dataInsert($strSQL,$intInsertId);
							}
						}
						$strSQL   	= "DELETE FROM `".$relVar['linkTable']."` WHERE `idMaster`=$intExists";
						$booReturn  = $this->myDataClass->dataInsert($strSQL,$intInsertId);
					}
				}
			}
    	} else {
      		// DB Eintrag einfügen
      		$strSQL1 = "INSERT INTO `".$strTable."` SET ";
      		$strSQL2 = "  `config_id`=".$this->intDomainId.", $strHash `active`='$intActive', `last_modified`=NOW()";
    	}

		// Description for the values
		// --------------------------
		// $strVCValues = Simple text values, will be stored as varchar / null = 'null' as text value / empty = ''
		// $strRLValues = Relations - values with relations to other tables
		// $strVWValues = Integer values - will be stored as INT values / null = -1, / empty values as NULL
		// $strVIValues = Decision values 0 = no, 1 = yes, 2 = skip, 3 = null

		// Read command configurations
		if ($strKeyField == "command_name") {
			$strVCValues = "command_name,command_line";
		  	// Find out command type
		  	if ((substr_count($arrImportData['command_line']['value'],"ARG1") != 0) ||
			  	(substr_count($arrImportData['command_line']['value'],"USER1") != 0)) {
		   		$strSQL1 .= "`command_type` = 1,";
		  	} else {
				$strSQL1 .= "`command_type` = 2,";
		  	}
		  	$intWriteConfig = 1;
		
    	// Read contact configurations
     	} else if ($strKeyField == "contact_name") {
			$strVCValues  	= "contact_name,alias,host_notification_options,service_notification_options,email,";
			$strVCValues   .= "pager,address1,address2,address3,address4,address5,address6,name";
			
			$strVIValues  	= "host_notifications_enabled,service_notifications_enabled,can_submit_commands,retain_status_information,";
			$strVIValues   .= "retain_nonstatus_information";
			
			$strRLValues  	= "contactgroups,host_notification_period,service_notification_period,host_notification_commands,";
			$strRLValues   .= "service_notification_commands,use";
			$intWriteConfig = 1;
    	
		// Read contactgroup configurations
		} else if ($strKeyField == "contactgroup_name") {
		  	$strVCValues  	= "contactgroup_name,alias";
		
		  	$strRLValues  	= "members,contactgroup_members";
		  	$intWriteConfig = 1;
		
    	// Read timeperiod configurations
     	} else if ($strKeyField == "timeperiod_name") {
			$strVCValues  	= "timeperiod_name,alias,name";
			
			$strRLValues  	= "use,exclude";
			$intWriteConfig = 1;
			
		// Read contacttemplate configurations
		} else if (($strKeyField == "name") && ($strTable == "tbl_contacttemplate")) {
		 	$strVCValues  	= "contact_name,alias,host_notification_options,service_notification_options,email,";
		  	$strVCValues   .= "pager,address1,address2,address3,address4,address5,address6,name";
		
		  	$strVIValues  	= "host_notifications_enabled,service_notifications_enabled,can_submit_commands,retain_status_information,";
		  	$strVIValues   .= "retain_nonstatus_information";
		
		  	$strRLValues  	= "contactgroups,host_notification_period,service_notification_period,host_notification_commands,";
		  	$strRLValues   .= "service_notification_commands,use";
		  	$intWriteConfig	= 1;
		
		// Read host configurations
		} else if ($strTable == "tbl_host") {
			$strVCValues  	= "host_name,alias,display_name,address,initial_state,flap_detection_options,notification_options,";
			$strVCValues   .= "stalking_options,notes,notes_url,action_url,icon_image,icon_image_alt,vrml_image,statusmap_image,";
			$strVCValues   .= "2d_coords,3d_coords,name";
			
			$strVWValues  	= "max_check_attempts,retry_interval,check_interval,freshness_threshold,low_flap_threshold,";
			$strVWValues   .= "high_flap_threshold,notification_interval,first_notification_delay,";
			
			$strVIValues  	= "active_checks_enabled,passive_checks_enabled,check_freshness,obsess_over_host,event_handler_enabled,";
			$strVIValues   .= "flap_detection_enabled,process_perf_data,retain_status_information,retain_nonstatus_information,";
			$strVIValues   .= "notifications_enabled";
			
			$strRLValues  	= "parents,hostgroups,check_command,use,check_period,event_handler,contacts,contact_groups,";
			$strRLValues   .= "notification_period";
			$intWriteConfig = 1;
		
		// Read hosttemplate configurations
		} else if (($strKeyField == "name") && ($strTable == "tbl_hosttemplate")) {
			$strVCValues  	= "template_name,alias,initial_state,flap_detection_options,notification_options,";
			$strVCValues   .= "stalking_options,notes,notes_url,action_url,icon_image,icon_image_alt,vrml_image,statusmap_image,";
			$strVCValues   .= "2d_coords,3d_coords,name";
			
			$strVWValues  	= "max_check_attempts,retry_interval,check_interval,freshness_threshold,low_flap_threshold,";
			$strVWValues   .= "high_flap_threshold,notification_interval,first_notification_delay,";
			
			$strVIValues  	= "active_checks_enabled,passive_checks_enabled,check_freshness,obsess_over_host,event_handler_enabled,";
			$strVIValues   .= "flap_detection_enabled,process_perf_data,retain_status_information,retain_nonstatus_information,";
			$strVIValues   .= "notifications_enabled";
			
			$strRLValues  	= "parents,hostgroups,check_command,use,check_period,event_handler,contacts,contact_groups,";
			$strRLValues   .= "notification_period";
			$intWriteConfig = 1;
		
		// Read hostgroup configurations
		} else if ($strKeyField == "hostgroup_name") {
			$strVCValues  	= "hostgroup_name,alias,notes,notes_url,action_url";
			
			$strRLValues  	= "members,hostgroup_members";
			$intWriteConfig = 1;
		
		// Read service configurations
		} else if ($strTable == "tbl_service") {
			$strVCValues  	= "service_description,display_name,initial_state,flap_detection_options,stalking_options,notes,notes_url,";
			$strVCValues   .= "action_url,icon_image,icon_image_alt,name,config_name,notification_options";
			
			$strVWValues  	= "max_check_attempts,check_interval,retry_interval,freshness_threshold,low_flap_threshold,";
			$strVWValues   .= "high_flap_threshold,notification_interval,first_notification_delay";
			
			$strVIValues  	= "is_volatile,active_checks_enabled,passive_checks_enabled,parallelize_check,obsess_over_service,";
			$strVIValues   .= "check_freshness,event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,";
			$strVIValues   .= "retain_nonstatus_information,notifications_enabled";
			
			$strRLValues  	= "host_name,hostgroup_name,servicegroups,use,check_command,check_period,event_handler,notification_period,contacts,contact_groups";
			$intWriteConfig = 1;
		
		// Read servicetemplate configurations
		} else if (($strKeyField == "name") && ($strTable == "tbl_servicetemplate")) {
			$strVCValues  	= "template_name,service_description,display_name,initial_state,flap_detection_options,stalking_options,notes,notes_url,";
			$strVCValues   .= "action_url,icon_image,icon_image_alt,name,notification_options";
			
			$strVWValues  	= "max_check_attempts,check_interval,retry_interval,freshness_threshold,low_flap_threshold,";
			$strVWValues   .= "high_flap_threshold,notification_interval,first_notification_delay";
			
			$strVIValues  	= "is_volatile,active_checks_enabled,passive_checks_enabled,parallelize_check,obsess_over_service,";
			$strVIValues   .= "check_freshness,event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,";
			$strVIValues   .= "retain_nonstatus_information,notifications_enabled";
			
			$strRLValues  	= "host_name,hostgroup_name,servicegroups,use,check_command,check_period,event_handler,notification_period,contacts,contact_groups";
			$intWriteConfig = 1;
		
		// Read servicegroup configurations
		} else if ($strKeyField == "servicegroup_name") {
			$strVCValues  	= "servicegroup_name,alias,notes,notes_url,action_url";
			
			$strRLValues  	= "members,servicegroup_members";
			$intWriteConfig = 1;
		
		// Read hostdependency configurations
		} else if ($strTable == "tbl_hostdependency") {
			$strVCValues  	= "config_name,execution_failure_criteria,notification_failure_criteria";
			
			$strVIValues  	= "inherits_parent";
			
			$strRLValues  	= "dependent_host_name,dependent_hostgroup_name,host_name,hostgroup_name,dependency_period";
			$intWriteConfig = 1;
		
		
		// Read hostescalation configurations
		} else if ($strTable == "tbl_hostescalation") {
			$strVCValues  	= "config_name,escalation_options";
			
			$strVWValues  	= "first_notification,last_notification,notification_interval";
			
			$strRLValues  	= "host_name,hostgroup_name,contacts,contact_groups,escalation_period";
			$intWriteConfig = 1;
		
		// Read hostextinfo configurations
		} else if ($strTable == "tbl_hostextinfo") {
			$strVCValues  	= "notes,notes_url,action_url,icon_image,icon_image_alt,vrml_image,statusmap_image,2d_coords,3d_coords";
			
			$strRLValues  	= "host_name";
			$intWriteConfig = 1;
		
		// Read hostdependency configurations
		} else if ($strTable == "tbl_servicedependency") {
			$strVCValues  	= "config_name,execution_failure_criteria,notification_failure_criteria";
			
			$strVIValues  	= "inherits_parent";
			
			$strRLValues  	= "dependent_host_name,dependent_hostgroup_name,dependent_service_description,host_name,";
			$strRLValues   .= "hostgroup_name,dependency_period,service_description";
			$intWriteConfig = 1;
		
		// Read serviceescalation configurations
		} else if ($strTable == "tbl_serviceescalation") {
			$strVCValues  	= "config_name,escalation_options";
			
			$strVWValues  	= "first_notification,last_notification,notification_interval";
			
			$strRLValues  	= "host_name,hostgroup_name,contacts,contact_groups,service_description,escalation_period";
			$intWriteConfig = 1;
		
		// Serviceextinfo configurations
		} else if ($strTable == "tbl_serviceextinfo") {
			$strVCValues  	= "notes,notes_url,action_url,icon_image,icon_image_alt";
			
			$strRLValues  	= "host_name,service_description";
			$intWriteConfig = 1;
		}
		
    	// Build value statemets
		foreach ($arrImportData AS $elem) {
			// Decompose command
      		if ($elem['key'] == "check_command") {
        		$arrValues = explode("!",$elem['value']);
      		}
      		$intCheck = 0;
      		// Write text values
      		if (in_array($elem['key'],explode(",",$strVCValues))) {
        		if (strtolower(trim($elem['value'])) == "null") {
          			$strSQL1 .= "`".$elem['key']."` = 'null',";
        		} else {
          			$elem['value']  = addslashes($elem['value']);
          			if ($intIsTemplate == 1) {
            			if ($elem['key'] == "name") {
              				$strSQL1 .= "template_name = '".$elem['value']."',";
						} else if (($elem['key'] == "config_name") && ($intExists != 0)) {
							// Do not overwrite config_names during an update! 
							$strSQLConfig  = "SELECT `config_name` FROM `".$strTable."` WHERE `id`=".$intExists;
							$elem['value'] = $this->myDBClass->getFieldData($strSQLConfig);
							$strSQL1 .= "`".$elem['key']."` = '".$elem['value']."',";
            			} else {
              				$strSQL1 .= "`".$elem['key']."` = '".$elem['value']."',";
            			}
          			} else {
            			$strSQL1 .= "`".$elem['key']."` = '".$elem['value']."',";
          			}
        		}
        		$intCheck = 1;
      		}
      		// Write status values
      		if (in_array($elem['key'],explode(",",$strVIValues))) {
        		if (strtolower(trim($elem['value'])) == "null") {
          			$strSQL1 .= "`".$elem['key']."` = 3,";
        		} else {
          			$strSQL1 .= "`".$elem['key']."` = '".$elem['value']."',";
        		}
        		$intCheck = 1;
      		}
      		// Write integer values
      		if (in_array($elem['key'],explode(",",$strVWValues))) {
        		if (strtolower(trim($elem['value'])) == "null") {
          			$strSQL1 .= "`".$elem['key']."` = -1,";
        		} else {
          			$strSQL1 .= "`".$elem['key']."` = '".$elem['value']."',";
        		}
        		$intCheck = 1;
      		}
      		// Write relations
      		if (($intCheck == 0) && (in_array($elem['key'],explode(",",$strRLValues)))) {
        		if ($elem['key'] == "use") $elem['key'] = "use_template";
        		$arrTemp        		= "";
        		$arrTemp['key']     	= $elem['key'];
        		$arrTemp['value']     	= $elem['value'];
        		$arrImportRelations[]   = $arrTemp;
        		$intInsertRelations   	= 1;
        		$intCheck         		= 1;
      		}
      		// Write free variables
      		if ($intCheck == 0) {
        		$strSkip = "register";
        		if (!in_array($elem['key'],explode(",",$strSkip))) {
					$arrTemp      		= "";
					$arrTemp['key']   	= $elem['key'];
					$arrTemp['value']   = $elem['value'];
					$arrFreeVariables[] = $arrTemp;
					$intInsertVariables = 1;
        		}
      		}
    	}
    	$strTemp1 = "";
    	$strTemp2 = "";
    	// Update database
		if ($intWriteConfig == 1) {
			$booResult = $this->myDBClass->insertData($strSQL1.$strSQL2);
		} else {
			$booResult = false;
		}
    	if ($strKeyField == "") {$strKey = $strConfigName;} else {$strKey = $strKeyField;}
    	if ($booResult != true) {
      		$this->strDBMessage = $this->myDBClass->strDBError;
      		if ($strKeyField != "") $this->strMessage .= translate('Entry')." ".$strKey."::".$arrImportData[$strKeyField]['value']." ".translate('inside')." ".$strTable." ".translate('could not be inserted:')." ".mysql_error()."<br>";
      		if ($strKeyField == "") $this->strMessage .= translate('Entry')." ".$strTemp1."::".$strTemp2.translate('inside')." ".$strTable." ".$strTable." ".translate('could not be inserted:')." ".mysql_error()."<br>";
      		return(1);
    	} else {
      		if ($strKeyField != "") $this->strMessage .= "<span class=\"greenmessage\">".translate('Entry')." ".$strKey."::".$arrImportData[$strKeyField]['value']." ".translate('inside')." ".$strTable." ".translate('successfully inserted')."</span><br>";
      		if ($strKeyField == "") $this->strMessage .= "<span class=\"greenmessage\">".translate('Entry')." ".$strTemp1."::".$strTemp2." ".translate('inside')." ".$strTable." ".translate('successfully inserted')."</span><br>";
      		// Define data ID
      		if ($intExists != 0) {
        		$intDatasetId = $intExists;
      		} else {
        		$intDatasetId = $this->myDBClass->intLastId;
      		}
      		// Are there any relations to be filled in?
      		if ($intInsertRelations == 1) {
        		foreach ($arrImportRelations AS $elem) {
          			foreach ($arrRelations AS $reldata) {
            			if ($reldata['fieldName'] == $elem['key']) {
              				if ($elem['key'] == "check_command") {
								$this->writeRelation_5($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata);
							} else if ($reldata['type'] == 1) {
								$this->writeRelation_1($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata,$arrImportData);
							} else if ($reldata['type'] == 2) {
								$this->writeRelation_2($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata);
							} else if ($reldata['type'] == 3) {
								$this->writeRelation_3($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata);
							} else if ($reldata['type'] == 4) {
								$this->writeRelation_4($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata);
							} else if ($reldata['type'] == 5) {
								$this->writeRelation_6($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata);
							} else if ($reldata['type'] == 6) {
								$this->writeRelation_7($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata);
							}
            			}
          			}
        		}
      		}
      		// Are there any free variables ore time definitions to be filled in?
      		if ($intInsertVariables == 1) {
        		if ($strTable == "tbl_timeperiod") {
          			// Remove old values
          			$strSQL   = "DELETE FROM `tbl_timedefinition` WHERE `tipId` = $intDatasetId";
          			$booResult  = $this->myDBClass->insertData($strSQL);
					foreach ($arrFreeVariables AS $elem) {
            			$strSQL 	= "INSERT INTO `tbl_timedefinition` SET `tipId` = $intDatasetId,
                 					   `definition` = '".addslashes($elem['key'])."', `range` = '".addslashes($elem['value'])."'";
           				$booResult  = $this->myDBClass->insertData($strSQL);
          			}
        		} else {
          			foreach ($arrFreeVariables AS $elem) {
						foreach ($arrRelations AS $reldata) {
							if ($reldata['type'] == 4) {
            					$this->writeRelation_4($elem['key'],$elem['value'],$intDatasetId,$strTable,$reldata);
							}
						}
          			}
        		}
      		}
      		return(0);
    	}
  	}
  
  /// 3.1 BETA start
  
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Help function: Create HASH
	///////////////////////////////////////////////////////////////////////////////////////////
	function createHash($strTable,$arrBlockData,&$strHash,&$strConfigName) {
		$strRawString  	= "";
		$strConfigName  = "imp_temporary";
		if ($strTable == "tbl_service") {
			// HASH from any host, any hostgroup and service description - step 1
			if (isset($arrBlockData['host_name'])) 				$strRawString .= $arrBlockData['host_name']['value'].",";
			if (isset($arrBlockData['hostgroup_name'])) 		$strRawString .= $arrBlockData['hostgroup_name']['value'].",";	
			// Replace *, + and ! in HASH raw string
			$strRawString = str_replace("*,","any,",$strRawString);
			$strRawString = str_replace("!","not_",$strRawString);
			$strRawString = str_replace("+","",$strRawString);
			// Create configuration name from first two hosts / hostgroups
			$arrConfig    = explode(",",$strRawString);
			if (isset($arrConfig[0]) && ($arrConfig[0] != "")) $strConfigName  = "imp_".$arrConfig[0];
			if (isset($arrConfig[1]) && ($arrConfig[1] != "")) $strConfigName .= "_".$arrConfig[1];
			// HASH from any host, any hostgroup and service description - step 2
			if (isset($arrBlockData['service_description'])) 	$strRawString .= $arrBlockData['service_description']['value'].",";
			if (isset($arrBlockData['display_name'])) 			$strRawString .= $arrBlockData['display_name']['value'].",";
			if (isset($arrBlockData['check_command'])) 			$strRawString .= $arrBlockData['check_command']['value'].",";
		}
		if (($strTable == "tbl_hostdependency") || ($strTable == "tbl_servicedependency")) {
			$strRawString1 = "";
			$strRawString2 = "";
			$strRawString3 = "";
			// HASH from any dependent host and any dependent hostgroup
			if (isset($arrBlockData['dependent_host_name'])) 			$strRawString1 .= $arrBlockData['dependent_host_name']['value'].",";
			if (isset($arrBlockData['dependent_hostgroup_name'])) 		$strRawString1 .= $arrBlockData['dependent_hostgroup_name']['value'].",";
			if (isset($arrBlockData['host_name'])) 						$strRawString2 .= $arrBlockData['host_name']['value'].",";
			if (isset($arrBlockData['hostgroup_name'])) 				$strRawString2 .= $arrBlockData['hostgroup_name']['value'].",";
			if (isset($arrBlockData['dependent_service_description'])) 	$strRawString3 .= $arrBlockData['dependent_service_description']['value'].",";
			if (isset($arrBlockData['service_description'])) 			$strRawString3 .= $arrBlockData['service_description']['value'].",";
			// Replace *, + and ! in HASH raw string
			$strRawString1 = str_replace("*,","any,",$strRawString1);
			$strRawString2 = str_replace("*,","any,",$strRawString2);
			$strRawString3 = str_replace("*,","any,",$strRawString3);
			$strRawString1 = str_replace("!","not_",$strRawString1);
			$strRawString2 = str_replace("!","not_",$strRawString2);
			$strRawString3 = str_replace("!","not_",$strRawString3);
			$arrConfig1    = explode(",",$strRawString1);
			$arrConfig2    = explode(",",$strRawString2);
			$arrConfig3    = explode(",",$strRawString3);
			if (isset($arrConfig1[0])) $strConfigName  = "imp_".$arrConfig1[0];
			if (isset($arrConfig2[0])) $strConfigName .= "_".$arrConfig2[0]; 
			if (isset($arrConfig3[0])) $strConfigName .= "_".$arrConfig3[0]; 
			$strSQL = "SELECT * FROM `".$strTable."` WHERE `config_name`='$strConfigName'";
			$booRet	= $this->myDBClass->getDataArray($strSQL,$arrData,$intDC);
			if ($booRet && ($intDC != 0)) {
				$intCounter = 1;
				do {	
					$strConfigNameTemp = $strConfigName."_".$intCounter;
					$strSQL = "SELECT * FROM `".$strTable."` WHERE `config_name`='$strConfigNameTemp'";
					$booRet	= $this->myDBClass->getDataArray($strSQL,$arrData,$intDC);
					$intCounter++;
				} while ($booRet && ($intDC != 0));
				$strConfigName = $strConfigNameTemp;
			}
			$strRawString = $strRawString1.$strRawString2.$strRawString3;
			$strRawString = substr($strRawString,0,-1);
		}
		if (($strTable == "tbl_hostescalation") || ($strTable == "tbl_serviceescalation")) {
			$strRawString1 = "";
			$strRawString2 = "";
			$strRawString3 = "";
			// HASH from any host and any hostgroup
			if (isset($arrBlockData['host_name'])) 				$strRawString1 .= $arrBlockData['host_name']['value'].",";
			if (isset($arrBlockData['hostgroup_name'])) 		$strRawString1 .= $arrBlockData['hostgroup_name']['value'].",";	
			if (isset($arrBlockData['contacts'])) 				$strRawString2 .= $arrBlockData['contacts']['value'].",";
			if (isset($arrBlockData['contact_groups'])) 		$strRawString2 .= $arrBlockData['contact_groups']['value'].",";
			if (isset($arrBlockData['service_description'])) 	$strRawString3 .= $arrBlockData['service_description']['value'].",";
			// Replace *, + and ! in HASH raw string
			$strRawString1 = str_replace("*,","any,",$strRawString1);
			$strRawString2 = str_replace("*,","any,",$strRawString2);
			$strRawString3 = str_replace("*,","any,",$strRawString3);
			$strRawString1 = str_replace("!","not_",$strRawString1);
			$strRawString2 = str_replace("!","not_",$strRawString2);
			$strRawString3 = str_replace("!","not_",$strRawString3);
			$arrConfig1    = explode(",",$strRawString1);
			$arrConfig2    = explode(",",$strRawString2);
			$arrConfig3    = explode(",",$strRawString3);
			if (isset($arrConfig1[0])) $strConfigName  = "imp_".$arrConfig1[0];
			if (isset($arrConfig2[0])) $strConfigName .= "_".$arrConfig2[0]; 
			if (isset($arrConfig3[0])) $strConfigName .= "_".$arrConfig3[0]; 
			$strSQL = "SELECT * FROM `".$strTable."` WHERE `config_name`='$strConfigName'";
			$booRet	= $this->myDBClass->getDataArray($strSQL,$arrData,$intDC);
			if ($booRet && ($intDC != 0)) {
				$intCounter = 1;
				do {	
					$strConfigNameTemp = $strConfigName."_".$intCounter;
					$strSQL = "SELECT * FROM `".$strTable."` WHERE `config_name`='$strConfigNameTemp'";
					$booRet	= $this->myDBClass->getDataArray($strSQL,$arrData,$intDC);
					$intCounter++;
				} while ($booRet && ($intDC != 0));
				$strConfigName = $strConfigNameTemp;
			}
			$strRawString = $strRawString1.$strRawString2.$strRawString3;
			$strRawString = substr($strRawString,0,-1);
		}
		if ($strTable == "tbl_serviceextinfo") {
			// HASH from any host, any hostgroup and service description - step 1
			if (isset($arrBlockData['host_name'])) 				$strRawString .= $arrBlockData['host_name']['value'].",";
			if (isset($arrBlockData['service_description'])) 	$strRawString .= $arrBlockData['service_description']['value'].",";	
			$strRawString = substr($strRawString,0,-1);
			// Create configuration name from first two items
			$arrConfig    = explode(",",$strRawString);
			if (isset($arrConfig[0]) && ($arrConfig[0] != "")) $strConfigName  = "imp_".$arrConfig[0];
			if (isset($arrConfig[1]) && ($arrConfig[1] != "")) $strConfigName .= "_".$arrConfig[1];
		}
		while (substr_count($strRawString," ") != 0) {
			$strRawString = str_replace(" ","",$strRawString);
		}
		// Sort hash string
		$arrTemp = explode(",",$strRawString);
		sort($arrTemp);
		$strRawString = implode(",",$arrTemp);
		$strHash = sha1($strRawString);
		//echo "Hash: ".$strRawString." --> ".$strHash."<br>";
  	}
  
  
  /// 3.1 BETA end
  
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Help function: Insert relation 1
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Inserts a relation type 1 (1:1)
	//
	//  Parameters:  		$strKey			Data field name
	//                      $strValue   	Data value
	//						$intDataId		Data ID
	//						$strDataTable 	Data table (Master)
	//						$arrRelData   	Relation data
	//						$arrImportData  Import Data
	//
  	//  Return value:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function writeRelation_1($strKey,$strValue,$intDataId,$strDataTable,$arrRelData,$arrImportData) {
    	// Define variables
    	$intSlaveId = 0;
    	if (strtolower(trim($strValue)) == "null") {
      		// Update data in master table
      		$strSQL   = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = -1 WHERE `id` = ".$intDataId;
      		$booResult  = $this->myDBClass->insertData($strSQL);
    	} else {
      		// Decompose data value
      		$arrValues = explode(",",$strValue);
      		// Process data values
      		foreach ($arrValues AS $elem) {
        		$strWhere = "";
        		$strLink  = "";
				$strAdd   = "";
				// Special processing for serviceextinfo
        		if (($strDataTable == "tbl_serviceextinfo") && ($strKey == "service_description")) {
					$strLink  = "LEFT JOIN `tbl_lnkServiceToHost` on `tbl_service`.`id`=`idMaster`
								 LEFT JOIN `tbl_host` ON `idSlave`=`tbl_host`.`id`";
          			$strWhere = "AND `tbl_host`.`host_name`='".$arrImportData['host_name']['value']."'";
        		}
        		// Does the value already exist?
        		$strSQL = "SELECT `".$arrRelData['tableName1']."`.`id` FROM `".$arrRelData['tableName1']."` $strLink 
						   WHERE `".$arrRelData['target1']."` = '".$elem."' $strWhere AND `".$arrRelData['tableName1']."`.`config_id`=".$this->intDomainId;
        		$strId  = $this->myDBClass->getFieldData($strSQL);
        		if ($strId != "") {
          			$intSlaveId = $strId+0;
        		}
        		if ($intSlaveId == 0) {
					// Insert a temporary value
         			if (($strDataTable == "tbl_serviceextinfo") && ($arrRelData['tableName1'] == 'tbl_service')) $strAdd = "`config_name`='imp_tmp_by_serviceextinfo',";
					$strSQL 	= "INSERT INTO `".$arrRelData['tableName1']."` SET `".$arrRelData['target1']."` = '".$elem."',
                 			       $strAdd `config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
          			$booResult  = $this->myDBClass->insertData($strSQL);
          			$intSlaveId = $this->myDBClass->intLastId;
					// Special processing for serviceextinfo
					if (($strDataTable == "tbl_serviceextinfo") && ($strKey == "service_description")) {
						$strSQL = "SELECT `id` FROM `tbl_host` WHERE `host_name`='".$arrImportData['host_name']['value']."'";
						$strId  = $this->myDBClass->getFieldData($strSQL);
						if ($strId != "") {
							$strSQL 	= "INSERT INTO `tbl_lnkServiceToHost` SET `idMaster` = '".$intSlaveId."',
										   `idSlave` = '".$strId."'";
							$booResult  = $this->myDBClass->insertData($strSQL);
							$strSQL 	= "UPDATE `tbl_service` SET `host_name`=0 WHERE `id`='".$intSlaveId."'";
							$booResult  = $this->myDBClass->insertData($strSQL);
						}
					}
        		}
        		// Update data in master table
        		$strSQL   	= "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = ".$intSlaveId." WHERE `id` = ".$intDataId;
        		$booResult  = $this->myDBClass->insertData($strSQL);
      		}
    	}
  	}

	///////////////////////////////////////////////////////////////////////////////////////////
	//  Help function: Insert relation 2
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Inserts a relation type 2 (1:n)
	//
	//  Parameters:  		$strKey			Data field name
	//                      $strValue   	Data value
	//						$intDataId		Data ID
	//						$strDataTable 	Data table (Master)
	//						$arrRelData   	Relation data
	//
  	//  Return value:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function writeRelation_2($strKey,$strValue,$intDataId,$strDataTable,$arrRelData) {
		// Does a tploption field exist?
		$strSQL   	= "SELECT * FROM `".$strDataTable."` WHERE `id` = ".$intDataId;
		$booResult  = $this->myDBClass->getSingleDataset($strSQL,$arrDataset);
		if (isset($arrDataset[$arrRelData['fieldName']."_tploptions"])) {
		  	$intTplOption = 1;
		} else {
		  	$intTplOption = 0;
		}
    	// Delete data from link table
    	$strSQL   	= "DELETE FROM `".$arrRelData['linkTable']."` WHERE `idMaster` = ".$intDataId;
    	$booResult  = $this->myDBClass->insertData($strSQL);
    	// Define variables
    	$intSlaveId = 0;
    	if (strtolower(trim($strValue)) == "null") {
      		// Update data in master table
      		if ($intTplOption == 1) {
        		$strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 0,
              			   `".$arrRelData['fieldName']."_tploptions` = 1  WHERE `id` = ".$intDataId;
      		} else {
        		$strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 0 WHERE `id` = ".$intDataId;
      		}
      		$booResult  = $this->myDBClass->insertData($strSQL);
    	} else {
      		if (substr(trim($strValue),0,1) == "+") {
        		$intOption = 0;
        		$strValue = str_replace("+","",$strValue);
      		} else {
        		$intOption = 2;
      		}
			// Decompose data value
			$arrValues = explode(",",$strValue);
			// Process data values
			foreach ($arrValues AS $elem) {
				if ($elem != "*") {
					$strWhere = "";
					$strLink  = "";
					// Exclude values
					if (substr($elem,0,1) == "!") {
						$intExclude = 1;
						$elem = substr($elem,1);
					} else {
						$intExclude = 0;
					}
					if ((($strDataTable == "tbl_servicedependency") || ($strDataTable == "tbl_serviceescalation")) &&
						(substr_count($strKey,"service") != 0)) {
						if (substr_count($strKey,"depend") != 0) {
							$strLink  = "LEFT JOIN `tbl_lnkServiceToHost` on `id`=`idMaster`";
							$strWhere = "AND `idSlave` IN (".substr($this->strList1,0,-1).")";
						} else {
							$strLink  = "LEFT JOIN `tbl_lnkServiceToHost` on `id`=`idMaster`";
							$strWhere = "AND `idSlave` IN (".substr($this->strList2,0,-1).")";
						}
					}
					// Does the entry already exist?
					$strSQL = "SELECT `id` FROM `".$arrRelData['tableName1']."` $strLink WHERE `".$arrRelData['target1']."` = '".$elem."'
							   $strWhere AND `config_id`=".$this->intDomainId;
					$strId  = $this->myDBClass->getFieldData($strSQL);
					if ($strId != "") {
						$intSlaveId = $strId+0;
					} else {
						$intSlaveId = 0;
					}
					if (($intSlaveId == 0) && ($elem != "*")) {
						// Insert a temporary value to the target table
						$strSQL = "INSERT INTO `".$arrRelData['tableName1']."` SET `".$arrRelData['target1']."` = '".$elem."',
								   `config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
						$booResult  = $this->myDBClass->insertData($strSQL);
						$intSlaveId = $this->myDBClass->intLastId;
					}
					// Insert relations
					$strSQL   	= "INSERT INTO `".$arrRelData['linkTable']."` SET `idMaster` = ".$intDataId.", `idSlave` = ".$intSlaveId.", 
								   `exclude`=".$intExclude;
					$booResult  = $this->myDBClass->insertData($strSQL);
					// Keep values
					if (($strDataTable == "tbl_servicedependency") || ($strDataTable == "tbl_serviceescalation")) {
						$strTemp = "";
						if (($strKey == "dependent_host_name") || ($strKey == "host_name")) {
							$strTemp .= $intSlaveId.",";
						} else if (($strKey == "dependent_hostgroup_name") || ($strKey == "hostgroup_name")) {
							$arrDataHg2 = "";
							$strSQL 	= "SELECT DISTINCT `id` FROM `tbl_host`
										   LEFT JOIN `tbl_lnkHostToHostgroup` ON `id` = `tbl_lnkHostToHostgroup`.`idMaster`
										   LEFT JOIN `tbl_lnkHostgroupToHost` ON `id` = `tbl_lnkHostgroupToHost`.`idSlave`
										   WHERE (`tbl_lnkHostgroupToHost`.`idMaster` = $intSlaveId
										   OR `tbl_lnkHostToHostgroup`.`idSlave` = $intSlaveId)
										   AND `active`='1' AND `config_id`=".$this->intDomainId;
							$booReturn 	= $this->myDBClass->getDataArray($strSQL,$arrDataHostgroups,$intDCHostgroups);
							if ($booReturn && ($intDCHostgroups != 0)) {
								foreach ($arrDataHostgroups AS $elem) {
									$strTemp .= $elem['id'].",";
								}
							}
						}
						if (substr_count($strKey,"dependent") != 0) {
							$this->strList1 .= $strTemp;
						} else {
							$this->strList2 .= $strTemp;
						}
					}
				}
				// Update field values in master table
				if (substr_count($strValue,"*") != 0) {
					$intRelValue = 2;
				} else {
					$intRelValue = 1;
				}
				if ($intTplOption == 1) {
					$strSQL   = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = $intRelValue,
								 `".$arrRelData['fieldName']."_tploptions` = ".$intOption." WHERE `id` = ".$intDataId;
				} else {
					$strSQL   = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = $intRelValue WHERE `id` = ".$intDataId;
				}
				$booResult  = $this->myDBClass->insertData($strSQL);
      		}
    	}
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Help function: Insert relation 3
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Inserts a relation type 3 (templates)
	//
	//  Parameters:  		$strKey			Data field name
	//                      $strValue   	Data value
	//						$intDataId		Data ID
	//						$strDataTable 	Data table (Master)
	//						$arrRelData   	Relation data
	//
  	//  Return value:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function writeRelation_3($strKey,$strValue,$intDataId,$strDataTable,$arrRelData) {
    	// Define variables
    	$intSlaveId = 0;
    	$intTable   = 0;
    	$intSortNr  = 1;
    	if (strtolower(trim($strValue)) == "null") {
      		// Update data in master table
      		$strSQL   = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 0,
              			`".$arrRelData['fieldName']."_tploptions` = 1  WHERE `id` = ".$intDataId;
      		$booResult  = $this->myDBClass->insertData($strSQL);
    	} else {
      		if (substr(trim($strValue),0,1) == "+") {
        		$intOption = 0;
        		$strValue = str_replace("+","",$strValue);
      		} else {
        		$intOption = 2;
      		}
			// Remove old relations
			$strSQL   	= "DELETE FROM `".$arrRelData['linkTable']."` WHERE `idMaster` = ".$intDataId;
			$booResult  = $this->myDBClass->insertData($strSQL);
      		// Decompose data value
      		$arrValues = explode(",",$strValue);
      		// Process data values
      		foreach ($arrValues AS $elem) {
				// Does the template already exist? (table 1)
        		$strSQL = "SELECT `id` FROM `".$arrRelData['tableName1']."` WHERE `".$arrRelData['target1']."` = '".$elem."' AND `config_id`=".$this->intDomainId;
        		$strId  = $this->myDBClass->getFieldData($strSQL);
        		if ($strId != "") {
          			$intSlaveId = $strId+0;
          			$intTable = 1;
        		}
        		if ($intSlaveId == 0) {
					// Does the template already exist? (table 2)
					$strSQL = "SELECT `id` FROM `".$arrRelData['tableName2']."` WHERE `".$arrRelData['target2']."` = '".$elem."' AND `config_id`=".$this->intDomainId;
					$strId  = $this->myDBClass->getFieldData($strSQL);
					if ($strId != "") {
						$intSlaveId = $strId+0;
						$intTable   = 2;
					}
        		}
        		if ($intSlaveId == 0) {
          			// Insert a temporary value to the target table
          			$strSQL 	= "INSERT INTO `".$arrRelData['tableName1']."` SET `".$arrRelData['target1']."` = '".$elem."',
                 			   	   `config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
          			$booResult  = $this->myDBClass->insertData($strSQL);
          			$intSlaveId = $this->myDBClass->intLastId;
          			$intTable   = 1;
        		}
        		// Insert relations
       			$strSQL   	= "INSERT INTO `".$arrRelData['linkTable']."` SET `idMaster` = ".$intDataId.", `idSlave` = ".$intSlaveId.",
                 			   `idSort` = ".$intSortNr.", `idTable` = ".$intTable;
        		$booResult  = $this->myDBClass->insertData($strSQL);
        		$intSortNr++;
				$intSlaveId = 0;
        		// Update field data in master table
        		$strSQL   	= "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 1,
                			   `".$arrRelData['fieldName']."_tploptions` = ".$intOption." WHERE `id` = ".$intDataId;
        		$booResult  = $this->myDBClass->insertData($strSQL);
      		}
    	}
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Help function: Insert relation 4
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Inserts a relation type 4 (free variables)
	//
	//  Parameters:  		$strKey			Data field name
	//                      $strValue   	Data value
	//						$intDataId		Data ID
	//						$strDataTable 	Data table (Master)
	//						$arrRelData   	Relation data
	//
  	//  Return value:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function writeRelation_4($strKey,$strValue,$intDataId,$strDataTable,$arrRelData) {
		// Remove empty variables
		if (($strKey == "") || ($strValue == "")) return(1);
		// Insert values to the table
		$strSQL   	= "INSERT INTO `tbl_variabledefinition` SET `name` = '$strKey', `value` = '$strValue', `last_modified`=now()";
		$booResult  = $this->myDBClass->insertData($strSQL);
		$intSlaveId = $this->myDBClass->intLastId;
		// Insert relations to the table
		$strSQL   	= "INSERT INTO `".$arrRelData['linkTable']."` SET `idMaster` = ".$intDataId.", `idSlave` = ".$intSlaveId;
		$booResult  = $this->myDBClass->insertData($strSQL);
		// Update data in master table
		$strSQL   	= "UPDATE `".$strDataTable."` SET `use_variables` = 1 WHERE `id` = ".$intDataId;
		$booResult  = $this->myDBClass->insertData($strSQL);
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Help function: Insert relation 5
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Inserts a relation type 5 (1:1 check command)
	//
	//  Parameters:  		$strKey			Data field name
	//                      $strValue   	Data value
	//						$intDataId		Data ID
	//						$strDataTable 	Data table (Master)
	//						$arrRelData   	Relation data
	//
  	//  Return value:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function writeRelation_5($strKey,$strValue,$intDataId,$strDataTable,$arrRelData) {
    	// Extract data values
    	$arrCommand = explode("!",$strValue);
    	$strValue   = $arrCommand[0];
    	// Define variables
    	$intSlaveId = 0;
    	if (strtolower(trim($strValue)) == "null") {
      		// Update data in master table
      		$strSQL   = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = -1 WHERE `id` = ".$intDataId;
      		$booResult  = $this->myDBClass->insertData($strSQL);
    	} else {
      		// Decompose data values
      		$arrValues = explode(",",$strValue);
      		// Process data values
      		foreach ($arrValues AS $elem) {
        		// Does the entry already exist?
       			$strSQL = "SELECT `id` FROM `".$arrRelData['tableName1']."` WHERE `".$arrRelData['target1']."` = '".$elem."' AND `config_id`=".$this->intDomainId;
        		$strId  = $this->myDBClass->getFieldData($strSQL);
        		if ($strId != "") {
          			$intSlaveId = $strId+0;
        		}
        		if ($intSlaveId == 0) {
          			// Insert a temporary value in target table
          			$strSQL 	= "INSERT INTO `".$arrRelData['tableName1']."` SET `".$arrRelData['target1']."` = '".$elem."',
                 			       `config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
          			$booResult  = $this->myDBClass->insertData($strSQL);
          			$intSlaveId = $this->myDBClass->intLastId;
        		}
        		// Update data in master table
        		$arrCommand[0] 	= $intSlaveId;
        		$strValue     	= implode("!",$arrCommand);
        		$strSQL   		= "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = '".mysql_real_escape_string($strValue)."' WHERE `id` = ".$intDataId;
        		$booResult  	= $this->myDBClass->insertData($strSQL);
      		}
    	}
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Help function: Insert relation 6
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Inserts a relation type 5 (1:n:n service groups)
	//
	//  Parameters:  		$strKey			Data field name
	//                      $strValue   	Data value
	//						$intDataId		Data ID
	//						$strDataTable 	Data table (Master)
	//						$arrRelData   	Relation data
	//
  	//  Return value:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function writeRelation_6($strKey,$strValue,$intDataId,$strDataTable,$arrRelData) {
    	// Define variables
		$intSlaveId  	= 0;
		$intSlaveIdS 	= 0;
		$intSlaveIdH 	= 0;
		$intSlaveIdHG	= 0;
    	// Decompose data value
    	$arrValues 	= explode(",",$strValue);
    	// Remove data from link table
    	$strSQL   	= "DELETE FROM `".$arrRelData['linkTable']."` WHERE `idMaster` = ".$intDataId;
    	$booResult  = $this->myDBClass->insertData($strSQL);
    	// Check the sum of elements
    	if (count($arrValues) % 2 != 0) {
      		$this->strMessage .= translate("Error: wrong number of arguments - cannot import service group members")."<br>";
    	} else {
      		// Process data values
      		$intCounter = 1;
      		foreach ($arrValues AS $elem) {
				if ($intCounter % 2 == 0) {
          			// Does the host entry already exist?
          			$strSQL = "SELECT `id` FROM `".$arrRelData['tableName1']."` 
							   WHERE `".$arrRelData['target1']."` = '".$strValue."' AND `config_id`=".$this->intDomainId;
          			$strId  = $this->myDBClass->getFieldData($strSQL);
          			if ($strId != "") {
            			$intSlaveIdH = $strId+0;
          			}
					// Does a hostgroup entry already exist?
					if ($intSlaveIdH == 0) {
						$strSQL = "SELECT `id` FROM `tbl_hostgroup` 
								   WHERE `hostgroup_name` = '".$strValue."' AND `config_id`=".$this->intDomainId;
						$strId  = $this->myDBClass->getFieldData($strSQL);
						if ($strId != "") {
							$intSlaveIdHG = $strId+0;
						}	
					}
          			if (($intSlaveIdH == 0) && ($intSlaveIdHG == 0)) {
            			// Insert a temporary value in table
            			$strSQL 		= "INSERT INTO `".$arrRelData['tableName1']."` SET `".$arrRelData['target1']."` = '".$strValue."',
                   			 	   		  `config_id`=".$this->intDomainId.", `active`='0', `last_modified`=NOW()";
            			$booResult   	= $this->myDBClass->insertData($strSQL);
            			$intSlaveIdH 	= $this->myDBClass->intLastId;
          			}
          			// Does the service entry already exist?
					if ($intSlaveIdH != 0) {
						$strSQL = "SELECT `id` FROM `".$arrRelData['tableName2']."`
								   LEFT JOIN `tbl_lnkServiceToHost` ON `id` = `idMaster`
								   WHERE `".$arrRelData['target2']."` = '".$elem."' AND `idSlave` = ".$intSlaveIdH." AND `config_id`=".$this->intDomainId;
						$strId  = $this->myDBClass->getFieldData($strSQL);
					} else if ($intSlaveIdHG != 0) {
						$strSQL = "SELECT `id` FROM `".$arrRelData['tableName2']."`
								   LEFT JOIN `tbl_lnkServiceToHostgroup` ON `id` = `idMaster`
								   WHERE `".$arrRelData['target2']."` = '".$elem."' AND `idSlave` = ".$intSlaveIdHG." AND `config_id`=".$this->intDomainId;
						$strId  = $this->myDBClass->getFieldData($strSQL);
					}
          			if ($strId != "") {
            			$intSlaveIdS = $strId+0;
          			} else {
						$intSlaveIdS = 0;
					}
					if ($intSlaveIdS == 0) {
						// Insert a temporary value in table
						$intHostName 		= 0;
						$intHostgroupName 	= 0;
						if ($intSlaveIdH != 0) {
							$intHostName 		= 1;
						} else if ($intSlaveIdHG != 0) {
							$intHostgroupName 	= 1;
						}
            			$strSQL		 = "INSERT INTO `".$arrRelData['tableName2']."` SET `config_name`='imp_tmp_by_servicegroup', `host_name`=$intHostName, 
										`hostgroup_name`=$intHostgroupName, `".$arrRelData['target2']."` = '".$elem."', `config_id`=".$this->intDomainId.", 
										`active`='0', `last_modified`=NOW()";
            			$booResult	 = $this->myDBClass->insertData($strSQL);
            			$intSlaveIdS = $this->myDBClass->intLastId;	
						// Make a relation from temp service to host / hostgroup
						if ($intSlaveIdH != 0) {
							$strSQL		= "INSERT INTO `tbl_lnkServiceToHost` SET `idMaster` = '".$intSlaveIdS."',
											`idSlave`=".$intSlaveIdH.", `exclude`='0'";
							$booResult	= $this->myDBClass->insertData($strSQL);
						} else if ($intSlaveIdHG != 0) {
							$strSQL		= "INSERT INTO `tbl_lnkServiceToHostgroup` SET `idMaster` = '".$intSlaveIdS."',
											`idSlave`=".$intSlaveIdHG.", `exclude`='0'";
							$booResult	= $this->myDBClass->insertData($strSQL);	
						}
					}
          			// Insert relation
          			$strSQL   	= "INSERT INTO `".$arrRelData['linkTable']."` 
								   SET `idMaster` = ".$intDataId.", `idSlaveH` = ".$intSlaveIdH.", `idSlaveS` = ".$intSlaveIdS;
          			$booResult  = $this->myDBClass->insertData($strSQL);
          			// Update data in master table
          			$strSQL   	= "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = 1 WHERE `id` = ".$intDataId;
          			$booResult  = $this->myDBClass->insertData($strSQL);
        		} else {
          			$strValue = $elem;
        		}
        		$intCounter++;
      		}
    	}
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Help function: Insert relation 7
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Inserts a relation type 6 (1:n:str)
	//
	//  Parameters:  		$strKey			Data field name
	//                      $strValue   	Data value
	//						$intDataId		Data ID
	//						$strDataTable 	Data table (Master)
	//						$arrRelData   	Relation data
	//
  	//  Return value:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function writeRelation_7($strKey,$strValue,$intDataId,$strDataTable,$arrRelData) {
    	// Delete data from link table
    	$strSQL   	= "DELETE FROM `".$arrRelData['linkTable']."` WHERE `idMaster` = ".$intDataId;
    	$booResult  = $this->myDBClass->insertData($strSQL);
    	// Define variables
    	$intSlaveId = 0;
    	// Decompose data value
		$arrValues = explode(",",$strValue);
		// Process data values
		foreach ($arrValues AS $elem) {
			if ($elem != "*") {
				$strWhere = "";
				$strLink  = "";
				// Exclude values
				if (substr($elem,0,1) == "!") {
					$intExclude = 1;
					$elem = substr($elem,1);
				} else {
					$intExclude = 0;
				}
				// Does the entry already exist?
				$strSQL = "SELECT `id` FROM `".$arrRelData['tableName1']."` WHERE `".$arrRelData['target1']."` = '".$elem."'
						   $strWhere AND `config_id`=".$this->intDomainId;
				$strId  = $this->myDBClass->getFieldData($strSQL);
				if ($strId != "") {
					$intSlaveId = $strId+0;
				} else {
					$intSlaveId = 0;
				}
				if (($intSlaveId == 0) && ($elem != "*")) {
					// Insert a temporary value to the target table
					$strSQL = "INSERT INTO `".$arrRelData['tableName1']."` SET `".$arrRelData['target1']."` = '".$elem."',
							   `host_name`=2, `hostgroup_name`=2, `config_name`='imp_tmp_by_servicedependency', `config_id`=".$this->intDomainId.", 
							   `active`='0', `last_modified`=NOW()";
					$booResult  = $this->myDBClass->insertData($strSQL);
					$intSlaveId = $this->myDBClass->intLastId;
				}
				// Insert relations
				$strSQL   	= "INSERT INTO `".$arrRelData['linkTable']."` SET `idMaster` = ".$intDataId.", `idSlave` = ".$intSlaveId.", 
							   `strSlave`='".$elem."', `exclude`=".$intExclude;
				$booResult  = $this->myDBClass->insertData($strSQL);
			}
			// Update field values in master table
			if (substr_count($strValue,"*") != 0) {
				$intRelValue = 2;
			} else {
				$intRelValue = 1;
			}
			$strSQL = "UPDATE `".$strDataTable."` SET `".$arrRelData['fieldName']."` = $intRelValue WHERE `id` = ".$intDataId;
			$booRes = $this->myDBClass->insertData($strSQL);
    	}
  	}
}
?>