<?php
// cms/login.php
require_once '../config.php';

$page_title = 'CMS Login';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Logout if already logged in
if (isset($_SESSION['user_id'])) {
    session_destroy();
    session_start();
}

// CSRF Token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf'])) {
    if ($_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid CSRF token';
    } else {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required';
        } else {
            $stmt = $mysqli->prepare("SELECT id, name, email, password, role FROM ws_users WHERE email = ?");
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
                header('Location: ' . URLROOT . 'cms/index.php');
                exit;
            } else {
                $_SESSION['error'] = 'Invalid email or password';
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
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="./assets/images/logo.jpg">
    <style>
        /* Full-screen centered container – NO SCROLL */
        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }
        .login-container {
            min-height: 100vh;
        }
        /* Eye icon perfectly centered */
        .password-wrapper {
            position: relative;
        }
       
        .password-toggle {
            position: absolute;
            top: 70%;
            right: 0.75rem;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
        }
        .password-toggle:hover {
            color: #1d4ed8;
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="login-container flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <img src="./assets/images/logo.jpg" alt="CMS Logo" class="h-13" style="height:105px">
        </div>

        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">CMS Login</h2>

        <!-- Error Message -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-50 text-red-700 p-3 rounded mb-4 text-sm">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" class="space-y-5">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Password -->
            <div class="password-wrapper">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <span class="password-toggle" id="toggle-password">
                    <i class="fas fa-eye"></i>
                </span>
            </div>

            <!-- Submit -->
            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2.5 rounded-md hover:bg-blue-700 transition font-medium">
                Login
            </button>

            <!-- Forgot Password -->
            <div class="text-center mt-4">
                <a href="<?php echo URLROOT; ?>cms/reset_password.php"
                   class="text-sm text-blue-600 hover:underline">Forgot Password?</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Toggle password visibility
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('toggle-password');

    toggleBtn.addEventListener('click', () => {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        toggleBtn.innerHTML = type === 'password'
            ? '<i class="fas fa-eye"></i>'
            : '<i class="fas fa-eye-slash"></i>';
    });
</script>

</body>
</html>