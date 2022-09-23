<?php
require (__DIR__ . '/../vendor/autoload.php');

//$host = "bsc-dataseed.binance.org";

$host = "gatotest.corp.merehead.xyz";

$credentials = new \Igor360\UniswapV2Connector\Connections\BaseCredentials($host, "29946", false);

//$route = "0x10ED43C718714eb63d5aA57B78B54704E256024E";
$route = "0x2Cf2EDC25f069f9f5B96A12dDdb05bDdB5280C3C";

$service = new \Igor360\UniswapV2Connector\Services\UniswapRouteService($route, $credentials);

// Get info

var_dump($service->getRouterInfoJson());

/// Get factory and load pairs

$factory = $service->getFactory();

var_dump($factory->getFactoryInfo());

$pairs = $factory->getPairs(); // get's 15 pairs

var_dump(json_encode($pairs));

// Load pair info
$pair = $factory->getPairInfo($pairs[0]);

var_dump($pair);

///// Calculate amountOut for pair
//
//$amountIn = 10 ** (int)$pair->token0Info->decimals;
//var_dump($service->getAmountOut((string)$amountIn, $pair->reserve0, $pair->reserve1));
//
///// Calculate amountIn for pair
//
//$amountOut = 10 ** (int)$pair->token0Info->decimals;
//var_dump($service->getAmountIn((string)$amountOut, $pair->reserve0, $pair->reserve1));
//
//// Calculate Quote
//
//$amountA = 10 ** (int)$pair->token0Info->decimals;
//var_dump($service->getQuote((string)$amountA, $pair->reserve0, $pair->reserve1));


// Calculate amountsOut for pair

$amountIn = 10 ** (int)$pair->token0Info->decimals;
var_dump($service->getAmountsOut((string)$amountIn, $pair->token0, $pair->token1));
//
///// Calculate amountsIn for pair
//
//$amountOut = 10 ** (int)$pair->token0Info->decimals;
//var_dump($service->getAmountsIn((string)$amountOut, $pair->token0, $pair->token1));

