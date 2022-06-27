<?php

namespace Igor360\UniswapV2Connector\Contracts;

use Igor360\UniswapV2Connector\Configs\ConfigFacade as Config;
use Igor360\UniswapV2Connector\Services\ContractService;
use Illuminate\Support\Arr;

class UniswapFactory extends ContractService
{
    function abi(): array
    {
        return json_decode(Config::get("uniswap-v2-connector.factoryABI"), true, 512, JSON_THROW_ON_ERROR);
    }

    // Setters

    public function encodeSetFeeTo(string $to): string
    {
        $this->validateAddress($to);
        return $this->ABIService->encodeCall('setFeeTo', [$to]);
    }

    public function encodeSetFeeToSetter(string $to)
    {
        $this->validateAddress($to);
        return $this->ABIService->encodeCall('setFeeToSetter', [$to]);
    }

    // Getters

    public function getPairAddress(string $contractAddress, int $id)
    {
        $this->validateAddress($contractAddress);

        return $this->callContractFunction($contractAddress, 'allPairs', [$id]);
    }

    // Max count created pairs
    public function getAllPairsLenght(string $contractAddress)
    {
        $this->validateAddress($contractAddress);

        return Arr::first($this->callContractFunction($contractAddress, 'allPairsLength'));
    }

    public function getPoolAddressByTokensAddresses(string $contractAddress, string $tokenA, string $tokenB)
    {
        $this->validateAddress($contractAddress, $tokenB, $tokenA);
        return Arr::first($this->callContractFunction($contractAddress, 'getPair', [$tokenA, $tokenB]));
    }

    public function getFeeToAddress(string $contractAddress)
    {
        $this->validateAddress($contractAddress);

        return Arr::first($this->callContractFunction($contractAddress, 'feeTo'));
    }

    public function getFeeToSetterAddress(string $contractAddress)
    {
        $this->validateAddress($contractAddress);
        return Arr::first($this->callContractFunction($contractAddress, "feeToSetter"));
    }
}

