<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : Group template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datainsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <script type="text/javascript">
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
                document.getElementById("selValue1").disabled = false;
                document.frmGroupInsert.submit();
                document.frmGroupInsert.subForm.disabled = true;
            }
        }
        // Check form entries
        function checkForm() {
            // Are all required fields filled in?
            const fields1 = "tfValue1,tfValue2";
            const msg1 = "{FILL_ALLFIELDS}";
            const msg2 = "{FILL_ILLEGALCHARS}";
            const header = "{LANG_FORMCHECK}";
            const form = document.frmGroupInsert;
            let check = checkfields(fields1, form, myFocusObject);
            if (check === false) {
                msginit(msg1,header,1);
                return false;
            }
            // Check for illegal chars
            if (form.tfValue1.value.match(/[^\w.-]/)) {
                msginit(msg2+" {LANG_GROUPNAME}",header,1);
                form.tfValue1.focus();
                return false;
            }
        }
        // Insert group user
        function insertGroupUser() {
            const txtUser = document.frmGroupInsert.selValue1.value;
            let txtRights = "";
            if (document.frmGroupInsert.chbRead.checked === true) {
                txtRights=txtRights+"1-";
            } else {
                txtRights=txtRights+"0-";
            }
            if (document.frmGroupInsert.chbWrite.checked === true) {
                txtRights=txtRights+"1-";
            } else {
                txtRights=txtRights+"0-";
            }
            if (document.frmGroupInsert.chbLink.checked === true) {
                txtRights=txtRights+"1";
            } else {
                txtRights=txtRights+"0";
            }
            if (txtUser === "") {
                const header = "{LANG_FORMCHECK}";
                msginit("{LANG_INSERT_ALL_VARIABLE}",header,1);
                return false;
            }
            document.getElementById("variableframe").src = "{BASE_PATH}admin/groupusers.php?dataId={DAT_ID}&version={VERSION}&mode=add&user="+txtUser+"&rights="+txtRights;
        }
        //-->
    </script>
    <form name="frmGroupInsert" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1">{LANG_GROUPNAME} *</td>
                <td><input title="{LANG_GROUPNAME}" name="tfValue1" type="text" id="tfValue1" tabindex="1" value="{DAT_GROUPNAME}" {NAME_DISABLE} class="inpmust" ><input name="tfValue3" type="hidden" id="tfValue3" value="{DAT_GROUPNAME}"></td>
            </tr>
            <tr>
                <td>{LANG_DESCRIPTION} *</td>
                <td><input title="{LANG_DESCRIPTION}" name="tfValue2" type="text" id="tfValue2" tabindex="2" value="{DAT_DESCRIPTION}" size="40"  style="width:350px" class="inpmust" ></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" style="padding-bottom:5px"><b>{LANG_USER_DEFINITIONS}</b></td>
            </tr>
            <tr>
                <td colspan="2" style="padding-bottom:2px" valign="top">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td style="width:240px;padding-left:5px;"><i>{LANG_USER_NAME}</i></td>
                            <td><i>{LANG_USER_RIGHTS}</i></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-bottom:10px"><iframe id="variableframe" frameborder="0" src="{BASE_PATH}admin/groupusers.php?dataId={DAT_ID}&amp;linktab=tbl_lnkGroupToUser" style="width:540px;height:150px;border:1px solid #000000"></iframe></td>
            </tr>
            <tr>
                <td colspan="2">
                    <table cellpadding="0" cellspacing="1" border="0"  class="inserttable">
                        <tr>
                            <td class="content_tbl_row1">{LANG_USER_NAME}</td>
                            <td>
                                <select title="{LANG_USER_NAME}" name="selValue1" id="selValue1" class="selectborder">
                                    <!-- BEGIN users -->
                                    <option value="{DAT_USER_ID}">{DAT_USER}</option>
                                    <!-- END users -->
                                </select></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>{LANG_USER_RIGHTS}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="width:20px"><input title="{LANG_USER_RIGHTS} {LANG_READ}" type="checkbox" name="chbRead" id="chbRead" value="1" {CHB_READ_SEL}></td>
                                        <td style="width:48px">{LANG_READ}&nbsp;&nbsp;&nbsp;</td>
                                        <td style="width:20px"><input title="{LANG_USER_RIGHTS} {LANG_WRITE}" type="checkbox" name="chbWrite" id="chbWrite" value="1" {CHB_WRITE_SEL}></td>
                                        <td style="width:48px">{LANG_WRITE}&nbsp;&nbsp;&nbsp;</td>
                                        <td style="width:20px"><input title="{LANG_USER_RIGHTS} {LANG_LINK}" type="checkbox" name="chbLink" id="chbLink" value="1" {CHB_LINK_SEL}></td>
                                        <td style="width:48px">{LANG_LINK}&nbsp;&nbsp;&nbsp;</td>
                                        <td style="width:54px"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('group','userrights','all','Info')" style="vertical-align:text-bottom; padding-bottom:2px!" ></td>
                                    </tr>
                                </table>
                            </td>
                            <td><input type="button" name="butVariableDefinition" value="{LANG_INSERT}" onClick="insertGroupUser()"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td>{LANG_ACTIVE}</td>
                <td><input title="{LANG_ACTIVE}" name="chbActive" type="checkbox" class="checkbox" id="chbActive" value="1" {ACT_CHECKED} {ACT_DISABLE}>
                    <input name="modus" type="hidden" id="modus" value="{MODUS}">
                    <input name="hidId" type="hidden" id="hidId" value="{DAT_ID}">
                    <input name="hidLimit" type="hidden" id="hidLimit" value="{LIMIT}">
                </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2"><input name="subForm" type="button" id="subForm" value="{LANG_SAVE}" onClick="LockButton()" {DISABLE_SAVE}>&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort()" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span></td>
            </tr>
            <tr>
                <td colspan="2"  style="padding-top:15px"><span class="redmessage">{WARNING}</span></td>
            </tr>
        </table>
    </form>
</div>
<div id="mutdialoguser">
    <div id="mutdialogusercontent" class="bd"></div>
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->