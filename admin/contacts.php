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
// Zweck:	Kontakte definieren
// Datei:	admin/contacts.php
// Version: 2.0.2 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 3;
$intSub  		= 5;
$intMenu 		= 2;
$preContent 	= "contacts.tpl.htm";
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
$chkTfName 				= isset($_POST['tfName']) 				? addslashes($_POST['tfName']) 			 : "";
$chkTfFriendly 			= isset($_POST['tfFriendly']) 			? addslashes($_POST['tfFriendly'])		 : "";
$chkSelContactGroup 	= isset($_POST['selContactGroup']) 		? $_POST['selContactGroup'] 			 : array("");
$chkSelHostPeriod 		= isset($_POST['selHostPeriod']) 		? addslashes($_POST['selHostPeriod'])	 : "";
$chkSelServicePeriod 	= isset($_POST['selServicePeriod']) 	? addslashes($_POST['selServicePeriod']) : "";
$chkSelHostCommand 		= isset($_POST['selHostCommand']) 		? $_POST['selHostCommand'] 				 : array("");
$chkSelServiceCommand 	= isset($_POST['selServiceCommand'])	? $_POST['selServiceCommand'] 			 : array("");
$chkTfEmail 			= isset($_POST['tfEmail']) 				? addslashes($_POST['tfEmail'])			 : "";
$chkTfPager 			= isset($_POST['tfPager']) 				? addslashes($_POST['tfPager'])			 : "";
$chkTfAddress1 			= isset($_POST['tfAddress1']) 			? addslashes($_POST['tfAddress1']) 		 : "";
$chkTfAddress2 			= isset($_POST['tfAddress2']) 			? addslashes($_POST['tfAddress2']) 		 : "";
$chkTfAddress3 			= isset($_POST['tfAddress3']) 			? addslashes($_POST['tfAddress3']) 		 : "";
$chkTfAddress4 			= isset($_POST['tfAddress4']) 			? addslashes($_POST['tfAddress4']) 		 : "";
$chkTfAddress5 			= isset($_POST['tfAddress5']) 			? addslashes($_POST['tfAddress5']) 		 : "";
$chkHOd					= isset($_POST['chbHOd'])				? $_POST['chbHOd'].","					 : "";
$chkHOu					= isset($_POST['chbHOu'])				? $_POST['chbHOu'].","					 : "";
$chkHOr					= isset($_POST['chbHOr'])				? $_POST['chbHOr'].","					 : "";
$chkSOw					= isset($_POST['chbSOw'])				? $_POST['chbSOw'].","					 : "";
$chkSOu					= isset($_POST['chbSOu'])				? $_POST['chbSOu'].","					 : "";
$chkSOc					= isset($_POST['chbSOc'])				? $_POST['chbSOc'].","					 : "";
$chkSOr					= isset($_POST['chbSOr'])				? $_POST['chbSOr'].","					 : "";
//
// Daten verarbeiten
// =================
$strHO = substr($chkHOd.$chkHOu.$chkHOr,0,-1);
$strSO = substr($chkSOw.$chkSOu.$chkSOc.$chkSOr,0,-1);
if (($chkSelContactGroup[0] == "")   || ($chkSelContactGroup[0] == "0"))   {$intContactGroups = 0;}  else {$intContactGroups = 1;}
if (($chkSelHostCommand[0] == "")    || ($chkSelHostCommand[0] == "0"))    {$intHostCommand = 0;}    else {$intHostCommand = 1;}
if (($chkSelServiceCommand[0] == "") || ($chkSelServiceCommand[0] == "0")) {$intServiceCommand = 0;} else {$intServiceCommand = 1;}
// Datein einfügen oder modifizieren
if (($chkModus == "insert") || ($chkModus == "modify")) {
	if ($hidActive == 1) $chkActive = 1;
	$strSQLx = "tbl_contact SET contact_name='$chkTfName', alias='$chkTfFriendly', contactgroups=$intContactGroups, 
				host_notification_period='$chkSelHostPeriod', service_notification_period='$chkSelServicePeriod', 
				host_notification_options='$strHO', service_notification_options='$strSO', 
				host_notification_commands=$intHostCommand, service_notification_commands=$intServiceCommand, 
				email='$chkTfEmail', pager='$chkTfPager', address1='$chkTfAddress1', address2='$chkTfAddress2', 
				address3='$chkTfAddress3', address4='$chkTfAddress4', address5='$chkTfAddress5',active='$chkActive', 
				last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL = "INSERT INTO ".$strSQLx; 
	} else {
		$strSQL = "UPDATE ".$strSQLx." WHERE id=$chkDataId";   
	}	
	if (($chkTfName != "") && ($chkTfFriendly != "") && ($chkSelHostPeriod != "") && 
		($chkSelServicePeriod != "") && ($strHO != "") && ($strSO != "")) {	
		$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		if ($intInsert == 1) {
			$intReturn = 1;
		} else {
			if ($chkModus  == "insert") 	$myDataClass->writeLog($LANG['logbook']['newcontact']." ".$chkTfName);
			if ($chkModus  == "modify") 	$myDataClass->writeLog($LANG['logbook']['modifycontact']." ".$chkTfName);
			//
			// Relationen eintragen/updaten
			// ============================
			$intTableA = $myDataClass->tableID("tbl_contact");
			if ($chkModus == "insert") {
				if ($intContactGroups  == 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_contactgroup"),$intInsertId,'contactgroups',$chkSelContactGroup);
				if ($intHostCommand    == 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_misccommand"),$intInsertId,'host_notification_commands',$chkSelHostCommand);
				if ($intServiceCommand == 1) $myDataClass->dataInsertRelation($intTableA,$myDataClass->tableID("tbl_misccommand"),$intInsertId,'service_notification_commands',$chkSelServiceCommand);
			} else if ($chkModus == "modify") {		
				if ($intContactGroups == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_contactgroup"),$chkDataId,'contactgroups',$chkSelContactGroup);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_contactgroup"),$chkDataId,'contactgroups');
				}
				if ($intHostCommand == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_misccommand"),$chkDataId,'host_notification_commands',$chkSelHostCommand);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_misccommand"),$chkDataId,'host_notification_commands');			
				}
				if ($intServiceCommand == 1) {
					$myDataClass->dataUpdateRelation($intTableA,$myDataClass->tableID("tbl_misccommand"),$chkDataId,'service_notification_commands',$chkSelServiceCommand);
				} else {
					$myDataClass->dataDeleteRelation($intTableA,$myDataClass->tableID("tbl_misccommand"),$chkDataId,'service_notification_commands');			
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
	$intReturn = $myConfigClass->createConfig("tbl_contact",0);
	$chkModus  = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$intReturn = $myDataClass->dataDeleteSimple("tbl_contact",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$intReturn = $myDataClass->dataCopySimple("tbl_contact",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_contact WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	$chkModus      = "add";
}
// Statusmitteilungen setzen
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $myDataClass->strDBMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myConfigClass->lastModified("tbl_contact",$strLastModified,$strFileDate,$strOld);
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm3']." -> ".$LANG['menu']['item_admsub5']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['contacts']);
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if ($chkModus == "add") {
	// Klassenvariabeln definieren
	$myVisClass->resTemplate     =& $conttp;
	$myVisClass->strTempValue1   = $chkSelModify;
	$myVisClass->intTabA   	     = $myDataClass->tableID("tbl_contact");
	if (isset($arrModifyData)) {
		$myVisClass->arrWorkdata = $arrModifyData;
		$myVisClass->intTabA_id  = $arrModifyData['id'];
	} else {
		$myVisClass->intTabA_id  = 0;
	}
	// Zeitperiodenfelder füllem
	$intReturn = 0;
	$intReturn = $myVisClass->parseSelectNew('tbl_timeperiod','timeperiod_name','DAT_TIMEPERIOD','timeperiodgroup1','host_notification_period');
	$intReturn = $myVisClass->parseSelectNew('tbl_timeperiod','timeperiod_name','DAT_TIMEPERIOD','timeperiodgroup2','service_notification_period');
	if ($intReturn != 0) $strDBWarning .= $LANG['admintable']['warn_timeperiod']."<br>";
	// Kommandonamenfelder füllen
	$myVisClass->parseSelectNew('tbl_misccommand','command_name','DAT_COMMAND1','commandgroup1','host_notification_commands',2,0);
	$myVisClass->parseSelectNew('tbl_misccommand','command_name','DAT_COMMAND2','commandgroup2','service_notification_commands',2,0);
	// Kontaktgruppenfeld setzen
	$myVisClass->parseSelectNew('tbl_contactgroup','contactgroup_name','DAT_CONTACTGROUP','contactgroup','contactgroups',2,1);
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
		if ($myDataClass->checkMustdata("tbl_contact",$arrModifyData['id'],$arrInfo) != 0) {
			$conttp->setVariable("ACT_DISABLED","disabled");
			$conttp->setVariable("ACTIVE","1");
			$conttp->setVariable("CHECK_MUST_DATA","<span class=\"dbmessage\">".$LANG['admintable']['noactivate']."</span>");
		}   
		// Optionskästchen verarbeiten
		foreach(explode(",",$arrModifyData['host_notification_options']) AS $elem) {
			$conttp->setVariable("DAT_HO".strtoupper($elem)."_CHECKED","checked");
		}
		foreach(explode(",",$arrModifyData['service_notification_options']) AS $elem) {
			$conttp->setVariable("DAT_SO".strtoupper($elem)."_CHECKED","checked");
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
	$mastertp->setVariable("FIELD_1",$LANG['admintable']['contactname']);
	$mastertp->setVariable("FIELD_2",$LANG['admintable']['friendly']);
	$mastertp->setVariable("DELETE",$LANG['admintable']['delete']);
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("DUPLICATE",$LANG['admintable']['duplicate']);	
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_contact");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_contact";
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
	// Datensätze holen
	$strSQL    = "SELECT id, contact_name, alias, active FROM tbl_contact 
				  ORDER BY contact_name LIMIT $chkLimit,".$SETS['common']['pagelines'];
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
			$mastertp->setVariable("DATA_FIELD_1",stripslashes($arrDataLines[$i]['contact_name']));
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
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org'>NagiosQL</a> - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>