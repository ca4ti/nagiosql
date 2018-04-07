--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  Project   : NagiosQL
--  Component : Update from NagiosQL 3.0.1 to NagiosQL 3.0.2
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
UPDATE `tbl_settings` SET `value` = '3.0.2' WHERE `tbl_settings`.`name` = 'version' LIMIT 1;
