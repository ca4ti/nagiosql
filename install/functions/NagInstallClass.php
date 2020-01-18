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
// Component : Installer Class
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
//// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Class: Common install functions
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Includes all functions used by the installer
//
// Name: naginstall
//
///////////////////////////////////////////////////////////////////////////////////////////////
namespace install\functions;

use functions\MysqliDbClass;

class NagInstallClass
{
    // Define class variables
    public $arrSession           = array();     // Session content

    // Class includes
    /** @var \HTML_Template_IT  */
    public $filTemplate = '';                   // template file
    /** @var MysqliDbClass */
    public $myDBClass;                          // Database class reference

    /**
     * NagINstallClass constructor.
     * @param array $arrSession                 PHP Session array
     */
    public function __construct($arrSession)
    {
        $this->arrSession = $arrSession;
    }

    /**
     * Parse template
     * @param array $arrTemplate                Array including template replacements
     * @param string $strTplFile                Template file
     * @return mixed|string
     */
    public function parseTemplate($arrTemplate, $strTplFile)
    {
        // Open template file
        if (file_exists($strTplFile) && is_readable($strTplFile)) {
            $strTemplate = '';
            $datTplFile = fopen($strTplFile, 'rb');
            while (!feof($datTplFile)) {
                $strTemplate .= fgets($datTplFile);
            }
            foreach ($arrTemplate as $key => $elem) {
                if (substr_count($strTemplate, '{' .$key. '}') != 0) {
                    $strTemplate = str_replace('{' .$key. '}', $elem, $strTemplate);
                }
            }
            return $strTemplate;
        }
        echo $this->translate('Template file not found'). ': ' .$strTplFile;
        return 0;
    }

    /**
     * Translate text
     * @param string $strLangString             String to translate
     * @return string                           Translated string
     */
    public function translate($strLangString)
    {
        $strTemp1 = gettext($strLangString);
        return str_replace(array('"', "'"), array('&quot;', '&#039;'), $strTemp1);
    }

    /**
     * Return supported languages
     * @return array                            Array including supported languages
     */
    public function getLangData()
    {
        $arrLangSupported = array();
        unset($arrLangSupported);
        // English
        $arrLangSupported['en_GB']['description'] = $this->translate('English');
        $arrLangSupported['en_GB']['nativedescription'] = 'English';

        // German
        $arrLangSupported['de_DE']['description'] = $this->translate('German');
        $arrLangSupported['de_DE']['nativedescription'] = 'Deutsch';

        // Chinese (Simplified)
        $arrLangSupported['zh_CN']['description'] = $this->translate('Chinese (Simplified)');
        $arrLangSupported['zh_CN']['nativedescription'] = '&#31616;&#20307;&#20013;&#25991;';

        // Italian
        $arrLangSupported['it_IT']['description'] = $this->translate('Italian');
        $arrLangSupported['it_IT']['nativedescription'] = 'Italiano';

        // French
        $arrLangSupported['fr_FR']['description'] = $this->translate('French');
        $arrLangSupported['fr_FR']['nativedescription'] = 'Fran&#231;ais';

        // Russian
        $arrLangSupported['ru_RU']['description'] = $this->translate('Russian');
        $arrLangSupported['ru_RU']['nativedescription'] = '&#1056;&#1091;&#1089;&#1089;&#1082;&#1080;&#1081;';

        // Spanish
        $arrLangSupported['es_ES']['description'] = $this->translate('Spanish');
        $arrLangSupported['es_ES']['nativedescription'] = 'Espa&#241;ol';

        // Brazilian Portuguese
        $arrLangSupported['pt_BR']['description'] = $this->translate('Portuguese (Brazilian)');
        $arrLangSupported['pt_BR']['nativedescription'] = 'Portugu&#234;s do Brasil';

        // Dutch
        $arrLangSupported['nl_NL']['description'] = $this->translate('Dutch');
        $arrLangSupported['nl_NL']['nativedescription'] = 'Nederlands';

        // Danish
        $arrLangSupported['da_DK']['description'] = $this->translate('Danish');
        $arrLangSupported['da_DK']['nativedescription'] = 'Dansk';

        // No longer supported language due to missing translators
        //
        //  // Japanese
        //  $arrLangSupported['ja_JP']['description'] = $this->translate('Japanese');
        //  $arrLangSupported['ja_JP']['nativedescription'] = '&#x65e5;&#x672c;&#x8a9e;';
        //
        //  // Polish
        //  $arrLangSupported['pl_PL']['description'] = $this->translate('Polish');
        //  $arrLangSupported['pl_PL']['nativedescription'] = 'Polski';
        //
        //  // Spanish (Argentina)
        //  $arrLangSupported['es_AR']['description'] = $this->translate('Spanish (Argentina)');
        //   $arrLangSupported['es_AR']['nativedescription'] = 'Espa&#241;ol Argentina';
        ///
        /// Currently not supported languages
        //
        //  // Albanian
        //  $arrLangSupported['sq']['description'] = $clang->$this->translate('Albanian');
        //  $arrLangSupported['sq']['nativedescription'] = 'Shqipe';
        //
        //  // Basque
        //  $arrLangSupported['eu']['description'] = $this->translate('Basque');
        //  $arrLangSupported['eu']['nativedescription'] = 'Euskara';
        //
        //  // Bosnian
        //  $arrLangSupported['bs']['description'] = $this->translate('Bosnian');
        //  $arrLangSupported['bs']['nativedescription'] =
        //                              '&#x0411;&#x044a;&#x043b;&#x0433;&#x0430;&#x0440;&#x0441;&#x043a;&#x0438;';
        //
        //  // Bulgarian
        //  $arrLangSupported['bg']['description'] = $this->translate('Bulgarian');
        //  $arrLangSupported['bg']['nativedescription'] =
        //                              '&#x0411;&#x044a;&#x043b;&#x0433;&#x0430;&#x0440;&#x0441;&#x043a;&#x0438;';
        //
        //  // Catalan
        //  $arrLangSupported['ca']['description'] = $this->translate('Catalan');
        //  $arrLangSupported['ca']['nativedescription'] = 'Catal&#940;';
        //
        //  // Welsh
        //  $arrLangSupported['cy']['description'] = $this->translate('Welsh');
        //  $arrLangSupported['cy']['nativedescription'] = 'Cymraeg';
        //
        //  // Chinese (Traditional - Hong Kong)
        //  $arrLangSupported['zh-Hant-HK']['description'] = $this->translate('Chinese (Traditional - Hong Kong)');
        //  $arrLangSupported['zh-Hant-HK']['nativedescription'] = '&#32321;&#39636;&#20013;&#25991;&#35486;&#31995;';
        //
        //  // Chinese (Traditional - Taiwan)
        //  $arrLangSupported['zh-Hant-TW']['description'] = $this->translate('Chinese (Traditional - Taiwan)');
        //  $arrLangSupported['zh-Hant-TW']['nativedescription'] = 'Chinese (Traditional - Taiwan)';
        //
        //  // Croatian
        //  $arrLangSupported['hr']['description'] = $this->translate('Croatian');
        //  $arrLangSupported['hr']['nativedescription'] = 'Hrvatski';
        //
        //  // Czech
        //  $arrLangSupported['cs']['description'] = $this->translate('Czech');
        //  $arrLangSupported['cs']['nativedescription'] = '&#x010c;esky';
        //
        //  // Estonian
        //  $arrLangSupported['et']['description'] = $this->translate('Estonian');
        //  $arrLangSupported['et']['nativedescription'] = 'Eesti';
        //
        //  // Finnish
        //  $arrLangSupported['fi']['description'] = $this->translate('Finnish');
        //  $arrLangSupported['fi']['nativedescription'] = 'Suomi';
        //
        //  // Galician
        //  $arrLangSupported['gl']['description'] = $this->translate('Galician');
        //  $arrLangSupported['gl']['nativedescription'] = 'Galego';
        //
        //  // German informal
        //  $arrLangSupported['de-informal']['description'] = $this->translate('German informal');
        //  $arrLangSupported['de-informal']['nativedescription'] = 'Deutsch (Du)';
        //
        //  // Greek
        //  $arrLangSupported['el']['description'] = $this->translate('Greek');
        //  $arrLangSupported['el']['nativedescription'] = '&#949;&#955;&#955;&#951;&#957;&#953;&#954;&#940;';
        //
        //  // Hebrew
        //  $arrLangSupported['he']['description'] = $this->translate('Hebrew');
        //  $arrLangSupported['he']['nativedescription'] = ' &#1506;&#1489;&#1512;&#1497;&#1514;';
        //
        //  // Hungarian
        //  $arrLangSupported['hu']['description'] = $this->translate('Hungarian');
        //  $arrLangSupported['hu']['nativedescription'] = 'Magyar';
        //
        //  // Indonesian
        //  $arrLangSupported['id']['description'] = $this->translate('Indonesian');
        //  $arrLangSupported['id']['nativedescription'] = 'Bahasa Indonesia';
        //
        //  // Lithuanian
        //  $arrLangSupported['lt']['description'] = $this->translate('Lithuanian');
        //  $arrLangSupported['lt']['nativedescription'] = 'Lietuvi&#371;';
        //
        //  // Macedonian
        //  $arrLangSupported['mk']['description'] = $this->translate('Macedonian');
        //  $arrLangSupported['mk']['nativedescription'] =
        //                                  '&#1052;&#1072;&#1082;&#1077;&#1076;&#1086;&#1085;&#1089;&#1082;&#1080;';
        //
        //  // Norwegian Bokml
        //  $arrLangSupported['nb']['description'] = $this->translate('Norwegian (Bokmal)');
        //  $arrLangSupported['nb']['nativedescription'] = 'Norsk Bokm&#229;l';
        //
        //  // Norwegian Nynorsk
        //  $arrLangSupported['nn']['description'] = $this->translate('Norwegian (Nynorsk)');
        //  $arrLangSupported['nn']['nativedescription'] = 'Norsk Nynorsk';
        //
        //  // Portuguese
        //  $arrLangSupported['pt']['description'] = $this->translate('Portuguese');
        //  $arrLangSupported['pt']['nativedescription'] = 'Portugu&#234;s';
        //
        //  // Romanian
        //  $arrLangSupported['ro']['description'] = $this->translate('Romanian');
        //  $arrLangSupported['ro']['nativedescription'] = 'Rom&#226;nesc';
        //
        //  // Slovak
        //  $arrLangSupported['sk']['description'] = $this->translate('Slovak');
        //  $arrLangSupported['sk']['nativedescription'] = 'Slov&aacute;k';
        //
        //  // Slovenian
        //  $arrLangSupported['sl']['description'] = $this->translate('Slovenian');
        //  $arrLangSupported['sl']['nativedescription'] = 'Sloven&#353;&#269;ina';
        //
        //  // Serbian
        //  $arrLangSupported['sr']['description'] = $this->translate('Serbian');
        //  $arrLangSupported['sr']['nativedescription'] = 'Srpski';
        //
        //  // Spanish (Mexico)
        //  $arrLangSupported['es-MX']['description'] = $this->translate('Spanish (Mexico)');
        //  $arrLangSupported['es-MX']['nativedescription'] = 'Espa&#241;ol Mejicano';
        //
        //  // Swedish
        //  $arrLangSupported['sv']['description'] = $this->translate('Swedish');
        //  $arrLangSupported['sv']['nativedescription'] = 'Svenska';
        //
        //  // Turkish
        //  $arrLangSupported['tr']['description'] = $this->translate('Turkish');
        //  $arrLangSupported['tr']['nativedescription'] = 'T&#252;rk&#231;e';
        //
        //  // Thai
        //  $arrLangSupported['th']['description'] = $this->translate('Thai');
        //  $arrLangSupported['th']['nativedescription'] = '&#3616;&#3634;&#3625;&#3634;&#3652;&#3607;&#3618;';
        //
        //  // Vietnamese
        //  $arrLangSupported['vi']['description'] = $this->translate('Vietnamese');
        //  $arrLangSupported['vi']['nativedescription'] = 'Ti&#7871;ng Vi&#7879;t';
        $nativedescription = array();
        $description       = array();
        foreach ($arrLangSupported as $key => $row) {
            $description[$key]       = $row['description'];
            $nativedescription[$key] = $row['nativedescription'];
        }
        array_multisort($description, SORT_ASC, $nativedescription, SORT_ASC, $arrLangSupported);
        return $arrLangSupported;
    }

    /**
     * Translate text
     * @param string $strCode                   Language code
     * @param bool $booNative                   Native code true/false
     * @return bool|string                      Language name if found / false if not exist
     */
    public function getLangNameFromCode($strCode, $booNative = true)
    {
        $strReturn    = false;
        $arrLanguages = $this->getLangData();
        if (isset($arrLanguages[$strCode]['description'])) {
            if ($booNative) {
                $strReturn = $arrLanguages[$strCode]['description'].' - '.$arrLanguages[$strCode]['nativedescription'];
            } else {
                $strReturn = $arrLanguages[$strCode]['description'];
            }
        }
        return $strReturn;
    }

    /**
     * Connect to database server as administrator
     * @param string $strStatusMessage          Array variable for status message
     * @param string $strErrorMessage           Error string
     * @param int $intMode                      Mode (0=admin user/1=NagiosQL user
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function openAdmDBSrv(&$strStatusMessage, &$strErrorMessage, $intMode = 0)
    {
        $intStatus  = 0;
        $intReturn  = 0;
        $this->myDBClass->hasDBConnection(1);
        if ($this->myDBClass->error == true) {
            $strErrorMessage .= str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
            $intStatus = 1;
        }
        /** @noinspection PhpStatementHasEmptyBodyInspection */
        /** @noinspection MissingOrEmptyGroupStatementInspection */
        if ($intMode == 1) {
            // TO BE DEFINED
        }
        if ($intStatus == 0) {
            $strStatusMessage = '<span class="green">' .$this->translate('passed'). '</span>';
        } else {
            $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
            $intReturn  = 1;
        }
        return $intReturn;
    }

    /**
     * Connect to database as administrator
     * @param string $strStatusMessage          Error string
     * @param string $strErrorMessage           Error string
     * @param int $intMode                      Mode (0=admin user/1=NagiosQL user
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function openDatabase(&$strStatusMessage, &$strErrorMessage, $intMode = 0)
    {
        $intStatus = 0;
        $intReturn = 0;
        // Connect to database
        $booDB      = $this->myDBClass->hasDBConnection();
        if (!$booDB) {
            $strErrorMessage .= $this->translate('Error while connecting to database:'). '<br>';
            $strErrorMessage .= str_replace('::', '<br>', $this->myDBClass->strErrorMessage)."\n";
            $intStatus = 1;
        }
        /** @noinspection PhpStatementHasEmptyBodyInspection */
        /** @noinspection MissingOrEmptyGroupStatementInspection */
        if ($intMode == 1) {
            // TO BE DEFINED
        }
        if ($intStatus == 0) {
            $strStatusMessage = '<span class="green">' .$this->translate('passed'). '</span>';
        } else {
            $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
            $intReturn = 1;
        }
        return $intReturn;
    }

    /**
     * Check database version
     * @param string $strStatusMessage          Variable for status message
     * @param string $strErrorMessage           Error string
     * @param string $setVersion                Database version
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function checkDBVersion(&$strStatusMessage, &$strErrorMessage, &$setVersion)
    {
        $arrDataset = array();
        $intReturn  = 0;
        $strDBError = '';
        $intVersion = 0;
        // Read version string from DB
        if ($this->arrSession['install']['dbtype'] == 'mysqli') {
            $this->myDBClass->hasSingleDataset("SHOW VARIABLES LIKE 'version'", $arrDataset);
            $setVersion = $arrDataset['Value'];
            $strDBError = str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
            $intVersion = version_compare($setVersion, '4.1.0');
        }
        if ($strDBError == '') {
            // Is the currrent version supported?
            if ($intVersion >=0) {
                $strStatusMessage = '<span class="green">' .$this->translate('supported'). '</span>';
            } else {
                $strStatusMessage = '<span class="red">' .$this->translate('not supported'). '</span>';
                $intReturn = 1;
            }
        } else {
            $strErrorMessage .=    $strDBError."<br>\n";
            $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
            $setVersion          = 'unknown';
            $intReturn = 1;
        }
        return $intReturn;
    }

    /**
     * Check NagiosQL version
     * @param string $strStatusMessage          Variable for status message
     * @param string $strErrorMessage           Error string
     * @param array $arrUpdate                  Array including all update files
     * @param string $setVersion                Current NagiosQL version string
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function checkQLVersion(&$strStatusMessage, &$strErrorMessage, &$arrUpdate, &$setVersion)
    {
        $intReturn  = 0;
        $strSQL     = "SELECT `value` FROM `tbl_settings` WHERE `category`='db' AND `name`='version'";
        $setVersion = $this->myDBClass->getFieldData($strSQL);
        $strDBError = str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
        // Process result
        if (($strDBError == '') && ($setVersion != '')) {
            // NagiosQL version supported?
            $intVersionError = 0;
            switch ($setVersion) {
                case '3.0.0':
                    $arrUpdate[] = 'sql/update_300_310.sql';
                    $arrUpdate[] = 'sql/update_310_320.sql';
                    break;
                case '3.0.1':
                    $arrUpdate[] = 'sql/update_302_303.sql';
                    $arrUpdate[] = 'sql/update_304_310.sql';
                    $arrUpdate[] = 'sql/update_310_320.sql';
                    break;
                case '3.0.2':
                    $arrUpdate[] = 'sql/update_302_303.sql';
                    $arrUpdate[] = 'sql/update_304_310.sql';
                    $arrUpdate[] = 'sql/update_310_320.sql';
                    break;
                case '3.0.3':
                    $arrUpdate[] = 'sql/update_304_310.sql';
                    $arrUpdate[] = 'sql/update_310_320.sql';
                    break;
                case '3.0.4':
                    $arrUpdate[] = 'sql/update_304_310.sql';
                    $arrUpdate[] = 'sql/update_310_320.sql';
                    break;
                case '3.1.0':
                    $arrUpdate[] = 'sql/update_310_320.sql';
                    break;
                case '3.1.1':
                    $arrUpdate[] = 'sql/update_311_320.sql';
                    break;
                case '3.2.0':
                    $arrUpdate[] = 'sql/update_320_340.sql';
                    break;
                case '3.3.0':
                    $arrUpdate[] = 'sql/update_320_340.sql';
                    break;
                case '3.4.0':
                    $arrUpdate[] = 'sql/update_340_341.sql';
                    break;
                case '3.4.1':
                    $intVersionError = 2;
                    break;
                default:
                    $intVersionError = 1;
                    break;
            }
            if ($intVersionError == 0) {
                $strStatusMessage = '<span class="green">' .$this->translate('supported'). '</span> ('
                    .$setVersion. ')';
            } elseif ($intVersionError == 2) {
                $strErrorMessage .= $this->translate('Your NagiosQL installation is up to date - no further '
                        . 'actions are needed!')."<br>\n";
                $strStatusMessage = '<span class="green">' .$this->translate('up-to-date'). '</span> ('
                    .$setVersion. ')';
                $intReturn  = 1;
            } else {
                $strErrorMessage .= $this->translate('Updates to NagiosQL 3.2 and above are only supported from '
                        . 'NagiosQL 3.0.0 and above!')."<br>\n";
                $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span> (' .$setVersion. ')';
                $intReturn  = 1;
            }
        } else {
            $strErrorMessage .= $this->translate('Error while selecting settings table.')."<br>\n";
            $strErrorMessage .= $strDBError."<br>\n";
            $strErrorMessage .= $this->translate('Updates to NagiosQL 3.2 and above are only supported '
                    . 'from NagiosQL 3.0.0 and above!')."<br>\n";
            $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
            $intReturn  = 1;
        }
        return $intReturn;
    }

    /**
     * Delete old NagiosQL database
     * @param string $strStatusMessage          Variable for status message
     * @param string $strErrorMessage           Error string
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function dropDB(&$strStatusMessage, &$strErrorMessage)
    {
        $intReturn  = 0;
        $booReturn  = $this->myDBClass->insertData('DROP DATABASE ' .$this->arrSession['install']['dbname']);
        $strDBError = str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
        if ($booReturn) {
            $strStatusMessage = '<span class="green">' .$this->translate('done'). '</span> (' .
                $this->arrSession['install']['dbname']. ')';
        } else {
            $strErrorMessage .= $strDBError."<br>\n";
            $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span> (' .
                $this->arrSession['install']['dbname']. ')';
            $intReturn = 1;
        }
        return $intReturn;
    }

    /**
     * Create NagiosQL database
     * @param string $strStatusMessage          Variable for status message
     * @param string $strErrorMessage           Error string
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function createDB(&$strStatusMessage, &$strErrorMessage)
    {
        $intReturn = 0;
        $strSQL    = '';
        // Create database
        if (($this->arrSession['install']['dbtype'] == 'mysql') ||
            ($this->arrSession['install']['dbtype'] == 'mysqli')) {
            $strSQL = 'CREATE DATABASE ' .$this->arrSession['install']['dbname']. ' DEFAULT CHARACTER SET utf8 DEFAULT '
                . 'COLLATE utf8_unicode_ci';
        } else {
            $strErrorMessage .= $this->translate('Unsupported database type.')."<br>\n";
            $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span> (' .
                $this->arrSession['install']['dbname']. ')';
            $intReturn = 1;
        }
        if ($intReturn == 0) {
            $booReturn  = $this->myDBClass->insertData($strSQL);
            $strDBError = str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
            if ($booReturn) {
                $strStatusMessage = '<span class="green">' .$this->translate('done'). '</span> (' .
                    $this->arrSession['install']['dbname']. ')';
            } else {
                $strErrorMessage .= $strDBError."<br>\n";
                $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span> (' .
                    $this->arrSession['install']['dbname']. ')';
                $intReturn = 1;
            }
        }
        return $intReturn;
    }

    /**
     * Grant user to database
     * @param string $strStatusMessage          Variable for status message
     * @param string $strErrorMessage           Error string
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function grantDBUser(&$strStatusMessage, &$strErrorMessage)
    {
        $intReturn    = 0;
        $intUserError = 0;
        $strDBError   = '';
        // Grant user
        if (($this->arrSession['install']['dbtype'] == 'mysql') ||
            ($this->arrSession['install']['dbtype'] == 'mysqli')) {
            // does the user exist?
            $intUserError = 0;
            $this->myDBClass->insertData('FLUSH PRIVILEGES');
            $strSQL     = "SELECT * FROM `mysql`.`user` WHERE  `Host`='".$this->arrSession['install']['localsrv']."' "
                . "AND `User`='".$this->arrSession['install']['dbuser']."'";
            $intCount   = $this->myDBClass->countRows($strSQL);
            if ($intCount == 0) {
                $strSQL    = "CREATE USER '".$this->arrSession['install']['dbuser']."'@'"
                    . $this->arrSession['install']['localsrv']."' "
                    . "IDENTIFIED BY '".$this->arrSession['install']['dbpass']."'";
                $booReturn = $this->myDBClass->insertData($strSQL);
                if ($booReturn == false) {
                    $intUserError = 1;
                    $strDBError   = str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
                }
            } elseif ($this->myDBClass->strErrorMessage == '') {
                $intUserError = 2;
            } else {
                $intUserError = 1;
                $strDBError   = str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
            }
            if ($intUserError != 1) {
                $this->myDBClass->insertData('FLUSH PRIVILEGES');
                $strSQL    = 'GRANT SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON '
                    . '`' .$this->arrSession['install']['dbname']. '`.*  TO '
                    . "'".$this->arrSession['install']['dbuser']."'@'"
                    . $this->arrSession['install']['localsrv']."'";
                $booReturn = $this->myDBClass->insertData($strSQL);
                if ($booReturn == false) {
                    $intUserError    = 1;
                    $strDBError    = str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
                }
                $this->myDBClass->insertData('FLUSH PRIVILEGES');
            }
        }
        if ($intUserError != 1) {
            if ($intUserError == 2) {
                $strStatusMessage = '<span class="green">' .$this->translate('done'). '</span> (' .
                    $this->translate('Only added rights to existing user'). ': ' .
                    $this->arrSession['install']['dbuser']. ')';
            } else {
                $strStatusMessage = '<span class="green">' .$this->translate('done'). '</span>';
            }
        } else {
            $strErrorMessage .= $strDBError."<br>\n";
            $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
            $intReturn = 1;
        }
        return $intReturn;
    }

    /**
     * Update NagiosQL database
     * @param string $strStatusMessage          Variable for status message
     * @param string $strErrorMessage           Error string
     * @param array $arrUpdate                  Array including all update files
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function updateQLDB(&$strStatusMessage, &$strErrorMessage, $arrUpdate)
    {
        $intReturn      = 0;
        $intUpdateOk    = 0;
        $intUpdateError = 0;
        if (\is_array($arrUpdate) && (\count($arrUpdate) != 0)) {
            $intUpdateOk    = 0;
            $intUpdateError = 0;
        } else {
            $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
            $strErrorMessage .= $this->translate('No SQL update files available')."<br>\n";
            $intReturn        = 1;
        }
        if ($intReturn == 0) {
            foreach ($arrUpdate as $elem) {
                if (($intUpdateError == 0) && is_readable($elem)) {
                    $this->processSqlFile($elem, $intUpdateOk, $intUpdateError, $strStatusMessage, $strErrorMessage);
                } else {
                    $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
                    $strErrorMessage .= $this->translate('SQL file is not readable or empty'). ' (' .$elem.")<br>\n";
                    $intUpdateError++;
                }
            }
            if ($intUpdateError == 0) {
                $strStatusMessage = '<span class="green">' .$this->translate('done'). '</span>';
            } else {
                $intReturn = 1;
            }
        }
        return $intReturn;
    }

    /**
     * Process SQL files
     * @param string $strFileName               SQL Filename
     * @param int $intSuccess                   Success counter
     * @param int $intError                     Error Counter
     * @param string $strStatus                 Status message string
     * @param string $strError                  Error message string
     */
    private function processSqlFile($strFileName, &$intSuccess, &$intError, &$strStatus, &$strError)
    {
        $filSqlNew = fopen($strFileName, 'rb');
        if ($filSqlNew) {
            $strSqlCommand = '';
            $intSQLError   = 0;
            $intLineCount  = 0;
            while (!feof($filSqlNew)) {
                $strLine = trim(fgets($filSqlNew));
                if ($intSQLError == 1) {
                    continue;
                }   // skip if an error was found
                $intLineCount++;
                if (($strLine == '') || (0 === strpos($strLine, '--'))) {
                    continue;
                }   // skip empty and comment lines
                $strSqlCommand .= $strLine;
                if (substr($strSqlCommand, -1) == ';') {
                    $booReturn = $this->myDBClass->insertData($strSqlCommand);
                    if ($booReturn == false) {
                        $intSQLError = 1;
                        $strError   .= str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
                        $intError    = 1;
                    }
                    $strSqlCommand = '';
                }
            }
            if ($intSQLError == 0) {
                $intSuccess++;
            } else {
                $strStatus = '<span class="red">' .$this->translate('failed'). '</span> (Line: ' .
                    $intLineCount. ' in file: ' .$strFileName. ')';
                $intError++;
            }
        } else {
            $strStatus = '<span class="red">' .$this->translate('failed'). '</span>';
            $strError .= $this->translate('SQL file is not readable or empty'). ' (' .$strFileName.")<br>\n";
            $intError++;
        }
    }

    /**
     * Create NagiosQL administrator
     * @param string $strStatusMessage          Variable for status message
     * @param string $strErrorMessage           Error string
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function createNQLAdmin(&$strStatusMessage, &$strErrorMessage)
    {
        $intReturn = 0;
        // Create admin user
        $strSQL  = "SELECT `id` FROM `tbl_language` WHERE `locale`='".$this->arrSession['install']['locale']."'";
        $intLang = (int)$this->myDBClass->getFieldData($strSQL);
        if ($intLang == 0) {
            $intLang = 1;
        }
        /** @noinspection SqlSignature */
        $strSQL  = 'INSERT INTO `tbl_user` (`id`, `username`, `alias`, `password`, `admin_enable`, `wsauth`, '
            . '`active`, `nodelete`, `language`, `domain`, `last_login`, `last_modified`) '
            . "VALUES (1, '".$this->arrSession['install']['qluser']."', 'Administrator', "
            . "MD5('".$this->arrSession['install']['qlpass']."'), '1', '0', '1', '1', '".$intLang
            . "', '1', '', NOW());";
        $booReturn  = $this->myDBClass->insertData($strSQL);
        $strDBError = str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
        if ($booReturn) {
            $strStatusMessage = '<span class="green">' .$this->translate('done'). '</span>';
        } else {
            $strErrorMessage .=    $strDBError."<br>\n";
            $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
            $intReturn = 1;
        }
        return $intReturn;
    }

    /**
     * Update settings database
     * @param string $strStatusMessage          Variable for status message
     * @param string $strErrorMessage           Error string
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function updateSettingsDB(&$strStatusMessage, &$strErrorMessage)
    {
        $intReturn = 0;
        // Checking initial settings
        $arrInitial[] = array('category'=>'db','name'=>'version','value'=>$this->arrSession['install']['version']);
        $arrInitial[] = array('category'=>'db','name'=>'type','value'=>$this->arrSession['install']['dbtype']);
        /** @noinspection ForeachSourceInspection */
        foreach ($this->arrSession['init_settings'] as $key => $value) {
            if ($key == 'db') {
                continue;
            } // do not store db values to database
            /** @var array $value */
            foreach ($value as $key2 => $value2) {
                $arrInitial[] = array('category'=>$key,'name'=>$key2,'value'=>$value2);
            }
        }
        /** @noinspection ForeachSourceInspection */
        foreach ($arrInitial as $elem) {
            $strSQL1 = 'SELECT `value` FROM `tbl_settings` '
                . "WHERE `category`='".$elem['category']."' AND `name`='".$elem['name']."'";
            $strSQL2 = 'INSERT INTO `tbl_settings` (`category`, `name`, `value`) '
                . "VALUES ('".$elem['category']."', '".$elem['name']."', '".$elem['value']."')";
            $intCount   = $this->myDBClass->countRows($strSQL1);
            if ($intCount == 0) {
                $booReturn  = $this->myDBClass->insertData($strSQL2);
                if ($booReturn == false) {
                    $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
                    $strErrorMessage .= $this->translate('Inserting initial data to settings database has '
                            . 'failed:'). '1<br>';
                    $strErrorMessage .= str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
                    $intReturn = 1;
                }
            } elseif ($this->myDBClass->strErrorMessage != '') {
                $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
                $strErrorMessage .= $this->translate('Inserting initial data to settings database has failed:')
                    . '2<br>';
                $strErrorMessage .= str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
                $intReturn = 1;
            }
        }
        if ($intReturn == 0) {
            $strBaseURL  = str_replace('install/install.php', '', filter_input(INPUT_SERVER, 'PHP_SELF', 513));
            $strBasePath = substr(realpath('.'), 0, -7);
            // Update some values
            $arrSettings[]     = array('category'=>'db','name'=>'version',
                'value'=>$this->arrSession['install']['version']);
            if (filter_input(INPUT_SERVER, 'REQUEST_SCHEME', FILTER_SANITIZE_STRING) == 'https') {
                $arrSettings[] = array('category'=>'path','name'=>'protocol','value'=>'https');
            } else {
                $arrSettings[] = array('category'=>'path','name'=>'protocol','value'=>'http');
            }
            $arrSettings[]     = array('category'=>'data','name'=>'locale',
                'value'=>$this->arrSession['install']['locale']);
            $arrSettings[]     = array('category'=>'path','name'=>'base_url','value'=>$strBaseURL);
            $arrSettings[]     = array('category'=>'path','name'=>'base_path','value'=>$strBasePath);
            /** @var array $arrSettings */
            foreach ($arrSettings as $elem) {
                $strSQL    = "UPDATE `tbl_settings` SET `value`='".$elem['value']."' "
                    . "WHERE `category`='".$elem['category']."' AND `name`='".$elem['name']."'";
                $booReturn = $this->myDBClass->insertData($strSQL);
                if ($booReturn == false) {
                    $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
                    $strErrorMessage .=    $this->translate('Inserting initial data to settings database has failed:');
                    $strErrorMessage .=    str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
                    $intReturn        = 1;
                }
            }
            if ($intReturn == 0) {
                $strStatusMessage = '<span class="green">' .$this->translate('done'). '</span>';
            }
        }
        return $intReturn;
    }

    /**
     * Update settings file
     * @param string $strStatusMessage          Variable for status message
     * @param string $strErrorMessage           Error string
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function updateSettingsFile(&$strStatusMessage, &$strErrorMessage)
    {
        $intReturn = 0;
        // open settings file
        $strBaseURL  = str_replace('install/install.php', '', filter_input(INPUT_SERVER, 'PHP_SELF', 513));
        $strBasePath = substr(realpath('.'), 0, -7);
        $strErrorId  = error_reporting();
        error_reporting(0);
        $filSettings = fopen($strBasePath. 'config/settings.php', 'wb');
        error_reporting($strErrorId);
        if ($filSettings) {
            // Write Database Configuration into settings.php
            fwrite($filSettings, "<?php\n");
            fwrite($filSettings, "exit;\n");
            fwrite($filSettings, "?>\n");
            fwrite($filSettings, ";///////////////////////////////////////////////////////////////////////////////\n");
            fwrite($filSettings, ";\n");
            fwrite($filSettings, "; NagiosQL\n");
            fwrite($filSettings, ";\n");
            fwrite($filSettings, ";///////////////////////////////////////////////////////////////////////////////\n");
            fwrite($filSettings, ";\n");
            fwrite($filSettings, "; Project  : NagiosQL\n");
            fwrite($filSettings, "; Component: Database Configuration\n");
            fwrite($filSettings, "; Website  : https://sourceforge.net/projects/nagiosql/\n");
            fwrite($filSettings, '; Date     : ' .date('F j, Y, g:i a')."\n");
            fwrite($filSettings, '; Version  : ' .$this->arrSession['install']['version']."\n");
            fwrite($filSettings, ";\n");
            fwrite($filSettings, ";///////////////////////////////////////////////////////////////////////////////\n");
            fwrite($filSettings, "[db]\n");
            fwrite($filSettings, 'type         = \'' .$this->arrSession['install']['dbtype']. "'\n");
            fwrite($filSettings, 'server       = \'' .$this->arrSession['install']['dbserver']. "'\n");
            fwrite($filSettings, 'port         = \'' .$this->arrSession['install']['dbport']. "'\n");
            fwrite($filSettings, 'database     = \'' .$this->arrSession['install']['dbname']. "'\n");
            fwrite($filSettings, 'username     = \'' .$this->arrSession['install']['dbuser']. "'\n");
            fwrite($filSettings, 'password     = \'' .$this->arrSession['install']['dbpass']. "'\n");
            fwrite($filSettings, "[path]\n");
            fwrite($filSettings, 'base_url     = \'' .$strBaseURL."'\n");
            fwrite($filSettings, 'base_path    = \'' .$strBasePath."'\n");
            fclose($filSettings);
            $strStatusMessage = '<span class="green">' .$this->translate('done'). '</span>';
        } else {
            $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
            $strErrorMessage .=    $this->translate('Connot open/write to config/settings.php')."<br>\n";
            $intReturn = 1;
        }
        return $intReturn;
    }

    /**
     * Update settings database
     * @param string $strStatusMessage          Variable for status message
     * @param string $strErrorMessage           Error string
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function updateQLpath(&$strStatusMessage, &$strErrorMessage)
    {
        $intReturn = 0;
        // Update configuration target database
        $strNagiosQLpath = str_replace('//', '/', $this->arrSession['install']['qlpath']. '/');
        $strNagiosPath   = str_replace('//', '/', $this->arrSession['install']['nagpath']. '/');
        $strSQL          = 'UPDATE `tbl_configtarget` SET '
            . "`basedir`='".$strNagiosQLpath."', "
            . "`hostconfig`='".$strNagiosQLpath."hosts/', "
            . "`serviceconfig`='".$strNagiosQLpath."services/', "
            . "`backupdir`='".$strNagiosQLpath."backup/', "
            . "`hostbackup`='".$strNagiosQLpath."backup/hosts/', "
            . "`servicebackup`='".$strNagiosQLpath."backup/services/', "
            . "`nagiosbasedir`='".$strNagiosPath."', "
            . "`importdir`='".$strNagiosPath."objects/', "
            . "`conffile`='".$strNagiosPath."nagios.cfg', "
            . "`last_modified`=NOW() WHERE `target`='localhost'";
        $booReturn            = $this->myDBClass->insertData($strSQL);
        if ($booReturn == false) {
            $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
            $strErrorMessage .= $this->translate('Inserting path data to database has failed:'). ' ' .
                str_replace('::', '<br>', $this->myDBClass->strErrorMessage)."\n";
            $intReturn = 1;
        }
        if ($intReturn == 0 && $this->arrSession['install']['createpath'] == 1) {
            if (is_writable($strNagiosQLpath) && is_dir($strNagiosQLpath) && is_executable($strNagiosQLpath)) {
                if (!file_exists($strNagiosQLpath . 'hosts') && !mkdir($strNagiosQLpath . 'hosts', 0755) &&
                    !is_dir($strNagiosQLpath . 'hosts')) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $strNagiosQLpath . 'hosts'));
                }
                if (!file_exists($strNagiosQLpath . 'services') && !mkdir($strNagiosQLpath . 'services', 0755) &&
                    !is_dir($strNagiosQLpath . 'services')) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $strNagiosQLpath
                        . 'services'));
                }
                if (!file_exists($strNagiosQLpath . 'backup') && !mkdir($strNagiosQLpath . 'backup', 0755) &&
                    !is_dir($strNagiosQLpath . 'backup')) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $strNagiosQLpath . 'backup'));
                }
                if (!file_exists($strNagiosQLpath . 'backup/hosts') &&
                    !mkdir($strNagiosQLpath . 'backup/hosts', 0755) && !is_dir($strNagiosQLpath . 'backup/hosts')) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $strNagiosQLpath
                        . 'backup/hosts'));
                }
                if (!file_exists($strNagiosQLpath . 'backup/services') &&
                    !mkdir($strNagiosQLpath . 'backup/services', 0755) &&
                    !is_dir($strNagiosQLpath . 'backup/services')) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $strNagiosQLpath
                        . 'backup/services'));
                }
                $strStatusMessage = '<span class="green">' .$this->translate('done'). '</span> (' .
                    $this->translate('Check the permissions of the created paths!'). ')';
            } else {
                $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
                $strErrorMessage .= $this->translate('NagiosQL config path is not writeable - only database '
                        . 'values updated')."<br>\n";
                $intReturn        = 1;
            }
        }
        if ($intReturn == 0) {
            $strStatusMessage = '<span class="green">' .$this->translate('done'). '</span>';
        }
        return $intReturn;
    }

    /**
     * Converting NagiosQL database to utf-8
     * @param string $strStatusMessage          Variable for status message
     * @param string $strErrorMessage           Error string
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function convQLDB(&$strStatusMessage, &$strErrorMessage)
    {
        $strDBError = '';
        $intReturn  = 0;
        if ($this->arrSession['install']['dbtype'] == 'mysqli') {
            $strSQL     = 'ALTER DATABASE `' .$this->arrSession['install']['dbname']. '` '
                . 'DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci';
            $this->myDBClass->insertData($strSQL);
            $strDBError = str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
        }
        if ($strDBError == '') {
            $strStatusMessage = '<span class="green">' .$this->translate('done'). '</span>';
        } else {
            $strErrorMessage .= $this->translate('Database errors while converting to utf-8:'). '<br>' .
                $strDBError."<br>\n";
            $strStatusMessage = '<span class="red">' .$this->translate('failed'). '</span>';
            $intReturn        = 1;
        }
        return $intReturn;
    }

    /**
     * Converting NagiosQL database tables to utf-8
     * @param string $strStatusMessage          Variable for status message
     * @param string $strErrorMessage           Error string
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function convQLDBTables(&$strStatusMessage, &$strErrorMessage)
    {
        $arrDataset   = array();
        $intDataCount = 0;
        $intReturn    = 0;
        $intError     = 0;
        $strDBError   = '';
        // Read version string from DB
        if ($this->arrSession['install']['dbtype'] == 'mysqli') {
            $strSQL = 'SHOW TABLES FROM `' .$this->arrSession['install']['dbname']. '`';
            $this->myDBClass->hasDataArray($strSQL, $arrDataset, $intDataCount);
            if ($intDataCount != 0) {
                foreach ($arrDataset as $elem) {
                    if ($intError == 1) {
                        continue;
                    }
                    $strSQL    = 'ALTER TABLE `' .$elem[0]. '` DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
                    $booReturn = $this->myDBClass->insertData($strSQL);
                    if ($booReturn == false) {
                        $intError   = 1;
                        $strDBError = str_replace('::', '<br>', $this->myDBClass->strErrorMessage);
                    }
                }
            }
        } else {
            $strErrorMessage .= translate('Database type not defined!'). ' (' .$this->arrSession['install']['dbtype']
                .")<br>\n";
            $strStatusMessage = '<span class="red">' .translate('failed'). '</span>';
            $intReturn = 1;
        }
        if ($intReturn == 0) {
            if ($strDBError == '') {
                $strStatusMessage = '<span class="green">' .translate('done'). '</span>';
            } else {
                $strErrorMessage .= translate('Database errors while converting to utf-8:'). '<br>' .$strDBError
                    ."<br>\n";
                $strStatusMessage = '<span class="red">' .translate('failed'). '</span>';
                $intReturn        = 1;
            }
        }
        return $intReturn;
    }

    /**
     * Converting NagiosQL database tables to utf-8
     * @param string $strStatusMessage          Variable for status message
     * @param string $strErrorMessage           Error string
     * @return int                              Status variable (0=ok,1=failed)
     */
    public function convQLDBFields(&$strStatusMessage, &$strErrorMessage)
    {
        $arrDataset1   = array();
        $arrDataset2   = array();
        $intDataCount1 = 0;
        $intDataCount2 = 0;
        $intReturn     = 0;
        $intError      = 0;
        $strDBError    = '';
        // Read version string from DB
        $strSQL1   = 'SHOW TABLES FROM `' .$this->arrSession['install']['dbname']. '`';
        $booReturn = $this->myDBClass->hasDataArray($strSQL1, $arrDataset1, $intDataCount1);
        if ($booReturn && ($intDataCount1 != 0)) {
            foreach ($arrDataset1 as $elem1) {
                $strSQL2   = 'SHOW FULL FIELDS FROM `' .$elem1[0]. '` '
                    . "WHERE (`Type` LIKE '%varchar%' OR `Type` LIKE '%enum%' OR `Type` LIKE '%text%') "
                    . "AND Collation <> 'utf8_unicode_ci'";
                $this->myDBClass->hasDataArray($strSQL2, $arrDataset2, $intDataCount2);
                if ($intDataCount2 != 0) {
                    foreach ($arrDataset2 as $elem2) {
                        if ($intError == 1) {
                            continue;
                        }
                        $this->convTabFields($elem1[0], $elem2, $intError, $strDBError);
                    }
                }
            }
        } else {
            $strErrorMessage .= translate('Database type not defined!'). ' ('
                .$this->arrSession['install']['dbtype'].")<br>\n";
            $strStatusMessage = '<span class="red">' .translate('failed'). '</span>';
            $intReturn = 1;
        }
        if ($intReturn == 0) {
            if ($strDBError == '') {
                $strStatusMessage = '<span class="green">' .translate('done'). '</span>';
            } else {
                $strErrorMessage .= translate('Database errors while converting to utf-8:'). '<br>' .$strDBError
                    ."<br>\n";
                $strStatusMessage = '<span class="red">' .translate('failed'). '</span>';
                $intReturn = 1;
            }
        }
        return $intReturn;
    }

    /**
     * Convert table fields
     * @param string $strTable                  Table Name
     * @param array $arrFields                  Table fields (array)
     * @param int $intError                     Error Counter
     * @param string $strDBError                DB error message string
     */
    private function convTabFields($strTable, $arrFields, &$intError, &$strDBError)
    {
        $strDefault = '';
        $strNull    = 'NOT NULL';
        if (($arrFields[5] === null) && ($arrFields[3] === 'YES')) {
            $strDefault = 'DEFAULT NULL';
        } elseif ($arrFields[5] != '') {
            $strDefault = "DEFAULT '".$arrFields[5]."'";
        }
        if ($arrFields[3] == 'YES') {
            $strNull = 'NULL';
        }
        $strSQL    = /** @lang text */ 'ALTER TABLE `' .$strTable. '` CHANGE `' .$arrFields[0]. '` `' .$arrFields[0]
            . '` ' . $arrFields[1]." CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' $strNull $strDefault";
        $booReturn = $this->myDBClass->insertData($strSQL);
        if ($booReturn == false) {
            $intError   = 1;
            $strDBError = 'Table:' .$strTable. ' - Field: ' .$arrFields[0]. ' ' .
                $this->myDBClass->strErrorMessage;
        }
    }
}
