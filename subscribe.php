<?php
require_once './config.php';

define('DEBUG_MODE', false);

function console_log($data) {
    if (DEBUG_MODE) {
        echo '<script>console.log(' . json_encode($data, JSON_UNESCAPED_UNICODE) . ');</script>';
    }
}

ob_start();
$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['email'])) {
        $response['message'] = 'Invalid request.';
        goto send_json;
    }

    $email = trim($_POST['email']);
    $subscribe_date = date('F j, Y');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please enter a valid email address.';
        goto send_json;
    }

    // ---- duplicate check ------------------------------------------------
    $stmt = $mysqli->prepare("SELECT id FROM ws_subscriptions WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $response['message'] = 'This email is already subscribed.';
        $stmt->close();
        goto send_json;
    }
    $stmt->close();

    // ---- insert ---------------------------------------------------------
    $stmt = $mysqli->prepare("INSERT INTO ws_subscriptions (email, created_at) VALUES (?, NOW())");
    $stmt->bind_param('s', $email);
    $ok = $stmt->execute();
    $stmt->close();

    if (!$ok) {
        $response['message'] = 'Subscription failed. Please try again.';
        goto send_json;
    }

    // ---- SEND SMS (admin only) -----------------------------------------
    $sms_text = "New subscriber!\nEmail: {$email}\nSubscribed on: {$subscribe_date}\n\nGolden View Therapeutic Clinik and Spa.";
    global $admin_phone;                     // <-- get admin phone from config
    $sms_ok = sendSMSMessage($admin_phone, $sms_text, 'Sebson');

    $response['success'] = true;
    $response['message'] = 'Thank you for subscribing!' . ($sms_ok ? ' SMS sent!' : '');

    if (DEBUG_MODE) {
        console_log(['subscription' => $email, 'sms' => $sms_ok ? 'sent' : 'failed']);
    }

} catch (Exception $e) {
    $response['message'] = 'Server error. Please try again.';
    if (DEBUG_MODE) console_log(['exception' => $e->getMessage()]);
}

send_json:
ob_clean();
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>