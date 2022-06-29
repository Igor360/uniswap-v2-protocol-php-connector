<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Exceptions\ContractException;
use Igor360\UniswapV2Connector\Exceptions\TransactionException;
use Igor360\UniswapV2Connector\Interfaces\ConnectionInterface;

class TransactionTokenService extends TransactionService
{
    private ?string $transactionHash;

    private TokenService $tokenService;

    /**
     * @param TokenService $tokenService
     */
    public function __construct(?string $transactionHash, ConnectionInterface $credentials)
    {
        parent::__construct($transactionHash, $credentials);
        $this->isContract();
        $this->tokenService = new TokenService($this->transactionInfo->to, $credentials);
    }

    public function getTokenService(): TokenService
    {
        return $this->tokenService;
    }

}
