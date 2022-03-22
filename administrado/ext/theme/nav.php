<?php
session_start();
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
    if (empty($_SESSION['uid']))
    {
        if ($_SERVER['SERVER_NAME'] === 'localhost')
        {
            $url = "//localhost/lv/";
        }
        else if ($_SERVER['SERVER_NAME'] === 'server01')
        {
            $url = "//server01/flexshare/lv/";
        }
		else if ($_SERVER['SERVER_NAME'] === 'www.lvteknik.com')
		{
			$url = "//www.lvteknik.com/";
			$url_admin = "//www.lvteknik.com/administrado/";
		}
        else
        {
            $url = "//www.lvteknik.se/";
        }
        header("Location: ".$url);
    }

	/*$table = "`".PREFIX."user2account`";
	$table2 = "`".PREFIX."account`";

	$sql = "SELECT * FROM $table t1 LEFT JOIN $table2 t2 ON t1.accountId = t2.autoId  WHERE t1.userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."' AND t1.accountId = '".mysqli_real_escape_string($link, $_SESSION['accountId'])."'";
    //echo __LINE__." ".$sql."<br>";
	$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link));
    $first = true;
	while ($row = mysqli_fetch_array($result))
	{
		include_once("kliento/".substr($row['replaceKey'], 0,1)."/".substr($row['replaceKey'], 0,2)."/".$row['replaceKey']."/config/db-c.php");
		include_once("../kliento/".substr($row['replaceKey'], 0,1)."/".substr($row['replaceKey'], 0,2)."/".$row['replaceKey']."/config/db-c.php");
		include_once("../../kliento/".substr($row['replaceKey'], 0,1)."/".substr($row['replaceKey'], 0,2)."/".$row['replaceKey']."/config/db-c.php");
		include_once("../../../kliento/".substr($row['replaceKey'], 0,1)."/".substr($row['replaceKey'], 0,2)."/".$row['replaceKey']."/config/db-c.php");
		include_once("../../../../kliento/".substr($row['replaceKey'], 0,1)."/".substr($row['replaceKey'], 0,2)."/".$row['replaceKey']."/config/db-c.php");
		call_user_func("checkdb_k_".$row['accountId']);
        if ($first)
        {
            if (empty($_SESSION['accountId']))
            {
                $_SESSION['accountId'] = $row['accountId'];
                $first = false;
            }    
        }
		
	}*/

    include_once("../../../common/crypto.php");

	include_once("../../../common/userData.php");
	
	function printHeader()
	{
		global $link;

		global $phrase;

		$lang = 'sv';

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
		else if ($_SERVER['SERVER_NAME'] === 'lvteknik.com')
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
		echo "<!--

		=========================================================
		* Now UI Dashboard - v1.5.0
		=========================================================

		* Product Page: https://www.creative-tim.com/product/now-ui-dashboard
		* Copyright 2019 Creative Tim (http://www.creative-tim.com)

		* Designed by www.invisionapp.com Coded by www.creative-tim.com

		=========================================================

		* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

		-->";
        

		echo "<head>";
        
		  echo "<meta charset=\"utf-8\" />";
		  echo "<link rel=\"apple-touch-icon\" sizes=\"76x76\" href=\"".$url_admin."ext/theme/assets/img/apple-icon.png\">";
		  echo "<link rel=\"icon\" type=\"image/png\" href=\"".$url_admin."ext/theme/assets/img/favicon.png\">";
		  echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" />";
		  echo "<title>";
			echo "LV Teknik";
		  echo "</title>";
		  echo "<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />";
		  echo "<!--     Fonts and icons     -->";
		  echo "<link href=\"https://fonts.googleapis.com/css?family=Montserrat:400,700,200\" rel=\"stylesheet\" />";
		  echo "<link rel=\"stylesheet\" href=\"https://use.fontawesome.com/releases/v5.7.1/css/all.css\" integrity=\"sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr\" crossorigin=\"anonymous\">";
		  echo "<!-- CSS Files -->";
		  echo "<link href=\"".$url."ext/bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\" />";
		  echo "<link href=\"".$url_admin."ext/theme/assets/css/now-ui-dashboard.css?v=1.5.0\" rel=\"stylesheet\" />";
		  echo "<!-- CSS Just for demo purpose, don't include it in your project -->";
		  echo "<link href=\"".$url_admin."ext/theme/assets/demo/demo.css\" rel=\"stylesheet\" />";
		
        echo "<!-- CSS Just for fancyTree, render nicer tree views -->";
		echo "<link href=\"".$url."ext/fancytree/dist/skin-win8/ui.fancytree.min.css\" rel=\"stylesheet\" />";
		
		echo "<!-- CSS Just for selectpicker, render nicer selects -->";
		echo "<link href=\"".$url."ext/bootstrap-select/dist/css/bootstrap-select.min.css\" rel=\"stylesheet\" />";
		
		echo "<!-- CSS Just for dataTables, render nicer table layouts -->";
		echo "<link href=\"".$url."ext/dataTables/datatables.min.css\" rel=\"stylesheet\" />";
		
		echo "<!-- CSS Just for dataTables, render nicer table layouts -->";
		echo "<link href=\"".$url."ext/titatoggle/dist/titatoggle-dist-min.css\" rel=\"stylesheet\" />";
        
        echo "<!-- FontAwsome, nice free icons -->";
		echo "<link href=\"".$url."ext/fontawsome/css/all.min.css\" rel=\"stylesheet\" />";
        
        echo "<link href=\"".$url."ext/jQuery/jquery-ui.min.css\" rel=\"stylesheet\" />";
        
        echo "<link href=\"".$url."ext/jquery-loading/demo/demo.css\" rel=\"stylesheet\" />";
		
        if (basename($_SERVER['SCRIPT_FILENAME']) == "index.php")
        {
            //echo "<link rel=\"stylesheet\" href=\"".$url."ext/sticky-note/css/main.css\">";
        }
        
		
        
		echo "</head>";
	}
	
	function displayMenuAdministradoHeader()
	{
		global $link;

		global $phrase;

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
		$displayMenuAdministradoHeader = $langStrings['displayMenuAdministradoHeader'];

		$ddisplayMenuAdministradoHeader_array = getLangstringsArray('displayMenuAdministradoHeader_array', $displayLang);
        
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
		else if ($_SERVER['SERVER_NAME'] === 'www.lvteknik.com')
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
		
		echo "<body class=\"\">";
		  echo "<div class=\"wrapper \">";
			echo "<div class=\"sidebar\" data-color=\"orange\">";
			  echo "<!--
				Tip 1: You can change the color of the sidebar using: data-color=\"blue | green | orange | red | yellow\"
			-->";
			  echo "<div class=\"logo\">";
				echo "<a href=\"//www.lvteknik.se\" >";
				  echo "<img src=\"".$url."img/lvteknik_300_transparent.png"."\" alt=\"Logo\">";
				echo "</a>";
			  echo "</div>";
			  echo "<div class=\"sidebar-wrapper\" id=\"sidebar-wrapper\">";
			  
            $table = "`".PREFIX."account`";
        
            /*$sql = "SELECT * FROM ".$table." WHERE autoId = '".$_SESSION['accountId']."'";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                  $accountPlan = $row['accountPlan'];
            }*/
        
			$table = "`".PREFIX."administrado_menu`";
			$table2 = "`".PREFIX."administrado_menu_lang`";
            $table3 = "`".PREFIX."icons`";
			
			$sql = "SELECT node.menuId, (COUNT(parent.menuId) - 1) AS depth
			  FROM $table AS node,
			  $table AS parent
			  WHERE node.lft BETWEEN parent.lft AND parent.rgt
			  GROUP BY node.lft
			  ORDER BY node.lft;";
			  $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

            while ($row = mysqli_fetch_array($result))
            {
                  $depth[$row['menuId']] = $row['depth'];
            }

			  $sql = "SELECT * FROM (SELECT node.tableKey,  CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) AS CHAR) as folder,  CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) AS CHAR) as file, CAST(AES_DECRYPT(node.icon, SHA2('".$phrase."', 512)) AS CHAR) as icon, REPLACE(CAST(AES_DECRYPT(node.icon, SHA2('".$phrase."', 512)) AS CHAR), 'fa-', '') as shortIcon, node.lft, node.rgt, node.menuId
			  FROM $table AS node,
					  $table AS parent
			  WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
			  ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT menuId, 
                    
					  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
					  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q 
					  ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
					  GROUP BY menuId) AS lang ON menu.menuId = lang.menuId
            LEFT OUTER JOIN (SELECT fas, far, fab, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) as CHAR) as iconNote FROM $table3) as iconData ON menu.shortIcon = iconData.iconNote
            ORDER BY menu.lft";
                //echo __LINE__." ".$sql."<br>";
			 $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
			  
				echo "<ul class=\"nav\">";
				$first = true;
				
				while ($row = mysqli_fetch_array($result))
				{
                    $temp = array_flip(array_map('trim', explode(",", $row['accountPlanId'])));
                  
                    if (!array_key_exists($accountPlan, $temp))
                    {
                        continue;
                    }
                    
                    if (userModule($row['tableKey']))
                    {
                        echo "<li ";
                        if (basename($_SERVER['SCRIPT_FILENAME']) === $row['file'])
                        {		
                            echo " "."class=\"active \"";
                            $first = false;
                        }
                        echo ">";

                            echo "<a href=\"".$url_admin;
                            if (!empty($row['folder']))
                            {
                                echo "modulo/".$row['folder']."/";
                            }
                            echo $row['file']."\">";
                          //echo "<i class=\"now-ui-icons design_app\"></i>";
                            echo "<i class=\"";
                                if ((int)$row['fas'] > 0)
                                {
                                    echo "fas"." ";
                                }
                                else if ((int)$row['far'] > 0)
                                {
                                    echo "far"." ";
                                }
                                else if ((int)$row['fab'] > 0)
                                {
                                    echo "fab"." ";
                                }
                                echo $row['icon']."\"></i>";
                          echo "<p>".$row['note']."</p>";
                        echo "</a>";
                        echo "</li>";
                    }    
				}
				  /*echo "<li class=\"active \">";
					echo "<a href=\"./dashboard.html\">";
					  echo "<i class=\"now-ui-icons design_app\"></i>";
					  echo "<p>Dashboard</p>";
					echo "</a>";
				  echo "</li>";
				  echo "<li>";
					echo "<a href=\"./icons.html\">";
					  echo "<i class=\"now-ui-icons education_atom\"></i>";
					  echo "<p>Icons</p>";
					echo "</a>";
				  echo "</li>";
				  echo "<li>";
					echo "<a href=\"./map.html\">";
					  echo "<i class=\"now-ui-icons location_map-big\"></i>";
					  echo "<p>Maps</p>";
					echo "</a>";
				  echo "</li>";
				  echo "<li>";
					echo "<a href=\"./notifications.html\">";
					  echo "<i class=\"now-ui-icons ui-1_bell-53\"></i>";
					  echo "<p>Notifications</p>";
					echo "</a>";
				  echo "</li>";
				  echo "<li>";
					echo "<a href=\"./user.html\">";
					  echo "<i class=\"now-ui-icons users_single-02\"></i>";
					  echo "<p>User Profile</p>";
					echo "</a>";
				  echo "</li>";
				  echo "<li>";
					echo "<a href=\"./tables.html\">";
					  echo "<i class=\"now-ui-icons design_bullet-list-67\"></i>";
					  echo "<p>Table List</p>";
					echo "</a>";
				  echo "</li>";
				  echo "<li>";
					echo "<a href=\"./typography.html\">";
					  echo "<i class=\"now-ui-icons text_caps-small\"></i>";
					  echo "<p>Typography</p>";
					echo "</a>";
				  echo "</li>";
				  echo "<li class=\"active-pro\">";
					echo "<a href=\"./upgrade.html\">";
					  echo "<i class=\"now-ui-icons arrows-1_cloud-download-93\"></i>";
					  echo "<p>Upgrade to PRO</p>";
					echo "</a>";
				  echo "</li>";*/
		
			echo "<li><hr></li>";
		
			$table = "`".PREFIX."servotablo_menu`";
			$table2 = "`".PREFIX."servotablo_menu_lang`";
        
            $table3 = "`".PREFIX."icons`";
			
			$sql = "SELECT parent.menuId
				FROM $table AS node,
						$table AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
						AND node.file = '".mysqli_real_escape_string($link, basename($_SERVER['SCRIPT_FILENAME']))."'
				ORDER BY parent.lft;";
			$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
			unset($selectedPath);
		
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$selectedPath[(int)$row['menuId']] = (int)$row['menuId'];
			}
		
			$sql = "SELECT node.menuId, (COUNT(parent.menuId) - 1) AS depth
			  FROM $table AS node,
			  $table AS parent
			  WHERE node.lft BETWEEN parent.lft AND parent.rgt
			  GROUP BY node.lft
			  ORDER BY node.lft;";
		      //echo __LINE__." ".$sql."<br>";
			  $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

				unset($depth);
			  while ($row = mysqli_fetch_array($result))
			  {
				$depth[$row['menuId']] = $row['depth'];
			  }

			  $sql = "SELECT * FROM (SELECT node.tableKey,  CAST(AES_DECRYPT(node.folder, SHA2('".$phrase."', 512)) AS CHAR) as folder,  CAST(AES_DECRYPT(node.file, SHA2('".$phrase."', 512)) AS CHAR) as file, CAST(AES_DECRYPT(node.icon, SHA2('".$phrase."', 512)) AS CHAR) as icon, REPLACE(CAST(AES_DECRYPT(node.icon, SHA2('".$phrase."', 512)) AS CHAR), 'fa-', '') as shortIcon, node.lft, node.rgt, node.menuId
			  FROM $table AS node,
					  $table AS parent
			  WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
			  ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT menuId, 
                    
					  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
					  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q 
					  ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
					  GROUP BY menuId) AS lang ON menu.menuId = lang.menuId
            LEFT OUTER JOIN (SELECT fas, far, fab, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) as CHAR) as iconNote FROM $table3) as iconData ON menu.shortIcon = iconData.iconNote
            ORDER BY menu.lft";
			 $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
			//echo $sql."<br>";	 
		      $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
			 
				echo "<ul class=\"nav\">";
				$first = true;
				$old_depth = 0;
				
				while ($row = mysqli_fetch_array($result))
				{
                    if (userModule($row['tableKey']))
                    {
                        if ($old_depth > (int)$row['depth'])
                        {
                            for ($i = 0; $i < ($old_depth - (int)$row['depth']); $i++)
                            {
                                echo "</ul></div></a></li>";
                            }

                        }

                        if (((int)$row['lft'] +1) < (int)$row['rgt'] )
                        {
                            echo "<li><a data-toggle=\"collapse\" href=\"#".$row['tableKey']."_list\" aria-expanded = \"";
                                if (array_key_exists((int)$row['menuId'], $selectedPath))
                                {
                                    echo "true";
                                    echo "\" class = \"\">";
                                }
                                else
                                {
                                    echo "false";
                                    echo "\" class = \"collapsed\">";
                                }

                              //echo "<i class=\"now-ui-icons design_app\"></i>";
                                echo "<i class=\"";
                                if ((int)$row['fas'] > 0)
                                {
                                    echo "fas"." ";
                                }
                                else if ((int)$row['far'] > 0)
                                {
                                    echo "far"." ";
                                }
                                else if ((int)$row['fab'] > 0)
                                {
                                    echo "fab"." ";
                                }
                                echo $row['icon']."\"></i>";
                            
                              echo "<p>".$row['note']."</p>";
                                echo "<b class = \"caret\"></b></a>";
                                echo "<div class = \"";
                                if (array_key_exists((int)$row['menuId'], $selectedPath))
                                {
                                    echo "collapse show\"";
                                }
                                else
                                {
                                    echo "collapse\"";
                                }

                                echo " id = \"".$row['tableKey']."_list\" ><ul class = \"nav\">";
                        }
                        else
                        {
                            echo "<li ";
                            if (basename($_SERVER['SCRIPT_FILENAME']) === $row['file'])
                            {		
                                echo " "."class=\"active \"";
                                $first = false;
                            }
                            echo ">";
                            echo "<a href=\"".$url_servotablo;
                            if (!empty($row['folder']))
                            {
                                echo "".$row['folder']."/";
                            }
                            echo $row['file']."\">";
                            //echo "<i class=\"now-ui-icons design_app\"></i>";
                            echo "<i class=\"";
                            if ((int)$row['fas'] > 0)
                            {
                                echo "fas"." ";
                            }
                            else if ((int)$row['far'] > 0)
                            {
                                echo "far"." ";
                            }
                            else if ((int)$row['fab'] > 0)
                            {
                                echo "fab"." ";
                            }
                            echo $row['icon']."\"></i>";
                            echo "<p>".$row['note']."</p>";

                            echo "</a>";
                          echo "</li>";
                        }

                        $old_depth = (int)$row['depth'];
                    }
                }   
		
				if ($old_depth > 0)
				{
					for ($i = 0; $i < $old_depth; $i++)
					{
						echo "</div></li>";
					}
				}
		 
				echo "</ul>";
			  echo "</div>";
			echo "</div>";
			echo "<div class=\"main-panel\" id=\"main-panel\">";
			  echo "<!-- Navbar -->";
			  echo "<nav class=\"navbar navbar-expand-lg navbar-transparent fixed-top bg-primary  navbar-absolute\" color-on-scroll = \"400\">";
				echo "<div class=\"container-fluid\">";
				  echo "<div class=\"navbar-wrapper\">";
					echo "<div class=\"navbar-toggle\">";
					  echo "<button type=\"button\" class=\"navbar-toggler\">";
						echo "<span class=\"navbar-toggler-bar bar1\"></span>";
						echo "<span class=\"navbar-toggler-bar bar2\"></span>";
						echo "<span class=\"navbar-toggler-bar bar3\"></span>";
					  echo "</button>";
					echo "</div>";
					echo "<a class=\"navbar-brand\" href=\"//www.lvteknik.se\">LV Teknik</a>";
				  echo "</div>";
				  echo "<button class=\"navbar-toggler\" type=\"button\" data-toggle=\"collapse\" data-target=\"#navigation\" aria-controls=\"navigation-index\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">";
					echo "<span class=\"navbar-toggler-bar navbar-kebab\"></span>";
					echo "<span class=\"navbar-toggler-bar navbar-kebab\"></span>";
					echo "<span class=\"navbar-toggler-bar navbar-kebab\"></span>";
				  echo "</button>";
				  echo "<div class=\"collapse navbar-collapse justify-content-end\" id=\"navigation\">";
					/*echo "<form>";
					  echo "<div class=\"input-group no-border\">";
						echo "<input type=\"text\" value=\"\" class=\"form-control\" placeholder=\"Search...\">";
						echo "<div class=\"input-group-append\">";
						  echo "<div class=\"input-group-text\">";
							echo "<i class=\"now-ui-icons ui-1_zoom-bold\"></i>";
						  echo "</div>";
						echo "</div>";
					  echo "</div>";
					echo "</form>";*/
					echo "<ul class=\"navbar-nav\">";
					  /*echo "<li class=\"nav-item\">";
						echo "<a class=\"nav-link\" href=\"#pablo\">";
						  echo "<i class=\"now-ui-icons media-2_sound-wave\"></i>";
						  echo "<p>";
							echo "<span class=\"d-lg-none d-md-block\">Stats</span>";
						  echo "</p>";
						echo "</a>";
					  echo "</li>";*/
					  /*echo "<li class=\"nav-item dropdown\">";
						echo "<a class=\"nav-link dropdown-toggle\" id=\"navbarDropdownMenuLink\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
						  echo "<i class=\"now-ui-icons location_world\"></i>";
						  echo "<p>";
							echo "<span class=\"d-lg-none d-md-block\">$displayMenu[4]</span>";
						  echo "</p>";
						echo "</a>";
						/*echo "<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"navbarDropdownMenuLink\">";
        
                            $table = "`".PREFIX."user2account`";
                            $table2 = "`".PREFIX."account`";

                            $sql = "SELECT *, CAST(AES_DECRYPT(accountName, SHA2('".$phrase."', 512)) AS CHAR) AS accountName FROM ".$table." t1 INNER JOIN ".$table2." t2 ON t1.accountId = t2.autoId WHERE userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."' ORDER BY CASE WHEN typeAccount = 'private' THEN 1 WHEN typeAccount = 'collaboration' THEN 2 WHEN typeAccount = 'company' THEN 3 ELSE 1000 END";
                            //echo __LINE__." ".$sql."<br>";
                            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));

                            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                            {
                                if (empty($row['accountName']))
                                {
                                    echo "<a class=\"dropdown-item changeAccount\" id = \"account[".$row['replaceKey']."]\" href=\"#\" data-replace_table = \"".$replaceTable[PREFIX."account"]."\">$displayMenu[3]</a>";
                                    //$data[$row['replaceKey']] = $displayMenu[3];
                                }
                                else
                                {
                                    echo "<a class=\"dropdown-item changeAccount\" id = \"account[".$row['replaceKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."account"]."\" href=\"#\">".$row['accountName']."</a>";
                                    $data[$row['replaceKey']] = $row['accountName'];
                                }

                            }
        				  /*echo "<a class=\"dropdown-item\" href=\"#\">Action23456789</a>";
						  echo "<a class=\"dropdown-item\" href=\"#\">Another action</a>";
						  echo "<a class=\"dropdown-item\" href=\"#\">Something else here</a>";*/
						/*echo "</div>";
					  echo "</li>";*/
					  /*echo "<li class=\"nav-item\">";
						echo "<a class=\"nav-link\" href=\"#pablo\">";
						  echo "<i class=\"now-ui-icons users_single-02\"></i>";
						  echo "<p>";
							echo "<span class=\"d-lg-none d-md-block\">Account</span>";
						  echo "</p>";
						echo "</a>";
					  echo "</li>";*/
        
                        echo "<li class=\"nav-item dropdown\">";
						echo "<a class=\"nav-link dropdown-toggle\" id=\"navbarDropdownMenuLink\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
						  echo "<i class=\"now-ui-icons users_single-02\"></i>";
						  echo "<p>";
							echo "<span class=\"d-lg-none d-md-block\">".$displayMenuAdministradoHeader[5]."</span>";
						  echo "</p>";
						echo "</a>";
						echo "<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"navbarDropdownMenuLink\">";
						  echo "<a class=\"dropdown-item\" href=\"".$url."logout.php\">".$displayMenuAdministradoHeader[1]."</a>";
						  echo "<a class=\"dropdown-item\" href=\"".$url."administrado/modulo/persone/editPersonal.php\">".$displayMenuAdministradoHeader[2]."</a>";
						  //echo "<a class=\"dropdown-item\" href=\"#\">Something else here</a>";
						echo "</div>";
					  echo "</li>"; 
					echo "</ul>";
				  echo "</div>";
				echo "</div>";
			  echo "</nav>";
			  echo "<!-- End Navbar -->";
	}

	function showContent()
	{
		global $link;

		global $phrase;

		$lang = 'sv';

		if ($_SERVER['SERVER_NAME'] === 'localhost')
		{
			$url = "//localhost/lv/";
			$url_admin = "//localhost/lv/administrado/";
		}
		else if ($_SERVER['SERVER_NAME'] === 'server01')
		{
			$url = "//server01/flexshare/lv/";
			$url_admin = "//server01/flexshare/lv/administrado/¨";
		}
		else if ($_SERVER['SERVER_NAME'] === 'www.lvteknik.com')
		{
			$url = "//www.lvteknik.com/";
			$url_admin = "//www.lvteknik.com/administrado/¨";
		}
		else
		{
			$url = "//www.lvteknik.se/";
			$url_admin = "//www.lvteknik.se/administrado/";
		}
		
		echo "<div class=\"panel-header panel-header-sm\">";
		
		echo "</div>";
		
		echo "<div class=\"content\" style = \"height : 100%;\">";
			echo "<div class=\"row\" style = \"height : 100%;\" >";
				echo "<div class=\"col-md-12\" style = \"height : 95%;\">";
					echo "<div class=\"card\" style = \"height : 100%;\">";
						echo "<div class=\"card-header\">";
							echo "<h1>Editera projekt</h1>";
					echo "</div>";
					echo "<div class=\"card-body\">";
						echo "<h5>Hmmmmmm!</h5>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}
	
	function displayContent2()
	{
		global $link;

		global $phrase;

		$lang = 'sv';

		if ($_SERVER['SERVER_NAME'] === 'localhost')
		{
			$url = "//localhost/lv/";
			$url_admin = "//localhost/lv/administrado/";
		}
		else if ($_SERVER['SERVER_NAME'] === 'server01')
		{
			$url = "//server01/flexshare/lv/";
			$url_admin = "//server01/flexshare/lv/administrado/¨";
		}
		else if ($_SERVER['SERVER_NAME'] === 'www.lvteknik.com')
		{
			$url = "//www.lvteknik.com/";
			$url_admin = "//www.lvteknik.com/administrado/¨";
		}
		else
		{
			$url = "//www.lvteknik.se/";
			$url_admin = "//www.lvteknik.se/administrado/";
		}
		
			echo "<div class=\"panel-header panel-header-lg\">";
				echo "<canvas id=\"bigDashboardChart\"></canvas>";
			  echo "</div>";
			  echo "<div class=\"content\">";
				echo "<div class=\"row\">";
				  echo "<div class=\"col-md-4\">";
					echo "<div class=\"card card-chart\">";
					  echo "<div class=\"card-header\">";
						echo "<h5 class=\"card-category\">Global Sales</h5>";
						echo "<h4 class=\"card-title\">Shipped Products</h4>";
						echo "<div class=\"dropdown\">";
						  echo "<button type=\"button\" class=\"btn btn-round btn-outline-default dropdown-toggle btn-simple btn-icon no-caret\" data-toggle=\"dropdown\">";
							echo "<i class=\"now-ui-icons loader_gear\"></i>";
						  echo "</button>";
						  echo "<div class=\"dropdown-menu dropdown-menu-right\">";
							echo "<a class=\"dropdown-item\" href=\"#\">Action</a>";
							echo "<a class=\"dropdown-item\" href=\"#\">Another action</a>";
							echo "<a class=\"dropdown-item\" href=\"#\">Something else here</a>";
							echo "<a class=\"dropdown-item text-danger\" href=\"#\">Remove Data</a>";
						  echo "</div>";
						echo "</div>";
					  echo "</div>";
					  echo "<div class=\"card-body\">";
						echo "<div class=\"chart-area\">";
						  echo "<canvas id=\"lineChartExample\"></canvas>";
						echo "</div>";
					  echo "</div>";
					  echo "<div class=\"card-footer\">";
						echo "<div class=\"stats\">";
						  echo "<i class=\"now-ui-icons arrows-1_refresh-69\"></i> Just Updated";
						echo "</div>";
					  echo "</div>";
					echo "</div>";
				  echo "</div>";
				  echo "<div class=\"col-lg-4 col-md-6\">";
					echo "<div class=\"card card-chart\">";
					  echo "<div class=\"card-header\">";
						echo "<h5 class=\"card-category\">2018 Sales</h5>";
						echo "<h4 class=\"card-title\">All products</h4>";
						echo "<div class=\"dropdown\">";
						  echo "<button type=\"button\" class=\"btn btn-round btn-outline-default dropdown-toggle btn-simple btn-icon no-caret\" data-toggle=\"dropdown\">";
							echo "<i class=\"now-ui-icons loader_gear\"></i>";
						  echo "</button>";
						  echo "<div class=\"dropdown-menu dropdown-menu-right\">";
							echo "<a class=\"dropdown-item\" href=\"#\">Action</a>";
							echo "<a class=\"dropdown-item\" href=\"#\">Another action</a>";
							echo "<a class=\"dropdown-item\" href=\"#\">Something else here</a>";
							echo "<a class=\"dropdown-item text-danger\" href=\"#\">Remove Data</a>";
						  echo "</div>";
						echo "</div>";
					  echo "</div>";
					  echo "<div class=\"card-body\">";
						echo "<div class=\"chart-area\">";
						  echo "<canvas id=\"lineChartExampleWithNumbersAndGrid\"></canvas>";
						echo "</div>";
					  echo "</div>";
					  echo "<div class=\"card-footer\">";
						echo "<div class=\"stats\">";
						  echo "<i class=\"now-ui-icons arrows-1_refresh-69\"></i> Just Updated";
						echo "</div>";
					  echo "</div>";
					echo "</div>";
				  echo "</div>";
				  echo "<div class=\"col-lg-4 col-md-6\">";
					echo "<div class=\"card card-chart\">";
					  echo "<div class=\"card-header\">";
						echo "<h5 class=\"card-category\">Email Statistics</h5>";
						echo "<h4 class=\"card-title\">24 Hours Performance</h4>";
					  echo "</div>";
					  echo "<div class=\"card-body\">";
						echo "<div class=\"chart-area\">";
						  echo "<canvas id=\"barChartSimpleGradientsNumbers\"></canvas>";
						echo "</div>";
					  echo "</div>";
					  echo "<div class=\"card-footer\">";
						echo "<div class=\"stats\">";
						  echo "<i class=\"now-ui-icons ui-2_time-alarm\"></i> Last 7 days";
						echo "</div>";
					  echo "</div>";
					echo "</div>";
				  echo "</div>";
				echo "</div>";
				echo "<div class=\"row\">";
				  echo "<div class=\"col-md-6\">";
					echo "<div class=\"card  card-tasks\">";
					  echo "<div class=\"card-header \">";
						echo "<h5 class=\"card-category\">Backend development</h5>";
						echo "<h4 class=\"card-title\">Tasks</h4>";
					  echo "</div>";
					  echo "<div class=\"card-body \">";
						echo "<div class=\"table-full-width table-responsive\">";
						  echo "<table class=\"table\">";
							echo "<tbody>";
							  echo "<tr>";
								echo "<td>";
								  echo "<div class=\"form-check\">";
									echo "<label class=\"form-check-label\">";
									  echo "<input class=\"form-check-input\" type=\"checkbox\" checked>";
									  echo "<span class=\"form-check-sign\"></span>";
									echo "</label>";
								  echo "</div>";
								echo "</td>";
								echo "<td class=\"text-left\">Sign contract for \"What are conference organizers afraid of?\"</td>";
								echo "<td class=\"td-actions text-right\">";
								  echo "<button type=\"button\" rel=\"tooltip\" title=\"\" class=\"btn btn-info btn-round btn-icon btn-icon-mini btn-neutral\" data-original-title=\"Edit Task\">";
									echo "<i class=\"now-ui-icons ui-2_settings-90\"></i>";
								  echo "</button>";
								  echo "<button type=\"button\" rel=\"tooltip\" title=\"\" class=\"btn btn-danger btn-round btn-icon btn-icon-mini btn-neutral\" data-original-title=\"Remove\">";
									echo "<i class=\"now-ui-icons ui-1_simple-remove\"></i>";
								  echo "</button>";
								echo "</td>";
							  echo "</tr>";
							  echo "<tr>";
								echo "<td>";
								  echo "<div class=\"form-check\">";
									echo "<label class=\"form-check-label\">";
									  echo "<input class=\"form-check-input\" type=\"checkbox\">";
									  echo "<span class=\"form-check-sign\"></span>";
									echo "</label>";
								  echo "</div>";
								echo "</td>";
								echo "<td class=\"text-left\">Lines From Great Russian Literature? Or E-mails From My Boss?</td>";
								echo "<td class=\"td-actions text-right\">";
								  echo "<button type=\"button\" rel=\"tooltip\" title=\"\" class=\"btn btn-info btn-round btn-icon btn-icon-mini btn-neutral\" data-original-title=\"Edit Task\">";
									echo "<i class=\"now-ui-icons ui-2_settings-90\"></i>";
								  echo "</button>";
								  echo "<button type=\"button\" rel=\"tooltip\" title=\"\" class=\"btn btn-danger btn-round btn-icon btn-icon-mini btn-neutral\" data-original-title=\"Remove\">";
									echo "<i class=\"now-ui-icons ui-1_simple-remove\"></i>";
								  echo "</button>";
								echo "</td>";
							  echo "</tr>";
							  echo "<tr>";
								echo "<td>";
								  echo "<div class=\"form-check\">";
									echo "<label class=\"form-check-label\">";
									  echo "<input class=\"form-check-input\" type=\"checkbox\" checked>";
									  echo "<span class=\"form-check-sign\"></span>";
									echo "</label>";
								  echo "</div>";
								echo "</td>";
								echo "<td class=\"text-left\">Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit";
								echo "</td>";
								echo "<td class=\"td-actions text-right\">";
								  echo "<button type=\"button\" rel=\"tooltip\" title=\"\" class=\"btn btn-info btn-round btn-icon btn-icon-mini btn-neutral\" data-original-title=\"Edit Task\">";
									echo "<i class=\"now-ui-icons ui-2_settings-90\"></i>";
								  echo "</button>";
								  echo "<button type=\"button\" rel=\"tooltip\" title=\"\" class=\"btn btn-danger btn-round btn-icon btn-icon-mini btn-neutral\" data-original-title=\"Remove\">";
									echo "<i class=\"now-ui-icons ui-1_simple-remove\"></i>";
								  echo "</button>";
								echo "</td>";
							  echo "</tr>";
							echo "</tbody>";
						  echo "</table>";
						echo "</div>";
					  echo "</div>";
					  echo "<div class=\"card-footer \">";
						echo "<hr>";
						echo "<div class=\"stats\">";
						  echo "<i class=\"now-ui-icons loader_refresh spin\"></i> Updated 3 minutes ago";
						echo "</div>";
					  echo "</div>";
					echo "</div>";
				  echo "</div>";
				  echo "<div class=\"col-md-6\">";
					echo "<div class=\"card\">";
					  echo "<div class=\"card-header\">";
						echo "<h5 class=\"card-category\">All Persons List</h5>";
						echo "<h4 class=\"card-title\"> Employees Stats</h4>";
					  echo "</div>";
					  echo "<div class=\"card-body\">";
						echo "<div class=\"table-responsive\">";
						  echo "<table class=\"table\">";
							echo "<thead class=\" text-primary\">";
							  echo "<th>";
								echo "Name";
							  echo "</th>";
							  echo "<th>";
								echo "Country";
							  echo "</th>";
							  echo "<th>";
								echo "City";
							  echo "</th>";
							  echo "<th class=\"text-right\">";
								echo "Salary";
							  echo "</th>";
							echo "</thead>";
							echo "<tbody>";
							  echo "<tr>";
								echo "<td>";
								  echo "Dakota Rice";
								echo "</td>";
								echo "<td>";
								  echo "Niger";
								echo "</td>";
								echo "<td>";
								  echo "Oud-Turnhout";
								echo "</td>";
								echo "<td class=\"text-right\">";
								  echo "$36,738";
								echo "</td>";
							  echo "</tr>";
							  echo "<tr>";
								echo "<td>";
								  echo "Minerva Hooper";
								echo "</td>";
								echo "<td>";
								  echo "Curaçao";
								echo "</td>";
								echo "<td>";
								  echo "Sinaai-Waas";
								echo "</td>";
								echo "<td class=\"text-right\">";
								  echo "$23,789";
								echo "</td>";
							  echo "</tr>";
							  echo "<tr>";
								echo "<td>";
								  echo "Sage Rodriguez";
								echo "</td>";
								echo "<td>";
								  echo "Netherlands";
								echo "</td>";
								echo "<td>";
								  echo "Baileux";
								echo "</td>";
								echo "<td class=\"text-right\">";
								  echo "$56,142";
								echo "</td>";
							  echo "</tr>";
							  echo "<tr>";
								echo "<td>";
								  echo "Doris Greene";
								echo "</td>";
								echo "<td>";
								  echo "Malawi";
								echo "</td>";
								echo "<td>";
								  echo "Feldkirchen in Kärnten";
								echo "</td>";
								echo "<td class=\"text-right\">";
								  echo "$63,542";
								echo "</td>";
							  echo "</tr>";
							  echo "<tr>";
								echo "<td>";
								  echo "Mason Porter";
								echo "</td>";
								echo "<td>";
								  echo "Chile";
								echo "</td>";
								echo "<td>";
								  echo "Gloucester";
								echo "</td>";
								echo "<td class=\"text-right\">";
								  echo "$78,615";
								echo "</td>";
							  echo "</tr>";
							echo "</tbody>";
						  echo "</table>";
						echo "</div>";
					  echo "</div>";
					echo "</div>";
				  echo "</div>";
				echo "</div>";
			  echo "</div>";
	}

    function displayFooterAdministrado()
    {
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
                $displayFooterAdministrado = $langStrings['displayFooterAdministrado'];

                //$displayFooterAdministrado[1] = str_replace("~~authorTheme~~", "<a href = \"//www.creative-tim.com\" target = \"_blank\">Creative Tim</a>", $displayFooter[1]);
                //$displayFooterAdministrado[1] = str_replace("~~authorCode~~", "<a href = \"mailto:info@dokumenti.net\">Anders Wallin. </a>", $displayFooter[1]);
        
                $displayFooterAdministrado_array = getLangstringsArray('displayFooterAdministrado_array', $displayLang);
                
				global $link;

				global $phrase;

				if ($_SERVER['SERVER_NAME'] === 'localhost')
				{
					$url = "//localhost/lv/";
					$url_admin = "//localhost/lv/administrado/";
				}
				else if ($_SERVER['SERVER_NAME'] === 'server01')
				{
					$url = "//server01/flexshare/lv/";
					$url_admin = "//server01/flexshare/lv/administrado/¨";
				}
				else if ($_SERVER['SERVER_NAME'] === 'www.lvteknik.com')
				{
					$url = "//www.lvteknik.com/";
					$url_admin = "//www.lvteknik.com/administrado/¨";
				}
				else
				{
					$url = "//wwww.lvteknik.se/";
					$url_admin = "//www.lvteknik.se/administrado/";
				}
				echo "<footer class=\"footer\">";
				echo "<div class=\" container-fluid \">";
				  echo "<nav>";
					/*echo "<ul>";
					  echo "<li>";
						echo "<a href=\"https://www.creative-tim.com\">";
						  echo "Creative Tim";
						echo "</a>";
					  echo "</li>";
					  echo "<li>";
						echo "<a href=\"http://presentation.creative-tim.com\">";
						  echo "About Us";
						echo "</a>";
					  echo "</li>";
					  echo "<li>";
						echo "<a href=\"http://blog.creative-tim.com\">";
						  echo "Blog";
						echo "</a>";
					  echo "</li>";
					echo "</ul>";*/
				  echo "</nav>";
				  echo "<div class=\"copyright\" id=\"copyright\">";
					echo "&copy; 2017 - <script>";
					  echo "document.getElementById('copyright').appendChild(document.createTextNode(new Date().getFullYear()))";
					echo "</script>,";
                    echo " ".$displayFooterAdministrado[1];
				  echo "</div>";
				echo "</div>";
			  echo "</footer>";
			echo "</div>";
		  echo "</div>";
	
	} 

	function printScripts($debug = false)
	{	
		global $link;

		global $phrase;

		$lang = 'sv';

		if ($_SERVER['SERVER_NAME'] === 'localhost')
		{
			$url = "//localhost/lv/";
			$url_admin = "//localhost/lv/administrado/";
		}
		else if ($_SERVER['SERVER_NAME'] === 'server01')
		{
			$url = "//server01/flexshare/lv/";
			$url_admin = "//server01/flexshare/lv/administrado/";
		}
		else if ($_SERVER['SERVER_NAME'] === 'www.lvteknik.com')
		{
			$url = "//www.lvteknik.com/";
			$url_admin = "//www.lvteknik.com/administrado/¨";
		}
		else
		{
			$url = "//www.lvteknik.se/";
			$url_admin = "//www.lvteknik.se/administrado/";
		}
		echo "<!--   Core JS Files   -->";
		  echo "<script src=\"".$url."ext/jQuery/jquery.min.js\"></script>";
        echo "<script src=\"".$url."ext/jQuery/jquery-ui.min.js\"></script>";
		  //echo "<script src=\"".$url."/assets/js/core/popper.min.js\"></script>";
		  echo "<script src=\"".$url."ext/bootstrap/js/bootstrap.bundle.min.js\"></script>";
		  echo "<script src=\"".$url_admin."ext/theme/assets/js/plugins/perfect-scrollbar.jquery.min.js\"></script>";
		  /*echo "<!--  Google Maps Plugin    -->";
		  echo "<script src=\"https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE\"></script>";*/
		  echo "<!-- Chart JS -->";
		  //echo "<script src=\"".$url_admin."ext/theme/assets/js/plugins/chartjs.min.js\"></script>";
		  echo "<!--  Notifications Plugin    -->";
		  //echo "<script src=\"".$url_admin."ext/theme/assets/js/plugins/bootstrap-notify.js\"></script>";
		  echo "<!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->";
		  echo "<script src=\"".$url_admin."ext/theme/assets/js/now-ui-dashboard.min.js?v=1.5.0\" type=\"text/javascript\"></script><!-- Now Ui Dashboard DEMO methods, don't include it in your project! -->";
		if (!$debug)
		{
			echo "<script src=\"".$url."ext/theme/assets/demo/demo.js\"></script>";
		}
        
        
		  
		echo "<script src=\"".$url_admin."index.js\"></script>";
		  
		echo "<script src=\"".$url."ext/fancytree/dist/jquery.fancytree-all-deps.min.js\"></script>";
		echo "<script src=\"".$url_admin."ext/tinymce/js/tinymce/tinymce.min.js\"></script>";
		echo "<script src=\"".$url_admin."ext/tinymce/js/tinymce/jquery.tinymce.min.js\"></script>";
		
		echo "<script src=\"".$url."ext/bootstrap-select/dist/js/bootstrap-select.min.js\"></script>";
		
		echo "<script src=\"".$url."ext/dataTables/js/jquery.dataTables.min.js\"></script>";
		
		echo "<script src=\"".$url."ext/jQuery-jeditable/jquery.jeditable.js\"></script>";
        
        echo "<script src=\"".$url."ext/jquery-loading/dist/jquery.loading.min.js\"></script>";
		
        if (basename($_SERVER['SCRIPT_FILENAME']) == "index.php")
        {
            /*echo "<script src=\"".$url."ext/sticky-note/json2.js\"></script>";
            echo "<script src=\"".$url."ext/sticky-note/sticky-note.js\"></script>";*/
        }
        
        echo "</body>";

		echo "</html>";
	}
?>