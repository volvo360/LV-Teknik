<?php
    error_reporting(E_ALL);

	include_once("./common/db.php");
    include_once("./common/userData.php");
    include_once("./theme.php");

	function manageNewsletter()
	{
		global $link;
		
		global $phrase;
		
		$replaceTable = getReplaceTable();

        if (isset($_SESSION['uid']))
        {
          $userSettings = getUserSettings();

            $displayLang = array_map("trim", explode(",", $userSettings['langService']));

        }

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

        if (isset($_SESSION['userLang']) && !isset($_SESSION['uid']))
        {
            $order[] = "WHEN lang = '".$_SESSION['userLang']."' THEN -1";
            $order_lang[] = "WHEN code = '".$$_SESSION['userLang']."' THEN -1";
            $i++;
        }

        foreach ($displayLang as $key => $value)
        {
            $order[] = "WHEN lang = '".$value."' THEN ".$i;
            $order_lang[] = "WHEN Code = '".$value."' THEN ".$i;
            $i++;
        }

        if (!isset($_SESSION['userLang']))
        {
            $_SESSION['userLang'] = reset($displayLang);
        }
        
        $langStrings = getlangstrings();
		$manageNewsletter = $langStrings['manageNewsletter'];

		$manageNewsletter_array = getLangstringsArray('manageNewsletter_array', $displayLang);
		
		$table = "`".PREFIX."newsletter_readers`";
		
		$sql = "SELECT *, AES_DECRYPT(name, SHA2('".$phrase."', 512)) as name, AES_DECRYPT(email, SHA2('".$phrase."', 512)) as email, AES_DECRYPT(lang, SHA2('".$phrase."', 512)) as lang FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_GET['readerKey'])."'";
		$result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));

		if (mysqli_num_rows($result) > 0)
		{
			
			echo "<h3>".$manageNewsletter[1]."</h3>";
			echo "<p>".$manageNewsletter[2]."</p>";
		
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				echo "<form>";
					echo "<div class=\"form-group row\">";
						echo "<label for=\"name[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$manageNewsletter[3]."</label>";

						echo "<div class=\"col-sm-5\">";
							echo "<input type=\"text\" class=\"form-control syncData\" id=\"name[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_readers"]."\" value = \"".$row['name']."\" required >";
						echo "</div>";
					echo "</div>";

					echo "<div class=\"form-group row\">";
						echo "<label for=\"email[".$row['tableKey']."]\" class=\"col-sm-2 col-form-label\">".$manageNewsletter[4]."</label>";

						echo "<div class=\"col-sm-5\">";
							echo "<input type=\"text\" class=\"form-control syncData\" id=\"email[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_readers"]."\" value = \"".$row['email']."\" required>";
						echo "</div>";
					echo "</div>";

					echo "<div class=\"form-group row\">";
						echo "<label for=\"none\" class=\"col-sm-2 col-form-label\">".$manageNewsletter[5]."</label>";

						echo "<div class=\"col-sm-5\">";
							echo "<div class=\"form-check checkbox-slider--b\">";
								echo "<label>";
									echo "<input class = \"syncData\" id = \"status[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_readers"]."\" type=\"checkbox\"";
										if ((int)$row['status'] > 0)
										{
											echo " "."checked";
										}
									echo "><span></span>"." ".$manageNewsletter[6];
								echo "</label>";
							echo "</div>";
							//echo "<button class = \"btn btn-secondary btn-block\" id = \"removeReaderNewsletter\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_readers"]."\">".$manageNewsletter[5]."</button>";
						echo "</div>";
					echo "</div>";
				
					echo "<div class=\"form-group row\">";
						echo "<label for=\"none\" class=\"col-sm-2 col-form-label\">"."&nbsp;"."</label>";

						echo "<div class=\"col-sm-5\">";
							
							echo "<button class = \"btn btn-secondary btn-block\" id = \"removeReaderNewsletter[".$row['tableKey']."]\" data-replace_table = \"".$replaceTable[PREFIX."newsletter_readers"]."\">".$manageNewsletter[7]."</button>";
						echo "</div>";
					echo "</div>";

					echo "<input type = \"hidden\" id = \"lang\" value = \"".$replaceLang[reset($displayLang)]."\">";
				echo "</form>";
			}
		}
		else
		{
			echo "<h1>".$manageNewsletter[8]."</h1>";
		}
	}
		

	function displayManageNewsletter()
	{
		echo "<div class=\"main-wrapper \">";
		echo "<section class=\"page-title bg-1\">";
		  echo "<div class=\"container\">";
			echo "<div class=\"row\">";
			  echo "<div class=\"col-md-12\">";
				echo "<div class=\"block text-center\">";
				  echo "<span class=\"text-white\">".$pageData['subHeader']."</span>";
				  echo "<h1 class=\"text-capitalize mb-4 text-lg\">".$pageData['header']."</h1>";
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

			manageNewsletter();

			echo "</div>";
		 echo "</section>";
    }

    printHeader();

   	displayManageNewsletter();

    displayFooter();

    
?>