<?php
    session_start();
	
	include_once("../../common/db.php");
    include_once("../../common/userData.php");
	include_once("../../common/crypto.php");
    include_once("../../common/crypto.php");

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        $replaceTable = getReplaceTable(false);
		 
        if ($replaceTable[$_POST['replaceTable']] === PREFIX.'newsletter_readers')
        {
            showAjaxAddNewsletterReader();
        }
		else
		{
			renderNewsletterReaders();
		}
	}

	function renderNewsletterReaders()
	{
		global $link;
		global $phrase;
		
		$siteSettings = getSiteSettings();

		$userSettings = getUserSettings();

		$replaceTable = getReplaceTable();

		$replaceLang = getReplaceLang(false);

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
		
		$table = "`".PREFIX."newsletter_readers`";
		$table2 = "`".PREFIX."newsletter_groups`";
		
		$langStrings = getlangstrings();
		$displayInformiloReaders = $langStrings['displayInformiloReaders'];

		$displayInformiloReaders_array = getLangstringsArray('displayInformiloReaders_array', $displayLang);

		$displayInformiloReaders_headers['status'] = $displayInformiloReaders[1];
		$displayInformiloReaders_headers['email'] = $displayInformiloReaders[2];
		$displayInformiloReaders_headers['name'] = $displayInformiloReaders[3];
		$displayInformiloReaders_headers['lang'] = $displayInformiloReaders[4];
		$displayInformiloReaders_headers['group'] = $displayInformiloReaders[5];
		$displayInformiloReaders_headers['admin'] = $displayInformiloReaders[6];
		
		unset($group);
		
		$table2 = "`".PREFIX."newsletter_groups_lang`";
	
		$sql = "SELECT * FROM (SELECT newsletterGroupId, AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as lang, AES_DECRYPT(note, SHA2('".$phrase."', 512)) as note FROM ".$table2.") as t2 GROUP BY newsletterGroupId";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$group[$row['newsletterGroupId']] = $row['note'];
		}
		
		$sql = "SELECT *, AES_DECRYPT(name, SHA2('".$phrase."', 512)) as name, AES_DECRYPT(email, SHA2('".$phrase."', 512)) as email, AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as lang FROM ".$table." ORDER BY CASE ".implode(" ", $order)." END, name, email";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		$json = "{\"data\": [";
		
		echo "<table id = \"newsltterReaders\" class = \"DataTable table table-striped table-bordered\" style=\"width:100%\">";
			echo "<thead>";
				echo "<tr>";
					foreach ($displayInformiloReaders_headers as $key => $value)
					{
						echo "<th id = \"".$key."\">".$value."</th>";
					}
				echo "</tr>";
			echo "</thead>";
		
			echo "<tbody>";
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					echo "<tr>";
						foreach ($displayInformiloReaders_headers as $key => $value)
						{
							echo "<td>";
								if ($key == "status")
								{
									echo "<div class=\"form-check checkbox-slider--b\">";
										echo "<label>";
											echo "<input class = \"syncData\" id = \"status[".$row['tableKey']."]\"data-replace_table = \"".$replaceTable[PREFIX."newsletter_readers"]."\" type=\"checkbox\"";
												if ((int)$row[$key] > 0)
												{
													echo " "."checked";
												}
											echo "><span></span>";
										echo "</label>";
									echo "</div>";
								}
								else if ($key == "group")
								{
									$temp = array_map("trim", explode(",", $row[$key]));
									
									unset($displayGroup);
									
									foreach ($temp as $key2 => $value2)
									{
										$displayGroup[] = $group[$value2];
									}
									echo implode( ", ", (array)$displayGroup);
								}
								else if ($key == "admin")
								{
									echo "<button type = \"button\" id = \"editReader[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_readers"]."\" class = \"btn btn-secondary editNewsletter\">".$displayInformiloReaders[6]."</button>";
								}
								else
								{
									echo $row[$key];
								}
							echo "</td>";
						}
					echo "</tr>";
				}
			echo "</tbody>";
		echo "</table>";
		
		/*while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$json .= "[";
			
				foreach ($displayInformiloReaders_headers as $key => $value)
				{
					$json .= "\"";
					
					if ($key == "status")
					{
						$json .= "<div class=\\\"form-check checkbox-slider--b\\\">";
							$json .= "<label>";
								$json .= "<input class = \\\"syncData\\\" id = \\\"status[".$row['tableKey']."]\\\" data-replace_table = \\\"".$replaceTable[PREFIX."newsletter_readers"]."\\\" type=\\\"checkbox\\\"";
									if ((int)$row[$key] > 0)
									{
										$json .= " "."checked";
									}
								$json .= "><span></span>";
							$json .= "</label>";
						$json .= "</div>";
					}
					else if ($key == "group")
					{
						$temp = array_map("trim", explode(",", $row[$key]));

						unset($displayGroup);

						foreach ($temp as $key2 => $value2)
						{
							$displayGroup[] = $group[$value2];
						}
						$json .= implode($displayGroup, ", ");
					}
					else if ($key == "admin")
					{
						$json .= "<button type = \\\"button\\\" id = \\\"editReader[".$row['tableKey']."]\\\" data-replace_table = \\\"".$replaceTable[PREFIX."newsletter_readers"]."\\\" class = \\\"btn btn-secondary editNewsletter\\\">".$displayInformiloReaders[6]."</button>";
					}
					else
					{
						$json .= $row[$key];
					}
					$json .= "\",";
				}
			$json .= "],";
		}
		
		$json .= "]}";
		
		$json = str_replace(",]", "]", $json);
		
		echo str_replace(",],]}", "]]}", $json);*/
	}	

	function showAjaxAddNewsletterReader()
	{
		global $link;
		global $phrase;
		
		$siteSettings = getSiteSettings();

		$userSettings = getUserSettings();

		$replaceTable = getReplaceTable();

		$replaceLang = getReplaceLang(false);

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
		
		$_POST['group'] = $_POST['group2'];
		unset($_POST['group2']);
		
		foreach ($_POST as $key => $value)
		{
			if ($key !== "replaceTable")
			{
				${mysqli_real_escape_string($link, $key)} = mysqli_real_escape_string($link, $value);
			}
		}
		
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			return false;
		}
		
		$table = "`".PREFIX."newsletter_readers`";
		$table2 = "`".PREFIX."newsletter_groups`";
		
		$sql = "SELECT * FROM ".$table2."";
		
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$groupRev[$row['tableKey']] = $row['newsletterGroupId'];
		}
		
		$temp = array_map("trim", explode(",", $group));
		
		foreach ($temp as $key => $value)
		{
			$groupInsert[] = $groupRev[$value];
		}
		
		$lang = $replaceLang[$lang];
		
		$sql = "SELECT * FROM ".$table." WHERE email = AES_ENCRYPT('".$email."', SHA2('".$phrase."', 512))";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		if (mysqli_num_rows($result) > 0)
		{
			$sql = "UPDATE ".$table." SET name =  AES_ENCRYPT('".$name."', SHA2('".$phrase."', 512)), `group` =  '".implode(", ", $groupInsert )."' WHERE email = AES_ENCRYPT('".$email."', SHA2('".$phrase."', 512))";
		}
		else
		{
			$sql = "INSERT INTO ".$table." (status, name, email, `group`, addDate, lang) VALUES ('1',AES_ENCRYPT('".$name."', SHA2('".$phrase."', 512)), AES_ENCRYPT('".$email."', SHA2('".$phrase."', 512)), '".implode(", ", (array)$groupInsert)."', NOW(), AES_ENCRYPT('".$lang."', SHA2('".$phrase."', 512)))";
		}
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		checkTable($table);
		
		$langStrings = getlangstrings();
		$displayInformiloReaders = $langStrings['displayInformiloReaders'];

		$displayInformiloReaders_array = getLangstringsArray('displayInformiloReaders_array', $displayLang);

		$displayInformiloReaders_headers['status'] = $displayInformiloReaders[1];
		$displayInformiloReaders_headers['email'] = $displayInformiloReaders[2];
		$displayInformiloReaders_headers['name'] = $displayInformiloReaders[3];
		$displayInformiloReaders_headers['lang'] = $displayInformiloReaders[4];
		$displayInformiloReaders_headers['group'] = $displayInformiloReaders[5];
		$displayInformiloReaders_headers['admin'] = $displayInformiloReaders[6];
		
		unset($group);
		
		$table2 = "`".PREFIX."newsletter_groups_lang`";
	
		$sql = "SELECT * FROM (SELECT newsletterGroupId, AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as lang, AES_DECRYPT(note, SHA2('".$phrase."', 512)) as note FROM ".$table2.") as t2 GROUP BY newsletterGroupId";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$group[$row['newsletterGroupId']] = $row['note'];
		}
		
		$sql = "SELECT *, AES_DECRYPT(name, SHA2('".$phrase."', 512)) as name, AES_DECRYPT(email, SHA2('".$phrase."', 512)) as email, AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as lang FROM ".$table." ORDER BY CASE ".implode(" ", (array)$order)." END, name, email";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		$json = "{\"data\": [";
		
		echo "<table id = \"newsltterReaders\" class = \"DataTable table table-striped table-bordered\" style=\"width:100%\">";
			echo "<thead>";
				echo "<tr>";
					foreach ($displayInformiloReaders_headers as $key => $value)
					{
						echo "<th id = \"".$key."\">".$value."</th>";
					}
				echo "</tr>";
			echo "</thead>";
		
			echo "<tbody>";
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
					echo "<tr>";
						foreach ($displayInformiloReaders_headers as $key => $value)
						{
							echo "<td>";
								if ($key == "status")
								{
									echo "<div class=\"form-check checkbox-slider--b\">";
										echo "<label>";
											echo "<input class = \"syncData\" id = \"status[".$row['tableKey']."]\"data-replace_table = \"".$replaceTable[PREFIX."newsletter_readers"]."\" type=\"checkbox\"";
												if ((int)$row[$key] > 0)
												{
													echo " "."checked";
												}
											echo "><span></span>";
										echo "</label>";
									echo "</div>";
								}
								else if ($key == "group")
								{
									$temp = array_map("trim", explode(",", $row[$key]));
									
									unset($displayGroup);
									
									foreach ($temp as $key2 => $value2)
									{
										$displayGroup[] = $group[$value2];
									}
									echo implode(", ", $displayGroup);
								}
								else if ($key == "admin")
								{
									echo "<button type = \"button\" id = \"editReader[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_readers"]."\" class = \"btn btn-secondary editNewsletter\">".$displayInformiloReaders[6]."</button>";
								}
								else
								{
									echo $row[$key];
								}
							echo "</td>";
						}
					echo "</tr>";
				}
			echo "</tbody>";
		echo "</table>";
		
		/*while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$json .= "[";
			
				foreach ($displayInformiloReaders_headers as $key => $value)
				{
					$json .= "\"";
					
					if ($key == "status")
					{
						$json .= "<div class=\\\"form-check checkbox-slider--b\\\">";
							$json .= "<label>";
								$json .= "<input class = \\\"syncData\\\" id = \\\"status[".$row['tableKey']."]\\\" data-replace_table = \\\"".$replaceTable[PREFIX."newsletter_readers"]."\\\" type=\\\"checkbox\\\"";
									if ((int)$row[$key] > 0)
									{
										$json .= " "."checked";
									}
								$json .= "><span></span>";
							$json .= "</label>";
						$json .= "</div>";
					}
					else if ($key == "group")
					{
						$temp = array_map("trim", explode(",", $row[$key]));

						unset($displayGroup);

						foreach ($temp as $key2 => $value2)
						{
							$displayGroup[] = $group[$value2];
						}
						$json .= implode($displayGroup, ", ");
					}
					else if ($key == "admin")
					{
						$json .= "<button type = \\\"button\\\" id = \\\"editReader[".$row['tableKey']."]\\\" data-replace_table = \\\"".$replaceTable[PREFIX."newsletter_readers"]."\\\" class = \\\"btn btn-secondary editNewsletter\\\">".$displayInformiloReaders[6]."</button>";
					}
					else
					{
						$json .= $row[$key];
					}
					$json .= "\",";
				}
			$json .= "],";
		}
		
		$json .= "]}";
		
		$json = str_replace(",]", "]", $json);
		
		echo str_replace(",],]}", "]]}", $json);*/
	}
?>