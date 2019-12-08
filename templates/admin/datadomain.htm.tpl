<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project    : NagiosQL -->
<!-- Component  : domain administration template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datainsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <div class="redmessage">{ERRMESSAGE}</div>
    <script type="text/javascript">
        <!--
        // Interrupt input
        function abort() {
                this.location.href = "{ACTION_INSERT}";
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
                const selfields = "selValue1";
                const ar_sel = selfields.split(",");
                for (let i=0; i<ar_sel.length; i++){
                document.getElementById(ar_sel[i]).disabled = false;
                for (let y=0;y<document.getElementById(ar_sel[i]).length;++y) {
                    document.getElementById(ar_sel[i]).options[y].disabled = false;
                }
            }
                document.frmDomainInsert.submit();
                document.frmDomainInsert.subForm.disabled = true;
            }
        }
        // Check form entries
        function checkForm() {
            // Are all required fields filled in?
            const fields1 = "tfValue1,tfValue2{CHECK_TARGETS}";
            const msg1 = "{FILL_ALLFIELDS}";
            const msg2 = "{FILL_ILLEGALCHARS}";
            const header = "{LANG_FORMCHECK}";
            const form = document.frmDomainInsert;
            let check;
            check = checkfields(fields1,form,myFocusObject);
            if (check === false) {
                msginit(msg1,header,1);
                return false;
            }
            // Check for illegal chars
            if (form.tfValue1.value.match(/[^a-zA-Z0-9.@_-]/)) {
                msginit(msg2+" {LANG_DOMAIN}",header,1);
                form.tfValue1.focus();
                return false;
            }
        }
        //-->
    </script>
    <form name="frmDomainInsert" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1">{LANG_DOMAIN} *</td>
                <td class="content_tbl_row2"><input title="{LANG_DOMAIN}" name="tfValue1" type="text" id="tfValue1" tabindex="1" value="{DAT_DOMAIN}" style="width:350px;" {DOMAIN_DISABLE} class="inpmust {LOCKCLASS}"></td>
                <td class="content_tbl_row2"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','domain','all','Info');" class="infobutton_1"><input name="tfValue3" type="hidden" id="tfValue3" value="{DAT_DOMAIN}"></td>
            </tr>
            <tr>
                <td>{LANG_DESCRIPTION} *</td>
                <td colspan="2"><input title="{LANG_DESCRIPTION}" name="tfValue2" type="text" id="tfValue2" tabindex="2" value="{DAT_ALIAS}" style="width:350px;" class="inpmust"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_CONFIG_TARGET} *</td>
                <td>
                    <select title="{LANG_CONFIG_TARGET}" name="selValue1" id="selValue1" class="selectbordermust inpmust">
<!-- BEGIN target -->
                        <option value="{DAT_TARGET_ID}" {DAT_TARGET_SEL}>{DAT_TARGET}</option>
<!-- END target -->
                    </select>
                </td>
                <td class="content_tbl_row4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','targets','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td>{LANG_NAGIOS_VERSION}</td>
                <td>
                    <select title="{LANG_NAGIOS_VERSION}" name="selValue2" id="selValue2" tabindex="21" class="selectborder">
                        <option value="4" {VER_SELECTED_4}>4.x</option>
                        <option value="3" {VER_SELECTED_3}>3.x</option>
                        <option value="1" {VER_SELECTED_1}>2.x</option>
                        <option value="2" {VER_SELECTED_2}>2.9</option>
                    </select>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','version','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_ENABLE_COMMON_DOMAIN}</td>
                <td>
                    <select title="{LANG_ENABLE_COMMON_DOMAIN}" name="selValue3" id="selValue3" tabindex="22" class="selectborder">
                        <option value="0" {ENA_COMMON_SELECTED_0}>{DISABLE}</option>
                        <option value="1" {ENA_COMMON_SELECTED_1}>{ENABLE}</option>
                    </select>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','enable_common','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr {RESTRICT_GROUP_ADMIN}>
                <td>{LANG_ACCESS_GROUP}</td>
                <td>
                    <select title="{LANG_ACCESS_GROUP}" name="selAccGr" tabindex="23" class="selectborder">
<!-- BEGIN acc_group -->
                        <option value="{DAT_ACC_GROUP_ID}" {DAT_ACC_GROUP_SEL}>{DAT_ACC_GROUP}</option>
<!-- END acc_group -->
                    </select>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','accessgroup','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_ACTIVE}</td>
                <td colspan="2" ><input title="{LANG_ACTIVE}" name="chbActive" type="checkbox" class="checkbox" id="chbActive" value="1" {ACT_CHECKED} {ACT_DISABLE}>
                    <input name="modus" type="hidden" id="modus" value="{MODUS}">
                    <input name="hidId" type="hidden" id="hidId" value="{DAT_ID}">
                    <input name="hidLimit" type="hidden" id="hidLimit" value="{LIMIT}"></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><input name="subForm" type="button" id="subForm" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE}>&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span><span class="redmessage" style="padding-left:30px;">{WARNING}</span></td>
            </tr>
        </table>
    </form>
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->