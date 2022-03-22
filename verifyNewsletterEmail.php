<?php

include_once("./common/db.php");
include_once("./common/userData.php");
include_once("./theme.php");

global $link;
    
global $phrase;

$siteSettings = getSiteSettings();

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

$displayLang = array_map("trim", explode(",", $siteSettings['language']));

foreach ($displayLang as $key => $value)
{
    $order[] = "WHEN lang = '".$value."' THEN ".$i;
    $order_lang[] = "WHEN code = '".$value."' THEN ".$i;
    $i++;
}
echo "<!doctype html>";
echo "<html lang=\"".reset($displayLang)."\">";

function activateNewsletterMail()
{
	global $link;
	global $phrase;
	
	$tableKey = $_GET['tablekey'];
	
	$langStrings = getlangstrings();
	$activateNewsletterMail = $langStrings['activateNewsletterMail'];
	
	$table = "`".PREFIX."newsletter_readers`";
	
	$sql = "UPDATE ".$table." SET status = 1 WHERE tableKey = '".mysqli_real_escape_string($link, $tableKey)."'";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
	
	if (mysqli_affected_rows($link) > 0)
	{
		$sql = "SELECT *, AES_DECRYPT(name, SHA2('".$phrase."', 512)) as name FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $tableKey)."'";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			echo "<h1>".str_replace("~~reciverName~~", $row['name'], $activateNewsletterMail[1])."</h1>";
			echo "<p>".$activateNewsletterMail[2]."</p>";
		}
	}
	else
	{
		$sql = "SELECT *, AES_DECRYPT(name, SHA2('".$phrase."', 512)) as name FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $tableKey)."'";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
		
		if (mysqli_num_rows($result) > 0)
		{
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				echo "<h1>".str_replace("~~reciverName~~", $row['name'], $activateNewsletterMail[1])."</h1>";
				echo "<p>".$activateNewsletterMail[5]."</p>";
			}
		}
		else
		{
			echo "<h1>".$activateNewsletterMail[3]."</h1>";
			echo "<p>".$activateNewsletterMail[4]."</p>";
		}
	}
}

function verifyNewsletterEmail()
{
	global $link;
	
	$langStrings = getlangstrings();
	$verifyNewsletterEmail = $langStrings['verifyNewsletterEmail'];
	
	echo "<div class=\"main-wrapper \">";
    echo "<section class=\"page-title bg-1\">";
      echo "<div class=\"container\">";
        echo "<div class=\"row\">";
          echo "<div class=\"col-md-12\">";
            echo "<div class=\"block text-center\">";
              echo "<span class=\"text-white\">"."&nbsp;"."</span>";
              echo "<h1 class=\"text-capitalize mb-4 text-lg\">".$verifyNewsletterEmail[1]."</h1>";
              /*echo "<ul class=\"list-inline\">";
                echo "<li class=\"list-inline-item\"><a href=\"index.html\" class=\"text-white\">Home</a></li>";
                echo "<li class=\"list-inline-item\"><span class=\"text-white\">/</span></li>";
                echo "<li class=\"list-inline-item\"><a href=\"#\" class=\"text-white-50\">About Us</a></li>";
              echo "</ul>";*/
            echo "</div>";
          echo "</div>";
        echo "</div>";
      echo "</div>";
    echo "</section>";
    echo "<!-- Section About Start -->";
    echo "<section class=\"section about-2 position-relative\">";
        echo "<div class=\"container\">";
    		
        	activateNewsletterMail();
        
        echo "</div>";
     echo "</section>";
    
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']))
{
    printHeader();
    verifyNewsletterEmail();
    displayFooter();
}