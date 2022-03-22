<?php

	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    include_once("../common/db.php");
    include_once("../common/crypto.php");
    include_once("../common/userData.php");

    global $link;

    global $phrase;

    if ($_SERVER['SERVER_NAME'] === 'localhost')
    {
        $url = "//localhost/lv/";
    }
    else if ($_SERVER['SERVER_NAME'] === 'server01')
    {
        $url = "//server01/flexshare/lv/";
    }
    else
    {
        $url = "//www.lvteknik.se/";
    }

    $siteSettings = getSiteSettings();

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

	$langStrings = getlangstrings($displayLang);

    $resetPassword = $langStrings['resetPassword'];

    $resetPassword_array = getLangstringsArray('resetPassword_array', $displayLang);

	$sendMail = $langStrings['sendMail'];

    $body = null;

    $table = "`".PREFIX."users`";

    $table10 = "`".PREFIX."reset_password_keys`";

    $sql = "SELECT * FROM ".$table." WHERE email = AES_ENCRYPT('".mysqli_real_escape_string($link,  $_POST['email'])."', SHA2('".$phrase."', 512))";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
    if (mysqli_num_rows($result) == 0)
    {
        return false;
    }

    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $userId = $row['autoId'];
    }

    $run = true;
    do
    {
        $resetKey = generateStrongPassword(30);
        
        $sql = "SELECT * FROM ".$table10." WHERE tableKey = '".$resetKey."'";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        if (mysqli_num_rows($result) == 0)
        {
            $run = false;
        }
        
    } while ($run);

    $sql = "INSERT INTO ".$table10." (tableKey, userId, endTime, remoteIp) VALUES ('".$resetKey."', '".$userId."', ADDTIME(NOW(), '12:00:00'), AES_ENCRYPT('".mysqli_real_escape_string($link, $_SERVER['REMOTE_ADDR'])."', SHA2('".$phrase."', 512)))";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

    $insertId = mysqli_insert_id($link);

    $sql = "SELECT endTime FROM ".$table10." WHERE autoId = '".$insertId."'";
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $endTime = $row['endTime'];
    }

    $replaceUrl = "<a href = \"".$url."resetPassword.php?tableKey=".$resetKey."\">".$url."resetPassword.php?tableKey=".$resetKey."</a>";

    $search[] = "~~serverTime~~";
    $search[] = "~~resetUrl~~";

    $replace[] = $endTime;
    $replace[] = $replaceUrl;

    $body .= str_replace($search,$replace,$resetPassword[2]);

    if (!empty($siteSettings['email_server']) && !empty($siteSettings['email_password']))
    {
        $mail = new PHPMailer();
        $mail->isSMTP(); 
        $mail->Host = 'mailcluster.loopia.se'; 
        $mail->SMTPAuth = true; 
        $mail->Username = $siteSettings['email_server']; // SMTP username
        $mail->Password = $siteSettings['email_password']; // SMTP password
        $mail->SMTPSecure = 'tls'; 
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->From = $siteSettings['email_server'];
        $mail->FromName = 'LV Teknik';
        $mail->addAddress('wedin@lvteknik.se'); // Add a recipient
        $mail->addReplyTo($_POST['email'], $_POST['name']);
        $mail->Subject = $resetPassword[4];
        $mail->Body = $body;
        $mail->AltBody = str_replace("<br>", "\r\n", $body);
        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    }
    else
    {
        // Multiple recipients
		$to = $_POST['email']; // note the comma

		// Subject
		$subject = $resetPassword[4];

		// Message
		$message = "
		<html>
		<head>
		  <title>".$resetPassword[4]."</title>
		</head>
		<body>
		  <p>".$body."</p>

		</body>
		</html>";

		// To send HTML mail, the Content-type header must be set
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=utf-8';

		// Additional headers
		$headers[] = "To: ".$_POST['email']."";
		$headers[] = 'From: Magnus Wedin<wedin@lvteknik.se>';
		$headers[] = 'Reply-To: <wedin@lvteknik.se>';	

		// Mail it
		mail($to, $subject, $message, implode("\r\n", $headers));
	
	}

	echo "<span id = \"resetMailMessage\">".$resetPassword[3]."<span\">";