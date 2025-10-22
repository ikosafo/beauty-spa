<?php
// cms/testimonials.php

require_once '../config.php';

$page_title = 'Edit Testimonial Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch testimonials data
$section = [];
$testimonials = [];
$facts = [];
$query = "SELECT * FROM ws_testimonials WHERE id IN (1, 2, 3, 4, 5, 6, 7, 8) ORDER BY id";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
} else {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $section = $row;
        } elseif ($row['type'] === 'testimonial') {
            $testimonials[$row['item_order']] = $row;
        } else {
            $facts[$row['item_order']] = $row;
        }
    }
}
if (empty($section)) {
    $section = [
        'id' => 1,
        'type' => 'section',
        'subtitle' => 'Testimonials',
        'title' => 'WHAT OUR CLIENTS SAYING'
    ];
}
if (empty($testimonials)) {
    $testimonials = [
        1 => [
            'id' => 2,
            'type' => 'testimonial',
            'quote' => 'I received a lymphatic massage, Aba was fantastic. The spa is clean, upscale decor and overall ambience are delightful. I will patronize Ecobel for a variety of services they offer.',
            'name' => 'Len Rosy Jacbos',
            'label' => 'Face make-up',
            'item_order' => 1
        ],
        2 => [
            'id' => 3,
            'type' => 'testimonial',
            'quote' => 'I received a lymphatic massage, Aba was fantastic. The spa is clean, upscale decor and overall ambience are delightful. I will patronize Ecobel for a variety of services they offer.',
            'name' => 'Len Rosy Jacbos',
            'label' => 'Face make-up',
            'item_order' => 2
        ],
        3 => [
            'id' => 4,
            'type' => 'testimonial',
            'quote' => 'I received a lymphatic massage, Aba was fantastic. The spa is clean, upscale decor and overall ambience are delightful. I will patronize Ecobel for a variety of services they offer.',
            'name' => 'Scorlett Johanson',
            'label' => 'Face make-up',
            'item_order' => 3
        ]
    ];
}
if (empty($facts)) {
    $facts = [
        1 => [
            'id' => 5,
            'type' => 'fact',
            'title' => 'Cosmetics',
            'icon' => 'flaticon-spa',
            'number' => 5684,
            'item_order' => 1
        ],
        2 => [
            'id' => 6,
            'type' => 'fact',
            'title' => 'Subscriber',
            'icon' => 'flaticon-wellness',
            'number' => 7458,
            'item_order' => 2
        ],
        3 => [
            'id' => 7,
            'type' => 'fact',
            'title' => 'Total Branches',
            'icon' => 'flaticon-hair-1',
            'number' => 7855,
            'item_order' => 3
        ],
        4 => [
            'id' => 8,
            'type' => 'fact',
            'title' => 'Campigns Done',
            'icon' => 'flaticon-herbal',
            'number' => 1458,
            'item_order' => 4
        ]
    ];
}
error_log('Fetched testimonials data: ' . print_r(['section' => $section, 'testimonials' => $testimonials, 'facts' => $facts], true));

// Define available Flaticon icons
$flaticon_options = [
    'flaticon-spa' => 'Spa',
    'flaticon-wellness' => 'Wellness',
    'flaticon-hair-1' => 'Hair 1',
    'flaticon-herbal' => 'Herbal',
    'flaticon-massage' => 'Massage',
    'flaticon-facial' => 'Facial'
];

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

        // Validate section fields
        if (empty($section_subtitle) || empty($section_title)) {
            $errors[] = 'All section title fields are required.';
            error_log('Validation failed: Missing section title fields');
        }

        // Testimonial data
        $testimonial_data = [];
        for ($i = 1; $i <= 3; $i++) {
            $testimonial_data[$i] = [
                'quote' => $_POST["testimonial{$i}_quote"] ?? '',
                'name' => $_POST["testimonial{$i}_name"] ?? '',
                'label' => $_POST["testimonial{$i}_label"] ?? ''
            ];

            // Validate testimonial fields
            if (empty($testimonial_data[$i]['quote']) || empty($testimonial_data[$i]['name']) || empty($testimonial_data[$i]['label'])) {
                $errors[] = "All fields for Testimonial $i are required.";
                error_log("Validation failed: Missing fields for Testimonial $i");
            }
        }

        // Fact box data
        $fact_data = [];
        for ($i = 1; $i <= 4; $i++) {
            $fact_data[$i] = [
                'title' => $_POST["fact{$i}_title"] ?? '',
                'icon' => $_POST["fact{$i}_icon"] ?? '',
                'number' => $_POST["fact{$i}_number"] ?? ''
            ];

            // Validate fact box fields
            if (empty($fact_data[$i]['title']) || empty($fact_data[$i]['icon']) || !is_numeric($fact_data[$i]['number'])) {
                $errors[] = "All fields for Fact Box $i are required, and number must be numeric.";
                error_log("Validation failed: Missing or invalid fields for Fact Box $i");
            } elseif (!array_key_exists($fact_data[$i]['icon'], $flaticon_options)) {
                $errors[] = "Invalid icon selected for Fact Box $i.";
                error_log("Validation failed: Invalid icon for Fact Box $i");
            }
        }

        // Proceed with database update if no errors
        if (empty($errors)) {
            // Update section title
            $query = "SELECT COUNT(*) AS count FROM ws_testimonials WHERE id = 1";
            $stmt = $mysqli->prepare($query);
            $stmt->execute();
            $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
            $stmt->close();

            if ($row_exists) {
                $query = "UPDATE ws_testimonials SET subtitle = ?, title = ? WHERE id = 1";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $errors[] = 'Prepare failed for section update: ' . $mysqli->error;
                    error_log('Prepare failed for section update: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('ss', $section_subtitle, $section_title);
                    if (!$stmt->execute()) {
                        $errors[] = 'Update failed for section: ' . $stmt->error;
                        error_log('Update failed for section: ' . $stmt->error);
                    } else {
                        error_log('Section update successful, affected rows: ' . $stmt->affected_rows);
                    }
                    $stmt->close();
                }
            } else {
                $query = "INSERT INTO ws_testimonials (id, type, subtitle, title) VALUES (1, 'section', ?, ?)";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $errors[] = 'Prepare failed for section insert: ' . $mysqli->error;
                    error_log('Prepare failed for section insert: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('ss', $section_subtitle, $section_title);
                    if (!$stmt->execute()) {
                        $errors[] = 'Insert failed for section: ' . $stmt->error;
                        error_log('Insert failed for section: ' . $stmt->error);
                    } else {
                        error_log('Section insert successful');
                    }
                    $stmt->close();
                }
            }

            // Update testimonials
            for ($i = 1; $i <= 3; $i++) {
                $testimonial_id = $i + 1; // IDs 2-4
                $query = "SELECT COUNT(*) AS count FROM ws_testimonials WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('i', $testimonial_id);
                $stmt->execute();
                $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
                $stmt->close();

                if ($row_exists) {
                    $query = "UPDATE ws_testimonials SET quote = ?, name = ?, label = ?, item_order = ? WHERE id = ?";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Testimonial $i update: " . $mysqli->error;
                        error_log("Prepare failed for Testimonial $i update: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('sssii', $testimonial_data[$i]['quote'], $testimonial_data[$i]['name'], $testimonial_data[$i]['label'], $i, $testimonial_id);
                        if (!$stmt->execute()) {
                            $errors[] = "Update failed for Testimonial $i: " . $stmt->error;
                            error_log("Update failed for Testimonial $i: " . $stmt->error);
                        } else {
                            error_log("Update successful for Testimonial $i, affected rows: " . $stmt->affected_rows);
                        }
                        $stmt->close();
                    }
                } else {
                    $query = "INSERT INTO ws_testimonials (id, type, quote, name, label, item_order) VALUES (?, 'testimonial', ?, ?, ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Testimonial $i insert: " . $mysqli->error;
                        error_log("Prepare failed for Testimonial $i insert: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('isssi', $testimonial_id, $testimonial_data[$i]['quote'], $testimonial_data[$i]['name'], $testimonial_data[$i]['label'], $i);
                        if (!$stmt->execute()) {
                            $errors[] = "Insert failed for Testimonial $i: " . $stmt->error;
                            error_log("Insert failed for Testimonial $i: " . $stmt->error);
                        } else {
                            error_log("Insert successful for Testimonial $i");
                        }
                        $stmt->close();
                    }
                }
            }

            // Update fact boxes
            for ($i = 1; $i <= 4; $i++) {
                $fact_id = $i + 4; // IDs 5-8
                $query = "SELECT COUNT(*) AS count FROM ws_testimonials WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('i', $fact_id);
                $stmt->execute();
                $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
                $stmt->close();

                if ($row_exists) {
                    $query = "UPDATE ws_testimonials SET title = ?, icon = ?, number = ?, item_order = ? WHERE id = ?";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Fact Box $i update: " . $mysqli->error;
                        error_log("Prepare failed for Fact Box $i update: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('ssiii', $fact_data[$i]['title'], $fact_data[$i]['icon'], $fact_data[$i]['number'], $i, $fact_id);
                        if (!$stmt->execute()) {
                            $errors[] = "Update failed for Fact Box $i: " . $stmt->error;
                            error_log("Update failed for Fact Box $i: " . $stmt->error);
                        } else {
                            error_log("Update successful for Fact Box $i, affected rows: " . $stmt->affected_rows);
                        }
                        $stmt->close();
                    }
                } else {
                    $query = "INSERT INTO ws_testimonials (id, type, title, icon, number, item_order) VALUES (?, 'fact', ?, ?, ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Fact Box $i insert: " . $mysqli->error;
                        error_log("Prepare failed for Fact Box $i insert: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('issii', $fact_id, $fact_data[$i]['title'], $fact_data[$i]['icon'], $fact_data[$i]['number'], $i);
                        if (!$stmt->execute()) {
                            $errors[] = "Insert failed for Fact Box $i: " . $stmt->error;
                            error_log("Insert failed for Fact Box $i: " . $stmt->error);
                        } else {
                            error_log("Insert successful for Fact Box $i");
                        }
                        $stmt->close();
                    }
                }
            }

            // Refresh data
            if (empty($errors)) {
                $query = "SELECT * FROM ws_testimonials WHERE id IN (1, 2, 3, 4, 5, 6, 7, 8) ORDER BY id";
                $result = $mysqli->query($query);
                if (!$result) {
                    $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                    error_log('Refresh query failed: ' . $mysqli->error);
                } else {
                    $section = [];
                    $testimonials = [];
                    $facts = [];
                    while ($row = $result->fetch_assoc()) {
                        if ($row['type'] === 'section') {
                            $section = $row;
                        } elseif ($row['type'] === 'testimonial') {
                            $testimonials[$row['item_order']] = $row;
                        } else {
                            $facts[$row['item_order']] = $row;
                        }
                    }
                    error_log('Testimonials data refreshed: ' . print_r(['section' => $section, 'testimonials' => $testimonials, 'facts' => $facts], true));
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Testimonial Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="testimonials-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <h3 class="text-lg font-medium mb-2">Section Title Settings</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="section_subtitle">Subtitle</label>
                    <input type="text" name="section_subtitle" id="section_subtitle" value="<?php echo htmlspecialchars($section['subtitle']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="section_title">Title</label>
                    <input type="text" name="section_title" id="section_title" value="<?php echo htmlspecialchars($section['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Testimonials</h3>
                <div class="space-y-4">
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="border p-4 rounded">
                            <h4 class="text-md font-medium mb-2">Testimonial <?php echo $i; ?></h4>
                            <label class="block text-gray-700 font-medium mb-1" for="testimonial<?php echo $i; ?>_quote">Quote</label>
                            <textarea name="testimonial<?php echo $i; ?>_quote" id="testimonial<?php echo $i; ?>_quote" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($testimonials[$i]['quote']); ?></textarea>
                            <label class="block text-gray-700 font-medium mb-1" for="testimonial<?php echo $i; ?>_name">Name</label>
                            <input type="text" name="testimonial<?php echo $i; ?>_name" id="testimonial<?php echo $i; ?>_name" value="<?php echo htmlspecialchars($testimonials[$i]['name']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                            <label class="block text-gray-700 font-medium mb-1" for="testimonial<?php echo $i; ?>_label">Label</label>
                            <input type="text" name="testimonial<?php echo $i; ?>_label" id="testimonial<?php echo $i; ?>_label" value="<?php echo htmlspecialchars($testimonials[$i]['label']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Fact Boxes</h3>
                <div class="space-y-4">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="border p-4 rounded">
                            <h4 class="text-md font-medium mb-2">Fact Box <?php echo $i; ?></h4>
                            <label class="block text-gray-700 font-medium mb-1" for="fact<?php echo $i; ?>_title">Title</label>
                            <input type="text" name="fact<?php echo $i; ?>_title" id="fact<?php echo $i; ?>_title" value="<?php echo htmlspecialchars($facts[$i]['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                            <label class="block text-gray-700 font-medium mb-1" for="fact<?php echo $i; ?>_icon">Icon</label>
                            <select name="fact<?php echo $i; ?>_icon" id="fact<?php echo $i; ?>_icon" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                                <?php foreach ($flaticon_options as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $facts[$i]['icon'] === $value ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label class="block text-gray-700 font-medium mb-1" for="fact<?php echo $i; ?>_number">Number</label>
                            <input type="number" name="fact<?php echo $i; ?>_number" id="fact<?php echo $i; ?>_number" value="<?php echo htmlspecialchars($facts[$i]['number']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
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

    document.getElementById('testimonials-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });
</script>

<?php
include 'includes/footer.php';
?>