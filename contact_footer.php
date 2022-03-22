<?php
global $link;

global $phrase;

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

$replaceTable = getReplaceTable();

$langStrings = getlangstrings();

$displayContactInfo = $langStrings['displayContactInfo'];

$displayContactInfo_array = getLangstringsArray('displayContactInfo_array', $displayLang);

echo "<li>";
	if ($_SERVER['SERVER_NAME'] === 'localhost')
	{
		$url = "//localhost/";
		$url_admin = "//localhost/administrado/";
		$url_servotablo = "//localhost/servotablo/";
	}
	else if ($_SERVER['SERVER_NAME'] === 'server01')
	{
		$url = "//server01/flexshare/lv/";
		$url_admin = "//server01/flexshare/lv/administrado/";
		$url_servotablo = "//server01/flexshare/lv/servotablo/";
	}
	else
	{
		$url = "//www.lvteknik.se/";
		$url_admin = "//www.lvteknik.se/administrado/";
		$url_servotablo = "//www.lvteknik.se/servotablo/";
	}

    /*if (file_exists("./img/lvteknik_300.png"))
    {
        echo "<img src = \"./img/lvteknik_300.png\" style = \"height : 30px;\">";
    }
    else
    {
        echo "<img src = \"../img/lvteknik_300.png\" style = \"height : 30px;\">";
    }*/

	echo "<img src = \"".$url."img/lvteknik_300.png\" style = \"height : 30px;\">";

    echo "<h6>";
    echo "<a href = \"mailto:wedin@lvteknik.se\">wedin@lvteknik.se</a></h6>";
    echo "<a href=\"tel:+460730969599\"><span class=\"text-color h4\">+46-0730-969 599</span></a>";
echo "</li>";

?>