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
    
    $sql = "SELECT ".implode(", ", $fields).", lang.*, t10.* FROM ".$table." as page LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM (SELECT ".implode(", ", $fields2)." FROM $table2) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t GROUP BY pageId) as lang ON page.pageId = lang.pageId INNER JOIN (SELECT tableKey, type, file FROM $table10 ) as t10 on t10.tableKey = page.tableKey WHERE t10.file = AES_ENCRYPT('showproject.php', SHA2('".$phrase."', 512))";
    //echo $sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $pageData = $row;
    }
    
    return $pageData;
    
}

function renderReferenceObject()
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
    
    $table40 = "`".PREFIX."ref_properties`";
    $table41 = "`".PREFIX."ref_properties_lang`";
    $table42 = "`".PREFIX."ref_properties2object`";    
    
    $sql = "SELECT * FROM (SELECT node.referenceId, node.lft, node.rgt, node.tableKey as masterTableKey, node.refType, COUNT(parent.lft) - 1 as depth
        FROM (SELECT * FROM ".$table10." WHERE tableKey = '".mysqli_real_escape_string($link, $_GET['tableKey'])."') AS node,
              (SELECT * FROM ".$table10." WHERE tableKey = '".mysqli_real_escape_string($link, $_GET['tableKey'])."') AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT referenceId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang,
              CAST(AES_DECRYPT(header, SHA2('".$phrase."', 512)) AS CHAR) as header,
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table11) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.referenceId = lang.referenceId ORDER BY menu.lft";
    //echo __LINE__." ".$sql."<br>";
    $result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
    {
        $refId = $row2['referenceId']; 
        $typeId = $row2['refType'];
        $refHeader = $row2['header'];
        $refNote = $row2['note'];
        $lft = $row2['lft'];
        $rgt = $row2['rgt'];
    }
    
    $sql = "SELECT * FROM ".$table10." WHERE lft < '".$lft."' ORDER BY rgt DESC LIMIT 1";
    $result20 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    if (mysqli_num_rows($result20) > 0)
    {
        while ($row20 = mysqli_fetch_array($result20, MYSQLI_ASSOC))
        {
            $prevTableKey = $row20['tableKey'];
        } 
    }
    
    else
    {
        $sql = "SELECT * FROM ".$table10." ORDER BY lft DESC LIMIT 1";
        $result20 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        while ($row20 = mysqli_fetch_array($result20, MYSQLI_ASSOC))
        {
            $prevTableKey = $row20['tableKey'];
        }
    }
    
    $sql = "SELECT * FROM ".$table10." WHERE lft > '".$lft."' ORDER BY rgt ASC LIMIT 1";
    $result20 = mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    if (mysqli_num_rows($result20) > 0)
    {
        while ($row20 = mysqli_fetch_array($result20, MYSQLI_ASSOC))
        {
            $nextTableKey = $row20['tableKey'];
        } 
    }
    
    else
    {
        $sql = "SELECT * FROM ".$table10." ORDER BY lft LIMIT 1";
        $result20 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        while ($row20 = mysqli_fetch_array($result20, MYSQLI_ASSOC))
        {
            $nextTableKey = $row20['tableKey'];
        }
    }
    
    $sql = "SELECT * FROM (SELECT node.typeId  , node.lft, node.rgt, node.tableKey as masterTableKey, COUNT(parent.lft) - 1 as depth
            FROM (SELECT * FROM ".$table." WHERE typeId = '".$typeId."') AS node,
          (SELECT * FROM ".$table." WHERE typeId = '".$typeId."') AS parent
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
        $data .= "<p>".$row['description']."</p>"; 
        /*$data .="<div><p class=\"row\">";
            $data .="<div class=\"col-sm-4 offset-sm-4\">";
                
            $data .= "</div>";
        $data .= "</div>";*/
            /*$data .= "<p><div class=\"float-left\"><a href=\"showproject.php?tableKey=".$prevTableKey."\" class=\"btn btn-main btn-round-full\"><<</a></div>";
            $data .= "<div class=\"float-right\"><a href=\"showproject.php?tableKey=".$nextTableKey."\" class=\"btn btn-main btn-round-full\">>></a></div></p>";*/
        
        //<a href="contact.html" class="btn btn-main btn-round-full">Contact Us</a>
        
        $sql = "SELECT * FROM (SELECT node.referenceId , node.lft, node.rgt, node.tableKey as masterTableKey, node.refType, COUNT(parent.lft) - 1 as depth
        FROM (SELECT * FROM ".$table10." WHERE referenceId = '".$refId."') AS node,
              (SELECT * FROM ".$table10." WHERE referenceId = '".$refId."') AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT referenceId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang,
              CAST(AES_DECRYPT(header, SHA2('".$phrase."', 512)) AS CHAR) as header,
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table11) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.referenceId = lang.referenceId GROUP BY lang.referenceId ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br><br>";
        $result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $end = false;
        
        while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
        {
            $sql = "SELECT * FROM (SELECT node.imageId, node.lft, node.rgt, node.tableKey as masterTableKey, CAST(AES_DECRYPT(node.fileName , SHA2('".$phrase."', 512)) AS CHAR) as                        file, COUNT(parent.lft) - 1 as depth
                    FROM (SELECT * FROM ".$table30." WHERE referenceId = '".$refId."') AS node,
                          (SELECT * FROM ".$table30." WHERE referenceId = '".$refId."' ) AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
                    ORDER BY node.lft) as menu LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT imageId , tableKey, 
                          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang,
                          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table31) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
                          ) AS lang ON menu.imageId = lang.imageId ORDER BY menu.lft ";
            //echo __LINE__." ".$sql."<br><br>";
            $result3 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

            $imageId = 0;
            
            if (mysqli_num_rows($result3) > 0)
            {
                $end = true;

                $data .= "<h2 class = \"text-center\">".$refHeader."</h2>";
                
                $data .= "<p><a href=\"showproject.php?tableKey=".$prevTableKey."\" class=\"btn btn-main btn-round-full\"><<</a>";
                //$data .= "<span class = \"text-center\">".."</span>";
                $data .= "<span class=\"float-right\"><a  href=\"showproject.php?tableKey=".$nextTableKey."\" class=\"btn btn-main btn-round-full\">>></a></span></p>";
                
                $data .= "<div id=\"carouselExampleControls\" class=\"carousel slide\" data-ride=\"carousel\">";
                while ($row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC))
                {
                    $imageData[] = $row3;

                    if ($imageId === 0)
                    {
                        $imageList[] = "<li data-target=\"#carouselExampleIndicators\" data-slide-to=\"".$imageId."\" class=\"active\"></li>";
                    }
                    else
                    {
                        $imageList[] = "<li data-target=\"#carouselExampleIndicators\" data-slide-to=\"".$imageId."\"></li>";
                    }
                    $imageId++;
                }
                
                    $data .= "<ol class=\"carousel-indicators\">";
                        $data .= implode('', $imageList);
                    $data .= "</ol>";
                    $data .= "<div class=\"carousel-inner\">";
            }

            $first = true;
            
            foreach ($imageData as $key => $row3)
            {
                $path = "./project/".substr($row2['masterTableKey'],0,1)."/".substr($row2['masterTableKey'],0,2)."/".$row2['masterTableKey']."/image/source/".$row3['file'];
                $imageName = $row3['note'];
                $data .= "<div class=\"carousel-item";
                    if ($first)
                    {
                        $first = false;
                        $data.= " "."active";
                    }
                    $data .= "\">";
                    $data .= "<img class=\"d-block w-100\" src=\"".$path."\" alt=\"".$imageName."\">";
                $data .= "</div>";
            }
        }
        if ($end)
        {
            //$data .= "</div>";
                $data .= "</div>";
                $data .= "<a class=\"carousel-control-prev\" href=\"#carouselExampleControls\" role=\"button\" data-slide=\"prev\">";
                    $data .= "<span class=\"carousel-control-prev-icon\" aria-hidden=\"true\"></span>";
                    $data .= "<span class=\"sr-only\">Previous</span>";
                $data .= "</a>";
                $data .= "<a class=\"carousel-control-next\" href=\"#carouselExampleControls\" role=\"button\" data-slide=\"next\">";
                    $data .= "<span class=\"carousel-control-next-icon\" aria-hidden=\"true\"></span>";
                    $data .= "<span class=\"sr-only\">Next</span>";
                $data .= "</a>";
            $data .= "</div>";
        }
        $data .= "<br><p><a href=\"showproject.php?tableKey=".$prevTableKey."\" class=\"btn btn-main btn-round-full\"><<</a>";
            $data .= "<span class=\"float-right\"><a  href=\"showproject.php?tableKey=".$nextTableKey."\" class=\"btn btn-main btn-round-full\">>></a></span></p>";
        $sql = "SELECT * FROM ".$table42." WHERE objectId = '".$refId."'";
        $result3 = mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while ($row3 = mysqli_fetch_array($result3))
        {
            $propertiesId[] = "propertiesId = '".$row3['propertiesId']."'";
        }
        
        $sql = "SELECT * FROM (SELECT node.propertiesId , node.lft, node.rgt, node.tableKey as masterTableKey, COUNT(parent.lft) - 1 as depth
        FROM (SELECT * FROM ".$table40." WHERE (".implode(" OR ", $propertiesId).")) AS node,
              (SELECT * FROM ".$table40." WHERE (".implode(" OR ", $propertiesId).")) AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT propertiesId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note,
              CAST(AES_DECRYPT(description, SHA2('".$phrase."', 512)) AS CHAR) as description FROM $table41) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.propertiesId = lang.propertiesId  ORDER BY menu.lft";
        $result3 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $data .= "<span class = \"text-center\">";
        $data .="<ul style = \"list-style-type: none;\">";

        while ($row3 = mysqli_fetch_array($result3))
        {
            $sql = "SELECT * FROM (SELECT parent.*  FROM
            ".$table40." node,
            ".$table40." parent
            WHERE (
                node.lft BETWEEN parent.lft AND parent.rgt          
            )
            AND node.propertiesId ='".$row3['propertiesId']."'
            ORDER BY parent.rgt - parent.lft
            LIMIT 1,1) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT propertiesId, tableKey, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table41) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              ) AS lang ON menu.propertiesId = lang.propertiesId  ORDER BY menu.lft";
            //$data .= __LINE__." ".$sql."<br>";
            $result4 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            while ($row4 = mysqli_fetch_array($result4))
            {
                $data .= "<li>".$row4['note']." : <span data-toggle=\"tooltip\" data-placement=\"top\" title='".strip_tags($row3['description'])."'>".$row3['note']."</span></li>";
            }
        }

        $data .="</ul></span>";

        $data .= "<p>".$refNote."</p>";
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

function printReferencePage()
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
    
        echo renderReferenceObject();
        
        echo "</div>";
     echo "</section>";
    }

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']))
{
    printHeader();
    printReferencePage();
    displayFooter();
}