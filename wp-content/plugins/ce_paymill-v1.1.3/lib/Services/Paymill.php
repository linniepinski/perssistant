<?php
if (!function_exists('curl_init')) {
	throw new Exception('Stripe needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
	throw new Exception('Stripe needs the JSON PHP extension.');
}
require(dirname(__FILE__) . '/Paymill/Transactions.php');
require(dirname(__FILE__) . '/Paymill/Clients.php');