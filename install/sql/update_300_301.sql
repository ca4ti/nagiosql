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
--  Component : Update from NagiosQL 3.0.0 to NagiosQL 3.0.1
--  Website   : www.nagiosql.org
--  Date      : $LastChangedDate: 2009-02-02 11:24:55 +0100 (Mo, 02. Feb 2009) $
--  Author    : $LastChangedBy: rouven $
--  Version   : 3.0.2
--  Revision  : $LastChangedRevision: 654 $
--  SVN-ID    : $Id: update_300_301.sql 654 2009-02-02 10:24:55Z rouven $
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--

--
--  Modify existing tbl_settings
--
UPDATE `tbl_settings` SET `value` = '3.0.1' WHERE `tbl_settings`.`name` = 'version' LIMIT 1;
--
--  Modify existing tbl_logbook
--
ALTER TABLE `tbl_logbook` ADD `domain` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `ipadress`;
ALTER TABLE `tbl_logbook` CHANGE `entry` `entry` TINYTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
