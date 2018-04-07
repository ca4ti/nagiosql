--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  Project   : NagiosQL
--  Component : Update from NagiosQL 3.1.0b2 to NagiosQL 3.1.0b3
--  Website   : www.nagiosql.org
--  Date      : $LastChangedDate: 2011-01-08 16:55:29 +0100 (Sa, 08 Jan 2011) $
--  Author    : $LastChangedBy: rouven $
--  Version   : 3.1.1
--  Revision  : $LastChangedRevision: 973 $
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
-- Modify existing tbl_domain
--
ALTER TABLE `tbl_domain` ADD `utf8_decode` tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER `enable_common`;
--
--  Modify existing tbl_settings
--
UPDATE `tbl_settings` SET `value` = '3.1.0b3' WHERE `tbl_settings`.`name` = 'version' LIMIT 1;
--
--  Modify existing tbl_submenu
--
UPDATE `tbl_submenu` SET `item` = 'Delete backup files' WHERE `link` = 'admin/delbackup.php';
