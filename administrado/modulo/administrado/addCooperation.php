<?php
    session_start();

    error_reporting(E_ALL);

    include_once("../../../common/db.php");
    include_once("../../../common/crypto.php");
    include_once("../../../common/db_def.php");
    include_once("./../../ext/theme/nav.php");
    include_once("../../../common/userData.php");
    include_once("./documentFiles.php");
    include_once("../../common/emptor.php");

    include_once("./manual.php");
    include_once("./../../common/syncDB.php");

    include_once("../../../responsiveEmail.php");
    include_once("../../../addUser.php");

    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))
    {
        $replaceTable = getReplaceTable(false);
        
        if ($replaceTable[$_POST['replaceTable']] === PREFIX.'account')
        {
            addCooperationAccount();
        }
    }

    function addCooperationAccount()
    {
        global $link;
        global $link_k;

        global $phrase;
        global $phrase_k;
        
        $table = "`".PREFIX."user`";
        
        $table10 = "`".PREFIX."account`";
        $table20 = "`".PREFIX."user2account`";
        
        $run = true;
        
        foreach ($_POST as $key => $value)
        {
            if (!$run)
            {
                break;
            }
            
            if ($key == "replaceTable")
            {
                continue;
            }
            
            if ($key == "accountName")
            {
                $accountName = mysqli_real_escape_string($link, $value);
            }
            else
            {
                foreach ($value as $sub_key => $sub_value)
                {
                    if ($key == "email")
                    {
                        if (filter_var($sub_value, FILTER_VALIDATE_EMAIL)) 
                        {
                            $user[$sub_key][$key] = mysqli_real_escape_string($link, $sub_value);
                        }
                        else
                        {
                            $run = false;
                        }
                    }
                    else
                    {
                        if (strlen(trim($sub_value)) > 0)
                        {
                            $user[$sub_key][$key] = mysqli_real_escape_string($link, $sub_value);
                        }
                        else
                        {
                            $run = $false;
                        }
                    }
                }
            }
        }
        
        if (isset($_POST['deleteUser']))
        {
            $sql = "SELECT t20.autoId FROM ".$table20." t20 INNER JOIN ".$table10." as t10 ON t20.accountId = t10.autoId INNER JOIN ".$table." as t1 ON t1.autoId = t20.userId WHERE t1.tableKey = '".mysqli_real_escape_string($link, $_POST['deleteUser'])."' AND t10.replaceKey = '".mysqli_real_escape_string($link, $_POST['projectKey'])."'";
            echo __LINE__." ".$sql."<br>";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $autoId = $row['autoId'];
                
                $sql = "DELETE FROM ".$table20." WHERE autoId = '".$row['autoId']."'";
                $result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            }
            
            return true;
        }
        
        if ($run)
        {
            
            
            if (isset($_POST['projectKey']))
            {
                $sql = "SELECT * FROM ".$table10." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['projectKey'])."'";
                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                {
                    $accountId = $row['autoId'];
                }
            }
            
            else
            {
                $sql = "INSERT INTO ".$table10." (accountName, typeAccount, accountPlan) VALUES (AES_ENCRYPT('".$accountName."', SHA2('".$phrase."', 512)), 'collaboration', '1')";
                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

                $accountId = mysqli_insert_id($link);
            }
            
            $run = true;
            
            do 
            {
                $phrase = generateStrongPassword();
                
                $sql = "SELECT * FROM ".$table10." WHERE replaceKey = '".$phrase."'";
                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                
                if (mysqli_num_rows($result))
                {
                    $run = false;
                }
            } while ($run);
            
            create_db_for_account($accountId, $phrase);
            generateDbConnection($accountId);
            
            if (!isset($_POST['projectKey']))
            {
                $sql = "INSERT INTO ".$table20." (accountId, userId) VALUES ('".$accountId."', '".$_SESSION['uid']."')";
                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            }

            foreach ($user as $key => $value)
            {
                $sql = "SELECT *  FROM ".$table." WHERE email = AES_ENCRYPT('".$value['email']."', SHA2('".$phrase."', 512))";
                $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

                if (mysqli_num_rows($result) > 0)
                {
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                    {
                        $userId = $row['autoId'];
                    }

                    $sql = "SELECT * FROM ".$table20." WHERE accountId = '".$accountId."' AND userId = '".$userId."'";
                    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                    
                    if (mysqli_num_rows($result) == 0)
                    {
                        $sql = "INSERT INTO ".$table20." (accountId, userId) VALUES ('".$accountId."', '".$userId."')";
                        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                    }
                                
                    $reciverMail = $value['email'];

                    $userSettings = getUserSettings($userId);

                    $lang = reset(array_map("trim", explode(",", $userSettings['langService'])));

                    echo __LINE__." ".$lang."<br>";

                    $langStrings = getlangstrings($lang);

                    $addCooperationAccount[1] = $langStrings['addCooperationAccount[1]'];

                    $message = str_replace("~~senderName~~", $firstName." ".$sureName[0], $addCooperationAccount[1]);

                    $message = str_replace("~~collaboration~~", "\"".$accountName."\"", $message);

                    $message = str_replace("~~collaborationMessage~~", $_POST['contactMessage'], $message);/**/

                    $header = str_replace("~~collaboration~~", "\"".$accountName."\"",$addCooperationAccount[2]);

                    print_r($userId);
                    echo "<br>";

                    sendMail($userId, $message, $short = null, null, $header, null, $replayMail);
                }
                else
                {
                    $sql = "INSERT INTO ".$table." (firstName, sureName, email) VALUES (AES_ECNRYPT('".$value['fristName']."', SHA2('".$phrase."', 512)), AES_ECNRYPT('".$value['sureName']."', SHA2('".$phrase."', 512)),AES_ECNRYPT('".$value['email']."', SHA2('".$phrase."', 512)))";
                    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                    
                    $userId = mysqli_insert_id($link);
                    
                    $sql = "INSERT INTO ".$table20." (accountId, userId) VALUES ('".$accountId."', '".$userId."')";
                    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
                    
                    $reciverMail = $value['email'];

                    $userSettings = getUserSettings($userId);

                    $lang = reset(array_map("trim", explode(",", $userSettings['langService'])));

                    echo __LINE__." ".$lang."<br>";

                    $langStrings = getlangstrings($lang);

                    $addCooperationAccount[1] = $langStrings['addCooperationAccount[3]'];

                    $message = str_replace("~~senderName~~", $firstName." ".$sureName[0], $addCooperationAccount[1]);

                    $message = str_replace("~~collaboration~~", "\"".$accountName."\"", $message);

                    $message = str_replace("~~collaborationMessage~~", $_POST['contactMessage'], $message);/**/

                    $header = str_replace("~~collaboration~~", "\"".$accountName."\"",$addCooperationAccount[2]);

                    sendMail($userId, $message, $short = null, null, $header, null, $replayMail);
                    
                    ajax_regUser($value['fristName'], $value['sureName'], $value['email']);
                }
            }
            
            $sql = "SELECT * FROM ".$table10." WHERE replaceKey IS NULL OR replaceKey = ''";
            $result= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link));

            while ($row = mysqli_fetch_array($result))
            {
                $run = true;

                do
                {
                    $tableKey = generateStrongPassword(14);

                    $sql = "SELECT * FROM ".$table10." WHERE replaceKey = '".$tableKey."'";
                    $result2= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link));

                    if (mysqli_num_rows($result2) === 0)
                    {
                        $sql = "UPDATE ".$table10 ." SET replaceKey = '".$tableKey."' WHERE autoId = '".$row['autoId']."'";

                        $result2= mysqli_query($link, $sql) or die ("Error ".__LINE__ ." " .basename(__FILE__) ." ".$sql." : ".mysqli_error ($link));

                        $run = false;
                    }

                } while ($run);
            }
            checkTable($table);
        }
    }
?>