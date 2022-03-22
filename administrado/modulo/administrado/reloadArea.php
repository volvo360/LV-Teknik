<?php
    session_start();

    error_reporting(E_ALL);

    include_once("../../../common/db.php");
    include_once("../../../common/crypto.php");
    include_once("../../../common/db_def.php");
    include_once("../../../common/userData.php");
    include_once("./../../ext/theme/nav.php");
    include_once("../../common/emptor.php");

    include_once("./../../common/syncDB.php");

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        $replaceTable = getReplaceTable(false);
        
        if ($replaceTable[$_POST['replaceTable']] === PREFIX."account" && isset($_POST['refreshDiv']))
        {
            //include_once("../../common/resyncTree.php");
            reloadCooperationDiv_users();
        }
        else if ($replaceTable[$_POST['replaceTable']] === PREFIX_K."dokumenti_default_field_text")
        {
            //include_once("../../common/resyncTree.php");
            reloadDokumenti_default_field_text();
        } 
    }

    function reloadCooperationDiv_users()
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
        
        $afterunderscore = substr($_POST['refreshDiv'], strpos($_POST['refreshDiv'], "_") + 1);
        
        $table10 = "`".PREFIX."account`";
        $table20 = "`".PREFIX."user2account`";
        $table30 = "`".PREFIX."user`";
        
        $sql = "SELECT *, CAST(AES_DECRYPT(t30.firstName, SHA2('".$phrase."', 512)) AS CHAR) as firstName, CAST(AES_DECRYPT(t30.sureName, SHA2('".$phrase."', 512)) AS CHAR) as sureName FROM ".$table10." t10 INNER JOIN ".$table20." t20 ON t20.accountId = t10.autoId INNER JOIN ".$table30." t30 ON t30.autoId = t20.userId WHERE t10.replaceKey = '".$afterunderscore."' AND typeAccount = 'collaboration' ORDER BY t10.autoId";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        $tools['contact'] = $editCooperation[4];
        $tools['delete'] = $editCooperation[5];
        
        if (mysqli_num_rows($result) > 0)
        {
            echo "<table id=\"table_".$$afterunderscore."\" class=\"table table-striped table-bordered DataTable\" style=\"width:100%\">";
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
                                echo "<button type=\"button\" class=\"btn btn-secondary contactUser\" id = \"".key($tools)."[".$row['tableKey']."]\">".reset($tools)."</button>";
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
        }
    }

    function reloadDokumenti_default_field_text()
    {
        global $link;
        global $link_k;
        
        global $phrase;
		global $phrase_k;
        
        include_once("../../common/resyncTree.php");
        
        $replaceTable = getReplaceTable(false);
        $replaceLang = getReplaceLang(false);
        
		$table = "`".$replaceTable[$_POST['replaceTable']]."`";
        $table2 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
        
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

        $order[] = "WHEN lang = '".$replaceLang[$_POST['lang_code']]."' THEN -1";
        
        foreach ($displayLang as $key => $value)
        {
            $order[] = "WHEN lang = '".$value."' THEN ".$i;
            $order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
            $i++;
        }
        
        $sql = "SELECT node.tableKey, node.fieldTextId, CAST(AES_DECRYPT(node.note, SHA2('".$phrase_k."', 512)) AS CHAR) as note , (COUNT(parent.lft) - 1) AS depth
                FROM ".$table." AS node,
                        ".$table." AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                GROUP BY node.lft
                ORDER BY node.lft;";

        $tree = retriveTree($sql, "customer", "fieldTextId");

        renderTree($tree, $_POST['replaceTable']);
    }
?>