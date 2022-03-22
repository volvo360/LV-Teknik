<?php
    session_start();

    error_reporting(E_ALL);

    include_once("../../../common/db.php");
    include_once("../../../common/userData.php");
    include_once("./../../ext/theme/nav.php");
    include_once("../../common/emptor.php");

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        $replaceTable = getReplaceTable(false);
 
        if ($replaceTable[$_POST['replaceTable']] === PREFIX.'social_media')
        {
            showSocialMedia();
        } 
        else if ($replaceTable[$_POST['replaceTable']] === PREFIX_K.'CVdocuments')
        {
            showCVdocuments();
        } 
        
    }

    function showSocialMedia()
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
		$showAjaxProjects = $langStrings['showAjaxProjects'];

		$showAjaxProjects_array = getLangstringsArray('showAjaxProjects_array', $displayLang);
        
        $table = "`".PREFIX."social_media`";
        $table2 = "`".PREFIX."social_media2user`";
        
        $tableKey = mysqli_real_escape_string($link, $_POST['id']);
        
        $sql = "SELECT *, CAST(AES_DECRYPT(t1.note, SHA2('".$phrase."', 512)) AS CHAR) as note, CASE WHEN t2.userTableKey IS NULL THEN t1.tableKey END as userTableKey FROM ".$table." t1 LEFT OUTER JOIN (SELECT *, tableKey as userTableKey, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as userUrl FROM $table2 WHERE userId = '".$_SESSION['uid']."') as t2 ON t1.socialMediaId = t2.socialMediaId WHERE t1.tableKey = '".$tableKey."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result))
        {
            echo "<div class=\"form-group row\">";
                echo "<label for=\"note[".$row['userTableKey']."]\" class=\"col-sm-2 col-form-label\">".$row['note']."</label>";
                echo "<div class=\"col-sm-10\">";
                    echo "<input type=\"text\" class=\"form-control syncData\" id=\"note[".$row['userTableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."social_media2user"]."\" value = \"".$row['userUrl']."\" placeholder = \"".$row['note']."\">";
                echo "</div>";
            echo "</div>";
        }
    } 
    
    function showCVworkExperience()
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
		$showAjaxProjects = $langStrings['showAjaxProjects'];

		$showAjaxProjects_array = getLangstringsArray('showAjaxProjects_array', $displayLang);
        
        $table = "`".PREFIX."CVworkExperience`";
        $table2 = "`".PREFIX."CVworkExperience_lang`";
        
        $tableKey = mysqli_real_escape_string($link, $_POST['id']);
        
        $sql = "SELECT *, CAST(AES_DECRYPT(t1.note, SHA2('".$phrase_k."', 512)) AS CHAR) as note, CASE WHEN t2.userTableKey IS NULL THEN t1.tableKey END as userTableKey FROM ".$table." t1 LEFT OUTER JOIN (SELECT *, tableKey as userTableKey, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as userUrl FROM $table2 WHERE userId = '".$_SESSION['uid']."') as t2 ON t1.socialMediaId = t2.socialMediaId WHERE t1.tableKey = '".$tableKey."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

        while ($row = mysqli_fetch_array($result))
        {
            echo "<div class=\"form-group row\">";
                echo "<label for=\"note[".$row['userTableKey']."]\" class=\"col-sm-2 col-form-label\">".$row['note']."</label>";
                echo "<div class=\"col-sm-10\">";
                    echo "<input type=\"text\" class=\"form-control syncData\" id=\"note[".$row['userTableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."social_media2user"]."\" value = \"".$row['userUrl']."\" placeholder = \"".$row['note']."\">";
                echo "</div>";
            echo "</div>";
        }
    }   

    function showCVdocuments()
    {
        global $link;
        global $link_k;

        global $phrase;
        global $phrase_k;
        
        $table = "`".PREFIX."user`";
        
        $sql = "SELECT * FROM ".$table." WHERE autoId = '".$_SESSION['uid']."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        while ($row = mysqli_fetch_array($result))
        {
            $profile = $row['tableKey'];
        }
        
        $file = mysqli_real_escape_string($link, $_POST['id']);
        
        if ($_SERVER['SERVER_NAME'] === 'localhost')
		{
			$url = "//localhost/lv/easyproject/";
			$url_admin = "//localhost/lv/easyproject/administrado/";
			$url_servotablo = "//localhost/lv/easyproject/servotablo/";
		}
		else if ($_SERVER['SERVER_NAME'] === 'server01')
		{
			$url = "//server01/flexshare/ep/";
			$url_admin = "//server01/flexshare/ep/administrado/";
			$url_servotablo = "//server01/flexshare/ep/servotablo/";
		}
		else
		{
			$url = "//mina-projekt.se/";
			$url_admin = "//mina-projekt.se/administrado/";
			$url_servotablo = "//mina-projekt.se/servotablo/";
		}
        
        echo "<iframe src = \"".$url."/common/getFile.php?profile=".$profile."&file=".$file."\" style = \"width:100%; heigth:100%;\">";
        
        //include_once("../../../common/getFile.php");
    }
?>