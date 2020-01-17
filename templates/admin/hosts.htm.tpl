<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : host template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datainsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <!--suppress JSUnresolvedVariable, JSUnusedLocalSymbols -->
    <script type="text/JavaScript">
        <!--
        // Initialize change dialog
        openMutDlgInit("mselValue1","mutdialogvalue1","{LANG_MODIFY_SELECTION}: {LANG_PARENTS}","mutvalue1","{LANG_SAVE}","{LANG_ABORT}","1");
        openMutDlgInit("mselValue2","mutdialogvalue2","{LANG_MODIFY_SELECTION}: {LANG_HOST_GROUPS}","mutvalue2","{LANG_SAVE}","{LANG_ABORT}","1");
        openMutDlgInit("mselValue3","mutdialogvalue3","{LANG_MODIFY_SELECTION}: {LANG_CONTACTS}","mutvalue3","{LANG_SAVE}","{LANG_ABORT}");
        openMutDlgInit("mselValue4","mutdialogvalue4","{LANG_MODIFY_SELECTION}: {LANG_CONTACT_GROUPS}","mutvalue4","{LANG_SAVE}","{LANG_ABORT}");
        openMutDlgInit("mselValue5","mutdialogvalue5","{LANG_MODIFY_SELECTION}: {LANG_SERVICES}","mutvalue5","{LANG_SAVE}","{LANG_ABORT}");
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
                const selfields = "mselValue1,mselValue2,mselValue3,mselValue4,mselValue5";
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
            const fields2 = "tfValue3,tfValue5,tfNullVal2,selValue2,selValue4";
            const fields3 = "chbGr1a,chbGr1b,chbGr1c,chbGr1d,chbGr1e";
            const fields4 = "tfNullVal7";
            const bypass  = "{CHECK_BYPASS_NEW}";
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
            // Check sum of required arguments
            for (let i=1;i<=argcount;i++) {
                if (document.getElementById("tfArg"+i).value === "") {
                    confirminit("{FILL_ARGUMENTS}","{LANG_SECURE_QUESTION}",2,"{LANG_YES}","{LANG_NO}",2);
                    return false;
                }
            }
            // Check for illegal chars
            if (form.tfValue1.value.match(/[^a-zA-Z0-9.@_-]/)) {
                msginit(msg2+" {LANG_HOST_NAME}",header,1);
                form.tfValue1.focus();
                return false;
            }	
            if (form.tfValue6.value.match(/[^a-zA-Z0-9.@_-]/)) {
                msginit(msg2+" {LANG_GENERIC_NAME}",header,1);
                form.tfValue1.focus();
                return false;
            }
            if (bypass === '0') {
                check = checkfields(fields4, form, myFocusObject);
                if (check === false) {
                    confirminit("{LANG_MUST_BUT_TEMPLATE}", "{LANG_SECURE_QUESTION}", 2, "{LANG_YES}", "{LANG_NO}", 2);
                    return false
                }
                check = checkfields2(fields2, form, myFocusObject);
                if (check === false) {
                    confirminit("{LANG_MUST_BUT_TEMPLATE}", "{LANG_SECURE_QUESTION}", 2, "{LANG_YES}", "{LANG_NO}", 2);
                    return false
                }
                if ((form.mselValue3.value === "") && (form.mselValue4.value === "")) {
                    confirminit("{LANG_MUST_BUT_TEMPLATE}", "{LANG_SECURE_QUESTION}", 2, "{LANG_YES}", "{LANG_NO}", 2);
                    return false
                }
                if (version !== "3") {
                    check = checkboxes(fields3, form);
                    if (check === false) {
                        confirminit("{LANG_MUST_BUT_TEMPLATE}", "{LANG_SECURE_QUESTION}", 2, "{LANG_YES}", "{LANG_NO}", 2);
                        return false
                    }
                }
            }
        }
        // Insert template definition
        function insertDefintion() {
            const txtDef = document.frmDetail.selTemplate.value;
            document.getElementById("templframe").src = "{BASE_PATH}admin/templatedefinitions.php?dataId={DAT_ID}&type=host&mode=add&def="+txtDef;
        }
        // Insert free variable definition
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
                // Enable select fields
                const selfields = "mselValue1,mselValue2,mselValue3,mselValue4,mselValue5";
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
        // Check initial state
        function checkInitial(key) {
            const form = document.frmDetail;
            if (key === "o") {
                form.chbGr2b.checked = false;
                form.chbGr2c.checked = false;
            }
            if (key === "d") {
                form.chbGr2a.checked = false;
                form.chbGr2c.checked = false;
            }
            if (key === "u") {
                form.chbGr2a.checked = false;
                form.chbGr2b.checked = false;
            }
        }
        // Get ID from command selection
        function getCommandValue() {
            let element = document.getElementById('selValue1');
            return element.options[element.selectedIndex].value;
        }
        //-->
    </script>
    <form name="frmDetail" method="post" action="{ACTION_INSERT}">  
        <div id="host" style="width:909px;" class="yui-navset">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{LANG_COMMON_SETTINGS}</em></a></li>
                <li><a href="#tab2"><em>{LANG_CHECK_SETTINGS}</em></a></li>
                <li><a href="#tab3"><em>{LANG_ALARM_SETTINGS}</em></a></li>
                <li><a href="#tab4"><em>{LANG_ADDON_SETTINGS}</em></a></li>
                <li><a href="#tab5"><em>{LANG_SERVICE_SETTINGS}</em></a></li>
            </ul>            
            <div class="yui-content">
                <div id="tab1">
                    <table border="0" cellpadding="0" class="content_formtable">
                        <tr>
                            <td colspan="6"><strong>{LANG_COMMON_SETTINGS}</strong></td>
                        </tr>
                        <tr>
                            <td class="content_tbl_row1">{LANG_HOST_NAME} *</td>
                            <td class="content_tbl_row2"><input title="{LANG_HOST_NAME}" name="tfValue1" type="text" id="tfValue1" value="{DAT_HOST_NAME}" class="inpmust" tabindex="1"><input type="hidden" name="tfValue2" id="tfValue2" value="{DAT_HOST_NAME}"></td>
                            <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','hostname','all','Info');" class="infobutton_1"></td>
                            <td class="content_tbl_row1">{LANG_DESCRIPTION} *</td>
                            <td class="content_tbl_row2"><input title="{LANG_DESCRIPTION}" name="tfValue3" type="text" id="tfValue3" value="{DAT_ALIAS}" class="inpmust" tabindex="2"></td>
                            <td class="content_tbl_row4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','alias','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_ADDRESS} *</td>
                            <td><input title="{LANG_ADDRESS}" name="tfValue5" type="text" id="tfValue5" value="{DAT_ADDRESS}" class="inpmust" tabindex="3"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','address','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_DISPLAY_NAME}</td>
                            <td><input title="{LANG_DISPLAY_NAME}" name="tfValue4" type="text" id="tfValue4" value="{DAT_DISPLAY_NAME}" tabindex="4"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','display_name','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td valign="top"><span class="{PARENTS_VISIBLE}">{LANG_PARENTS}<br><br><small>{LANG_CTRLINFO}</small></span></td>
                            <td valign="top">
                                <table cellpadding="0" cellspacing="0" border="0" class="{PARENTS_VISIBLE}">
                                    <tr>
                                        <td>
                                            <select title="{LANG_PARENTS}" name="mselValue1[]" size="4" multiple id="mselValue1" class="selectborder" {MSIE_DISABLED}>
<!-- BEGIN host_parents -->
                                                <option value="{DAT_HOST_PARENTS_ID}" class="empty_class {SPECIAL_STYLE} {IE_HOST_PARENTS_SEL}" {DAT_HOST_PARENTS_SEL} {OPTION_DISABLED}>{DAT_HOST_PARENTS}</option>
<!-- END host_parents -->
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top"><span class="{PARENTS_VISIBLE}"><img id="mutvalue1" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onClick="dialoginit('host','parents','all','Info');" class="infobutton_2"></span></td>
                            <td valign="top">{LANG_HOST_GROUPS}<br><br><small>{LANG_CTRLINFO}</small></td>
                            <td valign="top">
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
                            <td valign="top"><img id="mutvalue2" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onClick="dialoginit('host','hostgroups','all','Info');" class="infobutton_2"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>&nbsp;</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0" class="{PARENTS_VISIBLE}">
                                    <tr>
                                        <td class="radio_cell_1"><input title="+" name="radValue1" type="radio" class="checkbox" id="radValue10" value="0" {DAT_PAR0_CHECKED} tabindex="5"></td>
                                        <td class="radio_cell_2">+</td>
                                        <td class="radio_cell_1"><input title="null" name="radValue1" type="radio" class="checkbox" id="radValue11" value="1" {DAT_PAR1_CHECKED} tabindex="5"></td>
                                        <td class="radio_cell_2">null</td>
                                        <td class="radio_cell_1"><input title="{LANG_STANDARD}" name="radValue1" type="radio" class="checkbox" id="radValue12" value="2" {DAT_PAR2_CHECKED} tabindex="5"></td>
                                        <td class="radio_cell_2">{LANG_STANDARD}</td>
                                    </tr>
                                </table>
                            </td>
                            <td><span class="{PARENTS_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','tploptions','3','Info');" class="infobutton_1"></span></td>
                            <td>&nbsp;</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0" class="template_tab">
                                    <tr>
                                        <td class="radio_cell_1"><input title="+" name="radValue2" type="radio" class="checkbox" id="radValue20" value="0" {DAT_HOG0_CHECKED} tabindex="6"></td>
                                        <td class="radio_cell_2">+</td>
                                        <td class="radio_cell_1"><input title="null" name="radValue2" type="radio" class="checkbox" id="radValue21" value="1" {DAT_HOG1_CHECKED} tabindex="6"></td>
                                        <td class="radio_cell_2">null</td>
                                        <td class="radio_cell_1"><input title="{LANG_STANDARD}" name="radValue2" type="radio" class="checkbox" id="radValue22" value="2" {DAT_HOG2_CHECKED} tabindex="6"></td>
                                        <td class="radio_cell_2">{LANG_STANDARD}</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','tploptions','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_CHECK_COMMAND}</td>
                            <td>
                                <select title="{LANG_CHECK_COMMAND}" name="selValue1" id="selValue1" class="selectborder" onChange="setIframe(this.value);" tabindex="7">
<!-- BEGIN hostcommand -->
                                    <option value="{DAT_HOSTCOMMAND_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_HOSTCOMMAND_SEL}>{DAT_HOSTCOMMAND}</option>
<!-- END hostcommand -->
                                </select>
                            </td>
                            <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','check_command','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td valign="top" style="padding-top:3px;">{LANG_COMMAND_VIEW}</td>
                            <td valign="top" colspan="5"><iframe scrolling="no" id="fullcommand" name="fullcommand" src="{IFRAME_SRC}" width="100%" height="36"></iframe></td>
                        </tr>
                        <tr>
                            <td>$ARG1$&nbsp;<img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('cmd_arguments',getCommandValue(),'1','Argument Info');" class="infobutton_1"  style="vertical-align: middle;margin-bottom: 2px;"></td>
                            <td><input title="$ARG1$" name="tfArg1" type="text" id="tfArg1" value="{DAT_ARG1}" tabindex="8")"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','arguments','all','Info');" class="infobutton_1"></td>
                            <td>$ARG5$&nbsp;<img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('cmd_arguments',getCommandValue(),'5','Argument Info');" class="infobutton_1"  style="vertical-align: middle;margin-bottom: 2px;"></td>
                            <td colspan="2"><input title="$ARG5$" name="tfArg5" type="text" id="tfArg5" value="{DAT_ARG5}" tabindex="12"></td>
                        </tr>
                        <tr>
                            <td>$ARG2$&nbsp;<img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('cmd_arguments',getCommandValue(),'2','Argument Info');" class="infobutton_1"  style="vertical-align: middle;margin-bottom: 2px;"></td>
                            <td colspan="2"><input title="$ARG2$" name="tfArg2" type="text" id="tfArg2" value="{DAT_ARG2}" tabindex="9"></td>
                            <td>$ARG6$&nbsp;<img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('cmd_arguments',getCommandValue(),'6','Argument Info');" class="infobutton_1"  style="vertical-align: middle;margin-bottom: 2px;"></td>
                            <td colspan="2"><input title="$ARG6$" name="tfArg6" type="text" id="tfArg6" value="{DAT_ARG6}" tabindex="13"></td>
                        </tr>
                        <tr>
                            <td>$ARG3$&nbsp;<img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('cmd_arguments',getCommandValue(),'3','Argument Info');" class="infobutton_1"  style="vertical-align: middle;margin-bottom: 2px;"></td>
                            <td colspan="2"><input title="$ARG3$" name="tfArg3" type="text" id="tfArg3" value="{DAT_ARG3}" tabindex="10"></td>
                            <td>$ARG7$&nbsp;<img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('cmd_arguments',getCommandValue(),'7','Argument Info');" class="infobutton_1"  style="vertical-align: middle;margin-bottom: 2px;"></td>
                            <td colspan="2"><input title="$ARG7$" name="tfArg7" type="text" id="tfArg7" value="{DAT_ARG7}" tabindex="14"></td>
                        </tr>
                        <tr>
                            <td>$ARG4$&nbsp;<img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('cmd_arguments',getCommandValue(),'4','Argument Info');" class="infobutton_1"  style="vertical-align: middle;margin-bottom: 2px;"></td>
                            <td colspan="2"><input title="$ARG4$" name="tfArg4" type="text" id="tfArg4" value="{DAT_ARG4}" tabindex="11"></td>
                            <td>$ARG8$&nbsp;<img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('cmd_arguments',getCommandValue(),'8','Argument Info');" class="infobutton_1"  style="vertical-align: middle;margin-bottom: 2px;"></td>
                            <td colspan="2"><input title="$ARG8$" name="tfArg8" type="text" id="tfArg8" value="{DAT_ARG8}" tabindex="15"></td>
                        </tr>
                        <tr>
                          <td colspan="3">&nbsp;</td>
                          <td>{LANG_REGISTERED}</td>
                          <td><input title="{LANG_REGISTERED}" name="chbRegister" type="checkbox" class="checkbox" id="chbRegister" value="1" {REG_CHECKED} tabindex="16"></td>
                          <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','registered','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding-bottom:5px;"><strong>{LANG_ADDITIONAL_TEMPLATES}</strong></td>
                            <td>{LANG_ACTIVE}</td>
                            <td colspan="2"><input title="{LANG_ACTIVE}" name="chbActive" type="checkbox" class="checkbox" id="chbActive" value="1" {ACT_CHECKED} {ACT_DISABLED} tabindex="17">
                                <input name="hidActive" type="hidden" id="hidActive" value="{ACTIVE}"></td>
                        </tr>
                        <tr>
                            <td style="padding-bottom:2px;padding-left:5px"><i>{LANG_TEMPLATE_NAME}</i></td>
                            <td colspan="5" style="padding-bottom:2px;padding-left:5px"></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="padding-bottom:10px;"><iframe name="templframe" id="templframe" frameborder="0" src="{BASE_PATH}admin/templatedefinitions.php?dataId={DAT_ID}&amp;type=host" style="border:1px solid #000000; width:445px; height:120px;"></iframe></td>
                        </tr>
                        <tr>
                            <td>{LANG_TEMPLATE_NAME}</td>
                            <td>
                                <select title="{LANG_TEMPLATE_NAME}" name="selTemplate" class="selectborder" tabindex="18">
<!-- BEGIN template -->
                                    <option value="{DAT_TEMPLATE_ID}" class="empty_class {SPECIAL_STYLE}">{DAT_TEMPLATE}</option>
<!-- END template -->
                                </select>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','templateadd','all','Info');" class="content_infobutton_1"></td>
                            <td colspan="3"><input type="button" name="butTemplDefinition"  value="{LANG_INSERT}" onClick="insertDefintion();" tabindex="19"></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3"><input name="subForm" type="button" id="subForm1" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE} tabindex="20">&nbsp;<input name="subAbort" type="button" id="subAbort1" onClick="abort();" value="{LANG_ABORT}" tabindex="21"><span class="required_info">* {LANG_REQUIRED}</span></td>
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
                                        <td class="radio_cell_1"><input title="d" name="chbGr2b" type="checkbox" class="checkbox" id="chbGr2b" value="d" {DAT_ISD_CHECKED} onClick="checkInitial(this.value);"></td>
                                        <td class="radio_cell_1">d</td>
                                        <td class="radio_cell_1"><input title="u" name="chbGr2c" type="checkbox" class="checkbox" id="chbGr2c" value="u" {DAT_ISU_CHECKED} onClick="checkInitial(this.value);"></td>
                                        <td class="radio_cell_1">u</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','initial_state','3','Info');" class="infobutton_1"></td>    
                            <td>{LANG_RETRY_INTERVAL}</td>
                            <td><input title="{LANG_RETRY_INTERVAL}" name="tfNullVal1" type="text" id="tfNullVal1" value="{DAT_RETRY_INTERVAL}" class="short"><span class="shorttext">min</span></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','retry_interval','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td class="content_tbl_row1">{LANG_MAX_CHECK_ATTEMPTS} *</td>
                            <td class="content_tbl_row2"><input title="{LANG_MAX_CHECK_ATTEMPTS}" name="tfNullVal2" type="text" id="tfNullVal2" value="{DAT_MAX_CHECK_ATTEMPTS}" class="inpmust"></td>
                            <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','max_check_attempts','all','Info');"  class="infobutton_1"></td>
                            <td class="content_tbl_row1">{LANG_CHECK_INTERVAL}</td>
                            <td class="content_tbl_row2"><input title="{LANG_CHECK_INTERVAL}" name="tfNullVal3" type="text" id="tfNullVal3" value="{DAT_CHECK_INTERVAL}" class="short"><span class="shorttext">min</span></td>
                            <td class="content_tbl_row4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','check_interval','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_ACTIVE_CHECKS_ENABLED}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue5" type="radio" class="checkbox" id="radValue50" value="1" {DAT_ACE1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue5" type="radio" class="checkbox" id="radValue51" value="0" {DAT_ACE0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue5" type="radio" class="checkbox" id="radValue52" value="2" {DAT_ACE2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue5" type="radio" class="checkbox" id="radValue53" value="3" {DAT_ACE3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','active_checks_enabled','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_PASSIVE_CHECKS_ENABLED}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue6" type="radio" class="checkbox" id="radValue60" value="1" {DAT_PCE1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue6" type="radio" class="checkbox" id="radValue61" value="0" {DAT_PCE0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue6" type="radio" class="checkbox" id="radValue62" value="2" {DAT_PCE2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue6" type="radio" class="checkbox" id="radValue63" value="3" {DAT_PCE3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','passive_checks_enabled','all','Info');" class="infobutton_1"></td>
                        </tr> 
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>{LANG_CHECK_PERIOD} *</td>
                            <td>
                                <select title="{LANG_CHECK_PERIOD}" name="selValue2" id="selValue2" class="selectbordermust inpmust">
<!-- BEGIN checkperiod -->
                                    <option value="{DAT_CHECKPERIOD_ID}" class="empty_class inpmust {SPECIAL_STYLE}" {DAT_CHECKPERIOD_SEL}>{DAT_CHECKPERIOD}</option>
<!-- END checkperiod -->
                                </select>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','check_period','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_FRESHNESS_TRESHOLD}</td>
                            <td><input title="{LANG_FRESHNESS_TRESHOLD}" name="tfNullVal4" type="text" id="tfNullVal4" value="{DAT_FRESHNESS_THRESHOLD}" class="short"><span class="shorttext">sec</span></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','freshness_threshold','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_CHECK_FRESHNESS}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue7" type="radio" class="checkbox" id="radValue71" value="1" {DAT_FRE1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue7" type="radio" class="checkbox" id="radValue70" value="0" {DAT_FRE0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue7" type="radio" class="checkbox" id="radValue72" value="2" {DAT_FRE2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue7" type="radio" class="checkbox" id="radValue73" value="3" {DAT_FRE3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','check_freshness','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_OBSESS_OVER_HOST}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue8" type="radio" class="checkbox" id="radValue81" value="1" {DAT_OBS1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue8" type="radio" class="checkbox" id="radValue80" value="0" {DAT_OBS0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue8" type="radio" class="checkbox" id="radValue82" value="2" {DAT_OBS2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue8" type="radio" class="checkbox" id="radValue83" value="3" {DAT_OBS3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>      
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','obsess_over_host','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>{LANG_EVENT_HANDLER}</td>
                            <td>
                            <select title="{LANG_EVENT_HANDLER}" name="selValue3" id="selValue3" class="selectborder">
<!-- BEGIN eventhandler -->
                                <option value="{DAT_EVENTHANDLER_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_EVENTHANDLER_SEL}>{DAT_EVENTHANDLER}</option>
<!-- END eventhandler -->
                            </select>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','event_handler','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_EVENT_HANDLER_ENABLED}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue9" type="radio" class="checkbox" id="radValue91" value="1" {DAT_EVH1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue9" type="radio" class="checkbox" id="radValue90" value="0" {DAT_EVH0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue9" type="radio" class="checkbox" id="radValue92" value="2" {DAT_EVH2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue9" type="radio" class="checkbox" id="radValue93" value="3" {DAT_EVH3_CHECKED}></td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table> 
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','event_handler_enabled','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>{LANG_LOW_FLAP_THRESHOLD}</td>
                            <td><input title="{LANG_LOW_FLAP_THRESHOLD}" name="tfNullVal5" type="text" id="tfNullVal5" value="{DAT_LOW_FLAP_THRESHOLD}" class="short"><span class="shorttext">%</span></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','low_flap_threshold','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_HIGH_FLAP_THRESHOLD}</td>
                            <td><input title="{LANG_HIGH_FLAP_THRESHOLD}" name="tfNullVal6" type="text" id="tfNullVal6" value="{DAT_HIGH_FLAP_THRESHOLD}" class="short"><span class="shorttext">%</span></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','high_flap_threshold','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_FLAP_DETECTION_ENABLED}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue10" type="radio" class="checkbox" id="radValue101" value="1" {DAT_FLE1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue10" type="radio" class="checkbox" id="radValue100" value="0" {DAT_FLE0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue10" type="radio" class="checkbox" id="radValue102" value="2" {DAT_FLE2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue10" type="radio" class="checkbox" id="radValue103" value="3" {DAT_FLE3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>       
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','flap_detection_enabled','all','Info');" class="infobutton_1"></td>
                            <td class="{VERSION_30_VISIBLE}">{LANG_FLAP_DETECTION_OPTIONS}</td>
                            <td class="{VERSION_30_VISIBLE}">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="o" name="chbGr3a" type="checkbox" class="checkbox" id="chbGr3a" value="o" {DAT_FLO_CHECKED}></td>
                                        <td class="radio_cell_1">o</td>
                                        <td class="radio_cell_1"><input title="d" name="chbGr3b" type="checkbox" class="checkbox" id="chbGr3b" value="d" {DAT_FLD_CHECKED}></td>
                                        <td class="radio_cell_1">d</td>
                                        <td class="radio_cell_1"><input title="u" name="chbGr3c" type="checkbox" class="checkbox" id="chbGr3c" value="u" {DAT_FLU_CHECKED}></td>
                                        <td class="radio_cell_1">u</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','flap_detection_options','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>{LANG_RETAIN_STATUS_INFORMATION}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue11" type="radio" class="checkbox" id="radValue111" value="1" {DAT_STI1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue11" type="radio" class="checkbox" id="radValue110" value="0" {DAT_STI0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue11" type="radio" class="checkbox" id="radValue112" value="2" {DAT_STI2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue11" type="radio" class="checkbox" id="radValue113" value="3" {DAT_STI3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table> 
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','retain_status_information','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_RETAIN_NOSTATUS_INFORMATION}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue12" type="radio" class="checkbox" id="radValue121" value="1" {DAT_NSI1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue12" type="radio" class="checkbox" id="radValue120" value="0" {DAT_NSI0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue12" type="radio" class="checkbox" id="radValue122" value="2" {DAT_NSI2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue12" type="radio" class="checkbox" id="radValue123" value="3" {DAT_NSI3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table> 
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','retain_nonstatus_information','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_PROCESS_PERF_DATA}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue13" type="radio" class="checkbox" id="radValue131" value="1" {DAT_PED1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue13" type="radio" class="checkbox" id="radValue130" value="0" {DAT_PED0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue13" type="radio" class="checkbox" id="radValue132" value="2" {DAT_PED2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue13" type="radio" class="checkbox" id="radValue133" value="3" {DAT_PED3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table> 
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','process_perf_data','all','Info');" class="infobutton_1"></td>
                            <td colspan="3">&nbsp;</td>
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
                            <td valign="top">{LANG_CONTACT_GROUPS} *<br><br><small>{LANG_CTRLINFO}</small></td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td>
                                            <select title="{LANG_CONTACT_GROUPS}" name="mselValue4[]" size="4" multiple id="mselValue4" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN host_contactgroups -->
                                                <option value="{DAT_HOST_CONTACTGROUPS_ID}"  class="empty_class inpmust {SPECIAL_STYLE} {IE_HOST_CONTACTGROUPS_SEL}" {DAT_HOST_CONTACTGROUPS_SEL} {OPTION_DISABLED}>{DAT_HOST_CONTACTGROUPS}</option>
<!-- END host_contactgroups -->
                                            </select>
                                        </td>
                                    </tr>
                                </table>   	  
                            </td>
                            <td valign="top"><img id="mutvalue4" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onClick="dialoginit('host','contactgroups','all','Info');" class="infobutton_2"></td>                        
                            <td valign="top"><span class="{VERSION_30_VISIBLE}">{LANG_CONTACTS} *<br><br><small>{LANG_CTRLINFO}</small></span></td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr class="{VERSION_30_VISIBLE}">
                                        <td>
                                            <select title="{LANG_CONTACTS}" name="mselValue3[]" size="4" multiple id="mselValue3" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN host_contacts -->
                                                <option value="{DAT_HOST_CONTACTS_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_HOST_CONTACTS_SEL}" {DAT_HOST_CONTACTS_SEL} {OPTION_DISABLED}>{DAT_HOST_CONTACTS}</option>
<!-- END host_contacts -->
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top"><span class="{VERSION_30_VISIBLE}"><img id="mutvalue3" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onClick="dialoginit('host','contacts','3','Info');" class="infobutton_2"></span></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>&nbsp;</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="+" name="radValue4" type="radio" class="checkbox" id="radValue40" value="0" {DAT_COG0_CHECKED}></td>
                                        <td class="radio_cell_2">+</td>
                                        <td class="radio_cell_1"><input title="null" name="radValue4" type="radio" class="checkbox" id="radValue41" value="1" {DAT_COG1_CHECKED}></td>
                                        <td class="radio_cell_2">null</td>
                                        <td class="radio_cell_1"><input title="{LANG_STANDARD}" name="radValue4" type="radio" class="checkbox" id="radValue42" value="2" {DAT_COG2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_STANDARD}</td>
                                    </tr>
                                </table>            
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','tploptions','3','Info');" class="infobutton_1"></td>
                            <td>&nbsp;</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="+" name="radValue3" type="radio" class="checkbox" id="radValue30" value="0" {DAT_COT0_CHECKED}></td>
                                        <td class="radio_cell_2">+</td>
                                        <td class="radio_cell_1"><input title="null" name="radValue3" type="radio" class="checkbox" id="radValue31" value="1" {DAT_COT1_CHECKED}></td>
                                        <td class="radio_cell_2">null</td>
                                        <td class="radio_cell_1"><input title="{LANG_STANDARD}" name="radValue3" type="radio" class="checkbox" id="radValue32" value="2" {DAT_COT2_CHECKED}></td>
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
                                <select title="{LANG_NOTIFICATION_PERIOD}" name="selValue4" id="selValue4"  class="selectbordermust inpmust">
<!-- BEGIN notifyperiod -->
                                    <option value="{DAT_NOTIFYPERIOD_ID}" class="empty_class {SPECIAL_STYLE}" {DAT_NOTIFYPERIOD_SEL}>{DAT_NOTIFYPERIOD}</option>
<!-- END notifyperiod -->
                                </select>
                            </td>    
                            <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','notification_period','all','Info');" class="infobutton_1"></td>
                            <td class="content_tbl_row1">{LANG_NOTIFICATION_OPTIONS} {VERSION_20_STAR}</td>
                            <td class="content_tbl_row2">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="d" name="chbGr1a" type="checkbox" class=" checkbox" id="chbGr1a" value="d" {DAT_NOD_CHECKED}></td>
                                        <td class="radio_cell_1">d</td>
                                        <td class="radio_cell_1"><input title="u" name="chbGr1b" type="checkbox" class=" checkbox" id="chbGr1b" value="u" {DAT_NOU_CHECKED}></td>
                                        <td class="radio_cell_1">u</td>
                                        <td class="radio_cell_1"><input title="r" name="chbGr1c" type="checkbox" class=" checkbox" id="chbGr1c" value="r" {DAT_NOR_CHECKED}></td>
                                        <td class="radio_cell_1">r</td>
                                        <td class="radio_cell_1"><input title="f" name="chbGr1d" type="checkbox" class=" checkbox" id="chbGr1d" value="f" {DAT_NOF_CHECKED}></td>
                                        <td class="radio_cell_1">f</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="s" name="chbGr1e" type="checkbox" class=" checkbox" id="chbGr1e" value="s" {DAT_NOS_CHECKED}></td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}">s</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="content_tbl_row4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','notification_options','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_NOTIFICATION_INTERVAL} *</td>
                            <td><input title="{LANG_NOTIFICATION_INTERVAL}" name="tfNullVal7" type="text" id="tfNullVal7" value="{DAT_NOTIFICATION_INTERVAL}" class="shortmust"><span class="shorttext">min</span></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','notification_intervall','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_FIRST_NOTIFICATION_DELAY}</td>
                            <td><input title="{LANG_FIRST_NOTIFICATION_DELAY}" name="tfNullVal8" type="text" id="tfNullVal8" value="{DAT_FIRST_NOTIFICATION_DELAY}" class="short"><span class="shorttext">min</span></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','first_notification_delay','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_NOTIFICATION_ENABLED}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue14" type="radio" class="checkbox" id="radValue141" value="1" {DAT_NOE1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue14" type="radio" class="checkbox" id="radValue140" value="0" {DAT_NOE0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue14" type="radio" class="checkbox" id="radValue142" value="2" {DAT_NOE2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue14" type="radio" class="checkbox" id="radValue143" value="3" {DAT_NOE3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','notification_enabled','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_STALKING_OPTIONS}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="o" name="chbGr4a" type="checkbox" class=" checkbox" id="chbGr4a" value="o" {DAT_STO_CHECKED}></td>
                                        <td class="radio_cell_1">o</td>
                                        <td class="radio_cell_1"><input title="d" name="chbGr4b" type="checkbox" class=" checkbox" id="chbGr4b" value="d" {DAT_STD_CHECKED}></td>
                                        <td class="radio_cell_1">d</td>
                                        <td class="radio_cell_1"><input title="u" name="chbGr4c" type="checkbox" class=" checkbox" id="chbGr4c" value="u" {DAT_STU_CHECKED}></td>
                                        <td class="radio_cell_1">u</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','stalking_options','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr class="{VERSION_40_VISIBLE}">
                            <td>{LANG_IMPORTANCE}</td>
                            <td><input title="{LANG_IMPORTANCE}" name="tfNullVal9" type="text" id="tfNullVal9" value="{DAT_IMPORTANCE}"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','importance','all','Info');" class="infobutton_1"></td>
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
                            <td>{LANG_NOTES}</td>
                            <td><input title="{LANG_NOTES}" name="tfValue7" type="text" id="tfValue7" value="{DAT_NOTES}"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','notes','3','Info');" class="infobutton_1"></td>
                            <td>{LANG_VRML_IMAGE}</td>
                            <td><input title="{LANG_VRML_IMAGE}" name="tfValue8" type="text" id="tfValue8" value="{DAT_VRML_IMAGE}"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','vrml_image','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>{LANG_NOTES_URL}</td>
                            <td><input title="{LANG_NOTES_URL}" name="tfValue9" type="text" id="tfValue9" value="{DAT_NOTES_URL}"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','notes_url','3','Info');" class="infobutton_1"></td>
                            <td>{LANG_STATUS_IMAGE}</td>
                            <td><input title="{LANG_STATUS_IMAGE}" name="tfValue10" type="text" id="tfValue10" value="{DAT_STATUSMAP_IMAGE}"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','status_image','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>{LANG_ACTION_URL}</td>
                            <td><input title="{LANG_ACTION_URL}" name="tfValue11" type="text" id="tfValue11" value="{DAT_ACTION_URL}"></td>
                            <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','action_url','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>{LANG_ICON_IMAGE}</td>
                            <td><input title="{LANG_ICON_IMAGE}" name="tfValue12" type="text" id="tfValue12" value="{DAT_ICON_IMAGE}"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','icon_image','3','Info');" class="infobutton_1"></td>
                            <td>{LANG_2D_COORDS}</td>
                            <td><input title="{LANG_2D_COORDS}" name="tfValue14" type="text" id="tfValue14" value="{DAT_2D_COORDS}" class="short"><span class="shorttext">(x,y)</span></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','2d_coords','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>{LANG_ICON_IMAGE_ALT_TEXT}</td>
                            <td><input title="{LANG_ICON_IMAGE_ALT_TEXT}" name="tfValue13" type="text" id="tfValue13" value="{DAT_ICON_IMAGE_ALT}"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','icon_image_alt_text','3','Info');" class="infobutton_1"></td>
                            <td>{LANG_3D_COORDS}</td>
                            <td><input title="{LANG_3D_COORDS}" name="tfValue15" type="text" id="tfValue15" value="{DAT_3D_COORDS}" class="short"><span class="shorttext">(x,y,z)</span></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','3d_coords','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td colspan="6">&nbsp;</td>
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
                            <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','accessgroup','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr {RESTRICT_GROUP_ADMIN}>
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
                            <td colspan="6" style="padding-bottom:10px"><iframe name="variableframe" id="variableframe" frameborder="0" src="{BASE_PATH}admin/variabledefinitions.php?dataId={DAT_ID}&amp;linktab=tbl_lnkHostToVariabledefinition" style="width:540px;height:150px;border:1px solid #000000"></iframe></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>{LANG_VARIABLE_NAME}</td>
                            <td><input title="{LANG_VARIABLE_NAME}" type="text" name="txtVariablename" id="txtVariablename" class="selectborder"></td>
                            <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','free_variables_name','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>{LANG_VARIABLE_VALUE}</td>
                            <td><input title="{LANG_VARIABLE_VALUE}" type="text" name="txtVariablevalue" id="txtVariablevalue" class="selectborder"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','free_variables_value','all','Info');" class="infobutton_1"></td>
                            <td colspan="3"><input type="button" name="butVariableDefinition" value="{LANG_INSERT}" onClick="insertDefintionVar()"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="6" style="padding-bottom:5px"><b>{LANG_USE_THIS_AS_TEMPLATE}</b></td>
                        </tr>
                        <tr>
                            <td class="content_tbl_row1">{LANG_GENERIC_NAME}</td>
                            <td class="content_tbl_row2"><input title="{LANG_GENERIC_NAME}" type="text" name="tfValue6" id="tfValue6" value="{DAT_NAME}"></td>
                            <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('host','genericname','all','Info');" class="infobutton_1"></td>
                            <td class="content_tbl_row1">&nbsp;</td>
                            <td class="content_tbl_row2">&nbsp;</td>
                            <td class="content_tbl_row4">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3"><input name="subForm" type="button" id="subForm4" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE}>&nbsp;<input name="subAbort" type="button" id="subAbort4" onClick="abort();" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span></td>
                            <td colspan="3"><span class="redmessage">{WARNING}</span></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
					</table>
                </div>
                <div id="tab5">
                    <table border="0" cellpadding="0" class="content_formtable {VERSION_30_VISIBLE}">   
                        <tr>
                            <td colspan="3"><strong>{LANG_SERVICE_SETTINGS}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3">{LANG_SERVICE_SETTINGS_DESC}</td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="content_tbl_row1" valign="top">{LANG_SERVICES}<br><br><small>{LANG_CTRLINFO}</small></td>
                            <td style="width:685px;">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td>
                                            <select title="{LANG_SERVICES}" name="mselValue5[]" size="8" multiple id="mselValue5" class="selectborder" style="width:450px" {MSIE_DISABLED}>
<!-- BEGIN host_services -->
                                                <option value="{DAT_HOST_SERVICES_ID}" class="empty_class {SPECIAL_STYLE} {IE_HOST_SERVICES_SEL}" {DAT_HOST_SERVICES_SEL} {OPTION_DISABLED}>{DAT_HOST_SERVICES}</option>
<!-- END host_services -->
                                            </select>
                                        </td>
                                        <td valign="top"><img id="mutvalue5" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onClick="dialoginit('host','services','all','Info');" class="infobutton_2"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3">{LANG_SERVICES_WARNING}</td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3">
                            	<table cellpadding="0" cellspacing="0" border="0" width="100%">
                                	<tr>
                                        <td style="width:450px;"><input name="subForm" type="button" id="subForm5" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE}>&nbsp;<input name="subAbort" type="button" id="subAbort5" onClick="abort();" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span></td>
                                        <td><span class="redmessage">{WARNING}</span></td>
                                    </tr>
                                </table>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
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
            const tabView = new YAHOO.widget.TabView('host');
        })();
        //-->
    </script>
    <br>
    <span id="rel_text" class="{RELATION_CLASS}"><a href="javascript:showRelationData(1)" style="color:#00F">[{LANG_SHOW_RELATION_DATA}]</a></span><span id="rel_info" class="elementHide"><a href="javascript:showRelationData(0)" style="color:#00F">[{LANG_HIDE_RELATION_DATA}]</a>{CHECK_MUST_DATA}</span>
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
<div id="msgcontainer"></div>
<div id="confirmcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->