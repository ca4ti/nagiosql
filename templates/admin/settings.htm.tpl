<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : settings administration template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN settingssite -->
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
                document.frmSettings.submit();
                document.frmSettings.subForm.disabled = true;
            }
        }
        // Check form entries
        function checkForm() {
            // Are all required fields filled in?
            const fields1 = "selValue1,tfValue1,tfValue3,tfValue4,tfValue5,tfValue6";
            const fields2 = "tfValue10";
            const msg = "{FILL_ALLFIELDS}";
            const header = "{LANG_FORMCHECK}";
            const form = document.frmSettings;
            let check = checkfields(fields1,form,myFocusObject);
            if (check === false) {
                msginit(msg,header,1);
                return false;
            }
            // Check if Updatecheck is enabled
            let boxes = document.getElementsByName("radValue2");
            let checkedUpd = 0;
            for(let i=0; i<boxes.length; i++){
                if(boxes[i].checked){
                    checkedUpd = i;
                    break; // No need to check the rest since only one can be checked.
                }  
            }
            if (checkedUpd === 0) {
                boxes = document.getElementsByName("radValue3");
                let checkedProxy = 0;
                for(let i=0; i<boxes.length; i++){
                    if(boxes[i].checked){
                        checkedProxy = i;
                        break; // No need to check the rest since only one can be checked.
                    }  
                }
                if (checkedProxy === 0) {
                    check = checkfields(fields2,form,myFocusObject);
                    if (check === false) {
                        msginit(msg,header,1);
                        return false;
                    }					
                }
            }
        }
        // Enable hidden fields
        function showFields(name,key) {
            if (name === 'radValue2') {
                if (key === '0') {
                    document.getElementById('Proxy').className 	     = "elementHide";
                    document.getElementById('ProxyServer').className = "elementHide";
                    document.getElementById('ProxyUser').className   = "elementHide";
                    document.getElementById('ProxyPasswd').className = "elementHide";
                } else {
                    let boxes = document.getElementsByName("radValue3");
                    let checkedBox = 0;
                    for(let i=0; i<boxes.length; i++){
                        if(boxes[i].checked){
                            checkedBox = i;
                            break; // No need to check the rest since only one can be checked.
                        }
                    }
                    if (checkedBox === 0) {
                        document.getElementById('Proxy').className       = "elementShow";
                        document.getElementById('ProxyServer').className = "elementShow";
                        document.getElementById('ProxyUser').className   = "elementShow";
                        document.getElementById('ProxyPasswd').className = "elementShow";
                    } else {
                        document.getElementById('Proxy').className       = "elementHide";
                        document.getElementById('ProxyServer').className = "elementHide";
                        document.getElementById('ProxyUser').className   = "elementHide";
                        document.getElementById('ProxyPasswd').className = "elementHide";
                    }
                }
            } else if (name === 'radValue3') {
                if (key === '0') {
                    document.getElementById('Proxy').className       = "elementShow";
                    document.getElementById('ProxyServer').className = "elementHide";
                    document.getElementById('ProxyUser').className   = "elementHide";
                    document.getElementById('ProxyPasswd').className = "elementHide";
                } else {
                    document.getElementById('Proxy').className       = "elementShow";
                    document.getElementById('ProxyServer').className = "elementShow";	
                    document.getElementById('ProxyUser').className   = "elementShow";
                    document.getElementById('ProxyPasswd').className = "elementShow";
                }
            }				
        }
        //-->
    </script>
    <form name="frmSettings" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td colspan="3"><b>{PATH}</b></td>
            </tr>
            <tr>
                <td class="content_tbl_row1" style="width:250px;">{TEMPDIR_NAME} *</td>
                <td class="content_tbl_row2"><input title="{TEMPDIR_NAME}" type='text' name='tfValue1' id='tfValue1' value='{TEMPDIR_VALUE}' class="inpmust"></td>
                <td class="content_tbl_row3"><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','txtTempdir','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{PROTOCOL_NAME} *</td>
                <td>
                	<select title="{PROTOCOL_NAME}" name="selValue1" class="selectbordermust inpmust">
                    	<option class="inpmust" value="1" {HTTP_SELECTED}>http</option>
                    	<option class="inpmust" value="2" {HTTPS_SELECTED}>https</option>
                	</select>
		</td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','selProtocol','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><b>{DATA}</b></td>
            </tr>
            <tr>
                <td>{LOCALE}</td>
                <td>
                    <select title="{LOCALE}" name="selValue2" class="selectborder">
<!-- BEGIN language -->
                        <option value="{LANGUAGE_ID}" {LANGUAGE_SELECTED}>{LANGUAGE_NAME}</option>
<!-- END language -->
                    </select>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','selLanguage','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{ENCODING_NAME}</td>
                <td><input title="{ENCODING_NAME}" name='tfValue2' type='text' id='tfValue2' value='{ENCODING_VALUE}'></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','txtEncoding','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><b>{DB}</b></td>
            </tr>
            <tr>
                <td>{SERVER_NAME} *</td>
                <td><input title="{SERVER_NAME}" name='tfValue3' type='text' id='tfValue3' value='{SERVER_VALUE}' class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','txtDBserver','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{SERVER_PORT} *</td>
                <td><input title="{SERVER_PORT}" name='tfValue4' type='text' id='tfValue4' value='{PORT_VALUE}' class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','txtDBport','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{DATABASE_NAME} *</td>
                <td><input title="{DATABASE_NAME}" name='tfValue5' type='text' id='tfValue5' value='{DATABASE_VALUE}' class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','txtDBname','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{USERNAME_NAME} *</td>
                <td><input title="{USERNAME_NAME}" name='tfValue6' type='text' id='tfValue6' value='{USERNAME_VALUE}' class="inpmust"></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','txtDBuser','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{PASSWORD_NAME}</td>
                <td><input title="{PASSWORD_NAME}" name='tfValue7' type='password' id='tfValue7' value='{PASSWORD_VALUE}'></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','txtDBpass','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><b>{SECURITY}</b></td>
            </tr>
            <tr>
                <td>{LOGOFFTIME_NAME}</td>
                <td><input title="{LOGOFFTIME_NAME}" name='tfValue8' type='text' id='tfValue8' value='{LOGOFFTIME_VALUE}'></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','txtLogoff','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{WSAUTH_NAME} *</td>
                <td>
                	<select title="{WSAUTH_NAME}" name="selValue3" class="selectbordermust inpmust">
                		<option class="inpmust" value="0" {WSAUTH_0_SELECTED}>NagiosQL</option>
                        <option class="inpmust" value="1" {WSAUTH_1_SELECTED}>Apache</option>
                	</select>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','selWSAuth','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><b>{COMMON}</b></td>
            </tr>
            <tr>
                <td>{PAGELINES_NAME}</td>
                <td><input title="{PAGELINES_NAME}" name='tfValue9' type='text' id='tfValue9' value='{PAGELINES_VALUE}'></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','txtLines','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{SELDISABLE_NAME}</td>
                <td>
                	<select title="{SELDISABLE_NAME}" name="selValue4" class="selectborder">
                		<option value="1" {SELDISABLE_1_SELECTED}>NagiosQL 3</option>
                        <option value="0" {SELDISABLE_0_SELECTED}>NagiosQL 2</option>
                	</select>
				</td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','selSeldisable','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{TEMPLATE_CHECK}</td>
                <td>
                  <input title="{TEMPLATE_CHECK} {LANG_ENABLE}" type="radio" name="radValue1" value="1" {TPL_CHECK_1_CHECKED}><div style="float:left; padding: 3px 8px 3px 3px;">{LANG_ENABLE}</div>
                  <input title="{TEMPLATE_CHECK} {LANG_DISABLE}" type="radio" name="radValue1" value="0" {TPL_CHECK_0_CHECKED}><div style="float:left; padding:3px;">{LANG_DISABLE}</div>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','templatecheck','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td>{UPDATE_CHECK}</td>
                <td>
                	<input title="{UPDATE_CHECK} {LANG_ENABLE}" type="radio" name="radValue2" value="1" {UPD_CHECK_1_CHECKED}><div style="float:left;padding: 3px 8px 3px 3px;">{LANG_ENABLE}</div>
                  	<input title="{UPDATE_CHECK} {LANG_DISABLE}" type="radio" name="radValue2" value="0" {UPD_CHECK_0_CHECKED}><div style="float:left;padding:3px;">{LANG_DISABLE}</div>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','updatecheck','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr id="Proxy" class="{CLASS_NAME_1}">
                <td>{UPD_PROXY_CHECK}</td>
                <td>
                  <input title="{UPD_PROXY_CHECK} {LANG_ENABLE}" type="radio" name="radValue3" value="1" {UPD_PROXY_1_CHECKED} onClick="showFields(this.name,this.value);"><div style="float:left; padding: 3px 8px 3px 3px;">{LANG_ENABLE}</div>
                  <input title="{UPD_PROXY_CHECK} {LANG_DISABLE}" type="radio" name="radValue3" value="0" {UPD_PROXY_0_CHECKED} onClick="showFields(this.name,this.value);"><div style="float:left; padding:3px;">{LANG_DISABLE}</div>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','chkUpdProxy','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr id="ProxyServer" class="{CLASS_NAME_2}">
                <td>{UPD_PROXY_SERVER} *</td>
                <td><input title="{UPD_PROXY_SERVER}" name='tfValue10' class="inpmust" type='text' id='tfValue10' value='{UPD_PROXY_SERVER_VALUE}'></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','txtProxyServer','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr id="ProxyUser" class="{CLASS_NAME_2}">
                <td>{UPD_PROXY_USERNAME}</td>
                <td><input title="{UPD_PROXY_USERNAME}" name='tfValue11' type='text' id='tfValue11' value='{UPD_PROXY_USERNAME_VALUE}'></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','txtProxyUser','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr id="ProxyPasswd" class="{CLASS_NAME_2}">
                <td>{UPD_PROXY_PASSWORD}</td>
                <td><input title="{UPD_PROXY_PASSWORD}" name='tfValue12' type='password' id='tfValue12' value='{UPD_PROXY_PASSWORD_VALUE}'></td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','txtProxyPasswd','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><b>{PERFORMANCE}</b></td>
            </tr>
            <tr>
                <td>{SHOW_PARENTS}</td>
                <td>
                    <input title="{SHOW_PARENTS} {LANG_ENABLE}" type="radio" name="radValue4" value="1" {PAR_CHECK_1_CHECKED}><div style="float:left; padding: 3px 8px 3px 3px;">{LANG_ENABLE}</div>
                    <input title="{SHOW_PARENTS} {LANG_DISABLE}" type="radio" name="radValue4" value="0" {PAR_CHECK_0_CHECKED}><div style="float:left; padding:3px;">{LANG_DISABLE}</div>
                </td>
                <td><img src="{IMAGE_PATH}tip.gif" alt="{LANG_HELP}" title="{LANG_HELP}" width="18" height="18" onclick="dialoginit('settings','show_parents','all','Info');" class="infobutton_1"></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><input name="subForm" type="button" id="subForm" value="{LANG_SAVE}" onClick="LockButton();" {ADD_CONTROL}>&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{LANG_ABORT}"><span class="required_info">* {LANG_REQUIRED}</span></td>
            </tr>
        </table>
	</form>
    <br>
    <p><span class="redmessage">{ERRORMESSAGE}</span><span class="greenmessage">{INFOMESSAGE}</span></p>
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END settingssite -->