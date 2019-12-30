--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  (c) 2005-2020 by Martin Willisegger
--
--  Project   : NagiosQL
--  Component : Update from NagiosQL 3.2.0 to NagiosQL 3.4.1
--  Website   : https://sourceforge.net/projects/nagiosql/
--  Version   : 3.4.1
--  GIT Repo  : https://gitlab.com/wizonet/NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  Modify existing tbl_settings
--
UPDATE `tbl_settings` SET `value` = '3.4.1' WHERE `tbl_settings`.`name` = 'version' LIMIT 1;
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
INSERT INTO `tbl_info` (`id`, `key1`, `key2`, `version`, `language`, `infotext`) VALUES (NULL, 'contact', 'minimum_importance', 'all', 'default', '<p><strong>Contact - </strong><strong>minimum importance<br /></strong></p>\r\n<p>This directive is used as the value that the host or service importance value must equal before notification is sent to this contact. The importance values are intended to represent the value of a host or service to an organization. For example, you could set this value and the importance value of a host such that a system administrator would be notified when a development server goes down, but the CIO would only be notified when the company\'s production ecommerce database server was down. The minimum_importance value defaults to zero.</p>\r\n<p>In Nagios Core 4.0.0 to 4.0.3 this was known as minimum_value but has been replaced with minimum_importance.</p>\r\n<p>Parameter name: minimum_importance<br /> <em>Required:</em> no</p>'),
  (NULL, 'domain', 'ftps_option', 'all', 'default', 'Use encrypted FTP (FTPS) to connect to the remote server. '),
  (NULL, 'domain', 'cgifile', 'all', 'default', '<p>Absolute path to your Nagios CGI config file.<br /><br />Examples:<br />/etc/nagios/cgi.cfg<br />/usr/local/nagios/etc/cgi.cfg<br /><br />This is used to edit Nagios website options directly from NagiosQL.</p>'),
  (NULL, 'domain', 'resourcefile', 'all', 'default', '<p>Absolute path to your Nagios resource config file.<br /><br />Examples:<br />/etc/nagios/resource.cfg<br />/usr/local/nagios/etc/resource.cfg<br /><br />This file is used to verify your configuration in Nagios 4.x. Be sure this file is readably by your webserver\'s user!</p>'),
  (NULL, 'host', 'importance', 'all', 'default', '<p><strong>Host - importance</strong></p> <p>This directive is used to represent the importance of the host to your organization. The importance is used when determining whether to send notifications to a contact. If the host\'s importance value plus the importance values of all of the host\'s services is greater than or equal to the contact\'s minimum_importance, the contact will be notified. For example, you could set this value and the minimum_importance of contacts such that a system administrator would be notified when a development server goes down, but the CIO would only be notified when the company\'s production ecommerce database server was down. The importance could also be used as a sort criteria when generating reports or for calculating a good system administrator\'s bonus. The importance value defaults to zero. In Nagios Core 4.0.0 to 4.0.3 this was known as <em>hourly_value</em> but has been replaced with <em>importance</em>.</p> <p><em>Parameter name:</em> importance<br /><em>Required:</em> no</p>'),
  (NULL, 'service', 'importance', 'all', 'default', '<p><strong>Service - importance</strong></p>\r\n<p>This directive is used to represent the importance of the service to your organization. The importance is used when determining whether to send notifications to a contact. If the service\'s importance value is greater than or equal to the contact\'s minimum_importance, the contact will be notified. For example, you could set this value and the minimum_importance of contacts such that a system administrator would be notified of a disk full event on a development server, but the CIO would only be notified when the company\'s production ecommerce database was down. The importance could also be used as a sort criteria when generating reports or for calculating a good system administrator\'s bonus. The importance value defaults to zero. In Nagios Core 4.0.0 to 4.0.3 this was known as <em>hourly_value</em> but has been replaced with <em>importance</em>.</p>\r\n<p><em>Parameter name:</em> importance<br /><em>Required:</em> no</p>'),
  (NULL, 'service', 'parents', 'all', 'default', '<p><strong>Service - parents</strong></p>\r\n<p>This directive is used to define a comma-delimited list of short names of the \"parent\" services for this particular service. Parent services are typically other services that need to be available in order for a check of this service to occur. For example, if a service checks the status of a disk using SSH, the disk check service would have the SSH service as a parent. If the service has no parent services, simply omit the \"parents\" directive. More complex service dependencies may be specified with service dependency objects.</p>\r\n<p><em>Parameter name:</em> parents<br /><em>Required:</em> no</p>');
UPDATE `tbl_info` SET `infotext`='<p>The nagios version which is running in this domain.</p>\r\n<p>Be sure you select the correct version here - otherwise not all configuration options are available or not supported options are shown.</p>\r\n<p>You can change this with a running configuration - NagiosQL will then upgrade or downgrade your configuration. Don\'t forget to write your complete configuration after a version change!</p>\r\n<p>Difference between version in data domain and configuration domain:</p>\r\n<ul>\r\n<li>The version information of the data domain is used to define the options offered in the web forms in NagiosQL.</li>\r\n<li>The version information of the configuration domain is used to define the options offered in the written configuration files.</li>\r\n</ul>\r\n<p>This way you can create your data in a newer Nagios version and still write in an older version to keep the configuration compatible to the running Nagios version.</p>' WHERE `key1`='domain' AND `key2`='version';
UPDATE `tbl_info` SET `infotext`='<p><strong>Host or Service - generic name</strong></p>\r\n<p>It is possible to use a host definition as a template for other host configurations. If this definition should be used as template, a generic template name must be defined.</p>\r\n<p>We do not recommend to do this - it is more open to define a separate host template than to use this option.</p>\r\n<p><em>Parameter name:</em> name<em><br>Required:</em> no</p>' WHERE `key1`='host' AND `key2`='genericname';
