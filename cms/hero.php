<?php
// cms/hero.php

require_once '../config.php';

$page_title = 'Edit Hero Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
/* ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log'); */

// Fetch hero data
$query = "SELECT * FROM ws_hero WHERE id = 1";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
}
$hero = $result->fetch_assoc();
if (!$hero) {
    $hero = ['title' => 'Hero Section', 'subtitle' => 'Your catchy subtitle here', 'hero_image' => null];
}
error_log('Fetched hero data: ' . print_r($hero, true));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Form submitted with POST method');
    error_log('$_FILES: ' . print_r($_FILES, true));
    error_log('$_POST: ' . print_r($_POST, true));
    error_log('Request headers: ' . print_r(getallheaders(), true));

    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        error_log('CSRF validation failed');
    } elseif (!$hero['hero_image'] && (!isset($_FILES['hero_image']) || $_FILES['hero_image']['error'] !== 0)) {
        // Require image only if no existing image in the database
        $upload_error = $_FILES['hero_image']['error'] ?? 'No file uploaded';
        $_SESSION['error'] = 'Image upload is required when no existing image is set: ' . $upload_error;
        error_log('Image upload missing or failed: ' . $upload_error);
    } else {
        $title = $mysqli->real_escape_string($_POST['title'] ?? '');
        $subtitle = $mysqli->real_escape_string($_POST['subtitle'] ?? '');
        $hero_image = $hero['hero_image'] ?? null;

        // Handle image upload if provided
        if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === 0) {
            $file_tmp = $_FILES['hero_image']['tmp_name'];
            $file_type = $_FILES['hero_image']['type'];
            $file_size = $_FILES['hero_image']['size'];
            $file_name = basename($_FILES['hero_image']['name']);

            error_log("File details: tmp_name=$file_tmp, type=$file_type, size=$file_size, name=$file_name");

            // Validate file type and size
            if (strpos($file_type, 'image/') !== 0) {
                $_SESSION['error'] = 'Invalid file type. Please upload an image';
                error_log('Invalid file type: ' . $file_type);
            } elseif ($file_size > 5 * 1024 * 1024) { // Limit to 5MB
                $_SESSION['error'] = 'File size exceeds 5MB limit';
                error_log('File size too large: ' . $file_size);
            } elseif (!is_uploaded_file($file_tmp)) {
                $_SESSION['error'] = 'File is not a valid uploaded file';
                error_log('Invalid uploaded file: ' . $file_tmp);
            } else {
                $upload_dir = 'C:/wamp64/www/taymac/cms/uploads/hero/';
                $relative_dir = 'cms/uploads/hero/';

                // Ensure uploads directory exists
                if (!is_dir($upload_dir)) {
                    if (!mkdir($upload_dir, 0777, true)) {
                        $_SESSION['error'] = 'Failed to create uploads directory';
                        error_log('Failed to create uploads directory: ' . $upload_dir);
                    } else {
                        error_log('Created uploads directory: ' . $upload_dir);
                    }
                }

                // Check directory writable
                if (!is_writable($upload_dir)) {
                    $_SESSION['error'] = 'Uploads directory is not writable';
                    error_log('Uploads directory not writable: ' . $upload_dir);
                } else {
                    // Generate unique file name
                    $hero_image = $relative_dir . time() . '_' . preg_replace('/[^A-Za-z0-9\.\-_]/', '', $file_name);
                    $full_path = $upload_dir . basename($hero_image);

                    error_log("Attempting to save image to: $full_path");

                    // Delete existing image if it exists
                    if ($hero['hero_image'] && file_exists($hero['hero_image'])) {
                        if (unlink($hero['hero_image'])) {
                            error_log('Deleted old image: ' . $hero['hero_image']);
                        } else {
                            $_SESSION['error'] = 'Failed to delete old image';
                            error_log('Failed to delete old image: ' . $hero['hero_image']);
                        }
                    }

                    // Move uploaded file
                    if (!move_uploaded_file($file_tmp, $full_path)) {
                        $_SESSION['error'] = 'Failed to save image to uploads directory';
                        error_log('Failed to move uploaded file to: ' . $full_path);
                        $hero_image = $hero['hero_image'] ?? null; // Revert to old image
                    } else {
                        error_log('Image saved successfully: ' . $hero_image);
                    }
                }
            }
        }

        // Only proceed with database update if no errors
        if (!isset($_SESSION['error'])) {
            // Check if row exists
            $query = "SELECT COUNT(*) AS count FROM ws_hero WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Check query failed: ' . $mysqli->error;
                error_log('Check query failed: ' . $mysqli->error);
            }
            $row_exists = $result->fetch_assoc()['count'] > 0;
            error_log('Row exists: ' . ($row_exists ? 'Yes' : 'No'));

            if ($row_exists) {
                // Update existing row
                $query = "UPDATE ws_hero SET title = ?, subtitle = ?, hero_image = ? WHERE id = 1";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('sss', $title, $subtitle, $hero_image);
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
                $query = "INSERT INTO ws_hero (id, title, subtitle, hero_image) VALUES (?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $id = 1;
                    $stmt->bind_param('isss', $id, $title, $subtitle, $hero_image);
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
            $query = "SELECT * FROM ws_hero WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                error_log('Refresh query failed: ' . $mysqli->error);
            } else {
                $hero = $result->fetch_assoc();
                error_log('Hero data refreshed: ' . print_r($hero, true));
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Hero Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="hero-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="title">Title</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($hero['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="subtitle">Subtitle</label>
                <input type="text" name="subtitle" id="subtitle" value="<?php echo htmlspecialchars($hero['subtitle']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="hero_image">Hero Image<?php echo !$hero['hero_image'] ? ' (Required)' : ' (Optional)'; ?></label>
                <input type="file" name="hero_image" id="hero_image" accept="image/*" class="w-full p-2 border rounded" <?php echo !$hero['hero_image'] ? 'required' : ''; ?>>
                <?php if (isset($hero['hero_image']) && $hero['hero_image']): ?>
                    <p class="mt-2 text-gray-600">Current Image:</p>
                    <img src="<?php echo htmlspecialchars(URLROOT . $hero['hero_image']); ?>" alt="Hero Image Preview" class="w-full max-w-md h-auto rounded border mt-1">
                <?php endif; ?>
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

    document.getElementById('hero-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        const fileInput = document.getElementById('hero_image');
        <?php if (!$hero['hero_image']): ?>
        if (!fileInput.files.length) {
            console.log('No file selected');
            alert('Please select an image');
            e.preventDefault();
            return;
        }
        <?php endif; ?>
        console.log('File selected:', fileInput.files[0]?.name || 'No new file', 'Size:', fileInput.files[0]?.size || 'N/A');
        document.getElementById('submit-button').disabled = true;
    });
</script>

<?php
include 'includes/footer.php';
?>