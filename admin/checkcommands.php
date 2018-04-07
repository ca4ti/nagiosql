<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project   : NagiosQL
// Component : Admin command definitions
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2010-10-25 15:45:55 +0200 (Mo, 25 Okt 2010) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.0.4
// Revision  : $LastChangedRevision: 827 $
//
///////////////////////////////////////////////////////////////////////////////
// 
// Variabeln deklarieren
// =====================
$intMain 		= 4;
$intSub  		= 4;
$intMenu        = 2;
$preContent     = "admin/checkcommands.tpl.htm";
$intCount		= 0;
$strMessage		= "";
//
// Vorgabedatei einbinden
// ======================
$preAccess		= 1;
$preFieldvars 	= 1;

require("../functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkInsName 	= isset($_POST['tfName']) 			? $_POST['tfName'] 			: "";
$chkInsCommand 	= isset($_POST['tfCommand']) 		? $_POST['tfCommand'] 		: "";
$chkInsType 	= isset($_POST['selCommandType']) 	? $_POST['selCommandType'] 	: 0;
//
// Datenbankeintrag vorbereiten bei Sonderzeichen
// ==============================================
if (ini_get("magic_quotes_gpc") == 0) {
	$chkInsName 	= addslashes($chkInsName);
	$chkInsCommand  = addslashes($chkInsCommand);
}
//
// Daten verarbeiten
// =================
if (($chkModus == "insert") || ($chkModus == "modify")) {
	// Daten Einfügen oder Aktualisieren
	if ($hidActive == 1) $chkActive = 1;
	$strSQLx = "tbl_command SET command_name='$chkInsName', command_line='$chkInsCommand', command_type=$chkInsType,
				active='$chkActive', config_id=$chkDomainId, last_modified=NOW()";
	if ($chkModus == "insert") {
		$strSQL = "INSERT INTO ".$strSQLx; 
	} else {
		$strSQL = "UPDATE ".$strSQLx." WHERE id=$chkDataId";   
	}	
	if (($chkInsName != "") && ($chkInsCommand != "")) {
		$intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
		if ($intInsert == 1) {
			$intReturn = 1;
		} else {
			if ($chkModus  == "insert") 	$myDataClass->writeLog(gettext('New command inserted:')." ".$chkInsName);
			if ($chkModus  == "modify") 	$myDataClass->writeLog(gettext('Command modified:')." ".$chkInsName);
			$intReturn = 0;
		}
	} else {
		$strMessage    .= gettext('Database entry failed! Not all necessary data filled in!');
	}
	$chkModus = "display";
}  else if ($chkModus == "make") {
	// Konfigurationsdatei schreiben
	$intReturn = $myConfigClass->createConfig("tbl_command",0);
	$chkModus  = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "info")) {
	// Konfigurationsdatei schreiben
	$intReturn  = $myDataClass->infoRelation("tbl_command",$chkListId,"command_name");
	$strMessage	= $myDataClass->strDBMessage;
	$intReturn  = 0;
	$chkModus   = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
	// Gewählte Datensätze löschen
	$intReturn = $myDataClass->dataDeleteFull("tbl_command",$chkListId);
	$strMessage .= $myDataClass->strDBMessage;	
	$chkModus   = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
	// Gewählte Datensätze kopieren
	$intReturn = $myDataClass->dataCopyEasy("tbl_command","command_name",$chkListId);
	$chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
	// Daten des gewählten Datensatzes holen
	$booReturn = $myDBClass->getSingleDataset("SELECT * FROM tbl_command WHERE id=".$chkListId,$arrModifyData);
	if ($booReturn == false) $strMessage .= gettext('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
	$chkModus      = "add";
}
// Statusmitteilungen setzen
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $myDataClass->strDBMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myConfigClass->intDomainId = $_SESSION['domain'];
$myConfigClass->lastModified("tbl_command",$strLastModified,$strFileDate,$strOld);
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",gettext('Command definitions'));
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if ($chkModus == "add") {
	// Feldbeschriftungen setzen
	foreach($arrDescription AS $elem) {
		$conttp->setVariable($elem['name'],$elem['string']);
	}
	$conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
	$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
	$conttp->setVariable("LIMIT",$chkLimit);
	$conttp->setVariable("ACT_CHECKED","checked");
	$conttp->setVariable("MODUS","insert");
	$conttp->setVariable("NO_TYPE",gettext('unclassified'));
	$conttp->setVariable("CHECK_TYPE",gettext('check command'));
	$conttp->setVariable("MISC_TYPE",gettext('misc command'));
	// Im Modus "Modifizieren" die Datenfelder setzen
	if (isset($arrModifyData) && ($chkSelModify == "modify")) {
		foreach($arrModifyData AS $key => $value) {
			if (($key == "active") || ($key == "last_modified") || ($key == "access_rights")) continue;
			$conttp->setVariable("DAT_".strtoupper($key),htmlentities($value,ENT_COMPAT,'UTF-8'));
		}
		if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
		// Prüfen, ob dieser Eintrag in einer anderen Konfiguration verwendet wird
		if ($myDataClass->infoRelation("tbl_command",$arrModifyData['id'],"command_name") != 0) {
			$conttp->setVariable("ACT_DISABLED","disabled");
			$conttp->setVariable("ACT_CHECKED","checked");
			$conttp->setVariable("ACTIVE","1");
			$strInfo = "<br><span class=\"dbmessage\">".gettext('Entry cannot be activated because it is used by another configuration').":</span><br><span class=\"greenmessage\"".$myDataClass->strDBMessage."</span>";
			$conttp->setVariable("CHECK_MUST_DATA",$strInfo);
		} 
		// Befehlstyp eintragen
		if ($arrModifyData['command_type'] == 1) {$conttp->setVariable("CHECK_TYPE_SELECTED","selected");}
		if ($arrModifyData['command_type'] == 2) {$conttp->setVariable("MISC_TYPE_SELECTED","selected");}
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
	foreach($arrDescription AS $elem) {
		$mastertp->setVariable($elem['name'],$elem['string']);
	} 
	$mastertp->setVariable("FIELD_1",gettext('Command name'));
	$mastertp->setVariable("FIELD_2",gettext('Command line'));	
	$mastertp->setVariable("LIMIT",$chkLimit);
	$mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
	$mastertp->setVariable("TABLE_NAME","tbl_command");
	// Anzahl Datensätze holen
	$strSQL    = "SELECT count(*) AS number FROM tbl_command WHERE config_id=".$_SESSION['domain'];
	$booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
	if ($booReturn == false) {
		$strMessage .= gettext('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
	} else {
		$intCount = (int)$arrDataLinesCount['number'];
	}
	// Datensätze holen
	$strSQL    = "SELECT id, command_name, command_line, active FROM tbl_command 
				  WHERE config_id=".$_SESSION['domain']." ORDER BY command_name  
				  LIMIT $chkLimit,".$SETS['common']['pagelines'];
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	if ($booReturn == false) {
		$strMessage .= gettext('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";	
		$mastertp->setVariable("CELLCLASS_L","tdlb");
		$mastertp->setVariable("CELLCLASS_M","tdmb");	
		$mastertp->setVariable("DISABLED","disabled");
	} else if ($intDataCount != 0) {
		for ($i=0;$i<$intDataCount;$i++) {
			// Jede zweite Zeile einfärben (Klassen setzen)
			$strClassL = "tdld"; $strClassM = "tdmd"; $strChbClass = "checkboxline";
			if ($i%2 == 1) {$strClassL = "tdlb"; $strClassM = "tdmb"; $strChbClass = "checkbox";}
			if ($arrDataLines[$i]['active'] == 0) {$strActive = gettext('No');} else {$strActive = gettext('Yes');}	
			// Datenfelder setzen
			foreach($arrDescription AS $elem) {
				$mastertp->setVariable($elem['name'],$elem['string']);
			}		
			if (strlen($arrDataLines[$i]['command_line']) > 70) {$strAdd = " .....";} else {$strAdd = "";}
			$mastertp->setVariable("DATA_FIELD_1",htmlspecialchars($arrDataLines[$i]['command_name'],ENT_COMPAT,'UTF-8'));
			$mastertp->setVariable("DATA_FIELD_2",htmlspecialchars(substr($arrDataLines[$i]['command_line'],0,70),ENT_COMPAT,'UTF-8').$strAdd);
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
		$mastertp->setVariable("DATA_FIELD_1",gettext('No data'));
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
$mastertp->setVariable("LAST_MODIFIED",gettext('Last database update:')." <b>".$strLastModified."</b>");
$mastertp->setVariable("FILEDATE",gettext('Last change of the configuration file:')." <b>".$strFileDate."</b>");
if ($strOld != "") $mastertp->setVariable("FILEISOLD","<br><span class=\"dbmessage\">".$strOld."</span><br>");
$mastertp->parse("msgfooter");
$mastertp->show("msgfooter");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>