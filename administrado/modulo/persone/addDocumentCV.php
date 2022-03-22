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
 
        if ($replaceTable[$_POST['replaceTable']] === PREFIX_K.'CVdocuments')
        {
            showAddDocumentCV();
        }        
    }

    function showAddDocumentCV()
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
        $showAddDocumentCV = $langStrings['showAddDocumentCV'];

        $showAddDocumentCV_array = getLangstringsArray('showAddDocumentCV_array', $displayLang);
        
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
        
        echo "<div id = \"ajaxHeaderModal\">";

        echo "</div>";

        echo "<div id = \"ajaxBodyModal\">";
        
            $table = "`".PREFIX."user2account`";
            $table2 = "`".PREFIX."account`";

            if ($_SERVER['SERVER_NAME'] === 'localhost')
            {
                $url = "//localhost/lv/easyproject/";
                $url_admin = "//localhost/lv/easyproject/administrado/";
                $url_servotablo = "//localhost/lv/easyproject/servotablo/";
                $folder_url = "";
            }
            else if ($_SERVER['SERVER_NAME'] === 'server01')
            {
                $url = "//server01/flexshare/ep/";
                $url_admin = "//server01/flexshare/ep/administrado/";
                $url_servotablo = "//server01/flexshare/ep/servotablo/";
                $folder_url = "/var/flexshare/shares/ep/";
            }
            else
            {
                $url = "//mina-projekt.se/";
                $url_admin = "//mina-projekt.se/administrado/";
                $url_servotablo = "//mina-projekt.se/servotablo/";
            }
        
            $sql = "SELECT * FROM $table t1 LEFT JOIN $table2 t2 ON t1.accountId = t2.autoId  WHERE t1.userId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."'";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link));
            while ($row = mysqli_fetch_array($result))
            {
                if (!file_exists("../../../kliento/".substr($row['replaceKey'], 0,1)."/".substr($row['replaceKey'], 0,2)."/".$row['replaceKey']."/work_dir/cv/"))
                {
                    mkdir("../../../kliento/".substr($row['replaceKey'], 0,1)."/".substr($row['replaceKey'], 0,2)."/".$row['replaceKey']."/work_dir/cv/", 0777, true);
                    
                    mkdir("../../../kliento/".substr($row['replaceKey'], 0,1)."/".substr($row['replaceKey'], 0,2)."/".$row['replaceKey']."/work_dir/cv/thumbs/", 0777, true);
                    mkdir("../../../kliento/".substr($row['replaceKey'], 0,1)."/".substr($row['replaceKey'], 0,2)."/".$row['replaceKey']."/work_dir/cv/data/", 0777, true);
                }
                
                $_SESSION['folderUrl'] = "../../../kliento/".substr($row['replaceKey'], 0,1)."/".substr($row['replaceKey'], 0,2)."/".$row['replaceKey']."/work_dir/cv/";
            }
        
            echo "<iframe src=\"".$url."ext/responsive_filemanager/filemanager/dialogDokumenti.php\" style = \"width : 100%; height : 80vh; border : 0px;\">";
        
            //include_once("../../../ext/responsive_filemanager/filemanager/dialogDokumenti.php");
        echo "</div>";

        echo "<div id = \"ajaxFooterModal\">";
            echo "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>";
            //echo "<button type=\"button\" class=\"btn btn-primary\">Save changes</button>";
        echo "</div>";
    }
?>