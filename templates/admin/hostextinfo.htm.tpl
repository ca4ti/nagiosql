<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : hosteextinfo template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datainsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
	<script type="text/JavaScript">
		<!--
		// Interrupt input
		function abort() {
			this.location.href = "{ACTION_INSERT}?limit={LIMIT}";
		}
    	// Send form
        /**
         * @return {boolean}
         */
        function LockButton() {
			if (checkForm() === false) {
				return false;
			} else {
				// Enable select fields
				document.frmDetail.submit();
				document.frmDetail.subForm.disabled = true;
			}
		}
    	// Check form entries
    	function checkForm() {
      		// Are all required fields filled in?
            const fields1 = "selValue1";
            const msg1 = "{FILL_ALLFIELDS}";
            const header = "{LANG_FORMCHECK}";
            const form = document.frmDetail;
            let check = checkfields2(fields1,form,myFocusObject);
			if (check === false) {
	  			msginit(msg1,header,1);
				return false;
	  		}	
    	}
    	//-->
  	</script>
  	<form name="frmDetail" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1">{LANG_HOST_NAME} *</td>
                <td class="content_tbl_row2">
                    <select title="{LANG_HOST_NAME}" name="selValue1" id="selValue1" class="selectbordermust inpmust" tabindex="1">
<!-- BEGIN host -->
                        <option value="{DAT_HOST_ID}" class="empty_class inpmust {SPECIAL_STYLE}" {DAT_HOST_SEL}>{DAT_HOST}</option>
<!-- END host -->
                    </select>
                </td>
                <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostextinfo','host_name','all','Info');" class="infobutton_1"></td>
                <td class="content_tbl_row1">{LANG_ICON_IMAGE}</td>
                <td class="content_tbl_row2"><input title="{LANG_ICON_IMAGE}" name="tfValue4" type="text" id="tfValue4" value="{DAT_ICON_IMAGE}" tabindex="7"></td>
                <td class="content_tbl_row4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostextinfo','icon_image','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_NOTES}</td>
                <td><input title="{LANG_NOTES}" name="tfValue1" type="text" id="tfValue1" value="{DAT_NOTES}" tabindex="2"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostextinfo','notes','all','Info');" class="infobutton_1"></td>
                <td>{LANG_ICON_IMAGE_ALT_TEXT}</td>
                <td><input title="{LANG_ICON_IMAGE_ALT_TEXT}" name="tfValue5" type="text" id="tfValue5" value="{DAT_ICON_IMAGE_ALT}" tabindex="8"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostextinfo','icon_image_alt_text','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_NOTES_URL}</td>
                <td><input title="{LANG_NOTES_URL}" name="tfValue2" type="text" id="tfValue2" value="{DAT_NOTES_URL}" tabindex="3"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostextinfo','notes_url','all','Info');" class="infobutton_1"></td>
                <td>{LANG_VRML_IMAGE}</td>
                <td><input title="{LANG_VRML_IMAGE}" name="tfValue6" type="text" id="tfValue6" value="{DAT_VRML_IMAGE}" tabindex="9"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostextinfo','vrml_image','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_ACTION_URL}</td>
                <td><input title="{LANG_ACTION_URL}" name="tfValue3" type="text" id="tfValue3" value="{DAT_ACTION_URL}" tabindex="4"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostextinfo','action_url','all','Info');" class="infobutton_1"></td>
                <td>{LANG_STATUS_IMAGE}</td>
                <td><input title="{LANG_STATUS_IMAGE}" name="tfValue7" type="text" id="tfValue7" value="{DAT_STATUSMAP_IMAGE}" tabindex="10"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostextinfo','status_image','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_REGISTERED}</td>
                <td><input title="{LANG_REGISTERED}" name="chbRegister" type="checkbox" class="checkbox" id="chbRegister" value="1" {REG_CHECKED} tabindex="5"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','registered','all','Info');" class="infobutton_1"></td>
                <td>{LANG_2D_COORDS}</td>
                <td><input title="{LANG_2D_COORDS}" name="tfValue8" type="text" id="tfValue8" value="{DAT_2D_COORDS}" class="short" tabindex="11"><span class="shorttext">(x,y)</span></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostextinfo','2d_coords','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_ACTIVE}</td>
                <td colspan="2"><input title="{LANG_ACTIVE}" name="chbActive" type="checkbox" class="checkbox" id="chbActive" value="1" {ACT_CHECKED} tabindex="6">
                  <input name="modus" type="hidden" id="modus" value="{MODUS}">
                  <input name="hidId" type="hidden" id="hidId" value="{DAT_ID}">
                <input name="hidLimit" type="hidden" id="hidLimit" value="{LIMIT}"></td>
                <td>{LANG_3D_COORDS}</td>
                <td><input title="{LANG_3D_COORDS}" name="tfValue9" type="text" id="tfValue9" value="{DAT_3D_COORDS}" class="short" tabindex="12"><span class="shorttext">(x,y,z)</span></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostextinfo','3d_coords','all','Info');" class="infobutton_1"> </td>
            </tr>
            <tr {RESTRICT_GROUP_ADMIN}>
            	<td colspan="3">&nbsp;</td>
                <td>{LANG_ACCESS_GROUP}</td>
                <td>
                    <select title="{LANG_ACCESS_GROUP}" name="selAccGr" class="selectborder" tabindex="13">
<!-- BEGIN acc_group -->
                        <option value="{DAT_ACC_GROUP_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_ACC_GROUP_SEL}>{DAT_ACC_GROUP}</option>
<!-- END acc_group -->
                    </select>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','accessgroup','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><input name="subForm" type="button" id="subForm" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE} tabindex="14">&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{LANG_ABORT}" tabindex="15"><span class="required_info">* {LANG_REQUIRED}</span></td>
                <td colspan="3" class="redmessage">{WARNING}</td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
        </table>
	</form>
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->