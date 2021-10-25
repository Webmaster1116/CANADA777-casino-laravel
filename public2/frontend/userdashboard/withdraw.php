<?php

$curl = curl_init();

curl_setopt_array($curl, array(
CURLOPT_URL => "https://interac.express-connect.com/webflow?token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjExNDQ2NjExLCJ0cmFuc2FjdGlvbklkIjoiQUItMDUiLCJuYW1lIjoiR2lnYSBUZXN0IiwiZW1haWwiOiJjaHJpc0BjaHJpcy5jb20iLCJtb2JpbGUiOiIxMjM0NTY3ODkwIiwiYWNjdCI6IjEyMzQ1Njc4OTAiLCJzaXRlIjoiaHR0cHM6IC8vd3d3LnRlc3QuY29tIiwidXNlcklwIjoiNzAuNjcuMTY4LjE1NSIsImN1cnJlbmN5IjoiQ0FEIiwibGFuZ3VhZ2UiOiJlbiIsImFtb3VudCI6MTAuNDUsInR5cGUiOiJFVEkiLCJob3N0ZWQiOiJmYWxzZSIsInNhbmRib3giOnRydWUsInVzZXIiOiJjNWVjNzAxNDI4ZTBlOTNmMjY1OGU3MzkyNjZhZDc4YyIsImNhbXBhaWduIjoiN2IwNWI1ZjcyMzA3MDI4YTJjZjczNTM4ZmYxYWRjYjAiLCJpYXQiOjE2MDUwMjg3Nzd9.nwSUpmurEqeq6BwOBq0H-D1xLZmtdm0hWVlqsiL6RwI&transaction=AB-05",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => "",
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => "POST",
CURLOPT_POSTFIELDS =>"{\"userId\": 11446611,\r\n \"transactionId\": \"AB-05\",\r\n \"name\": \"Giga Test\",\r\n \"email\": \"chris@chris.com\",\r\n \"mobile\": \"1234567890\",\r\n \"acct\": \"1234567890\",\r\n \"site\": \"https: //www.test.com\",\r\n \"userIp\": \"70.67.168.155\",\r\n \"currency\": \"CAD\",\r\n \"language\": \"en\",\r\n \"amount\": 10.45,\r\n \"type\": \"ETI\",\r\n \"hosted\": \"false\",\r\n \"sandbox\": true\r\n }",
CURLOPT_HTTPHEADER => array(
"Authorization: Basic ZWVkNThkNzEtMzVhNS00MDcxLTg4NjAtMjFiOWZiYTI5ZjdiOmM5ODZjYTNiLTA0MzYtNDZlOC05ZjY4LTQ4NDRhOGJmZjM0OQ==",
"Content-Type: application/json",
"Cookie: fpcookie=fac4b74804ab645601bea543c553f312; connect.sid=s%3AYnpY_Y-s4KsICO3wYsSwJSTHzvaBIAOx.IEdOxItWAy8g7%2BV%2Ba8j7MhpW1%2BZK4mVDg9UgRSfxOkE"
),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

?>