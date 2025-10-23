<?php
// Determine current page for menu highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Define active class logic for main menu and subpages
$is_home_active = ($current_page === 'index') ? 'class="active"' : '';
$is_about_active = ($current_page === 'about') ? 'class="active"' : '';
$is_services_active = in_array($current_page, [
    'services', 'massage-relaxation', 'healing-therapeutic-massage', 'corporate-massage', 'hydro-therapy-sauna',
    'body-skin-care', 'body-exfoliating', 'natural-body-contouring', 'facial-therapy-skin-tag-removal',
    'foot-nail-care', 'medical-feet-care', 'beauty-therapy',
    'hair-services', 'hair-dressing-braids-locks', 'weave-on-hair-installation', 'additional-offerings'
]) ? 'class="active"' : '';
$is_training_active = ($current_page === 'training-school') ? 'class="active"' : '';
$is_shop_active = ($current_page === 'shop') ? 'class="active"' : '';
$is_connect_active = ($current_page === 'connect') ? 'class="active"' : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="HTML5 Template, Sylin - Beauty salon and Spa HTML Template, html template, wordpress theme, Fyna - Beauty salon and Spa WordPress Theme, Sylin - Beauty Salon and Spa HTML Template, unlimited colors available, ui/ux, ui/ux design, best html template, html template, html, woocommerce, shopify, prestashop, eCommerce, react js, react template, JavaScript, best CSS theme, css3, elementor theme, latest premium themes 2023, latest premium templates 2024, Preyan Technosys Pvt.Ltd, cymol themes, themetech mount, Web 3.0, multi-theme, website theme and template, woocommerce, bootstrap template, web templates, responsive theme, beauty parlor, beauty salon, beauty shop, cosmetic store, spa and salon, hospitality, spa salon, haircut salon, barber shop, beauty salon, nails art, hairstyle, hair stylist, makeup artist, hair dresser, beauty tips, beauty care">
    <meta name="description" content="Sylin - Beauty salon and Spa HTML Template">
    <meta name="author" content="https://www.themetechmount.com/">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Golden View Therapeutic Clinique & SPA</title>

    <!-- favicon icon -->
    <link rel="shortcut icon" href="<?php echo htmlspecialchars(URLROOT . '/cms/' . $logo_path); ?>">

    <!-- bootstrap -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <!-- animate -->
    <link rel="stylesheet" type="text/css" href="css/animate.css">
    <!-- owl-carousel -->
    <link rel="stylesheet" type="text/css" href="css/owl.carousel.css">
    <!-- fontawesome -->
    <link rel="stylesheet" type="text/css" href="css/font-awesome.css">
    <!-- themify -->
    <link rel="stylesheet" type="text/css" href="css/themify-icons.css">
    <!-- flaticon -->
    <link rel="stylesheet" type="text/css" href="css/flaticon.css">
    <!-- REVOLUTION LAYERS STYLES -->
    <link rel="stylesheet" type="text/css" href="revolution/css/rs6.css">
    <!-- prettyphoto -->
    <link rel="stylesheet" type="text/css" href="css/prettyPhoto.css">
    <!-- shortcodes -->
    <link rel="stylesheet" type="text/css" href="css/shortcodes.css">
    <!-- main -->
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!-- responsive -->
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
</head>
<body>
    <!--page start-->
    <div class="page">
        <!-- preloader start -->
        <div id="preloader">
            <div id="status">&nbsp;</div>
        </div>
        <!-- preloader end -->
        <!--header start-->
        <header id="masthead" class="header ttm-header-style-01">
            <!-- ttm-header-wrap -->
            <div class="ttm-header-wrap">
                <!-- ttm-stickable-header-w -->
                <div id="ttm-stickable-header-w" class="ttm-stickable-header-w clearfix">
                    <!-- ttm-topbar-wrapper -->
                    <div class="ttm-topbar-wrapper ttm-bgcolor-darkgrey ttm-textcolor-white clearfix">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <div class="ttm-topbar-content">
                                        <ul class="top-contact text-start">
                                            <li><i class="fa fa-map-marker ttm-textcolor-skincolor"></i><?php echo htmlspecialchars($contact_data['address']); ?></li>
                                            <li><i class="fa fa-clock-o ttm-textcolor-skincolor"></i><?php echo htmlspecialchars($contact_data['working_hours']); ?></li>
                                        </ul>
                                        <div class="topbar-right text-end">
                                            <div class="ttm-social-links-wrapper list-inline">
                                                <ul class="social-icons">
                                                    <?php if (!empty($contact_data['facebook'])): ?>
                                                        <li><a href="<?php echo htmlspecialchars($contact_data['facebook']); ?>" class="tooltip-bottom" data-tooltip="Facebook" target="_blank"><i class="fa fa-facebook"></i></a></li>
                                                    <?php endif; ?>
                                                    <?php if (!empty($contact_data['x'])): ?>
                                                        <li><a href="<?php echo htmlspecialchars($contact_data['x']); ?>" class="tooltip-bottom" data-tooltip="X" target="_blank"><i class="fa fa-twitter"></i></a></li>
                                                    <?php endif; ?>
                                                    <?php if (!empty($contact_data['linkedin'])): ?>
                                                        <li><a href="<?php echo htmlspecialchars($contact_data['linkedin']); ?>" class="tooltip-bottom" data-tooltip="LinkedIn" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                                                    <?php endif; ?>
                                                    <?php if (!empty($contact_data['instagram'])): ?>
                                                        <li><a href="<?php echo htmlspecialchars($contact_data['instagram']); ?>" class="tooltip-bottom" data-tooltip="Instagram" target="_blank"><i class="fa fa-instagram"></i></a></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                            <div class="header-btn">
                                                <a class="ttm-btn ttm-btn-size-md ttm-btn-style-fill ttm-btn-color-skincolor" href="#">Get An Appointment!</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- ttm-topbar-wrapper end -->
                    <div id="site-header-menu" class="site-header-menu">
                        <div class="site-header-menu-inner ttm-stickable-header">
                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <!--site-navigation -->
                                        <div id="site-navigation" class="site-navigation d-flex flex-row">
                                            <div class="site-branding me-auto">
                                                <!-- site-branding -->
                                                <a class="home-link" href="/home" title="Sylin Spa" rel="home">
                                                    <img id="logo-img" class="img-center lazyload" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $logo_path); ?>" alt="logo">
                                                </a>
                                                <!-- site-branding end -->                   
                                            </div>
                                            <div class="ttm-menu-toggle">
                                                <input type="checkbox" id="menu-toggle-form" />
                                                <label for="menu-toggle-form" class="ttm-menu-toggle-block">
                                                    <span class="toggle-block toggle-blocks-1"></span>
                                                    <span class="toggle-block toggle-blocks-2"></span>
                                                    <span class="toggle-block toggle-blocks-3"></span>
                                                </label>
                                            </div>
                                            <nav id="menu" class="menu">
                                                <ul class="dropdown">
                                                    <li <?php echo $is_home_active; ?>>
                                                        <a href="/home">Home</a>
                                                    </li>
                                                    <li <?php echo $is_about_active; ?>>
                                                        <a href="/about">About Us</a>
                                                    </li>
                                                    <li <?php echo $is_services_active; ?>>
                                                        <a href="/services">Services</a>
                                                        <ul>
                                                            <li><a href="/services/massage-relaxation">Massage & Relaxation</a>
                                                                <ul>
                                                                    <li><a href="/services/massage-relaxation/healing-therapeutic-massage">Healing Therapeutic Massage</a></li>
                                                                    <li><a href="/services/massage-relaxation/corporate-massage">Corporate Massage</a></li>
                                                                    <li><a href="/services/massage-relaxation/hydro-therapy-sauna">Hydro Therapy / Sauna</a></li>
                                                                </ul>
                                                            </li>
                                                            <li><a href="/services/body-skin-care">Body & Skin Care</a>
                                                                <ul>
                                                                    <li><a href="/services/body-skin-care/body-exfoliating">Body Exfoliating</a></li>
                                                                    <li><a href="/services/body-skin-care/natural-body-contouring">Natural Body Contouring</a></li>
                                                                    <li><a href="/services/body-skin-care/facial-therapy-skin-tag-removal">Facial Therapy / Skin Tag Removal</a></li>
                                                                </ul>
                                                            </li>
                                                            <li><a href="/services/foot-nail-care">Foot & Nail Care</a>
                                                                <ul>
                                                                    <li><a href="/services/foot-nail-care/medical-feet-care">Medical Feet Care</a></li>
                                                                    <li><a href="/services/foot-nail-care/beauty-therapy">Beauty Therapy</a></li>
                                                                </ul>
                                                            </li>
                                                            <li><a href="/services/hair-services">Hair Services</a>
                                                                <ul>
                                                                    <li><a href="/services/hair-services/hair-dressing-braids-locks">Hair Dressing / Braids & Locks</a></li>
                                                                    <li><a href="/services/hair-services/weave-on-hair-installation">Weave-On & Hair Installation</a></li>
                                                                </ul>
                                                            </li>
                                                            <li><a href="/services/additional-offerings">Additional Offerings</a></li>
                                                        </ul>
                                                    </li>
                                                    <li <?php echo $is_training_active; ?>>
                                                        <a href="/training-school">Training School</a>
                                                    </li>
                                                    <li <?php echo $is_shop_active; ?>>
                                                        <a href="/shop">Shop</a>
                                                    </li>
                                                    <li <?php echo $is_connect_active; ?>>
                                                        <a href="/connect">Connect</a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div><!-- site-navigation end-->
                                    </div>
                                </div>                              
                            </div>
                        </div>
                    </div>
                </div><!-- ttm-stickable-header-w end-->
            </div><!--ttm-header-wrap end -->
        </header>
        <!--header end-->