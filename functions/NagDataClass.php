<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2020 by Martin Willisegger
//
// Project   : NagiosQL
// Component : NagiosQL data processing class
// Website   : https://sourceforge.net/projects/nagiosql/
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Class: Data processing class
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Includes all functions used to manipulate the configuration data inside the database
//
// Name: NagDataClass
//
///////////////////////////////////////////////////////////////////////////////////////////////
namespace functions;

class NagDataClass
{
    // Define class variables
    public $arrSession           = array();  // Session content
    public $intDomainId          = 0;        // Configuration domain ID
    public $strUserName          = '';       // Logged in Username
    public $strErrorMessage      = '';       // String including error messages
    public $strInfoMessage       = '';       // String including information messages

    // Class includes
    /** @var MysqliDbClass */
    public $myDBClass;                       // Database class reference
    /** @var NagVisualClass */
    public $myVisClass;                      // NagiosQL visual class object
    /** @var NagConfigClass */
    public $myConfigClass;                   // NagiosQL configuration class object

    /**
     * NagDataClass constructor.
     * @param array $arrSession                 PHP Session array
     */
    public function __construct($arrSession)
    {
        if (isset($arrSession['domain'])) {
            $this->intDomainId = $arrSession['domain'];
        }
        if (isset($arrSession['username'])) {
            $this->strUserName = $arrSession['username'];
        }
        $this->arrSession = $arrSession;
    }

    /**
     * Saving a given string to the logbook
     * @param string $strLogMessage             Message string
     * @return int                              0 = successful / 1 = error
     */
    public function writeLog($strLogMessage)
    {
        // Variable definition
        $strRemoteAdress = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
        $intReturn       = 0;
        // Write log message to database
        if ($strRemoteAdress != null) {
            // Webinterface
            $strUserName = $this->strUserName;
            $strDomain   = $this->myDBClass->getFieldData('SELECT `domain` FROM `tbl_datadomain` ' .
                'WHERE `id`=' .$this->intDomainId);
            $booReturn   = $this->myDBClass->insertData("INSERT INTO `tbl_logbook` SET `user`='".$strUserName."',".
                "`time`=NOW(), `ipadress`='".$strRemoteAdress."', `domain`='$strDomain',".
                "`entry`='".addslashes($strLogMessage)."'");
            if ($booReturn == false) {
                $intReturn = 1;
            }
        } else {
            // Scriptinginterface
            $strUserName   = 'scripting';
            $strRemoteUser = filter_input(INPUT_SERVER, 'REMOTE_USER', FILTER_SANITIZE_STRING);
            $strHostname   = filter_input(INPUT_SERVER, 'REMOTE_HOST', FILTER_SANITIZE_STRING);
            $strSSHClient  = filter_input(INPUT_SERVER, 'SSH_CLIENT', FILTER_SANITIZE_STRING);
            if ($strRemoteUser != null) {
                $strUserName .= ' - ' .$strRemoteUser;
            }
            $strDomain = $this->myDBClass->getFieldData('SELECT `domain` FROM `tbl_datadomain` ' .
                'WHERE `id`=' .$this->intDomainId);
            if ($strHostname != null) {
                $booReturn = $this->myDBClass->insertData("INSERT INTO `tbl_logbook` SET `user`='".$strUserName."',".
                    "`time`=NOW(), `ipadress`='".$strHostname."', `domain`='$strDomain', ".
                    "`entry`='".addslashes($strLogMessage)."'");
            } elseif ($strSSHClient != null) {
                $arrSSHClient = explode(' ', $strSSHClient);
                $booReturn = $this->myDBClass->insertData("INSERT INTO `tbl_logbook` SET `user`='".$strUserName."',".
                    "`time`=NOW(), `ipadress`='".$arrSSHClient[0]."', `domain`='$strDomain', ".
                    "`entry`='".addslashes($strLogMessage)."'");
            } else {
                $booReturn = $this->myDBClass->insertData("INSERT INTO `tbl_logbook` SET `user`='".$strUserName."',".
                    "`time`=NOW(), `ipadress`='unknown', `domain`='$strDomain', ".
                    "`entry`='".addslashes($strLogMessage)."'");
            }
            if ($booReturn == false) {
                $intReturn = 1;
            }
        }
        return $intReturn;
    }

    /**
     * Sends an SQL string to the database server
     * @param string $strSQL                    SQL Command
     * @param int $intDataID                    Data ID of last inserted dataset (by reference)
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function dataInsert($strSQL, &$intDataID)
    {
        //Define variables
        $intReturn = 0;
        // Send the SQL command to the database server
        $booReturn = $this->myDBClass->insertData($strSQL);
        $intDataID = $this->myDBClass->intLastId;
        // Was the SQL command processed successfully?
        if ($booReturn) {
            $this->processClassMessage(translate('Data were successfully inserted to the data base!').
                '::', $this->strInfoMessage);
        } else {
            $this->processClassMessage(translate('Error while inserting the data into the database:').
                '::' .$this->myDBClass->strErrorMessage. '::', $this->strErrorMessage);
            $intReturn = 1;
        }
        return $intReturn;
    }

    /**
     * Merge message strings and check for duplicate messages
     * @param string $strNewMessage             New message to add
     * @param string $strOldMessage             Modified message string (by reference)
     */
    public function processClassMessage($strNewMessage, &$strOldMessage)
    {
        $strNewMessage = str_replace('::::', '::', $strNewMessage);
        if (($strOldMessage != '') && ($strNewMessage != '')) {
            if (substr_count($strOldMessage, $strNewMessage) == 0) {
                $strOldMessage .= $strNewMessage;
            }
        } else {
            $strOldMessage .= $strNewMessage;
        }
    }

    /**
     * Copies one or more records in a data table. Alternatively, an individual record ID
     * are specified, or the values of the $_POST['chbId_n'] variable is used where n
     * is the record ID.
     * @param string $strTableName              Table name
     * @param string $strKeyField               Key field of the table
     * @param int $intDataId                    Single data ID to copy
     * @param int $intDomainId                  Target domain ID
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function dataCopyEasy($strTableName, $strKeyField, $intDataId = 0, $intDomainId = -1)
    {
        // Define variables
        $arrRelations = array();
        $intError     = 0;
        $intNumber    = 0;
        $intReturn    = 0;
        $strAccess    = $this->myVisClass->getAccessGroups('write');
        if ($intDomainId == -1) {
            $intDomainId = $this->intDomainId;
        }
        // Get all data ID from target table
        $strAccWhere = "WHERE `access_group` IN ($strAccess)";
        if (($strTableName == 'tbl_user') || ($strTableName == 'tbl_group')) {
            $strAccWhere = '';
        }
        $strSQL    = 'SELECT `id` FROM `' .$strTableName."` $strAccWhere ORDER BY `id`";
        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if ($booReturn == false) {
            $this->processClassMessage(translate('Error while selecting data from database:').
                '::' .$this->myDBClass->strErrorMessage. '::', $this->strErrorMessage);
            return 1;
        }
        if ($intDataCount != 0) {
            for ($i=0; $i<$intDataCount; $i++) {
                // Skip common domain value
                if ($arrData[$i]['id'] == 0) {
                    continue;
                }
                // Build the name of the form variable
                $strChbName = 'chbId_' .$arrData[$i]['id'];
                // If a form variable with this name exists or a matching single data ID was passed
                if (((filter_input(INPUT_POST, $strChbName, FILTER_SANITIZE_STRING) != null) && ($intDataId == 0)) ||
                    ($intDataId == $arrData[$i]['id'])) {
                    // Get all data of this data ID
                    $strSQL = 'SELECT * FROM `' .$strTableName. '` WHERE `id`=' .$arrData[$i]['id'];
                    $this->myDBClass->hasSingleDataset($strSQL, $arrData[$i]);
                    // Build a temporary config name
                    $strNewName = $this->buildTempConfigName(
                        $strTableName,
                        $strKeyField,
                        $intDomainId,
                        $intDataCount,
                        $arrData,
                        $i
                    );
                    // Build the INSERT command based on the table name
                    $strSQLInsert = $this->buildInsertSQL(
                        $strTableName,
                        $strKeyField,
                        $intDomainId,
                        $strNewName,
                        $arrData,
                        $i
                    );
                    // Insert the master dataset
                    $intCheck    = 0;
                    $booReturn   = $this->myDBClass->insertData($strSQLInsert);
                    $intMasterId = $this->myDBClass->intLastId;
                    if ($booReturn == false) {
                        $intCheck++;
                    }
                    // Copy relations
                    if (($this->tableRelations($strTableName, $arrRelations) == 0) && ($intCheck == 0)) {
                        foreach ($arrRelations as $elem) {
                            // Normal 1:n relation
                            if ($elem['type'] == '2') {
                                $intCheck = $this->insertRelationType2($arrData, $i, $elem, $intMasterId, $intCheck);
                            } elseif ($elem['type'] == '3') { // 1:n relation for templates
                                $intCheck = $this->insertRelationType3($arrData, $i, $elem, $intMasterId, $intCheck);
                            } elseif ($elem['type'] == '4') { // Special relation for free variables
                                $intCheck = $this->insertRelationType4($arrData, $i, $elem, $intMasterId, $intCheck);
                            } elseif ($elem['type'] == '5') { // 1:n relation for tbl_lnkServicegroupToService
                                $intCheck = $this->insertRelationType5($arrData, $i, $elem, $intMasterId, $intCheck);
                            } elseif ($elem['type'] == '6') { // 1:n relation for services
                                $intCheck = $this->insertRelationType6($arrData, $i, $elem, $intMasterId, $intCheck);
                            }
                        }
                        // 1:n relation for time definitions
                        if ($strTableName == 'tbl_timeperiod') {
                            $intCheck = $this->insertRelationTimedefinition($arrData, $i, $intMasterId, $intCheck);
                        }
                        // 1:n relation for groups
                        if ($strTableName == 'tbl_group') {
                            $intCheck = $this->insertRelationGroup($arrData, $i, $intMasterId, $intCheck);
                        }
                        // 1:n relation fot service to host connections
                        if ($strTableName == 'tbl_host') {
                            $intCheck = $this->insertRelationHost($arrData, $i, $intMasterId, $intCheck);
                        }
                    }
                    // Write logfile
                    if ($intCheck != 0) {
                        // Error
                        $intError++;
                        $this->writeLog(translate('Data set copy failed - table [new name]:'). ' ' .$strTableName
                            . ' [' .$strNewName. ']');
                        $this->processClassMessage(translate('Data set copy failed - table [new name]:'). ' ' .
                            $strTableName. ' [' .$strNewName. ']::', $this->strInfoMessage);
                    } else {
                        // Success
                        $this->writeLog(translate('Data set copied - table [new name]:'). ' ' .$strTableName.
                            ' [' .$strNewName. ']');
                        $this->processClassMessage(translate('Data set copied - table [new name]:'). ' ' .
                            $strTableName. ' [' .$strNewName. ']::', $this->strInfoMessage);
                    }
                    $intNumber++;
                }
            }
            // Error processing
            if ($intNumber > 0) {
                if ($intError == 0) {
                    // Success
                    $this->processClassMessage(translate('Data were successfully inserted to the data base!')
                        . '::', $this->strInfoMessage);
                    $this->updateStatusTable($strTableName);
                } else {
                    // Error
                    $this->processClassMessage(translate('Error while inserting the data into the database:')
                        . '::' .$this->myDBClass->strErrorMessage, $this->strInfoMessage);
                    $intReturn = 1;
                }
            } else {
                $this->processClassMessage(translate('No dataset copied. Maybe the dataset does not exist or you do '.
                        'not have write permission.'). '::', $this->strErrorMessage);
                $intReturn = 1;
            }
        } else {
            $this->processClassMessage(translate('No dataset copied. Maybe the dataset does not exist or you do not '.
                    'have write permission.'). '::', $this->strErrorMessage);
            $intReturn = 1;
        }
        return $intReturn;
    }

    /**
     * Returns an array of all datafields of a table, which has an 1:1 or 1:n relation
     * to another table.
     * @param string $strTable                  Table name
     * @param array $arrRelations               Array with relations
     * @return int                              0 = successful / 1 = error
     */
    public function tableRelations($strTable, &$arrRelations)
    {
        // Define variable
        $arrRelations = array();
        $arrData      = array();
        $intDC        = 0;
        $intReturn    = 1;
        // Get relation data
        $strSQL    = "SELECT * FROM `tbl_relationinformation` WHERE `master`='$strTable' AND `fullRelation`=0";
        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDC);
        if ($booReturn && ($intDC != 0)) {
            foreach ($arrData as $elem) {
                $arrRelations[] = array('tableName1' => $elem['tableName1'], 'tableName2' => $elem['tableName2'],
                    'fieldName'  => $elem['fieldName'],  'linkTable'  => $elem['linkTable'],
                    'target1'    => $elem['target1'],    'target2'    => $elem['target2'],
                    'type'       => $elem['type']);
            }
            $intReturn = 0;
        }
        return $intReturn;
    }

    /**
     * Update the date inside the status table (used for last modified date)
     * @param string $strTable                  Table name
     * @return int                              0 = successful / 1 = error
     */
    public function updateStatusTable($strTable)
    {
        // Define variable
        $arrData      = array();
        $intDC        = 0;
        $intReturn    = 1;
        // Does the entry exist?
        $strSQL    = "SELECT * FROM `tbl_tablestatus` WHERE `tableName`='$strTable' AND `domainId`=".$this->intDomainId;
        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDC);
        if ($booReturn && ($intDC != 0)) {
            $strSQL    = 'UPDATE `tbl_tablestatus` SET `updateTime`=NOW() ' .
                "WHERE `tableName`='$strTable' AND `domainId`=".$this->intDomainId;
            $booReturn = $this->dataInsert($strSQL, $intDataID);
            if ($booReturn) {
                $intReturn = 0;
            }
        } elseif ($booReturn) {
            $strSQL    = 'INSERT INTO `tbl_tablestatus` ' .
                "SET `updateTime`=NOW(), `tableName`='$strTable', `domainId`=".$this->intDomainId;
            $booReturn = $this->dataInsert($strSQL, $intDataID);
            if ($booReturn) {
                $intReturn = 0;
            }
        }
        return $intReturn;
    }

    /**
     * Removes one or more dataset(s) from a table. Optinal a single data ID can be passed or the values will be
     * processed through the POST variable $_POST['chbId_n'] where 'n' represents the data ID.
     * -> This function does not delete any relation data! <-
     * @param string $strTableName              Table name
     * @param int $intDataId                    Single data ID
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function dataDeleteEasy($strTableName, $intDataId = 0)
    {
        // Define variables
        $strNoDelete = '';
        $intReturn   = 0;
        $arrData     = array();
        // Special rule for tables with "nodelete" cells
        if (($strTableName == 'tbl_datadomain') || ($strTableName == 'tbl_configtarget') ||
            ($strTableName == 'tbl_user')) {
            $strNoDelete = "AND `nodelete` <> '1'";
        }
        // Delete a single data set
        if ($intDataId != 0) {
            $strSQL    = 'DELETE FROM `' .$strTableName."` WHERE `id` = $intDataId $strNoDelete";
            $booReturn = $this->myDBClass->insertData($strSQL);
            if ($booReturn == false) {
                $this->processClassMessage(translate('Delete failed because a database error:').
                    '::' .$this->myDBClass->strErrorMessage. '::', $this->strInfoMessage);
                $intReturn = 1;
            } elseif ($this->myDBClass->intAffectedRows == 0) {
                $this->processClassMessage(translate('No data deleted. The dataset probably does not exist or '.
                        'is protected from deletion.'). '::', $this->strErrorMessage);
                $intReturn = 1;
            } else {
                $this->strInfoMessage .= translate('Dataset successfully deleted. Affected rows:'). ' ' .
                    $this->myDBClass->intAffectedRows. '::';
                $this->writeLog(translate('Delete dataset id:')." $intDataId ".translate('- from table:').
                    " $strTableName ".translate('- with affected rows:'). ' ' .$this->myDBClass->intAffectedRows);
                $this->updateStatusTable($strTableName);
            }
            // Delete data sets based on form POST parameter
        } else {
            $strSQL    = 'SELECT `id` FROM `' .$strTableName. '` WHERE 1=1 ';
            $strSQL   .= $strNoDelete;
            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
            if ($booReturn && ($intDataCount != 0)) {
                $intDeleteCount = 0;
                foreach ($arrData as $elem) {
                    $strChbName = 'chbId_' .$elem['id'];
                    // Should this data id to be deleted?
                    if ((filter_input(INPUT_POST, $strChbName) != null) &&
                        (filter_input(INPUT_POST, $strChbName, FILTER_SANITIZE_STRING) == 'on')) {
                        $strSQL = 'DELETE FROM `' .$strTableName. '` WHERE `id` = ' .$elem['id'];
                        $booReturn = $this->myDBClass->insertData($strSQL);
                        if ($booReturn == false) {
                            $this->processClassMessage(translate('Delete failed because a database error:').
                                '::' .$this->myDBClass->strErrorMessage. '::', $this->strInfoMessage);
                            $intReturn = 1;
                        } else {
                            $intDeleteCount += $this->myDBClass->intAffectedRows;
                        }
                    }
                }
                // Process messsages
                if ($intDeleteCount == 0) {
                    $this->processClassMessage(translate('No data deleted. Probably the dataset does not exist or '.
                            'it is protected from delete.'). '::', $this->strErrorMessage);
                    $intReturn = 1;
                } elseif ($intReturn == 0) {
                    $this->processClassMessage(translate('Dataset successfully deleted. Affected rows:'). ' ' .
                        $intDeleteCount. '::', $this->strInfoMessage);
                    $this->writeLog(translate('Deleted data from table:')." $strTableName ".
                        translate('- with affected rows:'). ' ' .$this->myDBClass->intAffectedRows);
                    $this->updateStatusTable($strTableName);
                }
            } else {
                $this->processClassMessage(translate('No data deleted. Probably the dataset does not exist or it is '.
                        'protected from delete.'). '::', $this->strErrorMessage);
                $intReturn = 1;
            }
        }
        return $intReturn;
    }

    /**
     * Removes one or more dataset(s) from a table. Optinal a single data ID can be passed or the values will be
     * processed through the POST variable $_POST['chbId_n'] where 'n' represents the data ID.
     * -> This function does also delete relation data! <-
     * @param string $strTableName              Table name
     * @param int $intDataId                    Single data ID
     * @param int $intForce                     Force deletion (1 = force, 1 = no force)
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function dataDeleteFull($strTableName, $intDataId = 0, $intForce = 0)
    {
        // Define variables
        $arrRelations = array();
        $arrData      = array();
        $arrConfigId  = array();
        // Get write access groups
        $strAccess = $this->myVisClass->getAccessGroups('write');
        // Get all relations
        $this->fullTableRelations($strTableName, $arrRelations);
        // Get all datasets
        if ($strTableName == 'tbl_group') {
            $strSQL = 'SELECT `id` FROM `' .$strTableName. '`';
        } else {
            $strSQL = 'SELECT `id` FROM `' .$strTableName. '` ' .
                'WHERE `config_id`=' .$this->intDomainId." AND `access_group` IN ($strAccess)";
        }
        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if ($booReturn && ($intDataCount != 0)) {
            $intDeleteCount  = 0;
            $strInfoMessage  = '';
            $strErrorMessage = '';

            foreach ($arrData as $elem) {
                $strChbName = 'chbId_' .$elem['id'];
                // Single ID
                if (($intDataId != 0) && ($intDataId != $elem['id'])) {
                    continue;
                }
                // Should this data id to be deleted?
                if ((($intDataId == $elem['id']) || ((filter_input(INPUT_POST, $strChbName) != null) &&
                            (filter_input(INPUT_POST, $strChbName, FILTER_SANITIZE_STRING) == 'on'))) &&
                    (($this->infoRelation($strTableName, $elem['id'], 'id', 1) == 0) || ($intForce == 1))) {
                    // Delete relations
                    if (!\is_array($arrRelations)) {
                        $arrRelations = array();
                    }
                    foreach ($arrRelations as $rel) {
                        $strSQL = '';
                        // Process flags
                        $arrFlags = explode(',', $rel['flags']);
                        // Simple 1:n relation
                        if ($arrFlags[3] == 1) {
                            $strSQL = 'DELETE FROM `' .$rel['tableName1']. '` ' .
                                'WHERE `' .$rel['fieldName']. '`=' .$elem['id'];
                        }
                        // Simple 1:1 relation
                        if ($arrFlags[3] == 0) {
                            // Delete relation
                            if ($arrFlags[2] == 0) {
                                $strSQL = 'DELETE FROM `' .$rel['tableName1']. '` ' .
                                    'WHERE `' .$rel['fieldName']. '`=' .$elem['id'];
                                // Set slave to 0
                            } elseif ($arrFlags[2] == 2) {
                                $strSQL = 'UPDATE `' .$rel['tableName1']. '` SET `' .$rel['fieldName']. '`=0 ' .
                                    'WHERE `' .$rel['fieldName']. '`=' .$elem['id'];
                            }
                        }
                        // Special 1:n relation for variables
                        if ($arrFlags[3] == 2) {
                            $strSQL    = 'SELECT * FROM `' .$rel['tableName1']. '` WHERE `idMaster`=' .$elem['id'];
                            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
                            if ($booReturn && ($intDataCount != 0)) {
                                foreach ($arrData as $vardata) {
                                    $strSQL = 'DELETE FROM `tbl_variabledefinition` ' .
                                        'WHERE `id`=' .$vardata['idSlave'];
                                    $this->myDBClass->insertData($strSQL);
                                }
                            }
                            $strSQL = 'DELETE FROM `' .$rel['tableName1']. '` WHERE `idMaster`=' .$elem['id'];
                        }
                        // Special 1:n relation for time definitions
                        if ($arrFlags[3] == 3) {
                            $strSQL = 'DELETE FROM `tbl_timedefinition` WHERE `tipId`=' .$elem['id'];
                            $this->myDBClass->insertData($strSQL);
                        }
                        if ($strSQL != '') {
                            $this->myDBClass->insertData($strSQL);
                        }
                    }
                    // Delete host configuration file
                    if (($strTableName == 'tbl_host') && ($this->intDomainId != 0)) {
                        $strSQL    = 'SELECT `host_name` FROM `tbl_host` WHERE `id`=' .$elem['id'];
                        $strHost   = $this->myDBClass->getFieldData($strSQL);
                        $this->myConfigClass->getConfigSets($arrConfigId);
                        if ($arrConfigId != 1) {
                            $intReturn = 0;
                            foreach ($arrConfigId as $intConfigId) {
                                $intReturn += $this->myConfigClass->moveFile(
                                    'host',
                                    $strHost. '.cfg',
                                    $intConfigId
                                );
                            }
                            if ($intReturn == 0) {
                                $this->processClassMessage(translate('The assigned, no longer used configuration '.
                                        'files were deleted successfully!'). '::', $strInfoMessage);
                                $this->writeLog(translate('Host file deleted:'). ' ' .$strHost. '.cfg');
                            } else {
                                $strErrorMessage .= translate('Errors while deleting the old configuration file - '.
                                        'please check!:'). ' ::' .$this->myConfigClass->strErrorMessage . '::';
                            }
                        }
                    }
                    // Delete service configuration file
                    if (($strTableName == 'tbl_service') && ($this->intDomainId != 0)) {
                        $strSQL     = 'SELECT `config_name` FROM `tbl_service` WHERE `id`=' .$elem['id'];
                        $strService = $this->myDBClass->getFieldData($strSQL);
                        $strSQL     = "SELECT * FROM `tbl_service` WHERE `config_name` = '$strService'";
                        $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
                        if ($intDataCount == 1) {
                            $this->myConfigClass->getConfigSets($arrConfigId);
                            if ($arrConfigId != 1) {
                                $intReturn = 0;
                                foreach ($arrConfigId as $intConfigId) {
                                    $intReturn += $this->myConfigClass->moveFile(
                                        'service',
                                        $strService. '.cfg',
                                        $intConfigId
                                    );
                                }
                                if ($intReturn == 0) {
                                    $this->processClassMessage(translate('The assigned, no longer used '.
                                            'configuration files were deleted successfully!').
                                        '::', $strInfoMessage);
                                    $this->writeLog(translate('Host file deleted:'). ' ' .$strService. '.cfg');
                                } else {
                                    $strErrorMessage .=  translate('Errors while deleting the old configuration '.
                                            'file - please check!:'). '::' .
                                        $this->myConfigClass->strErrorMessage. '::';
                                }
                            }
                        }
                    }
                    // Delete main entry
                    $strSQL = 'DELETE FROM `' .$strTableName. '` WHERE `id`=' .$elem['id'];
                    $this->myDBClass->insertData($strSQL);
                    $intDeleteCount++;
                }
            }
            // Process messages
            if ($intDeleteCount == 0) {
                $this->processClassMessage(translate('No data deleted. Probably the dataset does not exist, it is '.
                        'protected from deletion, you do not have write permission or it has relations to other '.
                        'configurations which cannot be deleted. Use the "info" function for detailed informations '.
                        'about relations!'). '::', $this->strErrorMessage);
                return 1;
            }

            $this->updateStatusTable($strTableName);
            $this->processClassMessage(translate('Dataset successfully deleted. Affected rows:'). ' ' .
                $intDeleteCount. '::', $this->strInfoMessage);
            $this->writeLog(translate('Deleted data from table:')." $strTableName ".
                translate('- with affected rows:'). ' ' .$intDeleteCount);
            $this->processClassMessage($strInfoMessage, $this->strInfoMessage);
            $this->processClassMessage($strErrorMessage, $this->strErrorMessage);
            return 0;
        }
        $this->processClassMessage(translate('No data deleted. Probably the dataset does not exist, it is '.
                'protected from deletion or you do not have write permission.'). '::' .
            $this->myDBClass->strErrorMessage, $this->strErrorMessage);
        return 1;
    }

    /**
     * Returns an array with any data fields from a table with existing relations to another table. This function
     * returns also passive relations which are not used in configurations.
     * This function is used for a full deletion of a configuration entry or to find out if a configuration is used
     * in another way.
     * @param string $strTable                  Table name
     * @param array $arrRelations               Array with relations
     * @return int                              0 = no field with relation / 1 = at least one field with relation
     *                                          Status message is stored in message class variables
     * Data array:      tableName               Table include the relation data
     *                  fieldName               Field name include the relation data
     *                  flags                   Pos 1 -> 0=Normal field / 1=Required field      (field type)
     *                                          Pos 2 -> 0=delete / 1=keep data / 2=set to 0    (normal deletion option)
     *                                          Pos 3 -> 0=delete / 2=set to 0                  (force deletion option)
     *                                          Pos 4 -> 0=1:1 / 1=1:n /                        (relation type)
     *                                                   2=1:n (variables) / 3=1:n              (timedef)
     */
    public function fullTableRelations($strTable, &$arrRelations)
    {
        // Define variable
        $arrRelations = array();
        $arrData      = array();
        $intDC        = 0;
        $intReturn    = 0;
        // Get relation data
        $strSQL    = "SELECT * FROM `tbl_relationinformation` WHERE `master`='$strTable' AND `fullRelation`=1";
        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDC);
        if ($booReturn && ($intDC != 0)) {
            foreach ($arrData as $elem) {
                $arrRelations[] = array('tableName1' => $elem['tableName1'], 'fieldName' => $elem['fieldName'],
                    'target1'    => $elem['target1'],    'targetKey' => $elem['targetKey'],
                    'flags'      => $elem['flags']);
            }
            $intReturn = 1;
        }
        return $intReturn;
    }

    /**
     * Searches any relation in the database and returns them as relation information
     * @param string $strTable                  Database table name
     * @param int $intMasterId                  Data ID from master table
     * @param string $strMasterfield            Info field name from master table
     * @param int $intReporting                 Output as text - 0=yes, 1=no
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function infoRelation($strTable, $intMasterId, $strMasterfield, $intReporting = 0)
    {
        $intDeletion  = 0;
        $arrDSCount   = array();
        $arrRelations = array();
        $arrData      = array();
        $arrDataCheck = array();
        $intReturn    = $this->fullTableRelations($strTable, $arrRelations);
        if ($intReturn == 1) {
            // Get master field data
            $strNewMasterfield = str_replace(',', '`,`', $strMasterfield);
            $strSQL            = 'SELECT `' .$strNewMasterfield. '` FROM `' .$strTable."` WHERE `id` = $intMasterId";
            $this->myDBClass->hasSingleDataset($strSQL, $arrSource);
            if (substr_count($strMasterfield, ',') != 0) {
                $arrTarget = explode(',', $strMasterfield);
                $strName   = $arrSource[$arrTarget[0]]. '-' .$arrSource[$arrTarget[1]];
            } else {
                $strName   = $arrSource[$strMasterfield];
            }
            $this->strInfoMessage .= '<span class="blackmessage">' .translate('Relation information for <b>').
                $strName.translate('</b> of table <b>').$strTable. ':</b></span>::';
            $this->strInfoMessage .= '<span class="bluemessage">';
            // Walk through relations
            foreach ($arrRelations as $elem) {
                // Process flags
                $arrFlags = explode(',', $elem['flags']);
                if ($elem['fieldName'] == 'check_command') {
                    $strSQL = 'SELECT * FROM `' .$elem['tableName1']. '` ' .
                        'WHERE SUBSTRING_INDEX(`' .$elem['fieldName']."`,'!',1)= $intMasterId";
                } else {
                    $strSQL = 'SELECT * FROM `' .$elem['tableName1']. '` WHERE `' .$elem['fieldName']."`= $intMasterId";
                }
                $booReturn  = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
                // Take only used relations
                if ($booReturn && ($intDataCount != 0)) {
                    // Relation type
                    if ($arrFlags[3] == 1) {
                        foreach ($arrData as $data) {
                            if ($elem['fieldName'] == 'idMaster') {
                                $strRef = 'idSlave';
                                // Process special tables
                                if ($elem['target1'] == 'tbl_service') {
                                    if ($elem['tableName1'] == 'tbl_lnkServicegroupToService') {
                                        $strRef = 'idSlaveS';
                                    }
                                } elseif ($elem['target1'] == 'tbl_host') {
                                    if ($elem['tableName1'] == 'tbl_lnkServicegroupToService') {
                                        $strRef = 'idSlaveH';
                                    }
                                } elseif ($elem['target1'] == 'tbl_hostgroup') {
                                    if ($elem['tableName1'] == 'tbl_lnkServicegroupToService') {
                                        $strRef = 'idSlaveHG';
                                    }
                                }
                            } else {
                                $strRef = 'idMaster';
                            }
                            // Get data
                            $strSQL = 'SELECT * FROM `' .$elem['tableName1']. '` ' .
                                'LEFT JOIN `' .$elem['target1']. '` ON `' .$strRef. '` = `id` ' .
                                'WHERE `' .$elem['fieldName']. '` = ' .$data[$elem['fieldName']]. ' ' .
                                'AND `' .$strRef. '`=' .$data[$strRef];
                            $this->myDBClass->hasSingleDataset($strSQL, $arrDSTarget);
                            if (substr_count($elem['targetKey'], ',') != 0) {
                                $arrTarget = explode(',', $elem['targetKey']);
                                $strTarget = $arrDSTarget[$arrTarget[0]]. '-' .$arrDSTarget[$arrTarget[1]];
                            } else {
                                $strTarget = $arrDSTarget[$elem['targetKey']];
                            }
                            // If the field is market as "required", check for any other entries
                            if ($arrFlags[0] == 1) {
                                $strSQL = 'SELECT * FROM `' .$elem['tableName1']. '` ' .
                                    'WHERE `' .$strRef. '` = ' .$arrDSTarget[$strRef];
                                $booReturn  = $this->myDBClass->hasDataArray($strSQL, $arrDSCount, $intDCCount);
                                if ($booReturn && ($intDCCount > 1)) {
                                    $this->strInfoMessage .= translate('Relation to <b>').$elem['target1'].
                                        translate('</b>, entry <b>').$strTarget.
                                        '</b> - <span style="color:#00CC00;">' .translate('deletion <b>possible</b>').
                                        '</span>::';
                                } else {
                                    $this->strInfoMessage .= translate('Relation to <b>').$elem['target1'].
                                        translate('</b>, entry <b>').$strTarget.
                                        '</b> - <span style="color:#FF0000;">' .
                                        translate('deletion <b>not possible</b>'). '</span>::';
                                    $intDeletion = 1;
                                }
                            } else {
                                $this->strInfoMessage .= translate('Relation to <b>').$elem['target1'].
                                    translate('</b>, entry <b>').$strTarget. '</b> - <span style="color:#00CC00;">' .
                                    translate('deletion <b>possible</b>'). '</span>::';
                            }
                        }
                    } elseif ($arrFlags[3] == 0) {
                        // Fetch remote entry
                        $strSQL = 'SELECT * FROM `' .$elem['tableName1']. '` '
                            . 'WHERE `' .$elem['fieldName']."`=$intMasterId";
                        $booReturn  = $this->myDBClass->hasDataArray($strSQL, $arrDataCheck, $intDCCheck);
                        if ($booReturn && ($intDCCheck != 0)) {
                            foreach ($arrDataCheck as $data) {
                                if (substr_count($elem['targetKey'], ',') != 0) {
                                    $arrTarget = explode(',', $elem['targetKey']);
                                    $strTarget = $data[$arrTarget[0]]. '-' .$data[$arrTarget[1]];
                                } else {
                                    $strTarget = $data[$elem['targetKey']];
                                }
                                if ($arrFlags[0] == 1) {
                                    $this->strInfoMessage .= translate('Relation to <b>').$elem['tableName1'].
                                        translate('</b>, entry <b>').$strTarget.
                                        '</b> - <span style="color:#FF0000;">' .
                                        translate('deletion <b>not possible</b>'). '</span>::';
                                    $intDeletion = 1;
                                } else {
                                    $this->strInfoMessage .= translate('Relation to <b>').$elem['tableName1'].
                                        translate('</b>, entry <b>').$strTarget.
                                        '</b> - <span style="color:#00CC00;">' .
                                        translate('deletion <b>possible</b>'). '</span>::';
                                }
                            }
                        }
                    }
                }
            }
            $this->strInfoMessage .= '</span>::';
        }
        if ($intReporting == 1) {
            $this->strInfoMessage = '';
        }
        return $intDeletion;
    }

    /**
     * Inserts any necessary dataset for an 1:n (optional 1:n:n) relation to the database table
     * @param string $strTable                  Database table name
     * @param int $intMasterId                  Data ID from master table
     * @param array $arrSlaveId                 Array with all data IDs from slave table
     * @param int $intMulti                     0 = for 1:n relations
     *                                          1 = for 1:n:n relations
     * @return int                              0 = successful / 1 = error
     */
    public function dataInsertRelation($strTable, $intMasterId, $arrSlaveId, $intMulti = 0)
    {
        // Define variables
        $intReturn = 0;
        $intDataId = 0;
        $strSQL    = '';
        // Walk through the slave data ID array
        foreach ($arrSlaveId as $elem) {
            // Pass empty and '*' values
            if ($elem == '0') {
                continue;
            }
            if ($elem == '*') {
                continue;
            }
            // Process exclude values
            if (0 === strpos($elem, 'e')) {
                $elem       = str_replace('e', '', $elem);
                $intExclude = 1;
            } else {
                $intExclude = 0;
            }
            // Define the SQL statement
            if ($intMulti != 0) {
                $arrValues = explode('::', $elem);
                $strSQL    = 'INSERT INTO `' .$strTable."` SET `idMaster`=$intMasterId, `idSlaveH`=".$arrValues[0]
                    . ', `idSlaveHG`=' .$arrValues[1]. ', `idSlaveS`=' .$arrValues[2].",  `exclude`=$intExclude";
            } else {
                if (($strTable == 'tbl_lnkServicedependencyToService_DS') ||
                    ($strTable == 'tbl_lnkServicedependencyToService_S')  ||
                    ($strTable == 'tbl_lnkServiceescalationToService')) {
                    // Get service description
                    $strSQLSrv  = "SELECT `service_description` FROM `tbl_service` WHERE id=$elem";
                    $strService = $this->myDBClass->getFieldData($strSQLSrv);
                    $strSQL     = 'INSERT INTO `' .$strTable."` SET `idMaster`=$intMasterId, `idSlave`=$elem, ".
                        "`strSlave`='".addslashes($strService)."', `exclude`=$intExclude";
                } elseif (($strTable == 'tbl_lnkServiceToService') ||
                    ($strTable == 'tbl_lnkServicetemplateToService')) {
                    $arrValues = explode('-', $elem);
                    if (isset($arrValues[0]) && isset($arrValues[1])) {
                        $strSQL = 'INSERT INTO `' .$strTable."` SET `idMaster`=$intMasterId, `idSlave`=$arrValues[0], "
                            ." `idHost`=$arrValues[1]";
                    }
                } elseif (($strTable != 'tbl_lnkTimeperiodToTimeperiod') &&
                    ($strTable != 'tbl_lnkDatadomainToConfigtarget')) {
                    $strSQL = 'INSERT INTO `' . $strTable . '` ' .
                        "SET `idMaster`=$intMasterId, `idSlave`=$elem, `exclude`=$intExclude";
                } else {
                    $strSQL = 'INSERT INTO `' .$strTable."` SET `idMaster`=$intMasterId, `idSlave`=$elem";
                }
            }
            // Insert data
            $intReturn = $this->dataInsert($strSQL, $intDataId);
            if ($intReturn != 0) {
                $intReturn = 1;
            }
        }
        return $intReturn;
    }

    /**
     * Update the datasets for 1:n (optional 1:n:m) relations in the database table
     * @param string $strTable                  Database table name
     * @param int $intMasterId                  Data ID from master table
     * @param array $arrSlaveId                 Array with all data IDs from slave table
     * @param int $intMulti                     0 = for 1:n relations
     *                                          1 = for 1:n:n relations
     * @return int                              0 = successful / 1 = error
     */
    public function dataUpdateRelation($strTable, $intMasterId, $arrSlaveId, $intMulti = 0)
    {
        $intReturn = 0;
        // Remove any old relations
        $intReturn1 = $this->dataDeleteRelation($strTable, $intMasterId);
        if ($intReturn1 != 0) {
            $intReturn = 1;
        }
        // Insert the new relations
        if ($intReturn == 0) {
            $intReturn2 = $this->dataInsertRelation($strTable, $intMasterId, $arrSlaveId, $intMulti);
            if ($intReturn2 != 0) {
                $intReturn = 1;
            }
        }
        return $intReturn;
    }

    /**
     * Removes any relation from the database
     * @param string $strTable                  Database table name
     * @param int $intMasterId                  Data ID from master table
     * @return int                              0 = successful / 1 = error
     */
    public function dataDeleteRelation($strTable, $intMasterId)
    {
        // Define variables
        $intDataId = 0;
        // Define the SQL statement
        $strSQL    = 'DELETE FROM `' .$strTable."` WHERE `idMaster`=$intMasterId";
        return $this->dataInsert($strSQL, $intDataId);
    }

    /**
     * Deactivates one or many datasets in the table be setting 'active' to '0'. Alternatively, a single record
     * ID can be specified or evaluated by the values of $_POST['chbId_n'] passed parameters, where n is the
     * record ID must match.
     * @param string $strTableName              Table name
     * @param int $intDataId                    Individual record ID, which is to be activate
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function dataDeactivate($strTableName, $intDataId = 0)
    {
        // Define variables
        $intReturn = 1;
        $arrData   = array();
        // Get write access groups
        $strAccess = $this->myVisClass->getAccessGroups('write');
        // Activate datasets
        $strSQL    = 'SELECT `id` FROM `' .$strTableName. '` ' .
            'WHERE `config_id`=' .$this->intDomainId." AND `access_group` IN ($strAccess)";
        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if ($booReturn && ($intDataCount != 0)) {
            $intActivateCount = 0;
            foreach ($arrData as $elem) {
                $strChbName = 'chbId_' .$elem['id'];
                // was the current record is marked for activate?
                if ((($intDataId == $elem['id']) || ((filter_input(INPUT_POST, $strChbName) != null) &&
                            (filter_input(INPUT_POST, $strChbName, FILTER_SANITIZE_STRING) == 'on'))) &&
                    $this->infoRelation($strTableName, $elem['id'], 'id', 1) == 0) {
                    // Update dataset
                    if (($strTableName == 'tbl_service') || ($strTableName == 'tbl_host')) {
                        $strSQL = 'UPDATE `' .$strTableName."` SET `active`='0', `last_modified`=now() ".
                            'WHERE `id`=' .$elem['id'];
                    } else {
                        $strSQL = 'UPDATE `' .$strTableName."` SET `active`='0' WHERE `id`=".$elem['id'];
                    }
                    $this->myDBClass->insertData($strSQL);
                    $intActivateCount++;
                }
            }
            // Process informations
            if ($intActivateCount == 0) {
                $this->processClassMessage(translate('No dataset deactivated. Maybe the dataset does not exist, it '.
                        'is protected from deactivation, no dataset was selected or you do not have write permission. '.
                        'Use the "info" function for detailed informations about relations!').
                    '::', $this->strErrorMessage);
            } else {
                $this->updateStatusTable($strTableName);
                $this->processClassMessage(translate('Dataset successfully deactivated. Affected rows:'). ' ' .
                    $intActivateCount. '::', $this->strInfoMessage);
                $this->writeLog(translate('Deactivate dataset from table:')." $strTableName ".
                    translate('- with affected rows:'). ' ' .$this->myDBClass->intAffectedRows);
                $intReturn = 0;
            }
        } else {
            $this->processClassMessage(translate('No dataset deactivated. Maybe the dataset does not exist or you '.
                    'do not have write permission.'). '::', $this->strErrorMessage);
        }
        return $intReturn;
    }

    /**
     * Activates one or many datasets in the table be setting 'active' to '1'. Alternatively, a single record ID can
     * be specified or evaluated by the values of $_POST['chbId_n'] passed parameters, where n is the record ID must
     * match.
     * @param string $strTableName              Table name
     * @param int $intDataId                    Individual record ID, which is to be activate
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function dataActivate($strTableName, $intDataId = 0)
    {
        // Define variables
        $intReturn = 1;
        $arrData   = array();
        // Get write access groups
        $strAccess = $this->myVisClass->getAccessGroups('write');
        // Activate datasets
        $strSQL    = 'SELECT `id` FROM `' .$strTableName. '` ' .
            'WHERE `config_id`=' .$this->intDomainId." AND `access_group` IN ($strAccess)";
        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if ($booReturn && ($intDataCount != 0)) {
            $intActivateCount = 0;
            foreach ($arrData as $elem) {
                $strChbName = 'chbId_' .$elem['id'];
                // was the current record is marked for activate?
                if (($intDataId == $elem['id']) || ((filter_input(INPUT_POST, $strChbName) != null) &&
                        (filter_input(INPUT_POST, $strChbName, FILTER_SANITIZE_STRING) == 'on'))) {
                    // Update dataset
                    if (($strTableName == 'tbl_service') || ($strTableName == 'tbl_host')) {
                        $strSQL = 'UPDATE `' .$strTableName."` SET `active`='1', `last_modified`=now() ".
                            'WHERE `id`=' .$elem['id'];
                    } else {
                        $strSQL = 'UPDATE `' .$strTableName."` SET `active`='1' WHERE `id`=".$elem['id'];
                    }
                    $this->myDBClass->insertData($strSQL);
                    $intActivateCount++;
                }
            }
            // Process informations
            if ($intActivateCount == 0) {
                $this->processClassMessage(translate('No dataset activated. Maybe the dataset does not exist, no '.
                        'dataset was selected or you do not have write permission.'). '::', $this->strErrorMessage);
            } else {
                $this->updateStatusTable($strTableName);
                $this->processClassMessage(translate('Dataset successfully activated. Affected rows:'). ' ' .
                    $intActivateCount. '::', $this->strInfoMessage);
                $this->writeLog(translate('Activate dataset from table:')." $strTableName ".
                    translate('- with affected rows:'). ' ' .$this->myDBClass->intAffectedRows);
                $intReturn = 0;
            }
        } else {
            $this->processClassMessage(translate('No dataset activated. Maybe the dataset does not exist or you do '.
                    'not have write permission.'). '::', $this->strErrorMessage);
        }
        return $intReturn;
    }

    /**
     * Updates the hash field im some configuration objects
     * @param string $strTable                  Table name
     * @param int $intId                        Data ID
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function updateHash($strTable, $intId)
    {
        // Define variables
        $strRawString = '';
        $arrData      = array();
        $intDC        = 0;
        $intDataID    = 0;
        // Service table
        if ($strTable == 'tbl_service') {
            // Get any hosts and host_groups
            $strSQL  = 'SELECT `host_name` AS `item_name` FROM `tbl_host` ' .
                "LEFT JOIN `tbl_lnkServiceToHost` ON `idSlave`=`id` WHERE `idMaster`=$intId ".
                'UNION SELECT `hostgroup_name` AS `item_name` FROM `tbl_hostgroup` ' .
                'LEFT JOIN `tbl_lnkServiceToHostgroup` ON `idSlave`=`id` ' .
                'WHERE `idMaster`=' .$intId. ' ORDER BY `item_name`';
            $booRet  = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($booRet && ($intDC != 0)) {
                foreach ($arrData as $elem) {
                    $strRawString .= $elem['item_name']. ',';
                }
            }
            $strSQL = 'SELECT * FROM `tbl_service` WHERE `id`=' .$intId;
            $booRet = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($booRet && ($intDC != 0)) {
                if ($arrData[0]['service_description'] != '') {
                    $strRawString .= $arrData[0]['service_description']. ',';
                }
                if ($arrData[0]['display_name'] != '') {
                    $strRawString .= $arrData[0]['display_name']. ',';
                }
                if ($arrData[0]['check_command'] != '') {
                    $arrField      = explode('!', $arrData[0]['check_command']);
                    $strCommand    = strstr($arrData[0]['check_command'], '!');
                    $strSQLRel     = 'SELECT `command_name` FROM `tbl_command` WHERE `id`=' .$arrField[0];
                    $strName       = $this->myDBClass->getFieldData($strSQLRel);
                    $strRawString .= $strName.$strCommand. ',';
                }
            }
        }
        if (($strTable == 'tbl_hostdependency') || ($strTable == 'tbl_servicedependency')) {
            // Get * values
            $strSQL = 'SELECT * FROM `' .$strTable. '` WHERE `id`=' .$intId;
            $booRet  = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($booRet && ($intDC != 0)) {
                if (isset($arrData[0]['dependent_host_name'])  && ($arrData[0]['dependent_host_name'] == 2)) {
                    $strRawString .= 'any,';
                }
                if (isset($arrData[0]['dependent_hostgroup_name']) && ($arrData[0]['dependent_hostgroup_name'] == 2)) {
                    $strRawString .= 'any,';
                }
                if (isset($arrData[0]['host_name']) && ($arrData[0]['host_name'] == 2)) {
                    $strRawString .= 'any,';
                }
                if (isset($arrData[0]['hostgroup_name']) && ($arrData[0]['hostgroup_name'] == 2)) {
                    $strRawString .= 'any,';
                }
                if (isset($arrData[0]['dependent_service_description']) &&
                    ($arrData[0]['dependent_service_description'] == 2)) {
                    $strRawString .= 'any,';
                }
                if (isset($arrData[0]['service_description']) && ($arrData[0]['service_description'] == 2)) {
                    $strRawString .= 'any,';
                }
            }
            if ($strTable == 'tbl_hostdependency') {
                // Get any hosts and host_groups
                $strSQL  = 'SELECT `host_name` AS `item_name`, exclude FROM `tbl_host` ' .
                    "LEFT JOIN `tbl_lnkHostdependencyToHost_DH` ON `idSlave`=`id` WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `hostgroup_name` AS `item_name`, exclude FROM `tbl_hostgroup` ' .
                    "LEFT JOIN `tbl_lnkHostdependencyToHostgroup_DH` ON `idSlave`=`id` WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `host_name` AS `item_name`, exclude FROM `tbl_host` ' .
                    "LEFT JOIN `tbl_lnkHostdependencyToHost_H` ON `idSlave`=`id` WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `hostgroup_name` AS `item_name`, exclude FROM `tbl_hostgroup` ' .
                    'LEFT JOIN `tbl_lnkHostdependencyToHostgroup_H` ON `idSlave`=`id` WHERE `idMaster`=' .$intId;
            }
            if ($strTable == 'tbl_servicedependency') {
                // Get any hosts and host_groups
                $strSQL  = 'SELECT `host_name` AS `item_name`, exclude FROM `tbl_host` ' .
                    "LEFT JOIN `tbl_lnkServicedependencyToHost_DH` ON `idSlave`=`id` WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `hostgroup_name` AS `item_name`, exclude FROM `tbl_hostgroup` ' .
                    'LEFT JOIN `tbl_lnkServicedependencyToHostgroup_DH` ON `idSlave`=`id` ' .
                    "WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `host_name` AS `item_name`, exclude FROM `tbl_host` ' .
                    "LEFT JOIN `tbl_lnkServicedependencyToHost_H` ON `idSlave`=`id` WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `hostgroup_name` AS `item_name`, exclude FROM `tbl_hostgroup` ' .
                    'LEFT JOIN `tbl_lnkServicedependencyToHostgroup_H` ON `idSlave`=`id` ' .
                    "WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `strSlave` AS `item_name`, exclude ' .
                    "FROM `tbl_lnkServicedependencyToService_DS` WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `strSlave` AS `item_name`, exclude ' .
                    "FROM `tbl_lnkServicedependencyToService_S` WHERE `idMaster`=$intId";
            }
            $booRet  = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($booRet && ($intDC != 0)) {
                foreach ($arrData as $elem) {
                    if ($elem['exclude'] == 0) {
                        $strRawString .= $elem['item_name']. ',';
                    } else {
                        $strRawString .= 'not_' .$elem['item_name']. ',';
                    }
                }
                $strRawString = substr($strRawString, 0, -1);
            }
        }
        if (($strTable == 'tbl_hostescalation') || ($strTable == 'tbl_serviceescalation')) {
            // Get * values
            $strSQL = 'SELECT * FROM `' .$strTable. '` WHERE `id`=' .$intId;
            $booRet  = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($booRet && ($intDC != 0)) {
                if (isset($arrData[0]['host_name']) && ($arrData[0]['host_name'] == 2)) {
                    $strRawString .= 'any,';
                }
                if (isset($arrData[0]['hostgroup_name']) && ($arrData[0]['hostgroup_name'] == 2)) {
                    $strRawString .= 'any,';
                }
                if (isset($arrData[0]['contacts']) && ($arrData[0]['contacts'] == 2)) {
                    $strRawString .= 'any,';
                }
                if (isset($arrData[0]['contact_groups']) && ($arrData[0]['contact_groups'] == 2)) {
                    $strRawString .= 'any,';
                }
                if (isset($arrData[0]['service_description']) && ($arrData[0]['service_description'] == 2)) {
                    $strRawString .= 'any,';
                }
            }
            // Get any hosts, host_groups, contacts and contact_groups
            if ($strTable == 'tbl_hostescalation') {
                $strSQL  = 'SELECT `host_name` AS `item_name`, exclude FROM `tbl_host` ' .
                    "LEFT JOIN `tbl_lnkHostescalationToHost` ON `idSlave`=`id` WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `hostgroup_name` AS `item_name`, exclude  FROM `tbl_hostgroup` ' .
                    "LEFT JOIN `tbl_lnkHostescalationToHostgroup` ON `idSlave`=`id` WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `contact_name` AS `item_name`, exclude  FROM `tbl_contact` ' .
                    "LEFT JOIN `tbl_lnkHostescalationToContact` ON `idSlave`=`id` WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `contactgroup_name` AS `item_name`, exclude  FROM `tbl_contactgroup` ' .
                    "LEFT JOIN `tbl_lnkHostescalationToContactgroup` ON `idSlave`=`id` WHERE `idMaster`=$intId";
            }
            if ($strTable == 'tbl_serviceescalation') {
                $strSQL  = 'SELECT `host_name` AS `item_name`, exclude FROM `tbl_host` ' .
                    "LEFT JOIN `tbl_lnkServiceescalationToHost` ON `idSlave`=`id` WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `hostgroup_name` AS `item_name`, exclude  FROM `tbl_hostgroup` ' .
                    "LEFT JOIN `tbl_lnkServiceescalationToHostgroup` ON `idSlave`=`id` WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `contact_name` AS `item_name`, exclude  FROM `tbl_contact` ' .
                    "LEFT JOIN `tbl_lnkServiceescalationToContact` ON `idSlave`=`id` WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `contactgroup_name` AS `item_name`, exclude  FROM `tbl_contactgroup` ' .
                    'LEFT JOIN `tbl_lnkServiceescalationToContactgroup` ON `idSlave`=`id` ' .
                    "WHERE `idMaster`=$intId ".
                    'UNION ALL SELECT `strSlave` AS `item_name`, exclude ' .
                    "FROM `tbl_lnkServiceescalationToService` WHERE `idMaster`=$intId";
            }
            $booRet  = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($booRet && ($intDC != 0)) {
                foreach ($arrData as $elem) {
                    if ($elem['exclude'] == 0) {
                        $strRawString .= $elem['item_name']. ',';
                    } else {
                        $strRawString .= 'not_' .$elem['item_name']. ',';
                    }
                }
                $strRawString = substr($strRawString, 0, -1);
            }
        }
        if ($strTable == 'tbl_serviceextinfo') {
            // Get any hosts and host_groups
            $strSQL  = 'SELECT `tbl_host`.`host_name` AS `item_name` FROM `tbl_host` ' .
                'LEFT JOIN `tbl_serviceextinfo` ON `tbl_host`.`id`=`tbl_serviceextinfo`.`host_name` ' .
                "WHERE `tbl_serviceextinfo`.`id`=$intId ".
                'UNION SELECT `tbl_service`.`service_description` AS `item_name` FROM `tbl_service` ' .
                'LEFT JOIN `tbl_serviceextinfo` ON ' .
                '`tbl_service`.`id`=`tbl_serviceextinfo`.`service_description` ' .
                "WHERE `tbl_serviceextinfo`.`id`=$intId ORDER BY `item_name`";
            $booRet  = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDC);
            if ($booRet && ($intDC != 0)) {
                foreach ($arrData as $elem) {
                    $strRawString .= $elem['item_name']. ',';
                }
                $strRawString = substr($strRawString, 0, -1);
            }
        }
        // Remove blanks
        while (substr_count($strRawString, ' ') != 0) {
            $strRawString = str_replace(' ', '', $strRawString);
        }
        // Sort hash string
        $arrTemp = explode(',', $strRawString);
        sort($arrTemp);
        $strRawString = implode(',', $arrTemp);
        // Update has in database
        $strSQL    = 'UPDATE `' .$strTable."` SET `import_hash`='".sha1($strRawString)."' WHERE `id`='$intId'";
        //echo "Hash: ".$strRawString." --> ".sha1($strRawString)."<br>";
        return $this->dataInsert($strSQL, $intDataID);
    }

    /**
     * PRIVATE functions
     */

    /**
     * Manually set some NULL values based on field names (key)
     * @param string $strTableName              Table name
     * @param string $key                       Data key (field name)
     * @param string $value                     Data value (field key)
     * @return NULL|string                      Manipulated data value
     */
    private function setNullValues($strTableName, $key, $value)
    {
        $arrNull = array('normal_check_interval', 'retry_check_interval', 'max_check_attempts', 'low_flap_threshold',
            'high_flap_threshold', 'freshness_threshold', 'notification_interval', 'first_notification_delay',
            'check_interval', 'retry_interval');
        if (\in_array($key, $arrNull, true) && ($value == '')) {
            $value = 'NULL';
        }
        // manually set some NULL values based on table name
        if (($strTableName == 'tbl_serviceextinfo') && ($key == 'service_description')) {
            $value = '0';
        }
        // Do not copy the password in tbl_user
        if (($strTableName == 'tbl_user') && ($key == 'password')) {
            $value = 'xxxxxxx';
        }
        // Do not copy nodelete and webserver authentification values in tbl_user
        if ($key == 'nodelete') {
            $value = '0';
        }
        if ($key == 'wsauth') {
            $value = '0';
        }
        return $value;
    }

    /**
     * Insert a normal 1:n relation
     * @param array $arrData                    Database value array
     * @param integer $intID                    Database array key
     * @param array $elem                       Link table information
     * @param integer $intMasterId              Data ID of master table
     * @param integer $intCheck                 Check error counter (before processing)
     * @return integer                          Check error counter (after processing)
     */
    private function insertRelationType2($arrData, $intID, $elem, $intMasterId, $intCheck)
    {
        $arrRelData      = array();
        $intRelDataCount = 0;
        if ($arrData[$intID][$elem['fieldName']] != 0) {
            $strSQL = 'SELECT `idSlave`, `exclude` FROM `' .$elem['linkTable']. '` ' .
                'WHERE `idMaster`=' .$arrData[$intID]['id'];
            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrRelData, $intRelDataCount);
            if ($booReturn && ($intRelDataCount != 0)) {
                foreach ($arrRelData as $elem2) {
                    $strSQLRel = 'INSERT INTO `' . $elem['linkTable'] . '` ' .
                        "SET `idMaster`=$intMasterId, `idSlave`=" . $elem2['idSlave'] . ', ' .
                        '`exclude`=' . $elem2['exclude'];
                    $booReturn = $this->myDBClass->insertData($strSQLRel);
                    if ($booReturn == false) {
                        $intCheck++;
                    }
                }
            }
        }
        return $intCheck;
    }

    /**
     * Insert a 1:n relation for templates
     * @param array $arrData                    Database value array
     * @param integer $intID                    Database array key
     * @param array $elem                       Link table information
     * @param integer $intMasterId              Data ID of master table
     * @param integer $intCheck                 Check error counter (before processing)
     * @return integer                          Check error counter (after processing)
     */
    private function insertRelationType3($arrData, $intID, $elem, $intMasterId, $intCheck)
    {
        $arrRelData      = array();
        $intRelDataCount = 0;
        if ($arrData[$intID][$elem['fieldName']] == 1) {
            $strSQL = 'SELECT `idSlave`,`idSort`,`idTable` FROM `' . $elem['linkTable'] . '` ' .
                'WHERE `idMaster`=' . $arrData[$intID]['id'];
            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrRelData, $intRelDataCount);
            if ($booReturn && ($intRelDataCount != 0)) {
                foreach ($arrRelData as $elem2) {
                    $strSQLRel = 'INSERT INTO `' . $elem['linkTable'] . '` ' .
                        "SET `idMaster`=$intMasterId, `idSlave`=" . $elem2['idSlave'] . ', ' .
                        '`idTable`=' . $elem2['idTable'] . ', `idSort`=' . $elem2['idSort'];
                    $booReturn = $this->myDBClass->insertData($strSQLRel);
                    if ($booReturn == false) {
                        $intCheck++;
                    }
                }
            }
        }
        return $intCheck;
    }

    /**
     * Insert a special relation for free variables
     * @param array $arrData                    Database value array
     * @param integer $intID                    Database array key
     * @param array $elem                       Link table information
     * @param integer $intMasterId              Data ID of master table
     * @param integer $intCheck                 Check error counter (before processing)
     * @return integer                          Check error counter (after processing)
     */
    private function insertRelationType4($arrData, $intID, $elem, $intMasterId, $intCheck)
    {
        $arrRelData      = array();
        $arrDataVar      = array();
        $intRelDataCount = 0;
        $intDCVar        = 0;
        if ($arrData[$intID][$elem['fieldName']] != 0) {
            $strSQL = 'SELECT `idSlave` FROM `' . $elem['linkTable'] . '` ' .
                'WHERE `idMaster` = ' . $arrData[$intID]['id'];
            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrRelData, $intRelDataCount);
            if ($booReturn && ($intRelDataCount != 0)) {
                foreach ($arrRelData as $elem2) {
                    // Copy variables and link them to the new master
                    $strSQLVar = 'SELECT * FROM `tbl_variabledefinition` WHERE `id`=' . $elem2['idSlave'];
                    $booReturn = $this->myDBClass->hasDataArray($strSQLVar, $arrDataVar, $intDCVar);
                    if ($booReturn && ($intDCVar != 0)) {
                        $strSQLInsVar = 'INSERT INTO `tbl_variabledefinition` ' .
                            "SET `name`='" . addslashes($arrDataVar[0]['name']) . "', " .
                            "`value`='" . addslashes($arrDataVar[0]['value']) . "', " .
                            '`last_modified`=NOW()';
                        $booReturn = $this->myDBClass->insertData($strSQLInsVar);
                        if ($booReturn == false) {
                            $intCheck++;
                        }
                        $strSQLRel = 'INSERT INTO `' . $elem['linkTable'] . '` ' .
                            "SET `idMaster`=$intMasterId, " .
                            '`idSlave`=' . $this->myDBClass->intLastId;
                        $booReturn = $this->myDBClass->insertData($strSQLRel);
                        if ($booReturn == false) {
                            $intCheck++;
                        }
                    }
                }
            }
        }
        return $intCheck;
    }

    /**
     * Insert a 1:n relation for tbl_lnkServicegroupToService
     * @param array $arrData                    Database value array
     * @param integer $intID                    Database array key
     * @param array $elem                       Link table information
     * @param integer $intMasterId              Data ID of master table
     * @param integer $intCheck                 Check error counter (before processing)
     * @return integer                          Check error counter (after processing)
     */
    private function insertRelationType5($arrData, $intID, $elem, $intMasterId, $intCheck)
    {
        $arrRelData      = array();
        $intRelDataCount = 0;
        if ($arrData[$intID][$elem['fieldName']] != 0) {
            $strSQL = 'SELECT `idSlaveH`,`idSlaveHG`,`idSlaveS`,`exclude` ' .
                'FROM `' . $elem['linkTable'] . '` WHERE `idMaster`=' . $arrData[$intID]['id'];
            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrRelData, $intRelDataCount);
            if ($booReturn && ($intRelDataCount != 0)) {
                foreach ($arrRelData as $elem2) {
                    $strSQLRel = 'INSERT INTO `' . $elem['linkTable'] . '` ' .
                        "SET `idMaster`=$intMasterId, `idSlaveH`=" . $elem2['idSlaveH'] . ', ' .
                        '`idSlaveHG`=' . $elem2['idSlaveHG'] . ', `idSlaveS`=' . $elem2['idSlaveS'] . ', `exclude`=' .
                        $elem2['exclude'];
                    $booReturn = $this->myDBClass->insertData($strSQLRel);
                    if ($booReturn == false) {
                        $intCheck++;
                    }
                }
            }
        }
        return $intCheck;
    }

    /**
     * Insert a 1:n relation for services
     * @param array $arrData                    Database value array
     * @param integer $intID                    Database array key
     * @param array $elem                       Link table information
     * @param integer $intMasterId              Data ID of master table
     * @param integer $intCheck                 Check error counter (before processing)
     * @return integer                          Check error counter (after processing)
     */
    private function insertRelationType6($arrData, $intID, $elem, $intMasterId, $intCheck)
    {
        $arrRelData      = array();
        $intRelDataCount = 0;
        if ($arrData[$intID][$elem['fieldName']] != 0) {
            $strSQL = 'SELECT `idSlave`, `strSlave`, `exclude` ' .
                'FROM `' . $elem['linkTable'] . '` WHERE `idMaster`=' . $arrData[$intID]['id'];
            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrRelData, $intRelDataCount);
            if ($booReturn && ($intRelDataCount != 0)) {
                foreach ($arrRelData as $elem2) {
                    $strSQLRel = 'INSERT INTO `' . $elem['linkTable'] . '` ' .
                        "SET `idMaster`=$intMasterId, `idSlave`=" . $elem2['idSlave'] . ', ' .
                        "`strSlave`='" . addslashes($elem2['strSlave']) . "', " .
                        '`exclude`=' . $elem2['exclude'];
                    $booReturn = $this->myDBClass->insertData($strSQLRel);
                    if ($booReturn == false) {
                        $intCheck++;
                    }
                }
            }
        }
        return $intCheck;
    }

    /**
     * Insert a 1:n relation for time definitions
     * @param array $arrData                    Database value array
     * @param integer $intID                    Database array key
     * @param integer $intMasterId              Data ID of master table
     * @param integer $intCheck                 Check error counter (before processing)
     * @return integer                          Check error counter (after processing)
     */
    private function insertRelationTimedefinition($arrData, $intID, $intMasterId, $intCheck)
    {
        $arrRelDataTP = array();
        $intRelDataCountTP = 0;
        $strSQL = 'SELECT * FROM `tbl_timedefinition` WHERE `tipId`=' . $arrData[$intID]['id'];
        $this->myDBClass->hasDataArray($strSQL, $arrRelDataTP, $intRelDataCountTP);
        if ($intRelDataCountTP != 0) {
            foreach ($arrRelDataTP as $elem) {
                $strSQLRel = 'INSERT INTO `tbl_timedefinition` (`tipId`,`definition`,`range`,' .
                    "`last_modified`) VALUES ($intMasterId,'" . addslashes($elem['definition']) . "'," .
                    "'" . addslashes($elem['range']) . "',now())";
                $booReturn = $this->myDBClass->insertData($strSQLRel);
                if ($booReturn == false) {
                    $intCheck++;
                }
            }
        }
        return $intCheck;
    }

    /**
     * Insert a 1:n relation for user groups
     * @param array $arrData                    Database value array
     * @param integer $intID                    Database array key
     * @param integer $intMasterId              Data ID of master table
     * @param integer $intCheck                 Check error counter (before processing)
     * @return integer                          Check error counter (after processing)
     */
    private function insertRelationGroup($arrData, $intID, $intMasterId, $intCheck)
    {
        $arrRelDataTP = array();
        $intRelDataCountTP = 0;
        $strSQL = 'SELECT * FROM `tbl_lnkGroupToUser` WHERE `idMaster`=' . $arrData[$intID]['id'];
        $this->myDBClass->hasDataArray($strSQL, $arrRelDataTP, $intRelDataCountTP);
        if ($intRelDataCountTP != 0) {
            foreach ($arrRelDataTP as $elem2) {
                $strSQLRel = 'INSERT INTO `tbl_lnkGroupToUser` (`idMaster`,`idSlave`,`read`,`write`,`link`) ' .
                    "VALUES ($intMasterId,'" . $elem2['idSlave'] . "','" . $elem2['read'] . "',".
                    "'" . $elem2['write'] . "','" . $elem2['link'] . "')";
                $booReturn = $this->myDBClass->insertData($strSQLRel);
                if ($booReturn == false) {
                    $intCheck++;
                }
            }
        }
        return $intCheck;
    }

    /**
     * Insert a 1:n relation fot service to host connections
     * @param array $arrData                    Database value array
     * @param integer $intID                    Database array key
     * @param integer $intMasterId              Data ID of master table
     * @param integer $intCheck                 Check error counter (before processing)
     * @return integer                          Check error counter (after processing)
     */
    private function insertRelationHost($arrData, $intID, $intMasterId, $intCheck)
    {
        $arrRelDataSH = array();
        $intRelDataCountSH = 0;
        $strSQL = 'SELECT * FROM `tbl_lnkServiceToHost` WHERE `idSlave`=' . $arrData[$intID]['id'];
        $this->myDBClass->hasDataArray($strSQL, $arrRelDataSH, $intRelDataCountSH);
        if ($intRelDataCountSH != 0) {
            foreach ($arrRelDataSH as $elem2) {
                $strSQLRel = 'INSERT INTO `tbl_lnkServiceToHost` (`idMaster`,`idSlave`,`exclude`) ' .
                    "VALUES ('" . $elem2['idMaster'] . "',$intMasterId,'" . $elem2['exclude'] . "')";
                $booReturn = $this->myDBClass->insertData($strSQLRel);
                if ($booReturn == false) {
                    $intCheck++;
                }
            }
        }
        return $intCheck;
    }

    /**
     * Build an INSERT command based on the table name
     * @param string $strTableName              Table name
     * @param string $strKeyField               Configuration field name
     * @param int $intDomainId                  Domain ID
     * @param string $strNewName                New configuration name
     * @param array $arrData                    Data array
     * @param int $intID                        Data array key
     * @return string                           SQL INSERT command
     */
    private function buildInsertSQL($strTableName, $strKeyField, $intDomainId, $strNewName, $arrData, $intID)
    {
        $strSQLInsert = 'INSERT INTO `' . $strTableName . '` SET `' . $strKeyField . "`='" . $strNewName . "'";
        /** @noinspection ForeachSourceInspection */
        foreach ($arrData[$intID] as $key => $value) {
            if (($key != $strKeyField) && ($key != 'active') && ($key != 'last_modified') &&
                ($key != 'id') && ($key != 'config_id')) {
                // manually set some NULL values based on field names
                $value = $this->setNullValues($strTableName, $key, $value);
                // If the data value is not "NULL", add single quotes to the value
                if ($value != 'NULL') {
                    $strSQLInsert .= ',`' . $key . "`='" . addslashes($value) . "'";
                } else {
                    $strSQLInsert .= ',`' . $key . '`=' . $value;
                }
            }
        }
        if (($strTableName == 'tbl_user') || ($strTableName == 'tbl_group') ||
            ($strTableName == 'tbl_datadomain') || ($strTableName == 'tbl_configtarget')) {
            $strSQLInsert .= ",`active`='0', `last_modified`=NOW()";
        } else {
            $strSQLInsert .= ",`active`='0', `config_id`=$intDomainId, `last_modified`=NOW()";
        }
        return $strSQLInsert;
    }

    /**
     * Build a temporary configuration name
     * @param string $strTableName              Table name
     * @param string $strKeyField               Configuration field name
     * @param int $intDomainId                  Domain ID
     * @param int $intCount                     Dataset counter
     * @param array $arrData                    Data array
     * @param int $intID                        Data array key
     * @return string                           Temporary configuration name
     */
    private function buildTempConfigName($strTableName, $strKeyField, $intDomainId, $intCount, $arrData, $intID)
    {
        // Define variables
        $strNewName = '';
        for ($y = 1; $y <= $intCount; $y++) {
            $strNewName = $arrData[$intID][$strKeyField] . " ($y)";
            if (($strTableName == 'tbl_user') || ($strTableName == 'tbl_group') ||
                ($strTableName == 'tbl_datadomain') || ($strTableName == 'tbl_configtarget')) {
                $strSQL = 'SELECT `id` FROM `' . $strTableName . '` WHERE `' . $strKeyField . "`='$strNewName'";
                $booReturn = $this->myDBClass->getFieldData($strSQL);
            } else {
                $strSQL = 'SELECT `id` FROM `' . $strTableName . '` ' .
                    'WHERE `' . $strKeyField . "`='$strNewName' AND `config_id`=$intDomainId";
                $booReturn = $this->myDBClass->getFieldData($strSQL);
            }
            // If the name is unused -> break the loop
            if ($booReturn == false) {
                break;
            }
        }
        // Manually overwrite new name for extinfo tables
        if ($strTableName == 'tbl_hostextinfo') {
            $strNewName = '0';
        }
        if ($strTableName == 'tbl_serviceextinfo') {
            $strNewName = '0';
        }
        return $strNewName;
    }
}
