<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project    : NagiosQL -->
<!-- Component  : NagiosQL support page template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN support -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <h2 style="padding-top:10px;">{SUBTITLE_1}</h2>
    <p>{SUPPORT_TEXT_1}</p>
    <p><a href="https://sourceforge.net/projects/nagiosql/" target="_blank">{WEBSITE_LINK}</a>
    <h2 style="padding-top:10px;">{SUBTITLE_2}</h2>
    <p style="margin-bottom:0;">{SUPPORT_TEXT_2}</p>
    <p><a href="http://sourceforge.net/donate/index.php?group_id=134390" target="_blank">{DONATE_LINK}</a>
    <h2 style="padding-top:10px;">{SUBTITLE_3}</h2>
    <p style="margin-bottom:0;">{SUPPORT_TEXT_3}</p>
    <p><a href="https://www.transifex.net/projects/p/nagiosql/" target="_blank">{TRANSLATION_LINK}</a>
    <h2 style="padding-top:10px;">{SUBTITLE_8}</h2>
    <p style="margin-bottom:0;">{SUPPORT_TEXT_5}</p>
    <p><a href="https://gitlab.com/wizonet/NagiosQL/" target="_blank">{GIT_LINK}</a>
    <h2 style="padding-top:10px;">{SUBTITLE_4}</h2>
    <p style="margin-bottom:0;">{SUPPORT_TEXT_4}</p>
<!-- BEGIN versioncheck_frame -->	      
    <iframe scrolling="no" id="versioncheck" name="fullcommand" src="{VERSION_IF_SRC}" width="100%" height="55" class="elementHide"></iframe>
    <div id="vcheck" class="elementShow" style="height:50px; vertical-align:top; padding-top:5px;"><img src="{LOADER_IMAGE}" alt="Loading..." title="Loading" width="16" height="16"></div>
<!-- END versioncheck_frame -->
    <table width="1000" border="0" cellpadding="0" cellspacing="0" class="env_table">
        <tr>
            <td style="width:200px;">{GIT_TITLE}</td>
            <td style="width:150px;" class="checkgreen">{GIT_VERSION}</td>
            <td style="width:300px;">&nbsp;</td>
            <td style="width:250px;">&nbsp;</td>
        </tr>
    </table>
    <h2 style="padding-top:10px;">{SUBTITLE_5}</h2>
    <table width="1000" border="0" cellpadding="0" cellspacing="0" class="env_table">
    	<tr>
            <td style="width:200px;">Javascript</td>
            <td style="width:150px;" id="jsfield" class="checkred">{FAILED}</td>
            <td style="width:300px;">{INI_FILE_UPLOADS}</td>
            <td style="width:250px;" class="{INI_FILE_UPLOADS_CLASS}">{INI_FILE_UPLOADS_RESULT}</td>
        </tr>
    	<tr>
            <td>{PHP_VERSION}</td>
            <td class="{PHP_CLASS}">{PHP_RESULT}</td>
            <td>{INI_AUTO_START}</td>
            <td class="{INI_AUTO_START_CLASS}">{INI_AUTO_START_RESULT}</td>
        </tr>
    	<tr>
            <td>{PHP_SESSION_MODULE}</td>
            <td class="{PHP_SESSION_CLASS}">{PHP_SESSION_RESULT}</td>
            <td>{INI_SUHO_SESS_ENC}</td>
            <td class="{INI_SUHO_SESS_ENC_CLASS}">{INI_SUHO_SESS_ENC_RESULT}</td>
        </tr>
    	<tr>
            <td>{PHP_GETTEXT_MODULE}</td>
            <td class="{PHP_GETTEXT_CLASS}">{PHP_GETTEXT_RESULT}</td>
            <td>{INI_DATE_TIMEZONE}</td>
            <td class="{INI_DATE_TIMEZONE_CLASS}">{INI_DATE_TIMEZONE_RESULT}</td>
        </tr>
    	<tr>
            <td>{PHP_FTP_MODULE}</td>
            <td class="{PHP_FTP_CLASS}">{PHP_FTP_RESULT}</td>
            <td>{RW_CONFIG}</td>
            <td class="{RW_CONFIG_CLASS}">{RW_CONFIG_RESULT}</td>
        </tr>
    	<tr>
            <td>{PHP_SSH2_MODULE}</td>
            <td class="{PHP_SSH2_CLASS}">{PHP_SSH2_RESULT}</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>            
        </tr>
    	<tr>
            <td>{DB_VERSION}</td>
            <td class="{DB_CLASS}">{DB_RESULT}</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>  
        </tr>
    </table>
<!-- BEGIN configdomain -->	
    <h2 style="padding-top:15px;">{SUBTITLE_6}</h2>
    <p style="margin-bottom:0;">{SUPPORT_TEXT_6}</p>
    <table width="1000" border="0" cellpadding="0" cellspacing="0" class="env_table">
    	<tr>
            <td style="width:200px;">{DOMAIN_NAME}</td>
            <td style="width:150px;" class="checkgreen">{DOMAIN_NAME_VALUE}</td>
            <td style="width:300px;">{RW_NAG_CONF}</td>
            <td style="width:250px;" class="{RW_NAG_CONF_CLASS}">{RW_NAG_CONF_RESULT}</td>
        </tr>
    	<tr>
            <td>{CONNECT_TYPE}</td>
            <td class="{CONNECT_TYPE_CLASS}">{CONNECT_TYPE_RESULT}</td>
            <td>{CHECK_NAG_LOCK}</td>
            <td class="{CHECK_NAG_LOCK_CLASS}">{CHECK_NAG_LOCK_RESULT}</td>            
        </tr>
    	<tr>
            <td>{CONNECT_CHECK}</td>
            <td class="{CONNECT_CHECK_CLASS}">{CONNECT_CHECK_RESULT}</td>
            <td>{RW_NAG_COMMAND}</td>
            <td class="{RW_NAG_COMMAND_CLASS}">{RW_NAG_COMMAND_RESULT}</td> 
        </tr>
    	<tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>{EXE_NAG_BINARY}</td>
            <td class="{EXE_NAG_BINARY_CLASS}">{EXE_NAG_BINARY_RESULT}</td> 
        </tr>
    </table>
    <h2 style="padding-top:15px;">{SUBTITLE_7}</h2>
	<table border="0" class="content_listtable" style="top:5px; margin-top:0; padding-top:0;">
        <tr style="height:18px;">
            <th style="width:150px;height:18px;">{CONFIGURATION_NAME}</th>
            <th style="width:200px;height:18px;">{USED}</th>
            <th style="width:500px;height:18px;">{DEMON_CONFIG}</th>
        </tr>
<!-- BEGIN configfileline -->
        <tr style="height:18px;">
            <td style="height:18px; text-align:left; padding-left:5px;" class="{CLASS_M}">{CONFIG_NAME}</td>
            <td style="height:18px; text-align:left; padding-left:5px;" class="{CLASS_M}">{ACTIVE_CONFIG_COUNT}</td>
            <td style="height:18px; text-align:left; padding-left:5px;" class="{CLASS_L}">{DEMON_CFG_OK}</td>
    	</tr>
<!-- END configfileline -->
	</table>
<!-- END configdomain -->	  
    <p><span class="redmessage">{ERRORMESSAGE}</span><span class="greenmessage">{INFOMESSAGE}</span></p>
    <script type="text/javascript">
        <!--
        document.getElementById('jsfield').className = 'checkgreen';
        document.getElementById('jsfield').firstChild.data = '{OK}';
        //-->
    </script>
<!-- BEGIN versioncheck_js -->	      
    <script type="text/javascript">
        <!--
        document.getElementById('versioncheck').src = '{VERSION_IF_SRC_RELOAD}';
        //-->
    </script>
<!-- END versioncheck_js -->
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
	<div id="dialogcontent" class="bd"></div>
</div>
<!-- END support -->