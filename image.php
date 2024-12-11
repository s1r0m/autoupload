<?php

$accessToken = base64_decode('SUdRV1JQUzFsdVpBM0pvU1hkRE16VlJRa1Z6UlhGNGNVVTFPV0pvZVdjNGN6RmhOVVUwUW1SZkxXdFVUbDlNYTBoUVZYaFVSalZRVTI1UGNHZHVVRkZJT0dKMllXbFBhVlpBdlJIQTRRemx4TFVKQlZ6Rm9XWGxrYVZjd2FXRlNiMHBCVkZBMFFrZzJXRk5xWXkxeGFVTkRhelYyYVV4eVFVRVpE');

$firebase_url = 'https://flamegarun-default-rtdb.firebaseio.com/inshorts.json';

// Function to send HTTP requests
function sendRequest($url2, $method2, $dataa2 = null) {
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, $url2);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

    if ($method2 === 'PUT' || $method2 === 'PATCH') {
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, $method2);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($dataa2));
    }

    if ($method2 === 'POST') {
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($dataa2));
    }

    if ($method2 === 'DELETE') {
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "DELETE");
    }

    curl_setopt($ch2, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);

    $responser2 = curl_exec($ch2);
    if (curl_errno($ch2)) {
        echo 'Error:' . curl_error($ch2);
    }
    curl_close($ch2);
    return $responser2;
}

// Save a string to Firebase
function saveString($firebase_url, $stringer2) {
    $dataa2 = ['value' => $stringer2];
    $responser2 = sendRequest($firebase_url, 'PUT', $dataa2); // Using PUT to overwrite existing data
    echo "Save Response: " . $responser2 . "\n";
}

// Read the string from Firebase
function readString($firebase_url) {
    $responser2 = sendRequest($firebase_url, 'GET');
    echo "Read Response: " . $responser2 . "\n";
}



$updnews = readString($firebase_url);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://inshorts.com/api/undefined/en/news?category=all_news&max_limit=15&include_card_data=true');
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

$tottitle = "";
$media_ids = [];
$counter = 0;

if(!empty($data->data->news_list)) {

foreach($data->data->news_list as $newsItemtest) {
        if($newsItemtest->news_type == "NEWS") {
            $title = $newsItem->news_obj->title;
if(str_contains($updnews, $title)) {
}
else
{
$counter = $counter+1;
}
            }
}

if($counter > 9)
{
echo "We are Ready To Go... \n";
}
else
{
die("Not Enough News");
}


    foreach($data->data->news_list as $newsItem) {
        if($newsItem->news_type == "NEWS") {
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

$tottitle = $tottitle.$title." \n ";
if (str_contains($updnews, $title)) {
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
    
    

$txt = $content."\n\n".$tags;
$media = 'https://hosting-db4b.onrender.com/ok'.$radm.'.jpeg';

// Access Token

$ch = curl_init();
// Set cURL options for media container creation
curl_setopt($ch, CURLOPT_URL, "https://graph.instagram.com/me/media");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'is_carousel_item' => true,
    'image_url' => $media,
    'caption' => $txt,
    'access_token' => $accessToken
]));

// Execute the request
$response = curl_exec($ch);
curl_close($ch);

// Decode the response
$responseData = json_decode($response, true);

if(isset($responseData['id'])) {
    $containerId = $responseData['id'];
    echo "Media container created with ID: $containerId\n";
    $media_ids[] = $containerId;
} else {
    die("Error creating media container: " . $response);
}

if(count($media_ids) === 10) {
        break; 
    }
    
} else {
    echo "Failed to execute script. Exit code: $return_var\n";
}
            
}
        }
        
    }
    
    if(count($media_ids) === 10) {
    $ch = curl_init();
// Set cURL options for media container creation
curl_setopt($ch, CURLOPT_URL, "https://graph.instagram.com/me/media");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'media_type' => 'CAROUSEL',
    'children' => implode(',', $media_ids),
    'access_token' => $accessToken
]));

// Execute the request
$response = curl_exec($ch);
curl_close($ch);

// Decode the response
$responseData = json_decode($response, true);

if (isset($responseData['id'])) {
    $containerId = $responseData['id'];
    echo "Media Crousel 10 container created with ID: $containerId\n";
} else {
    die("Error creating media container: " . $response);
}
    
    
    // Step 3: Publish the Media
$ch = curl_init();

// Set cURL options for media publishing
curl_setopt($ch, CURLOPT_URL, "https://graph.instagram.com/me/media_publish");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'creation_id' => $containerId,
    'access_token' => $accessToken
]);

// Execute the request
$response = curl_exec($ch);
curl_close($ch);

// Decode the response
$responseData = json_decode($response, true);

if(isset($responseData['id'])) {
    $mediaId = $responseData['id'];
    echo "Media published successfully with ID: $mediaId\n";
    
    saveString($firebase_url, $tottitle);
    
} else {
    die("Error publishing media: " . $response);
}
    }
    
} else {
    echo "No news data available.";
}
?>
