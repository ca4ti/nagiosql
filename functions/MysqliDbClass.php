<?php
///////////////////////////////////////////////////////////////////////////////
//
// Common utilities
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2020 by Martin Willisegger
//
// Project   : Common scripts
// Component : MySQLi data processing class
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
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
// Name: MysqliDbClass
//
// Class variables:     $arrParams              Array including the server settings
// ----------------     $strErrorMessage        Database error string
//                      $error                  Boolean - Error true/false
//                      $strDBId                Database connection id
//                      $intLastId              ID of last dataset
//                      $intAffectedRows        Counter variable of all affected data dows
//                      $booSSLuse              Use SSL connection
//
// Parameters:          $arrParams['server']    -> DB server name
// -----------          $arrParams['port']      -> DB server port
//                      $arrParams['user']      -> DB server username
//                      $arrParams['password']  -> DB server password
//                      $arrParams['database']  -> DB server database name
//
///////////////////////////////////////////////////////////////////////////////////////////////
namespace functions;

class MysqliDbClass
{
    // Define class variables
    public $error            = false;   // Will be filled in functions
    public $strDBId;                    // Will be filled in functions
    public $intLastId        = 0;       // Will be filled in functions
    public $intAffectedRows  = 0;       // Will be filled in functions
    public $strErrorMessage  = '';      // Will be filled in functions
    public $booSSLuse        = false;   // Defines if SSL is used or not
    public $arrParams        = array(); // Must be filled in while initialization

    /**
     * MysqliDbClass constructor.
     */
    public function __construct()
    {
        $this->arrParams['server']      = '';
        $this->arrParams['port']        = 0;
        $this->arrParams['username']    = '';
        $this->arrParams['password']    = '';
        $this->arrParams['database']    = '';
    }

    /**
     * MysqliDbClass destructor.
     */
    public function __destruct()
    {
        $this->dbDisconnect();
    }

    /**
     * Opens a connection to the database server and select a database
     * @param int $intMode                      1 = connect only / 0 = connect + dbselect
     * @return bool                             true = successful / false = error
     *                                          Status messages are stored in class variable
     */
    public function hasDBConnection($intMode = 0)
    {
        $booReturn = true;
        $this->dbconnect();
        if ($this->error == true) {
            $booReturn = false;
        }
        if (($booReturn == true) && ($intMode == 0)) {
            $this->dbselect();
            if ($this->error == true) {
                $booReturn = false;
            }
        }
        return $booReturn;
    }

    /**
     * Sends an SQL statement to the server and returns the result of the first data field
     * @param string $strSQL                    SQL Statement
     * @return string                           <data> = successful / <empty> = error
     *                                          Status messages are stored in class variable
     */
    public function getFieldData($strSQL)
    {
        // Reset error variables
        $this->strErrorMessage = '';
        $this->error           = false;
        $strReturn             = '';
        // Send the SQL statement to the server
        $resQuery = mysqli_query($this->strDBId, $strSQL);
        // Error processing
        if ($resQuery && (mysqli_num_rows($resQuery) != 0) && (mysqli_error($this->strDBId) == '')) {
            // Return the field value from position 0/0
            $arrDataset = mysqli_fetch_array($resQuery, MYSQLI_NUM);
            $strReturn = $arrDataset[0];
        } elseif (mysqli_error($this->strDBId) != '') {
            $this->strErrorMessage .= mysqli_error($this->strDBId). '::';
            $this->error             = true;
        }
        return $strReturn;
    }

    /**
     * Sends an SQL statement to the server and returns the result of the first data set
     * @param string $strSQL                    SQL Statement
     * @param array|null $arrDataset            Result array (by reference)
     * @return bool                             true = successful / false = error
     *                                          Status messages are stored in class variable
     */
    public function hasSingleDataset($strSQL, &$arrDataset)
    {
        //$arrDataset = array();
        $booReturn  = true;
        // Reset error variables
        $this->strErrorMessage = '';
        $this->error           = false;
        // Send the SQL statement to the server
        $resQuery = mysqli_query($this->strDBId, $strSQL);
        // Error processing
        if ($resQuery && (mysqli_num_rows($resQuery) != 0) && (mysqli_error($this->strDBId) == '')) {
            // Put the values into the array
            $arrDataset = mysqli_fetch_array($resQuery, MYSQLI_ASSOC);
        } elseif (mysqli_error($this->strDBId) != '') {
            $this->strErrorMessage .= mysqli_error($this->strDBId). '::';
            $this->error            = true;
            $booReturn              = false;
        }
        return $booReturn;
    }

    /**
     * Sends an SQL statement to the server and returns the result of all dataset in a data array
     * @param string $strSQL                    SQL Statement
     * @param array $arrDataset                 Result array (by reference)
     * @param int $intDataCount                 Number of data result sets
     * @return bool                             true = successful / false = error
     *                                          Status messages are stored in class variable
     */
    public function hasDataArray($strSQL, &$arrDataset, &$intDataCount)
    {
        $arrDataset   = array();
        $intDataCount = 0;
        $booReturn    = true;
        // Reset error variables
        $this->strErrorMessage = '';
        $this->error            = false;
        // Send the SQL statement to the server
        $resQuery = mysqli_query($this->strDBId, $strSQL);
        // Error processing
        if ($resQuery && (mysqli_num_rows($resQuery) != 0) && (mysqli_error($this->strDBId) == '')) {
            $intDataCount = mysqli_num_rows($resQuery);
            $intCount = 0;
            // Put the values into the array
            while ($arrDataTemp = mysqli_fetch_array($resQuery, MYSQLI_ASSOC)) {
                foreach ($arrDataTemp as $key => $value) {
                    $arrDataset[$intCount][$key] = $value;
                }
                $intCount++;
            }
        } elseif (mysqli_error($this->strDBId) != '') {
            $this->strErrorMessage .= mysqli_error($this->strDBId). '::';
            $this->error            = true;
            $booReturn              = false;
        }
        return $booReturn;
    }

    /**
     * Insert/update or delete data
     * @param string $strSQL                    SQL Statement
     * @return bool                             true = successful / false = error
     *                                          Status messages are stored in class variable
     */
    public function insertData($strSQL)
    {
        // Reset error variables
        $this->strErrorMessage = '';
        $this->error           = false;
        $booReturn             = false;
        // Send the SQL statement to the server
        if ($strSQL != '') {
            mysqli_query($this->strDBId, $strSQL);
            // Error processing
            if (mysqli_error($this->strDBId) == '') {
                $this->intLastId = mysqli_insert_id($this->strDBId);
                $this->intAffectedRows = mysqli_affected_rows($this->strDBId);
                $booReturn = true;
            } else {
                $this->strErrorMessage .= mysqli_error($this->strDBId) . '::';
                $this->error = true;
            }
        }
        return $booReturn;
    }

    /**
     * Count the sum of data records
     * @param string $strSQL                    SQL Statement
     * @return int                              <number> = successful / 0 = no dataset or error
     *                                          Status messages are stored in class variable
     */
    public function countRows($strSQL)
    {
        // Reset error variables
        $this->strErrorMessage = '';
        $this->error           = false;
        $intReturn             = 0;
        // Send the SQL statement to the server
        $resQuery = mysqli_query($this->strDBId, $strSQL);
        // Error processing
        if ($resQuery && (mysqli_error($this->strDBId) == '')) {
            $intReturn = mysqli_num_rows($resQuery);
        } else {
            $this->strErrorMessage .= mysqli_error($this->strDBId);
            $this->error            = true;
        }
        return $intReturn;
    }

    /**
     * Returns a safe insert string for database manipulations
     * @param string $strInput                  Input String
     * @return string                           Output String
     */
    public function realEscape($strInput)
    {
        return mysqli_real_escape_string($this->strDBId, $strInput);
    }

    /**
     * Initialize a mysql database connection
     * @return bool                             true = successful / false = error
     */
    private function dbinit()
    {
        $this->strDBId = mysqli_init();
        return true;
    }

    /**
     * Connect to database server
     * @param string $dbserver                  Server name
     * @param int $dbport                       TCP port
     * @param string $dbuser                    Database user
     * @param string $dbpasswd                  Database password
     * @return bool                             true = successful / false = error
     *                                          Status messages are stored in class variable
     */
    private function dbconnect($dbserver = null, $dbport = null, $dbuser = null, $dbpasswd = null)
    {
        // Reset error variables
        $this->strErrorMessage = '';
        $this->error           = false;
        $booReturn             = true;
        // Get parameters
        if ($dbserver == null) {
            $dbserver = $this->arrParams['server'];
        }
        if ($dbport   == null) {
            $dbport   = $this->arrParams['port'];
        }
        if ($dbuser   == null) {
            $dbuser   = $this->arrParams['username'];
        }
        if ($dbpasswd == null) {
            $dbpasswd = $this->arrParams['password'];
        }
        // Not all parameters available
        if (($dbserver == '') || ($dbuser == '') || ($dbpasswd == '')) {
            $this->strErrorMessage .= gettext('Missing server connection parameter!'). '::';
            $this->error = true;
            $booReturn   = false;
        }
        if ($booReturn == true) {
            $this->dbinit();
            //if ($this->booSSLuse == true) {
            // TO BE DEFINED
            //}
            $intErrorReporting = error_reporting();
            error_reporting(0);
            if ($dbport == 0) {
                $booReturn = mysqli_real_connect($this->strDBId, $dbserver, $dbuser, $dbpasswd);
            } else {
                $booReturn = mysqli_real_connect($this->strDBId, $dbserver, $dbuser, $dbpasswd, null, $dbport);
            }
            error_reporting($intErrorReporting);
            // Connection fails
            if ($booReturn == false) {
                $this->strErrorMessage  = '[' .$dbserver. '] ' .gettext('Connection to the database server has failed '
                        . 'by reason:'). ' ::';
                $strError = mysqli_connect_error();
                $this->strErrorMessage .= $strError. '::';
                $this->error            = true;
            }
        }
        return $booReturn;
    }

    /**
     * Select a database
     * @param string $database                  Database name
     * @return bool                             true = successful / false = error
     *                                          Status messages are stored in class variable
     */
    private function dbselect($database = null)
    {
        // Reset error variables
        $this->strErrorMessage = '';
        $this->error           = false;
        $booReturn             = true;
        // Get parameters
        if ($database == null) {
            $database = $this->arrParams['database'];
        }
        // Not all parameters available
        if ($database == '') {
            $this->strErrorMessage .= gettext('Missing database connection parameter!'). '::';
            $this->error   = true;
            $booReturn     = false;
        }
        if ($booReturn == true) {
            $bolConnect = mysqli_select_db($this->strDBId, $database);
            // Session cannot be etablished
            if (!$bolConnect) {
                $this->strErrorMessage .= '[' .$database. '] ' .
                    gettext('Connection to the database has failed by reason:'). ' ::';
                $this->strErrorMessage .= mysqli_error($this->strDBId). '::';
                $this->error            = true;
                $booReturn              = false;
            }
        }
        if ($booReturn == true) {
            mysqli_query($this->strDBId, "set names 'utf8'");
            if (mysqli_error($this->strDBId) != '') {
                $this->strErrorMessage .= mysqli_error($this->strDBId). '::';
                $this->error            = true;
                $booReturn              = false;
            }
        }
        if ($booReturn == true) {
            mysqli_query($this->strDBId, "set session sql_mode = 'NO_ENGINE_SUBSTITUTION'");
            if (mysqli_error($this->strDBId) != '') {
                $this->strErrorMessage .= mysqli_error($this->strDBId). '::';
                $this->error            = true;
                $booReturn              = false;
            }
        }
        return $booReturn;
    }

    /*
    /**
     * Set SSL connection parameters
     * @param string $sslkey                    SSL key
     * @param string $sslcert                   SSL certificate
     * @param string $sslca                     SSL CA file (optional)
     * @param string $sslpath                   SSL certificate path (optional)
     * @param string $sslcypher                 SSL cypher (optional)
     * @return bool                             true = successful
     *                                          Status messages are stored in class variable
     */
    /*
    private function dbsetssl($sslkey, $sslcert, $sslca = null, $sslpath = null, $sslcypher = null)
    {
        // Reset error variables
        $this->strErrorMessage = "";
        $this->error           = false;
        $booReturn             = true;
        // Values are missing
        if (($sslkey == "") || ($sslcert == "")) {
            $this->strErrorMessage = gettext("Missing MySQL SSL parameter!")."::";
            $this->error   = true;
            $booReturn     = false;
        }
        if ($booReturn == true) {
            mysqli_ssl_set($this->strDBId, $sslkey, $sslcert, $sslca, $sslpath, $sslcypher);
        }
        return($booReturn);
    }
    */

    /**
     * Close database server connectuon
     * @return bool                             true = successful / false = error
     */
    private function dbDisconnect()
    {
        mysqli_close($this->strDBId);
        return true;
    }
}
