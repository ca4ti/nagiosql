<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : Configuration target administration template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datainsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <div class="redmessage">{PATHMESSAGE}</div>
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
                document.frmDomainInsert.submit();
                document.frmDomainInsert.subForm.disabled = true;
            }
        }
        // Check form entries
        function checkForm() {
            // Are all required fields filled in?
            let fields1;
            let fields2;
            let fields3;
            if (document.frmDomainInsert.hidId.value === '0') {
                fields1 = "tfValue1,tfValue2";
                fields2 = "";
                fields3 = "";
            } else {
                fields1 = "tfValue1,tfValue2,tfValue4,tfValue8,tfValue9,tfValue10,tfValue11,tfValue12,tfValue13,tfValue14,tfValue20,tfValue21,tfValue22";
                fields2 = "tfValue5,tfValue6";
                fields3 = "tfValue5,tfValue7";
            }
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
            if (form.selValue1.value === '2') {
                let check2;
                check2 = checkfields(fields2,form,myFocusObject);
                if (check2 === false) {
                    msginit(msg1,header,1);
                    return false;
                }
            }
            if (form.selValue1.value === '3') {
                let check2a;
                let check2b;
                check2a = checkfields(fields2,form,myFocusObject);
                check2b = checkfields(fields3,form,myFocusObject);
                if ((check2a === false) && (check2b === false)) {
                    msginit(msg1,header,1);
                    return false;
                }
            }
            // Check for illegal chars
            if (form.tfValue1.value.match(/[^a-zA-Z0-9.@_-]/)) {
                msginit(msg2+" {LANG_DOMAIN}",header,1);
                form.tfValue1.focus();
                return false;
            }
        }
        // Check required fields
        function checkMust() {
            if (document.frmDomainInsert.hidId.value !== '0') {
                if ((document.frmDomainInsert.tfValue5.value === "") &&
                    (document.frmDomainInsert.tfValue6.value === "") &&
                    (document.frmDomainInsert.tfValue7.value === "")) {
                    document.frmDomainInsert.tfValue5.className = "inpmust";
                    document.frmDomainInsert.tfValue6.className = "inpmust";
                    document.frmDomainInsert.tfValue7.className = "inpmust";
                } else if (((document.frmDomainInsert.tfValue5.value !== "") ||
                    (document.frmDomainInsert.tfValue6.value !== "")) &&
                    (document.frmDomainInsert.tfValue7.value === "")) {
                    document.frmDomainInsert.tfValue7.className   = "inp";
                    document.frmDomainInsert.tfValue5.className = "inpmust";
                    document.frmDomainInsert.tfValue6.className = "inpmust";
                } else if (document.frmDomainInsert.tfValue7.value !== "") {
                    document.frmDomainInsert.tfValue7.className   = "inpmust";
                    document.frmDomainInsert.tfValue5.className = "inpmust";
                    document.frmDomainInsert.tfValue6.className = "inp";
                }
            }
            if (document.frmDomainInsert.selValue1.value === '2') {
                document.frmDomainInsert.tfValue5.className = "inpmust";
                document.frmDomainInsert.tfValue6.className = "inpmust";
            }
        }
        // Enable hidden fields
        function showFields(key) {
            if (key === '1') {
                document.getElementById('user').className    = "elementHide";
                document.getElementById('passwd').className  = "elementHide";
                document.getElementById('keypath').className = "elementHide";
                document.getElementById('ftps').className    = "elementHide";
                document.getElementById('port').className    = "elementHide";
            } else if (key === '2') {
                document.getElementById('user').className    = "elementShow";
                document.getElementById('passwd').className  = "elementShow";
                document.getElementById('keypath').className = "elementHide";
                document.getElementById('ftps').className    = "elementShow";
                document.getElementById('port').className    = "elementHide";
                document.frmDomainInsert.tfValue6.className  = "inpmust";
                document.frmDomainInsert.tfValue5.className  = "inpmust";
            } else {
                document.getElementById('user').className    = "elementShow";
                document.getElementById('passwd').className  = "elementShow";
                document.getElementById('keypath').className = "elementShow";
                document.getElementById('ftps').className    = "elementHide";
                document.getElementById('port').className    = "elementShow";
                document.frmDomainInsert.tfValue5.className  = "inpmust";
                document.frmDomainInsert.tfValue6.className  = "inpmust";
                document.frmDomainInsert.tfValue7.className  = "inpmust";
                if (document.frmDomainInsert.tfValue7.value !== "") {
                    document.frmDomainInsert.tfValue6.className = "inp";
                } else if (document.frmDomainInsert.tfValue6.value !== "") {
                    document.frmDomainInsert.tfValue7.className = "inp";
                }
            }
        }
        //-->
    </script>
    <form name="frmDomainInsert" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1" style=" width:270px;">{LANG_CONFIG_TARGET} *</td>
                <td class="content_tbl_row2"><input title="{LANG_CONFIG_TARGET}" name="tfValue1" type="text" id="tfValue1" tabindex="1" value="{DAT_TARGET}" style="width:350px;" {DOMAIN_DISABLE} class="inpmust {LOCKCLASS}"></td>
                <td class="content_tbl_row2"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','domain','all','Info');" class="infobutton_1"><input name="tfValue3" type="hidden" id="tfValue3" value="{DAT_DOMAIN}"></td>
            </tr>
            <tr>
                <td>{LANG_DESCRIPTION} *</td>
                <td colspan="2"><input title="{LANG_DESCRIPTION}" name="tfValue2" type="text" id="tfValue2" tabindex="2" value="{DAT_ALIAS}" style="width:350px;" class="inpmust"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_SERVER_NAME} *</td>
                <td colspan="2"><input title="{LANG_SERVER_NAME}" name="tfValue4" type="text" id="tfValue4" tabindex="4" value="{DAT_SERVER}" {SERVER_DISABLE} style="width:350px;" class="inpmust"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_METHOD}</td>
                <td colspan="2">
                    <select title="{LANG_METHOD}" name="selValue1" id="selValue1" tabindex="5" {METHOD_DISABLE} onchange="showFields(this.value);" class="selectborder">
                        <option value="1" {FILE_SELECTED}>Fileaccess</option>
                        <option value="2" {FTP_SELECTED}>FTP</option>
                        <option value="3" {SFTP_SELECTED}>SSH/SFTP</option>
                    </select>
                </td>
            </tr>
            <tr id="user" class="{CLASS_NAME_1}">
                <td>{LANG_USERNAME} *</td>
                <td colspan="2"><input title="{LANG_USERNAME}" name="tfValue5" type="text" id="tfValue5" tabindex="5" value="{DAT_USER}" style="width:350px;" class="inpmust" onchange="checkMust();"></td>
            </tr>
            <tr id="passwd" class="{CLASS_NAME_1}">
                <td>{LANG_PASSWORD} *</td>
                <td colspan="2"><input title="{LANG_PASSWORD}" name="tfValue6" type="password" id="tfValue6" tabindex="6" value="{DAT_PASSWORD}" style="width:350px;" class="inpmust" onchange="checkMust();"></td>
            </tr>
            <tr id="port" class="{CLASS_NAME_1}">
                <td>{LANG_SSH_PORT}</td>
                <td colspan="2"><input title="{LANG_SSH_PORT}" name="tfValue23" type="text" id="tfValue23" tabindex="7" value="{DAT_PORT}" style="width:350px;"></td>
            </tr>
            <tr id="keypath" class="{CLASS_NAME_2}">
                <td>{LANG_SSH_KEY} *</td>
                <td><input title="{LANG_SSH_KEY}" name="tfValue7" type="text" id="tfValue7" tabindex="8" value="{DAT_SSH_KEY_PATH}" style="width:350px;" class="inpmust" onchange="checkMust();"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','ssh_host_key','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr id="ftps" class="{CLASS_NAME_3}">
                <td>{LANG_FTPS}</td>
                <td><input title="{LANG_ACTIVE}" name="chbValue1" type="checkbox" class="checkbox" id="chbValue1" value="1" {FTPS_CHECKED}></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','ftps_option','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td colspan="3"><strong>{LANG_CONFIGURATION_DIRECTORIES}</strong></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_BASE_DIRECTORY} *</td>
                <td><input title="{LANG_BASE_DIRECTORY}" name="tfValue8" type="text" id="tfValue8" tabindex="9" value="{DAT_BASEDIR}"  style="width:350px;" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','basedir','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_HOST_DIRECTORY} *</td>
                <td><input title="{LANG_HOST_DIRECTORY}" name="tfValue9" type="text" id="tfValue9" tabindex="10" value="{DAT_HOSTCONFIG}"  style="width:350px;" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','hostdir','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_SERVICE_DIRECTORY} *</td>
                <td><input title="{LANG_SERVICE_DIRECTORY}" name="tfValue10" type="text" id="tfValue10" tabindex="11" value="{DAT_SERVICECONFIG}"  style="width:350px;" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','servicedir','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_BACKUP_DIRECTORY} *</td>
                <td><input title="{LANG_BACKUP_DIRECTORY}" name="tfValue11" type="text" id="tfValue11" tabindex="12" value="{DAT_BACKUPDIR}"  style="width:350px;" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','backupdir','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_HOST_BACKUP_DIRECTORY} *</td>
                <td><input title="{LANG_HOST_BACKUP_DIRECTORY}" name="tfValue12" type="text" id="tfValue12" tabindex="13" value="{DAT_HOSTBACKUP}"  style="width:350px;" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','backuphostdir','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_SERVICE_BACKUP_DIRECTORY} *</td>
                <td><input title="{LANG_SERVICE_BACKUP_DIRECTORY}" name="tfValue13" type="text" id="tfValue13" tabindex="14" value="{DAT_SERVICEBACKUP}"  style="width:350px;" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','backupservicedir','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td colspan="3"><strong>{LANG_NAGIOS_DIRECTORIES}</strong></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_NAGIOS_BASEDIR} *</td>
                <td><input title="{LANG_NAGIOS_BASEDIR}" name="tfValue14" type="text" id="tfValue14" tabindex="15" value="{DAT_NAGIOSBASEDIR}"  style="width:350px;" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','nagiosbasedir','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_IMPORT_DIRECTORY}</td>
                <td><input title="{LANG_IMPORT_DIRECTORY}" name="tfValue15" type="text" id="tfValue15" tabindex="16" value="{DAT_IMPORTDIR}"  style="width:350px;"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','importdir','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_PICTURE_DIRECTORY}</td>
                <td><input title="{LANG_PICTURE_DIRECTORY}" name="tfValue16" type="text" id="tfValue16" tabindex="17" value="{DAT_PICTUREDIR}"  style="width:350px;"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','picturedir','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_NAGIOS_COMMAND_FILE}</td>
                <td><input title="{LANG_NAGIOS_COMMAND_FILE}" name="tfValue17" type="text" id="tfValue17" tabindex="18" value="{DAT_COMMANDFILE}"  style="width:350px;"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','commandfile','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_NAGIOS_BINARY_FILE}</td>
                <td><input title="{LANG_NAGIOS_BINARY_FILE}" name="tfValue18" type="text" id="tfValue18" tabindex="19" value="{DAT_BINARYFILE}"  style="width:350px;"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','binary','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_NAGIOS_PROCESS_FILE}</td>
                <td><input title="{LANG_NAGIOS_PROCESS_FILE}" name="tfValue19" type="text" id="tfValue19" tabindex="20" value="{DAT_PIDFILE}"  style="width:350px;"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','pidfile','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_NAGIOS_CONFIG_FILE} *</td>
                <td><input title="{LANG_NAGIOS_CONFIG_FILE}" name="tfValue20" type="text" id="tfValue20" tabindex="21" value="{DAT_CONFFILE}"  style="width:350px;" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','conffile','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_NAGIOS_CGI_FILE} *</td>
                <td><input title="{LANG_NAGIOS_CGI_FILE}" name="tfValue21" type="text" id="tfValue21" tabindex="22" value="{DAT_CGIFILE}"  style="width:350px;" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','cgifile','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr {COMMON_INVISIBLE}>
                <td>{LANG_NAGIOS_RESOURCE_FILE} *</td>
                <td><input title="{LANG_NAGIOS_RESOURCE_FILE}" name="tfValue22" type="text" id="tfValue22" tabindex="23" value="{DAT_RESOURCEFILE}"  style="width:350px;" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('domain','resourcefile','all','Info');" class="infobutton_1"></td>
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
                <td colspan="3"><input name="subForm" type="button" id="subForm" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE}>&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span></td>
            </tr>
        </table>
    </form>
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<script type="text/javascript">
    <!--
    checkMust();
    //-->
</script>
<!-- END datainsert -->