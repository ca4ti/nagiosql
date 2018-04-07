<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project  : NagiosQL
// Component: Installer
// Website  : http://www.nagiosql.org
// Date     : $LastChangedDate: 2011-03-13 14:00:26 +0100 (So, 13. Mär 2011) $
// Author   : $LastChangedBy: rouven $
// Version  : 3.1.1
// Revision : $LastChangedRevision: 1058 $
//
///////////////////////////////////////////////////////////////////////////////
// Security
if(preg_match('#' . basename(__FILE__) . '#', htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'utf-8'))) {
  die("You can't access this file directly!");
}
$_SESSION['SETS']['install']['step']= 1;
$intError = 0;

$required_php_exts = array(
  'Session'   => 'session',
  'XML'       => 'xml',
	'Gettext'	  => 'gettext',
	'Filter'	  => 'filter',
	'SimpleXML' => 'SimpleXML'
);
$optional_php_exts = array(
	'FTP'		    => 'ftp',
	'SSH2'	    => 'ssh2',
	'curl'		  => 'curl'
);
$required_libs = array(
);

$supported_dbs = array(
  'MySQL'   => 'mysql'
);

$ini_checks = array(
	'file_uploads'                  => 1,
	'session.auto_start'            => 0,
	'suhosin.session.encrypt'       => 0,
);

$optional_checks = array(
  'date.timezone' => '-NOTEMPTY-'
);

$source_urls = array(
    'Sockets'   => 'http://www.php.net/manual/en/book.sockets.php',
    'Session'   => 'http://www.php.net/manual/en/book.session.php',
    'PCRE'      => 'http://www.php.net/manual/en/book.pcre.php',
    'FileInfo'  => 'http://www.php.net/manual/en/book.fileinfo.php',
    'Mcrypt'    => 'http://www.php.net/manual/en/book.mcrypt.php',
    'OpenSSL'   => 'http://www.php.net/manual/en/book.openssl.php',
    'JSON'      => 'http://www.php.net/manual/en/book.json.php',
    'DOM'       => 'http://www.php.net/manual/en/book.dom.php',
    'Intl'      => 'http://www.php.net/manual/en/book.intl.php',
	  'gettext'  	=> 'http://www.php.net/manual/en/book.gettext.php',
	  'curl'			=> 'http://www.php.net/manual/en/book.curl.php',
	  'Filter'    => 'http://www.php.net/manual/en/book.filter.php',
	  'XML'       => 'http://www.php.net/manual/en/book.xml.php',
	  'SimpleXML' => 'http://www.php.net/manual/en/book.simplexml.php',
	  'FTP'       => 'http://www.php.net/manual/en/book.ftp.php',
	  'MySQL'     => 'http://php.net/manual/de/book.mysql.php',
    'PEAR'      => 'http://pear.php.net',
	  'date.timezone' => 'http://www.php.net/manual/en/datetime.configuration.php#ini.date.timezone',
	  'SSH2'      => 'http://pecl.php.net/package/ssh2'
);

?>
<!-- DIV Container for installer Menu -->
<div id="installmenu">
  <div id="installmenu_content">
    <?php include "status.php"; ?>
  </div>
</div>
<!-- DIV Container for installer content -->
<div id="installmain">
  <div id="installmain_content">
    <?php
	echo "<h1>NagiosQL ".translate($_SESSION['SETS']['install']['InstallType']). ": ". translate("Checking requirements")."</h1>\n";
	echo "<h3>".translate("Checking Client")."</h3>\n";
	if ($_SESSION['SETS']['install']['javascript'] == 'on') {
		echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'> Javascript: <span class='green'>".translate("ENABLED")."</span>\n";		
	} else {
		echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'> Javascript: <span class='red'>".translate("NOT ENABLED")."</span>\n";
	}
	// PHP checks
	echo "<h3>".translate("Checking PHP version")."</h3>\n";
	define('MIN_PHP_VERSION', '5.2.0');
	if (version_compare(PHP_VERSION, MIN_PHP_VERSION, '>=')) {
		echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'> ".gettext ("Version").": <span class='green'>".translate("OK")."</span> (PHP ". PHP_VERSION ." ".gettext ("detected").")\n";
	} else {
		echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'> ".gettext ("Version").": <span class='red'>PHP ". PHP_VERSION ." ".gettext ("detected")."</span>, PHP ". MIN_PHP_VERSION ." ".translate("or greater is required")."\n";
		$intError = 1;
	}
	echo "<h3>".translate("Checking PHP extensions")."</h3>\n";
	echo "<p class='hint'>".translate("The following modules/extensions are <em>required</em> to run NagiosQL").":</p>\n";
	// get extensions location
	$ext_dir = ini_get('extension_dir');
	$prefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
	foreach ($required_php_exts as $name => $ext) {
		if (extension_loaded($ext)) {
			echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'> ".$name.": <span class='green'>".translate("OK")."</span>\n";
		} else {
			$_ext = $ext_dir . '/' . $prefix . $ext . '.' . PHP_SHLIB_SUFFIX;
			$msg = @is_readable($_ext) ? translate("Could be loaded. Please add in php.ini"): "<a href='".$source_urls[$name]."' target='_blank'><img src='images/onlinehelp.png' alt='online help' title='online help' class='textmiddle'></a>";
			echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'> ".$name.": <span class='red'>".translate("NOT AVAILABLE")." (".$msg.")</span>\n";
			$intError = 1;
		}
		echo '<br />';
	}
	echo "<p class='hint'>".translate("The next couple of extensions are <em>optional</em> but recommended").":</p>\n";
	foreach ($optional_php_exts as $name => $ext) {
		if (extension_loaded($ext)) {
			echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'> ".$name.": <span class='green'>".translate("OK")."</span>\n";
		}
		else {
			$_ext = $ext_dir . '/' . $prefix . $ext . '.' . PHP_SHLIB_SUFFIX;
			$msg = @is_readable($_ext) ? translate("Could be loaded. Please add in php.ini"): "<a href='".$source_urls[$name]."' target='_blank'><img src='images/onlinehelp.png' alt='online help' title='online help' class='textmiddle'></a>";
			echo "<img src='images/warning.png' alt='warning' title='warning' class='textmiddle'> ".$name.": <span class='yellow'>".translate("NOT AVAILABLE")." (".$msg.")</span>\n";
		}
		echo '<br />';
	}
	echo "<h3>".translate("Checking available database interfaces")."</h3>\n";
	echo "<p class='hint'>".translate("Check which of the supported extensions are installed. At least one of them is required.")."</p>\n";
	$prefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
	foreach ($supported_dbs as $database => $ext) {
		if (extension_loaded($ext)) {
			echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'> ".$database.": <span class='green'>".translate("OK")."</span>\n";
		}
		else {
			$_ext = $ext_dir . '/' . $prefix . $ext . '.' . PHP_SHLIB_SUFFIX;
			$msg = @is_readable($_ext) ? translate("Could be loaded. Please add in php.ini") : translate("Not installed").": <a href='".$source_urls[$database]."' target='_blank'><img src='images/onlinehelp.png' alt='online help' title='online help' class='textmiddle'></a>";;
			echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'> ".$database.": <span class='red'>".translate("NOT AVAILABLE")." (".$msg.")</span>\n";
            $intError = 1;
		}
		echo '<br />';
	}
/* 	echo "<h3>".translate("Check for required 3rd party libs")."</h3>\n";
	echo "<p class='hint'>".translate("This also checks if the include path is set correctly.")."</p>\n";
	foreach ($required_libs as $classname => $file) {
		@include_once $file;
		if (class_exists($classname)) {
			echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'> ".$classname.": <span class='green'>".translate("OK")."</span>\n";
		}
		else {
			echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'> ".$classname.": <span class='red'>".translate("Failed to load")." ".$file." (".$source_urls[$classname].")</span>\n";
			$intError = 1;
		}
		echo "<br />";
	} */
	echo "<h3>".translate("Checking php.ini/.htaccess settings")."</h3>\n";
	echo "<p class='hint'>".translate("The following settings are <em>required</em> to run NagiosQL").":</p>\n";
	foreach ($ini_checks as $var => $val) {
		$status = ini_get($var);
		if ($val === '-NOTEMPTY-') {
			if (empty($status)) {
				echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'> ".$var.": <span class='red'>".translate("NOT AVAILABLE")." (".translate("cannot be empty and needs to be set").")</span>\n";
				$intError = 1;
			} else {
				echo "<img src='images/valid.png' title='valid' alt='valid' class='textmiddle'> ".$var.": <span class='green'>".translate("OK")."</span>\n";
			}
			echo '<br />';
			continue;
		}
		if ($status == $val) {
			echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'> ".$var.": <span class='green'>".translate("OK")."</span>\n";
		} else {
			echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'> ".$var.": <span class='red'>".$status." (".translate("should be")." ".$val.")</span>\n";
			$intError = 1;
		}
		echo '<br />';
	}
	echo "<p class='hint'>".translate("The following settings are <em>optional</em> but recommended").":</p>\n";
	foreach ($optional_checks as $var => $val) {
		$status = ini_get($var);
		if ($val === '-NOTEMPTY-') {
			if (empty($status)) {
				echo "<img src='images/warning.png' alt='warning' title='warning' class='textmiddle'> ".$var.": <span class='yellow'>".translate("Could be set")." <a href='".$source_urls[$var]."' target='_blank'><img src='images/onlinehelp.png' alt='online help' title='online help' class='textmiddle'></a></span>\n";
			} else {
				echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'> ".$var.": <span class='green'>".translate("OK")."</span>\n";
			}
			echo '<br />';
			continue;
		}
		if ($status == $val) {
			echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'> ".$var.": <span class='green'>".translate("OK")."</span>\n";
		} else {
			echo "<img src='images/warning.png' alt='warning' title='warning' class='textmiddle'> ".$var.": <span class='yellow'>".$status." (".translate("Could be")." ".$val.")</span>\n";
		}
		echo '<br />';
	}
	// Checking file permission
	echo "<h3>".translate("Checking System Permission")."</h3>\n";
    // Read Config File
    $strFile = "../config/settings.php";
	$_SESSION['ConfigFile'] = $strFile;
	if(file_exists($strFile) && is_readable($strFile)) {
		echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'>".translate("Read test on settings file (config/settings.php)").": <span class='green'>".translate("OK")."</span>\n";
	} elseif (file_exists($strFile)&& (!(is_readable($strFile)))) {
		echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'>".translate("Read test on settings file (config/settings.php)").": <span class='red'>".translate("failed")."</span>\n";
		$intError = 2;
	} elseif (!(file_exists($strFile))) {
		echo "<img src='images/warning.png' alt='warning' title='warning' class='textmiddle'>".translate("Settings file does not exists (config/settings.php)").": <span class='yellow'>".translate("will be created")."</span>\n";
	}
	echo '<br />';
    // Write Config File
    if(file_exists($strFile) && is_writable($strFile)) {
		echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'>".translate("Write test on settings file (config/settings.php)").": <span class='green'>".translate("OK")."</span>\n";
    } elseif (is_writeable("../config") && (!(file_exists($strFile)))) {
		echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'>".translate("Write test on settings directory (config/)").": <span class='green'>".translate("OK")."</span>\n";
    } elseif (file_exists($strFile) && (!(is_writable($strFile)))) {
		echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'>".translate("Write test on settings file (config/settings.php)").": <span class='red'>".translate("failed")."</span>\n";
		$intError = 2;
    } else {
		echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'>".translate("Write test on settings directory (config/)").": <span class='red'>".translate("failed")."</span>\n";
		$intError = 2;
	}
	echo '<br />';
	// Read Nagios Class
    $strFile = "../functions/nag_class.php";
	if(file_exists($strFile) && is_readable($strFile)) {
   		echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'>".translate("Read test on a class file (functions/nag_class.php)").": <span class='green'>".translate("OK")."</span>\n";
    } else {
   		echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'>".translate("Read test on a class file (functions/nag_class.php)").": <span class='red'>".translate("failed")."</span>\n";
        $intError = 2;
    }
	echo '<br />';
	// Read adminsite
	$strFile = "../admin.php";
	if(file_exists($strFile) && is_readable($strFile)) {
   		echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'>".translate("Read test on startsite file (admin.php)").": <span class='green'>".translate("OK")."</span>\n";
	} else {
		echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'>".translate("Read test on startsite file (admin.php)").": <span class='red'>".translate("failed")."</span>\n";
        $intError = 2;
	}
	echo '<br />';
	// Read Template
	$strFile = "../templates/index.tpl.htm";
	if(file_exists($strFile) && is_readable($strFile)) {
   		echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'>".translate("Read test on a template file (templates/index.tpl.htm)").": <span class='green'>".translate("OK")."</span>\n";
	} else {
		echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'>".translate("Read test on a template file (templates/index.tpl.htm)").": <span class='red'>".translate("failed")."</span>\n";
        $intError = 2;
	}
	echo '<br />';
    // Read Admin Template
	$strFile = "../templates/admin/admin_master.tpl.htm";
	if(file_exists($strFile) && is_readable($strFile)) {
   		echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'>".translate("Read test on a admin template file (templates/admin/admin_master.tpl.htm)").": <span class='green'>".translate("OK")."</span>\n";
	} else {
		echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'>".translate("Read test on a admin template file (templates/admin/admin_master.tpl.htm)").": <span class='red'>".translate("failed")."</span>\n";
        $intError = 2;	
	}
	echo '<br />';
    // Read File Template
	$strFile = "../templates/files/contacts.tpl.dat";
	if(file_exists($strFile) && is_readable($strFile)) {
   		echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'>".translate("Read test on a file template (templates/files/contacts.tpl.dat)").": <span class='green'>".translate("OK")."</span>\n";
	} else {
		echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'>".translate("Read test on a file template (templates/files/contacts.tpl.dat)").": <span class='red'>".translate("failed")."</span>\n";
        $intError = 2;
	}
	echo '<br />';
	// Read image
	$strFile = "../images/pixel.gif";
	if(file_exists($strFile) && is_readable($strFile)) {
   		echo "<img src='images/valid.png' alt='valid' title='valid' class='textmiddle'>".translate("Read test on a image file (images/pixel.gif)").": <span class='green'>".translate("OK")."</span>\n";
	} else {
		echo "<img src='images/invalid.png' alt='invalid' title='invalid' class='textmiddle'>".translate("Read test on a image file (images/pixel.gif)").": <span class='red'>".translate("failed")."</span>\n";
        $intError = 2;
	}
	echo "<br />\n";
	echo "<br />\n";
	// Status Message
	if ($intError != 0) {
		echo "<span class='red'>".translate("There are some errors - please check your system settings and read the requirements of NagiosQL!")."</span><br><br>\n";
		echo translate("Read the INSTALLATION file from NagiosQL to find out, how to fix them.") ."<br>";
		echo translate("After that - refresh this page to proceed") ."...<br>\n";
		echo "<div id=\"install-center\">\n";
		echo "<form action='' method='post'>\n";
		echo "<input type='image' src='images/reload.png' title='refresh' value='Submit' alt='refresh' onClick='window.location.reload()'><br>".translate("Refresh")."\n";
		echo "</form>\n";
		echo "</div>\n";
	} else {
		echo "<span class='green'>".translate("Environment test sucessfully passed")."</span><br><br>\n";
		echo "<div id=\"install-next\">\n";
		echo "<form action='' method='post'>\n";
		echo "<input type='hidden' name='PHPSESSID' value='".session_id()."' />";
		echo "<input type='hidden' name='step' value='2'>\n";
		echo "<input type='image' src='images/next.png' value='Submit' title='next' alt='next'><br>".translate("Next")."\n";
		echo "</form>\n";
		echo "</div>\n";
	}
 ?>
  </div>
</div>
<div id="ie_clearing"> </div>