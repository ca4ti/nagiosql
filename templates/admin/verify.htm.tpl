<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : Verification template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN main -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <form name="frmImport" method="post" action="{ACTION_INSERT}">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td style="width:250px;">{WRITE_MONITORING_DATA}</td>
                <td><input name="butValue1" type="submit" id="butValue1" value="{MAKE}" {ADD_CONTROL}></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>	
            <tr>
                <td>{WRITE_ADDITIONAL_DATA}</td>
                <td><input name="butValue2" type="submit" id="butValue2" value="{MAKE}" {ADD_CONTROL}></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>	
            <tr>
                <td>{CHECK_CONFIG}</td>
                <td><input name="butValue3" type="submit" id="butValue3" value="{MAKE}"></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>	
            <tr>
                <td>{RESTART_NAGIOS}</td>
                <td><input name="butValue4" type="submit" id="butValue4" value="{MAKE}" {ADD_CONTROL}></td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>	
        </table>
	</form>
    <br>
    <p><span class="redmessage">{ERRORMESSAGE}</span><span class="greenmessage">{INFOMESSAGE}</span></p>
    <p>
<!-- BEGIN verifyline --><span class="{VERIFY_CLASS}">{VERIFY_LINE}</span><br><!-- END verifyline -->
    {DATA}</p>
</div>
<!-- END main -->