<?php
/* ----------------------------------------------------------------------------
 NagiosQL
-------------------------------------------------------------------------------
 (c) 2005-2022 by Martin Willisegger

 Project   : NagiosQL
 Component : Admin main site
 Website   : https://sourceforge.net/projects/nagiosql/
 Version   : 3.5.0
 GIT Repo  : https://gitlab.com/wizonet/NagiosQL
-----------------------------------------------------------------------------*/
/**
 * Class and variable includes
 * @var HTML_Template_IT $conttp
 * @var HTML_Template_IT $maintp
 * @var string $setFileVersion from prepend_adm.php
 * @var string $setGITVersion from prepend_adm.php
 */
/*
Path settings
*/
$preRelPath = strstr(filter_input(INPUT_SERVER, 'PHP_SELF'), 'admin.php', true);
$preBasePath = strstr(filter_input(INPUT_SERVER, 'SCRIPT_FILENAME'), 'admin.php', true);
/*
Define common variables
*/
$prePageId = 1;
$preContent = 'admin/mainpages.htm.tpl';
$preAccess = 1;
$preFieldvars = 1;
/*
Include preprocessing files
*/
require $preBasePath . 'functions/prepend_adm.php';
require $preBasePath . 'functions/prepend_content.php';
/*
Include Content
*/
$conttp->setVariable('TITLE', translate('NagiosQL Administration'));
$conttp->parse('header');
$conttp->show('header');
$conttp->setVariable('DESC', translate('Welcome to NagiosQL, the administration module that can be used to easily '
    . 'create, modify and delete configuration files for Nagios. The data is stored in a database '
    . 'and can be written directly to the standard files at any time you want.'));
$conttp->parse('main');
$conttp->show('main');
/*
Include footer
*/
$maintp->setVariable('VERSION_INFO', "<a href='https://sourceforge.net/projects/nagiosql/' "
    . "target='_blank'>NagiosQL</a> $setFileVersion - GIT Version: $setGITVersion");
$maintp->parse('footer');
$maintp->show('footer');