<?php
// config.php

// ---------------------------------------------------------------------
// 1. URL ROOT
// ---------------------------------------------------------------------
if (!defined('URLROOT')) {
    define('URLROOT', 'http://beauty-spa.local/');   // change for production
}

// ---------------------------------------------------------------------
// 2. DATABASE
// ---------------------------------------------------------------------
date_default_timezone_set('UTC');

$mysqli = new mysqli('localhost', 'root', 'root', 'beauty-spa');
if ($mysqli->connect_errno) {
    error_log("MySQL connection failed: {$mysqli->connect_error}");
    die("Database error");
}
$mysqli->set_charset('utf8mb4');

// ---------------------------------------------------------------------
// 3. mNotify SETTINGS
// ---------------------------------------------------------------------
$mnotify_api_key = '84MJ3KwogYrzWMriqYx6pIrVN';   

global $admin_phone;
$admin_phone = '233205737464';                

// ---------------------------------------------------------------------
// 4. SMS FUNCTION (3 arguments: $to, $text, $sender)
// ---------------------------------------------------------------------
function sendSMSMessage(string $to, string $text, string $sender = 'Sebson'): bool
{
    global $mnotify_api_key;

    if (empty($mnotify_api_key)) {
        error_log('mNotify API key missing');
        return false;
    }

    // ---- clean phone number ------------------------------------------------
    $to = preg_replace('/[^0-9]/', '', $to);
    if (substr($to, 0, 3) !== '233') {
        if (strlen($to) === 10 && $to[0] === '0') {
            $to = '233' . substr($to, 1);
        } else {
            error_log("Invalid phone format: $to");
            return false;
        }
    }

    // ---- build request ----------------------------------------------------
    $url = 'https://api.mnotify.com/api/sms/quick?key=' . urlencode($mnotify_api_key);

    $payload = [
        'recipient'     => [$to],
        'sender'        => $sender,
        'message'       => $text,
        'is_schedule'   => false,
        'schedule_date' => ''
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_CAINFO         => __DIR__ . '/cacert.pem'   // <-- must exist
    ]);

    $raw      = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr || $httpCode !== 200) {
        error_log("SMS failed to {$to}: HTTP {$httpCode}, cURL: {$curlErr}, Resp: {$raw}");
        return false;
    }

    $decoded = json_decode($raw, true);
    $ok = isset($decoded['status']) && $decoded['status'] === 'success';

    if ($ok) {
        error_log("SMS sent to {$to} (msg_id: " . ($decoded['summary']['message_id'] ?? '-') . ")");
    } else {
        error_log("SMS API error to {$to}: " . json_encode($decoded));
    }
    return $ok;
}

// ---------------------------------------------------------------------
// 5. SESSION (if you need it elsewhere)
// ---------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>