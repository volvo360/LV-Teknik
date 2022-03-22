<?php

session_start();

include_once($path."common/db.php");
include_once($path."common/crypto.php");
include_once($path."common/userData.php");

function rendernewslettertemplate($lang = 'sv')
{
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
	
	
	$langStrings = getlangstrings($lang);
	$renderNewsLetterTemplate = $langStrings['renderNewsLetterTemplate'];
	$html = "<!DOCTYPE html>";
	$html .= "<html lang=\"".$lang."\" xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">";
	$html .= "<head>";
		$html .= "<meta charset=\"utf-8\"> <!-- utf-8 works for most cases -->";
		$html .= "<meta name=\"viewport\" content=\"width=device-width\"> <!-- Forcing initial-scale shouldn't be necessary -->";
		$html .= "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"> <!-- Use the latest (edge) version of IE rendering engine -->";
		$html .= "<meta name=\"x-apple-disable-message-reformatting\">  <!-- Disable auto-scale in iOS 10 Mail entirely -->";
		$html .= "<title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->";

		$html .= "<link href=\"https://fonts.googleapis.com/css?family=Lato:300,400,700\" rel=\"stylesheet\">";

		$html .= "<!-- CSS Reset : BEGIN -->";
		$html .= "<style>";

			$html .= "/* What it does: Remove spaces around the email design added by some email clients. */";
			$html .= "/* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */";
			$html .= "html,";
	$html .= "body {";
		$html .= "margin: 0 auto !important;";
		$html .= "padding: 0 !important;";
		$html .= "height: 100% !important;";
		$html .= "width: 100% !important;";
		$html .= "background: #f1f1f1;";
	$html .= "}";

	$html .= "/* What it does: Stops email clients resizing small text. */";
	$html .= "* {";
		$html .= "-ms-text-size-adjust: 100%;";
		$html .= "-webkit-text-size-adjust: 100%;";
	$html .= "}";

	$html .= "/* What it does: Centers email on Android 4.4 */";
	$html .= "div[style*=\"margin: 16px 0\"] {";
		$html .= "margin: 0 !important;";
	$html .= "}";

	$html .= "/* What it does: Stops Outlook from adding extra spacing to tables. */";
	$html .= "table,";
	$html .= "td {";
		$html .= "mso-table-lspace: 0pt !important;";
		$html .= "mso-table-rspace: 0pt !important;";
	$html .= "}";

	$html .= "/* What it does: Fixes webkit padding issue. */";
	$html .= "table {";
		$html .= "border-spacing: 0 !important;";
		$html .= "border-collapse: collapse !important;";
		$html .= "table-layout: fixed !important;";
		$html .= "margin: 0 auto !important;";
	$html .= "}";

	$html .= "/* What it does: Uses a better rendering method when resizing images in IE. */";
	$html .= "img {";
		$html .= "-ms-interpolation-mode:bicubic;";
	$html .= "}";

	$html .= "/* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */";
	$html .= "a {";
		$html .= "text-decoration: none;";
	$html .= "}";

	$html .= "/* What it does: A work-around for email clients meddling in triggered links. */";
	$html .= "*[x-apple-data-detectors],  /* iOS */";
	$html .= ".unstyle-auto-detected-links *,";
	$html .= ".aBn {";
		$html .= "border-bottom: 0 !important;";
		$html .= "cursor: default !important;";
		$html .= "color: inherit !important;";
		$html .= "text-decoration: none !important;";
		$html .= "font-size: inherit !important;";
		$html .= "font-family: inherit !important;";
		$html .= "font-weight: inherit !important;";
		$html .= "line-height: inherit !important;";
	$html .= "}";

	$html .= "/* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */";
	$html .= ".a6S {";
		$html .= "display: none !important;";
		$html .= "opacity: 0.01 !important;";
	$html .= "}";

	$html .= "/* What it does: Prevents Gmail from changing the text color in conversation threads. */";
	$html .= ".im {";
		$html .= "color: inherit !important;";
	$html .= "}";

	$html .= "/* If the above doesn't work, add a .g-img class to any image in question. */";
	$html .= "img.g-img + div {";
		$html .= "display: none !important;";
	$html .= "}";

	$html .= "/* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */";
	$html .= "/* Create one of these media queries for each additional viewport size you'd like to fix */";

	$html .= "/* iPhone 4, 4S, 5, 5S, 5C, and 5SE */";
	$html .= "@media only screen and (min-device-width: 320px) and (max-device-width: 374px) {";
		$html .= "u ~ div .email-container {";
			$html .= "min-width: 320px !important;";
		$html .= "}";
	$html .= "}";
	$html .= "/* iPhone 6, 6S, 7, 8, and X */";
	$html .= "@media only screen and (min-device-width: 375px) and (max-device-width: 413px) {";
		$html .= "u ~ div .email-container {";
			$html .= "min-width: 375px !important;";
		$html .= "}";
	$html .= "}";
	$html .= "/* iPhone 6+, 7+, and 8+ */";
	$html .= "@media only screen and (min-device-width: 414px) {";
		$html .= "u ~ div .email-container {";
			$html .= "min-width: 414px !important;";
		$html .= "}";
	$html .= "}";

		$html .= "</style>";

		$html .= "<!-- CSS Reset : END -->";

		$html .= "<!-- Progressive Enhancements : BEGIN -->";
		$html .= "<style>";

			$html .= ".primary{";
		$html .= "background: #30e3ca;";
	$html .= "}";
	$html .= ".bg_white{";
		$html .= "background: #ffffff;";
	$html .= "}";
	$html .= ".bg_light{";
		$html .= "background: #fafafa;";
	$html .= "}";
	$html .= ".bg_black{";
		$html .= "background: #000000;";
	$html .= "}";
	$html .= ".bg_dark{";
		$html .= "background: rgba(0,0,0,.8);";
	$html .= "}";
	$html .= ".email-section{";
		$html .= "padding:2.5em;";
	$html .= "}";

	$html .= "/*BUTTON*/";
	$html .= ".btn{";
		$html .= "padding: 10px 15px;";
		$html .= "display: inline-block;";
	$html .= "}";
	$html .= ".btn.btn-primary{";
		$html .= "border-radius: 5px;";
		$html .= "background: #30e3ca;";
		$html .= "color: #ffffff;";
	$html .= "}";
	$html .= ".btn.btn-white{";
		$html .= "border-radius: 5px;";
		$html .= "background: #ffffff;";
		$html .= "color: #000000;";
	$html .= "}";
	$html .= ".btn.btn-white-outline{";
		$html .= "border-radius: 5px;";
		$html .= "background: transparent;";
		$html .= "border: 1px solid #fff;";
		$html .= "color: #fff;";
	$html .= "}";
	$html .= ".btn.btn-black-outline{";
		$html .= "border-radius: 0px;";
		$html .= "background: transparent;";
		$html .= "border: 2px solid #000;";
		$html .= "color: #000;";
		$html .= "font-weight: 700;";
	$html .= "}";

	$html .= "h1,h2,h3,h4,h5,h6{";
		$html .= "font-family: 'Lato', sans-serif;";
		$html .= "color: #000000;";
		$html .= "margin-top: 0;";
		$html .= "font-weight: 400;";
	$html .= "}";

	$html .= "body{";
		$html .= "font-family: 'Lato', sans-serif;";
		$html .= "font-weight: 400;";
		$html .= "font-size: 15px;";
		$html .= "line-height: 1.8;";
		$html .= "color: rgba(0,0,0,.4);";
	$html .= "}";

	$html .= "a{";
		$html .= "color: #30e3ca;";
	$html .= "}";

	$html .= "table{";
	$html .= "}";
	$html .= "/*LOGO*/";

	$html .= ".logo h1{";
		$html .= "margin: 0;";
	$html .= "}";
	$html .= ".logo h1 a{";
		$html .= "color: #30e3ca;";
		$html .= "font-size: 24px;";
		$html .= "font-weight: 700;";
		$html .= "font-family: 'Lato', sans-serif;";
	$html .= "}";

	$html .= "/*HERO*/";
	$html .= ".hero{";
		$html .= "position: relative;";
		$html .= "z-index: 0;";
	$html .= "}";

	$html .= ".hero .text{";
		$html .= "color: rgba(0,0,0,.3);";
	$html .= "}";
	$html .= ".hero .text h2{";
		$html .= "color: #000;";
		$html .= "font-size: 40px;";
		$html .= "margin-bottom: 0;";
		$html .= "font-weight: 400;";
		$html .= "line-height: 1.4;";
	$html .= "}";
	$html .= ".hero .text h3{";
		$html .= "font-size: 24px;";
		$html .= "font-weight: 300;";
	$html .= "}";
	$html .= ".hero .text h2 span{";
		$html .= "font-weight: 600;";
		$html .= "color: #30e3ca;";
	$html .= "}";


	$html .= "/*HEADING SECTION*/";
	$html .= ".heading-section{";
	$html .= "}";
	$html .= ".heading-section h2{";
		$html .= "color: #000000;";
		$html .= "font-size: 28px;";
		$html .= "margin-top: 0;";
		$html .= "line-height: 1.4;";
		$html .= "font-weight: 400;";
	$html .= "}";
	$html .= ".heading-section .subheading{";
		$html .= "margin-bottom: 20px !important;";
		$html .= "display: inline-block;";
		$html .= "font-size: 13px;";
		$html .= "text-transform: uppercase;";
		$html .= "letter-spacing: 2px;";
		$html .= "color: rgba(0,0,0,.4);";
		$html .= "position: relative;";
	$html .= "}";
	$html .= ".heading-section .subheading::after{";
		$html .= "position: absolute;";
		$html .= "left: 0;";
		$html .= "right: 0;";
		$html .= "bottom: -10px;";
		$html .= "content: '';";
		$html .= "width: 100%;";
		$html .= "height: 2px;";
		$html .= "background: #30e3ca;";
		$html .= "margin: 0 auto;";
	$html .= "}";

	$html .= ".heading-section-white{";
		$html .= "color: rgba(255,255,255,.8);";
	$html .= "}";
	$html .= ".heading-section-white h2{";
		$html .= "font-family: ";
		$html .= "line-height: 1;";
		$html .= "padding-bottom: 0;";
	$html .= "}";
	$html .= ".heading-section-white h2{";
		$html .= "color: #ffffff;";
	$html .= "}";
	$html .= ".heading-section-white .subheading{";
		$html .= "margin-bottom: 0;";
		$html .= "display: inline-block;";
		$html .= "font-size: 13px;";
		$html .= "text-transform: uppercase;";
		$html .= "letter-spacing: 2px;";
		$html .= "color: rgba(255,255,255,.4);";
	$html .= "}";


	$html .= "ul.social{";
		$html .= "padding: 0;";
	$html .= "}";
	$html .= "ul.social li{";
		$html .= "display: inline-block;";
		$html .= "margin-right: 10px;";
	$html .= "}";

	$html .= "/*FOOTER*/";

	$html .= ".footer{";
		$html .= "border-top: 1px solid rgba(0,0,0,.05);";
		$html .= "color: rgba(0,0,0,.5);";
	$html .= "}";
	$html .= ".footer .heading{";
		$html .= "color: #000;";
		$html .= "font-size: 20px;";
	$html .= "}";
	$html .= ".footer ul{";
		$html .= "margin: 0;";
		$html .= "padding: 0;";
	$html .= "}";
	$html .= ".footer ul li{";
		$html .= "list-style: none;";
		$html .= "margin-bottom: 10px;";
	$html .= "}";
	$html .= ".footer ul li a{";
		$html .= "color: rgba(0,0,0,1);";
	$html .= "}";


	$html .= "@media screen and (max-width: 500px) {";


	$html .= "}";


		$html .= "</style>";


	$html .= "</head>";

	$html .= "<body width=\"100%\" style=\"margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f1f1f1;\">";
		$html .= "<center style=\"width: 100%; background-color: #f1f1f1;\">";
		$html .= "<div style=\"display: none; font-size: 1px;max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;\">";
		 $html .= " &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;";
		$html .= "</div>";
		$html .= "<div style=\"max-width: 600px; margin: 0 auto;\" class=\"email-container\">";
			$html .= "<!-- BEGIN BODY -->";
		  $html .= "<table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" style=\"margin: auto;\">";
			$html .= "<tr>";
			  $html .= "<td valign=\"top\" class=\"bg_white\" style=\"padding: 1em 2.5em 0 2.5em;\">";
				$html .= "<table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
					$html .= "<tr>";
						$html .= "<td class=\"logo\" style=\"text-align: center;\">";
							$html .= "<a href=\"//www.lvteknik.se\"><img src = \"https://www.lvteknik.se/img/lvteknik_300.png\"></a></h1>";
						  $html .= "</td>";
					$html .= "</tr>";
	
					$html .= "<tr>";
						$html .= "<td style=\"text-align: center;\">";
							$html .= "<h3>".$renderNewsLetterTemplate[9]."</h3>";
							$html .= "<p>".$renderNewsLetterTemplate[10]."</p>";
						  $html .= "</td>";
					$html .= "</tr>";
				$html .= "</table>";
			  $html .= "</td>";
			  $html .= "</tr><!-- end tr -->";
			  
	
			
	
			$html .= "<tr>";
			 	$html .= "<td valign=\"middle\" class=\" bg_white\" style=\"padding: 2em 0 4em 0;\">";
				$html .= "<table>";
					$html .= "<tr>";
						$html .= "<td>";
							/*$html .= "<div class=\"text\" style=\"padding: 0 2.5em; text-align: center;\">";
								$html .= "<h2>Please verify your email</h2>";
								$html .= "<h3>Amazing deals, updates, interesting news right in your inbox</h3>";
								$html .= "<p><a href=\"#\" class=\"btn btn-primary\">Yes! Subscribe Me</a></p>";
							$html .= "</div>";*/
							$html .= "<div class=\"text\" style=\"padding: 0 2.5em; text-align: center;\">";
								$html .= "~~newsletterText~~";
							$html .= "</div>";
						$html .= "</td>";
					$html .= "</tr>";
				$html .= "</table>";
			  $html .= "</td>";
			  $html .= "</tr><!-- end tr -->";
		  $html .= "<!-- 1 Column Text + Button : END -->";
		  $html .= "</table>";
		  $html .= "<table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" style=\"margin: auto;\">";
			$html .= "<tr>";
			  $html .= "<td valign=\"middle\" class=\"bg_light footer email-section\">";
				$html .= "<table>";
					$html .= "<tr>";
					$html .= "<td valign=\"top\" width=\"33.333%\" style=\"padding-top: 20px;\">";
					  $html .= "<table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">";
						$html .= "<tr>";
						  $html .= "<td style=\"text-align: left; padding-right: 10px;\">";
							$html .= "<h3 class=\"heading\">".$renderNewsLetterTemplate[1]."</h3>";
							$html .= "<p>".$renderNewsLetterTemplate[2]."</p>";
						  $html .= "</td>";
						$html .= "</tr>";
					  $html .= "</table>";
					$html .= "</td>";
	
					/*$html .= "<td valign=\"top\" width=\"33.333%\" style=\"padding-top: 20px;\">";
					  $html .= "<table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">";
						$html .= "<tr>";
						  $html .= "<td style=\"text-align: left; padding-left: 5px; padding-right: 5px;\">";
							$html .= "<h3 class=\"heading\">Contact Info</h3>";
							$html .= "<ul>";
								$html .= "<li><span class=\"text\">203 Fake St. Mountain View, San Francisco, California, USA</span></li>";
								$html .= "<li><span class=\"text\">+2 392 3929 210</span></a></li>";
							  $html .= "</ul>";
						  $html .= "</td>";
						$html .= "</tr>";
					  $html .= "</table>";
					$html .= "</td>";*/
					$html .= "<td valign=\"top\" width=\"33.333%\" style=\"padding-top: 20px;\">";
					  $html .= "<table role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">";
						$html .= "<tr>";
						  $html .= "<td style=\"text-align: left; padding-left: 10px;\">";
							$html .= "<h3 class=\"heading\">".$renderNewsLetterTemplate[3]."</h3>";
							$html .= "<ul>";
										$html .= "<li><a href=\"//www.lvteknik.se/\">".$renderNewsLetterTemplate[4]."</a></li>";
										$html .= "<li><a href=\"//www.lvteknik.se/ownpage.php?pageId=yVDKSjT4jWcegM\">".$renderNewsLetterTemplate[5]."</a></li>";
										$html .= "<li><a href=\"//www.lvteknik.se/ownpage.php?pageId=kX8KUqTdGKsS8m\">".$renderNewsLetterTemplate[6]."</a></li>";
										$html .= "<li><a href=\"//www.lvteknik.se/ownpage.php?pageId=qJPaxfz3tgcAh2\">".$renderNewsLetterTemplate[7]."</a></li>";
									  $html .= "</ul>";
						  $html .= "</td>";
						$html .= "</tr>";
					  $html .= "</table>";
					$html .= "</td>";
				  $html .= "</tr>";
				$html .= "</table>";
			  $html .= "</td>";
			$html .= "</tr><!-- end: tr -->";
			$html .= "<tr>";
			  $html .= "<td class=\"bg_light\" style=\"text-align: center;\">";
				$html .= "<p>".$renderNewsLetterTemplate[8]."</p>";
			  $html .= "</td>";
			$html .= "</tr>";
		  $html .= "</table>";

		$html .= "</div>";
	  $html .= "</center>";
	$html .= "</body>";
	$html .= "</html>";
	
	return $html;
}
//echo renderNewsLetterTemplate();

?>