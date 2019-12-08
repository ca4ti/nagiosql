<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : timeperiod template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datainsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <script type="text/JavaScript">
        <!--
        // Initialize change dialog
        openMutDlgInit("mselValue1","mutdialogvalue1","{LANG_MODIFY_SELECTION}: {LANG_EXCLUDE}","mutvalue1","{LANG_SAVE}","{LANG_ABORT}","1");
        openMutDlgInit("mselValue2","mutdialogvalue2","{LANG_MODIFY_SELECTION}: {LANG_INCLUDE}","mutvalue2","{LANG_SAVE}","{LANG_ABORT}","1");
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
            const fields1 = "tfValue1,tfValue2";
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
                msginit(msg2+" {LANG_TIME_PERIOD}",header,1);
                form.tfValue1.focus();
                return false;
            }
        }
        // Insert time definitions
        function insertDefintion() {
            let txtDef;
            let txtRange;
            if (document.frmDetail.hidVersion.value >= 3) {
                txtDef   = document.frmDetail.txtTimedefinition.value;
                txtRange = document.frmDetail.txtTimerange2.value;
            } else {
                txtDef   = document.frmDetail.selTimedefinition.value;
                txtRange = document.frmDetail.txtTimerange1.value;
            }
            if ((txtDef === "") || (txtRange === "")) {
                const header = "{LANG_FORMCHECK}";
                msginit("{LANG_INSERT_ALL_TIMERANGE}",header,1);
                return false;
            }
            document.getElementById("timeframe").src = "{BASE_PATH}admin/timedefinitions.php?tipId={id}&version={NAGIOS_VERSION}&mode=add&def="+encodeURIComponent(txtDef)+"&range="+encodeURIComponent(txtRange);
        }
        //-->
    </script>
    <form name="frmDetail" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1">{LANG_TIME_PERIOD} *</td>
                <td class="content_tbl_row2"><input title="{LANG_TIME_PERIOD}" name="tfValue1" type="text" id="tfValue1" value="{DAT_TIMEPERIOD_NAME}" class="inpmust"></td>
                <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('timeperiod','timeperiod_name','all','Info');" class="infobutton_1"></td>
                <td class="content_tbl_row1" valign="top"><span class="{VERSION_30_VISIBLE}">{LANG_EXCLUDE}</span></td>
                <td class="content_tbl_row2" rowspan="4" valign="top">
                    <table cellpadding="0" cellspacing="0" border="0" id="ex30a" class="{VERSION_30_VISIBLE}">
                        <tr>
                            <td>
                                <select title="{LANG_EXCLUDE}" name="mselValue1[]" size="5" multiple id="mselValue1" class="selectborder" {MSIE_DISABLED}>
                                    <!-- BEGIN excludes -->
                                    <option value="{DAT_EXCLUDES_ID}" class="empty_class {SPECIAL_STYLE} {IE_EXCLUDES_SEL}" {DAT_EXCLUDES_SEL} {OPTION_DISABLED}>{DAT_EXCLUDES}</option>
                                    <!-- END excludes -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="content_tbl_row4" rowspan="4" valign="top"><span class="{VERSION_30_VISIBLE}"><img id="mutvalue1" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('timeperiod','exclude','3','Info');" class="infobutton_2"></span></td>
            </tr>
            <tr>
                <td>{LANG_DESCRIPTION} *</td>
                <td><input title="{LANG_DESCRIPTION} " name="tfValue2" type="text" id="tfValue2" value="{DAT_ALIAS}" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('timeperiod','alias','all','Info');" class="infobutton_1"></td>
                <td rowspan="2"><small>{LANG_CTRLINFO}</small></td>
            </tr>
            <tr class="{VERSION_30_VISIBLE}">
                <td>{LANG_TPLNAME}</td>
                <td><input title="{LANG_TPLNAME}" name="tfValue3" type="text" id="tfValue3" value="{DAT_NAME}"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('timeperiod','name','all','Info');" class="infobutton_1"></td>
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
                <td>{LANG_REGISTERED}</td>
                <td><input title="{LANG_REGISTERED}" name="chbRegister" type="checkbox" class="checkbox" id="chbRegister" value="1" {REG_CHECKED}></td>
                <td colspan="2"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','registered','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_ACTIVE}</td>
                <td colspan="2"><input title="{LANG_ACTIVE}" name="chbActive" type="checkbox" class="checkbox" id="chbActive" value="1" {ACT_CHECKED} {ACT_DISABLED}>
                    <input name="hidActive" type="hidden" id="hidActive" value="{ACTIVE}">
                    <input name="modus" type="hidden" id="modus" value="{MODUS}">
                    <input name="hidId" type="hidden" id="hidId" value="{DAT_ID}">
                    <input name="hidLimit" type="hidden" id="hidLimit" value="{LIMIT}">
                    <input type="hidden" name="hidVersion" value="{NAGIOS_VERSION}"></td>
                <td valign="top"><span class="{VERSION_30_VISIBLE}">{LANG_INCLUDE}</span></td>
                <td rowspan="4" valign="top">
                    <table cellpadding="0" cellspacing="0" border="0" class="{VERSION_30_VISIBLE}">
                        <tr>
                            <td>
                                <select title="{LANG_INCLUDE}" name="mselValue2[]" size="5" multiple id="mselValue2" class="selectborder" {MSIE_DISABLED}>
                                    <!-- BEGIN uses -->
                                    <option value="{DAT_USES_ID}" class="empty_class {SPECIAL_STYLE} {IE_USES_SEL}" {DAT_USES_SEL} {OPTION_DISABLED}>{DAT_USES}</option>
                                    <!-- END uses -->
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td rowspan="4" valign="top"><span class="{VERSION_30_VISIBLE}"><img id="mutvalue2" src="{IMAGE_PATH}mut.gif" width="24" height="24" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" style="cursor:pointer"><br><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('timeperiod','include','3','Info');" class="infobutton_2"></span></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
                <td rowspan="2"><small>{LANG_CTRLINFO}</small></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4" style="padding-bottom:5px;padding-left:5px;"><b>{LANG_TIME_DEFINITIONS}</b></td>
            </tr>
            <tr>
                <td colspan="6" style="padding-bottom:2px">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td style="width:235px;padding-left:5px;"><i>{LANG_TIME_DEFINITION}</i></td>
                            <td style="width:260px;padding-left:5px;"><i>{LANG_TIME_RANGE}</i></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="6" style="padding-bottom:10px"><iframe id="timeframe" frameborder="0" src="{BASE_PATH}admin/timedefinitions.php?tipId={TIP_ID}&amp;version={NAGIOS_VERSION}" style="width:540px;height:150px;border:1px solid #000000"></iframe></td>
            </tr>
            <tr class="{VERSION_20_VISIBLE}">
                <td>{LANG_WEEKDAY}</td>
                <td>
                    <select title="{LANG_WEEKDAY}" name="selTimedefinition" id="selTimedefinition" class="selectborder">
                        <option value="monday">{LANG_MONDAY}</option>
                        <option value="tuesday">{LANG_TUESDAY}</option>
                        <option value="wednesday">{LANG_WEDNESDAY}</option>
                        <option value="thursday">{LANG_THURSDAY}</option>
                        <option value="friday">{LANG_FRIDAY}</option>
                        <option value="saturday">{LANG_SATURDAY}</option>
                        <option value="sunday">{LANG_SUNDAY}</option>
                    </select>
                </td>
                <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('timeperiod','weekday','2','Info');" class="infobutton_1"></td>
            </tr>
            <tr class="{VERSION_20_VISIBLE}">
                <td>{LANG_TIME_RANGE}</td>
                <td><input title="{LANG_TIME_RANGE}" type="text" name="txtTimerange1" id="txtTimerange1"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('timeperiod','exception','2','Info');" class="infobutton_1"></td>
                <td colspan="3"><input type="button" name="butTimeDefinition"  value="{LANG_INSERT}" onClick="insertDefintion();"></td>
            </tr>
            <tr class="{VERSION_30_VISIBLE}">
                <td>{LANG_TIME_DEFINITION}</td>
                <td><input title="{LANG_TIME_DEFINITION}" type="text" name="txtTimedefinition" id="txtTimedefinition"></td>
                <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('timeperiod','weekday','3','Info');" class="infobutton_1"></td>
            </tr>
            <tr class="{VERSION_30_VISIBLE}">
                <td>{LANG_TIME_RANGE}</td>
                <td><input title="{LANG_TIME_RANGE}" type="text" name="txtTimerange2" id="txtTimerange2"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('timeperiod','timerange','3','Info');" class="infobutton_1"></td>
                <td colspan="3"><input type="button" name="butTimeDefinition" value="{LANG_INSERT}" onClick="insertDefintion();"></td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><input name="subForm" type="button" id="subForm" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE}>&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span></td>
                <td colspan="3"><span class="redmessage">{WARNING}</span></td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
        </table>
    </form>
    <span id="rel_text" class="{RELATION_CLASS}"><a href="javascript:showRelationData(1)" style="color:#00F">[{LANG_SHOW_RELATION_DATA}]</a></span><span id="rel_info" class="elementHide"><a href="javascript:showRelationData(0)" style="color:#00F">[{LANG_HIDE_RELATION_DATA}]</a>{CHECK_MUST_DATA}</span>
</div>
<div id="mutdialogvalue1">
    <div id="mutdialogvalue1content" class="bd"></div>
</div>
<div id="mutdialogvalue2">
    <div id="mutdialogvalue2content" class="bd"></div>
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->