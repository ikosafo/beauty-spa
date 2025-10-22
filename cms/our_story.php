<?php
// cms/story.php

require_once '../config.php';

$page_title = 'Edit Our Story Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch story data
$query = "SELECT * FROM ws_story WHERE id = 1";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
}
$story = $result->fetch_assoc();
if (!$story) {
    $story = [
        'paragraph1' => 'We prioritize the protection of both the environment and individuals, ensuring that every workplace remains safe and productive',
        'paragraph2' => 'We aim to share our commitment and enthusiasm with those around us, fostering a collaborative spirit and encouraging others to embrace our value',
        'paragraph3' => 'We embrace a CAN DO approach to tackle any challenges that come our way',
        'paragraph4' => 'We are continually seeking opportunities to improve ourselves and our systems',
        'youtube_url' => 'https://www.youtube.com'
    ];
}
error_log('Fetched story data: ' . print_r($story, true));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Form submitted with POST method');
    error_log('$_POST: ' . print_r($_POST, true));
    error_log('Request headers: ' . print_r(getallheaders(), true));

    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        error_log('CSRF validation failed');
    } else {
        // Use raw POST values (prepared statements handle escaping)
        $paragraph1 = $_POST['paragraph1'] ?? '';
        $paragraph2 = $_POST['paragraph2'] ?? '';
        $paragraph3 = $_POST['paragraph3'] ?? '';
        $paragraph4 = $_POST['paragraph4'] ?? '';
        $youtube_url = $_POST['youtube_url'] ?? '';

        // Validate required fields
        if (empty($paragraph1) || empty($paragraph2) || empty($paragraph3) || empty($paragraph4) || empty($youtube_url)) {
            $_SESSION['error'] = 'All fields are required';
            error_log('Validation failed: Missing required fields');
        } else {
            // Check if row exists
            $query = "SELECT COUNT(*) AS count FROM ws_story WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Check query failed: ' . $mysqli->error;
                error_log('Check query failed: ' . $mysqli->error);
            }
            $row_exists = $result->fetch_assoc()['count'] > 0;
            error_log('Row exists: ' . ($row_exists ? 'Yes' : 'No'));

            if ($row_exists) {
                // Update existing row
                $query = "UPDATE ws_story SET paragraph1 = ?, paragraph2 = ?, paragraph3 = ?, paragraph4 = ?, youtube_url = ? WHERE id = 1";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('sssss', $paragraph1, $paragraph2, $paragraph3, $paragraph4, $youtube_url);
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
                $query = "INSERT INTO ws_story (id, paragraph1, paragraph2, paragraph3, paragraph4, youtube_url) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $id = 1;
                    $stmt->bind_param('isssss', $id, $paragraph1, $paragraph2, $paragraph3, $paragraph4, $youtube_url);
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
            $query = "SELECT * FROM ws_story WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                error_log('Refresh query failed: ' . $mysqli->error);
            } else {
                $story = $result->fetch_assoc();
                error_log('Story data refreshed: ' . print_r($story, true));
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Story Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="story-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <h3 class="text-lg font-medium mb-2">Our Story Content</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="paragraph1">Paragraph 1</label>
                    <textarea name="paragraph1" id="paragraph1" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($story['paragraph1']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="paragraph2">Paragraph 2</label>
                    <textarea name="paragraph2" id="paragraph2" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($story['paragraph2']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="paragraph3">Paragraph 3</label>
                    <textarea name="paragraph3" id="paragraph3" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($story['paragraph3']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="paragraph4">Paragraph 4</label>
                    <textarea name="paragraph4" id="paragraph4" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($story['paragraph4']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="youtube_url">YouTube URL</label>
                    <input type="url" name="youtube_url" id="youtube_url" value="<?php echo htmlspecialchars($story['youtube_url']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
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

    document.getElementById('story-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });
</script>

<?php
include 'includes/footer.php';
?>