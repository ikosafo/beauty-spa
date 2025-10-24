<?php
// cms/services.php

require_once '../config.php';

$page_title = 'Edit Services Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch services data
$section = [];
$boxes = [];
$query = "SELECT * FROM ws_services WHERE id IN (1, 2, 3, 4, 5) ORDER BY id";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
} else {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $section = $row;
        } else {
            $boxes[$row['box_order']] = $row;
        }
    }
}
if (empty($section)) {
    $section = [
        'id' => 1,
        'type' => 'section',
        'subtitle' => 'Welcome',
        'title' => 'EXPLORE Our Services',
        'description' => 'Being the pampering connoisseurs that we are, we have discovered some wonderful spa services…To relax mind & body!'
    ];
}
if (empty($boxes)) {
    $boxes = [
        1 => [
            'id' => 2,
            'type' => 'box',
            'image' => 'images/services/01.jpg',
            'title' => 'Face Massage',
            'description' => 'To reverse the ageing effect from the skin, try our face hydration treatment to get a youthful glow',
            'icon' => 'flaticon-herbal',
            'link' => 'services-details.html',
            'box_order' => 1
        ],
        2 => [
            'id' => 3,
            'type' => 'box',
            'image' => 'images/services/02.jpg',
            'title' => 'Back Massage',
            'description' => 'To reverse the ageing effect from the skin, try our face hydration treatment to get a youthful glow',
            'icon' => 'flaticon-spa-1',
            'link' => 'services-details.html',
            'box_order' => 2
        ],
        3 => [
            'id' => 4,
            'type' => 'box',
            'image' => 'images/services/03.jpg',
            'title' => 'Hair Treatment',
            'description' => 'We offer the professional hair care for all hair types discover the best hair treatments, for healthy.',
            'icon' => 'flaticon-spa',
            'link' => 'services-details.html',
            'box_order' => 3
        ],
        4 => [
            'id' => 5,
            'type' => 'box',
            'image' => 'images/services/04.jpg',
            'title' => 'Skin Care',
            'description' => 'you’ll get therapies like panchakarma treatment which can help you for living a healthy life.',
            'icon' => 'flaticon-cupping',
            'link' => 'services-details.html',
            'box_order' => 4
        ]
    ];
}
error_log('Fetched services data: ' . print_r(['section' => $section, 'boxes' => $boxes], true));

// Define available Flaticon icons
$flaticon_options = [
    'flaticon-herbal' => 'Herbal',
    'flaticon-spa-1' => 'Spa 1',
    'flaticon-spa' => 'Spa',
    'flaticon-cupping' => 'Cupping',
    'flaticon-massage' => 'Massage',
    'flaticon-facial' => 'Facial'
];

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

        // Section title data
        $section_subtitle = $_POST['section_subtitle'] ?? '';
        $section_title = $_POST['section_title'] ?? '';
        $section_description = $_POST['section_description'] ?? '';

        // Validate section fields
        if (empty($section_subtitle) || empty($section_title) || empty($section_description)) {
            $errors[] = 'All section title fields are required.';
            error_log('Validation failed: Missing section title fields');
        }

        // Service boxes data
        $box_data = [];
        for ($i = 1; $i <= 4; $i++) {
            $box_data[$i] = [
                'image' => $boxes[$i]['image'],
                'title' => $_POST["box{$i}_title"] ?? '',
                'description' => $_POST["box{$i}_description"] ?? '',
                'icon' => $_POST["box{$i}_icon"] ?? '',
                'link' => $_POST["box{$i}_link"] ?? ''
            ];

            // Validate box fields
            if (empty($box_data[$i]['title']) || empty($box_data[$i]['description']) || empty($box_data[$i]['icon']) || empty($box_data[$i]['link'])) {
                $errors[] = "All fields for Service Box $i are required.";
                error_log("Validation failed: Missing fields for Service Box $i");
            } elseif (!array_key_exists($box_data[$i]['icon'], $flaticon_options)) {
                $errors[] = "Invalid icon selected for Service Box $i.";
                error_log("Validation failed: Invalid icon for Service Box $i");
            }

            // Handle image upload
            if (isset($_FILES["box{$i}_image"]) && $_FILES["box{$i}_image"]['error'] !== UPLOAD_ERR_NO_FILE) {
                $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
                $max_size = 2 * 1024 * 1024; // 2MB
                $upload_dir = './uploads/services/';
                $image_file = $_FILES["box{$i}_image"];

                // Validate file type and size
                if (!in_array($image_file['type'], $allowed_types)) {
                    $errors[] = "Invalid file type for Service Box $i. Only PNG, JPG, JPEG are allowed.";
                    error_log("Invalid file type for Service Box $i: " . $image_file['type']);
                } elseif ($image_file['size'] > $max_size) {
                    $errors[] = "File size exceeds 2MB limit for Service Box $i.";
                    error_log("File size too large for Service Box $i: " . $image_file['size']);
                } elseif ($image_file['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = "File upload error for Service Box $i: " . $image_file['error'];
                    error_log("File upload error for Service Box $i: " . $image_file['error']);
                } else {
                    // Generate unique filename
                    $ext = pathinfo($image_file['name'], PATHINFO_EXTENSION);
                    $image_filename = "service_box{$i}_" . time() . '.' . $ext;
                    $image_path = $upload_dir . $image_filename;

                    // Move uploaded file
                    if (!move_uploaded_file($image_file['tmp_name'], $image_path)) {
                        $errors[] = "Failed to move uploaded file for Service Box $i.";
                        error_log("Failed to move uploaded file to: " . $image_path);
                    } else {
                        $box_data[$i]['image'] = 'uploads/services/' . $image_filename;
                        error_log("Image uploaded successfully for Service Box $i: " . $box_data[$i]['image']);
                    }
                }
            }
        }

        // Proceed with database update if no errors
        if (empty($errors)) {
            // Update section title
            $query = "SELECT COUNT(*) AS count FROM ws_services WHERE id = 1";
            $stmt = $mysqli->prepare($query);
            $stmt->execute();
            $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
            $stmt->close();

            if ($row_exists) {
                $query = "UPDATE ws_services SET subtitle = ?, title = ?, description = ? WHERE id = 1";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $errors[] = 'Prepare failed for section update: ' . $mysqli->error;
                    error_log('Prepare failed for section update: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('sss', $section_subtitle, $section_title, $section_description);
                    if (!$stmt->execute()) {
                        $errors[] = 'Update failed for section: ' . $stmt->error;
                        error_log('Update failed for section: ' . $stmt->error);
                    } else {
                        error_log('Section update successful, affected rows: ' . $stmt->affected_rows);
                    }
                    $stmt->close();
                }
            } else {
                $query = "INSERT INTO ws_services (id, type, subtitle, title, description) VALUES (1, 'section', ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $errors[] = 'Prepare failed for section insert: ' . $mysqli->error;
                    error_log('Prepare failed for section insert: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('sss', $section_subtitle, $section_title, $section_description);
                    if (!$stmt->execute()) {
                        $errors[] = 'Insert failed for section: ' . $stmt->error;
                        error_log('Insert failed for section: ' . $stmt->error);
                    } else {
                        error_log('Section insert successful');
                    }
                    $stmt->close();
                }
            }

            // Update service boxes
            for ($i = 1; $i <= 4; $i++) {
                $box_id = $i + 1; // IDs 2-5
                $query = "SELECT COUNT(*) AS count FROM ws_services WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('i', $box_id);
                $stmt->execute();
                $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
                $stmt->close();

                if ($row_exists) {
                    $query = "UPDATE ws_services SET image = ?, title = ?, description = ?, icon = ?, link = ?, box_order = ? WHERE id = ?";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Service Box $i update: " . $mysqli->error;
                        error_log("Prepare failed for Service Box $i update: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('sssssii', $box_data[$i]['image'], $box_data[$i]['title'], $box_data[$i]['description'], $box_data[$i]['icon'], $box_data[$i]['link'], $i, $box_id);
                        if (!$stmt->execute()) {
                            $errors[] = "Update failed for Service Box $i: " . $stmt->error;
                            error_log("Update failed for Service Box $i: " . $stmt->error);
                        } else {
                            error_log("Update successful for Service Box $i, affected rows: " . $stmt->affected_rows);
                        }
                        $stmt->close();
                    }
                } else {
                    $query = "INSERT INTO ws_services (id, type, image, title, description, icon, link, box_order) VALUES (?, 'box', ?, ?, ?, ?, ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Service Box $i insert: " . $mysqli->error;
                        error_log("Prepare failed for Service Box $i insert: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('isssssi', $box_id, $box_data[$i]['image'], $box_data[$i]['title'], $box_data[$i]['description'], $box_data[$i]['icon'], $box_data[$i]['link'], $i);
                        if (!$stmt->execute()) {
                            $errors[] = "Insert failed for Service Box $i: " . $stmt->error;
                            error_log("Insert failed for Service Box $i: " . $stmt->error);
                        } else {
                            error_log("Insert successful for Service Box $i");
                        }
                        $stmt->close();
                    }
                }
            }

            // Refresh data
            if (empty($errors)) {
                $query = "SELECT * FROM ws_services WHERE id IN (1, 2, 3, 4, 5) ORDER BY id";
                $result = $mysqli->query($query);
                if (!$result) {
                    $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                    error_log('Refresh query failed: ' . $mysqli->error);
                } else {
                    $section = [];
                    $boxes = [];
                    while ($row = $result->fetch_assoc()) {
                        if ($row['type'] === 'section') {
                            $section = $row;
                        } else {
                            $boxes[$row['box_order']] = $row;
                        }
                    }
                    error_log('Services data refreshed: ' . print_r(['section' => $section, 'boxes' => $boxes], true));
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Services Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="services-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <h3 class="text-lg font-medium mb-2">Section Title Settings</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="section_subtitle">Subtitle</label>
                    <input type="text" name="section_subtitle" id="section_subtitle" value="<?php echo htmlspecialchars($section['subtitle']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="section_title">Title</label>
                    <input type="text" name="section_title" id="section_title" value="<?php echo htmlspecialchars($section['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="section_description">Description</label>
                    <textarea name="section_description" id="section_description" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($section['description']); ?></textarea>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Service Boxes</h3>
                <div class="space-y-4">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="border p-4 rounded">
                            <h4 class="text-md font-medium mb-2">Service Box <?php echo $i; ?></h4>
                            <label class="block text-gray-700 font-medium mb-1" for="box<?php echo $i; ?>_image">Image (PNG, JPG, JPEG, max 2MB)</label>
                            <input type="file" name="box<?php echo $i; ?>_image" id="box<?php echo $i; ?>_image" accept="image/png,image/jpeg,image/jpg" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <?php if (!empty($boxes[$i]['image'])): ?>
                                <img id="box<?php echo $i; ?>_image-preview" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $boxes[$i]['image']); ?>" alt="Image Preview" class="mt-2 max-w-xs h-auto">
                            <?php else: ?>
                                <img id="box<?php echo $i; ?>_image-preview" src="" alt="Image Preview" class="mt-2 max-w-xs h-auto hidden">
                            <?php endif; ?>
                            <label class="block text-gray-700 font-medium mb-1" for="box<?php echo $i; ?>_title">Title</label>
                            <input type="text" name="box<?php echo $i; ?>_title" id="box<?php echo $i; ?>_title" value="<?php echo htmlspecialchars($boxes[$i]['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                            <label class="block text-gray-700 font-medium mb-1" for="box<?php echo $i; ?>_description">Description</label>
                            <textarea name="box<?php echo $i; ?>_description" id="box<?php echo $i; ?>_description" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($boxes[$i]['description']); ?></textarea>
                            <label class="block text-gray-700 font-medium mb-1" for="box<?php echo $i; ?>_icon">Icon</label>
                            <select name="box<?php echo $i; ?>_icon" id="box<?php echo $i; ?>_icon" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                                <?php foreach ($flaticon_options as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $boxes[$i]['icon'] === $value ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label class="block text-gray-700 font-medium mb-1" for="box<?php echo $i; ?>_link">Link</label>
                            <input type="text" name="box<?php echo $i; ?>_link" id="box<?php echo $i; ?>_link" value="<?php echo htmlspecialchars($boxes[$i]['link']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
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

    document.getElementById('services-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });

    // Image preview for each service box
    <?php for ($i = 1; $i <= 4; $i++): ?>
        document.getElementById('box<?php echo $i; ?>_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('box<?php echo $i; ?>_image-preview');
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