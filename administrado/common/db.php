<?php
define('PREFIX', 'lv_');
global $link;
global $phrase;
checkdb();

function checkdb()
{
    global $mysql_host;
	global $mysql_user;
	global $mysql_pass;
	global $mysql_db;
	
	global $link;
	
	global $phrase;
	
	$phrase = '9MpNhrPMqww2CB5M';
    
	//if (($_SERVER['SERVER_NAME'] == '192.168.0.1') || ($_SERVER['SERVER_NAME'] == 'server1') || ($_SERVER['SERVER_NAME'] == 'utbildningsresurs.se') || ($_SERVER['SERVER_ADDR'] == "192.168.0.1"|| ($_SERVER['SERVER_NAME'] == 'localhost')))
	if ($_SERVER['SERVER_NAME'] === 'server01')
	{
		$mysql_host = 'localhost';
		$mysql_user = 'lvteknik3';
		$mysql_pass = 'oYfMXHgsPVULQHEH';
		$mysql_db   = 'lvteknik3';
	}
	else if ($_SERVER['SERVER_NAME'] === 'localhost')
	{
		$mysql_host = 'localhost';
		$mysql_user = 'lvteknik3';
		$mysql_pass = 'oYfMXHgsPVULQHEH';
		$mysql_db   = 'lvteknik3';
	}
	else if (strpos($_SERVER['SERVER_NAME'], 'lvteknik.se') !== false) 
	{
		$mysql_host = 'mysql13.loopia.se';
		$mysql_user = 'lvteknik@l285014';
		$mysql_pass = 'RV9AMbmOhq';
		$mysql_db   = 'lvteknik_se';
	}
	else if (strpos($_SERVER['SERVER_NAME'], 'lvteknik.com') !== false) 
	{
		$mysql_host = 'mysql13.loopia.se';
		$mysql_user = 'lvteknik@l285014';
		$mysql_pass = 'RV9AMbmOhq';
		$mysql_db   = 'lvteknik_se';
	}
	if (phpversion() > 5)
	{
		$link = @mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
        if (!$link) {
			die('Connect Error: ' . mysqli_connect_errno());
		}
        mysqli_set_charset($link, 'utf8');
	}	
}
?>