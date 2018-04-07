<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project   : NagiosQL
// Component : Supportive functions
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2010-10-25 15:45:55 +0200 (Mo, 25 Okt 2010) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.0.4
// Revision  : $LastChangedRevision: 827 $
// SVN-ID    : $Id: supportive.php 827 2010-10-25 13:45:55Z rouven $
//
///////////////////////////////////////////////////////////////////////////////

// Replacement for builtin parse_ini_file
function parseIniFile($iIniFile) {
  $aResult  =
  $aMatches = array();
  $a = &$aResult;
  $s = '\s*([[:alnum:]_\- \*]+?)\s*'; preg_match_all('#^\s*((\['.$s.'\])|(("?)'.$s.'\\5\s*=\s*("?)(.*?)\\7))\s*(;[^\n]*?)?$#ms', @file_get_contents($iIniFile), $aMatches, PREG_SET_ORDER);
  foreach ($aMatches as $aMatch) {
    if (empty($aMatch[2]))
        $a [$aMatch[6]] = $aMatch[8];
      else  $a = &$aResult [$aMatch[3]];
  }
  return $aResult;
}
?>