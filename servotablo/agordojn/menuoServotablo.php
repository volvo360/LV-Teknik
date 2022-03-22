<?php
include_once("../../common/db.php");
include_once("../../common/crypto.php");
include_once("../../common/userData.php");
include_once("../../administrado/ext/theme/nav.php");

function displayMenuAdministrado()
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
    $displayMenuAdministrado = $langStrings['displayMenuAdministrado'];

    $displayMenuAdministrado_array = getLangstringsArray('displayMenuAdministrado_array', $displayLang);
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."administrado_menu`";
    $table2 = "`".PREFIX."administrado_menu_lang`";
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
    
     echo "<div class = \"row\" style = \"max-height : 58vh; height : 58vh; overflow : auto\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."administrado_menu"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."administrado_menu"]."\" data-replace_table = \"".$replaceTable[PREFIX.'administrado_menu']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."administrado_menu"]."-data\" style = \"display:none;\" >";
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

            echo "<form id = \"addForm_".$replaceTable[PREFIX."administrado_menu"]."\">";
                echo $displayMenuAdministrado[1]."<br>";
                echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";

                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."administrado_menu"]."\" data-replace_table = \"".$replaceTable[PREFIX."administrado_menu"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$displayMenuAdministrado[2]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."administrado_menu"]."\">";
               // renderManualEdit($lang, $rowData);
            echo "</div>";
        echo "</div>";

     //echo "</div>";
echo "</div>";
    
}

function displayServotablo()
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
    $displayMenuAdministrado = $langStrings['displayMenuAdministrado'];

    $displayMenuAdministrado_array = getLangstringsArray('displayMenuAdministrado_array', $displayLang);
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."servotablo_menu`";
    $table2 = "`".PREFIX."servotablo_menu_lang`";
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
    
     echo "<div class = \"row\" style = \"max-height : 58vh; height : 58vh; overflow : auto\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."servotablo_menu"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."servotablo_menu"]."\" data-replace_table = \"".$replaceTable[PREFIX.'servotablo_menu']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."servotablo_menu"]."-data\" style = \"display:none;\" >";
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

            echo "<form id = \"addForm_".$replaceTable[PREFIX."servotablo_menu"]."\">";
                echo $displayMenuAdministrado[1]."<br>";
                echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";

                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."servotablo_menu"]."\" data-replace_table = \"".$replaceTable[PREFIX."servotablo_menu"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$displayMenuAdministrado[2]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."servotablo_menu"]."\">";
               // renderManualEdit($lang, $rowData);
            echo "</div>";
        echo "</div>";

     //echo "</div>";
echo "</div>";
    
}

function displayMenuTabs()
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
    $displayMenuTabs = $langStrings['displayMenuTabs'];

    $displayMenuTabs_array = getLangstringsArray('displayMenuTabs_array', $displayLang);
    
    $navs['displayMenuAdministrado'] = $displayMenuTabs[1];
    $navs['displayServotablo'] = $displayMenuTabs[2];
    
    $preselect = "displayServotablo";
    
    echo "<ul class=\"nav nav-tabs\" id=\"myTab\" role=\"tablist\">";
        foreach ($navs as $key => $value)
        {
            echo "<li class=\"nav-item\" role=\"presentation\">";
                echo "<a class=\"nav-link";
                    if ($key === $preselect)
                    {
                        echo " "."active";
                    }
                echo "\" id=\"".$key."-tab\" data-toggle=\"tab\" href=\"#".$key."\" role=\"tab\" aria-controls=\"".$key."\" aria-selected=\"";
                if ($key === $preselect)
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
                if ($key === $preselect)
                {
                    echo " "."show active";
                }
            echo "\" id=\"".$key."\" role=\"tabpanel\" aria-labelledby=\"".$key."-tab\">";
                echo "<br>";
                call_user_func($key);
            echo "</div>";
        }
    echo "</div>";
}

function displayBodyMenuServotablo()
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
    $displayBodyMenuServotablo = $langStrings['displayBodyMenuServotablo'];

    $displayBodyMenuServotablo_array = getLangstringsArray('displayBodyMenuServotablo_array', $displayLang);

    echo "<div class=\"panel-header panel-header-sm\">";

    echo "</div>";

    echo "<div class=\"content\" style = \"max-height : 65; height :  65vh; overflow : auto\">";
        echo "<div class=\"row\" style = \"height : 95%;\">";
            echo "<div class=\"col-md-12\" style = \"height : 99%;\">";
                echo "<div class=\"card\"style = \"height : 99%;\">";
                    echo "<div class=\"card-header\">";
                        echo "<h1>".$displayBodyMenuServotablo[1]."</h1>";
                echo "</div>";
                echo "<div class=\"card-body\">";
                    echo "<div class=\"row\" style = \"max-height :  65vh; height :  65vh; overflow : auto\">";
                        echo "<div class=\"col-md-12\">";
                            displayMenuTabs();
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
	displayBodyMenuServotablo();
	displayFooterAdministrado();
    printScripts();
}
?>