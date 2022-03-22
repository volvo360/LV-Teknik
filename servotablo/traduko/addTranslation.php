<?php
	session_start();

	include_once("../../common/db.php");
    include_once("../../common/userData.php");
	include_once("../../administrado/ext/theme/nav.php");
	
	include_once("../../common/crypto.php");

	function addTreeData($var = null)
	{
		global $link;
		global $link_k;

		global $phrase;
		global $phrase_k;
		
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
		$showAjaxProjects = $langStrings['showAjaxProjects'];

		$showAjaxProjects_array = getLangstringsArray('showAjaxProjects_array', $displayLang);
        
		$table = "`".PREFIX."translation`";
		
		$sql = "SELECT CASE WHEN MAX(arrayKey) IS NULL THEN 1 ELSE  MAX(arrayKey) + 1 END as arrayKey FROM (SELECT CAST(AES_DECRYPT(arrayKey, SHA2('".$phrase."',512)) AS int) as arrayKey FROM ".$table." WHERE variable = AES_ENCRYPT('".$var."', SHA2('".$phrase."',512))) as t";
        //echo __LINE__." ".$sql."<br>";
        $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$arrayKey = $row['arrayKey'];
        }
		
		foreach ($_POST as $master_key => $master_value)
		{
			if ($master_key === "replaceVar" || $master_key === "replaceTable")
			{
				continue;
			}
			
			foreach ($master_value as $key => $value)
			{
				$sql = "INSERT INTO ".$table." (variable, arrayKey, lang, ".$master_key.") VALUES (AES_ENCRYPT('".mysqli_real_escape_string($link, $var)."', SHA2('".$phrase."',512)), AES_ENCRYPT('".mysqli_real_escape_string($link, $arrayKey)."', SHA2('".$phrase."',512)),AES_ENCRYPT('".mysqli_real_escape_string($link, reset($displayLang))."', SHA2('".$phrase."',512)), AES_ENCRYPT('".mysqli_real_escape_string($link, $value)."', SHA2('".$phrase."',512)))";
				//echo __LINE__." ".$sql."<br>";
                $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link));
			}
		}
		
		return(checkTable($table));
	}

	$replaceTable = getReplaceTable(false);

    $replaceVar = getReplaceTranslationVar(false);

    if ($replaceTable[$_POST['replaceTable']] === PREFIX."translation")
	{
		$tableKey = addTreeData($replaceVar[$_POST['replaceVar']]);
		
		echo "<div id = \"tableKey\">";
			echo $tableKey;
		echo "</div>";
	}

?>