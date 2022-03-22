<?php

error_reporting(E_ALL);

session_start();
session_destroy();
session_start();

include_once("./common/db.php");
include_once("./common/userData.php");
include_once("./administrado/common/newsLetterTemplate.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './ext/PHPMailer/src/Exception.php';
require './ext/PHPMailer/src/PHPMailer.php';
require './ext/PHPMailer/src/SMTP.php';

global $link;
    
global $phrase;

function runnewsletter()
{
	global $link;
    
	global $phrase;
	
	if ($_SERVER['SERVER_NAME'] === "server01")
	{
		$url = "https://www.lvteknik.se/";
		$path = "/var/flexshare/shares/lv/";
	}
	else if ($_SERVER['SERVER_NAME'] === "localhost")
	{
		$url = "https://www.lvteknik.se/";
		$path = $_SERVER['DOCUMENT_ROOT']."/";
	}
	else
	{
		$url = "https://www.lvteknik.se/";
		$path = $_SERVER['DOCUMENT_ROOT']."/";
	}

	$siteSettings = getSiteSettings();
	
	$table = "`".PREFIX."newsletter`";
	$table2 = "`".PREFIX."newsletter_lang`";
	
	$table10 = "`".PREFIX."newsletter_readers`";
	
	$time = date("Y-m-d H:i:s");
	
	$sql = "SELECT * FROM ".$table." WHERE date <= '".$time."' AND status = 1 AND sent IS NULL";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	$first = true;
	
	$lang = null;
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$temp = array_map("trim", explode(",", $row['groups']));
		
		foreach ($temp as $key => $value)
		{
			$temp2[] = "`group` LIKE '".$value." %'"." OR `group` LIKE '".$value."'"." OR `group` LIKE '% ".$value." %'"." OR `group` LIKE '% ".$value.",%'"." OR `group` LIKE '% ".$value."'"." OR `group` LIKE '% ".$value."' OR `group` LIKE '".$value.", %' OR `group` LIKE '".$value."'";
		}
		
		$sql = "SELECT *, CAST(AES_DECRYPT(lang, SHA2('".$phrase."', 512)) AS CHAR) as lang, CAST(AES_DECRYPT(name, SHA2('".$phrase."', 512)) AS CHAR) as name, CAST(AES_DECRYPT(email, SHA2('".$phrase."', 512)) as CHAR) as email  FROM ".$table10." t10 WHERE (".implode($temp2, " OR ") .") AND status > 0 ORDER BY lang";
		//echo __LINE__." ".$sql."<br>";
		$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
		{
			if ($row2['lang'] !== $lang)
			{
				$lang = $row2['lang'];
				
				$sql = "SELECT *, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM ".$table2." WHERE newsletterId = '".$row['newsletterId']."' AND lang = AES_ENCRYPT('".$row2['lang']."', SHA2('".$phrase."', 512))";
				//echo __LINE__." ".$sql."<br>";
				$result3 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
				while ($row3 = mysqli_fetch_array($result3, MYSQLI_ASSOC))
				{
					$note = $row3['note'];
					echo __LINE__." ".$sql."<br>";
					$template = rendernewslettertemplate($lang);
					echo __LINE__." ".$sql."<br>";
				}
				$langStrings = getlangstrings((array)$lang);
				$runNewsletter = $langStrings['runNewsletter'];
				
				//$runNewsletter_array = getLangstringsArray('runNewsletter_array', $lang);
				
				$html = str_replace("~~newsletterText~~", $note, $template);
				$html = str_replace("src=\"../../", "style = \"max-width : 500px;\" src=\"".$url, $html);
			}
			if ($_SERVER['SERVER_NAME'] === "server01")
			{
				$url = "http://server01/flexshare/lv/";
			}
			
			$endNewsletter = $url."manageNewsletter.php?readerKey=".$row2['tableKey'];
			
			$messageHtml = str_replace("~~endNewsletter~~", $endNewsletter, $html);	
			
			$messageHtml = str_replace("~~reciverNewsletterName~~", $row2['name'], $messageHtml);
		
			$to = $To = $row2['email']; 
			$from = $siteSettings['sender_newsletter_email'];

			$subject = $runNewsletter[1]; 

			$headers = "MIME-Version: 1.0" . "\r\n"; 
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 

			// Additional headers 
			$headers .= 'From: '.$from. "\r\n";
			$headers .= 'Reply-To: ' . $siteSettings['replay_newsletter_mail'] . "\r\n";

			if (!empty($siteSettings['sender_newsletter_email_password']))	
			{
				$mail = new PHPMailer();
				
				$mail->IsHTML(true);
				$mail->isSMTP(); 
				$mail->Host = 'mailcluster.loopia.se'; 
				$mail->SMTPAuth = true; 
				$mail->Username = $siteSettings['sender_newsletter_email']; // SMTP username
				$mail->Password = $siteSettings['sender_newsletter_email_password']; // SMTP password
				$mail->SMTPSecure = 'tls'; 
				$mail->Port = 587;
				$mail->CharSet = 'UTF-8';
				
				$fromname = "LV Newsletter";

				$mail->addAddress($to);

				$mail->From = $from;
				$mail->FromName = $fromname; 
				$mail->SMTPDebug = 0;
				
				$mail->ClearReplyTos();
    			$mail->addReplyTo($siteSettings['replay_newsletter_mail'], 'Magnus Wedin : Ljus & Vattenteknik');
				
				$mail->Subject = $subject; 
				$mail->Body = $messageHtml;
				$mail->AddAddress($To); 
				$mail->set('X-Priority', '1'); //Priority 1 = High, 3 = Normal, 5 = low
				$mail->Send();	
			}
			else
			{
				if(mail($to, $subject, $messageHtml, $headers))
				{ 
					echo 'Email has sent successfully.';
				}
				else
				{ 
				   echo 'Email sending failed.'; 
				}
			}
		}
	}
	
	$sql = "UPDATE ".$table." SET sent = NOW() WHERE date <= '".$time."' AND status = 1 AND sent IS NULL";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
}

function runremindernewsletter()
{
	global $link;
    
	global $phrase;

	$siteSettings = getSiteSettings();
	
	if ($_SERVER['SERVER_NAME'] === "server01")
	{
		$url = "https://www.lvteknik.se/";
		$path = "/var/flexshare/shares/lv/";
	}
	else if ($_SERVER['SERVER_NAME'] === "localhost")
	{
		$url = "https://www.lvteknik.se/";
		$path = $_SERVER['DOCUMENT_ROOT']."/";
	}
	else
	{
		$url = "https://www.lvteknik.se/";

		$path = $_SERVER['DOCUMENT_ROOT']."/";
	}
	
	$table = "`".PREFIX."newsletter_reminder`";
	$table2 = "`".PREFIX."newsletter_reminder_lang`";
	
	$table3 = "`".PREFIX."cronlog_newsletter_reminder`";
	$table10 = "`".PREFIX."newsletter`";
	
	$table20 = "`".PREFIX."users`";
	
	$sql = "SELECT * FROM ".$table." WHERE YEAR() + '-' + `date` = '2021-07-21'";
	$sql = "SELECT *, STR_TO_DATE(concat(YEAR(NOW()), '-', t1.`date`),'%Y-%m-%d') as reminderDate FROM ".$table." t1 HAVING DATE_ADD(reminderDate, INTERVAL 14 DAY) >= CURRENT_DATE && reminderDate <= DATE_ADD(CURRENT_DATE, INTERVAL 14 DAY)";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	$first = true;
	
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		$sql = "SELECT * FROM ".$table3." t3 WHERE (t3.date >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)) AND newsletterReminderId = '".$row['newsletterReminderId']."'";
		$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		if (mysqli_num_rows($result2) == 0)
		{
			$sql = "SELECT * FROM ".$table10." WHERE date >= ".$row['reminderDate']." AND sent IS NULL LIMIT 1";
			$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

			$blockBtn = true;

			if (mysqli_num_rows($result2) > 0)
			{
				$reciver_reminder_newsletter = $siteSettings['reciver_reminder_newsletter'];

				$temp = array_map("trim", explode(";", $reciver_reminder_newsletter));

				foreach ($temp as $key => $value)
				{
					$userSetting = getUserSettings($value);

					if (empty($userSetting['displayLang']))
					{
						$userSetting['displayLang'] = 'sv';
					}

					$langStrings = getlangstrings($userSetting['displayLang']);

					$runReminderNewsletter = $langStrings['runReminderNewsletter'];
					$addUserNewsletter = $langStrings['addUserNewsletter'];

					$sql = "SELECT CAST(AES_DECRYPT(firstName, SHA2('".$phrase."', 512)) AS CHAR) as firstName, CAST(AES_DECRYPT(sureName, SHA2('".$phrase."', 512)) AS CHAR) as sureName, CAST(AES_DECRYPT(email, SHA2('".$phrase."', 512)) AS CHAR) as email FROM ".$table20." WHERE autoId = '".$value."'";

					$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

					$row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);

					$_POST['name'] = $row2['firstName']." ".$row2['sureName'];

					$email = $row2['email'];

					$sql = "SELECT *, CAST(AES_DECRYPT(note, SHA2('".$phrase."', 512)) AS CHAR) as note FROM ".$table2." WHERE newsletterReminderId = '".$row['newsletterReminderId']."' AND lang = AES_ENCRYPT('".$userSetting['displayLang']."', SHA2('".$phrase."', 512))";
					$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

					$row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);

					$html = "<!DOCTYPE html>";
					$html .= "<html lang=\"".key($displayLang)."\" xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">";
					$html .= "<head>";
					  $html .= "<meta charset=\"utf-8\">";
					  $html .= "<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\">";
					  $html .= "<meta name=\"x-apple-disable-message-reformatting\">";
					  $html .= "<title></title>";
					  $html .= "<!--[if mso]>";
					  $html .= "<style>";
						$html .= "table {border-collapse:collapse;border-spacing:0;border:none;margin:0;}";
						$html .= "div, td {padding:0;}";
						$html .= "div {margin:0 !important;}";
					  $html .= "</style>";
					  $html .= "<noscript>";
						$html .= "<xml>";
						  $html .= "<o:OfficeDocumentSettings>";
							$html .= "<o:PixelsPerInch>96</o:PixelsPerInch>";
						  $html .= "</o:OfficeDocumentSettings>";
						$html .= "</xml>";
					  $html .= "</noscript>";
					  $html .= "<![endif]-->";
					  $html .= "<style>";
						$html .= "table, td, div, h1, p {";
						  $html .= "font-family: Arial, sans-serif;";
						$html .= "}";
						$html .= "@media screen and (max-width: 530px) {";
						  $html .= ".unsub {";
							$html .= "display: block;";
							$html .= "padding: 8px;";
							$html .= "margin-top: 14px;";
							$html .= "border-radius: 6px;";
							$html .= "background-color: #555555;";
							$html .= "text-decoration: none !important;";
							$html .= "font-weight: bold;";
						  $html .= "}";
						  $html .= ".col-lge {";
							$html .= "max-width: 100% !important;";
						  $html .= "}";
						$html .= "}";
						$html .= "@media screen and (min-width: 531px) {";
						  $html .= ".col-sml {";
							$html .= "max-width: 35% !important;";
						  $html .= "}";
						  $html .= ".col-lge {";
							$html .= "max-width: 65% !important;";
						  $html .= "}";
						$html .= "}";
					  $html .= "</style>";
					$html .= "</head>";
					$html .= "<body style=\"margin:0;padding:0;word-spacing:normal;background-color:#939297;\">";
					  $html .= "<div role=\"article\" aria-roledescription=\"email\" lang=\"en\" style=\"text-size-adjust:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;background-color:#939297;\">";
						$html .= "<table role=\"presentation\" style=\"width:100%;border:none;border-spacing:0;\">";
						  $html .= "<tr>";
							$html .= "<td align=\"center\" style=\"padding:0;\">";
							 $html .= " <!--[if mso]>";
							  $html .= "<table role=\"presentation\" align=\"center\" style=\"width:600px;\">";
							  $html .= "<tr>";
							  $html .= "<td>";
							  $html .= "<![endif]-->";
							  $html .= "<table role=\"presentation\" style=\"width:94%;max-width:600px;border:none;border-spacing:0;text-align:left;font-family:Arial,sans-serif;font-size:16px;line-height:22px;color:#363636;\">";
								$html .= "<tr>";
								  $html .= "<td style=\"padding:40px 30px 30px 30px;text-align:center;font-size:24px;font-weight:bold;\">";
									$html .= "<a href=\"https://www.lvteknik.se/\" style=\"text-decoration:none;\"><img src=\"".$url."img/lvteknik_300.png\" width=\"165\" alt=\"Logo\" style=\"width:80%;max-width:165px;height:auto;border:none;text-decoration:none;color:#ffffff;\"></a>";
								  $html .= "</td>";
								$html .= "</tr>";
								$html .= "<tr>";
								  $html .= "<td style=\"padding:30px;background-color:#ffffff;\">";
									$html .= "<h1 style=\"margin-top:0;margin-bottom:16px;font-size:26px;line-height:32px;font-weight:bold;letter-spacing:-0.02em;\">".str_replace("~~reciverName~~", $_POST['name'],$addUserNewsletter[1])."</h1>";
									//$html .= "<p style=\"margin:0;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In tempus adipiscing felis, sit amet blandit ipsum volutpat sed. Morbi porttitor, <a href=\"http://www.example.com/\" style=\"color:#e50d70;text-decoration:underline;\">eget accumsan dictum</a>, nisi libero ultricies ipsum, in posuere mauris neque at erat.</p>";
									$html .= $row2['note'];


								  $html .= "</td>";
								$html .= "</tr>";

								if (!$blockBtn)
								{
									$html .= "<tr>";
									  $html .= "<td style=\"padding:35px 30px 11px 30px;font-size:0;background-color:#ffffff;border-bottom:1px solid #f0f0f5;border-color:rgba(201,201,207,.35);\">";
										$html .= "<!--[if mso]>";
										$html .= "<table role=\"presentation\" width=\"100%\">";
										$html .= "<tr>";
										$html .= "<td style=\"width:165px;\" align=\"left\" valign=\"top\">";
										$html .= "<![endif]-->";
										$html .= "<div class=\"col-sml\" style=\"display:inline-block;width:100%;max-width:165px;vertical-align:top;text-align:left;font-family:Arial,sans-serif;font-size:14px;color:#363636;\">";
										  //$html .= "<img src=\"https://assets.codepen.io/210284/icon.png\" width=\"115\" alt=\"\" style=\"width:80%;max-width:115px;margin-bottom:20px;\">";
										$html .= "</div>";
										$html .= "<!--[if mso]>";
										$html .= "</td>";
										$html .= "<td style=\"width:395px;padding-bottom:20px;\" valign=\"top\">";
										$html .= "<![endif]-->";
										$html .= "<div class=\"col-lge\" style=\"display:inline-block;width:100%;max-width:395px;vertical-align:top;padding-bottom:20px;font-family:Arial,sans-serif;font-size:16px;line-height:22px;color:#363636;\">";
										  //$html .= "<p style=\"margin-top:0;margin-bottom:12px;\">Nullam mollis sapien vel cursus fermentum. Integer porttitor augue id ligula facilisis pharetra. In eu ex et elit ultricies ornare nec ac ex. Mauris sapien massa, placerat non venenatis et, tincidunt eget leo.</p>";
										  //$html .= "<p style=\"margin-top:0;margin-bottom:18px;\">Nam non ante risus. Vestibulum vitae eleifend nisl, quis vehicula justo. Integer viverra efficitur pharetra. Nullam eget erat nibh.</p>";
										  $html .= "<p style=\"margin:0;\"><a href=\"~~newsletterConfirmUrl~~\" style=\"background: #ff3884; text-decoration: none; padding: 10px 25px; color: #ffffff; border-radius: 4px; display:inline-block; mso-padding-alt:0;text-underline-color:#ff3884\"><!--[if mso]><i style=\"letter-spacing: 25px;mso-font-width:-100%;mso-text-raise:20pt\">&nbsp;</i><![endif]--><span style=\"mso-text-raise:10pt;font-weight:bold;\">".$addUserNewsletter[3]."</span><!--[if mso]><i style=\"letter-spacing: 25px;mso-font-width:-100%\">&nbsp;</i><![endif]--></a></p>";
										$html .= "</div>";
										$html .= "<!--[if mso]>";
										$html .= "</td>";
										$html .= "</tr>";
										$html .= "</table>";
										$html .= "<![endif]-->";
									  $html .= "</td>";
									$html .= "</tr>";
								}

								$html .= "<tr>";
								  $html .= "<td style=\"padding:30px;text-align:center;font-size:12px;background-color:#404040;color:#cccccc;\">";
									//$html .= "<p style=\"margin:0 0 8px 0;\"><a href=\"http://www.facebook.com/\" style=\"text-decoration:none;\"><img src=\"https://assets.codepen.io/210284/facebook_1.png\" width=\"40\" height=\"40\" alt=\"f\" style=\"display:inline-block;color:#cccccc;\"></a> <a href=\"http://www.twitter.com/\" style=\"text-decoration:none;\"><img src=\"https://assets.codepen.io/210284/twitter_1.png\" width=\"40\" height=\"40\" alt=\"t\" style=\"display:inline-block;color:#cccccc;\"></a></p>";
									$html .= "<p style=\"margin:0;font-size:14px;line-height:20px;\">&reg; LV Teknik ".date("Y")."<br></p>";
								  $html .= "</td>";
								$html .= "</tr>";

							$html ."</tr>";
							  $html .= "</table>";
							  $html .= "<!--[if mso]>";
							  $html .= "</td>";
							  $html .= "</tr>";
							  $html .= "</table>";
							  $html .= "<![endif]-->";
							$html .= "</td>";
						  $html .= "</tr>";

						$html .= "</table>";
					  $html .= "</div>";
					$html .= "</body>";
				$html .= "</html>";

				$html = str_replace("~~newsletterConfirmUrl~~", $validateUrl, $html);

					$to = $email; 
					$from = $siteSettings['sender_newsletter_email'];

					$subject = $runReminderNewsletter[1]; 

					$headers = "MIME-Version: 1.0" . "\r\n"; 
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 

					// Additional headers 
					$headers .= 'From: '.$from.'' . "\r\n"; 

					echo __LINE__." ".$to."<br>";
					echo __LINE__." ".$subject."<br>";
					echo __LINE__." ".$headers."<br>";	

					if (!empty($siteSettings['sender_newsletter_email_password']))	
					{
						$mail = new PHPMailer();  
						$mail->IsHTML(true);
						$mail->isSMTP(); 
						$mail->Host = 'mailcluster.loopia.se'; 
						$mail->SMTPAuth = true; 
						$mail->Username = $siteSettings['sender_newsletter_email']; // SMTP username
						$mail->Password = $siteSettings['sender_newsletter_email_password']; // SMTP password
						$mail->SMTPSecure = 'tls'; 
						$mail->Port = 587;
						$mail->CharSet = 'UTF-8';
						$fromname = "LV Newsletter";

						$To = trim($email,"\r\n");

						$mail->From = $from;
						$mail->FromName = $fromname;        
						$mail->Subject = $subject; 
						$mail->Body = $html;
						$mail->AddAddress($To); 
						$mail->set('X-Priority', '1'); //Priority 1 = High, 3 = Normal, 5 = low
						$mail->Send();	
					}
					else
					{
						//if(mail($to, $subject, $html, $headers))
						if(1 == 1)	
						{ 
							echo 'Email has sent successfully.';

							if ($first)
							{
								$sql = "INSERT INTO ".$table3." (newsletterReminderId, date) VALUES ('".$row['newsletterReminderId']."', NOW())";
								$result2 =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

								$first = false;
							}
						}
						else
						{ 
						   echo 'Email sending failed.'; 
						}
					}
				}
			}
		}
	}
}

//$siteSettings = getSiteSettings();
runnewsletter();
runremindernewsletter();
?>