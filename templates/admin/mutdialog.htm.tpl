<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : mutation dialog template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN header -->
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title>none</title>
    <link href="{BASE_PATH}config/main.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td>
<!-- END header -->
<!-- BEGIN datainsert -->
                <div id="content_main">
                <form name="frmDataInsert_{OPENER_FIELD}" method="post" action="{ACTION_INSERT}">
                    <table border="0" cellpadding="0">
                        <tr>
                            <td colspan="2" valign="top">
                                <table border="0" class="content_formtable" style="top:0;">
                                    <tr>
                                        <td>{AVAILABLE}</td>
                                        <td>&nbsp;</td>
                                        <td>{SELECTED}</td>
                                    </tr>
                                    <tr>
                                        <td rowspan="3">
                                            <select title="{AVAILABLE}" name="{OPENER_FIELD}Avail[]" size="12" multiple id="{OPENER_FIELD}Avail" class="selectborder" style="width:300px; height:200px;">
                                            </select></td>
                                        <td style="width:75px;">&nbsp;</td>
                                        <td rowspan="3">
                                            <select title="{SELECTED}" name="{OPENER_FIELD}Selected[]" size="12" multiple id="{OPENER_FIELD}Selected" class="selectborder" style="width:300px; height:200px;">
                                            </select></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:center;" valign="middle"><img src="{IMAGE_PATH}pfeil_l.gif" height="21" width="40" alt="<-" onClick="desValue('{OPENER_FIELD}');" style="cursor:pointer"><br><br><img src="{IMAGE_PATH}pfeil_r.gif" height="21" width="40" alt="->" onClick="selValue('{OPENER_FIELD}');" style="cursor:pointer">{DISABLE_HTML_BEGIN}<br><br><img src="{IMAGE_PATH}exclude_01.gif" height="21" width="40" alt="!->" onClick="selValueEx('{OPENER_FIELD}');" style="cursor:pointer">{DISABLE_HTML_END}</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </form>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
<!-- END datainsert -->