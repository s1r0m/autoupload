<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);
function format_number($ono) {
$n = intval($ono);
    $abbrev = array('K', 'M', 'B', 'T');
    for ($i = count($abbrev) - 1; $i >= 0; $i--) {
        $size = pow(10, ($i + 1) * 3);
        if ($size <= abs($n)) {
            $result = $n / $size;
            $result = str_replace('.00', '', sprintf("%.2f", $result)) ." ". $abbrev[$i];
            break;
        }
    }
    return $result ?? $n;
}
function gid($string) {
    if (strpos($string, 'youtu.be') !== false) {
        $start_pos = strpos($string, 'youtu.be/') + strlen('youtu.be/');
        $video_id = substr($string, $start_pos, 11);
    } else {
        parse_str(parse_url($string, PHP_URL_QUERY), $query_params);
        if (isset($query_params['v'])) {
            $video_id = $query_params['v'];
        } else {
            $video_id = '';
        }
    }
    if(strpos($string, 'short') !== false)
    {
$video_id = substr(strstr($string, 'shorts/'), 7, 11);
    }
    if(strpos($string, 'live') !== false)
    {
$video_id = substr(strstr($string, 'live/'), 5, 11);
    }
    if($video_id == '')
    {
    $text = $string;
    $txxt = "hello ".$text." hii";
$pattern = '/(?<=\W)[a-zA-Z0-9_-]{11}(?=\W)/';
preg_match($pattern, $txxt, $matches);
$video_id = $matches[0];
     }
    return $video_id;
}
date_default_timezone_set('Asia/Kolkata');
$now = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
$hour = $now->format('G'); 
if($hour >= 3 && $hour < 12) { $timeOfDay = 'Good Morning 🌄'; } elseif($hour >= 12 && $hour < 17) { $timeOfDay = 'Good Afternoon 🌅'; } elseif($hour >= 17 && $hour < 21) { $timeOfDay = 'Good Evening 🌇'; } else { $timeOfDay = 'Good Night 🌃'; }
function find_address($needle, $haystack, $path = '') {
    foreach($haystack as $key => $value) {
        $current_path = $path . '/' . $key;
        
        if(is_array($value)) {
            $result = find_address($needle, $value, $current_path);
            
            if($result) {
                return $result;
            }
        } else {
            if($value === $needle) {
                return $current_path;
            }
        }
    }
    
    return false;
}
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

$ucid = "UCnUmNzJPs4J1ECu6lHARv7A";
$url = "https://www.googleapis.com/youtube/v3/commentThreads?key=AIzaSyABTU0VwF9e0xrBqsZ8f7vbA6Ty5pK_FTQ&textFormat=plainText&part=snippet&allThreadsRelatedToChannelId=UCnUmNzJPs4J1ECu6lHARv7A&maxResults=10000";
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$headers = array(
   "Content-Type: application/json; charset=utf-8",
   "currentChannelId: UCnUmNzJPs4J1ECu6lHARv7A",
   "currentChannelToken: 9dc69861-d1e9-4d53-8bbf-2766d277b17b",
   "currentUserId: eccab0f5-48ae-47a0-af9a-e63e8d4612d0",
   "currentUserToken: 29062289-f292-43a7-8e17-6466b123b4df",
   "User-Agent: Dalvik/2.1.0 (Linux; U; Android 13; SM-T245 Build/TP2A.220624.014)",
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$resp2 = curl_exec($curl);
//echo $resp2;
curl_close($curl);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.tubebuddy.com/mobileapi/comments/GetRecentComments_v2?howMany=50&sinceDate=2024-11-11');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
$headers = array();
$headers[] = 'Currentchannelid: UCnUmNzJPs4J1ECu6lHARv7A';
$headers[] = 'Currentchanneltoken: 9dc69861-d1e9-4d53-8bbf-2766d277b17b';
$headers[] = 'Currentuserid: eccab0f5-48ae-47a0-af9a-e63e8d4612d0';
$headers[] = 'Currentusertoken: 29062289-f292-43a7-8e17-6466b123b4df';
$headers[] = 'User-Agent: Dalvik/2.1.0 (Linux; U; Android 14; V2253 Build/UP1A.231005.007)';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
$response2 = json_decode($response);

$data56 = json_decode($resp2,true);
//var_dump($data56);
foreach($data56['items'] as $comment) {
//Print_r($comment);
$cccc = $comment['snippet']['totalReplyCount'];
$ccd = intval($cccc);
$commentId = $comment['id'];
$x = 0;
for ($x = 0; $x <= 99; $x++) {
  //echo $response2->Data[$x]->Replies[0]->AuthorChannelName;
  if($response2->Data[$x]->CommentId == $commentId)
  {
  break;
  }
}
    if($ccd == 0 && $response2->Data[$x]->Replies[0]->AuthorChannelName == "") {
    echo "<br><br>times<br>";
        $authorChannelName = $comment['snippet']['topLevelComment']['snippet']['authorDisplayName'];
        $chnid = $comment['snippet']['topLevelComment']['snippet']['authorChannelId']['value'];
        $pub = $comment['snippet']['topLevelComment']['snippet']['publishedAt'];
        $commentId = $comment['id'];
        $commentText = $comment['snippet']['topLevelComment']['snippet']['textOriginal'];
$html = preg_replace('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>/i', '<a>', $commentText);
$text = strip_tags($html);

$txxt = "hello ".$text." hii";
$pattern = '/(?<=\W)[a-zA-Z0-9_-]{11}(?=\W)/';
preg_match($pattern, $txxt, $matches);
$video_id = $matches[0];

if(substr($text, 0, 1) === "@") {
/*if(strpos($text," ") != false) {
$fw = strtok($text, " ");
    $msg = "Hey $authorChannelName !\nYou Requested a Text Art for $fw\nHere's it is : https://TheAloneTiS.blogspot.com/2023/04/ai-text-art-maker.html?q=$fw\n";
} else {
    $msg = "Hey $authorChannelName !\nYou Requested a Text Art for $text\nHere's it is : https://TheAloneTiS.blogspot.com/2023/04/ai-text-art-maker.html?q=$text\n";
}*/
} 

else if(strpos($text, "youtu") !== false or $video_id != '')
{
$vid = gid($text);
$vidd = file_get_contents('https://returnyoutubedislikeapi.com/votes?videoId='.$vid);
$json_data = json_decode($vidd,true);
$id = $json_data['id'];
$dateCreated = $json_data['dateCreated'];
$likes = $json_data['likes'];
$dislikes = $json_data['dislikes'];
$rating = $json_data['rating'];
$viewCount = $json_data['viewCount'];
$deleted = $json_data['deleted'];
$ov = format_number($viewCount);
$ol = format_number($likes);
$od = format_number($dislikes);
$rate = intval($rating);
$msg = "𝑽𝒊𝒅𝒆𝒐 𝑷𝒖𝒃𝒍𝒊𝒔𝒉𝒆𝒅 ➙ $dateCreated 🕒\nVɪᴇᴡꜱ: $viewCount ➟ ($ov) 👀\nLɪᴋᴇꜱ: $likes ➟ ($ol) 👍\nDɪꜱʟɪᴋᴇꜱ: $dislikes ➟ ($od) 👎\nRᴀᴛɪɴɢ: $rate/5 ✯\n𝑯𝒐𝒑𝒆 𝒚𝒐𝒖 𝑳𝒊𝒌𝒆𝒅 𝒖𝒔 & 𝑫𝒐𝒏'𝒕 𝑭𝒐𝒓𝒈𝒆𝒕 𝒕𝒐 𝑺𝒉𝒂𝒓𝒆 & 𝑺𝒖𝒃𝒔𝒄𝒓𝒊𝒃𝒆 𝑿𝒑𝒍𝒐𝒓𝒆 𝑨𝑰 🧡\n\n𝑪𝒐𝒎𝒎𝒆𝒏𝒕 𝑷𝒖𝒃𝒍𝒊𝒔𝒉𝒆𝒅 ➙ $pub";
}

else {
    $msg = "$timeOfDay $authorChannelName !\nTʜᴀɴᴋYᴏᴜ ꜰᴏʀ ʏᴏᴜʀ Cᴏᴍᴍᴇɴᴛ 🤗\n𝑯𝒐𝒑𝒆 𝒚𝒐𝒖 𝑳𝒊𝒌𝒆𝒅 𝒖𝒔 & 𝑫𝒐𝒏'𝒕 𝑭𝒐𝒓𝒈𝒆𝒕 𝒕𝒐 𝑺𝒉𝒂𝒓𝒆 & 𝑺𝒖𝒃𝒔𝒄𝒓𝒊𝒃𝒆 𝑿𝒑𝒍𝒐𝒓𝒆 𝑨𝑰 🧡\n\n𝑪𝒐𝒎𝒎𝒆𝒏𝒕 𝑷𝒖𝒃𝒍𝒊𝒔𝒉𝒆𝒅 ➙ $pub";
}

$url = "https://www.tubebuddy.com/mobileapi/comments/post_v3";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "Content-Type: application/json; charset=utf-8",
   "currentChannelId: UCnUmNzJPs4J1ECu6lHARv7A",
   "currentChannelToken: 9dc69861-d1e9-4d53-8bbf-2766d277b17b",
   "currentUserId: eccab0f5-48ae-47a0-af9a-e63e8d4612d0",
   "currentUserToken: 29062289-f292-43a7-8e17-6466b123b4df",
   "User-Agent: Dalvik/2.1.0 (Linux; U; Android 13; SM-T245 Build/TP2A.220624.014)",
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$data = '{"AuthorChannelId":null,"AuthorChannelName":null,"AuthorChannelUrl":null,"AuthorProfileImageUrl":null,"AuthorSubscriberCount":0,"CanRate":false,"CanReply":false,"ChannelId":null,"CommentDate":"0001-01-01T00:00:00","CommentId":null,"CommentText":"'.$msg.'","Hidden":false,"Id":null,"IsLiked":false,"LikeCount":0,"ModerationStatus":null,"ParentThreadId":"'.$commentId.'","Replies":[],"ShowItem":false,"Spam":false,"IsPatreonSupporter":false,"TotalReplyCount":0,"VideoId":null}';

curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

//for debug only!
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$resp = curl_exec($curl);
$response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

// Close the cURL handle
curl_close($curl);

// Output the response code
echo "Response Code: " . $response_code;

//var_dump($resp);
    }
}
?>