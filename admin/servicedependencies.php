<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2006 by Martin Willisegger / nagiosql_v2@wizonet.ch
//
// Projekt:	NagiosQL Applikation
// Author :	Martin Willisegger
// Datum:	12.03.2007
// Zweck:	Service Abhängigkeiten definieren
// Datei:	admin/servicedependencies.php
// Version: 2.0.2 (Internal)
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
$strDBWarning	= "";
$intCount		= 0;
$strMessage		= "";
//
// Vorgabedatei einbinden
// ======================
$preAccess	= 1;
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkSelHostDepend		= isset($_POST['selHostDepend']) 		? $_POST['selHostDepend'] 				: array("");
$chkSelServiceDepend 	= isset($_POST['selServiceDepend']) 	? $_POST['selServiceDepend'] 			: array("");
$chkSelHost 			= isset($_POST['selHost']) 				? $_POST['selHost'] 					: array("");
$chkSelService 			= isset($_POST['selService']) 			? $_POST['selService'] 					: array("");
$chkSelHostgroupDep		= isset($_POST['selHostgroupDep']) 		? $_POST['selHostgroupDep'] 			: array("");
$chkSelServicegroupDep 	= isset($_POST['selServicegroupDep']) 	? $_POST['selServicegroupDep'] 			: array("");
$chkSelHostgroup 		= isset($_POST['selHostgroup']) 		? $_POST['selHostgroup'] 				: array("");
$chkSelServicegroup		= isset($_POST['selServicegroup'])		? $_POST['selServicegroup'] 			: array("");
$chkTfConfigName		= isset($_POST['tfConfigName']) 		? stripslashes($_POST['tfConfigName'])	: "";
$chkEOo					= isset($_POST['chbEOo'])				? $_POST['chbEOo'].","					: "";
$chkEOw					= isset($_POST['chbEOw'])				? $_POST['chbEOw'].","					: "";
$chkEOu					= isset($_POST['chbEOu'])				? $_POST['chbEOu'].","					: "";
$chkEOc					= isset($_POST['chbEOc'])				? $_POST['chbEOc'].","					: "";
$chkEOp					= isset($_POST['chbEOp'])				? $_POST['chbEOp'].","					: "";
$chkEOn					= isset($_POST['chbEOn'])				? $_POST['chbEOn'].","					: "";
$chkNOo					= isset($_POST['chbNOo'])				? $_POST['chbNOo'].","					: "";
$chkNOw					= isset($_POST['chbNOw'])				? $_POST['chbNOw'].","					: "";
$chkNOu					= isset($_POST['chbNOu'])				? $_POST['chbNOu'].","					: "";
$chkNOc					= isset($_POST['chbNOc'])				? $_POST['chbNOc'].","					: "";
$chkNOp					= isset($_POST['chbNOp'])				? $_POST['chbNOp'].","					: "";
$chkNOn					= isset($_POST['chbNOn'])				? $_POST['chbNOn'].","					: "";
$chkInherit				= isset($_POST['chbInherit'])			? $_POST['chbInherit']					: 0;
//
// Daten verarbeiten
// =================
$strEO 	  = substr($chkEOo.$chkEOw.$chkEOu.$chkEOc.$chkEOp.$chkEOn,0,-1);
$strNO	  = substr($chkNOo.$chkNOw.$chkNOu.$chkNOc.$chkNOp.$chkNOn,0,-1);
if (($chkSelHostDepend[0] 	   == "") || ($chkSelHostDepend[0] 		== "0")) {$intSelHostDepend 	 = 0;} else {$intSelHostDepend 		= 1;}
if (($chkSelServiceDepend[0]   == "") || ($chkSelServiceDepend[0] 	== "0")) {$intSelServiceDepend 	 = 0;} else {$intSelServiceDepend 	= 1;}
if (($chkSelHost[0] 		   == "") || ($chkSelHost[0] 			== "0")) {$intSelHost 			 = 0;} else {$intSelHost 			= 1;}
if (($chkSelService[0] 		   == "") || ($chkSelService[0] 		== "0")) {$intSelService 		 = 0;} else {$intSelService 		= 1;}
if (($chkSelHostgroupDep[0]	   == "") || ($chkSelHostgroupDep[0] 	== "0")) {$intSelHostgroupDep 	 = 0;} else {$intSelHostgroupDep 	= 1;}
if (($chkSelServicegroupDep[0] == "") || ($chkSelServicegroupDep[0] == "0")) {$intSelServicegroupDep = 0;} else {$intSelServicegroupDep = 1;}
if (($chkSelHostgroup[0] 	   == "") || ($chkSelHostgroup[0] 		== "0")) {$intSelHostgroup 		 = 0;} else {$intSelHostgroup 		= 1;}
if (($chkSelServicegroup[0]    == "") || ($chkSelServicegroup[0] 	== "0")) {$intSelServicegroup 	 = 0;} else {$intSelServicegroup 	= 1;}
if ($chkSelService[0] 		   == "*")	$intSelService 		 = 2;
if ($chkSelServiceDepend[0]    == "*") 	$intSelServiceDepend = 2;
// Datein einfügen oder modifizieren
if (($chkModus == "insert") || ($chkModus == "modify")) {
	$strSQLx = "tbl_servicedependency SET dependent_host_name=$intSelHostDepend, dependent_service_description=$intSelServiceDepend, 
				host_name=$intSelHost, service_description=$intSelService, dependent_hostgroup_name=$intSelHostgroupDep,
				dependent_servicegroup_name=$intSelServicegroupDep, hostgroup_name=$intSelHostgroup, servicegroup_name=$intSelServicegroup,
				config_name='$chkTfConfigName', inherits_parent='$chkInherit', execution_failure_criteria='$strEO', 
				notification_failure_criteria='$strNO', active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL = "INSERT INTO ".$strSQLx; 
	} else {
		$strSQL = "UPDATE ".$strSQLx." WHERE id=$chkDataId";   
	}	
	if (((($intSelHost   != 0) && ($intSelHostDepend   != 0) && ($intSelService      != 0) && ($intSelServiceDepend   != 0)   && 
	   ($intSelHostgroup == 0) && ($intSelHostgroupDep == 0) && ($intSelServicegroup == 0) && ($intSelServicegroupDep == 0))  ||
	   (($intSelHost     == 0) && ($intSelHostDepend   == 0) && ($intSelService      != 0) && ($intSelServiceDepend   != 0)   && 
	   ($intSelHostgroup != 0) && ($intSelHostgroupDep != 0) && ($intSelServicegroup == 0) && ($intSelServicegroupDep == 0))  ||
	   (($intSelHost     == 0) && ($intSelHostDepend   == 0) && ($intSelService      == 0) && ($intSelServiceDepend   == 0)   && 
	   ($intSelHostgroup == 0) && ($intSelHostgroupDep == 0) && ($intSelServicegroup != 0) && ($intSelServicegroupDep != 0))) &&	
	   ($chkTfConfigName != "")) {
		$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		if ($intInsert == 1) {
			$intReturn = 1;
		} else {
			if ($chkModus  == "insert") 	$myDataClass->writeLog($LANG['logbook']['newservdep']." ".$chkTfConfigName);
			if ($chkModus  == "modify") 	$myDataClass->writeLog($LANG['logbook']['modifyservdep']." ".$chkTfConfigName);
			//
			// Relationen eintragen/updaten
			// ============================
			$intTableA = $myDataClass->tableID("tbl_servicedependency");
			if ($chkModus == "insert") {
				if ($intSelHostDepend 	   == 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_host"),$intInsertId,'dependent_host_name',$chkSelHostDepend);
				if ($intSelServiceDepend   == 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_service"),$intInsertId,'dependent_service_description',$chkSelServiceDepend);
				if ($intSelHost 		   == 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_host"),$intInsertId,'host_name',$chkSelHost);
				if ($intSelService 		   == 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_service"),$intInsertId,'service_description',$chkSelService);
				if ($intSelHostgroupDep    == 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$intInsertId,'dependent_hostgroup_name',$chkSelHostgroupDep);
				if ($intSelServicegroupDep == 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_servicegroup"),$intInsertId,'dependent_servicegroup_name',$chkSelServicegroupDep);
				if ($intSelHostgroup 	   == 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$intInsertId,'hostgroup_name',$chkSelHostgroup);
				if ($intSelServicegroup    == 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_servicegroup"),$intInsertId,'servicegroup_name',$chkSelServicegroup);
			} else if ($chkModus == "modify") {		
				if ($intSelHostDepend == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'dependent_host_name',$chkSelHostDepend);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'dependent_host_name');
				}
				if ($intSelServiceDepend == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_service"),$chkDataId,'dependent_service_description',$chkSelServiceDepend);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_service"),$chkDataId,'dependent_service_description');
				}			
				if ($intSelHost == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'host_name',$chkSelHost);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'host_name');
				}			
				if ($intSelService == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_service"),$chkDataId,'service_description',$chkSelService);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_service"),$chkDataId,'service_description');
				}	
				if ($intSelHostgroupDep == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$chkDataId,'dependent_hostgroup_name',$chkSelHostgroupDep);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$chkDataId,'dependent_hostgroup_name');
				}
				if ($intSelServicegroupDep == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_servicegroup"),$chkDataId,'dependent_servicegroup_name',$chkSelServicegroupDep);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_servicegroup"),$chkDataId,'dependent_servicegroup_name');
				}			
				if ($intSelHostgroup == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$chkDataId,'hostgroup_name',$chkSelHostgroup);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$chkDataId,'hostgroup_name');
				}			
				if ($intSelServicegroup == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_servicegroup"),$chkDataId,'servicegroup_name',$chkSelServicegroup);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_servicegroup"),$chkDataId,'servicegroup_name');
				}			
			}		
			$intReturn = 0;
		}
	} else {
		$strMessage .= $LANG['db']['datamissornak'];
	}
	$chkModus = "display";
}  else if ($chkModus == "make") {
	// Konfigurationsdatei schreiben
	$intReturn = $myConfigClass->createConfig("tbl_servicedependency",0);
	$chkModus  = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$intReturn = $myDataClass->dataDeleteSimple("tbl_servicedependency",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$intReturn = $myDataClass->dataCopySimple("tbl_servicedependency",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_servicedependency WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	// Ausgewählte Services holen
	$arrServices = "";
	if ($arrModifyData['service_description'] == 1) {
		$strSQL = "SELECT tbl_B_id FROM tbl_relation
				   WHERE tbl_A_id = $chkListId AND tbl_A_field = 'service_description'
						 AND tbl_A = ".$myDataClass->tableID("tbl_servicedependency")."
						 AND tbl_B = ".$myDataClass->tableID("tbl_service")."
				   ORDER BY tbl_B_id";
		$booReturn = $myDBClass->getDataArray($strSQL,$arrDataServices,$intDataCount);
		if ($intDataCount != 0) foreach($arrDataServices AS $elem) $arrServices[] = $elem['tbl_B_id'];
	} else {
		if ($arrModifyData['service_description'] == 0) $arrServices[] = "0";
		if ($arrModifyData['service_description'] == 2) $arrServices[] = "*";
	}
	$arrDependServices = "";
	if ($arrModifyData['dependent_service_description'] == 1) {
		$strSQL = "SELECT tbl_B_id FROM tbl_relation
				   WHERE tbl_A_id = $chkListId AND tbl_A_field = 'dependent_service_description'
						 AND tbl_A = ".$myDataClass->tableID("tbl_servicedependency")."
						 AND tbl_B = ".$myDataClass->tableID("tbl_service")."
				   ORDER BY tbl_B_id";
		$booReturn = $myDBClass->getDataArray($strSQL,$arrDataDependentServices,$intDataCount);
		if ($intDataCount != 0) foreach($arrDataDependentServices AS $elem) $arrDependServices[] = $elem['tbl_B_id'];
	} else {
		if ($arrModifyData['dependent_service_description'] == 0) $arrDependServices[] = "0";
		if ($arrModifyData['dependent_service_description'] == 2) $arrDependServices[] = "*";	
	}
	$chkModus = "add";
}
// Statusmitteilungen setzen
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $myDataClass->strDBMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myConfigClass->lastModified("tbl_servicedependency",$strLastModified,$strFileDate,$strOld);
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
	if ($chkModus == "refresh") {
		// Felder zurücksetzen
		if (($chkSelHost[0] != "") && ($chkSelHost[0] != 0)) 			 $chkSelHostgroup    = array("");
		if (($chkSelHostDepend[0] != "") && ($chkSelHostDepend[0] != 0)) $chkSelHostgroupDep = array("");
	}
	// Klassenvariabeln definieren
	$myVisClass->resTemplate     =& $conttp;
	$myVisClass->strTempValue1   = $chkSelModify;
	$myVisClass->strTempValue2   = $chkModus;
	$myVisClass->intTabA   	     = $myDataClass->tableID("tbl_servicedependency");
	if (isset($arrModifyData)) {
		$myVisClass->arrWorkdata = $arrModifyData;
		$myVisClass->intTabA_id  = $arrModifyData['id'];
	} else {
		$myVisClass->intTabA_id  = 0;
	}	
	// Dependent Hostname und Hostname in Auswahlliste einfügen
	$intReturn = 0;
	$intReturn  = $myVisClass->parseSelectNew('tbl_host','host_name','DAT_HOSTDEPEND','hostdepend','dependent_host_name',2,1,0,$chkSelHostDepend);
	$arrSelect1 = $myVisClass->arrTempValue1;
	$intReturn  = $myVisClass->parseSelectNew('tbl_host','host_name','DAT_HOST','host','host_name',2,1,0,$chkSelHost);
	$arrSelect2 = $myVisClass->arrTempValue1;
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_host']."<br>";
	// Dependent Hostgroup und Hostgroup in Auswahlliste einfügen
	$myVisClass->parseSelectNew('tbl_hostgroup','hostgroup_name','DAT_HOSTGROUPDEP','hostgroupdepend','dependent_hostgroup_name',2,1,0,$chkSelHostgroupDep);
	$arrSelect3 = $myVisClass->arrTempValue1;
	$myVisClass->parseSelectNew('tbl_hostgroup','hostgroup_name','DAT_HOSTGROUP','hostgroup','hostgroup_name',2,1,0,$chkSelHostgroup);
	$arrSelect4 = $myVisClass->arrTempValue1;
	// Dependent Service und Service vorbereiten
	$conttp->setVariable("DAT_SERVICE","");
	$conttp->setVariable("DAT_SERVICE_ID",0);
	if (($chkModus == "modify")  && (in_array("0",$arrServices))) $conttp->setVariable("DAT_SERVICE_SEL","selected");
	if (($chkModus == "refresh") && (in_array("0",$chkSelService))) $conttp->setVariable("DAT_SERVICE_SEL","selected");
	$conttp->parse("service");
	$conttp->setVariable("DAT_SERVICE","*");
	$conttp->setVariable("DAT_SERVICE_ID","*");
	if (($chkModus == "add") && (isset($arrServices)) && (in_array("*",$arrServices))) $conttp->setVariable("DAT_SERVICE_SEL","selected");
	if (($chkModus == "modify") && (in_array("*",$arrServices))) $conttp->setVariable("DAT_SERVICE_SEL","selected");
	if (($chkModus == "refresh") && (in_array("*",$chkSelService))) $conttp->setVariable("DAT_SERVICE_SEL","selected");
	$conttp->parse("service");
	$conttp->setVariable("DAT_SERVICEDEPEND","");
	$conttp->setVariable("DAT_SERVICEDEPEND_ID",0);
	if (($chkModus == "modify") && (in_array("0",$arrDependServices))) $conttp->setVariable("DAT_SERVICEDEPEND_SEL","selected");
	if (($chkModus == "refresh") && (in_array("0",$chkSelServiceDepend))) $conttp->setVariable("DAT_SERVICEDEPEND_SEL","selected");
	$conttp->parse("servicedepend");
	$conttp->setVariable("DAT_SERVICEDEPEND","*");
	$conttp->setVariable("DAT_SERVICEDEPEND_ID","*");
	if (($chkModus == "add") && (isset($arrServices)) && (in_array("*",$arrDependServices))) $conttp->setVariable("DAT_SERVICEDEPEND_SEL","selected");
	if (($chkModus == "modify") && (in_array("*",$arrDependServices))) $conttp->setVariable("DAT_SERVICEDEPEND_SEL","selected");
	if (($chkModus == "refresh") && (in_array("*",$chkSelServiceDepend))) $conttp->setVariable("DAT_SERVICEDEPEND_SEL","selected");
	$conttp->parse("servicedepend");
	//
	if ((count($arrSelect1) > 0) && (isset($arrSelect1[0]) && $arrSelect1[0] != 0)) $chkSelHostDepend 	= $arrSelect1;
	if ((count($arrSelect2) > 0) && (isset($arrSelect2[0]) && $arrSelect2[0] != 0)) $chkSelHost 		= $arrSelect2;
	if ((count($arrSelect3) > 0) && (isset($arrSelect3[0]) && $arrSelect3[0] != 0)) $chkSelHostgroupDep	= $arrSelect3;
	if ((count($arrSelect4) > 0) && (isset($arrSelect4[0]) && $arrSelect4[0] != 0)) $chkSelHostgroup	= $arrSelect4;
	//
	if (($chkSelHost[0] != "") && ($chkSelHost[0] != 0)) {
		$myDataClass->getServicesByHost($chkSelHost,$arrServiceIDs);
		foreach($arrServiceIDs AS $key) {
			if ($key['description'] == "") continue;
			$conttp->setVariable("DAT_SERVICE",$key['description']);
			$conttp->setVariable("DAT_SERVICE_ID",$key['id']);
			if (($chkModus == "add") && (in_array($key['id'],$arrServices))) $conttp->setVariable("DAT_SERVICE_SEL","selected");
			if (($chkModus == "refresh") && (in_array($key['id'],$chkSelService))) $conttp->setVariable("DAT_SERVICE_SEL","selected");
			$conttp->parse("service");			
		}		
	}
	if (($chkSelHostgroup[0] != "") && ($chkSelHostgroup[0] != 0)) {
		$myDataClass->getServicesByHostgroup($chkSelHostgroup,$arrServiceIDs);
		foreach($arrServiceIDs AS $key) {
			if ($key['description'] == "") continue;
			$conttp->setVariable("DAT_SERVICE",$key['description']);
			$conttp->setVariable("DAT_SERVICE_ID",$key['id']);
			if (($chkModus == "add") && (in_array($key['id'],$arrServices))) $conttp->setVariable("DAT_SERVICE_SEL","selected");
			if (($chkModus == "refresh") && (in_array($key['id'],$chkSelService))) $conttp->setVariable("DAT_SERVICE_SEL","selected");
			$conttp->parse("service");			
		}		
	}	
	if (($chkSelHostDepend[0] != "") && ($chkSelHostDepend[0] != 0)) {
		$myDataClass->getServicesByHost($chkSelHostDepend,$arrServiceIDs);
		foreach($arrServiceIDs AS $key) {
			if ($key['description'] == "") continue;
			$conttp->setVariable("DAT_SERVICEDEPEND",$key['description']);
			$conttp->setVariable("DAT_SERVICEDEPEND_ID",$key['id']);
			if (($chkModus == "add") && (in_array($key['id'],$arrDependServices))) $conttp->setVariable("DAT_SERVICEDEPEND_SEL","selected");
			if (($chkModus == "refresh") && (in_array($key['id'],$chkSelServiceDepend))) $conttp->setVariable("DAT_SERVICEDEPEND_SEL","selected");
			$conttp->parse("servicedepend");			
		}		
	}
	if (($chkSelHostgroupDep[0] != "") && ($chkSelHostgroupDep[0] != 0)) {
		$myDataClass->getServicesByHostgroup($chkSelHostgroupDep,$arrServiceIDs);
		foreach($arrServiceIDs AS $key) {
			if ($key['description'] == "") continue;
			$conttp->setVariable("DAT_SERVICEDEPEND",$key['description']);
			$conttp->setVariable("DAT_SERVICEDEPEND_ID",$key['id']);
			if (($chkModus == "add") && (in_array($key['id'],$arrDependServices))) $conttp->setVariable("DAT_SERVICEDEPEND_SEL","selected");
			if (($chkModus == "refresh") && (in_array($key['id'],$chkSelServiceDepend))) $conttp->setVariable("DAT_SERVICEDEPEND_SEL","selected");
			$conttp->parse("servicedepend");			
		}		
	}	
	// Dependent Servicegroup und Servicegroup in Auswahlliste einfügen
	$myVisClass->parseSelectNew('tbl_servicegroup','servicegroup_name','DAT_SERVICEGROUPDEP','servicegroupdepend','dependent_servicegroup_name',2,1);
	$myVisClass->parseSelectNew('tbl_servicegroup','servicegroup_name','DAT_SERVICEGROUP','servicegroup','servicegroup_name',2,1);
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
	// Im Modus "Modifizieren" die Datenfelder setzen
	} else if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		foreach($arrModifyData AS $key => $value) {
			if (($key == "active") || ($key == "last_modified")) continue;
			$conttp->setVariable("DAT_".strtoupper($key),htmlspecialchars(stripslashes($value)));
		}
		// Optionskästchen verarbeiten
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
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
	// Datensätze holen
	$strSQL    = "SELECT id, config_name, dependent_service_description, dependent_servicegroup_name, active
				  FROM tbl_servicedependency ORDER BY config_name LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
			$mastertp->setVariable("DATA_FIELD_1",stripslashes($arrDataLines[$i]['config_name']));
			$strService = "";
			if ($arrDataLines[$i]['dependent_service_description'] != 0) {
				$strSQL = "SELECT tbl_service.service_description AS service FROM tbl_relation
				   		   LEFT JOIN tbl_service ON tbl_B_id = tbl_service.id
				   		   WHERE tbl_relation.tbl_A_id = ".$arrDataLines[$i]['id']." 
						      	AND tbl_relation.tbl_A = ".$myDataClass->tableID("tbl_servicedependency")."
						   		AND tbl_relation.tbl_B = ".$myDataClass->tableID("tbl_service")." 
								AND tbl_A_field = 'dependent_service_description'
								AND tbl_A_id=".$arrDataLines[$i]['id']."
						   ORDER BY tbl_service.service_description"; 
				$booReturn = $myDBClass->getDataArray($strSQL,$arrDataService,$intDataCount1);
				if ($intDataCount1 != 0) {
					foreach($arrDataService AS $elem) $strService .= $elem['service'].", ";
					$strService = substr($strService,0,-2);
					if (strlen($strService) > 50) {$strService = ".....";} else {$strAdd = "";}
					$mastertp->setVariable("DATA_FIELD_2",substr(stripslashes($strService),0,50).$strAdd);
				} else {
					$mastertp->setVariable("DATA_FIELD_2","None or '*'");
				}
			} else {
				$strSQL = "SELECT tbl_servicegroup.servicegroup_name AS servicegroup_name FROM tbl_relation
				   		   LEFT JOIN tbl_servicegroup ON tbl_B_id = tbl_servicegroup.id
				   		   WHERE tbl_relation.tbl_A_id = ".$arrDataLines[$i]['id']." 
						      	AND tbl_relation.tbl_A = ".$myDataClass->tableID("tbl_servicedependency")."
						   		AND tbl_relation.tbl_B = ".$myDataClass->tableID("tbl_servicegroup")." 
								AND tbl_A_field = 'dependent_servicegroup_name'
								AND tbl_A_id=".$arrDataLines[$i]['id']."
						   ORDER BY tbl_servicegroup.servicegroup_name"; 
				$booReturn = $myDBClass->getDataArray($strSQL,$arrDataService,$intDataCount1);
				if ($intDataCount1 != 0) {
					foreach($arrDataService AS $elem) $strService .= $elem['servicegroup_name'].", ";
					$strService = substr($strService,0,-2);
					if (strlen($strService) > 50) {$strService = ".....";} else {$strAdd = "";}
					$mastertp->setVariable("DATA_FIELD_2",substr(stripslashes($strService),0,50).$strAdd);
				} else {
					$mastertp->setVariable("DATA_FIELD_2","None or '*'");
				}
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
	// Seiten anzeigen
	$mastertp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	if (isset($intCount)) $mastertp->setVariable("PAGES",$myVisClass->buildPageLinks($_SERVER['PHP_SELF'],$intCount,$chkLimit));
	$mastertp->parse("datatable");
	$mastertp->show("datatable");
}
// Mitteilungen ausgeben
if (isset($strMessage) && ($strMessage != "")) $mastertp->setVariable("DBMESSAGE",$strMessage);
$mastertp->setVariable("LAST_MODIFIED",$LANG['db']['last_modified']."<b>".$strLastModified."</b>");
$mastertp->setVariable("FILEDATE",$LANG['common']['filedate']."<b>".$strFileDate."</b>");
if ($strOld != "") $mastertp->setVariable("FILEISOLD","<br><span class=\"dbmessage\">".$strOld."</span><br>");
$mastertp->parse("msgfooter");
$mastertp->show("msgfooter");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org'>NagiosQL</a> - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>