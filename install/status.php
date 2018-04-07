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
// Date     : $LastChangedDate: 2010-10-25 15:45:55 +0200 (Mo, 25 Okt 2010) $
// Author   : $LastChangedBy: rouven $
// Version  : 3.0.4
// Revision : $LastChangedRevision: 827 $
//
///////////////////////////////////////////////////////////////////////////////

// Text to Array
$steptext[1]=gettext('Requirements');
$steptext[2]=gettext($_SESSION['InstallType']);
$steptext[3]=gettext('Finish');

// New Installation Menu
if( eregi(basename(__FILE__),$_SERVER['PHP_SELF']) ) {
  die("You can't access this file directly!");
}
for ($steps=1;$steps<4;$steps++) {
  if ($_SESSION['step'] == $steps) {
    echo "<p class='step".$steps."_active'><br><br>".$steptext[$steps]."</p>";
  } else {
    if ($_SESSION['step'] > $steps) {
      echo "<p class='step".$steps."_active'><a href='install.php?step=".$steps."'><br><br>".$steptext[$steps]."</a></p>";
    } else {
      echo "<p class='step".$steps."_deactive'><br><br>".$steptext[$steps]."</p>";
    }
  }
}
?>
