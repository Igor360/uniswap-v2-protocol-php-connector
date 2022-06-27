<?php
require (__DIR__ . '/../vendor/autoload.php');

$host = "bsc-dataseed.binance.org";

// https://api.pancakeswap.info/api/v2/tokens/0xe9e7cea3dedca5984780bafc599bd69add087d56
// Get pair info

$pair = "0x804678fa97d91B974ec2af3c843270886528a9E6"; // https://bscscan.com/address/0x804678fa97d91B974ec2af3c843270886528a9E6#code

$credentials = new \Igor360\UniswapV2Connector\Connections\BaseCredentials($host, null, true);

$pairService = new \Igor360\UniswapV2Connector\Services\UniswapPairService($pair, $credentials);

var_dump($pairService->getPairInfoJson());

// Get pair info with tokens


$pairService->loadToken0Info();
$pairService->loadToken1Info();

var_dump($pairService->getPairInfoJson());
