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
</div>
<!--site-main end-->

<?php include 'includes/footer.php'; ?>