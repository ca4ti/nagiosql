<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2018 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Configuration Class
// Website   : https://sourceforge.net/projects/nagiosql/
// Date      : $LastChangedDate: 2018-04-12 22:53:39 +0200 (Thu, 12 Apr 2018) $
// Author    : $LastChangedBy: martin $
// Version   : 3.4.0
// Revision  : $LastChangedRevision: 24 $
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
    private $arrSettings         = array();     // Array includes all global settings
    private $resConnectServer    = '';          // Connection server name for FTP and SSH connections
    private $resConnectType      = 'none';      // Connection type for FTP and SSH connections
    public $resConnectId;                       // Connection id for FTP and SSH connections
    public $resSFTP;                            // SFTP ressource id

    //private $arrRelData         = "";         // Relation data
    //
    public $arrSession           = array();     // Session content
    public $strRelTable          = "";          // Relation table name
    public $intNagVersion        = 0;           // Nagios version id
    public $strPicPath           = "none";      // Picture path string
    public $intDomainId          = 0;           // Configuration domain ID
    public $strErrorMessage      = "";          // String including error messages
    public $strInfoMessage       = "";          // String including information messages

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
        $strSQL   = "SELECT `".$strConfigItem."` FROM `tbl_datadomain` WHERE `id` = ".$this->intDomainId;
        $strValue = $this->myDBClass->getFieldData($strSQL);
        if ($strValue == "") {
            $intReturn = 1;
        }
        return($intReturn);
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
        $strFileName  = $strConfigName.".cfg";
        // Get table times
        $strActive            = 0;
        $arrTimeData          = array();
        $arrTimeData['table'] = "unknown";
        // Clear status cache
        clearstatcache();
        // Get last change on dataset
        if ($strTableName == "tbl_host") {
            $strSQL1 = "SELECT DATE_FORMAT(`last_modified`,'%Y-%m-%d %H:%i:%s') FROM `tbl_host` ".
                "WHERE `host_name`='$strConfigName' AND `config_id`=".$this->intDomainId;
            $strSQL2 = "SELECT `active` FROM `tbl_host`  WHERE `host_name`='$strConfigName' ".
                "AND `config_id`=".$this->intDomainId;
            $arrTimeData['table'] = $this->myDBClass->getFieldData($strSQL1);
            $strActive            = $this->myDBClass->getFieldData($strSQL2);
        } elseif ($strTableName == "tbl_service") {
            $strSQL1 = "SELECT DATE_FORMAT(`last_modified`,'%Y-%m-%d %H:%i:%s') FROM `tbl_service` ".
                "WHERE `id`='$intDataId' AND `config_id`=".$this->intDomainId;
            $strSQL2 = "SELECT * FROM `$strTableName` WHERE `config_name`='$strConfigName' ".
                "AND `config_id`=".$this->intDomainId." AND `active`='1'";
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
                $this->getConfigValues($intConfigId, "target", $strTarget);
                // Get last change on dataset
                if ($strTableName == "tbl_host") {
                    $this->getConfigValues($intConfigId, "hostconfig", $strBaseDir);
                } elseif ($strTableName == "tbl_service") {
                    $this->getConfigValues($intConfigId, "serviceconfig", $strBaseDir);
                }
                $arrTimeData[$strTarget] = "unknown";
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
                if ((strtotime($arrTimeData['table']) > $intFileStampTemp) && ($strActive == '1')) {
                    $intTimeInfo = 1;
                }
            }
            $intItems    = count($arrTimeData) - 1;
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
        return($intReturn);
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
        $strSQL    = "SELECT `targets` FROM `tbl_datadomain` WHERE `id`=".$this->intDomainId;
        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if ($booReturn && ($intDataCount != 0)) {
            foreach ($arrData as $elem) {
                $arrConfigId[] = $elem['targets'];
            }
            $intReturn = 0;
        }
        return($intReturn);
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
        $strSQL    = "SELECT `".$strConfigKey."` FROM `tbl_configtarget` WHERE `id`=".$intConfigId;
        $strValue  = $this->myDBClass->getFieldData($strSQL);
        if ($strValue != "") {
            $intReturn = 0;
        }
        return($intReturn);
    }

    /**
     * Get last modification date of a configuration file.
     * @param int $intConfigId                  Configuration ID
     * @param string $strFile                   Configuration file name
     * @param string $strBaseDir                Base directory with configuration file
     * @param int $intFileStamp                 File timestamp (by reference)
     * @param string $strTimeData               Human readable string of file time stamp (by reference)
     * @return int                              0 = successful / 1 = error
     */
    public function getFileDate($intConfigId, $strFile, $strBaseDir, &$intFileStamp, &$strTimeData)
    {
        $strMethod  = 1;
        $intReturn  = 0;
        // Get configuration file data
        $this->getConfigValues($intConfigId, "method", $strMethod);
        $strTimeData  = "unknown";
        $intFileStamp = -1;
        // Lokal file system
        if (($strMethod == 1) && (file_exists($strBaseDir."/".$strFile))) {
            $intFileStamp        = filemtime($strBaseDir."/".$strFile);
            $strTimeData = date("Y-m-d H:i:s", $intFileStamp);
        } elseif ($strMethod == 2) { // FTP file system
            // Check connection
            $intReturn = $this->getFTPConnection($intConfigId);
            if ($intReturn == 0) {
                $intFileStamp = ftp_mdtm($this->resConnectId, $strBaseDir . "/" . $strFile);
                if ($intFileStamp != -1) {
                    $strTimeData = date("Y-m-d H:i:s", $intFileStamp);
                }
            }
        } elseif ($strMethod == 3) { // SSH file system
            // Check connection
            $intReturn = $this->getSSHConnection($intConfigId);
            // Check file date
            $strFilePath = str_replace('//', '/', $strBaseDir.'/'.$strFile);
            $strCommand  = 'ls '.$strFilePath;
            $arrResult   =  array();
            if (($intReturn == 0) && ($this->sendSSHCommand($strCommand, $arrResult) == 0)) {
                $arrInfo      = ssh2_sftp_stat($this->resSFTP, $strFilePath);
                $intFileStamp = $arrInfo['mtime'];
                if ($intFileStamp != -1) {
                    $strTimeData = date("Y-m-d H:i:s", $intFileStamp);
                }
            }
        }
        return($intReturn);
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
        // Already connected?
        if (!isset($this->resConnectId) || !is_resource($this->resConnectId) || ($this->resConnectType != "FTP")) {
            // Define variables
            $booLogin = false;
            $this->getConfigValues($intConfigID, "server", $strServer);
            $this->getConfigValues($intConfigID, "ftp_secure", $intFtpSecure);
            // Set up basic connection
            $this->resConnectServer = $strServer;
            $this->resConnectType = "FTP";
            // Secure FTP?
            if ($intFtpSecure == 1) {
                $this->resConnectId = ftp_ssl_connect($strServer);
            } else {
                $this->resConnectId = ftp_connect($strServer);
            }
            // Login with username and password
            if ($this->resConnectId) {
                $this->getConfigValues($intConfigID, "user", $strUser);
                $this->getConfigValues($intConfigID, "password", $strPasswd);
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
                    " " . $strServer);
                $this->processClassMessage(translate('Connection to remote system failed (FTP connection):') .
                    " <b>" . $strServer . "</b>::", $this->strErrorMessage);
                if (isset($arrError) && ($arrError['message'] != "")) {
                    $this->processClassMessage($arrError['message'] . "::", $this->strErrorMessage);
                }
            }
        }
        return($intReturn);
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
        $intReturn = 0;
        // Already connected?
        if (!isset($this->resConnectId) || !is_resource($this->resConnectId) || ($this->resConnectType != "SSH")) {
            // SSH Possible
            if (!function_exists('ssh2_connect')) {
                $this->processClassMessage(translate('SSH module not loaded!')."::", $this->strErrorMessage);
                return(1);
            }
            // Define variables
            $booLogin = false;
            $this->getConfigValues($intConfigID, "server", $strServer);
            $this->resConnectServer = $strServer;
            $this->resConnectType   = 'SSH';
            $intErrorReporting  = error_reporting();
            error_reporting(0);
            $this->resConnectId = ssh2_connect($strServer);
            $arrError = error_get_last();
            error_reporting($intErrorReporting);
            // Check connection
            if ($this->resConnectId) {
                // Login with username and password
                $this->getConfigValues($intConfigID, "user", $strUser);
                $this->getConfigValues($intConfigID, "password", $strPasswd);
                $this->getConfigValues($intConfigID, "ssh_key_path", $strSSHKeyPath);
                if ($strSSHKeyPath != '') {
                    $strPublicKey = str_replace('//', '/', $strSSHKeyPath.'/id_rsa.pub');
                    $strPrivatKey = str_replace('//', '/', $strSSHKeyPath.'/id_rsa');
                    // Check if ssh key file are readable
                    if (!file_exists($strPublicKey) || !is_readable($strPublicKey)) {
                        $this->myDataClass->writeLog(translate('SSH public key does not exist or is not readable')." ".
                            $strSSHKeyPath.$strPublicKey);
                        $this->processClassMessage(translate('SSH public key does not exist or is not readable')." <b>".
                            $strSSHKeyPath.$strPublicKey."</b>::", $this->strErrorMessage);
                        $intReturn = 1;
                    }
                    if (!file_exists($strPrivatKey) || !is_readable($strPrivatKey)) {
                        $this->myDataClass->writeLog(translate('SSH private key does not exist or is not readable')." ".
                            $strPrivatKey);
                        $this->processClassMessage(translate('SSH private key does not exist or is not readable')." ".
                            $strPrivatKey."::", $this->strErrorMessage);
                        $intReturn = 1;
                    }
                    $intErrorReporting  = error_reporting();
                    error_reporting(0);
                    if ($strPasswd == "") {
                        $booLogin = ssh2_auth_pubkey_file(
                            $this->resConnectId,
                            $strUser,
                            $strSSHKeyPath."/id_rsa.pub",
                            $strSSHKeyPath."/id_rsa"
                        );
                    } else {
                        $booLogin = ssh2_auth_pubkey_file(
                            $this->resConnectId,
                            $strUser,
                            $strSSHKeyPath."/id_rsa.pub",
                            $strSSHKeyPath."/id_rsa",
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
                    $strPasswordNote = "If you are using ssh2 with user/password - you have to enable ".
                        "PasswordAuthentication in your sshd_config";
                    error_reporting($intErrorReporting);
                }
            } else {
                $this->myDataClass->writeLog(translate('Connection to remote system failed (SSH2 connection):').
                    " ".$strServer);
                $this->processClassMessage(translate('Connection to remote system failed (SSH2 connection):').
                    " <b>".$strServer."</b>::", $this->strErrorMessage);
                if ($arrError['message'] != "") {
                    $this->processClassMessage($arrError['message']."::", $this->strErrorMessage);
                }
                $intReturn = 1;
            }
            // Check connection
            if ((!$this->resConnectId) || (!$booLogin)) {
                $this->myDataClass->writeLog(translate('Connection to remote system failed (SSH2 connection):').
                    " ".$strServer);
                $this->processClassMessage(translate('Connection to remote system failed (SSH2 connection):')
                    ." ".$strServer."::", $this->strErrorMessage);
                if ($arrError['message'] != "") {
                    $this->processClassMessage($arrError['message']."::", $this->strErrorMessage);
                }
                if (isset($strPasswordNote)) {
                    $this->processClassMessage($strPasswordNote."::", $this->strErrorMessage);
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
        return($intReturn);
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
        if (is_resource($this->resConnectId)) {
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
                    } elseif (substr_count($strLine, "__END__") != 1) {
                        $arrResult[] = $strLine;
                        $intReturn   = 0;
                    } elseif (substr_count($strLine, "__END__") == 1) {
                        $booBreak = true;
                    }
                    $intCount2++;
                    $arrStatus = stream_get_meta_data($resStream);
                } while ($resStream && !(feof($resStream)) && ($intCount1 <= 10) && ($intCount2 <= $intLines) &&
                ($arrStatus['timed_out'] != true) && $booBreak == false);
                fclose($resStream);
                // Close SSH connection because of timing problems
                unset($this->resConnectId);
                //sleep(1);
            }
        }
        return($intReturn);
    }

    /**
     * Merge message strings and check for duplicate messages
     * @param string $strNewMessage             New message to add
     * @param string $strOldMessage             Modified message string (by reference)
     */
    public function processClassMessage($strNewMessage, &$strOldMessage)
    {
        $strNewMessage = str_replace("::::", "::", $strNewMessage);
        if (($strOldMessage != "") && ($strNewMessage != "") && (substr_count($strOldMessage, $strNewMessage) == 0)) {
            $strOldMessage .= $strNewMessage;
        } elseif ($strOldMessage == "") {
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
        $strSQL    = "SELECT `targets` FROM `tbl_datadomain` WHERE `id`=".$this->intDomainId;
        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if ($booReturn && ($intDataCount != 0)) {
            foreach ($arrData as $elem) {
                $arrConfigId[] = $elem['targets'];
            }
            $intReturn = 0;
        }
        return($intReturn);
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
            case "host":
                $this->getConfigData($intConfigID, "hostconfig", $strConfigDir);
                $this->getConfigData($intConfigID, "hostbackup", $strBackupDir);
                break;
            case "service":
                $this->getConfigData($intConfigID, "serviceconfig", $strConfigDir);
                $this->getConfigData($intConfigID, "servicebackup", $strBackupDir);
                break;
            case "basic":
                $this->getConfigData($intConfigID, "basedir", $strConfigDir);
                $this->getConfigData($intConfigID, "backupdir", $strBackupDir);
                break;
            case "nagiosbasic":
                $this->getConfigData($intConfigID, "nagiosbasedir", $strConfigDir);
                $this->getConfigData($intConfigID, "backupdir", $strBackupDir);
                break;
            default:
                $intReturn = 1;
        }
        if ($intReturn == 0) {
            // Variable definition
            $intMethod          = 1;
            $strDate            = date("YmdHis", time());
            $strSourceFile      = $strConfigDir."/".$strName;
            $strDestinationFile = $strBackupDir."/".$strName."_old_".$strDate;
            $booRetVal          = false;
            // Get connection method
            $this->getConfigData($intConfigID, "method", $intMethod);
            // Local file system
            if ($intMethod == 1) {
                // Save configuration file
                if (file_exists($strSourceFile) && is_writable($strBackupDir) &&  is_writable($strConfigDir)) {
                    copy($strSourceFile, $strDestinationFile);
                    unlink($strSourceFile);
                } elseif (!file_exists($strSourceFile)) {
                    $this->processClassMessage(translate('Cannot backup the old file because the source file is '
                            . 'missing - source file: ') . $strSourceFile . "::", $this->strErrorMessage);
                    $intReturn = 1;
                } else {
                    $this->processClassMessage(translate('Cannot backup the old file because the permissions are '
                            .'wrong - destination file: ').$strDestinationFile."::", $this->strErrorMessage);
                    $intReturn = 1;
                }
            } elseif ($intMethod == 2) { // Remote file (FTP)
                // Check connection
                $intReturn = $this->getFTPConnection($intConfigID);
                if ($intReturn == 0) {
                    $strSourceFile      = str_replace("//", "/", $strSourceFile);
                    $strDestinationFile = str_replace("//", "/", $strDestinationFile);
                    // Save configuration file
                    $intFileStamp = ftp_mdtm($this->resConnectId, $strSourceFile);
                    if ($intFileStamp > -1) {
                        $intErrorReporting = error_reporting();
                        error_reporting(0);
                        $booRetVal = ftp_rename($this->resConnectId, $strSourceFile, $strDestinationFile);
                        error_reporting($intErrorReporting);
                    } else {
                        $this->processClassMessage(translate('Cannot backup the old file because the source file is '
                                .'missing (remote FTP) - source file: '). $strSourceFile."::", $this->strErrorMessage);
                        $intReturn = 1;
                    }
                }
                if (($booRetVal == false) && ($intReturn == 0)) {
                    $this->processClassMessage(translate('Cannot backup the old file because the permissions are '
                            .'wrong (remote FTP) - destination file: ').$strDestinationFile."::", $this->strErrorMessage);
                    $intReturn = 1;
                }
            } elseif ($intMethod == 3) { // Remote file (SFTP)
                // Check connection
                $intReturn = $this->getSSHConnection($intConfigID);
                // Save configuration file
                $arrResult          = array();
                $strSourceFile      = str_replace("//", "/", $strSourceFile);
                $strDestinationFile = str_replace("//", "/", $strDestinationFile);
                $strCommand         = 'ls '.$strSourceFile;
                if (($intReturn == 0) && ($this->sendSSHCommand($strCommand, $arrResult) == 0)) {
                    if (isset($arrResult[0]) && $arrResult[0] == $strSourceFile) {
                        $arrInfo      = ssh2_sftp_stat($this->resSFTP, $strSourceFile);
                        if ($arrInfo['mtime'] > -1) {
                            $booRetVal  = ssh2_sftp_rename($this->resSFTP, $strSourceFile, $strDestinationFile);
                        }
                    } else {
                        $this->processClassMessage(translate('Cannot backup the old file because the source file is '
                                .'missing (remote SFTP) - source file: '). $strSourceFile."::", $this->strErrorMessage);
                        $intReturn = 1;
                    }
                }
                if (($booRetVal == false) && ($intReturn == 0)) {
                    $this->processClassMessage(translate('Cannot backup the old file because the permissions are '
                            .'wrong (remote SFTP) - destination file: ').$strDestinationFile."::", $this->strErrorMessage);
                    $intReturn = 1;
                }
            }
        }
        return($intReturn);
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
        $this->getConfigData($intConfigID, "method", $intMethod);
        // Local file system
        if ($intMethod == 1) {
            // Save configuration file
            if (file_exists($strFileName) && is_writable($strFileName)) {
                unlink($strFileName);
            } elseif (!file_exists($strFileName)) {
                $this->processClassMessage(translate('Cannot delete the file (file does not exist)!').'::'.
                    $strFileName."::", $this->strErrorMessage);
                $intReturn = 1;
            } elseif (!is_writable($strFileName)) {
                $this->processClassMessage(translate('Cannot delete the file (wrong permissions)!').'::'.
                    $strFileName."::", $this->strErrorMessage);
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
                            . 'FTP)!')."::", $this->strErrorMessage);
                    $intReturn = 1;
                }
            }
            if ($booRetVal == false) {
                $this->processClassMessage(translate('Cannot delete file because the permissions are wrong '
                        . '(remote FTP)!')."::", $this->strErrorMessage);
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
                            . 'SFTP)!')."::", $this->strErrorMessage);
                    $intReturn = 1;
                }
            }
            if (($intReturn == 0) && ($booRetVal == false)) {
                $this->processClassMessage(translate('Cannot delete file because the permissions are wrong '
                        . '(remote SFTP)!')."::", $this->strErrorMessage);
                $intReturn = 1;
            }
        }
        return($intReturn);
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
        $strSQL   = "SELECT `".$strConfigItem."` FROM `tbl_configtarget` WHERE `id` = ".$intConfigId;
        $strValue = $this->myDBClass->getFieldData($strSQL);
        if ($strValue != "") {
            $intReturn = 0;
        }
        return($intReturn);
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
            $resFile = fopen($strPath, 'a');
            if ($resFile) {
                $intReturnFile = 0;
            }
        } else {
            $intReturnFile = 0;
        }
        if (is_file($strPath)) {
            $strDirectory = dirname($strPath);
        } else {
            $strDirectory = $strPath;
        }
        $strFile = $strDirectory.'/'.uniqid(mt_rand()).'.tmp';
        // Check writing in directory directly
        if (is_dir($strDirectory) && is_writeable($strDirectory)) {
            $resFile = fopen($strFile, 'w');
            if ($resFile) {
                $intReturnDir = 0;
                unlink($strFile);
            }
        } elseif (!is_dir($strDirectory)) {
            $intReturnDir = 0;
        }
        if (($intReturnDir == 0) && ($intReturnFile == 0)) {
            $intReturn = 0;
        }
        return($intReturn);
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
        $this->getConfigData($intConfigID, "method", $intMethod);
        if ($intMethod == 2) {
            // Check connection
            $intReturn = $this->getFTPConnection($intConfigID);
            if (($intReturn == 0) && ($intDirection == 0)) {
                $intErrorReporting = error_reporting();
                error_reporting(0);
                if (!ftp_get($this->resConnectId, $strFileLocal, $strFileRemote, FTP_ASCII)) {
                    $this->processClassMessage(translate('Cannot get the remote file (it does not exist or is not '
                            . 'readable) - remote file: '). $strFileRemote."::", $this->strErrorMessage);
                    $intReturn = 1;
                }
                error_reporting($intErrorReporting);
            } elseif (($intReturn == 0) && ($intDirection == 1)) {
                $intErrorReporting = error_reporting();
                error_reporting(0);
                if (!ftp_put($this->resConnectId, $strFileRemote, $strFileLocal, FTP_ASCII)) {
                    $this->processClassMessage(translate('Cannot write the remote file (remote file is not writeable)'
                            . '- remote file: ').$strFileRemote."::", $this->strErrorMessage);
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
                    if (($this->sendSSHCommand('ls ' . $strFileRemote, $arrTemp) != 0)) {
                        $this->processClassMessage(translate('Cannot get the remote file (it does not exist or is not '
                                . 'readable) - remote file: ') .$strFileRemote. "::", $this->strErrorMessage);
                    } else {
                        $this->processClassMessage(translate('Remote file is not readable - remote file: ')
                            . $strFileRemote. "::", $this->strErrorMessage);
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
                                .' - remote file: '). $strFileRemote . "::", $this->strErrorMessage);
                        $intReturn = 1;
                    }
                    error_reporting($intErrorReporting);
                } else {
                    $this->processClassMessage(translate('Cannot copy a local file to remote because the local file '.
                            'does not exist or is not readable - local file: ').
                        $strFileLocal . "::", $this->strErrorMessage);
                    $intReturn = 1;
                }
            }
        }
        return($intReturn);
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
        while (substr($strSourceDir, -1) == "/" or substr($strSourceDir, -1) == "\\") {
            $strSourceDir = substr($strSourceDir, 0, -1);
        }
        $resHandle = opendir($strSourceDir);
        if ($resHandle === false) {
            if ($this->intDomainId != 0) {
                $strErrorMessage .= translate('Could not open directory').": ".$strSourceDir;
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
            foreach ($arrDir as $file) {
                if (!preg_match("/^\.{1,2}/", $file) && strlen($file)) {
                    if (is_dir($strSourceDir."/".$file)) {
                        $this->storeDirToArray(
                            $strSourceDir."/".$file,
                            $strIncPattern,
                            $strExcPattern,
                            $arrOutput,
                            $strErrorMessage
                        );
                    } else {
                        if (preg_match("/".$strIncPattern."/", $file) && (($strExcPattern == "") ||
                                !preg_match("/".$strExcPattern."/", $file))) {
                            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                                $strSourceDir=str_replace("/", "\\", $strSourceDir);
                                $arrOutput [] = $strSourceDir."\\".$file;
                            } else {
                                $arrOutput [] = $strSourceDir."/".$file;
                            }
                        }
                    }
                }
            }
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    //  Function: Get last change date of table and config files
    ///////////////////////////////////////////////////////////////////////////////////////////
    //
    //  Determines the dates of the last data table change and the last modification to the configuration files
    //
    //  Parameter:      $strTableName           Name of the data table
    //
    //  Return values:  0 = successful
    //                  1 = error
    //                  Status message is stored in message class variables
    //
    //                  $arrTimeData            Array with time data of table and all config files
    //                  $strCheckConfig         Information string (text message)
    //
    ///////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Determines the dates of the last data table change and the last modification to the configuration files
     * @param string $strTableName              Name of the data table
     * @param array $arrTimeData                Array with time data of table and all config files
     * @param string $strCheckConfig            Information string (text message)
     * @return int                              0 = successful / 1 = error
     *                                          Status message is stored in message class variables
     */
    public function lastModifiedFile($strTableName, &$arrTimeData, &$strCheckConfig) : int
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
        $arrTimeData['table'] = "unknown";
        // Clear status cache
        clearstatcache();
        $intRetVal1 = $this->getDomainData("enable_common", $intEnableCommon);
        // Get last change of date table
        if ($intRetVal1 == 0) {
            $strSQLAdd = '';
            if ($intEnableCommon == 1) {
                $strSQLAdd = "OR `domainId`=0";
            }
            $strSQL = "SELECT `updateTime` FROM `tbl_tablestatus` "
                . "WHERE (`domainId`=".$this->intDomainId." $strSQLAdd) AND `tableName`='".$strTableName."' "
                . "ORDER BY `updateTime` DESC LIMIT 1";
            $booReturn = $this->myDBClass->hasSingleDataset($strSQL, $arrDataset);
            if ($booReturn && isset($arrDataset['updateTime'])) {
                $arrTimeData['table'] = $arrDataset['updateTime'];
            } else {
                $strSQL = "SELECT `last_modified` FROM `".$strTableName."` "
                    . "WHERE `config_id`=".$this->intDomainId." ORDER BY `last_modified` DESC LIMIT 1";
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
                $this->getConfigData($intConfigId, "target", $strTarget);
                $this->getConfigData($intConfigId, "basedir", $strBaseDir);
                // Get time data
                $intReturn = $this->getFileDate(
                    $intConfigId,
                    $strFileName,
                    $strBaseDir,
                    $intFileStampTemp,
                    $arrTimeData[$strTarget]
                );
                if ($intFileStampTemp != 0) {
                    if (strtotime($arrTimeData['table']) > $intFileStampTemp) {
                        $strCheckConfig = translate('Warning: configuration file is out of date!');
                    }
                }
                if ($arrTimeData[$strTarget] == 'unknown') {
                    $strCheckConfig = translate('Warning: configuration file is out of date!');
                }
            }
        } else {
            $strCheckConfig = translate('Warning: no configuration target defined!');
        }
        return($intReturn);
    }

    // PRIVATE functions

    /**
     * Determines the configuration data for each database table
     * @return array                            filename (configuration file name)
     *                                          order_field (database order field)
     */
    private function getConfData()
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
        return($arrConfData);
    }
}
