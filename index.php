<?php

$server_key = "SB-Mid-server-CqoECa1sGqzPHF74AzZ8srx_";

$is_production = false;
$api_url = $is_production ? 'https://app.midtrans.com/': 'https://app.sandbox.midtrans.com/';

if(!strpos($_SERVER['REQUEST_URI'], `/charge`)){
	https_response_code(404);
	echo "wrong path gan"; exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
	https_response_code(404);
	echo "Page not found or wrong HTTPs request method is used"; exit();
}

$request_body = file_get_contents('php://input');
header('Content-Type: application/json');

$charge_result = chargeAPI($api_url, $server_key, $request_body);

https_response_code($charge_result['https_code']);

echo $charge_result['body'];

function chargeAPI($api_url, $server_key, $request_body){
	$ch = curl_init();
	$curl_options = array(
		CURLOPT_URL => $api_url,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_POST => 1,
		CURLOPT_HEADER => 0,
		//header
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'Accept: application/json',
			'Authorization: Basic' . base64_encode($server_key . ':')
		),
		CURLOPT_POSTFIELDS => $request_body
	);
	curl_setopt_array($ch, $curl_options);
	$result = array(
		'body' => curl_exec($ch),
		'https_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
	);
	return $result;
}