<?php
// cms/training-school.php

require_once '../config.php';

$page_title = 'Edit Training School Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch training school data
$training_section = [];
$programs = [];
$query = "SELECT * FROM ws_training_school WHERE id IN (1, 2, 3, 4, 5, 6) ORDER BY id";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
} else {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $training_section = $row;
        } else {
            $programs[$row['program_order']] = $row;
        }
    }
}
if (empty($training_section)) {
    $training_section = [
        'id' => 1,
        'type' => 'section',
        'image' => 'images/services/training-school.jpg',
        'subtitle' => 'Learn with Us',
        'title' => 'Sylin Beauty Academy',
        'description' => 'Join our professional training programs to become a certified beauty and spa specialist. Our courses cover makeup artistry, skincare, hairdressing, and more.'
    ];
}
if (empty($programs)) {
    $programs = [
        1 => [
            'id' => 2,
            'type' => 'program',
            'program_name' => 'Professional Makeup Artistry',
            'program_order' => 1
        ],
        2 => [
            'id' => 3,
            'type' => 'program',
            'program_name' => 'Advanced Skincare Techniques',
            'program_order' => 2
        ],
        3 => [
            'id' => 4,
            'type' => 'program',
            'program_name' => 'Hairdressing and Styling',
            'program_order' => 3
        ],
        4 => [
            'id' => 5,
            'type' => 'program',
            'program_name' => 'Spa Therapy and Massage',
            'program_order' => 4
        ],
        5 => [
            'id' => 6,
            'type' => 'program',
            'program_name' => 'Nail Art and Manicure',
            'program_order' => 5
        ]
    ];
}
error_log('Fetched training school data: ' . print_r(['training_section' => $training_section, 'programs' => $programs], true));

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

        // Image data
        $image_data = ['image' => $training_section['image']];
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
            $max_size = 2 * 1024 * 1024; // 2MB
            $upload_dir = './uploads/training-school/';
            $image_file = $_FILES['image'];

            // Validate file type and size
            if (!in_array($image_file['type'], $allowed_types)) {
                $errors[] = 'Invalid file type for image. Only PNG, JPG, JPEG are allowed.';
                error_log('Invalid file type for image: ' . $image_file['type']);
            } elseif ($image_file['size'] > $max_size) {
                $errors[] = 'File size exceeds 2MB limit for image.';
                error_log('File size too large for image: ' . $image_file['size']);
            } elseif ($image_file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'File upload error for image: ' . $image_file['error'];
                error_log('File upload error for image: ' . $image_file['error']);
            } else {
                // Generate unique filename
                $ext = pathinfo($image_file['name'], PATHINFO_EXTENSION);
                $image_filename = 'training_school_bg_' . time() . '.' . $ext;
                $image_path = $upload_dir . $image_filename;

                // Move uploaded file
                if (!move_uploaded_file($image_file['tmp_name'], $image_path)) {
                    $errors[] = 'Failed to move uploaded file for image.';
                    error_log('Failed to move uploaded file to: ' . $image_path);
                } else {
                    $image_data['image'] = 'Uploads/training-school/' . $image_filename;
                    error_log('Image uploaded successfully: ' . $image_data['image']);
                }
            }
        }

        // Training program data
        $program_data = [];
        for ($i = 1; $i <= 5; $i++) {
            $program_data[$i] = [
                'program_name' => $_POST["program_name{$i}"] ?? ''
            ];

            // Validate program fields
            if (empty($program_data[$i]['program_name'])) {
                $errors[] = "Program name for Program $i is required.";
                error_log("Validation failed: Missing program name for Program $i");
            }
        }

        // Proceed with database update if no errors
        if (empty($errors)) {
            // Update section
            $query = "SELECT COUNT(*) AS count FROM ws_training_school WHERE id = 1";
            $stmt = $mysqli->prepare($query);
            $stmt->execute();
            $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
            $stmt->close();

            if ($row_exists) {
                $query = "UPDATE ws_training_school SET image = ?, subtitle = ?, title = ?, description = ? WHERE id = 1";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $errors[] = 'Prepare failed for section update: ' . $mysqli->error;
                    error_log('Prepare failed for section update: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('ssss', $image_data['image'], $section_subtitle, $section_title, $section_description);
                    if (!$stmt->execute()) {
                        $errors[] = 'Update failed for section: ' . $stmt->error;
                        error_log('Update failed for section: ' . $stmt->error);
                    } else {
                        error_log('Section update successful, affected rows: ' . $stmt->affected_rows);
                    }
                    $stmt->close();
                }
            } else {
                $query = "INSERT INTO ws_training_school (id, type, image, subtitle, title, description) VALUES (1, 'section', ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $errors[] = 'Prepare failed for section insert: ' . $mysqli->error;
                    error_log('Prepare failed for section insert: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('ssss', $image_data['image'], $section_subtitle, $section_title, $section_description);
                    if (!$stmt->execute()) {
                        $errors[] = 'Insert failed for section: ' . $stmt->error;
                        error_log('Insert failed for section: ' . $stmt->error);
                    } else {
                        error_log('Section insert successful');
                    }
                    $stmt->close();
                }
            }

            // Update training programs
            for ($i = 1; $i <= 5; $i++) {
                $program_id = $i + 1; // IDs 2-6
                $query = "SELECT COUNT(*) AS count FROM ws_training_school WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('i', $program_id);
                $stmt->execute();
                $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
                $stmt->close();

                if ($row_exists) {
                    $query = "UPDATE ws_training_school SET program_name = ?, program_order = ? WHERE id = ?";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Program $i update: " . $mysqli->error;
                        error_log("Prepare failed for Program $i update: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('sii', $program_data[$i]['program_name'], $i, $program_id);
                        if (!$stmt->execute()) {
                            $errors[] = "Update failed for Program $i: " . $stmt->error;
                            error_log("Update failed for Program $i: " . $stmt->error);
                        } else {
                            error_log("Update successful for Program $i, affected rows: " . $stmt->affected_rows);
                        }
                        $stmt->close();
                    }
                } else {
                    $query = "INSERT INTO ws_training_school (id, type, program_name, program_order) VALUES (?, 'program', ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Program $i insert: " . $mysqli->error;
                        error_log("Prepare failed for Program $i insert: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('isi', $program_id, $program_data[$i]['program_name'], $i);
                        if (!$stmt->execute()) {
                            $errors[] = "Insert failed for Program $i: " . $stmt->error;
                            error_log("Insert failed for Program $i: " . $stmt->error);
                        } else {
                            error_log("Insert successful for Program $i");
                        }
                        $stmt->close();
                    }
                }
            }

            // Refresh data
            if (empty($errors)) {
                $query = "SELECT * FROM ws_training_school WHERE id IN (1, 2, 3, 4, 5, 6) ORDER BY id";
                $result = $mysqli->query($query);
                if (!$result) {
                    $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                    error_log('Refresh query failed: ' . $mysqli->error);
                } else {
                    $training_section = [];
                    $programs = [];
                    while ($row = $result->fetch_assoc()) {
                        if ($row['type'] === 'section') {
                            $training_section = $row;
                        } else {
                            $programs[$row['program_order']] = $row;
                        }
                    }
                    error_log('Training school data refreshed: ' . print_r(['training_section' => $training_section, 'programs' => $programs], true));
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Training School Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="training-school-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <h3 class="text-lg font-medium mb-2">Section Settings</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="section_subtitle">Subtitle</label>
                    <input type="text" name="section_subtitle" id="section_subtitle" value="<?php echo htmlspecialchars($training_section['subtitle']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="section_title">Title</label>
                    <input type="text" name="section_title" id="section_title" value="<?php echo htmlspecialchars($training_section['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="section_description">Description</label>
                    <textarea name="section_description" id="section_description" rows="4" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($training_section['description']); ?></textarea>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Background Image</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="image">Image (PNG, JPG, JPEG, max 2MB)</label>
                    <input type="file" name="image" id="image" accept="image/png,image/jpeg,image/jpg" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <?php if (!empty($training_section['image'])): ?>
                        <img id="image-preview" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $training_section['image']); ?>" alt="Image Preview" class="mt-2 max-w-xs h-auto">
                    <?php else: ?>
                        <img id="image-preview" src="" alt="Image Preview" class="mt-2 max-w-xs h-auto hidden">
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Training Programs</h3>
                <div class="space-y-4">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <div class="border p-4 rounded">
                            <h4 class="text-md font-medium mb-2">Program <?php echo $i; ?></h4>
                            <label class="block text-gray-700 font-medium mb-1" for="program_name<?php echo $i; ?>">Program Name</label>
                            <input type="text" name="program_name<?php echo $i; ?>" id="program_name<?php echo $i; ?>" value="<?php echo htmlspecialchars($programs[$i]['program_name'] ?? ''); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
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

    document.getElementById('training-school-form').addEventListener('submit', function(e) {
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