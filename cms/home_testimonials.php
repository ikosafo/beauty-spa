<?php
// cms/home_testimonials.php

require_once '../config.php';

$page_title = 'Edit Testimonial Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch ALL testimonials and facts (no ID limit)
$section = [];
$testimonials = [];
$facts = [];

$query = "SELECT * FROM ws_testimonials ORDER BY id";
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
        } elseif ($row['type'] === 'fact') {
            $facts[$row['item_order']] = $row;
        }
    }
}

// Default section
if (empty($section)) {
    $section = [
        'id' => 1,
        'type' => 'section',
        'subtitle' => 'Testimonials',
        'title' => 'WHAT OUR CLIENTS SAYING'
    ];
}

// Default testimonials (empty — will be added via CMS)
if (empty($testimonials)) {
    $testimonials = [];
}

// Default 4 fact boxes
if (empty($facts) || count($facts) < 4) {
    $facts = [
        1 => ['id' => 5, 'type' => 'fact', 'title' => 'Cosmetics', 'icon' => 'flaticon-spa', 'number' => 5684, 'item_order' => 1],
        2 => ['id' => 6, 'type' => 'fact', 'title' => 'Subscriber', 'icon' => 'flaticon-wellness', 'number' => 7458, 'item_order' => 2],
        3 => ['id' => 7, 'type' => 'fact', 'title' => 'Total Branches', 'icon' => 'flaticon-hair-1', 'number' => 7855, 'item_order' => 3],
        4 => ['id' => 8, 'type' => 'fact', 'title' => 'Campigns Done', 'icon' => 'flaticon-herbal', 'number' => 1458, 'item_order' => 4]
    ];
} else {
    // Ensure we always have exactly 4 fact boxes (fill missing with defaults)
    for ($i = 1; $i <= 4; $i++) {
        if (!isset($facts[$i])) {
            $facts[$i] = [
                'id' => 4 + $i,
                'type' => 'fact',
                'title' => "Fact $i",
                'icon' => 'flaticon-spa',
                'number' => 0,
                'item_order' => $i
            ];
        }
    }
    ksort($facts);
}

error_log('Fetched data: ' . print_r(['section' => $section, 'testimonials' => $testimonials, 'facts' => $facts], true));

// Flaticon options
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
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
    } else {
        $errors = [];

        // Section
        $section_subtitle = trim($_POST['section_subtitle'] ?? '');
        $section_title = trim($_POST['section_title'] ?? '');
        if (empty($section_subtitle) || empty($section_title)) {
            $errors[] = 'Section subtitle and title are required.';
        }

        // Dynamic Testimonials
        $testimonial_data = [];
        $testimonial_ids = $_POST['testimonial_id'] ?? [];
        $quotes = $_POST['testimonial_quote'] ?? [];
        $names = $_POST['testimonial_name'] ?? [];
        $labels = $_POST['testimonial_label'] ?? [];

        foreach ($testimonial_ids as $index => $id) {
            $id = (int)$id;
            $quote = trim($quotes[$index] ?? '');
            $name = trim($names[$index] ?? '');
            $label = trim($labels[$index] ?? '');

            if ($quote && $name && $label) {
                $testimonial_data[] = [
                    'id' => $id,
                    'quote' => $quote,
                    'name' => $name,
                    'label' => $label,
                    'item_order' => count($testimonial_data) + 1
                ];
            }
        }

        if (empty($testimonial_data)) {
            $errors[] = 'At least one testimonial is required.';
        }

        // Fixed 4 Fact Boxes
        $fact_data = [];
        for ($i = 1; $i <= 4; $i++) {
            $title = trim($_POST["fact{$i}_title"] ?? '');
            $icon = $_POST["fact{$i}_icon"] ?? '';
            $number = $_POST["fact{$i}_number"] ?? '';

            if (empty($title) || empty($icon) || !is_numeric($number)) {
                $errors[] = "Fact Box $i: All fields required and number must be numeric.";
            } elseif (!array_key_exists($icon, $flaticon_options)) {
                $errors[] = "Fact Box $i: Invalid icon.";
            } else {
                $fact_data[$i] = [
                    'title' => $title,
                    'icon' => $icon,
                    'number' => (int)$number,
                    'item_order' => $i
                ];
            }
        }

        // Save if no errors
        if (empty($errors)) {
            // === SECTION ===
            $stmt = $mysqli->prepare("INSERT INTO ws_testimonials (id, type, subtitle, title) VALUES (1, 'section', ?, ?) ON DUPLICATE KEY UPDATE subtitle = ?, title = ?");
            $stmt->bind_param('ssss', $section_subtitle, $section_title, $section_subtitle, $section_title);
            $stmt->execute();
            $stmt->close();

            // === TESTIMONIALS (Dynamic) ===
            // Delete old testimonials
            $mysqli->query("DELETE FROM ws_testimonials WHERE type = 'testimonial'");

            foreach ($testimonial_data as $t) {
                if ($t['id'] > 0) {
                    $stmt = $mysqli->prepare("INSERT INTO ws_testimonials (id, type, quote, name, label, item_order) VALUES (?, 'testimonial', ?, ?, ?, ?)");
                    $stmt->bind_param('isssi', $t['id'], $t['quote'], $t['name'], $t['label'], $t['item_order']);
                } else {
                    $stmt = $mysqli->prepare("INSERT INTO ws_testimonials (type, quote, name, label, item_order) VALUES ('testimonial', ?, ?, ?, ?)");
                    $stmt->bind_param('sssi', $t['quote'], $t['name'], $t['label'], $t['item_order']);
                }
                $stmt->execute();
                $stmt->close();
            }

            // === FACTS (Fixed 4) ===
            for ($i = 1; $i <= 4; $i++) {
                $fact_id = 4 + $i;
                $stmt = $mysqli->prepare("INSERT INTO ws_testimonials (id, type, title, icon, number, item_order) VALUES (?, 'fact', ?, ?, ?, ?) ON DUPLICATE KEY UPDATE title = ?, icon = ?, number = ?, item_order = ?");
                $stmt->bind_param('isssisssi',
                    $fact_id, $fact_data[$i]['title'], $fact_data[$i]['icon'], $fact_data[$i]['number'], $fact_data[$i]['item_order'],
                    $fact_data[$i]['title'], $fact_data[$i]['icon'], $fact_data[$i]['number'], $fact_data[$i]['item_order']
                );
                $stmt->execute();
                $stmt->close();
            }

            // Refresh data
            header("Location: home_testimonials.php?success=1");
            exit;
        } else {
            $_SESSION['error'] = implode('<br>', $errors);
        }
    }
}

include 'includes/header.php';
?>

<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_GET['success'])): ?>
            <p class="text-green-600 mb-4">Changes saved successfully!</p>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-6" id="testimonials-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <!-- Section Title -->
            <div>
                <h3 class="text-lg font-medium mb-3">Section Title</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium mb-1">Subtitle</label>
                        <input type="text" name="section_subtitle" value="<?php echo htmlspecialchars($section['subtitle']); ?>" class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Title</label>
                        <input type="text" name="section_title" value="<?php echo htmlspecialchars($section['title']); ?>" class="w-full p-2 border rounded" required>
                    </div>
                </div>
            </div>

            <!-- Dynamic Testimonials -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-medium">Testimonials</h3>
                    <button type="button" id="add-testimonial" class="bg-green-600 text-white px-3 py-1 rounded text-sm">+ Add Testimonial</button>
                </div>
                <div id="testimonials-container" class="space-y-4">
                    <?php foreach ($testimonials as $t): ?>
                        <div class="border p-4 rounded bg-gray-50 testimonial-item">
                            <input type="hidden" name="testimonial_id[]" value="<?php echo $t['id']; ?>">
                            <div class="grid grid-cols-1 gap-3">
                                <div>
                                    <label class="block font-medium text-sm">Quote</label>
                                    <textarea name="testimonial_quote[]" class="w-full p-2 border rounded" rows="3" required><?php echo htmlspecialchars($t['quote']); ?></textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-medium text-sm">Name</label>
                                        <input type="text" name="testimonial_name[]" value="<?php echo htmlspecialchars($t['name']); ?>" class="w-full p-2 border rounded" required>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-sm">Label</label>
                                        <input type="text" name="testimonial_label[]" value="<?php echo htmlspecialchars($t['label']); ?>" class="w-full p-2 border rounded" required>
                                    </div>
                                </div>
                                <button type="button" class="remove-testimonial text-red-600 text-sm">Remove</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Fixed 4 Fact Boxes -->
            <div>
                <h3 class="text-lg font-medium mb-3">Fact Boxes (Fixed 4)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="border p-4 rounded">
                            <h4 class="font-medium mb-2">Fact Box <?php echo $i; ?></h4>
                            <label class="block text-sm font-medium mb-1">Title</label>
                            <input type="text" name="fact<?php echo $i; ?>_title" value="<?php echo htmlspecialchars($facts[$i]['title']); ?>" class="w-full p-2 border rounded mb-2" required>
                            <label class="block text-sm font-medium mb-1">Icon</label>
                            <select name="fact<?php echo $i; ?>_icon" class="w-full p-2 border rounded mb-2" required>
                                <?php foreach ($flaticon_options as $val => $lbl): ?>
                                    <option value="<?php echo $val; ?>" <?php echo $facts[$i]['icon'] === $val ? 'selected' : ''; ?>><?php echo $lbl; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label class="block text-sm font-medium mb-1">Number</label>
                            <input type="number" name="fact<?php echo $i; ?>_number" value="<?php echo $facts[$i]['number']; ?>" class="w-full p-2 border rounded" required>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">Save Changes</button>
                <a href="<?php echo URLROOT; ?>cms/index.php" class="px-5 py-2 border rounded text-gray-700 hover:bg-gray-100">Cancel</a>
            </div>
        </form>
    </div>
</section>

<script>
    let testimonialCount = <?php echo count($testimonials); ?>;

    document.getElementById('add-testimonial').addEventListener('click', function () {
        testimonialCount++;

        const container = document.getElementById('testimonials-container');

        /* ---------- 1. CREATE NEW TESTIMONIAL BLOCK ---------- */
        const div = document.createElement('div');
        div.className = 'border p-4 rounded bg-gray-50 testimonial-item';
        div.innerHTML = `
            <input type="hidden" name="testimonial_id[]" value="0">
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block font-medium text-sm">Quote</label>
                    <textarea name="testimonial_quote[]" class="w-full p-2 border rounded" rows="3" required></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-sm">Name</label>
                        <input type="text" name="testimonial_name[]" class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label class="block font-medium text-sm">Label</label>
                        <input type="text" name="testimonial_label[]" class="w-full p-2 border rounded" required>
                    </div>
                </div>
                <button type="button" class="remove-testimonial text-red-600 text-sm">Remove</button>
            </div>
        `;

        container.appendChild(div);

        /* ---------- 2. SCROLL TO THE NEW BLOCK ---------- */
        div.scrollIntoView({
            behavior: 'smooth',   // nice animation
            block:    'center'    // puts the new block roughly in the middle of the viewport
        });

        /* optional: put the cursor in the first textarea */
        const firstTextarea = div.querySelector('textarea');
        if (firstTextarea) firstTextarea.focus();
    });

    /* ---------- REMOVE BUTTON (unchanged) ---------- */
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-testimonial')) {
            e.target.closest('.testimonial-item').remove();
        }
    });
</script>

<?php include 'includes/footer.php'; ?>