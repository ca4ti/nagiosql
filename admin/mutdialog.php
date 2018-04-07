<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project   : NagiosQL
// Component : Admin timeperiod definitions
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
$preContent   = "admin/mutdialog.tpl.htm";
//
// Vorgabedatei einbinden
// ======================
$preAccess    = 1;
$preFieldvars = 1;
$intSub       = 2;
$preNoMain    = 1;
require("../functions/prepend_adm.php");
//
// Übergabeparameter
// =================
$chkObject  = isset($_GET['object']) ?  $_GET['object'] : "";
//
// Content einbinden
// =================
$conttp->setVariable("BASE_PATH",$SETS['path']['root']);
$conttp->setVariable("OPENER_FIELD",$chkObject);
$conttp->parse("header");
$conttp->show("header");
//
// Formular
// ========
// Feldbeschriftungen setzen
foreach($arrDescription AS $elem) {
  $conttp->setVariable($elem['name'],$elem['string']);
}
$conttp->setVariable("OPENER_FIELD",$chkObject);
$conttp->setVariable("ACTION_INSERT",$_SERVER['PHP_SELF']);
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->setVariable("AVAILABLE",gettext('Available'));
$conttp->setVariable("SELECTED",gettext('Selected'));
$conttp->parse("datainsert");
$conttp->show("datainsert");
?>