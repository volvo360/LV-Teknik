<?php
	print_header();
	displayMenuAdministradoHeader();
	displayContent();
	displayFooterAdministrado();
	printScripts();
	
	function print_header()
	{
		global $link;

		global $phrase;

		$lang = 'sv';

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
		echo "<!--

		=========================================================
		* Now UI Dashboard - v1.5.0
		=========================================================

		* Product Page: https://www.creative-tim.com/product/now-ui-dashboard
		* Copyright 2019 Creative Tim (http://www.creative-tim.com)

		* Designed by www.invisionapp.com Coded by www.creative-tim.com

		=========================================================

		* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

		-->";
		echo "<!DOCTYPE html>";
		echo "<html lang=\"en\">";

		echo "<head>";
		  echo "<meta charset=\"utf-8\" />";
		  echo "<link rel=\"apple-touch-icon\" sizes=\"76x76\" href=\"//".$url_admin."ext/theme/assets/img/apple-icon.png\">";
		  echo "<link rel=\"icon\" type=\"image/png\" href=\"//".$url_admin."ext/theme/assets/img/favicon.png\">";
		  echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" />";
		  echo "<title>";
			echo "Mina projekt";
		  echo "</title>";
		  echo "<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />";
		  echo "<!--     Fonts and icons     -->";
		  echo "<link href=\"https://fonts.googleapis.com/css?family=Montserrat:400,700,200\" rel=\"stylesheet\" />";
		  echo "<link rel=\"stylesheet\" href=\"https://use.fontawesome.com/releases/v5.7.1/css/all.css\" integrity=\"sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr\" crossorigin=\"anonymous\">";
		  echo "<!-- CSS Files -->";
		  echo "<link href=\"".$url."ext/bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\" />";
		  echo "<link href=\"".$url_admin."ext/theme/assets/css/now-ui-dashboard.css?v=1.5.0\" rel=\"stylesheet\" />";
		  echo "<!-- CSS Just for demo purpose, don't include it in your project -->";
		  echo "<link href=\"".$url_admin."ext/theme/assets/demo/demo.css\" rel=\"stylesheet\" />";
		echo "</head>";
	}
	
	function displayMenu()
	{
		global $link;

		global $phrase;

		$lang = 'sv';

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
		
		echo "<body class=\"\">";
		  echo "<div class=\"wrapper \">";
			echo "<div class=\"sidebar\" data-color=\"orange\">";
			  echo "<!--
				Tip 1: You can change the color of the sidebar using: data-color=\"blue | green | orange | red | yellow\"
			-->";
			  echo "<div class=\"logo\">";
				echo "<a href=\"http://www.creative-tim.com\" class=\"simple-text logo-mini\">";
				  echo "MP";
				echo "</a>";
				echo "<a href=\"http://www.creative-tim.com\" class=\"simple-text logo-normal\">";
				  echo "Mina projekt";
				echo "</a>";
			  echo "</div>";
			  echo "<div class=\"sidebar-wrapper\" id=\"sidebar-wrapper\">";
				echo "<ul class=\"nav\">";
				  echo "<li class=\"active \">";
					echo "<a href=\"./dashboard.html\">";
					  echo "<i class=\"now-ui-icons design_app\"></i>";
					  echo "<p>Dashboard</p>";
					echo "</a>";
				  echo "</li>";
				  echo "<li>";
					echo "<a href=\"./icons.html\">";
					  echo "<i class=\"now-ui-icons education_atom\"></i>";
					  echo "<p>Icons</p>";
					echo "</a>";
				  echo "</li>";
				  echo "<li>";
					echo "<a href=\"./map.html\">";
					  echo "<i class=\"now-ui-icons location_map-big\"></i>";
					  echo "<p>Maps</p>";
					echo "</a>";
				  echo "</li>";
				  echo "<li>";
					echo "<a href=\"./notifications.html\">";
					  echo "<i class=\"now-ui-icons ui-1_bell-53\"></i>";
					  echo "<p>Notifications</p>";
					echo "</a>";
				  echo "</li>";
				  echo "<li>";
					echo "<a href=\"./user.html\">";
					  echo "<i class=\"now-ui-icons users_single-02\"></i>";
					  echo "<p>User Profile</p>";
					echo "</a>";
				  echo "</li>";
				  echo "<li>";
					echo "<a href=\"./tables.html\">";
					  echo "<i class=\"now-ui-icons design_bullet-list-67\"></i>";
					  echo "<p>Table List</p>";
					echo "</a>";
				  echo "</li>";
				  echo "<li>";
					echo "<a href=\"./typography.html\">";
					  echo "<i class=\"now-ui-icons text_caps-small\"></i>";
					  echo "<p>Typography</p>";
					echo "</a>";
				  echo "</li>";
				  echo "<li class=\"active-pro\">";
					echo "<a href=\"./upgrade.html\">";
					  echo "<i class=\"now-ui-icons arrows-1_cloud-download-93\"></i>";
					  echo "<p>Upgrade to PRO</p>";
					echo "</a>";
				  echo "</li>";
				echo "</ul>";
			  echo "</div>";
			echo "</div>";
			echo "<div class=\"main-panel\" id=\"main-panel\">";
			  echo "<!-- Navbar -->";
			  echo "<nav class=\"navbar navbar-expand-lg navbar-transparent  bg-primary  navbar-absolute\">";
				echo "<div class=\"container-fluid\">";
				  echo "<div class=\"navbar-wrapper\">";
					echo "<div class=\"navbar-toggle\">";
					  echo "<button type=\"button\" class=\"navbar-toggler\">";
						echo "<span class=\"navbar-toggler-bar bar1\"></span>";
						echo "<span class=\"navbar-toggler-bar bar2\"></span>";
						echo "<span class=\"navbar-toggler-bar bar3\"></span>";
					  echo "</button>";
					echo "</div>";
					echo "<a class=\"navbar-brand\" href=\"#pablo\">Dashboard</a>";
				  echo "</div>";
				  echo "<button class=\"navbar-toggler\" type=\"button\" data-toggle=\"collapse\" data-target=\"#navigation\" aria-controls=\"navigation-index\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">";
					echo "<span class=\"navbar-toggler-bar navbar-kebab\"></span>";
					echo "<span class=\"navbar-toggler-bar navbar-kebab\"></span>";
					echo "<span class=\"navbar-toggler-bar navbar-kebab\"></span>";
				  echo "</button>";
				  echo "<div class=\"collapse navbar-collapse justify-content-end\" id=\"navigation\">";
					echo "<form>";
					  echo "<div class=\"input-group no-border\">";
						echo "<input type=\"text\" value=\"\" class=\"form-control\" placeholder=\"Search...\">";
						echo "<div class=\"input-group-append\">";
						  echo "<div class=\"input-group-text\">";
							echo "<i class=\"now-ui-icons ui-1_zoom-bold\"></i>";
						  echo "</div>";
						echo "</div>";
					  echo "</div>";
					echo "</form>";
					echo "<ul class=\"navbar-nav\">";
					  echo "<li class=\"nav-item\">";
						echo "<a class=\"nav-link\" href=\"#pablo\">";
						  echo "<i class=\"now-ui-icons media-2_sound-wave\"></i>";
						  echo "<p>";
							echo "<span class=\"d-lg-none d-md-block\">Stats</span>";
						  echo "</p>";
						echo "</a>";
					  echo "</li>";
					  echo "<li class=\"nav-item dropdown\">";
						echo "<a class=\"nav-link dropdown-toggle\" id=\"navbarDropdownMenuLink\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">";
						  echo "<i class=\"now-ui-icons location_world\"></i>";
						  echo "<p>";
							echo "<span class=\"d-lg-none d-md-block\">Some Actions</span>";
						  echo "</p>";
						echo "</a>";
						echo "<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"navbarDropdownMenuLink\">";
						  echo "<a class=\"dropdown-item\" href=\"#\">Action</a>";
						  echo "<a class=\"dropdown-item\" href=\"#\">Another action</a>";
						  echo "<a class=\"dropdown-item\" href=\"#\">Something else here</a>";
						echo "</div>";
					  echo "</li>";
					  echo "<li class=\"nav-item\">";
						echo "<a class=\"nav-link\" href=\"#pablo\">";
						  echo "<i class=\"now-ui-icons users_single-02\"></i>";
						  echo "<p>";
							echo "<span class=\"d-lg-none d-md-block\">Account</span>";
						  echo "</p>";
						echo "</a>";
					  echo "</li>";
					echo "</ul>";
				  echo "</div>";
				echo "</div>";
			  echo "</nav>";
			  echo "<!-- End Navbar -->";
	}
	
	function displayContent()
	{
		global $link;

		global $phrase;

		$lang = 'sv';

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
		
			echo "<div class=\"panel-header panel-header-lg\">";
				echo "<canvas id=\"bigDashboardChart\"></canvas>";
			  echo "</div>";
			  echo "<div class=\"content\">";
				echo "<div class=\"row\">";
				  echo "<div class=\"col-lg-4\">";
					echo "<div class=\"card card-chart\">";
					  echo "<div class=\"card-header\">";
						echo "<h5 class=\"card-category\">Global Sales</h5>";
						echo "<h4 class=\"card-title\">Shipped Products</h4>";
						echo "<div class=\"dropdown\">";
						  echo "<button type=\"button\" class=\"btn btn-round btn-outline-default dropdown-toggle btn-simple btn-icon no-caret\" data-toggle=\"dropdown\">";
							echo "<i class=\"now-ui-icons loader_gear\"></i>";
						  echo "</button>";
						  echo "<div class=\"dropdown-menu dropdown-menu-right\">";
							echo "<a class=\"dropdown-item\" href=\"#\">Action</a>";
							echo "<a class=\"dropdown-item\" href=\"#\">Another action</a>";
							echo "<a class=\"dropdown-item\" href=\"#\">Something else here</a>";
							echo "<a class=\"dropdown-item text-danger\" href=\"#\">Remove Data</a>";
						  echo "</div>";
						echo "</div>";
					  echo "</div>";
					  echo "<div class=\"card-body\">";
						echo "<div class=\"chart-area\">";
						  echo "<canvas id=\"lineChartExample\"></canvas>";
						echo "</div>";
					  echo "</div>";
					  echo "<div class=\"card-footer\">";
						echo "<div class=\"stats\">";
						  echo "<i class=\"now-ui-icons arrows-1_refresh-69\"></i> Just Updated";
						echo "</div>";
					  echo "</div>";
					echo "</div>";
				  echo "</div>";
				  echo "<div class=\"col-lg-4 col-md-6\">";
					echo "<div class=\"card card-chart\">";
					  echo "<div class=\"card-header\">";
						echo "<h5 class=\"card-category\">2018 Sales</h5>";
						echo "<h4 class=\"card-title\">All products</h4>";
						echo "<div class=\"dropdown\">";
						  echo "<button type=\"button\" class=\"btn btn-round btn-outline-default dropdown-toggle btn-simple btn-icon no-caret\" data-toggle=\"dropdown\">";
							echo "<i class=\"now-ui-icons loader_gear\"></i>";
						  echo "</button>";
						  echo "<div class=\"dropdown-menu dropdown-menu-right\">";
							echo "<a class=\"dropdown-item\" href=\"#\">Action</a>";
							echo "<a class=\"dropdown-item\" href=\"#\">Another action</a>";
							echo "<a class=\"dropdown-item\" href=\"#\">Something else here</a>";
							echo "<a class=\"dropdown-item text-danger\" href=\"#\">Remove Data</a>";
						  echo "</div>";
						echo "</div>";
					  echo "</div>";
					  echo "<div class=\"card-body\">";
						echo "<div class=\"chart-area\">";
						  echo "<canvas id=\"lineChartExampleWithNumbersAndGrid\"></canvas>";
						echo "</div>";
					  echo "</div>";
					  echo "<div class=\"card-footer\">";
						echo "<div class=\"stats\">";
						  echo "<i class=\"now-ui-icons arrows-1_refresh-69\"></i> Just Updated";
						echo "</div>";
					  echo "</div>";
					echo "</div>";
				  echo "</div>";
				  echo "<div class=\"col-lg-4 col-md-6\">";
					echo "<div class=\"card card-chart\">";
					  echo "<div class=\"card-header\">";
						echo "<h5 class=\"card-category\">Email Statistics</h5>";
						echo "<h4 class=\"card-title\">24 Hours Performance</h4>";
					  echo "</div>";
					  echo "<div class=\"card-body\">";
						echo "<div class=\"chart-area\">";
						  echo "<canvas id=\"barChartSimpleGradientsNumbers\"></canvas>";
						echo "</div>";
					  echo "</div>";
					  echo "<div class=\"card-footer\">";
						echo "<div class=\"stats\">";
						  echo "<i class=\"now-ui-icons ui-2_time-alarm\"></i> Last 7 days";
						echo "</div>";
					  echo "</div>";
					echo "</div>";
				  echo "</div>";
				echo "</div>";
				echo "<div class=\"row\">";
				  echo "<div class=\"col-md-6\">";
					echo "<div class=\"card  card-tasks\">";
					  echo "<div class=\"card-header \">";
						echo "<h5 class=\"card-category\">Backend development</h5>";
						echo "<h4 class=\"card-title\">Tasks</h4>";
					  echo "</div>";
					  echo "<div class=\"card-body \">";
						echo "<div class=\"table-full-width table-responsive\">";
						  echo "<table class=\"table\">";
							echo "<tbody>";
							  echo "<tr>";
								echo "<td>";
								  echo "<div class=\"form-check\">";
									echo "<label class=\"form-check-label\">";
									  echo "<input class=\"form-check-input\" type=\"checkbox\" checked>";
									  echo "<span class=\"form-check-sign\"></span>";
									echo "</label>";
								  echo "</div>";
								echo "</td>";
								echo "<td class=\"text-left\">Sign contract for \"What are conference organizers afraid of?\"</td>";
								echo "<td class=\"td-actions text-right\">";
								  echo "<button type=\"button\" rel=\"tooltip\" title=\"\" class=\"btn btn-info btn-round btn-icon btn-icon-mini btn-neutral\" data-original-title=\"Edit Task\">";
									echo "<i class=\"now-ui-icons ui-2_settings-90\"></i>";
								  echo "</button>";
								  echo "<button type=\"button\" rel=\"tooltip\" title=\"\" class=\"btn btn-danger btn-round btn-icon btn-icon-mini btn-neutral\" data-original-title=\"Remove\">";
									echo "<i class=\"now-ui-icons ui-1_simple-remove\"></i>";
								  echo "</button>";
								echo "</td>";
							  echo "</tr>";
							  echo "<tr>";
								echo "<td>";
								  echo "<div class=\"form-check\">";
									echo "<label class=\"form-check-label\">";
									  echo "<input class=\"form-check-input\" type=\"checkbox\">";
									  echo "<span class=\"form-check-sign\"></span>";
									echo "</label>";
								  echo "</div>";
								echo "</td>";
								echo "<td class=\"text-left\">Lines From Great Russian Literature? Or E-mails From My Boss?</td>";
								echo "<td class=\"td-actions text-right\">";
								  echo "<button type=\"button\" rel=\"tooltip\" title=\"\" class=\"btn btn-info btn-round btn-icon btn-icon-mini btn-neutral\" data-original-title=\"Edit Task\">";
									echo "<i class=\"now-ui-icons ui-2_settings-90\"></i>";
								  echo "</button>";
								  echo "<button type=\"button\" rel=\"tooltip\" title=\"\" class=\"btn btn-danger btn-round btn-icon btn-icon-mini btn-neutral\" data-original-title=\"Remove\">";
									echo "<i class=\"now-ui-icons ui-1_simple-remove\"></i>";
								  echo "</button>";
								echo "</td>";
							  echo "</tr>";
							  echo "<tr>";
								echo "<td>";
								  echo "<div class=\"form-check\">";
									echo "<label class=\"form-check-label\">";
									  echo "<input class=\"form-check-input\" type=\"checkbox\" checked>";
									  echo "<span class=\"form-check-sign\"></span>";
									echo "</label>";
								  echo "</div>";
								echo "</td>";
								echo "<td class=\"text-left\">Flooded: One year later, assessing what was lost and what was found when a ravaging rain swept through metro Detroit";
								echo "</td>";
								echo "<td class=\"td-actions text-right\">";
								  echo "<button type=\"button\" rel=\"tooltip\" title=\"\" class=\"btn btn-info btn-round btn-icon btn-icon-mini btn-neutral\" data-original-title=\"Edit Task\">";
									echo "<i class=\"now-ui-icons ui-2_settings-90\"></i>";
								  echo "</button>";
								  echo "<button type=\"button\" rel=\"tooltip\" title=\"\" class=\"btn btn-danger btn-round btn-icon btn-icon-mini btn-neutral\" data-original-title=\"Remove\">";
									echo "<i class=\"now-ui-icons ui-1_simple-remove\"></i>";
								  echo "</button>";
								echo "</td>";
							  echo "</tr>";
							echo "</tbody>";
						  echo "</table>";
						echo "</div>";
					  echo "</div>";
					  echo "<div class=\"card-footer \">";
						echo "<hr>";
						echo "<div class=\"stats\">";
						  echo "<i class=\"now-ui-icons loader_refresh spin\"></i> Updated 3 minutes ago";
						echo "</div>";
					  echo "</div>";
					echo "</div>";
				  echo "</div>";
				  echo "<div class=\"col-md-6\">";
					echo "<div class=\"card\">";
					  echo "<div class=\"card-header\">";
						echo "<h5 class=\"card-category\">All Persons List</h5>";
						echo "<h4 class=\"card-title\"> Employees Stats</h4>";
					  echo "</div>";
					  echo "<div class=\"card-body\">";
						echo "<div class=\"table-responsive\">";
						  echo "<table class=\"table\">";
							echo "<thead class=\" text-primary\">";
							  echo "<th>";
								echo "Name";
							  echo "</th>";
							  echo "<th>";
								echo "Country";
							  echo "</th>";
							  echo "<th>";
								echo "City";
							  echo "</th>";
							  echo "<th class=\"text-right\">";
								echo "Salary";
							  echo "</th>";
							echo "</thead>";
							echo "<tbody>";
							  echo "<tr>";
								echo "<td>";
								  echo "Dakota Rice";
								echo "</td>";
								echo "<td>";
								  echo "Niger";
								echo "</td>";
								echo "<td>";
								  echo "Oud-Turnhout";
								echo "</td>";
								echo "<td class=\"text-right\">";
								  echo "$36,738";
								echo "</td>";
							  echo "</tr>";
							  echo "<tr>";
								echo "<td>";
								  echo "Minerva Hooper";
								echo "</td>";
								echo "<td>";
								  echo "Curaçao";
								echo "</td>";
								echo "<td>";
								  echo "Sinaai-Waas";
								echo "</td>";
								echo "<td class=\"text-right\">";
								  echo "$23,789";
								echo "</td>";
							  echo "</tr>";
							  echo "<tr>";
								echo "<td>";
								  echo "Sage Rodriguez";
								echo "</td>";
								echo "<td>";
								  echo "Netherlands";
								echo "</td>";
								echo "<td>";
								  echo "Baileux";
								echo "</td>";
								echo "<td class=\"text-right\">";
								  echo "$56,142";
								echo "</td>";
							  echo "</tr>";
							  echo "<tr>";
								echo "<td>";
								  echo "Doris Greene";
								echo "</td>";
								echo "<td>";
								  echo "Malawi";
								echo "</td>";
								echo "<td>";
								  echo "Feldkirchen in Kärnten";
								echo "</td>";
								echo "<td class=\"text-right\">";
								  echo "$63,542";
								echo "</td>";
							  echo "</tr>";
							  echo "<tr>";
								echo "<td>";
								  echo "Mason Porter";
								echo "</td>";
								echo "<td>";
								  echo "Chile";
								echo "</td>";
								echo "<td>";
								  echo "Gloucester";
								echo "</td>";
								echo "<td class=\"text-right\">";
								  echo "$78,615";
								echo "</td>";
							  echo "</tr>";
							echo "</tbody>";
						  echo "</table>";
						echo "</div>";
					  echo "</div>";
					echo "</div>";
				  echo "</div>";
				echo "</div>";
			  echo "</div>";
	}
			function displayFooter()
			{
				global $link;

				global $phrase;

				$lang = 'sv';

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
				echo "<footer class=\"footer\">";
				echo "<div class=\" container-fluid \">";
				  echo "<nav>";
					echo "<ul>";
					  echo "<li>";
						echo "<a href=\"https://www.creative-tim.com\">";
						  echo "Creative Tim";
						echo "</a>";
					  echo "</li>";
					  echo "<li>";
						echo "<a href=\"http://presentation.creative-tim.com\">";
						  echo "About Us";
						echo "</a>";
					  echo "</li>";
					  echo "<li>";
						echo "<a href=\"http://blog.creative-tim.com\">";
						  echo "Blog";
						echo "</a>";
					  echo "</li>";
					echo "</ul>";
				  echo "</nav>";
				  echo "<div class=\"copyright\" id=\"copyright\">";
					echo "&copy; <script>";
					  echo "document.getElementById('copyright').appendChild(document.createTextNode(new Date().getFullYear()))";
					echo "</script>, Designed by <a href=\"https://www.invisionapp.com\" target=\"_blank\">Invision</a>. Coded by <a href=\"https://www.creative-tim.com\" target=\"_blank\">Creative Tim</a>.";
				  echo "</div>";
				echo "</div>";
			  echo "</footer>";
			echo "</div>";
		  echo "</div>";
	
	} 
	function printScripts()
	{	
		global $link;

		global $phrase;

		$lang = 'sv';

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
		echo "<!--   Core JS Files   -->";
		  echo "<script src=\"".$url_admin."ext/theme/assets/js/core/jquery.min.js\"></script>";
		  //echo "<script src=\"".$url."/assets/js/core/popper.min.js\"></script>";
		  echo "<script src=\"".$url."ext/bootstrap/js/bootstrap.bundle.min.js\"></script>";
		  echo "<script src=\"".$url_admin."ext/theme/assets/js/plugins/perfect-scrollbar.jquery.min.js\"></script>";
		  echo "<!--  Google Maps Plugin    -->";
		  echo "<script src=\"https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE\"></script>";
		  echo "<!-- Chart JS -->";
		  echo "<script src=\"".$url_admin."ext/theme/assets/js/plugins/chartjs.min.js\"></script>";
		  echo "<!--  Notifications Plugin    -->";
		  echo "<script src=\"".$url_admin."ext/theme/assets/js/plugins/bootstrap-notify.js\"></script>";
		  echo "<!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->";
		  echo "<script src=\"".$url_admin."ext/theme/assets/js/now-ui-dashboard.min.js?v=1.5.0\" type=\"text/javascript\"></script><!-- Now Ui Dashboard DEMO methods, don't include it in your project! -->";
		  echo "<script src=\"".$url_admin."ext/theme/assets/demo/demo.js\"></script>";
		  echo "<script>";
			echo "$(document).ready(function() {";
			  echo "// Javascript method's body can be found in assets/js/demos.js";
			  echo "demo.initDashboardPageCharts();";

			echo "});";
		  echo "</script>";
		echo "</body>";

		echo "</html>";
	}
?>