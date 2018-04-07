<?php
///////////////////////////////////////////////////////////////////////////////
//
// Common utilities
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2017 by Martin Willisegger
//
// Project   : Common scripts
// Component : MySQLi data processing class
// Date      : $LastChangedDate: 2017-06-22 13:39:15 +0200 (Thu, 22 Jun 2017) $
// Author    : $LastChangedBy: martin $
// Version   : 3.3.0
// Revision  : $LastChangedRevision: 7 $
// SVN-ID    : $Id: mysqli_class.php 7 2017-06-22 11:39:15Z martin $
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Class: Common database functions for MySQL (mysqli database module)
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Includes any functions to communicate with an MySQL database server
//
// Name: mysqlidb
//
// Class variables:		$arrParams			Array including the server settings
// ----------------		$strErrorMessage	Database error string
//						$error				Boolean - Error true/false
//						$strDBId			Database connection id
//						$intLastId			ID of last dataset
//						$intAffectedRows	Counter variable of all affected data dows
//						$booSSLuse			Use SSL connection
//											(INSERT/DELETE/UPDATE)
//
// Parameters:			$arrParams['server']	-> DB server name
// -----------			$arrParams['port']		-> DB server port
//						$arrParams['user']		-> DB server username
//						$arrParams['password']	-> DB server password
//						$arrParams['database']	-> DB server database name
//
///////////////////////////////////////////////////////////////////////////////////////////////
class mysqlidb {
	// Define class variables
  	var $error            = false;	// Will be filled in functions
  	var $strDBId          = "";		// Will be filled in functions
  	var $intLastId        = 0;		// Will be filled in functions
  	var $intAffectedRows  = 0;		// Will be filled in functions
  	var $strErrorMessage  = "";		// Will be filled in functions
	var $booSSLuse		  = false;  // Defines if SSL is used or not
	var $arrParams		  = "";		// Must be filled in while initialization
	var $arrSQLdef		  = "";		// Must be filled in while initialization
	var $strSQLQuote1	  = "`"; 	// Quote char for table or row names
	var $strSQLQuote2	  = "'"; 	// Quote char for table or row names
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Class constructor
  	///////////////////////////////////////////////////////////////////////////////////////////
  	//
  	//  Activities during initialisation
  	//
  	///////////////////////////////////////////////////////////////////////////////////////////
  	function __construct() {
		$this->arrParams['server']		= "";
		$this->arrParams['port']		= 0;
		$this->arrParams['username']	= "";
		$this->arrParams['password']	= "";
		$this->arrParams['database']	= "";
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Connect to database
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Opens a connection to the database server and select a database
	//
	//
  	//  Return value:		true 				successful
	//						false 				error
	//						Status message is stored in class variable $this->strErrorMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function getdatabase() {
    	$this->dbconnect();
    	if ($this->error == true) {
      		return false;
    	}
    	$this->dbselect();
    	if ($this->error == true) {
      		return false;
    	}
    	return true;
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Get a single dataset field value
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Sends an SQL statement to the server and returns the result of the first data field  
	//
	//  Parameters:  		$strSQL     SQL Statement
	//
  	//  Return value:		<data> 	= successful
	//						<empty>	= error
	//						Status message is stored in class variable $this->strErrorMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function getFieldData($strSQL) {
		// Reset error variables
		$this->strErrorMessage = "";
		$this->error     	   = false;
		// Send the SQL statement to the server
		$resQuery = mysqli_query($this->strDBId,$strSQL);
		// Error processing
		if ($resQuery && (mysqli_num_rows($resQuery) != 0) && (mysqli_error($this->strDBId) == "")) {
			// Return the field value from position 0/0
			$arrDataset = mysqli_fetch_array($resQuery,MYSQLI_NUM);
			return $arrDataset[0];
		} else if (mysqli_error($this->strDBId) != "") {
			$this->strErrorMessage .= mysqli_error($this->strDBId)."::";
			$this->error     		= true;
     		return("");
		}
		return("");
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Get a single dataset    
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Sends an SQL statement to the server and returns the result of the first data set
	//
	//  Parameters:  		$strSQL     	SQL Statement
	//						$arrDataset		Return value including the data set
	//
  	//  Return value:		true 	= successful
	//						false 	= error
	//						Status message is stored in class variable $this->strErrorMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function getSingleDataset($strSQL,&$arrDataset) {
		$arrDataset = "";
		// Reset error variables
		$this->strErrorMessage = "";
		$this->error     	   = false;
		// Send the SQL statement to the server
		$resQuery = mysqli_query($this->strDBId,$strSQL);
		// Error processing
		if ($resQuery && (mysqli_num_rows($resQuery) != 0) && (mysqli_error($this->strDBId) == "")) {
			// Put the values into the array
			$arrDataset = mysqli_fetch_array($resQuery,MYSQLI_ASSOC);
			return true;
		} else if (mysqli_error($this->strDBId) != "") {
			$this->strErrorMessage .= mysqli_error($this->strDBId)."::";
			$this->error     	    = true;
			return false;
		}
		return true;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Get a full data part
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Sends an SQL statement to the server and returns the result inside a data array
	//
	//  Parameters:  		$strSQL     	SQL Statement
	//						$arrDataset		Return value including the data records
	//						$intDataCount	Return value including the number of the records
	//
  	//  Return value:		true 	= successful
	//						false 	= error
	//						Status message is stored in class variable $this->strErrorMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function getDataArray($strSQL,&$arrDataset,&$intDataCount) {
		$arrDataset   = "";
		$intDataCount = 0;
		// Reset error variables
		$this->strErrorMessage = "";
		$this->error     	   = false;
		// Send the SQL statement to the server
		$resQuery = mysqli_query($this->strDBId,$strSQL);
		// Error processing
		if ($resQuery && (mysqli_num_rows($resQuery) != 0) && (mysqli_error($this->strDBId) == "")) {
			$intDataCount = mysqli_num_rows($resQuery);
			$i = 0;
			// Put the values into the array
			while ($arrDataTemp = mysqli_fetch_array($resQuery,MYSQLI_ASSOC)) {
				foreach ($arrDataTemp AS $key => $value) {
					$arrDataset[$i][$key] = $value;
				}
				$i++;
			}
			return true;
		} else if (mysqli_error($this->strDBId) != "") {
			$this->strErrorMessage .= mysqli_error($this->strDBId)."::";
			$this->error     		= true;
			return false;
		}
		return true;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Insert/update/delete data
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Insert/update or delete data
	//
	//  Parameters:  		$strSQL     			SQL Statement
	//   					$this->intLastId    	Dataset insert ID
	//  					$this->intAffectedRows  The number of the affected records
	//
  	//  Return value:		true 	= successful
	//						false 	= error
	//						Status message is stored in class variable $this->strErrorMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function insertData($strSQL) {
		// Reset error variables
		$this->strErrorMessage = "";
		$this->error     	   = false;
		// Send the SQL statement to the server
		$resQuery = mysqli_query($this->strDBId,$strSQL);
		// Error processing
		if (mysqli_error($this->strDBId) == "") {
			$this->intLastId        = mysqli_insert_id($this->strDBId);
			$this->intAffectedRows  = mysqli_affected_rows($this->strDBId);
			return true;
		} else {
			$this->strErrorMessage .= mysqli_error($this->strDBId)."::";
			$this->error     		= true;
			return false;
		}
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Counts data rows
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Counts the number of records 
	//
	//  Parameters:  		$strSQL     			SQL Statement
	//
  	//  Return value:		<number> 	= successful
	//						0 			= no datasets or error
	//						Status message is stored in class variable $this->strErrorMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function countRows($strSQL) {
		// Reset error variables
		$this->strErrorMessage = "";
		$this->error     	   = false;
		// Send the SQL statement to the server
		$resQuery = mysqli_query($this->strDBId,$strSQL);
		// Error processing
		if ($resQuery && (mysqli_error($this->strDBId) == "")) {
			return mysqli_num_rows($resQuery);
		} else {
			$this->strErrorMessage .= mysqli_error($this->strDBId);
			$this->error     		= true;
			return 0;
		}
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Use mysqli_real_escape_string
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Returns a safe insert string for database manipulations
	//
	//  Value:			$strInput 	Input String
	//
	//  Return value:	$strOutput	Output String
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function real_escape($strInput) {
		return mysqli_real_escape_string($this->strDBId,$strInput);
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Assistant functions
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Initialize a mysql database connection
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Return value:			true
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dbinit() {
		$this->strDBId = mysqli_init();
    	return true;
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Connect to database server
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameters:			$dbserver 			Server name
	//						$dbuser				Database user
	//						$dbpasswd			Database password
	//						$dbname				Database name
	//						$dbport				TCP port
	//
  	//  Return value:		true 				successful
	//						false 				error
	//						Status message is stored in class variable $this->strErrorMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function dbconnect($dbserver=NULL,$dbport=NULL,$dbuser=NULL,$dbpasswd=NULL) {
		// Reset error variables
		$this->strErrorMessage = "";
		$this->error     	   = false;
		// Get parameters
		if ($dbserver == NULL) $dbserver = $this->arrParams['server'];
		if ($dbport   == NULL) $dbport   = $this->arrParams['port'];
		if ($dbuser   == NULL) $dbuser   = $this->arrParams['username'];
		if ($dbpasswd == NULL) $dbpasswd = $this->arrParams['password'];
    	// Not all parameters available
		if (($dbserver == "") || ($dbuser == "") || ($dbpasswd == "")) {
      		$this->strErrorMessage .= gettext("Missing server connection parameter!")."::";
      		$this->error   = true;
      		return false;
    	}
		$this->dbinit();
		if ($this->booSSLuse == true) {
			// TO BE DEFINED
		}
		$intErrorReporting = error_reporting();
		error_reporting(0);
		if ($dbport == 0) {
			$booReturn = mysqli_real_connect($this->strDBId,$dbserver,$dbuser,$dbpasswd);
		} else {
			$booReturn = mysqli_real_connect($this->strDBId,$dbserver,$dbuser,$dbpasswd,NULL,$dbport);
		}
		$arrError = error_get_last();
		error_reporting($intErrorReporting);
		// Connection fails
		if($booReturn == false) {
      		$this->strErrorMessage  = "[".$dbserver."] ".gettext("Connection to the database server has failed by reason:")." ::";
			if (mysqli_connect_error($this->strDBId) != "")	$this->strErrorMessage .= mysqli_connect_error($this->strDBId)."::";
      		$this->error   			= true;
      		return false;
    	}
    	return true;
  	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: select database
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameters:  		$database 		Database name
	//
  	//  Return value:		true 	= successful
	//						false 	= error
	//						Status message is stored in class variable $this->strErrorMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function dbselect($database=NULL) {
		// Reset error variables
		$this->strErrorMessage = "";
		$this->error     	   = false;
		// Get parameters
		if ($database == NULL) $database = $this->arrParams['database'];
    	// Not all parameters available
    	if ($database == "") {
     		$this->strErrorMessage .= gettext("Missing database connection parameter!")."::";
      		$this->error   = true;
      		return false;
    	}
    	$bolConnect = mysqli_select_db($this->strDBId,$database);
    	// Session cannot be etablished
    	if(!$bolConnect) {
      		$this->strErrorMessage .= "[".$database."] ".gettext("Connection to the database has failed by reason:")." ::";
      		$this->strErrorMessage .= mysqli_error($this->strDBId)."::";
      		$this->error   	   		= true;
      		return false;
    	}
		$resQuery = mysqli_query($this->strDBId,"set names 'utf8'");
		if (mysqli_error($this->strDBId) != "") {
      		$this->strErrorMessage	.= mysqli_error($this->strDBId)."::";
      		$this->error      		 = true;
      		return false;
    	}
		$resQuery = mysqli_query($this->strDBId,"set session sql_mode = 'NO_ENGINE_SUBSTITUTION'");
		if (mysqli_error($this->strDBId) != "") {
      		$this->strErrorMessage	.= mysqli_error($this->strDBId)."::";
      		$this->error      		 = true;
      		return false;
    	}
    	return true;
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Set SSL parameters
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameters:			$sslkey 			SSL key
	//						$sslcert			SSL certificate
	//						$sslca				SSL CA file (optional)
	//						$sslpath			SSL certificate path (optional)
	//						$sslcypher			SSL cypher (optional)
	//
  	//  Return value:		true 				successful
	//						The mysqli_ssl_set function always returns TRUE!
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dbsetssl($sslkey,$sslcert,$sslca=NULL,$sslpath=NULL,$sslcypher=NULL) {
		// Reset error variables
		$this->strErrorMessage = "";
		$this->error     	   = false;
		// Values are missing
		if (($sslkey == "") || ($sslcert == "")) {
			$this->strErrorMessage = gettext("Missing MySQL SSL parameter!")."::";
			$this->error   = true;
			return false;
		}
		mysqli_ssl_set($this->strDBId,$sslkey,$sslcert,$sslca,$sslpath,$sslcypher);
    	return true;
  	}	
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Close database server connectuon
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Value:			none
	//
	//  Return value:	true if successful, false if failed
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dbdisconnect() {
		mysqli_close($this->strDBId);
		return true;
	}
}
?>