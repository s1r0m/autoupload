<?php
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://inshorts.com/api/undefined/en/news?category=all_news&max_limit=10&include_card_data=true');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, ''); // Enables compressed response handling
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Cookie: _ga=' . rand() . '; _tenant=ENGLISH; _ga_L7P7D50590=' . rand()
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    $data = json_decode($response);
}

curl_close($ch);
$totcaption = "";
if (!empty($data->data->news_list)) {
    foreach ($data->data->news_list as $newsItem) {
        if ($newsItem->news_type == "NEWS") {
            // Extract details
            $sourceName = $newsItem->news_obj->source_name;
            $title = $newsItem->news_obj->title;
            $imageUrl = str_replace("?", "", $newsItem->news_obj->image_url);
            $content = $newsItem->news_obj->content;
            $tags = '';
foreach($newsItem->news_obj->relevancy_tags as $tag)
{
$tags = $tags." #".$tag;
}

            // Generate Instagram post code
            $html = "
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

$data2 = json_decode(file_get_contents('https://graph.instagram.com/me/media?fields=caption&access_token=IGQWRPWGdVZAlAyVXl6Q0F4RmhDTzA0R1E5T25yN2dDR0VlX1ZAwb284ZAXRHU3RrcTNhYTdmQ1g5N1hVc3JmSFdlZAm1sREp6ZAzE3VnpMR0xyZAnFvTUhKZAGlWdjFiUE9ycThYY1hiQ1J6Rk5GMS1MRllUOWlYQjk2dHMZD'), true);

$now = 0;

foreach ($data2['data'] as $media2) {
    $caption2 = $media2['caption'];
    $totcaption = $totcaption.$caption2."\n";
}
if (str_contains($totcaption, $content)) {
echo "This News is Already Uploaded! \n";
}
else
{
$file = 'ok.html';
file_put_contents($file, $html);
$radm = rand();
$script = 'ss.js ok'.$radm.'.jpeg';
$output = null;
$return_var = null;
exec("node $script", $output, $return_var);

if ($return_var === 0) {
    echo "Script executed successfully:\n";
    echo implode("\n", $output);
    
    
// The URL of the upload receiver script
$url = "https://hosting-db4b.onrender.com/upload.php";

// The file path to send
$filePath = 'ok'.$radm.'.jpeg';

// Check if the file exists
if (!file_exists($filePath)) {
    die("File not found: " . $filePath);
}

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);

// Attach the file using the `CURLFile` class
$file = new CURLFile($filePath);
$postData = ['file' => $file];

// Set the POST fields
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo "cURL error: " . curl_error($ch);
} else {
    echo "Response: " . $response;
}

// Close cURL
curl_close($ch);
    
    
    $apiUrl = "https://api.bufferapp.com/1/updates/create.json";

$requestUrl = $apiUrl."?profile_id=6749d20c9be8e4e2b746a499&access_token=2%2F49a62c2bd99d0bc693076797497772e6ba68a2e2995a778163e0b0985a9fb74f51dbd9cefb9e2049386e3efb8a68dc10cbd5ce459f32e4189588999b4b39bc0d&is_draft=false";

// JSON data payload

$txt = rawurlencode($content."\n\n".$tags);
$media = rawurlencode('https://hosting-db4b.onrender.com/ok'.$radm.'.jpeg');
$data = 'client_id=4e9680b8512f7e6b22000000&client_secret=16d821b11ca1f54c0047581c7e3ca25f&created_source=queue&text='.$txt.'&now=1&top=0&media%5Bpicture%5D='.$media.'&media%5Bthumbnail%5D='.$media.'&retweet=&profile_ids%5B0%5D=6749d20c9be8e4e2b746a499&channel_data%5Binstagram%5D%5Bpost_type%5D=post&channel_data%5Binstagram%5D%5Bscheduling_type%5D=direct';

$headers = [
    "Accept: application/json",
    "x-buffer-client-id: mobileapp-android-8.12.6",
    "Content-Type: application/x-www-form-urlencoded",
    "User-Agent: okhttp/4.12.0"
];

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $requestUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

// Execute cURL request
$response = curl_exec($ch);

// Check for errors
if ($response === false) {
    echo "cURL Error: " . curl_error($ch);
} else {
    // Parse and display response
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "HTTP Code: $httpCode\n";
    echo "Response: $response\n";
}

// Close cURL session
curl_close($ch);
    
} else {
    echo "Failed to execute script. Exit code: $return_var\n";
}
            
}
        }
    }
} else {
    echo "No news data available.";
}
?>
