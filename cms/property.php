<?php
// cms/property.php

require_once '../config.php';

$page_title = 'Edit Property Section';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Create uploads directory if it doesn't exist
$upload_dir = 'uploads/properties/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Fetch properties
$query = "SELECT * FROM ws_properties ORDER BY id";
$result = $mysqli->query($query);
$properties = [];
if (!$result) {
    error_log('Fetch properties query failed: ' . $mysqli->error);
} else {
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
}
error_log('Fetched properties: ' . print_r($properties, true));

// Fetch "What we do" content
$query = "SELECT * FROM ws_property_content WHERE id = 1";
$result = $mysqli->query($query);
if (!$result) {
    $_SESSION['error'] = 'Fetch content query failed: ' . $mysqli->error;
    error_log('Fetch content query failed: ' . $mysqli->error);
}
$content = $result->fetch_assoc();
if (!$content) {
    $content = [
        'subtitle' => 'What we do',
        'paragraph1' => 'At Taymac, we deliver an exceptional property management experience that preserves and enhances the asset value of your property throughout its lifespan. Our highly skilled team is dedicated to providing top-tier service in maintaining and protecting the quality of properties under our care.',
        'paragraph2' => 'As a full-service residential, non-residential, and commercial property management company operating in Ghana, we leverage our expertise, the latest technology, and superior customer service to ensure a seamless, stress-free experience for property owners. Our clientele includes prominent diplomatic missions and a range of organizations in Ghana.',
        'paragraph3' => 'In addition to property management, we also offer comprehensive office fit-out services for banks and multinational corporations across Ghana. Specializing in aluminium glazing and office partitions, we work with both glass and gypsum board materials to create customized solutions. With Taymac, property owners can maximize the financial benefits of their assets without the complexities of handling time-consuming management tasks.',
        'paragraph4' => 'Being a landlord goes far beyond collecting rent; it includes addressing challenges like dealing with difficult tenants, coordinating major and minor repairs, and maintaining the overall value of the property. We also handle move-ins, inspections, contractor management, and all necessary paperwork. At Taymac, we take care of every aspect of property management, both big and small, providing our clients with the confidence that their property is in trusted hands.',
        'list_item1' => 'Security management to safeguard your property.',
        'list_item2' => 'Ensuring compliance with Health, Safety, Security, and Environmental (HSSE) standards.',
        'list_item3' => 'Daily janitorial services for a clean and well-maintained environment.',
        'list_item4' => 'Implementation of planned preventative and reactive maintenance schedules to preserve the value and functionality of your property.',
        'list_item5' => 'Internal office fit-outs, specializing in aluminium glazing and gypsum board partitions tailored to your requirements.'
    ];
}
error_log('Fetched property content: ' . print_r($content, true));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Form submitted with POST method');
    error_log('$_POST: ' . print_r($_POST, true));
    error_log('$_FILES: ' . print_r($_FILES, true));

    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        error_log('CSRF validation failed');
    } else {
        // Handle "What we do" content update
        if (isset($_POST['update_content'])) {
            $subtitle = $_POST['subtitle'] ?? '';
            $paragraph1 = $_POST['paragraph1'] ?? '';
            $paragraph2 = $_POST['paragraph2'] ?? '';
            $paragraph3 = $_POST['paragraph3'] ?? '';
            $paragraph4 = $_POST['paragraph4'] ?? '';
            $list_item1 = $_POST['list_item1'] ?? '';
            $list_item2 = $_POST['list_item2'] ?? '';
            $list_item3 = $_POST['list_item3'] ?? '';
            $list_item4 = $_POST['list_item4'] ?? '';
            $list_item5 = $_POST['list_item5'] ?? '';

            if (empty($subtitle) || empty($paragraph1) || empty($paragraph2) || empty($paragraph3) || empty($paragraph4) ||
                empty($list_item1) || empty($list_item2) || empty($list_item3) || empty($list_item4) || empty($list_item5)) {
                $_SESSION['error'] = 'All content fields are required';
                error_log('Content validation failed: Missing required fields');
            } else {
                $query = "SELECT COUNT(*) AS count FROM ws_property_content WHERE id = 1";
                $result = $mysqli->query($query);
                if (!$result) {
                    $_SESSION['error'] = 'Check content query failed: ' . $mysqli->error;
                    error_log('Check content query failed: ' . $mysqli->error);
                }
                $row_exists = $result->fetch_assoc()['count'] > 0;

                if ($row_exists) {
                    $query = "UPDATE ws_property_content SET subtitle = ?, paragraph1 = ?, paragraph2 = ?, paragraph3 = ?, paragraph4 = ?, list_item1 = ?, list_item2 = ?, list_item3 = ?, list_item4 = ?, list_item5 = ? WHERE id = 1";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $_SESSION['error'] = 'Prepare content failed: ' . $mysqli->error;
                        error_log('Prepare content failed: ' . $mysqli->error);
                    } else {
                        $stmt->bind_param('ssssssssss', $subtitle, $paragraph1, $paragraph2, $paragraph3, $paragraph4, $list_item1, $list_item2, $list_item3, $list_item4, $list_item5);
                        if (!$stmt->execute()) {
                            $_SESSION['error'] = 'Update content failed: ' . $stmt->error;
                            error_log('Update content failed: ' . $stmt->error);
                        } else {
                            error_log('Content update successful');
                        }
                        $stmt->close();
                    }
                } else {
                    $query = "INSERT INTO ws_property_content (id, subtitle, paragraph1, paragraph2, paragraph3, paragraph4, list_item1, list_item2, list_item3, list_item4, list_item5) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $_SESSION['error'] = 'Prepare content failed: ' . $mysqli->error;
                        error_log('Prepare content failed: ' . $mysqli->error);
                    } else {
                        $id = 1;
                        $stmt->bind_param('isssssssss', $id, $subtitle, $paragraph1, $paragraph2, $paragraph3, $paragraph4, $list_item1, $list_item2, $list_item3, $list_item4, $list_item5);
                        if (!$stmt->execute()) {
                            $_SESSION['error'] = 'Insert content failed: ' . $stmt->error;
                            error_log('Insert content failed: ' . $stmt->error);
                        } else {
                            error_log('Content insert successful');
                        }
                        $stmt->close();
                    }
                }

                // Refresh content
                $query = "SELECT * FROM ws_property_content WHERE id = 1";
                $result = $mysqli->query($query);
                if (!$result) {
                    $_SESSION['error'] = 'Refresh content query failed: ' . $mysqli->error;
                    error_log('Refresh content query failed: ' . $mysqli->error);
                } else {
                    $content = $result->fetch_assoc();
                    error_log('Content refreshed: ' . print_r($content, true));
                }
            }
        }

        // Handle add property
        if (isset($_POST['add_property'])) {
            $title = $_POST['new_title'] ?? '';
            $description = $_POST['new_description'] ?? '';
            $status = $_POST['new_status'] ?? '';
            $image = $_FILES['new_image'] ?? null;

            if (empty($title) || empty($description) || empty($status) || empty($image['name'])) {
                $_SESSION['error'] = 'All property fields are required';
                error_log('Add property validation failed: Missing required fields');
            } else {
                $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
                $max_size = 5 * 1024 * 1024; // 5MB
                if (!in_array($image['type'], $allowed_types) || $image['size'] > $max_size) {
                    $_SESSION['error'] = 'Invalid image format or size. Use JPG, PNG, or WebP under 5MB.';
                    error_log('Add property validation failed: Invalid image');
                } else {
                    $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
                    $filename = 'property_' . time() . '.' . $ext;
                    $destination = $upload_dir . $filename;
                    if (!move_uploaded_file($image['tmp_name'], $destination)) {
                        $_SESSION['error'] = 'Failed to upload image';
                        error_log('Image upload failed: ' . $image['error']);
                    } else {
                        $query = "INSERT INTO ws_properties (image, title, description, status) VALUES (?, ?, ?, ?)";
                        $stmt = $mysqli->prepare($query);
                        if (!$stmt) {
                            $_SESSION['error'] = 'Prepare add property failed: ' . $mysqli->error;
                            error_log('Prepare add property failed: ' . $mysqli->error);
                        } else {
                            $image_path = 'cms/uploads/properties/' . $filename;
                            $stmt->bind_param('ssss', $image_path, $title, $description, $status);
                            if (!$stmt->execute()) {
                                $_SESSION['error'] = 'Insert property failed: ' . $stmt->error;
                                error_log('Insert property failed: ' . $stmt->error);
                            } else {
                                error_log('Property insert successful');
                            }
                            $stmt->close();
                        }
                    }
                }
            }
        }

        // Handle edit property
        if (isset($_POST['edit_property'])) {
            $edit_id = $_POST['edit_id'] ?? '';
            $title = $_POST['edit_title'] ?? '';
            $description = $_POST['edit_description'] ?? '';
            $status = $_POST['edit_status'] ?? '';
            $image = $_FILES['edit_image'] ?? null;

            if (empty($edit_id) || empty($title) || empty($description) || empty($status)) {
                $_SESSION['error'] = 'All edit property fields are required';
                error_log('Edit property validation failed: Missing required fields');
            } else {
                $query = "SELECT image FROM ws_properties WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('i', $edit_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $old_image = $result->fetch_assoc()['image'];
                $stmt->close();

                $image_path = $old_image;
                if (!empty($image['name'])) {
                    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
                    $max_size = 5 * 1024 * 1024; // 5MB
                    if (!in_array($image['type'], $allowed_types) || $image['size'] > $max_size) {
                        $_SESSION['error'] = 'Invalid image format or size. Use JPG, PNG, or WebP under 5MB.';
                        error_log('Edit property validation failed: Invalid image');
                    } else {
                        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
                        $filename = 'property_' . time() . '.' . $ext;
                        $destination = $upload_dir . $filename;
                        if (!move_uploaded_file($image['tmp_name'], $destination)) {
                            $_SESSION['error'] = 'Failed to upload image';
                            error_log('Image upload failed: ' . $image['error']);
                        } else {
                            $image_path = 'cms/uploads/properties/' . $filename;
                            if (file_exists($old_image)) {
                                unlink($old_image);
                            }
                        }
                    }
                }

                $query = "UPDATE ws_properties SET image = ?, title = ?, description = ?, status = ? WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare edit property failed: ' . $mysqli->error;
                    error_log('Prepare edit property failed: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('ssssi', $image_path, $title, $description, $status, $edit_id);
                    if (!$stmt->execute()) {
                        $_SESSION['error'] = 'Update property failed: ' . $stmt->error;
                        error_log('Update property failed: ' . $stmt->error);
                    } else {
                        error_log('Property update successful');
                    }
                    $stmt->close();
                }
            }
        }

        // Handle delete property
        if (isset($_POST['delete_property'])) {
            $delete_id = $_POST['delete_id'] ?? '';
            if (empty($delete_id)) {
                $_SESSION['error'] = 'No property selected for deletion';
                error_log('Delete property validation failed: No ID');
            } else {
                $query = "SELECT image FROM ws_properties WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('i', $delete_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $image = $result->fetch_assoc()['image'];
                $stmt->close();

                $query = "DELETE FROM ws_properties WHERE id = ?";
                $stmt = $mysqli->prepare($query);
                if (!$stmt) {
                    $_SESSION['error'] = 'Prepare delete property failed: ' . $mysqli->error;
                    error_log('Prepare delete property failed: ' . $mysqli->error);
                } else {
                    $stmt->bind_param('i', $delete_id);
                    if (!$stmt->execute()) {
                        $_SESSION['error'] = 'Delete property failed: ' . $stmt->error;
                        error_log('Delete property failed: ' . $stmt->error);
                    } else {
                        if (file_exists($image)) {
                            unlink($image);
                        }
                        error_log('Property delete successful');
                    }
                    $stmt->close();
                }
            }
        }

        // Refresh properties
        $query = "SELECT * FROM ws_properties ORDER BY id";
        $result = $mysqli->query($query);
        $properties = [];
        if (!$result) {
            $_SESSION['error'] = 'Refresh properties query failed: ' . $mysqli->error;
            error_log('Refresh properties query failed: ' . $mysqli->error);
        } else {
            while ($row = $result->fetch_assoc()) {
                $properties[] = $row;
            }
            error_log('Properties refreshed: ' . print_r($properties, true));
        }
    }
}

include 'includes/header.php';
?>

<!-- Property Edit Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        
        <!-- "What we do" Content Form -->
        <h2 class="text-xl font-medium mb-4">Edit "What we do" Content</h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4 mb-8" id="content-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <input type="hidden" name="update_content" value="1">
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="subtitle">Subtitle</label>
                <input type="text" name="subtitle" id="subtitle" value="<?php echo htmlspecialchars($content['subtitle']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="paragraph1">Paragraph 1</label>
                <textarea name="paragraph1" id="paragraph1" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($content['paragraph1']); ?></textarea>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="paragraph2">Paragraph 2</label>
                <textarea name="paragraph2" id="paragraph2" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($content['paragraph2']); ?></textarea>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="paragraph3">Paragraph 3</label>
                <textarea name="paragraph3" id="paragraph3" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($content['paragraph3']); ?></textarea>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="paragraph4">Paragraph 4</label>
                <textarea name="paragraph4" id="paragraph4" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($content['paragraph4']); ?></textarea>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="list_item1">List Item 1</label>
                <input type="text" name="list_item1" id="list_item1" value="<?php echo htmlspecialchars($content['list_item1']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="list_item2">List Item 2</label>
                <input type="text" name="list_item2" id="list_item2" value="<?php echo htmlspecialchars($content['list_item2']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="list_item3">List Item 3</label>
                <input type="text" name="list_item3" id="list_item3" value="<?php echo htmlspecialchars($content['list_item3']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="list_item4">List Item 4</label>
                <input type="text" name="list_item4" id="list_item4" value="<?php echo htmlspecialchars($content['list_item4']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="list_item5">List Item 5</label>
                <input type="text" name="list_item5" id="list_item5" value="<?php echo htmlspecialchars($content['list_item5']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Content Changes</button>
                <a href="<?php echo URLROOT; ?>cms/index.php" class="ml-4 text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>

        <!-- Add Property Form -->
        <h2 class="text-xl font-medium mb-4">Add New Property</h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4 mb-8" id="add-property-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <input type="hidden" name="add_property" value="1">
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="new_image">Image (JPG, PNG, WebP, max 5MB)</label>
                <input type="file" name="new_image" id="new_image" accept="image/jpeg,image/png,image/webp" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="new_title">Title</label>
                <input type="text" name="new_title" id="new_title" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="new_description">Description</label>
                <textarea name="new_description" id="new_description" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required></textarea>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="new_status">Status</label>
                <input type="text" name="new_status" id="new_status" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Property</button>
                <a href="<?php echo URLROOT; ?>cms/index.php" class="ml-4 text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>

        <!-- Existing Properties -->
        <h2 class="text-xl font-medium mb-4">Manage Existing Properties</h2>
        <?php foreach ($properties as $property): ?>
            <form method="POST" enctype="multipart/form-data" class="space-y-4 mb-4 border p-4 rounded" id="edit-property-form-<?php echo $property['id']; ?>">
                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="edit_property" value="1">
                <input type="hidden" name="edit_id" value="<?php echo $property['id']; ?>">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Current Image</label>
                    <img src="<?php echo URLROOT . htmlspecialchars($property['image']); ?>" alt="Property Image" class="w-32 h-32 object-cover">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="edit_image_<?php echo $property['id']; ?>">New Image (JPG, PNG, WebP, max 5MB, optional)</label>
                    <input type="file" name="edit_image" id="edit_image_<?php echo $property['id']; ?>" accept="image/jpeg,image/png,image/webp" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="edit_title_<?php echo $property['id']; ?>">Title</label>
                    <input type="text" name="edit_title" id="edit_title_<?php echo $property['id']; ?>" value="<?php echo htmlspecialchars($property['title']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="edit_description_<?php echo $property['id']; ?>">Description</label>
                    <textarea name="edit_description" id="edit_description_<?php echo $property['id']; ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" rows="4" required><?php echo htmlspecialchars($property['description']); ?></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="edit_status_<?php echo $property['id']; ?>">Status</label>
                    <input type="text" name="edit_status" id="edit_status_<?php echo $property['id']; ?>" value="<?php echo htmlspecialchars($property['status']); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Property</button>
                    <button type="submit" name="delete_property" value="1" formaction="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this property?');">Delete Property</button>
                </div>
            </form>
        <?php endforeach; ?>
    </div>
</section>

<script>
    console.log('Form initialized');

    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            console.log('Form submit triggered: ' + form.id);
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
            }
        });
    });
</script>

<?php
include 'includes/footer.php';
?>