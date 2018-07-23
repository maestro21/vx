<?php
/**
 * Created by PhpStorm.
 * User: adiacom NUC1
 * Date: 12/11/2017
 * Time: 5:00 PM
 */



/**
 * Lang getter setter
 * @param null $lang
 */

function lang($lang = null){
    if($lang != null) {
        $langs = langs();
        if(is_array($langs)) 
        foreach ($langs as $_lang) {
            if ($_lang['active']) {
                if (empty($lang) && HOST == $_lang['website']) {
                    val('lang', $_lang['abbr']);
                } elseif ($lang == $_lang['abbr']) {
                    val('lang', $_lang['abbr']);
                }
            }
        }
    }
    $lang = val('lang');
    if(!$lang) $lang = @v(G('deflang'),'en');
    return $lang;
}

function langs() {
    return tpl('langs');
}

function getLangs() {
    return G('langs');
}

function importlabels(){
    global $labels;
    $tmp = file("lang/".lang().".txt");
    foreach($tmp as $s){
        $_s = explode("=",$s); $label = $_s[0]; unset($_s[0]); $text = join("=",$_s);
        $labels[trim($label)] = trim($text);
    }
    if(file_exists('themes/'.G('theme').'/lang.php')) include('themes/'.G('theme').'/lang.php');
}

function labels() {
    val('labels', cache('i18n'));
}



function T($text, $number = 1, $addnumber = false, $ucfirst = false) {
    $_text = $text;
    $labels = val('labels');
    $lang = lang();
    if(!isset($labels[$text])) {
        //addLabel($text);
    }
    $text = (isset($labels[$text][$lang]) ? $labels[$text][$lang] : $text);
    if(is_array($text))
        $text = (isset($text[$number]) ? $text[$number] : ((isset($text['other']) ? $text['other'] : array_pop($text))));
    if($addnumber)
        $text = $number . ' ' . $text;

    if(empty($text)) $text = $_text;

    if($lang != 'ru' && $ucfirst) $text = strtoupper($text);

    return $text;

}

function addLabel($key) {
    $labels = cache('i18n');
    if(!$labels) $labels = array();
    if(!isset($labels[$key])) {
        $labels[$key] = [
            'label' => $key,
            'type' => '1'
        ];
        cache('i18n', $labels);
    }
}












function arr2line($key) {
    $pattern = array('[', ']', '--');
    $replace = array('-', '-', '-');
    $key = str_replace($pattern, $replace, $key);
    return $key;
}

function slug($text) {
    if (empty($text)) {
        return '';
    }
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // trim
    $text = trim($text, '-');
    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // lowercase
    $text = strtolower($text);

    return $text;
}

function rus2url($st)
{

    return strtr($st,
        array(
            "а" => "a",
            "б" => "b",
            "в" => "v",
            "г" => "g",
            "д" => "d",
            "е" => "e",
            "ё" => "yo",
            "ж" => "zh",
            "з" => "z",
            "и" => "i",
            "й" => "j",
            "к" => "k",
            "л" => "l",
            "м" => "m",
            "н" => "n",
            "о" => "o",
            "п" => "p",
            "р" => "r",
            "с" => "s",
            "т" => "t",
            "у" => "u",
            "ф" => "f",
            "х" => "h",
            "ц" => "c",
            "ч" => "ch",
            "ш" => "sh",
            "щ" => "shch",
            "ь" => "j",
            "ы" => "i",
            "ъ" => "'",
            "э" => "e",
            "ю" => "yu",
            "я" => "ya",
            "А" => "a",
            "Б" => "b",
            "В" => "v",
            "Г" => "g",
            "Д" => "d",
            "Е" => "ye",
            "Ё" => "yo",
            "Ж" => "zh",
            "З" => "z",
            "И" => "i",
            "Й" => "j",
            "К" => "k",
            "Л" => "l",
            "М" => "m",
            "Н" => "n",
            "О" => "o",
            "П" => "p",
            "Р" => "r",
            "С" => "s",
            "Т" => "t",
            "У" => "u",
            "Ф" => "f",
            "Х" => "h",
            "Ц" => "c",
            "Ч" => "ch",
            "Ш" => "sh",
            "Щ" => "shch",
            "Ь" => "j",
            "Ы" => "i",
            "Ъ" => "'",
            "Э" => "e",
            "Ю" => "yu",
            "Я" => "ya",
            " " => "-",
        )
    );
}