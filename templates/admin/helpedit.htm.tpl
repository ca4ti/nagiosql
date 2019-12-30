<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : nagios config template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN helpedit -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <script src="{BASE_PATH}functions/tinyMCE/jscripts/tiny_mce/tiny_mce.js"></script>
    <script type="text/javascript">
        tinyMCE.init({	
            // General options
            mode : "textareas",
            theme : "advanced",
            skin : "o2k7",
            plugins : "safari,table,searchreplace,contextmenu",

            // Theme options
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect",
            theme_advanced_buttons2 : "cut,copy,paste,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,help,code,|,forecolor,backcolor",
            theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_path : false,
            theme_advanced_resizing : true
        });
    </script>
    <script type="text/javascript">
        <!--
        // Interrupt input
    	function abort() {
            this.location.href = "{MAINSITE}";
    	}
    	// Send form
        function LockButton() {
            document.frmHelpEdit.tfValue3.value = "1";
            document.frmHelpEdit.submit();
            document.frmHelpEdit.subForm.disabled = true;
    	}
        // Refresh page
        function reloadPage() {
            document.frmHelpEdit.tfValue3.value = "0";
            document.frmHelpEdit.submit();
            document.frmHelpEdit.subForm.disabled = true;
        }
    	//-->
  	</script>
  	<form name="frmHelpEdit" method="post" action="{ACTION_INSERT}">
            <table border="0" cellpadding="0" class="content_formtable">
                <tr>
                    <td class="content_tbl_row1">{INFOKEY_1}</td>
                    <td>
                        <select title="{INFOKEY_1}" name="selInfoKey1" id="selInfoKey1" class="selectborder" onChange="reloadPage();">
                            <option value="">&nbsp;</option>
<!-- BEGIN infokey1 -->
                            <option value="{INFOKEY_1_VAL}" {INFOKEY_1_SEL}>{INFOKEY_1_VAL}</option>
<!-- END infokey1 -->        	
                        </select><input type="hidden" name="tfValue1" value="{INFOKEY_1_SEL_VAL}">
                    </td>
                </tr>
                <tr>
                    <td>{INFOKEY_2}</td>
                    <td>
                        <select title="{INFOKEY_2}" name="selInfoKey2" id="selInfoKey2" class="selectborder" onChange="reloadPage();">
                            <option value="">&nbsp;</option>
<!-- BEGIN infokey2 -->
                            <option value="{INFOKEY_2_VAL}" {INFOKEY_2_SEL}>{INFOKEY_2_VAL}</option>
<!-- END infokey2 -->        	
                        </select><input type="hidden" name="tfValue2" value="{INFOKEY_2_SEL_VAL}">
                    </td>
                </tr>
                <tr>
                    <td>{INFO_VERSION}</td>
                    <td>
                        <select title="{INFO_VERSION}" name="selInfoVersion" id="selInfoVersion" class="selectborder" onChange="reloadPage();">
                            <option value="">&nbsp;</option>
    <!-- BEGIN infoversion -->
                            <option value="{INFOVERSION_2_VAL}" {INFOVERSION_2_SEL}>{INFOVERSION_2_VAL}</option>
    <!-- END infoversion -->        	
                        </select><input type="hidden" name="hidVersion" value="{INFOVERSION_2_SEL_VAL}">
                    </td>
                </tr>
                <tr>
                    <td>{LOAD_DEFAULT}</td>
                    <td><input title="{LOAD_DEFAULT}" type="checkbox" name="chbValue1" id="chbValue1" value="1" onClick="reloadPage();" {DEFAULT_CHECKED}></td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2"><textarea title="" name="taFileText" cols="80" rows="20" id="taFileText">{DAT_HELPTEXT}</textarea></td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2"><input name="subForm" type="button" id="subForm" value="{LANG_SAVE}" onClick="LockButton();" {ADD_CONTROL}>&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{LANG_ABORT}">
                    <input name="tfValue3" type="hidden" id="tfValue3" value="0"></td>
                </tr>
            </table>
	</form>
    <p><span class="redmessage">{ERRORMESSAGE}</span><span class="greenmessage">{INFOMESSAGE}</span></p>
</div>
<div id="msgcontainer"></div>
<!-- END helpedit -->