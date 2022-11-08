<?php

namespace Igor360\UniswapV2Connector\Models;

class Pair
{
    public ?string $pairAddress;

    public string $token0;

    public Token $token0Info;

    public string $token1;

    public Token $token1Info;

    public string $reserve0;

    public string $reserve1;

    public string $kLast;

    // the price of token0 denominated in token1
    public string $price0CumulativeLast;

    /// the price of token1 denominated in token0
    public string $price1CumulativeLast;

    public string $lpSupply;

    public string $lpName;

    public string $lpSymbol;

    public int $lpDecimals;

    public string $priceToken0toToken1;

    public string $priceToken1toToken0;

    public bool $hasLiquidity = false;

    public int $blockTimestamp;

    public string $blockTimestampDateTime;


}
