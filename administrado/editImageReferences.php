<?php
session_start();
include_once("../common/db.php");
include_once("../common/crypto.php");
include_once("../common/userData.php");
include_once("./prioImage.php");

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
{
    $replaceTable = getReplaceTable(false);

    if ($replaceTable[$_POST['replaceTable']] === PREFIX.'ref_objects_image')
    {
        refObjectsImage();
    }
}

function showImage()
{
    global $link;
    
    global $phrase;
    
    $path = "../project/".substr($_POST['projectKey'],0,1)."/".substr($_POST['projectKey'],0,2)."/".$_POST['projectKey']."/image/";
    
    if (!file_exists($path))
    {
        mkdir ($path."/source/", "0777" , true); 
        mkdir ($path."/thumbs/", "0777" , true); 
    }
    
    $path = "/project/".substr($_POST['projectKey'],0,1)."/".substr($_POST['projectKey'],0,2)."/".$_POST['projectKey']."/image/";
    
    $_SESSION['folderUrl'] = $path;
    session_write_close();
    
    echo "<iframe src = \"../filemanager/dialog.php?type=1&field_id=".urlencode($_POST['inputField'])."\" style = \"width : 100%; height : 50vh;\"></iframe>";
}

function showPrioImage()
{
    global $link;

    global $phrase;
    
    $replaceTable = getReplaceTable();

    $data = getDirContents("../".$_SESSION['folderUrl']."source/");

    $table = "`".PREFIX."ref_objects`";
    $table10 = "`".PREFIX."ref_objects_image`";

    $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['projectKey'])."'";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

    while ($row = mysqli_fetch_array($result))
    {
        $referenceId = $row['referenceId'];   
    }

    $sql = "SELECT CAST(AES_DECRYPT(fileName, SHA2('".$phrase."', 512)) AS CHAR) as fileName FROM ".$table10." WHERE referenceId = '".$referenceId."'";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

    while ($row = mysqli_fetch_array($result))
    {
        $existingFiles[$row['fileName']] = $row['fileName'];
    }

    if (is_array($existingFiles))
    {
        $newFiles = array_diff($data, $existingFiles);
    }
    else
    {
        $newFiles = $data;
    }

    $deleteFiles = array_diff($existingFiles, $data );

    $sql = "SELECT CASE WHEN MAX(imageId) IS NULL THEN 1 ELSE MAX(imageId) +1 END as imageId FROM ".$table10."";
    //echo __LINE__." ".$sql."<br>";
    
    if (!empty($newFiles))
    {
        $sql = "SELECT CASE WHEN MAX(lft) IS NULL THEN 1 ELSE MAX(rgt) + 1 END as lf FROM ".$table10." WHERE referenceId = '".$referenceId."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $lft = $row['lft'];
            $imageId = $row['imageId'];
        }
        
        $sql = "SELECT CASE WHEN MAX(imageId) IS NULL THEN 1 ELSE MAX(imageId) +1 END as imageId FROM ".$table10."";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $imageId = $row['imageId'];
        }

        foreach ($newFiles as $key => $value)
        {
            $value = mysqli_real_escape_string($link, $value);

            $sql = "INSERT INTO ".$table10." (imageId, lft, rgt, referenceId, fileName) VALUES ('".$imageId."', '".$lft."', '".++$lft."', '".$referenceId."', AES_ENCRYPT('".$value."', SHA2('".$phrase."', 512)))";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            ++$lft;
        }

        checkTable($table10);
    }

    foreach ($deleteFiles as $key => $value)
    {
        $value = mysqli_real_escape_string($link, $value);

        $sql = "DELETE FROM ".$table10." WHERE fileName = AES_ENCRYPT('".$value."', SHA2('".$phrase."', 512)) AND referenceId = '".$referenceId."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    }

    $table = "`".PREFIX."ref_objects_image`";
    $table2 = "`".PREFIX."ref_objects_image_lang`";

    $sql = "SELECT *, CASE WHEN lang.note IS NULL then fileName ELSE lang.note END as note FROM (SELECT node.imageId  , node.lft, node.rgt, node.tableKey as masterTableKey, CAST(AES_DECRYPT(node.fileName, SHA2('".$phrase."', 512)) AS CHAR) as fileName, (COUNT(parent.imageId  ) - 1) AS depth
    FROM (SELECT * FROM $table WHERE referenceId = '".$referenceId."') AS node,
          (SELECT * FROM $table WHERE referenceId = '".$referenceId."' ) AS parent
    WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
    ORDER BY node.lft) as menu LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT imageId  , 
          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
          GROUP BY imageId  ) AS lang ON menu.imageId   = lang.imageId   ORDER BY menu.lft";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

    echo "<div class = \"row\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."ref_objects_image"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."ref_objects_image"]."\" data-replace_table = \"".$replaceTable[PREFIX.'ref_objects_image']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."ref_objects_image"]."-data\" style = \"display:none;\" >";

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

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."ref_objects_image"]."\">";
               // renderManualEdit($lang, $rowData);
            echo "</div>";
        echo "</div>";
    echo "</div>";

}

function refObjectsImage()
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
    $refObjectsImage = $langStrings['refObjectsImage'];

    $refObjectsImage_array = getLangstringsArray('refObjectsImage_array', $displayLang);

    echo "<div id = \"ajaxHeaderModal\">";
        echo "<h5 class=\"modal-title h4\" id=\"modalXlLabel\">".$refObjectsImage[1]."</h5>";
    echo "</div>";
        
    echo "<div id = \"ajaxBodyModal\">";
    
        $table = "`".PREFIX."ref_objects`";
        $table10 = "`".PREFIX."ref_objects_image`";
        
        $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['projectKey'])."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result))
        {
            $referenceId = $row['referenceId'];   
        }
        
        $sql = "SELECT CAST(AES_DECRYPT(fileName, SHA2('".$phrase."', 512)) AS CHAR) as fileName FROM ".$table10." WHERE referenceId = '".$referenceId."' LIMIT 1";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        if (mysqli_num_rows($result) > 0)
        {
            $preselect = "prioImage";
        }
        else
        {
            $preselect = "image";
        }
    
        $navs['image'] = $refObjectsImage[2];
        $navs['prioImage'] = $refObjectsImage[3];
    
        $navsUrl['prioImage'] = "prioImage.php";
    
        echo "<ul class=\"nav nav-tabs\" id=\"myTabRefObjectsImage\" role=\"tablist\">";
            foreach ($navs as $key => $value)
            {
                echo "<li class=\"nav-item\" role=\"presentation\">";
                    echo "<a class=\"nav-link";
                        if ($key == $preselect)
                        {
                            echo " "."active";
                        }
                    echo "\" id=\"".$key."-tab\" data-toggle=\"tab\" href=\"#".$key."\" role=\"tab\" aria-controls=\"".$key."\" aria-selected=\"";
                        if ($key == $preselect)
                        {
                            echo "true";
                        }
                        else
                        {
                            echo "false";
                        }
                echo "\"";
                if (!empty($navsUrl[$key]))
                {
                    echo " "."data-target_file = \"".$navsUrl[$key]."\" data-target_project = \"".$_POST['projectKey']."\" data-replace_table = \"".$_POST['replaceTable']."\"";
                }
                
                echo ">".$value."</a>";
                echo "</li>";
            }
        echo "</ul>";
        
        echo "<div class=\"tab-content\" id=\"myTabContentRefObjectsImage\">";
            foreach ($navs as $key => $value)
            {
                echo "<div class=\"tab-pane fade";
                        if ($key == $preselect)
                        {
                             echo " "."show active";
                        }
                    echo "\" id=\"".$key."\" role=\"tabpanel\" aria-labelledby=\"".$key."-tab\">";
                    echo "<br>";
                    call_user_func("show".ucfirst($key));
                echo "</div>";
            }
            
        echo "</div>";
        
    echo "</div>";

    echo "<div id = \"ajaxFooterModal\">";
        echo "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">".$refObjectsImage[4]."</button> &nbsp;";
    echo "</div>";
}