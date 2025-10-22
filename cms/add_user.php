<?php
// cms/add_user.php

require_once '../config.php';

$page_title = 'Add CMS User';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error'] = 'Access denied. Admins only.';
    header('Location: ' . URLROOT . 'cms/login.php');
    exit;
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('Add user form submitted');
    error_log('$_POST: ' . print_r($_POST, true));

    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        error_log('CSRF validation failed');
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'editor';

        if (empty($name) || empty($email) || empty($password) || !in_array($role, ['admin', 'editor'])) {
            $_SESSION['error'] = 'All fields are required, and role must be admin or editor';
            error_log('Validation failed: Missing or invalid fields');
        } elseif (strlen($password) < 8) {
            $_SESSION['error'] = 'Password must be at least 8 characters long';
            error_log('Validation failed: Password too short');
        } else {
            $query = "SELECT id FROM ws_users WHERE email = ?";
            $stmt = $mysqli->prepare($query);
            if (!$stmt) {
                $_SESSION['error'] = 'Database error: ' . $mysqli->error;
                error_log('Prepare failed: ' . $mysqli->error);
            } else {
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $_SESSION['error'] = 'Email already exists';
                    error_log('Validation failed: Email exists');
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $query = "INSERT INTO ws_users (name, email, password, role) VALUES (?, ?, ?, ?)";
                    $stmt = $mysqli->prepare($query);
                    if (!$stmt) {
                        $_SESSION['error'] = 'Database error: ' . $mysqli->error;
                        error_log('Prepare failed: ' . $mysqli->error);
                    } else {
                        $stmt->bind_param('ssss', $name, $email, $password_hash, $role);
                        if (!$stmt->execute()) {
                            $_SESSION['error'] = 'Failed to add user: ' . $stmt->error;
                            error_log('Insert failed: ' . $stmt->error);
                        } else {
                            $_SESSION['success'] = 'User added successfully';
                            error_log('User added: ' . $email);
                        }
                        $stmt->close();
                    }
                }
            }
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'Admin Panel'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .submenu { display: none; }
        .submenu.open { display: block; }
        .submenu a.active { background-color: #3b82f6; color: white; }
    </style>
</head>
<body class="bg-gray-200 font-sans">
    <div class="flex min-h-screen">
       
        <!-- Main Content -->
        <main class="flex-1 p-6">

<!-- Add User Form -->
<section>
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-medium mb-4">Add New CMS User</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <p class="text-green-600 mb-4"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4" id="add-user-form">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="name">Name</label>
                <input type="text" name="name" id="name" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="email">Email</label>
                <input type="email" name="email" id="email" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div class="relative">
                <label class="block text-gray-700 font-medium mb-1" for="password">Password (min 8 characters)</label>
                <input type="password" name="password" id="password" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                <span class="password-toggle" id="toggle-password">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="role">Role</label>
                <select name="role" id="role" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                    <option value="editor">Editor</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div>
                <button type="submit" id="submit-button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add User</button>
                <a href="<?php echo URLROOT; ?>cms/index.php" class="ml-4 text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</section>

<script>
    console.log('Add user form initialized');
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('toggle-password');

    togglePassword.addEventListener('click', () => {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        togglePassword.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    document.getElementById('add-user-form').addEventListener('submit', () => {
        console.log('Add user form submitted');
        document.getElementById('submit-button').disabled = true;
    });
</script>

<?php include 'includes/footer.php'; ?>