<?php declare(strict_types=1);

namespace Igor360\UniswapV2Connector\Interfaces;

interface IConfig
{
    public const DATE_FORMAT = "d-m-Y H:i:s";

    public const DATE_ZONE = "Europe/Helsinki";

    public const BASE_KEY = "uniswap-v2-connector";

    public static function get(string $key, $default = null);

    public static function toArray(): array;

}
