<?php
// cms/testimonials.php

require_once '../config.php';

$page_title = 'Edit Testimonials Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch testimonials data
$query = "SELECT * FROM ws_testimonials WHERE id = 1";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
}
$testimonials = $result->fetch_assoc();
if (!$testimonials) {
    $testimonials = [
        'testimonial1_quote' => 'Your creative work is superb, much better than the original and exactly what we want. Also your project management skills on budgeting, scheduling, client communication, and follow-through. A professional job, start to finish.',
        'testimonial1_client' => 'Tina & James',
        'testimonial2_quote' => 'I declare a standing ovation!...I hope you know that I think you are fantastic, and I feel extremely lucky to have you. I can\'t tell you how much I have enjoyed working with you.',
        'testimonial2_client' => 'Mary',
        'testimonial3_quote' => 'Have we told you lately that we love you? Your thoroughness and fairness is awesome. You are so easy to work with.',
        'testimonial3_client' => 'Paul & Susan'
    ];
}
error_log('Fetched testimonials data: ' . print_r($testimonials, true));

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
        $testimonial1_quote = $_POST['testimonial1_quote'] ?? '';
        $testimonial1_client = $_POST['testimonial1_client'] ?? '';
        $testimonial2_quote = $_POST['testimonial2_quote'] ?? '';
        $testimonial2_client = $_POST['testimonial2_client'] ?? '';
        $testimonial3_quote = $_POST['testimonial3_quote'] ?? '';
        $testimonial3_client = $_POST['testimonial3_client'] ?? '';

        // Validate required fields
        if (empty($testimonial1_quote) || empty($testimonial1_client) ||
            empty($testimonial2_quote) || empty($testimonial2_client) ||
            empty($testimonial3_quote) || empty($testimonial3_client)) {
            $_SESSION['error'] = 'All fields are required';
            error_log('Validation failed: Missing required fields');
        } else {
            // Check if row exists
            $query = "SELECT COUNT(*) AS count FROM ws_testimonials WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Check query failed: ' . $mysqli->error;
                error_log('Check query failed: ' . $mysqli->error);
            }
            $row_exists = $result->fetch_assoc()['count'] > 0;
            error_log('Row exists: ' . ($row_exists ? 'Yes' : 'No'));

            if ($row_exists) {
                // Update existing row
                $query = "UPDATE ws_testimonials SET 
                    testimonial1_quote = ?, testimonial1_client = ?, 
                    testimonial2_quote = ?, testimonial2_client = ?, 
                    testimonial3_quote = ?, testimonial3_client = ? 
                    WHERE id = 1";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('ssssss', 
                        $testimonial1_quote, $testimonial1_client,
                        $testimonial2_quote, $testimonial2_client,
                        $testimonial3_quote, $testimonial3_client);
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
                $query = "INSERT INTO ws_testimonials (
                    id, testimonial1_quote, testimonial1_client, 
                    testimonial2_quote, testimonial2_client, 
                    testimonial3_quote, testimonial3_client
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $id = 1;
                    $stmt->bind_param('issssss', 
                        $id, $testimonial1_quote, $testimonial1_client,
                        $testimonial2_quote, $testimonial2_client,
                        $testimonial3_quote, $testimonial3_client);
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
            $query = "SELECT * FROM ws_testimonials WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                error_log('Refresh query failed: ' . $mysqli->error);
            } else {
                $testimonials = $result->fetch_assoc();
                error_log('Testimonials data refreshed: ' . print_r($testimonials, true));
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Testimonials Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="testimonials-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <h3 class="text-lg font-medium mb-2">Testimonial 1</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="testimonial1_quote">Quote</label>
                    <textarea name="testimonial1_quote" id="testimonial1_quote" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="5" required><?php echo htmlspecialchars($testimonials['testimonial1_quote']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="testimonial1_client">Client Name(s)</label>
                    <input type="text" name="testimonial1_client" id="testimonial1_client" value="<?php echo htmlspecialchars($testimonials['testimonial1_client']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Testimonial 2</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="testimonial2_quote">Quote</label>
                    <textarea name="testimonial2_quote" id="testimonial2_quote" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="5" required><?php echo htmlspecialchars($testimonials['testimonial2_quote']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="testimonial2_client">Client Name(s)</label>
                    <input type="text" name="testimonial2_client" id="testimonial2_client" value="<?php echo htmlspecialchars($testimonials['testimonial2_client']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Testimonial 3</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="testimonial3_quote">Quote</label>
                    <textarea name="testimonial3_quote" id="testimonial3_quote" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="5" required><?php echo htmlspecialchars($testimonials['testimonial3_quote']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="testimonial3_client">Client Name(s)</label>
                    <input type="text" name="testimonial3_client" id="testimonial3_client" value="<?php echo htmlspecialchars($testimonials['testimonial3_client']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
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

    document.getElementById('testimonials-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });
</script>

<?php
include 'includes/footer.php';
?>