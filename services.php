<?php
require_once 'config.php';

// Services Data
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
        'title' => 'EXPLORE OUR SERVICES',
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
            'link' => '/services/body-skin-care/facial-therapy-skin-tag-removal',
            'box_order' => 1
        ],
        2 => [
            'image' => 'images/services/02.jpg',
            'title' => 'Back Massage',
            'description' => 'Relieve tension and stress with our expert back massage techniques',
            'icon' => 'flaticon-spa-1',
            'link' => '/services/massage-relaxation',
            'box_order' => 2
        ],
        3 => [
            'image' => 'images/services/03.jpg',
            'title' => 'Hair Treatment',
            'description' => 'We offer professional hair care for all hair types, ensuring healthy and vibrant hair',
            'icon' => 'flaticon-spa',
            'link' => '/services/hair-services',
            'box_order' => 3
        ],
        4 => [
            'image' => 'images/services/04.jpg',
            'title' => 'Skin Care',
            'description' => 'Experience therapies like body exfoliating and facial treatments for radiant skin',
            'icon' => 'flaticon-cupping',
            'link' => '/services/body-skin-care',
            'box_order' => 4
        ]
    ];
}

// Define all services from navigation menu for display
$all_services = [
    'Massage & Relaxation' => [
        'link' => '/services/massage-relaxation',
        'subservices' => [
            ['title' => 'Healing Therapeutic Massage', 'link' => '/services/massage-relaxation/healing-therapeutic-massage'],
            ['title' => 'Corporate Massage', 'link' => '/services/massage-relaxation/corporate-massage'],
            ['title' => 'Hydro Therapy / Sauna', 'link' => '/services/massage-relaxation/hydro-therapy-sauna']
        ]
    ],
    'Body & Skin Care' => [
        'link' => '/services/body-skin-care',
        'subservices' => [
            ['title' => 'Body Exfoliating', 'link' => '/services/body-skin-care/body-exfoliating'],
            ['title' => 'Natural Body Contouring', 'link' => '/services/body-skin-care/natural-body-contouring'],
            ['title' => 'Facial Therapy / Skin Tag Removal', 'link' => '/services/body-skin-care/facial-therapy-skin-tag-removal']
        ]
    ],
    'Foot & Nail Care' => [
        'link' => '/services/foot-nail-care',
        'subservices' => [
            ['title' => 'Medical Feet Care', 'link' => '/services/foot-nail-care/medical-feet-care'],
            ['title' => 'Beauty Therapy', 'link' => '/services/foot-nail-care/beauty-therapy']
        ]
    ],
    'Hair Services' => [
        'link' => '/services/hair-services',
        'subservices' => [
            ['title' => 'Hair Dressing / Braids & Locks', 'link' => '/services/hair-services/hair-dressing-braids-locks'],
            ['title' => 'Weave-On & Hair Installation', 'link' => '/services/hair-services/weave-on-hair-installation']
        ]
    ],
    'Additional Offerings' => [
        'link' => '/services/additional-offerings',
        'subservices' => []
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
                        <h1>SERVICES</h1>
                    </div>
                    <div class="breadcrumb-wrapper">
                        <span><a title="Homepage" href="/home">Home</a></span>
                        <span class="ttm-bread-sep">&nbsp; / &nbsp;</span>
                        <span>Services</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- page title -->

<!--site-main start-->
<div class="site-main">
    <!-- service-section -->
    <section class="ttm-row service-section bg-img1 clearfix">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-9 m-auto">
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
                <div class="services-slide owl-carousel" data-item="4" data-nav="false" data-dots="false" data-auto="false">
                    <?php foreach ($boxes as $box): ?>
                        <div class="featured-imagebox featured-imagebox-services text-center style1">
                            <div class="ttm-post-thumbnail featured-thumbnail">
                                <img class="img-fluid" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $box['image']); ?>" alt="service-image">
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
            <!-- All Services List -->
            <div class="row mt-50">
                <div class="col-lg-12">
                    <div class="section-title with-desc text-center clearfix">
                        <div class="title-header">
                            <h5 class="ttm-textcolor-skincolor">Our Offerings</h5>
                            <h2 class="title">ALL OUR SERVICES</h2>
                        </div>
                        <div class="title-desc">Explore our full range of spa and salon services designed to rejuvenate and enhance your beauty.</div>
                    </div>
                </div>
                <?php foreach ($all_services as $category => $service): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="featured-icon-box icon-align-before-content style2">
                            <div class="featured-icon">
                                <div class="ttm-icon ttm-icon_element-fill ttm-icon_element-color-white ttm-icon_element-size-md ttm-icon_element-style-rounded">
                                    <i class="flaticon-spa"></i>
                                </div>
                            </div>
                            <div class="featured-content">
                                <div class="featured-title">
                                    <h5><a href="<?php echo htmlspecialchars($service['link']); ?>"><?php echo htmlspecialchars($category); ?></a></h5>
                                </div>
                                <div class="featured-desc">
                                    <ul class="service-sub-list">
                                        <?php if (!empty($service['subservices'])): ?>
                                            <?php foreach ($service['subservices'] as $subservice): ?>
                                                <li><a href="<?php echo htmlspecialchars($subservice['link']); ?>"><?php echo htmlspecialchars($subservice['title']); ?></a></li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li><a href="<?php echo htmlspecialchars($service['link']); ?>">View Details</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- service-section end -->
</div>
<!--site-main end-->

<?php include 'includes/footer.php'; ?>

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