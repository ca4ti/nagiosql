<!-- (c) 2005-2018 by Martin Willisegger -->
<!-- -->
<!-- Project    : NagiosQL -->
<!-- Component  : serviceeextinfo template -->
<!-- Website    : https://sourceforge.net/projects/nagiosql/ -->
<!-- Date       : $LastChangedDate: 2018-04-01 11:15:21 +0200 (Sun, 01 Apr 2018) $ -->
<!-- Author     : $LastChangedBy: martin $ -->
<!-- Version    : 3.4.0 -->
<!-- Revision   : $LastChangedRevision: 7 $ -->
<!-- BEGIN header -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <!-- END header -->
    <!-- BEGIN datainsert -->
    <script language="JavaScript" type="text/JavaScript">
        <!--
        // Interrupt input
        function abort() {
            this.location.href = "{ACTION_INSERT}?limit={LIMIT}";
        }
        // Update form
        function update() {
            document.forms.frmDetail.modus.value = "refresh";
            document.forms.frmDetail.submit();
        }
        // Send form
        function LockButton() {
            if (checkForm() == false) {
                return false;
            } else {
                document.frmDetail.submit();
                document.frmDetail.subForm.disabled = true;
            }
        }
        // Check form entries
        function checkForm() {
            // Are all required fields filled in?
            var fields1 = "selValue1,selValue2";
            var msg1    = "{FILL_ALLFIELDS}";
            var header  = "{LANG_FORMCHECK}";
            var form    = document.frmDetail;
            var check   = false;
            check = checkfields2(fields1,form,myFocusObject);
            if (check == false) {
                msginit(msg1,header,1);
                return false;
            }
        }
        //-->
    </script>
    <form name="frmDetail" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1">{LANG_HOST_NAME} *</td>
                <td class="content_tbl_row2">
                    <select name="selValue1" id="selValue1" class="selectbordermust inpmust" onChange="update()" tabindex="1">
                        <!-- BEGIN host -->
                        <option value="{DAT_HOST_ID}" class="empty_class inpmust {SPECIAL_STYLE}" {DAT_HOST_SEL}>{DAT_HOST}</option>
                        <!-- END host -->
                    </select>
                </td>
                <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceextinfo','host_name','all','Info');" class="infobutton_1"></td>
                <td class="content_tbl_row1">{LANG_ICON_IMAGE}</td>
                <td class="content_tbl_row2"><input name="tfValue4" type="text" id="tfValue4" value="{DAT_ICON_IMAGE}" tabindex="5"></td>
                <td class="content_tbl_row4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceextinfo','icon_image','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_SERVICE_DESCRIPTION} *</td>
                <td>
                    <select name="selValue2" id="selValue2" class="selectbordermust inpmust" tabindex="2">
                        <!-- BEGIN service_extinfo -->
                        <option value="{DAT_SERVICE_EXTINFO_ID}" class="empty_class inpmust {SPECIAL_STYLE}" {DAT_SERVICE_EXTINFO_SEL}>{DAT_SERVICE_EXTINFO}</option>
                        <!-- END service_extinfo -->
                    </select>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceextinfo','service_description','all','Info');" class="infobutton_1"></td>
                <td>{LANG_ICON_IMAGE_ALT_TEXT}</td>
                <td><input name="tfValue5" type="text" id="tfValue5" value="{DAT_ICON_IMAGE_ALT}" tabindex="6"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceextinfo','icon_image_alt','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_NOTES}</td>
                <td><input name="tfValue1" type="text" id="tfValue1" value="{DAT_NOTES}" tabindex="3"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceextinfo','notes','all','Info');" class="infobutton_1"></td>
                <td>{LANG_ACTION_URL}</td>
                <td><input name="tfValue3" type="text" id="tfValue3" value="{DAT_ACTION_URL}" tabindex="7"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceextinfo','action_url','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_NOTES_URL}</td>
                <td><input name="tfValue2" type="text" id="tfValue2" value="{DAT_NOTES_URL}" tabindex="4"></td>
                <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('serviceextinfo','notes_url','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_REGISTERED}</td>
                <td><input name="chbRegister" type="checkbox" class="checkbox" id="chbRegister" value="1" {REG_CHECKED} tabindex="8"></td>
                <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','registered','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_ACTIVE}</td>
                <td colspan="2"><input name="chbActive" type="checkbox" class="checkbox" id="chbActive" value="1" {ACT_CHECKED} {ACT_DISABLED} tabindex="9">
                    <input name="modus" type="hidden" id="modus" value="{MODUS}">
                    <input name="hidId" type="hidden" id="hidId" value="{DAT_ID}">
                    <input name="hidLimit" type="hidden" id="hidLimit" value="{LIMIT}"></td>
                <td {RESTRICT_GROUP_ADMIN}>{LANG_ACCESS_GROUP}</td>
                <td {RESTRICT_GROUP_ADMIN}>
                    <select name="selAccGr" class="selectborder" tabindex="10">
                        <!-- BEGIN acc_group -->
                        <option value="{DAT_ACC_GROUP_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_ACC_GROUP_SEL}>{DAT_ACC_GROUP}</option>
                        <!-- END acc_group -->
                    </select>
                </td>
                <td {RESTRICT_GROUP_ADMIN}><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','accessgroup','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><input name="subForm" type="button" id="subForm" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE} tabindex="11">&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{LANG_ABORT}" tabindex="12"><span class="required_info">* {LANG_REQUIRED}</span></td>
                <td colspan="3"><span class="redmessage">{WARNING}</span></td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
        </table>
    </form>
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->