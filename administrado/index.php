<?php

include_once("../common/db.php");
include_once("../common/crypto.php");
include_once("../common/userData.php");
include_once("../common/modal.php");

$siteSettings = getSiteSettings();
$userSettings = getUserSettings();

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

$i = 0;

$displayLang = array_map("trim", explode(",", $siteSettings['language']));

foreach ($displayLang as $key => $value)
{
    $order[] = "WHEN lang = '".$value."' THEN ".$i;
    $order_lang[] = "WHEN code = '".$value."' THEN ".$i;
    $i++;
}

echo "<!DOCTYPE html>";
echo "<html lang=\"".reset($displayLang)."\">";
include_once("./ext/theme/nav.php");

function displayIndexSub()
{
    $siteSettings = getSiteSettings();

    $userSettings = getUserSettings();

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

    $i = 0;

    foreach ($displayLang as $key => $value)
    {
        $order[] = "WHEN lang = '".$value."' THEN ".$i;
        $order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
        $i++;
    }

    $langStrings = getlangstrings();
    $displayIndexSub = $langStrings['displayIndexSub'];

    $displayIndexSub_array = getLangstringsArray('displayIndexSub_array', $displayLang);
    
    echo $displayIndexSub[1];
}


function displayIndex()
{
    $siteSettings = getSiteSettings();

    $userSettings = getUserSettings();

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

    $i = 0;

    foreach ($displayLang as $key => $value)
    {
        $order[] = "WHEN lang = '".$value."' THEN ".$i;
        $order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
        $i++;
    }

    $langStrings = getlangstrings();
    $displayIndex = $langStrings['displayIndex'];

    $displayIndex_array = getLangstringsArray('displayIndex_array', $displayLang);

    echo "<div class=\"panel-header panel-header-sm\">";

    echo "</div>";

    echo "<div class=\"content\" style = \"max-height : 44vh; height : 44vh; overflow : auto\">";
        echo "<div class=\"row\" style = \"height : 95%;\">";
            echo "<div class=\"col-md-12\" style = \"height : 99%;\">";
                echo "<div class=\"card\"style = \"height : 99%;\">";
                    echo "<div class=\"card-header\">";
                        echo "<h1>".$displayIndex[1]."</h1>";
                echo "</div>";
                echo "<div class=\"card-body\">";
                    echo "<div class=\"row\">";
                        echo "<div class=\"col-md-12\">";
                            displayIndexSub();
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</div>";
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']))
{
    printHeader();
    displayMenuAdministradoHeader();
	displayIndex();;
	displayFooterAdministrado();

    print_modal_xl();

    printScripts();
}
?>