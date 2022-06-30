<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Services;

use Igor360\UniswapV2Connector\Interfaces\TransactionDecodeInterface;
use Igor360\UniswapV2Connector\Interfaces\TransactionSwapInterface;
use Igor360\UniswapV2Connector\Utils\ClassUtils;

class TransactionSwapService extends TransactionService implements TransactionDecodeInterface
{
    use ClassUtils;

    public function constantStorageClass(): string
    {
        return TransactionSwapInterface::class;
    }

    public function decode(): void
    {
        // TODO: Implement decode() method.
    }

    public function validate(): void
    {
        // TODO: Implement validate() method.
    }


}
