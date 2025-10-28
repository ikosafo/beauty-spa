<?php
// cms/services.php
require_once '../config.php';

$page_title = 'Edit Service Details';

// ------------------------------------------------
// CSRF Token
// ------------------------------------------------
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/logs/php_error.log');

// ------------------------------------------------
// 1. PROCESS FORM SUBMISSION (POST)
// ------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF Check
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Invalid CSRF token.';
        header('Location: services.php');
        exit;
    }

    $id          = (int)($_POST['id'] ?? 0);
    $title       = trim($_POST['title'] ?? '');
    $link        = trim($_POST['link'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $benefits    = array_filter(array_map('trim', $_POST['benefits'] ?? []));

    // Basic validation
    if ($id <= 0 || $title === '' || $link === '' || $description === '') {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: services.php');
        exit;
    }

    // Ensure service exists and is of type 'detail'
    $check = $mysqli->prepare("SELECT id, image FROM ws_services WHERE id = ? AND type = 'detail'");
    $check->bind_param('i', $id);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows === 0) {
        $_SESSION['error'] = 'Service not found.';
        header('Location: services.php');
        exit;
    }
    $current = $result->fetch_assoc();
    $oldImage = $current['image'] ?? '';
    $check->close();

    // ------------------------------------------------
    // IMAGE UPLOAD HANDLING → cms/uploads/services/
    // ------------------------------------------------
    $uploadDir     = __DIR__ . '/uploads/services/';           // Absolute path
    $relativeDir   = 'cms/uploads/services/';                  // Relative path stored in DB
    $newImagePath  = $oldImage; // keep old unless replaced

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp  = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];

        // Size limit: 2MB
        if ($fileSize > 2 * 1024 * 1024) {
            $_SESSION['error'] = 'Image must be 2MB or smaller.';
            header('Location: services.php');
            exit;
        }

        // Allowed MIME types
        $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($fileType, $allowed, true)) {
            $_SESSION['error'] = 'Only JPG, PNG, or WEBP images allowed.';
            header('Location: services.php');
            exit;
        }

        // Create directory if not exists
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                $_SESSION['error'] = 'Failed to create upload directory.';
                header('Location: services.php');
                exit;
            }
        }

        // Generate unique filename
        $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $baseName = "service_{$id}_" . time() . '_' . bin2hex(random_bytes(4));
        $targetFile = $uploadDir . $baseName . '.' . $ext;
        $dbPath     = $relativeDir . $baseName . '.' . $ext;

        // Move uploaded file
        if (!move_uploaded_file($fileTmp, $targetFile)) {
            $_SESSION['error'] = 'Failed to save image.';
            header('Location: services.php');
            exit;
        }

        // Delete old image if different
        if ($oldImage && $oldImage !== $dbPath && file_exists('../' . $oldImage)) {
            @unlink('../' . $oldImage);
        }

        $newImagePath = $dbPath;
    }

    // ------------------------------------------------
    // UPDATE DATABASE
    // ------------------------------------------------
    $benefitsJson = json_encode(array_values($benefits), JSON_UNESCAPED_UNICODE);

    $stmt = $mysqli->prepare("
        UPDATE ws_services 
        SET title = ?, link = ?, description = ?, image = ?, benefits = ?
        WHERE id = ? AND type = 'detail'
    ");
    $stmt->bind_param('sssssi', $title, $link, $description, $newImagePath, $benefitsJson, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Service '{$title}' updated successfully.";
    } else {
        $_SESSION['error'] = 'Database error: ' . $stmt->error;
    }
    $stmt->close();

    // Redirect
    header('Location: services.php');
    exit;
}

// ------------------------------------------------
// 2. FETCH ALL DETAILED SERVICES (type = 'detail')
// ------------------------------------------------
$services = [];
$query = "SELECT * FROM `ws_services` WHERE `type` = 'detail' ORDER BY id";
$result = $mysqli->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['benefits'] = $row['benefits'] ? json_decode($row['benefits'], true) : [];
        $services[$row['id']] = $row;
    }
} else {
    $no_data = true;
}

include 'includes/header.php';
?>

<section>
    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-xl font-bold mb-4">Edit Service Details</h3>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($no_data)): ?>
            <p class="text-orange-600">No detailed services found. Run the SQL insert script to populate the table.</p>
        <?php else: ?>
            <div class="space-y-8">
                <?php foreach ($services as $id => $s): ?>
                    <form method="POST" enctype="multipart/form-data" class="border p-5 rounded-lg bg-gray-50">
                        <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">

                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <!-- Title -->
                            <div>
                                <label class="block font-medium mb-1">Title</label>
                                <input type="text" name="title" value="<?php echo htmlspecialchars($s['title']); ?>" class="w-full p-2 border rounded" required>
                            </div>

                            <!-- Link -->
                            <div>
                                <label class="block font-medium mb-1">Link (e.g. #healing-therapeutic-massage)</label>
                                <input type="text" name="link" value="<?php echo htmlspecialchars($s['link']); ?>" class="w-full p-2 border rounded" required>
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label class="block font-medium mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full p-2 border rounded" required><?php echo htmlspecialchars($s['description']); ?></textarea>
                            </div>

                            <!-- Image Upload -->
                            <div>
                                <label class="block font-medium mb-1">Image (max 2MB, JPG/PNG/WEBP)</label>
                                <input type="file" name="image" accept="image/*" class="w-full p-2 border rounded">
                                <?php if ($s['image']): ?>
                                    <div class="mt-2">
                                        <img src="<?php echo URLROOT . '/' . $s['image']; ?>" class="h-32 object-cover rounded border" alt="Current">
                                        <small class="block text-gray-600 mt-1">Current: <?php echo htmlspecialchars(basename($s['image'])); ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Benefits -->
                            <div>
                                <label class="block font-medium mb-1">Benefits</label>
                                <div id="benefits-container-<?php echo $id; ?>" class="space-y-1">
                                    <?php foreach ($s['benefits'] as $i => $b): ?>
                                        <div class="flex gap-2">
                                            <input type="text" name="benefits[]" value="<?php echo htmlspecialchars($b); ?>" class="flex-1 p-2 border rounded" placeholder="Benefit">
                                            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:underline">Remove</button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" onclick="addBenefit(<?php echo $id; ?>)" class="text-blue-600 text-sm mt-1">+ Add Benefit</button>
                            </div>
                        </div>

                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
                            Save Changes
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function addBenefit(id) {
    const container = document.getElementById('benefits-container-' + id);
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" name="benefits[]" class="flex-1 p-2 border rounded" placeholder="Benefit">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:underline">Remove</button>
    `;
    container.appendChild(div);
}
</script>

<?php include 'includes/footer.php'; ?>