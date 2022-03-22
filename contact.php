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

include_once("theme.php");

function displayContactInfo()
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

    $displayContactInfo = $langStrings['displayContactInfo'];

    $displayContactInfo_array = getLangstringsArray('displayContactInfo_array', $displayLang);

    echo "<section class=\"contact-form-wrap section\">";
        echo "<div class=\"container\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-lg-6 col-md-12 col-sm-12\">";
                    echo "<form id=\"contact-form\" class=\"contact__form\" method=\"post\" action=\"mail.php\">";
                     echo "<!-- form message -->";
                        echo "<div class=\"row\">";
                            echo "<div class=\"col-12\">";
                                echo "<div class=\"alert alert-success contact__msg\" style=\"display: none\" role=\"alert\">";
                                    echo $displayContactInfo[1];
                                echo "</div>";
                            echo "</div>";
                        echo "</div>";
                        echo "<!-- end message -->";
                        echo "<span class=\"text-color\">".$displayContactInfo[2]."</span>";
                        echo "<h3 class=\"text-md mb-4\">".$displayContactInfo[3]."</h3>";
                        echo "<div class=\"form-group\">";
                            echo "<input name=\"name\" id = \"name\" type=\"text\" class=\"form-control\" placeholder=\"".$displayContactInfo[4]."\">";
                        echo "</div>";
                        echo "<div class=\"form-group\">";
                            echo "<input name=\"email\" id = \"email\" type=\"text\" class=\"form-control\" placeholder=\"".$displayContactInfo[5]."\">";
                        echo "</div>";
                        echo "<div class=\"form-group\">";
                            echo "<input name=\"company\" id = \"company\" type=\"text\" class=\"form-control\" placeholder=\"".$displayContactInfo[13]."\">";
                        echo "</div>";
                        echo "<div class=\"form-group-2 mb-4\">";
                            echo "<textarea name=\"message\" id=\"message\" class=\"form-control\" rows= \"4\" placeholder=\"".$displayContactInfo[6]."\"></textarea>";
                        echo "</div>";
                        echo "<button class=\"btn btn-main btn-round-full sendContatactMail\" name=\"submit\" id = \"submit\" type=\"button\" data-form_input = \"contact-form\">".$displayContactInfo[7]."</button>";
                    echo "</form>";
                echo "</div>";

                echo "<div class=\"col-lg-5 col-sm-12\">";
                    echo "<div class=\"contact-content pl-lg-5 mt-5 mt-lg-0\">";
                        echo "<span class=\"text-muted\">".$displayContactInfo[9]."</span>";
                        echo "<h2 class=\"mb-5 mt-2\">".$displayContactInfo[10]."</h2>";

                        echo "<ul class=\"address-block list-unstyled\">";
                            echo "<li>";
                                echo "<i class=\"ti-direction mr-3\"></i>".$displayContactInfo[8]."";
                            echo "</li>";
                            echo "<li>";
                                echo "<i class=\"ti-email mr-3\"></i>".$displayContactInfo[11]."";
                            echo "</li>";
                            echo "<li>";
                                echo "<i class=\"ti-mobile mr-3\"></i>".$displayContactInfo[12]."";
                            echo "</li>";
                        echo "</ul>";

                        /*echo "<ul class=\"social-icons list-inline mt-5\">";
                            echo "<li class=\"list-inline-item\">";
                                echo "<a href=\"http://www.themefisher.com\"><i class=\"fab fa-facebook-f\"></i></a>";
                            echo "</li>";
                            echo "<li class=\"list-inline-item\">";
                                echo "<a href=\"http://www.themefisher.com\"><i class=\"fab fa-twitter\"></i></a>";
                            echo "</li>";
                            echo "<li class=\"list-inline-item\">";
                                echo "<a href=\"http://www.themefisher.com\"><i class=\"fab fa-linkedin-in\"></i></a>";
                            echo "</li>";
                        echo "</ul>";*/
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</section>";
}

function printContactInfo()
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
    $displayMenu = $langStrings['displayMenu'];

    $displayMenu_array = getLangstringsArray('displayMenu_array', $displayLang);
    
    $table = "`".PREFIX."menu`";
    $table2 = "`".PREFIX."menu_lang`";
    $table3 = "`".PREFIX."own_pages_lang`";
    
    $sql = "SELECT * FROM $table AS menu INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT menuId, 
          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note,
          FROM $table2) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t GROUP BY menuId) AS lang ON menu.menuId = lang.menuId 
          LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
          CAST(AES_DECRYPT(subHeader, SHA2('".$phrase."', 512)) AS CHAR) as subHeader, 
          CAST(AES_DECRYPT(header, SHA2('".$phrase."', 512)) AS CHAR) as header,
          FROM $table3) as q2 ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t2  ) AS ownPage ON menu.tableKey = ownPage.tableKey 
          WHERE menu.file = AES_ENCRYPT('".basename(__FILE__)."', SHA2('".$phrase."', 512))";
    $sql = "SELECT *, CAST(AES_DECRYPT(menu.file, SHA2('".$phrase."', 512)) AS CHAR) as file FROM ".$table." as menu 
          LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT pageId, 
          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
          CAST(AES_DECRYPT(subHeader, SHA2('".$phrase."', 512)) AS CHAR) as subHeader, 
          CAST(AES_DECRYPT(header, SHA2('".$phrase."', 512)) AS CHAR) as header
          FROM $table3) as q2 ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t2  ) AS ownPage ON menu.menuId = ownPage.pageId 
          HAVING file = '".basename(__FILE__)."'";
   
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $pageData = $row;
    }
    
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
    
        displayContactInfo();
        
        echo "</div>";
     echo "</section>";
    }

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']))
{
    printHeader();
    printContactInfo();
    displayFooter();
}