<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$curl = curl_init();


$email = $_POST["email"];
$userId = $_POST["userId"];
$transactionId = $_POST["transactionId"];
$name = $_POST["name"];
$mobile = $_POST["mobile"];
$amount = $_POST["amount"];

curl_setopt_array($curl, array(
CURLOPT_URL => 'https://interac.express-connect.com/api/payment-token/7b05b5f72307028a2cf73538ff1adcb0',
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => '',
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'POST',
CURLOPT_POSTFIELDS => json_encode(array( 
'userId'=>$userId, 
'transactionId'=>$transactionId, // specified by merchant 
'name'=>$name, 
'email'=>$email, 
'mobile'=>$mobile, 
'site'=>'https://www.canada777.com', 
'userIp'=>$_SERVER['REMOTE_ADDR'],
'currency'=>'CAD', 
'language'=>'en', 
'amount'=>$amount,
'type'=>'CPI', 
'hosted'=>'true', 
'sandbox' => 'true',
'type'=>'ETO'  
)), 

CURLOPT_HTTPHEADER => array(
'Authorization: Basic ZWVkNThkNzEtMzVhNS00MDcxLTg4NjAtMjFiOWZiYTI5ZjdiOmM5ODZjYTNiLTA0MzYtNDZlOC05ZjY4LTQ4NDRhOGJmZjM0OQ==',
'Content-Type: application/json',
'Cookie: fpcookie=fac4b74804ab645601bea543c553f312; connect.sid=s%3ArSYOdn0ythQZkGi533JF_PZODM6fFNLA.oXIBN5Lj8xgj2q9fU5efuT4n0tsx3gA0dO6U5kgMCT0'
),
));

$response = curl_exec($curl);

curl_close($curl);

//echo $response;

$pay_token = json_decode($response, true)['token'];

//echo $pay_token;

$curl = curl_init();

curl_setopt_array($curl, array(
CURLOPT_URL => 'https://interac.express-connect.com/webflow?token='.$pay_token.'&transaction='.$transactionId.'',
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => '',
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'GET',

CURLOPT_POSTFIELDS => json_encode(array( 
'userId'=>$userId, 
'transactionId'=>$transactionId, // specified by merchant 
'name'=>$name, 
'email'=>$email, 
'mobile'=>$mobile, 
'site'=>'https://www.canada777.com', 
'userIp'=>$_SERVER['REMOTE_ADDR'],
'currency'=>'CAD', 
'language'=>'en', 
'amount'=>$amount,
'type'=>'CPI', 
'hosted'=>'true', 
'sandbox' => 'true',
'type'=>'ETO'  
)), 

CURLOPT_HTTPHEADER => array(
'Authorization: Basic ZWVkNThkNzEtMzVhNS00MDcxLTg4NjAtMjFiOWZiYTI5ZjdiOmM5ODZjYTNiLTA0MzYtNDZlOC05ZjY4LTQ4NDRhOGJmZjM0OQ==',
'Content-Type: application/json',
'Cookie: fpcookie=fac4b74804ab645601bea543c553f312; connect.sid=s%3ArSYOdn0ythQZkGi533JF_PZODM6fFNLA.oXIBN5Lj8xgj2q9fU5efuT4n0tsx3gA0dO6U5kgMCT0'
),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

?>