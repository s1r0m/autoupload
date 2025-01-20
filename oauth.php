<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
//thealonetis
$client->setClientId('53692494341-nomiqu7c6gtfbu7dqu6hcv5nrgjh9ae1.apps.googleusercontent.com');
$client->setClientSecret('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');

//cloudair
//$client->setClientId('860987439099-53dt8ovoufc7qo9pfo7qn0ntl328hg8f.apps.googleusercontent.com');
//$client->setClientSecret('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');
$client->setRedirectUri('https://autoupload-5rq8.onrender.com/oauth.php');
$client->addScope('https://www.googleapis.com/auth/blogger');
$client->setAccessType('offline'); // Request offline access
$client->setPrompt('consent');    // Ensure refresh token is generated

// Step 1: Redirect to Google for authorization
if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' . $authUrl);
}

// Step 2: Handle the callback
$client->authenticate($_GET['code']);
$token = $client->getAccessToken(); // This returns an array

// Ensure we correctly handle the access token
print_r($token);
if (is_array($token) && isset($token['access_token'])) {
    echo 'Access Token: ' . $token['access_token'];
} else {
    echo 'Error retrieving access token.';
}
?>