<?php

$environment = false;
$api_url = $environment ?
'https://app.midtrans.com/snap/v1/transactions' : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

$server_key = $environment ?
'Mid-server-kbj5leq_SJ783Kl_GjixZr8i' : 'SB-Mid-server-CqoECa1sGqzPHF74AzZ8srx_';

?>