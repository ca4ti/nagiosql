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
// Component : Admin time definition list
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
$preAccess  = 1;
$intSub     = 2;
$preNoMain  = 1;
require("../functions/prepend_adm.php");
//
// Process get parameters
// ======================
$chkDataId    = isset($_GET['dataId'])  ? htmlspecialchars($_GET['dataId'], ENT_QUOTES, 'utf-8')   : 0;
$chkMode      = isset($_GET['mode'])    ? htmlspecialchars($_GET['mode'], ENT_QUOTES, 'utf-8')     : "";
$chkUser      = isset($_GET['user'])    ? htmlspecialchars($_GET['user'], ENT_QUOTES, 'utf-8')     : "";
$chkRights    = isset($_GET['rights'])  ? htmlspecialchars($_GET['rights'], ENT_QUOTES, 'utf-8')   : "";
$chkId        = isset($_GET['id'])      ? htmlspecialchars($_GET['id'], ENT_QUOTES, 'utf-8')       : "";
$chkVersion   = isset($_GET['version']) ? htmlspecialchars($_GET['version'], ENT_QUOTES, 'utf-8')  : 0;
$chkLinkTab   = isset($_GET['linktab']) ? htmlspecialchars($_GET['linktab'], ENT_QUOTES, 'utf-8')  : "";
if (substr_count($chkRights,"-")) {
	$arrRights = explode("-",$chkRights);
	$strRights = "";
	if ($arrRights[0] == 1) $strRights .= "READ,";
	if ($arrRights[1] == 1) $strRights .= "WRITE,";
	if ($arrRights[2] == 1) $strRights .= "LINK,";
	if ($strRights != "")     $strRights = substr($strRights,0,-1);
	$chkRights = $strRights;
}
if (get_magic_quotes_gpc() == 0) {
	$chkUser     = addslashes($chkUser);
	$chkRights   = addslashes($chkRights);
}
//
// Get datasets
// ============
if ($chkLinkTab != "") {
	$strSQL    = "SELECT * FROM `tbl_user` LEFT JOIN `".$chkLinkTab."` ON `id`=`idSlave` WHERE `idMaster`=$chkDataId ORDER BY `username`";
	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
	//
	// Write data to session
	// =====================
	if ($chkMode == "") {
		$_SESSION['groupuser'] = "";
		if ($intDataCount != 0) {
			foreach ($arrDataLines AS $elem) {
				$arrTemp['id']    = $elem['id'];
				$arrTemp['user']  = $elem['id'];
				$strRights = "";
				if ($elem['read']  == 1) 	$strRights .= "READ,";
				if ($elem['write'] == 1) 	$strRights .= "WRITE,";
				if ($elem['link']  == 1) 	$strRights .= "LINK,";
				if ($strRights != "")     	$strRights = substr($strRights,0,-1);
				$arrTemp['rights']       = 	$strRights;
				$arrTemp['status']       = 	0;
				$_SESSION['groupuser'][] = 	$arrTemp;
			}
		}
	}
}
//
// Add mode
// ========
if ($chkMode == "add") {
	if (isset($_SESSION['groupuser']) && is_array($_SESSION['groupuser'])) {
		$intCheck = 0;
		foreach ($_SESSION['groupuser'] AS $key => $elem) {
			if (($elem['user'] == $chkUser) && ($elem['status'] == 0)) {
				$_SESSION['groupuser'][$key]['user'] = $chkUser;
				$_SESSION['groupuser'][$key]['rights'] = $chkRights;
				$intCheck = 1;
			}
		}
		if ($intCheck == 0) {
			$arrTemp['id'] = 0;
			$arrTemp['user'] = $chkUser;
			$arrTemp['rights'] = $chkRights;
			$arrTemp['status'] = 0;
			$_SESSION['groupuser'][] = $arrTemp;
		}
	} else {
		$arrTemp['id'] = 0;
		$arrTemp['user'] = $chkUser;
		$arrTemp['rights'] = $chkRights;
		$arrTemp['status'] = 0;
		$_SESSION['groupuser'][] = $arrTemp;
	}
}
//
// Deletion mode
// =============
if ($chkMode == "del") {
	if (isset($_SESSION['groupuser']) && is_array($_SESSION['groupuser'])) {
		foreach ($_SESSION['groupuser'] AS $key => $elem) {
			if (($elem['user'] == $chkUser) && ($elem['status'] == 0)) {
				$_SESSION['groupuser'][$key]['status'] = 1;
			}
		}
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>None</title>
		<link href="<?php echo $SETS['path']['root']; ?>config/main.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" language="javascript">
  			<!--
  			function doDel(key) {
    			document.location.href = "<?php echo $SETS['path']['root']; ?>admin/groupusers.php?dataId=<?php echo $chkDataId; ?>&mode=del&user="+key;
  			}
  			//-->
		</script>
	</head>
	<body style="margin:0">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
<?php
  if (isset($_SESSION['groupuser']) && is_array($_SESSION['groupuser']) && (count($_SESSION['groupuser']) != 0)) {
    foreach($_SESSION['groupuser'] AS $elem) {
      if ($elem['status'] == 0) {
		  $strUser = $myDBClass->getFieldData("SELECT username FROM tbl_user WHERE id=".$elem['user']);
?>
            <tr>
                <td class="tablerow" style="padding-bottom:2px; width:260px"><?php echo $strUser; ?></td>
                <td class="tablerow" style="padding-bottom:2px; width:260px"><?php echo htmlspecialchars(stripslashes($elem['rights']),ENT_COMPAT,'UTF-8'); ?></td>
                <td class="tablerow" style="width:50px" align="right"><img src="<?php echo $SETS['path']['root']; ?>images/delete.gif" width="18" height="18" alt="<?php echo translate('Delete'); ?>" title="<?php echo translate('Delete'); ?>" onClick="doDel('<?php echo $elem['user']; ?>')" style="cursor:pointer"></td>
            </tr>
<?php
      }
    }
  } else {
?>
            <tr>
                <td class="tablerow"><?php echo translate('No data'); ?></td>
                <td class="tablerow">&nbsp;</td>
                <td class="tablerow" align="right">&nbsp;</td>
            </tr>
<?php
  }
?>
		</table>
	</body>
</html>