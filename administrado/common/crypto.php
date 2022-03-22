<?php
if (!function_exists('generateHash'))
{
	function generateHash($plainText, $salt = null)
	{
		define('SALT_LENGTH', 9);
		if ($salt === null)
		{
			$salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
		}
		else
		{
			$salt = substr($salt, 0, SALT_LENGTH);
		}

		return $salt . sha1($salt . $plainText);
	}
}
if (!function_exists('r_generatephrase'))
{
	function r_generatephrase($company = FALSE)
	{
		global $conn;
		global $link;

		if ($company == FALSE)
		{
			die("Funktionen kräver inparametrar för att fungera (r_generatephrase)!!!");
		}
		$phrase = generateStrongPassword();

		$db = PREFIX ."phrase";

		//Kontrollera och se om vi har en egen mapp sedan tidigare
		$sql = "SELECT phrase FROM $db WHERE company_id = '$company'";
		//echo $sql ."<br>";
		$result= mysqli_query($link, $sql) or die ("Error $sql : ".mysqli_error ($link));
		$rad=mysqli_fetch_array($result);
		if ($rad['phrase'] != false)
		{
			$phrase = $rad['phrase'];

			$structure = "kliento/" .substr($phrase,0,1) ."/" .$phrase ."/";

		//	echo $structure ." 1<br>";

			if (!file_exists($structure))
			{
				if (!mkdir($structure,0777,true))
				{
					die('Failed to create folders...');
				}
			}

			//echo "Det finns redan ett unikt id....<br>";
			return false;
		}
		$sql = "SELECT phrase FROM $db WHERE phrase = '$phrase'";
		//echo $sql ."<br>";
		$result= mysqli_query($link, $sql) or die ("Error $sql : ".mysqli_error ($link));
		$rad=mysqli_fetch_array($result);
		if ($rad['phrase'] != FALSE)
		{
			//echo "Nyckeln finns redan....<br>";
			return false;
		}
		else
		{
			$sql = "INSERT INTO $db (company_id, phrase) VALUES ('$company', '$phrase')";
			//echo $sql ."<br>";
			$result= mysqli_query($link, $sql) or die ("Error $sql : ".mysqli_error ($link));

			$structure = "kliento/" . substr($phrase,0,1). "/" .$phrase ."/";

		//	echo $structure ." 2<br>";

			if (!file_exists($structure))
			{
				if (!mkdir($structure,0777,true))
				{
					die('Failed to create folders...');
				}
			}
			return $phrase;
		}
	}
}

if (!function_exists('generateDbConnection'))
{
	function generateDbConnection($company = FALSE)
	{
		//echo "Kommer att skapa en phrase....<br>";
		global $conn;
		global $link;
		global $phrase;
		global $link;

		if ($company == FALSE)
		{
			echo "Det har inte kommit in någon parameter till denna funktion (generatephrase)!!!! <br>";
			return false;
		}

		$db = "`".PREFIX."account`";

		//Kontrollera och se om vi har en egen mapp sedan tidigare
		$sql = "SELECT phrase FROM $db WHERE autoId = '$company'";
		echo $sql ."<br>";
		$result= mysqli_query($link, $sql) or die ("Error $sql : ".mysqli_error ($link));
		$rad=mysqli_fetch_array($result);

		$phrase = $rad['phrase'];

		$structure = "./kliento/" .substr($phrase,0,1) ."/".substr($phrase,0,2) ."/".$phrase ."/";
		echo $structure ."<br>";
		if (!is_dir($structure))
		{
			if (!mkdir($structure,0777,true))
			{
				die('Failed to create folders...kliento');
			}
		}

		$structure = "./kliento/" .substr($phrase,0,1) ."/".substr($phrase,0,2) ."/"  .$phrase ."/work_dir/";
		//echo $structure ." 5<br>";
		if (!is_dir($structure))
		{
			if (!mkdir($structure,0777,true))
			{
				die('Failed to create folders...workdir');
			}
		}

		$structure = "./kliento/" .substr($phrase,0,1) ."/".substr($phrase,0,2) ."/" .$phrase ."/work_dir/config/";
		//echo $structure ." 4<br>";
		if (!is_dir($structure))
		{
			if (!mkdir($structure,0777,true))
			{
				die('Failed to create folders...config');
			}
		}
		$file = $structure ."db-c.php";
		//echo $file ."<br>";
		if (!file_exists($file))
		{
			$data = "<?php\r\n\r\n";
			$data .="//Denna fil generars av crypto.php via funktionen generateDbConnection.\r\n\r\n";
			$data .= "date_default_timezone_set('Europe/Stockholm');\r\n\r\n";

			$link_k = 'global $link_k';
			$data .= "$link_k;\r\n";
			
			$phrase_k = 'global $phrase_k';
			$data .= "$phrase_k;\r\n";
			
			$temp = generateStrongPassword(9);
			
			$data .= '$phrase_k = '.$temp.';\r\n';
			global $phrase_k;
	
            $phrase_k = 'AThNRP5BB';

			$data .= "define ('PREFIX_K', 'ep_k_" .$company ."_');\r\n\r\n";

			$data .= "checkdb_k_".$company."();\r\n\r\n";
			$data .= "function checkdb_k_".$company."()\r\n";
			$data .="{\r\n";
			$link_c = 'global $link_k';
			$data .= "$link_c;\r\n";
			$mysql_host_c = '$mysql_host_c';
			if (($_SERVER['SERVER_NAME'] == 'server1') || ($_SERVER['SERVER_NAME'] == 'utbildningsresurs.se') || ($_SERVER['SERVER_ADDR'] == "192.168.0.1" || ($_SERVER['SERVER_NAME'] == 'server01')))
			{
				$data .= "\t$mysql_host_c = 'localhost';\r\n";
			}
			else
			{
				$data .= "\t$mysql_host_c = 'binary-solution-easy-import-company-data-2002542.mysql.crystone.se';\r\n";
			}
			$mysql_user_c = '$mysql_user_c';

			if (($_SERVER['SERVER_NAME'] == 'server1') || ($_SERVER['SERVER_NAME'] == 'utbildningsresurs.se'))
			{
				$data .= "\t$mysql_user_c = 'easyprojcet_k_$company';\r\n";
			}
			else
			{
				$data .= "\t$mysql_user_c = '2002542_mi76822';\r\n";
			}
			$mysql_pass_c = '$mysql_pass_c';
			if (($_SERVER['SERVER_NAME'] == 'server01') || ($_SERVER['SERVER_NAME'] == 'utbildningsresurs.se'))
			{
				$data .= "\t$mysql_pass_c = '$phrase';\r\n";
			}
			else
			{
				$data .= "\t$mysql_pass_c = '6fwihn(3/c';\r\n";
			}
			$mysql_db_c   = '$mysql_db_c';
			if (($_SERVER['SERVER_NAME'] == 'server01') || ($_SERVER['SERVER_NAME'] == 'utbildningsresurs.se'))
			{
				$data .= "\t$mysql_db_c = 'easyprojcet_k_$company';\r\n\r\n";
			}
			else
			{
				$data .= "\t$mysql_db_c = '2002542-binary-solution-easy-import-company-data';\r\n\r\n";
			}
			$conn_temp = '\$conn_c';


			$data .= "if (phpversion() > 5)
{
	if (!mysqli_ping (\$conn_c))
	{
		\$link_c = mysqli_connect (\$mysql_host_c, \$mysql_user_c, \$mysql_pass_c, \$mysql_db_c);
	 	mysqli_set_charset(\$link_c, 'utf8');
	 	mysqli_query(\$link_c, \"SET NAMES 'utf8'\");
	}
}\r\n";
			//$data .= "\t}\r\n";
			$data .= "}\r\n";

			$data .= "?>";

			$ourFileHandle = fopen($file, 'w') or die("can't open file in crypto.php");
			fwrite($ourFileHandle, $data);

			fclose($ourFileHandle);

		}

		return $phrase;
	}
}

if (!function_exists('create_db_for_account'))
{
	function create_db_for_account($company_id = null, $phrase = null)
	{
		global $link;
		echo __LINE__." ".$company_id ."<br>";
		echo __LINE__." ".$phrase ."<br>";

		if (empty($company_id) || empty($phrase))
		{
			die("Error : " .__LINE__." ".__FILE__);
		}
		/*
		if (!($_SERVER['SERVER_NAME'] == 'server1') && !($_SERVER['SERVER_NAME'] == 'utbildningsresurs.se'))
		{
			include_once("common/script/create_tables.php");
			check_tables();
			return true;
		}*/
		$company = "EP_K_" .$company_id;
		$db = $company;

		if ($_SERVER['SERVER_NAME'] == "server1")
		{
			$mysql_host_c = 'localhost';
			$mysql_user_c = 'root';
			$mysql_pass_c = 'qgp781';
			$mysql_db_c = $company ;
		}
		else
		{
			$mysql_host_c = 'binary-solution-easy-import-company-data-2002542.mysql.crystone.se';
			$mysql_user_c = '2002542_mi76822';
			$mysql_pass_c = '6fwihn(3/c';
			$mysql_db_c   = '2002542-binary-solution-easy-import-company-data';
		}

		if (($_SERVER['SERVER_ADDR'] == "192.168.0.1") || ($_SERVER['SERVER_NAME'] == 'server01') || ($_SERVER['SERVER_NAME'] == 'utbildningsresurs.se'))
		{
			//echo __LINE__ ." " . basename(__FILE__)."<br>";
			$mysql_host_c = 'localhost';
			$mysql_user_c = 'root';
			$mysql_pass_c = 'qgp781';
			$mysql_db_c = $company ;
			//$mysql_db_c = "enkelimport";
		}

		//global $phrase;

		if (!mysqli_ping ($link_t))
		{
			$mysql_db_c2 = "enkelimport";
			//here is the major trick, you have to close the connection (even though its not currently working) for it to recreate properly.
			mysqli_close($link_t);
			$link_t = mysqli_connect($mysql_host_c,$mysql_user_c , $mysql_pass_c, $mysql_db_c2);
			if (!$link_t)
			{
				echo "324 MySQL-fel. Kunde inte ansluta till databas (crypto.php).<br />Orsak: " . mysqli_error($link_t);
				exit;
			}

		}

		if (($_SERVER['SERVER_NAME'] == '192.168.0.1') || ($_SERVER['SERVER_NAME'] == 'server01') || ($_SERVER['SERVER_NAME'] == 'server01.wallinfoto.se') || ($_SERVER['SERVER_NAME'] == 'dokumenti.net'))
		{
			//echo __LINE__." ".__FILE__." ".$company_id."<br>";

			$sql = "FLUSH PRIVILEGES";
			$result= mysqli_query($link_t, $sql) or die ("Error $sql : ".mysqli_error ($link_t));
			$dbselect_c = @mysqli_select_db($link, $mysql_db_c);

			//if (!$dbselect_c)
			{
				//echo 'MySQL-fel. Kunde inte välja databas \'' . $mysql_db_c . '\'.<br />Orsak: ' . mysqli_error ($link) ."<br>";
				//exit;
				$sql = 'CREATE DATABASE ' .$mysql_db_c .' COLLATE utf8_general_ci';
				//echo __LINE__." ".__FILE__." ".$sql."<br>";
				$result= mysqli_query($link_t, $sql) or die ("Error $sql : ".mysqli_error ($link_t));

				$sql = "CREATE USER '$company'@'localhost' IDENTIFIED BY '$phrase'";
				//echo __LINE__." ".__FILE__." ".$sql."<br>";
				$result= mysqli_query($link_t, $sql) or die ("Error $sql : ".mysqli_error ($link_t));
			}
			$sql = "GRANT SELECT, INSERT, UPDATE, DELETE, ALTER, CREATE ON  $mysql_db_c.* TO '$company'@'localhost'";
			//echo __LINE__." ".__FILE__." ".$sql."<br>";
			mysqli_select_db($link_t, $mysql_db_c);
			$result= mysqli_query($link_t, $sql) or die ("Error $sql : ".mysqli_error ($link_t));

			//mysql_query("SET NAMES 'utf8'");
			$sql = "SELECT * FROM mysql.user WHERE User = '$company' AND Host = 'localhost'";
			$result= mysqli_query($link_t, $sql) or die ("Error $sql : ".mysqli_error ($link_t));
			if (mysqli_num_rows($result) == 0)
			{
				$sql = "CREATE USER '$company'@'localhost' IDENTIFIED BY '".$phrase."'";
				$result= mysqli_query($link_t, $sql) or die ("Error $sql : ".mysqli_error ($link_t));
				$sql = "GRANT SELECT, INSERT, UPDATE, DELETE, ALTER, CREATE ON  $mysql_db_c.* TO '$company'@'localhost'";
				$result= mysqli_query($link_t, $sql) or die ("Error $sql : ".mysqli_error ($link_t));
				mysqli_select_db($link_t, $mysql_db_c);

			}
		}
	}
}
if (!function_exists('generateStrongPassword'))
{
	function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'lud')
	{
		$sets = array();
		if(strpos($available_sets, 'l') !== false)
			$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		if(strpos($available_sets, 'u') !== false)
			$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		if(strpos($available_sets, 'd') !== false)
			$sets[] = '23456789';
		if(strpos($available_sets, 's') !== false)
			$sets[] = '!@#$%&*?';

		$all = '';
		$password = '';
		foreach($sets as $set)
		{
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}

		$all = str_split($all);
		for($i = 0; $i < $length - count($sets); $i++)
			$password .= $all[array_rand($all)];

		$password = str_shuffle($password);

		if(!$add_dashes)
			return $password;

		$dash_len = floor(sqrt($length));
		$dash_str = '';
		while(strlen($password) > $dash_len)
		{
			$dash_str .= substr($password, 0, $dash_len) . '-';
			$password = substr($password, $dash_len);
		}
		$dash_str .= $password;
		return $dash_str;
	}
}
?>
