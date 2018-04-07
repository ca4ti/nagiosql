<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2011 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Admin timeperiod definitions
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2011-03-13 14:00:26 +0100 (So, 13. Mär 2011) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.1.1
// Revision  : $LastChangedRevision: 1058 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$preContent   = "admin/mutdialog.tpl.htm";
//
// Process post parameters
// =======================
$chkObject  	= isset($_GET['object']) 	?  htmlspecialchars($_GET['object'], ENT_QUOTES, 'utf-8')  	: "";
$intSub     	= isset($_GET['menuid']) 	?  htmlspecialchars($_GET['menuid'], ENT_QUOTES, 'utf-8') 	: 2;
$intExclude 	= isset($_GET['exclude']) ?  htmlspecialchars($_GET['exclude'], ENT_QUOTES, 'utf-8')  : 0;
//
// Include preprocessing file
// ==========================
$preAccess    = 1;
$preFieldvars = 1;
$preNoMain    = 1;
require("../functions/prepend_adm.php");
//
// Include content
// ===============
$conttp->setVariable("BASE_PATH",$SETS['path']['root']);
$conttp->setVariable("OPENER_FIELD",$chkObject);
$conttp->parse("header");
$conttp->show("header");
//
// Form
// ====
foreach($arrDescription AS $elem) {
  	$conttp->setVariable($elem['name'],$elem['string']);
}
$conttp->setVariable("OPENER_FIELD",$chkObject);
$conttp->setVariable("ACTION_INSERT",filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING));
$conttp->setVariable("IMAGE_PATH",$SETS['path']['root']."images/");
$conttp->setVariable("AVAILABLE",translate('Available'));
$conttp->setVariable("SELECTED",translate('Selected'));
if ($intExclude == 1) {
	$conttp->setVariable("DISABLE_HTML_BEGIN","<!--");
	$conttp->setVariable("DISABLE_HTML_END","-->");
}
$conttp->parse("datainsert");
$conttp->show("datainsert");
?>