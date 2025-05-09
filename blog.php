<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
//thealonetis
$client->setClientId('53692494341-nomiqu7c6gtfbu7dqu6hcv5nrgjh9ae1.apps.googleusercontent.com');
$client->setClientSecret('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');

//cloudair
//$client->setClientId('860987439099-53dt8ovoufc7qo9pfo7qn0ntl328hg8f.apps.googleusercontent.com');
//$client->setClientSecret('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');
//$client->setRedirectUri('https://blogger-jegv.onrender.com/blog.php');
$client->addScope('https://www.googleapis.com/auth/blogger');

// Firebase URLs
$firebaseNewsUrl = 'https://flamegarun-default-rtdb.firebaseio.com/blog.json';

function generateUniqueValue() {
    $intPart = random_int(1000, 9999);                 // 4-digit random integer
    $decimalPart = number_format(mt_rand() / mt_getrandmax(), 8); // random decimal (8 digits)
    $stringPart = bin2hex(random_bytes(5));            // 10-char random hex string
    $timestamp = microtime(true);                      // precise current timestamp

    return $intPart . '.' . $decimalPart . '.' . $stringPart . '.' . $timestamp;
}

// Inshorts API URL
$inshortsApiUrl = 'https://inshorts.com/api/undefined/en/news?category=all_news&max_limit='.generateUniqueValue().'&include_card_data=true';

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


function generateRandomNumber($length) {
    $digits = '0123456789';
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= $digits[rand(0, strlen($digits) - 1)];
    }
    return $result;
}

function generateRandomHex($length) {
    $chars = '0123456789abcdef';
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $result;
}

function generateRandomCookieString() {
    // Randomize _ga value
    $ga_version = "GA1.1";
    $ga_random1 = generateRandomNumber(10);
    $ga_random2 = generateRandomNumber(10);
    $ga_value = "{$ga_version}.{$ga_random1}.{$ga_random2}";
    
    // Randomize _ga_L7P7D50590 value
    $gs_version = "GS2.1";
    $prefix = "s" . generateRandomNumber(10);
    $o = "o" . rand(1, 9);
    $g = "g" . rand(1, 9);
    $t = "t" . generateRandomNumber(10);
    $j = "j" . rand(1, 9);
    $l = "l" . rand(0, 9);
    $h = "h" . rand(0, 9);
    $gs_value = "{$gs_version}.{$prefix}\${$o}\${$g}\${$t}\${$j}\${$l}\${$h}";
    
    return "_ga={$ga_value}; _ga_L7P7D50590={$gs_value}; _tenant=ENGLISH";
}

function generateRandomUserAgent() {
    // Randomize browser versions
    $chromeVersion = rand(100, 130) . '.0.' . rand(1000, 9999) . '.' . rand(10, 99);
    $safariVersion = rand(605, 610) . '.' . rand(1, 15) . '.' . rand(1, 15);
    $firefoxVersion = rand(100, 120) . '.0';

    // Randomize OS versions
    $windowsVersion = '10.0';
    $macOSVersion = '10_' . rand(11, 16) . '_' . rand(1, 7);
    $androidVersion = rand(8, 14);
    $iOSVersion = '16_' . rand(0, 5);

    // Random device models (for mobile)
    $androidDevices = ['SM-A505FN', 'SM-G975F', 'Pixel 6', 'Redmi Note 10'];
    $iOSDevices = ['iPhone14,3', 'iPhone15,2', 'iPad13,1'];

    // Randomize build numbers (e.g., "Build/XYZ123")
    $buildNumber = 'Build/' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);

    // Randomize browser choice
    $browser = rand(0, 2);
    switch ($browser) {
        case 0: // Chrome (Windows/Mac/Linux)
            $osChoice = rand(0, 2);
            if ($osChoice === 0) {
                return "Mozilla/5.0 (Windows NT $windowsVersion; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/$chromeVersion Safari/537.36";
            } elseif ($osChoice === 1) {
                return "Mozilla/5.0 (Macintosh; Intel Mac OS X $macOSVersion) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/$chromeVersion Safari/537.36";
            } else {
                return "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/$chromeVersion Safari/537.36";
            }
            break;
        case 1: // Safari (Mac/iOS)
            $osChoice = rand(0, 1);
            if ($osChoice === 0) {
                return "Mozilla/5.0 (Macintosh; Intel Mac OS X $macOSVersion) AppleWebKit/$safariVersion (KHTML, like Gecko) Version/" . rand(15, 17) . ".0 Safari/$safariVersion";
            } else {
                $device = $iOSDevices[array_rand($iOSDevices)];
                return "Mozilla/5.0 ($device; CPU iPhone OS $iOSVersion like Mac OS X) AppleWebKit/$safariVersion (KHTML, like Gecko) Version/" . rand(15, 17) . ".0 Mobile/15E148 Safari/$safariVersion";
            }
            break;
        case 2: // Firefox (Windows/Mac/Linux)
            $osChoice = rand(0, 2);
            if ($osChoice === 0) {
                return "Mozilla/5.0 (Windows NT $windowsVersion; Win64; x64; rv:$firefoxVersion) Gecko/20100101 Firefox/$firefoxVersion";
            } elseif ($osChoice === 1) {
                return "Mozilla/5.0 (Macintosh; Intel Mac OS X $macOSVersion; rv:$firefoxVersion) Gecko/20100101 Firefox/$firefoxVersion";
            } else {
                return "Mozilla/5.0 (X11; Linux x86_64; rv:$firefoxVersion) Gecko/20100101 Firefox/$firefoxVersion";
            }
            break;
    }
}

// Fetch data from Inshorts API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$inshortsApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, generateRandomUserAgent());
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'User-Agent: '.generateRandomUserAgent(),
    'Cookie: '.generateRandomCookieString()
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

Print_r($client->isAccessTokenExpired());
if ($client->isAccessTokenExpired()) {
$reftok = str_replace('"','',sendGetRequest("https://flamegarun-default-rtdb.firebaseio.com/token/value/refresh_token.json"));
Echo $reftok;
    $client->fetchAccessTokenWithRefreshToken($reftok);
   echo sendRequest("https://flamegarun-default-rtdb.firebaseio.com/token.json", 'PUT', ['value' => $client->getAccessToken()]);
}

$accessToken = sendGetRequest("https://flamegarun-default-rtdb.firebaseio.com/token/value/access_token.json");

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
  if(str_contains($contant,"и"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"ó"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"ú"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"site"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"Access Denied"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,'"Access Denied"'))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,"403"))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,'"403"'))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,'javascript'))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,'JavaScript'))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,'Javascript'))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,'Website'))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,'website'))
  {
  $contant = str_replace('*','',summ($content));
  }
  if(str_contains($contant,'WebSite'))
  {
  $contant = str_replace('*','',summ($content));
  }
  
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
die("No Content");
}

if(strlen($contant) < 10)
{
$contant = str_replace('*','',summ($content));
}
if(strlen($contant) < 10)
{
$contant = $content;
}

$response = createBloggerPost($accessToken, $blogId, $title, $imageUrl, $contant, $labels);


if ($response) {
    echo 'Post published with ID: ' . $response['id'];
} else {
    echo 'An error occurred while publishing the post.';
}


sendRequest($firebaseNewsUrl, 'PUT', ['value' => $titleMd5]);

        echo "\n\nSaved In Database";
        die("work done");
        }
        }
        else
        {
        die("nothing from inshorts found");
        }
       
       ?>