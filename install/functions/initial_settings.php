<?php
exit;
?>
;///////////////////////////////////////////////////////////////////////////////
;
; NagiosQL
;
;///////////////////////////////////////////////////////////////////////////////
;
; Project  : NagiosQL
; Component: Initial configuration settings
; Website  : http://www.nagiosql.org
; Date     : May 27, 2011, 2:35 pm
; Version  : 3.2.0
; $LastChangedRevision: 1058 $
;
; DO NOT USE THIS FILE AS NAGIOSQL SETTINGS FILE!
;
;///////////////////////////////////////////////////////////////////////////////
[db]
type			= mysql
server			= localhost
port			= 3306
database		= db_nagiosql_v32
username		= nagiosql_user
password		= nagiosql_pass
[path]
protocol		= http
tempdir			= /tmp
base_url		= /
base_path		= ''
[data]
locale			= en_GB
encoding		= utf-8
[security]
logofftime		= 3600
wsauth			= 0
[common]
pagelines		= 15
seldisable		= 1
tplcheck		= 0
updcheck		= 1
[network]
proxy			= 0
proxyserver 	= ''
proxyuser		= ''
proxypasswd 	= ''
onlineupdate	= 0