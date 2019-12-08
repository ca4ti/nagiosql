<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : Installer template - step 3 -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<div id="installmenu">
    <div id="installmenu_content">
        <p class="step1_active"><a href='install.php?step=1'><br><br>{STEP1_BOX}</a></p>
        <p class='step2_active'><a href='install.php?step=2'><br><br>{STEP2_BOX}</a></p>
        <p class='step3_active'><br><br>{STEP3_BOX}</p>
    </div>
</div>
<div id="installmain">
    <div id="installmain_content">
    	<h1>{STEP3_TITLE}</h1>
        <form name="frmSetup" id="frmSetup" action="install.php" method="post" class="cmxform">
            <fieldset>
                <legend><b>{STEP3_SUB_TITLE}</b></legend>
                {ERRORMESSAGE}
                <table cellpadding="0" cellspacing="1" border="0" class="{INST_VISIBLE}">
                    <tr class="{STEP3_TEXT_02_SHOW}">
                        <td valign="top" style="width:350px;"><label>{STEP3_TEXT_01}</label></td>
                        <td>{STEP3_TEXT_02}</td>
                    </tr>
                    <tr class="{STEP3_TEXT_03_SHOW}">
                        <td valign="top"><label>{STEP3_TEXT_03}</label></td>
                        <td>{STEP3_TEXT_04}</td>
                    </tr>
                    <tr class="{STEP3_TEXT_05_SHOW}">
                        <td valign="top"><label>{STEP3_TEXT_05}</label></td>
                        <td>{STEP3_TEXT_06}</td>
                    </tr>
                    <tr class="{STEP3_TEXT_07_SHOW}">
                        <td valign="top"><label>{STEP3_TEXT_07}</label></td>
                        <td>{STEP3_TEXT_08}</td>
                    </tr>
                    <tr class="{STEP3_TEXT_09_SHOW}">
                        <td valign="top"><label>{STEP3_TEXT_09}</label></td>
                        <td>{STEP3_TEXT_10}</td>
                    </tr>
                    <tr class="{STEP3_TEXT_11_SHOW}">
                        <td valign="top"><label>{STEP3_TEXT_11}</label></td>
                        <td>{STEP3_TEXT_12}</td>
                    </tr>
                    <tr class="{STEP3_TEXT_13_SHOW}">
                        <td valign="top"><label>{STEP3_TEXT_13}</label></td>
                        <td>{STEP3_TEXT_14}</td>
                    </tr>
                    <tr class="{STEP3_TEXT_15_SHOW}">
                        <td valign="top"><label>{STEP3_TEXT_15}</label></td>
                        <td>{STEP3_TEXT_16}</td>
                    </tr>
                    <tr class="{STEP3_TEXT_17_SHOW}">
                        <td valign="top"><label>{STEP3_TEXT_17}</label></td>
                        <td>{STEP3_TEXT_18}</td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <fieldset class="{STEP4_VISIBLE}">
                <legend><b>{STEP4_SUB_TITLE}</b></legend>
                <table cellpadding="0" cellspacing="1" border="0" class="{STEP4_VISIBLE}">
                <tr>
                    <td valign="top" style="width:350px;"><label>{STEP4_TEXT_01}</label></td>
                    <td>{STEP4_TEXT_02}</td>
                </tr>
                <tr class="{STEP4_TEXT_03_SHOW}">
                    <td valign="top"><label>{STEP4_TEXT_03}</label></td>
                    <td>{STEP4_TEXT_04}</td>
                </tr>
                <tr class="{STEP4_TEXT_05_SHOW}">
                    <td valign="top"><label>{STEP4_TEXT_05}</label></td>
                    <td>{STEP4_TEXT_06}</td>
                </tr>
                <tr class="{STEP4_TEXT_07_SHOW}">
                    <td valign="top"><label>{STEP4_TEXT_07}</label></td>
                    <td>{STEP4_TEXT_08}</td>
                </tr>
                </table>
            </fieldset>   
            <p style="color:red; font-weight:bold;">{INFO_TEXT}</p>
            {BUTTON}
        </form>
    </div>
</div>     
<div id="ie_clearing"> </div>    