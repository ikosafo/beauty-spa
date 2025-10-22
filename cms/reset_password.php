<?php
// cms/reset_password.php
require_once '../config.php';

$page_title = 'Reset Password';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// End session if user is already logged in
if (isset($_SESSION['user_id'])) {
    session_destroy();
    session_start();
}

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\wamp64\logs\php_error.log');

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
    } else {
        if (isset($_POST['email'])) {
            // Step 1: Handle email submission
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

            if (empty($email)) {
                $_SESSION['error'] = 'Email is required';
            } else {
                $query = "SELECT id FROM ws_users WHERE email = ?";
                $stmt = $mysqli->prepare($query);
                if ($stmt) {
                    $stmt->bind_param('s', $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    $stmt->close();

                    if ($user) {
                        // Generate reset token
                        $token = bin2hex(random_bytes(16));
                        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                        $query = "UPDATE ws_users SET reset_token = ?, reset_token_expires = ? WHERE email = ?";
                        $stmt = $mysqli->prepare($query);
                        $stmt->bind_param('sss', $token, $expires, $email);
                        $stmt->execute();
                        $stmt->close();

                        $_SESSION['success'] = "Your reset token is: <strong>$token</strong><br>Use it below to reset your password. (Expires in 1 hour)";
                    } else {
                        $_SESSION['error'] = 'Email not found';
                    }
                } else {
                    $_SESSION['error'] = 'Database error: ' . $mysqli->error;
                }
            }
        } elseif (isset($_POST['reset_token'], $_POST['new_password'], $_POST['confirm_password'])) {
            // Step 2: Handle password reset
            $token = $_POST['reset_token'];
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($token) || empty($new_password) || empty($confirm_password)) {
                $_SESSION['error'] = 'All fields are required';
            } elseif ($new_password !== $confirm_password) {
                $_SESSION['error'] = 'Passwords do not match';
            } else {
                $query = "SELECT id, email FROM ws_users WHERE reset_token = ? AND reset_token_expires > NOW()";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('s', $token);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();

                if ($user) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $query = "UPDATE ws_users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?";
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param('si', $hashed_password, $user['id']);
                    $stmt->execute();
                    $stmt->close();

                    $_SESSION['success'] = 'Password reset successfully. Please log in.';
                    header('Location: ' . URLROOT . 'cms/login.php');
                    exit;
                } else {
                    $_SESSION['error'] = 'Invalid or expired token';
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
    <title>TAYMAC - <?php echo htmlspecialchars($page_title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        aside, header, footer { display: none !important; }
        .password-toggle {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-200 font-sans">
    <!-- Reset Password Form -->
    <section class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-6 rounded shadow w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">Reset Password</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <p class="text-red-600 mb-4"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
            <?php endif; ?>
            <?php if (isset($_SESSION['success'])): ?>
                <p class="text-green-600 mb-4"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
            <?php endif; ?>

            <!-- Step 1: Request Token -->
            <form method="POST" class="space-y-4 mb-6">
                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="email">Email</label>
                    <input type="email" name="email" id="email" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Generate Reset Token</button>
                </div>
            </form>

            <!-- Step 2: Reset Password -->
            <form method="POST" class="space-y-4">
                <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="reset_token">Reset Token</label>
                    <input type="text" name="reset_token" id="reset_token" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="new_password">New Password</label>
                    <div class="relative">
                        <input type="password" name="new_password" id="new_password" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                        <span class="password-toggle" id="toggle-new-password"><i class="fas fa-eye"></i></span>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="confirm_password">Confirm Password</label>
                    <div class="relative">
                        <input type="password" name="confirm_password" id="confirm_password" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                        <span class="password-toggle" id="toggle-confirm-password"><i class="fas fa-eye"></i></span>
                    </div>
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Reset Password</button>
                </div>
                <div class="text-center">
                    <a href="<?php echo URLROOT; ?>cms/login.php" class="text-blue-600 hover:underline">Back to Login</a>
                </div>
            </form>
        </div>
    </section>

    <script>
        const toggle = (inputId, toggleId) => {
            const input = document.getElementById(inputId);
            const toggle = document.getElementById(toggleId);
            if (input && toggle) {
                toggle.addEventListener('click', () => {
                    const type = input.type === 'password' ? 'text' : 'password';
                    input.type = type;
                    toggle.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                });
            }
        };
        toggle('new_password', 'toggle-new-password');
        toggle('confirm_password', 'toggle-confirm-password');
    </script>
</body>
</html>
