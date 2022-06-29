<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Interfaces\ITransactions;

class TransactionSwapService extends TransactionService
{
    public function getConstants(): array
    {
        $constantStorage = new \ReflectionClass(ITransactions::class);
        return $constantStorage->getConstants();
    }
}
