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
        $content = $newsItem['news_obj']['content']."";
        $imageUrl = $newsItem['news_obj']['image_url'];
        $tags = implode(' ', array_map(fn($tag) => "#$tag", $newsItem['news_obj']['relevancy_tags']));

        $titleMd5 = md5($title);
        if(str_contains(json_encode($processedNewsData), $titleMd5)) {
        $uploadsrv = str_replace('"','',file_get_contents("https://flamegarun-default-rtdb.firebaseio.com/hostsrv.json"));
        exec("php remthread.php");
   echo file_get_contents($uploadsrv."/remthread.php", false, stream_context_create(["http" => ["header" => "User-Agent: googlebot"]]));
            die("already Published");
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
file_put_contents("threads.html", $htmlTemplate);

$outputFile = "thrd$titleMd5.jpeg";
      $command = "node threads.js $outputFile";
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

        echo $uploadResponse."\n";
        
 $accessToken = str_replace('"','',file_get_contents("https://flamegarun-default-rtdb.firebaseio.com/thread.json"));
        
        $url = "https://graph.threads.net/me/threads";
$url .= "?media_type=IMAGE";
$url .= "&image_url=" . urlencode($uploadsrv."/".$outputFile);
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
            die("Error creating media container: $response");
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