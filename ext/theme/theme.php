<?php

echo __LINE__." ".__FILE__." Hmmmmmm....<br>";

if (isset($_SESSION['uid']))
{
  $userSettings = getUserSettings();

    $displayLang = array_map("trim", explode(",", $userSettings['langService']));
  
}


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

if (isset($_SESSION['userLang']) && !isset($_SESSION['uid']))
        {
            $order[] = "WHEN lang = '".$_SESSION['userLang']."' THEN -1";
            $order_lang[] = "WHEN code = '".$$_SESSION['userLang']."' THEN -1";
            $i++;
        }

foreach ($displayLang as $key => $value)
{
    $order[] = "WHEN lang = '".$value."' THEN ".$i;
    $order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
    $i++;
}

if (!isset($_SESSION['userLang']))
{
    $_SESSION['userLang'] = reset($displayLang);
}


function displayHeader()
{
  echo "<!--

  =========================================================
  * Now UI Kit - v1.3.0
  =========================================================

  * Product Page: https://www.creative-tim.com/product/now-ui-kit
  * Copyright 2019 Creative Tim (http://www.creative-tim.com)
  * Licensed under MIT (https://github.com/creativetimofficial/now-ui-kit/blob/master/LICENSE.md)

  * Designed by www.invisionapp.com Coded by www.creative-tim.com

  =========================================================

  * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

  -->";
  echo "<!DOCTYPE html>";
  echo "<html lang=\"en\">";

  echo "<head>";
    echo "<meta charset=\"utf-8\" />";
    echo "<link rel=\"apple-touch-icon\" sizes=\"76x76\" href=\"./ext/theme/assets/img/apple-icon.png\">";
    echo "<link rel=\"icon\" type=\"image/png\" href=\"./ext/theme/assets/img/favicon.png\">";
    echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" />";
    echo "<title>";
      echo "Mina projekt";
    echo "</title>";
    echo "<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />";
    echo "<!--     Fonts and icons     -->";
    echo "<link href=\"https://fonts.googleapis.com/css?family=Montserrat:400,700,200\" rel=\"stylesheet\" />";
    echo "<link rel=\"stylesheet\" href=\"https://use.fontawesome.com/releases/v5.7.1/css/all.css\" integrity=\"sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr\" crossorigin=\"anonymous\">";
    echo "<!-- CSS Files -->";
    echo "<link href=\"./ext/bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\" />";
    echo "<link href=\"./ext/theme/assets/css/now-ui-kit.css?v=1.3.0\" rel=\"stylesheet\" />";
    echo "<!-- CSS Just for demo purpose, don't include it in your project -->";
    echo "<link href=\"./ext/theme/assets/demo/demo.css\" rel=\"stylesheet\" />";
    echo "<link href=\"./ext/titatoggle/dist/titatoggle-dist-min.css\" rel=\"stylesheet\" />";

    echo "<link href=\"./ext/fancytree/src/skin-win8/ui.fancytree.css\" rel=\"stylesheet\">";
    echo "<link href=\"./ext/bootstrap-select/dist/css/bootstrap-select.min.css\" rel=\"stylesheet\">";

    echo "<style rel = \"stylesheet\">";
        echo "selecpicker {
    border: 1px solid #fff;
    background-color: transparent;
}";
    echo "</style>";
  echo "</head>";
}


function navbar()
{
  //Definera och sätt en del variabler med data

  global $link;

  global $phrase;

    $userSettings = getUserSettings();

    if (!empty($displayLang))
    {
        //Do nothing
    }
    if (isset($_SESSION['uid']))
    {
        $userSettings = getUserSettings();

        $displayLang = array_map("trim", explode(",", $userSettings['langService']));
    }

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

        if (isset($_SESSION['userLang']))
        {
            $temp[] = $_SESSION['userLang'];
        }
        
        $temp2 = array_filter(array_unique($displayLang));
        $displayLang = array_merge($temp, $temp2);
    }
    else
    {    
        if (isset($_SESSION['userLang']))
        {
            $temp[] = $_SESSION['userLang'];
        }
        
        $temp2 = array_map("trim", explode(",", $userSettings['langService']));
        $displayLang = array_merge($temp, $temp2);
    }

    $i = 0;
    
    if (isset($_SESSION['userLang']))
    {
        $displayLang[] = $_SESSION['userLang'];
        $order[] = "WHEN lang = '".$_SESSION['userLang']."' THEN -1";
        $order_lang[] = "WHEN code = '".$$_SESSION['userLang']."' THEN -1";
        $i++;
    }
    
    foreach ($displayLang as $key => $value)
    {
        $order[] = "WHEN lang = '".$value."' THEN ".$i;
        $order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
        $i++;
    }

    if (!isset($_SESSION['userLang']))
    {
        $_SESSION['userLang'] = reset($displayLang);
    }
    
  if ($_SERVER['SERVER_NAME'] === 'localhost')
  {
    $url = "//localhost/lv/";
  }
  else if ($_SERVER['SERVER_NAME'] === 'server01')
  {
    $url = "//server01/flexshare/ep/";
  }
  else
  {
    $url = "//www.lvteknik.se/";
  }
    
    $langStrings = getlangstrings();
    $navbar = $langStrings['navbar'];

    $navbar_array = getLangstringsArray('navbar_array', $displayLang);
  
//Hämta ut själva trädets struktur från databasen, denna är i lagrad i normalform samt använder sig av modified tree traversal algoritm http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/

  $table = "`".PREFIX."menu`";
  $table2 = "`".PREFIX."menu_lang`";

  $sql = "SELECT node.menuId, (COUNT(parent.menuId) - 1) AS depth
  FROM $table AS node,
  $table AS parent
  WHERE node.lft BETWEEN parent.lft AND parent.rgt AND displayMenu > 0
  GROUP BY node.lft
  ORDER BY node.lft;";
echo __LINE__." ".$sql."<br>";	
  $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

  while ($row = mysqli_fetch_array($result))
  {
    $depth[$row['menuId']] = $row['depth'];
  }

  $sql = "SELECT * FROM (SELECT node.*
  FROM $table AS node,
          $table AS parent
  WHERE node.lft BETWEEN parent.lft AND parent.rgt AND displayMenu > 0 group by node.lft
  ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM(SELECT menuId, 
          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
          GROUP BY menuId) AS lang ON menu.menuId = lang.menuId ORDER BY menu.lft";
 echo __LINE__." ".$sql."<br>";
 $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));


 echo "<body class=\"profile-page index-page sidebar-collapse\">";
 //echo "<body class=\" sidebar-collapse\">";
 echo "<!-- Navbar -->";
  echo "<nav class=\"navbar navbar-expand-lg bg-primary fixed-top navbar-transparent \" color-on-scroll=\"400\">";
    echo "<div class=\"container\">";
      echo "<div class=\"navbar-translate\">";
        echo "<a class=\"navbar-brand\" href=\"index.php\" rel=\"tooltip\" title=\"".$navbar[1]."\" data-placement=\"bottom\" target=\"_blank\">";
          echo "Dokumenti";
        echo "</a>";
        echo "<button class=\"navbar-toggler navbar-toggler\" type=\"button\" data-toggle=\"collapse\" data-target=\"#navigation\" aria-controls=\"navigation-index\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">";
          echo "<span class=\"navbar-toggler-bar top-bar\"></span>";
          echo "<span class=\"navbar-toggler-bar middle-bar\"></span>";
          echo "<span class=\"navbar-toggler-bar bottom-bar\"></span>";
        echo "</button>";
      echo "</div>";
     echo "<div class=\"collapse navbar-collapse justify-content-end\" id=\"navigation\" data-nav-image=\"./ext/theme/assets/img/blurred-image-1.jpg\">";
        echo "<ul class=\"navbar-nav\">";
        $old_depth = 0;
        if (!isset($_SESSION['uid']))
        {
            echo "<li class=\"nav-item\">";
            //echo "<a class=\"nav-link\" href=\"#\">";
                $temp = getDisplayLang();
                $tempRev = getDisplayLang(false);
                echo "<select class = \"selectpicker\" id  = \"selectLang\" data-style = \"btn-light\">";
                    foreach ($temp as $key => $value)
                    {
                        echo "<option value = \"".$key."\"";
                        if ($_SESSION['userLang'] == $tempRev[$key])
                        {
                            echo " "."selected";
                        }
                        echo ">".$value."</option>";
                    }
                echo "</select>";
              //echo "</a>";
            echo "</li>";
        }
         
        while ($row = mysqli_fetch_array($result))
        {
          if ((int)$row['display'] === 0 && isset($_SESSION['uid']))
          {
            continue;
          }
          else if ((int)$row['display'] > 0 && !isset($_SESSION['uid']))
          {
            continue;
          }
          if ($old_depth > (int)$depth[$row['menuId']])
          {
            for ($i = 0; $i <=  ($old_depth - (int)$depth[$row['menuId']]); $i++)
            {
              echo "</div>";
              echo "</li>";
            }
            
          }
          if (($row['lft']+1) < (int)$row['rgt'])
          {
            echo "<li class=\"nav-item dropdown\">";
            echo "<a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navbarDropdownMenuLink".$row['menuId']."\" data-toggle=\"dropdown\">";
              echo "<i class=\"now-ui-icons design_app\"></i>";
              echo "<p>".$row['note']."</p>";
            echo "</a>";
            echo "<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"navbarDropdownMenuLink".$row['menuId']."\">";
          }
          else if ((int)$depth[$row['menuId']] > 0)
          {
            
              echo "<a class=\"dropdown-item\" href=\"".$url.$row['folder'].$row['file']."\">";
                echo "<i class=\"now-ui-icons business_chart-pie-36\"></i> ".$row['note'];
              echo "</a>";
            
          }
          else
          {
            echo "<li class=\"nav-item\">";
              echo "<a class=\"nav-link\" href=\"".$url.$row['folder'].$row['file']."\">";
                echo "<i class=\"now-ui-icons arrows-1_cloud-download-93\"></i>";
                echo "<p>".$row['note']."</p>";
              echo "</a>";
            echo "</li>";
          }
          $old_depth = (int)$depth[$row['menuId']];
          //echo $row['note']."<br>";
        }
        for ($i = 0; $i < $old_depth; $i++)
        {
          echo "</div></li>";
        }

        /*echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" rel=\"tooltip\" title=\"Follow us on Twitter\" data-placement=\"bottom\" href=\"https://twitter.com/CreativeTim\" target=\"_blank\">";
              echo "<i class=\"fab fa-twitter\"></i>";
              echo "<p class=\"d-lg-none d-xl-none\">Twitter</p>";
            echo "</a>";
          echo "</li>";
          echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" rel=\"tooltip\" title=\"Like us on Facebook\" data-placement=\"bottom\" href=\"https://www.facebook.com/CreativeTim\" target=\"_blank\">";
              echo "<i class=\"fab fa-facebook-square\"></i>";
              echo "<p class=\"d-lg-none d-xl-none\">Facebook</p>";
            echo "</a>";
          echo "</li>";
          echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" rel=\"tooltip\" title=\"Follow us on Instagram\" data-placement=\"bottom\" href=\"https://www.instagram.com/CreativeTimOfficial\" target=\"_blank\">";
              echo "<i class=\"fab fa-instagram\"></i>";
              echo "<p class=\"d-lg-none d-xl-none\">Instagram</p>";
            echo "</a>";
          echo "</li>";*/

        echo "</ul>";
      echo "</div>";
    echo "</div>";
  
    echo "</nav>";
  echo "<!-- End Navbar -->";
  
}

  
     /* echo "<div class=\"collapse navbar-collapse justify-content-end\" id=\"navigation\" data-nav-image=\"./ext/theme/assets/img/blurred-image-1.jpg\">";
        echo "<ul class=\"navbar-nav\">";
          echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" href=\"javascript:void(0)\" onclick=\"scrollToDownload()\">";
              echo "<i class=\"now-ui-icons arrows-1_cloud-download-93\"></i>";
              echo "<p>Download</p>";
            echo "</a>";
          echo "</li>";
          echo "<li class=\"nav-item dropdown\">";
            echo "<a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navbarDropdownMenuLink1\" data-toggle=\"dropdown\">";
              echo "<i class=\"now-ui-icons design_app\"></i>";
              echo "<p>Components</p>";
            echo "</a>";
            echo "<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"navbarDropdownMenuLink1\">";
              echo "<a class=\"dropdown-item\" href=\"./index.html\">";
                echo "<i class=\"now-ui-icons business_chart-pie-36\"></i> All components";
              echo "</a>";
              echo "<a class=\"dropdown-item\" target=\"_blank\" href=\"https://demos.creative-tim.com/now-ui-kit/docs/1.0/getting-started/introduction.html\">";
                echo "<i class=\"now-ui-icons design_bullet-list-67\"></i> Documentation";
              echo "</a>";
            echo "</div>";
          echo "</li>";
          echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link btn btn-neutral\" href=\"https://www.creative-tim.com/product/now-ui-kit-pro\" target=\"_blank\">";
              echo "<i class=\"now-ui-icons arrows-1_share-66\"></i>";
              echo "<p>Upgrade to PRO</p>";
            echo "</a>";
          echo "</li>";
          echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" rel=\"tooltip\" title=\"Follow us on Twitter\" data-placement=\"bottom\" href=\"https://twitter.com/CreativeTim\" target=\"_blank\">";
              echo "<i class=\"fab fa-twitter\"></i>";
              echo "<p class=\"d-lg-none d-xl-none\">Twitter</p>";
            echo "</a>";
          echo "</li>";
          echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" rel=\"tooltip\" title=\"Like us on Facebook\" data-placement=\"bottom\" href=\"https://www.facebook.com/CreativeTim\" target=\"_blank\">";
              echo "<i class=\"fab fa-facebook-square\"></i>";
              echo "<p class=\"d-lg-none d-xl-none\">Facebook</p>";
            echo "</a>";
          echo "</li>";
          echo "<li class=\"nav-item\">";
            echo "<a class=\"nav-link\" rel=\"tooltip\" title=\"Follow us on Instagram\" data-placement=\"bottom\" href=\"https://www.instagram.com/CreativeTimOfficial\" target=\"_blank\">";
              echo "<i class=\"fab fa-instagram\"></i>";
              echo "<p class=\"d-lg-none d-xl-none\">Instagram</p>";
            echo "</a>";
          echo "</li>";
*/
    function contactAuthor($userReplaceKey = null)
  {
      $replaceTable = getReplaceTable();
        
        echo __LINE__." ".basename(__FILE__)." ".$userReplaceKey."<br>";
      
        echo "<div class=\"section section-contact-us text-center\">";
            echo "<div class=\"container\">";
                echo "<h2 class=\"title\">Vill du kontakta upphovsmannen?</h2>";
                echo "<p class=\"description\">Ange info nedan så återkommer de snarast!</p>";
                echo "<form id = \"contactUserForm\" method = \"post\" action = \"contact.php\">";
                    echo "<div class=\"row\">";
                        echo "<div class=\"col-lg-6 text-center col-md-8 ml-auto mr-auto\">";
                            echo "<div class=\"input-group input-lg\">";
                                echo "<div class=\"input-group-prepend\">";
                                    echo "<span class=\"input-group-text\">";
                                        echo "<i class=\"now-ui-icons users_circle-08\"></i>";
                                    echo "</span>";
                                echo "</div>";

                                echo "<input type=\"text\" id = \"name\" name = \"name\" class=\"form-control\" placeholder=\"Ditt namn...\">";
                            echo "</div>";

                            echo "<div class=\"input-group input-lg\">";
                                echo "<div class=\"input-group-prepend\">";
                                    echo "<span class=\"input-group-text\">";
                                        echo "<i class=\"now-ui-icons ui-1_email-85\"></i>";
                                    echo "</span>";
                                echo "</div>";
                                echo "<input type=\"text\" id = \"email\" name = \"email\" class=\"form-control\" placeholder=\"Email...\">";
                            echo "</div>";

                            echo "<div class=\"textarea-container\">";
                                echo "<textarea class=\"form-control\" name=\"message\" id = \"message\" rows=\"4\" cols=\"80\" placeholder=\"Skriv ditt meddelande...\"></textarea>";
                            echo "</div>";

                            echo "<input type = \"hidden\" id = \"contactLang\" name =\"contactLang\" value = \"".$_SESSION['userLang']."\">"; 

                            echo "<input type = \"hidden\" id = \"replaceTable\" name =\"replaceTable\" value = \"".$replaceTable[PREFIX."contact_user_messages"]."\">"; 
        
                            echo "<input type = \"hidden\" id = \"account\" name =\"account\" value = \"".$userReplaceKey."\">"; 

                            echo "<div class=\"send-button\">";
                                echo "<button type = \"button\" class=\"btn btn-primary btn-round btn-block btn-lg sendContactForm\" data-target_form = \"contactUserForm\">Sänd meddelande</a>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                echo "</form>";

                echo "<input type = \"hidden\" id = \"errorMessage\" value = \"Vänliga kontrollera att alla fält är ifylda!\">";
            echo "</div>";
        echo "</div>";
  }  

  function contactUs()
  {
        $replaceTable = getReplaceTable();
      
        $userSettings = getUserSettings();

        if (!empty($displayLang))
        {
            //Do nothing
        }
        if (isset($_SESSION['uid']))
        {
            $userSettings = getUserSettings();

            $displayLang = array_map("trim", explode(",", $userSettings['langService']));
        }

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

            if (isset($_SESSION['userLang']))
            {
                $temp[] = $_SESSION['userLang'];
            }

            $temp2 = array_filter(array_unique($displayLang));
            $displayLang = array_merge($temp, $temp2);
        }
        else
        {    
            if (isset($_SESSION['userLang']))
            {
                $temp[] = $_SESSION['userLang'];
            }

            $temp2 = array_map("trim", explode(",", $userSettings['langService']));
            $displayLang = array_merge($temp, $temp2);
        }

        $i = 0;

        if (isset($_SESSION['userLang']))
        {
            $displayLang[] = $_SESSION['userLang'];
            $order[] = "WHEN lang = '".$_SESSION['userLang']."' THEN -1";
            $order_lang[] = "WHEN code = '".$$_SESSION['userLang']."' THEN -1";
            $i++;
        }

        foreach ($displayLang as $key => $value)
        {
            $order[] = "WHEN lang = '".$value."' THEN ".$i;
            $order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
            $i++;
        }

        if (!isset($_SESSION['userLang']))
        {
            $_SESSION['userLang'] = reset($displayLang);
        }

        $langStrings = getlangstrings();
        $contactUs = $langStrings['contactUs'];

        $contactUs_array = getLangstringsArray('contactUs_array', $displayLang);
      
      
    echo "<div class=\"section section-contact-us text-center\">";
        echo "<div class=\"container\">";
            echo "<h2 class=\"title\">".$contactUs[1]."</h2>";
            echo "<p class=\"description\">".$contactUs[2]."</p>";
            echo "<form id = \"contactForm\" method = \"post\" action = \"contact.php\">";
                echo "<div class=\"row\">";
                    echo "<div class=\"col-lg-6 text-center col-md-8 ml-auto mr-auto\">";
                        echo "<div class=\"input-group input-lg\">";
                            echo "<div class=\"input-group-prepend\">";
                                echo "<span class=\"input-group-text\">";
                                    echo "<i class=\"now-ui-icons users_circle-08\"></i>";
                                echo "</span>";
                            echo "</div>";

                            echo "<input type=\"text\" id = \"name\" name = \"name\" class=\"form-control\" placeholder=\"".$contactUs[3]."\">";
                        echo "</div>";

                        echo "<div class=\"input-group input-lg\">";
                            echo "<div class=\"input-group-prepend\">";
                                echo "<span class=\"input-group-text\">";
                                    echo "<i class=\"now-ui-icons ui-1_email-85\"></i>";
                                echo "</span>";
                            echo "</div>";
                            echo "<input type=\"text\" id = \"email\" name = \"email\" class=\"form-control\" placeholder=\"".$contactUs[4]."\">";
                        echo "</div>";

                        echo "<div class=\"textarea-container\">";
                            echo "<textarea class=\"form-control\" name=\"message\" id = \"message\" rows=\"4\" cols=\"80\" placeholder=\"".$contactUs[5]."\"></textarea>";
                        echo "</div>";

                        echo "<input type = \"hidden\" id = \"contactLang\" name =\"contactLang\" value = \"".$_SESSION['userLang']."\">"; 
      
                        echo "<input type = \"hidden\" id = \"replaceTable\" name =\"replaceTable\" value = \"".$replaceTable[PREFIX."contact_messages"]."\">"; 
      
                        echo "<div class=\"send-button\">";
                            echo "<button type = \"button\" class=\"btn btn-primary btn-round btn-block btn-lg sendContactForm\" data-target_form = \"contactForm\">".$contactUs[6]."</a>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
            echo "</form>";
      
            echo "<input type = \"hidden\" id = \"errorMessage\" value = \"".$contactUs[7]."\">";
        echo "</div>";
    echo "</div>";
  }  

    function displayFooter()
    {
        if (isset($_SESSION['uid']))
        {
          $userSettings = getUserSettings();

            $displayLang = array_map("trim", explode(",", $userSettings['langService']));

        }

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

        if (isset($_SESSION['userLang']) && !isset($_SESSION['uid']))
        {
            $order[] = "WHEN lang = '".$_SESSION['userLang']."' THEN -1";
            $order_lang[] = "WHEN code = '".$$_SESSION['userLang']."' THEN -1";
            $i++;
        }

        foreach ($displayLang as $key => $value)
        {
            $order[] = "WHEN lang = '".$value."' THEN ".$i;
            $order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
            $i++;
        }

        if (!isset($_SESSION['userLang']))
        {
            $_SESSION['userLang'] = reset($displayLang);
        }
        
        $langStrings = getlangstrings();
		$displayFooter = $langStrings['displayFooter'];

		$displayFooter_array = getLangstringsArray('displayFooter_array', $displayLang);
        
        
        if ($_SERVER['SERVER_NAME'] === 'localhost')
		{
			$url = "//localhost/lv/";
		}
		else if ($_SERVER['SERVER_NAME'] === 'server01')
		{
			$url = "//server01/flexshare/ep/";
		}
		else
		{
			$url = "//mina-projekt.se/";
		}    
        
      echo "<footer class=\"footer\" data-background-color=\"black\">";
        echo "<div class=\" container \">";
          echo "<nav>";
            echo "<ul>";
              echo "<li>";
                echo "<a href=\"".$url."\">";
                  echo "Dokumenti";
                echo "</a>";
              echo "</li>";
              echo "<li>";
                echo "<a href=\"about.php\">";
                  echo $displayFooter[2];
                echo "</a>";
              echo "</li>";
              /*echo "<li>";
                echo "<a href=\"http://blog.creative-tim.com\">";
                  echo "Blog";
                echo "</a>";
              echo "</li>";*/
            echo "</ul>";
          echo "</nav>";
          echo "<div class=\"copyright\" id=\"copyright\">";
            echo "&copy;";
            echo "<script>";
              echo "document.getElementById('copyright').appendChild(document.createTextNode(new Date().getFullYear())) ";
            echo "</script>";
            /*echo "Design av ";
            echo "<a href=\"https://www.invisionapp.com\" target=\"_blank\">Invision</a>. Tema Now UI Kit av <a href = href=\"https://www.creative-tim.com\" target = \"_blank\">Creative Tim</a> , utvecklad av ";
            echo "Anders Wallin och Fredrik Hjärpe.";*/
            echo " ".$displayFooter[3];
          echo "</div>";
        echo "</div>";
      echo "</footer>";
    echo "</div>";
  
  }

	function scripts()
  	{
    	echo "<!--   Core JS Files   -->";
    	echo "<script src=\"./ext/theme/assets/js/core/jquery.min.js\" type=\"text/javascript\"></script>";
       
    	echo "<script src=\"./ext/bootstrap/js/bootstrap.bundle.min.js\" type=\"text/javascript\"></script>";
        echo "<script src=\"./ext/theme/assets/js/core/bootstrap.min.js\" type=\"text/javascript\"></script>";
    	//echo "<script src=\"./ext/theme/assets/js/core/bootstrap.min.js\" type=\"text/javascript\"></script>";
    	echo "<!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->";
    	echo "<script src=\"./ext/theme/assets/js/plugins/bootstrap-switch.js\"></script>";
    	echo "<!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->";
    	echo "<script src=\"./ext/theme/assets/js/plugins/nouislider.min.js\" type=\"text/javascript\"></script>";
    	echo "<!--  Plugin for the DatePicker, full documentation here: https://github.com/uxsolutions/bootstrap-datepicker -->";
    	echo "<script src=\"./ext/theme/assets/js/plugins/bootstrap-datepicker.js\" type=\"text/javascript\"></script>";
    	echo "<!-- Control Center for Now Ui Kit: parallax effects, scripts for the example pages etc -->";
    	echo "<script src=\"./ext/theme/assets/js/now-ui-kit.js?v=1.3.0\" type=\"text/javascript\"></script>";
    
      echo "<script src=\"./ext/jQuery/jquery-ui.min.js\" type=\"text/javascript\"></script>";
      echo "<script src=\"./ext/fancytree/dist/jquery.fancytree-all.min.js\" type=\"text/javascript\"></script>";
        echo "<script src=\"./ext/bootstrap-select/dist/js/bootstrap-select.min.js\" type=\"text/javascript\"></script>";
	      
echo "<script src=\"./ext/theme/assets/js/core/popper.min.js\" type=\"text/javascript\"></script>";
  echo "<script src=\"./ext/theme/assets/js/core/bootstrap.min.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"index.js\" type=\"text/javascript\"></script>";
	  
    	/*echo "<script>";
			echo "$(document).ready(function() {";
				echo ".// the body of this function is in assets/js/now-ui-kit.js";
				echo "nowuiKit.initSliders();";
			echo "});";

			echo "function scrollToDownload() {";
				echo "if ($('.section-download').length != 0) {";
					echo "$(\"html, body\").animate({";
						echo "scrollTop: $('.section-download').offset().top";
					echo "}, 1000);";
				echo "}";
			echo "}";
    	echo "</script>";*/
	echo "</body>";
  echo "</html>";
  }
  