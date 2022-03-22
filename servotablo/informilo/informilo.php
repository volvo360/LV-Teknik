<?php
include_once("../../common/db.php");
include_once("../../common/crypto.php");
include_once("../../common/userData.php");
include_once("../../administrado/ext/theme/nav.php");

include_once("../../common/modal.php");

function getNewsletterGroups()
{
	global $link;
    
    global $phrase;
	
	$siteSettings = getSiteSettings();

    $userSettings = getUserSettings();
	
	$replaceTable = getReplaceTable();

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
    $displayInformiloReaders = $langStrings['displayInformiloReaders'];

    $displayInformiloReaders_array = getLangstringsArray('displayInformiloReaders_array', $displayLang);
	
	$table = "`".PREFIX."newsletter_groups`";
	$table2 = "`".PREFIX."newsletter_groups_lang`";
	
	$sql = "SELECT * FROM (SELECT node.*, (COUNT(parent.lft) - 1) AS depth
	FROM ".$table." AS node,
			".$table." AS parent
	WHERE node.lft BETWEEN parent.lft AND parent.rgt
	GROUP BY node.lft
	ORDER BY node.lft) as newsletter_groups INNER JOIN (SELECT * FROM (SELECT newsletterGroupId, AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as lang, AES_DECRYPT(note, SHA2('".$phrase."', 512)) as note FROM ".$table2." ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t2 GROUP BY newsletterGroupId) as newsletter_groups_lang ON newsletter_groups.newsletterGroupId = newsletter_groups_lang.newsletterGroupId";
	
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$group[$row['tableKey']] = $row['note'];
	}
	
	return $group;
}

function displayInformiloReaders()
{
    global $link;
    
    global $phrase;
    
    $siteSettings = getSiteSettings();

    $userSettings = getUserSettings();
	
	$replaceTable = getReplaceTable();
	
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
    $displayInformiloReaders = $langStrings['displayInformiloReaders'];

    $displayInformiloReaders_array = getLangstringsArray('displayInformiloReaders_array', $displayLang);
	
	$displayInformiloReaders_headers['status'] = $displayInformiloReaders[1];
	$displayInformiloReaders_headers['email'] = $displayInformiloReaders[2];
	$displayInformiloReaders_headers['name'] = $displayInformiloReaders[3];
	$displayInformiloReaders_headers['lang'] = $displayInformiloReaders[4];
	$displayInformiloReaders_headers['group'] = $displayInformiloReaders[5];
	$displayInformiloReaders_headers['admin'] = $displayInformiloReaders[6];
	
	$table = "`".PREFIX."newsletter_readers`";
	$table2 = "`".PREFIX."newsletter_groups_lang`";
	
	$sql = "SELECT * FROM (SELECT newsletterGroupId, AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as lang, AES_DECRYPT(note, SHA2('".$phrase."', 512)) as note FROM ".$table2.") as t2 GROUP BY newsletterGroupId";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$group[$row['newsletterGroupId']] = $row['note'];
	}
	
	$sql = "SELECT *, AES_DECRYPT(name, SHA2('".$phrase."', 512)) as name, AES_DECRYPT(email, SHA2('".$phrase."', 512)) as email, AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as lang FROM ".$table." ORDER BY CASE ".implode(" ", (array)$order)." END, name, email";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	if (mysqli_num_rows($result) > 0)
	{
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
	}
	
	echo "<form id = \"addReaderNewsletterForm\">";
		echo "<div class=\"form-group row\">";
			echo "<label for=\"name\" class=\"col-sm-2 col-form-label\">".$displayInformiloReaders[3]."</label>";
			
			echo "<div class=\"col-sm-5\">";
				echo "<input type=\"text\" class=\"form-control\" id=\"name\" required >";
			echo "</div>";
		echo "</div>";
	
		echo "<div class=\"form-group row\">";
			echo "<label for=\"email\" class=\"col-sm-2 col-form-label\">".$displayInformiloReaders[2]."</label>";
			
			echo "<div class=\"col-sm-5\">";
				echo "<input type=\"text\" class=\"form-control\" id=\"email\" required>";
			echo "</div>";
		echo "</div>";
	
		echo "<div class=\"form-group row\">";
			echo "<label for=\"group\" class=\"col-sm-2 col-form-label\">".$displayInformiloReaders[5]."</label>";
			
			echo "<div class=\"col-sm-5\">";
				$temp = getNewsletterGroups();
	
				echo "<select class=\"selectpicker2 form-control\" id=\"group2\" multiple required>";
					foreach ($temp as $key => $value)
					{
						echo "<option value = \"".$key."\">".$value."</option>";
					}
				echo "</select>";
			echo "</div>";
		echo "</div>";
	
		echo "<div class=\"form-group row\">";
			echo "<label for=\"none\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";
			
			echo "<div class=\"col-sm-5\">";
				echo "<button class = \"btn btn-secondary btn-block\" id = \"addReaderNewsletter\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_readers"]."\">".$displayInformiloReaders[7]."</button>";
			echo "</div>";
		echo "</div>";
	
		echo "<input type = \"hidden\" id = \"lang\" value = \"".$replaceLang[reset($displayLang)]."\">";
	echo "</form>";
}

function displayInformiloGroups()
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
    $displayInformiloGroups = $langStrings['displayInformiloGroups'];

    $displayInformiloGroups_array = getLangstringsArray('displayInformiloGroups_array', $displayLang);
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."newsletter_groups`";
    $table2 = "`".PREFIX."newsletter_groups_lang`";
	checkTable($table2);
    unset($data);

    $sql = "SELECT * FROM (SELECT * FROM $table) as newsletterGroup LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT newsletterGroupId, 
          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
          GROUP BY newsletterGroupId) AS newsletterGroup_lang ON newsletterGroup.newsletterGroupId = newsletterGroup_lang.newsletterGroupId ORDER BY newsletterGroup.lft";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
	$oldYear = null;
	$first = true;
	
     echo "<div class = \"row\" style = \"max-height : 58vh; height : 58vh; overflow : auto\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."newsletter_groups"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."newsletter_groups"]."\" data-replace_table = \"".$replaceTable[PREFIX.'newsletter_groups']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."newsletter_groups"]."-data\" style = \"display:none;\" >";
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
                        $rowData[] = $row;

                        if ($oldDepth > (int)(int)$row['depth'])
                        {
                            for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                            {
                                echo "</ul></li>";
                            }
                        }
						
                        if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
                        {
                            echo "<li class = \"folder expanded\" id = \"".$row['tableKey']."\"";

                                if (array_key_exists($row['tableKey'], (array)$display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".$row['note']."<ul>";
                        }
                        else
                        {
                            echo "<li id = \"".$row['tableKey']."\"";

                                if (array_key_exists($row['tableKey'], (array)$display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".$row['note']."</li>";
                        }

                        $oldDepth = (int)$row['depth'];
                    }

                    if ($oldDepth > 0)
                    {
                        for ($i = 0; $i < ($oldDepth); $i++)
                        {
                            echo "</ul></li>";
                        }
                    }
	
					if (!$first)
					{
                    	echo "</ul></li>";
                    }
                echo "</ul>";
            echo "</div><br>";

            echo "<form id = \"addForm_".$replaceTable[PREFIX."newsletter_groups"]."\">";
                echo $displayInformiloGroups[1]."<br>";
				echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";

                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."newsletter_groups"]."\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_groups"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$displayInformiloGroups[2]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."newsletter_groups"]."\">";
               // renderManualEdit($lang, $rowData);
            echo "</div>";
        echo "</div>";

     //echo "</div>";
	echo "</div>";
}

function displayInformiloSettings()
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
    $displayInformiloSettings = $langStrings['displayInformiloSettings'];

    $displayInformiloSettings_array = getLangstringsArray('displayInformiloSettings_array', $displayLang);
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."newsletter_reminder`";
	$table2 = "`".PREFIX."newsletter_reminder_lang`";
    checkTable($table);
    
    $sql = "SELECT *, CAST(AES_DECRYPT(setting, SHA2('".$phrase."', 512)) AS CHAR) as setting, 
          CAST(AES_DECRYPT(data, SHA2('".$phrase."', 512)) AS CHAR) as data FROM $table as newsletter_settings ORDER BY newsletter_settings.lft";
    //echo __LINE__." ".$sql."<br>";
	$sql = "SELECT * FROM (SELECT * FROM $table) as newsletter_reminder LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT newsletterReminderId, 
			  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
			  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note,
			  CAST(AES_DECRYPT(header, SHA2('".$phrase."', 512)) AS CHAR) as header
			  FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
			  GROUP BY newsletterReminderId) AS newsletter_reminder_lang ON newsletter_reminder.newsletterReminderId = newsletter_reminder_lang.newsletterReminderId ORDER BY newsletter_reminder.lft";
	
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
	$oldYear = null;
	$first = true;
	
     echo "<div class = \"row\" style = \"max-height : 58vh; height : 58vh; overflow : auto\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."newsletter_reminder"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."newsletter_reminder"]."\" data-replace_table = \"".$replaceTable[PREFIX.'newsletter_reminder']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."newsletter_reminder"]."-data\" style = \"display:none;\" >";
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
                        $rowData[] = $row;

                        if ($oldDepth > (int)(int)$row['depth'])
                        {
                            for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                            {
                                echo "</ul></li>";
                            }
                        }
						
                        if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
                        {
                            echo "<li class = \"folder expanded\" id = \"".$row['tableKey']."\"";

                                if (array_key_exists($row['tableKey'], (array)$display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".$row['header']."<ul>";
                        }
                        else
                        {
                            echo "<li id = \"".$row['tableKey']."\"";

                                if (array_key_exists($row['tableKey'], (array)$display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".$row['header']."</li>";
                        }

                        $oldDepth = (int)$row['depth'];
                    }

                    if ($oldDepth > 0)
                    {
                        for ($i = 0; $i < ($oldDepth); $i++)
                        {
                            echo "</ul></li>";
                        }
                    }
	
					if (!$first)
					{
                    	echo "</ul></li>";
                    }
                echo "</ul>";
            echo "</div><br>";

            echo "<form id = \"addForm_".$replaceTable[PREFIX."newsletter_reminder"]."\">";
                echo $displayInformiloSettings[1]."<br>";
				echo "<input type = \"text\" id = \"header\" class = \"form-control\"><br>";

                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."newsletter_reminder"]."\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_reminder"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$displayInformiloSettings[2]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."newsletter_reminder"]."\">";
               // renderManualEdit($lang, $rowData);
            echo "</div>";
        echo "</div>";

     //echo "</div>";
	echo "</div>";
}

function displayInformilo2()
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
    $displayInformilo2 = $langStrings['displayInformilo2'];

    $displayInformilo2_array = getLangstringsArray('displayInformilo2_array', $displayLang);
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."newsletter`";
    $table2 = "`".PREFIX."newsletter_lang`";
	checkTable($table2);
    unset($data);

    $sql = "SELECT * FROM (SELECT *, YEAR(date) as year2 FROM $table) as newsletter LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM(SELECT newsletterId, 
          CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
          CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
          GROUP BY newsletterId) AS newsletter_lang ON newsletter.newsletterId = newsletter_lang.newsletterId ORDER BY newsletter.year2, newsletter.date";
    //echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
	$oldYear = null;
	$first = true;
	
     echo "<div class = \"row\" style = \"max-height : 58vh; height : 58vh; overflow : auto\">";
        echo "<div class = \"col-md-3\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id = \"tree_".$replaceTable[PREFIX."newsletter"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajax_".$replaceTable[PREFIX."newsletter"]."\" data-replace_table = \"".$replaceTable[PREFIX.'newsletter']."\">";
                echo "<ul id = \"tree_".$replaceTable[PREFIX."newsletter"]."-data\" style = \"display:none;\" >";
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
                        $rowData[] = $row;

                        if ($oldDepth > (int)(int)$row['depth'])
                        {
                            for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                            {
                                echo "</ul></li>";
                            }
                        }
						
						if ($row['year2'] !== $oldYear)
						{
							if (!$first)
							{
								echo "</ul></li>";
							}
							else
							{
								$first = false;
							}
							echo "<li class = \"folder expanded\" id = \"".$row['tableKey']."\"";

                                if (array_key_exists($row['tableKey'], (array)$display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".$row['year2']."<ul>";
							
						}

                        if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
                        {
                            echo "<li class = \"folder expanded\" id = \"".$row['tableKey']."\"";

                                if (array_key_exists($row['tableKey'], (array)$display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".date("Y-m-d", strtotime($row['date']))."<ul>";
                        }
                        else
                        {
                            echo "<li id = \"".$row['tableKey']."\"";

                                if (array_key_exists($row['tableKey'], (array)$display))
                                {
                                    echo " "."data-selected = true";
                                }

                            echo ">".date("Y-m-d", strtotime($row['date']))."</li>";
                        }

                        $oldDepth = (int)$row['depth'];
                    }

                    if ($oldDepth > 0)
                    {
                        for ($i = 0; $i < ($oldDepth); $i++)
                        {
                            echo "</ul></li>";
                        }
                    }
	
					if (!$first)
					{
                    	echo "</ul></li>";
                    }
                echo "</ul>";
            echo "</div><br>";

            echo "<form id = \"addForm_".$replaceTable[PREFIX."newsletter"]."\">";
                echo $displayInformilo2[1]."<br>";
				$monday =  date("Y-m-d",strtotime('first monday', strtotime('+1 week')));
                echo "<input type = \"date\" min = \"".date("Y-m-d")."\" id = \"date\" class = \"form-control\" value = \"".$monday."\"><br>";

                echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX."newsletter"]."\" data-replace_table = \"".$replaceTable[PREFIX."newsletter"]."\" data-replace_lang = \"".$replaceLang[$lang]."\">".$displayInformilo2[2]."</button>";
            echo "</form><br><br>";

        echo "</div>";
        echo "<div class = \"col-md-9\" style = \"max-height : 100%; overflow : auto;\">";
            echo "<div id =\"ajax_".$replaceTable[PREFIX."newsletter"]."\">";
               // renderManualEdit($lang, $rowData);
            echo "</div>";
        echo "</div>";

     //echo "</div>";
	echo "</div>";
    
}

function displayInformilo()
{
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

    foreach ($displayLang as $key => $value)
    {
        $order[] = "WHEN lang = '".$value."' THEN ".$i;
        $order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
        $i++;
    }

    $langStrings = getlangstrings();
    $displayInformilo = $langStrings['displayInformilo'];

    $displayInformilo_array = getLangstringsArray('displayInformilo_array', $displayLang);
    
    $navs['displayInformilo2'] = $displayInformilo[1];
    $navs['displayInformiloReaders'] = $displayInformilo[2];
	$navs['displayInformiloGroups'] = $displayInformilo[3];
	$navs['displayInformiloSettings'] = $displayInformilo[4];
    
    $preselect = "displayInformilo2";
	//$preselect = "displayInformiloReaders";
	
    echo "<ul class=\"nav nav-tabs\" id=\"myTab\" role=\"tablist\">";
        foreach ($navs as $key => $value)
        {
            echo "<li class=\"nav-item\" role=\"presentation\">";
                echo "<a class=\"nav-link";
                    if ($key === $preselect)
                    {
                        echo " "."active";
                    }
                echo "\" id=\"".$key."-tab\" data-toggle=\"tab\" href=\"#".$key."\" role=\"tab\" aria-controls=\"".$key."\" aria-selected=\"";
                if ($key === $preselect)
                {
                    echo "true";
                }
                else
                {
                    echo "false";
                }
            echo "\">".$value."</a>";
            echo "</li>";
        }
        
    echo "</ul>";
    
    echo "<div class=\"tab-content\" id=\"myTabContent\">";
        foreach ($navs as $key => $value)
        {
            echo "<div class=\"tab-pane fade";
                if ($key === $preselect)
                {
                    echo " "."show active";
                }
            echo "\" id=\"".$key."\" role=\"tabpanel\" aria-labelledby=\"".$key."-tab\">";
                echo "<br>";
               	call_user_func($key);
            echo "</div>";
        }
    echo "</div>";
}

function displayBodyMenuServotablo()
{
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

    foreach ($displayLang as $key => $value)
    {
        $order[] = "WHEN lang = '".$value."' THEN ".$i;
        $order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
        $i++;
    }

    $langStrings = getlangstrings();
    $informilo = $langStrings['informilo'];

    $informilo_array = getLangstringsArray('informilo_array', $displayLang);

    echo "<div class=\"panel-header panel-header-sm\">";

    echo "</div>";

    echo "<div class=\"content\" style = \"max-height : 65; height :  65vh; overflow : auto\">";
        echo "<div class=\"row\" style = \"height : 95%;\">";
            echo "<div class=\"col-md-12\" style = \"height : 99%;\">";
                echo "<div class=\"card\"style = \"height : 99%;\">";
                    echo "<div class=\"card-header\">";
                        echo "<h1>".$informilo[1]."</h1>";
                echo "</div>";
                echo "<div class=\"card-body\">";
                    echo "<div class=\"row\" style = \"max-height :  65vh; height :  65vh; overflow : auto\">";
                        echo "<div class=\"col-md-12\">";
                            displayInformilo();
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</div>";    
}


if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']))
{
    printHeader();
    displayMenuAdministradoHeader();
	displayBodyMenuServotablo();
	displayFooterAdministrado();
	print_modal_xl();
    printScripts();
}
?>