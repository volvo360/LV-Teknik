<?php

if (basename($_SERVER['SCRIPT_NAME']) === basename(__FILE__))
{
    header('Content-Type: application/json');
    
    session_start();
	include_once("../../../common/db.php");
	include_once("./../../ext/theme/nav.php");
    include_once("./../../../common/userData.php");
    
    prioLangJSON();
}

function prioLang()
{
    global $link;
    global $link_k;

    global $phrase;
    global $phrase_k;
    
    $userSettings = getUserSettings();

    $table = "`".PREFIX."translation`";
    $table2 = "`".PREFIX."languages`";

    $replaceTable = getReplaceTable();
    $userSettings = getUserSettings();
    $replaceLang = getReplaceLang();
    
    $displayLang = array_map("trim", explode(",", $userSettings['langService']));
    
    if (empty($userSettings['langService']))
    {
        $data = array_map("trim", explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']));

        foreach ($data as $key => $value)
        {
            if (!startswith($value, "q=" ))
            {
                $displayLang[] = substr($value,0,2);
            }
        }
        
        $displayLang = array_filter(array_unique($displayLang));
    }
    else
    {    
        $displayLang = array_map("trim", explode(",", $userSettings['langService']));
    }
    
    $langStrings = getlangstrings();
    $prioLang = $langStrings['prioLang'];

    $prioLang_array = getLangstringsArray('prioLang_array', $displayLang);
    
    $sql = "SELECT tableKey, `Local language name` as lang, Code FROM ".$table2." ORDER BY CASE ".implode(" ", (array)$order_lang)." WHEN Code = 'en' THEN 11 WHEN Code = 'de' THEN 12 WHEN Code = 'fr' THEN 13 WHEN Code = 'it' THEN 14 ELSE 100 END, code LIMIT 18446744073709551615";
    //echo __LINE__." ".$sql."<br>";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $replaceLang[$row['tableKey']] = $row['lang'];
        $replaceLangCode[$row['Code']] = $row['tableKey'];
    }
    
    echo "<div class=\"form-group row\">";
        echo "<label for=\"langService\" class=\"col-sm-2 col-form-label\">".$prioLang[1]."</label>";
        echo "<div class=\"col-sm-4\">";
            echo "<div id = \"tree_prioLang\" class = \"fancyTreeClass\" data-ajax_target = \"prioLang\" data-replace_table = \"".$replaceTable[PREFIX.'user_settings']."\">";
                echo "<ul style =\"display : none;\">";

                    foreach ($displayLang as $key => $value)
                    {
                        echo "<li id = \"".$replaceLangCode[$value]."\">";
                            echo $replaceLang[$replaceLangCode[$value]];
                        echo "</li>";
                    }

                echo "</ul>";
            echo "</div>";
        echo "</div>";
    
        echo "<div class=\"col-sm-6\">";
            echo $prioLang[2];
        echo "</div>";
    echo "</div>";

}

function prioLangJSON()
{
    global $link;
    global $link_k;

    global $phrase;
    global $phrase_k;

    $table = "`".PREFIX."translation`";
    $table2 = "`".PREFIX."languages`";

    $replaceTable = getReplaceTable();
    $userSettings = getUserSettings();
    $replaceLang = getReplaceLang();
    
    $displayLang = array_map("trim", explode(",", $userSettings['langService']));
    
    if (empty($userSettings['langService']))
    {
        $data = array_map("trim", explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']));

        foreach ($data as $key => $value)
        {
            if (!startswith($value, "q=" ))
            {
                $displayLang[] = substr($value,0,2);
            }
        }
        
        $displayLang = array_filter(array_unique($displayLang));
    }
    else
    {    
        $displayLang = array_map("trim", explode(",", $userSettings['langService']));
    }
    
    $sql = "SELECT tableKey, `Local language name` as lang, Code FROM ".$table2." ORDER BY CASE ".implode(" ", (array)$order_lang)." WHEN Code = 'en' THEN 11 WHEN Code = 'de' THEN 12 WHEN Code = 'fr' THEN 13 WHEN Code = 'it' THEN 14 ELSE 100 END, code LIMIT 18446744073709551615";
    //echo __LINE__." ".$sql."<br>";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $replaceLang[$row['tableKey']] = $row['lang'];
        $replaceLangCode[$row['Code']] = $row['tableKey'];
    }
    
    $i = 1;
    $p = count($displayLang);
    
    echo "{\"tree".$_POST['table']."2\" : \"".$_POST['table']."2\", \"children\" : [";
       
        foreach ($displayLang as $key => $value)
        {
            if ($i < $p)
            {
                echo "{\"title\" : \"".$replaceLang[$replaceLangCode[$value]]."\", \"key\" : \"".$replaceLangCode[$value]."\"},";
            }
            else
            {
                echo "{\"title\" : \"".$replaceLang[$replaceLangCode[$value]]."\", \"key\" : \"".$replaceLangCode[$value]."\"}";
            }
            $i++;
        }
    
    echo "]}";
}

?>