<?php

include_once("../common/db.php");
include_once("../common/crypto.php");
include_once("../common/userData.php");

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

include_once("../common/modal.php");

syncFieldOwnPage();

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

function displayHeader()
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
    $displayMenu = $langStrings['displayMenu'];

    $displayMenu_array = getLangstringsArray('displayMenu_array', $displayLang);
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."menu`";
    $table2 = "`".PREFIX."menu_lang`";
    checkTable($table2);
    unset($data);

    $sql = "SELECT * FROM (SELECT node.menuId, node.lft, node.rgt, node.tableKey, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) AS CHAR) as folder, CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) AS CHAR) as file,  CAST(AES_DECRYPT(node.icon, SHA2('".$phrase."', 512)) AS CHAR) as icon, (COUNT(parent.menuId) - 1) AS depth
    FROM $table AS node,
          $table AS parent
    WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
    ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
          GROUP BY menuId) AS lang ON menu.menuId = lang.menuId ORDER BY menu.lft";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
     echo "<div class = \"row\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 55vh; height : 55vh; overflow : auto\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."menu"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."menu"]."\" data-replace_table = \"".$replaceTable[PREFIX.'menu']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."menu"]."-data\" style = \"display:none;\" >";
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

            echo "<form id = \"addForm_".$replaceTable[PREFIX."menu"]."\">";
                echo $displayMenu[1]."<br>";
                echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";

                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."menu"]."\" data-replace_table = \"".$replaceTable[PREFIX."menu"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$displayMenu[2]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 55vh; height : 55vh; overflow : auto\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."menu"]."\">";
               // renderManualEdit($lang, $rowData);
            echo "</div>";
        echo "</div>";

     //echo "</div>";
    echo "</div>";
}

function displayFooter2()
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
    $displayMenu = $langStrings['displayMenu'];

    $displayMenu_array = getLangstringsArray('displayMenu_array', $displayLang);
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."menu_footer`";
    $table2 = "`".PREFIX."menu_footer_lang`";
    $table10 = "`".PREFIX."menu_lang`";
    checkTable($table2);
    unset($data);

    $sql = "SELECT *, CASE WHEN masterLang.note2 IS NOT NULL THEN masterLang.note2 ELSE lang.note END as note  FROM (SELECT node.menuId, node.masterMenuId, node.lft, node.rgt, node.tableKey, (COUNT(parent.lft) - 1) AS depth
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
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
     echo "<div class = \"row\" >";
        echo "<div class = \"col-md-3\" style = \"max-height : 55vh; height : 55vh; overflow : auto\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."menu_footer"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."menu_footer"]."\" data-replace_table = \"".$replaceTable[PREFIX.'menu_footer']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."menu_footer"]."-data\" style = \"display:none;\" >";
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
                        $rowData[] = $row;

                        if ($oldDepth > (int)(int)$row['depth'])
                        {
                            for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                            {
                                echo "311</ul></li>";
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

            echo "<form id = \"addForm_".$replaceTable[PREFIX."menu_footer"]."\">";
                echo $displayMenu[1]."<br>";
                echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";

                $table = "`".PREFIX."menu`";
                $table2 = "`".PREFIX."menu_lang`";
                checkTable($table2);
                unset($data);

                $sql = "SELECT * FROM (SELECT node.menuId, node.lft, node.rgt, node.tableKey, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) AS CHAR) as folder, CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) AS CHAR) as file,  CAST(AES_DECRYPT(node.icon, SHA2('".$phrase."', 512)) AS CHAR) as icon, node.type, node.displayMenu, (COUNT(parent.menuId) - 1) AS depth
                FROM $table AS node,
                      $table AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
                ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
                      CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
                      CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
                      GROUP BY menuId) AS lang ON menu.menuId = lang.menuId ORDER BY menu.lft";
                //echo __LINE__." ".$sql."<br>";
                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                    if ($row['type'] == "deleted")
                    {
                        continue;
                    }
                    else if ((int)$row['displayMenu'] > 0)
                    {
                        $rowData2[] = $row;    
                    }
                }
    
                echo "<select class = \"form-control selectpicker2 show-tick\" data-replace_table = \"".$replaceTable[PREFIX."menu_footer"]."\" id = \"masterMenuId\" data-size = \"5\">";
                        echo "<option value = \"-1\"></option>";
    
                        foreach ($rowData2 as $key => $value)
                        {
                            echo "<option value = \"".$value['tableKey']."\"";
                                if ($value['depth'] > 0)
                                {
                                    echo " "."style = \"margin-left : ".((int)$value['depth']*15)."px;\"";
                                }
                            echo ">".$value['note']."</option>";
                        }
                    echo "</select>";
    
                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."menu_footer"]."\" data-replace_table = \"".$replaceTable[PREFIX."menu_footer"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$displayMenu[2]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 55vh; height : 55vh; overflow : auto\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."menu_footer"]."\">";
               // renderManualEdit($lang, $rowData);
            echo "</div>";
        echo "</div>";

     //echo "</div>";
echo "</div>";
    
}

function displayBodyMenu()
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
    $displayBodyMenu = $langStrings['displayBodyMenu'];

    $displayBodyMenu_array = getLangstringsArray('displayBodyMenu_array', $displayLang);

    echo "<div class=\"panel-header panel-header-sm\">";

    echo "</div>";

    echo "<div class=\"content\" > ";
        echo "<div class=\"row\" >";
            echo "<div class=\"col-md-12\" style = \"max-height : 77vh; height : 77vh; overflow : auto\">";
                echo "<div class=\"card\">";
                    echo "<div class=\"card-header\">";
                        echo "<h1>".$displayBodyMenu[1]."</h1>";
                echo "</div>";
                echo "<div class=\"card-body\">";
                    echo "<div class=\"row\">";
                        echo "<div class=\"col-md-12\">";
    
                            $navs['header'] = $displayBodyMenu[2];
                            $navs['footer2'] = $displayBodyMenu[3];
    
                            $preselectNav = "header";
    
                            echo "<ul class=\"nav nav-tabs\" id=\"myTab\" role=\"tablist\">";
                                foreach ($navs as $key => $value)
                                {
                                    echo "<li class=\"nav-item\" role=\"presentation\">";
                                        echo "<a class=\"nav-link";
                                            if ($key === $preselectNav)
                                            {
                                                echo " "."active";
                                            }
                                        echo "\" id=\"".$key."-tab\" data-toggle=\"tab\" href=\"#".$key."\" role=\"tab\" aria-controls=\"".$key."\" aria-selected=\"";
                                        if ($key == $preselectNav)
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
                            echo "<div class=\"tab-content\" id=\"myTabContent\">";
                                foreach ($navs as $key => $value)
                                {
                                    echo "<div class=\"tab-pane fade";
                                    if ($key === $preselectNav)
                                    {
                                        echo " "."show active";
                                    }
                                    echo "\" id=\"".$key."\" role=\"tabpanel\" aria-labelledby=\"".$key."-tab\" style = \" overflow-x: none;\">";
                                        echo "<br>";
                                        //echo __LINE__." "."display".ucfirst($key)."<br>";
                                        call_user_func("display".ucfirst($key));
                                    echo "</div>";
                                }
                            echo "</div>";
    
                           // displayHeader();
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
	displayBodyMenu();
	displayFooterAdministrado();

    print_modal_xl();

    printScripts();
    
}



?>