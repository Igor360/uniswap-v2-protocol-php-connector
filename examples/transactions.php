<?php
require(__DIR__ . '/../vendor/autoload.php');

//$host = "bsc-dataseed.binance.org";
//
//$hashSuccess = "0x788d09183c0d527e470337bae907e626d32f2edbdf60a09b4a6f194ecb492a26";
//$hashRejected = "0x74e7dae156e9cddc4b498e14c2e9af068dac299fbac95a48a3068242bd6f1ffd";
//
//$credentials = new \Igor360\UniswapV2Connector\Connections\BaseCredentials($host, null, true);
//
//$txSwapService = new \Igor360\UniswapV2Connector\Services\TransactionService($hashSuccess, $credentials);
//
//var_dump($txSwapService->getTransactionInfo());
//
//$txSwapService = new \Igor360\UniswapV2Connector\Services\TransactionService($hashRejected, $credentials);
//
//var_dump($txSwapService->getTransactionInfo());
//
//// Swap tx decode
//
//$hash = "0x79774e1753e925ccfcbc3091d5e6c66fa052becb988d3893574340c24fe51d1c";
////$hash = "0x3b2d0b49dac42a68f0c9c2bfbd14e43f1506506070f4148b831cb4d5a2e61ea5";
//
//$service = new \Igor360\UniswapV2Connector\Services\TransactionSwapService($hash, $credentials);
//
//var_dump($service->getTransactionInfo()->callInfo);
//
//$service->setTransactionHash("0xcedd18b05cc7774048c710269481c1fa6313e98c216d214ab6d425d0e03c3cbe");
//
//var_dump($service->getTransactionInfo()->callInfo);


$host = "gatotest.corp.merehead.xyz";

$credentials = new \Igor360\UniswapV2Connector\Connections\BaseCredentials($host, "29946", false);
$txSwapService = new \Igor360\UniswapV2Connector\Services\TransactionSwapService("0xdf82d268029f800e362ac8ed6aba35c1a34281bc5b7539d4daf21dbbb08fb270", $credentials);

var_dump($txSwapService->getTransactionInfo());
//$txSwapService = new \Igor360\UniswapV2Connector\Services\TransactionTokenService("0x0b86b74853199e3ee8e4a05de3617841ebfad3cdf7f9549bf72abcd5963fd058", $credentials);

//var_dump($txSwapService->getTransactionInfo());
