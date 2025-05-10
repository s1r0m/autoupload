<?php
$firebaseUrl = 'https://flamegarun-default-rtdb.firebaseio.com/inmd5.json';
$inshortsUrl = 'https://flamegarun-default-rtdb.firebaseio.com/inshorts.json';
$accessToken = base64_decode('SUdRV1JQUzFsdVpBM0pvU1hkRE16VlJRa1Z6UlhGNGNVVTFPV0pvZVdjNGN6RmhOVVUwUW1SZkxXdFVUbDlNYTBoUVZYaFVSalZRVTI1UGNHZHVVRkZJT0dKMllXbFBhVlpBdlJIQTRRemx4TFVKQlZ6Rm9XWGxrYVZjd2FXRlNiMHBCVkZBMFFrZzJXRk5xWXkxeGFVTkRhelYyYVV4eVFVRVpE'); // Replace with your actual token
$carouselContainerUrl = 'https://graph.instagram.com/me/media';
$publishUrl = 'https://graph.instagram.com/me/media_publish';

// Function to send HTTP requests
function sendRequest($url, $method, $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    if ($method === 'POST' || $method === 'PATCH' || $method === 'PUT') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        if ($method === 'PATCH') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        }
        if ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        }
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);
    return $response;
}

// Step 1: Fetch data from Firebase /inmd5.json
$response = sendRequest($firebaseUrl, 'GET');
$inmd5Data = json_decode($response, true);

// Check if the total number of key-value pairs is more than 9
if (!is_array($inmd5Data) || count($inmd5Data) <= 9) {
exec("php reel.php",$reel);
Print_r($reel);
    die("Not enough keys to process. Found " . count($inmd5Data) . " key-value pairs.");
}

// Step 2: Extract MD5 hash keys (ignore numeric keys)
$md5Keys = array_filter(array_keys($inmd5Data), function ($key) {
    return !is_numeric($key);
});

// Select the first 5 MD5 hash keys
$selectedMd5Keys = array_slice($md5Keys, 0, 5);
$mediaIds = [];
$captionString = "";

// Step 3: Process each selected MD5 key
foreach ($selectedMd5Keys as $md5Key) {
    // Get the numeric media ID associated with the MD5 hash
    $mediaId = intval($inmd5Data[$md5Key]); // Ensure it's an integer
    $mediaIds[] = $mediaId;

    // Fetch the caption string from Firebase using the media ID as the key
    $mediaCaptionUrl = "https://flamegarun-default-rtdb.firebaseio.com/inmd5/$mediaId.json";
    $captionResponse = sendRequest($mediaCaptionUrl, 'GET');
    $caption = json_decode($captionResponse, true);

    // Append the caption to the combined caption string
    $captionString .= $caption . "\n\n";
}

// Ensure we have exactly 5 valid media IDs
if (count($mediaIds) !== 5) {
    die("Error: Less than 5 valid media IDs found. Found " . count($mediaIds));
}

 $accessToken = str_replace('"','',file_get_contents("https://flamegarun-default-rtdb.firebaseio.com/insta.json"));

// Step 4: Create a carousel container on Instagram
$carouselData = array_map('intval', $mediaIds); // Ensure media IDs are integers
$carouselContainerData = [
    'children' => $carouselData,
    'media_type' => 'CAROUSEL',
    'caption' => $captionString,
    'access_token' => $accessToken,
];

// Send request to create carousel container
$carouselResponse = sendRequest($carouselContainerUrl, 'POST', $carouselContainerData);
$carouselContainerId = json_decode($carouselResponse, true)['id'] ?? null;

if (!$carouselContainerId) {
    die("Error creating carousel container: $carouselResponse");
}

// Step 5: Publish the carousel container
$publishData = [
    'creation_id' => $carouselContainerId,
    'access_token' => $accessToken,
];
$publishResponse = sendRequest($publishUrl, 'POST', $publishData);
$publishResult = json_decode($publishResponse, true);

if (!isset($publishResult['id'])) {
    sendRequest("https://flamegarun-default-rtdb.firebaseio.com/inmd5.json", 'DELETE');
sleep(3);
    die("Error publishing carousel: $publishResponse");
}

echo "Carousel published successfully with ID: " . $publishResult['id'] . "\n";

// Step 6: Delete processed keys from Firebase /inmd5.json
foreach ($selectedMd5Keys as $md5Key) {
    // Delete MD5 hash key and its value
    $deleteMd5Url = "https://flamegarun-default-rtdb.firebaseio.com/inmd5/$md5Key.json";
    sendRequest($deleteMd5Url, 'DELETE');

    // Delete the associated media ID key and its value
    $mediaId = intval($inmd5Data[$md5Key]);
    $deleteMediaIdUrl = "https://flamegarun-default-rtdb.firebaseio.com/inmd5/$mediaId.json";
    sendRequest($deleteMediaIdUrl, 'DELETE');
}

// Step 7: Overwrite Firebase /inshorts.json with the processed MD5 keys
$newInshortsString = implode(" ", $selectedMd5Keys);
sendRequest($inshortsUrl, 'PUT', ['value' => $newInshortsString]);

echo "Processed MD5 keys: $newInshortsString\n";

?>