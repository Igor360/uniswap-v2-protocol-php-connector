<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Contracts\ContractsFactory;
use Igor360\UniswapV2Connector\Contracts\UniswapRouter;
use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;
use Igor360\UniswapV2Connector\Models\Router;

class UniswapRouteService
{
    private ?string $contractAddress;

    private UniswapRouter $contract;

    private Router $routerInfo;

    private UniswapFactoryService $factoryService;

    public function __construct(?string $contractAddress, ConnectionInterface $credentials)
    {
        $this->contractAddress = $contractAddress;
        $this->contract = ContractsFactory::make('router', $credentials);
        $this->routerInfo = new Router();
        $this->routerInfo->address = $contractAddress;
        $this->factoryService = new UniswapFactoryService(null, $credentials);
        if (!is_null($contractAddress)) {
            $this->loadRouterInfo();
        }
    }

    public function loadRouterInfo(): self
    {
        $this->routerInfo->factory = $this->contract->getFactoryAddress($this->contractAddress);
        $this->routerInfo->baseToken = $this->contract->getBaseCoinAddress($this->contractAddress);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContractAddress(): ?string
    {
        return $this->contractAddress;
    }

    /**
     * @param string|null $contractAddress
     */
    public function setContractAddress(?string $contractAddress): void
    {
        $this->contractAddress = $contractAddress;
        $this->routerInfo->address = $contractAddress;
    }

    public function getFactory(): UniswapFactoryService
    {
        $this->factoryService->setContractAddress($this->routerInfo->factory);
        $this->factoryService->loadFactoryInfo();
        return $this->factoryService;
    }

    public function getRouterInfo(): Router
    {
        return $this->routerInfo;
    }

    /**
     * @throws \JsonException
     */
    public function getRouterInfoJson(): string
    {
        return json_encode($this->routerInfo, JSON_THROW_ON_ERROR);
    }

    public function getAmountOut($amountIn, $reserveIn, $reserveOut)
    {
        return $this->contract->getAmountOut($this->contractAddress, $amountIn, $reserveIn, $reserveOut);
    }

    public function getAmountIn($amountOut, $reserveIn, $reserveOut)
    {
        return $this->contract->getAmountIn($this->contractAddress, $amountOut, $reserveIn, $reserveOut);
    }

    public function getAmountsOut($amountIn, ...$paths)
    {
        return $this->contract->getAmountsOut($this->contractAddress, $amountIn, $paths);
    }

    public function getAmountsIn($amountOut, ...$paths)
    {
        return $this->contract->getAmountsIn($this->contractAddress, $amountOut, $paths);
    }

    public function getQuote($amountA, $reserveA, $reserveB)
    {
        return $this->contract->getQuote($this->contractAddress, $amountA, $reserveA, $reserveB);
    }
}
