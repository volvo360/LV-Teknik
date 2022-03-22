<?php
	session_start();
    error_reporting(E_ALL);
    
    include_once("./db.php");
    include_once("../../common/userData.php");
    include_once("../../common/crypto.php");
    include_once("./../ext/theme/nav.php");

    function getReplaceLang2($mode = true)
    {
        global $link;
        global $link_k;

        global $phrase;
        global $phrase_k;

        $table = "`".PREFIX."languages`";

        $sql = "SELECT tableKey, code FROM ".$table;

        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            if ($mode)
            {
                $replace[$row['code']] = $row['tableKey'];	
            }
            else
            {
                $replace[$row['tableKey']] = $row['code'];
            }
        }

        return $replace;
    }

function getReplaceTable2($mode = true)
{   
    global $link;
    global $link_k;
	
	//updateReplaceTable();
	if (defined('PREFIX_K'))
	{
		$table = "`".PREFIX_K."replacetable`";

		$sql = "SELECT * FROM ".$table;
        $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link_k));

		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if ($mode)
			{
				$data[$row['replaceTable']] = $row['tableKey'];
			}
			else
			{
				$data[$row['tableKey']] = $row['replaceTable'];
			}
		}
	}
    $table = "`".PREFIX."replacetable`";
    
    $sql = "SELECT * FROM ".$table;
    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql. " : ".mysqli_error ($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        if ($mode)
        {
            $data[$row['replaceTable']] = $row['tableKey'];
        }
        else
        {
            $data[$row['tableKey']] = $row['replaceTable'];
        }
    }
    
    return $data;  
}


	function findConnectionField($table = null, $type = "default")
	{
		global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
		
		$sql = "DESCRIBE ".$table;

        if ($type === "default")
		{
			$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		}
		else if ($type === "customer")
		{
			$result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
		}

		$validInt['TINYINT'] = "TINYINT";
		$validInt['SMALLINT'] = "SMALLINT";
		$validInt['MEDIUMINT'] = "MEDIUMINT";
		$validInt['INT'] = "INT";
		$validInt['BIGINT'] = "BIGINT";

		$block = false;

		while ($row = mysqli_fetch_array($result))
		{
			if ($row['Field'] !== "autoId" && !$block)
			{
				foreach ($validInt as $key => $value)
				{
					if (strpos(strtolower($row['Type']), strtolower($key)) !== false) {
						$block = true;
						$field = $row['Field'];
						return $row['Field'];
					}
				}
			}
		}
		
		return $field;
	}

	function updateTable($table10 = null, $table11 = null, $mode = 'default')
	{
		global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
		
        $replaceLang = getReplaceLang2(false);

        if (isset($_POST['jEditable']))
		{
			$temp = array_map("trim",explode("[", $_POST['id']));
		
			foreach ($temp as $key => &$value)
			{
				$value = trim($value,"]");
			}
			
			// Fix to make sure that data that jEditable sends to this script ends up in the correct format for our script. This sends id and value as two different variables while we want them together
			$temp = array_map("trim",explode("[", $_POST['id']));
			foreach ($temp as $key => &$value)
			{
				$value = trim($value,"]");
			}
			
			$temp2 = explode("_", $temp[1]);
            
			if (array_key_exists(1,(array)$temp2))
			{
				$_POST[$temp[0]][$temp2[1]] = $_POST['value'];
			}
			else
			{
				$_POST[$temp[0]][$temp[1]] = $_POST['value'];
			}
			echo $_POST['value'];
			unset($_POST['id'], $_POST['value'], $_POST['jEditable']);
        }
		/*echo __LINE__." ";
        print_r($_POST);
        echo "<br>";*/
        
		foreach ($_POST as $key => $value)
		{
			if ($key !== "replaceTable" && $key !== "replaceLang")
			{
                $sql = "SHOW COLUMNS FROM ".$table10." LIKE '".$key."' ;";
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
					$table2 = $table10;
				}
				else 
				{
					$sql = "SHOW COLUMNS FROM ".$table11." LIKE '".$key."' ;";
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
						$table2 = $table11;
					}
					else
					{
                        continue;
					}
				}
				
				if (is_array($value))
				{
					foreach ($value as $key_sub => $value_sub)
					{
						$sql = "SELECT * FROM ".$table2." WHERE tableKey = '".$key_sub."'";
						
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
							if ($mode == 'default')
							{
								/*if ($key !== "folder" && $key !== "file" && $key !== "projectType" && $key !== "extra" && $key !== "fileExtension")
								{
									$sql = "UPDATE ".$table2. " SET ".mysqli_real_escape_string($link, $key)." = AES_ENCRYPT('".mysqli_real_escape_string($link, trim($value_sub))."', SHA2('".$phrase."', 512)) WHERE tableKey = '".mysqli_real_escape_string($link, $key_sub)."'";
								}
								else*/
								{
									if ($key !== "date" && $key !== "active" && $key !== "status" && $key !== "group")
									{
										//$sql = "UPDATE ".$table2. " SET ".mysqli_real_escape_string($link, $key)." = '".mysqli_real_escape_string($link, trim($value_sub))."' WHERE tableKey = '".mysqli_real_escape_string($link, $key_sub)."'";
										$sql = "UPDATE ".$table2. " SET ".mysqli_real_escape_string($link, $key)." = AES_ENCRYPT('".mysqli_real_escape_string($link, trim($value_sub))."', SHA2('".$phrase."', 512)) WHERE tableKey = '".mysqli_real_escape_string($link, $key_sub)."'";
									}
									else
									{
										$sql = "UPDATE ".$table2. " SET `".mysqli_real_escape_string($link, $key)."` = '".mysqli_real_escape_string($link, trim($value_sub))."' WHERE tableKey = '".mysqli_real_escape_string($link, $key_sub)."'";
									}
								}
								 $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
							}
							else if ($mode == 'customer')
							{
								if ($key !== "folder" && $key !== "file" && $key !== "projectType" && $key !== "minAccountId" && $key !== "headerType" && $key !== "start" && $key !== "end" && $key !== "fileExtension")
								{
									$sql = "UPDATE ".$table2. " SET ".mysqli_real_escape_string($link_k, $key)." = AES_ENCRYPT('".mysqli_real_escape_string($link_k, trim($value_sub))."', SHA2('".$phrase_k."', 512)) WHERE tableKey = '".mysqli_real_escape_string($link, $key_sub)."'";
								}
								else
								{
									$sql = "UPDATE ".$table2. " SET ".mysqli_real_escape_string($link_k, $key)." = '".mysqli_real_escape_string($link_k, trim($value_sub))."' WHERE tableKey = '".mysqli_real_escape_string($link, $key_sub)."'";
								}
                                //echo __LINE__." ".$sql."<br>";
                                $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
							}
						}
						else
						{     
                            if ($mode === "default")
                            {
                                $field = findConnectionField($table10, "default");
                            }
                            else
                            {
                                $field = findConnectionField($table10, "customer");
                            }
                            
                            if(strpos($key_sub, "_") !== false)
                            {
                                $temp4 = array_map("trim", explode("_", $key_sub));
                                
                                $sql = "SELECT * FROM ".$table10 ." WHERE tableKey = '".$temp4[0]."'";
								echo __LINE__." ".$sql."<br>";
								
								if ($mode == 'default')
                                {
                                    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                                }
                                else if ($mode == 'customer')
                                {
                                    $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
                                } 
                                
                                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                                {
                                    $temp2 = $row[$field];
                                }
                                
                                $replaceLang = getReplaceLang2(false);
                                
                                if ($mode == 'default')
                                {
                                    $sql = "SELECT * FROM ".$table11." WHERE ".$field." = '".$temp2."' AND lang = AES_ENCRYPT('".$replaceLang[$temp4[1]]."', SHA2('".$phrase."', 512))";
                                    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                                }
                                else if ($mode == 'customer')
                                {
                                    $sql = "SELECT * FROM ".$table11." WHERE ".$field." = '".$temp2."' AND lang = AES_ENCRYPT('".$replaceLang[$temp4[1]]."', SHA2('".$phrase_k."', 512))";
                                    $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
                                } 
                                
                                if (mysqli_num_rows($result) > 0)
                                {
                                    while ($row = mysqli_fetch_array($result))
                                    {
                                        $tableKey = $row['tableKey'];
                                    }
                                    
                                    if ($mode == 'default')
                                    {
                                        $sql = "UPDATE ".$table11." SET ".$key." = AES_ENCRYPT('".mysqli_real_escape_string($link, $value_sub)."', SHA2('".$phrase."', 512)) WHERE tableKey = '".$tableKey."'";
                                        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                                    }
                                    else if ($mode == 'customer')
                                    {
                                        $sql = "UPDATE ".$table11." SET ".$key." = AES_ENCRYPT('".mysqli_real_escape_string($link, $value_sub)."', SHA2('".$phrase_k."', 512)) WHERE tableKey = '".$tableKey."'";
                                        $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
                                    } 
                                }
                                else
                                {
                                    if ($mode == 'default')
                                    {
                                        $sql = "INSERT INTO ".$table11." (".$field.", ".$key.", lang) VALUES ('".$temp2."', AES_ENCRYPT('".mysqli_real_escape_string($link, $value_sub)."', SHA2('".$phrase."', 512)), AES_ENCRYPT('".mysqli_real_escape_string($link, $replaceLang[$temp4[1]])."', SHA2('".$phrase."', 512)))";
                                        echo __LINE__." ".$sql."<br>";
										$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                                    }
                                    else if ($mode == 'customer')
                                    {
                                        $sql = "INSERT INTO ".$table11." (".$field.", '".$key.", lang) VALUES ('".$temp2."', AES_ENCRYPT('".mysqli_real_escape_string($link_, $value_sub)."', SHA2('".$phrase_k."', 512)) , AES_ENCRYPT('".mysqli_real_escape_string($link, $replaceLang[$temp4[1]])."', SHA2('".$phrase_k."', 512)))";
                                        $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
                                    } 
                                    checkTable($table11, $mode);
                                    continue;
                                }
                                
                            }
                            
                            else if (isset($_POST['replaceLang']))
                            {
                                $sql = "SELECT * FROM ".$table10." WHERE tableKey = '".$key_sub."'";
                                if ($mode == 'default')
                                {
                                    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                                }
                                else if ($mode == 'customer')
                                {
                                    $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
                                } 
                            }
                            else
                            {
                                $sql = "SELECT * FROM ".$table10." WHERE tableKey = '".$key_sub."'";
                                //echo __LINE__." ".$sql."<br>";
                                if ($mode == 'default')
                                {
                                    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                                }
                                else if ($mode == 'customer')
                                {
                                    $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
                                }
                            }
							
                            if (mysqli_num_rows($result) == 0)
							{
								$id = key($_SESSION['replaceTempKey'][$key_sub]);
								$id = array_search($key_sub, $_SESSION['replaceTempKey'] );
							}
							else
								
							{
								while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
								{
									$id = $row[$field];
								}
							}
							
							if ($mode == 'default')
							{
								if (!empty($field))
								{
									$sql = "INSERT INTO ".$table2." (".$field.", lang, ".mysqli_real_escape_string($link, $key).") VALUES ('".$id."',AES_ENCRYPT('".mysqli_real_escape_string($link, $replaceLang[$_POST['replaceLang']])."', SHA2('".$phrase."', 512)), AES_ENCRYPT('".mysqli_real_escape_string($link, trim($value_sub))."', SHA2('".$phrase."', 512)))";
								}
								else
								{
									$sql = "INSERT INTO ".$table2." (lang, ".mysqli_real_escape_string($link, $key).") VALUES (AES_ENCRYPT('".mysqli_real_escape_string($link, $key_sub)."', SHA2('".$phrase."', 512)), AES_ENCRYPT('".mysqli_real_escape_string($link, trim($value_sub))."', SHA2('".$phrase."', 512)))";
								}
                                
                                $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
							}
							else if ($mode == 'customer')
							{
								if (!empty($field))
								{
									$sql = "INSERT INTO ".$table2." (".$field.",lang, ".mysqli_real_escape_string($link_k, $key).") VALUES ('".$id."',AES_ENCRYPT('".mysqli_real_escape_string($link_k, $replaceLang[$_POST['replaceLang']])."', SHA2('".$phrase_k."', 512)), AES_ENCRYPT('".mysqli_real_escape_string($link_k, trim($value_sub))."', SHA2('".$phrase_k."', 512)))";
								}
								else
								{
									$sql = "INSERT INTO ".$table2." (lang, ".mysqli_real_escape_string($link_k, $key).") VALUES (AES_ENCRYPT('".mysqli_real_escape_string($link_k, $key_sub)."', SHA2('".$phrase_k."', 512)), AES_ENCRYPT('".mysqli_real_escape_string($link_k, trim($value_sub))."', SHA2('".$phrase_k."', 512)))";
                                }
                                $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
							}
                            checkTable($table11, $mode);
						}
						//echo $sql."<br>";
					}
				}
            }
		}
	}

	function sync_site_settings()
	{
        global $link;
        global $phrase;
        
        $replaceTable = getReplaceTable(false);
        
		$table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
		$table11 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
        
        $replaceLangRev = getReplaceLang2(false);
        
        if (isset($_POST["data"]) || isset($_POST['setting']))
        {
            foreach ($_POST as $key => $value)
            {
                if ($key === "replaceTable")
                {
                    continue;
                }
                
                $field = $key;
                foreach ($value as $sub_key => $sub_value)
                {
                    $tableKey = $sub_key;
                    
                    if ($field == "lang")
                    {
                        $lang[] = $replaceLangRev[$sub_value];
                    }
                    else
                    {
                        $data = mysqli_real_escape_string($link, trim($sub_value));
                    }
                }
            }
        
            $sql = "SELECT * FROM ".$table10." WHERE tableKey = '".$tableKey."'";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                if ($row['type'] == "lang")
                {
                    $sql = "UPDATE ".$table10." SET data = AES_ENCRYPT('".implode(", ", (array)$lang)."', SHA2('".$phrase."',512)) WHERE tableKey = '".$tableKey."'";
                    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
                }
                else if ($row['type'] == "data")
                {
                    $sql = "UPDATE ".$table10." SET ".$field." = AES_ENCRYPT('".$data."', SHA2('".$phrase."',512)) WHERE tableKey = '".$tableKey."'";
                    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
                }
                else
                {
                    updateTable($table10, null, 'default');
                }
            }
        }
        else
        {
            updateTable($table10, $table11, 'default');
        }
    }

    function sync_menu()
	{
        global $link;
        global $phrase;
        
        $replaceTable = getReplaceTable2(false);
        
		$table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
		$table11 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
        
        if (isset($_POST['type']))
        {
            foreach ($_POST["type"] as $key => $value)
            {
                $tableKey = $key;
                $data = $value;
            }
            
            $sql = "UPDATE ".$table10." SET type = '".mysqli_real_escape_string($link, $data)."' WHERE tableKey = '".$tableKey."'";
            //echo __LINE__." ".$sql."<br>";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
        }
        else if (isset($_POST['displayMenu']))
        {
            foreach ($_POST["displayMenu"] as $key => $value)
            {
                $tableKey = $key;
                $type = $value;
            }
            
            $sql = "UPDATE ".$table10." SET displayMenu = '".mysqli_real_escape_string($link, $type)."' WHERE tableKey = '".$tableKey."'";
            //echo __LINE__." ".$sql."<br>";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
        }
        else
        {
            updateTable($table10, $table11, 'default');
        }
    }

    function sync_menu_footer()
    {
        global $link;
        global $phrase;
        
        $replaceTable = getReplaceTable2(false);
        $table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
		$table11 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
        
        $table20 = "`".PREFIX."menu`";
        
        if (isset($_POST['masterMenuId']))
        {
            foreach ($_POST["masterMenuId"] as $key => $value)
            {
                $tableKey = $key;
                $data = str_replace(array("[","]"),'',$value);
                $temp = explode(",", $data);
                $value = mysqli_real_escape_string($link, reset($temp));
            }
            
            if ((int)$value < 0)
            {
                $sql = "UPDATE ".$table10." SET masterMenuId = null WHERE tableKey = '".$tableKey."'";
                $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
            }
            else
            {
                $sql = "SELECT * FROM ".$table20." WHERE tableKey = '".$value."'";
                $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));    

                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                    $menuId = $row['menuId'];
                }
                if (!empty($menuId))
                {
                    $sql = "UPDATE ".$table10." SET masterMenuId = '".$menuId."' WHERE tableKey = '".$tableKey."'";
                    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                }
            }
            
             
        } 
        else
        {
           updateTable($table10, $table11, 'default'); 
        }
    }

    function sync_own_pages()
	{
        global $link;
        global $phrase;
        
        $replaceTable = getReplaceTable2(false);
        $table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
		$table11 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
        
        $table20 = "`".PREFIX."ref_properties`";
        
        if (isset($_POST['headerImage']))
        {
            foreach ($_POST["headerImage"] as $key => $value)
            {
                $tableKey = $key;
                $data = str_replace(array("[","]"),'',$value);
                $temp = explode(",", $data);
                $value = reset($temp);
            }
            
            $sql = "UPDATE ".$table10." SET headerImage = AES_ENCRYPT('".mysqli_real_escape_string($link, $data)."', SHA2('".$phrase."', 512)) WHERE tableKey = '".$tableKey."'";
            //echo __LINE__." ".$sql."<br>";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
        }
        else if (isset($_POST['type']))
        {
            foreach ($_POST["type"] as $key => $value)
            {
                $tableKey = $key;
                $type = $value;
            }
            
            $sql = "UPDATE ".$table10." SET type = '".mysqli_real_escape_string($link, $type)."' WHERE tableKey = '".$tableKey."'";
            echo __LINE__." ".$sql."<br>";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
        }
        
        else
        {
            updateTable($table10, $table11, 'default');
        }
    }

    function sync_properties2object()
    {
        global $link;
        global $phrase;
        
        $replaceTable = getReplaceTable2(false);
        $table1 = "`".PREFIX."ref_objects"."`";
		$table10 = "`".PREFIX."ref_properties"."`";
        $table11 = "`".PREFIX."ref_properties2object"."`";
        
        $sql = "SELECT * FROM ".$table10."";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $propertiesId[$row['tableKey']] = $row['propertiesId'];
        }
        
        foreach ($_POST as $key => $value)
        {
            if ($key === "replaceTable")
            {
                continue;
            }
            
            foreach ($value as $sub_key => $sub_value)
            {
                $refObject = mysqli_real_escape_string($link, $sub_key);
                
                foreach ($sub_value as $sub2_key => $sub2_value)
                {
                    $newData[] = mysqli_real_escape_string($link, $propertiesId[$sub2_value]);
                }
            }
        }
        
        $sql = "SELECT * FROM ".$table1 ." WHERE tableKey = '".$refObject."'";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $refObject = $row['referenceId'];
        }
        
        $sql = "SELECT * FROM ".$table11." WHERE objectId = '".$refObject."'";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $oldData[$row['propertiesId']] = $row['propertiesId'];
        }
        
        if (empty($insertData))
        {
            $insertData = $newData;
        }
        else
        {
            $insertData = array_diff($oldData, $newData);
        }
        
        $deleteData = array_diff($oldData, $newData); 
                
        if (is_array($insertData))
        {
            foreach ($insertData as $key => $value)
            {
                $sql = "SELECT * FROM ".$table11." WHERE objectId = '".$refObject."' AND propertiesId = '".$value."'";
                $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
                
                if (mysqli_num_rows($result) === 0)
                {
                    $sql = "INSERT INTO ".$table11." (objectId, propertiesId) VALUES ('".$refObject."', '".$value."')";
                    echo __LINE__." ".$sql."<br>";
                    $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
                }
            }
        }
        
        if (is_array($deleteData))
        {
            $sql = "DELETE FROM ".$table11." WHERE (propertiesId = '".implode("' OR propertiesId = '", (array)$deleteData)."') AND objectId = '".$refObject."'";
            echo 
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
        }
    }

    function sync_ref_objects()
	{
        global $link;
        global $phrase;
        
        $replaceTable = getReplaceTable2(false);
        $table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
		$table11 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
        
        $table20 = "`".PREFIX."ref_properties`";
        
        $table30 = "`".PREFIX."ref_types`";
        
        if (isset($_POST['refType']))
        {
            foreach ($_POST["refType"] as $key => $value)
            {
                $tableKey = $key;
                $type = $value;
            }
            
            $sql = "SELECT * FROM ".$table30." WHERE tableKey = '".$type."'";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
            
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $typeId = $row['typeId'];
            }
            
            $sql = "UPDATE ".$table10." SET refType = '".mysqli_real_escape_string($link, $typeId)."' WHERE tableKey = '".$tableKey."'";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
        }
        else if (isset($_POST['refType']))
        {
            foreach ($_POST["refType"] as $key => $value)
            {
                $tableKey = $key;
                $type = $value;
            }
            
            $sql = "SELECT * FROM ".$table20." WHERE tableKey = '".$type."'";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
            
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $propertiesId = $row['propertiesId'];
            }
            
            $sql = "UPDATE ".$table10." SET refType = '".mysqli_real_escape_string($link, $propertiesId)."' WHERE tableKey = '".$tableKey."'";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
        }
        else
        {
            updateTable($table10, $table11, 'default');
        }
    }

    function sync_administrado_menu()
    {
        global $link;
        global $phrase;
        
        $replaceTable = getReplaceTable(false);
        $table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
		$table11 = "`".PREFIX."icons`";
		$table12 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
        
        if (isset($_POST['icon']))
        {
            foreach ($_POST["icon"] as $key => $value)
            {
                $tableKey = $key;
                $type = mysqli_real_escape_string($link, $value);
            }
            
            $sql = "SELECT *, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM ".$table11." WHERE tableKey = '".$type."'";
            echo __LINE__." ".$sql."<br>";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
            
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                if ((int)$row['fas'])
                {
                    $string = "fa-"; 
                }
                else if ((int)$row['far'])
                {
                    $string = "fa-"; 
                }
                else if ((int)$row['fab'])
                {
                   $string = "fa-";  
                }
                $string .= $row['note'];
            }
            
            $sql = "UPDATE ".$table10." SET icon = AES_ENCRYPT('".mysqli_real_escape_string($link, $string)."', SHA2('".$phrase."', 512)) WHERE tableKey = '".$tableKey."'";
            echo __LINE__." ".$sql."<br>";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
        }
        
        else
        {
            updateTable($table10, $table12, 'default');
        }
    }

    function sync_servotablo_menu()
    {
        global $link;
        global $phrase;
        
        $replaceTable = getReplaceTable2(false);
        $table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
		$table11 = "`".PREFIX."icons`";
        
        if (isset($_POST['icon']))
        {
            foreach ($_POST["icon"] as $key => $value)
            {
                $tableKey = $key;
                $type = mysqli_real_escape_string($link, $value);
            }
            
            $sql = "SELECT *, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM ".$table11." WHERE tableKey = '".$type."'";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
            
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                if ((int)$row['fas'])
                {
                    $string = "fa-"; 
                }
                else if ((int)$row['far'])
                {
                    $string = "fa-"; 
                }
                else if ((int)$row['fab'])
                {
                   $string = "fa-";  
                }
                $string .= $row['note'];
            }
            
            $sql = "UPDATE ".$table10." SET icon = AES_ENCRYPT('".mysqli_real_escape_string($link, $string)."', SHA2('".$phrase."', 512)) WHERE tableKey = '".$tableKey."'";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
        }
        
        else
        {
            updateTable($table10, $table11, 'default');
        }
    }

	function sync_newsletter()
    {
        global $link;
        global $phrase;
        
        $replaceTable = getReplaceTable2(false);
		
        $table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
        $table11 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
        
		
		
        if (isset($_POST['groups']))
        {
            foreach ($_POST["groups"] as $key => $value)
            {
				$tableKey = mysqli_real_escape_string($link, $key);
				
				foreach ($value as $sub_key => $sub_value)
				{
                	$tableKey2[] = mysqli_real_escape_string($link, $sub_value);
				}
            }
			
			$table12 = "`".$replaceTable[$_POST['replaceTable']]."_groups`";
            
            $sql = "SELECT newsletterGroupId FROM ".$table12." WHERE (tableKey = '".implode("' OR tableKey = '", (array)$tableKey2)."')";
            echo __LINE__." ".$sql."<br>";
			$result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
            
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $groups[] = $row['newsletterGroupId'];
            }
            
            $sql = "UPDATE ".$table10." SET groups = '".implode(", ", (array)$groups)."' WHERE tableKey = '".$tableKey."'";
			echo __LINE__." ".$sql."<br>";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link)); 
        }
        
        else
        {
            updateTable($table10, $table11, 'default');
        }
    }

    $replaceTable = getReplaceTable2(false);

echo __LINE__ ." ".$replaceTable[$_POST['replaceTable']]."<br>";
        
    if (empty($_POST['replaceTable']))
    {
        return true;
    }

    if ($replaceTable[$_POST['replaceTable']] === PREFIX."site_settings")
	{
        sync_site_settings();
	}
    
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX."menu")
	{
        sync_menu();
	}
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX."menu_footer")
	{
        sync_menu_footer();
	}
    
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX."own_pages")
	{
        sync_own_pages();
	}
    
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX."ref_properties2object")
	{
        sync_properties2object();
	}
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX."ref_objects")
	{
        sync_ref_objects();
	}
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX."administrado_menu")
	{
        sync_administrado_menu();
	}
    else if ($replaceTable[$_POST['replaceTable']] === PREFIX."servotablo_menu")
	{
        sync_servotablo_menu();
	}
	else if ($replaceTable[$_POST['replaceTable']] === PREFIX."newsletter")
	{
        sync_newsletter();
	}
	else if ($replaceTable[$_POST['replaceTable']] === PREFIX."newsletter_readers")
	{
		if (isset($_POST['group']))
		{
			$table = "`".PREFIX."newsletter_groups`";
			
			$sql = "SELECT * FROM ".$table."";
			$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$group[$row['tableKey']] = $row['newsletterGroupId'];
			}
			
			if (is_array($_POST['group']))
			{
				foreach ($_POST['group'] as $key => $value)
				{
					$masterKey = $key;
					foreach ($value as $sub_key => $sub_value)
					{
						$temp[] = $group[$sub_value];
					}
				}
				
				$_POST['group'][$key] = array();
				
				$_POST['group'][$key] = implode(", ", (array)$temp);
			}
		}
		
		echo __LINE__." ";
		print_r($_POST['group']);
		echo "<br>";
		
        $table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
        
        updateTable($table10, null);
	}
	else if ($replaceTable[$_POST['replaceTable']] === PREFIX."users")
	{
		echo __LINE__." Hmmmmmmm.......<br>";
		
		if (isset($_POST['password']))
		{
			foreach ($_POST['password'] as $key => $value)
			{
				$table_key = mysqli_real_escape_string($link,$key);
				
				$password = mysqli_real_escape_string($link, trim($value));
			}
			
			foreach ($_POST['repPassword'] as $key => $value)
			{
					$reppassword = mysqli_real_escape_string($link, trim($value));
			}
			echo __LINE__." ".$password."<br>";
			echo __LINE__." ".$reppassword."<br>";
			
			if (strlen($password) >= 6)
			{
				if ($password == $reppassword)
				{
					echo __LINE__." ".$password."<br>";
					
					$newpassword = password_hash($password, PASSWORD_BCRYPT);
					echo __LINE__." ".$newpassword."<br>";
					
					$table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
					
					$sql = "UPDATE ".$table10." SET password = AES_ENCRYPT('".$newpassword."', SHA2('".$phrase."', 512)) WHERE tableKey = '".$table_key."'";
					echo __LINE__." ".$sql."<br>";
					$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			$table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
			$table11 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";

			updateTable($table10, $table11);
		}
	}
    else
    {
        $table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
        $table11 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
        
        updateTable($table10, $table11);
    }
?>