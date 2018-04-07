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
// Zweck:	Host Abhängigkeiten definieren
// Datei:	admin/hostdependencies.php
// Version: 2.00.00 (Internal)
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
$chkSelHostDepend		= isset($_POST['selHostDepend']) 	? $_POST['selHostDepend'] 				: array("");
$chkSelHost 			= isset($_POST['selHost']) 			? $_POST['selHost'] 					: array("");
$chkSelHostgroupDep		= isset($_POST['selHostgroupDep']) 	? $_POST['selHostgroupDep'] 			: array("");
$chkSelHostgroup		= isset($_POST['selHostgroup']) 	? $_POST['selHostgroup'] 				: array("");
$chkEOo					= isset($_POST['chbEOo'])			? $_POST['chbEOo'].","					: "";
$chkEOd					= isset($_POST['chbEOd'])			? $_POST['chbEOd'].","					: "";
$chkEOu					= isset($_POST['chbEOu'])			? $_POST['chbEOu'].","					: "";
$chkEOp					= isset($_POST['chbEOp'])			? $_POST['chbEOp'].","					: "";
$chkEOn					= isset($_POST['chbEOn'])			? $_POST['chbEOn'].","					: "";
$chkNOo					= isset($_POST['chbNOo'])			? $_POST['chbNOo'].","					: "";
$chkNOd					= isset($_POST['chbNOd'])			? $_POST['chbNOd'].","					: "";
$chkNOu					= isset($_POST['chbNOu'])			? $_POST['chbNOu'].","					: "";
$chkNOp					= isset($_POST['chbNOp'])			? $_POST['chbNOp'].","					: "";
$chkNOn					= isset($_POST['chbNOn'])			? $_POST['chbNOn'].","					: "";
$chkTfConfigName 		= isset($_POST['tfConfigName']) 	? addslashes($_POST['tfConfigName'])	: "";
$chkInherit				= isset($_POST['chbInherit'])		? $_POST['chbInherit']					: 0;
//
// Daten verarbeiten
// =================
$strEO 	  = substr($chkEOo.$chkEOd.$chkEOu.$chkEOp.$chkEOn,0,-1);
$strNO 	  = substr($chkNOo.$chkNOd.$chkNOu.$chkNOp.$chkNOn,0,-1);
if (($chkSelHostDepend[0]   == "")	|| ($chkSelHostDepend[0]   == "0"))	{$intSelHostDepend   = 0;}	else {$intSelHostDepend   = 1;}
if (($chkSelHost[0]         == "")	|| ($chkSelHost[0]         == "0"))	{$intSelHost         = 0;} 	else {$intSelHost 		  = 1;}
if (($chkSelHostgroupDep[0] == "")	|| ($chkSelHostgroupDep[0] == "0"))	{$intSelHostgroupDep = 0;}  else {$intSelHostgroupDep = 1;}
if (($chkSelHostgroup[0]    == "")	|| ($chkSelHostgroup[0]    == "0"))	{$intSelHostgroup    = 0;} 	else {$intSelHostgroup 	  = 1;}
// Datein einfügen oder modifizieren
if (($chkModus == "insert") || ($chkModus == "modify")) {
	if ($hidActive == 1) $chkActive = 1;
	$strSQLx = "tbl_hostdependency SET config_name='$chkTfConfigName', dependent_host_name=$intSelHostDepend, host_name=$intSelHost, 
				dependent_hostgroup_name=$intSelHostgroupDep, hostgroup_name=$intSelHostgroup, inherits_parent='$chkInherit', 
				execution_failure_criteria='$strEO', notification_failure_criteria='$strNO', active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL = "INSERT INTO ".$strSQLx; 
	} else {
		$strSQL = "UPDATE ".$strSQLx." WHERE id=$chkDataId";   
	}	
	if ((($intSelHostDepend != 0) && ($intSelHost != 0)) || (($intSelHostgroupDep != 0) && ($intSelHostgroup != 0))) {
		$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		if ($intInsert == 1) {
			$intReturn = 1;
		} else {
			if ($chkModus  == "insert") 	$myDataClass->writeLog($LANG['logbook']['newhostdep']." ".$chkTfConfigName);
			if ($chkModus  == "modify") 	$myDataClass->writeLog($LANG['logbook']['modifyhostdep']." ".$chkTfConfigName);
			//
			// Relationen eintragen/updaten
			// ============================
			$intTableA = $myDataClass->tableID("tbl_hostdependency");
			if ($chkModus == "insert") {
				if ($intSelHostDepend 	== 1) 	$myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_host"),$intInsertId,'dependent_host_name',$chkSelHostDepend);
				if ($intSelHost			== 1)  	$myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_host"),$intInsertId,'host_name',$chkSelHost);
				if ($intSelHostgroupDep == 1)	$myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$intInsertId,'dependent_hostgroup_name',$chkSelHostgroupDep);
				if ($intSelHostgroup 	== 1) 	$myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$intInsertId,'hostgroup_name',$chkSelHostgroup);
			} else if ($chkModus == "modify") {		
				if ($intSelHostDepend == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'dependent_host_name',$chkSelHostDepend);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'dependent_host_name');
				}
				if ($intSelHost == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'host_name',$chkSelHost);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'host_name');
				}
				if ($intSelHostgroupDep == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$chkDataId,'dependent_hostgroup_name',$chkSelHostgroupDep);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$chkDataId,'dependent_hostgroup_name');
				}
				if ($intSelHostgroup == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$chkDataId,'hostgroup_name',$chkSelHostgroup);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$chkDataId,'hostgroup_name');
				}	
			}		
			$intReturn = 0;
		}		
	} else {
		$strMessage .= $LANG['db']['datamissing'];
	}
	$chkModus = "display";
}  else if ($chkModus == "make") {
	// Konfigurationsdatei schreiben
	$intReturn = $myConfigClass->createConfig("tbl_hostdependency",0);
	$chkModus  = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$intReturn = $myDataClass->dataDeleteSimple("tbl_hostdependency",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$intReturn = $myDataClass->dataCopySimple("tbl_hostdependency",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_hostdependency WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	$chkModus      = "add";
}
// Statusmitteilungen setzen
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $myDataClass->strDBMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myConfigClass->lastModified("tbl_hostdependency",$strLastModified,$strFileDate,$strOld);
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
	// Klassenvariabeln definieren
	$myVisClass->resTemplate     =& $conttp;
	$myVisClass->strTempValue1   = $chkSelModify;
	$myVisClass->intTabA   	     = $myDataClass->tableID("tbl_hostdependency");
	if (isset($arrModifyData)) {
		$myVisClass->arrWorkdata = $arrModifyData;
		$myVisClass->intTabA_id  = $arrModifyData['id'];
	} else {
		$myVisClass->intTabA_id  = 0;
	}	
	// Hostfelder füllen
	$intReturn = 0;
	$intReturn = $myVisClass->parseSelectNew('tbl_host','host_name','DAT_HOSTDEPEND','hostdepend','dependent_host_name',2,1);
	$intReturn = $myVisClass->parseSelectNew('tbl_host','host_name','DAT_HOST','host','host_name',2,1);
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_host']."<br>";	
	// Hostgruppenfelder füllen
	$intReturn = $myVisClass->parseSelectNew('tbl_hostgroup','hostgroup_name','DAT_HOSTGROUPDEP','hostgroupdepend','dependent_hostgroup_name',2,1);
	$intReturn = $myVisClass->parseSelectNew('tbl_hostgroup','hostgroup_name','DAT_HOSTGROUP','hostgroup','hostgroup_name',2,1);
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
	// Im Modus "Modifizieren" die Datenfelder setzen
	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		foreach($arrModifyData AS $key => $value) {
			if (($key == "active") || ($key == "last_modified") || ($key == "access_rights")) continue;
			$conttp->setVariable("DAT_".strtoupper($key),htmlspecialchars(stripslashes($value)));
		}
		if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
		if ($arrModifyData['inherits_parent'] == 1) $conttp->setVariable("ACT_INHERIT","checked");
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
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
	// Datensätze holen
	$strSQL    = "SELECT id, config_name, dependent_host_name, dependent_hostgroup_name, active 
				  FROM tbl_hostdependency ORDER BY config_name LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
			if ($arrDataLines[$i]['dependent_host_name'] != 0) {
				$strSQLHost = "SELECT tbl_host.host_name FROM tbl_hostdependency
							   LEFT JOIN tbl_relation ON tbl_hostdependency.id = tbl_relation.tbl_A_id
							   LEFT JOIN tbl_host ON tbl_relation.tbl_B_id = tbl_host.id
							   WHERE tbl_relation.tbl_A=".$myDataClass->tableID("tbl_hostdependency")." 
							         AND tbl_relation.tbl_B=".$myDataClass->tableID("tbl_host")." 
									 AND tbl_A_field='dependent_host_name'
									 AND tbl_A_id=".$arrDataLines[$i]['id']."
							   ORDER BY tbl_host.host_name";
				$booReturn = $myDBClass->getDataArray($strSQLHost,$arrDataHosts,$intDataCount2);
				$strHosts = "";
				if ($intDataCount != 0) {
					foreach($arrDataHosts AS $elem) {
						$strHosts .= $elem['host_name'].",";
					}
				}
				if (strlen(substr($strHosts,0,-1)) > 50) {$strAdd = ".....";} else {$strAdd = "";}
				$mastertp->setVariable("DATA_FIELD_2",substr(stripslashes(substr($strHosts,0,-1)),0,50).$strAdd);
			} else {
				$strSQLHost = "SELECT tbl_hostgroup.hostgroup_name FROM tbl_hostdependency
							   LEFT JOIN tbl_relation ON tbl_hostdependency.id = tbl_relation.tbl_A_id
							   LEFT JOIN tbl_hostgroup ON tbl_relation.tbl_B_id = tbl_hostgroup.id
							   WHERE tbl_relation.tbl_A=".$myDataClass->tableID("tbl_hostdependency")." 
							         AND tbl_relation.tbl_B=".$myDataClass->tableID("tbl_hostgroup")." 
									 AND tbl_A_field='dependent_hostgroup_name'
									 AND tbl_A_id=".$arrDataLines[$i]['id']."
							   ORDER BY tbl_hostgroup.hostgroup_name";
				$booReturn = $myDBClass->getDataArray($strSQLHost,$arrDataHostgroups,$intDataCount2);
				echo mysql_error();
				$strHostgroups = "";
				if ($intDataCount != 0) {
					foreach($arrDataHostgroups AS $elem) {
						$strHostgroups .= $elem['hostgroup_name'].",";
					}
				}
				if (strlen(substr($strHostgroups,0,-1)) > 50) {$strAdd = ".....";} else {$strAdd = "";}
				$mastertp->setVariable("DATA_FIELD_2",substr(stripslashes(substr($strHostgroups,0,-1)),0,50).$strAdd);
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
$maintp->setVariable("VERSION_INFO","NagiosQL - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>