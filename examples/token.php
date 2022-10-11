<?php

require (__DIR__ . '/../vendor/autoload.php');

//$host = "bsc-dataseed.binance.org";
//$host = "data-seed-prebsc-1-s1.binance.org";
//$port = 8545;


$host = "gatotest.corp.merehead.xyz";

$credentials = new \Igor360\UniswapV2Connector\Connections\BaseCredentials($host, "29946", false);
//$credentials = new \Igor360\UniswapV2Connector\Connections\BaseCredentials($host, $port, true);

$token = "0xbbf8ed19c5311fd29ffac02e0dc4efd126a1cac9"; // https://bscscan.com/address/0xe9e7cea3dedca5984780bafc599bd69add087d56

$service = new \Igor360\UniswapV2Connector\Services\TokenService($token, $credentials);

//var_dump($service->getTokenInfoJson());

// Get Balance

var_dump($service->getBalance("0xEdEAc8618132a1AF98936986A58FDB44915602a2"));
var_dump($service->getContract()->balanceOf("0xbbf8ed19c5311fd29ffac02e0dc4efd126a1cac9","0x063C8512E1f351d49b5535b2a4B0BC77Da98153A"));


// Get allowance

var_dump($service->getAllowance("0x74e4716e431f45807dcf19f284c7aa99f18a4fbc", "0xdeda0a79845544814ea92ba4dcf23634b84012ea"));


