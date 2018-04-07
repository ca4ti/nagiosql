<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2017 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Installer Class
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2017-06-22 11:54:45 +0200 (Thu, 22 Jun 2017) $
// Author    : $LastChangedBy: martin $
// Version   : 3.3.0
// Revision  : $LastChangedRevision: 6 $
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
class naginstall {
  	// Define class variables
	var $filTemplate = "";		// template file
	var $myDBClass;				// Database class
	
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Class constructor
  	///////////////////////////////////////////////////////////////////////////////////////////
  	//
  	//  Activities during class initialization
  	//
  	///////////////////////////////////////////////////////////////////////////////////////////
  	function __construct() {

  	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Parse template
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		$arrTemplate   	Array including template replacements
	//						$strTplFile		Template file
	//						$intMode		Mode (0=admin user/1=NagiosQL user
	//   
  	//  Return values:		none
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function parseTemplate($arrTemplate,$strTplFile) {
		// Open template file
		if (file_exists($strTplFile) && is_readable($strTplFile)) {
			$strTemplate = "";
			$datTplFile = fopen($strTplFile,'r');
			while (!feof($datTplFile)) {
				$strTemplate .= fgets($datTplFile);
			}
			foreach ($arrTemplate AS $key => $elem) {
				if (substr_count($strTemplate,"{".$key."}") != 0) {
					$strTemplate = str_replace("{".$key."}",$elem,$strTemplate);
				}
			}
			return($strTemplate);
		} else {
			echo $this->translate("Template file not found").": ".$strTplFile;	
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Translate text
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		$strLangString  String to translate
	//   
  	//  Return values:		Translated string
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function translate($strLangString) {
		$strLangString = gettext($strLangString);
		$strLangString = str_replace('"','&quot;',$strLangString);
		$strLangString = str_replace("'",'&#039;',$strLangString);
		return $strLangString;
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Return supported languages
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		none
	//   
  	//  Return values:		Array including supported languages
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function getLangData() {
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
		//  $arrLangSupported['bs']['nativedescription'] = '&#x0411;&#x044a;&#x043b;&#x0433;&#x0430;&#x0440;&#x0441;&#x043a;&#x0438;';
		//
		//  // Bulgarian
		//  $arrLangSupported['bg']['description'] = $this->translate('Bulgarian');
		//  $arrLangSupported['bg']['nativedescription'] = '&#x0411;&#x044a;&#x043b;&#x0433;&#x0430;&#x0440;&#x0441;&#x043a;&#x0438;';
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
		//  $arrLangSupported['mk']['nativedescription'] = '&#1052;&#1072;&#1082;&#1077;&#1076;&#1086;&#1085;&#1089;&#1082;&#1080;';
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
		
		
		foreach ($arrLangSupported as $key => $row) {
   			$description[$key]  	 = $row['description'];
    		$nativedescription[$key] = $row['nativedescription'];
		}
		array_multisort($description, SORT_ASC, $nativedescription, SORT_ASC, $arrLangSupported);
		//uasort($arrLangSupported,"user_sort");
		return $arrLangSupported;
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Translate text
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		$strCode  	Language code
	//						$booNative	Native code true/false
	//   
  	//  Return values:		Language name if found / false if not exist
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function getLangNameFromCode($strCode, $booNative=true) {
		$arrLanguages = $this->getLangData();
		if (isset($arrLanguages[$strCode]['description'])) {
			if ($booNative) {
				return $arrLanguages[$strCode]['description'].' - '.$arrLanguages[$strCode]['nativedescription'];
			} else {
				return $arrLanguages[$strCode]['description'];}
		} else  {
			// else return false
			return false;
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Connect to database server as administrator
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//						$intMode			Mode (0=admin user/1=NagiosQL user
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function openAdmDBSrv(&$strStatusMessage,&$strErrorMessage,$intMode=0) {
		$intStatus  = 0;
		$this->myDBClass->dbconnect();
    	if ($this->myDBClass->error == true) {
			$strErrorMessage .= str_replace("::","<br>",$this->myDBClass->strErrorMessage);
      		$intStatus = 1;
    	}
		if ($intStatus == 0) {
			$strStatusMessage = "<span class=\"green\">".$this->translate("passed")."</span>";
			return(0);
		} else {
			$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
			return(1);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Connect to database as administrator
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//						$intMode			Mode (0=admin user/1=NagiosQL user
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function openDatabase(&$strStatusMessage,&$strErrorMessage,$intMode=0) {
		$intStatus  = 0;
		// Connect to database
		$booDB      = $this->myDBClass->dbselect();
		if (!$booDB) {
			$strErrorMessage .= $this->translate('Error while connecting to database:')."<br>";
			$strErrorMessage .= str_replace("::","<br>",$this->myDBClass->strErrorMessage)."\n";
			$intStatus = 1;
		}		
		if ($intStatus == 0) {
			$strStatusMessage = "<span class=\"green\">".$this->translate("passed")."</span>";
			return(0);
		} else {
			$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
			return(1);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Check database version
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//						$strVersion			Database version
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function checkDBVersion(&$strStatusMessage,&$strErrorMessage,&$setVersion) {
		// Read version string from DB
		if (($_SESSION['install']['dbtype'] == "mysql") || ($_SESSION['install']['dbtype'] == "mysqli")) {
			$this->myDBClass->getSingleDataset("SHOW VARIABLES LIKE 'version'",$arrDataset);
			$setVersion = $arrDataset['Value'];
			$strDBError = str_replace("::","<br>",$this->myDBClass->strErrorMessage);
			$intVersion = version_compare($setVersion,"4.1.0");
		}
		if ($strDBError == "") {
			// Is the currrent version supported?
			if ($intVersion >=0) {
				$strStatusMessage = "<span class=\"green\">".$this->translate("supported")."</span>";
				return(0);
			} else {
				$strStatusMessage = "<span class=\"red\">".$this->translate("not supported")."</span>";
				return(1);
			}
		} else {
			$strErrorMessage .=	$strDBError."<br>\n";
			$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
			$setVersion		  = "unknown";
			return(1);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Check NagiosQL version
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//						$arrUpdate			Array including all update files
	//						$setVersion			Current NagiosQL version string 
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function checkQLVersion(&$strStatusMessage,&$strErrorMessage,&$arrUpdate,&$setVersion) {
		$strSQL		= "SELECT `value` FROM `tbl_settings` WHERE `category`='db' AND `name`='version'";
		$setVersion = $this->myDBClass->getFieldData($strSQL);
		$strDBError = str_replace("::","<br>",$this->myDBClass->strErrorMessage);
		// Process result
		if (($strDBError == "") && ($setVersion != "")) {
			// NagiosQL version supported?
			$intVersionError = 0;
			switch($setVersion) {
				case '3.0.0': 	$arrUpdate[] = "sql/update_300_310.sql";
								$arrUpdate[] = "sql/update_310_320.sql";
								break;
				case '3.0.1': 	$arrUpdate[] = "sql/update_302_303.sql";
								$arrUpdate[] = "sql/update_304_310.sql";
								$arrUpdate[] = "sql/update_310_320.sql";
								break;
				case '3.0.2': 	$arrUpdate[] = "sql/update_302_303.sql";
								$arrUpdate[] = "sql/update_304_310.sql";
								$arrUpdate[] = "sql/update_310_320.sql";
								break;	
				case '3.0.3': 	$arrUpdate[] = "sql/update_304_310.sql";
								$arrUpdate[] = "sql/update_310_320.sql";
								break;	
				case '3.0.4': 	$arrUpdate[] = "sql/update_304_310.sql";
								$arrUpdate[] = "sql/update_310_320.sql";
								break;	
				case '3.1.0': 	$arrUpdate[] = "sql/update_310_320.sql";
								break;	
				case '3.1.1': 	$arrUpdate[] = "sql/update_311_320.sql";
								break;
				case '3.2.0':	$intVersionError = 2;
								break;
				case '3.3.0':	$intVersionError = 2;
								break;
				default:		$intVersionError = 1;
								break;
			}
			if ($intVersionError == 0) {
				$strStatusMessage = "<span class=\"green\">".$this->translate("supported")."</span> (".$setVersion.")";
				return(0);
			} else if ($intVersionError == 2) {
				$strErrorMessage .=	$this->translate("Your NagiosQL installation is up to date - no further actions are needed!")."<br>\n";
				$strStatusMessage = "<span class=\"green\">".$this->translate("up-to-date")."</span> (".$setVersion.")";
				return(1);
			} else {
				$strErrorMessage .=	$this->translate("Updates to NagiosQL 3.2 and above are only supported from NagiosQL 3.0.0 and above!")."<br>\n";
				$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span> (".$setVersion.")";
				return(1);
			}
		} else {
			$strErrorMessage .=	$this->translate("Error while selecting settings table.")."<br>\n";
			$strErrorMessage .=	$strDBError."<br>\n";
			$strErrorMessage .=	$this->translate("Updates to NagiosQL 3.2 and above are only supported from NagiosQL 3.0.0 and above!")."<br>\n";
			$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
			return(1);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Delete old NagiosQL database
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dropDB(&$strStatusMessage,&$strErrorMessage) {
		$booReturn  = $this->myDBClass->insertData("DROP DATABASE ".$_SESSION['install']['dbname']);
		$strDBError = str_replace("::","<br>",$this->myDBClass->strErrorMessage);
		if ($booReturn) {
			$strStatusMessage = "<span class=\"green\">".$this->translate("done")."</span> (".$_SESSION['install']['dbname'].")";
			return(0);
		} else {
			$strErrorMessage .=	$strDBError."<br>\n";
			$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span> (".$_SESSION['install']['dbname'].")";
			return(1);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Create NagiosQL database
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function createDB(&$strStatusMessage,&$strErrorMessage) {
		// Create database
		if (($_SESSION['install']['dbtype'] == "mysql") || ($_SESSION['install']['dbtype'] == "mysqli")) {
			$strSQL = "CREATE DATABASE ".$_SESSION['install']['dbname']." DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_unicode_ci";
		} else {
			$strErrorMessage .=	$this->translate("Unsupported database type.")."<br>\n";
			$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span> (".$_SESSION['install']['dbname'].")";
			return(1);
		}
		$booReturn  = $this->myDBClass->insertData($strSQL);
		$strDBError = str_replace("::","<br>",$this->myDBClass->strErrorMessage);
		if ($booReturn) {
			$strStatusMessage = "<span class=\"green\">".$this->translate("done")."</span> (".$_SESSION['install']['dbname'].")";
			return(0);
		} else {
			$strErrorMessage .=	$strDBError."<br>\n";
			$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span> (".$_SESSION['install']['dbname'].")";
			return(1);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Grant user to database
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function grantDBUser(&$strStatusMessage,&$strErrorMessage) {
		// Grant user
		if (($_SESSION['install']['dbtype'] == "mysql") || ($_SESSION['install']['dbtype'] == "mysqli")) {
			// does the user exist?
			$intUserError = 0;	
			$booReturn  = $this->myDBClass->insertData("FLUSH PRIVILEGES");
			$strSQL     = "SELECT * FROM `mysql`.`user` 
						   WHERE  `Host`='".$_SESSION['install']['localsrv']."' AND `User`='".$_SESSION['install']['dbuser']."'";
			$intCount   = $this->myDBClass->countRows($strSQL);
			if ($intCount == 0) {
				$strSQL    = "CREATE USER '".$_SESSION['install']['dbuser']."'@'".$_SESSION['install']['localsrv']."' IDENTIFIED BY '".$_SESSION['install']['dbpass']."'";
				$booReturn = $this->myDBClass->insertData($strSQL );
				if ($booReturn == false) {
					$intUserError 	= 1;
					$strDBError 	= str_replace("::","<br>",$this->myDBClass->strErrorMessage);
				}
			} else if ($this->myDBClass->strErrorMessage == "") {
				$intUserError   = 2;
			} else {
				$intUserError 	= 1;
				$strDBError 	= str_replace("::","<br>",$this->myDBClass->strErrorMessage);
			}
			if ($intUserError != 1) {
				$booReturn = $this->myDBClass->insertData("FLUSH PRIVILEGES");
				$strSQL    = "GRANT SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON `".$_SESSION['install']['dbname']."`.* 
							  TO '".$_SESSION['install']['dbuser']."'@'".$_SESSION['install']['localsrv']."'";
				$booReturn = $this->myDBClass->insertData($strSQL);
				if ($booReturn == false) {
					$intUserError 	= 1;
					$strDBError 	= str_replace("::","<br>",$this->myDBClass->strErrorMessage);
				}
				$booReturn = $this->myDBClass->insertData("FLUSH PRIVILEGES");
			}
		}
		if ($intUserError != 1) {
			if ($intUserError == 2) {
				$strStatusMessage = "<span class=\"green\">".$this->translate("done")."</span> (".$this->translate("Only added rights to existing user").": ".$_SESSION['install']['dbuser'].")";
			} else {
				$strStatusMessage = "<span class=\"green\">".$this->translate("done")."</span>";
			}
			return(0);
		} else {
			$strErrorMessage .=	$strDBError."<br>\n";
			$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
			return(1);
		}
	}		
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Update NagiosQL database
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//						$arrUpdate			Array including all update files
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function updateQLDB(&$strStatusMessage,&$strErrorMessage,$arrUpdate) {
		if (is_array($arrUpdate) && (count($arrUpdate) != 0)) {
			$intUpdateOk 	= 0;
			$intUpdateError = 0;
			foreach($arrUpdate AS $elem) {
				if ($intUpdateError == 0) {
					if (is_readable($elem)) {	
						$filSqlNew = fopen($elem,"r");
						if ($filSqlNew) {
							$strSqlCommand = "";
							$intSQLError   = 0;
							$intLineCount  = 0;
							while (!feof($filSqlNew)) {
								$strLine = fgets($filSqlNew);
								$strLine = trim($strLine);								
								if ($intSQLError == 1)  continue;			// skip if an error was found
								$intLineCount++;
								if ($strLine == "") continue; 				// skip empty lines
								if (substr($strLine,0,2) == "--") continue; // skip comment lines
								$strSqlCommand .= $strLine;								
								if (substr($strSqlCommand,-1) == ";") {
									$booReturn = $this->myDBClass->insertData($strSqlCommand);
									if ($booReturn == false) {
										$intSQLError = 1;
										$strErrorMessage .= str_replace("::","<br>",$this->myDBClass->strErrorMessage);
										$intError = 1;
									}
									$strSqlCommand = "";
								}
							}
							if ($intSQLError == 0) {
								$intUpdateOk++;
							} else {
								$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span> (Line: ".$intLineCount." in file: ".$elem.")";
								$intUpdateError++;
							}
						} else {
							$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
							$strErrorMessage .=	$this->translate("SQL file is not readable or empty")." (".$elem.")<br>\n";
							$intUpdateError++;
						}
					} else {
						$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
						$strErrorMessage .=	$this->translate("SQL file is not readable or empty")." (".$elem.")<br>\n";
						$intUpdateError++;
					}
				}					
			} 
			if ($intUpdateError == 0) {
				$strStatusMessage = "<span class=\"green\">".$this->translate("done")."</span>";
				return(0);
			} else {
				return(1);
			}
		} else {
			$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
			$strErrorMessage .=	$this->translate("No SQL update files available")."<br>\n";
			return(1);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Create NagiosQL administrator
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function createNQLAdmin(&$strStatusMessage,&$strErrorMessage) {
		// Create admin user
		$strSQL  = "SELECT `id` FROM `tbl_language` WHERE `locale`='".$_SESSION['install']['locale']."'";	
		$intLang	= $this->myDBClass->getFieldData($strSQL)+0;
		if ($intLang == 0) $intLang = 1;
		$strSQL  = "INSERT INTO `tbl_user` (`id`, `username`, `alias`, `password`, `admin_enable`, `wsauth`, `active`, 
								`nodelete`, `language`, `domain`, `last_login`, `last_modified`)
					VALUES (1, '".$_SESSION['install']['qluser']."', 'Administrator', md5('".$_SESSION['install']['qlpass']."'),
						   '1', '0', '1', '1', '".$intLang."', '1', '', NOW());";	
		$booReturn  = $this->myDBClass->insertData($strSQL);
		$strDBError = str_replace("::","<br>",$this->myDBClass->strErrorMessage);
		if ($booReturn) {
			$strStatusMessage = "<span class=\"green\">".$this->translate("done")."</span>";
			return(0);
		} else {
			$strErrorMessage .=	$strDBError."<br>\n";
			$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
			return(1);
		}
	}	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Update settings database
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function updateSettingsDB(&$strStatusMessage,&$strErrorMessage) {
		// Checking initial settings
		$arrInitial[] = array('category'=>'db','name'=>'version','value'=>$_SESSION['install']['version']);
		$arrInitial[] = array('category'=>'db','name'=>'type','value'=>$_SESSION['install']['dbtype']);
		foreach ($_SESSION['init_settings'] AS $key => $elem) {
			if ($key == 'db') continue; // do not store db values to database
			foreach ($elem AS $key2=>$elem2) {
				$arrInitial[] = array('category'=>$key,'name'=>$key2,'value'=>$elem2);
			}
		}
		foreach ($arrInitial AS $elem) {
			$strSQL1 = "SELECT `value` FROM `tbl_settings` WHERE `category`='".$elem['category']."'
					    AND `name`='".$elem['name']."'";	
			$strSQL2 = "INSERT INTO `tbl_settings` (`category`, `name`, `value`) 
						VALUES ('".$elem['category']."', '".$elem['name']."', '".$elem['value']."')";
			$intCount   = $this->myDBClass->countRows($strSQL1);
			if ($intCount == 0) {
				$booReturn  = $this->myDBClass->insertData($strSQL2);
				if ($booReturn == false) {
					$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
					$strErrorMessage .=	$this->translate("Inserting initial data to settings database has failed:")."1<br>";
					$strErrorMessage .=	str_replace("::","<br>",$this->myDBClass->strErrorMessage);
					return(1);
				}				
			} else if ($this->myDBClass->strErrorMessage != "") {
				$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
				$strErrorMessage .=	$this->translate("Inserting initial data to settings database has failed:")."2<br>";
				$strErrorMessage .=	str_replace("::","<br>",$this->myDBClass->strErrorMessage);
				return(1);		
			}
		}
		// Update some values
		$arrSettings[] 	= array('category'=>'db','name'=>'version','value'=>$_SESSION['install']['version']);
		if (substr_count($_SERVER['SERVER_PROTOCOL'],"HTTPS") != 0) {
			$arrSettings[] = array('category'=>'path','name'=>'protocol','value'=>'https');
		} else {
			$arrSettings[] = array('category'=>'path','name'=>'protocol','value'=>'http');	
		}
		//$strBaseURL  	= str_replace("install/install.php","",$_SERVER["PHP_SELF"]);
		//$arrSettings[] 	= array('category'=>'path','name'=>'base_url','value'=>$strBaseURL);	
		//$strBasePath	= substr(realpath('.'),0,-7);
		//$arrSettings[] 	= array('category'=>'path','name'=>'base_path','value'=>$strBasePath);
		$arrSettings[] 	= array('category'=>'data','name'=>'locale','value'=>$_SESSION['install']['locale']);
		foreach ($arrSettings AS $elem) {
			$strSQL	= "UPDATE `tbl_settings` SET `value`='".$elem['value']."' 
					   WHERE `category` = '".$elem['category']."' AND `name` = '".$elem['name']."'";
			$booReturn  = $this->myDBClass->insertData($strSQL);
			if ($booReturn == false) {
				$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
				$strErrorMessage .=	$this->translate("Inserting initial data to settings database has failed:");
				$strErrorMessage .=	str_replace("::","<br>",$this->myDBClass->strErrorMessage);
				return(1);				
			}
		}
		$strStatusMessage = "<span class=\"green\">".$this->translate("done")."</span>";
		return(0);
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Update settings file
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function updateSettingsFile(&$strStatusMessage,&$strErrorMessage) {
		// open settings file
		$strBaseURL  = str_replace("install/install.php","",$_SERVER["PHP_SELF"]);
		$strBasePath = substr(realpath('.'),0,-7);
		$strE_ID 	 = error_reporting();
		error_reporting(0);
		$filSettings = fopen($strBasePath."config/settings.php","w");
		error_reporting($strE_ID);
		if ($filSettings) {
			// Write Database Configuration into settings.php
			fwrite($filSettings,"<?php\n");
			fwrite($filSettings,"exit;\n");
			fwrite($filSettings,"?>\n");
			fwrite($filSettings,";///////////////////////////////////////////////////////////////////////////////\n");
			fwrite($filSettings,";\n");
			fwrite($filSettings,"; NagiosQL\n");
			fwrite($filSettings,";\n");
			fwrite($filSettings,";///////////////////////////////////////////////////////////////////////////////\n");
			fwrite($filSettings,";\n");
			fwrite($filSettings,"; Project  : NagiosQL\n");
			fwrite($filSettings,"; Component: Database Configuration\n");
			fwrite($filSettings,"; Website  : http://www.nagiosql.org\n");
			fwrite($filSettings,"; Date     : ".date("F j, Y, g:i a")."\n");
			fwrite($filSettings,"; Version  : ".$_SESSION['install']['version']."\n");
			fwrite($filSettings,";\n");
			fwrite($filSettings,";///////////////////////////////////////////////////////////////////////////////\n");
			fwrite($filSettings,"[db]\n");
			fwrite($filSettings,"type         = ".$_SESSION['install']['dbtype']."\n");
			fwrite($filSettings,"server       = ".$_SESSION['install']['dbserver']."\n");
			fwrite($filSettings,"port         = ".$_SESSION['install']['dbport']."\n");
			fwrite($filSettings,"database     = ".$_SESSION['install']['dbname']."\n");
			fwrite($filSettings,"username     = ".$_SESSION['install']['dbuser']."\n");
			fwrite($filSettings,"password     = ".$_SESSION['install']['dbpass']."\n");
			fwrite($filSettings,"[path]\n");
			fwrite($filSettings,"base_url     = ".$strBaseURL."\n");
			fwrite($filSettings,"base_path    = ".$strBasePath."\n");
			fclose($filSettings);	
			$strStatusMessage = "<span class=\"green\">".$this->translate("done")."</span>";
			return(0);
		} else {
			$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
			$strErrorMessage .=	$this->translate("Connot open/write to config/settings.php")."<br>\n";
			return(1);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Update settings database
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function updateQLpath(&$strStatusMessage,&$strErrorMessage) {
		// Update configuration target database
		$strNagiosQLpath	= str_replace("//","/",$_SESSION['install']['qlpath']."/");
		$strNagiosPath		= str_replace("//","/",$_SESSION['install']['nagpath']."/");
		$strSQL 			= "UPDATE `tbl_configtarget` SET 
									  `basedir`='".$strNagiosQLpath."', 
									  `hostconfig`='".$strNagiosQLpath."hosts/',
									  `serviceconfig`='".$strNagiosQLpath."services/',
									  `backupdir`='".$strNagiosQLpath."backup/',
									  `hostbackup`='".$strNagiosQLpath."backup/hosts/',
									  `servicebackup`='".$strNagiosQLpath."backup/services/',
									  `nagiosbasedir`='".$strNagiosPath."',
									  `importdir`='".$strNagiosPath."objects/',
									  `conffile`='".$strNagiosPath."nagios.cfg',
									  `last_modified`=NOW()
							  WHERE `target`='localhost'";
		$booReturn			= $this->myDBClass->insertData($strSQL);
		if ($booReturn == false) {
			$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
			$strErrorMessage .=	$this->translate("Inserting path data to database has failed:")." ".str_replace("::","<br>",$this->myDBClass->strErrorMessage)."\n";
			return(1);			
		}
		// Create real paths
		if ($_SESSION['install']['createpath'] == 1) {
			if (is_writable($strNagiosQLpath) && is_dir($strNagiosQLpath) && is_executable($strNagiosQLpath)) {
				if (!file_exists($strNagiosQLpath."hosts")) 			mkdir($strNagiosQLpath."hosts",0755);
				if (!file_exists($strNagiosQLpath."services")) 			mkdir($strNagiosQLpath."services",0755);
				if (!file_exists($strNagiosQLpath."backup")) 			mkdir($strNagiosQLpath."backup",0755);
				if (!file_exists($strNagiosQLpath."backup/hosts")) 		mkdir($strNagiosQLpath."backup/hosts",0755);
				if (!file_exists($strNagiosQLpath."backup/services")) 	mkdir($strNagiosQLpath."backup/services",0755);
				$strStatusMessage = "<span class=\"green\">".$this->translate("done")."</span> (".$this->translate("Check the permissions of the created paths!").")";
				return(0);
			} else {
				$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
				$strErrorMessage .=	$this->translate("NagiosQL config path is not writeable - only database values updated")."<br>\n";
				return(1);
			}
		}
		$strStatusMessage = "<span class=\"green\">".$this->translate("done")."</span>";
		return(0);
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Converting NagiosQL database to utf-8
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function convQLDB(&$strStatusMessage,&$strErrorMessage) {
		$strDBError = "";
		if ($_SESSION['install']['dbtype'] == "mysqli") {
		    $strSQL     = "ALTER DATABASE `".$_SESSION['install']['dbname']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$booReturn  = $this->myDBClass->insertData($strSQL);
			$strDBError = str_replace("::","<br>",$this->myDBClass->strErrorMessage);
		}
		if ($strDBError == "") {
			$strStatusMessage = "<span class=\"green\">".$this->translate("done")."</span>";
			return(0);
		} else {
			$strErrorMessage .= $this->translate("Database errors while converting to utf-8:")."<br>".$strDBError."<br>\n";
			$strStatusMessage = "<span class=\"red\">".$this->translate("failed")."</span>";
			return(1);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Converting NagiosQL database tables to utf-8
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function convQLDBTables(&$strStatusMessage,&$strErrorMessage) {
		// Read version string from DB
		if ($_SESSION['install']['dbtype'] == "mysqli") {
		    $strSQL    = "SHOW TABLES FROM `".$_SESSION['install']['dbname']."`";
			$booReturn  = $this->myDBClass->getDataArray($strSQL,$arrDataset,$intDataCount);
			if ($intDataCount != 0) {
				foreach ($arrDataset AS $elem) {
					if ($intError == 1) continue;
					$strSQL    = "ALTER TABLE `".$elem[0]."` DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
					$booReturn = $this->myDBClass->insertData($strSQL);
					if ($booReturn == false) {
						$intError 		= 1;
						$strDBError 	= str_replace("::","<br>",$this->myDBClass->strErrorMessage);
					}
				}
			}
		} else {
			$strErrorMessage .= translate("Database type not defined!")." (".$_SESSION['install']['dbtype'].")<br>\n";
			$strStatusMessage = "<span class=\"red\">".translate("failed")."</span>";
			return(1);	
		}
		if ($strDBError == "") {
			$strStatusMessage = "<span class=\"green\">".translate("done")."</span>";
			return(0);
		} else {
			$strErrorMessage .= translate("Database errors while converting to utf-8:")."<br>".$strDBError."<br>\n";
			$strStatusMessage = "<span class=\"red\">".translate("failed")."</span>";
			return(1);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Converting NagiosQL database tables to utf-8
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Parameter:  		strStatusMessage	Array variable for status message
	//						$strErrorMessage	Error string
	//   
  	//  Return values:		Status variable (0=ok,1=failed)
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function convQLDBFields(&$strStatusMessage,&$strErrorMessage) {
		// Read version string from DB
		$strSQL1   = "SHOW TABLES FROM `".$_SESSION['install']['dbname']."`";
		$booReturn  = $this->myDBClass->getDataArray($strSQL1,$arrDataset1,$intDataCount1);
		if ($intDataCount1 != 0) {
			foreach ($arrDataset1 AS $elem1) {
				if ($intError == 1) continue;
				$strSQL2	= "SHOW FULL FIELDS FROM `".$elem1[0]."` WHERE (`Type` LIKE '%varchar%' OR `Type` LIKE '%enum%' 
												OR `Type` LIKE '%text%') AND Collation <> 'utf8_unicode_ci'";
				$booReturn  = $this->myDBClass->getDataArray($strSQL2,$arrDataset2,$intDataCount2);
				if ($intDataCount2 != 0) {
					foreach ($arrDataset2 AS $elem2) {
						if ($intError2 == 1) continue;
						if (($elem2[5] === NULL) && ($elem2[3] == 'YES')){
							$strDefault = "DEFAULT NULL";
						} else if ($elem2[5] != '') {
							$strDefault = "DEFAULT '".$elem2[5]."'";
						} else {
							$strDefault = "";
						}
						if ($elem2[3] == 'YES') { $strNull = 'NULL'; } else { $strNull = 'NOT NULL'; }
						$strSQL3 	= "ALTER TABLE `".$elem[0]."` CHANGE `".$elem2[0]."` `".$elem2[0]."` ".$elem2[1]." 
												   CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' $strNull $strDefault";
						$booReturn = $this->myDBClass->insertData($strSQL3);
						if ($booReturn == false) {
							$intError2 		= 1;
							$strDBError 	= "Table:".$elem[0]." - Field: ".$elem2[0]." ".$this->myDBClass->strErrorMessage;
						}
					}
				}
			}
		} else {
			$strErrorMessage .= translate("Database type not defined!")." (".$_SESSION['install']['dbtype'].")<br>\n";
			$strStatusMessage = "<span class=\"red\">".translate("failed")."</span>";
			return(1);	
		}
		if ($strDBError == "") {
			$strStatusMessage = "<span class=\"green\">".translate("done")."</span>";
			return(0);
		} else {
			$strErrorMessage .= translate("Database errors while converting to utf-8:")."<br>".$strDBError."<br>\n";
			$strStatusMessage = "<span class=\"red\">".translate("failed")."</span>";
			return(1);
		}
	}
}
?>