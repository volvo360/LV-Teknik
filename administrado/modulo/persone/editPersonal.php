<?php
	session_start();

    echo "<!DOCTYPE html>";
    echo "<html lang=\"en\">";

	include_once("../../../common/db.php");
    include_once("./../../../common/crypto.php");
    include_once("./../../../common/userData.php");
	include_once("./../../ext/theme/nav.php");
    
    include_once("../../../common/modal.php");

	function getProfileSettings()
	{
		global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;

		global $profileSettings;
	
		$table = "`".PREFIX."user_profile_setting`";

		if (empty($profileSettings))
		{
			$sql = "SELECT CASE WHEN CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as char) IS NULL THEN 0 ELSE CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as char) END as lang, CAST(AES_DECRYPT(setting, SHA2('".$phrase."', 512)) as char) as setting, CAST(AES_DECRYPT(data, SHA2('".$phrase."', 512)) as char) as data FROM ".$table." WHERE userId = '".$_SESSION['uid']."'";
			$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$profileSettings[$row['lang']][$row['setting']] = $row['data'];
			}
		}

		return $profileSettings;

	}

	function editPersonal()
	{
		include_once("../../../common/editProfile.php");

		global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;

		$table = "`".PREFIX."users`";

		$sql = "SELECT * FROM ".$table." WHERE autoId = '".$_SESSION['uid']."'";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			echo showProfile($row['tableKey']);
		}
	}

	function editGenerally()
	{
		global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;

		$displayLang[] = 'sv';
		$displayLang[] = 'en';

		$replaceTable = getReplaceTable();

		$profileSettings = getProfileSettings();
        
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
        $editGenerally = $langStrings['editGenerally'];

        $editGenerally_array = getLangstringsArray('editGenerally_array', $displayLang);

		$table = "`".PREFIX."competence`";
		$table2 = "`".PREFIX."competence_lang`";
		$table3 = "`".PREFIX."user2competence`";
        
        $table10 = "`".PREFIX."user`";

		/*$editGenerally[1] = "Kompetens inom";
		$editGenerally[2] = "Synlig kompetens";
		$editGenerally[3] = "Gör min kompetens sökbar";
        
        $editGenerally[4] = "Profil offentlig";
        $editGenerally[5] = "Profil privat";*/
        
        if ($_SERVER['SERVER_NAME'] === 'localhost')
		{
			$url = "//localhost/lv/easyproject/";
		}
		else if ($_SERVER['SERVER_NAME'] === 'server01')
		{
			$url = "//server01/flexshare/ep/";
		}
		else
		{
			$url = "//mina-projekt.se/";
		}
        
        $sql = "SELECT * FROM ".$table10." WHERE autoId = '".$_SESSION['accountId']."'";
        //echo __LINE__." ".$sql."<br>";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
        
        while ($row = mysqli_fetch_array($result))
        {
            $tableKey = $row['tableKey'];
        }
        
        echo "<div class=\"form-group row\">";
			echo "<label for=\"displayCompetence\" class=\"col-sm-4 col-form-label\">".$editGenerally[4]."</label>";
			echo "<div class=\"col-sm-8\">";
				echo "<a href = \"".$url."profile.php?profile=".$tableKey."\" target = \"_blank\">".$url."profile.php?profile=".$tableKey."</a>";
			echo "</div>";
		echo "</div>";
        
        echo "<div class=\"form-group row\">";
			echo "<label for=\"displayCompetence\" class=\"col-sm-4 col-form-label\">".$editGenerally[5]."</label>";
			echo "<div class=\"col-sm-8\">";
				echo "<a href = \"".$url."profile.php?profile=".$tableKey."&tableKey=".getPublicKeyProject(null, "private")."\" target = \"_blank\">".$url."profile.php?profile=".$tableKey."&tableKey=".getPublicKeyProject(null, "private")."</a>";
			echo "</div>";
		echo "</div>";

		echo "<div class=\"form-group row\">";
			echo "<label for=\"userCompetence\" class=\"col-sm-4 col-form-label\">".$editGenerally[1]."</label>";
				echo "<div class=\"col-sm-8\">";

				$sql = "SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT competenceId, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note, CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang FROM ".$table2 .") as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as p GROUP BY competenceId) AS competence INNER JOIN (SELECT node.*, (COUNT(parent.competenceId) - 1) AS depth
				FROM ".$table." AS node, ".$table." AS parent WHERE node.lft BETWEEN parent.lft AND parent.rgt GROUP BY node.lft ORDER BY node.lft) as t ON t.competenceId = competence.competenceId LEFT OUTER JOIN (SELECT competenceId as selected FROM ".$table3." WHERE userId = '".$_SESSION['accountId']."') as user2competence ON user2competence.selected = t.competenceId ORDER BY t.lft";
				//echo __LINE__." ".$sql."<br>";
				$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
			
				$first = true;
				$oldDepth = 0;

				echo "<select class =\"form-control selectpicker2 show-tick\" id =\"competenceId[]\" data-replace_table = \"".$replaceTable[PREFIX."user2competence"]."\" data-live-search = \"true\" multiple = \"true\" data-size = 5>";
					while ($row = mysqli_fetch_array($result))
					{
						if ($oldDepth > (int)$row['depth'])
						{
							for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
							{
								echo "</optgroup>";
							}
						}

						if (((int)$row['lft'] + 1) < (int)$row['rgt'])
						{
							echo "<option disabled style = \"padding-left : ".(20*(int)$row['depth'])."px\">".$row['note']."</option>";
						}
						else
						{
							echo "<option value = \"".$row['tableKey']."\" style = \"padding-left : ".(20*(int)$row['depth'])."px\"";
								if (!empty($row['selected']))
								{
									echo " "."selected = \"true\"";
								}
							echo ">".$row['note']."</option>";
						}
						$oldDepth = (int)$row['depth'];
					}
				echo "</select>";
			echo "</div>";
		echo "</div>";

		echo "<div class=\"form-group row\">";
			echo "<label for=\"displayCompetence\" class=\"col-sm-4 col-form-label\">".$editGenerally[2]."</label>";
			echo "<div class=\"col-sm-8\">";
				echo "<div class=\"form-check checkbox-slider--b\">";
					echo "<label>";
						echo "<input type=\"checkbox\" class = \"syncData\" id = \"displaySearchCompetence\" data-replace_table = \"".$replaceTable[PREFIX."user_profile_setting"]."\"";
						if ((int)$profileSettings[0]['displaySearchCompetence'] > 0)
						{
							echo " "."checked = \"true\"";
						}
						echo "\"><span>".$editGenerally[3]."</span>";
					echo "</label>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
        
        echo "<div class=\"form-group row\">";
			echo "<label for=\"displayCompetence\" class=\"col-sm-4 col-form-label\">".$editGenerally[7]."</label>";
			echo "<div class=\"col-sm-8\">";
				echo "<button class = \"btn btn-secondary btnAddSocialMedia\" data-replace_table = \"".$replaceTable[PREFIX."social_media"]."\">".$editGenerally[9]."</button>";
			echo "</div>";
		echo "</div>";
		
	}

    function editServotablo()
    {
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
		$show_servotablo = $langStrings['show_servotablo'];

		$show_servotablo_array = getLangstringsArray('show_servotablo_array', $displayLang);
        
        /*$show_servotablo[1] = "När det läggs upp ett internt meddelande vill jag bli notifierad";
        $show_servotablo[2] = "När det läggs upp ett allmänt meddelande vill jag bli notifierad";
        $show_servotablo[3] = "När ett ärende inkommer till kundtjänst vill jag notifieras";
        $show_servotablo[4] = "När jag blir tilldelad ett ärende vill jag bli notifierad";
        $show_servotablo[5] = "När gruppen jag ingår i blir tilldelad ett är ärende vill jag bli notifierad";*/
        
        $noticeFields['noticeInteralMessage'] = $show_servotablo[1];
        $noticeFields['noticePublicMessage'] = $show_servotablo[2];
        $noticeFields['noticeCustomerService'] = $show_servotablo[3];
        $noticeFields['noticeAssignedNotice '] = $show_servotablo[4];
        $noticeFields['noticeGroupNotice'] = $show_servotablo[5];
       
        foreach ($noticeFields as $key => $value)
        {
            echo "<div class=\"form-check checkbox-slider--b\">";
                echo "<label>";
                    echo "<input id = \"".$key."\" type=\"checkbox\" class = \"syncData\" data-replace_table = \"".$replaceTable[PREFIX."user_settings"]."\"";
                        if ((int)$userSettings[$key] > 0 || !isset($userSettings[$key]))
                        {
                            echo " "."checked = true";
                        }
                    echo "><span>".$value."</span>";
                echo "</label>";
            echo "</div>";
        }
    }


	function editProfile($lang = null)
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
        $editProfile = $langStrings['editProfile'];

        $editProfile_array = getLangstringsArray('editProfile_array', $displayLang);
        
		$editProfile[1] = $editProfile_array[1][$lang];
		$editProfile[2] = $editProfile_array[2][$lang];
		$editProfile[3] = $editProfile_array[3][$lang];
		$editProfile[4] = $editProfile_array[4][$lang];
        $editProfile[5] = $editProfile_array[5][$lang];

		$replaceTable = getReplaceTable();
		$replaceLang = getReplaceLang();
        
        $profileSettings = getProfileSettings();
        
        $table10 = "`".PREFIX."user_profile`";
        
        $sql = "SELECT profileType, CAST(AES_DECRYPT(title, SHA2('".$phrase."', 512)) AS CHAR) as title, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM ".$table10 ." WHERE userId = '".$_SESSION['accountId']."' AND lang = AES_ENCRYPT('".$lang."', SHA2('".$phrase."', 512))";
        //echo __LINE__." ".$sql."<br>";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));

        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $title = $row['title'];
            $note[$row['profileType']] = $row['note'];
        }
        
        echo "<div class=\"form-group row\">";
			echo "<label for=\"title[".$replaceLang[$lang]."]\" class=\"col-sm-3 col-form-label\">".$editProfile[5]."</label>";
			echo "<div class=\"col-sm-9\">";
				echo "<input type=\"text\" class = \"form-control syncData\" id = \"title[".$replaceLang[$lang]."]\" data-replace_table = \"".$replaceTable[PREFIX.'user_profile']."\" value = \"".$title."\">";
                           
			echo "</div>";
		echo "</div>";
        
		echo "<div class=\"form-group row\">";
			echo "<label for=\"displayPublicProfile[".$replaceLang[$lang]."]\" class=\"col-sm-3 col-form-label\">".$editProfile[1]."</label>";
			echo "<div class=\"col-sm-9\">";
				echo "<div class=\"form-check checkbox-slider--b\">";
					echo "<label>";
						echo "<input type=\"checkbox\" class = \"syncData\" id = \"displayPublicProfile[".$replaceLang[$lang]."]\" data-replace_table = \"".$replaceTable[PREFIX.'user_profile_setting']."\"";
                            if ($profileSettings[$lang]['displayPublicProfile'])
                            {
                                echo " "."checked = \"true\"";
                            }
                        echo "><span>".$editProfile[1]."</span>";
					echo "</label>";
				echo "</div>";
			echo "</div>";
		echo "</div>";

        echo "<div style = \"max-height : 50vh; overflow:auto;\">";
        
            echo "<div class=\"form-group row\">";
                echo "<label for=\"publicProfile[".$replaceLang[$lang]."]\" class=\"col-sm-3 col-form-label\">".$editProfile[3]."</label>";
                echo "<div class=\"col-sm-9\">";
                    echo "<textarea class=\"tinyMceArea form-control tinyMceArea\" id=\"publicProfile[".$replaceLang[$lang]."]\" data-replace_table = \"".$replaceTable[PREFIX.'user_profile']."\" placeholder=\"".$editProfile[3]."\">".$note['publicProfile']."</textarea>";
                echo "</div>";
            echo "</div>";

            echo "<div class=\"form-group row\">";
                echo "<label for=\"displayPrivateProfile[".$replaceLang[$lang]."]\" class=\"col-sm-3 col-form-label\" >".$editProfile[2]."</label>";
                echo "<div class=\"col-sm-9\">";
                    echo "<div class=\"form-check checkbox-slider--b\">";
                        echo "<label>";
                            echo "<input type=\"checkbox\" class = \"syncData\" id = \"displayPrivateProfile[".$replaceLang[$lang]."]\" data-replace_table = \"".$replaceTable[PREFIX.'user_profile_setting']."\"";
                                if ($profileSettings[$lang]['displayPrivateProfile'])
                                {
                                    echo " "."checked = \"true\"";
                                }
                            echo "><span>".$editProfile[2]."</span>";
                        echo "</label>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";

            echo "<div class=\"form-group row\" >";
                echo "<label for=\"privateProfile[]\" class=\"col-sm-3 col-form-label\">".$editProfile[4]."</label>";
                echo "<div class=\"col-sm-9\">";
                    echo "<textarea class=\"tinyMceArea form-control tinyMceArea\" id=\"privateProfile[]\" placeholder=\"".$editProfile[4]."\" data-replace_table = \"".$replaceTable[PREFIX.'user_profile']."\">".$note['privateProfile']."</textarea>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    }


    function editCV($lang = null)
    {
        global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;

        $replaceTable = getReplaceTable();
        
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

        $order[] = "WHEN lang = '".$lang."' THEN -1";
        
        foreach ($displayLang as $key => $value)
        {
            $order[] = "WHEN lang = '".$value."' THEN ".$i;
            $order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
            $i++;
        }
			
        
		$langStrings = getlangstrings();
        $editCV = $langStrings['editCV'];

        $editCV_array = getLangstringsArray('editCV_array', $displayLang);
        
        //$editCV[1] = "Synlig för besökare";
        
        $cvHeadersType['text'] = $editCV[2];
        $cvHeadersType['workExperience'] = $editCV[3];
        $cvHeadersType['documents'] = $editCV[4];
        $cvHeadersType['educationExperience'] = $editCV[5];
        
        
        $table = "`".PREFIX_K."CVheaders`";
        $table2 = "`".PREFIX_K."CVheaders_lang`";
        
        $table10 = "`".PREFIX_K."CV_settings`";
        
        $table30 = "`".PREFIX."user2account`"; 
        
        $table40 = "`".PREFIX."account`"; 
        
        $table50 = "`".PREFIX."default_collection`";
        $table51 = "`".PREFIX."default_collection2account_plan`";
        $table52 = "`".PREFIX."default_collection2menu`";
        
        $table60 = "`".PREFIX."administrado_menu`";
        
        $table70 = "`".PREFIX."headers`";
        $table71 = "`".PREFIX."headers2account`";
        $table72 = "`".PREFIX."headers_lang`";
        
        $table80 = "`".PREFIX."default_users`";
        $table81 = "`".PREFIX."default_users_lang`";
        $table82 = "`".PREFIX."default_users2account_plan`";
        
        
        $table90 = "`".PREFIX."theme`";
        $table91 = "`".PREFIX."theme_lang`";
        $table92 = "`".PREFIX."theme_collection`";
        
        $sql = "SELECT CAST(AES_DECRYPT(setting, SHA2('".$phrase_k."', 512)) AS CHAR) as setting, CAST(AES_DECRYPT(data, SHA2('".$phrase_k."', 512)) AS CHAR) as data FROM ".$table10."";
        $result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));

        while ($row = mysqli_fetch_array($result))
        {
            ${$row['setting']} = $row['data'];   
        }
        
        $sql = "SELECT *, t50.tableKey as tableKey FROM ".$table50." t50 INNER JOIN ".$table51." t51 ON t51.collectionId = t50.collectionId INNER JOIN ".$table40." t40 ON t40.accountPlan = t51.accountPlan INNER JOIN ".$table60." t60 ON t60.accountPermission >= t51.accountPlan INNER JOIN ".$table52." t52 ON t52.menuId = t60.menuId WHERE t40.autoId = '".mysqli_real_escape_string($link, $_SESSION['accountId'])."' AND folder = 'persone' AND t52.collectionId = t50.collectionId AND extra = 'cv' GROUP BY t51.collectionId"; 
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result))
        {
            $collectionId[$row['collectionId']] = "collectionId = '".$row['collectionId']."'";
        }

        $sql = "SELECT * FROM ".$table40." t40 INNER JOIN ".$table30." t30 ON t40.autoId = t30.accountId WHERE userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."' AND accountId = '".mysqli_real_escape_string($link, $_SESSION['accountId'])."'";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result))
        {
            $accountPlan = $row['accountPlan'];
        }
        
        echo "<div class=\"form-group row\">";
			echo "<label for=\"CVLayout\" class=\"col-sm-3 col-form-label\">".$editCV_array[1][$lang]."</label>";
    		echo "<div class=\"col-sm-9\">";
                
                $sql = "SELECT * FROM ".$table92." WHERE UPPER(extra) = 'CV'";
                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                    $collectionId3 = $row['collectionId'];
                    $tableKey = $row['tableKey'];
                }
        
                $sql = "SELECT *,theme.tableKey as tableKey FROM (SELECT node.*, COUNT(parent.lft) - 1 AS depth
                FROM (SELECT * FROM ".$table90." WHERE collectionId = '".$collectionId3."') AS node,
                        (SELECT * FROM ".$table90." WHERE collectionId = '".$collectionId3."') AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                        GROUP BY node.lft
                ORDER BY node.lft) theme INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT tableKey, themeId, AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as lang, AES_DECRYPT(note, SHA2('".$phrase."', 512)) as note FROM ".$table91.") as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as p GROUP BY themeId) as theme_lang ON theme.themeId = theme_lang.themeId";
                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
				echo "<select id = \"CVlayout\" class = \"selectpicker2 syncData form-control\" data-replace_table = \"".$replaceTable[PREFIX_K.'CV_settings']."\">";
        
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
                        echo "<option value = \"".$row['tableKey']."\"";
                            if ((int)$CVLayout == $row['themeId'])
                            {
                                echo " "."selected";
                            }
                        echo ">".$row['note']."</option>";
                    }
                
                echo "</select>";
    		echo "</div>";
		echo "</div>";
        
        echo "<div class=\"form-group row\">";
			echo "<label for=\"minLevel\" class=\"col-sm-3 col-form-label\">".$editCV[1]."</label>";
    		echo "<div class=\"col-sm-9\">";
                $sql = "SELECT *, t80.tableKey as tableKey FROM ".$table80." t80 INNER JOIN ".$table82." t82 ON t80.defaultUserId = t82.defaultUserId INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT defaultUserId, CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as CHAR) as lang, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) as CHAR) as note FROM ".$table81." ) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t GROUP BY defaultUserId) as t81 ON t81.defaultUserId = t80.defaultUserId WHERE t82.accountPlan = '".$accountPlan."'";
                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
				echo "<select id = minlevel class = \"selectpicker2 syncData form-control\" data-replace_table = \"".$replaceTable[PREFIX_K.'CV_settings']."\">";
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
                        echo "<option value = \"".$row['tableKey']."\"";
                            if ((int)$minlevel === (int)$row['defaultUserId'])
                            {
                                echo " "."selected";
                            }
                        echo ">".$row['note']."</option>";
                    }
                echo "</select>";
    		echo "</div>";
		echo "</div>";
        
        
        
        $sql = "SELECT * FROM (SELECT node.*
                FROM (SELECT * FROM ".$table50." WHERE ".implode(" OR ", (array)$collectionId).") AS node,
                        (SELECT * FROM ".$table50." WHERE ".implode(" OR ", (array)$collectionId).") AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                        GROUP BY node.lft
                ORDER BY node.lft) as t50 INNER JOIN ".$table51." t51 ON t50.collectionId = t51.collectionId INNER JOIN ".$table70." t70 ON t50.collectionId = t70.collectionId INNER JOIN ".$table71." t71 ON t71.headerId = t70.headerId INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT headerId, CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as CHAR) as lang, CAST(AES_DECRYPT(comment, SHA2('".$phrase."', 512)) as CHAR) as comment FROM ".$table72." ) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t GROUP BY headerId) as t72 ON t70.headerId = t72.headerId WHERE t51.accountPlan = '".$accountPlan."' AND t71.accountTypeId = '".$accountPlan."'";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while ($row = mysqli_fetch_array($result))
        {
            $placeHolder[$row['headerId']] = $row['comment'];
        }
        
        checkTable($table);
        
        $sql = "SELECT *, t2.tableKey as tableKey, t1.tableKey as masterTableKey FROM (SELECT node.*, COUNT(parent.lft) as depth
                FROM ".$table." AS node, ".$table." AS parent WHERE node.lft BETWEEN parent.lft AND parent.rgt
                GROUP BY node.lft
                ORDER BY node.lft) t1 INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT tableKey, headerId, CAST(AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) as CHAR) as lang, CAST(AES_DECRYPT(note, SHA2('".$phrase_k."', 512)) as CHAR) as note, CAST(AES_DECRYPT(text, SHA2('".$phrase_k."', 512)) as CHAR) as text FROM ".$table2." ) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t GROUP BY headerId) as t2 ON t1.headerId = t2.headerId";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
        
        if (mysqli_num_rows($result) == 0)
        {
            
            
            insertCVheaderDefault();
            
            //Reload datbasequery with our newly inserted default data
            
            $sql = "SELECT *, t2.tableKey as tableKey FROM ".$table." t1 INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT tableKey, headerId, CAST(AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) as CHAR) as lang, CAST(AES_DECRYPT(note, SHA2('".$phrase_k."', 512)) as CHAR) as note FROM ".$table2." ) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t GROUP BY headerId) as t2 ON t1.headerId = t2.headerId";
           
            $result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
        }
        
        echo "<div class = \"row\">";
            echo "<div class =\"col-md-3\" style = \"max-height : 48vh; height : 48vh; overflow : auto\">";
                echo "<div id = \"tree_".$replaceTable[PREFIX_K.'CVheaders']."_".$lang."\" class =\"fancyTreeClass\" data-ajax_target = \"ajaxDefaultCVheaders_".$lang."\" data-replace_table = \"".$replaceTable[PREFIX_K.'CVheaders']."\" >";
                    echo "<ul id = \"tree_".$replaceTable[PREFIX_K.'CVheaders']."-data\" style = \"display:none;\" >";
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
                                echo "<li class = \"folder expanded\"";
                                    if (array_key_exists($row['headerId'], (array)$headerId))
                                    {
                                        echo " "."data-selected = true";
                                    }
                                echo "id = \"".$row['masterTableKey']."\" data-target_div = \"targetId_".$row['tableKey']."\">".$row['note']."<ul>";
                            }
                            else
                            {
                                echo "<li id = \"".$row['masterTableKey']."\"";
                                    if (array_key_exists($row['headerId'], (array)$headerId))
                                    {
                                        echo " "."data-selected = true";
                                    }
                                echo " data-target_div = \"targetId_".$row['tableKey']."\">".$row['note']."</li>";
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
        
                echo "<form id = addForm_".$replaceTable[PREFIX_K.'CVheaders']."_".$lang.">";
					echo $editCV[6]."<br>";
					echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";
					
					echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX_K.'CVheaders']."_".$lang."\" data-replace_table = \"".$replaceTable[PREFIX_K."CVheaders"]."\">".$editCV[7]."</button>";
				echo "</form>";
            echo "</div>";

            echo "<div class = \"col-md-9\" id = \"ajaxDefaultCVheaders_".$lang."\" style = \"max-height : 48vh; height : 48vh; overflow : auto\">";
        
            foreach ($tempData as $key => $row)
                {
                    echo "<div id = \"targetId_".$row['tableKey']."\"";
                        if ((int)$row['depth'] > 1)
                        {
                            echo " "."style = \"margin-left : ".(15*(int)$row['depth'])."px;\"";
                        }
                    echo">";
                        echo "<div class = \"row\">";
                            echo "<div class = \"col-md-8\">";
                                if (!empty($row['note']))
                                {
                                    echo "<h".$row['depth']." class = \"jeditable\" id = \"note[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX_K."CVheaders"]."\" style = \"width : 100%;\" data-reload_tree = \"tree_".$replaceTable[PREFIX_K.'CVheaders']."_".$lang."\" data-replace_lang = \"".$replaceLang[$lang]."\" data-replace_project = \"".$_POST['id']."\">".$row['note']."</h".$row['depth'].">";
                                }
                                else
                                {
                                    echo "<h".$row['depth']." class = \"jeditable\" id = \"note[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX_K."CVheaders"]."\" style = \"width : 100%;\">&nbsp;</h".$row['depth'].">";
                                }
                                
                            echo "</div>";
                        
                            echo "<div class = \"col-md-4\">";
                    
                                $sql = "SELECT SUBSTRING(COLUMN_TYPE,5) as enum
                                            FROM information_schema.COLUMNS
                                            WHERE TABLE_NAME='".PREFIX_K.'CVheaders'."'
                                                AND COLUMN_NAME='headerType'";
                                $result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
                                
                                while ($row2 = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                {
                                    $temp = $row2['enum']; 
                                }
                                
                                $temp = trim(trim($temp,"("), ")");
                    
                                $enum = array_map('trim', explode(",", $temp));
                    
                                echo "<select id = \"headerType[".$row['masterTableKey']."]\" class = \"selectpicker2\" data-replace_table =\"".$replaceTable[PREFIX_K.'CVheaders']."\" >";
                                    
                                    foreach ($enum as $key => &$value)
                                    {
                                        $value = trim($value, "'");
                                        
                                        if (empty($value))
                                        {
                                            continue;
                                        }
                                        
                                        echo "<option value = \"".$value."\"";
                                            if ($row['headerType'] == $value)
                                            {
                                                echo " "."selected";
                                            }
                                        echo ">".$cvHeadersType[$value]."</option>";
                                    }
                                    
                                echo "</select>";
                            echo "</div>";
                    
                        echo "</div>";
                    
                        if ($row['headerType'] == "workExperience")
                        {
                            displayWorkExperience($lang);
                        }
                        else if ($row['headerType'] == "educationExperience")
                        {
                            displayEducationExperience($lang);
                        }
                        else if ($row['headerType'] == "documents")
                        {
                            displayCVdocuments($lang);
                        }
                        else
                        {
                            echo "<textarea class = \"tinyMceArea form-control syncData\" id=\"text[".$row['tableKey']."]\" placeholder = \"".$placeHolder[$row['headerId']]."\" data-replace_table =\"".$replaceTable[PREFIX_K.'CVheaders']."\">".$row['text']."</textarea>";
                        }
                    echo "</div><br>";
                }
            echo "</div>";
        echo "</div>";
    }

	function editProfileTab()
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
        $editProfileTab = $langStrings['editProfileTab'];

        $editProfileTab_array = getLangstringsArray('editProfileTab_array', $displayLang);
		
		/*$editProfileTab[1] = "Generellt";

		$editProfileTab_array[1]['sv'] = "Profil";
		$editProfileTab_array[1]['en'] = "Profile";*/

		$tabs['generally'] = $editProfileTab[1];
        
		foreach ($editProfileTab_array[1] as $key => $value)
		{
			$tabs['profile_'.$key] = $value;
		} 
        
        $table40 = "`".PREFIX."account`";
        
        $sql = "SELECT * FROM ".$table40." WHERE autoId = '".mysqli_real_escape_string($link, $_SESSION['accountId'])."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $typeAccount = $row['typeAccount'];   
        }
        
        if ($typeAccount === "private")
        {
            foreach ($editProfileTab_array[2] as $key => $value)
            {
                $tabs['CV_'.$key] = $value."[".$key."]";
            } 
        }
        
		$table = "`".PREFIX."user_profile`";

		$first = true;

		echo "<ul class=\"nav nav-tabs\" id=\"myTab\" role=\"tablist\">";
			foreach ($tabs as $key => $value)
			{
				echo "<li class=\"nav-item\">";
					echo "<a class=\"nav-link ";
						if ($first)
						{
							echo " "."active ";
						}
						echo "\" id=\"".$key."-tab\" data-toggle=\"tab\" href=\"#".$key."\" role=\"tab\" aria-controls=\"".$key."\" aria-selected=\"";
						if ($first)
						{
							echo "true";
							$first = false;
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
			$first = true;
			foreach ($tabs as $key => $value)
			{
				echo "<div class=\"tab-pane fade";
					if ($first)
					{
						echo " ". "show active";
						$first = false;
					}
				echo "\" id=\"".$key."\" role=\"tabpanel\" aria-labelledby=\"".$key."-tab\">";
                echo "<br>";
					$key2 = explode("_", $key);
					call_user_func("edit".ucfirst($key2[0]), $key2[1]);
				echo "</div>";
			}
		echo "</div>";
	}

    function getMenues()
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
        $getMenues = $langStrings['getMenues'];

        $getMenues_array = getLangstringsArray('getMenues_array', $displayLang);

        $table = "`".PREFIX."servotablo_menu`";
        $table2 = "`".PREFIX."servotablo_menu_lang`";
        
        $table10 = "`".PREFIX."administrado_menu`";
        $table11 = "`".PREFIX."administrado_menu_lang`";
        
        $table20 = "`".PREFIX."menu`";
        $table21 = "`".PREFIX."menu_lang`";
        
        $sql = "SELECT * FROM (SELECT node.*, COUNT(parent.lft) as depth
			  FROM $table AS node,
					  $table AS parent
			  WHERE node.lft BETWEEN parent.lft AND parent.rgt
			  GROUP BY node.lft ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT menuId, 
					  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
					  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table2) as p 
					  ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
					  GROUP BY menuId) AS lang ON menu.menuId = lang.menuId ORDER BY menu.lft";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $data['servo'][$row['tableKey']]['note'] = $row['note'];
            $data['servo'][$row['tableKey']]['depth'] = $row['depth'];
        }
        
        $sql = "SELECT * FROM (SELECT node.*, COUNT(parent.lft) as depth
			  FROM $table10 AS node,
					  $table10 AS parent
			  WHERE node.lft BETWEEN parent.lft AND parent.rgt
			  GROUP BY node.lft ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT menuId, 
					  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
					  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table11) as p 
					  ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
					  GROUP BY menuId) AS lang ON menu.menuId = lang.menuId ORDER BY menu.lft";
        // echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $data['admini'][$row['tableKey']]['note'] = $row['note'];
            $data['admini'][$row['tableKey']]['depth'] = $row['depth'];
        }
        
        $sql = "SELECT * FROM (SELECT node.*, COUNT(parent.lft) as depth
			  FROM $table20 AS node,
					  $table20 AS parent
			  WHERE node.lft BETWEEN parent.lft AND parent.rgt
			  GROUP BY node.lft ORDER BY node.lft) as menu INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT menuId, 
					  CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, 
					  CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM $table21) as p 
					  ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t 
					  GROUP BY menuId) AS lang ON menu.menuId = lang.menuId WHERE display != 0 AND file != 'logout.php' ORDER BY menu.lft";
        // echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $data['home'][$row['tableKey']]['note'] = $row['note'];
            $data['home'][$row['tableKey']]['depth'] = $row['depth'];
        }
        
        return $data;
    }

    function getUser2Account()
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
		$getUser2Account = $langStrings['getUser2Account'];

		$getUser2Account_array = getLangstringsArray('getUser2Account_array', $displayLang);
        
        $table = "`".PREFIX."user2account`";
        $table2 = "`".PREFIX."account`";
        
        $sql = "SELECT *, CAST(AES_DECRYPT(accountName, SHA2('".$phrase."', 512)) AS CHAR) AS accountName FROM ".$table." t1 INNER JOIN ".$table2." t2 ON t1.accountId = t2.autoId WHERE userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."' ORDER BY CASE WHEN typeAccount = 'private' THEN 1 WHEN typeAccount = 'collaboration' THEN 2 WHEN typeAccount = 'company' THEN 3 ELSE 1000 END";
        //echo __LINE__." ".$sql."<br>";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));

        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            if (empty($row['accountName']))
            {
                $data[$row['replaceKey']] = $getUser2Account[1];
            }
            else
            {
                $data[$row['replaceKey']] = $row['accountName'];
            }
            
        }
        
        return $data;
    }

    function prioTranslation()
    {
        global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
        
        $table = "`".PREFIX."translator2lang`";
        $table2 = "`".PREFIX."languages`";
        $table3 = "`".PREFIX."user_translation_settings`";
        
        $table10 = "`".PREFIX."servotablo_permission2user`";
        
        $sql = "SELECT * FROM ".$table10." WHERE userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."'";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
        
        if (mysqli_num_rows($result) == 0)
        {
            return false;
        }
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $permissionId = $row['permissionId'];
        }
        
        $userSettings = getUserSettings();
        
        $replaceLang = getReplaceLang();
        
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
         
        $sql = "SELECT CAST(AES_DECRYPT(data, SHA2('".$phrase."', 512)) AS CHAR) as data FROM ".$table3." WHERE setting = AES_ENCRYPT('langService', SHA2('".$phrase."', 512)) AND userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."'";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
        
        if (mysqli_num_rows($result) > 0)
        {
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $temp = $row['data'];
            }
            
            $temp2 = array_map("trim", explode(",", $temp));
                
            $i = 0;
            foreach ($temp2 as $key => $value)
            {
                $order[] = "WHEN lang = '".$value."' THEN ".$i;
                $order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
                if ((int)$permissionId > 1)
                {
                    $where_lang[] = "code = '".$value."'";
                }

                $i++;
            }    
        }
        else
        {
            $i = 0;
            foreach ($displayLang as $key => $value)
            {
                $order[] = "WHEN lang = '".$value."' THEN ".$i;
                $order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
                if ((int)$permissionId >= 1)
                {
                    $where_lang[] = "code = '".$value."'";
                }

                $i++;
            }
        }
        
        
        if ((int)$permissionId > 1)
        {
            $where_lang[] = "code = 'en'";
            $where_lang[] = "code = 'de'";
            $where_lang[] = "code = 'fr'";
            $where_lang[] = "code = 'it'";
            $where_lang[] = "code = 'es'";
        }
        else
        {
            $sql = "SELECT * FROM ".$table."t1 INNER JOIN ".$table2." t2 ON t1.langId = t2.autoId WHERE t1.userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."'";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
        
            if (mysqli_num_rows($result) == 0)
            {
                return false;
            }

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $where_lang[] = "code = '".$row['code']."'";
            }
        }
        
        $langStrings = getlangstrings();
		$prioTranslation = $langStrings['prioTranslation'];

		$prioTranslation_array = getLangstringsArray('prioTranslation_array', $displayLang);
        
        $sql = "SELECT tableKey, `Local language name` as lang, Code FROM ".$table2." WHERE (".implode(" OR ", (array)$where_lang).") ORDER BY CASE ".implode(" ", (array)$order_lang)." WHEN Code = 'en' THEN 11 WHEN Code = 'de' THEN 12 WHEN Code = 'fr' THEN 13 WHEN Code = 'it' THEN 14 ELSE 100 END, code LIMIT 18446744073709551615";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $prioLang[$row['tableKey']] = $row['lang'];
            $replaceLangCode[$row['Code']] = $row['tableKey'];
        }
        
        echo "<div class=\"form-group row\">";
            echo "<label for=\"selectedLang\" class=\"col-sm-2 col-form-label\">".$prioTranslation[1]."</label>";
            echo "<div class=\"col-sm-4\">";
                echo "<div id = \"tree_prioTranslationLang\" class = \"fancyTreeClass\" data-replace_table = \"".$replaceTable[PREFIX.'user_settings']."\">";
                    echo "<ul style =\"display : none;\">";

                        foreach ($prioLang as $key => $value)
                        {
                            echo "<li id = \"".$key."\">";
                                echo $value;
                            echo "</li>";
                        }

                    echo "</ul>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    }

    function editPersonalSettings()
    {
        global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
        
        $table = "`".PREFIX."translation`";
        $table2 = "`".PREFIX."languages`";
        
        $replaceTable = getReplaceTable();
        $userSettings = getUserSettings();
        $temp = getUser2Account();
        $replaceLang = getReplaceLang();
        
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
		$editPersonalSettings = $langStrings['editPersonalSettings'];

		$editPersonalSettings_array = getLangstringsArray('editPersonalSettings_array', $displayLang);
        
        $contactTypes['service'] = $editPersonalSettings[1];
        $contactTypes['mail'] = $editPersonalSettings[2];
        /*echo "<div class=\"form-group row\">";
            echo "<label for=\"inputEmail3\" class=\"col-sm-2 col-form-label\">Email</label>";
            echo "<div class=\"col-sm-10\">";
                echo "<input type=\"email\" class=\"form-control\" id=\"inputEmail3\">";
            echo "</div>";
        echo "</div>";*/
        
        
        if (count($temp) >= 1)
        {
            echo "<div class=\"form-group row\">";
                echo "<label for=\"defaultAccount \" class=\"col-sm-2 col-form-label\">".$editPersonalSettings[3]."</label>";
                echo "<div class=\"col-sm-10\">";
                    echo "<select class=\"form-control selectpicker2\" id=\"defaultAccount\" data-repalce_table = \"".$replaceTable[PREFIX."user_settings"]."\"  data-size = 5>";
                        foreach ($temp as $key => $value)
                        {
                            echo "<option value = \"".$key."\"";
                                if ($defaultAccount === $key)
                                {
                                    echo " "."selected = true";
                                }
                            echo ">".$value."</option>";
                        }
                    echo "</select>";
                echo "</div>";
            echo "</div>";
        }
        

        echo "<div class=\"form-group row\">";
            echo "<label for=\"inputEmail3\" class=\"col-sm-2 col-form-label\">".$editPersonalSettings[4]."</label>";
            echo "<div class=\"col-sm-10\">";
                $data = getMenues();
                    
                echo "<select class = \"form-control selectpicker2\" id =\"defaultPage\" data-repalce_table = \"".$replaceTable[PREFIX."user_settings"]."\" data-size = 5>";
                    foreach ($data as $key => $value)
                    {
                        echo "<optgroup label = \"".$key."\">";
                            foreach ($value as $sub_key => $sub_value)
                            {
                                echo "<option value = \"".$sub_key."\" style = \"padding-left : ".(15*(int)$sub_value['depth'])."px\"";
                                    if ($defaultPage === $key)
                                    {
                                        echo " "."selected = true";
                                    }
                                echo ">".$sub_value['note']."</option>";
                            }
                        echo "</optgroup>";
                    }
                echo "</select>";
                
        echo "<br>";
            echo "</div>";
        echo "</div>";
        
        echo "<div class=\"form-group row\">";
            echo "<label for=\"contactType\" class=\"col-sm-2 col-form-label\">".$editPersonalSettings[5]."</label>";
            echo "<div class=\"col-sm-10\">";
                echo "<select id = \"contactType\" class = \"selectpicker2 form-control\" data-replace_table = \"".PREFIX."user_settings"."\">";
                    foreach ($contactTypes as $key => $value)
                    {
                        echo "<option value = \"".$key."\"";
                            if ($contactType === $key)
                            {
                                echo " "."selected = \"true\"";
                            }
                        echo ">".$value."</option>";
                    }
                echo "</select><br>";
                echo "<small id=\"contactTypeHelp\" class=\"form-text text-muted\">".$editPersonalSettings[6]."</small>";
            echo "</div>";
        echo "</div>";        
        
        echo "<div class=\"form-group row\">";
            echo "<label for=\"langService\" class=\"col-sm-2 col-form-label\">".$editPersonalSettings[7]."</label>";
            echo "<div class=\"col-sm-10\">";
    
                $sql = "SELECT tableKey, `Local language name` as lang, Code FROM ".$table2." ORDER BY CASE ".implode(" ", (array)$order_lang)." WHEN Code = 'en' THEN 11 WHEN Code = 'de' THEN 12 WHEN Code = 'fr' THEN 13 WHEN Code = 'it' THEN 14 ELSE 100 END, code LIMIT 18446744073709551615";
                //echo __LINE__." ".$sql."<br>";
                $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));

                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                    $replaceLang[$row['tableKey']] = $row['lang'];
                    $replaceLangCode[$row['Code']] = $row['tableKey'];
                }
        
                $sql = "SELECT * FROM (SELECT CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang FROM (SELECT * FROM ".$table." GROUP BY lang) as t) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615";
                //echo $sql."<br>";
                $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));

                unset($data);
        
                while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                    $data[$row['lang']] = $row['lang'];
                }
                
                echo "<select id = \"langService\" class = \"form-control selectpicker2\" data-replace_table = \"".$replaceTable[PREFIX."user_settings"]."\" data-update_tree = \"prioLang\" data-target_ajax_div = \"prioLang.php\" data-size = \"5\" multiple = \"true\">";
                    foreach ($data as $key => $value)
                    {
                        if (array_key_exists($key, (array)$replaceLangCode))
                        {
                            echo "<option value = \"".$replaceLangCode[$key]."\"";
                            
                                if (array_search($key, $displayLang) !== false)
                                {
                                    echo " "."selected";
                                }
                            
                            echo ">".$replaceLang[$replaceLangCode[$key]]."</option>";
                        }
                    }
        
                    echo "<optgroup label=\"Ej översatta\">";
                        foreach ($replaceLangCode as $key => $value)
                        {
                            if (!array_key_exists($key, (array)$data))
                            {
                                echo "<option value = \"".$value."\"";

                                    if (array_search($key, $displayLang) !== false)
                                    {
                                        echo " "."selected";
                                    }

                                echo ">".$replaceLang[$value]."</option>";
                            }
                        }
                    echo "</optgroup>";
                echo "</select>";
            echo "</div>";
        echo "</div>";
        
        echo "<div id = \"prioLang\">";
            prioLang();
        echo "</div>";
        
        echo "<div>";
            prioTranslation();
        echo "</div>";
        
    }

	function displayContentPersonalData()
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
		$displayContentPersonalData = $langStrings['displayContentPersonalData'];

		$displayContentPersonalData_array = getLangstringsArray('displayContentPersonalData_array', $displayLang);

		$navs['personal'] = $displayContentPersonalData[1];
		$navs['profileTab'] = $displayContentPersonalData[2];
        $navs['personalSettings'] = $displayContentPersonalData[3];
        $navs['servotablo'] = $displayContentPersonalData[4];

		echo "<div class=\"row\">";
			echo "<div class=\"col-3\">";
				echo "<div class=\"nav flex-column nav-pills\" id=\"v-pills-tab\" role=\"tablist\" aria-orientation=\"vertical\">";
					$first = true;
					foreach ($navs as $key => $value)
					{	
						echo "<a class=\"nav-link";
							if ($first)
							{
								echo " "."active";
							}
						echo "\" id=\"v-pills-".$key."-tab\" data-toggle=\"pill\" href=\"#v-pills-".$key."\" role=\"tab\" aria-controls=\"v-pills-".$key."\" aria-selected=\"";
							if ($first)
							{
								echo "true";
								$first = false;
							}
							else
							{
								echo "false";
							}
						echo "\">".$value."</a>";
					}
				echo "</div>";
			echo "</div>";
			echo "<div class=\"col-9\">";
				echo "<div class=\"tab-content\" id=\"v-pills-tabContent\">";
					$first = true;
					foreach ($navs as $key => $value)
					{
						echo "<div class=\"tab-pane fade";
							if ($first)
							{
								echo " "."show active";
								$first = false;
							}
						echo "\" id=\"v-pills-".$key."\" role=\"tabpanel\" aria-labelledby=\"v-pills-".$key."-tab\">";
							call_user_func("edit".ucfirst($key));
						echo "</div>";
					}
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}

	function displayContentPersonal()
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
		$displayContentPersonal = $langStrings['displayContentPersonal'];

		$displayContentPersonal_array = getLangstringsArray('displayContentPersonal_array', $displayLang);
        
		echo "<div class=\"panel-header panel-header-sm\">";
		
		echo "</div>";

        echo "<div class=\"content\" style = \"max-height : 40vh; height : 40vh;\">";
            echo "<div class=\"row\" style = \"height : 95%;\">";
                echo "<div class=\"col-md-12\" style = \"height : 100%;\">";
                    echo "<div class=\"card\" style = \"height : 100%;\">";
                        echo "<div class=\"card-header\">";
                            echo "<h1>".$displayContentPersonal[1]."</h1>";
                        echo "</div>";
                        echo "<div class=\"card-body\">";
                            //displayContentPersonalData();
                            editPersonal();
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
	}

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        printHeader();
        displayMenuAdministradoHeader();
        displayContentPersonal();
        displayFooterAdministrado();
        printScripts();
    }
?>