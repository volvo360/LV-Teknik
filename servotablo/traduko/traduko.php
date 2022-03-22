<?php
session_start();
include_once("../../common/db.php");
include_once("./../../common/userData.php");
include_once("../../administrado/ext/theme/nav.php");
include_once("./../../common/crypto.php");
include_once("./../../common/modal.php");


generateTempTableKey();

function generateTempTableKey()
{
    $replaceTable = getReplaceTable();

    do
    {
        $key = generateStrongPassword();

        if (!array_key_exists($key, (array)$replaceTable))
        {
            $run = false;
        }

    } while ($run);

    $_SESSION['tempTableKey'] = $key;

    return $key;
}

function generateTempTableKeyAll()
{
    $replaceTable = getReplaceTable();

    do
    {
        $key = generateStrongPassword();

        if (!array_key_exists($key, (array)$replaceTable))
        {
            $run = false;
        }

    } while ($run);

    $_SESSION['tempTableKeyAll'] = $key;

    return $key;
}
	

function showTranslationOther()
{
    global $link;
    global $link_k;

    global $phrase;
    global $phrase_k;

    $lang = $settings['def_lang'] = 'sv';
    $country = $settings['def_country'] = 'SE';

    $table = "`".PREFIX."translation`";
    $table2 = "`".PREFIX."competence_lang`";

    $tableX = "`".PREFIX."languages`";

    checkTable($tableX);

    $replaceTable = getReplaceTable();

    $langString = getlangstrings();

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
    $showTranslationTree = $langStrings['showTranslationTree'];

    $showTranslationTree_array = getLangstringsArray('showTranslationTree_array', $displayLang);

    $sql = "SELECT table_schema as database_name, table_name
            FROM information_schema.tables
            WHERE table_type = 'BASE TABLE'
                AND table_name like '%_lang'
            ORDER BY table_schema,
                table_name;";

    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        if (endsWith($row['table_name'],"_lang"))
        {
            $display[$replaceTable[$row['table_name']]] = str_replace(PREFIX,'',$row['table_name']);
        }
    }

    echo "<div class = \"row\">";
        echo "<div class =\"col-md-2\" style = \"max-height : 53vh; overflow : auto;\">";
            echo "<div id = \"tree_langTables\" class =\"fancyTreeClass\" data-ajax_target = \"ajaxTree_langTables\" data-replace_table = \"".generateTempTableKey()."\">";
                echo "<ul id = \"tree_langTables-data\" style = \"display:none;\" >";
                    if (is_array($display))
                    {
                        foreach ($display as $key => $value)
                        {
                            echo "<li id = \"".$key."\" class = \"getTranslationLang\">".$value."</li>";
                        }
                    }
                echo "</ul>";
            echo "</div><br>";

        echo "</div>";

        echo "<div class = \"col-md-10\" id = \"ajaxTree_langTables\" style = \"max-height : 53vh; height : 53vh; overflow : auto;\">";

        echo "</div>";
    echo "</div>";/**/
}

function displayTraduko()
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
    $displayTraduko = $langStrings['displayTraduko'];

    $displayTraduko_array = getLangstringsArray('displayTraduko_array', $displayLang);
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."translation_var`";
    //$table2 = "`".PREFIX."administrado_menu_lang`";
    checkTable($table);
    unset($data);

    $sql = "SELECT *, CAST(AES_DECRYPT(variable, SHA2('".$phrase."', 512)) as CHAR) as variable FROM $table ORDER BY variable";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
     echo "<div class = \"row\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 59vh; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."translation"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."translation"]."\" data-replace_table = \"".$replaceTable[PREFIX.'translation']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."translation"]."-data\" style = \"display:none;\" >";
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

                            echo ">".$row['variable']."<ul>";
                        }
                        else
                        {
                            echo "<li id = \"".$row['tableKey']."\"";

                                if (array_key_exists($row['tableKey'], (array)$display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".$row['variable']."</li>";
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

            echo "<form id = \"addForm_".$replaceTable[PREFIX."translation"]."\">";
                echo $displayTraduko[1]."<br>";
                echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";

                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."translation"]."\" data-replace_table = \"".$replaceTable[PREFIX."translation"]."\" data-replace_lang = \"".$replaceLang[$lang]."\" data-target_form = \"addForm_".$replaceTable[PREFIX."translation"]."\">".$displayTraduko[2]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"height : 59vh; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."translation"]."\">";
               // renderManualEdit($lang, $rowData);
            echo "</div>";
        echo "</div>";

     //echo "</div>";
echo "</div>";
    
}

function displayBodyTraduko()
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
    
    $langStrings = getlangstrings();
    $displayBodyTraduko = $langStrings['displayBodyTraduko'];

    $displayBodyTraduko_array = getLangstringsArray('displayBodyTraduko_array', $displayLang);
    
    $tabs['displayTraduko'] = $displayBodyTraduko[2];
    $tabs['showTranslationOther'] = $displayBodyTraduko[3];
    
    $firstTab = 'displayTraduko';
    //$displayBodyTraduko[1] = "Strängar i tjänsten";
    
    //echo "<h1 class=\"m-0\">".$displayBodyTraduko[1]."</h1>";
    
    echo "<ul class=\"nav nav-tabs\" id=\"myTab\" role=\"tablist\">";
        foreach ($tabs as $key => $value)
        {
            echo "<li class=\"nav-item\" role=\"presentation\">";
            echo "<a class=\"nav-link";
                if ($key == $firstTab)
                {
                    echo " "."active";
                }
            echo "\" id=\"".$key."-tab\" data-toggle=\"tab\" href=\"#".$key."\" role=\"tab\" aria-controls=\"".$key."\" aria-selected=\"";
                if ($key == $firstTab)
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
        foreach ($tabs as $key => $value)
        {
            echo "<div class=\"tab-pane fade";
            if ($key == $firstTab)
            {
                echo " ". "show active";
            }
            echo "\" id=\"".$key."\" role=\"tabpanel\" aria-labelledby=\"".$key."-tab\">";
            echo "<br>";
                call_user_func($key);
            echo "</div>";
        }
        
    echo "</div>";
}

function displayBodyTraduko2()
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
    $displayBodyTraduko = $langStrings['displayBodyTraduko'];

    $displayBodyTraduko_array = getLangstringsArray('displayBodyTraduko_array', $displayLang);

    echo "<div class=\"panel-header panel-header-sm\">";

    echo "</div>";

    echo "<div class=\"content\" style = \"max-height : 44vh; height : 44vh; overflow : auto\">";
        echo "<div class=\"row\" style = \"height : 95%;\">";
            echo "<div class=\"col-md-12\" style = \"height : 99%;\">";
                echo "<div class=\"card\"style = \"height : 99%;\">";
                    echo "<div class=\"card-header\">";
                        echo "<h1>".$displayBodyTraduko[1]."</h1>";
                echo "</div>";
                echo "<div class=\"card-body\">";
                    echo "<div class=\"row\">";
                        echo "<div class=\"col-md-12\">";
                            displayBodyTraduko();
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
	displayBodyTraduko2();;
	displayFooterAdministrado();
    printScripts();
    
}
?>