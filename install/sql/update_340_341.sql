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
--  Modify existing tbl_settings
--
UPDATE `tbl_settings` SET `value` = '3.4.1' WHERE `tbl_settings`.`name` = 'version' LIMIT 1;
--
--  Modify table tbl_relationinformation
--
UPDATE `tbl_relationinformation` SET `target1`='name' WHERE `master`='tbl_timeperiod' AND `fieldName`='use_template';
UPDATE `tbl_relationinformation` SET `targetKey`='name' WHERE `master`='tbl_timeperiod' AND `tableName1`='tbl_lnkTimeperiodToTimeperiodUse' AND `fieldName`='idMaster';
UPDATE `tbl_relationinformation` SET `targetKey`='name' WHERE `master`='tbl_timeperiod' AND `tableName1`='tbl_lnkTimeperiodToTimeperiodUse' AND `fieldName`='idSlave';