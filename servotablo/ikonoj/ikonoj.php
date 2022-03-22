<?php
	session_start();
	include_once("../../common/db.php");
    include_once("../../common/crypto.php");
	include_once("../../common/userData.php");
	include_once("../../administrado/ext/theme/nav.php");
	

    function renderIcons()
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
		$renderIcons = $langStrings['renderIcons'];

		$renderIcons_array = getLangstringsArray('renderIcons_array', $displayLang);
		
		$table = "`".PREFIX."icons`";
		$table2 = "`".PREFIX."icon_types`";
		
		$lang = 'sv';
		
		$replaceTable = getReplaceTable();
		
		$sql = "SELECT * FROM (SELECT node.tableKey, node.lft, node.rgt, CAST(AES_DECRYPT(node.note, SHA2('".$phrase."', 512)) AS CHAR) as note, (COUNT(parent.lft) - 1) AS depth
				  FROM $table AS node,
						  $table AS parent
				  WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
				  ORDER BY node.lft LIMIT 18446744073709551615) as socailMedia";
		//echo $sql."<br>";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		echo "<div class = \"row\">";
			echo "<div class =\"col-md-2\" style=\"max-height : 64vh; height : 64vh; overflow : auto\">";
				echo "<div id = \"tree_".$replaceTable[PREFIX.'icons']."\" class =\"fancyTreeClass\" data-ajax_target = \"ajaxSocial_media\" data-replace_table = \"".$replaceTable[PREFIX.'icons']."\">";
					echo "<ul id = \"tree_".$replaceTable[PREFIX.'icons']."-data\" style = \"display:none;\" >";
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
								echo "<li class = \"folder expanded\" id = \"".$row['tableKey']."\">".$row['note']."<ul>";
							}
							else
							{
								echo "<li id = \"".$row['tableKey']."\">".$row['note']."</li>";
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
		
				echo "<form id = addForm_".$replaceTable[PREFIX.'icons'].">";
					echo $renderIcons[1]."<br>";
					echo "<input type = \"text\" id = \"note\" class = \"form-control\"><br>";
					/*echo "<div class=\"form-check checkbox-slider--b\">";
						echo "<label>";
							echo "<input type=\"checkbox\" checked><span title = \"".$render_customer[4]."\">".$render_customer[3]."</span>";
						echo "</label>";
					echo "</div>";*/
                    $sql = "SELECT * FROM (SELECT node.tableKey, node.lft, node.rgt, CAST(AES_DECRYPT(node.note, SHA2('".$phrase."', 512)) AS CHAR) as note, (COUNT(parent.lft) - 1) AS depth
                              FROM $table2 AS node,
                                      $table2 AS parent
                              WHERE node.lft BETWEEN parent.lft AND parent.rgt group by node.lft
                              ORDER BY node.lft LIMIT 18446744073709551615) as iconType";
                    //echo __LINE__." ".$sql."<br>";
                    $result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                    while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
					{
                        $collection[$row2['tableKey']] = $row2['note'];
                    }
                    
                    if (count($collection) <= 1)
                    {
                        echo "<input type = \"hidden\" id = \"icontype\" value = \"".key($collection)."\">";
                    }
                    else
                    {    
                        echo "<select id = \"icontype\" class = \"selectpicker2 form-control\">";
                            foreach ($collection as $key => $value)
                            {
                                echo "<option value = \"".$key."\">".$value."</option>";
                            }
                        echo "</select>";
                    }
                    
                    echo "<button type = \"button\" class = \"btn btn-secondary addToTree\" data-target_tree = \"tree_".$replaceTable[PREFIX.'icons']."\" data-replace_table = \"".$replaceTable[PREFIX."icons"]."\">".$renderIcons[2]."</button>";
				echo "</form>";
			echo "</div>";
		
			echo "<div class = \"col-md-10\" id = \"ajaxSocial_media\">";
		
			echo "</div>";
		echo "</div>";
    }


	function displayIkonoj()
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
		$displayIkonoj = $langStrings['displayIkonoj'];

		$displayIkonoj_array = getLangstringsArray('displayIkonoj_array', $displayLang);
        
		echo "<div class=\"panel-header panel-header-sm\">";
		
		echo "</div>";
		
        echo "<div class=\"content\" style = \"max-height : 43vh; height : 43vh;\">";
			echo "<div class=\"row\" >";
				echo "<div class=\"col-md-12\" >";
					echo "<div class=\"card\">";
						echo "<div class=\"card-header\">";
							echo "<h1>".$displayIkonoj[1]."</h1>";
					echo "</div>";
					echo "<div class=\"card-body\">";
						echo "<div class=\"row\">";
							echo "<div class=\"col-md-12\">";
								renderIcons();
							echo "</div>";
						echo "</div>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}

	printHeader();
	displayMenuAdministradoHeader();
	displayIkonoj();
	displayFooterAdministrado();
	printScripts();
?>