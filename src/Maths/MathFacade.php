<?php

namespace Igor360\UniswapV2Connector\Maths;

use Igor360\UniswapV2Connector\Exceptions\InvalidImplementationClassException;
use Igor360\UniswapV2Connector\Exceptions\InvalidMethodCallException;
use Igor360\UniswapV2Connector\Interfaces\IMath;

abstract class MathFacade
{
    private static string $source = Math::class;

    public static function changeConfigSource(string $newSource): void
    {
        if (is_subclass_of($newSource, IMath::class)) {
            self::$source = $newSource;
        }
        throw new InvalidImplementationClassException("New source config is not realize IConfig");
    }

    public static function __callStatic($name, $arguments)
    {
        if (method_exists(self::$source, $name)) {
            $class = self::getSourceInstance();
            return $class->$name(...$arguments);
        }
        throw new InvalidMethodCallException();
    }


    public static function getSourceInstance(): IMath
    {
        return new self::$source();
    }

    public static function getSource(): string
    {
        return self::$source;
    }

}
