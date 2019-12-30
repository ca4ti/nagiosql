<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : contact template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datainsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <!--suppress JSUnusedLocalSymbols -->
    <script type="text/javascript">
        <!--
        // Initialize change dialog
        openMutDlgInit("mselValue1","mutdialogvalue1","{LANG_MODIFY_SELECTION}: {LANG_CONTACT_GROUP}","mutvalue1","{LANG_SAVE}","{LANG_ABORT}");
        openMutDlgInit("mselValue2","mutdialogvalue2","{LANG_MODIFY_SELECTION}: {LANG_HOST_COMMAND}","mutvalue2","{LANG_SAVE}","{LANG_ABORT}","1");
        openMutDlgInit("mselValue3","mutdialogvalue3","{LANG_MODIFY_SELECTION}: {LANG_SERVICE_COMMAND}","mutvalue3","{LANG_SAVE}","{LANG_ABORT}","1");
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
                const selfields = "mselValue1,mselValue2,mselValue3";
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
            const fields1 = "tfValue1{VERSION_20_VALUE_MUST}";
            const fields2 = "{HOST_OPTION_FIELDS}";
            const fields3 = "{SERVICE_OPTION_FIELDS}";
            const fields4 = "selValue1,selValue2,mselValue2,mselValue3";
            const version = "{NAGIOS_VERSION}";
            const bypass  = "{CHECK_BYPASS_NEW}";
            const msg1 = "{FILL_ALLFIELDS}";
            const msg2 = "{FILL_ILLEGALCHARS}";
            const header = "{LANG_FORMCHECK}";
            const form = document.frmDetail;
            let check = checkfields(fields1,form,myFocusObject);
            if (check === false) {
                msginit(msg1,header,1);
                return false;
            }
            // Check for illegal chars
            if (form.tfValue1.value.match(/[^a-zA-Z0-9.@_-]/)) {
                msginit(msg2+" {LANG_CONTACT_NAME}",header,1);
                form.tfValue1.focus();
                return false;
            }
            if (form.tfValue11.value.match(/[^a-zA-Z0-9.@_-]/)) {
                msginit(msg2+" {LANG_GENERIC_NAME}",header,1);
                form.tfValue11.focus();
                return false;
            }
            if (form.tfNullVal1.value.match(/[^0-9]/)) {
                msginit(msg2+" {LANG_MINIMUM_IMPORTANCE}",header,1);
                form.tfNullVal1.focus();
                return false;
            }
            if (bypass === '0') {
                if ((version === '3') && ((form.radValue2[2].checked === true) || (form.radValue3[2].checked === true))) {
                    confirminit("{LANG_MUST_BUT_TEMPLATE}", "{LANG_SECURE_QUESTION}", 2, "{LANG_YES}", "{LANG_NO}", 2);
                    return false
                }
                check = checkfields2(fields4, form, myFocusObject);
                if (check === false) {
                    confirminit("{LANG_MUST_BUT_TEMPLATE}", "{LANG_SECURE_QUESTION}", 2, "{LANG_YES}", "{LANG_NO}", 2);
                    return false
                }
                check = checkboxes(fields2, form);
                if (check === false) {
                    confirminit("{LANG_MUST_BUT_TEMPLATE}", "{LANG_SECURE_QUESTION}", 2, "{LANG_YES}", "{LANG_NO}", 2);
                    return false
                }
                check = checkboxes(fields3, form);
                if (check === false) {
                    confirminit("{LANG_MUST_BUT_TEMPLATE}", "{LANG_SECURE_QUESTION}", 2, "{LANG_YES}", "{LANG_NO}", 2);
                    return false
                }
            }
            return true;
        }
        // Insert free variable definitions
        function insertDefintionVar() {
            const txtDef = document.frmDetail.txtVariablename.value;
            const txtRange = document.frmDetail.txtVariablevalue.value;
            if ((txtDef === '') || (txtRange === '')) {
                const header = "{LANG_FORMCHECK}";
                msginit("{LANG_INSERT_ALL_VARIABLE}",header,1);
                return false;
            }
            document.getElementById("variableframe").src = "{BASE_PATH}admin/variabledefinitions.php?dataId={DAT_ID}&version={NAGIOS_VERSION}&mode=add&def="+encodeURIComponent(txtDef)+"&range="+encodeURIComponent(txtRange);
        }
        // Insert template definitions
        function insertDefintion() {
            const txtDef = document.frmDetail.selTemplate.value;
            document.getElementById("templframe").src = "{BASE_PATH}admin/templatedefinitions.php?dataId={DAT_ID}&type=contact&mode=add&def="+txtDef;
        }
        // Process security question answers
        function confOpenerYes(key) {
            if (key === 2) {
                // Enable select fields
                const selfields = "mselValue1,mselValue2,mselValue3";
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
        //-->
    </script>
    <form name="frmDetail" method="post" action="{ACTION_INSERT}">
        <div id="contacts" style="width:909px;" class="yui-navset">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{LANG_COMMON_SETTINGS}</em></a></li>
                <li><a href="#tab1"><em>{LANG_ADDON_SETTINGS}</em></a></li>
            </ul>            
            <div class="yui-content">
                <div id="tab1">
                    <table border="0" cellpadding="0" class="content_formtable">
                        <tr>
                            <td class="content_tbl_row1">{LANG_CONTACT_NAME} *</td>
                            <td class="content_tbl_row2"><input title="{LANG_CONTACT_NAME}" name="tfValue1" type="text" id="tfValue1" value="{DAT_CONTACT_NAME}" class="inpmust"></td>
                            <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','contact_name','all','Info');" class="infobutton_1"></td>
                            <td class="content_tbl_row1">{LANG_CONTACT_GROUP}</td>
                            <td class="content_tbl_row2" rowspan="3" valign="top">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td>
                                            <select title="{LANG_CONTACT_GROUP}" name="mselValue1[]" size="4" multiple id="mselValue1" class="selectborder" {MSIE_DISABLED}>
<!-- BEGIN contactgroup -->
                                                <option value="{DAT_CONTACTGROUP_ID}" class="empty_class {SPECIAL_STYLE} {IE_CONTACTGROUP_SEL}" {DAT_CONTACTGROUP_SEL} {OPTION_DISABLED}>{DAT_CONTACTGROUP}</option>
<!-- END contactgroup -->
                                            </select>
                                        </td>
                                    </tr>
                                </table> 
                            </td>
                            <td class="content_tbl_row4" rowspan="3" valign="top"><img id="mutvalue1" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','contactgroups','all','Info');" class="infobutton_1"></td>      
                        </tr>
                        <tr>
                            <td>{LANG_DESCRIPTION} {VERSION_20_STAR}</td>
                            <td><input title="{LANG_DESCRIPTION}" name="tfValue2" type="text" id="tfValue2" value="{DAT_ALIAS}" class="empty_class {VERSION_20_MUST}"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','alias','all','Info');" class="infobutton_1"></td>
                            <td rowspan="2"><small>{LANG_CTRLINFO}</small></td>
                        </tr>
                        <tr>
                            <td class="{VERSION_40_VISIBLE}">{LANG_MINIMUM_IMPORTANCE}</td>
                            <td class="{VERSION_40_VISIBLE}"><input title="{LANG_MINIMUM_IMPORTANCE}" name="tfNullVal1" type="text" id="tfNullVal1" value="{DAT_MINIMUM_IMPORTANCE}"></td>
                            <td class="{VERSION_40_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','minimum_importance','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td colspan="4">&nbsp;</td> 
                            <td> 
                                <table cellpadding="0" cellspacing="0" border="0" class="{VERSION_30_VISIBLE}">
                                    <tr>
                                        <td class="radio_cell_1"><input title="+" name="radValue1" type="radio" class="checkbox" id="radValue10" value="0" {DAT_COG0_CHECKED}></td>
                                        <td class="radio_cell_2">+</td>
                                        <td class="radio_cell_1"><input title="null" name="radValue1" type="radio" class="checkbox" id="radValue11" value="1" {DAT_COG1_CHECKED}></td>
                                        <td class="radio_cell_2">null</td>
                                        <td class="radio_cell_1"><input title="{LANG_STANDARD}" name="radValue1" type="radio" class="checkbox" id="radValue12" value="2" {DAT_COG2_CHECKED}></td>
                                        <td>{LANG_STANDARD}</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','tploptions','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_EMAIL_ADDRESS}</td>
                            <td><input title="{LANG_EMAIL_ADDRESS}" name="tfValue3" type="text" id="tfValue3" value="{DAT_EMAIL}"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','email','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_PAGER_NUMBER}</td>
                            <td><input title="{LANG_PAGER_NUMBER}" name="tfValue4" type="text" id="tfValue4" value="{DAT_PAGER}"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','pager','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_ADDON_ADDRESS} 1</td>
                            <td><input title="{LANG_ADDON_ADDRESS} 1" name="tfValue5" type="text" id="tfValue5" value="{DAT_ADDRESS1}"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','address','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_ADDON_ADDRESS} 2</td>
                            <td colspan="2"><input title="{LANG_ADDON_ADDRESS} 2" name="tfValue6" type="text" id="tfValue6" value="{DAT_ADDRESS2}"></td>
                        </tr>
                        <tr>
                            <td>{LANG_ADDON_ADDRESS} 3</td>
                            <td colspan="2"><input title="{LANG_ADDON_ADDRESS} 3" name="tfValue7" type="text" id="tfValue7" value="{DAT_ADDRESS3}"></td>
                            <td>{LANG_ADDON_ADDRESS} 4</td>
                            <td colspan="2"><input title="{LANG_ADDON_ADDRESS} 4" name="tfValue8" type="text" id="tfValue8" value="{DAT_ADDRESS4}"></td>
                        </tr>
                        <tr>
                            <td>{LANG_ADDON_ADDRESS} 5</td>
                            <td colspan="2"><input title="{LANG_ADDON_ADDRESS} 5" name="tfValue9" type="text" id="tfValue9" value="{DAT_ADDRESS5}"></td>
                            <td>{LANG_ADDON_ADDRESS} 6</td>
                            <td colspan="2"><input title="{LANG_ADDON_ADDRESS} 6" name="tfValue10" type="text" id="tfValue10" value="{DAT_ADDRESS6}"></td>
                        </tr>    
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>{LANG_HOST_NOTIF_ENABLE} *</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue2" type="radio" class="checkbox" id="radValue21" value="1" {DAT_HNE1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue2" type="radio" class="checkbox" id="radValue20" value="0" {DAT_HNE0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue2" type="radio" class="checkbox" id="radValue22" value="2" {DAT_HNE2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','host_notifications_enabled','3','Info');" class="infobutton_1"></td>
                            <td>{LANG_SERVICE_NOTIF_ENABLE} *</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue3" type="radio" class="checkbox" id="radValue31" value="1" {DAT_SNE1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue3" type="radio" class="checkbox" id="radValue30" value="0" {DAT_SNE0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue3" type="radio" class="checkbox" id="radValue32" value="2" {DAT_SNE2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','service_notifications_enabled','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_TIME_PERIOD_HOSTS} *</td>
                            <td>
                                <select title="{LANG_TIME_PERIOD_HOSTS}" name="selValue1" id="selValue1" class="selectbordermust inpmust">
<!-- BEGIN host_time -->
                                    <option value="{DAT_HOST_TIME_ID}" class="empty_class inpmust {SPECIAL_STYLE}" {DAT_HOST_TIME_SEL}>{DAT_HOST_TIME}</option>
<!-- END host_time -->
                                </select>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','host_notification_period','all','Info');" class="infobutton_1"></td>
                            <td>{LANG_TIME_PERIOD_SERVICES} *</td>
                            <td>
                                <select title="{LANG_TIME_PERIOD_SERVICES}" name="selValue2" id="selValue2" class="selectbordermust inpmust">
<!-- BEGIN service_time -->
                                    <option value="{DAT_SERVICE_TIME_ID}" class="empty_class inpmust {SPECIAL_STYLE}" {DAT_SERVICE_TIME_SEL}>{DAT_SERVICE_TIME}</option>
<!-- END service_time -->
                                </select>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','service_notification_period','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td>{LANG_HOST_OPTIONS} *</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="d" name="chbGr1a" type="checkbox" class="checkbox" id="chbGr1a" value="d" {DAT_HOD_CHECKED}></td>
                                        <td class="radio_cell_1">d</td>
                                        <td class="radio_cell_1"><input title="u" name="chbGr1b" type="checkbox" class="checkbox" id="chbGr1b" value="u" {DAT_HOU_CHECKED}></td>
                                        <td class="radio_cell_1">u</td>
                                        <td class="radio_cell_1"><input title="r" name="chbGr1c" type="checkbox" class="checkbox" id="chbGr1c" value="r" {DAT_HOR_CHECKED}></td>
                                        <td class="radio_cell_1">r</td>
                                        <td class="radio_cell_1"><input title="f" name="chbGr1d" type="checkbox" class="checkbox" id="chbGr1d" value="f" {DAT_HOF_CHECKED}></td>
                                        <td class="radio_cell_1">f</td>    
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="s" name="chbGr1e" type="checkbox" class="checkbox" id="chbGr1e" value="s" {DAT_HOS_CHECKED}></td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}">s</td>   
                                        <td class="radio_cell_1"><input title="n" name="chbGr1f" type="checkbox" class="checkbox" id="chbGr1f" value="n" {DAT_HON_CHECKED}></td>
                                        <td class="radio_cell_1">n</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="{VERSION_20_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','host_notification_options','2','Info');" class="infobutton_1"></td>
                            <td class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','host_notification_options','3','Info');" class="infobutton_1"></td>
                            <td>{LANG_SERVICE_OPTIONS} *</td>
                            <td valign="middle">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="w" name="chbGr2a" type="checkbox" class="checkbox" id="chbGr2a" value="w" {DAT_SOW_CHECKED}></td>
                                        <td class="radio_cell_1">w</td>
                                        <td class="radio_cell_1"><input title="u" name="chbGr2b" type="checkbox" class="checkbox" id="chbGr2b" value="u" {DAT_SOU_CHECKED}></td>
                                        <td class="radio_cell_1">u</td>
                                        <td class="radio_cell_1"><input title="c" name="chbGr2c" type="checkbox" class="checkbox" id="chbGr2c" value="c" {DAT_SOC_CHECKED}></td>
                                        <td class="radio_cell_1">c</td>
                                        <td class="radio_cell_1"><input title="r" name="chbGr2d" type="checkbox" class="checkbox" id="chbGr2d" value="r" {DAT_SOR_CHECKED}></td>
                                        <td class="radio_cell_1">r</td>    
                                        <td class="radio_cell_1"><input title="f" name="chbGr2e" type="checkbox" class="checkbox" id="chbGr2e" value="f" {DAT_SOF_CHECKED}></td>
                                        <td class="radio_cell_1">f</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="s" name="chbGr2f" type="checkbox" class="checkbox" id="chbGr2f" value="s" {DAT_SOS_CHECKED}></td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}">s</td>
                                        <td class="radio_cell_1"><input title="n" name="chbGr2g" type="checkbox" class="checkbox" id="chbGr2g" value="n" {DAT_SON_CHECKED}></td>
                                        <td class="radio_cell_1">n</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="{VERSION_20_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','service_notification_options','2','Info');" class="infobutton_1"></td>
                            <td class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','service_notification_options','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td valign="top">{LANG_HOST_COMMAND} *</td>
                            <td rowspan="2" valign="top">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td>
                                            <select title="{LANG_HOST_COMMAND}" name="mselValue2[]" size="4" multiple id="mselValue2" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN host_command -->
                                                <option value="{DAT_HOST_COMMAND_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_HOST_COMMAND_SEL}" {DAT_HOST_COMMAND_SEL} {OPTION_DISABLED}>{DAT_HOST_COMMAND}</option>
<!-- END host_command -->
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td rowspan="2" valign="top"><img id="mutvalue2" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','host_notification_commands','all','Info');" class="infobutton_2"></td>
                            <td valign="top">{LANG_SERVICE_COMMAND} *</td>
                            <td rowspan="2" valign="top">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td>
                                            <select title="{LANG_SERVICE_COMMAND}" name="mselValue3[]" size="4" multiple id="mselValue3" class="selectbordermust inpmust" {MSIE_DISABLED}>
<!-- BEGIN service_command -->
                                                <option value="{DAT_SERVICE_COMMAND_ID}" class="empty_class inpmust {SPECIAL_STYLE} {IE_SERVICE_COMMAND_SEL}" {DAT_SERVICE_COMMAND_SEL} {OPTION_DISABLED}>{DAT_SERVICE_COMMAND}</option>
<!-- END service_command -->
                                            </select>
                                        </td>
                                    </tr>
                                </table>  
                            </td>
                            <td valign="top" rowspan="2"><img id="mutvalue3" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','service_notification_commands','all','Info');" class="infobutton_2"></td>
                        </tr>
                        <tr>
                            <td><small>{LANG_CTRLINFO}</small></td>
                            <td><small>{LANG_CTRLINFO}</small></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0" class="{VERSION_30_VISIBLE}">
                                    <tr>
                                        <td class="radio_cell_1"><input title="+" name="radValue4" type="radio" class="checkbox" id="radValue40" value="0" {DAT_HOC0_CHECKED}></td>
                                        <td class="radio_cell_2">+</td>
                                        <td class="radio_cell_1"><input title="{LANG_STANDARD}" name="radValue4" type="radio" class="checkbox" id="radValue42" value="2" {DAT_HOC2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_STANDARD}</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','tploptions','3','Info');" class="infobutton_1"></td>
                            <td>&nbsp;</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0" class="{VERSION_30_VISIBLE}">
                                    <tr>
                                        <td class="radio_cell_1"><input title="+" name="radValue5" type="radio" class="checkbox" id="radValue50" value="0" {DAT_SEC0_CHECKED}></td>
                                        <td class="radio_cell_2">+</td>
                                        <td class="radio_cell_1"><input title="{LANG_STANDARD}" name="radValue5" type="radio" class="checkbox" id="radValue52" value="2" {DAT_SEC2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_STANDARD}</td>
                                    </tr>
                                </table>
                            </td>
                            <td class="{VERSION_30_VISIBLE}"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','tploptions','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>  
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>{LANG_RETAIN_STATUS_INFO}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue6" type="radio" class="checkbox" id="radValue61" value="1" {DAT_RSI1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue6" type="radio" class="checkbox" id="radValue60" value="0" {DAT_RSI0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue6" type="radio" class="checkbox" id="radValue62" value="2" {DAT_RSI2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue6" type="radio" class="checkbox" id="radValue63" value="3" {DAT_RSI3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','retain_status_information','3','Info');" class="infobutton_1"></td>    
                            <td>{LANG_CAN_SUBMIT_COMMANDS}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue8" type="radio" class="checkbox" id="radValue81" value="1" {DAT_CSC1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue8" type="radio" class="checkbox" id="radValue80" value="0" {DAT_CSC0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue8" type="radio" class="checkbox" id="radValue82" value="2" {DAT_CSC2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue8" type="radio" class="checkbox" id="radValue83" value="3" {DAT_CSC3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','can_submit_commands','3','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>{LANG_RETAIN_NONSTATUS_INFO}</td>
                            <td>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td class="radio_cell_1"><input title="{LANG_ON}" name="radValue7" type="radio" class="checkbox" id="radValue71" value="1" {DAT_RNS1_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_ON}</td>
                                        <td class="radio_cell_1"><input title="{LANG_OFF}" name="radValue7" type="radio" class="checkbox" id="radValue70" value="0" {DAT_RNS0_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_OFF}</td>
                                        <td class="radio_cell_1"><input title="{LANG_SKIP}" name="radValue7" type="radio" class="checkbox" id="radValue72" value="2" {DAT_RNS2_CHECKED}></td>
                                        <td class="radio_cell_2">{LANG_SKIP}</td>
                                        <td class="radio_cell_1 {VERSION_30_VISIBLE}"><input title="null" name="radValue7" type="radio" class="checkbox" id="radValue73" value="3" {DAT_RNS3_CHECKED}></td>
                                        <td class="radio_cell_2 {VERSION_30_VISIBLE}">null</td>
                                    </tr>
                                </table> 
                            </td> 
                            <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','retain_nostatus_information','3','Info');" class="infobutton_1"></td>         
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
                            <td colspan="3"><input name="subForm" type="button" id="subForm1" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE}>&nbsp;<input name="subAbort" type="button" id="subAbort1" onClick="abort();" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span></td>
                            <td colspan="3"><span class="redmessage">{WARNING}</span></td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
					</table>
                </div>
                <div id="tab2">  
                    <table border="0" cellpadding="0" class="content_formtable">
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
                            <td colspan="6" style="padding-bottom:10px"><iframe id="variableframe" frameborder="0" src="{BASE_PATH}admin/variabledefinitions.php?dataId={DAT_ID}&amp;linktab=tbl_lnkContactToVariabledefinition" style="width:540px;height:150px;border:1px solid #000000"></iframe></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">

                            <td>{LANG_VARIABLE_NAME}</td>
                            <td><input title="{LANG_VARIABLE_NAME}" type="text" name="txtVariablename" id="txtVariablename"></td>
                            <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','free_variables_name','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td>{LANG_VARIABLE_VALUE}</td>
                            <td><input title="{LANG_VARIABLE_VALUE}" type="text" name="txtVariablevalue" id="txtVariablevalue"></td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','free_variables_value','all','Info');" class="infobutton_1"></td>
                            <td colspan="3"><input type="button" name="butVariableDefinition" value="{LANG_INSERT}" onClick="insertDefintionVar();"></td>
                        </tr>
                        <tr class="{VERSION_30_VISIBLE}">
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="6" style="padding-bottom:5px;"><strong>{LANG_ADDITIONAL_TEMPLATES}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="padding-bottom:10px;"><iframe id="templframe" frameborder="0" src="{BASE_PATH}admin/templatedefinitions.php?dataId={DAT_ID}&amp;type=contact" style="border:1px solid #000000; width:445px; height:120px;"></iframe></td>
                        </tr>
                        <tr>
                            <td>{LANG_TEMPLATE_NAME}</td>
                            <td>
                                <select title="{LANG_TEMPLATE_NAME}" name="selTemplate" class="selectborder">
<!-- BEGIN template -->
                                    <option value="{DAT_TEMPLATE_ID}" class="empty_class{SPECIAL_STYLE}">{DAT_TEMPLATE}</option>
<!-- END template -->
                                </select>
                            </td>
                            <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','templateadd','all','Info');" class="infobutton_1"></td>
                            <td colspan="3"><input type="button" name="butTemplDefinition"  value="{LANG_INSERT}" onClick="insertDefintion();"></td>
                        </tr>
                        <tr>
                            <td class="content_tbl_row1">&nbsp;</td>
                            <td class="content_tbl_row2">&nbsp;</td>
                            <td class="content_tbl_row3">&nbsp;</td>
                            <td class="content_tbl_row1">&nbsp;</td>
                            <td class="content_tbl_row2">&nbsp;</td>
                            <td class="content_tbl_row4">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="6" style="padding-bottom:5px"><b>{LANG_USE_THIS_AS_TEMPLATE}</b></td>
                        </tr>
                        <tr>
                            <td>{LANG_GENERIC_NAME}</td>
                            <td><input title="{LANG_GENERIC_NAME}" type="text" name="tfValue11" id="tfValue11" value="{DAT_NAME}"></td>
                            <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('contact','genericname','all','Info');" class="infobutton_1"></td>
                        </tr>
                        <tr {RESTRICT_GROUP_ADMIN}>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr {RESTRICT_GROUP_ADMIN}>
                            <td colspan="6" style="padding-bottom:5px"><b>{LANG_OBJECT_ACCESS_RESTRICTIONS}</b></td>
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
			</div>
		</div>
	</form>
    <!--suppress JSUnusedLocalSymbols -->
    <script type="text/javascript">
        <!--
        (function() {
            let tabView = new YAHOO.widget.TabView('contacts');
        })();
        //-->
    </script>

<br>    
</div>    
<span id="rel_text" class="{RELATION_CLASS}"><a href="javascript:showRelationData(1)" style="color:#00F">[{LANG_SHOW_RELATION_DATA}]</a></span><span id="rel_info" class="elementHide"><a href="javascript:showRelationData(0)" style="color:#00F">[{LANG_HIDE_RELATION_DATA}]</a>{CHECK_MUST_DATA}</span>
<div id="mutdialogvalue1">
    <div id="mutdialogvalue1content" class="bd"></div>
</div>
<div id="mutdialogvalue2">
    <div id="mutdialogvalue2content" class="bd"></div>
</div>
<div id="mutdialogvalue3">
    <div id="mutdialogvalue3content" class="bd"></div>
</div>
<div id="msgcontainer"></div>
<div id="confirmcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->