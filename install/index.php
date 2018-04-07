<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project  : NagiosQL
// Component: Installer
// Website  : http://www.nagiosql.org
// Date     : $LastChangedDate: 2011-03-13 14:00:26 +0100 (So, 13. Mär 2011) $
// Author   : $LastChangedBy: rouven $
// Version  : 3.1.1
// Revision : $LastChangedRevision: 1058 $
//
///////////////////////////////////////////////////////////////////////////////
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>[NagiosQL] Installation Wizard</title>
<link rel="stylesheet" type="text/css" href="css/install.css">
<link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
if (isset($_GET['js'])) {
	$js = htmlspecialchars($_GET['js'],ENT_QUOTES, 'utf-8');
} else { 
  ?>
	<script language="JavaScript">
	location.href = location.href+'?js='+'on';/*send get with js*/
	</script>
	<noscript>
	<!/* send get with header*/>
	<meta http-equiv="refresh" content="0;url=index.php?js=off" />    
	</noscript>
	<?php
}
require_once("functions/func_installer.php");
$intError = 0;
// Init POST and GET variables
$locale = isset($_POST['locale']) ? $locale = htmlspecialchars($_POST['locale'],ENT_QUOTES, 'utf-8') : "";
?> 
</head>
<body>
  <div id="page_margins">
    <div id="page">
      <div id="header">
        <div id="header-logo">
          <a href="index.php"><img src="images/nagiosql.png" border="0" alt="NagiosQL"></a>
        </div>
        <div id="documentation">
        <?php
          if (extension_loaded('gettext')) {
            // Language Definition
            if ($locale == "") {
              if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2) == "de") {
                  $locale = 'de_DE';
              } else {
                $locale = 'en_GB';
              }
            }
            $encoding = 'utf-8';
            putenv("LC_ALL=".$locale.".".$encoding);
            putenv("LANG=".$locale.".".$encoding);
            $domain = $locale; // defines gettext domain
            $localPath = "../config/locale";
            setlocale(LC_ALL, $locale.".".$encoding); // defines language
            bindtextdomain($domain, $localPath); // location of language files
            bind_textdomain_codeset($domain, $encoding); // define encoding and domain
            textdomain($domain); // use the domain
            require("../functions/translator.php");
            echo "<a href='http://www.nagiosql.org/faq/' target='_blank'>" . translate("Online Documentation") ."</a>"; ?>
        </div>
        <div id="langselector">
        	<?php
            echo "<FORM action='' name='language' method='post'>\n";
            echo translate("Language").": ";
            echo "<SELECT name='locale' onchange='document.language.submit();'>\n";
            $arrAvailableLanguages=getLanguageData();
            foreach(getLanguageData() as $key=>$val) {
              echo "<option";
              if ($locale == $key) {
                echo " selected";
              }
              echo " value='".$key."'>".getLanguageNameFromCode($key,false)."</option>\n";
            }
            echo "</SELECT>\n";
            echo "</form>\n";            
         ?>
        </div>
      </div>
      <div id="main">
        <div id="indexmain">
            <div id="indexmain_content">
            <?php
            echo "<h1>". translate("Welcome to the NagiosQL Installation Wizard")."</h1>\n";
            echo "<center>". translate("This wizard will help you to install and configure NagiosQL.")."<br>";
            echo translate("For questions please visit")." <a href=\"http://www.nagiosql.org\" target=\"_blank\">www.nagiosql.org</a></center>\n";
						// Display basic requirements
						echo "<p>".translate("First let's check your local environment and find out if everything NagiosQL needs is available.")."</p>\n";
						echo "<p>".translate("The basic requirements are:")."</p>\n";
						echo "<ul>\n";
						echo "<li>".translate("PHP 5.2.0 or greater including:")."\n";
						echo "<ul>\n";
						echo "<li>".translate("PHP Module:")." Session</li>\n";
						echo "<li>".translate("PHP Module:")." MySQL</li>\n";
						echo "<li>".translate("PHP Module:")." gettext</li>\n";
						echo "<li>".translate("PHP Module:")." filter</li>\n";
						echo "<li>".translate("PHP Module:")." XML</li>\n";
						echo "<li>".translate("PHP Module:")." SimpleXML</li>\n";   	
						echo "<li>".translate("PHP Module:")." FTP ".translate("(optional)")."</li>\n";
						echo "<li>".translate("PHP Module:")." curl ".translate("(optional)")."</li>\n";           			
						echo "<li>".translate("PECL Extension:")." SSH ".translate("(optional)")."</li>\n";  
						echo "</ul>\n";
						echo "</li>\n";
						echo "<li>".translate("php.ini options").":\n";
						echo "<ul>\n";
						echo "<li>".translate("file_uploads on (for upload features)")."</li>\n";
						echo "<li>".translate("session.auto_start needs to be off")."</li>\n";
						echo "</ul>\n";
						echo "</li>\n";
						echo "<li>".translate("A MySQL database server")."</li>\n";
						echo "<li>".translate("Nagios 2.x/3.x or Icinga 1.x")."</li>\n";
						echo "</ul>\n";
						echo "<form action='install.php' method='post' name='installer'>\n";
						echo "<input type='hidden' name='locale' value=".$locale.">\n";
						echo "<input type='hidden' name='step' value='1'>\n";
						echo "<input type='hidden' name='js' value='".$js."'>\n";
						//
						// Check for install mode: update or new installation
						//
						// Read configuration file if exists
            $strFile = "../config/settings.php";
            if(file_exists($strFile) && is_readable($strFile)) {
							$_SESSION['ConfigFile'] = $strFile;
							$SETS = parseIniFile($strFile);
							if (isset($SETS['db']['server']) && isset($SETS['db']['port']) && isset ($SETS['db']['database']) && isset($SETS['db']['username']) &&  isset($SETS['db']['password'])) {
								// Store the settings to the session
								$_SESSION['SETS'] = $SETS;
								if (extension_loaded('mysql')) {
									// Include mysql class
									include("../functions/mysql_class.php");
									// Initialize mysql class
									$myDBClass = new mysqldb;
									if ($myDBClass->error == true) {
										$strMessage .= translate('Error while connecting to database:')."<br>".$myDBClass->strDBError."<br>";
										$intError 	 = 1;
									}
									// Get additional configuration from the table tbl_settings
									if ($intError == 0) {
										$strSQL    = "SELECT `category`,`name`,`value` FROM `tbl_settings`";
										$booReturn = $myDBClass->getDataArray($strSQL,$arrDataLines,$intDataCount);
										if ($booReturn == false) {
											$strMessage .= translate('Error while selecting data from database:')."<br>".$myDBClass->strDBError."<br>";
											$intError 	 = 1;
										} else if ($intDataCount != 0) {
											for ($i=0;$i<$intDataCount;$i++) {
												$SETS[$arrDataLines[$i]['category']][$arrDataLines[$i]['name']] = $arrDataLines[$i]['value'];
											}
										}
									}
								} else {
									$intError = 3;
								}
							}
						} else {
							$intError = 2;
						}
						// Store configuration from settings.php to the session or initiate variables
						$_SESSION['SETS']['db']['server']						= isset($SETS['db']['server'])					?   $SETS['db']['server']						: "localhost";
						$_SESSION['SETS']['db']['port']							= isset($SETS['db']['port'])  					?   $SETS['db']['port']							: "3306";
						$_SESSION['SETS']['db']['database']					= isset($SETS['db']['database'])  			?   $SETS['db']['database']					: "db_nagiosql_v3";
						$_SESSION['SETS']['db']['username']					= isset($SETS['db']['username'])  			?   $SETS['db']['username']					: "nagiosql_user";
						$_SESSION['SETS']['db']['password']					= isset($SETS['db']['password'])  			?   $SETS['db']['password']					: "nagiosql_pass";
						// Store additional configuration from tbl_settings to the session or initiate variables
						$_SESSION['SETS']['path']['protocol']				= isset($SETS['path']['protocol'])  		?   $SETS['path']['protocol']				: get_protocol();			
						$_SESSION['SETS']['path']['tempdir']				= isset($SETS['path']['tempdir'])  			?   $SETS['path']['tempdir']				: sys_get_temp_dir();
						$_SESSION['SETS']['data']['locale']					= isset($locale)												?	  $locale													: 'en_GB';
						$_SESSION['SETS']['data']['encoding']				= isset($SETS['data']['encoding'])  		?   $SETS['data']['encoding']				: 'utf-8';
			-			$_SESSION['SETS']['security']['logofftime']	= isset($SETS['security']['logofftime'])?   $SETS['security']['logofftime']	: 3600;
						$_SESSION['SETS']['security']['wsauth']			= isset($SETS['security']['wsauth'])  	?   $SETS['security']['wsauth']			: 0;
						$_SESSION['SETS']['common']['pagelines']		= isset($SETS['common']['pagelines'])  	?   $SETS['common']['pagelines']		: 15;
						$_SESSION['SETS']['common']['seldisable']		= isset($SETS['common']['seldisable'])  ?   $SETS['common']['seldisable']		: 1;
						$_SESSION['SETS']['common']['tplcheck']			= isset($SETS['common']['tplcheck'])  	?   $SETS['common']['tplcheck']			: 0;
						$_SESSION['SETS']['common']['updcheck']			= isset($SETS['common']['updcheck'])  	?   $SETS['common']['updcheck']			: 0;			
						$_SESSION['SETS']['network']['Proxy']				= isset($SETS['network']['Proxy'])  		?   $SETS['network']['Proxy']				: 0;				
						$_SESSION['SETS']['network']['ProxyServer']	= isset($SETS['network']['ProxyServer'])?   $SETS['network']['ProxyServer']	: '';	
						$_SESSION['SETS']['network']['ProxyUser']		= isset($SETS['network']['ProxyUser'])  ?   $SETS['network']['ProxyUser']		: '';	
						$_SESSION['SETS']['network']['ProxyPasswd']	= isset($SETS['network']['ProxyPasswd'])?   $SETS['network']['ProxyPasswd']	: '';	
			      // proceed with installation or update
			      if ($intError == 0) {
			        $result = get_current_version($_SESSION['SETS']['db']['server'], $_SESSION['SETS']['db']['port'], $_SESSION['SETS']['db']['username'], $_SESSION['SETS']['db']['password'], $_SESSION['SETS']['db']['database'], $strCurrentVersion,$errmsg);
			    		if ($errmsg) echo "<div class='red'>".$errmsg."</div><br>";
			    		if ($result) {        			
			      		echo "<input type='hidden' name='butInstallType' value='Update'>\n";
			      		echo "<input value='".translate("START UPDATE")."' type='submit'>\n";
			    		} else {
			      		echo "<input type='hidden' name='butInstallType' value='Installation'>\n";
			      		echo "<input value='".translate("START INSTALLATION")."' type='submit'>\n";       		}
			    	} else {
			    		echo "<input type='hidden' name='butInstallType' value='Installation'>\n";
			     		echo "<input value='".translate("START INSTALLATION")."' type='submit'>\n";
			     	}
						echo "<input type='hidden' name='PHPSESSID' value='".session_id()."' />";
			      echo "</form>\n";
			    } else {
			      echo "<a href='http://www.nagiosql.org/faq.html' target='_blank'>Online Documentation</a>"; ?>
          </div>
        </div>
        <div id="main">
          <div id="indexmain">
              <div id="indexmain_content">
            <h1>Welcome to the NagiosQL <?php echo BASE_VERSION; ?> Installation</h1>
            <center><h2><font color="red">Installation cannot continue, please make sure you have the php-gettext extension loaded!</font></h2></center>
            <form action='index.php' method='post'>
              <input type='button' value='Refresh' onClick='history.go()'/>
            </form>
          <?php
            }
          ?>
        </div>
      </div>
    </div>
    <div id="footer">
        <a href='http://www.nagiosql.org' target='_blank'>NagiosQL</a> <?php echo BASE_VERSION; ?>
    </div>
  </div>
</div>
</body>
</html>
