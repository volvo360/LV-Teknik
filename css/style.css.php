<?php
header("Content-type: text/css", true);

include_once("../common/db.php");

function getPageData()
{
    global $link;
    
    global $phrase;
    
    $table = "`".PREFIX."own_pages`";
    
    $sql = "SELECT *, CAST(AES_DECRYPT(headerImage, SHA2('".$phrase."', 512)) AS CHAR) as headerImage FROM ".$table." WHERE tableKey = '".mysqli_real_escape_string($link, $_GET['pageId'])."'";
    
    //echo __LINE__." ".$sql."<br>";
    
    $result =  mysqli_query($link, $sql) or die(__LINE__.": $sql - Error: ".mysqli_error($link));
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
    {
        $headerImage = $row['headerImage'];
    }
    
    return $headerImage;
}

?>
/*
THEME: Megakit | HTML5 Agency Template
VERSION: 1.0.0
AUTHOR: Themefisher

HOMEPAGE: https://themefisher.com/products/megakit-multipurpose-bootstrap-template/
DEMO: https://demo.themefisher.com/megakit/
GITHUB: https://github.com/themefisher/Megakit-Bootstrap-Agency-Template

WEBSITE: https://themefisher.com
TWITTER: https://twitter.com/themefisher
FACEBOOK: https://www.facebook.com/themefisher
*/
/*=== MEDIA QUERY ===*/
@import url("https://fonts.googleapis.com/css2?family=Hind:wght@400;500;600;700&family=Montserrat:wght@400;700&family=Poppins:wght@300;400;600;700&display=swap");
html {
  overflow-x: hidden;
}

body {
  line-height: 1.5;
  font-family: "Hind", serif;
  -webkit-font-smoothing: antialiased;
  font-size: 17px;
  color: rgba(0, 0, 0, 0.65);
}

h1, .h1, h2, .h2, h3, .h3, h4, .h4, h5, .h5, h6, .h6 {
  font-family: "Poppins", sans-serif;
  font-weight: 600;
  color: #242424;
}

h1, .h1 {
  font-size: 2.5rem;
}

h2, .h2 {
  font-size: 2rem;
  font-weight: 600;
  line-height: 42px;
}

h3, .h3 {
  font-size: 1.5rem;
}

h4, .h4 {
  font-size: 1.3rem;
  line-height: 30px;
}

h5, .h5 {
  font-size: 1.25rem;
}

h6, .h6 {
  font-size: 1rem;
}

p {
  line-height: 30px;
}

.navbar-toggle .icon-bar {
  background: #f75757;
}

input[type="email"], input[type="password"], input[type="text"], input[type="tel"] {
  box-shadow: none;
  height: 45px;
  outline: none;
  font-size: 14px;
}

input[type="email"]:focus, input[type="password"]:focus, input[type="text"]:focus, input[type="tel"]:focus {
  box-shadow: none;
  border: 1px solid #f75757;
}

.form-control {
  box-shadow: none;
  border-radius: 0;
}

.form-control:focus {
  box-shadow: none;
  border: 1px solid #f75757;
}

.py-7 {
  padding: 7rem 0px;
}

.btn {
  display: inline-block;
  font-size: 14px;
  font-size: 0.8125rem;
  font-weight: 500;
  padding: 1rem 2.5rem .8rem;
  text-transform: uppercase;
  border-radius: 0;
  transition: 0.3s;
}

.btn.btn-icon i {
  font-size: 16px;
  vertical-align: middle;
  margin-right: 5px;
}

.btn:focus {
  outline: 0px;
  box-shadow: none;
}

.btn-main, .btn-transparent, .btn-small {
  background: #f75757;
  color: #fff;
  transition: all 0.2s ease;
}

.btn-main:hover, .btn-transparent:hover, .btn-small:hover {
  background: #f52626;
  color: #fff;
}

.btn-solid-border {
  border: 2px solid #f75757;
  background: transparent;
  color: #242424;
}

.btn-solid-border:hover {
  border: 2px solid #f75757;
  background: #f75757;
}

.btn-transparent {
  background: transparent;
  padding: 0;
  color: #f75757;
}

.btn-transparent:hover {
  background: transparent;
  color: #f75757;
}

.btn-large {
  padding: 20px 45px;
}

.btn-large.btn-icon i {
  font-size: 16px;
  vertical-align: middle;
  margin-right: 5px;
}

.btn-small {
  padding: 13px 25px 10px;
  font-size: 12px;
}

.btn-round {
  border-radius: 4px;
}

.btn-round-full {
  border-radius: 50px;
}

.btn.active:focus, .btn:active:focus, .btn:focus {
  outline: 0;
}

.bg-gray {
  background: #f5f8f9;
}

.bg-primary {
  background: #f75757;
}

.bg-primary-dark {
  background: #f52626;
}

.bg-primary-darker {
  background: #dd0b0b;
}

.bg-dark {
  background: #242424;
}

.bg-gradient {
  background-image: linear-gradient(145deg, rgba(19, 177, 205, 0.95) 0%, rgba(152, 119, 234, 0.95) 100%);
  background-repeat: repeat-x;
}

.section {
  padding: 100px 0;
}

.section-sm {
  padding: 70px 0;
}

.section-title {
  margin-bottom: 70px;
}

.section-title .title {
  font-size: 50px;
  line-height: 50px;
}

.section-title p {
  color: #666;
  font-family: "Poppins", sans-serif;
}

.subtitle {
  color: #f75757;
  font-size: 14px;
  letter-spacing: 1px;
}

.overly, .page-title, .slider, .cta, .hero-img {
  position: relative;
}

.overly:before, .page-title:before, .slider:before, .cta:before, .hero-img:before {
  content: "";
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
  opacity: 0.5;
  background: #000;
}

.overly-2, .bg-counter, .cta-block, .latest-blog {
  position: relative;
}

.overly-2:before, .bg-counter:before, .cta-block:before, .latest-blog:before {
  content: "";
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
}

.text-color {
  color: #f75757;
}

.text-black {
  color: #242424;
}

.text-color2 {
  color: #c54041;
}

.text-color2 {
  color: #b99769;
}

.text-sm {
  font-size: 14px;
}

.text-md {
  font-size: 2.25rem;
}

.text-lg {
  font-size: 3.75rem;
}

.no-spacing {
  letter-spacing: 0px;
}

/* Links */
a {
  color: #242424;
  text-decoration: none;
}

a:focus, a:hover {
  color: #f75757;
  text-decoration: none;
}

a:focus {
  outline: none;
}

.content-title {
  font-size: 40px;
  line-height: 50px;
}

.page-title {
  padding: 100px 0;
}

.page-title .block h1 {
  color: #fff;
}

.page-title .block p {
  color: #fff;
}

.page-wrapper {
  padding: 70px 0;
}

#wrapper-work {
  overflow: hidden;
  padding-top: 100px;
}

#wrapper-work ul li {
  width: 50%;
  float: left;
  position: relative;
}

#wrapper-work ul li img {
  width: 100%;
  height: 100%;
}

#wrapper-work ul li .items-text {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  width: 100%;
  height: 100%;
  color: #fff;
  background: rgba(0, 0, 0, 0.6);
  padding-left: 44px;
  padding-top: 140px;
}

#wrapper-work ul li .items-text h2 {
  padding-bottom: 28px;
  padding-top: 75px;
  position: relative;
}

#wrapper-work ul li .items-text h2:before {
  content: "";
  position: absolute;
  left: 0;
  bottom: 0;
  width: 75px;
  height: 3px;
  background: #fff;
}

#wrapper-work ul li .items-text p {
  padding-top: 30px;
  font-size: 16px;
  line-height: 27px;
  font-weight: 300;
  padding-right: 80px;
}

/*--
	features-work Start 
--*/
#features-work {
  padding-top: 50px;
  padding-bottom: 75px;
}

#features-work .block ul li {
  width: 19%;
  text-align: center;
  display: inline-block;
  padding: 40px 0px;
}

#navbar {
  background: #222328;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

#navbar li {
  padding-left: 15px;
}

@media (max-width: 992px) {
  #navbar li {
    padding-left: 0;
  }
}

#navbar .nav-link {
  font-family: "Poppins", sans-serif;
  font-weight: 500;
  color: #fff;
  text-transform: uppercase;
  font-size: 14px;
  letter-spacing: .5px;
  transition: all .25s ease;
}

#navbar .nav-link:hover, #navbar .nav-link:focus,
#navbar .active .nav-link {
  color: #f75757;
}

#navbar .btn {
  padding: .7rem 1.5rem .5rem;
  color: #fff;
}

@media (max-width: 992px) {
  #navbar .btn {
    margin: 15px 0 10px;
  }
}

.header-top {
  background: #222328;
  color: #919194;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.header-top .header-top-socials {
  border-right: 1px solid rgba(255, 255, 255, 0.05);
  padding: 12px 0px;
}

.header-top .header-top-socials {
  margin-left: -8px;
}

.header-top .header-top-socials a {
  color: #919194;
  margin-right: 8px;
  font-size: 16px;
  padding: 0 8px;
}

.header-top .header-top-socials a:hover {
  color: #f75757;
}

.header-top .header-top-info {
  color: #919194;
  font-size: 16px;
}

.header-top .header-top-info a span {
  color: #fff;
}

.header-top .header-top-info a {
  margin-left: 35px;
  color: #919194;
}

.navbar-toggler {
  padding: 0;
  font-size: 1.5rem;
  color: #fff;
}

.navbar-toggler:focus {
  outline: 0;
}

.navbar-brand {
  color: #fff;
  font-weight: 600;
  letter-spacing: 1px;
}

.navbar-brand span {
  color: #f75757;
}

.dropdown-menu {
  padding: 0px;
  border: 0;
  border-radius: 0px;
}

@media (max-width: 992px) {
  .dropdown-menu {
    text-align: center;
    float: left !important;
    width: 100%;
    margin: 0;
  }
}

.dropdown-menu li:first-child {
  margin-top: 5px;
}

.dropdown-menu li:last-child {
  margin-bottom: 5px;
}

.dropdown-toggle::after {
  display: none;
}

.dropleft .dropdown-menu,
.dropright .dropdown-menu {
  margin: 0;
}

.dropleft .dropdown-toggle::before,
.dropright .dropdown-toggle::after {
  font-weight: bold;
  font-family: 'Font Awesome 5 Free';
  border: 0;
  font-size: 10px;
  vertical-align: 1px;
}

.dropleft .dropdown-toggle::before {
  content: "\f053";
  margin-right: 5px;
}

.dropright .dropdown-toggle::after {
  content: "\f054";
  margin-left: 5px;
}

.dropdown-item {
  padding: .8rem 1.5rem .55rem;
  text-transform: uppercase;
  font-size: 14px;
  font-weight: 500;
}

@media (max-width: 992px) {
  .dropdown-item {
    padding: .6rem 1.5rem .35rem;
  }
}

.dropdown-submenu.active > .dropdown-toggle,
.dropdown-submenu:hover > .dropdown-item,
.dropdown-item.active,
.dropdown-item:hover {
  background: #f75757;
  color: #fff;
}

ul.dropdown-menu li {
  padding-left: 0px !important;
}

@media (min-width: 992px) {
  .dropdown-menu {
    transition: all .2s ease-in, visibility 0s linear .2s, -webkit-transform .2s linear;
    transition: all .2s ease-in, visibility 0s linear .2s, transform .2s linear;
    transition: all .2s ease-in, visibility 0s linear .2s, transform .2s linear, -webkit-transform .2s linear;
    display: block;
    visibility: hidden;
    opacity: 0;
    min-width: 200px;
    margin-top: 15px;
  }
  .dropdown-menu li:first-child {
    margin-top: 10px;
  }
  .dropdown-menu li:last-child {
    margin-bottom: 10px;
  }
  .dropleft .dropdown-menu,
  .dropright .dropdown-menu {
    margin-top: -10px;
  }
  .dropdown:hover > .dropdown-menu {
    visibility: visible;
    transition: all .45s ease 0s;
    opacity: 1;
  }
}

.bg-1 {
    <?php
        $headerImage = getPageData();
        if (!empty($headerImage))
        {
            echo "background: url(\"".$headerImage."\") no-repeat 50% 50%;";
        }
        else
        {
            echo "background: url(\"../images/bg/home-2.jpg\") no-repeat 50% 50%;";
        }

    ?>
  
  background-size: cover;
}

.bg-2 {
  background: url("../images/bg/home-5.jpg");
  background-size: cover;
}

.slider {
  background: url("../images/bg/home-1.jpg") no-repeat;
  background-size: cover;
  background-position: 10% 0%;
  padding: 200px 0;
  position: relative;
}

@media (max-width: 768px) {
  .slider {
    padding: 150px 0;
  }
}

.slider .block h1 {
  font-size: 70px;
  line-height: 80px;
  font-weight: 600;
  color: #fff;
}

.slider .block p {
  margin-bottom: 30px;
  color: #b9b9b9;
  font-size: 18px;
  line-height: 27px;
  font-weight: 300;
}

.slider .block span {
  letter-spacing: 1px;
}

.intro-item i {
  font-size: 60px;
  line-height: 60px;
}

.color-one {
  color: #f75757;
}

.color-two {
  color: #00d747;
}

.color-three {
  color: #9262ff;
}

.color-four {
  color: #088ed3;
}

.bg-about {
  position: absolute;
  content: "";
  left: 0px;
  top: 0px;
  width: 45%;
  min-height: 650px;
  background: url("../images/about/home-8.jpg") no-repeat;
  background-size: cover;
}

.about-content {
  padding: 20px 0px 0px 80px;
}

.about-content h4 {
  font-weight: 600;
}

.about-content h4:before {
  position: absolute;
  content: "\f576";
  font-family: "Font Awesome 5 Free";
  font-size: 30px;
  position: absolute;
  top: 8px;
  left: -65px;
  font-weight: 700;
}

.counter-item .counter-stat {
  font-size: 50px;
}

.counter-item p {
  margin-bottom: 0px;
}

.bg-counter {
  background: url("../images/bg/counter.jpg") no-repeat;
  background-size: cover;
}

.team-img-hover .team-social li a.facebook {
  background: #6666cc;
}

.team-img-hover .team-social li a.twitter {
  background: #3399cc;
}

.team-img-hover .team-social li a.instagram {
  background: #cc66cc;
}

.team-img-hover .team-social li a.linkedin {
  background: #3399cc;
}

.team-img-hover {
  position: absolute;
  top: 10px;
  left: 10px;
  right: 10px;
  bottom: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.6);
  opacity: 0;
  transition: all 0.2s ease-in-out;
  -webkit-transform: scale(0.8);
          transform: scale(0.8);
}

.team-img-hover li a {
  display: inline-block;
  color: #fff;
  width: 50px;
  height: 50px;
  font-size: 20px;
  line-height: 50px;
  border: 2px solid transparent;
  border-radius: 2px;
  text-align: center;
  -webkit-transform: translateY(0);
          transform: translateY(0);
  -webkit-backface-visibility: hidden;
          backface-visibility: hidden;
  transition: all 0.3s ease-in-out;
}

.team-img-hover:hover li a:hover {
  -webkit-transform: translateY(4px);
          transform: translateY(4px);
}

.team-item:hover .team-img-hover {
  opacity: 1;
  -webkit-transform: scale(1);
          transform: scale(1);
  top: 0px;
  left: 0px;
  right: 0px;
  bottom: 0px;
}

.service-item {
  position: relative;
  padding-left: 80px;
}

.service-item i {
  position: absolute;
  left: 0px;
  top: 5px;
  font-size: 50px;
  opacity: .4;
}

.cta {
  background: url("../images/bg/home-3.jpg") fixed 50% 50%;
  background-size: cover;
  padding: 120px 0px;
}

.cta-block {
  background: url("../images/bg/home-3.jpg") no-repeat;
  background-size: cover;
}

.testimonial-item {
  padding: 50px 30px;
}

.testimonial-item i {
  font-size: 40px;
  position: absolute;
  left: 30px;
  top: 30px;
  z-index: 1;
}

.testimonial-item .testimonial-text {
  font-size: 20px;
  line-height: 38px;
  color: #242424;
  margin-bottom: 30px;
  font-style: italic;
}

.testimonial-item .testimonial-item-content {
  padding-left: 65px;
}

.slick-slide:focus, .slick-slide a {
  outline: none;
}

.hero-img {
  background: url("../images/bg/home-5.jpg");
  position: absolute;
  content: "";
  background-size: cover;
  width: 100%;
  height: 100%;
  top: 0px;
}

.h70 {
  height: 55%;
}

.lh-45 {
  line-height: 45px;
}

.pricing-header h1 {
  font-size: 70px;
  font-weight: 300;
}

.pricing .btn-solid-border {
  border-color: #dedede;
}

.pricing .btn-solid-border:Hover {
  border-color: #f75757;
}

.portflio-item .portfolio-item-content {
  position: absolute;
  content: "";
  right: 0px;
  bottom: 0px;
  opacity: 0;
  transition: all .35s ease;
}

.portflio-item:before {
  position: absolute;
  content: "";
  left: 0px;
  top: 0px;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  opacity: 0;
  transition: all .35s ease;
  overflow: hidden;
}

.portflio-item:hover:before {
  opacity: 1;
}

.portflio-item:hover .portfolio-item-content {
  opacity: 1;
  bottom: 20px;
  right: 30px;
}

.portflio-item .overlay-item {
  position: absolute;
  content: "";
  left: 0px;
  top: 0px;
  bottom: 0px;
  right: 0px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 80px;
  color: #f75757;
  opacity: 0;
  transition: all .35s ease;
}

.portflio-item:hover .overlay-item {
  opacity: 1;
}

.contact-form-wrap .form-group {
  padding-bottom: 15px;
  margin: 0px;
}

.contact-form-wrap .form-group .form-control {
  background: #f5f8f9;
  height: 48px;
  border: 1px solid #EEF2F6;
  box-shadow: none;
  width: 100%;
}

.contact-form-wrap .form-group-2 {
  margin-bottom: 13px;
}

.contact-form-wrap .form-group-2 textarea {
  background: #f5f8f9;
  height: 135px;
  border: 1px solid #EEF2F6;
  box-shadow: none;
  width: 100%;
}

.address-block li {
  margin-bottom: 10px;
}

.address-block li i {
  font-size: 20px;
  width: 20px;
}

.social-icons li {
  margin: 0 6px;
}

.social-icons i {
  margin-right: 15px;
  font-size: 25px;
}

.google-map {
  position: relative;
}

.google-map #map {
  width: 100%;
  height: 450px;
}

/*=================================================================
  Latest Posts
==================================================================*/
.blog-item-content h3 {
  line-height: 36px;
}

.blog-item-content h3 a {
  transition: all .4s ease 0s;
}

.blog-item-content h3 a:hover {
  color: #f75757 !important;
}

.lh-36 {
  line-height: 36px;
}

.tags a {
  background: #f5f8f9;
  display: inline-block;
  padding: 8px 23px;
  border-radius: 38px;
  margin-bottom: 10px;
  border: 1px solid #eee;
  font-size: 14px;
  text-transform: capitalize;
}

.pagination .nav-links a,
.pagination .nav-links span.current {
  font-size: 20px;
  font-weight: 500;
  color: #c9c9c9;
  margin: 0 10px;
  text-transform: uppercase;
  letter-spacing: 1.2px;
}

.pagination .nav-links span.current,
.pagination .nav-links a.next,
.pagination .nav-links a.prev {
  color: #242424;
}

h3.quote {
  font-size: 24px;
  line-height: 40px;
  font-weight: normal;
  padding: 0px 25px 0px 85px;
  margin: 65px 0 65px 0 !important;
  position: relative;
}

@media (max-width: 768px) {
  h3.quote {
    padding: 0;
    padding-left: 20px;
  }
}

h3.quote::before {
  content: '';
  width: 55px;
  height: 2px;
  background: #f75757;
  position: absolute;
  top: 25px;
  left: 0;
}

@media (max-width: 768px) {
  h3.quote::before {
    top: 5px;
    width: 2px;
    height: 35px;
  }
}

.nav-posts-title {
  line-height: 25px;
  font-size: 18px;
}

.latest-blog {
  position: relative;
  padding-bottom: 150px;
}

.mt-70 {
  margin-top: -70px;
}

.border-1 {
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.blog-item {
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

/*=================================================================
  Single Blog Page
==================================================================*/
.post.post-single {
  border: none;
}

.post.post-single .post-thumb {
  margin-top: 30px;
}

.post-sub-heading {
  border-bottom: 1px solid #dedede;
  padding-bottom: 20px;
  letter-spacing: 2px;
  text-transform: uppercase;
  font-size: 16px;
  margin-bottom: 20px;
}

.post-social-share {
  margin-bottom: 50px;
}

.post-comments {
  margin: 30px 0;
}

.post-comments .media {
  margin-top: 20px;
}

.post-comments .media > .pull-left {
  padding-right: 20px;
}

.post-comments .comment-author {
  margin-top: 0;
  margin-bottom: 0px;
  font-weight: 500;
}

.post-comments .comment-author a {
  color: #f75757;
  font-size: 14px;
  text-transform: uppercase;
}

.post-comments time {
  margin: 0 0 5px;
  display: inline-block;
  color: #808080;
  font-size: 12px;
}

.post-comments .comment-button {
  color: #f75757;
  display: inline-block;
  margin-left: 5px;
  font-size: 12px;
}

.post-comments .comment-button i {
  margin-right: 5px;
  display: inline-block;
}

.post-comments .comment-button:hover {
  color: #f75757;
}

.post-excerpt {
  margin-bottom: 60px;
}

.post-excerpt h3 a {
  color: #000;
}

.post-excerpt p {
  margin: 0 0 30px;
}

.post-excerpt blockquote.quote-post {
  margin: 20px 0;
}

.post-excerpt blockquote.quote-post p {
  line-height: 30px;
  font-size: 20px;
  color: #f75757;
}

.single-blog {
  background-color: #fff;
  margin-bottom: 50px;
  padding: 20px;
}

.blog-subtitle {
  font-size: 15px;
  padding-bottom: 10px;
  border-bottom: 1px solid #dedede;
  margin-bottom: 25px;
  text-transform: uppercase;
}

.next-prev {
  border-bottom: 1px solid #dedede;
  border-top: 1px solid #dedede;
  margin: 20px 0;
  padding: 25px 0;
}

.next-prev a {
  color: #000;
}

.next-prev a:hover {
  color: #f75757;
}

.next-prev .prev-post i {
  margin-right: 10px;
}

.next-prev .next-post i {
  margin-left: 10px;
}

.social-profile ul li {
  margin: 0 10px 0 0;
  display: inline-block;
}

.social-profile ul li a {
  color: #4e595f;
  display: block;
  font-size: 16px;
}

.social-profile ul li a i:hover {
  color: #f75757;
}

.comments-section {
  margin-top: 35px;
}

.author-about {
  margin-top: 40px;
}

.post-author {
  margin-right: 20px;
}

.post-author > img {
  border: 1px solid #dedede;
  max-width: 120px;
  padding: 5px;
  width: 100%;
}

.comment-list ul {
  margin-top: 20px;
}

.comment-list ul li {
  margin-bottom: 20px;
}

.comment-wrap {
  border: 1px solid #dedede;
  border-radius: 1px;
  margin-left: 20px;
  padding: 10px;
  position: relative;
}

.comment-wrap .author-avatar {
  margin-right: 10px;
}

.comment-wrap .media .media-heading {
  font-size: 14px;
  margin-bottom: 8px;
}

.comment-wrap .media .media-heading a {
  color: #f75757;
  font-size: 13px;
}

.comment-wrap .media .comment-meta {
  font-size: 12px;
  color: #888;
}

.comment-wrap .media p {
  margin-top: 15px;
}

.comment-reply-form {
  margin-top: 80px;
}

.comment-reply-form input, .comment-reply-form textarea {
  height: 35px;
  border-radius: 0;
  box-shadow: none;
}

.comment-reply-form input:focus, .comment-reply-form textarea:focus {
  box-shadow: none;
  border: 1px solid #f75757;
}

.comment-reply-form textarea, .comment-reply-form .btn-main, .comment-reply-form .btn-transparent, .comment-reply-form .btn-small {
  height: auto;
}

.widget {
  margin-bottom: 30px;
  padding-bottom: 35px;
}

.widget .widget-title {
  margin-bottom: 15px;
  padding-bottom: 10px;
  font-size: 16px;
  color: #333;
  font-weight: 500;
  border-bottom: 1px solid #dedede;
}

.widget.widget-latest-post .media .media-object {
  width: 100px;
  height: auto;
}

.widget.widget-latest-post .media .media-heading a {
  color: #242424;
  font-size: 16px;
}

.widget.widget-latest-post .media p {
  font-size: 12px;
  color: #808080;
}

.widget.widget-category ul li {
  margin-bottom: 10px;
}

.widget.widget-category ul li a {
  color: #837f7e;
  transition: all 0.3s ease;
}

.widget.widget-category ul li a:before {
  padding-right: 10px;
}

.widget.widget-category ul li a:hover {
  color: #f75757;
  padding-left: 5px;
}

.widget.widget-tag ul li {
  margin-bottom: 10px;
  display: inline-block;
  margin-right: 5px;
}

.widget.widget-tag ul li a {
  color: #837f7e;
  display: inline-block;
  padding: 8px 15px;
  border: 1px solid #dedede;
  border-radius: 30px;
  font-size: 14px;
  transition: all 0.3s ease;
}

.widget.widget-tag ul li a:hover {
  color: #fff;
  background: #f75757;
  border: 1px solid #f75757;
}

.footer {
  padding-bottom: 10px;
}

.footer .copyright a {
  font-weight: 600;
}

.lh-35 {
  line-height: 35px;
}

.logo {
  color: #242424;
  font-weight: 600;
  letter-spacing: 1px;
}

.logo span {
  color: #f75757;
}

.sub-form {
  position: relative;
}

.sub-form .form-control {
  border: 1px solid rgba(0, 0, 0, 0.06);
  background: #f5f8f9;
}

.footer-btm {
  border-top: 1px solid rgba(0, 0, 0, 0.06);
}

.footer-socials li a {
  margin-left: 15px;
}

.scroll-to-top {
  position: fixed;
  bottom: 30px;
  right: 30px;
  z-index: 999;
  height: 40px;
  width: 40px;
  background: #f75757;
  border-radius: 50%;
  text-align: center;
  line-height: 43px;
  color: white;
  cursor: pointer;
  transition: 0.3s;
  display: none;
}

@media (max-width: 480px) {
  .scroll-to-top {
    bottom: 15px;
    right: 15px;
  }
}

.scroll-to-top:hover {
  background-color: #333;
}

/*=== MEDIA QUERY ===*/
@media (max-width: 992px) {
  .slider .block h1 {
    font-size: 56px;
    line-height: 70px;
  }
  .bg-about {
    display: none;
  }
  section.about {
    border: 1px solid #dee2e6;
    border-left: 0;
    border-right: 0;
  }
  .footer-socials {
    margin-top: 20px;
  }
  .footer-socials li a {
    margin-left: 0px;
  }
}

@media (max-width: 768px) {
  .navbar-toggler {
    color: #fff;
  }
  .bg-about {
    display: none;
  }
  .slider .block h1 {
    font-size: 48px;
    line-height: 62px;
  }
  .blog-item-meta span {
    margin: 6px 0px;
  }
  .widget {
    margin-bottom: 30px;
    padding-bottom: 0px;
  }
}

@media (max-width: 480px) {
  .header-top .header-top-info a {
    margin-left: 10px;
    margin-right: 10px;
  }
  .navbar-toggler {
    color: #fff;
  }
  .slider .block h1 {
    font-size: 38px;
    line-height: 50px;
  }
  .content-title {
    font-size: 28px;
    line-height: 46px;
  }
  .p-5 {
    padding: 2rem !important;
  }
  h2, .h2 {
    font-size: 1.3rem;
    font-weight: 600;
    line-height: 36px;
  }
  .testimonial-item .testimonial-item-content {
    padding-left: 0px;
    padding-top: 30px;
  }
  .widget {
    margin-bottom: 30px;
    padding-bottom: 0px;
  }
}

@media (max-width: 400px) {
  .header-top .header-top-info a {
    display: block;
  }
  .navbar-toggler {
    color: #fff;
  }
  .content-title {
    font-size: 28px;
    line-height: 46px;
  }
  .bg-about {
    display: none;
  }
  .p-5 {
    padding: 2rem !important;
  }
  h2, .h2 {
    font-size: 1.3rem;
    font-weight: 600;
    line-height: 36px;
  }
  .testimonial-item .testimonial-item-content {
    padding-left: 0px;
    padding-top: 30px;
  }
  .text-lg {
    font-size: 3rem;
  }
  .widget {
    margin-bottom: 30px;
    padding-bottom: 0px;
  }
}

/*# sourceMappingURL=style.css.map */