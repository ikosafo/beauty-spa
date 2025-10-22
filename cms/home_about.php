<?php
// cms/about.php

require_once '../config.php';

$page_title = 'Edit About Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch about data
$query = "SELECT * FROM ws_about WHERE id = 1";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
}
$about = $result->fetch_assoc();
if (!$about) {
    $about = [
        'image' => 'images/single-img-one.jpg',
        'subtitle' => 'Wellness',
        'title' => 'WELCOME TO HOME OF RELAXE & RESPITE.',
        'description' => 'There’s nothing more luxurious and relaxing than a trip to the spa & Salon. We offer a wide variety of body spa therapies to help you heal your body naturally. Get relaxed from stressed & hectic schedule.',
        'paragraph' => 'Everybody is looking for places where to relax and get more energy. In our wellness center silence, energy, beauty and vitality meet. The treatments we offer will refresh both your body and soul. We’ll be glad to welcome you and recommend our facilities and services.',
        'box1_title' => 'Massage',
        'box1_icon' => 'flaticon-spa',
        'box2_title' => 'Therapies',
        'box2_icon' => 'flaticon-wellness',
        'box3_title' => 'Relaxation',
        'box3_icon' => 'flaticon-hammam',
        'box4_title' => 'Facial',
        'box4_icon' => 'flaticon-person-silhouette-in-sauna'
    ];
}
error_log('Fetched about data: ' . print_r($about, true));

// Define available Flaticon icons
$flaticon_options = [
    'flaticon-spa' => 'Spa',
    'flaticon-wellness' => 'Wellness',
    'flaticon-hammam' => 'Hammam',
    'flaticon-person-silhouette-in-sauna' => 'Person in Sauna',
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
        // Use raw POST values (prepared statements handle escaping)
        $subtitle = $_POST['subtitle'] ?? '';
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $paragraph = $_POST['paragraph'] ?? '';
        $box1_title = $_POST['box1_title'] ?? '';
        $box1_icon = $_POST['box1_icon'] ?? '';
        $box2_title = $_POST['box2_title'] ?? '';
        $box2_icon = $_POST['box2_icon'] ?? '';
        $box3_title = $_POST['box3_title'] ?? '';
        $box3_icon = $_POST['box3_icon'] ?? '';
        $box4_title = $_POST['box4_title'] ?? '';
        $box4_icon = $_POST['box4_icon'] ?? '';
        $image = $about['image']; // Default to existing image

        // Validate required fields
        if (empty($subtitle) || empty($title) || empty($description) || empty($paragraph) ||
            empty($box1_title) || empty($box1_icon) || empty($box2_title) || empty($box2_icon) ||
            empty($box3_title) || empty($box3_icon) || empty($box4_title) || empty($box4_icon)) {
            $_SESSION['error'] = 'All fields except image are required';
            error_log('Validation failed: Missing required fields');
        } elseif (!array_key_exists($box1_icon, $flaticon_options) || !array_key_exists($box2_icon, $flaticon_options) ||
                 !array_key_exists($box3_icon, $flaticon_options) || !array_key_exists($box4_icon, $flaticon_options)) {
            $_SESSION['error'] = 'Invalid icon selected';
            error_log('Validation failed: Invalid icon selected');
        } else {
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
                $max_size = 2 * 1024 * 1024; // 2MB
                $upload_dir = './uploads/about/';
                $image_file = $_FILES['image'];

                // Validate file type and size
                if (!in_array($image_file['type'], $allowed_types)) {
                    $_SESSION['error'] = 'Invalid file type. Only PNG, JPG, and JPEG are allowed.';
                    error_log('Invalid file type: ' . $image_file['type']);
                } elseif ($image_file['size'] > $max_size) {
                    $_SESSION['error'] = 'File size exceeds 2MB limit.';
                    error_log('File size too large: ' . $image_file['size']);
                } elseif ($image_file['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['error'] = 'File upload error: ' . $image_file['error'];
                    error_log('File upload error: ' . $image_file['error']);
                } else {
                    // Generate unique filename to avoid overwrites
                    $ext = pathinfo($image_file['name'], PATHINFO_EXTENSION);
                    $image_filename = 'about_image_' . time() . '.' . $ext;
                    $image_path = $upload_dir . $image_filename;

                    // Move uploaded file
                    if (!move_uploaded_file($image_file['tmp_name'], $image_path)) {
                        $_SESSION['error'] = 'Failed to move uploaded file.';
                        error_log('Failed to move uploaded file to: ' . $image_path);
                    } else {
                        $image = 'uploads/about/' . $image_filename; // Store relative path
                        error_log('Image uploaded successfully: ' . $image);
                    }
                }
            }

            // Proceed with database update if no errors
            if (!isset($_SESSION['error'])) {
                // Check if row exists
                $query = "SELECT COUNT(*) AS count FROM ws_about WHERE id = 1";
                $result = $mysqli->query($query);
                if (!$result) {
                    $_SESSION['error'] = 'Check query failed: ' . $mysqli->error;
                    error_log('Check query failed: ' . $mysqli->error);
                }
                $row_exists = $result->fetch_assoc()['count'] > 0;
                error_log('Row exists: ' . ($row_exists ? 'Yes' : 'No'));

                if ($row_exists) {
                    // Update existing row
                    $query = "UPDATE ws_about SET image = ?, subtitle = ?, title = ?, description = ?, paragraph = ?, box1_title = ?, box1_icon = ?, box2_title = ?, box2_icon = ?, box3_title = ?, box3_icon = ?, box4_title = ?, box4_icon = ? WHERE id = 1";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                        error_log('Prepare failed: ' . $mysqli->error);
                    } else {
                        $stmt->bind_param('sssssssssssss', $image, $subtitle, $title, $description, $paragraph, $box1_title, $box1_icon, $box2_title, $box2_icon, $box3_title, $box3_icon, $box4_title, $box4_icon);
                        if (!$stmt->execute()) {
                            $_SESSION['error'] = 'Update failed: ' . $stmt->error;
                            error_log('Update failed: ' . $stmt->error);
                        } else {
                            error_log('Update successful, affected rows: ' . $stmt->affected_rows);
                        }
                        $stmt->close();
                    }
                } else {
                    // Insert new row
                    $query = "INSERT INTO ws_about (image, subtitle, title, description, paragraph, box1_title, box1_icon, box2_title, box2_icon, box3_title, box3_icon, box4_title, box4_icon) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                        error_log('Prepare failed: ' . $mysqli->error);
                    } else {
                        $stmt->bind_param('sssssssssssss', $image, $subtitle, $title, $description, $paragraph, $box1_title, $box1_icon, $box2_title, $box2_icon, $box3_title, $box3_icon, $box4_title, $box4_icon);
                        if (!$stmt->execute()) {
                            $_SESSION['error'] = 'Insert failed: ' . $stmt->error;
                            error_log('Insert failed: ' . $stmt->error);
                        } else {
                            error_log('Insert successful');
                        }
                        $stmt->close();
                    }
                }

                // Refresh data
                $query = "SELECT * FROM ws_about WHERE id = 1";
                $result = $mysqli->query($query);
                if (!$result) {
                    $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                    error_log('Refresh query failed: ' . $mysqli->error);
                } else {
                    $about = $result->fetch_assoc();
                    error_log('About data refreshed: ' . print_r($about, true));
                }
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- About Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="about-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <h3 class="text-lg font-medium mb-2">About Section Settings</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="image">Image (PNG, JPG, JPEG, max 2MB)</label>
                    <input type="file" name="image" id="image" accept="image/png,image/jpeg,image/jpg" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <?php if (!empty($about['image'])): ?>
                        <img id="image-preview" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $about['image']); ?>" alt="Image Preview" class="mt-2 max-w-xs h-auto">
                    <?php else: ?>
                        <img id="image-preview" src="" alt="Image Preview" class="mt-2 max-w-xs h-auto hidden">
                    <?php endif; ?>
                    <label class="block text-gray-700 font-medium mb-1" for="subtitle">Subtitle</label>
                    <input type="text" name="subtitle" id="subtitle" value="<?php echo htmlspecialchars($about['subtitle']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="title">Title</label>
                    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($about['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="description">Description</label>
                    <textarea name="description" id="description" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($about['description']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="paragraph">Paragraph</label>
                    <textarea name="paragraph" id="paragraph" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($about['paragraph']); ?></textarea>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Icon Boxes</h3>
                <div class="space-y-4">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="border p-4 rounded">
                            <h4 class="text-md font-medium mb-2">Box <?php echo $i; ?></h4>
                            <label class="block text-gray-700 font-medium mb-1" for="box<?php echo $i; ?>_title">Title</label>
                            <input type="text" name="box<?php echo $i; ?>_title" id="box<?php echo $i; ?>_title" value="<?php echo htmlspecialchars($about["box{$i}_title"]); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                            <label class="block text-gray-700 font-medium mb-1" for="box<?php echo $i; ?>_icon">Icon</label>
                            <select name="box<?php echo $i; ?>_icon" id="box<?php echo $i; ?>_icon" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                                <?php foreach ($flaticon_options as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $about["box{$i}_icon"] === $value ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
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

    document.getElementById('about-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });

    // Image preview
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('image-preview');
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
</script>

<?php
include 'includes/footer.php';
?>