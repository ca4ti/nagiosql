<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : hostescalation template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datainsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
	<!--suppress JSUnresolvedVariable -->
    <script type="text/JavaScript">
		<!--
    	// Initialize change dialog
		openMutDlgInit("mselValue1","mutdialogvalue1","{LANG_MODIFY_SELECTION}: {LANG_CONTACTS}","mutvalue1","{LANG_SAVE}","{LANG_ABORT}");
		openMutDlgInit("mselValue2","mutdialogvalue2","{LANG_MODIFY_SELECTION}: {LANG_CONTACT_GROUPS}","mutvalue2","{LANG_SAVE}","{LANG_ABORT}"); 
		openMutDlgInit("mselValue3","mutdialogvalue3","{LANG_MODIFY_SELECTION}: {LANG_HOSTS}","mutvalue3","{LANG_SAVE}","{LANG_ABORT}");
		openMutDlgInit("mselValue4","mutdialogvalue4","{LANG_MODIFY_SELECTION}: {LANG_HOSTGROUPS}","mutvalue4","{LANG_SAVE}","{LANG_ABORT}");
        const version = "{VERSION}";

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
                const selfields = "mselValue1,mselValue2,mselValue3,mselValue4";
                const ar_sel = selfields.split(",");
                for (let i=0;i<ar_sel.length;i++){
                    document.getElementById(ar_sel[i]).disabled = false;
                    for (let y=0;y<document.getElementById(ar_sel[i]).length;++y) {
                        document.getElementById(ar_sel[i]).options[y].disabled = false;
                    }
                }
				document.frmDetail.submit();
				document.frmDetail.subForm.disabled = true;
      		}
   		 }
    	// Check form entries
		function checkForm() {
      		// Are all required fields filled in?
            const fields1 = "tfValue1,tfNullVal1,tfNullVal2,tfNullVal3";
            const msg1 = "{FILL_ALLFIELDS}";
            const header = "{LANG_FORMCHECK}";
            const form = document.frmDetail;
            let check;
            check = checkfields(fields1,form,myFocusObject);
			if (check === false) {
				msginit(msg1,header,1);
				return false;
			}	
			if ((version >= 3) && (form.mselValue1.value === "") && (form.mselValue2.value === "")) {
				msginit(msg1,header,1);
				return false;
			}
			if ((version < 3) && (form.mselValue2.value === "")) {
				msginit(msg1,header,1);
				return false;
			}
			if ((form.mselValue3.value === "") && (form.mselValue4.value === "")) {
				msginit(msg1,header,1);
				return false;
			}
		}	
		//-->
	</script>
	<form name="frmDetail" method="post" action="{ACTION_INSERT}">
       <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1" valign="top">{LANG_HOSTS} (*)</td>
                <td class="content_tbl_row2" valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_HOSTS}" name="mselValue3[]" size="5" multiple id="mselValue3" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN host -->
                                    <option value="{DAT_HOST_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_HOST_SEL}" {DAT_HOST_SEL} {OPTION_DISABLED}>{DAT_HOST}</option>
<!-- END host -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="content_tbl_row3" valign="top" rowspan="2"><img id="mutvalue3" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostescalation','host','all','Info');" class="infobutton_2"></td>
                <td class="content_tbl_row1" valign="top">{LANG_HOSTGROUPS} (*)</td>
                <td class="content_tbl_row2" valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_HOSTGROUPS}" name="mselValue4[]" size="5" multiple id="mselValue4" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN hostgroup -->
                                    <option value="{DAT_HOSTGROUP_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_HOSTGROUP_SEL}" {DAT_HOSTGROUP_SEL} {OPTION_DISABLED}>{DAT_HOSTGROUP}</option>
<!-- END hostgroup -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="content_tbl_row4" valign="top" rowspan="2"><img id="mutvalue4" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostescalation','hostgroup','all','Info');" class="infobutton_2"></td>
            </tr>
            <tr>
                <td><small>{LANG_CTRLINFO}</small></td>
                <td><small>{LANG_CTRLINFO}</small></td>
            </tr>
            <tr>
                <td valign="top"><span class="{VERSION_30_VISIBLE}">{LANG_CONTACTS} (*)</span></td>
                <td valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0" class="{VERSION_30_VISIBLE}">
                        <tr>
                            <td>
                                <select title="{LANG_CONTACTS}" name="mselValue1[]" size="5" multiple id="mselValue1" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN contact -->
                                    <option value="{DAT_CONTACT_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_CONTACT_SEL}" {DAT_CONTACT_SEL} {OPTION_DISABLED}>{DAT_CONTACT}</option>
<!-- END contact -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td valign="top" rowspan="2"><span class="{VERSION_30_VISIBLE}"><img id="mutvalue1" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostescalation','contact','all','Info');" class="infobutton_2"></span></td>
                <td valign="top">{LANG_CONTACT_GROUPS} (*)</td>
                <td valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_CONTACT_GROUPS}" name="mselValue2[]" size="5" multiple id="mselValue2" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN contactgroup -->
                                    <option value="{DAT_CONTACTGROUP_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_CONTACTGROUP_SEL}" {DAT_CONTACTGROUP_SEL} {OPTION_DISABLED}>{DAT_CONTACTGROUP}</option>
<!-- END contactgroup -->
                                </select>
                            </td>
                        </tr>
                    </table>      
                </td>
                <td valign="top" rowspan="2"><img id="mutvalue2" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostescalation','contactgroup','all','Info');" class="infobutton_2"></td>
            </tr>
            <tr>
                <td><small>{LANG_CTRLINFO}</small></td>
                <td><small>{LANG_CTRLINFO}</small></td>
            </tr>
            <tr>
                <td>{LANG_CONFIG_NAME} *</td>
                <td><input title="{LANG_CONFIG_NAME}" name="tfValue1" type="text" id="tfValue1" value="{DAT_CONFIG_NAME}" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostescalation','config_name','all','Info');" class="infobutton_1"></td>
                <td>{LANG_ESCALATION_PERIOD}</td>
                <td>
                    <select title="{LANG_ESCALATION_PERIOD}" name="selValue1" id="selValue1" class="selectborder">
<!-- BEGIN escperiod -->
                        <option value="{DAT_ESCPERIOD_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_ESCPERIOD_SEL}>{DAT_ESCPERIOD}</option>
<!-- END escperiod -->
                    </select>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostescalation','escalation_period','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_FIRST_NOTIFICATION} *</td>
                <td><input title="{LANG_FIRST_NOTIFICATION}" name="tfNullVal1" type="text" value="{DAT_FIRST_NOTIFICATION}" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostescalation','first_notification','all','Info');" class="infobutton_1"></td>
                <td>{LANG_ESCALATION_OPTIONS}</td>
                <td>
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td class="radio_cell_1"><input title="d" name="chbGr1a" type="checkbox" class=" checkbox" id="chbGr1a" value="d" {DAT_EOD_CHECKED}></td>
                            <td class="radio_cell_1">d</td>
                            <td class="radio_cell_1"><input title="u" name="chbGr1b" type="checkbox" class=" checkbox" id="chbGr1b" value="u" {DAT_EOU_CHECKED}></td>
                            <td class="radio_cell_1">u</td>
                            <td class="radio_cell_1"><input title="r" name="chbGr1c" type="checkbox" class=" checkbox" id="chbGr1c" value="r" {DAT_EOR_CHECKED}></td>
                            <td class="radio_cell_1">r</td>
                        </tr>
                    </table>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostescalation','escalation_options','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_LAST_NOTIFICATION} *</td>
                <td valign="middle"><input title="{LANG_LAST_NOTIFICATION}" name="tfNullVal2" type="text" value="{DAT_LAST_NOTIFICATION}" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostescalation','last_notification','all','Info');" class="infobutton_1"></td>
                <td {RESTRICT_GROUP_ADMIN}>{LANG_ACCESS_GROUP}</td>
                <td {RESTRICT_GROUP_ADMIN}>
                    <select title="{LANG_ACCESS_GROUP}" name="selAccGr" class="selectborder">
<!-- BEGIN acc_group -->
                        <option value="{DAT_ACC_GROUP_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_ACC_GROUP_SEL}>{DAT_ACC_GROUP}</option>
<!-- END acc_group -->
                    </select>
                </td>
                <td {RESTRICT_GROUP_ADMIN}><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','accessgroup','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
              <td>{LANG_NOTIFICATION_INTERVAL} *</td>
              <td valign="middle"><input title="{LANG_NOTIFICATION_INTERVAL}" name="tfNullVal3" type="text" value="{DAT_NOTIFICATION_INTERVAL}" class="shortmust"><span class="shorttext">min</span></td>
              <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostescalation','notification_intervall','all','Info');" class="infobutton_1"></td>
              <td>{LANG_REGISTERED}</td>
              <td><input title="{LANG_REGISTERED}" name="chbRegister" type="checkbox" class="checkbox" id="chbRegister" value="1" {REG_CHECKED}></td>
              <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','registered','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
                <td>{LANG_ACTIVE}</td>
                <td colspan="2"><input title="{LANG_ACTIVE}" name="chbActive" type="checkbox" class="checkbox" id="chbActive" value="1" {ACT_CHECKED}>
                    <input name="modus" type="hidden" id="modus" value="{MODUS}">
                    <input name="hidId" type="hidden" id="hidId" value="{DAT_ID}">
                    <input name="hidLimit" type="hidden" id="hidLimit" value="{LIMIT}"></td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><input name="subForm" type="button" id="subForm" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE}>&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span></td>
                <td colspan="3"><span class="redmessage">{WARNING}</span></td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
        </table>
	</form>
</div>
<br>
<div id="mutdialogvalue1">
    <div id="mutdialogvalue1content" class="bd"></div>
</div>
<div id="mutdialogvalue2">
    <div id="mutdialogvalue2content" class="bd"></div>
</div>
<div id="mutdialogvalue3">
    <div id="mutdialogvalue3content" class="bd"></div>
</div>
<div id="mutdialogvalue4">
    <div id="mutdialogvalue4content" class="bd"></div>
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->