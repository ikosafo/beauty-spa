<?php
require_once 'config.php';

// Services Data from ws_services
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
        'subtitle' => 'Welcome to Golden View',
        'title' => 'Explore Our Luxurious Services',
        'description' => 'Step into a world of wellness at Golden View Therapeutic Clinique and Spa, where our expertly crafted treatments rejuvenate your body, mind, and soul.'
    ];
}
if (empty($boxes)) {
    $boxes = [
        1 => [
            'image' => 'images/services/01.jpg',
            'title' => 'Healing Therapeutic Massage',
            'description' => 'Melt away stress with our deep tissue massage, tailored to relieve chronic pain.',
            'icon' => 'flaticon-spa-1',
            'link' => '/services#healing-therapeutic-massage',
            'box_order' => 1
        ],
        2 => [
            'image' => 'images/services/02.jpg',
            'title' => 'Body Exfoliating',
            'description' => 'Reveal radiant skin with our gentle, organic exfoliation treatments.',
            'icon' => 'flaticon-herbal',
            'link' => '/services#body-exfoliating',
            'box_order' => 2
        ],
        3 => [
            'image' => 'images/services/03.jpg',
            'title' => 'Facial Therapy',
            'description' => 'Restore a youthful glow with our advanced facial treatments.',
            'icon' => 'flaticon-spa',
            'link' => '/services#facial-therapy-skin-tag-removal',
            'box_order' => 3
        ],
        4 => [
            'image' => 'images/services/04.jpg',
            'title' => 'Medical Feet Care',
            'description' => 'Address foot concerns with our specialized treatments for ingrown nails and more.',
            'icon' => 'flaticon-cupping',
            'link' => '/services#medical-feet-care',
            'box_order' => 4
        ]
    ];
}

// Define all services without subservices
$all_services = [
    'Healing Therapeutic Massage' => [
        'id' => 'healing-therapeutic-massage',
        'link' => '/services#healing-therapeutic-massage',
        'image' => 'images/services/healing-therapeutic-massage.jpg',
        'description' => 'Experience unparalleled relaxation with our Healing Therapeutic Massage. Our expert therapists use advanced deep tissue techniques to relieve chronic pain, reduce muscle tension, and promote overall wellness, leaving you refreshed and revitalized.',
        'benefits' => [
            'Relieves chronic pain and tension',
            'Enhances circulation and mobility',
            'Promotes deep relaxation'
        ]
    ],
    'Body Exfoliating' => [
        'id' => 'body-exfoliating',
        'link' => '/services#body-exfoliating',
        'image' => 'images/services/body-exfoliating.jpg',
        'description' => 'Unveil smoother, more radiant skin with our Body Exfoliating treatments. Using natural, organic scrubs, our specialists gently remove dead skin cells, leaving your skin soft, hydrated, and glowing with vitality.',
        'benefits' => [
            'Removes dead skin for a smoother texture',
            'Boosts skin hydration and radiance',
            'Stimulates cell renewal'
        ]
    ],
    'Medical Feet Care' => [
        'id' => 'medical-feet-care',
        'link' => '/services#medical-feet-care',
        'image' => 'images/services/medical-feet-care.jpg',
        'description' => 'Restore comfort and health to your feet with our Medical Feet Care services. Our expert pedicurists provide specialized treatments for ingrown nails, toe nail reconstruction, and severe heel cracks, ensuring your feet feel rejuvenated and look their best.',
        'benefits' => [
            'Relieves pain from ingrown nails',
            'Restores nail and heel health',
            'Improves foot comfort and appearance'
        ]
    ],
    'Facial Therapy / Skin Tag Removal' => [
        'id' => 'facial-therapy-skin-tag-removal',
        'link' => '/services#facial-therapy-skin-tag-removal',
        'image' => 'images/services/facial-therapy.jpg',
        'description' => 'Rejuvenate your complexion with our Facial Therapy and Skin Tag Removal services. Our advanced treatments reduce signs of aging, hydrate the skin, and safely remove skin tags, leaving your face radiant and flawless.',
        'benefits' => [
            'Reduces fine lines and wrinkles',
            'Safely removes skin tags',
            'Enhances skin clarity and glow'
        ]
    ],
    'Hair Dressing / Braids & Locks' => [
        'id' => 'hair-dressing-braids-locks',
        'link' => '/services#hair-dressing-braids-locks',
        'image' => 'images/services/hair-dressing.jpg',
        'description' => 'Transform your look with our Hair Dressing, Braids, and Locks services. Our skilled stylists create stunning, customized designs for all hair types, ensuring a vibrant, long-lasting style that enhances your natural beauty.',
        'benefits' => [
            'Personalized, long-lasting styles',
            'Suits all hair types',
            'Enhances natural beauty'
        ]
    ],
    'Natural Body Contouring' => [
        'id' => 'natural-body-contouring',
        'link' => '/services#natural-body-contouring',
        'image' => 'images/services/body-contouring.jpg',
        'description' => 'Sculpt your body naturally with our non-invasive Natural Body Contouring treatments. Designed to tone and enhance your natural curves, this service boosts confidence and promotes overall wellness.',
        'benefits' => [
            'Tones and firms skin',
            'Non-surgical and safe',
            'Enhances body confidence'
        ]
    ],
    'Beauty Therapy' => [
        'id' => 'beauty-therapy',
        'link' => '/services#beauty-therapy',
        'image' => 'images/services/beauty-therapy.jpg',
        'description' => 'Indulge in our Beauty Therapy services, featuring luxurious pedicures, manicures, nail treatments, and professional makeup application. Our treatments are designed to pamper you and enhance your natural beauty for any occasion.',
        'benefits' => [
            'Polished, professional nails and makeup',
            'Enhances hands, feet, and overall appearance',
            'Relaxing and luxurious experience'
        ]
    ],
    'Weave-On and Hair Installation' => [
        'id' => 'weave-on-hair-installation',
        'link' => '/services#weave-on-hair-installation',
        'image' => 'images/services/weave-on.jpg',
        'description' => 'Achieve a seamless, natural look with our Weave-On and Hair Installation services. Our experts provide versatile, durable hair enhancements that suit your style and boost confidence.',
        'benefits' => [
            'Natural, seamless hair enhancements',
            'Versatile styling options',
            'Low-maintenance and durable'
        ]
    ],
    'Corporate Massage' => [
        'id' => 'corporate-massage',
        'link' => '/services#corporate-massage',
        'image' => 'images/services/corporate-massage.jpg',
        'description' => 'Boost workplace wellness with our Corporate Massage services. Our quick, stress-relieving sessions are tailored for busy professionals, promoting relaxation and productivity.',
        'benefits' => [
            'Reduces workplace stress',
            'Boosts productivity and focus',
            'Convenient, quick sessions'
        ]
    ],
    'Hydro Therapy / Sauna' => [
        'id' => 'hydro-therapy-sauna',
        'link' => '/services#hydro-therapy-sauna',
        'image' => 'images/services/hydro-therapy.jpg',
        'description' => 'Detoxify and unwind with our Hydro Therapy and Sauna sessions. These treatments cleanse the body, improve skin health, and promote deep relaxation for a rejuvenated you.',
        'benefits' => [
            'Detoxifies and cleanses the body',
            'Improves skin health and radiance',
            'Promotes relaxation and stress relief'
        ]
    ],
    'Training School' => [
        'id' => 'training-school',
        'link' => '/services#training-school',
        'image' => 'images/services/training-school.jpg',
        'description' => 'Launch your career in wellness with our Training School. Our expert-led courses in spa and beauty techniques empower you with the skills to excel in the industry.',
        'benefits' => [
            'Expert-led, hands-on training',
            'Industry-recognized certifications',
            'Empowers career growth'
        ]
    ],
    'Additional Offerings' => [
        'id' => 'additional-offerings',
        'link' => '/services#additional-offerings',
        'image' => 'images/services/additional.jpg',
        'description' => 'Explore our exclusive wellness packages, including aromatherapy, wellness consultations, and tailored spa experiences designed to enhance your journey to well-being.',
        'benefits' => [
            'Customized wellness experiences',
            'Enhances overall well-being',
            'Exclusive, tailored packages'
        ]
    ]
];
?>

<?php include 'includes/header.php'; ?>

<!-- page title -->
<div class="ttm-page-title-row">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-box text-center">
                    <div class="page-title-heading">
                        <h1>Our Services</h1>
                    </div>
                    <div class="breadcrumb-wrapper">
                        <span><a title="Homepage" href="/">Home</a></span>
                        <span class="ttm-bread-sep">&nbsp; / &nbsp;</span>
                        <span>Services</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- page title end -->

<!--site-main start-->
<div class="site-main">
    <!-- service-section -->
    <section class="ttm-row service-section ttm-bgcolor-grey clearfix">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-10 col-sm-12 m-auto">
                    <div class="section-title with-desc text-center clearfix">
                        <div class="title-header">
                            <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($section['subtitle']); ?></h5>
                            <h2 class="title"><?php echo htmlspecialchars($section['title']); ?></h2>
                        </div>
                        <div class="title-desc"><?php echo htmlspecialchars($section['description']); ?></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="services-slide owl-carousel" data-item="4" data-nav="true" data-dots="false" data-auto="true">
                    <?php foreach ($boxes as $box): ?>
                        <div class="featured-imagebox featured-imagebox-services text-center style1">
                            <div class="ttm-post-thumbnail featured-thumbnail">
                                <img class="img-fluid lazyload" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $box['image']); ?>" alt="<?php echo htmlspecialchars($box['title']); ?>">
                                <div class="featured-icon">
                                    <div class="ttm-icon ttm-icon_element-fill ttm-icon_element-color-skincolor ttm-icon_element-size-md ttm-icon_element-style-rounded">
                                        <i class="<?php echo htmlspecialchars($box['icon']); ?>"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="featured-content box-shadow">
                                <div class="featured-title">
                                    <h5><a href="<?php echo htmlspecialchars($box['link']); ?>"><?php echo htmlspecialchars($box['title']); ?></a></h5>
                                </div>
                                <div class="featured-desc">
                                    <p><?php echo htmlspecialchars($box['description']); ?></p>
                                </div>
                                <a class="ttm-btn ttm-btn-size-sm ttm-btn-style-border ttm-btn-color-skincolor mt-15" href="<?php echo htmlspecialchars($box['link']); ?>">Learn More</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <!-- service-section end -->

    <!-- sidebar -->
    <div class="ttm-row sidebar service-detail ttm-bgcolor-white clearfix">
        <div class="container">
            <!-- row -->
            <div class="row">
                <div class="col-lg-9 content-area order-lg-2">
                    <?php foreach ($all_services as $category => $service): ?>
                        <section class="ttm-row service-category-section ttm-bgcolor-grey mb-50 clearfix" id="<?php echo htmlspecialchars($service['id']); ?>">
                            <div class="ttm-service-single-content-area box-shadow">
                                <div class="ttm_single_image-wrapper mb-30 res-767-mb-20">
                                    <img class="img-fluid lazyload" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $service['image']); ?>" alt="<?php echo htmlspecialchars($category); ?>">
                                </div>
                                <div class="ttm-service-description p-30">
                                    <h3 class="mb-20"><?php echo htmlspecialchars($category); ?></h3>
                                    <p class="mb-20"><?php echo htmlspecialchars($service['description']); ?></p>
                                    <div class="ttm-service-benefits mb-30">
                                        <h5 class="mb-15">Benefits</h5>
                                        <ul class="ttm-list ttm-list-style-icon ttm-list-icon-color-skincolor style3">
                                            <?php foreach ($service['benefits'] as $benefit): ?>
                                                <li><i class="fa fa-check-circle"></i><span class="ttm-list-li-content"><?php echo htmlspecialchars($benefit); ?></span></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <a class="ttm-btn ttm-btn-size-md ttm-btn-style-fill ttm-btn-color-skincolor mt-20" href="/book">Book Now</a>
                                </div>
                            </div>
                        </section>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-3 widget-area">
                    <aside class="widget widget-nav-menu box-shadow ttm-bgcolor-grey mb-30">
                        <h3 class="widget-title">Our Services</h3>
                        <ul class="widget-menu">
                            <?php foreach ($all_services as $category => $service): ?>
                                <li><a href="<?php echo htmlspecialchars($service['link']); ?>" class="<?php echo (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], $service['id']) !== false) ? 'active' : ''; ?>"><?php echo htmlspecialchars($category); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </aside>
                    <aside class="widget widget-download mb-30">
                        <ul class="download">
                            <li>
                                <span class="ttm-fileicon position-relative ttm-textcolor-skincolor"><i class="fa fa-file-pdf-o"></i></span>
                                <div class="ttm-fielcontent ml-20">
                                    <h5 class="mb-0">Services Brochure</h5>
                                    <a href="/downloads/services-brochure.pdf" title="Download">Download.pdf</a>
                                </div>
                            </li>
                        </ul>
                    </aside>
                    <aside class="widget widget_media_image res-991-text-center">
                        <div class="banner-img-box">
                            <img class="img-fluid lazyload" alt="sidebar-right-banner1" src="<?php echo htmlspecialchars(URLROOT . '/cms/images/sidebar-right-banner1.jpg'); ?>">
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
    <!-- sidebar end -->
</div>
<!--site-main end-->

<?php include 'includes/footer.php'; ?>