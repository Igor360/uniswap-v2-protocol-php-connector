<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Interfaces;

/**
 * Interface for factory to call decode function for decode transaction
 */
interface TransactionDecodeInterface
{
    public function decode(): void;

    public function validate(): void;

    public function isValidated(): bool;
}
