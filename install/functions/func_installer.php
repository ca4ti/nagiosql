<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project  : NagiosQL
// Component: Installer Functions
// Website  : http://www.nagiosql.org
// Date     : $LastChangedDate: 2011-04-10 16:59:17 +0200 (So, 10. Apr 2011) $
// Author   : $LastChangedBy: rouven $
// Version  : 3.1.1
// Revision : $LastChangedRevision: 1069 $
//
///////////////////////////////////////////////////////////////////////////////
// Security
if(preg_match('#' . basename(__FILE__) . '#', htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'utf-8'))) {
  die("You can't access this file directly!");
}
//
// Define global constants
//
define('BASE_PATH',realpath('.'));
define('BASE_URL', dirname($_SERVER["SCRIPT_NAME"]));
define('BASE_VERSION','3.1.1');
//
// Class to secure MySQL input
//
class MysqlStringEscaper {
    function __get($value) {
    	if (get_magic_quotes_gpc() == 1) $value = stripslashes($value);
    	return mysql_real_escape_string($value);
    }
}
//
// MySQL Import Functions
//
function getQueriesFromFile($file) {
  // import file line by line
  // and filter (remove) those lines, beginning with an sql comment token
  $file = array_filter(file($file),create_function('$line', 'return strpos(ltrim($line), "--") !== 0;'));
  // this is a list of SQL commands, which are allowed to follow a semicolon
  $keywords = array('ALTER', 'CREATE', 'DELETE', 'DROP', 'INSERT', 'REPLACE', 'SELECT', 'SET', 'TRUNCATE', 'UPDATE', 'USE');
  // create the regular expression
  $regexp = sprintf('/\s*;\s*(?=(%s)\b)/s', implode('|', $keywords));
  // split there
  $splitter = preg_split($regexp, implode("\r\n", $file));
  // remove trailing semicolon or whitespaces
  $splitter = array_map(create_function('$line','return preg_replace("/[\s;]*$/", "", $line);'),$splitter);
  // remove empty lines
  return array_filter($splitter, create_function('$line', 'return !empty($line);'));
}
//
// MySQL Create Database
//
function mysql_install_db($dbname, $dbsqlfile,&$errmsg) {
	if (get_magic_quotes_gpc() == 1) $dbname = stripslashes($dbname);
  $result = true;
  if(!mysql_select_db($dbname)) {
  	$str = new MysqlStringEscaper;
    $result = @mysql_query("CREATE DATABASE `".$str->$dbname."` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci");
    if(!$result) {
      $errmsg = translate("Could not create")." [".htmlspecialchars($dbname, ENT_QUOTES, 'utf-8')."] ".translate("database in MySQL");
      return false;
    }
    $result = mysql_select_db($dbname);
  }
  if(!$result) {
    $errmsg = translate("Could not select")." [".htmlspecialchars($dbname, ENT_QUOTES, 'utf-8')."] ".translate("database in MySQL");
    return false;
  }
  $queries = getQueriesFromFile($dbsqlfile);
  for ($i = 0, $ix = count($queries); $i < $ix; ++$i) {
    $sql = $queries[$i];
    if (!mysql_query($sql)) {
      $errmsg=mysql_error();
      return false;
    }
  }
  return $result;
}
//
// Write Settings to database
//
function writeSettingsDB(&$errmsg) {
	$str = new MysqlStringEscaper;
  $errmsg="";
  $inittbl = @mysql_connect($_SESSION['SETS']['db']['server'].':'.$_SESSION['SETS']['db']['port'],$_SESSION['SETS']['db']['username'],$_SESSION['SETS']['db']['password']);
  $selectdb = mysql_select_db($_SESSION['SETS']['db']['database']);
  $temp = @mysql_query("SET @previous_value := NULL;");
  $strSQL  = "INSERT INTO `tbl_settings` (`category`,`name`,`value`) VALUES";
  $strSQL .= "('path','protocol','".$str->$_SESSION['SETS']['path']['protocol']."'),";
  $strSQL .= "('path','tempdir','".str_replace("\\", "\\\\", $_SESSION['SETS']['path']['tempdir'])."'),";
  $strSQL .= "('data','locale','".$str->$_SESSION['SETS']['data']['locale']."'),";
  $strSQL .= "('data','encoding','".$str->$_SESSION['SETS']['data']['encoding']."'),";
  $strSQL .= "('security','logofftime','".$str->$_SESSION['SETS']['security']['logofftime']."'),";
  $strSQL .= "('security','wsauth','".$str->$_SESSION['SETS']['security']['wsauth']."'),";
  $strSQL .= "('common','pagelines','".$str->$_SESSION['SETS']['common']['pagelines']."'),";
  $strSQL .= "('common','seldisable','".$str->$_SESSION['SETS']['common']['seldisable']."'),";
  $strSQL .= "('db','version','". BASE_VERSION ."'),";
  $strSQL .= "('common','tplcheck','".$str->$_SESSION['SETS']['common']['tplcheck']."'),";
  $strSQL .= "('common','updcheck','".$str->$_SESSION['SETS']['common']['updcheck']."'),";
  $strSQL .= "('network','Proxy','".$str->$_SESSION['SETS']['network']['Proxy']."'),";
  $strSQL .= "('network','ProxyServer','".$str->$_SESSION['SETS']['network']['ProxyServer']."'),";
  $strSQL .= "('network','ProxyUser','".$str->$_SESSION['SETS']['network']['ProxyUser']."'),";
  $strSQL .= "('network','ProxyPasswd','".$str->$_SESSION['SETS']['network']['ProxyPasswd']."') ";
  $strSQL .= "ON DUPLICATE KEY UPDATE value = IF((@previous_value := value) <> NULL IS NULL, VALUES(value), NULL);";
  if (mysql_query($strSQL)) {
    return true;
  } else {
    $errmsg=mysql_error();
    return false;
  }
  $temp = mysql_query("SELECT @previous_note;");
}
//
// Insert initial NagiosQL User/Pass
//
function setQLUser($qluser,$qlpass,&$errmsg) {
	$str = new MysqlStringEscaper;
  $errmsg="";
  $returncode=true;
  $inittbl = @mysql_connect($_SESSION['SETS']['db']['server'].':'.$_SESSION['SETS']['db']['port'],$_SESSION['SETS']['db']['username'],$_SESSION['SETS']['db']['password']);
  $selectdb = mysql_select_db($_SESSION['SETS']['db']['database']);
  $strSQL  = "INSERT INTO `tbl_user` (`id`, `username`, `alias`, `password`, `access_rights`, `wsauth`, `active`, `nodelete`, `last_login`, `last_modified`) VALUES (1, '".$str->$qluser."', 'Administrator', md5('".$str->$qlpass."'), '11111111', '0', '1', '1', '', NOW());";
  if (mysql_query($strSQL)) {
    $returncode=true;
  } else {
    $errmsg=mysql_error();
    $returncode=false;
  }
  mysql_close($inittbl);
  return $returncode;
}
//
// Write DB Configuration to file
//
function writeSettingsFile(&$errmsg) {
  $errmsg="";
  $filSet = fopen(BASE_PATH ."/../config/settings.php","w");
  if ($filSet) {
    // Write Database Configuration into settings.php
    fwrite($filSet,"<?php\n");
    fwrite($filSet,"exit;\n");
    fwrite($filSet,"?>\n");
    fwrite($filSet,";///////////////////////////////////////////////////////////////////////////////\n");
    fwrite($filSet,";\n");
    fwrite($filSet,"; NagiosQL\n");
    fwrite($filSet,";\n");
    fwrite($filSet,";///////////////////////////////////////////////////////////////////////////////\n");
    fwrite($filSet,";\n");
    fwrite($filSet,"; Project  : NagiosQL\n");
    fwrite($filSet,"; Component: Database Configuration\n");
    fwrite($filSet,"; Website  : http://www.nagiosql.org\n");
    fwrite($filSet,"; Date     : ".date("F j, Y, g:i a")."\n");
    fwrite($filSet,"; Version  : ".BASE_VERSION."\n");
    fwrite($filSet,'; Revision : $LastChangedRevision: 1069 $'."\n");
    fwrite($filSet,";\n");
    fwrite($filSet,";///////////////////////////////////////////////////////////////////////////////\n");
    fwrite($filSet,"[db]\n");
    fwrite($filSet,"server       = ".$_SESSION['SETS']['db']['server']."\n");
    fwrite($filSet,"port         = ".$_SESSION['SETS']['db']['port']."\n");
    fwrite($filSet,"database     = ".$_SESSION['SETS']['db']['database']."\n");
    fwrite($filSet,"username     = ".$_SESSION['SETS']['db']['username']."\n");
    fwrite($filSet,"password     = ".$_SESSION['SETS']['db']['password']."\n");
    fclose($filSet);
    return true;
  } else {
    $errmsg=translate("Could not open settings.php in config directory for writing!");
    return false;
  }
}
//
// Detect current NagiosQL Version
//
function get_current_version($db_server, $db_port, $db_privusr, $db_privpwd, $db_name, &$strCurrentVersion, &$errmsg) {
  $return = true;
  $strCurrentVersion="";
  $errmsg = "";
  $link = @mysql_connect($db_server.':'.$db_port,$db_privusr,$db_privpwd);
  if ($link) {
    // Define current database version
    if (mysql_select_db($db_name,$link)) {
      // NagiosQL >= 1.0
      $query = mysql_query("SELECT `admin1` FROM `tbl_user` LIMIT 0,1",$link);
      if ($query) {
        $strCurrentVersion = "1.0";
        $return = false;
      } else {
        // NagiosQL >= 2.0
        $query = mysql_query("SELECT `wsauth` FROM `tbl_user` LIMIT 0,1",$link);
        if (!$query) {
          $query = mysql_query("SELECT `failure_prediction_enabled` FROM `tbl_host` LIMIT 0,1",$link);
          if ($query) {
            $strCurrentVersion = "2.0.2";
          } else {
            $strCurrentVersion = "2.0.0";
          }
        } else {
          // NagiosQL >= 3.0
          $result = mysql_result(mysql_query("SELECT `value` FROM `tbl_settings` WHERE `name` = 'version'",$link),0,0);
          if ($result) {
            $strCurrentVersion = $result;
          } else {
            $strCurrentVersion = "3.0.0 beta1";
          }
        }
      }
    } else {
      $errmsg=mysql_error();
      $return = false;
    }
    mysql_close($link);
  } else {
    $errmsg=mysql_error();
    $return = false;
  }
  return $return;
}
//
// Update NagiosQL
//
function updateQL($strCurrentVersion, $dbhost, $dbport, $dbprivuser, $dbprivpass, $dbname, &$errmsg) {
  $errmsg="";
  $result=true;
  switch ($strCurrentVersion) {
    case "3.1.1":
	    $result=true;
      return $result;
    case "3.1.0":
      $strFile="sql/update_310_311.sql";
      break; 
    case "3.1.0rc1":
      $strFile="sql/update_310rc1_310.sql";
      break; 
    case "3.1.0b3":
      $strFile="sql/update_310b3_310rc1.sql";
      break; 
	  case "3.1.0b2":
      $strFile="sql/update_310b2_310b3.sql";
      break; 
    case "3.1.0b1":
      $strFile="sql/update_310b1_310b2.sql";
      break;
	  case "3.0.4":
      $strFile="sql/update_304_310.sql";
      break;
    case "3.0.3":
      $strFile="sql/update_303_304.sql";
      break;
	  case "3.0.2":
      $strFile="sql/update_302_303.sql";
      break;
    case "3.0.1":
      $strFile="sql/update_301_302.sql";
      break;
    case "3.0.0":
      $strFile="sql/update_300_301.sql";
      break;
    case "3.0.0 rc1":
      $strFile="sql/update_300rc1_300.sql";
      break;
    case "3.0.0 beta2":
      $strFile="sql/update_300b2_300rc1.sql";
      break;
    case "3.0.0 beta1":
      $strFile="sql/update_300b1_300b2.sql";
      break;
    case "2.0.2":
      $strFile="sql/update_202_303.sql";
      break;
    case "2.0.0":
      $strFile="sql/update_200_202.sql";
      break;
    default:
      $result=false;
      $errmsg=translate("Unknown version!");
      break;
  }
  if (isset($strFile) AND file_exists($strFile) AND is_readable($strFile)) {
    $link=db_connect($dbhost,$dbport,$dbprivuser,$dbprivpass,"","",$errmsg);
    if ($link) {
      $result=mysql_install_db($dbname, $strFile, $errmsg);
      if (!$result or $errmsg != "") {
        $return=false;
      }
    } else {
      $return=false;
    }
  } else {
     if ($errmsg == "") $errmsg=translate("Could not access")." ".$strFile;
     $result=false;
  }
  return $result;
}
//
// Import sample data
//
function importSample($dbhost,$dbport,$dbuser,$dbpass,$dbname,$strFile,&$errmsg) {
  $errmsg="";
  if (isset($strFile) AND file_exists($strFile) AND is_readable($strFile)) {
    $link=db_connect($dbhost,$dbport,$dbuser,$dbpass,"","",$errmsg);
    if ($link) {
      $result=mysql_install_db($dbname, $strFile, $errmsg);
      if (!$result) {
        $return=false;
      }
    } else {
      $return=false;
    }
  } else {
     if ($errmsg == "") $errmsg=translate("Could not access")." ".$strFile;
     $result=false;
  }
  return $result;
}
//
// Database connectivity
//
function db_connect($host,$port,$user,$pass,$db,$charset,&$errmsg) {
	$str = new MysqlStringEscaper;
  $errmsg="";
  $dbh=@mysql_connect($host.($port==''?"":":".$port),$user,$pass,TRUE);
  if (!$dbh) {
    $errmsg=translate("Error").": ".translate("Cannot connect to the database.")." ".translate("MySQL Error").": ".mysql_error();
  }
  if (($dbh) AND $db != "") {
    $create=@mysql_query("CREATE DATABASE IF NOT EXISTS `".$str->$db."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
    if (!$create) {
      $errmsg=translate("Error").": ".mysql_error();
    } else {
      $res=@mysql_select_db($db, $dbh);
      if (!$res) {
         $errmsg=translate("Error").": ".translate("Cannot select the database.")." ".translate("MySQL Error").": ".mysql_error();
      }else{
      	 $res=@mysql_set_charset($charset, $dbh); 
         if (!$res) $errmsg=translate("Error").": ".translate("Cannot set")." CHARSET. ".translate("MySQL Error").": ".mysql_error();
      }
    }
  }
  return $dbh;
}
//
// Add NagiosQL database user
//
function addMySQLUser($dbhost, $dbport, $dbprivuser, $dbprivpass, $dbuser, $dbpass, &$errmsg) {
	$str = new MysqlStringEscaper;
  $errmsg="";
  $link=db_connect($dbhost,$dbport,$dbprivuser,$dbprivpass,"","",$errmsg);
  if ($errmsg == "") {
    $ipAddress = gethostbyname($_SERVER['SERVER_NAME']);
    if ($dbhost != "127.0.0.1" AND $dbhost != "localhost") {
      if ($ipAddress == "127.0.0.1") {
        $dbhost="%";
      } else {
        $dbhost = $ipAddress;
      }
    }
    $result=@mysql_query("GRANT USAGE ON *.* TO '".$str->$dbuser."'@'".$str->$dbhost."' IDENTIFIED BY '".$str->$dbpass."'");
    if (!$result) {
      $errmsg=mysql_error();
      $return=false;
    } else {
      $return=true;
    }
  } else {
    $return=false;
  }
  return $return;
}
//
// Set NagiosQL DB user permissions
//
function setMySQLPermission($dbhost, $dbport, $dbname, $dbprivuser, $dbprivpass, $dbuser, &$errmsg) {
	$str = new MysqlStringEscaper;
  $errmsg="";
  $link=db_connect($dbhost,$dbport,$dbprivuser,$dbprivpass,"","",$errmsg);
  if ($errmsg == "") {
    $ipAddress = gethostbyname($_SERVER['SERVER_NAME']);
    if ($dbhost != "127.0.0.1" AND $dbhost != "localhost") {
      if ($ipAddress == "127.0.0.1") {
        $dbhost="%";
      } else {
        $dbhost = $ipAddress;
      }
    }
    $result=@mysql_query("GRANT SELECT,INSERT,UPDATE,DELETE ON `".$str->$dbname."`.* TO '".$str->$dbuser."'@'".$str->$dbhost."'");
    if (!$result) {
      $errmsg=mysql_error();
      $return=false;
    } else {
      $return=true;
    }
  } else {
    $return=false;
  }
  return $return;
}
//
// Flush MySQL privileges
//
function flushMySQLPrivileges($dbhost, $dbport, $dbprivuser, $dbprivpass, &$errmsg) {
  $errmsg="";
  $link=db_connect($dbhost,$dbport,$dbprivuser,$dbprivpass,"","",$errmsg);
  if ($errmsg == "") {
    $result=@mysql_query("FLUSH PRIVILEGES");
    if (!$result) {
      $errmsg=mysql_error();
      $return=false;
    } else {
      $return=true;
    }
  } else {
    $return=false;
  }
  return $return;
}
//
// Drop MySQL Database
//
function dropMySQLDB($dbhost, $dbport, $dbprivuser, $dbprivpass, $dbname, &$errmsg) {
	$str = new MysqlStringEscaper;
  $errmsg="";
  $link=db_connect($dbhost,$dbport,$dbprivuser,$dbprivpass,"","",$errmsg);
  if ($errmsg == "") {
    $result=@mysql_query("DROP DATABASE IF EXISTS `".$str->$dbname."`");
    if (!$result) {
      $errmsg=mysql_error();
      $return=false;
    } else {
      $return=true;
    }
  } else {
    $return=false;
  }
  return $return;
}
//
// Convert database to UTF-8 character set
//
function convertDBUTF8($dbhost, $dbport, $dbprivuser, $dbprivpass, $dbname, &$errmsg) {
	$str = new MysqlStringEscaper;
  $errmsg="";
  $link=db_connect($dbhost,$dbport,$dbprivuser,$dbprivpass,"","",$errmsg);
  if ($errmsg == "") {
    $result=@mysql_query("ALTER DATABASE `".$str->$dbname."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
    if (!$result) {
			$errmsg=mysql_error();
			$return=false;
    } else {
			$return=true;
		}
  } else {
    $return=false;
  }
  return $return;
}
//
// Convert database table to UTF-8 character set
//
function convertDBTablesUTF8($dbhost, $dbport, $dbprivuser, $dbprivpass, $dbname, &$errmsg) {
	$str = new MysqlStringEscaper;
  $errmsg="";
  $link=@mysql_connect($dbhost.':'.$dbport,$dbprivuser,$dbprivpass);
  if ($link) {
  	$selectdb=mysql_select_db($dbname,$link);
  	$result = @mysql_query("SHOW TABLES FROM `".$str->$dbname."`", $link);
		if (!$result) {
			$errmsg=mysql_error();
			$return=false;
		} else {
			$errmsg=mysql_error();
			while ($row = mysql_fetch_row($result)) {
				$converttable=@mysql_query("ALTER TABLE `".$str->$row[0]."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;",$link);
				$errmsg=mysql_error();
				$return=true;
				if ($errmsg != "") {
					$return=false;
					break;
				}
			}
		}
  } else {
    $return=false;
  }
  return $return;
}
//
// Convert database fields to UTF-8 character set
//
function convertDBFieldsUTF8($dbhost, $dbport, $dbprivuser, $dbprivpass, $dbname, &$errmsg) {
	$str = new MysqlStringEscaper;
	$convert_to   = 'utf8_general_ci';
	$character_set= 'utf8';
	$errmsg="";
  $link=@mysql_connect($dbhost.':'.$dbport,$dbprivuser,$dbprivpass);
	if ($link) {
		set_time_limit(0);
		$selectdb = mysql_select_db($dbname,$link);
		$rs_tables = mysql_query(" SHOW TABLES ",$link);
		while ($row_tables = mysql_fetch_row($rs_tables)) {
			$table = $str->$row_tables[0];
			$rs = mysql_query(" SHOW FULL FIELDS FROM `$table` ",$link);
			while ( $row = mysql_fetch_assoc($rs) ) {
				if ( $row['Collation'] == '' || $row['Collation'] == $convert_to ) continue;
				// Is the field allowed to be null?
				if ( $row['Null'] == 'YES' ) {
					$nullable = ' NULL ';
				} else {
					$nullable = ' NOT NULL ';
				}
				// Does the field default to null, a string, or nothing?
				if ( $row['Default'] === NULL && $row['Null'] == 'YES' ) {
					$default = " DEFAULT NULL ";
				} elseif ( $row['Default'] != '' ) {
				    $default = " DEFAULT '".$str->$row['Default']."'";
				} else {
					$default = '';
				}
				// sanity check and fix (wrong combination of 'default value' and 'NULL-flag' detected and fixed)
				if ($nullable == ' NOT NULL ' && $default == ' DEFAULT NULL ') $default = '';
				// Don't alter INT columns: no collations, and altering them drops autoincrement values
				if (strpos($row['Type'], 'int') !== false) {
					$show_alter_field = False;
				} else {
					$show_alter_field = True;
				}
				// Alter field collation:
				// ALTER TABLE `tbl_ql` CHANGE `field` `field` CHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
				if ($show_alter_field) {
					$field = $str->$row['Field'];
					$query=mysql_query("ALTER TABLE `$table` CHANGE `$field` `$field` $row[Type] CHARACTER SET $character_set COLLATE $convert_to $nullable $default;",$link);
				}
			}
		}
		$return=true;
  } else {
  	$errmsg=mysql_error();
    $return=false;
  }
  return $return;
}
//
// Read settings file
//
function parseIniFile($iIniFile) {
	$aResult  =
	$aMatches = array();
	$a = &$aResult;
	$s = '\s*([[:alnum:]_\- \*]+?)\s*'; 
	preg_match_all('#^\s*((\['.$s.'\])|(("?)'.$s.'\\5\s*=\s*("?)(.*?)\\7))\s*(;[^\n]*?)?$#ms', @file_get_contents($iIniFile), $aMatches, PREG_SET_ORDER);
	foreach ($aMatches as $aMatch) {
  	if (empty($aMatch[2])) {
      	$a [$aMatch[6]] = $aMatch[8];
    } else {  
				$a = &$aResult [$aMatch[3]];
		}
  }
  	return $aResult;
}
//
// Define temporary directory
//
if ( !function_exists('sys_get_temp_dir') ) {
  // Based on http://www.phpit.net/
  // article/creating-zip-tar-archives-dynamically-php/2/
  function sys_get_temp_dir() {
    // Try to get from environment variable
    if ( !empty($_ENV['TMP']) ) {
      return realpath( $_ENV['TMP'] );
    } elseif ( !empty($_ENV['TMPDIR']) ) {
      return realpath( $_ENV['TMPDIR'] );
    } elseif ( !empty($_ENV['TEMP']) ){
      return realpath( $_ENV['TEMP'] );
    } else {
      // Detect by creating a temporary file
      // Try to use system's temporary directory
      // as random name shouldn't exist
      $temp_file = tempnam( md5(uniqid(rand(), TRUE)), '' );
      if ( $temp_file ) {
        $temp_dir = realpath( dirname($temp_file) );
        unlink( $temp_file );
        return $temp_dir;
      } else {
        return FALSE;
      }
    }
  }
}
//
// Define Server Protocol
//
function get_protocol() {
	if (substr_count($_SERVER['SERVER_PROTOCOL'],"HTTPS")) {
	  $protocol = "https";
	} else {
	  $protocol = "http";
	}
	return $protocol;
}
?>
