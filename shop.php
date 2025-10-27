<?php

require_once 'config.php';

// Fetch shop data
$shop_section = [];
$products = [];

// NOTE: Assuming $mysqli is correctly initialized in config.php
$query = "SELECT * FROM ws_shop WHERE id IN (1, 2, 3, 4, 5) ORDER BY id";
$result = $mysqli->query($query); 
if (!$result) {
    // Log database fetch error
    error_log('Fetch query failed: ' . $mysqli->error);
} else {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            // Found the shop section metadata
            $shop_section = $row;
        } else {
            // Found a product
            // Using product_order as the array key for ordered display later
            $products[$row['product_order']] = $row;
        }
    }
}

// Default data if database fetch fails or is empty
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
            'id' => 2,
            'product_name' => 'Hydrating Face Cream',
            'product_description' => 'Nourish your skin with our rich, organic face cream.',
            'product_price' => 29.99,
            'product_image' => 'images/shop/product1.jpg',
            'product_order' => 1
        ],
        2 => [
            'id' => 3,
            'product_name' => 'Aromatherapy Oil Set',
            'product_description' => 'Relax with our blend of essential oils.',
            'product_price' => 39.99,
            'product_image' => 'images/shop/product2.jpg',
            'product_order' => 2
        ],
        3 => [
            'id' => 4,
            'product_name' => 'Hair Repair Serum',
            'product_description' => 'Restore shine and strength to your hair.',
            'product_price' => 24.99,
            'product_image' => 'images/shop/product3.jpg',
            'product_order' => 3
        ],
        4 => [
            'id' => 5,
            'product_name' => 'Spa Bath Kit',
            'product_description' => 'Indulge in a luxurious spa experience at home.',
            'product_price' => 49.99,
            'product_image' => 'images/shop/product4.jpg',
            'product_order' => 4
        ]
    ];
}

// Log fetched data for debugging
error_log('Fetched shop data: ' . print_r(['shop_section' => $shop_section, 'products' => $products], true));

// Handle Checkout Form Submission
$order_success = false;
$customer_info = [];
$cart_items = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $customer_info = [
        'name' => $_POST['customer_name'] ?? '',
        'email' => $_POST['customer_email'] ?? '',
        'phone' => $_POST['customer_phone'] ?? ''
    ];
    $cart_items = json_decode($_POST['cart_items'], true) ?? [];
    
    // Insert order into database using prepared statements for security
    $stmt = $mysqli->prepare("INSERT INTO ws_orders (customer_name, customer_email, customer_phone, cart_items) VALUES (?, ?, ?, ?)");
    $cart_items_json = json_encode($cart_items);
    
    // 'ssss' means four string parameters
    $stmt->bind_param("ssss", $customer_info['name'], $customer_info['email'], $customer_info['phone'], $cart_items_json);
    
    if ($stmt->execute()) {
        $order_success = true;
    } else {
        error_log('Order insert failed: ' . $mysqli->error);
    }
    $stmt->close();
}

// NOTE: URLROOT and includes/header.php must be defined/exist in your environment
include 'includes/header.php'; 
?>

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
<div class="site-main">
    <section class="ttm-row shop-section ttm-bgcolor-grey clearfix">
        <div class="container">
            <div id="cart-notification" class="alert alert-success alert-dismissible" role="alert" style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 1000; min-width: 300px;">
                <span id="cart-message"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?php if ($order_success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <h5>Order Successfully Placed! 🥳</h5>
                    <p>Thank you, **<?php echo htmlspecialchars($customer_info['name']); ?>**! Your order has been received and will be processed soon.</p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($customer_info['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer_info['phone']); ?></p>
                    <p><strong>Ordered Products:</strong></p>
                    <ul>
                        <?php 
                        $total_amount = 0;
                        foreach ($cart_items as $item): 
                            $item_total = $item['price'] * $item['quantity'];
                            $total_amount += $item_total;
                        ?>
                            <li><?php echo htmlspecialchars($item['name']); ?> (GHS <?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?> = GHS <?php echo number_format($item_total, 2); ?>)</li>
                        <?php endforeach; ?>
                        <li>**Total: GHS <?php echo number_format($total_amount, 2); ?>**</li>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

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
            
            <div class="row">
                <div class="col-md-6">
                    <div class="ttm_single_image-wrapper mb-30 res-767-mb-20">
                        <img class="img-fluid lazyload" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $shop_section['image']); ?>" alt="shop-image">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="ttm-service-description p-30">
                        <h3>Our Featured Products</h3>
                        <p>Discover our range of premium beauty and spa products, crafted to elevate your self-care routine. Shop now to bring the spa experience home.</p>
                        <a class="ttm-btn ttm-btn-size-md ttm-btn-style-fill ttm-btn-color-skincolor mt-20" href="#explore-collection">Shop Now!</a>
                    </div>
                </div>
            </div>
            
            <div class="row mt-50" id="explore-collection">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <h3>Explore Our Collection</h3>
                    </div>
                    <p class="text-center">Browse our curated selection of high-quality products, designed to nourish your skin, hair, and senses.</p>
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
                    <div class="text-center mt-30" id="checkout-button" style="display: none;">
                        <button class="ttm-btn ttm-btn-size-md ttm-btn-style-fill ttm-btn-color-skincolor" id="toggle-checkout">Proceed to Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ttm-row checkout-section ttm-bgcolor-white clearfix" id="checkout-details" style="display: none;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title text-center mb-30">
                        <h2>Final Checkout Details</h2>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="checkout-box p-30 border rounded bg-light">
                                <h4 class="mb-3">Cart Summary</h4>
                                <ul class="list-unstyled" id="cart-summary">
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkout-box p-30 border rounded bg-white">
                                <h4 class="mb-3">Customer Information</h4>
                                <form id="checkout-form" method="POST" action="">
                                    <div class="form-group">
                                        <label for="customer_name">Full Name *</label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="customer_email">Email Address *</label>
                                        <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="customer_phone">Phone Number *</label>
                                        <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required>
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Cart Management with Local Storage
    function getCart() {
        return JSON.parse(localStorage.getItem('cart')) || {};
    }

    function saveCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCheckoutButton();
        updateCartSummary();
    }

    function updateCheckoutButton() {
        const cart = getCart();
        const checkoutButtonContainer = document.getElementById('checkout-button');
        const checkoutDetailsSection = document.getElementById('checkout-details');
        const hasItems = Object.keys(cart).length > 0;

        // Show/Hide "Proceed to Checkout" button
        checkoutButtonContainer.style.display = hasItems ? 'block' : 'none';

        // Automatically hide the checkout form if the cart becomes empty
        if (!hasItems) {
            checkoutDetailsSection.style.display = 'none';
        }
    }

    function updateCartSummary() {
        const cart = getCart();
        const summary = document.getElementById('cart-summary');
        const cartItemsInput = document.getElementById('cart-items');
        summary.innerHTML = '';
        
        let subtotal = 0;
        let cartItemsArray = [];

        if (Object.keys(cart).length === 0) {
            summary.innerHTML = '<li>Your cart is empty. Please add products to place an order.</li>';
        } else {
            for (const id in cart) {
                const item = cart[id];
                const itemTotal = parseFloat(item.price) * parseInt(item.quantity); // Calculate total for this item
                subtotal += itemTotal;

                const li = document.createElement('li');
                li.className = 'd-flex justify-content-between py-1 border-bottom'; 
                li.innerHTML = `<span>${item.name} (x${item.quantity})</span><span>GHS ${itemTotal.toFixed(2)}</span>`; // Show total price
                summary.appendChild(li);

                cartItemsArray.push(item); // Prepare data for PHP submission
            }

            // Add total to summary
            const totalLi = document.createElement('li');
            totalLi.className = 'd-flex justify-content-between py-2 mt-2';
            totalLi.innerHTML = `<strong>Total:</strong><strong>GHS ${subtotal.toFixed(2)}</strong>`;
            summary.appendChild(totalLi);
        }

        // Populate the hidden input field for PHP submission
        cartItemsInput.value = JSON.stringify(cartItemsArray);
    }

    // Handle Add to Cart
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const productName = this.querySelector('input[name="product_name"]').value;
            const productPrice = parseFloat(this.querySelector('input[name="product_price"]').value);
            const quantity = parseInt(this.querySelector('input[name="quantity"]').value) || 1;

            const cart = getCart();
            if (cart[productId]) {
                cart[productId].quantity += quantity;
            } else {
                cart[productId] = {
                    id: productId, // Include ID for clarity
                    name: productName,
                    price: productPrice,
                    quantity: quantity
                };
            }
            saveCart(cart);

            // Show notification
            const notification = document.getElementById('cart-notification');
            const message = document.getElementById('cart-message');
            message.textContent = `Added ${productName} (x${quantity}) to cart!`;
            notification.style.display = 'block';
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => { notification.style.display = 'none'; }, 200);
            }, 3000);
        });
    });

    // Toggle visibility of the new checkout section
    document.getElementById('toggle-checkout').addEventListener('click', function() {
        const checkoutDetailsSection = document.getElementById('checkout-details');
        const isVisible = checkoutDetailsSection.style.display !== 'none';
        
        if (isVisible) {
            checkoutDetailsSection.style.display = 'none';
        } else {
            checkoutDetailsSection.style.display = 'block';
            // Scroll to the checkout section for better UX
            checkoutDetailsSection.scrollIntoView({ behavior: 'smooth', block: 'start' }); 
            // Focus on the first form field
            document.getElementById('customer_name').focus();
        }
    });

    // Clear cart on successful checkout
    document.getElementById('checkout-form').addEventListener('submit', function() {
        // Since the PHP handles the order, we clear the client-side cart after submission.
        localStorage.removeItem('cart');
        // The page will reload after POST, so the updates below are technically redundant 
        // but included for completeness in case of AJAX submission.
        // updateCheckoutButton(); 
        // updateCartSummary(); 
    });

    // Initialize cart state on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateCheckoutButton();
        updateCartSummary();
    });
</script>

<style>
    /* ... (CSS from original code) ... */

    /* NEW STYLES FOR DEDICATED CHECKOUT SECTION */
    .checkout-section {
        padding: 40px 0;
        border-top: 1px solid #eee;
    }

    .checkout-box {
        background-color: #ffffff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    
    .checkout-box h4 {
        color: #D4A017;
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    /* Update cart summary list style in the new box */
    #cart-summary {
        padding-left: 0;
    }
    #cart-summary li {
        padding: 5px 0;
        font-size: 15px;
        color: #555;
    }
    #cart-summary li strong {
        font-size: 16px;
        color: #333;
    }

    /* Style improvements for form controls */
    .form-group label {
        font-weight: 500;
        color: #333;
    }
    

    /* Retain and update existing styles as needed */
    .shop-section .ttm-btn:hover {
        transform: translateY(-2px);
        background-color: #b88c14; /* Darker shade of #D4A017 */
    }
    .featured-imagebox-product .add-to-cart-btn:hover {
        background-color: #b88c14;
        transform: translateY(-2px);
    }
</style>

<?php 
include 'includes/footer.php'; 
?>