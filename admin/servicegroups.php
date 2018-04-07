<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2008, 2009 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Admin servicegroup definition
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2009-04-28 15:02:27 +0200 (Di, 28. Apr 2009) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.0.3
// Revision  : $LastChangedRevision: 708 $
// SVN-ID    : $Id: servicegroups.php 708 2009-04-28 13:02:27Z rouven $
//
///////////////////////////////////////////////////////////////////////////////
//
// Variabeln deklarieren
// =====================
$intMain      = 2;
$intSub       = 9;
$intMenu      = 2;
$preContent   = "admin/servicegroups.tpl.htm";
$strDBWarning = "";
$intCount     = 0;
$strMessage   = "";
//
// Vorgabedatei einbinden
// ======================
$preAccess    = 1;
$preFieldvars = 1;
require("../functions/prepend_adm.php");
$myConfigClass->getConfigData("version",$intVersion);
//
// Übergabeparameter
// =================
$chkTfName                  = isset($_POST['tfName'])         			? $_POST['tfName']         			: "";
$chkTfFriendly              = isset($_POST['tfFriendly'])     			? $_POST['tfFriendly']     			: "";
$chkSelMembers              = isset($_POST['selMembers'])     			? $_POST['selMembers']     			: array("");
$chkSelServicegroupMembers  = isset($_POST['selServicegroupMembers']) 	? $_POST['selServicegroupMembers'] 	: array("");
$chkTfNotes                 = isset($_POST['tfNotes'])        			? $_POST['tfNotes']        			: "";
$chkTfNotesURL              = isset($_POST['tfNotesURL'])     			? $_POST['tfNotesURL']     			: "";
$chkTfActionURL             = isset($_POST['tfActionURL'])    			? $_POST['tfActionURL']    			: "";
//
// Datenbankeintrag vorbereiten bei Sonderzeichen
// ==============================================
if ($SETS['db']['magic_quotes'] == 0) {
  $chkTfName    = addslashes($chkTfName);
  $chkTfFriendly  = addslashes($chkTfFriendly);
  $chkTfNotes   = addslashes($chkTfNotes);
  $chkTfNotesURL  = addslashes($chkTfNotesURL);
  $chkTfActionURL = addslashes($chkTfActionURL);
}
//
// Daten verarbeiten
// =================
if (($chkSelMembers[0] == "")         		|| ($chkSelMembers[0] == "0"))        		{$intSelMembers = 0;}         		else {$intSelMembers = 1;}
if (($chkSelServicegroupMembers[0] == "")   || ($chkSelServicegroupMembers[0] == "0"))  {$intSelServicegroupMembers = 0;}   else {$intSelServicegroupMembers = 1;}
// Datein einfügen oder modifizieren
if (($chkModus == "insert") || ($chkModus == "modify")) {
  if ($hidActive == 1) $chkActive = 1;
  $strSQLx = "`tbl_servicegroup` SET `servicegroup_name`='$chkTfName', `alias`='$chkTfFriendly', `members`=$intSelMembers,
        `servicegroup_members`=$intSelServicegroupMembers, `notes`='$chkTfNotes', `notes_url`='$chkTfNotesURL',
        `action_url`='$chkTfActionURL', `active`='$chkActive', `config_id`=$chkDomainId, `last_modified`=NOW()";
  if ($chkModus == "insert") {
    $strSQL = "INSERT INTO ".$strSQLx;
  } else {
    $strSQL = "UPDATE ".$strSQLx." WHERE `id`=$chkDataId";
  }
  if (($chkTfName != "") && ($chkTfFriendly != "") && (($intSelMembers != 0) || ($intVersion == 3))) {
    $intInsert = $myDataClass->dataInsert($strSQL,$intInsertId);
    if ($chkModus == "insert") {
      $chkDataId = $intInsertId;
    }
    if ($intInsert == 1) {
      $intReturn = 1;
    } else {
      if ($chkModus  == "insert")   $myDataClass->writeLog(gettext('New service group inserted:')." ".$chkTfName);
      if ($chkModus  == "modify")   $myDataClass->writeLog(gettext('Service group modified:')." ".$chkTfName);
      //
      // Relationen eintragen/updaten
      // ============================
      if ($chkModus == "insert") {
        if ($intSelMembers  == 1)				$myDataClass->dataInsertRelation("tbl_lnkServicegroupToService",$chkDataId,$chkSelMembers,1);
        if ($intSelServicegroupMembers  == 1)   $myDataClass->dataInsertRelation("tbl_lnkServicegroupToServicegroup",$chkDataId,$chkSelServicegroupMembers);
	  } else if ($chkModus == "modify") {
        if ($intSelMembers == 1) {
          $myDataClass->dataUpdateRelation("tbl_lnkServicegroupToService",$chkDataId,$chkSelMembers,1);
        } else {
          $myDataClass->dataDeleteRelation("tbl_lnkServicegroupToService",$chkDataId);
        }
        if ($intSelServicegroupMembers == 1) {
          $myDataClass->dataUpdateRelation("tbl_lnkServicegroupToServicegroup",$chkDataId,$chkSelServicegroupMembers);
        } else {
          $myDataClass->dataDeleteRelation("tbl_lnkServicegroupToServicegroup",$chkDataId);
        }
      }
      $intReturn = 0;
    }
  } else {
    $strMessage .= gettext('Database entry failed! Not all necessary data filled in!');
  }
  $chkModus = "display";
}  else if ($chkModus == "make") {
  // Konfigurationsdatei schreiben
  $intReturn = $myConfigClass->createConfig("tbl_servicegroup",0);
  $chkModus  = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "info")) {
  // Konfigurationsdatei schreiben
  $intReturn  = $myDataClass->infoRelation("tbl_servicegroup",$chkListId,"servicegroup_name");
  $strMessage = $myDataClass->strDBMessage;
  $intReturn  = 0;
  $chkModus   = "display";
}  else if (($chkModus == "checkform") && ($chkSelModify == "delete")) {
  // Gewählte Datensätze löschen
  $intReturn = $myDataClass->dataDeleteFull("tbl_servicegroup",$chkListId);
  $strMessage .= $myDataClass->strDBMessage;
  $chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "copy")) {
  // Gewählte Datensätze kopieren
  $intReturn = $myDataClass->dataCopyEasy("tbl_servicegroup","servicegroup_name",$chkListId);
  $chkModus  = "display";
} else if (($chkModus == "checkform") && ($chkSelModify == "modify")) {
  // Daten des gewählten Datensatzes holen
  $booReturn = $myDBClass->getSingleDataset("SELECT * FROM `tbl_servicegroup` WHERE `id`=".$chkListId,$arrModifyData);
  if ($booReturn == false) $strMessage .= gettext('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
  $chkModus      = "add";
}
// Statusmitteilungen setzen
if (isset($intReturn) && ($intReturn == 1)) $strMessage = $myDataClass->strDBMessage;
if (isset($intReturn) && ($intReturn == 0)) $strMessage = "<span class=\"greenmessage\">".$myDataClass->strDBMessage."&nbsp;</span>";
//
// Letzte Datenbankänderung und Filedatum
// ======================================
$myConfigClass->lastModified("tbl_servicegroup",$strLastModified,$strFileDate,$strOld);
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",gettext('Define service groups (servicegroups.cfg)'));
$conttp->parse("header");
$conttp->show("header");
//
// Eingabeformular
// ===============
if ($chkModus == "add") {
  // Serviceauswahlfeld füllen
  $intReturn = 0;
  if (isset($arrModifyData['members'])) {$intFieldId = $arrModifyData['members'];} else {$intFieldId = 0;}
  $intReturn = $myVisClass->parseSelect('tbl_service','service_description','DAT_SERVICES','services',$conttp,$chkListId,'tbl_lnkServicegroupToService',$intFieldId,0,0,6);
  if (($intReturn != 0) && ($intVersion != 3)) $strDBWarning .= gettext('Attention, no services defined!')."<br>";
  // Servicegruppenmitglieder
  if (isset($arrModifyData['servicegroup_members'])) {$intFieldId = $arrModifyData['servicegroup_members'];} else {$intFieldId = 0;}
  if (isset($arrModifyData['id'])) {$intDataKey = $arrModifyData['id'];} else {$intDataKey = 0;}
  $intReturn = $myVisClass->parseSelect('tbl_servicegroup','servicegroup_name','DAT_SERVICEGROUP','servicegroupmembers',$conttp,$chkListId,'tbl_lnkServicegroupToServicegroup',$intFieldId,0,$intDataKey);
  // Feldbeschriftungen setzen
  foreach($arrDescription AS $elem) {
    $conttp->setVariable($elem['name'],$elem['string']);
  }
  $conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
  $conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
  $conttp->setVariable("LIMIT",$chkLimit);
  if ($strDBWarning != "") $conttp->setVariable("WARNING",$strDBWarning.gettext('Saving not possible!'));
  $conttp->setVariable("ACT_CHECKED","checked");
  $conttp->setVariable("MODUS","insert");
  if ($SETS['common']['seldisable'] == 1)$conttp->setVariable("SELECT_FIELD_DISABLED","disabled");
  // Versionsdifferenzen festlegen
  if ($intVersion == 3) {
    $conttp->setVariable("CLASS_NAME_20","elementHide");
    $conttp->setVariable("CLASS_NAME_30","elementShow");
  } else {
    $conttp->setVariable("CLASS_NAME_20","elementShow");
    $conttp->setVariable("CLASS_NAME_30","elementHide");
    $conttp->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
    $conttp->setVariable("MUST_20_STAR","*");
    $conttp->setVariable("MEMBER_20_MUST","selMembers,");

  }
  // Im Modus "Modifizieren" die Datenfelder setzen
  if (isset($arrModifyData) && ($chkSelModify == "modify")) {
    foreach($arrModifyData AS $key => $value) {
      if (($key == "active") || ($key == "last_modified") || ($key == "access_rights")) continue;
      $conttp->setVariable("DAT_".strtoupper($key),htmlentities($value));
    }
    if ($arrModifyData['active'] != 1) $conttp->setVariable("ACT_CHECKED","");
    // Prüfen, ob dieser Eintrag in einer anderen Konfiguration verwendet wird
    if ($myDataClass->infoRelation("tbl_servicegroup",$arrModifyData['id'],"servicegroup_name") != 0) {
      $conttp->setVariable("ACT_DISABLED","disabled");
      $conttp->setVariable("ACT_CHECKED","checked");
      $conttp->setVariable("ACTIVE","1");
      $strInfo = "<br><span class=\"dbmessage\">".gettext('Entry cannot be activated because it is used by another configuration').":</span><br><span class=\"greenmessage\">".$myDataClass->strDBMessage."</span>";
      $conttp->setVariable("CHECK_MUST_DATA",$strInfo);
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
  foreach($arrDescription AS $elem) {
    $mastertp->setVariable($elem['name'],$elem['string']);
  }
  $mastertp->setVariable("FIELD_1",gettext('Service group'));
  $mastertp->setVariable("FIELD_2",gettext('Description'));
  $mastertp->setVariable("LIMIT",$chkLimit);
  $mastertp->setVariable("ACTION_MODIFY",$_SERVER['PHP_SELF']);
  $mastertp->setVariable("TABLE_NAME","tbl_servicegroup");
  // Anzahl Datensätze holen
  $strSQL    = "SELECT count(*) AS `number` FROM `tbl_servicegroup` WHERE `config_id`=$chkDomainId";
  $booReturn = $myDBClass->getSingleDataset($strSQL,$arrDataLinesCount);
  if ($booReturn == false) {
    $strMessage .= gettext('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
  } else {
    $intCount = (int)$arrDataLinesCount['number'];
  }
  // Datensätze holen
  $strSQL    = "SELECT `id`, `servicegroup_name`, `alias`, `active` FROM `tbl_servicegroup` WHERE `config_id`=$chkDomainId
          ORDER BY `servicegroup_name` LIMIT $chkLimit,".$SETS['common']['pagelines'];
  $booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
  if ($booReturn == false) {
    $strMessage .= gettext('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
    $mastertp->setVariable("CELLCLASS_L","tdlb");
    $mastertp->setVariable("CELLCLASS_M","tdmb");
    $mastertp->setVariable("DISABLED","disabled");
    $mastertp->setVariable("DATA_FIELD_1",gettext('No data'));
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
      $mastertp->setVariable("DATA_FIELD_1",htmlspecialchars($arrDataLines[$i]['servicegroup_name']));
      $mastertp->setVariable("DATA_FIELD_2",htmlspecialchars($arrDataLines[$i]['alias']));
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
if ($strOld != "") $mastertp->setVariable("FILEISOLD","<br><span class=\"dbmessage\">".$strOld."&nbsp;</span><br>");
$mastertp->parse("msgfooter");
$mastertp->show("msgfooter");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>