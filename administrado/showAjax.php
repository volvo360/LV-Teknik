<?php
    session_start();
    include_once("../common/db.php");
    include_once("../common/crypto.php");
    include_once("../common/userData.php");

    function syncFieldOwnPage()
    {
        global $link;

        global $phrase;

        $replaceTable = getReplaceTable(false);

        $table1 = $table10 = "`".PREFIX."menu`";;

        $table10 = "`".PREFIX."own_pages`";

        $sql = "SELECT * FROM ".$table1;
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $data[$row['tableKey']] = $row;
        }

        $sql = "SELECT * FROM ".$table10;
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $ownPageData[$row['tableKey']] = $row;
        }

        $insertKey = array_diff_key($data, $ownPageData);

        if (empty($ownPageData))
        {
            $insertKey = $ownPageData;
        }

        foreach ($insertKey as $key => $value)
        {
            $sql = "INSERT INTO ".$table10." (tableKey, pageId, lft, rgt) VALUES ('".$data[$key]['tableKey']."', '".$data[$key]['menuId']."', '".$data[$key]['lft']."', '".$data[$key]['rgt']."' )";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        }  

        foreach ($data as $key => $value)
        {
            $sql = "UPDATE ".$table10." SET lft = ".$data[$key]['lft'].", rgt = ".$data[$key]['rgt']." WHERE tableKey = '".$key."'";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        }
    }

    function getHeaderMenu()
    {
        global $link;
        global $phrase;
        
        $replaceTable = getReplaceTable();
        
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
        
        $table = "`".PREFIX."menu`";
        $table2 = "`".PREFIX."menu_lang`";
        checkTable($table2);
        unset($data);

        $sql = "SELECT * FROM (SELECT node.menuId, node.lft, node.rgt, node.tableKey, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) AS CHAR) as folder, CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) AS CHAR) as file,  CAST(AES_DECRYPT(node.icon, SHA2('".$phrase."', 512)) AS CHAR) as icon, node.displayMenu, node.type, (COUNT(parent.menuId) - 1) AS depth
        FROM $table AS node,
              $table AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              GROUP BY menuId) AS lang ON menu.menuId = lang.menuId WHERE menu.type != 'deleted' ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            if ((int)$row['displayMenu'] == 0)
            {
                //continue;    
            }
            else if ($row['type'] === "deleted")
            {
                return false;
            }
            
            $rowData[$row['tableKey']] = $row;
        }
        
        return $rowData;
    }

    function showAjax_menu_footer()
    {
        global $link;
        global $phrase;
        
        $replaceTable = getReplaceTable();
        
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
        $showAjax_menu_footer = $langStrings['showAjax_menu_footer'];

        $showAjax_menu_footer_array = getLangstringsArray('showAjax_menu_footer_array', $displayLang);
        
        $table1 = $table10 = "`".PREFIX."menu_footer`";
        $table2 = $table10 = "`".PREFIX."menu_footer_lang`";

        $table10 = "`".PREFIX."own_pages`";
        $table11 = "`".PREFIX."own_pages_lang`";

        $sql = "SELECT * FROM ".$table1." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            if ((int)$row['lft'] +1 < (int)$row['rgt'])
            {
                $header = true;
                $folder = false;
            }
            else if (empty($row['masterMenuId']))
            {
                $header = true;
                $folder = true;
            }
            else
            {
                $header = false;
                $rowData = $row;
            }
            $rowData2 = $row;
        }
        
        if ($header)
        {
            $sql = "SELECT * FROM (SELECT node.menuId, node.tableKey as masterTableKey, node.masterMenuId, node.lft, node.rgt, node.tableKey, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) AS CHAR) as folder, CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) AS CHAR) as file, (COUNT(parent.menuId) - 1) AS depth
                    FROM $table1 AS node,
                          $table1 AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
                    ORDER BY node.lft) as menu LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, tableKey, 
                          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
                          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
                          GROUP BY menuId) AS lang ON menu.menuId = lang.menuId WHERE masterTableKey = '".mysqli_real_escape_string($link, $_POST['id'])."' ORDER BY menu.lft";
            //echo __LINE__." ".$sql."<br>";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            
            $first = true;
            
            $rows = mysqli_num_rows($result);
            
            $i = 1;
            
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                if ($i == 1)
                {
                    echo "<div class=\"form-group row\">";
                        echo "<label for=\"menuId\" class=\"col-sm-2 col-form-label\">".$showAjax_menu_footer[4];
                        echo "</label>";   
                        echo "<div class=\"col-sm-10\">";
                            $temp = getHeaderMenu();
                            echo "<select class = \"form-control selectpicker2 show-tick\" data-replace_table = \"".$replaceTable[PREFIX."menu_footer"]."\" id = \"masterMenuId[".$row['masterTableKey']."]\" data-size = \"5\" data-target_div = \"ajax_".$replaceTable[PREFIX."menu_footer"]."\">";
                                echo "<option value = \"-1\" selected>".$showAjax_menu_footer[5]."</option>";
                                foreach ($temp as $key => $value)
                                {
                                    echo "<option value = \"".$value['tableKey']."\"";
                                        if ($value['depth'] > 0)
                                        {
                                            echo " "."style = \"margin-left : ".((int)$value['depth']*15)."px;\"";
                                        }

                                    if ($value['menuId'] === $rowData['masterMenuId'])
                                    {
                                        echo " "."selected";
                                    }

                                    echo ">".$value['note']."</option>";
                                }
                            echo "</select>";
                        echo "</div>";
                    echo "</div>";
                }
                
                echo "<div class=\"form-group row\">";
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu_footer_array[1][$row['lang']];
                        if (count($displayLang) > 1)
                        {
                            echo " [".$row['lang']."]";
                        }
                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."menu_footer"]."\" id=\"note[".$row['tableKey']."]\" value = \"".$row['note']."\"";
                            if ($first)
                            {
                                $first = false;
                                
                                echo " "."data-reload_tree=\"tree_".$replaceTable[PREFIX."menu_footer"]."\"";
                            }
                        echo ">";
                    echo "</div>";
                echo "</div>";
                
                if ($i == $rows && $folder)
                {
                    echo "<div class=\"form-group row\">";
                        echo "<label for=\"folder[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu_footer[2];
                            
                        echo "</label>";
                        echo "<div class=\"col-sm-10\">";
                            echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."menu_footer"]."\" id=\"folder[".$row['masterTableKey']."]\" value = \"".$row['folder']."\"";
                                
                            echo ">";
                        echo "</div>";
                    echo "</div>";
                    
                    echo "<div class=\"form-group row\">";
                        echo "<label for=\"file[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu_footer[3];
                           
                        echo "</label>";
                        echo "<div class=\"col-sm-10\">";
                            echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$replaceTable[PREFIX."menu_footer"]."\" id=\"file[".$row['masterTableKey']."]\" value = \"".$row['file']."\"";
                                
                            echo ">";
                        echo "</div>";
                    echo "</div>";
                }
            }
        }
        else
        {
            echo "<div class=\"form-group row\">";
                echo "<label for=\"masterMenuId[".$rowData2['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu_footer[1];
                echo "</label>";   
                echo "<div class=\"col-sm-10\">";
                    $temp = getHeaderMenu();
                    echo "<select class = \"form-control selectpicker2 show-tick\" data-replace_table = \"".$replaceTable[PREFIX."menu_footer"]."\" id = \"masterMenuId[".$rowData2['tableKey']."]\" data-size = \"5\" data-reload_tree = \"tree_".$replaceTable[PREFIX."menu_footer"]."\" data-target_div = \"ajax_".$replaceTable[PREFIX."menu_footer"]."\">";
                        echo "<option value = \"-1\" selected>".$showAjax_menu_footer[5]."</option>";
                        foreach ($temp as $key => $value)
                        {
                            echo "<option value = \"".$value['tableKey']."\"";
                                if ($value['depth'] > 0)
                                {
                                    echo " "."style = \"margin-left : ".((int)$value['depth']*15)."px;\"";
                                }
                            
                            if ($value['menuId'] === $rowData['masterMenuId'])
                            {
                                echo " "."selected";
                            }
                            
                            echo ">".$value['note']."</option>";
                        }
                    echo "</select>";
                echo "</div>";
             echo "</div>";
        }
    }

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        $replaceTable = getReplaceTable(false);
        
        if ($replaceTable[$_POST['replaceTable']] === PREFIX.'menu')
        {
            syncFieldOwnPage();
            showAjax_menu();
        }
        else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'menu_footer')
        {
            showAjax_menu_footer();
        }
        
        else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'site_settings')
        {
            showAjaxSite_settings();
        }
        else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'users')
        {
            showAjaxUsers();
        }else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'ref_objects')
        {
            showAjaxRefObjects();
        }
        else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'ref_properties')
        {
            showAjaxRefProperties();
        }
        else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'ref_types')
        {
            showAjaxRefTypes();
        }
        else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'ref_settings')
        {
            showAjaxRefSettings();
        }
        else if ($replaceTable[$_POST['replaceTable']] === PREFIX.'ref_objects_image')
        {
            showAjaxRefObjectsImage();
        }
    }
    
    function editRefTypes()
    {
        global $link;

        global $phrase;

        $replaceTable = getReplaceTable();
        
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
        $editRefTypes = $langStrings['editRefTypes'];

        $editRefTypes_array = getLangstringsArray('editRefTypes_array', $displayLang);

        $table = "`".PREFIX."ref_types`";
        $table2 = "`".PREFIX."ref_types_lang`";
        
        $sql = "SELECT * FROM (SELECT node.typeId, node.lft, node.rgt, node.tableKey, (COUNT(parent.typeId) - 1) AS depth
        FROM $table AS node,
              $table AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT typeId, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              GROUP BY typeId) AS lang ON menu.typeId = lang.typeId ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

         echo "<div class = \"row\">";
            echo "<div class = \"col-md-3\" style = \"max-height : 100%; overflow : auto;\">";
                echo "<div id = \"tree_".$replaceTable[PREFIX."ref_types"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."ref_types"]."\" data-replace_table = \"".$replaceTable[PREFIX.'ref_types']."\">";
                    echo "<ul id = \"tree_".$replaceTable[PREFIX."ref_types"]."-data\" style = \"display:none;\" >";
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                        {
                            $rowData[] = $row;

                            if ($oldDepth > (int)(int)$row['depth'])
                            {
                                for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                                {
                                    echo "</ul></li>";
                                }
                            }

                            if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
                            {
                                echo "<li class = \"folder expanded\" id = \"".$row['tableKey']."\"";

                                    if (array_key_exists($row['tableKey'], (array)$display))
                                    {
                                        echo " "."data-selected = true";
                                    }

                                echo ">".$row['note']."<ul>";
                            }
                            else
                            {
                                echo "<li id = \"".$row['tableKey']."\"";

                                    if (array_key_exists($row['tableKey'], (array)$display))
                                    {
                                        echo " "."data-selected = true";
                                    }

                                echo ">".$row['note']."</li>";
                            }

                            $oldDepth = (int)$row['depth'];
                        }

                        if ($oldDepth > 0)
                        {
                            for ($i = 0; $i < ($oldDepth); $i++)
                            {
                                echo "</ul></li>";
                            }
                        }
                    echo "</ul>";
                echo "</div><br>";

                echo "<form id = \"addForm_".$replaceTable[PREFIX."ref_types"]."\">";
                    echo $editRefTypes[1]."<br>";
                    echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";

                    echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."ref_types"]."\" data-replace_table = \"".$replaceTable[PREFIX."ref_types"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$editRefTypes[2]."</button>";
                echo "</form><br><br>";

            echo "</div>";
            echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
                echo "<div id =\"ajax_".$replaceTable[PREFIX."ref_types"]."\">";
                   // renderManualEdit($lang, $rowData);
                echo "</div>";
            echo "</div>";
        echo "</div>";
    }

    function editRefProperties()
    {
        global $link;

        global $phrase;

        $replaceTable = getReplaceTable();
        
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
        $editRefProperties = $langStrings['editRefProperties'];
        $editRefProperties_array = getLangstringsArray('editRefProperties_array', $displayLang);

        $table = "`".PREFIX."ref_properties`";
        $table2 = "`".PREFIX."ref_properties_lang`";
       
        $sql = "SELECT * FROM (SELECT node.propertiesId, node.lft, node.rgt, node.tableKey, (COUNT(parent.propertiesId) - 1) AS depth
        FROM $table AS node,
              $table AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT propertiesId, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              GROUP BY propertiesId) AS lang ON menu.propertiesId = lang.propertiesId ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

         echo "<div class = \"row\">";
            echo "<div class = \"col-md-3\" style = \"max-height : 100%; overflow : auto;\">";
                echo "<div id = \"tree_".$replaceTable[PREFIX."ref_properties"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."ref_properties"]."\" data-replace_table = \"".$replaceTable[PREFIX.'ref_properties']."\">";
                    echo "<ul id = \"tree_".$replaceTable[PREFIX."ref_properties"]."-data\" style = \"display:none;\" >";
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                        {
                            $rowData[] = $row;

                            if ($oldDepth > (int)(int)$row['depth'])
                            {
                                for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                                {
                                    echo "</ul></li>";
                                }
                            }

                            if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
                            {
                                echo "<li class = \"folder expanded\" id = \"".$row['tableKey']."\"";

                                    if (array_key_exists($row['tableKey'], (array)$display))
                                    {
                                        echo " "."data-selected = true";
                                    }

                                echo ">".$row['note']."<ul>";
                            }
                            else
                            {
                                echo "<li id = \"".$row['tableKey']."\"";

                                    if (array_key_exists($row['tableKey'], (array)$display))
                                    {
                                        echo " "."data-selected = true";
                                    }

                                echo ">".$row['note']."</li>";
                            }

                            $oldDepth = (int)$row['depth'];
                        }

                        if ($oldDepth > 0)
                        {
                            for ($i = 0; $i < ($oldDepth); $i++)
                            {
                                echo "</ul></li>";
                            }
                        }
                    echo "</ul>";
                echo "</div><br>";

                echo "<form id = \"addForm_".$replaceTable[PREFIX."ref_properties"]."\">";
                    echo $editRefProperties[1]."<br>";
                    echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";

                    echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."ref_properties"]."\" data-replace_table = \"".$replaceTable[PREFIX."ref_properties"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$editRefProperties[2]."</button>";
                echo "</form><br><br>";

            echo "</div>";
            echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
                echo "<div id =\"ajax_".$replaceTable[PREFIX."ref_properties"]."\">";
                   // renderManualEdit($lang, $rowData);
                echo "</div>";
            echo "</div>";
        echo "</div>";
    }

    function editRefObjects_2021_04_30()
    {
        global $link;

        global $phrase;

        $replaceTable = getReplaceTable();
        
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
        $editRefObjects = $langStrings['editRefObjects'];

        $editRefObjects_array = getLangstringsArray('editRefObjects_array', $displayLang);

        $table = "`".PREFIX."ref_objects`";
        $table2 = "`".PREFIX."ref_objects_lang`";
        echo __LINE__." ".__FUNCTION__." <br>";
        
        $sql = "SELECT * FROM (SELECT node.referenceId, node.lft, node.rgt, node.tableKey, (COUNT(parent.referenceId) - 1) AS depth
        FROM $table AS node,
              $table AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT referenceId, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              GROUP BY referenceId) AS lang ON menu.referenceId = lang.referenceId ORDER BY menu.lft";
        echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

         echo "<div class = \"row\">";
            echo "<div class = \"col-md-3\" style = \"max-height : 100%; overflow : auto;\">";
                echo "<div id = \"tree_".$replaceTable[PREFIX."ref_objects"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."ref_objects"]."\" data-replace_table = \"".$replaceTable[PREFIX.'ref_objects']."\">";
                    echo "<ul id = \"tree_".$replaceTable[PREFIX."ref_objects"]."-data\" style = \"display:none;\" >";
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                        {
                            $rowData[] = $row;

                            if ($oldDepth > (int)(int)$row['depth'])
                            {
                                for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                                {
                                    echo "</ul></li>";
                                }
                            }

                            if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
                            {
                                echo "<li class = \"folder expanded\" id = \"".$row['tableKey']."\"";

                                    if (array_key_exists($row['tableKey'], (array)$display))
                                    {
                                        echo " "."data-selected = true";
                                    }

                                echo ">".$row['note']."<ul>";
                            }
                            else
                            {
                                echo "<li id = \"".$row['tableKey']."\"";

                                    if (array_key_exists($row['tableKey'], (array)$display))
                                    {
                                        echo " "."data-selected = true";
                                    }

                                echo ">".$row['note']."</li>";
                            }

                            $oldDepth = (int)$row['depth'];
                        }

                        if ($oldDepth > 0)
                        {
                            for ($i = 0; $i < ($oldDepth); $i++)
                            {
                                echo "</ul></li>";
                            }
                        }
                    echo "</ul>";
                echo "</div><br>";

                echo "<form id = \"addForm_".$replaceTable[PREFIX."ref_objects"]."\">";
                    echo $editRefObjects[1]."<br>";
                    echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";

                    echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."ref_objects"]."\" data-replace_table = \"".$replaceTable[PREFIX."ref_objects"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$editRefObjects[2]."</button>";
                echo "</form><br><br>";

            echo "</div>";
            echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
                echo "<div id =\"ajax_".$replaceTable[PREFIX."ref_objects"]."\">";
                   // renderManualEdit($lang, $rowData);
                echo "</div>";
            echo "</div>";
        echo "</div>";

    }

    function editRefSettings()
    {
        global $link;

        global $phrase;

        $replaceTable = getReplaceTable();
        
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
        $ediRefSettings = $langStrings['ediRefSettings'];

        $ediRefSettings_array = getLangstringsArray('ediRefSettings_array', $displayLang);
        
        $table = "`".PREFIX."ref_settings`";
        $table2 = "`".PREFIX."ref_settings_lang`";
        
        $sql = "SELECT * FROM (SELECT node.settingId , node.lft, node.rgt, node.tableKey, (COUNT(parent.settingId ) - 1) AS depth
        FROM (SELECT * FROM $table ) AS node,
              (SELECT * FROM $table ) AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT settingId , 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              GROUP BY settingId ) AS lang ON menu.settingId  = lang.settingId  ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        echo "<div class = \"row\">";
            echo "<div class = \"col-md-3\" style = \"max-height : 100%; overflow : auto;\">";
                echo "<div id = \"tree_".$replaceTable[PREFIX."ref_settings"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."ref_settings"]."\" data-replace_table = \"".$replaceTable[PREFIX.'ref_settings']."\">";
                    echo "<ul id = \"tree_".$replaceTable[PREFIX."ref_settings"]."-data\" style = \"display:none;\" >";
         
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                        {
                            $rowData[] = $row;

                            if ($oldDepth > (int)(int)$row['depth'])
                            {
                                for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                                {
                                    echo "</ul></li>";
                                }
                            }

                            if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
                            {
                                echo "<li class = \"folder expanded\" id = \"".$row['tableKey']."\"";

                                    if (array_key_exists($row['tableKey'], (array)$display))
                                    {
                                        echo " "."data-selected = true";
                                    }

                                echo ">".$row['note']."<ul>";
                            }
                            else
                            {
                                echo "<li id = \"".$row['tableKey']."\"";

                                    if (array_key_exists($row['tableKey'], (array)$display))
                                    {
                                        echo " "."data-selected = true";
                                    }

                                echo ">".$row['note']."</li>";
                            }

                            $oldDepth = (int)$row['depth'];
                        }

                        if ($oldDepth > 0)
                        {
                            for ($i = 0; $i < ($oldDepth); $i++)
                            {
                                echo "</ul></li>";
                            }
                        }
                     echo "</ul>";
                echo "</div><br>";

                echo "<form id = \"addForm_".$replaceTable[PREFIX."ref_settings"]."\">";
                    echo $editRefSettings[1]."<br>";
                    echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";

                    echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."ref_settings"]."\" data-replace_table = \"".$replaceTable[PREFIX."ref_settings"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$editRefSettings[2]."</button>";
                echo "</form><br><br>";

            echo "</div>";
            echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
                echo "<div id =\"ajax_".$replaceTable[PREFIX."ref_settings"]."\">";
                   // renderManualEdit($lang, $rowData);
                echo "</div>";
            echo "</div>";
        echo "</div>";

    }

    function editRefPageText()
    {
        global $link;

        global $phrase;

        $replaceTable = getReplaceTable();
        
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
        $editOwnPage = $langStrings['editOwnPage'];

        $editOwnPage_array = getLangstringsArray('editOwnPage_array', $displayLang);

        $table = "`".PREFIX."menu`";
        $table2 = "`".PREFIX."menu_lang`";
        
        //$_SESSION['folderUrl'] = "flexshare/lv/v3/themes/megakit-premium/img/";
        $_SESSION['folderUrl'] = '/img/';
        
        $table = "`".PREFIX."own_pages`";
        $table2 = "`".PREFIX."own_pages_lang`";

        $sql = "SHOW COLUMNS FROM ".$table."";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            if ($row['Field'] !== "autoId")
            {
                if ($row['Type'] === "blob")
                {
                    $fields[$row['Field']] = "CAST(AES_DECRYPT(".$row['Field'].", SHA2('".$phrase."', 512)) AS CHAR) as ".$row['Field']."";
                }
                else
                {    
                    if ($row['Field'] == "pageId")
                    {
                        $fields[$row['Field']] = "page.".$row['Field'];
                    }
                    else if ($row['Field'] == "tableKey")
                    {
                        $fields[$row['Field']] = "page.".$row['Field']." as masterTableKey";
                    }
                    else
                    {
                        $fields[$row['Field']] = $row['Field'];
                    }
                }
            }
        }

        $sql = "SHOW COLUMNS FROM ".$table2."";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            if ($row['Field'] !== "autoId")
            {
                if ($row['Type'] === "blob")
                {
                    $fields2[$row['Field']] = "CAST(AES_DECRYPT(".$row['Field'].", SHA2('".$phrase."', 512)) AS CHAR) as ".$row['Field']."";
                }
                else
                {    
                    $fields2[$row['Field']] = $row['Field'];
                }
            }
        }

        $sql = "SELECT ".implode(", ", $fields).", lang.* FROM ".$table." as page LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM (SELECT ".implode(", ", (array)$fields2)." FROM $table2) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t GROUP BY pageId) as lang ON page.pageId = lang.pageId WHERE page.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
        
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $pageData[] = $row;
        }
        
        $replaceLang = getReplaceLang();
        
        $first = true;
        
        foreach ($pageData as $key => $value)
        {
            if ($first)
            {
                $first = false;
                echo "<div class=\"form-group row\">";
                    echo "<label for=\"headerFile[".$value['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$editOwnPage[1]." ";
                    echo "</label>";

                    echo "<div class=\"col-sm-10\">";
                        
                         echo "<input type = \"hidden\" class = \"syncData\" data-replace_id = \"headerImage[".$value['masterTableKey']."]\" id = \"headerImage\" data-replace_table = \"".$replaceTable[PREFIX."own_pages"]."\"> ";
                        echo "<button id = \"selectHeaderImage\" class = \"btn btn-default\" data-target_input = \"headerImage\">"."Välj bild"."</button>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"form-group row\">";
                    echo "<label for=\"note[".$value['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$editOwnPage[2]." ";
                    echo "</label>";

                    echo "<div class=\"col-sm-10\">";
                        echo "<input type = \"text\" ";
                        if (empty($value['tableKey']))
                            {
                                echo "id = \"subHeader[".$value['masterTableKey']."_".$replaceLang[reset($displayLang)]."]\"";
                            }
                            else
                            {
                                echo "id = \"subHeader[".$value['tableKey']."]\"";
                            }
                        echo "class = \"syncData form-control\" data-replace_table = \"".$replaceTable[PREFIX."own_pages"]."\"  value = \"".$value['subHeader']."\">";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"form-group row\">";
                    echo "<label for=\"note[".$value['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$editOwnPage[4]." ";
                    echo "</label>";

                    echo "<div class=\"col-sm-10\">";
                        echo "<input type = \"text\" ";
                        if (empty($value['tableKey']))
                            {
                                echo "id = \"header[".$value['masterTableKey']."_".$replaceLang[reset($displayLang)]."]\"";
                            }
                            else
                            {
                                echo "id = \"header[".$value['tableKey']."]\"";
                            }
                        echo "class = \"syncData form-control\" data-replace_table = \"".$replaceTable[PREFIX."own_pages"]."\" value = \"".$value['header']."\">";
                    echo "</div>";
                echo "</div>";
            }
        
            echo "<div class=\"form-group row\">";
                echo "<label for=\"note[".$value['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu_array[1][$value['lang']]." ";
                if ($numRows > 1)
                {
                    echo "[".$row['lang']."]";
                }
               
                echo "</label>";

                echo "<div class=\"col-sm-10\">";
                    echo "<textarea class = \"tinyMceArea\" ";

                    if (empty($value['tableKey']))
                    {
                        echo "id = \"note[".$value['masterTableKey']."_".$replaceLang[reset($displayLang)]."]\"";
                    }
                    else
                    {
                        echo "id = \"note[".$value['tableKey']."]\"";
                    }

                    echo " data-replace_table = \"".$replaceTable[PREFIX."own_pages"]."\">";
                        echo $value['note'];
                    echo "</textarea>";
                echo "</div>";
            echo "</div>";
            showMetaText($value['lang']);
        }
    }


    function renderSubReferences($refType = null)
    {
        global $link;

        global $phrase;

        $replaceTable = getReplaceTable();
        
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
        $editRefObjects = $langStrings['editRefObjects'];

        $editRefObjects_array = getLangstringsArray('editRefObjects_array', $displayLang);

        $table = "`".PREFIX."ref_objects`";
        $table2 = "`".PREFIX."ref_objects_lang`";
        
        $table = "`".PREFIX."ref_objects`";
        $table2 = "`".PREFIX."ref_objects_lang`";
        
        $sql = "SELECT * FROM (SELECT node.referenceId, node.lft, node.rgt, node.tableKey, (COUNT(parent.referenceId) - 1) AS depth
        FROM (SELECT * FROM $table WHERE refType = '".$refType."') AS node,
              (SELECT * FROM $table WHERE refType = '".$refType."') AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT referenceId, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(header, SHA2('".$phrase."', 512)) AS CHAR) as header FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              GROUP BY referenceId) AS lang ON menu.referenceId = lang.referenceId ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

         
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $rowData[] = $row;

                if ($oldDepth > (int)(int)$row['depth'])
                {
                    for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                    {
                        echo "</ul></li>";
                    }
                }

                if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
                {
                    echo "<li class = \"folder expanded\" id = \"".$row['tableKey']."\"";

                        if (array_key_exists($row['tableKey'], (array)$display))
                        {
                            echo " "."data-selected = true";
                        }

                    echo ">".$row['header']."<ul>";
                }
                else
                {
                    echo "<li id = \"".$row['tableKey']."\"";

                        if (array_key_exists($row['tableKey'], (array)$display))
                        {
                            echo " "."data-selected = true";
                        }

                    echo ">".$row['header']."</li>";
                }

                $oldDepth = (int)$row['depth'];
            }

            if ($oldDepth > 0)
            {
                for ($i = 0; $i < ($oldDepth); $i++)
                {
                    echo "</ul></li>";
                }
            }
        //echo "</ul>";
    }

    function editRefObjects()
    {
        global $link;

        global $phrase;

        $replaceTable = getReplaceTable();
        
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
        $editRefObjects = $langStrings['editRefObjects'];

        $editRefObjects_array = getLangstringsArray('editRefObjects_array', $displayLang);

        $table = "`".PREFIX."ref_types`";
        $table2 = "`".PREFIX."ref_types_lang`";
        
        $sql = "SELECT * FROM (SELECT node.typeId , node.lft, node.rgt, node.tableKey, (COUNT(parent.typeId ) - 1) AS depth
        FROM $table AS node,
              $table AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT typeId , 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              GROUP BY typeId ) AS lang ON menu.typeId  = lang.typeId  ORDER BY menu.lft";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

         echo "<div class = \"row\">";
            echo "<div class = \"col-md-3\" style = \"max-height : 100%; overflow : auto;\">";
                echo "<div id = \"tree_".$replaceTable[PREFIX."ref_objects"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."ref_objects"]."\" data-replace_table = \"".$replaceTable[PREFIX.'ref_objects']."\">";
                    echo "<ul id = \"tree_".$replaceTable[PREFIX."ref_objects"]."-data\" style = \"display:none;\" >";
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                        {
                            $rowData[] = $row;

                            if ($oldDepth > (int)(int)$row['depth'])
                            {
                                for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                                {
                                    echo "</ul></li>";
                                }
                            }

                            if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
                            {
                                echo "<li class = \"folder expanded\" id = \"".$row['tableKey']."\"";

                                    if (array_key_exists($row['tableKey'], (array)$display))
                                    {
                                        echo " "."data-selected = true";
                                    }

                                echo ">".$row['note']."<ul>";
                            }
                            else
                            {
                                echo "<li id = \"".$row['tableKey']."\" class = \"folder expanded\"";

                                    if (array_key_exists($row['tableKey'], (array)$display))
                                    {
                                        echo " "."data-selected = true";
                                    }

                                echo ">".$row['note'];
                                
                                echo "<ul>";
                                    renderSubReferences($row['typeId']);
                                echo "</ul></li>";
                            }

                            $oldDepth = (int)$row['depth'];
                        }

                        if ($oldDepth > 0)
                        {
                            for ($i = 0; $i < ($oldDepth); $i++)
                            {
                                echo "</ul></li>";
                            }
                        }
                    echo "</ul>";
                echo "</div><br>";

                echo "<form id = \"addForm_".$replaceTable[PREFIX."ref_objects"]."\">";
                    echo $editRefObjects[1]."<br>";
                    echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";

                    echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."ref_objects"]."\" data-replace_table = \"".$replaceTable[PREFIX."ref_objects"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$editRefObjects[2]."</button>";
                echo "</form><br><br>";

            echo "</div>";
            echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
                echo "<div id =\"ajax_".$replaceTable[PREFIX."ref_objects"]."\">";
                   // renderManualEdit($lang, $rowData);
                echo "</div>";
            echo "</div>";
        echo "</div>";

    }

    function editGalleryPage()
    {
        global $link;

        global $phrase;

        $replaceTable = getReplaceTable();
        
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
        $editGalleryPage = $langStrings['editGalleryPage'];

        $editGalleryPage_array = getLangstringsArray('editGalleryPage_array', $displayLang);

        $navs['refObjects'] = $editGalleryPage[1];
        $navs['refTypes'] = $editGalleryPage[3];
        $navs['refProperties'] = $editGalleryPage[2];
        $navs['refPageText'] = $editGalleryPage[5];
        $navs['refSettings'] = $editGalleryPage[4];
        
        $defaultNav = "refObjects";
        
        $table = "`".PREFIX."menu`";
        $table2 = "`".PREFIX."menu_lang`";
        
        echo "<ul class=\"nav nav-tabs\" id=\"myTab\" role=\"tablist\">";
            
            foreach ($navs as $key => $value)
            {
                echo "<li class=\"nav-item\" role=\"presentation\">";
                    echo "<a class=\"nav-link";
                        if ($key == $defaultNav)
                        {
                            echo " "."active";
                        }
                    echo "\" id=\"".$key."-tab\" data-toggle=\"tab\" href=\"#".$key."\" role=\"tab\" aria-controls=\"".$key."\" aria-selected=\"";
                        if ($key == $defaultNav)
                        {
                            echo "true";
                        }
                        else
                        {
                            echo "false";
                        }
                    echo "\">".$value."</a>";
                echo "</li>";
            }
            
        echo "</ul>";
        echo "<div class=\"tab-content\" id=\"myTabContent\" style = \"max-height : 49vh ; overflow: auto;\">";
            foreach ($navs as $key => $value)
            {
                echo "<div class=\"tab-pane fade";
                    if ($key === $defaultNav)
                    {
                        echo " "."show active";
                    }
                echo "\" id=\"".$key."\" role=\"tabpanel\" aria-labelledby=\"".$key."-tab\" style = \"height : 48vh;\">";
                echo "<br>";
                call_user_func("edit".ucfirst($key));
                echo "</div>";
            }

        echo "</div>";
    }

    function showMetaText($lang = null)
    {
        global $link;

        global $phrase;

        $replaceTable = getReplaceTable();
        
        $replaceLabg = getReplaceLang();
        
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
        $showMetaText = $langStrings['showMetaText'];
		
		echo __LINE__." ";
		print_r($showMetaText);
		echo "<br>";

        $showMetaText_array = getLangstringsArray('showMetaText_array', $displayLang);

        $table = "`".PREFIX."own_pages`";
        $table2 = "`".PREFIX."own_pages_lang`";
        
        $sql = "SHOW COLUMNS FROM ".$table."";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            if ($row['Field'] !== "autoId")
            {
                if ($row['Type'] === "blob")
                {
                    $fields[$row['Field']] = "CAST(AES_DECRYPT(".$row['Field'].", SHA2('".$phrase."', 512)) AS CHAR) as ".$row['Field']."";
                }
                else
                {    
                    if ($row['Field'] == "pageId")
                    {
                        $fields[$row['Field']] = "page.".$row['Field'];
                    }
                    else if ($row['Field'] == "tableKey")
                    {
                        $fields[$row['Field']] = "page.".$row['Field']." as masterTableKey";
                    }
                    else
                    {
                        $fields[$row['Field']] = $row['Field'];
                    }
                }
            }
        }

        $sql = "SHOW COLUMNS FROM ".$table2."";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            if ($row['Field'] !== "autoId")
            {
                if ($row['Type'] === "blob")
                {
                    $fields2[$row['Field']] = "CAST(AES_DECRYPT(".$row['Field'].", SHA2('".$phrase."', 512)) AS CHAR) as ".$row['Field']."";
                }
                else
                {    
                    $fields2[$row['Field']] = $row['Field'];
                }
            }
        }

        $sql = "SELECT ".implode(", ", $fields).", lang.* FROM ".$table." as page LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM (SELECT ".implode(", ", (array)$fields2)." FROM $table2) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t GROUP BY pageId) as lang ON page.pageId = lang.pageId WHERE page.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."' AND lang = '".$lang."'";
        //echo $sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $pageData[] = $row;

                echo "<div class=\"form-group row\">";
					if (empty($row['tableKey']))
                        {
                            echo "<label for=\"metaKeywords[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showMetaText[1]." ";
                        }
                        else
                        {
                            echo "<label for=\"metaKeywords[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showMetaText[1]." ";
                        }
                    
                    echo "</label>";

                    echo "<div class=\"col-sm-10\">";
                        echo "<input type = \"text\" ";
                        if (empty($row['tableKey']))
                        {
                            echo "id = \"metaKeywords[".$row['masterTableKey']."_".$replaceLang[$lang]."]\"";
                        }
                        else
                        {
                            echo "id = \"metaKeywords[".$row['tableKey']."]\"";
                        }
                        echo "class = \"syncData form-control\" data-replace_table = \"".$replaceTable[PREFIX."own_pages"]."\"  value = \"".$row['metaKeywords']."\">";
                    echo "</div>";
                echo "</div>";
                
                echo "<div class=\"form-group row\">";
                    echo "<label for=\"metaDescription[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showMetaText[2]." ";
                    echo "</label>";

                    echo "<div class=\"col-sm-10\">";
                        echo "<textarea ";
                        if (empty($row['tableKey']))
                            {
                                echo "id = \"metaDescription[".$row['masterTableKey']."_".$replaceLang[$lang]."]\"";
                            }
                            else
                            {
                                echo "id = \"metaDescription[".$row['tableKey']."]\"";
                            }
                        echo "class = \"syncData form-control\" data-replace_table = \"".$replaceTable[PREFIX."own_pages"]."\">";
                        echo $row['metaDescription']."</textarea>";
                    echo "</div>";
                echo "</div>";
            }    
        }
        else
        {
            echo "<div class=\"form-group row\">";
                echo "<label for=\"metaKeywords[".$pageData['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showMetaText[1]." ";
                echo "</label>";

                echo "<div class=\"col-sm-10\">";
                    echo "<input type = \"text\" ";
                    if (empty($pageData['tableKey']))
                        {
                            echo "id = \"metaKeywords[".$pageData['tableKey']."_".$replaceLang[$lang]."]\"";
                        }
                        else
                        {
                            echo "id = \"metaKeywords [".$pageData['tableKey']."]\"";
                        }
                    echo "class = \"syncData form-control\" data-replace_table = \"".$replaceTable[PREFIX."own_pages"]."\"  value = \"".$pageData['metaKeywords']."\">";
                echo "</div>";
            echo "</div>";
                
            echo "<div class=\"form-group row\">";
                echo "<label for=\"metaDescription[".$pageData['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showMetaText[2]." ";
                echo "</label>";

                echo "<div class=\"col-sm-10\">";
                    echo "<textarea ";
                    if (empty($pageData['tableKey']))
                        {
                            echo "id = \"metaDescription[".$pageData['masterTableKey']."_".$replaceLang[$lang]."]\"";
                        }
                        else
                        {
                            echo "id = \"metaDescription[".$row['tableKey']."]\"";
                        }
                    echo "class = \"syncData form-control\" data-replace_table = \"".$replaceTable[PREFIX."own_pages"]."\">";
                    echo $row['metaDescription']."</textarea>";
                echo "</div>";
            echo "</div>";
        }
    }

    function editOwnPage()
    {
        global $link;

        global $phrase;

        $replaceTable = getReplaceTable();
        
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
        $editOwnPage = $langStrings['editOwnPage'];

        $editOwnPage_array = getLangstringsArray('editOwnPage_array', $displayLang);

        $table = "`".PREFIX."menu`";
        $table2 = "`".PREFIX."menu_lang`";
        
        //$_SESSION['folderUrl'] = "flexshare/lv/v3/themes/megakit-premium/img/";
        $_SESSION['folderUrl'] = '/img/';
        
        $table = "`".PREFIX."own_pages`";
        $table2 = "`".PREFIX."own_pages_lang`";

        $sql = "SHOW COLUMNS FROM ".$table."";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            if ($row['Field'] !== "autoId")
            {
                if ($row['Type'] === "blob")
                {
                    $fields[$row['Field']] = "CAST(AES_DECRYPT(".$row['Field'].", SHA2('".$phrase."', 512)) AS CHAR) as ".$row['Field']."";
                }
                else
                {    
                    if ($row['Field'] == "pageId")
                    {
                        $fields[$row['Field']] = "page.".$row['Field'];
                    }
                    else if ($row['Field'] == "tableKey")
                    {
                        $fields[$row['Field']] = "page.".$row['Field']." as masterTableKey";
                    }
                    else
                    {
                        $fields[$row['Field']] = $row['Field'];
                    }
                }
            }
        }

        $sql = "SHOW COLUMNS FROM ".$table2."";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            if ($row['Field'] !== "autoId")
            {
                if ($row['Type'] === "blob")
                {
                    $fields2[$row['Field']] = "CAST(AES_DECRYPT(".$row['Field'].", SHA2('".$phrase."', 512)) AS CHAR) as ".$row['Field']."";
                }
                else
                {    
                    $fields2[$row['Field']] = $row['Field'];
                }
            }
        }

        $sql = "SELECT ".implode(", ", $fields).", lang.* FROM ".$table." as page LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM (SELECT ".implode(", ", (array)$fields2)." FROM $table2) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t GROUP BY pageId) as lang ON page.pageId = lang.pageId WHERE page.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
        //echo __LINE__." ". $sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $pageData[] = $row;
        }
        
        $replaceLang = getReplaceLang();
        
        $first = true;
        
        foreach ($pageData as $key => $value)
        {
            if ($first)
            {
                $first = false;
                echo "<div class=\"form-group row\">";
                    echo "<label for=\"headerFile[".$value['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$editOwnPage[1]." ";
                    echo "</label>";

                    echo "<div class=\"col-sm-10\">";
                        
                         echo "<input type = \"hidden\" class = \"syncData\" data-replace_id = \"headerImage[".$value['masterTableKey']."]\" id = \"headerImage\" data-replace_table = \"".$replaceTable[PREFIX."own_pages"]."\"> ";
                        echo "<button id = \"selectHeaderImage\" class = \"btn btn-default\" data-target_input = \"headerImage\">"."Välj bild"."</button>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"form-group row\">";
                    echo "<label for=\"note[".$value['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$editOwnPage[2]." ";
                    echo "</label>";

                    echo "<div class=\"col-sm-10\">";
                        echo "<input type = \"text\" ";
                        if (empty($value['tableKey']))
                            {
                                echo "id = \"subHeader[".$value['masterTableKey']."_".$replaceLang[reset($displayLang)]."]\"";
                            }
                            else
                            {
                                echo "id = \"subHeader[".$value['tableKey']."]\"";
                            }
                        echo "class = \"syncData form-control\" data-replace_table = \"".$replaceTable[PREFIX."own_pages"]."\"  value = \"".$value['subHeader']."\">";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"form-group row\">";
                    echo "<label for=\"note[".$value['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$editOwnPage[4]." ";
                    echo "</label>";

                    echo "<div class=\"col-sm-10\">";
                        echo "<input type = \"text\" ";
                        if (empty($value['tableKey']))
                            {
                                echo "id = \"header[".$value['masterTableKey']."_".$replaceLang[reset($displayLang)]."]\"";
                            }
                            else
                            {
                                echo "id = \"header[".$value['tableKey']."]\"";
                            }
                        echo "class = \"syncData form-control\" data-replace_table = \"".$replaceTable[PREFIX."own_pages"]."\" value = \"".$value['header']."\">";
                    echo "</div>";
                echo "</div>";
            }
        
            echo "<div class=\"form-group row\">";
                echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu_array[1][$row['lang']]." ";
                if ($numRows > 1)
                {
                    echo "[".$row['lang']."]";
                }
               
                echo "</label>";

                echo "<div class=\"col-sm-10\">";
                    echo "<textarea class = \"tinyMceArea\" ";

                    if (empty($value['tableKey']))
                    {
                        echo "id = \"note[".$value['masterTableKey']."_".$replaceLang[reset($displayLang)]."]\"";
                    }
                    else
                    {
                        echo "id = \"note[".$value['tableKey']."]\"";
                    }

                    echo " data-replace_table = \"".$replaceTable[PREFIX."own_pages"]."\">";
                        echo $value['note'];
                    echo "</textarea>";
                echo "</div>";
            echo "</div>";
            //echo __LINE__." Hmmmmm.... ".$value['lang']."<br>";
             showMetaText($value['lang']);
        }
    }

    function showAjax_menu()
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
        $enumText = $langStrings['enumText'];
        
        $showAjax_menu = $langStrings['showAjax_menu'];

        $showAjax_menu_array = getLangstringsArray('showAjax_menu_array', $displayLang);
        
        $editOwnPage = $langStrings['editOwnPage'];

        $replaceLang = getReplaceLang();
        $replaceTable = getReplaceTable();
        
        $table = "`".PREFIX."menu`";
        $table2 = "`".PREFIX."menu_lang`";

        $sql = "SELECT * FROM (SELECT node.menuId, node.lft, node.rgt, node.tableKey as masterTableKey, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) as char) as folder, CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) as char) as file, CAST(AES_DECRYPT(node.icon, SHA2('".$phrase."', 512)) as char) as icon, node.type, node.displayMenu, COUNT(parent.lft) - 1 as depth
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

        /*$showAjax_menu[1] = "Katalog";
        $showAjax_menu[2] = "Fil";
        
        $showAjax_menu_array[1]['sv'] = "Meny namn";*/

        $first = true;
        
        $i = 1;
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            echo "<div class=\"form-group row\">";
                echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu_array[1][$row['lang']]." ";
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
                
                $sql = "SHOW COLUMNS FROM ".$table." LIKE 'type'";
                $result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                
                while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
                {
                    $temp = $row2['Type'];
                    $temp = str_replace("enum(", '', $temp);
                    $temp = str_replace(")", '', $temp);
                    $temp = str_replace("'", '', $temp);
                    
                    $enum = array_filter(array_map("trim", explode(",", $temp)));
                }
                
                $i = 1;
                
                foreach ($enum as $key => $value)
                {
                    $emnumvalue[$value] = $enumText[$i];
                    $i++;
                }
                
                echo "<div class=\"form-group row\">";
                    echo "<label for=\"type[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu[3]." ";
                    if ($numRows > 1)
                    {
                        echo "[".$row['lang']."]";
                    }
                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<select class=\"form-control selectpicker2 show-tick\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"type[".$row['masterTableKey']."]\" data-target_div = \"ajax_".$_POST['replaceTable']."\">";
                            if (empty($row['type']))
                            {
                                $row['type'] = "file";
                            }
                            foreach ($emnumvalue as $key => $value)
                            {
                                echo "<option value = \"".$key."\"";
                                if ($row['type'] == $key)
                                {
                                    echo " "."selected";
                                }
                                echo ">".$value."</option>";
                            }
                        echo "</select>";
                    echo "</div>";
                echo "</div>";
                if ($row['type'] == "text")
                {
                    editOwnPage();
                }
                elseif ($row['type'] == "gallery")
                {
                    editGalleryPage();
                }
                else
                {
                    
                    
                    echo "<div class=\"form-group row\">";
                        echo "<label for=\"displayMenu[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu[4]." ";

                        echo "</label>";
                        echo "<div class=\"col-sm-10\">";
                            echo "<div class=\"form-check checkbox-slider--b\">";
                                echo "<label>";
                                    echo "<input id = \"displayMenu[".$row['masterTableKey']."]\" type=\"checkbox\" class = \"syncData\" data-replace_table = \"".$_POST['replaceTable']."\"";
                                        if ((int)$row['displayMenu'] > 0)
                                        {
                                            echo " "."checked";
                                        }
                                    echo "><span>".$showAjax_menu[4]."</span>";
                                echo "</label>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                    
                    echo "<div class=\"form-group row\">";
                        echo "<label for=\"folder[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu[1]." ";

                        echo "</label>";
                        echo "<div class=\"col-sm-10\">";
                            echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"folder[".$row['masterTableKey']."]\" value = \"".$row['folder']."\"";

                            echo ">";
                        echo "</div>";
                    echo "</div>";

                    echo "<div class=\"form-group row\">";
                        echo "<label for=\"file[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_menu[2]." ";
                        echo "</label>";
                        echo "<div class=\"col-sm-10\">";
                            echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"file[".$row['masterTableKey']."]\" value = \"".$row['file']."\"";
                           echo ">";
                        echo "</div>";
                    echo "</div>";

                    echo "<div class=\"form-group row\">";
                        echo "<label for=\"headerFile[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$editOwnPage[1]." ";
                        echo "</label>";

                        echo "<div class=\"col-sm-10\">";

                             echo "<input type = \"hidden\" class = \"syncData\" data-replace_id = \"headerImage[".$row['masterTableKey']."]\" id = \"headerImage\" data-replace_table = \"".$_POST['replaceTable']."\"> ";
                            echo "<button id = \"selectHeaderImage\" class = \"btn btn-default\" data-target_input = \"headerImage\">"."Välj bild"."</button>";
                        echo "</div>";
                    echo "</div>";
                    
                    
                    
                    $table10 = "`".PREFIX."own_pages`";
                    $table11 = "`".PREFIX."own_pages_lang`";

                    $sql = "SELECT * FROM (SELECT node.pageId , node.lft, node.rgt, node.tableKey as masterTableKey, COUNT(parent.lft) - 1 as depth
                    FROM (SELECT * FROM ".$table10." WHERE tableKey = '".mysqli_real_escape_string($link, $row['masterTableKey'])."') AS node,
                          (SELECT * FROM ".$table10." WHERE tableKey = '".mysqli_real_escape_string($link, $row['masterTableKey'])."') AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
                    ORDER BY node.lft) as menu LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT pageId, tableKey, 
                          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
                          CAST(AES_DECRYPT(subHeader, SHA2('".$phrase."', 512)) AS CHAR) as subHeader,
                          CAST(AES_DECRYPT(header, SHA2('".$phrase."', 512)) AS CHAR) as header,
                          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table11) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
                          ) AS lang ON menu.pageId  = lang.pageId  GROUP BY lang.lang ORDER BY menu.lft";
                    //echo __LINE__." ".$sql."<br>";
                    $result4 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                    
                    while ($row4 = mysqli_fetch_array($result4))
                    {    
                        echo "<div class=\"form-group row\">";
                            echo "<label for=\"note[".$row4['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$editOwnPage[2]." ";
                            echo "</label>";

                            echo "<div class=\"col-sm-10\">";
                                echo "<input type = \"text\" ";
                                if (empty($row4['tableKey']))
                                    {
                                        echo "id = \"subHeader[".$row4['masterTableKey']."_".$replaceLang[reset($displayLang)]."]\"";
                                    }
                                    else
                                    {
                                        echo "id = \"subHeader[".$row4['tableKey']."]\"";
                                    }
                                echo "class = \"syncData form-control\" data-replace_table = \"".$replaceTable[PREFIX."own_pages"]."\"  value = \"".$row4['subHeader']."\">";
                            echo "</div>";
                        echo "</div>";

                        echo "<div class=\"form-group row\">";
                            echo "<label for=\"note[".$row4['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$editOwnPage[4]." ";
                            echo "</label>";

                            echo "<div class=\"col-sm-10\">";
                                echo "<input type = \"text\" ";
                                if (empty($row4['tableKey']))
                                    {
                                        echo "id = \"header[".$row4['masterTableKey']."_".$replaceLang[reset($displayLang)]."]\"";
                                    }
                                    else
                                    {
                                        echo "id = \"header[".$row4['tableKey']."]\"";
                                    }
                                echo "class = \"syncData form-control\" data-replace_table = \"".$replaceTable[PREFIX."own_pages"]."\" value = \"".$row4['header']."\">";
                            echo "</div>";
                        echo "</div>";
                        
                        showMetaText($row['lang']);
                    }
                }
            }
        }
    }

    function getRefTypes()
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
        
        $showAjaxRefObjects = $langStrings['showAjaxRefObjects'];

        $showAjaxRefObjects_array = getLangstringsArray('showAjaxRefObjects_array', $displayLang);

        $table = "`".PREFIX."ref_types`";
        $table2 = "`".PREFIX."ref_types_lang`";

        $sql = "SELECT * FROM (SELECT node.typeId , node.lft, node.rgt, node.tableKey as masterTableKey, COUNT(parent.lft) - 1 as depth
        FROM (SELECT * FROM ".$table.") AS node,
              (SELECT * FROM ".$table.") AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT typeId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.typeId = lang.typeId ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $numRows = mysqli_num_rows($result);
        
        $first = true;
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $type[] = $row;
        }
        
        return $type;
    }

    function getObjectTypes()
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
        
        $showAjaxRefObjects = $langStrings['showAjaxRefObjects'];

        $showAjaxRefObjects_array = getLangstringsArray('showAjaxRefObjects_array', $displayLang);

        $table = "`".PREFIX."ref_properties`";
        $table2 = "`".PREFIX."ref_properties_lang`";

        $sql = "SELECT * FROM (SELECT node.propertiesId, node.lft, node.rgt, node.tableKey as masterTableKey, COUNT(parent.lft) - 1 as depth
        FROM (SELECT * FROM ".$table.") AS node,
              (SELECT * FROM ".$table.") AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT propertiesId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.propertiesId = lang.propertiesId ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $numRows = mysqli_num_rows($result);
        
        $first = true;
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $ref_properties[] = $row;
        }
        
        return $ref_properties;
    }    

    function ref_properties2object($objectId = null)
    {
        global $link;

        global $phrase;

        
        $table = "`".PREFIX."ref_properties2object`";
        

        $sql = "SELECT * FROM ".$table." WHERE objectId = '".$objectId."'";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $properties[$row['propertiesId']] = $row['propertiesId'];
        }
        
        return $properties;
    }

    function showAjaxRefObjects()
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

        $replaceTable = getReplaceTable();
        
        $langStrings = getlangstrings();
        
        $showAjaxRefObjects = $langStrings['showAjaxRefObjects'];

        $showAjaxRefObjects_array = getLangstringsArray('showAjaxRefObjects_array', $displayLang);

        $table = "`".PREFIX."ref_objects`";
        $table2 = "`".PREFIX."ref_objects_lang`";

        $sql = "SELECT * FROM (SELECT node.referenceId , node.lft, node.rgt, node.tableKey as masterTableKey, node.refType, COUNT(parent.lft) - 1 as depth
        FROM (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS node,
              (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT referenceId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang,
              CAST(AES_DECRYPT(header, SHA2('".$phrase."', 512)) AS CHAR) as header, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.referenceId = lang.referenceId ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $numRows = mysqli_num_rows($result);
        
        $first = true;
        
        $first2 = true;
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            if ($first)
            {
                $first = false;
                
                echo "<div class=\"form-group row\">";
            
                    echo "<label for=\"image[".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefObjects[4]."";
                
                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                        
                        echo "<button class = \"btn btn-default editImageGallery\" data-replace_table = \"".$replaceTable[PREFIX."ref_objects_image"]."\" id=\"imageGallery[".$row['masterTableKey']."]\">".$showAjaxRefObjects[5]."</button>";
                
                    echo "</div>";
                echo "</div>";
                
                echo "<div class=\"form-group row\">";
            
                    echo "<label for=\"refType [".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefObjects[2]."";
                
                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                        $refTypes = getRefTypes();
                
                        echo "<select class = \"form-control selectpicker2 show-tick\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"refType[".$row['masterTableKey']."]\" data-size = \"5\">";
                            foreach ($refTypes as $key => $value)
                            {
                                echo "<option value = \"".$value['masterTableKey']."\"";
                                
                                    if ($row['refType'] === $value['typeId'])
                                    {
                                        echo " "."selected";
                                    }
                                
                                echo ">".$value['note']."</option>";
                            }
                        echo "</select>";
                
                    echo "</div>";
                echo "</div>";
                
                echo "<div class=\"form-group row\">";
            
                    echo "<label for=\"refType [".$row['masterTableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefObjects[3]."";
                
                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                        $objectTypes = getObjectTypes();
                
                        $selectedProperties = ref_properties2object($row['referenceId']);
                
                        echo "<select class = \"form-control selectpicker2 show-tick\" data-replace_table = \"".$replaceTable[PREFIX."ref_properties2object"]."\" id=\"propertiesId[".$row['masterTableKey']."]\" multiple data-size = \"5\">";
                            foreach ($objectTypes as $key => $value)
                            {
                                if ($old_depth > $value['depth'])
                                {
                                    for ($i = 0; $i < ($old_depth - (int)$value['depth']); $i++)
                                    {
                                        echo "</optgroup>";
                                    }
                                }
                                
                                if ((int)($value['lft'] + 1) < (int)$value['rgt'])
                                {
                                    echo "<optgroup label=\"".$value['note']."\">";
                                }
                                else
                                {
                                   echo "<option value = \"".$value['masterTableKey']."\"";
                                        if ((int)$value['depth'] > 0)
                                        {
                                            echo " "."style = \"margin-left : ".((int)$value['depth']*15)."px;\"";
                                        }

                                        if (array_key_exists($value['propertiesId'], (array)$selectedProperties))
                                        {
                                            echo " "."selected";
                                        }

                                    echo ">".$value['note']."</option>"; 
                                }
                                
                                $old_depth = (int)$value['depth'];
                                
                            }
                            for ($i = 0; $i < $old_depth; $i++)
                            {
                                echo "</optgroup>";
                            }
                
                        echo "</select>";
                
                    echo "</div>";
                echo "</div>";
                
            }
            
            
            echo "<div class=\"form-group row\">";
            
                if ($numRows > 1)
                {
                    echo "<label for=\"header[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefObjects_array[1][$row['lang']]." [".$row['lang']."] ";
                }
                else
                {
                    echo "<label for=\"header[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefObjects_array[1][$row['lang']]."";
                }

                echo "</label>";
                echo "<div class=\"col-sm-10\">";
                    echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"header[".$row['tableKey']."]\" value = \"".$row['header']."\" data-replace_table = \"".$replaceTable[PREFIX."ref_objects"]."\"";
                    
                    if ($first2)
                    {
                        $first2 = false;
                        
                        echo " "."data-reload_tree=\"tree_".$_POST['replaceTable']."\"";
                    }

                    echo ">";
                echo "</div>";
            echo "</div>";
            
            echo "<div class=\"form-group row\">";
            
                if ($numRows > 1)
                {
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefObjects_array[CRYPT_SHA256][$row['lang']]." [".$row['lang']."] ";
                }
                else
                {
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefObjects_array[2][$row['lang']]."";
                }

                echo "</label>";
                echo "<div class=\"col-sm-10\">";
                    echo "<textarea class = \"tinyMceArea\" placeholder = \"".$showAjaxRefObjects_array[3][$row['lang']]."\" id = \"note[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."ref_objects"]."\">";
                        echo $row['note'];
                    echo "</textarea>";
                echo "</div>";
            echo "</div>";
        }
    }
    
        
    function showAjaxRefProperties()
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
        $enumText = $langStrings['enumText'];
        
        $showAjaxRefProperties = $langStrings['showAjaxRefProperties'];

        $showAjaxRefProperties_array = getLangstringsArray('showAjaxRefProperties_array', $displayLang);

        $table = "`".PREFIX."ref_properties`";
        $table2 = "`".PREFIX."ref_properties_lang`";

        $sql = "SELECT * FROM (SELECT node.propertiesId , node.lft, node.rgt, node.tableKey as masterTableKey, COUNT(parent.lft) - 1 as depth
        FROM (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS node,
              (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT propertiesId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note,
              CAST(AES_DECRYPT(description, SHA2('".$phrase."', 512)) AS CHAR) as description FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.propertiesId = lang.propertiesId ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $numRows = mysqli_num_rows($result);
        
        $first = true;
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
             echo "<div class=\"form-group row\">";
            
                if ($numRows > 1)
                {
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefProperties_array[1][$row['lang']]." [".$row['lang']."] ";
                }
                else
                {
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefProperties_array[1][$row['lang']]."";
                }

                echo "</label>";
                echo "<div class=\"col-sm-10\">";
                    echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"note[".$row['tableKey']."]\" value = \"".$row['note']."\"";
                    if ($first)
                    {
                        $first = false;
                        echo " "."data-reload_tree=\"tree_".$_POST['replaceTable']."\"";
                    }
                    echo ">";
                echo "</div>";
            echo "</div>";
            
            echo "<div class=\"form-group row\">";
            
                if ($numRows > 1)
                {
                    echo "<label for=\"description[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefProperties_array[1][$row['lang']]." [".$row['lang']."] ";
                }
                else
                {
                    echo "<label for=\"description[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefProperties_array[1][$row['lang']]."";
                }

                echo "</label>";
                echo "<div class=\"col-sm-10\">";
                    echo "<textarea class=\"tinyMceArea\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"description[".$row['tableKey']."]\">";
                        echo $row['description'];
                    echo "</textarea>";
                    
                echo "</div>";
            echo "</div>";
        }
    }

    function showAjaxRefTypes()
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
        $enumText = $langStrings['enumText'];
        
        $showAjaxRefTypes = $langStrings['showAjaxRefTypes'];

        $showAjaxRefTypes_array = getLangstringsArray('showAjaxRefTypes_array', $displayLang);

        $table = "`".PREFIX."ref_types`";
        $table2 = "`".PREFIX."ref_types_lang`";

        $sql = "SELECT * FROM (SELECT node.typeId , node.lft, node.rgt, node.tableKey as masterTableKey, COUNT(parent.lft) - 1 as depth
        FROM (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS node,
              (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT typeId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(description, SHA2('".$phrase."', 512)) AS CHAR) as description, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.typeId = lang.typeId ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $numRows = mysqli_num_rows($result);
        
        $first = true;
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
             echo "<div class=\"form-group row\">";
            
                if ($numRows > 1)
                {
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefTypes_array[1][$row['lang']]." [".$row['lang']."] ";
                }
                else
                {
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefTypes_array[1][$row['lang']]."";
                }

                echo "</label>";
                echo "<div class=\"col-sm-10\">";
                    echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"note[".$row['tableKey']."]\" value = \"".$row['note']."\"";
                    if ($first)
                    {
                        $first = false;
                        
                        echo " "."data-reload_tree=\"tree_".$_POST['replaceTable']."\"";
                        
                    }
                    echo ">";
                echo "</div>";
            echo "</div>";
            
            echo "<div class=\"form-group row\">";
            
                if ($numRows > 1)
                {
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefProperties_array[2][$row['lang']]." [".$row['lang']."] ";
                }
                else
                {
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefProperties_array[2][$row['lang']]."";
                }

                echo "</label>";
                echo "<div class=\"col-sm-10\">";
                    echo "<textarea class=\"tinyMceArea\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"description[".$row['tableKey']."]\">";
                        echo $row['description'];
                echo "</textarea>";
            echo "</div>";
        }
    }

    function showAjaxRefSettings()
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
        
        $showAjaxRefSettings = $langStrings['showAjaxRefSettings'];

        $showAjaxRefSettings_array = getLangstringsArray('showAjaxRefSettings_array', $displayLang);

        $table = "`".PREFIX."ref_settings`";
        $table2 = "`".PREFIX."ref_settings_lang`";

        $sql = "SELECT * FROM (SELECT node.settingId , CAST(AES_DECRYPT(node.setting, SHA2('".$phrase."', 512)) AS CHAR) as setting, CAST(AES_DECRYPT(node.data, SHA2('".$phrase."', 512)) AS CHAR) as data, node.lft, node.rgt, node.tableKey as masterTableKey, COUNT(parent.lft) - 1 as depth
        FROM (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS node,
              (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT settingId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.settingId = lang.settingId GROUP BY lang.lang ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $numRows = mysqli_num_rows($result);
        
        $i = 1;
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
             echo "<div class=\"form-group row\">";
            
                if ($numRows > 1)
                {
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefSettings_array[1][$row['lang']]." [".$row['lang']."] ";
                }
                else
                {
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefSettings_array[1][$row['lang']]."";
                }

                echo "</label>";
                echo "<div class=\"col-sm-10\">";
                    echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"note[".$row['tableKey']."]\" value = \"".$row['note']."\"";
                        if ($i === 1)
                        {
                            echo " "."data-reload_tree=\"tree_".$_POST['replaceTable']."\"";
                        }
                    echo ">";
                echo "</div>";
            echo "</div>";
            
            if ($i == $numRows)
            {
                echo "<div class=\"form-group row\">";
            
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefSettings[1]."";

                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"data[".$row['masterTableKey']."]\" value = \"".$row['data']."\"";

                        echo ">";
                    echo "</div>";
                echo "</div>";
                
                echo "<div class=\"form-group row\">";
            
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefSettings[2]."";

                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"setting[".$row['masterTableKey']."]\" value = \"".$row['setting']."\"";

                        echo ">";
                    echo "</div>";
                echo "</div>";
            }
        }
    }

    function showAjaxRefObjectsImage()
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
        
        $replaceLang = getReplaceLang();
        
        $showAjaxRefObjectsImage= $langStrings['showAjaxRefObjectsImage'];

        $showAjaxRefObjectsImage_array = getLangstringsArray('showAjaxRefObjectsImage_array', $displayLang);

        $table = "`".PREFIX."ref_objects_image`";
        $table2 = "`".PREFIX."ref_objects_image_lang`";

        $sql = "SELECT CASE WHEN MAX(imageId) IS NULL THEN 1 ELSE MAX(imageId) +1 END as imageId FROM ".$table."";
        //echo __LINE__." ".$sql."<br>";
        
        $sql = "SELECT * FROM (SELECT node.imageId, node.lft, node.rgt, node.tableKey as masterTableKey, COUNT(parent.lft) - 1 as depth, CAST(AES_DECRYPT(node.fileName, SHA2('".$phrase."', 512)) AS CHAR) as file
        FROM (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS node,
              (SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."') AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT imageId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.imageId = lang.imageId GROUP BY lang.lang ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $numRows = mysqli_num_rows($result);
        
        $i = 1;
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            if (empty($row['lang']))
            {
                $t = $i -1;
                $row['lang'] = $displayLang[$t];
            }
            
            if ($i == 1)
            {
                echo "<img src=\"../".$_SESSION['folderUrl']."source/".$row['file']."\" class=\"img-thumbnail\" alt=\"".$row['file']."\">";
            }
            
             echo "<div class=\"form-group row\">";
            
                if ($numRows > 1)
                {
                    echo "<label for=\"note[";
                    if (!empty($row['tableKey']))
                    {
                        echo $row['tableKey'];
                    }
                    else
                    {
                        echo $row['masterTableKey']."_".$replaceLang[$row['lang']];
                    }
                    
                    echo "]\" class=\"col-sm-4 col-form-label\">".$showAjaxRefObjectsImage_array[1][$row['lang']]." [".$row['lang']."] ";
                }
                else
                {
                   echo "<label for=\"note[";
                    if (!empty($row['tableKey']))
                    {
                        echo $row['tableKey'];
                    }
                    else
                    {
                        echo $row['masterTableKey']."_".$replaceLang[$row['lang']];
                    }
                    
                    echo "]\" class=\"col-sm-4 col-form-label\">".$showAjaxRefObjectsImage_array[1][$row['lang']]."";
                }

                echo "</label>";
                echo "<div class=\"col-sm-8\">";
                    echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"note[";
                    if (!empty($row['tableKey']))
                    {
                        echo $row['tableKey'];
                    }
                    else
                    {
                        echo $row['masterTableKey']."_".$replaceLang[$row['lang']];
                    }
                    echo "]\" value = \"".$row['note']."\"";
                        if ($i === 1)
                        {
                            echo " "."data-reload_tree=\"tree_".$_POST['replaceTable']."\" data-extra_key = \"".$row['masterTableKey']."\"";
                        }
                    echo ">";
                echo "</div>";
            echo "</div>";
            
            /*if ($i == $numRows)
            {
                echo "<div class=\"form-group row\">";
            
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefSettings[1]."";

                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"data[".$row['masterTableKey']."]\" value = \"".$row['data']."\"";

                        echo ">";
                    echo "</div>";
                echo "</div>";
                
                echo "<div class=\"form-group row\">";
            
                    echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxRefSettings[2]."";

                    echo "</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<input type=\"text\" class=\"form-control syncData\" data-replace_table = \"".$_POST['replaceTable']."\" id=\"setting[".$row['masterTableKey']."]\" value = \"".$row['setting']."\"";

                        echo ">";
                    echo "</div>";
                echo "</div>";
            }*/
            
            $i++;
        }
    }
?>