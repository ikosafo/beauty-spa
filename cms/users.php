<?php
// cms/users.php
require_once '../config.php';

$page_title = 'Manage Users';

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
        header('Location: users.php');
        exit;
    }

    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    // ------------------------------------------------
    // ADD USER
    // ------------------------------------------------
    if ($action === 'add') {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = in_array($_POST['role'] ?? '', ['admin', 'user']) ? $_POST['role'] : 'user';

        if (!$name || !$email || !$password) {
            $_SESSION['error'] = 'Name, email, and password are required.';
            header('Location: users.php');
            exit;
        }

        // Check duplicate email
        $check = $mysqli->prepare("SELECT id FROM ws_users WHERE email = ?");
        $check->bind_param('s', $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $_SESSION['error'] = 'Email already exists.';
            $check->close();
            header('Location: users.php');
            exit;
        }
        $check->close();

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("INSERT INTO ws_users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $name, $email, $hash, $role);

        if ($stmt->execute()) {
            $_SESSION['success'] = "User '{$name}' added successfully.";
        } else {
            $_SESSION['error'] = 'Failed to add user.';
        }
        $stmt->close();
    }

    // ------------------------------------------------
    // EDIT USER
    // ------------------------------------------------
    elseif ($action === 'edit') {
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role  = in_array($_POST['role'] ?? '', ['admin', 'user']) ? $_POST['role'] : 'user';

        if ($id <= 0 || !$name || !$email) {
            $_SESSION['error'] = 'All fields are required.';
            header('Location: users.php');
            exit;
        }

        $stmt = $mysqli->prepare("UPDATE ws_users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param('sssi', $name, $email, $role, $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "User updated.";
        } else {
            $_SESSION['error'] = 'Update failed.';
        }
        $stmt->close();
    }

    // ------------------------------------------------
    // RESET PASSWORD
    // ------------------------------------------------
    elseif ($action === 'reset_password') {
        if ($id <= 0) {
            $_SESSION['error'] = 'Invalid user.';
            header('Location: users.php');
            exit;
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $mysqli->prepare("UPDATE ws_users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
        $stmt->bind_param('ssi', $token, $expires, $id);

        if ($stmt->execute()) {
            $reset_link = URLROOT . "/reset-password.php?token=" . urlencode($token);
            $_SESSION['success'] = "Reset link generated:<br><code class='bg-light p-2 d-block mt-2'>$reset_link</code><br><small>Valid for 1 hour.</small>";
        } else {
            $_SESSION['error'] = 'Reset failed.';
        }
        $stmt->close();
    }

    // ------------------------------------------------
    // DELETE USER
    // ------------------------------------------------
    elseif ($action === 'delete') {
        if ($id <= 0 || $id === ($_SESSION['user_id'] ?? 0)) {
            $_SESSION['error'] = 'Cannot delete yourself or invalid ID.';
            header('Location: users.php');
            exit;
        }

        $stmt = $mysqli->prepare("DELETE FROM ws_users WHERE id = ?");
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'User deleted.';
        } else {
            $_SESSION['error'] = 'Delete failed.';
        }
        $stmt->close();
    }

    header('Location: users.php');
    exit;
}

// ------------------------------------------------
// 2. FETCH ALL USERS
// ------------------------------------------------
$users = [];
$query = "SELECT id, name, email, role, created_at FROM ws_users ORDER BY created_at DESC";
$result = $mysqli->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[$row['id']] = $row;
    }
} else {
    $no_data = true;
}

include 'includes/header.php';
?>

<section>
    <div class="bg-white p-6 rounded shadow">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-xl font-bold mb-0">Manage Users</h3>
            <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm" 
                    onclick="openAddModal()">+ Add User</button>
        </div>

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
            <p class="text-orange-600">No users found.</p>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($users as $id => $u): ?>
                    <div class="border p-5 rounded-lg bg-gray-50">
                        <form method="POST" class="grid md:grid-cols-2 gap-4">
                            <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">

                            <!-- Name -->
                            <div>
                                <label class="block font-medium mb-1">Name</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($u['name']); ?>" 
                                       class="w-full p-2 border rounded" required>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block font-medium mb-1">Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($u['email']); ?>" 
                                       class="w-full p-2 border rounded" required>
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block font-medium mb-1">Role</label>
                                <select name="role" class="w-full p-2 border rounded">
                                    <option value="user" <?php echo $u['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                    <option value="admin" <?php echo $u['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </div>

                            <!-- Created -->
                            <div>
                                <label class="block font-medium mb-1">Created</label>
                                <input type="text" value="<?php echo date('M j, Y g:i A', strtotime($u['created_at'])); ?>" 
                                       class="w-full p-2 border rounded bg-gray-100" disabled>
                            </div>

                            <div class="md:col-span-2 flex gap-2 items-center">
                                <button type="submit" 
                                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                                    Save Changes
                                </button>

                                <button type="button" 
                                        onclick="confirmReset(<?php echo $id; ?>, '<?php echo htmlspecialchars($u['name']); ?>')"
                                        class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 text-sm">
                                    Reset Password
                                </button>

                                <form method="POST" class="inline" onsubmit="return confirm('Delete user <?php echo htmlspecialchars($u['name']); ?>?');">
                                    <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                                    <button type="submit" 
                                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ADD USER MODAL -->
<div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h3 class="text-xl font-bold mb-4">Add New User</h3>
        <form method="POST">
            <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="add">

            <div class="mb-3">
                <label class="block font-medium mb-1">Name</label>
                <input type="text" name="name" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-3">
                <label class="block font-medium mb-1">Email</label>
                <input type="email" name="email" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-3">
                <label class="block font-medium mb-1">Password</label>
                <input type="password" name="password" class="w-full p-2 border rounded" required minlength="6">
            </div>
            <div class="mb-3">
                <label class="block font-medium mb-1">Role</label>
                <select name="role" class="w-full p-2 border rounded">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Add User
                </button>
                <button type="button" onclick="closeAddModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- RESET CONFIRM MODAL -->
<div id="resetModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm">
        <h3 class="text-lg font-bold mb-3">Reset Password</h3>
        <p>Generate a password reset link for <strong id="resetUserName"></strong>?</p>
        <form method="POST" id="resetForm">
            <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="reset_password">
            <input type="hidden" name="id" id="resetUserId">
            <div class="flex gap-2 mt-4">
                <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                    Generate Link
                </button>
                <button type="button" onclick="closeResetModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Open Add Modal
function openAddModal() {
    document.getElementById('addUserModal').classList.remove('hidden');
}

// Close Add Modal
function closeAddModal() {
    document.getElementById('addUserModal').classList.add('hidden');
}

// Confirm Reset
function confirmReset(id, name) {
    document.getElementById('resetUserId').value = id;
    document.getElementById('resetUserName').textContent = name;
    document.getElementById('resetModal').classList.remove('hidden');
}

// Close Reset Modal
function closeResetModal() {
    document.getElementById('resetModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(e) {
    const addModal = document.getElementById('addUserModal');
    const resetModal = document.getElementById('resetModal');
    if (e.target === addModal) closeAddModal();
    if (e.target === resetModal) closeResetModal();
};
</script>

<?php include 'includes/footer.php'; ?>