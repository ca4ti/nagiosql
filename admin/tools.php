<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2018 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Tools overview
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.0
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Path settings
// ===================
$preRelPath  = strchr(filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING), 'admin', true);
$preBasePath = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_STRING).$preRelPath;
//
// Define common variables
// =======================
$prePageId  = 6;
$preContent = "admin/mainpages.htm.tpl";
//
// Include preprocessing file
// ==========================
require($preBasePath.'functions/prepend_adm.php');
//
// Include content
// ===============
$conttp->setVariable("TITLE", translate('Different tools'));
$conttp->setVariable("DESC", translate('Useful functions for data import, main configuration, daemon control and '
    . 'so on.'));
$conttp->parse("main");
$conttp->show("main");
//
// Include Footer
// ==============
$maintp->setVariable("VERSION_INFO", "<a href='https://sourceforge.net/projects/nagiosql/' "
    . "target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
