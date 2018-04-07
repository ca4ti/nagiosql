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
// Zweck:	Host Abhängigkeiten definieren
// Datei:	admin/hostdependencies.php
// Version: 1.02
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 5;
$intSub  		= 12;
$intMenu 		= 2;
$preContent 	= "hostdependencies.tpl.htm";
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
$chkSelHostDepend		= isset($_POST['selHostDepend']) 	? $_POST['selHostDepend'] 		: array("");
$chkSelHost 			= isset($_POST['selHost']) 			? $_POST['selHost'] 			: array("");
$chkSelHostgroupDep		= isset($_POST['selHostgroupDep']) 	? $_POST['selHostgroupDep'] 	: array("");
$chkSelHostgroup		= isset($_POST['selHostgroup']) 	? $_POST['selHostgroup'] 		: array("");
$chkEOo					= isset($_POST['chbEOo'])			? $_POST['chbEOo'].","			: "";
$chkEOd					= isset($_POST['chbEOd'])			? $_POST['chbEOd'].","			: "";
$chkEOu					= isset($_POST['chbEOu'])			? $_POST['chbEOu'].","			: "";
$chkEOp					= isset($_POST['chbEOp'])			? $_POST['chbEOp'].","			: "";
$chkEOn					= isset($_POST['chbEOn'])			? $_POST['chbEOn'].","			: "";
$chkNOo					= isset($_POST['chbNOo'])			? $_POST['chbNOo'].","			: "";
$chkNOd					= isset($_POST['chbNOd'])			? $_POST['chbNOd'].","			: "";
$chkNOu					= isset($_POST['chbNOu'])			? $_POST['chbNOu'].","			: "";
$chkNOp					= isset($_POST['chbNOp'])			? $_POST['chbNOp'].","			: "";
$chkNOn					= isset($_POST['chbNOn'])			? $_POST['chbNOn'].","			: "";
$chkTfConfigName 		= isset($_POST['tfConfigName']) 	? $_POST['tfConfigName'] 		: "";
$chkInherit				= isset($_POST['chbInherit'])		? $_POST['chbInherit']			: 0;
//
// Daten verarbeiten
// =================
$strEO 	  = substr($chkEOo.$chkEOd.$chkEOu.$chkEOp.$chkEOn,0,-1);
$strNO 	  = substr($chkNOo.$chkNOd.$chkNOu.$chkNOp.$chkNOn,0,-1);
// Strings zusammenstellen
$strHost		 = $myVisClass->makeCommaString($chkSelHost);
$strHostgroup    = $myVisClass->makeCommaString($chkSelHostgroup);
$strHostDep		 = $myVisClass->makeCommaString($chkSelHostDepend);
$strHostgroupDep = $myVisClass->makeCommaString($chkSelHostgroupDep);
if (($chkModus == "insert") || ($chkModus == "modify")) {
	// Daten Einfügen oder Aktualisieren
	$strSQL2 = "tbl_hostdependency SET config_name='$chkTfConfigName', dependent_host_name='$strHostDep', host_name='$strHost', 
				dependent_hostgroup_name='$strHostgroupDep', hostgroup_name='$strHostgroup', inherits_parent='$chkInherit', 
				execution_failure_criteria='$strEO', notification_failure_criteria='$strNO', active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL1 = "INSERT INTO ";
		$strSQL3 = "";
	} else {
		$strSQL1 = "UPDATE ";
		$strSQL3 = " WHERE id=$chkDataId";	
	}	
	$strSQL = $strSQL1.$strSQL2.$strSQL3;
	if (($chkSelHostDepend != "") && ($chkSelHost != "")) {
		$myVisClass->dataInsert($strSQL);
		$strMessage = $myVisClass->strDBMessage;
		if ($chkModus == "insert") $myVisClass->writeLog($LANG['logbook']['newhostdep']." ".$chkTfConfigName);
		if ($chkModus == "modify") $myVisClass->writeLog($LANG['logbook']['modifyhostdep']." ".$chkTfConfigName);
	} else {
		$strMessage  = $LANG['db']['datamissing'];
	}
	$chkModus = "display";
}  else if ($chkModus == "make") {
	// Konfigurationsdatei schreiben
	$myVisClass->createConfig("tbl_hostdependency");
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$myVisClass->dataDelete("tbl_hostdependency",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$myVisClass->dataCopy("tbl_hostdependency",$chkListId);
	$strMessage = $myVisClass->strDBMessage;
	$chkModus = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_hostdependency WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	$chkModus      = "add";
}
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myVisClass->lastModified("tbl_hostdependency",$strLastModified,$strFileDate,$strOld);
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm5']." -> ".$LANG['menu']['info12']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['hostdepend']);
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if ($chkModus == "add") {
	// Datenbankabfragen
	$myVisClass->strTempValue1 = $chkSelModify;
	$myVisClass->resTemplate   =& $conttp;
	if (isset($arrModifyData)) $myVisClass->arrWorkdata = $arrModifyData;	
	// Hostfelder füllem
	$intReturn = 0;
	$strSQL    = "SELECT host_name FROM tbl_host WHERE active='1' ORDER BY host_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_HOSTDEPEND","host_name","dependent_host_name","hostdepend",1);
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_HOST","host_name","host_name","host",1);
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_host']."<br>";	
	// Hostgruppenfelder füllem
	$strSQL    = "SELECT hostgroup_name FROM tbl_hostgroup WHERE active='1' ORDER BY hostgroup_name";
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_HOSTGROUPDEP","hostgroup_name","dependent_hostgroup_name","hostgroupdepend",1);
	$intReturn = $myVisClass->parseSelect($strSQL,"DAT_HOSTGROUP","hostgroup_name","hostgroup_name","hostgroup",1);	
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
	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		// Im Modus "Modifizieren" die Datenfelder setzen
		foreach($arrModifyData AS $key => $value) {
			if (($key == "active") || ($key == "last_modified")) continue;
			$conttp->setVariable("DAT_".strtoupper($key),htmlspecialchars($value));
		}
		if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
		$conttp->setVariable("MODUS","modify");
		foreach(explode(",",$arrModifyData['execution_failure_criteria']) AS $elem) {
			$conttp->setVariable("DAT_EO".strtoupper($elem)."_CHECKED","checked");
		}
		foreach(explode(",",$arrModifyData['notification_failure_criteria']) AS $elem) {
			$conttp->setVariable("DAT_NO".strtoupper($elem)."_CHECKED","checked");
		}		
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
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['dependhosts']." / ".$LANG['admintable']['dependhostgrs']);	
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);	
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_hostdependency");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_hostdependency";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";} else {$intCount = (int)$arrDataLinesCount['number'];}
	// Datensätze holen
	$strSQL    = "SELECT id, config_name, dependent_host_name, dependent_hostgroup_name, active 
							 FROM tbl_hostdependency ORDER BY config_name LIMIT $chkLimit,15";
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
			if ($arrDataLines[$i]['dependent_host_name'] != "") {
				if (strlen($arrDataLines[$i]['dependent_host_name']) > 50) {$strAdd = ".....";} else {$strAdd = "";}
				$mastertp->setVariable("DATA_FIELD_2",substr($arrDataLines[$i]['dependent_host_name'],0,50).$strAdd);
			} else {
				if (strlen($arrDataLines[$i]['dependent_hostgroup_name']) > 50) {$strAdd = ".....";} else {$strAdd = "";}
				$mastertp->setVariable("DATA_FIELD_2",substr($arrDataLines[$i]['dependent_hostgroup_name'],0,50).$strAdd);
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