<?php
require_once 'config.php';

// ==================================================
// 1. FETCH ALL DATA FROM ws_services
// ==================================================
$section = null;
$boxes   = [];
$details = [];

$query = "
    SELECT 
        id, type, box_order, title, subtitle, description, link, image, benefits, icon
    FROM ws_services 
    ORDER BY 
        CASE 
            WHEN type = 'section' THEN 0
            WHEN type = 'box'     THEN 1
            ELSE 2 
        END,
        box_order ASC, id ASC
";

$result = $mysqli->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        switch ($row['type']) {
            case 'section':
                $section = $row;
                break;
            case 'box':
                $order = $row['box_order'] ?? 999;
                $boxes[$order] = $row;
                break;
            case 'detail':
                $row['benefits'] = $row['benefits'] ? json_decode($row['benefits'], true) : [];
                $details[$row['id']] = $row;
                break;
        }
    }
}

// ==================================================
// 2. FALLBACK DATA
// ==================================================
if (!$section) {
    $section = [
        'subtitle'    => 'Welcome to Golden View',
        'title'       => 'Explore Our Luxurious Services',
        'description' => 'Step into a world of wellness...'
    ];
}

if (empty($boxes)) {
    $boxes = [
        1 => ['image'=>'images/services/01.jpg','title'=>'Healing Therapeutic Massage','description'=>'Melt away stress...','icon'=>'flaticon-spa-1','link'=>'/services#healing-therapeutic-massage'],
        2 => ['image'=>'images/services/02.jpg','title'=>'Body Exfoliating','description'=>'Reveal radiant skin...','icon'=>'flaticon-herbal','link'=>'/services#body-exfoliating'],
        3 => ['image'=>'images/services/03.jpg','title'=>'Facial Therapy','description'=>'Restore a youthful glow...','icon'=>'flaticon-spa','link'=>'/services#facial-therapy-skin-tag-removal'],
        4 => ['image'=>'images/services/04.jpg','title'=>'Medical Feet Care','description'=>'Address foot concerns...','icon'=>'flaticon-cupping','link'=>'/services#medical-feet-care'],
    ];
}

// ==================================================
// 3. HELPER: Build Full Image URL
// ==================================================
function imgUrl($path) {
    if (!$path) return '';
    if (preg_match('#^https?://#i', $path)) return $path;
    return rtrim(URLROOT, '/') . '/cms/' . ltrim($path, '/');
}

// ==================================================
// 4. Active Fragment
// ==================================================
$currentFragment = '';
if (isset($_SERVER['REQUEST_URI'])) {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $currentFragment = $url['fragment'] ?? '';
}

include 'includes/header.php';
?>

<!-- PAGE TITLE -->
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

<div class="site-main">

    <!-- CAROUSEL -->
    <section class="ttm-row service-section ttm-bgcolor-grey clearfix">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-10 col-sm-12 m-auto">
                    <div class="section-title with-desc text-center clearfix">
                        <div class="title-header">
                            <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($section['subtitle'] ?? ''); ?></h5>
                            <h2 class="title"><?php echo htmlspecialchars($section['title'] ?? ''); ?></h2>
                        </div>
                        <div class="title-desc"><?php echo nl2br(htmlspecialchars($section['description'] ?? '')); ?></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="services-slide owl-carousel" data-item="4" data-nav="true" data-dots="false" data-auto="true">
                    <?php ksort($boxes); foreach ($boxes as $box): ?>
                        <div class="featured-imagebox featured-imagebox-services text-center style1">
                            <div class="ttm-post-thumbnail featured-thumbnail">
                                <img class="img-fluid lazyload" src="<?php echo imgUrl($box['image']); ?>" alt="<?php echo htmlspecialchars($box['title']); ?>" style="height: 252px;width:245px; object-fit: cover;">
                                <div class="featured-icon">
                                    <div class="ttm-icon ttm-icon_element-fill ttm-icon_element-color-skincolor ttm-icon_element-size-md ttm-icon_element-style-rounded">
                                        <i class="<?php echo htmlspecialchars($box['icon'] ?? ''); ?>"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="featured-content box-shadow">
                                <div class="featured-title">
                                    <h5><a href="<?php echo htmlspecialchars($box['link'] ?? '#'); ?>"><?php echo htmlspecialchars($box['title']); ?></a></h5>
                                </div>
                                <div class="featured-desc"><p><?php echo htmlspecialchars($box['description']); ?></p></div>
                                <!-- <a class="ttm-btn ttm-btn-size-sm ttm-btn-style-border ttm-btn-color-skincolor mt-15" href="<?php echo htmlspecialchars($box['link'] ?? '#'); ?>">Learn More</a> -->
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- DETAILED + SIDEBAR -->
    <div class="ttm-row sidebar service-detail ttm-bgcolor-white clearfix">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 content-area order-lg-2">
                    <?php foreach ($details as $svc): 
                        $fragment = $svc['link'] ? parse_url($svc['link'], PHP_URL_FRAGMENT) : '';
                        $fragment = ltrim($fragment, '#');
                    ?>
                        <section class="ttm-row service-category-section mb-50 clearfix" id="<?php echo htmlspecialchars($fragment ?: 'service-' . $svc['id']); ?>">
                            <div class="ttm-service-single-content-area">
                                <div class="ttm_single_image-wrapper mb-30 res-767-mb-20">
                                    <img class="img-fluid lazyload" src="<?php echo imgUrl($svc['image']); ?>" alt="<?php echo htmlspecialchars($svc['title']); ?>" style="height: 400px; width: 100%; object-fit: cover;">
                                </div>
                                <div class="ttm-service-description p-30">
                                    <h3 class="mb-20"><?php echo htmlspecialchars($svc['title']); ?></h3>
                                    <p class="mb-20"><?php echo nl2br(htmlspecialchars($svc['description'])); ?></p>
                                    <?php if (!empty($svc['benefits'])): ?>
                                        <div class="ttm-service-benefits mb-30">
                                            <h5 class="mb-15">Benefits</h5>
                                            <ul class="ttm-list ttm-list-style-icon ttm-list-icon-color-skincolor style3">
                                                <?php foreach ($svc['benefits'] as $b): ?>
                                                    <li><i class="fa fa-check-circle"></i><span class="ttm-list-li-content"><?php echo htmlspecialchars($b); ?></span></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    <a class="ttm-btn ttm-btn-size-md ttm-btn-style-fill ttm-btn-color-skincolor mt-20" href="/appointment">Book Now</a>
                                </div>
                            </div>
                        </section>
                    <?php endforeach; ?>
                </div>

                <div class="col-lg-3 widget-area">
                    <aside class="widget widget-nav-menu ttm-bgcolor-grey mb-30">
                        <h3 class="widget-title" style="padding:10px">Our Services</h3>
                        <ul class="widget-menu">
                            <?php foreach ($details as $svc):
                                $link = $svc['link'] ?? '#';
                                $frag = parse_url($link, PHP_URL_FRAGMENT) ?? '';
                                $active = ($currentFragment && $currentFragment === ltrim($frag, '#'));
                            ?>
                                <li><a href="<?php echo htmlspecialchars($link); ?>" class="<?php echo $active ? 'active' : ''; ?>"><?php echo htmlspecialchars($svc['title']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </aside>
                    <!-- <aside class="widget widget-download mb-30">
                        <ul class="download">
                            <li>
                                <span class="ttm-fileicon position-relative ttm-textcolor-skincolor"><i class="fa fa-file-pdf-o"></i></span>
                                <div class="ttm-fielcontent ml-20">
                                    <h5 class="mb-0">Services Brochure</h5>
                                    <a href="/downloads/services-brochure.pdf">Download.pdf</a>
                                </div>
                            </li>
                        </ul>
                    </aside> -->
                    <!-- <aside class="widget widget_media_image res-991-text-center">
                        <div class="banner-img-box">
                            <img class="img-fluid lazyload" src="<?php echo imgUrl('images/sidebar-right-banner1.jpg'); ?>" alt="sidebar">
                        </div>
                    </aside> -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>