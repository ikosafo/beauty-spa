<?php
// cms/login.php

require_once '../config.php';

$page_title = 'CMS Login';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// End session if user is already logged in
if (isset($_SESSION['user_id'])) {
    session_destroy();
    session_start();
    error_log('Session destroyed for user accessing login page');
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf'])) {
    error_log('Login form submitted');
    error_log('$_POST: ' . print_r($_POST, true));

    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
        error_log('CSRF validation failed');
    } else {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required';
            error_log('Validation failed: Missing email or password');
        } else {
            $query = "SELECT id, name, email, password, role FROM ws_users WHERE email = ?";
            $stmt = $mysqli->prepare($query);
            if (!$stmt) {
                $_SESSION['error'] = 'Database error: ' . $mysqli->error;
                error_log('Prepare failed: ' . $mysqli->error);
            } else {
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    error_log('Login successful for user: ' . $user['email']);
                    header('Location: ' . URLROOT . 'cms/index.php');
                    exit;
                } else {
                    $_SESSION['error'] = 'Invalid email or password';
                    error_log('Login failed: Invalid credentials for ' . $email);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            <!-- Login Form -->
            <section class="min-h-screen flex items-center justify-center">
                <div class="bg-white p-6 rounded shadow w-full max-w-md">
                    <div class="flex justify-center mb-6">
                        <img src="./assets/images/logo.jpg" alt="CMS Logo" class="h-12">
                    </div>
                    <h2 class="text-2xl font-bold mb-6 text-center">CMS Login</h2>
                    <?php if (isset($_SESSION['error'])): ?>
                        <p class="text-red-600 mb-4"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
                    <?php endif; ?>
                    <form method="POST" class="space-y-4" id="login-form">
                        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1" for="email">Email</label>
                            <input type="email" name="email" id="email" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                        </div>
                        <div class="relative">
                            <label class="block text-gray-700 font-medium mb-1" for="password">Password</label>
                            <input type="password" name="password" id="password" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer password-toggle" id="toggle-password">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div>
                            <button type="submit" id="submit-button" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Login</button>
                        </div>
                        <div class="text-center">
                            <a href="<?php echo URLROOT; ?>cms/reset_password.php" class="text-blue-600 hover:underline">Forgot Password?</a>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        console.log('Login form initialized');
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');

        if (passwordInput && togglePassword) {
            togglePassword.addEventListener('click', () => {
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                togglePassword.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                console.log('Password toggle clicked, type changed to: ' + type);
            });
        } else {
            console.error('Password input or toggle element not found');
        }

        document.getElementById('login-form').addEventListener('submit', () => {
            console.log('Login form submitted');
            document.getElementById('submit-button').disabled = true;
        });
    </script>
</body>
</html>