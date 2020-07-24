<?php
//base URL
$service_url = 'http://www.boredapi.com/api/activity/';


//Use CURL to fetch the respoonse
$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$curl_response = curl_exec($curl);
if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
    die('error occurred during curl exec. Additional info: ' . var_export($info));
}
curl_close($curl);

$decoded = json_decode($curl_response, 1);

/*
echo"<pre>";
print_r($decoded);
echo"</pre>";
*/

$activity = $decoded['activity'];
?>