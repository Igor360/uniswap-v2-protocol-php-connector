<?php
require(__DIR__ . '/../vendor/autoload.php');

use Igor360\UniswapV2Connector\Services\DataTypes\Keccak;

$host = "bsc-dataseed.binance.org";
$hash = "0x08733bd655fda1c46ad62346ab027b8450f8f631b3fa76433a885aafda8fc43e";

$credentials = new \Igor360\UniswapV2Connector\Connections\BaseCredentials($host, null, true);

$service = new \Igor360\UniswapV2Connector\Services\TransactionSwapService($hash, $credentials);

//$service = new \Igor360\UniswapV2Connector\Services\TokenService(null, $credentials);

var_dump($service->getTransactionInfo());
