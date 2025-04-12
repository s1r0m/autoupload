<?php

$pk = str_replace('"',"",file_get_contents("https://flamegarun-default-rtdb.firebaseio.com/comment2/pk.json"));
$acctok = str_replace('"',"",file_get_contents("https://flamegarun-default-rtdb.firebaseio.com/insta.json"));

function reply($comid,$reply,$acctoks)
{
$api_url = "https://graph.instagram.com/$comid/replies?message=".urlencode($reply)."&access_token=".$acctoks;

// Initialize cURL session
$ch = curl_init();
// Set cURL options
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $response_data = json_decode($response, true);
    
    if ($http_code == 200) {
        echo "Reply posted successfully!";
        echo "\nResponse: " . print_r($response_data, true);
    } else {
        echo "Error posting reply. HTTP Status Code: {$http_code}";
        echo "\nError details: " . print_r($response_data, true);
    }
}

// Close cURL session
curl_close($ch);
}

function poster()
{
$rd = file_get_contents("pk");
$firebaseUrl = "https://flamegarun-default-rtdb.firebaseio.com/comment2.json";

$data = [
    'pk' => $rd
];

// Initialize cURL
$ch = curl_init();

// Set cURL options for PUT request
curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Use PUT instead of POST
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    echo 'Data successfully sent to Firebase with your custom key: ' . $response;
}

// Close cURL
curl_close($ch);
}

function extractBetweenStrings($input, $startDelimiter, $endDelimiter) {
    $results = array();
    $startDelimiterLength = strlen($startDelimiter);
    $endDelimiterLength = strlen($endDelimiter);
    $startFrom = 0;
    
    while (false !== ($startPos = strpos($input, $startDelimiter, $startFrom))) {
        $startPos += $startDelimiterLength;
        $endPos = strpos($input, $endDelimiter, $startPos);
        
        if (false === $endPos) {
            break;
        }
        
        $length = $endPos - $startPos;
        $results[] = substr($input, $startPos, $length);
        $startFrom = $endPos + $endDelimiterLength;
    }
    
    return $results;
}

function aireply($name,$ques)
{
$url = 'https://www.blackbox.ai/api/chat';
$data = [
    'messages' => [
        [
            'role' => 'user',
            'content' => 'UserName: '.$name.', Question: '.$ques.', Note: No new lines or special characters or symbols like * # @ , \' etc should not be present in the answer and also answer in only one paragraph and as small as it can',
            'id' => ''
        ]
    ],
    'agentMode' => new stdClass(),
    'id' => '',
    'previewToken' => null,
    'userId' => null,
    'codeModelMode' => true,
    'trendingAgentMode' => new stdClass(),
    'isMicMode' => false,
    'userSystemPrompt' => null,
    'maxTokens' => 1024,
    'playgroundTopP' => null,
    'playgroundTemperature' => null,
    'isChromeExt' => false,
    'githubToken' => '',
    'clickedAnswer2' => false,
    'clickedAnswer3' => false,
    'clickedForceWebSearch' => false,
    'visitFromDelta' => false,
    'isMemoryEnabled' => false,
    'mobileClient' => false,
    'userSelectedModel' => null,
    'validated' => '00f37b34-a166-4efb-bce5-1312d87f2f94',
    'imageGenerationMode' => false,
    'webSearchModePrompt' => false,
    'deepSearchMode' => false,
    'domains' => null,
    'vscodeClient' => false,
    'codeInterpreterMode' => false,
    'customProfile' => [
        'name' => '',
        'occupation' => '',
        'traits' => [],
        'additionalInfo' => '',
        'enableNewChats' => false
    ],
    'session' => null,
    'isPremium' => false,
    'subscriptionCache' => null,
    'beastMode' => false,
    'reasoningMode' => false
];

$options = [
    'http' => [
        'header' => [
            'Host: www.blackbox.ai',
            'Origin: https://www.blackbox.ai',
            'Content-Type: application/json'
        ],
        'method' => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

if ($response === false) {
    die("Error making the request");
} else {
$noise = '$~~~$[{"title'.getStringBetween($response,'$~~~$[{"title','}]$~~~$').'}]$~~~$';
    $main = str_replace($noise,"",$response);
    $response = $main;
echo stripcslashes(str_replace('\n',"",str_replace("*","",$response)));
    return stripcslashes(str_replace('\n',"",str_replace("*","",$response)));
}
}
function getStringBetween($string, $start, $end) {
    $startPos = strpos($string, $start);
    if ($startPos === false) return ''; // start not found

    $startPos += strlen($start);
    $endPos = strpos($string, $end, $startPos);
    if ($endPos === false) return ''; // end not found

    return substr($string, $startPos, $endPos - $startPos);
}


$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => 'https://i.instagram.com/api/v1/media/3604163470543091824_71061062411/stream_comments/?sort_order=recent',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => [
    'User-Agent: Instagram 309.1.0.41.113 Android (34/14; 440dpi; 1080x2276; vivo; V2253; V2225; mt6833; en_US; 541635890)',
    'authorization: Bearer IGT:2:eyJkc191c2VyX2lkIjoiNzEwNjEwNjI0MTEiLCJzZXNzaW9uaWQiOiI3MTA2MTA2MjQxMSUzQXpMY3JNWU94a1I1bnpmJTNBMjMlM0FBWWVoZG9za3dEYktHVjU2U0ZRT05rN2t1bU5KNUJkSFB2T0ZQa21DdlEifQ==',
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  die('cURL Error #:' . $err);
} else {



$result = getStringBetween($response, "can_view_more_preview_comments", 'sort_order":"recent","should_render_upsell":false,"filter_options":[],"sort_options":[],"status":"ok"');
$result = '{"can_view_more_preview_comments'.$result.'sort_order":"recent","should_render_upsell":false,"filter_options":[],"sort_options":[],"status":"ok"}';
$response = str_replace($result,"",$response);
$result2 = getStringBetween($response, "can_view_more_preview_comments", 'sort_order":"recent","should_render_upsell":false,"filter_options":[],"sort_options":[],"status":"ok"');
$result2 = '{"can_view_more_preview_comments'.$result2.'sort_order":"recent","should_render_upsell":false,"filter_options":[],"sort_options":[],"status":"ok"}';
$response = str_replace($result2,"",$response);
$result3 = getStringBetween($response, "can_view_more_preview_comments", 'sort_order":"recent","should_render_upsell":false,"filter_options":[],"sort_options":[],"status":"ok"');
$result3 = '{"can_view_more_preview_comments'.$result3.'sort_order":"recent","should_render_upsell":false,"filter_options":[],"sort_options":[],"status":"ok"}';
$response = str_replace($result3,"",$response);
$count = 1;
foreach(json_decode($result)->comments as $comment)
{
if($comment->type == 0)
{
/*print_r($comment->pk);
echo "\n";
print_r($comment->text);
echo "\n";
print_r($comment->user->username);
echo "\n";
print_r($comment->user->full_name);
echo "\n";
print_r($comment->user->profile_pic_url);
echo "\n";
echo "\n";*/

echo $pk." ooooo ".$comment->pk;
if($pk == $comment->pk)
{
die("finished replying");
}
else
{
reply($comment->pk,aireply($comment->user->full_name,$comment->text),$acctok);
file_put_contents("pk",$comment->pk);
if($count == 1)
{
poster();
}
$count++;
}

}
}
//echo $result3;
if($result3 != '{"can_view_more_preview_commentssort_order":"recent","should_render_upsell":false,"filter_options":[],"sort_options":[],"status":"ok"}')
{
foreach(json_decode($result2)->comments as $comment)
{
if($comment->type == 0)
{
/*print_r($comment->pk);
echo "\n";
print_r($comment->text);
echo "\n";
print_r($comment->user->username);
echo "\n";
print_r($comment->user->full_name);
echo "\n";
print_r($comment->user->profile_pic_url);
echo "\n";
echo "\n";*/

if($pk == $comment->pk)
{
die("finished replying");
}
else
{
reply($comment->pk,aireply($comment->user->full_name,$comment->text),$acctok);
file_put_contents("pk",$comment->pk);
}

}
}
foreach(json_decode($result3)->comments as $comment)
{
if($comment->type == 0)
{
/*print_r($comment->pk);
echo "\n";
print_r($comment->text);
echo "\n";
print_r($comment->user->username);
echo "\n";
print_r($comment->user->full_name);
echo "\n";
print_r($comment->user->profile_pic_url);
echo "\n";
echo "\n";*/

if($pk == $comment->pk)
{
die("finished replying");
}
else
{
reply($comment->pk,aireply($comment->user->full_name,$comment->text),$acctok);
file_put_contents("pk",$comment->pk);
}

}
}
}


}