<?php
	session_start();

	include_once("db.php");
     include_once("../../common/crypto.php");
    include_once("../../common/userData.php");
	include_once("./../ext/theme/nav.php");
	

    function syncTableInfo($table = null)
    {
        $sql = "SHOW COLUMNS FROM ".$table." LIKE 'type';"; 
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        if (mysqli_num_rows($result) > 0)
        {
            $sql = "SELECT * FROM ".$table." WHERE type = 'deleted' ORDER BY lft LIMIT 1";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $lft = $row['lft'];
                $rgt = $row['rgt'];
            }
            
            $sql = "SELECT * FROM ".$table." WHERE type = 'displayMenu' ORDER BY lft LIMIT 1";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            
            if (mysqli_num_rows($result) > 0)
            {
                $sql = "UPDATE ".$table." SET type = 'deleted', displayMenu = 0 WHERE lft > '".$lft."' AND rgt < '".$rgt."'";
                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

                $sql = "UPDATE ".$table." SET type = NULL, displayMenu = 1 WHERE type = 'deleted' AND lft < '".$lft."' OR lft > '".$rgt."'";
                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            }
            else
            {
                $sql = "UPDATE ".$table." SET type = 'deleted' WHERE lft > '".$lft."' AND rgt < '".$rgt."'";
                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

                $sql = "UPDATE ".$table." SET type = NULL WHERE type = 'deleted' AND lft < '".$lft."' OR lft > '".$rgt."'";
                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            }
        }
    }

    function syncFieldOwnPage()
    {
        global $link;

        global $phrase;

        $replaceTable = getReplaceTable(false);

        $table1 = $table10 = "`".PREFIX."menu`";;

        $table10 = "`".PREFIX."own_pages`";

        $sql = "SELECT * FROM ".$table1;
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $data[$row['tableKey']] = $row;
        }

        $sql = "SELECT * FROM ".$table10;
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $ownPageData[$row['tableKey']] = $row;
        }

        $insertKey = array_diff_key($data, $ownPageData);

        if (empty($ownPageData))
        {
            $insertKey = $ownPageData;
        }

        foreach ($insertKey as $key => $value)
        {
            $sql = "INSERT INTO ".$table10." (tableKey, pageId, lft, rgt) VALUES ('".$data[$key]['tableKey']."', '".$data[$key]['menuId']."', '".$data[$key]['lft']."', '".$data[$key]['rgt']."' )";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        } 
        
        foreach ($data as $key => $value)
        {
            $sql = "UPDATE ".$table10." SET lft = ".$data[$key]['lft'].", rgt = ".$data[$key]['rgt']." WHERE tableKey = '".$key."'";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        }
    }

	function extract_tree_data_sub($tree2, &$curr_node, &$pos, &$cur_expanded_node)
	{
		global $folder_id;
		
		if (is_array($tree2))
		{
			foreach ($tree2 as $b => $a)
			{
				$tree_data[$curr_node]['key'] = $a['key'];
				$tree_data[$curr_node]['folder'] = 0;
		
				if ($a['selected'] === "false" || !($a['selected']))
				{
					$tree_data[$curr_node]['selected'] = 0;
				}
				else
				{
					$tree_data[$curr_node]['selected'] = 1;
				}
				
				if (isset($a['folder']))
				{
					if ($a['folder'] === "true")
					{
						$tree_data[$curr_node]['folder'] = 1;
					}
				}
				$tree_data[$curr_node]['title'] = trim($a['title']);
				$tree_data[$curr_node]['nleft'] = $pos;

				$pos++;
				if (is_array($a['children']))
				{
					$old_node = $curr_node;
					$curr_node++;	
					
					$extra_tree_data = extract_tree_data_sub($a['children'], $curr_node, $pos, $cur_expanded_node);
					
					$temp_tree = $tree_data;
					$tree_data = $temp_tree + $extra_tree_data;
					$tree_data[$old_node]['nright'] = $pos;	
				}
				else
				{
					$tree_data[$curr_node]['nright'] = $pos;	
					$curr_node++;	
				}
				$pos++;
			}	
			return $tree_data;
		}
		
	}

	function extract_tree_data($tree3, &$curr_node, &$pos, &$cur_expanded_node)
	{
		global $folder_id;
		
		foreach ($tree3 as $b => $a)
		{
			$re1='(root)';	# Word 1
			//if ($c=preg_match_all ("/".$re1."/is", $a['key'], $matches))
			{
				if (is_array($a))
				{
					//Vi är nere i vårt "träd" med den data vi söker, vi får med lite "skräp" när vi hämtar ut datan från fancytree
					
					$old_node = $curr_node;
					$curr_node++;	
					$extra_tree_data = extract_tree_data_sub($a, $curr_node, $pos, $cur_expanded_node);
					
					return $extra_tree_data;
				}
			}
		}

		return $tree_data;
	}

	
	function insertIntoTable($table1 = null, $table2 = null, $mode = 'default', $treeData2 = null)
	{
		global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
        
        if (empty($treeData2))
        {
            return false;
        }

		$sql = "SELECT tableKey FROM ".$table1;

		if ($mode == 'default')
		{
			$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
		}
		else if ($mode == 'customer')
		{
			$result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
		}


		if (mysqli_num_rows($result) > 0)
		{
			while ($row = mysqli_fetch_array($result))
			{
				$oldArray[$row['tableKey']] = $row['tableKey'];
			}
		}

		$sql = "UPDATE ".$table1 ." SET lft = NULL, rgt = NULL";

		if ($mode == 'default')
		{
			$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
		}
		else if ($mode == 'customer')
		{
			$result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
		}

		

		foreach ($treeData2 as $key => $value)
		{
			if ($mode == 'default')
			{
				$sql = "UPDATE ".$table1 ." SET lft = '".mysqli_real_escape_string($link, $value['nleft'])."', rgt = '".mysqli_real_escape_string($link,$value['nright'])."' WHERE tableKey = '".mysqli_real_escape_string($link, $value['key'])."'";
				$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
			}
			else if ($mode == 'customer')
			{
				$sql = "UPDATE ".$table1 ." SET lft = '".mysqli_real_escape_string($link_k, $value['nleft'])."', rgt = '".mysqli_real_escape_string($link_k,$value['nright'])."' WHERE tableKey = '".mysqli_real_escape_string($link_k, $value['key'])."'";
                //echo __LINE__." ".$sql."<br>";
				$result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
			}
			$newArray[$value['key']] = $value['key'];
		}

		$delArray = array_diff($oldArray, $newArray);

		if (count($delArray) > 0)
		{
			foreach ($delArray as $key => $value)
			{
				$sql = "DELETE FROM ".$table1." WHERE tableKey = '".mysqli_real_escape_string($link_c, $value)."'";

				if ($mode == 'default')
				{
					$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
				}
				else if ($mode == 'customer')
				{
					$result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
				}
			}
		}
	}

	$replaceTable = getReplaceTable(false);
	
	echo "<div id = \"debug\">";
		/*print_r($_POST);
		echo "<br><br>";*/
	echo "</div>";	

	global $link_c;
	global $phrase;

	$data = $_POST['tree'];

	$curr_node = 0;
	$pos = 1;
	$cur_expanded_node = 1;

	$tree_data = extract_tree_data($data, $curr_node, $pos, $cur_expanded_node);

    function syncPostTable_headers($data = null)
	{
        global $link;
        global $phrase;
        
		$table1 = PREFIX."headers";
		$table2 = PREFIX."headers_lang";
        
        $table10 = PREFIX."default_collection";
        
        $collectionId = 0;
        
        $lft = 1;
        
        foreach ($data as $key => $value)
        {
            $sql = "SELECT * FROM ".$table1." WHERE tableKey = '".$value['key']."'";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
            
            if (mysqli_num_rows($result) > 0)
            {
                $sql = "UPDATE ".$table1." SET collectionId = '".$collectionId."', lft = '".$value['nleft']."', rgt = '".$value['nright']."' WHERE tableKey = '".$value['key']."'";
                $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
            }
            else
            {
                $sql = "SELECT * FROM ".$table10." WHERE tableKey = '".$value['key']."'";
                $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                if (mysqli_num_rows($result) > 0)
                {
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
                        $collectionId = $row['collectionId'];
                        $lft = $lft+2;
                    }
                }
            }
        }
    }
        
    function syncPostTable_user_settings($tree_data)
    {
        global $link;
        global $phrase;
        
        $replaceTable = getReplaceTable(false);
        $replaceLang = getReplaceLang(false);
        
        $table1 = $replaceTable[$_POST['replaceTable']];
        
        foreach ($tree_data as $key => $value)
        {
            $data[] = $replaceLang[$value['key']];
        }
        
        $sql = "SELECT * FROM ".$table1." WHERE setting = AES_ENCRYPT('langService', SHA2('".$phrase."', 512)) AND userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."'";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
        
        if (mysqli_num_rows($result) > 0)
        {
            $sql = "UPDATE ".$table1." SET data = AES_ENCRYPT('".implode(", ", (array)$data)."', SHA2('".$phrase."', 512)) WHERE setting = AES_ENCRYPT('langService', SHA2('".$phrase."', 512)) AND userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."'";
        }
        else
        {
            $sql = "INSERT INTO ".$table1." (userId, setting, data) VALUES ('".mysqli_real_escape_string($link, $_SESSION['uid'])."', AES_ENCRYPT('langService', SHA2('".$phrase."', 512)),AES_ENCRYPT('".implode(", ", (array)$data)."', SHA2('".$phrase."', 512)))";
        }
        //echo __LINE__." ".$sql."<br>";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
    }

    if ($replaceTable[$_POST['replaceTable']] === PREFIX."account_permission")
	{
		syncPostTable_account_permission($tree_data);
	}
	else if ($replaceTable[$_POST['replaceTable']] === PREFIX."newsletter")
	{
		return false;
	}
    else
    {
        $replaceTable = getReplaceTable(false);
        
        $table1 = $replaceTable[$_POST['replaceTable']];
		$table2 = $replaceTable[$_POST['replaceTable']]."_lang";

		insertIntoTable($table1, $table2, "default", $tree_data);
    }

    syncTableInfo($replaceTable[$_POST['replaceTable']]);
?>