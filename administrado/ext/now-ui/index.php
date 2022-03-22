<?php

session_start();

error_reporting(E_ALL);
nav();
footer();
function nav()
{
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
	  echo "<link rel=\"apple-touch-icon\" sizes=\"76x76\" href=\"assets/img/apple-icon.png\">";
	  echo "<link rel=\"icon\" type=\"image/png\" href=\"assets/img/favicon.png\">";
	  echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" />";
	  echo "<title>";
		echo "Now UI Dashboard by Creative Tim";
	  echo "</title>";
	  echo "<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />";
	  echo "<!--     Fonts and icons     -->";
	  echo "<link href=\"//fonts.googleapis.com/css?family=Montserrat:400,700,200\" rel=\"stylesheet\" />";
	  echo "<link rel=\"stylesheet\" href=\"https://use.fontawesome.com/releases/v5.7.1/css/all.css\" integrity=\"sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr\" crossorigin=\"anonymous\">";
	  echo "<!-- CSS Files -->";
	  echo "<link href=\"assets/css/bootstrap.min.css\" rel=\"stylesheet\" />";
	  echo "<link href=\"assets/css/now-ui-dashboard.css?v=1.5.0\" rel=\"stylesheet\" />";
	  echo "<!-- CSS Just for demo purpose, don't include it in your project -->";
	  echo "<link href=\"assets/demo/demo.css\" rel=\"stylesheet\" />";
	echo "</head>";

	echo "<body class=\"\">";
	  echo "<div class=\"wrapper \">";
		echo "<div class=\"sidebar\" data-color=\"orange\">";
		  echo "<!--
			Tip 1: You can change the color of the sidebar using: data-color=\"blue | green | orange | red | yellow\"
		-->";
		  echo "<div class=\"logo\">";
			echo "<a href=\"http://www.creative-tim.com\" class=\"simple-text logo-mini\">";
			  echo "CT";
			echo "</a>";
			echo "<a href=\"http://www.creative-tim.com\" class=\"simple-text logo-normal\">";
			  echo "Creative Tim";
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

			echo "<div  class=\"panel-header panel-header-lg\" style = \"height : 5%;\">";
			echo "</div>";
			echo "<div id = \"ajax-panel-header\" class = \"content\" style = \"height : 80%;\">";
			echo "</div>";	
}

function footer()
{
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
  echo "<!--   Core JS Files   -->";
  echo "<script src=\"assets/js/core/jquery.min.js\"></script>";
  echo "<script src=\"assets/js/core/popper.min.js\"></script>";
  echo "<script src=\"assets/js/core/bootstrap.min.js\"></script>";
  echo "<script src=\"assets/js/plugins/perfect-scrollbar.jquery.min.js\"></script>";
  echo "<!--  Notifications Plugin    -->";
  echo "<script src=\"assets/js/plugins/bootstrap-notify.js\"></script>";
  echo "<!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->";
  echo "<script src=\"assets/js/now-ui-dashboard.min.js?v=1.5.0\" type=\"text/javascript\"></script><!-- Now Ui Dashboard DEMO methods, don't include it in your project! -->";
  echo "<script src=\"assets/demo/demo.js\"></script>";
  echo "<script>";
    echo "$(document).ready(function() {";
      echo "// Javascript method's body can be found in assets/js/demos.js";
      echo "demo.initDashboardPageCharts();";

    echo "});";
 echo "</script>";
}
?>