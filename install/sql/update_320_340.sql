--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  (c) 2005-2018 by Martin Willisegger
--
--  Project   : NagiosQL
--  Component : Update from NagiosQL 3.2.0 to NagiosQL 3.4.0
--  Website   : https://sourceforge.net/projects/nagiosql/
--  Version   : 3.4.0
--  GIT Repo  : https://gitlab.com/wizonet/NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  Modify existing tbl_settings
--
UPDATE `tbl_settings` SET `value` = '3.4.0' WHERE `tbl_settings`.`name` = 'version' LIMIT 1;
--
--  Modify existing tbl_configtarget
--
ALTER TABLE `tbl_configtarget` ADD `cgifile` VARCHAR(255) NOT NULL AFTER `conffile`;
ALTER TABLE `tbl_configtarget` ADD `resourcefile` VARCHAR(255) NOT NULL AFTER `cgifile`;
ALTER TABLE `tbl_configtarget` ADD `ftp_secure` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `ssh_key_path`;
--
--  Modify existing tbl_contact
--
ALTER TABLE `tbl_contact` ADD `minimum_importance` INT NULL DEFAULT NULL AFTER `contactgroups_tploptions`;
--
--  Modify existing tbl_contacttemplate
--
ALTER TABLE `tbl_contacttemplate` ADD `minimum_importance` INT NULL DEFAULT NULL AFTER `contactgroups_tploptions`;
--
--  Modify existing tbl_hosts
--
ALTER TABLE `tbl_host` ADD `importance` INT NULL DEFAULT NULL AFTER `parents_tploptions`;
--
--  Modify existing tbl_hoststemplates
--
ALTER TABLE `tbl_hosttemplate` ADD `importance` INT NULL DEFAULT NULL AFTER `parents_tploptions`;
--
--  Modify existing tbl_services
--
ALTER TABLE `tbl_service` ADD `parents` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `display_name`;
ALTER TABLE `tbl_service` ADD `parents_tploptions` TINYINT UNSIGNED NOT NULL DEFAULT '2' AFTER `parents`;
ALTER TABLE `tbl_service` ADD `importance` INT NULL DEFAULT NULL AFTER `parents_tploptions`;
--
--  Modify existing tbl_servicetemplate
--
ALTER TABLE `tbl_servicetemplate` ADD `parents` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `display_name`;
ALTER TABLE `tbl_servicetemplate` ADD `parents_tploptions` TINYINT UNSIGNED NOT NULL DEFAULT '2' AFTER `parents`;
ALTER TABLE `tbl_servicetemplate` ADD `importance` INT NULL DEFAULT NULL AFTER `parents_tploptions`;
--
-- Tabellenstruktur für Tabelle `tbl_lnkServiceToService`
--
CREATE TABLE `tbl_lnkServiceToService` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `idHost` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
--
-- Indizes für die Tabelle `tbl_lnkServiceToService`
--
ALTER TABLE `tbl_lnkServiceToService` ADD PRIMARY KEY (`idMaster`,`idSlave`);
--
-- Tabellenstruktur für Tabelle `tbl_lnkServicetemplateToService`
--
CREATE TABLE `tbl_lnkServicetemplateToService` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `idHost` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
--
-- Indizes für die Tabelle `tbl_lnkServicetemplateToService`
--
ALTER TABLE `tbl_lnkServicetemplateToService` ADD PRIMARY KEY (`idMaster`,`idSlave`);
--
--  Modify table tbl_relationinformation
--
INSERT INTO `tbl_relationinformation` (`id`, `master`, `tableName1`, `tableName2`, `fieldName`, `linkTable`, `target1`, `target2`, `targetKey`, `fullRelation`, `flags`, `type`) VALUES (NULL, 'tbl_service', 'tbl_service', '', 'parents', 'tbl_lnkServiceToService', 'service_description', '', '', '0', '', '7');
INSERT INTO `tbl_relationinformation` (`id`, `master`, `tableName1`, `tableName2`, `fieldName`, `linkTable`, `target1`, `target2`, `targetKey`, `fullRelation`, `flags`, `type`) VALUES (NULL, 'tbl_servicetemplate', 'tbl_service', '', 'parents', 'tbl_lnkServicetemplateToService', 'service_description', '', '', '0', '', '7');
--
--  Modify existing tbl_info
--
INSERT INTO `tbl_info` (`id`, `key1`, `key2`, `version`, `language`, `infotext`) VALUES (NULL, 'contact', 'minimum_importance', 'all', 'default', '<p><strong>Contact - </strong><strong>minimum importance<br /></strong></p>\r\n<p>This directive is used as the value that the host or service importance value must equal before notification is sent to this contact. The importance values are intended to represent the value of a host or service to an organization. For example, you could set this value and the importance value of a host such that a system administrator would be notified when a development server goes down, but the CIO would only be notified when the company\'s production ecommerce database server was down. The minimum_importance value defaults to zero.</p>\r\n<p>In Nagios Core 4.0.0 to 4.0.3 this was known as minimum_value but has been replaced with minimum_importance.</p>\r\n<p>Parameter name: minimum_importance<br /> <em>Required:</em> no</p>');