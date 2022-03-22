<?php
    session_start();

    error_reporting(E_ALL);

    include_once("../../common/db.php");
    include_once("../../common/crypto.php");
    include_once("../../common/userData.php");
    //include_once("./../index.php");

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        $replaceTable = getReplaceTable(false);
        if ($replaceTable[$_POST['replaceTable']] === PREFIX.'translation')
        {
            //showAjaxTranslation();
            showAjax_translation();
        }
        else if ($_SESSION['tempTableKey'] == $_POST['replaceTable'])
		{
            showAjax_tableOther();
		}
    }

function showAjaxTranslation()
{
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

    $langStrings = getlangstrings();
    $showAjaxAdministrado_menu = $langStrings['showAjaxAdministrado_menu'];

    $showAjaxAdministrado_menu_array = getLangstringsArray('showAjaxAdministrado_menu_array', $displayLang);

    $table = "`".PREFIX."translation`";
    $table2 = "`".PREFIX."translation_lang`";

    $table10 = "`".PREFIX."translation_var`";

    $sql = "SELECT *, CAST(AES_DECRYPT(variable, SHA2('".$phrase."', 512)) as CHAR) as variable FROM $table WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

    $sql = "SELECT * FROM (SELECT node.menuId, node.lft, node.rgt, node.tableKey as masterTableKey, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) as char) as folder, CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) as char) as file, CAST(AES_DECRYPT(node.icon, SHA2('".$phrase."', 512)) as char) as icon, COUNT(parent.lft) - 1 as depth
    FROM (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS node,
          (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS parent
    WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
    ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, tableKey, 
          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
          ) AS lang ON menu.menuId = lang.menuId GROUP BY lang.lang ORDER BY menu.lft";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

    $numRows = mysqli_num_rows($result);

    $showAjaxAdministrado_menu[1] = "Katalog";
    $showAjaxAdministrado_menu[2] = "Fil";

    $showAjaxAdministrado_menu_array[1]['sv'] = "Meny namn";

    $first = true;

    $i = 1;

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        echo "<div class=\"form-group row\">";
            echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxAdministrado_menu_array[1][$row['lang']]." ";
            if ($numRows > 1)
            {
                echo "[".$row['lang']."]";
            }
            echo "</label>";
            echo "<div class=\"col-sm-10\">";
                echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"note[".$row['tableKey']."]\" value = \"".$row['note']."\"";
                if ($first)
                {
                    $first = false;

                    echo " "."data-reload_tree = \"tree_".$_POST['replaceTable']."\"";
                }
                echo ">";
            echo "</div>";
        echo "</div>";

        if ($i == $numRows)
        {
            echo "<div class=\"form-group row\">";
                echo "<label for=\"folder[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxAdministrado_menu[1]." ";

                echo "</label>";
                echo "<div class=\"col-sm-10\">";
                    echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"folder[".$row['masterTableKey']."]\" value = \"".$row['folder']."\"";

                    echo ">";
                echo "</div>";
            echo "</div>";

            echo "<div class=\"form-group row\">";
                echo "<label for=\"file[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxAdministrado_menu[2]." ";
                echo "</label>";
                echo "<div class=\"col-sm-10\">";
                    echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"file[".$row['masterTableKey']."]\" value = \"".$row['file']."\"";
                   echo ">";
                echo "</div>";
            echo "</div>";
        }
    }
}

function validateReplaceKey($temp = null, $key = null)
{
	if (empty($temp) || empty($key))
	{
		return false;
	}
	
	if (in_array($temp, $_SESSION['replaceTempKey']) )
	{
		return true;
	}
	else
	{
		$_SESSION['replaceTempKey'][$key] = $temp;
		session_write_close();
		return false;
	}
}

function createReplaceKey($key)
{
	$run = true;
	
	if (array_key_exists($key, (array)$_SESSION['replaceTempKey']))
	{
		return $_SESSION['replaceTempKey'][$key] ;
	}
	
	do {
		
		$temp = generateStrongPassword();
		$run = validateReplaceKey($temp, $key);
		
	} while ($run);
	
	return $temp;
}


function renderTranslationTable($dbData, $tableName = 'translation', $fields = array("note" => "note"), $masterField = null)
{
    global $link;
    global $phrase;
    
	if (empty($dbData))
	{
		//return false;
	}
	
	$lang = $settings['def_lang'] = 'sv';
	
	$table = "`".PREFIX."translation`";
	$table2 = "`".PREFIX."translation_var`";

    $table10 = "`".PREFIX."languages`";
    
    $userSettings = getUserSettings();
    
    if (isset($_POST['primary']))
    {
        unset($displayLang);
        $where[] = "tableKey = '".mysqli_real_escape_string($link, $_POST['primary'])."'";
        $where[] = "tableKey = '".mysqli_real_escape_string($link, $_POST['secondary'])."'";
        
        foreach ($where as $key => $value)
        {
            $orderBy[] = "WHEN tableKey = '".str_replace("tableKey = '",'',$value)." THEN ".$key;
        }
        
        $sql = "SELECT * FROM ".$table10." WHERE ".implode(" OR ", (array)$where)." ORDER BY CASE ".implode(" ", (array)$orderBy)." END";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        if (mysqli_num_rows($result) > 0)
        {
            unset ($where, $orderBy);
            while ($row = mysqli_fetch_array($result))
            {
              $displayLang[] = $row['Code'];  
            }
        }
        else
        {    
            return false;
        }
    }
    else if (empty($userSettings['langService']))
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

        $displayLang = array_map("trim", explode(",", $siteSettings['language']));

        foreach ($displayLang as $key => $value)
        {
            $order[] = "WHEN lang = '".$value."' THEN ".$i;
            $order_lang[] = "WHEN code = '".$value."' THEN ".$i;
            $i++;
        }
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
	$replacVar = getReplaceTranslationVar();
	$replaceLang = getReplaceLang();
	
	$langString = getlangstrings($displayLang);
		
	$renderTranslationTable = $langString['renderTranslationTable'];
	
	//If the page should present the corresponding variable name of the strings
	
	if (array_key_exists('variable', (array)$fields))
	{
		$showTH['var'] =  $renderTranslationTable['1'];
	}
	
	$showTH['note1'] = $renderTranslationTable['2'];
    //if (isset($displayLang[1]))
    {
        $showTH['note2'] = $renderTranslationTable['3'];
    }
	
	foreach ($fields as $key => $value)
	{
		$displayField[$key][1] = $key."1";
		$displayField[$key][2] = $key."2";
	}
	
	echo "<table id=\"table_translation_length\" class=\"table table-striped table-bordered DataTable\" style=\"width:100%\">";
		
		if ($tableName === "all")
		{
			$tableName = "translation";	
		}
	
		echo "<thead>";
			echo "<tr>";	
				foreach ($showTH as $key => $value)
				{
					echo "<th>";
						echo $value;
						if ($key === "note1")
						{
							echo "[".$displayLang[0]."]";
						}
						elseif ($key === "note2" && isset($displayLang[1]))
						{
							echo "[".$displayLang[1]."]";
						}
					echo "</th>";
				}
			echo "</tr>";
		echo "</thead>";

		echo "<tbody>";

			foreach ($dbData as $key => $value)
			{
				foreach ($displayField as $disp_key => $disp_value)
				{
					if ($disp_key === "variable" || $disp_key === "arrayKey" )
					{
						continue;
					}
					
					$k = 0;
					
					echo "<tr>";
						if (array_key_exists("variable", (array)$fields))
						{
							echo "<td>";
								if (!empty($value['variable1']))
								{
									echo $value['variable1'];
								}
								else
								{
									echo $value['variable2'];
								}
								echo "[";

								if (!empty($value['arrayKey1']))
								{
									echo $value['arrayKey1'];
								}
								else
								{
									echo $value['arrayKey2'];
								}
								echo "]";
							echo "</td>";
						}
					
						foreach ($disp_value as $sub_key => $sub_value)
						{
							//Extract fieldname, used by syncdata.php and corespondig integer
							$field = preg_replace('/[0-9]+/', '', $sub_value);
							preg_match_all('!\d+!', $sub_key, $matches);
							$int = implode('',(array)$matches[0]);

							//Special for untranslated strings, these do not have their own tablekey in the table ...
                            if (empty($value[$sub_value]) && empty($value['tableKey'+$k]))
							//if (empty($value[$sub_value]))
							{
								//echo "<td class = \"jeditable\" id = \"".$field."[".$replaceTable[PREFIX.$tableName]."_".createReplaceKey($value[$masterField])."]\" data-replace_table = \"".$replaceTable[PREFIX.$tableName]."\" data-replace_lang = \"".$replaceLang[$displayLang[($int-1)]]."\">";
                                echo "<td class = \"jeditable\" id = \"".$field."[".$replaceTable[PREFIX.$tableName]."_".$replaceLang[$displayLang[($int-1)]]."]\" data-replace_table = \"".$replaceTable[PREFIX.$tableName]."\" data-replace_lang = \"".$replaceLang[$displayLang[($int-1)]]."\">";
							}
							else
							{
								echo "<td class = \"jeditable\" id = \"".$field."[".$value['tableKey'.$int]."]\" data-replace_table = \"".$replaceTable[PREFIX.$tableName]."\" data-replace_lang = \"".$replaceLang[$displayLang[($int-1)]]."\">";
								echo $value[$sub_value];
							}

							echo "</td>";
							$k++;
						}
					echo "</tr>";
				}
			}

		echo "</tbody>";
	echo "</table><br>";
}


function findFields($table = null, $type = "default")
{
	global $link;
	global $link_k;

	global $phrase;
	global $phrase_k;

	$sql = "DESCRIBE ".$table;

    //echo __LINE__." ".$sql."<br>";
    
	if ($type =="default")
	{
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	}
	else if ($type === "customer")
	{
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	}

	$validInt['TINYINT'] = "TINYINT";
	$validInt['SMALLINT'] = "SMALLINT";
	$validInt['MEDIUMINT'] = "MEDIUMINT";
	$validInt['INT'] = "INT";
	$validInt['BIGINT'] = "BIGINT";

	$blockArray['autoId'] = "autoId";
	$blockArray['tableKey'] = "tableKey";
    //$blockArray['settingId'] = "settingId";
	//$blockArray['lang'] = "lang";
	
	$block = false;

	while ($row = mysqli_fetch_array($result))
	{
		if (array_key_exists($row['Field'], (array)$blockArray))
		{
			continue;
		}
		
		if ($row['Field'] !== "autoId" && !$block)
		{
			foreach ($validInt as $key => $value)
			{
				if (strpos(strtolower($row['Type']), strtolower($key)) !== false) 
				{
					$field['master'] = $row['Field'];
					$block = true;
				}
				else
				{
					$field[$row['Field']] = $row['Field'];
				}
			}
		}
		else
		{
			$field[$row['Field']] = $row['Field'];
		}
	}

	return $field;
}


function showAjax_translation()
{
	global $link;
	global $link_k;

	global $phrase;
	global $phrase_k;

	$lang = $settings['def_lang'] = 'sv';
	
	$table = "`".PREFIX."translation`";
	$table2 = "`".PREFIX."translation_var`";
    
    $table10 = "`".PREFIX."languages`";
	
    $userSettings = getUserSettings();

    $displayLang = array_map("trim", explode(",", $userSettings['langService']));

    if (isset($_POST['primary']))
    {
        unset($displayLang);
        
        $where[] = "tableKey = '".mysqli_real_escape_string($link, $_POST['primary'])."'";
        $where[] = "tableKey = '".mysqli_real_escape_string($link, $_POST['secondary'])."'";
        
        foreach ($where as $key => $value)
        {
            $orderBy[] = "WHEN tableKey = '".str_replace("tableKey = '",'',$value)." THEN ".$key;
        }
        
        $sql = "SELECT * FROM ".$table10." WHERE ".implode(" OR ", (array)$where)." ORDER BY CASE ".implode(" ", (array)$orderBy)." END";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        if (mysqli_num_rows($result) > 0)
        {
            unset ($where, $orderBy);
            while ($row = mysqli_fetch_array($result))
            {
              $displayLang[] = $row['Code'];  
            }
        }
        else
        {    
            echo __LINE__." Hmmmmm<br>";
            return false;
        }
    }
    
    else if (empty($userSettings['langService']))
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

    $langStrings = getlangstrings($displayLang);
    $showAjax_translation = $langStrings['showAjax_translation'];

    $showAjax_translationLang = getLangstringsArray('showAjax_translationLang', $displayLang);
    
    $showAjax_translation[1] = "Addera översättning";
    $showAjax_translation[2] = "Addera";
    
	$replaceTable = getReplaceTable();
	$replacVar = getReplaceTranslationVar();
	$replaceLang = getReplaceLang();
	
    checkTable($table);
    checkTable($table2);
		
	$fields = findFields($table, "default");
	
	$field = $fields['master'];	
	
	unset($fields['master']);
	unset($fields[$field]);
		
    $displayLang = array_values($displayLang);
    
	foreach ($displayLang as $key => $value)
	{
		$where[] = "lang = AES_ENCRYPT('".$value."', SHA2('".$phrase."',512))";
		
		$orderBy[] = "WHEN lang = '".$value."' THEN ".$key;
	}
	
	foreach ($fields as $key => $value)
	{
		$select1[] = "CAST(AES_DECRYPT(".$key.", SHA2('".$phrase."',512)) AS CHAR) AS ".$key."1";
		$select2[] = "CAST(AES_DECRYPT(".$key.", SHA2('".$phrase."',512)) AS CHAR) AS ".$key."2";
	}
	
	unset($fields['lang']);
	
	$sql = "SELECT CAST(AES_DECRYPT(variable, SHA2('".$phrase."',512)) AS CHAR) AS variable FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
	//echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	if (mysqli_num_rows($result) > 0)
	{
		while ($row = mysqli_fetch_array($result))
		{
			$variable = $row['variable'];
		}
	}
	else
	{
		$sql = "SELECT CAST(AES_DECRYPT(variable, SHA2('".$phrase."',512)) AS CHAR) AS variable FROM ".$table2." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		if (mysqli_num_rows($result) > 0)
		{
			while ($row = mysqli_fetch_array($result))
			{
				$variable = $row['variable'];
			}
		}
	}
	
    $sql = "SELECT * , lang1.variable1 as variable FROM (SELECT * FROM (SELECT tableKey as tableKey1, ".implode(", ",(array)$select1)." FROM $table WHERE variable = AES_ENCRYPT('".$variable."', SHA2('".$phrase."',512))  ) as p1 WHERE lang1 = '".$displayLang[0]."' LIMIT 18446744073709551615) AS lang1 
		RIGHT OUTER JOIN (SELECT tableKey as tableKey2, ".implode(", ",(array)$select2)." FROM $table WHERE variable = AES_ENCRYPT('".$variable."', SHA2('".$phrase."',512)) HAVING lang2 = '".$displayLang[1]."' LIMIT 18446744073709551615) AS lang2 
			ON lang1.variable1 = lang2.variable2 AND lang1.arrayKey1 = lang2.arrayKey2 ORDER BY lang1.arrayKey1";
    //echo __LINE__." ".$sql."<br>";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	if (mysqli_num_rows($result) > 0)
	{
		while ($row = mysqli_fetch_array($result))
		{
			$dbData[] = $row;
		}
		//if (!empty($dbData))
		{
			renderTranslationTable($dbData, "translation", $fields, $variable);
		}
	}
	else
	{
        $sql = "SELECT *, lang1.variable1 as variable FROM (SELECT tableKey as tableKey1, ".implode(", ",(array)$select1)." FROM $table WHERE variable = AES_ENCRYPT('".$variable."', SHA2('".$phrase."',512)) LIMIT 18446744073709551615) AS lang1 ORDER BY lang1.arrayKey1";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_array($result))
            {
                $dbData[] = $row;
            }
            //if (!empty($dbData))
            {
                //echo __LINE__." Bingo???<br>";
                
                renderTranslationTable($dbData, "translation", $fields, $variable);
            }
	   }
        else
        {
            renderTranslationTable($dbData, "translation", $fields, $variable);
        }
		
	}
	
	echo $showAjax_translation['4']."<br><br>";
	
    if (basename($_SERVER['SCRIPT_FILENAME']) !== "ajaxResyncTable.php")
    {
        echo "<form id = \"formTranslationTree\" class = \"needs-validation\" novalidate>";

            if (is_array($showAjax_translationLang))
            {
                foreach ($showAjax_translationLang as $master_key => $master_value)
                {
                    foreach ($master_value as $key => $value)
                    {
                        echo "<div class=\"form-group row\">";
                            echo "<label for=\"note[".$key."]\" class=\"col-sm-2 col-form-label\">".$value."[".$key."]</label>";
                            echo "<div class=\"col-sm-10\">";
                                echo "<textarea class=\"form-control\" id=\"note[".$key."]\" required></textarea>";
                                echo "<div id = warning_note[".$key."] class = \"text-warning\" style = \"display : none\">";
                                    echo $showAjax_translation['6'];
                                echo "</div>";
                            echo "</div>";
                        echo "</div>";
                    }
                }
            }
            else
            {
                echo "<div class=\"form-group row\">";
                    echo "<label for=\"note[".$key."]\" class=\"col-sm-2 col-form-label\">".$showAjax_translation['1']."</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<textarea class=\"form-control\" id=\"note[".$key."]\" required></textarea>";
                        echo "<div id = warning_note[".$key."] class = \"text-warning\" style = \"display : none\">";
                            echo $showAjax_translation['6'];
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
            }

            echo "<button type = \"submit\" class = \"btn btn-secondary addTranslation\" data-target_form = \"formTranslationTree\" data-replace_table = \"".$replaceTable[PREFIX.'translation']."\" data-replace_var = \"".$_POST['id']."\" data-reload_table = \"table_translation_length\">".$showAjax_translation['2']."</buton>";
        echo "</form>";

        echo "<input type = \"hidden\" id = \"replaceTable\" value = \"".$replaceTable[PREFIX.'translation']."\">";
    }
	
}

function showAjax_tableAll()
{
    global $link;
    global $link_k;

    global $phrase;
    global $phrase_k;

    $table10 = "`".PREFIX."languages`";
    
    $userSettings = getUserSettings();

    $displayLang = array_map("trim", explode(",", $userSettings['langService']));

    if (isset($_POST['primary']))
    {
        unset($displayLang);
        
        $where[] = "tableKey = '".mysqli_real_escape_string($link, $_POST['primary'])."'";
        $where[] = "tableKey = '".mysqli_real_escape_string($link, $_POST['secondary'])."'";
        
        foreach ($where as $key => $value)
        {
            $orderBy[] = "WHEN tableKey = '".str_replace("tableKey = '",'',$value)." THEN ".$key;
        }
        
        $sql = "SELECT * FROM ".$table10." WHERE ".implode(" OR ", (array)$where)." ORDER BY CASE ".implode(" ", $orderBy)." END";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        if (mysqli_num_rows($result) > 0)
        {
            unset ($where, $orderBy);
            while ($row = mysqli_fetch_array($result))
            {
              $displayLang[] = $row['Code'];  
            }
        }
        else
        {    
            echo __LINE__." Hmmmmm<br>";
            return false;
        }
    }
    
    elseif (empty($userSettings['langService']))
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
    $showTranslationTree = $langStrings['showTranslationTree'];

    $showTranslationTree_array = getLangstringsArray('showTranslationTree_array', $displayLang);

    $table = "`".PREFIX."translation`";
    $table2 = "`".PREFIX."competence_lang`";

    $tableX = "`".PREFIX."languages`";

    checkTable($tableX);

    $replaceTable = getReplaceTable();

    $sql = "SELECT * FROM (SELECT tableKey as tableKey1, CAST(AES_DECRYPT(lang, SHA2('".$phrase."',512)) AS CHAR) AS lang1, CAST(AES_DECRYPT(variable, SHA2('".$phrase."',512)) AS CHAR) AS variable1, CAST(AES_DECRYPT(arrayKey, SHA2('".$phrase."',512)) AS CHAR) AS arrayKey1, CAST(AES_DECRYPT(note, SHA2('".$phrase."',512)) AS CHAR) AS note1 FROM $table  HAVING lang1 = '".$displayLang[0]."' LIMIT 18446744073709551615) AS lang1 
        RIGHT OUTER JOIN (SELECT tableKey as tableKey2, CAST(AES_DECRYPT(lang, SHA2('".$phrase."',512)) AS CHAR) AS lang2, CAST(AES_DECRYPT(variable, SHA2('".$phrase."',512)) AS CHAR) AS variable2, CAST(AES_DECRYPT(arrayKey, SHA2('".$phrase."',512)) AS CHAR) AS arrayKey2, CAST(AES_DECRYPT(note, SHA2('".$phrase."',512)) AS CHAR) AS note2 FROM $table HAVING lang2 = '".$displayLang[1]."' LIMIT 18446744073709551615) AS lang2 
            ON lang1.variable1 = lang2.variable2 AND lang1.arrayKey1 = lang2.arrayKey2 ORDER BY lang1.variable1, lang1.arrayKey1";
    echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

    while ($row = mysqli_fetch_array($result))
    {
        $dbData[] = $row;
    }

    renderTranslationTable($dbData,"all");
}

function showAjax_tableOther()
{
	global $link;
	global $link_k;

	global $phrase;
	global $phrase_k;

	$lang = $settings['def_lang'] = 'sv';
    
    $userSettings = getUserSettings();

    $table10 = "`".PREFIX."languages`";
    
    if (isset($_POST['primary']))
    {
        unset($displayLang);
        
        $where[] = "tableKey = '".mysqli_real_escape_string($link, $_POST['primary'])."'";
        $where[] = "tableKey = '".mysqli_real_escape_string($link, $_POST['secondary'])."'";
        
        foreach ($where as $key => $value)
        {
            $orderBy[] = "WHEN tableKey = '".str_replace("tableKey = '",'',$value)." THEN ".$key;
        }
        
        $sql = "SELECT * FROM ".$table10." WHERE ".implode(" OR ", (array)$where)." ORDER BY CASE ".implode(" ", (array)$orderBy)." END";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        if (mysqli_num_rows($result) > 0)
        {
            unset ($where, $orderBy);
            while ($row = mysqli_fetch_array($result))
            {
              $displayLang[] = $row['Code'];  
            }
        }
        else
        {    
            return false;
        }
    }
    
    else if (empty($userSettings['langService']))
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

        $displayLang = array_map("trim", explode(",", $siteSettings['language']));

        foreach ($displayLang as $key => $value)
        {
            $order[] = "WHEN lang = '".$value."' THEN ".$i;
            $order_lang[] = "WHEN code = '".$value."' THEN ".$i;
            $i++;
        }
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

    $langStrings = getlangstrings($displayLang);
    $showAjax_tableOther = $langStrings['showAjax_tableOther'];

    $showAjax_tableOther_array = getLangstringsArray('showAjax_tableOther_array', $displayLang);
	
	$getReplaceTable = getReplaceTable(false);
	$replacVar = getReplaceTranslationVar();
	$replaceLang = getReplaceLang();
	
	$table = "`".$getReplaceTable[mysqli_real_escape_string($link, $_POST['id'])]."`";
	$table2 = "`".str_replace("_lang", "", $getReplaceTable[mysqli_real_escape_string($link, $_POST['id'])])."`";
    
    checkTable($table);
    checkTable($table2);
	
	$fields = findFields($table, "default");
	$field = $fields['master'];	
	
	unset($fields['master']);
	
	unset($fields[$field]);
		
	foreach ($displayLang as $key => $value)
	{
		$where[] = "lang = AES_ENCRYPT('".$value."', SHA2('".$phrase."',512))";
		
		$orderBy[] = "WHEN lang = '".$value."' THEN ".$key;
	}
	
	foreach ($fields as $key => $value)
	{
		$select1[] = "CAST(AES_DECRYPT(".$key.", SHA2('".$phrase."',512)) AS CHAR) AS ".$key."1";
		$select2[] = "CAST(AES_DECRYPT(".$key.", SHA2('".$phrase."',512)) AS CHAR) AS ".$key."2";
	}
	
	unset($fields['lang']);
	
	/*$sql = "SELECT *, CASE WHEN tableKey1 IS NULL THEN t2.tableKey ELSE tableKey1 END AS tableKey1, CASE WHEN tablekey2 IS NULL THEN t2.tablekey ELSE tablekey2 END AS tableKey2, CASE WHEN lang1 IS NULL THEN '".$langArray[0]."' ELSE lang1 END AS lang1, CASE WHEN lang2 IS NULL THEN '".$langArray[1]."' ELSE lang2 END AS lang2 FROM (SELECT tableKey as tableKey1, ".$field.",  CAST(AES_DECRYPT(lang, SHA2('".$phrase."',512)) AS CHAR) AS lang1, CAST(AES_DECRYPT(note, SHA2('".$phrase."',512)) AS CHAR) AS note1 FROM $table WHERE lang = AES_ENCRYPT('".$langArray[0]."', SHA2('".$phrase."',512)) LIMIT 18446744073709551615) AS lang1 
		RIGHT JOIN (SELECT tableKey as tableKey2, ".$field.", CAST(AES_DECRYPT(lang, SHA2('".$phrase."',512)) AS CHAR) AS lang2, CAST(AES_DECRYPT(note, SHA2('".$phrase."',512)) AS CHAR) AS note2 FROM $table WHERE lang = AES_ENCRYPT('".$langArray[1]."', SHA2('".$phrase."',512))  LIMIT 18446744073709551615) AS lang2 
			ON lang1.".$field." = lang2.".$field." INNER JOIN $table2 as t2 ON t2.$field IN (lang1.".$field.", lang2.".$field.") ORDER BY t2.lft";
	*/
	$sql = "SELECT *, CASE WHEN tableKey1 IS NULL THEN t2.tableKey ELSE tableKey1 END AS tableKey1, CASE WHEN tablekey2 IS NULL THEN t2.tablekey ELSE tablekey2 END AS tableKey2, CASE WHEN lang1 IS NULL THEN '".$displayLang[0]."' ELSE lang1 END AS lang1, CASE WHEN lang2 IS NULL THEN '".$displayLang[1]."' ELSE lang2 END AS lang2 FROM (SELECT tableKey as tableKey1, ".$field.",  ".implode(",",(array)$select1)." FROM $table WHERE lang = AES_ENCRYPT('".$displayLang[0]."', SHA2('".$phrase."',512)) LIMIT 18446744073709551615) AS lang1 
		LEFT OUTER JOIN (SELECT tableKey as tableKey2, ".$field.", ".implode(",", (array)$select2)." FROM $table WHERE lang = AES_ENCRYPT('".$displayLang[1]."', SHA2('".$phrase."',512)) LIMIT 18446744073709551615) AS lang2 
			ON lang1.".$field." = lang2.".$field." INNER JOIN $table2 as t2 ON t2.$field IN (lang1.".$field.", lang2.".$field.") ORDER BY t2.lft";
//	echo __LINE__ ." ".$sql."<br>";
    
    if (count($displayLang <=1))
    {
        $sql = "SELECT * FROM (SELECT tableKey as tableKey1, ".$field.",  ".implode(",", (array)$select1)." FROM $table WHERE lang = AES_ENCRYPT('".$displayLang[0]."', SHA2('".$phrase."',512)) LIMIT 18446744073709551615) AS lang1 
		 ORDER BY ".$field;
	//echo __LINE__ ." ".$sql."<br>";
    }
    
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	while ($row = mysqli_fetch_array($result))
	{
		$dbData[] = $row;
	}
    
    renderTranslationTable($dbData, str_replace(PREFIX, "", str_replace("_lang", "", $getReplaceTable[mysqli_real_escape_string($link, $_POST['id'])])), $fields, $field);
}

?>