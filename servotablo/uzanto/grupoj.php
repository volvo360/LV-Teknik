<?php
include_once("../../common/db.php");
include_once("./../../common/userData.php");
include_once("../../administrado/ext/theme/nav.php");
include_once("./../../common/crypto.php");
include_once("./../../common/modal.php");

function displayGrupoj()
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
    $displayagordojn = $langStrings['displayagordojn'];

    $displayagordojn_array = getLangstringsArray('displayagordojn_array', $displayLang);
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."servotablo_group`";
    $table2 = "`".PREFIX."servotablo_group_lang`";
    checkTable($table);
    unset($data);

    $sql = "SELECT * FROM (SELECT node.groupId, node.lft, node.rgt, node.tableKey as masterTableKey, COUNT(parent.lft) - 1 as depth
        FROM ".$table." AS node,
              ".$table." AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT groupId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.groupId = lang.groupId GROUP BY lang.lang ORDER BY menu.lft";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
     echo "<div class = \"row\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 95vh; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."site_settings"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."site_settings"]."\" data-replace_table = \"".$replaceTable[PREFIX.'site_settings']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."site_settings"]."-data\" style = \"display:none;\" >";
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
                            echo "<li class = \"folder expanded\" id = \"".$row['masterTableKey']."\"";

                                if (array_key_exists($row['tableKey'], (array)$display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".$row['note']."<ul>";
                        }
                        else
                        {
                            echo "<li id = \"".$row['masterTableKey']."\"";

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

            echo "<form id = \"addForm_".$replaceTable[PREFIX."site_settings"]."\">";
                echo $displayagordojn[1]."<br>";
                echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";

                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."site_settings"]."\" data-replace_table = \"".$replaceTable[PREFIX."site_settings"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$displayagordojn[2]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."site_settings"]."\">";
                echo "Det går ej att addera grupper i tjänsten än, detta är bara en förberedelse för framtiden!";
            echo "</div>";
        echo "</div>";

     //echo "</div>";
echo "</div>";
    
}

function displayBodyGrupoj()
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
    $displayBodyGrupoj = $langStrings['displayBodyGrupoj'];

    $displayBodyGrupoj_array = getLangstringsArray('displayBodyGrupoj_array', $displayLang);

    echo "<div class=\"panel-header panel-header-sm\">";

    echo "</div>";

    echo "<div class=\"content\" style = \"max-height : 44vh; height : 44vh; overflow : auto\">";
        echo "<div class=\"row\" style = \"height : 95%;\">";
            echo "<div class=\"col-md-12\" style = \"height : 99%;\">";
                echo "<div class=\"card\"style = \"height : 99%;\">";
                    echo "<div class=\"card-header\">";
                        echo "<h1>".$displayBodyGrupoj[1]."</h1>";
                echo "</div>";
                echo "<div class=\"card-body\">";
                    echo "<div class=\"row\">";
                        echo "<div class=\"col-md-12\">";
                            displayGrupoj();
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
	displayBodyGrupoj();
	displayFooterAdministrado();
    printScripts();
}
?>