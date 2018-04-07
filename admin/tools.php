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
// Component : Tools overview
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
$intMain    	= 6;
$intSub     	= 0;
$intMenu    	= 2;
$preContent   	= "admin/mainpages.tpl.htm";
//
// Include preprocessing file
// ==========================
require("../functions/prepend_adm.php");
//
// Build content menu
// ==================
$myVisClass->getMenu($intMain,$intSub,$intMenu);
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