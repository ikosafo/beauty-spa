<?php
// cms/clients.php

require_once '../config.php';

$page_title = 'Edit Clients Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch clients data
$query = "SELECT * FROM ws_clients WHERE id = 1";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
}
$clients = $result->fetch_assoc();
if (!$clients) {
    $clients = [
        'subtitle' => 'Our present and past clients',
        'title' => 'Trusted by countless organizations for our commitment to excellence',
        'list1_client1' => 'Cnergy Ghana Limited',
        'list1_client2' => 'Colgate Palmolive Ghana Limited',
        'list1_client3' => 'Best Assurance Company Limited',
        'list1_client4' => 'Special Investment Company Limited',
        'list1_client5' => 'China Pipeline Petroleum, Ghana Office',
        'list2_client1' => 'Novartis Pharma A.G.',
        'list2_client2' => 'Best Pensions Limited',
        'list2_client3' => 'Amec Foster Wheeler Ghana Office',
        'list2_client4' => 'Best Point Saving and Loans Company Limited',
        'list2_client5' => 'Louis Dreyfus Commodities - West Africa Office'
    ];
}
error_log('Fetched clients data: ' . print_r($clients, true));

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
        $subtitle = $_POST['subtitle'] ?? '';
        $title = $_POST['title'] ?? '';
        $list1_client1 = $_POST['list1_client1'] ?? '';
        $list1_client2 = $_POST['list1_client2'] ?? '';
        $list1_client3 = $_POST['list1_client3'] ?? '';
        $list1_client4 = $_POST['list1_client4'] ?? '';
        $list1_client5 = $_POST['list1_client5'] ?? '';
        $list2_client1 = $_POST['list2_client1'] ?? '';
        $list2_client2 = $_POST['list2_client2'] ?? '';
        $list2_client3 = $_POST['list2_client3'] ?? '';
        $list2_client4 = $_POST['list2_client4'] ?? '';
        $list2_client5 = $_POST['list2_client5'] ?? '';

        // Validate required fields
        if (empty($subtitle) || empty($title) || empty($list1_client1) || empty($list2_client1)) {
            $_SESSION['error'] = 'Subtitle, title, and the first client in each list are required';
            error_log('Validation failed: Missing required fields');
        } else {
            // Check if row exists
            $query = "SELECT COUNT(*) AS count FROM ws_clients WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Check query failed: ' . $mysqli->error;
                error_log('Check query failed: ' . $mysqli->error);
            }
            $row_exists = $result->fetch_assoc()['count'] > 0;
            error_log('Row exists: ' . ($row_exists ? 'Yes' : 'No'));

            if ($row_exists) {
                // Update existing row
                $query = "UPDATE ws_clients SET subtitle = ?, title = ?, list1_client1 = ?, list1_client2 = ?, list1_client3 = ?, list1_client4 = ?, list1_client5 = ?, list2_client1 = ?, list2_client2 = ?, list2_client3 = ?, list2_client4 = ?, list2_client5 = ? WHERE id = 1";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('ssssssssssss', $subtitle, $title, $list1_client1, $list1_client2, $list1_client3, $list1_client4, $list1_client5, $list2_client1, $list2_client2, $list2_client3, $list2_client4, $list2_client5);
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
                $query = "INSERT INTO ws_clients (id, subtitle, title, list1_client1, list1_client2, list1_client3, list1_client4, list1_client5, list2_client1, list2_client2, list2_client3, list2_client4, list2_client5) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $id = 1;
                    $stmt->bind_param('issssssssssss', $id, $subtitle, $title, $list1_client1, $list1_client2, $list1_client3, $list1_client4, $list1_client5, $list2_client1, $list2_client2, $list2_client3, $list2_client4, $list2_client5);
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
            $query = "SELECT * FROM ws_clients WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                error_log('Refresh query failed: ' . $mysqli->error);
            } else {
                $clients = $result->fetch_assoc();
                error_log('Clients data refreshed: ' . print_r($clients, true));
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Clients Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="clients-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="subtitle">Subtitle</label>
                <input type="text" name="subtitle" id="subtitle" value="<?php echo htmlspecialchars($clients['subtitle']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="title">Title</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($clients['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Client List 1</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="list1_client1">Client 1 (Required)</label>
                    <input type="text" name="list1_client1" id="list1_client1" value="<?php echo htmlspecialchars($clients['list1_client1']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="list1_client2">Client 2</label>
                    <input type="text" name="list1_client2" id="list1_client2" value="<?php echo htmlspecialchars($clients['list1_client2']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <label class="block text-gray-700 font-medium mb-1" for="list1_client3">Client 3</label>
                    <input type="text" name="list1_client3" id="list1_client3" value="<?php echo htmlspecialchars($clients['list1_client3']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <label class="block text-gray-700 font-medium mb-1" for="list1_client4">Client 4</label>
                    <input type="text" name="list1_client4" id="list1_client4" value="<?php echo htmlspecialchars($clients['list1_client4']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <label class="block text-gray-700 font-medium mb-1" for="list1_client5">Client 5</label>
                    <input type="text" name="list1_client5" id="list1_client5" value="<?php echo htmlspecialchars($clients['list1_client5']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Client List 2</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="list2_client1">Client 1 (Required)</label>
                    <input type="text" name="list2_client1" id="list2_client1" value="<?php echo htmlspecialchars($clients['list2_client1']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="list2_client2">Client 2</label>
                    <input type="text" name="list2_client2" id="list2_client2" value="<?php echo htmlspecialchars($clients['list2_client2']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <label class="block text-gray-700 font-medium mb-1" for="list2_client3">Client 3</label>
                    <input type="text" name="list2_client3" id="list2_client3" value="<?php echo htmlspecialchars($clients['list2_client3']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <label class="block text-gray-700 font-medium mb-1" for="list2_client4">Client 4</label>
                    <input type="text" name="list2_client4" id="list2_client4" value="<?php echo htmlspecialchars($clients['list2_client4']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <label class="block text-gray-700 font-medium mb-1" for="list2_client5">Client 5</label>
                    <input type="text" name="list2_client5" id="list2_client5" value="<?php echo htmlspecialchars($clients['list2_client5']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
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

    document.getElementById('clients-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });
</script>

<?php
include 'includes/footer.php';
?>