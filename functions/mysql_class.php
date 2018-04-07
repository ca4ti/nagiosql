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
// Zweck:	MySQL Datenbank Klasse
// Datei:	functions/mysql_class.php
// Version: 2.00.00 (Internal)
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Klasse: Allgemeine Datenbankfunktionen MySQL
//
///////////////////////////////////////////////////////////////////////////////////////////////
//
// Behandelt sämtliche Funktionen, die für den Datenaustausch mit einem MySQL Server
// nötig sind
//
// Version:	2.00.00 (Internal)
// Datum:	12.03.2007
//
// Name: mysqldb
//
// Klassenvariabeln:	$arrSettings	Array mit den Applikationseinstellungen
// -----------------	$strDBError		Datenbankfehlermeldungen
//						$error			Boolean - Fehler aufgetreten true/false
//						$strDBId 		Datenbankverbindungs ID
//
// Externe Funktionen
// ------------------	getFieldData(...)		Einzelnes Datenfeld abfragen
//						getSingleDataset(...)	Einzelner Datensatz abfragen
//						getDataArray(...)		Mehrere Datensätze abfragen						
// 						insertData(...) 		Daten einfügen/modifizieren/löschen
//						countRows(...)			Anzahl Datenzeilen zählen
// 	
///////////////////////////////////////////////////////////////////////////////////////////////
class mysqldb {
    // Klassenvariabeln deklarieren
    var $arrSettings;
	var $strDBError   = "";
	var $error        = false;
	var $strDBId      = "";
	var $intLastId	  = 0;
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Klassenkonstruktor
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Tätigkeiten bei Klasseninitialisierung
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function mysqldb() {
		// Globale Einstellungen einlesen
		$this->arrSettings = $_SESSION['SETS'];
		// Mit NagiosQL Datenbank verbinden
		$this->getDatabase($this->arrSettings['db']);		
	}

	///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Verbindung mit der Datenbank herstellen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Verbindet mit dem Datenbankserver und wählt eine Datenbank aus
	//
	//  Übergabeparameter:	$arrSettings	Array mit den Verbindungsdaten
	//										-> Key server 	= Servername
	//										-> Key username = Benutzername
	//										-> Key password	= Passwort
	//										-> Key database = Datenbank	
	//
	//  Returnwert:			true bei Erfolg / false bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function getdatabase($arrSettings) {
		$this->dbconnect($arrSettings['server'],$arrSettings['username'],$arrSettings['password']);
		if ($this->error == true) {
			return false;
		}
		$this->dbselect($arrSettings['database']);
		if ($this->error == true) {
			return false;
		}
		return true;	
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Einzelnes Datenfeld holen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Ruft mehrere Datensätze ab und speichert diese in ein nummerisches Array
	//
	//  Übergabeparameter:	$strSQL			SQL Statement	
	//
	//  Returnwert:			Feldwert bei Erfolg, false bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function getFieldData($strSQL) {
		// SQL Statement an Server senden
		$resQuery = mysql_query($strSQL);
		// Fehlerbehandlung
		if ($resQuery && (mysql_num_rows($resQuery) != 0) && (mysql_error() == "")) {
			// Feldwert zurückgeben
			return mysql_result($resQuery,0,0);
		} else if (mysql_error() != "") {
			$this->strDBError 	= mysql_error();
			$this->error   		= true;
			return false;			
		}
		return("");
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Einzelner Datensatz abfragen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Ruft einen einzelnen Datensatz ab und gibt diesen als assoziiertes Array zurück
	//
	//  Übergabeparameter:	$strSQL			SQL Statement
	//
	//  Rückgabewert:		$arrDataset		Datenarray als assoziieres Array	
	//
	//  Returnwert:			true bei Erfolg / false bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function getSingleDataset($strSQL,&$arrDataset) {
		$arrDataset = "";
		// SQL Statement an Server senden
		$resQuery = mysql_query($strSQL);
		// Fehlerbehandlung
		if ($resQuery && (mysql_num_rows($resQuery) != 0) && (mysql_error() == "")) {
			// Array abfüllen
			$arrDataset = mysql_fetch_array($resQuery,MYSQL_ASSOC);
			return true;
		} else if (mysql_error() != "") {
			$this->strDBError 	= mysql_error();
			$this->error   		= true;
			return false;			
		}
		return true;
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Mehrere Datensätze holen und in Array speichern
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Ruft mehrere Datensätze ab und speichert diese in ein nummerisches Array
	//
	//  Übergabeparameter:	$strSQL			SQL Statement
	//
	//  Rückgabewert:		$arrDataset		Datenarray als assoziieres Array
	//						$intDataCount	Anzahl Datensätze	
	//
	//  Returnwert:			true bei Erfolg / false bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function getDataArray($strSQL,&$arrDataset,&$intDataCount) {
		$arrDataset   = "";
		$intDataCount = 0;
		// SQL Statement an Server senden
		$resQuery = mysql_query($strSQL);
		// Fehlerbehandlung
		if ($resQuery && (mysql_num_rows($resQuery) != 0) && (mysql_error() == "")) {
			$intDataCount = mysql_num_rows($resQuery);
			$i = 0;
			// Array abfüllen
			while ($arrDataTemp = mysql_fetch_array($resQuery, MYSQL_ASSOC)) {
				foreach ($arrDataTemp AS $key => $value) {
					$arrDataset[$i][$key] = $value;
				}
				$i++;
			}
			return true;
		} else if (mysql_error() != "") {
			$this->strDBError 	= mysql_error();
			$this->error   		= true;
			return false;			
		}
		return true;
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Daten einfügen oder aktualisieren
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Fügt Daten in die Datenbank ein oder aktualisiert diese
	//
	//  Übergabeparameter:	$strSQL				SQL Statement	
	//
	//  Rückgabeparameter:	$this->intLastId	ID des erzeugten Datensatzes
	//
	//  Returnwert:			true bei Erfolg / false bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function insertData($strSQL) {
		// SQL Statement an Server senden
		$resQuery        = mysql_query($strSQL);
		// Fehlerbehandlung
		if (mysql_error() == "") {
			// Array abfüllen
			$this->intLastId = mysql_insert_id();
			return true;
		} else {
			$this->strDBError 	= mysql_error();
			$this->error   		= true;
			return false;			
		}
	}

    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Datenzeilen zählen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Zählt die Anzahl Datenzeilen einer Abfrage
	//
	//  Übergabeparameter:	$strSQL			SQL Statement	
	//
	//  Returnwert:			Anzahl Zeilen bei Erfolg / 0 bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function countRows($strSQL) {
		// SQL Statement an Server senden
		$resQuery = mysql_query($strSQL);
		// Fehlerbehandlung
		if ($resQuery && (mysql_error() == "")) {
			// Array abfüllen
			return mysql_num_rows($resQuery);
		} else {
			$this->strDBError 	= mysql_error();
			$this->error   		= true;
			return 0;			
		}
		
	}

    ///////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Hilfsfunktionen
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Datenbankserver verbinden
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Verbindung mit dem Datenbankserver herstellen
	//
	//  Übergabeparameter:	$dbserver	Servername
	//						$dbuser		Datenbankbenutzer
	//						$dbpasswd	Datenbankpasswort
	//
	//  Returnwert:			true bei Erfolg / false bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dbconnect($dbserver,$dbuser,$dbpasswd) {
					   
		// Parameter fehlen
		if (($dbserver == "") || ($dbuser == "")) {
			$this->strDBError = "Missing server connection parameter!<br>\n"; 
			$this->error   = true;
			return false;
		} 
		$this->strDBId = @mysql_connect($dbserver,$dbuser,$dbpasswd);
		// Verbindung schlug fehl	
   		if(!$this->strDBId) {
			$this->strDBError  = "Connection to database server \"$dbserver\" has failed by reason:<br>\n"; 
			$this->strDBError .= mysql_error()."\n";
			$this->error   = true;
			return false;
		}
		return true;
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Datenbank wählen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00.00 (Internal)
	//  Datum:		12.03.2007
	//  
	//  Verbindung mit einer Datenbank herstellen
	//
	//  Übergabeparameter:	$database	Datenbankname
	//
	//  Returnwert:			true bei Erfolg / false bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dbselect($database) {
		// Parameter fehlen
		if ($database == "") {
			$this->strDBError = "Missing database connection parameter!<br>\n"; 
			$this->error   = true;
			return false;
		} 
		$bolConnect = @mysql_select_db($database);
		// Verbindung schlug fehl
		if(!$bolConnect) {
			$this->strDBError  = "Connection to database \"$database\" has failed by reason:<br>\n";
			$this->strDBError .= mysql_error()."\n";
			$this->error   = true;
			return false;
		}
		return true;
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////
	//  Funktion: Datenbankserververbindung schliessen
	///////////////////////////////////////////////////////////////////////////////////////////
	//  
	//  Version:	2.00-BETA-1
	//  Datum:		12.03.2007	
	//  
	//  Schliesst die Verbindung zum Datenbankserver
	//
	//  Übergabeparameter:	keine
	//
	//  Returnwert:			true bei Erfolg / false bei Misserfolg
	//
	///////////////////////////////////////////////////////////////////////////////////////////
	function dbdisconnect() {
		@mysql_close($this->strDBId);
		return true;
	}
}
?>