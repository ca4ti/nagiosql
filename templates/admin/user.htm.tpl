<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : User template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datainsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <script type="text/javascript">
        <!--
        // Abort form
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
                document.frmUserInsert.submit();
                document.frmUserInsert.subForm.disabled = true;
            }
        }
        // Check form entries
        function checkForm() {
            // Are all required fields filled in?
            const fields1 = "tfValue1,tfValue2";
            const msg1 = "{FILL_ALLFIELDS}";
            const msg2 = "{FILL_ILLEGALCHARS}";
            const msg3 = "{FILL_PASSWD_NOT_EQUAL}";
            const msg4 = "{FILL_PASSWORD}";
            const msg5 = "{FILL_PWDSHORT}";
            const header = "{LANG_FORMCHECK}";
            const form = document.frmUserInsert;
            let check;
            check = checkfields(fields1, form, myFocusObject);
            if (check === false) {
                msginit(msg1,header,1);
                return false;
            }
            // Check for illegal chars
            if (form.tfValue1.value.match(/[^a-zA-Z0-9.@_-]/)) {
                msginit(msg2+" {LANG_USERNAME}",header,1);
                form.tfValue1.focus();
                return false;
            }
            // The passwords do not match
            if(form.tfValue3.value !== form.tfValue4.value)  {
                msginit(msg3,header,1);
                form.tfValue3.focus();
                return false;
            }
            // One password is missing
            if((form.tfValue3.value === "") && (form.hidId.value === "")) {
                msginit(msg4,header,1);
                form.tfValue3.focus();
                return false;
            }
            // The password is too short
            if((form.tfValue3.value !== "") && (form.tfValue3.value.length <= 5)) {
                msginit(msg5,header,1);
                form.tfValue3.focus();
                return false;
            }
        }
        //-->
    </script>
    <form name="frmUserInsert" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td>{LANG_USERNAME} *</td>
                <td colspan="2"><input title="{LANG_USERNAME}" name="tfValue1" type="text" id="tfValue1" tabindex="1" value="{DAT_USERNAME}" {NAME_DISABLE} class="inpmust" ><input name="tfValue5" type="hidden" id="tfValue5" value="{DAT_USERNAME}"></td>
            </tr>
            <tr>
                <td>{LANG_DESCRIPTION} *</td>
                <td colspan="2"><input title="{LANG_DESCRIPTION} " name="tfValue2" type="text" id="tfValue2" tabindex="2" value="{DAT_ALIAS}" size="40"  style="width:350px" class="inpmust" ></td>
            </tr>
            <tr>
                <td>{LANG_PASSWORD} {PASSWORD_MUST_STAR}</td>
                <td colspan="2"><input title="{LANG_PASSWORD}" name="tfValue3" type="password" id="tfValue3" value="" tabindex="3" {PASSWORD_MUST} ></td>
            </tr>
            <tr>
                <td>{LANG_CONFIRM_PASSWORD} {PASSWORD_MUST_STAR}</td>
                <td colspan="2"><input title="{LANG_CONFIRM_PASSWORD}" name="tfValue4" type="password" id="tfValue4" value="" tabindex="4" {PASSWORD_MUST} ></td>
            </tr>
            <tr>
                <td>{LANG_LANGUAGE}</td>
                <td>
                    <select title="{LANG_LANGUAGE}" name="selValue1" id="selValue1" class="selectborder">
<!-- BEGIN language_name -->
                        <option value="{DAT_LANGUAGE_NAME_ID}" {DAT_LANGUAGE_NAME_SEL}>{DAT_LANGUAGE_NAME}</option>
<!-- END language_name -->
                    </select>
                </td>
                <td class="content_tbl_row4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('user','language','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_STANDARD_DOMAIN}</td>
                <td>
                    <select title="{LANG_STANDARD_DOMAIN}" name="selValue2" id="selValue2" class="selectborder">
<!-- BEGIN std_domain -->
                        <option value="{DAT_STD_DOMAIN_ID}" {DAT_STD_DOMAIN_SEL}>{DAT_STD_DOMAIN}</option>
<!-- END std_domain -->
                    </select>
                </td>
                <td class="content_tbl_row4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('user','standarddomain','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td class="content_tbl_row1">{LANG_ADMIN_ENABLE}</td>
                <td class="content_tbl_row2"><input title="{LANG_ADMIN_ENABLE}" name="chbValue1" type="checkbox" class="checkbox" id="chbValue1" value="1" {ADMINENABLE_CHECKED} {ADMINENABLE_DISABLE}></td>
                <td style="width:145px;"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('user','adminenable','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_WEBSERVER_AUTH}</td>
                <td><input title="{LANG_WEBSERVER_AUTH}" name="chbValue2" type="checkbox" class="checkbox" id="chbValue2" value="1" {WSAUTH_CHECKED} {WSAUTH_DISABLE}></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('user','webserverauth','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td>{LANG_ACTIVE}</td>
                <td colspan="2"><input title="{LANG_ACTIVE}" name="chbActive" type="checkbox" class="checkbox" id="chbActive" value="1" {ACT_CHECKED} {ACT_DISABLE}>
                    <input name="modus" type="hidden" id="modus" value="{MODUS}">
                    <input name="hidId" type="hidden" id="hidId" value="{DAT_ID}">
                    <input name="hidLimit" type="hidden" id="hidLimit" value="{LIMIT}">
                </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><input name="subForm" type="button" id="subForm" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE}>&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span></td>
            </tr>
        </table>
    </form>
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->