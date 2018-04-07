<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project   : NagiosQL
// Component : Search Hosts by IP, Hostname or Alias
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2010-10-25 15:45:55 +0200 (Mo, 25 Okt 2010) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.0.4
// Revision  : $LastChangedRevision: 827 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Vorgabedatei einbinden
// ======================
$preAccess  = 1;
$intSub     = 4; // TODO Submenu ID übergeben?
$preNoMain  = 1;
require("../functions/prepend_adm.php");
//
// Search Hosts
//
function search_escape($str, $char = '\\') {
    return ereg_replace('[%_]', $char . '\0', $str);
}
if(isset($_POST['strQueryString'])) {
  $strQueryString = search_escape($_POST['strQueryString']);
  if(strlen($strQueryString) >0) {
    $strSQLMain = "SELECT `id`, `host_name`, `alias`, `address` FROM `tbl_host` WHERE `host_name` LIKE '%$strQueryString%' OR `alias` LIKE '%$strQueryString%' OR `address` LIKE '%$strQueryString%' LIMIT 20";
    $booReturn = $myDBClass->getDataArray($strSQLMain,$arrDataMain,$intDataCountMain);
    if (($booReturn != false) && ($intDataCountMain != 0)) {
      $y=1;
      for ($i=0;$i<$intDataCountMain;$i++) {
        echo "<li><a href=\"javascript:actionPic('modify','".$arrDataMain[$i]['id']."','');\">".$arrDataMain[$i]['host_name']."</a></li>";
        $y++;
      }
    } else {
      return (1);
    }
  }
}
?>