<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : admin mainpage template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN main -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <p>{DESC}</p>
    <br>
<!-- BEGIN statistics -->	  
    <h2>{STATISTICS}</h2>
    <table border="0" cellpadding="0" class="content_listtable">
        <tr>
            <th class="content_tbl_row1">{TYPE}</th>
            <th class="content_tbl_row3" style="text-align:center;">{ACTIVE}</th>
            <th class="content_tbl_row3" style="text-align:center;">{INACTIVE}</th>
        </tr>
<!-- BEGIN statisticrow -->	
        <tr>
            <td class="tdlb" style="height:20px;">{NAME}</td>
            <td class="tdmb" style="height:20px;">{ACT_COUNT}</td>
            <td class="tdmb" style="height:20px;">{INACT_COUNT}</td>
        </tr>
<!-- END statisticrow -->	
    </table>
<!-- END statistics -->	
</div>
<div id="msgcontainer"></div>
<div id="infodialog">
    <div id="dialogcontent" class="bd"></div>
</div>
<!-- END main -->