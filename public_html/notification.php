<?php
require 'vendor/autoload.php';
include 'MidtransUtils.php';

use Kreait\Firebase\Factory;




$data = file_get_contents('php://input');
$data = json_decode($data);

if (isset($data->order_id)) {
	
	$input = $data->order_id . $data->status_code . $data->gross_amount . $server_key;
	$signature = openssl_digest($input, 'sha512');

	if ($signature == $data->signature_key) {

		if ($data->payment_type == "cstore") {
			$payment_type = ucfirst($data->store);
			$payment_code = $data->payment_code;
		} else if ($data->payment_type == "bank_transfer") {
			if (isset($data->permata_va_number)) {
				$payment_type = "Permata";
				$payment_code = $data->permata_va_number;
			} else {
				$payment_type = $data->va_numbers[0]->bank;
				$payment_type = strtoupper($payment_type);
				$payment_code = $data->va_numbers[0]->va_number;
			}
		} else if ($data->payment_type == "echannel") {
			$payment_type =  "Mandiri";
			$payment_code = $data->biller_code . $data->bill_key;
		}

		if($data->status_code == "200"){
			$status = 1;
		} else if ($data->status_code = "201"){
			$status = 0;
		} else if($data->status_code = "202"){
			$status = 2;
		} else {
			$status = 2;
		}

		$email = explode("_", $data->order_id)[0];

		$factory = (new Factory)->withServiceAccount('/var/www/html/medico/medico-8c179-8fd5d0a844c4.json');
		$database = $factory->createDatabase();
		$postData = [
			'status' => $status,
			'id_transaksi_midtrans' => $data->transaction_id,
			'waktu_transaksi_midtrans' => $data->transaction_time,
			'jenis_pembayaran' => $payment_type,
			'kode_pembyaran' => $payment_code
		];
		$database->getReference('Tiket/'.$email.'/'.$data->order_id)->update($postData);

		$result['status'] = TRUE;
		$result['message'] = "Sukses menerima notifikasi";

	} else {
		$result['status'] = FALSE;
		$result['message'] = "Signature Key tidak valid";
	}
} else {
	$result['status'] = FALSE;
	$result['message'] = "Data tidak valid";
}


echo json_encode($result);
?>