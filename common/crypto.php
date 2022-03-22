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
