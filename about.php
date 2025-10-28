<?php
require_once 'config.php';

// Contact Data (needed for header and footer)
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

// About
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
?>

<?php include 'includes/header.php'; ?>

<!-- page title -->
<div class="ttm-page-title-row">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-box text-center">
                    <div class="page-title-heading">
                        <h1>ABOUT US</h1>
                    </div>
                    <div class="breadcrumb-wrapper">
                        <span><a title="Homepage" href="/home">home</a></span>
                        <span class="ttm-bread-sep">&nbsp; / &nbsp;</span>
                        <span>About Us</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- page title end -->

<!--site-main start-->
<div class="site-main">
    <!-- aboutus-section -->
    <section class="ttm-row aboutus-section clearfix">
        <div class="container">
            <div class="row">
                <div class="col-md-6 res-767-center">
                    <img src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $about['image']); ?>" class="img-fluid" alt="about-image">
                </div>
                <div class="col-md-6 res-767-pt-30">
                    <div class="spacing-1">
                        <div class="section-title with-desc clearfix res-1199-mb-0">
                            <div class="title-header">
                                <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($about['subtitle']); ?></h5>
                                <h2 class="title"><?php echo htmlspecialchars($about['title']); ?></h2>
                            </div>
                            <div class="title-desc"><?php echo htmlspecialchars($about['description']); ?></div>
                        </div>
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
    <!-- aboutus-section end -->

    <!-- mission-vision-section -->
    <section class="ttm-row mission-vision-section ttm-bgcolor-grey clearfix">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <div class="title-header">
                            <h5 class="ttm-textcolor-skincolor">Our Purpose</h5>
                            <h2 class="title">Mission & Vision</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="featured-box style2">
                        <div class="featured-icon">
                            <div class="ttm-icon ttm-icon_element-size-md ttm-textcolor-skincolor">
                                <i class="flaticon-wellness"></i>
                            </div>
                        </div>
                        <div class="featured-content">
                            <div class="featured-title">
                                <h5>Our Mission</h5>
                            </div>
                            <div class="featured-desc">
                                <p>At Golden View, our mission is to provide a sanctuary of holistic wellness, where every guest experiences rejuvenation through expertly crafted therapies and natural, high-quality products. We strive to restore balance and vitality to both body and soul.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="featured-box style2">
                        <div class="featured-icon">
                            <div class="ttm-icon ttm-icon_element-size-md ttm-textcolor-skincolor">
                                <i class="flaticon-spa"></i>
                            </div>
                        </div>
                        <div class="featured-content">
                            <div class="featured-title">
                                <h5>Our Vision</h5>
                            </div>
                            <div class="featured-desc">
                                <p>We envision Golden View as a global leader in wellness, inspiring transformative experiences that empower individuals to live healthier, more balanced lives. Our goal is to create serene environments where beauty, energy, and tranquility converge.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- mission-vision-section end -->

    <!-- philosophy-section -->
    <section class="ttm-row philosophy-section clearfix">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <div class="title-header">
                            <h5 class="ttm-textcolor-skincolor">Our Approach</h5>
                            <h2 class="title">Our Philosophy</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="ttm-textcolor-darkgrey text-center">
                        <p>At Golden View, we believe wellness is a harmonious blend of mind, body, and spirit. Our philosophy centers on holistic care, using only premium, natural products to nurture your well-being. Each treatment is designed to restore balance, enhance beauty, and promote inner peace, creating a transformative experience tailored to you.</p>
                        <a class="ttm-btn ttm-btn-size-md ttm-btn-shape-rounded ttm-btn-bgcolor-skincolor mt-20" href="/services">Explore Our Services</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- philosophy-section end -->

    <!-- why-choose-us-section – IMPROVED UI -->
    <section class="ttm-row why-choose-us-section ttm-bgcolor-grey py-5 clearfix">
        <div class="container">
            <!-- Section Title -->
            <div class="row mb-5">
                <div class="col-lg-12 text-center">
                    <div class="section-title">
                        <div class="title-header">
                            <h5 class="ttm-textcolor-skincolor">Why Golden View</h5>
                            <h2 class="title">What Sets Us Apart</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature Cards -->
            <div class="row g-4 justify-content-center">
                <?php
                // You can later move these to DB (ws_about or a new table)
                $features = [
                    [
                        'icon'  => 'fa-solid fa-user-tie',
                        'title' => 'Expert Therapists',
                        'desc'  => 'Our certified professionals deliver unparalleled care with years of expertise in holistic wellness.'
                    ],
                    [
                        'icon'  => 'fa-solid fa-leaf',
                        'title' => 'Premium Products',
                        'desc'  => 'We use only high-quality, natural products to ensure safe and effective treatments.'
                    ],
                    [
                        'icon'  => 'fa-solid fa-spa',
                        'title' => 'Serene Ambiance',
                        'desc'  => 'Our tranquil environment is designed to promote relaxation and inner peace.'
                    ],
                    [
                        'icon'  => 'fa-solid fa-heart-pulse',
                        'title' => 'Personalized Care',
                        'desc'  => 'Every treatment is tailored to meet your unique needs and wellness goals.'
                    ]
                ];

                foreach ($features as $f):
                ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="feature-card text-center p-4 bg-white rounded-3 shadow-sm h-100 d-flex flex-column justify-content-start transition-all">
                            <!-- Icon -->
                           <!--  <div class="feature-icon mb-3">
                                <i class="<?php echo $f['icon']; ?> fa-3x ttm-textcolor-skincolor"></i>
                            </div> -->
                            <!-- Title -->
                            <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($f['title']); ?></h5>
                            <!-- Description -->
                            <p class="text-muted small flex-grow-1"><?php echo htmlspecialchars($f['desc']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<style>
/* Hover animation & smooth transitions */
.feature-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #eee;
}
.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.1) !important;
    border-color: var(--skincolor, #d4a574);
}
.feature-icon i {
    transition: color 0.3s ease;
}
.feature-card:hover .feature-icon i {
    color: var(--skincolor, #d4a574) !important;
}
</style>
</div>
<!--site-main end-->

<?php include 'includes/footer.php'; ?>