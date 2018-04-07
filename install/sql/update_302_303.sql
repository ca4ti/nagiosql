--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  (c) 2008, 2009 by Martin Willisegger
--
--  Project   : NagiosQL
--  Component : Update from NagiosQL 3.0.2 to NagiosQL 3.0.3
--  Website   : www.nagiosql.org
--  Date      : $LastChangedDate: 2010-11-01 17:54:21 +0100 (Mo, 01. Nov 2010) $
--  Author    : $LastChangedBy: martin $
--  Version   : 3.0.3
--  Revision  : $LastChangedRevision: 839 $
--  SVN-ID    : $Id: update_302_303.sql 839 2010-11-01 16:54:21Z martin $
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
