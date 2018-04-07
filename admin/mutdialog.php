<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2012 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Admin timeperiod definitions
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2012-02-27 13:01:17 +0100 (Mon, 27 Feb 2012) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1257 $
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
$conttp->setVariable("BASE_PATH",$_SESSION['SETS']['path']['base_url']);
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
$conttp->setVariable("IMAGE_PATH",$_SESSION['SETS']['path']['base_url']."images/");
$conttp->setVariable("AVAILABLE",translate('Available'));
$conttp->setVariable("SELECTED",translate('Selected'));
if (($intExclude == 1) || ($intVersion < 3)) {
	$conttp->setVariable("DISABLE_HTML_BEGIN","<!--");
	$conttp->setVariable("DISABLE_HTML_END","-->");
}
$conttp->parse("datainsert");
$conttp->show("datainsert");
?>