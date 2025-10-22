<?php
// cms/approach.php

require_once '../config.php';

$page_title = 'Edit Our Approach Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch approach data
$query = "SELECT * FROM ws_approach WHERE id = 1";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
}
$approach = $result->fetch_assoc();
if (!$approach) {
    $approach = [
        'title' => 'We embrace a proactive CAN DO mindset, tackling challenges head-on.',
        'subtitle' => 'Our Approach',
        'step1_title' => 'Find Your Ideal Property in Prime Locations.',
        'step1_description' => 'Discover your perfect property in highly sought-after areas, where convenience, comfort, and community align to provide the best living and investment opportunities.',
        'step1_icon' => 'fa-map-location',
        'step2_title' => 'Schedule a Visit with Our Expert Agents.',
        'step2_description' => 'Arrange an appointment at your convenience with one of our dedicated agents. We\'re prepared to offer personalized insights and tailored solutions to help you make informed decisions.',
        'step2_icon' => 'fa-calendar-alt',
        'step3_title' => 'Experience Tailored Real Estate Solutions.',
        'step3_description' => 'Partner with our experienced team to receive customized real estate solutions designed to meet your specific goals, ensuring a seamless and successful property search or transaction.',
        'step3_icon' => 'fa-igloo'
    ];
}
error_log('Fetched approach data: ' . print_r($approach, true));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Form submitted with POST method');
    error_log('$_POST: ' . print_r($_POST, true));
    error_log('Request headers: ' . print_r(getallheaders(), true));

    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        error_log('CSRF validation failed');
    } else {
        // Sanitize input
        $title = $mysqli->real_escape_string($_POST['title'] ?? '');
        $subtitle = $mysqli->real_escape_string($_POST['subtitle'] ?? '');
        $step1_title = $mysqli->real_escape_string($_POST['step1_title'] ?? '');
        $step1_description = $mysqli->real_escape_string($_POST['step1_description'] ?? '');
        $step1_icon = $mysqli->real_escape_string($_POST['step1_icon'] ?? '');
        $step2_title = $mysqli->real_escape_string($_POST['step2_title'] ?? '');
        $step2_description = $mysqli->real_escape_string($_POST['step2_description'] ?? '');
        $step2_icon = $mysqli->real_escape_string($_POST['step2_icon'] ?? '');
        $step3_title = $mysqli->real_escape_string($_POST['step3_title'] ?? '');
        $step3_description = $mysqli->real_escape_string($_POST['step3_description'] ?? '');
        $step3_icon = $mysqli->real_escape_string($_POST['step3_icon'] ?? '');

        // Validate required fields
        if (empty($title) || empty($subtitle) || 
            empty($step1_title) || empty($step1_description) || empty($step1_icon) ||
            empty($step2_title) || empty($step2_description) || empty($step2_icon) ||
            empty($step3_title) || empty($step3_description) || empty($step3_icon)) {
            $_SESSION['error'] = 'All fields are required';
            error_log('Validation failed: Missing required fields');
        } else {
            // Check if row exists
            $query = "SELECT COUNT(*) AS count FROM ws_approach WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Check query failed: ' . $mysqli->error;
                error_log('Check query failed: ' . $mysqli->error);
            }
            $row_exists = $result->fetch_assoc()['count'] > 0;
            error_log('Row exists: ' . ($row_exists ? 'Yes' : 'No'));

            if ($row_exists) {
                // Update existing row
                $query = "UPDATE ws_approach SET title = ?, subtitle = ?, step1_title = ?, step1_description = ?, step1_icon = ?, step2_title = ?, step2_description = ?, step2_icon = ?, step3_title = ?, step3_description = ?, step3_icon = ? WHERE id = 1";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('sssssssssss', $title, $subtitle, $step1_title, $step1_description, $step1_icon, $step2_title, $step2_description, $step2_icon, $step3_title, $step3_description, $step3_icon);
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
                $query = "INSERT INTO ws_approach (id, title, subtitle, step1_title, step1_description, step1_icon, step2_title, step2_description, step2_icon, step3_title, step3_description, step3_icon) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $id = 1;
                    $stmt->bind_param('isssssssssss', $id, $title, $subtitle, $step1_title, $step1_description, $step1_icon, $step2_title, $step2_description, $step2_icon, $step3_title, $step3_description, $step3_icon);
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
            $query = "SELECT * FROM ws_approach WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                error_log('Refresh query failed: ' . $mysqli->error);
            } else {
                $approach = $result->fetch_assoc();
                error_log('Approach data refreshed: ' . print_r($approach, true));
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Approach Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="approach-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="title">Title</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($approach['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="subtitle">Subtitle</label>
                <input type="text" name="subtitle" id="subtitle" value="<?php echo htmlspecialchars($approach['subtitle']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Step 1</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="step1_title">Step 1 Title</label>
                    <input type="text" name="step1_title" id="step1_title" value="<?php echo htmlspecialchars($approach['step1_title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="step1_description">Step 1 Description</label>
                    <textarea name="step1_description" id="step1_description" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($approach['step1_description']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="step1_icon">Step 1 Icon</label>
                    <select name="step1_icon" id="step1_icon" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                        <option value="fa-map-location" <?php echo $approach['step1_icon'] === 'fa-map-location' ? 'selected' : ''; ?>>Map Location (fa-map-location)</option>
                        <option value="fa-calendar-alt" <?php echo $approach['step1_icon'] === 'fa-calendar-alt' ? 'selected' : ''; ?>>Calendar (fa-calendar-alt)</option>
                        <option value="fa-igloo" <?php echo $approach['step1_icon'] === 'fa-igloo' ? 'selected' : ''; ?>>Igloo (fa-igloo)</option>
                        <!-- Add more Font Awesome icons as needed -->
                    </select>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Step 2</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="step2_title">Step 2 Title</label>
                    <input type="text" name="step2_title" id="step2_title" value="<?php echo htmlspecialchars($approach['step2_title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="step2_description">Step 2 Description</label>
                    <textarea name="step2_description" id="step2_description" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($approach['step2_description']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="step2_icon">Step 2 Icon</label>
                    <select name="step2_icon" id="step2_icon" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                        <option value="fa-map-location" <?php echo $approach['step2_icon'] === 'fa-map-location' ? 'selected' : ''; ?>>Map Location (fa-map-location)</option>
                        <option value="fa-calendar-alt" <?php echo $approach['step2_icon'] === 'fa-calendar-alt' ? 'selected' : ''; ?>>Calendar (fa-calendar-alt)</option>
                        <option value="fa-igloo" <?php echo $approach['step2_icon'] === 'fa-igloo' ? 'selected' : ''; ?>>Igloo (fa-igloo)</option>
                        <!-- Add more Font Awesome icons as needed -->
                    </select>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Step 3</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="step3_title">Step 3 Title</label>
                    <input type="text" name="step3_title" id="step3_title" value="<?php echo htmlspecialchars($approach['step3_title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="step3_description">Step 3 Description</label>
                    <textarea name="step3_description" id="step3_description" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($approach['step3_description']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="step3_icon">Step 3 Icon</label>
                    <select name="step3_icon" id="step3_icon" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                        <option value="fa-map-location" <?php echo $approach['step3_icon'] === 'fa-map-location' ? 'selected' : ''; ?>>Map Location (fa-map-location)</option>
                        <option value="fa-calendar-alt" <?php echo $approach['step3_icon'] === 'fa-calendar-alt' ? 'selected' : ''; ?>>Calendar (fa-calendar-alt)</option>
                        <option value="fa-igloo" <?php echo $approach['step3_icon'] === 'fa-igloo' ? 'selected' : ''; ?>>Igloo (fa-igloo)</option>
                        <!-- Add more Font Awesome icons as needed -->
                    </select>
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

    document.getElementById('approach-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });
</script>

<?php
include 'includes/footer.php';
?>