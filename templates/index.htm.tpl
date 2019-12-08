<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project    : NagiosQL -->
<!-- Component  : index template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN main -->
    <div id="login">
        <p><a href="{ACTION_INSERT}"><img src="{IMAGE_PATH}nagiosql_logo.png" alt="NagiosQL" title='NagiosQL' border="0"></a></p>
        <h1 style="text-align:center">{TITLE_LOGIN}</h1>
        <form name="frmPassword" method="post" action="{ACTION_INSERT}">
            <table border="0" cellpadding="0" cellspacing="1" class="content_formtable" style="margin:0 auto;">
                <tr>
                    <td style="padding-right:10px;">{USERNAME}: </td>
                    <td><input title="{USERNAME}" type="text" name="tfUsername"></td>
                </tr><tr>
                    <td style="padding-right:10px;">{PASSWORD}: </td>
                    <td><input title="{PASSWORD}" type="password" name="tfPassword"></td>
                </tr><tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" name="Submit" value="{LOGIN}"></td>
                </tr>
            </table>
        </form>
        <div class="redmessage"><p>{MESSAGE}</p></div>
        <div id="login-text">
            <p>{LOGIN_TEXT}</p>
        </div>
    </div>
<!-- END main -->