<!-- (c) 2005-2020 by Martin Willisegger -->
<!-- -->
<!-- Project   : NagiosQL -->
<!-- Component : Installer template - step 1 -->
<!-- Website   : https://sourceforge.net/projects/nagiosql/ -->
<!-- Version   : 3.4.1 -->
<!-- GIT Repo  : https://gitlab.com/wizonet/NagiosQL -->
<div id="installmenu">
    <div id="installmenu_content">
        <p class="step1_active"><br><br>{STEP1_BOX}</p>
        <p class='step2_active'><a href='install.php?step=2'><br><br>{STEP2_BOX}</a></p>
        <p class='step3_active'><a href='install.php?step=3'><br><br>{STEP3_BOX}</a></p>
    </div>
</div>
<div id="installmain">
    <div id="installmain_content">
        <h1>{STEP1_TITLE}</h1>
        <h3>{STEP1_SUBTITLE1}</h3>
        <img src='images/{CHECK_1_PIC}.png' alt='{CHECK_1_PIC}' title='{CHECK_1_PIC}' class='textmiddle'> Javascript: <span class='{CHECK_1_CLASS}'>{CHECK_1_VALUE}</span>{CHECK_1_INFO}
        <h3>{STEP1_SUBTITLE2}</h3>
        <img src='images/{CHECK_2_PIC}.png' alt='{CHECK_2_PIC}' title='{CHECK_2_PIC}' class='textmiddle'> {CHECK_2_TEXT}: <span class='{CHECK_2_CLASS}'>{CHECK_2_VALUE}</span> {CHECK_2_INFO}
        <h3>{STEP1_SUBTITLE3}</h3>
        <p class='hint'>{STEP1_TEXT3_1}:</p>
        {CHECK_3_CONTENT_1}
        <p class='hint'>{STEP1_TEXT3_2}:</p>
        {CHECK_3_CONTENT_2}
        <h3>{STEP1_SUBTITLE4}</h3>
        <p class='hint'>{STEP1_TEXT4_1}:</p>
        {CHECK_4_CONTENT_1}
        <h3>{STEP1_SUBTITLE5}</h3>
        <p class='hint'>{STEP1_TEXT5_1}:</p>
        {CHECK_5_CONTENT_1}
        <h3>{STEP1_SUBTITLE6}</h3>
        {CHECK_6_CONTENT_1}
        {CHECK_6_CONTENT_2}
        {CHECK_6_CONTENT_3}
        {CHECK_6_CONTENT_4}
        {CHECK_6_CONTENT_5}
        {CHECK_6_CONTENT_6}
        {CHECK_6_CONTENT_7}
        {CHECK_6_CONTENT_8}
        <br>
        {MESSAGE}
        <br>
        <div id="{DIV_ID}">
            <form name="frmStep1" id="frmStep1" action="install.php" method="post">
                <input type="hidden" name="hidJScript" value="">
                {FORM_CONTENT}
            </form>
        </div>
    </div>
</div>     
<script type="text/javascript">
    <!--
    document.frmStep1.hidJScript.value = 'yes';
    //-->
</script>
<div id="ie_clearing"> </div>          