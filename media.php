<?php

// Firebase URLs
$firebaseNewsUrl = 'https://flamegarun-default-rtdb.firebaseio.com/inshorts.json';
$firebaseMd5Url = 'https://flamegarun-default-rtdb.firebaseio.com/inmd5.json';

// Inshorts API URL
$inshortsApiUrl = 'https://inshorts.com/api/undefined/en/news?category=all_news&max_limit=3&include_card_data=true';

// Access Token for Instagram
$accessToken = base64_decode('SUdRV1JQUzFsdVpBM0pvU1hkRE16VlJRa1Z6UlhGNGNVVTFPV0pvZVdjNGN6RmhOVVUwUW1SZkxXdFVUbDlNYTBoUVZYaFVSalZRVTI1UGNHZHVVRkZJT0dKMllXbFBhVlpBdlJIQTRRemx4TFVKQlZ6Rm9XWGxrYVZjd2FXRlNiMHBCVkZBMFFrZzJXRk5xWXkxeGFVTkRhelYyYVV4eVFVRVpE');

// Utility function to send HTTP requests
function sendRequest($url, $method, $data = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if ($method === 'PUT' || $method === 'PATCH') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    }

    curl_close($ch);
    return $response;
}

// Fetch data from Inshorts API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$inshortsApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Cookie: _ga=' . rand() . '; _tenant=ENGLISH; _ga_L7P7D50590=' . rand()
]);
$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    $inshortsData = json_decode($response, true);
}
curl_close($ch);


// Fetch already processed news data from Firebase
$processedNewsData = json_decode(file_get_contents($firebaseNewsUrl), true) ?? [];

// Fetch MD5 hash data from Firebase
$md5Data = json_decode(file_get_contents($firebaseMd5Url), true) ?? [];

// Check and process news items
if (!empty($inshortsData['data']['news_list'])) {
    foreach ($inshortsData['data']['news_list'] as $newsItem) {
        if ($newsItem['news_type'] !== 'NEWS') {
            continue; // Skip non-news items
        }

        // Extract news details
        $sourceName = $newsItem['news_obj']['source_name'];
        $title = $newsItem['news_obj']['title'];
        $content = $newsItem['news_obj']['content'];
        $imageUrl = $newsItem['news_obj']['image_url'];
        $tags = implode(' ', array_map(fn($tag) => "#$tag", $newsItem['news_obj']['relevancy_tags']));

        $titleMd5 = md5($title);

        // Skip if news is already processed (exists in /inshorts.json)
        if (str_contains(json_encode($processedNewsData), $titleMd5)) {
            continue;
        }

        // Check if MD5 hash key exists in /inmd5.json
        if (isset($md5Data[$titleMd5]) && !empty($md5Data[$titleMd5])) {
            continue;
        }

        // Create HTML template for news
        $htmlTemplate = "
            <div style='position: relative; width: 100%; height: 100%; background: url(\"$imageUrl\") no-repeat center center; background-size: cover; font-family: Arial, sans-serif;'>
    <!-- Title Section -->
    <div style='position: absolute; top: 100px; left: 50px; right: 50px; text-align: center;'>
        <h1 style='font-size: 40px; font-weight: bold; color: black; background: linear-gradient(90deg, yellow, transparent); padding: 10px; margin: 0; display: inline-block; font-family: Georgia, serif;'>
            $title
        </h1>
    </div>

    <!-- Content Section -->
    <div style='position: absolute; bottom: 50px; left: 50px; right: 50px; text-align: center;'>
        <p style='font-size: 40px; color: white; font-style: italic; background: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px;'>
            $content
        </p>
    </div>

    <!-- Source Section -->
    <div style='position: absolute; bottom: 30px; right: 30px; text-align: right;'>
        <span style='font-size: 18px; color: yellow; background: red; padding: 5px 10px; border-radius: 5px;'>
            $sourceName
        </span>
    </div>
</div>";

        // Save HTML template to a file
        file_put_contents("ok.html", $htmlTemplate);

        // Generate image using ss.js
        $outputFile = "ok$titleMd5.jpeg";
        $command = "node ss.js $outputFile";
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            die("Error generating image: " . implode("\n", $output));
        }

        // Upload image to external server
        $uploadUrl = "https://hosting-atm2.onrender.com/upload.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new CURLFile($outputFile)]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $uploadResponse = curl_exec($ch);
        curl_close($ch);

        echo $uploadResponse;

        // Create Instagram media container
        $mediaContainerUrl = "https://graph.instagram.com/me/media";
        $mediaData = [
            'is_carousel_item' => true,
            'image_url' => "https://hosting-atm2.onrender.com/$outputFile",
            'access_token' => $accessToken,
        ];

        $mediaResponse = sendRequest($mediaContainerUrl, 'POST', $mediaData);
        $mediaContainerId = json_decode($mediaResponse, true)['id'] ?? null;

        if (!$mediaContainerId) {
            die("Error creating media container: $mediaResponse");
        }

        // Save container ID and caption to Firebase
        $caption = "$content\n$tags";
        $updateData = [
            $titleMd5 => $mediaContainerId,
            $mediaContainerId => $caption,
        ];

        sendRequest($firebaseMd5Url, 'PATCH', $updateData);

        // Stop execution to comply with 30-second cron job limit
        die("Processed news: $title");
    }
} else {
    die("No news to process.");
}

?>
