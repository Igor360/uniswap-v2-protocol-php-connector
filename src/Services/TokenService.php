<?php

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Contracts\ContractsFactory;
use Igor360\UniswapV2Connector\Contracts\ERC20Contract;
use Igor360\UniswapV2Connector\Contracts\WETHContract;
use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;
use Igor360\UniswapV2Connector\Models\Token;

class TokenService
{
    protected ?string $contractAddress;

    private ERC20Contract $contract;

    /**
     * @var WETHContract|ContractService
     */
    private WETHContract $contractWeth;

    private Token $tokeInfo;

    public function __construct(?string $contractAddress, ConnectionInterface $credentials)
    {
        $this->contractAddress = $contractAddress;
        $this->tokeInfo = new Token();
        $this->tokeInfo->address = $contractAddress;
        $this->contract = ContractsFactory::make('erc20', $credentials);
        $this->contractWeth = ContractsFactory::make('weth', $credentials);
        if (!is_null($contractAddress)) {
            $this->loadTokenInfo();
        }
    }

    /**
     * @return WETHContract|ContractService
     */
    public function getContractWeth()
    {
        return $this->contractWeth;
    }


    /**
     * @return string|null
     */
    public function getContractAddress(): ?string
    {
        return $this->contractAddress;
    }

    /**
     * @return ERC20Contract|ContractService
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @param string|null $contractAddress
     * @return TokenService
     */
    public function setContractAddress(string $contractAddress): self
    {
        $this->contractAddress = $contractAddress;
        return $this;
    }

    public function getTokenInfo(): Token
    {
        return $this->tokeInfo;
    }

    /**
     * @throws \JsonException
     */
    public function getTokenInfoJson(): string
    {
        return json_encode($this->tokeInfo, JSON_THROW_ON_ERROR);
    }

    public function loadTokenInfo(): self
    {
        $this->tokeInfo->name = $this->contract->name($this->contractAddress);
        $this->tokeInfo->symbol = $this->contract->symbol($this->contractAddress);
        $this->tokeInfo->decimals = $this->contract->decimals($this->contractAddress);
        $this->tokeInfo->totalSupply = $this->contract->totalSupply($this->contractAddress);
        $this->tokeInfo->owner = $this->contract->owner($this->contractAddress);
        return $this;
    }

    public function getBalance(string $address): string
    {
        return $this->contract->balanceOfString($this->contractAddress, $address);
    }

    public function getAllowance(string $owner, string $spender): ?string
    {
        return $this->contract->allowance($this->contractAddress, $owner, $spender);
    }

    public function getFunctionSelector(string $name): array
    {
        return $this->contract->getMethodSelector($name);
    }

    public function getEventsTopics(): array
    {
        return $this->contract->getEventsTopics() ?? [];
    }
}
