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
