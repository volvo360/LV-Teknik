<?php
	session_start();
	if ($_SERVER['SERVER_NAME'] === 'localhost')
		{
			$url = "//localhost/lv/easyproject/";
			$url_admin = "//localhost/lv/easyproject/administrado/";
		}
		else if ($_SERVER['SERVER_NAME'] === 'server01')
		{
			$url = "//server01/flexshare/ep/";
			$url_admin = "//server01/flexshare/ep/administrado/¨";
		}
		else
		{
			$url = "//mina-projekt.se/";
			$url_admin = "//mina-projekt.se/administrado/";
		}
	header("Location: ".$url);
?>