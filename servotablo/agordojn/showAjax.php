<?php
    session_start();
    include_once("../../common/db.php");
    include_once("../../common/userData.php");
    include_once("../../administrado/ext/theme/nav.php");
    include_once("../../common/crypto.php");
    

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        $replaceTable = getReplaceTable(false);
        
        if ($replaceTable[$_POST['replaceTable']] === PREFIX.'administrado_menu')
        {
            showAjaxAdministrado_menu();
        }
        if ($replaceTable[$_POST['replaceTable']] === PREFIX.'servotablo_menu')
        {
            showAjaxServotablo_menu();
        }
        else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'site_settings')
        {
            showAjaxSite_settings();
        }
        else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'users')
        {
            showAjaxUsers();
        }
    }

    function getIcons($type = "fa-icon")
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
        
        $table = "`".PREFIX."icons`";
        $table2 = "`".PREFIX."icon_types`";
        
        $sql = "SELECT *, t1.tableKey as masterTableKey, CAST(AES_DECRYPT(t1.note, SHA2('".$phrase."', 512)) AS char) as note, CAST(AES_DECRYPT(t2.note, SHA2('".$phrase."', 512)) AS char) as note2 FROM ".$table." as t1 INNER JOIN ".$table2." as t2 ON t1.iconTypeId = t2.iconTypeId WHERE t2.note = AES_ENCRYPT('".$type."', SHA2('".$phrase."', 512))";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $pageData[] = $row;
        }
        
        return $pageData;
    }

    function showAjaxServotablo_menu()
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
        $showAjaxServotablo_menu = $langStrings['showAjaxServotablo_menu'];

        $showAjaxServotablo_menu_array = getLangstringsArray('showAjaxServotablo_menu_array', $displayLang);

        $table = "`".PREFIX."servotablo_menu`";
        $table2 = "`".PREFIX."servotablo_menu_lang`";

        $sql = "SELECT * FROM (SELECT node.menuId, node.lft, node.rgt, node.tableKey as masterTableKey, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) as char) as folder, CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) as char) as file,  CAST(AES_DECRYPT(node.icon, SHA2('".$phrase."', 512)) as char) as icon, COUNT(parent.lft) - 1 as depth
        FROM (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS node,
              (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.menuId = lang.menuId GROUP BY lang.lang ORDER BY menu.lft";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        $numRows = mysqli_num_rows($result);

        $first = true;
        
        $i = 1;
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            echo "<div class=\"form-group row\">";
                echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxServotablo_menu_array[1][$row['lang']]." ";
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
                    echo "<label for=\"folder[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxServotablo_menu[1]." ";
                    
                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"folder[".$row['masterTableKey']."]\" value = \"".$row['folder']."\"";
                        
                        echo ">";
                    echo "</div>";
                echo "</div>";
                
                echo "<div class=\"form-group row\">";
                    echo "<label for=\"file[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxServotablo_menu[2]." ";
                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"file[".$row['masterTableKey']."]\" value = \"".$row['file']."\"";
                       echo ">";
                    echo "</div>";
                echo "</div>";
                
                echo "<div class=\"form-group row\">";
                    echo "<label for=\"icon[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxServotablo_menu[3]." ";
                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                
                        unset($data);
                
                        $data = getIcons();
                        
                        echo "<select class = \"selectpicker2 form-control show-tick\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"icon[".$row['masterTableKey']."]\" data-size = \"5\" data-live-search = \"true\">";
                        
                            foreach ($data as $key => $value)
                            {
                                if (empty(trim($value['note'])))
                                {
                                    continue;
                                }
                                
                                echo "<option value = \"".$value['masterTableKey']."\"";
                                    if ((int)$value['fas'])
                                    {
                                        $string = "fa-"; 
                                    }
                                    else if ((int)$value['far'])
                                    {
                                        $string = "fa-"; 
                                    }
                                    else if ((int)$value['fab'])
                                    {
                                       $string = "fa-";  
                                    }
                
                                
                                    if (($row['icon']) === ($string.$value['note']))
                                    {
                                        echo " "."selected";
                                    }
                                echo " data-icon = \"fa-".$value['note']."\">".$value['note']."</option>";
                            }
                        echo "</select>";
                
                echo "</div>";
                echo "</div>";
            }
        }
    }

    function showAjaxAdministrado_menu()
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
        $showAjaxServotablo_menu = $langStrings['showAjaxServotablo_menu'];

        $showAjaxServotablo_menu_array = getLangstringsArray('showAjaxServotablo_menu_array', $displayLang);

        $table = "`".PREFIX."administrado_menu`";
        $table2 = "`".PREFIX."administrado_menu_lang`";

        $sql = "SELECT * FROM (SELECT node.menuId, node.lft, node.rgt, node.tableKey as masterTableKey, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) as char) as folder, CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) as char) as file,  CAST(AES_DECRYPT(node.icon, SHA2('".$phrase."', 512)) as char) as icon, COUNT(parent.lft) - 1 as depth
        FROM (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS node,
              (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.menuId = lang.menuId GROUP BY lang.lang ORDER BY menu.lft";
		//echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        $numRows = mysqli_num_rows($result);

        $first = true;
        
        $i = 1;
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            echo "<div class=\"form-group row\">";
                echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxServotablo_menu_array[1][$row['lang']]." ";
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
                    echo "<label for=\"folder[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxServotablo_menu[1]." ";
                    
                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"folder[".$row['masterTableKey']."]\" value = \"".$row['folder']."\"";
                        
                        echo ">";
                    echo "</div>";
                echo "</div>";
                
                echo "<div class=\"form-group row\">";
                    echo "<label for=\"file[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxServotablo_menu[2]." ";
                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"file[".$row['masterTableKey']."]\" value = \"".$row['file']."\"";
                       echo ">";
                    echo "</div>";
                echo "</div>";
                
                echo "<div class=\"form-group row\">";
                    echo "<label for=\"icon[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxServotablo_menu[3]." ";
                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                
                        unset($data);
                
                        $data = getIcons();
                        
                        echo "<select class = \"selectpicker2 form-control show-tick\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"icon[".$row['masterTableKey']."]\" data-size = \"5\" data-live-search = \"true\">";
                        
                            foreach ($data as $key => $value)
                            {
                                if (empty(trim($value['note'])))
                                {
                                    continue;
                                }
                                
                                echo "<option value = \"".$value['masterTableKey']."\"";
                                    if ((int)$value['fas'])
                                    {
                                        $string = "fa-"; 
                                    }
                                    else if ((int)$value['far'])
                                    {
                                        $string = "fa-"; 
                                    }
                                    else if ((int)$value['fab'])
                                    {
                                       $string = "fa-";  
                                    }
                
                                
                                    if (($row['icon']) === ($string.$value['note']))
                                    {
                                        echo " "."selected";
                                    }
                                echo " data-icon = \"fa-".$value['note']."\">".$value['note']."</option>";
                            }
                        echo "</select>";
                
                echo "</div>";
                echo "</div>";
            }
        }
    }

    function showAjaxSite_settings()
    {
        global $link;

        global $phrase;

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
        $showAjaxSite_settings = $langStrings['showAjaxSite_settings'];

        $showAjaxSite_settings_array = getLangstringsArray('showAjaxSite_settings_array', $displayLang);

        $replaceLangRev = getReplaceLang(false);
        
        $table = "`".PREFIX."site_settings`";
        $table2 = "`".PREFIX."site_settings_lang`";
        
        $table10 = "`".PREFIX."languages`";

        $sql = "SELECT * FROM (SELECT node.	settingId, node.lft, node.rgt, node.tableKey as masterTableKey, CAST(AES_DECRYPT(node.setting, SHA2('".$phrase."', 512)) as char) as setting, CAST(AES_DECRYPT(node.data, SHA2('".$phrase."', 512)) as char) as data, node.type, COUNT(parent.lft) - 1 as depth
        FROM (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS node,
              (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT settingId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.settingId = lang.settingId GROUP BY lang.lang ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        $numRows = mysqli_num_rows($result);

        /*$showAjaxSite_settings[1] = "Parameter";
        $showAjaxSite_settings[2] = "Värde";
        
        $showAjaxSite_settings_array[1]['sv'] = "Inställning";*/

        $first = true;
        
        $i = 1;
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            echo "<div class=\"form-group row\">";
                echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxSite_settings_array[1][$row['lang']]." ";
                if ($numRows > 1)
                {
                    echo "[".$row['lang']."]";
                }
                echo "</label>";
                echo "<div class=\"col-sm-10\">";
                    echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"note[".$row['masterTableKey']."_".$replaceLang[$row['lang']]."]\" value = \"".$row['note']."\"";
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
                    echo "<label for=\"setting[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxSite_settings[1]." ";
                    
                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"setting[".$row['masterTableKey']."]\" value = \"".$row['setting']."\"";
                        
                        echo ">";
                        if(strpos($row['setting'], "password") !== false)
                        {
                            $password = true;
                        }
                        else
                        {
                            $password = false;
                        }
                    echo "</div>";
                echo "</div>";
                
                if ($row['type'] !== "lang")
                {
                    echo "<div class=\"form-group row\">";
                        echo "<label for=\"setting[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxSite_settings[2]." ";
                        echo "</label>";
                        echo "<div class=\"col-sm-10\">";
                            echo "<input type=\"";
                            if ($password)
                            {
                                echo "password";
                            }
                            else
                            {
                                echo "text";
                            }   
                            echo "\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"data[".$row['masterTableKey']."]\" value = \"".$row['data']."\"";
                           echo ">";
                        echo "</div>";
                    echo "</div>";
                }
                else
                {
                    echo "<div class=\"form-group row\">";
                        echo "<label for=\"setting[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxSite_settings[3]." ";
                        echo "</label>";
                        echo "<div class=\"col-sm-10\">";
                    
                        $sql = "SELECT tableKey, `Local language name` as lang, Code FROM ".$table10." ORDER BY CASE ".implode(" ", (array)$order_lang)." WHEN Code = 'en' THEN 11 WHEN Code = 'de' THEN 12 WHEN Code = 'fr' THEN 13 WHEN Code = 'it' THEN 14 ELSE 100 END, code LIMIT 18446744073709551615";
                        //echo __LINE__." ".$sql."<br>";
                        $result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

                        $langues = array_flip(array_map("trim", explode(",", $row['data'])));
                        
                        echo "<select id = \"data[".$row['masterTableKey']."]\" class = \"form-control syncData selectpicker2 show-tick\" data-replace_table = \"".$_POST['replaceTable']."\" multiple>";
                            while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
                            {
                                echo "<option value = \"".$row2['tableKey']."\"";
                                if (array_key_exists($replaceLangRev[$row2['tableKey']], (array)$langues))
                                {
                                    echo " "."selected";
                                }
                                echo ">".$row2['lang']."</option>";
                            }
                        echo "</select>";
                        echo "</div>";
                    echo "</div>";
                }
                
            }
        }
    }

    function showAjaxUsers()
    {
        include_once("../../editProfile.php");
        
        $_POST['editProfile'] = $_POST['id'];
        editUserProfile();
    }

?>