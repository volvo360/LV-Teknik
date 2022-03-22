<?php

    include_once("./common/db.php");
    include_once("./common/userData.php");
    include_once("./theme.php");
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

    echo "<!DOCTYPE html>";
        echo "<html lang=\"".reset($displayLang)."\">";

    echo "<style>";
        echo "btn > a:visited {";
            echo "color: white;";
            echo "}";
    echo "</style>";

    function getMenu()
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

        $table = "`".PREFIX."menu`";
        $table2 = "`".PREFIX."menu_lang`";

        $sql = "SELECT * FROM (SELECT node.menuId, node.lft, node.rgt, node.type, node.tableKey, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) as char) as folder, CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) as char) as file, CAST(AES_DECRYPT(node.icon, SHA2('".$phrase."', 512)) as char) as icon, node.displayMenu, COUNT(parent.lft) - 1 as depth
        FROM $table AS node,
              $table AS parent
        WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft 
        ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
              CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
              CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
              GROUP BY menuId) AS lang ON menu.menuId = lang.menuId WHERE menu.displayMenu > 0 ORDER BY menu.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            if ($row['type'] == "deleted")
            {
                return false;
            }
            
            $data2[] = $row;
        }

        return $data2;
    }

function getHeaderMetaData()
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
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."menu`";
    $table2 = "`".PREFIX."menu_lang`";
     $table3 = "`".PREFIX."own_pages_lang`";
    $table4 = "`".PREFIX."own_pages`";
    checkTable($table2);
    unset($data);
    
    $folder = dirname($_SERVER['PHP_SELF']);
    $file = basename($_SERVER['PHP_SELF']);
    if (!empty($folder))
    {
        $where = "WHERE folder = AES_ENCRYPT('".mysqli_real_escape_string($link, $folder)."', SHA2('".$phrase."', 512)) AND file = AES_ENCRYPT('".mysqli_real_escape_string($link, $file)."', SHA2('".$phrase."', 512))";
    }
    else
    {
        $where = "WHERE file = AES_ENCRYPT('".mysqli_real_escape_string($link, $file)."', SHA2('".$phrase."', 512))";
        if (empty($file))
        {
            $where = "WHERE file = AES_ENCRYPT('index.php', SHA2('".$phrase."', 512))";
        }
    }
    
    
    if ($file == "ownpage.php")
    {
        $tableKey = $_GET['pageId'];
        
        $where = "WHERE menu.tableKey = '".mysqli_real_escape_string($link, $tableKey)."'";
    }

    $sql = "SELECT * FROM (SELECT node.menuId, node.lft, node.rgt, node.tableKey, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) AS CHAR) as folder, CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) AS CHAR) as file,  CAST(AES_DECRYPT(node.icon, SHA2('".$phrase."', 512)) AS CHAR) as icon, (COUNT(parent.menuId) - 1) AS depth
    FROM $table AS node,
          $table AS parent
    WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
    ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
          GROUP BY menuId) AS lang ON menu.menuId = lang.menuId
    LEFT OUTER JOIN (SELECT * FROM ".$table4.") as  ownPage ON ownPage.tableKey = menu.tableKey 
    LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT tableKey, pageId, 
          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
          CAST(AES_DECRYPT(metaKeywords, SHA2('".$phrase."', 512)) AS CHAR) as metaKeywords,
          CAST(AES_DECRYPT(metaDescription, SHA2('".$phrase."', 512)) AS CHAR) as  	metaDescription FROM $table3) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
          GROUP BY pageId) AS ownPageLang ON ownPage.pageId = ownPageLang.pageId      
          ";
    if (!empty($where))
    {
        $sql .= " ".$where;
    }
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    if (basename($_SERVER['SCRIPT_FILENAME']) === "login.php" || basename($_SERVER['SCRIPT_FILENAME']) === "login.php")
    {
        echo "<meta name=\"robots\" content=\"noindex\">";
    }
        
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        if (empty($row['metaKeywords']) || empty($row['metaDescription']))
        {
            $where = "WHERE file = AES_ENCRYPT('index.php', SHA2('".$phrase."', 512))";
            $sql = "SELECT * FROM (SELECT node.menuId, node.lft, node.rgt, node.tableKey, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) AS CHAR) as folder, CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) AS CHAR) as file,  CAST(AES_DECRYPT(node.icon, SHA2('".$phrase."', 512)) AS CHAR) as icon, (COUNT(parent.menuId) - 1) AS depth
                FROM $table AS node,
                      $table AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
                ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
                      CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
                      CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
                      GROUP BY menuId) AS lang ON menu.menuId = lang.menuId
                LEFT OUTER JOIN (SELECT * FROM ".$table4.") as  ownPage ON ownPage.tableKey = menu.tableKey 
                LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT tableKey, pageId, 
                      CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
                      CAST(AES_DECRYPT(metaKeywords, SHA2('".$phrase."', 512)) AS CHAR) as metaKeywords,
                      CAST(AES_DECRYPT(metaDescription, SHA2('".$phrase."', 512)) AS CHAR) as  	metaDescription FROM $table3) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
                      GROUP BY pageId) AS ownPageLang ON ownPage.pageId = ownPageLang.pageId      
                      ";
            if (!empty($where))
            {
                $sql .= " ".$where;
            }    
            //echo __LINE__." ".$sql."<br>";
            $result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            while ($row2  = mysqli_fetch_array($result2, MYSQLI_ASSOC))
            {
                if (empty($row['metaKeywords']))
                {
                    $row['metaKeywords'] = $row2['metaKeywords'];
                }
                if (empty($row['metaDescription']))
                {
                    $row['metaDescription'] = $row2['metaDescription'];
                }
            }
        }
        
        return $row;
    }
    
}

function printHeader()
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
    
	if ($_SERVER['SERVER_NAME'] === 'localhost')
	{
		$url = "//localhost/lv/";
		$url_admin = "//localhost/lv/administrado/";
		$url_servotablo = "//localhost/lv/servotablo/";
	}
	else if ($_SERVER['SERVER_NAME'] === 'server01')
	{
		$url = "//server01/flexshare/lv/";
		$url_admin = "//server01/flexshare/lv/administrado/";
		$url_servotablo = "//server01/flexshare/lv/servotablo/";
	}
	else if (strpos($_SERVER['SERVER_NAME'], 'lvteknik.com') !== false)
	{
		$url = "//www.lvteknik.com/";
		$url_admin = "//www.lvteknik.com/administrado/";
		$url_servotablo = "//www.lvteknik.com/servotablo/";
	}
	else
	{
		$url = "//www.lvteknik.se/";
		$url_admin = "//www.lvteknik.se/administrado/";
		$url_servotablo = "//www.lvteknik.se/servotablo/";
	}	
	
    $langStrings = getlangstrings();
    $printHeader = $langStrings['printHeader'];

    $printHeader_array = getLangstringsArray('printHeader_array', $displayLang);
    
    echo "<head>";
      echo "<!-- Required meta tags -->";
      echo "<meta charset=\"utf-8\">";
    
        $metaData = getHeaderMetaData();
    
        if (!empty($metaData['metaDescription']))
        {
            echo "<meta name=\"description\" content=\"".$metaData['metaDescription']."\">";
        }
    
        if (!empty($metaData['metaKeywords']))
        {
            echo "<meta name=\"keywords\" content=\"".$metaData['metaKeywords']."\">";
        }
    
      echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">";
      echo "<meta name=\"description\" content=\"megakit,business,company,agency,multipurpose,modern,bootstrap4\">";

      echo "<meta name=\"author\" content=\"https://www.binarysolutions.se\">";

      echo "<title>LV Teknik</title>";

      echo "<!-- bootstrap.min css -->";
      echo "<link rel=\"stylesheet\" href=\"plugins/bootstrap/css/bootstrap.min.css\">";
      echo "<!-- Icon Font Css -->";
      echo "<link rel=\"stylesheet\" href=\"plugins/themify/css/themify-icons.css\">";
      echo "<link rel=\"stylesheet\" href=\"plugins/fontawesome/css/all.css\">";
      echo "<link rel=\"stylesheet\" href=\"plugins/magnific-popup/dist/magnific-popup.css\">";
      echo "<!-- Owl Carousel CSS -->";
      echo "<link rel=\"stylesheet\" href=\"plugins/slick-carousel/slick/slick.css\">";
      echo "<link rel=\"stylesheet\" href=\"plugins/slick-carousel/slick/slick-theme.css\">";
	
		echo "<link rel=\"stylesheet\" href=\"".$url."ext/titatoggle/dist/titatoggle-dist-min.css\">";
		echo "<link rel=\"stylesheet\" href=\"".$url."ext/fancytree/dist/skin-win8/ui.fancytree.min.css\">";

      echo "<!-- Main Stylesheet -->";
      echo "<link rel=\"stylesheet\" href=\"css/style.css.php?pageId=".$_GET['pageId']."\">";

    echo "</head>";

    echo "<body>";


    echo "<!-- Header Start -->"; 

    echo "<header class=\"navigation\">";
        echo "<div class=\"header-top \">";
            echo "<div class=\"container\">";
                echo "<div class=\"row justify-content-between align-items-center\">";
                    echo "<div class=\"col-lg-2 col-md-4\">";
                        /*echo "<div class=\"header-top-socials text-center text-lg-left text-md-left\">";
                            echo "<a href=\"https://www.facebook.com/themefisher\" target=\"_blank\"><i class=\"ti-facebook\"></i></a>";
                            echo "<a href=\"https://twitter.com/themefisher\" target=\"_blank\"><i class=\"ti-twitter\"></i></a>";
                            echo "<a href=\"https://github.com/themefisher/\" target=\"_blank\"><i class=\"ti-github\"></i></a>";
                        echo "</div>";*/
                    echo "</div>";
                    echo "<div class=\"col-lg-10 col-md-8 text-center text-lg-right text-md-right\">";
                        echo "<div class=\"header-top-info\">";
                            //echo "<a href=\"tel:+46-0730-969599\">Call Us : <span>(+46) 0730-969599</span></a>";
                            echo $printHeader[1];
                            echo "<a href=\"mailto:wedin@lvteknik.se\" ><i class=\"fa fa-envelope mr-2\"></i><span>wedin@lvteknik.se</span></a>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
        echo "<nav class=\"navbar navbar-expand-lg  py-4\" id=\"navbar\">";
            echo "<div class=\"container\">";
              echo "<a class=\"navbar-brand\" href=\"index.php\">";
                //echo "LV<span>teknik</span>";
                echo "<img src=\"".$url."img/lvteknik_300_transparent.png"."\" style = \"height : 40px;\" alt=\"Logo\">";
              echo "</a>";
    
                  echo "<button class=\"navbar-toggler collapsed\" type=\"button\" data-toggle=\"collapse\" data-target=\"#navbarsExample09\" aria-controls=\"navbarsExample09\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">";
                    echo "<span class=\"fa fa-bars\"></span>";
                  echo "</button>";
                    echo "<div class=\"collapse navbar-collapse text-center\" id=\"navbarsExample09\">";
			        echo "<ul class=\"navbar-nav ml-auto\">";
                    $menu = getMenu();

                    foreach ($menu as $key => $row)
                    {
                        if (($oldDepth > (int)$row['depth']))
                        {
                            for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                            {
                                echo "</ul></li>";
                            }                        
                        }

                        /*if ( (int)$row['depth'] == 0)
                        {
                            echo "<li class=\"nav-item\">";
                                    echo "<a href=\"".$url;
                                    if (!empty($row['folder']) && !empty($row['file']))
                                    {
                                        echo $row['folder']."/";
                                    }
                                    if (!empty($row['file']))
                                    {
                                        echo $row['file'];
                                    }
                                    else
                                    {
                                        echo "#";
                                    }
                                echo "\" class=\"nav-link\">";
                                     echo $row['note'];
                                    echo "</a>";
                                  echo "</li>";
                        }
                        else
                        {*/
                            if (((int)$row['lft'] +1) < (int)$row['rgt'])
                            {
                                if ((int)$row['depth'] > 0)
                                { 
                                    echo "<li class = \"dropdown dropdown-submenu dropleft\">";
                                    echo "<a id = \"".$row['tableKey']."\" class=\"dropdown-item dropdown-toggle\" href=\"";
                                    if (!empty($row['folder']) && !empty($row['file']))
                                    {
                                        echo $row['folder']."/";
                                    }
                                    if ($row['type'] === "text")
                                    {
                                        echo "ownpage.php?pageId=".$row['tableKey'];
                                    }
                                    
                                    else if (!empty($row['file']))
                                    {
                                        if (!empty($row['file']))
                                        {
                                            echo $row['file'];
                                        }
                                    }
                                    else
                                    {
                                        echo "#";
                                    }
                                    echo "\" role=\"button\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">".$row['note']."</a>";
                                    echo "<ul class=\"dropdown-menu\" aria-labelledby=\"".$row['tableKey']."\">";
                                   
                                }
                                else
                                {
                                    echo "<li class=\"nav-item dropdown @@".$row['tableKey']."\">";
                                     echo "<a class=\"nav-link dropdown-toggle\" href=\"";
                                        if (!empty($row['folder']) && !empty($row['file']))
                                        {
                                            echo $row['folder']."/";
                                        }
                                        if ($row['type'] === "text")
                                        {
                                            echo "ownpage.php?pageId=".$row['tableKey'];
                                        }

                                        else if (!empty($row['file']))
                                        {
                                            if (!empty($row['file']))
                                            {
                                                echo $row['file'];
                                            }
                                        }
                                        else
                                        {
                                            echo "#";
                                        }
                                    echo "\" id=\"".$row['tableKey']."\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">".$row['note']." <i class=\"fas fa-chevron-down small
                                        \"></i></a>";
                                    echo "<ul class=\"dropdown-menu\" aria-labelledby=\"".$row['tableKey']."\">";
                                
                                }
                                
                            }
                            else
                            {
                                if ((int)$row['depth'] > 0)
                                {
                                    echo "<li >";
                                    echo "<a class = \"dropdown-item\" href=\"".$url;
                                    
                                    if (!empty($row['folder']) && !empty($row['file']))
                                    {
                                        echo $row['folder']."/";
                                    }
                                    if ($row['type'] === "text")
                                    {
                                        echo "ownpage.php?pageId=".$row['tableKey'];
                                    }
                                    
                                    else if (!empty($row['file']))
                                    {
                                        if (!empty($row['file']))
                                        {
                                            echo $row['file'];
                                        }
                                    }
                                    else
                                    {
                                        echo "#";
                                    }
                                    echo "\" class=\"dropdown-item\">";
                                     echo $row['note'];
                                    echo "</a>";
                                  echo "</li>";
                                }
                                else
                                {
                                    echo "<li class=\"nav-item";
                                        if (basename($_SERVER['SCRIPT_FILENAME']) == $row['file'] || basename(__FILE__) == "index.php")
                                        {
                                            echo " "."active";
                                        }
                                        else if (basename($_SERVER['SCRIPT_FILENAME']) == "ownpage.php" && $_GET['pageId'] == $row['tableKey'])
                                        {
                                            echo " "."active";
                                        }
                                    echo "\">";
                                    echo "<a href=\"".$url;
                                    if (!empty($row['folder']) && !empty($row['file']))
                                    {
                                        echo $row['folder']."/";
                                    }
                                    
                                    if ($row['type'] === "text" || $row['type'] === "gallery")
                                    {
                                        echo "ownpage.php?pageId=".$row['tableKey'];
                                    }
                                    else if (!empty($row['file']))
                                    {
                                        if (!empty($row['file']))
                                        {
                                            echo $row['file'];
                                        }
                                    }
                                    else if (!empty($row['file']))
                                    {
                                        if (!empty($row['file']))
                                        {
                                            echo $row['file'];
                                        }
                                    }
                                    else
                                    {
                                        echo "#";
                                    }
                                echo "\" class=\"nav-link\">";
                                     echo $row['note'];
                                    echo "</a>";
                                  echo "</li>";
                                }

                            }
                            $oldDepth = (int)$row['depth'];
                        //}

                    }
                    if ($oldDepth > 0)
                    {
                        for ($i = 0; $i <= $oldDepth; $i++)
                        {
                            echo "</ul></li>";
                        }                        
                    }
                    echo "</ul>";
                  /*echo "<li class=\"nav-item active\">";
                    echo "<a class=\"nav-link\" href=\"index.php\">Home <span class=\"sr-only\">(current)</span></a>";
                  echo "</li>";
                  echo "<li class=\"nav-item dropdown\">";
                        echo "<a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"dropdown03\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">About</a>";
                        echo "<ul class=\"dropdown-menu\" aria-labelledby=\"dropdown03\">";
                            echo "<li><a class=\"dropdown-item\" href=\"about.html\">Our company</a></li>";
                            echo "<li><a class=\"dropdown-item\" href=\"pricing.html\">Pricing</a></li>";
                        echo "</ul>";
                  echo "</li>";
                   echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"service.html\">Services</a></li>";
                   echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"project.html\">Portfolio</a></li>";
                   echo "<li class=\"nav-item dropdown\">";
                        echo "<a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"dropdown05\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">Blog</a>";
                        echo "<ul class=\"dropdown-menu\" aria-labelledby=\"dropdown05\">";
                            echo "<li><a class=\"dropdown-item\" href=\"blog-grid.html\">Blog Grid</a></li>";
                            echo "<li><a class=\"dropdown-item\" href=\"blog-sidebar.html\">Blog with Sidebar</a></li>";

                            echo "<li><a class=\"dropdown-item\" href=\"blog-single.html\">Blog Single</a></li>";
                        echo "</ul>";
                  echo "</li>";
                   echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"contact.html\">Contact</a></li>";
                echo "</ul>";

                echo "<form class=\"form-lg-inline my-2 my-md-0 ml-lg-4 text-center\">";
                  echo "<a href=\"contact.html\" class=\"btn btn-solid-border btn-round-full\">Get a Quote</a>";
                echo "</form>";*/
              echo "</div>";
            echo "</div>";
        echo "</nav>";
    echo "</header>";

    echo "<!-- Header Close -->"; 

    
}

function printmodalTheme()
{
    echo "<section class=\"section intro\">";
        echo "<div class=\"container\">";
            echo "<div class=\"row \">";
                echo "<div class=\"col-lg-8\">";
                    echo "<div class=\"section-title\">";
                        echo "<span class=\"h6 text-color \">We are creative & expert people</span>";
                        echo "<h2 class=\"mt-3 content-title\">We work with business & provide solution to client with their business problem </h2>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
            echo "<div class=\"row justify-content-center\">";
                echo "<div class=\"col-lg-4 col-md-6 col-12\">";
                    echo "<div class=\"intro-item mb-5 mb-lg-0\">"; 
                        echo "<i class=\"ti-desktop color-one\"></i>";
                        echo "<h4 class=\"mt-4 mb-3\">Modern & Responsive design</h4>";
                        echo "<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odit, ducimus.</p>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"col-lg-4 col-md-6\">";
                    echo "<div class=\"intro-item mb-5 mb-lg-0\">";
                        echo "<i class=\"ti-medall color-one\"></i>"; 
                        echo "<h4 class=\"mt-4 mb-3\">Awarded licensed company</h4>";
                        echo "<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odit, ducimus.</p>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"col-lg-4 col-md-6\">";
                    echo "<div class=\"intro-item\">";
                        echo "<i class=\"ti-layers-alt color-one\"></i>";
                        echo "<h4 class=\"mt-4 mb-3\">Build your website Professionally</h4>";
                        echo "<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odit, ducimus.</p>";
                    echo "</div>";
                echo "</div>"; 
            echo "</div>";
        echo "</div>";
    echo "</section>";

    echo "<!-- Section Intro END -->";
    echo "<!-- Section About Start -->";

    echo "<section class=\"section about position-relative\">";
        echo "<div class=\"bg-about\"></div>";
        echo "<div class=\"container\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-lg-6 offset-lg-6 offset-md-0\">";
                    echo "<div class=\"about-item \">";
                        echo "<span class=\"h6 text-color\">What we are</span>";
                        echo "<h2 class=\"mt-3 mb-4 position-relative content-title\">We are dynamic team of creative people</h2>";
                        echo "<div class=\"about-content\">";
                            echo "<h4 class=\"mb-3 position-relative\">We are Perfect Solution</h4>";
                            echo "<p class=\"mb-5\">We provide consulting services in the area of IFRS and management reporting, helping companies to reach their highest level. We optimize business processes, making them easier.</p>";

                            echo "<a href=\"#\" class=\"btn btn-main btn-round-full\">Get started</a>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</section>";

    echo "<!-- Section About End -->";
    echo "<!-- section Counter Start -->";
    echo "<section class=\"section counter\">";
        echo "<div class=\"container\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-lg-3 col-md-6 col-sm-6\">";
                    echo "<div class=\"counter-item text-center mb-5 mb-lg-0\">";
                        echo "<h3 class=\"mb-0\"><span class=\"counter-stat font-weight-bold\">1730</span> +</h3>";
                        echo "<p class=\"text-muted\">Project Done</p>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"col-lg-3 col-md-6 col-sm-6\">";
                    echo "<div class=\"counter-item text-center mb-5 mb-lg-0\">";
                        echo "<h3 class=\"mb-0\"><span class=\"counter-stat font-weight-bold\">125 </span>M </h3>";
                        echo "<p class=\"text-muted\">User Worldwide</p>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"col-lg-3 col-md-6 col-sm-6\">";
                    echo "<div class=\"counter-item text-center mb-5 mb-lg-0\">";
                        echo "<h3 class=\"mb-0\"><span class=\"counter-stat font-weight-bold\">39</span></h3>";
                        echo "<p class=\"text-muted\">Availble Country</p>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"col-lg-3 col-md-6 col-sm-6\">";
                    echo "<div class=\"counter-item text-center\">";
                        echo "<h3 class=\"mb-0\"><span class=\"counter-stat font-weight-bold\">14</span></h3>";
                        echo "<p class=\"text-muted\">Award Winner </p>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</section>";
    echo "<!-- section Counter End  -->";
    echo "<!--  Section Services Start -->";
    echo "<section class=\"section service border-top\">";
        echo "<div class=\"container\">";
            echo "<div class=\"row justify-content-center\">";
                echo "<div class=\"col-lg-7 text-center\">";
                    echo "<div class=\"section-title\">";
                        echo "<span class=\"h6 text-color\">Our Services</span>";
                        echo "<h2 class=\"mt-3 content-title \">We provide a wide range of creative services </h2>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";

            echo "<div class=\"row justify-content-center\">";
                echo "<div class=\"col-lg-4 col-md-6 col-sm-6\">";
                    echo "<div class=\"service-item mb-5\">";
                        echo "<i class=\"ti-desktop\"></i>";
                        echo "<h4 class=\"mb-3\">Web development.</h4>";
                        echo "<p>A digital agency isn't here to replace your internal team, we're here to partner</p>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-4 col-md-6 col-sm-6\">";
                    echo "<div class=\"service-item mb-5\">";
                        echo "<i class=\"ti-layers\"></i>";
                        echo "<h4 class=\"mb-3\">Interface Design.</h4>";
                        echo "<p>A digital agency isn't here to replace your internal team, we're here to partner</p>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-4 col-md-6 col-sm-6\">";
                    echo "<div class=\"service-item mb-5\">";
                        echo "<i class=\"ti-bar-chart\"></i>";
                        echo "<h4 class=\"mb-3\">Business Consulting.</h4>";
                        echo "<p>A digital agency isn't here to replace your internal team, we're here to partner</p>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-4 col-md-6 col-sm-6\">";
                    echo "<div class=\"service-item mb-5 mb-lg-0\">";
                        echo "<i class=\"ti-vector\"></i>";
                        echo "<h4 class=\"mb-3\">Branding.</h4>";
                        echo "<p>A digital agency isn't here to replace your internal team, we're here to partner</p>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-4 col-md-6 col-sm-6\">";
                    echo "<div class=\"service-item mb-5 mb-lg-0\">";
                        echo "<i class=\"ti-android\"></i>";
                        echo "<h4 class=\"mb-3\">App development.</h4>";
                        echo "<p>A digital agency isn't here to replace your internal team, we're here to partner</p>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-4 col-md-6 col-sm-6\">";
                    echo "<div class=\"service-item mb-5 mb-lg-0\">";
                        echo "<i class=\"ti-pencil-alt\"></i>";
                        echo "<h4 class=\"mb-3\">Content creation.</h4>";
                        echo "<p>A digital agency isn't here to replace your internal team, we\'re here to partner</p>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</section>";
    echo "<!--  Section Services End -->";
     echo "<!-- Section Cta Start -->";
    echo "<section class=\"section cta\">";
        echo "<div class=\"container\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-lg-5\">";
                    echo "<div class=\"cta-item  bg-white p-5 rounded\">";
                        echo "<span class=\"h6 text-color\">We create for you</span>";
                        echo "<h2 class=\"mt-2 mb-4\">Entrust Your Project to Our Best Team of Professionals</h2>";
                        echo "<p class=\"lead mb-4\">Have any project on mind? For immidiate support :</p>";
                        echo "<h3><i class=\"ti-mobile mr-3 text-color\"></i>+23 876 65 455</h3>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</section>";
    echo "<!--  Section Cta End-->";
    echo "<!-- Section Testimonial Start -->";
    echo "<section class=\"section testimonial\">";
        echo "<div class=\"container\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-lg-7 \">";
                    echo "<div class=\"section-title\">";
                        echo "<span class=\"h6 text-color\">Clients testimonial</span>";
                        echo "<h2 class=\"mt-3 content-title\">Check what\'s our clients say about us</h2>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";

        echo "<div class=\"container\">";
            echo "<div class=\"row testimonial-wrap\">";
                echo "<div class=\"testimonial-item position-relative\">";
                    echo "<i class=\" text-color\"></i>";

                    echo "<div class=\"testimonial-item-content\">";
                        echo "<p class=\"testimonial-text\">Quam maiores perspiciatis temporibus odio reiciendis error alias debitis atque consequuntur natus iusto recusandae numquam corrupti facilis blanditiis.</p>";

                        echo "<div class=\"testimonial-author\">";
                            echo "<h5 class=\"mb-0 text-capitalize\">Thomas Johnson</h5>";
                            echo "<p>Excutive Director,themefisher</p>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"testimonial-item position-relative\">";
                    echo "<i class=\" text-color\"></i>";

                    echo "<div class=\"testimonial-item-content\">";
                        echo "<p class=\"testimonial-text\">Consectetur adipisicing elit. Quam maiores perspiciatis temporibus odio reiciendis error alias debitis atque consequuntur natus iusto recusandae .</p>";

                        echo "<div class=\"testimonial-author\">";
                            echo "<h5 class=\"mb-0 text-capitalize\">Mickel hussy</h5>";
                            echo "<p>Excutive Director,themefisher</p>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"testimonial-item position-relative\">";
                    echo "<i class=\" text-color\"></i>";

                    echo "<div class=\"testimonial-item-content\">";
                        echo "<p class=\"testimonial-text\">Quam maiores perspiciatis temporibus odio reiciendis error alias debitis atque consequuntur natus iusto recusandae numquam corrupti.</p>";

                        echo "<div class=\"testimonial-author\">";
                            echo "<h5 class=\"mb-0 text-capitalize\">James Watson</h5>";
                            echo "<p>Excutive Director,themefisher</p>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"testimonial-item position-relative\">";
                    echo "<i class=\" text-color\"></i>";

                    echo "<div class=\"testimonial-item-content\">";
                        echo "<p class=\"testimonial-text\">Consectetur adipisicing elit. Quam maiores perspiciatis temporibus odio reiciendis error alias debitis atque consequuntur natus iusto recusandae .</p>";

                        echo "<div class=\"testimonial-author\">";
                            echo "<h5 class=\"mb-0 text-capitalize\">Mickel hussy</h5>";
                            echo "<p>Excutive Director,themefisher</p>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</section>";
    echo "<!-- Section Testimonial End -->";
    echo "<section class=\"section latest-blog bg-2\">";
        echo "<div class=\"container\">";
            echo "<div class=\"row justify-content-center\">";
                echo "<div class=\"col-lg-7 text-center\">";
                    echo "<div class=\"section-title\">";
                        echo "<span class=\"h6 text-color\">Latest News</span>";
                        echo "<h2 class=\"mt-3 content-title text-white\">Latest articles to enrich knowledge</h2>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";

            echo "<div class=\"row justify-content-center\">";
                echo "<div class=\"col-lg-4 col-md-6 mb-5\">";
                    echo "<div class=\"card bg-transparent border-0\">";
                        echo "<img src=\"images/blog/1.jpg\" alt=\"\" class=\"img-fluid rounded\">";

                        echo "<div class=\"card-body mt-2\">";
                            echo "<div class=\"blog-item-meta\">";
                                echo "<a href=\"#\" class=\"text-white-50\">Design<span class=\"ml-2 mr-2\">/</span></a>";
                                echo "<a href=\"#\"  class=\"text-white-50\">Ui/Ux<span class=\"ml-2\">/</span></a>";
                                echo "<a href=\"#\" class=\"text-white-50 ml-2\"><i class=\"fa fa-user mr-2\"></i>admin</a>";
                            echo "</div>"; 

                            echo "<h3 class=\"mt-3 mb-5 lh-36\"><a href=\"#\" class=\"text-white \">How to improve design with typography?</a></h3>";

                            echo "<a href=\"blog-single.html\" class=\"btn btn-small btn-solid-border btn-round-full text-white\">Learn More</a>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-4 col-md-6 mb-5\">";
                    echo "<div class=\"card border-0 bg-transparent\">";
                        echo "<img src=\"images/blog/2.jpg\" alt=\"\" class=\"img-fluid rounded\">";

                        echo "<div class=\"card-body mt-2\">";
                            echo "<div class=\"blog-item-meta\">";
                                echo "<a href=\"#\" class=\"text-white-50\">Design<span class=\"ml-2 mr-2\">/</span></a>";
                                echo "<a href=\"#\"  class=\"text-white-50\">Ui/Ux<span class=\"ml-2\">/</span></a>";
                                echo "<a href=\"#\" class=\"text-white-50 ml-2\"><i class=\"fa fa-user mr-2\"></i>admin</a>";
                            echo "</div>";  

                            echo "<h3 class=\"mt-3 mb-5 lh-36\"><a href=\"#\" class=\"text-white\">Interactivity design may connect consumer</a></h3>";

                            echo "<a href=\"blog-single.html\" class=\"btn btn-small btn-solid-border btn-round-full text-white\">Learn More</a>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-4 col-md-6 mb-5\">";
                    echo "<div class=\"card border-0 bg-transparent\">";
                        echo "<img src=\"images/blog/3.jpg\" alt=\"\" class=\"img-fluid rounded\">";

                        echo "<div class=\"card-body mt-2\">";
                            echo "<div class=\"blog-item-meta\">";
                                echo "<a href=\"#\" class=\"text-white-50\">Design<span class=\"ml-2 mr-2\">/</span></a>";
                                echo "<a href=\"#\"  class=\"text-white-50\">Ui/Ux<span class=\"ml-2\">/</span></a>";
                                echo "<a href=\"#\" class=\"text-white-50 ml-2\"><i class=\"fa fa-user mr-2\"></i>admin</a>";
                            echo "</div>"; 

                            echo "<h3 class=\"mt-3 mb-5 lh-36\"><a href=\"#\" class=\"text-white\">Marketing Strategy to bring more affect</a></h3>";

                            echo "<a href=\"blog-single.html\" class=\"btn btn-small btn-solid-border btn-round-full text-white\">Learn More</a>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</section>";

    echo "<section class=\"mt-70 position-relative\">";
        echo "<div class=\"container\">";
        echo "<div class=\"cta-block-2 bg-gray p-5 rounded border-1\">";
            echo "<div class=\"row justify-content-center align-items-center \">";
                echo "<div class=\"col-lg-7\">";
                    echo "<span class=\"text-color\">For Every type business</span>";
                    echo "<h2 class=\"mt-2 mb-4 mb-lg-0\">Entrust Your Project to Our Best Team of Professionals</h2>";
                echo "</div>";
                echo "<div class=\"col-lg-4\">";
                    echo "<a href=\"contact.html\" class=\"btn btn-main btn-round-full float-lg-right \">Contact Us</a>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</div>";

    echo "</section>";
}

function displayFooter()
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
    $displayFooter = $langStrings['displayFooter'];

    $displayFooter_array = getLangstringsArray('displayFooter_array', $displayLang);
    
    echo "<!-- footer Start -->";
    echo "<footer class=\"footer section\">";
        echo "<div class=\"container\">";
            $table = "`".PREFIX."menu_footer`";
            $table2 = "`".PREFIX."menu_footer_lang`";
            $table10 = "`".PREFIX."menu`";
            $table11 = "`".PREFIX."menu_lang`";

            $sql = "SELECT *, menu.lft as lft, menu.rgt as rgt FROM (SELECT node.menuId, node.masterMenuId, node.lft, node.rgt, node.tableKey, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) as char) as folder, CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) as char) as file, node.displayMenu, COUNT(parent.lft) - 1 as depth
            FROM $table AS node,
                  $table AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
            ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
                  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
                  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
                  GROUP BY menuId) AS lang ON menu.menuId = lang.menuId 
                  LEFT OUTER JOIN (SELECT *,  CAST(AES_DECRYPT(folder, SHA2('".$phrase."', 512)) AS CHAR) as folder2, 
                  CAST(AES_DECRYPT(file, SHA2('".$phrase."', 512)) AS CHAR) as file2 FROM $table10) as t2 ON t2.menuId = menu.masterMenuId 
                  WHERE menu.displayMenu > 0 AND depth = 0 ORDER BY menu.lft";
            //echo __LINE__." ".$sql."<br>";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

            $headers = mysqli_num_rows($result);
    
            $k = 1;
    
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $headersData[$k] = $row;
                $k++;
            }
    
            if ($_SERVER['SERVER_NAME'] === 'localhost')
            {
                $url = "//localhost/lv/";
                $url_admin = "//localhost/lv/administrado/";
                $url_servotablo = "//localhost/lv/servotablo/";
            }
            else if ($_SERVER['SERVER_NAME'] === 'server01')
	{
		$url = "//server01/flexshare/lv/";
		$url_admin = "//server01/flexshare/lv/administrado/";
		$url_servotablo = "//server01/flexshare/lv/servotablo/";
	}
	else if (strpos($_SERVER['SERVER_NAME'], 'lvteknik.com') !== false)
	{
		$url = "//www.lvteknik.com/";
		$url_admin = "//www.lvteknik.com/administrado/";
		$url_servotablo = "//www.lvteknik.com/servotablo/";
	}
	else
	{
		$url = "//www.lvteknik.se/";
		$url_admin = "//www.lvteknik.se/administrado/";
		$url_servotablo = "//www.lvteknik.se/servotablo/";
	}	
            echo "<div class=\"row\">";
    
            for ($t = 1; $t <= $headers; $t++)
            {
                echo "<div class=\"col-lg-".(12/(int)$headers)." col-md-6 col-sm-6\">";
                    echo "<div class=\"widget\">";
                        echo "<h4 class=\"text-capitalize mb-4\">".$headersData[$t]['note']."</h4>";
                        echo "<ul class=\"list-unstyled footer-menu lh-35\">";
                
                            $sql = "SELECT *, menu.folder as folder, menu.file as file FROM (SELECT node.menuId, node.masterMenuId, node.lft, node.rgt, node.tableKey, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) as char) as folder, CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) as char) as file, node.displayMenu, COUNT(parent.lft) - 1 as depth
                                FROM $table AS node,
                                      $table AS parent
                                WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
                                ORDER BY node.lft) as menu LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
                                      CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
                                      CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
                                      GROUP BY menuId) AS lang ON menu.menuId = lang.menuId 
                                      LEFT OUTER JOIN (SELECT *,  CAST(AES_DECRYPT(folder, SHA2('".$phrase."', 512)) AS CHAR) as folder2, 
                                      CAST(AES_DECRYPT(file, SHA2('".$phrase."', 512)) AS CHAR) as file2 FROM $table10) as t2 ON t2.menuId = menu.masterMenuId 
                                      LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
                                          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
                                          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table11) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
                                          GROUP BY menuId) AS menuLang ON menuLang.menuId = menu.masterMenuId
                                      WHERE menu.displayMenu > 0 AND menu.lft > ".$headersData[$t]['lft']." AND menu.rgt < ".$headersData[$t]['rgt']."";
                            //echo __LINE__." ".$sql."<br><br><br>";
							$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

                            if (mysqli_num_rows($result) === 0)
                            {
                                $sql = "SELECT *, menu.folder as folder, menu.file as file FROM (SELECT node.menuId, node.masterMenuId, node.lft, node.rgt, node.tableKey, CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) as char) as folder, CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) as char) as file, node.displayMenu, COUNT(parent.lft) - 1 as depth
                                FROM $table AS node,
                                      $table AS parent
                                WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
                                ORDER BY node.lft) as menu LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
                                      CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
                                      CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
                                      GROUP BY menuId) AS lang ON menu.menuId = lang.menuId 
                                      LEFT OUTER JOIN (SELECT *,  CAST(AES_DECRYPT(folder, SHA2('".$phrase."', 512)) AS CHAR) as folder2, 
                                      CAST(AES_DECRYPT(file, SHA2('".$phrase."', 512)) AS CHAR) as file2 FROM $table10) as t2 ON t2.menuId = menu.masterMenuId 
                                      LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
                                          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
                                          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table11) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
                                          GROUP BY menuId) AS menuLang ON menuLang.menuId = menu.masterMenuId
                                      WHERE menu.displayMenu > 0 AND menu.lft >= ".$headersData[$t]['lft']." AND menu.rgt <= ".$headersData[$t]['rgt']."";
                                //echo __LINE__." ".$sql."<br>";
                                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                            }
                
                            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                            {
                                
                                    if (empty($row['file']))
                                    {
                                        echo "<li><a href = \"";
                                        echo $url."ownpage.php?pageId=".$row['tableKey'];
                                        echo "\">".$row['note']."</a></li>";
                                    }
                                    else if (!empty($row['file2']))
                                    {
                                        if (!empty($row['folder2']))
                                        {
                                            include_once($url.$row['folder2']."/".$row['file2']);
                                        }
                                        else
                                        {
                                            include_once($url.$row['file2']);
                                        }
                                    }
                                    else if (!empty($row['file']))
                                    {
                                        if (!empty($row['folder']))
                                        {
                                            if (file_exists($row['folder']."/".$row['file']))
                                            {
                                                include_once($row['folder']."/".$row['file']);
                                            }
                                            else
                                            {
                                                include_once("../".$row['folder']."/".$row['file']);
                                            }
                                            
                                        }
                                        else
                                        {
                                            if (file_exists($row['file']))
                                            {
                                                include_once($row['file']);
                                            }
                                            else
                                            {
                                                include_once("../".$row['file']);
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if (!empty($row['folder']))
                                        {
                                            if (file_exists($row['folder2']."/".$row['file2']))
                                            {
                                                include_once($row['folder2']."/".$row['file2']);
                                            }
                                            else
                                            {
                                                include_once("../".$row['folder2']."/".$row['file2']);
                                            }
                                            
                                        }
                                        else
                                        {
                                            if (file_exists($row['file2']))
                                            {
                                                include_once($row['file2']);
                                            }
                                            else
                                            {
                                                include_once("../".$row['file2']);
                                            }
                                        }
                                    }
                                
                            }
                            /*echo "<li><a href=\"#\">Terms & Conditions</a></li>";
                            echo "<li><a href=\"#\">Privacy Policy</a></li>";
                            echo "<li><a href=\"#\">Support</a></li>";
                            echo "<li><a href=\"#\">FAQ</a></li>";*/
                        echo "</ul>";
                    echo "</div>";
                echo "</div>";
            }
            echo "</div>";
    
            
    
            /*echo "<div class=\"row\">";
                echo "<div class=\"col-lg-3 col-md-6 col-sm-6\">";
                    echo "<div class=\"widget\">";
                        echo "<h4 class=\"text-capitalize mb-4\">Company</h4>";

                        echo "<ul class=\"list-unstyled footer-menu lh-35\">";
                            echo "<li><a href=\"#\">Terms & Conditions</a></li>";
                            echo "<li><a href=\"#\">Privacy Policy</a></li>";
                            echo "<li><a href=\"#\">Support</a></li>";
                            echo "<li><a href=\"#\">FAQ</a></li>";
                        echo "</ul>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"col-lg-2 col-md-6 col-sm-6\">";
                    echo "<div class=\"widget\">";
                        echo "<h4 class=\"text-capitalize mb-4\">Quick Links</h4>";

                        echo "<ul class=\"list-unstyled footer-menu lh-35\">";
                            echo "<li><a href=\"#\">About</a></li>";
                            echo "<li><a href=\"#\">Services</a></li>";
                            echo "<li><a href=\"#\">Team</a></li>";
                            echo "<li><a href=\"#\">Contact</a></li>";
                        echo "</ul>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"col-lg-3 col-md-6 col-sm-6\">";
                    echo "<div class=\"widget\">";
                        echo "<h4 class=\"text-capitalize mb-4\">Subscribe Us</h4>";
                        echo "<p>Subscribe to get latest news article and resources  </p>";

                        echo "<form action=\"#\" class=\"sub-form\">";
                            echo "<input type=\"text\" class=\"form-control mb-3\" placeholder=\"Subscribe Now ...\">";
                            echo "<a href=\"#\" class=\"btn btn-main btn-small\">subscribe</a>";
                        echo "</form>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-3 ml-auto col-sm-6\">";
                    echo "<div class=\"widget\">";
                        echo "<div class=\"logo mb-4\">";
                            echo "<h3>Mega<span>kit.</span></h3>";
                        echo "</div>";
                        echo "<h6><a href=\"tel:+23-345-67890\" >Support@megakit.com</a></h6>";
                        echo "<a href=\"mailto:support@gmail.com\"><span class=\"text-color h4\">+23-456-6588</span></a>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";/**/

            echo "<div class=\"footer-btm pt-4\">";
                echo "<div class=\"row\">";
                    echo "<div class=\"col-lg-12\">";
                        echo "<div class=\"copyright\">";
                            echo "&copy; 2017 - ".date("Y")." ".$displayFooter[1];
                        echo "</div>";
                    echo "</div>";
                    /*echo "<div class=\"col-lg-6 text-left text-lg-right\">";
                        echo "<ul class=\"list-inline footer-socials\">";
                            echo "<li class=\"list-inline-item\"><a href=\"https://www.facebook.com/themefisher\"><i class=\"ti-facebook mr-2\"></i>Facebook</a></li>";
                            echo "<li class=\"list-inline-item\"><a href=\"https://twitter.com/themefisher\"><i class=\"ti-twitter mr-2\"></i>Twitter</a></li>";
                            echo "<li class=\"list-inline-item\"><a href=\"https://www.pinterest.com/themefisher/\"><i class=\"ti-linkedin mr-2 \"></i>Linkedin</a></li>";
                        echo "</ul>";
                    echo "</div>";*/
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</footer>";

        echo "</div>";

        echo "<!--";
        echo "Essential Scripts";
        echo "=====================================-->";


        echo "<!-- Main jQuery -->";
        echo "<script src=\"ext/jQuery/jquery.min.js\"></script>";
		echo "<script src=\"ext/jQuery/jquery-ui.min.js\"></script>";
        echo "<script src=\"js/contact.js\"></script>";
        echo "<!-- Bootstrap 4.3.1 -->";
        echo "<script src=\"plugins/bootstrap/js/popper.js\"></script>";
        echo "<script src=\"plugins/bootstrap/js/bootstrap.min.js\"></script>";
       echo "<!--  Magnific Popup-->";
        echo "<script src=\"plugins/magnific-popup/dist/jquery.magnific-popup.min.js\"></script>";
        echo "<!-- Slick Slider -->";
        echo "<script src=\"plugins/slick-carousel/slick/slick.min.js\"></script>";
        echo "<!-- Counterup -->";
        echo "<script src=\"plugins/counterup/jquery.waypoints.min.js\"></script>";
        echo "<script src=\"plugins/counterup/jquery.counterup.min.js\"></script>";

        echo "<!-- Google Map -->";
        //echo "<script src=\"plugins/google-map/map.js\"></script>";
        //echo "<script src=\"https://maps.googleapis.com/maps/api/js?key=AIzaSyAkeLMlsiwzp6b3Gnaxd86lvakimwGA6UA&callback=initMap\"></script>";    

		echo "<script src=\"".$url."ext/fancytree/dist/jquery.fancytree-all-deps.min.js\"></script>";
	
        echo "<script src=\"js/script.js\"></script>";
        echo "<script src=\"index.js\"></script>";

      echo "</body>";
      echo "</html>";
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']))
{
    printHeader();
    printmodalTheme();
    displayFooter();
}

?>
   