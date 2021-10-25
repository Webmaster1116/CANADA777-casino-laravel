<?php
curl --location --request POST 'https://interac.express-connect.com/api/payment-token/7b05b5f72307028a2cf73538ff1adcb0' \
--header 'Authorization: Basic ZWVkNThkNzEtMzVhNS00MDcxLTg4NjAtMjFiOWZiYTI5ZjdiOmM5ODZjYTNiLTA0MzYtNDZlOC05ZjY4LTQ4NDRhOGJmZjM0OQ==' \
--header 'Content-Type: application/json' \
--header 'Cookie: fpcookie=fac4b74804ab645601bea543c553f312; connect.sid=s%3AYnpY_Y-s4KsICO3wYsSwJSTHzvaBIAOx.IEdOxItWAy8g7%2BV%2Ba8j7MhpW1%2BZK4mVDg9UgRSfxOkE' \
--data-raw '{"userId": 11446611,
"transactionId": "AB-04",
"name": "Giga Test",
"email": "chris@chris.com",
"mobile": "1234567890",
"acct": "1234567890",
"site": "https: //www.test.com",
"userIp": "70.67.168.155",
"currency": "CAD",
"language": "en",
"amount": 10.45,
"type": "ETI",
"hosted": "false",
"sandbox": true
}'

?>