<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : Host list view template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datatablehost -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <!--suppress JSUnusedAssignment, JSUnusedLocalSymbols -->
    <script type="text/javascript">
        <!--
        let intCheck = 0;
        // Action icons
        function actionPic(modus,id,name) {
            if(id !== "") {
                if (modus === "download") {
                    getDownload(id,name);
                    return;
                }
                document.frmDatalist.hidModify.value = modus;
                document.frmDatalist.hidListId.value = id;
                if (document.frmDatalist.hidModify.value === "delete") {
                    confirminit("{LANG_DELETESINGLE}\n"+name+"?","{LANG_SECURE_QUESTION}",2,"{LANG_YES}","{LANG_NO}",1);
                } else {
                    document.frmDatalist.submit();
                }
            }
        }
        // Download function
        function getDownload(id,name) {
            const time = new Date();
            const table = "{TABLE_NAME}";
            this.location.href = "download.php?table="+table+"&line="+id+"&config="+name+"&timestamp="+time.getTime();
        }
        // Add dataset function
        function addDataset() {
            document.frmDatalist.modus.value = "add";
            document.frmDatalist.submit();
        }
        // Write configuration function
        function writeConfig() {
            document.frmDatalist.modus.value = "make";
            document.frmDatalist.submit();
        }
        // Deletion confirmation
        function checkMode() {
            if (document.frmDatalist.selModify.value === "delete") {
                confirminit("{LANG_DELETEOK}","{LANG_SECURE_QUESTION}",2,"{LANG_YES}","{LANG_NO}",2);
            } else {
                document.frmDatalist.submit();
                document.frmDatalist.subDo.disabled = true;
            }
        }
        // Mark all check boxes
        function markAll() {
            if (intCheck === 0) {
                for(let i={MIN_ID};i<={MAX_ID};i++) {
                    if (document.getElementById("chbId_"+i)) {
                        document.getElementById("chbId_"+i).checked = true;
                    }
                }
                intCheck = 1;
            } else {
                for(let i={MIN_ID};i<={MAX_ID};i++) {
                    if (document.getElementById("chbId_"+i)) {
                        document.getElementById("chbId_"+i).checked = false;
                    }
                }
                intCheck = 0;
            }
        }
        // Delete function
        function del(key) {
            if (key === "search") {
                document.frmDatalist.txtSearch.value = "";
                document.frmDatalist.submit();
            }
        }
        // Domain copy enable
        function checkCopy(elem) {
            if (elem === 'copy') {
                document.getElementById("copytext").className = "elementShow";
                document.getElementById("selTarDom").className = "elementShow";
            } else {
                document.getElementById("copytext").className = "elementHide";
                document.getElementById("selTarDom").className = "elementHide";
            }
        }
        //Submit form
        function confOpenerYes(key) {
            if (key === 1) {
                document.frmDatalist.submit();
            }
            if (key === 2) {
                document.frmDatalist.submit();
                document.frmDatalist.subDo.disabled = true;
            }
        }
        // Row sorting
        function sort_row(row,direction) {
            if (('{DISABLE_SORT_2}' === '') || (row === '1')) {
                document.frmDatalist.hidSortBy.value = row;
                document.frmDatalist.hidSortDir.value = direction;
                document.frmDatalist.hidSort.value = '1';
                document.frmDatalist.submit();
            }
        }
        //-->
    </script>
    <form name="frmDatalist" method="post" action="{ACTION_MODIFY}">
        <table style="border: 0; border-spacing: 1px; border-collapse: separate;" class="content_formtable">
            <tr>
                <td style="width: 120px;">{LANG_SEARCH_STRING}:</td>
                <td class="content_tbl_row2"><input title="{LANG_SEARCH_STRING}" type="text" name="txtSearch" value="{DAT_SEARCH}" class="selectborder"></td>
                <td style="width:100px;"><img src="{IMAGE_PATH_HEAD}lupe.gif" width="18" height="18" alt="{LANG_SEARCH}" title="{LANG_SEARCH}" style="cursor:pointer;" onClick="document.frmDatalist.submit();">&nbsp;<img src="{IMAGE_PATH_HEAD}del.png" width="18" height="18" alt="{LANG_DELETE_SEARCH}" title="{LANG_DELETE_SEARCH}" onClick="del('search');" style="cursor:pointer;"></td>
                <td style="width: 180px;">&nbsp;</td>
                <td style="width: 70px;">{LANG_FILTER}:</td>
                <td style="width: 85px;">{LANG_REGISTERED}:</td>
                <td><select name="selRegFilter" id="selRegFilter" title="{LANG_REGISTERED}" class="selectborder" style="width: 50px;" onchange="document.frmDatalist.submit();">
                        <option value="0" {SEL_REGFILTER_0_SELECTED}>{LANG_ALL}</option>
                        <option value="1" {SEL_REGFILTER_1_SELECTED}>{LANG_YES}</option>
                        <option value="2" {SEL_REGFILTER_2_SELECTED}>{LANG_NO}</option>
                    </select></td>
                <td style="width: 20px;">&nbsp;</td>
                <td style="width: 50px;">{LANG_ACTIVE}:</td>
                <td><select name="selActiveFilter" id="selActiveFilter" title="{LANG_ACTIVE}" class="selectborder" style="width: 50px;" onchange="document.frmDatalist.submit();">
                        <option value="0" {SEL_ACTIVEFILTER_0_SELECTED}>{LANG_ALL}</option>
                        <option value="1" {SEL_ACTIVEFILTER_1_SELECTED}>{LANG_YES}</option>
                        <option value="2" {SEL_ACTIVEFILTER_2_SELECTED}>{LANG_NO}</option>
                    </select></td>
            </tr>
        </table>
        <table border="0" cellpadding="0" class="content_listtable" width="100%">
            <tr>
                <th style="width:30px;text-align:center;padding-top:0;padding-bottom:0;"><input name="chbMark" type="checkbox" class="checkbox" id="chbMark" onMouseUp="markAll();" value="" title="{LANG_MARKALL}"></th>
                <th style="width:225px;cursor:pointer;" onclick="sort_row(1,'{SORT_DIR_1}');"><div style="float:left">{FIELD_1}</div><div style="float:right">{SORT_IMAGE_1}</div></th>
                <th style="width:270px;cursor:pointer;{DISABLE_SORT_2}" onclick="sort_row(2,'{SORT_DIR_2}');"><div style="float:left">{FIELD_2}</div><div style="float:right">{SORT_IMAGE_2}</div></th>
                <th style="width:80px;text-align:center;">{LANG_REGISTERED}</th>
                <th style="width:80px;text-align:center;">{LANG_ACTIVE}</th>
                <th style="width:80px;text-align:center;">{LANG_FILE}</th>
                <th style="width:125px;text-align:center;"><div style="width:125px">{LANG_FUNCTION}</div></th>
            </tr>
            <!-- BEGIN datarowhost -->
            <tr>
                <td class="{CELLCLASS_M}"><input title="" type="checkbox" name="chbId_{LINE_ID}"  id="chbId_{LINE_ID}" {DISABLED}></td>
                <td class="{CELLCLASS_L}">{DATA_FIELD_1} {DOMAIN_SPECIAL}</td>
                <td class="{CELLCLASS_L}">{DATA_FIELD_2}</td>
                <td class="{CELLCLASS_M}">{DATA_REGISTERED}</td>
                <td class="{CELLCLASS_M}">{DATA_ACTIVE}</td>
                <td class="{CELLCLASS_M}">{DATA_FILE}</td>
                <td class="{CELLCLASS_M}" valign="middle"><img src="{IMAGE_PATH}edit.gif" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" width="18" height="18" border="0" onClick="actionPic('modify','{LINE_ID}','');" class="{PICTURE_CLASS}">
                    <img src="{IMAGE_PATH}copy.gif" alt="{LANG_DUPLICATE}" title="{LANG_DUPLICATE}" width="18" height="18" border="0" onClick="actionPic('copy','{LINE_ID}','');" class="{PICTURE_CLASS} {LINE_CONTROL}">
                    <img src="{IMAGE_PATH}delete.gif" alt="{LANG_DELETE}" title="{LANG_DELETE}" width="18" height="18" onClick="actionPic('delete','{LINE_ID}','{DATA_FIELD_1}');" class="{PICTURE_CLASS} {LINE_CONTROL}">
                    <img src="{IMAGE_PATH}write.gif" alt="{LANG_WRITE_CONFIG}" title="{LANG_WRITE_CONFIG}" width="18" height="18" onClick="actionPic('config','{LINE_ID}','{DATA_FIELD_1}');" class="{PICTURE_CLASS} {LINE_CONTROL} {ACTIVE_CONTROL}">
                    <img src="{IMAGE_PATH}download.gif" alt="{LANG_DOWNLOAD}" title="{LANG_DOWNLOAD}" width="18" height="18" onClick="actionPic('download','{LINE_ID}','{DATA_FIELD_1}');" class="{PICTURE_CLASS} {LINE_CONTROL} {ACTIVE_CONTROL}">
                    <img src="{IMAGE_PATH}info.gif" alt="{INFO}" title="{INFO}" width="18" height="18" onClick="actionPic('info','{LINE_ID}','');" class="{PICTURE_CLASS}"></td>
            </tr>
            <!-- END datarowhost -->
        </table>
        <table border="0" cellpadding="0" class="content_formtable" width="100%">
            <tr>
                <td><input name="subAdd" type="button" id="subAdd" onClick="addDataset();" value="{LANG_ADD}" {ADD_CONTROL} style="width:100px;">
                    <input name="subMake" type="button" id="subMake" onclick="writeConfig();" value="{LANG_WRITE_CONF_ALL}" {ADD_CONTROL} style="width:205px;"></td>
                <td><input name="modus" type="hidden" id="modus" value="checkform">
                    <input name="hidModify" type="hidden" id="hidModify">
                    <input name="hidListId" type="hidden" id="hidListId">
                    <input name="hidLimit" type="hidden" id="hidLimit" value="{LIMIT}">
                    <input name="hidSortBy" type="hidden" id="hidSortBy" value="{SORT_BY}">
                    <input name="hidSortDir" type="hidden" id="hidSortDir" value="{SORT_DIR}">
                    <input name="hidSort" type="hidden" id="hidSort" value="0"></td>
                <td style="text-align:right">{LANG_MARKED}:
                    <select title="{LANG_MARKED}" name="selModify" id="selModify" onchange="checkCopy(this.value);" class="selectborder" {ADD_CONTROL} style="width:120px;">
                        <option value="none">&nbsp;</option>
                        <option value="delete">{DELETE}</option>
                        <option value="copy">{DUPLICATE}</option>
                        <option value="config">{WRITE_CONFIG}</option>
                        <option value="activate">{ACTIVATE}</option>
                        <option value="deactivate">{DEACTIVATE}</option>
                    </select>
                    <span id="copytext" style="padding-left:10px" class="elementHide">to Domain:</span>
                    <select title="to Domain" name="selTarDom" id="selTarDom" class="elementHide selectborder" {ADD_CONTROL} style="width:120px;">
                        <!-- BEGIN domainlist -->
                        <option value="{DOMAIN_ID}" {DOMAIN_SEL}>{DOMAIN_NAME}</option>
                        <!-- END domainlist -->
                    </select>
                    <input name="subDo" type="button" id="subDo" value="{LANG_DO_IT}" onClick="checkMode();" {ADD_CONTROL} style="width:120px;"></td>
            </tr>
        </table>
    </form>
    <br>
    <div class="pagelinks">{PAGES}</div>
</div>
<div id="confirmcontainer"></div>
<div id="msgcontainer"></div>
<!-- END datatablehost -->
<!-- BEGIN msgfooter -->
<p style="padding-left:10px; width:890px;">
    <!-- BEGIN consistency --><span>{CONSIST_USAGE}</span><br><br><!-- END consistency -->
    <!-- BEGIN infomessage --><span class="greenmessage">{INFOMESSAGE}</span><br><!-- END infomessage -->
    <!-- BEGIN errormessage --><span class="redmessage">{ERRORMESSAGE}</span><br><!-- END errormessage -->
    <!-- BEGIN table_time --><span class="timeinfo">{LAST_MODIFIED_TABLE}</span><br><!-- END table_time -->
    <!-- BEGIN file_time --><span class="timeinfo">{LAST_MODIFIED_FILE}</span><br><!-- END file_time -->
    <!-- BEGIN modification_status --><span class="redmessage">{MODIFICATION_STATUS}</span><br><!-- END modification_status -->
</p>
<!-- END msgfooter -->