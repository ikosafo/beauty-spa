<?php
// cms/slider.php

require_once '../config.php';

$page_title = 'Edit Slider Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch slider data
$slides = [];
$query = "SELECT * FROM ws_slider WHERE id IN (1, 2) ORDER BY id";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
} else {
    while ($row = $result->fetch_assoc()) {
        $slides[$row['id']] = [
            'slide_key' => $row['slide_key'],
            'background_media' => $row['background_media'] ?? 'images/slides/slider-mainbg-001.jpg',
            'subtitle' => $row['subtitle'] ?? 'Best Place for',
            'heading1' => $row['heading1'] ?? 'THE BEST TIME',
            'heading2' => $row['heading2'] ?? 'TO RELAX WITH SYLIN',
            'description' => $row['description'] ?? 'Professional Beauty Center Since 1919.',
            'button_text' => $row['button_text'] ?? 'Get An Appointment!',
            'button_url' => $row['button_url'] ?? '#'
        ];
    }
}
if (empty($slides)) {
    $slides = [
        1 => [
            'slide_key' => 'rs-3',
            'background_media' => 'images/slides/slider-mainbg-001.jpg',
            'subtitle' => 'Best Place for',
            'heading1' => 'THE BEST TIME',
            'heading2' => 'TO RELAX WITH SYLIN',
            'description' => 'Professional Beauty Center Since 1919.',
            'button_text' => 'Get An Appointment!',
            'button_url' => '#'
        ],
        2 => [
            'slide_key' => 'rs-4',
            'background_media' => 'images/slides/istockphoto-1047656636-640_adpp_is.mp4',
            'subtitle' => 'Best Place for',
            'heading1' => 'THE BEST TIME',
            'heading2' => 'TO RELAX WITH SYLIN',
            'description' => 'Professional Beauty Center Since 1919.',
            'button_text' => 'Watch Video',
            'button_url' => 'https://youtu.be/7e90gBu4pas'
        ]
    ];
}
error_log('Fetched slider data: ' . print_r($slides, true));

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
        foreach ([1, 2] as $slide_id) {
            $slide_key = $slides[$slide_id]['slide_key'];
            $subtitle = $_POST["subtitle_$slide_id"] ?? '';
            $heading1 = $_POST["heading1_$slide_id"] ?? '';
            $heading2 = $_POST["heading2_$slide_id"] ?? '';
            $description = $_POST["description_$slide_id"] ?? '';
            $button_text = $_POST["button_text_$slide_id"] ?? '';
            $button_url = $_POST["button_url_$slide_id"] ?? '';
            $background_media = $slides[$slide_id]['background_media'];

            // Validate required fields
            if (empty($subtitle) || empty($heading1) || empty($heading2) || empty($description)) {
                $errors[] = "All text fields for Slide $slide_id are required.";
                error_log("Validation failed for Slide $slide_id: Missing required fields");
            }

            // Handle media upload
            if (isset($_FILES["background_media_$slide_id"]) && $_FILES["background_media_$slide_id"]['error'] !== UPLOAD_ERR_NO_FILE) {
                $allowed_types = $slide_id == 1 ? ['image/png', 'image/jpeg', 'image/jpg'] : ['video/mp4', 'image/png', 'image/jpeg', 'image/jpg'];
                $max_size = $slide_id == 1 ? 2 * 1024 * 1024 : 5 * 1024 * 1024; // 2MB for images, 5MB for videos
                $upload_dir = './uploads/slider/';
                $media_file = $_FILES["background_media_$slide_id"];

                // Validate file type and size
                if (!in_array($media_file['type'], $allowed_types)) {
                    $errors[] = "Invalid file type for Slide $slide_id. Only " . ($slide_id == 1 ? 'PNG, JPG, JPEG' : 'MP4, PNG, JPG, JPEG') . " are allowed.";
                    error_log("Invalid file type for Slide $slide_id: " . $media_file['type']);
                } elseif ($media_file['size'] > $max_size) {
                    $errors[] = "File size exceeds " . ($slide_id == 1 ? '2MB' : '5MB') . " limit for Slide $slide_id.";
                    error_log("File size too large for Slide $slide_id: " . $media_file['size']);
                } elseif ($media_file['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = "File upload error for Slide $slide_id: " . $media_file['error'];
                    error_log("File upload error for Slide $slide_id: " . $media_file['error']);
                } else {
                    // Generate unique filename
                    $ext = pathinfo($media_file['name'], PATHINFO_EXTENSION);
                    $media_filename = "slide{$slide_id}_" . time() . '.' . $ext;
                    $media_path = $upload_dir . $media_filename;

                    // Move uploaded file
                    if (!move_uploaded_file($media_file['tmp_name'], $media_path)) {
                        $errors[] = "Failed to move uploaded file for Slide $slide_id.";
                        error_log("Failed to move uploaded file to: " . $media_path);
                    } else {
                        $background_media = 'uploads/slider/' . $media_filename;
                        error_log("Media uploaded successfully for Slide $slide_id: " . $background_media);
                    }
                }
            }

            // Proceed with database update if no errors
            if (empty($errors)) {
                $query = "SELECT COUNT(*) AS count FROM ws_slider WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('i', $slide_id);
                $stmt->execute();
                $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
                $stmt->close();
                error_log("Row exists for Slide $slide_id: " . ($row_exists ? 'Yes' : 'No'));

                if ($row_exists) {
                    // Update existing row
                    $query = "UPDATE ws_slider SET slide_key = ?, background_media = ?, subtitle = ?, heading1 = ?, heading2 = ?, description = ?, button_text = ?, button_url = ? WHERE id = ?";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Slide $slide_id: " . $mysqli->error;
                        error_log("Prepare failed for Slide $slide_id: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('ssssssssi', $slide_key, $background_media, $subtitle, $heading1, $heading2, $description, $button_text, $button_url, $slide_id);
                        if (!$stmt->execute()) {
                            $errors[] = "Update failed for Slide $slide_id: " . $stmt->error;
                            error_log("Update failed for Slide $slide_id: " . $stmt->error);
                        } else {
                            error_log("Update successful for Slide $slide_id, affected rows: " . $stmt->affected_rows);
                        }
                        $stmt->close();
                    }
                } else {
                    // Insert new row
                    $query = "INSERT INTO ws_slider (id, slide_key, background_media, subtitle, heading1, heading2, description, button_text, button_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Slide $slide_id: " . $mysqli->error;
                        error_log("Prepare failed for Slide $slide_id: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('issssssss', $slide_id, $slide_key, $background_media, $subtitle, $heading1, $heading2, $description, $button_text, $button_url);
                        if (!$stmt->execute()) {
                            $errors[] = "Insert failed for Slide $slide_id: " . $stmt->error;
                            error_log("Insert failed for Slide $slide_id: " . $stmt->error);
                        } else {
                            error_log("Insert successful for Slide $slide_id");
                        }
                        $stmt->close();
                    }
                }
            }
        }

        // Refresh data
        if (empty($errors)) {
            $query = "SELECT * FROM ws_slider WHERE id IN (1, 2) ORDER BY id";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                error_log('Refresh query failed: ' . $mysqli->error);
            } else {
                $slides = [];
                while ($row = $result->fetch_assoc()) {
                    $slides[$row['id']] = $row;
                }
                error_log('Slider data refreshed: ' . print_r($slides, true));
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errors);
        }
    }
}

include 'includes/header.php';
?>

<!-- Slider Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="slider-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <?php foreach ([1, 2] as $slide_id): ?>
                <div>
                    <h3 class="text-lg font-medium mb-2">Slide <?php echo $slide_id; ?> Settings</h3>
                    <div class="space-y-2">
                        <label class="block text-gray-700 font-medium mb-1" for="subtitle_<?php echo $slide_id; ?>">Subtitle</label>
                        <input type="text" name="subtitle_<?php echo $slide_id; ?>" id="subtitle_<?php echo $slide_id; ?>" value="<?php echo htmlspecialchars($slides[$slide_id]['subtitle']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                        <label class="block text-gray-700 font-medium mb-1" for="heading1_<?php echo $slide_id; ?>">Heading 1</label>
                        <input type="text" name="heading1_<?php echo $slide_id; ?>" id="heading1_<?php echo $slide_id; ?>" value="<?php echo htmlspecialchars($slides[$slide_id]['heading1']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                        <label class="block text-gray-700 font-medium mb-1" for="heading2_<?php echo $slide_id; ?>">Heading 2</label>
                        <input type="text" name="heading2_<?php echo $slide_id; ?>" id="heading2_<?php echo $slide_id; ?>" value="<?php echo htmlspecialchars($slides[$slide_id]['heading2']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                        <label class="block text-gray-700 font-medium mb-1" for="description_<?php echo $slide_id; ?>">Description</label>
                        <textarea name="description_<?php echo $slide_id; ?>" id="description_<?php echo $slide_id; ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($slides[$slide_id]['description']); ?></textarea>
                        <label class="block text-gray-700 font-medium mb-1" for="button_text_<?php echo $slide_id; ?>">Button Text (optional)</label>
                        <input type="text" name="button_text_<?php echo $slide_id; ?>" id="button_text_<?php echo $slide_id; ?>" value="<?php echo htmlspecialchars($slides[$slide_id]['button_text']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <label class="block text-gray-700 font-medium mb-1" for="button_url_<?php echo $slide_id; ?>">Button URL (optional)</label>
                        <input type="url" name="button_url_<?php echo $slide_id; ?>" id="button_url_<?php echo $slide_id; ?>" value="<?php echo htmlspecialchars($slides[$slide_id]['button_url']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <label class="block text-gray-700 font-medium mb-1" for="background_media_<?php echo $slide_id; ?>">Background Media (<?php echo $slide_id == 1 ? 'PNG, JPG, JPEG, max 2MB' : 'MP4, PNG, JPG, JPEG, max 5MB'; ?>)</label>
                        <input type="file" name="background_media_<?php echo $slide_id; ?>" id="background_media_<?php echo $slide_id; ?>" accept="<?php echo $slide_id == 1 ? 'image/png,image/jpeg,image/jpg' : 'video/mp4,image/png,image/jpeg,image/jpg'; ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <?php if (!empty($slides[$slide_id]['background_media'])): ?>
                            <?php if (pathinfo($slides[$slide_id]['background_media'], PATHINFO_EXTENSION) === 'mp4'): ?>
                                <video id="media-preview-<?php echo $slide_id; ?>" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $slides[$slide_id]['background_media']); ?>" class="mt-2 max-w-xs h-auto" controls></video>
                            <?php else: ?>
                                <img id="media-preview-<?php echo $slide_id; ?>" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $slides[$slide_id]['background_media']); ?>" alt="Media Preview" class="mt-2 max-w-xs h-auto">
                            <?php endif; ?>
                        <?php else: ?>
                            <img id="media-preview-<?php echo $slide_id; ?>" src="" alt="Media Preview" class="mt-2 max-w-xs h-auto hidden">
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div>
                <button type="submit" id="submit-button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Changes</button>
                <a href="<?php echo URLROOT; ?>cms/index.php" class="ml-4 text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</section>

<script>
    console.log('Form initialized');

    document.getElementById('slider-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });

    // Media preview for each slide
    <?php foreach ([1, 2] as $slide_id): ?>
        document.getElementById('background_media_<?php echo $slide_id; ?>').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('media-preview-<?php echo $slide_id; ?>');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (file.type.startsWith('video/')) {
                        preview.outerHTML = '<video id="media-preview-<?php echo $slide_id; ?>" src="' + e.target.result + '" class="mt-2 max-w-xs h-auto" controls></video>';
                    } else {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    }
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
            }
        });
    <?php endforeach; ?>
</script>

<?php
include 'includes/footer.php';
?>