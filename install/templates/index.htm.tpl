<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : Installer main template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>{PAGETITLE}</title>
        <link href="css/install.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div id="page_margins">
            <div id="page">
                <div id="header">
                    <div id="header-logo">
                        <a href="index.php"><img src="images/nagiosql.png" border="0" alt="NagiosQL"></a>
                    </div>
                    <div id="documentation">
                        <a href='https://sourceforge.net/projects/nagiosql/files/nagiosql/Documentation/' target='_blank'>{NAGIOS_FAQ}</a>
                    </div>
                    <div id="langselector">
                        <form action="" name="frmLanguage" id="frmLanguage" method="post">
                            {LANGUAGE} :
                            <select title="{LANGUAGE}" name="selLanguage" onchange="document.frmLanguage.submit();">
                                {LANG_OPTION}
                            </select>
                        </form>
                    </div>
                </div>
                <div id="main">
                    <div id="indexmain">
                        <div id="indexmain_content">
                            <h1>{MAIN_TITLE}</h1>
                            <div style="text-align: center;">{TEXT_PART_1}<br>{TEXT_PART_2}<a href="https://sourceforge.net/projects/nagiosql/" target="_blank">NagiosQL @ Sourceforge</a></div>
                            <p><div style="text-align: center;"><strong>{TEXT_PART_9}</strong></div>
                            <br>
                            <p>{TEXT_PART_3}</p>
                            <p>{TEXT_PART_4}</p>
                            <ul>
                                <li>{TEXT_PART_5}</li>
                                <ul>
                                    <li>{TEXT_PHP_REQ_1}</li>
                                    <li>{TEXT_PHP_REQ_2}</li>
                                    <li>{TEXT_PHP_REQ_3}</li>
                                    <li>{TEXT_PHP_REQ_6}</li>
                                    <li>{TEXT_PHP_REQ_8}</li>
                                    <li>{TEXT_PHP_REQ_10}</li>
                                </ul>
                            </ul>  
                            <ul>
                                <li>{TEXT_PART_6}</li>    
                                <ul>
                                    <li>{TEXT_INI_REQ_1}</li>
                                    <li>{TEXT_INI_REQ_2}</li>
                                </ul>
                            </ul> 
                            <ul>
                                <li>{TEXT_PART_7}</li>
                                <li>{TEXT_PART_8}</li>
                            </ul>
                            {UPDATE_ERROR}
                            <form name="frmInstall" id="frmInstall" action="install.php" method="post">
                                <input type="hidden" name="hidLocale" value="{LOCALE}">
                                <input type="hidden" name="hidStep" value="1">
                                <input type="hidden" name="hidJScript" value="">
                                <p><input type="submit" name="butNewInstall" id="butNewInstall" value="{NEW_INSTALLATION}" {DISABLE_NEW}>
                                <input type="submit" name="butUpgrade" id="butUpgrade" value="{UPDATE}" {DISABLE_UPDATE}></p>
                                <p><a href='https://sourceforge.net/projects/nagiosql/files/nagiosql/Documentation/' target='_blank'>{ONLINE_DOC}</a></p>
                            </form>
                        </div>
                    </div>
                    <div id="footer">
                            <a href='https://sourceforge.net/projects/nagiosql/' target='_blank'>NagiosQL</a> <?php echo BASE_VERSION; ?>
                    </div>
                </div>
            </div>
        </div>
        <script type="">
            <!--
            document.frmInstall.hidJScript.value = 'yes';
            //-->
        </script>
    </body>
</html>