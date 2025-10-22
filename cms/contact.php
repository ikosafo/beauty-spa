<?php
// cms/contact.php

require_once '../config.php';

$page_title = 'Edit Contact Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch contact data
$query = "SELECT * FROM ws_contact WHERE id = 1";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
}
$contact = $result->fetch_assoc();
if (!$contact) {
    $contact = [
        'map_iframe_src' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.41412345!2d-0.1770726!3d5.6128054!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fbb123456789!2sSenchi+St,+Accra!5e0!3m2!1sen!2sus!4v1633012345678!5m2!1sen!2sus&maptype=satellite',
        'phone1' => '+233 (0) 245-710-614',
        'phone2' => '+233 (0) 302-789-025',
        'email' => 'info@taymac.net',
        'address' => 'Ground Floor Le Pierre, 14 Choice Close off Senchi Street, Airport Residential Area, Accra',
        'working_hours' => 'Mon-Sat: 9am to 6pm',
        'logo' => 'images/logo.png',
        'facebook' => '',
        'x' => '',
        'linkedin' => '',
        'instagram' => ''
    ];
} else {
    // Ensure social media and working hours fields are not NULL
    $contact['facebook'] = $contact['facebook'] ?? '';
    $contact['x'] = $contact['x'] ?? '';
    $contact['linkedin'] = $contact['linkedin'] ?? '';
    $contact['instagram'] = $contact['instagram'] ?? '';
    $contact['working_hours'] = $contact['working_hours'] ?? 'Mon-Sat: 9am to 6pm';
}
error_log('Fetched contact data: ' . print_r($contact, true));

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
        $map_iframe_src = $_POST['map_iframe_src'] ?? '';
        $phone1 = $_POST['phone1'] ?? '';
        $phone2 = $_POST['phone2'] ?? '';
        $email = $_POST['email'] ?? '';
        $address = $_POST['address'] ?? '';
        $working_hours = $_POST['working_hours'] ?? '';
        $facebook = $_POST['facebook'] ?? '';
        $x = $_POST['x'] ?? '';
        $linkedin = $_POST['linkedin'] ?? '';
        $instagram = $_POST['instagram'] ?? '';
        $logo = $contact['logo']; // Default to existing logo

        // Validate required fields
        if (empty($map_iframe_src) || empty($phone1) || empty($phone2) || empty($email) || empty($address) || empty($working_hours)) {
            $_SESSION['error'] = 'All fields except logo and social media links are required';
            error_log('Validation failed: Missing required fields');
        } else {
            // Handle logo upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
                $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
                $max_size = 2 * 1024 * 1024; // 2MB
                $upload_dir = './uploads/logo/';
                $logo_file = $_FILES['logo'];

                // Validate file type and size
                if (!in_array($logo_file['type'], $allowed_types)) {
                    $_SESSION['error'] = 'Invalid file type. Only PNG, JPG, and JPEG are allowed.';
                    error_log('Invalid file type: ' . $logo_file['type']);
                } elseif ($logo_file['size'] > $max_size) {
                    $_SESSION['error'] = 'File size exceeds 2MB limit.';
                    error_log('File size too large: ' . $logo_file['size']);
                } elseif ($logo_file['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['error'] = 'File upload error: ' . $logo_file['error'];
                    error_log('File upload error: ' . $logo_file['error']);
                } else {
                    // Generate unique filename to avoid overwrites
                    $ext = pathinfo($logo_file['name'], PATHINFO_EXTENSION);
                    $logo_filename = 'logo_' . time() . '.' . $ext;
                    $logo_path = $upload_dir . $logo_filename;

                    // Move uploaded file
                    if (!move_uploaded_file($logo_file['tmp_name'], $logo_path)) {
                        $_SESSION['error'] = 'Failed to move uploaded file.';
                        error_log('Failed to move uploaded file to: ' . $logo_path);
                    } else {
                        $logo = 'uploads/logo/' . $logo_filename; // Store relative path
                        error_log('Logo uploaded successfully: ' . $logo);
                    }
                }
            }

            // Proceed with database update if no errors
            if (!isset($_SESSION['error'])) {
                // Check if row exists
                $query = "SELECT COUNT(*) AS count FROM ws_contact WHERE id = 1";
                $result = $mysqli->query($query);
                if (!$result) {
                    $_SESSION['error'] = 'Check query failed: ' . $mysqli->error;
                    error_log('Check query failed: ' . $mysqli->error);
                }
                $row_exists = $result->fetch_assoc()['count'] > 0;
                error_log('Row exists: ' . ($row_exists ? 'Yes' : 'No'));

                if ($row_exists) {
                    // Update existing row
                    $query = "UPDATE ws_contact SET map_iframe_src = ?, phone1 = ?, phone2 = ?, email = ?, address = ?, working_hours = ?, logo = ?, facebook = ?, x = ?, linkedin = ?, instagram = ? WHERE id = 1";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                        error_log('Prepare failed: ' . $mysqli->error);
                    } else {
                        $stmt->bind_param('sssssssssss', $map_iframe_src, $phone1, $phone2, $email, $address, $working_hours, $logo, $facebook, $x, $linkedin, $instagram);
                        if (!$stmt->execute()) {
                            $_SESSION['error'] = 'Update failed: ' . $stmt->error;
                            error_log('Update failed: ' . $stmt->error);
                        } else {
                            error_log('Update successful, affected rows: ' . $stmt->affected_rows);
                        }
                        $stmt->close();
                    }
                } else {
                    // Insert new row (omit id due to auto-increment)
                    $query = "INSERT INTO ws_contact (map_iframe_src, phone1, phone2, email, address, working_hours, logo, facebook, x, linkedin, instagram) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                        error_log('Prepare failed: ' . $mysqli->error);
                    } else {
                        $stmt->bind_param('sssssssssss', $map_iframe_src, $phone1, $phone2, $email, $address, $working_hours, $logo, $facebook, $x, $linkedin, $instagram);
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
                $query = "SELECT * FROM ws_contact WHERE id = 1";
                $result = $mysqli->query($query);
                if (!$result) {
                    $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                    error_log('Refresh query failed: ' . $mysqli->error);
                } else {
                    $contact = $result->fetch_assoc();
                    // Ensure social media and working hours fields are not NULL
                    $contact['facebook'] = $contact['facebook'] ?? '';
                    $contact['x'] = $contact['x'] ?? '';
                    $contact['linkedin'] = $contact['linkedin'] ?? '';
                    $contact['instagram'] = $contact['instagram'] ?? '';
                    $contact['working_hours'] = $contact['working_hours'] ?? 'Mon-Sat: 9am to 6pm';
                    error_log('Contact data refreshed: ' . print_r($contact, true));
                }
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Contact Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="contact-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <h3 class="text-lg font-medium mb-2">Contact Information</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="map_iframe_src">Google Maps Iframe Source URL</label>
                    <textarea name="map_iframe_src" id="map_iframe_src" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($contact['map_iframe_src']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="phone1">Phone Number 1</label>
                    <input type="text" name="phone1" id="phone1" value="<?php echo htmlspecialchars($contact['phone1']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="phone2">Phone Number 2</label>
                    <input type="text" name="phone2" id="phone2" value="<?php echo htmlspecialchars($contact['phone2']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="email">Email Address</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($contact['email']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="address">Physical Address</label>
                    <textarea name="address" id="address" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($contact['address']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="working_hours">Working Hours</label>
                    <input type="text" name="working_hours" id="working_hours" value="<?php echo htmlspecialchars($contact['working_hours']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="logo">Logo (PNG, JPG, JPEG, max 2MB)</label>
                    <input type="file" name="logo" id="logo" accept="image/png,image/jpeg,image/jpg" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <?php if (!empty($contact['logo'])): ?>
                        <img id="logo-preview" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $contact['logo']); ?>" alt="Logo Preview" class="mt-2 max-w-xs h-auto">
                    <?php else: ?>
                        <img id="logo-preview" src="" alt="Logo Preview" class="mt-2 max-w-xs h-auto hidden">
                    <?php endif; ?>
                    <label class="block text-gray-700 font-medium mb-1" for="facebook">Facebook URL (optional)</label>
                    <input type="url" name="facebook" id="facebook" value="<?php echo htmlspecialchars($contact['facebook'] ?? ''); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <label class="block text-gray-700 font-medium mb-1" for="x">X URL (optional)</label>
                    <input type="url" name="x" id="x" value="<?php echo htmlspecialchars($contact['x'] ?? ''); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <label class="block text-gray-700 font-medium mb-1" for="linkedin">LinkedIn URL (optional)</label>
                    <input type="url" name="linkedin" id="linkedin" value="<?php echo htmlspecialchars($contact['linkedin'] ?? ''); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <label class="block text-gray-700 font-medium mb-1" for="instagram">Instagram URL (optional)</label>
                    <input type="url" name="instagram" id="instagram" value="<?php echo htmlspecialchars($contact['instagram'] ?? ''); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
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

    document.getElementById('contact-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });

    // Logo preview
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('logo-preview');
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