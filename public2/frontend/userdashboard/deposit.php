<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://interac.express-connect.com/webflow?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjExNDQ2NjExLCJ0cmFuc2FjdGlvbklkIjoiQUItMDQiLCJuYW1lIjoiR2lnYSBUZXN0IiwiZW1haWwiOiJjaHJpc0BjaHJpcy5jb20iLCJtb2JpbGUiOiIxMjM0NTY3ODkwIiwiYWNjdCI6IjEyMzQ1Njc4OTAiLCJzaXRlIjoiaHR0cHM6IC8vd3d3LnRlc3QuY29tIiwidXNlcklwIjoiNzAuNjcuMTY4LjE1NSIsImN1cnJlbmN5IjoiQ0FEIiwibGFuZ3VhZ2UiOiJlbiIsImFtb3VudCI6MTAuNDUsInR5cGUiOiJFVEkiLCJob3N0ZWQiOiJmYWxzZSIsInNhbmRib3giOnRydWUsInVzZXIiOiJjNWVjNzAxNDI4ZTBlOTNmMjY1OGU3MzkyNjZhZDc4YyIsImNhbXBhaWduIjoiN2IwNWI1ZjcyMzA3MDI4YTJjZjczNTM4ZmYxYWRjYjAiLCJpYXQiOjE2MDUwMjEyMTR9.SPX0Rbn9RRPzsX8pqZFKIEcPd6Z8IySuQrG7x6lUlq8&transaction=AB-566',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_POSTFIELDS =>'{"userId": 11446694,
"transactionId": "AB-566",
"name": "payout code",
"email": "martibnfotech@gmail.com",
"mobile": "1234567890",
"acct": "1234567894",
"site": "https: //www.test.com",
"userIp": "70.67.168.155",
"currency": "CAD",
"language": "en",
"amount": 10.46,
"type": "ETO",
"hosted": "true",
"sandbox": false
}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic MTllNTJmMjAtZTg1Zi00ZmRkLWE3YWYtZGY3NzU1ZmRlMzA4OmJhNDViYjNiLTg2NTQtNDNlYS1iODEyLTZlODY1NWY0YjhhNQ==',
    'Content-Type: application/json',
    'Cookie: fpcookie=fac4b74804ab645601bea543c553f312; connect.sid=s%3AYnpY_Y-s4KsICO3wYsSwJSTHzvaBIAOx.IEdOxItWAy8g7%2BV%2Ba8j7MhpW1%2BZK4mVDg9UgRSfxOkE'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
 

?>