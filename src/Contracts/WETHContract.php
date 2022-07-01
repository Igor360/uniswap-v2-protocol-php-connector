<?php

namespace Igor360\UniswapV2Connector\Contracts;

use Igor360\UniswapV2Connector\Configs\ConfigFacade as Config;

class WETHContract extends ERC20Contract
{
    function abi(): array
    {
        return json_decode(Config::get("uniswap-v2-connector.erc20ABI"), true, 512, JSON_THROW_ON_ERROR);
    }

}
