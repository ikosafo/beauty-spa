<?php
require_once 'config.php';

// Fetch shop data
$shop_section = [];
$products = [];
$query = "SELECT * FROM ws_shop WHERE id IN (1, 2, 3, 4, 5) ORDER BY id";
$result = $mysqli->query($query);
if (!$result) {
    error_log('Fetch query failed: ' . $mysqli->error);
} else {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $shop_section = $row;
        } else {
            $products[$row['product_order']] = $row;
        }
    }
}
if (empty($shop_section)) {
    $shop_section = [
        'image' => 'images/shop/shop-bg.jpg',
        'subtitle' => 'Discover Our Products',
        'title' => 'Sylin Beauty Shop',
        'description' => 'Explore our curated selection of premium beauty and spa products, designed to enhance your wellness routine.'
    ];
}
if (empty($products)) {
    $products = [
        1 => [
            'product_name' => 'Hydrating Face Cream',
            'product_description' => 'Nourish your skin with our rich, organic face cream.',
            'product_price' => 29.99,
            'product_image' => 'images/shop/product1.jpg',
            'product_order' => 1
        ],
        2 => [
            'product_name' => 'Aromatherapy Oil Set',
            'product_description' => 'Relax with our blend of essential oils.',
            'product_price' => 39.99,
            'product_image' => 'images/shop/product2.jpg',
            'product_order' => 2
        ],
        3 => [
            'product_name' => 'Hair Repair Serum',
            'product_description' => 'Restore shine and strength to your hair.',
            'product_price' => 24.99,
            'product_image' => 'images/shop/product3.jpg',
            'product_order' => 3
        ],
        4 => [
            'product_name' => 'Spa Bath Kit',
            'product_description' => 'Indulge in a luxurious spa experience at home.',
            'product_price' => 49.99,
            'product_image' => 'images/shop/product4.jpg',
            'product_order' => 4
        ]
    ];
}
error_log('Fetched shop data: ' . print_r(['shop_section' => $shop_section, 'products' => $products], true));

include 'includes/header.php';
?>

<!-- page title -->
<div class="ttm-page-title-row">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-box text-center">
                    <div class="page-title-heading">
                        <h1><?php echo htmlspecialchars($shop_section['title']); ?></h1>
                    </div>
                    <div class="breadcrumb-wrapper">
                        <span><a title="Homepage" href="/">Home</a></span>
                        <span class="ttm-bread-sep">&nbsp; / &nbsp;</span>
                        <span>Shop</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- page title end -->

<!--site-main start-->
<div class="site-main">
    <!-- shop-section -->
    <section class="ttm-row shop-section clearfix">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title with-desc text-center clearfix">
                        <div class="title-header">
                            <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($shop_section['subtitle']); ?></h5>
                            <h2 class="title"><?php echo htmlspecialchars($shop_section['title']); ?></h2>
                        </div>
                        <div class="title-desc"><?php echo htmlspecialchars($shop_section['description']); ?></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <img src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $shop_section['image']); ?>" class="img-fluid lazyload" alt="shop-image">
                </div>
                <div class="col-md-6">
                    <h3>Our Featured Products</h3>
                    <p>Discover our range of premium beauty and spa products, crafted to elevate your self-care routine. Shop now to bring the spa experience home.</p>
                    <a class="ttm-btn ttm-btn-size-md ttm-btn-style-fill ttm-btn-color-skincolor mt-20" href="/cart">Shop Now!</a>
                </div>
            </div>
            <div class="row mt-50">
                <div class="col-lg-12">
                    <h3>Explore Our Collection</h3>
                    <p>Browse our curated selection of high-quality products, designed to nourish your skin, hair, and senses.</p>
                    <div class="row">
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-3 col-sm-6">
                                <div class="featured-imagebox featured-imagebox-product style1 text-center">
                                    <div class="featured-thumbnail">
                                        <img class="img-fluid lazyload" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $product['product_image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                    </div>
                                    <div class="featured-content box-shadow">
                                        <div class="featured-title">
                                            <h5><?php echo htmlspecialchars($product['product_name']); ?></h5>
                                        </div>
                                        <div class="featured-desc">
                                            <p><?php echo htmlspecialchars($product['product_description']); ?></p>
                                        </div>
                                        <div class="featured-price">
                                            <p>GHS <?php echo number_format($product['product_price'], 2); ?></p>
                                        </div>
                                        <a class="ttm-btn ttm-btn-size-sm ttm-btn-style-border ttm-btn-color-skincolor mt-15" href="/cart?product_id=<?php echo $product['id']; ?>">Add to Cart</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- shop-section end -->
</div>
<!--site-main end-->

<?php include 'includes/footer.php'; ?>

