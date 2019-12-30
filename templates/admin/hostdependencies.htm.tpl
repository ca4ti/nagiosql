<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : hostdependencies template -->
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
		openMutDlgInit("mselValue1","mutdialogvalue1","{LANG_MODIFY_SELECTION}: {LANG_DEPENDHOSTS}","mutvalue1","{LANG_SAVE}","{LANG_ABORT}");
		openMutDlgInit("mselValue2","mutdialogvalue2","{LANG_MODIFY_SELECTION}: {LANG_HOSTS}","mutvalue2","{LANG_SAVE}","{LANG_ABORT}");
		openMutDlgInit("mselValue3","mutdialogvalue3","{LANG_MODIFY_SELECTION}: {LANG_DEPENDHOSTGRS}","mutvalue3","{LANG_SAVE}","{LANG_ABORT}");
		openMutDlgInit("mselValue4","mutdialogvalue4","{LANG_MODIFY_SELECTION}: {LANG_HOSTGROUPS}","mutvalue4","{LANG_SAVE}","{LANG_ABORT}");
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
            const fields1 = "tfValue1";
            const msg1 = "{FILL_ALLFIELDS}";
            const header = "{LANG_FORMCHECK}";
            const form = document.frmDetail;
            let check;
            check = checkfields(fields1,form,myFocusObject);
			if (check === false) {
	  			msginit(msg1,header,1);
				return false;
	  		}	
	  		// Are dependent hosts or dependent hostgroups selected?
      		if ((form.mselValue1.value === "") &&
		 	   	(form.mselValue3.value === "")) {
		 		msginit(msg1,header,1);
		 		return false;
      		}
	  		// Are hosts or hostgroups selected?
      		if ((form.mselValue2.value === "") &&
		 		(form.mselValue4.value === "")) {
		 		msginit(msg1,header,1);
		 		return false;
      		}
    	}
    	//-->
  	</script>
  	<form name="frmDetail" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1" valign="top">{LANG_DEPENDHOSTS} (*)</td>
                <td class="content_tbl_row2" valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_DEPENDHOSTS}" name="mselValue1[]" size="5" multiple id="mselValue1" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN depend_host -->
                                    <option value="{DAT_DEPEND_HOST_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_DEPEND_HOST_SEL}" {DAT_DEPEND_HOST_SEL} {OPTION_DISABLED}>{DAT_DEPEND_HOST}</option>
<!-- END depend_host -->
                                </select>
                            </td>
                        </tr>
                    </table>      
                </td>
                <td class="content_tbl_row3" rowspan="2" valign="top"><img id="mutvalue1" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostdependency','dependent_host','all','Info');" class="infobutton_2"></td>
                <td class="content_tbl_row1" valign="top"><span class="{VERSION_30_VISIBLE}">{LANG_DEPENDHOSTGRS} (*)</span></td>
                <td class="content_tbl_row2" valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0" class="{VERSION_30_VISIBLE}">
                        <tr>
                            <td>
                                <select title="{LANG_DEPENDHOSTGRS}" name="mselValue3[]" size="5" multiple id="mselValue3" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN depend_hostgroup -->
                                    <option value="{DAT_DEPEND_HOSTGROUP_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_DEPEND_HOSTGROUP_SEL}" {DAT_DEPEND_HOSTGROUP_SEL} {OPTION_DISABLED}>{DAT_DEPEND_HOSTGROUP}</option>
<!-- END depend_hostgroup -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="{VERSION_30_VISIBLE}" rowspan="2" valign="top"><img id="mutvalue3" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostdependency','dependent_hostgroups','all','Info');" class="infobutton_2"></td>
            </tr>
            <tr>
                <td><small>{LANG_CTRLINFO}</small></td>
                <td><small>{LANG_CTRLINFO}</small></td>
            </tr>
            <tr>
                <td valign="top">{LANG_HOSTS} (*)</td>
                <td valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_HOSTS}" name="mselValue2[]" size="5" multiple id="mselValue2" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN host -->
                                    <option value="{DAT_HOST_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_HOST_SEL}" {DAT_HOST_SEL} {OPTION_DISABLED}>{DAT_HOST}</option>
<!-- END host -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td rowspan="2" valign="top"><img id="mutvalue2" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostdependency','host','all','Info');" class="infobutton_2"></td>
                <td valign="top"><span class="{VERSION_30_VISIBLE}">{LANG_HOSTGROUPS} (*)</span></td>
                <td valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0" class="{VERSION_30_VISIBLE}">
                        <tr>
                            <td width="205">
                                <select title="{LANG_HOSTGROUPS}" name="mselValue4[]" size="5" multiple id="mselValue4" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN hostgroup -->
                                    <option value="{DAT_HOSTGROUP_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_HOSTGROUP_SEL}" {DAT_HOSTGROUP_SEL} {OPTION_DISABLED}>{DAT_HOSTGROUP}</option>
<!-- END hostgroup -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td rowspan="2" valign="top" class="{VERSION_30_VISIBLE}"><img id="mutvalue4" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostdependency','hostgroup','all','Info');" class="infobutton_2"></td>
            </tr>
            <tr>
                <td><small>{LANG_CTRLINFO}</small></td>
                <td><small>{LANG_CTRLINFO}</small></td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td>{LANG_CONFIG_NAME} *</td>
                <td><input title="{LANG_CONFIG_NAME}" name="tfValue1" type="text" id="tfValue1" value="{DAT_CONFIG_NAME}" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostdependency','config_name','all','Info');" class="infobutton_1"></td>
                <td>{LANG_INHERIT}</td>
                <td><input title="{LANG_INHERIT}" type="checkbox" id="chbValue1" name="chbValue1" class="checkbox" value="1" {ACT_INHERIT}></td>
                <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostdependency','inherit_parents','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td><span class="{VERSION_30_VISIBLE}">{LANG_DEPENDENCY_PERIOD}</span></td>
                <td><span class="{VERSION_30_VISIBLE}">
                    <select title="{LANG_DEPENDENCY_PERIOD}" name="selValue1" id="selValue1" class="selectborder">
<!-- BEGIN timeperiod -->
                        <option value="{DAT_TIMEPERIOD_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_TIMEPERIOD_SEL}>{DAT_TIMEPERIOD}</option>
<!-- END timeperiod -->
                    </select>
                    </span>
                </td>
                <td><span class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostdependency','dependency_period','3','Info');" class="infobutton_1"></span></td>
                <td>{LANG_EXECFAILCRIT}</td>
                <td>
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td class="radio_cell_1"><input title="o" name="chbGr1a" type="checkbox" class=" checkbox" id="chbGr1a" value="o" {DAT_EOO_CHECKED}></td>
                            <td class="radio_cell_1">o</td>
                            <td class="radio_cell_1"><input title="d" name="chbGr1b" type="checkbox" class=" checkbox" id="chbGr1b" value="d" {DAT_EOD_CHECKED}></td>
                            <td class="radio_cell_1">d</td>
                            <td class="radio_cell_1"><input title="u" name="chbGr1c" type="checkbox" class=" checkbox" id="chbGr1c" value="u" {DAT_EOU_CHECKED}></td>
                            <td class="radio_cell_1">u</td>
                            <td class="radio_cell_1"><input title="p" name="chbGr1d" type="checkbox" class=" checkbox" id="chbGr1d" value="p" {DAT_EOP_CHECKED}></td>
                            <td class="radio_cell_1">p</td>
                            <td class="radio_cell_1"><input title="n" name="chbGr1e" type="checkbox" class=" checkbox" id="chbGr1e" value="n" {DAT_EON_CHECKED}></td>
                            <td class="radio_cell_1">n</td>
                        </tr>
                    </table>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostdependency','execution_failure_criteria','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td {RESTRICT_GROUP_ADMIN}>{LANG_ACCESS_GROUP}</td>
                <td {RESTRICT_GROUP_ADMIN}>
                    <select title="{LANG_ACCESS_GROUP}" name="selAccGr" class="selectborder">
<!-- BEGIN acc_group -->
                        <option value="{DAT_ACC_GROUP_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_ACC_GROUP_SEL}>{DAT_ACC_GROUP}</option>
<!-- END acc_group -->
                    </select>
                </td>
                <td {RESTRICT_GROUP_ADMIN}><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','accessgroup','all','Info');" class="infobutton_1"></td>
                <td>{LANG_NOTIFFAILCRIT}</td>
                <td>
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td class="radio_cell_1"><input title="o" name="chbGr2a" type="checkbox" class=" checkbox" id="chbGr2a" value="o" {DAT_NOO_CHECKED}></td>
                            <td class="radio_cell_1">o</td>
                            <td class="radio_cell_1"><input title="d" name="chbGr2b" type="checkbox" class=" checkbox" id="chbGr2b" value="d" {DAT_NOD_CHECKED}></td>
                            <td class="radio_cell_1">d</td>
                            <td class="radio_cell_1"><input title="u" name="chbGr2c" type="checkbox" class=" checkbox" id="chbGr2c" value="u" {DAT_NOU_CHECKED}></td>
                            <td class="radio_cell_1">u</td>
                            <td class="radio_cell_1"><input title="p" name="chbGr2d" type="checkbox" class=" checkbox" id="chbGr2d" value="p" {DAT_NOP_CHECKED}></td>
                            <td class="radio_cell_1">p</td>
                            <td class="radio_cell_1"><input title="n" name="chbGr2e" type="checkbox" class=" checkbox" id="chbGr2e" value="n" {DAT_NON_CHECKED}></td>
                            <td class="radio_cell_1">n</td>
                        </tr>
                    </table>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostdependency','notification_failure_criteria','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
              <td>{LANG_REGISTERED}</td>
              <td><input title="{LANG_REGISTERED}" name="chbRegister" type="checkbox" class="checkbox" id="chbRegister" value="1" {REG_CHECKED}></td>
              <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','registered','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_ACTIVE}</td>
                <td colspan="5"><input title="{LANG_ACTIVE}" name="chbActive" type="checkbox" class="checkbox" id="chbActive" value="1" {ACT_CHECKED}>
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
<div id="mutdialogvalue1">
    <div id="mutdialogvalue1content" class="bd"></div>
</div>
<div id="mutdialogvalue3">
    <div id="mutdialogvalue3content" class="bd"></div>
</div>
<div id="mutdialogvalue2">
    <div id="mutdialogvalue2content" class="bd"></div>
</div>
<div id="mutdialogvalue4">
    <div id="mutdialogvalue4content" class="bd"></div>
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->