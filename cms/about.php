<?php
// cms/about.php

require_once '../config.php';

$page_title = 'Edit About Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch about data
$query = "SELECT * FROM ws_about WHERE id = 1";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
}
$about = $result->fetch_assoc();
if (!$about) {
    $about = [
        'title' => 'About',
        'paragraph1' => 'TAYMAC, established in 2011 as Taymac Safety Consulting Limited and registered in Ghana under registration number CA-88,491, was founded to address the critical need for prioritizing Occupational Health, Safety, Security, and Environmental Management (HSSE) across all sectors of industry, commerce, and business. Since its inception, TAYMAC has been committed to making HSSE the foremost concern in the operational practices of its clients, ensuring a safer and more sustainable working environment for all.',
        'paragraph2' => 'In early 2012, TAYMAC expanded its operations into Property and Facility Management, seizing a strategic opportunity to diversify its service offerings. To better align with this shift in focus, the company rebranded from Taymac Safety Consulting Limited to TAYMAC, reflecting its broader range of expertise and commitment to delivering comprehensive management solutions.',
        'paragraph3' => 'As we managed properties in Accra, we gradually expanded into aluminum glazing and office partitioning, utilizing glass, aluminum profiles, and gypsum board. This shift occurred alongside managing tenant move-ins for properties under our care. By 2016, we had fully transitioned into comprehensive facilities management, incorporating office cleaning services as part of our operations. The consistent quality and durability of our work have earned us a solid reputation, enabling us to build an impressive and growing clientele over the years.',
        'methodology_title' => 'Project Methodology',
        'methodology_item1' => 'We utilize premium materials for all our projects, carefully tailored to meet the unique requirements of each client, ensuring optimal results from the outset of every engagement.',
        'methodology_item2' => 'All necessary equipment will be deployed to the site, with work conducted in consideration of other building occupants. This may require us to operate after regular business hours or during weekends, depending on the specific needs of our clients.',
        'methodology_item3' => 'The final project handover is completed upon the client\'s full approval and satisfaction.'
    ];
}
error_log('Fetched about data: ' . print_r($about, true));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Form submitted with POST method');
    error_log('$_POST: ' . print_r($_POST, true));
    error_log('Raw methodology_item3 from POST: ' . ($_POST['methodology_item3'] ?? ''));
    error_log('Request headers: ' . print_r(getallheaders(), true));

    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        error_log('CSRF validation failed');
    } else {
        // Use raw POST values (prepared statements handle escaping)
        $title = $_POST['title'] ?? '';
        $paragraph1 = $_POST['paragraph1'] ?? '';
        $paragraph2 = $_POST['paragraph2'] ?? '';
        $paragraph3 = $_POST['paragraph3'] ?? '';
        $methodology_title = $_POST['methodology_title'] ?? '';
        $methodology_item1 = $_POST['methodology_item1'] ?? '';
        $methodology_item2 = $_POST['methodology_item2'] ?? '';
        $methodology_item3 = $_POST['methodology_item3'] ?? '';

        // Validate required fields
        if (empty($title) || empty($paragraph1) || empty($paragraph2) || empty($paragraph3) ||
            empty($methodology_title) || empty($methodology_item1) || empty($methodology_item2) || empty($methodology_item3)) {
            $_SESSION['error'] = 'All fields are required';
            error_log('Validation failed: Missing required fields');
        } else {
            // Check if row exists
            $query = "SELECT COUNT(*) AS count FROM ws_about WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Check query failed: ' . $mysqli->error;
                error_log('Check query failed: ' . $mysqli->error);
            }
            $row_exists = $result->fetch_assoc()['count'] > 0;
            error_log('Row exists: ' . ($row_exists ? 'Yes' : 'No'));

            if ($row_exists) {
                // Update existing row
                $query = "UPDATE ws_about SET title = ?, paragraph1 = ?, paragraph2 = ?, paragraph3 = ?, methodology_title = ?, methodology_item1 = ?, methodology_item2 = ?, methodology_item3 = ? WHERE id = 1";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('ssssssss', $title, $paragraph1, $paragraph2, $paragraph3, $methodology_title, $methodology_item1, $methodology_item2, $methodology_item3);
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
                $query = "INSERT INTO ws_about (id, title, paragraph1, paragraph2, paragraph3, methodology_title, methodology_item1, methodology_item2, methodology_item3) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare failed: ' . $mysqli->error;
                    error_log('Prepare failed: ' . $mysqli->error);
                } else {
                    $id = 1;
                    $stmt->bind_param('issssssss', $id, $title, $paragraph1, $paragraph2, $paragraph3, $methodology_title, $methodology_item1, $methodology_item2, $methodology_item3);
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
            $query = "SELECT * FROM ws_about WHERE id = 1";
            $result = $mysqli->query($query);
            if (!$result) {
                $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                error_log('Refresh query failed: ' . $mysqli->error);
            } else {
                $about = $result->fetch_assoc();
                error_log('About data refreshed: ' . print_r($about, true));
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- About Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="about-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="title">Title</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($about['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">About Content</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="paragraph1">Paragraph 1</label>
                    <textarea name="paragraph1" id="paragraph1" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="5" required><?php echo htmlspecialchars($about['paragraph1']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="paragraph2">Paragraph 2</label>
                    <textarea name="paragraph2" id="paragraph2" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="5" required><?php echo htmlspecialchars($about['paragraph2']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="paragraph3">Paragraph 3</label>
                    <textarea name="paragraph3" id="paragraph3" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="5" required><?php echo htmlspecialchars($about['paragraph3']); ?></textarea>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Project Methodology</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="methodology_title">Methodology Title</label>
                    <input type="text" name="methodology_title" id="methodology_title" value="<?php echo htmlspecialchars($about['methodology_title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="methodology_item1">Methodology Item 1</label>
                    <textarea name="methodology_item1" id="methodology_item1" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="3" required><?php echo htmlspecialchars($about['methodology_item1']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="methodology_item2">Methodology Item 2</label>
                    <textarea name="methodology_item2" id="methodology_item2" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="3" required><?php echo htmlspecialchars($about['methodology_item2']); ?></textarea>
                    <label class="block text-gray-700 font-medium mb-1" for="methodology_item3">Methodology Item 3</label>
                    <textarea name="methodology_item3" id="methodology_item3" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="3" required><?php echo htmlspecialchars($about['methodology_item3']); ?></textarea>
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

    document.getElementById('about-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });
</script>

<?php
include 'includes/footer.php';
?>