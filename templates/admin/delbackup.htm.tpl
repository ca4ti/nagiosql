<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : backup file deletion template -->
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
            document.frmDeleteFile.hidStatus.value  = 1;
            document.frmDeleteFile.submit();
            document.frmDeleteFile.subForm.disabled = true;
        }
        function del(key) {
            if (key === "search") {
                document.frmDeleteFile.txtSearch.value = "";
                document.frmDeleteFile.submit();
            }
        }
        //-->
    </script>
    <form name="frmDeleteFile" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1">{LANG_SEARCH_STRING}</td>
                <td class="content_tbl_row2"><input title="{LANG_SEARCH_STRING}" type="text" name="txtSearch" value="{DAT_SEARCH}"></td>
                <td style="width:490px;"><img src="{IMAGE_PATH}lupe.gif" width="18" height="18" alt="{LANG_SEARCH}" title="{LANG_SEARCH}" style="cursor:pointer;" onClick="document.frmDeleteFile.submit();">&nbsp;<img src="{IMAGE_PATH}del.png" width="18" height="18" alt="{LANG_DELETE_SEARCH}" title="{LANG_DELETE_SEARCH}" onClick="del('search');" style="cursor:pointer;"></td>
            </tr>
            <tr>
                <td valign="top">{BACKUPFILE} *</td>
                <td rowspan="2" colspan="2">
                    <select title="{BACKUPFILE}" name="mselValue1[]" size="10" multiple id="mselValue1" class="selectborder" style="width:500px;">
<!-- BEGIN filelist -->
                        <option value="{DAT_BACKUPFILE}">{DAT_BACKUPFILE}</option>
<!-- END filelist -->
                    </select></td>
            </tr>
            <tr>
                <td valign="top"><small>{CTRL_INFO}</small></td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><input name="subForm" type="button" id="subForm" value="{MAKE}" onClick="LockButton();" {ADD_CONTROL}>&nbsp;<input name="subAbort" type="button" id="subAbort" onClick="abort();" value="{ABORT}">
                    <input name="hidStatus" type="hidden" id="hidStatus" value="0"><span class="required_info">* {LANG_REQUIRED}</span></td>
            </tr>
        </table>
    </form>
    <br>
    <p><span class="redmessage">{ERRORMESSAGE}</span><span class="greenmessage">{INFOMESSAGE}</span></p>
</div>
<!-- END main -->