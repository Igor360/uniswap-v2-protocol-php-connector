<?php

require (__DIR__ . '/../vendor/autoload.php');

$host = "bsc-dataseed.binance.org";

$credentials = new \Igor360\UniswapV2Connector\Connections\BaseCredentials($host, null, true);

$factory = "0xcA143Ce32Fe78f1f7019d7d551a6402fC5350c73";

$service = new \Igor360\UniswapV2Connector\Services\UniswapFactoryService($factory, $credentials);

// load factory info
var_dump($service->getFactoryInfoJson());

// load pairs

$pairs = $service->getPairs();

var_dump(json_encode($pairs));

// Get pair info

var_dump(json_encode($service->getPairInfo($pairs[0])));


// Get pair address by tokens addresses
$tokenA = "0xe9e7cea3dedca5984780bafc599bd69add087d56";
$tokenB = "0x0e09fabb73bd3ade0a17ecc321fd13a19e81ce82";

var_dump($service->getPairByTokens($tokenA, $tokenB));
