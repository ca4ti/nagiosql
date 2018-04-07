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
// Date     : $LastChangedDate: 2011-04-10 16:59:17 +0200 (So, 10. Apr 2011) $
// Author   : $LastChangedBy: rouven $
// Version  : 3.1.1
// Revision : $LastChangedRevision: 1069 $
//
///////////////////////////////////////////////////////////////////////////////

// Security
if(preg_match('#' . basename(__FILE__) . '#', filter_var($_SERVER['PHP_SELF'], FILTER_SANITIZE_STRING))) {
  die("You can't access this file directly!");
}
if (!isset($_SESSION['SETS']['install']['step'])) {
  header("Location: index.php");
} else {
  $_SESSION['SETS']['install']['step'] = 3;
}
$intError = 0;
if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get")) {
 @date_default_timezone_set(@date_default_timezone_get());
}
?>
<!-- DIV Container for installer Menu -->
<div id="installmenu">
  <div id="installmenu_content">
    <?php include "status.php"; ?>
  </div>
</div>
<!-- DIV Container for installer content -->
<div id="installmain">
  <div id="installmain_content">
    <h1>NagiosQL <?php echo translate($_SESSION['SETS']['install']['InstallType']). ": ". translate("Finishing Setup"); ?></h1>
    <form action="" method="post" class="cmxform" id="setup" name="setup">
    <?php
    	switch ($_SESSION['SETS']['install']['InstallType']) {
      	case "Installation":
	    		echo "<fieldset>\n";
					echo "<legend>".translate("Create new NagiosQL database")."</legend>\n";
					echo "<ol>\n";
					// Check if "no db drop" was selected but database exists
					if ($_SESSION['SETS']['install']['db_drop'] == 0) {
						$link = @mysql_connect($_SESSION['SETS']['db']['server'].':'.$_SESSION['SETS']['db']['port'],$_SESSION['SETS']['install']['db_privuser'],$_SESSION['SETS']['install']['db_privpwd']);
						if ($link) {
							$selectDB = @mysql_select_db($_SESSION['SETS']['db']['database'], $link);
							if ($selectDB) {
								echo "<li><label><span class='red'>".translate("Database already exists and drop database was not selected, please correct or manage manually").".</span></label></li>\n";
								$intError=1;
							}
							mysql_close($link);
						}
					}
					// Database connectivity
					if ($intError != 1) {
						echo "<li><label>".translate("MySQL server connection (privileged user)")."</label>";
						$newdb=db_connect($_SESSION['SETS']['db']['server'],$_SESSION['SETS']['db']['port'],$_SESSION['SETS']['install']['db_privuser'],$_SESSION['SETS']['install']['db_privpwd'],$_SESSION['SETS']['db']['database'],"utf8",$errmsg);
						if ($errmsg != "") {
							echo "<div id='dbconnecterror' class='tooltip'>".$errmsg."</div>\n";
							echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'dbconnecterror');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
							$intError=1;
						} else {
							echo "<span class='green'>".translate("passed")."</span></li>\n";
							echo "<li><label>".translate("MySQL server version")."</label>";
							$setVersion = @mysql_result(@mysql_query("SHOW VARIABLES LIKE 'version'"),0,1);
							if (mysql_error() == "") {
								echo "<span class='green'>$setVersion</span></li>\n";
								$arrVersion1 = explode("-",$setVersion);
								$arrVersion2 = explode(".",$arrVersion1[0]);
								if ($arrVersion2[0] <  4) $setMySQLVersion = 0;
								if ($arrVersion2[0] == 4) $setMySQLVersion = 1;
								if (($arrVersion2[0] == 4) && ($arrVersion2[1] > 0))  $setMySQLVersion = 2;
								if ($arrVersion2[0] >  4) $setMySQLVersion = 2;
								echo "<li><label>".translate("MySQL server support")."</label>";
								if ($setMySQLVersion != 0) {
									echo "<span class='green'>".translate("supported")."</span></li>\n";
								} else {
									echo "<span class='red'>".translate("not supported")."</span></li>\n";
									$intError = 1;
								}
							} else {
								echo "<span class='red'>".translate("failed")."</span></li>\n";
								$intError = 1;
							}
						}
					}
			// Drop existing NagiosQL database if checked
			if ($intError != 1 AND $_SESSION['SETS']['install']['db_drop'] == 1) {
				echo "<li><label>".translate("Delete existing NagiosQL database")." ".htmlspecialchars($_SESSION['SETS']['db']['database'], ENT_QUOTES, 'utf-8')."</label>";
				$result = dropMySQLDB($_SESSION['SETS']['db']['server'], $_SESSION['SETS']['db']['port'], $_SESSION['SETS']['install']['db_privuser'], $_SESSION['SETS']['install']['db_privpwd'], $_SESSION['SETS']['db']['database'], $errmsg);
				if ($result) {
					echo "<span class='green'>".translate("done")."</span></li>\n";
				} else {
					echo "<div id='dbdroperror' class='tooltip'>".$errmsg."</div>\n";
					echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'dbdroperror');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
					$intError = 1;
				}
			}
			// Install new database
			if ($intError != 1) {
				echo "<li><label>".translate("Creating new database")." ".htmlspecialchars($_SESSION['SETS']['db']['database'], ENT_QUOTES, 'utf-8')."</label>";
				$strFile="sql/nagiosQL_v31_db_mysql.sql";
				if (file_exists($strFile) AND is_readable($strFile)) {
					$link=db_connect($_SESSION['SETS']['db']['server'], $_SESSION['SETS']['db']['port'], $_SESSION['SETS']['install']['db_privuser'], $_SESSION['SETS']['install']['db_privpwd'],"","",$errmsg);
					if ($errmsg == "") {
						$result=mysql_install_db($_SESSION['SETS']['db']['database'], $strFile, $errmsg);
						if (!$result) {
							echo "<div id='dbinstallerror' class='tooltip'>".$errmsg."</div>\n";
							echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'dbinstallerror');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
							$intError = 1;
						} else {
							echo "<span class='green'>".translate("done")."</span></li>\n";
						}
					} else {
						echo "<div id='dbconnecterror' class='tooltip'>".$errmsg."</div>\n";
						echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'dbconnecterror');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
						$intError = 1;
					}
				} else {
					echo "<span class='red'>".translate("Could not access")." ".$strFile."</span></li>\n";
					$intError = 1;
				}
			}
			// Add NagiosQL MySQL user
			if ($intError != 1) {
				echo "<li><label>".translate("Create NagiosQL MySQL user")."</label>";
				$result = addMySQLUser($_SESSION['SETS']['db']['server'], $_SESSION['SETS']['db']['port'], $_SESSION['SETS']['install']['db_privuser'], $_SESSION['SETS']['install']['db_privpwd'], $_SESSION['SETS']['db']['username'], $_SESSION['SETS']['db']['password'],$errmsg);
				if ($result) {
					echo "<span class='green'>".translate("done")."</span></li>\n";
				} else {
					echo "<div id='dbcreatedbuser' class='tooltip'>".$errmsg."</div>\n";
					echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'dbcreatedbuser');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
					$intError = 1;
				}
			}
			// Set MySQL permission
			if ($intError != 1) {
				echo "<li><label>".translate("Update MySQL permissions")."</label>";
				$result = setMySQLPermission($_SESSION['SETS']['db']['server'], $_SESSION['SETS']['db']['port'], $_SESSION['SETS']['db']['database'], $_SESSION['SETS']['install']['db_privuser'], $_SESSION['SETS']['install']['db_privpwd'], $_SESSION['SETS']['db']['username'], $errmsg);
				if ($result) {
					echo "<span class='green'>".translate("done")."</span></li>\n";
				} else {
					echo "<div id='dbsetpermission' class='tooltip'>".$errmsg."</div>\n";
					echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'dbsetpermission');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
					$intError = 1;
				}
			}
			// Flush MySQL privileges
			if ($intError != 1) {
				echo "<li><label>".translate("Reloading MySQL User Table")."</label>";
				$result = flushMySQLPrivileges($_SESSION['SETS']['db']['server'], $_SESSION['SETS']['db']['port'], $_SESSION['SETS']['install']['db_privuser'], $_SESSION['SETS']['install']['db_privpwd'], $errmsg);
				if ($result) {
					echo "<span class='green'>".translate("done")."</span></li>\n";
				} else {
					echo "<div id='dbflushpriv' class='tooltip'>".$errmsg."</div>\n";
					echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'dbflushpriv');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
					$intError = 1;
				}
			}
			// Validating new database connection with recently added user
			if ($intError != 1) {		 
				echo "<li><label>".translate("Testing database connection to")." ".htmlspecialchars($_SESSION['SETS']['db']['database'], ENT_QUOTES, 'utf-8')."</label>";
				$link = @mysql_connect($_SESSION['SETS']['db']['server'].':'.$_SESSION['SETS']['db']['port'],$_SESSION['SETS']['db']['username'],$_SESSION['SETS']['db']['password']);
				if ($link) {
					$selectDB = @mysql_query("SELECT `id` FROM `".mysql_real_escape_string($_SESSION['SETS']['db']['database'])."`.`tbl_settings` LIMIT 1");
					if ($selectDB) {
						echo "<span class='green'>".translate("passed")."</span></li>\n";		 
					} else {
						echo "<div id='dbnewselect' class='tooltip'>".mysql_error()."</div>\n";
						echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'dbnewselect');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
						$intError = 1;
					}
					mysql_close($link);
				} else {
					echo "<div id='dbnewconnect' class='tooltip'>".mysql_error()."</div>\n";
					echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'dbnewconnect');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
					$intError = 1;
				}		
			}
			// Write settings to database
			if ($intError != 1) {
				echo "</ol>\n";
				echo "</fieldset>\n";
				echo "<fieldset>\n";
				echo "<legend>".translate("Deploy NagiosQL settings")."</legend>\n";
				echo "<ol>\n";
				echo "<li><label>".translate("Writing global settings to database")."</label>";
				if (writeSettingsDB($errmsg)) {
					echo "<span class='green'>".translate("done")."</span></li>\n";
				} else {
					echo "<div id='dbdeploysettings' class='tooltip'>".$errmsg."</div>\n";
					echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'dbdeploysettings');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
					$intError=1;
				}
			}
		 // Write database settings to file
         if ($intError != 1) {
			echo "<li><label>".translate("Writing database configuration to settings.php")."</label>";
			if (writeSettingsFile($errmsg)) {
				echo "<span class='green'>".translate("done")."</span></li>\n";
			} else {
				echo "<div id='deploysettings' class='tooltip'>".$errmsg."</div>\n";
				echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'deploysettings');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
				$intError=1;
			}
         }
		// Set initial NagiosQL User/Pass
		if ($intError != 1) {
			echo "<li><label>".translate("Set initial NagiosQL Administrator")."</label>";
			$result = setQLUser($_SESSION['SETS']['install']['ql_user'], $_SESSION['SETS']['install']['ql_pass'], $errmsg);
			if ($result) {
				echo "<span class='green'>".translate("done")."</span></li>\n";
			} else {
				echo "<div id='setqluser' class='tooltip'>".$errmsg."</div>\n";
				echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'setqluser');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
				$intError = 1;
			}
		}
		// Import Nagios sample data
		if ($intError != 1 && $_SESSION['SETS']['install']['sampleData'] == 1) {
			echo "<li><label>".translate("Import Nagios sample data")."</label>";
			$result = importSample($_SESSION['SETS']['db']['server'],$_SESSION['SETS']['db']['port'],$_SESSION['SETS']['db']['username'],$_SESSION['SETS']['db']['password'],$_SESSION['SETS']['db']['database'],"sql/import_nagios_sample.sql",$errmsg);
			if ($result) {
				echo "<span class='green'>".translate("done")."</span></li>\n";
			} else {
				echo "<div id='import' class='tooltip'>".$errmsg."</div>\n";
				echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'import');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
				$intError = 1;
			}
		}
		echo "</ol>\n";
		echo "</fieldset>\n";
        break;
        case "Update":
        	echo "<fieldset>\n";
			echo "<legend>".translate("Updating existing NagiosQL database")."</legend>\n";
			echo "<ol>\n";
			// Check existing NagiosQL Version
			echo "<li><label>".translate("Installed NagiosQL version")."</label>";
			$result = get_current_version($_SESSION['SETS']['db']['server'], $_SESSION['SETS']['db']['port'], $_SESSION['SETS']['install']['db_privuser'], $_SESSION['SETS']['install']['db_privpwd'], $_SESSION['SETS']['db']['database'], $strCurrentVersion,$errmsg);
			if ($result) {
				echo "<span class='green'>".$strCurrentVersion."</span></li>\n";
			} else {
				if ($strCurrentVersion != "") {
					echo "<span class='red'>".$strCurrentVersion." ".translate("is not supported!")."</span></li>\n";
					$intError=1;
				} else {
					echo "<div id='versioncheck' class='tooltip'>".$errmsg."</div>\n";
					echo "<span class='red'>".translate("error")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'versioncheck');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
					$intError=1;
				}
			}
			// Upgrade NagiosQL DB
			if ($intError != 1) {
				while ($strCurrentVersion != BASE_VERSION AND $errmsg == "") {
				  echo "<li><label>".translate("Upgrading from version")." ".$strCurrentVersion." ".translate("to")."</label>";
				  $result=updateQL($strCurrentVersion, $_SESSION['SETS']['db']['server'], $_SESSION['SETS']['db']['port'], $_SESSION['SETS']['install']['db_privuser'], $_SESSION['SETS']['install']['db_privpwd'], $_SESSION['SETS']['db']['database'], $errmsg);
				  if ($result and $errmsg == "") {
						$result=get_current_version($_SESSION['SETS']['db']['server'], $_SESSION['SETS']['db']['port'], $_SESSION['SETS']['install']['db_privuser'], $_SESSION['SETS']['install']['db_privpwd'], $_SESSION['SETS']['db']['database'], $strCurrentVersion, $errmsg);
						echo "<span class='green'>".$strCurrentVersion."</span></li>\n";
				  } else {
						echo "<div id='updatecheck' class='tooltip'>".$errmsg."</div>\n";
						echo "<span class='red'>".translate("error")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'updatecheck');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
						$intError=1;
						break;
				  }
				}
			}
			// Converting database to UTF8
			if ($intError != 1) {		 
				echo "<li><label>".translate("Converting database to utf8 character set")."</label>";
				$result=convertDBUTF8($_SESSION['SETS']['db']['server'], $_SESSION['SETS']['db']['port'], $_SESSION['SETS']['install']['db_privuser'], $_SESSION['SETS']['install']['db_privpwd'], $_SESSION['SETS']['db']['database'], $errmsg);
				if ($result) {
					echo "<span class='green'>".translate("done")."</span></li>\n";
				} else {
					echo "<div id='dbconvertutf8' class='tooltip'>".$errmsg."</div>\n";
					echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'dbconvertutf8');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
					$intError = 1;
				}
			}
			// Converting database tables to UTF8
			if ($intError != 1) {		 
				echo "<li><label>".translate("Converting database tables to utf8 character set")."</label>";
				$result=convertDBTablesUTF8($_SESSION['SETS']['db']['server'], $_SESSION['SETS']['db']['port'], $_SESSION['SETS']['install']['db_privuser'], $_SESSION['SETS']['install']['db_privpwd'], $_SESSION['SETS']['db']['database'], $errmsg);
				if ($result) {
					echo "<span class='green'>".translate("done")."</span></li>\n";
				} else {
					echo "<div id='tableconvertutf8' class='tooltip'>".$errmsg."</div>\n";
					echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'tableconvertutf8');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
					$intError = 1;
				}
			}			
			// Converting database fields to UTF8
			if ($intError != 1) {		 
				echo "<li><label>".translate("Converting database fields to utf8 character set")."</label>";
				$result=convertDBFieldsUTF8($_SESSION['SETS']['db']['server'], $_SESSION['SETS']['db']['port'], $_SESSION['SETS']['install']['db_privuser'], $_SESSION['SETS']['install']['db_privpwd'], $_SESSION['SETS']['db']['database'], $errmsg);
				if ($result) {
					echo "<span class='green'>".translate("done")."</span></li>\n";
				} else {
					echo "<div id='fieldconvertutf8' class='tooltip'>".$errmsg."</div>\n";
					echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'fieldconvertutf8');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
					$intError = 1;
				}
			}	
			// Validating new database connection with existing db user
			if ($intError != 1) {		 
				echo "<li><label>".translate("Testing database connection to")." ".htmlspecialchars($_SESSION['SETS']['db']['database'], ENT_QUOTES, 'utf-8')."</label>";
				$link = @mysql_connect($_SESSION['SETS']['db']['server'].':'.$_SESSION['SETS']['db']['port'],$_SESSION['SETS']['db']['username'],$_SESSION['SETS']['db']['password']);
				if ($link) {
					$selectDB = @mysql_query("SELECT `id` FROM `".mysql_real_escape_string($_SESSION['SETS']['db']['database'])."`.`tbl_settings` LIMIT 1");
					if ($selectDB) {
						echo "<span class='green'>".translate("passed")."</span></li>\n";		 
					} else {
						echo "<div id='dbnewselect' class='tooltip'>".mysql_error()."</div>\n";
						echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'dbnewselect');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
						$intError = 1;
					}
					mysql_close($link);
				} else {
					echo "<div id='dbnewconnect' class='tooltip'>".mysql_error()."</div>\n";
					echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'dbnewconnect');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
					$intError = 1;
				}		
			}
					// Write settings to database
					if ($intError != 1) {
						echo "</ol>\n";
						echo "</fieldset>\n";
						echo "<fieldset>\n";
						echo "<legend>".translate("Deploy NagiosQL settings")."</legend>\n";
						echo "<ol>\n";
						echo "<li><label>".translate("Writing global settings to database")."</label>";
						if (writeSettingsDB($errmsg)) {
							echo "<span class='green'>".translate("done")."</span></li>\n";
						} else {
							echo "<div id='dbdeploysettings' class='tooltip'>".$errmsg."</div>\n";
							echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'dbdeploysettings');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
							$intError=1;
						}
					}
				 	// Write database settings to file
		      if ($intError != 1) {
							echo "<li><label>".translate("Writing database configuration to settings.php")."</label>";
						if (writeSettingsFile($errmsg)) {
								echo "<span class='green'>".translate("done")."</span></li>\n";
						} else {
								echo "<div id='deploysettings' class='tooltip'>".$errmsg."</div>\n";
								echo "<span class='red'>".translate("failed")."</span> <a href=\"javascript:void(0);\" onmouseover=\"createTip(this,'deploysettings');\"><img src='images/invalid.png' alt='invalid' title='invalid'></a></li>\n";
								$intError=1;
						}
		      }
					echo "</ol>\n";
					echo "</fieldset>\n";
       		break;
      }

    // Display database error
    echo "<br />\n";
    echo "<br />\n";
    echo "</form>";
    if ($intError != 1) {
      echo "<span class='red'>".translate("Please delete the install directory to continue!")."</span><br /><br />\n";
      echo "<div id=\"install-next\">\n";
      echo "<a href='../index.php'><img src='images/next.png' alt='finish' title='finish' border='0' /></a><br />".translate("Finish")."\n";
      echo "</div>\n";
    } else {
      echo "<div id=\"install-back\">\n";
      echo "<form action='' method='post'>\n";
      echo "<input type='hidden' name='step' value='2' />\n";
      echo "<input type='image' src='images/previous.png' value='Submit' alt='Submit' /><br />".translate("Back")."\n";
      echo "</form>\n";
      echo "</div>\n";
    }
    ?>
  </div>
</div>
<div id="ie_clearing"> </div>
