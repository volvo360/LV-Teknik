<?php
session_start();

include_once("../../common/db.php");
include_once("./../../common/userData.php");
include_once("../../administrado/ext/theme/nav.php");
include_once("./../../common/crypto.php");
include_once("./../../common/modal.php");

function displayUzanto()
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
    $displayUzanto = $langStrings['displayUzanto'];

    $displayUzanto_array = getLangstringsArray('displayUzanto_array', $displayLang);
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."users`";
    $table2 = "`".PREFIX."site_settings_lang`";
    checkTable($table);
    unset($data);

    $sql = "SELECT *, CONCAT(k.firstName, ' ', k.sureName) as note FROM (SELECT CAST(AES_DECRYPT(firstName, SHA2('".$phrase."', 512)) as char) as firstName, CAST(AES_DECRYPT(sureName, SHA2('".$phrase."', 512)) as char) as sureName, tableKey FROM $table) as k ORDER BY k.firstName, k.sureName";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
     echo "<div class = \"row\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 85vh; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."users"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."users"]."\" data-replace_table = \"".$replaceTable[PREFIX.'users']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."users"]."-data\" style = \"display:none;\" >";
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
                        $rowData[] = $row;

                        echo "<li id = \"".$row['tableKey']."\">".$row['note']."</li>";
                    }

                echo "</ul>";
            echo "</div><br>";

            echo "<form id = \"addForm_".$replaceTable[PREFIX."users"]."\">";
                /*echo $displayagordojn[1]."<br>";
                echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";
*/
                echo "<button type = \"button\" class = \"btn btn-secondary addUserToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."users"]."\" data-replace_table = \"".$replaceTable[PREFIX."users"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$displayUzanto[1]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 85vh; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."users"]."\">";
               // renderManualEdit($lang, $rowData);
            echo "</div>";
        echo "</div>";

     //echo "</div>";
echo "</div>";
    
}

function displayBodyUzanto()
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
    $displayBodyUzanto = $langStrings['displayBodyUzanto'];

    $displayBodyUzanto_array = getLangstringsArray('displayBodyUzanto_array', $displayLang);

    echo "<div class=\"panel-header panel-header-sm\">";

    echo "</div>";

    echo "<div class=\"content\" style = \"max-height : 44vh; height : 44vh; overflow : auto\">";
        echo "<div class=\"row\" style = \"height : 95%;\">";
            echo "<div class=\"col-md-12\" style = \"height : 99%;\">";
                echo "<div class=\"card\"style = \"height : 99%;\">";
                    echo "<div class=\"card-header\">";
                        echo "<h1>".$displayBodyUzanto[1]."</h1>";
                echo "</div>";
                echo "<div class=\"card-body\">";
                    echo "<div class=\"row\">";
                        echo "<div class=\"col-md-12\">";
                            displayUzanto();
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

    displayBodyUzanto();
	
    displayFooterAdministrado();
    print_modal_xl();
    printScripts();
}
?>