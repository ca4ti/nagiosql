-- ///////////////////////////////////////////////////////////////////////////////
-- //
-- // NagiosQL 2005
-- //
-- ///////////////////////////////////////////////////////////////////////////////
-- //
-- // (c) 2005 by Martin Willisegger / nagios.ql2005@wizonet.ch
-- //
-- // Projekt:	NagiosQL Applikation
-- // Author :	Martin Willisegger
-- // Datum:	01.04.2005
-- // Zweck:	MySQL Datenbankstruktur V4.0
-- // Version:	1.02
-- //
-- ///////////////////////////////////////////////////////////////////////////////

-- 
-- Datenbank erstellen
-- 
CREATE DATABASE db_nagiosql;
USE db_nagiosql;

-- 
-- Tabelle tbl_checkcommand
-- 

CREATE TABLE tbl_checkcommand (
  id int(10) unsigned NOT NULL auto_increment,
  command_name varchar(40) NOT NULL default '',
  command_line text NOT NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY command_name (command_name)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_contact
-- 

CREATE TABLE tbl_contact (
  id int(10) unsigned NOT NULL auto_increment,
  contact_name varchar(40) NOT NULL default '',
  alias varchar(120) NOT NULL default '',
  contactgroups tinytext,
  host_notification_period varchar(40) NOT NULL default '',
  service_notification_period varchar(40) NOT NULL default '',
  host_notification_options varchar(10) NOT NULL default '',
  service_notification_options varchar(10) NOT NULL default '',
  host_notification_commands varchar(40) default NULL,
  service_notification_commands varchar(40) default NULL,
  email varchar(60) default NULL,
  pager varchar(40) default NULL,
  address1 varchar(60) default NULL,
  address2 varchar(60) default NULL,
  address3 varchar(60) default NULL,
  address4 varchar(60) default NULL,
  address5 varchar(60) default NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY contact_name (contact_name)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_contactgroup
-- 

CREATE TABLE tbl_contactgroup (
  id int(10) unsigned NOT NULL auto_increment,
  contactgroup_name varchar(40) NOT NULL default '',
  alias varchar(120) NOT NULL default '',
  members tinytext NOT NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY contactgroup_name (contactgroup_name)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_host
-- 

CREATE TABLE tbl_host (
  id int(10) unsigned NOT NULL auto_increment,
  host_name varchar(255) NOT NULL default '',
  alias varchar(120) NOT NULL default '',
  address varchar(255) NOT NULL default '',
  parents text,
  hostgroups text,
  check_command tinytext,
  max_check_attempts tinyint(3) unsigned NOT NULL default '0',
  check_interval mediumint(8) unsigned default NULL,
  active_checks_enabled enum('0','1') NOT NULL default '1',
  passive_checks_enabled enum('0','1') NOT NULL default '1',
  check_period varchar(40) NOT NULL default '',
  obsess_over_host enum('0','1') NOT NULL default '1',
  check_freshness enum('0','1') NOT NULL default '0',
  freshness_threshold mediumint(8) unsigned default NULL,
  event_handler varchar(40) default NULL,
  event_handler_enabled enum('0','1') NOT NULL default '1',
  low_flap_threshold tinyint(3) unsigned default NULL,
  high_flap_threshold tinyint(3) unsigned default NULL,
  flap_detection_enabled enum('0','1') NOT NULL default '1',
  process_perf_data enum('0','1') NOT NULL default '0',
  retain_status_information enum('0','1') NOT NULL default '1',
  retain_nonstatus_information enum('0','1') NOT NULL default '1',
  contact_groups tinytext NOT NULL,
  notification_interval mediumint(8) unsigned NOT NULL default '1',
  notification_period varchar(40) NOT NULL default '',
  notification_options varchar(10) NOT NULL default '',
  notifications_enabled enum('0','1') NOT NULL default '1',
  stalking_options varchar(10) default NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id),
  UNIQUE KEY host_name (host_name)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_hostdependency
-- 

CREATE TABLE tbl_hostdependency (
  id int(10) unsigned NOT NULL auto_increment,
  config_name varchar(40) NOT NULL default '',
  dependent_host_name text NOT NULL,
  dependent_hostgroup_name text NOT NULL,
  host_name text NOT NULL,
  hostgroup_name text NOT NULL,
  inherits_parent enum('0','1') NOT NULL default '0',
  execution_failure_criteria varchar(10) default NULL,
  notification_failure_criteria varchar(10) default NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id),
  UNIQUE KEY config_name (config_name)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_hostescalation
-- 

CREATE TABLE tbl_hostescalation (
  id int(10) unsigned NOT NULL auto_increment,
  config_name varchar(40) NOT NULL default '',
  host_name text NOT NULL,
  hostgroup_name text NOT NULL,
  contact_groups text NOT NULL,
  first_notification tinyint(3) unsigned NOT NULL default '0',
  last_notification tinyint(3) unsigned NOT NULL default '0',
  notification_interval mediumint(8) unsigned NOT NULL default '0',
  escalation_period varchar(40) default NULL,
  escalation_options varchar(10) default NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id),
  UNIQUE KEY config_name (config_name)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_hostextinfo
-- 

CREATE TABLE tbl_hostextinfo (
  id int(10) unsigned NOT NULL auto_increment,
  host_name varchar(255) NOT NULL default '',
  notes tinytext,
  notes_url tinytext,
  action_url tinytext,
  statistik_url tinytext,
  icon_image varchar(40) default NULL,
  icon_image_alt varchar(40) default NULL,
  vrml_image varchar(40) default NULL,
  statusmap_image varchar(40) default NULL,
  2d_coords varchar(30) default NULL,
  3d_coords varchar(40) default NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY host_name (host_name)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_hostgroup
-- 

CREATE TABLE tbl_hostgroup (
  id int(10) unsigned NOT NULL auto_increment,
  hostgroup_name varchar(120) NOT NULL default '',
  alias varchar(120) NOT NULL default '',
  members text NOT NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY hostgroup_name (hostgroup_name)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_logbook
-- 

CREATE TABLE tbl_logbook (
  id bigint(20) unsigned NOT NULL auto_increment,
  time timestamp NOT NULL,
  user varchar(20) NOT NULL default '',
  entry tinytext NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_mainmenu
-- 

CREATE TABLE tbl_mainmenu (
  id tinyint(3) unsigned NOT NULL auto_increment,
  order_id tinyint(3) unsigned NOT NULL default '0',
  menu_id tinyint(3) unsigned NOT NULL default '0',
  item varchar(20) NOT NULL default '',
  link varchar(50) NOT NULL default '',
  rights varchar(10) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='Hauptmenutabelle';

-- 
-- Daten für tbl_mainmenu
-- 

INSERT INTO tbl_mainmenu VALUES (1, 1, 2, 'item_adm1', 'admin.php', 'admin1');
INSERT INTO tbl_mainmenu VALUES (2, 2, 2, 'item_adm2', 'admin/monitoring.php', 'admin1');
INSERT INTO tbl_mainmenu VALUES (3, 3, 2, 'item_adm3', 'admin/contact.php', 'admin1');
INSERT INTO tbl_mainmenu VALUES (4, 4, 2, 'item_adm4', 'admin/command.php', 'admin1');
INSERT INTO tbl_mainmenu VALUES (5, 5, 2, 'item_adm5', 'admin/special.php', 'admin1');
INSERT INTO tbl_mainmenu VALUES (6, 6, 2, 'item_adm6', 'admin/tools.php', 'admin_all');
INSERT INTO tbl_mainmenu VALUES (7, 7, 2, 'item_adm7', 'admin/administration.php', 'admin_all');

-- 
-- Tabelle tbl_misccommand
-- 

CREATE TABLE tbl_misccommand (
  id int(10) unsigned NOT NULL auto_increment,
  command_name varchar(40) NOT NULL default '',
  command_line text NOT NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY command_name (command_name)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_service
-- 

CREATE TABLE tbl_service (
  id int(10) unsigned NOT NULL auto_increment,
  config_name varchar(40) NOT NULL default '',
  host_name text,
  hostgroup_name text,
  service_description varchar(120) NOT NULL default '',
  servicegroups text,
  is_volatile enum('0','1') default '0',
  check_command tinytext NOT NULL,
  max_check_attempts tinyint(3) unsigned NOT NULL default '0',
  normal_check_interval mediumint(8) unsigned NOT NULL default '0',
  retry_check_interval mediumint(8) unsigned NOT NULL default '0',
  active_checks_enabled enum('0','1') NOT NULL default '1',
  passive_checks_enabled enum('0','1') NOT NULL default '1',
  check_period varchar(40) NOT NULL default '',
  parallelize_check enum('0','1') NOT NULL default '1',
  obsess_over_service enum('0','1') NOT NULL default '1',
  check_freshness enum('0','1') NOT NULL default '0',
  freshness_threshold mediumint(8) unsigned default NULL,
  event_handler varchar(40) default NULL,
  event_handler_enabled enum('0','1') NOT NULL default '1',
  low_flap_threshold tinyint(3) unsigned default NULL,
  high_flap_threshold tinyint(3) unsigned default NULL,
  flap_detection_enabled enum('0','1') NOT NULL default '1',
  process_perf_data enum('0','1') NOT NULL default '1',
  retain_status_information enum('0','1') NOT NULL default '1',
  retain_nonstatus_information enum('0','1') NOT NULL default '1',
  contact_groups tinytext NOT NULL,
  notification_interval mediumint(8) unsigned NOT NULL default '0',
  notification_period varchar(40) NOT NULL default '',
  notification_options varchar(10) NOT NULL default '',
  notifications_enabled enum('0','1') NOT NULL default '1',
  stalking_options varchar(10) default NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_servicedependency
-- 

CREATE TABLE tbl_servicedependency (
  id int(10) unsigned NOT NULL auto_increment,
  config_name varchar(40) NOT NULL default '',
  dependent_host_name text,
  dependent_service_description text,
  host_name text,
  service_description text,
  dependent_hostgroup_name text,
  dependent_servicegroup_name text,
  hostgroup_name text,
  servicegroup_name text,
  inherits_parent enum('0','1') NOT NULL default '0',
  execution_failure_criteria varchar(12) default NULL,
  notification_failure_criteria varchar(12) default NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY config_name (config_name)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_serviceescalation
-- 

CREATE TABLE tbl_serviceescalation (
  id int(10) unsigned NOT NULL auto_increment,
  config_name varchar(40) NOT NULL default '',
  host_name text,
  service_description text,
  hostgroup_name text,
  servicegroup_name text,
  contact_groups tinytext NOT NULL,
  first_notification tinyint(3) unsigned NOT NULL default '0',
  last_notification tinyint(3) unsigned NOT NULL default '0',
  notification_interval mediumint(8) unsigned NOT NULL default '0',
  escalation_period varchar(40) default NULL,
  escalation_options varchar(10) default NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_serviceextinfo
-- 

CREATE TABLE tbl_serviceextinfo (
  id int(10) unsigned NOT NULL auto_increment,
  host_name varchar(255) NOT NULL default '',
  service_description varchar(40) NOT NULL default '',
  notes tinytext,
  notes_url tinytext,
  action_url tinytext,
  statistic_url tinytext,
  icon_image varchar(40) default NULL,
  icon_image_alt varchar(40) default NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_servicegroup
-- 

CREATE TABLE tbl_servicegroup (
  id int(10) unsigned NOT NULL auto_increment,
  servicegroup_name varchar(120) NOT NULL default '',
  alias varchar(120) NOT NULL default '',
  members text NOT NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY servicegroup_name (servicegroup_name)
) TYPE=MyISAM;

-- 
-- Tabelle tbl_submenu
-- 

CREATE TABLE tbl_submenu (
  id tinyint(3) unsigned NOT NULL auto_increment,
  id_main tinyint(3) unsigned NOT NULL default '0',
  order_id tinyint(3) unsigned NOT NULL default '0',
  item varchar(20) NOT NULL default '',
  link varchar(50) NOT NULL default '',
  rights varchar(10) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM PACK_KEYS=0 COMMENT='Untermenutabelle';

-- 
-- Daten für tbl_submenu
-- 

INSERT INTO tbl_submenu VALUES (1, 2, 1, 'item_admsub1', 'admin/hosts.php', 'admin1');
INSERT INTO tbl_submenu VALUES (2, 3, 3, 'item_admsub2', 'admin/timeperiod.php', 'admin1');
INSERT INTO tbl_submenu VALUES (3, 4, 2, 'item_admsub3', 'admin/misccommands.php', 'admin1');
INSERT INTO tbl_submenu VALUES (4, 4, 1, 'item_admsub4', 'admin/checkcommands.php', 'admin1');
INSERT INTO tbl_submenu VALUES (5, 3, 1, 'item_admsub5', 'admin/contacts.php', 'admin1');
INSERT INTO tbl_submenu VALUES (6, 3, 2, 'item_admsub6', 'admin/contactgroups.php', 'admin1');
INSERT INTO tbl_submenu VALUES (7, 2, 2, 'item_admsub7', 'admin/services.php', 'admin1');
INSERT INTO tbl_submenu VALUES (8, 2, 3, 'item_admsub8', 'admin/hostgroups.php', 'admin1');
INSERT INTO tbl_submenu VALUES (9, 2, 4, 'item_admsub9', 'admin/servicegroups.php', 'admin1');
INSERT INTO tbl_submenu VALUES (10, 5, 4, 'item_admsub10', 'admin/servicedependencies.php', 'admin1');
INSERT INTO tbl_submenu VALUES (11, 5, 5, 'item_admsub11', 'admin/serviceescalations.php', 'admin1');
INSERT INTO tbl_submenu VALUES (12, 5, 1, 'item_admsub12', 'admin/hostdependencies.php', 'admin1');
INSERT INTO tbl_submenu VALUES (13, 5, 2, 'item_admsub13', 'admin/hostescalations.php', 'admin1');
INSERT INTO tbl_submenu VALUES (14, 5, 3, 'item_admsub14', 'admin/hostextinfo.php', 'admin1');
INSERT INTO tbl_submenu VALUES (15, 5, 6, 'item_admsub15', 'admin/serviceextinfo.php', 'admin1');
INSERT INTO tbl_submenu VALUES (16, 6, 1, 'item_admsub16', 'admin/import.php', 'admin2');
INSERT INTO tbl_submenu VALUES (17, 6, 2, 'item_admsub17', 'admin/delbackup.php', 'admin2');
INSERT INTO tbl_submenu VALUES (18, 7, 2, 'item_admsub18', 'admin/users.php', 'admin3');
INSERT INTO tbl_submenu VALUES (19, 6, 5, 'item_admsub19', 'admin/verify.php', 'admin2');
INSERT INTO tbl_submenu VALUES (20, 7, 1, 'item_admsub20', 'admin/password.php', 'admin_all');
INSERT INTO tbl_submenu VALUES (21, 7, 3, 'item_admsub21', 'admin/logbook.php', 'admin3');
INSERT INTO tbl_submenu VALUES (22, 6, 3, 'item_admsub22', 'admin/nagioscfg.php', 'admin2');
INSERT INTO tbl_submenu VALUES (23, 6, 4, 'item_admsub23', 'admin/cgicfg.php', 'admin2');

-- 
-- Tabelle tbl_timeperiod
-- 

CREATE TABLE tbl_timeperiod (
  id int(10) unsigned NOT NULL auto_increment,
  timeperiod_name varchar(40) NOT NULL default '',
  alias varchar(120) NOT NULL default '',
  sunday varchar(100) default NULL,
  monday varchar(100) default NULL,
  tuesday varchar(100) default NULL,
  wednesday varchar(100) default NULL,
  thursday varchar(100) default NULL,
  friday varchar(100) default NULL,
  saturday varchar(100) default NULL,
  active enum('0','1') NOT NULL default '1',
  last_modified timestamp NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY 	timeperiod_name (timeperiod_name)
) TYPE=MyISAM PACK_KEYS=0;

-- 
-- Tabelle tbl_user
-- 

CREATE TABLE tbl_user (
  id int(10) unsigned NOT NULL auto_increment,
  username varchar(20) NOT NULL default '',
  alias varchar(40) NOT NULL default '',
  password varchar(40) NOT NULL default '',
  admin1 enum('0','1') NOT NULL default '0',
  admin2 enum('0','1') NOT NULL default '0',
  admin3 enum('0','1') NOT NULL default '0',
  active enum('0','1') NOT NULL default '0',
  last_login timestamp NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle tbl_user
-- 

INSERT INTO tbl_user VALUES (1, 'admin', 'Administrator', MD5('admin'), '1', '1', '1', '1', NOW());

--
-- Datenbankrechte
--

GRANT USAGE ON *.* TO 'nagiosqlusr'@'localhost' IDENTIFIED BY 'nagiosqlpwd';
GRANT SELECT,INSERT,UPDATE,DELETE ON `db_nagiosql`.* TO'nagiosqlusr'@'localhost';