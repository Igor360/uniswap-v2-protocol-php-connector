<?php

require(__DIR__ . '/../vendor/autoload.php');

$host = "bsc-dataseed.binance.org";

$addressWallet = "0x10ed43c718714eb63d5aa57b78b54704e256024e";

$credentials = new \Igor360\UniswapV2Connector\Connections\BaseCredentials($host, null, true);
$credentials->setPath("/new/");
$ethService = new \Igor360\UniswapV2Connector\Services\EthereumService($credentials);

var_dump($ethService->getBalance($addressWallet));
