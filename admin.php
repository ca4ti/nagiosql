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
// Component : Admin main site
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2012-03-14 13:43:23 +0100 (Wed, 14 Mar 2012) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1296 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$prePageId			= 1;
$preContent   		= "admin/mainpages.tpl.htm";
$preAccess    		= 1;
$preFieldvars 		= 1;
//
// Include preprocessing files
// ===========================
require("functions/prepend_adm.php");
require("functions/prepend_content.php");
//
// Include Content
// ===============
$conttp->setVariable("TITLE",translate('NagiosQL Administration'));
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("DESC",translate('Welcome to NagiosQL, the administration module that can be used to easily create, modify and delete configuration files for Nagios. The data is stored in a database and can be written directly to the standard files at any time you want.'));
$conttp->parse("main");
$conttp->show("main");
//
// Include footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>