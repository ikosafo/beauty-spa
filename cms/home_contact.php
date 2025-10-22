<?php
// cms/contact1.php

require_once '../config.php';

$page_title = 'Edit Contact1 Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch contact1 data
$contact1_section = [];
$timeslots = [];
$query = "SELECT * FROM ws_contact1 WHERE id IN (1, 2, 3, 4) ORDER BY id";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
} else {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $contact1_section = $row;
        } else {
            $timeslots[$row['slot_order']] = $row;
        }
    }
}
if (empty($contact1_section)) {
    $contact1_section = [
        'id' => 1,
        'type' => 'section',
        'image' => 'images/bg-image/col-bgimage-4.jpg',
        'subtitle' => 'Contactus',
        'title' => 'GET A FREE QUOTES'
    ];
}
if (empty($timeslots)) {
    $timeslots = [
        1 => [
            'id' => 2,
            'type' => 'timeslot',
            'time_range' => '9:00 am – 11:00 am',
            'spaces_available' => '10 spaces available',
            'slot_order' => 1
        ],
        2 => [
            'id' => 3,
            'type' => 'timeslot',
            'time_range' => '11:00 am – 1:00 am',
            'spaces_available' => '10 spaces available',
            'slot_order' => 2
        ],
        3 => [
            'id' => 4,
            'type' => 'timeslot',
            'time_range' => '4:00 am – 6:00 am',
            'spaces_available' => '10 spaces available',
            'slot_order' => 3
        ]
    ];
}
error_log('Fetched contact1 data: ' . print_r(['contact1_section' => $contact1_section, 'timeslots' => $timeslots], true));

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

        // Validate section fields
        if (empty($section_subtitle) || empty($section_title)) {
            $errors[] = 'All section title fields are required.';
            error_log('Validation failed: Missing section title fields');
        }

        // Image data
        $image_data = ['image' => $contact1_section['image']];
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
            $max_size = 2 * 1024 * 1024; // 2MB
            $upload_dir = './uploads/contact1/';
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
                $image_filename = 'contact1_bg_' . time() . '.' . $ext;
                $image_path = $upload_dir . $image_filename;

                // Move uploaded file
                if (!move_uploaded_file($image_file['tmp_name'], $image_path)) {
                    $errors[] = 'Failed to move uploaded file for image.';
                    error_log('Failed to move uploaded file to: ' . $image_path);
                } else {
                    $image_data['image'] = 'uploads/contact1/' . $image_filename;
                    error_log('Image uploaded successfully: ' . $image_data['image']);
                }
            }
        }

        // Time slot data
        $timeslot_data = [];
        for ($i = 1; $i <= 3; $i++) {
            $timeslot_data[$i] = [
                'time_range' => $_POST["timeslot_range{$i}"] ?? '',
                'spaces_available' => $_POST["timeslot_spaces{$i}"] ?? ''
            ];

            // Validate time slot fields
            if (empty($timeslot_data[$i]['time_range']) || empty($timeslot_data[$i]['spaces_available'])) {
                $errors[] = "All fields for Time Slot $i are required.";
                error_log("Validation failed: Missing fields for Time Slot $i");
            }
        }

        // Proceed with database update if no errors
        if (empty($errors)) {
            // Update section title and image
            $query = "SELECT COUNT(*) AS count FROM ws_contact1 WHERE id = 1";
            $stmt = $mysqli->prepare($query);
            $stmt->execute();
            $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
            $stmt->close();

            if ($row_exists) {
                $query = "UPDATE ws_contact1 SET image = ?, subtitle = ?, title = ? WHERE id = 1";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $errors[] = 'Prepare failed for section update: ' . $mysqli->error;
                    error_log('Prepare failed for section update: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('sss', $image_data['image'], $section_subtitle, $section_title);
                    if (!$stmt->execute()) {
                        $errors[] = 'Update failed for section: ' . $stmt->error;
                        error_log('Update failed for section: ' . $stmt->error);
                    } else {
                        error_log('Section update successful, affected rows: ' . $stmt->affected_rows);
                    }
                    $stmt->close();
                }
            } else {
                $query = "INSERT INTO ws_contact1 (id, type, image, subtitle, title) VALUES (1, 'section', ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $errors[] = 'Prepare failed for section insert: ' . $mysqli->error;
                    error_log('Prepare failed for section insert: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('sss', $image_data['image'], $section_subtitle, $section_title);
                    if (!$stmt->execute()) {
                        $errors[] = 'Insert failed for section: ' . $stmt->error;
                        error_log('Insert failed for section: ' . $stmt->error);
                    } else {
                        error_log('Section insert successful');
                    }
                    $stmt->close();
                }
            }

            // Update time slots
            for ($i = 1; $i <= 3; $i++) {
                $slot_id = $i + 1; // IDs 2-4
                $query = "SELECT COUNT(*) AS count FROM ws_contact1 WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('i', $slot_id);
                $stmt->execute();
                $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
                $stmt->close();

                if ($row_exists) {
                    $query = "UPDATE ws_contact1 SET time_range = ?, spaces_available = ?, slot_order = ? WHERE id = ?";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Time Slot $i update: " . $mysqli->error;
                        error_log("Prepare failed for Time Slot $i update: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('ssii', $timeslot_data[$i]['time_range'], $timeslot_data[$i]['spaces_available'], $i, $slot_id);
                        if (!$stmt->execute()) {
                            $errors[] = "Update failed for Time Slot $i: " . $stmt->error;
                            error_log("Update failed for Time Slot $i: " . $stmt->error);
                        } else {
                            error_log("Update successful for Time Slot $i, affected rows: " . $stmt->affected_rows);
                        }
                        $stmt->close();
                    }
                } else {
                    $query = "INSERT INTO ws_contact1 (id, type, time_range, spaces_available, slot_order) VALUES (?, 'timeslot', ?, ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Time Slot $i insert: " . $mysqli->error;
                        error_log("Prepare failed for Time Slot $i insert: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('issi', $slot_id, $timeslot_data[$i]['time_range'], $timeslot_data[$i]['spaces_available'], $i);
                        if (!$stmt->execute()) {
                            $errors[] = "Insert failed for Time Slot $i: " . $stmt->error;
                            error_log("Insert failed for Time Slot $i: " . $stmt->error);
                        } else {
                            error_log("Insert successful for Time Slot $i");
                        }
                        $stmt->close();
                    }
                }
            }

            // Refresh data
            if (empty($errors)) {
                $query = "SELECT * FROM ws_contact1 WHERE id IN (1, 2, 3, 4) ORDER BY id";
                $result = $mysqli->query($query);
                if (!$result) {
                    $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                    error_log('Refresh query failed: ' . $mysqli->error);
                } else {
                    $contact1_section = [];
                    $timeslots = [];
                    while ($row = $result->fetch_assoc()) {
                        if ($row['type'] === 'section') {
                            $contact1_section = $row;
                        } else {
                            $timeslots[$row['slot_order']] = $row;
                        }
                    }
                    error_log('Contact1 data refreshed: ' . print_r(['contact1_section' => $contact1_section, 'timeslots' => $timeslots], true));
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Contact1 Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="contact1-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <h3 class="text-lg font-medium mb-2">Section Title Settings</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="section_subtitle">Subtitle</label>
                    <input type="text" name="section_subtitle" id="section_subtitle" value="<?php echo htmlspecialchars($contact1_section['subtitle']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="section_title">Title</label>
                    <input type="text" name="section_title" id="section_title" value="<?php echo htmlspecialchars($contact1_section['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Background Image</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="image">Image (PNG, JPG, JPEG, max 2MB)</label>
                    <input type="file" name="image" id="image" accept="image/png,image/jpeg,image/jpg" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <?php if (!empty($contact1_section['image'])): ?>
                        <img id="image-preview" src="<?php echo htmlspecialchars(URLROOT . '/' . $contact1_section['image']); ?>" alt="Image Preview" class="mt-2 max-w-xs h-auto">
                    <?php else: ?>
                        <img id="image-preview" src="" alt="Image Preview" class="mt-2 max-w-xs h-auto hidden">
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Time Slots</h3>
                <div class="space-y-4">
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="border p-4 rounded">
                            <h4 class="text-md font-medium mb-2">Time Slot <?php echo $i; ?></h4>
                            <label class="block text-gray-700 font-medium mb-1" for="timeslot_range<?php echo $i; ?>">Time Range (e.g., 9:00 am – 11:00 am)</label>
                            <input type="text" name="timeslot_range<?php echo $i; ?>" id="timeslot_range<?php echo $i; ?>" value="<?php echo htmlspecialchars($timeslots[$i]['time_range']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                            <label class="block text-gray-700 font-medium mb-1" for="timeslot_spaces<?php echo $i; ?>">Spaces Available (e.g., 10 spaces available)</label>
                            <input type="text" name="timeslot_spaces<?php echo $i; ?>" id="timeslot_spaces<?php echo $i; ?>" value="<?php echo htmlspecialchars($timeslots[$i]['spaces_available']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
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

    document.getElementById('contact1-form').addEventListener('submit', function(e) {
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