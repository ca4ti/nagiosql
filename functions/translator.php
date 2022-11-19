<?php
/* ----------------------------------------------------------------------------
 NagiosQL
-------------------------------------------------------------------------------
 (c) 2005-2022 by Martin Willisegger

 Project   : NagiosQL
 Component : Translation Functions
 Website   : https://sourceforge.net/projects/nagiosql/
 Version   : 3.5.0
 GIT Repo  : https://gitlab.com/wizonet/NagiosQL
-----------------------------------------------------------------------------*/
/*
Translate given text
*/
function translate($strTranslation): string
{
    return str_replace("'", '&#039;', gettext(str_replace('"', '&quot;', gettext($strTranslation))));
}

/*
Internationalization and Localization utilities
*/
function getLanguageCodefromLanguage($strLangSearch): string
{
    $strReturn = 'en_GB';
    $arrLangDetail = getLanguageData();
    foreach ($arrLangDetail as $key => $elem) {
        if ($strLangSearch === $elem['description']) {
            $strReturn = $key;
        }
    }
    return $strReturn;
}

function getLanguageNameFromCode($codetosearch, $withnative = true)
{
    $strReturn = false;
    $detaillanguages = getLanguageData();
    if (isset($detaillanguages[$codetosearch]['description'])) {
        if ($withnative) {
            $strReturn = $detaillanguages[$codetosearch]['description'] . ' - ' .
                $detaillanguages[$codetosearch]['nativedescription'];
        } else {
            $strReturn = $detaillanguages[$codetosearch]['description'];
        }
    }
    return $strReturn;
}


function getLanguageData()
{
    unset($arrLangSupported);
    /* English */
    $arrLangSupported['en_GB']['description'] = translate('English');
    $arrLangSupported['en_GB']['nativedescription'] = 'English';

    /* German */
    $arrLangSupported['de_DE']['description'] = translate('German');
    $arrLangSupported['de_DE']['nativedescription'] = 'Deutsch';

    /* Chinese (Simplified) */
    $arrLangSupported['zh_CN']['description'] = translate('Chinese (Simplified)');
    $arrLangSupported['zh_CN']['nativedescription'] = '&#31616;&#20307;&#20013;&#25991;';

    /* Italian */
    $arrLangSupported['it_IT']['description'] = translate('Italian');
    $arrLangSupported['it_IT']['nativedescription'] = 'Italiano';

    /* French */
    $arrLangSupported['fr_FR']['description'] = translate('French');
    $arrLangSupported['fr_FR']['nativedescription'] = 'Fran&#231;ais';

    /* Russian */
    $arrLangSupported['ru_RU']['description'] = translate('Russian');
    $arrLangSupported['ru_RU']['nativedescription'] = '&#1056;&#1091;&#1089;&#1089;&#1082;&#1080;&#1081;';

    /* Spanish */
    $arrLangSupported['es_ES']['description'] = translate('Spanish');
    $arrLangSupported['es_ES']['nativedescription'] = 'Espa&#241;ol';

    /* Brazilian Portuguese */
    $arrLangSupported['pt_BR']['description'] = translate('Portuguese (Brazilian)');
    $arrLangSupported['pt_BR']['nativedescription'] = 'Portugu&#234;s do Brasil';

    /* Dutch */
    $arrLangSupported['nl_NL']['description'] = translate('Dutch');
    $arrLangSupported['nl_NL']['nativedescription'] = 'Nederlands';

    /* Danish */
    $arrLangSupported['da_DK']['description'] = translate('Danish');
    $arrLangSupported['da_DK']['nativedescription'] = 'Dansk';

    uasort($arrLangSupported, 'user_sort');
    return $arrLangSupported;
}

function user_sort($intValue1, $intValue2): int
{
    $intReturn = -1;
    if ($intValue1['description'] > $intValue2['description']) {
        $intReturn = 1;
    }
    return $intReturn;
}