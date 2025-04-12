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
        $uploadsrv = str_replace('"','',file_get_contents("https://flamegarun-default-rtdb.firebaseio.com/hostsrv.json"));
        exec("php reminsta.php");
   echo file_get_contents($uploadsrv."/reminsta.php", false, stream_context_create(["http" => ["header" => "User-Agent: googlebot"]]));
            continue;
        }

        // Check if MD5 hash key exists in /inmd5.json
        if (isset($md5Data[$titleMd5]) && !empty($md5Data[$titleMd5])) {
        $uploadsrv = str_replace('"','',file_get_contents("https://flamegarun-default-rtdb.firebaseio.com/hostsrv.json"));
        exec("php reminsta.php");
  echo file_get_contents($uploadsrv."/reminsta.php", false, stream_context_create(["http" => ["header" => "User-Agent: googlebot"]]));
            continue;
        }

        // Create HTML template for news
                $htmlTemplate = "
<div style='position: relative; width: 100%; height: 100%; min-height: 600px; background: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.1)), url(\"$imageUrl\") no-repeat center center; background-size: cover; font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border-radius: 12px; overflow: hidden; border: 3px solid #FFD700;'>
    
    <!-- Gradient Overlay for Depth -->
    <div style='position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, rgba(0,0,0,0.05) 0%, rgba(0,0,0,0.3) 100%); z-index: 0;'></div>
    
    <!-- Title Section -->
    <div style='position: relative; padding: 60px 50px 0; z-index: 1;'>
        <h1 style='font-size: 50px; font-weight: 700; color: #fff; text-shadow: 2px 2px 12px rgba(0,0,0,0.7); margin: 0; line-height: 1.2; font-family: \"Playfair Display\", Georgia, serif; border-left: 6px solid #FFD700; padding-left: 20px;'>
            $title
        </h1>
        <div style='height: 4px; width: 120px; background: #FFD700; margin: 20px 0 0 25px;'></div>
    </div>

    <!-- Content Section -->
<div style='position: relative; padding: 0 50px; z-index: 1; margin-top: auto; margin-bottom: 20px;'>
    <p style='
        font-size: 36px;
        color: #fff;
        line-height: 1.5;
        font-style: italic;
        background: linear-gradient(90deg, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.5));
        padding: 35px;
        border-radius: 12px;
        backdrop-filter: blur(8px);
        border-left: 6px solid #FFD700;
        font-family: \"Helvetica Neue\", sans-serif;
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.7);
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
        transition: transform 0.3s ease-in-out;
    ' onmouseover='this.style.transform=\"scale(1.05)\";' onmouseout='this.style.transform=\"scale(1)\";'>
        $content
    </p>
</div>

    <!-- Source and Branding Section -->
    <div style='position: relative; display: flex; justify-content: space-between; align-items: flex-end; padding: 0 30px 30px; z-index: 1;'>
        <div style='font-size: 16px; color: rgba(255,255,255,0.7); font-style: italic;'>
            Trusted News Source
        </div>
        <div style='display: flex; align-items: center; gap: 15px;'>
        <span style='font-size: 18px; color: #fff; background: linear-gradient(135deg, #2c1a0a, #1a1005); padding: 8px 20px; border-radius: 30px; font-weight: bold; box-shadow: 0 4px 15px rgba(0,0,0,0.2); border: 1px solid #FFD700; text-transform: uppercase; letter-spacing: 1px;'>
                @Buzz.Indica
            </span>
            <span style='font-size: 18px; color: #fff; background: linear-gradient(90deg, #B8860B, #FFD700); padding: 8px 20px; border-radius: 30px; font-weight: bold; box-shadow: 0 4px 15px rgba(0,0,0,0.2);'>
                $sourceName
            </span>
            <div style='display: flex; gap: 12px; background: rgba(255,255,255,0.2); backdrop-filter: blur(8px); padding: 10px 18px; border-radius: 30px; border: 2px solid rgba(255,255,255,0.4); box-shadow: 0 0 12px rgba(255,255,255,0.3);'>

                <!-- Emojis with Enhanced Borders & Effects -->
                <span style=\"color: pink; font-size: 20px;\">
                    <img src=\"https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/1f497.svg\" 
                        alt=\"💗\" width=\"24\" height=\"24\" 
                        style=\"border-radius: 50%; object-fit: cover; border: 3px solid pink; box-shadow: 0 0 10px pink;\">
                </span>
                <span style=\"color: lime; font-size: 20px; font-family: &quot;Segoe UI Emoji&quot;, &quot;Apple Color Emoji&quot;, &quot;Noto Color Emoji&quot;, sans-serif;\">
                    <img src=\"https://cdn-icons-png.freepik.com/512/3545/3545766.png\" 
                        alt=\"➤\" width=\"24\" height=\"24\" 
                        style=\"border-radius: 50%; object-fit: cover; border: 3px solid lime; box-shadow: 0 0 10px lime;\">
                </span>
                <span style=\"color: yellow; font-size: 20px;\">
                    <img src=\"https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/1f6ce.svg\" 
                        alt=\"🛎️\" width=\"24\" height=\"24\" 
                        style=\"border-radius: 50%; object-fit: cover; border: 3px solid yellow; box-shadow: 0 0 10px yellow;\">
                </span>

            </div>
        </div>
    </div>

    <!-- Subtle Pattern Overlay -->
    <div style='position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: url(\"data:image/svg+xml,%3Csvg width=\'100\' height=\'100\' viewBox=\'0 0 100 100\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z\' fill=\'%23ffffff\' fill-opacity=\'0.05\' fill-rule=\'evenodd\'/%3E%3C/svg%3E\'); z-index: 0; pointer-events: none;'></div>

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

$uploadsrv = str_replace('"','',file_get_contents("https://flamegarun-default-rtdb.firebaseio.com/hostsrv.json"));

        // Upload image to external server
        $uploadUrl = $uploadsrv."/upload.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new CURLFile($outputFile)]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "googlebot");

        $uploadResponse = curl_exec($ch);
        curl_close($ch);

        echo $uploadResponse;
        
 $accessToken = str_replace('"','',file_get_contents("https://flamegarun-default-rtdb.firebaseio.com/insta.json"));

        // Create Instagram media container
        $mediaContainerUrl = "https://graph.instagram.com/me/media";
        $mediaData = [
            'is_carousel_item' => true,
            'image_url' => $uploadsrv."/".$outputFile,
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

       echo sendRequest($firebaseMd5Url, 'PATCH', $updateData);
        
        
        
        //Twitter 
        
        
function uploadFileToFirebase($filePath, $fileName, $tit) {
    $bucketUrl = "https://firebasestorage.googleapis.com/v0/b/flamegarun.appspot.com/o";
    $file = fopen($filePath, 'r');
    $fileData = fread($file, filesize($filePath));
    fclose($file);

    $boundary = uniqid();
    $headers = [
        "Content-Type: multipart/related; boundary={$boundary}",
    ];

    $body = "--{$boundary}\r\n";
    $body .= "Content-Type: application/octet-stream\r\n";
    $body .= "Content-Disposition: form-data; name=\"file\"; filename=\"{$fileName}\"\r\n\r\n";
    $body .= $fileData . "\r\n";
    $body .= "--{$boundary}--";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "{$bucketUrl}?uploadType=media&name={$fileName}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        echo "File uploaded successfully.\n";
        echo $response;
        
$updateData = [
    str_replace(".","-",$fileName) => [ 
        "url" => "https://firebasestorage.googleapis.com/v0/b/flamegarun.appspot.com/o/" . $fileName . "?alt=media&token=" . json_decode($response)->downloadTokens,
        "title" => $tit,
    ]
];

// Convert the array to JSON

// Send the request
echo sendRequest("https://flamegarun-default-rtdb.firebaseio.com/x.json", 'PATCH', $updateData);
        
    } else {
        echo "File upload failed.\n";
        echo $response;
    }
}

// Example Usage
$filePath = "ok$titleMd5.jpeg";
$fileName = "ok$titleMd5.jpeg";
uploadFileToFirebase($filePath, $fileName, $title." \n".$tags);
        
        
        

        // Stop execution to comply with 30-second cron job limit
        die("Processed news: $title");
    }
} else {
    die("No news to process.");
}

?>
