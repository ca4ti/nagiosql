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
--  Component : Update from NagiosQL 3.0.4 to NagiosQL 3.1.0
--  Website   : https://sourceforge.net/projects/nagiosql/
--  Version   : 3.4.1
--  GIT Repo  : https://gitlab.com/wizonet/NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  Update from 3.0.0 to 3.0.1
--
--  Modify existing tbl_logbook
--
ALTER TABLE `tbl_logbook` CHANGE `entry` `entry` TINYTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
--
--  Update from 3.0.1 to 3.0.4
--
ALTER TABLE `tbl_settings` CHANGE `value` `value` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
--
--  Modify existing tbl_lnkServicegroupToService
--
ALTER TABLE `tbl_lnkServicegroupToService` DROP PRIMARY KEY, ADD PRIMARY KEY ( `idMaster` , `idSlaveH` , `idSlaveHG`, `idSlaveS` );
--
--  Modify existing tbl_serviceextinfo
--
ALTER TABLE `tbl_serviceextinfo` CHANGE `host_name` `host_name` INT( 11 ) NULL DEFAULT '0';

--
-- Replace access_rights by access_group
--
ALTER TABLE `tbl_domain` CHANGE `access_rights` `access_group` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_contactgroup` CHANGE `access_rights` `access_group` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_contact` CHANGE `access_rights` `access_group` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_command` CHANGE `access_rights` `access_group` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_contacttemplate` CHANGE `access_rights` `access_group` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_host` CHANGE `access_rights` `access_group` INT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_hostdependency` CHANGE `access_rights` `access_group` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_hostescalation` CHANGE `access_rights` `access_group` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_hostextinfo` CHANGE `access_rights` `access_group` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_hostgroup` CHANGE `access_rights` `access_group` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_hosttemplate` CHANGE `access_rights` `access_group` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_service` CHANGE `access_rights` `access_group` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_servicedependency` CHANGE `access_rights` `access_group` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_serviceescalation` CHANGE `access_rights` `access_group` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_serviceextinfo` CHANGE `access_rights` `access_group` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_servicegroup` CHANGE `access_rights` `access_group` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_servicetemplate` CHANGE `access_rights` `access_group` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_timeperiod` CHANGE `access_rights` `access_group` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_submenu` CHANGE `access_rights` `access_group` INT( 8 ) UNSIGNED NOT NULL DEFAULT '0';
--
-- Replace last_modified field type from TIMESTAMP to DATETIME
--
ALTER TABLE `tbl_domain` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_contactgroup` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_contact` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_command` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_contacttemplate` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_host` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_hostdependency` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_hostescalation` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_hostextinfo` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_hostgroup` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_service` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_servicedependency` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_serviceescalation` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_serviceextinfo` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_servicegroup` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_servicetemplate` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_timedefinition` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_timeperiod` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_user` CHANGE `last_login` `last_login` DATETIME NOT NULL;
ALTER TABLE `tbl_user` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
ALTER TABLE `tbl_variabledefinition` CHANGE `last_modified` `last_modified` DATETIME NOT NULL;
--
-- Add new import_hash field for all tables without key
--
ALTER TABLE `tbl_hostdependency` ADD `import_hash` VARCHAR( 255 ) NULL DEFAULT NULL; 
ALTER TABLE `tbl_hostescalation` ADD `import_hash` VARCHAR( 255 ) NULL DEFAULT NULL;
ALTER TABLE `tbl_service` ADD `import_hash` VARCHAR( 255 ) NULL DEFAULT NULL;
ALTER TABLE `tbl_servicedependency` ADD `import_hash` VARCHAR( 255 ) NULL DEFAULT NULL;
ALTER TABLE `tbl_serviceescalation` ADD `import_hash` VARCHAR( 255 ) NULL DEFAULT NULL;
ALTER TABLE `tbl_serviceextinfo` ADD `import_hash` VARCHAR( 255 ) NULL DEFAULT NULL;
ALTER TABLE `tbl_servicetemplate` ADD `import_hash` VARCHAR( 255 ) NULL DEFAULT NULL;
--
-- Add exclude field in link tables
--
ALTER TABLE `tbl_lnkContactgroupToContact` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkContactgroupToContactgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkContacttemplateToCommandHost` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkContacttemplateToCommandService` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkContacttemplateToContactgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkContactToCommandHost` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkContactToCommandService` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkContactToContactgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHostdependencyToHostgroup_DH` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHostdependencyToHostgroup_H` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHostdependencyToHost_DH` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHostdependencyToHost_H` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHostescalationToContact` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHostescalationToContactgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHostescalationToHost` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHostescalationToHostgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHostgroupToHost` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHostgroupToHostgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHosttemplateToContact` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHosttemplateToContactgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHosttemplateToHost` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHosttemplateToHostgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHostToContact` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHostToContactgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHostToHost` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkHostToHostgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServicedependencyToHostgroup_DH` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServicedependencyToHostgroup_H` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServicedependencyToHost_DH` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServicedependencyToHost_H` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServicedependencyToService_DS` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServicedependencyToService_S` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServiceescalationToContact` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServiceescalationToContactgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServiceescalationToHost` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServiceescalationToHostgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServiceescalationToService` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServicegroupToService` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServicegroupToServicegroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServicetemplateToContact` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServicetemplateToContactgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServicetemplateToHost` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServicetemplateToHostgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServicetemplateToServicegroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServiceToContact` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServiceToContactgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServiceToHost` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServiceToHostgroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkServiceToServicegroup` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_lnkTimeperiodToTimeperiod` ADD `exclude` TINYINT UNSIGNED NOT NULL DEFAULT '0';
--
-- Add slave string field for link tables
--
ALTER TABLE `tbl_lnkServicedependencyToService_DS` ADD `strSlave` VARCHAR( 255 ) NULL AFTER `idSlave`; 
ALTER TABLE `tbl_lnkServicedependencyToService_S` ADD `strSlave` VARCHAR( 255 ) NULL AFTER `idSlave`;
ALTER TABLE `tbl_lnkServiceescalationToService` ADD `strSlave` VARCHAR( 255 ) NULL AFTER `idSlave`;
--
-- Reset permissions for submenus
--
UPDATE `tbl_submenu` SET `access_group` = '0';
--
-- Extent submenu for configuration cleaner and group administration
--
INSERT INTO `tbl_submenu` (`id` ,`id_main` ,`order_id` ,`item` ,`link` ,`access_group`) VALUES (NULL , '7', '3', 'Group admin', 'admin/group.php', '0');
INSERT INTO `tbl_submenu` (`id` ,`id_main` ,`order_id` ,`item` ,`link` ,`access_group`) VALUES (NULL , '6', '3', 'Delete config files', 'admin/delconfig.php', '0');
UPDATE `tbl_submenu` SET `item` = 'Delete backup files' WHERE `link` = 'admin/delbackup.php';
UPDATE `tbl_submenu` SET `item` = 'Extended Host' WHERE `link` = 'admin/hostextinfo.php';
UPDATE `tbl_submenu` SET `item` = 'Extended Service' WHERE `link` = 'admin/serviceextinfo.php';
UPDATE `tbl_submenu` SET `item` = 'Service dependency' WHERE `link` = 'admin/servicedependencies.php';
UPDATE `tbl_submenu` SET `item` = 'Service escalation' WHERE `link` = 'admin/serviceescalations.php';
--
-- Reorder submenu
--
UPDATE `tbl_submenu` SET `order_id` = '4' WHERE `tbl_submenu`.`id` =24;
UPDATE `tbl_submenu` SET `order_id` = '5' WHERE `tbl_submenu`.`id` =25;
UPDATE `tbl_submenu` SET `order_id` = '6' WHERE `tbl_submenu`.`id` =21;
UPDATE `tbl_submenu` SET `order_id` = '7' WHERE `tbl_submenu`.`id` =29;
UPDATE `tbl_submenu` SET `order_id` = '8' WHERE `tbl_submenu`.`id` =30;
UPDATE `tbl_submenu` SET `order_id` = '5' WHERE `tbl_submenu`.`id` =23;
UPDATE `tbl_submenu` SET `order_id` = '4' WHERE `tbl_submenu`.`id` =22;
UPDATE `tbl_submenu` SET `order_id` = '6' WHERE `tbl_submenu`.`id` =19;
--
-- Create new table tbl_group
--
CREATE TABLE IF NOT EXISTS `tbl_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `users` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `active` enum('0','1') NOT NULL,
  `last_modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
--
-- Create new table tbl_lnkGroupToUser
--
CREATE TABLE IF NOT EXISTS `tbl_lnkGroupToUser` (
  `idMaster` int(10) unsigned NOT NULL,
  `idSlave` int(10) unsigned NOT NULL,
  `read` enum('0','1') NOT NULL DEFAULT '1',
  `write` enum('0','1') NOT NULL DEFAULT '1',
  `link` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
--
-- Create new table tbl_lnkTimeperiodToTimeperiodUse
--
CREATE TABLE IF NOT EXISTS  `tbl_lnkTimeperiodToTimeperiodUse` (
  `idMaster` int( 11 ) NOT NULL ,
  `idSlave` int( 11 ) NOT NULL ,
  `exclude` tinyint( 3 ) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY ( `idMaster` , `idSlave` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
--
-- Modify existing tbl_domain
--
ALTER TABLE `tbl_domain` ADD `ssh_key_path` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `password`;
ALTER TABLE `tbl_domain` ADD `conffile` VARCHAR( 255 ) NOT NULL DEFAULT '/mypath/nagios.cfg' AFTER `pidfile`;
ALTER TABLE `tbl_domain` ADD `picturedir` VARCHAR( 255 ) NULL AFTER `importdir`;
ALTER TABLE `tbl_domain` ADD `enable_common` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `version`;
ALTER TABLE `tbl_domain` ADD `utf8_decode` tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER `enable_common`;
INSERT INTO `tbl_domain` (`id`, `domain`, `alias`, `server`, `method`, `user`, `password`, `ssh_key_path`, `basedir`, `hostconfig`, `serviceconfig`, `backupdir`, `hostbackup`, `servicebackup`, `nagiosbasedir`, `importdir`, `commandfile`, `binaryfile`, `pidfile`, `conffile`, `version`, `access_group`, `active`, `nodelete`, `last_modified`) VALUES(0, 'common', 'administrative global domain', '', '1', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 3, 0, '1', '1', NOW());
UPDATE `tbl_domain` SET `id` = '0' WHERE `tbl_domain`.`domain` ='common';
--
-- Modify existing tbl_timeperiod
--
ALTER TABLE `tbl_timeperiod` ADD `use_template` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `exclude`;
--
-- Modify existing tbl_user
-- 
ALTER TABLE `tbl_user` ADD `admin_enable` ENUM( '0', '1' ) NOT NULL DEFAULT '0' AFTER `access_rights`; 
UPDATE `tbl_user` SET `admin_enable` = '1' WHERE `tbl_user`.`id` =1;
--
-- Create new tbl_tablestatus
--
CREATE TABLE `tbl_tablestatus` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`tableName` VARCHAR( 255 ) NOT NULL ,
`domainId` INT NOT NULL ,
`updateTime` DATETIME NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM ;
--
-- Create new tbl_relationinformation
--
CREATE TABLE IF NOT EXISTS `tbl_relationinformation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `master` varchar(255) NOT NULL,
  `tableName1` varchar(255) NOT NULL,
  `tableName2` varchar(255) NOT NULL,
  `fieldName` varchar(255) NOT NULL,
  `linkTable` varchar(255) NOT NULL,
  `target1` varchar(255) NOT NULL,
  `target2` varchar(255) NOT NULL,
  `targetKey` varchar(255) NOT NULL,
  `fullRelation` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `flags` varchar(255) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=234 ;
--
-- Initialize tbl_relationinformation
-- 
INSERT INTO `tbl_relationinformation` (`id`, `master`, `tableName1`, `tableName2`, `fieldName`, `linkTable`, `target1`, `target2`, `targetKey`, `fullRelation`, `flags`, `type`) VALUES
(1, 'tbl_timeperiod', 'tbl_timeperiod', '', 'exclude', 'tbl_lnkTimeperiodToTimeperiod', 'timeperiod_name', '', '', 0, '', 2),
(2, 'tbl_contact', 'tbl_command', '', 'host_notification_commands', 'tbl_lnkContactToCommandHost', 'command_name', '', '', 0, '', 2),
(3, 'tbl_contact', 'tbl_command', '', 'service_notification_commands', 'tbl_lnkContactToCommandService', 'command_name', '', '', 0, '', 2),
(4, 'tbl_contact', 'tbl_contactgroup', '', 'contactgroups', 'tbl_lnkContactToContactgroup', 'contactgroup_name', '', '', 0, '', 2),
(5, 'tbl_contact', 'tbl_timeperiod', '', 'host_notification_period', '', 'timeperiod_name', '', '', 0, '', 1),
(6, 'tbl_contact', 'tbl_timeperiod', '', 'service_notification_period', '', 'timeperiod_name', '', '', 0, '', 1),
(7, 'tbl_contact', 'tbl_contacttemplate', 'tbl_contact', 'use_template', 'tbl_lnkContactToContacttemplate', 'template_name', 'name', '', 0, '', 3),
(8, 'tbl_contact', 'tbl_variabledefinition', '', 'use_variables', 'tbl_lnkContactToVariabledefinition', 'name', '', '', 0, '', 4),
(9, 'tbl_contacttemplate', 'tbl_command', '', 'host_notification_commands', 'tbl_lnkContacttemplateToCommandHost', 'command_name', '', '', 0, '', 2),
(10, 'tbl_contacttemplate', 'tbl_command', '', 'service_notification_commands', 'tbl_lnkContacttemplateToCommandService', 'command_name', '', '', 0, '', 2),
(11, 'tbl_contacttemplate', 'tbl_contactgroup', '', 'contactgroups', 'tbl_lnkContacttemplateToContactgroup', 'contactgroup_name', '', '', 0, '', 2),
(12, 'tbl_contacttemplate', 'tbl_timeperiod', '', 'host_notification_period', '', 'timeperiod_name', '', '', 0, '', 1),
(13, 'tbl_contacttemplate', 'tbl_timeperiod', '', 'service_notification_period', '', 'timeperiod_name', '', '', 0, '', 1),
(14, 'tbl_contacttemplate', 'tbl_contacttemplate', 'tbl_contact', 'use_template', 'tbl_lnkContacttemplateToContacttemplate', 'template_name', 'name', '', 0, '', 3),
(15, 'tbl_contacttemplate', 'tbl_variabledefinition', '', 'use_variables', 'tbl_lnkContacttemplateToVariabledefinition', 'name', '', '', 0, '', 4),
(16, 'tbl_contactgroup', 'tbl_contact', '', 'members', 'tbl_lnkContactgroupToContact', 'contact_name', '', '', 0, '', 2),
(17, 'tbl_contactgroup', 'tbl_contactgroup', '', 'contactgroup_members', 'tbl_lnkContactgroupToContactgroup', 'contactgroup_name', '', '', 0, '', 2),
(18, 'tbl_hosttemplate', 'tbl_host', '', 'parents', 'tbl_lnkHosttemplateToHost', 'host_name', '', '', 0, '', 2),
(19, 'tbl_hosttemplate', 'tbl_hostgroup', '', 'hostgroups', 'tbl_lnkHosttemplateToHostgroup', 'hostgroup_name', '', '', 0, '', 2),
(20, 'tbl_hosttemplate', 'tbl_contactgroup', '', 'contact_groups', 'tbl_lnkHosttemplateToContactgroup', 'contactgroup_name', '', '', 0, '', 2),
(21, 'tbl_hosttemplate', 'tbl_contact', '', 'contacts', 'tbl_lnkHosttemplateToContact', 'contact_name', '', '', 0, '', 2),
(22, 'tbl_hosttemplate', 'tbl_timeperiod', '', 'check_period', '', 'timeperiod_name', '', '', 0, '', 1),
(23, 'tbl_hosttemplate', 'tbl_command', '', 'check_command', '', 'command_name', '', '', 0, '', 1),
(24, 'tbl_hosttemplate', 'tbl_timeperiod', '', 'notification_period', '', 'timeperiod_name', '', '', 0, '', 1),
(25, 'tbl_hosttemplate', 'tbl_command', '', 'event_handler', '', 'command_name', '', '', 0, '', 1),
(26, 'tbl_hosttemplate', 'tbl_hosttemplate', 'tbl_host', 'use_template', 'tbl_lnkHosttemplateToHosttemplate', 'template_name', 'name', '', 0, '', 3),
(27, 'tbl_hosttemplate', 'tbl_variabledefinition', '', 'use_variables', 'tbl_lnkHosttemplateToVariabledefinition', 'name', '', '', 0, '', 4),
(28, 'tbl_host', 'tbl_host', '', 'parents', 'tbl_lnkHostToHost', 'host_name', '', '', 0, '', 2),
(29, 'tbl_host', 'tbl_hostgroup', '', 'hostgroups', 'tbl_lnkHostToHostgroup', 'hostgroup_name', '', '', 0, '', 2),
(30, 'tbl_host', 'tbl_contactgroup', '', 'contact_groups', 'tbl_lnkHostToContactgroup', 'contactgroup_name', '', '', 0, '', 2),
(31, 'tbl_host', 'tbl_contact', '', 'contacts', 'tbl_lnkHostToContact', 'contact_name', '', '', 0, '', 2),
(32, 'tbl_host', 'tbl_timeperiod', '', 'check_period', '', 'timeperiod_name', '', '', 0, '', 1),
(33, 'tbl_host', 'tbl_command', '', 'check_command', '', 'command_name', '', '', 0, '', 1),
(34, 'tbl_host', 'tbl_timeperiod', '', 'notification_period', '', 'timeperiod_name', '', '', 0, '', 1),
(35, 'tbl_host', 'tbl_command', '', 'event_handler', '', 'command_name', '', '', 0, '', 1),
(36, 'tbl_host', 'tbl_hosttemplate', 'tbl_host', 'use_template', 'tbl_lnkHostToHosttemplate', 'template_name', 'name', '', 0, '', 3),
(37, 'tbl_host', 'tbl_variabledefinition', '', 'use_variables', 'tbl_lnkHostToVariabledefinition', 'name', '', '', 0, '', 4),
(38, 'tbl_hostgroup', 'tbl_host', '', 'members', 'tbl_lnkHostgroupToHost', 'host_name', '', '', 0, '', 2),
(39, 'tbl_hostgroup', 'tbl_hostgroup', '', 'hostgroup_members', 'tbl_lnkHostgroupToHostgroup', 'hostgroup_name', '', '', 0, '', 2),
(40, 'tbl_servicetemplate', 'tbl_host', '', 'host_name', 'tbl_lnkServicetemplateToHost', 'host_name', '', '', 0, '', 2),
(41, 'tbl_servicetemplate', 'tbl_hostgroup', '', 'hostgroup_name', 'tbl_lnkServicetemplateToHostgroup', 'hostgroup_name', '', '', 0, '', 2),
(42, 'tbl_servicetemplate', 'tbl_servicegroup', '', 'servicegroups', 'tbl_lnkServicetemplateToServicegroup', 'servicegroup_name', '', '', 0, '', 2),
(43, 'tbl_servicetemplate', 'tbl_contactgroup', '', 'contact_groups', 'tbl_lnkServicetemplateToContactgroup', 'contactgroup_name', '', '', 0, '', 2),
(44, 'tbl_servicetemplate', 'tbl_contact', '', 'contacts', 'tbl_lnkServicetemplateToContact', 'contact_name', '', '', 0, '', 2),
(45, 'tbl_servicetemplate', 'tbl_timeperiod', '', 'check_period', '', 'timeperiod_name', '', '', 0, '', 1),
(46, 'tbl_servicetemplate', 'tbl_command', '', 'check_command', '', 'command_name', '', '', 0, '', 1),
(47, 'tbl_servicetemplate', 'tbl_timeperiod', '', 'notification_period', '', 'timeperiod_name', '', '', 0, '', 1),
(48, 'tbl_servicetemplate', 'tbl_command', '', 'event_handler', '', 'command_name', '', '', 0, '', 1),
(49, 'tbl_servicetemplate', 'tbl_servicetemplate', 'tbl_service', 'use_template', 'tbl_lnkServicetemplateToServicetemplate', 'template_name', 'name', '', 0, '', 3),
(50, 'tbl_servicetemplate', 'tbl_variabledefinition', '', 'use_variables', 'tbl_lnkServicetemplateToVariabledefinition', 'name', '', '', 0, '', 4),
(51, 'tbl_service', 'tbl_host', '', 'host_name', 'tbl_lnkServiceToHost', 'host_name', '', '', 0, '', 2),
(52, 'tbl_service', 'tbl_hostgroup', '', 'hostgroup_name', 'tbl_lnkServiceToHostgroup', 'hostgroup_name', '', '', 0, '', 2),
(53, 'tbl_service', 'tbl_servicegroup', '', 'servicegroups', 'tbl_lnkServiceToServicegroup', 'servicegroup_name', '', '', 0, '', 2),
(54, 'tbl_service', 'tbl_contactgroup', '', 'contact_groups', 'tbl_lnkServiceToContactgroup', 'contactgroup_name', '', '', 0, '', 2),
(55, 'tbl_service', 'tbl_contact', '', 'contacts', 'tbl_lnkServiceToContact', 'contact_name', '', '', 0, '', 2),
(56, 'tbl_service', 'tbl_timeperiod', '', 'check_period', '', 'timeperiod_name', '', '', 0, '', 1),
(57, 'tbl_service', 'tbl_command', '', 'check_command', '', 'command_name', '', '', 0, '', 1),
(58, 'tbl_service', 'tbl_timeperiod', '', 'notification_period', '', 'timeperiod_name', '', '', 0, '', 1),
(59, 'tbl_service', 'tbl_command', '', 'event_handler', '', 'command_name', '', '', 0, '', 1),
(60, 'tbl_service', 'tbl_servicetemplate', 'tbl_service', 'use_template', 'tbl_lnkServiceToServicetemplate', 'template_name', 'name', '', 0, '', 3),
(61, 'tbl_service', 'tbl_variabledefinition', '', 'use_variables', 'tbl_lnkServiceToVariabledefinition', 'name', '', '', 0, '', 4),
(62, 'tbl_servicegroup', 'tbl_host', 'tbl_service', 'members', 'tbl_lnkServicegroupToService', 'host_name', 'service_description', '', 0, '', 5),
(63, 'tbl_servicegroup', 'tbl_servicegroup', '', 'servicegroup_members', 'tbl_lnkServicegroupToServicegroup', 'servicegroup_name', '', '', 0, '', 2),
(64, 'tbl_hostdependency', 'tbl_host', '', 'dependent_host_name', 'tbl_lnkHostdependencyToHost_DH', 'host_name', '', '', 0, '', 2),
(65, 'tbl_hostdependency', 'tbl_host', '', 'host_name', 'tbl_lnkHostdependencyToHost_H', 'host_name', '', '', 0, '', 2),
(66, 'tbl_hostdependency', 'tbl_hostgroup', '', 'dependent_hostgroup_name', 'tbl_lnkHostdependencyToHostgroup_DH', 'hostgroup_name', '', '', 0, '', 2),
(67, 'tbl_hostdependency', 'tbl_hostgroup', '', 'hostgroup_name', 'tbl_lnkHostdependencyToHostgroup_H', 'hostgroup_name', '', '', 0, '', 2),
(68, 'tbl_hostdependency', 'tbl_timeperiod', '', 'dependency_period', '', 'timeperiod_name', '', '', 0, '', 1),
(69, 'tbl_hostescalation', 'tbl_host', '', 'host_name', 'tbl_lnkHostescalationToHost', 'host_name', '', '', 0, '', 2),
(70, 'tbl_hostescalation', 'tbl_hostgroup', '', 'hostgroup_name', 'tbl_lnkHostescalationToHostgroup', 'hostgroup_name', '', '', 0, '', 2),
(71, 'tbl_hostescalation', 'tbl_contact', '', 'contacts', 'tbl_lnkHostescalationToContact', 'contact_name', '', '', 0, '', 2),
(72, 'tbl_hostescalation', 'tbl_contactgroup', '', 'contact_groups', 'tbl_lnkHostescalationToContactgroup', 'contactgroup_name', '', '', 0, '', 2),
(73, 'tbl_hostescalation', 'tbl_timeperiod', '', 'escalation_period', '', 'timeperiod_name', '', '', 0, '', 1),
(74, 'tbl_hostextinfo', 'tbl_host', '', 'host_name', '', 'host_name', '', '', 0, '', 1),
(75, 'tbl_servicedependency', 'tbl_host', '', 'dependent_host_name', 'tbl_lnkServicedependencyToHost_DH', 'host_name', '', '', 0, '', 2),
(76, 'tbl_servicedependency', 'tbl_host', '', 'host_name', 'tbl_lnkServicedependencyToHost_H', 'host_name', '', '', 0, '', 2),
(77, 'tbl_servicedependency', 'tbl_hostgroup', '', 'dependent_hostgroup_name', 'tbl_lnkServicedependencyToHostgroup_DH', 'hostgroup_name', '', '', 0, '', 2),
(78, 'tbl_servicedependency', 'tbl_hostgroup', '', 'hostgroup_name', 'tbl_lnkServicedependencyToHostgroup_H', 'hostgroup_name', '', '', 0, '', 2),
(79, 'tbl_servicedependency', 'tbl_service', '', 'dependent_service_description', 'tbl_lnkServicedependencyToService_DS', 'service_description', '', '', 0, '', 6),
(80, 'tbl_servicedependency', 'tbl_service', '', 'service_description', 'tbl_lnkServicedependencyToService_S', 'service_description', '', '', 0, '', 6),
(81, 'tbl_servicedependency', 'tbl_timeperiod', '', 'dependency_period', '', 'timeperiod_name', '', '', 0, '', 1),
(82, 'tbl_serviceescalation', 'tbl_host', '', 'host_name', 'tbl_lnkServiceescalationToHost', 'host_name', '', '', 0, '', 2),
(83, 'tbl_serviceescalation', 'tbl_hostgroup', '', 'hostgroup_name', 'tbl_lnkServiceescalationToHostgroup', 'hostgroup_name', '', '', 0, '', 2),
(84, 'tbl_serviceescalation', 'tbl_service', '', 'service_description', 'tbl_lnkServiceescalationToService', 'service_description', '', '', 0, '', 6),
(85, 'tbl_serviceescalation', 'tbl_contact', '', 'contacts', 'tbl_lnkServiceescalationToContact', 'contact_name', '', '', 0, '', 2),
(86, 'tbl_serviceescalation', 'tbl_contactgroup', '', 'contact_groups', 'tbl_lnkServiceescalationToContactgroup', 'contactgroup_name', '', '', 0, '', 2),
(87, 'tbl_serviceescalation', 'tbl_timeperiod', '', 'escalation_period', '', 'timeperiod_name', '', '', 0, '', 1),
(88, 'tbl_serviceextinfo', 'tbl_host', '', 'host_name', '', 'host_name', '', '', 0, '', 1),
(89, 'tbl_serviceextinfo', 'tbl_service', '', 'service_description', '', 'service_description', '', '', 0, '', 1),
(90, 'tbl_command', 'tbl_lnkContacttemplateToCommandHost', '', 'idSlave', '', 'tbl_contacttemplate', '', 'template_name', 1, '0,0,0,1', 0),
(91, 'tbl_command', 'tbl_lnkContacttemplateToCommandService', '', 'idSlave', '', 'tbl_contacttemplate', '', 'template_name', 1, '0,0,0,1', 0),
(92, 'tbl_command', 'tbl_lnkContactToCommandHost', '', 'idSlave', '', 'tbl_contact', '', 'contact_name', 1, '1,1,0,1', 0),
(93, 'tbl_command', 'tbl_lnkContactToCommandService', '', 'idSlave', '', 'tbl_contact', '', 'contact_name', 1, '1,1,0,1', 0),
(94, 'tbl_command', 'tbl_host', '', 'check_command', '', '', '', 'host_name', 1, '0,2,2,0', 0),
(95, 'tbl_command', 'tbl_host', '', 'event_handler', '', '', '', 'host_name', 1, '0,2,2,0', 0),
(96, 'tbl_command', 'tbl_service', '', 'check_command', '', '', '', 'config_name,service_description', 1, '1,1,2,0', 0),
(97, 'tbl_command', 'tbl_service', '', 'event_handler', '', '', '', 'config_name,service_description', 1, '0,2,2,0', 0),
(98, 'tbl_contact', 'tbl_lnkContactgroupToContact', '', 'idSlave', '', 'tbl_contactgroup', '', 'contactgroup_name', 1, '1,2,0,1', 0),
(99, 'tbl_contact', 'tbl_lnkContactToCommandHost', '', 'idMaster', '', 'tbl_command', '', 'command_name', 1, '0,0,0,1', 0),
(100, 'tbl_contact', 'tbl_lnkContactToCommandService', '', 'idMaster', '', 'tbl_command', '', 'command_name', 1, '0,0,0,1', 0),
(101, 'tbl_contact', 'tbl_lnkContactToContactgroup', '', 'idMaster', '', 'tbl_contactgroup', '', 'contactgroup_name', 1, '0,0,0,1', 0),
(102, 'tbl_contact', 'tbl_lnkContactToContacttemplate', '', 'idMaster', '', 'tbl_contacttemplate', '', 'template_name', 1, '0,0,0,1', 0),
(103, 'tbl_contact', 'tbl_lnkContactToVariabledefinition', '', 'idMaster', '', 'tbl_variabledefinition', '', 'name', 1, '0,0,0,2', 0),
(104, 'tbl_contact', 'tbl_lnkHostescalationToContact', '', 'idSlave', '', 'tbl_hostescalation', '', 'config_name', 1, '1,1,0,1', 0),
(105, 'tbl_contact', 'tbl_lnkHosttemplateToContact', '', 'idSlave', '', 'tbl_hosttemplate', '', 'template_name', 1, '0,0,0,1', 0),
(106, 'tbl_contact', 'tbl_lnkHostToContact', '', 'idSlave', '', 'tbl_host', '', 'host_name', 1, '1,1,0,1', 0),
(107, 'tbl_contact', 'tbl_lnkServiceescalationToContact', '', 'idSlave', '', 'tbl_serviceescalation', '', 'config_name', 1, '1,1,0,1', 0),
(108, 'tbl_contact', 'tbl_lnkServicetemplateToContact', '', 'idSlave', '', 'tbl_servicetemplate', '', 'template_name', 1, '0,0,0,1', 0),
(109, 'tbl_contact', 'tbl_lnkServiceToContact', '', 'idSlave', '', 'tbl_service', '', 'config_name,service_description', 1, '1,1,0,1', 0),
(110, 'tbl_contactgroup', 'tbl_lnkContactgroupToContact', '', 'idMaster', '', 'tbl_contact', '', 'contact_name', 1, '0,0,0,1', 0),
(111, 'tbl_contactgroup', 'tbl_lnkContactgroupToContactgroup', '', 'idMaster', '', 'tbl_contactgroup', '', 'contactgroup_name', 1, '0,0,0,1', 0),
(112, 'tbl_contactgroup', 'tbl_lnkContactgroupToContactgroup', '', 'idSlave', '', 'tbl_contactgroup', '', 'contactgroup_name', 1, '0,0,0,1', 0),
(113, 'tbl_contactgroup', 'tbl_lnkContacttemplateToContactgroup', '', 'idSlave', '', 'tbl_contacttemplate', '', 'template_name', 1, '0,0,0,1', 0),
(114, 'tbl_contactgroup', 'tbl_lnkContactToContactgroup', '', 'idSlave', '', 'tbl_contact', '', 'contact_name', 1, '0,0,0,1', 0),
(115, 'tbl_contactgroup', 'tbl_lnkHostescalationToContactgroup', '', 'idSlave', '', 'tbl_hostescalation', '', 'config_name', 1, '1,1,0,1', 0),
(116, 'tbl_contactgroup', 'tbl_lnkHosttemplateToContactgroup', '', 'idSlave', '', 'tbl_hosttemplate', '', 'template_name', 1, '0,0,0,1', 0),
(117, 'tbl_contactgroup', 'tbl_lnkHostToContactgroup', '', 'idSlave', '', 'tbl_host', '', 'host_name', 1, '1,1,0,1', 0),
(118, 'tbl_contactgroup', 'tbl_lnkServiceescalationToContactgroup', '', 'idSlave', '', 'tbl_serviceescalation', '', 'config_name', 1, '1,1,0,1', 0),
(119, 'tbl_contactgroup', 'tbl_lnkServicetemplateToContactgroup', '', 'idSlave', '', 'tbl_servicetemplate', '', 'template_name', 1, '0,0,0,1', 0),
(120, 'tbl_contactgroup', 'tbl_lnkServiceToContactgroup', '', 'idSlave', '', 'tbl_service', '', 'config_name,service_description', 1, '1,1,0,1', 0),
(121, 'tbl_contacttemplate', 'tbl_lnkContacttemplateToCommandHost', '', 'idMaster', '', 'tbl_command', '', 'command_name', 1, '0,0,0,1', 0),
(122, 'tbl_contacttemplate', 'tbl_lnkContacttemplateToCommandService', '', 'idMaster', '', 'tbl_command', '', 'command_name', 1, '0,0,0,1', 0),
(123, 'tbl_contacttemplate', 'tbl_lnkContacttemplateToContactgroup', '', 'idMaster', '', 'tbl_contactgroup', '', 'contactgroup_name', 1, '0,0,0,1', 0),
(124, 'tbl_contacttemplate', 'tbl_lnkContacttemplateToContacttemplate', '', 'idMaster', '', 'tbl_contacttemplate', '', 'template_name', 1, '0,0,0,1', 0),
(125, 'tbl_contacttemplate', 'tbl_lnkContacttemplateToContacttemplate', '', 'idSlave', '', 'tbl_contacttemplate', '', 'template_name', 1, '0,0,0,1', 0),
(126, 'tbl_contacttemplate', 'tbl_lnkContacttemplateToVariabledefinition', '', 'idMaster', '', 'tbl_variabledefinition', '', 'name', 1, '0,0,0,2', 0),
(127, 'tbl_contacttemplate', 'tbl_lnkContactToContacttemplate', '', 'idSlave', '', 'tbl_contact', '', 'contact_name', 1, '0,0,0,1', 0),
(128, 'tbl_host', 'tbl_lnkHostdependencyToHost_DH', '', 'idSlave', '', 'tbl_hostdependency', '', 'config_name', 1, '1,1,0,1', 0),
(129, 'tbl_host', 'tbl_lnkHostdependencyToHost_H', '', 'idSlave', '', 'tbl_hostdependency', '', 'config_name', 1, '1,1,0,1', 0),
(130, 'tbl_host', 'tbl_lnkHostescalationToHost', '', 'idSlave', '', 'tbl_hostescalation', '', 'config_name', 1, '1,1,0,1', 0),
(131, 'tbl_host', 'tbl_lnkHosttemplateToHost', '', 'idSlave', '', 'tbl_hosttemplate', '', 'template_name', 1, '0,0,0,1', 0),
(132, 'tbl_host', 'tbl_lnkHostToContact', '', 'idMaster', '', 'tbl_contact', '', 'contact_name', 1, '0,0,0,1', 0),
(133, 'tbl_host', 'tbl_lnkHostToContactgroup', '', 'idMaster', '', 'tbl_contactgroup', '', 'contactgroup_name', 1, '0,0,0,1', 0),
(134, 'tbl_host', 'tbl_lnkHostToHost', '', 'idMaster', '', 'tbl_host', '', 'host_name', 1, '0,0,0,1', 0),
(135, 'tbl_host', 'tbl_lnkHostToHost', '', 'idSlave', '', 'tbl_host', '', 'host_name', 1, '0,0,0,1', 0),
(136, 'tbl_host', 'tbl_lnkHostToHostgroup', '', 'idMaster', '', 'tbl_hostgroup', '', 'hostgroup_name', 1, '0,0,0,1', 0),
(137, 'tbl_host', 'tbl_lnkHostgroupToHost', '', 'idSlave', '', 'tbl_hostgroup', '', 'hostgroup_name', 1, '0,0,0,1', 0),
(138, 'tbl_host', 'tbl_lnkHostToHosttemplate', '', 'idMaster', '', 'tbl_hosttemplate', '', 'template_name', 1, '0,0,0,1', 0),
(139, 'tbl_host', 'tbl_lnkHostToVariabledefinition', '', 'idMaster', '', 'tbl_variabledefinition', '', 'name', 1, '0,0,0,2', 0),
(140, 'tbl_host', 'tbl_lnkServicedependencyToHost_DH', '', 'idSlave', '', 'tbl_servicedependency', '', 'config_name', 1, '1,1,0,1', 0),
(141, 'tbl_host', 'tbl_lnkServicedependencyToHost_H', '', 'idSlave', '', 'tbl_servicedependency', '', 'config_name', 1, '1,1,0,1', 0),
(142, 'tbl_host', 'tbl_lnkServiceescalationToHost', '', 'idSlave', '', 'tbl_serviceescalation', '', 'config_name', 1, '1,1,0,1', 0),
(143, 'tbl_host', 'tbl_lnkServicetemplateToHost', '', 'idSlave', '', 'tbl_servicetemplate', '', 'template_name', 1, '0,0,0,1', 0),
(144, 'tbl_host', 'tbl_lnkServiceToHost', '', 'idSlave', '', 'tbl_service', '', 'config_name,service_description', 1, '1,1,0,1', 0),
(145, 'tbl_host', 'tbl_lnkServicegroupToService', '', 'idSlaveH', '', 'tbl_servicegroup', '', 'servicegroup_name', 1, '0,0,0,1', 0),
(146, 'tbl_host', 'tbl_hostextinfo', '', 'host_name', '', '', '', 'host_name', 1, '0,0,0,0', 0),
(147, 'tbl_host', 'tbl_serviceextinfo', '', 'host_name', '', '', '', 'host_name', 1, '0,0,0,0', 0),
(148, 'tbl_hostdependency', 'tbl_lnkHostdependencyToHostgroup_DH', '', 'idMaster', '', 'tbl_hostgroup', '', 'hostgroup_name', 1, '0,0,0,1', 0),
(149, 'tbl_hostdependency', 'tbl_lnkHostdependencyToHostgroup_H', '', 'idMaster', '', 'tbl_hostgroup', '', 'hostgroup_name', 1, '0,0,0,1', 0),
(150, 'tbl_hostdependency', 'tbl_lnkHostdependencyToHost_DH', '', 'idMaster', '', 'tbl_host', '', 'host_name', 1, '0,0,0,1', 0),
(151, 'tbl_hostdependency', 'tbl_lnkHostdependencyToHost_H', '', 'idMaster', '', 'tbl_host', '', 'host_name', 1, '0,0,0,1', 0),
(152, 'tbl_hostescalation', 'tbl_lnkHostescalationToContact', '', 'idMaster', '', 'tbl_contact', '', 'contact_name', 1, '0,0,0,1', 0),
(153, 'tbl_hostescalation', 'tbl_lnkHostescalationToContactgroup', '', 'idMaster', '', 'tbl_contactgroup', '', 'contactgroup_name', 1, '0,0,0,1', 0),
(154, 'tbl_hostescalation', 'tbl_lnkHostescalationToHost', '', 'idMaster', '', 'tbl_host', '', 'host_name', 1, '0,0,0,1', 0),
(155, 'tbl_hostescalation', 'tbl_lnkHostescalationToHostgroup', '', 'idMaster', '', 'tbl_hostgroup', '', 'hostgroup_name', 1, '0,0,0,1', 0),
(156, 'tbl_hostgroup', 'tbl_lnkHostdependencyToHostgroup_DH', '', 'idSlave', '', 'tbl_hostdependency', '', 'config_name', 1, '0,0,0,1', 0),
(157, 'tbl_hostgroup', 'tbl_lnkHostdependencyToHostgroup_H', '', 'idSlave', '', 'tbl_hostdependency', '', 'config_name', 1, '0,0,0,1', 0),
(158, 'tbl_hostgroup', 'tbl_lnkHostescalationToHostgroup', '', 'idSlave', '', 'tbl_hostescalation', '', 'config_name', 1, '0,0,0,1', 0),
(159, 'tbl_hostgroup', 'tbl_lnkHostgroupToHost', '', 'idMaster', '', 'tbl_host', '', 'host_name', 1, '0,0,0,1', 0),
(160, 'tbl_hostgroup', 'tbl_lnkHostgroupToHostgroup', '', 'idMaster', '', 'tbl_hostgroup', '', 'hostgroup_name', 1, '0,0,0,1', 0),
(161, 'tbl_hostgroup', 'tbl_lnkHostgroupToHostgroup', '', 'idSlave', '', 'tbl_hostgroup', '', 'hostgroup_name', 1, '0,0,0,1', 0),
(162, 'tbl_hostgroup', 'tbl_lnkHosttemplateToHostgroup', '', 'idSlave', '', 'tbl_hosttemplate', '', 'template_name', 1, '0,0,0,1', 0),
(163, 'tbl_hostgroup', 'tbl_lnkHostToHostgroup', '', 'idSlave', '', 'tbl_host', '', 'host_name', 1, '0,0,0,1', 0),
(164, 'tbl_hostgroup', 'tbl_lnkServicedependencyToHostgroup_DH', '', 'idSlave', '', 'tbl_servicedependency', '', 'config_name', 1, '0,0,0,1', 0),
(165, 'tbl_hostgroup', 'tbl_lnkServicedependencyToHostgroup_H', '', 'idSlave', '', 'tbl_servicedependency', '', 'config_name', 1, '0,0,0,1', 0),
(166, 'tbl_hostgroup', 'tbl_lnkServiceescalationToHostgroup', '', 'idSlave', '', 'tbl_serviceescalation', '', 'config_name', 1, '0,0,0,1', 0),
(167, 'tbl_hostgroup', 'tbl_lnkServicetemplateToHostgroup', '', 'idSlave', '', 'tbl_servicetemplate', '', 'template_name', 1, '0,0,0,1', 0),
(168, 'tbl_hostgroup', 'tbl_lnkServiceToHostgroup', '', 'idSlave', '', 'tbl_service', '', 'config_name,service_description', 1, '0,0,0,1', 0),
(169, 'tbl_hostgroup', 'tbl_lnkServicegroupToService', '', 'idSlaveHG', '', 'tbl_servicegroup', '', 'servicegroup_name', 1, '0,0,0,1', 0),
(170, 'tbl_hosttemplate', 'tbl_lnkHosttemplateToContact', '', 'idMaster', '', 'tbl_contact', '', 'contact_name', 1, '0,0,0,1', 0),
(171, 'tbl_hosttemplate', 'tbl_lnkHosttemplateToContactgroup', '', 'idMaster', '', 'tbl_contactgroup', '', 'contactgroup_name', 1, '0,0,0,1', 0),
(172, 'tbl_hosttemplate', 'tbl_lnkHosttemplateToHost', '', 'idMaster', '', 'tbl_host', '', 'host_name', 1, '0,0,0,1', 0),
(173, 'tbl_hosttemplate', 'tbl_lnkHosttemplateToHostgroup', '', 'idMaster', '', 'tbl_hostgroup', '', 'hostgroup_name', 1, '0,0,0,1', 0),
(174, 'tbl_hosttemplate', 'tbl_lnkHosttemplateToHosttemplate', '', 'idMaster', '', 'tbl_service', '', 'config_name,service_description', 1, '0,0,0,1', 0),
(175, 'tbl_hosttemplate', 'tbl_lnkHosttemplateToHosttemplate', '', 'idSlave', '', 'tbl_hosttemplate', '', 'template_name', 1, '0,0,0,1', 0),
(176, 'tbl_hosttemplate', 'tbl_lnkHosttemplateToVariabledefinition', '', 'idMaster', '', 'tbl_variabledefinition', '', 'name', 1, '0,0,0,2', 0),
(177, 'tbl_hosttemplate', 'tbl_lnkHostToHosttemplate', '', 'idSlave', '', 'tbl_host', '', 'host_name', 1, '0,0,0,1', 0),
(178, 'tbl_service', 'tbl_lnkServicedependencyToService_DS', '', 'idSlave', '', 'tbl_servicedependency', '', 'config_name', 1, '1,1,0,1', 0),
(179, 'tbl_service', 'tbl_lnkServicedependencyToService_S', '', 'idSlave', '', 'tbl_servicedependency', '', 'config_name', 1, '1,1,0,1', 0),
(180, 'tbl_service', 'tbl_lnkServiceescalationToService', '', 'idSlave', '', 'tbl_serviceescalation', '', 'config_name', 1, '1,1,0,1', 0),
(181, 'tbl_service', 'tbl_lnkServicegroupToService', '', 'idSlaveS', '', 'tbl_servicegroup', '', 'servicegroup_name', 1, '0,0,0,1', 0),
(182, 'tbl_service', 'tbl_lnkServiceToContact', '', 'idMaster', '', 'tbl_contact', '', 'contact_name', 1, '0,0,0,1', 0),
(183, 'tbl_service', 'tbl_lnkServiceToContactgroup', '', 'idMaster', '', 'tbl_contactgroup', '', 'contactgroup_name', 1, '0,0,0,1', 0),
(184, 'tbl_service', 'tbl_lnkServiceToHost', '', 'idMaster', '', 'tbl_host', '', 'host_name', 1, '0,0,0,1', 0),
(185, 'tbl_service', 'tbl_lnkServiceToHostgroup', '', 'idMaster', '', 'tbl_hostgroup', '', 'hostgroup_name', 1, '0,0,0,1', 0),
(186, 'tbl_service', 'tbl_lnkServiceToServicegroup', '', 'idMaster', '', 'tbl_servicegroup', '', 'servicegroup_name', 1, '0,0,0,1', 0),
(187, 'tbl_service', 'tbl_lnkServiceToServicetemplate', '', 'idMaster', '', 'tbl_servicetemplate', '', 'template_name', 1, '0,0,0,1', 0),
(188, 'tbl_service', 'tbl_lnkServiceToVariabledefinition', '', 'idMaster', '', 'tbl_variabledefinition', '', 'name', 1, '0,0,0,2', 0),
(189, 'tbl_service', 'tbl_serviceextinfo', '', 'service_description', '', '', '', 'host_name', 1, '0,0,0,0', 0),
(190, 'tbl_servicedependency', 'tbl_lnkServicedependencyToHostgroup_DH', '', 'idMaster', '', 'tbl_hostgroup', '', 'hostgroup_name', 1, '0,0,0,1', 0),
(191, 'tbl_servicedependency', 'tbl_lnkServicedependencyToHostgroup_H', '', 'idMaster', '', 'tbl_hostgroup', '', 'hostgroup_name', 1, '0,0,0,1', 0),
(192, 'tbl_servicedependency', 'tbl_lnkServicedependencyToHost_DH', '', 'idMaster', '', 'tbl_host', '', 'host_name', 1, '0,0,0,1', 0),
(193, 'tbl_servicedependency', 'tbl_lnkServicedependencyToHost_H', '', 'idMaster', '', 'tbl_host', '', 'host_name', 1, '0,0,0,1', 0),
(194, 'tbl_servicedependency', 'tbl_lnkServicedependencyToService_DS', '', 'idMaster', '', 'tbl_service', '', 'config_name,service_description', 1, '0,0,0,1', 0),
(195, 'tbl_servicedependency', 'tbl_lnkServicedependencyToService_S', '', 'idMaster', '', 'tbl_service', '', 'config_name,service_description', 1, '0,0,0,1', 0),
(196, 'tbl_serviceescalation', 'tbl_lnkServiceescalationToContact', '', 'idMaster', '', 'tbl_contact', '', 'contact_name', 1, '0,0,0,1', 0),
(197, 'tbl_serviceescalation', 'tbl_lnkServiceescalationToContactgroup', '', 'idMaster', '', 'tbl_contactgroup', '', 'contactgroup_name', 1, '0,0,0,1', 0),
(198, 'tbl_serviceescalation', 'tbl_lnkServiceescalationToHost', '', 'idMaster', '', 'tbl_host', '', 'host_name', 1, '0,0,0,1', 0),
(199, 'tbl_serviceescalation', 'tbl_lnkServiceescalationToHostgroup', '', 'idMaster', '', 'tbl_hostgroup', '', 'hostgroup_name', 1, '0,0,0,1', 0),
(200, 'tbl_serviceescalation', 'tbl_lnkServiceescalationToService', '', 'idMaster', '', 'tbl_service', '', 'config_name,service_description', 1, '0,0,0,1', 0),
(201, 'tbl_servicegroup', 'tbl_lnkServicegroupToService', '', 'idMaster', '', 'tbl_service', '', 'config_name,service_description', 1, '0,0,0,1', 0),
(202, 'tbl_servicegroup', 'tbl_lnkServicegroupToServicegroup', '', 'idMaster', '', 'tbl_servicegroup', '', 'servicegroup_name', 1, '0,0,0,1', 0),
(203, 'tbl_servicegroup', 'tbl_lnkServicegroupToServicegroup', '', 'idSlave', '', 'tbl_servicegroup', '', 'servicegroup_name', 1, '0,0,0,1', 0),
(204, 'tbl_servicegroup', 'tbl_lnkServicetemplateToServicegroup', '', 'idSlave', '', 'tbl_servicetemplate', '', 'template_name', 1, '0,0,0,1', 0),
(205, 'tbl_servicegroup', 'tbl_lnkServiceToServicegroup', '', 'idSlave', '', 'tbl_service', '', 'config_name,service_description', 1, '0,0,0,1', 0),
(206, 'tbl_servicetemplate', 'tbl_lnkServicetemplateToContact', '', 'idMaster', '', 'tbl_contact', '', 'contact_name', 1, '0,0,0,1', 0),
(207, 'tbl_servicetemplate', 'tbl_lnkServicetemplateToContactgroup', '', 'idMaster', '', 'tbl_contactgroup', '', 'contactgroup_name', 1, '0,0,0,1', 0),
(208, 'tbl_servicetemplate', 'tbl_lnkServicetemplateToHost', '', 'idMaster', '', 'tbl_host', '', 'host_name', 1, '0,0,0,1', 0),
(209, 'tbl_servicetemplate', 'tbl_lnkServicetemplateToHostgroup', '', 'idMaster', '', 'tbl_hostgroup', '', 'hostgroup_name', 1, '0,0,0,1', 0),
(210, 'tbl_servicetemplate', 'tbl_lnkServicetemplateToServicegroup', '', 'idMaster', '', 'tbl_servicegroup', '', 'servicegroup_name', 1, '0,0,0,1', 0),
(211, 'tbl_servicetemplate', 'tbl_lnkServicetemplateToServicetemplate', '', 'idMaster', '', 'tbl_servicetemplate', '', 'template_name', 1, '0,0,0,1', 0),
(212, 'tbl_servicetemplate', 'tbl_lnkServicetemplateToServicetemplate', '', 'idSlave', '', 'tbl_servicetemplate', '', 'template_name', 1, '0,0,0,1', 0),
(213, 'tbl_servicetemplate', 'tbl_lnkServicetemplateToVariabledefinition', '', 'idMaster', '', 'tbl_variabledefinition', '', 'name', 1, '0,0,0,2', 0),
(214, 'tbl_servicetemplate', 'tbl_lnkServiceToServicetemplate', '', 'idSlave', '', 'tbl_service', '', 'config_name,service_description', 1, '0,0,0,1', 0),
(215, 'tbl_timeperiod', 'tbl_lnkTimeperiodToTimeperiod', '', 'idMaster', '', 'tbl_timeperiod', '', 'timeperiod_name', 1, '0,0,0,1', 0),
(216, 'tbl_timeperiod', 'tbl_lnkTimeperiodToTimeperiod', '', 'idSlave', '', 'tbl_timeperiod', '', 'timeperiod_name', 1, '0,0,0,1', 0),
(217, 'tbl_timeperiod', 'tbl_contact', '', 'host_notification_period', '', '', '', 'contact_name', 1, '1,1,2,0', 0),
(218, 'tbl_timeperiod', 'tbl_contact', '', 'service_notification_period', '', '', '', 'contact_name', 1, '1,1,2,0', 0),
(219, 'tbl_timeperiod', 'tbl_contacttemplate', '', 'host_notification_period', '', '', '', 'template_name', 1, '0,2,2,0', 0),
(220, 'tbl_timeperiod', 'tbl_contacttemplate', '', 'service_notification_period', '', '', '', 'template_name', 1, '0,2,2,0', 0),
(221, 'tbl_timeperiod', 'tbl_host', '', 'check_period', '', '', '', 'host_name', 1, '1,1,2,0', 0),
(222, 'tbl_timeperiod', 'tbl_host', '', 'notification_period', '', '', '', 'host_name', 1, '1,1,2,0', 0),
(223, 'tbl_timeperiod', 'tbl_hosttemplate', '', 'check_period', '', '', '', 'template_name', 1, '0,2,2,0', 0),
(224, 'tbl_timeperiod', 'tbl_hosttemplate', '', 'notification_period', '', '', '', 'template_name', 1, '0,2,2,0', 0),
(225, 'tbl_timeperiod', 'tbl_hostdependency', '', 'dependency_period', '', '', '', 'config_name', 1, '0,2,2,0', 0),
(226, 'tbl_timeperiod', 'tbl_hostescalation', '', 'escalation_period', '', '', '', 'config_name', 1, '0,2,2,0', 0),
(227, 'tbl_timeperiod', 'tbl_service', '', 'check_period', '', '', '', 'config_name,service_description', 1, '1,1,2,0', 0),
(228, 'tbl_timeperiod', 'tbl_service', '', 'notification_period', '', '', '', 'config_name,service_description', 1, '0,2,2,0', 0),
(229, 'tbl_timeperiod', 'tbl_servicetemplate', '', 'check_period', '', '', '', 'template_name', 1, '0,2,2,0', 0),
(230, 'tbl_timeperiod', 'tbl_servicetemplate', '', 'notification_period', '', '', '', 'template_name', 1, '1,1,2,0', 0),
(231, 'tbl_timeperiod', 'tbl_servicedependency', '', 'dependency_period', '', '', '', 'config_name', 1, '0,2,2,0', 0),
(232, 'tbl_timeperiod', 'tbl_serviceescalation', '', 'escalation_period', '', '', '', 'config_name', 1, '0,2,2,0', 0),
(233, 'tbl_timeperiod', 'tbl_timedefinition', '', 'tipId', '', '', '', 'id', 1, '0,0,0,3', 0),
(234, 'tbl_timeperiod', 'tbl_timeperiod', '', 'use_template', 'tbl_lnkTimeperiodToTimeperiodUse', 'timeperiod_name', '', '', 0, '', 2),
(235, 'tbl_timeperiod', 'tbl_lnkTimeperiodToTimeperiodUse', '', 'idMaster', '', 'tbl_timeperiod', '', 'timeperiod_name', 1, '0,0,0,1', 0),
(236, 'tbl_timeperiod', 'tbl_lnkTimeperiodToTimeperiodUse', '', 'idSlave', '', 'tbl_timeperiod', '', 'timeperiod_name', 1, '0,0,0,1', 0);
--
-- Modify existing tbl_info
--
UPDATE `tbl_info` SET `infotext`='<p><strong>User - webserver authentication</strong></p>\r\n<p>If your webserver uses authentication and the NagiosQL user name is the same which is actually logged in - the NagiosQL login process will passed. This means, that NagiosQL no longer shows a login page if this user is already logged in by webserver authentication.</p>' WHERE `id`=10;
UPDATE `tbl_info` SET `infotext`='<p><strong>Timeperiod - </strong><strong>timeperiod name</strong></p>\r\n<p>This directives is the short name used to identify the time period.</p>\r\n<p><em>Parameter name:</em> timeperiod_name<br> <em>Required:</em> yes</p>' WHERE `id`=152;
UPDATE `tbl_info` SET `infotext`='<p><strong>Timeperiod - </strong><strong>exclude</strong></p>\r\n<p>This directive is used to specify the short names of other timeperiod definitions whose time ranges should be excluded from this timeperiod. Multiple timeperiod names should be separated with a comma.</p>\r\n<p><em>Parameter name:</em> exclude<br> <em>Required:</em> no</p>' WHERE `id`=153;
UPDATE `tbl_info` SET `infotext`='<p><strong>Timeperiod - </strong><strong>alias</strong></p>\r\n<p>This directive is a longer name or description used to identify the time period.</p>\r\n<p><em>Parameter name:</em> alias<br> <em>Required:</em> yes</p>' WHERE `id`=154;
UPDATE `tbl_info` SET `infotext`='<p><strong>Timeperiod - </strong><strong>template name</strong></p>\r\n<p>Not yet implemented.</p>\r\n<p><em>Parameter name:</em> name<br> <em>Required:</em> no</p>' WHERE `id`=155;
UPDATE `tbl_info` SET `infotext`='<p><strong>Timeperiod - </strong><strong>time definition<br></strong></p>\r\n<p>The <em>sunday</em> through <em>saturday</em> directives are comma-delimited lists of time ranges that are "valid" times for a particular day of the week. Notice that there are seven different days for which you can define time ranges (Sunday through Saturday).</p>\r\n<p><em>Parameter name:</em> [weekday] [exception]<br> <em>Required:</em> no</p>' WHERE `id`=156;
UPDATE `tbl_info` SET `infotext`='<p><strong>Timeperiod - </strong><strong>time range<br></strong></p>\r\n<p>Each time range is in the form of <strong>HH:MM-HH:MM</strong>, where hours are specified on a 24 hour clock.  For example, <strong>00:15-24:00</strong> means 12:15am in the morning for this day until 12:20am midnight (a 23 hour, 45 minute total time range). If you wish to exclude an entire day from the timeperiod, simply do not include it in the timeperiod definition.</p>\r\n<p><em>Parameter name:</em> [weekday] [exception]<br> <em>Required:</em> no</p>' WHERE `id`=157;
UPDATE `tbl_info` SET `infotext`='<p><strong>Timeperiod - </strong><strong>time definition<br></strong></p>\r\n<p>The weekday directives ("<em>sunday</em>" through "<em>saturday</em>")are comma-delimited lists of time ranges that are "valid" times for a particular day of the week. Notice that there are seven different days for which you can define time ranges (Sunday through Saturday).&nbsp;</p>\r\n<p>You can also specify several different types of exceptions to the standard rotating weekday schedule. Exceptions can take a number of different forms including single days of a specific or generic month, single weekdays in a month, or single calendar dates. You can also specify a range of days/dates and even specify skip intervals to obtain functionality described by "every 3 days between these dates". Rather than list all the possible formats for exception strings, Weekdays and different types of exceptions all have different levels of precedence, so its important to understand how they can affect each other. More information on this can be found in the documentation on timeperiods.</p>\r\n<p><em>Parameter name:</em> [weekday] [exception]<br> <em>Required:</em> no</p>' WHERE `id`=158;
UPDATE `tbl_info` SET `infotext`='<p><strong>Timeperiod - </strong><strong>time range<br></strong></p>\r\n<p>Each time range is in the form of <strong>HH:MM-HH:MM</strong>, where hours are specified on a 24 hour clock.  For example, <strong>00:15-24:00</strong> means 12:15am in the morning for this day until 12:00am midnight (a 23 hour, 45 minute total time range). If you wish to exclude an entire day from the timeperiod, simply do not include it in the timeperiod definition.</p>\r\n<p><em>Parameter name:</em> [weekday] [exception]<br> <em>Required:</em> no</p>' WHERE `id`=159;
UPDATE `tbl_info` SET `infotext`='<p>The nagios version which is running in this domain.</p>\r\n<p>Be sure you select the correct version here - otherwise not all configuration options are available or not supported options are shown.</p>\r\n<p>You can change this with a running configuration - NagiosQL will then upgrade or downgrade your configuration. Don''t forget to write your complete configuration after a version change!</p>' WHERE `tbl_info`.`key1` = 'domain' AND `tbl_info`.`key2` = 'version' AND `tbl_info`.`version` = 'all' AND `tbl_info`.`language` = 'default';
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('host', 'services', 'all', 'default', '<p><strong>Host - service settings</strong></p>\r\n<p><span id="result_box" lang="en"><span>This box can be used to allocate already existing services to a host.&nbsp;</span></span></p>\r\n<p>This is an internal function of NagiosQL.</p>\r\n<p><span id="result_box" lang="en"><span><strong>Note:</strong> To activate the changes, the corresponding service definitions have to be rewritten!</span></span></p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('timeperiod', 'name', 'all', 'default', '<p><strong>Timeperiod - </strong><strong>name</strong></p>\r\n<p>It''s just a "template" name that can be referenced in other object definitions so they can inherit the objects properties/variables. Template names must be unique amongst objects of the same type, so you  can''t have two or more time definitions that have "mytemplate" as  their template name.</p>\r\n<p><em>Parameter name:</em> name<br /> <em>Required:</em> no</p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('timeperiod', 'include', '3', 'default', '<p><strong>Timeperiod - </strong><strong>include</strong></p>\r\n<p>This directive is used to specify the short names (template names) of other timeperiod definitions whose time ranges should be included to this timeperiod. Multiple timeperiod names should be separated with a comma.</p>\r\n<p><em>Parameter name:</em> use<br /> <em>Required:</em> no</p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('user', 'adminenable', 'all', 'default', '<p><strong>User - enable group administration<br /></strong></p>\r\n<p>If this option is selected, the specified user is able to modify the access group for every object definition. This should be restricted only to administrators; otherwise a user might be able to lock himself out.<span id="result_box" lang="en"><span></span></span></p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('group', 'userrights', 'all', 'default', '<p><strong>Group - user rights</strong></p>\r\n<p>Define the object access rights for a user.</p>\r\n<p><strong>READ</strong> = The user can see the objects belong to this group<br /><strong>WRITE</strong> = The user can modify the objects belong to this group<br /><strong>LINK</strong> = The user can use the objects belong to this group to link them in other objects*<br /><br />* <em>Example:</em> If a time object belongs to this group - the user can user can add (link) this time object to his contact objects.</p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('domain', 'conffile', 'all', 'default', '<p>Absolute path to your Nagios config file.<br /><br />Examples:<br />/etc/nagios/nagios.cfg<br />/usr/local/nagios/etc/nagios.cfg<br /><br />This is used to verify your Nagios configuration directly from NagiosQL.</p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('domain', 'enable_common', 'all', 'default', '<p>This option is used to enable or disable the global common domain functionality.</p>\r\n<p>If this option is enabled, all objects from the global common domain will be added to this domains configuration files. The global common domain can be used to define objects like timeperiods or contacts that are used in all domains the same.</p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('domain', 'utf8_decode', 'all', 'default', '<p>This is an experimental option!</p>\r\n<p>If this option is enabled, UTF8 data from database will be translated to ISO in configuration file. So, the configuration files will be in ISO mode. This could be helpful, if Nagios does not understand the UTF8 data from NagiosQL.</p>\r\n<p>Tested only with Western European configurations!</p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('domain', 'picturedir', 'all', 'default', '<p><strong>Relative</strong> path to your nagios icon images.<br /><br />Example:<br />/my/own/images/</p>\r\n<p>This path is based on your nagios standard image path. Images are assumed to be in the <strong>logos/</strong> subdirectory in your HTML images directory (i.e. /usr/local/nagios/share/images/logos).</p>\r\n<p>So in the example above, the images are located in:</p>\r\n<p>/usr/local/nagios/share/images/logos<span style="color: #ff0000;">/my/own/images/</span></p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('common', 'accessgroup', 'all', 'default', '<p><strong>Access group</strong></p>\r\n<p>Select an access group name to restrict this object to the group members.</p>');
--
--  Modify existing tbl_settings
--
DELETE FROM `tbl_settings` WHERE `tbl_settings`.`category` = 'path' AND `tbl_settings`.`name` = 'physical';
DELETE FROM `tbl_settings` WHERE `tbl_settings`.`category` = 'path' AND `tbl_settings`.`name` = 'root';
DELETE FROM `tbl_settings` WHERE `tbl_settings`.`category` = 'db' AND `tbl_settings`.`name` = 'magic_quotes';
INSERT INTO `tbl_settings` (`id`, `category`, `name`, `value`) VALUES ('', 'common', 'tplcheck', '0');
INSERT INTO `tbl_settings` (`id`, `category`, `name`, `value`) VALUES ('', 'common', 'updcheck', '0');
INSERT INTO `tbl_settings` (`id`, `category`, `name`, `value`) VALUES ('', 'network', 'Proxy', '0');
INSERT INTO `tbl_settings` (`id`, `category`, `name`, `value`) VALUES ('', 'network', 'ProxyServer', '');
INSERT INTO `tbl_settings` (`id`, `category`, `name`, `value`) VALUES ('', 'network', 'ProxyUser', '');
INSERT INTO `tbl_settings` (`id`, `category`, `name`, `value`) VALUES ('', 'network', 'ProxyPasswd', '');
UPDATE `tbl_settings` SET `value` = '3.1.0' WHERE `tbl_settings`.`name` = 'version' LIMIT 1;

--
--  Modify some field settings (added for 3.2)
--

ALTER TABLE `tbl_hostextinfo` CHANGE `host_name` `host_name` INT( 11 ) NOT NULL;
ALTER TABLE `tbl_info` CHANGE `key1` `key1` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_info` CHANGE `key2` `key2` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_info` CHANGE `language` `language` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
TRUNCATE `tbl_logbook`;
ALTER TABLE `tbl_logbook` CHANGE `time` `time` DATETIME NOT NULL ;
ALTER TABLE `tbl_serviceextinfo` CHANGE `host_name` `host_name` INT( 11 ) NOT NULL;
ALTER TABLE `tbl_timeperiod` PACK_KEYS = DEFAULT;
ALTER TABLE `tbl_user` CHANGE `last_login` `last_login` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `tbl_user` CHANGE `last_modified` `last_modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';

UPDATE `tbl_info` SET `infotext`='<p><strong>User - webserver authentification</strong></p>\r\n<p>If your webserver uses authentification and the NagiosQL user name is the same which is actually logged in - the NagiosQL login process will passed. This means, that NagiosQL no longer shows a login page if this user is already logged in by webserver authentification.</p>' WHERE `key1`='user' AND `key2`='webserverauth' AND `version`='all' AND `language`='default';
UPDATE `tbl_info` SET `infotext`='<p><strong>Host - Templates</strong></p>\r\n<p>You can add one or more host templates to a host configuration. Nagios will add the definitions from each template to a host configuration.</p>\r\n<p>If you add more than one template - the sort order will be used to overwrite configuration items which are defined inside templates before.</p>\r\n<p>The host configuration itselves will overwrite all values which are defined in templates before and pass all values which are not defined.</p>' WHERE `key1`='host' AND `key2`='templateadd' AND `version`='all' AND `language`='default';
UPDATE `tbl_info` SET `infotext`='<p><strong>Service - Templates</strong></p>\r\n<p>You can add one or more service templates to a service configuration. Nagios will add the definitions from each template to a service configuration.</p>\r\n<p>If you add more than one template - the sort order will be used to overwrite configuration items which are defined inside templates before.</p>\r\n<p>The host configuration itselves will overwrite all values which are defined in templates before and pass all values which are not defined.</p>' WHERE `key1`='service' AND `key2`='templateadd' AND `version`='all' AND `language`='default';
UPDATE `tbl_info` SET `infotext`='<p><strong>Contact - Templates</strong></p>\r\n<p>You can add one or more contact templates to a contact configuration. Nagios will add the definitions from each template to a contact configuration.</p>\r\n<p>If you add more than one template - the sort order will be used to overwrite configuration items which are defined inside templates before.</p>\r\n<p>The host configuration itselves will overwrite all values which are defined in templates before and pass all values which are not defined.</p>' WHERE `key1`='contact' AND `key2`='templateadd' AND `version`='all' AND `language`='default';
UPDATE `tbl_info` SET `infotext`='<p><strong>Timeperiod - </strong><strong>name</strong></p>\r\n<p>Its just a "template" name that can be referenced in other object  definitions so they can inherit the objects properties/variables.   Template names must be unique amongst objects of the same type, so you  can''t have two or more time definitions that have "mytemplate" as  their template name.</p>\r\n<p><em>Parameter name:</em> name<br /> <em>Required:</em> no</p>' WHERE `key1`='timeperiod' AND `key2`='name' AND `version`='all' AND `language`='default';
UPDATE `tbl_info` SET `infotext`='<p><strong>Timeperiod - </strong><strong>include</strong></p>\r\n<p>This directive is used to specify the short names (template names) of other timeperiod  definitions whose time ranges should be included to this timeperiod.  Multiple timeperiod names should be separated with a comma.</p>\r\n<p><em>Parameter name:</em> use<br /> <em>Required:</em> no</p>' WHERE `key1`='timeperiod' AND `key2`='include' AND `version`='3' AND `language`='default';
UPDATE `tbl_info` SET `infotext`='<p>This is an experimental option!</p>\r\n<p>If this option is enabled, UTF8 data from database will be translated to ISO in configuration file. So, the configuration files will be in ISO mode. This could be helpful, if nagios does not understand the UTF8 data from NagiosQL.</p>\r\n<p>Tested only with western european configurations!</p>' WHERE `key1`='domain' AND `key2`='utf8_decode' AND `version`='all' AND `language`='default';