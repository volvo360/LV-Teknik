<?php
  
    session_start();
    include_once("../common/db.php");
    include_once("../common/crypto.php");
    include_once("../common/userData.php");
    include_once("./ext/theme/nav.php");
    
    $replaceTable = getReplaceTable(false);
    
    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        showAjaxPrioImage();
    }

    function showAjaxPrioImage()
    {
        $replaceTable = getReplaceTable(false);

        if ($replaceTable[$_POST['replaceTable']] == PREFIX."xxx")
        {
            xxx();
        }
        else if ($replaceTable[$_POST['replaceTable']] == PREFIX."ref_objects_image")
        {
            showRefObjectsImage();
        }
    }

    function getDirContents($dir, &$results = array()) {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $value;
            } else if ($value != "." && $value != "..") {
                getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }

    function showRefObjectsImage()
    {
        global $link;
        
        global $phrase;
        
        $replaceTable = getReplaceTable();
        
        $data = getDirContents("../".$_SESSION['folderUrl']."source/");
        
        $table = "`".PREFIX."ref_objects`";
        $table10 = "`".PREFIX."ref_objects_image`";
        
        $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
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
        
        if (!empty($newFiles))
        {
            $sql = "SELECT CASE WHEN MAX(lft) IS NULL THEN 1 ELSE MAX(rgt) + 1 END as lft, CASE WHEN MAX(imageId) IS NULL THEN 1 ELSE MAX(imageId) +1 END as imageId FROM ".$table10." WHERE referenceId = '".$referenceId."'";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $lft = $row['lft'];
                $imageId = $row['imageId'];
            }
            
            foreach ($newFiles as $key => $value)
            {
                $value = mysqli_real_escape_string($link, $value);
                
                $sql = "INSERT INTO ".$table10." (imageId, lft, rgt, referenceId, fileName) VALUES ('".$imageId."', '".$lft."', '".++$lft."', '".$referenceId."', AES_ENCRYPT('".$value."', SHA2('".$phrase."', 512)))";
                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                ++$lft;
                ++$imageId;
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
?>     