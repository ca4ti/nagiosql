<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : hostgroup template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datainsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <script type="text/JavaScript">
        <!--
        // Initialize change dialog
        openMutDlgInit("mselValue1","mutdialogvalue1","{LANG_MODIFY_SELECTION}: {LANG_MEMBERS}","mutvalue1","{LANG_SAVE}","{LANG_ABORT}");
        openMutDlgInit("mselValue2","mutdialogvalue2","{LANG_MODIFY_SELECTION}: {LANG_HOSTGROUP_MEMBERS}","mutvalue2","{LANG_SAVE}","{LANG_ABORT}");
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
                const selfields = "mselValue1,mselValue2";
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
            const fields1 = "tfValue1,{VERSION_20_VALUE_MUST}tfValue2";
            const msg1 = "{FILL_ALLFIELDS}";
            const msg2 = "{FILL_ILLEGALCHARS}";
            const header = "{LANG_FORMCHECK}";
            const form = document.frmDetail;
            let check;
            check = checkfields(fields1,form,myFocusObject);
            if (check === false) {
                msginit(msg1,header,1);
                return false;
            }	
            // Check for illegal chars
            if (form.tfValue1.value.match(/[^a-zA-Z0-9.@ _-]/)) {
                msginit(msg2+" {LANG_HOSTGROUP_NAME}",header,1);
                 document.frmDetail.tfValue1.focus();
                return false;
            }
        }
        //-->
    </script>    
    <form name="frmDetail" method="post" action="{ACTION_INSERT}">    
    <table border="0" cellpadding="0" class="content_formtable">
        <tr>
            <td class="content_tbl_row1">{LANG_HOSTGROUP_NAME} *</td>
            <td class="content_tbl_row2"><input title="{LANG_HOSTGROUP_NAME}" name="tfValue1" type="text" id="tfValue1" value="{DAT_HOSTGROUP_NAME}" class="inpmust" tabindex="1"></td>
            <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostgroup','hostgroup_name','all','Info');" class="infobutton_1"></td>
            <td class="content_tbl_row1">{LANG_MEMBERS} {VERSION_20_STAR}</td>
            <td class="content_tbl_row2" rowspan="4" valign="top">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td>
                            <select title="{LANG_MEMBERS}" name="mselValue1[]" size="5" multiple id="mselValue1" class="selectborder {VERSION_20_MUST}" {MSIE_DISABLED}>
<!-- BEGIN host_members -->
                                <option value="{DAT_HOST_MEMBERS_ID}" class="empty_class {VERSION_20_MUST} {SPECIAL_STYLE} {IE_HOST_MEMBERS_SEL}" {DAT_HOST_MEMBERS_SEL} {OPTION_DISABLED}>{DAT_HOST_MEMBERS}</option>
<!-- END host_members -->
                            </select>           
                        </td>
                    </tr>
                </table>      
            </td>
            <td class="content_tbl_row4" rowspan="4" valign="top"><img id="mutvalue1" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostgroup','members','all','Info');" class="infobutton_2"></td>
        </tr> 
        <tr>
            <td>{LANG_DESCRIPTION} *</td>
            <td><input title="{LANG_DESCRIPTION}" name="tfValue2" type="text" id="tfValue2" value="{DAT_ALIAS}" class="inpmust" tabindex="2"></td>
            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostgroup','description','all','Info');" class="infobutton_1"></td>
            <td rowspan="3"><small>{LANG_CTRLINFO}</small></td>
        </tr>
        <tr>
            <td><span class="{VERSION_30_VISIBLE}">{LANG_NOTES}</span></td>
            <td><span class="{VERSION_30_VISIBLE}"><input title="{LANG_NOTES}" name="tfValue3" type="text" id="tfValue3" value="{DAT_NOTES}" tabindex="3"></span></td>
            <td><span class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostgroup','notes','3','Info');" class="infobutton_1"></span></td>
        </tr>
        <tr>
            <td><span class="{VERSION_30_VISIBLE}">{LANG_NOTES_URL}</span></td>
            <td><span class="{VERSION_30_VISIBLE}"><input title="{LANG_NOTES_URL}" name="tfValue4" type="text" id="tfValue4" value="{DAT_NOTES_URL}" tabindex="4"></span></td>
            <td><span class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostgroup','notes_url','3','Info');" class="infobutton_1"></span></td>
        </tr>
        <tr>
            <td><span class="{VERSION_30_VISIBLE}">{LANG_ACTION_URL}</span></td>
            <td><span class="{VERSION_30_VISIBLE}"><input title="{LANG_ACTION_URL}" name="tfValue5" type="text" id="tfValue5" value="{DAT_ACTION_URL}" tabindex="5"></span></td>
            <td><span class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostgroup','action_url','3','Info');" class="infobutton_1"></span></td>
            <td><span class="{VERSION_30_VISIBLE}">{LANG_HOSTGROUP_MEMBERS}</span></td>
            <td rowspan="5" valign="top">
                <table cellpadding="0" cellspacing="0" border="0" class="{VERSION_30_VISIBLE}">
                    <tr>
                        <td>
                            <select title="{LANG_HOSTGROUP_MEMBERS}" name="mselValue2[]" size="5" multiple="multiple" id="mselValue2" class="selectborder {VERSION_20_MUST}" {MSIE_DISABLED}>
<!-- BEGIN hostgroups -->
                                <option value="{DAT_HOSTGROUPS_ID}" class="empty_class {VERSION_20_MUST} {SPECIAL_STYLE} {IE_HOSTGROUPS_SEL}" {DAT_HOSTGROUPS_SEL} {OPTION_DISABLED}>{DAT_HOSTGROUPS}</option>
<!-- END hostgroups -->
                            </select>            
                        </td>
                    </tr>
                </table>      
            </td>
            <td rowspan="5" valign="top"><span class="{VERSION_30_VISIBLE}"><img id="mutvalue2" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('hostgroup','hostgroup_members','all','Info');" class="infobutton_2"></span></td>
        </tr> 
        <tr {RESTRICT_GROUP_ADMIN}>
            <td>{LANG_ACCESS_GROUP}</td>
            <td>
                <select title="{LANG_ACCESS_GROUP}" name="selAccGr" class="selectborder">
<!-- BEGIN acc_group -->
                    <option value="{DAT_ACC_GROUP_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_ACC_GROUP_SEL}>{DAT_ACC_GROUP}</option>
<!-- END acc_group -->
                </select>
            </td>
            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','accessgroup','all','Info');" class="infobutton_1"></td>
            <td rowspan="3"><small>{LANG_CTRLINFO}</small></td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td>{LANG_REGISTERED}</td>
            <td><input title="{LANG_REGISTERED}" name="chbRegister" type="checkbox" id="chbRegister" value="1" {REG_CHECKED}></td>
            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','registered','all','Info');" class="infobutton_1"></td> 
        </tr>
        <tr>
            <td>{LANG_ACTIVE}</td>
            <td colspan="3" valign="top"><input title="{LANG_ACTIVE}" name="chbActive" type="checkbox" id="chbActive" value="1" {ACT_CHECKED} {ACT_DISABLED}>
                <input name="hidActive" type="hidden" id="hidActive" value="{ACTIVE}">
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
    {CHECK_MUST_DATA}
</div>
<div id="mutdialogvalue1">
    <div id="mutdialogvalue1content" class="bd"></div>
</div>
<div id="mutdialogvalue2">
    <div id="mutdialogvalue2content" class="bd"></div>
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->