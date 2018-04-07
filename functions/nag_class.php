<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2006 by Martin Willisegger / nagiosql_v2@wizonet.ch
//
// Projekt:	NagiosQL Applikation
// Author :	Martin Willisegger
// Datum:	12.03.2007
// Zweck:	Administrationsklassen
// Datei:	functions/nag_class.php
// Version: 2.00.00 (Internal)
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
// Version 2.00.00 (Internal)
// Datum   12.03.2007 wim
//
// Name: nagvisual
//
// Klassenvariabeln:
// -----------------
// $arrSettings:	Mehrdimensionales Array mit den globalen Konfigurationseinstellungen
// $arrLanguage:	Mehrdimensionales Array mit den globalen Sprachstrings
// $myDBClass:		Datenbank Klassenobjekt
// $myDataClass:	NagiosQL Datenklasse
// $resTemplate		Objektvariable zum abspeichern der externen Templateklasse
// $arrWorkdata		Temoräres Arbeitsarray
// $strTempValue1	Temporärer Wert 1
// $strTempValue2	Temporärer Wert 2
// $arrTempValue1	Temporäres Array 1
// $intTabA			Tabellen ID der A Tabelle
// $intTabA_id		Datensatz ID innder halb der A Tabelle
//
// Externe Funktionen
// ------------------
// 
// 	
///////////////////////////////////////////////////////////////////////////////////////////////
class nagvisual {
    // Klassenvariabeln deklarieren
    var $arrSettings;					// Wird im Klassenkonstruktor gefüllt
	var $arrLanguage;					// Wird in der Datei prepend_adm.php gefüllt
	var $myDBClass;						// Wird in der Datei prepend_adm.php definiert
	var $myDataClass;					// Wird in der Datei prepend_adm.php definiert
	var $resTemplate;					// Wird vor dem Aufruf der Funktion gefüllt
	var $arrWorkdata   	 = "";			// Wird vor dem Aufruf der Funktion gefüllt
	var $strTempValue1   = "";			// Wird vor dem Aufruf der Funktion gefüllt
	var $strTempValue2   = "";			// Wird vor dem Aufruf der Funktion gefüllt
	var $arrTempValue1   = "";			// Wird Klassenintern verwendet
	var $intTabA		 = 0;			// Wird vor dem Aufruf der Funktion gefüllt
	var $intTabA_id		 = 0;			// Wird vor dem Aufruf der Funktion gefüllt
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Klassenkonstruktor
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.00.00 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Tätigkeiten bei Klasseninitialisierung
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function nagvisual() {
		// Globale Einstellungen einlesen
		$this->arrSettings = $_SESSION['SETS'];
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Sprachdatei laden
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.00.00 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Lädt die Sprachdefinition in das Spracharray
	//
	//  Übergabeparameter:	$strVersion		Version der Sprache (lang_de, lang_en)
	//
	//  Rückgabewert:		$LANG			Array mit den Sprachdefinitionen
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function getLanguage($strVersion,&$LANG) {
		// Datenbankabfrage
		$strSQL    = "SELECT category,keyword,$strVersion FROM tbl_language ORDER by category,keyword,$strVersion";
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrLanguage,$intDataCount);
		if (($booReturn == false) || ($intDataCount == 0)) return(1); 
		// Werte in das Spracharray schreiben
		foreach ($arrLanguage AS $elem) {
			// Sprachdefinition leer oder NULL
			if (($elem[$strVersion] == "") || ($elem[$strVersion] == "NULL")) {
				$elem[$strVersion] = "- no language definition -";
			}
			$LANG[$elem['category']][$elem['keyword']] = $elem[$strVersion];
		}
		return(0);
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Hauptmenu anzeigen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.00.00 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Gibt das Hauptmenu aus
	//
	//  Übergabeparameter:	$intMain	ID des ausgewählten Hauptmenueintrages
	//						$intSub		ID des ausgewählten Submenueintrages (0, wenn kein)
	//						$intMenu	ID der Menugruppe
	//
	//  Returnwert:			0 bei Erfolg / 1 bei Misserfolg
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
			$strURIVisible 	 = $_SERVER['PHP_SELF']."?menu=visible";
			$strURIInvisible = $_SERVER['PHP_SELF']."?menu=invisible";	
		}
		//
		// Menupunkte aus Datenbank auslesen und in Arrays speichern
		// =========================================================
		$strSQLMain = "SELECT id, item, link FROM tbl_mainmenu WHERE menu_id = $intMenu ORDER BY order_id";
		$strSQLSub  = "SELECT id, item, link, access_rights FROM tbl_submenu WHERE id_main = $intMain ORDER BY order_id";
		// Datensätze für das Hauptmenu in einem numerischen Array speichern
		$booReturn = $this->myDBClass->getDataArray($strSQLMain,$arrDataMain,$intDataCountMain);
		if (($booReturn != false) && ($intDataCountMain != 0)) {
			$y=1;
			for ($i=0;$i<$intDataCountMain;$i++) {
				$arrMainLink[$y] = $this->arrSettings['path']['root'].$arrDataMain[$i]['link'];
				$arrMainId[$y]   = $arrDataMain[$i]['id'];
				$arrMain[$y] 	 = $this->arrLanguage['menu'][$arrDataMain[$i]['item']];
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
					$arrSub[$y]     = $this->arrLanguage['menu'][$arrDataSub[$i]['item']];
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
			echo "<table cellspacing=\"5\" class=\"menutable\">\n";
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
			echo "<br><a href=\"$strURIInvisible\" class=\"menulinksmall\">[".$this->arrLanguage['menu']['disable']."]</a>\n";
			echo "</td>\n";
		} else {
			// Menu ist ausgeblendet
			echo "<td valign=\"top\">\n"; 
			echo "<a href=\"$strURIVisible\"><img src=\"".$this->arrSettings['path']['root']."images/menu.gif\" alt=\"".$this->arrLanguage['menu']['enable']."\" border=\"0\"></a>\n"; 
			echo "</td>\n";
		}
		return(0);
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Zugriffsschlüssel umwandeln
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.00.00 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Wandelt den Zugriffsschlüsselstring in ein Array um
	//
	//  Übergabeparameter:	$strKey 		Array mit den Sprachdefinitionen
	//
	//  Returnwert:			$arrKey			Array mit den Schlüsselwerten
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
	//  Funktion: Berechtigung prüfen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.00.00 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Prüft die Berechtigung über den Zugriffsschlüssel
	//
	//  Übergabeparameter:	$strUserKey 	Zugriffsschlüssel des Benutzers
	//	 					$strAccessKey	Benötigter Zugriffsschlüssel
	//
	//  Returnwert:			0/1				0 wenn Zugriff ok / 1 wenn Zugriff verweigert
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
	//  Version 2.00.00 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Erstellt einen String, der die Links für die einzelnen Seiten zum anwählen enthält
	//
	//  Übergabeparameter:	$strSite		Link zur Seite
	//						$intCount		Anzahl Datensätze
	//						$chkLimit		Aktuelles Limit (Seitenlink fettschreiben)
	//						$chkSelOrderBy	OrderBy-String (für Services Seite)
	//
	//  Returnwert:			String mit den Seitenlinks
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function buildPageLinks($strSite,$intCount,$chkLimit,$chkSelOrderBy="") {
		// String definieren Teil 1
		$strPages = $this->arrLanguage['admintable']['pages']." [ ";
		// In Schritten von 15 die Datensätze in Seiten unterteilen
		$y = 1;
		for($i=0;$i<$intCount;$i=$i+$this->arrSettings['common']['pagelines']) {
			// Aktuelle Seitennummer fett schreiben
		    if ($i == $chkLimit) {$strNumber = "<b>$y</b>";} else {$strNumber = $y;}
			if ($chkSelOrderBy == "") {
				$strPages .= "<a href=\"".$strSite."?limit=$i\">".$strNumber."</a> ";
			} else {
				$strOrderBy = rawurlencode($chkSelOrderBy);
				$strPages .= "<a href=\"".$strSite."?limit=$i&orderby=$chkSelOrderBy\">".$strNumber."</a> ";
			} 
			$y++;
		}
		$strPages .= " ] ";
		// Linkstring zurückgeben
		return($strPages);
	}

    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Auswahlfeld aufbauen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version 2.00.00 (Internal)
	//  Datum   12.03.2007 wim
	//  
	//  Baut ein Auswahlfeld innerhalb eines Formulars auf
	//
	//  Übergabeparameter:	$strTable		Tabellenname aus dem die einzufüllenden Daten stammen
	//						$strTabB_field	Feldname der Tabelle aus dem die einzufüllenden Daten stammen
	//						$strParseVar	Templateschlüssel für Datenwert [{DAT_XXX}]
	//						$strParseGroup	Templategruppe des Auswahlfeldes [$templ->parse(xxx)]
	//						$strTabA_field	Feldname der Tabelle in das einzufüllenden Daten geschrieben werden
	//						$intRelation	Typ der Tabellenverknüpfung 1 = 1:1 / 2 = 1:n
	//						$intModeId		0=nur Daten, 1=mit Leerzeile, 2=mit Leerzeile und *, 3=mit *
	//						$intSkipId		ID eines Eintrages, der nicht angezeigt werden darf
	//						$arrSelectId	Array mit allen IDs der Einträge, die selektiert werden müssen
	//						
	//	Klassenvariabeln:	$this->resTemplate		Templateobjekt
	//						$this->strTempValue1	Modus ("modify","add" etc.)
	//						$this->strTempValue2	Erster Datenbankeintrag (Rückgabewert)	
	//						$this->arrWorkdata[]	Aktuelle Formulardaten
	//						$this->intTabA			ID der Tabelle A
	//						$this->intTabA_id		ID des aktuellen Eintrages in der Tabelle A	
	//
	//  Returnwert:			0 bei Erfolg, 1 bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////	
	function parseSelectNew($strTable,$strTabB_field,$strParseVar,$strParseGroup,$strTabA_field,$intRelation=1,$intModeId=0,$intSkipId=0,$arrSelectId=0) {
		// Variabeln deklarieren
		$this->arrTempValue1 = "";
		$intTabB = $this->myDataClass->tableID($strTable);
		$strSQL  = "SELECT id, $strTabB_field FROM $strTable WHERE active='1' ORDER BY $strTabB_field";
		// Datensätze aus der Datenbank holen
		$booReturn = $this->myDBClass->getDataArray($strSQL,$arrData,$intDataCount);
		if (($booReturn == false) || ($intDataCount == 0)) return(1);
		
		// Im Modus 1 und 2 Leerzeile einfügen
		if (($intModeId == 1) || ($intModeId == 2)) {
			$this->resTemplate->setVariable($strParseVar,"");
			$this->resTemplate->setVariable($strParseVar."_ID",0);
			if ((isset($this->arrWorkdata[$strTabA_field]) && $this->arrWorkdata[$strTabA_field] == 0) && (!is_array($arrSelectId) || ($arrSelectId[0] == ""))) {
				//$this->resTemplate->setVariable($strParseVar."_SEL","selected");
				$this->arrTempValue1[] = "0";
			}
			// Refreshregel
			if ((is_array($arrSelectId)) && (in_array("0",$arrSelectId)) && (count($arrSelectId) == 1)) {
				$this->resTemplate->setVariable($strParseVar."_SEL","selected");
				$this->arrTempValue1[] = "0";
			}		
			$this->resTemplate->parse($strParseGroup);
		}				
		// Im Modus 2 und 3 einen "*" einfügen
		if (($intModeId == 2) || ($intModeId == 3)) {
			$this->resTemplate->setVariable($strParseVar,"*");
			$this->resTemplate->setVariable($strParseVar."_ID","*");
			if ((isset($this->arrWorkdata[$strTabA_field]) && $this->arrWorkdata[$strTabA_field] == 2) && (!is_array($arrSelectId))) {
				$this->resTemplate->setVariable($strParseVar."_SEL","selected");
				$this->arrTempValue1[] = "*";
			}
			// Refreshregel
			if ((is_array($arrSelectId)) && (in_array("*",$arrSelectId)) && (count($arrSelectId) == 1)) {
				$this->resTemplate->setVariable($strParseVar."_SEL","selected");
				$this->arrTempValue1[] = "*";
			}		
			$this->resTemplate->parse($strParseGroup);
		}
		// Datensätze eintragen
		for ($i=0;$i<$intDataCount;$i++) {		
			// Wert- und Textfeld füllen
			if (($intSkipId == 0) || ($intSkipId != $arrData[$i]['id'])) {
				if ($i == 0) $this->strTempValue2 = $arrData[$i]['id'];
				$this->resTemplate->setVariable($strParseVar,$arrData[$i][$strTabB_field]);
				$this->resTemplate->setVariable($strParseVar."_ID",$arrData[$i]['id']);
				//echo $arrData[$i]['id']."<br>";
				// Refreshregel
				if ((is_array($arrSelectId)) && (in_array($arrData[$i]['id'],$arrSelectId))) {
					$this->resTemplate->setVariable($strParseVar."_SEL","selected");
					$this->arrTempValue1[] = $arrData[$i]['id'];
				}				
				// Gewählte Felder markieren
				if (($this->strTempValue1 == "modify") && ($this->arrWorkdata[$strTabA_field] != "")) {
					if ($intRelation == 1) {
						// Ausnahmeregel für Hostcommand [HOST_EXCEPT]
						if (($strParseGroup == "hostcommand") && ($strTabA_field == "check_command")) {
							$arrID = explode("!",$this->arrWorkdata[$strTabA_field]);
							if ($arrID['0'] == $arrData[$i]['id']) {
								$this->resTemplate->setVariable($strParseVar."_SEL","selected");
								$this->arrTempValue1[] = $arrData[$i]['id'];
							}
						}
						// Ausnahmeregel für Servicecommand [SERVICE_EXCEPT]
						if (($strParseGroup == "servicecommand") && ($strTabA_field == "check_command")) {
							$arrID = explode("!",$this->arrWorkdata[$strTabA_field]);
							if ($arrID['0'] == $arrData[$i]['id']) {
								$this->resTemplate->setVariable($strParseVar."_SEL","selected");
								$this->arrTempValue1[] = $arrData[$i]['id'];
							}
						}
						// Standardregel		
						if ($this->arrWorkdata[$strTabA_field] == $arrData[$i]['id']) {
							$this->resTemplate->setVariable($strParseVar."_SEL","selected");
							$this->arrTempValue1[] = $arrData[$i]['id'];
						}
					} else if ($intRelation == 2) {
						//echo "TabA: ".$this->intTabA." - TabB: ".$intTabB." - TabA_id ".$this->intTabA_id." - TabA_field ".$strTabA_field." - TabB_id ".$arrData[$i]['id']."<br>";
						//echo $this->myDataClass->findRelation($this->intTabA,$intTabB,$this->intTabA_id,$strTabA_field,$arrData[$i]['id'])." - ".$arrData[$i]['id']."<br>";
						if ($this->myDataClass->findRelation($this->intTabA,$intTabB,$this->intTabA_id,$strTabA_field,$arrData[$i]['id']) == 1) {
							//echo "Hier<br>";
							$this->resTemplate->setVariable($strParseVar."_SEL","selected");
							$this->arrTempValue1[] = $arrData[$i]['id'];
						}
					}
				}		
				$this->resTemplate->parse($strParseGroup);	
			}	
		}
	}
}
?>