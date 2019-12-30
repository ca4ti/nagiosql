<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : contactgroup template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datainsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <script type="text/JavaScript">
        <!--
        // Initialize change dialog
        openMutDlgInit("mselValue1","mutdialogmember","{LANG_MODIFY_SELECTION}: {LANG_MEMBERS}","mutmembers","{LANG_SAVE}","{LANG_ABORT}");
        openMutDlgInit("mselValue2","mutdialoggroups","{LANG_MODIFY_SELECTION}: {LANG_GROUP_MEMBERS}","mutgroups","{LANG_SAVE}","{LANG_ABORT}","1");
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
            const fields1 = "tfValue1,tfValue2,mselValue1";
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
            if (form.tfValue1.value.match(/[^a-zA-Z0-9.@_-]/)) {
                msginit(msg2+" {LANG_CONTACT_GROUP}",header,1);
                form.tfValue1.focus();
                return false;
            }
        }
        //-->
    </script>
    <form name="frmDetail" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1">{LANG_CONTACT_GROUP} *</td>
                <td class="content_tbl_row2"><input title="{LANG_CONTACT_GROUP}" name="tfValue1" type="text" id="tfValue1" value="{DAT_CONTACTGROUP_NAME}" class="inpmust"></td>
                <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contactgroup','contactgroup_name','all','Info');" class="infobutton_1"></td>
                <td class="content_tbl_row1">{LANG_MEMBERS} *</td>
                <td class="content_tbl_row2" rowspan="6" valign="top">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_MEMBERS}" name="mselValue1[]" size="8" multiple id="mselValue1" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN contacts -->
                                    <option value="{DAT_CONTACTS_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_CONTACTS_SEL}" {DAT_CONTACTS_SEL} {OPTION_DISABLED}>{DAT_CONTACTS}</option>
<!-- END contacts -->
                                </select>
                            </td>
                    </table>
                </td>
                <td class="content_tbl_row4" rowspan="6" valign="top"><img id="mutmembers" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contactgroup','members','all','Info');" class="infobutton_2"></td>
            </tr>
            <tr>
                <td>{LANG_DESCRIPTION} *</td>
                <td><input title="{LANG_DESCRIPTION}" name="tfValue2" type="text" id="tfValue2" value="{DAT_ALIAS}" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contactgroup','alias','all','Info');" class="infobutton_1"></td>
                <td rowspan="3"><small>{LANG_CTRLINFO}</small></td>
            </tr>
            <tr id="groups30" class="{VERSION_30_VISIBLE}">
                <td valign="top">{LANG_GROUP_MEMBERS}</td>
                <td valign="top" rowspan="2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <select title="{LANG_GROUP_MEMBERS}" name="mselValue2[]" size="4" multiple id="mselValue2" class="selectborder" {MSIE_DISABLED}>
<!-- BEGIN contactgroups -->
                                    <option value="{DAT_CONTACTGROUPS_ID}" class="empty_class {SPECIAL_STYLE} {IE_CONTACTGROUPS_SEL}" {DAT_CONTACTGROUPS_SEL} {OPTION_DISABLED}>{DAT_CONTACTGROUPS}</option>
<!-- END contactgroups -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td rowspan="2" valign="top"><img id="mutgroups" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contactgroup','contactgroup_members','all','Info');" class="infobutton_2"></td>
            </tr>
            <tr>
                <td><small>{LANG_CTRLINFO}</small></td>
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
                <td colspan="2"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','accessgroup','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
              <td>{LANG_REGISTERED}</td>
              <td><input title="{LANG_REGISTERED}" name="chbRegister" type="checkbox" class="checkbox" id="chbRegister" value="1" {REG_CHECKED}></td>
              <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','registered','all','Info');" class="infobutton_1"></td>
          </tr>
            <tr>
                <td>{LANG_ACTIVE}</td>
                <td colspan="5"><input title="{LANG_ACTIVE}" name="chbActive" type="checkbox" class="checkbox" id="chbActive" value="1" {ACT_CHECKED} {ACT_DISABLED}>
                    <input name="hidActive" type="hidden" id="hidActive" value="{ACTIVE}">
                    <input name="modus" type="hidden" id="modus" value="{MODUS}">
                    <input name="hidId" type="hidden" id="hidId" value="{DAT_ID}">
                    <input name="hidLimit" type="hidden" id="hidLimit" value="{LIMIT}"></td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" valign="bottom"><input name="subForm" type="button" id="subForm" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE}>&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span></td>
                <td colspan="3"><span class="redmessage">{WARNING}</span></td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
        </table>
  </form>
</div>  
<span id="rel_text" class="{RELATION_CLASS}"><a href="javascript:showRelationData(1)" style="color:#00F">[{LANG_SHOW_RELATION_DATA}]</a></span><span id="rel_info" class="elementHide"><a href="javascript:showRelationData(0)" style="color:#00F">[{LANG_HIDE_RELATION_DATA}]</a>{CHECK_MUST_DATA}</span>
<div id="mutdialogmember">
    <div id="mutdialogmembercontent" class="bd"></div>
</div>
<div id="mutdialoggroups">
    <div id="mutdialoggroupscontent" class="bd"></div>
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->