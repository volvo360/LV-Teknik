<?php

include_once("./common/db.php");
include_once("./common/userData.php");

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
echo "<!doctype html>";
echo "<html lang=\"".reset($displayLang)."\">";

include_once("./theme.php");

function getPageInfo()
{
    global $link;
    
    global $phrase;
    
    $table = "`".PREFIX."own_pages`";
    $table2 = "`".PREFIX."own_pages_lang`";
    
    $table10 = "`".PREFIX."menu`";
    
    $sql = "SHOW COLUMNS FROM ".$table."";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        if ($row['Field'] !== "autoId" && $row['Field'] !== "tableKey")
        {
            if ($row['Type'] === "blob")
            {
                $fields[$row['Field']] = "CAST(AES_DECRYPT(".$row['Field'].", SHA2('".$phrase."', 512)) AS CHAR) as ".$row['Field']."";
            }
            else
            {    
                if ($row['Field'] == "pageId")
                {
                    $fields[$row['Field']] = "page.".$row['Field'];
                }
                else
                {
                    $fields[$row['Field']] = $row['Field'];
                }
            }
        }
    }
    
    $sql = "SHOW COLUMNS FROM ".$table2."";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        if ($row['Field'] !== "autoId" && $row['Field'] !== "tableKey")
        {
            if ($row['Type'] === "blob")
            {
                $fields2[$row['Field']] = "CAST(AES_DECRYPT(".$row['Field'].", SHA2('".$phrase."', 512)) AS CHAR) as ".$row['Field']."";
            }
            else
            {    
                $fields2[$row['Field']] = $row['Field'];
            }
        }
    }
    
    $sql = "SELECT ".implode(", ", $fields).", lang.*, t10.* FROM ".$table." as page LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM (SELECT ".implode(", ", $fields2)." FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t GROUP BY pageId) as lang ON page.pageId = lang.pageId INNER JOIN (SELECT tableKey, type FROM $table10 ) as t10 on t10.tableKey = page.tableKey WHERE page.tableKey = '".mysqli_real_escape_string($link, $_GET['pageId'])."'";
    //echo $sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $pageData = $row;
    }
    
    return $pageData;
    
}

function renderGalleryData()
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

    $replaceTable = getReplaceTable();

    $langStrings = getlangstrings();

    $showAjaxRefObjects = $langStrings['showAjaxRefObjects'];

    $showAjaxRefObjects_array = getLangstringsArray('showAjaxRefObjects_array', $displayLang);

    $table = "`".PREFIX."ref_types`";
    $table2 = "`".PREFIX."ref_types_lang`";
    
    $table10 = "`".PREFIX."ref_objects`";
    $table11 = "`".PREFIX."ref_objects_lang`";
    
    $table20 = "`".PREFIX."ref_settings`";
    
    $table30 = "`".PREFIX."ref_objects_image`";
    $table31 = "`".PREFIX."ref_objects_image_lang`";
    
    $sql = "SELECT CAST(AES_DECRYPT(setting, SHA2('".$phrase."', 512)) AS CHAR) as setting, CAST(AES_DECRYPT(data, SHA2('".$phrase."', 512)) AS CHAR) as data FROM ".$table20."";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $setting[$row['setting']] = $row['data'];
    }
    
    $sql = "SELECT * FROM (SELECT node.typeId  , node.lft, node.rgt, node.tableKey as masterTableKey, COUNT(parent.lft) - 1 as depth
    FROM (SELECT * FROM ".$table.") AS node,
          (SELECT * FROM ".$table.") AS parent
    WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
    ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT typeId , tableKey, 
          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang,
          CAST(AES_DECRYPT( description, SHA2('".$phrase."', 512)) AS CHAR) as description,
          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
          ) AS lang ON menu.typeId = lang.typeId ORDER BY menu.lft";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

    $numRows = mysqli_num_rows($result);
    
    $data = null;
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $data .= "<h1>".$row['note']."</h1>"; 
        
        $sql = "SELECT * FROM (SELECT node.referenceId , node.lft, node.rgt, node.tableKey as masterTableKey, node.refType, COUNT(parent.lft) - 1 as depth
        FROM (SELECT * FROM ".$table10." WHERE refType = '".$row['typeId']."') AS node,
              (SELECT * FROM ".$table10." WHERE refType = '".$row['typeId']."') AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT referenceId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang,
              CAST(AES_DECRYPT(header, SHA2('".$phrase."', 512)) AS CHAR) as header,
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table11) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.referenceId = lang.referenceId ORDER BY menu.lft LIMIT ".$setting['maxGallery']."";
        //echo __LINE__." ".$sql."<br><br>";
        $result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $end = false;
        
        if (mysqli_num_rows($result) > 0)
        {
            $end = true;
            
            $data .= "<!-- Section Testimonial Start -->";
            $data .= "<section class=\"section testimonial\">";
            
            $data .= "<div class=\"container\">";
                $data .= "<div class=\"row\">";
                    $data .= "<div class=\"col-lg-7 \">";
                        $data .= "<div class=\"section-title\">";
                            $data .= "<span class=\"h6 text-color\">".$row2['header']."</span>";
                            $data .= $row['description'];
                        $data .= "</div>";
                    $data .= "</div>";
                $data .= "</div>";
            $data .= "</div>";
            
            $data .= "<div class=\"container\">";
                $data .= "<div class=\"row testimonial-wrap\">";
        }
        while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
        {
            /*$data .= "<div class=\"container\">";
                $data .= "<div class=\"row\">";
                    $data .= "<div class=\"col-lg-7 \">";
                        $data .= "<div class=\"section-title\">";
                            $data .= "<span class=\"h6 text-color\">".$row2['header']."</span>";
                            $data .= "<h2 class=\"mt-3 content-title\">".$indexTestimonial[2]."</h2>";
                        $data .= "</div>";
                    $data .= "</div>";
                $data .= "</div>";
            $data .= "</div>";
        */          $sql = "SELECT * FROM (SELECT node.imageId, node.lft, node.rgt, node.tableKey as masterTableKey, CAST(AES_DECRYPT(node.fileName , SHA2('".$phrase."', 512)) AS CHAR) as                        file, COUNT(parent.lft) - 1 as depth
                    FROM (SELECT * FROM ".$table30." WHERE referenceId = '".$row2['referenceId']."') AS node,
                          (SELECT * FROM ".$table30." WHERE referenceId = '".$row2['referenceId']."' ) AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
                    ORDER BY node.lft) as menu LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT imageId , tableKey, 
                          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang,
                          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table31) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
                          ) AS lang ON menu.imageId = lang.imageId ORDER BY menu.lft LIMIT 1";
                    //echo __LINE__." ".$sql."<br><br>";
                    $result3 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

                    while ($row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC))
                    {
                        $path = "./project/".substr($row2['masterTableKey'],0,1)."/".substr($row2['masterTableKey'],0,2)."/".$row2['masterTableKey']."/image/source/".$row3['file'];
                        $imageName = $row3['note'];
                    }
            
                    $data .= "<div class=\"testimonial-item position-relative\">";
                        $data .= "<i class=\" text-color\"></i>";

                        $data .= "<div class=\"testimonial-item-content\">";
                            $data .= "<p class=\"testimonial-text\">".$row2['header']."</p>";

                            $data .= "<div class=\"testimonial-author\">";
                                $data .= "<h5 class=\"mb-0 text-capitalize\">".$indexTestimonial[4]."</h5>";
                                $data .= "<p><img src = \"".$path."\" class=\"img-thumbnail\" alt = \"".$imageName."\"></p>";
                                $data .= "<p><a href = \"showproject.php?tableKey=".$row2['masterTableKey']."\"<button class = \"btn btn-main btn-round-full\">"."Undersök"."</button></a></p>";
                            $data .= "</div>";
                        $data .= "</div>";
                    $data .= "</div>";
                    /*$data .= "<div class=\"testimonial-item position-relative\">";
                        $data .= "<i class=\" text-color\"></i>";

                        $data .= "<div class=\"testimonial-item-content\">";
                            $data .= "<p class=\"testimonial-text\">".$indexTestimonial[6]."</p>";

                            $data .= "<div class=\"testimonial-author\">";
                                $data .= "<h5 class=\"mb-0 text-capitalize\">".$indexTestimonial[7]."</h5>";
                                $data .= "<p>".$indexTestimonial[8]."</p>";
                            $data .= "</div>";
                        $data .= "</div>";
                    $data .= "</div>";
                    $data .= "<div class=\"testimonial-item position-relative\">";
                        $data .= "<i class=\" text-color\"></i>";

                        $data .= "<div class=\"testimonial-item-content\">";
                            $data .= "<p class=\"testimonial-text\">".$indexTestimonial[9]."</p>";

                            $data .= "<div class=\"testimonial-author\">";
                                $data .= "<h5 class=\"mb-0 text-capitalize\">".$indexTestimonial[10]."</h5>";
                                $data .= "<p>".$indexTestimonial[11]."</p>";
                            $data .= "</div>";
                        $data .= "</div>";
                    $data .= "</div>";
                    $data .= "<div class=\"testimonial-item position-relative\">";
                        $data .= "<i class=\" text-color\"></i>";

                        $data .= "<div class=\"testimonial-item-content\">";
                            $data .= "<p class=\"testimonial-text\">".$indexTestimonial[12]."</p>";

                            $data .= "<div class=\"testimonial-author\">";
                                $data .= "<h5 class=\"mb-0 text-capitalize\">".$indexTestimonial[13]."</h5>";
                                $data .= "<p>".$indexTestimonial[14]."</p>";
                            $data .= "</div>";
                        $data .= "</div>";
                    $data .= "</div>";*/
            
        }
        if ($end)
        {
                    $data .= "</div>";
                $data .= "</div>";
            $data .= "</section>";
        $data .= "<!-- Section Testimonial End -->";
        }
        
    }
    
    
    
    return $data;
}


function printGallery($data = null)
{
    global $link;
    global $phrase;
    
    $gallery = renderGalleryData();
    
    echo str_replace("~~gallery~~", $gallery, $data);
}

function  printOwnPage()
{
    $pageData = getPageInfo();
    
    echo "<div class=\"main-wrapper \">";
    echo "<section class=\"page-title bg-1\">";
      echo "<div class=\"container\">";
        echo "<div class=\"row\">";
          echo "<div class=\"col-md-12\">";
            echo "<div class=\"block text-center\">";
              echo "<span class=\"text-white\">".$pageData['subHeader']."</span>";
              echo "<h1 class=\"text-capitalize mb-4 text-lg\">".$pageData['header']."</h1>";
              /*echo "<ul class=\"list-inline\">";
                echo "<li class=\"list-inline-item\"><a href=\"index.html\" class=\"text-white\">Home</a></li>";
                echo "<li class=\"list-inline-item\"><span class=\"text-white\">/</span></li>";
                echo "<li class=\"list-inline-item\"><a href=\"#\" class=\"text-white-50\">About Us</a></li>";
              echo "</ul>";*/
            echo "</div>";
          echo "</div>";
        echo "</div>";
      echo "</div>";
    echo "</section>";
    echo "<!-- Section About Start -->";
    echo "<section class=\"section about-2 position-relative\">";
        echo "<div class=\"container\">";
    
        if ($pageData['type'] == "gallery")
        {
            printGallery($pageData['note']);
        }
        else
        {
            echo $pageData['note'];
        }
        
        echo "</div>";
     echo "</section>";
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']))
{
    printHeader();
    printOwnPage();
    displayFooter();
}