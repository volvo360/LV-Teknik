<?php

include_once("./common/db.php");
include_once("./common/userData.php");

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

include_once("./theme.php");

function changePassword()
{
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

    $langStrings = getlangstrings();
    $changePassword = $langStrings['changePassword'];

    $changePassword_array = getLangstringsArray('changePassword_array', $displayLang);
    
    if (($_POST['password'] == $_POST['repPassword']) && strlen(trim($_POST[$_POST['password']])) >= 6)
    {
        $table = "`".PREFIX."users`";
        $table10 = "`".PREFIX."reset_password_keys`";
        
        $sql = "SELECT * FROM ".$table10." WHERE tableKey = '".mysqli_real_escape_string($link, $_POST['tableKey'])."' AND  endTime < NOW()";
        $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
        
        if (mysqli_num_rows($result) == 0)
        {
            echo $changePassword[1];
            
            return -1;
        }
        else
        {
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $userId = $row['userId'];
            }
            
            $sql = "UPDATE ".$table." SET password = AES_ENCRYPT('".password_hash(mysqli_real_escape_string($link, $_POST['password']))."') WHERE autoId = '".$autoId."'";
            $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
            
            if ($_SERVER['SERVER_NAME'] === 'localhost')
            {
                $url = "//localhost/lv/administrado/";
            }
            else if ($_SERVER['SERVER_NAME'] === 'server01')
            {
                $url = "//server01/flexshare/lv/administrado/";
            }
            else
            {
                $url = "//www.lvteknik.se/administrado/";
            }
            
            header("Location: ".$url);
        }
    }
    else
    {
        return false;
    }
}

function displayContentrResetPassword()
{
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

    $langStrings = getlangstrings($displayLang);
    $displayContentrResetPassword = $langStrings['displayContentrResetPassword'];

    $displayContentrResetPassword_array = getLangstringsArray('displayContentrResetPassword_array', $displayLang);
    
    $changePassword = $langStrings['changePassword'];

    $changePassword_array = getLangstringsArray('changePassword_array', $displayLang);
    
    $replaceTable = getReplaceTable();
    
    $table = "`".PREFIX."menu_footer`";
    $table2 = "`".PREFIX."menu_footer_lang`";
	
	$table10 = "`".PREFIX."reset_password_keys`";
    
    if (isset($_POST['password']))
    {
        $result = changePassword();
        if ($result < 0)
        {
            return false;
        }
        
        echo "<h1>".$displayContentrResetPassword[3]."</h1>";
    }
    
    $sql = "SELECT * FROM ".$table10." WHERE tableKey = '".mysqli_real_escape_string($link, $_GET['tableKey'])."' AND  endTime > NOW()";
	//echo __LINE__." ".$sql."<br>";
	$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

	if (mysqli_num_rows($result) == 0)
	{
		echo $changePassword[1];

		return -1;
	}
	else
	{

		echo "<form id = \"formSetNewPassword\">";
			echo "<div class=\"form-group row\">";
				echo "<label for=\"password\" class=\"col-sm-5 col-form-label\">".$displayContentrResetPassword[1]."</label>";
				echo "<div class=\"col-sm-5\">";
					echo "<input type=\"password\" class=\"form-control\" id=\"password\" name = \"password\">";
				echo "</div>";
			echo "</div>";
			echo "<div class=\"form-group row\">";
				echo "<label for=\"repPassword\" class=\"col-sm-5 col-form-label\">".$displayContentrResetPassword[2]."</label>";
				echo "<div class=\"col-sm-5\">";
					echo "<input type=\"password\" class=\"form-control\" id = \"repPassword\" name = = \"repPassword\">";
				echo "</div>";
			echo "</div>";
			echo "<input type = \"hidden\" id = \"tableKey\" name = \"tableKey\" value = \"".mysqli_real_escape_string($link, $_GET['tableKey'])."\">";

			echo "<div class=\"form-group row\">";
				echo "<label for=\"repPassword\" class=\"col-sm-5 col-form-label\">"."&nbsp;"."</label>";
				echo "<div class=\"col-sm-5\">";
					echo "<button type = \"submitt\" class = \"btn btn-main btn-round-full\" id = sendNewPassword\">".$displayContentrResetPassword[4]."</button>";
				echo "</div>";
			echo "</div>";
		
			
		echo "</form>";
	}
}


printHeader();

echo "<div class=\"page-header clear-filter\" filter-color=\"orange\">";
			echo "<div class=\"page-header-image\"></div>";
				echo "<div class=\"content\">";
					echo "<div class=\"container\">";
						echo "<div class=\"col-md-12 ml-auto mr-auto\">";
							echo "<div class=\"card card-login card-plain\">";
								echo "<div class=\"card-header text-center\">";
									echo "<div class=\"logo-container\">";
										echo "<img src=\"./img/lvteknik_300.png\" alt=\"\">";
									echo "</div>";
								echo "</div>";

								echo "<div class=\"card-body\">";
									displayContentrResetPassword();
								echo "</div>";				
						echo "</div>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";



displayFooter();