<?php

require (__DIR__ . '/../vendor/autoload.php');

//$host = "bsc-dataseed.binance.org";
$host = "data-seed-prebsc-1-s1.binance.org";
$port = 8545;

$credentials = new \Igor360\UniswapV2Connector\Connections\BaseCredentials($host, $port, true);

$token = "0xaebcd1e0807d0000a382a4d95b9045bbeb4ad795"; // https://bscscan.com/address/0xe9e7cea3dedca5984780bafc599bd69add087d56

$service = new \Igor360\UniswapV2Connector\Services\TokenService($token, $credentials);

var_dump($service->getTokenInfoJson());

// Get Balance

var_dump($service->getBalance("0xEdEAc8618132a1AF98936986A58FDB44915602a2"));


// Get allowance

var_dump($service->getAllowance("0x74e4716e431f45807dcf19f284c7aa99f18a4fbc", "0xdeda0a79845544814ea92ba4dcf23634b84012ea"));


