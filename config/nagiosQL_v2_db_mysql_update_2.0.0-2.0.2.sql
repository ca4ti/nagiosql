-- ///////////////////////////////////////////////////////////////////////////////
-- //
-- // NagiosQL
-- //
-- ///////////////////////////////////////////////////////////////////////////////
-- //
-- // (c) 2007 by Martin Willisegger / nagiosql_v2@wizonet.ch
-- //
-- // Projekt:	NagiosQL Applikation
-- // Author :	Martin Willisegger
-- // Datum:	29.09.2007
-- // Zweck:	MySQL Datenbank update - MySQL 4.0-5.x
-- // Datei:	config/nagiosQL_V2_db_mysql_update_2.0.0-2.01.00.sql
-- // Version:  2.01-P00
-- // SVN:	$Id$
-- //
-- ///////////////////////////////////////////////////////////////////////////////

USE db_nagiosql_v2;

ALTER TABLE `tbl_host` CHANGE `freshness_threshold` `freshness_threshold` MEDIUMINT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `tbl_host` ADD `failure_prediction_enabled` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `flap_detection_enabled`;
ALTER TABLE `tbl_hosttemplate` CHANGE `freshness_threshold` `freshness_threshold` MEDIUMINT UNSIGNED NULL DEFAULT NULL;
UPDATE `tbl_language` SET `version` = '1.00',
`lang_de` = 'Nagios Binary nicht gefunden oder keine Rechte zum ausf&uuml;hren!',
`lang_en` = 'Cannot find the Nagios binary or no rights for execution!',
`lang_xy` = NULL WHERE `id` =309 LIMIT 1 ;
UPDATE `tbl_language` SET `version` = '2.00',
`lang_de` = 'Eintrag kann nicht deaktiviert werden, da er als obligatorischer Eintrag in einer anderen Konfiguration verwendet wird',
`lang_en` = 'Entry cannot be deactivated because it is used by another configuration',
`lang_xy` = NULL WHERE `id` =431 LIMIT 1 ;
UPDATE `tbl_language` SET `version` = '2.00',
`lang_de` = 'Schreibe alle &Uuml;berwachungskonfigurationen:',
`lang_en` = 'Write all monitoring configurations:',
`lang_xy` = NULL WHERE `id` =450 LIMIT 1 ;
INSERT INTO `tbl_language` (`id`, `version`, `category`, `keyword`, `lang_de`, `lang_en`, `lang_xy`) VALUES 
(452, '2.01', 'title', 'dataselect', 'Datenauswahl', 'Data selection', NULL),
(453, '2.01', 'admintable', 'dataselect', 'Datenauswahl', 'Data selection', NULL),
(454, '2.01', 'formchecks', 'fill_data', 'Bitte mindestens einen Datensatz auswählen!', 'Please select at least one dataset', NULL);
ALTER TABLE `tbl_service` CHANGE `freshness_threshold` `freshness_threshold` MEDIUMINT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `tbl_service` ADD `failure_prediction_enabled` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `flap_detection_enabled`;

