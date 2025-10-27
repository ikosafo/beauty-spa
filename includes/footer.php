<?php
require_once 'config.php';

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

<style>
    .widget-services.ttm-service-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .widget-services.ttm-service-list > li {
        width: 48%; /* Two columns */
        margin: 0; /* No spacing */
        padding: 0;
    }
    .widget-services.ttm-service-list li a {
        color: #f7c08a; /* Softer gold for main services */
        font-weight: 500;
        text-decoration: none;
        font-size: 14px;
        line-height: 1.2; /* Tight line height */
        display: block;
    }
    .widget-services.ttm-service-list li a:hover {
        text-decoration: underline;
        color: #ffffff; /* White on hover */
    }
    .widget-services.ttm-service-list ul {
        list-style: none;
        padding-left: 10px;
        margin: 0; /* No spacing */
    }
    .widget-services.ttm-service-list ul li {
        margin: 8px !important; /* No spacing */
        padding: 0;
    }
    .widget-services.ttm-service-list ul li a {
        font-size: 13px; /* Slightly smaller for sub-services */
        color: #e0b670; /* Darker gold for sub-services */
        line-height: 1.2; /* Tight line height */
    }
</style>

<!--footer start-->
<footer class="footer widget-footer clearfix">
    <div class="first-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-7 col-sm-9 m-auto text-center">
                    <div class="footer-logo">
                        <img id="footer-logo-img" class="img-center" src="<?php echo '/cms/' . htmlspecialchars($logo_path); ?>" alt="Golden View Therapeutic Clinique and Spa Logo">
                    </div>
                    <h4 class="widget-text ttm-textcolor-white">Stay Connected with Golden View</h4>
                    <form id="subscribe-form" class="newsletter-form" method="post" action="subscribe.php" data-mailchimp="true">
                        <div class="mailchimp-inputbox clearfix" id="subscribe-content"> 
                            <p><input type="email" name="email" placeholder="Your Email Address..." required=""></p>
                            <p><button class="submit ttm-btn ttm-btn-size-md ttm-btn-shape-rounded ttm-btn-bgcolor-skincolor ttm-textcolor-white" style="background: #D4A017;" type="submit">Subscribe Now!</button></p>
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
                        <h3 class="widget-title">About Golden View</h3>
                        <div class="textwidget widget-text">
                            <p class="pb-10 res-767-p-0">Golden View Therapeutic Clinique and Spa has been a sanctuary for holistic wellness, with 7 global locations offering transformative treatments.</p>
                            <p class="pb-10 res-767-p-0">Our expert therapists use premium, natural products to rejuvenate your body and soul.</p>
                            <a class="ttm-color-skincolor" href="/about" title="">- Discover Our Story</a>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 widget-area">
                    <div class="widget widget_text clearfix">
                        <h3 class="widget-title">Our Services</h3>
                        <ul class="widget-services ttm-service-list">
                            <li>
                                <ul>
                                    <li><a href="/services#healing-therapeutic-massage">Healing Therapeutic Massage</a></li>
                                    <li><a href="/services#corporate-massage">Corporate Massage</a></li>
                                    <li><a href="/services#hydro-therapy-sauna">Hydro Therapy / Sauna</a></li>
                                    <li><a href="/services#body-exfoliating">Body Exfoliating</a></li>
                                    <li><a href="/services#natural-body-contouring">Natural Body Contouring</a></li>
                                </ul>
                            </li>
                            <li>
                                <ul>
                                    <li><a href="/services#facial-therapy-skin-tag-removal">Facial Therapy / Skin Tag Removal</a></li>
                                    <li><a href="/services#medical-feet-care">Medical Feet Care</a></li>
                                    <li><a href="/services#beauty-therapy">Beauty Therapy</a></li>
                                    <li><a href="/services#hair-dressing-braids-locks">Hair Dressing / Braids & Locks</a></li>
                                    <li><a href="/services#weave-on-hair-installation">Weave-On & Hair Installation</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-4 widget-area">
                    <div class="widget flicker_widget clearfix">
                        <h3 class="widget-title">Get In Touch</h3>
                        <div class="textwidget widget-text">
                            <div class="featured-icon-box icon-align-before-content icon-ver_align-top style3">
                                <div class="featured-icon">
                                    <div class="ttm-icon ttm-icon_element-onlytxt ttm-icon_element-color-skincolor ttm-icon_element-size-sm">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                </div>
                                <div class="featured-content">
                                    <div class="featured-desc">
                                        <p><?php echo htmlspecialchars($contact_data['address'] ?? ''); ?></p>
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
                                        <p><a href="mailto:info@goldenviewspa.com">info@goldenviewspa.com</a></p>
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
                                        <p>(+01) 987 654 3210</p>
                                    </div>
                                </div>
                            </div>
                            <div class="featured-icon-box icon-align-before-content icon-ver_align-top style3">
                                <div class="featured-icon">
                                    <div class="ttm-icon ttm-icon_element-onlytxt ttm-icon_element-color-skincolor ttm-icon_element-size-sm">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                </div>
                                <div class="featured-content">
                                    <div class="featured-desc">
                                        <p><?php echo htmlspecialchars($contact_data['working_hours'] ?? ''); ?></p>
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
                        <span>&copy; <?php echo date('Y') ?>&nbsp;<a class="ttm-textcolor-skincolor" href="#">Golden View Therapeutic Clinique and Spa</a>. All rights reserved.</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex flex-row align-items-center justify-content-end social-icons">
                        <ul class="social-icons list-inline">
                            <?php if (!empty($contact_data['facebook'])): ?>
                                <li><a href="<?php echo htmlspecialchars($contact_data['facebook']); ?>" class="tooltip-top" data-tooltip="Facebook" target="_blank"><i class="fa fa-facebook"></i></a></li>
                            <?php endif; ?>
                            <?php if (!empty($contact_data['x'])): ?>
                                <li><a href="<?php echo htmlspecialchars($contact_data['x']); ?>" class="tooltip-top" data-tooltip="X" target="_blank"><i class="fa fa-twitter"></i></a></li>
                            <?php endif; ?>
                            <?php if (!empty($contact_data['linkedin'])): ?>
                                <li><a href="<?php echo htmlspecialchars($contact_data['linkedin']); ?>" class="tooltip-top" data-tooltip="LinkedIn" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                            <?php endif; ?>
                            <?php if (!empty($contact_data['instagram'])): ?>
                                <li><a href="<?php echo htmlspecialchars($contact_data['instagram']); ?>" class="tooltip-top" data-tooltip="Instagram" target="_blank"><i class="fa fa-instagram"></i></a></li>
                            <?php endif; ?>
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

<!-- AJAX handler for subscription form -->
<script>
    $(document).ready(function() {
        $('#subscribe-form').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            var $form = $(this);
            var $msgDiv = $('#subscribe-msg');
            var email = $form.find('input[name="email"]').val();

            $.ajax({
                url: $form.attr('action'), // subscribe.php
                type: 'POST',
                data: { email: email },
                dataType: 'json',
                success: function(response) {
                    $msgDiv.html('<div class="alert alert-' + (response.success ? 'success' : 'danger') + '">' + response.message + '</div>');
                    if (response.success) {
                        $form.find('input[name="email"]').val(''); // Clear input on success
                    }
                },
                error: function() {
                    $msgDiv.html('<div class="alert alert-danger">An error occurred. Please try again later.</div>');
                }
            });
        });
    });
</script>
<!-- Javascript end-->

<style>
    .alert {
        padding: 10px;
        margin-top: 10px;
        border-radius: 4px;
        font-size: 14px;
        text-align: center;
    }
    .alert-success {
        background-color: #dff0d8;
        border-color: #d6e9c6;
        color: #3c763d;
    }
    .alert-danger {
        background-color: #f2dede;
        border-color: #ebccd1;
        color: #a94442;
    }
</style>