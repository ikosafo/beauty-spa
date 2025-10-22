<?php
// cms/gallery.php

require_once '../config.php';

$page_title = 'Edit Gallery Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch gallery data
$section = [];
$images = [];
$query = "SELECT * FROM ws_gallery WHERE id IN (1, 2, 3, 4, 5) ORDER BY id";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
} else {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $section = $row;
        } else {
            $images[$row['image_order']] = $row;
        }
    }
}
if (empty($section)) {
    $section = [
        'id' => 1,
        'type' => 'section',
        'subtitle' => 'Gallery',
        'title' => 'AN INCREDIBLE SPA EXPERIENCE',
        'description' => 'Stunning treatment rooms are endowed with lush open air gardens soak bathtub and cushioned daybed built for two.'
    ];
}
if (empty($images)) {
    $images = [
        1 => [
            'id' => 2,
            'type' => 'image',
            'image' => 'images/gallery/01.jpg',
            'image_order' => 1
        ],
        2 => [
            'id' => 3,
            'type' => 'image',
            'image' => 'images/gallery/02.jpg',
            'image_order' => 2
        ],
        3 => [
            'id' => 4,
            'type' => 'image',
            'image' => 'images/gallery/03.jpg',
            'image_order' => 3
        ],
        4 => [
            'id' => 5,
            'type' => 'image',
            'image' => 'images/gallery/04.jpg',
            'image_order' => 4
        ]
    ];
}
error_log('Fetched gallery data: ' . print_r(['section' => $section, 'images' => $images], true));

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

        // Image data
        $image_data = [];
        for ($i = 1; $i <= 4; $i++) {
            $image_data[$i] = [
                'image' => $images[$i]['image']
            ];

            // Handle image upload
            if (isset($_FILES["image{$i}"]) && $_FILES["image{$i}"]['error'] !== UPLOAD_ERR_NO_FILE) {
                $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
                $max_size = 2 * 1024 * 1024; // 2MB
                $upload_dir = './uploads/gallery/';
                $image_file = $_FILES["image{$i}"];

                // Validate file type and size
                if (!in_array($image_file['type'], $allowed_types)) {
                    $errors[] = "Invalid file type for Image $i. Only PNG, JPG, JPEG are allowed.";
                    error_log("Invalid file type for Image $i: " . $image_file['type']);
                } elseif ($image_file['size'] > $max_size) {
                    $errors[] = "File size exceeds 2MB limit for Image $i.";
                    error_log("File size too large for Image $i: " . $image_file['size']);
                } elseif ($image_file['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = "File upload error for Image $i: " . $image_file['error'];
                    error_log("File upload error for Image $i: " . $image_file['error']);
                } else {
                    // Generate unique filename
                    $ext = pathinfo($image_file['name'], PATHINFO_EXTENSION);
                    $image_filename = "gallery_image{$i}_" . time() . '.' . $ext;
                    $image_path = $upload_dir . $image_filename;

                    // Move uploaded file
                    if (!move_uploaded_file($image_file['tmp_name'], $image_path)) {
                        $errors[] = "Failed to move uploaded file for Image $i.";
                        error_log("Failed to move uploaded file to: " . $image_path);
                    } else {
                        $image_data[$i]['image'] = 'uploads/gallery/' . $image_filename;
                        error_log("Image uploaded successfully for Image $i: " . $image_data[$i]['image']);
                    }
                }
            }
        }

        // Proceed with database update if no errors
        if (empty($errors)) {
            // Update section title
            $query = "SELECT COUNT(*) AS count FROM ws_gallery WHERE id = 1";
            $stmt = $mysqli->prepare($query);
            $stmt->execute();
            $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
            $stmt->close();

            if ($row_exists) {
                $query = "UPDATE ws_gallery SET subtitle = ?, title = ?, description = ? WHERE id = 1";
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
                $query = "INSERT INTO ws_gallery (id, type, subtitle, title, description) VALUES (1, 'section', ?, ?, ?)";
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

            // Update gallery images
            for ($i = 1; $i <= 4; $i++) {
                $image_id = $i + 1; // IDs 2-5
                $query = "SELECT COUNT(*) AS count FROM ws_gallery WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('i', $image_id);
                $stmt->execute();
                $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
                $stmt->close();

                if ($row_exists) {
                    $query = "UPDATE ws_gallery SET image = ?, image_order = ? WHERE id = ?";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Image $i update: " . $mysqli->error;
                        error_log("Prepare failed for Image $i update: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('sii', $image_data[$i]['image'], $i, $image_id);
                        if (!$stmt->execute()) {
                            $errors[] = "Update failed for Image $i: " . $stmt->error;
                            error_log("Update failed for Image $i: " . $stmt->error);
                        } else {
                            error_log("Update successful for Image $i, affected rows: " . $stmt->affected_rows);
                        }
                        $stmt->close();
                    }
                } else {
                    $query = "INSERT INTO ws_gallery (id, type, image, image_order) VALUES (?, 'image', ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Image $i insert: " . $mysqli->error;
                        error_log("Prepare failed for Image $i insert: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('isi', $image_id, $image_data[$i]['image'], $i);
                        if (!$stmt->execute()) {
                            $errors[] = "Insert failed for Image $i: " . $stmt->error;
                            error_log("Insert failed for Image $i: " . $stmt->error);
                        } else {
                            error_log("Insert successful for Image $i");
                        }
                        $stmt->close();
                    }
                }
            }

            // Refresh data
            if (empty($errors)) {
                $query = "SELECT * FROM ws_gallery WHERE id IN (1, 2, 3, 4, 5) ORDER BY id";
                $result = $mysqli->query($query);
                if (!$result) {
                    $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                    error_log('Refresh query failed: ' . $mysqli->error);
                } else {
                    $section = [];
                    $images = [];
                    while ($row = $result->fetch_assoc()) {
                        if ($row['type'] === 'section') {
                            $section = $row;
                        } else {
                            $images[$row['image_order']] = $row;
                        }
                    }
                    error_log('Gallery data refreshed: ' . print_r(['section' => $section, 'images' => $images], true));
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Gallery Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="gallery-form">
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
                <h3 class="text-lg font-medium mb-2">Gallery Images</h3>
                <div class="space-y-4">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="border p-4 rounded">
                            <h4 class="text-md font-medium mb-2">Image <?php echo $i; ?></h4>
                            <label class="block text-gray-700 font-medium mb-1" for="image<?php echo $i; ?>">Image (PNG, JPG, JPEG, max 2MB)</label>
                            <input type="file" name="image<?php echo $i; ?>" id="image<?php echo $i; ?>" accept="image/png,image/jpeg,image/jpg" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <?php if (!empty($images[$i]['image'])): ?>
                                <img id="image<?php echo $i; ?>-preview" src="<?php echo htmlspecialchars(URLROOT . '/' . $images[$i]['image']); ?>" alt="Image Preview" class="mt-2 max-w-xs h-auto">
                            <?php else: ?>
                                <img id="image<?php echo $i; ?>-preview" src="" alt="Image Preview" class="mt-2 max-w-xs h-auto hidden">
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

    document.getElementById('gallery-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });

    // Image preview for each gallery image
    <?php for ($i = 1; $i <= 4; $i++): ?>
        document.getElementById('image<?php echo $i; ?>').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('image<?php echo $i; ?>-preview');
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