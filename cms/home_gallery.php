<?php
// cms/home_gallery.php

require_once '../config.php';
$page_title = 'Edit Gallery Section';

// CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch ALL gallery data
$section = [];
$images = [];

$query = "SELECT * FROM ws_gallery ORDER BY image_order, id";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Database query failed: ' . $mysqli->error;
} else {
    while ($row = $result->fetch_assoc()) {
        if ($row['type'] === 'section') {
            $section = $row;
        } elseif ($row['type'] === 'image') {
            $images[$row['image_order']] = $row;
        }
    }
}

// Defaults
if (empty($section)) {
    $section = [
        'id' => 1,
        'type' => 'section',
        'subtitle' => 'Gallery',
        'title' => 'AN INCREDIBLE SPA EXPERIENCE',
        'description' => 'Stunning treatment rooms are endowed with lush open air gardens soak bathtub and cushioned daybed built for two.'
    ];
}
if (empty($images)) {
    $images = [
        1 => ['id' => 2, 'type' => 'image', 'image' => 'images/gallery/01.jpg', 'caption' => 'Luxurious Spa Room', 'image_order' => 1],
        2 => ['id' => 3, 'type' => 'image', 'image' => 'images/gallery/02.jpg', 'caption' => 'Relaxing Massage', 'image_order' => 2],
        3 => ['id' => 4, 'type' => 'image', 'image' => 'images/gallery/03.jpg', 'caption' => 'Facial Treatment', 'image_order' => 3],
        4 => ['id' => 5, 'type' => 'image', 'image' => 'images/gallery/04.jpg', 'caption' => 'Hot Stone Therapy', 'image_order' => 4],
    ];
}

// Reindex
$images = array_values($images);
foreach ($images as $idx => $img) {
    $images[$idx]['image_order'] = $idx + 1;
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
    } else {
        $errors = [];

        // Section
        $subtitle = trim($_POST['section_subtitle'] ?? '');
        $title = trim($_POST['section_title'] ?? '');
        $desc = trim($_POST['section_description'] ?? '');
        if (empty($subtitle) || empty($title) || empty($desc)) {
            $errors[] = 'Section fields are required.';
        }

        // Images + Captions
        $image_data = [];
        $image_ids = $_POST['image_id'] ?? [];
        $captions = $_POST['caption'] ?? [];
        $files = $_FILES['image'] ?? [];

        foreach ($image_ids as $idx => $id) {
            $id = (int)$id;
            $current_path = $images[$idx]['image'] ?? '';
            $caption = trim($captions[$idx] ?? '');

            // Upload new image
            if (isset($files['name'][$idx]) && $files['error'][$idx] !== UPLOAD_ERR_NO_FILE) {
                $file = [
                    'name' => $files['name'][$idx],
                    'type' => $files['type'][$idx],
                    'tmp_name' => $files['tmp_name'][$idx],
                    'error' => $files['error'][$idx],
                    'size' => $files['size'][$idx]
                ];

                $allowed = ['image/png', 'image/jpeg', 'image/jpg'];
                $max_size = 2 * 1024 * 1024;

                if (!in_array($file['type'], $allowed)) {
                    $errors[] = "Image " . ($idx + 1) . ": Invalid file type.";
                } elseif ($file['size'] > $max_size) {
                    $errors[] = "Image " . ($idx + 1) . ": File too large (>2MB).";
                } elseif ($file['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = "Image " . ($idx + 1) . ": Upload error.";
                } else {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = "gallery_" . time() . "_{$idx}.{$ext}";
                    $path = './uploads/gallery/' . $filename;

                    if (move_uploaded_file($file['tmp_name'], $path)) {
                        $current_path = 'uploads/gallery/' . $filename;
                    } else {
                        $errors[] = "Image " . ($idx + 1) . ": Failed to save.";
                    }
                }
            }

            if ($current_path) {
                $image_data[] = [
                    'id' => $id,
                    'image' => $current_path,
                    'caption' => $caption,
                    'image_order' => count($image_data) + 1
                ];
            }
        }

        if (count($image_data) < 1) {
            $errors[] = 'At least one image is required.';
        }

        // Save
        if (empty($errors)) {
            // Section
            $stmt = $mysqli->prepare("INSERT INTO ws_gallery (id, type, subtitle, title, description) VALUES (1, 'section', ?, ?, ?) ON DUPLICATE KEY UPDATE subtitle = ?, title = ?, description = ?");
            $stmt->bind_param('ssssss', $subtitle, $title, $desc, $subtitle, $title, $desc);
            $stmt->execute();
            $stmt->close();

            // Delete old images
            $mysqli->query("DELETE FROM ws_gallery WHERE type = 'image'");

            // Insert new
            foreach ($image_data as $img) {
                if ($img['id'] > 0) {
                    $stmt = $mysqli->prepare("INSERT INTO ws_gallery (id, type, image, caption, image_order) VALUES (?, 'image', ?, ?, ?)");
                    $stmt->bind_param('issi', $img['id'], $img['image'], $img['caption'], $img['image_order']);
                } else {
                    $stmt = $mysqli->prepare("INSERT INTO ws_gallery (type, image, caption, image_order) VALUES ('image', ?, ?, ?)");
                    $stmt->bind_param('ssi', $img['image'], $img['caption'], $img['image_order']);
                }
                $stmt->execute();
                $stmt->close();
            }

            header("Location: home_gallery.php?success=1");
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
            <p class="text-green-600 mb-4">Gallery saved successfully!</p>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6" id="gallery-form">
            <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf_token']; ?>">

            <!-- Section Settings -->
            <div>
                <h3 class="text-lg font-medium mb-3">Section Title</h3>
                <div class="grid md:grid-cols-3 gap-4">
                    <input type="text" name="section_subtitle" value="<?php echo htmlspecialchars($section['subtitle']); ?>" placeholder="Subtitle" class="p-2 border rounded" required>
                    <input type="text" name="section_title" value="<?php echo htmlspecialchars($section['title']); ?>" placeholder="Title" class="p-2 border rounded" required>
                    <textarea name="section_description" rows="2" placeholder="Description" class="p-2 border rounded" required><?php echo htmlspecialchars($section['description']); ?></textarea>
                </div>
            </div>

            <!-- Dynamic Images + Captions -->
            <div>
                <div class="flex justify-between mb-3">
                    <h3 class="text-lg font-medium">Gallery Images</h3>
                    <button type="button" id="add-image" class="bg-green-600 text-white px-3 py-1 rounded text-sm">+ Add Image</button>
                </div>
                <div id="images-container" class="space-y-4">
                    <?php foreach ($images as $img): ?>
                        <div class="border p-4 rounded bg-gray-50 image-item">
                            <input type="hidden" name="image_id[]" value="<?php echo $img['id']; ?>">
                            <div class="grid md:grid-cols-3 gap-4 items-start">
                                <div>
                                    <input type="file" name="image[]" accept="image/png,image/jpeg,image/jpg" class="w-full p-2 border rounded">
                                    <?php if (!empty($img['image'])): ?>
                                        <img src="<?php echo URLROOT . '/cms/' . $img['image']; ?>" class="mt-2 w-full h-32 object-cover rounded" alt="preview">
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Caption (for hover)</label>
                                    <input type="text" name="caption[]" value="<?php echo htmlspecialchars($img['caption'] ?? ''); ?>" placeholder="e.g. Luxurious Spa Room" class="w-full p-2 border rounded">
                                </div>
                                <div class="flex items-center justify-center">
                                    <button type="button" class="remove-image text-red-600 text-sm">Remove</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded">Save Gallery</button>
                <a href="index.php" class="px-5 py-2 border rounded text-gray-700">Cancel</a>
            </div>
        </form>
    </div>
</section>

<script>
    document.getElementById('add-image').addEventListener('click', function () {
        const container = document.getElementById('images-container');
        const div = document.createElement('div');
        div.className = 'border p-4 rounded bg-gray-50 image-item';
        div.innerHTML = `
            <input type="hidden" name="image_id[]" value="0">
            <div class="grid md:grid-cols-3 gap-4 items-start">
                <div>
                    <input type="file" name="image[]" accept="image/png,image/jpeg,image/jpg" class="w-full p-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Caption (for hover)</label>
                    <input type="text" name="caption[]" placeholder="e.g. Luxurious Spa Room" class="w-full p-2 border rounded">
                </div>
                <div class="flex items-center justify-center">
                    <button type="button" class="remove-image text-red-600 text-sm">Remove</button>
                </div>
            </div>
        `;
        container.appendChild(div);
        div.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-image')) {
            e.target.closest('.image-item').remove();
        }
    });
</script>

<?php include 'includes/footer.php'; ?>