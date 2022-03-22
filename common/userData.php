<?php

//https://stackoverflow.com/a/834355 2021-01-14
function startswith( $haystack, $needle ) {
     $length = strlen( $needle );
     return substr( $haystack, 0, $length ) === $needle;
}

function endsWith($haystack, $needle) {
    return substr_compare($haystack, $needle, -strlen($needle)) === 0;
}

function checkTable($table = null, $always = true)
{
	global $link;
	global $link_k;
	
	if (!$always)
	{
		return true;
	}
	//Function for creating uniqe replacekeys in a table, safty first....
	if ((substr($table, 0,6) === "`lv_k_") || (substr($table, 0,5) === "lv_k_"))
	{
		$sql = "SELECT * FROM ".$table." WHERE tableKey IS NULL OR tableKey = ''";
        $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link_k));

		while ($row = mysqli_fetch_array($result))
		{
			$run = true;

			do
			{
				$tableKey = generateStrongPassword(15);
				$sql = "SELECT * FROM ".$table." WHERE tableKey = '".$tableKey."'";
				$result2= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link_k));

				if (mysqli_num_rows($result2) === 0)
				{
					$sql = "UPDATE ".$table ." SET tableKey = '".$tableKey."' WHERE autoId = '".$row['autoId']."'";
					$result2= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link_k));

					$run = false;
				}

			} while ($run);
		}
		
		return $tableKey;
	}
	else
	{
		$sql = "SELECT * FROM ".$table." WHERE tableKey IS NULL OR tableKey = ''";
		$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link));

		while ($row = mysqli_fetch_array($result))
		{
			$run = true;

			do
			{
                $tableKey = generateStrongPassword(14);

				$sql = "SELECT * FROM ".$table." WHERE tableKey = '".$tableKey."'";
				$result2= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link));

				if (mysqli_num_rows($result2) === 0)
				{
					$sql = "UPDATE ".$table ." SET tableKey = '".$tableKey."' WHERE autoId = '".$row['autoId']."'";

					$result2= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link));

					$run = false;
				}

			} while ($run);
		}
		
		return $tableKey;
	}
}


function getSiteSettings()
{
    global $link;
    global $phrase;
    
    global $getSiteSettings;
    if (!empty($getSiteSettings))
    {
        return $getSiteSettings;
    }
    
    $table = "`".PREFIX."site_settings`";
    $sql = "SELECT AES_DECRYPT(setting, SHA2('".$phrase."', 512)) as setting, AES_DECRYPT(data, SHA2('".$phrase."', 512)) as data FROM ".$table."";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

    while ($row = mysqli_fetch_array($result))
    {
        $getSiteSettings[$row['setting']] = $row['data'];
    }
    
    return $getSiteSettings;
}

function userModule($moduleKey = null)
{
    global $link;
    global $link_k;

    global $phrase;
    global $phrase_k;
    
    global $moduleData;
    
    global $coworker;
    global $accountPlan;
    global $servotabloPermission;
    
    if (empty($moduleKey))
    {
        return false;
    }
    
    if (empty ($moduleData))
    {
        $table = "`".PREFIX."administrado_menu`";
        $table2 = "`".PREFIX."servotablo_menu`";
        
        $table10 = "`".PREFIX."account`";
        $table11 = "`".PREFIX."user2account`";
        $table12 = "`".PREFIX."user`";
        
        $table20 = "`".PREFIX."servotablo_permission2user`";
        
        /*$sql = "SELECT * FROM ".$table12." t12 INNER JOIN ".$table11." t11 ON t11.userId = t12.autoId INNER JOIN ".$table10." t10 ON t11.accountId = t10.autoId WHERE userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result))
        {
            $coworker = $row['coworker'];
            if (!empty($accountPlan))
            {
                if ($accountPlan < $row['accountPlan'])
                {
                    $accountPlan = $row['accountPlan'];
                }
            }
            else
            {
                $accountPlan = $row['accountPlan'];
            }
        }*/
        
        /*$sql = "SELECT * FROM ".$table20." WHERE userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result))
        {
            $servotabloPermission = (int)$row['permissionId'];
        }*/
        
        $sql = "SELECT * FROM ".$table;
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result))
        {
            $moduleData['administrado'][$row['tableKey']] = $row['accountPermission'];
        }
        
        /*$sql = "SELECT * FROM ".$table2;
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result))
        {
            $moduleData['servotablo'][$row['tableKey']] = $row['accountPermission'];
        }*/
    }
    
    if ($servotabloPermission > 100)
    {
        return (true);
    }
    
    foreach ($moduleData as $key => $value)
    {
        $minLevel = $value[$moduleKey];
        
        if ($key === "administrado")
        {
            if ($accountPlan >= $minLevel)
            {
                return true;
            }
            
        }
        else if ($key === "servotablo")
        {
            if ($servotabloPermission >= $minLevel)
            {
                return true;
            }
        }
    }
    
    return false;
}

function getUserSettings($userId = null)
{
    global $link;
    global $link_k;

    global $phrase;
    global $phrase_k;
    
    global $userSettings;
    
    if (empty($userSettings))
    {
        $table = "`".PREFIX."user_settings`";
    
        if (empty($userId))
        {
            $sql = "SELECT CAST(AES_DECRYPT(setting, SHA2('".$phrase."', 512)) AS CHAR) as setting, CAST(AES_DECRYPT(data, SHA2('".$phrase."', 512)) AS CHAR) as data FROM ".$table." WHERE userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."'";
        }
        else
        {
            $sql = "SELECT CAST(AES_DECRYPT(setting, SHA2('".$phrase."', 512)) AS CHAR) as setting, CAST(AES_DECRYPT(data, SHA2('".$phrase."', 512)) AS CHAR) as data FROM ".$table." WHERE userId = '".mysqli_real_escape_string($link, $userId)."'";
        }
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result))
        {
            $userSettings[$row['setting']] = $row['data'];
        }
    }

    return $userSettings;
}

function getPublicKeyProject($id = null, $type = null, $accountId = null)
{
    global $link;
    global $link_k;

    global $phrase;
    global $phrase_k;

    $table = "`".PREFIX."project_keys`";

    if (!empty($id))
    {
        if (!empty($accountId))
        {
            $sql = "SELECT * FROM ".$table ." WHERE projectId = '".$id."' AND accountId = '".$accountId."'";
        }
        else
        {
            $sql = "SELECT * FROM ".$table ." WHERE projectId = '".$id."' AND accountId = '".$_SESSION['accountId']."'";
        }
        
    }
    else if (!empty($type))
    {
        $sql = "SELECT * FROM ".$table ." WHERE projectType = '".$type."' AND accountId = '".$_SESSION['accountId']."' AND projectId IS NULL";
    }
    else if (empty($type))
    {
        $sql = "SELECT * FROM ".$table ." WHERE projectType = 'public' AND accountId = '".$_SESSION['accountId']."' AND projectId IS NULL";
    }
    else
    {
        return false;
    }
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

    while ($row = mysqli_fetch_array($result))
    {
        return $row['tableKey'];
    }
}

function getlangstrings($displayLang = null)
{
	global $link;
    global $link_k;
	
	global $phrase;
	global $phrase_k;
	
	$table = "`".PREFIX."translation`";
	
	$country = $settings['def_country'] = 'SE';
	
    $userSettings = getUserSettings();
    if (!empty($displayLang))
    {
        //Do nothing
    }
    else if (isset($_SESSION['uid']))
    {
        $userSettings = getUserSettings();

        $displayLang = array_map("trim", explode(",", $userSettings['langService']));
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

        if (isset($_SESSION['userLang']) && isset($_SESSION['uid']))
        {
            $temp[] = $_SESSION['userLang'];
        }
        
        $temp2 = array_filter(array_unique($displayLang));
		
		if (!empty($temp))
		{
			$displayLang = array_merge($temp, $temp2);
		}
		else
		{
			$displayLang = $temp2;
		}
        
    }
    else
    {    
        if (isset($_SESSION['userLang']) && !(isset($_SESSION['uid'])))
        {
            $temp[] = $_SESSION['userLang'];
            $temp2 = array_map("trim", explode(",", $userSettings['langService']));
            $displayLang = array_merge($temp, $temp2);
        }
        else
        {
             $displayLang  = array_map("trim", explode(",", $userSettings['langService']));
        }
    }
    $i = 0;
    
    if (isset($_SESSION['userLang']) && !isset($_SESSION['uid']))
    {
		
        $displayLang[] = $_SESSION['userLang'];
        $order[] = "WHEN lang = '".$_SESSION['userLang']."' THEN -1";
        $order_lang[] = "WHEN code = '".$$_SESSION['userLang']."' THEN -1";
        $i++;
    }
	else
	{
		$displayLang = array_map("trim", explode(",", $siteSettings['language']));
	}
	
    foreach ($displayLang as $key => $value)
    {
        $order[] = "WHEN lang = '".$value."' THEN ".$i;
        $order_lang[] = "WHEN code = '".$value."' THEN ".$i;
        $i++;
    }
    if (!isset($_SESSION['userLang']))
    {
        $_SESSION['userLang'] = reset($displayLang);
    }
    
	//We need all these sub-questions as the field with the language is also encrypted.
	
	$sql = "SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT CAST(AES_DECRYPT(lang, SHA2('".$phrase."',512)) AS CHAR) AS lang, CAST(AES_DECRYPT(variable, SHA2('".$phrase."',512)) AS CHAR) AS variable, CAST(AES_DECRYPT(arrayKey, SHA2('".$phrase."',512)) AS CHAR) AS arrayKey, CAST(AES_DECRYPT(note, SHA2('".$phrase."',512)) AS CHAR) AS note FROM $table) AS q ORDER BY variable, arrayKey LIMIT 18446744073709551615) as k ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 10 WHEN lang = 'de' THEN 11 WHEN lang = 'fr' THEN 12 WHEN lang = 'it' THEN 13 ELSE 100 END LIMIT 18446744073709551615) as lang GROUP BY variable, arrayKey";
    //echo __LINE__." ".$sql."<br>";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$data[$row['variable']][(int)$row['arrayKey']] = $row['note'];	
	}
	return $data;
}

function getLangstringsArray($variable = null, $langs = null)
{
	global $link;
    global $link_k;
	
	global $phrase;
	global $phrase_k;
	
	if (empty($variable) || empty($langs))
	{
		echo __LINE__." ".basename(__FILE__)." ERROR variable = ".$variable."<br>";
		return false;
	}
	
	foreach ($langs as $key => $value)
	{
		$where[] = "lang = '".$value."'";
		
		$orderBy[] = "WHEN lang = '".$value."' THEN ".$key;
	}
	
	$orderBy[] = "ELSE 100 END";
	
	
	$table = "`".PREFIX."translation`";
	
	//$sql = "SELECT * FROM (SELECT * FROM (SELECT CAST(AES_DECRYPT(lang, SHA2('".$phrase."',512)) AS CHAR) AS lang, CAST(AES_DECRYPT(variable, SHA2('".$phrase."',512)) AS CHAR) AS variable, CAST(AES_DECRYPT(arrayKey, SHA2('".$phrase."',512)) AS CHAR) AS arrayKey, CAST(AES_DECRYPT(note, SHA2('".$phrase."',512)) AS CHAR) AS note FROM $table HAVING variable = '".$variable."' AND ".implode(" OR ", $lang)." ORDER BY variable, arrayKey LIMIT 18446744073709551615) as k ORDER BY CASE WHEN lang = '".$lang."' THEN 1 WHEN lang = 'en' THEN 2 WHEN lang = 'de' THEN 3 WHEN lang = 'fr' THEN 4 WHEN lang = 'it' THEN 5 ELSE 10 END LIMIT 18446744073709551615) as lang GROUP BY variable, arrayKey";
	
	$sql = "SELECT * FROM (SELECT * FROM(SELECT CAST(AES_DECRYPT(lang, SHA2('".$phrase."',512)) AS CHAR) AS lang, CAST(AES_DECRYPT(variable, SHA2('".$phrase."',512)) AS CHAR) AS variable, CAST(AES_DECRYPT(arrayKey, SHA2('".$phrase."',512)) AS CHAR) AS arrayKey, CAST(AES_DECRYPT(note, SHA2('".$phrase."',512)) AS CHAR) AS note FROM $table WHERE variable = AES_ENCRYPT('".$variable."', SHA2('".$phrase."', 512))) as p WHERE (".implode(" OR ", (array)$where).") ORDER BY arrayKey LIMIT 18446744073709551615) as k ORDER BY CASE ".implode(" ", (array)$orderBy);
	//echo __LINE__." ".$sql."<br>";
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$data[$row['arrayKey']][$row['lang']] = $row['note'];
	}

	return $data;
}

function getReplaceLang($mode = true)
{
    global $link;
    global $link_k;
	
	global $phrase;
	global $phrase_k;
	
	$table = "`".PREFIX."languages`";
	
	$sql = "SELECT tableKey, code FROM ".$table;
    
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		if ($mode)
		{
			$replace[$row['code']] = $row['tableKey'];	
		}
		else
		{
			$replace[$row['tableKey']] = $row['code'];
		}
	}
    
    return $replace;
}

function getReplaceTranslationVar($mode = true)
{
    global $link;
    global $link_k;
	
	global $phrase;
	global $phrase_k;
	
	$table = "`".PREFIX."translation_var`";
	
	$sql = "SELECT tableKey, CAST(AES_DECRYPT(variable, SHA2('".$phrase."',512)) AS CHAR) AS variable FROM ".$table;
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		if ($mode)
		{
			$replace[$row['variable']] = $row['tableKey'];	
		}
		else
		{
			$replace[$row['tableKey']] = $row['variable'];
		}
	}
	
	return $replace;
}

function updateTranslationTable()
{
	global $link;
    global $link_k;
	
	global $phrase;
	global $phrase_k;
	
	$table = "`".PREFIX."translation`";
	$table2 = "`".PREFIX."translation_var`";
	
	//Extract existing variable from translation table
	
	$sql = "SELECT * FROM (SELECT CAST(AES_DECRYPT(variable, SHA2('".$phrase."',512)) AS CHAR) AS variable FROM $table LIMIT 18446744073709551615) AS lang1 GROUP BY lang1.variable ORDER BY lang1.variable";
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$masterVar[$row['variable']] = $row['variable'];
	}
	
	//Extract existing variable from translation replace table
	
	$sql = "SELECT CAST(AES_DECRYPT(variable, SHA2('".$phrase."',512)) AS CHAR) AS variable FROM ".$table2;
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$existingVar[$row['variable']] = $row['variable'];
	}
	
	//Get diff between the two tables
	
	if (is_array($existingVar))
	{
		$diff = array_diff($masterVar, $existingVar);
	}
	else
	{
		$diff = $masterVar;
	}
	
	//If nececery insert new vars
	
	if (is_array($diff))
	{
		foreach ($diff as $key => $value)
		{
			$sql = "INSERT INTO ".$table2." (variable) VALUES (AES_ENCRYPT('".$value."', SHA2('".$phrase."',512)))";
			$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
		}
		
		checkTable($table2);
	}
}


function updateReplaceTable($mode = true)
{
	global $link;
    global $link_k;
	
	if (!$mode)
	{
		return true;	
	}
	
	/*if (!empty($link_k))
	{
		$sql = "SHOW TABLES;";
        
        $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link_k));
		
		$resync = false;
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			foreach ($row as $key => $value)
			{
				if ("`".$value."`" !== PREFIX_K."replacetable")
				{
					$sql = "SELECT * FROM ".PREFIX_K."replacetable WHERE replaceTable = '".$value."'"; 
					$result2= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link_k));
					
					if (mysqli_num_rows($result2) === 0)
					{
						$sql = "INSERT INTO ".PREFIX_K."replacetable (replaceTable) VALUES ('".$value."')";
						$result2= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link_k));
						
						$resync = true;
					}
                    
				}
			}
            checkTable(PREFIX_K."replacetable");
        }
		
		if ($resync)
		{
			checkTable(PREFIX_K."replacetable");
		}
	}*/
		
	$sql = "SHOW TABLES;";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
    
	$resync = false;
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		foreach ($row as $key => $value)
		{
			if ("`".$value."`" !== PREFIX."replacetable")
			{
				$sql = "SELECT * FROM ".PREFIX."replacetable WHERE replacetable = '".$value."'"; 
                $result2= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
				
				if (mysqli_num_rows($result2) === 0)
				{
					$sql = "INSERT INTO ".PREFIX."replacetable (replaceTable) VALUES ('".$value."')";
                    $result2= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
					
					$resync = true;
                    //checkTable(PREFIX."replacetable");
				}
			}
		}
	}
	
	if ($resync)
	{
        echo __LINE__." ".__FILE__." ".$table."<br>";
        checkTable(PREFIX."replacetable");
	}
}

function getReplaceTable($mode = true)
{   
    global $link;
    global $link_k;
	
	//updateReplaceTable();
	if (defined('PREFIX_K'))
	{
		$table = "`".PREFIX_K."replacetable`";

		$sql = "SELECT * FROM ".$table;
        $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link_k));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if ($mode)
			{
				$data[$row['replaceTable']] = $row['tableKey'];
			}
			else
			{
				$data[$row['tableKey']] = $row['replaceTable'];
			}
		}
	}
    $table = "`".PREFIX."replacetable`";
    
    $sql = "SELECT * FROM ".$table;
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        if ($mode)
        {
            $data[$row['replaceTable']] = $row['tableKey'];
        }
        else
        {
            $data[$row['tableKey']] = $row['replaceTable'];
        }
    }
    
    return $data;  
}


function getCountryes()
{
	global $link;
	global $link_k;

	global $phrase;
	global $phrase_k;
	
	$table = "`".PREFIX."country`";
    
    $sql = "SELECT * FROM ".$table;
    //echo $sql."<br>";
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
		$country[$row['country_code']] = $row['country_name'];
	}
	
	return $country;
}

function getDisplayLang($mode = true) 
{
    global $link;
    global $link_k;

    global $phrase;
    global $phrase_k;
    
    $table = "`".PREFIX."translation`";
    
    $table10 = "`".PREFIX."languages`";
    
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
   

    $i = 0;
    
    $displayLang = array_map("trim", explode(",", $siteSettings['language']));
        
    foreach ($displayLang as $key => $value)
    {
        $order[] = "WHEN lang = '".$value."' THEN ".$i;
        $order_lang[] = "WHEN code = '".$value."' THEN ".$i;
        $i++;
    }
    
    $sql = "SELECT * FROM (SELECT CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang FROM ".$table.") as k GROUP BY lang";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));  
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $where_lang[] = "code = '".$row['lang']."'";
    }
   
    $sql = "SELECT * FROM ".$table10." WHERE (".implode(" OR ", (array)$where_lang).") ORDER BY CASE ".implode(" ", (array)$order_lang)." WHEN code = 'en' THEN 11 WHEN code = 'de' THEN 12 WHEN code = 'fr' THEN 13 WHEN code = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615";
    //echo __LINE__." ".$sql."<br>";
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
    
    while ($row = mysqli_fetch_array($result))
    {
        if ($mode)
        {
            $langs[$row['tableKey']] = $row['Local language name'];
        }
        else
        {
            $langs[$row['tableKey']] = $row['Code'];
        }
    }
    
    return $langs;
}

/*function verifyMasterUser($accountId = null)
{
    global $link;
    global $link_k;

    global $phrase;
    global $phrase_k;
    
    global $masterUser;

    if (!empty($masterUser))
    {
        $table = "`".PREFIX."project_keys`";

        $table10 = "`".PREFIX."user2account`";

        $sql = "SELECT * FROM ".$table10." WHERE accountId = '".$accountId."' AND userId = '".$_SESSION['uid']."'";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
        if (mysqli_num_rows($result) > 0)
        {
            $masterUser = true; 
        }
        else
        {
            $masterUser = false; 
        }
    }

    return $masterUser;
}

function verifyTableKey($accountId = null)
{
    global $link;
    global $link_k;

    global $phrase;
    global $phrase_k;

    global $statusUniqeLink;

    $displayLang[] = 'sv';
    $displayLang[] = 'en'; 

    $table = "`".PREFIX."project_keys`";

    $table10 = "`".PREFIX."user2account`";

    if (!isset($statusUniqeLink))
    {
        if (isset($_SESSION['uid']))
        {
            $sql = "SELECT * FROM ".$table10." WHERE accountId = '".$accountId."' AND userId = '".$_SESSION['uid']."'";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
            if (mysqli_num_rows($result) > 0)
            {
                $statusUniqeLink = true; 
                return $statusUniqeLink;
            }
            else
            {
                $statusUniqeLink = false; 
            }
        }

        $sql = "SELECT * FROM ".$table." WHERE accountId = '".$accountId."' AND tableKey = '".mysqli_real_escape_string($link, $_GET['tableKey'])."'";
        //echo $sql."<br>";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_array($result))
            {
                if (empty($row['projectId']))
                {
                   $statusUniqeLink = true; 
                }
            }
        }
        else
        {
            $statusUniqeLink = false; 
        }
    }

    return $statusUniqeLink;
}

function checkPermission($accountId = null, $minAccount = null)
{
    global $statusUniqeLink;
    
    global $masterUser;
    
    if (empty($masterUser))
    {
        verifyMasterUser($accountId);  
    }
    if ($masterUser)
    {
        return true;
    }
    
    if (empty($statusUniqeLink))
    {
        verifyTableKey($accountId);
    }
    
    $table30 = "`".PREFIX."account_plans`";
        
    $table40 = "`".PREFIX."default_users`";
    $table41 = "`".PREFIX."default_users_lang`";
    $table42 = "`".PREFIX."default_users2account_plan`";
    
    $sql = "SELECT *, t40.tableKey as tableKey  FROM ".$table42." t32 INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT defaultUserId, CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note, CAST(AES_DECRYPT(noteDefaultUsers, SHA2('".$phrase."', 512)) AS CHAR) as noteDefaultUsers FROM ".$table41.") as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 1844674407370955161) as t GROUP BY defaultUserId) as t31 ON t32.defaultUserId = t31.defaultUserId INNER JOIN ".$table40." t40 ON t40.defaultUserId = t32.defaultUserId WHERE accountPlan = '".$accountPlan."'";
        //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

    while ($row = mysqli_fetch_array($result))
    {
        $defaultAccountI[$row['defaultUserId']] = $row['tableKey'];	
    }
        
    
    //$sql = 
    
    if ($masterUser)
    {
        return true;
    }
}*/

//updateReplaceTable();

?>