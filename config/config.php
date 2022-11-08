<?php
return [
    "routerABI" => \Igor360\UniswapV2Connector\Configs\ConfigFacade::loadRouterABI(),
    "factoryABI" => \Igor360\UniswapV2Connector\Configs\ConfigFacade::loadFactoryABI(),
    "pairABI" => \Igor360\UniswapV2Connector\Configs\ConfigFacade::loadPairABI(),
    "erc20ABI" => \Igor360\UniswapV2Connector\Configs\ConfigFacade::loadERC20ABI(),
    "erc721ABI" => \Igor360\UniswapV2Connector\Configs\ConfigFacade::loadERC721ABI(),
    "wethABI" =>  \Igor360\UniswapV2Connector\Configs\ConfigFacade::loadWETHABI(),
    "eth" => [
        "host" => "",
        "port" => "",
        "ssh" => false
    ]
];
