<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL 2005
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005 by Martin Willisegger / nagios.ql2005@wizonet.ch
//
// Projekt:	NagiosQL Applikation
// Author :	Martin Willisegger
// Datum:	30.03.2005
// Zweck:	Logbuch ansehen
// Datei:	admin/logbook.php
// Version:	1.02
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
$setFileVersion = "1.02";
$strDBWarning	= "";
$strMessage		= "";
//
// Vorgabedatei einbinden
// ======================
$preRights 	= "admin3";
$SETS 		= parse_ini_file("../config/settings.ini",TRUE);
require($SETS['path']['physical']."functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkFromLine	= isset($_GET['from_line'])	 ? $_GET['from_line']+0	: 0;
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
$maintp->setVariable("POSITION",$LANG['position']['admin']." -> ".$LANG['menu']['item_adm3']." -> ".$LANG['menu']['item_admsub4']);
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
$maintp->setVariable("VERSION_INFO","NagiosQL 2005 - Version: $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>