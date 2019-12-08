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
// Component : Content Class
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Class: Common content functions
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Includes all functions used to display the application data
//
// Name: nagcontent
//
///////////////////////////////////////////////////////////////////////////////////////////////
namespace functions;

class NagContentClass
{
    // Define class variables
    private $arrSettings         = array();  // Array includes all global settings
    //
    public $arrSession           = array();  // Session content
    public $arrDescription       = array();  // Text values from fieldvars.php
    public $intLimit             = 15;       // Data limit value
    public $intDomainId          = 0;        // Configuration domain ID
    public $intSortBy            = -1;       // Sort by field id
    public $intGlobalWriteAccess = -1;       // Global write access id
    public $intVersion           = 0;        // Nagios version id
    public $intGroupAdm          = 0;        // Group admin enabled/disabled
    public $intWriteAccessId     = 0;        // Write access id
    public $strTableName         = '';       // Data table name
    public $strSearchSession     = '';       // Search session name
    public $strErrorMessage      = '';       // String including error messages
    public $strSortDir           = 'ASC';    // SQL sort direction (ASC/DESC)
    public $strBrowser           = '';       // Browser string

    // Class includes
    /** @var MysqliDbClass */
    public $myDBClass;                       // Database class reference
    /** @var NagConfigClass */
    public $myConfigClass;                   // NagiosQL configuration class object
    /** @var NagVisualClass */
    public $myVisClass;                      // NagiosQL visual class object

    /**
     * NagContentClass constructor.
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
        $this->arrSession    = $arrSession;
    }

    /**
     * Data list view - form initialization
     * @param \HTML_Template_IT $objTemplate    Form template object
     */
    public function listViewInit($objTemplate)
    {
        // Language text replacements from fieldvars.php file
        foreach ($this->arrDescription as $elem) {
            $objTemplate->setVariable($elem['name'], $elem['string']);
        }
        // Some single replacements
        $objTemplate->setVariable('LIMIT', $this->intLimit);
        $objTemplate->setVariable('ACTION_MODIFY', filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING));
        $objTemplate->setVariable('TABLE_NAME', $this->strTableName);
        if (isset($this->arrSession['search'][$this->strSearchSession])) {
            $objTemplate->setVariable('DAT_SEARCH', $this->arrSession['search'][$this->strSearchSession]);
        }
        $objTemplate->setVariable('MAX_ID', '0');
        $objTemplate->setVariable('MIN_ID', '0');
    }

    /**
     * Data list view - value insertions
     * @param \HTML_Template_IT $objTemplate    Form template object
     * @param array $arrData                    Database values
     * @param int $intDLCount1                  Total count of data lines for one page
     * @param int $intDLCount2                  Total count of data lines (all data)
     * @param string $strField1                 Field name for data field 1
     * @param string $strField2                 Field name for data field 2
     * @param int $intLimit                     Actual data char limit for field 2
     */
    public function listData(
        $objTemplate,
        $arrData,
        $intDLCount1,
        $intDLCount2,
        $strField1,
        $strField2,
        $intLimit = 0
    ) {
        // Template block names
        $strTplPart = 'datatable';
        $strTplRow  = 'datarow';
        if ($this->strTableName == 'tbl_host') {
            $strTplPart = 'datatablehost';
            $strTplRow  = 'datarowhost';
        }
        if ($this->strTableName == 'tbl_service') {
            $strTplPart = 'datatableservice';
            $strTplRow  = 'datarowservice';
        }
        if (($this->strTableName == 'tbl_user') || ($this->strTableName == 'tbl_group') ||
            ($this->strTableName == 'tbl_datadomain') || ($this->strTableName == 'tbl_configtarget')) {
            $strTplPart = 'datatablecommon';
            $strTplRow  = 'datarowcommon';
        }
        // Some single replacements
        $objTemplate->setVariable('IMAGE_PATH_HEAD', $this->arrSettings['path']['base_url']. 'images/');
        $objTemplate->setVariable('CELLCLASS_L', 'tdlb');
        $objTemplate->setVariable('CELLCLASS_M', 'tdmb');
        $objTemplate->setVariable('DISABLED', 'disabled');
        $objTemplate->setVariable('DATA_FIELD_1', translate('No data'));
        $objTemplate->setVariable('DATA_FIELD_2', '&nbsp;');
        $objTemplate->setVariable('DATA_REGISTERED', '&nbsp;');
        $objTemplate->setVariable('DATA_ACTIVE', '&nbsp;');
        $objTemplate->setVariable('DATA_FILE', '&nbsp;');
        $objTemplate->setVariable('PICTURE_CLASS', 'elementHide');
        $objTemplate->setVariable('DOMAIN_SPECIAL', '&nbsp;');
        $objTemplate->setVariable('SORT_BY', $this->intSortBy);
        // Inserting data values
        if ($intDLCount1 != 0) {
            $intMinID = 0;
            $intMaxID = 0;
            for ($i=0; $i<$intDLCount1; $i++) {
                // Get biggest and smalest value
                if ($i == 0) {
                    $intMinID = $arrData[$i]['id'];
                    $intMaxID = $arrData[$i]['id'];
                }
                if ($arrData[$i]['id'] < $intMinID) {
                    $intMinID = $arrData[$i]['id'];
                }
                if ($arrData[$i]['id'] > $intMaxID) {
                    $intMaxID = $arrData[$i]['id'];
                }
                $objTemplate->setVariable('MAX_ID', $intMaxID);
                $objTemplate->setVariable('MIN_ID', $intMinID);
                // Line colours
                $strClassL = 'tdld';
                $strClassM = 'tdmd';
                if ($i%2 == 1) {
                    $strClassL = 'tdlb';
                    $strClassM = 'tdmb';
                }
                if (isset($arrData[$i]['register']) && ($arrData[$i]['register'] == 0)) {
                    $strRegister = translate('No');
                } else {
                    $strRegister = translate('Yes');
                }
                if ($arrData[$i]['active']   == 0) {
                    $strActive     = translate('No');
                } else {
                    $strActive    = translate('Yes');
                }
                // Get file date for hosts and services
                $intTimeInfo = 0;
                $arrTimeData = array();
                if ($this->strTableName == 'tbl_host') {
                    $intReturn = $this->myConfigClass->lastModifiedDir(
                        $this->strTableName,
                        $arrData[$i]['host_name'],
                        $arrData[$i]['id'],
                        $arrTimeData,
                        $intTimeInfo
                    );
                    if ($intReturn == 1) {
                        $this->strErrorMessage = $this->myConfigClass->strErrorMessage;
                    }
                }
                if ($this->strTableName == 'tbl_service') {
                    $intReturn = $this->myConfigClass->lastModifiedDir(
                        $this->strTableName,
                        $arrData[$i]['config_name'],
                        $arrData[$i]['id'],
                        $arrTimeData,
                        $intTimeInfo
                    );
                    if ($intReturn == 1) {
                        $this->strErrorMessage = $this->myConfigClass->strErrorMessage;
                    }
                }
                // Set datafields
                foreach ($this->arrDescription as $elem) {
                    $objTemplate->setVariable($elem['name'], $elem['string']);
                }
                if ($arrData[$i][$strField1] == '') {
                    $arrData[$i][$strField1] = 'NOT DEFINED - ' .$arrData[$i]['id'];
                }
                $objTemplate->setVariable('DATA_FIELD_1', htmlentities($arrData[$i][$strField1], ENT_COMPAT, 'UTF-8'));
                $objTemplate->setVariable('DATA_FIELD_1S', addslashes(htmlentities(
                    $arrData[$i][$strField1],
                    ENT_COMPAT,
                    'UTF-8'
                )));
                if ($strField2 == 'process_field') {
                    $arrData[$i]['process_field'] = $this->processField($arrData[$i], $this->strTableName);
                } else {
                    $objTemplate->setVariable('DATA_FIELD_2S', addslashes(htmlentities(
                        $arrData[$i][$strField2],
                        ENT_COMPAT,
                        'UTF-8'
                    )));
                }
                if ($intLimit != 0) {
                    if (\strlen($arrData[$i][$strField2]) > $intLimit) {
                        $strAdd = ' ...';
                    } else {
                        $strAdd = '';
                    }
                    $objTemplate->setVariable('DATA_FIELD_2', htmlentities(substr(
                            $arrData[$i][$strField2],
                            0,
                            $intLimit
                        ), ENT_COMPAT, 'UTF-8').$strAdd);
                } else {
                    $objTemplate->setVariable('DATA_FIELD_2', htmlentities(
                        $arrData[$i][$strField2],
                        ENT_COMPAT,
                        'UTF-8'
                    ));
                }
                $objTemplate->setVariable('DATA_REGISTERED', $strRegister);
                if (substr_count($this->strTableName, 'template') != 0) {
                    $objTemplate->setVariable('DATA_REGISTERED', '-');
                }
                $objTemplate->setVariable('DATA_ACTIVE', $strActive);
                $objTemplate->setVariable('DATA_FILE', '<span class="redmessage">'.translate('out-of-date').'</span>');
                if ($intTimeInfo == 4) {
                    $objTemplate->setVariable('DATA_FILE', translate('no target'));
                }
                if ($intTimeInfo == 3) {
                    $objTemplate->setVariable('DATA_FILE', '<span class="greenmessage">'.translate('missed').'</span>');
                }
                if ($intTimeInfo == 2) {
                    $objTemplate->setVariable('DATA_FILE', '<span class="redmessage">'.translate('missed').'</span>');
                }
                if ($intTimeInfo == 0) {
                    $objTemplate->setVariable('DATA_FILE', translate('up-to-date'));
                }
                $objTemplate->setVariable('LINE_ID', $arrData[$i]['id']);
                $objTemplate->setVariable('CELLCLASS_L', $strClassL);
                $objTemplate->setVariable('CELLCLASS_M', $strClassM);
                $objTemplate->setVariable('IMAGE_PATH', $this->arrSettings['path']['base_url']. 'images/');
                $objTemplate->setVariable('PICTURE_CLASS', 'elementShow');
                $objTemplate->setVariable('DOMAIN_SPECIAL', '');
                $objTemplate->setVariable('DISABLED', '');
                // Disable common domain objects
                if (isset($arrData[$i]['config_id'])) {
                    if ($arrData[$i]['config_id'] != $this->intDomainId) {
                        $objTemplate->setVariable('PICTURE_CLASS', 'elementHide');
                        $objTemplate->setVariable('DOMAIN_SPECIAL', ' [common]');
                        $objTemplate->setVariable('DISABLED', 'disabled');
                    } else {
                        // Inactive items should not be written/downloaded
                        if ($arrData[$i]['active'] == 0) {
                            $objTemplate->setVariable('ACTIVE_CONTROL', 'elementHide');
                        }
                    }
                }
                // Check access rights for list objects
                if (isset($arrData[$i]['access_group'])) {
                    if ($this->myVisClass->checkAccountGroup($arrData[$i]['access_group'], 'write') != 0) {
                        $objTemplate->setVariable('LINE_CONTROL', 'elementHide');
                    }
                } else {
                    if ($this->intGlobalWriteAccess != 0) {
                        $objTemplate->setVariable('LINE_CONTROL', 'elementHide');
                    }
                }
                // Check global access rights for list objects
                if ($this->intGlobalWriteAccess != 0) {
                    $objTemplate->setVariable('LINE_CONTROL', 'elementHide');
                }
                $objTemplate->parse($strTplRow);
            }
        } else {
            $objTemplate->setVariable('IMAGE_PATH', $this->arrSettings['path']['base_url']. 'images/');
            $objTemplate->parse($strTplRow);
        }
        $objTemplate->setVariable('BUTTON_CLASS', 'elementShow');
        if ($this->intDomainId == 0) {
            $objTemplate->setVariable('BUTTON_CLASS', 'elementHide');
        }
        // Check access rights for adding new objects
        if ($this->intGlobalWriteAccess != 0) {
            $objTemplate->setVariable('ADD_CONTROL', 'disabled="disabled"');
        }
        // Show page numbers
        $objTemplate->setVariable('PAGES', $this->myVisClass->buildPageLinks(filter_input(
            INPUT_SERVER,
            'PHP_SELF',
            FILTER_SANITIZE_STRING
        ), $intDLCount2, $this->intLimit, $this->intSortBy, $this->strSortDir));
        $objTemplate->parse($strTplPart);
        $objTemplate->show($strTplPart);
    }

    /**
     * Display information messages
     * @param \HTML_Template_IT $objTemplate    Form template object
     * @param string $strErrorMessage           Error messages
     * @param string $strInfoMessage            Information messages
     * @param string $strConsistMessage         Consistency messages
     * @param array $arrTimeData                Time data array
     * @param string $strTimeInfoString         Time information message
     * @param int $intNoTime                    Status value for showing time information (0 = show time)
     */
    public function showMessages(
        $objTemplate,
        $strErrorMessage,
        $strInfoMessage,
        $strConsistMessage,
        $arrTimeData,
        $strTimeInfoString,
        $intNoTime = 0
    ) {
        // Display info messages
        if ($strInfoMessage != '') {
            $objTemplate->setVariable('INFOMESSAGE', $strInfoMessage);
            $objTemplate->parse('infomessage');
        }
        // Display error messages
        if ($strErrorMessage != '') {
            $objTemplate->setVariable('ERRORMESSAGE', $strErrorMessage);
            $objTemplate->parse('errormessage');
        }
        // Display time informations
        if (($this->intDomainId != 0) && ($intNoTime == 0)) {
            foreach ($arrTimeData as $key => $elem) {
                if ($key == 'table') {
                    $objTemplate->setVariable('LAST_MODIFIED_TABLE', translate('Last database update:'). ' <b>' .
                        $elem. '</b>');
                    $objTemplate->parse('table_time');
                } else {
                    $objTemplate->setVariable('LAST_MODIFIED_FILE', translate('Last file change of the configuration '.
                            'target '). ' <i>' .$key. '</i>: <b>' .$elem. '</b>');
                    $objTemplate->parse('file_time');
                }
            }
            if ($strTimeInfoString != '') {
                $objTemplate->setVariable('MODIFICATION_STATUS', $strTimeInfoString);
                $objTemplate->parse('modification_status');
            }
        }
        // Display consistency messages
        if ($strConsistMessage != '') {
            $objTemplate->setVariable('CONSIST_USAGE', $strConsistMessage);
            $objTemplate->parse('consistency');
        }
        $objTemplate->parse('msgfooter');
        $objTemplate->show('msgfooter');
    }

    /**
     * Display page footer
     * @param \HTML_Template_IT $objTemplate    Form template object
     * @param string $setFileVersion            NagiosQL version
     */
    public function showFooter($objTemplate, $setFileVersion)
    {
        $objTemplate->setVariable('VERSION_INFO', "<a href='https://sourceforge.net/projects/nagiosql/' ".
            "target='_blank'>NagiosQL</a> $setFileVersion");
        $objTemplate->parse('footer');
        $objTemplate->show('footer');
    }

    /**
     * Single data form initialization
     * @param \HTML_Template_IT $objTemplate    Form template object
     * @param string $strChbFields              Comma separated string of checkbox value names
     */
    public function addFormInit($objTemplate, $strChbFields = '')
    {
        // Language text replacements from fieldvars.php file
        foreach ($this->arrDescription as $elem) {
            $objTemplate->setVariable($elem['name'], $elem['string']);
        }
        // Some single replacements
        $objTemplate->setVariable('ACTION_INSERT', filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING));
        $objTemplate->setVariable('IMAGE_PATH', $this->arrSettings['path']['base_url']. 'images/');
        $objTemplate->setVariable('DOCUMENT_ROOT', $this->arrSettings['path']['base_url']);
        $objTemplate->setVariable('ACT_CHECKED', 'checked');
        $objTemplate->setVariable('REG_CHECKED', 'checked');
        $objTemplate->setVariable('MODUS', 'insert');
        $objTemplate->setVariable('VERSION', $this->intVersion);
        $objTemplate->setVariable('LIMIT', $this->intLimit);
        $objTemplate->setVariable('RELATION_CLASS', 'elementHide');
        $objTemplate->setVariable('IFRAME_SRC', $this->arrSettings['path']['base_url']. 'admin/commandline.php');
        // Some conditional replacements
        if ($this->strBrowser != 'msie') {
            $objTemplate->setVariable('MSIE_DISABLED', 'disabled="disabled"');
        }
        if ($this->intGroupAdm == 0) {
            $objTemplate->setVariable('RESTRICT_GROUP_ADMIN', 'class="elementHide"');
        }
        if ($this->arrSettings['common']['seldisable'] == 0) {
            $objTemplate->setVariable('MSIE_DISABLED', '');
        }
        if ($this->arrSettings['common']['tplcheck'] == 0) {
            $objTemplate->setVariable('CHECK_BYPASS', 'return true;');
            $objTemplate->setVariable('CHECK_BYPASS_NEW', '1');
        } else {
            $objTemplate->setVariable('CHECK_BYPASS_NEW', '0');
        }
        // Some replacements based on nagios version
        if ($this->intVersion < 3) {
            $objTemplate->setVariable('VERSION_20_VISIBLE', 'elementShow');
            $objTemplate->setVariable('VERSION_30_VISIBLE', 'elementHide');
            $objTemplate->setVariable('VERSION_40_VISIBLE', 'elementHide');
            $objTemplate->setVariable('VERSION_20_MUST', 'inpmust');
            $objTemplate->setVariable('VERSION_30_MUST', '');
            $objTemplate->setVariable('VERSION_40_MUST', '');
            $objTemplate->setVariable('VERSION_20_STAR', '*');
            $objTemplate->setVariable('NAGIOS_VERSION', '2');
        }
        if ($this->intVersion >= 3) {
            $objTemplate->setVariable('VERSION_20_VISIBLE', 'elementHide');
            $objTemplate->setVariable('VERSION_30_VISIBLE', 'elementShow');
            $objTemplate->setVariable('VERSION_40_VISIBLE', 'elementHide');
            $objTemplate->setVariable('VERSION_20_MUST', '');
            $objTemplate->setVariable('VERSION_30_MUST', 'inpmust');
            $objTemplate->setVariable('VERSION_40_MUST', '');
            $objTemplate->setVariable('VERSION_20_STAR', '');
            $objTemplate->setVariable('NAGIOS_VERSION', '3');
        }
        if ($this->intVersion >= 4) {
            $objTemplate->setVariable('VERSION_40_VISIBLE', 'elementShow');
            $objTemplate->setVariable('VERSION_40_MUST', 'inpmust');
            $objTemplate->setVariable('NAGIOS_VERSION', '4');
        }
        // Checkbox and radio field value replacements
        if ($strChbFields != '') {
            foreach (explode(',', $strChbFields) as $elem) {
                $objTemplate->setVariable('DAT_' .$elem. '0_CHECKED', '');
                $objTemplate->setVariable('DAT_' .$elem. '1_CHECKED', '');
                $objTemplate->setVariable('DAT_' .$elem. '2_CHECKED', 'checked');
            }
        }
    }

    /**
     * Single data form - value insertion
     * @param \HTML_Template_IT $objTemplate    Form template object
     * @param array $arrModifyData              Database values
     * @param int $intLocked                    Data is locked (0 = no / 1 = yes)
     * @param string $strInfo                   Information string
     * @param string $strChbFields              Comma separated string of checkbox value names
     */
    public function addInsertData($objTemplate, $arrModifyData, $intLocked, $strInfo, $strChbFields = '')
    {
        // Insert text data values
        foreach ($arrModifyData as $key => $value) {
            if (($key == 'active') || ($key == 'register') || ($key == 'last_modified') || ($key == 'access_rights')) {
                continue;
            }
            $objTemplate->setVariable('DAT_' .strtoupper($key), htmlentities($value, ENT_QUOTES, 'UTF-8'));
        }
        // Insert checkbox data values
        if (isset($arrModifyData['active']) && ($arrModifyData['active']   != 1)) {
            $objTemplate->setVariable('ACT_CHECKED', '');
        }
        if (isset($arrModifyData['register']) && ($arrModifyData['register'] != 1)) {
            $objTemplate->setVariable('REG_CHECKED', '');
        }
        // Deselect any checkboxes
        if ($strChbFields != '') {
            foreach (explode(',', $strChbFields) as $elem) {
                $objTemplate->setVariable('DAT_' .$elem. '0_CHECKED', '');
                $objTemplate->setVariable('DAT_' .$elem. '1_CHECKED', '');
                $objTemplate->setVariable('DAT_' .$elem. '2_CHECKED', '');
            }
        }
        // Change some status values in locked data sets
        if ($intLocked != 0) {
            $objTemplate->setVariable('ACT_DISABLED', 'disabled');
            $objTemplate->setVariable('ACT_CHECKED', 'checked');
            $objTemplate->setVariable('ACTIVE', '1');
            $objTemplate->setVariable('CHECK_MUST_DATA', $strInfo);
            $objTemplate->setVariable('RELATION_CLASS', 'elementShow');
        }
        // Change mode to modify
        $objTemplate->setVariable('MODUS', 'modify');
        // Check write permission
        if ($this->intWriteAccessId == 1) {
            $objTemplate->setVariable('DISABLE_SAVE', 'disabled="disabled"');
        }
        if ($this->intGlobalWriteAccess == 1) {
            $objTemplate->setVariable('DISABLE_SAVE', 'disabled="disabled"');
        }
    }

    /**
     * Process field view
     * @param array $arrData                    Data array
     * @param string $strTableName              Table name
     * @return string                           String includung field data
     */
    public function processField($arrData, $strTableName)
    {
        $strField          = '';
        $arrDataHosts      = array();
        $arrDataHostgroups = array();
        $arrDataService    = array();
        $arrDataServices   = array();
        // Hostdependency table
        if ($strTableName == 'tbl_hostdependency') {
            if ($arrData['dependent_host_name'] != 0) {
                $strSQLHost = 'SELECT `host_name`, `exclude` FROM `tbl_host` ' .
                    'LEFT JOIN `tbl_lnkHostdependencyToHost_DH` ON `id`=`idSlave` ' .
                    'WHERE `idMaster`=' .$arrData['id']. ' ORDER BY `host_name`';
                $this->myDBClass->hasDataArray($strSQLHost, $arrDataHosts, $intDCHost);
                if ($intDCHost != 0) {
                    foreach ($arrDataHosts as $elem) {
                        if ($elem['exclude'] == 1) {
                            $strField .= 'H:!' .$elem['host_name']. ',';
                        } else {
                            $strField .= 'H:' .$elem['host_name']. ',';
                        }
                    }
                }
            }
            if ($arrData['dependent_hostgroup_name'] != 0) {
                $strSQLHost = 'SELECT `hostgroup_name`, `exclude` FROM `tbl_hostgroup` ' .
                    'LEFT JOIN `tbl_lnkHostdependencyToHostgroup_DH` ON `id`=`idSlave` ' .
                    'WHERE `idMaster`=' .$arrData['id']. ' ORDER BY `hostgroup_name`';
                $this->myDBClass->hasDataArray($strSQLHost, $arrDataHostgroups, $intDCHostgroup);
                if ($intDCHostgroup != 0) {
                    foreach ($arrDataHostgroups as $elem) {
                        if ($elem['exclude'] == 1) {
                            $strField .= 'HG:!' .$elem['hostgroup_name']. ',';
                        } else {
                            $strField .= 'HG:' .$elem['hostgroup_name']. ',';
                        }
                    }
                }
            }
        }
        // Hostescalation table
        if ($strTableName == 'tbl_hostescalation') {
            if ($arrData['host_name'] != 0) {
                $strSQLHost = 'SELECT `host_name` FROM `tbl_host` ' .
                    'LEFT JOIN `tbl_lnkHostescalationToHost` ON `id`=`idSlave` ' .
                    'WHERE `idMaster`=' .$arrData['id']. ' ORDER BY `host_name`';
                $this->myDBClass->hasDataArray($strSQLHost, $arrDataHosts, $intDCHost);
                if ($intDCHost != 0) {
                    foreach ($arrDataHosts as $elem) {
                        $strField .= 'H:' .$elem['host_name']. ',';
                    }
                }
            }
            if ($arrData['hostgroup_name'] != 0) {
                $strSQLHost = 'SELECT `hostgroup_name` FROM `tbl_hostgroup` ' .
                    'LEFT JOIN `tbl_lnkHostescalationToHostgroup` ON `id`=`idSlave` ' .
                    'WHERE `idMaster`=' .$arrData['id']. ' ORDER BY `hostgroup_name`';
                $this->myDBClass->hasDataArray($strSQLHost, $arrDataHostgroups, $intDCHostgroup);
                if ($intDCHostgroup != 0) {
                    foreach ($arrDataHostgroups as $elem) {
                        $strField .= 'HG:' .$elem['hostgroup_name']. ',';
                    }
                }
            }
        }
        // Servicedependency table
        if ($strTableName == 'tbl_servicedependency') {
            if ($arrData['dependent_service_description'] == 2) {
                $strField .= '*';
            } elseif ($arrData['dependent_service_description'] != 0) {
                $strSQLService = 'SELECT `strSlave` FROM `tbl_lnkServicedependencyToService_DS` ' .
                    'WHERE `idMaster`=' .$arrData['id']. ' ORDER BY `strSlave`';
                $this->myDBClass->hasDataArray($strSQLService, $arrDataService, $intDCService);
                if ($intDCService != 0) {
                    foreach ($arrDataService as $elem) {
                        $strField .= $elem['strSlave']. ',';
                    }
                }
            }
            if ($strField == '') {
                $strSQLService = 'SELECT `servicegroup_name` FROM `tbl_servicegroup` ' .
                    'LEFT JOIN `tbl_lnkServicedependencyToServicegroup_DS` ON `idSlave`=`id` ' .
                    'WHERE `idMaster`=' .$arrData['id']. ' ORDER BY `servicegroup_name`';
                $this->myDBClass->hasDataArray($strSQLService, $arrDataService, $intDCService);
                if ($intDCService != 0) {
                    foreach ($arrDataService as $elem) {
                        $strField .= $elem['servicegroup_name']. ',';
                    }
                }
            }
        }
        // Serviceescalation table
        if ($strTableName == 'tbl_serviceescalation') {
            if ($arrData['service_description'] == 2) {
                $strField .= '*';
            } elseif ($arrData['service_description'] != 0) {
                $strSQLService = 'SELECT `strSlave` FROM `tbl_lnkServiceescalationToService` ' .
                    'WHERE `idMaster`=' .$arrData['id'];
                $this->myDBClass->hasDataArray($strSQLService, $arrDataServices, $intDCServices);
                if ($intDCServices != 0) {
                    foreach ($arrDataServices as $elem) {
                        $strField .= $elem['strSlave']. ',';
                    }
                }
            }
            if ($strField == '') {
                $strSQLService = 'SELECT `servicegroup_name` FROM `tbl_servicegroup` ' .
                    'LEFT JOIN `tbl_lnkServiceescalationToServicegroup` ON `idSlave`=`id` ' .
                    'WHERE `idMaster`=' .$arrData['id']. ' ORDER BY `servicegroup_name`';
                $this->myDBClass->hasDataArray($strSQLService, $arrDataService, $intDCService);
                if ($intDCService != 0) {
                    foreach ($arrDataService as $elem) {
                        $strField .= $elem['servicegroup_name']. ',';
                    }
                }
            }
        }
        // Some string manipulations - remove comma on line end
        if (substr($strField, -1) == ',') {
            $strField = substr($strField, 0, -1);
        }
        return $strField;
    }
}
