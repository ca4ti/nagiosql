--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  Project   : NagiosQL
--  Component : Update from NagiosQL 3.0.2 to NagiosQL 3.0.3
--  Website   : www.nagiosql.org
--  Date      : $LastChangedDate: 2010-10-25 15:45:55 +0200 (Mo, 25 Okt 2010) $
--  Author    : $LastChangedBy: rouven $
--  Version   : 3.0.4
--  Revision  : $LastChangedRevision: 827 $
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--

--
--  Modify existing tbl_settings
--
UPDATE `tbl_settings` SET `value` = '3.0.3' WHERE `tbl_settings`.`name` = 'version' LIMIT 1;
ALTER TABLE `tbl_settings` CHANGE `value` `value` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
--
--  Modify existing tbl_lnkServicegroupToService
--
ALTER TABLE `tbl_lnkServicegroupToService` DROP PRIMARY KEY, ADD PRIMARY KEY ( `idMaster` , `idSlaveH` , `idSlaveHG`, `idSlaveS` );
--
--  Modify existing tbl_serviceextinfo
--
ALTER TABLE `tbl_serviceextinfo` CHANGE `host_name` `host_name` INT( 11 ) DEFAULT NULL;
--
--  Modify existing tbl_hostextinfo
--
ALTER TABLE `tbl_hostextinfo` CHANGE `host_name` `host_name` INT( 11 ) DEFAULT NULL;
--
-- Modify existing tbl_info
--
UPDATE `tbl_info` SET `infotext` = '<p><strong>Host - Templates</strong></p>\r\n<p>You can add one or more host templates to a host configuration. Nagios will add the definitions from each template to a host configuration.</p>\r\n<p>If you add more than one template - the templates from the bottom to the top will be used to overwrite configuration items which are defined inside templates before.</p>\r\n<p>The host configuration itselves will overwrite all values which are defined in templates before and pass all values which are not defined.</p>' WHERE `id` = 25;
UPDATE `tbl_info` SET `infotext` = '<p><strong>Service - Templates</strong></p>\r\n<p>You can add one or more service templates to a service configuration. Nagios will add the definitions from each template to a service configuration.</p>\r\n<p>If you add more than one template - the templates from the bottom to the top will be used to overwrite configuration items which are defined inside templates before.</p>\r\n<p>The host configuration itselves will overwrite all values which are defined in templates before and pass all values which are not defined.</p>' WHERE `id` = 76;
UPDATE `tbl_info` SET `infotext` = '<p><strong>Contact - Templates</strong></p>\r\n<p>You can add one or more contact templates to a contact configuration. Nagios will add the definitions from each template to a contact configuration.</p>\r\n<p>If you add more than one template - the templates from the bottom to the top will be used to overwrite configuration items which are defined inside templates before.</p>\r\n<p>The host configuration itselves will overwrite all values which are defined in templates before and pass all values which are not defined.</p>' WHERE `id` = 146;
