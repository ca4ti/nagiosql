<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : main template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN header -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title>{PAGETITLE}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="description" content="{META_DESCRIPTION}">
        <meta name="keywords" content="{META_KEYWORDS}">
        <meta name="robots" content="{ROBOTS}">
        <meta name="author" content="{AUTHOR}">
        <meta name="content-language" content="{LANGUAGE}">
        <meta name="publisher" content="{PUBLISHER}">{SPECIALMETA}
        <link rel="SHORTCUT ICON" href="{BASE_PATH}favicon.ico">
        <!-- YUI Components -->
        <link href="{BASE_PATH}functions/yui/build/button/assets/skins/sam/button.css" rel="stylesheet" type="text/css">
        <link href="{BASE_PATH}functions/yui/build/container/assets/skins/sam/container.css" rel="stylesheet" type="text/css">
        <link href="{BASE_PATH}functions/yui/build/calendar/assets/skins/sam/calendar.css" rel="stylesheet" type="text/css">
        <!-- CSS Include -->
        <link href="{BASE_PATH}config/main.css" rel="stylesheet" type="text/css">
        <link href="{BASE_PATH}config/content.css" rel="stylesheet" type="text/css">
        <link href="{BASE_PATH}functions/yui/build/tabview/assets/skins/sam/tabview.css" rel="stylesheet" type="text/css">
        <!-- YUI Scripts -->
        <script type="text/JavaScript" src="{BASE_PATH}functions/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
        <script type="text/JavaScript" src="{BASE_PATH}functions/yui/build/element/element-min.js"></script>
        <script type="text/JavaScript" src="{BASE_PATH}functions/yui/build/button/button-min.js"></script>
        <script type="text/JavaScript" src="{BASE_PATH}functions/yui/build/connection/connection-min.js"></script>
        <script type="text/JavaScript" src="{BASE_PATH}functions/yui/build/utilities/utilities.js"></script>
        <script type="text/JavaScript" src="{BASE_PATH}functions/yui/build/container/container-min.js"></script>
        <script type="text/JavaScript" src="{BASE_PATH}functions/yui/build/calendar/calendar-min.js"></script>
        <script type="text/JavaScript" src="{BASE_PATH}functions/yui/build/tabview/tabview-min.js"></script>
        <script type="text/JavaScript" src="{BASE_PATH}functions/common.js"></script>
        <script type="text/javascript" language="javascript">
            <!--
            function doChangeDomain() {
                  document.frmDomChange.submit();
            }
            //-->
        </script>
    </head>
    <body  class="yui-skin-sam">
        <table width="100%" cellpadding="0" cellspacing="0" class="header">
            <tr>
                <td class="headerleft">{ADMIN}&nbsp;{MONITOR}&nbsp;{PLUGINS}</td>
                <td class="headermiddle">&nbsp;</td>
                <td class="headerright">&nbsp;</td>
            </tr>
            <tr>
                <td class="infoleft">{POSITION}</td>
                <td class="inforight">&nbsp;</td>
                <td class="inforight">
                    <form name="frmDomChange" action="" method="post">
                        <table cellpadding="0" cellspacing="0" border="0" align="right" width="350">
                            <tr>
<!-- END header -->  
<!-- BEGIN dselect -->         
                                <td class="inforight2">{DOMAIN_INFO}</td>
                                <td class="inforight2">    
                                    <select title="{DOMAIN_INFO}" name="selDomain" onChange="doChangeDomain();">
<!-- BEGIN domainsel -->
                                        <option value="{DOMAIN_VALUE}" {DOMAIN_SELECTED}>{DOMAIN_TEXT}</option>
<!-- END domainsel -->
                                    </select>  
                                </td>
<!-- END dselect -->               
<!-- BEGIN header2 -->                
                                <td class="inforight2">&nbsp;&nbsp;{LOGIN_INFO}&nbsp;{LOGOUT_INFO}</td>
                            </tr>
                        </table>
                    </form>
                </td>
            </tr>
        </table>
        <table width="100%">
            <tr>
                {MAINMENU}
                <td class="main" valign="top">
<!-- END header2 -->
<!-- BEGIN footer -->
                </td>
            </tr>
        </table>
        <p class="version">{VERSION_INFO}</p>
    </body>
</html>
<!-- END footer -->