<?php

$curl = curl_init();

curl_setopt_array($curl, array(
CURLOPT_URL => 'https://interac.express-connect.com/webflow?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyaWQiOiJBQi0xQSIsInRyYW5zYWN0aW9uaWQiOiIxMDEyMyIsIm5hbWUiOiJsb2dhbiIsImVtYWlsIjoidGVzdEB0ZXN0Lm1haWwiLCJzaXRlIjoiQ2hyaXMudGVzdCIsInVzZXJJcCI6IjExMS4yMi4xMDYuNTIiLCJtb2JpbGUiOiI0MDM5OTE5OTkxIiwiY3VycmVuY3kiOiJDQUQiLCJsYW5ndWFnZSI6ImVuIiwiYW1vdW50IjozMCwidHlwZSI6IkVUTyIsInNhbmRib3giOnRydWUsImhvc3RlZCI6dHJ1ZSwidXNlciI6ImM1ZWM3MDE0MjhlMGU5M2YyNjU4ZTczOTI2NmFkNzhjIiwiY2FtcGFpZ24iOiI3YjA1YjVmNzIzMDcwMjhhMmNmNzM1MzhmZjFhZGNiMCIsImlhdCI6MTYwODI5Nzg1Mn0.TwgWmG7TdA2zcosP4tFE1yjbWOTsYNBclTXNaw9uWB8&transaction=AB-2A',
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => '',
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'GET',
CURLOPT_POSTFIELDS =>'{
"userid":"AB-2A",
"transactionid":"101233",
"name":"logan",
"email":"test@test.mail",
"site":"Chris.test",
"userIp":"111.22.106.52",
"mobile":"4039919991",
"currency":"CAD",
"language":"en",
"amount":30.0,
"type":"CPI",
"sandbox":true,
"hosted":true,
"type":"ETO"
}',
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