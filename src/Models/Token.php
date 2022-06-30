<?php

namespace Igor360\UniswapV2Connector\Models;

class Token
{
    public ?string $address;

    public string $name;

    public string $symbol;

    public string $decimals;

    public string $totalSupply;

    public ?string $owner; // It's not required field can be null, not all contract to realize this functionality
}
