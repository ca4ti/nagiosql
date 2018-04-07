<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project  : NagiosQL
// Component: Installer (Step Buttons)
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
// Text to Array
$steptext[1]=translate('Requirements');
$steptext[2]=translate($_SESSION['SETS']['install']['InstallType']);
$steptext[3]=translate('Finish');

// New Installation Menu
for ($steps=1;$steps<4;$steps++) {
  if ($_SESSION['SETS']['install']['step'] == $steps) {
    echo "<p class='step".$steps."_active'><br><br>".$steptext[$steps]."</p>";
  } else {
    if ($_SESSION['SETS']['install']['step'] > $steps) {
      echo "<p class='step".$steps."_active'><a href='install.php?step=".$steps."'><br><br>".$steptext[$steps]."</a></p>";
    } else {
      echo "<p class='step".$steps."_deactive'><br><br>".$steptext[$steps]."</p>";
    }
  }
}
?>
