<?php
session_start();
include_once("../../../common/db.php");
include_once("./../../ext/theme/nav.php");

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
{
    $replaceTable = getReplaceTable(false);
    if (strcmp(substr($replaceTable[$_POST['replaceTable']], 0, strlen(PREFIX_K."dokumenti_")), PREFIX_K."dokumenti_") === 0 && endsWith($replaceTable[$_POST['replaceTable']], "default_field_text"))
    {
        showAjaxDefaultFieldText();
    }
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'account')
    {
        showAjaxAccount();
        
    }
    
}

function showAjaxDefaultFieldText()
{
    global $link;
	global $link_k;

	global $phrase;
	global $phrase_k;
    
    $replaceTable = getReplaceTable();
    
    /*$replaceLang = getReplaceLang();
    
    /*$showAjaxDefaultFieldText_array['en'] = "Standard text";
    $showAjaxDefaultFieldText_array['sv'] = "Standard text";*/
    
   /* $userSettings = getUserSettings();

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

    foreach ($displayLang as $key => $value)
    {
        $order[] = "WHEN lang = '".$value."' THEN ".$key;
    }
    */
    $i = 0;

    
    
    $table2 = "`".PREFIX_K."dokumenti_settings`";
    
    $sql = "SELECT *, CAST(AES_DECRYPT(setting, SHA2('".$phrase_k."', 512)) AS CHAR) as setting,
					  CAST(AES_DECRYPT(data, SHA2('".$phrase_k."', 512)) AS CHAR) as data
				FROM ".$table2 ." WHERE projectId IS NULL OR projectId = 0";
	//echo $sql ."<br>";
	$result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
	
	while ($row = mysqli_fetch_array($result))
	{
		${$row['setting']} = $row['data'];
	}
    
    $temp = array_map(trim, explode(",", $defaultDokumentiLang));
        
    foreach ($temp as $key => $value)
    {
        $displayLang[] = $value;
    }

    if (empty($showDoclang))
    {
        $showDoclang = reset($displayLang);
    }

    $replaceTable = getReplaceTable(false);
    
    $langStrings = getlangstrings();
    $showAjaxDefaultFieldText = $langStrings['showAjaxDefaultFieldText'];

    $showAjaxDefaultFieldText_array = getLangstringsArray('showAjaxDefaultFieldText_array', $displayLang);
 
    $table1 = $replaceTable[$_POST['replaceTable']];
    $table2 = $replaceTable[$_POST['replaceTable']]."_lang";
    
    $replaceTable = getReplaceTable();
    
    /*$sql = "SELECT * FROM (SELECT node.fieldTextId, node.tableKey, CAST(AES_DECRYPT(node.note, SHA2('".$phrase_k."', 512)) AS CHAR) as note, COUNT(parent.lft) - 1 as depth
            FROM ".$table1." AS node,
                    ".$table1." AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
                    GROUP BY node.lft
            ORDER BY node.lft) AS table1 LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM (SELECT fieldTextId, CAST(AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) AS CHAR) as lang, CAST(AES_DECRYPT(note, SHA2('".$phrase_k."', 512)) AS CHAR) as fieldNote FROM ".$table2.") as p ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END LIMIT 18446744073709551615 ) as q ) as table2 ON table1.fieldTextId = table2.fieldTextId WHERE table1.tableKey = '".mysqli_real_escape_string($link_k, $_POST['id'])."';";*/
    $sql = "SELECT * FROM (SELECT fieldTextId, CAST(AES_DECRYPT(note, SHA2('".$phrase_k."', 512)) AS CHAR) as note, tableKey FROM ".$table1.") AS table1 LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM (SELECT fieldTextId, tableKey as langReplaceKey, CAST(AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) AS CHAR) as lang, CAST(AES_DECRYPT(note, SHA2('".$phrase_k."', 512)) AS CHAR) as fieldNote FROM ".$table2.") as p ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END LIMIT 18446744073709551615 ) as q ) as table2 ON table1.fieldTextId = table2.fieldTextId WHERE table1.tableKey = '".mysqli_real_escape_string($link_k, $_POST['id'])."';";
    //echo $sql."<br>";
    $result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
    
    $first = true;
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $data[$row['lang']] = $row;
    }
    
    foreach ($displayLang as $key => $row)
    {
        if ($first)
        {
            echo "<h3 class = \"jeditable\" id = \"note[".$data[$row]['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX_K.'dokumenti_default_field_text']."\" data-reload_tree = \"tree_".$replaceTable[PREFIX_K.'dokumenti_default_field_text']."\" data-replace_project = \"".$_POST['id']."\">".$data[$row]['note']."</h3>";
            
            $first = false;
        }
        
        if (empty($row['langReplaceKey']))
        {
            $displayId = $_POST['id']."_".$replaceLang[$row];
        }
        else
        {
            $displayId = $row['langReplaceKey'];
        }
        
        echo "<div class=\"form-group row\">";
            echo "<label for=\"note[".$displayId."]\" class=\"col-sm-2 col-form-label\">".$showAjaxDefaultFieldText_array[1][$row]." [".$row."]</label>";
            echo "<div class=\"col-sm-10\">";
                echo "<textarea class = \"form-control syncData\" id = \"note[".$displayId."]\" data-replace_table = \"".$_POST['replaceTable']."\">";
                    echo $data[$row]['fieldNote'];
                echo "</textarea>";
            echo "</div>";
        echo "</div>";
    }
   
    //while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    foreach ($data as $key => $row)    
    {
        if (empty($key))
        {
            continue;
        }
        
        if (array_search($key, $displayLang) !== false)
        {
            continue;
        }
        
        if ($first)
        {
            $first = false;
            
            echo "<h3>".$row['note']."</h3>";
        }
        
        if (empty($row['tableKey']))
        {
            $displayId = $_POST['id']."_".$replaceLang[$row];
        }
        else
        {
            $displayId = $row['langReplaceKey'];
        }
        
        echo "<div class=\"form-group row\">";
            echo "<label for=\"note[".$displayId."]\" class=\"col-sm-2 col-form-label\">".$showAjaxDefaultFieldText_array[$row['lang']]." [".$key."]</label>";
            echo "<div class=\"col-sm-10\">";
                
        
                echo "<textarea class = \"form-control syncData\" id \"note[".$displayId."]\" data-replace_table = \"".$_POST['replaceTable']."\">";
                    echo $data[$row]['fieldNote'];
                echo "</textarea>";
            echo "</div>";
        echo "</div>";
    }
}

function showAjaxAccount()
{
    include_once("account.php");
    
    show_account_cooperation();
}

//Bara kvar för om vi eventuellt använder koden, men ganska troligen inte....

/*function showAjaxAccount_20210319()
{
	global $link;
	global $link_k;

	global $phrase;
	global $phrase_k;

	$table = "`".PREFIX_K."projects`";
	$table2 = "`".PREFIX_K."projects_settings`";
	$table3 = "`".PREFIX_K."projects_lang`";
    
    echo __LINE__." ".__FILE__." "."Varför kommer vi hit???<br>";

	$replaceTable = getReplaceTable();
	
	$showAjaxAccount[1]['sv'] = "Privata projekt";
	$showAjaxAccount[2]['sv'] = "Vid privata projekt, krävs att man kommer åt dessa via en uniklänk. De kommer alltså inte att presenteras på vår hemsida över offentliga projekt!";
	$showAjaxAccount[3]['sv'] = "Offentlig projekt";
	$showAjaxAccount[4]['sv'] = "Vid offentliga projekt, du medger att vi tipsar om dinna projekt direkt på vår hemsida. Besökarna kan alltså tag del av dessa och förhoppningsvis tag kontakt med dig!";
	$showAjaxAccount[5]['sv'] = "Hemliga projekt";
	$showAjaxAccount[6]['sv'] = "Vid hemliga projekt är det unika länkar för att komma in för att titta på dessa!";
	
	$showAjaxAccount[7]['sv'] = "Synligheten för alla projekt under denna typ : ";
	
	$showAjaxAccount[8]['sv'] = "Projekttyp : ";
	
	$projectTypes['private'] = 'private';
	$projectTypes['public'] = 'public';
	$projectTypes['secret'] = 'secret';
	
	$projectTypesText['private'][1] = 1;
	$projectTypesText['private'][2] = 2;
	
	$projectTypesText['public'][3] = 3;
	$projectTypesText['public'][4] = 4;
	
	$projectTypesText['secret'][5] = 5;
	$projectTypesText['secret'][6] = 6;
	
	
	$lang = $settings['def_lang'] = 'sv';
	
	$sql = "SELECT *, t3.tableKey as subTableKey, CAST(AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) AS CHAR) as lang, 
					  CAST(AES_DECRYPT(note, SHA2('".$phrase_k."', 512)) AS CHAR) as note,
					  CAST(AES_DECRYPT(description, SHA2('".$phrase_k."', 512)) AS CHAR) as description
				FROM $table t1 LEFT OUTER JOIN $table3 t3 ON t1.projectId = t3.projectId WHERE t1.tableKey = '".mysqli_real_escape_string($link_k, $_POST['id'])."' ORDER BY CASE WHEN lang = '".$lang."' THEN 1 WHEN lang = 'en' THEN 2 WHEN lang = 'de' THEN 3 
					  WHEN lang = 'fr' THEN 3 WHEN lang = 'it' THEN 5 ELSE 10 END ";
	//echo $sql ."<br>";
	$result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
	
	while ($row = mysqli_fetch_array($result))
	{
		echo "<h3>".$showAjaxAccount[8][$lang]." ".$row['note']."</h3>";
	}
	
	$sql = "SELECT *, CAST(AES_DECRYPT(setting, SHA2('".$phrase_k."', 512)) AS CHAR) as setting,
					  CAST(AES_DECRYPT(data, SHA2('".$phrase_k."', 512)) AS CHAR) as data
				FROM $table t1 LEFT OUTER JOIN $table2 t2 ON t1.projectId = t2.projectId WHERE t1.tableKey = '".mysqli_real_escape_string($link_k, $_POST['id'])."'";
	//echo $sql ."<br>";
	$result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
	
	while ($row = mysqli_fetch_array($result))
	{
		$projectSettings[$row['setting']] = $row['data'];
	}
	
	echo $showAjaxAccount[7][$lang]."<br><br>";
	
	foreach ($projectTypes as $key => $value)
	{
		echo "<div class=\"form-check\">";
          echo "<input class=\"form-check-input syncData\" class = \"\" type=\"radio\" name=\"projectType[".$_POST['id']."]\" id=\"projectType[".$_POST['id']."]\" value=\"".$key."\"  ";
			if ($key == $projectSettings['projectType'])
			{
				echo "checked";
			}
			echo ">";
          echo "<label class=\"form-check-label\">";
			$first = true;
            foreach ($projectTypesText[$key] as $key2 =>  $value2)
			{
				if ($first)
				{
					$first = false;
					echo $showAjaxAccount[$value2][$lang]."<br>";
				}
				else
				{
					echo "<small>".$showAjaxAccount[$value2][$lang]."</small>";
				}
			}
          echo "</label>";
        echo "</div>";
	}
	
	
	echo "<input class = \"syncData\" type = \"hidden\" id = \"replaceTable\" value = \"".$replaceTable[PREFIX_K.'projects_settings']."\">";
}*/
	

?>