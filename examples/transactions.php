<?php
require(__DIR__ . '/../vendor/autoload.php');

$host = "bsc-dataseed.binance.org";

$hashSuccess = "0x788d09183c0d527e470337bae907e626d32f2edbdf60a09b4a6f194ecb492a26";
$hashRejected = "0x74e7dae156e9cddc4b498e14c2e9af068dac299fbac95a48a3068242bd6f1ffd";

$credentials = new \Igor360\UniswapV2Connector\Connections\BaseCredentials($host, null, true);

$txSwapService = new \Igor360\UniswapV2Connector\Services\TransactionService($hashSuccess, $credentials);

var_dump($txSwapService->getTransactionInfo());

$txSwapService = new \Igor360\UniswapV2Connector\Services\TransactionService($hashRejected, $credentials);

var_dump($txSwapService->getTransactionInfo());

// Swap tx decode

$hash = "0x79774e1753e925ccfcbc3091d5e6c66fa052becb988d3893574340c24fe51d1c";
//$hash = "0x3b2d0b49dac42a68f0c9c2bfbd14e43f1506506070f4148b831cb4d5a2e61ea5";

$service = new \Igor360\UniswapV2Connector\Services\TransactionSwapService($hash, $credentials);

var_dump($service->getTransactionInfo()->callInfo);

$service->setTransactionHash("0xe864ca685aa915302f98a7d758b5a9c7a410ef54b3bc0b2235dc5379ef7241f5");

var_dump($service->getTransactionInfo()->callInfo);


//var_dump($service->getTransactionInfo());
