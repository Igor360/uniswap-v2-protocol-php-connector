<?php

namespace Igor360\UniswapV2Connector\Maths;

use Igor360\UniswapV2Connector\Exceptions\InvalidImplementationClassException;
use Igor360\UniswapV2Connector\Exceptions\InvalidMethodCallException;
use Igor360\UniswapV2Connector\Interfaces\MathInterface;

abstract class MathFacade
{
    private static string $source = Math::class;

    public static function changeConfigSource(string $newSource): void
    {
        if (is_subclass_of($newSource, MathInterface::class)) {
            self::$source = $newSource;
        }
        throw new InvalidImplementationClassException("New source config is not realize ConfigInterface");
    }

    public static function __callStatic($name, $arguments)
    {
        if (method_exists(self::$source, $name)) {
            $class = self::getSourceInstance();
            return $class->$name(...$arguments);
        }
        throw new InvalidMethodCallException();
    }


    public static function getSourceInstance(): MathInterface
    {
        return new self::$source();
    }

    public static function getSource(): string
    {
        return self::$source;
    }

}
