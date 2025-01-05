<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('53692494341-nomiqu7c6gtfbu7dqu6hcv5nrgjh9ae1.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('https://blogger-jegv.onrender.com/blog.php');
$client->addScope('https://www.googleapis.com/auth/blogger');

// Firebase URLs
$firebaseNewsUrl = 'https://flamegarun-default-rtdb.firebaseio.com/blog.json';

// Inshorts API URL
$inshortsApiUrl = 'https://inshorts.com/api/undefined/en/news?category=all_news&max_limit=1&include_card_data=true';

function sendGetRequest($url) {
    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set timeout (optional)

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL error: " . $error);
    }

    // Close cURL session
    curl_close($ch);

    return $response;
}

function summ($sumtxt)
{
$url = 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/signupNewUser?key=AIzaSyAtZlkvxb8DJBIyMHNIjAG_m3se8uKz-Wk';
$data = json_encode(['clientType' => 'CLIENT_TYPE_ANDROID']);

$options = [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data,
];
$curl = curl_init();
curl_setopt_array($curl, $options);
$response = curl_exec($curl);
if (curl_errno($curl)) {
    echo 'Error: ' . curl_error($curl);
} else {
    $id = json_decode($response)->idToken;
$url = 'https://summarize-ai-3b4dcd9.zuplo.app/summarize';
$authorization = 'Bearer '.$id;
$data = json_encode(['text' => $sumtxt]);
$options = [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: ' . $authorization,
    ],
    CURLOPT_POSTFIELDS => $data,
];

$curl = curl_init();
curl_setopt_array($curl, $options);

$response = curl_exec($curl);

if (curl_errno($curl)) {
    echo 'Error: ' . curl_error($curl);
} else {
$start = '"delta":{"content":"';
$end = '"},"logprobs":';
$string = $response;
    $pattern = sprintf('/%s(.*?)%s/', preg_quote($start, '/'), preg_quote($end, '/'));
    
    // Perform a global regular expression match
    preg_match_all($pattern, $string, $matches);
    $tottxt = "";
    // Print each match directly
    foreach ($matches[1] as $match) {
        $tottxt = $tottxt.$match.""; // Print each word on a new line
    }
    return stripcslashes($tottxt);

}
curl_close($curl);
}
curl_close($curl);
}

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

if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken(file_get_contents("https://flamegarun-default-rtdb.firebaseio.com/reftoken.json"));
    sendRequest("https://flamegarun-default-rtdb.firebaseio.com/token.json", 'PUT', ['value' => $client->getAccessToken()]);
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
$sourceurl = $newsItem['news_obj']['source_url'];
$title = $newsItem['news_obj']['title'];
$content = "<p>".$newsItem['news_obj']['content']."</p>";
$imageUrl = $newsItem['news_obj']['image_url'];
$tags = $newsItem['news_obj']['relevancy_tags'];

        $titleMd5 = md5($title);
        if(str_contains(json_encode($processedNewsData), $titleMd5)) {
            die("already Published");
        }

function createBloggerPost($accessToken, $blogId, $title, $imageUrl, $content, $labels = []) {
    // API endpoint for creating a new post
    $url = "https://www.googleapis.com/blogger/v3/blogs/{$blogId}/posts/";

    // Construct the post body with HTML content
    $postBody = [
        'kind' => 'blogger#post',
        'title' => $title,
        'content' => "
            <div style='text-align: center;'>
                <img src='{$imageUrl}' alt='thumbnail' />
            </div>
            <div>
            <br>
                {$content}
            </div>
        ",
        'labels' => $labels
    ];

    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$accessToken}",
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postBody));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);
    echo $response;

    // Check for errors
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
        curl_close($ch);
        return null;
    }

    // Close cURL session
    curl_close($ch);

    // Decode and return the response
    return json_decode($response, true);
}

// Example usage
$accessToken = file_get_contents("https://flamegarun-default-rtdb.firebaseio.com/token/value.json");

$blogId = '852776495822446883';

$labels = $tags;

$contant = "";
try {
    $contento = sendGetRequest($sourceurl);
    $contant = str_replace('*','',summ($contento));
  if(str_contains($contant,"html"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"HTML"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"Html"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"à"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"á"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"â"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"ä"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"ā"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"å"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"ã"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"æ"))
  {
  $contant = str_replace('*','',summ($content));
  }
  
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
die("No Content");
}

if(strlen($contant) < 10)
{
die("ni content");
}

$response = createBloggerPost($accessToken, $blogId, $title, $imageUrl, $contant, $labels);


if ($response) {
    echo 'Post published with ID: ' . $response['id'];
} else {
    echo 'An error occurred while publishing the post.';
}


sendRequest($firebaseNewsUrl, 'PUT', ['value' => $titleMd5]);

        echo "\n\nSaved In Database";
        
        }
        }
        else
        {
        die("nothing from inshorts found");
        }
       
       ?>