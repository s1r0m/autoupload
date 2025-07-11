<?php
function generateRandomUserAgent() {
    $androidVersions = ['10', '11', '12', '13'];
    $chromeVersions = ['138.0.0.0', '139.0.0.0', '140.0.0.0', '141.0.0.0'];
    
    return sprintf(
        'Mozilla/5.0 (Linux; Android %s; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/%s Mobile Safari/537.36',
        $androidVersions[array_rand($androidVersions)],
        $chromeVersions[array_rand($chromeVersions)]
    );
}

function generateRandomGreq() {
    return mt_rand(1000000000000, 9999999999999);
}

// Define multiple parameter sets to check
$parameterSets = [
    // Format: trainNumber, journeyDate, fromStnCode, toStnCode, classCode, quotaCode
    ['13152', '20250911', 'AYC', 'BSB', '3E', 'GN'],
    ['22346', '20250911', 'AYC', 'BSB', 'CC', 'GN'],
    ['22500', '20250913', 'BSB', 'GAYA', 'CC', 'GN'],
    // Add more sets as needed
];

// Check if URL parameters are provided (to override array values)
$useUrlParams = isset($_GET['use_url_params']) && $_GET['use_url_params'] === '1';
$urlParams = [];
if ($useUrlParams) {
    $urlParams = [
        'trainNumber' => $_GET['trainNumber'] ?? '',
        'journeyDate' => $_GET['journeyDate'] ?? '',
        'fromStnCode' => $_GET['fromStnCode'] ?? '',
        'toStnCode' => $_GET['toStnCode'] ?? '',
        'classCode' => $_GET['classCode'] ?? '',
        'quotaCode' => $_GET['quotaCode'] ?? ''
    ];
}


        $botToken = '7203214762:AAEHAB5_pGAb__MTeQwvlpYss1dtasO9tZE';

// Message to send

// Function to call Telegram API
function callTelegramAPI($method, $params = []) {
    global $botToken;
    $url = "https://api.telegram.org/bot{$botToken}/{$method}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// 1. Get all updates to find chats where bot is added
$updates = callTelegramAPI('getUpdates', [
    'offset' => 0,
    'limit' => 100,
    'timeout' => 30
]);

$groupChats = [];

// 2. Process updates to find group chats
if (isset($updates['result']) && is_array($updates['result'])) {
    foreach ($updates['result'] as $update) {
        // Check for message or my_chat_member updates
        if (isset($update['message']['chat'])) {
            $chat = $update['message']['chat'];
        } elseif (isset($update['my_chat_member']['chat'])) {
            $chat = $update['my_chat_member']['chat'];
        } else {
            continue;
        }

        // Only include groups and supergroups (negative IDs)
        if (isset($chat['id']) && $chat['id'] < 0) {
            $chatId = $chat['id'];
            $title = isset($chat['title']) ? $chat['title'] : 'Unknown Group';
            
            // Store unique chat IDs
            if (!isset($groupChats[$chatId])) {
                $groupChats[$chatId] = $title;
            }
        }
    }
}

if (empty($groupChats)) {
    die("No groups found where the bot is added.\n");
}

echo "Found " . count($groupChats) . " groups:\n";
foreach ($groupChats as $id => $title) {
    echo "- {$title} (ID: {$id})\n";
}

// Process either URL parameters or each parameter set
$processList = $useUrlParams ? [$urlParams] : $parameterSets;

foreach ($processList as $params) {
    // Extract parameters (either from URL or array)
    $trainNumber = $useUrlParams ? $params['trainNumber'] : $params[0];
    $journeyDate = $useUrlParams ? $params['journeyDate'] : $params[1];
    $fromStnCode = $useUrlParams ? $params['fromStnCode'] : $params[2];
    $toStnCode = $useUrlParams ? $params['toStnCode'] : $params[3];
    $classCode = $useUrlParams ? $params['classCode'] : $params[4];
    $quotaCode = $useUrlParams ? $params['quotaCode'] : $params[5];

    // Build URL with parameters
    $url = "https://www.irctc.co.in/eticketing/protected/mapps1/avlFarenquiry/$trainNumber/$journeyDate/$fromStnCode/$toStnCode/$classCode/$quotaCode/N";

    $data = [
        "paymentFlag" => "N",
        "concessionBooking" => false,
        "ftBooking" => false,
        "loyaltyRedemptionBooking" => false,
        "ticketType" => "E",
        "quotaCode" => $quotaCode,
        "moreThanOneDay" => true,
        "trainNumber" => $trainNumber,
        "fromStnCode" => $fromStnCode,
        "toStnCode" => $toStnCode,
        "isLogedinReq" => false,
        "journeyDate" => $journeyDate,
        "classCode" => $classCode
    ];

    $headers = [
        'Host: www.irctc.co.in',
        'Connection: keep-alive',
        'greq: ' . generateRandomGreq(),
        'Accept-Language: en-US,en;q=0.0',
        'User-Agent: ' . generateRandomUserAgent(),
        'Accept: application/json, text/plain, */*',
        'Content-Type: application/json; charset=UTF-8',
        'Referer: https://www.irctc.co.in/nget/booking/train-list'
    ];

    echo "\nProcessing: Train $trainNumber from $fromStnCode to $toStnCode on $journeyDate (Class: $classCode, Quota: $quotaCode)\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_HEADER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($response, $headerSize);

    if (curl_errno($ch)) {
        echo "Error: " . curl_error($ch) . "\n";
        curl_close($ch);
        continue;
    }

    curl_close($ch);

    if ($httpCode == 200) {
        $responseArray = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Error decoding JSON response\n";
            echo "Raw response: " . $body . "\n";
            continue;
        }
        

        
        if (isset($responseArray['errorMessage'])) {
        
        
        $updates = file_get_contents("https://api.telegram.org/bot{$botToken}/getUpdates?limit=100");
$updates = json_decode($updates, true);

$lastGroupMessage = null;

// Find the most recent message from our group
foreach ($updates['result'] as $update) {
    if (isset($update['message']['chat']['id'])) {
    foreach ($groupChats as $groupId => $title) {
        if ($update['message']['chat']['id'] == $groupId) {
            $lastGroupMessage = $update['message'];
        }
    }
    }
}

if ($lastGroupMessage) {
    if($lastGroupMessage['text'] == "status" || $lastGroupMessage['text'] == "Status")
    {
    $message = " 🚂 ❌ Booking not yet started: " . $responseArray['errorMessage'] . "\n For "."Train $trainNumber from $fromStnCode to $toStnCode on $journeyDate (Class: $classCode, Quota: $quotaCode)";
    foreach ($groupChats as $chatId => $title) {
    $result = callTelegramAPI('sendMessage', [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ]);

    if ($result['ok']) {
        echo "Message sent to: {$title} (ID: {$chatId})\n";
    } else {
        echo "Failed to send to {$title} (ID: {$chatId}): {$result['description']}\n";
    }
    
    // Small delay to avoid rate limiting
    sleep(1);
}

echo "Message sending process completed.\n";
    }
} else {
    echo "No recent messages found in this group.";
}
        
            echo " 🚂 ❌ Booking not yet started: " . $responseArray['errorMessage'] . "\n";
        } else {
            echo "Successful response:\n";
            // Extract the data
$trainName = $responseArray['trainName'] ?? 'N/A';
$enqClass = $responseArray['enqClass'] ?? 'N/A';
$totalFare = $responseArray['totalFare'] ?? 'N/A';
$from = $responseArray['from'] ?? 'N/A';
$to = $responseArray['to'] ?? 'N/A';

// Start building the printable data
$printableData = "  🚄✅  Booking Opened 📣🥁 \n\n    🚂✨ Train Details ✨🚂\n";
$printableData .= "════════════════════\n";
$printableData .= "✨ Name: $trainName\n";
$printableData .= "✨ Class: $enqClass\n";
$printableData .= "✨ Fare: ₹$totalFare\n";
$printableData .= "✨ Route: $from → $to\n\n";

// Add availability section
$printableData .= " Availability Status\n";
$printableData .= "════════════════════\n";

foreach ($responseArray['avlDayList'] ?? [] as $day) {
    $date = $day['availablityDate'] ?? 'N/A';
    $status = $day['availablityStatus'] ?? 'N/A';
    
    // Add emoji based on status
    $statusEmoji = match(strtoupper($status)) {
        'AVAILABLE' => '✅',
        'RAC' => '⚠️',
        'WAITING' => '⌛',
        'WL' => '⌛',
        default => '✅'
    };
    
    $printableData .= "🗓 $date: $statusEmoji $status\n";
}

// Add final separator
$printableData .= "════════════════════\n";

// Now you can use $printableData whenever needed
//echo $printableData;
            
            $message = $printableData;

// 3. Send message to all detected groups
foreach ($groupChats as $chatId => $title) {
    $result = callTelegramAPI('sendMessage', [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ]);

    if ($result['ok']) {
        echo "Message sent to: {$title} (ID: {$chatId})\n";
    } else {
        echo "Failed to send to {$title} (ID: {$chatId}): {$result['description']}\n";
    }
    
    // Small delay to avoid rate limiting
    sleep(1);
}

echo "Message sending process completed.\n";
            
            
            
            echo json_encode($responseArray, JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "HTTP Request failed with status code: " . $httpCode . "\n";
        echo "Response body: " . $body . "\n";
    }

    // Add delay between requests to avoid rate limiting
    sleep(1);
}

// Usage instructions
if (php_sapi_name() === 'cli') {
    echo "\nTo use URL parameters instead of predefined sets, call this script with:\n";
    echo "?use_url_params=1&trainNumber=XXXXX&journeyDate=YYYYMMDD&fromStnCode=XXX&toStnCode=XXX&classCode=XX&quotaCode=XX\n";
}
?>