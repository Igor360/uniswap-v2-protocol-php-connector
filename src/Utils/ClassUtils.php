<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Utils;

trait ClassUtils
{
    public function getConstants(): array
    {
        $constantStorage = new \ReflectionClass($this->constantStorageClass());
        return $constantStorage->getConstants();
    }

    abstract public function constantStorageClass(): string;
}
