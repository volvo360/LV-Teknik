<?php
session_start();

unset($_SESSION['replaceTempKey']);

include_once("../../common/db.php");
include_once("../../common/crypto.php");
include_once("../../common/userData.php");
include_once("./../../administrado/ext/theme/nav.php");


if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
{
	showAjaxIkonoj();
	
	unset($_SESSION['replaceTempKey']);
}

function showAjax_ikonoj()
{
    global $link;
    global $link_k;
    
    global $phrase;
    global $phrase_k;
    
    $replaceTable = getReplaceTable();
    
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
    $showAjax_ikonoj = $langStrings['showAjax_ikonoj'];

    $showAjax_ikonoj_array = getLangstringsArray('showAjax_ikonoj_array', $displayLang);
    
    $table = "`".PREFIX."icons`";
    
    $sql = "SELECT *, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
    //echo $sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        foreach ($row as $key => $value)
        {
            if ($key === "autoId" || $key === "iconTypeId" || $key === "iconId" || $key === "tableKey" || $key === "lft" || $key === "rgt" )
            {
                continue;
            }
            else if ($key === "note")    
            {
                echo "<div class=\"form-group row\">";
                    echo "<label for=\"note[".$_POST['id']."]\" class=\"col-sm-2 col-form-label\">".$showAjax_ikonoj[1]."</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<input type=\"text\" class=\"form-control syncData\" data-reload_tree = \"tree_".$replaceTable[PREFIX.'icons']."\" data-replace_table = \"".$replaceTable[PREFIX."icons"]."\" id=\"".$key."[".$_POST['id']."]\" value = \"".$row['note']."\">";
                    echo "</div>";
                echo "</div>";  
            }
            else
            {
                echo "<div class=\"form-group row\">";
                    echo "<label for=\"note[".$_POST['id']."]\" class=\"col-sm-2 col-form-label\">".$key."</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<div class=\"form-check checkbox-slider--b\">";
                            echo "<label>";
                                echo "<input type=\"checkbox\" id=\"".$key."[".$_POST['id']."]\" class = \"syncData\" data-replace_table = \"".$replaceTable[PREFIX."icons"]."\"";
                                    if ((int)$value > 0)
                                    {
                                        echo " "."checked";
                                    }
                                echo "><span>".$key."</span>";
                            echo "</label>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";  
                    
            }
        }        
    }
}

//if (!(function_exists("showAjax")))
{
	function showAjaxIkonoj()
	{
		$replaceTable = getReplaceTable(false);

        if ($replaceTable[$_POST['replaceTable']] == PREFIX."icons")
		{
        	showAjax_ikonoj();
		}
		
        echo "<input type = \"hidden\" id = \"replaceTable\" value =\"".$_POST['replaceTable']."\">";
	}
}
?>