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
// Component : Import Class
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
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
///////////////////////////////////////////////////////////////////////////////////////////////
namespace functions;

class NagImportClass
{
    // Define class variables
    private $arrSettings         = array();     // Array includes all global settings
    public $intDomainId          = 0;           // Configuration domain ID
    public $strErrorMessage      = '';          // String including error messages
    public $strInfoMessage       = '';          // String including information messages
    private $strList1            = '';
    private $strList2            = '';

    // Class includes
    /** @var MysqliDbClass */
    public $myDBClass;                          // Database class reference
    /** @var NagDataClass */
    public $myDataClass;                        // Data processing class reference
    /** @var NagConfigClass */
    public $myConfigClass;                      // Configuration class reference

    /**
     * NagImportClass constructor.
     * @param array $arrSession                 PHP Session array
     */
    public function __construct($arrSession)
    {
        if (isset($arrSession['SETS'])) {
            // Read global settings
            $this->arrSettings = $arrSession['SETS'];
        }
        if (isset($arrSession['domain'])) {
            $this->intDomainId = $arrSession['domain'];
        }
    }

    /**
     * Import a config file and writes the values to the database
     * @param string $strFileNameRaw            Import file name
     * @param int $intConfigId                  Configuration set id
     * @param int $intOverwrite                 0 = Do not replace existing data
     *                                          1 = Replace existing data in tables
     * @return int                              0 = successful / 1 = error
     *                                          Status messages are stored in class variables
     */
    public function fileImport($strFileNameRaw, $intConfigId, $intOverwrite = 0)
    {
        // Define variables
        $intBlock        = 0;
        $intRemoveTmp    = 0;
        $strImportFile   = '';
        $strConfLineTemp = '';
        $strBlockKey     = '';
        $arrData         = array();
        $strFileName     = trim($strFileNameRaw);
        // Get file
        $intReturn = $this->getImportFile($intConfigId, $strFileName, $strImportFile, $intRemoveTmp);
        // Open and read config file
        if ($intReturn == 0) {
            $resFile     = fopen($strImportFile, 'rb');
            $intMultiple = 0;
            while ($resFile && !feof($resFile)) {
                // Read line and remove blank chars
                $strConfLine = trim(fgets($resFile));
                // Process multi-line configuration instructions
                if (substr($strConfLine, -1) == '\\') {
                    if ($intMultiple == 0) {
                        $strConfLineTemp = str_replace("\\", ',', $strConfLine);
                        $intMultiple     = 1;
                    } else {
                        $strConfLineTemp .= str_replace("\\", ',', $strConfLine);
                    }
                    continue;
                }
                if ($intMultiple == 1) {
                    $strConfLine = $strConfLineTemp.$strConfLine;
                    $intMultiple = 0;
                }
                // Find NAGIOSQL variable
                if (substr_count($strConfLine, '#NAGIOSQL_') != 0) {
                    $strConfLine = str_replace('#NAGIOSQL_CONFIG_NAME', '_NAGIOSQL_CONFIG_NAME', $strConfLine);
                }
                // Pass comments and empty lines
                if (0 === strpos($strConfLine, '#')) {
                    continue;
                }
                if ($strConfLine == '') {
                    continue;
                }
                if (($intBlock == 1) && ($strConfLine == '{')) {
                    continue;
                }
                // Process line (remove blanks and cut comments)
                $strLineTmp  = str_replace("\;", ':semi:', $strConfLine);
                $arrLine     = preg_split("/[\s]+/", $strLineTmp);
                $arrTemp     = explode(';', implode(' ', $arrLine));
                $strNewLine  = str_replace(':semi:', "\;", trim($arrTemp[0]));
                // Find block begin
                if ($arrLine[0] == 'define') {
                    $intBlock    = 1;
                    $strBlockKey = str_replace('{', '', $arrLine[1]);
                    $arrData     = array();
                    continue;
                }
                // Store the block data to an array
                if (($intBlock == 1) && ($arrLine[0] != '}')) {
                    $strExclude = 'template_name,alias,name,use';
                    if (($strBlockKey == 'timeperiod') && (!\in_array($arrLine[0], explode(',', $strExclude), true))) {
                        $arrNewLine = explode(' ', $strNewLine);
                        $strTPKey   = str_replace(' ' .$arrNewLine[\count($arrNewLine)-1], '', $strNewLine);
                        $strTPValue = $arrNewLine[\count($arrNewLine)-1];
                        $arrData[$strTPKey] = array('key' => $strTPKey,
                            'value' => $strTPValue);
                    } else {
                        $key   = $arrLine[0];
                        $value = str_replace($arrLine[0]. ' ', '', $strNewLine);
                        // Special retry_check_interval, normal_check_interval
                        if ($key == 'retry_check_interval') {
                            $key = 'retry_interval';
                        }
                        if ($key == 'normal_check_interval') {
                            $key = 'check_interval';
                        }
                        $arrData[$arrLine[0]] = array('key' => $key, 'value' => $value);
                    }
                }
                // Process data at end of block
                if ((substr_count($strConfLine, '}') == 1) && ($arrData !== null) && \is_array($arrData)) {
                    $intBlock  = 0;
                    $intRetVal = $this->importTable($strBlockKey, $arrData, $intOverwrite);
                    if ($intRetVal != 0) {
                        $intReturn = 1;
                    }
                } elseif ($arrData === null) {
                    $this->strErrorMessage .= translate('No valid configuration found:'). ' ' .$strFileName. '::';
                    $intReturn = 1;
                }
            }
            if ($intRemoveTmp == 1) {
                unlink($strImportFile);
            }
        } else {
            $this->strErrorMessage .= translate('Import file does not exist or is not readable:'). ' ' .$strFileName
                . '::';
            $intReturn = 1;
        }
        return $intReturn;
    }

    // PRIVATE functions

    /**
     * @param int $intConfigId                  Configuration set id
     * @param string $strFileName               Configuration file name
     * @param string|bool $strImportFile        Temporary file for data import (by reference)
     * @param int $intRemoveTmp                 Remove temporary file (1 = yes / 0 = no) (by reference)
     * @return int                              0 = successful / 1 = error
     *                                          Status messages are stored in class variables
     */
    private function getImportFile($intConfigId, $strFileName, &$strImportFile, &$intRemoveTmp)
    {
        $intMethod        = 1;
        $intReturn        = 0;
        $intRemoveTmp     = 0;
        $strImportFile    = '';
        $strImportFileTmp = '';
        // File transfer method
        if (substr_count($strFileName, 'nagiosql_local_imp') == 1) {
            $intMethod = 1;
            $intRetVal = 0;
        } else {
            $intRetVal = $this->myConfigClass->getConfigData($intConfigId, 'method', $intMethod);
        }
        if ($intRetVal != 0) {
            $this->strErrorMessage .= translate('Unable to get configuration data:'). ' method::';
            $intReturn = 1;
        }
        if ($intReturn == 0) {
            // Read import file
            if ($intMethod == 1) { // Local file system
                if (!is_readable($strFileName)) {
                    $this->strErrorMessage .= translate('Cannot open the data file (check the permissions)!'). ' ' .
                        $strFileName. '::';
                    $intReturn = 1;
                } else {
                    $strImportFileTmp = $strFileName;
                }
            } elseif ($intMethod == 2) { // FTP access
                // Open ftp connection
                $intRetVal = $this->myConfigClass->getFTPConnection($intConfigId);
                if ($intRetVal != 0) {
                    $this->strErrorMessage .= $this->myConfigClass->strErrorMessage;
                    $intReturn = 1;
                } else {
                    // Transfer file from remote server to a local temp file
                    if (isset($this->arrSettings['path']) && isset($this->arrSettings['path']['tempdir'])) {
                        $strConfigFile = tempnam($this->arrSettings['path']['tempdir'], 'nagiosql_imp');
                    } else {
                        $strConfigFile = tempnam(sys_get_temp_dir(), 'nagiosql_imp');
                    }
                    if (!ftp_get($this->myConfigClass->resConnectId, $strConfigFile, $strFileName, FTP_ASCII)) {
                        $this->strErrorMessage .= translate('Cannot receive the configuration file (FTP connection)!').
                            '::';
                        ftp_close($this->myConfigClass->resConnectId);
                        $intReturn = 1;
                    } else {
                        $intRemoveTmp     = 1;
                        $strImportFileTmp = $strConfigFile;
                    }
                }
            } elseif ($intMethod == 3) { // SSH Access
                // Open ssh connection
                $intRetVal = $this->myConfigClass->getSSHConnection($intConfigId);
                if ($intRetVal != 0) {
                    $this->strErrorMessage .= $this->myConfigClass->strErrorMessage;
                    $intReturn = 1;
                } else {
                    // Transfer file from remote server to a local temp file
                    if (isset($this->arrSettings['path']) && isset($this->arrSettings['path']['tempdir'])) {
                        $strConfigFile = tempnam($this->arrSettings['path']['tempdir'], 'nagiosql_imp');
                    } else {
                        $strConfigFile = tempnam(sys_get_temp_dir(), 'nagiosql_imp');
                    }
                    if (!ssh2_scp_recv($this->myConfigClass->resConnectId, $strFileName, $strConfigFile)) {
                        $this->strErrorMessage .= translate('Cannot receive the configuration file (SSH connection)!').
                            '::';
                        $intReturn = 1;
                    } else {
                        $intRemoveTmp     = 1;
                        $strImportFileTmp = $strConfigFile;
                    }
                }
            }
            // Open and read config file
            if (file_exists($strImportFileTmp) && is_readable($strImportFileTmp)) {
                $strImportFile = $strImportFileTmp;
            } else {
                $intReturn    = 1;
                $intRemoveTmp = 0;
            }
        }
        return $intReturn;
    }

    /**
     * Writes the block data to the database
     * @param string $strBlockKey               Config key (from define)
     * @param array $arrImportData              Imported block data
     * @param int $intOverwrite                 0 = Do not replace existing data
     *                                          1 = Replace existing data in tables
     * @return int                              0 = successful / 1 = error
     *                                          Status messages are stored in class variables
     */
    private function importTable($strBlockKey, $arrImportData, $intOverwrite)
    {
        // Define variables
        $intIsTemplate      = 0;
        $intExists          = 0;
        $intInsertRelations = 0;
        $intInsertVariables = 0;
        $strHash            = '';
        $strConfigName      = '';
        $arrImportRelations = array();
        $arrFreeVariables   = array();
        $arrRelations       = array();
        // Block data from template or real configuration?
        if (array_key_exists('name', $arrImportData) && (isset($arrImportData['register']) &&
                ($arrImportData['register']['value'] == 0))) {
            $intIsTemplate = 1;
        }
        // Get table name and key for import
        $intReturn = $this->getTableData($strBlockKey, $intIsTemplate, $strTable, $strKeyField);
        if ($intReturn == 0) {
            // Create an import hash if no key field is available
            if ($strKeyField == '') {
                $this->createHash($strTable, $arrImportData, $strHash, $strConfigName);
                $arrImportData['config_name']['key']    = 'config_name';
                $arrImportData['config_name']['value']  = $strConfigName;
                $strKeyField = 'config_name';
            } else {
                $strHash = '';
            }
            // Get relation data
            $intRelation = $this->myDataClass->tableRelations($strTable, $arrRelations);
            // Does this entry already exist?
            if (($intIsTemplate == 0) && ($strKeyField != '') && isset($arrImportData[$strKeyField])) {
                if ($strHash == '') {
                    // Special key field values
                    if ($strBlockKey == 'hostextinfo') {
                        $strSQL  = 'SELECT `id`FROM `tbl_host` ' .
                            "WHERE `host_name`='".$arrImportData[$strKeyField]['value']."'";
                        $intHost = (int)$this->myDBClass->getFieldData($strSQL);
                        $strSQL  = 'SELECT `id` FROM `' .$strTable. '` ' .
                            'WHERE `config_id`=' .$this->intDomainId. ' AND `' .$strKeyField."`='".$intHost."'";
                    } else {
                        $strSQL  = 'SELECT `id` FROM `' .$strTable. '` ' .
                            'WHERE `config_id`=' .$this->intDomainId. ' AND ' .
                            '`' .$strKeyField."`='".$arrImportData[$strKeyField]['value']."'";
                    }
                } else {
                    $strSQL = 'SELECT `id` FROM `' .$strTable. '` ' .
                        'WHERE `config_id`=' .$this->intDomainId." AND `import_hash`='".$strHash."'";
                }
                $intExists = $this->myDBClass->getFieldData($strSQL);
                if ($intExists == false) {
                    $intExists = 0;
                }
            } elseif (($intIsTemplate == 1) && ($strKeyField != '') && isset($arrImportData['name'])) {
                $strSQL = 'SELECT `id` FROM `' .$strTable. '` ' .
                    'WHERE `config_id`=' .$this->intDomainId. ' AND ' .
                    "`template_name`='".$arrImportData['name']['value']."'";
                $intExists = $this->myDBClass->getFieldData($strSQL);
                if ($intExists == false) {
                    $intExists = 0;
                }
            }
            // Entry exsists but should not be overwritten
            if (($intExists != 0) && ($intOverwrite == 0)) {
                if ($strKeyField == 'config_name') {
                    $strSQLConfig  = 'SELECT `config_name` FROM `' .$strTable. '` WHERE `id`=' .$intExists;
                    $arrImportData[$strKeyField]['value'] = $this->myDBClass->getFieldData($strSQLConfig);
                }
                $this->strInfoMessage .= translate('Entry'). ' <b class="blackmessage">' .$strKeyField. ' -> ' .
                    $arrImportData[$strKeyField]['value']. '</b> ' .translate('inside'). ' <b class="blackmessage">' .
                    $strTable. '</b> ' .translate('exists and were not overwritten'). '::';
            } elseif (isset($arrImportData[$strKeyField]) && ($arrImportData[$strKeyField] == '*')) {
                // Do not write "*" values
                $this->strInfoMessage .= translate('Entry'). ' <b class="blackmessage">' .$strKeyField. ' -> ' .
                    $arrImportData[$strKeyField]['value']. '</b> ' .translate('inside'). ' <b class="blackmessage">' .
                    $strTable. '</b> ' .translate('were not written'). '::';
            } else {
                // Define SQL statement - part 1
                $this->getSQLPart1(
                    $arrImportData,
                    $strHash,
                    $intExists,
                    $strTable,
                    $strKeyField,
                    $intRelation,
                    $arrRelations,
                    $strSQL1,
                    $strSQL2
                );
                // Read command configurations
                list($strVCValues, $intWriteConfig, $strVIValues, $strRLValues, $strVWValues) =
                    $this->getImportValues($arrImportData, $strKeyField, $strSQL1, $strTable);


                // Build value statemets
                foreach ($arrImportData as $elem) {
                    // Write text values
                    $intCheckVC  = $this->writeTextValues(
                        $elem,
                        $strVCValues,
                        $strSQL1,
                        $intIsTemplate,
                        $intExists,
                        $strTable
                    );
                    // Write status values
                    $intCheckVI  = $this->writeStatusValues($elem, $strVIValues, $strSQL1);
                    // Write integer values
                    $intCheckVW  = $this->writeIntegerValues($elem, $strVWValues, $strSQL1);
                    // Write relations
                    $intCheckRel = $this->writeRelations($elem, $strRLValues, $arrImportRelations, $intInsertRelations);
                    // Write free variables
                    $intCheck = $intCheckVC+$intCheckVI+$intCheckVW+$intCheckRel;
                    if ($intCheck == 0) {
                        $arrTemp            = array();
                        $arrTemp['key']     = $elem['key'];
                        $arrTemp['value']   = $elem['value'];
                        $arrFreeVariables[] = $arrTemp;
                        $intInsertVariables = 1;
                    }
                }
                $strTemp1 = '';
                $strTemp2 = '';
                // Update database
                if ($intWriteConfig == 1) {
                    $booResult = $this->myDBClass->insertData($strSQL1.$strSQL2);
                } else {
                    $booResult = false;
                }
                if ($strKeyField == '') {
                    $strKey = $strConfigName;
                } else {
                    $strKey = $strKeyField;
                }
                if ($booResult != true) {
                    $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                    if ($strKeyField != '') {
                        $this->strErrorMessage .= translate('Entry'). ' <b class="blackmessage">' .$strKey. ' -> ' .
                            $arrImportData[$strKeyField]['value']. '</b> ' .translate('inside').
                            ' <b class="blackmessage">' .$strTable. '</b> ' .translate('could not be inserted:'). ' ' .
                            $this->myDBClass->strErrorMessage. '::';
                    }
                    if ($strKeyField == '') {
                        $this->strErrorMessage .= translate('Entry'). ' <b class="blackmessage">' .$strTemp1. ' -> ' .
                            $strTemp2.translate('inside'). '</b> ' .$strTable. ' <b class="blackmessage">' .$strTable.
                            '</b> ' .translate('could not be inserted:'). ' ' .$this->myDBClass->strErrorMessage. '::';
                    }
                    return 1;
                }
                if ($strKeyField != '') {
                    $this->strInfoMessage .= translate('Entry'). ' <b class="blackmessage">' .$strKey. ' -> ' .
                        $arrImportData[$strKeyField]['value']. '</b> ' .translate('inside').
                        ' <b class="blackmessage">' .$strTable. '</b> ' .translate('successfully inserted'). '::';
                }
                if ($strKeyField == '') {
                    $this->strInfoMessage .= translate('Entry'). ' <b class="blackmessage">' .$strTemp1. ' -> ' .
                        $strTemp2. '</b> ' .translate('inside'). ' <b class="blackmessage">' .$strTable.
                        '</b> ' .translate('successfully inserted'). '::';
                }
                // Define data ID
                if ($intExists != 0) {
                    $intDataId = $intExists;
                } else {
                    $intDataId = $this->myDBClass->intLastId;
                }
                // Are there any relations to be filled in?
                if ($intInsertRelations == 1) {
                    foreach ($arrImportRelations as $elem) {
                        foreach ($arrRelations as $reldata) {
                            if ($reldata['fieldName'] == $elem['key']) {
                                $strValue = $elem['value'];
                                $strKey   = $elem['key'];
                                if ($elem['key'] == 'check_command') {
                                    $this->writeRelation5($strValue, $intDataId, $strTable, $reldata);
                                } elseif ($reldata['type'] == 1) {
                                    $this->writeRelation1(
                                        $strKey,
                                        $strValue,
                                        $intDataId,
                                        $strTable,
                                        $reldata,
                                        $arrImportData
                                    );
                                } elseif ($reldata['type'] == 2) {
                                    $this->writeRelation2($strKey, $strValue, $intDataId, $strTable, $reldata);
                                } elseif ($reldata['type'] == 3) {
                                    $this->writeRelation3($strValue, $intDataId, $strTable, $reldata);
                                } elseif ($reldata['type'] == 4) {
                                    $this->writeRelation4($strKey, $strValue, $intDataId, $strTable, $reldata);
                                } elseif ($reldata['type'] == 5) {
                                    $this->writeRelation6($strValue, $intDataId, $strTable, $reldata);
                                } elseif ($reldata['type'] == 6) {
                                    $this->writeRelation7($strValue, $intDataId, $strTable, $reldata);
                                } elseif ($reldata['type'] == 7) {
                                    $this->writeRelation8($strValue, $intDataId, $strTable, $reldata);
                                }
                            }
                        }
                    }
                }
                // Are there any free variables ore time definitions to be filled in?
                if ($intInsertVariables == 1) {
                    if ($strTable == 'tbl_timeperiod') {
                        // Remove old values
                        $strSQL    = "DELETE FROM `tbl_timedefinition` WHERE `tipId` = $intDataId";
                        $booResult = $this->myDBClass->insertData($strSQL);
                        if ($booResult == false) {
                            $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                        }
                        foreach ($arrFreeVariables as $elem) {
                            $strSQL    = "INSERT INTO `tbl_timedefinition` SET `tipId` = $intDataId, ".
                                "`definition` = '".addslashes($elem['key'])."', ".
                                "`range` = '".addslashes($elem['value'])."'";
                            $booResult = $this->myDBClass->insertData($strSQL);
                            if ($booResult == false) {
                                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                            }
                        }
                    } else {
                        $intRemoveOldVariables = 1;
                        foreach ($arrFreeVariables as $elem) {
                            foreach ($arrRelations as $reldata) {
                                if ($reldata['type'] == 4) {
                                    $this->writeRelation4(
                                        $elem['key'],
                                        $elem['value'],
                                        $intDataId,
                                        $intRemoveOldVariables,
                                        $strTable,
                                        $reldata
                                    );
                                    $intRemoveOldVariables = 0;
                                }
                            }
                        }
                    }
                }
                // Update Table times
                $this->myDataClass->updateStatusTable($strTable);
            }
        }
        return $intReturn;
    }

    /**
     * Get table name and key for import
     * @param string $strBlockKey               Block data key
     * @param int $intIsTemplate                Template data 1 = yes / 0 - no
     * @param string $strTable                  Template name
     * @param string $strKeyField               Table key name
     * @return int                              0 = successful / 1 = error
     */
    private function getTableData($strBlockKey, $intIsTemplate, &$strTable, &$strKeyField)
    {
        // Define variables
        $intReturn = 0;
        $arrTableData['command']            = array('tbl_command', 'command_name');
        $arrTableData['contactgroup']       = array('tbl_contactgroup', 'contactgroup_name');
        $arrTableData['contact']            = array('tbl_contact', 'contact_name');
        $arrTableData['timeperiod']         = array('tbl_timeperiod', 'timeperiod_name');
        $arrTableData['host']               = array('tbl_host', 'host_name');
        $arrTableData['service']            = array('tbl_service', '');
        $arrTableData['hostgroup']          = array('tbl_hostgroup', 'hostgroup_name');
        $arrTableData['servicegroup']       = array('tbl_servicegroup', 'servicegroup_name');
        $arrTableData['hostescalation']     = array('tbl_hostescalation', '');
        $arrTableData['serviceescalation']  = array('tbl_serviceescalation', '');
        $arrTableData['hostdependency']     = array('tbl_hostdependency', '');
        $arrTableData['servicedependency']  = array('tbl_servicedependency', '');
        $arrTableData['hostextinfo']        = array('tbl_hostextinfo', 'host_name');
        $arrTableData['serviceextinfo']     = array('tbl_serviceextinfo', '');
        $arrTableData['contactgroup']       = array('tbl_contactgroup', 'contactgroup_name');
        $arrTableDataTpl['contact']         = array('tbl_contacttemplate', 'name');
        $arrTableDataTpl['host']            = array('tbl_hosttemplate', 'name');
        $arrTableDataTpl['service']         = array('tbl_servicetemplate', 'name');

        // Define table name and key
        if (($intIsTemplate == 0) && isset($arrTableData[$strBlockKey])) {
            $strTable    = $arrTableData[$strBlockKey][0];
            /** @noinspection MultiAssignmentUsageInspection */
            $strKeyField = $arrTableData[$strBlockKey][1];
        } elseif (($intIsTemplate == 1) && isset($arrTableDataTpl[$strBlockKey])) {
            $strTable    =  $arrTableDataTpl[$strBlockKey][0];
            /** @noinspection MultiAssignmentUsageInspection */
            $strKeyField =  $arrTableDataTpl[$strBlockKey][1];
        } else {
            $this->strErrorMessage .= translate('Table for import definition').$strBlockKey.
                translate('is not available!') . '::';
            $intReturn = 1;
        }
        return $intReturn;
    }

    /**
     * Create a unique data hash from table data
     * @param $strTable
     * @param $arrBlockData
     * @param $strHash
     * @param $strConfigName
     */
    public function createHash($strTable, $arrBlockData, &$strHash, &$strConfigName)
    {
        $strRawString  = '';
        $strConfigName = 'imp_temporary';
        if ($strTable == 'tbl_service') {
            // HASH from any host, any hostgroup and service description - step 1
            if (isset($arrBlockData['host_name'])) {
                $strRawString .= $arrBlockData['host_name']['value']. ',';
            }
            if (isset($arrBlockData['hostgroup_name'])) {
                $strRawString .= $arrBlockData['hostgroup_name']['value']. ',';
            }
            // Replace *, + and ! in HASH raw string
            $strRawString = str_replace(array('*,', '!', '+'), array('any,', 'not_', ''), $strRawString);
            // Create configuration name from NagiosQL variable if exists
            if (isset($arrBlockData['_NAGIOSQL_CONFIG_NAME'])) {
                $strConfigName  = $arrBlockData['_NAGIOSQL_CONFIG_NAME']['value'];
            } else {
                // Create configuration name from first two hosts / hostgroups
                $arrConfig    = explode(',', $strRawString);
                if (isset($arrConfig[0]) && ($arrConfig[0] != '')) {
                    $strConfigName  = 'imp_' .$arrConfig[0];
                }
                if (isset($arrConfig[1]) && ($arrConfig[1] != '')) {
                    $strConfigName .= '_' .$arrConfig[1];
                }
            }
            // HASH from any host, any hostgroup and service description - step 2
            if (isset($arrBlockData['service_description'])) {
                $strRawString .= $arrBlockData['service_description']['value']. ',';
            }
            if (isset($arrBlockData['display_name'])) {
                $strRawString .= $arrBlockData['display_name']['value']. ',';
            }
            if (isset($arrBlockData['check_command'])) {
                $strRawString .= $arrBlockData['check_command']['value']. ',';
            }
        }
        if (($strTable == 'tbl_hostdependency') || ($strTable == 'tbl_servicedependency')) {
            $strRawString1 = '';
            $strRawString2 = '';
            $strRawString3 = '';
            // HASH from any dependent host and any dependent hostgroup
            if (isset($arrBlockData['dependent_host_name'])) {
                $strRawString1 .= $arrBlockData['dependent_host_name']['value']. ',';
            }
            if (isset($arrBlockData['dependent_hostgroup_name'])) {
                $strRawString1 .= $arrBlockData['dependent_hostgroup_name']['value']. ',';
            }
            if (isset($arrBlockData['host_name'])) {
                $strRawString2 .= $arrBlockData['host_name']['value']. ',';
            }
            if (isset($arrBlockData['hostgroup_name'])) {
                $strRawString2 .= $arrBlockData['hostgroup_name']['value']. ',';
            }
            if (isset($arrBlockData['dependent_service_description'])) {
                $strRawString3 .= $arrBlockData['dependent_service_description']['value']. ',';
            }
            if (isset($arrBlockData['service_description'])) {
                $strRawString3 .= $arrBlockData['service_description']['value']. ',';
            }
            if (isset($arrBlockData['dependent_servicegroup_name'])) {
                $strRawString3 .= $arrBlockData['dependent_servicegroup_name']['value']. ',';
            }
            if (isset($arrBlockData['servicegroup_name'])) {
                $strRawString3 .= $arrBlockData['servicegroup_name']['value']. ',';
            }
            // Replace *, + and ! in HASH raw string
            $strRawString1 = str_replace('*,', 'any,', $strRawString1);
            $strRawString2 = str_replace('*,', 'any,', $strRawString2);
            $strRawString3 = str_replace('*,', 'any,', $strRawString3);
            $strRawString1 = str_replace('!', 'not_', $strRawString1);
            $strRawString2 = str_replace('!', 'not_', $strRawString2);
            $strRawString3 = str_replace('!', 'not_', $strRawString3);
            // Create configuration name from NagiosQL variable if exists
            if (isset($arrBlockData['_NAGIOSQL_CONFIG_NAME'])) {
                $strConfigName  = $arrBlockData['_NAGIOSQL_CONFIG_NAME']['value'];
            } else {
                $arrConfig1    = explode(',', $strRawString1);
                $arrConfig2    = explode(',', $strRawString2);
                $arrConfig3    = explode(',', $strRawString3);
                if (isset($arrConfig1[0])) {
                    $strConfigName  = 'imp_' .$arrConfig1[0];
                }
                if (isset($arrConfig2[0])) {
                    $strConfigName .= '_' .$arrConfig2[0];
                }
                if (isset($arrConfig3[0])) {
                    $strConfigName .= '_' .$arrConfig3[0];
                }
                $strSQL = 'SELECT * FROM `' .$strTable."` WHERE `config_name`='$strConfigName'";
                $booRet    = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDC);
                if ($booRet && ($intDC != 0)) {
                    $intCounter = 1;
                    do {
                        $strConfigNameTemp = $strConfigName. '_' .$intCounter;
                        $strSQL = 'SELECT * FROM `' .$strTable."` WHERE `config_name`='$strConfigNameTemp'";
                        $booRet    = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDC);
                        $intCounter++;
                    } while ($booRet && ($intDC != 0));
                    $strConfigName = $strConfigNameTemp;
                }
            }
            // HASH string
            $strRawString = $strRawString1.$strRawString2.$strRawString3;
            $strRawString = substr($strRawString, 0, -1);
        }
        if (($strTable == 'tbl_hostescalation') || ($strTable == 'tbl_serviceescalation')) {
            $strRawString1 = '';
            $strRawString2 = '';
            $strRawString3 = '';
            // HASH from any host and any hostgroup
            if (isset($arrBlockData['host_name'])) {
                $strRawString1 .= $arrBlockData['host_name']['value']. ',';
            }
            if (isset($arrBlockData['hostgroup_name'])) {
                $strRawString1 .= $arrBlockData['hostgroup_name']['value']. ',';
            }
            if (isset($arrBlockData['contacts'])) {
                $strRawString2 .= $arrBlockData['contacts']['value']. ',';
            }
            if (isset($arrBlockData['contact_groups'])) {
                $strRawString2 .= $arrBlockData['contact_groups']['value']. ',';
            }
            if (isset($arrBlockData['service_description'])) {
                $strRawString3 .= $arrBlockData['service_description']['value']. ',';
            }
            // Replace *, + and ! in HASH raw string
            $strRawString1 = str_replace('*,', 'any,', $strRawString1);
            $strRawString2 = str_replace('*,', 'any,', $strRawString2);
            $strRawString3 = str_replace('*,', 'any,', $strRawString3);
            $strRawString1 = str_replace('!', 'not_', $strRawString1);
            $strRawString2 = str_replace('!', 'not_', $strRawString2);
            $strRawString3 = str_replace('!', 'not_', $strRawString3);
            // Create configuration name from NagiosQL variable if exists
            if (isset($arrBlockData['_NAGIOSQL_CONFIG_NAME'])) {
                $strConfigName  = $arrBlockData['_NAGIOSQL_CONFIG_NAME']['value'];
            } else {
                $arrConfig1    = explode(',', $strRawString1);
                $arrConfig2    = explode(',', $strRawString2);
                $arrConfig3    = explode(',', $strRawString3);
                if (isset($arrConfig1[0])) {
                    $strConfigName  = 'imp_' .$arrConfig1[0];
                }
                if (isset($arrConfig2[0])) {
                    $strConfigName .= '_' .$arrConfig2[0];
                }
                if (isset($arrConfig3[0])) {
                    $strConfigName .= '_' .$arrConfig3[0];
                }
                $strSQL = 'SELECT * FROM `' .$strTable."` WHERE `config_name`='$strConfigName'";
                $booRet    = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDC);
                if ($booRet && ($intDC != 0)) {
                    $intCounter = 1;
                    do {
                        $strConfigNameTemp = $strConfigName. '_' .$intCounter;
                        $strSQL = 'SELECT * FROM `' .$strTable."` WHERE `config_name`='$strConfigNameTemp'";
                        $booRet    = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDC);
                        $intCounter++;
                    } while ($booRet && ($intDC != 0));
                    $strConfigName = $strConfigNameTemp;
                }
            }
            // HASH string
            $strRawString = $strRawString1.$strRawString2.$strRawString3;
            $strRawString = substr($strRawString, 0, -1);
        }
        if ($strTable == 'tbl_serviceextinfo') {
            // HASH from any host, any hostgroup and service description - step 1
            if (isset($arrBlockData['host_name'])) {
                $strRawString .= $arrBlockData['host_name']['value']. ',';
            }
            if (isset($arrBlockData['service_description'])) {
                $strRawString .= $arrBlockData['service_description']['value']. ',';
            }
            // HASH string
            $strRawString = substr($strRawString, 0, -1);
            // Create configuration name from NagiosQL variable if exists
            if (isset($arrBlockData['_NAGIOSQL_CONFIG_NAME'])) {
                $strConfigName  = $arrBlockData['_NAGIOSQL_CONFIG_NAME']['value'];
            } else {
                // Create configuration name from first two items
                $arrConfig    = explode(',', $strRawString);
                if (isset($arrConfig[0]) && ($arrConfig[0] != '')) {
                    $strConfigName  = 'imp_' .$arrConfig[0];
                }
                if (isset($arrConfig[1]) && ($arrConfig[1] != '')) {
                    $strConfigName .= '_' .$arrConfig[1];
                }
            }
        }
        while (substr_count($strRawString, ' ') != 0) {
            $strRawString = str_replace(' ', '', $strRawString);
        }
        // Sort hash string
        $arrTemp = explode(',', $strRawString);
        sort($arrTemp);
        $strRawString = implode(',', $arrTemp);
        $strHash = sha1($strRawString);
        //echo "Hash: ".$strRawString." --> ".$strHash."<br>";
    }

    /**
     * @param array $arrImportData              Imported block data
     * @param string $strHash                   Unique data hash
     * @param int $intExists                    Does the dataset already exist?
     * @param string $strTable                  Table name
     * @param string $strKeyField               Table key file
     * @param int $intRelation                  Relation type
     * @param array $arrRelations               Relation array
     * @param string $strSQL1                   SQL statement part 1
     * @param string $strSQL2                   SQL statement part 2
     */
    private function getSQLPart1(
        $arrImportData,
        $strHash,
        $intExists,
        $strTable,
        $strKeyField,
        $intRelation,
        $arrRelations,
        &$strSQL1,
        &$strSQL2
    ) {
        // Define variables
        $intActive = 1;
        $arrData   = array();
        $intDataCount = 0;

        if ($strHash != '') {
            $strHash = " `import_hash`='" . $strHash . "', ";
        }
        if ($intExists != 0) {
            // Update database
            $strSQL1 = 'UPDATE `' .$strTable. '` SET ';
            $strSQL2 = ' `config_id`=' .$this->intDomainId.", $strHash `active`='$intActive', ".
                "`last_modified`=NOW() WHERE `id`=$intExists";
            // Keep config name while update
            if (($strKeyField == 'config_name') && !isset($arrImportData['_NAGIOSQL_CONFIG_NAME'])) {
                $strSQLConfig = 'SELECT `config_name` FROM `' .$strTable. '` WHERE `id`=' . $intExists;
                $arrImportData['config_name']['value'] = $this->myDBClass->getFieldData($strSQLConfig);
            }
            // Remove free variables
            if ($intRelation != 0) {
                foreach ($arrRelations as $relVar) {
                    if ($relVar['type'] == 4) {
                        $strSQL    = 'SELECT * FROM `' .$relVar['linkTable']."` WHERE `idMaster`=$intExists";
                        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
                        if ($booReturn && ($intDataCount != 0)) {
                            foreach ($arrData as $elem) {
                                $strSQL = 'DELETE FROM `tbl_variabledefinition` WHERE `id`=' .$elem['idSlave'];
                                $this->myDataClass->dataInsert($strSQL, $intInsertId);
                            }
                        }
                        $strSQL = 'DELETE FROM `' .$relVar['linkTable']."` WHERE `idMaster`=$intExists";
                        $this->myDataClass->dataInsert($strSQL, $intInsertId);
                    }
                }
            }
        } else {
            // DB Eintrag einfügen
            $test='';
            $strSQL1  = 'INSERT INTO `' .$strTable."` SET $test";
            $strSQL2  = '  `config_id`=' .$this->intDomainId.", $strHash `active`='$intActive', `last_modified`=NOW()";
        }
    }

    /**
     * @param array $arrImportData              Imported block data
     * @param string $strKeyField               Table key file
     * @param string $strSQL1                   SQL statement part 1
     * @param string $strTable                  Table name
     * @return array                            List of import values
     */
    private function getImportValues($arrImportData, $strKeyField, &$strSQL1, $strTable)
    {
        // Description for the values
        // --------------------------
        // $strVCValues = Simple text values, will be stored as varchar / null = 'null' as text value / empty = ''
        // $strRLValues = Relations - values with relations to other tables
        // $strVWValues = Integer values - will be stored as INT values / null = -1, / empty values as NULL
        // $strVIValues = Decision values 0 = no, 1 = yes, 2 = skip, 3 = null

        // Define variables
        $strVCValues    = '';
        $strVIValues    = '';
        $strRLValues    = '';
        $strVWValues    = '';
        $intWriteConfig = 0;

        // Read command configurations
        if ($strKeyField == 'command_name') {
            $strVCValues = 'command_name,command_line';
            // Find out command type
            if (isset($arrImportData['command_line'])) {
                if ((substr_count($arrImportData['command_line']['value'], 'ARG1') != 0) ||
                    (substr_count($arrImportData['command_line']['value'], 'USER1') != 0)) {
                    $strSQL1 .= '`command_type` = 1,';
                } else {
                    $strSQL1 .= '`command_type` = 2,';
                }
            }
            $intWriteConfig = 1;

            // Read contact configurations
        } elseif ($strKeyField == 'contact_name') {
            $strVCValues  = 'contact_name,alias,host_notification_options,service_notification_options,email,';
            $strVCValues .= 'pager,address1,address2,address3,address4,address5,address6,name';

            $strVWValues  = 'minimum_importance';

            $strVIValues  = 'host_notifications_enabled,service_notifications_enabled,can_submit_commands,';
            $strVIValues .= 'retain_status_information,retain_nonstatus_information';

            $strRLValues  = 'contactgroups,host_notification_period,service_notification_period,';
            $strRLValues .= 'host_notification_commands,service_notification_commands,use';
            $intWriteConfig = 1;

            // Read contactgroup configurations
        } elseif ($strKeyField == 'contactgroup_name') {
            $strVCValues = 'contactgroup_name,alias';

            $strRLValues = 'members,contactgroup_members';
            $intWriteConfig = 1;

            // Read timeperiod configurations
        } elseif ($strKeyField == 'timeperiod_name') {
            $strVCValues = 'timeperiod_name,alias,name';

            $strRLValues = 'use,exclude';
            $intWriteConfig = 1;

            // Read contacttemplate configurations
        } elseif (($strKeyField == 'name') && ($strTable == 'tbl_contacttemplate')) {
            $strVCValues  = 'contact_name,alias,host_notification_options,service_notification_options,email,';
            $strVCValues .= 'pager,address1,address2,address3,address4,address5,address6,name';

            $strVWValues  = 'minimum_importance';

            $strVIValues  = 'host_notifications_enabled,service_notifications_enabled,can_submit_commands,';
            $strVIValues .= 'retain_status_information,retain_nonstatus_information';

            $strRLValues  = 'contactgroups,host_notification_period,service_notification_period,';
            $strRLValues .= 'host_notification_commands,service_notification_commands,use';
            $intWriteConfig = 1;

            // Read host configurations
        } elseif ($strTable == 'tbl_host') {
            $strVCValues  = 'host_name,alias,display_name,address,initial_state,flap_detection_options,';
            $strVCValues .= 'notification_options,stalking_options,notes,notes_url,action_url,icon_image,';
            $strVCValues .= 'icon_image_alt,vrml_image,statusmap_image,2d_coords,3d_coords,name';

            $strVWValues  = 'max_check_attempts,retry_interval,check_interval,freshness_threshold,low_flap_threshold,';
            $strVWValues .= 'high_flap_threshold,notification_interval,first_notification_delay,importance';

            $strVIValues  = 'active_checks_enabled,passive_checks_enabled,check_freshness,obsess_over_host,';
            $strVIValues .= 'event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,';
            $strVIValues .= 'retain_nonstatus_information,notifications_enabled';

            $strRLValues = 'parents,hostgroups,check_command,use,check_period,event_handler,contacts,contact_groups,';
            $strRLValues .= 'notification_period';
            $intWriteConfig = 1;

            // Read hosttemplate configurations
        } elseif (($strKeyField == 'name') && ($strTable == 'tbl_hosttemplate')) {
            $strVCValues  = 'template_name,alias,initial_state,flap_detection_options,notification_options,';
            $strVCValues .= 'stalking_options,notes,notes_url,action_url,icon_image,icon_image_alt,vrml_image,';
            $strVCValues .= 'statusmap_image,2d_coords,3d_coords,name';

            $strVWValues  = 'max_check_attempts,retry_interval,check_interval,freshness_threshold,low_flap_threshold,';
            $strVWValues .= 'high_flap_threshold,notification_interval,first_notification_delay,importance';

            $strVIValues  = 'active_checks_enabled,passive_checks_enabled,check_freshness,obsess_over_host,';
            $strVIValues .= 'event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,';
            $strVIValues .= 'retain_nonstatus_information,notifications_enabled';

            $strRLValues  = 'parents,hostgroups,check_command,use,check_period,event_handler,contacts,contact_groups,';
            $strRLValues .= 'notification_period';
            $intWriteConfig = 1;

            // Read hostgroup configurations
        } elseif ($strKeyField == 'hostgroup_name') {
            $strVCValues = 'hostgroup_name,alias,notes,notes_url,action_url';

            $strRLValues = 'members,hostgroup_members';
            $intWriteConfig = 1;

            // Read service configurations
        } elseif ($strTable == 'tbl_service') {
            $strVCValues  = 'service_description,display_name,initial_state,flap_detection_options,stalking_options,';
            $strVCValues .= 'notes,notes_url,action_url,icon_image,icon_image_alt,name,config_name,';
            $strVCValues .= 'notification_options';

            $strVWValues  = 'max_check_attempts,check_interval,retry_interval,freshness_threshold,low_flap_threshold,';
            $strVWValues .= 'high_flap_threshold,notification_interval,first_notification_delay,importance';

            $strVIValues  = 'is_volatile,active_checks_enabled,passive_checks_enabled,parallelize_check,';
            $strVIValues .= 'obsess_over_service,check_freshness,event_handler_enabled,flap_detection_enabled,';
            $strVIValues .= 'process_perf_data,retain_status_information,retain_nonstatus_information,';
            $strVIValues .= 'notifications_enabled';

            $strRLValues  = 'host_name,hostgroup_name,servicegroups,use,check_command,check_period,event_handler,';
            $strRLValues .= 'notification_period,contacts,contact_groups,parents';
            $intWriteConfig = 1;

            // Read servicetemplate configurations
        } elseif (($strKeyField == 'name') && ($strTable == 'tbl_servicetemplate')) {
            $strVCValues  = 'template_name,service_description,display_name,initial_state,flap_detection_options,';
            $strVCValues .= 'stalking_options,notes,notes_url,action_url,icon_image,icon_image_alt,name,';
            $strVCValues .= 'notification_options';

            $strVWValues  = 'max_check_attempts,check_interval,retry_interval,freshness_threshold,low_flap_threshold,';
            $strVWValues .= 'high_flap_threshold,notification_interval,first_notification_delay,importance';

            $strVIValues  = 'is_volatile,active_checks_enabled,passive_checks_enabled,parallelize_check,';
            $strVIValues .= 'obsess_over_service,check_freshness,event_handler_enabled,flap_detection_enabled,';
            $strVIValues .= 'process_perf_data,retain_status_information,retain_nonstatus_information,';
            $strVIValues .= 'notifications_enabled';

            $strRLValues  = 'host_name,hostgroup_name,servicegroups,use,check_command,check_period,event_handler,';
            $strRLValues .= 'notification_period,contacts,contact_groups,parents';
            $intWriteConfig = 1;

            // Read servicegroup configurations
        } elseif ($strKeyField == 'servicegroup_name') {
            $strVCValues = 'servicegroup_name,alias,notes,notes_url,action_url';

            $strRLValues = 'members,servicegroup_members';
            $intWriteConfig = 1;

            // Read hostdependency configurations
        } elseif ($strTable == 'tbl_hostdependency') {
            $strVCValues = 'config_name,execution_failure_criteria,notification_failure_criteria';

            $strVIValues = 'inherits_parent';

            $strRLValues = 'dependent_host_name,dependent_hostgroup_name,host_name,hostgroup_name,dependency_period';
            $intWriteConfig = 1;

            // Read hostescalation configurations
        } elseif ($strTable == 'tbl_hostescalation') {
            $strVCValues = 'config_name,escalation_options';

            $strVWValues = 'first_notification,last_notification,notification_interval';

            $strRLValues = 'host_name,hostgroup_name,contacts,contact_groups,escalation_period';
            $intWriteConfig = 1;

            // Read hostextinfo configurations
        } elseif ($strTable == 'tbl_hostextinfo') {
            $strVCValues  = 'notes,notes_url,action_url,icon_image,icon_image_alt,vrml_image,statusmap_image,';
            $strVCValues .= '2d_coords,3d_coords';

            $strRLValues = 'host_name';
            $intWriteConfig = 1;

            // Read servicedependency configurations
        } elseif ($strTable == 'tbl_servicedependency') {
            $strVCValues  = 'config_name,execution_failure_criteria,notification_failure_criteria';

            $strVIValues  = 'inherits_parent';

            $strRLValues  = 'dependent_host_name,dependent_hostgroup_name,dependent_service_description,host_name,';
            $strRLValues .= 'hostgroup_name,dependency_period,service_description,dependent_servicegroup_name,';
            $strRLValues .= 'servicegroup_name';
            $intWriteConfig = 1;

            // Read serviceescalation configurations
        } elseif ($strTable == 'tbl_serviceescalation') {
            $strVCValues  = 'config_name,escalation_options';

            $strVIValues  = 'first_notification,last_notification,notification_interval';

            $strRLValues  = 'host_name,hostgroup_name,contacts,contact_groups,service_description,escalation_period,';
            $strRLValues .= 'servicegroup_name';
            $intWriteConfig = 1;

            // Serviceextinfo configurations
        } elseif ($strTable == 'tbl_serviceextinfo') {
            $strVCValues = 'notes,notes_url,action_url,icon_image,icon_image_alt';

            $strRLValues = 'host_name,service_description';
            $intWriteConfig = 1;
        }

        // Common values (all configurations)
        if ($strVWValues == '') {
            $strVWValues = 'register';
        } else {
            $strVWValues .= ',register';
        }
        return array($strVCValues, $intWriteConfig, $strVIValues, $strRLValues, $strVWValues);
    }

    /**
     * @param $elem
     * @param $strVCValues
     * @param $strSQL1
     * @param $intIsTemplate
     * @param $intExists
     * @param $strTable
     * @return int
     */
    private function writeTextValues($elem, $strVCValues, &$strSQL1, $intIsTemplate, $intExists, $strTable)
    {
        $intCheck = 0;
        if (\in_array($elem['key'], explode(',', $strVCValues), true)) {
            if (strtolower(trim($elem['value'])) == 'null') {
                $strSQL1 .= '`' . $elem['key'] . "` = 'null',";
            } else {
                $elem['value'] = addslashes($elem['value']);
                if ($intIsTemplate == 1) {
                    if ($elem['key'] == 'name') {
                        $strSQL1 .= "template_name = '" . $elem['value'] . "',";
                    } elseif (($elem['key'] == 'config_name') && ($intExists != 0)) {
                        // Do not overwrite config_names during an update!
                        $strSQLConfig = 'SELECT `config_name` FROM `' . $strTable . '` WHERE `id`=' . $intExists;
                        $elem['value'] = $this->myDBClass->getFieldData($strSQLConfig);
                        $strSQL1 .= '`' . $elem['key'] . "` = '" . $elem['value'] . "',";
                    } else {
                        $strSQL1 .= '`' . $elem['key'] . "` = '" . $elem['value'] . "',";
                    }
                } else {
                    $strSQL1 .= '`' . $elem['key'] . "` = '" . $elem['value'] . "',";
                }
            }
            $intCheck = 1;
        }
        return $intCheck;
    }

    /**
     * @param $elem
     * @param $strVIValues
     * @param $strSQL1
     * @return int
     */
    private function writeStatusValues($elem, $strVIValues, &$strSQL1)
    {
        $intCheck = 0;
        if (\in_array($elem['key'], explode(',', $strVIValues), true)) {
            if (strtolower(trim($elem['value'])) == 'null') {
                $strSQL1 .= '`' . $elem['key'] . '` = 3,';
            } else {
                $strSQL1 .= '`' . $elem['key'] . "` = '" . $elem['value'] . "',";
            }
            $intCheck = 1;
        }
        return $intCheck;
    }

    /**
     * @param $elem
     * @param $strVWValues
     * @param $strSQL1
     * @return int
     */
    private function writeIntegerValues($elem, $strVWValues, &$strSQL1)
    {
        $intCheck = 0;
        if (\in_array($elem['key'], explode(',', $strVWValues), true)) {
            if (strtolower(trim($elem['value'])) == 'null') {
                $strSQL1 .= '`' . $elem['key'] . '` = -1,';
            } else {
                $strSQL1 .= '`' . $elem['key'] . "` = '" . $elem['value'] . "',";
            }
            $intCheck = 1;
        }
        return $intCheck;
    }

    /**
     * @param array $elem
     * @param string $strRLValues
     * @param array $arrImportRelations
     * @param $intInsertRelations
     * @return int
     */
    private function writeRelations(&$elem, $strRLValues, &$arrImportRelations, &$intInsertRelations)
    {
        $intCheck = 0;
        if (($intCheck == 0) && \in_array($elem['key'], explode(',', $strRLValues), true)) {
            if ($elem['key'] == 'use') {
                $elem['key'] = 'use_template';
            }
            $arrTemp = array();
            $arrTemp['key'] = $elem['key'];
            $arrTemp['value'] = $elem['value'];
            $arrImportRelations[] = $arrTemp;
            $intInsertRelations = 1;
            $intCheck = 1;
        }
        return $intCheck;
    }

    /**
     * Inserts a relation type 1 (1:1)
     * @param string $strKey                    Data field name
     * @param string $strValue                  Data value
     * @param int $intDataId                    Data ID
     * @param string $strDataTable              Data table (Master)
     * @param array $arrRelData                 Relation data
     * @param array $arrImportData              Import Data
     */
    public function writeRelation1($strKey, $strValue, $intDataId, $strDataTable, $arrRelData, $arrImportData)
    {
        // Define variables
        $intSlaveId = 0;
        if (strtolower(trim($strValue)) == 'null') {
            // Update data in master table
            $strSQL   = 'UPDATE `' .$strDataTable. '` SET `' .$arrRelData['fieldName']. '` = -1 WHERE `id` = '
                .$intDataId;
            $booResult  = $this->myDBClass->insertData($strSQL);
            if ($booResult == false) {
                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
            }
        } else {
            // Decompose data value
            $arrValues = explode(',', $strValue);
            // Process data values
            foreach ($arrValues as $elem) {
                $strWhere = '';
                $strLink  = '';
                $strAdd   = '';
                // Special processing for serviceextinfo
                if (($strDataTable == 'tbl_serviceextinfo') && ($strKey == 'service_description')) {
                    $strLink  = 'LEFT JOIN `tbl_lnkServiceToHost` on `tbl_service`.`id`=`idMaster` ' .
                        'LEFT JOIN `tbl_host` ON `idSlave`=`tbl_host`.`id`';
                    $strWhere = "AND `tbl_host`.`host_name`='".$arrImportData['host_name']['value']."'";
                }
                // Does the value already exist?
                $strSQL = 'SELECT `' .$arrRelData['tableName1']. '`.`id` FROM `' .$arrRelData['tableName1'].
                    "` $strLink " . 'WHERE `' .$arrRelData['target1']."` = '".$elem."' $strWhere AND ".
                    '`' .$arrRelData['tableName1']."`.`active`='1' AND ".
                    '`' .$arrRelData['tableName1']. '`.`config_id`=' .$this->intDomainId;
                $strId  = $this->myDBClass->getFieldData($strSQL);
                if ($strId != '') {
                    $intSlaveId = (int)$strId;
                }
                if ($intSlaveId == 0) {
                    // Insert a temporary value
                    if (($strDataTable == 'tbl_serviceextinfo') && ($arrRelData['tableName1'] == 'tbl_service')) {
                        $strAdd = "`config_name`='imp_tmp_by_serviceextinfo',";
                    }
                    $strSQL    = 'INSERT INTO `' .$arrRelData['tableName1']. '` ' .
                        'SET `' .$arrRelData['target1']."` = '".$elem."', ".
                        "$strAdd `config_id`=".$this->intDomainId.", `active`='0', ".
                        '`last_modified`=NOW()';
                    $booResult  = $this->myDBClass->insertData($strSQL);
                    if ($booResult == false) {
                        $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                    }
                    $intSlaveId = $this->myDBClass->intLastId;

                    // Special processing for serviceextinfo
                    if (($strDataTable == 'tbl_serviceextinfo') && ($strKey == 'service_description')) {
                        $strSQL = 'SELECT `id` FROM `tbl_host` ' .
                            "WHERE `host_name`='".$arrImportData['host_name']['value']."'";
                        $strId  = $this->myDBClass->getFieldData($strSQL);
                        if ($strId != '') {
                            $strSQL     = 'INSERT INTO `tbl_lnkServiceToHost` ' .
                                "SET `idMaster` = '".$intSlaveId."', `idSlave` = '".$strId."'";
                            $booResult  = $this->myDBClass->insertData($strSQL);
                            if ($booResult == false) {
                                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                            }
                            $strSQL    = "UPDATE `tbl_service` SET `host_name`=0 WHERE `id`='".$intSlaveId."'";
                            $booResult  = $this->myDBClass->insertData($strSQL);
                            if ($booResult == false) {
                                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                            }
                        }
                    }
                }
                // Update data in master table
                $strSQL    = 'UPDATE `' .$strDataTable. '` SET `' .$arrRelData['fieldName']. '` = ' .$intSlaveId. ' ' .
                    'WHERE `id` = ' .$intDataId;
                $booResult  = $this->myDBClass->insertData($strSQL);
                if ($booResult == false) {
                    $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                }
            }
        }
    }

    /**
     * Inserts a relation type 2 (1:n)
     * @param string $strKey                    Data field name
     * @param string $strValue                  Data value
     * @param int $intDataId                    Data ID
     * @param string $strDataTable              Data table (Master)
     * @param array $arrRelData                 Relation data
     */
    public function writeRelation2($strKey, $strValue, $intDataId, $strDataTable, $arrRelData)
    {
        // Does a tploption field exist?
        $strSQL = 'SELECT * FROM `' .$strDataTable. '` WHERE `id` = ' .$intDataId;
        $this->myDBClass->hasSingleDataset($strSQL, $arrDataset);
        $strFieldName = $arrRelData['fieldName']. '_tploptions';
        if (isset($arrDataset[$strFieldName])) {
            $intTplOption = 1;
        } else {
            $intTplOption = 0;
        }
        // Delete data from link table
        $strSQL    = 'DELETE FROM `' .$arrRelData['linkTable']. '` WHERE `idMaster` = ' .$intDataId;
        $booResult = $this->myDBClass->insertData($strSQL);
        if ($booResult == false) {
            $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
        }
        // Define variables
        if (strtolower(trim($strValue)) == 'null') {
            // Update data in master table
            if ($intTplOption == 1) {
                $strSQL = 'UPDATE `' .$strDataTable. '` SET `' .$arrRelData['fieldName']. '` = 0, ' .
                    '`' .$arrRelData['fieldName']. '_tploptions` = 1  WHERE `id` = ' .$intDataId;
            } else {
                $strSQL = 'UPDATE `' .$strDataTable. '` SET `' .$arrRelData['fieldName']. '` = 0 WHERE `id` = ' .
                    $intDataId;
            }
            $this->myDBClass->insertData($strSQL);
        } else {
            if (0 === strpos(trim($strValue), '+')) {
                $intOption = 0;
                $strValue = str_replace('+', '', $strValue);
            } else {
                $intOption = 2;
            }
            // Decompose data value
            $arrValues = explode(',', $strValue);
            // Process data values
            foreach ($arrValues as $elem) {
                if ($elem != '*') {
                    $strWhere = '';
                    $strLink  = '';
                    // Exclude values
                    if (0 === strpos($elem, '!')) {
                        $intExclude = 1;
                        $elem = substr($elem, 1);
                    } else {
                        $intExclude = 0;
                    }
                    if ((($strDataTable == 'tbl_servicedependency') || ($strDataTable == 'tbl_serviceescalation')) &&
                        (substr_count($strKey, 'service') != 0) && (substr_count($strKey, 'group') == 0)) {
                        if (substr_count($strKey, 'depend') != 0) {
                            $strLink  = 'LEFT JOIN `tbl_lnkServiceToHost` on `id`=`idMaster`';
                            $strWhere = 'AND `idSlave` IN (' .substr($this->strList1, 0, -1). ')';
                        } else {
                            $strLink  = 'LEFT JOIN `tbl_lnkServiceToHost` on `id`=`idMaster`';
                            $strWhere = 'AND `idSlave` IN (' .substr($this->strList2, 0, -1). ')';
                        }
                    }
                    // Does the entry already exist?
                    $strSQL = 'SELECT `id` FROM `' .$arrRelData['tableName1']."` $strLink ".
                        'WHERE `' .$arrRelData['target1']."` = '".$elem."' $strWhere AND ".
                        '`config_id`=' .$this->intDomainId;
                    $strId  = $this->myDBClass->getFieldData($strSQL);
                    if ($strId != '') {
                        $intSlaveId = (int)$strId;
                    } else {
                        $intSlaveId = 0;
                    }
                    if (($intSlaveId == 0) && ($elem != '*')) {
                        // Insert a temporary value to the target table
                        $strSQL    = 'INSERT INTO `' .$arrRelData['tableName1']. '` ' .
                            'SET `' .$arrRelData['target1']."`='".$elem."', ".
                            '`config_id`=' .$this->intDomainId.", `active`='0', `last_modified`=NOW()";
                        $booResult = $this->myDBClass->insertData($strSQL);
                        if ($booResult == false) {
                            $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                        }
                        $intSlaveId = $this->myDBClass->intLastId;
                    }
                    // Insert relations
                    $strSQL    = 'INSERT INTO `' .$arrRelData['linkTable']. '` ' .
                        'SET `idMaster` = ' .$intDataId. ', `idSlave` = ' .$intSlaveId. ', `exclude`=' .$intExclude;
                    $booResult = $this->myDBClass->insertData($strSQL);
                    if ($booResult == false) {
                        $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                    }
                    // Keep values
                    if (($strDataTable == 'tbl_servicedependency') || ($strDataTable == 'tbl_serviceescalation')) {
                        $strTemp = '';
                        if (($strKey == 'dependent_host_name') || ($strKey == 'host_name')) {
                            $strTemp .= $intSlaveId. ',';
                        } elseif (($strKey == 'dependent_hostgroup_name') || ($strKey == 'hostgroup_name')) {
                            $arrDataHostgroups = array();
                            $intDCHostgroups   = 0;
                            $strSQL = 'SELECT DISTINCT `id` FROM `tbl_host` ' .
                                'LEFT JOIN `tbl_lnkHostToHostgroup` ON `id`=`tbl_lnkHostToHostgroup`.`idMaster` ' .
                                'LEFT JOIN `tbl_lnkHostgroupToHost` ON `id`=`tbl_lnkHostgroupToHost`.`idSlave` ' .
                                "WHERE (`tbl_lnkHostgroupToHost`.`idMaster` = $intSlaveId ".
                                "OR `tbl_lnkHostToHostgroup`.`idSlave` = $intSlaveId) ".
                                "AND `active`='1' AND `config_id`=".$this->intDomainId;
                            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrDataHostgroups, $intDCHostgroups);
                            if ($booReturn && ($intDCHostgroups != 0)) {
                                foreach ($arrDataHostgroups as $elem2) {
                                    $strTemp .= $elem2['id']. ',';
                                }
                            }
                        }
                        if (substr_count($strKey, 'dependent') != 0) {
                            $this->strList1 .= $strTemp;
                        } else {
                            $this->strList2 .= $strTemp;
                        }
                    }
                }
                // Update field values in master table
                if (substr_count($strValue, '*') != 0) {
                    $intRelValue = 2;
                } else {
                    $intRelValue = 1;
                }
                if ($intTplOption == 1) {
                    $strSQL = 'UPDATE `' .$strDataTable. '` SET `' .$arrRelData['fieldName']."`=$intRelValue, ".
                        '`' .$arrRelData['fieldName']. '_tploptions` = ' .$intOption. ' WHERE `id` = ' .$intDataId;
                } else {
                    $strSQL = 'UPDATE `' .$strDataTable. '` SET `' .$arrRelData['fieldName']."`=$intRelValue ".
                        'WHERE `id` = ' .$intDataId;
                }
                $booResult  = $this->myDBClass->insertData($strSQL);
                if ($booResult == false) {
                    $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                }
            }
        }
    }

    /**
     * Inserts a relation type 3 (templates)
     * @param string $strValue                  Data value
     * @param int $intDataId                    Data ID
     * @param string $strDataTable              Data table (Master)
     * @param array $arrRelData                 Relation data
     */
    public function writeRelation3($strValue, $intDataId, $strDataTable, $arrRelData)
    {
        // Define variables
        $intSlaveId = 0;
        $intTable   = 0;
        $intSortNr  = 1;
        if (strtolower(trim($strValue)) == 'null') {
            // Update data in master table
            $strSQL = 'UPDATE `' .$strDataTable. '` SET `' .$arrRelData['fieldName']. '` = 0, ' .
                '`' .$arrRelData['fieldName']. '_tploptions` = 1  WHERE `id` = ' .$intDataId;
            $this->myDBClass->insertData($strSQL);
        } else {
            if (0 === strpos(trim($strValue), '+')) {
                $intOption = 0;
                $strValue = str_replace('+', '', $strValue);
            } else {
                $intOption = 2;
            }
            // Remove old relations
            $strSQL    = 'DELETE FROM `' .$arrRelData['linkTable']. '` WHERE `idMaster` = ' .$intDataId;
            $booResult = $this->myDBClass->insertData($strSQL);
            if ($booResult == false) {
                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
            }
            // Decompose data value
            $arrValues = explode(',', $strValue);
            // Process data values
            foreach ($arrValues as $elem) {
                // Does the template already exist? (table 1)
                $strSQL = 'SELECT `id` FROM `' .$arrRelData['tableName1']. '` ' .
                    'WHERE `' .$arrRelData['target1']."` = '".$elem."' AND `config_id`=".$this->intDomainId;
                $strId  = $this->myDBClass->getFieldData($strSQL);
                if ($strId != '') {
                    $intSlaveId = (int)$strId;
                    $intTable = 1;
                }
                if ($intSlaveId == 0) {
                    // Does the template already exist? (table 2)
                    $strSQL = 'SELECT `id` FROM `' .$arrRelData['tableName2']. '` ' .
                        'WHERE `' .$arrRelData['target2']."` = '".$elem."' AND `config_id`=".$this->intDomainId;
                    $strId  = $this->myDBClass->getFieldData($strSQL);
                    if ($strId != '') {
                        $intSlaveId = (int)$strId;
                        $intTable   = 2;
                    }
                }
                if ($intSlaveId == 0) {
                    // Insert a temporary value to the target table
                    $strSQL    = 'INSERT INTO `' .$arrRelData['tableName1']. '` ' .
                        'SET `' .$arrRelData['target1']."` = '".$elem."', `config_id`=".$this->intDomainId. ', ' .
                        "`active`='0', `last_modified`=NOW()";
                    $booResult = $this->myDBClass->insertData($strSQL);
                    if ($booResult == false) {
                        $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                    }
                    $intSlaveId = $this->myDBClass->intLastId;
                    $intTable   = 1;
                }
                // Insert relations
                $strSQL    = 'INSERT INTO `' .$arrRelData['linkTable']. '` ' .
                    'SET `idMaster` = ' .$intDataId. ', `idSlave`=' .$intSlaveId. ', `idSort`=' .$intSortNr. ', ' .
                    '`idTable` = ' .$intTable;
                $booResult = $this->myDBClass->insertData($strSQL);
                if ($booResult == false) {
                    $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                }
                $intSortNr++;
                $intSlaveId = 0;
                // Update field data in master table
                $strSQL    = 'UPDATE `' .$strDataTable. '` SET `' .$arrRelData['fieldName']. '` = 1, ' .
                    '`' .$arrRelData['fieldName']. '_tploptions` = ' .$intOption. ' WHERE `id` = ' .$intDataId;
                $booResult = $this->myDBClass->insertData($strSQL);
                if ($booResult == false) {
                    $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                }
            }
        }
    }

    /**
     * Inserts a relation type 4 (free variables)
     * @param string $strKey                    Data field name
     * @param string $strValue                  Data value
     * @param int $intDataId                    Data ID
     * @param int $intRemoveData                0 = do not remove data / 1 = do remove data
     * @param string $strDataTable              Data table (Master)
     * @param array $arrRelData                 Relation data
     * @return int                              0 = successful / 1 = error
     */
    public function writeRelation4($strKey, $strValue, $intDataId, $intRemoveData, $strDataTable, $arrRelData)
    {
        // Define variables
        $intReturn = 0;
        // Remove empty variables
        if (($strKey == '') || ($strValue == '')) {
            $intReturn = 1;
        }
        // Remove NagiosQL variables
        if ($strKey == '_NAGIOSQL_CONFIG_NAME') {
            $intReturn = 1;
        }
        // Remove old variables
        if ($intRemoveData == 1) {
            $strSQL = 'SELECT * FROM '.$arrRelData['linkTable'].' WHERE idMaster='.$intDataId;
            $booResult = $this->myDBClass->hasDataArray($strSQL, $arrLinkData, $intLinkCount);
            if ($booResult && ($intLinkCount != 0)) {
                /** @var array $arrLinkData */
                foreach ($arrLinkData as $elem) {
                    $strSQL1 = 'DELETE FROM tbl_variabledefinition WHERE id=' .$elem['idSlave'];
                    $booResult = $this->myDBClass->insertData($strSQL1);
                    if ($booResult == false) {
                        $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                    }
                    $strSQL2 = 'DELETE FROM '.$arrRelData['linkTable'].' WHERE idMaster='.$elem['idMaster'];
                    $booResult = $this->myDBClass->insertData($strSQL2);
                    if ($booResult == false) {
                        $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                    }
                }
            }
        }
        // Insert free Variables
        if ($intReturn == 0) {
            // Insert values to the table
            $strSQL = "INSERT INTO `tbl_variabledefinition` SET `name` = '" . addslashes($strKey) . "', " .
                "`value` = '" . addslashes($strValue) . "', `last_modified`=now()";
            $booResult = $this->myDBClass->insertData($strSQL);
            if ($booResult == false) {
                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
            }
            $intSlaveId = $this->myDBClass->intLastId;
            // Insert relations to the table
            $strSQL = 'INSERT INTO `' . $arrRelData['linkTable'] . '` ' .
                'SET `idMaster` = ' . $intDataId . ', `idSlave` = ' . $intSlaveId;
            $booResult = $this->myDBClass->insertData($strSQL);
            if ($booResult == false) {
                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
            }
            // Update data in master table
            $strSQL = 'UPDATE `' . $strDataTable . '` SET `use_variables` = 1 WHERE `id` = ' . $intDataId;
            $booResult = $this->myDBClass->insertData($strSQL);
            if ($booResult == false) {
                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
            }
        }
        return $intReturn;
    }

    /**
     * Inserts a relation type 5 (1:1 check command)
     * @param string $strValue                  Data value
     * @param int $intDataId                    Data ID
     * @param string $strDataTable              Data table (Master)
     * @param array $arrRelData                 Relation data
     */
    public function writeRelation5($strValue, $intDataId, $strDataTable, $arrRelData)
    {
        // Extract data values
        $arrCommand = explode('!', $strValue);
        $strValue   = $arrCommand[0];
        // Define variables
        $intSlaveId = 0;
        if (strtolower(trim($strValue)) == 'null') {
            // Update data in master table
            $strSQL   = 'UPDATE `' .$strDataTable. '` SET `' .$arrRelData['fieldName']. '` = -1 WHERE `id` = ' .
                $intDataId;
            $booResult  = $this->myDBClass->insertData($strSQL);
            if ($booResult == false) {
                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
            }
        } else {
            // Decompose data values
            $arrValues = explode(',', $strValue);
            // Process data values
            foreach ($arrValues as $elem) {
                // Does the entry already exist?
                $strSQL = 'SELECT `id` FROM `' .$arrRelData['tableName1']. '` ' .
                    'WHERE `' .$arrRelData['target1']."` = '".$elem."' AND `config_id`=".$this->intDomainId;
                $strId  = $this->myDBClass->getFieldData($strSQL);
                if ($strId != '') {
                    $intSlaveId = (int)$strId;
                }
                if ($intSlaveId == 0) {
                    // Insert a temporary value in target table
                    $strSQL    = 'INSERT INTO `' .$arrRelData['tableName1']. '` ' .
                        'SET `' .$arrRelData['target1']."` = '".$elem."', `config_id`=".$this->intDomainId. ', ' .
                        "`active`='0', `last_modified`=NOW()";
                    $booResult = $this->myDBClass->insertData($strSQL);
                    if ($booResult == false) {
                        $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                    }
                    $intSlaveId = $this->myDBClass->intLastId;
                }
                // Update data in master table
                $arrCommand[0] = $intSlaveId;
                $strValue      = implode('!', $arrCommand);
                $strSQL        = 'UPDATE `' .$strDataTable. '` ' .
                    'SET `' .$arrRelData['fieldName']."`='".$this->myDBClass->realEscape($strValue)."' ".
                    'WHERE `id` = ' .$intDataId;
                $booResult     = $this->myDBClass->insertData($strSQL);
                if ($booResult == false) {
                    $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                }
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Inserts a relation type 5 (1:n:n service groups)
     * @param string $strValue                  Data value
     * @param int $intDataId                    Data ID
     * @param string $strDataTable              Data table (Master)
     * @param array $arrRelData                 Relation data
     */
    public function writeRelation6($strValue, $intDataId, $strDataTable, $arrRelData)
    {
        // Define variables
        $intSlaveIdH  = 0;
        $intSlaveIdHG = 0;
        // Decompose data value
        $arrValues    = explode(',', $strValue);
        // Remove data from link table
        $strSQL    = 'DELETE FROM `' .$arrRelData['linkTable']. '` WHERE `idMaster` = ' .$intDataId;
        $booResult = $this->myDBClass->insertData($strSQL);
        if ($booResult == false) {
            $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
        }
        // Check the sum of elements
        if (\count($arrValues) % 2 != 0) {
            $this->strErrorMessage .= translate('Error: incorrect number of arguments - cannot import service group ' .
                    'members'). '::';
        } else {
            // Process data values
            $intCounter = 1;
            foreach ($arrValues as $elem) {
                if ($intCounter % 2 == 0) {
                    // Does the host entry already exist?
                    $strSQL = 'SELECT `id` FROM `' .$arrRelData['tableName1']. '` ' .
                        'WHERE `' .$arrRelData['target1']."` = '".$strValue."' AND `active`='1' ".
                        'AND `config_id`=' .$this->intDomainId;
                    $strId  = $this->myDBClass->getFieldData($strSQL);
                    if ($strId != '') {
                        $intSlaveIdH = (int)$strId;
                    }
                    // Does a hostgroup entry already exist?
                    if ($intSlaveIdH == 0) {
                        $strSQL = "SELECT `id` FROM `tbl_hostgroup` WHERE `hostgroup_name` = '".$strValue."' ".
                            "AND `active`='1' AND `config_id`=".$this->intDomainId;
                        $strId  = $this->myDBClass->getFieldData($strSQL);
                        if ($strId != '') {
                            $intSlaveIdHG = (int)$strId;
                        }
                    }
                    if (($intSlaveIdH == 0) && ($intSlaveIdHG == 0)) {
                        // Insert a temporary value in table
                        $strSQL    = 'INSERT INTO `' .$arrRelData['tableName1']. '` ' .
                            'SET `' .$arrRelData['target1']."` = '".$strValue."', ".
                            '`config_id`=' .$this->intDomainId.", `active`='0', `last_modified`=NOW()";
                        $booResult = $this->myDBClass->insertData($strSQL);
                        if ($booResult == false) {
                            $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                        }
                        $intSlaveIdH    = $this->myDBClass->intLastId;
                    }
                    // Does the service entry already exist?
                    if ($intSlaveIdH != 0) {
                        $strSQL = 'SELECT `id` FROM `' .$arrRelData['tableName2']. '` ' .
                            'LEFT JOIN `tbl_lnkServiceToHost` ON `id` = `idMaster` ' .
                            'WHERE `' .$arrRelData['target2']."` = '".$elem."' AND `idSlave` = ".$intSlaveIdH. ' ' .
                            'AND `config_id`=' .$this->intDomainId;
                        $strId  = $this->myDBClass->getFieldData($strSQL);
                        if ($strId == '') {
                            $strSQL = 'SELECT `id` FROM `' .$arrRelData['tableName2']. '` ' .
                                'LEFT JOIN `tbl_lnkServiceToHostgroup` ON ' .
                                '`id`=`tbl_lnkServiceToHostgroup`.`idMaster` ' .
                                'LEFT JOIN `tbl_lnkHostgroupToHost` ON ' .
                                '`tbl_lnkHostgroupToHost`.`idMaster`=`tbl_lnkServiceToHostgroup`.`idSlave` ' .
                                'WHERE `' .$arrRelData['target2']."` = '".$elem."' AND ".
                                '`tbl_lnkHostgroupToHost`.`idSlave` = ' .$intSlaveIdH. ' AND ' .
                                "`active`='1' AND `config_id`=".$this->intDomainId;
                            $strId  = $this->myDBClass->getFieldData($strSQL);
                        }
                        if ($strId == '') {
                            $strSQL = 'SELECT `id` FROM `' .$arrRelData['tableName2']. '` ' .
                                'LEFT JOIN `tbl_lnkServiceToHostgroup` ON ' .
                                '`id` = `tbl_lnkServiceToHostgroup`.`idMaster` ' .
                                'LEFT JOIN `tbl_lnkHostToHostgroup` ON ' .
                                '`tbl_lnkHostToHostgroup`.`idSlave`=`tbl_lnkServiceToHostgroup`.`idSlave` ' .
                                'WHERE `' .$arrRelData['target2']."` = '".$elem."' AND ".
                                '`tbl_lnkHostToHostgroup`.`idMaster` = ' .$intSlaveIdH. ' AND ' .
                                "`active`='1' AND `config_id`=".$this->intDomainId;
                            $strId  = $this->myDBClass->getFieldData($strSQL);
                        }
                    } elseif ($intSlaveIdHG != 0) {
                        $strSQL = 'SELECT `id` FROM `' .$arrRelData['tableName2']. '` ' .
                            'LEFT JOIN `tbl_lnkServiceToHostgroup` ON `id` = `idMaster` ' .
                            'WHERE `' .$arrRelData['target2']."` = '".$elem."' AND `idSlave`=".$intSlaveIdHG. ' ' .
                            "AND `active`='1' AND `config_id`=".$this->intDomainId;
                        $strId  = $this->myDBClass->getFieldData($strSQL);
                    }
                    if ($strId != '') {
                        $intSlaveIdS = (int)$strId;
                    } else {
                        $intSlaveIdS = 0;
                    }
                    if ($intSlaveIdS == 0) {
                        // Insert a temporary value in table
                        $intHostName      = 0;
                        $intHostgroupName = 0;
                        if ($intSlaveIdH != 0) {
                            $intHostName        = 1;
                        } elseif ($intSlaveIdHG != 0) {
                            $intHostgroupName    = 1;
                        }
                        $strSQL    = 'INSERT INTO `' .$arrRelData['tableName2']. '` ' .
                            "SET `config_name`='imp_tmp_by_servicegroup', `host_name`=$intHostName, ".
                            "`hostgroup_name`=$intHostgroupName, `".$arrRelData['target2']."` = '".$elem."', ".
                            '`config_id`=' .$this->intDomainId.", `active`='0', `last_modified`=NOW()";
                        $booResult = $this->myDBClass->insertData($strSQL);
                        if ($booResult == false) {
                            $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                        }
                        $intSlaveIdS = $this->myDBClass->intLastId;
                        // Make a relation from temp service to host / hostgroup
                        if ($intSlaveIdH != 0) {
                            $strSQL    = 'INSERT INTO `tbl_lnkServiceToHost` ' .
                                "SET `idMaster`='".$intSlaveIdS."', `idSlave`=".$intSlaveIdH.", `exclude`='0'";
                            $booResult = $this->myDBClass->insertData($strSQL);
                            if ($booResult == false) {
                                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                            }
                        } elseif ($intSlaveIdHG != 0) {
                            $strSQL    = 'INSERT INTO `tbl_lnkServiceToHostgroup` ' .
                                "SET `idMaster`='".$intSlaveIdS."', `idSlave`=".$intSlaveIdHG. ', ' .
                                "`exclude`='0'";
                            $booResult = $this->myDBClass->insertData($strSQL);
                            if ($booResult == false) {
                                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                            }
                        }
                    }
                    // Insert relation
                    $strSQL    = 'INSERT INTO `' .$arrRelData['linkTable']. '` ' .
                        'SET `idMaster`=' .$intDataId. ', `idSlaveH`=' .$intSlaveIdH. ', `idSlaveS`=' .$intSlaveIdS;
                    $booResult = $this->myDBClass->insertData($strSQL);
                    if ($booResult == false) {
                        $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                    }
                    // Update data in master table
                    $strSQL    = 'UPDATE `' .$strDataTable. '` ' .
                        'SET `' .$arrRelData['fieldName']. '` = 1 WHERE `id` = ' .$intDataId;
                    $booResult = $this->myDBClass->insertData($strSQL);
                    if ($booResult == false) {
                        $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                    }
                } else {
                    $strValue = $elem;
                }
                $intCounter++;
            }
        }
    }

    /**
     * Inserts a relation type 6 (1:n:str)
     * @param string $strValue                  Data value
     * @param int $intDataId                    Data ID
     * @param string $strDataTable              Data table (Master)
     * @param array $arrRelData                 Relation data
     */
    public function writeRelation7($strValue, $intDataId, $strDataTable, $arrRelData)
    {
        // Delete data from link table
        $strSQL    = 'DELETE FROM `' .$arrRelData['linkTable']. '` WHERE `idMaster` = ' .$intDataId;
        $booResult  = $this->myDBClass->insertData($strSQL);
        if ($booResult == false) {
            $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
        }
        // Decompose data value
        $arrValues = explode(',', $strValue);
        // Process data values
        foreach ($arrValues as $elem) {
            if ($elem != '*') {
                $strWhere = '';
                // Exclude values
                if (0 === strpos($elem, '!')) {
                    $intExclude = 1;
                    $elem = substr($elem, 1);
                } else {
                    $intExclude = 0;
                }
                // Does the entry already exist?
                $strSQL = 'SELECT `id` FROM `' .$arrRelData['tableName1']. '` ' .
                    'WHERE `' .$arrRelData['target1']."`='".$elem."' $strWhere ".
                    'AND `config_id`=' .$this->intDomainId;
                $strId  = $this->myDBClass->getFieldData($strSQL);
                if ($strId != '') {
                    $intSlaveId = (int)$strId;
                } else {
                    $intSlaveId = 0;
                }
                if (($intSlaveId == 0) && ($elem != '*')) {
                    // Insert a temporary value to the target table
                    $strSQL    = 'INSERT INTO `' .$arrRelData['tableName1']. '` ' .
                        'SET `' .$arrRelData['target1']."` = '".$elem."', `host_name`=2, `hostgroup_name`=2, ".
                        "`config_name`='imp_tmp_by_servicedependency', `config_id`=".$this->intDomainId. ', ' .
                        "`active`='0', `last_modified`=NOW()";
                    $booResult = $this->myDBClass->insertData($strSQL);
                    if ($booResult == false) {
                        $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                    }
                    $intSlaveId = $this->myDBClass->intLastId;
                }
                // Insert relations
                $strSQL    = 'INSERT INTO `' .$arrRelData['linkTable']. '` ' .
                    'SET `idMaster` = ' .$intDataId. ', `idSlave` = ' .$intSlaveId.", `strSlave`='".$elem."', ".
                    '`exclude`=' .$intExclude;
                $booResult = $this->myDBClass->insertData($strSQL);
                if ($booResult == false) {
                    $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                }
            }
            // Update field values in master table
            if (substr_count($strValue, '*') != 0) {
                $intRelValue = 2;
            } else {
                $intRelValue = 1;
            }
            $strSQL    = 'UPDATE `' .$strDataTable. '` ' .
                'SET `' .$arrRelData['fieldName']."` = $intRelValue WHERE `id` = ".$intDataId;
            $booResult = $this->myDBClass->insertData($strSQL);
            if ($booResult == false) {
                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
            }
        }
    }

    /**
     * Inserts a relation type 6 (service and servicetemplate parents - 1:service:host)
     * @param string $strValue                  Data value
     * @param int $intDataId                    Data ID
     * @param string $strDataTable              Data table (Master)
     * @param array $arrRelData                 Relation data
     */
    public function writeRelation8($strValue, $intDataId, $strDataTable, $arrRelData)
    {
        // Decompose data value
        $arrValues = explode(',', $strValue);
        // Delete data from link table
        $strSQL    = 'DELETE FROM `' .$arrRelData['linkTable']. '` WHERE `idMaster` = ' .$intDataId;
        $booResult  = $this->myDBClass->insertData($strSQL);
        if ($booResult == false) {
            $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
        }
        // Check the sum of elements
        /** @noinspection ExplodeMissUseInspection */
        if (count($arrValues) % 2 != 0) {
            $this->strErrorMessage .= translate('Error: incorrect number of arguments - cannot import service parent ' .
                    'members'). '::';
        } else {
            // Process data values
            $intCounter  = 1;
            $strHostName = '';
            foreach ($arrValues as $elem) {
                if ($intCounter % 2 == 0) {
                    $strServiceName = $elem;
                    if (($strServiceName != '') && ($strHostName != '')) {
                        $strSQL = 'SELECT tbl_service.id AS id_1, C.id AS id_2, D.id AS id_3, E.id AS id_4 '
                            . 'FROM tbl_service '
                            . 'LEFT JOIN tbl_lnkServiceToHost ON tbl_service.id=tbl_lnkServiceToHost.idMaster '
                            . 'LEFT JOIN tbl_lnkServiceToHostgroup '
                            . 'ON tbl_service.id=tbl_lnkServiceToHostgroup.idMaster '
                            . 'LEFT JOIN tbl_lnkHostgroupToHost AS A ON tbl_lnkServiceToHostgroup.idSlave=A.idMaster '
                            . 'LEFT JOIN tbl_lnkHostToHostgroup AS B ON tbl_lnkServiceToHostgroup.idSlave=B.idSlave '
                            . 'LEFT JOIN tbl_host AS C ON A.idSlave=C.id '
                            . 'LEFT JOIN tbl_host AS D ON B.idMaster=D.id '
                            . 'LEFT JOIN tbl_host AS E ON tbl_lnkServiceToHost.idSlave=E.id '
                            . "WHERE tbl_service.service_description='".$strServiceName."' "
                            . "AND (C.host_name='".$strHostName."' OR D.host_name='".$strHostName."' "
                            . "OR E.host_name='".$strHostName."')";
                        $booResult = $this->myDBClass->hasDataArray($strSQL, $arrDataset, $intCount);
                        if ($booResult && ($intCount == 1)) {
                            $intServiceId = 0;
                            $intHostId    = 0;
                            $intId1       = $arrDataset[0]['id_1'];
                            $intId2       = $arrDataset[0]['id_2'];
                            $intId3       = $arrDataset[0]['id_3'];
                            $intId4       = $arrDataset[0]['id_4'];
                            if (($intId1!= null) && ($intId1 != 0) && ($intServiceId == 0)) {
                                $intServiceId = (int)$intId1;
                            }
                            $intHostSum = 0;
                            if (($intId2 != null) && ($intId2 != 0) && ($intHostId == 0)) {
                                $intHostId   = (int)$intId2;
                                $intHostSum += $intHostId;
                            }
                            if (($intId3 != null) && ($intId3 != 0) && ($intHostId == 0)) {
                                $intHostId   = (int)$intId3;
                                $intHostSum += $intHostId;
                            }
                            if (($intId4 != null) && ($intId4 != 0) && ($intHostId == 0)) {
                                $intHostId   = (int)$intId4;
                                $intHostSum += $intHostId;
                            }
                            if (($intHostId == $intHostSum) && ($intServiceId != 0) && ($intHostId != 0)) {
                                $strSQL = 'INSERT INTO ' .$arrRelData['linkTable']. ' '
                                    . "SET idMaster=$intDataId, idSlave=$intServiceId, idHost=$intHostId";
                                $booResult = $this->myDBClass->insertData($strSQL);
                                if ($booResult == false) {
                                    $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                                }
                                $strSQL    = 'UPDATE `' .$strDataTable. '` ' .
                                    'SET `' .$arrRelData['fieldName']. '` = 1 WHERE `id` = ' .$intDataId;
                                $booResult = $this->myDBClass->insertData($strSQL);
                                if ($booResult == false) {
                                    $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                                }
                            } else {
                                $this->strErrorMessage .= translate('Error: cannot import the service parent member ')
                                    . $strServiceName . '-' . $strHostName . '. '
                                    . translate('This combination is not unique!') . '::';
                            }
                        } else {
                            $this->strErrorMessage .= translate('Error: cannot import the service parent member ')
                                . $strServiceName. '-' .$strHostName. '. '
                                . translate('This combination is not unique or does not exist!').'::';
                        }
                    }
                } else {
                    $strHostName = $elem;
                }
                $intCounter++;
            }
        }
    }
}
