<?php
define("SECRET_KEY", "0011001100112345"); // 16-byte key
define("HIDE_STRING", "@satpathpandey");
date_default_timezone_set('Asia/Kolkata');

$datetime = new DateTime();

// Get the timestamp in milliseconds
$timestampMillis = $datetime->getTimestamp() * 1000;

$apptime = intval(extractAndDecrypt($_GET["q"]));
$apptime = $apptime+10000;
//Print_r($apptime." ".$timestampMillis);

if($apptime > $timestampMillis)
{

}
else
{
die("Next time You are going to be Blacklisted");
}

function encryptAndHide($data) {
    // AES Encryption
    $encryptedBytes = openssl_encrypt($data, "AES-128-ECB", SECRET_KEY, OPENSSL_RAW_DATA);

    // Convert encrypted bytes to binary string
    $binaryString = '';
    foreach (str_split($encryptedBytes) as $char) {
        $binaryString .= sprintf("%08b", ord($char));
    }

    // Convert binary string to zero-width characters
    $zeroWidthString = "\u{200E}"; // Start marker
    foreach (str_split($binaryString) as $bit) {
        if ($bit === '0') {
            $zeroWidthString .= "\u{200B}"; // Zero-width space
        } else {
            $zeroWidthString .= "\u{200C}"; // Zero-width non-joiner
        }
    }
    $zeroWidthString .= "\u{200F}"; // End marker

    // Embed zero-width string into the hiding string
    return HIDE_STRING . $zeroWidthString;
}

function extractAndDecrypt($hiddenString) {
    // Extract zero-width characters sequence
    $start = mb_strpos($hiddenString, "\u{200E}");
    $end = mb_strpos($hiddenString, "\u{200F}");
    if ($start === false || $end === false || $start >= $end) {
        throw new Exception("No hidden message found");
    }
    $zeroWidthSequence = mb_substr($hiddenString, $start + 1, $end - $start - 1);

    // Convert zero-width characters back to binary string
    $binaryString = '';
    foreach (mb_str_split($zeroWidthSequence) as $char) {
        if ($char === "\u{200B}") {
            $binaryString .= '0';
        } elseif ($char === "\u{200C}") {
            $binaryString .= '1';
        }
    }

    // Convert binary string to byte array
    $encryptedBytes = '';
    foreach (str_split($binaryString, 8) as $byte) {
        $encryptedBytes .= chr(bindec($byte));
    }

    // AES Decryption
    return openssl_decrypt($encryptedBytes, "AES-128-ECB", SECRET_KEY, OPENSSL_RAW_DATA);
}

// Example usage
try {
$rawData = json_decode(file_get_contents('php://input'));
    $originalString = $rawData->data;
    //echo "Original String: " . $originalString . PHP_EOL;

    //$hiddenEncryptedString = encryptAndHide($originalString);
    //echo "Hidden Encrypted String: " . $hiddenEncryptedString . PHP_EOL;

    $decryptedString = extractAndDecrypt($originalString);

Echo encryptAndHide($decryptedString);

    //echo "Decrypted String: " . $decryptedString . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>