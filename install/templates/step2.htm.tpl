<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : Installer template - step 2 -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<script type="text/javascript">
    <!--
    // Send form
    /**
     * @return {boolean}
     */
    function LockButton() {
        if (checkForm() === false) {
            return false;
        } else {
            // Submit form
            document.frmSetup.submit();
        }
    }
    // Check form entries
    function checkForm() {
        // Are all required fields filled in?
        const fields = "tfDBserver,tfLocalSrv,tfDBport,tfDBname,tfDBuser,tfDBpass{INSTALL_FIELDS}";
        const form = document.frmSetup;
        const ar_field = fields.split(",");
        for (let i=0;i<ar_field.length;i++){
            if (form[ar_field[i]].value === "") {
                alert("{FIELDS_MESSAGE}");
                form[ar_field[i]].focus();
                return false;
            }
        }
        if (form.tfQLpass.value !== form.tfQLpassrepeat.value) {
            alert("PASSWORD_MESSAGE");
            return false;
        }
        return true;
    }
    // Modify port value
    function modifyPort(db) {
        if ((db === 'mysql') || (db === 'mysqli')) {
            document.frmSetup.tfDBport.value = '3306';
            document.frmSetup.tfDBport.className = 'required';
        } else if (db === 'pgsql') {
            document.frmSetup.tfDBport.value = '5432';
            document.frmSetup.tfDBport.className = 'required';
        } else {
            document.frmSetup.tfDBport.value = '';
            document.frmSetup.tfDBport.className = '';
        }
    }
    //-->
</script>   
<div id="installmenu">
    <div id="installmenu_content">
        <p class="step1_active"><a href='install.php?step=1'><br><br>{STEP1_BOX}</a></p>
        <p class='step2_active'><br><br>{STEP2_BOX}</p>
        <p class='step3_active'><a href='install.php?step=3'><br><br>{STEP3_BOX}</a></p>
    </div>
</div>
<div id="installmain">
    <div id="installmain_content">
        <h1>{STEP2_TITLE}</h1>
        <form name="frmSetup" id="frmSetup" action="install.php" method="post" class="cmxform">
            <p class='hint'>{STEP2_TEXT1_1}:</p>
            {STEP2_TEXT1_2}
            <fieldset>
                <legend><b>{STEP2_TEXT2_1}</b></legend>
                <table cellpadding="0" cellspacing="1" border="0">
                    <tr>
                        <td style="width:250px;">{STEP2_TEXT2_2} <em>*</em></td>
                        <td>
                            <select title="{STEP2_TEXT2_2}" name="selDBtype" id="selDBtype" class="required" onchange="modifyPort(this.value);">
                                {STEP2_VALUE2_2}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{STEP2_TEXT2_3} <em>*</em></td>
                        <td><input title="{STEP2_TEXT2_3}" type="text" name="tfDBserver" id="tfDBserver" class="required" value="{STEP2_VALUE2_3}"></td>
                    </tr>
                    <tr>
                        <td>{STEP2_TEXT2_4} <em>*</em></td>
                        <td><input title="{STEP2_TEXT2_4}" type="text" name="tfLocalSrv" id="tfLocalSrv" class="required" value="{STEP2_VALUE2_4}"></td>
                    </tr>
                    <tr>
                        <td>{STEP2_TEXT2_5} <em>*</em></td>
                        <td><input title="{STEP2_TEXT2_5}" type="text" name="tfDBport" id="tfDBport" class="required" value="{STEP2_VALUE2_5}"></td>
                    </tr>
                    <tr>
                        <td>{STEP2_TEXT2_6} <em>*</em></td>
                        <td><input title="{STEP2_TEXT2_6}" type="text" name="tfDBname" id="tfDBname" class="required" value="{STEP2_VALUE2_6}"></td>
                    </tr>
                    <tr class="{INST_VISIBLE}">
                        <td>{STEP2_TEXT2_7} <em>*</em></td>
                        <td><input title="{STEP2_TEXT2_7}" type="text" name="tfDBuser" id="tfDBuser" class="required" value="{STEP2_VALUE2_7}"></td>
                    </tr>
                    <tr class="{INST_VISIBLE}">
                        <td>{STEP2_TEXT2_8} <em>*</em></td>
                        <td><input title="{STEP2_TEXT2_8}" type="password" name="tfDBpass" id="tfDBpass" class="required" value="{STEP2_VALUE2_8}"></td>
                    </tr>
                    <tr>
                        <td>{STEP2_TEXT2_9} <em>*</em></td>
                        <td><input title="{STEP2_TEXT2_9}" type="text" name="tfDBprivUser" id="tfDBprivUser" class="required" value="{STEP2_VALUE2_9}" size="19"></td>
                    </tr>
                    <tr>
                        <td>{STEP2_TEXT2_10} <em>*</em></td>
                        <td><input title="{STEP2_TEXT2_10}" name="tfDBprivPass" id="tfDBprivPass" class="required" type="password"></td>
                    </tr>
                    <tr class="{INST_VISIBLE}">
                        <td>{STEP2_TEXT2_11}</td>
                        <td><input title="{STEP2_TEXT2_11}" type="checkbox" name="chbDrop" id="chbDrop" value="1" {STEP2_VALUE2_11}></td>
                    </tr>
                </table>
            </fieldset>
            <fieldset class="{INST_VISIBLE}">
                <legend><b>{STEP2_TEXT3_1}</b></legend>
                <table cellpadding="0" cellspacing="1" border="0">
                    <tr>
                        <td style="width:250px;">{STEP2_TEXT3_2} <em>*</em></td>
                        <td><input title="{STEP2_TEXT3_2}" type="text" name="tfQLuser" id="tfQLuser" class="required" value="{STEP2_VALUE3_2}" size="15"></td>
                    </tr>
                    <tr>
                        <td>{STEP2_TEXT3_3} <em>*</em></td>
                        <td><input title="{STEP2_TEXT3_3}" type="password" class="required" name="tfQLpass" id="tfQLpass" value="{STEP2_VALUE3_3}"  size="15"></td>
                    </tr>
                    <tr>
                        <td>{STEP2_TEXT3_4} <em>*</em></td>
                        <td><input title="{STEP2_TEXT3_4}" type="password" class="required" name="tfQLpassrepeat" id="tfQLpassrepeat" size="15"></td>
                    </tr>
                </table>
            </fieldset>
            <fieldset class="{INST_VISIBLE}">
                <legend><b>{STEP2_TEXT4_1}</b></legend>
                <table cellpadding="0" cellspacing="1" border="0">
                    <tr>
                        <td style="width:250px;">{STEP2_TEXT4_2} </td>
                        <td><input title="{STEP2_TEXT4_2}" type="checkbox" name="chbSample" id="chbSample" value="1" {STEP2_VALUE4_2}></td>
                    </tr>
                </table>
            </fieldset>
            <fieldset class="{INST_VISIBLE}">
                <legend><b>{STEP2_TEXT5_1}</b></legend>
                <table cellpadding="0" cellspacing="1" border="0">
                    <tr>
                        <td style="width:250px;">{STEP2_TEXT5_2}</td>
                        <td><input title="{STEP2_TEXT5_2}" type="checkbox" name="chbPath" id="chbPath" value="1" {STEP2_VALUE5_2}></td>
                    </tr>
                    <tr>
                        <td>{STEP2_TEXT5_3}</td>
                        <td><input title="{STEP2_TEXT5_3}" type="text" name="tfQLpath" id="tfQLpath" value="{STEP2_VALUE5_3}" size="15"></td>
                    </tr>
                    <tr>
                        <td>{STEP2_TEXT5_4}</td>
                        <td><input title="{STEP2_TEXT5_4}" type="text" name="tfNagiosPath" id="tfNagiosPath" value="{STEP2_VALUE5_4}" size="15"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding-top:5px;">{STEP2_TEXT5_5}<br>{STEP2_TEXT5_6}</td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <div id="install-next">
                <input type="hidden" name="hidStep" value="3">
                <img src="images/next.png" onClick="LockButton();" alt="{STEP2_FORM_1}" title="{STEP2_FORM_1}" style="cursor:pointer"><br>{STEP2_FORM_1}
            </div>
        </form>
    </div>
</div>     
<div id="ie_clearing"> </div>    