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
// Component : Tools overview
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2012-02-21 14:10:41 +0100 (Tue, 21 Feb 2012) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1229 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$prePageId		= 6;
$preContent   	= "admin/mainpages.tpl.htm";
//
// Include preprocessing file
// ==========================
require("../functions/prepend_adm.php");
//
// Include content
// ===============
$conttp->setVariable("TITLE",translate('Different tools'));
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("DESC",translate('Useful functions for data import, main configuration, daemon control and so on.'));
$conttp->parse("main");
$conttp->show("main");
//
// Include Footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>