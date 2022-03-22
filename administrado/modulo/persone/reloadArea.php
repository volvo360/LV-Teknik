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
        
        if ($replaceTable[$_POST['replaceTable']] === PREFIX_K."CVheaders")
        {
            //include_once("../../common/resyncTree.php");
            reloadCVHeader();
        } 
        else if ($replaceTable[$_POST['replaceTable']] === PREFIX_K."CVworkExperience")
        {
            //include_once("../../common/resyncTree.php");
            reloadCVworkExperience();
        }   
        
        else if ($replaceTable[$_POST['replaceTable']] === PREFIX_K."CVeducationExperience")
        {
            //include_once("../../common/resyncTree.php");
            reloadCVeducationExperience();
        }   
    }

    function reloadCVHeader()
    {
        global $link;
        global $link_k;
        
        global $phrase;
		global $phrase_k;
        
        include_once("../../common/resyncTree.php");
        
        $replaceTable = getReplaceTable(false);
        $replaceLang = getReplaceLang(false);
        
		$table = PREFIX."project_types";
        $table2 = PREFIX."project_types_lang";
        
        $table10 = PREFIX."project_keys";
        
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
        
        $sql = "SELECT *, t2.tableKey as tableKey FROM ".$table." t1 INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT tableKey, headerId, CAST(AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) as CHAR) as lang, CAST(AES_DECRYPT(note, SHA2('".$phrase_k."', 512)) as CHAR) as note FROM ".$table2." ) as q ORDER BY CASE ".implode(" ", (array)$order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t GROUP BY headerId) as t2 ON t1.headerId = t2.headerId";

        //echo __LINE__." ".$sql."<br>";
        
        $tree = retriveTree($sql, "customer", "headerId");

        renderTree($tree);
    }

    function reloadCVworkExperience()
    {
        global $link;
        global $link_k;
        
        global $phrase;
		global $phrase_k;
        
        include_once("../../common/resyncTree.php");
        
        $replaceTable = getReplaceTable(false);
        $replaceLang = getReplaceLang(false);
        
		$table = PREFIX."project_types";
        $table2 = PREFIX."project_types_lang";
        
        $table10 = PREFIX."project_keys";
        
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
        
        //$sql = "SELECT *, t2.tableKey as tableKey FROM ".$table." t1 INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT tableKey, workExperienceId, CAST(AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) as CHAR) as lang, CAST(AES_DECRYPT(company, SHA2('".$phrase_k."', 512)) as CHAR) as company FROM ".$table2." ) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t GROUP BY workExperienceId) as t2 ON t1.workExperienceId = t2.workExperienceId";
        
        $sql = "SELECT node.*, CAST(AES_DECRYPT(node.company, SHA2('".$phrase_k."', 512)) as CHAR) as note, (COUNT(parent.lft) - 1) AS depth
                FROM ".$table." AS node,
                        ".$table." AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                GROUP BY node.lft
                ORDER BY node.lft;";

        //echo __LINE__." ".$sql."<br>";
        
        $tree = retriveTree($sql, "customer", "workExperienceId");

        renderTree($tree);
    }

    function reloadCVeducationExperience()
    {
        global $link;
        global $link_k;
        
        global $phrase;
		global $phrase_k;
        
        include_once("../../common/resyncTree.php");
        
        $replaceTable = getReplaceTable(false);
        $replaceLang = getReplaceLang(false);
        
		$table = PREFIX."project_types";
        $table2 = PREFIX."project_types_lang";
        
        $table10 = PREFIX."project_keys";
        
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
        
        //$sql = "SELECT *, t2.tableKey as tableKey FROM ".$table." t1 INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT tableKey, workExperienceId, CAST(AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) as CHAR) as lang, CAST(AES_DECRYPT(company, SHA2('".$phrase_k."', 512)) as CHAR) as company FROM ".$table2." ) as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as t GROUP BY workExperienceId) as t2 ON t1.workExperienceId = t2.workExperienceId";
        
        $sql = "SELECT node.*, CAST(AES_DECRYPT(node.education, SHA2('".$phrase_k."', 512)) as CHAR) as note, (COUNT(parent.lft) - 1) AS depth
                FROM ".$table." AS node,
                        ".$table." AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                GROUP BY node.lft
                ORDER BY node.lft;";

        //echo __LINE__." ".$sql."<br>";
        
        $tree = retriveTree($sql, "customer", "educationExperienceId");

        renderTree($tree);
    }

    /*function reload_k_document_text_nav()
    {
        global $link;
        global $link_k;
        
        global $phrase;
        global $phrase_k;
        
        $replaceTable = getReplaceTable(false);
        
        $replaceLang = getReplaceLang(false);
        
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
		
		$langStrings = getlangstrings();
		$documentsFiles = $langStrings['documentsFiles'];

		$documentsFiles_array = getLangstringsArray('documentsFiles_array', $displayLang);
        
        $navTabs['showDocumentsFiles'] = $documentsFiles[1];
        $navTabs['showDocumentsText'] = $documentsFiles[2];
        
        if (isset($_POST['replaceProject']))
        {
            $table = "`".PREFIX_K."project`";
            
            $sql = "SELECT * FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link_k, $_POST['replaceProject'])."'";
            $result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));

            while ($row = mysqli_fetch_array($result))
            {
                $projectId = $row['projectId'];    
            }
        
            define (PREFIX_K_D, PREFIX_K."dokumenti_".$projectId."_");
        }
        
        $table10 = "`".PREFIX_K_D."document_text`";
        $table11 = "`".PREFIX_K_D."document_text_lang`";
        
        $sql = "SELECT * FROM (SELECT node.tableKey, node.lft, node.rgt, node.headerId, (COUNT(parent.lft) - 1) AS depth
            FROM ".$table10." AS node,
                    ".$table10." AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
            GROUP BY node.lft
            ORDER BY node.lft) AS t10 INNER JOIN (SELECT * FROM (SELECT * FROM (SELECT headerId, AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) as lang, AES_DECRYPT(header, SHA2('".$phrase_k."', 512)) as header FROM ".$table11.") as q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as p GROUP BY headerId) as t11 ON t10.headerId = t11.headerId";
        //echo __LINE__." ".$sql."<br>";
        $result =  mysqli_query($link_k, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link_k));

        while ($row = mysqli_fetch_array($result))
        {
            $navTabs['showDocText_'.$row['tableKey']] = $row['header'];
        }
        
        echo "<ul class=\"nav nav-tabs\" id=\"fileDocumentTabs\" role=\"tablist\">";
            $first = true;
            foreach ($navTabs as $key => $value)
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
        
    }

    function reload_k_document_text()
    {
        global $link;
        global $link_k;
        
        global $phrase;
        global $phrase_k;
        
        include_once("../../common/resyncTree.php");
        
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
		
		$langStrings = getlangstrings();
		$showAjaxProjects = $langStrings['showAjaxProjects'];

		$showAjaxProjects_array = getLangstringsArray('showAjaxProjects_array', $displayLang);
        
        $table20 = "`".PREFIX_K."project`";
        
        $sql = "SELECT * FROM ".$table20." WHERE tableKey = '".mysqli_real_escape_string($link_k, $_POST['replaceProject'])."'";
        $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
        
        while ($row = mysqli_fetch_array($result))
        {
            $projectId = $row['projectId'];
        }
        
        define (PREFIX_K_M, PREFIX_K."manual_".$projectId."_");
        
        $table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
        $table11 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
        
        $sql = "SELECT * FROM (SELECT node.lft, node.rgt, node.headerId, (COUNT(parent.lft) - 1) AS depth
                FROM ".$table10." AS node,
                        ".$table10." AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                GROUP BY node.lft
                ORDER BY node.lft) t10 INNER JOIN (SELECT * FROM (SELECT headerId, CAST(AES_DECRYPT(header, SHA2('".$phrase_k."', 512)) as CHAR) as note, CAST(AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) as CHAR) as lang FROM ".$table11.") AS q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as lang ON t10.headerId = lang.headerId GROUP BY t10.headerId ORDER BY t10.lft";
        //echo __LINE__." ".$sql."<br>";
        
        $tree = retriveTree($sql, "customer", "headerId");
        
        renderTree($tree);
        //echo "]}";
    }

    function reload_k_documenti_header()
    {
        global $link;
        global $link_k;
        
        global $phrase;
        global $phrase_k;
        
        include_once("../../common/resyncTree.php");
        
        $replaceTable = getReplaceTable(false);
        
        $replaceLang = getReplaceLang(false);
        
        $$userSettings = getUserSettings();

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
		
		$langStrings = getlangstrings();
		$showAjaxProjects = $langStrings['showAjaxProjects'];

		$showAjaxProjects_array = getLangstringsArray('showAjaxProjects_array', $displayLang);
        
        $table20 = "`".PREFIX_K."project`";
        
        $sql = "SELECT * FROM ".$table20." WHERE tableKey = '".mysqli_real_escape_string($link_k, $_POST['replaceProject'])."'";
        $result= mysqli_query($link_k, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." : ".mysqli_error ($link_k));
        
        while ($row = mysqli_fetch_array($result))
        {
            $projectId = $row['projectId'];
        }
        
        define (PREFIX_K_D, PREFIX_K."dokumenti_".$projectId."_");
        
        $table10 = "`".$replaceTable[$_POST['replaceTable']]."`";
        $table11 = "`".$replaceTable[$_POST['replaceTable']]."_lang`";
        
        $sql = "SELECT * FROM (SELECT node.lft, node.rgt, node.headerId, (COUNT(parent.lft) - 1) AS depth
                FROM ".$table10." AS node,
                        ".$table10." AS parent
                WHERE node.lft BETWEEN parent.lft AND parent.rgt
                GROUP BY node.lft
                ORDER BY node.lft) t10 INNER JOIN (SELECT * FROM (SELECT headerId, CAST(AES_DECRYPT(note, SHA2('".$phrase_k."', 512)) as CHAR) as note, CAST(AES_DECRYPT(lang, SHA2('".$phrase_k."', 512)) as CHAR) as lang FROM ".$table11.") AS q ORDER BY CASE ".implode(" ", $order)." WHEN lang = 'en' THEN 11 WHEN lang = 'de' THEN 12 WHEN lang = 'fr' THEN 13 WHEN lang = 'it' THEN 14 ELSE 100 END  LIMIT 18446744073709551615) as lang ON t10.headerId = lang.headerId GROUP BY t10.headerId ORDER BY t10.lft";
        //echo __LINE__." ".$sql."<br>";
        
        $tree = retriveTree($sql, "customer", "headerId");
        
        renderTree($tree);
        //echo "]}";
    } */   
?>