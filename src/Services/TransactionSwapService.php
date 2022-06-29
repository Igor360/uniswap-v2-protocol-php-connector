<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Interfaces\TransactionSwapInterface;
use Igor360\UniswapV2Connector\Utils\ClassUtils;

class TransactionSwapService extends TransactionService
{
    use ClassUtils;

    public function getConstants(): array
    {
        return $this->getClassConstants(TransactionSwapInterface::class);
    }
}
