<?php
// grok-chat.php
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$message = $input['message'] ?? '';

$api_key = 'xai-yPxWa35hO5hDUL3kqRTlwQcyOSzlB7GQq88TcP5UipSuhCSLMJD32RGjFu1yk9AgG6UyueCYtdfC4R1w';

$ch = curl_init('https://api.x.ai/v1/chat/completions');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'Authorization: Bearer ' . $api_key,
]);

$data = [
  'model' => 'grok-3-latest',
  'messages' => [
    ['role' => 'system', 'content' => 'شما یک مشاور سایت هستید که با حوصله پاسخ کاربران را می‌دهید.'],
    ['role' => 'user', 'content' => $message],
  ],
  'temperature' => 0.5,
  'stream' => false,
];

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$reply = $result['choices'][0]['message']['content'] ?? 'متاسفم، مشکلی پیش آمده.';

// بررسی پاسخ خام در صورت بروز خطا
if (!isset($result['choices'][0]['message']['content'])) {
  echo json_encode(['reply' => '❌ خطا در دریافت پاسخ: ' . $response]);
  exit;
}

echo json_encode(['reply' => $reply]);
