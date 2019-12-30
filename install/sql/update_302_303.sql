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
--  Component : Update from NagiosQL 3.0.2 to NagiosQL 3.0.3
--  Website   : https://sourceforge.net/projects/nagiosql/
--  Version   : 3.4.1
--  GIT Repo  : https://gitlab.com/wizonet/NagiosQL
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
ALTER TABLE `tbl_serviceextinfo` CHANGE `host_name` `host_name` INT( 11 ) NULL DEFAULT '0';
