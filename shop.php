<?php
require_once 'config.php'; // <-- ONLY ONE INCLUDE

/* ==============================================================
   FETCH SHOP DATA
   ============================================================== */
$shop_section = [];
$products = [];

$query = "SELECT * FROM ws_shop WHERE id IN (1, 2, 3, 4, 5) ORDER BY id";
$result = $mysqli->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $shop_section = $row;
        } else {
            $products[$row['product_order']] = $row;
        }
    }
}

// Default fallback data
if (empty($shop_section)) {
    $shop_section = [
        'image' => 'images/shop/shop-bg.jpg',
        'subtitle' => 'Discover Our Products',
        'title' => 'Golden View Therapeutic Clinik and Spa',
        'description' => 'Explore our curated selection of premium beauty and spa products.'
    ];
}
if (empty($products)) {
    $products = [
        1 => ['id' => 2, 'product_name' => 'Hydrating Face Cream', 'product_description' => 'Nourish your skin with our rich, organic face cream.', 'product_price' => 29.99, 'product_image' => 'images/shop/product1.jpg', 'product_order' => 1],
        2 => ['id' => 3, 'product_name' => 'Aromatherapy Oil Set', 'product_description' => 'Relax with our blend of essential oils.', 'product_price' => 39.99, 'product_image' => 'images/shop/product2.jpg', 'product_order' => 2],
        3 => ['id' => 4, 'product_name' => 'Hair Repair Serum', 'product_description' => 'Restore shine and strength to your hair.', 'product_price' => 24.99, 'product_image' => 'images/shop/product3.jpg', 'product_order' => 3],
        4 => ['id' => 5, 'product_name' => 'Spa Bath Kit', 'product_description' => 'Indulge in a luxurious spa experience at home.', 'product_price' => 49.99, 'product_image' => 'images/shop/product4.jpg', 'product_order' => 4]
    ];
}

/* ==============================================================
   HANDLE CHECKOUT
   ============================================================== */
$order_success = false;
$customer_info = [];
$cart_items = [];
$total_amount = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $customer_info = [
        'name'  => trim($_POST['customer_name'] ?? ''),
        'email' => trim($_POST['customer_email'] ?? ''),
        'phone' => trim($_POST['customer_phone'] ?? '')
    ];
    $cart_items = json_decode($_POST['cart_items'] ?? '[]', true) ?? [];

    if (empty($customer_info['name']) || empty($customer_info['email']) || empty($customer_info['phone']) || empty($cart_items)) {
        $order_success = false;
    } else {
        $stmt = $mysqli->prepare("INSERT INTO ws_orders (customer_name, customer_email, customer_phone, cart_items) VALUES (?, ?, ?, ?)");
        $cart_json = json_encode($cart_items);
        $stmt->bind_param("ssss", $customer_info['name'], $customer_info['email'], $customer_info['phone'], $cart_json);

        if ($stmt->execute()) {
            $order_id = $mysqli->insert_id;
            $order_success = true;

            // Calculate total
            foreach ($cart_items as $item) {
                $total_amount += $item['price'] * $item['quantity'];
            }

            // Build items list
            $items_list = "";
            foreach ($cart_items as $item) {
                $line_total = $item['price'] * $item['quantity'];
                $items_list .= "\n• {$item['name']} (x{$item['quantity']}) = GHS " . number_format($line_total, 2);
            }

            // === 1. ADMIN SMS ===
            $admin_sms = "NEW ORDER #{$order_id}\n" .
                         "Customer: {$customer_info['name']}\n" .
                         "Phone: {$customer_info['phone']}\n" .
                         "Email: {$customer_info['email']}\n" .
                         "Items:{$items_list}\n" .
                         "TOTAL: GHS " . number_format($total_amount, 2) . "\n" .
                         "Time: " . date('M j, Y g:i A') . "\n" .
                         "Golden View Therapeutic Clinik and Spa";

            global $admin_phone;
            sendSMSMessage($admin_phone, $admin_sms, 'GoldenView');

            // === 2. CUSTOMER SMS ===
            $customer_sms = "Thank you, {$customer_info['name']}!\n" .
                            "Order #{$order_id} confirmed.\n" .
                            "Total: GHS " . number_format($total_amount, 2) . "\n" .
                            "We will contact you soon.\n" .
                            "Golden View Therapeutic Clinik and Spa";

            sendSMSMessage($customer_info['phone'], $customer_sms, 'GoldenView');
        } else {
            error_log("Order insert failed: " . $mysqli->error);
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<!-- Page Title -->
<div class="ttm-page-title-row">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-box text-center">
                    <div class="page-title-heading">
                        <h1><?php echo htmlspecialchars($shop_section['title']); ?></h1>
                    </div>
                    <div class="breadcrumb-wrapper">
                        <span><a href="/">Home</a></span>
                        <span class="ttm-bread-sep">&nbsp; / &nbsp;</span>
                        <span>Shop</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="site-main">
    <section class="ttm-row shop-section ttm-bgcolor-grey clearfix">
        <div class="container">
            <!-- Cart Notification -->
            <div id="cart-notification" class="alert alert-success alert-dismissible" style="display:none; position:fixed; bottom:20px; right:20px; z-index:1000; min-width:300px;">
                <span id="cart-message"></span>
                <button type="button" class="close" data-dismiss="alert">×</button>
            </div>

            <!-- Success Message -->
            <?php if ($order_success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <h5>Order Placed Successfully!</h5>
                    <p>Thank you, <strong><?php echo htmlspecialchars($customer_info['name']); ?></strong>!</p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($customer_info['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer_info['phone']); ?></p>
                    <p><strong>Items:</strong></p>
                    <ul>
                        <?php foreach ($cart_items as $item): 
                            $item_total = $item['price'] * $item['quantity'];
                        ?>
                            <li><?php echo htmlspecialchars($item['name']); ?> (GHS <?php echo number_format($item['price'], 2); ?> × <?php echo $item['quantity']; ?> = GHS <?php echo number_format($item_total, 2); ?>)</li>
                        <?php endforeach; ?>
                        <li><strong>Total: GHS <?php echo number_format($total_amount, 2); ?></strong></li>
                    </ul>
                    <p><strong>SMS sent to you and admin.</strong></p>
                    <button type="button" class="close" data-dismiss="alert">×</button>
                </div>
            <?php endif; ?>

            <!-- Shop Header -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title with-desc text-center clearfix mt-50">
                        <div class="title-header">
                            <h5 class="ttm-textcolor-skincolor"><?php echo htmlspecialchars($shop_section['subtitle']); ?></h5>
                            <h2 class="title"><?php echo htmlspecialchars($shop_section['title']); ?></h2>
                        </div>
                        <div class="title-desc"><?php echo htmlspecialchars($shop_section['description']); ?></div>
                    </div>
                </div>
            </div>

            <!-- Image + Description -->
            <div class="row">
                <div class="col-md-6">
                    <div class="ttm_single_image-wrapper mb-30 res-767-mb-20">
                        <img class="img-fluid lazyload" src="<?php echo URLROOT . '/cms/' . htmlspecialchars($shop_section['image']); ?>" alt="shop">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="ttm-service-description p-30">
                        <h3>Our Featured Products</h3>
                        <p>Discover our range of premium beauty and spa products.</p>
                        <a class="ttm-btn ttm-btn-size-md ttm-btn-style-fill ttm-btn-color-skincolor mt-20" href="#explore-collection">Shop Now!</a>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row mt-50" id="explore-collection">
                <div class="col-lg-12">
                    <div class="section-title text-center"><h3>Explore Our Collection</h3></div>
                    <p class="text-center">Browse our curated selection of high-quality products.</p>
                    <div class="row">
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-3 col-sm-6">
                                <div class="featured-imagebox featured-imagebox-product style1 text-center">
                                    <div class="featured-thumbnail">
                                        <img class="img-fluid lazyload" src="<?php echo URLROOT . '/cms/' . htmlspecialchars($product['product_image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                    </div>
                                    <div class="featured-content box-shadow">
                                        <div class="featured-title"><h5><?php echo htmlspecialchars($product['product_name']); ?></h5></div>
                                        <div class="featured-desc"><p><?php echo htmlspecialchars($product['product_description']); ?></p></div>
                                        <div class="featured-price"><p>GHS <?php echo number_format($product['product_price'], 2); ?></p></div>
                                        <form class="add-to-cart-form" data-product-id="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>">
                                            <input type="hidden" name="product_price" value="<?php echo $product['product_price']; ?>">
                                            <div class="input-group mb-15">
                                                <input type="number" name="quantity" class="form-control quantity-input" value="1" min="1">
                                                <button type="submit" class="ttm-btn ttm-btn-size-sm ttm-btn-style-fill ttm-btn-color-skincolor add-to-cart-btn">Add to Cart</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-30" id="checkout-button" style="display:none;">
                        <button class="ttm-btn ttm-btn-size-md ttm-btn-style-fill ttm-btn-color-skincolor" id="toggle-checkout">Proceed to Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Checkout Section -->
    <section class="ttm-row checkout-section ttm-bgcolor-white clearfix" id="checkout-details" style="display:none;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title text-center mb-30"><h2>Final Checkout Details</h2></div>
                    <div class="row">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="checkout-box p-30 border rounded bg-light">
                                <h4 class="mb-3">Cart Summary</h4>
                                <ul class="list-unstyled" id="cart-summary"></ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkout-box p-30 border rounded bg-white">
                                <h4 class="mb-3">Customer Information</h4>
                                <form id="checkout-form" method="POST">
                                    <div class="form-group">
                                        <label for="customer_name">Full Name *</label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="customer_email">Email Address *</label>
                                        <input type="email" class="form-control" id="customer_email" name="customer_email">
                                    </div>
                                    <div class="form-group">
                                        <label for="customer_phone">Phone Number *</label>
                                        <input type="tel" class="form-control" id="customer_phone" name="customer_phone" placeholder="e.g. 0241234567" required>
                                    </div>
                                    <input type="hidden" name="cart_items" id="cart-items">
                                    <button type="submit" name="checkout" class="ttm-btn ttm-btn-size-md ttm-btn-style-fill ttm-btn-color-skincolor mt-3">Place Order</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function getCart() { return JSON.parse(localStorage.getItem('cart')) || {}; }
    function saveCart(cart) { localStorage.setItem('cart', JSON.stringify(cart)); updateCheckoutButton(); updateCartSummary(); }

    function updateCheckoutButton() {
        const cart = getCart();
        document.getElementById('checkout-button').style.display = Object.keys(cart).length > 0 ? 'block' : 'none';
    }

    function updateCartSummary() {
        const cart = getCart();
        const summary = document.getElementById('cart-summary');
        const input = document.getElementById('cart-items');
        summary.innerHTML = '';
        let subtotal = 0;
        const items = [];

        if (Object.keys(cart).length === 0) {
            summary.innerHTML = '<li>Your cart is empty.</li>';
        } else {
            for (const id in cart) {
                const item = cart[id];
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                const li = document.createElement('li');
                li.className = 'd-flex justify-content-between py-1 border-bottom';
                li.innerHTML = `<span>${item.name} (x${item.quantity})</span><span>GHS ${itemTotal.toFixed(2)}</span>`;
                summary.appendChild(li);
                items.push(item);
            }
            const totalLi = document.createElement('li');
            totalLi.className = 'd-flex justify-content-between py-2 mt-2';
            totalLi.innerHTML = `<strong>Total:</strong><strong>GHS ${subtotal.toFixed(2)}</strong>`;
            summary.appendChild(totalLi);
        }
        input.value = JSON.stringify(items);
    }

    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = this.dataset.productId;
            const name = this.querySelector('[name="product_name"]').value;
            const price = parseFloat(this.querySelector('[name="product_price"]').value);
            const qty = parseInt(this.querySelector('[name="quantity"]').value) || 1;
            const cart = getCart();
            cart[id] ? cart[id].quantity += qty : cart[id] = {id, name, price, quantity: qty};
            saveCart(cart);
            const msg = document.getElementById('cart-message');
            msg.textContent = `Added ${name} (x${qty}) to cart!`;
            const notif = document.getElementById('cart-notification');
            notif.style.display = 'block';
            setTimeout(() => notif.style.display = 'none', 3000);
        });
    });

    document.getElementById('toggle-checkout').addEventListener('click', () => {
        const section = document.getElementById('checkout-details');
        section.style.display = section.style.display === 'none' ? 'block' : 'none';
        if (section.style.display === 'block') {
            section.scrollIntoView({ behavior: 'smooth' });
            document.getElementById('customer_name').focus();
        }
    });

    document.getElementById('checkout-form').addEventListener('submit', () => {
        localStorage.removeItem('cart');
    });

    document.addEventListener('DOMContentLoaded', () => {
        updateCheckoutButton();
        updateCartSummary();
    });
</script>

<?php include 'includes/footer.php'; ?>