<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Utils;

trait ClassUtils
{
    public function getClassConstants(string $className): array
    {
        $constantStorage = new \ReflectionClass($className);
        return $constantStorage->getConstants();
    }
}
