<?php

    include_once("./common/db.php");
    include_once("./common/userData.php");

    include_once("./theme.php");

    echo "<!doctype html>";
    echo "<html lang=\"sv\">";

function printIndexTheme()
{
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
    $SliderStart = $langStrings['SliderStart'];

    $SliderStart_array = getLangstringsArray('SliderStart_array', $displayLang);
    
    echo "<div class=\"main-wrapper \">";
    echo "<!-- Slider Start -->";
    echo "<section class=\"slider\">";
        echo "<div class=\"container\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-lg-9 col-md-10\">";
                    echo "<div class=\"block\">";
                        echo "<span class=\"d-block mb-3 text-white text-capitalize\">".$SliderStart[1]."</span>";
                        echo "<h1 class=\"animated fadeInUp mb-5\">".$SliderStart[2]."</h1>";
                        //echo "<a href=\"#intro\" target=\"_blank\" class=\"btn btn-main animated fadeInUp btn-round-full\" >".$SliderStart[3]."<i class=\"btn-icon fa fa-angle-right ml-2\"></i></a>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</section>";
    echo "<!-- Section Intro Start -->";
    
    $indexIntro = $langStrings['indexIntro'];

    $indexIntro_array = getLangstringsArray('indexIntro_array', $displayLang);
    
    echo "<section class=\"section intro\">";
        echo "<div class=\"container\" id = \"intro\">";
            echo "<div class=\"row \">";
                echo "<div class=\"col-lg-8\">";
                    echo "<div class=\"section-title\">";
                        echo "<span class=\"h6 text-color \">".$indexIntro[1]."</span>";
                        echo "<h2 class=\"mt-3 content-title\">".$indexIntro[2]."</h2>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
            echo "<div class=\"row justify-content-center\">";
                echo "<div class=\"col-lg-4 col-md-6 col-12\">";
                    echo "<div class=\"intro-item mb-5 mb-lg-0\">"; 
                        echo "<i class=\"ti-desktop color-one\"></i>";
                        echo "<h4 class=\"mt-4 mb-3\">".$indexIntro[3]."</h4>";
                        echo "<p>".$indexIntro[4]."</p>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"col-lg-4 col-md-6\">";
                    echo "<div class=\"intro-item mb-5 mb-lg-0\">";
                        echo "<i class=\"ti-medall color-one\"></i>"; 
                        echo "<h4 class=\"mt-4 mb-3\">".$indexIntro[5]."</h4>";
                        echo "<p>".$indexIntro[6]."</p>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"col-lg-4 col-md-6\">";
                    echo "<div class=\"intro-item\">";
                        echo "<i class=\"ti-layers-alt color-one\"></i>";
                        echo "<h4 class=\"mt-4 mb-3\">".$indexIntro[7]."</h4>";
                        echo "<p>".$indexIntro[8]."</p>";
                    echo "</div>";
                echo "</div>"; 
            echo "</div>";
        echo "</div>";
    echo "</section>";

    echo "<!-- Section Intro END -->";
    echo "<!-- Section About Start -->";

    $indexAbout = $langStrings['indexAbout'];

    $indexAbout_array = getLangstringsArray('indexAbout_array', $displayLang);
    
    echo "<section class=\"section about position-relative\">";
        echo "<div class=\"bg-about\"></div>";
        echo "<div class=\"container\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-lg-6 offset-lg-6 offset-md-0\">";
                    echo "<div class=\"about-item \">";
                        echo "<span class=\"h6 text-color\">".$indexAbout[1]."</span>";
                        echo "<h2 class=\"mt-3 mb-4 position-relative content-title\">".$indexAbout[3]."</h2>";
                        echo "<div class=\"about-content\">";
                            echo "<h4 class=\"mb-3 position-relative\">".$indexAbout[4]."</h4>";
                            echo "<p class=\"mb-5\">".$indexAbout[2]."</p>";

                            echo "<a href=\"ownpage.php?pageId=yVDKSjT4jWcegM\" class=\"btn btn-main btn-round-full\">".$indexAbout[5]."</a>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</section>";

    echo "<!-- Section About End -->";
    /*echo "<!-- section Counter Start -->";
    echo "<section class=\"section counter\">";
        echo "<div class=\"container\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-lg-3 col-md-6 col-sm-6\">";
                    echo "<div class=\"counter-item text-center mb-5 mb-lg-0\">";
                        echo "<h3 class=\"mb-0\"><span class=\"counter-stat font-weight-bold\">1730</span> +</h3>";
                        echo "<p class=\"text-muted\">Project Done</p>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"col-lg-3 col-md-6 col-sm-6\">";
                    echo "<div class=\"counter-item text-center mb-5 mb-lg-0\">";
                        echo "<h3 class=\"mb-0\"><span class=\"counter-stat font-weight-bold\">125 </span>M </h3>";
                        echo "<p class=\"text-muted\">User Worldwide</p>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"col-lg-3 col-md-6 col-sm-6\">";
                    echo "<div class=\"counter-item text-center mb-5 mb-lg-0\">";
                        echo "<h3 class=\"mb-0\"><span class=\"counter-stat font-weight-bold\">39</span></h3>";
                        echo "<p class=\"text-muted\">Availble Country</p>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"col-lg-3 col-md-6 col-sm-6\">";
                    echo "<div class=\"counter-item text-center\">";
                        echo "<h3 class=\"mb-0\"><span class=\"counter-stat font-weight-bold\">14</span></h3>";
                        echo "<p class=\"text-muted\">Award Winner </p>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</section>";
    echo "<!-- section Counter End  -->";*/
    
    $indexServices = $langStrings['indexServices'];

    $indexServices_array = getLangstringsArray('indexServices_array', $displayLang);
    
    echo "<!--  Section Services Start -->";
    echo "<section class=\"section service border-top\">";
        echo "<div class=\"container\">";
            echo "<div class=\"row justify-content-center\">";
                echo "<div class=\"col-lg-7 text-center\">";
                    echo "<div class=\"section-title\">";
                        echo "<span class=\"h6 text-color\">".$indexServices[1]."</span>";
                        echo "<h2 class=\"mt-3 content-title \">".$indexServices[2]."</h2>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";

            echo "<div class=\"row justify-content-center\">";
                echo "<div class=\"col-lg-4 col-md-6 col-sm-6\">";
                    echo "<div class=\"service-item mb-5\">";
                        echo "<i class=\"ti-desktop\"></i>";
                        echo "<h4 class=\"mb-3\">".$indexServices[3]."</h4>";
                        echo "<p>".$indexServices[4]."</p>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-4 col-md-6 col-sm-6\">";
                    echo "<div class=\"service-item mb-5\">";
                        echo "<i class=\"ti-layers\"></i>";
                        echo "<h4 class=\"mb-3\">".$indexServices[5]."</h4>";
                        echo "<p>".$indexServices[6]."</p>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-4 col-md-6 col-sm-6\">";
                    echo "<div class=\"service-item mb-5\">";
                        echo "<i class=\"ti-bar-chart\"></i>";
                        echo "<h4 class=\"mb-3\">".$indexServices[7]."</h4>";
                        echo "<p>".$indexServices[8]."</p>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-4 col-md-6 col-sm-6\">";
                    echo "<div class=\"service-item mb-5 mb-lg-0\">";
                        echo "<i class=\"ti-vector\"></i>";
                        echo "<h4 class=\"mb-3\">".$indexServices[9]."</h4>";
                        echo "<p>".$indexServices[10]."</p>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-4 col-md-6 col-sm-6\">";
                    echo "<div class=\"service-item mb-5 mb-lg-0\">";
                        echo "<i class=\"ti-android\"></i>";
                        echo "<h4 class=\"mb-3\">".$indexServices[12]."</h4>";
                        echo "<p>".$indexServices[13]."</p>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-4 col-md-6 col-sm-6\">";
                    echo "<div class=\"service-item mb-5 mb-lg-0\">";
                        echo "<i class=\"ti-pencil-alt\"></i>";
                        echo "<h4 class=\"mb-3\">".$indexServices[14]."</h4>";
                        echo "<p>".$indexServices[15]."</p>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</section>";
    echo "<!--  Section Services End -->";
     
    echo "<!-- Section Cta Start -->";
    
    $indexCta = $langStrings['indexCta'];

    $indexCta_array = getLangstringsArray('indexCta_array', $displayLang);
    
    echo "<section class=\"section cta\">";
        echo "<div class=\"container\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-lg-5\">";
                    echo "<div class=\"cta-item  bg-white p-5 rounded\">";
                        echo "<span class=\"h6 text-color\">".$indexCta[1]."</span>";
                        echo "<h2 class=\"mt-2 mb-4\">".$indexCta[3]."</h2>";
                        echo "<p class=\"lead mb-4\">".$indexCta[2]."</p>";
                        echo "<h3><i class=\"ti-mobile mr-3 text-color\"></i>(+46) 0730 969 599</h3>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</section>";
    echo "<!--  Section Cta End-->";
    
    $indexTestimonial = $langStrings['indexTestimonial'];

    $indexTestimonial_array = getLangstringsArray('indexTestimonial_array', $displayLang);
    
    /*echo "<!-- Section Testimonial Start -->";
    echo "<section class=\"section testimonial\">";
        echo "<div class=\"container\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-lg-7 \">";
                    echo "<div class=\"section-title\">";
                        echo "<span class=\"h6 text-color\">".$indexTestimonial[1]."</span>";
                        echo "<h2 class=\"mt-3 content-title\">".$indexTestimonial[2]."</h2>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";

        echo "<div class=\"container\">";
            echo "<div class=\"row testimonial-wrap\">";
                echo "<div class=\"testimonial-item position-relative\">";
                    echo "<i class=\" text-color\"></i>";

                    echo "<div class=\"testimonial-item-content\">";
                        echo "<p class=\"testimonial-text\">".$indexTestimonial[3]."</p>";

                        echo "<div class=\"testimonial-author\">";
                            echo "<h5 class=\"mb-0 text-capitalize\">".$indexTestimonial[4]."</h5>";
                            echo "<p>".$indexTestimonial[5]."</p>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"testimonial-item position-relative\">";
                    echo "<i class=\" text-color\"></i>";

                    echo "<div class=\"testimonial-item-content\">";
                        echo "<p class=\"testimonial-text\">".$indexTestimonial[6]."</p>";

                        echo "<div class=\"testimonial-author\">";
                            echo "<h5 class=\"mb-0 text-capitalize\">".$indexTestimonial[7]."</h5>";
                            echo "<p>".$indexTestimonial[8]."</p>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"testimonial-item position-relative\">";
                    echo "<i class=\" text-color\"></i>";

                    echo "<div class=\"testimonial-item-content\">";
                        echo "<p class=\"testimonial-text\">".$indexTestimonial[9]."</p>";

                        echo "<div class=\"testimonial-author\">";
                            echo "<h5 class=\"mb-0 text-capitalize\">".$indexTestimonial[10]."</h5>";
                            echo "<p>".$indexTestimonial[11]."</p>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
                echo "<div class=\"testimonial-item position-relative\">";
                    echo "<i class=\" text-color\"></i>";

                    echo "<div class=\"testimonial-item-content\">";
                        echo "<p class=\"testimonial-text\">".$indexTestimonial[12]."</p>";

                        echo "<div class=\"testimonial-author\">";
                            echo "<h5 class=\"mb-0 text-capitalize\">".$indexTestimonial[13]."</h5>";
                            echo "<p>".$indexTestimonial[14]."</p>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</section>";
	*/
    echo "<!-- Section Testimonial End -->";
    /*echo "<section class=\"section latest-blog bg-2\">";
        echo "<div class=\"container\">";
            echo "<div class=\"row justify-content-center\">";
                echo "<div class=\"col-lg-7 text-center\">";
                    echo "<div class=\"section-title\">";
                        echo "<span class=\"h6 text-color\">Latest News</span>";
                        echo "<h2 class=\"mt-3 content-title text-white\">Latest articles to enrich knowledge</h2>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";

            echo "<div class=\"row justify-content-center\">";
                echo "<div class=\"col-lg-4 col-md-6 mb-5\">";
                    echo "<div class=\"card bg-transparent border-0\">";
                        echo "<img src=\"images/blog/1.jpg\" alt=\"\" class=\"img-fluid rounded\">";

                        echo "<div class=\"card-body mt-2\">";
                            echo "<div class=\"blog-item-meta\">";
                                echo "<a href=\"#\" class=\"text-white-50\">Design<span class=\"ml-2 mr-2\">/</span></a>";
                                echo "<a href=\"#\"  class=\"text-white-50\">Ui/Ux<span class=\"ml-2\">/</span></a>";
                                echo "<a href=\"#\" class=\"text-white-50 ml-2\"><i class=\"fa fa-user mr-2\"></i>admin</a>";
                            echo "</div>"; 

                            echo "<h3 class=\"mt-3 mb-5 lh-36\"><a href=\"#\" class=\"text-white \">How to improve design with typography?</a></h3>";

                            echo "<a href=\"blog-single.html\" class=\"btn btn-small btn-solid-border btn-round-full text-white\">Learn More</a>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-4 col-md-6 mb-5\">";
                    echo "<div class=\"card border-0 bg-transparent\">";
                        echo "<img src=\"images/blog/2.jpg\" alt=\"\" class=\"img-fluid rounded\">";

                        echo "<div class=\"card-body mt-2\">";
                            echo "<div class=\"blog-item-meta\">";
                                echo "<a href=\"#\" class=\"text-white-50\">Design<span class=\"ml-2 mr-2\">/</span></a>";
                                echo "<a href=\"#\"  class=\"text-white-50\">Ui/Ux<span class=\"ml-2\">/</span></a>";
                                echo "<a href=\"#\" class=\"text-white-50 ml-2\"><i class=\"fa fa-user mr-2\"></i>admin</a>";
                            echo "</div>";  

                            echo "<h3 class=\"mt-3 mb-5 lh-36\"><a href=\"#\" class=\"text-white\">Interactivity design may connect consumer</a></h3>";

                            echo "<a href=\"blog-single.html\" class=\"btn btn-small btn-solid-border btn-round-full text-white\">Learn More</a>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"col-lg-4 col-md-6 mb-5\">";
                    echo "<div class=\"card border-0 bg-transparent\">";
                        echo "<img src=\"images/blog/3.jpg\" alt=\"\" class=\"img-fluid rounded\">";

                        echo "<div class=\"card-body mt-2\">";
                            echo "<div class=\"blog-item-meta\">";
                                echo "<a href=\"#\" class=\"text-white-50\">Design<span class=\"ml-2 mr-2\">/</span></a>";
                                echo "<a href=\"#\"  class=\"text-white-50\">Ui/Ux<span class=\"ml-2\">/</span></a>";
                                echo "<a href=\"#\" class=\"text-white-50 ml-2\"><i class=\"fa fa-user mr-2\"></i>admin</a>";
                            echo "</div>"; 

                            echo "<h3 class=\"mt-3 mb-5 lh-36\"><a href=\"#\" class=\"text-white\">Marketing Strategy to bring more affect</a></h3>";

                            echo "<a href=\"blog-single.html\" class=\"btn btn-small btn-solid-border btn-round-full text-white\">Learn More</a>";
                        echo "</div>";
                    echo "</div>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</section>";*/

    $lastContactArea = $langStrings['lastContactArea'];

    $lastContactArea_array = getLangstringsArray('lastContactArea_array', $displayLang);
    
    echo "<!--Last contact area --!>";
    echo "<section class=\"mt-70 position-relative\">";
        echo "<div class=\"container\">";
        echo "<div class=\"cta-block-2 bg-gray p-5 rounded border-1\">";
            echo "<div class=\"row justify-content-center align-items-center \">";
                echo "<div class=\"col-lg-7\">";
                    echo "<span class=\"text-color\">".$lastContactArea[1]."</span>";
                    echo "<h2 class=\"mt-2 mb-4 mb-lg-0\">".$lastContactArea[2]."</h2>";
                echo "</div>";
                echo "<div class=\"col-lg-4\">";
                    echo "<a href=\"contact.php\" class=\"btn btn-main btn-round-full float-lg-right \">".$lastContactArea[3]."</a>";
                echo "</div>";
            echo "</div>";
        echo "</div>";
    echo "</div>";

    echo "</section>";
    echo "<!--End last contact area --!>";
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']))
{
	printHeader();
	printIndexTheme();
	displayFooter();
}

?>
   