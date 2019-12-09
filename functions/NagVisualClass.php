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
// Component : Visualization Class
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Class: Common visualization functions
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Includes all functions used to display the application data
//
// Name: NagVisualClass
//
///////////////////////////////////////////////////////////////////////////////////////////////
namespace functions;

class NagVisualClass
{
    // Define class variables
    private $arrSettings        = array();  // Array includes all global settings
    private $intPageId          = 0;        // Content page ID
    //
    public $arrSession          = array();  // Session content
    public $intDomainId         = 0;        // Configuration domain ID
    public $intDataId           = 0;        // Content data ID
    public $strErrorMessage     = '';       // String including error messages


    // Class includes
    /** @var MysqliDbClass */
    public $myDBClass;                      // Database class reference
    /** @var NagConfigClass */
    public $myConfigClass;                  // Configuraton class reference
    /** @var \HTML_Template_IT */
    public $myContentTpl;                   // Content template class reference

    /**
     * NagVisualClass constructor.
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
     * Search for browser type
     * @return string                           Browser String
     */
    public function browserCheck()
    {
        $strUserAgent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
        // Define variables
        $strBrowserString = 'unknown';
        if (false !== stripos($strUserAgent, 'msie')) {
            $strBrowserString = 'msie';
        } elseif (false !== stripos($strUserAgent, 'firefox')) {
            $strBrowserString = 'firefox';
        } elseif (false !== stripos($strUserAgent, 'opera')) {
            $strBrowserString = 'opera';
        } elseif (false !== stripos($strUserAgent, 'chrome')) {
            $strBrowserString = 'chrome';
        }
        return $strBrowserString;
    }

    /**
     * Checks if an user has acces to an account group
     * @param int $intGroupId                   Group ID
     * @param string $strType                   Access type (read,write,link)
     * @return int                              0 = access granted / 1 = no access
     */
    public function checkAccountGroup($intGroupId, $strType)
    {
        // Define variables
        $intReturn = 0;
        // Admin user or member og group 0 do not need permissions
        if (($this->arrSession['userid'] != 1) && ($intGroupId != 0)) {
            // Define variables
            $arrDataMain  = array();
            // Read database values
            $strTypeValue = $this->getGroupValue($strType);
            if ($strTypeValue != '') {
                $strSQL    = "SELECT * FROM `tbl_lnkGroupToUser` WHERE `idMaster`=$intGroupId AND ".
                    '`idSlave`=' .$this->arrSession['userid']." AND $strTypeValue";
                $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrDataMain, $intDataCount);
                if ($booReturn == false) {
                    $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                }
                if (($booReturn == false) || ($intDataCount == 0)) {
                    $intReturn = 1;
                }
            }
        }
        return $intReturn;
    }

    /**
     * Find out the actual position inside the menu tree and returns it as an info line
     * @param int $intPageId                    Current content id
     * @param string $strTop                    Label string for the root node
     * @return string                           HTML info string
     */
    public function getPosition($intPageId, $strTop = '')
    {
        // Define variables
        $arrData      = array();
        $intDataCount = 0;
        $strPosition  = '';
        // Read database values
        $strSQL    = 'SELECT B.`mnuName` AS `mainitem`, B.`mnuLink` AS `mainlink`, A.`mnuName` AS `subitem`, '
            . 'A.`mnuLink` AS `sublink` FROM `tbl_menu` AS A '
            . 'LEFT JOIN `tbl_menu` AS B ON A.`mnuTopId` = B.`mnuId` WHERE A.`mnuId`=' .$intPageId;
        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if ($booReturn == false) {
            $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
        } elseif ($intDataCount != 0) {
            $strMainLink = $this->arrSettings['path']['base_url'].$arrData[0]['mainlink'];
            $strMain     = $arrData[0]['mainitem'];
            $strSubLink  = $this->arrSettings['path']['base_url'].$arrData[0]['sublink'];
            $strSub      = $arrData[0]['subitem'];
            if ($strTop != '') {
                $strPosition .= "<a href='".$this->arrSettings['path']['base_url']."admin.php'>".$strTop. '</a> -> ';
            }
            if ($strMain != '') {
                $strPosition .= "<a href='".$strMainLink."'>".translate($strMain)."</a> -> <a href='".$strSubLink."'>".
                    translate($strSub). '</a>';
            } else {
                $strPosition .= "<a href='".$strSubLink."'>".translate($strSub). '</a>';
            }
        }
        return $strPosition;
    }

    /**
     * Returns any group ID with the requested access type
     * @param string $strType                   Access type (read,write,link)
     * @return string                           Comma separated string with group id's
     */
    public function getAccessGroups($strType)
    {
        $strReturn = '0,';
        $arrData   = array();
        // Admin has rights for all groups
        if ($this->arrSession['userid'] == 1) {
            $strSQL    = 'SELECT `id` FROM `tbl_group`';
            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intCount);
            if ($booReturn == false) {
                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
            } elseif ($intCount != 0) {
                foreach ($arrData as $elem) {
                    $strReturn .= $elem['id']. ',';
                }
            }
        } else {
            $strTypeValue = $this->getGroupValue($strType);
            $strSQL    = 'SELECT `idMaster` FROM `tbl_lnkGroupToUser` ' .
                'WHERE `idSlave`=' . $this->arrSession['userid'] . " AND $strTypeValue";
            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrData, $intCount);
            if ($booReturn == false) {
                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
            } elseif ($intCount != 0) {
                foreach ($arrData as $elem) {
                    $strReturn .= $elem['idMaster'] . ',';
                }
            }
        }
        if (substr($strReturn, -1) == ',') {
            $strReturn = substr($strReturn, 0, -1);
        }
        return $strReturn;
    }

    /**
     * Generate the main menu HTML
     * @param int $intPageId                    Current content id
     * @param int $intCntId                     Menu group ID
     * @return string                           HTML menu string
     */
    public function getMenu($intPageId, $intCntId = 1)
    {
        // Define variables
        $strQueryString = filter_input(INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_STRING);
        $strPHPSelf     = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING);

        // Modify URL for visible/invisible menu
        $strQuery = str_replace(
            array('menu=visible&', 'menu=invisible&', 'menu=visible', 'menu=invisible'),
            '',
            $strQueryString
        );
        if ($strQuery != '') {
            $strVisible   = str_replace('&', '&amp;', $strPHPSelf. '?menu=visible&' .$strQuery);
            $strInvisible = str_replace('&', '&amp;', $strPHPSelf. '?menu=invisible&' .$strQuery);
        } else {
            $strVisible   = $strPHPSelf. '?menu=visible';
            $strInvisible = $strPHPSelf. '?menu=invisible';
        }
        $this->intPageId = $intPageId;
        if (!isset($this->arrSession['menu']) || ($this->arrSession['menu'] != 'invisible')) {
            // Menu visible
            $strHTML  = '<td width="150" align="center" valign="top">'."\n";
            $strHTML .= '<table cellspacing="1" class="menutable">'."\n";
            $this->hasMenuRecursive(0, 'menu', $intCntId, $strHTML);
            $strHTML .= '</table>'."\n";
            $strHTML .= '<br><a href="'.$strInvisible.'" class="menulinksmall">['.translate('Hide menu').']</a>'."\n";
            $strHTML .= '<div id="donate"><a href="http://sourceforge.net/donate/index.php?group_id=134390" ';
            $strHTML .= 'target="_blank"><img src="'.$this->arrSettings['path']['base_url'].'images/donate_2.png" ';
            $strHTML .= 'width="60" height="24" border="0" alt="'.translate('Donate for NagiosQL on sourceforge');
            $strHTML .= '" title="'.translate('Donate for NagiosQL on sourceforge').'"></a></div>';
            $strHTML .= '</td>'."\n";
        } else {
            // Menu invisible
            $strHTML  = '<td valign="top">'."\n";
            $strHTML .= '<a href="'.$strVisible.'"><img src="'.$this->arrSettings['path']['base_url'];
            $strHTML .= 'images/menu.gif" alt="'.translate('Show menu').'" border="0" ></a>'."\n";
            $strHTML .= '</td>'."\n";
        }
        return $strHTML;
    }

    /**
     * Add security features to text values
     * @param string $strKey                    Process string
     * @return string                           Modified process string
     */
    public function tfSecure($strKey)
    {
        $strKey = stripslashes($strKey);
        $strKey = $this->myDBClass->realEscape($strKey);
        return $strKey;
    }

    /**
     * Build a string which contains links for additional pages. This is used in data lists
     * with more items then defined in settings "lines per page limit"
     * @param string $strSite                   Link to page
     * @param int $intDataCount                 Sum of all data lines
     * @param int $chkLimit                     Actual data limit
     * @param string $strOrderBy                OrderBy Field
     * @param string $strOrderDir               Order direction
     * @return string                           Page site number string (HTML)
     */
    public function buildPageLinks($strSite, $intDataCount, $chkLimit, $strOrderBy = '', $strOrderDir = '')
    {
        $intMaxLines  = $this->arrSettings['common']['pagelines'];
        $intCount     = 1;
        $intCheck     = 0;
        $strReturn    = '';
        $strSiteHTML  = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n<tr>\n<td class=\"sitenumber\" ";
        $strSiteHTML .= 'style="padding-left:7px;padding-right:7px;">' .translate('Page').": </td>\n";
        for ($i=0; $i<$intDataCount; $i += $intMaxLines) {
            $strLink1 = "<a href=\"$strSite?limit=$i&amp;orderby=$strOrderBy&amp;orderdir=$strOrderDir\">";
            $strLink2 = "onclick=\"location.href='$strSite?limit=$i&amp;orderby=$strOrderBy&amp;orderdir=".
                "$strOrderDir'\"";
            if ((!(($chkLimit >= ($i+($intMaxLines*5))) || ($chkLimit <= ($i-($intMaxLines*5))))) || ($i==0) ||
                ($i>=($intDataCount-$intMaxLines))) {
                if ($chkLimit == $i) {
                    $strSiteHTML .= "<td class=\"sitenumber-sel\">$intCount</td>\n";
                } else {
                    $strSiteHTML .= "<td class=\"sitenumber\" $strLink2>".$strLink1.$intCount."</a></td>\n";
                }
                $intCheck = 0;
            } elseif ($intCheck == 0) {
                $strSiteHTML .= "<td class=\"sitenumber\">...</td>\n";
                $intCheck = 1;
            }
            $intCount++;
        }
        $strSiteHTML .= "</tr>\n</table>\n";
        if ($intCount > 2) {
            $strReturn = $strSiteHTML;
        }
        return $strReturn;
    }

    /**
     * Builds a simple selection field inside a template
     * @param string $strTable                  Table name (source data)
     * @param string $strTabField               Field name (source data)
     * @param string $strTemplKey               Template key
     * @param int $intModeId                    0=only data, 1=with empty line at the beginning,
     *                                          2=with empty line and 'null' line at the beginning
     * @param int $intSelId                     Selected data ID (from master table)
     * @param int $intExclId                    Exclude ID
     * @return int                              0 = successful / 1 = error
     */
    public function parseSelectSimple(
        $strTable,
        $strTabField,
        $strTemplKey,
        $intModeId = 0,
        $intSelId = -9,
        $intExclId = -9
    ) {
        // Define variables
        $intOption = 0;
        $arrData   = array();
        $intReturn = 1;
        // Compute option value
        if (($strTemplKey == 'hostcommand') || ($strTemplKey == 'servicecommand')) {
            $intOption = 1;
        }
        if ($strTemplKey == 'eventhandler') {
            $intOption = 2;
        }
        if ($strTemplKey == 'service_extinfo') {
            $intOption = 7;
        }
        // Get version
        $this->myConfigClass->getDomainData('version', $intVersion);
        // Get raw data
        $booRaw = $this->getSelectRawdata($strTable, $strTabField, $arrData, $intOption);
        if ($booRaw == 0) {
            // Insert an empty line in mode 1
            if (($intModeId == 1) || ($intModeId == 2)) {
                $this->myContentTpl->setVariable('SPECIAL_STYLE', '');
                $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey), '&nbsp;');
                $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey).'_ID', 0);
                if ($intVersion < 3) {
                    $this->myContentTpl->setVariable('VERSION_20_MUST', 'inpmust');
                }
                $this->myContentTpl->parse($strTemplKey);
            }
            // Insert a 'null' line in mode 2
            if ($intModeId == 2) {
                $this->myContentTpl->setVariable('SPECIAL_STYLE', '');
                $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey), 'null');
                $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey).'_ID', -1);
                if ($intVersion < 3) {
                    $this->myContentTpl->setVariable('VERSION_20_MUST', 'inpmust');
                }
                if ($intSelId == -1) {
                    $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey). '_SEL', 'selected');
                }
                $this->myContentTpl->parse($strTemplKey);
            }
            // Insert data sets
            foreach ($arrData as $elem) {
                $this->myContentTpl->setVariable('SPECIAL_STYLE', '');
                if ($elem['key'] == $intExclId) {
                    continue;
                }
                if (isset($elem['active']) && $elem['active'] == 0) {
                    $strActive=' [inactive]';
                    $this->myContentTpl->setVariable('SPECIAL_STYLE', 'inactive_option');
                } else {
                    $strActive = '';
                }
                if (isset($elem['config_id']) && $elem['config_id'] == 0) {
                    $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey), htmlspecialchars(
                            $elem['value'],
                            ENT_QUOTES,
                            'UTF-8'
                        ).' [common]'.$strActive);
                } else {
                    $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey), htmlspecialchars(
                            $elem['value'],
                            ENT_QUOTES,
                            'UTF-8'
                        ).$strActive);
                }
                $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey). '_ID', $elem['key']);
                if ($intVersion < 3) {
                    $this->myContentTpl->setVariable('VERSION_20_MUST', 'inpmust');
                }
                if ($intSelId == $elem['key']) {
                    $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey). '_SEL', 'selected');
                }
                $this->myContentTpl->parse($strTemplKey);
            }
            $intReturn = 0;
        }
        return $intReturn;
    }

    /**
     * Builds a multi selection field inside a template
     * @param string $strTable                  Table name (source data)
     * @param string $strTabField               Field name (source data)
     * @param string $strTemplKey               Template key
     * @param string $strLinkTable              Name of link table
     * @param int $intModeId                    0 = only data
     *                                          1 = with empty line at the beginning
     *                                          2 = with * line at the beginning
     * @param int $intTypeId                    Type ID (from master table)
     * @param int $intExclId                    Exclude ID
     * @param string $strRefresh                Session token for refresh mode
     * @return int                              0 = successful / 1 = error
     */
    public function parseSelectMulti(
        $strTable,
        $strTabField,
        $strTemplKey,
        $strLinkTable,
        $intModeId = 0,
        $intTypeId = -9,
        $intExclId = -9,
        $strRefresh = ''
    ) {
        // Compute option value
        $intOption      = 2;
        $intRefresh     = 0;
        $intReturn      = 1;
        $arrSelectedAdd = array();
        $arrData        = array();
        $booSelAdd      = false;
        if ($strLinkTable == 'tbl_lnkServicegroupToService') {
            $intOption = 3;
        }
        if ($strLinkTable == 'tbl_lnkServicedependencyToService_DS') {
            $intOption = 4;
        }
        if ($strLinkTable == 'tbl_lnkServicedependencyToService_S') {
            $intOption = 5;
        }
        if ($strLinkTable == 'tbl_lnkServiceescalationToService') {
            $intOption = 6;
        }
        if ($strTemplKey  == 'host_services') {
            $intOption = 8;
        }
        if ($strTemplKey  == 'service_parents') {
            $intOption = 9;
        }
        if (($strLinkTable == 'tbl_lnkServiceToService') || ($strLinkTable == 'tbl_lnkServicetemplateToService')) {
            $intOption = 10;
        }
        // Get version
        $this->myConfigClass->getDomainData('version', $intVersion);
        // Get raw data
        $booRaw = $this->getSelectRawdata($strTable, $strTabField, $arrData, $intOption);
        // Get selected data
        $booSel = $this->getSelectedItems($strLinkTable, $arrSelected, $intOption);
        // Get additional selected data
        if ($strLinkTable == 'tbl_lnkHostToHostgroup') {
            $booSelAdd = $this->getSelectedItems('tbl_lnkHostgroupToHost', $arrSelectedAdd, 8);
        }
        if ($strLinkTable == 'tbl_lnkHostgroupToHost') {
            $booSelAdd = $this->getSelectedItems('tbl_lnkHostToHostgroup', $arrSelectedAdd, 8);
        }
        // Get browser
        $strBrowser = $this->browserCheck();
        // Refresh processing (replaces selection array)
        if ($strRefresh != '' && isset($this->arrSession['refresh']) &&
            isset($this->arrSession['refresh'][$strRefresh]) &&
            \is_array($this->arrSession['refresh'][$strRefresh])) {
            $arrSelected = $this->arrSession['refresh'][$strRefresh];
            $intRefresh  = 1;
            $booSel      = 0;
        }
        if ($booRaw == 0) {
            $intCount = 0;
            // Insert an empty line in mode 1
            if ($intModeId == 1) {
                $this->myContentTpl->setVariable('SPECIAL_STYLE', '');
                $this->myContentTpl->setVariable('OPTION_DISABLED', '');
                if (($strBrowser == 'msie') && ($this->arrSettings['common']['seldisable'] != 0)) {
                    $this->myContentTpl->setVariable('OPTION_DISABLED', 'disabled="disabled"');
                }
                $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey), '&nbsp;');
                $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey).'_ID', 0);
                if ($intVersion < 3) {
                    $this->myContentTpl->setVariable('VERSION_20_MUST', 'inpmust');
                }
                $this->myContentTpl->parse($strTemplKey);
                $intCount++;
            }
            // Insert an * line in mode 2
            if ($intModeId == 2) {
                $this->myContentTpl->setVariable('SPECIAL_STYLE', '');
                $this->myContentTpl->setVariable('OPTION_DISABLED', '');
                if (($strBrowser == 'msie') && ($this->arrSettings['common']['seldisable'] != 0)) {
                    $this->myContentTpl->setVariable('OPTION_DISABLED', 'disabled="disabled"');
                }
                $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey), '*');
                $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey).'_ID', '*');
                if ($intVersion < 3) {
                    $this->myContentTpl->setVariable('VERSION_20_MUST', 'inpmust');
                }
                if ($intTypeId  == 2) {
                    $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey). '_SEL', 'selected');
                }
                if (($intRefresh == 1) && \in_array('*', $arrSelected, true)) {
                    $this->myContentTpl->setVariable('DAT_' .strtoupper($strTemplKey). '_SEL', 'selected');
                    $this->myContentTpl->setVariable('IE_' .strtoupper($strTemplKey). '_SEL', 'ieselected');
                }
                $intCount++;
                $this->myContentTpl->parse($strTemplKey);
            }
            // Insert data sets
            foreach ($arrData as $elem) {
                if ($elem['key'] == $intExclId) {
                    continue;
                }
                if (( $intOption == 10) && (strstr($elem['key'], '-', true) == $intExclId)) {
                    continue;
                }
                if ($elem['value'] == '') {
                    continue;
                }
                $intIsSelected = 0;
                $intIsExcluded = 0;
                $intIsForeign  = 0;
                $this->myContentTpl->setVariable('SPECIAL_STYLE', '');
                $this->myContentTpl->setVariable('OPTION_DISABLED', '');
                if (($strBrowser == 'msie') && ($this->arrSettings['common']['seldisable'] != 0)) {
                    $this->myContentTpl->setVariable('OPTION_DISABLED', 'disabled="disabled"');
                }
                if (isset($elem['active']) && $elem['active'] == 0) {
                    $strActive=' [inactive]';
                    $this->myContentTpl->setVariable('SPECIAL_STYLE', 'inactive_option');
                } else {
                    $strActive = '';
                }
                if (isset($elem['config_id']) && $elem['config_id'] == 0) {
                    $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey), htmlspecialchars(
                            $elem['value'],
                            ENT_QUOTES,
                            'UTF-8'
                        ).' [common]'.$strActive);
                } else {
                    $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey), htmlspecialchars(
                            $elem['value'],
                            ENT_QUOTES,
                            'UTF-8'
                        ).$strActive);
                }
                $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey). '_ID', $elem['key']);
                $this->myContentTpl->setVariable('CLASS_SEL', '');
                if ($intVersion < 3) {
                    $this->myContentTpl->setVariable('VERSION_20_MUST', 'inpmust');
                }
                if (($booSel == 0) && \in_array($elem['key'], $arrSelected, true)) {
                    $intIsSelected = 1;
                }
                if (($booSel == 0) && \in_array($elem['value'], $arrSelected, true)) {
                    $intIsSelected = 1;
                }
                if ($booSelAdd !== null && ($booSelAdd == 0) && \in_array($elem['key'], $arrSelectedAdd, true)) {
                    $intIsForeign = 1;
                }
                if ($booSelAdd !== null && ($booSelAdd == 0) && \in_array($elem['value'], $arrSelectedAdd, true)) {
                    $intIsForeign = 1;
                }
                if (($intIsForeign == 1) && ($strActive == '')) {
                    $this->myContentTpl->setVariable('SPECIAL_STYLE', 'foreign_option');
                }
                // Exclude rule
                if (($booSel == 0) && \in_array('e' . $elem['key'], $arrSelected, true)) {
                    $intIsExcluded = 1;
                }
                if (($booSel == 0) && \in_array('e' . '::' . $elem['value'], $arrSelected, true)) {
                    $intIsExcluded = 1;
                }
                if ($intIsExcluded == 1) {
                    if (isset($elem['config_id']) && $elem['config_id'] == 0) {
                        $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey), '!'.
                            htmlspecialchars($elem['value'], ENT_QUOTES, 'UTF-8').' [common]'.$strActive);
                    } else {
                        $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey), '!'.
                            htmlspecialchars($elem['value'], ENT_QUOTES, 'UTF-8').$strActive);
                    }
                    $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey). '_ID', 'e'.$elem['key']);
                }
                if (($intIsSelected == 1) || ($intIsExcluded == 1)) {
                    $this->myContentTpl->setVariable('DAT_' .strtoupper($strTemplKey). '_SEL', 'selected');
                    $this->myContentTpl->setVariable('IE_' .strtoupper($strTemplKey). '_SEL', 'ieselected');
                }
                $intCount++;
                $this->myContentTpl->parse($strTemplKey);
            }
            if ($intCount == 0) {
                // Insert an empty line to create valid HTML select fields
                $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey), '&nbsp;');
                $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey).'_ID', 0);
                $this->myContentTpl->parse($strTemplKey);
            }
            $intReturn = 0;
        } else {
            // Insert an empty line to create valid HTML select fields
            $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey), '&nbsp;');
            $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey).'_ID', 0);
            $this->myContentTpl->parse($strTemplKey);
        }
        return $intReturn;
    }

    /**
     * Merge message strings and check for duplicate messages
     * @param string $strNewMessage             Message to add
     * @param string $strOldMessage             Modified message string (by reference)
     * @param string $strSeparate               Separate string (<br> or \n)
     */
    public function processMessage($strNewMessage, &$strOldMessage, $strSeparate = '<br>')
    {
        $strNewMessage = str_replace(array('::::', '::'), array('::', $strSeparate), $strNewMessage);
        if (($strOldMessage != '') && ($strNewMessage != '')) {
            if (substr_count($strOldMessage, $strNewMessage) == 0) {
                if (substr_count(substr($strOldMessage, -5), $strSeparate) == 0) {
                    $strOldMessage .= $strSeparate.$strNewMessage;
                } else {
                    $strOldMessage .= $strNewMessage;
                }
            }
        } else {
            $strOldMessage .= $strNewMessage;
        }
    }

    /**
     * Returns an SQL fragment based on group access type
     * @param string $strType                   Access type (read,write,link)
     * @return string                           SQL fragment for group selection
     */
    private function getGroupValue($strType)
    {
        // Define variables
        $strTypeValue = '';
        // Select SQL by type
        switch ($strType) {
            case 'read':
                $strTypeValue = "`read`='1'";
                break;
            case 'write':
                $strTypeValue = "`write`='1'";
                break;
            case 'link':
                $strTypeValue = "`link`='1'";
                break;
        }
        return $strTypeValue;
    }

    /**
     * Recursive function to build the main menu
     * @param int intTopId                      ID of top menu point
     * @param string $strCSS                    CSS class
     * @param int $intCntId                     Menu group ID
     * @param string $strMenuHTML               HTML menu string (by Reference)
     * @return bool
     */
    private function hasMenuRecursive($intTopId, $strCSS, $intCntId, &$strMenuHTML)
    {
        // Define variables
        $intLevel  = substr_count($strCSS, '_sub') + 1;
        $booReturn = false;
        $arrData   = array();
        // Define SQL
        $strSQL = 'SELECT mnuId, mnuName, mnuTopId, mnuLink FROM tbl_menu ' .
            "WHERE mnuTopId=$intTopId AND mnuCntId=$intCntId AND mnuActive <> 0 AND ".
            'mnuGrpId IN (' .$this->getAccessGroups('read'). ') ORDER BY mnuOrderId';
        $booRet = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if (($booRet != false) && ($intDataCount != 0)) {
            $strTemp   = '';
            // Menu items
            foreach ($arrData as $elem) {
                $strName  = translate($elem['mnuName']);
                $strLink  = $this->arrSettings['path']['base_url'].$elem['mnuLink'];
                $strTemp .= '  <tr>'."\n";
                if (($elem['mnuId'] == $this->intPageId) || ($this->isMenuActive($elem['mnuId']) == true)) {
                    $strTemp  .= '    <td class="'.$strCSS.'_act">';
                    $strTemp  .= '<a href="'.$strLink.'">'.$strName.'</a></td>'."\n";
                    $booReturn = true;
                } else {
                    $strTemp  .= '    <td class="'.$strCSS.'">';
                    $strTemp  .= '<a href="'.$strLink.'">'.$strName.'</a></td>'."\n";
                }
                $strTemp .= '  </tr>'."\n";
                // Recursive call to get submenu items
                if ((($elem['mnuId'] == $this->intPageId) || ($this->isMenuActive($elem['mnuId']) == true)) &&
                    $this->hasMenuRecursive($elem['mnuId'], $strCSS . '_sub', $intCntId, $strTemp) == true) {
                    $booReturn = true;
                }
                if ($intTopId == $this->intPageId) {
                    $booReturn = true;
                }
            }
            if ($booReturn == true) {
                $strMenuHTML .= $strTemp;
            } elseif ($intLevel == 1) {
                $strMenuHTML .= $strTemp;
            }
        } else {
            $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
        }
        return $booReturn;
    }

    /**
     * Check if menu point is selected
     * @param int $intMenuId                    Menu ID
     * @return bool                             true if active
     */
    public function isMenuActive($intMenuId)
    {
        $booReturn = false;
        $arrData   = array();
        $strSQL = 'SELECT mnuTopId FROM tbl_menu WHERE mnuId=' .$this->intPageId. ' AND mnuActive <> 0 ' .
            'AND mnuGrpId IN (' .$this->getAccessGroups('read'). ')';
        $booRet = $this->myDBClass->hasDataArray($strSQL, $arrData, $intDataCount);
        if (($booRet != false) && ($intDataCount != 0)) {
            foreach ($arrData as $elem) {
                if ($elem['mnuTopId'] == $intMenuId) {
                    $booReturn = true;
                }
            }
        } else {
            $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
        }
        return $booReturn;
    }

    /**
     * Get raw table data
     * @param string $strTable                  Data table name
     * @param string $strTabField               Data field name
     * @param array $arrData                    Raw data array (by reference)
     * @param int $intOption                    Option value
     * @return int                              0 = successful / 1 = error
     */
    public function getSelectRawdata($strTable, $strTabField, &$arrData, $intOption = 0)
    {
        // Define variables
        $arrDataRaw   = array();
        $intDataCount = 0;
        $intReturn    = 0;
        $intDouble    = 0;
        // Get link rights
        $strAccess = $this->getAccessGroups('link');
        // Common domain is enabled?
        $this->myConfigClass->getDomainData('enable_common', $intCommonEnable);
        if ($intCommonEnable == 1) {
            $strDomainWhere1 = ' (`config_id`=' .$this->intDomainId. ' OR `config_id`=0) ';
            $strDomainWhere2 = ' (`tbl_service`.`config_id`=' .$this->intDomainId. ' OR `tbl_service`.`config_id`=0) ';
        } else {
            $strDomainWhere1 = ' `config_id`=' .$this->intDomainId. ' ';
            $strDomainWhere2 = ' `tbl_service`.`config_id`=' .$this->intDomainId. ' ';
        }
        // Define SQL commands
        if ($strTable == 'tbl_group') {
            $strSQL = $this->getRawDataSQLGroup($strTabField);
        } elseif (($strTable == 'tbl_configtarget') || ($strTable == 'tbl_datadomain') ||
            ($strTable == 'tbl_language')) {
            $strSQL = $this->getRawDataSQLDomain($strTable, $strTabField);
        } elseif ($strTable == 'tbl_command') {
            $strSQL = $this->getRawDataSQLCommand($strTabField, $strDomainWhere1, $strAccess, $intOption);
        } elseif (($strTable == 'tbl_timeperiod') && ($strTabField == 'name')) {
            $strSQL = $this->getRawDataSQLTimeperiod($strDomainWhere1, $strAccess);
        } elseif (($strTable == 'tbl_service') && ($intOption == 3)) {
            $strSQL = $this->getRawDataSQLService3($strDomainWhere2, $strAccess);
            $intDouble = 1;
        } elseif (($strTable == 'tbl_service') && (($intOption == 4) || ($intOption == 5) || ($intOption == 6))) {
            $strSQL = $this->getRawDataSQLService456($strTabField, $intOption, $strDomainWhere1, $strAccess);
        } elseif (($strTable == 'tbl_service') && ($intOption == 7)) {
            if (isset($this->arrSession['refresh']) && isset($this->arrSession['refresh']['se_host'])) {
                $intHostId = $this->arrSession['refresh']['se_host'];
                $strSQL = $this->getRawDataSQLService7($strTabField, $strDomainWhere1, $intHostId, $strAccess);
            } else {
                $strSQL = '';
            }
        } elseif ((($strTable == 'tbl_service') || ($strTable == 'tbl_servicetemplate')) &&
            (($intOption == 8) || ($intOption == 9))) {
            // Service selection inside Host definition
            $strSQL = $this->getRawDataSQLService89($strDomainWhere1, $strAccess);
        } elseif ((($strTable == 'tbl_service') || ($strTable == 'tbl_servicetemplate')) &&
            ($intOption == 10)) {
            // Service selection inside Host definition
            $strSQL = $this->getRawDataSQLService10($strDomainWhere2, $strAccess);
        } else {
            // Common statement
            $strSQL = $this->getRawDataSQLCommon($strTable, $strTabField, $strDomainWhere1, $strAccess);
        }
        // Process data
        if ($strSQL != '') {
            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrDataRaw, $intDataCount);
            if ($booReturn == false) {
                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
                $intReturn = 1;
            }
            if ($intDouble == 1) {
                $arrDataRawTemp = array();
                $arrKey = array();
                foreach ($arrDataRaw AS $elem) {
                    if (!isset($arrKey[$elem['key']])) {
                        $arrKey[$elem['key']] = 1;
                        $arrDataRawTemp[] = $elem;
                    }
                }
                $arrDataRaw = $arrDataRawTemp;
            }
        }
        if ($strTable == 'tbl_group') {
            $arrTemp = array();
            $arrTemp['key']   = 0;
            $arrTemp['value'] = translate('Unrestricted access');
            $arrData[] = $arrTemp;
        }
        if (($intReturn == 0) && ($intDataCount != 0)) {
            foreach ($arrDataRaw as $elem) {
                $arrData[] = $elem;
            }
        } elseif ($strTable != 'tbl_group') {
            $arrData = array('key' => 0, 'value' => 'no data');
            $intReturn = 1;
        }
        return $intReturn;
    }

    /**
     * Inserts the domain list to the list view template (host and services only)
     * @param \HTML_Template_IT $resTemplate    Template object
     */
    public function insertDomainList($resTemplate)
    {
        $arrDataDomain = array();
        $strSQL    = "SELECT * FROM `tbl_datadomain` WHERE `active` <> '0' ORDER BY `domain`";
        $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrDataDomain, $intDataCount);
        if ($booReturn && ($intDataCount != 0)) {
            foreach ($arrDataDomain as $elem) {
                // Check access rights
                if ($this->checkAccountGroup($elem['access_group'], 'read') == 0) {
                    $resTemplate->setVariable('DOMAIN_ID', $elem['id']);
                    $resTemplate->setVariable('DOMAIN_NAME', $elem['domain']);
                    if ($this->intDomainId == $elem['id']) {
                        $resTemplate->setVariable('DOMAIN_SEL', 'selected');
                    }
                    $resTemplate->parse('domainlist');
                }
            }
        } elseif (!$booReturn) {
            $this->strErrorMessage .= translate('Error while selecting data from database:').
                '::' .$this->myDBClass->strErrorMessage;
        }
    }

    /**
     * Adds a "/" after a parh string and replaces double "//" with "/"
     * @param string $strPath                   Path string
     * @return string                           Modified path string
     */
    public function addSlash($strPath)
    {
        if ($strPath == '') {
            return '';
        }
        $strPath .= '/';
        while (substr_count($strPath, '//') != 0) {
            $strPath = str_replace('//', '/', $strPath);
        }
        return $strPath;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////
    //  Function: Process "null" values
    ///////////////////////////////////////////////////////////////////////////////////////////
    //
    //  Replaces "NULL" with -1
    //
    //  Parameters:                      $strKey                    Process string
    //
    //  Return value:                    Modified process string
    //
    ///////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Replaces "NULL" with -1
     * @param string $strKey                    Process string
     * @return string                           Modified process string
     */
    public function checkNull($strKey)
    {
        $strReturn = $strKey;
        if (strtoupper($strKey) == 'NULL') {
            $strReturn = -1;
        }
        return $strReturn;
    }


    // PRIVATE functions

    /**
     * Define SQL commands for group table
     * @param string $strTabField               Table field
     * @return string                           SQL Statement
     */
    private function getRawDataSQLGroup($strTabField)
    {
        $strSQL = 'SELECT `id` AS `key`, `' . $strTabField . '` AS `value`, `active` ' .
            "FROM `tbl_group` WHERE `active`='1' AND `" . $strTabField . "` <> '' ".
            'AND `' . $strTabField . '` IS NOT NULL ORDER BY `' . $strTabField . '`';
        return $strSQL;
    }

    /**
     * Define SQL commands for configtarget, datadomain and language table
     * @param string $strTable                  Table name
     * @param string $strTabField               Table field
     * @return string                           SQL Statement
     */
    private function getRawDataSQLDomain($strTable, $strTabField)
    {
        $strSQL = 'SELECT `id` AS `key`, `' . $strTabField . '` AS `value`, `active` ' .
            'FROM `' . $strTable . '` WHERE `' . $strTabField . "` <> '' AND `" . $strTabField .
            '` IS NOT NULL ORDER BY `' . $strTabField . '`';
        return $strSQL;
    }

    /**
     * Define SQL commands for command table
     * @param string $strTabField               Table field
     * @param string $strDomainWhere1           WHERE SQL domain part
     * @param string $strAccess                 Access groups
     * @param int $intOption                    Command type option
     * @return string                           SQL Statement
     */
    private function getRawDataSQLCommand($strTabField, $strDomainWhere1, $strAccess, $intOption)
    {
        $strSQL = 'SELECT `id` AS `key`, `' . $strTabField . '` AS `value`, `config_id`, `active` ' .
            "FROM `tbl_command` WHERE $strDomainWhere1 AND `" . $strTabField . "` <> '' AND `" .
            $strTabField . "` IS NOT NULL AND `access_group` IN ($strAccess) AND (`command_type` = 0 ".
            'OR `command_type` = ' . $intOption . ') ORDER BY `' . $strTabField . '`';
        return $strSQL;
    }

    /**
     * Define SQL commands for timeperiod table
     * @param string $strDomainWhere1           WHERE SQL domain part
     * @param string $strAccess                 Access groups
     * @return string                           SQL Statement
     */
    private function getRawDataSQLTimeperiod($strDomainWhere1, $strAccess)
    {
        $strSQL = 'SELECT `id` AS `key`, `name` AS `value`, `config_id`, `active` ' .
            "FROM `tbl_timeperiod` WHERE $strDomainWhere1 AND `name` <> '' AND `name` IS NOT NULL ".
            "AND `access_group` IN ($strAccess) ORDER BY value";
        return $strSQL;
    }

    /**
     * Define SQL commands for service table
     * @param string $strDomainWhere2           WHERE SQL domain part
     * @param string $strAccess                 Access groups
     * @return string                           SQL Statement
     */
    private function getRawDataSQLService3($strDomainWhere2, $strAccess)
    {
        $strSQLPart1 = "WHERE $strDomainWhere2 AND `tbl_service`.`service_description` <> '' " .
            'AND `tbl_service`.`service_description` IS NOT NULL AND `tbl_service`.`hostgroup_name` <> 0  ' .
            "AND `tbl_service`.`access_group` IN ($strAccess) ";
        $strSQL = "SELECT CONCAT_WS('::',`tbl_host`.`id`,'0',`tbl_service`.`id`) AS `key`, " .
            "CONCAT('H:',`tbl_host`.`host_name`,',',`tbl_service`.`service_description`) AS `value`, " .
            '`tbl_service`.`active` FROM `tbl_service` ' .
            'LEFT JOIN `tbl_lnkServiceToHost` ON `tbl_service`.`id` = `tbl_lnkServiceToHost`.`idMaster` ' .
            'LEFT JOIN `tbl_host` ON `tbl_lnkServiceToHost`.`idSlave` = `tbl_host`.`id` ' .
            str_replace('hostgroup_name', 'host_name', $strSQLPart1) .
            'UNION ' .
            "SELECT CONCAT_WS('::','0',`tbl_hostgroup`.`id`,`tbl_service`.`id`) AS `key`, " .
            "CONCAT('HG:',`tbl_hostgroup`.`hostgroup_name`,',',`tbl_service`.`service_description`) " .
            'AS `value`, `tbl_service`.`active` FROM `tbl_service` ' .
            'LEFT JOIN `tbl_lnkServiceToHostgroup` ON `tbl_service`.`id`=`tbl_lnkServiceToHostgroup`.`idMaster`' .
            'LEFT JOIN `tbl_hostgroup` ON `tbl_lnkServiceToHostgroup`.`idSlave` = `tbl_hostgroup`.`id` ' .
            $strSQLPart1 .
            'UNION ' .
            "SELECT CONCAT_WS('::',`tbl_host`.`id`,'0',`tbl_service`.`id`) AS `key`, " .
            "CONCAT('HHG:',`tbl_host`.`host_name`,',',`tbl_service`.`service_description`) AS `value`, " .
            '`tbl_service`.`active` FROM `tbl_service` ' .
            'LEFT JOIN `tbl_lnkServiceToHostgroup` ON `tbl_service`.`id`=`tbl_lnkServiceToHostgroup`.`idMaster`' .
            'LEFT JOIN `tbl_lnkHostgroupToHost` ON `tbl_lnkHostgroupToHost`.`idMaster` = ' .
            '`tbl_lnkServiceToHostgroup`.`idSlave` ' .
            'LEFT JOIN `tbl_host` ON `tbl_lnkHostgroupToHost`.`idSlave` = `tbl_host`.`id` ' .
            $strSQLPart1 .
            'UNION ' .
            "SELECT CONCAT_WS('::',`tbl_host`.`id`,'0',`tbl_service`.`id`) AS `key`, " .
            "CONCAT('HGH:',`tbl_host`.`host_name`,',',`tbl_service`.`service_description`) AS `value`, " .
            '`tbl_service`.`active` FROM `tbl_service` ' .
            'LEFT JOIN `tbl_lnkServiceToHostgroup` ON `tbl_service`.`id`=`tbl_lnkServiceToHostgroup`.`idMaster` ' .
            'LEFT JOIN `tbl_lnkHostToHostgroup` ON `tbl_lnkHostToHostgroup`.`idSlave` = ' .
            '`tbl_lnkServiceToHostgroup`.`idSlave` ' .
            'LEFT JOIN `tbl_host` ON `tbl_lnkHostToHostgroup`.`idMaster` = `tbl_host`.`id` ' .
            $strSQLPart1 .
            'ORDER BY value';
        return $strSQL;
    }

    /**
     * Define SQL commands for service table
     * @param string $strDomainWhere1           WHERE SQL domain part
     * @param string $elem                      Host array
     * @param string $strAccess                 Access groups
     * @return string                           SQL Statement
     */
    private function getRawDataSQLService4($strDomainWhere1, $elem, $strAccess)
    {
        $strSQL = 'SELECT `id`, `service_description` FROM `tbl_service` ' .
            'LEFT JOIN `tbl_lnkServiceToHost` ON `tbl_service`.`id` = `tbl_lnkServiceToHost`.`idMaster` ' .
            "WHERE $strDomainWhere1 AND `tbl_lnkServiceToHost`.`idSlave` = $elem AND `service_description`<>'' ".
            "AND `service_description` IS NOT NULL AND `access_group` IN ($strAccess) " .
            'UNION ' .
            'SELECT `id`, `service_description` FROM `tbl_service` ' .
            'LEFT JOIN `tbl_lnkServiceToHostgroup` ON `tbl_service`.`id`=`tbl_lnkServiceToHostgroup`.`idMaster` ' .
            'LEFT JOIN `tbl_lnkHostToHostgroup` ON `tbl_lnkServiceToHostgroup`.`idSlave` = ' .
            '`tbl_lnkHostToHostgroup`.`idSlave` ' .
            "WHERE $strDomainWhere1 AND `tbl_lnkHostToHostgroup`.`idMaster`=$elem AND `service_description`<>'' ".
            " AND `service_description` IS NOT NULL AND `access_group` IN ($strAccess) ".
            'UNION ' .
            'SELECT `id`, `service_description` FROM `tbl_service` ' .
            'LEFT JOIN `tbl_lnkServiceToHostgroup` ON `tbl_service`.`id`=`tbl_lnkServiceToHostgroup`.`idMaster` ' .
            'LEFT JOIN `tbl_lnkHostgroupToHost` ON `tbl_lnkServiceToHostgroup`.`idSlave` = ' .
            '`tbl_lnkHostgroupToHost`.`idMaster` ' .
            "WHERE $strDomainWhere1 AND `tbl_lnkHostgroupToHost`.`idSlave`=$elem AND `service_description`<>'' ".
            "AND `service_description` IS NOT NULL AND `access_group` IN ($strAccess)";
        return $strSQL;
    }

    /**
     * Define SQL commands for service table
     * @param string $strDomainWhere1           WHERE SQL domain part
     * @param string $elem                      Hostgroup array
     * @param string $strAccess                 Access groups
     * @return string                           SQL Statement
     */
    private function getRawDataSQLService5($strDomainWhere1, $elem, $strAccess)
    {
        $strSQL = 'SELECT `id`, `service_description` FROM `tbl_service` ' .
            'LEFT JOIN `tbl_lnkServiceToHostgroup` ON `tbl_service`.`id`=`tbl_lnkServiceToHostgroup`.`idMaster` ' .
            "WHERE $strDomainWhere1 AND `tbl_lnkServiceToHostgroup`.`idSlave` = $elem ".
            "AND `service_description` <> '' AND `service_description` IS NOT NULL AND `access_group` ".
            "IN ($strAccess)";
        return $strSQL;
    }

    /**
     * Define SQL commands for service table
     * @param string $strTabField               Table field
     * @param string $strWhere                  WHERE SQL domain part
     * @param string $strServices               Comma separated list of services
     * @param string $strServicesId             Comma separated list of services IDs
     * @param string $strAccess                 Access groups
     * @return string                           SQL Statement
     */
    private function getRawDataSQLService6($strTabField, $strWhere, $strServices, $strServicesId, $strAccess)
    {
        $strSQL = 'SELECT `id` AS `key`, `' . $strTabField . '` AS `value`, `active` FROM `tbl_service` ' .
            'LEFT JOIN `tbl_lnkServiceToHost` ON `tbl_service`.`id` = `tbl_lnkServiceToHost`.`idMaster` ' .
            "WHERE $strWhere AND `tbl_service`.`service_description` IN ($strServices) ".
            "AND `tbl_service`.`id` IN ($strServicesId) AND `" . $strTabField . "` <> '' AND `" .
            $strTabField . "` IS NOT NULL AND `access_group` IN ($strAccess) GROUP BY `value` ".
            'UNION ' .
            'SELECT `id` AS `key`, `' . $strTabField . '` AS `value`, `active` FROM `tbl_service` ' .
            'LEFT JOIN `tbl_lnkServiceToHostgroup` ON `tbl_service`.`id`=`tbl_lnkServiceToHostgroup`.`idMaster` ' .
            "WHERE $strWhere AND `tbl_service`.`service_description` IN ($strServices) ".
            "AND `tbl_service`.`id` IN ($strServicesId) AND `" . $strTabField . "` <> '' AND `" .
            $strTabField . "` IS NOT NULL AND `access_group` IN ($strAccess) GROUP BY `value` ".
            'UNION ' .
            'SELECT `id` AS `key`, `' . $strTabField . '` AS `value`, `active` FROM `tbl_service` ' .
            "WHERE $strWhere AND `host_name`=2 OR  `hostgroup_name`=2 AND `" . $strTabField . "` <> '' ".
            'AND `' . $strTabField . "` IS NOT NULL AND `access_group` IN ($strAccess) ".
            'GROUP BY `value` ORDER BY `value`';
        return $strSQL;
    }

    /**
     * Define SQL commands for service table
     * @param string $strTabField               Table field
     * @param string $strDomainWhere1           WHERE SQL domain part
     * @param int $intHostId                    Host ID
     * @param string $strAccess                 Access groups
     * @return string                           SQL Statement
     */
    private function getRawDataSQLService7($strTabField, $strDomainWhere1, $intHostId, $strAccess)
    {
        $strSQL = 'SELECT `tbl_service`.`id` AS `key`, `tbl_service`.`' . $strTabField . '` AS `value`, ' .
            '`tbl_service`.`active` FROM `tbl_service` ' .
            'LEFT JOIN `tbl_lnkServiceToHost` ON `tbl_service`.`id` = `tbl_lnkServiceToHost`.`idMaster` ' .
            "WHERE $strDomainWhere1 AND `tbl_lnkServiceToHost`.`idSlave` = $intHostId AND `" . $strTabField .
            "` <> '' AND `" . $strTabField . "` IS NOT NULL AND `access_group` IN ($strAccess) ".
            'ORDER BY `' . $strTabField . '`';
        return $strSQL;
    }

    /**
     * Define SQL commands for service table
     * @param string $strDomainWhere1           WHERE SQL domain part
     * @param string $strAccess                 Access groups
     * @return string                           SQL Statement
     */
    private function getRawDataSQLService89($strDomainWhere1, $strAccess)
    {
        $strSQL = "SELECT `tbl_service`.`id` AS `key`, CONCAT(`tbl_service`.`config_name`, ' - ', ".
            '`tbl_service`.`service_description`) AS `value`, `active` ' .
            "FROM `tbl_service` WHERE $strDomainWhere1 AND `tbl_service`.`config_name` <> '' ".
            "AND `tbl_service`.`config_name` IS NOT NULL AND `tbl_service`.`service_description` <> '' ".
            "AND `tbl_service`.`service_description` IS NOT NULL AND `access_group` IN ($strAccess) ".
            'ORDER BY `value`';
        return $strSQL;
    }

    /**
     * Define SQL commands for service table
     * @param string $strDomainWhere2           WHERE SQL domain part for services
     * @param string $strAccess                 Access groups
     * @return string                           SQL Statement
     */
    private function getRawDataSQLService10($strDomainWhere2, $strAccess)
    {
        $strSQL = 'SELECT CONCAT(tbl_service.id, "-", tbl_host.id) AS `key`, CONCAT(tbl_host.host_name, " - ", '
            . 'tbl_service.service_description) AS `value`, tbl_service.active '
            . 'FROM tbl_service '
            . 'LEFT JOIN tbl_lnkServiceToHost ON tbl_service.id=tbl_lnkServiceToHost.idMaster '
            . 'LEFT JOIN tbl_host ON tbl_lnkServiceToHost.idSlave=tbl_host.id '
            . 'WHERE '.$strDomainWhere2.' AND tbl_service.service_description <> "" '
            . 'AND tbl_service.service_description IS NOT NULL AND tbl_host.host_name IS NOT NULL '
            . 'AND tbl_service.access_group IN ('.$strAccess.') '
            . 'UNION '
            . 'SELECT CONCAT(tbl_service.id, "-", tbl_host.id) AS `key`, CONCAT(tbl_host.host_name, " - ", '
            . 'tbl_service.service_description) AS `value`, tbl_service.active '
            . 'FROM tbl_service '
            . 'LEFT JOIN tbl_lnkServiceToHostgroup ON tbl_service.id=tbl_lnkServiceToHostgroup.idMaster '
            . 'LEFT JOIN tbl_lnkHostgroupToHost ON tbl_lnkServiceToHostgroup.idSlave = '
            . 'tbl_lnkHostgroupToHost.idMaster '
            . 'LEFT JOIN tbl_host ON tbl_lnkHostgroupToHost.idSlave=tbl_host.id '
            . 'WHERE '.$strDomainWhere2.' AND tbl_service.service_description <> "" '
            . 'AND tbl_service.service_description IS NOT NULL AND tbl_host.host_name IS NOT NULL '
            . 'AND tbl_service.access_group IN ('.$strAccess.') '
            . 'ORDER BY `value`';
        return $strSQL;
    }

    /**
     * Define SQL commands for common tables
     * @param string $strTable                  Table name
     * @param string $strTabField               Table field
     * @param string $strDomainWhere1           WHERE SQL domain part
     * @param string $strAccess                 Access groups
     * @return string                           SQL Statement
     */
    private function getRawDataSQLCommon($strTable, $strTabField, $strDomainWhere1, $strAccess)
    {
        $strSQL = 'SELECT `id` AS `key`, `' . $strTabField . '` AS `value`, `config_id`, `active` ' .
            'FROM `' . $strTable . "` WHERE $strDomainWhere1 AND `" . $strTabField . "` <> '' ".
            'AND `' . $strTabField . "` IS NOT NULL AND `access_group` IN ($strAccess) ".
            'ORDER BY `' . $strTabField . '`';
        return $strSQL;
    }

    /**
     * Define SQL commands for service table
     * @param string $strTabField               Table field
     * @param int $intOption                    Option ID
     * @param string $strDomainWhere1           WHERE SQL domain part
     * @param string $strAccess                 Access groups
     * @return string                           SQL Statement
     */
    private function getRawDataSQLService456($strTabField, $intOption, $strDomainWhere1, $strAccess)
    {
        // Define variables
        if ($intOption == 6) {
            $strHostVar      = 'se_host';
            $strHostGroupVar = 'se_hostgroup';
        } elseif ($intOption == 4) {
            $strHostVar      = 'sd_dependent_host';
            $strHostGroupVar = 'sd_dependent_hostgroup';
        } else {
            $strHostVar      = 'sd_host';
            $strHostGroupVar = 'sd_hostgroup';
        }
        if (!isset($this->arrSession['refresh'])) {
            $this->arrSession['refresh'] = array();
        }
        $arrHosts         = array();
        $arrHostgroups    = array();
        $arrServices      = array();
        $arrDataHost      = array();
        $arrDataTmp       = array();
        $arrHostTemp      = array();
        $arrHostgroupTemp = array();
        $arrServicesId    = array();
        $intDCHost        = 0;
        $intDataTmp       = 0;
        // Refresh mode - fill arrays
        if (isset($this->arrSession['refresh'][$strHostVar]) &&
            \is_array($this->arrSession['refresh'][$strHostVar])) {
            $arrHosts = $this->arrSession['refresh'][$strHostVar];
        } else {
            if ($intOption == 4) {
                $strSQL = 'SELECT `idSlave` FROM `tbl_lnkServicedependencyToHost_DH` '
                    . 'WHERE `idMaster`=' . $this->intDataId;
            } elseif ($intOption == 6) {
                $strSQL = 'SELECT `idSlave` FROM `tbl_lnkServiceescalationToHost` '
                    . 'WHERE `idMaster`=' . $this->intDataId;
            } else {
                $strSQL = 'SELECT `idSlave` FROM `tbl_lnkServicedependencyToHost_H` '
                    . 'WHERE `idMaster`=' .$this->intDataId;
            }
            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrDataHost, $intDCHost);
            if ($booReturn == false) {
                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
            } elseif ($intDCHost != 0) {
                $arrHostTemp = array();
                foreach ($arrDataHost as $elem) {
                    $arrHostTemp[] = $elem['idSlave'];
                }
                $arrHosts = $arrHostTemp;
            }
        }
        if (isset($this->arrSession['refresh'][$strHostGroupVar]) &&
            \is_array($this->arrSession['refresh'][$strHostGroupVar])) {
            $arrHostgroups = $this->arrSession['refresh'][$strHostGroupVar];
        } else {
            if ($intOption == 4) {
                $strSQL = 'SELECT `idSlave` FROM `tbl_lnkServicedependencyToHostgroup_DH` '
                    . 'WHERE `idMaster`=' .$this->intDataId;
            } elseif ($intOption == 6) {
                $strSQL = 'SELECT `idSlave` FROM `tbl_lnkServiceescalationToHostgroup` '
                    . 'WHERE `idMaster`=' . $this->intDataId;
            } else {
                $strSQL = 'SELECT `idSlave` FROM `tbl_lnkServicedependencyToHostgroup_H` '
                    . 'WHERE `idMaster`=' .$this->intDataId;
            }
            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrDataHost, $intDCHost);
            if ($booReturn == false) {
                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
            } elseif ($intDCHost != 0) {
                $arrHostgroupTemp = array();
                foreach ($arrDataHost as $elem) {
                    $arrHostgroupTemp[] = $elem['idSlave'];
                }
                $arrHostgroups = $arrHostgroupTemp;
            }
        }
        if (\is_array($arrHosts) && (\count($arrHosts) == 1) && $arrHosts[0] == '') {
            $arrHosts = array();
        }
        if (\is_array($arrHostgroups) && (\count($arrHostgroups) == 1) && $arrHostgroups[0] == '') {
            $arrHostgroups = array();
        }
        if (\in_array('*', $arrHosts, true)) {
            $strSQL = "SELECT id FROM tbl_host WHERE $strDomainWhere1 AND `access_group` IN ($strAccess)";
            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrDataHost, $intDCHost);
            if ($booReturn == false) {
                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
            }
            if ($booReturn && ($intDCHost != 0)) {
                $arrHostTemp = array();
                foreach ($arrDataHost as $elem) {
                    if (\in_array('e' . $elem['id'], $this->arrSession['refresh'][$strHostVar], true)) {
                        continue;
                    }
                    $arrHostTemp[] = $elem['id'];
                }
            }
            $strHosts = 1;
            $arrHosts = $arrHostTemp;
        } else {
            $strHosts = \count($arrHosts) + 0;
        }
        // * Value in host groups -> disabled in NagiosQL 3.2
        if (\in_array('*', $arrHostgroups, true)) {
            $strSQL = "SELECT id FROM tbl_hostgroup WHERE $strDomainWhere1 AND `access_group` " .
                "IN ($strAccess)";
            $booReturn = $this->myDBClass->hasDataArray($strSQL, $arrDataHost, $intDCHost);
            if ($booReturn == false) {
                $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
            }
            if ($booReturn && ($intDCHost != 0)) {
                $arrHostgroupTemp = array();
                foreach ($arrDataHost as $elem) {
                    if (\in_array('e' . $elem['id'], $this->arrSession['refresh'][$strHostGroupVar], true)) {
                        continue;
                    }
                    $arrHostgroupTemp[] = $elem['id'];
                }
            }
            $strHostsGroup = 1;
            $arrHostgroups = $arrHostgroupTemp;
        } else {
            $strHostsGroup = \count($arrHostgroups) + 0;
        }
        // Special method - only host_name or hostgroup_name selected
        if (($strHostVar == 'sd_dependent_host') && ($strHosts == 0) && ($strHostsGroup == 0)) {
            if (\is_array($this->arrSession['refresh']['sd_host'])) {
                $arrHosts = $this->arrSession['refresh']['sd_host'];
            }
            if (\is_array($this->arrSession['refresh']['sd_hostgroup'])) {
                $arrHostgroups = $this->arrSession['refresh']['sd_hostgroup'];
            }
            if ((\count($arrHosts) == 1) && $arrHosts[0] == '') {
                $arrHosts = array();
            }
            if ((\count($arrHostgroups) == 1) && $arrHostgroups[0] == '') {
                $arrHostgroups = array();
            }
            $strHosts      = \count($arrHosts) + 0;
            $strHostsGroup = \count($arrHostgroups) + 0;
        }
        // If no hosts and hostgroups are selected show any service
        if (($strHosts == 0) && ($strHostsGroup == 0)) {
            $strSQL = 'SELECT `id` AS `key`, `' . $strTabField . '` AS `value`, `active` FROM `tbl_service` ' .
                "WHERE $strDomainWhere1 AND `" . $strTabField . "` <> '' AND `" . $strTabField . '` ' .
                "IS NOT NULL AND `access_group` IN ($strAccess) GROUP BY `value` ORDER BY `value`";
        } else {
            if ($strHosts != 0) {
                $intCounter = 0;
                foreach ($arrHosts as $elem) {
                    if (($intCounter != 0) && (\count($arrServices) == 0)) {
                        continue;
                    }
                    $arrTempServ = array();
                    $arrTempServId = array();
                    $elem = str_replace('e', '', $elem);
                    $strSQLTmp = $this->getRawDataSQLService4($strDomainWhere1, $elem, $strAccess);
                    $booReturn = $this->myDBClass->hasDataArray($strSQLTmp, $arrDataTmp, $intDataTmp);
                    if ($booReturn && ($intDataTmp != 0)) {
                        foreach ($arrDataTmp as $elem2) {
                            if ($intCounter == 0) {
                                $arrTempServ[] = $elem2['service_description'];
                                $arrTempServId[] = $elem2['id'];
                            } elseif (\in_array($elem2['service_description'], $arrServices, true) &&
                                !\in_array($elem2['service_description'], $arrTempServ, true)) {
                                $arrTempServ[] = $elem2['service_description'];
                                $arrTempServId[] = $elem2['id'];
                            }
                        }
                    }
                    $arrServices = $arrTempServ;
                    $arrServicesId = $arrTempServId;
                    $intCounter++;
                }
            }
            if ($strHostsGroup != 0) {
                $intCounter = 0;
                foreach ($arrHostgroups as $elem) {
                    if (($intCounter != 0) && (\count($arrServices) == 0)) {
                        continue;
                    }
                    $arrTempServ = array();
                    $arrTempServId = array();
                    $elem = str_replace('e', '', $elem);
                    $strSQLTmp = $this->getRawDataSQLService5($strDomainWhere1, $elem, $strAccess);
                    $booReturn = $this->myDBClass->hasDataArray($strSQLTmp, $arrDataTmp, $intDataTmp);
                    if ($booReturn && ($intDataTmp != 0)) {
                        foreach ($arrDataTmp as $elem2) {
                            if ($intCounter == 0) {
                                $arrTempServ[] = $elem2['service_description'];
                                $arrTempServId[] = $elem2['id'];
                            } elseif (\in_array($elem2['service_description'], $arrServices, true) &&
                                !\in_array($elem2['service_description'], $arrTempServ, true)) {
                                $arrTempServ[] = $elem2['service_description'];
                                $arrTempServId[] = $elem2['id'];
                            }
                        }
                    }
                    $arrServices = $arrTempServ;
                    $arrServicesId = $arrTempServId;
                    $intCounter++;
                }
            }
            if (\count($arrServices) != 0) {
                $strServices   = "'" . implode("','", $arrServices) . "'";
                $strServicesId = implode(',', $arrServicesId);
                $strSQL = $this->getRawDataSQLService6(
                    $strTabField,
                    $strDomainWhere1,
                    $strServices,
                    $strServicesId,
                    $strAccess
                );
            } else {
                $strSQL = '';
            }
        }
        return $strSQL;
    }

    /**
     * Get selected data
     * @param string $strLinkTable              Link table name
     * @param array $arrSelect                  Result data array
     * @param int $intOption                    Option parameter
     * @return int                              0 = successful / 1 = error
     */
    private function getSelectedItems($strLinkTable, &$arrSelect, $intOption = 0)
    {
        // Define variables
        $arrSelectedRaw = array();
        $intDataCount   = 0;
        $intReturn      = 1;
        // Define SQL commands
        if ($intOption == 8) {
            $strSQL = 'SELECT * FROM `' .$strLinkTable. '` WHERE `idSlave`=' .$this->intDataId;
        } else {
            $strSQL = 'SELECT * FROM `' .$strLinkTable. '` WHERE `idMaster`=' .$this->intDataId;
        }
        // Process data
        $booReturn  = $this->myDBClass->hasDataArray($strSQL, $arrSelectedRaw, $intDataCount);
        if ($booReturn == false) {
            $this->strErrorMessage .= $this->myDBClass->strErrorMessage;
        }
        if ($booReturn && ($intDataCount != 0)) {
            foreach ($arrSelectedRaw as $elem) {
                // Multi tables
                if ($strLinkTable == 'tbl_lnkServicegroupToService') {
                    if (isset($elem['exclude']) && ($elem['exclude'] == 1)) {
                        $arrSelect[] = 'e' .$elem['idSlaveH']. '::' .$elem['idSlaveHG']. '::' .$elem['idSlaveS'];
                    } else {
                        $arrSelect[] = $elem['idSlaveH']. '::' .$elem['idSlaveHG']. '::' .$elem['idSlaveS'];
                    }
                    // Servicedependencies and -escalations
                } elseif (($strLinkTable == 'tbl_lnkServicedependencyToService_DS') ||
                    ($strLinkTable == 'tbl_lnkServicedependencyToService_S') ||
                    ($strLinkTable == 'tbl_lnkServiceescalationToService')) {
                    if (isset($elem['exclude']) && ($elem['exclude'] == 1)) {
                        $arrSelect[] = 'e::' .$elem['strSlave'];
                    } else {
                        $arrSelect[] = $elem['strSlave'];
                    }
                    // Service parents
                } elseif (($strLinkTable == 'tbl_lnkServiceToService') ||
                    ($strLinkTable == 'tbl_lnkServicetemplateToService')) {
                    $arrSelect[] = $elem['idSlave'].'-'.$elem['idHost'];
                    // Standard tables
                } else {
                    if ($intOption == 8) {
                        if (isset($elem['exclude']) && ($elem['exclude'] == 1)) {
                            $arrSelect[] = 'e' .$elem['idMaster'];
                        } else {
                            $arrSelect[] = $elem['idMaster'];
                        }
                    } else {
                        if (isset($elem['exclude']) && ($elem['exclude'] == 1)) {
                            $arrSelect[] = 'e' .$elem['idSlave'];
                        } else {
                            $arrSelect[] = $elem['idSlave'];
                        }
                    }
                }
            }
            $intReturn = 0;
        }
        return $intReturn;
    }
}
