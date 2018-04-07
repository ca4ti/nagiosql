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
// Zweck:	Service escalations definieren
// Datei:	admin/serviceescalations.php
// Version: 2.00.00 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 5;
$intSub  		= 11;
$intMenu 		= 2;
$preContent 	= "serviceescalations.tpl.htm";
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
$chkSelHost 			= isset($_POST['selHost']) 			? $_POST['selHost'] 					: array("");
$chkSelService		 	= isset($_POST['selService']) 		? $_POST['selService'] 					: array("");
$chkSelHostgroup 		= isset($_POST['selHostGroup']) 	? $_POST['selHostGroup'] 				: array("");
$chkSelServicegroup	 	= isset($_POST['selServiceGroup']) 	? $_POST['selServiceGroup'] 			: array("");
$chkSelEscPeriod		= isset($_POST['selEscPeriod']) 	? $_POST['selEscPeriod'] 				: "";
$chkTfConfigName 		= isset($_POST['tfConfigName']) 	? addslashes($_POST['tfConfigName']) 	: "";
$chkTfFirstNotif 		= isset($_POST['tfFirstNotif']) 	? addslashes($_POST['tfFirstNotif']) 	: "NULL";
$chkTfLastNotif 		= isset($_POST['tfLastNotif']) 		? addslashes($_POST['tfLastNotif']) 	: "NULL";
$chkTfNotifInterval 	= isset($_POST['tfNotifInterval']) 	? addslashes($_POST['tfNotifInterval']) : "NULL";
$chkEOw					= isset($_POST['chbEOw'])			? $_POST['chbEOw'].","					: "";
$chkEOu					= isset($_POST['chbEOu'])			? $_POST['chbEOu'].","					: "";
$chkEOc					= isset($_POST['chbEOc'])			? $_POST['chbEOc'].","					: "";
$chkEOr					= isset($_POST['chbEOr'])			? $_POST['chbEOr'].","					: "";
// Hostgruppe abwählen, wenn beides benutzt wird
//if (($chkSelHost[0] != "0") && ($chkSelHostgroup[0] != "0")) {
//	$chkSelHostgroup = "";
//	$chkSelHostgroup[0] = "0";
//}
//
// Daten verarbeiten
// =================
$strEO 	  = substr($chkEOw.$chkEOu.$chkEOc.$chkEOr,0,-1);
if (($chkSelHost[0] 		== "") || ($chkSelHost[0] 		  == "0")) {$intSelHost 		= 0;} else {$intSelHost 		= 1;}
if (($chkSelHostgroup[0] 	== "") || ($chkSelHostgroup[0] 	  == "0")) {$intSelHostGroup 	= 0;} else {$intSelHostGroup 	= 1;}
if (($chkSelService[0] 		== "") || ($chkSelService[0] 	  == "0")) {$intSelService 		= 0;} else {$intSelService 		= 1;}
if (($chkSelServicegroup[0] == "") || ($chkSelServicegroup[0] == "0")) {$intSelServicegroup = 0;} else {$intSelServicegroup = 1;}
if (($chkSelContactGroup[0] == "") || ($chkSelContactGroup[0] == "0")) {$intSelContactGroup = 0;} else {$intSelContactGroup = 1;}
// Datein einfügen oder modifizieren
if (($chkModus == "insert") || ($chkModus == "modify")) {
	if ($hidActive == 1) $chkActive = 1;
	$strSQLx = "tbl_serviceescalation SET config_name='$chkTfConfigName', host_name=$intSelHost, 
				service_description=$intSelService, hostgroup_name=$intSelHostGroup, 
				servicegroup_name=$intSelServicegroup, contact_groups=$intSelContactGroup, 
				first_notification=$chkTfFirstNotif, last_notification=$chkTfLastNotif, 
				notification_interval=$chkTfNotifInterval, escalation_period='$chkSelEscPeriod', 
				escalation_options='$strEO', active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL = "INSERT INTO ".$strSQLx; 
	} else {
		$strSQL = "UPDATE ".$strSQLx." WHERE id=$chkDataId";   
	}	
	if (((($intSelHost != 0) && ($intSelService != 0) && ($intSelHostGroup == 0) && ($intSelServicegroup == 0)) ||
	     (($intSelHost == 0) && ($intSelService != 0) && ($intSelHostGroup != 0) && ($intSelServicegroup == 0)) ||
	     (($intSelHost == 0) && ($intSelService == 0) && ($intSelHostGroup == 0) && ($intSelServicegroup != 0))) && 
		 ($intSelContactGroup != 0) && ($chkTfFirstNotif != "NULL") && ($chkTfLastNotif != "NULL") && 
		 ($chkTfNotifInterval != "NULL") && ($chkTfConfigName != "")) {
		$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		if ($intInsert == 1) {
			$intReturn = 1;
		} else {
			if ($chkModus  == "insert") 	$myDataClass->writeLog($LANG['logbook']['newservesc']." ".$chkTfConfigName);
			if ($chkModus  == "modify") 	$myDataClass->writeLog($LANG['logbook']['modifyservesc']." ".$chkTfConfigName);
			//
			// Relationen eintragen/updaten
			// ============================
			$intTableA = $myDataClass->tableID("tbl_serviceescalation");
			if ($chkModus == "insert") {
				if ($intSelHost 		== 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_host"),$intInsertId,'host_name',$chkSelHost);
				if ($intSelHostGroup 	== 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_hostgroup"),$intInsertId,'hostgroup_name',$chkSelHostGroup);
				if ($intSelService 		== 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_service"),$intInsertId,'service_description',$chkSelService);
				if ($intSelServicegroup == 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_servicegroup"),$intInsertId,'servicegroup_name',$chkSelServicegroup);
				if ($intSelContactGroup == 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_contactgroup"),$intInsertId,'contact_groups',$chkSelContactGroup);
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
				if ($intSelHost == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_service"),$chkDataId,'service_description',$chkSelService);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_service"),$chkDataId,'service_description');
				}
				if ($intSelServicegroup == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_servicegroup"),$chkDataId,'servicegroup_name',$chkSelServicegroup);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_servicegroup"),$chkDataId,'servicegroup_name');
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
	$intReturn = $myConfigClass->createConfig("tbl_serviceescalation",0);
	$chkModus  = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$intReturn = $myDataClass->dataDeleteSimple("tbl_serviceescalation",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$intReturn = $myDataClass->dataCopySimple("tbl_serviceescalation",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_serviceescalation WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";	
	$strHosts	   = $arrModifyData['host_name'];
	$strHostGroups = $arrModifyData['hostgroup_name'];
	$chkModus      = "add";
}
// Statusmitteilungen setzen
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $myDataClass->strDBMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myConfigClass->lastModified("tbl_serviceescalation",$strLastModified,$strFileDate,$strOld);
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm5']." -> ".$LANG['menu']['info11']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['serviceescal']);
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if (($chkModus == "add") || ($chkModus == "refresh")) {
	// Klassenvariabeln definieren
	$myVisClass->resTemplate     =& $conttp;
	$myVisClass->strTempValue1	 = $chkSelModify;
	$myVisClass->strTempValue2 	 = $chkModus;
	$myVisClass->intTabA   	     = $myDataClass->tableID("tbl_serviceescalation");
	if (isset($arrModifyData)) {
		$myVisClass->arrWorkdata = $arrModifyData;
		$myVisClass->intTabA_id  = $arrModifyData['id'];
	} else {
		$myVisClass->intTabA_id  = 0;
	}	
	// Hostname in Auswahlliste einfügen
	$intReturn = 0;
	$intReturn = $myVisClass->parseSelectNew('tbl_host','host_name','DAT_HOST','host','host_name',2,2,0,$chkSelHost);
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_host']."<br>";	
	// Hostgruppe in Auswahlliste einfügen
	$intReturn = 0;
	$intReturn = $myVisClass->parseSelectNew('tbl_hostgroup','hostgroup_name','DAT_HOSTGROUP','hostgroup','hostgroup_name',2,2,0,$chkSelHostgroup);
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_host']."<br>";	
	// Service vorbereiten
	if (($intSelHost == "0") && ($intSelHostGroup != "0")) {
	    $strAddSql = "";
		if ($chkSelHostgroup[0] != "*") {
			foreach ($chkSelHostgroup AS $elem) $strHostgroupId = $elem.",";
			$strAddSql = "AND tbl_hostgroup.id IN (".substr($strHostgroupId,0,-1).")";
		}
		$strSQLMembers = "SELECT DISTINCT tbl_service.id, tbl_host.host_name, tbl_service.service_description FROM tbl_hostgroup
						  LEFT JOIN tbl_relation AS rel_1 ON tbl_hostgroup.id=rel_1.tbl_A_id
						  LEFT JOIN tbl_host ON rel_1.tbl_B_id=tbl_host.id
						  LEFT JOIN tbl_relation AS rel_2 ON tbl_host.id=rel_2.tbl_B_id
						  LEFT JOIN tbl_service ON rel_2.tbl_A_id=tbl_service.id						  
						  WHERE (rel_1.tbl_A=8 AND rel_1.tbl_B=4 AND rel_1.tbl_A_field='members' AND
						  rel_2.tbl_A=10 AND rel_2.tbl_B=4 AND rel_2.tbl_A_field='host_name'
						  $strAddSql) OR tbl_service.host_name=2
						  GROUP BY tbl_service.service_description
						  ORDER BY tbl_service.service_description";
		$booReturn = $myDBClass->getDataArray($strSQLMembers,$arrDataMembers,$intDataCount);
		if ($booReturn == false) {
			$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
		} else if ($intDataCount != 0) {
			foreach($arrDataMembers AS $key) {
				$conttp->setVariable("DAT_SERVICE",$key['service_description']);
				$conttp->setVariable("DAT_SERVICE_ID",$key['id']);
				if (isset($arrModifyData) && ($myDataClass->findRelation(12,10,$arrModifyData['id'],"service_description",$key['id']) == 1)) {
					$conttp->setVariable("DAT_SERVICE_SEL","selected");
				}
			$conttp->parse("service");
			}		
		}
	} else if ((($intSelHost != "0") && ($intSelHostGroup == "0")) || (isset($arrModifyData['host_name']) && ($arrModifyData['host_name'] != 0))) {
		$strAddSql = "";
		if ($chkSelHost[0] != "*") {
			$strHostId = "";
			foreach ($chkSelHost AS $elem) $strHostId .= $elem.",";
			$strAddSql = "AND tbl_relation.tbl_B_id IN (".substr($strHostId,0,-1).")";
		}
		if (isset($arrModifyData['host_name'])) {
			$strHostId = "";
			$strSQL = "SELECT tbl_B_id FROM tbl_relation WHERE tbl_A=12 AND tbl_B=4 AND tbl_A_id=".$arrModifyData['id']." AND
					   tbl_A_field='host_name'";
			$myDBClass->getDataArray($strSQL,$arrDataServices,$intDataCount);
			foreach ($arrDataServices AS $elem) {$strHostId .= $elem['tbl_B_id'].",";}
			$strAddSql = "AND tbl_relation.tbl_B_id IN (".substr($strHostId,0,-1).")";
		}
		$strSQLMembers = "SELECT DISTINCT tbl_service.id, tbl_service.service_description FROM tbl_service
						  LEFT JOIN tbl_relation ON tbl_service.id=tbl_relation.tbl_A_id
						  WHERE (tbl_relation.tbl_A=10 AND tbl_relation.tbl_B=4 AND tbl_relation.tbl_A_field='host_name'
						  $strAddSql) OR tbl_service.host_name=2
						  ORDER BY tbl_service.service_description";
		$booReturn = $myDBClass->getDataArray($strSQLMembers,$arrDataMembers,$intDataCount);
		if ($booReturn == false) {
			$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
		} else if ($intDataCount != 0) {
			foreach($arrDataMembers AS $key) {
				$conttp->setVariable("DAT_SERVICE",$key['service_description']);
				$conttp->setVariable("DAT_SERVICE_ID",$key['id']);
				if (isset($arrModifyData) && ($myDataClass->findRelation(12,10,$arrModifyData['id'],"service_description",$key['id']) == 1)) {
					$conttp->setVariable("DAT_SERVICE_SEL","selected");
				}
			$conttp->parse("service");
			}		
		}
	}
	// Servicegruppen in Auswahlliste einfügen
	$myVisClass->parseSelectNew('tbl_servicegroup','servicegroup_name','DAT_SERVICEGROUP','servicegroup','servicegroup_name',2,2,0,$chkSelServicegroup);
	// Eskalationszeiten in Auswahlliste einfügen
	$myVisClass->parseSelectNew('tbl_timeperiod','timeperiod_name','DAT_ESCPERIOD','escperiod','escalation_period',1,1,0,$chkSelEscPeriod);
	// Kontaktgruppen in Auswahlliste einfügen
	$intReturn = 0;
	$intReturn = $myVisClass->parseSelectNew('tbl_contactgroup','contactgroup_name','DAT_CONTACTGROUP','contactgroup','contact_groups',2,0,0,$chkSelContactGroup);
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
	if ($chkModus == "refresh") {
		if ($chkTfFirstNotif != "NULL") 	$conttp->setVariable("DAT_FIRST_NOTIFICATION",$chkTfFirstNotif);
		if ($chkTfLastNotif != "NULL") 		$conttp->setVariable("DAT_LAST_NOTIFICATION",$chkTfLastNotif);
		if ($chkTfNotifInterval != "NULL") 	$conttp->setVariable("DAT_NOTIFICATION_INTERVAL",$chkTfNotifInterval);
		if ($chkTfConfigName != "") 		$conttp->setVariable("DAT_CONFIG_NAME",$chkTfConfigName);
		foreach(explode(",",$strEO) AS $elem) {
			$conttp->setVariable("DAT_EO".strtoupper($elem)."_CHECKED","checked");
		}
		if ($chkActive != 1) $conttp->setVariable("ACT_CHECKED","");
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
		foreach(explode(",",$arrModifyData['escalation_options']) AS $elem) {
			$conttp->setVariable("DAT_EO".strtoupper($elem)."_CHECKED","checked");
		}		
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
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['service']." / ".$LANG['admintable']['servicegroup']);	
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);	
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_serviceescalation");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_serviceescalation";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
	// Datensätze holen
	$strSQL    = "SELECT tbl_serviceescalation.id, tbl_serviceescalation.config_name, tbl_service.service_description, 
						 tbl_servicegroup.servicegroup_name, tbl_serviceescalation.active
				  FROM tbl_serviceescalation 
				  LEFT JOIN tbl_relation AS rel_1 ON tbl_serviceescalation.id=rel_1.tbl_A_id
				  LEFT JOIN tbl_service ON rel_1.tbl_B_id=tbl_service.id
				  LEFT JOIN tbl_relation AS rel_2 ON tbl_serviceescalation.id=rel_2.tbl_A_id
				  LEFT JOIN tbl_servicegroup ON rel_2.tbl_B_id=tbl_servicegroup.id
				  WHERE (rel_1.tbl_A=12 AND rel_1.tbl_B=10 AND rel_1.tbl_A_field='service_description') OR
				  		(rel_2.tbl_A=12 AND rel_2.tbl_B=14 AND rel_2.tbl_A_field='servicegroup_name') OR
						tbl_serviceescalation.service_description = 2
				  GROUP by tbl_serviceescalation.config_name
				  ORDER BY tbl_service.service_description,tbl_servicegroup.servicegroup_name
				  LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
			if ($arrDataLines[$i]['service_description'] != "") {
				if (strlen($arrDataLines[$i]['service_description']) > 50) {$strAdd = ".....";} else {$strAdd = "";}
				$mastertp->setVariable("DATA_FIELD_2",substr($arrDataLines[$i]['service_description'],0,50).$strAdd);
			} else {
				if (strlen($arrDataLines[$i]['servicegroup_name']) > 50) {$strAdd = ".....";} else {$strAdd = "";}
				$mastertp->setVariable("DATA_FIELD_2",substr($arrDataLines[$i]['servicegroup_name'],0,50).$strAdd);
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