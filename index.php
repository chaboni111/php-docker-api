<?php
// CORS Headers to allow browser-style access
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-HTTP-Method-Override, Accept, Origin, User-Agent");

// Handle preflight (OPTIONS) request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

// Supported voices list
$supportedVoices = ['alloy', 'echo', 'fable', 'onyx', 'nova', 'shimmer'];

// Check if both parameters exist
if (!isset($_GET['input']) || !isset($_GET['voice'])) {
    header("Content-Type: application/json");
    echo json_encode([
        "status" => "error",
        "message" => "Missing required parameters.",
        "required_parameters" => [
            "input" => "Text you want to convert to audio.",
            "voice" => "One of: " . implode(', ', $supportedVoices)
        ],
        "supported_voices" => $supportedVoices,
        "api_owner" => "Md. Hridoy Sheikh",
        "powered_by" => "School of Mind Light",
        "support" => [
            "telegram_channel" => "https://t.me/schoolofmindlight2018"
        ]
    ]);
    exit;
}

$inputText = $_GET['input'];
$voice = $_GET['voice'];

// Validate voice
if (!in_array($voice, $supportedVoices)) {
    header("Content-Type: application/json");
    echo json_encode([
        "status" => "error",
        "message" => "Invalid voice selected.",
        "supported_voices" => $supportedVoices,
        "hint" => "Use ?voice= followed by one of the supported voices."
    ]);
    exit;
}

// Set audio response header
header("Content-Type: audio/mpeg");

// Prepare POST data
$postData = json_encode([
    'input' => $inputText,
    'voice' => $voice,
    'response_format' => 'mp3'
]);

// cURL request to TTS API
$ch = curl_init("https://ttsapi.site/v1/audio/speech");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

// Browser-style headers to mimic a real browser
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: */*',
    'Accept-Encoding: gzip, deflate, br',
    'Accept-Language: en-US,en;q=0.9',
    'Origin: https://www.google.com',
    'Referer: https://www.google.com/',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Return audio if successful
if ($httpCode === 200) {
    echo $response;
} else {
    header("Content-Type: application/json");
    http_response_code($httpCode);
    echo json_encode([
        "status" => "error",
        "message" => "Failed to fetch audio from TTS API.",
        "http_code" => $httpCode,
        "api_owner" => "Md. Hridoy Sheikh",
        "powered_by" => "School of Mind Light",
        "support_channel" => "https://t.me/schoolofmindlight2018"
    ]);
}