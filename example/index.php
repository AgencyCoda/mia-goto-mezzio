<?php

require '../vendor/autoload.php';

use Mia\GoToLog\GoToHelper;

$clientId = '';
$clientSecret = '';
$redirectUrl = 'http://shared-angular.s3-website.us-east-2.amazonaws.com';

$service = new GoToHelper($clientId, $clientSecret);
//echo $service->getAuthorizeUrl($redirectUrl);
//exit();

$code = '';
//$response = $service->generateAccessToken($code, $redirectUrl);
//var_dump($response);
//exit();

$accessToken = '';
$refreshToken = '';
$organizerKey = '5129425568427922375';
$accountKey = '6041954248784335813';

//var_dump($service->refreshAccessToken($refreshToken));
//exit();

$service->setAccessToken($accessToken);
var_dump($service->getAllWebinars($organizerKey));

// account_key, email, firstName, lastName, organizer_key
