<?php

require (__DIR__ . '/../vendor/autoload.php');

$host = "bsc-dataseed.binance.org";

$token = "0x0e09fabb73bd3ade0a17ecc321fd13a19e81ce82"; // https://bscscan.com/address/0x0e09fabb73bd3ade0a17ecc321fd13a19e81ce82

$credentials = new \Igor360\UniswapV2Connector\Connections\BaseCredentials($host, null, true);

$service = new \Igor360\UniswapV2Connector\Services\TokenService($token, $credentials);

var_dump($service->getTokenInfoJson());


$token = "0xe9e7cea3dedca5984780bafc599bd69add087d56"; // https://bscscan.com/address/0xe9e7cea3dedca5984780bafc599bd69add087d56

$service = new \Igor360\UniswapV2Connector\Services\TokenService($token, $credentials);

var_dump($service->getTokenInfoJson());

// Get Balance

var_dump($service->getBalance("0x74e4716e431f45807dcf19f284c7aa99f18a4fbc"));


// Get allowance

var_dump($service->getAllowance("0x74e4716e431f45807dcf19f284c7aa99f18a4fbc", "0xdeda0a79845544814ea92ba4dcf23634b84012ea"));
