<?php

    session_start();
    include_once("../../../common/db.php");
    include_once("./../../ext/theme/nav.php");
    include_once("../../../common/userData.php");

    include_once("../../../responsiveEmail.php");

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        $replaceTable = getReplaceTable(false);
        
        if ($replaceTable[$_POST['replaceTable']] === PREFIX.'account')
        {
            showContactFormCollaboration();
        }
        else
        {
            sendContactMessageCollaboration();
        }
    }

    function showContactFormCollaboration()
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
		$showContactFormCoowork = $langStrings['showContactFormCoowork'];

		$showContactFormCoowork_array = getLangstringsArray('showContactFormCoowork_array', $displayLang);
        
        echo "<div id = \"ajaxHeaderModal\">";
            echo "<h5 class=\"modal-title h4\" id=\"modalXlLabel\">".$showContactFormCoowork[1]."</h5>";
        echo "</div>";

        echo "<div id = \"ajaxBodyModal\">";
        
            $projectKey = mysqli_real_escape_string($link, $_POST['projectKey']);
        
            $table10 = "`".PREFIX."account`";
            $table20 = "`".PREFIX."user2account`";
            $table30 = "`".PREFIX."user`";

            $sql = "SELECT *, CAST(AES_DECRYPT(t30.firstName, SHA2('".$phrase."', 512)) AS CHAR) as firstName, CAST(AES_DECRYPT(t30.sureName, SHA2('".$phrase."', 512)) AS CHAR) as sureName FROM ".$table10." t10 INNER JOIN ".$table20." t20 ON t20.accountId = t10.autoId INNER JOIN ".$table30." t30 ON t30.autoId = t20.userId WHERE t10.replaceKey = '".$projectKey."' AND typeAccount = 'collaboration' ORDER BY t10.autoId";
            //echo __LINE__." ".$sql."<br>";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            
            if (mysqli_num_rows($result) > 0)
            {
                echo "<div class=\"form-group row\">";
                    echo "<label for=\"reciverMessage\" class=\"col-sm-2 col-form-label\">".$showContactFormCoowork[4]."</label>";
                    echo "<div class=\"col-sm-10\">";
                        echo "<select id = \"reciverMessage\" class = \"selectpicker2 show-tick\" multiple>";
                
                            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                            {
                                $replaceKey = $row['replaceKey'];
                                
                                echo "<option value = \"".$row['tableKey']."\"";
                                
                                    if ($row['tableKey'] === $_POST['conctactUser'])
                                    {
                                        echo " "."selected";
                                    }
                                
                                echo ">".$row['firstName']." ".$row['sureName']."</option>";
                            }

                        echo "</select>";
                    echo "</div>";
                echo "</div>";
                
                echo "<div class=\"form-group row\">";
                    echo "<label for=\"reciverMessage\" class=\"col-sm-2 col-form-label\">".$showContactFormCoowork[4]."</label>";
                
                    echo "<div class=\"col-sm-10\">";
                        echo "<textarea id = \"contactMessageCollaboration\" class = \"tinyMceArea\">";
                        echo "</textarea>";
                    echo "</div>";
                echo "</div>";
            }
            
        
        echo "</div>";

        echo "<div id = \"ajaxFooterModal\">";
            echo "<button type=\"button\" class=\"btn btn-warning\" data-dismiss=\"modal\">".$showContactFormCoowork[3]."</button>";
            echo "<button type=\"button\" class=\"btn btn-success ajaxContactMessageCollaboration\" data-replace_table = \"".$_POST['replaceTable']."\" data-project_key = \"".$replaceKey."\">".$showContactFormCoowork[2]."</button>";
        echo "</div>";
    }

    function sendContactMessageCollaboration()
    {
        global $link;
        global $link_k;
        
        global $phrase;
        global $phrase_k;
        
        $contactMessage = trim($_POST['contactMessage']);
        
        if (empty($contactMessage))
        {
            return false;
        }
        
        foreach ($_POST as $key => $value)
        {
            if ($key !== "replaceTable" && $key !== "contactMessage" && $key !== "project_key")
            {
                foreach ($_POST['reciver'] as $sub_key => $sub_value)
                {
                    $reciverKey = mysqli_real_escape_string($link, $sub_value);
                    $table30 = "`".PREFIX."user`";
                    
                    $table40 = "`".PREFIX."account`";
                    $table50 = "`".PREFIX."user`";
                    
                    $sql = "SELECT CAST(AES_DECRYPT(accountName, SHA2('".$phrase."', 512)) as CHAR) AS accountName FROM ".$table40;
                    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

                    if (mysqli_num_rows($result) > 0)
                    {
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                        {
                            $accountName = $row['accountName'];
                        }
                        
                        $sql = "SELECT CAST(AES_DECRYPT(firstName, SHA2('".$phrase."', 512)) as CHAR) AS firstName, CAST(AES_DECRYPT(sureName, SHA2('".$phrase."', 512)) as CHAR) AS sureName, CAST(AES_DECRYPT(email, SHA2('".$phrase."', 512)) as CHAR) AS email FROM ".$table50." WHERE autoId = '".mysqli_real_escape_string($link, $_SESSION['uid'])."'";
                        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                        
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                        {
                            $firstName = $row['firstName'];
                            $sureName = $row['sureName'];
                            $replayMail = $row['email'];
                        }
                        
                        $sql = "SELECT *, CAST(AES_DECRYPT(t30.firstName, SHA2('".$phrase."', 512)) AS CHAR) as firstName, CAST(AES_DECRYPT(t30.sureName, SHA2('".$phrase."', 512)) AS CHAR) as sureName, CAST(AES_DECRYPT(t30.email, SHA2('".$phrase."', 512)) AS CHAR) as email  FROM ".$table30." t30 WHERE t30.tableKey = '".$reciverKey."'";
                        echo __LINE__." ".$sql."<br>";
                        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

                        if (mysqli_num_rows($result) > 0)
                        {
                            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                            {
                                $userId = $row['autoId'];
                                
                                $reciverMail = $row['email'];

                                $userSettings = getUserSettings($userId);

                                $lang = reset(array_map("trim", explode(",", $userSettings['langService'])));

                                echo __LINE__." ".$lang."<br>";

                                $langStrings = getlangstrings($lang);

                                $sendContactMessageCollaboration = $langStrings['sendContactMessageCollaboration'];
                                
                                $message = str_replace("~~senderName~~", $firstName." ".$sureName[0], $sendContactMessageCollaboration[1]);
                                
                                $message = str_replace("~~collaboration~~", "\"".$accountName."\"", $message);
                                
                                $message = str_replace("~~collaborationMessage~~", $_POST['contactMessage'], $message);/**/
                                
                                $header = str_replace("~~collaboration~~", "\"".$accountName."\"",$sendContactMessageCollaboration[2]);
                                
                                print_r($userId);
                                echo "<br>";

                                sendMail($userId, $message, $short = null, null, $header, null, $replayMail);
                            } 
                        }
                    }
                }
            }
        }
    }
?>