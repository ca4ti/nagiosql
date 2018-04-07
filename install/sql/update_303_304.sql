--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  Project   : NagiosQL
--  Component : Update from NagiosQL 3.0.3 to NagiosQL 3.0.4
--  Website   : www.nagiosql.org
--  Date      : $LastChangedDate: 2011-03-13 14:00:26 +0100 (So, 13. Mär 2011) $
--  Author    : $LastChangedBy: rouven $
--  Version   : 3.1.1
--  Revision  : $LastChangedRevision: 1058 $
--  SVN-ID    : $Id: update_303_304.sql 1058 2011-03-13 13:00:26Z rouven $
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  Modify existing tbl_settings
--
UPDATE `tbl_settings` SET `value` = '3.0.4' WHERE `tbl_settings`.`name` = 'version' LIMIT 1;
