<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : serviceescalation template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datainsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
	<!--suppress JSUnusedLocalSymbols, JSUnresolvedVariable -->
    <script type="text/JavaScript">
		<!--
    	// Initialize change dialog
		openMutDlgInit("mselValue1","mutdialogvalue1","{LANG_MODIFY_SELECTION}: {LANG_HOSTS}","mutvalue1","{LANG_SAVE}","{LANG_ABORT}");
		openMutDlgInit("mselValue2","mutdialogvalue2","{LANG_MODIFY_SELECTION}: {LANG_HOSTGROUPS}","mutvalue2","{LANG_SAVE}","{LANG_ABORT}");
		openMutDlgInit("mselValue3","mutdialogvalue3","{LANG_MODIFY_SELECTION}: {LANG_SERVICES}","mutvalue3","{LANG_SAVE}","{LANG_ABORT}");
		openMutDlgInit("mselValue4","mutdialogvalue4","{LANG_MODIFY_SELECTION}: {LANG_CONTACTS}","mutvalue4","{LANG_SAVE}","{LANG_ABORT}");
		openMutDlgInit("mselValue5","mutdialogvalue5","{LANG_MODIFY_SELECTION}: {LANG_CONTACT_GROUPS}","mutvalue5","{LANG_SAVE}","{LANG_ABORT}","1"); 
		openMutDlgInit("mselValue6","mutdialogvalue6","{LANG_MODIFY_SELECTION}: {LANG_SERVICEGROUPS}","mutvalue6","{LANG_SAVE}","{LANG_ABORT}","1");
        const version = "{VERSION}";
        const update = 1;
        // Process form update
		function updateForm(key) {
	  		if ((key !== 'mselValue3') && (key !== 'mselValue4') && (key !== 'mselValue5')) {
				document.frmDetail.modus.value = "refresh";
                const selfields = "mselValue1,mselValue2,mselValue3,mselValue4,mselValue5,mselValue6";
                const ar_sel = selfields.split(",");
                for (let i=0;i<ar_sel.length;i++){
                    document.getElementById(ar_sel[i]).disabled = false;
                    for (let y=0;y<document.getElementById(ar_sel[i]).length;++y) {
                        document.getElementById(ar_sel[i]).options[y].disabled = false;
                    }
                }
				document.frmDetail.submit();
	  		}
		}	
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
                const selfields = "mselValue1,mselValue2,mselValue3,mselValue4,mselValue5,mselValue6";
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
            let check = checkfields(fields1,form,myFocusObject);
			if (check === false) {
	  			msginit(msg1,header,1);
				return false;
	  		}	
			if ((version >= 3) && (form.mselValue4.value === "") && (form.mselValue5.value === "")) {
				msginit(msg1,header,1);
				return false;
			}
			if ((version < 3) && (form.mselValue5.value === "")) {
				msginit(msg1,header,1);
				return false;
			}
			if ((form.mselValue1.value === "") && (form.mselValue2.value === "") && (form.mselValue6.value === "")) {
				msginit(msg1,header,1);
				return false;
			}
			if ((form.mselValue3.value === "") && (form.mselValue6.value === "")) {
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
                                <select title="{LANG_HOSTS}" name="mselValue1[]" size="5" multiple id="mselValue1" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN host -->
                                    <option value="{DAT_HOST_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_HOST_SEL}" {DAT_HOST_SEL} {OPTION_DISABLED}>{DAT_HOST}</option>
<!-- END host -->
                                </select>
                            </td>
                        </tr>
                    </table>      
                </td>
                <td class="content_tbl_row3" valign="top" rowspan="2"><img id="mutvalue1" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceescalation','host','all','Info');" class="infobutton_2"></td>
                <td class="content_tbl_row2" valign="top">{LANG_CONTACT_GROUPS} (*)</td>
                <td class="content_tbl_row2" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_CONTACT_GROUPS}" name="mselValue5[]" size="5" multiple id="mselValue5" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN contactgroup -->
                                    <option value="{DAT_CONTACTGROUP_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_CONTACTGROUP_SEL}" {DAT_CONTACTGROUP_SEL} {OPTION_DISABLED}>{DAT_CONTACTGROUP}</option>
<!-- END contactgroup -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="content_tbl_row4" valign="top" rowspan="2"><img id="mutvalue5" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceescalation','contactgroup','all','Info');" class="infobutton_2"></td>
            </tr>
            <tr>
                <td><small>{LANG_CTRLINFO}</small></td>
                <td><small>{LANG_CTRLINFO}</small></td>
            </tr>
            <tr class="{VERSION_30_VISIBLE}">
                <td valign="top">{LANG_HOST_GROUPS} (*)</td>
                <td valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_HOST_GROUPS}" name="mselValue2[]" size="5" multiple id="mselValue2" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN hostgroup -->
                                    <option value="{DAT_HOSTGROUP_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_HOSTGROUP_SEL}" {DAT_HOSTGROUP_SEL} {OPTION_DISABLED}>{DAT_HOSTGROUP}</option>
<!-- END hostgroup -->
                                </select>
                            </td>
                        </tr>
                    </table>
				</td>
                <td valign="top" rowspan="2"><img id="mutvalue2" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceescalation','hostgroup','all','Info');" class="infobutton_2"></td>
                <td valign="top">{LANG_CONTACTS} (*)</td>
                <td valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_CONTACTS}" name="mselValue4[]" size="5" multiple id="mselValue4" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN contact -->
                                    <option value="{DAT_CONTACT_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_CONTACT_SEL}" {DAT_CONTACT_SEL} {OPTION_DISABLED}>{DAT_CONTACT}</option>
<!-- END contact -->
                                </select>            
                            </td>
                        </tr>
                    </table>
                </td>
                <td valign="top" rowspan="2"><img id="mutvalue4" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceescalation','contact','all','Info');" class="infobutton_2"></td>
            </tr>
            <tr class="{VERSION_30_VISIBLE}">
                <td><small>{LANG_CTRLINFO}</small></td>
                <td><small>{LANG_CTRLINFO}</small></td>
            </tr>
            <tr>
                <td>{LANG_SERVICES} (*)</td>
                <td valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_SERVICES}" name="mselValue3[]" size="5" multiple id="mselValue3" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN service -->
                                    <option value="{DAT_SERVICE_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_SERVICE_SEL}" {DAT_SERVICE_SEL} {OPTION_DISABLED}>{DAT_SERVICE}</option>
<!-- END service -->
                                </select>                            
                            </td>
                        </tr>
                    </table>
                </td>
                <td valign="top" rowspan="2"><img id="mutvalue3" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceescalation','service','all','Info');" class="infobutton_2"></td>
                <td class="{VERSION_30_VISIBLE}">{LANG_SERVICEGROUPS} (*)</td>
                <td class="{VERSION_30_VISIBLE}" valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_SERVICEGROUPS}" name="mselValue6[]" size="5" multiple id="mselValue6" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN servicegroup -->
                                    <option value="{DAT_SERVICEGROUP_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_SERVICEGROUP_SEL}" {DAT_SERVICEGROUP_SEL} {OPTION_DISABLED}>{DAT_SERVICEGROUP}</option>
<!-- END servicegroup -->
                                </select>                            
                            </td>
                        </tr>
                    </table>
				</td>
                <td valign="top" rowspan="2" class="{VERSION_30_VISIBLE}"><img id="mutvalue6" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceescalation','servicegroup','all','Info');" class="infobutton_2"></td>
            </tr>
            <tr>
                <td><small>{LANG_CTRLINFO}</small></td>
                <td class="{VERSION_30_VISIBLE}"><small>{LANG_CTRLINFO}</small></td>
            </tr>
            <tr>
              <td>{LANG_CONFIG_NAME} *</td>
              <td><input title="{LANG_CONFIG_NAME}" name="tfValue1" type="text" id="tfValue1" value="{DAT_CONFIG_NAME}" class="inpmust"></td>
              <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceescalation','config_name','all','Info');" class="infobutton_1"></td>
              <td>{LANG_FIRST_NOTIFICATION} *</td>
              <td><input title="{LANG_FIRST_NOTIFICATION}" name="tfNullVal1" type="text" value="{DAT_FIRST_NOTIFICATION}" class="inpmust"></td>
              <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceescalation','first_notification','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
              <td>{LANG_NOTIFICATION_INTERVAL} *</td>
              <td><input title="{LANG_NOTIFICATION_INTERVAL}" name="tfNullVal3" type="text" value="{DAT_NOTIFICATION_INTERVAL}" class="shortmust">
              <span class="shorttext">min</span></td>
              <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceescalation','notification_intervall','all','Info');" class="infobutton_1"></td>
              <td>{LANG_LAST_NOTIFICATION} *</td>
              <td><input title="{LANG_LAST_NOTIFICATION}" name="tfNullVal2" type="text" value="{DAT_LAST_NOTIFICATION}" class="inpmust"></td>
              <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceescalation','last_notification','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_ESCALATION_PERIOD}</td>
                <td>
                	<select title="{LANG_ESCALATION_PERIOD}" name="selValue1" id="selValue1" class="selectborder">
<!-- BEGIN timeperiod -->
                      <option value="{DAT_TIMEPERIOD_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_TIMEPERIOD_SEL}>{DAT_TIMEPERIOD}</option>
<!-- END timeperiod -->
                    </select>
                </td>
             	<td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceescalation','escalation_period','all','Info');" class="infobutton_1"></td>
                <td>{LANG_ESCALATION_OPTIONS}</td>
                <td>
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td class="radio_cell_1"><input title="w" name="chbGr1a" type="checkbox" class=" checkbox" id="chbGr1a" value="w" {DAT_EOW_CHECKED}></td>
                            <td class="radio_cell_1">w</td>
                            <td class="radio_cell_1"><input title="u" name="chbGr1b" type="checkbox" class=" checkbox" id="chbGr1b" value="u" {DAT_EOU_CHECKED}></td>
                            <td class="radio_cell_1">u</td>
                            <td class="radio_cell_1"><input title="c" name="chbGr1c" type="checkbox" class=" checkbox" id="chbGr1c" value="c" {DAT_EOC_CHECKED}></td>
                            <td class="radio_cell_1">c</td>
                            <td class="radio_cell_1"><input title="r" name="chbGr1d" type="checkbox" class=" checkbox" id="chbGr1d" value="r" {DAT_EOR_CHECKED}></td>
                            <td class="radio_cell_1">r</td>
                        </tr>
                    </table>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceescalation','escalation_options','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
				<td>{LANG_REGISTERED}</td>
				<td><input title="{LANG_REGISTERED}" name="chbRegister" type="checkbox" class="checkbox" id="chbRegister" value="1" {REG_CHECKED}></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','registered','all','Info');" class="infobutton_1"></td>
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
                <td>{LANG_ACTIVE}</td>
                <td colspan="5"><input title="{LANG_ACTIVE}" name="chbActive" type="checkbox" class="checkbox" id="chbActive" value="1" {ACT_CHECKED}>
                    <input name="modus" type="hidden" id="modus" value="{MODUS}">
                    <input name="hidId" type="hidden" id="hidId" value="{DAT_ID}"> <input name="hidLimit" type="hidden" id="hidLimit" value="{LIMIT}"></td>
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
<div id="mutdialogvalue5">
    <div id="mutdialogvalue5content" class="bd"></div>
</div>
<div id="mutdialogvalue6">
    <div id="mutdialogvalue6content" class="bd"></div>
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->