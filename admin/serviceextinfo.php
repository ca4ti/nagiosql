<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2007 by Martin Willisegger / nagiosql_v2@wizonet.ch
//
// Projekt:	NagiosQL Applikation
// Author :	Martin Willisegger
// Datum:	12.03.2007
// Zweck:	Service Zusatzinformationen definieren
// Datei:	admin/serviceextinfo.php
// Version: 2.0.2 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 5;
$intSub  		= 15;
$intMenu 		= 2;
$preContent 	= "serviceextinfo.tpl.htm";
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
$chkSelHost 		= isset($_POST['selHost']) 			? $_POST['selHost'] 					: 0;
$chkSelService	 	= isset($_POST['selService']) 		? $_POST['selService']					: 0;
$chkTfNotes		 	= isset($_POST['tfNotes']) 			? addslashes($_POST['tfNotes']) 		: "";
$chkTfNotesURL		= isset($_POST['tfNotesURL']) 		? addslashes($_POST['tfNotesURL']) 		: "";
$chkTfActionURL 	= isset($_POST['tfActionURL']) 		? addslashes($_POST['tfActionURL']) 	: "";
$chkTfIconImage		= isset($_POST['tfIconImage']) 		? addslashes($_POST['tfIconImage']) 	: "";
$chkTfIconImageAlt 	= isset($_POST['tfIconImageAlt']) 	? addslashes($_POST['tfIconImageAlt']) 	: "";
//
// Daten verarbeiten
// =================
if (($chkModus == "insert") || ($chkModus == "modify")) {
	// Daten Einfügen oder Aktualisieren
	$strSQLx = "tbl_serviceextinfo SET host_name='$chkSelHost', service_description='$chkSelService', notes='$chkTfNotes', 
				notes_url='$chkTfNotesURL', action_url='$chkTfActionURL', icon_image='$chkTfIconImage', 
				icon_image_alt='$chkTfIconImageAlt', active='$chkActive', last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL = "INSERT INTO ".$strSQLx; 
	} else {
		$strSQL = "UPDATE ".$strSQLx." WHERE id=$chkDataId";   
	}	
	if (($chkSelHost != "") && ($chkSelService != "")) {
		$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		if ($intInsert == 1) {
			$intReturn = 1;
		} else {
			if ($chkModus  == "insert") 	$myDataClass->writeLog($LANG['logbook']['newservext']." ".$chkSelHost."::".$chkSelService);
			if ($chkModus  == "modify") 	$myDataClass->writeLog($LANG['logbook']['modifyservext']." ".$chkSelHost."::".$chkSelService);
			$intReturn = 0;
		}
	} else {
		$strMessage  = $LANG['db']['datamissing'];
	}
	$chkModus = "display";
}  else if ($chkModus == "make") {
	// Konfigurationsdatei schreiben
	$intReturn = $myConfigClass->createConfig("tbl_serviceextinfo",0);
	$chkModus  = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$intReturn = $myDataClass->dataDeleteSimple("tbl_serviceextinfo",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$intReturn = $myDataClass->dataCopySimple("tbl_serviceextinfo",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_serviceextinfo WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	$chkSelHost	= $arrModifyData['host_name'];
	$chkModus = "add";
}
// Statusmitteilungen setzen
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $myDataClass->strDBMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myConfigClass->lastModified("tbl_serviceextinfo",$strLastModified,$strFileDate,$strOld);
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm5']." -> ".$LANG['menu']['item_admsub15']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['serviceextinfo']);
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if (($chkModus == "add") || ($chkModus == "refresh")) {
	// Klassenvariabeln definieren
	$myVisClass->resTemplate     =& $conttp;
	$myVisClass->strTempValue1   = $chkSelModify;	
	$myVisClass->intTabA   	     = $myDataClass->tableID("tbl_serviceextinfo");	
	$arrHosts[]					 = $chkSelHost;
	if (isset($arrModifyData)) {
		$myVisClass->arrWorkdata = $arrModifyData;
		$myVisClass->intTabA_id  = $arrModifyData['id'];
	} else {
		$myVisClass->intTabA_id  = 0;
	}	
	// Hostfelder füllen
	$intReturn = 0;
	$intReturn = $myVisClass->parseSelectNew('tbl_host','host_name','DAT_HOST','host','host_name',1,0,0,$arrHosts);
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_host']."<br>";	
	// Servicefelder füllen
	if ($chkSelHost == 0) $chkSelHost = $myVisClass->strTempValue2;
	$strSQL    = "SELECT DISTINCT tbl_service.id, tbl_service.service_description FROM tbl_service 
				  LEFT JOIN tbl_relation ON tbl_service.id = tbl_relation.tbl_A_id
				  LEFT JOIN tbl_host ON tbl_relation.tbl_B_id = tbl_host.id
				  WHERE (tbl_service.host_name = 1 AND tbl_host.id = $chkSelHost AND 
				  	    tbl_relation.tbl_A = ".$myDataClass->tableID("tbl_service")."
				        AND tbl_relation.tbl_B = ".$myDataClass->tableID("tbl_host").") 
						OR (tbl_service.host_name = 2 AND tbl_host.id IS NULL)
						OR tbl_service.host_name = 0
				  ORDER BY tbl_service.service_description";   
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataGroups,$intDataCount);
	if ($booReturn && ($intDataCount != 0)) {
		foreach($arrDataGroups AS $key) {
			$conttp->setVariable("DAT_SERVICE",$key['service_description']);
			$conttp->setVariable("DAT_SERVICE_ID",$key['id']);
			if (isset($arrModifyData) && ($key['id'] == $arrModifyData['service_description'])) {
				$conttp->setVariable("DAT_SERVICE_SEL","selected");
			}
			$conttp->parse("service");
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
	if ($chkModus == "refresh") {
		$conttp->setVariable("DAT_NOTES",$chkTfNotes);
		$conttp->setVariable("DAT_NOTES_URL",$chkTfNotesURL);
		$conttp->setVariable("DAT_ACTION_URL",$chkTfActionURL);
		$conttp->setVariable("DAT_ICON_IMAGE",$chkTfIconImage);
		$conttp->setVariable("DAT_ICON_IMAGE_ALT",$chkTfIconImageAlt);
		if ($chkActive != 1) $conttp->setVariable("ACT_CHECKED","");
		if ($chkDataId != 0) {
			$conttp->setVariable("MODUS","modify");
			$conttp->setVariable("DAT_ID",$chkDataId);
		}
	// Im Modus "Modifizieren" die Datenfelder setzen
	} else if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		foreach($arrModifyData AS $key => $value) {
			if (($key == "active") || ($key == "last_modified") || ($key == "access_rights")) continue;
			$conttp->setVariable("DAT_".strtoupper($key),htmlspecialchars(stripslashes($value)));
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
	$mastertp->setVariable("FIELD_1",$LANG['admintable']['hostname']);
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['service']);	
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);	
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_serviceextinfo");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_serviceextinfo";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
	// Datensätze holen
	$strSQL    = "SELECT tbl_serviceextinfo.id, tbl_host.host_name, tbl_service.service_description, tbl_serviceextinfo.active 
				  FROM tbl_serviceextinfo 
				  LEFT JOIN tbl_host ON tbl_serviceextinfo.host_name = tbl_host.id 
				  LEFT JOIN tbl_service ON tbl_serviceextinfo.service_description = tbl_service.id
				  ORDER BY tbl_host.host_name,tbl_service.service_description, active LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
			if ($arrDataLines[$i]['host_name'] != "") {
				$mastertp->setVariable("DATA_FIELD_1",stripslashes($arrDataLines[$i]['host_name']));
			} else {
				$mastertp->setVariable("DATA_FIELD_1","NOT DEFINED - ".$arrDataLines[$i]['id']);
			}
			$mastertp->setVariable("DATA_FIELD_2",stripslashes($arrDataLines[$i]['service_description']));
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