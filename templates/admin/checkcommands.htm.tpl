<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : command administration template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datainsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <script type="text/JavaScript">
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
                document.frmDetail.submit();
                document.frmDetail.subForm.disabled = true;
            }
    	}
    	// Check form entries
    	function checkForm() {
            // Are all required fields filled in?
            const fields1 = "tfValue1,tfSpValue1";
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
                msginit(msg2+" {LANG_COMMAND}",header,1);
                form.tfValue1.focus();
                return false;
            }
        }
    	//-->
    </script>
    <form name="frmDetail" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1">{LANG_COMMAND} *</td>
                <td class="content_tbl_row2"><input title="{LANG_COMMAND}" name="tfValue1" type="text" id="tfValue1" value="{DAT_COMMAND_NAME}" class="inpmust"></td>
                <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('command','command_name','all','Info');" class="infobutton_1"></td>
                <td class="content_tbl_row1">&nbsp;</td>
                <td class="content_tbl_row2">&nbsp;</td>
                <td class="content_tbl_row4">&nbsp;</td>
            </tr>
            <tr>
                <td>{LANG_COMMAND_LINE} *</td>
                <td colspan="4"><input title="{LANG_COMMAND_LINE}" name="tfSpValue1" type="text" id="tfSpValue1" value="{DAT_COMMAND_LINE}" style="width:650px;" class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('command','command_line','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{LANG_COMMAND_TYPE}</td>
                <td>
                    <select title="{LANG_COMMAND_TYPE}" name="selValue1" id="selValue1" class="selectborder" onclick="location.href=">
                        <option value="0">{NO_TYPE}</option>
                        <option value="1" {CHECK_TYPE_SELECTED}>{CHECK_TYPE}</option>
                        <option value="2" {MISC_TYPE_SELECTED}>{MISC_TYPE}</option>
                    </select>
                </td>
                <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('command','command_type','all','Info');" class="infobutton_1">
                </td>
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
                <td>{LANG_REGISTERED}</td>
                <td><input title="{LANG_REGISTERED}" name="chbRegister" type="checkbox" class="checkbox" id="chbRegister" value="1" {REG_CHECKED}></td>
                <td colspan="4"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('common','registered','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td style="vertical-align: top; padding-top: 2px;">{LANG_HELP_TEXT} $ARG1$</td>
                <td><textarea title="{LANG_HELP_TEXT} $ARG1$" name="taArg1Info" id="taArg1Info" class="arginfo">{DAT_ARG1_INFO}</textarea></td>
                <td>&nbsp;</td>
                <td style="vertical-align: top; padding-top: 2px;">{LANG_HELP_TEXT} $ARG2$</td>
                <td><textarea title="{LANG_HELP_TEXT} $ARG2$" name="taArg2Info" id="taArg2Info" class="arginfo">{DAT_ARG2_INFO}</textarea></td>
            </tr>
            <tr>
                <td style="vertical-align: top; padding-top: 2px;">{LANG_HELP_TEXT} $ARG3$</td>
                <td><textarea title="{LANG_HELP_TEXT} $ARG3$" name="taArg3Info" id="taArg3Info" class="arginfo">{DAT_ARG3_INFO}</textarea></td>
                <td>&nbsp;</td>
                <td style="vertical-align: top; padding-top: 2px;">{LANG_HELP_TEXT} $ARG4$</td>
                <td><textarea title="{LANG_HELP_TEXT} $ARG4$" name="taArg4Info" id="taArg4Info" class="arginfo">{DAT_ARG4_INFO}</textarea></td>
            </tr>
            <tr>
                <td style="vertical-align: top; padding-top: 2px;">{LANG_HELP_TEXT} $ARG5$</td>
                <td><textarea title="{LANG_HELP_TEXT} $ARG5$" name="taArg5Info" id="taArg5Info" class="arginfo">{DAT_ARG5_INFO}</textarea></td>
                <td>&nbsp;</td>
                <td style="vertical-align: top; padding-top: 2px;">{LANG_HELP_TEXT} $ARG6$</td>
                <td><textarea title="{LANG_HELP_TEXT} $ARG6$" name="taArg6Info" id="taArg6Info" class="arginfo">{DAT_ARG6_INFO}</textarea></td>
            </tr>
            <tr>
                <td style="vertical-align: top; padding-top: 2px;">{LANG_HELP_TEXT} $ARG7$</td>
                <td><textarea title="{LANG_HELP_TEXT} $ARG7$" name="taArg7Info" id="taArg7Info" class="arginfo">{DAT_ARG7_INFO}</textarea></td>
                <td>&nbsp;</td>
                <td style="vertical-align: top; padding-top: 2px;">{LANG_HELP_TEXT} $ARG8$</td>
                <td><textarea title="{LANG_HELP_TEXT} $ARG8$" name="taArg8Info" id="taArg8Info" class="arginfo">{DAT_ARG8_INFO}</textarea></td>
            </tr>
            <tr>
                <td>{LANG_ACTIVE}</td>
                <td colspan="5"><input title="{LANG_ACTIVE}" name="chbActive" type="checkbox" class="checkbox" id="chbActive" value="1" {ACT_CHECKED} {ACT_DISABLED}>
                    <input name="hidActive" type="hidden" id="hidActive" value="{ACTIVE}">
                    <input name="modus" type="hidden" id="modus" value="{MODUS}">
                    <input name="hidId" type="hidden" id="hidId" value="{DAT_ID}">
                    <input name="hidLimit" type="hidden" id="hidLimit" value="{LIMIT}">
                </td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" valign="bottom"><input name="subForm" type="button" id="subForm" value="{LANG_SAVE}" onClick="LockButton();" {DISABLE_SAVE}>&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span></td>
                <td colspan="3"><span class="redmessage">{WARNING}</span></td>
            </tr>
        </table>
    </form>
    <br>    
    <span id="rel_text" class="{RELATION_CLASS}"><a href="javascript:showRelationData(1)" style="color:#00F">[{LANG_SHOW_RELATION_DATA}]</a></span><span id="rel_info" class="elementHide"><a href="javascript:showRelationData(0)" style="color:#00F">[{LANG_HIDE_RELATION_DATA}]</a>{CHECK_MUST_DATA}</span>
</div>   
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END datainsert -->