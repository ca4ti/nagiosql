*******************************************************************************
*                                                                             *
*                  NagiosQL 2005 - Install documentation                      *
*                                                                             *
*******************************************************************************

Disclaimer
==========

This software is subject to the "New BSD License"

Copyright (c) 2005, Martin Willisegger
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, 
are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, 
	  this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, 
	  this list of conditions and the following disclaimer in the documentation 
	  and/or other materials provided with the distribution.
    * Neither the name of Martin Willisegger nor the names of its contributors 
	  may be used to endorse or promote products derived from this software 
	  without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, 
OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE.

About Nagios
============
Nagios is a registered trademark of Ethan Galstad. - http://www.nagios.org


Version 1.00-RC1
================

1. Requirements
2. Installation
3. Settings
4. Rights
5. Using

1. Requirements
   ============   

   - Web server (Apache 1.3.x/2.0.x)
   - PHP Version 4.1 or higher / 5.0 or higher
   - MySQL Version 4.0 or higher / 4.1 or higher
   - Pear modul HTML_Template_IT Version 1.1 (http://pear.php.net)
   - Nagios Version 2.x (1.x is not supported)

   Pear modules can be normally installed with the following instruction automatically:
   # pear install HTML_Template_IT 

2. Installation
   ============

a. Unpack nagiosql-1.0RC1.tar.gz into your webserver's root, a subdirectory "nagiosQL"
   will be created:
   
   # cd /srv/www/htdocs/
   # cp nagiosql-1.0RC1.tar.gz /srv/www/htdocs/nagiosQL/
   # gunzip -c nagiosql-1.0RC1.tar.gz | tar xf -
   
b. Create the MySQL database - version nagiosql_40.sql is for MySQL 4.0, nagiosql_41.sql 
   for MySQL version 4.1. If you have troubles with the collation in nagiosql_41.sql and
   MySQL 4.1, you can try nagiosql_41b.sql which has no collation defined.
   The Script creates automatically a new data base with the name db_nagiosql, all tables 
   as well as the user "nagiosqlusr" with the password "nagiosqlpwd".

   # cd /srv/www/htdocs/nagiosQL/config
   # mysql -uroot -p < nagiosql_40.sql
   # Password: xxxx
   
   (Example for data base administrator "root" with password "xxxx"
   
   It is very important to set a new password for the user:
   
   # mysql -uroot -p
   # Password: xxxx
   mysql> use mysql;
   mysql> UPDATE user SET Password=PASSWORD("mypassword") WHERE User="nagiosqlusr";
   mysql> FLUSH PRIVILEGES;
   mysql> exit
   #
   
c. Webserver settings
   The webserver directory for nagiosQL must be set up that .htaccess files are 
   read and interpreted.
   
   <Directory  /srv/www/htdocs/nagiosQL>
     AllowOverride All
   </Directory>
   
   Alternatively the settings of the file congig/.htaccess can also be written into the 
   server configuration. It is important, that the *.ini files cannot be shown with a 
   browser.
   
d. Configuration directories (Structure)
   The configuration directories must have the following structure:
   
   /etc/nagios/ 			-> Common configuration files
   	"     /hosts			-> Host configuration files
	"     /services			-> Service configuration files
	"     /backup/			-> Backups of the common configuration files
	"	 "   /hosts		-> Backups of the host configuration files
	"	 "   /services		-> Backups of the service configuration files
		
   The directory names are changeable but the structure must be kept!

3. Settings
   ========
   
   The application settings are made in the file config/settings.ini. The individual 
   values are described within this file.
   
a. The Nagios main configuration file must be adjusted as follows, whereby the main directory 
   does not have to be called /etc/nagios. The configuration files must be designated however 
   accordingly. Following settings fits on them supplied by settings.ini. 
   
   cfg_file=/etc/nagios/contactgroups.cfg
   cfg_file=/etc/nagios/contacts.cfg
   cfg_file=/etc/nagios/timeperiods.cfg 
   
   cfg_file=/etc/nagios/hostgroups.cfg
   cfg_file=/etc/nagios/servicegroups.cfg

   #cfg_file=/etc/nagios/servicedependencies.cfg
   #cfg_file=/etc/nagios/serviceescalations.cfg
   #cfg_file=/etc/nagios/hostdependencies.cfg
   #cfg_file=/etc/nagios/hostescalations.cfg
   #cfg_file=/etc/nagios/hostextinfo.cfg
   #cfg_file=/etc/nagios/serviceextinfo.cfg

   cfg_dir=/etc/nagios/hosts
   cfg_dir=/etc/nagios/services
   
   The lines started with # are optional.
   
4. Rights
   ======
   
   The following file rights are necessary - for example the web server runs by the 
   user "wwwrun" and the group "www" as well as Nagios runs by the user "nagios" and 
   the group "daemon". As configuration directory is used /etc/nagios:
   
   # chmod 6755 /etc/nagios
   # chown wwwrun.daemon /etc/nagios
   # chmod 6755 /etc/nagios/hosts
   # chown wwwrun.daemon /etc/nagios/hosts
   # chmod 6755 /etc/nagios/services
   # chown wwwrun.daemon /etc/nagios/services   
   
   # chmod 6755 /etc/nagios/backup
   # chown wwwrun.daemon /etc/nagios/backup
   # chmod 6755 /etc/nagios/backup/hosts
   # chown wwwrun.daemon /etc/nagios/backup/hosts
   # chmod 6755 /etc/nagios/backup/services
   # chown wwwrun.daemon /etc/nagios/backup/services

   # chmod 644 /etc/nagios/*.cfg
   # chown wwwrun.daemon /etc/nagios/*.cfg
   
   If these directories already have files inside:
   # chmod 644 /etc/nagios/hosts/*.cfg
   # chown wwwrun.daemon /etc/nagios/hosts/*.cfg   
   # chmod 644 /etc/nagios/services/*.cfg
   # chown wwwrun.daemon /etc/nagios/services/*.cfg    

   Also the Nagios binary must be allowed to be execute by the web server user:
   
   # chown nagios.www /usr/sbin/nagios
   # chmod 750 /usr/sbin/nagios
   
   Last the web server user must be allowed to write to the Nagios commandfile:
   
   # chown nagios.www /usr/local/nagios/var/rw/nagios.cmd
   # chmod 660 /usr/local/nagios/var/rw/nagios.cmd
   
   (attention - the paths can be differently!)
   
5. Using
   =====
   
   Now you can start NagiosQL 2005 with any browser you want:
   http://www.domain.tld/nagiosQL/index.php
   
   Requirements for the browser
   - Javascript accepted
   - Cookies accepted
   
   The first login can be done with the following user:
   Username: admin
   Password: admin
   -> Attention, as the first activity you should set a new password for admin!!!
   
   For problems the NagiosQL support forum helps:
   http://www.secretdoor.ch/nagiosqlsupport
   
   If you like this tool or if you use it in an commercial environment, it would make
   me happy when you support the furter development with a donation. Look at
   http://sourceforge.net/projects/nagiosql
   http://sourceforge.net/donate/index.php?group_id=134390
   for more details.
   