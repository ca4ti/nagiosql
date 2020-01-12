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
// Component : Preprocessing script for content pages
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$intLineCount     = 0;   // Database line count
$intWriteAccessId = 0;   // Write access to data id ($chkDataId)
$intReadAccessId  = 0;   // Read access to data id ($chkListId)
$intDataWarning   = 0;   // Missing data indicator
$intNoTime        = 0;   // Show modified time list (0=show)
$strSearchWhere   = '';  // SQL WHERE addon for searching
$strSearchWhere2  = '';  // SQL WHERE addon for configuration selection list
$chkTfValue3      = '';
$chkTfValue5      = '';
//
// Define missing variables used in this prepend file
// ==================================================
if (!isset($preTableName)) {
    $preTableName        = '';
} // Predefined variable table name
if (!isset($preSearchSession)) {
    $preSearchSession    = '';
} // Predefined variable search session
//
// Store some variables to content class
// =====================================
$myContentClass->intLimit         = $chkLimit;
/** @var int $intVersion - defined in prepend_adm.php */
$myContentClass->intVersion       = $intVersion;
$myContentClass->strBrowser       = $preBrowser;
$myContentClass->intGroupAdm      = $chkGroupAdm;
$myContentClass->strTableName     = $preTableName;
$myContentClass->strSearchSession = $preSearchSession;
$myContentClass->intSortBy        = $hidSortBy;
$myContentClass->strSortDir       = $hidSortDir;
//
// Process get parameters
// ======================
$chkFromLine = filter_input(INPUT_GET, 'from_line', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
//
// Process post parameters
// =======================
$chkTfSearchRaw     = filter_input(INPUT_POST, 'txtSearch', FILTER_SANITIZE_STRING);
$chkSelAccGr        = filter_input(INPUT_POST, 'selAccGr', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
$chkSelCnfName      = filter_input(INPUT_POST, 'selCnfName', FILTER_SANITIZE_STRING);
$chkSelRegFilter    = filter_input(INPUT_POST, 'selRegFilter', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
$chkSelActiveFilter = filter_input(INPUT_POST, 'selActiveFilter', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
//
// Common text field value
for ($i = 1; $i <= 23; $i++) {
    $tmpVar  = 'chkTfValue'.$i;
    $$tmpVar = filter_input(INPUT_POST, 'tfValue'.$i, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    if (get_magic_quotes_gpc() == 0) {
        $$tmpVar = addslashes($$tmpVar);
    }
    if (isset($$tmpVar)) {
        $$tmpVar = $myVisClass->tfSecure($$tmpVar);
    }
}
// Common argument text field value
for ($i = 1; $i <= 8; $i++) {
    $tmpVar  = 'chkTfArg'.$i;
    $$tmpVar = filter_input(INPUT_POST, 'tfArg'.$i, FILTER_UNSAFE_RAW);
    if (get_magic_quotes_gpc() == 0) {
        $$tmpVar = addslashes($$tmpVar);
    }
    if (isset($$tmpVar)) {
        $$tmpVar = $myVisClass->tfSecure($$tmpVar);
    }
}
// Common argument info field value
for ($i = 1; $i <= 8; $i++) {
    $tmpVar  = 'chkTaArg'.$i.'Info';
    $$tmpVar = filter_input(INPUT_POST, 'taArg'.$i.'Info', FILTER_UNSAFE_RAW);
    if (get_magic_quotes_gpc() == 0) {
        $$tmpVar = addslashes($$tmpVar);
    }
    if (isset($$tmpVar)) {
        $$tmpVar = $myVisClass->tfSecure($$tmpVar);
    }
}
// Common multi select field value
for ($i = 1; $i <= 8; $i++) {
    $tmpVar  = 'chkMselValue'.$i;
    $tmpVar2 = 'intMselValue'.$i;
    $$tmpVar = filter_input(INPUT_POST, 'mselValue'.$i, FILTER_SANITIZE_STRING, FILTER_FORCE_ARRAY);
    // Multiselect data processing
    if ((${$tmpVar}[0] == '') || (${$tmpVar}[0] == '0')) {
        $$tmpVar2 = 0;
    } elseif (${$tmpVar}[0] == '*') {
        $$tmpVar2 = 2;
    } else {
        $$tmpVar2 = 1;
    }
}
// Common select field value
for ($i = 1; $i <= 5; $i++) {
    $tmpVar  = 'chkSelValue'.$i;
    $$tmpVar = filter_input(INPUT_POST, 'selValue'.$i, FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
}
//Common radio field value
for ($i = 1; $i <= 18; $i++) {
    $tmpVar  = 'chkRadValue'.$i;
    $$tmpVar = filter_input(INPUT_POST, 'radValue'.$i, FILTER_VALIDATE_INT, array('options' => array('default' => 2)));
}
// Common checkbox group
$arrChar = explode(';', 'a;b;c;d;e;f;g;h');
for ($i = 1; $i <= 4; $i++) {
    foreach ($arrChar as $elem) {
        $tmpVar  = 'chkChbGr'.$i.$elem;
        $$tmpVar = filter_input(INPUT_POST, 'chbGr'.$i.$elem, FILTER_SANITIZE_STRING);
        if ($$tmpVar != '') {
            $$tmpVar .= ',';
        }
    }
}
// Common button value
for ($i = 1; $i <= 5; $i++) {
    $tmpVar  = 'chkButValue'.$i;
    $$tmpVar = filter_input(INPUT_POST, 'butValue'.$i, FILTER_SANITIZE_STRING);
}
// Common text NULL field value
for ($i = 1; $i <= 9; $i++) {
    $tmpVar  = 'chkTfNullVal'.$i;
    $$tmpVar = filter_input(INPUT_POST, 'tfNullVal'.$i, FILTER_SANITIZE_STRING);
    if (isset($$tmpVar) && ($$tmpVar != '')) {
        $myVisClass->checkNull($$tmpVar);
    } else {
        $$tmpVar = 'NULL';
    }
}
// Common checkbox field value
$chkChbValue1 = filter_input(INPUT_POST, 'chbValue1', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
$chkChbValue2 = filter_input(INPUT_POST, 'chbValue2', FILTER_VALIDATE_INT, array('options' => array('default' => 0)));
// Common file selection field
$chkDatValue1 = filter_input(INPUT_POST, 'datValue1', FILTER_SANITIZE_STRING);
// Common text area value
$chkTaValue1Raw = filter_input(INPUT_POST, 'taValue1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
// Common text area value for file import (not SQL)
$chkTaFileTextRaw = filter_input(INPUT_POST, 'taFileText', FILTER_UNSAFE_RAW);
// Common text field with special chars
$chkTfSpValue1 = filter_input(INPUT_POST, 'tfSpValue1', FILTER_UNSAFE_RAW);
//
// Quote special characters
// ==========================
if (get_magic_quotes_gpc() == 0) {
    $chkTfSearchRaw    = addslashes($chkTfSearchRaw);
    $chkTaValue1Raw    = addslashes($chkTaValue1Raw);
    $chkTaFileTextRaw  = addslashes($chkTaFileTextRaw);
    $chkTfSpValue1     = addslashes($chkTfSpValue1);
}
//
// Security function for text fields
// =================================
$chkTfSearch    = $myVisClass->tfSecure($chkTfSearchRaw);
$chkTaValue1    = $myVisClass->tfSecure($chkTaValue1Raw);
$chkTfSpValue1  = $myVisClass->tfSecure($chkTfSpValue1);
$chkTaFileText  = stripslashes($chkTaFileTextRaw);
//
// Search/sort/filter - session data
// =================================
if (!isset($_SESSION['search'][$preSearchSession])) {
    $_SESSION['search'][$preSearchSession] = '';
}
if (!isset($_SESSION['filter'][$preSearchSession]['registered'])) {
    $_SESSION['filter'][$preSearchSession]['registered'] = '';
}
if (!isset($_SESSION['filter'][$preSearchSession]['active'])) {
    $_SESSION['filter'][$preSearchSession]['active'] = '';
}
if (!isset($_SESSION['search']['config_selection'])) {
    $_SESSION['search']['config_selection'] = '';
}
if (($chkModus == 'checkform') || ($chkModus == 'filter')) {
    $_SESSION['search'][$preSearchSession]  = $chkTfSearch;
    $_SESSION['search']['config_selection'] = $chkSelCnfName;
    $_SESSION['filter'][$preSearchSession]['registered'] = $chkSelRegFilter;
    $_SESSION['filter'][$preSearchSession]['active'] = $chkSelActiveFilter;
    $myContentClass->arrSession = $_SESSION;
}
//
// Process additional templates/variables
// ======================================
if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition']) &&
   (count($_SESSION['templatedefinition']) != 0)) {
    $intTemplates = 1;
} else {
    $intTemplates = 0;
}
if (isset($_SESSION['variabledefinition']) && is_array($_SESSION['variabledefinition']) &&
   (count($_SESSION['variabledefinition']) != 0)) {
    $intVariables = 1;
} else {
    $intVariables = 0;
}
//
// Common SQL parts
// ================
if ($hidActive   == 1) {
    $chkActive = 1;
}
if ($chkGroupAdm == 1) {
    $strGroupSQL = "`access_group`=$chkSelAccGr, ";
} else {
    $strGroupSQL = '';
}
$preSQLCommon1 = "$strGroupSQL `active`='$chkActive', `register`='$chkRegister', `config_id`=$chkDomainId, "
               . '`last_modified`=NOW()';
$preSQLCommon2 = "$strGroupSQL `active`='$chkActive', `register`='0', `config_id`=$chkDomainId, `last_modified`=NOW()";
$intRet1 = 0;
$intRet2 = 0;
$intRet3 = 0;
$intRet4 = 0;
$intRet5 = 0;
$intRet6 = 0;
$intRet7 = 0;
$intRet8 = 0;
//
// Check read and write access
// ===========================
if (isset($prePageKey)) {
    // Global read access (0 = access granted)
    $intGlobalReadAccess  = $myVisClass->checkAccountGroup($prePageKey, 'read');
    // Global write access (0 = access granted)
    $intGlobalWriteAccess = $myVisClass->checkAccountGroup($prePageKey, 'write');
    $myContentClass->intGlobalWriteAccess = $intGlobalWriteAccess;
}
if (!isset($preNoAccessGrp) || ($preNoAccessGrp == 0)) {
    if ($chkDataId != 0) {
        $strSQLWrite      = "SELECT `access_group` FROM `$preTableName` WHERE `id`=".$chkDataId;
        $intWriteAccessId = $myVisClass->checkAccountGroup((int)$myDBClass->getFieldData($strSQLWrite), 'write');
        $myContentClass->intWriteAccessId = $intWriteAccessId;
    }
    if ($chkListId != 0) {
        $strSQLWrite      = "SELECT `access_group` FROM `$preTableName` WHERE `id`=".$chkListId;
        $intReadAccessId  = $myVisClass->checkAccountGroup((int)$myDBClass->getFieldData($strSQLWrite), 'read');
        $intWriteAccessId = $myVisClass->checkAccountGroup((int)$myDBClass->getFieldData($strSQLWrite), 'write');
        $myContentClass->intWriteAccessId = $intWriteAccessId;
    }
}
//
// Data processing
// ===============
if (($chkModus == 'make') && ($intGlobalWriteAccess == 0)) {
    $intError    = 0;
    $intSuccess = 0;
    // Get write access groups
    $strAccess = $myVisClass->getAccessGroups('write');
    // Write configuration file
    if ($preTableName == 'tbl_host') {
        /** @var string $strDomainWhere - defined in prepend_adm.php */
        $strSQL    = "SELECT `id` FROM `$preTableName` "
                   . "WHERE $strDomainWhere AND `access_group` IN ($strAccess) AND `active`='1'";
        $booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if ($booReturn == false) {
            $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
        }
        if ($booReturn && ($intDataCount != 0)) {
            foreach ($arrData as $data) {
                $intReturn = $myConfigClass->createConfigSingle($preTableName, $data['id']);
                if ($intReturn == 1) {
                    $intError++;
                    $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
                } else {
                    $intSuccess++;
                }
            }
        } else {
            $myVisClass->processMessage(translate('Some configuration files were not written. Dataset not activated, '
                    . 'not found or you do not have write permission!'), $strErrorMessage);
        }
        if ($intSuccess != 0) {
            $myVisClass->processMessage(translate('Configuration files successfully written!'), $strInfoMessage);
        }
        if ($intError   != 0) {
            $myVisClass->processMessage(translate('Some configuration files were not written. Dataset not activated, '
                    . 'not found or you do not have write permission!'), $strErrorMessage);
        }
    } elseif ($preTableName == 'tbl_service') {
        /** @var string $strDomainWhere - defined in prepend_adm.php */
        $strSQL  = "SELECT `id`, `$preKeyField` FROM `$preTableName` "
                 . "WHERE $strDomainWhere AND `access_group` IN ($strAccess) AND `active`='1' "
                 . "GROUP BY `$preKeyField`, `id`";
        $myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if ($booReturn == false) {
            $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
        }
        if ($booReturn && ($intDataCount != 0)) {
            foreach ($arrData as $data) {
                $intReturn = $myConfigClass->createConfigSingle($preTableName, $data['id']);
                if ($intReturn == 1) {
                    $intError++;
                    $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
                } else {
                    $intSuccess++;
                }
            }
        } else {
            $myVisClass->processMessage(translate('Some configuration files were not written. Dataset not activated, '
                    . 'not found or you do not have write permission!'), $strErrorMessage);
        }
        if ($intSuccess != 0) {
            $myVisClass->processMessage(translate('Configuration files successfully written!'), $strInfoMessage);
        }
        if ($intError   != 0) {
            $myVisClass->processMessage(translate('Some configuration files were not written. Dataset not activated, '
                    . 'not found or you do not have write permission!'), $strErrorMessage);
        }
    } else {
        $intReturn = $myConfigClass->createConfig($preTableName, 0);
        if ($intReturn == 1) {
            $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
        }
        if ($intReturn == 0) {
            $myVisClass->processMessage($myConfigClass->strInfoMessage, $strInfoMessage);
        }
    }
    $chkModus  = 'display';
} elseif (($chkModus == 'checkform') && ($chkSelModify == 'info')) {
    // Display additional relation information
    if ($preTableName == 'tbl_service') {
        $intReturn = $myDataClass->infoRelation($preTableName, $chkListId, "$preKeyField,service_description");
    } else {
        $intReturn = $myDataClass->infoRelation($preTableName, $chkListId, $preKeyField);
    }
    $myVisClass->processMessage($myDataClass->strInfoMessage, $strConsistMessage);
    $chkModus  = 'display';
} elseif (($chkModus == 'checkform') && ($chkSelModify == 'delete') && ($intGlobalWriteAccess == 0)) {
    $intReturn = 1;
    // Delete selected datasets
    if (($preTableName == 'tbl_user') && ($chkTfValue5 == 'Admin')) {
        $myVisClass->processMessage(translate('Admin cannot be deleted'), $strErrorMessage);
        $intReturn = 0;
    } elseif ((($preTableName == 'tbl_datadomain') || ($preTableName == 'tbl_configtarget')) &&
              ($chkTfValue3 == 'localhost')) {
        $myVisClass->processMessage(translate("Localhost can't be deleted"), $strErrorMessage);
        $intReturn = 0;
    } elseif (($preTableName == 'tbl_user') || ($preTableName == 'tbl_datadomain') ||
              ($preTableName == 'tbl_configtarget')) {
        $intReturn = $myDataClass->dataDeleteEasy($preTableName, $chkListId);
    } else {
        $strInfoMessageTmp = $strInfoMessage;
        if ($preTableName == 'tbl_service') {
            $intRetVal = $myDataClass->infoRelation($preTableName, $chkListId, "$preKeyField,service_description");
        } else {
            $intRetVal = $myDataClass->infoRelation($preTableName, $chkListId, $preKeyField);
        }
        if ($intRetVal == 0) {
            $strInfoMessage = $strInfoMessageTmp;
            $intReturn = $myDataClass->dataDeleteFull($preTableName, $chkListId);
        }
    }
    $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
    $myVisClass->processMessage($myDataClass->strInfoMessage, $strInfoMessage);
    $chkModus  = 'display';
} elseif (($chkModus == 'checkform') && ($chkSelModify == 'copy') && ($intGlobalWriteAccess == 0)) {
    // Copy selected datasets
    $intReturn = $myDataClass->dataCopyEasy($preTableName, $preKeyField, $chkListId, $chkSelTarDom);
    if ($intReturn == 1) {
        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
    }
    if ($intReturn == 0) {
        $myVisClass->processMessage($myDataClass->strInfoMessage, $strInfoMessage);
    }
    $chkModus = 'display';
} elseif (($chkModus == 'checkform') && ($chkSelModify == 'activate') && ($intGlobalWriteAccess == 0)) {
    // Activate selected datasets
    $intReturn = $myDataClass->dataActivate($preTableName, $chkListId);
    if ($intReturn == 1) {
        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
    }
    if ($intReturn == 0) {
        $myVisClass->processMessage($myDataClass->strInfoMessage, $strInfoMessage);
    }
    $chkModus  = 'display';
} elseif (($chkModus == 'checkform') && ($chkSelModify == 'deactivate') && ($intGlobalWriteAccess == 0)) {
    // Deactivate selected datasets
    $intReturn = $myDataClass->dataDeactivate($preTableName, $chkListId);
    if ($intReturn == 1) {
        $myVisClass->processMessage($myDataClass->strErrorMessage, $strErrorMessage);
    }
    if ($intReturn == 0) {
        $myVisClass->processMessage($myDataClass->strInfoMessage, $strInfoMessage);
    }
    // Remove deactivated files
    if ($preTableName == 'tbl_host') {
        if ($chkListId != 0) {
            $strChbName = 'chbId_' .$chkListId;
            $_POST[$strChbName] = 'on';
        }
        // Get write access groups
        $strAccess = $myVisClass->getAccessGroups('write');
        // Getting data sets
        $strSQL    = 'SELECT `id`, `host_name` FROM `' .$preTableName. '` '
                   . "WHERE `active`='0' AND `access_group` IN ($strAccess) AND `config_id`=".$chkDomainId;
        $booReturn    = $myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if ($booReturn && ($intDataCount != 0) && ($chkDomainId != 0)) {
            $intReturn  = $myConfigClass->getConfigTargets($arrConfigID);
            $intError   = 0;
            $intSuccess = 0;
            if (($arrConfigID != 1) && is_array($arrConfigID)) {
                foreach ($arrData as $elem) {
                    $strChbName = 'chbId_' .$elem['id'];
                    // was the current record is marked for deactivate?
                    if ((filter_input(INPUT_POST, $strChbName) != null) &&
                        (filter_input(INPUT_POST, $strChbName, FILTER_SANITIZE_STRING) == 'on')) {
                        $intCount  = 0;
                        $intReturn = 0;
                        foreach ($arrConfigID as $intConfigID) {
                            $intReturn += $myConfigClass->moveFile('host', $elem['host_name']. '.cfg', $intConfigID);
                            if ($intReturn == 0) {
                                $myDataClass->writeLog(translate('Host file deleted:'). ' ' .$elem['host_name']
                                    . '.cfg');
                                $intCount++;
                            }
                        }
                        if ($intReturn == 0) {
                            $intSuccess++;
                        }
                        if ($intReturn != 0) {
                            $intError++;
                        }
                    }
                }
                if (($intSuccess != 0) && ($intCount != 0)) {
                    $myVisClass->processMessage(translate('The assigned, no longer used configuration files were '
                            . 'deleted successfully!').$intCount, $strInfoMessage);
                }
                if ($intError != 0) {
                    $myVisClass->processMessage(translate('Errors while deleting the old configuration file - please '
                            . 'check!:'), $strErrorMessage);
                }
            }
        } elseif ($chkDomainId == 0) {
            $myVisClass->processMessage(translate('Common files cannot be removed from target systems - please check '
                    . 'manually'), $strErrorMessage);
        }
    } elseif ($preTableName == 'tbl_service') {
        if ($chkListId != 0) {
            $strChbName = 'chbId_' .$chkListId;
            $_POST[$strChbName] = 'on';
        }
        // Get write access groups
        $strAccess = $myVisClass->getAccessGroups('write');
        // Getting data sets
        $strSQL    = 'SELECT `id`, `config_name` FROM `' .$preTableName. '` '
                   . "WHERE `active`='0' AND `access_group` IN ($strAccess) AND `config_id`=".$chkDomainId;
        $booReturn = $myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if ($booReturn && ($intDataCount != 0) && ($chkDomainId != 0)) {
            $intReturn  = $myConfigClass->getConfigTargets($arrConfigID);
            $intError   = 0;
            $intSuccess = 0;
            if (($arrConfigID != 1) && is_array($arrConfigID)) {
                $intCount  = 0;
                foreach ($arrData as $elem) {
                    $strChbName = 'chbId_' .$elem['id'];
                    // was the current record is marked for deactivate?
                    if (filter_input(INPUT_POST, $strChbName) && (filter_input(INPUT_POST, $strChbName) == 'on')) {
                        $intServiceCount = $myDBClass->countRows("SELECT * FROM `$preTableName` "
                                                               . "WHERE `$preKeyField`='".$elem['config_name']."' "
                                                               . "AND `config_id`=$chkDomainId AND `active`='1'");
                        if ($intServiceCount == 0) {
                            $intReturn = 0;
                            foreach ($arrConfigID as $intConfigID) {
                                $intReturn += $myConfigClass->moveFile(
                                    'service',
                                    $elem['config_name']. '.cfg',
                                    $intConfigID
                                );
                                if ($intReturn == 0) {
                                    $myDataClass->writeLog(translate('Service file deleted:'). ' ' .
                                                                      $elem['config_name']. '.cfg');
                                }
                                $intCount++;
                            }
                            if ($intReturn == 0) {
                                $intSuccess++;
                            }
                            if ($intReturn != 0) {
                                $intError++;
                            }
                        }
                    }
                }
                if (($intSuccess != 0) && ($intCount != 0)) {
                    $myVisClass->processMessage(translate('The assigned, no longer used configuration files were '
                            . 'deleted successfully!'), $strInfoMessage);
                }
                if ($intError != 0) {
                    $myVisClass->processMessage(translate('Errors while deleting the old configuration file - please '
                            . 'check!:'), $strErrorMessage);
                }
            }
        } elseif ($chkDomainId == 0) {
            $myVisClass->processMessage(translate('Common files cannot be removed from target systems - please check '
                    . 'manually'), $strErrorMessage);
        }
    }
    $chkModus  = 'display';
} elseif (($chkModus == 'checkform') && ($chkSelModify == 'modify')) {
    // Open the dataset to modify
    if ($intReadAccessId == 0) {
        $booReturn = $myDBClass->hasSingleDataset("SELECT * FROM `$preTableName` "
                . 'WHERE `id`=' .$chkListId, $arrModifyData);
        if ($booReturn == false) {
            $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
            $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
            $chkModus = 'display';
        } else {
            $chkModus = 'add';
        }
    } else {
        $myVisClass->processMessage(translate('No permission to open configuration!'), $strErrorMessage);
        $chkModus = 'display';
    }
} elseif (($chkModus == 'checkform') && ($chkSelModify == 'config') && ($intGlobalWriteAccess == 0)) {
    // Write configuration file (hosts and services)
    $intDSId  = (int)substr(array_search('on', filter_input_array(INPUT_POST), true), 6);
    if (isset($chkListId) && ($chkListId != 0)) {
        $intDSId = $chkListId;
    }
    $intValCount = 0;
    foreach (filter_input_array(INPUT_POST) as $key => $elem) {
        if ($elem == 'on') {
            $intValCount++;
        }
    }
    if ($intValCount > 1) {
        $intDSId = 0;
    }
    $intReturn = $myConfigClass->createConfigSingle($preTableName, $intDSId);
    if ($intReturn == 1) {
        $myVisClass->processMessage($myConfigClass->strErrorMessage, $strErrorMessage);
    }
    if ($intReturn == 0) {
        $myVisClass->processMessage($myConfigClass->strInfoMessage, $strInfoMessage);
    }
    $chkModus  = 'display';
}
//
// Some common list view functions
// ===============================
if ($chkModus != 'add') {
    // Get Group id's with READ
    $strAccess = $myVisClass->getAccessGroups('read');
    // Include domain list
    /** @var HTML_Template_IT $mastertp */
    $myVisClass->insertDomainList($mastertp);
    // Process filter string
}
