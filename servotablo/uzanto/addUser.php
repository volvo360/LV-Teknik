<?php
    session_start();
    include_once("../../common/db.php");
    include_once("../../common/crypto.php");
    include_once("../../common/userData.php");

    include_once("../../administrado/editProfile.php");

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
    $addUser = $langStrings['addUser'];

    $addUser_array = getLangstringsArray('addUser_array', $displayLang);

    echo "<div id = \"ajaxHeaderModal\">";
        echo "<h5 class=\"modal-title h4\" id=\"modalXlLabel\">".$addUser[1]."</h5>";
    echo "</div>";
        
    echo "<div id = \"ajaxBodyModal\">";
        editUserProfile();
    echo "</div>";

    echo "<div id = \"ajaxFooterModal\">";
        echo "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">".$addUser[2]."</button> &nbsp;";
        echo "<button type=\"button\" class=\"btn btn-secondary addNewUser\" data-target_tree=\"tree_".$replaceTable[PREFIX.'users']."\" data-table_key = \"".$replaceTable[PREFIX.'users']."\">".$addUser[3]."</button>";
    echo "</div>";  

?>