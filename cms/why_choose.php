<?php
// cms/why_choose.php

require_once '../config.php';

$page_title = 'Edit Why Choose Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch why choose data
$query = "SELECT * FROM ws_why_choose WHERE id = 1";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
}
$why_choose = $result->fetch_assoc();
if (!$why_choose) {
    $why_choose = [
        'title' => 'Why Choose TAYMAC',
        'reason1_title' => 'No Hidden Fees',
        'reason1_description' => 'We believe in complete transparency. With us, you can rest assured that there are no surprise costs or hidden charges. Every fee is clearly outlined upfront, so you know exactly what to expect. Our goal is to provide a seamless and trustworthy experience, ensuring you get the best value for your investment without any financial surprises.',
        'reason2_title' => 'Property Appraisal',
        'reason2_description' => 'Our expert property appraisal services help you make informed decisions, whether you\'re buying, selling, or renting. We provide accurate, up-to-date valuations based on local market trends and property conditions, ensuring you get the best possible insight into the true worth of any property. Trust our experienced team to guide you in making smart property investments.',
        'reason3_title' => 'Large Coverage',
        'reason3_description' => 'With an extensive portfolio that spans across multiple cities and regions, we offer unparalleled access to a wide range of properties. Whether you’re looking for a home in the heart of the city, a peaceful suburban retreat, or a prime investment opportunity in a growing market, our large coverage ensures we have options to meet every need and lifestyle across various locations.'
    ];
}
error_log('Fetched why choose data: ' . print_r($why_choose, true));

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
        $reason1_title = $mysqli->real_escape_string($_POST['reason1_title'] ?? '');
        $reason1_description = $mysqli->real_escape_string($_POST['reason1_description'] ?? '');
        $reason2_title = $mysqli->real_escape_string($_POST['reason2_title'] ?? '');
        $reason2_description = $mysqli->real_escape_string($_POST['reason2_description'] ?? '');
        $reason3_title = $mysqli->real_escape_string($_POST['reason3_title'] ?? '');
        $reason3_description = $mysqli->real_escape_string($_POST['reason3_description'] ?? '');

        // Validate required fields
        if (empty($title) || 
            empty($reason1_title) || empty($reason1_description) ||
            empty($reason2_title) || empty($reason2_description) ||
            empty($reason3_title) || empty($reason3_description)) {
            $_SESSION['error'] = 'All fields are required';
            error_log('Validation failed: Missing required fields');
        } else {
            // Check if row exists
            $query = "SELECT COUNT(*) AS count FROM ws_why_choose WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Check query failed: ' . $mysqli->error;
                error_log('Check query failed: ' . $mysqli->error);
            }
            $row_exists = $result->fetch_assoc()['count'] > 0;
            error_log('Row exists: ' . ($row_exists ? 'Yes' : 'No'));

            if ($row_exists) {
                // Update existing row
                $query = "UPDATE ws_why_choose SET title = ?, reason1_title = ?, reason1_description = ?, reason2_title = ?, reason2_description = ?, reason3_title = ?, reason3_description = ? WHERE id = 1";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('sssssss', $title, $reason1_title, $reason1_description, $reason2_title, $reason2_description, $reason3_title, $reason3_description);
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
                $query = "INSERT INTO ws_why_choose (id, title, reason1_title, reason1_description, reason2_title, reason2_description, reason3_title, reason3_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $id = 1;
                    $stmt->bind_param('isssssss', $id, $title, $reason1_title, $reason1_description, $reason2_title, $reason2_description, $reason3_title, $reason3_description);
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
            $query = "SELECT * FROM ws_why_choose WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                error_log('Refresh query failed: ' . $mysqli->error);
            } else {
                $why_choose = $result->fetch_assoc();
                error_log('Why choose data refreshed: ' . print_r($why_choose, true));
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Why Choose Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="why-choose-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="title">Title</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($why_choose['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Reason 1</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="reason1_title">Reason 1 Title</label>
                    <input type="text" name="reason1_title" id="reason1_title" value="<?php echo htmlspecialchars($why_choose['reason1_title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="reason1_description">Reason 1 Description</label>
                    <textarea name="reason1_description" rows="5"  id="reason1_description" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($why_choose['reason1_description']); ?></textarea>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Reason 2</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="reason2_title">Reason 2 Title</label>
                    <input type="text" name="reason2_title" id="reason2_title" value="<?php echo htmlspecialchars($why_choose['reason2_title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="reason2_description">Reason 2 Description</label>
                    <textarea name="reason2_description" rows="5" id="reason2_description" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($why_choose['reason2_description']); ?></textarea>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Reason 3</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="reason3_title">Reason 3 Title</label>
                    <input type="text" name="reason3_title" id="reason3_title" value="<?php echo htmlspecialchars($why_choose['reason3_title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="reason3_description">Reason 3 Description</label>
                    <textarea name="reason3_description" rows="5"  id="reason3_description" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required><?php echo htmlspecialchars($why_choose['reason3_description']); ?></textarea>
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

    document.getElementById('why-choose-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });
</script>

<?php
include 'includes/footer.php';
?>