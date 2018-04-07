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
// Zweck:	Servicegruppen definieren
// Datei:	admin/servicegroups.php
// Version: 2.00.00 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 2;
$intSub  		= 9;
$intMenu 		= 2;
$preContent 	= "servicegroups.tpl.htm";
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
$chkTfName 				= isset($_POST['tfName']) 			? addslashes($_POST['tfName']) 		: "";
$chkTfFriendly 			= isset($_POST['tfFriendly']) 		? addslashes($_POST['tfFriendly']) 	: "";
$chkSelMembers 			= isset($_POST['selMembers']) 		? $_POST['selMembers'] 				: array("");
//
// Daten verarbeiten
// =================
if (($chkSelMembers[0] == "")   || ($chkSelMembers[0] == "0"))   {$intSelMembers = 0;}  else {$intSelMembers = 1;}
// Datein einfügen oder modifizieren
if (($chkModus == "insert") || ($chkModus == "modify")) {
	if ($hidActive == 1) $chkActive = 1;
	$strSQLx = "tbl_servicegroup SET servicegroup_name='$chkTfName', alias='$chkTfFriendly', members=$intSelMembers, 
			    active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL = "INSERT INTO ".$strSQLx; 
	} else {
		$strSQL = "UPDATE ".$strSQLx." WHERE id=$chkDataId";   
	}	
	if (($chkTfName != "") && ($chkTfFriendly != "") && ($intSelMembers == 1)) {
		$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		if ($intInsert == 1) {
			$intReturn = 1;
		} else {
			if ($chkModus  == "insert") 	$myDataClass->writeLog($LANG['logbook']['newservgr']." ".$chkTfName);
			if ($chkModus  == "modify") 	$myDataClass->writeLog($LANG['logbook']['modifyservgr']." ".$chkTfName);
			//
			// Relationen eintragen/updaten
			// ============================
			$intTableA = $myDataClass->tableID("tbl_servicegroup");
			if ($chkModus == "insert") {
				if ($intSelMembers == 1)  $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_host"),$intInsertId,'members',$chkSelMembers,$myDataClass->tableID("tbl_service"));
			} else if ($chkModus == "modify") {		
				if ($intSelMembers == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'members',$chkSelMembers,$myDataClass->tableID("tbl_service"));
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_host"),$chkDataId,'members',$myDataClass->tableID("tbl_service"));
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
	$intReturn = $myConfigClass->createConfig("tbl_servicegroup",0);
	$chkModus  = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$intReturn = $myDataClass->dataDeleteSimple("tbl_servicegroup",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$intReturn = $myDataClass->dataCopySimple("tbl_servicegroup",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_servicegroup WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";	
	$chkModus      = "add";
}
// Statusmitteilungen setzen
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $myDataClass->strDBMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myConfigClass->lastModified("tbl_servicegroup",$strLastModified,$strFileDate,$strOld);
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm2']." -> ".$LANG['menu']['item_admsub9']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['servicegroups']);
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if ($chkModus == "add") {	
	// Servicegruppenfelder füllen
	$strSQL    = "SELECT DISTINCT tbl_service.id AS service_id, tbl_host.id AS host_id, tbl_service.host_name AS service_host_id, 
						 tbl_service.hostgroup_name AS service_hostgroup_id, tbl_service.service_description, tbl_host.host_name AS hostname 
				  FROM tbl_service 
				  LEFT JOIN tbl_relation ON tbl_service.id = tbl_relation.tbl_A_id
				  LEFT JOIN tbl_host ON tbl_relation.tbl_B_id = tbl_host.id
				  WHERE ((tbl_service.host_name = 1 AND tbl_relation.tbl_A = ".$myDataClass->tableID("tbl_service")."
				        AND tbl_relation.tbl_B = ".$myDataClass->tableID("tbl_host").") 
						OR (tbl_service.host_name = 2 AND tbl_host.id IS NULL)
						OR tbl_service.host_name = 0) AND tbl_service.active='1'
				  ORDER BY tbl_host.host_name, tbl_service.service_description";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataGroups,$intDataCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
	} else if ($intDataCount != 0) {
		for ($i=0;$i<$intDataCount;$i++) {
			if ($arrDataGroups[$i]['service_host_id'] == 2) {
				$strTxtValue = "*,".stripslashes($arrDataGroups[$i]['service_description']);
				$strIDValue  = "*.".$arrDataGroups[$i]['service_id'];
				$arrCheckServices[] = array("text" => $strTxtValue, "id" => $strIDValue);
			} else if ($arrDataGroups[$i]['service_host_id'] == 1) {
				$strTxtValue = stripslashes($arrDataGroups[$i]['hostname']).",".stripslashes($arrDataGroups[$i]['service_description']);
				$strIDValue  = $arrDataGroups[$i]['host_id'].".".$arrDataGroups[$i]['service_id'];			
				$arrCheckServices[] = array("text" => $strTxtValue, "id" => $strIDValue);	
			} else if ($arrDataGroups[$i]['service_host_id'] == 0) {
				// Host via Hostgroup bestimmen
				if ($arrDataGroups[$i]['service_hostgroup_id'] == 2) {
					$arrHostgroup[] = "*";
					$booReturn = $myDataClass->findHostsByHostgroup($arrHostgroup,$arrResult);
					foreach($arrResult AS $elem) {
						$strTxtValue = stripslashes($elem['hostname']).",".stripslashes($arrDataGroups[$i]['service_description']);
						$strIDValue  = $elem['id'].".".$arrDataGroups[$i]['service_id'];
						$arrCheckServices[] = array("text" => $strTxtValue, "id" => $strIDValue);
					}
				} else if ($arrDataGroups[$i]['service_hostgroup_id'] == 1) {
					$strSQLHostgroup = "SELECT tbl_hostgroup.id FROM tbl_service
										LEFT JOIN tbl_relation ON tbl_service.id = tbl_relation.tbl_A_id
										LEFT JOIN tbl_hostgroup ON tbl_relation.tbl_B_id = tbl_hostgroup.id
										WHERE tbl_service.id=".$arrDataGroups[$i]['service_id']." 
										AND tbl_relation.tbl_A = ".$myDataClass->tableID("tbl_service")."
				        				AND tbl_relation.tbl_B = ".$myDataClass->tableID("tbl_hostgroup")."
										AND tbl_service.active='1'";
					$booReturn1 = $myDBClass->getDataArray($strSQLHostgroup,$arrDataHostgroup,$intDataCountHostgroup);
					$booReturn2 = $myDataClass->findHostsByHostgroup($arrDataHostgroup,$arrResult);
					foreach($arrResult AS $elem) {
						$strTxtValue = stripslashes($elem['host_name']).",".stripslashes($arrDataGroups[$i]['service_description']);
						$strIDValue  = $elem['id'].".".$arrDataGroups[$i]['service_id'];
						$arrCheckServices[] = array("text" => $strTxtValue, "id" => $strIDValue);
					}					
				}
			}
		}
		// doppelte Arrayeinträge entfernen
		$arrCheck[] = "";
		foreach ($arrCheckServices AS $elem) {
			if (!in_array($elem['text'],$arrCheck)) {
				$arrNewServices[] = array("text" => $elem['text'], "id" => $elem['id']);
				$arrCheck[] = $elem['text'];
			}
		}
		$arrCheckServices = $arrNewServices;
		// Array sortiren und ausgeben
		asort($arrCheckServices);
		reset($arrCheckServices);
		// Daten auswählen
		if ($chkSelModify == "modify") {
			$strSQL = "SELECT tbl_B1_id, tbl_B2_id FROM tbl_relation_special
					   WHERE tbl_A=".$myDataClass->tableID("tbl_servicegroup")." AND tbl_B1=".$myDataClass->tableID("tbl_host")." 
							 AND tbl_B2=".$myDataClass->tableID("tbl_service")." AND tbl_A_field='members' 
							 AND tbl_A_id=".$arrModifyData['id'];
			$booReturn = $myDBClass->getDataArray($strSQL,$arrSelect,$intDataCount);
			if ($intDataCount != 0) {
				foreach($arrSelect AS $elem) {
					if ($elem['tbl_B1_id'] == 0) $elem['tbl_B1_id'] = "*";
					$arrSelected[] = $elem['tbl_B1_id'].".".$elem['tbl_B2_id'];
				}
			}
		}
		foreach($arrCheckServices AS $key) {
			$conttp->setVariable("DAT_SERVICES",$key['text']);
			$conttp->setVariable("DAT_SERVICES_ID",$key['id']);
			if (($chkSelModify == "modify") && ($intDataCount != 0) && (in_array($key['id'],$arrSelected))) {
				 $conttp->setVariable("DAT_SERVICES_SEL","selected");
			}
			$conttp->parse("services");			
		}
	} else {
		$strDBWarning .= $LANG['admintable']['warn_service']."<br>";
	}		
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
		// Prüfen, ob dieser Eintrag in einer anderen Konfiguration verwendet wird
		if ($myDataClass->checkMustdata("tbl_servicegroup",$arrModifyData['id'],$arrInfo) != 0) {
			$conttp->setVariable("ACT_DISABLED","disabled");
			$conttp->setVariable("ACTIVE","1");
			$conttp->setVariable("CHECK_MUST_DATA","<span class=\"dbmessage\">".$LANG['admintable']['noactivate']."</span>");
		}  
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
	$mastertp->setVariable("FIELD_1",$LANG['admintable']['servicegroup']);
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['friendly']);	
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);	
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_servicegroup");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_servicegroup";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
	// Datensätze holen
	$strSQL    = "SELECT id, servicegroup_name, alias, active FROM tbl_servicegroup 
				  ORDER BY servicegroup_name LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
			$mastertp->setVariable("DATA_FIELD_1",stripslashes($arrDataLines[$i]['servicegroup_name']));
			$mastertp->setVariable("DATA_FIELD_2",stripslashes($arrDataLines[$i]['alias']));
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