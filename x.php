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


function sendBufferRequest($url, $headers, $body) {
    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

    // Execute cURL request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check response code
    if ($httpCode === 200) {
        echo "Success: " . $response;
    } else {
        echo "Not successful. HTTP Code: " . $httpCode . "\nResponse: " . $response;
    }
}

function fetchDataFromFirebase($firebaseUrl) {
    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check for successful response
    if ($httpCode === 200) {
        return json_decode($response, true); // Return associative array
    } else {
        die("Failed to fetch data from Firebase. HTTP Code: $httpCode");
    }
}

// Firebase URL
$firebaseUrl = "https://flamegarun-default-rtdb.firebaseio.com/x.json";

// Fetch data
$data = fetchDataFromFirebase($firebaseUrl);

// Check if the number of keys is at least 4
echo count($data);
if (count($data) < 2) {


function listFilesFromFirebase() {
    $bucketUrl = "https://firebasestorage.googleapis.com/v0/b/flamegarun.appspot.com/o";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $bucketUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        $files = json_decode($response, true);
        foreach ($files['items'] as $file) {
        $name = str_replace(".","-",$file['name']);
        $conx = file_get_contents("https://flamegarun-default-rtdb.firebaseio.com/x/$name.json");
if($conx == "null" or $conx == null)
{
echo $name."\n";
deleteFileFromFirebase(str_replace("-",".",$name));
}
        }
    } else {
        echo "Failed to list files.\n";
        echo $response;
    }
}

// Example Usage
listFilesFromFirebase();


    die("Data has fewer than 2 keys.");
}

// Get 4 keys and extract their `url` and `title`
$keys = array_slice(array_keys($data), 0, 2);
$selectedData = [];
foreach ($keys as $key) {
    $selectedData[] = [
        "key" => $key,
        "title" => $data[$key]['title'],
        "url" => $data[$key]['url']
    ];
}

$img1 = 1;
$img2 = 2;
$img3 = 3;
$img4 = 4;
$tottit = "";

// Print the selected data
foreach ($selectedData as $item) {
   // echo "Title: " . $item['title'] . "\n";
   // echo "URL: " . $item['url'] . "\n\n";
    $tottit = $tottit."✴️ ".$item['title'] . "\n\n";
    
    if($img1 == 1)
    {
    $img1 = $item['url'];
    $img2 = 2;
    }
    else
    {
    $img2 = $item['url'];
    $img3 = 3;
    }
}


// URL
$url = "https://api.bufferapp.com/1/updates/create.json?access_token=2%2F49a62c2bd99d0bc693076797497772e6ba68a2e2995a778163e0b0985a9fb74f51dbd9cefb9e2049386e3efb8a68dc10cbd5ce459f32e4189588999b4b39bc0d&is_draft=false";

// Headers
$headers = [
    "Accept: application/json",
    "x-buffer-client-id: mobileapp-android-8.12.6",
    "Content-Type: application/x-www-form-urlencoded",
    "User-Agent: okhttp/4.12.0"
];

// Body
$body = 'client_id=4e9680b8512f7e6b22000000&client_secret=16d821b11ca1f54c0047581c7e3ca25f&created_source=queue&text='.rawurlencode("Breaking News 📰🚨 \n\n".$tottit).'&fb_text='.rawurlencode($tottit).'&now=1&top=0&media%5Bpicture%5D='.rawurlencode($img1).'&media%5Bthumbnail%5D='.rawurlencode($img1).'&extra_media%5B0%5D%5Bphoto%5D='.rawurlencode($img2).'&extra_media%5B0%5D%5Bthumbnail%5D='.rawurlencode($img2).'&extra_media%5B0%5D%5Buploaded%5D=true&extra_media%5B0%5D%5Bprogress%5D=100&retweet=&profile_ids%5B0%5D=677551054697c1deff366e9e';

// Send the request
sendBufferRequest($url, $headers, $body);

foreach ($selectedData as $item) {
$del = $item['key'];
 echo sendRequest("https://flamegarun-default-rtdb.firebaseio.com/x/".$del.".json", 'DELETE');
 deleteFileFromFirebase(str_replace("-",".",$del));
}

?>