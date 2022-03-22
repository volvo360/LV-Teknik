<?php
	include_once("./common/db.php");
    include_once("./common/userData.php");
    include_once("./theme.php");
    $siteSettings = getSiteSettings();

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

	$i = 0;

	$displayLang = array_map("trim", explode(",", $siteSettings['language']));

	foreach ($displayLang as $key => $value)
	{
		$order[] = "WHEN lang = '".$value."' THEN ".$i;
		$order_lang[] = "WHEN code = '".$value."' THEN ".$i;
		$i++;
	}

	$langStrings = getlangstrings();
    $addNewsletter = $langStrings['addNewsletter'];

    $addNewsletter_array = getLangstringsArray('addNewsletter_array', $displayLang);

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

    $langStrings = getlangstrings();
    $displayInformilo2 = $langStrings['displayInformilo2'];

    $displayInformilo2_array = getLangstringsArray('displayInformilo2_array', $displayLang);
    
    $replaceTable = getReplaceTable();
    

    $table100 = "`".PREFIX."newsletter`";
    $table101 = "`".PREFIX."newsletter_lang`";
	checkTable($table100);
    unset($data);

    $sql = "SELECT * FROM (SELECT * FROM $table100) as newsletter LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT newsletterId, 
          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table101) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
          GROUP BY newsletterId) AS newsletter_lang ON newsletter.newsletterId = newsletter_lang.newsletterId WHERE (`groups` LIKE '1 %' OR `groups` LIKE '1' OR `groups` LIKE '% 1 %'"." OR `groups` LIKE '% 1,%'"." OR `groups` LIKE '% 1' OR `groups` LIKE '1, %' OR `groups` LIKE '1') ORDER BY newsletter.date DESC LIMIT 1";

   	//echo __LINE__." ".$sql."<br>";
    $result10 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

	if (mysqli_num_rows($result10) > 0)
	{
		echo $addNewsletter[5]."<br>";
		echo "<br>";
	}

    while ($row = mysqli_fetch_array($result10))
    {
        $getSiteSettings[$row['setting']] = $row['data'];
    }
	echo "<form id = \"add2newsletter\">";
		echo "<input class = \form-control\" id = \"name\" required placeholder = \"".$addNewsletter[1]."\"><br><br>";
		echo "<input class = \form-control\" id = \"email\" required placeholder = \"".$addNewsletter[2]."\"><br><br>";
		echo "<button type = \"button\" class = \"btn btn-secondary btn-block\" id = \"submittNewsletter\" >".$addNewsletter[3]."</button><br><br>";
		echo "<input type = \"hidden\" id = \"lang\" value = \"".$replaceLang[reset($displayLang)]."\">";
	echo "</form>";

	echo "<input type = \"hidden\" id = \"error_text_newsletter\" value = \"".$addNewsletter[4]."\">";
?>