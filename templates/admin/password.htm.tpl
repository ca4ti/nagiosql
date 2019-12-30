<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : Password template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN passwordsite -->
<div id="content_main">
    <div id="content_title">{LANG_CHANGE_PASSWORD}</div>
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
                document.frmPasswdInsert.submit();
                document.frmPasswdInsert.subForm.disabled = true;
            }
        }
        // Check form entries
        function checkForm() {
            // Are all required fields filled in?
            const fields1 = "tfValue1,tfValue2";
            const msg1 = "{FILL_ALLFIELDS}";
            const msg2 = "{FILL_NEW_PASSWD_NOT_EQUAL}";
            const msg3 = "{FILL_NEW_PWDSHORT}";
            const header = "{LANG_FORMCHECK}";
            const form = document.frmPasswdInsert;
            let check;
            check = checkfields(fields1,form,myFocusObject);
            if (check === false) {
                msginit(msg1,header,1);
                return false;
            }
            // Are the passwords not equal
            if(form.tfValue2.value !== form.tfValue3.value)  {
                msginit(msg2,header,1);
                form.tfValue2.focus();
                return false;
            }
            // Is the new passord too short
            if((form.tfValue2.value !== "") && (form.tfValue2.value.length <= 5)) {
                msginit(msg3,header,1);
                form.tfValue2.focus();
                return false;
            }
        }
        //-->
    </script>
    <form name="frmPasswdInsert" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1">{LANG_OLD_PASSWORD} *</td>
                <td class="content_tbl_row2"><input title="{LANG_OLD_PASSWORD}" name="tfValue1" type="password" id="tfValue1" tabindex="1" maxlength="15" class="inpmust"></td>
            </tr>
            <tr>
                <td>{LANG_NEW_PASSWORD} *</td>
                <td><input title="{LANG_NEW_PASSWORD}" name="tfValue2" type="password" id="tfValue2" tabindex="3" maxlength="15" class="inpmust"></td>
            </tr>
            <tr>
                <td>{LANG_CONFIRM_PASSWORD} *</td>
                <td><input title="{LANG_CONFIRM_PASSWORD}" name="tfValue3" type="password" id="tfValue3" tabindex="4" maxlength="15" class="inpmust"></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2"><input name="subForm" type="button" id="subForm" value="{LANG_SAVE}" onClick="LockButton();" {ADD_CONTROL}>&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span></td>
            </tr>
        </table>
    </form>
    <br>
    <p><span class="redmessage">{ERRORMESSAGE}</span></p>
</div>
<div id="msgcontainer"></div>
<!-- END passwordsite -->