<?php

require_once 'config.php';
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



$query = "SELECT * FROM ws_slider WHERE id IN (1, 2) ORDER BY id";
$result = $mysqli->query($query);
$slides = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $slides[$row['id']] = $row;
    }
}
if (empty($slides)) {
    $slides = [
        1 => [
            'slide_key' => 'rs-3',
            'background_media' => 'images/slides/slider-mainbg-001.jpg',
            'subtitle' => 'Best Place for',
            'heading1' => 'THE BEST TIME',
            'heading2' => 'TO RELAX WITH SYLIN',
            'description' => 'Professional Beauty Center Since 1919.',
            'button_text' => 'Get An Appointment!',
            'button_url' => '#'
        ],
        2 => [
            'slide_key' => 'rs-4',
            'background_media' => 'images/slides/istockphoto-1047656636-640_adpp_is.mp4',
            'subtitle' => 'Best Place for',
            'heading1' => 'THE BEST TIME',
            'heading2' => 'TO RELAX WITH SYLIN',
            'description' => 'Professional Beauty Center Since 1919.',
            'button_text' => 'Watch Video',
            'button_url' => 'https://youtu.be/7e90gBu4pas'
        ]
    ];
}



$query = "SELECT * FROM ws_about WHERE id = 1";
$result = $mysqli->query($query);
$about = $result && $result->num_rows > 0 ? $result->fetch_assoc() : [
    'image' => 'images/single-img-one.jpg',
    'subtitle' => 'Wellness',
    'title' => 'WELCOME TO HOME OF RELAXE & RESPITE.',
    'description' => 'There’s nothing more luxurious and relaxing than a trip to the spa & Salon. We offer a wide variety of body spa therapies to help you heal your body naturally. Get relaxed from stressed & hectic schedule.',
    'paragraph' => 'Everybody is looking for places where to relax and get more energy. In our wellness center silence, energy, beauty and vitality meet. The treatments we offer will refresh both your body and soul. We’ll be glad to welcome you and recommend our facilities and services.',
    'box1_title' => 'Massage',
    'box1_icon' => 'flaticon-spa',
    'box2_title' => 'Therapies',
    'box2_icon' => 'flaticon-wellness',
    'box3_title' => 'Relaxation',
    'box3_icon' => 'flaticon-hammam',
    'box4_title' => 'Facial',
    'box4_icon' => 'flaticon-person-silhouette-in-sauna'
];



$query = "SELECT * FROM ws_services WHERE id IN (1, 2, 3, 4, 5) ORDER BY id";
$result = $mysqli->query($query);
$section = [];
$boxes = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $section = $row;
        } else {
            $boxes[$row['box_order']] = $row;
        }
    }
}
if (empty($section)) {
    $section = [
        'subtitle' => 'Welcome',
        'title' => 'EXPLORE Our Services',
        'description' => 'Being the pampering connoisseurs that we are, we have discovered some wonderful spa services…To relax mind & body!'
    ];
}
if (empty($boxes)) {
    $boxes = [
        1 => [
            'image' => 'images/services/01.jpg',
            'title' => 'Face Massage',
            'description' => 'To reverse the ageing effect from the skin, try our face hydration treatment to get a youthful glow',
            'icon' => 'flaticon-herbal',
            'link' => 'services-details.html',
            'box_order' => 1
        ],
        2 => [
            'image' => 'images/services/02.jpg',
            'title' => 'Back Massage',
            'description' => 'To reverse the ageing effect from the skin, try our face hydration treatment to get a youthful glow',
            'icon' => 'flaticon-spa-1',
            'link' => 'services-details.html',
            'box_order' => 2
        ],
        3 => [
            'image' => 'images/services/03.jpg',
            'title' => 'Hair Treatment',
            'description' => 'We offer the professional hair care for all hair types discover the best hair treatments, for healthy.',
            'icon' => 'flaticon-spa',
            'link' => 'services-details.html',
            'box_order' => 3
        ],
        4 => [
            'image' => 'images/services/04.jpg',
            'title' => 'Skin Care',
            'description' => 'you’ll get therapies like panchakarma treatment which can help you for living a healthy life.',
            'icon' => 'flaticon-cupping',
            'link' => 'services-details.html',
            'box_order' => 4
        ]
    ];
}


$query = "SELECT * FROM ws_testimonials WHERE id IN (1, 2, 3, 4, 5, 6, 7, 8) ORDER BY id";
$result = $mysqli->query($query);
$testimonial_section = []; 
$testimonials = [];
$facts = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $testimonial_section = $row; 
        } elseif ($row['type'] === 'testimonial') {
            $testimonials[$row['item_order']] = $row;
        } else {
            $facts[$row['item_order']] = $row;
        }
    }
}
if (empty($testimonial_section)) {
    $testimonial_section = [ 
        'subtitle' => 'Testimonials',
        'title' => 'WHAT OUR CLIENTS SAYING'
    ];
}
if (empty($testimonials)) {
    $testimonials = [
        1 => [
            'quote' => 'I received a lymphatic massage, Aba was fantastic. The spa is clean, upscale decor and overall ambience are delightful. I will patronize Ecobel for a variety of services they offer.',
            'name' => 'Len Rosy Jacbos',
            'label' => 'Face make-up',
            'item_order' => 1
        ],
        2 => [
            'quote' => 'I received a lymphatic massage, Aba was fantastic. The spa is clean, upscale decor and overall ambience are delightful. I will patronize Ecobel for a variety of services they offer.',
            'name' => 'Len Rosy Jacbos',
            'label' => 'Face make-up',
            'item_order' => 2
        ],
        3 => [
            'quote' => 'I received a lymphatic massage, Aba was fantastic. The spa is clean, upscale decor and overall ambience are delightful. I will patronize Ecobel for a variety of services they offer.',
            'name' => 'Scorlett Johanson',
            'label' => 'Face make-up',
            'item_order' => 3
        ]
    ];
}
if (empty($facts)) {
    $facts = [
        1 => [
            'title' => 'Cosmetics',
            'icon' => 'flaticon-spa',
            'number' => 5684,
            'item_order' => 1
        ],
        2 => [
            'title' => 'Subscriber',
            'icon' => 'flaticon-wellness',
            'number' => 7458,
            'item_order' => 2
        ],
        3 => [
            'title' => 'Total Branches',
            'icon' => 'flaticon-hair-1',
            'number' => 7855,
            'item_order' => 3
        ],
        4 => [
            'title' => 'Campigns Done',
            'icon' => 'flaticon-herbal',
            'number' => 1458,
            'item_order' => 4
        ]
    ];
}


$query = "SELECT * FROM ws_gallery WHERE id IN (1, 2, 3, 4, 5) ORDER BY id";
$result = $mysqli->query($query);
$gallery_section = [];
$images = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $gallery_section = $row;
        } else {
            $images[$row['image_order']] = $row;
        }
    }
}
if (empty($gallery_section)) {
    $gallery_section = [
        'subtitle' => 'Gallery',
        'title' => 'AN INCREDIBLE SPA EXPERIENCE',
        'description' => 'Stunning treatment rooms are endowed with lush open air gardens soak bathtub and cushioned daybed built for two.'
    ];
}
if (empty($images)) {
    $images = [
        1 => [
            'image' => 'images/gallery/01.jpg',
            'image_order' => 1
        ],
        2 => [
            'image' => 'images/gallery/02.jpg',
            'image_order' => 2
        ],
        3 => [
            'image' => 'images/gallery/03.jpg',
            'image_order' => 3
        ],
        4 => [
            'image' => 'images/gallery/04.jpg',
            'image_order' => 4
        ]
    ];
}



$query = "SELECT * FROM ws_processes WHERE id IN (1, 2, 3, 4) ORDER BY id";
$result = $mysqli->query($query);
$processes_section = [];
$processes = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $processes_section = $row;
        } else {
            $processes[$row['process_order']] = $row;
        }
    }
}
if (empty($processes_section)) {
    $processes_section = [
        'subtitle' => 'Welcome',
        'title' => 'LOOK WELNESS PROCESSES',
        'description' => 'Lorem Ipsum is simply dummy text of the printing and tdustry. Lorem Ipsum has been the industry’s standard dualley.'
    ];
}
if (empty($processes)) {
    $processes = [
        1 => [
            'icon' => 'flaticon-hammam',
            'title' => 'Get A Free Quotes',
            'description' => 'Get full details about Spa treatments & other amenities here!',
            'process_order' => 1
        ],
        2 => [
            'icon' => 'flaticon-massage-spa-body-treatment',
            'title' => 'Get A Free Quotes',
            'description' => 'Book your appointment at your suitable schedule & get notified!',
            'process_order' => 2
        ],
        3 => [
            'icon' => 'flaticon-hair-cut',
            'title' => 'Get A Free Quotes',
            'description' => 'We always appreciate your valuable feedback over our service!',
            'process_order' => 3
        ]
    ];
}



$query = "SELECT * FROM ws_contact1 WHERE id IN (1, 2, 3, 4) ORDER BY id";
$result = $mysqli->query($query);
$contact1_section = [];
$timeslots = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $contact1_section = $row;
        } else {
            $timeslots[$row['slot_order']] = $row;
        }
    }
}
if (empty($contact1_section)) {
    $contact1_section = [
        'image' => 'images/bg-image/col-bgimage-4.jpg',
        'subtitle' => 'Contactus',
        'title' => 'GET A FREE QUOTES'
    ];
}
if (empty($timeslots)) {
    $timeslots = [
        1 => [
            'time_range' => '9:00 am – 11:00 am',
            'spaces_available' => '10 spaces available',
            'slot_order' => 1
        ],
        2 => [
            'time_range' => '11:00 am – 1:00 am',
            'spaces_available' => '10 spaces available',
            'slot_order' => 2
        ],
        3 => [
            'time_range' => '4:00 am – 6:00 am',
            'spaces_available' => '10 spaces available',
            'slot_order' => 3
        ]
    ];
}
?>

<style>
    /* Custom styles for the gallery section */
    .gallery-section .featured-imagebox-portfolio {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    .gallery-section .featured-imagebox-portfolio:hover {
        transform: scale(1.05);
    }
    .gallery-section .featured-imagebox-portfolio img {
        width: 100%;
        height: 250px; /* Fixed height for consistency */
        object-fit: cover; /* Ensures images maintain aspect ratio */
        display: block;
    }
    .gallery-section .featured-content-portfolio {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.3); /* Semi-transparent overlay */
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .gallery-section .featured-imagebox-portfolio:hover .featured-content-portfolio {
        opacity: 1;
    }
    .gallery-section .featured-content-portfolio a {
        font-size: 24px;
        color: #fff;
        background: #ff6f61; /* Matches ttm-textcolor-skincolor */
        padding: 10px;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .gallery-section .row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px; /* Consistent spacing between images */
    }
    .gallery-section .col-md-4 {
        flex: 1 1 calc(33.333% - 20px); /* Equal width with gap */
        max-width: calc(33.333% - 20px);
    }
    @media (max-width: 767px) {
        .gallery-section .col-md-4 {
            flex: 1 1 calc(50% - 20px); /* Two columns on small screens */
            max-width: calc(50% - 20px);
        }
    }
    @media (max-width: 575px) {
        .gallery-section .col-md-4 {
            flex: 1 1 100%; /* Single column on extra-small screens */
            max-width: 100%;
        }
    }
</style>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="HTML5 Template, Sylin - Beauty salon and Spa HTML Template,html template, wordpress theme, Fyna - Beauty salon and Spa WordPress Theme, Sylin - Beauty Salon and Spa HTML Template, unlimited colors available, ui/ux, ui/ux design, best html template, html template, html, woocommerce, shopify, prestashop, eCommerce, react js, react template, JavaScript, best CSS theme,css3, elementor theme, latest premium themes 2023, latest premium templates 2024, Preyan Technosys Pvt.Ltd, cymol themes, themetech mount, Web 3.0, multi-theme, website theme and template, woocommerce, bootstrap template, web templates, responsive theme, beauty parlor, beauty salon, beauty shop, cosmetic store, spa and salon, hospitality, spa salon, haircut salon, barber shop, beauty salon, nails art, hairstyle, hair stylist, makeup artist, hair dresser, beauty tips, beauty care">
    <meta name="description" content="Sylin - Beauty salon and Spa HTML Template">
    <meta name="author" content="https://www.themetechmount.com/">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Sylin Spa &#8211; Beauty Saloon and Spa</title>

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
                                                    <li class="active"><a href="/home">Home</a></li>
                                                    <li><a href="/about">About Us</a></li>
                                                    <li><a href="/services">Services</a>
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
                                                    <li><a href="/training-school">Training School</a></li>
                                                    <li><a href="/shop">Shop</a></li>
                                                    <li><a href="/connect">Connect</a></li>
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


        <!-- START homebanner -->
        <rs-module-wrap id="rev_slider_2_1_wrapper" data-source="gallery">
            <rs-module id="rev_slider_2_1" data-version="6.1.3" class="rev_slider_1_1_height">
                <!-- rs-slides -->
                <rs-slides>
                    <!-- Slide 1 -->
                    <rs-slide data-key="<?php echo htmlspecialchars($slides[1]['slide_key']); ?>" data-title="Slide" data-thumb="<?php echo htmlspecialchars(URLROOT . '/cms/' . $slides[1]['background_media']); ?>" data-anim="ei:d;eo:d;s:d;r:0;t:slidingoverlayhorizontal;sl:d;">
                        <img src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $slides[1]['background_media']); ?>" title="home-mainslider-bg001" width="1920" height="790" class="rev-slidebg" data-no-retina>
                        <rs-layer
                            id="slider-2-slide-3-layer-0"
                            data-type="text"
                            data-rsp_ch="on"
                            data-xy="x:l,l,c,c;xo:55px,55px,0,0;y:m;yo:80px,80px,-103px,-62px;"
                            data-text="w:normal;s:60,60,40,30;l:100,100,62,38;"
                            data-frame_0="sX:0.9;sY:0.9;"
                            data-frame_1="st:160;sp:500;sR:160;"
                            data-frame_999="o:0;st:w;sR:8340;"
                            style="z-index:9;font-family:Herr Von Muellerhoff;"
                        ><span class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($slides[1]['subtitle']); ?></span>
                        </rs-layer>
                        <rs-layer
                            id="slider-2-slide-3-layer-1"
                            data-type="shape"
                            data-rsp_ch="on"
                            data-xy="xo:55px,55px,-490px,-302px;y:m;yo:272px,272px,-60px,-37px;"
                            data-text="w:normal;s:20,20,12,7;l:0,0,15,9;"
                            data-dim="w:30px,30px,18px,11px;h:1px;"
                            data-vbility="t,t,f,f"
                            data-frame_0="sX:0.8;sY:0.8;"
                            data-frame_1="e:Power4.easeOut;st:50;sp:500;sR:50;"
                            data-frame_999="o:0;st:w;sR:8450;"
                            style="z-index:8;background-color:#ffffff;font-family:Roboto;"
                        ></rs-layer>
                        <rs-layer
                            id="slider-2-slide-3-layer-2"
                            data-type="text"
                            data-rsp_ch="on"
                            data-xy="x:l,l,c,c;xo:55px,55px,0,0;y:m;yo:212px,212px,5px,21px;"
                            data-text="w:normal;s:60,60,47,33;l:90,90,56,34;"
                            data-frame_0="sX:0.8;sY:0.8;"
                            data-frame_1="e:Power4.easeOut;st:750;sp:1000;sR:750;"
                            data-frame_999="o:0;st:w;sR:7250;"
                            style="z-index:11;font-family:Nimbus Roman No9 L;"
                        ><span class="slider-heding-title-font"><?php echo htmlspecialchars($slides[1]['heading2']); ?></span>
                        </rs-layer>
                        <rs-layer
                            id="slider-2-slide-3-layer-3"
                            data-type="text"
                            data-rsp_ch="on"
                            data-xy="x:l,l,c,c;xo:55px,55px,0,0;y:m;yo:146px,146px,-49px,-20px;"
                            data-text="w:normal;s:60,60,47,33;l:90,90,56,34;"
                            data-frame_0="sX:0.8;sY:0.8;"
                            data-frame_1="e:Power4.easeOut;st:510;sp:1000;sR:510;"
                            data-frame_999="o:0;st:w;sR:7490;"
                            style="z-index:10;font-family:Nimbus Roman No9 L;"
                        ><span class="slider-heding-title-font"><?php echo htmlspecialchars($slides[1]['heading1']); ?></span>
                        </rs-layer>
                        <rs-layer
                            id="slider-2-slide-3-layer-4"
                            data-type="text"
                            data-rsp_ch="on"
                            data-xy="x:l,l,c,c;xo:98px,98px,0,430px;y:m;yo:275px,275px,50px,32px;"
                            data-text="w:normal;s:18,18,16,9;l:28,28,17,10;"
                            data-vbility="t,t,t,f"
                            data-frame_0="sX:0.8;sY:0.8;"
                            data-frame_1="e:Power4.easeOut;st:1020;sp:1000;sR:1020;"
                            data-frame_999="o:0;st:w;sR:6980;"
                            style="z-index:12;font-family:Poppins;font-style:italic;"
                        ><?php echo htmlspecialchars($slides[1]['description']); ?>
                        </rs-layer>
                        <a
                            id="slider-2-slide-3-layer-7"
                            class="rs-layer ttm-btn ttm-btn-style-border ttm-btn-color-skincolor"
                            href="<?php echo htmlspecialchars($slides[1]['button_url']); ?>" target="_self" rel="nofollow"
                            data-type="text"
                            data-color="#fc84b4"
                            data-rsp_ch="on"
                            data-xy="x:r,r,c,c;xo:40px,40px,0,0;y:m;yo:163px,163px,105px,73px;"
                            data-text="w:normal;s:15,15,14,12;l:20,20,12,10;fw:500;"
                            data-padding="t:15,15,12,10;r:35,35,25,20;b:15,15,12,10;l:35,35,25,20;"
                            data-border="bos:solid;boc:#fc84b4;bow:1px,1px,1px,1px;"
                            data-frame_0="sX:0.8;sY:0.8;"
                            data-frame_1="e:Power4.easeOut;st:1100;sp:600;sR:1100;"
                            data-frame_999="o:0;st:w;sR:7300;"
                            data-frame_hover="c:#fff;bgc:#fc84b4;boc:#fc84b4;bos:solid;bow:1px,1px,1px,1px;"
                            style="z-index:13;font-family:Poppins;margin-right: 15px;"
                        ><?php echo htmlspecialchars($slides[1]['button_text']); ?>
                        </a>
                    </rs-slide>
                    <!-- Slide 2 -->
                    <rs-slide data-key="<?php echo htmlspecialchars($slides[2]['slide_key']); ?>" data-title="Slide" data-anim="ei:d;eo:d;s:d;r:0;t:fade;sl:d;">
                        <img src="images/slides/transparent.png" title="home-mainslider-bg002" width="1920" height="850" class="rev-slidebg" data-no-retina>
                        <?php if (pathinfo($slides[2]['background_media'], PATHINFO_EXTENSION) === 'mp4'): ?>
                            <rs-bgvideo
                                data-video="w:100%;h:100%;nse:false;l:true;ptimer:false;"
                                data-mp4="<?php echo htmlspecialchars(URLROOT . '/cms/' . $slides[2]['background_media']); ?>"
                            ></rs-bgvideo>
                        <?php endif; ?>
                        <rs-layer
                            id="slider-2-slide-4-layer-0"
                            data-type="text"
                            data-rsp_ch="on"
                            data-xy="x:l,l,c,c;xo:55px,55px,0,0;y:m;yo:90px,90px,-108px,-69px;"
                            data-text="w:normal;s:60,60,40,30;l:100,100,62,38;"
                            data-frame_0="sX:0.9;sY:0.9;"
                            data-frame_1="st:160;sp:500;sR:160;"
                            data-frame_999="o:0;st:w;sR:8340;"
                            style="z-index:10;font-family:Herr Von Muellerhoff;"
                        ><span class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($slides[2]['subtitle']); ?></span>
                        </rs-layer>
                        <rs-layer
                            id="slider-2-slide-4-layer-1"
                            data-type="shape"
                            data-rsp_ch="on"
                            data-xy="xo:55px,55px,-490px,-302px;y:m;yo:285px,285px,-60px,-37px;"
                            data-text="w:normal;s:20,20,12,7;l:0,0,15,9;"
                            data-dim="w:30px,30px,18px,11px;h:1px;"
                            data-vbility="t,t,f,f"
                            data-frame_0="sX:0.8;sY:0.8;"
                            data-frame_1="e:Power4.easeOut;st:1010;sp:500;sR:1010;"
                            data-frame_999="o:0;st:w;sR:7490;"
                            style="z-index:14;background-color:#ffffff;font-family:Roboto;"
                        ></rs-layer>
                        <rs-layer
                            id="slider-2-slide-4-layer-2"
                            data-type="text"
                            data-rsp_ch="on"
                            data-xy="x:l,l,c,c;xo:55px,55px,0,0;y:m;yo:222px,222px,0,14px;"
                            data-text="w:normal;s:60,60,47,33;l:90,90,56,34;"
                            data-frame_0="sX:0.8;sY:0.8;"
                            data-frame_1="e:Power4.easeOut;st:750;sp:1000;sR:750;"
                            data-frame_999="o:0;st:w;sR:7250;"
                            style="z-index:12;font-family:Nimbus Roman No9 L;"
                        ><span class="slider-heding-title-font"><?php echo htmlspecialchars($slides[2]['heading2']); ?></span>
                        </rs-layer>
                        <rs-layer
                            id="slider-2-slide-4-layer-3"
                            data-type="text"
                            data-rsp_ch="on"
                            data-xy="x:l,l,c,c;xo:55px,55px,0,0;y:m;yo:156px,156px,-54px,-27px;"
                            data-text="w:normal;s:60,60,47,33;l:90,90,56,34;"
                            data-frame_0="sX:0.8;sY:0.8;"
                            data-frame_1="e:Power4.easeOut;st:510;sp:1000;sR:510;"
                            data-frame_999="o:0;st:w;sR:7490;"
                            style="z-index:11;font-family:Nimbus Roman No9 L;"
                        ><span class="slider-heding-title-font"><?php echo htmlspecialchars($slides[2]['heading1']); ?></span>
                        </rs-layer>
                        <rs-layer
                            id="slider-2-slide-4-layer-4"
                            data-type="text"
                            data-rsp_ch="on"
                            data-xy="x:l,l,c,c;xo:98px,98px,0,430px;y:m;yo:285px,285px,45px,32px;"
                            data-text="w:normal;s:18,18,16,9;l:28,28,17,10;"
                            data-vbility="t,t,t,f"
                            data-frame_0="sX:0.8;sY:0.8;"
                            data-frame_1="e:Power4.easeOut;st:1020;sp:1000;sR:1020;"
                            data-frame_999="o:0;st:w;sR:6980;"
                            style="z-index:15;font-family:Poppins;font-style:italic;"
                        ><?php echo htmlspecialchars($slides[2]['description']); ?>
                        </rs-layer>
                        <rs-layer
                            id="slider-2-slide-4-layer-14"
                            data-type="shape"
                            data-rsp_ch="on"
                            data-xy="x:c;y:m;"
                            data-text="w:normal;s:20,20,12,7;l:0,0,15,9;"
                            data-dim="w:5000px,5000px,3137px,1935px;h:800px,800px,700px,400px;"
                            data-frame_999="o:0;st:w;sR:8700;"
                            style="z-index:5;background-color:rgba(0,11,40,0.74);font-family:Roboto;"
                        ></rs-layer>
                        <a
                            id="slider-2-slide-4-layer-16"
                            class="rs-layer ttm_prettyphoto"
                            href="<?php echo htmlspecialchars($slides[2]['button_url']); ?>" target="_self" rel="nofollow"
                            data-type="text"
                            data-rsp_ch="on"
                            data-xy="x:r,r,c,c;xo:30px,30px,0,0;y:m;yo:177px,177px,103px,67px;"
                            data-text="w:normal;s:18,18,15,12;l:50,50,45,35;a:center;"
                            data-dim="w:50px,50px,45px,35px;h:50px,50px,45px,35px;"
                            data-border="bor:50%,50%,50%,50%;"
                            data-frame_0="sX:0.8;sY:0.8;"
                            data-frame_1="e:Power4.easeOut;st:1330;sp:600;sR:1330;"
                            data-frame_999="o:0;st:w;sR:7070;"
                            data-frame_hover="bgc:#ff68a4;bor:50%,50%,50%,50%;"
                            style="z-index:7;background-color:#fc84b4;font-family:Roboto;margin-right: 15px;"
                        ><i class="fa fa-play"></i>
                        </a>
                        <a
                            id="slider-2-slide-4-layer-17"
                            class="rs-layer"
                            href="<?php echo htmlspecialchars($slides[2]['button_url']); ?>" target="_self" rel="nofollow"
                            data-type="text"
                            data-rsp_ch="on"
                            data-xy="x:r;xo:115px,115px,800px,493px;y:m;yo:179px,179px,74px,45px;"
                            data-text="w:normal;s:15,15,9,5;l:25,25,15,9;fw:500;"
                            data-vbility="t,t,f,f"
                            data-frame_0="sX:0.8;sY:0.8;"
                            data-frame_1="e:Power4.easeOut;st:1320;sp:600;sR:1320;"
                            data-frame_999="o:0;st:w;sR:7080;"
                            data-frame_hover="c:#fc84b4;"
                            style="z-index:13;font-family:Poppins;"
                        ><?php echo htmlspecialchars($slides[2]['button_text']); ?>
                        </a>
                    </rs-slide>
                </rs-slides>
                <rs-static-layers></rs-static-layers>
                <rs-progress class="rs-bottom" style="visibility: hidden !important;"></rs-progress>
            </rs-module>
        </rs-module-wrap>
        <!-- END homebanner -->

        <!--site-main start-->
        <div class="site-main">

            <!-- about us-section -->
            <section class="ttm-row aboutus-section clearfix">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 res-767-center">
                            <img src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $about['image']); ?>" class="img-fluid" alt="about-image">
                        </div>
                        <div class="col-md-6 res-767-pt-30">
                            <!-- featured-icon-box -->
                            <div class="spacing-1">
                                <!-- section title -->
                                <div class="section-title with-desc clearfix res-1199-mb-0">
                                    <div class="title-header">
                                        <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($about['subtitle']); ?></h5>
                                        <h2 class="title"><?php echo htmlspecialchars($about['title']); ?></h2>
                                    </div>
                                    <div class="title-desc"><?php echo htmlspecialchars($about['description']); ?></div>
                                </div><!-- section title end -->
                                <div class="row justify-content-space-between">
                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <div class="col-lg-3 col-md-6 col-sm-3 col-6 featured-icon-box style1 text-center res-575-w-50">
                                            <div class="featured-icon">
                                                <div class="ttm-icon ttm-icon_element-size-lg">
                                                    <i class="<?php echo htmlspecialchars($about["box{$i}_icon"]); ?> ttm-textcolor-skincolor"></i>
                                                </div>
                                            </div>
                                            <div class="featured-content">
                                                <div class="featured-title">
                                                    <h5><?php echo htmlspecialchars($about["box{$i}_title"]); ?></h5>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                <p class="mt-40 res-1199-mt-20 res-1199-mb-0"><?php echo htmlspecialchars($about['paragraph']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- about us-section end -->

            <!-- service-section-->
            <section class="ttm-row service-section bg-img1 clearfix">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-7 col-md-7 col-sm-9 m-auto">
                            <!-- section title -->
                            <div class="section-title with-desc text-center clearfix">
                                <div class="title-header">
                                    <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($section['subtitle'] ?? 'Welcome'); ?></h5>
                                    <h2 class="title"><?php echo htmlspecialchars($section['title'] ?? 'EXPLORE Our Services'); ?></h2>
                                </div>
                                <div class="title-desc"><?php echo htmlspecialchars($section['description'] ?? 'Being the pampering connoisseurs that we are, we have discovered some wonderful spa services…To relax mind & body!'); ?></div>
                            </div><!-- section title end -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="services-slide owl-carousel" data-item="4" data-nav="false" data-dots="false" data-auto="false">
                            <?php foreach ($boxes as $box): ?>
                                <div class="featured-imagebox featured-imagebox-services text-center style1">
                                    <div class="ttm-post-thumbnail featured-thumbnail">
                                        <img class="img-fluid" src="<?php echo htmlspecialchars(URLROOT . '/' . $box['image']); ?>" alt="service-image">
                                        <div class="featured-icon">
                                            <div class="ttm-icon ttm-icon_element-fill ttm-icon_element-color-white ttm-icon_element-size-md ttm-icon_element-style-rounded">
                                                <i class="<?php echo htmlspecialchars($box['icon']); ?>"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="featured-content">
                                        <div class="featured-title">
                                            <h5><a href="<?php echo htmlspecialchars($box['link']); ?>"><?php echo htmlspecialchars($box['title']); ?></a></h5>
                                        </div>
                                        <div class="featured-desc">
                                            <p><?php echo htmlspecialchars($box['description']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>
            <!-- service-section end-->

			<!-- testimonial-section -->
            <section class="ttm-row testimonial-section clearfix">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 text-end pt-80 res-767-pt-0">
                            <div class="testimonial-wrapper ttm-quotestyle-row">
                                <div class="spacing-2 col-bg-img-one ttm-col-bgimage-yes ttm-bg res-767-h-auto">
                                    <div class="ttm-col-wrapper-bg-layer ttm-bg-layer"></div>
                                    <div class="layer-content h-100">
                                        <!-- section title -->
                                        <div class="section-title with-desc clearfix">
                                            <div class="title-header">
                                                <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($testimonial_section['subtitle']); ?></h5>
                                                <h2 class="title"><?php echo htmlspecialchars($testimonial_section['title']); ?></h2>
                                            </div>
                                        </div>
                                        <div class="testimonial-slide style2 owl-carousel" data-item="1" data-nav="false" data-dots="false" data-auto="false">
                                            <?php foreach ($testimonials as $testimonial): ?>
                                                <div class="testimonials style2">
                                                    <div class="testimonial-content">
                                                        <p><?php echo htmlspecialchars($testimonial['quote']); ?></p>
                                                        <div class="testimonial-caption">
                                                            <h6><?php echo htmlspecialchars($testimonial['name']); ?></h6>
                                                            <label><?php echo htmlspecialchars($testimonial['label']); ?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="ttm-fid-wrapper">
                                <div class="spacing-3 res-767-mb-15 ttm-bgcolor-darkgrey ttm-bg ttm-col-bgimage-yes col-bg-img-two res-767-h-auto">
                                    <div class="ttm-col-wrapper-bg-layer ttm-bg-layer"></div>
                                    <div class="layer-content h-100">
                                        <div class="row">
                                            <?php foreach ($facts as $index => $fact): ?>
                                                <div class="col-md-6 col-sm-6 col-6 p-0 <?php echo $index <= 2 ? 'border-bottom' : ''; ?> <?php echo $index % 2 == 0 ? 'border-left' : ''; ?> text-center">
                                                    <div class="ttm-fid inside style1 <?php echo $index > 2 ? 'pb-0' : 'pt-10'; ?>">
                                                        <div class="ttm-fid-center">
                                                            <div class="ttm-fid-icon-wrapper ttm-textcolor-skincolor">
                                                                <i class="<?php echo htmlspecialchars($fact['icon']); ?>"></i>
                                                            </div>
                                                            <h4 class="ttm-fid-inner ttm-textcolor-white">
                                                                <span data-appear-animation="animateDigits"
                                                                    data-from="0"
                                                                    data-to="<?php echo htmlspecialchars($fact['number']); ?>"
                                                                    data-interval="100"
                                                                    data-before=""
                                                                    data-before-style="sup"
                                                                    data-after=""
                                                                    data-after-style="sub"
                                                                ><?php echo htmlspecialchars($fact['number']); ?></span>
                                                            </h4>
                                                        </div>
                                                        <div class="ttm-fid-contents">
                                                            <h3 class="ttm-fid-title"><?php echo htmlspecialchars($fact['title']); ?></h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- testimonial section end -->

            <!-- team-section -->
            <!-- team section end -->

            <!-- gallery-section-section -->
            <section class="ttm-row gallery-section ttm-bgcolor-grey clearfix">
                <div class="gallery-title-section ttm-bgcolor-skincolor">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-9 m-auto">
                                <!-- section title -->
                                <div class="section-title with-desc text-center clearfix">
                                    <div class="title-header">
                                        <h5><?php echo htmlspecialchars($gallery_section['subtitle']); ?></h5>
                                        <h2 class="title"><?php echo htmlspecialchars($gallery_section['title']); ?></h2>
                                    </div>
                                    <div class="title-desc ttm-textcolor-white pl-50 pr-50 res-767-p-0"><?php echo htmlspecialchars($gallery_section['description']); ?></div>
                                </div><!-- section title end -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container mt_70 res-991-mt_50">
                    <div class="row">
                        <?php foreach ($images as $index => $image): ?>
                            <div class="col-md-4">
                                <div class="featured-imagebox-portfolio">
                                    <img class="img-fluid" src="<?php echo htmlspecialchars(URLROOT . '/' . $image['image']); ?>" alt="gallery-image">
                                    <div class="featured-content-portfolio">
                                        <a class="ttm_prettyphoto ttm_image ttm-textcolor-skincolor ttm-bgcolor-white" title="" data-gal="" data-rel="prettyPhoto" href="<?php echo htmlspecialchars(URLROOT . '/' . $image['image']); ?>"><i class="fa fa-camera"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <!-- gallery-section end -->

            <!-- process-section  -->
            <section class="ttm-row ttm-bgcolor-grey process-section">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-7 col-md-7 col-sm-9 m-auto">
                            <!-- section title -->
                            <div class="section-title with-desc text-center clearfix">
                                <div class="title-header">
                                    <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($processes_section['subtitle']); ?></h5>
                                    <h2 class="title"><?php echo htmlspecialchars($processes_section['title']); ?></h2>
                                </div>
                                <div class="title-desc"><?php echo htmlspecialchars($processes_section['description']); ?></div>
                            </div><!-- section title end -->
                        </div>
                    </div>
                    <div class="row featured-icon-row-wrapper pb-50">
                        <?php foreach ($processes as $index => $process): ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="featured-icon-box icon-ver_align-top text-center style2">
                                    <div class="featured-icon">
                                        <div class="ttm-icon ttm-icon_element-fill ttm-icon_element-color-white ttm-icon_element-size-lg ttm-icon_element-style-rounded">
                                            <i class="flaticon <?php echo htmlspecialchars($process['icon']); ?>"></i>
                                            <div class="process-num"><span class="number"><?php echo sprintf('%02d', $index); ?></span></div>
                                        </div>
                                    </div>
                                    <div class="featured-content">
                                        <div class="featured-title">
                                            <h5><?php echo htmlspecialchars($process['title']); ?></h5>
                                        </div>
                                        <div class="featured-desc">
                                            <p><?php echo htmlspecialchars($process['description']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <!-- process-section  end-->
            <!-- contact1-section  -->
            <section class="ttm-row contact1-section mt_100 clearfix">
                <div class="container">
                    <div class="row g-0">
                        <div class="col-md-5 pr-0 res-767-pr-15 res-767-pb-15 text-center">
                            <div class="col-bg-img-four ttm-col-bgimage-yes ttm-bg res-767-h-auto">
                                <div class="ttm-col-wrapper-bg-layer ttm-bg-layer"></div>
                            </div>
                            <img src="<?php echo htmlspecialchars(URLROOT . '/' . $contact1_section['image']); ?>" class="ttm-equal-height-image img-fluid" alt="bg-image">
                        </div>
                        <div class="col-md-7 p-0">
                            <div class="spacing-5 ttm-bgcolor-darkgrey">
                                <!-- section title -->
                                <div class="section-title with-desc clearfix">
                                    <div class="title-header">
                                        <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($contact1_section['subtitle']); ?></h5>
                                        <h2 class="title ttm-textcolor-white"><?php echo htmlspecialchars($contact1_section['title']); ?></h2>
                                    </div>
                                </div><!-- section title end -->
                                <ul class="appointment-list ttm-textcolor-white p-0">
                                    <?php foreach ($timeslots as $timeslot): ?>
                                        <li>
                                            <div class="time-slot">
                                                <span class="timeslot-range"><i class="fa fa-clock-o ttm-textcolor-skincolor"></i>&nbsp;&nbsp;<?php echo htmlspecialchars($timeslot['time_range']); ?></span>
                                                <span><?php echo htmlspecialchars($timeslot['spaces_available']); ?></span>
                                            </div>
                                            <div class="appointment-time">
                                                <button class="ttm-btn ttm-btn-size-xs ttm-btn-style-fill ttm-btn-color-skincolor">Book Appointment</button>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- contact1-section  -->
            <!-- blog section -->
            <section class="ttm-row blog-section">
                <div class="container">
                    <div class="row">
                            <div class="col-lg-7 col-md-7 col-sm-9 m-auto">
                                <!-- section title -->
                                <div class="section-title with-desc text-center clearfix">
                                    <div class="title-header">
                                        <h5 class="ttm-textcolor-skincolor">Welcome</h5>
                                        <h2 class="title">DEFINITIVE SPA COLLECTION</h2>
                                    </div>
                                    <div class="title-desc">You can choose the various type of massage you want from kinds of massages our team has expertise in!</div>
                                </div><!-- section title end -->
                            </div>
                        </div>
                    <div class="row">
                        <div class="blog-slide owl-carousel" data-item="3" data-nav="false" data-dots="false" data-auto="false">
                            <div class="featured-imagebox featured-imagebox-post style3">
                                <div class="ttm-post-thumbnail featured-thumbnail"> 
                                    <img class="img-fluid" src="images/blog/01.jpg" alt="image"> 
                                </div>
                                <div class="featured-content featured-content-post">
                                    <div class="post-meta mb-10">
                                        <span class="ttm-meta-line"><i class="fa fa-calendar"></i>January 13, 2020</span>
                                        <span class="ttm-meta-line"><i class="fa fa-folder-open"></i>Bath &amp; Body</span>
                                    </div>
                                    <div class="post-title featured-title">
                                        <h5><a href="blog-single.html">Maintaining Health and Beauty Through Spas</a></h5>
                                    </div>
                                    <div class="post-footer"><!-- post-meta -->
                                        <span class="ttm-meta-line"><a class="ttm-textcolor-skincolor" href="blog-single.html">read more&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a></span>
                                        <span class="ttm-meta-line"><i class="fa fa-comment-o"></i>&nbsp;0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="featured-imagebox featured-imagebox-post style3">
                                <div class="ttm-post-thumbnail featured-thumbnail"> 
                                    <img class="img-fluid" src="images/blog/02.jpg" alt="image"> 
                                </div>
                                <div class="featured-content featured-content-post">
                                    <div class="post-meta mb-10">
                                        <span class="ttm-meta-line"><i class="fa fa-calendar"></i>January 13, 2020</span>
                                        <span class="ttm-meta-line"><i class="fa fa-folder-open"></i>Make-up</span>
                                    </div>
                                    <div class="post-title featured-title">
                                        <h5><a href="blog-single.html">A Relaxation of the Senses with Their Help</a></h5>
                                    </div>
                                    <div class="post-footer"><!-- post-meta -->
                                        <span class="ttm-meta-line"><a class="ttm-textcolor-skincolor" href="blog-single.html">read more&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a></span>
                                        <span class="ttm-meta-line"><i class="fa fa-comment-o"></i>&nbsp;0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="featured-imagebox featured-imagebox-post style3">
                                <div class="ttm-post-thumbnail featured-thumbnail"> 
                                    <img class="img-fluid" src="images/blog/03.jpg" alt="image"> 
                                </div>
                                <div class="featured-content featured-content-post">
                                    <div class="post-meta mb-10">
                                        <span class="ttm-meta-line"><i class="fa fa-calendar"></i>January 13, 2020</span>
                                        <span class="ttm-meta-line"><i class="fa fa-folder-open"></i>Natural</span>
                                    </div>
                                    <div class="post-title featured-title">
                                        <h5><a href="blog-single.html">Differences Between a Sauna and a Turkish Bath</a></h5>
                                    </div>
                                    <div class="post-footer"><!-- post-meta -->
                                        <span class="ttm-meta-line"><a class="ttm-textcolor-skincolor" href="blog-single.html">read more&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a></span>
                                        <span class="ttm-meta-line"><i class="fa fa-comment-o"></i>&nbsp;0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="featured-imagebox featured-imagebox-post style3">
                                <div class="ttm-post-thumbnail featured-thumbnail"> 
                                    <img class="img-fluid" src="images/blog/04.jpg" alt="image"> 
                                </div>
                                <div class="featured-content featured-content-post">
                                    <div class="post-meta mb-10">
                                        <span class="ttm-meta-line"><i class="fa fa-calendar"></i>January 13, 2020</span>
                                        <span class="ttm-meta-line"><i class="fa fa-folder-open"></i>Hair care</span>
                                    </div>
                                    <div class="post-title featured-title">
                                        <h5><a href="blog-single.html">Do Massages Have Real Health Benefits?</a></h5>
                                    </div>
                                    <div class="post-footer"><!-- post-meta -->
                                        <span class="ttm-meta-line"><a class="ttm-textcolor-skincolor" href="blog-single.html">read more&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a></span>
                                        <span class="ttm-meta-line"><i class="fa fa-comment-o"></i>&nbsp;0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="featured-imagebox featured-imagebox-post style3">
                                <div class="ttm-post-thumbnail featured-thumbnail"> 
                                    <img class="img-fluid" src="images/blog/05.jpg" alt="image"> 
                                </div>
                                <div class="featured-content featured-content-post">
                                    <div class="post-meta mb-10">
                                        <span class="ttm-meta-line"><i class="fa fa-calendar"></i>January 13, 2020</span>
                                        <span class="ttm-meta-line"><i class="fa fa-folder-open"></i>Bath &amp; Body</span>
                                    </div>
                                    <div class="post-title featured-title">
                                        <h5><a href="blog-single.html">Massage Therapy for Anxiety and Stress</a></h5>
                                    </div>
                                    <div class="post-footer"><!-- post-meta -->
                                        <span class="ttm-meta-line"><a class="ttm-textcolor-skincolor" href="blog-single.html">read more&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a></span>
                                        <span class="ttm-meta-line"><i class="fa fa-comment-o"></i>&nbsp;0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="featured-imagebox featured-imagebox-post style3">
                                <div class="ttm-post-thumbnail featured-thumbnail"> 
                                    <img class="img-fluid" src="images/blog/06.jpg" alt="image"> 
                                </div>
                                <div class="featured-content featured-content-post">
                                    <div class="post-meta mb-10">
                                        <span class="ttm-meta-line"><i class="fa fa-calendar"></i>January 4, 2020</span>
                                        <span class="ttm-meta-line"><i class="fa fa-folder-open"></i>Bath &amp; Body</span>
                                    </div>
                                    <div class="post-title featured-title">
                                        <h5><a href="blog-single.html">Main Responsibilities in Beauty Industry</a></h5>
                                    </div>
                                    <div class="post-footer"><!-- post-meta -->
                                        <span class="ttm-meta-line"><a class="ttm-textcolor-skincolor" href="blog-single.html">read more&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a></span>
                                        <span class="ttm-meta-line"><i class="fa fa-comment-o"></i>&nbsp;0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="featured-imagebox featured-imagebox-post style3">
                                <div class="ttm-post-thumbnail featured-thumbnail"> 
                                    <img class="img-fluid" src="images/blog/07.jpg" alt="image"> 
                                </div>
                                <div class="featured-content featured-content-post">
                                    <div class="post-meta mb-10">
                                        <span class="ttm-meta-line"><i class="fa fa-calendar"></i>January 4, 2020</span>
                                        <span class="ttm-meta-line"><i class="fa fa-folder-open"></i>Make-up</span>
                                    </div>
                                    <div class="post-title featured-title">
                                        <h5><a href="blog-single.html">Turkish Bathroom Benefits for Your Health</a></h5>
                                    </div>
                                    <div class="post-footer"><!-- post-meta -->
                                        <span class="ttm-meta-line"><a class="ttm-textcolor-skincolor" href="blog-single.html">read more&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a></span>
                                        <span class="ttm-meta-line"><i class="fa fa-comment-o"></i>&nbsp;0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="featured-imagebox featured-imagebox-post style3">
                                <div class="ttm-post-thumbnail featured-thumbnail"> 
                                    <img class="img-fluid" src="images/blog/08.jpg" alt="image"> 
                                </div>
                                <div class="featured-content featured-content-post">
                                    <div class="post-meta mb-10">
                                        <span class="ttm-meta-line"><i class="fa fa-calendar"></i>January 4, 2020</span>
                                        <span class="ttm-meta-line"><i class="fa fa-folder-open"></i>Hair care</span>
                                    </div>
                                    <div class="post-title featured-title">
                                        <h5><a href="blog-single.html">How To Straighten Hair To Using  Home Remedies.</a></h5>
                                    </div>
                                    <div class="post-footer"><!-- post-meta -->
                                        <span class="ttm-meta-line"><a class="ttm-textcolor-skincolor" href="blog-single.html">read more&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a></span>
                                        <span class="ttm-meta-line"><i class="fa fa-comment-o"></i>&nbsp;0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="featured-imagebox featured-imagebox-post style3">
                                <div class="ttm-post-thumbnail featured-thumbnail"> 
                                    <img class="img-fluid" src="images/blog/09.jpg" alt="image"> 
                                </div>
                                <div class="featured-content featured-content-post">
                                    <div class="post-meta mb-10">
                                        <span class="ttm-meta-line"><i class="fa fa-calendar"></i>January 4, 2020</span>
                                        <span class="ttm-meta-line"><i class="fa fa-folder-open"></i>Special Product</span>
                                    </div>
                                    <div class="post-title featured-title">
                                        <h5><a href="blog-single.html">Effects of Indian Head Massage and Benefits</a></h5>
                                    </div>
                                    <div class="post-footer"><!-- post-meta -->
                                        <span class="ttm-meta-line"><a class="ttm-textcolor-skincolor" href="blog-single.html">read more&nbsp;&nbsp;<i class="fa fa-angle-right"></i></a></span>
                                        <span class="ttm-meta-line"><i class="fa fa-comment-o"></i>&nbsp;0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- blog section end-->            
        </div><!--site-main end-->

        <!--footer start-->
        <footer class="footer widget-footer clearfix">
            <div class="first-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 col-md-7 col-sm-9 m-auto text-center">
                            <div class="footer-logo">
                                <img id="footer-logo-img" class="img-center" src="images/footer-logo.png" alt="">
                            </div>
                            <h4 class="textwidget widget-text ttm-textcolor-white">Sign Up To Get Latest Updates</h4>
                            <form id="subscribe-form" class="newsletter-form" method="post" action="#" data-mailchimp="true">
                                <div class="mailchimp-inputbox clearfix" id="subscribe-content"> 
                                    <p><input type="email" name="email" placeholder="Your Email Address..." required=""></p>
                                    <p><button class="submit ttm-btn ttm-btn-size-md ttm-btn-shape-rounded ttm-btn-bgcolor-skincolor ttm-textcolor-white" type="submit">Subscribe Now!</button></p>
                                </div>
                                <div id="subscribe-msg"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="second-footer ttm-textcolor-white">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 widget-area">
                            <div class="widget widget_text clearfix">
                                <h3 class="widget-title">About Us</h3>
                                <div class="textwidget widget-text">
                                    <p class="pb-10 res-767-p-0">We consistently showed year on year growth and is now a chain of 118+ branches in Delhi NCR & Northern & Central and worldwide</p>
                                    <p class="pb-10 res-767-p-0">The most innovative products tested & approvby the greatest names in hairdressing.</p>
                                    <a class="ttm-color-skincolor" href="#" title="">- More About Hair Salone</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 widget-area">
                            <div class="widget widget_text clearfix">
                                <h3 class="widget-title">Resent News</h3>
                                <ul class="widget-post ttm-recent-post-list">
                                    <li>
                                        <a href="blog-single.html"><img src="images/blog/01.jpg" class="lazyload" alt="post-img"></a>
                                        <span class="post-date">January 22, 2020</span>
                                        <a href="blog-single.html">Essential barbering tips need to know start</a>
                                    </li>
                                    <li>
                                        <a href="blog-single.html"><img src="images/blog/02.jpg" class="lazyload" alt="post-img"></a>
                                        <span class="post-date">January 18, 2020</span>
                                        <a href="blog-single.html">Winter Dreams– Capturing The Snow</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 widget-area">
                            <div class="widget flicker_widget clearfix">
                               <h3 class="widget-title">Get In  Touch</h3>
                               <div class="textwidget widget-text">
                                    <div class="featured-icon-box icon-align-before-content icon-ver_align-top style3">
                                        <div class="featured-icon">
                                            <div class="ttm-icon ttm-icon_element-onlytxt ttm-icon_element-color-skincolor ttm-icon_element-size-sm">
                                                <i class="fa fa-map-marker"></i>
                                            </div>
                                        </div>
                                        <div class="featured-content">
                                            <div class="featured-desc">
                                                <p>4789 Melmorn Street,Zakila Ton<br>Mashintron Town </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="featured-icon-box icon-align-before-content icon-ver_align-top style3">
                                        <div class="featured-icon">
                                            <div class="ttm-icon ttm-icon_element-onlytxt ttm-icon_element-color-skincolor ttm-icon_element-size-sm">
                                                <i class="fa fa-envelope-o"></i>
                                            </div>
                                        </div>
                                        <div class="featured-content">
                                            <div class="featured-desc">
                                                <p><a href="mailto:info@example.com.com">info@example.com</a></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="featured-icon-box icon-align-before-content icon-ver_align-top style3">
                                        <div class="featured-icon">
                                            <div class="ttm-icon ttm-icon_element-onlytxt ttm-icon_element-color-skincolor ttm-icon_element-size-sm">
                                                <i class="fa fa-phone"></i>
                                            </div>
                                        </div>
                                        <div class="featured-content">
                                            <div class="featured-desc">
                                                <p>(+01) 123 456 7890</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom-footer-text">
                <div class="container">
                    <div class="row copyright">
                        <div class="col-md-9">
                            <div class="ttm-textcolor-white">
                                <span>Copyright &copy; 2020&nbsp;<a class="ttm-textcolor-skincolor" href="#">Sylin Theme</a> by <a href="https://themetechmount.com/" target="_blank">Themetechmount</a></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex flex-row align-items-center justify-content-end social-icons">
                            <ul class="social-icons list-inline">
                                <li><a href="https://www.facebook.com/preyantechnosys19" class=" tooltip-top" data-tooltip="Facebook" target="_blank"><i class="fa fa-facebook"></i></a>
                                </li>
                                <li><a href="https://twitter.com/PreyanTechnosys" class=" tooltip-top" data-tooltip="Twitter" target="_blank"><i class="fa fa-twitter"></i></a>
                                </li>
                                <li><a href="https://www.flickr.com/photos/166353669@N03/" class=" tooltip-top" data-tooltip="Flickr" target="_blank"><i class="fa fa-flickr"></i></a>
                                </li>
                                <li><a href="https://www.linkedin.com/in/preyan-technosys-pvt-ltd/" class=" tooltip-top" data-tooltip="Linkedin" target="_blank"><i class="fa fa-linkedin"></i></a>
                                </li>
                            </ul>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!--footer end-->

        <!--back-to-top start-->
        <a id="totop" href="#top">
            <i class="fa fa-angle-up"></i>
        </a>
        <!--back-to-top end-->

    </div><!-- page end -->

        <!-- Javascript -->

        <script src="js/jquery.min.js"></script>
        <script src="js/jquery-migrate-3.4.1.min.js"></script>
        <script src="js/tether.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.easing.js"></script>    
        <script src="js/jquery-waypoints.js"></script>    
        <script src="js/jquery-validate.js"></script> 
        <script src="js/owl.carousel.js"></script>
        <script src="js/jquery.prettyPhoto.js"></script>
        <script src="js/numinate.min6959.js?ver=4.9.3"></script>
        <script src="js/lazysizes.min.js"></script>
        <script src="js/main.js"></script>

        <!-- Revolution Slider -->
        <script src="revolution/js/revolution.tools.min.js"></script>
        <script src="revolution/js/rs6.min.js"></script>
        <script src="revolution/js/slider.js"></script>

        <!-- Javascript end-->

</body>

</html>