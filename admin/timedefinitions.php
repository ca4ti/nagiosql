<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2012 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Admin time definition list
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2012-02-27 13:01:17 +0100 (Mon, 27 Feb 2012) $
// Author    : $LastChangedBy: martin $
// Version   : 3.2.0
// Revision  : $LastChangedRevision: 1257 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$preAccess  	= 1;
//$prePageId		= 16;
$preNoMain  	= 1;
//
// Include preprocessing file
// ==========================
require("../functions/prepend_adm.php");
//
// Process post parameters
// =======================
$chkTipId   	= (isset($_GET['tipId']) && ($_GET['tipId'] != ""))   ? htmlspecialchars($_GET['tipId'], ENT_QUOTES, 'utf-8')   : 0;
$chkMode    	= isset($_GET['mode'])    ? htmlspecialchars($_GET['mode'], ENT_QUOTES, 'utf-8')	: "";
$chkDef     	= isset($_GET['def'])     ? html_entity_decode($_GET['def'])      					: "";
$chkRange   	= isset($_GET['range'])   ? html_entity_decode($_GET['range'])    					: "";
$chkId      	= isset($_GET['id'])      ? htmlspecialchars($_GET['id'], ENT_QUOTES, 'utf-8')		: "";
$chkVersion 	= isset($_GET['version']) ? htmlspecialchars($_GET['version'], ENT_QUOTES, 'utf-8')	: 0;
if (get_magic_quotes_gpc() == 0) {
  $chkDef   = addslashes($chkDef);
  $chkRange = addslashes($chkRange);
}
//
// Get data
// ========
$strSQL    = "SELECT * FROM `tbl_timedefinition` WHERE `tipId` = $chkTipId ORDER BY `definition`";
$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
//
// Store data to session
// ============================
if ($chkMode == "") {
	$_SESSION['timedefinition'] = "";
	if ($booReturn && ($intDataCount != 0)) {
		foreach ($arrDataLines AS $elem) {
			$arrTemp['id']            = $elem['id'];
			$arrTemp['definition']    = addslashes($elem['definition']);
			$arrTemp['range']         = addslashes($elem['range']);
			$arrTemp['status']        = 0;
			$_SESSION['timedefinition'][]   = $arrTemp;
		}
	}
}
//
// Add mode
// =========
if ($chkMode == "add") {
  	if (isset($_SESSION['timedefinition']) && is_array($_SESSION['timedefinition'])) {
    	$intCheck = 0;
    	foreach ($_SESSION['timedefinition'] AS $key => $elem) {
      		if (($elem['definition'] == $chkDef) && ($elem['status'] == 0)) {
        		$_SESSION['timedefinition'][$key]['definition'] = $chkDef;
        		$_SESSION['timedefinition'][$key]['range'] = $chkRange;
        		$intCheck = 1;
      		}
    	}
    	if ($intCheck == 0) {
      		$arrTemp['id'] = 0;
      		$arrTemp['definition'] = $chkDef;
      		$arrTemp['range'] = $chkRange;
      		$arrTemp['status'] = 0;
      		$_SESSION['timedefinition'][] = $arrTemp;
    	}
  	} else {
    	$arrTemp['id'] = 0;
    	$arrTemp['definition'] = $chkDef;
    	$arrTemp['range'] = $chkRange;
    	$arrTemp['status'] = 0;
    	$_SESSION['timedefinition'][] = $arrTemp;
  	}
}
//
// Deletion mode
// =============
if ($chkMode == "del") {
	if (isset($_SESSION['timedefinition']) && is_array($_SESSION['timedefinition'])) {
		foreach ($_SESSION['timedefinition'] AS $key => $elem) {
			if (($elem['definition'] == $chkDef) && ($elem['status'] == 0)) {
				$_SESSION['timedefinition'][$key]['status'] = 1;
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
	<link href="<?php echo $_SESSION['SETS']['path']['base_url']; ?>config/main.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" language="javascript">
  		<!--
  		function doEdit(key,range) {
<?php
if ($chkVersion == 3) {
?>
  			parent.document.frmDetail.txtTimedefinition.value = decodeURIComponent(key);
  			parent.document.frmDetail.txtTimerange2.value = decodeURIComponent(range);
<?php
} else {
?>
			if (key == "monday") {
				parent.document.frmDetail.selTimedefinition.selectedIndex = 0;
			} else if (key == "tuesday") {
				parent.document.frmDetail.selTimedefinition.selectedIndex = 1;
			} else if (key == "wednesday") {
				parent.document.frmDetail.selTimedefinition.selectedIndex = 2;
			} else if (key == "thursday") {
				parent.document.frmDetail.selTimedefinition.selectedIndex = 3;
			} else if (key == "friday") {
				parent.document.frmDetail.selTimedefinition.selectedIndex = 4;
			} else if (key == "saturday") {
				parent.document.frmDetail.selTimedefinition.selectedIndex = 5;
			} else if (key == "sunday") {
				parent.document.frmDetail.selTimedefinition.selectedIndex = 6;
			}
			parent.document.frmDetail.txtTimerange1.value = range;
<?php
}
?>
  		}
  		function doDel(key) {
    		document.location.href = "<?php echo $_SESSION['SETS']['path']['base_url']; ?>admin/timedefinitions.php?tipId=<?php echo $chkTipId; ?>&mode=del&def="+key;
  		}
  		//-->
	</script>
	<style type="text/css">
  		.tablerow {
			border-bottom:1px solid #009900;
			font-size:12px;
			height:20px;
			padding-top:2px;
			padding-left:5px;
			padding-right:5px;
  		}
	</style>
</head>
<body style="margin:0">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
<?php
if (isset($_SESSION['timedefinition']) && is_array($_SESSION['timedefinition']) && (count($_SESSION['timedefinition']) != 0)) {
	foreach($_SESSION['timedefinition'] AS $elem) {
		if ($elem['status'] == 0) {
?>
		<tr>
            <td class="tablerow" style="padding-bottom:2px; width:260px"><?php echo htmlentities(stripslashes($elem['definition']),ENT_COMPAT,'UTF-8'); ?></td>
            <td class="tablerow" style="padding-bottom:2px; width:260px"><?php echo htmlentities(stripslashes($elem['range']),ENT_COMPAT,'UTF-8'); ?></td>
            <td class="tablerow" style="width:50px" align="right"><img src="<?php echo $_SESSION['SETS']['path']['base_url'];?>images/edit.gif" width="18" height="18" alt="<?php echo translate('Modify');?>" title="<?php echo translate('Modify'); ?>" onClick="doEdit('<?php echo rawurlencode(stripslashes($elem['definition']));?>','<?php echo rawurlencode(stripslashes($elem['range'])); ?>')" style="cursor:pointer">&nbsp;<img src="<?php echo $_SESSION['SETS']['path']['base_url']; ?>images/delete.gif" width="18" height="18" alt="<?php echo translate('Delete');?>" title="<?php echo translate('Delete');?>" onClick="doDel('<?php echo rawurlencode(stripslashes($elem['definition'])); ?>')" style="cursor:pointer"></td>
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