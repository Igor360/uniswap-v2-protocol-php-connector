<?php

namespace Igor360\UniswapV2Connector\Contracts;

use Igor360\UniswapV2Connector\Exceptions\InvalidContractInstanceKeyException;
use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;
use Igor360\UniswapV2Connector\Services\ContractService;

abstract class ContractsFactory
{
    protected const ContractClasses = [
        'erc20' => ERC20Contract::class,
        'weth' => WETHContract::class,
        'erc721' => ERC721Contract::class,
        'factory' => UniswapFactory::class,
        'pair' => UniswapPair::class,
        'router' => UniswapRouter::class,
    ];

    public static function make(string $key, ConnectionInterface $credentials): ContractService
    {
        if (array_key_exists($key, self::ContractClasses)) {
            $class = self::ContractClasses[$key];
            return new $class($credentials);
        }
        throw new InvalidContractInstanceKeyException();
    }
}

