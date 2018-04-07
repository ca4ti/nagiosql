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
// Component : Admin main site
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
$intMain      = 1;
$intSub       = 0;
$intMenu      = 2;
$preContent   = "admin/mainpages.tpl.htm";
$strMessage   = "";
//
// Include preprocessing file
// ==========================
$preFieldvars = 1;
//
// Include preprocessing file
// ==========================
require("functions/prepend_adm.php");
//
// Build menu
// ==========
$myVisClass->getMenu($intMain,$intSub,$intMenu);
//
// Include Content
// ===============
$conttp->setVariable("TITLE",translate('NagiosQL Administration'));
$conttp->parse("header");
$conttp->show("header");
$conttp->setVariable("DESC",translate('Welcome to NagiosQL, the administration module that can be used to easily create, modify and delete configuration files for Nagios and Icinga. The data is stored in a MySQL database and can be written directly to the standard files at any time you want.'));
if (isset($SETS['common']['updcheck']) && ($SETS['common']['updcheck'] == '1')) {
	foreach($arrDescription AS $elem) {
  	$conttp->setVariable($elem['name'],$elem['string']);
	}
	// Read XML
	libxml_use_internal_errors(true);
	$versionfeed='http://api.nagiosql.org/versioncheck.php?check=1';
	if ((isset($SETS['network']['Proxy']) && ($SETS['network']['Proxy'] == '1')) AND (isset($SETS['network']['ProxyServer']) && ($SETS['network']['ProxyServer'] != ""))) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $versionfeed);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_PROXY, $SETS['network']['ProxyServer']);
		if ((isset($SETS['network']['ProxyUser']) && ($SETS['network']['ProxyUser'] != "")) AND (isset($SETS['network']['ProxyPasswd']) && ($SETS['network']['ProxyPasswd'] != ""))) {
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $SETS['network']['ProxyUser'].":".$SETS['network']['ProxyPasswd']);
		}
		$versionstring = curl_exec($ch);
		$info = curl_getinfo($ch);
		// Curl Errorhandling
		if ($versionstring === false || $info['http_code'] != 200) {
		  $strMessage .= "<p class='dbmessage'>".translate("Could not connect to Updateserver"). " [". $info['http_code']. "]";
		  if (curl_error($ch)) $strMessage .= "<br>".translate("Error").": ".curl_error($ch);
		  $strMessage .= "</p>";
		  curl_close($ch);
		}	else {
			$versionrss=@simplexml_load_string($versionstring);
			curl_close($ch);
		}
	} else {
		$versionrss=@simplexml_load_file($versionfeed);
	}
	// XML Errorhandling
	function display_xml_error($error, $xml) {
    $return  = $xml[$error->line - 1] . "\n";
    $return .= str_repeat('-', $error->column) . "^\n";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "Warning $error->code: ";
            break;
         case LIBXML_ERR_ERROR:
            $return .= "Error $error->code: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "Fatal Error $error->code: ";
            break;
    }
    $return .= trim($error->message) .
               "\n  Line: $error->line" .
               "\n  Column: $error->column";
    if ($error->file) {
        $return .= "\n  File: $error->file";
    }
    return "$return\n\n--------------------------------------------\n\n";
}
	if (isset($versionrss) AND (!$versionrss)) {
		$strMessage .= "<p class='dbmessage'>".translate("An error occured during the version check")."</p>";
		$errors = libxml_get_errors();
    foreach ($errors as $error) {
			$strMessage .= str_replace($versionfeed, 'http://api.nagiosql.org', display_xml_error($error, $versionrss));
		}
		libxml_clear_errors();
	}
	$conttp->setVariable("DBMESSAGE","&nbsp;");
	$conttp->setVariable("VERSIONCHECK",translate('Checking for NagiosQL Updates'));
	$conttp->setVariable("CURRENT",$setFileVersion);
	$conttp->setVariable("VERSION_INSTALLED",translate('Installed'));
	$conttp->setVariable("VERSION_AVAILABLE",translate('Available'));
	$conttp->setVariable("VERSION_INFORMATION",translate('Information'));
	$conttp->setVariable("IMAGE_PATH_ADMIN",$strRootPath."images/");
	if ($strMessage != "") {
		// Show messages
		$conttp->setVariable("DBMESSAGE",$strMessage);
	} else {
		$conttp->setVariable("LATEST",$versionrss->stable->version);
		if (version_compare($versionrss->stable->version, $setFileVersion,'==')) {
			$conttp->setVariable("INFORMATION","<span class='greenmessage'>".translate('You already have the latest version installed')."</span>");
			$strHeadline="<h3>NagiosQL ".$versionrss->stable->version."</h3><br>";
			$versionrss->stable->description=$strHeadline . str_replace("<![CDATA[", "", $versionrss->stable->description);
			$_SESSION['updInfo']=str_replace("]]>", "", $versionrss->stable->description);		
		} elseif (version_compare($versionrss->stable->version, $setFileVersion,'<=')) {
				if (version_compare($versionrss->development->version, $setFileVersion,'==')) {
					$conttp->setVariable("INFORMATION","<span class='greenmessage'>".translate('You already have the latest development version installed')."</span>");
					$strHeadline="<h3>NagiosQL ".$versionrss->development->version."</h3><br>";
					$versionrss->development->description=$strHeadline . str_replace("<![CDATA[", "", $versionrss->development->description);
					$_SESSION['updInfo']=str_replace("]]>", "", $versionrss->development->description);					
				} elseif (version_compare($versionrss->development->version, $setFileVersion,'>=')) {
					$conttp->setVariable("INFORMATION","<span class='dbmessage'>".translate('You are using an older development version. Please update to the latest development version')."</span>:");
					$conttp->setVariable("DL_LINK"," <a href=\"".$versionrss->development->link."\">".urldecode($versionrss->development->link)."</a>");
					$strHeadline="<h3>NagiosQL ".$versionrss->development->version."</h3><br>";
					$versionrss->development->description=$strHeadline . str_replace("<![CDATA[", "", $versionrss->development->description);
					$_SESSION['updInfo']=str_replace("]]>", "", $versionrss->development->description);
				}
				$conttp->setVariable("LATEST",$versionrss->development->version);
		} elseif (version_compare($versionrss->stable->version, $setFileVersion,'>=')) {
			$conttp->setVariable("INFORMATION","<span class='dbmessage'>".translate('You are using an old NagiosQL version. Please update to the latest stable version')."</span>:");
			$conttp->setVariable("DL_LINK"," <a href=\"".$versionrss->stable->link."\">".urldecode($versionrss->stable->link)."</a>");
			$strHeadline="<h3>NagiosQL ".$versionrss->stable->version."</h3><br>";
			$versionrss->stable->description=$strHeadline . str_replace("<![CDATA[", "", $versionrss->stable->description);
			$_SESSION['updInfo']=str_replace("]]>", "", $versionrss->stable->description);		
		}
		$conttp->parse("versioncheck");
	}
}
$conttp->parse("main");
$conttp->show("main");
//
// Include footer
// ==============
$maintp->setVariable("VERSION_INFO","<a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse("footer");
$maintp->show("footer");
?>