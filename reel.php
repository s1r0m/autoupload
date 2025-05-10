<?php
function deleteFileFromFirebase($fileName) {
    $bucketUrl = "https://firebasestorage.googleapis.com/v0/b/flamegarun.appspot.com/o";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "{$bucketUrl}/{$fileName}");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        echo "File deleted successfully.\n";
    } else {
        echo "Failed to delete file.\n";
        echo $response;
    }
}
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
// API endpoint and headers
$url = 'https://news-search.newsinshorts.com/en/v3/news_tag_search?type=VIDEO_NEWS_CATEGORY';
$headers = [
    'X-DEVICE-ID: ' . uniqid(), // Generate a random device ID
    'X-APP-VERSION: 849',
    'Content-Type: application/json'
];

// Request payload
$data = [
    "read_card_ids" => [],
    "read_deck_ids" => [],
    "latest_read_ids" => [],
    "read_hash_ids" => [],
    "read_other_ids" => [],
    "read_video_opinion_ids" => []
];

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    die('cURL error: ' . curl_error($ch));
}

// Close cURL
curl_close($ch);

// Decode the JSON response
$responseData = json_decode($response, true);

// Initialize an array to store the extracted video news
$videoNews = [];

// Check if suggested_news exists in the response
if (isset($responseData['suggested_news']) && is_array($responseData['suggested_news'])) {
    // Loop through each news item (0 to 29)
    foreach ($responseData['suggested_news'] as $newsItem) {
        // Check if the type is VIDEO_NEWS
        if (isset($newsItem['type']) && $newsItem['type'] === 'VIDEO_NEWS') {
            // Extract the required fields
            $hashId = $newsItem['hash_id'] ?? null;
            
            // Check if news_obj exists and has the required fields
            if (isset($newsItem['news_obj'])) {
                $content = $newsItem['news_obj']['content'] ?? null;
                $videoUrl = $newsItem['news_obj']['video_url'] ?? null;
                
                // Add to our collection if all required fields are present
                if ($hashId && $content && $videoUrl) {
                    $videoNews[] = [
                        'hash_id' => $hashId,
                        'content' => $content,
                        'video_url' => $videoUrl
                    ];
                    
                    $firstr = str_replace('"','',file_get_contents("https://flamegarun-default-rtdb.firebaseio.com/hash.json"));
                    if(str_contains($firstr,$hashId))
                    {
                    
                    }
                    else
                    {
                    unlink("output.mp4");
$m3u8Url = $videoUrl;

// Output .mp4 file path
$outputFile = "output.mp4"; 

// FFmpeg command to convert .m3u8 to .mp4
$ffmpegCommand = "ffmpeg -i " . escapeshellarg($m3u8Url) . " -c copy -bsf:a aac_adtstoasc " . escapeshellarg($outputFile) . " 2>&1";

// Execute the command
exec($ffmpegCommand, $output, $returnCode);

// Check if conversion was successful
if ($returnCode === 0) {
    echo "✅ Conversion successful! MP4 saved at: " . $outputFile;
    
 
    $fileName = $outputFile;
    $filePath = $outputFile;
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
        
        
         $accessToken = str_replace('"','',file_get_contents("https://flamegarun-default-rtdb.firebaseio.com/insta.json"));

        // Create Instagram media container
        $mediaContainerUrl = "https://graph.instagram.com/me/media";
        $mediaData = [
            'media_type' => 'REELS',
            'video_url' => "https://firebasestorage.googleapis.com/v0/b/flamegarun.appspot.com/o/" . $fileName . "?alt=media&token=" . json_decode($response)->downloadTokens,
            'caption' => $content,
            'access_token' => $accessToken,
        ];

        $mediaResponse = sendRequest($mediaContainerUrl, 'POST', $mediaData);
        $mediaContainerId = json_decode($mediaResponse, true)['id'] ?? null;

        if (!$mediaContainerId) {
            die("Error creating media container: $mediaResponse");
        }
        
        echo "\n\n".$mediaContainerId."\n\n";
        echo $mediaResponse;
        
        sleep(13);
        
        $publishUrl = 'https://graph.instagram.com/me/media_publish';
        
        $publishData = [
    'creation_id' => $mediaContainerId,
    'access_token' => $accessToken,
];
$publishResponse = sendRequest($publishUrl, 'POST', $publishData);
$publishResult = json_decode($publishResponse, true);

if (!isset($publishResult['id'])) {
    die("Error publishing carousel: $publishResponse");
}

echo $publishResponse;

$responsey = sendRequest("https://flamegarun-default-rtdb.firebaseio.com/hash.json", 'PUT', $firstr." ".$hashId);

// Output the response
echo "Update response: " . $responsey;

        deleteFileFromFirebase($fileName);
        
        die("Work Done");
        
    } else {
        echo "File upload failed.\n";
        echo $response;
    }

    
    
    
} else {
    echo "❌ Conversion failed. Error: " . implode("\n", $output);
}

                    }
                }
            }
        }
    }
}

// Output the results
echo "Found " . count($videoNews) . " VIDEO_NEWS items:\n";
print_r($videoNews);

// You can also save to a file or process further as needed
// file_put_contents('video_news.json', json_encode($videoNews, JSON_PRETTY_PRINT));
?>