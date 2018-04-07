<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2017 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Template definition list
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2017-06-22 09:29:35 +0200 (Thu, 22 Jun 2017) $
// Author    : $LastChangedBy: martin $
// Version   : 3.3.0
// Revision  : $LastChangedRevision: 2 $
//
///////////////////////////////////////////////////////////////////////////////
//
// Define common variables
// =======================
$preAccess  	= 1;
$preNoMain  	= 1;
//
// Include preprocessing file
// ==========================
require("../functions/prepend_adm.php");
//
// Process post parameters
// =======================
$chkDataId  	= isset($_GET['dataId'])  ? htmlspecialchars($_GET['dataId'], ENT_QUOTES, 'utf-8') : 0;
$chkMode    	= isset($_GET['mode'])    ? htmlspecialchars($_GET['mode'], ENT_QUOTES, 'utf-8')   : "";
$chkKey     	= isset($_GET['key'])     ? htmlspecialchars($_GET['key'], ENT_QUOTES, 'utf-8')    : "";
$chkDef     	= isset($_GET['def'])     ? htmlspecialchars($_GET['def'], ENT_QUOTES, 'utf-8')    : "";
$chkType    	= isset($_GET['type'])    ? htmlspecialchars($_GET['type'], ENT_QUOTES, 'utf-8')   : "";
if ($chkDataId == "") $chkDataId = 0;
if (get_magic_quotes_gpc() == 0) {
  	$chkDef   = addslashes($chkDef);
}
$arrDefinition = explode("::",$chkDef);
if ($chkType == "") {
  	exit;
}
if ($chkType == "host") {
  	$chkLinkTab = "tbl_lnkHostToHosttemplate";
  	$chkPreTab  = "host";
}
if ($chkType == "hosttemplate") {
  	$chkLinkTab = "tbl_lnkHosttemplateToHosttemplate";
  	$chkPreTab  = "host";
}
if ($chkType == "service") {
  	$chkLinkTab = "tbl_lnkServiceToServicetemplate";
  	$chkPreTab  = "service";
}
if ($chkType == "servicetemplate") {
  	$chkLinkTab = "tbl_lnkServicetemplateToServicetemplate";
  	$chkPreTab  = "service";
}
if ($chkType == "contact") {
  	$chkLinkTab = "tbl_lnkContactToContacttemplate";
  	$chkPreTab  = "contact";
}
if ($chkType == "contacttemplate") {
  	$chkLinkTab = "tbl_lnkContacttemplateToContacttemplate";
  	$chkPreTab  = "contact";
}
//
// Get data
// ========
if ($chkLinkTab != "") {
  	$strSQL    = "SELECT * FROM `".$chkLinkTab."` WHERE `idMaster` = $chkDataId ORDER BY `idSort`";
  	$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
  	//
  	// Store data to session
  	// ============================
  	if ($chkMode == "") {
    	$_SESSION['templatedefinition'] = "";
		if ($booReturn && ($intDataCount != 0)) {
			foreach ($arrDataLines AS $elem) {
				if ($elem['idTable'] == 1) {
					$strSQL2 = "SELECT `template_name` FROM `tbl_".$chkPreTab."template` WHERE `id` = ".$elem['idSlave'];
					$strSQL3 = "SELECT `active` FROM `tbl_".$chkPreTab."template` WHERE `id` = ".$elem['idSlave'];
				} else {
					$strSQL2 = "SELECT `name` FROM `tbl_".$chkPreTab."` WHERE `id` = ".$elem['idSlave'];
					$strSQL3 = "SELECT `active` FROM `tbl_".$chkPreTab."` WHERE `id` = ".$elem['idSlave'];
				}
				$arrTemp['idSlave']       			= $elem['idSlave'];
				$arrTemp['definition']      		= addslashes($myDBClass->getFieldData($strSQL2));
				$arrTemp['idTable']       			= $elem['idTable'];
				$arrTemp['idSort']       			= $elem['idSort'];
				$arrTemp['active']       			= $myDBClass->getFieldData($strSQL3)+0;
				$arrTemp['status']        			= 0;
				$_SESSION['templatedefinition'][]   = $arrTemp;
			}
		}
	}
}
//
// Add mode
// ========
if ($chkMode == "add") {
  	if ($arrDefinition[1] == 1) {
    	$strSQL2 = "SELECT `template_name` FROM `tbl_".$chkPreTab."template` WHERE `id` = ".$arrDefinition[0];
		$strSQL3 = "SELECT `active` FROM `tbl_".$chkPreTab."template` WHERE `id` = ".$arrDefinition[0];
  	} else {
    	$strSQL2 = "SELECT `name` FROM `tbl_".$chkPreTab."` WHERE `id` = ".$arrDefinition[0];
		$strSQL3 = "SELECT `active` FROM `tbl_".$chkPreTab."` WHERE `id` = ".$arrDefinition[0];
  	}
  	if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition'])) {
    	$intCheck = 0;
    	foreach ($_SESSION['templatedefinition'] AS $key => $elem) {
      		if (($elem['idSlave'] == $arrDefinition[0]) && ($elem['idTable'] == $arrDefinition[1]) && ($elem['status'] == 0)) {
        		$intCheck = 1;
      		}
    	}
    	if ($intCheck == 0) {
			$arrTemp['idSlave'] = $arrDefinition[0];
			$arrTemp['definition']  = addslashes($myDBClass->getFieldData($strSQL2));
			$arrTemp['idTable'] = $arrDefinition[1];
			$arrTemp['idSort']  = 0;
			$arrTemp['status'] = 0;
			$arrTemp['active'] = $myDBClass->getFieldData($strSQL3)+0;
			$_SESSION['templatedefinition'][] = $arrTemp;
    	}
  	} else {
		$arrTemp['idSlave'] = $arrDefinition[0];
		$arrTemp['definition']  = addslashes($myDBClass->getFieldData($strSQL2));
		$arrTemp['idTable'] = $arrDefinition[1];
		$arrTemp['idSort']  = 0;
		$arrTemp['status'] = 0;
		$arrTemp['active'] = $myDBClass->getFieldData($strSQL3)+0;
		$_SESSION['templatedefinition'][] = $arrTemp;
  	}
}
//
// Deletion mode
// =============
if ($chkMode == "del") {
	if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition'])) {
		foreach ($_SESSION['templatedefinition'] AS $key => $elem) {
			if (($elem['idSlave'] == $arrDefinition[0]) && ($elem['idTable'] == $arrDefinition[1]) && ($elem['status'] == 0)) {
				$_SESSION['templatedefinition'][$key]['status'] = 1;
			}
		}
	}
}
//
// Sort mode
// =========
if ($chkMode == "sortup") {
	if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition'])) {
		$arrTemp = "";
		$arrWait = "";
		$intNow  = 0;
		foreach ($_SESSION['templatedefinition'] AS $key => $elem) {
			if ($key != ($chkKey-1)) {
				$arrTemp[] = $elem;
				if ($intNow == 1) {
					$intNow = 0;
					$arrTemp[] = $arrWait;
				}
			} else {
				$arrWait = $elem;
				$intNow  = 1;
			}
		}
		$_SESSION['templatedefinition'] = $arrTemp;
	}
}
if ($chkMode == "sortdown") {
  	if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition'])) {
    	$arrTemp = "";
    	$arrWait = "";
    	$intNow  = 0;
    	foreach ($_SESSION['templatedefinition'] AS $key => $elem) {
      		if ($key != $chkKey) {
        		$arrTemp[] = $elem;
        		if ($intNow == 1) {
          			$intNow = 0;
          			$arrTemp[] = $arrWait;
        		}
      		} else {
        		$arrWait = $elem;
        		$intNow  = 1;
      		}
    	}
    	$_SESSION['templatedefinition'] = $arrTemp;
  	}
}
//
// Clean up data structure
// =======================
if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition'])) {
  	$arrTemp = "";
  	foreach ($_SESSION['templatedefinition'] AS $key => $elem) {
    	if ($elem['status'] == 0) {
      		$arrTemp[] = $elem;
    	}
  	}
  	$_SESSION['templatedefinition'] = $arrTemp;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>None</title>
	<link href="<?php echo $_SESSION['SETS']['path']['base_url']?>config/main.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" language="javascript">
  		<!--
  		function doDel(key) {
    		document.location.href = "<?php echo $_SESSION['SETS']['path']['base_url']; ?>admin/templatedefinitions.php?dataId=<?php echo $chkDataId; ?>&type=<?php echo $chkType; ?>&mode=del&def="+key;
  		}
  		function doUp(key,elem) {
    		document.location.href = "<?php echo $_SESSION['SETS']['path']['base_url']; ?>admin/templatedefinitions.php?dataId=<?php echo $chkDataId; ?>&type=<?php echo $chkType; ?>&mode=sortup&key="+key+"def="+elem;
  		}
  		function doDown(key,elem) {
    		document.location.href = "<?php echo $_SESSION['SETS']['path']['base_url']; ?>admin/templatedefinitions.php?dataId=<?php echo $chkDataId; ?>&type=<?php echo $chkType; ?>&mode=sortdown&key="+key+"def="+elem;
  		}
  		//-->
	</script>
</head>
<body style="margin:0">
	<table cellpadding="0" cellspacing="0" border="0" width="100%">
<?php
  if (isset($_SESSION['templatedefinition']) && is_array($_SESSION['templatedefinition']) && (count($_SESSION['templatedefinition']) != 0)) {
    foreach($_SESSION['templatedefinition'] AS $key => $elem) {
      if ($elem['status'] == 0) {
?>
  		<tr>
      		<td class="tablerow" style="padding-bottom:2px;"><?php echo htmlspecialchars(stripslashes($elem['definition']),ENT_COMPAT,'UTF-8'); if ($elem['active'] == 0) echo " [inactive]"; ?></td>
        	<td class="tablerow" align="right"><img src="<?php echo $_SESSION['SETS']['path']['base_url']; ?>images/up.gif" width="18" height="18" alt="<?php echo translate('Up');?>" title="<?php echo translate('Up');?>" onClick="doUp('<?php echo $key; ?>','<?php echo $elem['idSlave']."::".$elem['idTable']; ?>')" style="cursor:pointer">&nbsp;<img src="<?php echo $_SESSION['SETS']['path']['base_url']; ?>images/down.gif" width="18" height="18" alt="<?php echo translate('Down'); ?>" title="<?php echo translate('Down'); ?>" onClick="doDown('<?php echo $key; ?>','<?php echo $elem['idSlave']."::".$elem['idTable']; ?>')" style="cursor:pointer">&nbsp;<img src="<?php echo $_SESSION['SETS']['path']['base_url']; ?>images/delete.gif" width="18" height="18" alt="<?php echo translate('Delete'); ?>" title="<?php echo translate('Delete'); ?>" onClick="doDel('<?php echo $elem['idSlave']."::".$elem['idTable']; ?>')" style="cursor:pointer"></td>
		</tr>
<?php
      }
    }
  } else {
?>
		<tr>
			<td class="tablerow"><?php echo translate('No data'); ?></td>
			<td class="tablerow" align="right">&nbsp;</td>
		</tr>
<?php
  }
?>
	</table>
</body>
</html>