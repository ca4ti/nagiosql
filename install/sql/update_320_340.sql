--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  Project   : NagiosQL
--  Component : Update from NagiosQL 3.2.0 to NagiosQL 3.4.0
--  Website   : https://sourceforge.net/projects/nagiosql/
--  Version   : 3.4.0
--  GIT Repo  : https://gitlab.com/wizonet/NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  Modify existing tbl_settings
--
UPDATE `tbl_settings` SET `value` = '3.4.0' WHERE `tbl_settings`.`name` = 'version' LIMIT 1;
--
--  Modify existing tbl_configtarget
--
ALTER TABLE `tbl_configtarget` ADD `cgifile` VARCHAR(255) NOT NULL AFTER `conffile`;
ALTER TABLE `tbl_configtarget` ADD `ftp_secure` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `ssh_key_path`;
