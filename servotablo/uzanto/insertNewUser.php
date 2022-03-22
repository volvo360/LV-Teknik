<?php
    session_start();

    error_reporting(E_ALL);

    include_once("../../common/db.php");
    include_once("../../administrado/ext/theme/nav.php");
    include_once("../../common/crypto.php");
    include_once("../../common/userData.php");

    $run = true;

    foreach ($_POST as $key => $value)
    {
        if ($key !== "repPassword")
        {
            $value = trim($value);
            
            if (strlen($value) == 0 )
            {
                $run = false;
            }
            
            $keyData[] = mysqli_real_escape_string($link, $key);
            
            if ($key == "password")
            {
                $valueData[] = "AES_ENCRYPT('".password_hash(mysqli_real_escape_string($link, $value), "PASSWORD_BCRYPT")."', SHA2('".$phrase."', 512))";
            }
            else
            {
                $valueData[mysqli_real_escape_string($link, $key)] = "AES_ENCRYPT('".mysqli_real_escape_string($link, $value)."', SHA2('".$phrase."', 512))";
            }
        }
    }

    if ($_POST['password'] !== $_POST['repPassword'])
    {
        $run = false;
    }
    
    else if (strlen(trim($_POST['password'])) < 6)
    {
        $run = false;
    }
    else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
    {
        $run = false;
    }

    if ($run)
    {
        $table1 = PREFIX."users";

        $sql = "SELECT * FROM ".$table1." WHERE email = ".$valueData['email'];
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        if (mysqli_num_rows($result) == 0)
        {
            $sql = "INSERT INTO ".$table1." (".implode(", ", (array)$keyData).") VALUES (".implode(", ", (array)$valueData).")";
            //echo __LINE__." ".$sql."<br>";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            checkTable($table1);
        }
    }
    else
    {
        echo "Error";
    }
    
?>