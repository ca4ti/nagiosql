<?php
///////////////////////////////////////////////////////////////////////////////
//
// NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// (c) 2005-2020 by Martin Willisegger
//
// Project   : NagiosQL
// Component : Translation Functions
// Website   : https://sourceforge.net/projects/nagiosql/
// Version   : 3.4.1
// GIT Repo  : https://gitlab.com/wizonet/NagiosQL
//
///////////////////////////////////////////////////////////////////////////////
//
// Translate given text
//
function translate($translation)
{
    $translation = str_replace('"', '&quot;', gettext($translation));
    $translation = str_replace("'", '&#039;', gettext($translation));
    return $translation;
}
///
/// Internationalization and Localization utilities
///
function getLanguageCodefromLanguage($languagetosearch)
{
    $strReturn = 'en_GB';
    $detaillanguages = getLanguageData();
    /** @noinspection ForeachSourceInspection */
    foreach ($detaillanguages as $key2 => $languagename) {
        if ($languagetosearch == $languagename['description']) {
            $strReturn = $key2;
        }
    }
    // else return default en code
    return $strReturn;
}

function getLanguageNameFromCode($codetosearch, $withnative = true)
{
    $strReturn = false;
    $detaillanguages = getLanguageData();
    if (isset($detaillanguages[$codetosearch]['description'])) {
        if ($withnative) {
            $strReturn = $detaillanguages[$codetosearch]['description'].' - '.
                $detaillanguages[$codetosearch]['nativedescription'];
        } else {
            $strReturn = $detaillanguages[$codetosearch]['description'];
        }
    }
    return $strReturn;
}


function getLanguageData()
{
    unset($supportedLanguages);
    // English
    $supportedLanguages['en_GB']['description'] = translate('English');
    $supportedLanguages['en_GB']['nativedescription'] = 'English';

    // German
    $supportedLanguages['de_DE']['description'] = translate('German');
    $supportedLanguages['de_DE']['nativedescription'] = 'Deutsch';

    // Chinese (Simplified)
    $supportedLanguages['zh_CN']['description'] = translate('Chinese (Simplified)');
    $supportedLanguages['zh_CN']['nativedescription'] = '&#31616;&#20307;&#20013;&#25991;';

    // Italian
    $supportedLanguages['it_IT']['description'] = translate('Italian');
    $supportedLanguages['it_IT']['nativedescription'] = 'Italiano';

    // French
    $supportedLanguages['fr_FR']['description'] = translate('French');
    $supportedLanguages['fr_FR']['nativedescription'] = 'Fran&#231;ais';

    // Russian
    $supportedLanguages['ru_RU']['description'] = translate('Russian');
    $supportedLanguages['ru_RU']['nativedescription'] = '&#1056;&#1091;&#1089;&#1089;&#1082;&#1080;&#1081;';

    // Spanish
    $supportedLanguages['es_ES']['description'] = translate('Spanish');
    $supportedLanguages['es_ES']['nativedescription'] = 'Espa&#241;ol';

    // Brazilian Portuguese
    $supportedLanguages['pt_BR']['description'] = translate('Portuguese (Brazilian)');
    $supportedLanguages['pt_BR']['nativedescription'] = 'Portugu&#234;s do Brasil';

    // Dutch
    $supportedLanguages['nl_NL']['description'] = translate('Dutch');
    $supportedLanguages['nl_NL']['nativedescription'] = 'Nederlands';

    // Danish
    $supportedLanguages['da_DK']['description'] = translate('Danish');
    $supportedLanguages['da_DK']['nativedescription'] = 'Dansk';
   
    // No longer supported language because of missing translators
    //
    //  // Japanese
    //  $supportedLanguages['ja_JP']['description'] = translate('Japanese');
    //  $supportedLanguages['ja_JP']['nativedescription'] = '&#x65e5;&#x672c;&#x8a9e;';
    //
    //  // Polish
    //  $supportedLanguages['pl_PL']['description'] = translate('Polish');
    //  $supportedLanguages['pl_PL']['nativedescription'] = 'Polski';
    //
    //  // Spanish (Argentina)
    //  $supportedLanguages['es_AR']['description'] = translate('Spanish (Argentina)');
    //   $supportedLanguages['es_AR']['nativedescription'] = 'Espa&#241;ol Argentina';
    ///
    /// Currently not supported languages
    //
    //  // Albanian
    //  $supportedLanguages['sq']['description'] = $clang->translate('Albanian');
    //  $supportedLanguages['sq']['nativedescription'] = 'Shqipe';
    //
    //  // Basque
    //  $supportedLanguages['eu']['description'] = translate('Basque');
    //  $supportedLanguages['eu']['nativedescription'] = 'Euskara';
    //
    //  // Bosnian
    //  $supportedLanguages['bs']['description'] = translate('Bosnian');
    //  $supportedLanguages['bs']['nativedescription'] =
    //                              '&#x0411;&#x044a;&#x043b;&#x0433;&#x0430;&#x0440;&#x0441;&#x043a;&#x0438;';
    //
    //  // Bulgarian
    //  $supportedLanguages['bg']['description'] = translate('Bulgarian');
    //  $supportedLanguages['bg']['nativedescription'] =
    //                              '&#x0411;&#x044a;&#x043b;&#x0433;&#x0430;&#x0440;&#x0441;&#x043a;&#x0438;';
    //
    //  // Catalan
    //  $supportedLanguages['ca']['description'] = translate('Catalan');
    //  $supportedLanguages['ca']['nativedescription'] = 'Catal&#940;';
    //
    //  // Welsh
    //  $supportedLanguages['cy']['description'] = translate('Welsh');
    //  $supportedLanguages['cy']['nativedescription'] = 'Cymraeg';
    //
    //  // Chinese (Traditional - Hong Kong)
    //  $supportedLanguages['zh-Hant-HK']['description'] = translate('Chinese (Traditional - Hong Kong)');
    //  $supportedLanguages['zh-Hant-HK']['nativedescription'] = '&#32321;&#39636;&#20013;&#25991;&#35486;&#31995;';
    //
    //  // Chinese (Traditional - Taiwan)
    //  $supportedLanguages['zh-Hant-TW']['description'] = translate('Chinese (Traditional - Taiwan)');
    //  $supportedLanguages['zh-Hant-TW']['nativedescription'] = 'Chinese (Traditional - Taiwan)';
    //
    //  // Croatian
    //  $supportedLanguages['hr']['description'] = translate('Croatian');
    //  $supportedLanguages['hr']['nativedescription'] = 'Hrvatski';
    //
    //  // Czech
    //  $supportedLanguages['cs']['description'] = translate('Czech');
    //  $supportedLanguages['cs']['nativedescription'] = '&#x010c;esky';
    //
    //
    //  // Estonian
    //  $supportedLanguages['et']['description'] = translate('Estonian');
    //  $supportedLanguages['et']['nativedescription'] = 'Eesti';
    //
    //  // Finnish
    //  $supportedLanguages['fi']['description'] = translate('Finnish');
    //  $supportedLanguages['fi']['nativedescription'] = 'Suomi';
    //
    //  // Galician
    //  $supportedLanguages['gl']['description'] = translate('Galician');
    //  $supportedLanguages['gl']['nativedescription'] = 'Galego';
    //
    //  // German informal
    //  $supportedLanguages['de-informal']['description'] = translate('German informal');
    //  $supportedLanguages['de-informal']['nativedescription'] = 'Deutsch (Du)';
    //
    //  // Greek
    //  $supportedLanguages['el']['description'] = translate('Greek');
    //  $supportedLanguages['el']['nativedescription'] = '&#949;&#955;&#955;&#951;&#957;&#953;&#954;&#940;';
    //
    //  // Hebrew
    //  $supportedLanguages['he']['description'] = translate('Hebrew');
    //  $supportedLanguages['he']['nativedescription'] = ' &#1506;&#1489;&#1512;&#1497;&#1514;';
    //
    //  // Hungarian
    //  $supportedLanguages['hu']['description'] = translate('Hungarian');
    //  $supportedLanguages['hu']['nativedescription'] = 'Magyar';
    //
    //  // Indonesian
    //  $supportedLanguages['id']['description'] = translate('Indonesian');
    //  $supportedLanguages['id']['nativedescription'] = 'Bahasa Indonesia';
    //
    //
    //  // Lithuanian
    //  $supportedLanguages['lt']['description'] = translate('Lithuanian');
    //  $supportedLanguages['lt']['nativedescription'] = 'Lietuvi&#371;';
    //
    //  // Macedonian
    //  $supportedLanguages['mk']['description'] = translate('Macedonian');
    //  $supportedLanguages['mk']['nativedescription'] =
    //                              '&#1052;&#1072;&#1082;&#1077;&#1076;&#1086;&#1085;&#1089;&#1082;&#1080;';
    //
    //  // Norwegian Bokml
    //  $supportedLanguages['nb']['description'] = translate('Norwegian (Bokmal)');
    //  $supportedLanguages['nb']['nativedescription'] = 'Norsk Bokm&#229;l';
    //
    //  // Norwegian Nynorsk
    //  $supportedLanguages['nn']['description'] = translate('Norwegian (Nynorsk)');
    //  $supportedLanguages['nn']['nativedescription'] = 'Norsk Nynorsk';
    //
    //  // Portuguese
    //  $supportedLanguages['pt']['description'] = translate('Portuguese');
    //  $supportedLanguages['pt']['nativedescription'] = 'Portugu&#234;s';
    //
    //  // Romanian
    //  $supportedLanguages['ro']['description'] = translate('Romanian');
    //  $supportedLanguages['ro']['nativedescription'] = 'Rom&#226;nesc';
    //
    //  // Slovak
    //  $supportedLanguages['sk']['description'] = translate('Slovak');
    //  $supportedLanguages['sk']['nativedescription'] = 'Slov&aacute;k';
    //
    //  // Slovenian
    //  $supportedLanguages['sl']['description'] = translate('Slovenian');
    //  $supportedLanguages['sl']['nativedescription'] = 'Sloven&#353;&#269;ina';
    //
    //  // Serbian
    //  $supportedLanguages['sr']['description'] = translate('Serbian');
    //  $supportedLanguages['sr']['nativedescription'] = 'Srpski';
    //
    //  // Spanish (Mexico)
    //  $supportedLanguages['es-MX']['description'] = translate('Spanish (Mexico)');
    //  $supportedLanguages['es-MX']['nativedescription'] = 'Espa&#241;ol Mejicano';
    //
    //  // Swedish
    //  $supportedLanguages['sv']['description'] = translate('Swedish');
    //  $supportedLanguages['sv']['nativedescription'] = 'Svenska';
    //
    //  // Turkish
    //  $supportedLanguages['tr']['description'] = translate('Turkish');
    //  $supportedLanguages['tr']['nativedescription'] = 'T&#252;rk&#231;e';
    //
    //  // Thai
    //  $supportedLanguages['th']['description'] = translate('Thai');
    //  $supportedLanguages['th']['nativedescription'] = '&#3616;&#3634;&#3625;&#3634;&#3652;&#3607;&#3618;';
    //
    //  // Vietnamese
    //  $supportedLanguages['vi']['description'] = translate('Vietnamese');
    //  $supportedLanguages['vi']['nativedescription'] = 'Ti&#7871;ng Vi&#7879;t';

    uasort($supportedLanguages, 'user_sort');
    return $supportedLanguages;
}

function user_sort($intValue1, $intValue2)
{
    $intReturn = -1;
    // smarts is all-important, so sort it first
    if ($intValue1['description'] > $intValue2['description']) {
        $intReturn = 1;
    }
    return $intReturn;
}
