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
// Component : Configuration Class
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
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
// Name: NagConfigClass
//
///////////////////////////////////////////////////////////////////////////////////////////////
namespace functions;

class NagConfigClass
{
    // Define class variables
    /** @var resource $resConnectId */
    public $resConnectId;                       // Connection id for FTP and SSH connections
    public $resSFTP;                            // SFTP ressource id

    public $arrSession           = array();     // Session content
    public $strRelTable          = '';          // Relation table name
    public $strErrorMessage      = '';          // String including error messages
    public $strInfoMessage       = '';          // String including information messages
    public $strPicPath           = 'none';      // Picture path string
    public $intNagVersion        = 0;           // Nagios version id
    public $intDomainId          = 0;           // Configuration domain ID
    // PRIVATE
    private $arrSettings         = array();     // Array includes all global settings
    private $resConnectServer    = '';          // Connection server name for FTP and SSH connections
    private $resConnectType      = 'none';      // Connection type for FTP and SSH connections
    private $arrRelData          = '';          // Relation data
    // Class includes
    /** @var MysqliDbClass */
    public $myDBClass;                          // Database class reference
    /** @var NagDataClass */
    public $myDataClass;                        // Data processing class reference

    /**
     * NagConfigClass constructor.
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
        $this->arrSession = $arrSession;
    }

    /**
     * Get domain configuration parameters.
     * @param string $strConfigItem             Configuration key
     * @param string $strValue                  Configuration value (by reference)
     * @return int                              0 = successful / 1 = error
     */
    public function getDomainData($strConfigItem, &$strValue)
    {
        // Variable definition
        $intReturn = 0;
        // Request domain data from database
        $strSQL   = 'SELECT `' .$strConfigItem. '` FROM `tbl_datadomain` WHERE `id` = ' .$this->intDomainId;
        $strValue = $this->myDBClass->getFieldData($strSQL);
        if ($strValue == '') {
            $intReturn = 1;
        }
        return $intReturn;
    }

    /**
     * Get last modification date of a database table and any configuration files inside a directory.
     * @param string $strTableName              Name of the database table
     * @param string $strConfigName             Name of the configuration file
     * @param int $intDataId                    ID of the dataset for service table
     * @param array $arrTimeData                Array with the timestamps of the files and the DB table (by reference)
     * @param int $intTimeInfo                  Time status value (by reference)
     *                                          0 = all files are newer than the database item
     *                                          1 = some file are older than the database item
     *                                          2 = one file is missing
     *                                          3 = any files are missing
     *                                          4 = no configuration targets defined
     * @return int                              0 = successful / 1 = error
     *                                          Status messages are stored in class variables
     */
    public function lastModifiedDir($strTableName, $strConfigName, $intDataId, &$arrTimeData, &$intTimeInfo)
    {
        // Variable definitions
        $intReturn       = 0;
        // Create file name
        $strFileName  = $strConfigName. '.cfg';
        // Get table times
        $strActive            = 0;
        $arrTimeData          = array();
        $arrTimeData['table'] = 'unknown';
        // Clear status cache
        clearstatcache();
        // Get last change on dataset
        if ($strTableName == 'tbl_host') {
            $strSQL1 = "SELECT DATE_FORMAT(`last_modified`,'%Y-%m-%d %H:%i:%s') FROM `tbl_host` ".
                "WHERE `host_name`='$strConfigName' AND `config_id`=".$this->intDomainId;
            $strSQL2 = "SELECT `active` FROM `tbl_host`  WHERE `host_name`='$strConfigName' ".
                'AND `config_id`=' .$this->intDomainId;
            $arrTimeData['table'] = $this->myDBClass->getFieldData($strSQL1);
            $strActive            = $this->myDBClass->getFieldData($strSQL2);
        } elseif ($strTableName == 'tbl_service') {
            $strSQL1 = "SELECT DATE_FORMAT(`last_modified`,'%Y-%m-%d %H:%i:%s') FROM `tbl_service` ".
                "WHERE `id`='$intDataId' AND `config_id`=".$this->intDomainId;
            $strSQL2 = "SELECT * FROM `$strTableName` WHERE `config_name`='$strConfigName' ".
                'AND `config_id`=' .$this->intDomainId." AND `active`='1'";
            $arrTimeData['table'] = $this->myDBClass->getFieldData($strSQL1);
            $intServiceCount      = $this->myDBClass->countRows($strSQL2);
            if ($intServiceCount != 0) {
                $strActive = 1;
            }
        } else {
            $intReturn = 1;
        }
        // Get config sets
        $arrConfigId      = array();
        $strTarget        = '';
        $strBaseDir       = '';
        $intTimeInfo      = -1;
        $intRetVal2  = $this->getConfigTargets($arrConfigId);
        if ($intRetVal2 == 0) {
            foreach ($arrConfigId as $intConfigId) {
                // Get configuration file data
                $this->getConfigValues($intConfigId, 'target', $strTarget);
                // Get last change on dataset
                if ($strTableName == 'tbl_host') {
                    $this->getConfigValues($intConfigId, 'hostconfig', $strBaseDir);
                } elseif ($strTableName == 'tbl_service') {
                    $this->getConfigValues($intConfigId, 'serviceconfig', $strBaseDir);
                }
                $arrTimeData[$strTarget] = 'unknown';
                $intFileStampTemp        = -1;
                // Get time data
                $intReturn = $this->getFileDate(
                    $intConfigId,
                    $strFileName,
                    $strBaseDir,
                    $intFileStampTemp,
                    $arrTimeData[$strTarget]
                );
                if (($intFileStampTemp == 0) && ($strActive == '1')) {
                    $intTimeInfo = 2;
                }
                if (($strActive == '1') && (strtotime($arrTimeData['table']) > $intFileStampTemp)) {
                    $intTimeInfo = 1;
                }
            }
            $intItems    = \count($arrTimeData) - 1;
            $intUnknown  = 0;
            $intUpToDate = 0;
            foreach ($arrTimeData as $key) {
                if ($key == 'unknown') {
                    $intUnknown++;
                }
                if (strtotime($arrTimeData['table']) < strtotime($key)) {
                    $intUpToDate++;
                }
            }
            if ($intUnknown  == $intItems) {
                $intTimeInfo = 3;
            }
            if ($intUpToDate == $intItems) {
                $intTimeInfo = 0;
            }
        } else {
            $intTimeInfo = 4;
        }
        return $intReturn;
    }

    /**
     * Get configuration target IDs
     * @param array $arrConfigId                Configuration target IDs (by reference)
     * @return int                              0 = successful / 1 = error
     */
    public function getConfigTargets(&$arrConfigId)
    {
        // Variable definition
        $arrData      = array();
        $arrConfigId  = array();
        $intDataCount = 0;
        $intReturn    = 1;
        // Request target ID
        $strSQL    = 'SELECT `targets` FROM `tbl_datadomain` WHERE `id`=' .$this->intDomainId;
        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if ($booReturn && ($intDataCount != 0)) {
            foreach ($arrData as $elem) {
                $arrConfigId[] = $elem['targets'];
            }
            $intReturn = 0;
        }
        return $intReturn;
    }

    /**
     * Get configuration domain values
     * @param int $intConfigId                  Configuration ID
     * @param string $strConfigKey              Configuration key
     * @param string $strValue                  Configuration value (by reference)
     * @return int                              0 = successful / 1 = error
     */
    public function getConfigValues($intConfigId, $strConfigKey, &$strValue)
    {
        // Define variables
        $intReturn = 1;
        // Read database
        $strSQL    = 'SELECT `' .$strConfigKey. '` FROM `tbl_configtarget` WHERE `id`=' .$intConfigId;
        $strValue  = $this->myDBClass->getFieldData($strSQL);
        if ($strValue != '') {
            $intReturn = 0;
        }
        return $intReturn;
    }

    /**
     * Get last modification date of a configuration file.
     * @param int $intConfigId                  Configuration ID
     * @param string $strFile                   Configuration file name
     * @param string $strBaseDir                Base directory with configuration file
     * @param int|bool $intFileStamp            File timestamp (by reference)
     * @param string $strTimeData               Human readable string of file time stamp (by reference)
     * @return int                              0 = successful / 1 = error
     */
    public function getFileDate($intConfigId, $strFile, $strBaseDir, &$intFileStamp, &$strTimeData)
    {
        $strMethod  = 1;
        $intReturn  = 0;
        // Get configuration file data
        $this->getConfigValues($intConfigId, 'method', $strMethod);
        $strTimeData  = 'unknown';
        $intFileStamp = -1;
        // Lokal file system
        if (($strMethod == 1) && file_exists($strBaseDir. '/' .$strFile)) {
            $intFileStamp = filemtime($strBaseDir. '/' .$strFile);
            $strTimeData = date('Y-m-d H:i:s', $intFileStamp);
        } elseif ($strMethod == 2) { // FTP file system
            // Check connection
            $intReturn = $this->getFTPConnection($intConfigId);
            if ($intReturn == 0) {
                $intFileStamp = ftp_mdtm($this->resConnectId, $strBaseDir . '/' . $strFile);
                if ($intFileStamp != -1) {
                    $strTimeData = date('Y-m-d H:i:s', $intFileStamp);
                }
            }
        } elseif ($strMethod == 3) { // SSH file system
            // Check connection
            $intReturn = $this->getSSHConnection($intConfigId);
            // Check file date
            $strFilePath = str_replace('//', '/', $strBaseDir.'/'.$strFile);
            $strCommand  = 'ls '.$strFilePath;
            $arrResult   =  array();
            if (($intReturn == 0) && ($this->sendSSHCommand($strCommand, $arrResult) == 0) &&
                isset($arrResult[0]) && ($arrResult[0] == $strFilePath)) {
                $arrInfo      = ssh2_sftp_stat($this->resSFTP, $strFilePath);
                $intFileStamp = $arrInfo['mtime'];
                if ($intFileStamp != -1) {
                    $strTimeData = date('Y-m-d H:i:s', $intFileStamp);
                }
            }
        }
        return $intReturn;
    }

    /**
     * Open an FTP connection
     * @param int $intConfigID                  Configuration ID
     * @return int                              0 = successful / 1 = error
     *                                          Status messages are stored in class variables
     */
    public function getFTPConnection($intConfigID)
    {
        // Define variables
        $intReturn = 0;
        $arrError  = array();
        // Already connected?
        if (empty($this->resConnectId) || !\is_resource($this->resConnectId) || ($this->resConnectType != 'FTP')) {
            // Define variables
            $booLogin = false;
            $this->getConfigValues($intConfigID, 'server', $strServer);
            $this->getConfigValues($intConfigID, 'ftp_secure', $intFtpSecure);
            // Set up basic connection
            $this->resConnectServer = $strServer;
            $this->resConnectType = 'FTP';
            // Secure FTP?
            if ($intFtpSecure == 1) {
                $this->resConnectId = ftp_ssl_connect($strServer);
            } else {
                $this->resConnectId = ftp_connect($strServer);
            }
            // Login with username and password
            if ($this->resConnectId) {
                $this->getConfigValues($intConfigID, 'user', $strUser);
                $this->getConfigValues($intConfigID, 'password', $strPasswd);
                $intErrorReporting = error_reporting();
                error_reporting('0');
                $booLogin = ftp_login($this->resConnectId, $strUser, $strPasswd);
                $arrError = error_get_last();
                error_reporting($intErrorReporting);
                if ($booLogin == false) {
                    ftp_close($this->resConnectId);
                    $this->resConnectServer = '';
                    $this->resConnectType   = 'none';
                    $this->resConnectId     = null;
                    $intReturn = 1;
                } else {
                    // Change to PASV mode
                    ftp_pasv($this->resConnectId, true);
                }
            }
            // Check connection
            if ((!$this->resConnectId) || (!$booLogin)) {
                $this->myDataClass->writeLog(translate('Connection to remote system failed (FTP connection):') .
                    ' ' . $strServer);
                $this->processClassMessage(translate('Connection to remote system failed (FTP connection):') .
                    ' <b>' . $strServer . '</b>::', $this->strErrorMessage);
                if ($arrError !== null && ($arrError['message'] != '')) {
                    $this->processClassMessage($arrError['message'] . '::', $this->strErrorMessage);
                }
            }
        }
        return $intReturn;
    }

    /**
     * Open an SSH connection
     * @param int $intConfigID                  Configuration ID
     * @return int                              0 = successful / 1 = error
     *                                          Status messages are stored in class variables
     */
    public function getSSHConnection($intConfigID)
    {
        // Define variables
        $intReturn       = 0;
        $strPasswordNote = '';
        // Already connected?
        if (empty($this->resConnectId) || !\is_resource($this->resConnectId) || ($this->resConnectType != 'SSH')) {
            // SSH Possible
            if (!\function_exists('ssh2_connect')) {
                $this->processClassMessage(translate('SSH module not loaded!'). '::', $this->strErrorMessage);
                return 1;
            }
            // Define variables
            $booLogin = false;
            $this->getConfigValues($intConfigID, 'server', $strServer);
            $this->getConfigValues($intConfigID, 'port', $intPort);
            $this->resConnectServer = $strServer;
            $this->resConnectType   = 'SSH';
            $intPort = (int)$intPort;
            if ($intPort == 0) {
                $intPort = 22;
            }
            $intErrorReporting  = error_reporting();
            error_reporting(0);
            $this->resConnectId = ssh2_connect($strServer, $intPort);
            $arrError = error_get_last();
            error_reporting($intErrorReporting);
            // Check connection
            if ($this->resConnectId) {
                // Login with username and password
                $this->getConfigValues($intConfigID, 'user', $strUser);
                $this->getConfigValues($intConfigID, 'password', $strPasswd);
                $this->getConfigValues($intConfigID, 'ssh_key_path', $strSSHKeyPath);
                if ($strSSHKeyPath != '') {
                    $strPublicKey = str_replace('//', '/', $strSSHKeyPath.'/id_rsa.pub');
                    $strPrivatKey = str_replace('//', '/', $strSSHKeyPath.'/id_rsa');
                    // Check if ssh key file are readable
                    if (!file_exists($strPublicKey) || !is_readable($strPublicKey)) {
                        $this->myDataClass->writeLog(translate('SSH public key does not exist or is not readable')
                            . ' ' . $strSSHKeyPath.$strPublicKey);
                        $this->processClassMessage(translate('SSH public key does not exist or is not readable')
                            . ' <b>' . $strSSHKeyPath.$strPublicKey. '</b>::', $this->strErrorMessage);
                        $intReturn = 1;
                    }
                    if (!file_exists($strPrivatKey) || !is_readable($strPrivatKey)) {
                        $this->myDataClass->writeLog(translate('SSH private key does not exist or is not readable')
                            . ' ' . $strPrivatKey);
                        $this->processClassMessage(translate('SSH private key does not exist or is not readable'). ' ' .
                            $strPrivatKey. '::', $this->strErrorMessage);
                        $intReturn = 1;
                    }
                    $intErrorReporting  = error_reporting();
                    error_reporting(0);
                    if ($strPasswd == '') {
                        $booLogin = ssh2_auth_pubkey_file(
                            $this->resConnectId,
                            $strUser,
                            $strSSHKeyPath. '/id_rsa.pub',
                            $strSSHKeyPath. '/id_rsa'
                        );
                    } else {
                        $booLogin = ssh2_auth_pubkey_file(
                            $this->resConnectId,
                            $strUser,
                            $strSSHKeyPath. '/id_rsa.pub',
                            $strSSHKeyPath. '/id_rsa',
                            $strPasswd
                        );
                    }
                    $arrError = error_get_last();
                    error_reporting($intErrorReporting);
                } else {
                    $intErrorReporting  = error_reporting();
                    error_reporting(0);
                    $booLogin        = ssh2_auth_password($this->resConnectId, $strUser, $strPasswd);
                    $arrError        = error_get_last();
                    $strPasswordNote = 'If you are using ssh2 with user/password - you have to enable ' .
                        'PasswordAuthentication in your sshd_config';
                    error_reporting($intErrorReporting);
                }
            } else {
                $this->myDataClass->writeLog(translate('Connection to remote system failed (SSH2 connection):').
                    ' ' .$strServer. ' / ' . translate('port') . ' : ' .$intPort);
                $this->processClassMessage(translate('Connection to remote system failed (SSH2 connection):').
                    ' <b>' .$strServer. ' / ' . translate('port') . ' : ' .$intPort. '</b>::', $this->strErrorMessage);
                if ($arrError['message'] != '') {
                    $this->processClassMessage($arrError['message']. '::', $this->strErrorMessage);
                }
                $intReturn = 1;
            }
            // Check connection
            if ((!$this->resConnectId) || (!$booLogin)) {
                $this->myDataClass->writeLog(translate('Connection to remote system failed (SSH2 connection):').
                    ' ' .$strServer. ' / ' . translate('port') . ' : ' .$intPort);
                $this->processClassMessage(translate('Connection to remote system failed (SSH2 connection):')
                    . ' ' .$strServer. ' / ' . translate('port') . ' : ' .$intPort. '::', $this->strErrorMessage);
                if ($arrError['message'] != '') {
                    $this->processClassMessage($arrError['message']. '::', $this->strErrorMessage);
                }
                if ($strPasswordNote !== null) {
                    $this->processClassMessage($strPasswordNote. '::', $this->strErrorMessage);
                }
                $this->resConnectServer = '';
                $this->resConnectType   = 'none';
                $this->resConnectId     = null;
                $intReturn = 1;
            } else {
                // Etablish an SFTP connection ressource
                $this->resSFTP = ssh2_sftp($this->resConnectId);
            }
        }
        return $intReturn;
    }

    /**
     * Sends a command via SSH and stores the result in an array
     * @param string $strCommand                Command string
     * @param array $arrResult                  Output as array (by reference)
     * @param int $intLines                     Maximal length of output to read
     * @return int                              0 = successful / 1 = error
     */
    public function sendSSHCommand($strCommand, &$arrResult, $intLines = 100)
    {
        // Define variables
        $intCount1 = 0; // empty lines
        $intCount2 = 0; // data lines
        $booBreak = false;
        $this->getConfigTargets($arrConfigSet);
        // Check connection
        $intReturn = $this->getSSHConnection($arrConfigSet[0]);
        if (\is_resource($this->resConnectId)) {
            // Send command
            $resStream = ssh2_exec($this->resConnectId, $strCommand.'; echo __END__');
            if ($resStream) {
                // read result
                stream_set_blocking($resStream, true);
                stream_set_timeout($resStream, 2);
                do {
                    $strLine = stream_get_line($resStream, 1024, "\n");
                    if ($strLine == '') {
                        $intCount1++;
                    } elseif (substr_count($strLine, '__END__') != 1) {
                        $arrResult[] = $strLine;
                        $intReturn   = 0;
                    } elseif (substr_count($strLine, '__END__') == 1) {
                        $booBreak = true;
                    }
                    $intCount2++;
                    $arrStatus = stream_get_meta_data($resStream);
                } while ($resStream && !feof($resStream) && ($intCount1 <= 10) && ($intCount2 <= $intLines) &&
                ($arrStatus['timed_out'] != true) && $booBreak == false);
                fclose($resStream);
                // Close SSH connection because of timing problems
                unset($this->resConnectId);
                //sleep(1);
            }
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
        if (($strOldMessage != '') && ($strNewMessage != '') && (substr_count($strOldMessage, $strNewMessage) == 0)) {
            $strOldMessage .= $strNewMessage;
        } elseif ($strOldMessage == '') {
            $strOldMessage .= $strNewMessage;
        }
    }

    /**
     * Get configuration target IDs
     * @param array $arrConfigId                Configuration target IDs (by reference)
     * @return int                              0 = successful / 1 = error
     */
    public function getConfigSets(&$arrConfigId)
    {
        // Variable definition
        $arrData      = array();
        $arrConfigId  = array();
        $intDataCount = 0;
        $intReturn    = 1;
        // Request target ID
        $strSQL    = 'SELECT `targets` FROM `tbl_datadomain` WHERE `id`=' .$this->intDomainId;
        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if ($booReturn && ($intDataCount != 0)) {
            foreach ($arrData as $elem) {
                $arrConfigId[] = $elem['targets'];
            }
            $intReturn = 0;
        }
        return $intReturn;
    }

    /**
     * Moves an existing configuration file to the backup directory and removes then the original file
     * @param string $strType                   Type of the configuration file
     * @param string $strName                   Name of the configuration file
     * @param int $intConfigID                  Configuration target ID
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function moveFile($strType, $strName, $intConfigID)
    {
        // Variable definitions
        $strConfigDir = '';
        $strBackupDir = '';
        $intReturn    = 0;
        // Get directories
        switch ($strType) {
            case 'host':
                $this->getConfigData($intConfigID, 'hostconfig', $strConfigDir);
                $this->getConfigData($intConfigID, 'hostbackup', $strBackupDir);
                break;
            case 'service':
                $this->getConfigData($intConfigID, 'serviceconfig', $strConfigDir);
                $this->getConfigData($intConfigID, 'servicebackup', $strBackupDir);
                break;
            case 'basic':
                $this->getConfigData($intConfigID, 'basedir', $strConfigDir);
                $this->getConfigData($intConfigID, 'backupdir', $strBackupDir);
                break;
            case 'nagiosbasic':
                $this->getConfigData($intConfigID, 'nagiosbasedir', $strConfigDir);
                $this->getConfigData($intConfigID, 'backupdir', $strBackupDir);
                break;
            default:
                $intReturn = 1;
        }
        if ($intReturn == 0) {
            // Variable definition
            $intMethod          = 1;
            $strDate            = date('YmdHis');
            $strSourceFile      = $strConfigDir. '/' .$strName;
            $strDestinationFile = $strBackupDir. '/' .$strName. '_old_' .$strDate;
            $booRetVal          = false;
            // Get connection method
            $this->getConfigData($intConfigID, 'method', $intMethod);
            // Local file system
            if ($intMethod == 1) {
                // Save configuration file
                if (file_exists($strSourceFile)) {
                    if (is_writable($strBackupDir) && is_writable($strConfigDir)) {
                        copy($strSourceFile, $strDestinationFile);
                        unlink($strSourceFile);
                    } else {
                        $this->processClassMessage(translate('Cannot backup the old file because the permissions are '
                                .'wrong - destination file: ').$strDestinationFile. '::', $this->strErrorMessage);
                        $intReturn = 1;
                    }
                } else {
                    $this->processClassMessage(translate('Cannot backup the old file because the source file is '
                            . 'missing - source file: ') . $strSourceFile . '::', $this->strErrorMessage);
                    $intReturn = 1;
                }
            } elseif ($intMethod == 2) { // Remote file (FTP)
                // Check connection
                $intReturn = $this->getFTPConnection($intConfigID);
                if ($intReturn == 0) {
                    $strSourceFile      = str_replace('//', '/', $strSourceFile);
                    $strDestinationFile = str_replace('//', '/', $strDestinationFile);
                    // Save configuration file
                    $intFileStamp = ftp_mdtm($this->resConnectId, $strSourceFile);
                    if ($intFileStamp > -1) {
                        $intErrorReporting = error_reporting();
                        error_reporting(0);
                        $booRetVal = ftp_rename($this->resConnectId, $strSourceFile, $strDestinationFile);
                        error_reporting($intErrorReporting);
                    } else {
                        $this->processClassMessage(translate('Cannot backup the old file because the source file is '
                                .'missing (remote FTP) - source file: '). $strSourceFile. '::', $this->strErrorMessage);
                        $intReturn = 1;
                    }
                }
                if (($booRetVal == false) && ($intReturn == 0)) {
                    $this->processClassMessage(translate('Cannot backup the old file because the permissions are '
                            .'wrong (remote FTP) - destination file: ').$strDestinationFile. '::', $this->strErrorMessage);
                    $intReturn = 1;
                }
            } elseif ($intMethod == 3) { // Remote file (SFTP)
                // Check connection
                $intReturn = $this->getSSHConnection($intConfigID);
                // Save configuration file
                $arrResult          = array();
                $strSourceFile      = str_replace('//', '/', $strSourceFile);
                $strDestinationFile = str_replace('//', '/', $strDestinationFile);
                $strCommand         = 'ls '.$strSourceFile;
                if (($intReturn == 0) && ($this->sendSSHCommand($strCommand, $arrResult) == 0)) {
                    if (isset($arrResult[0]) && $arrResult[0] == $strSourceFile) {
                        $arrInfo      = ssh2_sftp_stat($this->resSFTP, $strSourceFile);
                        if ($arrInfo['mtime'] > -1) {
                            $booRetVal = ssh2_sftp_rename($this->resSFTP, $strSourceFile, $strDestinationFile);
                        }
                    } else {
                        $this->processClassMessage(translate('Cannot backup the old file because the source file is '
                                .'missing (remote SFTP) - source file: '). $strSourceFile. '::', $this->strErrorMessage);
                        $intReturn = 1;
                    }
                }
                if (($booRetVal == false) && ($intReturn == 0)) {
                    $this->processClassMessage(translate('Cannot backup the old file because the permissions are '
                            .'wrong (remote SFTP) - destination file: ').$strDestinationFile. '::', $this->strErrorMessage);
                    $intReturn = 1;
                }
            }
        }
        return $intReturn;
    }

    /**
     * Remove a file
     * @param string $strFileName               Filename including path to remove
     * @param int $intConfigID                  Configuration target ID
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function removeFile($strFileName, $intConfigID)
    {
        // Variable definitions
        $intMethod = 1;
        $intReturn = 0;
        $booRetVal = false;
        // Get connection method
        $this->getConfigData($intConfigID, 'method', $intMethod);
        // Local file system
        if ($intMethod == 1) {
            // Save configuration file
            if (file_exists($strFileName)) {
                if (is_writable($strFileName)) {
                    unlink($strFileName);
                } else {
                    $this->processClassMessage(translate('Cannot delete the file (wrong permissions)!').'::'.
                        $strFileName. '::', $this->strErrorMessage);
                    $intReturn = 1;
                }
            } else {
                $this->processClassMessage(translate('Cannot delete the file (file does not exist)!').'::'.
                    $strFileName. '::', $this->strErrorMessage);
                $intReturn = 1;
            }
        } elseif ($intMethod == 2) { // Remote file (FTP)
            // Check connection
            $intReturn = $this->getFTPConnection($intConfigID);
            if ($intReturn == 0) {
                // Save configuration file
                $intFileStamp = ftp_mdtm($this->resConnectId, $strFileName);
                if ($intFileStamp > -1) {
                    $intErrorReporting = error_reporting();
                    error_reporting(0);
                    $booRetVal = ftp_delete($this->resConnectId, $strFileName);
                    error_reporting($intErrorReporting);
                } else {
                    $this->processClassMessage(translate('Cannot delete file because it does not exists (remote '
                            . 'FTP)!'). '::', $this->strErrorMessage);
                    $intReturn = 1;
                }
            }
            if ($booRetVal == false) {
                $this->processClassMessage(translate('Cannot delete file because the permissions are incorrect '
                        . '(remote FTP)!'). '::', $this->strErrorMessage);
                $intReturn = 1;
            }
        } elseif ($intMethod == 3) { // Remote file (SFTP)
            // Check connection
            $intReturn = $this->getSSHConnection($intConfigID);
            // Save configuration file
            if (($intReturn == 0) && ($this->sendSSHCommand('ls '.$strFileName, $arrResult) == 0)) {
                if (isset($arrResult[0])) {
                    $booRetVal = ssh2_sftp_unlink($this->resSFTP, $strFileName);
                } else {
                    $this->processClassMessage(translate('Cannot delete file because it does not exists (remote '
                            . 'SSH/SFTP)!'). '::', $this->strErrorMessage);
                    $intReturn = 1;
                }
            }
            if (($intReturn == 0) && ($booRetVal == false)) {
                $this->processClassMessage(translate('Cannot delete file because the permissions are incorrect '
                        . '(remote SSH/SFTP)!'). '::', $this->strErrorMessage);
                $intReturn = 1;
            }
        }
        return $intReturn;
    }

    /**
     * Get configuration domain parameters
     * @param int $intConfigId                  Configuration ID
     * @param string $strConfigItem             Configuration key
     * @param string $strValue                  Configuration value (by reference)
     * @return int                              0 = successful / 1 = error
     */
    public function getConfigData($intConfigId, $strConfigItem, &$strValue)
    {
        $intReturn = 1;
        $strSQL   = 'SELECT `' .$strConfigItem. '` FROM `tbl_configtarget` WHERE `id` = ' .$intConfigId;
        $strValue = $this->myDBClass->getFieldData($strSQL);
        if ($strValue != '') {
            $intReturn = 0;
        }
        return $intReturn;
    }

    /**
     * Check a directory for write access
     * @param string $strPath                   Physical path
     * @return int                              0 = successful / 1 = error
     */
    public function isDirWriteable($strPath)
    {
        // Define variables
        $intReturnFile = 1;
        $intReturnDir  = 1;
        $intReturn     = 1;
        // Is input path a file?
        if (file_exists($strPath) && is_file($strPath)) {
            $resFile = fopen($strPath, 'ab');
            if ($resFile) {
                $intReturnFile = 0;
            }
        } else {
            $intReturnFile = 0;
        }
        if (is_file($strPath)) {
            $strDirectory = \dirname($strPath);
        } else {
            $strDirectory = $strPath;
        }
        $strFile = $strDirectory.'/'.uniqid(mt_rand(), true).'.tmp';
        // Check writing in directory directly
        if (is_dir($strDirectory) && is_writable($strDirectory)) {
            $resFile = fopen($strFile, 'wb');
            if ($resFile) {
                $intReturnDir = 0;
                unlink($strFile);
            }
        } else {
            $intReturnDir = 0;
        }
        if (($intReturnDir == 0) && ($intReturnFile == 0)) {
            $intReturn = 0;
        }
        return $intReturn;
    }

    /**
     * Copy a remote file
     * @param string $strFileRemote             Remote file name
     * @param int $intConfigID                  Configuration target id
     * @param string $strFileLocal              Local file name
     * @param int $intDirection                 0 = from remote to local / 1 = from local to remote
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function remoteFileCopy($strFileRemote, $intConfigID, $strFileLocal, $intDirection = 0)
    {
        // Variable definitions
        $intMethod = 3;
        $intReturn = 0;
        $arrTemp   = array();
        // Get method
        $this->getConfigData($intConfigID, 'method', $intMethod);
        if ($intMethod == 2) {
            // Check connection
            $intReturn = $this->getFTPConnection($intConfigID);
            if (($intReturn == 0) && ($intDirection == 0)) {
                $intErrorReporting = error_reporting();
                error_reporting(0);
                if (!ftp_get($this->resConnectId, $strFileLocal, $strFileRemote, FTP_ASCII)) {
                    $this->processClassMessage(translate('Cannot get the remote file (it does not exist or is not '
                            . 'readable) - remote file: '). $strFileRemote. '::', $this->strErrorMessage);
                    $intReturn = 1;
                }
                error_reporting($intErrorReporting);
            } elseif (($intReturn == 0) && ($intDirection == 1)) {
                $intErrorReporting = error_reporting();
                error_reporting(0);
                if (!ftp_put($this->resConnectId, $strFileRemote, $strFileLocal, FTP_ASCII)) {
                    $this->processClassMessage(translate('Cannot write the remote file (remote file is not writeable)'
                            . '- remote file: ').$strFileRemote. '::', $this->strErrorMessage);
                    $intReturn = 1;
                }
                error_reporting($intErrorReporting);
            }
            ftp_close($this->resConnectId);
        } elseif ($intMethod == 3) { // Remote file (SFTP)
            $intReturn = $this->getSSHConnection($intConfigID);
            if (($intReturn == 0) && ($intDirection == 0)) {
                // Copy file
                $intErrorReporting = error_reporting();
                error_reporting(0);
                if (!ssh2_scp_recv($this->resConnectId, $strFileRemote, $strFileLocal)) {
                    if ($this->sendSSHCommand('ls ' . $strFileRemote, $arrTemp) != 0) {
                        $this->processClassMessage(translate('Cannot get the remote file (it does not exist or is not '
                                . 'readable) - remote file: ') .$strFileRemote. '::', $this->strErrorMessage);
                    } else {
                        $this->processClassMessage(translate('Remote file is not readable - remote file: ')
                            . $strFileRemote. '::', $this->strErrorMessage);
                    }
                    $intReturn = 1;
                }
                error_reporting($intErrorReporting);
            } elseif (($intReturn == 0) && ($intDirection == 1)) {
                if (file_exists($strFileLocal) && is_readable($strFileLocal)) {
                    $intErrorReporting = error_reporting();
                    error_reporting(0);
                    if (!ssh2_scp_send($this->resConnectId, $strFileLocal, $strFileRemote, 0644)) {
                        $this->processClassMessage(translate('Cannot write a remote file (remote file is not writeable)'
                                .' - remote file: '). $strFileRemote . '::', $this->strErrorMessage);
                        $intReturn = 1;
                    }
                    error_reporting($intErrorReporting);
                } else {
                    $this->processClassMessage(translate('Cannot copy a local file to remote because the local file '.
                            'does not exist or is not readable - local file: ').
                        $strFileLocal . '::', $this->strErrorMessage);
                    $intReturn = 1;
                }
            }
        }
        return $intReturn;
    }

    /**
     * Add files of a given directory to an array
     * @param string $strSourceDir              Source directory
     * @param string $strIncPattern             Include file pattern
     * @param string $strExcPattern             Exclude file pattern
     * @param array $arrOutput                  Output array (by reference)
     * @param string $strErrorMessage           Error messages (by reference)
     */
    public function storeDirToArray($strSourceDir, $strIncPattern, $strExcPattern, &$arrOutput, &$strErrorMessage)
    {
        // Define variables
        $arrDir = array();
        while (substr($strSourceDir, -1) == '/' or substr($strSourceDir, -1) == "\\") {
            $strSourceDir = substr($strSourceDir, 0, -1);
        }
        $resHandle = opendir($strSourceDir);
        if ($resHandle === false) {
            if ($this->intDomainId != 0) {
                $strErrorMessage .= translate('Could not open directory'). ': ' .$strSourceDir;
            }
        } else {
            $booBreak = true;
            while ($booBreak) {
                if (!$arrDir[] = readdir($resHandle)) {
                    $booBreak = false;
                }
            }
            closedir($resHandle);
            sort($arrDir);
            /** @var string $file */
            foreach ($arrDir as $file) {
                /** @noinspection StrlenInEmptyStringCheckContextInspection */
                if (!preg_match("/^\.{1,2}/", $file) && \strlen($file)) {
                    if (is_dir($strSourceDir. '/' .$file)) {
                        $this->storeDirToArray(
                            $strSourceDir. '/' .$file,
                            $strIncPattern,
                            $strExcPattern,
                            $arrOutput,
                            $strErrorMessage
                        );
                    } else {
                        if (preg_match('/' .$strIncPattern. '/', $file) && (($strExcPattern == '') ||
                                !preg_match('/' .$strExcPattern. '/', $file))) {
                            if (0 === stripos(PHP_OS, 'WIN')) {
                                $strSourceDir=str_replace('/', "\\", $strSourceDir);
                                $arrOutput [] = $strSourceDir."\\".$file;
                            } else {
                                $arrOutput [] = $strSourceDir. '/' .$file;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Determines the dates of the last data table change and the last modification to the configuration files
     * @param string $strTableName              Name of the data table
     * @param array $arrTimeData                Array with time data of table and all config files
     * @param string $strCheckConfig            Information string (text message)
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function lastModifiedFile($strTableName, &$arrTimeData, &$strCheckConfig)
    {
        // Variable definitions
        $intEnableCommon = 0;
        $arrDataset      = array();
        $strFileName     = '';
        $strCheckConfig  = '';
        $intReturn       = 0;
        // Get configuration filename based on table name
        $arrConfigData = $this->getConfData();
        if (isset($arrConfigData[$strTableName])) {
            $strFileName   = $arrConfigData[$strTableName]['filename'];
        } else {
            $intReturn = 1;
        }
        // Get table times
        $arrTimeData          = array();
        $arrTimeData['table'] = 'unknown';
        // Clear status cache
        clearstatcache();
        $intRetVal1 = $this->getDomainData('enable_common', $intEnableCommon);
        // Get last change of date table
        if ($intRetVal1 == 0) {
            $strSQLAdd = '';
            if ($intEnableCommon == 1) {
                $strSQLAdd = 'OR `domainId`=0';
            }
            $strSQL = 'SELECT `updateTime` FROM `tbl_tablestatus` '
                . 'WHERE (`domainId`=' .$this->intDomainId." $strSQLAdd) AND `tableName`='".$strTableName."' "
                . 'ORDER BY `updateTime` DESC LIMIT 1';
            $booReturn = $this->myDBClass->hasSingleDataset($strSQL, $arrDataset);
            if ($booReturn && isset($arrDataset['updateTime'])) {
                $arrTimeData['table'] = $arrDataset['updateTime'];
            } else {
                $strSQL = 'SELECT `last_modified` FROM `' .$strTableName. '` '
                    . 'WHERE `config_id`=' .$this->intDomainId. ' ORDER BY `last_modified` DESC LIMIT 1';
                $booReturn = $this->myDBClass->hasSingleDataset($strSQL, $arrDataset);
                if (($booReturn == true) && isset($arrDataset['last_modified'])) {
                    $arrTimeData['table'] = $arrDataset['last_modified'];
                }
            }
        }
        // Get config sets
        $arrConfigId      = array();
        $strTarget        = '';
        $strBaseDir       = '';
        $intFileStampTemp = 0;
        $intRetVal2  = $this->getConfigSets($arrConfigId);
        if ($intRetVal2 == 0) {
            foreach ($arrConfigId as $intConfigId) {
                // Get configuration file data
                $this->getConfigData($intConfigId, 'target', $strTarget);
                $this->getConfigData($intConfigId, 'basedir', $strBaseDir);
                // Get time data
                $intReturn = $this->getFileDate(
                    $intConfigId,
                    $strFileName,
                    $strBaseDir,
                    $intFileStampTemp,
                    $arrTimeData[$strTarget]
                );
                if ($intFileStampTemp != 0 && strtotime($arrTimeData['table']) > $intFileStampTemp) {
                    $strCheckConfig = translate('Warning: configuration file is out of date!');
                }
                if ($arrTimeData[$strTarget] == 'unknown') {
                    $strCheckConfig = translate('Warning: configuration file is out of date!');
                }
            }
        } else {
            $strCheckConfig = translate('Warning: no configuration target defined!');
        }
        return $intReturn;
    }

    /**
     * Writes a configuration file including all datasets of a configuration table or returns the output as a text
     * file for download. (Public master function)
     * @param string $strTableName              Table name
     * @param int $intMode                      0 = Write file to filesystem
     *                                          1 = Return Textfile for download test
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function createConfig($strTableName, $intMode = 0)
    {
        // Define Variables
        $intReturn     = 0;
        // Do not create configs in common domain
        if ($this->intDomainId == 0) {
            $this->processClassMessage(translate('It is not possible to write config files directly from the common '
                    . 'domain!'). '::', $this->strErrorMessage);
            $intReturn = 1;
        }
        if ($intReturn == 0) {
            // Get configuration targets
            $this->getConfigSets($arrConfigID);
            if (($arrConfigID != 1) && \is_array($arrConfigID)) {
                foreach ($arrConfigID as $intConfigID) {
                    $intReturn = $this->writeConfTemplate($intConfigID, $strTableName, $intMode);
                }
            } else {
                $this->processClassMessage(translate('Warning: no configuration target defined!').
                    '::', $this->strErrorMessage);
                $intReturn = 1;
            }
        }
        return $intReturn;
    }

    /**
     * Writes a configuration file including one single datasets of a configuration table or returns the output as
     * a text file for download.
     * @param string $strTableName              Table name
     * @param int $intDbId                      Data ID
     * @param int $intMode                      0 = Write file to filesystem
     *                                          1 = Return Textfile for download test
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function createConfigSingle($strTableName, $intDbId = 0, $intMode = 0)
    {
        // Define Variables
        $arrData            = array();
        $intDataCount       = 0;
        $setEnableCommon    = 0;
        $intReturn          = 0;
        $strDomainWhere     = ' (`config_id`=' .$this->intDomainId. ') ';
        // Read some settings and informations
        $this->getDomainData('enable_common', $setEnableCommon);
        // Variable rewritting
        if ($setEnableCommon != 0) {
            $strDomainWhere = str_replace(')', ' OR `config_id`=0)', $strDomainWhere);
        }
        // Do not create configs in common domain
        if ($this->intDomainId == 0) {
            $this->processClassMessage(translate('It is not possible to write config files directly from the common '
                    . 'domain!'). '::', $this->strErrorMessage);
            $intReturn = 1;
        }
        if ($intReturn == 0) {
            if ($intDbId == 0) {
                $strSQL = 'SELECT * FROM `' .$strTableName."` WHERE $strDomainWhere AND `active`='1' ORDER BY `id`";
            } else {
                $strSQL = 'SELECT * FROM `' .$strTableName."` WHERE $strDomainWhere AND `active`='1' AND `id`=$intDbId";
            }
            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
            if (($booReturn != false) && ($intDataCount != 0)) {
                /** @noinspection ForeachInvariantsInspection */
                for ($i = 0; $i < $intDataCount; $i++) {
                    // Process form POST variable
                    $strChbName = 'chbId_' .$arrData[$i]['id'];
                    // Check if this POST variable exists or the data ID parameter matches
                    if ((($intDbId != 0) && ($intDbId == $arrData[$i]['id'])) ||
                        (filter_input(INPUT_POST, $strChbName) !== null)) {
                        // Get configuration targets
                        $this->getConfigSets($arrConfigID);
                        if (($arrConfigID != 1) && \is_array($arrConfigID)) {
                            foreach ($arrConfigID as $intConfigID) {
                                $intReturn = $this->writeConfTemplate(
                                    $intConfigID,
                                    $strTableName,
                                    $intMode,
                                    $arrData,
                                    $i
                                );
                            }
                        } else {
                            $this->processClassMessage(translate('Warning: no configuration target defined!').
                                '::', $this->strErrorMessage);
                            $intReturn = 1;
                        }
                    }
                }
            } else {
                $this->myDataClass->writeLog(translate('Writing of the configuration failed - no dataset or not '
                    . 'activated dataset found'));
                $this->processClassMessage(translate('Writing of the configuration failed - no dataset or not '
                        . 'activated dataset found'). '::', $this->strErrorMessage);
                $intReturn = 1;
            }
        }
        return $intReturn;
    }


    // PRIVATE functions

    /**
     * Determines the configuration data for each database table
     * @return array                            filename (configuration file name)
     *                                          order_field (database order field)
     */
    public function getConfData()
    {
        $arrConfData['tbl_timeperiod']        = array('filename' => 'timeperiods.cfg',
            'order_field' => 'timeperiod_name');
        $arrConfData['tbl_command']           = array('filename' => 'commands.cfg',
            'order_field' => 'command_name');
        $arrConfData['tbl_contact']           = array('filename' => 'contacts.cfg',
            'order_field' => 'contact_name');
        $arrConfData['tbl_contacttemplate']   = array('filename' => 'contacttemplates.cfg',
            'order_field' => 'template_name');
        $arrConfData['tbl_contactgroup']      = array('filename' => 'contactgroups.cfg',
            'order_field' => 'contactgroup_name');
        $arrConfData['tbl_hosttemplate']      = array('filename' => 'hosttemplates.cfg',
            'order_field' => 'template_name');
        $arrConfData['tbl_servicetemplate']   = array('filename' => 'servicetemplates.cfg',
            'order_field' => 'template_name');
        $arrConfData['tbl_hostgroup']         = array('filename' => 'hostgroups.cfg',
            'order_field' => 'hostgroup_name');
        $arrConfData['tbl_servicegroup']      = array('filename' => 'servicegroups.cfg',
            'order_field' => 'servicegroup_name');
        $arrConfData['tbl_hostdependency']    = array('filename' => 'hostdependencies.cfg',
            'order_field' => 'dependent_host_name');
        $arrConfData['tbl_servicedependency'] = array('filename' => 'servicedependencies.cfg',
            'order_field' => 'config_name');
        $arrConfData['tbl_hostescalation']    = array('filename' => 'hostescalations.cfg',
            'order_field' => 'host_name`,`hostgroup_name');
        $arrConfData['tbl_serviceescalation'] = array('filename' => 'serviceescalations.cfg',
            'order_field' => 'config_name');
        $arrConfData['tbl_hostextinfo']       = array('filename' => 'hostextinfo.cfg',
            'order_field' => 'host_name');
        $arrConfData['tbl_serviceextinfo']    = array('filename' => 'serviceextinfo.cfg',
            'order_field' => 'host_name');
        return $arrConfData;
    }

    /**
     * Writes a configuration file including all datasets of a configuration table or returns the output as a text
     * file for download. (Private worker function)
     * @param int $intConfigID                  Configuration target ID
     * @param string $strTableName              Table name
     * @param int $intMode                      0 = Write file to filesystem
     *                                          1 = Return Textfile for download test
     * @param array $arrTableData               Dataset array for host and services only
     * @param int $intID                        Key for dataset array
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    /** @noinspection ArrayTypeOfParameterByDefaultValueInspection */
    private function writeConfTemplate($intConfigID, $strTableName, $intMode, $arrTableData = array(), $intID = 0)
    {
        // Variable definitions
        $strSQL         = '';
        $strOrderField  = '';
        $strFileString  = '';
        $arrTplOptions  = array('use_preg' => false);
        $strDomainWhere = ' (`config_id`=' . $this->intDomainId . ') ';
        $intType        = 0;
        $intReturn      = 0;
        // Read some settings and informations
        $this->getConfigData($intConfigID, 'utf8_decode', $setUTF8Decode);
        $this->getDomainData('enable_common', $setEnableCommon);
        $this->getConfigData($intConfigID, 'version', $intNagiosVersion);
        $arrConfigData = $this->getConfData();
        if (isset($arrConfigData[$strTableName])) {
            $strFileString = str_replace('.cfg', '', $arrConfigData[$strTableName]['filename']);
            $strOrderField = $arrConfigData[$strTableName]['order_field'];
        }
        // Variable rewritting
        if ($setEnableCommon != 0) {
            $strDomainWhere = str_replace(')', ' OR `config_id`=0)', $strDomainWhere);
        }
        // Special processing for table host and service
        $setTemplate = $strFileString. '.tpl.dat';
        if (($strTableName == 'tbl_host') || ($strTableName == 'tbl_service')) {
            // Define variable names based on table name
            switch ($strTableName) {
                case 'tbl_host':
                    $strFileString = $arrTableData[$intID]['host_name'];
                    $intDomainId   = $arrTableData[$intID]['config_id'];
                    $setTemplate   = 'hosts.tpl.dat';
                    $intType       = 1;
                    $strSQL        = 'SELECT * FROM `' . $strTableName . "` WHERE `host_name`='$strFileString' "
                        . "AND `active`='1' AND `config_id`=$intDomainId";
                    break;
                case 'tbl_service':
                    $strFileString = $arrTableData[$intID]['config_name'];
                    $intDomainId   = $arrTableData[$intID]['config_id'];
                    $setTemplate   = 'services.tpl.dat';
                    $intType       = 2;
                    $strSQL        = 'SELECT * FROM `' . $strTableName . "` WHERE `config_name`='$strFileString' "
                        . "AND `active`='1' AND `config_id`=$intDomainId ORDER BY `service_description`";
                    break;
            }
        } else {
            $strSQL  = 'SELECT * FROM `' .$strTableName."` WHERE $strDomainWhere AND `active`='1' ".
                'ORDER BY `' .$strOrderField. '`';
        }
        $strFile     = $strFileString. '.cfg';
        // Load configuration template file
        $tplConf = new \HTML_Template_IT($this->arrSettings['path']['base_path']. '/templates/files/');
        $tplConf->loadTemplatefile($setTemplate, true, true);
        $tplConf->setOptions($arrTplOptions);
        $tplConf->setVariable('CREATE_DATE', date('Y-m-d H:i:s'));
        $tplConf->setVariable('NAGIOS_QL_VERSION', $this->arrSettings['db']['version']);
        $tplConf->setVariable('VERSION', $this->getVersionString($intConfigID));
        // Write data from configuration table
        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if ($booReturn && ($intDataCount != 0) && ($strFileString != '')) {
            // Process every data set
            /** @noinspection ForeachInvariantsInspection */
            for ($i = 0; $i < $intDataCount; $i++) {
                $intDataId = 0;
                /** @noinspection ForeachSourceInspection */
                foreach ($arrData[$i] as $key => $value) {
                    if ($key == 'id') {
                        $intDataId = $value;
                    }
                    if ($key == 'config_name') {
                        $key = '#NAGIOSQL_CONFIG_NAME';
                    }
                    // UTF8 decoded vaules
                    if ($setUTF8Decode == 1) {
                        $value = utf8_decode($value);
                    }
                    // Pass special fields (NagiosQL data fields not used by Nagios itselves)
                    if ($this->skipEntries($strTableName, $intNagiosVersion, $key, $value) == 1) {
                        continue;
                    }
                    // Get relation data
                    $intSkip = $this->getRelationData($strTableName, $tplConf, $arrData[$i], $key, $value);
                    // Rename field names
                    $this->renameFields($strTableName, $intConfigID, $intDataId, $key, $value, $intSkip);
                    // Inset data field
                    if ($intSkip != 1) {
                        // Insert fill spaces
                        $strFillLen = (30- \strlen($key));
                        $strSpace = ' ';
                        for ($f = 0; $f < $strFillLen; $f++) {
                            $strSpace .= ' ';
                        }
                        // Write key and value to template
                        $tplConf->setVariable('ITEM_TITLE', $key.$strSpace);
                        // Short values
                        if (($intNagiosVersion != 3) || (\strlen($value) < 800)) {
                            $tplConf->setVariable('ITEM_VALUE', $value);
                            $tplConf->parse('configline');
                        } else { // Long values
                            $arrValueTemp = explode(',', $value);
                            $strValueNew  = '';
                            $intArrCount  = \count($arrValueTemp);
                            $intCounter   = 0;
                            $strSpace     = ' ';
                            for ($f = 0; $f < 30; $f++) {
                                $strSpace .= ' ';
                            }
                            foreach ($arrValueTemp as $elem) {
                                if (\strlen($strValueNew) < 800) {
                                    $strValueNew .= $elem. ',';
                                } else {
                                    if (substr($strValueNew, -1) == ',') {
                                        $strValueNew = substr($strValueNew, 0, -1);
                                    }
                                    if ($intCounter < $intArrCount) {
                                        $strValueNew .= ",\\";
                                        $tplConf->setVariable('ITEM_VALUE', $strValueNew);
                                        $tplConf->parse('configline');
                                        $tplConf->setVariable('ITEM_TITLE', $strSpace);
                                    } else {
                                        $tplConf->setVariable('ITEM_VALUE', $strValueNew);
                                        $tplConf->parse('configline');
                                        $tplConf->setVariable('ITEM_TITLE', $strSpace);
                                    }
                                    $strValueNew = $elem. ',';
                                }
                                $intCounter++;
                            }
                            if ($strValueNew != '') {
                                if (substr($strValueNew, -1) == ',') {
                                    $strValueNew = substr($strValueNew, 0, -1);
                                }
                                $tplConf->setVariable('ITEM_VALUE', $strValueNew);
                                $tplConf->parse('configline');
                            }
                        }
                    }
                }
                // Special processing for time periods
                if ($strTableName == 'tbl_timeperiod') {
                    $arrDataTime = array();
                    $strSQLTime = 'SELECT `definition`, `range` '
                        . 'FROM `tbl_timedefinition` WHERE `tipId` = ' .$arrData[$i]['id'];
                    $booReturn  = $this->myDBClass->hasDataArray($strSQLTime, $arrDataTime, $intDataCountTime);
                    if ($booReturn && $intDataCountTime != 0) {
                        foreach ($arrDataTime as $data) {
                            // Skip other values than weekdays in nagios version below 3
                            if ($intNagiosVersion < 3) {
                                $arrWeekdays = array('monday','tuesday','wednesday','thursday','friday','saturday',
                                    'sunday');
                                if (!\in_array($data['definition'], $arrWeekdays, true)) {
                                    continue;
                                }
                            }
                            // Insert fill spaces
                            $strFillLen = (30- \strlen($data['definition']));
                            $strSpace = ' ';
                            for ($f = 0; $f < $strFillLen; $f++) {
                                $strSpace .= ' ';
                            }
                            // Write key and value
                            $tplConf->setVariable('ITEM_TITLE', $data['definition'].$strSpace);
                            $tplConf->setVariable('ITEM_VALUE', $data['range']);
                            $tplConf->parse('configline');
                        }
                    }
                }
                // Write configuration set
                $tplConf->parse('configset');
            }
        } elseif ($booReturn && ($intDataCount == 0) && ($strFileString != '')) {
            $this->processClassMessage(translate('Error while selecting data from database:')
                . '::', $this->strErrorMessage);
            $this->processClassMessage($this->myDBClass->strErrorMessage, $this->strErrorMessage);
            $intReturn = 1;
        } else {
            $this->myDataClass->writeLog(translate('Writing of the configuration failed - no dataset '
                . 'or not activated dataset found'));
            $this->processClassMessage(translate('Writing of the configuration failed - no dataset '
                    . 'or not activated dataset found'). '::', $this->strErrorMessage);
            $intReturn = 1;
        }
        if ($intMode == 0) {
            $intReturn = $this->getConfigFile($strFile, $intConfigID, $intType, $resCfgFile, $strCfgFile);
            if ($intReturn == 0) {
                $tplConf->parse();
                $strContent = $tplConf->get();
                $intReturn  = $this->writeConfigFile(
                    $strContent,
                    $strFile,
                    $intType,
                    $intConfigID,
                    $resCfgFile,
                    $strCfgFile
                );
            }
        } elseif ($intMode == 1) {
            $tplConf->show();
        }
        return $intReturn;
    }

    /**
     * Get Nagios version string
     * @param int $intConfigID                  Configuration target ID
     * @return string                           Version string
     */
    private function getVersionString($intConfigID)
    {
        $arrVersion = array(
            'Nagios 2.x config file',
            'Nagios 2.9 config file',
            'Nagios 3.x config file',
            'Nagios 4.x config file'
        );
        $this->getConfigData($intConfigID, 'version', $intVersion);
        if (($intVersion >= 1) && ($intVersion <= \count($arrVersion))) {
            $strVersion = $arrVersion[$intVersion - 1];
        } else {
            $strVersion = '';
        }
        return $strVersion;
    }

    /**
     * Skip database values based on Nagios version
     * @param string $strTableName              Table name
     * @param int $intVersionValue              Nagios version value
     * @param string $key                       Data key
     * @param string $value                     Data value
     * @return int
     */
    private function skipEntries($strTableName, $intVersionValue, $key, &$value)
    {
        // Define variables
        $arrOption = array();
        $intReturn = 0;
        // Skip common fields
        $strSpecial  = 'id,active,last_modified,access_rights,access_group,config_id,template,nodelete,command_type,';
        $strSpecial .= 'import_hash';

        // Skip fields of special tables
        if ($strTableName == 'tbl_hosttemplate') {
            $strSpecial .= ',parents_tploptions,hostgroups_tploptions,contacts_tploptions';
            $strSpecial .= ',contact_groups_tploptions,use_template_tploptions';
        }
        if ($strTableName == 'tbl_servicetemplate') {
            $strSpecial .= ',host_name_tploptions,hostgroup_name_tploptions,parents_tploptions,contacts_tploptions';
            $strSpecial .= ',servicegroups_tploptions,contact_groups_tploptions,use_template_tploptions';
        }
        if ($strTableName == 'tbl_contact') {
            $strSpecial .= ',use_template_tploptions,contactgroups_tploptions';
            $strSpecial .= ',host_notification_commands_tploptions,service_notification_commands_tploptions';
        }
        if ($strTableName == 'tbl_contacttemplate') {
            $strSpecial .= ',use_template_tploptions,contactgroups_tploptions';
            $strSpecial .= ',host_notification_commands_tploptions,service_notification_commands_tploptions';
        }
        if ($strTableName == 'tbl_host') {
            $strSpecial .= ',parents_tploptions,hostgroups_tploptions,contacts_tploptions';
            $strSpecial .= ',contact_groups_tploptions,use_template_tploptions';
        }
        if ($strTableName == 'tbl_service') {
            $strSpecial .= ',host_name_tploptions,hostgroup_name_tploptions,parents_tploptions';
            $strSpecial .= ',servicegroups_tploptions,contacts_tploptions,contact_groups_tploptions';
            $strSpecial .= ',use_template_tploptions';
        }
        if ($strTableName == 'tbl_command') {
            $strSpecial .= ',arg1_info,arg2_info,arg3_info,arg4_info,arg5_info,arg6_info,arg7_info,arg8_info';
        }

        // Pass fields based on nagios version lower than 3.x
        if ($intVersionValue < 3) {
            if ($strTableName == 'tbl_timeperiod') {
                $strSpecial .= ',use_template,exclude,name';
            }
            if (($strTableName == 'tbl_contact') || ($strTableName == 'tbl_contacttemplate')) {
                $strSpecial .= ',host_notifications_enabled,service_notifications_enabled,can_submit_commands';
                $strSpecial .= ',retain_status_information,retain_nonstatus_information';
                $arrOption['host_notification_options']    = ',s';
                $arrOption['service_notification_options'] = ',s';
            }
            if ($strTableName == 'tbl_contactgroup') {
                $strSpecial .= ',contactgroup_members';
            }
            if ($strTableName == 'tbl_hostgroup') {
                $strSpecial .= ',hostgroup_members,notes,notes_url,action_url';
            }
            if ($strTableName == 'tbl_servicegroup') {
                $strSpecial .= ',servicegroup_members,notes,notes_url,action_url';
            }
            if ($strTableName == 'tbl_hostdependency') {
                $strSpecial .= ',dependent_hostgroup_name,hostgroup_name,dependency_period';
            }
            if ($strTableName == 'tbl_hostescalation') {
                $strSpecial .= ',contacts';
            }
            if ($strTableName == 'tbl_servicedependency') {
                $strSpecial .= ',dependent_hostgroup_name,hostgroup_name,dependency_period,dependent_servicegroup_name';
                $strSpecial .= ',servicegroup_name';
            }
            if ($strTableName == 'tbl_serviceescalation') {
                $strSpecial .= ',hostgroup_name,contacts,servicegroup_name';
            }
            if (($strTableName == 'tbl_host') || ($strTableName == 'tbl_hosttemplate')) {
                $strSpecial .= ',initial_state,flap_detection_options,contacts,notes,notes_url,action_url';
                $strSpecial .= ',icon_image,icon_image_alt,vrml_image,statusmap_image,2d_coords,3d_coords';
                $arrOption['notification_options'] = ',s';
            }
            // Services
            if (($strTableName == 'tbl_service') || ($strTableName == 'tbl_servicetemplate')) {
                $strSpecial .= ',initial_state,flap_detection_options,contacts,notes,notes_url,action_url';
                $strSpecial .= ',icon_image,icon_image_alt';
                $arrOption['notification_options'] = ',s';
            }
        }
        // Pass fields based on nagios version higher than 2.x
        if ($intVersionValue > 2) {
            if ($strTableName == 'tbl_servicetemplate') {
                $strSpecial .= ',parallelize_check ';
            }
            if ($strTableName == 'tbl_service') {
                $strSpecial .= ',parallelize_check';
            }
        }
        // Pass fields based on nagios version lower than 4.x
        if ($intVersionValue < 4) {
            if (($strTableName == 'tbl_contact') || ($strTableName == 'tbl_contacttemplate')) {
                $strSpecial .= ',minimum_importance';
            }
            if ($strTableName == 'tbl_host') {
                $strSpecial .= ',importance';
            }
            if (($strTableName == 'tbl_service') || ($strTableName == 'tbl_servicetemplate')) {
                $strSpecial .= ',importance,parents';
            }
        }
        if ($intVersionValue == 1) {
            $strSpecial .= '';
        }
        // Reduce option values
        if (array_key_exists($key, $arrOption) && (\count($arrOption) != 0)) {
            $value = str_replace(array($arrOption[$key], str_replace(',', '', $arrOption[$key])), '', $value);
            if ($value == '') {
                $intReturn = 1;
            }
        }
        if ($intReturn == 0) {
            // Skip entries
            $arrSpecial = explode(',', $strSpecial);
            if (($value == '') || \in_array($key, $arrSpecial, true)) {
                $intReturn = 1;
            }
        }
        if ($intReturn == 0) {
            // Do not write config data (based on 'skip' option)
            $strNoTwo  = 'active_checks_enabled,passive_checks_enabled,obsess_over_host,check_freshness,';
            $strNoTwo .= 'event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,';
            $strNoTwo .= 'retain_nonstatus_information,notifications_enabled,parallelize_check,is_volatile,';
            $strNoTwo .= 'host_notifications_enabled,service_notifications_enabled,can_submit_commands,';
            $strNoTwo .= 'obsess_over_service';
            foreach (explode(',', $strNoTwo) as $elem) {
                if (($key == $elem) && ($value == '2')) {
                    $intReturn = 1;
                }
                if (($intVersionValue < 3) && ($key == $elem) && ($value == '3')) {
                    $intReturn = 1;
                }
            }
        }
        return $intReturn;
    }

    /**
     * Get related data
     * @param string $strTableName              Table name
     * @param \HTML_Template_IT $resTemplate    Template ressource
     * @param array $arrData                    Dataset array
     * @param string $strDataKey                Data key
     * @param string $strDataValue              Data value
     * @return int                              0 = use data / 1 = skip data
     *                                          Status message is stored in message class variables
     */
    private function getRelationData($strTableName, $resTemplate, $arrData, $strDataKey, &$strDataValue)
    {
        // Define Variables
        $intReturn    = 0;
        $intSkipProc  = 0;
        $arrRelations = array();
        // Pass function for tbl_command
        if ($strTableName == 'tbl_command') {
            $intSkipProc = 1;
        }
        // Get relation info and store the value in a class variable (speedup export)
        if (($intSkipProc == 0) && ($this->strRelTable != $strTableName)) {
            $intReturn = $this->myDataClass->tableRelations($strTableName, $arrRelations);
            $this->strRelTable = $strTableName;
            $this->arrRelData  = $arrRelations;
        } elseif ($intSkipProc == 0) {
            $arrRelations = $this->arrRelData;
            $intReturn   = 0;
        }
        if (($intSkipProc == 0) && (!\is_array($arrRelations)) && (\count($arrRelations) == 0)) {
            $intSkipProc = 1;
            $intReturn   = 1;
        }
        if ($intSkipProc == 0) {
            // Common domain is enabled?
            $this->getDomainData('enable_common', $intCommonEnable);
            if ($intCommonEnable == 1) {
                $strDomainWhere1 = ' (`config_id`=' . $this->intDomainId . ' OR `config_id`=0) ';
            } else {
                $strDomainWhere1 = ' `config_id`=' . $this->intDomainId . ' ';
            }
            // Process relations
            foreach ($arrRelations as $elem) {
                if ($elem['fieldName'] == $strDataKey) {
                    // Process normal 1:n relations (1 = only data / 2 = including a * value)
                    if (($elem['type'] == 2) && (($strDataValue == 1) || ($strDataValue == 2))) {
                        $intReturn = $this->processRelation1($arrData, $strDataValue, $elem, $strDomainWhere1);
                        // Process normal 1:1 relations
                    } elseif ($elem['type'] == 1) {
                        $intReturn = $this->processRelation2($arrData, $strDataValue, $elem, $strDomainWhere1);
                        // Process normal 1:n relations with special table and idSort (template tables)
                    } elseif (($elem['type'] == 3) && ($strDataValue == 1)) {
                        $intReturn = $this->processRelation3($arrData, $strDataValue, $elem, $strDomainWhere1);
                        // Process special 1:n:str relations with string values (servicedependencies)
                    } elseif (($elem['type'] == 6) && (($strDataValue == 1) || ($strDataValue == 2))) {
                        $intReturn = $this->processRelation4($arrData, $strDataValue, $elem, $strDomainWhere1);
                        // Process special relations for free variables
                    } elseif (($elem['type'] == 4) && ($strDataValue == 1) && ($this->intNagVersion >= 3)) {
                        $intReturn = $this->processRelation5($resTemplate, $arrData, $elem);
                        // Process special relations for service groups
                    } elseif (($elem['type'] == 5) && ($strDataValue == 1)) {
                        $intReturn = $this->processRelation6($arrData, $strDataValue, $elem, $strDomainWhere1);
                        // Process special relations for service parents
                    } elseif (($elem['type'] == 7) && ($strDataValue == 1)) {
                        $intReturn = $this->processRelation7($arrData, $strDataValue, $elem);
                        // Process "*"
                    } elseif ($strDataValue == 2) {
                        $strDataValue = '*';
                    } else {
                        $intReturn = 1;
                    }
                }
            }
        }
        return $intReturn;
    }

    /**
     * Rename field names
     * @param string $strTableName              Table name
     * @param int $intConfigID                  Configuration target ID
     * @param int $intDataId                    Data ID
     * @param string $key                       Data key (by reference)
     * @param string $value                     Data value (by reference)
     * @param int $intSkip                      Skip value (by reference) 1 = skip / 0 = pass
     */
    private function renameFields($strTableName, $intConfigID, $intDataId, &$key, &$value, &$intSkip)
    {
        if ($this->intNagVersion == 0) {
            $this->getConfigData($intConfigID, 'version', $this->intNagVersion);
        }
        // Picture path
        if ($this->strPicPath == 'none') {
            $this->getConfigData($intConfigID, 'picturedir', $this->strPicPath);
        }
        if ($key == 'icon_image') {
            $value = $this->strPicPath.$value;
        }
        if ($key == 'vrml_image') {
            $value = $this->strPicPath.$value;
        }
        if ($key == 'statusmap_image') {
            $value = $this->strPicPath.$value;
        }
        // Tables
        if ($strTableName == 'tbl_host') {
            if ($key == 'use_template') {
                $key = 'use';
            }
            $strVIValues  = 'active_checks_enabled,passive_checks_enabled,check_freshness,obsess_over_host,';
            $strVIValues .= 'event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,';
            $strVIValues .= 'retain_nonstatus_information,notifications_enabled';
            if (\in_array($key, explode(',', $strVIValues), true)) {
                if ($value == -1) {
                    $value = 'null';
                }
                if ($value == 3) {
                    $value = 'null';
                }
            }
            if ($key == 'parents') {
                $value = $this->checkTpl($value, 'parents_tploptions', 'tbl_host', $intDataId, $intSkip);
            }
            if ($key == 'hostgroups') {
                $value = $this->checkTpl($value, 'hostgroups_tploptions', 'tbl_host', $intDataId, $intSkip);
            }
            if ($key == 'contacts') {
                $value = $this->checkTpl($value, 'contacts_tploptions', 'tbl_host', $intDataId, $intSkip);
            }
            if ($key == 'contact_groups') {
                $value = $this->checkTpl($value, 'contact_groups_tploptions', 'tbl_host', $intDataId, $intSkip);
            }
            if ($key == 'use') {
                $value = $this->checkTpl($value, 'use_template_tploptions', 'tbl_host', $intDataId, $intSkip);
            }
            if ($key == 'check_command') {
                $value = str_replace("\::bang::", "\!", $value);
            }
            if ($key == 'check_command') {
                $value = str_replace('::bang::', "\!", $value);
            }
        }
        if ($strTableName == 'tbl_service') {
            if ($key == 'use_template') {
                $key = 'use';
            }
            if ($this->intNagVersion < 2) {
                if ($key == 'check_interval') {
                    $key = 'normal_check_interval';
                }
                if ($key == 'retry_interval') {
                    $key = 'retry_check_interval';
                }
            }
            $strVIValues  = 'is_volatile,active_checks_enabled,passive_checks_enabled,parallelize_check,';
            $strVIValues .= 'obsess_over_service,check_freshness,event_handler_enabled,flap_detection_enabled,';
            $strVIValues .= 'process_perf_data,retain_status_information,retain_nonstatus_information,';
            $strVIValues .= 'notifications_enabled';
            if (\in_array($key, explode(',', $strVIValues), true)) {
                if ($value == -1) {
                    $value = 'null';
                }
                if ($value == 3) {
                    $value = 'null';
                }
            }
            if ($key == 'host_name') {
                $value = $this->checkTpl($value, 'host_name_tploptions', 'tbl_service', $intDataId, $intSkip);
            }
            if ($key == 'hostgroup_name') {
                $value = $this->checkTpl($value, 'hostgroup_name_tploptions', 'tbl_service', $intDataId, $intSkip);
            }
            if ($key == 'parents') {
                $value = $this->checkTpl($value, 'parents_tploptions', 'tbl_service', $intDataId, $intSkip);
            }
            if ($key == 'servicegroups') {
                $value = $this->checkTpl($value, 'servicegroups_tploptions', 'tbl_service', $intDataId, $intSkip);
            }
            if ($key == 'contacts') {
                $value = $this->checkTpl($value, 'contacts_tploptions', 'tbl_service', $intDataId, $intSkip);
            }
            if ($key == 'contact_groups') {
                $value = $this->checkTpl($value, 'contact_groups_tploptions', 'tbl_service', $intDataId, $intSkip);
            }
            if ($key == 'use') {
                $value = $this->checkTpl($value, 'use_template_tploptions', 'tbl_service', $intDataId, $intSkip);
            }
            if ($key == 'check_command') {
                $value = str_replace("\::bang::", "\!", $value);
            }
            if ($key == 'check_command') {
                $value = str_replace('::bang::', "\!", $value);
            }
        }
        if ($strTableName == 'tbl_hosttemplate') {
            if ($key == 'template_name') {
                $key = 'name';
            }
            if ($key == 'use_template') {
                $key = 'use';
            }
            $strVIValues  = 'active_checks_enabled,passive_checks_enabled,check_freshness,obsess_over_host,';
            $strVIValues .= 'event_handler_enabled,flap_detection_enabled,process_perf_data,retain_status_information,';
            $strVIValues .= 'retain_nonstatus_information,notifications_enabled';
            if (\in_array($key, explode(',', $strVIValues), true)) {
                if ($value == -1) {
                    $value = 'null';
                }
                if ($value == 3) {
                    $value = 'null';
                }
            }
            if ($key == 'parents') {
                $value = $this->checkTpl($value, 'parents_tploptions', 'tbl_hosttemplate', $intDataId, $intSkip);
            }
            if ($key == 'hostgroups') {
                $value = $this->checkTpl($value, 'hostgroups_tploptions', 'tbl_hosttemplate', $intDataId, $intSkip);
            }
            if ($key == 'contacts') {
                $value = $this->checkTpl($value, 'contacts_tploptions', 'tbl_hosttemplate', $intDataId, $intSkip);
            }
            if ($key == 'contact_groups') {
                $value = $this->checkTpl($value, 'contact_groups_tploptions', 'tbl_hosttemplate', $intDataId, $intSkip);
            }
            if ($key == 'use') {
                $value = $this->checkTpl($value, 'use_template_tploptions', 'tbl_hosttemplate', $intDataId, $intSkip);
            }
        }
        if ($strTableName == 'tbl_servicetemplate') {
            if ($key == 'template_name') {
                $key = 'name';
            }
            if ($key == 'use_template') {
                $key = 'use';
            }
            if ($this->intNagVersion < 2) {
                if ($key == 'check_interval') {
                    $key = 'normal_check_interval';
                }
                if ($key == 'retry_interval') {
                    $key = 'retry_check_interval';
                }
            }
            $strVIValues  = 'is_volatile,active_checks_enabled,passive_checks_enabled,parallelize_check,';
            $strVIValues .= 'obsess_over_service,check_freshness,event_handler_enabled,flap_detection_enabled,';
            $strVIValues .= 'process_perf_data,retain_status_information,retain_nonstatus_information,';
            $strVIValues .= 'notifications_enabled';
            if (\in_array($key, explode(',', $strVIValues), true)) {
                if ($value == -1) {
                    $value = 'null';
                }
                if ($value == 3) {
                    $value = 'null';
                }
            }
            if ($key == 'host_name') {
                $value = $this->checkTpl($value, 'host_name_tploptions', 'tbl_servicetemplate', $intDataId, $intSkip);
            }
            if ($key == 'hostgroup_name') {
                $value = $this->checkTpl(
                    $value,
                    'hostgroup_name_tploptions',
                    'tbl_servicetemplate',
                    $intDataId,
                    $intSkip
                );
            }
            if ($key == 'parents') {
                $value = $this->checkTpl($value, 'parents_tploptions', 'tbl_servicetemplate', $intDataId, $intSkip);
            }
            if ($key == 'servicegroups') {
                $value = $this->checkTpl(
                    $value,
                    'servicegroups_tploptions',
                    'tbl_servicetemplate',
                    $intDataId,
                    $intSkip
                );
            }
            if ($key == 'contacts') {
                $value = $this->checkTpl($value, 'contacts_tploptions', 'tbl_servicetemplate', $intDataId, $intSkip);
            }
            if ($key == 'contact_groups') {
                $value = $this->checkTpl(
                    $value,
                    'contact_groups_tploptions',
                    'tbl_servicetemplate',
                    $intDataId,
                    $intSkip
                );
            }
            if ($key == 'use') {
                $value = $this->checkTpl(
                    $value,
                    'use_template_tploptions',
                    'tbl_servicetemplate',
                    $intDataId,
                    $intSkip
                );
            }
        }
        if ($strTableName == 'tbl_contact') {
            if ($key == 'use_template') {
                $key = 'use';
            }
            $strVIValues  = 'host_notifications_enabled,service_notifications_enabled,can_submit_commands,';
            $strVIValues .= 'retain_status_information,retain_nonstatus_information';
            if (\in_array($key, explode(',', $strVIValues), true)) {
                if ($value == -1) {
                    $value = 'null';
                }
                if ($value == 3) {
                    $value = 'null';
                }
            }
            if ($key == 'contactgroups') {
                $value = $this->checkTpl($value, 'contactgroups_tploptions', 'tbl_contact', $intDataId, $intSkip);
            }
            if ($key == 'host_notification_commands') {
                $value = $this->checkTpl(
                    $value,
                    'host_notification_commands_tploptions',
                    'tbl_contact',
                    $intDataId,
                    $intSkip
                );
            }
            if ($key == 'service_notification_commands') {
                $value = $this->checkTpl(
                    $value,
                    'service_notification_commands_tploptions',
                    'tbl_contact',
                    $intDataId,
                    $intSkip
                );
            }
            if ($key == 'use') {
                $value = $this->checkTpl($value, 'use_template_tploptions', 'tbl_contact', $intDataId, $intSkip);
            }
        }
        if ($strTableName == 'tbl_contacttemplate') {
            if ($key == 'template_name') {
                $key = 'name';
            }
            if ($key == 'use_template') {
                $key = 'use';
            }
            $strVIValues  = 'host_notifications_enabled,service_notifications_enabled,can_submit_commands,';
            $strVIValues .= 'retain_status_information,retain_nonstatus_information';
            if (in_array($key, explode(',', $strVIValues), true)) {
                if ($value == -1) {
                    $value = 'null';
                }
                if ($value == 3) {
                    $value = 'null';
                }
            }
            if ($key == 'contactgroups') {
                $value = $this->checkTpl(
                    $value,
                    'contactgroups_tploptions',
                    'tbl_contacttemplate',
                    $intDataId,
                    $intSkip
                );
            }
            if ($key == 'host_notification_commands') {
                $value = $this->checkTpl(
                    $value,
                    'host_notification_commands_tploptions',
                    'tbl_contacttemplate',
                    $intDataId,
                    $intSkip
                );
            }
            if ($key == 'service_notification_commands') {
                $value = $this->checkTpl(
                    $value,
                    'service_notification_commands_tploptions',
                    'tbl_contacttemplate',
                    $intDataId,
                    $intSkip
                );
            }
            if ($key == 'use') {
                $value = $this->checkTpl(
                    $value,
                    'use_template_tploptions',
                    'tbl_contacttemplate',
                    $intDataId,
                    $intSkip
                );
            }
        }
        if ((($strTableName == 'tbl_hosttemplate') || ($strTableName == 'tbl_servicetemplate') ||
                ($strTableName == 'tbl_contacttemplate')) && $key == 'register') {
            $value = '0';
        }
        if ($strTableName == 'tbl_timeperiod' && $key == 'use_template') {
            $key = 'use';
        }
    }

    /**
     * Open configuration file
     * @param string $strFile                   File name
     * @param int $intConfigID                  Configuration ID
     * @param int $intType                      Type ID
     * @param resource|bool $resConfigFile      Temporary or configuration file ressource (by reference)
     * @param string $strConfigFile             Configuration file name (by reference)
     * @return int                              0 = successful / 1 = error
     */
    private function getConfigFile($strFile, $intConfigID, $intType, &$resConfigFile, &$strConfigFile)
    {
        // Variable definitions
        $strBaseDir   = '';
        $intMethod    = 1;
        $intReturn    = 0;
        // Get config data
        if ($intType == 1) {
            $this->getConfigData($intConfigID, 'hostconfig', $strBaseDir);
            $strType = 'host';
        } elseif ($intType == 2) {
            $this->getConfigData($intConfigID, 'serviceconfig', $strBaseDir);
            $strType = 'service';
        } else {
            $this->getConfigData($intConfigID, 'basedir', $strBaseDir);
            $strType = 'basic';
        }
        $this->getConfigData($intConfigID, 'method', $intMethod);
        // Backup config file
        $this->moveFile($strType, $strFile, $intConfigID);
        // Variable definition
        $strConfigFile = $strBaseDir. '/' .$strFile;
        // Local file system
        if ($intMethod == 1) {
            // Save configuration file
            if (is_writable($strConfigFile) || (!file_exists($strConfigFile) && is_writable($strBaseDir))) {
                $resConfigFile = fopen($strConfigFile, 'wb');
                chmod($strConfigFile, 0644);
            } else {
                $this->myDataClass->writeLog(translate('Configuration write failed:'). ' ' .$strFile);
                $this->processClassMessage(translate('Cannot open/overwrite the configuration file (check the '
                        .'permissions)!'). '::', $this->strErrorMessage);
                $intReturn = 1;
            }
        } elseif ($intMethod == 2) { // Remote file (FTP)
            // Check connection
            if (empty($this->resConnectId) || !\is_resource($this->resConnectId) ||
                ($this->resConnectType != 'FTP')) {
                $intReturn = $this->getFTPConnection($intConfigID);
            }
            if ($intReturn == 0) {
                // Open the config file
                if (isset($this->arrSettings['path']) && isset($this->arrSettings['path']['tempdir'])) {
                    $strConfigFile = tempnam($this->arrSettings['path']['tempdir'], 'nagiosql');
                } else {
                    $strConfigFile = tempnam(sys_get_temp_dir(), 'nagiosql');
                }
                $resConfigFile = fopen($strConfigFile, 'wb');
            }
        } elseif ($intMethod == 3) { // Remote file (SFTP)
            // Check connection
            if (empty($this->resConnectId) || !\is_resource($this->resConnectId) ||
                ($this->resConnectType != 'SSH')) {
                $intReturn = $this->getSSHConnection($intConfigID);
            }
            if ($intReturn == 0) {
                if (isset($this->arrSettings['path']) && isset($this->arrSettings['path']['tempdir'])) {
                    $strConfigFile = tempnam($this->arrSettings['path']['tempdir'], 'nagiosql');
                } else {
                    $strConfigFile = tempnam(sys_get_temp_dir(), 'nagiosql');
                }
                $resConfigFile = fopen($strConfigFile, 'wb');
            }
        }
        return $intReturn;
    }

    /**
     * Write configuration file
     * @param string $strData               Data string
     * @param string $strFile               File name
     * @param int $intType                  Type ID
     * @param int $intConfigID              Configuration target ID
     * @param resource $resConfigFile       Temporary or configuration file ressource
     * @param string $strConfigFile         Configuration file name
     * @return int                          0 = successful / 1 = error
     */
    private function writeConfigFile($strData, $strFile, $intType, $intConfigID, $resConfigFile, $strConfigFile)
    {
        // Variable definitions
        $intReturn = 0;
        // Get config data
        if ($intType == 1) {
            $this->getConfigData($intConfigID, 'hostconfig', $strBaseDir);
        } elseif ($intType == 2) {
            $this->getConfigData($intConfigID, 'serviceconfig', $strBaseDir);
        } else {
            $this->getConfigData($intConfigID, 'basedir', $strBaseDir);
        }
        $this->getConfigData($intConfigID, 'method', $intMethod);
        $strData  = str_replace("\r\n", "\n", $strData);
        fwrite($resConfigFile, $strData);
        // Local filesystem
        if ($intMethod == 1) {
            fclose($resConfigFile);
        } elseif ($intMethod == 2) { // FTP access
            // SSH Possible
            if (!\function_exists('ftp_put')) {
                $this->processClassMessage(translate('FTP module not loaded!'). '::', $this->strErrorMessage);
                $intReturn = 1;
            } else {
                $intErrorReporting = error_reporting();
                error_reporting(0);
                if (!ftp_put($this->resConnectId, $strBaseDir . '/' . $strFile, $strConfigFile, FTP_ASCII)) {
                    $arrError = error_get_last();
                    error_reporting($intErrorReporting);
                    $this->processClassMessage(translate('Cannot open/overwrite the configuration file (FTP connection '
                            .'failed)!') . '::', $this->strErrorMessage);
                    if ($arrError['message'] != '') {
                        $this->processClassMessage($arrError['message'] . '::', $this->strErrorMessage);
                    }
                    $intReturn = 1;
                }
                error_reporting($intErrorReporting);
                ftp_close($this->resConnectId);
                fclose($resConfigFile);
            }
        } elseif ($intMethod == 3) { // SSH access
            // SSH Possible
            if (!\function_exists('ssh2_scp_send')) {
                $this->processClassMessage(translate('SSH module not loaded!'). '::', $this->strErrorMessage);
                $intReturn = 1;
            } else {
                $intErrorReporting = error_reporting();
                error_reporting(0);
                if (!ssh2_scp_send($this->resConnectId, $strConfigFile, $strBaseDir . '/' . $strFile, 0644)) {
                    $arrError = error_get_last();
                    error_reporting($intErrorReporting);
                    $this->processClassMessage(translate('Cannot open/overwrite the configuration file (remote SFTP)!').
                        '::', $this->strErrorMessage);
                    if ($arrError['message'] != '') {
                        $this->processClassMessage($arrError['message'] . '::', $this->strErrorMessage);
                    }
                    $this->resConnectId = null;
                    $intReturn = 1;
                }
                error_reporting($intErrorReporting);
                fclose($resConfigFile);
                unlink($strConfigFile);
                $this->resConnectId = null;
            }
        }
        if ($intReturn == 0) {
            $this->myDataClass->writeLog(translate('Configuration successfully written:') . ' ' . $strFile);
            $this->processClassMessage(translate('Configuration file successfully written!').
                '::', $this->strInfoMessage);
        }
        return $intReturn;
    }

    /**
     * Process special settings based on template option
     * @param string $strValue              Original data value
     * @param string $strKeyField           Template option field name
     * @param string $strTable              Table name
     * @param int $intId                    Dataset ID
     * @param int $intSkip                  Skip value (by reference)
     * @return string                       Manipulated data value
     */
    public function checkTpl($strValue, $strKeyField, $strTable, $intId, &$intSkip)
    {
        if ($this->intNagVersion < 3) {
            return $strValue;
        }
        $strSQL   = 'SELECT `' .$strKeyField. '` FROM `' .$strTable."` WHERE `id` = $intId";
        $intValue = $this->myDBClass->getFieldData($strSQL);
        if ($intValue == 0) {
            return('+' .$strValue);
        }
        if ($intValue == 1) {
            $intSkip = 0;
            return 'null';
        }
        return $strValue;
    }

    /**
     * @param array $arrData                    Dataset array
     * @param string $strDataValue              Data value
     * @param array $elem                       Relation data array
     * @param string $strDomainWhere1           SQL WHERE add-in
     * @return int                              0 = use data / 1 = skip data
     */
    private function processRelation1($arrData, &$strDataValue, $elem, $strDomainWhere1)
    {
        // Define variables
        $arrDataRel      = array();
        $intDataCountRel = 0;
        $intReturn       = 0;
        // Get relation data
        $strSQLRel = 'SELECT `' . $elem['tableName1'] . '`.`' . $elem['target1'] . '`, `' . $elem['linkTable'] .
            '`.`exclude` FROM `' . $elem['linkTable'] . '` LEFT JOIN `' . $elem['tableName1'] .
            '` ON `' . $elem['linkTable'] . '`.`idSlave` = `' . $elem['tableName1'] . '`.`id`' .
            'WHERE `idMaster`=' . $arrData['id'] . " AND `active`='1' AND $strDomainWhere1" .
            'ORDER BY `' . $elem['tableName1'] . '`.`' . $elem['target1'] . '`';
        $booReturn = $this->myDBClass->hasDataArray($strSQLRel, $arrDataRel, $intDataCountRel);
        if ($booReturn && ($intDataCountRel != 0)) {
            // Rewrite $strDataValue with returned relation data
            if ($strDataValue == 2) {
                $strDataValue = '*,';
            } else {
                $strDataValue = '';
            }
            foreach ($arrDataRel as $data) {
                if ($data['exclude'] == 0) {
                    $strDataValue .= $data[$elem['target1']] . ',';
                } elseif ($this->intNagVersion >= 3) {
                    $strDataValue .= '!' . $data[$elem['target1']] . ',';
                }
            }
            $strDataValue = substr($strDataValue, 0, -1);
            if ($strDataValue == '') {
                $intReturn = 1;
            }
        } else {
            if ($strDataValue == 2) {
                $strDataValue = '*';
            } else {
                $intReturn = 1;
            }
        }
        return $intReturn;
    }

    /**
     * @param array $arrData                    Dataset array
     * @param string $strDataValue              Data value
     * @param array $elem                       Relation data array
     * @param string $strDomainWhere1           SQL WHERE add-in
     * @return int                              0 = use data / 1 = skip data
     */
    private function processRelation2($arrData, &$strDataValue, $elem, $strDomainWhere1)
    {
        // Define variables
        $arrDataRel      = array();
        $arrField        = array();
        $intDataCountRel = 0;
        $intReturn       = 0;
        $strCommand      = '';
        // Get relation data
        if (($elem['tableName1'] == 'tbl_command') &&
            (substr_count($arrData[$elem['fieldName']], '!') != 0)) {
            $arrField = explode('!', $arrData[$elem['fieldName']]);
            $strCommand = strstr($arrData[$elem['fieldName']], '!');
            $strSQLRel = 'SELECT `' . $elem['target1'] . '` FROM `' . $elem['tableName1'] . '`' .
                'WHERE `id`=' . $arrField[0] . "  AND `active`='1' AND $strDomainWhere1";
        } else {
            $strSQLRel = 'SELECT `' . $elem['target1'] . '` FROM `' . $elem['tableName1'] . '`' .
                'WHERE `id`=' . $arrData[$elem['fieldName']] . "  AND `active`='1' AND $strDomainWhere1";
        }
        $booReturn = $this->myDBClass->hasDataArray($strSQLRel, $arrDataRel, $intDataCountRel);
        if ($booReturn && ($intDataCountRel != 0)) {
            // Rewrite $strDataValue with returned relation data
            if (($elem['tableName1'] == 'tbl_command') && (substr_count($strDataValue, '!') != 0)) {
                $strDataValue = $arrDataRel[0][$elem['target1']] . $strCommand;
            } else {
                $strDataValue = $arrDataRel[0][$elem['target1']];
            }
        } else {
            if (($elem['tableName1'] == 'tbl_command') && (substr_count($strDataValue, '!') != 0) &&
                ($arrField[0] == -1)) {
                $strDataValue = 'null';
            } else {
                $intReturn = 1;
            }
        }
        return $intReturn;
    }

    /**
     * @param array $arrData                    Dataset array
     * @param string $strDataValue              Data value
     * @param array $elem                       Relation data array
     * @param string $strDomainWhere1           SQL WHERE add-in
     * @return int                              0 = use data / 1 = skip data
     */
    private function processRelation3($arrData, &$strDataValue, $elem, $strDomainWhere1)
    {
        // Define variables
        $arrDataRel      = array();
        $intDataCountRel = 0;
        $intReturn       = 0;
        // Get relation data
        $strSQLRel = 'SELECT * FROM `' .$elem['linkTable']. '` WHERE `idMaster`=' .$arrData['id']. ' ORDER BY idSort';
        $booReturn = $this->myDBClass->hasDataArray($strSQLRel, $arrDataRel, $intDataCountRel);
        if ($booReturn && ($intDataCountRel != 0)) {
            // Rewrite $strDataValue with returned relation data
            $strDataValue = '';
            foreach ($arrDataRel as $data) {
                if ($data['idTable'] == 1) {
                    $strSQLName = 'SELECT `' .$elem['target1']. '` FROM `' .$elem['tableName1']. '`' .
                        "WHERE `active`='1' AND $strDomainWhere1 AND `id`=".$data['idSlave'];
                } else {
                    $strSQLName = 'SELECT `' .$elem['target2']. '` FROM `' .$elem['tableName2']. '`' .
                        "WHERE `active`='1' AND $strDomainWhere1 AND `id`=".$data['idSlave'];
                }
                $strDataValue .= $this->myDBClass->getFieldData($strSQLName) . ',';
            }
            $strDataValue = substr($strDataValue, 0, -1);
        } else {
            $intReturn = 1;
        }
        return $intReturn;
    }

    /**
     * @param array $arrData                    Dataset array
     * @param string $strDataValue              Data value
     * @param array $elem                       Relation data array
     * @param string $strDomainWhere1           SQL WHERE add-in
     * @return int                              0 = use data / 1 = skip data
     */
    private function processRelation4($arrData, &$strDataValue, $elem, $strDomainWhere1)
    {
        // Define variables
        $arrDataRel      = array();
        $intDataCountRel = 0;
        $intReturn       = 0;
        // Get relation data
        $strSQLRel = 'SELECT `' .$elem['linkTable']. '`.`strSlave`, `' .$elem['linkTable']. '`.`exclude` ' .
            'FROM `' .$elem['linkTable']. '` ' .
            'LEFT JOIN `tbl_service` ON `' .$elem['linkTable']. '`.`idSlave`=`tbl_service`.`id` ' .
            'WHERE `' .$elem['linkTable']. '`.`idMaster`=' .$arrData['id']." AND `active`='1' AND ".
            $strDomainWhere1. ' ' .
            'ORDER BY `' .$elem['linkTable']. '`.`strSlave`';
        $booReturn = $this->myDBClass->hasDataArray($strSQLRel, $arrDataRel, $intDataCountRel);
        if ($booReturn && ($intDataCountRel != 0)) {
            // Rewrite $strDataValue with returned relation data
            if ($strDataValue == 2) {
                $strDataValue = '*,';
            } else {
                $strDataValue = '';
            }
            foreach ($arrDataRel as $data) {
                if ($data['exclude'] == 0) {
                    $strDataValue .= $data['strSlave'] . ',';
                } elseif ($this->intNagVersion >= 3) {
                    $strDataValue .= '!' . $data['strSlave'] . ',';
                }
            }
            $strDataValue = substr($strDataValue, 0, -1);
            if ($strDataValue == '') {
                $intReturn = 1;
            }
        } else {
            if ($strDataValue == 2) {
                $strDataValue = '*';
            } else {
                $intReturn = 1;
            }
        }
        return $intReturn;
    }

    /**
     * @param \HTML_Template_IT $resTemplate    Template object
     * @param array $arrData                    Dataset array
     * @param array $elem                       Relation data array
     * @return int                              0 = use data / 1 = skip data
     */
    private function processRelation5($resTemplate, $arrData, $elem)
    {
        // Define variables
        $arrDataRel      = array();
        $intDataCountRel = 0;
        $strSQLRel = 'SELECT * FROM `tbl_variabledefinition` LEFT JOIN `' .$elem['linkTable']. '` ' .
            'ON `id`=`idSlave` WHERE `idMaster`=' .$arrData['id']. ' ORDER BY `name`';
        $booReturn = $this->myDBClass->hasDataArray($strSQLRel, $arrDataRel, $intDataCountRel);
        if ($booReturn && ($intDataCountRel != 0)) {
            foreach ($arrDataRel as $vardata) {
                // Insert fill spaces
                $strFillLen = (30 - \strlen($vardata['name']));
                $strSpace = ' ';
                for ($f = 0; $f < $strFillLen; $f++) {
                    $strSpace .= ' ';
                }
                $resTemplate->setVariable('ITEM_TITLE', $vardata['name'] . $strSpace);
                $resTemplate->setVariable('ITEM_VALUE', html_entity_decode($vardata['value'], ENT_QUOTES | ENT_XML1, 'UTF-8'));
                $resTemplate->parse('configline');
            }
        }
        return 1;
    }

    /**
     * @param array $arrData                    Dataset array
     * @param string $strDataValue              Data value
     * @param array $elem                       Relation data array
     * @param string $strDomainWhere1           SQL WHERE add-in
     * @return int                              0 = use data / 1 = skip data
     */
    private function processRelation6($arrData, &$strDataValue, $elem, $strDomainWhere1)
    {
        // Define variables
        $arrDataRel = array();
        $arrHG1 = array();
        $arrHG2 = array();
        $intDataCountRel = 0;
        $intHG1 = 0;
        $intHG2 = 0;
        $intReturn = 0;
        // Get relation data
        $strSQLMaster = 'SELECT * FROM `' . $elem['linkTable'] . '` WHERE `idMaster`=' . $arrData['id'];
        $booReturn = $this->myDBClass->hasDataArray($strSQLMaster, $arrDataRel, $intDataCountRel);
        if ($booReturn && ($intDataCountRel != 0)) {
            // Rewrite $strDataValue with returned relation data
            $strDataValue = '';
            foreach ($arrDataRel as $data) {
                // Get excluded hosts
                $arrExclude = array();
                $strSQLEx   = 'SELECT `idSlave` FROM `tbl_lnkServiceToHost` WHERE `exclude`=1 AND `idMaster`=' .
                    $data['idSlaveS'];
                $booReturn = $this->myDBClass->hasDataArray($strSQLEx, $arrEx, $intEx);
                if ($booReturn && ($intEx != 0)) {
                    foreach ($arrEx as $elemEx) {
                        $arrExclude[] = $elemEx['idSlave'];
                    }
                }
                if ($data['idSlaveHG'] != 0) {
                    // Get Sevices
                    $strSQLSrv = 'SELECT `' . $elem['target2'] . '` FROM `' . $elem['tableName2'] .
                        '` WHERE `id`=' . $data['idSlaveS'];
                    $strService = $this->myDBClass->getFieldData($strSQLSrv);
                    $strSQLHG1 = 'SELECT `host_name`, `idSlave` FROM `tbl_host` ' .
                        'LEFT JOIN `tbl_lnkHostgroupToHost` ON `id`=`idSlave` ' .
                        'WHERE `idMaster`=' . $data['idSlaveHG'] . "  AND `active`='1' AND `exclude`=0 " .
                        "AND $strDomainWhere1";
                    $booReturn = $this->myDBClass->hasDataArray($strSQLHG1, $arrHG1, $intHG1);
                    if ($booReturn && ($intHG1 != 0)) {
                        foreach ($arrHG1 as $elemHG1) {
                            if (!in_array($elemHG1['idSlave'], $arrExclude, true) &&
                                substr_count($strDataValue, $elemHG1['host_name'] . ',' . $strService) == 0) {
                                $strDataValue .= $elemHG1['host_name'] . ',' . $strService . ',';
                            }
                        }
                    }
                    $strSQLHG2 = 'SELECT `host_name`, `idMaster` FROM `tbl_host` ' .
                        'LEFT JOIN `tbl_lnkHostToHostgroup` ON `id`=`idMaster` ' .
                        'WHERE `idSlave`=' . $data['idSlaveHG'] . " AND `active`='1' AND  `exclude`=0 " .
                        "AND $strDomainWhere1";
                    $booReturn = $this->myDBClass->hasDataArray($strSQLHG2, $arrHG2, $intHG2);
                    if ($booReturn && ($intHG2 != 0)) {
                        foreach ($arrHG2 as $elemHG2) {
                            if (!in_array($elemHG2['idMaster'], $arrExclude, true) &&
                                substr_count($strDataValue, $elemHG2['host_name'] . ',' . $strService) == 0) {
                                $strDataValue .= $elemHG2['host_name'] . ',' . $strService . ',';
                            }
                        }
                    }
                } else {
                    $strSQLHost = 'SELECT `' . $elem['target1'] . '` FROM `' . $elem['tableName1'] . '` ' .
                        'WHERE `id`=' . $data['idSlaveH'] . "  AND `active`='1' AND $strDomainWhere1";
                    $strHost = $this->myDBClass->getFieldData($strSQLHost);
                    $strSQLSrv = 'SELECT `' . $elem['target2'] . '` FROM `' . $elem['tableName2'] . '` ' .
                        'WHERE `id`=' . $data['idSlaveS'] . "  AND `active`='1' AND $strDomainWhere1";
                    $strService = $this->myDBClass->getFieldData($strSQLSrv);
                    if (($strHost != '') && ($strService != '') &&
                        substr_count($strDataValue, $strHost . ',' . $strService) == 0 &&
                        !in_array($data['idSlaveH'], $arrExclude, true)) {
                        $strDataValue .= $strHost . ',' . $strService . ',';
                    }
                }
            }
            $strDataValue = substr($strDataValue, 0, -1);
            if ($strDataValue == '') {
                $intReturn = 1;
            }
        } else {
            $intReturn = 1;
        }
        return $intReturn;
    }

    /**
     * @param array $arrData                    Dataset array
     * @param string $strDataValue              Data value
     * @param array $elem                       Relation data array
     * @return int                              0 = use data / 1 = skip data
     */
    private function processRelation7($arrData, &$strDataValue, $elem)
    {
        $intReturn = 1;
        // Get relation data
        $strSQLMaster = 'SELECT * FROM `' . $elem['linkTable'] . '` WHERE `idMaster`=' . $arrData['id'];
        $booReturn = $this->myDBClass->hasDataArray($strSQLMaster, $arrDataRel, $intDataCountRel);
        if ($booReturn && ($intDataCountRel != 0)) {
            // Rewrite $strDataValue with returned relation data
            $strDataValue = '';
            /** @var array $arrDataRel */
            foreach ($arrDataRel as $data) {
                $strSQL = 'SELECT host_name FROM tbl_host WHERE id=' .$data['idHost'];
                $strHost = $this->myDBClass->getFieldData($strSQL);
                $strSQL = 'SELECT service_description FROM tbl_service WHERE id=' .$data['idSlave'];
                $strService = $this->myDBClass->getFieldData($strSQL);
                $strDataValue .= $strHost . ',' . $strService . ',';
                $intReturn = 0;
            }
            $strDataValue = substr($strDataValue, 0, -1);
        }
        return $intReturn;
    }
}
