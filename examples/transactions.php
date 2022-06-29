<?php
require(__DIR__ . '/../vendor/autoload.php');

$host = "bsc-dataseed.binance.org";

$hashSuccess = "0x788d09183c0d527e470337bae907e626d32f2edbdf60a09b4a6f194ecb492a26";
$hashRejected = "0x74e7dae156e9cddc4b498e14c2e9af068dac299fbac95a48a3068242bd6f1ffd";

$credentials = new \Igor360\UniswapV2Connector\Connections\BaseCredentials($host, null, true);

$txSwapService = new \Igor360\UniswapV2Connector\Services\TransactionService($hashSuccess, $credentials);

var_dump($txSwapService->loadLogs());
