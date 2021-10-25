<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://interac.express-connect.com/api/payment-token/7b05b5f72307028a2cf73538ff1adcb0',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{"userId": 11446631,
"transactionId": "AB-09",
"name": "Giga Test",
"email": "chris@chris.com",
"mobile": "1234567890",
"acct": "1234567890",
"site": "https: //www.test.com",
"userIp": "70.67.168.155",
"currency": "CAD",
"language": "en",
"amount": 10.48,
"type": "ETI",
"hosted": "false",
"sandbox": true
}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic ZWVkNThkNzEtMzVhNS00MDcxLTg4NjAtMjFiOWZiYTI5ZjdiOmM5ODZjYTNiLTA0MzYtNDZlOC05ZjY4LTQ4NDRhOGJmZjM0OQ==',
    'Content-Type: application/json',
    'Cookie: fpcookie=fac4b74804ab645601bea543c553f312; connect.sid=s%3AYnpY_Y-s4KsICO3wYsSwJSTHzvaBIAOx.IEdOxItWAy8g7%2BV%2Ba8j7MhpW1%2BZK4mVDg9UgRSfxOkE'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

?>