<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Contracts;

use Igor360\UniswapV2Connector\Services\ContractService;

///
class UniswapPair extends ERC20Contract
{
    function abi(): array
    {
        return json_decode(config("uniswap-v2-connector.pairABI"), true, 512, JSON_THROW_ON_ERROR);
    }

    /// Getters

    public function getMinimumLiquidity(string $contractAddress)
    {
        $this->validateAddress($contractAddress);
        return $this->callContractFunction($contractAddress, "MINIMUM_LIQUIDITY");
    }

    public function getFactoryAddress(string $contractAddress)
    {
        $this->validateAddress($contractAddress);

        return $this->callContractFunction($contractAddress, "factory");
    }

    public function getKLast(string $contractAddress)
    {
        $this->validateAddress($contractAddress);
        return $this->callContractFunction($contractAddress, "kLast");
    }

    public function getNoncesByAddress(string $contractAddress, string $account)
    {
        $this->validateAddress($contractAddress);

        return $this->callContractFunction($contractAddress, "nonces", [$account]);
    }

    public function getPrice0CumulativeLast(string $contractAddress)
    {
        $this->validateAddress($contractAddress);

        return $this->callContractFunction($contractAddress, "price0CumulativeLast");
    }

    public function getPrice1CumulativeLast(string $contractAddress)
    {
        $this->validateAddress($contractAddress);

        return $this->callContractFunction($contractAddress, "price1CumulativeLast");
    }

    public function getReserves(string $contractAddress)
    {
        $this->validateAddress($contractAddress);

        return $this->callContractFunction($contractAddress, "getReserves");
    }

    public function getToken0Address(string $contractAddress)
    {
        $this->validateAddress($contractAddress);

        return $this->callContractFunction($contractAddress, 'token0');
    }

    public function getToken1Address(string $contractAddress)
    {
        $this->validateAddress($contractAddress);

        return $this->callContractFunction($contractAddress, 'token1');
    }
}
