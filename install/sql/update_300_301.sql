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
--  Date      : $LastChangedDate: 2012-02-23 07:49:34 +0100 (Thu, 23 Feb 2012) $
--  Author    : $LastChangedBy: martin $
--  Version   : 3.0.2
--  Revision  : $LastChangedRevision: 1237 $
--  SVN-ID    : $Id: update_300_301.sql 1237 2012-02-23 06:49:34Z martin $
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
