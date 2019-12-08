<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : servicedependencies template -->
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
		openMutDlgInit("mselValue1","mutdialogvalue1","{LANG_MODIFY_SELECTION}: {LANG_HOSTS}","mutvalue1","{LANG_SAVE}","{LANG_ABORT}","1");
		openMutDlgInit("mselValue2","mutdialogvalue2","{LANG_MODIFY_SELECTION}: {LANG_DEPENDHOSTS}","mutvalue2","{LANG_SAVE}","{LANG_ABORT}","1");
		openMutDlgInit("mselValue3","mutdialogvalue3","{LANG_MODIFY_SELECTION}: {LANG_HOSTGROUPS}","mutvalue3","{LANG_SAVE}","{LANG_ABORT}","1");
		openMutDlgInit("mselValue4","mutdialogvalue4","{LANG_MODIFY_SELECTION}: {LANG_DEPENDHOSTGRS}","mutvalue4","{LANG_SAVE}","{LANG_ABORT}","1");
		openMutDlgInit("mselValue5","mutdialogvalue5","{LANG_MODIFY_SELECTION}: {LANG_SERVICES}","mutvalue5","{LANG_SAVE}","{LANG_ABORT}");
		openMutDlgInit("mselValue6","mutdialogvalue6","{LANG_MODIFY_SELECTION}: {LANG_DEPENDSERVICES}","mutvalue6","{LANG_SAVE}","{LANG_ABORT}");
		openMutDlgInit("mselValue7","mutdialogvalue7","{LANG_MODIFY_SELECTION}: {LANG_SERVICEGROUPS}","mutvalue7","{LANG_SAVE}","{LANG_ABORT}","1");
		openMutDlgInit("mselValue8","mutdialogvalue8","{LANG_MODIFY_SELECTION}: {LANG_DEPENDSERVICEGROUPS}","mutvalue8","{LANG_SAVE}","{LANG_ABORT}","1");
        let update = 1;
        // Process form updateServices
		function updateForm(key) {
			document.frmDetail.modus.value = "refresh";
            const selfields = "mselValue1,mselValue2,mselValue3,mselValue4,mselValue5,mselValue6,mselValue7,mselValue8";
            const ar_sel = selfields.split(",");
            for (let i=0;i<ar_sel.length;i++){
				document.getElementById(ar_sel[i]).disabled = false;
				for (let y=0;y<document.getElementById(ar_sel[i]).length;++y) {
					document.getElementById(ar_sel[i]).options[y].disabled = false;
				}
			}
			document.frmDetail.submit();
		}
		// Interrupt input
		function abort() {
			this.location.href = "{ACTION_INSERT}?limit={LIMIT}";
		}
		// Refresh form
		function updateServices(intMode) {
			if (intMode === 2) {
				document.frmDetail.mselValue1.value = "";
				document.frmDetail.mselValue1.selectedIndex = -1;
			}
			if (intMode === 4) {
				document.frmDetail.mselValue2.value = "";
				document.frmDetail.mselValue2.selectedIndex = -1;
			}
			alert('da');
			document.frmDetail.modus.value = "refresh";
			document.frmDetail.submit();
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
                const selfields = "mselValue1,mselValue2,mselValue3,mselValue4,mselValue5,mselValue6,mselValue7,mselValue8";
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
            let check = checkfields(fields1,form,myFocusObject);
			if (check === false) {
				msginit(msg1,header,1);
				return false;
			}	
			// Are dependent services or dependent servicegroup selected?
			if ((form.mselValue6.value === "") &&
				(form.mselValue8.value === "")) {
				msginit(msg1,header,1);
				return false;
			}
			// Are services or servicegroups selected?
			if ((form.mselValue5.value === "") &&
				(form.mselValue7.value === "")) {
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
                            	<select title="{LANG_DEPENDHOSTS}" name="mselValue2[]" size="5" multiple id="mselValue2" class="selectbordermust inpmust" onchange="updateServices(3)" {MSIE_DISABLED}>
<!-- BEGIN dependent_host -->
                            		<option value="{DAT_DEPENDENT_HOST_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_DEPENDENT_HOST_SEL}" {DAT_DEPENDENT_HOST_SEL} {OPTION_DISABLED}>{DAT_DEPENDENT_HOST}</option>
<!-- END dependent_host -->
                            	</select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="content_tbl_row3" valign="top" rowspan="2"><img id="mutvalue2" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('servicedependency','dependent_host','all','Info');" class="infobutton_2"></td>
                <td class="content_tbl_row1" valign="top">{LANG_HOSTS} (*)</td>
                <td class="content_tbl_row2" valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_HOSTS}" name="mselValue1[]" size="5" multiple id="mselValue1" class="selectbordermust inpmust" onChange="updateServices(1)" {MSIE_DISABLED}>
<!-- BEGIN host -->
                                    <option value="{DAT_HOST_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_HOST_SEL}" {DAT_HOST_SEL} {OPTION_DISABLED}>{DAT_HOST}</option>
<!-- END host -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="content_tbl_row4" valign="top" rowspan="2"><img id="mutvalue1" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('servicedependency','host','all','Info');" class="infobutton_2"></td>
            </tr>
            <tr>
                <td><small>{LANG_CTRLINFO}</small></td>
                <td><small>{LANG_CTRLINFO}</small></td>
            </tr>  
            <tr class="{VERSION_30_VISIBLE}">
                <td valign="top">{LANG_DEPENDHOSTGRS} (*)</td>
                <td valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_DEPENDHOSTGRS}" name="mselValue4[]" size="5" multiple id="mselValue4" class="selectbordermust inpmust" onChange="updateServices(4)" {MSIE_DISABLED}>
<!-- BEGIN dependent_hostgroup -->
                                <option value="{DAT_DEPENDENT_HOSTGROUP_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_DEPENDENT_HOSTGROUP_SEL}" {DAT_DEPENDENT_HOSTGROUP_SEL} {OPTION_DISABLED}>{DAT_DEPENDENT_HOSTGROUP}</option>
<!-- END dependent_hostgroup -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td valign="top" rowspan="2"><img id="mutvalue4" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('servicedependency','dependent_hostgroup','all','Info');" class="infobutton_2"></td>
                <td valign="top">{LANG_HOSTGROUPS} (*)</td>
                <td valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_HOSTGROUPS}" name="mselValue3[]" size="5" multiple id="mselValue3" class="selectbordermust inpmust" onChange="updateServices(2)" {MSIE_DISABLED}>
<!-- BEGIN hostgroup -->
                                    <option value="{DAT_HOSTGROUP_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_HOSTGROUP_SEL}" {DAT_HOSTGROUP_SEL} {OPTION_DISABLED}>{DAT_HOSTGROUP}</option>
<!-- END hostgroup -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td valign="top" rowspan="2"><img id="mutvalue3" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('servicedependency','hostgroup','all','Info');" class="infobutton_2"></td>
            </tr>
            <tr class="{VERSION_30_VISIBLE}">
                <td><small>{LANG_CTRLINFO}</small></td>
                <td><small>{LANG_CTRLINFO}</small></td>
            </tr>
            <tr>
                <td valign="top">{LANG_DEPENDSERVICES} (*)</td>
                <td valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_DEPENDSERVICES}" name="mselValue6[]" size="5" multiple id="mselValue6" class="selectbordermust inpmust" onChange="updateServices(3)" {MSIE_DISABLED}>
<!-- BEGIN dependent_service -->
                                    <option value="{DAT_DEPENDENT_SERVICE_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_DEPENDENT_SERVICE_SEL}" {DAT_DEPENDENT_SERVICE_SEL} {OPTION_DISABLED}>{DAT_DEPENDENT_SERVICE}</option>
<!-- END dependent_service -->
                                </select>
                            </td>
                            <td valign="top" style="padding-left:1px"></td>
                        </tr>
                    </table>
                </td>
                <td valign="top" rowspan="2"><img id="mutvalue6" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('servicedependency','dependent_services','all','Info');" class="infobutton_2"></td>
                <td valign="top">{LANG_SERVICES} (*)</td>
                <td valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_SERVICES}" name="mselValue5[]" size="5" multiple id="mselValue5" class="selectbordermust inpmust" onChange="updateServices(1)" {MSIE_DISABLED}>
<!-- BEGIN service -->
                                    <option value="{DAT_SERVICE_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_SERVICE_SEL}" {DAT_SERVICE_SEL} {OPTION_DISABLED}>{DAT_SERVICE}</option>
<!-- END service -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td valign="top" rowspan="2"><img id="mutvalue5" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('servicedependency','services','all','Info');" class="infobutton_2"></td>
            </tr>
            <tr>
                <td><small>{LANG_CTRLINFO}</small></td>
                <td><small>{LANG_CTRLINFO}</small></td>
            </tr>
            <tr class="{VERSION_30_VISIBLE}">
                <td valign="top">{LANG_DEPENDSERVICEGROUPS} (*)</td>
                <td valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_DEPENDSERVICEGROUPS}" name="mselValue8[]" size="5" multiple id="mselValue8" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN dependent_servicegroup -->
                                    <option value="{DAT_DEPENDENT_SERVICEGROUP_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_DEPENDENT_SERVICEGROUP_SEL}" {DAT_DEPENDENT_SERVICEGROUP_SEL} {OPTION_DISABLED}>{DAT_DEPENDENT_SERVICEGROUP}</option>
<!-- END dependent_servicegroup -->
                                </select>
                            </td>
                            <td valign="top" style="padding-left:1px"></td>
                        </tr>
                    </table>
                </td>
                <td valign="top" rowspan="2"><img id="mutvalue8" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('servicedependency','dependent_servicegroup_name','all','Info');" class="infobutton_2"></td>
                <td valign="top">{LANG_SERVICEGROUPS} (*)</td>
                <td valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_SERVICEGROUPS}" name="mselValue7[]" size="5" multiple id="mselValue7" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN servicegroup -->
                                    <option value="{DAT_SERVICEGROUP_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_SERVICEGROUP_SEL}" {DAT_SERVICEGROUP_SEL} {OPTION_DISABLED}>{DAT_SERVICEGROUP}</option>
<!-- END servicegroup -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td valign="top" rowspan="2"><img id="mutvalue7" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('servicedependency','servicegroup_name','all','Info');" class="infobutton_2"></td>
            </tr>
            <tr class="{VERSION_30_VISIBLE}">
                <td><small>{LANG_CTRLINFO}</small></td>
                <td><small>{LANG_CTRLINFO}</small></td>
            </tr>
            <tr>
                <td>{LANG_CONFIG_NAME} *</td>
                <td><input title="{LANG_CONFIG_NAME}" name="tfValue1" type="text" id="tfValue1" value="{DAT_CONFIG_NAME}" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('servicedependency','config_name','all','Info');" class="infobutton_1"></td>
                <td>{LANG_INHERIT}</td>
                <td><input title="{LANG_INHERIT}" type="checkbox" id="chbValue1" name="chbValue1" class="checkbox" value="1" {ACT_INHERIT}></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('servicedependency','inherit_parents','all','Info');"  class="infobutton_1"></td>
            </tr>
            <tr>
                <td><span class="{VERSION_30_VISIBLE}">{LANG_DEPENDENCY_PERIOD}</span></td>
                <td><span class="{VERSION_30_VISIBLE}">
                	<select title="{LANG_DEPENDENCY_PERIOD}" name="selValue1" id="selValue1" class="selectborder">
<!-- BEGIN timeperiod -->
                        <option value="{DAT_TIMEPERIOD_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_TIMEPERIOD_SEL}>{DAT_TIMEPERIOD}</option>
<!-- END timeperiod -->
                    </select>
                </span></td>
                <td><span class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('servicedependency','dependency_period','all','Info');" class="infobutton_1"></span></td>
                <td>{LANG_EXECFAILCRIT}</td>
                <td>
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td class="radio_cell_1"><input title="o" name="chbGr1a" type="checkbox" class=" checkbox" id="chbGr1a" value="o" {DAT_EOO_CHECKED}></td>
                            <td class="radio_cell_1">o</td>
                            <td class="radio_cell_1"><input title="w" name="chbGr1b" type="checkbox" class=" checkbox" id="chbGr1b" value="w" {DAT_EOW_CHECKED}></td>
                            <td class="radio_cell_1">w</td>
                            <td class="radio_cell_1"><input title="u" name="chbGr1c" type="checkbox" class=" checkbox" id="chbGr1c" value="u" {DAT_EOU_CHECKED}></td>
                            <td class="radio_cell_1">u</td>
                            <td class="radio_cell_1"><input title="c" name="chbGr1d" type="checkbox" class=" checkbox" id="chbGr1d" value="c" {DAT_EOC_CHECKED}></td>
                            <td class="radio_cell_1">c</td>
                            <td class="radio_cell_1"><input title="p" name="chbGr1e" type="checkbox" class=" checkbox" id="chbGr1e" value="p" {DAT_EOP_CHECKED}></td>
                            <td class="radio_cell_1">p</td>
                            <td class="radio_cell_1"><input title="n" name="chbGr1f" type="checkbox" class=" checkbox" id="chbGr1f" value="n" {DAT_EON_CHECKED}></td>
                            <td class="radio_cell_1">n</td>
                        </tr>
                    </table>      
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('servicedependency','execution_failure_criteria','all','Info');" class="infobutton_1"></td>
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
                            <td class="radio_cell_1"><input title="w" name="chbGr2b" type="checkbox" class=" checkbox" id="chbGr2b" value="w" {DAT_NOW_CHECKED}></td>
                            <td class="radio_cell_1">w</td>
                            <td class="radio_cell_1"><input title="u" name="chbGr2c" type="checkbox" class=" checkbox" id="chbGr2c" value="u" {DAT_NOU_CHECKED}></td>
                            <td class="radio_cell_1">u</td>
                            <td class="radio_cell_1"><input title="c" name="chbGr2d" type="checkbox" class=" checkbox" id="chbGr2d" value="c" {DAT_NOC_CHECKED}></td>
                            <td class="radio_cell_1">c</td>
                            <td class="radio_cell_1"><input title="p" name="chbGr2e" type="checkbox" class=" checkbox" id="chbGr2e" value="p" {DAT_NOP_CHECKED}></td>
                            <td class="radio_cell_1">p</td>
                            <td class="radio_cell_1"><input title="n" name="chbGr2f" type="checkbox" class=" checkbox" id="chbGr2f" value="n" {DAT_NON_CHECKED}></td>
                            <td class="radio_cell_1">n</td>
                        </tr>
                    </table>       
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('servicedependency','notification_failure_criteria','all','Info');" class="infobutton_1"></td>
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
<div id="mutdialogvalue2">
    <div id="mutdialogvalue2content" class="bd"></div>
</div>
<div id="mutdialogvalue1">
    <div id="mutdialogvalue1content" class="bd"></div>
</div>
<div id="mutdialogvalue4">
    <div id="mutdialogvalue4content" class="bd"></div>
</div>
<div id="mutdialogvalue3">
    <div id="mutdialogvalue3content" class="bd"></div>
</div>
<div id="mutdialogvalue6">
    <div id="mutdialogvalue6content" class="bd"></div>
</div>
<div id="mutdialogvalue5">
    <div id="mutdialogvalue5content" class="bd"></div>
</div>
<div id="mutdialogvalue8">
    <div id="mutdialogvalue8content" class="bd"></div>
</div>
<div id="mutdialogvalue7">
    <div id="mutdialogvalue7content" class="bd"></div>
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->