<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Project   : NagiosQL
// Component : Visualization Class
// Website   : http://www.nagiosql.org
// Date      : $LastChangedDate: 2010-10-25 15:45:55 +0200 (Mo, 25 Okt 2010) $
// Author    : $LastChangedBy: rouven $
// Version   : 3.0.4
// Revision  : $LastChangedRevision: 827 $
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Klasse: Allgemeine Darstellungsfunktionen
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Behandelt sämtliche Funktionen, zur Darstellung der Applikation notwendig
// sind
//
// Name: nagvisual
//
// Klassenvariabeln:
// -----------------
// $arrSettings:  Mehrdimensionales Array mit den globalen Konfigurationseinstellungen
// $intDomainId:  Domänen Id
// $myDBClass:    Datenbank Klassenobjekt
//
// Externe Funktionen
// ------------------
//
//
///////////////////////////////////////////////////////////////////////////////////////////////
class nagvisual {
    // Klassenvariabeln deklarieren
    var $arrSettings;         // Wird im Klassenkonstruktor gefüllt
  var $intDomainId;         // Wird im Klassenkonstruktor gefüllt
  var $myDBClass;           // Wird in der Datei prepend_adm.php definiert

    ///////////////////////////////////////////////////////////////////////////////////////////
  //  Klassenkonstruktor
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Tätigkeiten bei Klasseninitialisierung
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function nagvisual() {
    // Globale Einstellungen einlesen
    $this->arrSettings = $_SESSION['SETS'];
    if (isset($_SESSION['domain'])) $this->intDomainId = $_SESSION['domain'];
  }
    ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Position festlegen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Ermittelt die aktuelle Position innerhalb der Menustruktur und gibt diese als
  //  Infozeile zurück.
  //
  //  Übergabeparameter:  $intMain  ID des ausgewählten Hauptmenueintrages
  //            $intSub   ID des ausgewählten Submenueintrages (0, wenn keiner)
  //            $strTop   Der Root Knoten als String (optional)
  //
  //  Returnwert:     Positionsstring
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function getPosition($intMain,$intSub = 0,$strTop = "") {
    $strPosition = "";
    $strSQLMain = "SELECT `item`, `link` FROM `tbl_mainmenu` WHERE `id` = $intMain";
    $booReturn = $this->myDBClass->getDataArray($strSQLMain,$arrDataMain,$intDataCountMain);
    if (($booReturn != false) && ($intDataCountMain != 0)) {
      $strMainLink = $this->arrSettings['path']['root'].$arrDataMain[0]['link'];
      $strMain = gettext($arrDataMain[0]['item']);
      if ($strTop != "") {
        $strPosition .= "<a href='".$_SESSION['SETS']['path']['root']."admin.php'>".$strTop."</a> -> ";
      }
      $strPosition .= "<a href='".$strMainLink."'>".gettext($strMain)."</a>";
    }
    if ($intSub != 0) {
      $strSQLSub  = "SELECT `item`, `link` FROM `tbl_submenu` WHERE `id_main` = $intMain AND `id` = $intSub";
      $booReturn = $this->myDBClass->getDataArray($strSQLSub,$arrDataSub,$intDataCountSub);
      if (($booReturn != false) && ($intDataCountSub != 0)) {
        $strSubLink = $this->arrSettings['path']['root'].$arrDataSub[0]['link'];
        $strSub = gettext($arrDataSub[0]['item']);
        if ($strSub != "") {
          $strPosition .= " -> <a href='".$strSubLink."'>".gettext($strSub)."</a>";
        }
      }
    }
    return $strPosition;
  }
    ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Hauptmenu anzeigen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Gibt das Hauptmenu aus
  //
  //  Übergabeparameter:  $intMain  ID des ausgewählten Hauptmenueintrages
  //            $intSub   ID des ausgewählten Submenueintrages (0, wenn kein)
  //            $intMenu  ID der Menugruppe
  //
  //  Returnwert:     0 bei Erfolg / 1 bei Misserfolg
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function getMenu($intMain,$intSub,$intMenu) {
    //
    // URL für sichtbares/unsichtbares Menu modifizieren
    // =================================================
    $strQuery = str_replace("menu=visible&","",$_SERVER['QUERY_STRING']);
    $strQuery = str_replace("menu=invisible&","",$strQuery);
    $strQuery = str_replace("menu=visible","",$strQuery);
    $strQuery = str_replace("menu=invisible","",$strQuery);
    if ($strQuery != "") {
      $strURIVisible   = str_replace("&","&amp;",$_SERVER['PHP_SELF']."?menu=visible&".$strQuery);
      $strURIInvisible = str_replace("&","&amp;",$_SERVER['PHP_SELF']."?menu=invisible&".$strQuery);
    } else {
      $strURIVisible   = $_SERVER['PHP_SELF']."?menu=visible";
      $strURIInvisible = $_SERVER['PHP_SELF']."?menu=invisible";
    }
    //
    // Menupunkte aus Datenbank auslesen und in Arrays speichern
    // =========================================================
    $strSQLMain = "SELECT `id`, `item`, `link` FROM `tbl_mainmenu` WHERE `menu_id` = $intMenu ORDER BY `order_id`";
    $strSQLSub  = "SELECT `id`, `item`, `link`, `access_rights` FROM `tbl_submenu` WHERE `id_main` = $intMain ORDER BY `order_id`";
    // Datensätze für das Hauptmenu in einem numerischen Array speichern
    $booReturn = $this->myDBClass->getDataArray($strSQLMain,$arrDataMain,$intDataCountMain);
    if (($booReturn != false) && ($intDataCountMain != 0)) {
      $y=1;
      for ($i=0;$i<$intDataCountMain;$i++) {
        $arrMainLink[$y] = $this->arrSettings['path']['root'].$arrDataMain[$i]['link'];
        $arrMainId[$y]   = $arrDataMain[$i]['id'];
        $arrMain[$y]   = gettext($arrDataMain[$i]['item']);
        $y++;
      }
    } else {
      return(1);
    }
    // Datensätze für das Untermenu in einem numerischenArray speichern
    $booReturn = $this->myDBClass->getDataArray($strSQLSub,$arrDataSub,$intDataCountSub);
    if (($booReturn != false) && ($intDataCountSub != 0)) {
      $y=1;
      for ($i=0;$i<$intDataCountSub;$i++) {
        // Menupunkt nur in Array übertragen, wenn der Benutzer über die nötigen Rechte verfügt
        if ($this->checkKey($_SESSION['keystring'],$arrDataSub[$i]['access_rights']) == 0) {
          $arrSubLink[$y] = $this->arrSettings['path']['root'].$arrDataSub[$i]['link'];
          $arrSubID[$y]   = $arrDataSub[$i]['id'];
          $arrSub[$y]     = gettext($arrDataSub[$i]['item']);
          $y++;
        }
      }
    }
    //
    // Ausgabe der kompletten Menustruktur
    // ===================================
    if (!(isset($_SESSION['menu'])) || ($_SESSION['menu'] != "invisible")) {
      // Menu ist eingeblendet
      echo "<td width=\"150\" align=\"center\" valign=\"top\">\n";
      echo "<table cellspacing=\"1\" class=\"menutable\">\n";
      // Jeden Hauptmenueintrag abarbeiten
      for ($i=1;$i<=count($arrMain);$i++) {
        echo "<tr>\n";
        if ($arrMainId[$i] == $intMain) {
          echo "<td class=\"menuaktiv\"><a href=\"".$arrMainLink[$i]."\">".$arrMain[$i]."</a></td>\n</tr>\n";
          // Falls Untermenueintrag existiert
          if (isset($arrSub)) {
            echo "<tr>\n<td class=\"menusub\">\n";
            // Jeden Untermenueintrag abarbeiten
            for ($y=1;$y<=count($arrSub);$y++) {
              if ((isset($arrSubLink[$y])) && ($arrSubLink[$y] != "")) {
                if ($arrSubID[$y] == $intSub) {
                  echo "<a class=\"menulink\" href=\"".$arrSubLink[$y]."\"><b>".$arrSub[$y]."</b></a><br>\n";
                } else {
                  echo "<a class=\"menulink\" href=\"".$arrSubLink[$y]."\">".$arrSub[$y]."</a><br>\n";
                }
              }
            }
            echo "</td>\n</tr>\n";
          }
        } else {
          echo "<td class=\"menuinaktiv\"><a href=\"".$arrMainLink[$i]."\">".$arrMain[$i]."</a></td>\n</tr>\n";
        }
      }
      echo "</table>\n";
      echo "<br><a href=\"$strURIInvisible\" class=\"menulinksmall\">[".gettext('Hide menu')."]</a>\n";
      echo "</td>\n";
    } else {
      // Menu ist ausgeblendet
      echo "<td valign=\"top\">\n";
      echo "<a href=\"$strURIVisible\"><img src=\"".$this->arrSettings['path']['root']."images/menu.gif\" alt=\"".gettext('Show menu')."\" border=\"0\" ></a>\n";
      echo "</td>\n";
    }
    return(0);
  }
  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Zugriffsschlüssel umwandeln
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Wandelt den Zugriffsschlüsselstring in ein Array um
  //
  //  Übergabeparameter:  $strKey     Array mit den Sprachdefinitionen
  //
  //  Returnwert:     $arrKey     Array mit den Schlüsselwerten
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function getKeyArray($strKey) {
    // Übergabeschlüssel leer?
    if (($strKey == "") || (strlen($strKey) != 8)) $strKey = "00000000";
    // Schlüsselstring verarbeiten
    for($i=0;$i<8;$i++) {
      $arrKey[] = substr($strKey,$i,1);
    }
    return($arrKey);
  }

  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Übergabewert "null verarbeiten
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Wandelt den Übergabewert "null" in -1 um oder belässt ihn
  //
  //  Übergabeparameter:  $strKey     Sring mit dem Übergabewert
  //
  //  Returnwert:             Verarbeiteter Sting
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function checkNull($strKey) {
    // Ist der Übergabewert "null"
    if (strtoupper($strKey) == "NULL") {
      return("-1");
    }
    return($strKey);
  }

  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Ein "/" am Ende des Strings anhängen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Hängt ein "/" am Ende eine Strings an und entfernt doppelte("/") aus diesem
  //
  //  Übergabeparameter:  $strPath    Sring mit dem Übergabewert
  //
  //  Returnwert:             Verarbeiteter Sting
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function addSlash($strPath) {
    if ($strPath == "") return("");
    $strPath = $strPath."/";
    $strPath = str_replace("//","/",$strPath);
    $strPath = str_replace("//","/",$strPath);
    return ($strPath);
  }


  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Berechtigung prüfen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Prüft die Berechtigung über den Zugriffsschlüssel
  //
  //  Übergabeparameter:  $strUserKey   Zugriffsschlüssel des Benutzers
  //            $strAccessKey Benötigter Zugriffsschlüssel
  //
  //  Returnwert:     0/1       0 wenn Zugriff ok / 1 wenn Zugriff verweigert
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function checkKey($strUserKey,$strAccessKey) {
    // Schlüssel in Array wandeln
    $arrUserKey   = $this->getKeyArray($strUserKey);
    $arrAccessKey = $this->getKeyArray($strAccessKey);
    // Array vergleichen
    $intReturn = 0;
    for ($i=0;$i<8;$i++) {
      // Kein Schlüssel benötigt
      if ($arrAccessKey[$i] == 0) continue;
      // Schlüssel vorhanden
      if (($arrAccessKey[$i] == 1) && ($strUserKey[$i] == 1)) continue;
      return(1);
    }
    return(0);
  }
    ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Seitenlinks zusammenstellen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Erstellt einen String, der die Links für die einzelnen Seiten zum anwählen enthält
  //
  //  Übergabeparameter:  $strSite    Link zur Seite
  //            $intCount   Anzahl Datensätze
  //            $chkLimit   Aktuelles Limit (Seitenlink fettschreiben)
  //            $chkSelOrderBy  OrderBy-String (für Services Seite)
  //
  //  Returnwert:     String mit den Seitenlinks
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function buildPageLinks($strSite,$intCount,$chkLimit,$chkSelOrderBy="") {
        $strPages = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n<tr>\n";
    // String definieren Teil 1
    if ($chkLimit > 0) {
      $intPrev   = $chkLimit-$this->arrSettings['common']['pagelines'];
      $strPages .= "<td valign=\"middle\" align=\"left\" width=\"25\"><a href=\"".$strSite;
      $strPages .= "?limit=$intPrev\"><img src=\"".$this->arrSettings['path']['root'];
      $strPages .= "images/left.gif\" width=\"18\" height=\"18\" border=\"0\" alt=\"Prev\" title=\"Prev\">";
      $strPages .= "</a></td><td valign=\"middle\" align=\"center\">".gettext('Pages:')." [ ";
    } else {
      $strPages .= "<td valign=\"middle\" align=\"left\" width=\"25\"><img src=\"".$this->arrSettings['path']['root'];
      $strPages .= "images/pixel.gif\" width=\"18\" height=\"18\" border=\"0\" alt=\"-\" title=\"-\">";
      $strPages .= "</td><td valign=\"middle\" align=\"center\">".gettext('Pages:')." [ ";
    }
    // In Schritten von 15 die Datensätze in Seiten unterteilen
    $y     = 1;
    $intNext = 0;
    for($i=0;$i<$intCount;$i=$i+$this->arrSettings['common']['pagelines']) {
      // Aktuelle Seitennummer fett schreiben
        if ($i == $chkLimit) {
        $strNumber = "<b>$y</b>";
        $intNext = $chkLimit + $this->arrSettings['common']['pagelines'];
      } else {
        $strNumber = $y;
      }
      if ($chkSelOrderBy == "") {
        $strPages .= "<a href=\"".$strSite."?limit=$i\">".$strNumber."</a> ";
      } else {
        $strOrderBy = rawurlencode($chkSelOrderBy);
        $strPages .= "<a href=\"".$strSite."?limit=$i&orderby=$chkSelOrderBy\">".$strNumber."</a> ";
      }
      $y++;
    }
    if ($intNext < $intCount) {
      $strPages .= " ] </td><td valign=\"middle\" align=\"right\" width=\"25\"><a href=\"".$strSite;
      $strPages .= "?limit=$intNext\"><img src=\"".$this->arrSettings['path']['root'];
      $strPages .= "images/right.gif\" width=\"18\" height=\"18\" border=\"0\" alt=\"Prev\" title=\"Prev\">";
      $strPages .= "</a></td>\n</tr>\n</table>\n";
    } else {
      $strPages .= " ] </td><td valign=\"middle\" align=\"right\" width=\"25\"><img src=\"".$this->arrSettings['path']['root'];
      $strPages .= "images/pixel.gif\" width=\"18\" height=\"18\" border=\"0\" alt=\"-\" title=\"-\">";
      $strPages .= "</td>\n</tr>\n</table>\n";
    }
    // Linkstring zurückgeben falls mehr als eine Seite angezeigt wird
    if ($y > 2) {
      return($strPages);
    } else {
      return("");
    }
  }
  
  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Alle Services entsprechend einer Hostliste suchen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Sucht alle Services, die allen Hosts in der angegebenen Liste gemeinsam sind.
  //
  //  Übergabeparameter:  	$strHostlist 			Kommagetrennte Hostliste
  //
  //  Returnwert:     		Array mit Service IDs
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function getServicesHost($strHostlist,&$arrServices) {
    // TODO -> servicetemplate ergänzen !!!
	// Alle Hosts abarbeiten
	if ($strHostlist != "") {
		$arrHosts = explode(",",$strHostlist);
		$arrTemp  = array();
		foreach ($arrHosts AS $elem) {
			// Services, welche direkt einem Host zugeteilt sind
			$strSQL    = "SELECT `id`, `service_description` FROM `tbl_service`
						  LEFT JOIN `tbl_lnkServiceToHost` ON `id` = `idMaster`
						  WHERE `active`='1'
							AND `config_id`=".$this->intDomainId."
							AND `idSlave` = ".$elem."
						  UNION	
						  SELECT `id`, `service_description` FROM `tbl_service`
                      	  LEFT JOIN `tbl_lnkServiceToHostgroup` ON `id` = `tbl_lnkServiceToHostgroup`.`idMaster`
					  	  LEFT JOIN `tbl_lnkHostgroupToHost` ON `tbl_lnkServiceToHostgroup`.`idSlave` = `tbl_lnkHostgroupToHost`.`idMaster`
                      	  WHERE `active`='1'
                        	AND `config_id`=".$this->intDomainId."
                        	AND `tbl_lnkHostgroupToHost`.`idSlave` = ".$elem."
						  UNION
						  SELECT `id`, `service_description` FROM `tbl_service`
                      	  LEFT JOIN `tbl_lnkServiceToHostgroup` ON `id` = `tbl_lnkServiceToHostgroup`.`idMaster`
					  	  LEFT JOIN `tbl_lnkHostToHostgroup` ON `tbl_lnkServiceToHostgroup`.`idSlave` = `tbl_lnkHostToHostgroup`.`idSlave`
                      	  WHERE `active`='1'
                        	AND `config_id`=".$this->intDomainId."
                        	AND `tbl_lnkHostToHostgroup`.`idMaster` = ".$elem;
			$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
			if ($intDataCount != 0) {
				if (count($arrTemp) == 0) {
					$arrTemp = $arrData;
				} else {
					$arrTemp1 = array();
					foreach ($arrTemp AS $elem) {
						if (in_array($elem,$arrData)) $arrTemp1[] = $elem;
					}
					$arrTemp = $arrTemp1;
				}
			} else {
				$arrServices = array();
				return false;
			}
		}
		$arrServices = $arrTemp;
		return true;
	} else {
		$arrServices = array();
		return false;
	}
  }

  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Alle Services entsprechend einer Hostgruppenliste suchen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Sucht alle Services, die allen Hosts in der angegebenen Liste gemeinsam sind.
  //
  //  Übergabeparameter:  	$strHostgrouplist 		Kommagetrennte Hostgruppenliste
  //
  //  Returnwert:     		Array mit Service IDs
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function getServicesHostgroup($strHostgrouplist,&$arrServices) {

	// Alle Hosts abarbeiten
	if ($strHostgrouplist != "") {
		$arrHostgroups = explode(",",$strHostgrouplist);
		$arrTemp  = array();
		foreach ($arrHostgroups AS $elem) {
			// Services, welche direkt einem Host zugeteilt sind
			$strSQL    = "SELECT `id`, `service_description` FROM `tbl_service`
						  LEFT JOIN `tbl_lnkServiceToHostgroup` ON `id` = `idMaster`
						  WHERE `active`='1'
							AND `config_id`=".$this->intDomainId."
							AND `idSlave` = ".$elem;
			$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
			if ($intDataCount != 0) {
				if (count($arrTemp) == 0) {
					$arrTemp = $arrData;
				} else {
					$arrTemp1 = array();
					foreach ($arrTemp AS $elem) {
						if (in_array($elem,$arrData)) $arrTemp1[] = $elem;
					}
					$arrTemp = $arrTemp1;
				}
			} else {
				$arrServices = array();
				return false;
			}
		}
		$arrServices = $arrTemp;
		return true;
	} else {
		$arrServices = array();
		return false;
	}
  }

  ///////////////////////////////////////////////////////////////////////////////////////////
  //  Funktion: Auswahlfeld aufbauen
  ///////////////////////////////////////////////////////////////////////////////////////////
  //
  //  Baut ein Auswahlfeld innerhalb eines Formulars auf
  //
  //  Übergabeparameter:  $strTable   Tabellenname aus dem die einzufüllenden Daten stammen
  //            $strTabField  Feldname der Tabelle aus dem die einzufüllenden Daten stammen
  //            $objTemplate  Templatename
  //            $strParseVar  Templateschlüssel für Datenwert [{DAT_XXX}]
  //            $strParseGroup  Templategruppe des Auswahlfeldes [$templ->parse(xxx)]
  //            $intDataId    Datensatz ID (Mastertabelle)
  //            $strLinkTable Linktabellenname
  //            $intSelMode   ModusId der Auswahl 0=nichts 1=Relationen 2=* -1=null
  //                    Wenn die Linktabelle leer übergeben wird, enthält $intSelMode
  //                    die Id der Slavetabelle
  //            $intModeId    0=nur Daten, 1=mit Leerzeile, 2=mit Leerzeile und *, 3=mit *
  //            $intSkipId    Einzelne Id die nicht angezeigt werden darf
  //            $intOption    Optionswert zur allgemeinen Verwendung
  //            $strPostKey   $_POST-Schlüssel bei Refresh
  //
  //  Returnwert:     0 bei Erfolg, 1 bei Misserfolg
  //
  ///////////////////////////////////////////////////////////////////////////////////////////
  function parseSelect($strTable,$strTabField,$strParseVar,$strParseGroup,&$objTemplate,$intDataId,$strLinkTable,$intSelMode=0,$intModeId=0,$intSkipId=0,$intOption=0,$strPostKey="") {
    // Version festlegen
    $this->myConfigClass->getConfigData("version",$intVersion);
    // Daten aus der Haupttabelle laden
    if ($intSkipId != 0) {$strWhere = "AND `id` <> $intSkipId";} else {$strWhere = "";}
    // Bei den Befehlsdefinitionen misc oder check unterscheiden
    if (($strTable == "tbl_command") && (($intOption == 1) || ($intOption == 3))) {
      $strWhere = "AND (`command_type` = 0 OR `command_type` = 1)";
    }
    if (($strTable == "tbl_command") && (($intOption == 2) || ($intOption == 4))) {
      $strWhere = "AND (`command_type` = 0 OR `command_type` = 2)";
    }
    if (($intOption != 7) && ($intOption != 8) && ($intOption != 9) && ($intOption != 10)) {
      $strSQL  = "SELECT `id`, `".$strTabField."` FROM `".$strTable."` WHERE `active`='1' AND `config_id`=".$this->intDomainId." 
	              $strWhere AND `".$strTabField."` <> '' AND `".$strTabField."` IS NOT NULL ORDER BY `".$strTabField."`";
      $booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
    } else {
      if ($intOption == 7) {
        $arrHost    = $_SESSION['servicedependency']['arrHostDepend'];
        $arrHostgroup = $_SESSION['servicedependency']['arrHostgroupDepend'];
      } else if ($intOption == 8) {
        $arrHost    = $_SESSION['servicedependency']['arrHost'];
        $arrHostgroup = $_SESSION['servicedependency']['arrHostgroup'];
      } else if ($intOption == 9) {
        $arrHost    = $_SESSION['serviceescalation']['arrHost'];
        $arrHostgroup = $_SESSION['serviceescalation']['arrHostgroup'];
      } else if ($intOption == 10) {
        $arrHost[]    = $_SESSION['serviceextinfo']['arrHost'];
        $arrHostgroup = "";
      } else {
        return(1);
      }
      //if ((is_array($arrHost) && in_array("*",$arrHost)) || (is_array($arrHostgroup) && in_array("*",$arrHostgroup))) {
		//if (is_array($arrHost)) {
		  //$booReturn = $this->getServicesHost('%',$arrServListHost);
		  //$arrData = $arrServListHost;
		  //$intDataCount = count($arrData);
		  /*$strSQL  = "SELECT `id` FROM `tbl_host` WHERE `active`='1' AND `config_id`=".$this->intDomainId;
          $booReturn = $this->myDBClass->getDataArray($strSQL,$arrTemp,$intDCTemp);
          foreach($arrTemp AS $elem) {
            $arrTempHost[] = $elem['id'];
          }
          $strSQL  = "SELECT `id`, `".$strTabField."`, count(`idSlave`) AS `counter` FROM `".$strTable."`
                LEFT JOIN `tbl_lnkServiceToHost` ON `id` = `tbl_lnkServiceToHost`.`idMaster`
                WHERE `active`='1'
                  AND `config_id`=".$this->intDomainId."
                  AND `tbl_lnkServiceToHost`.`idSlave` IN (".implode(",",$arrTempHost).")
                  GROUP BY `".$strTabField."`
                  HAVING `counter` = $intDCTemp
                  ORDER BY `".$strTabField."`";
          $booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
        }
        if (is_array($arrHostgroup)) {
          $strSQL  = "SELECT `id` FROM `tbl_hostgroup` WHERE `active`='1' AND `config_id`=".$this->intDomainId;
          $booReturn = $this->myDBClass->getDataArray($strSQL,$arrTemp,$intDCTemp);
          foreach($arrTemp AS $elem) {
            $arrTempHostgroup[] = $elem['id'];
          }
          $strSQL  = "SELECT `id`, `".$strTabField."`, count(`idSlave`) AS `counter` FROM `".$strTable."`
                LEFT JOIN `tbl_lnkServiceToHostgroup` ON `id` = `tbl_lnkServiceToHostgroup`.`idMaster`
                WHERE `active`='1'
                  AND `config_id`=".$this->intDomainId."
                  AND `tbl_lnkServiceToHostgroup`.`idSlave` IN (".implode(",",$arrTempHostgroup).")
                  GROUP BY `".$strTabField."`
                  HAVING `counter` = $intDCTemp
                  ORDER BY `".$strTabField."`";
          $booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
        }
      } else {*/
		// Service Dependency/Escalation Auswahl entsprechend POST Parameter
        if ($intVersion != 3) {
		  if (is_array($arrHost)) {
            $intCounter1 = count($arrHost);
          } else {
            $intCounter1 = 0;
          }
          if ($intCounter1 != 0) {
            if (in_array("*",$arrHost)) {
				$strSQL  = "SELECT `id`, `".$strTabField."`, count(`idSlave`) AS `counter` FROM `".$strTable."`
					  LEFT JOIN `tbl_lnkServiceToHost` ON `id` = `tbl_lnkServiceToHost`.`idMaster`
					  WHERE `active`='1'
						AND `config_id`=".$this->intDomainId."
						AND `tbl_lnkServiceToHost`.`idSlave` IN (%)
						GROUP BY `".$strTabField."`
						HAVING `counter` = $intCounter1
						ORDER BY `".$strTabField."`";
			} else {
				$strSQL  = "SELECT `id`, `".$strTabField."`, count(`idSlave`) AS `counter` FROM `".$strTable."`
					  LEFT JOIN `tbl_lnkServiceToHost` ON `id` = `tbl_lnkServiceToHost`.`idMaster`
					  WHERE `active`='1'
						AND `config_id`=".$this->intDomainId."
						AND `tbl_lnkServiceToHost`.`idSlave` IN (".implode(",",$arrHost).")
						GROUP BY `".$strTabField."`
						HAVING `counter` = $intCounter1
						ORDER BY `".$strTabField."`";
			}
            $booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
          } else {
            $booReturn = false;
          }
        } else {
			// TEST THIS!!!
		  $booReturn 	  = true;
		  $arrData   	  = array();
		  $intHostOk 	  = 0;
		  $intHostgroupOk = 0;  
		  // Services nach Host suchen
		  if (is_array($arrHost)) {
			if (in_array("*",$arrHost)) {
				$booReturn = $this->getServicesHost("%",$arrServListHost);
			} else {
				$booReturn = $this->getServicesHost(implode(",",$arrHost),$arrServListHost);
			}
			$intHostOk = 1;
		  } 
  		  // Services nach Hostgruppe suchen
		  if (is_array($arrHostgroup)) {
		  	if (in_array("*",$arrHostgroup)) {
				$booReturn = $this->getServicesHostgroup("%",$arrServListHostgroup);
			} else {
				$booReturn = $this->getServicesHostgroup(implode(",",$arrHostgroup),$arrServListHostgroup);
			}
			$intHostgroupOk = 1;
		  } 
		  if (($intHostOk == 1) && ($intHostgroupOk == 0)) {
		  	$arrData = $arrServListHost;
		  } else if (($intHostOk == 0) && ($intHostgroupOk == 1)) {
		  	$arrData = $arrServListHostgroup;
		  } else if (($intHostOk == 1) && ($intHostgroupOk == 1)) {
		  	$arrTemp = array();
			foreach ($arrServListHost AS $elem) {
				if (in_array($elem,$arrServListHostgroup)) $arrTemp[] = $elem;
			}
		  	$arrData = $arrTemp;
		  }
		  $intDataCount = count($arrData);
			// TEST THIS !!!
		  /*	
          if (is_array($arrHostgroup)) {
            $intCounter1 = count($arrHostgroup);
          } else {
            $intCounter1 = 0;
          }
          if (is_array($arrHost)) {
            $intCounter2 = count($arrHost);
          } else {
            $intCounter2 = 0;
          }
          if ($intCounter1 != 0) {
            $strSQL = "SELECT DISTINCT `id` FROM `tbl_host`
                   LEFT JOIN `tbl_lnkHostToHostgroup` ON `id` = `tbl_lnkHostToHostgroup`.`idMaster`
                   LEFT JOIN `tbl_lnkHostgroupToHost` ON `id` = `tbl_lnkHostgroupToHost`.`idSlave`
                   WHERE (`tbl_lnkHostgroupToHost`.`idMaster` IN (".implode(",",$arrHostgroup).")
                    OR `tbl_lnkHostToHostgroup`.`idSlave` IN (".implode(",",$arrHostgroup)."))
                   AND `active`='1'
                   AND `config_id`=".$this->intDomainId;
			$booReturn = $this->myDBClass->getDataArray($strSQL,$arrDataHostgroups,$intDCHostgroups);
            $arrDataHg2 = "";
            if ($intDCHostgroups != 0) {
				foreach ($arrDataHostgroups AS $elem) {
				  $arrHostgroupList[] = $elem['id'];
				}
			 } else {
				$arrHostgroupList[] = 0;
			 }
            if ($intCounter2 != 0) {
			  $strSQL = "SELECT `id` FROM `tbl_host` WHERE `active`='1' AND `config_id`=".$this->intDomainId;
              $booReturn = $this->myDBClass->getDataArray($strSQL,$arrDataHost,$intDCHost);
              $arrHostIdList = "";
              foreach ($arrDataHost AS $elem) {
                if ((($arrHostIdList == "") || !in_array($elem['id'],$arrHostIdList)) &&
                  (in_array($elem['id'],$arrHostgroupList) || in_array($elem['id'],$arrHost))) {
                  $arrHostIdList[] = $elem['id'];
                }
              }
            } else {
			  $arrHostIdList = $arrHostgroupList;
            }
			$intCounter = count($arrHostIdList);
            $strSQL  = "SELECT `id`, `".$strTabField."`, count(`idSlave`) AS `counter` FROM `".$strTable."`
                  LEFT JOIN `tbl_lnkServiceToHost` ON `id` = `tbl_lnkServiceToHost`.`idMaster`
                  WHERE `active`='1'
                    AND `config_id`=".$this->intDomainId."
                    AND `tbl_lnkServiceToHost`.`idSlave` IN (".implode(",",$arrHostIdList).")
                    GROUP BY `".$strTabField."`
					HAVING `counter` = $intCounter
				  UNION
				  SELECT `id`, `".$strTabField."`, count(`idSlave`) AS `counter` FROM `".$strTable."`
                    LEFT JOIN `tbl_lnkServiceToHostgroup` ON `id` = `tbl_lnkServiceToHostgroup`.`idMaster`
                    WHERE `active`='1'
                    AND `config_id`=".$this->intDomainId."
                    AND `tbl_lnkServiceToHostgroup`.`idSlave` IN (".implode(",",$arrHostgroup).")
					GROUP BY `".$strTabField."`
                    HAVING `counter` = $intCounter
			      UNION 
			      SELECT `id`, `".$strTabField."`, $intCounter FROM `".$strTable."`
                    WHERE `active`='1'
                    AND `config_id`=".$this->intDomainId."
					AND `".$strTable."`.`hostgroup_name` = 2
			      UNION SELECT `id`, `".$strTabField."`, $intCounter FROM `".$strTable."`
                  WHERE `active`='1'
                    AND `config_id`=".$this->intDomainId."
					AND `".$strTable."`.`host_name` = 2
					GROUP BY 2
                    ORDER BY 2";
			$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
//            $strSQL  = "SELECT `id`, `".$strTabField."`, count(`idSlave`) AS `counter` FROM `".$strTable."`
//                  LEFT JOIN `tbl_lnkServiceToHostgroup` ON `id` = `tbl_lnkServiceToHostgroup`.`idMaster`
//                  WHERE `active`='1'
//                    AND `config_id`=".$this->intDomainId."
//                    AND `tbl_lnkServiceToHostgroup`.`idSlave` IN (".implode(",",$arrHostgroup).")
//                    GROUP BY `".$strTabField."`
//                    HAVING `counter` = $intCounter
//                    ORDER BY `".$strTabField."`";
//			$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData2,$intDataCount);
//			$arrData = array_merge($arrData,$arrData2);		
				
          } else if ($intCounter2 != 0) {
            $strSQL  = "SELECT `id`, `".$strTabField."`, count(`idSlave`) AS `counter` FROM `".$strTable."`
                  LEFT JOIN `tbl_lnkServiceToHost` ON `id` = `tbl_lnkServiceToHost`.`idMaster`
                  WHERE `active`='1'
                    AND `config_id`=".$this->intDomainId."
                    AND `tbl_lnkServiceToHost`.`idSlave` IN (".implode(",",$arrHost).")
					GROUP BY `".$strTabField."`
                    HAVING `counter` = $intCounter2
			      UNION SELECT `id`, `".$strTabField."`, $intCounter2 FROM `".$strTable."`
                  WHERE `active`='1'
                    AND `config_id`=".$this->intDomainId."
					AND `".$strTable."`.`host_name` = 2
					GROUP BY `".$strTabField."`
					ORDER BY 2";
			$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
          } else if ($intOption == 10) {
            $strSQL  = "SELECT `id`, `".$strTabField."` FROM `".$strTable."`
                  LEFT JOIN `tbl_lnkServiceToHost` ON `id` = `tbl_lnkServiceToHost`.`idMaster`
                  WHERE `active`='1'
                    AND `config_id`=".$this->intDomainId."
                    AND `tbl_lnkServiceToHost`.`idSlave` IN ($arrHost)
                    ORDER BY `".$strTabField."`";
			$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
          } else {
            $booReturn = false;
          }
          */
		//}
      }
    }
    if (($booReturn == false) || ($intDataCount == 0)) {
      // HTML Validität - eine Option schreiben
      if ($intVersion != 3) $objTemplate->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
      $objTemplate->setVariable($strParseVar,"&nbsp;");
      $objTemplate->setVariable($strParseVar."_ID",0);
      $objTemplate->parse($strParseGroup);
      return(1);
    }
    if (($intSelMode == 1) && ($strLinkTable != "")) {
      // Auswahlen selektieren
      if ($intOption != 6){
        if (($strPostKey == "") || (!isset($_POST[$strPostKey]))) {
          $strSQL   = "SELECT `idSlave` FROM `".$strLinkTable."` WHERE `idMaster`=$intDataId";
          $booReturn  = $this->myDBClass->getDataArray($strSQL,$arrDataSelected,$intDCSelected);
          if ($intDCSelected != 0) {
            foreach($arrDataSelected AS $elem) {
              $arrSelect[] = $elem['idSlave'];
            }
          }
        } else {
          $arrSelect    = $_POST[$strPostKey];
          $intDCSelected  = count($_POST[$strPostKey]);
        }
      } else {
        $strSQL   = "SELECT `idSlaveH`, `idSlaveHG`, `idSlaveS` FROM `".$strLinkTable."` WHERE `idMaster`=$intDataId";
        $booReturn  = $this->myDBClass->getDataArray($strSQL,$arrDataSelected,$intDCSelected);
        if ($intDCSelected != 0) {
          foreach($arrDataSelected AS $elem) {
            $arrSelect[] = $elem['idSlaveH']."::".$elem['idSlaveHG']."::".$elem['idSlaveS'];
          }
        }
      }
    }
	//echo $intModeId;
    // Im Modus 1 und 2 Leerzeile einfügen
    if (($intModeId == 1) || ($intModeId == 2)) {
      $objTemplate->setVariable($strParseVar,"&nbsp;");
      $objTemplate->setVariable($strParseVar."_ID",0);
      if ($intVersion != 3) $objTemplate->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
      $objTemplate->parse($strParseGroup);
    }
    // Im Modus 2 und 3 einen "*" einfügen
    if (($intModeId == 2) || ($intModeId == 3)) {
      $objTemplate->setVariable($strParseVar,"*");
      $objTemplate->setVariable($strParseVar."_ID","*");
      if ($intVersion != 3) $objTemplate->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
      if ($intSelMode == 2) {
        $objTemplate->setVariable($strParseVar."_SEL","selected");
      }
      if (($strPostKey != "") && (isset($_POST[$strPostKey])) && in_array("*",$arrSelect)) {
        $objTemplate->setVariable($strParseVar."_SEL","selected");
      }
      $objTemplate->parse($strParseGroup);
    }
    // Bei Spezialoption "null" eintragen
    if (($intOption == 3) || ($intOption == 4) || ($intOption == 5)) {
      $objTemplate->setVariable($strParseVar,"null");
      $objTemplate->setVariable($strParseVar."_ID",-1);
      if ($intVersion != 3) $objTemplate->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
      if ($intSelMode == -1) $objTemplate->setVariable($strParseVar."_SEL","selected");
      $objTemplate->parse($strParseGroup);
    }
    if ($intOption != 6) {
      // Datensätze eintragen
      foreach ($arrData AS $elem) {
        $objTemplate->setVariable($strParseVar,$elem[$strTabField]);
        $objTemplate->setVariable($strParseVar."_ID",$elem['id']);
        if ($intVersion != 3) $objTemplate->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
        if (($intSelMode == 1) && ($strLinkTable != "") && ($intDCSelected != 0) && in_array($elem['id'],$arrSelect)) {
          $objTemplate->setVariable($strParseVar."_SEL","selected");
        }
        if (($strLinkTable == "") && ($elem['id'] == $intSelMode) && !isset($_POST[$strPostKey])) {
          $objTemplate->setVariable($strParseVar."_SEL","selected");
        }
        if (($strLinkTable == "") && ($strPostKey != "") && isset($_POST[$strPostKey]) && ($elem['id'] == $_POST[$strPostKey])) {
          $objTemplate->setVariable($strParseVar."_SEL","selected");
        }
        $objTemplate->parse($strParseGroup);
      }
    } else {
      // Datensätze eintragen (Servicegruppen)
      foreach ($arrData AS $elem) {
        // Hostnamen holen
        $strSQL = "SELECT `idSlave`, `host_name` FROM `tbl_lnkServiceToHost` LEFT JOIN `tbl_host` ON `id` = `idSlave` WHERE `idMaster` = ".$elem['id']." ORDER BY `host_name`";
        $booReturn = $this->myDBClass->getDataArray($strSQL,$arrDataHost,$intDCHost);
        if ($intDCHost != "") {
          foreach ($arrDataHost AS $hostdata) {
            $arrTemp[] = array ( "name"  =>  "H:".$hostdata['host_name'].",".$elem[$strTabField],
                       "value" =>  $hostdata['idSlave']."::0::".$elem['id']);
          }
        }
        // Hostgruppen holen
        $strSQL = "SELECT `idSlave`, `hostgroup_name` FROM `tbl_lnkServiceToHostgroup` LEFT JOIN `tbl_hostgroup` ON `id` = `idSlave` WHERE `idMaster` = ".$elem['id']." ORDER BY `hostgroup_name`";
        $booReturn = $this->myDBClass->getDataArray($strSQL,$arrDataHostgroup,$intDCHostgroup);
        if ($intDCHostgroup != "") {
          foreach ($arrDataHostgroup AS $hostgroupdata) {
            $arrTemp[] = array ( "name"  =>  "HG:".$hostgroupdata['hostgroup_name'].",".$elem[$strTabField],
                       "value" =>  "0::".$hostgroupdata['idSlave']."::".$elem['id']);
          }
        }
      }
      //var_dump($arrSelect);
	  if (isset($arrTemp) && is_array($arrTemp)) {
        asort($arrTemp);
        foreach ($arrTemp AS $elem) {
          $objTemplate->setVariable($strParseVar,$elem['name']);
          $objTemplate->setVariable($strParseVar."_ID",$elem['value']);
          if ($intVersion != 3) $objTemplate->setVariable("CLASS_20_MUST_ONLY","class=\"inpmust\"");
          if (($intSelMode == 1) && ($strLinkTable != "") && ($intDCSelected != 0) && in_array($elem['value'],$arrSelect)) {
            $objTemplate->setVariable($strParseVar."_SEL","selected");
          }
          $objTemplate->parse($strParseGroup);
        }
      }
    }
    return(0);
  }
}
?>