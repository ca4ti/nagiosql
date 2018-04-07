--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  NagiosQL
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
--  Project   : NagiosQL
--  Component : Update from NagiosQL 3.1.0b3 to NagiosQL 3.1.0rc1
--  Website   : www.nagiosql.org
--  Date      : $LastChangedDate: 2011-03-13 14:00:26 +0100 (So, 13. Mär 2011) $
--  Author    : $LastChangedBy: rouven $
--  Version   : 3.1.1
--  Revision  : $LastChangedRevision: 1058 $
--
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --
--
-- Modify existing tbl_domain
--
UPDATE `tbl_domain` SET `conffile` = '/mypath/nagios.cfg' WHERE `conffile` IS NULL;
ALTER TABLE `tbl_domain` ALTER COLUMN `conffile` varchar(255) NOT NULL;
--
--  Modify existing tbl_settings
--
UPDATE `tbl_settings` SET `value` = '3.1.0rc1' WHERE `tbl_settings`.`name` = 'version' LIMIT 1;
--
-- Modify existing tbl_info
--
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('domain', 'picturedir', 'all', 'default', '<p><strong>Relative</strong> path to your nagios icon images.<br /><br />Example:<br />/my/own/images/</p>\r\n<p>This path is based on your nagios standard image path. Images are assumed to be in the <strong>logos/</strong> subdirectory in your HTML images directory (i.e. /usr/local/nagios/share/images/logos).</p>\r\n<p>So in the example above, the images are located in:</p>\r\n<p>/usr/local/nagios/share/images/logos<span style="color: #ff0000;">/my/own/images/</span></p>');
INSERT INTO `tbl_info` (`key1`, `key2`, `version`, `language`, `infotext`) VALUES('common', 'accessgroup', 'all', 'default', '<p><strong>Access group</strong></p>\r\n<p>Select an access group name to restrict this object to the group members.</p>');
UPDATE `tbl_info` SET `infotext` =  '<p>The nagios version which is running in this domain.</p>\r\n<p>Be sure you select the correct version here - otherwise not all configuration options are available or not supported options are shown.</p>\r\n<p>You can change this with a running configuration - NagiosQL will then upgrade or downgrade your configuration. Don''t forget to write your complete configuration after a version change!</p>' WHERE `tbl_info`.`key1` = 'domain' AND `tbl_info`.`key2` = 'version' AND `tbl_info`.`version` = 'all' AND `tbl_info`.`language` = 'default';
--
-- Modify existing tbl_submenu
--
UPDATE `tbl_submenu` SET `item` = 'Extended Host' WHERE `link` = 'admin/hostextinfo.php';
UPDATE `tbl_submenu` SET `item` = 'Extended Service' WHERE `link` = 'admin/serviceextinfo.php';
UPDATE `tbl_submenu` SET `item` = 'Service dependency' WHERE `link` = 'admin/servicedependencies.php';
UPDATE `tbl_submenu` SET `item` = 'Service escalation' WHERE `link` = 'admin/serviceescalations.php';
