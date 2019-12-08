<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : servicetemplate template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datainsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <!--suppress JSUnusedLocalSymbols -->
    <script type="text/JavaScript">
        <!--
        // Initialize change dialog
        openMutDlgInit("mselValue1","mutdialogvalue1","{LANG_MODIFY_SELECTION}: {LANG_HOSTS}","mutvalue1","{LANG_SAVE}","{LANG_ABORT}");
        openMutDlgInit("mselValue2","mutdialogvalue2","{LANG_MODIFY_SELECTION}: {LANG_HOST_GROUPS}","mutvalue2","{LANG_SAVE}","{LANG_ABORT}");
        openMutDlgInit("mselValue3","mutdialogvalue3","{LANG_MODIFY_SELECTION}: {LANG_SERVICEGROUPS}","mutvalue3","{LANG_SAVE}","{LANG_ABORT}","1");
        openMutDlgInit("mselValue4","mutdialogvalue4","{LANG_MODIFY_SELECTION}: {LANG_CONTACTS}","mutvalue4","{LANG_SAVE}","{LANG_ABORT}");
        openMutDlgInit("mselValue5","mutdialogvalue5","{LANG_MODIFY_SELECTION}: {LANG_CONTACT_GROUPS}","mutvalue5","{LANG_SAVE}","{LANG_ABORT}");
        openMutDlgInit("mselValue6","mutdialogvalue6","{LANG_MODIFY_SELECTION}: {LANG_PARENT_SERVICES}","mutvalue6","{LANG_SAVE}","{LANG_ABORT}");
        const version = "{VERSION}";
        const argcount = 0;

        // Interrupt input
        function abort() {
            this.location.href = "{ACTION_INSERT}?limit={LIMIT}";
        }
        // Set iframe URL
        function setIframe(cname) {
            document.getElementById("fullcommand").src = "{BASE_PATH}admin/commandline.php?cname="+cname;
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
                const selfields = "mselValue1,mselValue2,mselValue4,mselValue5,mselValue3,mselValue6";
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
            const msg2 = "{FILL_ILLEGALCHARS}";
            const header = "{LANG_FORMCHECK}";
            const form = document.frmDetail;
            let check;
            check = checkfields(fields1,form,myFocusObject);
            if (check === false) {
                msginit(msg1,header,1);
                return false;
            }
            // Check command arguments
            for (let i=1;i<=argcount;i++) {
                if (document.getElementById("tfArg"+i).value === "") {
                    confirminit("{FILL_ARGUMENTS}","{LANG_SECURE_QUESTION}",2,"{LANG_YES}","{LANG_NO}",2);
                    return false;
                }
            }
            // Check for illegal chars
            if (form.tfValue1.value.match(/[^a-zA-Z0-9.@_-]/)) {
                msginit(msg2+" {LANG_TEMPLATE_NAME}",header,1);
                form.frmDetail.focus();
                return false;
            }
        }
        // Insert template definitions
        function insertDefintion() {
            const txtDef = document.frmDetail.selTemplate.value;
            document.getElementById("templframe").src = "{BASE_PATH}admin/templatedefinitions.php?dataId={DAT_ID}&type=servicetemplate&mode=add&def="+txtDef;
        }
        // Insert free variable definitions
        function insertDefintionVar() {
            const txtDef = document.frmDetail.txtVariablename.value;
            const txtRange = document.frmDetail.txtVariablevalue.value;
            if ((txtDef === "") || (txtRange === "")) {
                const header = "{LANG_FORMCHECK}";
                msginit("{LANG_INSERT_ALL_VARIABLE}",header,1);
                return false;
            }
            document.getElementById("variableframe").src = "{BASE_PATH}admin/variabledefinitions.php?dataId={DAT_ID}&version={VERSION}&mode=add&def="+encodeURIComponent(txtDef)+"&range="+encodeURIComponent(txtRange);
        }
        // Process security questions
        function confOpenerYes(key) {
            if (key === 2) {
                document.getElementById("mselValue1").disabled = false;
                document.getElementById("mselValue2").disabled = false;
                document.getElementById("mselValue3").disabled = false;
                document.getElementById("mselValue4").disabled = false;
                document.getElementById("mselValue5").disabled = false;
                document.getElementById("mselValue6").disabled = false;
                document.frmDetail.submit();
                document.frmDetail.subForm.disabled = true;
            }
        }
        // Check initial state
        function checkInitial(key) {
            const form = document.frmDetail;
            if (key === "o") {
                form.chbGr2b.checked = false;
                form.chbGr2c.checked = false;
                form.chbGr2d.checked = false;
            }
            if (key === "w") {
                form.chbGr2a.checked = false;
                form.chbGr2c.checked = false;
                form.chbGr2d.checked = false;
            }
            if (key === "u") {
                form.chbGr2a.checked = false;
                form.chbGr2b.checked = false;
                form.chbGr2d.checked = false;
            }
            if (key === "c") {
                form.chbGr2a.checked = false;
                form.chbGr2b.checked = false;
                form.chbGr2c.checked = false;
            }
        }
        //-->
    </script>
    <form name="frmDetail" method="post" action="{ACTION_INSERT}">
        <div id="servicetemplate" style="width:909px;" class="yui-navset">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{LANG_COMMON_SETTINGS}</em></a></li>
                <li><a href="#tab2"><em>{LANG_CHECK_SETTINGS}</em></a></li>
                <li><a href="#tab3"><em>{LANG_ALARM_SETTINGS}</em></a></li>
                <li><a href="#tab4"><em>{LANG_ADDON_SETTINGS}</em></a></li>
            </ul>
            <div class="yui-content">
                <div id="tab1">
                    <table border="0" cellpadding="0" class="content_formtable">
                        <tr>
                            <td colspan="6"><strong>{LANG_COMMON_SETTINGS}</strong></td>
                        </tr>
                        <tr>
                            <td class="content_tbl_row1">{LANG_TEMPLATE_NAME} *</td>
                            <td class="content_tbl_row2"><input title="{LANG_TEMPLATE_NAME}" name="tfValue1" type="text" id="tfValue1" value="{DAT_TEMPLATE_NAME}" class="inpmust" tabindex="1"></td>
                            <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('servicetemplate','template_name','all','Info');" class="infobutton_1"></td>
                            <td class="content_tbl_row1">&nbsp;</td>
                            <td class="content_tbl_row2">&nbsp;</td>
                            <td class="content_tbl_row4">&nbsp;</td>
                        </tr>
                        <tr>
                            <td valign="top">{LANG_HOSTS}<br><br><small>{LANG_CTRLINFO}</small></td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td>
                                            <select title="{LANG_HOSTS}" name="mselValue1[]" size="4" multiple id="mselValue1" class="selectborder" {MSIE_DISABLED}>
                                                <!-- BEGIN hosts -->
                                                <option value="{DAT_HOSTS_ID}" class="empty_class {SPECIAL_STYLE} {IE_HOSTS_SEL}" {DAT_HOSTS_SEL} {OPTION_DISABLED}>{DAT_HOSTS}</option>
                                                <!-- END hosts -->
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top"><img id="mutvalue1" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','hosts','all','Info');" class="infobutton_2"></td>
                            <td valign="top">{LANG_HOST_GROUPS}<br><br><small>{LANG_CTRLINFO}</small></td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td>
                                            <select title="{LANG_HOST_GROUPS}" name="mselValue2[]" size="4" multiple id="mselValue2" class="selectborder" {MSIE_DISABLED}>
                                                <!-- BEGIN hostgroup -->
                                                <option value="{DAT_HOSTGROUP_ID}" class="empty_class {SPECIAL_STYLE} {IE_HOSTGROUP_SEL}" {DAT_HOSTGROUP_SEL} {OPTION_DISABLED}>{DAT_HOSTGROUP}</option>
                                                <!-- END hostgroup -->
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top"><img id="mutvalue2" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','hostgroups','all','Info');" class="infobutton_2"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>&nbsp;</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="+" name="radValue1" type="radio" class="checkbox" id="radValue10" value="0" {DAT_HOS0_CHECKED} tabindex="2"></td>
                                        <td class="radio_cell_2">+</td>
                                        <td class="radio_cell_1"><input title="null" name="radValue1" type="radio" class="checkbox" id="radValue11" value="1" {DAT_HOS1_CHECKED} tabindex="2"></td>
                                        <td class="radio_cell_2">null</td>
                                        <td class="radio_cell_1"><input title="{LANG_STANDARD}" name="radValue1" type="radio" class="checkbox" id="radValue12" value="2" {DAT_HOS2_CHECKED} tabindex="2"></td>
                                        <td class="radio_cell_2">{LANG_STANDARD}</td>
                                    </tr>
                                </table>
                            </td>
                            <td colspan="2"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','tploptions','3','Info');" class="infobutton_1"></td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="+" name="radValue2" type="radio" class="checkbox" id="radValue20" value="0" {DAT_HOG0_CHECKED} tabindex="3"></td>
                                        <td class="radio_cell_2">+</td>
                                        <td class="radio_cell_1"><input title="null" name="radValue2" type="radio" class="checkbox" id="radValue21" value="1" {DAT_HOG1_CHECKED} tabindex="3"></td>
                                        <td class="radio_cell_2">null</td>
                                        <td class="radio_cell_1"><input title="{LANG_STANDARD}" name="radValue2" type="radio" class="checkbox" id="radValue22" value="2" {DAT_HOG2_CHECKED} tabindex="3"></td>
                                        <td class="radio_cell_2">{LANG_STANDARD}</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','tploptions','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td valign="top"><span class="{VERSION_40_VISIBLE}">{LANG_PARENT_SERVICES}<br><br><small>{LANG_CTRLINFO}</small></span></td>
                            <td valign="top">
                                <table cellpadding="0" cellspacing="0" border="0" class="{VERSION_40_VISIBLE}">
                                    <tr>
                                        <td>
                                            <select title="{LANG_PARENT_SERVICES}" name="mselValue6[]" size="4" multiple id="mselValue6" class="selectborder" {MSIE_DISABLED}>
                                                <!-- BEGIN service_parents -->
                                                <option value="{DAT_SERVICE_PARENTS_ID}" class="empty_class {SPECIAL_STYLE} {IE_SERVICE_PARENTS_SEL}" {DAT_SERVICE_PARENTS_SEL} {OPTION_DISABLED}>{DAT_SERVICE_PARENTS}</option>
                                                <!-- END service_parents -->
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top"><span class="{VERSION_40_VISIBLE}"><img id="mutvalue6" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onClick="dialoginit('service','parents','all','Info');" class="infobutton_1"></span></td>
                            <td valign="top">{LANG_SERVICEGROUPS}<br><br><small>{LANG_CTRLINFO}</small></td>
                            <td valign="top">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td>
                                            <select title="{LANG_SERVICEGROUPS}" name="mselValue3[]" size="4" multiple id="mselValue3" class="selectborder" {MSIE_DISABLED}>
                                                <!-- BEGIN servicegroup -->
                                                <option value="{DAT_SERVICEGROUP_ID}" class="empty_class {SPECIAL_STYLE} {IE_SERVICEGROUP_SEL}" {DAT_SERVICEGROUP_SEL} {OPTION_DISABLED}>{DAT_SERVICEGROUP}</option>
                                                <!-- END servicegroup -->
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top"><img id="mutvalue3" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','service_groups','all','Info');" class="infobutton_2"></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0" class="{VERSION_40_VISIBLE}">
                                    <tr>
                                        <td class="radio_cell_1"><input title="+" name="radValue18" type="radio" class="checkbox" id="radValue180" value="0" tabindex="8" {DAT_SEG0_CHECKED}></td>
                                        <td class="radio_cell_2">+</td>
                                        <td class="radio_cell_1"><input title="null" name="radValue18" type="radio" class="checkbox" id="radValue181" value="1" tabindex="8" {DAT_SEG1_CHECKED}></td>
                                        <td class="radio_cell_2">null</td>
                                        <td class="radio_cell_1"><input title="{LANG_STANDARD}" name="radValue18" type="radio" class="checkbox" id="radValue182" value="2" tabindex="8" {DAT_SEG2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_STANDARD}</td>
                                    </tr>
                                </table>
                            </td>
                            <td><span  class="{VERSION_40_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','tploptions','3','Info');" class="infobutton_1"></span></td>
                            <td>&nbsp;</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0" class="{VERSION_30_VISIBLE}">
                                    <tr>
                                        <td class="radio_cell_1"><input title="+" name="radValue3" type="radio" class="checkbox" id="radValue30" value="0" {DAT_SEG0_CHECKED} tabindex="7"></td>
                                        <td class="radio_cell_2">+</td>
                                        <td class="radio_cell_1"><input title="null" name="radValue3" type="radio" class="checkbox" id="radValue31" value="1" {DAT_SEG1_CHECKED} tabindex="7"></td>
                                        <td class="radio_cell_2">null</td>
                                        <td class="radio_cell_1"><input title="{LANG_STANDARD}" name="radValue3" type="radio" class="checkbox" id="radValue32" value="2" {DAT_SEG2_CHECKED} tabindex="7"></td>
                                        <td class="radio_cell_2">{LANG_STANDARD}</td>
                                    </tr>
                                </table>
                            </td>
                            <td><span class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','tploptions','3','Info');" class="infobutton_1"></span></td>
                        </tr>
                        <tr>
                            <td>{LANG_SERVICE_DESCRIPTION} *</td>
                            <td><input title="{LANG_SERVICE_DESCRIPTION}" name="tfValue2" type="text" id="tfValue2" value="{DAT_SERVICE_DESCRIPTION}" tabindex="4"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','service_description','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_DISPLAY_NAME}</td>
                            <td><input title="{LANG_DISPLAY_NAME}" name="tfValue3" type="text" id="tfValue3" value="{DAT_DISPLAY_NAME}" tabindex="5"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','display_name','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_CHECK_COMMAND}</td>
                            <td>
                                <select title="{LANG_CHECK_COMMAND}" name="selValue1" id="selValue1" class="selectborder" onChange="setIframe(this.value);" tabindex="8">
                                    <!-- BEGIN servicecommand -->
                                    <option value="{DAT_SERVICECOMMAND_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_SERVICECOMMAND_SEL}>{DAT_SERVICECOMMAND}</option>
                                    <!-- END servicecommand -->
                                </select>
                            </td>
                            <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','check_command','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td valign="top" style="padding-top:3px;">{LANG_COMMAND_VIEW}</td>
                            <td colspan="5"><iframe scrolling="no" id="fullcommand" name="fullcommand" src="{IFRAME_SRC}" width="100%" height="36"></iframe></td>
                        </tr>
                        <tr>
                            <td>$ARG1$</td>
                            <td><input title="$ARG1$" name="tfArg1" type="text" id="tfArg1" value="{DAT_ARG1}" tabindex="9"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','argument','all','Info');" class="infobutton_1"></td>
                            <td>$ARG5$</td>
                            <td colspan="2"><input title="$ARG5$" name="tfArg5" type="text" id="tfArg5" value="{DAT_ARG5}" tabindex="13"></td>
                        </tr>
                        <tr>
                            <td>$ARG2$</td>
                            <td colspan="2"><input title="$ARG2$" name="tfArg2" type="text" id="tfArg2" value="{DAT_ARG2}" tabindex="10"></td>
                            <td>$ARG6$</td>
                            <td colspan="2"><input title="$ARG6$" name="tfArg6" type="text" id="tfArg6" value="{DAT_ARG6}" tabindex="14"></td>
                        </tr>
                        <tr>
                            <td>$ARG3$</td>
                            <td colspan="2"><input title="$ARG3$" name="tfArg3" type="text" id="tfArg3" value="{DAT_ARG3}" tabindex="11"></td>
                            <td>$ARG7$</td>
                            <td colspan="2"><input title="$ARG7$" name="tfArg7" type="text" id="tfArg7" value="{DAT_ARG7}" tabindex="15"></td>
                        </tr>
                        <tr>
                            <td>$ARG4$</td>
                            <td colspan="2"><input title="$ARG4$" name="tfArg4" type="text" id="tfArg4" value="{DAT_ARG4}" tabindex="12"></td>
                            <td>$ARG8$</td>
                            <td colspan="2"><input title="$ARG8$" name="tfArg8" type="text" id="tfArg8" value="{DAT_ARG8}" tabindex="16"></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding-bottom:5px;"><strong>{LANG_ADDITIONAL_TEMPLATES}</strong></td>
                            <td>{LANG_ACTIVE}</td>
                            <td colspan="2"><input title="{LANG_ACTIVE}" name="chbActive" type="checkbox" class="checkbox" id="chbActive" value="1" {ACT_CHECKED} {ACT_DISABLED} tabindex="7">
                                <input name="hidActive" type="hidden" id="hidActive" value="{ACTIVE}"></td>
                        </tr>
                        <tr>
                            <td style="padding-bottom:2px;padding-left:5px"><i>{LANG_TEMPLATE_NAME}</i></td>
                            <td colspan="5">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="6" style="padding-bottom:10px;"><iframe id="templframe" frameborder="0" src="{BASE_PATH}admin/templatedefinitions.php?dataId={DAT_ID}&amp;type=servicetemplate" width="445" height="120" style="border:1px solid #000000"></iframe></td>
                        </tr>
                        <tr>
                            <td>{LANG_TEMPLATE_NAME}</td>
                            <td>
                                <select title="{LANG_TEMPLATE_NAME}" name="selTemplate" class="selectborder" tabindex="17">
                                    <!-- BEGIN template -->
                                    <option value="{DAT_TEMPLATE_ID}" class="empty_class {SPECIAL_STYLE}">{DAT_TEMPLATE}</option>
                                    <!-- END template -->
                                </select>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','templateadd','all','Info');" class="infobutton_1"></td>
                            <td colspan="3"><input type="button" name="butTemplDefinition"  value="{LANG_INSERT}" onClick="insertDefintion()" tabindex="18"></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3"><input name="subForm" type="button" id="subForm1" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE} tabindex="19">&nbsp;<input name="subAbort" type="button" id="subAbort1" onClick="abort();" value="{LANG_ABORT}" tabindex="20"><span class="required_info">* {LANG_REQUIRED}</span></td>
                            <td colspan="3"><span class="redmessage">{WARNING}</span></td>
                        </tr>
                        <tr>
                            <td colspan="6"><input name="modus" type="hidden" id="modus" value="{MODUS}">
                                <input name="hidId" type="hidden" id="hidId" value="{DAT_ID}">
                                <input name="hidLimit" type="hidden" id="hidLimit" value="{LIMIT}"></td>
                        </tr>
                    </table>
                </div>
                <div id="tab2">
                    <table border="0" cellpadding="0" class="content_formtable">
                        <tr>
                            <td colspan="6"><strong>{LANG_CHECK_SETTINGS}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="{VERSION_20_VISIBLE}">&nbsp;</td>
                            <td class="{VERSION_30_VISIBLE}">{LANG_INITIAL_STATE}</td>
                            <td class="{VERSION_30_VISIBLE}">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="o" name="chbGr2a" type="checkbox" class="checkbox" id="chbGr2a" value="o" {DAT_ISO_CHECKED} onClick="checkInitial(this.value);"></td>
                                        <td class="radio_cell_1">o</td>
                                        <td class="radio_cell_1"><input title="w" name="chbGr2b" type="checkbox" class="checkbox" id="chbGr2b" value="w" {DAT_ISW_CHECKED} onClick="checkInitial(this.value);"></td>
                                        <td class="radio_cell_1">w</td>
                                        <td class="radio_cell_1"><input title="u" name="chbGr2c" type="checkbox" class="checkbox" id="chbGr2c" value="u" {DAT_ISU_CHECKED} onClick="checkInitial(this.value);"></td>
                                        <td class="radio_cell_1">u</td>
                                        <td class="radio_cell_1"><input title="c" name="chbGr2d" type="checkbox" class="checkbox" id="chbGr2d" value="c" {DAT_ISC_CHECKED} onClick="checkInitial(this.value);"></td>
                                        <td class="radio_cell_1">c</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','initial_state','3','Info');" class="infobutton_1"></td>
                            <td>{LANG_RETRY_INTERVAL}</td>
                            <td><input title="{LANG_RETRY_INTERVAL}" name="tfNullVal1" type="text" id="tfNullVal1" value="{DAT_RETRY_INTERVAL}" class="short"><span class="shorttext">min</span></td>
                            <tD><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','retry_interval','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td class="content_tbl_row1">{LANG_MAX_CHECK_ATTEMPTS}</td>
                            <td class="content_tbl_row2"><input title="{LANG_MAX_CHECK_ATTEMPTS}" name="tfNullVal2" type="text" id="tfNullVal2" value="{DAT_MAX_CHECK_ATTEMPTS}"></td>
                            <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','max_check_attempts','all','Info');" class="infobutton_1"></td>
                            <td class="content_tbl_row1">{LANG_CHECK_INTERVAL}</td>
                            <td class="content_tbl_row2"><input title="{LANG_CHECK_INTERVAL}" name="tfNullVal3" type="text" id="tfNullVal3" value="{DAT_CHECK_INTERVAL}" class="short"><span class="shorttext">min</span></td>
                            <td class="content_tbl_row4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','check_interval','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_ACTIVE_CHECKS_ENABLED}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue6" type="radio" class="checkbox" id="radValue60" value="1" {DAT_ACE1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue6" type="radio" class="checkbox" id="radValue61" value="0" {DAT_ACE0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue6" type="radio" class="checkbox" id="radValue62" value="2" {DAT_ACE2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue6" type="radio" class="checkbox" id="radValue63" value="3" {DAT_ACE3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','active_checks_enabled','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_PASSIVE_CHECKS_ENABLED}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue7" type="radio" class="checkbox" id="radValue70" value="1" {DAT_PCE1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue7" type="radio" class="checkbox" id="radValue71" value="0" {DAT_PCE0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue7" type="radio" class="checkbox" id="radValue72" value="2" {DAT_PCE2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue7" type="radio" class="checkbox" id="radValue73" value="3" {DAT_PCE3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','passive_checks_enabled','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr class="{VERSION_20_VISIBLE}">
                            <td>{LANG_PARALLELIZE_CHECK}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue8" type="radio" class="checkbox" id="radValue80" value="0" {DAT_PAC0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue8" type="radio" class="checkbox" id="radValue81" value="1" {DAT_PAC1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue8" type="radio" class="checkbox" id="radValue82" value="2" {DAT_PAC2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                    </tr>
                                </table>
                            </td>
                            <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','parallelize_checks','2','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_CHECK_PERIOD}</td>
                            <td>
                                <select title="{LANG_CHECK_PERIOD}" name="selValue2" id="selValue2" class="selectborder">
                                    <!-- BEGIN checkperiod -->
                                    <option value="{DAT_CHECKPERIOD_ID}" class="empty_class inpmust {SPECIAL_STYLE}" {DAT_CHECKPERIOD_SEL}>{DAT_CHECKPERIOD}</option>
                                    <!-- END checkperiod -->
                                </select>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','check_period','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_FRESHNESS_TRESHOLD}</td>
                            <td><input title="{LANG_FRESHNESS_TRESHOLD}" name="tfNullVal4" type="text" id="tfNullVal4" value="{DAT_FRESHNESS_THRESHOLD}" class="short"><span class="shorttext">sec</span></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','freshness_threshold','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_CHECK_FRESHNESS}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue9" type="radio" class="checkbox" id="radValue91" value="1" {DAT_FRE1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue9" type="radio" class="checkbox" id="radValue90" value="0" {DAT_FRE0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue9" type="radio" class="checkbox" id="radValue92" value="2" {DAT_FRE2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue9" type="radio" class="checkbox" id="radValue93" value="3" {DAT_FRE3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','check_freshness','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_OBSESS_OVER_SERVICE}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue10" type="radio" class="checkbox" id="radValue101" value="1" {DAT_OBS1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue10" type="radio" class="checkbox" id="radValue100" value="0" {DAT_OBS0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue10" type="radio" class="checkbox" id="radValue102" value="2" {DAT_OBS2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue10" type="radio" class="checkbox" id="radValue103" value="3" {DAT_OBS3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','obsess_over_service','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>{LANG_EVENT_HANDLER}</td>
                            <td>
                                <select title="{LANG_EVENT_HANDLER}" name="selValue3" id="selValue3" class="selectborder">
                                    <!-- BEGIN eventhandler -->
                                    <option value="{DAT_EVENTHANDLER_ID}" class="empty_class inpmust {SPECIAL_STYLE}" {DAT_EVENTHANDLER_SEL}>{DAT_EVENTHANDLER}</option>
                                    <!-- END eventhandler -->
                                </select>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','event_handler','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_EVENT_HANDLER_ENABLED}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue11" type="radio" class="checkbox" id="radValue111" value="1" {DAT_EVH1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue11" type="radio" class="checkbox" id="radValue110" value="0" {DAT_EVH0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue11" type="radio" class="checkbox" id="radValue112" value="2" {DAT_EVH2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue11" type="radio" class="checkbox" id="radValue113" value="3" {DAT_EVH3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','event_handler_enabled','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>{LANG_LOW_FLAP_THRESHOLD}</td>
                            <td><input title="{LANG_LOW_FLAP_THRESHOLD}" name="tfNullVal5" type="text" id="tfNullVal5" value="{DAT_LOW_FLAP_THRESHOLD}" class="short"><span class="shorttext">%</span></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','low_flap_threshold','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_HIGH_FLAP_THRESHOLD}</td>
                            <td><input title="{LANG_HIGH_FLAP_THRESHOLD}" name="tfNullVal6" type="text" id="tfNullVal6" value="{DAT_HIGH_FLAP_THRESHOLD}" class="short"><span class="shorttext">%</span></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','high_flap_threshold','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_FLAP_DETECTION_ENABLED}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue12" type="radio" class="checkbox" id="radValue121" value="1" {DAT_FLE1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue12" type="radio" class="checkbox" id="radValue120" value="0" {DAT_FLE0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue12" type="radio" class="checkbox" id="radValue122" value="2" {DAT_FLE2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue12" type="radio" class="checkbox" id="radValue123" value="3" {DAT_FLE3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','flap_detection_enabled','all','Info');" class="infobutton_1"></td>
                            <td class="{VERSION_30_VISIBLE}">{LANG_FLAP_DETECTION_OPTIONS}</td>
                            <td class="{VERSION_30_VISIBLE}">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="o" name="chbGr3a" type="checkbox" class="checkbox" id="chbGr3a" value="o" {DAT_FLO_CHECKED}></td>
                                        <td class="radio_cell_1">o</td>
                                        <td class="radio_cell_1"><input title="w" name="chbGr3b" type="checkbox" class="checkbox" id="chbGr3b" value="w" {DAT_FLW_CHECKED}></td>
                                        <td class="radio_cell_1">w</td>
                                        <td class="radio_cell_1"><input title="u" name="chbGr3c" type="checkbox" class="checkbox" id="chbGr3c" value="u" {DAT_FLU_CHECKED}></td>
                                        <td class="radio_cell_1">u</td>
                                        <td class="radio_cell_1"><input title="c" name="chbGr3d" type="checkbox" class="checkbox" id="chbGr3d" value="c" {DAT_FLC_CHECKED}></td>
                                        <td class="radio_cell_1">c</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','flap_detection_options','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>{LANG_RETAIN_STATUS_INFORMATION}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue13" type="radio" class="checkbox" id="radValue131" value="1" {DAT_STI1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue13" type="radio" class="checkbox" id="radValue130" value="0" {DAT_STI0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue13" type="radio" class="checkbox" id="radValue132" value="2" {DAT_STI2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue13" type="radio" class="checkbox3" id="radValue13" value="3" {DAT_STI3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','retain_status_information','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_RETAIN_NOSTATUS_INFORMATION}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue14" type="radio" class="checkbox" id="radValue141" value="1" {DAT_NSI1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue14" type="radio" class="checkbox" id="radValue140" value="0" {DAT_NSI0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue14" type="radio" class="checkbox" id="radValue142" value="2" {DAT_NSI2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue14" type="radio" class="checkbox" id="radValue143" value="3" {DAT_NSI3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','retain_nonstatus_information','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_PROCESS_PERF_DATA}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue15" type="radio" class="checkbox" id="radValue151" value="1" {DAT_PED1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue15" type="radio" class="checkbox" id="radValue150" value="0" {DAT_PED0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue15" type="radio" class="checkbox" id="radValue152" value="2" {DAT_PED2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue15" type="radio" class="checkbox" id="radValue153" value="3" {DAT_PED3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','process_perf_data','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_IS_VOLATILE}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue16" type="radio" class="checkbox" id="radValue161" value="1" {DAT_ISV1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue16" type="radio" class="checkbox" id="radValue160" value="0" {DAT_ISV0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue16" type="radio" class="checkbox" id="radValue162" value="2" {DAT_ISV2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue16" type="radio" class="checkbox" id="radValue163" value="3" {DAT_ISV3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','is_volatile','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3"><input name="subForm" type="button" id="subForm2" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE}>&nbsp;<input name="subAbort" type="button" id="subAbort2" onClick="abort();" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span></td>
                            <td colspan="3"><span class="redmessage">{WARNING}</span></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                    </table>
                </div>
                <div id="tab3">
                    <table border="0" cellpadding="0" class="content_formtable">
                        <tr>
                            <td colspan="6"><strong>{LANG_ALARM_SETTINGS}</strong></td>
                        </tr>
                        <tr>
                            <td valign="top">{LANG_CONTACT_GROUPS}<br><br><small>{LANG_CTRLINFO}</small></td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td>
                                            <select title="{LANG_CONTACT_GROUPS}" name="mselValue5[]" size="4" multiple id="mselValue5" class="selectborder" {MSIE_DISABLED}>
                                                <!-- BEGIN service_contactgroups -->
                                                <option value="{DAT_SERVICE_CONTACTGROUPS_ID}" class="empty_class {SPECIAL_STYLE} {IE_SERVICE_CONTACTGROUPS_SEL}" {DAT_SERVICE_CONTACTGROUPS_SEL} {OPTION_DISABLED}>{DAT_SERVICE_CONTACTGROUPS}</option>
                                                <!-- END service_contactgroups -->
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top"><img id="mutvalue5" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','contactgroups','all','Info');" class="infobutton_2"></td>
                            <td valign="top"><span class="{VERSION_30_VISIBLE}">{LANG_CONTACTS}<br><br><small>{LANG_CTRLINFO}</small></span></td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr class="{VERSION_30_VISIBLE}">
                                        <td>
                                            <select title="{LANG_CONTACTS}" name="mselValue4[]" size="4" multiple id="mselValue4" class="selectborder" {MSIE_DISABLED}>
                                                <!-- BEGIN service_contacts -->
                                                <option value="{DAT_SERVICE_CONTACTS_ID}" class="empty_class {SPECIAL_STYLE} {IE_SERVICE_CONTACTS_SEL}" {DAT_SERVICE_CONTACTS_SEL} {OPTION_DISABLED}>{DAT_SERVICE_CONTACTS}</option>
                                                <!-- END service_contacts -->
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top"><span class="{VERSION_30_VISIBLE}"><img id="mutvalue4" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','contacts','3','Info');" class="infobutton_2"></span></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>&nbsp;</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="+" name="radValue5" type="radio" class="checkbox" id="radValue50" value="0" {DAT_COG0_CHECKED}></td>
                                        <td class="radio_cell_2">+</td>
                                        <td class="radio_cell_1"><input title="null" name="radValue5" type="radio" class="checkbox" id="radValue51" value="1" {DAT_COG1_CHECKED}></td>
                                        <td class="radio_cell_2">null</td>
                                        <td class="radio_cell_1"><input title="{LANG_STANDARD}" name="radValue5" type="radio" class="checkbox" id="radValue52" value="2" {DAT_COG2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_STANDARD}</td>
                                    </tr>
                                </table>
                            </td>
                            <td colspan="2"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','tploptions','3','Info');" class="infobutton_1"></td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="+" name="radValue4" type="radio" class="checkbox" id="radValue40" value="0" {DAT_COT0_CHECKED}></td>
                                        <td class="radio_cell_2">+</td>
                                        <td class="radio_cell_1"><input title="null" name="radValue4" type="radio" class="checkbox" id="radValue41" value="1" {DAT_COT1_CHECKED}></td>
                                        <td class="radio_cell_2">null</td>
                                        <td class="radio_cell_1"><input title="{LANG_STANDARD}" name="radValue4" type="radio" class="checkbox" id="radValue42" value="2" {DAT_COT2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_STANDARD}</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','tploptions','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="content_tbl_row1">{LANG_NOTIFICATION_PERIOD} *</td>
                            <td class="content_tbl_row2">
                                <select title="{LANG_NOTIFICATION_PERIOD}" name="selValue4" id="selValue4" class="selectborder">
                                    <!-- BEGIN notifyperiod -->
                                    <option value="{DAT_NOTIFYPERIOD_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_NOTIFYPERIOD_SEL}>{DAT_NOTIFYPERIOD}</option>
                                    <!-- END notifyperiod -->
                                </select>
                            </td>
                            <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','notification_period','all','Info');" class="infobutton_1"></td>
                            <td class="content_tbl_row1">{LANG_NOTIFICATION_OPTIONS}</td>
                            <td class="content_tbl_row2">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="w" name="chbGr1a" type="checkbox" class=" checkbox" id="chbGr1a" value="w" {DAT_NOW_CHECKED}></td>
                                        <td class="radio_cell_1">w</td>
                                        <td class="radio_cell_1"><input title="u" name="chbGr1b" type="checkbox" class=" checkbox" id="chbGr1b" value="u" {DAT_NOU_CHECKED}></td>
                                        <td class="radio_cell_1">u</td>
                                        <td class="radio_cell_1"><input title="c" name="chbGr1c" type="checkbox" class=" checkbox" id="chbGr1c" value="c" {DAT_NOC_CHECKED}></td>
                                        <td class="radio_cell_1">c</td>
                                        <td class="radio_cell_1"><input title="r" name="chbGr1d" type="checkbox" class=" checkbox" id="chbGr1d" value="r" {DAT_NOR_CHECKED}></td>
                                        <td class="radio_cell_1">r</td>
                                        <td class="radio_cell_1"><input title="f" name="chbGr1e" type="checkbox" class=" checkbox" id="chbGr1e" value="f" {DAT_NOF_CHECKED}></td>
                                        <td class="radio_cell_1">f</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="s" name="chbGr1f" type="checkbox" class=" checkbox" id="chbGr1f" value="s" {DAT_NOS_CHECKED}></td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}">s</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="content_tbl_row4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','notification_options','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_NOTIFICATION_INTERVAL}</td>
                            <td><input title="{LANG_NOTIFICATION_INTERVAL}" name="tfNullVal7" type="text" id="tfNullVal7" value="{DAT_NOTIFICATION_INTERVAL}"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','notification_intervall','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_FIRST_NOTIFICATION_DELAY}</td>
                            <td><input title="{LANG_FIRST_NOTIFICATION_DELAY}" name="tfNullVal8" type="text" id="tfNullVal8" value="{DAT_FIRST_NOTIFICATION_DELAY}"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','first_notification_delay','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_NOTIFICATION_ENABLED}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue17" type="radio" class="checkbox" id="radValue171" value="1" {DAT_NOE1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue17" type="radio" class="checkbox" id="radValue170" value="0" {DAT_NOE0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue17" type="radio" class="checkbox" id="radValue172" value="2" {DAT_NOE2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue17" type="radio" class="checkbox" id="radValue173" value="3" {DAT_NOE3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','notification_enabled','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_STALKING_OPTIONS}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="o" name="chbGr4a" type="checkbox" class=" checkbox" id="chbGr4a" value="o" {DAT_STO_CHECKED}></td>
                                        <td class="radio_cell_1">o</td>
                                        <td class="radio_cell_1"><input title="w" name="chbGr4b" type="checkbox" class=" checkbox" id="chbGr4b" value="w" {DAT_STW_CHECKED}></td>
                                        <td class="radio_cell_1">w</td>
                                        <td class="radio_cell_1"><input title="u" name="chbGr4c" type="checkbox" class=" checkbox" id="chbGr4c" value="u" {DAT_STU_CHECKED}></td>
                                        <td class="radio_cell_1">u</td>
                                        <td class="radio_cell_1"><input title="c" name="chbGr4d" type="checkbox" class=" checkbox" id="chbGr4d" value="c" {DAT_STC_CHECKED}></td>
                                        <td class="radio_cell_1">c</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','stalking_options','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr class="{VERSION_40_VISIBLE}">
                            <td>{LANG_IMPORTANCE}</td>
                            <td><input title="{LANG_IMPORTANCE}" name="tfNullVal9" type="text" id="tfNullVal9" value="{DAT_IMPORTANCE}"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','importance','all','Info');" class="infobutton_1"></td>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3"><input name="subForm" type="button" id="subForm3" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE}>&nbsp;<input name="subAbort" type="button" id="subAbort3" onClick="abort();" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span></td>
                            <td colspan="3"><span class="redmessage">{WARNING}</span></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                    </table>
                </div>
                <div id="tab4">
                    <table border="0" cellpadding="0" class="content_formtable">
                        <tr>
                            <td colspan="6"><strong>{LANG_ADDON_SETTINGS}</strong></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td class="content_tbl_row1">{LANG_NOTES}</td>
                            <td class="content_tbl_row2"><input title="{LANG_NOTES}" name="tfValue4" type="text" id="tfValue4" value="{DAT_NOTES}" tabindex="1"></td>
                            <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','notes','3','Info');" class="infobutton_1"></td>
                            <td class="content_tbl_row1">{LANG_ICON_IMAGE}</td>
                            <td class="content_tbl_row2"><input title="{LANG_ICON_IMAGE}" name="tfValue7" type="text" id="tfValue7" value="{DAT_ICON_IMAGE}" tabindex="4"></td>
                            <td class="content_tbl_row4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','icon_image','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>{LANG_NOTES_URL}</td>
                            <td><input title="{LANG_NOTES_URL}" name="tfValue5" type="text" id="tfValue5" value="{DAT_NOTES_URL}" tabindex="2"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','notes_url','3','Info');" class="infobutton_1"></td>
                            <td>{LANG_ICON_IMAGE_ALT_TEXT}</td>
                            <td><input title="{LANG_ICON_IMAGE_ALT_TEXT}" name="tfValue8" type="text" id="tfValue8" value="{DAT_ICON_IMAGE_ALT}" tabindex="5"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','icon_image_alt_text','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>{LANG_ACTION_URL}</td>
                            <td><input title="{LANG_ACTION_URL}" name="tfValue6" type="text" id="tfValue6" value="{DAT_ACTION_URL}" tabindex="3"></td>
                            <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('service','action_url','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr {RESTRICT_GROUP_ADMIN}>
                            <td class="content_tbl_row1">{LANG_ACCESS_GROUP}</td>
                            <td class="content_tbl_row2">
                                <select title="{RESTRICT_GROUP_ADMIN}" name="selAccGr" class="selectborder" tabindex="6">
                                    <!-- BEGIN acc_group -->
                                    <option value="{DAT_ACC_GROUP_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_ACC_GROUP_SEL}>{DAT_ACC_GROUP}</option>
                                    <!-- END acc_group -->
                                </select>
                            </td>
                            <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','accessgroup','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td colspan="6" style="padding-bottom:5px"><b>{LANG_FREE_VARIABLE_DEFINITIONS}</b></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td colspan="6" style="padding-bottom:2px">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="width:235px;padding-left:5px;"><i>{LANG_VARIABLE_NAME}</i></td>
                                        <td style="width:260px;padding-left:5px;"><i>{LANG_VARIABLE_VALUE}</i></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td colspan="6" style="padding-bottom:10px"><iframe id="variableframe" frameborder="0" src="{BASE_PATH}admin/variabledefinitions.php?dataId={DAT_ID}&amp;linktab=tbl_lnkServicetemplateToVariabledefinition" style="width:540px;height:150px;border:1px solid #000000"></iframe></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>{LANG_VARIABLE_NAME}</td>
                            <td><input title="{LANG_VARIABLE_NAME}" type="text" name="txtVariablename" id="txtVariablename" tabindex="7"></td>
                            <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','free_variables_name','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>{LANG_VARIABLE_VALUE}</td>
                            <td><input title="{LANG_VARIABLE_VALUE}" type="text" name="txtVariablevalue" id="txtVariablevalue" tabindex="8"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','free_variables_value','all','Info');" class="infobutton_1"></td>
                            <td colspan="3"><input type="button" name="butVariableDefinition" value="{LANG_INSERT}" onClick="insertDefintionVar()" tabindex="9"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3"><input name="subForm" type="button" id="subForm4" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE} tabindex="10">&nbsp;<input name="subAbort" type="button" id="subAbort4" onClick="abort();" value="{LANG_ABORT}" tabindex="11"><span class="required_info">* {LANG_REQUIRED}</span></td>
                            <td colspan="3"><span class="redmessage">{WARNING}</span></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </form>
    <!--suppress JSUnusedLocalSymbols -->
    <script type="text/javascript" language="javascript">
        <!--
        (function() {
            const tabView = new YAHOO.widget.TabView('servicetemplate');
        })();
        //-->
    </script>
    {CHECK_MUST_DATA}
    <br>
</div>
<div id="mutdialogvalue1">
    <div id="mutdialogvalue1content" class="bd"></div>
</div>
<div id="mutdialogvalue2">
    <div id="mutdialogvalue2content" class="bd"></div>
</div>
<div id="mutdialogvalue3">
    <div id="mutdialogvalue3content" class="bd"></div>
</div>
<div id="mutdialogvalue4">
    <div id="mutdialogvalue4content" class="bd"></div>
</div>
<div id="mutdialogvalue5">
    <div id="mutdialogvalue5content" class="bd"></div>
</div>
<div id="mutdialogvalue6">
    <div id="mutdialogvalue6content" class="bd"></div>
</div>
<div id="msgcontainer"></div>
<div id="confirmcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->