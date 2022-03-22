<?php
    error_reporting(E_ALL);
    session_start();

	if (isset($_SESSION['uid']))
	{
		if ($_SERVER['SERVER_NAME'] === 'localhost')
		{
			$url = "//localhost/";
			$url_admin = "//localhost/administrado/";
		}
		else if ($_SERVER['SERVER_NAME'] === 'server01')
		{
			$url = "//server01/flexshare/lv/";
			$url_admin = "//server01/flexshare/lv/administrado/";
		}
		else
		{
			$url = "//www.lvteknik.se/";
			$url_admin = "//www.lvteknik.se/administrado/";
		}
		header('Location: '.$url_admin);
	}

    include_once("./common/db.php");
    include_once("./common/userData.php");
    include_once("./theme.php");

	function verifyFB()
	{
		return false;
	}

	function verifyTwiter()
	{
		return false;
	}

	function verifyInstagram()
	{
		return false;
	}

	function loginUser()
	{
		global $link;
		
		global $phrase;
		
		if ($_SERVER['SERVER_NAME'] === 'localhost')
		{
			$url = "//localhost/";
			$url_a = "//localhost/administrado/common/syncDB.php";
            $url_sync_def = "//localhost/administrado/";
		}
		else if ($_SERVER['SERVER_NAME'] === 'server01')
		{
			$url = "//server01/flexshare/lv/";
			$url_a = "//server01/flexshare/lv/administrado/common/syncDB.php";
            $url_sync_def = "//server01/administrado/";
		}
		else
		{
			$url = "//www.lvteknik.se/";
			$url_a = "//wwww.lvteknik.se/administrado/common/syncDB.php";
            $url_sync_def = "//www.lvteknik.se/administrado/";
		}
		
		$table = "`".PREFIX."users`";
        $table2 = "`".PREFIX."user_settings`";
		
		$sql = "SELECT autoId, CAST(AES_DECRYPT(password, SHA2('".$phrase."',512)) AS CHAR) AS password FROM ".$table." WHERE email = AES_ENCRYPT('".mysqli_real_escape_string($link, $_POST['email'])."', SHA2('".$phrase."', 512))";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		if (mysqli_num_rows($result) > 0)
		{
			if (isset($_POST['FB']))
			{
				verifyFB();
			}
			if (isset($_POST['TW']))
			{
				verifyTwiter();
			}
			if (isset($_POST['IG']))
			{
				verifyInstagram();
			}
			else
			{
				while ($row = mysqli_fetch_array($result))
				{
					$passWord = $row['password'];

					$uid = $row['autoId'];
				}
				
				//echo __LINE__." ".$_POST['password']." ".$passWord."<br>";
				
                if (password_verify($_POST['password'], $passWord))
				{
					$_SESSION['uid'] = $uid;
                    
                    $sql = "SELECT CAST(AES_DECRYPT(setting, SHA2('".$phrase."',512)) AS CHAR) AS setting, CAST(AES_DECRYPT(data, SHA2('".$phrase."',512)) AS CHAR) AS data FROM ".$table2." WHERE userId = '".$uid."'";
                    echo __LINE__." ".$sql."<br>";
                    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                    
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
                        ${$row['setting']} = $row['data'];
                    }
                    
					header('Location: '.$url_sync_def);
				}
				else if (password_verify("'".$_POST['password']."'", $passWord))
				{
					$_SESSION['uid'] = $uid;
                    
                    $sql = "SELECT CAST(AES_DECRYPT(setting, SHA2('".$phrase."',512)) AS CHAR) AS setting, CAST(AES_DECRYPT(data, SHA2('".$phrase."',512)) AS CHAR) AS data FROM ".$table2." WHERE userId = '".$uid."'";
                    echo __LINE__." ".$sql."<br>";
                    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                    
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
                        ${$row['setting']} = $row['data'];
                    }
                    
					header('Location: '.$url_sync_def);
				}
			}
		}	
	}

	function displayContentLogin()
	{
		$replaceTable = getReplaceTable();

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
		$displayContentLogin = $langStrings['displayContentLogin'];

		$displayContentLogin_array = getLangstringsArray('displayContentLogin_array', $displayLang);
        
        
		echo "<div class=\"page-header clear-filter\" filter-color=\"orange\">";
			echo "<div class=\"page-header-image\"></div>";
				echo "<div class=\"content\">";
					echo "<div class=\"container\">";
						echo "<div class=\"col-md-12 ml-auto mr-auto\">";
							echo "<div class=\"card card-login card-plain\">";
								echo "<form class=\"form\" method=\"post\" action=\"\">";
									echo "<div class=\"card-header text-center\">";
										echo "<div class=\"logo-container\">";
											echo "<img src=\"./img/lvteknik_300.png\" alt=\"\">";
										echo "</div>";
									echo "</div>";

									echo "<div class=\"card-body\">";
		
										if (isset($_POST['email']))
										{
											echo $displayContentLogin[1];
										}
		
										echo "<div class=\"input-group no-border input-lg\">";
											echo "<div class=\"input-group-prepend\">";
												echo "<span class=\"input-group-text\">";
													echo "<i class=\"fas fa-user\"></i></i>";
												echo "</span>";
											echo "</div>";

											echo "<input type=\"email\" class=\"form-control\"  id = \"email\" name = \"email\" placeholder=\"".$displayContentLogin[2]."\" requierd>";
										echo "</div>";

										echo "<div class=\"input-group no-border input-lg\">";
											echo "<div class=\"input-group-prepend\">";
												echo "<span class=\"input-group-text\">";
													echo "<i class=\"far fa-keyboard\"></i></i>";
												echo "</span>";
											echo "</div>";

											echo "<input type=\"password\" id = \"password\" name = \"password\" placeholder=\"".$displayContentLogin[3]."\" class=\"form-control\" requierd>";
										echo "</div>";
									echo "</div>";

									echo "<div class=\"card-footer text-center\">";
										echo "<button id =\"login\" class=\"btn btn-primary btn-round btn-lg btn-block\">".$displayContentLogin[4]."</button><br><br>";
										echo "<div class=\"pull-left\">";

										/*echo "<h6>";
											echo "<a href=\"index.php#reg\" class=\"link\">".$displayContentLogin[6]."</a>";
										echo "</h6>";*/
									echo "</div>";
									echo "<div class=\"pull-right\">";
										echo "<h6>";
											echo "<a type = \"button\" id = \"resetpassword\" class=\"link\">".$displayContentLogin[5]."</a>";
										echo "</h6>";
								echo "</div>";
							echo "</form>";
						echo "</div>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}

	if (isset($_POST['email']))
	{
        loginUser();
	}

    printHeader();

    displayContentLogin();

    displayFooter();

    
?>