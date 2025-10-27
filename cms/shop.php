<?php
// cms/shop.php

require_once '../config.php';

$page_title = 'Edit Shop Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch shop data
$shop_section = [];
$products = [];
$query = "SELECT * FROM ws_shop WHERE id IN (1, 2, 3, 4, 5) ORDER BY id";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
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
        'id' => 1,
        'type' => 'section',
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
            'type' => 'product',
            'product_name' => 'Hydrating Face Cream',
            'product_description' => 'Nourish your skin with our rich, organic face cream.',
            'product_price' => 29.99,
            'product_image' => 'images/shop/product1.jpg',
            'product_order' => 1
        ],
        2 => [
            'id' => 3,
            'type' => 'product',
            'product_name' => 'Aromatherapy Oil Set',
            'product_description' => 'Relax with our blend of essential oils.',
            'product_price' => 39.99,
            'product_image' => 'images/shop/product2.jpg',
            'product_order' => 2
        ],
        3 => [
            'id' => 4,
            'type' => 'product',
            'product_name' => 'Hair Repair Serum',
            'product_description' => 'Restore shine and strength to your hair.',
            'product_price' => 24.99,
            'product_image' => 'images/shop/product3.jpg',
            'product_order' => 3
        ],
        4 => [
            'id' => 5,
            'type' => 'product',
            'product_name' => 'Spa Bath Kit',
            'product_description' => 'Indulge in a luxurious spa experience at home.',
            'product_price' => 49.99,
            'product_image' => 'images/shop/product4.jpg',
            'product_order' => 4
        ]
    ];
}
error_log('Fetched shop data: ' . print_r(['shop_section' => $shop_section, 'products' => $products], true));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Form submitted with POST method');
    error_log('$_POST: ' . print_r($_POST, true));
    error_log('$_FILES: ' . print_r($_FILES, true));
    error_log('Request headers: ' . print_r(getallheaders(), true));

    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        error_log('CSRF validation failed');
    } else {
        $errors = [];

        // Section data
        $section_subtitle = $_POST['section_subtitle'] ?? '';
        $section_title = $_POST['section_title'] ?? '';
        $section_description = $_POST['section_description'] ?? '';

        // Validate section fields
        if (empty($section_subtitle) || empty($section_title) || empty($section_description)) {
            $errors[] = 'All section fields are required.';
            error_log('Validation failed: Missing section fields');
        }

        // Section image data
        $section_image_data = ['image' => $shop_section['image']];
        if (isset($_FILES['section_image']) && $_FILES['section_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
            $max_size = 2 * 1024 * 1024; // 2MB
            $upload_dir = './uploads/shop/';
            $image_file = $_FILES['section_image'];

            // Validate file type and size
            if (!in_array($image_file['type'], $allowed_types)) {
                $errors[] = 'Invalid file type for section image. Only PNG, JPG, JPEG are allowed.';
                error_log('Invalid file type for section image: ' . $image_file['type']);
            } elseif ($image_file['size'] > $max_size) {
                $errors[] = 'File size exceeds 2MB limit for section image.';
                error_log('File size too large for section image: ' . $image_file['size']);
            } elseif ($image_file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'File upload error for section image: ' . $image_file['error'];
                error_log('File upload error for section image: ' . $image_file['error']);
            } else {
                // Generate unique filename
                $ext = pathinfo($image_file['name'], PATHINFO_EXTENSION);
                $image_filename = 'shop_bg_' . time() . '.' . $ext;
                $image_path = $upload_dir . $image_filename;

                // Move uploaded file
                if (!move_uploaded_file($image_file['tmp_name'], $image_path)) {
                    $errors[] = 'Failed to move uploaded file for section image.';
                    error_log('Failed to move uploaded file to: ' . $image_path);
                } else {
                    $section_image_data['image'] = 'uploads/shop/' . $image_filename;
                    error_log('Section image uploaded successfully: ' . $section_image_data['image']);
                }
            }
        }

        // Product data
        $product_data = [];
        for ($i = 1; $i <= 4; $i++) {
            $product_data[$i] = [
                'product_name' => $_POST["product_name{$i}"] ?? '',
                'product_description' => $_POST["product_description{$i}"] ?? '',
                'product_price' => $_POST["product_price{$i}"] ?? '',
                'product_image' => $products[$i]['product_image'] ?? ''
            ];

            // Validate product fields
            if (empty($product_data[$i]['product_name']) || empty($product_data[$i]['product_description']) || empty($product_data[$i]['product_price'])) {
                $errors[] = "All fields for Product $i are required.";
                error_log("Validation failed: Missing fields for Product $i");
            } elseif (!is_numeric($product_data[$i]['product_price']) || $product_data[$i]['product_price'] < 0) {
                $errors[] = "Invalid price for Product $i.";
                error_log("Validation failed: Invalid price for Product $i");
            }

            // Product image handling
            if (isset($_FILES["product_image{$i}"]) && $_FILES["product_image{$i}"]['error'] !== UPLOAD_ERR_NO_FILE) {
                $image_file = $_FILES["product_image{$i}"];
                if (!in_array($image_file['type'], $allowed_types)) {
                    $errors[] = "Invalid file type for Product $i image.";
                    error_log("Invalid file type for Product $i image: " . $image_file['type']);
                } elseif ($image_file['size'] > $max_size) {
                    $errors[] = "File size exceeds 2MB limit for Product $i image.";
                    error_log("File size too large for Product $i image: " . $image_file['size']);
                } elseif ($image_file['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = "File upload error for Product $i image: " . $image_file['error'];
                    error_log("File upload error for Product $i image: " . $image_file['error']);
                } else {
                    $ext = pathinfo($image_file['name'], PATHINFO_EXTENSION);
                    $image_filename = "product{$i}_" . time() . '.' . $ext;
                    $image_path = $upload_dir . $image_filename;
                    if (!move_uploaded_file($image_file['tmp_name'], $image_path)) {
                        $errors[] = "Failed to move uploaded file for Product $i image.";
                        error_log("Failed to move uploaded file to: " . $image_path);
                    } else {
                        $product_data[$i]['product_image'] = 'uploads/shop/' . $image_filename;
                        error_log("Product $i image uploaded successfully: " . $product_data[$i]['product_image']);
                    }
                }
            }
        }

        // Proceed with database update if no errors
        if (empty($errors)) {
            // Update section
            $query = "SELECT COUNT(*) AS count FROM ws_shop WHERE id = 1";
            $stmt = $mysqli->prepare($query);
            $stmt->execute();
            $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
            $stmt->close();

            if ($row_exists) {
                $query = "UPDATE ws_shop SET image = ?, subtitle = ?, title = ?, description = ? WHERE id = 1";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $errors[] = 'Prepare failed for section update: ' . $mysqli->error;
                    error_log('Prepare failed for section update: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('ssss', $section_image_data['image'], $section_subtitle, $section_title, $section_description);
                    if (!$stmt->execute()) {
                        $errors[] = 'Update failed for section: ' . $stmt->error;
                        error_log('Update failed for section: ' . $stmt->error);
                    } else {
                        error_log('Section update successful, affected rows: ' . $stmt->affected_rows);
                    }
                    $stmt->close();
                }
            } else {
                $query = "INSERT INTO ws_shop (id, type, image, subtitle, title, description) VALUES (1, 'section', ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $errors[] = 'Prepare failed for section insert: ' . $mysqli->error;
                    error_log('Prepare failed for section insert: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('ssss', $section_image_data['image'], $section_subtitle, $section_title, $section_description);
                    if (!$stmt->execute()) {
                        $errors[] = 'Insert failed for section: ' . $stmt->error;
                        error_log('Insert failed for section: ' . $stmt->error);
                    } else {
                        error_log('Section insert successful');
                    }
                    $stmt->close();
                }
            }

            // Update products
            for ($i = 1; $i <= 4; $i++) {
                $product_id = $i + 1; // IDs 2-5
                $query = "SELECT COUNT(*) AS count FROM ws_shop WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('i', $product_id);
                $stmt->execute();
                $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
                $stmt->close();

                if ($row_exists) {
                    $query = "UPDATE ws_shop SET product_name = ?, product_description = ?, product_price = ?, product_image = ?, product_order = ? WHERE id = ?";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Product $i update: " . $mysqli->error;
                        error_log("Prepare failed for Product $i update: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('ssdsi', $product_data[$i]['product_name'], $product_data[$i]['product_description'], $product_data[$i]['product_price'], $product_data[$i]['product_image'], $i, $product_id);
                        if (!$stmt->execute()) {
                            $errors[] = "Update failed for Product $i: " . $stmt->error;
                            error_log("Update failed for Product $i: " . $stmt->error);
                        } else {
                            error_log("Update successful for Product $i, affected rows: " . $stmt->affected_rows);
                        }
                        $stmt->close();
                    }
                } else {
                    $query = "INSERT INTO ws_shop (id, type, product_name, product_description, product_price, product_image, product_order) VALUES (?, 'product', ?, ?, ?, ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Product $i insert: " . $mysqli->error;
                        error_log("Prepare failed for Product $i insert: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('issdsi', $product_id, $product_data[$i]['product_name'], $product_data[$i]['product_description'], $product_data[$i]['product_price'], $product_data[$i]['product_image'], $i);
                        if (!$stmt->execute()) {
                            $errors[] = "Insert failed for Product $i: " . $stmt->error;
                            error_log("Insert failed for Product $i: " . $stmt->error);
                        } else {
                            error_log("Insert successful for Product $i");
                        }
                        $stmt->close();
                    }
                }
            }

            // Refresh data
            if (empty($errors)) {
                $query = "SELECT * FROM ws_shop WHERE id IN (1, 2, 3, 4, 5) ORDER BY id";
                $result = $mysqli->query($query);
                if (!$result) {
                    $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                    error_log('Refresh query failed: ' . $mysqli->error);
                } else {
                    $shop_section = [];
                    $products = [];
                    while ($row = $result->fetch_assoc()) {
                        if ($row['type'] === 'section') {
                            $shop_section = $row;
                        } else {
                            $products[$row['product_order']] = $row;
                        }
                    }
                    error_log('Shop data refreshed: ' . print_r(['shop_section' => $shop_section, 'products' => $products], true));
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Shop Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="shop-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <h3 class="text-lg font-medium mb-2">Section Settings</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="section_subtitle">Subtitle</label>
                    <input type="text" name="section_subtitle" id="section_subtitle" value="<?php echo htmlspecialchars($shop_section['subtitle']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="section_title">Title</label>
                    <input type="text" name="section_title" id="section_title" value="<?php echo htmlspecialchars($shop_section['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="section_description">Description</label>
                    <textarea name="section_description" id="section_description" rows="4" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($shop_section['description']); ?></textarea>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Background Image</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="section_image">Image (PNG, JPG, JPEG, max 2MB)</label>
                    <input type="file" name="section_image" id="section_image" accept="image/png,image/jpeg,image/jpg" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <?php if (!empty($shop_section['image'])): ?>
                        <img id="section-image-preview" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $shop_section['image']); ?>" alt="Section Image Preview" class="mt-2 max-w-xs h-auto">
                    <?php else: ?>
                        <img id="section-image-preview" src="" alt="Section Image Preview" class="mt-2 max-w-xs h-auto hidden">
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Featured Products</h3>
                <div class="space-y-4">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="border p-4 rounded">
                            <h4 class="text-md font-medium mb-2">Product <?php echo $i; ?></h4>
                            <label class="block text-gray-700 font-medium mb-1" for="product_name<?php echo $i; ?>">Product Name</label>
                            <input type="text" name="product_name<?php echo $i; ?>" id="product_name<?php echo $i; ?>" value="<?php echo htmlspecialchars($products[$i]['product_name'] ?? ''); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                            <label class="block text-gray-700 font-medium mb-1" for="product_description<?php echo $i; ?>">Product Description</label>
                            <textarea name="product_description<?php echo $i; ?>" id="product_description<?php echo $i; ?>" rows="3" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($products[$i]['product_description'] ?? ''); ?></textarea>
                            <label class="block text-gray-700 font-medium mb-1" for="product_price<?php echo $i; ?>">Price (GHS)</label>
                            <input type="number" name="product_price<?php echo $i; ?>" id="product_price<?php echo $i; ?>" value="<?php echo htmlspecialchars($products[$i]['product_price'] ?? ''); ?>" step="0.01" min="0" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                            <label class="block text-gray-700 font-medium mb-1" for="product_image<?php echo $i; ?>">Product Image (PNG, JPG, JPEG, max 2MB)</label>
                            <input type="file" name="product_image<?php echo $i; ?>" id="product_image<?php echo $i; ?>" accept="image/png,image/jpeg,image/jpg" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <?php if (!empty($products[$i]['product_image'])): ?>
                                <img id="product-image-preview<?php echo $i; ?>" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $products[$i]['product_image']); ?>" alt="Product <?php echo $i; ?> Image Preview" class="mt-2 max-w-xs h-auto">
                            <?php else: ?>
                                <img id="product-image-preview<?php echo $i; ?>" src="" alt="Product <?php echo $i; ?> Image Preview" class="mt-2 max-w-xs h-auto hidden">
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div>
                <button type="submit" id="submit-button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Changes</button>
                <a href="<?php echo URLROOT; ?>cms/index.php" class="ml-4 text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</section>

<script>
    console.log('Form initialized');

    document.getElementById('shop-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });

    // Section image preview
    document.getElementById('section_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('section-image-preview');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.classList.add('hidden');
        }
    });

    // Product image previews
    <?php for ($i = 1; $i <= 4; $i++): ?>
        document.getElementById('product_image<?php echo $i; ?>').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('product-image-preview<?php echo $i; ?>');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
            }
        });
    <?php endfor; ?>
</script>

<?php
include 'includes/footer.php';
?>