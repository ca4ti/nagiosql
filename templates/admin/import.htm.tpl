<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project    : NagiosQL -->
<!-- Component  : nagios config template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN main -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <script type="text/javascript">
        <!--
        // Interrupt input
    	function abort() {
            this.location.href = "{ACTION_INSERT}";
    	}
    	// Send form
    	function LockButton() {
            document.frmImport.hidStatus.value  = 1;
            document.frmImport.submit();
            document.frmImport.subForm.disabled = true;
    	}
        function del(key) {
            if (key === "search") {
                document.frmImport.txtSearch.value = "";
                document.frmImport.submit();
            }
        }
    	//-->
    </script>
    <form action="{ACTION_INSERT}" method="post" enctype="multipart/form-data" name="frmImport">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1">{LANG_SEARCH_STRING}</td>
                <td class="content_tbl_row2"><input title="{LANG_SEARCH_STRING}" type="text" name="txtSearch" value="{DAT_SEARCH}"></td>
                <td style="width:490px;"><img src="{IMAGE_PATH}lupe.gif" width="18" height="18" alt="{LANG_SEARCH}" title="{LANG_SEARCH}" style="cursor:pointer;" onClick="document.frmImport.submit();">&nbsp;<img src="{IMAGE_PATH}del.png" width="18" height="18" alt="{LANG_DELETE_SEARCH}" title="{LANG_DELETE_SEARCH}" onClick="del('search');" style="cursor:pointer;"></td>
            </tr>
            <tr>
                <td valign="top">{IMPORTFILE}</td>
                <td rowspan="2" colspan="2">
                    <select title="{IMPORTFILE}" name="mselValue1[]" size="10" multiple id="mselValue1" class="selectborder" style="width:500px;">
<!-- BEGIN filelist2 -->
                        <option value="{DAT_IMPORTFILE_2}">{DAT_IMPORTFILE_2}</option>
<!-- END filelist2 -->
                    </select></td>
            </tr>
            <tr>
                <td valign="top"><small>{CTRL_INFO}</small></td>
            </tr>
            <tr>
                <td>{LOCAL_FILE}</td>
                <td colspan="2"><input type="file" name="datValue1" id="datValue1" size="70"></td>
            </tr>
            <tr>
                <td>{OVERWRITE}</td>
                <td colspan="2"><input title="{OVERWRITE}" name="chbValue1" type="checkbox" class="checkbox" id="chbValue1" value="1" checked></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><input name="subForm" type="button" id="subForm" value="{MAKE}" onClick="LockButton();" {ADD_CONTROL}>&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{ABORT}"><input name="hidStatus" type="hidden" id="hidStatus" value="0"></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
        </table>
	</form>
    <p>{IMPORT_INFO_1}<span style="color:#FF0000">{IMPORT_INFO_2}</span></p>
	<p><span class="redmessage">{ERRORMESSAGE}</span><span class="greenmessage">{INFOMESSAGE}</span></p>
</div>
<!-- END main -->