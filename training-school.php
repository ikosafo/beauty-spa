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
        'title' => 'Golden View Therapeutic Clinik and Spa Academy',
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
            <!-- why-choose-academy-section – MODERN UI -->
        <div class="row mt-5">
            <div class="col-lg-12 text-center mb-4">
                <h3 class="fw-bold">Why Choose Our Academy?</h3>
                <p class="text-muted">Our training school combines theoretical knowledge with practical experience, ensuring you’re ready to excel in the beauty industry.</p>
            </div>
        </div>

        <div class="row g-4 justify-content-center mb-5"  style="padding-bottom: 30px !important;">
            <?php
            // You can later move these to DB (e.g. ws_training_school with type='why')
            $why_items = [
                [
                    'icon'  => 'fa-solid fa-chalkboard-teacher',
                    'title' => 'Expert Instructors',
                    'desc'  => 'Learn from industry leaders with extensive experience, guiding you to master professional techniques.'
                ],
                [
                    'icon'  => 'fa-solid fa-hands-helping',
                    'title' => 'Hands-On Training',
                    'desc'  => 'Gain practical skills through real-world practice in our state-of-the-art facilities.'
                ],
                [
                    'icon'  => 'fa-solid fa-certificate',
                    'title' => 'Certification',
                    'desc'  => 'Earn industry-recognized certifications to launch your career with confidence.'
                ]
            ];

            foreach ($why_items as $item):
            ?>
                <div class="col-lg-4 col-md-6">
                    <div class="why-card text-center p-4 bg-white rounded-3 shadow-sm h-100 d-flex flex-column transition-all">
                        <!-- Icon -->
                        <!-- <div class="why-icon mb-3">
                            <i class="<?php echo $item['icon']; ?> fa-3x ttm-textcolor-skincolor"></i>
                        </div> -->
                        <!-- Title -->
                        <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($item['title']); ?></h5>
                        <!-- Description -->
                        <p class="text-muted small flex-grow-1"><?php echo htmlspecialchars($item['desc']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

<style>
/* Hover animation */
.why-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #eee;
}
.why-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.12) !important;
    border-color: var(--skincolor, #d4a574);
}
.why-icon i {
    transition: color 0.3s ease;
}
.why-card:hover .why-icon i {
    color: var(--skincolor, #d4a574) !important;
}
</style>
        </div>
    </section>
    <!-- training-section end -->
</div>
<!--site-main end-->

<?php include 'includes/footer.php'; ?>