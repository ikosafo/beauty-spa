<?php
// cms/processes.php

require_once '../config.php';

$page_title = 'Edit Processes Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch processes data
$processes_section = [];
$processes = [];
$query = "SELECT * FROM ws_processes WHERE id IN (1, 2, 3, 4) ORDER BY id";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
} else {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $processes_section = $row;
        } else {
            $processes[$row['process_order']] = $row;
        }
    }
}
if (empty($processes_section)) {
    $processes_section = [
        'id' => 1,
        'type' => 'section',
        'subtitle' => 'Welcome',
        'title' => 'LOOK WELNESS PROCESSES',
        'description' => 'Lorem Ipsum is simply dummy text of the printing and tdustry. Lorem Ipsum has been the industry’s standard dualley.'
    ];
}
if (empty($processes)) {
    $processes = [
        1 => [
            'id' => 2,
            'type' => 'process',
            'icon' => 'flaticon-hammam',
            'title' => 'Get A Free Quotes',
            'description' => 'Get full details about Spa treatments & other amenities here!',
            'process_order' => 1
        ],
        2 => [
            'id' => 3,
            'type' => 'process',
            'icon' => 'flaticon-massage-spa-body-treatment',
            'title' => 'Get A Free Quotes',
            'description' => 'Book your appointment at your suitable schedule & get notified!',
            'process_order' => 2
        ],
        3 => [
            'id' => 4,
            'type' => 'process',
            'icon' => 'flaticon-hair-cut',
            'title' => 'Get A Free Quotes',
            'description' => 'We always appreciate your valuable feedback over our service!',
            'process_order' => 3
        ]
    ];
}
error_log('Fetched processes data: ' . print_r(['processes_section' => $processes_section, 'processes' => $processes], true));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Form submitted with POST method');
    error_log('$_POST: ' . print_r($_POST, true));
    error_log('Request headers: ' . print_r(getallheaders(), true));

    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        error_log('CSRF validation failed');
    } else {
        $errors = [];

        // Section title data
        $section_subtitle = $_POST['section_subtitle'] ?? '';
        $section_title = $_POST['section_title'] ?? '';
        $section_description = $_POST['section_description'] ?? '';

        // Validate section fields
        if (empty($section_subtitle) || empty($section_title) || empty($section_description)) {
            $errors[] = 'All section title fields are required.';
            error_log('Validation failed: Missing section title fields');
        }

        // Process items data
        $process_data = [];
        for ($i = 1; $i <= 3; $i++) {
            $process_data[$i] = [
                'icon' => $_POST["process_icon{$i}"] ?? '',
                'title' => $_POST["process_title{$i}"] ?? '',
                'description' => $_POST["process_description{$i}"] ?? ''
            ];

            // Validate process fields
            if (empty($process_data[$i]['icon']) || empty($process_data[$i]['title']) || empty($process_data[$i]['description'])) {
                $errors[] = "All fields for Process $i are required.";
                error_log("Validation failed: Missing fields for Process $i");
            }
        }

        // Proceed with database update if no errors
        if (empty($errors)) {
            // Update section title
            $query = "SELECT COUNT(*) AS count FROM ws_processes WHERE id = 1";
            $stmt = $mysqli->prepare($query);
            $stmt->execute();
            $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
            $stmt->close();

            if ($row_exists) {
                $query = "UPDATE ws_processes SET subtitle = ?, title = ?, description = ? WHERE id = 1";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $errors[] = 'Prepare failed for section update: ' . $mysqli->error;
                    error_log('Prepare failed for section update: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('sss', $section_subtitle, $section_title, $section_description);
                    if (!$stmt->execute()) {
                        $errors[] = 'Update failed for section: ' . $stmt->error;
                        error_log('Update failed for section: ' . $stmt->error);
                    } else {
                        error_log('Section update successful, affected rows: ' . $stmt->affected_rows);
                    }
                    $stmt->close();
                }
            } else {
                $query = "INSERT INTO ws_processes (id, type, subtitle, title, description) VALUES (1, 'section', ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $errors[] = 'Prepare failed for section insert: ' . $mysqli->error;
                    error_log('Prepare failed for section insert: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('sss', $section_subtitle, $section_title, $section_description);
                    if (!$stmt->execute()) {
                        $errors[] = 'Insert failed for section: ' . $stmt->error;
                        error_log('Insert failed for section: ' . $stmt->error);
                    } else {
                        error_log('Section insert successful');
                    }
                    $stmt->close();
                }
            }

            // Update process items
            for ($i = 1; $i <= 3; $i++) {
                $process_id = $i + 1; // IDs 2-4
                $query = "SELECT COUNT(*) AS count FROM ws_processes WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('i', $process_id);
                $stmt->execute();
                $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
                $stmt->close();

                if ($row_exists) {
                    $query = "UPDATE ws_processes SET icon = ?, title = ?, description = ?, process_order = ? WHERE id = ?";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Process $i update: " . $mysqli->error;
                        error_log("Prepare failed for Process $i update: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('sssii', $process_data[$i]['icon'], $process_data[$i]['title'], $process_data[$i]['description'], $i, $process_id);
                        if (!$stmt->execute()) {
                            $errors[] = "Update failed for Process $i: " . $stmt->error;
                            error_log("Update failed for Process $i: " . $stmt->error);
                        } else {
                            error_log("Update successful for Process $i, affected rows: " . $stmt->affected_rows);
                        }
                        $stmt->close();
                    }
                } else {
                    $query = "INSERT INTO ws_processes (id, type, icon, title, description, process_order) VALUES (?, 'process', ?, ?, ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Process $i insert: " . $mysqli->error;
                        error_log("Prepare failed for Process $i insert: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('isssi', $process_id, $process_data[$i]['icon'], $process_data[$i]['title'], $process_data[$i]['description'], $i);
                        if (!$stmt->execute()) {
                            $errors[] = "Insert failed for Process $i: " . $stmt->error;
                            error_log("Insert failed for Process $i: " . $stmt->error);
                        } else {
                            error_log("Insert successful for Process $i");
                        }
                        $stmt->close();
                    }
                }
            }

            // Refresh data
            if (empty($errors)) {
                $query = "SELECT * FROM ws_processes WHERE id IN (1, 2, 3, 4) ORDER BY id";
                $result = $mysqli->query($query);
                if (!$result) {
                    $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                    error_log('Refresh query failed: ' . $mysqli->error);
                } else {
                    $processes_section = [];
                    $processes = [];
                    while ($row = $result->fetch_assoc()) {
                        if ($row['type'] === 'section') {
                            $processes_section = $row;
                        } else {
                            $processes[$row['process_order']] = $row;
                        }
                    }
                    error_log('Processes data refreshed: ' . print_r(['processes_section' => $processes_section, 'processes' => $processes], true));
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Processes Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="processes-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <h3 class="text-lg font-medium mb-2">Section Title Settings</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="section_subtitle">Subtitle</label>
                    <input type="text" name="section_subtitle" id="section_subtitle" value="<?php echo htmlspecialchars($processes_section['subtitle']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="section_title">Title</label>
                    <input type="text" name="section_title" id="section_title" value="<?php echo htmlspecialchars($processes_section['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="section_description">Description</label>
                    <textarea name="section_description" id="section_description" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($processes_section['description']); ?></textarea>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Process Items</h3>
                <div class="space-y-4">
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="border p-4 rounded">
                            <h4 class="text-md font-medium mb-2">Process <?php echo $i; ?></h4>
                            <label class="block text-gray-700 font-medium mb-1" for="process_icon<?php echo $i; ?>">Icon Class (e.g., flaticon-hammam)</label>
                            <input type="text" name="process_icon<?php echo $i; ?>" id="process_icon<?php echo $i; ?>" value="<?php echo htmlspecialchars($processes[$i]['icon']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                            <label class="block text-gray-700 font-medium mb-1" for="process_title<?php echo $i; ?>">Title</label>
                            <input type="text" name="process_title<?php echo $i; ?>" id="process_title<?php echo $i; ?>" value="<?php echo htmlspecialchars($processes[$i]['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                            <label class="block text-gray-700 font-medium mb-1" for="process_description<?php echo $i; ?>">Description</label>
                            <textarea name="process_description<?php echo $i; ?>" id="process_description<?php echo $i; ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="3" required><?php echo htmlspecialchars($processes[$i]['description']); ?></textarea>
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

    document.getElementById('processes-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });
</script>

<?php
include 'includes/footer.php';
?>