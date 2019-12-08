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
--  Component : Update from NagiosQL 3.1.x to NagiosQL 3.2.0
--  Website   : https://sourceforge.net/projects/nagiosql/
--  Version   : 3.4.1
--  GIT Repo  : https://gitlab.com/wizonet/NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  Modify existing tbl_settings
--
UPDATE `tbl_settings` SET `value` = '3.2.0' WHERE `tbl_settings`.`name` = 'version' LIMIT 1;
INSERT INTO `tbl_settings` (`id` ,`category` ,`name` ,`value`) VALUES (NULL , 'path', 'base_url', '/');
--
--  Modify object tables
--
ALTER TABLE `tbl_command` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `command_type`;
UPDATE `tbl_command`  SET `register`=`active`;
ALTER TABLE `tbl_contact` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `use_template_tploptions`;
UPDATE `tbl_contact`  SET `register`=`active`;
ALTER TABLE `tbl_contactgroup` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `contactgroup_members`;
UPDATE `tbl_contactgroup`  SET `register`=`active`;
ALTER TABLE `tbl_contacttemplate` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '0' AFTER `use_template_tploptions`;
UPDATE `tbl_contacttemplate`  SET `register`='0';
ALTER TABLE `tbl_host` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `name`;
UPDATE `tbl_host`  SET `register`=`active`;
ALTER TABLE `tbl_hostdependency` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `dependency_period`;
UPDATE `tbl_hostdependency`  SET `register`=`active`;
ALTER TABLE `tbl_hostescalation` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `escalation_options`;
UPDATE `tbl_hostescalation`  SET `register`=`active`;
ALTER TABLE `tbl_hostextinfo` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `3d_coords`;
UPDATE `tbl_hostextinfo`  SET `register`=`active`;
ALTER TABLE `tbl_hostgroup` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `action_url`;
UPDATE `tbl_hostgroup`  SET `register`=`active`;
ALTER TABLE `tbl_hosttemplate` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '0' AFTER `use_variables`;
UPDATE `tbl_hosttemplate`  SET `register`='0';
ALTER TABLE `tbl_service` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `name`;
UPDATE `tbl_service`  SET `register`=`active`;
ALTER TABLE `tbl_servicedependency` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `dependency_period`;
UPDATE `tbl_servicedependency`  SET `register`=`active`;
ALTER TABLE `tbl_serviceescalation` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `escalation_options`;
UPDATE `tbl_serviceescalation`  SET `register`=`active`;
ALTER TABLE `tbl_serviceextinfo` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `icon_image_alt`;
UPDATE `tbl_serviceextinfo`  SET `register`=`active`;
ALTER TABLE `tbl_servicegroup` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `action_url`;
UPDATE `tbl_servicegroup`  SET `register`=`active`;
ALTER TABLE `tbl_servicetemplate` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '0' AFTER `use_variables`;
UPDATE `tbl_servicetemplate`  SET `register`='0';
ALTER TABLE `tbl_timeperiod` ADD `register` ENUM( '0', '1' ) NOT NULL DEFAULT '1' AFTER `name`;
UPDATE `tbl_timeperiod`  SET `register`=`active`;

CREATE TABLE IF NOT EXISTS `tbl_menu` (
  `mnuId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mnuTopId` int(10) unsigned NOT NULL,
  `mnuGrpId` int(10) unsigned NOT NULL DEFAULT '0',
  `mnuCntId` int(10) unsigned NOT NULL,
  `mnuName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mnuLink` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mnuActive` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `mnuOrderId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`mnuId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=39 ;

INSERT INTO `tbl_menu` (`mnuId`, `mnuTopId`, `mnuGrpId`, `mnuCntId`, `mnuName`, `mnuLink`, `mnuActive`, `mnuOrderId`) VALUES
(1, 0, 0, 1, 'Main page', 'admin.php', 1, 1),
(2, 0, 0, 1, 'Supervision', 'admin/monitoring.php', 1, 2),
(3, 0, 0, 1, 'Alarming', 'admin/alarming.php', 1, 3),
(4, 0, 0, 1, 'Commands', 'admin/commands.php', 1, 4),
(5, 0, 0, 1, 'Specialties', 'admin/specials.php', 1, 5),
(6, 0, 0, 1, 'Tools', 'admin/tools.php', 1, 6),
(7, 0, 0, 1, 'Administration', 'admin/administration.php', 1, 7),
(8, 2, 0, 1, 'Host', 'admin/hosts.php', 1, 1),
(9, 2, 0, 1, 'Services', 'admin/services.php', 1, 2),
(10, 2, 0, 1, 'Host groups', 'admin/hostgroups.php', 1, 3),
(11, 2, 0, 1, 'Service groups', 'admin/servicegroups.php', 1, 4),
(12, 2, 0, 1, 'Host templates', 'admin/hosttemplates.php', 1, 5),
(13, 2, 0, 1, 'Service templates', 'admin/servicetemplates.php', 1, 6),
(14, 3, 0, 1, 'Contact data', 'admin/contacts.php', 1, 1),
(15, 3, 0, 1, 'Contact groups', 'admin/contactgroups.php', 1, 2),
(16, 3, 0, 1, 'Time periods', 'admin/timeperiods.php', 1, 3),
(17, 3, 0, 1, 'Contact templates', 'admin/contacttemplates.php', 1, 4),
(18, 4, 0, 1, 'Definitions', 'admin/checkcommands.php', 1, 1),
(19, 5, 0, 1, 'Host dependency', 'admin/hostdependencies.php', 1, 1),
(20, 5, 0, 1, 'Host escalation', 'admin/hostescalations.php', 1, 2),
(21, 5, 0, 1, 'Extended Host', 'admin/hostextinfo.php', 1, 3),
(22, 5, 0, 1, 'Service dependency', 'admin/servicedependencies.php', 1, 4),
(23, 5, 0, 1, 'Service escalation', 'admin/serviceescalations.php', 1, 5),
(24, 5, 0, 1, 'Extended Service', 'admin/serviceextinfo.php', 1, 6),
(25, 6, 0, 1, 'Data import', 'admin/import.php', 1, 1),
(26, 6, 0, 1, 'Delete backup files', 'admin/delbackup.php', 1, 2),
(27, 6, 0, 1, 'Delete config files', 'admin/delconfig.php', 1, 3),
(28, 6, 0, 1, 'Nagios config', 'admin/nagioscfg.php', 1, 4),
(29, 6, 0, 1, 'CGI config', 'admin/cgicfg.php', 1, 5),
(30, 6, 0, 1, 'Nagios control', 'admin/verify.php', 1, 6),
(31, 7, 0, 1, 'New password', 'admin/password.php', 1, 1),
(32, 7, 0, 1, 'User admin', 'admin/user.php', 1, 2),
(33, 7, 0, 1, 'Group admin', 'admin/group.php', 1, 3),
(34, 7, 0, 1, 'Menu access', 'admin/menuaccess.php', 1, 4),
(35, 7, 0, 1, 'Data domains', 'admin/datadomain.php', 1, 5),
(36, 7, 0, 1, 'Config targets', 'admin/configtargets.php', 1, 6),
(37, 7, 0, 1, 'Logbook', 'admin/logbook.php', 1, 7),
(38, 7, 0, 1, 'Settings', 'admin/settings.php', 1, 8),
(39, 7, 0, 1, 'Help editor', 'admin/helpedit.php', 1, 9),
(40, 7, 0, 1, 'Support', 'admin/support.php', 1, 10);

UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 1) WHERE `tbl_menu`.`mnuId` = 8 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 7) WHERE `tbl_menu`.`mnuId` = 9 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 8) WHERE `tbl_menu`.`mnuId` = 10 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 9) WHERE `tbl_menu`.`mnuId` = 11 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 26) WHERE `tbl_menu`.`mnuId` = 12 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 27) WHERE `tbl_menu`.`mnuId` = 13 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 5) WHERE `tbl_menu`.`mnuId` = 14 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 6) WHERE `tbl_menu`.`mnuId` = 15 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 2) WHERE `tbl_menu`.`mnuId` = 16 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 28) WHERE `tbl_menu`.`mnuId` = 17 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 4) WHERE `tbl_menu`.`mnuId` = 18 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 12) WHERE `tbl_menu`.`mnuId` = 19 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 13) WHERE `tbl_menu`.`mnuId` = 20 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 14) WHERE `tbl_menu`.`mnuId` = 21 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 10) WHERE `tbl_menu`.`mnuId` = 22 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 11) WHERE `tbl_menu`.`mnuId` = 23 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 15) WHERE `tbl_menu`.`mnuId` = 24 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 16) WHERE `tbl_menu`.`mnuId` = 25 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 17) WHERE `tbl_menu`.`mnuId` = 26 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 32) WHERE `tbl_menu`.`mnuId` = 27 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 22) WHERE `tbl_menu`.`mnuId` = 28 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 23) WHERE `tbl_menu`.`mnuId` = 29 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 19) WHERE `tbl_menu`.`mnuId` = 30 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 20) WHERE `tbl_menu`.`mnuId` = 31 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 18) WHERE `tbl_menu`.`mnuId` = 32 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 31) WHERE `tbl_menu`.`mnuId` = 33 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 24) WHERE `tbl_menu`.`mnuId` = 34 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 25) WHERE `tbl_menu`.`mnuId` = 35 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 21) WHERE `tbl_menu`.`mnuId` = 37 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 29) WHERE `tbl_menu`.`mnuId` = 38 ;
UPDATE `tbl_menu` SET `mnuGrpId` = (SELECT `access_group` FROM `tbl_submenu` WHERE `tbl_submenu`.`id` = 30) WHERE `tbl_menu`.`mnuId` = 39 ;

DROP TABLE `tbl_mainmenu`;
DROP TABLE `tbl_submenu`;

RENAME TABLE `tbl_domain` TO `tbl_datadomain`;
CREATE TABLE `tbl_configtarget` AS SELECT * FROM `tbl_datadomain`;
ALTER TABLE `tbl_configtarget` ENGINE = MYISAM;
ALTER TABLE `tbl_datadomain` DROP `server` ,
DROP `method` ,
DROP `user` ,
DROP `password` ,
DROP `ssh_key_path` ,
DROP `basedir` ,
DROP `hostconfig` ,
DROP `serviceconfig` ,
DROP `backupdir` ,
DROP `hostbackup` ,
DROP `servicebackup` ,
DROP `nagiosbasedir` ,
DROP `importdir` ,
DROP `picturedir` ,
DROP `commandfile` ,
DROP `binaryfile` ,
DROP `pidfile` ,
DROP `conffile`,
ADD `targets` INT UNSIGNED NOT NULL AFTER `alias` ;
DELETE FROM `tbl_configtarget` WHERE `domain`='common';
ALTER TABLE `tbl_configtarget` ADD PRIMARY KEY ( `id` );
ALTER TABLE `tbl_configtarget` CHANGE `id` `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT; 
ALTER TABLE `tbl_configtarget` DROP `enable_common` , DROP `utf8_decode`;
ALTER TABLE `tbl_configtarget` CHANGE `conffile` `conffile` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_configtarget` CHANGE `domain` `target` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_datadomain`   CHANGE `domain` `domain` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_configtarget` ADD UNIQUE ( `target` );
UPDATE `tbl_datadomain` SET `id` = '0' WHERE `domain` = 'common';
UPDATE `tbl_datadomain` SET `targets`= (SELECT `id` FROM `tbl_configtarget` WHERE `target`=`tbl_datadomain`.`domain`);

CREATE TABLE IF NOT EXISTS `tbl_lnkServicedependencyToServicegroup_DS` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `tbl_lnkServicedependencyToServicegroup_S` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `tbl_lnkServiceescalationToServicegroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `tbl_servicedependency` ADD `dependent_servicegroup_name` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `dependent_service_description`;
ALTER TABLE `tbl_servicedependency` ADD `servicegroup_name` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `service_description`;
ALTER TABLE `tbl_serviceescalation` ADD `servicegroup_name` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `service_description`;

CREATE TABLE IF NOT EXISTS `tbl_language` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
  `last_modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT INTO `tbl_language` (`id`, `language`, `locale`, `active`, `last_modified`) VALUES(1, 'English', 'en_GB', '1', NOW());
INSERT INTO `tbl_language` (`id`, `language`, `locale`, `active`, `last_modified`) VALUES(2, 'German', 'de_DE', '1', NOW());
INSERT INTO `tbl_language` (`id`, `language`, `locale`, `active`, `last_modified`) VALUES(3, 'Chinese (Simplified)', 'zh_CN', '1', NOW());
INSERT INTO `tbl_language` (`id`, `language`, `locale`, `active`, `last_modified`) VALUES(5, 'Italian', 'it_IT', '1', NOW());
INSERT INTO `tbl_language` (`id`, `language`, `locale`, `active`, `last_modified`) VALUES(6, 'French', 'fr_FR', '1', NOW());
INSERT INTO `tbl_language` (`id`, `language`, `locale`, `active`, `last_modified`) VALUES(7, 'Russian', 'ru_RU', '1', NOW());
INSERT INTO `tbl_language` (`id`, `language`, `locale`, `active`, `last_modified`) VALUES(8, 'Spanish', 'es_ES', '1', NOW());
INSERT INTO `tbl_language` (`id`, `language`, `locale`, `active`, `last_modified`) VALUES(9, 'Portuguese (Brazilian)', 'pt_BR', '1', NOW());
INSERT INTO `tbl_language` (`id`, `language`, `locale`, `active`, `last_modified`) VALUES(11, 'Dutch', 'nl_NL', '1', NOW());
INSERT INTO `tbl_language` (`id`, `language`, `locale`, `active`, `last_modified`) VALUES(13, 'Danish', 'da_DK', '1', NOW());

ALTER TABLE `tbl_user` DROP `access_rights`;
ALTER TABLE `tbl_user` ADD `language` VARCHAR( 20 ) NOT NULL AFTER `nodelete` ;
ALTER TABLE `tbl_user` ADD `domain` INT UNSIGNED NOT NULL DEFAULT '1' AFTER `language`; 

INSERT INTO `tbl_relationinformation` (`master`, `tableName1`, `tableName2`, `fieldName`, `linkTable`, `target1`, `target2`, `targetKey`, `fullRelation`, `flags`, `type`) VALUES ('tbl_group', 'tbl_user', '', 'users', 'tbl_lnkGroupToUser', 'username', '', '', 0, '', 0);
INSERT INTO `tbl_relationinformation` (`master`, `tableName1`, `tableName2`, `fieldName`, `linkTable`, `target1`, `target2`, `targetKey`, `fullRelation`, `flags`, `type`) VALUES ('tbl_group', 'tbl_lnkGroupToUser', '', 'idMaster', '', 'tbl_user', '', 'username', 1, '0,0,0,1', 0);
INSERT INTO `tbl_relationinformation` (`master`, `tableName1`, `tableName2`, `fieldName`, `linkTable`, `target1`, `target2`, `targetKey`, `fullRelation`, `flags`, `type`) VALUES ('tbl_servicedependency', 'tbl_servicegroup', '', 'dependent_servicegroup_name', 'tbl_lnkServicedependencyToServicegroup_DS', 'servicegroup_name', '', '', '0', '', '2');
INSERT INTO `tbl_relationinformation` (`master`, `tableName1`, `tableName2`, `fieldName`, `linkTable`, `target1`, `target2`, `targetKey`, `fullRelation`, `flags`, `type`) VALUES ('tbl_servicedependency', 'tbl_servicegroup', '', 'servicegroup_name', 'tbl_lnkServicedependencyToServicegroup_S', 'servicegroup_name', '', '', '0', '', '2');
INSERT INTO `tbl_relationinformation` (`master`, `tableName1`, `tableName2`, `fieldName`, `linkTable`, `target1`, `target2`, `targetKey`, `fullRelation`, `flags`, `type`) VALUES ('tbl_servicedependency', 'tbl_lnkServicedependencyToServicegroup_DS', '', 'idMaster', '', 'tbl_servicegroup', '', 'servicegroup_name', '1', '0,0,0,1', '0');
INSERT INTO `tbl_relationinformation` (`master`, `tableName1`, `tableName2`, `fieldName`, `linkTable`, `target1`, `target2`, `targetKey`, `fullRelation`, `flags`, `type`) VALUES ('tbl_servicedependency', 'tbl_lnkServicedependencyToServicegroup_S', '', 'idMaster', '', 'tbl_servicegroup', '', 'servicegroup_name', '1', '0,0,0,1', '0');
INSERT INTO `tbl_relationinformation` (`master`, `tableName1`, `tableName2`, `fieldName`, `linkTable`, `target1`, `target2`, `targetKey`, `fullRelation`, `flags`, `type`) VALUES ('tbl_serviceescalation', 'tbl_servicegroup', '', 'servicegroup_name', 'tbl_lnkServiceescalationToServicegroup', 'servicegroup_name', '', '', '0', '', '2');
INSERT INTO `tbl_relationinformation` (`master`, `tableName1`, `tableName2`, `fieldName`, `linkTable`, `target1`, `target2`, `targetKey`, `fullRelation`, `flags`, `type`) VALUES ('tbl_serviceescalation', 'tbl_lnkServiceescalationToServicegroup', '', 'idMaster', '', 'tbl_servicegroup', '', 'servicegroup_name', '1', '0,0,0,1', '0');

INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('common', 'registered', 'all', 'default', '<p><strong>Register</strong></p>\r\n<p>This variable is used to indicate whether or not the object definition should be "registered" with Nagios. By default, all object definitions are registered. If you are using a partial object definition as a template, you would want to prevent it from being registered (an example of this is provided later). Values are as follows: 0 = do NOT register object definition, 1 = register object definition (this is the default). This variable is NOT inherited; every (partial) object definition used as a template must explicitly set the <em>register</em> directive to be <em>0</em>. This prevents the need to override an inherited <em>register</em> directive with a value of <em>1</em> for every object that should be registered.</p>\r\n<p><em>Parameter name:</em> register<br> <em>Required:</em> yes</p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('servicedependency', 'dependent_servicegroup_name', 'all', 'default', '<p><strong>Servicedependency -</strong><strong> dependent servicegroup name</strong><strong></strong></p>\r\n<p>This directive is used to identify the <em>name</em> of the <em>dependent</em> servicegroup.</p>\r\n<p><em>Parameter name:</em> dependent_servicegroup_name<br> <em>Required:</em> yes (no, if a dependent service is defined)</p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('servicedependency', 'servicegroup_name', 'all', 'default', '<p><strong>Servicedependency -</strong><strong> </strong><strong>servicegroup name</strong><strong></strong></p>\r\n<p>This directive is used to identify the <em>name</em> of the servicegroup <em>that is being depended upon</em> (also referred to as the master service).</p>\r\n<p><em>Parameter name:</em> servicegroup_name<br> <em>Required:</em> yes (no, if a service is defined)</p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('serviceescalation', 'servicegroup', 'all', 'default', '<p><strong>Serviceescalation - </strong><strong>servicegroup name</strong><strong></strong></p>\r\n<p>This directive is used to identify the <em>name</em> of the servicegroup the escalation should apply to.</p>\r\n<p><em>Parameter name:</em> servicegroup_name<br> <em>Required:</em> yes (no, if a service is defined)</p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('user', 'language', 'all', 'default', '<p><strong>User - language<br /></strong></p>\r\n<p>Defines a default UI language for the user.</p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('user', 'standarddomain', 'all', 'default', '<p><strong>User - standard domain<br /></strong></p>\r\n<p>Defines a standard domain for the user. After the user has logged in, the defined domain is pre-selected.</p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('domain', 'targets', 'all', 'default', '<p>Select a configuration domain which is assigned to this data domain</p>\r\n<p>The settings where to store the configuration files are defined in a configuration domain. Select here the desired target for your configuration files.</p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('domain', 'ssh_host_key', 'all', 'default', 'Absolute path to the ssh key directory for the defined ssh user.<br><br>Examples:<br>/etc/nagiosql/ssh/ <br>/usr/local/nagios/etc/.ssh/<br><br>This directory includes the key file (id_rsa) for the user to connect to the remote system. Note, that the file name is set to id_rsa!');
UPDATE `tbl_info` SET `infotext`='<p><strong>Group - user rights</strong></p>\r\n<p>Define the object access rights for a user.</p>\r\n<p><strong>READ</strong> = The user can see the objects belong to this group<br /><strong>WRITE</strong> = The user can modify the objects belong to this group<br /><strong>LINK</strong> = The user can use the objects belong to this group to link them in other objects*<br /><br />* <em>Example:</em> If a time object belongs to this group - the user can add (link) this time object to his contact objects.</p>' WHERE `key1`='group' AND `key2`='userrights' AND `version`='all' AND `language`='default';

UPDATE `tbl_settings` SET `name`='proxy' WHERE `category`='network' AND `name`='Proxy';
UPDATE `tbl_settings` SET `name`='proxyserver' WHERE `category`='network' AND `name`='ProxyServer';
UPDATE `tbl_settings` SET `name`='proxyuser' WHERE `category`='network' AND `name`='ProxyUser';
UPDATE `tbl_settings` SET `name`='proxypasswd' WHERE `category`='network' AND `name`='ProxyPasswd';

UPDATE `tbl_user` SET `admin_enable` = '1' WHERE `tbl_user`.`id`=1;

--
--  Modify some field settings
--

ALTER TABLE `tbl_configtarget` CHANGE `ssh_key_path` `ssh_key_path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_configtarget` CHANGE `picturedir` `picturedir` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_configtarget` CHANGE `version` `version` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '3';
ALTER TABLE `tbl_configtarget` CHANGE `active` `active` ENUM( '0', '1' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1';
ALTER TABLE `tbl_contact` CHANGE `email` `email` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contact` CHANGE `pager` `pager` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contact` CHANGE `address1` `address1` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contact` CHANGE `address2` `address2` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contact` CHANGE `address3` `address3` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contact` CHANGE `address4` `address4` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contact` CHANGE `address5` `address5` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contact` CHANGE `address6` `address6` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contactgroup` CHANGE `contactgroup_members` `contactgroup_members` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `tbl_contacttemplate` CHANGE `email` `email` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contacttemplate` CHANGE `pager` `pager` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contacttemplate` CHANGE `address1` `address1` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contacttemplate` CHANGE `address2` `address2` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contacttemplate` CHANGE `address3` `address3` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contacttemplate` CHANGE `address4` `address4` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contacttemplate` CHANGE `address5` `address5` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_contacttemplate` CHANGE `address6` `address6` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_datadomain` CHANGE `version` `version` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT '3';
ALTER TABLE `tbl_datadomain` CHANGE `active` `active` ENUM( '0', '1' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1';
ALTER TABLE `tbl_group` CHANGE `description` `description` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_group` CHANGE `active` `active` ENUM( '0', '1' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1';
ALTER TABLE `tbl_host` CHANGE `display_name` `display_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_host` CHANGE `check_command` `check_command` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_host` CHANGE `initial_state` `initial_state` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_host` CHANGE `flap_detection_options` `flap_detection_options` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_host` CHANGE `notification_options` `notification_options` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_host` CHANGE `stalking_options` `stalking_options` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_host` CHANGE `notes` `notes` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_host` CHANGE `notes_url` `notes_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_host` CHANGE `action_url` `action_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_host` CHANGE `icon_image_alt` `icon_image_alt` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_host` CHANGE `vrml_image` `vrml_image` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_host` CHANGE `statusmap_image` `statusmap_image` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_host` CHANGE `2d_coords` `2d_coords` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_host` CHANGE `3d_coords` `3d_coords` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hostdependency` CHANGE `execution_failure_criteria` `execution_failure_criteria` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hostdependency` CHANGE `notification_failure_criteria` `notification_failure_criteria` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hostdependency` CHANGE `import_hash` `import_hash` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hostescalation` CHANGE `escalation_options` `escalation_options` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hostescalation` CHANGE `import_hash` `import_hash` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hostgroup` CHANGE `notes` `notes` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hostgroup` CHANGE `notes_url` `notes_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hostgroup` CHANGE `action_url` `action_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `check_command` `check_command` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `initial_state` `initial_state` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `flap_detection_options` `flap_detection_options` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `notification_options` `notification_options` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `stalking_options` `stalking_options` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `notes` `notes` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `notes_url` `notes_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `action_url` `action_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `icon_image_alt` `icon_image_alt` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `vrml_image` `vrml_image` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `statusmap_image` `statusmap_image` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `2d_coords` `2d_coords` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `3d_coords` `3d_coords` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_lnkServicedependencyToService_DS` CHANGE `strSlave` `strSlave` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_lnkServicedependencyToService_S` CHANGE `strSlave` `strSlave` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_lnkServiceescalationToService` CHANGE `strSlave` `strSlave` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_service` CHANGE `stalking_options` `stalking_options` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_service` CHANGE `name` `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_service` CHANGE `import_hash` `import_hash` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_servicedependency` CHANGE `execution_failure_criteria` `execution_failure_criteria` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_servicedependency` CHANGE `notification_failure_criteria` `notification_failure_criteria` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_servicedependency` CHANGE `import_hash` `import_hash` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_serviceescalation` CHANGE `escalation_options` `escalation_options` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_serviceescalation` CHANGE `import_hash` `import_hash` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_serviceextinfo` CHANGE `import_hash` `import_hash` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_servicetemplate` CHANGE `stalking_options` `stalking_options` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_servicetemplate` CHANGE `import_hash` `import_hash` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_timeperiod` CHANGE `timeperiod_name` `timeperiod_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_timeperiod` CHANGE `alias` `alias` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE `tbl_lnkServicegroupToService` DROP PRIMARY KEY , ADD PRIMARY KEY ( `idMaster` , `idSlaveH` , `idSlaveHG` , `idSlaveS` );

--
--  Modify icon_image field for PNP4Nagios
--
ALTER TABLE `tbl_servicetemplate` CHANGE `icon_image` `icon_image` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_service` CHANGE `icon_image` `icon_image` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hosttemplate` CHANGE `icon_image` `icon_image` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL; 
ALTER TABLE `tbl_host` CHANGE `icon_image` `icon_image` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `tbl_hostextinfo` CHANGE `icon_image` `icon_image` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL; 
ALTER TABLE `tbl_serviceextinfo` CHANGE `icon_image` `icon_image` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;