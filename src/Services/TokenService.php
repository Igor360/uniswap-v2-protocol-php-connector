<?php

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Contracts\ContractsFactory;
use Igor360\UniswapV2Connector\Contracts\ERC20Contract;
use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;
use Igor360\UniswapV2Connector\Models\Token;

class TokenService
{
    private ?string $contractAddress;

    private ERC20Contract $contract;

    private Token $tokeInfo;

    public function __construct(?string $contractAddress, ConnectionInterface $credentials)
    {
        $this->contractAddress = $contractAddress;
        $this->tokeInfo = new Token();
        $this->tokeInfo->address = $contractAddress;
        $this->contract = ContractsFactory::make('erc20', $credentials);
        if (!is_null($contractAddress)) {
            $this->loadTokenInfo();
        }
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
}
