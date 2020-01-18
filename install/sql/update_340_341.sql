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
--  Component : Update from NagiosQL 3.4.0 to NagiosQL 3.4.1
--  Website   : https://sourceforge.net/projects/nagiosql/
--  Version   : 3.4.1
--  GIT Repo  : https://gitlab.com/wizonet/NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  Modify table tbl_relationinformation
--
UPDATE `tbl_relationinformation` SET `target1`='name' WHERE `master`='tbl_timeperiod' AND `fieldName`='use_template';
UPDATE `tbl_relationinformation` SET `targetKey`='name' WHERE `master`='tbl_timeperiod' AND `tableName1`='tbl_lnkTimeperiodToTimeperiodUse' AND `fieldName`='idMaster';
UPDATE `tbl_relationinformation` SET `targetKey`='name' WHERE `master`='tbl_timeperiod' AND `tableName1`='tbl_lnkTimeperiodToTimeperiodUse' AND `fieldName`='idSlave';
--
--  Modify table tbl_command
--
ALTER TABLE `tbl_command` ADD `arg1_info` text NULL DEFAULT NULL AFTER `command_type`;
ALTER TABLE `tbl_command` ADD `arg2_info` text NULL DEFAULT NULL AFTER `arg1_info`;
ALTER TABLE `tbl_command` ADD `arg3_info` text NULL DEFAULT NULL AFTER `arg2_info`;
ALTER TABLE `tbl_command` ADD `arg4_info` text NULL DEFAULT NULL AFTER `arg3_info`;
ALTER TABLE `tbl_command` ADD `arg5_info` text NULL DEFAULT NULL AFTER `arg4_info`;
ALTER TABLE `tbl_command` ADD `arg6_info` text NULL DEFAULT NULL AFTER `arg5_info`;
ALTER TABLE `tbl_command` ADD `arg7_info` text NULL DEFAULT NULL AFTER `arg6_info`;
ALTER TABLE `tbl_command` ADD `arg8_info` text NULL DEFAULT NULL AFTER `arg7_info`;
--
--  Modify table tbl_configtarget
--
ALTER TABLE `tbl_configtarget` ADD `port` INT UNSIGNED NOT NULL DEFAULT '22' AFTER `server`;
--
--  Modify existing tbl_settings
--
INSERT INTO `tbl_settings` (`id`, `category`, `name`, `value`) VALUES (NULL,'performance', 'parents', '1');
UPDATE `tbl_settings` SET `value` = '3.4.1' WHERE `tbl_settings`.`name` = 'version' LIMIT 1;
