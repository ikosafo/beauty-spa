<?php
require_once 'config.php';

// Initialize response array
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    // Validate email
    if (empty($email)) {
        $response['message'] = 'Please enter an email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please enter a valid email address.';
    } else {
        // Check if email already exists
        $stmt = $mysqli->prepare("SELECT id FROM ws_subscriptions WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $response['message'] = 'This email is already subscribed.';
        } else {
            // Insert new subscription
            $stmt = $mysqli->prepare("INSERT INTO ws_subscriptions (email) VALUES (?)");
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Thank you for subscribing!';
            } else {
                $response['message'] = 'Failed to subscribe. Please try again later.';
                error_log('Subscription insert failed: ' . $mysqli->error);
            }
        }
        $stmt->close();
    }
} else {
    $response['message'] = 'Invalid request.';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>