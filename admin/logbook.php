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
// Zweck:	Logbuch ansehen
// Datei:	admin/logbook.php
// Version: 2.00.00 (Internal)
//
///////////////////////////////////////////////////////////////////////////////
// error_reporting(E_ALL);
// 
// Variabeln deklarieren
// =====================
$intMain 		= 7;
$intSub  		= 21;
$intMenu        = 2;
$preContent     = "admin_master.tpl.htm";
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
$chkFromLine	= isset($_GET['from_line'])	 	? $_GET['from_line']+0	: 0;
$chkDelYear 	= isset($_POST['tfYear']) 		? $_POST['tfYear'] 		: "0000";
$chkDelMonth 	= isset($_POST['tfMonth']) 		? $_POST['tfMonth'] 	: "00";
$chkDelDay 		= isset($_POST['tfDay']) 		? $_POST['tfDay'] 		: "00";
//
// Daten löschen
// =============
if (isset($_POST['submit'])) {
	$strDate = $chkDelYear."-".$chkDelMonth."-".$chkDelDay." 00:00:00";
	$strSQL  = "DELETE FROM tbl_logbook WHERE time < '$strDate'";
	$booReturn  = $myDBClass->insertData($strSQL);
	if ($booReturn == false) {
		$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
	} else {
		$strMessage .= $LANG['db']['success_del'];	
	}
}
//
// Datenbank abfragen
// ==================
$intNumRows = $myDBClass->getFieldData("SELECT count(*) FROM tbl_logbook");
$strSQL     = "SELECT DATE_FORMAT(time,'%Y-%m-%d %H:%i:%s') AS time, user, entry 
			   FROM tbl_logbook ORDER BY time DESC LIMIT $chkFromLine,20";
$booReturn  = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
if ($booReturn == false) {
	$strMessage .= $LANG['db']['dberror']."<br>".$myDBClass->strDBError."<br>";		
}
//
// HTML Template laden
// ===================
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm7']." -> ".$LANG['menu']['item_admsub21']);
$maintp->parse("header");
$maintp->show("header");
//
// Menu aufbauen
// =============
$myVisClass->getMenu($intMain,$intSub,$intMenu); 
//
// Content einbinden
// =================
$conttp->setVariable("TITLE",$LANG['title']['logbook']);
foreach($LANG['logbook'] AS $key => $value) {
	$conttp->setVariable("LANG_".strtoupper($key),$value);
}
// Legende einblenden 
if ($chkFromLine > 1) {
	$intPrevNumber = $chkFromLine - 20;
	$conttp->setVariable("LANG_PREVIOUS", "<a href=\"".$_SERVER['PHP_SELF']."?from_line=".$intPrevNumber."\"><< ".$LANG['logbook']['previous']."</a>");
} else {
	$conttp->setVariable("LANG_PREVIOUS", "");
}
if ($chkFromLine < $intNumRows-20) {
	$intNextNumber = $chkFromLine + 20;
	$conttp->setVariable("LANG_NEXT", "<a href=\"".$_SERVER['PHP_SELF']."?from_line=".$intNextNumber."\">".$LANG['logbook']['next']." >></a>");
} else {
	$conttp->setVariable("LANG_NEXT", "");
}
//Logdaten ausgeben
if ($intDataCount != 0) {
	for ($i=0;$i<$intDataCount;$i++) {
		$conttp->setVariable("DAT_TIME", $arrDataLines[$i]['time']);
		$conttp->setVariable("DAT_ACCOUNT", $arrDataLines[$i]['user']);	
		$conttp->setVariable("DAT_ACTION", $arrDataLines[$i]['entry']);	
		$conttp->parse("logdatacell");
	}
}
if ($strMessage != "") $conttp->setVariable("LOGDBMESSAGE",$strMessage);
$conttp->parse("logbooksite");
$conttp->show("logbooksite");
//
// Footer ausgeben
// ===============
$maintp->setVariable("VERSION_INFO","NagiosQL - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>