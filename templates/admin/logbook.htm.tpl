<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : Logbook template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN logbooksite -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <!--suppress JSUnusedLocalSymbols -->
    <script type="text/javascript">
        <!--
        // build calendar
        calendarinit("{LOCALE}",1,"tfValue1","showfrom","cal-cont","cal");
        calendarinit("{LOCALE}",1,"tfValue2","showto","cal-cont2","cal2");
        // Delete function
        function del(key) {
            if (key === "from") {
                document.frmLogfile.tfValue1.value = "";
            }
            if (key === "to") {
                document.frmLogfile.tfValue2.value = "";
            }
            if (key === "search") {
                document.logSearchForm.txtSearch.value = "";
                document.logSearchForm.submit();
            }
        }
        // Confirmation question
        /**
         * @return {boolean}
         */
        function Validate() {
            const form = document.frmLogfile;
            if ((form.tfValue1.value === "") && (form.tfValue2.value === "")) {
                msginit("{LANG_SELECT_DATE}","{LANG_SECURE_QUESTION}",1);
                return false;
            }
            confirminit("{LANG_DELETELOG}","{LANG_SECURE_QUESTION}",2,"{LANG_YES}","{LANG_NO}",1);
        }
        // Submit form
        function confOpenerYes(key) {
            if (key === 1) {
                document.frmLogfile.submit();
            }
        }
        //-->
    </script>
    <form name="logSearchForm"  method="post" action="">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td class="content_tbl_row1">{LANG_SEARCH_STRING}:</td>
                <td class="content_tbl_row2"><input title="{LANG_SEARCH_STRING}" type="text" name="txtSearch" value="{DAT_SEARCH}"></td>
                <td class="content_tbl_row3"><img src="{IMAGE_PATH}lupe.gif" width="18" height="18" alt="{LANG_SEARCH}" title="{LANG_SEARCH}" style="cursor:pointer;" onClick="document.logSearchForm.submit();">&nbsp;<img src="{IMAGE_PATH}del.png" width="18" height="18" alt="{LANG_DELETE_SEARCH}" title="{LANG_DELETE_SEARCH}" onClick="del('search');" style="cursor:pointer;"></td>
            </tr>
        </table>
    </form>
    <table border="0" cellpadding="0" class="content_logtable">
        <tr>
            <th scope="col" style="width:150px;">{LANG_TIME}</th>
            <th scope="col" style="width:80px;">{LANG_USER}</th>
            <th scope="col" style="width:100px;">{LANG_IP}</th>
            <th scope="col" style="width:80px;">{LANG_DOMAIN}</th>
            <th scope="col" style="width:490px;">{LANG_ENTRY}</th>
        </tr>
        <!-- BEGIN logdatacell -->
        <tr>
            <td>{DAT_TIME}</td>
            <td>{DAT_ACCOUNT}</td>
            <td>{DAT_IPADRESS}</td>
            <td>{DAT_DOMAIN}</td>
            <td style="text-align:left;">{DAT_ACTION}</td>
        </tr>
        <!-- END logdatacell -->
    </table>
    <table border="0" cellpadding="0" class="content_logtable">
        <tr>
            <td align="left" class="loglegend">{LANG_PREVIOUS}</td>
            <td align="right" class="loglegend">{LANG_NEXT}</td>
        </tr>
    </table>
    <form name="frmLogfile" method="post" action="">
        <table border="0" cellpadding="0" class="content_formtable">
            <tr>
                <td colspan="7" class="loglegend" style="padding-bottom:5px;"><strong>{LANG_ENTRIES_BEFORE}</strong></td>
            </tr>
            <tr>
                <td style="width:40px;">{LANG_FROM}:</td>
                <td style="width:80px;"><input title="{LANG_FROM}" type="text" name="tfValue1" id="tfValue1" value="" style="width:80px;" readonly></td>
                <td style="width:80px;"><img src="{IMAGE_PATH}calbtn.gif" width="18" height="18" alt="{LANG_CALENDAR}" title="{LANG_CALENDAR}" id="showfrom" style="cursor:pointer;">&nbsp;<img src="{IMAGE_PATH}del.png" width="18" height="18" alt="{LANG_DELETE_SEARCH}" title="{LANG_DELETE_SEARCH}" onClick="del('from');" style="cursor:pointer;"></td>
                <td style="width:40px;">{LANG_TO}:</td>
                <td style="width:80px;"><input title="{LANG_TO}" type="text" name="tfValue2" id="tfValue2" value="" style="width:80px;" readonly></td>
                <td style="width:60px;"><img src="{IMAGE_PATH}calbtn.gif" width="18" height="18" alt="{LANG_CALENDAR}" title="{LANG_CALENDAR}" id="showto" style="cursor:pointer;">&nbsp;<img src="{IMAGE_PATH}del.png" width="18" height="18" alt="{LANG_DELETE_SEARCH}" title="{LANG_DELETE_SEARCH}" onClick="del('to');" style="cursor:pointer;"></td>
                <td><input type="button" name="butSubmit" id="butSubmit" value="{LANG_DELETE_LOG_ENTRIES}" style="font-size:12px;" onClick="Validate();" {ADD_CONTROL}></td>
            </tr>
        </table>
    </form>
    <p><span class="redmessage">{ERRORMESSAGE}</span><span class="greenmessage">{INFOMESSAGE}</span></p>
</div>
<div id="cal-cont" style="visibility:hidden">
    <div class="hd">{LANG_CALENDAR}</div>
    <div class="bd">
        <div id="cal"></div>
    </div>
</div>
<div id="cal-cont2" style="visibility:hidden">
    <div class="hd">{LANG_CALENDAR}</div>
    <div class="bd">
        <div id="cal2"></div>
    </div>
</div>
<div id="confirmcontainer"></div>
<div id="msgcontainer"></div>
<!-- END logbooksite -->