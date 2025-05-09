<?php
function generateUniqueValue() {
    $intPart = random_int(1000, 9999);                 // 4-digit random integer
    $decimalPart = number_format(mt_rand() / mt_getrandmax(), 8); // random decimal (8 digits)
    $stringPart = bin2hex(random_bytes(5));            // 10-char random hex string
    $timestamp = microtime(true);                      // precise current timestamp

    return $intPart . '.' . $decimalPart . '.' . $stringPart . '.' . $timestamp;
}

// Firebase URLs
$firebaseNewsUrl = 'https://flamegarun-default-rtdb.firebaseio.com/threads.json';

// Inshorts API URL
$inshortsApiUrl = 'https://inshorts.com/api/undefined/en/news?category=all_news&max_limit='.generateUniqueValue().'&include_card_data=true';

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
        die("work done");
        }
        }
        else
        {
        die("nothing from inshorts found");
        }
       
       ?>