<?php

namespace Igor360\UniswapV2Connector\Contracts;

use Igor360\UniswapV2Connector\Services\ContractService;

class UniswapFactory extends ContractService
{
    function abi(): array
    {
        return json_decode(config("uniswap-v2-connector.factoryABI"), true, 512, JSON_THROW_ON_ERROR);
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

        return $this->callContractFunction($contractAddress, 'allPairsLength');
    }

    public function getPoolAddressByTokensAddresses(string $contractAddress, string $tokenA, string $tokenB)
    {
        $this->validateAddress($contractAddress, $tokenB, $tokenA);
        return $this->callContractFunction($contractAddress, 'getPair');
    }

    public function getFeeToAddress(string $contractAddress)
    {
        $this->validateAddress($contractAddress);

        return $this->callContractFunction($contractAddress, 'feeTo');
    }

    public function getFeeToSetterAddress(string $contractAddress)
    {
        $this->validateAddress($contractAddress);
        return $this->callContractFunction($contractAddress, "feeToSetter");
    }
}

