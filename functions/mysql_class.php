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
// Component : Mysql data processing class
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2012-02-21 14:10:41 +0100 (Tue, 21 Feb 2012) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1229 $
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Class: Common database functions for MySQL
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Includes any functions to communicate with an MySQL database server
//
// Name: mysqldb
//
// Class variables:  	$arrSettings    	Includes all global settings ($SETS)
// 						$strErrorMessage     	Includes database error messages
//            			$error        		Boolean - error occurred (true/false)
//            			$strDBId      		Database connection ID
//            			$intLastId      	Last insert ID
//            			$intAffectedRows  	Counter for affected data rows (INSERT/DELETE/UPDATE)
//
///////////////////////////////////////////////////////////////////////////////////////////////
class mysqldb {
  	// Define class variables
  	var $arrSettings;				// Will be filled in class constructor
  	var $error            = false;	// Will be filled in functions
  	var $strDBId          = "";		// Will be filled in functions
  	var $intLastId        = 0;		// Will be filled in functions
  	var $intAffectedRows  = 0;		// Will be filled in functions
  	var $strErrorMessage  = "";		// Will be filled in functions

	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Class constructor
  	///////////////////////////////////////////////////////////////////////////////////////////
  	//
  	//  Activities during initialisation
  	//
  	///////////////////////////////////////////////////////////////////////////////////////////
  	function mysqldb() {
    	if (isset($_SESSION) && isset($_SESSION['SETS'])) {
			// Read global settings
			$this->arrSettings = $_SESSION['SETS'];
    		// Connect to Database
			if (isset($this->arrSettings['db'])) $this->getDatabase($this->arrSettings['db']);
		}
  	}

	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Connect to a database
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Opens a connection to the database server and select a database
	//
	//  Parameters:  		$arrSettings  		Connection parameter
	//                    	-> Key server   = Servername
	//                    	-> Key username = Benutzername
	//                    	-> Key password = Passwort
	//                    	-> Key database = Datenbank
	//
  	//  Return value:		true 	= successful
	//						false 	= error
	//						Status message is stored in class variable $this->strErrorMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function getdatabase($arrSettings) {
    	$this->dbconnect($arrSettings['server'],$arrSettings['port'],$arrSettings['username'],$arrSettings['password']);
    	if ($this->error == true) {
      		return false;
    	}
    	$this->dbselect($arrSettings['database']);
    	if ($this->error == true) {
      		return false;
    	}
    	return true;
  	}

	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Get a singe data field
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
    	// Send an SQL Statement to the server
    	$resQuery = mysql_query($strSQL);
    	// Error processing
    	if ($resQuery && (mysql_num_rows($resQuery) != 0) && (mysql_error() == "")) {
      		// return the field value at postition 0/0
			return mysql_result($resQuery,0,0);
    	} else if (mysql_error() != "") {
      		$this->strErrorMessage .= mysql_error()."::";
      		$this->error        	= true;
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
    	// Send an SQL Statement to the server
    	$resQuery = mysql_query($strSQL);
    	// Error processing
    	if ($resQuery && (mysql_num_rows($resQuery) != 0) && (mysql_error() == "")) {
      		// Fill the data to the array
      		$arrDataset = mysql_fetch_array($resQuery,MYSQL_ASSOC);
      		return true;
    	} else if (mysql_error() != "") {
      		$this->strErrorMessage .= mysql_error()."::";
      		$this->error      		= true;
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
    	// Send an SQL Statement to the server
    	$resQuery = mysql_query($strSQL);
    	// Error processing
    	if ($resQuery && (mysql_num_rows($resQuery) != 0) && (mysql_error() == "")) {
      		$intDataCount = mysql_num_rows($resQuery);
      		$i = 0;
      		// Fill array
	  		while ($arrDataTemp = mysql_fetch_array($resQuery, MYSQL_ASSOC)) {
        		foreach ($arrDataTemp AS $key => $value) {
          			$arrDataset[$i][$key] = $value;
        		}
        		$i++;
      		}
      		return true;
    	} else if (mysql_error() != "") {
      		$this->strErrorMessage .= mysql_error()."::";
      		$this->error 			= true;
      		return false;
    	}
    	return true;
  	}

    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Insert data
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Inserts data to the database server
	//
	//  Parameters:  		$strSQL     			SQL Statement
	//
	//   					$this->intLastId    	Dataset insert ID
	//  					$this->intAffectedRows  The number of the affected records
	//
  	//  Return value:		true 	= successful
	//						false 	= error
	//						Status message is stored in class variable $this->strErrorMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function insertData($strSQL) {
    	// Send an SQL Statement to the server
    	$resQuery        = mysql_query($strSQL);
    	// Error processing
    	if (mysql_error() == "") {
      		$this->intLastId    	= mysql_insert_id();
      		$this->intAffectedRows  = mysql_affected_rows();
      		return true;
    	} else {
      		$this->strErrorMessage .= mysql_error()."::";
      		$this->error      		= true;
      		return false;
    	}
  	}

    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Count records
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
    	// Send an SQL Statement to the server
    	$resQuery = mysql_query($strSQL);
    	// Error processing
    	if ($resQuery && (mysql_error() == "")) {
      		return mysql_num_rows($resQuery);
    	} else {
      		$this->strErrorMessage .= mysql_error()."::";
      		$this->error      		= true;
      		return 0;
    	}
  	}

    ///////////////////////////////////////////////////////////////////////////////////////////
	//
	// help functions
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Connet to the database server
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameters:  		$dbserver 		Server name
	//						$dbport			Server port
	//						$dbuser			Database user
	//						$dbpasswd		Database password
	//
  	//  Return value:		true 	= successful
	//						false 	= error
	//						Status message is stored in class variable $this->strErrorMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function dbconnect($dbserver,$dbport,$dbuser,$dbpasswd) {
    	// Not all parameters available
    	if (($dbserver == "") || ($dbuser == "")) {
      		$this->strErrorMessage .= translate("Missing server connection parameter!")."::";
      		$this->error   = true;
      		return false;
    	}
    	$this->strDBId = @mysql_connect($dbserver.":".$dbport,$dbuser,$dbpasswd);
    	// Session cannot be etablished
      	if(!$this->strDBId) {
      		$this->strErrorMessage .= "[".$this->arrSettings['db']['server']."] ".translate("Connection to the database server has failed by reason:")."::";
      		$this->strErrorMessage .= mysql_error()."::";
      		$this->error   	   		= true;
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
  	function dbselect($database) {
    	// Not all parameters available
    	if ($database == "") {
     		$this->strErrorMessage .= translate("Missing database connection parameter!")."::";
      		$this->error   = true;
      		return false;
    	}
    	$bolConnect = @mysql_select_db($database);
    	// Session cannot be etablished
    	if(!$bolConnect) {
      		$this->strErrorMessage .= "[".$this->arrSettings['db']['server']."] ".translate("Connection to the database server has failed by reason:")."::";
      		$this->strErrorMessage .= mysql_error()."::";
      		$this->error   	   		= true;
      		return false;
    	}
		$resQuery = mysql_query("set names 'utf8'");
		if (mysql_error() != "") {
      		$this->strErrorMessage	.= mysql_error()."::";
      		$this->error      		 = true;
      		return false;
    	}
    	return true;
  	}

    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Close database connection
	///////////////////////////////////////////////////////////////////////////////////////////
	//
  	//  Return value:		true 	= successful
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function dbdisconnect() {
    	@mysql_close($this->strDBId);
    	return true;
  	}
}
?>