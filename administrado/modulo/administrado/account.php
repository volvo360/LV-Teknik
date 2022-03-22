<?php
	session_start();

    echo "<!DOCTYPE html>";
    echo "<html lang=\"en\">";

	include_once("../../../common/db.php");
    include_once("../../../common/userData.php");
	include_once("./../../ext/theme/nav.php");
    include_once("../../../common/modal.php");
    
    function show_defaultType()
    {
        global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
		
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
		$show_defaultType = $langStrings['show_defaultType'];

		$show_defaultType_array = getLangstringsArray('show_defaultType_array', $displayLang);
        
        /*$show_defaultType[1] = "Standard typ";
        $show_defaultType[2] = "Standard språk";
        $show_defaultType[3] = "Dokumentations språk";*/
        
        $table2 = "`".PREFIX_K."dokumenti_settings`";
    
        $sql = "SELECT *, CAST(AES_DECRYPT(setting, SHA2('".$phrase_k."', 512)) AS CHAR) as setting,
                          CAST(AES_DECRYPT(data, SHA2('".$phrase_k."', 512)) AS CHAR) as data
                    FROM ".$table2 ." WHERE projectId IS NULL OR projectId = 0";
        //echo $sql ."<br>";
        $result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));

        while ($row = mysqli_fetch_array($result))
        {
            ${$row['setting']} = $row['data'];
            $settingTableKey[$row['setting']] = $row['tableKey'];
        }
       
        $temp = array_map(trim, explode(",", $documentationLang));

        foreach ($temp as $key => $value)
        {
            $displayLang[] = $value;
        }

        if (empty($showDoclang))
        {
            $showDoclang = reset($displayLang);
        }

        foreach ($displayLang as $key => $value)
        {
            $order[] = "WHEN lang = '".$value."' THEN ".$key;
            $order_lang[] = "WHEN Code = '".$value."' THEN ".$key;
        }
        
        $table = "`".PREFIX."languages`";
	
        $sql = "SELECT tableKey, `Local language name` as lang, Code FROM ".$table." ORDER BY CASE ".implode(" ", (array)$order_lang)." WHEN Code = 'en' THEN 11 WHEN Code = 'de' THEN 12 WHEN Code = 'fr' THEN 13 WHEN Code = 'it' THEN 14 ELSE 100 END, code LIMIT 18446744073709551615";
        //echo __LINE__." ".$sql."<br>";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $replaceLang[$row['tableKey']] = $row['lang'];
            $replaceLangCode[$row['Code']] = $row['tableKey'];
        }
        
        echo "<div class=\"form-group row\">";
            echo "<label for=\"defaultProjectType[".$row['tableKey']."\" class=\"col-sm-2 col-form-label\">".$show_defaultType[1]."</label>";
            echo "<div class=\"col-sm-10\">";
                $table = PREFIX."project_types";
                $table2 = PREFIX."project_types_lang";

                $table10 = PREFIX."project_keys";

                $table100 = PREFIX_K."project";
                $table101 = PREFIX_K."project_lang";

                $replaceTable = getReplaceTable();

                $sql = "SELECT * FROM ".$table." t1 INNER JOIN (SELECT * FROM (SELECT projectId, CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) as CHAR) as note FROM ".$table2.") as k ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as lang ON lang.projectId = t1.projectId GROUP BY t1.projectId";
                //echo $sql."<br>";
                $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));

                $temp = array_flip(array_filter(array_map("trim", explode(",", $defaultProjectType))));
        
                echo "<select id = \"defaultProjectType[".$settingTableKey['defaultProjectType']."]\" class = \"selectpicker2 form-control\" data-replace_table = \"".$replaceTable[PREFIX_K."dokumenti_settings"]."\">";
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                    echo "<option value = \"".$row['projectType']."\"";
                        if (array_key_exists($row['projectType'], (array)$temp))
                        {
                            echo " "."selected = \"true\"";
                        }
                    echo ">".$row['note']."</option>";
                }
                echo "</select>";
            echo "</div>";
        echo "</div>";
        
        echo "<div class=\"form-group row\">";
            echo "<label for=\"defaultProjectLang[".$row['tableKey']."\" class=\"col-sm-2 col-form-label\">".$show_defaultType[2]."</label>";
            echo "<div class=\"col-sm-10\">";
                $temp = array_flip(array_filter(array_map("trim", explode(",", $defaultProjectLang))));
        
                foreach ($temp as $key => $value)
                {
                    $temp2[$replaceLangCode[$key]] = $replaceLangCode[$key];
                }
        
                echo "<select id = \"defaultProjectLang[".$settingTableKey['defaultProjectLang']."]\"class = \"selectpicker2 form-control show-tick\" data-size=\"4\" data-dropup-auto=false multiple = true data-replace_table = \"".$replaceTable[PREFIX_K."dokumenti_settings"]."\" data-live-search = \"true\">";
                    foreach ($replaceLang as $key => $value)
                    {
                        echo "<option value = \"".$key."\"";
                            if (array_key_exists($key, (array)$temp2))
                            {
                                echo " "."selected = \"true\"";
                            }
                        echo ">".$value."</option>";
                    }
                echo "</select>";
            echo "</div>";
        echo "</div>";
        
        echo "<div class=\"form-group row\">";
            echo "<label for=\"defaultDokumentiLang[".$row['tableKey']."\"] class=\"col-sm-2 col-form-label\">".$show_defaultType[3]."</label>";
            echo "<div class=\"col-sm-10\">";
                $temp = array_flip(array_filter(array_map("trim", explode(",", $defaultDokumentiLang))));
                
                unset($temp2);
        
                foreach ($temp as $key => $value)
                {
                    $temp2[$replaceLangCode[$key]] = $replaceLangCode[$key];
                }
        
                echo "<select id = \"defaultDokumentiLang[".$settingTableKey['defaultDokumentiLang']."]\"class = \"selectpicker2 form-control show-tick\" data-dropup-auto=false data-size=\"4\" multiple = true data-replace_table = \"".$replaceTable[PREFIX_K."dokumenti_settings"]."\" data-live-search = \"true\">";
                    foreach ($replaceLang as $key => $value)
                    {
                        echo "<option value = \"".$key."\"";
                            if (array_key_exists($key, (array)$temp2))
                            {
                                echo " "."selected = \"true\"";
                            }
                        echo ">".$value."</option>";
                    }
                echo "</select>";
            echo "</div>";
        echo "</div>";
    }

    function show_defaultTextDBField()
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
		$show_defaultTextDBField = $langStrings['show_defaultTextDBField'];

		$show_defaultTextDBField_array = getLangstringsArray('show_defaultTextDBField_array', $displayLang);
        
        /*$show_defaultTextDBField[1] = "Ange fält";
        $show_defaultTextDBField[2] = "Addera";*/
        
        $replaceTable = getReplaceTable();
        
        foreach ($displayLang as $key => $value)
        {
            $order[] = "WHEN lang = '".$value."' THEN ".$key;
        }
        
        $table = "`".PREFIX_K."dokumenti_default_field_text`";
        $table2 = "`".PREFIX_K."dokumenti_default_field_text_lang`";
        
        $sql = "SELECT * FROM (SELECT node.*, CAST(AES_DECRYPT(node.note, SHA2('".$phrase_k."', 512)) AS CHAR) as note, COUNT(parent.lft) - 1 as depth
            FROM ".$table." AS node,
                    ".$table." AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
                    GROUP BY node.lft
            ORDER BY node.lft) AS table1 LEFT OUTER JOIN (SELECT * FROM (SELECT * FROM (SELECT fieldTextId, CAST(AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) AS CHAR) as lang, CAST(AES_DECRYPT(note, SHA2('".$phrase_k."', 512)) AS CHAR) as note FROM ".$table2.") as p ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END LIMIT 18446744073709551615 ) as q GROUP BY fieldTextId) as table2 ON table1.fieldTextId = table2.fieldTextId;";
        //echo $sql."<br>";
        
        $sql = "SELECT node.*, CAST(AES_DECRYPT(node.note, SHA2('".$phrase_k."', 512)) AS CHAR) as note, COUNT(parent.lft) - 1 as depth
            FROM ".$table." AS node,
                    ".$table." AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
                    GROUP BY node.lft
            ORDER BY node.lft";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
        
        echo "<div class = \"row\">";
            echo "<div class = \"col-md-3\" style = \"max-height : 31vh; overflow : auto;\">";
                echo "<div id = \"tree_".$replaceTable[PREFIX_K."dokumenti_default_field_text"]."\" class =\"fancyTreeClass\" data-ajax_target = \"ajaxDocumentiTextTree\" data-replace_table = \"".$replaceTable[PREFIX_K."dokumenti_default_field_text"]."\">";
                    echo "<ul id = \"tree_".$replaceTable[PREFIX_K."dokumenti_default_field_text"]."-data\" style = \"display:none;\" >";
                        $oldDepth = 0;

                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                        {

                            if ($oldDepth > (int)$row['depth'])
                            {
                                for ($i = 0; $i < ($oldDepth - (int)$row['depth']); $i++)
                                {
                                    echo "</ul></li>";
                                }

                            }
                            if (((int)$row['lft'] +1) < (int)$row['rgt'])
                            {
                                echo "<li class =\"folder";
                                if ((int)$row['depth'] <= 1 )
                                {
                                    echo " "."expanded";
                                }
                                echo "\" id = \"".$row['tableKey']."\">";
                                echo $row['note'];
                                echo "<ul>";

                            }
                            else
                            {
                                echo "<li  id = \"".$row['tableKey']."\">".$row['note']."</li>";
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

                echo "<form id = addForm_".$replaceTable[PREFIX_K."dokumenti_default_field_text"].">";
                    echo $show_defaultTextDBField[1]."<br>";
                    echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";
                    echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX_K."dokumenti_default_field_text"]."\" data-replace_table = \"".$replaceTable[PREFIX_K."dokumenti_default_field_text"]."\">".$show_defaultTextDBField[2]."</button>";
                echo "</form>";

            echo "</div>";
            echo "<div id = \"ajaxDocumentiTextTree\" class = \"col-md-8\" style = \"max-height : 31vh; overflow: auto;\">";

            echo "</div>";
        echo "</div>";
    }

	function show_projects()
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
		$show_projects = $langStrings['show_projects'];

		$show_projects_array = getLangstringsArray('show_projects_array', $displayLang);
		
		//$replaceTable = getReplaceTable();
        
        /*$show_projects[1] = "Grundinställningar nya projekt";
        $show_projects[2] = "Standardtexter för fält i databas";*/
        
        $accordion['defaultType'] = $show_projects[1];
        $accordion['defaultTextDBField'] = $show_projects[2];
        
		$presecltAccordian = "defaultTextDBField";
        
        if (empty($presecltAccordian))
        {
            $presecltAccordian = key(reset($accordion));   
        }        
        
		$table = "`".PREFIX_K."projects`";
		$table2 = "`".PREFIX_K."projects_lang`";
		
        echo "<div class=\"accordion\" id=\"accordionExample\">";
            echo "<div class=\"card\">";
        
                foreach ($accordion as $key => $value)
                {
                    echo "<div class=\"card-header\" id=\"heading".$key."\">";
                        echo "<h2 class=\"mb-0\">";
                            echo "<button class=\"btn btn-link btn-block text-left\" type=\"button\" data-toggle=\"collapse\" data-target=\"#collapse".$key."\" aria-expanded=\"";
                                if ($key === $presecltAccordian)
                                {
                                    echo "true";
                                }
                                else
                                {
                                    echo "false";
                                }
                            echo "\" aria-controls=\"collapse".$key."\">";
                                echo $value;
                            echo "</button>";
                        echo "</h2>";
                    echo "</div>";

                    echo "<div id=\"collapse".$key."\" class=\"collapse";
                        if ($key === $presecltAccordian)
                        {
                            echo " "."show";
                        }
                    echo "\" aria-labelledby=\"heading".$key."\" data-parent=\"#accordionExample\">";
                        echo "<div class=\"card-body\" style = \"max-height : 32vh; min-height : 32vh; overflow: auto;\">";
                            call_user_func("show_".$key);
                        echo "</div>";
                    echo "</div>";
                }
                
            echo "</div>";
        echo "</div>";            
	}

	function show_account_users()
	{
		global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
		
		echo __FUNCTION__." "."<br>";
	}

	function show_account_groups()
	{
		global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
		
		echo __FUNCTION__." "."<br>";
	}

	function show_account_pren()
	{
		global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
		
		echo __FUNCTION__." "."<br><br>";
		
		echo "Ja, vilka möjligheter ska vi kunna erbjuda betalande användare? Kunna dokumentera kodprojekt och ansluta grupper av användare till projekten på sidan? Spåna på.... <br><br>Jag som användare av denna tjänst skulle inte vara bered på att betala för att marknadsföra mig själv. Utan jag vill mer ha möjligheten att markandsföra mig lite djupare för poteniella arbetsgivare osv på ett enkelt sätt....";
	}

    function addNewCooperation()
    {
        global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
        
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
		$show_account_cooperation = $langStrings['show_account_cooperation'];

		$show_account_cooperation_array = getLangstringsArray('show_account_cooperation_array', $displayLang);
        
		echo "<p>".$show_account_cooperation[1]."</p>";
        
        echo "<form id = \"show_account_cooperation\">";
            echo "<div class=\"form-group row\">";
                echo "<label for=\"accountName\" class=\"col-sm-2 col-form-label\">".$show_account_cooperation[2]."</label>";
                
                echo "<div class=\"col-sm-5\">";
                    echo "<input type=\"text\" class=\"form-control\" id=\"accountName\">";
                echo "</div>";
            echo "</div>";
        
            echo "<div id = \"addUserRow_master\">";
                
                echo "<hr>";
        
                echo "<div class=\"form-group row\" >";
                    echo "<label for=\"firstName[1]\" class=\"col-sm-2 col-form-label\">".$show_account_cooperation[4]."</label>";

                    echo "<div class=\"col-sm-5\">";
                        echo "<input type=\"text\" class=\"form-control\" id=\"firstName[1]\">";
                    echo "</div>";
        
                    echo "<div class=\"col-sm-1\" id = \"removeUser[1]\" style = \"display : none;\">";
                        echo "<button id = removeUser[1] class =\"btn btn-secondary\"><i class=\"far fa-minus-square\"></i></button>";
                    echo "</div>";
                echo "</div>";
        
                echo "<div class=\"form-group row\" >";
                    echo "<label for=\"sureName[1]\" class=\"col-sm-2 col-form-label\">".$show_account_cooperation[5]."</label>";

                    echo "<div class=\"col-sm-5\">";
                        echo "<input type=\"text\" class=\"form-control\" id=\"sureName[1]\">";
                    echo "</div>";
                echo "</div>";
        
                echo "<div class=\"form-group row\" >";
                    echo "<label for=\"email[1]\" class=\"col-sm-2 col-form-label\">".$show_account_cooperation[3]."</label>";

                    echo "<div class=\"col-sm-5\">";
                        echo "<input type=\"text\" class=\"form-control\" id=\"email[1]\">";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        
            
        
            echo "<div class=\"form-group row\">";
                echo "<label for=\"addBtn\" class=\"col-sm-2 col-form-label\">&nbsp;</label>";
                
                echo "<div class=\"col-sm-5\">";
                    echo "<button id = \"addUserCooperation\" class =\"btn btn-secondary\">".$show_account_cooperation[6]."</button>";
                echo "</div>";
            echo "</div>";
        
            echo "<div class=\"form-group row\">";
                echo "<label for=\"addBtn\" class=\"col-sm-2 col-form-label\">&nbsp;</label>";
                
                echo "<div class=\"col-sm-5\">";
                    echo "<button id = \"createCooperation\" class =\"btn btn-secondary\" data-replace_table = \"".$replaceTable[PREFIX."account"]."\" data-reload_div = \"area_cooperation\">".$show_account_cooperation[7]."</button>";
                echo "</div>";
            echo "</div>";
        
            echo "<div id = \"newUserCooperation\">";
                echo "";
            echo "</div>";
        
        echo "</form>";
        
        echo "<input type = \"hidden\" id = \"confirmMessage\" value = \"".$show_account_cooperation[9]."\">";
    //echo "</div>";    
    }

    function editCooperation($projectKey = null)
    {
        global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
        
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

		foreach ($displayLang as $key => $value)
		{
			$order[] = "WHEN lang = '".$value."' THEN ".$i;
			$order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
			$i++;
		}
		
		$langStrings = getlangstrings();
		
        $editCooperation = $langStrings['editCooperation'];

		$editCooperation_array = getLangstringsArray('editCooperation_array', $displayLang);
        
        $show_account_cooperation = $langStrings['show_account_cooperation'];

		$show_account_cooperation_array = getLangstringsArray('show_account_cooperation_array', $displayLang);
        
        $table10 = "`".PREFIX."account`";
        $table20 = "`".PREFIX."user2account`";
        $table30 = "`".PREFIX."user`";
        
        $sql = "SELECT *, CAST(AES_DECRYPT(t30.firstName, SHA2('".$phrase."', 512)) AS CHAR) as firstName, CAST(AES_DECRYPT(t30.sureName, SHA2('".$phrase."', 512)) AS CHAR) as sureName FROM ".$table10." t10 INNER JOIN ".$table20." t20 ON t20.accountId = t10.autoId INNER JOIN ".$table30." t30 ON t30.autoId = t20.userId WHERE t10.replaceKey = '".$projectKey."' AND typeAccount = 'collaboration' ORDER BY t10.autoId";
        //echo $sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $tools['contact'] = $editCooperation[4];
        $tools['delete'] = $editCooperation[5];
        
        if (mysqli_num_rows($result) > 0)
        {
            echo "<table id=\"table_".$projectKey."\" class=\"table table-striped table-bordered DataTable\" style=\"width:100%\">";
                echo "<thead>";
                    echo "<tr>";
                        echo "<th>".$editCooperation[1]."</th>";
                        echo "<th>".$editCooperation[2]."</th>";
                        echo "<th>".$editCooperation[3]."</th>";
                    echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
            
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                    echo "<tr>";
                        echo "<td>";
                            echo $row['firstName'];
                        echo "</td>";
                    
                        echo "<td>";
                            echo $row['sureName'];
                        echo "</td>";
                    
                        echo "<td>";
                            echo "<div class=\"btn-group\">";
                                echo "<button type=\"button\" class=\"btn btn-secondary contactUser\" id = \"".key($tools)."[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."account"]."\" data-project_key = \"".$projectKey."\">".reset($tools)."</button>";
                                echo "<button type=\"button\" class=\"btn btn-secondary dropdown-toggle dropdown-toggle-split\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
                                echo "<span class=\"sr-only\">Toggle Dropdown</span>";
                                echo "</button>";
                                echo "<div class=\"dropdown-menu\">";
                                foreach ($tools as $key => $value)
                                {
                                    echo "<a class=\"dropdown-item";
                                        if ($key === "delete")
                                        {
                                            echo " "."deleteUser";
                                        }
                                        else if ($key === "contact")
                                        {
                                            echo " "."contactUser";
                                        }
                                    echo "\" id = \"".$key."[".$row['tableKey']."]\" href=\"#\" data-replace_table = \"".$replaceTable[PREFIX."account"]."\" data-project_key = \"".$projectKey."\"";
                                    if ($key == "delete")
                                    {
                                        echo " " ."data-reload_div = \"editCooperation_".$projectKey."\"";
                                    }
                                    
                                    echo ">".$value."</a>";
                                }
                                echo "</div>";
                            echo "</div>";
                        echo "</td>";
                    echo "</tr>";

                }
            echo "</table>";
            
            echo "<form id = \"add_user_cooperation[".$projectKey."]\">";
  /*              echo "<div class=\"form-group row\">";
                    echo "<label for=\"accountName\" class=\"col-sm-2 col-form-label\">".$show_account_cooperation[2]."</label>";

                    echo "<div class=\"col-sm-5\">";
                        echo "<input type=\"text\" class=\"form-control\" id=\"accountName\">";
                    echo "</div>";
                echo "</div>";
*/
                echo "<div id = \"addUserRow_master_".$projectKey."\">";

                    echo "<hr>";

                    echo "<div class=\"form-group row\" >";
                        echo "<label for=\"firstName[1]\" class=\"col-sm-2 col-form-label\">".$show_account_cooperation[4]."</label>";

                        echo "<div class=\"col-sm-5\">";
                            echo "<input type=\"text\" class=\"form-control\" id=\"firstName[1]\">";
                        echo "</div>";

                        echo "<div class=\"col-sm-1\" id = \"removeUser[1]\" style = \"display : none;\">";
                            echo "<button id = removeUser[1] class =\"btn btn-secondary\"><i class=\"far fa-minus-square\"></i></button>";
                        echo "</div>";
                    echo "</div>";

                    echo "<div class=\"form-group row\" >";
                        echo "<label for=\"sureName[1]\" class=\"col-sm-2 col-form-label\">".$show_account_cooperation[5]."</label>";

                        echo "<div class=\"col-sm-5\">";
                            echo "<input type=\"text\" class=\"form-control\" id=\"sureName[1]\">";
                        echo "</div>";
                    echo "</div>";

                    echo "<div class=\"form-group row\" >";
                        echo "<label for=\"email[1]\" class=\"col-sm-2 col-form-label\">".$show_account_cooperation[3]."</label>";

                        echo "<div class=\"col-sm-5\">";
                            echo "<input type=\"text\" class=\"form-control\" id=\"email[1]\">";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";



                echo "<div class=\"form-group row\">";
                    echo "<label for=\"addBtn\" class=\"col-sm-2 col-form-label\">&nbsp;</label>";

                    echo "<div class=\"col-sm-5\">";
                        echo "<button id = \"addUserCooperation_".$projectKey."\" class =\"btn btn-secondary addUserCooperation\" data-target_form = \"add_user_cooperation[".$projectKey."]\" data-target_div = \"newUserCooperation_".$projectKey."\">".$show_account_cooperation[6]."</button>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"form-group row\">";
                    echo "<label for=\"addBtn\" class=\"col-sm-2 col-form-label\">&nbsp;</label>";

                    echo "<div class=\"col-sm-5\">";
                        echo "<button id = \"addUserCooperation_".$projectKey."\" class =\"btn btn-secondary addUsers2Cooperation\" data-replace_table = \"".$replaceTable[PREFIX."account"]."\" data-reload_div = \"editCooperation_".$projectKey."\">".$editCooperation[6]."</button>";
                    echo "</div>";
                echo "</div>";

                echo "<div id = \"newUserCooperation_".$projectKey."\">";
                    echo "";
                echo "</div>";

            echo "</form>";
        }
        
    }

    function show_account_cooperation()
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
		
        $show_account_cooperation = $langStrings['show_account_cooperation'];

		$show_account_cooperation_array = getLangstringsArray('show_account_cooperation_array', $displayLang);
        
        $table10 = "`".PREFIX."account`";
        $table20 = "`".PREFIX."user2account`";
        
        $sql = "SELECT *, CAST(AES_DECRYPT(accountName, SHA2('".$phrase."', 512)) AS CHAR) as accountName FROM ".$table10." t10 WHERE t10.autoId = '".mysqli_real_escape_string($link, $_SESSION['accountId'])."' AND typeAccount = 'collaboration'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        if (mysqli_num_rows($result) === 0)
        {
            $navTabsCooperation['addNewCooperation'] = $show_account_cooperation[8];
        }
        
        $sql = "SELECT *, CAST(AES_DECRYPT(accountName, SHA2('".$phrase."', 512)) AS CHAR) as accountName FROM ".$table10." t10 INNER JOIN ".$table20." t20 ON t20.accountId = t10.autoId  WHERE t20.accountId = '".mysqli_real_escape_string($link, $_SESSION['accountId'])."' OR userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."' AND typeAccount = 'collaboration' HAVING accountName IS NOT NULL ORDER BY t10.autoId ";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $navTabsCooperation['editCooperation_'.$row['replaceKey']] = $row['accountName'];
        }
        
		echo "<ul class=\"nav nav-tabs\" id=\"navTabsCooperation\" role=\"tablist\">";
           
                $first = true;
                foreach ($navTabsCooperation as $key => $value)
                {
                    echo "<li class=\"nav-item\" role=\"presentation\">";
                        echo "<a class=\"nav-link";
                            if ($first)
                            {
                                echo " "."active";
                            }

                        echo "\" id=\"".$key."-tab\" data-toggle=\"tab\" href=\"#".$key."\" role=\"tab\" aria-controls=\"".$key."\" aria-selected=\"";
                            if ($first)
                            {
                                $first = false;
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
        
        echo "<div class=\"tab-content\" id=\"myTabContentCooperation\">";
            
            $first = true;
        
            foreach ($navTabsCooperation as $key => $value)
            {
                echo "<div class=\"tab-pane fade";
                    if ($first)
                    {
                        echo " "."show active";
                        $first = false;
                    }
                echo "\" id=\"".$key."\" role=\"tabpanel\" aria-labelledby=\"".$key."-tab\">";
                    echo "<br>";
                    if (strpos($key,"_") !== false)
                    {
                        $temp = array_map("trim", explode("_", $key));
                        
                        call_user_func($temp[0], $temp[1]);  
                    }
                    else
                    {
                        call_user_func($key);  
                    }
                echo "</div>";
            }
            
        echo "</div>";
    //echo "</div>";    
	}

	function show_account()
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
		
        $lang = reset($displayLang);
        
		$langStrings = getlangstrings();
		$show_account = $langStrings['show_account'];

		$show_account_array = getLangstringsArray('show_accountarray', $displayLang);
		
		$table = "`".PREFIX_K."projects`";
		$table2 = "`".PREFIX_K."projects_lang`";
		
		/*$show_account[1]['sv'] = "Användare";
		$show_account[2]['sv'] = "Grupper";
		$show_account[3]['sv'] = "Prenumation";*/
		
        $table10 = "`".PREFIX."account`";
        $table11 = "`".PREFIX."account_plans`";
        
        $sql = "SELECT * FROM ".$table10." t10 INNER JOIN ".$table11." t11 ON t10.accountPlan = t11.accountPlanId WHERE t10.autoId = '".mysqli_real_escape_string($link, $_SESSION['accountId'])."'";
        //echo $sql."<br>";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while ($row = mysqli_fetch_array($result))
        {
            $extra = $row['extra'];
        }
        
        if (/*$extra == "cooperation" ||*/ $extra == "business")
        {
            $account_properties['users'] = $show_account[1];
            $account_properties['groups'] = $show_account[2];
        }
        else if ($extra == "private" || $extra == "cooperation" )
        {
            $account_properties['cooperation'] = $show_account[4];
        }
		
		$account_properties['pren'] = $show_account[3];
		
		//echo "Detta är bara en förberedelse inför framtiden, här ska kunden ställa in möjlighet att uppgradera konto. Ange grupper av användare som de ska ha tillgång till osv..."."<br>";
		echo "<div class = \"row\">";
			echo "<div class=\"col-md-2\">";
				echo "<div class=\"nav flex-column nav-pills\" id=\"v-pills-tab\" role=\"tablist\" aria-orientation=\"vertical\">";

					$first = true;

					foreach ($account_properties as $key => $value)
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
					//echo "</div>";
				echo "</div>";
			echo "</div>";
			echo "<div class=\"col-md-10\" id = \"area_cooperation\">";
				echo "<div class=\"tab-content\" id=\"v-pills-tabContent\">";

					$first = true;
					foreach ($account_properties as $key => $value)
					{
						echo "<div class=\"tab-pane fade";
                            if ($first)
                            {
                                $first = false;
                                echo " "."show active";
                            }
                            echo "\" id=\"v-pills-".$key."\" role=\"tabpanel\" aria-labelledby=\"v-pills-".$key."-tab\">";
							call_user_func("show_account_".$key);
						echo "</div>";
					}
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}

    
	function showTabs()
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
		$showTabs = $langStrings['showTabs'];

		$showTabs_array = getLangstringsArray('showTabs_array', $displayLang);
        
		/*$account[1]['sv'] = "Projekt";
		$account[2]['sv'] = "Konto";*/
		
		$navTabs['projects'] = $showTabs[1];
		$navTabs['account'] = $showTabs[2];
        
        $table = "`".PREFIX."user`";
        
		$first = true;
		
		echo "<ul class=\"nav nav-tabs\" id=\"myTabAccount\" role=\"tablist\">";
			foreach ($navTabs as $key => $value)
			{
			     echo "<li class=\"nav-item\" role=\"presentation\">";
                        echo "<a class=\"nav-link";
                        if ($first)
                        {
                            echo " "."active";
                        }
                        echo "\" "."id=\"".$key."-tab\" data-toggle=\"tab\" href=\"#".$key."\" role=\"tab\" aria-controls=\"".$key."\" aria-selected=\"";
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
		
		$first = true;
		echo "<div class=\"tab-content\" id=\"myTabContent\" style = \"max-height : 59vh; min-height : 59vh; overflow: auto;\">";
			foreach ($navTabs as $key => $value)
			{
				echo "<div class=\"tab-pane fade";
				if ($first)
				{
					echo "show active";
					$first = false;
				}
				echo "\" id=\"".$key."\" role=\"tabpanel\" aria-labelledby=\"".$value."-tab\">";
				echo "<br>";
                    //echo __LINE__." ".$key."<br>";
					call_user_func("show_".$key);
				echo "</div>";
			}
		echo "</div>";
	}


	function showEditAccount()
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

		foreach ($displayLang as $key => $value)
		{
			$order[] = "WHEN lang = '".$value."' THEN ".$i;
			$order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
			$i++;
		}
		
		$langStrings = getlangstrings();
		$showEditAccount = $langStrings['showEditAccount'];

		$showEditAccount_array = getLangstringsArray('showEditAccount_array', $displayLang);

		echo "<div class=\"panel-header panel-header-sm\">";
		
		echo "</div>";
		
		echo "<div class=\"content\" style = \"max-height : 50vh; height : 50vh;\">";
			echo "<div class=\"row\">";
				echo "<div class=\"col-md-12\" >";
					echo "<div class=\"card\">";
						echo "<div class=\"card-header\">";
							echo "<h1>".$showEditAccount[1]."</h1>";
					echo "</div>";
					echo "<div class=\"card-body\">";
						echo "<div class=\"row\">";
							echo "<div class=\"col-md-12\">";
								showTabs();
							echo "</div>";
						echo "</div>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        
        
        printHeader();
        displayMenuAdministradoHeader();

        showEditAccount();

        displayFooterAdministrado();
        printScripts();
        
        print_modal_xl();
    }
?>