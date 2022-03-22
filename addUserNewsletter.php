<?php

	include_once("./common/db.php");
	include_once("./common/userData.php");
	include_once("./common/crypto.php");
	include_once("./theme.php");

	if (isset($_POST['removeKey']))
	{
		$table = "`".PREFIX."newsletter_readers`";
		
		$sql = "DELETE FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['removeKey'])."'";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	}

	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
	{
		return false;
	}

	$repalaceLangRev = getReplaceLang(false);

	foreach ($_POST as $key => $value)
	{
		${mysqli_real_escape_string($link,$key)} = mysqli_real_escape_string($link, $value);
	}

	$lang = $repalaceLangRev[$lang];

	$siteSettings = getSiteSettings();

	$userSettings = getUserSettings();

	$displayLang[] = $repalaceLangRev[$lang];

	/*if (empty($userSettings['langService']))
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
	*/
	foreach ($displayLang as $key => $value)
	{
		$order[] = "WHEN lang = '".$value."' THEN ".$i;
		$order_lang[] = "WHEN code = '".$value."' THEN ".$i;
		$i++;
	}

	$langStrings = getlangstrings(reset($displayLang));
	$addUserNewsletter = $langStrings['addUserNewsletter'];

	$renderNewsLetterTemplate = $langStrings['renderNewsLetterTemplate'];

	if (isset($_GET['newsletterKey']))
	{
		echo "<!DOCTYPE html>";
        	echo "<html lang=\"".reset($displayLang)."\">";
	}
	else
	{
		if ($_SERVER['SERVER_NAME'] === "server01")
		{
			$url = "//server01/flexshare/lv/";
			$path = "/var/flexshare/shares/lv/";
		}
		else if ($_SERVER['SERVER_NAME'] === "localhost")
		{
			$url = "//localhost/";
			$path = $_SERVER['DOCUMENT_ROOT']."/";
		}
		else
		{
			$url = "//www.lvteknik.se/";

			$path = $_SERVER['DOCUMENT_ROOT']."/";
		}
		
		$table = "`".PREFIX."newsletter_readers`";
		
		$sql = "SELECT * FROM ".$table." WHERE email = AES_ENCRYPT('".$email."', SHA2('".$phrase."', 512))";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		if (mysqli_num_rows($result) > 0)
		{
			$addUserNewsletter[2] = $addUserNewsletter[4];
			$blockBtn = true;
			
			$sql = "UPDATE ".$table." SET status = 1, lang = '".$lang."'  WHERE email = AES_ENCRYPT('".$email."', SHA2('".$phrase."', 512))";
			$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		}
		else
		{
			$sql = "INSERT INTO ".$table." (`name`, `email`, `lang`, `addDate`, `group`) VALUES (AES_ENCRYPT('".$name."', SHA2('".$phrase."', 512)), AES_ENCRYPT('".$email."', SHA2('".$phrase."', 512)), AES_ENCRYPT('".$lang."', SHA2('".$phrase."', 512)), NOW(), 1)";
			$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
			
			$replaceKey = checkTable($table);
			$validateUrl = $url."verifyNewsletterEmail.php?tablekey=".$replaceKey;
		}
		
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
						$html .= $addUserNewsletter[2];
		
						
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
					/*$html .= "<tr>";
					  $html .= "<td style=\"padding:30px;font-size:24px;line-height:28px;font-weight:bold;background-color:#ffffff;border-bottom:1px solid #f0f0f5;border-color:rgba(201,201,207,.35);\">";
						$html .= "<a href=\"http://www.example.com/\" style=\"text-decoration:none;\"><img src=\"https://assets.codepen.io/210284/1200x800-1.png\" width=\"540\" alt=\"\" style=\"width:100%;height:auto;border:none;text-decoration:none;color:#363636;\"></a>";
					  $html .= "</td>";
					$html .= "</tr>";
					/*$html .= "<tr>";
					  $html .= "<td style=\"padding:30px;background-color:#ffffff;\">";
						$html .= "<p style=\"margin:0;\">Duis sit amet accumsan nibh, varius tincidunt lectus. Quisque commodo, nulla ac feugiat cursus, arcu orci condimentum tellus, vel placerat libero sapien et libero. Suspendisse auctor vel orci nec finibus.</p>";
					  $html .= "</td>";
					$html .= "</tr>";*/
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

		$subject = $addUserNewsletter[3]; 
		
		$headers = "MIME-Version: 1.0" . "\r\n"; 
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 

		// Additional headers 
		$headers .= 'From: '.$from.'' . "\r\n"; 

		if(mail($to, $subject, $html, $headers)){ 
			echo 'Email has sent successfully.'; 
		}else{ 
		   echo 'Email sending failed.'; 
		}
		
		//echo $html."<br>";
	}

    
?>