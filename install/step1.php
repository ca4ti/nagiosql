<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2020 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Installer script - step 1
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Prevent this file from direct access
// ====================================
if (preg_match('#' . basename(__FILE__) . '#', htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'utf-8'))) {
    header('Location: install.php');
    exit;
}
//
// Define common variables
// =======================
$preIncludeContent = $preBasePath.'install/templates/step1.htm.tpl';
$intError          = 0;
//
// Define check arrays
// ===================
$arrRequiredExt = array(
    'Session' => 'session',
    'Gettext' => 'gettext',
    'Filter'  => 'filter'
);
$arrOptionalExt = array(
    'FTP'     => 'ftp',
    'SSH2'    => 'ssh2'
);
$arrSupportedDBs = array(
    'MySQLi'  => 'mysqli'
);
$arrIniCheck = array(
    'file_uploads'            => 1,
    'session.auto_start'      => 0,
    'suhosin.session.encrypt' => 0,
    'date.timezone'           => '-NOTEMPTY-'
);
$arrSourceURLs = array(
    'Sockets'       => 'http://www.php.net/manual/en/book.sockets.php',
    'Session'       => 'http://www.php.net/manual/en/book.session.php',
    'PCRE'          => 'http://www.php.net/manual/en/book.pcre.php',
    'FileInfo'      => 'http://www.php.net/manual/en/book.fileinfo.php',
    'Mcrypt'        => 'http://www.php.net/manual/en/book.mcrypt.php',
    'OpenSSL'       => 'http://www.php.net/manual/en/book.openssl.php',
    'JSON'          => 'http://www.php.net/manual/en/book.json.php',
    'DOM'           => 'http://www.php.net/manual/en/book.dom.php',
    'Intl'          => 'http://www.php.net/manual/en/book.intl.php',
    'gettext'       => 'http://www.php.net/manual/en/book.gettext.php',
    'curl'          => 'http://www.php.net/manual/en/book.curl.php',
    'Filter'        => 'http://www.php.net/manual/en/book.filter.php',
    'XML'           => 'http://www.php.net/manual/en/book.xml.php',
    'SimpleXML'     => 'http://www.php.net/manual/en/book.simplexml.php',
    'FTP'           => 'http://www.php.net/manual/en/book.ftp.php',
    'MySQL'         => 'http://php.net/manual/de/book.mysqli.php',
    'PEAR'          => 'http://pear.php.net',
    'date.timezone' => 'http://www.php.net/manual/en/datetime.configuration.php#ini.date.timezone',
    'SSH2'          => 'http://pecl.php.net/package/ssh2'
);
//
// Build content
// =============
$arrTemplate['STEP1_BOX']       = $myInstClass->translate('Requirements');
$arrTemplate['STEP2_BOX']       = $myInstClass->translate('Installation');
$arrTemplate['STEP3_BOX']       = $myInstClass->translate('Finish');
$arrTemplate['STEP1_TITLE']     = 'NagiosQL ' .$myInstClass->translate('Installation'). ': ' .
    $myInstClass->translate('Checking requirements');
$arrTemplate['STEP1_SUBTITLE1'] = $myInstClass->translate('Checking Client');
$arrTemplate['STEP1_SUBTITLE2'] = $myInstClass->translate('Checking PHP version');
$arrTemplate['STEP1_SUBTITLE3'] = $myInstClass->translate('Checking PHP extensions');
$arrTemplate['STEP1_SUBTITLE4'] = $myInstClass->translate('Checking available database interfaces');
$arrTemplate['STEP1_SUBTITLE5'] = $myInstClass->translate('Checking php.ini/.htaccess settings');
$arrTemplate['STEP1_SUBTITLE6'] = $myInstClass->translate('Checking System Permission');
$arrTemplate['STEP1_TEXT3_1']   = $myInstClass->translate('The following modules/extensions are <em>required</em> '
    . 'to run NagiosQL');
$arrTemplate['STEP1_TEXT3_2']   = $myInstClass->translate('The next couple of extensions are <em>optional</em> but '
    . 'recommended');
$arrTemplate['STEP1_TEXT4_1']   = $myInstClass->translate('Check which of the supported extensions are installed. At '
    . 'least one of them is required.');
$arrTemplate['STEP1_TEXT5_1']   = $myInstClass->translate('The following settings are <em>required</em> to run '
    . 'NagiosQL');
//
// Conditional checks
// =======================
$strHTMLPart1 = '<img src="images/valid.png" alt="valid" title="valid" class="textmiddle"> ';
$strHTMLPart2 = '<img src="images/invalid.png" alt="invalid" title="invalid" class="textmiddle"> ';
$strHTMLPart3 = '<img src="images/warning.png" alt="warning" title="warning" class="textmiddle"> ';
$strHTMLPart4 = ': <span class="green">';
$strHTMLPart5 = ': <span class="red">';
$strHTMLPart6 = ': <span class="yellow">';
$strHTMLPart7 = '<img src="images/onlinehelp.png" alt="online help" title="online help" class="textmiddle" '
    . 'style="border:none;">';

// Javascript check
if ($_SESSION['install']['jscript'] == 'yes') {
    $arrTemplate['CHECK_1_PIC']   = 'valid';
    $arrTemplate['CHECK_1_CLASS'] = 'green';
    $arrTemplate['CHECK_1_VALUE'] = $myInstClass->translate('ENABLED');
    $arrTemplate['CHECK_1_INFO']  = '';
} else {
    $arrTemplate['CHECK_1_PIC']   = 'invalid';
    $arrTemplate['CHECK_1_CLASS'] = 'green';
    $arrTemplate['CHECK_1_VALUE'] = $myInstClass->translate('NOT ENABLED');
    $arrTemplate['CHECK_1_INFO']  = '(' .$myInstClass->translate('After enabling Javascript, the page must be updated '
            . 'twice so that the status changes'). ')';
}
// PHP version check
$strMinPHPVersion = '5.5.0';
$arrTemplate['CHECK_2_TEXT'] = $myInstClass->translate('Version');
if (version_compare(PHP_VERSION, $strMinPHPVersion, '>=')) {
    $arrTemplate['CHECK_2_PIC']   = 'valid';
    $arrTemplate['CHECK_2_CLASS'] = 'green';
    $arrTemplate['CHECK_2_VALUE'] = $myInstClass->translate('OK');
    $arrTemplate['CHECK_2_INFO']  = '(PHP ' . PHP_VERSION . ' ' .$myInstClass->translate('detected'). ')';
} else {
    $arrTemplate['CHECK_2_PIC']   = 'invalid';
    $arrTemplate['CHECK_2_CLASS'] = 'green';
    $arrTemplate['CHECK_2_VALUE'] = 'PHP ' . PHP_VERSION . ' ' .$myInstClass->translate('detected');
    $arrTemplate['CHECK_2_INFO']  = '(PHP ' . $strMinPHPVersion . ' ' .$myInstClass->translate('or greater is '
            . 'required'). ')';
    $intError = 1;
}
// PHP modules / extensions
$strExtPath = ini_get('extension_dir');
$strPrefix  = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
$strHTML1   = '';
// Check for pear
$intErrorReporting = error_reporting();
error_reporting(0);
/** @noinspection PhpIncludeInspection */
include_once 'System.php';
error_reporting($intErrorReporting);
$intPearResult = 0;
if (class_exists('System')) {
    $intPearResult = 1;
}
if ($intPearResult === 1) {
    $strHTML1 .= $strHTMLPart1.'PEAR'.$strHTMLPart4.$myInstClass->translate('OK')."</span>\n";
} else {
    $strMsg    = '<a href="' .$arrSourceURLs['PEAR']. '" target="_blank">' .$strHTMLPart7. '</a>';
    $strHTML1 .= $strHTMLPart2.'PEAR'.$strHTMLPart5.$myInstClass->translate('NOT AVAILABLE'). ' (' .$strMsg. ')'
        . "</span>\n";
    $intError  = 1;
}
$strHTML1 .= "<br>\n";
foreach ($arrRequiredExt as $key => $elem) {
    if (extension_loaded($elem)) {
        $strHTML1 .= $strHTMLPart1.$key.$strHTMLPart4.$myInstClass->translate('OK')."</span>\n";
    } else {
        $strPath   = $strExtPath. '/' .$strPrefix.$elem. '.' .PHP_SHLIB_SUFFIX;
        $strMsg    = is_readable($strPath) ? $myInstClass->translate('Could be loaded. Please add in php.ini')
            : '<a href="' .$arrSourceURLs[$key]. '" target="_blank">' .$strHTMLPart7. '</a>';
        $strHTML1 .= $strHTMLPart2.$key.$strHTMLPart5.$myInstClass->translate('NOT AVAILABLE'). ' (' .$strMsg. ')'
            . "</span>\n";
        $intError  = 1;
    }
    $strHTML1 .= "<br>\n";
}
$arrTemplate['CHECK_3_CONTENT_1'] = $strHTML1;
$strHTML2   = '';
foreach ($arrOptionalExt as $key => $elem) {
    if (extension_loaded($elem)) {
        $strHTML2 .= $strHTMLPart1.$key.$strHTMLPart4.$myInstClass->translate('OK')."</span>\n";
    } else {
        $strPath   = $strExtPath. '/' .$strPrefix.$elem. '.' .PHP_SHLIB_SUFFIX;
        $strMsg    = is_readable($strPath) ? $myInstClass->translate('Could be loaded. Please add in php.ini')
            : '<a href="' .$arrSourceURLs[$key]. '" target="_blank">' .$strHTMLPart7. '</a>';
        $strHTML2 .= $strHTMLPart3.$key.$strHTMLPart6.$myInstClass->translate('NOT AVAILABLE'). ' (' .$strMsg. ')'
            . "</span>\n";
        //$intError = 1;
    }
    $strHTML2 .= "<br>\n";
}
$arrTemplate['CHECK_3_CONTENT_2'] = $strHTML2;
// PHP database interfaces
$strHTML3 = '';
$intTemp  = 0;
$_SESSION['install']['dbtype_available'] = array();
foreach ($arrSupportedDBs as $key => $elem) {
    if (extension_loaded($elem)) {
        $strNewInstallOnly = '';
        if (isset($_SESSION['install']['dbtype']) && ($_SESSION['install']['mode'] == 'Update') &&
            ($_SESSION['install']['dbtype'] != $elem) &&
            (0 !== strpos($_SESSION['install']['dbtype'], substr($elem, 0, 5)))) {
            $strNewInstallOnly = ' (' .$myInstClass->translate('New installation only - updates are only supported '
                    . 'using the same database interface!'). ')';
        }
        $strHTML3 .= $strHTMLPart1.$key.$strHTMLPart4.$myInstClass->translate('OK')."</span>  $strNewInstallOnly\n";
        if ($strNewInstallOnly == '') {
            $_SESSION['install']['dbtype_available'][] = $elem;
        }
        $intTemp++;
    } else {
        $strPath   = $strExtPath. '/' .$strPrefix.$elem. '.' .PHP_SHLIB_SUFFIX;
        $strMsg    = is_readable($strPath) ? $myInstClass->translate('Could be loaded. Please add in php.ini')
            : '<a href="' .$arrSourceURLs[$key]. '" target="_blank">' .$strHTMLPart7. '</a>';
        $strHTML3 .= $strHTMLPart2.$key.$strHTMLPart5.$myInstClass->translate('NOT AVAILABLE'). ' (' .$strMsg. ')'
            . "</span>\n";
    }
    $strHTML3 .= "<br>\n";
}
$arrTemplate['CHECK_4_CONTENT_1'] = $strHTML3;
if ($intTemp == 0) {
    $intError = 1;
}
// PHP ini checks
$strHTML4 = '';
foreach ($arrIniCheck as $key => $elem) {
    $strStatus = ini_get($key);
    if ($elem === '-NOTEMPTY-') {
        if (empty($strStatus)) {
            $strHTML4 .= $strHTMLPart2.$key.$strHTMLPart5.$myInstClass->translate('NOT AVAILABLE'). ' (' .
                $myInstClass->translate('cannot be empty and needs to be set').")</span>\n";
            $intError = 1;
        } else {
            $strHTML4 .= $strHTMLPart1.$key.$strHTMLPart4.$myInstClass->translate('OK')."</span>\n";
        }
    } else {
        if ($strStatus == $elem) {
            $strHTML4 .= $strHTMLPart1.$key.$strHTMLPart4.$myInstClass->translate('OK')."</span>\n";
        } else {
            $strHTML4 .= $strHTMLPart2.$key.$strHTMLPart5.$strStatus. ' (' .$myInstClass->translate('should be'). ' ' .
                $elem.")</span>\n";
            $intError = 1;
        }
    }
    $strHTML4 .= "<br>\n";
}
$arrTemplate['CHECK_5_CONTENT_1'] = $strHTML4;
// File access checks
$strConfigFile = '../config/settings.php';
if (file_exists($strConfigFile) && is_readable($strConfigFile)) {
    $arrTemplate['CHECK_6_CONTENT_1'] = $strHTMLPart1.$myInstClass->translate('Read test on settings file '
            . '(config/settings.php)').$strHTMLPart4.$myInstClass->translate('OK')."</span><br>\n";
} elseif (file_exists($strConfigFile)&& !is_readable($strConfigFile)) {
    $arrTemplate['CHECK_6_CONTENT_1'] = $strHTMLPart2.$myInstClass->translate('Read test on settings file '
            . '(config/settings.php)').$strHTMLPart5.$myInstClass->translate('failed')."</span><br>\n";
} elseif (!file_exists($strConfigFile)) {
    $arrTemplate['CHECK_6_CONTENT_1'] = $strHTMLPart3.$myInstClass->translate('Settings file does not exists '
            . '(config/settings.php)').$strHTMLPart6.$myInstClass->translate('will be created')."</span><br>\n";
}
if (file_exists($strConfigFile) && is_writable($strConfigFile)) {
    $arrTemplate['CHECK_6_CONTENT_2'] = $strHTMLPart1.$myInstClass->translate('Write test on settings file '
            . '(config/settings.php)').$strHTMLPart4.$myInstClass->translate('OK')."</span><br>\n";
} elseif (is_writable('../config') && !file_exists($strConfigFile)) {
    $arrTemplate['CHECK_6_CONTENT_2'] = $strHTMLPart1.$myInstClass->translate('Write test on settings directory '
            . '(config/)').$strHTMLPart4.$myInstClass->translate('OK')."</span><br>\n";
} elseif (file_exists($strConfigFile) && !is_writable($strConfigFile)) {
    $arrTemplate['CHECK_6_CONTENT_2'] = $strHTMLPart2.$myInstClass->translate('Write test on settings file '
            . '(config/settings.php)').$strHTMLPart5.$myInstClass->translate('failed')."</span><br>\n";
    $intError = 1;
} else {
    $arrTemplate['CHECK_6_CONTENT_2'] = $strHTMLPart2.$myInstClass->translate('Write test on settings directory '
            . '(config/)').$strHTMLPart5.$myInstClass->translate('failed')."</span><br>\n";
    $intError = 1;
}
$strClassFile = '../functions/NagVisualClass.php';
if (file_exists($strClassFile) && is_readable($strClassFile)) {
    $arrTemplate['CHECK_6_CONTENT_3'] = $strHTMLPart1.$myInstClass->translate('Read test on one class file '
            . '(functions/NagVisualClass.php)').$strHTMLPart4.$myInstClass->translate('OK')."</span><br>\n";
} else {
    $arrTemplate['CHECK_6_CONTENT_3'] = $strHTMLPart2.$myInstClass->translate('Read test on one class file '
            . '(functions/NagVisualClass.php)').$strHTMLPart5.$myInstClass->translate('failed')."</span><br>\n";
    $intError = 1;
}
$strFile1 = '../admin.php';
if (file_exists($strFile1) && is_readable($strFile1)) {
    $arrTemplate['CHECK_6_CONTENT_4'] = $strHTMLPart1.$myInstClass->translate('Read test on home page file '
            . '(admin.php)').$strHTMLPart4.$myInstClass->translate('OK')."</span><br>\n";
} else {
    $arrTemplate['CHECK_6_CONTENT_4'] = $strHTMLPart2.$myInstClass->translate('Read test on home page file '
            . '(admin.php)').$strHTMLPart5.$myInstClass->translate('failed')."</span><br>\n";
    $intError = 1;
}
$strFile2 = '../templates/index.htm.tpl';
if (file_exists($strFile2) && is_readable($strFile2)) {
    $arrTemplate['CHECK_6_CONTENT_5'] = $strHTMLPart1.$myInstClass->translate('Read test on one template file '
            . '(templates/index.tpl.htm)').$strHTMLPart4.$myInstClass->translate('OK')."</span><br>\n";
} else {
    $arrTemplate['CHECK_6_CONTENT_5'] = $strHTMLPart2.$myInstClass->translate('Read test on one template file '
            . '(templates/index.tpl.htm)').$strHTMLPart5.$myInstClass->translate('failed')."</span><br>\n";
    $intError = 1;
}
$strFile3 = '../templates/admin/datalist.htm.tpl';
if (file_exists($strFile3) && is_readable($strFile3)) {
    $arrTemplate['CHECK_6_CONTENT_6'] = $strHTMLPart1.$myInstClass->translate('Read test on one admin template file '
            . '(templates/admin/datalist.htm.tpl)').$strHTMLPart4.$myInstClass->translate('OK')."</span><br>\n";
} else {
    $arrTemplate['CHECK_6_CONTENT_6'] = $strHTMLPart2.$myInstClass->translate('Read test on one admin template file '
            . '(templates/admin/datalist.htm.tpl)').$strHTMLPart5.$myInstClass->translate('failed')."</span><br>\n";
    $intError = 1;
}
$strFile4 = '../templates/files/contacts.tpl.dat';
if (file_exists($strFile4) && is_readable($strFile4)) {
    $arrTemplate['CHECK_6_CONTENT_7'] = $strHTMLPart1.$myInstClass->translate('Read test on one file template '
            . '(templates/files/contacts.tpl.dat)').$strHTMLPart4.$myInstClass->translate('OK')."</span><br>\n";
} else {
    $arrTemplate['CHECK_6_CONTENT_7'] = $strHTMLPart2.$myInstClass->translate('Read test on one file template '
            . '(templates/files/contacts.tpl.dat)').$strHTMLPart5.$myInstClass->translate('failed')."</span><br>\n";
    $intError = 1;
}
$strFile5 = '../images/pixel.gif';
if (file_exists($strFile5) && is_readable($strFile5)) {
    $arrTemplate['CHECK_6_CONTENT_8'] = $strHTMLPart1.$myInstClass->translate('Read test on one image file '
            . '(images/pixel.gif)').$strHTMLPart4.$myInstClass->translate('OK')."</span><br>\n";
} else {
    $arrTemplate['CHECK_6_CONTENT_9'] = $strHTMLPart2.$myInstClass->translate('Read test on one image file '
            . '(images/pixel.gif)').$strHTMLPart5.$myInstClass->translate('failed')."</span><br>\n";
    $intError = 1;
}
if ($intError != 0) {
    $arrTemplate['MESSAGE']     = '<span class="red">' .$myInstClass->translate('There are some errors - please '
            . 'check your system settings and read the requirements of NagiosQL!')."</span><br><br>\n";
    $arrTemplate['MESSAGE']    .= $myInstClass->translate('Read the INSTALLATION file in the NagiosQL doc directory '
        . 'or the installation PDF file on our');
    $arrTemplate['MESSAGE']    .= ' <a href="https://sourceforge.net/projects/nagiosql/documentation.html" '
        . 'target="_blank">';
    $arrTemplate['MESSAGE']    .= $myInstClass->translate('online documentation'). '</a><br>' .
        $myInstClass->translate('site to find out, how to fix them.') ."<br>\n";
    $arrTemplate['MESSAGE']    .= $myInstClass->translate('After that - refresh this page to proceed') ."...<br>\n";
    $arrTemplate['DIV_ID']     = 'install-center';
    $arrTemplate['FORM_CONTENT']  = '<input type="image" src="images/reload.png" title="refresh" '
        . 'value="Submit" alt="refresh" onClick="window.location.reload()"><br>';
    $arrTemplate['FORM_CONTENT'] .= $myInstClass->translate('Refresh')."\n";
} else {
    $arrTemplate['MESSAGE']  = '<span class="green">' .$myInstClass->translate('Environment test completed '
            . 'successfully')."</span><br><br>\n";
    $arrTemplate['DIV_ID']   = 'install-next';
    $arrTemplate['FORM_CONTENT']  = "<input type=\"hidden\" name=\"hidStep\" id=\"hidStep\" value=\"2\">\n";
    $arrTemplate['FORM_CONTENT'] .= '<input type="image" src="images/next.png" value="Submit" title="next" '
        . 'alt="next"><br>' .$myInstClass->translate('Next')."\n";
}
//
// Write content
// =============
$strContent = $myInstClass->parseTemplate($arrTemplate, $preIncludeContent);
echo $strContent;
