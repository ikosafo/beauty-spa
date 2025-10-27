<?php
require_once 'config.php';

// Fetch training school data
$training_section = [];
$programs = [];
$query = "SELECT * FROM ws_training_school WHERE id IN (1, 2, 3, 4, 5, 6) ORDER BY id";
$result = $mysqli->query($query);
if (!$result) {
    error_log('Fetch query failed: ' . $mysqli->error);
} else {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $training_section = $row;
        } else {
            $programs[$row['program_order']] = $row;
        }
    }
}
if (empty($training_section)) {
    $training_section = [
        'id' => 1,
        'type' => 'section',
        'image' => 'images/services/training-school.jpg',
        'subtitle' => 'Learn with Us',
        'title' => 'Sylin Beauty Academy',
        'description' => 'Join our professional training programs to become a certified beauty and spa specialist. Our courses cover makeup artistry, skincare, hairdressing, and more.'
    ];
}
if (empty($programs)) {
    $programs = [
        1 => [
            'id' => 2,
            'type' => 'program',
            'program_name' => 'Professional Makeup Artistry',
            'program_order' => 1
        ],
        2 => [
            'id' => 3,
            'type' => 'program',
            'program_name' => 'Advanced Skincare Techniques',
            'program_order' => 2
        ],
        3 => [
            'id' => 4,
            'type' => 'program',
            'program_name' => 'Hairdressing and Styling',
            'program_order' => 3
        ],
        4 => [
            'id' => 5,
            'type' => 'program',
            'program_name' => 'Spa Therapy and Massage',
            'program_order' => 4
        ],
        5 => [
            'id' => 6,
            'type' => 'program',
            'program_name' => 'Nail Art and Manicure',
            'program_order' => 5
        ]
    ];
}
error_log('Fetched training school data: ' . print_r(['training_section' => $training_section, 'programs' => $programs], true));

?>

<?php include 'includes/header.php'; ?>

<!-- page title -->
<div class="ttm-page-title-row">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-box text-center">
                    <div class="page-title-heading">
                        <h1>Training School</h1>
                    </div>
                    <div class="breadcrumb-wrapper">
                        <span><a title="Homepage" href="/">Home</a></span>
                        <span class="ttm-bread-sep">&nbsp; / &nbsp;</span>
                        <span>Training School</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- page title end -->

<!--site-main start-->
<div class="site-main">
    <!-- training-section -->
    <section class="ttm-row training-section ttm-bgcolor-grey clearfix">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title with-desc text-center clearfix mt-50">
                        <div class="title-header">
                            <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($training_section['subtitle']); ?></h5>
                            <h2 class="title"><?php echo htmlspecialchars($training_section['title']); ?></h2>
                        </div>
                        <div class="title-desc"><?php echo htmlspecialchars($training_section['description']); ?></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="ttm_single_image-wrapper mb-30 res-767-mb-20">
                        <img class="img-fluid lazyload" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $training_section['image']); ?>" alt="Training School">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="ttm-service-description p-30">
                        <h3>Our Training Programs</h3>
                        <p>At <?php echo htmlspecialchars($training_section['title']); ?>, we offer comprehensive courses designed to equip you with the skills needed in the beauty industry. From beginner to advanced levels, our expert instructors provide hands-on training.</p>
                        <ul class="ttm-list ttm-list-style-icon ttm-list-icon-color-skincolor style3">
                            <?php foreach ($programs as $program): ?>
                                <li><i class="fa fa-check-circle"></i><span class="ttm-list-li-content"><?php echo htmlspecialchars($program['program_name']); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                        <a class="ttm-btn ttm-btn-size-md ttm-btn-style-fill ttm-btn-color-skincolor mt-20" href="/enroll">Enroll Now!</a>
                    </div>
                </div>
            </div>
            <div class="row mt-50">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <h3>Why Choose Our Academy?</h3>
                    </div>
                    <p class="text-center">Our training school combines theoretical knowledge with practical experience, ensuring you’re ready to excel in the beauty industry. Learn in a supportive environment designed for success.</p>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="featured-icon-box style1 text-center box-shadow">
                                <div class="featured-icon-box-inner">
                                    <div class="featured-icon">
                                        <div class="ttm-icon ttm-icon_element-size-lg ttm-textcolor-skincolor">
                                            <i class="flaticon-spa"></i>
                                        </div>
                                    </div>
                                    <div class="featured-content">
                                        <div class="featured-title">
                                            <h5>Expert Instructors</h5>
                                        </div>
                                        <div class="featured-desc">
                                            <p>Learn from industry leaders with extensive experience, guiding you to master professional techniques.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="featured-icon-box style1 text-center box-shadow">
                                <div class="featured-icon-box-inner">
                                    <div class="featured-icon">
                                        <div class="ttm-icon ttm-icon_element-size-lg ttm-textcolor-skincolor">
                                            <i class="flaticon-wellness"></i>
                                        </div>
                                    </div>
                                    <div class="featured-content">
                                        <div class="featured-title">
                                            <h5>Hands-On Training</h5>
                                        </div>
                                        <div class="featured-desc">
                                            <p>Gain practical skills through real-world practice in our state-of-the-art facilities.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="featured-icon-box style1 text-center box-shadow">
                                <div class="featured-icon-box-inner">
                                    <div class="featured-icon">
                                        <div class="ttm-icon ttm-icon_element-size-lg ttm-textcolor-skincolor">
                                            <i class="flaticon-hammam"></i>
                                        </div>
                                    </div>
                                    <div class="featured-content">
                                        <div class="featured-title">
                                            <h5>Certification</h5>
                                        </div>
                                        <div class="featured-desc">
                                            <p>Earn industry-recognized certifications to launch your career with confidence.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- training-section end -->
</div>
<!--site-main end-->

<?php include 'includes/footer.php'; ?>