<?php
	if ($_SERVER['SERVER_NAME'] === 'localhost')
	{
		$url = "//localhost/lv/";
		$url_admin = "//localhost/lv/administrado/";
		$url_servotablo = "//localhost/lv/servotablo/";
	}
	else if ($_SERVER['SERVER_NAME'] === 'server01')
	{
		$url = "//server01/flexshare/lv/";
		$url_admin = "//server01/flexshare/lv/administrado/";
		$url_servotablo = "//server01/flexshare/lv/servotablo/";
	}
	else if (strpos($_SERVER['SERVER_NAME'], 'lvteknik.com') !== false)
	{
		$url = "//www.lvteknik.com/";
		$url_admin = "//www.lvteknik.com/administrado/";
		$url_servotablo = "//www.lvteknik.com/servotablo/";
	}
	else
	{
		$url = "//www.lvteknik.se/";
		$url_admin = "//www.lvteknik.se/administrado/";
		$url_servotablo = "//www.lvteknik.se/servotablo/";
	}	

	header("Location: ".$url);
?>