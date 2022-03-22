<?php
    session_start();
	
	include_once("../../common/db.php");
    include_once("../../common/userData.php");
	include_once("../../common/crypto.php");
    include_once("../../administrado/ext/theme/nav.php");
    include_once("../../common/crypto.php");

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        $replaceTable = getReplaceTable(false);
		 
        if ($replaceTable[$_POST['replaceTable']] === PREFIX.'newsletter')
        {
            showAjaxNewsletter();
        }
		elseif ($replaceTable[$_POST['replaceTable']] === PREFIX.'newsletter_groups')
		{
			ajaxDisplayNewsletterGroups();
		}
		elseif ($replaceTable[$_POST['replaceTable']] === PREFIX.'newsletter_reminder')
		{
			ajaxDisplayNewsletterReminder();
		}	
		elseif ($replaceTable[$_POST['replaceTable']] === PREFIX.'newsletter_readers')
		{
			ajaxDisplayEditReader();
		}
    }

	function queNewsletter()
	{
		global $link;
    
		global $phrase;
		
		$table = "`".PREFIX."newsletter`";
		
		$sql = "UPDATE ".$table." SET status = 1 WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['sendNewsletter'])."'";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	}

	function showAjaxNewsletter()
	{
		if (isset($_POST['sendNewsletter']))
		{
			queNewsletter();
			$_POST['id'] = $_POST['sendNewsletter'];
		}
		
		global $link;
    
		global $phrase;

		$siteSettings = getSiteSettings();

		$userSettings = getUserSettings();
		
		$replaceLang = getReplaceLang();

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
		$showAjaxNewsletter = $langStrings['showAjaxNewsletter'];

		$showAjaxNewsletter_array = getLangstringsArray('showAjaxNewsletter_array', $displayLang);
		
		$replaceTable = getReplaceTable();
    
		$table = "`".PREFIX."newsletter`";
		$table2 = "`".PREFIX."newsletter_lang`";
		checkTable($table2);
		unset($data);

		$sql = "SELECT *, newsletter.tableKey as tableKey FROM (SELECT *, YEAR(date) as year2 FROM $table) as newsletter LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT newsletterId, 
			  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
			  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
			  GROUP BY newsletterId) AS newsletter_lang ON newsletter.newsletterId = newsletter_lang.newsletterId WHERE newsletter.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."' ";
		//echo __LINE__." ".$sql."<br>";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$newsletter = $row;
		}
		
		$path = "../../newsletter/".substr($newsletter['tableKey'],0,1)."/".substr($newsletter['tableKey'],0,2)."/".$newsletter['tableKey']."/image/";
    
		if (!file_exists($path))
		{
			mkdir ($path."/source/", "0777" , true); 
			mkdir ($path."/thumbs/", "0777" , true); 
		}
    
		$path = "/newsletter/".substr($newsletter['tableKey'],0,1)."/".substr($newsletter['tableKey'],0,2)."/".$newsletter['tableKey']."/image/";

		$_SESSION['folderUrl'] = $path;
		session_write_close();
		
		$selectNewsletterGroup = array_flip(array_map("trim", explode(",", $newsletter['groups'])));
		
		$table = "`".PREFIX."newsletter_groups`";
		$table2 = "`".PREFIX."newsletter_groups_lang`";
		checkTable($table2);
		unset($data);
		
		$sql = "SELECT * FROM (SELECT * FROM $table) as newsletterGroup LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT newsletterGroupId, 
			  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
			  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note,
			  CAST(AES_DECRYPT(comment, SHA2('".$phrase."', 512)) AS CHAR) as comment
			  FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
			  GROUP BY newsletterGroupId) AS newsletterGroup_lang ON newsletterGroup.newsletterGroupId = newsletterGroup_lang.newsletterGroupId ORDER BY newsletterGroup.lft";
		//echo __LINE__." ".$sql."<br>";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$newsletterGroups[] = $row;
		}
		
		if (empty($newsletter['sent']))
		{
			if ((int)$newsletter['status'] > 0)
			{
				echo "<h3 style = \"color : red;\">".$showAjaxNewsletter[3]."</h3>";
			}
			
			echo "<div class=\"form-group row\">";
				echo "<label for=\"group[".$newsletter['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$showAjaxNewsletter[1]."</label>";
				echo "<div class=\"col-sm-10\">";
					echo "<select class = \"selectpicker2 show-tick\" id = \"groups[".$newsletter['tableKey']."]\" data-live-search = \"true\" data-replace_table = \"".$replaceTable[PREFIX."newsletter"]."\" multiple>";
						foreach ($newsletterGroups as $key => $row)
						{
							echo "<option value = \"".$row['tableKey']."\"";
							if (array_key_exists($row['newsletterGroupId'], (array)$selectNewsletterGroup))
							{
								echo " "."selected";
							}
							echo ">".$row['note']."</option>";
						}
					echo "</select>";
				echo "</div>";
			echo "</div>";
			echo "<div class=\"form-group row\">";
				echo "<label for=\"sendNewsletter\" class=\"col-sm-2 col-form-label\">".".$showAjaxNewsletter[3]."."</label>";
				echo "<div class=\"col-sm-10\">";
					echo "<textarea id = \"note[".mysqli_real_escape_string($link, $_POST['id'])."_".$replaceLang[reset($displayLang)]."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter"]."\" class = \"tinyMceArea\">";

					if (empty($newsletter['lang']))
					{
						echo $newsletter['note'];
					}
					else if ($newsletter['lang'] !== reset($order) && !empty($newsletter['note']))
					{
						echo $newsletter['note'];
					}
					else
					{
						echo $newsletter['note'];
					}
					echo "</textarea>";
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"form-group row\">";
				echo "<label for=\"sendNewsletter\" class=\"col-sm-2 col-form-label\">".$showAjaxNewsletter[4]."</label>";
				echo "<div class=\"col-sm-10\">";
					if (!empty($newsletter['date']))
					{
						$temp = explode(" ",$newsletter['date']);
						
						echo "<input type=\"date\" class = \"syncData\" id=\"date[".$newsletter['tableKey']."]\" name=\"date\" data-replace_table = \"".$replaceTable[PREFIX."newsletter"]."\" value=\"".$temp[0]."\" min=\"".date("Y-m-d")."\">&nbsp;";
						echo "<input type=\"time\" class = \"syncData\" id=\"time[".$newsletter['tableKey']."]\" name=\"time\" data-replace_table = \"".$replaceTable[PREFIX."newsletter"]."\" value=\"".$temp[1]."\">";
					}
					else
					{
						echo "<input type=\"date\" class = \"syncData\" id=\"date[".$newsletter['tableKey']."]\" name=\"date\" data-replace_table = \"".$replaceTable[PREFIX."newsletter"]."\" value=\"".date("Y-m-d", strtotime("next monday"))."\" min=\"".date("Y-m-d")."\">&nbsp;";
						echo "<input type=\"time\" class = \"syncData\" id=\"time[".$newsletter['tableKey']."]\" name=\"time\" data-replace_table = \"".$replaceTable[PREFIX."newsletter"]."\" value=\"09:00\">";	
					}
					
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"form-group row\">";
				echo "<label for=\"sendNewsletter\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";
				echo "<div class=\"col-sm-10\">";
					echo "<button type = \"button\" class = \"btn btn-secondary btn-block sendNewsletter\" id = \"sendNewsletter[".$newsletter['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter"]."\">".$showAjaxNewsletter[2]."</button>";
				echo "</div>";
			echo "</div>";
		}
		else
		{
			echo $newsletter['note'];
		}
	}

	function ajaxDisplayNewsletterGroups()
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
		$ajaxDisplayNewsletterGroups = $langStrings['ajaxDisplayNewsletterGroups'];

		$ajaxDisplayNewsletterGroups_array = getLangstringsArray('ajaxDisplayNewsletterGroups_array', $displayLang);

		$replaceTable = getReplaceTable();
		
		$replaceLang = getReplaceLang();

		$table = "`".PREFIX."newsletter_groups`";
		$table2 = "`".PREFIX."newsletter_groups_lang`";
		checkTable($table2);
		unset($data);

		$sql = "SELECT * FROM (SELECT * FROM $table) as newsletterGroup LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT newsletterGroupId, 
			  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
			  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note,
			  CAST(AES_DECRYPT(comment, SHA2('".$phrase."', 512)) AS CHAR) as comment
			  FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
			  GROUP BY newsletterGroupId) AS newsletterGroup_lang ON newsletterGroup.newsletterGroupId = newsletterGroup_lang.newsletterGroupId WHERE newsletterGroup.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
		//echo __LINE__." ".$sql."<br>";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			echo "<div class=\"form-group row\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$ajaxDisplayNewsletterGroups[1]."</label>";
				echo "<div class=\"col-sm-10\">";
					echo "<input type=\"text\" class=\"form-control syncData\" id=\"note[".$row['tableKey']."_".$replaceLang[reset($displayLang)]."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_groups"]."\" value = \"".$row['note']."\">";
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"form-group row\">";
				echo "<label for=\"comment[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$ajaxDisplayNewsletterGroups[2]."</label>";
				echo "<div class=\"col-sm-10\">";
					echo "<textarea class=\"tinyMceArea\" id=\"comment[".$row['tableKey']."_".$replaceLang[reset($displayLang)]."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_groups"]."\">";
					echo $row['comment'];
					echo "</textarea>";
				echo "</div>";
			echo "</div>";
		}
	}

	function ajaxDisplayNewsletterReminder()
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
		$ajaxDisplayNewsletterReminder = $langStrings['ajaxDisplayNewsletterReminder'];

		$ajaxDisplayNewsletterReminder_array = getLangstringsArray('ajaxDisplayNewsletterGroups_array', $displayLang);

		$replaceTable = getReplaceTable();

		$replaceLang = getReplaceLang();
		
		$table = "`".PREFIX."newsletter_reminder`";
		$table2 = "`".PREFIX."newsletter_reminder_lang`";
		checkTable($table);

		$sql = "SELECT *, CAST(AES_DECRYPT(setting, SHA2('".$phrase."', 512)) AS CHAR) as setting, CAST(AES_DECRYPT(data, SHA2('".$phrase."', 512)) AS CHAR) as data FROM $table as newsletterSetting 
			  WHERE newsletterSetting.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
		//echo __LINE__." ".$sql."<br>";
		
		$sql = "SELECT * FROM (SELECT * FROM $table) as newsletter_reminder LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT newsletterReminderId, 
			  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
			  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note,
			  CAST(AES_DECRYPT(header, SHA2('".$phrase."', 512)) AS CHAR) as header
			  FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
			  GROUP BY newsletterReminderId) AS newsletter_reminder_lang ON newsletter_reminder.newsletterReminderId = newsletter_reminder_lang.newsletterReminderId WHERE newsletter_reminder.tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
		//echo __LINE__." ".$sql."<br>";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			
			echo "<div class=\"form-group row\">";
				echo "<label for=\"date[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$ajaxDisplayNewsletterReminder[1]."</label>";
				echo "<div class=\"col-sm-10\">";
					echo "<input type=\"text\" class=\"form-control syncData\" id=\"date[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_reminder"]."\" value = \"".$row['date']."\">";
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"form-group row\">";
				echo "<label for=\"active[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$ajaxDisplayNewsletterReminder[2]."</label>";
				echo "<div class=\"col-sm-10\">";
					echo "<div class=\"form-check checkbox-slider--b\">";
						echo "<label>";
							echo "<input type=\"checkbox\" class = \"syncData\" id=\"active[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_reminder"]."\"";
								if ((int)$row['active'] > 0)
								{
									echo " "."checked";
								}
							echo "><span>".$ajaxDisplayNewsletterReminder[3]."</span>";
						echo "</label>";
					echo "</div>";
					//echo "<input type=\"text\" class=\"form-control\" id=\"active[".$row['tableKey']."\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_reminder"]."\" value = \"".$row['active']."\">";
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"form-group row\">";
				echo "<label for=\"note[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$ajaxDisplayNewsletterReminder[4]."</label>";
				echo "<div class=\"col-sm-10\">";
					echo "<input type=\"text\" class=\"form-control\" id=\"header[".$row['tableKey']."_".$replaceLang[reset($displayLang)]."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_reminder"]."\" value = \"".$row['header']."\">";
				echo "</div>";
			echo "</div>";
			
			echo "<div class=\"form-group row\">";
				echo "<label for=\"comment[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$ajaxDisplayNewsletterReminder[5]."</label>";
				echo "<div class=\"col-sm-10\">";
					echo "<textarea class=\"tinyMceArea\" id=\"note[".$row['tableKey']."_".$replaceLang[reset($displayLang)]."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_reminder"]."\">";
					echo $row['note'];
					echo "</textarea>";
				echo "</div>";
			echo "</div>";
		}
	}

	function ajaxDisplayEditReader()
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
		$ajaxDisplayEditReader = $langStrings['ajaxDisplayEditReader'];

		$ajaxDisplayEditReader_array = getLangstringsArray('ajaxDisplayNewsletterGroups_array', $displayLang);

		$replaceTable = getReplaceTable();

		$replaceLang = getReplaceLang();
		
		$table = "`".PREFIX."newsletter_readers`";
		$table2 = "`".PREFIX."newsletter_groups`";
		$table3 = "`".PREFIX."newsletter_groups_lang`";
	
		$sql = "SELECT * FROM (SELECT node.tableKey, node.newsletterGroupId, (COUNT(parent.lft) - 1) AS depth
				FROM ".$table2." AS node,
						".$table2." AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
				GROUP BY node.lft
				ORDER BY node.lft) as newsletter_groups 
				INNER JOIN (SELECT * FROM (SELECT newsletterGroupId, AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as lang, AES_DECRYPT(note, SHA2('".$phrase."', 512)) as note FROM ".$table3.") as t2 GROUP BY newsletterGroupId) newsletter_groups_lang ON newsletter_groups_lang.newsletterGroupId = newsletter_groups.newsletterGroupId";
		echo __LINE__." ".$sql."<br>";
		
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$group[$row['newsletterGroupId']] = $row['note'];
			$group_rep[$row['newsletterGroupId']] = $row['tableKey'];
		}
		
		checkTable($table);

		$sql = "SELECT *, CAST(AES_DECRYPT(name, SHA2('".$phrase."', 512)) AS CHAR) as name, CAST(AES_DECRYPT(email, SHA2('".$phrase."', 512)) AS CHAR) as email, CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang FROM $table WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['id'])."'";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		$langStrings = getlangstrings();
		$displayInformiloReaders = $langStrings['displayInformiloReaders'];

		$displayInformiloReaders_array = getLangstringsArray('displayInformiloReaders_array', $displayLang);

		$displayInformiloReaders_headers['status'] = $displayInformiloReaders[1];
		$displayInformiloReaders_headers['email'] = $displayInformiloReaders[2];
		$displayInformiloReaders_headers['name'] = $displayInformiloReaders[3];
		$displayInformiloReaders_headers['lang'] = $displayInformiloReaders[4];
		$displayInformiloReaders_headers['group'] = $displayInformiloReaders[5];
		//$displayInformiloReaders_headers['admin'] = $displayInformiloReaders[6];
	
		echo "<div id = \"ajaxHeaderModal\">";
			echo $ajaxDisplayEditReader[1];
		echo "</div>";
		
		echo "<div id = \"ajaxBodyModal\">";
		
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				echo "<form>";
					foreach ($displayInformiloReaders_headers as $key => $value)
					{
						echo "<div class=\"row mb-3\">";
							echo "<label for=\"".$key[$row['tableKey']]."\" class=\"col-sm-2 col-form-label\">".$value."</label>";

							if ($key == "status")
							{
								echo "<div class=\"form-check checkbox-slider--b\">";
									echo "<label>";
										echo "<input class = \"syncData\" id = \"status[".$row['tableKey']."]\"data-replace_table = \"".$replaceTable[PREFIX."newsletter_readers"]."\" type=\"checkbox\"";
											if ((int)$row[$key] > 0)
											{
												echo " "."checked";
											}
										if ((int)$row[$key] < 0)
										{
											echo "><span></span>"." ".$ajaxDisplayEditReader[3];
										}
										else
										{
											echo "><span></span>";
										}
										
									echo "</label>";
								echo "</div>";
							}
							else if ($key == "lang")
							{
								$displayLang2 = array_map("trim", explode(",", $siteSettings['language']));
								
								if (count($displayLang2) > 1)
								{
									echo "Vi ska presentera lite olika språk, gnäll på utvecklaren!<br><br>";
									echo __FILE__." ".__LINE__."<br>";
								}
								else
								{
									echo "Svenska";
								}
							}
							else if ($key == "group")
							{
								$temp = array_map("trim", explode(",", $row[$key]));
									
								unset($displayGroup);

								foreach ($temp as $key2 => $value2)
								{
									$displayGroup[$value2] = $group[$value2];
								}
								
								echo "<select class = \"selectpicker2 show-tick\" multiple id = \"".$key."[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_readers"]."\">";
									foreach ($group as $key2 => $value2)
									{
										echo "<option value = \"".$group_rep[$key2]."\"";
											if (array_key_exists($key2, $displayGroup))
											{
												echo " "."selected";
											}
										echo ">".$value2."</option>";
									}
								echo "</select>";
							}
							else
							{
								echo "<div class=\"col-sm-10\">";
									echo "<input type=\"text\" class=\"syncData\" id=\"".$key."[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_readers"]."\" value = \"".$row[$key]."\">";
								echo "</div>";
							}
							
						echo "</div>";
					}
				echo "</form>";
			}
		echo "</div>";
		
		echo "<div id = \"ajaxFooterModal\">";
			echo "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">".$ajaxDisplayEditReader[2]."</button>";
		echo "</div>";
	}
?>