<?php
// cms/blog.php

require_once '../config.php';

$page_title = 'Edit Blog Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Fetch blog data
$blog_section = [];
$posts = [];
$query = "SELECT * FROM ws_blog WHERE id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10) ORDER BY id";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
    error_log('Fetch query failed: ' . $mysqli->error);
} else {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $blog_section = $row;
        } else {
            $posts[$row['post_order']] = $row;
        }
    }
}
if (empty($blog_section)) {
    $blog_section = [
        'id' => 1,
        'type' => 'section',
        'subtitle' => 'Welcome',
        'title' => 'DEFINITIVE SPA COLLECTION',
        'description' => 'You can choose the various type of massage you want from kinds of massages our team has expertise in!'
    ];
}
if (empty($posts)) {
    $posts = [
        1 => [
            'id' => 2,
            'type' => 'post',
            'image' => 'images/blog/01.jpg',
            'post_date' => 'January 13, 2020',
            'category' => 'Bath & Body',
            'title' => 'Maintaining Health and Beauty Through Spas',
            'link' => 'blog-single.html',
            'post_order' => 1
        ],
        2 => [
            'id' => 3,
            'type' => 'post',
            'image' => 'images/blog/02.jpg',
            'post_date' => 'January 13, 2020',
            'category' => 'Make-up',
            'title' => 'A Relaxation of the Senses with Their Help',
            'link' => 'blog-single.html',
            'post_order' => 2
        ],
        3 => [
            'id' => 4,
            'type' => 'post',
            'image' => 'images/blog/03.jpg',
            'post_date' => 'January 13, 2020',
            'category' => 'Natural',
            'title' => 'Differences Between a Sauna and a Turkish Bath',
            'link' => 'blog-single.html',
            'post_order' => 3
        ],
        4 => [
            'id' => 5,
            'type' => 'post',
            'image' => 'images/blog/04.jpg',
            'post_date' => 'January 13, 2020',
            'category' => 'Hair care',
            'title' => 'Do Massages Have Real Health Benefits?',
            'link' => 'blog-single.html',
            'post_order' => 4
        ],
        5 => [
            'id' => 6,
            'type' => 'post',
            'image' => 'images/blog/05.jpg',
            'post_date' => 'January 13, 2020',
            'category' => 'Bath & Body',
            'title' => 'Massage Therapy for Anxiety and Stress',
            'link' => 'blog-single.html',
            'post_order' => 5
        ],
        6 => [
            'id' => 7,
            'type' => 'post',
            'image' => 'images/blog/06.jpg',
            'post_date' => 'January 4, 2020',
            'category' => 'Bath & Body',
            'title' => 'Main Responsibilities in Beauty Industry',
            'link' => 'blog-single.html',
            'post_order' => 6
        ],
        7 => [
            'id' => 8,
            'type' => 'post',
            'image' => 'images/blog/07.jpg',
            'post_date' => 'January 4, 2020',
            'category' => 'Make-up',
            'title' => 'Turkish Bathroom Benefits for Your Health',
            'link' => 'blog-single.html',
            'post_order' => 7
        ],
        8 => [
            'id' => 9,
            'type' => 'post',
            'image' => 'images/blog/08.jpg',
            'post_date' => 'January 4, 2020',
            'category' => 'Hair care',
            'title' => 'How To Straighten Hair To Using Home Remedies.',
            'link' => 'blog-single.html',
            'post_order' => 8
        ],
        9 => [
            'id' => 10,
            'type' => 'post',
            'image' => 'images/blog/09.jpg',
            'post_date' => 'January 4, 2020',
            'category' => 'Special Product',
            'title' => 'Effects of Indian Head Massage and Benefits',
            'link' => 'blog-single.html',
            'post_order' => 9
        ]
    ];
}
error_log('Fetched blog data: ' . print_r(['blog_section' => $blog_section, 'posts' => $posts], true));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Form submitted with POST method');
    error_log('$_POST: ' . print_r($_POST, true));
    error_log('$_FILES: ' . print_r($_FILES, true));
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

        // Post data
        $post_data = [];
        for ($i = 1; $i <= 9; $i++) {
            $post_data[$i] = [
                'image' => isset($posts[$i]['image']) ? $posts[$i]['image'] : '',
                'post_date' => $_POST["post_date{$i}"] ?? '',
                'category' => $_POST["post_category{$i}"] ?? '',
                'title' => $_POST["post_title{$i}"] ?? '',
                'link' => $_POST["post_link{$i}"] ?? ''
            ];

            // Validate post fields (only for non-empty posts)
            if (!empty($post_data[$i]['post_date']) || !empty($post_data[$i]['category']) || !empty($post_data[$i]['title']) || !empty($post_data[$i]['link'])) {
                if (empty($post_data[$i]['post_date']) || empty($post_data[$i]['category']) || empty($post_data[$i]['title']) || empty($post_data[$i]['link'])) {
                    $errors[] = "All fields for Post $i are required if any field is filled.";
                    error_log("Validation failed: Missing fields for Post $i");
                }
            }

            // Handle image upload
            if (isset($_FILES["post_image{$i}"]) && $_FILES["post_image{$i}"]['error'] !== UPLOAD_ERR_NO_FILE) {
                $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
                $max_size = 2 * 1024 * 1024; // 2MB
                $upload_dir = './uploads/blog/';
                $image_file = $_FILES["post_image{$i}"];

                // Validate file type and size
                if (!in_array($image_file['type'], $allowed_types)) {
                    $errors[] = "Invalid file type for Post $i image. Only PNG, JPG, JPEG are allowed.";
                    error_log("Invalid file type for Post $i image: " . $image_file['type']);
                } elseif ($image_file['size'] > $max_size) {
                    $errors[] = "File size exceeds 2MB limit for Post $i image.";
                    error_log("File size too large for Post $i image: " . $image_file['size']);
                } elseif ($image_file['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = "File upload error for Post $i image: " . $image_file['error'];
                    error_log("File upload error for Post $i image: " . $image_file['error']);
                } else {
                    // Generate unique filename
                    $ext = pathinfo($image_file['name'], PATHINFO_EXTENSION);
                    $image_filename = "blog_post{$i}_" . time() . '.' . $ext;
                    $image_path = $upload_dir . $image_filename;

                    // Move uploaded file
                    if (!move_uploaded_file($image_file['tmp_name'], $image_path)) {
                        $errors[] = "Failed to move uploaded file for Post $i image.";
                        error_log("Failed to move uploaded file to: " . $image_path);
                    } else {
                        $post_data[$i]['image'] = 'Uploads/blog/' . $image_filename;
                        error_log("Image uploaded successfully for Post $i: " . $post_data[$i]['image']);
                    }
                }
            }
        }

        // Proceed with database update if no errors
        if (empty($errors)) {
            // Update section title
            $query = "SELECT COUNT(*) AS count FROM ws_blog WHERE id = 1";
            $stmt = $mysqli->prepare($query);
            $stmt->execute();
            $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
            $stmt->close();

            if ($row_exists) {
                $query = "UPDATE ws_blog SET subtitle = ?, title = ?, description = ? WHERE id = 1";
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
                $query = "INSERT INTO ws_blog (id, type, subtitle, title, description) VALUES (1, 'section', ?, ?, ?)";
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

            // Update blog posts
            for ($i = 1; $i <= 9; $i++) {
                $post_id = $i + 1; // IDs 2-10
                if (empty($post_data[$i]['post_date']) && empty($post_data[$i]['category']) && empty($post_data[$i]['title']) && empty($post_data[$i]['link']) && empty($post_data[$i]['image'])) {
                    // Skip empty posts
                    continue;
                }

                $query = "SELECT COUNT(*) AS count FROM ws_blog WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('i', $post_id);
                $stmt->execute();
                $row_exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
                $stmt->close();

                if ($row_exists) {
                    $query = "UPDATE ws_blog SET image = ?, post_date = ?, category = ?, title = ?, link = ?, post_order = ? WHERE id = ?";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Post $i update: " . $mysqli->error;
                        error_log("Prepare failed for Post $i update: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('sssssii', $post_data[$i]['image'], $post_data[$i]['post_date'], $post_data[$i]['category'], $post_data[$i]['title'], $post_data[$i]['link'], $i, $post_id);
                        if (!$stmt->execute()) {
                            $errors[] = "Update failed for Post $i: " . $stmt->error;
                            error_log("Update failed for Post $i: " . $stmt->error);
                        } else {
                            error_log("Update successful for Post $i, affected rows: " . $stmt->affected_rows);
                        }
                        $stmt->close();
                    }
                } else {
                    $query = "INSERT INTO ws_blog (id, type, image, post_date, category, title, link, post_order) VALUES (?, 'post', ?, ?, ?, ?, ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $errors[] = "Prepare failed for Post $i insert: " . $mysqli->error;
                        error_log("Prepare failed for Post $i insert: " . $mysqli->error);
                    } else {
                        $stmt->bind_param('isssssi', $post_id, $post_data[$i]['image'], $post_data[$i]['post_date'], $post_data[$i]['category'], $post_data[$i]['title'], $post_data[$i]['link'], $i);
                        if (!$stmt->execute()) {
                            $errors[] = "Insert failed for Post $i: " . $stmt->error;
                            error_log("Insert failed for Post $i: " . $stmt->error);
                        } else {
                            error_log("Insert successful for Post $i");
                        }
                        $stmt->close();
                    }
                }
            }

            // Refresh data
            if (empty($errors)) {
                $query = "SELECT * FROM ws_blog WHERE id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10) ORDER BY id";
                $result = $mysqli->query($query);
                if (!$result) {
                    $_SESSION['error'] = 'Refresh query failed: ' . $mysqli->error;
                    error_log('Refresh query failed: ' . $mysqli->error);
                } else {
                    $blog_section = [];
                    $posts = [];
                    while ($row = $result->fetch_assoc()) {
                        if ($row['type'] === 'section') {
                            $blog_section = $row;
                        } else {
                            $posts[$row['post_order']] = $row;
                        }
                    }
                    error_log('Blog data refreshed: ' . print_r(['blog_section' => $blog_section, 'posts' => $posts], true));
                }
            } else {
                $_SESSION['error'] = implode('<br>', $errors);
            }
        }
    }
}

include 'includes/header.php';
?>

<!-- Blog Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="space-y-4" id="blog-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <h3 class="text-lg font-medium mb-2">Section Title Settings</h3>
                <div class="space-y-2">
                    <label class="block text-gray-700 font-medium mb-1" for="section_subtitle">Subtitle</label>
                    <input type="text" name="section_subtitle" id="section_subtitle" value="<?php echo htmlspecialchars($blog_section['subtitle']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="section_title">Title</label>
                    <input type="text" name="section_title" id="section_title" value="<?php echo htmlspecialchars($blog_section['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <label class="block text-gray-700 font-medium mb-1" for="section_description">Description</label>
                    <textarea name="section_description" id="section_description" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($blog_section['description']); ?></textarea>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-medium mb-2">Blog Posts</h3>
                <div class="space-y-4">
                    <?php for ($i = 1; $i <= 9; $i++): ?>
                        <div class="border p-4 rounded">
                            <h4 class="text-md font-medium mb-2">Post <?php echo $i; ?> (Leave empty to exclude)</h4>
                            <label class="block text-gray-700 font-medium mb-1" for="post_image<?php echo $i; ?>">Image (PNG, JPG, JPEG, max 2MB)</label>
                            <input type="file" name="post_image<?php echo $i; ?>" id="post_image<?php echo $i; ?>" accept="image/png,image/jpeg,image/jpg" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <?php if (!empty($posts[$i]['image'])): ?>
                                <img id="post_image<?php echo $i; ?>-preview" src="<?php echo htmlspecialchars(URLROOT . '/cms/' . $posts[$i]['image']); ?>" alt="Image Preview" class="mt-2 max-w-xs h-auto">
                            <?php else: ?>
                                <img id="post_image<?php echo $i; ?>-preview" src="" alt="Image Preview" class="mt-2 max-w-xs h-auto hidden">
                            <?php endif; ?>
                            <label class="block text-gray-700 font-medium mb-1 mt-2" for="post_date<?php echo $i; ?>">Date (e.g., January 13, 2020)</label>
                            <input type="text" name="post_date<?php echo $i; ?>" id="post_date<?php echo $i; ?>" value="<?php echo isset($posts[$i]['post_date']) ? htmlspecialchars($posts[$i]['post_date']) : ''; ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <label class="block text-gray-700 font-medium mb-1 mt-2" for="post_category<?php echo $i; ?>">Category</label>
                            <input type="text" name="post_category<?php echo $i; ?>" id="post_category<?php echo $i; ?>" value="<?php echo isset($posts[$i]['category']) ? htmlspecialchars($posts[$i]['category']) : ''; ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <label class="block text-gray-700 font-medium mb-1 mt-2" for="post_title<?php echo $i; ?>">Title</label>
                            <input type="text" name="post_title<?php echo $i; ?>" id="post_title<?php echo $i; ?>" value="<?php echo isset($posts[$i]['title']) ? htmlspecialchars($posts[$i]['title']) : ''; ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <label class="block text-gray-700 font-medium mb-1 mt-2" for="post_link<?php echo $i; ?>">Link (e.g., blog-single.html)</label>
                            <input type="text" name="post_link<?php echo $i; ?>" id="post_link<?php echo $i; ?>" value="<?php echo isset($posts[$i]['link']) ? htmlspecialchars($posts[$i]['link']) : ''; ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
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

    document.getElementById('blog-form').addEventListener('submit', function(e) {
        console.log('Form submit triggered');
        document.getElementById('submit-button').disabled = true;
    });

    // Image preview for each blog post
    <?php for ($i = 1; $i <= 9; $i++): ?>
        document.getElementById('post_image<?php echo $i; ?>').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('post_image<?php echo $i; ?>-preview');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
            }
        });
    <?php endfor; ?>
</script>

<?php
include 'includes/footer.php';
?>