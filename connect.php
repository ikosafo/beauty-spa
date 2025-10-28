<?php
require_once 'config.php';

// Contact Data
$query = "SELECT `address`, `email`, `phone1`, `logo`, `working_hours` FROM ws_contact WHERE id = 1";
$result = $mysqli->query($query);
$connect_data = $result && $result->num_rows > 0 ? $result->fetch_assoc() : [
    'address' => '1010 Avenue of the Moon, 12',
    'email' => 'info@fyansalone.com',
    'phone1' => '+ (123) 456 7890',
    'logo' => 'images/logo.png',
    'working_hours' => 'Mon-Fri: 9 AM - 5 PM'
];

// Log query errors for debugging
if (!$result) {
    error_log('Contact query failed: ' . $mysqli->error);
}

// Ensure all keys exist and are not null
$connect_data = array_merge([
    'address' => '1010 Avenue of the Moon, 12',
    'email' => 'info@fyansalone.com',
    'phone1' => '+ (123) 456 7890',
    'logo' => 'images/logo.png',
    'working_hours' => 'Mon-Fri: 9 AM - 5 PM'
], $connect_data);

// Set logo path
$logo_path = $connect_data['logo'];

// Handle Contact Form Submission
$submission_message = '';
$form_data = [
    'name' => $_POST['name'] ?? '',
    'email' => $_POST['email'] ?? '',
    'subject' => $_POST['subject'] ?? '',
    'message' => $_POST['message'] ?? ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validate required fields
    if (empty($form_data['name']) || empty($form_data['email']) || empty($form_data['subject']) || empty($form_data['message'])) {
        $submission_message = '<div class="alert alert-danger">Please fill in all required fields.</div>';
    } else {
        // Insert into ws_contact_form table
        $stmt = $mysqli->prepare("INSERT INTO ws_contact_form (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $form_data['name'], $form_data['email'], $form_data['subject'], $form_data['message']);
        if ($stmt->execute()) {
            $submission_message = '<div class="alert alert-success">Message sent successfully!</div>';
            // Clear form data on success
            $form_data = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
        } else {
            $submission_message = '<div class="alert alert-danger">Failed to send message: ' . $mysqli->error . '</div>';
            error_log('Contact form insert failed: ' . $mysqli->error);
        }
        $stmt->close();
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- page title -->
<div class="ttm-page-title-row">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-box text-center">
                    <div class="page-title-heading">
                        <h1>CONNECT</h1>
                    </div>
                    <div class="breadcrumb-wrapper">
                        <span><a title="Homepage" href="/home">Home</a></span>
                        <span class="ttm-bread-sep">&nbsp; / &nbsp;</span>
                        <span>Connect</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- page title -->

<!--site-main start-->
<div class="site-main">
    <!-- contact detail-section -->
    <section class="ttm-row contact-detail">
        <div class="container">
            <div class="contact-detail-wrapper ttm-bgcolor-grey">
                <div class="row justify-content-center align-items-center">
                    <div class="col-md-4 col-sm-6">
                        <div class="featured-icon-box icon-align-before-content style6">
                            <div class="featured-icon">
                                <div class="ttm-icon ttm-icon_element-color-skincolor ttm-icon_element-size-md">
                                    <i class="ti-location-pin"></i>
                                </div>
                            </div>
                            <div class="featured-content">
                                <div class="featured-title">
                                    <h5>Our Address</h5>
                                </div>
                                <div class="featured-desc">
                                    <p><?php echo htmlspecialchars($connect_data['address'] ?? ''); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="featured-icon-box icon-align-before-content style6">
                            <div class="featured-icon">
                                <div class="ttm-icon ttm-icon_element-color-skincolor ttm-icon_element-size-md">
                                    <i class="ti-email"></i>
                                </div>
                            </div>
                            <div class="featured-content">
                                <div class="featured-title">
                                    <h5>Our Email</h5>
                                </div>
                                <div class="featured-desc">
                                    <p><?php echo htmlspecialchars($connect_data['email'] ?? ''); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="featured-icon-box icon-align-before-content style6">
                            <div class="featured-icon">
                                <div class="ttm-icon ttm-icon_element-color-skincolor ttm-icon_element-size-md">
                                    <i class="fa fa-phone"></i>
                                </div>
                            </div>
                            <div class="featured-content">
                                <div class="featured-title">
                                    <h5>Our Phone Number</h5>
                                </div>
                                <div class="featured-desc">
                                    <p><?php echo htmlspecialchars($connect_data['phone1'] ?? ''); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-4 col-sm-6">
                        <div class="featured-icon-box icon-align-before-content style6">
                            <div class="featured-icon">
                                <div class="ttm-icon ttm-icon_element-color-skincolor ttm-icon_element-size-md">
                                    <i class="fa fa-clock-o"></i>
                                </div>
                            </div>
                            <div class="featured-content">
                                <div class="featured-title">
                                    <h5>Working Hours</h5>
                                </div>
                                <div class="featured-desc">
                                    <p><?php echo htmlspecialchars($connect_data['working_hours'] ?? ''); ?></p>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
                <div class="row">
                    <div class="col-12">
                        <!-- map-section -->
                        <div class="map-section mb_80 mt-50 res-767-mt-0 box-shadow clearfix">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!--map-start-->
                                        <div class="map-wrapper">
                                            <div id="map_canvas" style="height: 400px;"></div>
                                        </div>
                                        <!--map-end-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- map-section end -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- contact detail-section end -->
    <!-- contact-section -->
    <section class="ttm-row contact-section clearfix">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-9 res-1199-pt-50 m-auto">
                    <div class="section-title with-desc text-center clearfix">
                        <div class="title-header">
                            <h5 class="ttm-textcolor-skincolor">Connect</h5>
                            <h2 class="title">DO YOU HAVE ANY QUESTIONS?</h2>
                        </div>
                        <div class="title-desc">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text.</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <!-- contact form -->
                    <?php if ($submission_message): ?>
                        <?php echo $submission_message; ?>
                    <?php endif; ?>
                    <form id="ttm-quote-form" class="row ttm-quote-form clearfix" method="post" action="">
                        <div class="col-sm-6 col-md-6">
                            <div class="form-group">
                                <input name="name" type="text" class="form-control" placeholder="Name*" required="required" value="<?php echo htmlspecialchars($form_data['name']); ?>">
                            </div>
                            <div class="form-group">
                                <input name="email" type="email" placeholder="Email Address*" required="required" class="form-control" value="<?php echo htmlspecialchars($form_data['email']); ?>">
                            </div>
                            <div class="form-group">
                                <input name="subject" type="text" placeholder="Subject*" required="required" class="form-control" value="<?php echo htmlspecialchars($form_data['subject']); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="form-group">
                                <textarea name="message" rows="9" placeholder="Your Comment..." required="required" class="form-control"><?php echo htmlspecialchars($form_data['message']); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 pt-10">
                            <div class="text-center">
                                <button type="submit" name="submit" id="submit" class="ttm-btn ttm-btn-size-md ttm-textcolor-white ttm-btn-bgcolor-skincolor" value="submit">
                                    Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                    <!-- contact end-->
                </div>
            </div>
        </div>
    </section>
    <!-- contact-section end-->
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
<!-- Google Maps -->
<script src="https://maps.google.com/maps/api/js?sensor=false"></script>
<script>
    function initialize() {
        var latlng = new google.maps.LatLng(5.8357, -0.1236); // Coordinates for Aburi, Ghana
        var myOptions = {
            zoom: 12,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    }
    google.maps.event.addDomListener(window, "load", initialize);
</script>
<!-- Javascript end-->