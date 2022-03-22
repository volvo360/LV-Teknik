<style>
    .modal-footer > .float-left {
    width: 100%;
}
</style>

<?php

function print_modal_xl()
{
	echo "<div class=\"modal fade\" id=\"modalXl\" tabindex=\"-1\" aria-labelledby=\"modalXlLabel\" aria-hidden=\"true\">";
		echo "<div class=\"modal-dialog modal-xl\">";
			echo "<div class=\"modal-content\">";

			echo "<div class=\"modal-header\">";
				echo "<h5 class=\"modal-title h4\" id=\"modalXlLabel\">Extra large modal</h5>";
				
				echo "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">";
					echo "<span aria-hidden=\"true\">&times;</span>";
				echo "</button>";
			echo "</div>";
			
				echo "<div class=\"modal-body\" id = \"modalXlbody\">";
				echo "</div>";
				
				echo "<div class=\"modal-footer float-right\" id = \"modalXlfooter\">";
					echo "<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>";
					echo "<button type=\"button\" class=\"btn btn-primary\">Save changes</button>";
			  	echo "</div>";
			echo "</div>";
		echo "</div>";
	echo "</div>";
}

function print_modal()
{
	echo "<div class=\"modal fade\" id=\"myModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\" aria-hidden=\"true\">";
		echo "<div class=\"modal-dialog\">";
			echo "<div class=\"modal-content\">";
				echo "<div class=\"modal-header justify-content-center\">";
					//echo "<h4 id = \"modalHeader\" class=\"title title-up\">Modal title</h4>";
					
					echo "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">";
						echo "<i class=\"now-ui-icons ui-1_simple-remove\"></i>";
					echo "</button>";
				echo "</div>";

				echo "<div id = \"modalBody\" class=\"modal-body\">";
					echo "<p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean. A small river named Duden flows by their place and supplies it with the necessary regelialia. It is a paradisematic country, in which roasted parts of sentences fly into your mouth.";
					echo "</p>";
				echo "</div>";

				echo "<div id = \"modalFooter\" class=\"modal-footer\">";
					echo "<button type=\"button\" class=\"btn btn-default\">Nice Button</button>";
					echo "<button type=\"button\" class=\"btn btn-danger\" data-dismiss=\"modal\">Close</button>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	echo "</div>";
}

?>