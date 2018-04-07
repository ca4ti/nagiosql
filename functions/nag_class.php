<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2011 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Visualization Class
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-03-28 07:49:21 +0200 (Mo, 28. Mär 2011) $
// Author    : $LastChangedBy: martin $
// Version   : 3.1.1
// Revision  : $LastChangedRevision: 1068 $
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
// Name: nagvisual
//
// Class variables:
// $arrSettings  		Includes all global settings ($SETS)
// $intDomainId			Domain ID
// $strDBMessage		Process messages
// $myDBClass     		MySQL database class object
//
///////////////////////////////////////////////////////////////////////////////////////////////
class nagvisual {
  	// Define class variables
    var $arrSettings;       // Will be filled in class constructor
  	var $intDomainId  = 0;  // Will be filled in class constructor
  	var $strDBMessage = ""; // Will be filled in functions
  	var $myDBClass;         // Will be filled in prepend_adm.php
	var $myContentTpl;		// Content template object
	var $dataId;			// Content data ID

	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Class constructor
  	///////////////////////////////////////////////////////////////////////////////////////////
  	//
  	//  Activities during initialisation
  	//
  	///////////////////////////////////////////////////////////////////////////////////////////
  	function nagvisual() {
    	// Read global settings
    	$this->arrSettings = $_SESSION['SETS'];
    	if (isset($_SESSION['domain'])) $this->intDomainId = $_SESSION['domain'];
  	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
    //  Function: Get menu position
    ///////////////////////////////////////////////////////////////////////////////////////////
    //
    //  Determines the actual position inside the menu tree and returns it as an info line
  	//
  	//  Parameters:  		$intMain  	Current main menu id
  	//            			$intSub   	Current sub menu id (0 if no sub menu is selected)
  	//            			$strTop     Label string for the root node
  	//
  	//  Return value:     	HTML info string
  	//
  	///////////////////////////////////////////////////////////////////////////////////////////
  	function getPosition($intMain,$intSub=0,$strTop="") {
    	$strPosition = "";
    	$strSQLMain  = "SELECT `item`, `link` FROM `tbl_mainmenu` WHERE `id` = $intMain";
    	$booReturn 	 = $this->myDBClass->getDataArray($strSQLMain,$arrDataMain,$intDataCountMain);
    	if ($booReturn && ($intDataCountMain != 0)) {
      		$strMainLink 	= $this->arrSettings['path']['root'].$arrDataMain[0]['link'];
      		$strMain 		= translate($arrDataMain[0]['item']);
      		if ($strTop != "") {
        		$strPosition .= "<a href='".$_SESSION['SETS']['path']['root']."admin.php'>".$strTop."</a> -> ";
      		}
      		$strPosition .= "<a href='".$strMainLink."'>".translate($strMain)."</a>";
    	}
    	if ($intSub != 0) {
      		$strSQLSub  = "SELECT `item`, `link` FROM `tbl_submenu` WHERE `id_main` = $intMain AND `id` = $intSub";
      		$booReturn 	= $this->myDBClass->getDataArray($strSQLSub,$arrDataSub,$intDataCountSub);
      		if ($booReturn && ($intDataCountSub != 0)) {
        		$strSubLink = $this->arrSettings['path']['root'].$arrDataSub[0]['link'];
        		$strSub 	= translate($arrDataSub[0]['item']);
				if ($strSub != "") {
					$strPosition .= " -> <a href='".$strSubLink."'>".translate($strSub)."</a>";
				}
			}
    	}
    	return $strPosition;
  	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
  	//  Function: Display main menu
  	///////////////////////////////////////////////////////////////////////////////////////////
  	//
  	//  Build the main menu and display them
  	//
  	//  Parameters:  		$intMain  	Current main menu id
  	//            			$intSub   	Current sub menu id (0 if no sub menu is selected)
  	//            			$intMenu  	Menu group ID
  	//
  	//  Return value:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
  	//
  	///////////////////////////////////////////////////////////////////////////////////////////
  	function getMenu($intMain,$intSub,$intMenu) {
		// Modify URL for visible/invisible menu
    	$strQuery = str_replace("menu=visible&","",$_SERVER['QUERY_STRING']);
    	$strQuery = str_replace("menu=invisible&","",$strQuery);
    	$strQuery = str_replace("menu=visible","",$strQuery);
    	$strQuery = str_replace("menu=invisible","",$strQuery);
    	if ($strQuery != "") {
      		$strURIVisible   = str_replace("&","&amp;",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING)."?menu=visible&".$strQuery);
      		$strURIInvisible = str_replace("&","&amp;",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING)."?menu=invisible&".$strQuery);
    	} else {
      		$strURIVisible   = filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING)."?menu=visible";
      		$strURIInvisible = filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING)."?menu=invisible";
    	}
		// Get main menu items from database and store them to an array
    	$strSQLMain = "SELECT `id`, `item`, `link` FROM `tbl_mainmenu` WHERE `menu_id` = $intMenu ORDER BY `order_id`";
    	$strSQLSub  = "SELECT `id`, `item`, `link`, `access_group` FROM `tbl_submenu` WHERE `id_main` = $intMain ORDER BY `order_id`";
    	$booReturn 	= $this->myDBClass->getDataArray($strSQLMain,$arrDataMain,$intDataCountMain);
    	if ($booReturn && ($intDataCountMain != 0)) {
      		$y=1;
      		for ($i=0;$i<$intDataCountMain;$i++) {
        		$arrMainLink[$y] = $this->arrSettings['path']['root'].$arrDataMain[$i]['link'];
        		$arrMainId[$y]   = $arrDataMain[$i]['id'];
        		$arrMain[$y]   	 = translate($arrDataMain[$i]['item']);
        		$y++;
      		}
    	} else {
      		return(1);
    	}
    	// Get sub menu items from database and store them to an array
    	$booReturn = $this->myDBClass->getDataArray($strSQLSub,$arrDataSub,$intDataCountSub);
    	if ($booReturn && ($intDataCountSub != 0)) {
      		$y=1;
      		for ($i=0;$i<$intDataCountSub;$i++) {
				// Check for access rights - insert only menu items for which an user is granted
				if ($this->checkAccGroup($_SESSION['userid'],$arrDataSub[$i]['access_group']) == 0) {
          			$arrSubLink[$y] = $this->arrSettings['path']['root'].$arrDataSub[$i]['link'];
          			$arrSubID[$y]   = $arrDataSub[$i]['id'];
          			$arrSub[$y]     = translate($arrDataSub[$i]['item']);
          			$y++;
        		}
      		}
    	}
		//
		// Display the menu structure
		// ==========================
    	if (!(isset($_SESSION['menu'])) || ($_SESSION['menu'] != "invisible")) {
      		// Menu visible
      		echo "<td width=\"150\" align=\"center\" valign=\"top\">\n";
      		echo "<table cellspacing=\"1\" class=\"menutable\">\n";
      		// Process every main menu item
      		for ($i=1;$i<=count($arrMain);$i++) {
        		echo "<tr>\n";
        		if ($arrMainId[$i] == $intMain) {
          			echo "<td class=\"menuaktiv\"><a href=\"".$arrMainLink[$i]."\">".$arrMain[$i]."</a></td>\n</tr>\n";
          			// if a sub menu item is present
          			if (isset($arrSub)) {
            			echo "<tr>\n<td class=\"menusub\">\n";
            			// Process every sub menu item
            			for ($y=1;$y<=count($arrSub);$y++) {
              				if ((isset($arrSubLink[$y])) && ($arrSubLink[$y] != "")) {
                				if ($arrSubID[$y] == $intSub) {
                  					echo "<a class=\"menulink\" href=\"".$arrSubLink[$y]."\"><b>".$arrSub[$y]."</b></a><br>\n";
                				} else {
                  					echo "<a class=\"menulink\" href=\"".$arrSubLink[$y]."\">".$arrSub[$y]."</a><br>\n";
                				}
              				}
            			}
            			echo "</td>\n</tr>\n";
          			}
        		} else {
          			echo "<td class=\"menuinaktiv\"><a href=\"".$arrMainLink[$i]."\">".$arrMain[$i]."</a></td>\n</tr>\n";
        		}
      		}
      		echo "</table>\n";
      		echo "<br><a href=\"$strURIInvisible\" class=\"menulinksmall\">[".translate('Hide menu')."]</a>\n";
      		echo "</td>\n";
    	} else {
      		// Menu invisible
      		echo "<td valign=\"top\">\n";
      		echo "<a href=\"$strURIVisible\"><img src=\"".$this->arrSettings['path']['root']."images/menu.gif\" alt=\"".translate('Show menu')."\" border=\"0\" ></a>\n";
      		echo "</td>\n";
    	}
    	return(0);
  	}

	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Function: Process "null" values
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Replaces "NULL" with -1
	//
  	//  Parameters:  		$strKey		Process string
	//
  	//  Return value:		Midified process string
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function checkNull($strKey) {
    	if (strtoupper($strKey) == "NULL") {
      		return("-1");
    	}
    	return($strKey);
  	}

	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Processing path strings
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Adds a "/" after a parh string and replaces double "//" with "/"
	//
  	//  Parameters:  		$strPath	Path string
	//
  	//  Return value:		Modified path string
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function addSlash($strPath) {
    	if ($strPath == "") return("");
    	$strPath = $strPath."/";
		while(substr_count($strPath,"//") != 0) {
    		$strPath = str_replace("//","/",$strPath);
		}
    	return ($strPath);
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Processing message strings
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Merge message strings and check for duplicate messages
	//
  	//  Parameters:  		$strNewMessage	Message to add
	//
  	//  Return value:		Modified message string
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function processMessage($strNewMessage,&$strMessage) {
		if (($strMessage != "") && ($strNewMessage != "")) {
			if (substr_count($strMessage,$strNewMessage) == 0) {
				$strMessage .= "<br>".$strNewMessage;
			}
		} else {
			$strMessage .= $strNewMessage;
		}
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Check account group
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Checks if an user has acces to an account group
	//
  	//  Parameters:  		$intUserId		User ID
	//						$intGroupId		Group ID
	//
  	//  Return value:		0 = access granted
	//						1 = no access
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function checkAccGroup($intUserId,$intGroupId) {
		// Admin braucht keine Berechtigung
		if ($intUserId == 1)  return(0);
		// Gruppe 0 hat uneingeschränkte Rechte
		if ($intGroupId == 0) return(0);
		// Datenbank abfragen
		$strSQL    = "SELECT * FROM `tbl_lnkGroupToUser` WHERE `idMaster` = $intGroupId AND `idSlave`=$intUserId AND `read`='1'";
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrDataMain,$intDataCount);
		if (($booReturn != false) && ($intDataCount != 0)) {
			return(0);
		}
		return(1);
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Returns read groups
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Returns any group ID with read access for the submitted user id
	//
  	//  Parameters:  		$intUserId		User ID
	//
  	//  Return value:		Comma separated string with group id's
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function getAccGroupRead($intUserId) {
		$strReturn = "0,";
		// Admin becomes rights to all groups
		if ($intUserId == 1) {
			$strSQL = "SHOW TABLE STATUS LIKE 'tbl_group'";
			$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intCount);
			if ($booReturn && ($intCount != 0)) { 
				for ($i=1;$i<=$arrData[0]['Auto_increment'];$i++) {
					$strReturn .= $i.",";  
				}
			}
			$strReturn = substr($strReturn,0,-1);
			return $strReturn;
		}
		$strSQL    = "SELECT `idMaster` FROM `tbl_lnkGroupToUser` WHERE `idSlave`=".$_SESSION['userid']." AND `read`='1'";
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intCount);
		if ($booReturn && ($intCount != 0)) { 
			foreach (  $arrData AS $elem ) {
				$strReturn .= $elem['idMaster'].","; 	
			}
		}
		$strReturn = substr($strReturn,0,-1);
		return $strReturn;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Returns link groups
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Returns any group ID with link access for the submitted user id
	//
  	//  Parameters:  		$intUserId		User ID
	//
  	//  Return value:		Comma separated string with group id's
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function getAccGroupLink($intUserId) {
		$strReturn = "0,";
		// Admin becomes rights to all groups
		if ($intUserId == 1) {
			$strSQL = "SHOW TABLE STATUS LIKE 'tbl_group'";
			$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intCount);
			if ($booReturn && ($intCount != 0)) { 
				for ($i=1;$i<=$arrData[0]['Auto_increment'];$i++) {
					$strReturn .= $i.",";  
				}
			}
			$strReturn = substr($strReturn,0,-1);
			return $strReturn;
		}
		$strReturn = "0,";
		$strSQL    = "SELECT `idMaster` FROM `tbl_lnkGroupToUser` WHERE `idSlave`=".$_SESSION['userid']." AND `link`='1'";
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intCount);
		if ($booReturn && ($intCount != 0)) { 
			foreach (  $arrData AS $elem ) {
				$strReturn .= $elem['idMaster'].","; 	
			}
		}
		$strReturn = substr($strReturn,0,-1);
		return $strReturn;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Returns write groups
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Returns any group ID with link access for the submitted user id
	//
  	//  Parameters:  		$intUserId		User ID
	//
  	//  Return value:		Comma separated string with group id's
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function checkAccGroupWrite($intUserId,$intGroupId) {
		// Admin braucht keine Berechtigung
		if ($intUserId == 1)  return(0);
		// Gruppe 0 hat uneingeschränkte Rechte
		if ($intGroupId == 0) return(0);
		// Datenbank abfragen
		$strSQL    = "SELECT * FROM `tbl_lnkGroupToUser` WHERE `idMaster` = $intGroupId AND `idSlave`=$intUserId AND `write`='1'";
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrDataMain,$intDataCount);
		if (($booReturn != false) && ($intDataCount != 0)) {
			return(0);
		}
		return(1);
	}
  
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Build site numbers
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Build a string which contains links for additional pages. This is used in data lists
	//  with more items then defined in settings "lines per page limit"
	//
  	//  Parameters:  		$strSite    	Link to page
	//            			$intCount   	Sum of all data lines
	//            			$chkLimit   	Actual data limit
	//            			$chkSelOrderBy  OrderBy-String (for services page)
	//
  	//  Return value:		HTML string
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function buildPageLinks($strSite,$intDataCount,$chkLimit,$chkSelOrderBy="") {
		$intMaxLines  = $this->arrSettings['common']['pagelines'];
		$intCount     = 1;
		$intCheck 	  = 0;
		$strSiteHTML  = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n<tr>\n<td class=\"sitenumber\" ";
		$strSiteHTML .= "style=\"padding-left:7px; padding-right:7px;\">".translate('Page').": </td>\n";
		for ($i=0;$i<$intDataCount;$i=$i+$intMaxLines) {
			if ($chkSelOrderBy == "") {
				$strLink1 = "<a href=\"".$strSite."?limit=$i\">"; 
				$strLink2 = "onclick=\"location.href='".$strSite."?limit=$i'\""; 
			} else {
				$strOrderBy = rawurlencode($chkSelOrderBy);
				$strLink1 = "<a href=\"".$strSite."?limit=$i&orderby=$chkSelOrderBy\">"; 
				$strLink2 = "onclick=\"location.href='".$strSite."?limit=$i&orderby=$chkSelOrderBy'\""; 
			}
			if ((!(($chkLimit >= ($i+($intMaxLines*5))) || ($chkLimit <= ($i-($intMaxLines*5))))) || ($i==0) || ($i>=($intDataCount-$intMaxLines))) {
				if ($chkLimit == $i) {
					$strSiteHTML .= "<td class=\"sitenumber-sel\">$intCount</td>\n";	
				} else {
					$strSiteHTML .= "<td class=\"sitenumber\" $strLink2>".$strLink1.$intCount."</a></td>\n";
				}
				$intCheck = 0;
			} else if ($intCheck == 0) {
				$strSiteHTML .= "<td class=\"sitenumber\">...</td>\n";
				$intCheck = 1;
			}
			$intCount++;
		}
		$strSiteHTML .= "</tr>\n</table>\n";
    	if ($intCount > 2) {
      		return($strSiteHTML);
    	} else {
      		return("");
    	}
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Insert Domain list
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Inserts the domain list to the list view template
	//
  	//  Parameters:  		$resTemplate    Template object
	//            			$intCount   	Sum of all data lines
	//            			$chkLimit   	Actual data limit
	//            			$chkSelOrderBy  OrderBy-String (for services page)
	//
  	//  Return value:		HTML string
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function insertDomainList($resTemplate) {
		$strSQL    = "SELECT * FROM `tbl_domain` WHERE `active` <> '0' ORDER BY `domain`";
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrDataDomain,$intDataCount);
		if ($booReturn == false) {
			$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
		} else {
			foreach($arrDataDomain AS $elem) {
				// Check acces rights
				if ($this->checkAccGroup($_SESSION['userid'],$elem['access_group']) == 0) {
					$resTemplate->setVariable("DOMAIN_ID",$elem['id']);
					$resTemplate->setVariable("DOMAIN_NAME",$elem['domain']);
					if ($_SESSION['domain'] == $elem['id']) {
						$resTemplate->setVariable("DOMAIN_SEL","selected");
					}
					$resTemplate->parse("domainlist");
				}
			}
		}
	}
	

	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Parse selection field (simple)
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Builds a simple selection field inside a template
	//
  	//  Parameters:  		$strTable     	Table name (source data)
	//                		$strTabField  	Field name (source data)
	//						$strTemplKey  	Template key
	//			    		$intModeId    	0=only data, 1=with empty line at the beginning, 
	//							  			2=with empty line and 'null' line at the beginning
	//			    		$intSelId     	Selected data ID (from master table)
	//						$intExclId	  	Exclude ID
	//				
	//
  	//  Return value:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function parseSelectSimple($strTable,$strTabField,$strTemplKey,$intModeId=0,$intSelId=-9,$intExclId=-9) {
    	// Compute option value
		$intOption = 0;
		if ($strTemplKey == 'hostcommand') 		$intOption = 1;
		if ($strTemplKey == 'servicecommand') 	$intOption = 1;
		if ($strTemplKey == 'eventhandler') 	$intOption = 2;
		if ($strTemplKey == 'service_extinfo') 	$intOption = 7;
    	// Get version
    	$this->myConfigClass->getConfigData("version",$intVersion);
		// Get link rights
		$strAccess = $this->getAccGroupLink($_SESSION['userid']);
		// Get raw data
		$booRaw = $this->getSelectRawdata($strTable,$strTabField,$arrData,$intOption);
    	if ($booRaw == 0) {
	  		// Insert an empty line in mode 1
	  		if (($intModeId == 1) || ($intModeId == 2)) {
      			$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey),"&nbsp;");
				$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey).'_ID',0);
      			if ($intVersion != 3) $this->myContentTpl->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
      			$this->myContentTpl->parse($strTemplKey);
	  		}
	  		// Insert a 'null' line in mode 2
	  		if ($intModeId == 2) {
				$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey),"null");
				$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey).'_ID',-1);
				if ($intVersion != 3) $this->myContentTpl->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
				if ($intSelId == -1)  $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey)."_SEL","selected");
				$this->myContentTpl->parse($strTemplKey);
	  		}
			// Insert data sets
			foreach ($arrData AS $elem) {
				if ($elem['key'] == $intExclId) continue;
				if (isset($elem['config_id']) && $elem['config_id'] == 0) {
					$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey),$elem['value'].' [common]');
				} else {
					$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey),$elem['value']);
				}
				$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey)."_ID",$elem['key']);
				if ($intVersion != 3) $this->myContentTpl->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
				if ($intSelId == $elem['key']) {
					$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey)."_SEL","selected");
				}
				$this->myContentTpl->parse($strTemplKey);
			}
	  		return(0);
  		}
		return(1);
  	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Function: Parse selection field (multi)
	///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Builds a multi selection field inside a template
	//
  	//  Parameters:  		$strTable     	Table name (source data)
	//                		$strTabField  	Field name (source data)
	//						$strTemplKey  	Template key
	//						$intDataId	  	Data ID of master table
	//			    		$intModeId    	0 = only data 
	//							  			1 = with empty line at the beginning
	//							  			2 = with * line at the beginning
	//			    		$intTypeId    	Type ID (from master table)
	//						$intExclId	  	Exclude ID
	//						$strRefresh	  	Session token for refresh mode
	//				
	//
  	//  Return value:		0 = successful
	//						1 = error
	//						Status message is stored in class variable  $this->strDBMessage
	//
	///////////////////////////////////////////////////////////////////////////////////////////
  	function parseSelectMulti($strTable,$strTabField,$strTemplKey,$strLinkTable,$intModeId=0,$intTypeId=-9,$intExclId=-9,$strRefresh='') {
    	// Compute option value
		$intOption  = 0;
		$intRefresh = 0;
		if ($strLinkTable == 'tbl_lnkContactToCommandHost')    			$intOption = 2;
		if ($strLinkTable == 'tbl_lnkContactToCommandService') 			$intOption = 2;
		if ($strLinkTable == 'tbl_lnkContacttemplateToCommandHost')    	$intOption = 2;
		if ($strLinkTable == 'tbl_lnkContacttemplateToCommandService') 	$intOption = 2;
		if ($strLinkTable == 'tbl_lnkServicegroupToService')   			$intOption = 3;
		if ($strLinkTable == 'tbl_lnkServicedependencyToService_DS')    $intOption = 4;
		if ($strLinkTable == 'tbl_lnkServicedependencyToService_S')    	$intOption = 5;
		if ($strLinkTable == 'tbl_lnkServiceescalationToService')    	$intOption = 6;
		if ($strTemplKey  == 'host_services')							$intOption = 8;
		// Get version
    	$this->myConfigClass->getConfigData("version",$intVersion);
		// Get raw data
		$booRaw = $this->getSelectRawdata($strTable,$strTabField,$arrData,$intOption);
		// Get selected data
		$booSel = $this->getSelectedItems($strLinkTable,$arrSelected,$intOption);
		// Refresh processing (replaces selection array)
		if ($strRefresh != '') {
			if (isset($_SESSION['refresh']) && isset($_SESSION['refresh'][$strRefresh]) && is_array($_SESSION['refresh'][$strRefresh])) {
				$arrSelected = $_SESSION['refresh'][$strRefresh];
				$intRefresh  = 1;
				$booSel 	 = 0;
			}
		}
    	if ($booRaw == 0) {
	  		$intCount = 0;
			// Insert an empty line in mode 1
	  		if ($intModeId == 1) {
				$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey),"&nbsp;");
				$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey).'_ID',0);
				if ($intVersion != 3) $this->myContentTpl->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
				$this->myContentTpl->parse($strTemplKey);
				$intCount++;
	  		}
			// Insert an * line in mode 2
			if ($intModeId == 2) {
				$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey),"*");
				$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey).'_ID',"*");
				if ($intVersion != 3) $this->myContentTpl->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
				if ($intTypeId  == 2) $this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey)."_SEL","selected");
				if (($intRefresh == 1) && (in_array('*',$arrSelected))) {
					$this->myContentTpl->setVariable("DAT_".strtoupper($strTemplKey)."_SEL","selected");
					$this->myContentTpl->setVariable("CLASS_".strtoupper($strTemplKey)."_SEL","class=\"ieselected\"");
					$this->myContentTpl->setVariable("CLASS_".strtoupper($strTemplKey)."_SEL_SINGLE","ieselected");
				}
				$intCount++;
				$this->myContentTpl->parse($strTemplKey);
			}
			// Insert data sets
			foreach ($arrData AS $elem) {
				if ($elem['key'] == $intExclId) continue;
				if ($elem['value'] == "") continue;
				$intIsSelected = 0;
				$intIsExcluded = 0;
				if (isset($elem['config_id']) && $elem['config_id'] == 0) {
					$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey),$elem['value'].' [common]');
				} else {
					$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey),$elem['value']);
				}
				$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey)."_ID",$elem['key']);
				$this->myContentTpl->setVariable('CLASS_SEL',"");
				if ($intVersion != 3) $this->myContentTpl->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
				if (($booSel == 0) && (in_array($elem['key'],$arrSelected)))   $intIsSelected = 1;
				if (($booSel == 0) && (in_array($elem['value'],$arrSelected))) $intIsSelected = 1;
				// Exclude rule
				if (($booSel == 0) && (in_array("e".$elem['key'],$arrSelected))) 		$intIsExcluded = 1;
				if (($booSel == 0) && (in_array("e"."::".$elem['value'],$arrSelected))) $intIsExcluded = 1;
				if ($intIsExcluded == 1) {
					if (isset($elem['config_id']) && $elem['config_id'] == 0) {
						$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey),'!'.$elem['value'].' [common]');
					} else {
						$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey),'!'.$elem['value']);
					}
					$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey)."_ID",'e'.$elem['key']);
				}
				if (($intIsSelected == 1) || ($intIsExcluded == 1)) {
					$this->myContentTpl->setVariable("DAT_".strtoupper($strTemplKey)."_SEL","selected");
					$this->myContentTpl->setVariable("CLASS_".strtoupper($strTemplKey)."_SEL","class=\"ieselected\"");
					$this->myContentTpl->setVariable("CLASS_".strtoupper($strTemplKey)."_SEL_SINGLE","ieselected");
				}
				$intCount++;
				$this->myContentTpl->parse($strTemplKey);
			}
			if ($intCount == 0) {
				// Insert an empty line to create valid HTML select fields
				$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey),"&nbsp;");
				$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey).'_ID',0);
				$this->myContentTpl->parse($strTemplKey);
			}
	  		return(0);
  		}
		// Insert an empty line to create valid HTML select fields
		$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey),"&nbsp;");
		$this->myContentTpl->setVariable('DAT_'.strtoupper($strTemplKey).'_ID',0);
		$this->myContentTpl->parse($strTemplKey);
		return(1);
  	}

    //3.1 HELP FUNCTIONS
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Help function: Get raw data
  	///////////////////////////////////////////////////////////////////////////////////////////
	//  $strTable			-> Table name
	//  $strTabField		-> Data field name
	//  $arrData			-> Raw data array
	//  $intOption			-> Option value
	//	Return value		-> 0=successful / 1=error
	///////////////////////////////////////////////////////////////////////////////////////////
  	function getSelectRawdata($strTable,$strTabField,&$arrData,$intOption=0) {
		// Get link rights
		$strAccess = $this->getAccGroupLink($_SESSION['userid']);
		// Define SQL commands
		if ($strTable == 'tbl_group') {
			$strSQL  = "SELECT `id` AS `key`, `".$strTabField."` AS `value` FROM `tbl_group` WHERE `active`='1' 
					   	AND `".$strTabField."` <> '' AND `".$strTabField."` IS NOT NULL ORDER BY `".$strTabField."`"; 
		} else if (($strTable == 'tbl_command') && ($intOption == 1)) {
		   	$strSQL  = "SELECT `id` AS `key`, `".$strTabField."` AS `value`, `config_id` FROM `".$strTable."` WHERE (`config_id`=".$this->intDomainId." OR `config_id`=0) 
					   	AND `".$strTabField."` <> '' AND `".$strTabField."` IS NOT NULL AND `access_group` IN ($strAccess) 
					   	AND (`command_type` = 0 OR `command_type` = 1) ORDER BY `".$strTabField."`"; 
		} else if (($strTable == 'tbl_command') && ($intOption == 2)) {
		   	$strSQL  = "SELECT `id` AS `key`, `".$strTabField."` AS `value`, `config_id` FROM `".$strTable."` WHERE (`config_id`=".$this->intDomainId." OR `config_id`=0) 
					   	AND `".$strTabField."` <> '' AND `".$strTabField."` IS NOT NULL AND `access_group` IN ($strAccess) 
					   	AND (`command_type` = 0 OR `command_type` = 2) ORDER BY `".$strTabField."`"; 
		} else if (($strTable == 'tbl_timeperiod') && ($strTabField == 'name')) {
		   	$strSQL  = "SELECT `id` AS `key`, `timeperiod_name` AS `value`, `config_id` FROM `tbl_timeperiod` WHERE (`config_id`=".$this->intDomainId." OR `config_id`=0) 
					   	AND `timeperiod_name` <> '' AND `timeperiod_name` IS NOT NULL AND `access_group` IN ($strAccess)
					   	UNION
					   	SELECT `id` AS `key`, `name` AS `value`, `config_id` FROM `tbl_timeperiod` WHERE (`config_id`=".$this->intDomainId." OR `config_id`=0) 
					   	AND `name` <> '' AND `name` IS NOT NULL AND `name` <> `timeperiod_name` AND `access_group` IN ($strAccess) 
					   	ORDER BY value"; 
		} else if (($strTable == 'tbl_service') && ($intOption == 3)) {
	   		// Service groups
			$strSQL  = "SELECT CONCAT_WS('::',`tbl_host`.`id`,'0',`tbl_service`.`id`) AS `key`, 
					   	CONCAT('H:',`tbl_host`.`host_name`,',',`tbl_service`.`service_description`) AS `value` FROM `tbl_service`
					   	LEFT JOIN `tbl_lnkServiceToHost` ON `tbl_service`.`id` = `tbl_lnkServiceToHost`.`idMaster`
					   	LEFT JOIN `tbl_host` ON `tbl_lnkServiceToHost`.`idSlave` = `tbl_host`.`id`
					   	WHERE (`tbl_service`.`config_id`=".$this->intDomainId." OR `tbl_service`.`config_id`=0) AND `tbl_service`.`service_description` <> '' 
					   	AND `tbl_service`.`service_description` IS NOT NULL AND `tbl_service`.`host_name` <> 0 AND `tbl_service`.`access_group` IN ($strAccess)
					   	UNION
					   	SELECT CONCAT_WS('::','0',`tbl_hostgroup`.`id`,`tbl_service`.`id`) AS `key`, 
					   	CONCAT('HG:',`tbl_hostgroup`.`hostgroup_name`,',',`tbl_service`.`service_description`) AS `value` FROM `tbl_service`
					   	LEFT JOIN `tbl_lnkServiceToHostgroup` ON `tbl_service`.`id` = `tbl_lnkServiceToHostgroup`.`idMaster`
					   	LEFT JOIN `tbl_hostgroup` ON `tbl_lnkServiceToHostgroup`.`idSlave` = `tbl_hostgroup`.`id`
					   	WHERE (`tbl_service`.`config_id`=".$this->intDomainId." OR `tbl_service`.`config_id`=0) AND `tbl_service`.`service_description` <> '' 
					   	AND `tbl_service`.`service_description` IS NOT NULL AND `tbl_service`.`hostgroup_name` <> 0  AND `tbl_service`.`access_group` IN ($strAccess)
					   	ORDER BY value";
		} else if (($strTable == 'tbl_service') && (($intOption == 4) || ($intOption == 5) || ($intOption == 6))) {
			// Define session variables
			if ($intOption == 6) {
				$strHostVar 	 = 'se_host';
				$strHostGroupVar = 'se_hostgroup';
			} else if ($intOption == 4) {
				$strHostVar 	 = 'sd_dependent_host';
				$strHostGroupVar = 'sd_dependent_hostgroup';
			} else {
				$strHostVar 	 = 'sd_host';
				$strHostGroupVar = 'sd_hostgroup';
			}
			if (isset($_SESSION['refresh']) && 
					(isset($_SESSION['refresh']['sd_dependent_service']) && is_array($_SESSION['refresh']['sd_dependent_service'])) ||
					(isset($_SESSION['refresh']['sd_service']) && is_array($_SESSION['refresh']['sd_service'])) ||
					(isset($_SESSION['refresh']['se_service']) && is_array($_SESSION['refresh']['se_service']))){
				// * Value in hosts
				if (in_array('*',$_SESSION['refresh'][$strHostVar])) {
					$strSQL 	= "SELECT id FROM tbl_host WHERE (`config_id`=".$this->intDomainId." OR `config_id`=0) 
								   AND `access_group` IN ($strAccess)";
					$booReturn = $this->myDBClass->getDataArray($strSQL,$arrDataHost,$intDCHost);
					if ($booReturn && ($intDCHost != 0)) {
						$arrHostTemp = '';
						foreach ($arrDataHost AS $elem) {
							if (in_array("e".$elem['id'],$_SESSION['refresh'][$strHostVar])) continue;
							$arrHostTemp[] = $elem['id'];												  
						}
					}
					$strHosts 		= "'".implode("','",$arrHostTemp)."'";
				} else {
					$strHosts 		= "'".implode("','",$_SESSION['refresh'][$strHostVar])."'";
				}
				// * Value in host groups
				if (in_array('*',$_SESSION['refresh'][$strHostGroupVar])) {
					$strSQL 	= "SELECT id FROM tbl_host WHERE (`config_id`=".$this->intDomainId." OR `config_id`=0) 
								   AND `access_group` IN ($strAccess)";
					$booReturn = $this->myDBClass->getDataArray($strSQL,$arrDataHost,$intDCHost);
					if ($booReturn && ($intDCHost != 0)) {
						$arrHostgroupTemp = '';
						foreach ($arrDataHost AS $elem) {
							if (in_array("e".$elem['id'],$_SESSION['refresh'][$strHostGroupVar])) continue;
							$arrHostgroupTemp[] = $elem['id'];												  
						}
					}
					$strHostsGroup 	= "'".implode("','",$arrHostgroupTemp)."'";
				} else {
					$strHostsGroup 	= "'".implode("','",$_SESSION['refresh'][$strHostGroupVar])."'";
				}
				if ($strHosts 		== '')	$strHosts 		= 0;
				if ($strHostsGroup 	== '') 	$strHostsGroup 	= 0;
				// -> check for data - UNION with any host of any selected hostgroup / check services not connected to any selected host
				$strSQL = "SELECT `id` AS `key`, `".$strTabField."` AS `value` FROM `tbl_service` 
						   LEFT JOIN `tbl_lnkServiceToHost` ON `tbl_service`.`id` = `tbl_lnkServiceToHost`.`idMaster` 
						   WHERE (`config_id`=".$this->intDomainId." OR `config_id`=0)
						   AND `tbl_lnkServiceToHost`.`idSlave` IN ($strHosts) 
						   AND `".$strTabField."` <> '' AND `".$strTabField."` IS NOT NULL AND `access_group` IN ($strAccess) 
						   GROUP BY `value` 
						   UNION 
						   SELECT `id` AS `key`, `".$strTabField."` AS `value` FROM `tbl_service` 
						   LEFT JOIN `tbl_lnkServiceToHostgroup` ON `tbl_service`.`id` = `tbl_lnkServiceToHostgroup`.`idMaster` 
						   WHERE (`config_id`=".$this->intDomainId." OR `config_id`=0)
						   AND `tbl_lnkServiceToHostgroup`.`idSlave` IN ($strHostsGroup) 
						   AND `".$strTabField."` <> '' AND `".$strTabField."` IS NOT NULL AND `access_group` IN ($strAccess) 
						   GROUP BY `value` 
						   UNION 
						   SELECT `id` AS `key`, `".$strTabField."` AS `value` FROM `tbl_service` 
						   WHERE (`config_id`=".$this->intDomainId." OR `config_id`=0)
						   AND `host_name`=2 OR  `hostgroup_name`=2
						   AND `".$strTabField."` <> '' AND `".$strTabField."` IS NOT NULL AND `access_group` IN ($strAccess) 
						   GROUP BY `value` ORDER BY `value`";
			} else {
				$strSQL = "";	
			}
		} else if (($strTable == 'tbl_service') && ($intOption == 7)) {
			if (isset($_SESSION['refresh']) && isset($_SESSION['refresh']['se_host'])) {
				$strHostId = $_SESSION['refresh']['se_host'];
				$strSQL  = "SELECT `tbl_service`.`id` AS `key`, `tbl_service`.`".$strTabField."` AS `value` FROM `tbl_service`
							LEFT JOIN `tbl_lnkServiceToHost` ON `tbl_service`.`id` = `tbl_lnkServiceToHost`.`idMaster`
							WHERE (`config_id`=".$this->intDomainId." OR `config_id`=0) AND `tbl_lnkServiceToHost`.`idSlave` = $strHostId
							AND `".$strTabField."` <> '' AND `".$strTabField."` IS NOT NULL AND `access_group` IN ($strAccess) ORDER BY `".$strTabField."`";
			} else {
				$strSQL = "";	
			}
		} else if (($strTable == 'tbl_service') && ($intOption == 8)) {	
			// Service selection inside Host definition
			$strSQL  = "SELECT `tbl_service`.`id` AS `key`, CONCAT(`tbl_service`.`config_name`, ' - ', `tbl_service`.`service_description`) AS `value` 
						FROM `tbl_service` WHERE (`config_id`=".$this->intDomainId." OR `config_id`=0) AND `tbl_service`.`config_name` <> '' 
						AND `tbl_service`.`config_name` IS NOT NULL AND `tbl_service`.`service_description` <> '' AND `tbl_service`.`service_description` IS NOT NULL 
						AND `access_group` IN ($strAccess) ORDER BY `".$strTabField."`";
		} else {
	   		// Common statement
			$strSQL  = "SELECT `id` AS `key`, `".$strTabField."` AS `value`, `config_id` FROM `".$strTable."` WHERE (`config_id`=".$this->intDomainId." OR `config_id`=0)
				   		AND `".$strTabField."` <> '' AND `".$strTabField."` IS NOT NULL AND `access_group` IN ($strAccess) ORDER BY `".$strTabField."`";
		}
		// Process data
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrDataRaw,$intDataCount);
		if (mysql_error() != "") {
			$this->strDBMessage = mysql_error()."<br>".$strSQL;
		}
		if ($strTable == 'tbl_group') {
			$arrTemp = "";
			$arrTemp['key']   = 0;
			$arrTemp['value'] = translate('Unrestricted access');
			$arrData[] = $arrTemp;
		}		
		if ($booReturn && ($intDataCount != 0)) {
			foreach ($arrDataRaw AS $elem) {
				$arrData[] = $elem;
			}
			return(0);
		} else {
			if ($strTable == 'tbl_group') return(0);
			$arrData = array('key' => 0, 'value' => 'no data');
			return(1);
		}
  	}
	///////////////////////////////////////////////////////////////////////////////////////////
  	//  Help function: Get selected data
  	///////////////////////////////////////////////////////////////////////////////////////////
	//  $strLinkTable		-> Link table name
	//  $arrSelect			-> Selected data array
	//  $intOption			-> Option parameter
	//	Return value		-> 0=successful / 1=error
	///////////////////////////////////////////////////////////////////////////////////////////
  	function getSelectedItems($strLinkTable,&$arrSelect,$intOption=0) {
		// Define SQL commands
		if ($intOption == 8) {
			$strSQL = "SELECT * FROM `".$strLinkTable."` WHERE `idSlave`=".$this->dataId;
		} else {
			$strSQL = "SELECT * FROM `".$strLinkTable."` WHERE `idMaster`=".$this->dataId;
		}
		// Process data
		$booReturn  = $this->myDBClass->getDataArray($strSQL,$arrSelectedRaw,$intDataCount);
		if ($booReturn && ($intDataCount != 0)) {
			foreach($arrSelectedRaw AS $elem) {
				// Multi tables
				if ($strLinkTable == 'tbl_lnkServicegroupToService') {
					if (isset($elem['exclude']) && ($elem['exclude'] == 1)) {
						$arrSelect[] = "e".$elem['idSlaveH']."::".$elem['idSlaveHG']."::".$elem['idSlaveS'];
					} else {
						$arrSelect[] = $elem['idSlaveH']."::".$elem['idSlaveHG']."::".$elem['idSlaveS'];
					}
				// Servicedependencies and -escalations
				} else if (($strLinkTable == 'tbl_lnkServicedependencyToService_DS') || 
						   ($strLinkTable == 'tbl_lnkServicedependencyToService_S') ||
						   ($strLinkTable == 'tbl_lnkServiceescalationToService')) {
					if (isset($elem['exclude']) && ($elem['exclude'] == 1)) {
						$arrSelect[] = "e::".$elem['strSlave'];
					} else {
						$arrSelect[] = $elem['strSlave'];
					}	
				// Standard tables
				} else {
					if ($intOption == 8) {
						$arrSelect[] = $elem['idMaster'];
					} else {
						if (isset($elem['exclude']) && ($elem['exclude'] == 1)) {
							$arrSelect[] = "e".$elem['idSlave'];
						} else {
							$arrSelect[] = $elem['idSlave'];
						}
					}
				}
			}
			return(0);
		} else {
			return(1);
		}
  	}
}
?>