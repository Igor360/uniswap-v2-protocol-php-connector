<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Contracts;

use Igor360\UniswapV2Connector\Configs\ConfigFacade as Config;
use Igor360\UniswapV2Connector\Services\ContractService;
use Illuminate\Support\Arr;

/**
 *
 */
class UniswapRouter extends ContractService
{
    function abi(): array
    {
        return json_decode(Config::get("uniswap-v2-connector.routerABI"), true, 512, JSON_THROW_ON_ERROR);
    }

    // Getters

    public function getBaseCoinAddress(string $contractAddress)
    {
        $this->validateAddress($contractAddress);

        return Arr::first($this->callContractFunction($contractAddress, "WETH"));
    }

    public function getFactoryAddress(string $contractAddress)
    {
        $this->validateAddress($contractAddress);
        return Arr::first($this->callContractFunction($contractAddress, "factory"));
    }

    public function getAmountIn(string $contractAddress, string $amountOut, string $reserveIn, string $reserveOut)
    {
        $this->validateAddress($contractAddress);
        return Arr::first($this->callContractFunction($contractAddress, "getAmountIn", [$amountOut, $reserveIn, $reserveOut]));
    }

    public function getAmountOut(string $contractAddress, string $amountIn, string $reserveIn, string $reserveOut)
    {
        $this->validateAddress($contractAddress);
        return Arr::first($this->callContractFunction($contractAddress, "getAmountOut", [$amountIn, $reserveIn, $reserveOut]));
    }

    /// TODO need tests
    public function getAmountsOut(string $contractAddress, string $amountIn, array $paths)
    {
        $this->validateAddress($contractAddress, ...$paths);
        return Arr::first($this->callContractFunction($contractAddress, "getAmountsOut", [$amountIn, $paths]));
    }

    /// TODO need tests
    public function getAmountsIn(string $contractAddress, string $amountOut, array $paths)
    {
        $this->validateAddress($contractAddress, ...$paths);

        return Arr::first($this->callContractFunction($contractAddress, "getAmountsIn", [$amountOut, $paths]));
    }

    public function getQuote(string $contractAddress, string $amountA, string $reserveA, string $reserveB)
    {
        $this->validateAddress($contractAddress);

        return Arr::first($this->callContractFunction($contractAddress, "quote", [$amountA, $reserveA, $reserveB]));
    }
}
