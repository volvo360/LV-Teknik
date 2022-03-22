<?php
    session_start();
    include_once("../../common/db.php");
    include_once("./../../administrado/ext/theme/nav.php");
    include_once("../../common/crypto.php");
    include_once("../../common/userData.php");

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        showAjaxCVheaders();
    }

    function showAjaxCVheaders()
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
        $editCV = $langStrings['editCV'];

        $editCV_array = getLangstringsArray('editCV_array', $displayLang);
        
        $editCV[1] = $editCV_array[1][$lang];
        
        $table30 = "`".PREFIX."user2account`"; 
        
        $table40 = "`".PREFIX."account`"; 
        
        $table50 = "`".PREFIX."default_collection`";
        $table51 = "`".PREFIX."default_collection2account_plan`";
        $table52 = "`".PREFIX."default_collection2menu`";
        
        $table60 = "`".PREFIX."administrado_menu`";
        
        $table70 = "`".PREFIX."headers`";
        $table71 = "`".PREFIX."headers2account`";
        $table72 = "`".PREFIX."headers_lang`";
        
        echo "<div id = \"ajaxHeaderModal\">";
        
        echo "</div>";
        
        echo "<div id = \"ajaxBodyModal\">";
        
            $sql = "SELECT *, t50.tableKey as tableKey FROM ".$table50." t50 INNER JOIN ".$table51." t51 ON t51.collectionId = t50.collectionId INNER JOIN ".$table40." t40 ON t40.accountPlan = t51.accountPlan INNER JOIN ".$table60." t60 ON t60.accountPermission >= t51.accountPlan INNER JOIN ".$table52." t52 ON t52.menuId = t60.menuId WHERE t40.autoId = '".mysqli_real_escape_string($link, $_SESSION['accountId'])."' AND folder = 'persone' AND t52.collectionId = t50.collectionId AND extra = 'cv' GROUP BY t51.collectionId"; 
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

            while ($row = mysqli_fetch_array($result))
            {
                $collectionId[$row['collectionId']] = "collectionId = '".$row['collectionId']."'";
            }

            $sql = "SELECT * FROM ".$table40." t40 INNER JOIN ".$table30." t30 ON t40.autoId = t30.accountId WHERE userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."' AND accountId = '".mysqli_real_escape_string($link, $_SESSION['accountId'])."'";
            echo __LINE__." ".$sql."<br>";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

            while ($row = mysqli_fetch_array($result))
            {
                $accountPlan = $row['accountPlan'];
            }

            $sql = "SELECT * FROM (SELECT node.*
                FROM (SELECT * FROM ".$table50." WHERE ".implode(" OR ", (array)$collectionId).") AS node,
                        (SELECT * FROM ".$table50." WHERE ".implode(" OR ", (array)$collectionId).") AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                        GROUP BY node.lft
                ORDER BY node.lft) as t50 INNER JOIN ".$table51." t51 ON t50.collectionId = t51.collectionId INNER JOIN ".$table70." t70 ON t50.collectionId = t70.collectionId INNER JOIN ".$table71." t71 ON t71.headerId = t70.headerId INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT headerId, CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as CHAR) as lang, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) as CHAR) as note FROM ".$table72." ) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t GROUP BY headerId) as t72 ON t70.headerId = t72.headerId WHERE t51.accountPlan = '".$accountPlan."' AND t71.accountTypeId = '".$accountPlan."'";
            echo __LINE__." ".$sql."<br>";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
            echo "<div class = \"row\">";
                echo "<div class =\"col-md-4\" style = \"overflow : auto\">";
                    echo "<div id = \"tree_".$replaceTable[PREFIX_K.'CVheaders']."\" class =\"fancyTreeSelectClass\" data-ajax_target = \"ajaxDefaultCVheaders\" data-replace_table = \"".$replaceTable[PREFIX_K.'CVheaders']."\" >";
                        echo "<ul id = \"tree_".$replaceTable[PREFIX_K.'CVheaders']."-data\" style = \"display:none;\" >";
                            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                            {
                                if ($oldDepth > (int)$row['depth'])
                                {
                                    for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                                    {
                                        echo "</ul></li>";
                                    }
                                }

                                if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
                                {
                                    echo "<li class = \"folder expanded\"";
                                        if (array_key_exists($row['headerId'], (array)$headerId))
                                        {
                                            echo " "."data-selected = true";
                                        }
                                    echo "id = \"".$row['tableKey']."\">".$row['note']."<ul>";
                                }
                                else
                                {
                                    echo "<li id = \"".$row['tableKey']."\"";
                                        if (array_key_exists($row['headerId'], (array)$headerId))
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
                        echo "</ul>";
                    echo "</div><br>";
                echo "</div>";

                echo "<div class = \"col-md-8\" id = \"ajaxDefaultHeaders\" style = \"max-height : 61; height : 61vh; overflow : auto\">";

                echo "</div>";
		  echo "</div>";
       
        echo "</div>";
        
        echo "<div id = \"ajaxFooterModal\">";
            echo "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>";
            echo "<button type=\"button\" class=\"btn btn-secondary resyncMasterHeader\" data-target_tree = \"tree_".$replaceTable[PREFIX_K.'CVheaders']."\" data-table_key = \"".$replaceTable[PREFIX_K."CVheaders"]."\" data-project_id = \"".$projectTableKey."\">Save changes</button>";
        echo "</div>";
    }

    function insertCVheaderDefault()
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
            $where[] = "lang = '".$value."'";
            $i++;
        }
		
		$langStrings = getlangstrings();
        $editCV = $langStrings['editCV'];

        $editCV_array = getLangstringsArray('editCV_array', $displayLang);
        
        $editCV[1] = $editCV_array[1][$lang];
        
        $table30 = "`".PREFIX."user2account`"; 
        
        $table40 = "`".PREFIX."account`"; 
        
        $table50 = "`".PREFIX."default_collection`";
        $table51 = "`".PREFIX."default_collection2account_plan`";
        $table52 = "`".PREFIX."default_collection2menu`";
        
        $table60 = "`".PREFIX."administrado_menu`";
        
        $table70 = "`".PREFIX."headers`";
        $table71 = "`".PREFIX."headers2account`";
        $table72 = "`".PREFIX."headers_lang`";
        
        $table100 = "`".PREFIX_K."CVheaders`";
        $table101 = "`".PREFIX_K."CVheaders_lang`";
        
        $sql = "SELECT *, t50.tableKey as tableKey FROM ".$table50." t50 INNER JOIN ".$table51." t51 ON t51.collectionId = t50.collectionId INNER JOIN ".$table40." t40 ON t40.accountPlan = t51.accountPlan INNER JOIN ".$table60." t60 ON t60.accountPermission >= t51.accountPlan INNER JOIN ".$table52." t52 ON t52.menuId = t60.menuId WHERE t40.autoId = '".mysqli_real_escape_string($link, $_SESSION['accountId'])."' AND folder = 'persone' AND t52.collectionId = t50.collectionId AND extra = 'cv' GROUP BY t51.collectionId"; 
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result))
        {
            $collectionId[$row['collectionId']] = "collectionId = '".$row['collectionId']."'";
        }

        $sql = "SELECT * FROM ".$table40." t40 INNER JOIN ".$table30." t30 ON t40.autoId = t30.accountId WHERE userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."' AND accountId = '".mysqli_real_escape_string($link, $_SESSION['accountId'])."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result))
        {
            $accountPlan = $row['accountPlan'];
        }

        $sql = "SELECT * FROM (SELECT node.*
            FROM (SELECT * FROM ".$table50." WHERE ".implode(" OR ", (array)$collectionId).") AS node,
                    (SELECT * FROM ".$table50." WHERE ".implode(" OR ", (array)$collectionId).") AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
                    GROUP BY node.lft
            ORDER BY node.lft) as t50 INNER JOIN ".$table51." t51 ON t50.collectionId = t51.collectionId INNER JOIN ".$table70." t70 ON t50.collectionId = t70.collectionId INNER JOIN ".$table71." t71 ON t71.headerId = t70.headerId GROUP by t71.headerId";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $sql = "INSERT INTO ".$table100." (headerId, lft, rgt, minAccountId) VALUES ('".$row['headerId']."', '".$row['lft']."', '".$row['rgt']."', '".$row['defaultUserId']."')";
            $result4 =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
            
            $sql = "SELECT * FROM (SELECT headerId, AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as lang, AES_DECRYPT(note, SHA2('".$phrase."', 512)) as note FROM ".$table72.") as t72 WHERE (".implode( " OR ", (array)$where).") AND headerId = '".$row['headerId']."'";
            $result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
            while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
            {
                $sql = "INSERT INTO ".$table101." (headerId, lang, note) VALUES ('".$row['headerId']."', AES_ENCRYPT('".$row2['lang']."', SHA2('".$phrase_k."', 512)),  AES_ENCRYPT('".$row2['note']."', SHA2('".$phrase_k."', 512)))";
                $result3 =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
            }
        }
        
        checkTable($table100);
        checkTable($table101);
    }

    function displayWorkExperience($lang = null)
    {
        global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
        
        $replaceTable = getReplaceTable();
        
        $replaceLang = getReplaceLang();
        
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
            $where[] = "lang = '".$value."'";
            $i++;
        }
		
		$langStrings = getlangstrings();
        $displayWorkExperience = $langStrings['displayWorkExperience'];

        $displayWorkExperience_array = getLangstringsArray('displayWorkExperience_array', $displayLang);
        
        $table = "`".PREFIX_K."CVworkExperience`"; 
        $table2 = "`".PREFIX_K."CVworkExperience_lang`";
        
        checkTable($table2);
        
        $sql = "SELECT *, t1.tableKey as masterTableKey FROM (SELECT node.*, CAST(AES_DECRYPT(node.company, SHA2('".$phrase_k."', 512)) AS CHAR) as company2
            FROM ".$table." AS node,
                    ".$table."AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
                    GROUP BY node.lft
            ORDER BY node.lft) as t1 LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM (SELECT tableKey, workExperienceId, CAST(AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) AS CHAR) as lang, CAST(AES_DECRYPT(employment, SHA2('".$phrase_k."', 512)) AS CHAR) as employment, CAST(AES_DECRYPT(note, SHA2('".$phrase_k."', 512)) AS CHAR) as workNote  FROM $table2 ) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615 ) as p GROUP BY workExperienceId) as t2 ON t1.workExperienceId = t2.workExperienceId";
        //echo __LINE__." ".$sql."<br>";
        
        $result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
        
        echo "<div class = \"row\">";
			echo "<div class =\"col-md-4\" style = \"max-height : 40vh; height : 40vh; overflow : auto;\">";
				echo "<div id = \"tree_".$replaceTable[PREFIX_K.'CVworkExperience']."_".$lang."\" class =\"fancyTreeClass\" data-ajax_target = \"ajaxCVworkExperience_".$lang."\" data-replace_table = \"".$replaceTable[PREFIX_K.'CVworkExperience']."\">";
					echo "<ul id = \"tree_".$replaceTable[PREFIX_K.'CVworkExperience']."-data\" style = \"display:none;\" >";
						while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						{
                            $tempData[] = $row;
                            
							if ($oldDepth > (int)$row['depth'])
							{
								for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
								{
									echo "</ul></li>";
								}
							}

							if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
							{
								echo "<li class = \"folder expanded\" id = \"".$row['masterTableKey']."\" data-target_div=\"targetId_".$row['tableKey']."\">".$row['company2']."<ul>";
							}
							else
							{
								echo "<li id = \"".$row['masterTableKey']."\" data-target_div=\"targetId_".$row['tableKey']."\">".$row['company2']."</li>";
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
					echo "</ul>";
				echo "</div><br><br>";
		
				echo "<form id = addForm_".$replaceTable[PREFIX_K.'CVworkExperience']."_".$lang.">";
					echo $displayWorkExperience[1]."<br>";
					echo "<input type = \"text\" id = \"company\" class = \"form-control\"><br>";
					/*echo "<div class=\"form-check checkbox-slider--b\">";
						echo "<label>";
							echo "<input type=\"checkbox\" checked><span title = \"".$render_customer[4]."\">".$render_customer[3]."</span>";
						echo "</label>";
					echo "</div>";*/
					echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX_K.'CVworkExperience']."_".$lang."\" data-replace_table = \"".$replaceTable[PREFIX_K."CVworkExperience"]."\">".$displayWorkExperience[3]."</button>";
				echo "</form>";
			echo "</div>";
		
			echo "<div class = \"col-md-8\" id = \"ajaxCVworkExperience_".$lang."\" style = \"max-height : 40vh; height : 40vh; overflow : auto;\">";
            foreach ($tempData as $key => $row)
            {
                echo "<div id = \"targetId_".$row['tableKey']."\">";
                    echo "<h2 class = \"jeditable\" id = \"company[".$row['masterTableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX_K."CVworkExperience"]."\" data-reload_tree = \"tree_".$replaceTable[PREFIX_K.'CVworkExperience']."_".$lang."\" data-replace_lang = \"".$replaceLang[$lang]."\" data-replace_project = \"".$_POST['id']."\">".$row['company2']."</h2>";
                
                    echo "<div class=\"form-group row\">";
                        echo "<label for=\"employment[".$row['masterTableKey']."]\" class=\"col-sm-6 col-form-label\">".$displayWorkExperience[7]."</label>";
                        echo "<div class=\"col-sm-6\">";
                            echo "<input type=\"text\" class=\"form-control syncData\" id=\"employment[".$row['tableKey']."]\" value = \"".$row['employment']."\">";
                        echo "</div>";
                    echo "</div>";
                
                    echo "<div class=\"form-group row\">";
                        echo "<label for=\"start[".$row['masterTableKey']."]\" class=\"col-sm-6 col-form-label\">".$displayWorkExperience[5]."</label>";
                        echo "<div class=\"col-sm-6\">";
                            echo "<input type=\"text\" class=\"form-control datepicker syncData\" id=\"start[".$row['masterTableKey']."]\" value = \"".$row['start']."\" data-replace_table = \"".$replaceTable[PREFIX_K."CVworkExperience"]."\">";
                        echo "</div>";
                    echo "</div>";
                
                    echo "<div class=\"form-group row\">";
                        echo "<label for=\"end[".$row['masterTableKey']."]\" class=\"col-sm-6 col-form-label\">".$displayWorkExperience[6]."</label>";
                        echo "<div class=\"col-sm-6\">";
                            echo "<input type=\"text\" class=\"form-control datepicker syncData\" id=\"end[".$row['masterTableKey']."]\" value = \"".$row['end']."\" data-replace_table = \"".$replaceTable[PREFIX_K."CVworkExperience"]."\">";
                        echo "</div>";
                    echo "</div>";

                    echo "<textarea id = \"note[";
                    if (!empty($row['tableKey']))
                    {
                        echo $row['tableKey'];
                    }
                    else 
                    {
                        echo $row['masterTableKey']."_".$replaceLang[$lang];
                    }
                    echo "]\" class = \"tinyMceArea syncData form-control\" data-replace_table = \"".$replaceTable[PREFIX_K.'CVworkExperience']."\" placeholder = \"".$displayWorkExperience[4]."\">".$row['workNote']."</textarea>";
                echo "</div>";
            }
			echo "</div>";
        echo "</div>";
    }

    function displayEducationExperience($lang = null)
    {
        global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
        
        $replaceTable = getReplaceTable();
        
        $replaceLang = getReplaceLang();
        
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
            $where[] = "lang = '".$value."'";
            $i++;
        }
		
		$langStrings = getlangstrings();
        $displayEducationExperience = $langStrings['displayEducationExperience'];

        $displayEducationExperience_array = getLangstringsArray('displayWorkExperience_array', $displayLang);
        
        $table = "`".PREFIX_K."CVeducationExperience`"; 
        $table2 = "`".PREFIX_K."CVeducationExperience_lang`";
        
        checkTable($table2);
        
        $sql = "SELECT *, t1.tableKey as masterTableKey FROM (SELECT node.*, CAST(AES_DECRYPT(node.education, SHA2('".$phrase_k."', 512)) AS CHAR) as education2
            FROM ".$table." AS node,
                    ".$table."AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
                    GROUP BY node.lft
            ORDER BY node.lft) as t1 LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM (SELECT tableKey, educationExperienceId, CAST(AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) AS CHAR) as lang, CAST(AES_DECRYPT(note, SHA2('".$phrase_k."', 512)) AS CHAR) as workNote  FROM $table2 ) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615 ) as p GROUP BY educationExperienceId) as t2 ON t1.educationExperienceId = t2.educationExperienceId";
        //echo __LINE__." ".$sql."<br>";
        
        $result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
        
        echo "<div class = \"row\">";
			echo "<div class =\"col-md-4\">";
				echo "<div id = \"tree_".$replaceTable[PREFIX_K.'CVeducationExperience']."_".$lang."\" class =\"fancyTreeClass\" data-ajax_target = \"ajaxCVeducationExperience_".$lang."\" data-replace_table = \"".$replaceTable[PREFIX_K.'CVeducationExperience']."\">";
					echo "<ul id = \"tree_".$replaceTable[PREFIX_K.'CVeducationExperience']."-data\" style = \"max-height : 48vh; height : 48vh; display:none;\" >";
						while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						{
                            $tempData[] = $row;
                            
							if ($oldDepth > (int)$row['depth'])
							{
								for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
								{
									echo "</ul></li>";
								}
							}

							if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
							{
								echo "<li class = \"folder expanded\" id = \"".$row['masterTableKey']."\" data-target_div=\"targetId_".$row['tableKey']."\">".$row['education2']."<ul>";
							}
							else
							{
								echo "<li id = \"".$row['masterTableKey']."\" data-target_div=\"targetId_".$row['tableKey']."\">".$row['education2']."</li>";
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
					echo "</ul>";
				echo "</div><br><br>";
		
				echo "<form id = addForm_".$replaceTable[PREFIX_K.'CVeducationExperience']."_".$lang.">";
					echo $displayEducationExperience[1]."<br>";
					echo "<input type = \"text\" id = \"education\" class = \"form-control\"><br>";
					/*echo "<div class=\"form-check checkbox-slider--b\">";
						echo "<label>";
							echo "<input type=\"checkbox\" checked><span title = \"".$render_customer[4]."\">".$render_customer[3]."</span>";
						echo "</label>";
					echo "</div>";*/
					echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX_K.'CVeducationExperience']."_".$lang."\" data-replace_table = \"".$replaceTable[PREFIX_K."CVeducationExperience"]."\">".$displayEducationExperience[2]."</button>";
				echo "</form>";
			echo "</div>";
		
			echo "<div class = \"col-md-8\" id = \"ajaxCVeducationExperience_".$lang."\" style = \"max-height : 40vh; height : 40vh; overflow : auto;\">";
            foreach ($tempData as $key => $row)
            {
                echo "<div id = \"targetId_".$row['tableKey']."\">";
                    echo "<h2 class = \"jeditable\" id = \"education[".$row['masterTableKey']."_".$replaceLang[$lang]."]\" data-replace_table = \"".$replaceTable[PREFIX_K."CVeducationExperience"]."\" data-reload_tree = \"tree_".$replaceTable[PREFIX_K.'CVeducationExperience']."_".$lang."\" data-replace_lang = \"".$replaceLang[$lang]."\" data-replace_project = \"".$_POST['id']."\">".$row['education2']."</h2>";
                
                    echo "<div class=\"form-group row\">";
                        echo "<label for=\"start[".$row['masterTableKey']."]\" class=\"col-sm-6 col-form-label\">".$displayWorkExperience[4]."</label>";
                        echo "<div class=\"col-sm-6\">";
                            echo "<input type=\"email\" class=\"form-control datepicker syncData\" id=\"start[".$row['masterTableKey']."]\" value = \"".$row['start']."\" data-replace_table = \"".$replaceTable[PREFIX_K."CVeducationExperience"]."\">";
                        echo "</div>";
                    echo "</div>";
                
                    echo "<div class=\"form-group row\">";
                        echo "<label for=\"end[".$row['masterTableKey']."]\" class=\"col-sm-6 col-form-label\">".$displayWorkExperience[5]."</label>";
                        echo "<div class=\"col-sm-6\">";
                            echo "<input type=\"email\" class=\"form-control datepicker syncData\" id=\"end[".$row['masterTableKey']."]\" value = \"".$row['end']."\" data-replace_table = \"".$replaceTable[PREFIX_K."CVworkExperience"]."\">";
                        echo "</div>";
                    echo "</div>";

                    echo "<textarea id = \"note[";
                    if (!empty($row['tableKey']))
                    {
                        echo $row['tableKey'];
                    }
                    else 
                    {
                        echo $row['masterTableKey']."_".$replaceLang[$lang];
                    }
                    echo "]\" class = \"tinyMceArea syncData form-control\" data-replace_table = \"".$replaceTable[PREFIX_K.'CVeducationExperience']."\" placeholder = \"".$displayWorkExperience[4]."\">".$row['workNote']."</textarea>";
                echo "</div>";
            }
			echo "</div>";
        echo "</div>";
    }

    function displayCVdocuments($lang = null)
    {
        global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
        
        $replaceTable = getReplaceTable();
        
        $replaceLang = getReplaceLang();
        
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
            $where[] = "lang = '".$value."'";
            $i++;
        }
		
		$langStrings = getlangstrings();
        $displayCVdocuments = $langStrings['displayCVdocuments'];

        $displayCVdocuments_array = getLangstringsArray('displayCVdocuments_array', $displayLang);
        
        $table = "`".PREFIX_K."CVdocuments`"; 
        $table2 = "`".PREFIX_K."CVdocuments_lang`";
        
        checkTable($table2);
        
        $sql = "SELECT *, t1.tableKey as masterTableKey FROM (SELECT node.*
            FROM ".$table." AS node,
                    ".$table."AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
                    GROUP BY node.lft
            ORDER BY node.lft) as t1 LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM (SELECT tableKey, documentId	, CAST(AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) AS CHAR) as lang, CAST(AES_DECRYPT(note, SHA2('".$phrase_k."', 512)) AS CHAR) as note FROM $table2 ) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615 ) as p GROUP BY 	documentId	) as t2 ON t1.documentId = t2.documentId";
        
        $sql = "SELECT node.*, node.tableKey as masterTableKey, CAST(AES_DECRYPT(node.orgFilename, SHA2('".$phrase_k."', 512)) AS CHAR) as note
            FROM ".$table." AS node,
                    ".$table."AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
                    GROUP BY node.lft
            ORDER BY node.lft";
        //echo __LINE__." ".$sql."<br>";
        
        $result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
        
        echo "<div class = \"row\">";
			echo "<div class =\"col-md-4\" style = \"max-height : 40vh; height : 40vh; overflow : auto;\">";
				echo "<div id = \"tree_".$replaceTable[PREFIX_K.'CVdocuments']."_".$lang."\" class =\"fancyTreeClass\" data-ajax_target = \"ajaxCVdocuments_".$lang."\" data-replace_table = \"".$replaceTable[PREFIX_K.'CVdocuments']."\">";
					echo "<ul id = \"tree_".$replaceTable[PREFIX_K.'CVdocuments']."-data\" style = \"display:none;\" >";
						while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						{
                            $tempData[] = $row;
                            
							if ($oldDepth > (int)$row['depth'])
							{
								for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
								{
									echo "</ul></li>";
								}
							}

							if (((int)$row['lft'] + 1 < (int)$row['rgt'])) 
							{
								echo "<li class = \"folder expanded\" id = \"".$row['masterTableKey']."\">".$row['note']."<ul>";
							}
							else
							{
								echo "<li id = \"".$row['masterTableKey']."\" >".$row['note']."</li>";
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
					echo "</ul>";
				echo "</div><br><br>";
		
				//echo "<form id = addForm_".$replaceTable[PREFIX_K.'CVdocuments']."_".$lang.">";
					//echo $displayEducationExperience[1]."<br>";
					//echo "<input type = \"text\" id = \"education\" class = \"form-control\"><br>";
					/*echo "<div class=\"form-check checkbox-slider--b\">";
						echo "<label>";
							echo "<input type=\"checkbox\" checked><span title = \"".$render_customer[4]."\">".$render_customer[3]."</span>";
						echo "</label>";
					echo "</div>";*/
					echo "<button type = \"button\" class = \"btn btn-secondary addDocumentCV\" data-target_tree = \"tree_".$replaceTable[PREFIX_K.'CVdocuments']."_".$lang."\" data-replace_table = \"".$replaceTable[PREFIX_K."CVdocuments"]."\">".$displayCVdocuments[1]."</button>";
				//echo "</form>";
			echo "</div>";
		
			echo "<div class = \"col-md-8\" id = \"ajaxCVdocuments_".$lang."\" style = \"width:100%; height:40vh; border : 0px;\">";
            
			echo "</div>";
        echo "</div>";
    }
    
?>