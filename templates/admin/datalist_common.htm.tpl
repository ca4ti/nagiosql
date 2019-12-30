<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : Common list view template -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<!-- BEGIN datatablecommon -->
<div id="content_main">
    <div id="content_title">{TITLE}</div>
    <!--suppress JSUnusedLocalSymbols -->
    <script type="text/javascript">
        // Action icons
        function actionPic(modus,id,name) {
            if(id !== '') {
                document.frmDatalist.hidModify.value = modus;
                document.frmDatalist.hidListId.value = id;
                if ((document.frmDatalist.hidModify.value === "delete") && (name === "Admin")) {
                    msginit(name+" {LANG_NODELETE}","{LANG_SECURE_QUESTION}",1);
                    return false;
                }
                if (document.frmDatalist.hidModify.value === "delete") {
                    confirminit("{LANG_DELETESINGLE}\n"+name+"?","{LANG_SECURE_QUESTION}",2,"{LANG_YES}","{LANG_NO}",1);
                } else {
                    document.frmDatalist.submit();
                }
            }
        }
        // Add dataset function
        function addDataset() {
            document.frmDatalist.modus.value = "add";
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
        <table border="0" cellpadding="0" class="content_listtable" width="100%">
            <tr>
                <th style="width:30px;">&nbsp;</th>
                <th style="width:250px;cursor:pointer;" onclick="sort_row(1,'{SORT_DIR_1}');"><div style="float:left">{FIELD_1}</div><div style="float:right">{SORT_IMAGE_1}</div></th>
                <th style="width:500px;cursor:pointer;{DISABLE_SORT_2}" onclick="sort_row(2,'{SORT_DIR_2}');"><div style="float:left">{FIELD_2}</div><div style="float:right">{SORT_IMAGE_2}</div></th>
                <th style="width:100px;text-align:center;">{LANG_ACTIVE}</th>
                <th style="width:100px;text-align:center;">{LANG_FUNCTION}</th>
            </tr>
            <!-- BEGIN datarowcommon -->
            <tr>
                <td class="{CELLCLASS_M}"><input title="" type="checkbox" name="chbId_{LINE_ID}" {DISABLED}></td>
                <td class="{CELLCLASS_L}">{DATA_FIELD_1}</td>
                <td class="{CELLCLASS_L}">{DATA_FIELD_2}</td>
                <td class="{CELLCLASS_M}">{DATA_ACTIVE}</td>
                <td class="{CELLCLASS_M}" valign="middle">
                    <img src="{IMAGE_PATH}edit.gif" alt="{LANG_MODIFY}" title="{LANG_MODIFY}" width="18" height="18" border="0" onClick="actionPic('modify','{LINE_ID}','');" class="{PICTURE_CLASS}">
                    <img src="{IMAGE_PATH}copy.gif" alt="{LANG_COPY}" title="{LANG_COPY}" width="18" height="18" border="0" onClick="actionPic('copy','{LINE_ID}','');" class="{PICTURE_CLASS} {LINE_CONTROL}">
                    {DEL_HIDE_START}<img src="{IMAGE_PATH}delete.gif" alt="{LANG_DELETE}" title="{LANG_DELETE}" width="18" height="18" onClick="actionPic('delete','{LINE_ID}','{DATA_FIELD_1}');" class="{PICTURE_CLASS} {LINE_CONTROL}">{DEL_HIDE_STOP}</td>
            </tr>
            <!-- END datarowcommon -->
        </table>
        <table border="0" cellpadding="0" class="content_formtable" width="100%">
            <tr>
                <td><input name="subAdd" type="button" id="subAdd" onClick="addDataset();" value="{LANG_ADD}" {ADD_CONTROL} style="width:100px;"></td>
                <td><input name="modus" type="hidden" id="modus" value="checkform">
                    <input name="hidModify" type="hidden" id="hidModify">
                    <input name="hidListId" type="hidden" id="hidListId">
                    <input name="hidLimit" type="hidden" id="hidLimit" value="{LIMIT}">
                    <input name="hidSortBy" type="hidden" id="hidSortBy" value="{SORT_BY}">
                    <input name="hidSortDir" type="hidden" id="hidSortDir" value="ASC">
                    <input name="hidSort" type="hidden" id="hidSort" value="0"></td>
                <td style="text-align:right">{LANG_MARKED}:
                    <select title="{LANG_MARKED}" name="selModify" id="select" class="selectborder" style="width:120px;" {ADD_CONTROL}>
                        <option value="none">&nbsp;</option>
                        <option value="delete">{DELETE}</option>
                        <option value="copy">{DUPLICATE}</option>
                    </select>
                    <input name="subDo" type="button" id="subDo" value="{LANG_DO_IT}" onClick="checkMode();" style="width:95px;" {ADD_CONTROL}></td>
            </tr>
        </table>
    </form>
    <div class="pagelinks">{PAGES}</div>
</div>
<div id="confirmcontainer"></div>
<div id="msgcontainer"></div>
<!-- END datatablecommon -->
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