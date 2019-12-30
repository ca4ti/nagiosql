<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : Menu access template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN menuaccesssite -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <script type="text/javascript">
        <!--
        // Update form
        function update() {
            document.frmMenuAccess.submit();
        }
        //-->
    </script>
    <form name="frmMenuAccess" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1">{LANG_MENU_PAGE}</td>
                <td style="width:500px;">
                    <select title="{LANG_MENU_PAGE}" name="selValue1" onChange="update();" class="selectborder">
                        <!-- BEGIN submenu -->
                        <option value="{SUBMENU_VALUE}" {SUBMENU_SELECTED}>{SUBMENU_NAME}</option>
                        <!-- END submenu -->
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td>{LANG_ACCESS_GROUP}</td>
                <td>
                    <select title="{LANG_ACCESS_GROUP}" name="selValue2" class="selectborder">
                        <!-- BEGIN acc_group -->
                        <option value="{DAT_ACC_GROUP_ID}" {DAT_ACC_GROUP_SEL}>{DAT_ACC_GROUP}</option>
                        <!-- END acc_group -->
                    </select></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">{LANG_ACCESSDESCRIPTION}</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2"><input name="subSave" type="submit" value="{LANG_SAVE}" {DISABLE_SAVE}></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
        </table>
    </form>
    <p><span class="redmessage">{ERRORMESSAGE}</span><span class="greenmessage">{INFOMESSAGE}</span></p>
</div>
<!-- END menuaccesssite -->