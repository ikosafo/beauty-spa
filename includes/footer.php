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



<!--footer start-->
<footer class="footer widget-footer clearfix">
    <div class="first-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-7 col-sm-9 m-auto text-center">
                    <div class="footer-logo">
                        <img id="footer-logo-img" class="img-center" src="<?php echo htmlspecialchars($logo_path); ?>" alt="Golden View Therapeutic Clinique and Spa Logo">
                    </div>
                    <h4 class="widget-text ttm-textcolor-white">Stay Connected with Golden View</h4>
                    <form id="subscribe-form" class="newsletter-form" method="post" action="#" data-mailchimp="true">
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
                        <h3 class="widget-title">Recent News</h3>
                        <ul class="widget-post ttm-recent-post-list">
                            <li>
                                <a href="blog/enhancing-wellness-through-spa-treatments.html"><img src="images/blog/massage.jpg" class="lazyload" alt="Massage therapy"></a>
                                <span class="post-date">October 10, 2025</span>
                                <a href="blog/enhancing-wellness-through-spa-treatments.html">Enhancing Wellness Through Expert Spa Treatments</a>
                            </li>
                            <li>
                                <a href="blog/soothing-senses-with-facials.html"><img src="images/blog/facial.jpg" class="lazyload" alt="Facial treatment"></a>
                                <span class="post-date">October 8, 2025</span>
                                <a href="blog/soothing-senses-with-facials.html">Soothing Your Senses with Luxurious Facials</a>
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
                                        <p><?php echo htmlspecialchars($contact_data['address']); ?></p>
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
                                        <p><?php echo htmlspecialchars($contact_data['working_hours']); ?></p>
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

    <!-- Javascript end-->
</body>
</html>