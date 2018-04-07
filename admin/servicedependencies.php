<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL 2005
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005 by Martin Willisegger / nagios.ql2005@wizonet.ch
//
// Projekt:	Nagios NG Applikation
// Author :	Martin Willisegger
// Datum:	30.03.2005
// Zweck:	Service Abhängigkeiten definieren
// Datei:	admin/servicedependencies.php
// Version:	1.02
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 5;
$intSub  		= 10;
$intMenu 		= 2;
$preContent 	= "servicedependencies.tpl.htm";
$setFileVersion = "1.02";
$strDBWarning	= "";
$intCount		= 0;
$strMessage		= "";
//
// Vorgabedatei einbinden
// ======================
$preRights 	= "admin1";
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkSelHostDepend		= isset($_POST['selHostDepend']) 		? $_POST['selHostDepend'] 		: array("");
$chkSelServiceDepend 	= isset($_POST['selServiceDepend']) 	? $_POST['selServiceDepend'] 	: array("");
$chkSelHost 			= isset($_POST['selHost']) 				? $_POST['selHost'] 			: array("");
$chkSelService 			= isset($_POST['selService']) 			? $_POST['selService'] 			: array("");
$chkSelHostgroupDep		= isset($_POST['selHostgroupDep']) 		? $_POST['selHostgroupDep'] 	: array("");
$chkSelServicegroupDep 	= isset($_POST['selServicegroupDep']) 	? $_POST['selServicegroupDep'] 	: array("");
$chkSelHostgroup 		= isset($_POST['selHostgroup']) 		? $_POST['selHostgroup'] 		: array("");
$chkSelServicegroup		= isset($_POST['selServicegroup'])		? $_POST['selServicegroup'] 	: array("");
$chkTfConfigName		= isset($_POST['tfConfigName']) 		? $_POST['tfConfigName'] 		: "";
$chkEOo					= isset($_POST['chbEOo'])				? $_POST['chbEOo'].","			: "";
$chkEOw					= isset($_POST['chbEOw'])				? $_POST['chbEOw'].","			: "";
$chkEOu					= isset($_POST['chbEOu'])				? $_POST['chbEOu'].","			: "";
$chkEOc					= isset($_POST['chbEOc'])				? $_POST['chbEOc'].","			: "";
$chkEOp					= isset($_POST['chbEOp'])				? $_POST['chbEOp'].","			: "";
$chkEOn					= isset($_POST['chbEOn'])				? $_POST['chbEOn'].","			: "";
$chkNOo					= isset($_POST['chbNOo'])				? $_POST['chbNOo'].","			: "";
$chkNOw					= isset($_POST['chbNOw'])				? $_POST['chbNOw'].","			: "";
$chkNOu					= isset($_POST['chbNOu'])				? $_POST['chbNOu'].","			: "";
$chkNOc					= isset($_POST['chbNOc'])				? $_POST['chbNOc'].","			: "";
$chkNOp					= isset($_POST['chbNOp'])				? $_POST['chbNOp'].","			: "";
$chkNOn					= isset($_POST['chbNOn'])				? $_POST['chbNOn'].","			: "";
$chkInherit				= isset($_POST['chbInherit'])			? $_POST['chbInherit']			: 0;
//
// Daten verarbeiten
// =================
$strEO 	  = substr($chkEOo.$chkEOw.$chkEOu.$chkEOc.$chkEOp.$chkEOn,0,-1);
$strNO	  = substr($chkNOo.$chkNOw.$chkNOu.$chkNOc.$chkNOp.$chkNOn,0,-1);
// Strings zusammenstellen
$strHostDepend		= $myVisClass->makeCommaString($chkSelHostDepend);
$strServiceDepend	= $myVisClass->makeCommaString($chkSelServiceDepend);
$strHost 			= $myVisClass->makeCommaString($chkSelHost);
$strServices 		= $myVisClass->makeCommaString($chkSelService);
$strHostgroupDep	= $myVisClass->makeCommaString($chkSelHostgroupDep);
$strServicegroupDep	= $myVisClass->makeCommaString($chkSelServicegroupDep);
$strHostgroup		= $myVisClass->makeCommaString($chkSelHostgroup);
$strServicegroup	= $myVisClass->makeCommaString($chkSelServicegroup);
// Modus auswerten
if (($chkModus == "insert") || ($chkModus == "modify")) {
	$strSQL2 = "tbl_servicedependency SET dependent_host_name='$strHostDepend', dependent_service_description='$strServiceDepend', 
				host_name='$strHost', service_description='$strServices', dependent_hostgroup_name='$strHostgroupDep',
				dependent_servicegroup_name='$strServicegroupDep', hostgroup_name='$strHostgroup', servicegroup_name='$strServicegroup',
				config_name='$chkTfConfigName', inherits_parent='$chkInherit', execution_failure_criteria='$strEO', 
				notification_failure_criteria='$strNO', active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL1 = "INSERT INTO ";
		$strSQL3 = "";
	} else {
		$strSQL1 = "UPDATE ";
		$strSQL3 = " WHERE id=$chkDataId";	
	}
	$strSQL = $strSQL1.$strSQL2.$strSQL3;
	if (((($strHost  != "") && ($strHostDepend != "") && ($strServices != "") && ($strServiceDepend != "") && 
	   ($strHostgroup == "") && ($strHostgroupDep == "") && ($strServicegroup =="") && ($strServicegroupDep == "")) ||
	   (($strHost  == "") && ($strHostDepend == "") && ($strServices != "") && ($strServiceDepend != "") && 
	   ($strHostgroup != "") && ($strHostgroupDep != "") && ($strServicegroup =="") && ($strServicegroupDep == "")) ||
	   (($strHost  == "") && ($strHostDepend == "") && ($strServices == "") && ($strServiceDepend == "") && 
	   ($strHostgroup == "") && ($strHostgroupDep == "") && ($strServicegroup !="") && ($strServicegroupDep != ""))) &&	
	   ($chkTfConfigName != "")) {
		$myVisClass->dataInsert($strSQL);
		$strMessage = $myVisClass->strDBMessage;
		if ($chkModus == "insert") $myVisClass->writeLog($LANG['logbook']['newservdep']." ".$chkTfConfigName);
		if ($chkModus == "modify") $myVisClass->writeLog($LANG['logbook']['modifyservdep']." ".$chkTfConfigName);
	} else {
		$strMessage  = $LANG['db']['datamissornak'];
	}
	$chkModus = "display";
}  else if ($chkModus == "make") {
	// Konfigurationsdatei schreiben
	$myVisClass->createConfig("tbl_servicedependency");
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$myVisClass->dataDelete("tbl_servicedependency",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$myVisClass->dataCopy("tbl_servicedependency",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_servicedependency WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	$strHost	   		= $arrModifyData['host_name'];
	$strHostgroup 		= $arrModifyData['hostgroup_name'];
	$strHostDepend	   	= $arrModifyData['dependent_host_name'];
	$strHostgroupDep	= $arrModifyData['dependent_hostgroup_name'];
	$chkModus      		= "add";
}
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myVisClass->lastModified("tbl_servicedependency",$strLastModified,$strFileDate,$strOld);
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm5']." -> ".$LANG['menu']['info10']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['servicedepend']);
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if (($chkModus == "add") || ($chkModus == "refresh")) {
	// Datenbankabfragen
	$chkGetHost				   = "'".str_replace(",","','",$strHost)."'";
	$intCountHost			   = count(explode(",",$strHost));
	$chkGetHostDep			   = "'".str_replace(",","','",$strHostDepend)."'";
	$intCountHostDep 		   = count(explode(",",$strHostDepend));	
	$chkGetHostgroup 		   = "'".str_replace(",","','",$strHostgroup)."'";
	$chkGetHostgroupDep		   = "'".str_replace(",","','",$strHostgroupDep)."'";
	$myVisClass->strTempValue1 = $chkSelModify;
	$myVisClass->strTempValue2 = $chkModus;
	$myVisClass->resTemplate   =& $conttp;
	if (isset($arrModifyData)) $myVisClass->arrWorkdata = $arrModifyData;
	// Dependent Hostname und Hostname in Auswahlliste einfügen
	$intReturn = 0;
	$strSQL    = "SELECT host_name FROM tbl_host WHERE active='1' ORDER BY host_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_HOSTDEPEND","host_name","dependent_host_name","hostdepend",1,$strHostDepend);
	if ($chkGetHostDep == "") $chkGetHostDep = $myVisClass->strTempValue3;
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_HOST","host_name","host_name","host",1,$strHost);
	if ($chkGetHost == "")	  $chkGetHost    = $myVisClass->strTempValue3;
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_host']."<br>";
	// Dependent Hostgroup und Hostgroup in Auswahlliste einfügen
	$strSQL    = "SELECT hostgroup_name FROM tbl_hostgroup WHERE active='1' ORDER BY hostgroup_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_HOSTGROUPDEP","hostgroup_name","dependent_hostgroup_name","hostgroupdepend",1,$chkGetHostgroupDep);
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_HOSTGROUP","hostgroup_name","hostgroup_name","hostgroup",1,$strHostgroup);
	// Dependent Service und Service vorbereiten
	if (($chkGetHost == "''") && ($chkGetHostgroup != "''")) {
	    if ($chkGetHostgroup == "'*'") {
			$strSQLMembers = "SELECT members FROM tbl_hostgroup";
		} else {
			$strSQLMembers = "SELECT members FROM tbl_hostgroup WHERE hostgroup_name IN ($chkGetHostgroup)";
		}
		$booReturn = $myDBClass->getDataArray($strSQLMembers,$arrDataMembers,$intDataCount);
		if ($booReturn == false) {
			$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
		} else if ($intDataCount != 0) {
			$chkGetHost   = "'";
			$intCountHost = 0;		
			for ($i=0;$i<$intDataCount;$i++) {		
				$arrTemp = explode(",",$arrDataMembers[$i]['members']);
				foreach($arrTemp AS $elem) {
					if (substr_count($chkGetHost,$elem) == 0) {
						$chkGetHost .= $elem."','";
						$intCountHost++;
					}
				}
			}
			$chkGetHost = substr($chkGetHost,0,-2);
		}
	}
	if (($chkGetHostDep == "''") && ($chkGetHostgroupDep != "''")) {
	    if ($chkGetHostgroupDep == "'*'") {
			$strSQLMembers = "SELECT members FROM tbl_hostgroup";
		} else {
			$strSQLMembers = "SELECT members FROM tbl_hostgroup WHERE hostgroup_name IN ($chkGetHostgroupDep)";
		}
		$booReturn = $myDBClass->getDataArray($strSQLMembers,$arrDataMembers,$intDataCount);
		if ($booReturn == false) {
			$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
		} else if ($intDataCount != 0) {
			$chkGetHostDep   = "'";
			$intCountHostDep = 0;
			for ($i=0;$i<$intDataCount;$i++) {
				$arrTemp = explode(",",$arrDataMembers[$i]['members']);
				foreach($arrTemp AS $elem) {
					if (substr_count($chkGetHostDep,$elem) == 0) {
						$chkGetHostDep .= $elem."','";
						$intCountHostDep++;
					}
				}
			}
			$chkGetHostDep = substr($chkGetHostDep,0,-2);
		}
	}
	// Service in Auswahlliste eintragen	
	if (substr_count($chkGetHost,"'*'") == 0) {
		$strSQL = "SELECT service_description FROM tbl_service WHERE host_name IN($chkGetHost) AND active='1' 
				   GROUP BY service_description HAVING count(*) >= $intCountHost ORDER BY service_description";
	} else {
		$intCountHost = $myDBClass->countRows("SELECT DISTINCT host_name FROM tbl_service");
		$strSQL = "SELECT service_description FROM tbl_service WHERE host_name LIKE '%' AND active='1' 
				   GROUP BY service_description HAVING count(*) >= $intCountHost ORDER BY service_description";
	}
	$intReturn = 0;
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_SERVICE","service_description","service_description","service",2,$strServices);
	if (($intReturn != 0) && (($chkGetHost != "''") || ($chkGetHostgroup != "''"))) $strDBWarning .= $LANG['admintable']['warn_serv2']."<br>";
	// Dependent Service Auswahlliste eintragen
	if (substr_count($chkGetHostDep,"'*'") == 0) {
		$strSQL = "SELECT service_description FROM tbl_service WHERE host_name IN($chkGetHostDep) AND active='1' 
		GROUP BY service_description HAVING count(*) >= $intCountHostDep ORDER BY service_description";
	} else {
		$intCountHost = $myDBClass->countRows("SELECT DISTINCT host_name FROM tbl_service");
		$strSQL = "SELECT service_description FROM tbl_service WHERE host_name LIKE '%' AND active='1' 
				   GROUP BY service_description HAVING count(*) >= $intCountHostDep ORDER BY service_description";
	}
	$intReturn = 0;
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_SERVICEDEPEND","service_description","dependent_service_description","servicedepend",2,$strServiceDepend);
	if (($intReturn != 0) && (($chkGetHost != "''") || ($chkGetHostgroup != "''"))) $strDBWarning .= $LANG['admintable']['warn_serv2']."<br>";
	// Dependent Servicegroup und Servicegroup in Auswahlliste einfügen
	$strSQL    = "SELECT servicegroup_name FROM tbl_servicegroup WHERE active='1' ORDER BY servicegroup_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_SERVICEGROUPDEP","servicegroup_name","dependent_servicegroup_name","servicegroupdepend",1,$strServicegroupDep);
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_SERVICEGROUP","servicegroup_name","servicegroup_name","servicegroup",1,$strServicegroup);
	// Feldbeschriftungen setzen
	foreach($LANG['admintable'] AS $key => $value) {
		$conttp->setVariable("LANG_".strtoupper($key),$value);
	}
	foreach($LANG['formchecks'] AS $key => $value) {
		$conttp->setVariable(strtoupper($key),$value);
	}
	$conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
	$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	$conttp->setVariable("LIMIT",$chkLimit);
	if ($strDBWarning != "") $conttp->setVariable("WARNING",$strDBWarning.$LANG['admintable']['warn_save']);
	$conttp->setVariable("ACT_CHECKED","checked");
	$conttp->setVariable("MODUS","insert");
	if ($chkModus == "refresh") {
		if ($chkTfConfigName != "")	$conttp->setVariable("DAT_CONFIG_NAME",$chkTfConfigName);
		foreach(explode(",",$strEO) AS $elem) {
			$conttp->setVariable("DAT_EO".strtoupper($elem)."_CHECKED","checked");
		}
		foreach(explode(",",$strNO) AS $elem) {
			$conttp->setVariable("DAT_NO".strtoupper($elem)."_CHECKED","checked");
		}		
		if ($chkActive != 1)  $conttp->setVariable("ACT_CHECKED","");
		if ($chkInherit == 1) $conttp->setVariable("ACT_INHERIT","checked");
		if ($chkDataId != 0) {
			$conttp->setVariable("MODUS","modify");
			$conttp->setVariable("DAT_ID",$chkDataId);
		}
	} else if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		// Im Modus "Modifizieren" die Datenfelder setzen
		foreach($arrModifyData AS $key => $value) {
			if (($key == "active") || ($key == "last_modified")) continue;
			$conttp->setVariable("DAT_".strtoupper($key),htmlspecialchars($value));
		}
		foreach(explode(",",$arrModifyData['execution_failure_criteria']) AS $elem) {
			$conttp->setVariable("DAT_EO".strtoupper($elem)."_CHECKED","checked");
		}
		foreach(explode(",",$arrModifyData['notification_failure_criteria']) AS $elem) {
			$conttp->setVariable("DAT_NO".strtoupper($elem)."_CHECKED","checked");
		}
		if ($arrModifyData['inherits_parent'] == 1) $conttp->setVariable("ACT_INHERIT","checked");
		if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
		$conttp->setVariable("MODUS","modify");
	}
	$conttp->parse("datainsert");
	$conttp->show("datainsert");
}
//
// Datentabelle
// ============
// Titel setzen
if ($chkModus == "display") {
	// Feldbeschriftungen setzen
	foreach($LANG['admintable'] AS $key => $value) {
		$mastertp->setVariable("LANG_".strtoupper($key),$value);
	}  
	$mastertp->setVariable("FIELD_1",$LANG['admintable']['configname']);
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['dependservices']." / ".$LANG['admintable']['dependsergrs']);	
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);	
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_servicedependency");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_servicedependency";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";} else {$intCount = (int)$arrDataLinesCount['number'];}
	// Datensätze holen
	$strSQL    = "SELECT id, config_name, dependent_service_description, dependent_servicegroup_name, active
				  FROM tbl_servicedependency ORDER BY config_name LIMIT $chkLimit,15";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
	} else if ($intDataCount != 0) {
		for ($i=0;$i<$intDataCount;$i++) {	
			// Jede zweite Zeile einfärben (Klassen setzen)
			$strClassL = "tdld"; $strClassM = "tdmd"; $strChbClass = "checkboxline";
			if ($i%2 == 1) {$strClassL = "tdlb"; $strClassM = "tdmb"; $strChbClass = "checkbox";}
			if ($arrDataLines[$i]['active'] == 0) {$strActive = $LANG['common']['no_nak'];} else {$strActive = $LANG['common']['yes_ok'];}	
			// Datenfelder setzen
			foreach($LANG['admintable'] AS $key => $value) {
				$mastertp->setVariable("LANG_".strtoupper($key),$value);
			} 
			$mastertp->setVariable("DATA_FIELD_1",$arrDataLines[$i]['config_name']);
			if ($arrDataLines[$i]['dependent_service_description'] != "") {
				if (strlen($arrDataLines[$i]['dependent_service_description']) > 50) {$strAdd = ".....";} else {$strAdd = "";}
				$mastertp->setVariable("DATA_FIELD_2",substr($arrDataLines[$i]['dependent_service_description'],0,50).$strAdd);
			} else {
				if (strlen($arrDataLines[$i]['dependent_servicegroup_name']) > 50) {$strAdd = ".....";} else {$strAdd = "";}
				$mastertp->setVariable("DATA_FIELD_2",substr($arrDataLines[$i]['dependent_servicegroup_name'],0,50).$strAdd);
			}
			$mastertp->setVariable("DATA_ACTIVE",$strActive);
			$mastertp->setVariable("LINE_ID",$arrDataLines[$i]['id']);
			$mastertp->setVariable("CELLCLASS_L",$strClassL);
			$mastertp->setVariable("CELLCLASS_M",$strClassM);
			$mastertp->setVariable("CHB_CLASS",$strChbClass);
			$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
			if ($chkModus != "display") $conttp->setVariable("DISABLED","disabled");		
			$mastertp->parse("datarow");		
		}
	} else {
		$mastertp->setVariable("DATA_FIELD_1",$LANG['admintable']['nodata']);
		$mastertp->setVariable("DATA_FIELD_2","&nbsp;");
		$mastertp->setVariable("DATA_ACTIVE","&nbsp;");
		$mastertp->setVariable("CELLCLASS_L","tdlb");
		$mastertp->setVariable("CELLCLASS_M","tdmb");
		$mastertp->setVariable("CHB_CLASS","checkbox");
		$mastertp->setVariable("DISABLED","disabled");
	}
	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	if (isset($intCount)) $mastertp->setVariable("PAGES",$myVisClass->buildPageLinks($_SERVER['PHP_SELF'],$intCount,$chkLimit));
	$mastertp->parse("datatable");
	$mastertp->show("datatable");
}
// Mitteilungen ausgeben
if (isset($strMessage)) $mastertp->setVariable("DBMESSAGE",$strMessage);
$mastertp->setVariable("LAST_MODIFIED",$LANG['db']['last_modified']."<b>".$strLastModified."</b>");
$mastertp->setVariable("FILEDATE",$LANG['common']['filedate']."<b>".$strFileDate."</b>");
$mastertp->setVariable("FILEISOLD","<br><span class=\"dbmessage\">".$strOld."</span>");
$mastertp->parse("msgfooter");
$mastertp->show("msgfooter");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>