<?php

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Contracts\ContractsFactory;
use Igor360\UniswapV2Connector\Contracts\UniswapFactory;
use Igor360\UniswapV2Connector\Exceptions\GethException;
use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;
use Igor360\UniswapV2Connector\Models\Factory;
use Igor360\UniswapV2Connector\Models\Pair;
use Illuminate\Support\Arr;

class UniswapFactoryService
{
    private ?string $contractAddress;

    private UniswapFactory $contract;

    private Factory $factoryInfo;

    private UniswapPairService $pairService;

    /**
     * @param string $contractAddress
     * @param ConnectionInterface $credentials
     * @throws \Igor360\UniswapV2Connector\Exceptions\InvalidContractInstanceKeyException
     */
    public function __construct(?string $contractAddress, ConnectionInterface $credentials)
    {
        $this->contractAddress = $contractAddress;
        $this->contract = ContractsFactory::make('factory', $credentials);
        $this->factoryInfo = new Factory();
        $this->factoryInfo->address = $contractAddress;
        $this->pairService = new UniswapPairService(null, $credentials);
        if (!is_null($contractAddress)) {
            $this->loadFactoryInfo();
        }
    }


    public function getFactoryInfo(): Factory
    {
        return $this->factoryInfo;
    }

    /**
     * @throws \JsonException
     */
    public function getFactoryInfoJson(): string
    {
        return json_encode($this->factoryInfo, JSON_THROW_ON_ERROR);
    }

    /**
     * @return string|null
     */
    public function getContractAddress(): ?string
    {
        return $this->contractAddress;
    }

    /**
     * @return UniswapFactory|ContractService
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @param string|null $contractAddress
     * @return UniswapFactoryService
     */
    public function setContractAddress(?string $contractAddress): self
    {
        $this->contractAddress = $contractAddress;
        $this->factoryInfo->address = $contractAddress;
        return $this;
    }

    public function loadFactoryInfo(): self
    {
        $this->factoryInfo->allPairsLength = $this->contract->getAllPairsLenght($this->contractAddress);
        $this->factoryInfo->feeTo = $this->contract->getFeeToAddress($this->contractAddress);
        $this->factoryInfo->feeToSetter = $this->contract->getFeeToSetterAddress($this->contractAddress);
        return $this;
    }

    public function getPairs(int $elements = 15, int $from = 0): array
    {
        $pairs = [];
        for ($i = $from; $elements > $i - $from; $i++) {
            try {
                $pairs[] = Arr::first($this->contract->getPairAddress($this->contractAddress, $i));
            } catch (GethException $exception) {
                continue;
            }
        }
        return $pairs;
    }

    public function getPairsFromTo(int $from = 0, int $to = 15): array
    {
        return $this->getPairs($from, $to - $from);
    }

    public function getAllPairs(): array
    {
        return $this->getPairs( $this->factoryInfo->allPairsLength, 0);
    }

    public function getPairInfo(string $address): Pair
    {
        $this->pairService->setContractAddress($address);
        $this->pairService->loadPairInfo();
        $this->pairService->loadToken1Info();
        $this->pairService->loadToken0Info();
        return $this->pairService->getPairInfo();
    }

    public function getPairInfoSimple(string $address): Pair
    {
        $this->pairService->setContractAddress($address);
        $this->pairService->loadPairInfo();
        return $this->pairService->getPairInfo();
    }

    public function getPairByTokens(string $tokenA, string $tokenB): string
    {
        $this->contract->validateAddress($tokenA, $tokenB);
        return $this->contract->getPoolAddressByTokensAddresses($this->contractAddress, $tokenA, $tokenB);
    }
}
