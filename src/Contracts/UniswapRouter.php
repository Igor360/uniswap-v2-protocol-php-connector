<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Contracts;

use Igor360\UniswapV2Connector\Services\ContractService;

/**
 *
 */
class UniswapRouter extends ContractService
{
    function abi(): array
    {
        return json_decode(config("uniswap-v2-connector.routerABI"), true, 512, JSON_THROW_ON_ERROR);
    }

    // Getters

    public function getBaseCoinAddress(string $contractAddress)
    {
        $this->validateAddress($contractAddress);

        return $this->callContractFunction($contractAddress, "WETH");
    }

    public function getFactoryAddress(string $contractAddress)
    {
        $this->validateAddress($contractAddress);
        return $this->callContractFunction($contractAddress, "factory");
    }

    public function getAmountIn(string $contractAddress, int $amountOut, int $reserveIn, int $reserveOut)
    {
        $this->validateAddress($contractAddress);
        return $this->callContractFunction($contractAddress, "getAmountIn", [$amountOut, $reserveIn, $reserveOut]);
    }

    public function getAmountOut(string $contractAddress, int $amountIn, int $reserveIn, int $reserveOut)
    {
        $this->validateAddress($contractAddress);
        return $this->callContractFunction($contractAddress, "getAmountOut", [$amountIn, $reserveIn, $reserveOut]);
    }

    /// TODO need tests
    public function getAmountsOut(string $contractAddress, int $amountIn, array $paths)
    {
        $this->validateAddress($contractAddress, ...$paths);
        return $this->callContractFunction($contractAddress, "getAmountsOut", [$amountIn, $paths]);
    }

    /// TODO need tests
    public function getAmountsIn(string $contractAddress, int $amountOut, array $paths)
    {
        $this->validateAddress($contractAddress, ...$paths);

        return $this->callContractFunction($contractAddress, "getAmountsIn", [$amountOut, $paths]);
    }

    public function getQuote(string $contractAddress, int $amountA, int $reserveA, int $reserveB)
    {
        $this->validateAddress($contractAddress);

        return $this->callContractFunction($contractAddress, "quote", [$amountA, $reserveA, $reserveB]);
    }
}
