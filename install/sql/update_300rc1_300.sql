--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  Project   : NagiosQL
--  Component : Update from NagiosQL 3.0.0 rc1 to NagiosQL 3.0.0 (final)
--  Website   : www.nagiosql.org
--  Date      : $LastChangedDate: 2010-10-25 15:45:55 +0200 (Mo, 25 Okt 2010) $
--  Author    : $LastChangedBy: rouven $
--  Version   : 3.0.4
--  Revision  : $LastChangedRevision: 827 $
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--

--
-- Modify existing tbl_settings
--
UPDATE `tbl_settings` SET `value` = '3.0.0' WHERE `tbl_settings`.`name` = 'version' LIMIT 1;
--
-- Modify existing tbl_info
--
UPDATE `tbl_info` SET `infotext` = 'NagiosQL writes services grouped into files identified by the service configuration names. It is useful to store this files inside an own subdirectory below your Nagios configuration path.<br><br>Examples:<br>/etc/nagios/services <br>/usr/local/nagios/etc/services<br><br>Be sure, that your configuration settings are matching with your nagios.cfg!<br> (cfg_dir=<font color="red">/etc/nagios/services</font>)' WHERE `key1` = 'domain' AND `key2` = 'servicedir' LIMIT 1;
