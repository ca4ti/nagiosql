<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : nagios config template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN naginsert -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <script type="text/javascript">
    	<!--
        // Interrupt input
        function abort() {
            this.location.href = "{MAINSITE}";
        }
    	// Send form
        /**
         * @return {boolean}
         */
        function LockButton() {
            if (checkForm() === false) {
                return false;
            } else {
                document.frmNagiosConfig.submit();
                document.frmNagiosConfig.subForm.disabled = true;
            }
    	}
    	// Check form entries
    	function checkForm() {
            // Are all required fields filled in?
            const fields1 = "taFileText";
            const msg1 = "{FILL_ALLFIELDS}";
            const header = "{LANG_FORMCHECK}";
            const form = document.frmNagiosConfig;
            let check = checkfields(fields1,form,myFocusObject);
            if (check === false) {
                msginit(msg1,header,1);
                return false;
            }	
    	}
    	//-->
    </script>
    <form name="frmNagiosConfig" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td><textarea title="{TITLE}" name="taFileText" cols="100" rows="20" id="taFileText">{DAT_NAGIOS_CONFIG}</textarea></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><input name="subForm" type="button" id="subForm" value="{LANG_SAVE}" onClick="LockButton();" {ADD_CONTROL}>&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{LANG_ABORT}">
                    <input name="modus" type="hidden" id="modus" value="{MODUS}"></td>
            </tr>
        </table>
    </form>
    <br>
    <p><span class="redmessage">{ERRORMESSAGE}</span><span class="greenmessage">{INFOMESSAGE}</span></p>
</div>
<div id="msgcontainer"></div>
<!-- END naginsert -->