<?php
session_start();

//include_once("../index.php");
include_once("../../common/db.php");
include_once("../../common/crypto.php");
include_once("../../common/userData.php");
include_once("../../common/modal.php");

function editUserProfile()
{
    global $link;
    
    global $phrase;
    
    $userSettings = getUserSettings();
    
    $replaceTable = getReplaceTable();

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
    $editUserProfile = $langStrings['editUserProfile'];

    $editUserProfile_array = getLangstringsArray('editUserProfile_array', $displayLang);
    
    $table = "`".PREFIX."users`";
    
    $sql = "SHOW COLUMNS FROM ".$table.";";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        if ($row['Field'] !== "autoId" && $row['Field'] !== "tableKey")
        {
            $fields[$row['Field']] = $row['Field'];
        }
    }
    
    foreach ($fields as $key => $value)
	{
		$select[] = "CAST(AES_DECRYPT(".$key.", SHA2('".$phrase."',512)) AS CHAR) AS ".$key;
	}
    
    if (isset($_POST['editProfile']))
    {
        $sql = "SELECT * , ".implode(", ", (array)$select)." FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['editProfile'])."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            foreach ($fields as $key => $value)
            {
                $fieldsData[$key] = $row[$key];
            }
            $fieldsData['tableKey'] = $row['tableKey'];
        }
    }
    
    $fieldsName['firstName'] = $editUserProfile[1];
    $fieldsName['sureName'] = $editUserProfile[2];
    $fieldsName['email'] = $editUserProfile[3];
    $fieldsName['mobilePhone'] = $editUserProfile[4];
    $fieldsName['password'] = $editUserProfile[5];
    
    echo "<form id = \"editProfileForm\">";
    
        $even = true;

        foreach ($fields as $key => $value)
        {
            if ($even)
            {
                echo "<div class=\"form-row\">";
            }

                echo "<div class=\"form-group col-md-6\">";
                echo "<label for=\"".$key."\">".$fieldsName[$key]."</label>";
                if ($key !== "password")
                {
                    echo "<input type=\"text\" class=\"form-control";
                    if (isset($_POST['editProfile']))
                    {
                        echo " "."syncData";
                    }
                    echo "\" id=\"".$key."";
                    if (!empty($fieldsData['tableKey']))
                    {
                        echo "[".$fieldsData['tableKey']."]";
                    }
                    echo "\" value = \"".$fieldsData[$key]."\" data-reload_tree = \"tree_".$replaceTable[PREFIX."users"]."\"";
                    if (isset($_POST['editProfile']))
                    {
                        echo " "." data-replace_table = \"".$replaceTable[PREFIX."users"]."\"";
                    }

                    echo ">";
                }
                else
                {
                    echo "<input type=\"password\" class=\"form-control ";
                    if (isset($_POST['editProfile']))
                    {
                        echo " "."syncPassword";
                    }
                    echo "\" id=\"".$key."";
                    if (!empty($fieldsData['tableKey']))
                    {
                        echo "[".$fieldsData['tableKey']."]";
                    }
					echo "\" "." data-replace_table = \"".$replaceTable[PREFIX."users"]."\"";
                    echo "\">";
                }
                echo "</div>";

                if ($even && $key == "password")
                {
                   echo "<div class=\"form-group col-md-6\">";
                        echo "<label for=\"repPassword\">".$editUserProfile[6]."</label>";
                        echo "<input type=\"password\" class=\"form-control syncPassword\" id=\"repPassword[".$fieldsData['tableKey']."]\"";
							if (isset($_POST['editProfile']))
							{
								echo " "." data-replace_table = \"".$replaceTable[PREFIX."users"]."\"";
							}
						echo " "." data-replace_table = \"".$replaceTable[PREFIX."users"]."\"";
						echo ">";
                    echo "</div>";
                }
                else if ($key == "password")
                {
                    echo "<div class=\"form-group col-md-6\">";
                        echo "<label for=\"repPassword\">".$editUserProfile[6]."</label>";
                        echo "<input type=\"password\" class=\"form-control syncPassword\" id=\"repPassword[".$fieldsData['tableKey']."]\"";
						if (isset($_POST['editProfile']))
						{
							echo " "." data-replace_table = \"".$replaceTable[PREFIX."users"]."\"";
						}
					echo ">";
                    echo "</div>";

                }


            if (!$even)
            {
                echo "</div>";
            }
            $even = !$even;
        }

        if ($even)
        {
            echo "</div>";
        }
    echo "</form>";
}


function displayUserProfile()
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
    $displayUserProfile = $langStrings['displayUserProfile'];

    $displayUserProfile_array = getLangstringsArray('displayUserProfile_array', $displayLang);
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."users`";
    $table2 = "`".PREFIX."site_settings_lang`";
    checkTable($table);
    unset($data);

    $sql = "SELECT * FROM (SELECT CAST(AES_DECRYPT(firstName, SHA2('".$phrase."', 512)) as char) as firstName, CAST(AES_DECRYPT(sureName, SHA2('".$phrase."', 512)) as char) as sureName, tableKey FROM $table) as k ORDER BY k.firstName, k.sureName";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
     echo "<div class = \"row\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."users"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."users"]."\" data-replace_table = \"".$replaceTable[PREFIX.'users']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."users"]."-data\" style = \"display:none;\" >";
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

            echo "<form id = \"addForm_".$replaceTable[PREFIX."users"]."\">";
                /*echo $displayagordojn[1]."<br>";
                echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";
*/
                echo "<button type = \"button\" class = \"btn btn-secondary addUserToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."users"]."\" data-replace_table = \"".$replaceTable[PREFIX."users"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$displayUzanto[1]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
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
    
    $displayLang = array_map("trim", explode(",", $siteSettings['language']));
        
    foreach ($displayLang as $key => $value)
    {
        $order[] = "WHEN lang = '".$value."' THEN ".$i;
        $order_lang[] = "WHEN code = '".$value."' THEN ".$i;
        $i++;
    }
    
    $langStrings = getlangstrings();
    $displayBodyGrupoj = $langStrings['displayBodyGrupoj'];

    $displayBodyGrupoj_array = getLangstringsArray('displayBodyGrupoj_array', $displayLang);
    
    echo "<h1 class=\"m-0\">".$displayBodyGrupoj[1]."</h1>";
    
    displayUserProfile();
}


if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']))
{
    displayHeader();
    
    
    displayBodyUserProfile();
    
    displayFooterAdministrado();
    
    print_modal_xl();
}

?>