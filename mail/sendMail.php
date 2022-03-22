<?php

error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

include_once("../common/db.php");

include_once("../common/userData.php");

global $link;
    
global $phrase;

echo __LINE__." Hmmmm<br>";

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

$sendMail = $langStrings['sendMail'];

$sendMail_array = getLangstringsArray('sendMail_array', $displayLang);

$body = null;
    
if (strlen(($_POST['company'])) > 0)
{
    $body .= $sendMail[1]." ".$_POST['company']."<br><br>";
}

$body .= $_POST['name']. "<br>";

$body .= $_POST['message'];

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
    $mail->Subject = $sendMail[1];
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
$to = 'wedin@lvteknik.se'; // note the comma

// Subject
$subject = $sendMail[1];

// Message
$message = "
<html>
<head>
  <title>".$sendMail[1]."</title>
</head>
<body>
  <p>".$body."</p>
  
</body>
</html>";

// To send HTML mail, the Content-type header must be set
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=iso-8859-1';

// Additional headers
$headers[] = 'To: <wedin@lvteknik.se>';
$headers[] = "Reply-To: ".$_POST['email']."";

echo __LINE__." ". $to."<br>";
	echo __LINE__." ". $subject."<br>";
	echo __LINE__." ". $message."<br>";
	echo __LINE__." ". implode("<br>", $headers)."<br>";
	
// Mail it
mail($to, $subject, $message, implode("\r\n", $headers));
}


?>