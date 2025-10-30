<?php
require_once 'config.php';

// Determine current page for menu highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$fragment = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_FRAGMENT) : '';

// Define active class logic for main menu and subpages
$is_home_active = ($current_page === 'index') ? 'class="active"' : '';
$is_about_active = ($current_page === 'about') ? 'class="active"' : '';
$is_services_active = ($current_page === 'services' || in_array($fragment, [
    'massage-relaxation', 'healing-therapeutic-massage', 'corporate-massage', 'hydro-therapy-sauna',
    'body-skin-care', 'body-exfoliating', 'natural-body-contouring', 'facial-therapy-skin-tag-removal',
    'foot-nail-care', 'medical-feet-care', 'beauty-therapy',
    'hair-services', 'hair-dressing-braids-locks', 'weave-on-hair-installation', 'additional-offerings'
])) ? 'class="active"' : '';
$is_training_active = ($current_page === 'training-school') ? 'class="active"' : '';
$is_shop_active = ($current_page === 'shop') ? 'class="active"' : '';
$is_connect_active = ($current_page === 'connect') ? 'class="active"' : '';

// Contact Data
$query = "SELECT `logo`, `address`, `working_hours`, facebook, x, linkedin, instagram FROM ws_contact WHERE id = 1";
$result = $mysqli->query($query);
$contact_data = $result && $result->num_rows > 0 ? $result->fetch_assoc() : [
    'logo' => 'images/logo.png',
    'address' => '24 Tech Roqad st Ny 10023',
    'working_hours' => 'Mon-Sat: 9am to 6pm',
    'facebook' => '',
    'x' => '',
    'linkedin' => '',
    'instagram' => ''
];
// Ensure social media and working hours fields are not NULL
$contact_data['facebook'] = $contact_data['facebook'] ?? '';
$contact_data['x'] = $contact_data['x'] ?? '';
$contact_data['linkedin'] = $contact_data['linkedin'] ?? '';
$contact_data['instagram'] = $contact_data['instagram'] ?? '';
$contact_data['working_hours'] = $contact_data['working_hours'] ?? 'Mon-Sat: 9am to 6pm';
$logo_path = $contact_data['logo'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="Golden View Therapeutic Clinique and Spa, Beauty, Spa, Wellness, Massage, Skin Care, Hair Services">
    <meta name="description" content="Golden View Therapeutic Clinique and Spa - Your destination for relaxation and rejuvenation. Explore our services and shop for premium beauty products.">
    <meta name="author" content="https://www.sebson.org/">
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
                                                <a class="ttm-btn ttm-btn-size-md ttm-btn-style-fill ttm-btn-color-skincolor" href="appointment">Get An Appointment!</a>
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
                                                <a class="home-link" href="/" title="Golden View Therapeutic Clinique and Spa" rel="home">
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
                                                        <a href="/">Home</a>
                                                    </li>
                                                    <li <?php echo $is_about_active; ?>>
                                                        <a href="/about">About Us</a>
                                                    </li>
                                                    <li <?php echo $is_services_active; ?>>
                                                        <a href="/services">Services</a>
                                                        <ul>
                                                            <li><a href="/services#massage-relaxation">Massage & Relaxation</a>
                                                                <ul>
                                                                    <li><a href="/services#healing-therapeutic-massage">Healing Therapeutic Massage</a></li>
                                                                    <li><a href="/services#corporate-massage">Corporate Massage</a></li>
                                                                    <li><a href="/services#hydro-therapy-sauna">Hydro Therapy / Sauna</a></li>
                                                                </ul>
                                                            </li>
                                                            <li><a href="/services#body-skin-care">Body & Skin Care</a>
                                                                <ul>
                                                                    <li><a href="/services#body-exfoliating">Body Exfoliating</a></li>
                                                                    <li><a href="/services#natural-body-contouring">Natural Body Contouring</a></li>
                                                                    <li><a href="/services#facial-therapy-skin-tag-removal">Facial Therapy / Skin Tag Removal</a></li>
                                                                </ul>
                                                            </li>
                                                            <li><a href="/services#foot-nail-care">Foot & Nail Care</a>
                                                                <ul>
                                                                    <li><a href="/services#medical-feet-care">Medical Feet Care</a></li>
                                                                    <li><a href="/services#beauty-therapy">Beauty Therapy</a></li>
                                                                </ul>
                                                            </li>
                                                            <li><a href="/services#hair-services">Hair Services</a>
                                                                <ul>
                                                                    <li><a href="/services#hair-dressing-braids-locks">Hair Dressing / Braids & Locks</a></li>
                                                                    <li><a href="/services#weave-on-hair-installation">Weave-On & Hair Installation</a></li>
                                                                </ul>
                                                            </li>
                                                            <li><a href="/services#additional-offerings">Additional Offerings</a></li>
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