<?php

// Firebase URLs
$firebaseNewsUrl = 'https://flamegarun-default-rtdb.firebaseio.com/threads.json';

// Inshorts API URL
$inshortsApiUrl = 'https://inshorts.com/api/undefined/en/news?category=all_news&max_limit=1&include_card_data=true';

// Access Token for Instagram
$accessToken = base64_decode('VEhBQVNpMkI0N2VsRkJZbGRSVjA1bk5VODFURTB0V25WWWRrbFVhMnRqZG5KTUxWUXhiWEJPYTJGb1lWVkVSUzB3TlZGUWFUTnJlV2gyVDFOaWRXOUNiMFpBR2VpMTVOM0UwZUVoRlpBVkV4UnpKSVVHUmlZMnRST0daQVVWR016TTA1MGRrcHRZMnhvTVdNd2JVbFpBUmxsbFRWcGllRFpBalZFeHFOMk5PTmxWRVIxcFJVMHRVU21aQWFRWEl5T0ZKWVNXc1pE');


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
//echo $response;
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    $inshortsData = json_decode($response, true);
}
curl_close($ch);


// Fetch already processed news data from Firebase
$processedNewsData = json_decode(file_get_contents($firebaseNewsUrl), true) ?? [];

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
        $tags = implode(' \n', array_map(fn($tag) => "#$tag", $newsItem['news_obj']['relevancy_tags']));

        $titleMd5 = md5($title);
        if(str_contains(json_encode($processedNewsData), $titleMd5)) {
            die("already Published");
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

    <!-- Source Section -->
    <div style='position: absolute; bottom: 30px; right: 30px; text-align: right;'>
        <span style='font-size: 18px; color: yellow; background: red; padding: 5px 10px; border-radius: 5px;'>
            $sourceName
        </span>
    </div>
</div>";

        // Save HTML template to a file
file_put_contents("threads.html", $htmlTemplate);

$outputFile = "thrd$titleMd5.jpeg";
      $command = "node threads.js $outputFile";
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            die("Error generating image: " . implode("\n", $output));
        }

        // Upload image to external server
        $uploadUrl = "https://hosting-db4b.onrender.com/upload.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new CURLFile($outputFile)]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $uploadResponse = curl_exec($ch);
        curl_close($ch);

        echo $uploadResponse."\n";
        

        
        $url = "https://graph.threads.net/me/threads";
$url .= "?media_type=IMAGE";
$url .= "&image_url=" . urlencode("https://hosting-db4b.onrender.com/".$outputFile);
$url .= "&text=" . urlencode($content."\n\n".$tags);
$url .= "&access_token=".$accessToken;

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true); // HTTP POST method
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as string

// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    // Display the response
        $mediaContainerId = json_decode($response, true)['id'] ?? null;
}

// Close cURL session
curl_close($ch);
       

        if (!$mediaContainerId) {
            die("Error creating media container: $mediaResponse");
        }
        else
        {
        echo "Media Container Created\n";
        }
        

        $publishUrl = 'https://graph.threads.net/me/threads_publish';
        
        $publishData = [
    'creation_id' => $mediaContainerId,
    'access_token' => $accessToken
];
$publishResponse = sendRequest($publishUrl, 'POST', $publishData);
$publishResult = json_decode($publishResponse, true);

if (!isset($publishResult['id'])) {
    die("Error publishing carousel: $publishResponse");
}

echo "Carousel published successfully with ID: " . $publishResult['id'] . "\n";


sendRequest($firebaseNewsUrl, 'PUT', ['value' => $titleMd5]);

        echo "\n\nSaved In Database";
        
        }
        }
        else
        {
        die("nothing from inshorts found");
        }
       
       ?>