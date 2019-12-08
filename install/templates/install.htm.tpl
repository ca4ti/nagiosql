<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : Installer template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>{PAGETITLE}</title>
        <link href="css/install.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div id="page_margins">
            <div id="page">
                <div id="header">
                    <div id="header-logo">
                        <a href="index.php"><img src="images/nagiosql.png" border="0" alt="NagiosQL Logo" title="NagiosQL Logo"></a>
                    </div>
                    <div id="documentation">
                        <a href="https://sourceforge.net/projects/nagiosql/faq.html" target="_blank"><?php echo translate("Online Documentation"); ?></a>   
                    </div>
                </div>
                <div id="main">
                    {CONTENT}
                </div>
                <div id="footer">
                    <a href='https://sourceforge.net/projects/nagiosql/' target='_blank'>NagiosQL</a> <?php echo BASE_VERSION; ?>
                </div>
            </div>
        </div>
    </body>
</html>