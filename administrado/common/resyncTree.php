<?php
    header('Content-Type: application/json');
	
	session_start();
	
	include_once("../../common/db.php");
    include_once("../../common/userData.php");
	include_once("../../administrado/ext/theme/nav.php");
	include_once("../../common/crypto.php");
	
    global $userReplaceKey;

	function retriveTree($sql = null, $mode = 'default', $field = null)
	{
		global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;

		$lang = $settings['def_lang'] = 'sv';

		$lang = 'sv';
		
		//echo $sql."<br>";
		
		if ($mode == 'default')
		{
			$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
		}
		else if ($mode == 'customer')
		{
			$result = mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
		}
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			foreach ($row as $key => $value)
			{
				if ($key !== 'autoId' && $key !== $field)
				{
					$data[$row[$field]][$key] = $value;
				}
				
			}
		}
		
		return $data;
	}

	function getTree_administrado_menu()
	{
		global $phrase;
		global $phrase_k;
		
		$table = PREFIX."administrado_menu";
		$table2 = PREFIX."administrado_menu_lang";
		
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
		
		$sql = "SELECT * FROM (SELECT node.*, (COUNT(parent.menuId) - 1) AS depth
				  FROM $table AS node,
						  $table AS parent
				  WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
				  ORDER BY node.lft LIMIT 18446744073709551615) as menu INNER JOIN 
				  (SELECT * FROM (SELECT menuId, 
						  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
						  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2 
						  ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
						  GROUP BY menuId) AS lang ON menu.menuId = lang.menuId ORDER BY menu.lft";
		
		return retriveTree($sql, 'default', 'menuId');
	}

        
	function getTree_menu()
	{
		global $phrase;
		global $phrase_k;
		
		$table = PREFIX."menu";
		$table2 = PREFIX."menu_lang";
		
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
		
		$sql = "SELECT * FROM (SELECT node.*, (COUNT(parent.menuId) - 1) AS depth
				  FROM $table AS node,
						  $table AS parent
				  WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
				  ORDER BY node.lft LIMIT 18446744073709551615) as menu INNER JOIN 
				  (SELECT * FROM (SELECT menuId, 
						  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
						  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2 
						  ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
						  GROUP BY menuId) AS lang ON menu.menuId = lang.menuId ORDER BY menu.lft";
		return retriveTree($sql, 'default', 'menuId');
	}

    function getTree_ref_objects()
    {
		global $phrase;
		global $phrase_k;
		
		$table = PREFIX."ref_objects";
		$table2 = PREFIX."ref_objects_lang";
		
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
		
		$sql = "SELECT * FROM (SELECT node.*, (COUNT(parent.referenceId) - 1) AS depth
				  FROM $table AS node,
						  $table AS parent
				  WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
				  ORDER BY node.lft LIMIT 18446744073709551615) as menu INNER JOIN 
				  (SELECT * FROM (SELECT referenceId, 
						  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
						  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2 
						  ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
						  GROUP BY referenceId) AS lang ON menu.referenceId  = lang.referenceId ORDER BY menu.lft";
		return retriveTree($sql, 'default', 'referenceId');
	}   

    function getTree_ref_types()
    {
		global $phrase;
		global $phrase_k;
		
		$table = PREFIX."ref_types";
		$table2 = PREFIX."ref_types_lang";
		
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
		
		$sql = "SELECT * FROM (SELECT node.*, (COUNT(parent.typeId ) - 1) AS depth
				  FROM $table AS node,
						  $table AS parent
				  WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
				  ORDER BY node.lft LIMIT 18446744073709551615) as menu INNER JOIN 
				  (SELECT * FROM (SELECT typeId , 
						  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
						  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2 
						  ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
						  GROUP BY typeId) AS lang ON menu.typeId  = lang.typeId ORDER BY menu.lft";
		return retriveTree($sql, 'default', 'typeId');
	}    
        
    function getTree_ref_settings()
    {
		global $phrase;
		global $phrase_k;
		
		$table = PREFIX."ref_settings";
		$table2 = PREFIX."ref_settings_lang";
		
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
		
		$sql = "SELECT * FROM (SELECT node.*, (COUNT(parent.settingId) - 1) AS depth
				  FROM $table AS node,
						  $table AS parent
				  WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
				  ORDER BY node.lft LIMIT 18446744073709551615) as menu INNER JOIN 
				  (SELECT * FROM (SELECT settingId, 
						  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
						  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2 
						  ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
						  GROUP BY settingId) AS lang ON menu.settingId  = lang.settingId ORDER BY menu.lft";
		return retriveTree($sql, 'default', 'settingId');
	}
        
    function getTree_ref_properties()
    {
		global $phrase;
		global $phrase_k;
		
		$table = PREFIX."ref_properties";
		$table2 = PREFIX."ref_properties_lang";
		
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
		
		$sql = "SELECT * FROM (SELECT node.*, (COUNT(parent.propertiesId) - 1) AS depth
				  FROM $table AS node,
						  $table AS parent
				  WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
				  ORDER BY node.lft LIMIT 18446744073709551615) as menu INNER JOIN 
				  (SELECT * FROM (SELECT propertiesId, 
						  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
						  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2 
						  ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
						  GROUP BY propertiesId) AS lang ON menu.propertiesId  = lang.propertiesId ORDER BY menu.lft";
		return retriveTree($sql, 'default', 'propertiesId');
	}    

    function getTree_ref_objects_image()
    {
        global $phrase;
		global $link;
        
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
		
		$table = PREFIX."ref_objects_image";
		$table2 = PREFIX."ref_objects_image_lang";
		
		if ($_POST['extraData'])
        {
            $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['extraData'])."'";
            //echo __LINE__." ".$sql."<br>";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
            
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $referenceId = $row['referenceId'];
            }
        }
        
        
		$sql = "SELECT * FROM (SELECT node.*, (COUNT(parent.imageId ) - 1) AS depth
				  FROM (SELECT * FROM $table WHERE referenceId = '".$referenceId."') AS node,
						  (SELECT * FROM $table WHERE referenceId = '".$referenceId."') AS parent
				  WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
				  ORDER BY node.lft LIMIT 18446744073709551615) as menu INNER JOIN 
				  (SELECT * FROM (SELECT imageId , 
						  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
						  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2 
						  ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
						  GROUP BY imageId ) AS lang ON menu.imageId = lang.imageId  ORDER BY menu.lft";
		
        //echo __LINE__." ".$sql."<br>";
        
        //print_r(retriveTree($sql, 'default', 'imageId'));
        return retriveTree($sql, 'default', 'imageId');
    }

    function getTree_menu_footer()
    {
        global $link;
        global $phrase;
        
        $table = "`".PREFIX."menu_footer`";
        $table2 = "`".PREFIX."menu_footer_lang`";
        $table10 = "`".PREFIX."menu_lang`";
        checkTable($table2);
        unset($data);

        $sql = "SELECT *,CASE WHEN masterLang.note2 IS NOT NULL THEN masterLang.note2 ELSE lang.note END as note, menu.menuId as menuId FROM (SELECT node.menuId, node.menuId2, node.masterMenuId, node.lft, node.rgt, node.tableKey, node.displayMenu, (COUNT(parent.lft) - 1) AS depth
        FROM $table AS node,
              $table AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              GROUP BY menuId) AS lang ON menu.menuId = lang.menuId LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note2 FROM $table10) as q2 ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t2 
              GROUP BY menuId) AS masterLang ON masterLang.menuId = menu.masterMenuId ORDER BY menu.lft";
        //print_r(retriveTree($sql, 'default', 'imageId'));
        //echo __LINE__." ".$sql."<br>";
        return retriveTree($sql, 'default', 'menuId2');
    }    
        
    function getTree_users()
	{
        global $link;
        
		global $phrase;
		
		$table = "`".PREFIX."users`";
        checkTable($table);
        unset($data);

        $sql = "SELECT *, CONCAT(k.firstName, ' ', k.sureName) as note FROM (SELECT CAST(AES_DECRYPT(firstName, SHA2('".$phrase."', 512)) as char) as firstName, CAST(AES_DECRYPT(sureName, SHA2('".$phrase."', 512)) as char) as sureName, tableKey FROM $table) as k ORDER BY k.firstName, k.sureName";
		$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $tree[] = $row;
        }
		if (isset($tree))
        {
            $old_depth = 0;
            $first = true;
            
            if (isset($_POST['table']))
            {
                echo "{\"tree".$_POST['table']."\" : \"".$_POST['table']."\", \"children\" : ["; 
            }
            else
            {
                echo "{\"tree".$_POST['replaceTable']."_".$_POST['lang_code']."\" : \"".$_POST['replaceTable']."_".$_POST['lang_code']."\", \"children\" : [";
            }
            foreach ($tree as $key => $value)
            {

                if ($first)
                {
                    $first = false;
                }
                else
                {
                    echo ", ";
                }

                echo "{\"title\" : \"".$value['note']."\", \"key\" : \"".$value['tableKey']."\", \"".$data."\":\"".$tree2."\"}";
            }
        }
        echo "]}";
    }
    
    $replaceTable = getReplaceTable(false);


	if ($replaceTable[$_POST['replaceTable']] == PREFIX."administrado_menu")
	{
        $tree = getTree_administrado_menu();
	}
    else if ($replaceTable[$_POST['replaceTable']] == PREFIX."users")
	{
        $tree = getTree_users();
	}
	
	else if ($replaceTable[$_POST['replaceTable']] == PREFIX."menu")
	{
		$tree = getTree_menu();
	}
    else if ($replaceTable[$_POST['replaceTable']] == PREFIX."ref_objects")
	{
		$tree = getTree_ref_objects();
	}
    else if ($replaceTable[$_POST['replaceTable']] == PREFIX."ref_types")
	{
		$tree = getTree_ref_types();
	}    
    else if ($replaceTable[$_POST['replaceTable']] == PREFIX."ref_properties")
	{
		$tree = getTree_ref_properties();
	}
    else if ($replaceTable[$_POST['replaceTable']] == PREFIX."ref_settings")
	{
		$tree = getTree_ref_settings();
	}
    else if ($replaceTable[$_POST['replaceTable']] == PREFIX."ref_objects_image")
	{
		$tree = getTree_ref_objects_image();
	}
    else if ($replaceTable[$_POST['replaceTable']] == PREFIX."menu_footer")
	{
		$tree = getTree_menu_footer();
        
          /*$first = true;
        $data .= "{\"tree".$_POST['replaceTable']."\" : \"".$_POST['replaceTable']."\", \"children\" : ["; 
        //$data .= "[";
        foreach ($tree2 as $key => $value)
        {
            print_r($value);
            echo "<br>";
            
            if ($oldDepth > (int)$value['depth'])
            {
                echo __LINE__." ".$oldDepth ." > ".(int)$value['depth']."<br>";
                for ($i = 0 ; $i < ($oldDepth -(int)$value['depth'] ); $i++ )
                {
                    $data .= "]},";
                }
            }
            if (((int)$value['lft'] +1) < (int)$value['rgt'])
            {
                echo __LINE__." ".((int)$value['lft'] +1) ." < ".(int)$value['rgt']."<br>";
                $data .="{\"title\" : \"".$value['note']."\", \"key\" : \"".$value['tableKey']."\", \"folder\" : \"true\", \"expanded\" : \"true\",\"".$data."\":\"".$tree2."\", \"children\" : [";
                //$data .= "{\"title\" : \"".$row['note']."\",  \"menu\" : [";
            }
            else
            {
                echo __LINE__." ".(int)$value['lft']." ".(int)$value['rgt']."<br>";
                $data .= "{\"title\" : \"".$value['note']."\", \"key\" : \"".$value['tableKey']."\", \"".$data."\":\"".$tree2."\"}";
            }
            $oldDepth = (int)$value['depth'];
        }
        for ($i = 0 ; $i < $oldDepth; $i++ )
        {
            $data .= "]},";
        }
    
        echo "]}";

        $data = str_replace(",}", "}", $data);
        $data = str_replace(",]", "]", $data);
        
        echo $data;*/
	}

    renderTree($tree);

	function renderTree($tree = null, $table = null, $extra = null)
    {
        $replaceTable = getReplaceTable(false);
        
        $target_div = false;
        
        $data = "replace_table";
        
        if (!empty($table))
        {
            $target_div_json_table = target_div_json_table();
            
            if (array_key_exists($table, (array)$target_div_json_table))
            {
                $target_div = true;
                
                $data = "target_div";
                
                $data2 = "targetId_";
            }
        }
        
        if (isset($_POST['table']))
        {
            $tree2 = $_POST['table']; 
        }
        else
        {
            $tree2 = $_POST['replaceTable'];
        }
        
        if (isset($tree))
        {
            $old_depth = 0;
            $first = true;
            
            if (isset($_POST['table']))
            {
                echo "{\"tree".$_POST['table']."\" : \"".$_POST['table']."\", \"children\" : ["; 
            }
            else
            {
                echo "{\"tree".$_POST['replaceTable']."_".$_POST['lang_code']."\" : \"".$_POST['replaceTable']."_".$_POST['lang_code']."\", \"children\" : [";
            }
                foreach ($tree as $key => $value)
                {
                    if ($old_depth > (int)$value['depth'])
                    {	
                        for ($i = 0; $i < ($old_depth - (int)$value['depth']) ; $i++)
                        {
                            echo "]}";
                        }

                    }
                    if (((int)$value['lft'] +1) < (int)$value['rgt'])
                    {
                        if ($first)
                        {
                            $first = false;
                        }
                        else
                        {
                            echo ", ";
                        }
                        
                        if ($target_div)
                        {
                            echo "{\"title\" : \"".$value['note']."\", \"key\" : \"".$value['tableKey']."\", \"folder\" : \"true\", \"expanded\" : \"true\",\"".$data."\":\"".$data2.$value['tableKey']."\", \"children\" : [";  
                        }
                        else
                        {
                            echo "{\"title\" : \"".$value['note']."\", \"key\" : \"".$value['tableKey']."\", \"folder\" : \"true\", \"expanded\" : \"true\",\"".$data."\":\"".$tree2."\", \"children\" : [";
                        }
                        $first = true;
                    }
                    else
                    {
                        if ($first)
                        {
                            $first = false;
                        }
                        else
                        {
                            echo ", ";
                        }
                        if ($target_div)
                        {
                            echo "{\"title\" : \"".$value['note']."\", \"key\" : \"".$value['tableKey']."\", \"".$data."\":\"".$data2.$value['tableKey']."\"}";
                        }
                        else
                        {
                            echo "{\"title\" : \"".$value['note']."\", \"key\" : \"".$value['tableKey']."\", \"".$data."\":\"".$tree2."\"}";
                        }
                    }

                    $old_depth = (int)$value['depth'];
                }

                if ($old_depth > 0)
                {	
                    for ($i = 0; $i < ($old_depth) ; $i++)
                    {
                        echo "]}";
                    }

                }

            echo "]}";
            /*if ($replaceTable[$_POST['replaceTable']] == PREFIX."menu_footer")
            {
                echo "]}";
            }*/
        }
    }
?>