<?php        
include 'MidtransUtils.php';
        // Check if request doesn't contains `/charge` in the url/path, display 404
if (!strpos($_SERVER['REQUEST_URI'], '/charge')) {
	http_response_code(404);
	echo "wrong path, make sure it's `/charge`";
	exit();
}
        // Check if method is not HTTP POST, display 404
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(404);
	echo "Page not found or wrong HTTP request method is used";
	exit();
}
        // get the HTTP POST body of the request
$request_body = file_get_contents('php://input');
        // set response's content type as JSON
header('Content-Type: application/json');
        // call charge API using request body passed by mobile SDK


$ch = curl_init();
$curl_options = array(
	CURLOPT_URL => $api_url,
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_POST => 1,
	CURLOPT_HEADER => 0,
            // Add header to the request, including Authorization generated from server key
	CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json',
		'Accept: application/json',
		'Authorization: Basic ' . base64_encode($server_key . ':')
	),
	CURLOPT_POSTFIELDS => $request_body
);
curl_setopt_array($ch, $curl_options);

        // set the response http status code
http_response_code(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        // then print out the response body
echo curl_exec($ch);


?>