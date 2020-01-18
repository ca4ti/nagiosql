<?php
exit;
?>
;///////////////////////////////////////////////////////////////////////////////
;
; NagiosQL
;
;///////////////////////////////////////////////////////////////////////////////
;
; Project   : NagiosQL
; Component : Initial configuration settings
; Website   : https://sourceforge.net/projects/nagiosql/
; Version   : 3.4.1
; GIT Repo  : https://gitlab.com/wizonet/NagiosQL
;
; DO NOT USE THIS FILE AS NAGIOSQL SETTINGS FILE!
;
;///////////////////////////////////////////////////////////////////////////////
[db]
type            = mysqli
server          = localhost
port            = 3306
database        = db_nagiosql_v341
username        = nagiosql_user
password        = nagiosql_pass
[path]
protocol        = http
tempdir         = /tmp
base_url        = /
base_path       = ''
[data]
locale          = en_GB
encoding        = utf-8
[security]
logofftime      = 3600
wsauth          = 0
[common]
pagelines       = 15
seldisable      = 1
tplcheck        = 0
updcheck        = 1
[network]
proxy           = 0
proxyserver     = ''
proxyuser       = ''
proxypasswd     = ''
onlineupdate    = 0
[performance]
parents         = 1