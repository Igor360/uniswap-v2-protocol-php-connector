<?php
require(__DIR__ . '/../vendor/autoload.php');


$host = "gatotest.corp.merehead.xyz";

$credentials = new \Igor360\UniswapV2Connector\Connections\BaseCredentials($host, "29946", false);

$hash = "0x38b27292036bd4117bd812a6e16d28ac7ddc241e3cc618cf1b14f544606d5fa1";

$service = new \Igor360\UniswapV2Connector\Services\TransactionService($hash, $credentials);


// Tx info
var_dump($service->getTransactionInfoJson());

// Tx trace
$txTrace = $service->getRpc()->getTransactionTrace($hash);
file_put_contents("test.json", json_encode($txTrace));
var_dump(\Igor360\UniswapV2Connector\Services\DataTypes\ASCII::base16Decode($txTrace['returnValue'] ?? null)); // reason reject
var_dump($txTrace['failed']);

// Block trace

var_dump(\Igor360\UniswapV2Connector\Services\DataTypes\ASCII::base16Decode($service->getRpc()->getBlockTrace(1001)[0]['result']['returnValue']));
var_dump($service->getRpc()->getBlockTrace(1010)[0]['result']['failed']);
