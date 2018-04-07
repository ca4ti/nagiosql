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
// Zweck:	Host Eskalationen definieren
// Datei:	admin/hostescalations.php
// Version: 2.0.2 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 5;
$intSub  		= 13;
$intMenu 		= 2;
$preContent 	= "hostescalations.tpl.htm";
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
$chkSelContactGroup 	= isset($_POST['selContactGroup']) 	? $_POST['selContactGroup'] 			: array("");
$chkSelHostGroup 		= isset($_POST['selHostGroup']) 	? $_POST['selHostGroup'] 				: array("");
$chkSelHost 			= isset($_POST['selHost']) 			? $_POST['selHost'] 					: array("");
$chkSelService		 	= isset($_POST['selService']) 		? $_POST['selService'] 					: "";
$chkSelEscPeriod		= isset($_POST['selEscPeriod']) 	? $_POST['selEscPeriod']				: "";
$chkTfConfigName 		= isset($_POST['tfConfigName']) 	? addslashes($_POST['tfConfigName'])	: "";
$chkTfFirstNotif 		= isset($_POST['tfFirstNotif']) 	? $_POST['tfFirstNotif'] 				: "NULL";
$chkTfLastNotif 		= isset($_POST['tfLastNotif']) 		? $_POST['tfLastNotif'] 				: "NULL";
$chkTfNotifInterval 	= isset($_POST['tfNotifInterval']) 	? $_POST['tfNotifInterval'] 			: "NULL";
$chkEOd					= isset($_POST['chbEOd'])			? $_POST['chbEOd'].","					: "";
$chkEOu					= isset($_POST['chbEOu'])			? $_POST['chbEOu'].","					: "";
$chkEOr					= isset($_POST['chbEOr'])			? $_POST['chbEOr'].","					: "";
//
// Daten verarbeiten
// =================
$strEO = substr($chkEOd.$chkEOu.$chkEOr,0,-1);
if (($chkSelHost[0] 		== "")	|| ($chkSelHost[0] 	   	   == "0"))	{$intSelHost 		 = 0;}	else {$intSelHost 		  = 1;}
if (($chkSelHostGroup[0] 	== "")	|| ($chkSelHostGroup[0]    == "0"))	{$intSelHostGroup    = 0;}  else {$intSelHostGroup 	  = 1;}
if (($chkSelContactGroup[0] == "")	|| ($chkSelContactGroup[0] == "0"))	{$intSelContactGroup = 0;}  else {$intSelContactGroup = 1;}
if ($chkSelHost[0]          == "*") $intSelHost = 2;
if ($chkSelHostGroup[0]     == "*") $intSelHostGroup = 2;
// Datein einfügen oder modifizieren
if (($chkModus == "insert") || ($chkModus == "modify")) {
	if ($hidActive == 1) $chkActive = 1;
	$strSQLx = "tbl_hostescalation SET config_name='$chkTfConfigName', host_name=$intSelHost, hostgroup_name=$intSelHostGroup, 
				contact_groups=$intSelContactGroup, first_notification=$chkTfFirstNotif, last_notification=$chkTfLastNotif, 
				notification_interval=$chkTfNotifInterval, escalation_period='$chkSelEscPeriod', escalation_options='$strEO',
				active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL = "INSERT INTO ".$strSQLx; 
	} else {
		$strSQL = "UPDATE ".$strSQLx." WHERE id=$chkDataId";   
	}	
	if ((($intSelHost != 0) || ($chkSelHostGroup != 0)) && ($intSelContactGroup != 0) && ( $chkTfFirstNotif != "NULL") && 
	    ($chkTfLastNotif != "NULL") && ($chkTfNotifInterval != "NULL")) {
		$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		if ($intInsert == 1) {
			$intReturn = 1;
		} else {
			if ($chkModus  == "insert") 	$myDataClass->writeLog($LANG['logbook']['newhostesc']." ".$chkTfConfigName);
			if ($chkModus  == "modify") 	$myDataClass->writeLog($LANG['logbook']['modifyhostesc']." ".$chkTfConfigName);
			//
			// Relationen eintragen/updaten
			// ============================
			$intTableA = $myDataClass->tableID("tbl_hostescalation");
			if ($chkModus == "insert") {
				if ($intSelHost 		== 1) 	$myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_host"),$intInsertId,'host_name',$chkSelHost);
				if ($intSelHostGroup    == 1)  	$myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$intInsertId,'hostgroup_name',$chkSelHostGroup);
				if ($intSelContactGroup == 1)	$myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_contactgroup"),$intInsertId,'contact_groups',$chkSelContactGroup);
			} else if ($chkModus == "modify") {		
				if ($intSelHost == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'host_name',$chkSelHost);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'host_name');
				}
				if ($intSelHostGroup == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$chkDataId,'hostgroup_name',$chkSelHostGroup);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$chkDataId,'hostgroup_name');
				}
				if ($intSelContactGroup == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_contactgroup"),$chkDataId,'contact_groups',$chkSelContactGroup);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_contactgroup"),$chkDataId,'contact_groups');
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
	$intReturn = $myConfigClass->createConfig("tbl_hostescalation",0);
	$chkModus  = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$intReturn = $myDataClass->dataDeleteSimple("tbl_hostescalation",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$intReturn = $myDataClass->dataCopySimple("tbl_hostescalation",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_hostescalation WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	$chkModus      = "add";
}
// Statusmitteilungen setzen
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $myDataClass->strDBMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myConfigClass->lastModified("tbl_hostescalation",$strLastModified,$strFileDate,$strOld);
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm5']." -> ".$LANG['menu']['info13']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['hostescal']);
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if ($chkModus == "add") {
	// Klassenvariabeln definieren
	$myVisClass->resTemplate     =& $conttp;
	$myVisClass->strTempValue1   = $chkSelModify;
	$myVisClass->intTabA   	     = $myDataClass->tableID("tbl_hostescalation");
	if (isset($arrModifyData)) {
		$myVisClass->arrWorkdata = $arrModifyData;
		$myVisClass->intTabA_id  = $arrModifyData['id'];
	} else {
		$myVisClass->intTabA_id  = 0;
	}	
	// Hostfelder füllen
	$intReturn = 0;
	$intReturn = $myVisClass->parseSelectNew('tbl_host','host_name','DAT_HOST','host','host_name',2,2);
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_host']."<br>";
	// Hostgruppenfelder füllen
	$myVisClass->parseSelectNew('tbl_hostgroup','hostgroup_name','DAT_HOSTGROUP','hostgroup','hostgroup_name',2,2);
	// Eskalationsfelder füllen
	$myVisClass->parseSelectNew('tbl_timeperiod','timeperiod_name','DAT_ESCPERIOD','escperiod','escalation_period',1,1);
	// Kontaktgruppenfelder füllen
	$intReturn = 0;
	$intReturn = $myVisClass->parseSelectNew('tbl_contactgroup','contactgroup_name','DAT_CONTACTGROUP','contactgroup','contact_groups',2,0);
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_contgroups']."<br>";
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
		$conttp->setVariable("MODUS","modify");
		// Optionskästchen verarbeiten
		foreach(explode(",",$arrModifyData['escalation_options']) AS $elem) {
			$conttp->setVariable("DAT_EO".strtoupper($elem)."_CHECKED","checked");
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
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['hostnames']." / ".$LANG['admintable']['hostgroups']);	
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);	
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_hostescalation");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_hostescalation";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
	// Datensätze holen
	$strSQL    = "SELECT id, config_name, host_name, hostgroup_name, active 
				  FROM tbl_hostescalation ORDER BY config_name LIMIT $chkLimit,".$SETS['common']['pagelines'];
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	echo mysql_error();
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
			if ($arrDataLines[$i]['host_name'] != "0") {
				$strSQLHost = "SELECT tbl_host.host_name FROM tbl_hostescalation
							   LEFT JOIN tbl_relation ON tbl_hostescalation.id = tbl_relation.tbl_A_id
							   LEFT JOIN tbl_host ON tbl_relation.tbl_B_id = tbl_host.id
							   WHERE tbl_relation.tbl_A=".$myDataClass->tableID("tbl_hostescalation")." 
							         AND tbl_relation.tbl_B=".$myDataClass->tableID("tbl_host")." 
									 AND tbl_A_field='host_name'
									 AND tbl_A_id=".$arrDataLines[$i]['id']."
							   ORDER BY tbl_host.host_name";
				$booReturn = $myDBClass->getDataArray($strSQLHost,$arrDataHosts,$intDataCount2);
				$strHosts = "";
				if ($intDataCount2 != 0) {
					foreach($arrDataHosts AS $elem) {
						$strHosts .= $elem['host_name'].",";
					}
				}
				if (strlen(substr($strHosts,0,-1)) > 50) {$strAdd = ".....";} else {$strAdd = "";}
				if ($strHosts == "") $strHosts = "*,";
				$mastertp->setVariable("DATA_FIELD_2",substr(stripslashes(substr($strHosts,0,-1)),0,50).$strAdd);
			} else {
				$strSQLHost = "SELECT tbl_hostgroup.hostgroup_name FROM tbl_hostescalation
							   LEFT JOIN tbl_relation ON tbl_hostescalation.id = tbl_relation.tbl_A_id
							   LEFT JOIN tbl_hostgroup ON tbl_relation.tbl_B_id = tbl_hostgroup.id
							   WHERE tbl_relation.tbl_A=".$myDataClass->tableID("tbl_hostescalation")." 
							         AND tbl_relation.tbl_B=".$myDataClass->tableID("tbl_hostgroup")." 
									 AND tbl_A_field='hostgroup_name'
									 AND tbl_A_id=".$arrDataLines[$i]['id']."
							   ORDER BY tbl_hostgroup.hostgroup_name";
				$booReturn = $myDBClass->getDataArray($strSQLHost,$arrDataHostgroups,$intDataCount2);
				echo mysql_error();
				$strHostgroups = "";
				if ($intDataCount2 != 0) {
					foreach($arrDataHostgroups AS $elem) {
						$strHostgroups .= $elem['hostgroup_name'].",";
					}
				}
				if ($strHostgroups == "") $strHostgroups = "*,";
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
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org'>NagiosQL</a> - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>