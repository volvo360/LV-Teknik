<?php
	session_start();

	include_once("../common/db.php");
	
	include_once("../administrado/ext/theme/nav.php");

	include_once("../common/crypto.php");
	include_once("../common/userData.php");
	include_once("../common/modal.php");

	if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME']))
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
		$editProfile = $langStrings['editProfile'];

		$editProfile_array = getLangstringsArray('editProfile_array', $displayLang);
        
		/*$editProfile[1] = "Editera profil";
		$editProfile[2] = "Klar";*/
		
		
		echo "<div id = \"ajaxHeaderModal\">";
			echo "<h5 class=\"modal-title h4\" id=\"modalXlLabel\">".$editProfile[1]."</h5>";
		echo "</div>";
		
		echo "<div id = \"ajaxBodyModal\">";
			showProfile($_POST['tableKey']);
		echo "</div>";
		
		echo "<div id = \"ajaxFooterModal\">";
			echo "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">".$editProfile[2]."</button>";
		echo "</div>";
	}


	function showProfile($tableKey)
	{
		global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;

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
		$showProfile = $langStrings['showProfile'];

		$showProfile_array = getLangstringsArray('showProfile_array', $displayLang);
		
        $table = "`".PREFIX."users`";
        $table10 = "`".PREFIX."servotablo_permission`";
        $table11 = "`".PREFIX."servotablo_permission_lang`";
        $table12 = "`".PREFIX."servotablo_permission2user`";
        
        $showProfileHeader['firstName'] = $showProfile[1];
		$showProfileHeader['sureName'] = $showProfile[2];
		$showProfileHeader['email'] = $showProfile[3];
		$showProfileHeader['mobilePhone'] = $showProfile[5];
        /*$showProfileHeader['address'] = $showProfile[11];
        $showProfileHeader['zipcode'] = $showProfile[12];
        $showProfileHeader['city'] = $showProfile[13];*/
		$showProfileHeader['password'] = $showProfile[8];
		$showProfileHeader['repPassword'] = $showProfile[9];
		
		/*$showProfileHeader['country'] = $showProfile[4];*/
        
        /*$sql = "SELECT * FROM ".$table." WHERE autoId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		if (mysqli_num_rows($result) > 0)
		{
            $showProfileHeader['coworker'] = $showProfile[8];
        }*/
		
		$replaceTable = getReplaceTable();
		
		$countryes = getCountryes();
		
		$sql = "SELECT *, ";
				foreach ($showProfileHeader as $key => $value)
				{
					if ($key === "password" || $key === "repPassword")
					{
						continue;
					}
					$field[] = "CAST(AES_DECRYPT(".$key.", SHA2('".$phrase."', 512)) AS CHAR) as ".$key;
				}
				$sql .= implode(", ", (array)$field)." ";
			$sql .= "FROM ".$table ." WHERE tableKey = '".mysqli_real_escape_string($link, $tableKey)."'"; 
		//echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		if (mysqli_num_rows($result) > 0)
		{
			$first = true;
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$even = true;
				foreach ($showProfileHeader as $key => $value)
				{
					if ($first && $even)
					{
                        $enven = false;
						 $first = false;
                    }
					
					if ($even)
					{
						$even = false;
						$odd = true;
						//$first = true;
						echo "<div class = \"row\">";
					}
					else
					{
						$even = true;
						$odd = false;
					}
                    
					echo "<div class=\"col-md-6 pl-1\">";
						echo "<div class=\"form-group\">";
						echo "<label for = \"".$key."[".$row['tableKey']."]\">".$value."</label>";
							if ($key === "password" )
							{
								echo "<input id = \"password[".$row['tableKey']."]\" type=\"password\" class=\"form-control\" placeholder=\"".$value."\" data-replace_table = \"".$replaceTable[PREFIX."user"]."\">";
							}
                            else if ($key === "repPassword")
							{
								echo "<input id = \"repPassword[".$row['tableKey']."]\" type=\"password\" class=\"form-control\" placeholder=\"".$value."\" data-replace_table = \"".$replaceTable[PREFIX."user"]."\">";
							}
							else if ($key == "country")
							{
								echo "<select class = \"form-control selectpicker2 show-tick\" id =\"".$key."[".$row['tableKey']."]\" data-size = \"5\" data-live-search = \"true\" data-replace_table = \"".$replaceTable[PREFIX."user"]."\" data-update_div = \"v-pills-coworkers\">";
									foreach ($countryes as $country_key => $country_value)
									{
										echo "<option value = \"".$country_key."\"";
											if ($country_key === $row['country'])
											{
												echo " "."selected";
											}
										echo ">";
											echo $country_value;
										echo "</option>";
									}
								echo "</select>";
							}
                            else if ($key == "coworker")
                            {
                                /*$sql = "SELECT * FROM ".$table12." WHERE userId = '".$row['autoId']."'";
                                $result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                                
                                while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
                                {
                                    $prermissionId = $row2['permissionId'];
                                }
                                
                                $sql = "SELECT * FROM (SELECT node.*, (COUNT(parent.permissionId) - 1) AS depth
                                          FROM $table10 AS node,
                                                  $table10 AS parent
                                          WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
                                          ORDER BY node.lft LIMIT 18446744073709551615) as menu INNER JOIN 
                                          (SELECT * FROM (SELECT * FROM (SELECT permissionId, 
                                                  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
                                                  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table11) as q 
                                                  ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
                                                  GROUP BY permissionId) AS lang ON menu.permissionId = lang.permissionId ORDER BY menu.lft";
                                $result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

                                if (mysqli_num_rows($result2) > 0)
                                {
                                    echo "<select class = \"form-control selectpicker2 show-tick\" id =\"permissionId[".$row['tableKey']."]\" data-size = \"5\" data-live-search = \"true\" data-replace_table = \"".$replaceTable[PREFIX."servotablo_permission2user"]."\" data-update_div = \"v-pills-coworkers\">";
                                        
                                    echo "<option value = \"-1\">".$row2['note']."</option>";
                                    
                                    while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
                                    {
                                        echo "<option value = \"".$row2['tableKey']."\"";
                                        
                                            if ((int)$prermissionId == (int)$row2['permissionId'])
                                            {
                                                echo " "."selected = true";
                                            }
                                        
                                        echo ">".$row2['note']."</option>";
                                    }
                                    echo "</select>";
                                }*/  
                            }
							else
							{
								echo "<input id =\"".$key."[".$row['tableKey']."]\" type=\"text\" class=\"form-control syncData\" placeholder=\"".$value."\" value=\"".$row[$key]."\" data-replace_table = \"".$replaceTable[PREFIX."user"]."\" data-update_div = \"v-pills-coworkers\">";
							}
								
						echo "</div>";
					echo "</div>";
					if (!$odd)
					{
						echo "</div>";
					}
				}
				if (!$first)
				{
					echo "</div>";
				}
			}
		}
	}

?>