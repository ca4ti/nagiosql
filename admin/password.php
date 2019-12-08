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
// Component : Password administration
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Path settings
// ===================
$strPattern = '(admin/[^/]*.php)';
$preRelPath  = preg_replace($strPattern, '', filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING));
$preBasePath = preg_replace($strPattern, '', filter_input(INPUT_SERVER, 'SCRIPT_FILENAME', FILTER_SANITIZE_STRING));
//
// Define common variables
// =======================
$prePageId     = 31;
$preContent    = 'admin/password.htm.tpl';
$preAccess     = 1;
$preFieldvars  = 1;
$preShowHeader = 0;
//
// Include preprocessing files
// ===========================
require $preBasePath.'functions/prepend_adm.php';
require $preBasePath.'functions/prepend_content.php';
//
// Change password
// =======================
if (($chkTfValue1 != '') && ($chkTfValue2 != '')) {
    // Check old password
    $strSQL    = 'SELECT * FROM `tbl_user` '
               . "WHERE `username`='".$_SESSION['username']."' AND `password`=MD5('$chkTfValue1')";
    $booReturn = $myDBClass->hasDataArray($strSQL, $arrDataLines, $intDataCount);
    if ($booReturn == false) {
        $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
        $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
    } elseif ($intDataCount == 1) {
        // Check equality and password length
        if (($chkTfValue2 === $chkTfValue3) && (strlen($chkTfValue2) >=5)) {
            // Update database
            $strSQLUpdate = "UPDATE `tbl_user` SET `password`=MD5('$chkTfValue2'), `last_login`=NOW() "
                          . "WHERE `username`='".$_SESSION['username']."'";
            $booReturn = $myDBClass->insertData($strSQLUpdate);
            if ($booReturn == true) {
                $myDataClass->writeLog(translate('Password successfully modified'));
                // Force new login
                $_SESSION['logged_in'] = 0;
                $_SESSION['username']  = '';
                $_SESSION['userid']    = 0;
                $_SESSION['groupadm']  = 0;
                $_SESSION['domain']    = 0;
                header('Location: ' .$SETS['path']['protocol']. '://' .
                        filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING).
                        $_SESSION['SETS']['path']['base_url']. 'index.php');
            } else {
                $myVisClass->processMessage(translate('Error while selecting data from database:'), $strErrorMessage);
                $myVisClass->processMessage($myDBClass->strErrorMessage, $strErrorMessage);
            }
        } else {
            // New password wrong
            $myVisClass->processMessage(
                translate('Password too short or password fields do not match!'),
                $strErrorMessage
            );
        }
    } else {
        // Old password wrong
        $myVisClass->processMessage(translate('The old password is invalid'), $strErrorMessage);
    }
} elseif (filter_input(INPUT_POST, 'submit')) {
    // Wrong data
    $myVisClass->processMessage(
        translate('Database entry failed! Not all necessary data filled in!'),
        $strErrorMessage
    );
}
//
// Output header variable
// ======================
echo $tplHeaderVar;
//
// Include content
// ===============
foreach ($arrDescription as $elem) {
    $conttp->setVariable($elem['name'], $elem['string']);
}
$conttp->setVariable('LANG_SAVE', translate('Save'));
$conttp->setVariable('LANG_ABORT', translate('Abort'));
$conttp->setVariable('FILL_ALLFIELDS', translate('Please fill in all fields marked with an *'));
$conttp->setVariable('FILL_NEW_PASSWD_NOT_EQUAL', translate('The new passwords don not match!'));
$conttp->setVariable('FILL_NEW_PWDSHORT', translate('The new password is too short - use at least 6 characters!'));
if ($strErrorMessage != '') {
    $conttp->setVariable('ERRORMESSAGE', $strErrorMessage);
}
$conttp->setVariable('ACTION_INSERT', filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING));
$conttp->setVariable('IMAGE_PATH', $_SESSION['SETS']['path']['base_url']. 'images/');
// Check access rights for adding new objects
if ($myVisClass->checkAccountGroup($prePageKey, 'write') != 0) {
    $conttp->setVariable('ADD_CONTROL', 'disabled="disabled"');
}
$conttp->parse('passwordsite');
$conttp->show('passwordsite');
//
// Include footer
// ==============
$maintp->setVariable('VERSION_INFO', "<a href='https://sourceforge.net/projects/nagiosql/' "
                   . "target='_blank'>NagiosQL</a> $setFileVersion");
$maintp->parse('footer');
$maintp->show('footer');
