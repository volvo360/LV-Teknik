<?php
error_reporting(E_ALL);
session_start();

include_once("db.php");
include_once("../../common/crypto.php");
include_once("./crypto.php");
include_once("./userData.php");
include_once("./../ext/theme/nav.php");


$replaceTable = getReplaceTable(false);

function insertIntoTable($table1 = null, $table2 = null, $type = "default")
{
	global $link;
	global $link_k;
	
	global $phrase;
	global $phrase_k;
	
	$lang = $settings['def_lang'] = 'sv';
	$country = $settings['def_country'] = 'SE';
	
	foreach ($_POST as $key => $value)
	{
		if ($key !== "replaceTable" && $key !== "projectReplaceKey")
		{
			if ($type =="default")
			{
				$sub_field[mysqli_real_escape_string($link, $key)] = mysqli_real_escape_string($link, $value);
			}
			else if ($type === "customer")
			{
				$sub_field[mysqli_real_escape_string($link_k, $key)] = mysqli_real_escape_string($link_k, $value);
			}
		}
	}
	
	$sub_field['lang'] = $settings['def_lang'];
	
	$sql = "DESCRIBE ".$table1;
	
	if ($type =="default")
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
		if ($row['Field'] !== "autoId")
		{
			if ($row['Type'] == "blob")
			{
				$blob[$row['Field']] = $row['Field'];
			}
			else if (!$block)
			{
				foreach ($validInt as $key => $value)
				{
					if (strpos(strtolower($row['Type']), strtolower($key)) !== false) {
						$block = true;
						$field = $row['Field'];
					}
				}
			}
			
		}
	}
	$sql = "SELECT CASE WHEN MAX(".$field.") IS NULL THEN 1 ELSE MAX(".$field.")+1 END as ".$field." FROM ".$table1.";";
	if ($type === "default")
	{
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	}
	else if ($type === "customer")
	{
		$result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
	}
	
	while ($row = mysqli_fetch_array($result))
	{
		$insertId = reset($row);
	}	
	
	$sql = "SHOW COLUMNS FROM ".$table1." LIKE '".key($sub_field)."'";
	if ($type === "default")
	{
    	$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	}
	else if ($type === "customer")
	{
		$result2 =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
	}
	
	if (mysqli_num_rows($result2) > 0)
	{
		if (array_key_exists(key($sub_field),(array)$blob))
		{
			$sql = "INSERT INTO ".$table1." (".$field.", ".key($sub_field).") VALUES ('".$insertId."', AES_ENCRYPT('".reset($sub_field)."', SHA2('".$phrase."', 512)))"; 
		}
		else
		{
			$sql = "INSERT INTO ".$table1." (".$field.", ".key($sub_field).") VALUES ('".$insertId."', '".reset($sub_field)."')"; 
		}
	}
	else
	{
		$sql = "INSERT INTO ".$table1." (".$field.") VALUES ('".$insertId."')"; 
	}
	
	if ($type === "default")
	{
		$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	}
	else if ($type === "customer")
	{
		$result2 =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
	}
	
	$sql = "SHOW TABLES LIKE ".str_replace("`", "'", $table2);
	if ($type === "default")
	{
        echo __LINE__." ".$sql."<br>";
        
		$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	}
	else if ($type === "customer")
	{
		$result2 =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
	}
	
	if (mysqli_num_rows($result2) > 0)
	{
		$sql = "SHOW COLUMNS FROM ".$table2." LIKE '".key($sub_field)."'";
		if ($type === "default")
		{
			echo __LINE__." ".$sql."<br>";

			$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		}
		else if ($type === "customer")
		{
			$result2 =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
		}

		if (mysqli_num_rows($result2) > 0)
		{

			$sql = "INSERT INTO ".$table2." (";
				unset ($data);

				$data[] = $field;
				foreach ($sub_field as $key => $value)
				{
					$data[] =$key;
				}
				$sql .= implode(", ",(array)$data).")";
			$sql .= " "."VALUES (";

				unset ($data);

				$data[] = "'".$insertId."'";

				foreach ($sub_field as $key => $value)
				{
					$temp = "AES_ENCRYPT('".$value."', SHA2('";

					if ($type =="default")
					{
						$temp .=  $phrase;
					}
					else if ($type === "customer")
					{
						$temp .=  $phrase_k;
					}

					$temp .= "',512))";

					$data[] = $temp;

				}
			$sql .= implode(", ",(array)$data).")";

			if ($type === "default")
			{
				echo __LINE__." ".$sql."<br>";

				$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
			}
			else if ($type === "customer")
			{
				$result2 =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
			}
		}
		checkTable($table2);
	}
	checkTable($table1);
	
	
	$sql = "SELECT * FROM ".$table1." WHERE ".$field." = '".$insertId."'";
	if ($type === "default")
	{
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	}
	else if ($type === "customer")
	{
		$result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));
	}
	
	while ($row = mysqli_fetch_array($result))
	{
		/*echo "<div id = \"replaceKey\">";
			echo $row['tableKey'];
		echo "</div>";*/
        
        return $row['tableKey'];
	}
}

/*function addPostTable_account_permission()
{
	$table1 = PREFIX."account_permission";
	$table2 = PREFIX."account_permission_lang";
	
	insertIntoTable($table1, $table2, "default");
}

function addPostTable_account_plans()
{
	$table1 = PREFIX."account_plans";
	$table2 = PREFIX."account_plans_lang";
	
	insertIntoTable($table1, $table2, "default");
}

function addPostTable_administrado_menu()
{
	$table1 = PREFIX."administrado_menu";
	$table2 = PREFIX."administrado_menu_lang";
	
	insertIntoTable($table1, $table2, "default");
}

function addPostTable_competence()
{
	$table1 = PREFIX."competence";
	$table2 = PREFIX."competence_lang";
	
	insertIntoTable($table1, $table2, "default");
}

function addPostTable_default_collection()
{
	$table1 = PREFIX."default_collection";
	$table2 = PREFIX."default_collection_lang";
	
	insertIntoTable($table1, $table2, "default");
}

function addPostTable_default_files_folders()
{
    global $link;
    global $phrase;
    
    $table1 = PREFIX."default_files_folders";
    
    $sql = "SELECT * FROM $table1 WHERE UPPER(AES_DECRYPT(folder, SHA2('".$phrase."', 512))) = UPPER('".mysqli_real_escape_string($link, $_POST['folder'])."')";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	if (mysqli_num_rows($result) > 0)
	{
		return false;
	}
    
    $sql = "INSERT INTO ".$table1." (folder) VALUES (AES_ENCRYPT('".mysqli_real_escape_string($link, $_POST['folder'])."', SHA2('".$phrase."', 512)))";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    $sql = "UPDATE ".$table1." SET fileFolderId = '".mysqli_insert_id($link)."' WHERE autoId = '".mysqli_insert_id($link)."'";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    return checkTable($table1);
}
    
function addPostTable_default_users()
{
	$table1 = PREFIX."default_users";
	$table2 = PREFIX."default_users_lang";
	
	insertIntoTable($table1, $table2, "default");
}

function addPostTable_default_page_type()
{
	$table1 = PREFIX."default_page_type";
	$table2 = PREFIX."default_page_type_lang";
	
	insertIntoTable($table1, $table2, "default");
}

function addPostTable_global_messages()
{
    global $link;
    global $link_k;
    
    global $phrase;
    global $phrase_k;
    
	$table1 = PREFIX."global_messages";
	$table2 = PREFIX."global_messages_lang";
	
	$key = insertIntoTable($table1, $table2, "default");
    
    $sql = "UPDATE ".$table1." SET startDate = DATE_ADD(NOW(), INTERVAL 1 DAY), endDate = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE tableKey = '".$key."'";
    echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
}    
    
function addPostTable_headers()
{
	$table1 = PREFIX."headers";
	$table2 = PREFIX."headers_lang";
	
	insertIntoTable($table1, $table2, "default");
}

function addPostTable_icons()
{
    global $link;
	global $link_k;
	
	global $phrase;
	global $phrase_k;
    
	$table1 = PREFIX."icons";
	$table2 = PREFIX."icon_types";
    
	$sql = "SELECT * FROM (SELECT AES_DECRYPT(note, SHA2('".$phrase."', 512)) as note FROM ".$table1 .") AS t1 WHERE UPPER(note) = UPPER('".mysqli_real_escape_string($link, $_POST['note'])."')";
	
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	if (mysqli_num_rows($result) > 0)
	{
		return false;
	}
	
    $sql = "SELECT * FROM ".$table2." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['icontype'])."'";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	if (mysqli_num_rows($result) == 0)
	{
		return false;
	}
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $iconTypeId = $row['iconTypeId'];
    }
    
	$sql = "INSERT INTO ".$table1." (note, iconTypeId) VALUES (AES_ENCRYPT('".mysqli_real_escape_string($link, $_POST['note'])."', SHA2('".$phrase."', 512)), '".$iconTypeId."')";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	return checkTable($table1);
	
	//insertIntoTable($table1, $table2, "default");
}

function addPostTable_internal_message_group()
{
	$table1 = PREFIX."internal_message_group";
	$table2 = PREFIX."internal_message_group_lang";
	
	insertIntoTable($table1, $table2, "default");
}    

function addPostTable_menu()
{
	$table1 = PREFIX."menu";
	$table2 = PREFIX."menulang";
	
	insertIntoTable($table1, $table2, "default");
}	

function addPostTable_project_types()
{
	$table1 = PREFIX."project_types";
	$table2 = PREFIX."project_types_lang";
	
	insertIntoTable($table1, $table2, "default");
}

function addPostTable_servotablo_menu()
{
	$table1 = PREFIX."servotablo_menu";
	$table2 = PREFIX."servotablo_menu_lang";
	
	insertIntoTable($table1, $table2, "default");
}

function addPostTable_servotablo_permission()
{
	$table1 = PREFIX."servotablo_permission";
	$table2 = PREFIX."servotablo_permission_lang";
	
	insertIntoTable($table1, $table2, "default");
}

function addPostTable_social_media()
{
	$table1 = PREFIX."social_media";
	//$table2 = PREFIX."servotablo_permission_lang";
	
	global $link;
	global $link_k;
	
	global $phrase;
	global $phrase_k;
	
	$sql = "SELECT * FROM (SELECT AES_DECRYPT(note, SHA2('".$phrase."', 512)) as note FROM ".$table1 .") AS t1 WHERE UPPER(note) = UPPER('".mysqli_real_escape_string($link, $_POST['note'])."')";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	if (mysqli_num_rows($result) > 0)
	{
		return false;
	}
	
	$sql = "INSERT INTO ".$table1." (note) VALUES (AES_ENCRYPT('".mysqli_real_escape_string($link, $_POST['note'])."', SHA2('".$phrase."', 512)))";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	return checkTable($table1);
}


function addPostTable_target_div_json_table()
{
    global $link;
	global $link_k;
	
	global $phrase;
	global $phrase_k;
    
	$table1 = PREFIX."target_div_json_table";
	//$table2 = PREFIX."status_lang";
    
    $sql = "INSERT INTO ".$table1." (dbTable) VALUES (AES_ENCRYPT('".mysqli_real_escape_string($link, $_POST['dbTable'])."', SHA2('".$phrase."', 512)));";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    return checkTable($table1);
	//insertIntoTable($table1, $table2, "default");
}

function addPostTable_status()
{
	$table1 = PREFIX."status";
	$table2 = PREFIX."status_lang";
	
	insertIntoTable($table1, $table2, "default");
}

function addPostTable_theme()
{
	$table1 = PREFIX."theme";
	$table2 = PREFIX."theme_lang";
	
	insertIntoTable($table1, $table2, "default");
}

function addPostTable_theme_collection()
{
	$table1 = PREFIX."theme_collection";
	$table2 = PREFIX."theme_collection_lang";
	
	insertIntoTable($table1, $table2, "default");
}
*/

function addPostTable_menu_footer()
{
    $table1 = PREFIX."menu_footer";
	$table2 = PREFIX."menu_footer_lang";
    
    $table10 = PREFIX."menu";
	
	global $link;
	
	global $phrase;
    
    if (isset($_POST['masterMenuId']))
    {
        $sql = "SELECT * FROM ".$table10." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['masterMenuId'])."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $menuId = $row['menuId'];
        }
        
        $sql = "INSERT INTO ".$table1." (masterMenuId) VALUES ('".$menuId."')";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        return checkTable($table1);
    }
    else
    {
        return insertIntoTable($table1, $table2, "default");
    }
}


function addPostTable_translation()
{
	$table1 = PREFIX."translation_var";
	$table2 = PREFIX."servotablo_permission_lang";
	
	global $link;
	
	global $phrase;
	
	$sql = "SELECT * FROM ".$table1 ." WHERE variable = AES_ENCRYPT('".mysqli_real_escape_string($link, $_POST['note'])."', SHA2('".$phrase."', 512))";
	//echo __LINE__." ".$sql."<br>";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	if (mysqli_num_rows($result) > 0)
	{
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
            return $row['tableKey'];
		}
	}
	
	$sql = "INSERT INTO ".$table1." (variable) VALUES (AES_ENCRYPT('".mysqli_real_escape_string($link, $_POST['note'])."', SHA2('".$phrase."', 512)))";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	return checkTable($table1);
}

function syncFieldOwnPage()
{
    global $link;
    
    global $phrase;
    
    $replaceTable = getReplaceTable(false);
    
    $table1 = "`".$replaceTable[$_POST['replaceTable']]."`";
    
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
}

$replaceTable = getReplaceTable(false);

if ($replaceTable[$_POST['replaceTable']] === PREFIX."translation")
{
    $tableKey = addPostTable_translation();
}
else if ($replaceTable[$_POST['replaceTable']] === PREFIX."menu_footer")
{
	$tableKey = addPostTable_menu_footer();
    
    global $link;
    
    $table1 = "`".$replaceTable[$_POST['replaceTable']]."`";
    $sql = "UPDATE ".$table1." SET menuId2 = autoId";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
}

else
{
    $table1 = "`".$replaceTable[$_POST['replaceTable']]."`";
    $table2 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
    
    $tableKey = insertIntoTable($table1, $table2, "default");
}

if (!empty($tableKey))
{
	echo "<div id = \"replaceKey\">";
		echo $tableKey;
	echo "</div>";
    
    /*if (is_array($data))
    {
        echo "<div id = \"blockInsertTree\">";
            echo "false";
        echo "</div>";
    }*/
}

?>