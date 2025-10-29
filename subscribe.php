<?php
require_once 'config.php';

/* --------------------------------------------------------------
   WhatsApp Cloud API – values come from config.php
   -------------------------------------------------------------- */
$api_version     = WA_VERSION;               // e.g. v22.0
$phone_number_id = WA_PHONE_NUMBER_ID;       // e.g. 892625530596718
$access_token    = WA_ACCESS_TOKEN;          // long token
$to_number       = WA_TO_NUMBER;             // test number first: 15556355258

/* --------------------------------------------------------------
   Helper – write a line to the browser console (visible in DevTools)
   -------------------------------------------------------------- */
function console_log($data) {
    $output = json_encode($data, JSON_UNESCAPED_UNICODE);
    echo "<script>console.log($output);</script>";
}

/* --------------------------------------------------------------
   Response array
   -------------------------------------------------------------- */
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['email'])) {
    $response['message'] = 'Invalid request.';
    console_log(['error' => 'Invalid request method or missing email']);
    goto send_json;
}

/* --------------------------------------------------------------
   1. Validate & dedupe email
   -------------------------------------------------------------- */
$email = trim($_POST['email']);
$subscribe_date = date('F j, Y');   // October 29, 2025

if (empty($email)) {
    $response['message'] = 'Please enter an email address.';
    console_log(['error' => 'Empty email']);
    goto send_json;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Please enter a valid email address.';
    console_log(['error' => 'Invalid email format', 'email' => $email]);
    goto send_json;
}

/* ---- check duplicate ---- */
$stmt = $mysqli->prepare("SELECT id FROM ws_subscriptions WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $response['message'] = 'This email is already subscribed.';
    console_log(['info' => 'Duplicate email', 'email' => $email]);
    $stmt->close();
    goto send_json;
}
$stmt->close();

/* --------------------------------------------------------------
   2. Insert into DB
   -------------------------------------------------------------- */
$stmt = $mysqli->prepare(
    "INSERT INTO ws_subscriptions (email, created_at) VALUES (?, NOW())"
);
$stmt->bind_param('s', $email);
$db_ok = $stmt->execute();
$stmt->close();

if (!$db_ok) {
    $response['message'] = 'Database error – please try again.';
    console_log(['error' => 'DB insert failed', 'mysqli_error' => $mysqli->error]);
    goto send_json;
}

/* --------------------------------------------------------------
   3. Send WhatsApp message (automated)
   -------------------------------------------------------------- */
$wa_text = "New subscriber!\nEmail: {$email}\nSubscribed on: {$subscribe_date}";
$wa_success = sendWhatsAppMessage($to_number, $wa_text);

$response['success'] = true;
$response['message'] = 'Thank you for subscribing!' .
    ($wa_success ? ' WhatsApp sent!' : ' WhatsApp failed – see console.');

console_log([
    'subscription' => $email,
    'whatsapp'     => $wa_success ? 'sent' : 'failed'
]);

/* --------------------------------------------------------------
   End – send JSON back to AJAX
   -------------------------------------------------------------- */
send_json:
header('Content-Type: application/json');
echo json_encode($response);
exit;

/* ==============================================================
   WhatsApp Cloud API – cURL call
   ============================================================== */
function sendWhatsAppMessage(string $to, string $text): bool
{
    global $api_version, $phone_number_id, $access_token;

    $url = "https://graph.facebook.com/{$api_version}/{$phone_number_id}/messages";

    $payload = [
        'messaging_product' => 'whatsapp',
        'to'                 => $to,
        'type'               => 'text',
        'text'               => ['body' => $text]
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token
        ],
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $raw      = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    /* ---- cURL problem ---- */
    if ($curlErr) {
        console_log(['whatsapp_error' => 'cURL error', 'msg' => $curlErr]);
        return false;
    }

    /* ---- HTTP not 200 ---- */
    if ($httpCode !== 200) {
        console_log([
            'whatsapp_error' => "HTTP {$httpCode}",
            'response'       => $raw
        ]);
        return false;
    }

    $decoded = json_decode($raw, true);

    /* ---- Success ---- */
    if (isset($decoded['messages'][0]['id'])) {
        console_log([
            'whatsapp_success' => true,
            'message_id'       => $decoded['messages'][0]['id']
        ]);
        return true;
    }

    /* ---- Unexpected JSON ---- */
    console_log([
        'whatsapp_error' => 'Unexpected API response',
        'raw'            => $decoded
    ]);
    return false;
}
?>